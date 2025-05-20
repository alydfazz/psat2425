<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}
include 'db.php';
$id = $_GET['id'];
$conn->query("DELETE FROM siswa WHERE id=$id");
header("Location: dashboard.php");
?>