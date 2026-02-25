<?php
require('../fpdf/fpdf.php');
include '../config/koneksi.php';

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(190,10,'Laporan Transaksi Inventaris',0,1,'C');

$pdf->SetFont('Arial','',10);
$pdf->Cell(10,8,'No',1);
$pdf->Cell(50,8,'Produk',1);
$pdf->Cell(30,8,'Tipe',1);
$pdf->Cell(30,8,'Jumlah',1);
$pdf->Cell(40,8,'Tanggal',1);
$pdf->Ln();

$data = mysqli_query($conn,"
SELECT t.*, p.name 
FROM transactions t
JOIN products p ON t.product_id=p.id
");

$no=1;
while($row=mysqli_fetch_assoc($data)){
    $pdf->Cell(10,8,$no++,1);
    $pdf->Cell(50,8,$row['name'],1);
    $pdf->Cell(30,8,$row['transaction_type'],1);
    $pdf->Cell(30,8,$row['total'],1);
    $pdf->Cell(40,8,$row['date'],1);
    $pdf->Ln();
}

$pdf->Output();