# Validasi Konflik Waktu Kegiatan

## Deskripsi Fitur

Fitur ini mencegah pembuatan kegiatan yang bertabrakan dengan kegiatan yang sudah ada di kelas dan waktu yang sama. Sistem akan memvalidasi:

1. **Konflik Kelas dan Tanggal**: Tidak dapat membuat kegiatan di kelas yang sama pada tanggal dan waktu yang sama
2. **Konflik Guru**: Guru tidak dapat mengajar di lebih dari 1 kelas pada waktu yang bersamaan
3. **Konflik Waktu**: Tidak dapat membuat kegiatan yang waktunya bertabrakan dengan kegiatan yang sudah ada
4. **Validasi Real-time**: Peringatan muncul secara otomatis saat user mengisi form
5. **Validasi Update**: Mencegah update yang menyebabkan konflik waktu

## Cara Kerja

### 1. Validasi Server-side (Backend)
- File: `app/controllers/proses_tambah_kegiatan.php` - Validasi saat menambah kegiatan
- File: `app/controllers/proses_update_kegiatan.php` - Validasi saat update kegiatan
- Query untuk memeriksa konflik waktu kelas dan guru:
```sql
SELECT k.id_kegiatan, k.jam_mulai, k.jam_selesai
FROM kegiatan k
WHERE k.id_kelas = $1 
AND k.tanggal = $2
AND (
    (k.jam_mulai <= $3::time AND k.jam_selesai > $3::time) OR
    (k.jam_mulai < $4::time AND k.jam_selesai >= $4::time) OR
    (k.jam_mulai >= $3::time AND k.jam_selesai <= $4::time)
)
```

### 2. Validasi Client-side (Frontend)
- File: `app/controllers/proses_cek_waktu.php`
- AJAX request untuk pengecekan real-time
- Peringatan otomatis muncul di form

### 3. Logika Konflik Waktu
Sistem mendeteksi konflik jika:
- **Overlap Awal**: Waktu mulai baru berada di tengah kegiatan yang sudah ada
- **Overlap Akhir**: Waktu selesai baru berada di tengah kegiatan yang sudah ada  
- **Enveloped**: Kegiatan baru sepenuhnya berada di dalam kegiatan yang sudah ada

## Aturan Validasi

### ✅ DAPAT Membuat Kegiatan:
- Kelas berbeda, tanggal sama, waktu sama
- Kelas sama, tanggal berbeda, waktu sama
- Kelas sama, tanggal sama, waktu berbeda (tidak overlap)

### ❌ TIDAK DAPAT Membuat Kegiatan:
- Kelas sama, tanggal sama, waktu overlap

## Contoh Skenario

### Skenario 1: Konflik Waktu
```
Kegiatan yang sudah ada: Kelas X IPA, 15/08/2025, 08:00-10:00
Kegiatan baru: Kelas X IPA, 15/08/2025, 09:00-11:00
Status: ❌ DITOLAK (overlap waktu)
```

### Skenario 2: Tidak Konflik
```
Kegiatan yang sudah ada: Kelas X IPA, 15/08/2025, 08:00-10:00
Kegiatan baru: Kelas X IPA, 15/08/2025, 10:00-12:00
Status: ✅ DITERIMA (tidak overlap)
```

### Skenario 3: Kelas Berbeda
```
Kegiatan yang sudah ada: Kelas X IPA, 15/08/2025, 08:00-10:00
Kegiatan baru: Kelas X IPS, 15/08/2025, 08:00-10:00
Status: ✅ DITERIMA (kelas berbeda)
```

### Skenario 4: Konflik Guru
```
Kegiatan yang sudah ada: Guru A di Kelas X IPA, 15/08/2025, 08:00-10:00
Kegiatan baru: Guru A di Kelas X IPS, 15/08/2025, 08:00-10:00
Status: ❌ DITOLAK (guru sama, waktu sama, kelas berbeda)
```

## File yang Dimodifikasi

1. **`proses_tambah_kegiatan.php`** - Validasi server-side untuk tambah kegiatan
2. **`proses_update_kegiatan.php`** - Validasi server-side untuk update kegiatan (termasuk guru, jenis, kelas)
3. **`tambah_kegiatan.php`** - Form dengan validasi real-time dan auto-save
4. **`proses_cek_waktu.php`** - Endpoint AJAX untuk pengecekan konflik
5. **`kegiatan.php`** - Dashboard dengan modal edit yang lengkap

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

### Database Query
Menggunakan PostgreSQL dengan parameter binding untuk keamanan:
```php
$conflict_query = "
    SELECT k.id_kegiatan, k.jam_mulai, k.jam_selesai
    FROM kegiatan k
    WHERE k.id_kelas = $1 
    AND k.tanggal = $2
    AND (
        (k.jam_mulai <= $3::time AND k.jam_selesai > $3::time) OR
        (k.jam_mulai < $4::time AND k.jam_selesai >= $4::time) OR
        (k.jam_mulai >= $3::time AND k.jam_selesai <= $4::time)
    )
";
```

### JavaScript Event Handling
```javascript
// Check conflict when any relevant field changes
document.getElementById('tanggal').addEventListener('change', checkTimeConflict);
document.getElementById('jam_mulai').addEventListener('change', checkTimeConflict);
document.getElementById('jam_selesai').addEventListener('change', checkTimeConflict);
```

## Keuntungan Fitur

1. **Mencegah Double Booking**: Tidak ada kegiatan yang bertabrakan
2. **User Experience**: Peringatan real-time tanpa perlu submit form
3. **Data Integrity**: Konsistensi data kegiatan di database
4. **Fleksibilitas**: Tetap bisa membuat kegiatan di waktu/kelas yang berbeda
5. **Form Persistence**: Data form tidak hilang saat reload halaman
6. **Modal Edit Lengkap**: Bisa update semua field kegiatan termasuk guru, jenis, dan kelas
7. **Urutan Dashboard**: Kegiatan diurutkan berdasarkan tanggal (dari yang terlama)

## Testing

Untuk menguji fitur ini:
1. Buat kegiatan pertama di kelas tertentu dengan tanggal dan waktu tertentu
2. Coba buat kegiatan kedua di kelas yang sama, tanggal yang sama, dengan waktu yang overlap
3. Sistem harus menampilkan pesan error konflik waktu
4. Coba buat kegiatan di kelas berbeda atau tanggal berbeda - harus berhasil
