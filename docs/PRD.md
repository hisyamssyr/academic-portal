# Product Requirements Document (PRD)

## Secure Academic Portal

**Versi:** 1.0
**Status:** Draft
**Jenis:** Tugas Kuliah Individu - Pertemuan 5
**Framework:** Laravel
**Authentication:** Laravel Breeze
**Database:** SQLite

---

# 1. Ringkasan Project

Secure Academic Portal adalah aplikasi portal akademik sederhana berbasis Laravel yang menerapkan sistem autentikasi dan otorisasi multi-peran.

Aplikasi memiliki tiga jenis pengguna:

* **Mahasiswa**
* **Asisten Dosen (Asdos)**
* **Dosen**

Project ini merupakan pengembangan dari project Laravel sebelumnya dan berfokus pada penerapan:

1. Laravel Breeze untuk authentication.
2. Custom role pada user.
3. Custom Middleware untuk pembatasan akses berdasarkan role.
4. Global Gate untuk authorization aksi tertentu.
5. Model Policy untuk authorization terhadap resource tertentu.
6. Blade authorization menggunakan `@can`.
7. Rate Limiting untuk submission proposal.
8. Auto-login akun demo khusus local development.

Fokus utama project adalah menunjukkan perbedaan antara **authentication** dan **authorization**, serta penerapan beberapa mekanisme authorization Laravel secara langsung.

---

# 2. Tujuan Project

## 2.1 Tujuan Utama

Membangun portal akademik sederhana yang mampu:

* Melakukan register dan login menggunakan Laravel Breeze.
* Menyimpan role setiap user.
* Membatasi akses halaman berdasarkan role.
* Membatasi aksi tertentu menggunakan Gate.
* Membatasi akses terhadap resource tertentu menggunakan Policy.
* Menyembunyikan fitur yang tidak dapat digunakan user pada Blade UI.
* Mencegah spam submission proposal menggunakan rate limiting.
* Mempermudah pengujian tiga role melalui auto-login pada local development.

## 2.2 Tujuan Pembelajaran

Project harus menunjukkan pemahaman terhadap:

* Laravel Authentication.
* Laravel Middleware.
* Laravel Gates.
* Laravel Policies.
* Authorization pada Controller.
* Authorization pada Blade.
* Database Migration.
* Eloquent Relationship.
* Rate Limiting.
* Environment-based feature.

---

# 3. Target User

## 3.1 Mahasiswa

Mahasiswa dapat:

* Register akun.
* Login.
* Melihat dashboard.
* Membuat proposal/rencana proyek AI.
* Melihat proposal miliknya.
* Mengedit proposal miliknya.
* Tidak dapat mengakses area `/admin`.
* Tidak dapat mengedit proposal milik mahasiswa lain.

## 3.2 Asisten Dosen

Asdos dapat:

* Login.
* Mengakses area `/admin`.
* Melihat daftar proposal.
* Meninjau proposal.
* Menginput nilai mahasiswa.

Asdos tidak dapat melakukan aksi yang hanya diperuntukkan bagi mahasiswa.

## 3.3 Dosen

Dosen memiliki akses yang sama terhadap area admin seperti Asdos untuk kebutuhan tugas:

* Login.
* Mengakses `/admin`.
* Melihat proposal mahasiswa.
* Meninjau proposal.
* Menginput nilai.

---

# 4. Tech Stack

## Backend

* PHP
* Laravel
* Laravel Breeze
* Eloquent ORM

## Frontend

* Blade
* Tailwind CSS
* Vite
* Vanilla JavaScript jika diperlukan

## Database

* SQLite

## Development Tools

* Composer
* npm
* Git
* Laravel Artisan

Tidak menggunakan package permission pihak ketiga seperti Spatie Permission karena tugas membutuhkan implementasi custom Middleware, Gate, dan Policy.

---

# 5. Arsitektur Aplikasi

```text
Browser
   │
   ▼
Laravel Routes
   │
   ├── Public Area
   │
   ├── Authentication
   │      └── Laravel Breeze
   │
   ├── Student Area
   │      └── Proposal
   │             └── Policy
   │
   └── Admin Area
          └── cek.peran Middleware
                 ├── Dosen
                 └── Asdos

Controllers
   │
   ├── Gate
   └── Policy
   │
   ▼
Eloquent Models
   │
   ▼
SQLite
```

---

# 6. Role dan Permission

