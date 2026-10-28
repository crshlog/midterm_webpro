<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ListAccountsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounts:list
                            {--role= : Filter by role (employer or freelancer)}
                            {--search= : Search by user name, email, or company}
                            {--limit=30 : Maximum number of accounts to display}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all registered accounts with telemetry for QA and debugging';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $role = $this->option('role');
        $search = $this->option('search');
        $limit = (int) $this->option('limit');

        $query = User::with(['employer.jobPostings', 'applications', 'bookmarks'])
            ->withCount(['applications', 'bookmarks'])
            ->latest();

        if ($role && in_array(strtolower($role), ['employer', 'freelancer'])) {
            $query->where('role', strtolower($role));
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

        $totalUsers = User::count();
        $totalEmployers = User::where('role', 'employer')->count();
        $totalFreelancers = User::where('role', 'freelancer')->count();

        $users = $query->take($limit)->get();

        $this->info("=== Ascend QA Account Tracker ===");
        $this->line("Total Registered: <fg=cyan>{$totalUsers}</> (<fg=blue>{$totalEmployers} Employers</>, <fg=green>{$totalFreelancers} Freelancers</>)\n");

        if ($users->isEmpty()) {
            $this->warn("No accounts found matching your query.");
            return 0;
        }

        $headers = ['ID', 'Name', 'Email', 'Role', 'Organization / Profile', 'Activity Stats', 'Registered'];
        $rows = [];

        foreach ($users as $u) {
            $org = $u->isEmployer()
                ? ($u->employer?->company_name ?? 'No profile')
                : 'Student Candidate';

            $stats = $u->isEmployer()
                ? ($u->employer?->jobPostings?->count() ?? 0) . ' gigs posted'
                : "{$u->applications_count} apps, {$u->bookmarks_count} saves";

            $rows[] = [
                $u->id,
                $u->name,
                $u->email,
                ucfirst($u->role),
                $org,
                $stats,
                $u->created_at->format('Y-m-d H:i') . ' (' . $u->created_at->diffForHumans() . ')',
            ];
        }

        $this->table($headers, $rows);

        if ($users->count() < $totalUsers) {
            $this->comment("Showing top {$users->count()} of {$totalUsers} accounts. Use --limit={$totalUsers} to show all.");
        }

        return 0;
    }
}
