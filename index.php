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
            
            $jumlahDataPerHalaman = 5;
            $jumlahData = count(query("SELECT * FROM nim177 ORDER BY tanggal_panen DESC"));
            $jumlahHalaman = ceil($jumlahData / $jumlahDataPerHalaman);
            $halamanAktif = (isset($_GET['halaman'])) ? $_GET['halaman'] : 1;
            $awalData = ($jumlahDataPerHalaman * $halamanAktif - $jumlahDataPerHalaman);

            $data_tani = query("SELECT * FROM nim177 ORDER BY tanggal_panen DESC");
        ?>

        <!-- fungsi tampil_data -->
        <?php function tampil_data($data_tani) { ?>
            <?php 
                global $halamanAktif;
                global $jumlahHalaman;
                global $awalData;
                global $jumlahDataPerHalaman;

                // join tabel kategori dan nim177, relasi tabel untuk atribut kode_kategori
                $query = query("SELECT * FROM kategori JOIN nim177 ON kategori.kode_kategori = nim177.kode_kategori  
                                ORDER BY tanggal_panen DESC 
                                LIMIT $awalData, $jumlahDataPerHalaman");
            ?>

                    <fieldset>
                        <legend>
                            <h2>Data Panen</h2>
                        </legend>

                        <a class="btn tambah" href="index.php?aksi=create">Tambah data</a>
            
                        <table class="data-panen" border="1" cellpadding="10" cellspacing="0">
            
                            <tr class="th">
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
                                    <a class="btn ubah" href="index.php?aksi=update&id=<?= $data['id']; ?>">Ubah</a>
                                    <a class="btn hapus" href="index.php?aksi=delete&id=<?= $data['id']; ?>">Hapus</a>
                                </td>

                            </tr>

                            <?php $i++ ?>

                        <?php endforeach; ?>

                        </table>
                        
                        <!-- navigasi halaman -->
                        <div class="nav">
                            <?php if($halamanAktif > 1) : ?>
                            <a class="prev" href="?halaman=<?= $halamanAktif - 1 ?>"></a>
                    
                            <?php endif; ?>
    
                            <?php for($i = 1; $i <= $jumlahHalaman; $i++) : ?> 
                                <?php if($i == $halamanAktif) : ?>
                                    <a class="halaman" href="?halaman=<?= $i; ?>" style="background: #38946e; color: #fff;"><?= $i; ?></a>
    
                                <?php else :?>
                                    <a class="halaman" href="?halaman=<?= $i; ?>"><?= $i; ?></a>
    
                                <?php endif; ?>
                            <?php endfor; ?>
                            
                            <?php if($halamanAktif < $jumlahHalaman) : ?>
                                <a class="next" href="?halaman=<?= $halamanAktif + 1 ?>"></a>
                    
                            <?php endif; ?>
                            
                        </div>
                    
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

                    <a class="back" href="index.php"><span>Home</span></a>

                    <ul>
                        <li>
                            <label for="kategori">Kategori</label>
                            <select name="kategori" id="kategori">
                                <?php 
                                    // query menampilkan kategori pada combobox
                                    $query = query("SELECT * FROM kategori ORDER BY id_kategori ASC");
                                    foreach($query as $data) : ?>
                                        <option value="<?= $data['kode_kategori'] ?>"><?= $data['nama_kategori']; ?></option>';
                                    
                                <?php endforeach; ?>

                            </select>
                        </li>
                        <li>
                            <label>Nama tanaman <input type="text" name="nm_tanaman" id="nm-tanaman" autocomplete="off"></label>
                        </li>
                        <li>
                            <label>Hasil panen (kg) <input type="number" name="hasil" id="hasil"></label>
                        </li>
                        <li>
                            <label>Lama tanam (bulan) <input type="number" name="lama" id="lama"></label>
                        </li>
                        <li>
                            <label>Tanggal panen <input type="date" name="tgl_panen" id="tgl-panen"></label>
                        </li>
                        <li>
                            <label>
                                <button class="btn simpan" type="submit" name="btn_simpan">Simpan</button>
                                <button class="btn bersihkan" type="reset" name="reset">Bersihkan</button>
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
                    <form action="" method="POST">
                        <fieldset>
                            <legend>
                                <h2>Ubah Data</h2>
                            </legend>

                            <div class="btn-container">
                                <a class="back" href="index.php"><span>Home</span></a>
                                <a class="plus" href="index.php?aksi=create"><span>Tambah Data</span></a>
                            </div>

                            <input type="hidden" name="id" value="<?= $id; ?>">
                            <ul>
                                <li>
                                    <label>Kategori</label>
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
                                    <label>Nama tanaman <input type="text" name="nm_tanaman" id="nm-tanaman" value="<?= $data_tani['nama_tanaman']; ?>"></label>
                                </li>
                                <li>
                                    <label>Hasil panen (kg) <input type="number" name="hasil" id="hasil" value="<?= $data_tani['hasil_panen']; ?>"></label>
                                </li>
                                <li>
                                    <label>Lama tanam (bulan) <input type="number" name="lama" id="lama" value="<?= $data_tani['lama_tanam']; ?>"></label>
                                </li>
                                <li>
                                    <label>Tanggal panen <input type="date" name="tgl_panen" id="tgl-panen" value="<?= $data_tani['tanggal_panen']; ?>"></label>
                                </li>
                                <li>
                                    <label>
                                        <a class="btn simpan" type="submit" name="btn_ubah">Simpan Perubahan</a>
                                        <a class="btn bersihkan" href="index.php?aksi=delete&id=<?= $_GET['id'] ?>">(x) Hapus Data Ini</a>
                                    </label>
                                </li>
                                <li>
                                    <p><?= isset($pesan) ? $pesan : ""; ?></p>
                                </li>
                                <li>
                                    <a class="btn settings" href="index.php?settings=read">Kategori</a>
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