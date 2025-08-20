# Validasi Konflik Waktu Kegiatan

## Deskripsi Fitur

Fitur ini mencegah pembuatan dan update kegiatan yang bertabrakan dengan kegiatan yang sudah ada. Sistem akan memvalidasi:

1. **Konflik Kelas dan Tanggal**: Tidak dapat membuat kegiatan di kelas yang sama pada tanggal dan waktu yang sama
2. **Konflik Guru**: Guru tidak dapat mengajar di lebih dari 1 kelas pada waktu yang bersamaan
3. **Konflik Waktu**: Tidak dapat membuat kegiatan yang waktunya bertabrakan dengan kegiatan yang sudah ada
4. **Validasi Real-time**: Peringatan muncul secara otomatis saat user mengisi form
5. **Validasi Update**: Mencegah update yang menyebabkan konflik waktu

## Cara Kerja

### 1. Validasi Server-side (Backend)
- File: `app/controllers/proses_tambah_kegiatan.php` - Validasi saat menambah kegiatan
- File: `app/controllers/proses_update_kegiatan.php` - Validasi saat update kegiatan
- Query untuk memeriksa konflik waktu kelas dan guru

### 2. Validasi Client-side (Frontend)
- File: `app/controllers/proses_cek_waktu.php` - AJAX endpoint untuk pengecekan real-time
- Peringatan otomatis muncul di form
- Validasi sebelum submit

### 3. Logika Konflik Waktu
Sistem mendeteksi konflik jika:
- **Overlap Awal**: Waktu mulai baru berada di tengah kegiatan yang sudah ada
- **Overlap Akhir**: Waktu selesai baru berada di tengah kegiatan yang sudah ada  
- **Enveloped**: Kegiatan baru sepenuhnya berada di dalam kegiatan yang sudah ada

## Aturan Validasi

### ✅ DAPAT Membuat/Update Kegiatan:
- Kelas berbeda, tanggal sama, waktu sama
- Kelas sama, tanggal berbeda, waktu sama
- Kelas sama, tanggal sama, waktu berbeda (tidak overlap)
- Guru berbeda, kelas berbeda, tanggal sama, waktu sama

### ❌ TIDAK DAPAT Membuat/Update Kegiatan:
- Kelas sama, tanggal sama, waktu overlap
- Guru sama, tanggal sama, waktu overlap (meskipun kelas berbeda)

## Contoh Skenario

### Skenario 1: Konflik Waktu di Kelas
```
Kegiatan yang sudah ada: Kelas X IPA, 15/08/2025, 08:00-10:00
Kegiatan baru: Kelas X IPA, 15/08/2025, 09:00-11:00
Status: ❌ DITOLAK (overlap waktu di kelas yang sama)
```

### Skenario 2: Konflik Guru
```
Kegiatan yang sudah ada: Guru A di Kelas X IPA, 15/08/2025, 08:00-10:00
Kegiatan baru: Guru A di Kelas X IPS, 15/08/2025, 08:00-10:00
Status: ❌ DITOLAK (guru sama, waktu sama, kelas berbeda)
```

### Skenario 3: Tidak Konflik
```
Kegiatan yang sudah ada: Kelas X IPA, 15/08/2025, 08:00-10:00
Kegiatan baru: Kelas X IPA, 15/08/2025, 10:00-12:00
Status: ✅ DITERIMA (tidak overlap)
```

### Skenario 4: Guru Berbeda
```
Kegiatan yang sudah ada: Guru A di Kelas X IPA, 15/08/2025, 08:00-10:00
Kegiatan baru: Guru B di Kelas X IPS, 15/08/2025, 08:00-10:00
Status: ✅ DITERIMA (guru berbeda)
```

## File yang Dimodifikasi

1. **`proses_tambah_kegiatan.php`** - Validasi server-side untuk tambah kegiatan
2. **`proses_update_kegiatan.php`** - Validasi server-side untuk update kegiatan
3. **`tambah_kegiatan.php`** - Form dengan validasi real-time
4. **`proses_cek_waktu.php`** - Endpoint AJAX untuk pengecekan konflik

## Pesan Error

### Error Konflik Waktu di Kelas
```
Konflik Waktu di Kelas!
Sudah ada kegiatan [Nama Kegiatan] oleh [Nama Guru] di [Kelas] pada jam [Waktu].
Silakan pilih kelas lain, tanggal lain, atau waktu yang berbeda.
```

### Error Konflik Guru
```
Konflik Waktu Guru!
Guru [Nama Guru] sudah mengajar [Nama Kegiatan] di [Kelas] pada jam [Waktu].
Guru tidak dapat mengajar di lebih dari 1 kelas pada waktu yang bersamaan. 
Silakan pilih guru lain, tanggal lain, atau waktu yang berbeda.
```

