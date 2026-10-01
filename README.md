# Secure Academic Portal

Portal akademik sederhana berbasis **Laravel 13** yang berfokus pada penerapan **authentication** dan **authorization multi-peran** secara lengkap: Laravel Breeze, custom role, custom middleware, Global Gate, Model Policy, Blade `@can`, rate limiting, dan auto-login khusus local development.

Project ini adalah tugas kuliah individu (PBKK) dan merupakan pengembangan dari project Laravel sebelumnya. Sumber kebutuhan lengkap ada di [`docs/PRD.md`](docs/PRD.md).

---

## Daftar Isi

1. [Ringkasan](#ringkasan)
2. [Fitur Utama](#fitur-utama)
3. [Tech Stack](#tech-stack)
4. [Kebutuhan Sistem (Prerequisites)](#kebutuhan-system-prerequisites)
5. [Instalasi](#instalasi)
6. [Menjalankan di Local](#menjalankan-di-local)
7. [Akun Demo](#akun-demo)
8. [Struktur Project](#struktur-project)
9. [Role dan Permission](#role-dan-permission)
10. [Implementasi Authorization](#implementasi-authorization)
11. [Database](#database)
12. [Routes](#routes)
13. [Testing](#testing)
14. [Command yang Tersedia](#command-yang-tersedia)
15. [Catatan Deviasi dari PRD](#catatan-deviasi-dari-prd)

---

## Ringkasan

| Aspek | Detail |
| --- | --- |
| Nama Aplikasi | Secure Academic Portal |
| Framework | Laravel 13 (PHP 8.3+) |
| Autentikasi | Laravel Breeze (Blade + session) |
| Database | SQLite |
| Role | `mahasiswa`, `asdos`, `dosen` |
| Objek Domain | `ProjectProposal` (proposal rencana proyek AI) |
| Fokus | Perbedaan antara **authentication** (siapa kamu) dan **authorization** (apa yang boleh kamu lakukan) |

**Use case utama:** mahasiswa membuat proposal rencana proyek AI dan mengirimkannya untuk ditinjau; dosen/asdos meninjau proposal, memberi nilai + feedback, dan statusnya berubah menjadi `reviewed`. Mahasiswa yang mencoba mengedit proposal milik mahasiswa lain ditolak oleh **Policy**, sedangkan akses ke area admin dijaga oleh **middleware** dan **Gate**.

---

## Fitur Utama

### 1. Authentication (Laravel Breeze)

- Register (default role `mahasiswa`)
- Login / Logout
- Email verification
- Password reset & konfirmasi password
- Update password & hapus akun (profile)

### 2. Role Management

- Kolom `role` ditambahkan ke tabel `users` melalui **custom migration** (bukan mengubah migration bawaan).
- Role direpresentasikan sebagai **PHP Enum** (`App\Enums\UserRole`) dan di-cast otomatis oleh Eloquent.

### 3. Authorization (4 lapis)

| Lapis | Mechanisme | Masalah yang diselesaikan |
| --- | --- | --- |
| **Middleware** | `cek.peran` | "Area ini hanya untuk siapa?" → batasi route `/admin` |
| **Gate** | `input-nilai` | "Aksi apa yang boleh?" → validasi aksi input nilai/review di backend |
| **Policy** | `ProjectProposalPolicy` | "Resource ini boleh diakses siapa?" → edit/submit hanya pemilik proposal |
| **Blade `@can`** | Directive Blade | "Tampilkan apa yang boleh dilihat?" → sembunyikan tombol yang tidak relevan (UI protection) |

### 4. Rate Limiting (Challenge A+)

- Endpoint submission proposal dibatasi **3 request / 1 menit** per user.
- Request ke-4 dan seterusnya dijawab **HTTP 429 Too Many Requests**.

### 5. Auto-Login Demo (Challenge A+)

- Endpoint `/login/as/{role}` hanya aktif pada environment `local`.
- Di luar `local` (mis. production) endpoint selalu **404**.

### 6. Seeder Demo

- 4 akun demo + 5 proposal contoh (berbagai status: draft, submitted, reviewed) untuk memudahkan demo.
- Seeder bersifat **idempotent** (`firstOrCreate`) sehingga aman dijalankan berulang kali.

---

## Tech Stack

### Backend

| Teknologi | Versi | Keterangan |
| --- | --- | --- |
| PHP | `^8.3` | Minimal requirement (Composer) |
| Laravel Framework | `^13.17` (terpasang 13.34.0) | Framework utama |
| Laravel Breeze | `^2.4` | Scaffolding autentikasi (dev dependency) |
| Laravel Pint | `^1.27` | PSR-12 code formatter |
| Laravel Tinker | `^3.0` | REPL |
| PHPUnit | `^12.5` | Unit & feature testing |
| Faker | `^1.23` | Data factory |
| Mockery / Collision | `^1.6` / `^8.6` | Mocking & output test |
| Laravel Boost | `^2.10` | Guideline + skill untuk AI agent |
| Laravel Pao | `^1.0.6` | `php artisan dev` (multi-process dev server) |

### Frontend

| Teknologi | Versi | Keterangan |
| --- | --- | --- |
| Blade | (bawaan Laravel) | Semua view |
| Tailwind CSS | `^3.1` | Styling, dark mode |
| `@tailwindcss/forms` | `^0.5.2` | Plugin form styling |
| Vite | `^8.0` | Bundler & dev server |
| `laravel-vite-plugin` | `^3.1` | Integrasi Laravel ↔ Vite |
| Alpine.js | `^3.4` | Interaksi dropdown/hamburger menu |
| Autoprefixer | `^10.4` | CSS prefix |
| concurrently | `^10.0` | Menjalankan beberapa proses dev bersamaan |

> **Catatan:** `package.json` juga mencantumkan `@tailwindcss/vite ^4.0.0`, namun konfigurasi aktif di `postcss.config.js` memakai pipeline **Tailwind v3** + `@tailwind` directives. Jangan memindahkan config ke v4 tanpa menyesuaikan `resources/css/app.css`.

### Database

- **SQLite** (file: `database/database.sqlite`, tidak di-commit ke Git)
- Driver lain yang tersedia di `.env.example` (mysql/pgsql) tetap terdefinisi di `config/database.php`, namun seluruh dokumentasi dan pengujian proyek ini memakai SQLite.

### Development Tools

- Composer, npm, Git, Laravel Pint, PHPUnit, Laravel Pail, Laravel Boost

### Package Permission

Sengaja **tidak** memakai package pihak ketiga seperti Spatie Permission — seluruh mekanisme role/Gate/Policy diimplementasikan custom sesuai requirement tugas.

---

## Kebutuhan Sistem (Prerequisites)

| Tool | Minimum / Versi |
| --- | --- |
| PHP | 8.3 atau lebih baru (dengan ekstensi `pdo_sqlite`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcrypt`) |
| Composer | 2.x |
| Node.js | 20+ (Laravel 13 + Vite 8) |
| npm | 10+ |
| SQLite3 | untuk inspeksi DB secara manual (opsional) |

Cek ketersediaan:

```bash
php -v
composer -V
node -v
npm -v
```

---

## Instalasi

### Cara A — Satu Perintah (paling cepat)

```bash
composer run setup
```

Script `setup` (lihat `composer.json`) menjalankan: `composer install` → copy `.env.example` ke `.env` → `php artisan key:generate` → `php artisan migrate --force` → `npm install` → `npm run build`.

> Jika `composer run setup` tidak menyiapkan file SQLite, jalankan `touch database/database.sqlite` secara manual (lihat Cara B).

### Cara B — Manual (langkah demi langkah)

```bash
# 1. Install dependency PHP
composer install

# 2. Siapkan environment file
cp .env.example .env

# 3. Generate application key
php artisan key:generate

# 4. Buat file database SQLite
touch database/database.sqlite

# 5. Jalankan migration + seeder demo
php artisan migrate --seed

# 6. Install dependency JavaScript & build asset
npm install
npm run build
```

### Verifikasi Instalasi

```bash
php artisan about     # versi Laravel, environment, driver DB, cache, dsb.
php artisan route:list
php artisan test
```

### Reset Database dari Nol

```bash
php artisan migrate:fresh --seed
```

---

## Menjalankan di Local

### Development (two terminal)

```bash
# Terminal 1 — Vite dev server (hot reload, Tailwind)
npm run dev

# Terminal 2 — PHP built-in server
php artisan serve
```

Buka **http://localhost:8000**.

### Development (satu perintah, multi-process)

Project ini membawa `laravel/pao` yang menyediakan `php artisan dev` untuk menjalankan Vite + server + log viewer sekaligus:

```bash
composer run dev
# atau
php artisan dev
```

Cek daftar proses terdaftar:

```bash
php artisan dev:list
```

### Production Build (untuk diuji lokal)

```bash
npm run build      # bundle asset ke public/build
php artisan serve
```

### Troubleshooting

| Gejala | Penyebab & Solusi |
| --- | --- |
| `Unable to locate file in Vite manifest` | Asset belum di-build → jalankan `npm run build` atau `npm run dev` |
| Halaman tanpa styling | `npm run dev` belum jalan / asset belum di-build |
| `database/database.sqlite` not found | Jalankan `touch database/database.sqlite` lalu `php artisan migrate --seed` |
| `APP_KEY` kosong / error enkripsi | Jalankan `php artisan key:generate` |
| 500 pada session | Jalankan `php artisan migrate` (SESSION_DRIVER=database, CACHE_STORE=database) |

---

## Akun Demo

Dibuat otomatis oleh `DatabaseSeeder` (`php artisan db:seed`). Seeder idempotent, jadi aman dijalankan berulang kali.

| Role | Email | Password |
| --- | --- | --- |
| Mahasiswa | `mahasiswa@example.com` | `password` |
| Mahasiswa (kedua, untuk demo Policy) | `mahasiswa2@example.com` | `password` |
| Asisten Dosen | `asdos@example.com` | `password` |
| Dosen | `dosen@example.com` | `password` |

### Auto-Login (hanya environment `local`)

Tidak perlu mengetik password saat demo:

| URL | Hasil |
| --- | --- |
| `/login/as/mahasiswa` | Login sebagai mahasiswa pertama → redirect `/dashboard` |
| `/login/as/asdos` | Login sebagai asdos pertama → redirect `/admin` |
| `/login/as/dosen` | Login sebagai dosen pertama → redirect `/admin` |

Data proposal demo dari seeder:

| Judul | Pemilik | Status | Nilai |
| --- | --- | --- | --- |
| Deteksi Penyakit Daun Padi Menggunakan CNN | Mahasiswa Demo | `draft` | – |
| Chatbot Layanan Akademik Berbasis NLP | Mahasiswa Demo | `submitted` | – |
| Sistem Rekomendasi Mata Kuliah Pilihan | Mahasiswa Demo | `reviewed` | 88.5 |
| Prediksi Harga Pangan dengan Regresi | Mahasiswa Lain | `submitted` | – |
| Klasifikasi Sentimen Ulasan Aplikasi | Mahasiswa Lain | `reviewed` | 82 |

---

## Struktur Project

```text
app/
├── Enums/
│   ├── ProposalStatus.php          # draft | submitted | revised | reviewed
│   └── UserRole.php                # mahasiswa | asdos | dosen (+ label(), isStaff())
├── Http/
│   ├── Controllers/
│   │   ├── AdminController.php     # Panel admin, review proposal (input nilai)
│   │   ├── ProfileController.php
│   │   ├── ProposalController.php  # CRUD proposal + submit (student area)
│   │   └── Auth/                   # Breeze + AutoLoginController
│   ├── Middleware/
│   │   └── CekPeran.php            # Custom middleware, alias: cek.peran
│   └── Requests/
│       └── ProjectProposalRequest.php  # Form request: title, description
├── Models/
│   ├── ProjectProposal.php
│   └── User.php
├── Policies/
│   └── ProjectProposalPolicy.php   # update, submit
├── Providers/
│   └── AppServiceProvider.php      # Gate input-nilai + RateLimiter proposals
└── View/Components/                # AppLayout, GuestLayout

bootstrap/app.php                   # Registrasi alias middleware cek.peran

database/
├── factories/                      # UserFactory (state: mahasiswa/asdos/dosen), ProjectProposalFactory
├── migrations/
│   ├── 0001_01_01_000000_create_users_table.php
│   ├── 0001_01_01_000001_create_cache_table.php
│   ├── 0001_01_01_000002_create_jobs_table.php
│   ├── 2026_09_29_162936_add_role_to_users_table.php
│   └── 2026_09_29_162937_create_project_proposals_table.php
└── seeders/DatabaseSeeder.php      # 4 akun demo + 5 proposal

resources/views/
├── layouts/                        # app.blade.php, guest.blade.php, navigation.blade.php
├── components/                     # x-input-error, x-proposal-status, x-primary-button, dll
├── auth/                           # login, register, forgot-password, reset-password, verify-email, confirm-password
├── profile/                        # edit + partials (update profile, password, delete account)
├── proposals/                      # index, create, edit, _form
├── admin/                          # index, proposals/index, proposals/show
├── dashboard.blade.php
└── welcome.blade.php               # Public landing page

routes/
├── web.php                         # Public, dashboard, student area, admin area, profile
├── auth.php                        # Breeze auth routes + /login/as/{role}
└── console.php

tests/
├── Feature/                        # AdminAccess, AdminProposalReview, Proposal, AutoLogin,
│                                   # InputNilaiGate, DatabaseSeeder, Profile, Auth/*
└── Unit/Policies/ProjectProposalPolicyTest.php

docs/PRD.md                         # Product Requirements Document
```

---

## Role dan Permission

| Fitur | Guest | Mahasiswa | Asdos | Dosen |
| --- | :---: | :---: | :---: | :---: |
| Landing Page `/` | ✓ | ✓ | ✓ | ✓ |
| Register | ✓ | – | – | – |
| Login | ✓ | ✓ | ✓ | ✓ |
| Dashboard `/dashboard` | – | ✓ | ✓ | ✓ |
| Daftar & buat proposal | – | ✓ | ✗ | ✗ |
| Edit proposal sendiri | – | ✓ | ✗ | ✗ |
| Edit proposal user lain | – | ✗ | ✗ | ✗ |
| Submit proposal | – | ✓ | ✗ | ✗ |
| Akses `/admin` | ✗ | ✗ | ✓ | ✓ |
| Lihat proposal | ✗ | Miliknya sendiri | ✓ | ✓ |
| Lihat proposal `draft` | ✗ | Miliknya sendiri | ✗ (404) | ✗ (404) |
| Input nilai / review | ✗ | ✗ | ✓ | ✓ |

Status response yang digunakan:

| Kondisi | Response |
| --- | --- |
| Guest mengakses area terproteksi | Redirect ke `/login` |
| Role tidak sesuai middleware | `403 Forbidden` |
| Resource milik orang lain pada aksi Policy | `403 Forbidden` |
| Resource `draft` dibuka dari admin | `404 Not Found` |
| Melebihi rate limit | `429 Too Many Requests` |
| Auto-login di luar `local` | `404 Not Found` |

---

## Implementasi Authorization

### 1. Role sebagai Enum

`app/Enums/UserRole.php`

```php
enum UserRole: string
{
    case Mahasiswa = 'mahasiswa';
    case Asdos = 'asdos';
    case Dosen = 'dosen';

    public function label(): string { /* Mahasiswa / Asisten Dosen / Dosen */ }
    public function isStaff(): bool { /* true untuk asdos & dosen */ }
    public static function values(): array { /* semua value */ }
    public static function staffValues(): array { /* asdos, dosen */ }
}
```

`User` melakukan cast `'role' => UserRole::class` (app/Models/User.php:30), sehingga pemanggilan `role` langsung menghasilkan enum dan tidak perlu validasi string manual.

### 2. Custom Middleware `CekPeran`

Alias `cek.peran` didaftarkan di `bootstrap/app.php`:

```php
$middleware->alias([
    'cek.peran' => CekPeran::class,
]);
```

Implementasi `app/Http/Middleware/CekPeran.php`:

```php
public function handle(Request $request, Closure $next, string ...$roles): Response
{
    if (! $request->user()) {
        return redirect()->route('login');          // Guest → login
    }

    $allowedRoles = $roles === [] ? UserRole::staffValues() : $roles;

    abort_unless(in_array($request->user()->role->value, $allowedRoles, true), 403);

    return $next($request);
}
```

Karena menerima variadic parameter, middleware bisa dipakai dua cara:

```php
// Default: hanya staff (asdos, dosen) — app/Http/Middleware/CekPeran.php:23
Route::middleware(['auth', 'cek.peran'])->prefix('admin')->...;

// Eksplisit: hanya mahasiswa — routes/web.php:24
Route::middleware(['auth', 'cek.peran:mahasiswa'])->group(...);
```

### 3. Global Gate `input-nilai`

`app/Providers/AppServiceProvider.php:27`

```php
Gate::define('input-nilai', fn (User $user): bool => $user->isStaff());
```

Dipakai di backend pada aksi review/input nilai (`app/Http/Controllers/AdminController.php:87`):

```php
Gate::authorize('input-nilai');
```

### 4. Model Policy `ProjectProposalPolicy`

Laravel melakukan **auto-discovery** policy: `App\Models\ProjectProposal` → `App\Policies\ProjectProposalPolicy` (tidak perlu registrasi manual).

```php
class ProjectProposalPolicy
{
    public function update(User $user, ProjectProposal $projectProposal): bool
    {
        return $projectProposal->isOwnedBy($user);          // hanya pemilik
    }

    public function submit(User $user, ProjectProposal $projectProposal): bool
    {
        return $projectProposal->isOwnedBy($user)
            && $projectProposal->status === ProposalStatus::Draft;
    }
}
```

Dipakai di controller **sebelum** aksi dijalankan:

| Aksi | Lokasi |
| --- | --- |
| Tampilkan form edit | `app/Http/Controllers/ProposalController.php:52` |
| Simpan perubahan | `app/Http/Controllers/ProposalController.php:62` |
| Submit proposal | `app/Http/Controllers/ProposalController.php:83` |

`isOwnedBy()` membandingkan `user_id` proposal dengan `id` user — inilah contoh klasik authorization berbasis kepemilikan resource, bukan sekadar pengecekan role.

### 5. Blade Authorization (`@can`)

`@can` hanya **UI protection** — keamanan sesungguhnya tetap di backend (Gate/Policy).

| Lokasi | Guard |
| --- | --- |
| `resources/views/layouts/navigation.blade.php:25` & `:99` | Menu "Admin" & "Review Proposal" hanya tampil bila `@can('input-nilai')` |
| `resources/views/dashboard.blade.php:61` & `:67` | Tombol Edit / Submit per proposal (`@can('update'/'submit', $proposal)`) |
| `resources/views/dashboard.blade.php:99` | Link ke panel admin |
| `resources/views/proposals/index.blade.php:58` & `:64` | Tombol Edit / Submit |
| `resources/views/admin/proposals/show.blade.php:84` | Form review/input nilai |

Navigasi desktop maupun mobile memakai guard yang sama, dan role juga ditampilkan pada dropdown user (`role->label()`).

### 6. Rate Limiting

`app/Providers/AppServiceProvider.php:29`

```php
RateLimiter::for('proposals', fn (Request $request): Limit => Limit::perMinute(3)
    ->by($request->user()?->id ?: $request->ip()));
```

Diterapkan hanya pada endpoint submission (`routes/web.php:29`):

```php
Route::post('proposals', [ProposalController::class, 'store'])
    ->middleware('throttle:proposals')
    ->name('proposals.store');
```

Dibatasi **per user id** (fallback IP bila guest), sehingga mahasiswa lain tidak ikut terkena limit.

### 7. Auto-Login Local

`app/Http/Controllers/Auth/AutoLoginController.php`

```php
public function __invoke(Request $request, string $role): RedirectResponse
{
    abort_unless(app()->environment('local'), 404);   // nonaktif di production
    $userRole = UserRole::tryFrom($role);
    abort_if($userRole === null, 404);
    $user = User::where('role', $userRole)->first();
    abort_if($user === null, 404);

    Auth::login($user);
    $request->session()->regenerate();               // anti session fixation

    return redirect()->route($userRole->isStaff() ? 'admin.dashboard' : 'dashboard');
}
```

Route berada di `routes/auth.php:40` (di luar grup `guest` agar bisa dipakai saat sudah login):

```php
Route::get('login/as/{role}', AutoLoginController::class)->name('auto-login');
```

### 8. Form Request & Validasi Server-Side

`app/Http/Requests/ProjectProposalRequest.php`

```php
'title'       => ['required', 'string', 'max:255'],
'description' => ['required', 'string'],
```

Review di admin (`AdminController::reviewProposal`):

```php
'score'    => ['required', 'numeric', 'min:0', 'max:100'],
'feedback' => ['nullable', 'string', 'max:1000'],
```

`ProjectProposalRequest::authorize()` mengembalikan `true` dengan sengaja — otorisasi tetap dilakukan eksplisit lewat Policy di controller agar tidak membingungkan antara "validasi" dan "otorisasi".

### 9. Lapisan keamanan lain

- Password di-hash (`'password' => 'hashed'` cast pada `User`)
- CSRF token pada seluruh form (`@csrf`, `@method('PATCH'|'DELETE')`)
- Session ID di-regenerate saat login
- Query binding pada route model binding (`proposals/{proposal}`) mencegah SQL injection
- Role pada form register dipaksa `UserRole::Mahasiswa` (user tidak bisa mendaftarkan diri sebagai dosen)
- Draft proposal tidak terlihat oleh admin (`abort_if(..., 404)`), mencegah kebocoran data yang belum dikirim

---

## Database

### `users`

```text
id              PK
name
email           unique
password        hashed
role            string, default 'mahasiswa'   -- mahasiswa | asdos | dosen
email_verified_at  nullable
remember_token
created_at / updated_at
```

Kolom `role` ditambahkan lewat migration terpisah: `database/migrations/2026_09_29_162936_add_role_to_users_table.php`.

### `project_proposals`

```text
id            PK
user_id       FK → users.id   (cascade on delete)   -- pembuat / mahasiswa
title         string
description   text
status        string, default 'draft'   -- draft | submitted | revised | reviewed
score         decimal(5,2) nullable      -- nilai dari dosen/asdos
feedback      text nullable
grader_id     FK → users.id nullable (null on delete)  -- dosen/asdos penilai
reviewed_at   timestamp nullable
created_at / updated_at
INDEX(status)
```

> Implementasi terbaru menggabungkan konsep tabel `grades` (pada PRD awal) ke dalam `project_proposals` — satu proposal satu nilai. Migration `create_grades_table` yang direncanakan PRD tidak dibuat karena tidak ada route/controller `GradeController` di implementasi sekarang.

### Relasi

```text
User (1) ──hasMany──▶ (N) ProjectProposal   as proposal owner
User (1) ──belongsTo─◀ (N) ProjectProposal   as reviewer (grader_id)
```

Pada model:

- `User::proposals(): HasMany` (app/Models/User.php:44)
- `ProjectProposal::user(): BelongsTo` (app/Models/ProjectProposal.php:47)
- `ProjectProposal::grader(): BelongsTo` (app/Models/ProjectProposal.php:56)

Eager loading dipakai di area admin (`with('user')`, `load(['user', 'grader'])`) untuk menghindari N+1.

### Siklus status proposal

```text
draft ──submit (mahasiswa, via Policy 'submit')──▶ submitted
submitted ──review (staff, via Gate 'input-nilai')──▶ reviewed
reviewed ──edit oleh mahasiswa──▶ revised  ──submit──▶ submitted
```

Perubahan `reviewed → revised` terjadi otomatis di `ProposalController::update()` bila mahasiswa mengedit proposal yang sudah direview.

---

## Routes

### Public

| Method | URI | Nama | Middleware |
| --- | --- | --- | --- |
| GET | `/` | `home` | – |
| GET | `/up` | – | health check |

### Authentication (Laravel Breeze)

| Method | URI | Nama |
| --- | --- | --- |
| GET/POST | `/register` | `register` |
| GET/POST | `/login` | `login` |
| POST | `/logout` | `logout` |
| GET/POST | `/forgot-password` | `password.request`, `password.email` |
| GET/POST | `/reset-password/{token}` | `password.reset`, `password.store` |
| GET/POST | `/confirm-password` | `password.confirm`, `password.store` |
| GET | `/verify-email` | `verification.notice` |
| GET | `/verify-email/{id}/{hash}` | `verification.verify` |
| POST | `/email/verification-notification` | `verification.send` |
| GET | `/login/as/{role}` | `auto-login` (khusus `local`) |

### Student Area

| Method | URI | Nama | Middleware |
| --- | --- | --- | --- |
| GET | `/dashboard` | `dashboard` | `auth` |
| GET | `/proposals` | `proposals.index` | `auth`, `cek.peran:mahasiswa` |
| GET | `/proposals/create` | `proposals.create` | `auth`, `cek.peran:mahasiswa` |
| GET | `/proposals/{proposal}/edit` | `proposals.edit` | `auth`, `cek.peran:mahasiswa`, **Policy** |
| POST | `/proposals` | `proposals.store` | `auth`, `cek.peran:mahasiswa`, **`throttle:proposals`** |
| PATCH | `/proposals/{proposal}` | `proposals.update` | `auth`, `cek.peran:mahasiswa`, **Policy** |
| PATCH | `/proposals/{proposal}/submit` | `proposals.submit` | `auth`, `cek.peran:mahasiswa`, **Policy** |
| GET/PATCH/DELETE | `/profile` | `profile.*` | `auth` |

### Admin Area (prefix `/admin`, nama diawali `admin.`)

| Method | URI | Nama | Middleware |
| --- | --- | --- | --- |
| GET | `/admin` | `admin.dashboard` | `auth`, `cek.peran` |
| GET | `/admin/proposals` | `admin.proposals.index` | `auth`, `cek.peran` |
| GET | `/admin/proposals/{proposal}` | `admin.proposals.show` | `auth`, `cek.peran` |
| PATCH | `/admin/proposals/{proposal}/review` | `admin.proposals.review` | `auth`, `cek.peran`, **Gate `input-nilai`** |

Lihat daftar lengkap dan terkini dengan:

```bash
php artisan route:list
php artisan route:list --except-vendor
```

---

## Testing

Test suite memakai **PHPUnit 12** dengan `RefreshDatabase` dan database SQLite in-memory (`:memory:`), sehingga tidak menyentuh `database/database.sqlite`.

### Menjalankan test

```bash
php artisan test                        # semua test
php artisan test --compact              # output ringkas
php artisan test tests/Feature/ProposalTest.php
php artisan test --filter=test_guest_is_redirected_to_login
vendor/bin/phpunit                      # langsung ke runner
```

Status saat ini: **81 test, 81 passed, 205 assertions**.

### Cakupan test

| File Test | Fokus |
| --- | --- |
| `tests/Feature/Auth/AuthenticationTest.php` | Login, logout, password tidak plaintext |
| `tests/Feature/Auth/RegistrationTest.php` | Register mahasiswa |
| `tests/Feature/Auth/EmailVerificationTest.php` | Verifikasi email |
| `tests/Feature/Auth/PasswordConfirmationTest.php` | Konfirmasi password |
| `tests/Feature/Auth/PasswordResetTest.php` | Reset password |
| `tests/Feature/Auth/PasswordUpdateTest.php` | Update password |
| `tests/Feature/AdminAccessTest.php` | Guest → login, mahasiswa → 403, asdos/dosen → 200 |
| `tests/Feature/AdminProposalReviewTest.php` | Review proposal, input nilai, validasi, draft 404 |
| `tests/Feature/ProposalTest.php` | CRUD proposal, Policy, submit, **rate limit request ke-4 ditolak** |
| `tests/Feature/InputNilaiGateTest.php` | Gate `input-nilai` per role + visibilitas tombol UI |
| `tests/Feature/AutoLoginTest.php` | Auto-login 3 role, 404 di luar `local`, 404 role tak dikenal |
| `tests/Feature/DatabaseSeederTest.php` | Akun demo & proposal demo |
| `tests/Feature/ProfileTest.php` | Edit profil, ganti & hapus password, hapus akun |
| `tests/Unit/Policies/ProjectProposalPolicyTest.php` | Unit test `update` & `submit` policy |

### Skenario demo yang dapat direproduksi dengan test

1. **Mahasiswa**: buat proposal → edit proposal sendiri → coba `/admin` (403) → tombol input nilai tidak ada.
2. **Asdos / Dosen**: buka `/admin` → daftar proposal → review + nilai → status jadi `reviewed`.
3. **Policy**: mahasiswa A edit proposal A (berhasil) vs proposal B (403).
4. **Rate limiting**: 3 submit berhasil, submit ke-4 → 429.
5. **Auto-login**: `/login/as/dosen` berhasil di `local`, 404 di environment lain.

---

## Command yang Tersedia

```bash
# Dev
php artisan serve               # server PHP (http://localhost:8000)
npm run dev                     # Vite dev server + HMR
npm run build                   # build asset untuk produksi
composer run dev                # php artisan dev (multi-process)

# Database
php artisan migrate             # jalankan migration
php artisan migrate:fresh --seed # reset + seed demo
php artisan db:seed             # seed demo saja (idempotent)
php artisan migrate:rollback    # rollback migration

# Quality
php artisan test                # test suite
vendor/bin/pint                 # format kode (PSR-12)
vendor/bin/pint --dirty         # format hanya file yang berubah
php artisan boost:install       # setup Laravel Boost (guidelines + skills untuk AI agent)

# Informasi
php artisan about               # ringkasan environment aplikasi
php artisan route:list          # daftar route
```

---

## Catatan Deviasi dari PRD

Implementasi saat ini mengikuti PRD dengan penyesuaian berikut (semua tetap konsisten secara fungsional):

1. **Tabel `grades` tidak dibuat.** Konsep input nilai direalisasikan sebagai kolom `score`, `feedback`, `grader_id`, `reviewed_at` pada `project_proposals`. Route `/admin/grades*` dan `GradeController` tidak ada; sebagai ganya, review dilakukan di `PATCH /admin/proposals/{proposal}/review`.
2. **Status `revised` ditambahkan.** PRD hanya menyebut `draft | submitted | reviewed`; implementasi menambah `revised` agar alur "revisi setelah review" tetap eksplisit dan tervalidasi.
3. **Proposal `draft` tidak terlihat oleh admin.** `AdminController` hanya menampilkan status `submitted | revised | reviewed` dan memblokir akses detail `draft` dengan 404 (draft dianggap data privat pemilik).
4. **Route review memakai `PATCH` dengan prefix `admin.`** sesuai konvensi penamaan route Laravel, bukan `PUT`.
5. **Route `/login/as/{role}` berada di luar middleware `guest`** agar tetap bisa dipakai saat session sudah ada (berguna untuk demo berpindah role).
6. **Permission berbasis role pada area admin memakai default middleware** (`cek.peran` tanpa parameter = staff), sedangkan area `proposals` dikunci eksplisit dengan `cek.peran:mahasiswa`.

---

## License

MIT — lihat bagian license pada `composer.json`.
