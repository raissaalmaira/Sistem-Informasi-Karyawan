<?php
require('vendor/setasign/fpdf/fpdf.php');
include 'config.php';

class PDF extends FPDF
{
    // Margin dan pengaturan awal
    function __construct()
    {
        parent::__construct('L', 'mm', 'A4'); // Menggunakan orientasi landscape
        $this->SetMargins(10, 10, 10);  // Atur margin kiri, atas, kanan
        $this->SetAutoPageBreak(true, 10);  // Atur page break otomatis dengan jarak 10mm
    }

    // Header di tiap halaman
    function Header()
    {
        // Logo
        $logo = __DIR__ . '/Padang TV logo.png';
        if (file_exists($logo)) {
            $this->Image($logo, 10, 6, 25);  // Menampilkan logo dengan lebar 25mm
        }
        
        // Judul
        $this->SetFont('Arial','B',16);
        $this->Cell(0, 10, 'Laporan Gaji Karyawan - Padang TV', 0, 1, 'C');
        $this->Ln(5);  // Jarak setelah judul

        // Header Tabel
        $this->SetFont('Arial','B',10);
        $this->SetFillColor(200, 200, 200);  // Warna latar belakang header tabel
        $this->SetDrawColor(100, 100, 100);  // Warna garis tepi

        // Definisikan lebar kolom
        $cols = [
            'ID' => 10, 'Nama' => 40, 'Bulan' => 20, 'Tahun' => 20, 
            'Gaji Pokok' => 30, 'Tunjangan' => 30, 'Potongan' => 30, 'Total Gaji' => 30
        ];

        // Menampilkan header kolom
        foreach ($cols as $title => $width) {
            $this->Cell($width, 8, $title, 'LRB', 0, 'C', true);
        }
        $this->Ln();
    }

    // Footer di tiap halaman
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }

    // Fungsi untuk menambahkan baris dengan data
    function AddData($data)
    {
        $this->SetFont('Arial', '', 9);

        $this->Cell(10, 7, $data['id'], 'LRB', 0, 'C');
        $this->Cell(40, 7, $data['nama'], 'LRB', 0, 'L');
        $this->Cell(20, 7, $data['bulan'], 'LRB', 0, 'C');
        $this->Cell(20, 7, $data['tahun'], 'LRB', 0, 'C');

        // Format angka dengan pemisah ribuan dan dua desimal
        foreach (['gaji_pokok','tunjangan','potongan','total_gaji'] as $col) {
            $val = $data[$col] == 0 ? '-' : 'Rp ' . number_format($data[$col], 0, ',', '.');
            $this->Cell(30, 7, $val, 'LRB', 0, 'R');
        }

        $this->Ln();
    }
}

// Membuat objek PDF baru
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

// Query untuk mengambil data gaji karyawan
$query = "SELECT g.id, k.nama, g.bulan, g.tahun, g.gaji_pokok, g.tunjangan, g.potongan, g.total_gaji
          FROM gaji g
          JOIN karyawan k ON g.karyawan_id=k.id
          ORDER BY g.tahun DESC, g.bulan DESC";
$res = mysqli_query($conn, $query);

// Menampilkan data dalam tabel
while ($r = mysqli_fetch_assoc($res)) {
    // Mengecek apakah data cukup banyak, jika ya, tambah halaman baru
    if ($pdf->GetY() > 250) {  // Jika sudah mendekati batas bawah halaman
        $pdf->AddPage();
    }

    $pdf->AddData($r);
}

// Output PDF ke browser
$pdf->Output();
exit;
?>
