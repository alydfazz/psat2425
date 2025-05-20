<?php

function loadEnv($path) {
    if (!file_exists($path)) return;

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;

        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        putenv("$key=$value");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

loadEnv(__DIR__ . '/.env');

$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$db_name = getenv('DB_NAME');

// Koneksi tanpa database dulu untuk buat database jika belum ada
$conn_temp = new mysqli($host, $user, $pass);
if ($conn_temp->connect_error) die("Koneksi gagal: " . $conn_temp->connect_error);

// Buat database jika belum ada
$conn_temp->query("CREATE DATABASE IF NOT EXISTS `$db_name`");
$conn_temp->close();

// Koneksi ke database yang sudah dijamin ada
$conn = new mysqli($host, $user, $pass, $db_name);
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

// Buat tabel siswa jika belum ada
$table_check = $conn->query("SHOW TABLES LIKE 'siswa'");
if ($table_check->num_rows == 0) {
    $sql = "CREATE TABLE siswa (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama VARCHAR(100),
        kelas VARCHAR(50),
        absen INT,
        foto VARCHAR(255)
    )";
    $conn->query($sql);
}

// Buat tabel user jika belum ada
$table_check_user = $conn->query("SHOW TABLES LIKE 'user'");
if ($table_check_user->num_rows == 0) {
    $sql_user = "CREATE TABLE user (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(50) NOT NULL
    )";
    $conn->query($sql_user);

    // Tambahkan user admin default (username: admin, password: 123)
    $conn->query("INSERT INTO user (username, password) VALUES ('admin', '123')");
}
?>
