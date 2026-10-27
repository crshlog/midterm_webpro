# Project: Ascend — Revenue Operations SaaS (EF234301 Midterm Exam)

Laravel 13 MVC CRUD application + WebGL 3D Earth Landing Page ("Ascend — Scale Smarter, Not Harder").

## Tech Stack
- **Backend**: Laravel 13.38 (PHP 8.3.33, MVC Architecture, Eloquent ORM, Session Auth)
- **Database**: SQLite (`database/database.sqlite` pre-configured) / MySQL or PostgreSQL (production), 4 Foreign Keys (`leads.user_id`, `leads.pipeline_id`, `lead_activities.lead_id`, `lead_activities.user_id`)
- **Frontend**: Blade Components + Vanilla CSS (`public/css/ascend.css` using exact Ascend `:root` tokens) + Three.js `r0.143.0` ES Module importmap (`public/js/ascend-planet.js`) + Lenis `1.3.23`

## Token-Optimization Rules (`token-optimizer` + `context-engineering`)
1. **WRITE-ONCE WebGL Module (`public/js/ascend-planet.js`)**:
   - Place the entire Three.js r0.143.0 planet, GLSL shaders (`snoise`, `FinalPass`, day/night/ocean shader, cloud shells, motes, stars, land markers), and scroll choreography in `public/js/ascend-planet.js`.
   - **NEVER re-read `public/js/ascend-planet.js`** once created unless debugging a WebGL shader issue. This saves ~3,500 tokens per turn.
2. **File Size Cap (< 150 lines)**:
   - Keep every PHP controller, model, request, and Blade partial under 150 lines (excludes `public/js/ascend-planet.js` and `public/css/ascend.css`).
   - Split UI into modular Blade partials in `resources/views/components/`.
3. **Thin Controllers & Eloquent Patterns (`laravel-patterns`)**:
   - Controllers `< 80` lines; validation in `app/Http/Requests/`; filtering in `Lead::scopeFilter()`.
   - Always eager-load relationships (`Lead::with(['pipeline', 'user'])`).
4. **Full-Output Enforcement (`full-output-enforcement`)**:
   - Never use `// ...`, `// TODO`, or placeholder comments. Output complete, runnable files.
   - Zero conversational filler before or after code blocks.
   - Do NOT install `laravel/boost` or unnecessary third-party packages.

## Commands
- Migrate & Seed: `php artisan migrate:fresh --seed`
- Dev Server: `php artisan serve`
- Clear Cache: `php artisan optimize:clear`
