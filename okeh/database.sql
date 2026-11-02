USE padangtv_karyawan;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100),
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    role ENUM('admin', 'karyawan') NOT NULL
);

CREATE TABLE karyawan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100),
    tempat_lahir VARCHAR(50),
    tanggal_lahir DATE,
    alamat TEXT,
    jabatan VARCHAR(50),
    status_karyawan ENUM('aktif', 'tidak aktif'),
    foto VARCHAR(255),
    user_id INT UNIQUE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE gaji (
    id INT AUTO_INCREMENT PRIMARY KEY,
    karyawan_id INT,
    bulan VARCHAR(20),
    tahun INT,
    gaji_pokok DECIMAL(10,2),
    tunjangan DECIMAL(10,2),
    potongan DECIMAL(10,2),
    total_gaji DECIMAL(10,2) GENERATED ALWAYS AS (gaji_pokok + tunjangan - potongan) STORED,
    FOREIGN KEY (karyawan_id) REFERENCES karyawan(id) ON DELETE CASCADE
);