| Fitur                   | Guest |        Mahasiswa | Asdos | Dosen |
| ----------------------- | ----: | ---------------: | ----: | ----: |
| Landing Page            |     ✓ |                ✓ |     ✓ |     ✓ |
| Register                |     ✓ |                - |     - |     - |
| Login                   |     ✓ |                ✓ |     ✓ |     ✓ |
| Dashboard               |     - |                ✓ |     ✓ |     ✓ |
| Membuat Proposal        |     - |                ✓ |     ✗ |     ✗ |
| Edit Proposal Sendiri   |     - |                ✓ |     ✗ |     ✗ |
| Edit Proposal User Lain |     - |                ✗ |     ✗ |     ✗ |
| Akses `/admin`          |     ✗ |                ✗ |     ✓ |     ✓ |
| Melihat Proposal        |     ✗ | Proposal sendiri |     ✓ |     ✓ |
| Input Nilai             |     ✗ |                ✗ |     ✓ |     ✓ |

---

# 7. Area Aplikasi

## 7.1 Public Area

URL:

```text
/
```

Dapat diakses oleh semua orang tanpa login.

Isi minimal:

* Nama aplikasi.
* Sambutan.
* Deskripsi singkat portal.
* Tombol Login.
* Tombol Register.

---

# 8. Authentication

Authentication wajib menggunakan **Laravel Breeze**.

Fitur:

* Register.
* Login.
* Logout.
* Session authentication.
* Password hashing.
* Validation bawaan Breeze.

Tidak membuat sistem authentication custom.

---

# 9. User Role

Tabel `users` mendapatkan kolom tambahan:

```text
role
```

Nilai yang diperbolehkan:

```text
mahasiswa
asdos
dosen
```

Role digunakan untuk menentukan authorization user.

Contoh:

```text
User
├── name
├── email
├── password
└── role
      ├── mahasiswa
      ├── asdos
      └── dosen
```

Role ditambahkan menggunakan **custom migration**, bukan mengubah migration bawaan secara langsung setelah project berjalan.

---

# 10. Database Design

## 10.1 Users

```text
users
-------------------------
id
name
email
password
role
created_at
updated_at
```

`role` menentukan jenis akses user.

---

## 10.2 Project Proposals

```text
project_proposals
-------------------------
id
user_id
title
description
status
created_at
updated_at
```

Relationship:

```text
User
  │
  │ hasMany
  ▼
ProjectProposal
```

Setiap proposal memiliki satu user sebagai pembuat.

Status proposal:

```text
draft
submitted
reviewed
```

---

## 10.3 Grades

```text
grades
-------------------------
id
student_id
grader_id
course
score
feedback
created_at
updated_at
```

Relationship:

```text
student_id → users.id
grader_id  → users.id
```

`student_id` merupakan mahasiswa yang menerima nilai.

`grader_id` merupakan dosen/asdos yang memberikan nilai.

---

# 11. Entity Relationship

```text
                       ┌─────────────────┐
                       │      users      │
                       ├─────────────────┤
                       │ PK id           │
                       │ name            │
                       │ email           │
                       │ password        │
                       │ role            │
                       └───────┬─────────┘
                               │
                  ┌────────────┴────────────┐
                  │                         │
                1:N                       1:N
                  │                         │
                  ▼                         ▼
       ┌────────────────────┐    ┌────────────────────┐
       │ project_proposals  │    │       grades       │
       ├────────────────────┤    ├────────────────────┤
       │ PK id              │    │ PK id              │
       │ FK user_id         │    │ FK student_id      │
       │ title              │    │ FK grader_id       │
       │ description        │    │ course             │
       │ status             │    │ score              │
       │ created_at         │    │ feedback           │
       │ updated_at         │    │ created_at         │
       └────────────────────┘    │ updated_at         │
                                 └────────────────────┘
```

---

# 12. Student Area

URL:

```text
/dashboard
```

Hanya dapat diakses user yang telah login.

Dashboard mahasiswa menampilkan:

* Informasi user.
* Role user.
* Daftar proposal milik user.
* Tombol membuat proposal.
* Tombol edit proposal yang dimiliki.

---

# 13. Proposal Management

Mahasiswa dapat membuat proposal rencana proyek AI.

Field:

```text
title
description
```

Status awal:

```text
draft
```

Ketika proposal disubmit:

```text
draft → submitted
```

Proposal yang telah ditinjau dapat memiliki status:

```text
reviewed
```

---

# 14. Policy

Model Policy yang digunakan:

```text
ProjectProposalPolicy
```

Policy bertanggung jawab terhadap authorization proposal berdasarkan pemilik resource.

Aturan utama:

```text
Mahasiswa hanya dapat mengedit proposal miliknya sendiri.
```

Logika authorization:

```text
authenticated user
        │
        ▼
proposal.user_id == user.id ?
        │
   ┌────┴────┐
  YES        NO
   │          │
 ALLOW       DENY
```

