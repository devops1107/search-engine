<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spark\drivers\Auth;

/**
 * A port of http-foundation's PDOSessionHandler
 */
class MySqlSessionHandler implements \SessionHandlerInterface
{
    /**
     * No locking is done. This means sessions are prone to loss of data due to
     * race conditions of concurrent requests to the same session. The last session
     * write will win in this case. It might be useful when you implement your own
     * logic to deal with this like an optimistic approach.
     */
    const LOCK_NONE = 0;

    /**
     * Creates an application-level lock on a session. The disadvantage is that the
     * lock is not enforced by the database and thus other, unaware parts of the
     * application could still concurrently modify the session. The advantage is it
     * does not require a transaction.
     * This mode is not available for SQLite and not yet implemented for oci and sqlsrv.
     */
    const LOCK_ADVISORY = 1;

    /**
     * Issues a real row lock. Since it uses a transaction between opening and
     * closing a session, you have to be careful when you use same database connection
     * that you also use for your application logic. This mode is the default because
     * it's the only reliable solution across DBMSs.
     */
    const LOCK_TRANSACTIONAL = 2;

    /**
     * @var \PDO|null PDO instance or null when not connected yet
     */
    private $pdo;

    /**
     * @var string Table name
     */
    private $table = 'sessions';

    /**
     * @var string Column for session id
     */
    private $idCol = 'session_id';

    /**
     * @var string Column for session data
     */
    private $dataCol = 'session_data';

    /**
     * @var string Column for lifetime
     */
    private $lifetimeCol = 'session_lifetime';

    /**
     * @var string Column for timestamp
     */
    private $timeCol = 'session_time';

    private $ipCol = 'session_ip';

    private $uaCol = 'session_ua';

    private $userIdCol = 'user_id';

    /**
     * @var int The strategy for locking, see constants
     */
    private $lockMode = self::LOCK_TRANSACTIONAL;

    /**
     * It's an array to support multiple reads before closing which is manual, non-standard usage.
     *
     * @var \PDOStatement[] An array of statements to release advisory locks
     */
    private $unlockStatements = [];

    /**
     * @var bool True when the current session exists but expired according to session.gc_maxlifetime
     */
    private $sessionExpired = false;

    /**
     * @var bool Whether a transaction is active
     */
    private $inTransaction = false;

    /**
     * @var bool Whether gc() has been called
     */
    private $gcCalled = false;


    public function __construct(array $options = [])
    {
        $this->app = app();

        $this->pdo = $this->app->db;
        $this->lockMode = isset($options['lock_mode']) ? $options['lock_mode'] : $this->lockMode;

        if (!empty($options['table'])) {
            $this->table = $options['table'];
        }
    }

    /**
     * Returns true when the current session exists but expired according to session.gc_maxlifetime.
     *
     * Can be used to distinguish between a new session and one that expired due to inactivity.
     *
     * @return bool Whether current session expired
     */
    public function isSessionExpired()
    {
        return $this->sessionExpired;
    }

