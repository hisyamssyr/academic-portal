<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Realistic Agentic AI proposal themes.
     *
     * @var array<int, string>
     */
    private const TEMA_AGENT = [
        'Ollama',
        'LangChain',
        'AutoGen',
        'CrewAI',
        'LlamaIndex',
        'OpenAI Agents SDK',
        'Semantic Kernel',
        'Vertex AI Agent Builder',
    ];

    /**
     * Realistic Agentic AI proposal ideas.
     *
     * @var array<int, array{judul: string, deskripsi: string}>
     */
    private const IDE_PROPOSAL = [
        [
            'judul' => 'Agen Otonom Penjadwal Konsultasi Akademik',
            'deskripsi' => 'Agen berbasis LLM yang membaca kalender dosen dan mahasiswa, menegosiasikan slot konsultasi, lalu mengirim undangan otomatis dengan memori percakapan lintas sesi.',
        ],
        [
            'judul' => 'Asisten Riset Multi-Agen untuk Tinjauan Pustaka',
            'deskripsi' => 'Sistem multi-agen yang memecah topik riset menjadi sub-kueri, mencari literatur, merangkum temuan, dan menyusun matriks perbandingan metode secara mandiri.',
        ],
        [
            'judul' => 'Agen Kurikulum Adaptif Berbasis RAG',
            'deskripsi' => 'Agen yang memanfaatkan retrieval-augmented generation atas dokumen kurikulum untuk merekomendasikan urutan mata kuliah sesuai capaian pembelajaran mahasiswa.',
        ],
        [
            'judul' => 'Agen Verifikasi Sitasi dan Orisinalitas Naskah',
            'deskripsi' => 'Agen yang menelusuri klaim pada draf naskah, mencocokkan sumber, mendeteksi sitasi yang tidak valid, dan menyusun laporan pemeriksaan otomatis.',
        ],
        [
            'judul' => 'Agen Monitoring Progres Tugas Mahasiswa',
            'deskripsi' => 'Agen proaktif yang memantau tenggat, memberi nudge personal, dan mengeskalasi risiko keterlambatan kepada dosen melalui ringkasan mingguan.',
        ],
        [
            'judul' => 'Agen Helpdesk Akademik dengan Tool Calling',
            'deskripsi' => 'Agen layanan akademik yang memanggil tool internal untuk cek nilai, jadwal, dan status beasiswa, serta mengeskalasi tiket kompleks ke petugas manusia.',
        ],
        [
            'judul' => 'Agen Analisis Sentimen Umpan Balik Perkuliahan',
            'deskripsi' => 'Agen yang mengklasifikasi umpan balik mahasiswa, mengelompokkan tema keluhan, dan merumuskan rekomendasi perbaikan bagi koordinator mata kuliah.',
        ],
        [
            'judul' => 'Agen Rekomendasi Topik Skripsi Berbasis Minat',
            'deskripsi' => 'Agen yang memetakan minat dan riwayat nilai mahasiswa ke pustaka penelitian laboratorium, lalu mengusulkan topik skripsi beserta calon pembimbing yang relevan.',
        ],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ide = fake()->randomElement(self::IDE_PROPOSAL);

        return [
            'user_id' => User::factory()->mahasiswa(),
            'judul' => $ide['judul'],
            'deskripsi' => $ide['deskripsi'],
            'tema_agent' => fake()->randomElement(self::TEMA_AGENT),
            'api_key_secure' => 'sk-'.Str::random(48),
        ];
    }

    /**
     * Indicate that the proposal has no description yet.
     */
    public function tanpaDeskripsi(): static
    {
        return $this->state(fn (array $attributes): array => [
            'deskripsi' => null,
        ]);
    }
}
