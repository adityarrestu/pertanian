<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="style/style.css">
        <title>Data Pertanian</title>
    </head>
    <body>

        <?php 
            require 'settings.php';

            // koneksi ke database
            $conn = mysqli_connect("localhost", "root", "", "aditya_db");

            // fungsi query database
            function query($query) {
                global $conn;
                $result = mysqli_query($conn, $query);
                $rows = [];

                while($row = mysqli_fetch_assoc($result)) {
                    $rows[] = $row;
                }
                return $rows;
            }
            
            $data_tani = query("SELECT * FROM nim177 ORDER BY tanggal_panen DESC");
        ?>

        <!-- fungsi tampil_data -->
        <?php function tampil_data($data_tani) { ?>
            <?php 
                // join tabel kategori dan nim177, relasi tabel untuk atribut kode_kategori
                $query = query("SELECT * FROM kategori JOIN nim177 ON kategori.kode_kategori = nim177.kode_kategori");
            ?>

                    <fieldset>
                        <legend>
                            <h2>Data Panen</h2>
                        </legend>

                        <button class="btn-tambah"><a class="tambah" href="index.php?aksi=create">Tambah data</a></button>
            
                        <table class="data-panen" border="1" cellpadding="10" cellspacing="0">
            
                            <tr>
                                <th>No.</th>
                                <th>Nama Kategori</th>
                                <th>Nama Tanaman</th>
                                <th>Hasil Panen  <br> (kilogram) </th>
                                <th>Lama Tanam <br> (bulan) </th>
                                <th>Tanggal Panen</th>
                                <th>Tindakan</th>
                            </tr>
            
                        <?php $i = 1; ?>
                        <?php foreach($query as $data) : ?>
                            <tr>      
                                <td><?= $i; ?></td>
                                <td><?= $data['nama_kategori']; ?></td>
                                <td><?= $data['nama_tanaman']; ?></td>
                                <td><?= $data['hasil_panen']; ?></td>
                                <td><?= $data['lama_tanam']; ?></td>
                                <td><?= $data['tanggal_panen']; ?></td>
                                <td>
                                    <a href="index.php?aksi=update&id=<?= $data['id']; ?>">Ubah</a>
                                    <a href="index.php?aksi=delete&id=<?= $data['id']; ?>">Hapus</a>
                                </td>

                            </tr>

                            <?php $i++ ?>

                        <?php endforeach; ?>

                        </table>
                    
                </fieldset>          
        <?php } ?>
    
        <!-- fungsi tambah data -->
        <?php 
            function tambah($data) {
                global $conn;

                if(isset($data['btn_simpan'])) {
                    $id = time();
                    $nm_tanaman = mysqli_real_escape_string($conn, htmlspecialchars($data['nm_tanaman']));
                    $hasil = mysqli_real_escape_string($conn, htmlspecialchars($data['hasil']));
                    $lama = mysqli_real_escape_string($conn, htmlspecialchars($data['lama']));
                    $tgl_panen = mysqli_real_escape_string($conn, htmlspecialchars($data['tgl_panen']));
                    $kategori = mysqli_real_escape_string($conn, htmlspecialchars($data['kategori']));

                    if(!empty($nm_tanaman) && !empty($hasil) && !empty($lama) && !empty($tgl_panen) && !empty($kategori)) {

                        $query = "INSERT INTO nim177 VALUES ('', '$nm_tanaman', '$hasil', '$lama', '$tgl_panen', '$kategori')";

                        $simpan = mysqli_query($conn, $query);
                        if($simpan &&  isset($_GET['aksi']) == 'create') {
                            header('Location: index.php');
                        }

                    } else {
                        $pesan = "Tidak dapat menyimapn, data belum lengkap!"; 

                    }
                }
        ?> 
            <form action="" method="POST">
                <fieldset>
                    <legend>
                        <h2>Tambah Data</h2>
                    </legend>

                    <a href="index.php"> &laquo; Home</a>

                    <ul>
                        <li>
                            <select name="kategori">
                                <?php 
                                    // query menampilkan kategori pada combobox
                                    $query = query("SELECT * FROM kategori ORDER BY id_kategori ASC");
                                    foreach($query as $data) : ?>
                                        <option value="<?= $data['kode_kategori'] ?>"><?= $data['nama_kategori']; ?></option>';
                                    
                                <?php endforeach; ?>

                            </select>
                        </li>
                        <li>
                            <label>Nama tanaman <input type="text" name="nm_tanaman"></label>
                        </li>
                        <li>
                            <label>Hasil panen <input type="number" name="hasil"> kg</label>
                        </li>
                        <li>
                            <label>Lama tanam <input type="number" name="lama"> bulan</label>
                        </li>
                        <li>
                            <label>Tanggal panen <input type="date" name="tgl_panen"></label>
                        </li>
                        <li>
                            <label>
                                <button type="submit" name="btn_simpan">Simpan</button>
                                <button type="reset" name="reset">Bersihkan</button>
                            </label>
                        </li>
                        <li>
                            <p><?= isset($pesan) ? $pesan : ""; ?></p>
                        </li>
                    </ul>

                </fieldset>
            </form>

        <?php } ?>
        
        <!-- fungsi ubah data -->
        <?php 
            function ubah($data) {
                global $conn;

                if(isset($_POST['btn_ubah'])) {
                    $id = $_POST['id'];
                    $kategori = mysqli_real_escape_string($conn, htmlspecialchars($data['kategori']));
                    $nm_tanaman = mysqli_real_escape_string($conn, htmlspecialchars($data['nm_tanaman']));
                    $hasil = mysqli_real_escape_string($conn, htmlspecialchars($data['hasil']));
                    $lama = mysqli_real_escape_string($conn, htmlspecialchars($data['lama']));
                    $tgl_panen = mysqli_real_escape_string($conn, htmlspecialchars($data['tgl_panen']));

                    if(!empty($nm_tanaman) && !empty($hasil) && !empty($lama) && !empty($tgl_panen)) {

                        $query = "UPDATE nim177 SET 
                            nama_tanaman = '$nm_tanaman',
                            hasil_panen = '$hasil',
                            lama_tanam = '$lama',
                            tanggal_panen = '$tgl_panen',
                            kode_kategori = '$kategori'
                            WHERE id = '$id'
                        ";

                        $update = mysqli_query($conn, $query);
                        if($update &&  isset($_GET['aksi']) == 'update') {
                            header('Location: index.php');
                        }

                    } else {
                        $pesan = "Data tidak lengkap!"; 

                    }
                }

                if(isset($_GET['id'])) :
                    $id = $_GET['id'];
                    $data_tani = query("SELECT * FROM nim177 WHERE id = $id")[0];
        ?>
                    <a href="index.php">&laquo; Home</a>
                    <a href="index.php?aksi=create"> (+) Tambah Data</a>
                    <br>

                    <form action="" method="POST">
                        <fieldset>
                            <legend>
                                <h2>Ubah Data</h2>
                            </legend>

                            <input type="hidden" name="id" value="<?= $id; ?>">
                            <ul>
                                <li>
                                    <select name="kategori">
                                        <?php 
                                            // query menampilkan kategori pada combobox
                                            $query = query("SELECT * FROM kategori ORDER BY id_kategori DESC");
                                            foreach($query as $data) : ?>
                                                <option value="<?= $data['kode_kategori'] ?>"><?= $data['nama_kategori']; ?></option>';

                                        <?php endforeach; ?>

                                    </select>
                                </li>
                                <li>
                                    <label>Nama tanaman <input type="text" name="nm_tanaman" value="<?= $data_tani['nama_tanaman']; ?>"></label>
                                </li>
                                <li>
                                    <label>Hasil panen <input type="number" name="hasil" value="<?= $data_tani['hasil_panen']; ?>"> kg</label>
                                </li>
                                <li>
                                    <label>Lama tanam <input type="number" name="lama" value="<?= $data_tani['lama_tanam']; ?>"> bulan</label>
                                </li>
                                <li>
                                    <label>Tanggal panen <input type="date" name="tgl_panen" value="<?= $data_tani['tanggal_panen']; ?>"></label>
                                </li>
                                <li>
                                    <label>
                                        <button type="submit" name="btn_ubah">Simpan Perubahan</button>
                                        <button><a href="index.php?aksi=delete&id=<?= $_GET['id'] ?>">(x) Hapus Data Ini</a></button>
                                    </label>
                                </li>
                                <li>
                                    <p><?= isset($pesan) ? $pesan : ""; ?></p>
                                </li>
                                <li>
                                    <button><a href="index.php?settings=read">Pengaturan</a></button>
                                </li>
                            </ul>

                        </fieldset>

                    </form>
        <?php
                endif;

            }
        ?>

        <!-- fungsi hapus data -->
        <?php 
            function hapus() {
                global $conn;

                if(isset($_GET['id']) && isset($_GET['aksi'])) {
                    $id = $_GET['id'];
                    $query = "DELETE FROM nim177 WHERE id = '$id'";

                    $hapus = mysqli_query($conn, $query);
                    if($hapus && $_GET['aksi'] == 'delete') {
                        header('Location: index.php');
                    }
                }

            }
        ?>
        
        <!-- fungsi utama -->
        <?php 
            if(isset($_GET['aksi'])) {
                switch($_GET['aksi']) {
                    case 'create':
                        tambah($_POST);
                        break;
                    
                    case "read":
                        tampil_data($data_tani);
                        break;

                    case 'update':
                        ubah($_POST);
                        break;
                    
                    case 'delete':
                        hapus();
                        break;

                    default:
                        break;
                }
                
            } else if(isset($_GET['settings'])) {
                switch($_GET['settings']) {
                    case 'create':
                        tambah_kategori($_POST);
                        break;

                    case 'read':
                        settings($opsi);
                        break;

                    case 'update':
                        ubah_kategori($_POST);
                        break;

                    case 'delete':
                        hapus_kategori();
                        break;

                    default:
                        break;
                }

            } else {
                tampil_data($data_tani);

            }

        ?>    
        
    </body>
</html>