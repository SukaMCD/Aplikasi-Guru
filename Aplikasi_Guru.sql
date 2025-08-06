-- Tabel users
CREATE TABLE users (
  id_user SERIAL PRIMARY KEY,
  username VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  level VARCHAR(20) NOT NULL CHECK (level IN ('admin', 'guru', 'murid'))
);

-- Tabel guru
CREATE TABLE guru (
  id_guru SERIAL PRIMARY KEY,
  nama_guru VARCHAR(255) NOT NULL,
  nip VARCHAR(100) NOT NULL
);

-- Tabel kelas
CREATE TABLE kelas (
  id_kelas SERIAL PRIMARY KEY,
  nama_kelas VARCHAR(100) NOT NULL,
  jurusan VARCHAR(100),
  tingkat INTEGER
);

-- Tabel jenis_kegiatan
CREATE TABLE jenis_kegiatan (
  id_jenis_kegiatan SERIAL PRIMARY KEY,
  nama_kegiatan VARCHAR(255) NOT NULL
);

-- Tabel kegiatan
CREATE TABLE kegiatan (
  id_kegiatan SERIAL PRIMARY KEY,
  id_guru INTEGER NOT NULL,
  id_kelas INTEGER NOT NULL,
  id_jenis_kegiatan INTEGER NOT NULL,
  tanggal DATE NOT NULL,
  laporan TEXT,
  FOREIGN KEY (id_guru) REFERENCES guru(id_guru),
  FOREIGN KEY (id_kelas) REFERENCES kelas(id_kelas),
  FOREIGN KEY (id_jenis_kegiatan) REFERENCES jenis_kegiatan(id_jenis_kegiatan)
);
-- :3