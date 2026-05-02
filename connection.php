<?php
mysqli_report(MYSQLI_REPORT_STRICT);

$host = 'localhost';
$user = 'root';
$pass = '';
$dtbs = 'db_24183207007';

try {
    $conn = mysqli_connect($host, $user, $pass, $dtbs);
} catch (Exception $e) 
{

    $conn = mysqli_connect($host, $user, $pass);
    $sql = "CREATE DATABASE IF NOT EXISTS $dtbs";
    mysqli_query($conn, $sql);
    $conn = mysqli_connect($host, $user, $pass, $dtbs);
}
?>