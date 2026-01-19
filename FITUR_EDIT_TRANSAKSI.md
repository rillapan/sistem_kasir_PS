# Fitur Edit Transaksi - Dokumentasi

## Ringkasan
Fitur edit transaksi telah berhasil ditambahkan ke sistem rental PS. Fitur ini memungkinkan admin untuk mengedit transaksi yang belum dibayar (unpaid) dengan berbagai kemampuan.

## Fitur yang Diimplementasikan

### 1. **Edit Perangkat (Device)**
- ✅ Dapat mengubah perangkat dari TV1 ke TV2 (atau perangkat lainnya)
- ✅ Timer tetap berjalan dari waktu mulai awal (tidak reset ke nol)
- ✅ Perangkat lama otomatis dibebaskan (status: Tersedia)
- ✅ Perangkat baru otomatis ditandai sebagai Digunakan
- ✅ Validasi: hanya perangkat yang tersedia yang dapat dipilih

### 2. **Ubah Tipe Transaksi**
- ✅ Lost Time → Paket
- ✅ Lost Time → Custom Paket
- ✅ Paket → Lost Time
- ✅ Paket → Custom Paket
- ✅ Custom Paket → Lost Time
- ✅ Custom Paket → Paket

**Perilaku saat mengubah tipe:**
- **Ke Lost Time**: Timer mulai dari sekarang, harga dihitung saat selesai
- **Ke Paket**: Harga dihitung berdasarkan jam main yang dipilih
- **Ke Custom Paket**: Harga sesuai paket yang dipilih

### 3. **Ubah Jam Main (untuk tipe Paket)**
- ✅ Dapat mengubah dari 3 jam ke 1 jam (atau sebaliknya)
- ✅ Harga otomatis menyesuaikan dengan jam main baru
- ✅ Preview harga real-time saat memilih jam main
- ✅ Formula: Harga Total = (Harga per Jam × Jam Main) + Total FnB

### 4. **Keamanan & Validasi**
- ✅ Hanya transaksi dengan status "unpaid" yang dapat diedit
- ✅ Validasi perangkat tersedia sebelum dipindah
- ✅ Validasi kompatibilitas perangkat dengan custom paket
- ✅ Timer tetap berjalan untuk semua perubahan

## Cara Menggunakan

### Langkah-langkah:
1. Buka halaman Transaksi (http://127.0.0.1:8000/transaction)
2. Cari transaksi yang belum dibayar (status: Unpaid)
3. Klik tombol **"Edit"** (tombol kuning) di kolom Action
4. Lakukan perubahan yang diinginkan:
   - Pilih perangkat baru (jika ingin pindah perangkat)
   - Pilih tipe transaksi baru (jika ingin mengubah tipe)
   - Pilih jam main baru (jika tipe Paket)
5. Klik **"Simpan Perubahan"**
6. Sistem akan menampilkan pesan sukses dan kembali ke halaman transaksi

## Tombol di Halaman Transaksi

Untuk transaksi **Unpaid**, tersedia tombol:
- 🔵 **Detail** - Melihat detail transaksi
- 💰 **Bayar** - Proses pembayaran
- ⚠️ **Edit** - Edit transaksi (BARU!)
- ➕ **Tambah Pesanan** - Tambah FnB
- 🛑 **Selesai** - Akhiri transaksi (untuk Lost Time yang berjalan)

## Contoh Skenario

### Skenario 1: Pindah Perangkat
**Situasi**: Customer sedang main di TV1, tapi TV2 lebih bagus
- Klik Edit
- Pilih TV2 di dropdown Perangkat
- Simpan
- ✅ Customer pindah ke TV2, timer tetap jalan dari waktu mulai awal

### Skenario 2: Ubah dari Lost Time ke Paket
**Situasi**: Customer awalnya pilih Lost Time, tapi mau ganti ke Paket 2 jam
- Klik Edit
- Ubah Tipe Transaksi ke "Paket"
- Pilih Jam Main: 2 Jam
- Lihat preview harga otomatis
- Simpan
- ✅ Transaksi berubah ke Paket 2 jam dengan harga tetap

### Skenario 3: Ubah Jam Main
**Situasi**: Customer pesan Paket 3 jam, tapi cuma mau main 1 jam
- Klik Edit
- Ubah Jam Main dari 3 Jam ke 1 Jam
- Harga otomatis turun (misal dari Rp 30.000 ke Rp 10.000)
- Simpan
- ✅ Harga disesuaikan dengan jam main baru

## File yang Dimodifikasi

1. **app/Http/Controllers/TransactionController.php**
   - Method `edit()` - Load data untuk form edit
   - Method `update()` - Proses update transaksi

2. **resources/views/transaction/edit.blade.php**
   - Form edit lengkap dengan validasi
   - Preview harga real-time
   - Informasi FnB yang ada

3. **resources/views/transaction/index.blade.php**
   - Tambah tombol Edit di kolom Action

## Catatan Penting

⚠️ **Timer Tetap Berjalan**
- Saat mengubah perangkat atau tipe transaksi, timer TIDAK direset
- Timer tetap menggunakan waktu_mulai yang asli
- Ini memastikan customer tidak mendapat waktu gratis saat pindah

⚠️ **Hanya Unpaid**
- Transaksi yang sudah dibayar (Paid) TIDAK dapat diedit
- Ini untuk menjaga integritas data pembayaran

⚠️ **FnB Tidak Berubah**
- Edit transaksi tidak mengubah FnB yang sudah dipesan
- Untuk menambah FnB, gunakan tombol "Tambah Pesanan"

## Testing Checklist

- [ ] Edit perangkat dari TV1 ke TV2 - Timer tetap jalan
- [ ] Ubah Lost Time ke Paket - Harga menyesuaikan
- [ ] Ubah Paket ke Lost Time - Timer mulai dari sekarang
- [ ] Ubah jam main dari 3 ke 1 jam - Harga turun
- [ ] Coba edit transaksi yang sudah Paid - Harus ditolak
- [ ] Validasi perangkat tidak tersedia - Harus ditolak
- [ ] Custom Paket dengan perangkat tidak sesuai - Harus ditolak
