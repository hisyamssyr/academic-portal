<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutoLoginController extends Controller
{
    /**
     * Log in as the first user with the given role on local environments.
     */
    public function __invoke(Request $request, string $role): RedirectResponse
    {
        abort_unless(app()->environment('local'), 404);

        $userRole = UserRole::tryFrom($role);

        abort_if($userRole === null, 404);

        $user = User::where('role', $userRole)->first();

        abort_if($user === null, 404);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route($userRole->isStaff() ? 'admin.dashboard' : 'dashboard');
    }
}
