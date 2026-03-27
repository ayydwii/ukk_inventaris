<?php
$conn = mysqli_connect("localhost", "root", "", "dwi_ukk_inventaris");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>