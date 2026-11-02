<?php
include 'config.php';

// Handle search
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Handle sort
$sort_by = 'id';
$order   = 'ASC';
if (isset($_GET['sort_by']) && in_array($_GET['sort_by'], ['id','nama'])) {
    $sort_by = $_GET['sort_by'];
}
if (isset($_GET['order']) && in_array($_GET['order'], ['ASC','DESC'])) {
    $order = $_GET['order'];
}

// Query utama: list karyawan untuk tabel
$sql = "SELECT * FROM karyawan";
if ($search !== '') {
    $sql .= " WHERE nama LIKE '%$search%'";
}
$sql .= " ORDER BY $sort_by $order";
$result = mysqli_query($conn, $sql);

// Query terpisah: daftar karyawan untuk dropdown export
$karyawanList = mysqli_query($conn, "SELECT id, nama FROM karyawan ORDER BY nama ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Penggajian - Padang TV</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="includes/style.css">
</head>
<body>
<?php include 'includes/sidebar.php'; ?>

<div class="container mt-4">
  <h1>Penggajian</h1>

  <!-- Search & Sort + Export Button -->
  <form method="GET" action="gaji.php" class="row g-3 mb-3 align-items-end">
    <div class="col-md-4">
      <label class="form-label">Cari Nama</label>
      <input type="text" name="search" class="form-control" placeholder="Cari nama karyawan..." value="<?= htmlspecialchars($search) ?>">
    </div>
    <div class="col-md-2">
      <label class="form-label">Sort by</label>
      <select name="sort_by" class="form-control">
        <option value="id"    <?= $sort_by=='id'   ? 'selected':'' ?>>ID</option>
        <option value="nama"  <?= $sort_by=='nama' ? 'selected':'' ?>>Nama</option>
      </select>
    </div>
    <div class="col-md-2">
      <label class="form-label">Order</label>
      <select name="order" class="form-control">
        <option value="ASC"  <?= $order=='ASC'  ? 'selected':'' ?>>Ascending</option>
        <option value="DESC" <?= $order=='DESC' ? 'selected':'' ?>>Descending</option>
      </select>
    </div>
    <div class="col-md-1">
      <button type="submit" class="btn btn-primary w-100"><i class="fa fa-search"></i></button>
    </div>
    <div class="col-md-3 text-end">
      <!-- Export Modal Trigger -->
      <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exportModal">
        <i class="fa fa-download"></i> Export
      </button>
    </div>
  </form>

  <!-- Export Modal -->
  <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exportModalLabel">Export Laporan Gaji</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="f_search" value="<?= urlencode($search) ?>">
          <input type="hidden" id="f_sort"  value="<?= $sort_by ?>">
          <input type="hidden" id="f_order" value="<?= $order ?>">

          <!-- Pilih Karyawan -->
          <div class="mb-3">
            <label class="form-label">Nama Karyawan</label>
            <select id="f_nama" class="form-control">
              <option value="">-- Semua Karyawan --</option>
              <?php while ($k = mysqli_fetch_assoc($karyawanList)): ?>
                <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama']) ?></option>
              <?php endwhile; ?>
            </select>
          </div>

          <!-- Pilihan Bulan dan Tahun -->
          <div class="mb-3">
            <label class="form-label">Bulan</label>
            <select id="f_bulan" class="form-control">
              <option value="">-- Semua Bulan --</option>
              <?php
                $bulanList = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April',
                              '05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus',
                              '09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
                foreach ($bulanList as $k => $v) {
                  echo "<option value=\"$k\">$v</option>";
                }
              ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Tahun</label>
            <select id="f_tahun" class="form-control">
              <option value="">-- Semua Tahun --</option>
              <?php for ($y = 2020; $y <= date('Y'); $y++): ?>
                <option value="<?= $y ?>"><?= $y ?></option>
              <?php endfor; ?>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <a href="#" id="btnPdf"   class="btn btn-danger"><i class="fa fa-file-pdf"></i> PDF</a>
          <a href="#" id="btnExcel" class="btn btn-success"><i class="fa fa-file-excel"></i> Excel</a>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Table karyawan -->
  <table class="table table-bordered table-striped">
    <thead class="table-light">
      <tr><th>ID</th><th>Nama Karyawan</th><th>Aksi</th></tr>
    </thead>
    <tbody>
      <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['nama']) ?></td>
            <td>
              <a href="riwayat_gaji.php?karyawan_id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">
                <i class="fa fa-money-bill"></i> Lihat Riwayat
              </a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="3" class="text-center">Tidak ada karyawan ditemukan.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include 'includes/footer.php'; ?>

<script>
// Update Export Links when modal opens or filter changes
function updateLinks() {
  let s  = document.getElementById('f_search').value;
  let so = document.getElementById('f_sort').value;
  let o  = document.getElementById('f_order').value;
  let n  = document.getElementById('f_nama').value;
  let b  = document.getElementById('f_bulan').value;
  let t  = document.getElementById('f_tahun').value;
  let basePdf   = `gaji_export_pdf.php?search=${s}&sort_by=${so}&order=${o}`;
  let baseExcel = `gaji_export_excel.php?search=${s}&sort_by=${so}&order=${o}`;
  if (n) basePdf += `&karyawan_id=${n}`, baseExcel += `&karyawan_id=${n}`;
  if (b) basePdf += `&bulan=${b}`, baseExcel += `&bulan=${b}`;
  if (t) basePdf += `&tahun=${t}`, baseExcel += `&tahun=${t}`;
  document.getElementById('btnPdf').href   = basePdf;
  document.getElementById('btnExcel').href = baseExcel;
}
// Attach listeners
['f_nama','f_bulan','f_tahun'].forEach(id => {
  document.getElementById(id).addEventListener('change', updateLinks);
});
// Also when modal shown
let exportModal = document.getElementById('exportModal');
exportModal.addEventListener('show.bs.modal', updateLinks);
</script>

</body>
</html>
