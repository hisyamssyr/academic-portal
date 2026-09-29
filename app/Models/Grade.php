<?php

namespace App\Models;

use Database\Factories\GradeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['student_id', 'grader_id', 'course', 'score', 'feedback'])]
class Grade extends Model
{
    /** @use HasFactory<GradeFactory> */
    use HasFactory;

    /**
     * Courses available for grading.
     *
     * @var array<string>
     */
    public const COURSES = [
        'Kecerdasan Buatan',
        'Pembelajaran Mesin',
        'Pemrosesan Bahasa Alami',
        'Visi Komputer',
        'Pemrograman Berbasis Kerangka Kerja',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
        ];
    }

    /**
     * The student who received the grade.
     *
     * @return BelongsTo<User, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * The lecturer or teaching assistant who gave the grade.
     *
     * @return BelongsTo<User, $this>
     */
    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'grader_id');
    }
}
