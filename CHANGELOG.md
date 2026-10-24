# Catatan Perubahan (Changelog) - EventPlan

Seluruh perubahan penting, fitur baru, perbaikan, dan optimasi arsitektur pada proyek **EventPlan** dicatat secara kronologis dalam dokumen ini.

---

## [1.0.0] - 2026-10-24

### 🌟 Fitur Inti & Algoritma (Core Engine)
- **Kalkulasi Critical Path Method (CPM)**:
  - Implementasi *Forward Pass* untuk menghitung *Early Start* (ES) dan *Early Finish* (EF).
  - Implementasi *Backward Pass* untuk menghitung *Late Start* (LS) dan *Late Finish* (LF).
  - Kalkulasi *Total Float* otomatis dan penentuan jalur kritis (*Critical Path*) tugas dengan kelonggaran waktu 0 hari.
- **Kalkulator Kalender Kerja & Hari Libur**:
  - Perhitungan tanggal selesai (*end date*) tugas melompati akhir pekan (Sabtu-Minggu) dan hari libur nasional/cuti bersama.
  - Dukungan multi-kalender kerja per proyek.
- **Deteksi Siklus Ketergantungan (DAG Cycle Detection)**:
  - Validasi dependensi tugas berbasis *Directed Acyclic Graph* (DAG) untuk mencegah referensi melingkar (*circular dependency*).

### 📊 Visualisasi & Pelaporan
- **Gantt Chart Linimasa**:
  - Linimasa interaktif horizontal dengan penanda jalur kritis (warna merah kontras), status tugas, dan persentase progres.
- **PERT / Network Diagram**:
  - Diagram visual simpul graf (*Activity on Node*) berbasis SVG dengan kontrol geser (*pan*), zoom, dan penanda arah dependensi.
- **Matriks Beban Kerja Sumber Daya (Resource Workload)**:
  - Visualisasi beban kerja harian tim, pendeteksi bentrok jadwal cuti, serta peringatan *over-allocation* (> 100% kapasitas).
- **Ekspor Dokumen**:
  - Ekspor laporan proyek ke format PDF dengan tata letak rapi (*print-ready*).
  - Ekspor tugas ke CSV dengan sanitasi formula injection.

### 👥 Manajemen Proyek & Kolaborasi
- **Sistem Peran Proyek (Multi-Role)**:
  - Peran *Leader* (pemilik/manajer) dan *Member* (anggota tim).
  - Sistem undangan proyek berbasis email (*Project Invitations*) dengan alur terima/tolak.
  - Isolasi akses proyek (*ProjectPolicy*) untuk keamanan data antar pengguna.
- **Manajemen Tugas & Alokasi Tim**:
  - CRUD tugas, penentuan durasi hari kerja, prioritas, dan tanggal target.
  - Alokasi anggota tim (*task assignments*) per tugas acara.

### 🔐 Autentikasi & Pengelolaan Akun
- **Multi-Metode Autentikasi**:
  - Dukungan login menggunakan Username atau Alamat Email.
  - Integrasi login Google OAuth (Laravel Socialite).
  - Tombol pengujian cepat (*Dev / Local Login*) untuk kebutuhan pengembangan.
- **Panel Profil & Admin**:
  - Halaman edit profil mandiri, ubah kata sandi, dan unggah foto profil (avatar).
  - Panel kelola pengguna khusus administrator (*EnsureUserIsAdmin*).

### 🌐 Halaman Utama (Landing Page) & Tampilan Publik
- **Landing Page Interaktif**:
  - Hero section dinamis dengan ajakan bertindak (CTA).
  - Simulator CPM interaktif untuk simulasi durasi proyek acara secara langsung di browser tanpa login.
  - Showcase visualisasi Gantt Chart interaktif sebagai pilihan utama.
  - Komparasi fitur konvensional vs EventPlan CPM.
  - Showcase tipe acara (Konser Musik, Konferensi Tech, Pameran Seni, Pernikahan Akbar, Festival Kuliner).
  - Accordion FAQ interaktif.
- **Layout Login & Register**:
  - Desain *fixed viewport* satu layar tanpa scroll vertikal yang presisi.
  - Glassmorphism backdrop dengan visual atmosfer event konser.
  - Tombol pintasan akun demo instan untuk admin dan perencana proyek (*Mohamad Nazril R. Daud*).

### 🔍 SEO & Keamanan Perayapan
- **Meta Tag & OpenGraph**:
  - Konfigurasi title dinamis, meta description, kata kunci relevan, dan OpenGraph kartu sosial media.
- **Proteksi robots.txt**:
  - Larangan perayapan bot mesin pencari untuk direktori privat `/login`, `/register`, `/dashboard`, `/admin/`, dan `/projects/`.
  - Tag `<meta name="robots" content="noindex, nofollow">` pada seluruh view privat aplikasi.

### 🧪 Pengujian Otomatis (Automated Testing)
- Rangkaian 57 pengujian otomatis (Unit & Feature Tests) dengan 220 assertion yang lolos 100% mencakup:
  - `CriticalPathCalculatorTest`
  - `WorkingDaysCalculatorTest`
  - `TaskAndResourceManagementTest`
  - `VisualizationTest`
  - `DashboardAndExportTest`
  - `AuthAndProjectTest`
  - `EmailPasswordAuthTest`
  - `RegistrationTest`
  - `ProfileTest`
  - `AdminUserManagementTest`
  - `ProjectPdfExportTest`
  - `ProjectCollaborationAndRolesTest`
