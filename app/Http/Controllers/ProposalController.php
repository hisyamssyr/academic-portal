<?php

namespace App\Http\Controllers;

use App\Enums\ProposalStatus;
use App\Http\Requests\ProjectProposalRequest;
use App\Models\ProjectProposal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProposalController extends Controller
{
    /**
     * Display the proposals owned by the authenticated student.
     */
    public function index(Request $request): View
    {
        $proposals = $request->user()->proposals()
            ->latest()
            ->latest('id')
            ->paginate(10);

        return view('proposals.index', ['proposals' => $proposals]);
    }

    /**
     * Show the form for creating a new proposal.
     */
    public function create(): View
    {
        return view('proposals.create');
    }

    /**
     * Store a newly created proposal.
     */
    public function store(ProjectProposalRequest $request): RedirectResponse
    {
        $request->user()->proposals()->create($request->validated());

        return redirect()
            ->route('proposals.index')
            ->with('status', 'proposal-created');
    }

    /**
     * Show the form for editing the given proposal.
     */
    public function edit(ProjectProposal $proposal): View
    {
        $this->authorize('update', $proposal);

        return view('proposals.edit', ['proposal' => $proposal]);
    }

    /**
     * Update the given proposal.
     */
    public function update(ProjectProposalRequest $request, ProjectProposal $proposal): RedirectResponse
    {
        $this->authorize('update', $proposal);

        $proposal->update($request->validated());

        return redirect()
            ->route('proposals.index')
            ->with('status', 'proposal-updated');
    }

    /**
     * Submit the given proposal for review.
     */
    public function submit(ProjectProposal $proposal): RedirectResponse
    {
        $this->authorize('submit', $proposal);

        $proposal->update(['status' => ProposalStatus::Submitted]);

        return redirect()
            ->route('proposals.index')
            ->with('status', 'proposal-submitted');
    }
}
