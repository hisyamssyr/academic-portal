<?php

namespace Database\Seeders;

use App\Enums\ProposalStatus;
use App\Enums\UserRole;
use App\Models\ProjectProposal;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with demo accounts and sample data.
     *
     * The seeder is idempotent so it can be re-run without unique constraint errors.
     */
    public function run(): void
    {
        $mahasiswa = $this->demoUser('Mahasiswa Demo', 'mahasiswa@example.com', UserRole::Mahasiswa);
        $mahasiswaLain = $this->demoUser('Mahasiswa Lain', 'mahasiswa2@example.com', UserRole::Mahasiswa);
        $asdos = $this->demoUser('Asisten Dosen Demo', 'asdos@example.com', UserRole::Asdos);
        $dosen = $this->demoUser('Dosen Demo', 'dosen@example.com', UserRole::Dosen);

        $this->demoProposal(
            $mahasiswa,
            'Deteksi Penyakit Daun Padi Menggunakan CNN',
            'Rencana proyek AI untuk mengklasifikasikan penyakit daun padi berdasarkan citra digital menggunakan convolutional neural network.',
            ProposalStatus::Draft,
        );

        $this->demoProposal(
            $mahasiswa,
            'Chatbot Layanan Akademik Berbasis NLP',
            'Rencana proyek AI berupa chatbot yang menjawab pertanyaan mahasiswa seputar layanan akademik menggunakan pemrosesan bahasa alami.',
            ProposalStatus::Submitted,
        );

        $this->demoProposal(
            $mahasiswa,
            'Sistem Rekomendasi Mata Kuliah Pilihan',
            'Rencana proyek AI untuk merekomendasikan mata kuliah pilihan berdasarkan riwayat nilai dan minat mahasiswa.',
            ProposalStatus::Reviewed,
            score: 88.5,
            feedback: 'Analisis masalah kuat, perkuat validasi dataset.',
            grader: $dosen,
        );

        $this->demoProposal(
            $mahasiswaLain,
            'Prediksi Harga Pangan dengan Regresi',
            'Rencana proyek AI untuk memprediksi harga komoditas pangan mingguan menggunakan model regresi.',
            ProposalStatus::Submitted,
        );

        $this->demoProposal(
            $mahasiswaLain,
            'Klasifikasi Sentimen Ulasan Aplikasi',
            'Rencana proyek AI untuk mengklasifikasikan sentimen ulasan pengguna aplikasi menggunakan TF-IDF dan naive Bayes.',
            ProposalStatus::Reviewed,
            score: 82,
            feedback: 'Metodologi jelas, tambahkan perbandingan model.',
            grader: $asdos,
        );
    }

    private function demoUser(string $name, string $email, UserRole $role): User
    {
        return User::query()->firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'role' => $role,
                'email_verified_at' => now(),
                'password' => 'password',
            ],
        );
    }

    private function demoProposal(
        User $user,
        string $title,
        string $description,
        ProposalStatus $status,
        ?float $score = null,
        ?string $feedback = null,
        ?User $grader = null,
    ): void {
        ProjectProposal::query()->firstOrCreate(
            ['user_id' => $user->id, 'title' => $title],
            [
                'description' => $description,
                'status' => $status,
                'score' => $score,
                'feedback' => $feedback,
                'grader_id' => $grader?->id,
                'reviewed_at' => $grader !== null ? now() : null,
            ],
        );
    }
}
