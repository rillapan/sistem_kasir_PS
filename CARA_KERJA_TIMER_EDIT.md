# Sistem Timer Dinamis - Dokumentasi Lengkap

## 🎯 Konsep Utama

Sistem timer dirancang agar **selalu mencerminkan waktu yang sebenarnya telah berlalu**, baik saat beralih antar tipe transaksi maupun mengubah durasi paket.

## ⏱️ Dua Jenis Timer

### 1. **Timer Maju (Count Up)** - Lost Time
- Dimulai dari 00:00:00
- Terus bertambah seiring waktu
- Contoh: 00:05:00, 00:10:00, 00:15:00...

### 2. **Timer Mundur (Countdown)** - Paket
- Dimulai dari durasi paket
- Berkurang seiring waktu
- Contoh: 01:00:00, 00:55:00, 00:50:00...

## 📊 Skenario Transisi

### Skenario 1: Lost Time → Paket (Timer Mundur)

**Contoh:**
```
10:00 - Mulai Lost Time
10:05 - Sudah main 5 menit (timer: 00:05:00)
10:05 - Ubah ke Paket 1 jam
       → Timer berubah ke: 00:55:00 (countdown)
       → Waktu selesai: 11:00 (10:00 + 1 jam)
```

**Perhitungan:**
```php
waktu_mulai = 10:00
elapsed = 5 menit (sudah terpakai)
jam_main = 1 jam = 60 menit
remaining = 60 - 5 = 55 menit
waktu_selesai = sekarang (10:05) + 55 menit = 11:00
```

**Hasil:**
- ✅ Timer menunjukkan sisa waktu: 55 menit
- ✅ Total durasi tetap 1 jam dari waktu mulai asli
- ✅ Customer tidak dapat waktu tambahan

### Skenario 2: Paket → Lost Time (Timer Maju)

**Contoh:**
```
10:00 - Mulai Paket 1 jam
10:05 - Sudah main 5 menit (timer: 00:55:00 countdown)
10:05 - Ubah ke Lost Time
       → Timer berubah ke: 00:05:00 (count up)
       → Melanjutkan dari waktu yang sudah terpakai
```

**Perhitungan:**
```php
waktu_mulai = 10:00
elapsed = 5 menit
lost_time_start = 10:00 (dari waktu_mulai asli)
Timer sekarang = 00:05:00 (melanjutkan)
```

**Hasil:**
- ✅ Timer melanjutkan dari 00:05:00 (bukan 00:00:00)
- ✅ Saat selesai, durasi dihitung dari 10:00
- ✅ Tidak ada waktu yang hilang

### Skenario 3: Ubah Jam Main Paket (Timer Menyesuaikan)

**Contoh:**
```
10:00 - Mulai Paket 3 jam
10:05 - Sudah main 5 menit (timer: 02:55:00)
10:05 - Ubah ke Paket 1 jam
       → Timer berubah ke: 00:55:00
       → Waktu selesai: 11:00 (bukan 13:00)
```

**Perhitungan:**
```php
waktu_mulai = 10:00
elapsed = 5 menit
jam_main_baru = 1 jam = 60 menit
remaining = 60 - 5 = 55 menit
waktu_selesai = 10:05 + 55 = 11:00
harga = 1 jam × harga_per_jam (bukan 3 jam)
```

**Hasil:**
- ✅ Timer menyesuaikan ke sisa waktu baru
- ✅ Harga berubah sesuai jam main baru
- ✅ Waktu yang sudah terpakai tetap dihitung

### Skenario 4: Ubah Jam Main (Waktu Sudah Lewat)

**Contoh:**
```
10:00 - Mulai Paket 3 jam
11:30 - Sudah main 90 menit (timer: 01:30:00)
11:30 - Ubah ke Paket 1 jam (60 menit)
       → Waktu sudah lewat! (90 > 60)
       → Timer: 00:00:00 (overtime)
       → Waktu selesai: 11:30 (sekarang)
```

**Perhitungan:**
```php
elapsed = 90 menit
jam_main_baru = 60 menit
remaining = 60 - 90 = -30 menit (negatif!)
waktu_selesai = sekarang (11:30)
Status: Overtime
```

**Hasil:**
- ⚠️ Timer menunjukkan 00:00:00 atau overtime
- ⚠️ Waktu selesai = sekarang
- ✅ Harga tetap dihitung berdasarkan jam main baru

## 🔧 Implementasi Teknis

### Fungsi Perhitungan Waktu Selesai (Paket)

```php
// Hitung waktu yang sudah terpakai
$waktuMulaiCarbon = Carbon::parse(
    $transaction->created_at->toDateString() . ' ' . 
    $transaction->waktu_mulai
);
$now = Carbon::now();
$elapsedMinutes = $waktuMulaiCarbon->diffInMinutes($now);

// Hitung sisa waktu
$jamMainMinutes = $jamMain * 60;
$remainingMinutes = $jamMainMinutes - $elapsedMinutes;

// Set waktu selesai
if ($remainingMinutes > 0) {
    // Masih ada sisa waktu
    $waktu_selesai = $now->addMinutes($remainingMinutes);
} else {
    // Waktu sudah habis/lewat
    $waktu_selesai = $now;
}
```

