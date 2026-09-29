<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    /**
     * Proposals created by the user.
     *
     * @return HasMany<ProjectProposal, $this>
     */
    public function proposals(): HasMany
    {
        return $this->hasMany(ProjectProposal::class);
    }

    /**
     * Grades the user received as a student.
     *
     * @return HasMany<Grade, $this>
     */
    public function gradesReceived(): HasMany
    {
        return $this->hasMany(Grade::class, 'student_id');
    }

    /**
     * Grades the user gave as a lecturer or teaching assistant.
     *
     * @return HasMany<Grade, $this>
     */
    public function gradesGiven(): HasMany
    {
        return $this->hasMany(Grade::class, 'grader_id');
    }

    public function isStaff(): bool
    {
        return $this->role->isStaff();
    }
}
