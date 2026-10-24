# EventPlan - Aplikasi Perencanaan & Manajemen Waktu Acara Berbasis Critical Path Method (CPM)

EventPlan adalah platform web modern untuk manajemen proyek dan perencanaan waktu acara (*Event Planning & Time Management*) yang mengimplementasikan algoritma **Critical Path Method (CPM)**, visualisasi interaktif **Gantt Chart**, **PERT / Network Diagram (Activity on Node)**, serta pemantauan kapasitas tim (**Resource Workload & Over-allocation**).

Dibangun menggunakan stack **Laravel 13**, **Tailwind CSS v4**, **Alpine.js**, dan basis data **MySQL**.

---

## 🌟 Fitur Utama

1. **Autentikasi & Multi-Tenancy Proyek**
   - Mendukung login OAuth Google melalui Laravel Socialite.
   - Tersedia tombol cepat *Dev / Local Login* untuk kemudahan pengujian di lingkungan pengembangan lokal tanpa kredensial Google asli.
   - Kebijakan isolasi otorisasi data (*ProjectPolicy*): hanya pemilik (*owner*) atau anggota proyek yang berwenang yang dapat melihat dan memodifikasi data acara.

2. **Manajemen Proyek & Kalender Kerja Kustom**
   - Konfigurasi hari kerja mingguan (default: Senin - Jumat) dan jam operasional per hari.
   - Manajemen hari libur nasional atau cuti bersama per kalender kerja.
   - Perhitungan otomatis tanggal selesai tugas (*end date*) yang melompati akhir pekan dan hari libur secara presisi.

3. **Struktur Rincian Tugas & Ketergantungan (Task & Dependency Engine)**
   - Manajemen tugas dan subtugas dengan penentuan prioritas (*Urgent*, *High*, *Medium*, *Low*) dan status kerja.
   - Relasi ketergantungan antar-tugas: **Finish-to-Start (FS)** dan **Start-to-Start (SS)** beserta pengaturan waktu jeda (*lag days*).
   - Validasi siklus ketergantungan berbasis algoritma deteksi siklus **Directed Acyclic Graph (DAG)** untuk mencegah *infinite loop* atau referensi melingkar (*deadlock*).

4. **Kalkulasi Critical Path Method (CPM) Presisi Tinggi**
   - **Forward Pass**: Menghitung *Early Start* (ES) dan *Early Finish* (EF).
   - **Backward Pass**: Menghitung *Late Start* (LS) dan *Late Finish* (LF).
   - **Total Float & Critical Path**: Mengidentifikasi tugas-tugas dengan kelonggaran waktu nol (*Total Float = 0*) yang menjadi jalur kritis penentu keterlambatan seluruh proyek acara.

5. **Visualisasi Interaktif**
   - **Gantt Chart**: Linimasa visual tugas, persentase progres, dan penyorotan tugas di jalur kritis dengan kontras warna khusus.
   - **PERT / Network Diagram**: Diagram graf simpul (*Activity on Node*) SVG interaktif yang dilengkapi fitur geser (*pan*), perbesar/perkecil (*zoom*), serta panah berarah merah untuk jalur kritis.
   - **Resource Workload Matrix**: Matriks heatmap dan timeline beban kerja harian tim, pendeteksi bentrok jadwal cuti, dan peringatan *over-allocation* (> 100% kapasitas).

6. **Dashboard Analitik & Ekspor Laporan CSV**
   - Ringkasan statistik proyek (Aktif, Planning, Selesai), tugas kritis, dan widget tugas dengan *deadline* terdekat (7 hari mendatang).
   - Fitur ekspor data tugas dan hasil kalkulasi CPM ke format CSV dengan proteksi *CSV Formula Injection Mitigation* (penambahan karakter proteksi pada sel yang diawali tanda `=`, `+`, `-`, atau `@`).

---

## 🚀 Kebutuhan Sistem & Dependensi

