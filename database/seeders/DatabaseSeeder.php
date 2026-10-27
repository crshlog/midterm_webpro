<?php

namespace Database\Seeders;

use App\Models\Bookmark;
use App\Models\Employer;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Demo Employer Accounts
        $employerUser1 = User::create([
            'name' => 'Sarah Jenkins (Nexus Studio)',
            'email' => 'employer@nexus.io',
            'password' => Hash::make('password'),
            'role' => 'employer',
            'phone' => '+1 (555) 234-5678',
        ]);

        $employer1 = Employer::create([
            'user_id' => $employerUser1->id,
            'company_name' => 'Nexus Creative & Digital Studio',
            'industry' => 'UI/UX & Web Development',
            'website' => 'https://nexuscreative.io',
            'bio' => 'Nexus is a fast-growing digital agency partnering with innovative tech startups. We actively scout passionate student freelancers for UI/UX sprints, branding kits, and landing page engineering.',
            'logo_image' => null,
            'social_media_links' => 'https://linkedin.com/company/nexus-studio, https://x.com/nexus_digital',
        ]);

        $employerUser2 = User::create([
            'name' => 'Marcus Vance (NovaTech Labs)',
            'email' => 'marcus@novatech.dev',
            'password' => Hash::make('password'),
            'role' => 'employer',
            'phone' => '+1 (555) 876-5432',
        ]);

        $employer2 = Employer::create([
            'user_id' => $employerUser2->id,
            'company_name' => 'NovaTech AI Systems',
            'industry' => 'AI & Cloud Software',
            'website' => 'https://novatech.ai',
            'bio' => 'Building next-generation telemetry and automation microservices. We love working with student developers who want real-world production experience.',
            'logo_image' => null,
            'social_media_links' => 'https://github.com/novatech-ai, https://linkedin.com/company/novatech',
        ]);

        $employerUser3 = User::create([
            'name' => 'Clara Oswald (Campus Tutoring & Media)',
            'email' => 'clara@campustutors.org',
            'password' => Hash::make('password'),
            'role' => 'employer',
            'phone' => '+1 (555) 432-1098',
        ]);

        $employer3 = Employer::create([
            'user_id' => $employerUser3->id,
            'company_name' => 'Campus Student Tutoring Hub',
            'industry' => 'Education & Academic Support',
            'website' => 'https://campustutors.org',
            'bio' => 'University student-run cooperative providing 1-on-1 tutoring, study group curation, and academic writing coaching across STEM and Humanities.',
            'logo_image' => null,
            'social_media_links' => 'https://instagram.com/campustutors',
        ]);

        // 2. Create Demo Freelancer Accounts
        $freelancer1 = User::create([
            'name' => 'Alex Rivera (Student Developer)',
            'email' => 'freelancer@ascend.io',
            'password' => Hash::make('password'),
            'role' => 'freelancer',
            'phone' => '+1 (555) 987-6543',
        ]);

        $freelancer2 = User::create([
            'name' => 'Rianti Dewi (Student Designer)',
            'email' => 'rianti@student.ac.id',
            'password' => Hash::make('password'),
            'role' => 'freelancer',
            'phone' => '+62 812 3456 7890',
        ]);

        $freelancer3 = User::create([
            'name' => 'Darminto Utomo (Coder & Math Tutor)',
            'email' => 'darminto@student.ac.id',
            'password' => Hash::make('password'),
            'role' => 'freelancer',
            'phone' => '+62 813 9876 5432',
        ]);

        // 3. Create Diverse Job Postings
        $jobs = [
            [
                'employer_id' => $employer1->id,
                'title' => 'Landing Page Redesign (Figma to Tailwind CSS)',
                'job_type' => 'Remote',
                'budget' => 650.00,
                'deadline' => now()->addDays(14),
                'requirements' => "• Proficiency in modern Tailwind CSS and semantic HTML\n• Experience translating Figma micro-interactions into lightweight JS\n• Responsive layout tested on desktop, tablet, and mobile browsers\n• Git branch workflow with pull request delivery",
                'description' => 'We need a student front-end freelancer to implement 4 landing page sections from our existing Figma board into clean, componentized HTML & Tailwind CSS. Great portfolio builder with direct client credit.',
                'is_active' => true,
            ],
            [
                'employer_id' => $employer1->id,
                'title' => 'Brand Identity Kit & Vector Iconography',
                'job_type' => 'Remote',
                'budget' => 450.00,
                'deadline' => now()->addDays(20),
                'requirements' => "• Vector design using Adobe Illustrator or Figma\n• 15 custom SVG icons matching our dark-tech brand guidelines\n• Color palette token definitions and typography pairing guide\n• High-resolution PNG and SVG export assets",
                'description' => 'Looking for a creative student designer to expand our design system with 15 custom geometric vector icons and social media asset templates.',
                'is_active' => true,
            ],
            [
                'employer_id' => $employer2->id,
                'title' => 'Python API Scraping & Data Pipeline Microservice',
                'job_type' => 'Remote',
                'budget' => 850.00,
                'deadline' => now()->addDays(10),
                'requirements' => "• Python 3.11+ using FastAPI or requests/httpx\n• JSON data schema validation and SQLite/PostgreSQL ingestion\n• Rate-limiting and error backoff handling\n• Clean README documentation with unit tests",
                'description' => 'Build a standalone asynchronous ingestion worker that queries public developer APIs, formats payloads, and stores structured benchmarks for telemetry analytics.',
                'is_active' => true,
            ],
            [
                'employer_id' => $employer2->id,
                'title' => 'DevOps Linux Server Hardening & Docker Setup',
                'job_type' => 'Remote',
                'budget' => 500.00,
                'deadline' => now()->addDays(7),
                'requirements' => "• Ubuntu 24.04 LTS configuration\n• Docker & Docker Compose containerization\n• Nginx reverse proxy with automated Let's Encrypt SSL\n• Basic UFW firewall and SSH key authentication hardening",
                'description' => 'We need a knowledgeable computer science student to write a Docker Compose stack and deployment script for our staging environment.',
                'is_active' => true,
            ],
            [
                'employer_id' => $employer3->id,
                'title' => 'Calculus II & Linear Algebra Peer Tutor',
                'job_type' => 'On-site',
                'budget' => 350.00,
                'deadline' => now()->addDays(5),
                'requirements' => "• Minimum Grade A in Multivariable Calculus or Linear Algebra\n• Excellent pedagogical patience and communication skills\n• 4 hours per week on-campus tutoring sessions (flexible hours)\n• Ability to prepare concise homework review worksheets",
                'description' => 'Campus Student Hub is hiring an energetic peer tutor to lead weekly study sessions for sophomore engineering students preparing for midterm exams.',
                'is_active' => true,
            ],
            [
                'employer_id' => $employer3->id,
                'title' => 'Technical Documentation & Blog Content Writer',
                'job_type' => 'Hybrid',
                'budget' => 400.00,
                'deadline' => now()->addDays(18),
                'requirements' => "• Strong technical English writing and editing capability\n• Ability to explain computer programming concepts to beginners\n• Deliver four 1,000-word tutorial articles in Markdown format\n• Basic code snippets verification",
                'description' => 'Produce 4 introductory coding walkthroughs for our student learning blog covering Git fundamentals, REST APIs, and responsive CSS grids.',
                'is_active' => true,
            ],
            [
                'employer_id' => $employer1->id,
                'title' => 'Mobile Flutter UI Prototype (Completed Gig)',
                'job_type' => 'Remote',
                'budget' => 950.00,
                'deadline' => now()->subDays(5),
                'requirements' => "• Flutter widget composition\n• State management with Riverpod or Provider",
                'description' => 'Archived gig: Completed student project developing a prototype mobile dashboard.',
                'is_active' => false,
            ],
        ];

        $createdJobs = [];
        foreach ($jobs as $jobData) {
            $createdJobs[] = JobPosting::create($jobData);
        }

        // 4. Create Demo Job Applications
        // Application 1: Alex Rivera -> Landing Page Redesign (Hired)
        JobApplication::create([
            'job_id' => $createdJobs[0]->id,
            'applicant_id' => $freelancer1->id,
            'cover_letter' => "Hi Sarah! I am a 3rd-year CS student with 2 years of freelance experience shipping production Tailwind CSS and Laravel Blade apps. I have reviewed your Figma preview and can deliver pixel-perfect, accessible components within 5 days.",
            'portfolio_url' => 'https://github.com/alexrivera-dev',
            'cv_file' => null,
            'status' => 'Hired',
        ]);

        // Application 2: Alex Rivera -> Python Scraping (Interviewed)
        JobApplication::create([
            'job_id' => $createdJobs[2]->id,
            'applicant_id' => $freelancer1->id,
            'cover_letter' => "Hello NovaTech team! I have built asynchronous web scrapers using Python httpx and asyncio for multiple university research labs. I'd love to help automate your telemetry pipelines.",
            'portfolio_url' => 'https://alexrivera.dev',
            'cv_file' => null,
            'status' => 'Interviewed',
        ]);

        // Application 3: Alex Rivera -> Calculus Tutor (Pending - Applicant can withdraw this!)
        JobApplication::create([
            'job_id' => $createdJobs[4]->id,
            'applicant_id' => $freelancer1->id,
            'cover_letter' => "I received an A+ in Engineering Calculus II last semester and previously served as a student teaching assistant. I am available Tuesday and Thursday afternoons on campus.",
            'portfolio_url' => 'https://linkedin.com/in/alex-rivera-student',
            'cv_file' => null,
            'status' => 'Pending',
        ]);

        // Application 4: Rianti Dewi -> Brand Identity Kit (Interviewed)
        JobApplication::create([
            'job_id' => $createdJobs[1]->id,
            'applicant_id' => $freelancer2->id,
            'cover_letter' => "Hi Nexus Studio! I specialize in minimalist vector iconography and UI design systems. My portfolio includes 4 published Figma community asset packs with over 12k downloads.",
            'portfolio_url' => 'https://dribbble.com/rianti-dewi',
            'cv_file' => null,
            'status' => 'Interviewed',
        ]);

        // Application 5: Darminto Utomo -> Documentation Writer (Pending)
        JobApplication::create([
            'job_id' => $createdJobs[5]->id,
            'applicant_id' => $freelancer3->id,
            'cover_letter' => "I am an enthusiastic student writer who has published 10+ technical articles on Hashnode and Dev.to. I can produce engaging, well-documented guides.",
            'portfolio_url' => 'https://dev.to/darminto',
            'cv_file' => null,
            'status' => 'Pending',
        ]);

        // 5. Create Demo Bookmarks
        Bookmark::create([
            'user_id' => $freelancer1->id,
            'job_id' => $createdJobs[1]->id,
        ]);
        Bookmark::create([
            'user_id' => $freelancer1->id,
            'job_id' => $createdJobs[3]->id,
        ]);
        Bookmark::create([
            'user_id' => $freelancer2->id,
            'job_id' => $createdJobs[0]->id,
        ]);
    }
}
