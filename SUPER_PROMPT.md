<role>
You are an elite Full-Stack Laravel MVC Architect and Creative WebGL Front-End Engineer. Your task is to build **"Ascend — Scale Smarter, Not Harder"**, a complete Laravel MVC Revenue-Operations SaaS Web Application for the **EF234301 Web Programming (D) Midterm Exam**.
</role>

<skills_and_execution_rules>
Apply these strict engineering and token-efficiency rules throughout execution:
1. **TOKEN OPTIMIZATION (`token-optimizer` & `context-engineering`)**:
   - **Isolate Heavy WebGL Code**: Write the entire Three.js r0.143.0 WebGL Earth scene and GLSL shaders into a single standalone ES module at `public/js/ascend-planet.js` and all design tokens/styles into `public/css/ascend.css`. Once written, **DO NOT re-read `public/js/ascend-planet.js`** during backend or CRUD tasks (saves ~3,500 tokens per turn).
   - **Max 150 Lines Per Application File**: Every Controller, Model, FormRequest, Migration, and Blade view/component MUST be under 150 lines with a single responsibility.
   - **Zero Conversational Slop**: No preambles ("Sure! Here is..."), no post-summaries, no restating requirements. Output code and execute commands directly.
2. **FULL-OUTPUT ENFORCEMENT (`full-output-enforcement`)**:
   - NEVER use truncation placeholders (`// ...`, `// rest of code`, `// TODO`, `/* ... */`, `<!-- similar for other cards -->`).
   - Every file must be 100% complete, valid, and runnable.
   - If approaching output token limits, finish the current file cleanly and stop with:
     `[PAUSED — Phase X of 4 complete. Send "continue" to resume from: <next file>]`
3. **LARAVEL MVC PATTERNS (`laravel-patterns`)**:
   - Use Eloquent ORM relationships (`belongsTo`, `hasMany`), eager loading (`with()`), and query scopes (`scopeFilter`). Never write raw SQL joins.
   - Keep Controllers thin (`< 80` lines); validate via `FormRequest` classes in `app/Http/Requests/`.
</skills_and_execution_rules>

<exam_and_product_architecture>
The application merges the exact **Ascend** WebGL landing page with a functional **Ascend Revenue-Operations Command Center** satisfying all EF234301 Midterm rubric criteria:

### A. Database Schema (4 Tables, 4 Foreign Keys)
1. `users`: `id`, `name`, `email`, `password`, `role` (default `'Revenue Operator'`), `region` (default `'Global'`), `timestamps`
2. `pipelines`: `id`, `name`, `slug`, `region`, `color` (default `'#5df0a8'`), `target_revenue` (decimal 12,2), `description`, `timestamps`
3. `leads`: `id`, `user_id` (FK -> `users.id` cascadeOnDelete), `pipeline_id` (FK -> `pipelines.id` cascadeOnDelete), `company_name`, `contact_name`, `contact_email`, `deal_value` (decimal 12,2), `lead_score` (unsignedTinyInteger 0–100), `stage` (`enum: 'new_lead', 'qualified', 'in_negotiation', 'closed_won'`), `priority` (`enum: 'low', 'medium', 'high'`), `expected_close_date` (date nullable), `notes` (text nullable), `timestamps`
   - Model Relations: `belongsTo(User::class)`, `belongsTo(Pipeline::class)`, `hasMany(LeadActivity::class)`
   - Model Scope: `scopeFilter($query, array $filters)` filtering by `search` (company/contact/email), `pipeline_id`, `stage`, `priority`, and `sort`.
4. `lead_activities`: `id`, `lead_id` (FK -> `leads.id` cascadeOnDelete), `user_id` (FK -> `users.id` cascadeOnDelete), `activity_type` (`'call'`, `'email'`, `'trigger'`, `'meeting'`, `'stage_change'`), `summary`, `timestamps`

### B. Authentication (Required, Excluded from the 8-Feature Count)
- Session-based **Login** (`GET/POST /login`), **Registration** (`GET/POST /register`), and **Logout** (`POST /logout`) implemented via a clean `AuthController` (< 90 lines) using glassmorphic Ascend cards floating over the WebGL planet.