- **PHP**: `>= 8.2` (Rekomendasi PHP 8.3 / 8.5 dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `bcmath`)
- **Composer**: `>= 2.0`
- **Node.js**: `>= 20.x` & **npm**
- **Basis Data**: MySQL `>= 8.0` atau MariaDB `>= 10.5`

---

## 🛠️ Panduan Instalasi & Menjalankan Aplikasi

### 1. Kloning Repositori & Install Dependensi PHP
```bash
git clone https://github.com/arieldauddotcom/EventPlan.git
cd EventPlan

# Install pustaka vendor PHP
composer install
```

### 2. Konfigurasi Lingkungan (.env)
Salin berkas konfigurasi template `.env.example` ke `.env`:
```bash
cp .env.example .env
php artisan key:generate
```

Pastikan konfigurasi koneksi basis data pada berkas `.env` telah sesuai:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=eventplan_db
DB_USERNAME=root
DB_PASSWORD=
```

Buat database jika belum tersedia:
```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS eventplan_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 3. Jalankan Migrasi Basis Data
```bash
php artisan migrate
```

### 4. Kompilasi Aset Frontend (Tailwind CSS v4 & Alpine.js)
```bash
npm install
npm run build
```

Untuk mode pengembangan frontend secara *hot-reload*:
```bash
npm run dev
```

### 5. Menjalankan Server Lokal Laravel
```bash
php artisan serve
```
Buka browser pada alamat: `http://127.0.0.1:8000`.

---

## 🔑 Petunjuk Masuk (Login)

1. **Dev / Local Login (Instan)**
   Pada halaman login (`/login`), klik tombol **"Masuk Cepat (Akun Uji Coba / Developer)"**. Sistem akan secara otomatis mengotentikasi sebagai pengguna pengembang `dev@eventplan.local`.

2. **Google OAuth (Opsional)**
   Tambahkan kredensial Google Cloud Console pada berkas `.env` jika ingin mengaktifkan *Sign in with Google*:
   ```env
   GOOGLE_CLIENT_ID=your_client_id_here
   GOOGLE_CLIENT_SECRET=your_client_secret_here
   GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
   ```

---

## 🧪 Menjalankan Automated Testing

Proyek ini telah dilengkapi serangkaian pengujian unit (*Unit Tests*) dan fitur (*Feature Tests*) menggunakan PHPUnit / Pest:

```bash
php artisan test
```

Cakupan pengujian mencakup:
- **CriticalPathCalculatorTest**: Validasi kalkulasi forward pass, backward pass, total float, dan deteksi critical path.
- **WorkingDaysCalculatorTest**: Validasi pemotongan hari libur dan akhir pekan.
- **TaskAndResourceManagementTest**: Validasi kalkulasi tanggal kerja, pencegahan circular dependency (siklus DAG), dan deteksi konflik cuti resource.
- **VisualizationTest**: Validasi render dan aksesibilitas view Gantt Chart, PERT Network Diagram, dan Resource Chart.
- **DashboardAndExportTest**: Pengujian metrik dashboard, widget deadline, serta integritas sanitasi file CSV terhadap formula injection.
- **AuthAndProjectTest**: Pengujian alur otentikasi dan isolasi akses antar-pengguna.

---

## 📂 Struktur Modul & Layanan Utama

- `app/Services/CriticalPathCalculator.php`: Algoritma inti penentuan jalur kritis (CPM).
- `app/Services/WorkingDaysCalculator.php`: Layanan perhitungan hari dan jam kerja.
- `app/Services/DependencyValidator.php`: Pemeriksa siklus ketergantungan graf (*Cycle Detection*).
- `app/Http/Controllers/VisualizationController.php`: Pengontrol tampilan Gantt, PERT, dan Resource Heatmap.
- `app/Http/Controllers/ExportController.php`: Ekspor data dan sanitasi CSV.
- `resources/views/projects/visualizations/`: Template Blade visualisasi interaktif Gantt, PERT, dan Resource.

---

## 📄 Lisensi

EventPlan didistribusikan di bawah lisensi terbuka [MIT License](LICENSE).
