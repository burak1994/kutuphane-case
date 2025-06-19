<?php
require_once 'config/database.php';

$testDB  = new Database();
$pdo = $testDB->getConnection();


?>