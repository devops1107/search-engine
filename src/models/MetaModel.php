<?php

namespace spark\models;

/**
* MetaModel
*
*/
class MetaModel extends Model
{
    protected static $table = 'meta';

    protected $queryKey = 'meta_id';

    protected $autoTimestamp = false;

    protected $sortRules = [
    ];

    public function getAllMeta($targetID, $key = null, $type = null)
    {
        $data = $this->select(['meta_key', 'meta_value'])
                    ->where('meta_target_id', '=', $targetID);

        if ($type) {
            $data = $data->where('meta_type', '=', $type);
        }


        $data = $data->execute()->fetchAll();

        $meta = [];

        foreach ($data as $key => $value) {
            $meta[$value['meta_key']] = $value['meta_value'];
        }

        return $meta;
    }

    public function getMeta($targetID, $key, $type, $fallback = null)
    {
        $stmt = $this->select(['meta_value'])
                    ->where('meta_target_id', '=', $targetID)
                    ->where('meta_key', '=', $key)
                    ->limit(1, 0)
                    ->execute()
                    ->fetch();

        if ($stmt) {
            return $stmt['meta_value'];
        }

        return $fallback;
    }

    public function setMeta($targetID, $key, $value, $type)
    {
        // If already exists, update
        if ($this->hasMeta($targetID, $key, $type)) {
            return $this->updateMeta($targetID, $key, $value, $type);
        }

        return $this->create([
            'meta_type' => $type,
            'meta_key' => $key,
            'meta_value' => $value,
            'meta_target_id' => $targetID
        ]);
    }

    public function updateMeta($targetID, $key, $value, $type)
    {
        $db = $this->db();
        $table = $this->getTable();


        return $db->update([
            'meta_value' => $value,
        ])
        ->table($table)
        ->where('meta_target_id', '=', $targetID)
        ->where('meta_type', '=', $type)
        ->where('meta_key', '=', $key)
        ->execute();
    }


    public function deleteMeta($targetID, $key, $type)
    {
        $db = $this->db();
        $table = $this->getTable();


        return $db->delete()
        ->from($table)
        ->where('meta_target_id', '=', $targetID)
        ->where('meta_key', '=', $key)
        ->where('meta_type', '=', $type)
        ->execute();
    }

    /**
     * Clear all meta that has a specific id and type
     *
     * @param  mixed $targetID
     * @param  mixed $type
     * @return boolean
     */
    public function clearMeta($targetID, $type)
    {
        return $db->delete()
        ->from($table)
        ->where('meta_target_id', '=', $targetID)
        ->where('meta_type', '=', $type)
        ->execute();
    }

    /**
     * Check if a meta exists for a target ID and specific keys and type
     *
     * @param  mixed  $targetID
     * @param  mixed  $key
     * @param  mixed  $type
     * @return boolean
     */
    public function hasMeta($targetID, $key, $type)
    {
        $sql = $this->select(["COUNT( {$this->queryKey} ) as count"])
                       ->where('meta_key', '=', $key)
                       ->where('meta_target_id', '=', $targetID)
                       ->where('meta_type', '=', $type);

        $stmt = $sql->execute()->fetch();

        if (!$stmt) {
            return false;
        }

        return (bool) $stmt['count'];
    }
}