### Error Validasi Lainnya
- Semua field harus diisi
- Format waktu tidak valid
- Jam selesai harus lebih besar dari jam mulai

## Implementasi Teknis

### Database Query untuk Konflik Kelas
```sql
SELECT k.id_kegiatan, k.jam_mulai, k.jam_selesai, 
       g.nama_guru, jk.nama_kegiatan, kl.tingkat, kl.jurusan
FROM kegiatan k
JOIN guru g ON k.id_guru = g.id_guru
JOIN jenis_kegiatan jk ON k.id_jenis_kegiatan = jk.id_jenis_kegiatan
JOIN kelas kl ON k.id_kelas = kl.id_kelas
WHERE k.id_kelas = $1 
AND k.tanggal = $2
AND (
    (k.jam_mulai <= $3::time AND k.jam_selesai > $3::time) OR
    (k.jam_mulai < $4::time AND k.jam_selesai >= $4::time) OR
    (k.jam_mulai >= $3::time AND k.jam_selesai <= $4::time)
)
```

### Database Query untuk Konflik Guru
```sql
SELECT k.id_kegiatan, k.jam_mulai, k.jam_selesai, 
       g.nama_guru, jk.nama_kegiatan, kl.tingkat, kl.jurusan
FROM kegiatan k
JOIN guru g ON k.id_guru = g.id_guru
JOIN jenis_kegiatan jk ON k.id_jenis_kegiatan = jk.id_jenis_kegiatan
JOIN kelas kl ON k.id_kelas = kl.id_kelas
WHERE k.id_guru = $1 
AND k.tanggal = $2
AND k.id_kelas != $3
AND (
    (k.jam_mulai <= $4::time AND k.jam_selesai > $4::time) OR
    (k.jam_mulai < $5::time AND k.jam_selesai >= $5::time) OR
    (k.jam_mulai >= $4::time AND k.jam_selesai <= $5::time)
)
```

### JavaScript Event Handling
```javascript
// Check conflict when any relevant field changes
document.getElementById('tanggal').addEventListener('change', checkTimeConflict);
document.getElementById('jam_mulai').addEventListener('change', checkTimeConflict);
document.getElementById('jam_selesai').addEventListener('change', checkTimeConflict);
```

## Keuntungan Fitur

1. **Mencegah Double Booking**: Tidak ada kegiatan yang bertabrakan di kelas yang sama
2. **Mencegah Overload Guru**: Guru tidak dapat mengajar di lebih dari 1 kelas bersamaan
3. **User Experience**: Peringatan real-time tanpa perlu submit form
4. **Data Integrity**: Konsistensi data kegiatan di database
5. **Fleksibilitas**: Tetap bisa membuat kegiatan di waktu/kelas yang berbeda
6. **Validasi Update**: Mencegah update yang menyebabkan konflik

## Testing

### Test Case 1: Konflik Kelas
1. Buat kegiatan pertama di kelas tertentu dengan tanggal dan waktu tertentu
2. Coba buat kegiatan kedua di kelas yang sama, tanggal yang sama, dengan waktu yang overlap
3. Sistem harus menampilkan pesan error konflik waktu di kelas

### Test Case 2: Konflik Guru
1. Buat kegiatan pertama dengan guru tertentu di kelas tertentu
2. Coba buat kegiatan kedua dengan guru yang sama di kelas berbeda, tanggal yang sama, waktu yang sama
3. Sistem harus menampilkan pesan error konflik guru

### Test Case 3: Update Konflik
1. Buat kegiatan dengan waktu tertentu
2. Coba update waktu kegiatan tersebut ke waktu yang konflik dengan kegiatan lain
3. Sistem harus menampilkan pesan error dan mencegah update

### Test Case 4: Tidak Konflik
1. Buat kegiatan di kelas tertentu dengan waktu tertentu
2. Buat kegiatan di kelas berbeda dengan waktu yang sama - harus berhasil
3. Buat kegiatan di kelas yang sama dengan waktu yang berbeda - harus berhasil

## Perubahan dari Versi Sebelumnya

1. **Validasi Guru**: Ditambahkan validasi bahwa guru tidak bisa mengajar di lebih dari 1 kelas bersamaan
2. **Validasi Update**: Ditambahkan validasi konflik waktu saat update kegiatan
3. **Pesan Error**: Diperbaiki pesan error untuk lebih informatif
4. **Real-time Validation**: Diperbaiki validasi real-time untuk mencakup konflik guru

