<?php

function GET($key, $def='')
{
    $res = isset($_SESSION[$key]) && $_SESSION[$key] != '' ? $_SESSION[$key] : $def;
    $res = isset($_POST[$key]) && $_POST[$key] != '' ? $_POST[$key] : $res;
    $res = isset($_GET[$key]) && $_GET[$key] != '' ? $_GET[$key] : $res;
    return $res;
}

function GenerateTable($conn)
{
    $sql = array(
        "CREATE TABLE IF NOT EXISTS barang (
            ID varchar(32) NOT NULL,
            Name varchar(128) NOT NULL,
            Price int NOT NULL,
            Stock int NOT NULL,
            Added datetime NOT NULL,
            PRIMARY KEY(ID)
        )",
        "CREATE TABLE IF NOT EXISTS tipe (
            id_tipe INT AUTO_INCREMENT PRIMARY KEY,
            nama_tipe VARCHAR(50),
            Added DATETIME NOT NULL
        )"
    );

    for($i=0; $i<sizeof($sql); $i++)
    {
        mysqli_query($conn, $sql[$i]);
    }
        $cek_kolom = mysqli_query($conn, "SHOW COLUMNS FROM barang LIKE 'id_tipe'");
    if(mysqli_num_rows($cek_kolom) == 0){
        mysqli_query($conn, "ALTER TABLE barang ADD id_tipe INT");
    }

    $cek_tipe = mysqli_query($conn, "SELECT COUNT(*) as total FROM tipe");
    $row_tipe = mysqli_fetch_assoc($cek_tipe);

}
?>