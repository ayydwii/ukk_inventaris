<?php
$conn = mysqli_connect("localhost", "root", "", "ukk_inventaris");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>