### Fungsi Lost Time Start

```php
if (!$transaction->lost_time_start) {
    // Set dari waktu_mulai asli
    $waktuMulaiCarbon = Carbon::parse(
        $transaction->created_at->toDateString() . ' ' . 
        $transaction->waktu_mulai
    );
    $lost_time_start = $waktuMulaiCarbon;
}
// Jika sudah ada, jangan ubah - timer melanjutkan
```

## 📋 Tabel Ringkasan

| Transisi | Timer Awal | Timer Setelah | Waktu Terpakai | Hasil |
|----------|------------|---------------|----------------|-------|
| Lost Time → Paket 1 jam | 00:05:00 | 00:55:00 | 5 menit | Countdown dari sisa |
| Paket 1 jam → Lost Time | 00:55:00 | 00:05:00 | 5 menit | Count up melanjutkan |
| Paket 3 jam → Paket 1 jam | 02:55:00 | 00:55:00 | 5 menit | Countdown sisa baru |
| Paket 1 jam → Paket 3 jam | 00:55:00 | 02:55:00 | 5 menit | Countdown sisa baru |

## ✅ Keuntungan Sistem Ini

1. **Akurat** - Waktu yang terpakai selalu dihitung dengan benar
2. **Adil** - Customer tidak bisa "curang" dengan reset timer
3. **Fleksibel** - Admin bisa ubah tipe/durasi kapan saja
4. **Transparan** - Timer selalu menunjukkan waktu yang sebenarnya
5. **Konsisten** - Semua perhitungan dari waktu_mulai yang sama

## 🎯 Prinsip Dasar

**Waktu mulai (`waktu_mulai`) TIDAK PERNAH BERUBAH!**

Semua perhitungan selalu mengacu ke waktu mulai asli:
- ✅ Lost Time: Hitung dari waktu_mulai sampai selesai
- ✅ Paket: Hitung sisa waktu dari waktu_mulai + durasi
- ✅ Edit: Sesuaikan waktu_selesai berdasarkan waktu terpakai

## 📝 Field Database

| Field | Fungsi | Contoh |
|-------|--------|--------|
| `waktu_mulai` | Waktu mulai asli (TETAP) | "10:00" |
| `waktu_Selesai` | Waktu target selesai (DINAMIS) | "11:00" |
| `jam_main` | Durasi paket (untuk Paket) | 1 atau "1 jam 30 menit" |
| `lost_time_start` | Timestamp mulai (untuk Lost Time) | "2025-12-10 10:00:00" |
| `created_at` | Tanggal transaksi | "2025-12-10 10:00:00" |

## 🔍 Contoh Lengkap

### Customer A: Lost Time → Paket

```
10:00:00 - Mulai Lost Time
          waktu_mulai: 10:00
          lost_time_start: 10:00:00
          Timer: 00:00:00 (count up)

10:05:00 - Sudah main 5 menit
          Timer: 00:05:00

10:05:00 - Admin ubah ke Paket 1 jam
          waktu_mulai: 10:00 (TETAP)
          jam_main: 1
          elapsed: 5 menit
          remaining: 55 menit
          waktu_selesai: 11:00
          Timer: 00:55:00 (countdown)

10:30:00 - Masih main
          Timer: 00:30:00 (countdown)

11:00:00 - Waktu habis
          Timer: 00:00:00
          Total durasi: 1 jam (dari 10:00)
          Harga: 1 jam × harga_per_jam
```

### Customer B: Paket 3 jam → Paket 1 jam

```
10:00:00 - Mulai Paket 3 jam
          waktu_mulai: 10:00
          jam_main: 3
          waktu_selesai: 13:00
          Timer: 03:00:00 (countdown)

10:05:00 - Sudah main 5 menit
          Timer: 02:55:00

10:05:00 - Admin ubah ke Paket 1 jam
          waktu_mulai: 10:00 (TETAP)
          jam_main: 1
          elapsed: 5 menit
          remaining: 55 menit
          waktu_selesai: 11:00 (bukan 13:00)
          Timer: 00:55:00 (countdown)
          Harga: 1 jam × harga_per_jam (bukan 3 jam)

11:00:00 - Waktu habis
          Total durasi: 1 jam
          Harga disesuaikan
```

## ⚠️ Catatan Penting

1. **Overtime Handling**: Jika waktu terpakai > durasi baru, waktu_selesai = sekarang
2. **Harga Menyesuaikan**: Harga selalu berdasarkan jam_main terbaru
3. **Timer Visual**: Frontend harus menampilkan timer sesuai tipe (count up/countdown)
4. **Validasi**: Sistem tidak mencegah overtime, tapi menandai dengan waktu_selesai = now
