<?php
require 'vendor/autoload.php';
include 'config.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

// 1) Mapping bulan → nama
$bulanList = [
    1  => 'Januari',   2  => 'Februari', 3  => 'Maret',
    4  => 'April',     5  => 'Mei',      6  => 'Juni',
    7  => 'Juli',      8  => 'Agustus',  9  => 'September',
    10 => 'Oktober', 11 => 'November', 12 => 'Desember',
];

// 2) Ambil & sanitize parameter
$search  = $_GET['search']   ?? '';
$sort_by = in_array($_GET['sort_by'] ?? '', ['id','nama']) ? $_GET['sort_by'] : 'tahun';
$order   = in_array($_GET['order']   ?? '', ['ASC','DESC']) ? $_GET['order'] : 'DESC';
$bulan   = isset($_GET['bulan']) && $_GET['bulan'] !== '' ? intval($_GET['bulan']) : null;
$tahun   = isset($_GET['tahun']) && $_GET['tahun'] !== '' ? intval($_GET['tahun']) : null;

// 3) Build WHERE clauses
$clauses = [];
if ($search !== '')  $clauses[] = "k.nama LIKE '%".mysqli_real_escape_string($conn,$search)."%'";
if ($bulan)         $clauses[] = "g.bulan = $bulan";
if ($tahun)         $clauses[] = "g.tahun = $tahun";

// 4) Query data
$sql = "SELECT k.id, k.nama, g.bulan, g.tahun,
               g.gaji_pokok, g.tunjangan, g.potongan, g.total_gaji
        FROM karyawan k
        JOIN gaji g ON k.id = g.karyawan_id";
if (count($clauses)) {
    $sql .= ' WHERE ' . implode(' AND ', $clauses);
}
$sql .= " ORDER BY {$sort_by} {$order}, k.nama ASC";
$res = mysqli_query($conn, $sql);

// 5) Create spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Laporan Gaji');

// 6) Sisipkan logo (logo.png di folder yang sama)
$drawing = new Drawing();
$drawing->setName('Logo Padang TV');
$drawing->setPath('Padang TV Logo.png');
$drawing->setCoordinates('A1');
$drawing->setHeight(60);
$drawing->setWorksheet($sheet);

// 7) Tulis judul dan periode
$titleRow = 4;
$sheet->mergeCells("A{$titleRow}:H{$titleRow}");
$sheet->setCellValue("A{$titleRow}", 'Laporan Penggajian Padang TV');
$sheet->getStyle("A{$titleRow}")
      ->getFont()->setBold(true)->setSize(16);
$sheet->getStyle("A{$titleRow}")
      ->getAlignment()->setHorizontal('center');

$periodRow = 5;
$periodText = '';
if ($bulan && $tahun) {
    $periodText = "Periode: {$bulanList[$bulan]} {$tahun}";
} elseif ($tahun) {
    $periodText = "Tahun: {$tahun}";
} elseif ($bulan) {
    $periodText = "Bulan: {$bulanList[$bulan]}";
}
$sheet->mergeCells("A{$periodRow}:H{$periodRow}");
$sheet->setCellValue("A{$periodRow}", $periodText);
$sheet->getStyle("A{$periodRow}")
      ->getFont()->setSize(12);
$sheet->getStyle("A{$periodRow}")
      ->getAlignment()->setHorizontal('center');

// 8) Header tabel mulai row 7
$headerRow = 7;
$headers = [
    'A'=>'ID','B'=>'Nama','C'=>'Bulan','D'=>'Tahun',
    'E'=>'Gaji Pokok','F'=>'Tunjangan','G'=>'Potongan','H'=>'Total'
];
foreach ($headers as $col => $text) {
    $sheet->setCellValue("{$col}{$headerRow}", $text);
    $sheet->getStyle("{$col}{$headerRow}")
          ->getFont()->setBold(true);
    $sheet->getStyle("{$col}{$headerRow}")
          ->getAlignment()->setHorizontal('center');
    $sheet->getStyle("{$col}{$headerRow}")
          ->getBorders()->getAllBorders()
          ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
}

// Set column widths
$sheet->getColumnDimension('A')->setWidth(5);
$sheet->getColumnDimension('B')->setWidth(30);
$sheet->getColumnDimension('C')->setWidth(15);
$sheet->getColumnDimension('D')->setWidth(10);
$sheet->getColumnDimension('E')->setWidth(18);
$sheet->getColumnDimension('F')->setWidth(18);
$sheet->getColumnDimension('G')->setWidth(15);
$sheet->getColumnDimension('H')->setWidth(20);

// 9) Isi data mulai row 8
$rowNum = $headerRow + 1;
while ($r = mysqli_fetch_assoc($res)) {
    $sheet->setCellValue("A{$rowNum}", $r['id']);
    $sheet->setCellValue("B{$rowNum}", $r['nama']);
    $sheet->setCellValue("C{$rowNum}", $bulanList[$r['bulan']] ?? $r['bulan']);
    $sheet->setCellValue("D{$rowNum}", $r['tahun']);
    $sheet->setCellValue("E{$rowNum}", number_format($r['gaji_pokok'],0,',','.'));
    $sheet->setCellValue("F{$rowNum}", number_format($r['tunjangan'],0,',','.'));
    $sheet->setCellValue("G{$rowNum}", number_format($r['potongan'],0,',','.'));
    $sheet->setCellValue("H{$rowNum}", number_format($r['total_gaji'],0,',','.'));
    // border & alignment
    foreach (['A','B','C','D','E','F','G','H'] as $col) {
        $sheet->getStyle("{$col}{$rowNum}")
              ->getBorders()->getAllBorders()
              ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        if (in_array($col, ['E','F','G','H'])) {
            $sheet->getStyle("{$col}{$rowNum}")
                  ->getAlignment()->setHorizontal('right');
        } else {
            $sheet->getStyle("{$col}{$rowNum}")
                  ->getAlignment()->setHorizontal('center');
        }
    }
    $rowNum++;
}

// 10) Output file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="laporan_gaji.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
