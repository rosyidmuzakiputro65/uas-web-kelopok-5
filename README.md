# uas-web-kelopok-5
Sistem Tahfizh - Aplikasi Manajemen Hafalan Al-Qur'an
Sistem informasi berbasis web untuk mengelola data setoran hafalan Al-Qur'an santri dengan 3 level pengguna: Admin, Muhaffizh (Penguji), dan Dosen (Monitoring).
--------------------------------------------------------------------------------------------------------------------------------------------------------------------
- Fitur Utama
- Admin
Kelola akun pengguna (Admin, Muhaffizh, Dosen)
Kelola data santri (NIM, Nama, Semester)
CRUD lengkap untuk manajemen user dan santri
--------------------------------------------------------------------------------------------------------------------------------------------------------------------
- Muhaffizh (Penguji)
Input setoran hafalan santri
Edit dan hapus data setoran sendiri
Pencarian riwayat inputan
Penilaian dengan 4 predikat: A (Mumtaz), B (Jayyid), C (Maqbul), D (Rasib)
--------------------------------------------------------------------------------------------------------------------------------------------------------------------
- Dosen (Monitoring)
Monitoring seluruh setoran hafalan
Filter berdasarkan:
Pencarian (nama santri, surah, muhaffizh)
Predikat/nilai
Tanggal
Statistik total setoran dan nilai A (Mumtaz)
Cetak laporan
--------------------------------------------------------------------------------------------------------------------------------------------------------------------
- Default Credentials
Use these accounts to test the Role-Based Access Control (RBAC):

ex = (Role :	"Username",	"Password",	"Access")
Administrator :  ("admin", "admin123",	[Full Access Users, Data Student, Reports])
Muhafidz :  ("rosyid1", "rosyid1233", [Inventory, Daily Reports])
Dosen :  ("g", "password123",	[Monitoring, View Inventory])
