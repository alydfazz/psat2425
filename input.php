<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $kelas = $_POST['kelas'];
    $absen = $_POST['absen'];
    $foto = $_FILES['foto']['name'];
    $tmp = $_FILES['foto']['tmp_name'];
    move_uploaded_file($tmp, "uploads/" . $foto);
    $conn->query("INSERT INTO siswa (nama, kelas, absen, foto) VALUES ('$nama', '$kelas', '$absen', '$foto')");
    header("Location: dashboard.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Input Siswa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="style.css" rel="stylesheet" /></head>
<body>

<footer class="text-center mt-0 py-0" style="background-color: DarkSlateGray; color: white;">
    IP Lokal : <?= $_SERVER['SERVER_ADDR'] ?><br>
    <?php echo 'IP Public : ' . file_get_contents('http://checkip.amazonaws.com'); ?>
</footer>

<?php include 'menu.php'; ?>

<div class="container mt-4">
        <div style='text-align:center;'>
        <h2 >Penilaian Sumatif Akhir Tahun</h2>
        <h4>Mapil DevOps XI TJKT 1 - Penilaian Praktek</h4>
        <p>SMKN 1 Banyumas - TA. 2024 2025</p>
        <hr>
        </div>
    
    <h4>Input Data Siswa</h4>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="nama" class="form-control mb-2" placeholder="Nama" required>
        <input type="text" name="kelas" class="form-control mb-2" placeholder="Kelas" required>
        <input type="number" name="absen" class="form-control mb-2" placeholder="Absen" required>
        <input type="file" name="foto" class="form-control mb-2" required>
        <button type="submit" class="btn btn-success">Simpan</button>
    </form>
</div>
</body>
</html>