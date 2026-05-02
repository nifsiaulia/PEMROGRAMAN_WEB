<?php
function DataBarang()
{
    global $menu;
    include 'connection.php';

    GenerateTable($conn);

    $page = GET('page', 0);
    $id_barang = GET('id', '');

    /*TAMBAH DATA */
    if($page==1 && GET('exec')!='')
    {
        $brng = GET('brng', '');
        $hrga = GET('hrga', '');
        $stck = GET('stck', '');
        $id_tipe = GET('id_tipe', '');

        if($brng!='' && $hrga!='' && $stck!='')
        {
            $id = md5(time());

            $sql = "INSERT INTO barang VALUES(
                '$id',
                '$brng',
                '$hrga',
                '$stck',
                NOW(),
                '$id_tipe'
            )";

            mysqli_query($conn, $sql);
            echo "<script>window.location='?menu=barang';</script>";
            exit;
        }
    }

    /*PROSES EDIT DATA*/
    if($page==2 && GET('exec')!='')
    {
        $sql = "UPDATE barang SET
                    Name='".GET('brng')."',
                    Price='".GET('hrga')."',
                    Stock='".GET('stck')."',
                    id_tipe='".GET('id_tipe')."'
                WHERE ID='$id_barang'";

        mysqli_query($conn, $sql);
        echo "<script>window.location='?menu=barang';</script>";
        exit;
    }

    /*PROSES HAPUS DATA*/
    if($page==4)
    {
        mysqli_query($conn, "DELETE FROM barang WHERE ID='$id_barang'");
        echo "<script>window.location='?menu=barang';</script>";
        exit;
    }

    /*FORM TAMBAH*/
    if($page==1)
    {
        echo '<legend class="title">
                <a href="?menu=barang" style="text-decoration:none; color:#aaa;">Daftar Barang</a>
                <span>› Tambah</span>
              </legend>';

        echo '<form method="post" action="?menu=barang&page=1">
                <input type="hidden" name="exec" value="1">
                <table>
                    <tr>
                        <td>Nama</td>
                        <td><input type="text" name="brng" placeholder="Nama Barang"></td>
                    </tr>
                    <tr>
                        <td>Harga</td>
                        <td><input type="number" name="hrga" placeholder="Harga Barang"></td>
                    </tr>
                    <tr>
                        <td>Stok</td>
                        <td><input type="number" name="stck" placeholder="Stok Barang"></td>
                    </tr>
                    <tr>
                        <td>Jenis</td>
                        <td>
                            <select name="id_tipe">';

        $data = mysqli_query($conn, "SELECT * FROM tipe");
        while($d = mysqli_fetch_array($data))
        {
            echo "<option value='".$d['id_tipe']."'>".$d['nama_tipe']."</option>";
        }

        echo '          </select>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <input type="submit" value="Simpan">
                            <a href="?menu=barang" class="btn-batal">Kembali</a>
                        </td>
                    </tr>
                </table>
              </form>';
    }

    /*FORM EDIT*/
    if($page==2)
    {
        $res = mysqli_query($conn, "SELECT * FROM barang WHERE ID='$id_barang'");
        $row = mysqli_fetch_row($res);

        echo '<legend class="title">
                <a href="?menu=barang" style="text-decoration:none; color:#aaa;">Daftar Barang</a>
                <span>› Edit</span>
              </legend>';

        echo '<form method="post" action="?menu=barang&page=2&id='.$id_barang.'">
                <input type="hidden" name="exec" value="1">
                <table>
                    <tr>
                        <td>Nama Barang</td>
                        <td><input type="text" name="brng" value="'.$row[1].'"></td>
                    </tr>
                    <tr>
                        <td>Harga</td>
                        <td><input type="number" name="hrga" value="'.$row[2].'"></td>
                    </tr>
                    <tr>
                        <td>Stok</td>
                        <td><input type="number" name="stck" value="'.$row[3].'"></td>
                    </tr>
                    <tr>
                        <td>Jenis</td>
                        <td>
                            <select name="id_tipe">';

        $data = mysqli_query($conn, "SELECT * FROM tipe");
        while($d = mysqli_fetch_array($data))
        {
            $selected = ($d['id_tipe'] == $row[5]) ? 'selected' : '';
            echo "<option value='".$d['id_tipe']."' $selected>".$d['nama_tipe']."</option>";
        }

        echo '          </select>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <input type="submit" value="Simpan">
                            <a href="?menu=barang" class="btn-batal">Kembali</a>
                        </td>
                    </tr>
                </table>
              </form>';
    }

    /*KONFIRMASI HAPUS*/
    if($page==3)
    {
        $res = mysqli_query($conn, "SELECT Name FROM barang WHERE ID='$id_barang'");
        $row = mysqli_fetch_row($res);

        echo '<legend class="title">
                <a href="?menu=barang" style="text-decoration:none; color:#aaa;">Daftar Barang</a>
                <span>› Hapus</span>
              </legend>';

        echo '<div class="alert-hapus">
                Apakah anda yakin ingin menghapus <b>'.$row[0].'</b>?
              </div>';

        echo '<a href="?menu=barang&page=4&id='.$id_barang.'" class="btn-hapus">Hapus</a>
              <a href="?menu=barang" class="btn-batal">Batal</a>';
    }

    /*TAMPIL DATA TABEL*/

if($page==0)
{
    echo '<legend class="title">Daftar Barang</legend>';
    echo '<a href="?menu=barang&page=1" class="btn-tambah">Tambah</a>';

    echo '<table border="1">
            <tr>
                <th class="center">No</th>
                <th class="center">Nama Barang</th>
                <th class="center">Harga</th>
                <th class="center">Stok</th>
                <th class="center">Jenis</th>
                <th class="center">Ditambahkan</th>
                <th class="center">Edit</th>
            </tr>';

    $res = mysqli_query($conn, "
        SELECT barang.ID,
               barang.Name,
               barang.Price,
               barang.Stock,
               tipe.nama_tipe,
               barang.Added,
               barang.Status
        FROM barang
        LEFT JOIN tipe ON barang.id_tipe = tipe.id_tipe
        ORDER BY barang.Added DESC
    ");

    $count = mysqli_num_rows($res);

    if($count == 0)
    {
        echo '<tr><td colspan="7">Data kosong</td></tr>';
    }
    else
    {
        $no = 1;
        while($row = mysqli_fetch_row($res))
        {
            echo '<tr id="'.$row[0].'" style="'.($row[6] ? '' : 'opacity:0.5;').'">
                    <td class="center">'.$no++.'</td>
                    <td>'.$row[1].'</td>
                    <td class="center">Rp '.number_format($row[2],0,",",".").'</td>
                    <td class="center">'.$row[3].'</td>
                    <td class="center">'.$row[4].'</td>
                    <td class="center">'.date('d F Y, H:i', strtotime($row[5])).' WIB</td>
                    <td>
                        <a href="?menu=barang&page=2&id='.$row[0].'" class="edit">≡</a>
                        <a href="?menu=barang&page=3&id='.$row[0].'" class="hapus">×</a>
                        <a onclick="ToggleStatus(\''.$row[0].'\')" class="view">👁</a>
                    </td>
                  </tr>';
        }
    }

    echo '</table>';

    if($count > 0)
    {
        echo '<br>'.$count.' data ditemukan';
    }
}

}
?>
