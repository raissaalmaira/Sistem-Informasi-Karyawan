<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Karyawan - Padang TV</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
            text-align: center;
        }
        .kartu {
            display: inline-block;
            padding: 20px 30px;
            border: 2px solid maroon;
            border-radius: 10px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 380px;
            text-align: center;
        }
        .kartu-header img {
            width: 80px;
            margin-bottom: 5px;
        }
        .kartu-header h2 {
            margin: 5px 0 10px;
            font-size: 18px;
            color: maroon;
        }
        .kartu-body {
        display: flex;
        align-items: center; /* Dari flex-start jadi center */
        justify-content: flex-start;
        margin-top: 10px;
        gap: 15px;
        text-align: left;
       }
        .foto-karyawan {
            width: 80px;
            height: 120px;
            object-fit: cover;
            border: 2px solid maroon;
            border-radius: 5px;
            flex-shrink: 0;
        }
        .info {
            font-size: 13px;
        }
        .info table {
            border-collapse: collapse;
        }
        .info td {
            padding: 2px 5px;
            vertical-align: top;
        }
        .info .label {
            font-weight: bold;
            text-align: left;
            white-space: nowrap;
        }
        .btn-cetak {
            margin-top: 20px;
            padding: 10px 25px;
            background-color: maroon;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
        }
        @media print {
            .btn-cetak {
                display: none;
            }
            body {
                background: white;
                margin: 0;
                padding: 0;
            }
            .kartu {
                box-shadow: none;
                border: 2px solid maroon;
            }
        }
    </style>
</head>
<body>

<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'karyawan') {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "ID tidak ditemukan.";
    exit();
}

$query = "SELECT * FROM karyawan WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo "Data karyawan tidak ditemukan.";
    exit();
}
?>

<div class="kartu">
    <div class="kartu-header">
        <img src="Padang TV logo.png" alt="Logo Padang TV">
        <h2>Kartu Karyawan Padang TV</h2>
    </div>
    <div class="kartu-body">
        <img src="uploads/<?= htmlspecialchars($data['foto']) ?>" alt="Foto Karyawan" class="foto-karyawan">
        <div class="info">
            <table>
                <tr>
                    <td class="label">ID Karyawan</td>
                    <td>:</td>
                    <td><?= htmlspecialchars($data['id']) ?></td>
                </tr>
                <tr>
                    <td class="label">Nama</td>
                    <td>:</td>
                    <td><?= htmlspecialchars($data['nama']) ?></td>
                </tr>
                <tr>
                    <td class="label">Jabatan</td>
                    <td>:</td>
                    <td><?= htmlspecialchars($data['jabatan']) ?></td>
                </tr>
                <tr>
                    <td class="label">Status</td>
                    <td>:</td>
                    <td><?= ucfirst(htmlspecialchars($data['status_karyawan'])) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>

<div style="margin-top: 20px;">
    <button onclick="window.print()" class="btn-cetak">Cetak Kartu</button>
</div>

</body>
</html>
