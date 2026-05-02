<?php
function DataTipe()
{
    global $menu;
    include 'connection.php';

    $page = GET('page',0);
    $id   = GET('id','');

    /* =========================
       PROSES EDIT
    ========================= */
    if($page==2 && GET('exec')!=''){
        mysqli_query($conn,
            "UPDATE tipe
             SET nama_tipe='".GET('nama')."'
             WHERE id_tipe='$id'"
        );

        echo "<script>window.location='?menu=tipe';</script>";
        exit;
    }

    /* =========================
       PROSES HAPUS
    ========================= */
    if($page==4){
        mysqli_query($conn,
            "DELETE FROM tipe
             WHERE id_tipe='$id'"
        );

        echo "<script>window.location='?menu=tipe';</script>";
        exit;
    }

    /* =========================
       PROSES TAMBAH
    ========================= */
    if(isset($_POST['simpan'])){
        mysqli_query($conn,
            "INSERT INTO tipe(nama_tipe, Added)
             VALUES('".$_POST['nama']."', NOW())"
        );

        echo "<script>window.location='?menu=tipe';</script>";
        exit;
    }


    // Cek kolom Added
    $cek = mysqli_query($conn,"SHOW COLUMNS FROM tipe LIKE 'Added'");
    if(mysqli_num_rows($cek)==0){
        mysqli_query($conn,"ALTER TABLE tipe ADD Added DATETIME");
    }


    /* =========================
       HALAMAN TAMBAH
    ========================= */
    if($page==1)
    {
        echo '
        <legend class="title">
            <a href="?menu=barang" style="text-decoration:none; color:#aaa;">Barang</a>
            <span> › </span>
            <a href="?menu=tipe" style="text-decoration:none; color:#aaa;">Tipe Barang</a>
            <span> › Tambah</span>
        </legend>';

        echo '
        <form method="post">
            <table>
                <tr>
                    <td>Deskripsi Tipe</td>
                    <td>
                        <input type="text" name="nama"
                               placeholder="Deskripsi Barang" required>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input type="submit" name="simpan" value="Simpan">
                        <a href="?menu=tipe" class="btn-batal">Kembali</a>
                    </td>
                </tr>
            </table>
        </form>';
    }

    /* =========================
       FORM EDIT
    ========================= */
    elseif($page==2)
    {
        $res = mysqli_query($conn,
            "SELECT * FROM tipe WHERE id_tipe='$id'"
        );
        $row = mysqli_fetch_assoc($res);

        echo '
        <legend class="title">Edit Tipe</legend>

        <form method="post" action="?menu=tipe&page=2&id='.$id.'">
            <input type="hidden" name="exec" value="1">

            <table>
                <tr>
                    <td>Nama</td>
                    <td>
                        <input type="text" name="nama"
                               value="'.$row['nama_tipe'].'" required>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input type="submit" value="Simpan">
                        <a href="?menu=tipe" class="btn-batal">Kembali</a>
                    </td>
                </tr>
            </table>
        </form>';
    }

    /*KONFIRMASI HAPUS*/
    elseif($page==3)
    {
        $res = mysqli_query($conn,
            "SELECT nama_tipe FROM tipe WHERE id_tipe='$id'"
        );
        $row = mysqli_fetch_assoc($res);

        echo '
        <div class="alert-hapus">
            Yakin hapus <b>'.$row['nama_tipe'].'</b>?
        </div>

        <a href="?menu=tipe&page=4&id='.$id.'" class="btn-hapus">Hapus</a>
        <a href="?menu=tipe" class="btn-batal">Batal</a>';
    }

    /*TAMPIL DATA*/
    else
    {
        echo '
        <legend class="title">
            <a href="?menu=barang" style="color:#aaa;">Barang</a>
            <span> › </span>
            <span style="color:#2c7da0;">Tipe Barang</span>
        </legend>';

        echo '<a href="?menu=tipe&page=1" class="btn-tambah">Tambah</a>';

        $res = mysqli_query($conn,
            "SELECT * FROM tipe ORDER BY Added DESC"
        );

        echo '
        <table>
            <tr>
                <th class="center">No</th>
                <th class="center">Deskripsi</th>
                <th class="center">Ditambahkan</th>
                <th class="center">Edit</th>
            </tr>';

        $no = 1;
        while($row = mysqli_fetch_assoc($res)){
            echo '
            <tr>
                <td class="center">'.$no++.'</td>
                <td>'.$row['nama_tipe'].'</td>
                <td class="center">'.
                    ($row['Added']
                        ? date('d F Y, H:i', strtotime($row['Added'])).' WIB'
                        : '-') .
                '</td>
                <td class="center">
                    <a href="?menu=tipe&page=2&id='.$row['id_tipe'].'" class="edit">≡</a>
                    <a href="?menu=tipe&page=3&id='.$row['id_tipe'].'" class="hapus">×</a>
                </td>
            </tr>';
        }

        echo '</table>';
    }

}
?>