### C. Minimum 8 Distinct Features (Excluding Auth)
1. **Feature 1 — Live Revenue & Q3 Funnel Command Dashboard (`GET /dashboard`)**:
   - Displays live KPI stat cards (Total Pipeline Value, Capture Accuracy / Avg Lead Score, Active Deals, Closed Won Revenue), the animated 7-bar Pipeline Velocity chart, and live stage progress bars (`New leads`, `Qualified`, `In negotiation`, `Closed won` computed dynamically from the database).
2. **Feature 2 — Pipeline Territories / Workflows CRUD (`Route::resource('pipelines', PipelineController::class)`)**:
   - Full Create, Read, Update, Delete for revenue pipelines (`pipelines` table) showing deal count and total value per pipeline (`withCount('leads')->withSum('leads', 'deal_value')`).
3. **Feature 3 — Lead & Deal Management CRUD (`Route::resource('leads', LeadController::class)`)**:
   - Full Create, Read, Update, Delete for leads (`leads` table) with foreign-key dropdowns (`pipeline_id`), deal value, lead score (0–100), stage, priority, and expected close date.
4. **Feature 4 — Multi-Criteria Funnel Search, Filter & Sorting (`GET /leads` with query params)**:
   - Filter bar above leads list allowing combined keyword search, pipeline filter, stage filter, priority filter, and sort by deal value or lead score using `Lead::filter(request()->only(['search','pipeline_id','stage','priority','sort']))`.
5. **Feature 5 — Instant Workflow Stage Progression & Score Trigger (`PATCH /leads/{lead}/advance`)**:
   - 1-click button on every lead row/detail view that advances the lead to the next funnel stage (`new_lead` -> `qualified` -> `in_negotiation` -> `closed_won`), increments `lead_score` (+15 up to 100), and automatically logs a `stage_change` record in `lead_activities`.
6. **Feature 6 — Deal Activity & Instant Trigger Log CRUD (`POST /leads/{lead}/activities`, `DELETE /activities/{activity}`)**:
   - On the Lead Detail page (`GET /leads/{lead}`), operators can log new activities/triggers (`call`, `email`, `trigger`, `meeting`) and delete entries, displaying the author (`user.name`) and timestamp.
7. **Feature 7 — Pipeline CSV Export & Printable Executive Report (`GET /leads/export/csv`, `GET /leads/export/print`)**:
   - Streams a downloadable `.csv` file of currently filtered leads and provides a print-formatted executive summary table (`window.print()`).
8. **Feature 8 — Operator Profile & Security Settings (`GET /profile`, `PUT /profile`, `PUT /profile/password`)**:
   - Edit operator name, email, role title, region, update password with current-password verification, and view personal closed-won revenue metrics.
</exam_and_product_architecture>

<exact_frontend_and_webgl_specification>
The public landing page (`GET /` -> `resources/views/welcome.blade.php`) and shared layout (`resources/views/layouts/ascend.blade.php`) MUST reproduce the exact **Ascend — Scale Smarter, Not Harder** visual specification below verbatim.

### 1. HTML Head, Fonts & Importmap (Include in `layouts/ascend.blade.php` and `welcome.blade.php`)
- `<html lang="en">`, `<meta charset="utf-8">`, responsive viewport meta.
- Google Fonts **Inter** weights 400–900 (`<link>` preconnect + stylesheet).
- Pin Three.js `r0.143.0` via CDN importmap (required for `WebGL1Renderer`, `outputEncoding`, `sRGBEncoding`, and shader chunk names):
```html
<script type="importmap">
{ "imports": {
  "three": "https://unpkg.com/three@0.143.0/build/three.module.js",
  "three/examples/jsm/": "https://unpkg.com/three@0.143.0/examples/jsm/"
}}
</script>
```
- Modules imported in `public/js/ascend-planet.js` from `three/examples/jsm/`:
  `postprocessing/EffectComposer.js`, `postprocessing/RenderPass.js`, `postprocessing/UnrealBloomPass.js`, `postprocessing/ShaderPass.js`, `shaders/GammaCorrectionShader.js`, `shaders/CopyShader.js`, `loaders/GLTFLoader.js`, `loaders/DRACOLoader.js`, `controls/OrbitControls.js`.
- Lenis smooth scroll imported from `https://unpkg.com/lenis@1.3.23/dist/lenis.mjs`.
- DOM structure: `<canvas class="planet-canvas"></canvas>` first, then `<div class="page" id="top">` containing all sections.

