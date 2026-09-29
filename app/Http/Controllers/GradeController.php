<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GradeController extends Controller
{
    /**
     * Display all grades.
     */
    public function index(): View
    {
        $grades = Grade::query()
            ->with(['student', 'grader'])
            ->latest()
            ->latest('id')
            ->paginate(10);

        return view('admin.grades.index', ['grades' => $grades]);
    }

    /**
     * Show the form for creating a new grade.
     */
    public function create(): View
    {
        Gate::authorize('input-nilai');

        return view('admin.grades.create', [
            'students' => User::where('role', UserRole::Mahasiswa)->orderBy('name')->get(),
            'courses' => Grade::COURSES,
        ]);
    }

    /**
     * Store a newly created grade.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('input-nilai');

        $validated = $request->validate([
            'student_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('role', UserRole::Mahasiswa->value),
            ],
            'course' => ['required', 'string', Rule::in(Grade::COURSES)],
            'score' => ['required', 'numeric', 'min:0', 'max:100'],
            'feedback' => ['nullable', 'string', 'max:1000'],
        ]);

        $request->user()->gradesGiven()->create($validated);

        return redirect()
            ->route('admin.grades.index')
            ->with('status', 'grade-created');
    }
}
