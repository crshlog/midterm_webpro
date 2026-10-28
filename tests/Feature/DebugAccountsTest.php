<?php

namespace Tests\Feature;

use App\Models\Employer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DebugAccountsTest extends TestCase
{
    use RefreshDatabase;

    protected User $employerUser;
    protected User $freelancerUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->employerUser = User::create([
            'name' => 'Demo Employer Org',
            'email' => 'qa.employer@ascend.io',
            'password' => bcrypt('password'),
            'role' => 'employer',
        ]);

        Employer::create([
            'user_id' => $this->employerUser->id,
            'company_name' => 'QA Employer Org',
            'industry' => 'Software Testing',
        ]);

        $this->freelancerUser = User::create([
            'name' => 'Demo Freelancer QA',
            'email' => 'qa.freelancer@ascend.io',
            'password' => bcrypt('password'),
            'role' => 'freelancer',
        ]);
    }

    public function test_it_renders_debug_accounts_inspector(): void
    {
        $response = $this->get(route('debug.accounts.index'));

        $response->assertStatus(200);
        $response->assertSee('Registered Accounts Inspector');
        $response->assertSee('qa.employer@ascend.io');
        $response->assertSee('qa.freelancer@ascend.io');
    }

    public function test_it_filters_accounts_by_role(): void
    {
        $response = $this->get(route('debug.accounts.index', ['role' => 'employer']));

        $response->assertStatus(200);
        $response->assertSee('qa.employer@ascend.io');
        $response->assertDontSee('qa.freelancer@ascend.io');
    }

    public function test_it_switches_user_session(): void
    {
        $this->actingAs($this->freelancerUser);
        $this->assertAuthenticatedAs($this->freelancerUser);

        $response = $this->post(route('debug.accounts.switch', $this->employerUser));

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($this->employerUser);
    }

    public function test_it_executes_artisan_accounts_list_command(): void
    {
        $this->artisan('accounts:list')
            ->expectsOutputToContain('Ascend QA Account Tracker')
            ->expectsOutputToContain('qa.employer@ascend.io')
            ->expectsOutputToContain('qa.freelancer@ascend.io')
            ->assertExitCode(0);
    }
}