### 2. Global CSS (`public/css/ascend.css` — Use Verbatim + CRUD Glass Table/Form Extensions)
```css
:root{
  --bg:#04060f; --bg-2:#060a18; --ink:#f3f6ff; --muted:#9aa6c4; --muted-2:#6b7796;
  --line:rgba(150,175,230,.12); --line-strong:rgba(150,175,230,.22);
  --accent:#5df0a8; --accent-2:#2fd38a; --accent-ink:#032018;
  --glass:rgba(12,18,38,.55); --glass-2:rgba(16,24,48,.65);
  --radius:18px; --radius-lg:26px; --maxw:1180px;
  --shadow:0 30px 80px -30px rgba(0,0,0,.75);
  --font:'Inter',-apple-system,BlinkMacSystemFont,system-ui,sans-serif;
}
*{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{font-family:var(--font);color:var(--ink);background:var(--bg);-webkit-font-smoothing:antialiased;text-rendering:optimizeLegibility;overflow-x:hidden}
a{color:inherit;text-decoration:none}
html.lenis,html.lenis body{height:auto}
.lenis.lenis-smooth{scroll-behavior:auto !important}
.lenis.lenis-smooth [data-lenis-prevent]{overscroll-behavior:contain}
.lenis.lenis-stopped{overflow:hidden}
[data-reveal]{opacity:0;transform:translateY(26px);transition:opacity .7s cubic-bezier(.22,.7,.2,1),transform .7s cubic-bezier(.22,.7,.2,1);transition-delay:var(--rd,0ms);will-change:opacity,transform}
[data-reveal].in{opacity:1;transform:none}
.planet-canvas{position:fixed;inset:0;width:100vw;height:100vh;display:block;z-index:0;pointer-events:none}
.page{position:relative;z-index:1}
.section{position:relative;z-index:1;max-width:var(--maxw);margin:0 auto;padding:clamp(80px,12vw,160px) 24px}
.eyebrow{display:inline-block;font-size:12px;font-weight:600;letter-spacing:.18em;text-transform:uppercase;color:var(--accent);margin-bottom:18px}
.section-head{max-width:680px;margin:0 auto clamp(48px,6vw,80px);text-align:center}
.section-head h2,.showcase-copy h2,.cta-card h2{font-size:clamp(30px,4.4vw,50px);font-weight:800;line-height:1.05;letter-spacing:-.02em;text-shadow:0 2px 40px rgba(0,0,0,.6)}
.section-head p{margin-top:18px;color:var(--muted);font-size:18px;line-height:1.65;text-shadow:0 1px 24px rgba(0,0,0,.55)}
.btn{display:inline-flex;align-items:center;gap:9px;font-size:15px;font-weight:600;padding:13px 22px;border-radius:999px;border:1px solid transparent;cursor:pointer;transition:transform .18s ease,box-shadow .25s ease,background .2s ease,border-color .2s ease;white-space:nowrap}
.btn svg{width:17px;height:17px}
.btn-primary{color:var(--accent-ink);background:linear-gradient(180deg,var(--accent),var(--accent-2));box-shadow:0 10px 30px -8px rgba(93,240,168,.55),inset 0 1px 0 rgba(255,255,255,.4)}
.btn-primary:hover{transform:translateY(-2px);box-shadow:0 16px 38px -8px rgba(93,240,168,.7)}
.btn-outline{color:var(--ink);background:rgba(255,255,255,.04);border-color:var(--line-strong);backdrop-filter:blur(10px)}
.btn-outline:hover{background:rgba(255,255,255,.09);transform:translateY(-2px)}
.btn-ghost{color:var(--ink);background:rgba(255,255,255,.05);border-color:var(--line-strong);padding:9px 18px}
.btn-ghost:hover{background:rgba(255,255,255,.1)}
```
Also include responsive breakpoints in `public/css/ascend.css`:
- `<=980px`: `.feature-grid` 2 cols; `.showcase-grid` 1 col, copy centered; `.stats` 2 cols.
- `<=720px`: hide `.nav-links`; `.nav` padding `18px 20px`; `.section` padding `clamp(64px,16vw,110px) 20px`.
- `<=560px`: `.feature-grid` 1 col; `.dash-row` `90px 1fr`; `.hero-actions` buttons full-width `flex:1`; `.cta-card` padding `40px 22px`; `.footer` column.
- `<=380px`: `.stats` 1 col; smaller logos.
- `@media (prefers-reduced-motion: reduce)`: disable reveals (show immediately), bar/fill animations, smooth scroll, and skip Lenis + planet float-up entrance.
- Add matching glassmorphic utility classes for the authenticated Command Center forms, tables, badges, and filter bars using `var(--glass-2)`, `var(--line-strong)`, `var(--accent)`, and `backdrop-filter: blur(16px)`.

