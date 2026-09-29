<?php

namespace App\Policies;

use App\Enums\ProposalStatus;
use App\Models\ProjectProposal;
use App\Models\User;

class ProjectProposalPolicy
{
    /**
     * Determine whether the user may update the proposal.
     */
    public function update(User $user, ProjectProposal $projectProposal): bool
    {
        return $projectProposal->isOwnedBy($user);
    }

    /**
     * Determine whether the user may submit the proposal for review.
     */
    public function submit(User $user, ProjectProposal $projectProposal): bool
    {
        return $projectProposal->isOwnedBy($user)
            && $projectProposal->status === ProposalStatus::Draft;
    }
}
