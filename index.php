<?php
session_start();

if (isset($_SESSION['login'])) {
    header("Location: dashboard.php");
    exit;
}

require 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input_user = $_POST['username'];
    $input_pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $input_user, $input_pass);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        $_SESSION['login'] = true;
        $_SESSION['username'] = $row['username'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="style.css" rel="stylesheet" />
</head>
<body>
<footer class="text-center mt-0 py-0" style="background-color: DarkSlateGray; color: white;">
    IP Lokal : <?= $_SERVER['SERVER_ADDR'] ?><br>
    <?php echo 'IP Public : ' . file_get_contents('http://checkip.amazonaws.com'); ?>
</footer>

    <div class="container mt-5 mx-auto" style="max-width: 550px;">
        <div style='text-align:center;'>
        <h2 >Penilaian Sumatif Akhir Tahun</h2>
        <h4>Mapil DevOps XI TJKT 1 - Penilaian Praktek</h4>
        <p>SMKN 1 Banyumas - TA. 2024 2025</p>
        <hr>
        </div>
      
        <h4 class="mt-4 mb-3">Login</h4>


        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
<br>
        <form method="POST" autocomplete="off" novalidate>
            Username
            <input type="text" name="username" class="form-control mt-2 mb-3" placeholder="Username" required autofocus>
            Password
            <input type="password" name="password" class="form-control mt-2" placeholder="Password" required>
            <br><hr>
            <button type="submit" class="btn btn-primary mt-3  w-100">Login</button>
        </form>
    </div>
</body>
</html>