### 3. Exact SVG Icons (24×24, `fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"`, arrow uses `stroke-width="2"`)
- **workflow**: `<rect x="3" y="3" width="6" height="6" rx="1.5"/><rect x="15" y="15" width="6" height="6" rx="1.5"/><path d="M6 9v3a3 3 0 0 0 3 3h3"/>`
- **analytics**: `<path d="M4 19V5"/><path d="M4 19h16"/><path d="M8 16v-4"/><path d="M13 16V8"/><path d="M18 16v-7"/>`
- **leads**: `<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>`
- **shield**: `<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/>`
- **bolt**: `<path d="M13 2 3 14h9l-1 8 10-12h-9l1-8Z"/>`
- **globe**: `<circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15 15 0 0 1 0 20 15 15 0 0 1 0-20Z"/>`
- **arrow** (`stroke-width="2"`): `<path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>`

### 4. Landing Page Sections (`resources/views/welcome.blade.php` — Match Copy, Classes & Stagger `--rd` Exactly)
- **SECTION 1 · HERO (`<header class="hero">` + `.logos`)**:
  - `.hero`: `min-height:100vh`, flex column, top-only scrim `background:radial-gradient(125% 62% at 50% -10%, rgba(4,6,15,.82) 0%, rgba(4,6,15,.28) 44%, transparent 68%)`.
  - `.nav` (`max-width:var(--maxw); padding:22px 24px; display:flex; justify-content:space-between; align-items:center; width:100%; margin:0 auto`):
    - Left `.brand` (`inline-flex`, gap `9px`, weight `800`, `19px`, letter-spacing `.16em`): `<span class="brand-mark">▲</span> Ascend` (`.brand-mark` color `var(--accent)`, `14px`, `transform:translateY(-1px)`).
    - Center `.nav-links` (gap `30px`, `14.5px`, `var(--muted)` -> hover `var(--ink)`): `Features` (`#features`), `Solutions` (`#solutions`), `Plans` (`#cta`), `Command Center` (`{{ route('dashboard') }}`).
    - Right: `@auth` `<a href="{{ route('dashboard') }}" class="btn btn-ghost">Command Center</a>` `@else` `<a href="{{ route('register') }}" class="btn btn-ghost">Sign Up</a>` `@endauth`.
  - `.hero-inner` (`flex:1`, centered flex column, `text-align:center`, `transform:translateY(-10vh)`):
    - `<h1 class="hero-title" data-reveal style="--rd:60ms">Scale Smarter,<br>Not <em>Harder</em></h1>` (`clamp(34px,5.2vw,62px)`, weight 800, line-height 1.04, letter-spacing `-.03em`, max-width `15ch`, `text-shadow:0 2px 50px rgba(0,0,0,.55)`; `em` non-italic with `linear-gradient(180deg,var(--accent),var(--accent-2))` `background-clip:text`, transparent color).
    - `<p class="hero-sub" data-reveal style="--rd:180ms">Drive your funnel forward with clever workflows, analytics, and seamless lead management — all in one immersive command center.</p>` (margin-top 24px, max-width 560px, `clamp(16px,2vw,19px)`, line-height 1.6, `var(--muted)`).
    - `.hero-actions` (`data-reveal style="--rd:300ms"`, margin-top 38px, flex, gap 14px, centered, wrap):
      - Primary button (`href="{{ route('register') }}"` or `{{ route('dashboard') }}`): **“Start Free Right Now”** + arrow SVG.
      - Outline button (`href="{{ route('login') }}"` or `#solutions`): **“Schedule a Consult”**.
  - `.logos` (`data-reveal style="--rd:420ms"`, centered, padding `0 24px clamp(36px,6vw,64px)`):
    - `.logos-label` (`12px`, letter-spacing `.16em`, uppercase, `var(--muted-2)`): **“Trusted by fast-moving teams”**
    - `.logos-row` (flex wrap centered, gap `clamp(24px,5vw,56px)`): six `.logo` items (`17px`, weight 700, `var(--muted-2)`, opacity `.8`, hover `var(--ink)`/opacity 1): **Nimbus · Pluma · Citrus · Spider · Helod · Vertx**.
