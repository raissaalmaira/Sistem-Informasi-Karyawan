<?php
include 'config.php';

$karyawan_id = intval($_GET['karyawan_id'] ?? 0);
if (!$karyawan_id) {
    header("Location: gaji.php");
    exit;
}

// Ambil nama karyawan
$qk = mysqli_query($conn, "SELECT nama FROM karyawan WHERE id = $karyawan_id");
$karyawan = mysqli_fetch_assoc($qk) ?: ['nama'=>'-'];

// Ambil riwayat gaji
$rq = "SELECT * FROM gaji 
       WHERE karyawan_id = $karyawan_id 
       ORDER BY tahun DESC, bulan DESC";
$resultGaji = mysqli_query($conn, $rq);

// Bulan array
$bulanArr = [
    1=>"Januari",2=>"Februari",3=>"Maret",4=>"April",
    5=>"Mei",6=>"Juni",7=>"Juli",8=>"Agustus",
    9=>"September",10=>"Oktober",11=>"November",12=>"Desember"
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Riwayat Gaji – <?= htmlspecialchars($karyawan['nama']) ?></title>
  <!-- Bootstrap & FontAwesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="includes/style.css">
  <style>
    /* Hilangkan garis atas footer modal */
    .modal-footer { border-top: none !important; }
  </style>
</head>
<body>
<?php include 'includes/sidebar.php'; ?>

<div class="container mt-4">
  <h1>Riwayat Gaji – <?= htmlspecialchars($karyawan['nama']) ?></h1>

  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <a href="gaji.php" class="btn btn-secondary">
        <i class="fa fa-arrow-left"></i> Kembali
      </a>
      <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#tambahGajiModal">
        <i class="fa fa-plus"></i> Tambah Gaji
      </button>
    </div>
    
  </div>

  <input type="text" id="searchInput" class="form-control mb-3" placeholder="Cari Bulan/Tahun...">

  <table class="table table-bordered" id="gajiTable">
    <thead class="table-light">
      <tr>
        <th class="sortable">Bulan</th>
        <th class="sortable">Tahun</th>
        <th class="sortable">Gaji Pokok</th>
        <th class="sortable">Tunjangan</th>
        <th class="sortable">Potongan</th>
        <th class="sortable">Total Gaji</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
    <?php while($r = mysqli_fetch_assoc($resultGaji)): ?>
      <tr>
        <td><?= $bulanArr[$r['bulan']] ?></td>
        <td><?= $r['tahun'] ?></td>
        <td><?= number_format($r['gaji_pokok'],2,',','.') ?></td>
        <td><?= number_format($r['tunjangan'],2,',','.') ?></td>
        <td><?= number_format($r['potongan'],2,',','.') ?></td>
        <td><strong><?= number_format($r['total_gaji'],2,',','.') ?></strong></td>
        <td>
          <button class="btn btn-warning btn-sm"
                  data-bs-toggle="modal" data-bs-target="#editGajiModal"
                  data-id="<?= $r['id'] ?>"
                  data-bulan="<?= $r['bulan'] ?>"
                  data-tahun="<?= $r['tahun'] ?>"
                  data-gp="<?= $r['gaji_pokok'] ?>"
                  data-tj="<?= $r['tunjangan'] ?>"
                  data-pt="<?= $r['potongan'] ?>">
                  <i class="fas fa-edit"></i>
          </button>
          <button class="btn btn-danger btn-sm"
                  data-bs-toggle="modal" data-bs-target="#hapusGajiModal"
                  data-id="<?= $r['id'] ?>">
                  <i class="fas fa-trash"></i>
          </button>
          <a href="print_slip.php?id=<?= $r['id'] ?>" class="btn btn-primary btn-sm" target="_blank">
            <i class="fa fa-print"></i> 
          </a>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>

<!-- Modal Tambah Gaji -->
<div class="modal fade" id="tambahGajiModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="tambah_gaji.php" method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Gaji</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="karyawan_id" value="<?= $karyawan_id ?>">
        <div class="mb-3">
          <label class="form-label">Bulan</label>
          <select name="bulan" class="form-select" required>
            <?php foreach($bulanArr as $k=>$b): ?>
              <option value="<?= $k ?>"><?= $b ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Tahun</label>
          <input type="number" name="tahun" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Gaji Pokok</label>
          <input type="number" name="gaji_pokok" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Tunjangan</label>
          <input type="number" name="tunjangan" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Potongan</label>
          <input type="number" name="potongan" class="form-control" required>
        </div>
      </div>
      <div class="modal-footer d-flex justify-content-end">
        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Gaji -->
<div class="modal fade" id="editGajiModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="edit_gaji.php" method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Gaji</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" id="editId">
        <input type="hidden" name="karyawan_id" value="<?= $karyawan_id ?>">
        <div class="mb-3">
          <label class="form-label">Bulan</label>
          <select name="bulan" id="editBulan" class="form-select" required>
            <?php foreach($bulanArr as $k=>$b): ?>
              <option value="<?= $k ?>"><?= $b ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Tahun</label>
          <input type="number" name="tahun" id="editTahun" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Gaji Pokok</label>
          <input type="number" name="gaji_pokok" id="editGajiPokok" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Tunjangan</label>
          <input type="number" name="tunjangan" id="editTunjangan" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Potongan</label>
          <input type="number" name="potongan" id="editPotongan" class="form-control" required>
        </div>
      </div>
      <div class="modal-footer d-flex justify-content-end">
        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Hapus Gaji -->
<div class="modal fade" id="hapusGajiModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="hapus_gaji.php" method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Hapus Gaji</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Apakah Anda yakin ingin menghapus data gaji ini?</p>
        <input type="hidden" name="id" id="hapusId">
      </div>
      <div class="modal-footer d-flex justify-content-end">
        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-danger">Hapus</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Search live
  document.getElementById('searchInput').addEventListener('keyup', function(){
    const kw = this.value.toLowerCase();
    document.querySelectorAll('#gajiTable tbody tr').forEach(tr=>{
      tr.style.display = tr.innerText.toLowerCase().includes(kw)?'':'none';
    });
  });

  // Sortable columns
  document.querySelectorAll('#gajiTable thead th.sortable').forEach((th, idx)=>{
    let asc = true;
    th.style.cursor = 'pointer';
    th.addEventListener('click', ()=>{
      const tbody = th.closest('table').querySelector('tbody');
      Array.from(tbody.querySelectorAll('tr'))
        .sort((a,b)=>{
          let va = a.children[idx].innerText.replace(/[Rp\.\, ]/g,''), 
              vb = b.children[idx].innerText.replace(/[Rp\.\, ]/g,'');
          if (!isNaN(va) && !isNaN(vb)) return asc ? va - vb : vb - va;
          return asc 
            ? a.children[idx].innerText.localeCompare(b.children[idx].innerText)
            : b.children[idx].innerText.localeCompare(a.children[idx].innerText);
        })
        .forEach(tr=>tbody.appendChild(tr));
      asc = !asc;
    });
  });

  // Bind data to modals
  const editModal = document.getElementById('editGajiModal');
  editModal?.addEventListener('show.bs.modal', e=>{
    const b = e.relatedTarget;
    document.getElementById('editId').value        = b.dataset.id;
    document.getElementById('editBulan').value     = b.dataset.bulan;
    document.getElementById('editTahun').value     = b.dataset.tahun;
    document.getElementById('editGajiPokok').value = b.dataset.gp;
    document.getElementById('editTunjangan').value = b.dataset.tj;
    document.getElementById('editPotongan').value  = b.dataset.pt;
  });
  const delModal = document.getElementById('hapusGajiModal');
  delModal?.addEventListener('show.bs.modal', e=>{
    delModal.querySelector('#hapusId').value = e.relatedTarget.dataset.id;
  });
</script>
</body>
</html>
