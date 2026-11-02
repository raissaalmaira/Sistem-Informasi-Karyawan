<?php
require('vendor/setasign/fpdf/fpdf.php');
include 'config.php';

// Ambil data karyawan
$query = "SELECT * FROM karyawan";
$result = mysqli_query($conn, $query);

// Buat PDF baru
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Judul
$pdf->Cell(0, 10, 'Daftar Karyawan Padang TV', 0, 1, 'C');
$pdf->Ln(10);

// Header tabel
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 10, 'ID', 1);
$pdf->Cell(30, 10, 'Foto', 1);  // Kolom Foto
$pdf->Cell(40, 10, 'Nama', 1);
$pdf->Cell(40, 10, 'Tempat/Tgl Lahir', 1);
$pdf->Cell(50, 10, 'Alamat', 1);
$pdf->Cell(30, 10, 'Jabatan', 1);
$pdf->Cell(20, 10, 'Status', 1);
$pdf->Ln();

// Isi tabel
$pdf->SetFont('Arial', '', 10);
while ($row = mysqli_fetch_assoc($result)) {
    $pdf->Cell(10, 10, $row['id'], 1);
    
    // Cek apakah ada foto
    if (!empty($row['foto']) && file_exists('uploads/' . $row['foto'])) {
        // Tambahkan gambar (menyesuaikan ukuran dan posisi)
        $pdf->Image('uploads/' . $row['foto'], $pdf->GetX(), $pdf->GetY(), 20, 20);  // x, y, width, height
    } else {
        $pdf->Cell(30, 10, 'No Photo', 1);  // Jika tidak ada foto
    }

    $pdf->Cell(40, 10, $row['nama'], 1);
    $pdf->Cell(40, 10, $row['tempat_lahir'] . ', ' . $row['tanggal_lahir'], 1);
    $pdf->Cell(50, 10, $row['alamat'], 1);
    $pdf->Cell(30, 10, $row['jabatan'], 1);
    $pdf->Cell(20, 10, ucfirst($row['status_karyawan']), 1);
    $pdf->Ln();
}

// Output PDF
$pdf->Output('D', 'data_karyawan.pdf'); // 'D' = download langsung
?>