- **SECTION 2 · FEATURES (`<section class="section features" id="features">`)**:
  - `.section-head`: eyebrow (`--rd:0ms`) **“Everything in orbit”**; `h2` (`--rd:80ms`) **“One platform to run the entire revenue engine”**; `p` (`--rd:160ms`) **“Stop stitching tools together. Ascend unifies capture, automation, and insight into a single surface your whole team actually enjoys using.”**
  - `.feature-grid` (`repeat(3,1fr)`, gap `18px`): 6 `.card` elements (padding 30px, radius `var(--radius)`, `background:var(--glass)`, `border:1px solid var(--line)`, `backdrop-filter:blur(14px)`, hover `translateY(-4px)`, border `var(--line-strong)`, bg `var(--glass-2)`, `::before` `radial-gradient(120% 120% at 0% 0%, rgba(93,240,168,.08), transparent 45%)`). Stagger `--rd` by `index * 90ms` (`0ms`, `90ms`, `180ms`, `270ms`, `360ms`, `450ms`):
    1. **workflow** · “Clever workflows” · “Automate every hand-off with visual, branching pipelines that move deals forward while you sleep.”
    2. **analytics** · “Live analytics” · “Real-time dashboards turn raw funnel data into the one number that tells you what to do next.”
    3. **leads** · “Seamless leads” · “Capture, score, and route every lead automatically — no spreadsheet, no leak, no lag.”
    4. **bolt** · “Instant triggers” · “Fire actions across your stack the moment intent appears, in milliseconds not minutes.”
    5. **shield** · “Enterprise-grade” · “SOC 2, SSO, and granular roles baked in — security that scales quietly with your team.”
    6. **globe** · “Global by default” · “Multi-region, multi-currency, and low-latency everywhere your customers happen to be.”
- **SECTION 3 · SHOWCASE + STATS (`<section class="section showcase" id="solutions">`)**:
  - `.showcase-grid` (`grid-template-columns:1fr 1.05fr`, gap `clamp(36px,6vw,72px)`, align-items center):
    - `.showcase-copy`: eyebrow (`--rd:0ms`) **“Built for momentum”**; `h2` (`--rd:90ms`) **“See the whole funnel move in real time”**; `p` (`--rd:180ms`) **“Every stage, every signal, every dollar — rendered live. Ascend gives operators the altitude to spot what’s working and the controls to double down instantly.”**; primary button (`--rd:270ms`, `href="{{ route('dashboard') }}"`) **“Explore the platform”** + arrow SVG.
    - `.dashboard` (`--rd:160ms`, padding 24px, radius `var(--radius-lg)`, bg `var(--glass-2)`, border `var(--line-strong)`, `var(--shadow)`, `backdrop-filter:blur(18px)`, `::after` gradient border `linear-gradient(160deg, rgba(93,240,168,.35), transparent 40%)` via mask-composite):
      - `.dash-top`: title **“Pipeline · Q3”** + `.dash-live` (mint dot + **“Live”**).
      - `.dash-bars` (flex, align-end, gap 10px, height 150px, bg `rgba(0,0,0,.25)`, border `var(--line)`, radius 14px, padding 16px): seven `.dash-bar` elements with heights **62%, 88%, 47%, 95%, 71%, 80%, 58%**, gradient `linear-gradient(180deg,var(--accent),rgba(47,211,138,.25))`, radius `6px 6px 3px 3px`, `grow` keyframe (`scaleY(0)->1`, origin bottom, delayed `index * 90ms`).
      - `.dash-rows`: 4 rows (`grid-template-columns:120px 1fr`, gap 14px, 13.5px, `var(--muted)`):
        - **New leads** (`--w:90%`), **Qualified** (`--w:72%`), **In negotiation** (`--w:48%`), **Closed won** (`--w:34%`), each with `.dash-track` (8px height, radius 999px, bg `rgba(255,255,255,.06)`) and `.dash-fill` (`linear-gradient(90deg,var(--accent-2),var(--accent))`, width `0 -> var(--w)`, `.3s` delay).
  - `.stats` (margin-top `clamp(56px,8vw,96px)`, `repeat(4,1fr)`, gap 18px): 4 `.stat` cards staggered `index * 80ms` (`0ms`, `80ms`, `160ms`, `240ms`):
    - **3.4×** / Faster pipeline velocity
    - **92%** / Lead capture accuracy
    - **11k+** / Teams scaling on Ascend
    - **$2.1B** / Revenue influenced