Policy juga digunakan pada controller sebelum aksi edit/update.

Contoh konsep:

```php
$this->authorize('update', $proposal);
```

Policy tidak digantikan oleh pengecekan role sederhana.

---

# 15. Custom Middleware

Middleware:

```text
CekPeran
```

Alias:

```text
cek.peran
```

Middleware digunakan untuk melindungi route admin.

Route:

```text
/admin
```

Hanya role berikut yang diperbolehkan:

```text
dosen
asdos
```

Mahasiswa harus ditolak.

Guest harus diarahkan ke halaman login.

Flow:

```text
GET /admin
     │
     ▼
cek.peran
     │
     ├── Guest ──────► Login
     │
     ├── Mahasiswa ─► Access Denied
     │
     ├── Asdos ─────► Admin
     │
     └── Dosen ─────► Admin
```

Middleware harus didaftarkan menggunakan alias:

```text
cek.peran
```

---

# 16. Admin Area

URL:

```text
/admin
```

Hanya dapat diakses:

```text
dosen
asdos
```

Halaman admin menyediakan:

## Dashboard Admin

Menampilkan:

* Nama user.
* Role.
* Jumlah proposal.
* Daftar proposal terbaru.

## Proposal Review

Dosen/asdos dapat:

* Melihat daftar proposal.
* Melihat detail proposal.
* Melihat informasi mahasiswa.
* Mengubah status review jika diperlukan.

## Input Nilai

Dosen/asdos dapat:

* Memilih mahasiswa.
* Memilih mata kuliah.
* Mengisi nilai.
* Mengisi feedback.

---

# 17. Global Gate

Minimal satu Gate global harus digunakan.

Gate yang direncanakan:

```text
input-nilai
```

Tujuan:

Memastikan hanya user dengan role `dosen` atau `asdos` yang dapat melakukan aksi input nilai.

Logic:

```text
user.role == dosen
        OR
user.role == asdos
```

Controller harus menggunakan Gate untuk authorization aksi.

Contoh konsep:

```php
Gate::authorize('input-nilai');
```

---

# 18. Blade UI Protection

UI harus menggunakan Blade authorization directive.

Contoh penggunaan:

```blade
@can('input-nilai')
    <a href="{{ route('admin.grades.create') }}">
        Input Nilai
    </a>
@endcan
```

Tujuan:

User hanya melihat tombol yang memang dapat digunakan.

Penting:

**Blade `@can` hanya merupakan UI protection.**

Authorization backend tetap harus dilakukan melalui Gate/Policy sehingga user tidak dapat melewati keamanan hanya dengan mengetik URL secara manual.

---

# 19. Rate Limiting

Challenge A+:

Mahasiswa hanya dapat melakukan submission proposal maksimal:

```text
3 request / 1 menit
```

Rate limiter diterapkan pada endpoint submission proposal.

Contoh flow:

```text
POST /proposals
      │
      ▼
Rate Limiter
      │
      ├── Request 1 ✓
      ├── Request 2 ✓
      ├── Request 3 ✓
      └── Request 4 ✗
             │
             ▼
        Too Many Requests
```

Rate limiting hanya berlaku pada endpoint submission proposal.

---

# 20. Local Auto-Login

Challenge A+:

Auto-login hanya aktif ketika environment:

```text
local
```

Endpoint:

```text
/login/as/mahasiswa
/login/as/asdos
/login/as/dosen
```

Flow:

```text
/login/as/dosen
        │
        ▼
Cari user dengan role dosen
        │
        ▼
Auth::login()
        │
        ▼
/dashboard atau /admin
```

Feature tidak boleh aktif pada production environment.

Akun demo dibuat menggunakan Seeder.

---

# 21. Seeder

Database Seeder harus menyediakan minimal tiga akun untuk testing.

```text
Mahasiswa
email: mahasiswa@example.com

Asdos
email: asdos@example.com

Dosen
email: dosen@example.com
```

Password demo menggunakan password yang konsisten untuk kebutuhan development.

Seeder juga dapat menyediakan beberapa data proposal dan nilai untuk mempermudah demo.

---

# 22. Routes

Rancangan route:

```text
/
```

Public landing page.

```text
/dashboard
```

Dashboard authenticated user.

```text
/proposals
```

Daftar proposal.

```text
/proposals/create
```

Form membuat proposal.

```text
/proposals/{proposal}/edit
```

Form edit proposal.

```text
/admin
```

Admin dashboard.

```text
/admin/proposals
```

Daftar proposal untuk dosen/asdos.

```text
/admin/grades
```

Daftar nilai.

```text
/admin/grades/create
```

