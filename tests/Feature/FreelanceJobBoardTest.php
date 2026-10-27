<?php

namespace Tests\Feature;

use App\Models\Bookmark;
use App\Models\Employer;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FreelanceJobBoardTest extends TestCase
{
    use RefreshDatabase;

    protected User $employerUser;
    protected Employer $employer;
    protected User $freelancerUser;
    protected JobPosting $activeJob;

    protected function setUp(): void
    {
        parent::setUp();

        $this->employerUser = User::create([
            'name' => 'Nexus Studio Admin',
            'email' => 'nexus@agency.io',
            'password' => bcrypt('password'),
            'role' => 'employer',
        ]);

        $this->employer = Employer::create([
            'user_id' => $this->employerUser->id,
            'company_name' => 'Nexus Studio',
            'industry' => 'UI/UX Design',
            'website' => 'https://nexus.io',
            'bio' => 'Award winning student design studio.',
        ]);

        $this->freelancerUser = User::create([
            'name' => 'Alex Rivera',
            'email' => 'alex@student.io',
            'password' => bcrypt('password'),
            'role' => 'freelancer',
        ]);

        $this->activeJob = JobPosting::create([
            'employer_id' => $this->employer->id,
            'title' => 'Tailwind CSS Component Developer',
            'job_type' => 'Remote',
            'budget' => 500.00,
            'deadline' => now()->addDays(10),
            'requirements' => 'Tailwind, Blade, Git',
            'description' => 'Build 5 accessible UI components.',
            'is_active' => true,
        ]);
    }

    public function test_user_can_register_as_freelancer_and_employer(): void
    {
        $res1 = $this->post(route('register.submit'), [
            'name' => 'Rianti Dewi',
            'email' => 'rianti@test.com',
            'role' => 'freelancer',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);
        $res1->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('users', ['email' => 'rianti@test.com', 'role' => 'freelancer']);

        $this->post(route('logout'));

        $res2 = $this->post(route('register.submit'), [
            'name' => 'NovaTech Labs',
            'email' => 'marcus@novatech.test',
            'role' => 'employer',
            'company_name' => 'NovaTech AI Systems',
            'industry' => 'Cloud & AI',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);
        $res2->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('users', ['email' => 'marcus@novatech.test', 'role' => 'employer']);
        $this->assertDatabaseHas('employers', ['company_name' => 'NovaTech AI Systems']);
    }

    public function test_user_can_login_and_logout(): void
    {
        $res = $this->post(route('login.submit'), [
            'email' => 'alex@student.io',
            'password' => 'password',
        ]);
        $res->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($this->freelancerUser);

        $logoutRes = $this->post(route('logout'));
        $logoutRes->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_feature_1_job_board_search_and_filters(): void
    {
        JobPosting::create([
            'employer_id' => $this->employer->id,
            'title' => 'Python Scraper Worker',
            'job_type' => 'On-site',
            'budget' => 900.00,
            'deadline' => now()->addDays(5),
            'requirements' => 'FastAPI, Python',
            'is_active' => true,
        ]);

        $res1 = $this->get(route('jobs.index', ['search' => 'Tailwind']));
        $res1->assertStatus(200);
        $res1->assertSee('Tailwind CSS Component Developer');
        $res1->assertDontSee('Python Scraper Worker');

        $res2 = $this->get(route('jobs.index', ['job_type' => 'On-site']));
        $res2->assertStatus(200);
        $res2->assertSee('Python Scraper Worker');
        $res2->assertDontSee('Tailwind CSS Component Developer');
    }

    public function test_feature_2_job_posting_crud_and_status_toggle(): void
    {
        $this->actingAs($this->employerUser);

        // Create
        $createRes = $this->post(route('employer.jobs.store'), [
            'title' => 'Mobile App Prototyper',
            'job_type' => 'Hybrid',
            'budget' => 750.00,
            'deadline' => now()->addDays(14)->format('Y-m-d'),
            'requirements' => 'Flutter or React Native',
            'description' => 'Design and test 3 prototype screens.',
            'is_active' => '1',
        ]);
        $createRes->assertRedirect(route('employer.jobs.index'));
        $this->assertDatabaseHas('job_postings', ['title' => 'Mobile App Prototyper']);

        $newJob = JobPosting::where('title', 'Mobile App Prototyper')->first();

        // Update
        $this->put(route('employer.jobs.update', $newJob), [
            'title' => 'Mobile App Prototyper Updated',
            'job_type' => 'Hybrid',
            'budget' => 800.00,
            'deadline' => now()->addDays(14)->format('Y-m-d'),
            'requirements' => 'Flutter or React Native',
            'is_active' => '1',
        ]);
        $this->assertDatabaseHas('job_postings', ['id' => $newJob->id, 'budget' => 800.00]);

        // Toggle Status
        $this->patch(route('employer.jobs.toggle-status', $newJob));
        $this->assertFalse((bool)$newJob->fresh()->is_active);

        // Delete
        $this->delete(route('employer.jobs.destroy', $newJob));
        $this->assertDatabaseMissing('job_postings', ['id' => $newJob->id]);
    }

    public function test_feature_3_application_submission_with_pdf_upload(): void
    {
        Storage::fake('public');
        $this->actingAs($this->freelancerUser);

        $fakePdf = UploadedFile::fake()->create('alex_rivera_cv.pdf', 300, 'application/pdf');

        $res = $this->post(route('jobs.apply', $this->activeJob), [
            'cover_letter' => 'I have 2 years experience building responsive web UIs in Laravel.',
            'portfolio_url' => 'https://github.com/alexrivera',
            'cv_file' => $fakePdf,
        ]);

        $res->assertRedirect(route('my-applications.index'));
        $this->assertDatabaseHas('job_applications', [
            'job_id' => $this->activeJob->id,
            'applicant_id' => $this->freelancerUser->id,
            'status' => 'Pending',
        ]);
    }

    public function test_feature_4_employer_applicant_review_and_status_update(): void
    {
        $app = JobApplication::create([
            'job_id' => $this->activeJob->id,
            'applicant_id' => $this->freelancerUser->id,
            'cover_letter' => 'Pitch test',
            'status' => 'Pending',
        ]);

        $this->actingAs($this->employerUser);

        $reviewPage = $this->get(route('employer.applications.index'));
        $reviewPage->assertStatus(200);
        $reviewPage->assertSee('Pitch test');

        $statusUpdate = $this->patch(route('employer.applications.update-status', $app), [
            'status' => 'Hired',
        ]);
        $statusUpdate->assertSessionHas('success');
        $this->assertEquals('Hired', $app->fresh()->status);
    }

    public function test_feature_5_applicant_status_tracker(): void
    {
        JobApplication::create([
            'job_id' => $this->activeJob->id,
            'applicant_id' => $this->freelancerUser->id,
            'cover_letter' => 'Pitched by Alex',
            'status' => 'Interviewed',
        ]);

        $this->actingAs($this->freelancerUser);
        $trackerRes = $this->get(route('my-applications.index'));
        $trackerRes->assertStatus(200);
        $trackerRes->assertSee('Interviewed');
        $trackerRes->assertSee($this->activeJob->title);
    }

    public function test_feature_6_job_bookmarking_toggle_and_watchlist(): void
    {
        $this->actingAs($this->freelancerUser);

        // Bookmark
        $this->post(route('jobs.bookmark', $this->activeJob));
        $this->assertDatabaseHas('bookmarks', [
            'user_id' => $this->freelancerUser->id,
            'job_id' => $this->activeJob->id,
        ]);

        $watchlistRes = $this->get(route('bookmarks.index'));
        $watchlistRes->assertStatus(200);
        $watchlistRes->assertSee($this->activeJob->title);

        // Un-bookmark
        $this->post(route('jobs.bookmark', $this->activeJob));
        $this->assertDatabaseMissing('bookmarks', [
            'user_id' => $this->freelancerUser->id,
            'job_id' => $this->activeJob->id,
        ]);
    }

    public function test_feature_7_application_withdrawal(): void
    {
        $app = JobApplication::create([
            'job_id' => $this->activeJob->id,
            'applicant_id' => $this->freelancerUser->id,
            'cover_letter' => 'Will withdraw this',
            'status' => 'Pending',
        ]);

        $this->actingAs($this->freelancerUser);
        $withdrawRes = $this->delete(route('my-applications.withdraw', $app));
        $withdrawRes->assertSessionHas('success');
        $this->assertDatabaseMissing('job_applications', ['id' => $app->id]);
    }

    public function test_feature_8_employer_company_profile_crud(): void
    {
        Storage::fake('public');
        $this->actingAs($this->employerUser);

        $fakeLogo = UploadedFile::fake()->create('nexus_logo.png', 100, 'image/png');

        $updateRes = $this->put(route('employer.profile.update'), [
            'company_name' => 'Nexus Studio Labs Global',
            'industry' => 'Fintech & EdTech',
            'website' => 'https://nexuslabs.global',
            'bio' => 'Empowering university creators worldwide.',
            'social_media_links' => 'https://x.com/nexus_global',
            'logo_image' => $fakeLogo,
        ]);

        $updateRes->assertSessionHas('success');
        $this->assertDatabaseHas('employers', [
            'id' => $this->employer->id,
            'company_name' => 'Nexus Studio Labs Global',
            'industry' => 'Fintech & EdTech',
        ]);
    }
}
