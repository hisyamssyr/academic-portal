<?php

namespace Database\Seeders;

use App\Enums\ProposalStatus;
use App\Models\Grade;
use App\Models\ProjectProposal;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with demo accounts and sample data.
     */
    public function run(): void
    {
        $mahasiswa = User::factory()->mahasiswa()->create([
            'name' => 'Mahasiswa Demo',
            'email' => 'mahasiswa@example.com',
        ]);

        $mahasiswaLain = User::factory()->mahasiswa()->create([
            'name' => 'Mahasiswa Lain',
            'email' => 'mahasiswa2@example.com',
        ]);

        $asdos = User::factory()->asdos()->create([
            'name' => 'Asisten Dosen Demo',
            'email' => 'asdos@example.com',
        ]);

        $dosen = User::factory()->dosen()->create([
            'name' => 'Dosen Demo',
            'email' => 'dosen@example.com',
        ]);

        ProjectProposal::factory()->for($mahasiswa)->create([
            'title' => 'Deteksi Penyakit Daun Padi Menggunakan CNN',
            'description' => 'Rencana proyek AI untuk mengklasifikasikan penyakit daun padi berdasarkan citra digital menggunakan convolutional neural network.',
            'status' => ProposalStatus::Draft,
        ]);

        ProjectProposal::factory()->for($mahasiswa)->create([
            'title' => 'Chatbot Layanan Akademik Berbasis NLP',
            'description' => 'Rencana proyek AI berupa chatbot yang menjawab pertanyaan mahasiswa seputar layanan akademik menggunakan pemrosesan bahasa alami.',
            'status' => ProposalStatus::Submitted,
        ]);

        ProjectProposal::factory()->for($mahasiswa)->create([
            'title' => 'Sistem Rekomendasi Mata Kuliah Pilihan',
            'description' => 'Rencana proyek AI untuk merekomendasikan mata kuliah pilihan berdasarkan riwayat nilai dan minat mahasiswa.',
            'status' => ProposalStatus::Reviewed,
        ]);

        ProjectProposal::factory()->for($mahasiswaLain)->create([
            'title' => 'Prediksi Harga Pangan dengan Regresi',
            'description' => 'Rencana proyek AI untuk memprediksi harga komoditas pangan mingguan menggunakan model regresi.',
            'status' => ProposalStatus::Draft,
        ]);

        Grade::factory()->for($mahasiswa, 'student')->for($dosen, 'grader')->create([
            'course' => 'Kecerdasan Buatan',
            'score' => 88.5,
            'feedback' => 'Analisis masalah kuat, perkuat validasi dataset.',
        ]);

        Grade::factory()->for($mahasiswa, 'student')->for($asdos, 'grader')->create([
            'course' => 'Pemrograman Berbasis Kerangka Kerja',
            'score' => 92,
            'feedback' => 'Implementasi rapi dan sesuai konvensi framework.',
        ]);

        Grade::factory()->for($mahasiswaLain, 'student')->for($dosen, 'grader')->create([
            'course' => 'Pembelajaran Mesin',
            'score' => 79,
            'feedback' => 'Perlu eksperimen tambahan untuk membandingkan model.',
        ]);
    }
}