Form input nilai.

Auto-login local:

```text
/login/as/{role}
```

---

# 23. Route Protection

Route student:

```text
auth
```

Route admin:

```text
auth
cek.peran
```

Contoh struktur:

```text
Route::middleware('auth')->group(function () {

    // Student / authenticated routes

});

Route::middleware(['auth', 'cek.peran'])->prefix('admin')->group(function () {

    // Admin routes

});
```

Policy digunakan pada resource proposal.

Gate digunakan pada aksi input nilai.

---

# 24. Validation

Proposal:

```text
title
- required
- string
- max:255

description
- required
- string
```

Grade:

```text
student_id
- required
- exists:users,id

course
- required
- string

score
- required
- numeric
- min:0
- max:100

feedback
- nullable
- string
```

Validation harus dilakukan di server-side.

---

# 25. Authorization Rules

Authorization harus dilakukan di backend.

Aturan:

### Mahasiswa

* Tidak boleh mengakses `/admin`.
* Hanya dapat mengedit proposal sendiri.
* Tidak dapat input nilai.

### Asdos

* Dapat mengakses `/admin`.
* Dapat melihat proposal.
* Dapat input nilai.
* Tidak dapat mengedit proposal sebagai pemilik.

### Dosen

* Dapat mengakses `/admin`.
* Dapat melihat proposal.
* Dapat input nilai.
* Tidak dapat mengedit proposal mahasiswa melalui Policy.

### Guest

* Hanya dapat mengakses public area dan authentication pages.
* Tidak dapat mengakses dashboard.
* Tidak dapat mengakses admin.

---

# 26. Error Handling

Untuk akses yang ditolak:

* Guest → redirect ke login.
* User tanpa permission → HTTP 403 atau halaman access denied.
* Rate limit → HTTP 429.
* Resource tidak ditemukan → HTTP 404.

Pesan error harus jelas tetapi tidak membocorkan informasi sensitif.

---

# 27. UI Requirements

UI tidak menjadi fokus utama penilaian.

Prioritas:

1. Clean.
2. Responsive.
3. Mudah didemokan.
4. Menunjukkan perbedaan area berdasarkan role.
5. Tombol yang tidak memiliki permission disembunyikan dengan `@can`.

Struktur navigasi dapat berubah berdasarkan role.

Contoh:

```text
Guest
├── Home
├── Login
└── Register

Mahasiswa
├── Dashboard
├── My Proposals
└── Logout

Asdos / Dosen
├── Dashboard
├── Admin
├── Proposals
├── Grades
└── Logout
```

---

# 28. Security Requirements

Project harus menerapkan:

* Laravel Breeze authentication.
* Password hashing.
* CSRF protection.
* Server-side validation.
* Authentication middleware.
* Custom role middleware.
* Gate authorization.
* Policy authorization.
* Blade authorization.
* Rate limiting.
* Local-only auto-login.

Tidak boleh mengandalkan hidden button sebagai satu-satunya sistem keamanan.

---

# 29. Project Structure

Target struktur:

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── AdminController.php
│   │   ├── ProposalController.php
│   │   └── GradeController.php
│   │
│   └── Middleware/
│       └── CekPeran.php
│
├── Models/
│   ├── User.php
│   ├── ProjectProposal.php
│   └── Grade.php
│
└── Policies/
    └── ProjectProposalPolicy.php

database/
├── migrations/
│   ├── create_users_table.php
│   ├── add_role_to_users_table.php
│   ├── create_project_proposals_table.php
│   └── create_grades_table.php
│
└── seeders/
    └── DatabaseSeeder.php

resources/
└── views/
    ├── auth/
    ├── layouts/
    ├── dashboard.blade.php
    ├── proposals/
    └── admin/

routes/
└── web.php
```

---

# 30. Git Strategy

Database SQLite tidak disimpan ke repository.

Yang disimpan:

```text
database/
├── migrations/
└── seeders/
```

File database lokal:

```text
database/database.sqlite
```

harus masuk `.gitignore`.

Project harus dapat direproduksi dengan:

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

touch database/database.sqlite

php artisan migrate --seed

npm run dev
php artisan serve
```

---

# 31. Testing Plan

## Authentication

* [ ] Register mahasiswa berhasil.
* [ ] Login berhasil.
* [ ] Logout berhasil.
* [ ] Password tidak disimpan plaintext.

## Middleware

* [ ] Guest tidak dapat mengakses `/admin`.
* [ ] Mahasiswa tidak dapat mengakses `/admin`.
* [ ] Asdos dapat mengakses `/admin`.
* [ ] Dosen dapat mengakses `/admin`.

