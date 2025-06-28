# Sistem Kode Redeem - PustakaDigital

Sistem kode redeem untuk aplikasi PustakaDigital yang memungkinkan admin membuat dan mengelola kode promo, diskon, dan cashback.

## Jenis Kode Redeem

1. **Diskon** - Pengurangan harga berdasarkan persentase atau nominal tetap
2. **Cashback** - Pengembalian dana setelah pembelian
3. **Promo** - Penawaran khusus untuk produk tertentu

## Fitur Utama

- **Manajemen Kode**: Admin dapat membuat, mengedit, dan menghapus kode redeem
- **Validasi Otomatis**: Sistem memvalidasi kode berdasarkan masa berlaku dan batas penggunaan
- **Laporan Penggunaan**: Tracking penggunaan kode untuk analisis
- **Integrasi Checkout**: Kode dapat digunakan langsung saat checkout

## Struktur Database

### Tabel `redeem_codes`

| Field | Type | Description |
|-------|------|-------------|
| `id` | bigint | Primary key |
| `code` | varchar(50) | Kode redeem (unique) |
| `type` | enum | Jenis kode: discount, cashback, promo |
| `value` | decimal | Nilai diskon/cashback |
| `value_type` | enum | Tipe nilai: percentage, fixed |
| `max_usage` | int | Maksimal penggunaan |
| `used_count` | int | Jumlah penggunaan saat ini |
| `min_purchase` | decimal | Minimal pembelian untuk menggunakan kode |
| `valid_from` | timestamp | Tanggal mulai berlaku |
| `valid_until` | timestamp | Tanggal berakhir berlaku |
| `status` | enum | Status: active, inactive, expired |
| `description` | text | Deskripsi kode |
| `created_at` | timestamp | Waktu pembuatan |
| `updated_at` | timestamp | Waktu update terakhir |

## Contoh Kode Redeem

- `DISKON10` - Diskon 10% (min. pembelian Rp 100.000)
- `CASHBACK50K` - Cashback Rp 50.000 (min. pembelian Rp 200.000)
- `DISKON25` - Diskon 25% (min. pembelian Rp 150.000)
- `WELCOME20` - Diskon 20% untuk pelanggan baru

## Cara Penggunaan

### Untuk Admin

1. **Membuat Kode Baru**:
   - Akses menu "Redeem Code" di admin panel
   - Klik "Tambah Kode Baru"
   - Isi form dengan detail kode
   - Simpan kode

2. **Mengelola Kode**:
   - Lihat daftar kode di halaman index
   - Edit kode yang ada
   - Hapus kode yang tidak diperlukan
   - Monitor penggunaan kode

### Untuk User

1. **Menggunakan Kode**:
   - Masukkan kode saat checkout
   - Sistem akan memvalidasi kode otomatis
   - Diskon/cashback akan diterapkan jika valid

2. **Validasi Kode**:
   - Kode harus masih berlaku
   - Belum melebihi batas penggunaan
   - Memenuhi syarat minimal pembelian
   - Status kode aktif

## Integrasi dengan Sistem

### Controller: `RedeemCodeController`

- `index()` - Menampilkan daftar kode
- `create()` - Form pembuatan kode
- `store()` - Menyimpan kode baru
- `edit()` - Form edit kode
- `update()` - Update kode
- `destroy()` - Hapus kode
- `show()` - Detail kode

### Model: `RedeemCode`

- Relasi dengan `Order` untuk tracking penggunaan
- Method validasi kode
- Method untuk mengecek status kode

### View Templates

- `index.blade.php` - Daftar kode dengan fitur search dan filter
- `create.blade.php` - Form pembuatan kode
- `edit.blade.php` - Form edit kode
- `show.blade.php` - Detail kode

## Validasi dan Keamanan

- **Unique Code**: Kode redeem harus unik
- **Date Validation**: Validasi tanggal berlaku
- **Usage Limit**: Pembatasan penggunaan kode
- **Status Check**: Hanya kode aktif yang bisa digunakan
- **CSRF Protection**: Proteksi form dari CSRF attack

## Monitoring dan Analytics

- **Usage Tracking**: Mencatat setiap penggunaan kode
- **Performance Metrics**: Analisis efektivitas kode
- **User Behavior**: Tracking preferensi user terhadap kode
- **Revenue Impact**: Dampak kode terhadap pendapatan

## Best Practices

1. **Naming Convention**: Gunakan nama kode yang mudah diingat
2. **Value Setting**: Set nilai yang reasonable dan profitable
3. **Expiry Management**: Atur masa berlaku yang sesuai
4. **Usage Monitoring**: Monitor penggunaan secara berkala
5. **User Communication**: Berikan informasi jelas tentang kode

## Troubleshooting

### Masalah Umum

1. **Kode Tidak Berlaku**:
   - Cek tanggal berlaku
   - Cek status kode
   - Cek batas penggunaan

2. **Kode Tidak Diterima**:
   - Cek minimal pembelian
   - Cek tipe produk yang berlaku
   - Cek kombinasi dengan kode lain

3. **Error Validasi**:
   - Cek format kode
   - Cek karakter yang diizinkan
   - Cek panjang kode

## Update dan Maintenance

- **Regular Review**: Review kode secara berkala
- **Performance Optimization**: Optimasi query database
- **Security Updates**: Update keamanan sistem
- **Feature Enhancement**: Tambah fitur baru sesuai kebutuhan 