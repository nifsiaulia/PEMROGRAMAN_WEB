<?php
function DataTransaksi()
{
    global $menu;
    include 'connection.php';

    /*TABLE*/
    mysqli_query($conn,"CREATE TABLE IF NOT EXISTS transaksi(
        id INT AUTO_INCREMENT PRIMARY KEY,
        tanggal DATETIME,
        id_barang VARCHAR(32),
        jumlah INT,
        id_pelanggan INT,
        total INT
    )");

    // tambah status TOGLE
    $cek = mysqli_query($conn,"SHOW COLUMNS FROM transaksi LIKE 'Status'");
    if(mysqli_num_rows($cek)==0){
        mysqli_query($conn,"ALTER TABLE transaksi ADD Status INT DEFAULT 1");
    }

    $page = GET('page',0);
    $id   = GET('id','');

    /*TOGGLE STATUS 👁*/
    if(GET('toggle')!=''){
        $idToggle = GET('toggle');

        mysqli_query($conn,"
            UPDATE transaksi 
            SET Status = IF(Status=1,0,1)
            WHERE id='$idToggle'
        ");

        header("Location:?menu=transaksi");
        exit;
    }

    /*EDIT*/
    if($page==2 && GET('exec')!=''){
        mysqli_query($conn,"UPDATE transaksi SET 
            tanggal='".GET('tanggal')."',
            jumlah='".GET('jumlah')."'
            WHERE id='$id'
        ");

        header("Location:?menu=transaksi");
        exit;
    }

    /*DELETE*/
    if($page==4){
        mysqli_query($conn,"DELETE FROM transaksi WHERE id='$id'");
        header("Location:?menu=transaksi");
        exit;
    }

    /*TAMBAH*/
    if($page==1)
    {
        echo'<legend class="title">
        <a href="?menu=barang" style="color:#aaa;">Barang</a>
        <span>›</span>
        <a href="?menu=transaksi" style="color:#aaa;">Transaksi</a>
        <span>› Tambah</span>
        </legend>';

        echo'<form method="post">
        <table>

        <tr><td>Tanggal</td>
        <td><input type="datetime-local" name="tanggal"></td></tr>';

        echo '<tr><td>Barang</td><td><select name="id_barang">';
        $barang=mysqli_query($conn,"SELECT * FROM barang");
        while($b=mysqli_fetch_assoc($barang)){
            echo "<option value='".$b['ID']."'>".$b['Name']."</option>";
        }
        echo '</select></td></tr>';

        echo '<tr><td>Pelanggan</td><td><select name="id_pelanggan">';
        $pel=mysqli_query($conn,"SELECT * FROM pelanggan");
        while($p=mysqli_fetch_assoc($pel)){
            echo "<option value='".$p['id']."'>".$p['nama']."</option>";
        }
        echo '</select></td></tr>';

        echo '<tr><td>Jumlah</td>
        <td><input type="number" name="jumlah"></td></tr>

        <tr>
        <td></td>
        <td>
            <input type="submit" name="simpan" value="Simpan">
            <a href="?menu=transaksi" class="btn-batal">Kembali</a>
        </td>
        </tr>
        </table>
        </form>';

        if(isset($_POST['simpan'])){

            $h=mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT Price FROM barang WHERE ID='".$_POST['id_barang']."'"));

            $total = $h['Price'] * $_POST['jumlah'];

            mysqli_query($conn,"INSERT INTO transaksi 
            (tanggal,id_barang,jumlah,id_pelanggan,total,Status)
            VALUES(
                '".$_POST['tanggal']."',
                '".$_POST['id_barang']."',
                '".$_POST['jumlah']."',
                '".$_POST['id_pelanggan']."',
                '$total',
                1
            )");

            mysqli_query($conn,"
            UPDATE barang SET Stock = Stock - ".$_POST['jumlah']."
            WHERE ID='".$_POST['id_barang']."'
            ");

            header("Location:?menu=transaksi");
            exit;
        }
    }

    /*EDIT*/
    elseif($page==2)
    {
        $res=mysqli_query($conn,"SELECT * FROM transaksi WHERE id='$id'");
        $row=mysqli_fetch_assoc($res);

        echo '<legend class="title">Edit Transaksi</legend>';

        echo '<form method="post" action="?menu=transaksi&page=2&id='.$id.'">
        <input type="hidden" name="exec" value="1">

        <table>
        <tr><td>Tanggal</td>
        <td><input type="datetime-local" name="tanggal" value="'.$row['tanggal'].'"></td></tr>

        <tr><td>Jumlah</td>
        <td><input type="number" name="jumlah" value="'.$row['jumlah'].'"></td></tr>

        <tr>
        <td></td>
        <td>
            <input type="submit" value="Simpan">
            <a href="?menu=transaksi" class="btn-batal">Kembali</a>
        </td>
        </tr>
        </table></form>';
    }

    /*HAPUS*/
    elseif($page==3)
    {
        echo '<div class="alert-hapus">
        Yakin hapus transaksi ini?
        </div>';

        echo '<a href="?menu=transaksi&page=4&id='.$id.'" class="btn-hapus">Hapus</a>
        <a href="?menu=transaksi" class="btn-batal">Batal</a>';
    }

    /*TAMPIL DATA TABEL*/
    else
    {
        echo'<legend class="title">
        <a href="?menu=barang" style="color:#aaa;">Barang</a>
        <span> › </span>
        <span style="color:#2c7da0;">Transaksi</span>
        </legend>';

        echo '<a href="?menu=transaksi&page=1" class="btn-tambah">Tambah</a>';

        $res=mysqli_query($conn,"
        SELECT 
            transaksi.*,
            barang.Name AS nama_barang,
            pelanggan.nama AS nama_pelanggan
        FROM transaksi
        LEFT JOIN barang ON transaksi.id_barang = barang.ID
        LEFT JOIN pelanggan ON transaksi.id_pelanggan = pelanggan.id
        ");

        echo '<table>
        <tr>
            <th class="center">No</th>
            <th class="center">Tanggal</th>
            <th class="center">Nama Pelanggan</th>
            <th class="center">Barang</th>
            <th class="center">Jumlah</th>
            <th class="center">Total</th>
            <th class="center">Edit</th>
        </tr>';

        $no=1;
        while($row=mysqli_fetch_assoc($res)){
            echo'<tr style="'.($row['Status'] ? '' : 'opacity:0.5;').'">
            <td>'.$no++.'</td>
            <td>'.date('d F Y, H:i', strtotime($row['tanggal'])).'</td>
            <td>'.$row['nama_pelanggan'].'</td>
            <td>'.$row['nama_barang'].'</td>
            <td>'.$row['jumlah'].'</td>
            <td>Rp '.number_format($row['total'],0,',','.').'</td>
            <td>
                <a href="?menu=transaksi&page=2&id='.$row['id'].'" class="edit">≡</a>
                <a href="?menu=transaksi&page=3&id='.$row['id'].'" class="hapus">×</a>
                <a href="?menu=transaksi&toggle='.$row['id'].'" class="view">👁</a>
            </td>
            </tr>';
        }

        echo'</table>';
    }

    echo'</fieldset>';
}
?>
