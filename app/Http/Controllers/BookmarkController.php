<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\JobPosting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BookmarkController extends Controller
{
    // Feature 6: Personal Watchlist
    public function index(): View
    {
        $bookmarkedJobs = auth()->user()
            ->bookmarkedJobs()
            ->with('employer')
            ->latest('bookmarks.created_at')
            ->paginate(10);

        return view('bookmarks.index', compact('bookmarkedJobs'));
    }

    // Feature 6: Toggle Bookmark
    public function toggle(JobPosting $job): RedirectResponse
    {
        $user = auth()->user();
        $existing = Bookmark::where('user_id', $user->id)
            ->where('job_id', $job->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $msg = 'Gig removed from your saved watchlist.';
        } else {
            Bookmark::create([
                'user_id' => $user->id,
                'job_id' => $job->id,
            ]);
            $msg = 'Gig saved to your watchlist!';
        }

        return back()->with('success', $msg);
    }
}
