<!DOCTYPE html>
<html>
<head>
    <title>Debugger</title>
</head>
<body>

<?php
error_reporting(E_ALL);
require 'src/constants.php';

$backupPath = SRCPATH . 'var/etc/htaccess.bak';
$htaccessPath = BASEPATH . '.htaccess';
$htaExists = is_file($htaccessPath);

if (!$htaExists) {
    $backupContent = file_get_contents($backupPath);
    echo "<span style='color:red'>.htaccess file not found at the root.</span><br>
    <span>Generating one for you....</span><br>";

    if (@file_put_contents($htaccessPath, $backupContent, LOCK_EX)) {
        echo "<hr><span style='color:green'>Generated .htaccess file.</span><br>";
    } else {
        echo "<hr><span style='color:red'>Failed to generate .htaccess file, make sure you have permissions on this server.</span><br>";
    }
} else {
    $htaccessContent = file_get_contents($htaccessPath);

    if (!strstr($htaccessContent, '# BEGIN Spark')) {
        echo "<span style='color:red'>.htaccess found but it's not the one for the script.</span><br>
    <span>Fixing it for you....</span><br>";

        $backupContent = file_get_contents($backupPath);

        if (@file_put_contents($htaccessPath, $backupContent, LOCK_EX)) {
            echo "<hr><span style='color:green'>Generated .htaccess file.</span><br>";
        } else {
            echo "<hr><span style='color:red'>Failed to generate .htaccess file, make sure you have permissions on this server.</span><br>";
        }
    }
}



?>
</body>
</html>
