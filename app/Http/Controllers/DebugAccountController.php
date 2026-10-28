<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DebugAccountController extends Controller
{
    /**
     * Display a live list of all registered accounts for QA / debugging.
     */
    public function index(Request $request)
    {
        $roleFilter = $request->query('role');
        $search = $request->query('search');

        $query = User::with(['employer.jobPostings', 'applications', 'bookmarks'])
            ->withCount(['applications', 'bookmarks'])
            ->latest();

        if ($roleFilter && in_array($roleFilter, ['employer', 'freelancer'])) {
            $query->where('role', $roleFilter);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('employer', function ($eq) use ($search) {
                      $eq->where('company_name', 'like', "%{$search}%");
                  });
            });
        }

        $users = $query->paginate(25)->withQueryString();

        $stats = [
            'total_users'        => User::count(),
            'total_employers'    => User::where('role', 'employer')->count(),
            'total_freelancers'  => User::where('role', 'freelancer')->count(),
            'total_jobs'         => JobPosting::count(),
            'total_applications' => JobApplication::count(),
            'newest_account'     => User::latest()->first(),
        ];

        return view('debug.accounts', compact('users', 'stats', 'roleFilter', 'search'));
    }

    /**
     * Instantly log in as any user for testing role permissions and edge cases.
     */
    public function switch(Request $request, User $user)
    {
        Auth::login($user);
        $request->session()->regenerate();

        $roleLabel = ucfirst($user->role);
        return redirect()->route('dashboard')->with(
            'success',
            "⚡ Debugger Session Switched: You are now authenticated as {$user->name} ({$roleLabel}) [ID: #{$user->id}]."
        );
    }

    /**
     * Remove a test account and its cascading relationships.
     */
    public function destroy(User $user)
    {
        $name = $user->name;
        $id = $user->id;

        if (Auth::id() === $user->id) {
            Auth::logout();
        }

        $user->delete();

        return redirect()->route('debug.accounts.index')->with(
            'success',
            "Account #{$id} ({$name}) was successfully deleted."
        );
    }
}