- **SECTION 4 · CTA + FOOTER (`<section class="section cta" id="cta">`)**:
  - `.cta-card` (centered, max-width 760px, padding `clamp(48px,7vw,80px) clamp(28px,5vw,64px)`, radius `var(--radius-lg)`, `background:radial-gradient(120% 140% at 50% 0%, rgba(93,240,168,.12), transparent 55%), var(--glass-2)`, border `var(--line-strong)`, `var(--shadow)`, blur 18px):
    - Eyebrow (`--rd:60ms`) **“Ready when you are”**; `h2` (`--rd:140ms`) **“Put your growth on a new trajectory”**; `p` (`--rd:220ms`) **“Launch in minutes. No credit card. Cancel anytime. Join thousands of teams compounding revenue on Ascend.”**; `.hero-actions` (`--rd:300ms`, margin-top 28px) with primary button **“Start Free Right Now”** (`href="{{ route('register') }}"`) + arrow SVG and outline button **“Talk to Sales”** (`href="{{ route('leads.index') }}"`).
  - `.footer` (flex space-between, gap 48px, wrap, margin-top `clamp(64px,9vw,110px)`, padding-top 48px, `border-top:1px solid var(--line)`):
    - `.footer-brand`: `▲ Ascend` + “The revenue command center for ambitious teams.”
    - `.footer-cols` (3 columns: **Product**: Features, Solutions, Plans · **Company**: About, Careers, Blog · **Legal**: Privacy, Terms, Security).
  - `.copyright` (centered, margin-top 56px, 13px, `var(--muted-2)`): `© <span id="year"></span> Ascend, Inc. — Built among the stars.` (`new Date().getFullYear()`).

### 5. WebGL Planet Scene (`public/js/ascend-planet.js` — Port Verbatim)
Write `public/js/ascend-planet.js` as an ES module implementing the exact WebGL specification:
- **Assets**:
  - `ASSET_BASE_URL = 'https://api.getlayers.ai/storage/v1/object/public/public/assets/ascend-d9857ad1f2'`
  - `PLANET_GLB = `${ASSET_BASE_URL}/planet.glb`` (loaded with `GLTFLoader` + `DRACOLoader` using decoder `'https://www.gstatic.com/draco/versioned/decoders/1.5.5/'`)
  - `PLANET_LIGHTS_GLB = `${ASSET_BASE_URL}/planet-lights.glb``
  - `PLANET_CLOUDS_PNG = `${ASSET_BASE_URL}/planet-clouds.png``
- **Exact `CONFIG` and `LAYERS` constants**:
  ```js
  const CONFIG = {
    rimColor:'#c1faff', rimPower:2.4, nightLights:10, terrainDepth:0.33, terrainShade:1.3,
    oceanGlint:0.45, oceanDeep:0.12, oceanFlow:3, oceanFlowSpeed:0.8, oceanFlowScale:2.1,
    glowColor:'#3a6cff', glowIntensity:3.35, planetRadius:1.95, spin:0.03, initRotation:2.07,
    tilt:0.37, autoRotate:0,
    cloud1Height:1.005, cloud1Opacity:0.6, cloud1Spin:0.06,
    cloud2Height:1.03, cloud2Opacity:0.5, cloud2Spin:0.14,
    cloud3Height:1.075, cloud3Opacity:0.5, cloud3Spin:0.1,
    bgColor:'#040a1e', flameColor:'#3a6cff', flameColor2:'#c1faff', flameAmt:0.15,
    atmoColor:'#9fc4ff', atmoCount:320, atmoSize:22, atmoSpeed:0.8,
    starColor:'#cfe0ff', starCount:1400, starSize:1.6, starFlicker:1,
    markerColor:'#ffd27a', markerCount:60, markerSize:16, markerSpeed:0.5,
  };
  const LAYERS = { NONE:0, TORUS_SCENE:1, BLOOM_SCENE:2, ENTIRE_SCENE:3 };
  ```
