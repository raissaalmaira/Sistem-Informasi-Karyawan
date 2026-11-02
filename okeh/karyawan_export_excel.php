<?php
require 'vendor/autoload.php';
include 'config.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

// 1. Siapkan spreadsheet & worksheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Data Karyawan');

// 2. Header kolom
$headers = ['A'=>'ID','B'=>'Foto','C'=>'Nama','D'=>'Tempat/Tgl Lahir','E'=>'Alamat','F'=>'Jabatan','G'=>'Status'];
foreach ($headers as $col => $text) {
    $sheet->setCellValue("{$col}1", $text);
    // styling header
    $sheet->getStyle("{$col}1")->getFont()->setBold(true);
    $sheet->getStyle("{$col}1")->getAlignment()->setHorizontal('center');
}

// 3. Ambil data karyawan
$query  = "SELECT * FROM karyawan";
$result = mysqli_query($conn, $query);

$rowNum = 2;
while ($row = mysqli_fetch_assoc($result)) {
    // 3a. Isi ID
    $sheet->setCellValue("A{$rowNum}", $row['id']);

    // 3b. Sisipkan gambar
    $file = __DIR__ . "/uploads/{$row['foto']}";
    if (!empty($row['foto']) && file_exists($file)) {
        $drawing = new Drawing();
        $drawing->setName('Foto '.$row['nama']);
        $drawing->setPath($file);
        $drawing->setHeight(50);            // atur tinggi gambar (px)
        $drawing->setCoordinates("B{$rowNum}");
        // offset agar ada jarak dari tepi sel
        $drawing->setOffsetX(5);
        $drawing->setOffsetY(5);
        $drawing->setWorksheet($sheet);
    }

    // 3c. Isi kolom lain
    $sheet->setCellValue("C{$rowNum}", $row['nama']);
    $sheet->setCellValue("D{$rowNum}", $row['tempat_lahir'].', '.$row['tanggal_lahir']);
    $sheet->setCellValue("E{$rowNum}", $row['alamat']);
    $sheet->setCellValue("F{$rowNum}", $row['jabatan']);
    $sheet->setCellValue("G{$rowNum}", ucfirst($row['status_karyawan']));

    // 3d. Set row height agar muat gambar
    $sheet->getRowDimension($rowNum)->setRowHeight(60);

    $rowNum++;
}

// 4. Auto‐size kolom (kecuali kolom Foto B, yang fix width)
foreach (range('A','G') as $col) {
    if ($col !== 'B') {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    } else {
        $sheet->getColumnDimension('B')->setWidth(15);
    }
}

// 5. Kirim header & output file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="data_karyawan.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
