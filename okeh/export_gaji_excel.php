<?php
require 'vendor/autoload.php';
include 'config.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Menambahkan logo perusahaan
$logo = new Drawing();
$logo->setName('Logo');
$logo->setDescription('Logo Perusahaan');
$logo->setPath('Padang TV logo.png'); // Ganti path ke logo kamu
$logo->setHeight(50);
$logo->setCoordinates('A1');
$logo->setWorksheet($sheet);

// Menambahkan judul besar
$sheet->mergeCells('A2:H2');
$sheet->setCellValue('A2', 'Data Gaji Karyawan Padang TV');
$sheet->getStyle('A2')->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 16,
        'color' => ['rgb' => '800000'], // Warna maroon
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
]);

// Set Header
$header = ['ID', 'Nama', 'Bulan', 'Tahun', 'Gaji Pokok', 'Tunjangan', 'Potongan', 'Total Gaji'];
$col = 'A';
foreach ($header as $head) {
    $sheet->setCellValue($col . '3', $head);
    $col++;
}

// Ambil data dari database
$query = "SELECT gaji.*, karyawan.nama FROM gaji 
          JOIN karyawan ON gaji.karyawan_id = karyawan.id 
          ORDER BY tahun DESC, bulan DESC";
$result = mysqli_query($conn, $query);

$rowNum = 4;
while ($row = mysqli_fetch_assoc($result)) {
    $sheet->setCellValue('A' . $rowNum, $row['id']);
    $sheet->setCellValue('B' . $rowNum, $row['nama']);
    $sheet->setCellValue('C' . $rowNum, $row['bulan']);
    $sheet->setCellValue('D' . $rowNum, $row['tahun']);

    // Cek angka 0 jadi "-"
    $sheet->setCellValue('E' . $rowNum, $row['gaji_pokok'] == 0 ? '-' : $row['gaji_pokok']);
    $sheet->setCellValue('F' . $rowNum, $row['tunjangan'] == 0 ? '-' : $row['tunjangan']);
    $sheet->setCellValue('G' . $rowNum, $row['potongan'] == 0 ? '-' : $row['potongan']);
    $sheet->setCellValue('H' . $rowNum, $row['total_gaji'] == 0 ? '-' : $row['total_gaji']);

    $rowNum++;
}

$lastRow = $rowNum - 1;

// Format Kolom Uang
foreach (['E', 'F', 'G', 'H'] as $col) {
    $sheet->getStyle($col . '4:' . $col . $lastRow)
          ->getNumberFormat()
          ->setFormatCode('"Rp"#,##0;-"');
}

// Styling Header
$sheet->getStyle('A3:H3')->applyFromArray([
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'], // Warna putih
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '800000'], // Warna maroon
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
]);

// Border untuk semua tabel
$sheet->getStyle('A3:H' . $lastRow)->applyFromArray([
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000'],
        ],
    ],
]);

// Auto width
foreach (range('A', 'H') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Output
$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="data_gaji.xlsx"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit;
?>
