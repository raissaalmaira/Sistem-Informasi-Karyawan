<?php
require('vendor/setasign/fpdf/fpdf.php');
include 'config.php';

$id = $_GET['id'];
$query = "SELECT gaji.*, karyawan.nama FROM gaji 
          JOIN karyawan ON gaji.karyawan_id = karyawan.id 
          WHERE gaji.id = $id";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

// Membuat objek PDF baru
$pdf = new FPDF('P', 'mm', 'A4');  // Format Portrait

// Menambahkan halaman baru
$pdf->AddPage();

// Menambahkan Logo
$logo = __DIR__ . '/Padang TV logo.png';  // Pastikan path logo benar
if (file_exists($logo)) {
    $pdf->Image($logo, 10, 10, 30); // Menampilkan logo (lebar 30mm)
}

// Judul Slip Gaji
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(190, 10, 'SLIP GAJI KARYAWAN - PADANG TV', 0, 1, 'C');
$pdf->Ln(10);

// Mengatur font untuk informasi gaji
$pdf->SetFont('Arial', '', 12);

// Mengonversi bulan numerik menjadi nama bulan
$bulanArray = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', 
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', 
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];

// Pastikan bulan yang diterima berupa string dua digit
$bulanString = str_pad($data['bulan'], 2, '0', STR_PAD_LEFT);

// Mengambil bulan dan mengonversinya
$bulanNama = isset($bulanArray[$bulanString]) ? $bulanArray[$bulanString] : $data['bulan'];

// Menampilkan informasi karyawan dan gaji
$pdf->Cell(50, 10, 'Nama Karyawan', 0, 0);
$pdf->Cell(100, 10, ': ' . $data['nama'], 0, 1);
$pdf->Cell(50, 10, 'Bulan', 0, 0);
$pdf->Cell(100, 10, ': ' . $bulanNama, 0, 1);  // Menampilkan nama bulan
$pdf->Cell(50, 10, 'Tahun', 0, 0);
$pdf->Cell(100, 10, ': ' . $data['tahun'], 0, 1);
$pdf->Ln(5);

// Menampilkan rincian gaji
$pdf->Cell(50, 10, 'Gaji Pokok', 0, 0);
$pdf->Cell(100, 10, ': Rp ' . number_format($data['gaji_pokok'], 2, ',', '.'), 0, 1);
$pdf->Cell(50, 10, 'Tunjangan', 0, 0);
$pdf->Cell(100, 10, ': Rp ' . number_format($data['tunjangan'], 2, ',', '.'), 0, 1);
$pdf->Cell(50, 10, 'Potongan', 0, 0);
$pdf->Cell(100, 10, ': Rp ' . number_format($data['potongan'], 2, ',', '.'), 0, 1);
$pdf->Cell(50, 10, 'Total Gaji', 0, 0);
$pdf->Cell(100, 10, ': Rp ' . number_format($data['total_gaji'], 2, ',', '.'), 0, 1);
$pdf->Ln(20);

// Menambahkan footer
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(190, 10, 'Terima Kasih atas Kerja Keras Anda!', 0, 1, 'C');

// Output file PDF
$pdf->Output();
?>
