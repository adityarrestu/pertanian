<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="style/style.css">
        <link rel="stylesheet" href="style/kategori.css">
        <title>Pengaturan Kategori</title>
    </head>
    <body>
        
        <?php 
            $conn = mysqli_connect("localhost", "root", "", "aditya_db");

            function querys($query) {
                global $conn;
                $result = mysqli_query($conn, $query);
                $rows = [];

                while($row = mysqli_fetch_assoc($result)) {
                    $rows[] = $row;
                }
                return $rows;
            }
            
            $opsi = querys("SELECT * FROM kategori ORDER BY kode_kategori ASC");
        ?>

        <!-- fungsi tampil kategori -->
        <?php function settings($opsi) { 
            global $conn;
        ?>       

            <form action="index.php" method="POST">
                <fieldset>
                    <legend>
                        <h2>Daftar Kategori</h2>
                    </legend>

                    <div class="btn-container">
                        <a class="back" href="index.php"><span>Home</span></a>
                        <a class="plus-kategori" href="index.php?settings=create"><span>Tambah Kategori</span></a>
                    </div>
                    
                    <table class="opsi-kategori" border="1" cellpadding="10" cellspacing="0">
    
                        <tr class="th">
                            <th>No.</th>
                            <th>Kode Kategori</th>
                            <th>Nama Kategori</th>
                            <th>Tindakan</th>
                        </tr>
    
                        <?php $i = 1; ?>
                        <?php foreach($opsi as $data) : ?>
                            <tr>
                                <td><?= $i; ?></td>
                                <td><?= $data['kode_kategori']; ?></td>
                                <td><?= $data['nama_kategori']; ?></td>
                                <td>
                                    <a class="btn ubah" href="index.php?settings=update&id=<?= $data['id_kategori']; ?>">Ubah</a>
                                    <a class="btn hapus" href="index.php?settings=delete&id=<?= $data['id_kategori']; ?>">Hapus</a>
                                </td>
                            </tr>
    
                            <?php $i++; ?>
    
                        <?php endforeach; ?>
    
                    </table>
    
                </fieldset>
            </form>

        <?php } ?>

        <!-- fungsi tambah opsi -->
        <?php 
            function tambah_kategori($data) {
                var_dump($data);
                global $conn;

                if(isset($data['btn_simpan'])) {
                    $id = time();
                    $kode_kategori = mysqli_real_escape_string($conn, htmlspecialchars($data['kode_kategori']));
                    $nama_kategori = mysqli_real_escape_string($conn, htmlspecialchars($data['nama_kategori']));

                    if(!empty($kode_kategori) && !empty($nama_kategori)) {

                        $query = "INSERT INTO kategori VALUES ('', '$kode_kategori', '$nama_kategori')";

                        $simpan = mysqli_query($conn, $query);
                        if($simpan &&  isset($_GET['settings']) == 'create') {
                            header('Location: index.php?settings=read');
                        }

                    } else {
                        $pesan = "Tidak dapat menyimapn, data belum lengkap!"; 

                    }
                }
        ?> 
            <form action="" method="POST">
                <fieldset>
                    <legend>
                        <h2>Tambah Kategori</h2>
                    </legend>

                    <a class="back" href="index.php?settings=read"><span>Kembali</span></a>

                    <ul>
                        </li>
                        <li>
                            <label for="nama_kategori">Nama Kategori</label>
                            <input id="nm-kategori" type="text" name="nama_kategori" id="nama_kategori">
                        </li>
                        <li>
                            <label for="kode_kategori">Kode Kategori</label>
                            <input id="kode-kategori" type="text" name="kode_kategori" id="kode_kategori">
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

        <!-- fungsi ubah kategori -->
        <?php 
            function ubah_kategori($data) {
                global $conn;

                if(isset($_POST['btn_ubah'])) {
                    $id = $_POST['id'];
                    $kode_kategori = mysqli_real_escape_string($conn, htmlspecialchars($data['kode_kategori']));
                    $nama_kategori = mysqli_real_escape_string($conn, htmlspecialchars($data['nama_kategori']));

                    if(!empty($kode_kategori) && !empty($nama_kategori)) {

                        $query = "UPDATE kategori SET
                            kode_kategori = '$kode_kategori',
                            nama_kategori = '$nama_kategori'
                            WHERE id_kategori = '$id'
                        ";

                        $update = mysqli_query($conn, $query);
                        if($update && isset($_GET['settings'])) {
                            header('Location: index.php?settings=read');
                        }

                    } else {
                        $pesan = "Data tidak lengkap!";

                    }
                }

                if(isset($_GET['id'])) :
                    $id = $_GET['id'];
                    $opsi = querys("SELECT * FROM kategori WHERE id_kategori = $id")[0];
        ?>
                    <form action="" method="POST">
                        <fieldset>
                            <legend>
                                <h2>Ubah Kategori</h2>
                            </legend>

                            <div class="btn-container">
                                <a class="back" href="index.php?settings=read"><span>Kembali</span></a>
                                <a class="plus-kategori" href="index.php?settings=create"><span>Tambah Kategori</span></a>
                            </div>


                            <input type="hidden" name="id" value="<?= $id; ?>">
                            <ul>
                                <li>
                                    <label for="kode_kategori">Kode Kategori</label>
                                    <input id="kode-kategori" type="text" name="kode_kategori" id="kode_kategori" value="<?= $opsi['kode_kategori'] ?>">
                                </li>
                                <li>
                                    <label for="nama_kategori">Nama Kategori</label>
                                    <input id="nm-kategori" type="text" name="nama_kategori" id="nama_kategori" value="<?= $opsi['nama_kategori'] ?>">
                                </li>
                                <li>
                                    <label>
                                        <a class="btn simpan" type="submit" name="btn_ubah">Simpan Perubahan</a>
                                        <a class="btn bersihkan" href="index.php?aksi=delete&id=<?= $_GET['id'] ?>">Hapus Kategori Ini</a>
                                    </label>
                                </li>
                                <li>
                                    <p><?= isset($pesan) ? $pesan : ""; ?></p>
                                </li>
                            </ul>

                        </fieldset>

                    </form>

        <?php 
                endif;
        
            }
        ?>

        <!-- fungsi hapus kategori -->
        <?php 
            function hapus_kategori() {
                global $conn;
                
                if(isset($_GET['id']) && isset($_GET['settings'])) {
                    var_dump($_GET['id']);
                    $id = $_GET['id'];
                    $query = "DELETE FROM kategori WHERE id_kategori = '$id'";

                    $hapus = mysqli_query($conn, $query);
                    if($hapus && $_GET['settings'] == 'delete') {
                        header('Location: index.php?settings=read');
                    }
                }        
            }
        ?>

    </body>
</html>