- **Exact Shaders & Post-Processing**:
  - Shared 3D simplex noise (`permute`, `taylorInvSqrt`, `snoise`).
  - `THREE.WebGL1Renderer({ canvas, antialias:true })`, `outputEncoding = THREE.sRGBEncoding`, `VSMShadowMap`.
  - 3-composer chain: `torusComposer`, `bloomComposer`, `finalComposer` with `FinalPass` (`warp3d` corner flame + bg + halo + bloom + torus + diffuse).
  - Planet material `onBeforeCompile` shader injection (rim, blueDom `waterMask`, `shimmer`, `oceanFlow`, `oceanDeep`, screen-space derivative bump relief `dFdx`/`dFdy`, `nightBlendTexture` city lights, specular `oceanGlint`).
  - Atmosphere halo billboard (`PlaneGeometry(2,2)` scaled `planetRadius*2.3`, `AdditiveBlending`).
  - 3 Cloud shells (`SphereGeometry(planetRadius * h, 64, 64)`, `RepeatWrapping` 5×5, `onBeforeCompile` simplex noise + limb edge + day/night fade).
  - Ambient atmosphere motes (`320` points, `warp` vertex shader, follows camera, updates `finalPass.uniforms.iTime`).
  - Starfield (`1400` points on sphere `r=90` with sinusoidal flicker).
  - Land markers (`60` golden radar-ping points sampled via cumulative triangle area binary search over the planet mesh, rejecting ocean pixels where `cb > cr+6 && cb > cg+6`, lifted `×1.012`, animated ring + core shader).
  - Scroll keyframes (`STOPS_X`, `STOPS_Y`, `STOPS_S`), one-time float-up entrance (`ENTRY_DUR=1.9`, `ENTRY_START_Y=-6.5`, cubic ease-out `1-(1-t)^3`), Lenis smooth scroll (`duration:1.15, smoothWheel:true, touchMultiplier:1.5`), and `IntersectionObserver` for `[data-reveal]`.
</exact_frontend_and_webgl_specification>

<execution_plan>
Execute the build in **4 clean phases**:
- **Phase 1 (Foundation & Static Assets)**:
  1. Configure `.env` for SQLite (`DB_CONNECTION=sqlite`) so it runs immediately out of the box.
  2. Create `public/css/ascend.css` (exact Ascend CSS + glassmorphic CRUD table/form/badge styles).
  3. Create `public/js/ascend-planet.js` (complete verbatim WebGL planet, shaders, Lenis, and reveal observer). **Do not re-read this file after creation.**
- **Phase 2 (Database Schema, Models & Seeders)**:
  1. Create migrations for `users` (with `role`, `region`), `pipelines`, `leads` (with FKs `user_id`, `pipeline_id`), and `lead_activities` (with FKs `lead_id`, `user_id`).
  2. Create `User`, `Pipeline`, `Lead` (with `scopeFilter`), and `LeadActivity` models.
  3. Create `DatabaseSeeder` seeding a demo operator (`operator@ascend.io` / `password`), 4 global pipelines (`North America Enterprise`, `EMEA Mid-Market`, `APAC Velocity`, `LATAM Expansion`), 12 realistic SaaS deals across all 4 stages (`new_lead`, `qualified`, `in_negotiation`, `closed_won`) matching the Q3 metrics, and 20 deal activity logs.
  4. Run `php artisan migrate:fresh --seed`.
- **Phase 3 (Controllers, Requests & Routes)**:
  1. Create `AuthController` (Login, Register, Logout).
  2. Create `DashboardController` (Feature 1), `PipelineController` (Feature 2), `LeadController` (Features 3, 4, 5), `LeadActivityController` (Feature 6), `LeadExportController` (Feature 7), and `ProfileController` (Feature 8).
  3. Create `StoreLeadRequest`, `UpdateLeadRequest`, and `StorePipelineRequest`.
  4. Wire all routes cleanly in `routes/web.php`.
- **Phase 4 (Blade Views & Verification)**:
  1. Create `resources/views/layouts/ascend.blade.php` (shared WebGL canvas background + top Command Center navbar + flash alerts).
  2. Create `resources/views/welcome.blade.php` (exact 4-section Ascend landing page).
  3. Create modular Blade views (< 150 lines each) for Auth (`auth/login`, `auth/register`), Dashboard (`dashboard`), Pipelines CRUD (`pipelines/index`, `pipelines/form`), Leads CRUD + Filter + Export (`leads/index`, `leads/form`, `leads/show`, `leads/print`), and Profile (`profile/edit`).
  4. Verify all routes with `php artisan route:list` and test responses.
</execution_plan>
