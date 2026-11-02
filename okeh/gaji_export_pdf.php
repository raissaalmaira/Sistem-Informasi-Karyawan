<?php
require('vendor/setasign/fpdf/fpdf.php');
include 'config.php';

// 1) Mapping bulan → nama
$bulanList = [
    1  => 'Januari',  2  => 'Februari', 3  => 'Maret',
    4  => 'April',    5  => 'Mei',      6  => 'Juni',
    7  => 'Juli',     8  => 'Agustus',  9  => 'September',
    10 => 'Oktober', 11 => 'November', 12 => 'Desember',
];

// 2) Ambil & sanitize parameter
$search  = $_GET['search']   ?? '';
$sort_by = in_array($_GET['sort_by'] ?? '', ['id','nama']) ? $_GET['sort_by'] : 'tahun';
$order   = in_array($_GET['order']   ?? '', ['ASC','DESC']) ? $_GET['order'] : 'DESC';
$bulan   = isset($_GET['bulan'])  && $_GET['bulan']  !== '' ? intval($_GET['bulan']) : null;
$tahun   = isset($_GET['tahun'])  && $_GET['tahun']  !== '' ? intval($_GET['tahun']) : null;

// 3) Build WHERE
$clauses = [];
if ($search!=='')  $clauses[] = "k.nama LIKE '%".mysqli_real_escape_string($conn,$search)."%'";
if ($bulan)       $clauses[] = "g.bulan = $bulan";
if ($tahun)       $clauses[] = "g.tahun = $tahun";

// 4) Query
$sql = "SELECT k.id, k.nama, g.bulan, g.tahun,
               g.gaji_pokok, g.tunjangan, g.potongan, g.total_gaji
        FROM karyawan k
        JOIN gaji g ON k.id = g.karyawan_id";
if (count($clauses)) {
    $sql .= " WHERE ".implode(' AND ',$clauses);
}
$sql .= " ORDER BY {$sort_by} {$order}, k.nama ASC";
$res = mysqli_query($conn, $sql);

// 5) Inisialisasi PDF (portrait A4, margin 20 mm)
$pdf = new FPDF('P','mm','A4');
$pdf->SetMargins(20,20,20);
$pdf->AddPage();

// 6) Logo
// -- Pastikan file logo.png ada di direktori yang sama, atau sesuaikan path-nya:
$pdf->Image('Padang TV logo.png', 20, 20, 25);  // x=20mm, y=20mm, width=25mm
$pdf->Ln(15); // beri spasi setelah logo

// 7) Judul
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Laporan Penggajian Padang TV',0,1,'C');
$pdf->SetFont('Arial','',12);
if ($bulan && $tahun) {
    $pdf->Cell(0,8,"Periode: {$bulanList[$bulan]} {$tahun}",0,1,'C');
} elseif ($tahun) {
    $pdf->Cell(0,8,"Tahun: {$tahun}",0,1,'C');
} elseif ($bulan) {
    $pdf->Cell(0,8,"Bulan: {$bulanList[$bulan]}",0,1,'C');
}
$pdf->Ln(5);

// 8) Header tabel
$pdf->SetFont('Arial','B',10);
$w = [10,40,20,15,20,20,20,25];  // total ≤ 170mm
$h = ['ID','Nama','Bulan','Tahun','Gaji Pokok','Tunjangan','Potongan','Total'];
foreach ($h as $i => $label) {
    $pdf->Cell($w[$i],8,$label,1,0,'C');
}
$pdf->Ln();

// 9) Isi tabel
$pdf->SetFont('Arial','',10);
while ($r = mysqli_fetch_assoc($res)) {
    $pdf->Cell($w[0],8,$r['id'],1,0,'C');
    $pdf->Cell($w[1],8,$r['nama'],1,0,'L');
    $pdf->Cell($w[2],8,$bulanList[$r['bulan']] ?? $r['bulan'],1,0,'C');
    $pdf->Cell($w[3],8,$r['tahun'],1,0,'C');
    $pdf->Cell($w[4],8,number_format($r['gaji_pokok'],0,',','.'),1,0,'R');
    $pdf->Cell($w[5],8,number_format($r['tunjangan'],0,',','.'),1,0,'R');
    $pdf->Cell($w[6],8,number_format($r['potongan'],0,',','.'),1,0,'R');
    $pdf->Cell($w[7],8,number_format($r['total_gaji'],0,',','.'),1,0,'R');
    $pdf->Ln();
}

// 10) Output PDF
$pdf->Output('D','laporan_gaji.pdf');
exit;
?>
