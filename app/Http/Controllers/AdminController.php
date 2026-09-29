<?php

namespace App\Http\Controllers;

use App\Enums\ProposalStatus;
use App\Models\ProjectProposal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Review statuses visible to staff; drafts stay private to their owner.
     *
     * @var array<int, ProposalStatus>
     */
    private const VISIBLE_STATUSES = [ProposalStatus::Submitted, ProposalStatus::Revised, ProposalStatus::Reviewed];

    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        return view('admin.index', [
            'pendingReviewCount' => ProjectProposal::whereIn('status', [ProposalStatus::Submitted, ProposalStatus::Revised])->count(),
            'reviewedCount' => ProjectProposal::where('status', ProposalStatus::Reviewed)->count(),
            'recentProposals' => ProjectProposal::query()
                ->with('user')
                ->whereIn('status', self::VISIBLE_STATUSES)
                ->latest()
                ->latest('id')
                ->limit(5)
                ->get(),
        ]);
    }

    /**
     * Display submitted and reviewed proposals for review.
     */
    public function proposals(Request $request): View
    {
        $currentStatus = ProposalStatus::tryFrom($request->string('status')->toString());

        if ($currentStatus === ProposalStatus::Draft) {
            $currentStatus = null;
        }

        $proposals = ProjectProposal::query()
            ->with('user')
            ->when(
                $currentStatus,
                fn (Builder $query, ProposalStatus $status): Builder => $query->where('status', $status),
                fn (Builder $query): Builder => $query->whereIn('status', self::VISIBLE_STATUSES),
            )
            ->latest()
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.proposals.index', [
            'proposals' => $proposals,
            'statuses' => self::VISIBLE_STATUSES,
            'currentStatus' => $currentStatus?->value ?? '',
        ]);
    }

    /**
     * Display the given proposal with its student and review information.
     */
    public function showProposal(ProjectProposal $proposal): View
    {
        abort_if($proposal->status === ProposalStatus::Draft, 404);

        $proposal->load(['user', 'grader']);

        return view('admin.proposals.show', ['proposal' => $proposal]);
    }

    /**
     * Store the review result and mark the given proposal as reviewed.
     */
    public function reviewProposal(Request $request, ProjectProposal $proposal): RedirectResponse
    {
        Gate::authorize('input-nilai');

        abort_if($proposal->status === ProposalStatus::Draft, 404);

        $validated = $request->validate([
            'score' => ['required', 'numeric', 'min:0', 'max:100'],
            'feedback' => ['nullable', 'string', 'max:1000'],
        ]);

        $proposal->update([
            ...$validated,
            'status' => ProposalStatus::Reviewed,
            'grader_id' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return redirect()
            ->route('admin.proposals.show', $proposal)
            ->with('status', 'proposal-reviewed');
    }
}
