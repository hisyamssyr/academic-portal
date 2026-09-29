<?php

namespace App\Models;

use App\Enums\ProposalStatus;
use Database\Factories\ProjectProposalFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'title', 'description', 'status', 'score', 'feedback', 'grader_id', 'reviewed_at'])]
class ProjectProposal extends Model
{
    /** @use HasFactory<ProjectProposalFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ProposalStatus::class,
            'score' => 'decimal:2',
            'reviewed_at' => 'datetime',
        ];
    }

    /**
     * The student who created the proposal.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The lecturer or teaching assistant who reviewed the proposal.
     *
     * @return BelongsTo<User, $this>
     */
    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'grader_id');
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    public function isReviewed(): bool
    {
        return $this->status === ProposalStatus::Reviewed;
    }
}
