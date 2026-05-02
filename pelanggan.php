<?php
function DataPelanggan()
{
    global $menu;
    include 'connection.php';

    mysqli_query($conn,"CREATE TABLE IF NOT EXISTS pelanggan(
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama VARCHAR(100) NOT NULL,
        alamat VARCHAR(100) NOT NULL,
        telp VARCHAR(20) NOT NULL
    )");

    // TAMBAH KOLOM Added
    $cek = mysqli_query($conn,"SHOW COLUMNS FROM pelanggan LIKE 'Added'");
    if(mysqli_num_rows($cek)==0){
        mysqli_query($conn,"ALTER TABLE pelanggan ADD Added DATETIME");
    }

    // TAMBAH KOLOM Status TOGLE
    $cekStatus = mysqli_query($conn,"SHOW COLUMNS FROM pelanggan LIKE 'Status'");
    if(mysqli_num_rows($cekStatus)==0){
        mysqli_query($conn,"ALTER TABLE pelanggan ADD Status INT DEFAULT 1");
    }

    $page = GET('page',0);
    $id   = GET('id','');

    /*TOGGLE STATUS (👁)*/
    if(GET('toggle')!=''){
        $idToggle = GET('toggle');

        mysqli_query($conn,"
            UPDATE pelanggan 
            SET Status = IF(Status=1,0,1)
            WHERE id='$idToggle'
        ");

        header("Location:?menu=pelanggan");
        exit;
    }

    /*UPDATE*/
    if($page==2 && GET('exec')!=''){
        mysqli_query($conn,"UPDATE pelanggan SET 
            nama='".GET('nama')."',
            alamat='".GET('alamat')."',
            telp='".GET('telp')."'
            WHERE id='$id'
        ");

        header("Location:?menu=pelanggan");
        exit;
    }

    /*DELETE*/
    if($page==4){
        mysqli_query($conn,"DELETE FROM pelanggan WHERE id='$id'");
        header("Location:?menu=pelanggan");
        exit;
    }

    /*TAMBAH*/
    if($page==1)
    {
        echo'<legend class="title">
        <a href="?menu=barang" style="color:#aaa;">Barang</a>
        <span> › </span>
        <a href="?menu=pelanggan" style="color:#aaa;">Pelanggan</a>
        <span> › </span>
        <span style="color:#2c7da0;">Tambah</span>
        </legend>';

        echo'<form method="post">
        <table>
        <tr><td>Nama</td><td><input type="text" name="nama"></td></tr>
        <tr><td>Alamat</td><td><input type="text" name="alamat"></td></tr>
        <tr><td>Telepon</td><td><input type="text" name="telp"></td></tr>
        <tr>
            <td></td>
            <td>
                <input type="submit" name="simpan" value="Simpan">
                <a href="?menu=pelanggan" class="btn-batal">Kembali</a>
            </td>
        </tr>
        </table>
        </form>';

        if(isset($_POST['simpan'])){
            mysqli_query($conn,"INSERT INTO pelanggan (nama,alamat,telp,Added,Status)  
            VALUES(
                '".$_POST['nama']."',
                '".$_POST['alamat']."',
                '".$_POST['telp']."',
                NOW(),
                1
            )");

            header("Location:?menu=pelanggan");
            exit;
        }
    }

    /*EDIT*/
    elseif($page==2)
    {
        $res=mysqli_query($conn,"SELECT * FROM pelanggan WHERE id='$id'");
        $row=mysqli_fetch_assoc($res);

        echo '<legend class="title">Edit Pelanggan</legend>';

        echo '<form method="post" action="?menu=pelanggan&page=2&id='.$id.'">
        <input type="hidden" name="exec" value="1">

        <table>
        <tr><td>Nama</td><td><input type="text" name="nama" value="'.$row['nama'].'"></td></tr>
        <tr><td>Alamat</td><td><input type="text" name="alamat" value="'.$row['alamat'].'"></td></tr>
        <tr><td>Telepon</td><td><input type="text" name="telp" value="'.$row['telp'].'"></td></tr>
        <tr>
            <td></td>
            <td>
                <input type="submit" value="Simpan">
                <a href="?menu=pelanggan" class="btn-batal">Kembali</a>
            </td>
        </tr>
        </table>
        </form>';
    }

    /*HAPUS*/
    elseif($page==3)
    {
        $res=mysqli_query($conn,"SELECT nama FROM pelanggan WHERE id='$id'");
        $row=mysqli_fetch_assoc($res);

        echo '<div class="alert-hapus">
        Yakin hapus <b>'.$row['nama'].'</b>?
        </div>';

        echo '<a href="?menu=pelanggan&page=4&id='.$id.'" class="btn-hapus">Hapus</a>
        <a href="?menu=pelanggan" class="btn-batal">Batal</a>';
    }

    /*TAMPIL DATA*/
    else
    {
        echo'<legend class="title">
        <a href="?menu=barang" style="color:#aaa;">Barang</a>
        <span> › </span>
        <span style="color:#2c7da0;">Pelanggan</span>
        </legend>';

        echo '<a href="?menu=pelanggan&page=1" class="btn-tambah">Tambah</a>';

        $res=mysqli_query($conn,"SELECT * FROM pelanggan ORDER BY Added DESC");

        echo '<table>
        <tr>
            <th class="center">No</th>
            <th class="center">Nama Pelanggan</th>
            <th class="center">Alamat</th>
            <th class="center">Telepon</th>
            <th class="center">Ditambahkan</th>
            <th class="center">Edit</th>
        </tr>';

        $no=1;
        while($row=mysqli_fetch_assoc($res)){
            echo'<tr style="'.($row['Status'] ? '' : 'opacity:0.5;').'">
            <td>'.$no++.'</td>
            <td>'.$row['nama'].'</td>
            <td>'.$row['alamat'].'</td>
            <td>'.$row['telp'].'</td>
            <td>'.(!empty($row['Added']) ? date('d F Y, H:i', strtotime($row['Added'])) : '-').' WIB</td>
            <td>
                <a href="?menu=pelanggan&page=2&id='.$row['id'].'" class="edit">≡</a>
                <a href="?menu=pelanggan&page=3&id='.$row['id'].'" class="hapus">×</a>
                <a href="?menu=pelanggan&toggle='.$row['id'].'" class="view">👁</a>
            </td>
            </tr>';
        }

        echo'</table>';
    }

    echo'</fieldset>';
}
?>
