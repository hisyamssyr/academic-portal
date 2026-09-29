<?php

namespace App\Http\Controllers;

use App\Enums\ProposalStatus;
use App\Models\Grade;
use App\Models\ProjectProposal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        $proposalCounts = collect(ProposalStatus::cases())->mapWithKeys(
            fn (ProposalStatus $status): array => [
                $status->value => ProjectProposal::where('status', $status)->count(),
            ],
        );

        return view('admin.index', [
            'proposalCounts' => $proposalCounts,
            'totalProposals' => $proposalCounts->sum(),
            'totalGrades' => Grade::count(),
            'recentProposals' => ProjectProposal::with('user')->latest()->latest('id')->limit(5)->get(),
        ]);
    }

    /**
     * Display all proposals for review.
     */
    public function proposals(Request $request): View
    {
        $status = $request->string('status')->toString();

        $proposals = ProjectProposal::query()
            ->with('user')
            ->when(
                ProposalStatus::tryFrom($status),
                fn (Builder $query, ProposalStatus $status): Builder => $query->where('status', $status),
            )
            ->latest()
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.proposals.index', [
            'proposals' => $proposals,
            'statuses' => ProposalStatus::cases(),
            'currentStatus' => $status,
        ]);
    }

    /**
     * Display the given proposal with its student information.
     */
    public function showProposal(ProjectProposal $proposal): View
    {
        $proposal->load('user');

        return view('admin.proposals.show', ['proposal' => $proposal]);
    }

    /**
     * Update the review status of the given proposal.
     */
    public function updateProposalStatus(Request $request, ProjectProposal $proposal): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::enum(ProposalStatus::class)],
        ]);

        $proposal->update($validated);

        return redirect()
            ->route('admin.proposals.show', $proposal)
            ->with('status', 'proposal-status-updated');
    }
}