    /**
     * {@inheritdoc}
     */
    public function open($savePath, $sessionName)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($sessionId)
    {
        try {
            return $this->doRead($sessionId);
        } catch (\PDOException $e) {
            $this->rollback();

            throw $e;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function gc($maxlifetime)
    {
        // We delay gc() to close() so that it is executed outside the transactional and blocking read-write process.
        // This way, pruning expired sessions does not block them from being started while the current session is used.
        $this->gcCalled = true;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessionId)
    {
        // delete the record associated with this id
        $sql = "DELETE FROM $this->table WHERE $this->idCol = :id";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $sessionId, \PDO::PARAM_STR);
            $stmt->execute();
        } catch (\PDOException $e) {
            $this->rollback();

            throw $e;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function write($sessionId, $data)
    {
        $maxlifetime = (int) ini_get('session.gc_maxlifetime');

        try {
            // We use a single MERGE SQL query when supported by the database.
            $mergeStmt = $this->getMergeStatement($sessionId, $data, $maxlifetime);
            $mergeStmt->execute();
            return true;
        } catch (\PDOException $e) {
            $this->rollback();
            throw $e;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        $this->commit();

        while ($unlockStmt = array_shift($this->unlockStatements)) {
            $unlockStmt->execute();
        }

        if ($this->gcCalled) {
            $this->gcCalled = false;

            // delete the session records that have expired
            $sql = "DELETE FROM $this->table WHERE $this->lifetimeCol + $this->timeCol < :time";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':time', time(), \PDO::PARAM_INT);
            $stmt->execute();
        }

        return true;
    }

    /**
     * Helper method to begin a transaction.
     *
     *
     * MySQLs default isolation, REPEATABLE READ, causes deadlock for different sessions
     * due to http://www.mysqlperformanceblog.com/2013/12/12/one-more-innodb-gap-lock-to-avoid/ .
     * So we change it to READ COMMITTED.
     */
    private function beginTransaction()
    {
        if (!$this->inTransaction) {
            $this->pdo->exec('SET TRANSACTION ISOLATION LEVEL READ COMMITTED');
            $this->pdo->beginTransaction();
            $this->inTransaction = true;
        }
    }

    /**
     * Helper method to commit a transaction.
     */
    private function commit()
    {
        if ($this->inTransaction) {
            try {
                // commit read-write transaction which also releases the lock
                $this->pdo->commit();
                $this->inTransaction = false;
            } catch (\PDOException $e) {
                $this->rollback();

                throw $e;
            }
        }
    }

    /**
     * Helper method to rollback a transaction.
     */
    private function rollback()
    {
        // We only need to rollback if we are in a transaction. Otherwise the resulting
        // error would hide the real problem why rollback was called. We might not be
        // in a transaction when not using the transactional locking behavior or when
        // two callbacks (e.g. destroy and write) are invoked that both fail.
        if ($this->inTransaction) {
                $this->pdo->rollBack();
            $this->inTransaction = false;
        }
    }

    /**
     * Reads the session data in respect to the different locking strategies.
     *
     * We need to make sure we do not return session data that is already considered garbage according
     * to the session.gc_maxlifetime setting because gc() is called after read() and only sometimes.
     *
     * @param string $sessionId Session ID
     *
     * @return string The session data
     */
    private function doRead($sessionId)
    {
        $this->sessionExpired = false;

        if (self::LOCK_ADVISORY === $this->lockMode) {
            $this->unlockStatements[] = $this->doAdvisoryLock($sessionId);
        }

        $selectSql = $this->getSelectSql();
        $selectStmt = $this->pdo->prepare($selectSql);
        $selectStmt->bindParam(':id', $sessionId, \PDO::PARAM_STR);

        do {
            $selectStmt->execute();
            $sessionRows = $selectStmt->fetchAll(\PDO::FETCH_NUM);

            if ($sessionRows) {
                if ($sessionRows[0][1] + $sessionRows[0][2] < time()) {
                    $this->sessionExpired = true;

                    return '';
                }

                return is_resource($sessionRows[0][0]) ? stream_get_contents($sessionRows[0][0]) : $sessionRows[0][0];
            }

            if (self::LOCK_TRANSACTIONAL === $this->lockMode) {
                // Exclusive-reading of non-existent rows does not block, so we need to do an insert to block
                // until other connections to the session are committed.
                try {
                    $insertStmt = $this->pdo->prepare(
                        "INSERT INTO $this->table ($this->idCol, $this->dataCol, $this->lifetimeCol, $this->timeCol, $this->userIdCol, $this->ipCol, $this->uaCol) VALUES (:id, :data, :lifetime, :time, :uid, :ip, :ua)"
                    );
                    $insertStmt->bindParam(':id', $sessionId, \PDO::PARAM_STR);
                    $insertStmt->bindValue(':data', '', \PDO::PARAM_LOB);
                    $insertStmt->bindValue(':lifetime', 0, \PDO::PARAM_INT);
                    $insertStmt->bindValue(':time', time(), \PDO::PARAM_INT);


                    $insertStmt->bindValue(':uid', $this->app->user->getID(), \PDO::PARAM_INT);
                    $insertStmt->bindValue(':ip', $this->app->request->getIp(), \PDO::PARAM_STR);
                    $insertStmt->bindValue(':ua', $this->app->request->getUserAgent(), \PDO::PARAM_STR);
                    $insertStmt->execute();
                } catch (\PDOException $e) {
                    // Catch duplicate key error because other connection created the session already.
                    // It would only not be the case when the other connection destroyed the session.
                    if (0 === strpos($e->getCode(), '23')) {
                        // Retrieve finished session data written by concurrent connection by restarting the loop.
                        // We have to start a new transaction as a failed query will mark the current transaction as
                        // aborted in PostgreSQL and disallow further queries within it.
                        $this->rollback();
                        $this->beginTransaction();
                        continue;
                    }

                    throw $e;
                }
            }

            return '';
        } while (true);
    }

    /**
     * Executes an application-level lock on the database.
     *
     * @param string $sessionId Session ID
     *
     * @return \PDOStatement The statement that needs to be executed later to release the lock
     *
     * @throws \DomainException When an unsupported PDO driver is used
     *
     * @todo implement missing advisory locks
     *       - for oci using DBMS_LOCK.REQUEST
     *       - for sqlsrv using sp_getapplock with LockOwner = Session
     */
    private function doAdvisoryLock($sessionId)
    {
        // should we handle the return value? 0 on timeout, null on error
        // we use a timeout of 50 seconds which is also the default for innodb_lock_wait_timeout
        $stmt = $this->pdo->prepare('SELECT GET_LOCK(:key, 50)');
        $stmt->bindValue(':key', $sessionId, \PDO::PARAM_STR);
        $stmt->execute();

        $releaseStmt = $this->pdo->prepare('DO RELEASE_LOCK(:key)');
        $releaseStmt->bindValue(':key', $sessionId, \PDO::PARAM_STR);

        return $releaseStmt;
    }

    /**
     * Return a locking or nonlocking SQL query to read session information.
     *
     * @return string The SQL string
     */
    private function getSelectSql()
    {
        if (self::LOCK_TRANSACTIONAL === $this->lockMode) {
            $this->beginTransaction();
            return "SELECT $this->dataCol, $this->lifetimeCol, $this->timeCol FROM $this->table WHERE $this->idCol = :id FOR UPDATE";
        }

        return "SELECT $this->dataCol, $this->lifetimeCol, $this->timeCol FROM $this->table WHERE $this->idCol = :id";
    }

    /**
     * Returns a merge/upsert (i.e. insert or update) statement when supported by the database for writing session data.
     *
     * @param string $sessionId   Session ID
     * @param string $data        Encoded session data
     * @param int    $maxlifetime session.gc_maxlifetime
     *
     * @return \PDOStatement|null The merge statement or null when not supported
     */
    private function getMergeStatement($sessionId, $data, $maxlifetime)
    {
        $mergeSql = "INSERT INTO $this->table ($this->idCol, $this->dataCol, $this->lifetimeCol, $this->timeCol, $this->userIdCol, $this->ipCol, $this->uaCol) VALUES (:id, :data, :lifetime, :time, :uid, :ip, :ua) ".
        "ON DUPLICATE KEY UPDATE $this->dataCol = VALUES($this->dataCol), $this->lifetimeCol = VALUES($this->lifetimeCol), $this->timeCol = VALUES($this->timeCol), $this->userIdCol = VALUES($this->userIdCol), $this->ipCol = VALUES($this->ipCol)";

        $mergeStmt = $this->pdo->prepare($mergeSql);

        $mergeStmt->bindParam(':id', $sessionId, \PDO::PARAM_STR);
        $mergeStmt->bindParam(':data', $data, \PDO::PARAM_LOB);
        $mergeStmt->bindParam(':lifetime', $maxlifetime, \PDO::PARAM_INT);
        $mergeStmt->bindValue(':time', time(), \PDO::PARAM_INT);
        $mergeStmt->bindValue(':uid', $this->app->user->getID(), \PDO::PARAM_INT);
        $mergeStmt->bindValue(':ip', $this->app->request->getIp(), \PDO::PARAM_STR);
        $mergeStmt->bindValue(':ua', $this->app->request->getUserAgent(), \PDO::PARAM_STR);

        return $mergeStmt;
    }

    /**
     * Return a PDO instance.
     *
     * @return \PDO
     */
    protected function getConnection()
    {
        return $this->pdo;
    }
}
