# Redeem Code System

Sistem kode redeem untuk aplikasi PustakaDigital yang memungkinkan admin membuat dan mengelola kode promo, diskon, cashback, dan gratis ongkir.

## Fitur Utama

### Admin Panel
- ✅ **Manajemen Kode Redeem**: CRUD lengkap untuk kode redeem
- ✅ **Generate Multiple Codes**: Membuat banyak kode sekaligus
- ✅ **Validasi Kode**: Sistem validasi otomatis berdasarkan periode dan penggunaan
- ✅ **Monitoring Penggunaan**: Tracking penggunaan kode
- ✅ **Status Management**: Aktif/Nonaktif/Kadaluarsa

### User Features
- ✅ **Validasi Kode**: API untuk memvalidasi kode sebelum checkout
- ✅ **Penggunaan Kode**: API untuk menggunakan kode saat checkout
- ✅ **Daftar Kode Tersedia**: API untuk menampilkan kode yang masih aktif

## Tipe Kode Redeem

1. **Diskon** - Potongan harga berdasarkan persentase atau nominal
2. **Cashback** - Pengembalian dana setelah pembelian
3. **Gratis Ongkir** - Pembebasan biaya pengiriman

## Struktur Database

### Tabel: `redeem_codes`

| Field | Type | Description |
|-------|------|-------------|
| `id` | bigint | Primary key |
| `code` | string | Kode unik (8 karakter) |
| `type` | enum | Tipe kode (discount/cashback/free_shipping) |
| `value` | decimal | Nilai diskon/cashback |
| `value_type` | enum | Tipe nilai (percentage/fixed) |
| `max_usage` | integer | Maksimal penggunaan |
| `used_count` | integer | Jumlah sudah digunakan |
| `min_purchase` | decimal | Minimal pembelian (opsional) |
| `valid_from` | date | Tanggal mulai berlaku |
| `valid_until` | date | Tanggal berakhir berlaku |
| `status` | enum | Status (active/inactive/expired) |
| `description` | text | Deskripsi kode (opsional) |
| `created_at` | timestamp | Waktu dibuat |
| `updated_at` | timestamp | Waktu terakhir update |

## API Endpoints

### Admin Routes
```
GET    /admin/redeem_code              # Daftar semua kode
GET    /admin/redeem_code/create       # Form buat kode baru
POST   /admin/redeem_code              # Simpan kode baru
GET    /admin/redeem_code/{id}         # Detail kode
GET    /admin/redeem_code/{id}/edit    # Form edit kode
PUT    /admin/redeem_code/{id}         # Update kode
DELETE /admin/redeem_code/{id}         # Hapus kode
POST   /admin/redeem_code/{id}/toggle-status  # Toggle status
POST   /admin/redeem_code/generate-multiple   # Generate multiple codes
```

### User Routes
```
POST   /redeem-code/validate           # Validasi kode
POST   /redeem-code/use                # Gunakan kode
GET    /redeem-code/available          # Daftar kode tersedia
```

## Cara Penggunaan

### 1. Admin - Membuat Kode Redeem

1. Login sebagai admin
2. Akses menu "Manajemen kode redeem"
3. Klik "Buat Kode Baru" atau "Generate Multiple"
4. Isi form dengan detail kode
5. Simpan kode

### 2. User - Menggunakan Kode Redeem

#### Menggunakan JavaScript
```javascript
// Include redeem-code.js
<script src="/js/redeem-code.js"></script>

// Initialize redeem code input
redeemCodeManager.initRedeemCodeInput('#redeem-code-input', '#validate-button', 150000);

// Validate code manually
const result = await redeemCodeManager.validateCode('DISKON10', 150000);
if (result.success) {
    console.log('Discount amount:', result.data.discount_amount);
}

// Use code
const usage = await redeemCodeManager.useCode('DISKON10', 150000);
if (usage.success) {
    console.log('Code used successfully');
}
```

#### Menggunakan API Langsung
```javascript
// Validate code
fetch('/redeem-code/validate', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
    },
    body: JSON.stringify({
        code: 'DISKON10',
        amount: 150000
    })
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log('Valid code:', data.data);
    } else {
        console.log('Invalid code:', data.message);
    }
});
```

## Validasi Kode

Sistem akan memvalidasi kode berdasarkan:

1. **Ketersediaan**: Kode harus ada di database
2. **Status**: Kode harus berstatus 'active'
3. **Periode Berlaku**: Tanggal saat ini harus dalam rentang valid_from - valid_until
4. **Penggunaan**: used_count harus < max_usage
5. **Minimal Pembelian**: Jika ada, amount harus >= min_purchase

## Sample Kode Redeem

Sistem sudah dilengkapi dengan sample kode untuk testing:

- `DISKON10` - Diskon 10% (min. pembelian Rp 100.000)
- `CASHBACK50K` - Cashback Rp 50.000 (min. pembelian Rp 200.000)
- `GRATISONGKIR` - Gratis ongkir (min. pembelian Rp 50.000)
- `DISKON25` - Diskon 25% (min. pembelian Rp 150.000)
- `WELCOME20` - Diskon 20% (tanpa minimal pembelian)

## Keamanan

- Kode di-generate secara otomatis dengan kombinasi huruf dan angka
- Validasi server-side untuk semua operasi
- CSRF protection untuk semua form dan API
- Rate limiting dapat ditambahkan untuk mencegah abuse

## Extensions

Sistem dapat diperluas dengan fitur:

- **User-specific codes**: Kode khusus untuk user tertentu
- **Category-specific codes**: Kode untuk kategori buku tertentu
- **First-time user codes**: Kode khusus pelanggan baru
- **Loyalty codes**: Kode berdasarkan poin loyalitas
- **Referral codes**: Kode referral untuk user baru
- **Bulk import/export**: Import/export kode dari file Excel/CSV

## Troubleshooting

### Kode tidak ditemukan
- Pastikan kode diinput dengan benar (case insensitive)
- Periksa apakah kode sudah dibuat di admin panel

### Kode tidak dapat digunakan
- Periksa status kode (harus 'active')
- Periksa periode berlaku kode
- Periksa jumlah penggunaan (tidak boleh melebihi max_usage)
- Periksa minimal pembelian (jika ada)

### Error saat generate kode
- Pastikan semua field required terisi
- Periksa format tanggal (valid_until harus > valid_from)
- Periksa nilai numerik (tidak boleh negatif) 