# 📚 Catatan Kelas

Aplikasi pencatatan harian kelas berbasis web menggunakan Laravel dan Filament. Digunakan untuk mencatat aktivitas pembelajaran harian, presensi siswa, dan approval oleh wali kelas.

## ✨ Fitur Utama

- **Catatan Harian** - Input materi, jam kosong, catatan, dan presensi siswa
- **Multi-Role Access Control** - Admin, Guru, dan Wali Kelas dengan akses berbeda
- **Approval System** - Wali kelas dapat meng-approve catatan harian
- **Laporan** - Generate laporan catatan harian dalam format PDF
- **Dashboard** - Widget statistik dan catatan terbaru

## 👥 Role & Permission

| Role | Akses |
|------|-------|
| **Admin** | Akses penuh ke semua data dan fitur |
| **Guru** | Melihat jadwal & catatan yang dia ajar |
| **Wali Kelas** | Melihat semua catatan di kelas yang diwalikan + approve |
| **Wali Kelas + Guru** | Kombinasi kedua role |

## 🛠️ Tech Stack

- **Laravel 11** - PHP Framework
- **Filament 3** - Admin Panel
- **MySQL** - Database
- **Spatie Permission** - Role & Permission Management
- **DomPDF** - PDF Generation

## 📦 Instalasi

### Requirements
- PHP >= 8.2
- Composer
- MySQL/MariaDB
- Node.js & NPM

### Setup

```bash
# Clone repository
git clone https://github.com/kyraara/catatan-kelas.git
cd catatan-kelas

# Install dependencies
composer install
npm install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Configure database di .env
# DB_DATABASE=catatan_kelas
# DB_USERNAME=root
# DB_PASSWORD=

# Run migrations
php artisan migrate

# Seed dummy data (optional, untuk testing)
php artisan db:seed --class=DummyDataSeeder

# Build assets
npm run build

# Start server
php artisan serve
```

## 🧪 Testing dengan Dummy Data

Jalankan seeder untuk mengisi data dummy:

```bash
php artisan db:seed --class=DummyDataSeeder
```

### Akun Testing

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@school.test | password |
| Guru (Matematika) | budi.guru@school.test | password |
| Guru (B. Indo) | siti.guru@school.test | password |
| Wali Kelas 10A + Guru IPA | andi.walikelas@school.test | password |
| Wali Kelas 10B + Guru IPS | dewi.walikelas@school.test | password |
| Wali Kelas 10C | rudi.walikelas@school.test | password |

## 📁 Struktur Penting

```
app/
├── Filament/
│   ├── Resources/
│   │   ├── CatatanHarianResource.php   # CRUD Catatan Harian
│   │   ├── JadwalResource.php          # CRUD Jadwal
│   │   ├── KelasResource.php           # CRUD Kelas
│   │   └── SiswaResource.php           # CRUD Siswa
│   ├── Pages/
│   │   └── LaporanCatatanHarian.php    # Halaman Laporan
│   └── Widgets/
│       └── LatestCatatanHarian.php     # Widget Dashboard
├── Models/
│   ├── CatatanHarian.php   # Model dengan Global Scope role-based
│   ├── Jadwal.php
│   ├── Kelas.php
│   ├── Siswa.php
│   └── Presensi.php
```

## 🔐 Role-Based Filtering

### CatatanHarian
- **Admin**: Semua catatan
- **Guru**: Catatan dari jadwal yang diajar + yang diinput sendiri
- **Wali Kelas**: Catatan di kelas yang diwalikan + yang diinput sendiri

### Jadwal
- **Admin**: Semua jadwal
- **Guru**: Jadwal yang diajar
- **Wali Kelas**: Jadwal di kelas yang diwalikan + yang diajar

## 📝 License

MIT License - bebas digunakan untuk keperluan apapun.

## 🤝 Kontribusi

Pull request dan issue sangat diterima!