## Policy

* [ ] Mahasiswa dapat mengedit proposal sendiri.
* [ ] Mahasiswa tidak dapat mengedit proposal mahasiswa lain.
* [ ] User tanpa permission mendapatkan 403.

## Gate

* [ ] Dosen dapat input nilai.
* [ ] Asdos dapat input nilai.
* [ ] Mahasiswa tidak dapat input nilai.
* [ ] Tombol input nilai tersembunyi untuk mahasiswa.
* [ ] URL input nilai tetap terlindungi di backend.

## Rate Limiting

* [ ] Submission pertama berhasil.
* [ ] Submission kedua berhasil.
* [ ] Submission ketiga berhasil.
* [ ] Submission keempat dalam satu menit ditolak.

## Auto Login

* [ ] `/login/as/mahasiswa` berhasil pada local.
* [ ] `/login/as/asdos` berhasil pada local.
* [ ] `/login/as/dosen` berhasil pada local.
* [ ] Feature tidak aktif di production.

---

# 32. Demo Scenario

Demo harus memperlihatkan tiga role.

## Scenario 1 - Mahasiswa

1. Login sebagai mahasiswa.
2. Masuk dashboard.
3. Membuat proposal.
4. Edit proposal sendiri.
5. Coba akses `/admin`.
6. Tunjukkan bahwa akses ditolak.
7. Tunjukkan tombol input nilai tidak tersedia.

## Scenario 2 - Asdos

1. Login sebagai asdos.
2. Masuk `/admin`.
3. Melihat proposal mahasiswa.
4. Membuka halaman input nilai.
5. Menginput nilai.

## Scenario 3 - Dosen

1. Login sebagai dosen.
2. Masuk `/admin`.
3. Melihat proposal.
4. Input nilai.
5. Tunjukkan role-based access.

## Scenario 4 - Policy

1. Login sebagai mahasiswa A.
2. Buka proposal mahasiswa A.
3. Edit berhasil.
4. Coba membuka proposal mahasiswa B.
5. Edit ditolak oleh Policy.

## Scenario 5 - Rate Limiting

1. Submit proposal beberapa kali.
2. Tiga request pertama berhasil.
3. Request keempat dalam satu menit mendapatkan HTTP 429.

---

# 33. Rubric Mapping

| Rubric                            | Implementasi                                    |
| --------------------------------- | ----------------------------------------------- |
| Breeze & Migration - 30%          | Laravel Breeze + migration `role`               |
| Custom Middleware & Routing - 25% | `CekPeran` + alias `cek.peran` + `/admin`       |
| Gates & Policies - 25%            | Gate `input-nilai` + `ProjectProposalPolicy`    |
| Challenge A+ - 10%                | Rate Limiting + Auto-login                      |
| Code Quality - 10%                | Separation of concerns, PSR-12, clean structure |

Target:

```text
Authentication
       ↓
Role
       ↓
Middleware
       ↓
Gate
       ↓
Policy
       ↓
Blade @can
       ↓
Rate Limiting
       ↓
Auto Login
```

---

# 34. Definition of Done

Project dianggap selesai apabila:

* [ ] Laravel Breeze terpasang dan berfungsi.
* [ ] Register berfungsi.
* [ ] Login berfungsi.
* [ ] Logout berfungsi.
* [ ] Kolom `role` ditambahkan menggunakan migration.
* [ ] Tiga role tersedia.
* [ ] Custom middleware `CekPeran` tersedia.
* [ ] Alias `cek.peran` terdaftar.
* [ ] `/admin` hanya dapat diakses dosen/asdos.
* [ ] Project Proposal memiliki Policy.
* [ ] Mahasiswa hanya dapat mengedit proposal sendiri.
* [ ] Global Gate tersedia.
* [ ] Gate digunakan untuk input nilai.
* [ ] Blade menggunakan `@can`.
* [ ] Rate limiting proposal tersedia.
* [ ] Auto-login local tersedia.
* [ ] Seeder menyediakan akun demo.
* [ ] Database dapat dibuat ulang menggunakan migration dan seeder.
* [ ] SQLite tidak di-commit ke Git.
* [ ] Seluruh skenario demo berhasil.
* [ ] Kode mengikuti struktur Laravel dan PSR-12.

---

# 35. Out of Scope

Fitur berikut tidak diperlukan untuk tugas ini:

* API REST.
* Mobile application.
* Notification system.
* Email verification custom.
* File upload proposal.
* Chat.
* Real-time notification.
* Complex academic curriculum management.
* Payment system.
* Third-party role/permission package.
* Deployment production.

Fokus project tetap pada **authentication dan authorization Laravel**.