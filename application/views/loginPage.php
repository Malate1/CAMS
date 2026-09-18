<!DOCTYPE html>
<html lang="en" class="h-full scroll-smooth">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CAMS | HNU Medical Center</title>
  <meta name="description" content="HNU Medical Center Clinics Appointment Management System">
  <meta name="theme-color" content="#0b3f31">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="<?=base_url()?>assets/fonts/google-sans/google-sans.css?v=<?=@filemtime(FCPATH.'assets/fonts/google-sans/google-sans.css')?>">

  <script>
    tailwind = window.tailwind || {};
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          fontFamily: {
            sans: ['Google Sans', 'Arial', 'sans-serif']
          },
          colors: {
            cams: {
              50: '#eefaf5',
              100: '#d7f3e6',
              200: '#b2e6d1',
              300: '#7fd1b3',
              400: '#49b68f',
              500: '#259b73',
              600: '#147a59',
              700: '#0f6249',
              800: '#0d4f3c',
              900: '#0b3f31',
              950: '#05251d'
            }
          },
          boxShadow: {
            soft: '0 18px 50px rgba(15, 23, 42, .08)',
            lift: '0 26px 70px rgba(15, 23, 42, .14)'
          }
        }
      }
    };
  </script>
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    [x-cloak]{display:none!important}
    body {
      font-family: "Google Sans", Arial, sans-serif;
    }
  </style>

  <script>
    (function () {
      try {
        var saved = localStorage.getItem('cams-theme') || 'light';
        if (saved === 'dark') document.documentElement.classList.add('dark');
      } catch (e) {}
    })();

    function toggleCamsTheme() {
      var dark = document.documentElement.classList.toggle('dark');
      try { localStorage.setItem('cams-theme', dark ? 'dark' : 'light'); } catch (e) {}
      updateThemeLabel();
    }

    function updateThemeLabel() {
      var label = document.getElementById('cams-theme-label');
      if (label) label.textContent = document.documentElement.classList.contains('dark') ? 'Light' : 'Dark';
    }

    document.addEventListener('DOMContentLoaded', updateThemeLabel);
  </script>
</head>

<body class="min-h-full bg-slate-50 text-slate-900 antialiased transition-colors dark:bg-slate-950 dark:text-slate-100">
  <div class="min-h-screen">
    <header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/90 backdrop-blur-xl dark:border-slate-800 dark:bg-slate-950/85">
      <div class="flex w-full items-center justify-between px-5 py-3 sm:px-8 lg:px-12">
        <a href="<?=base_url()?>" class="flex min-w-0 items-center gap-3 no-underline">
          <span class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-emerald-100 bg-white p-1 shadow-sm dark:border-slate-700">
            <img src="<?=base_url()?>assets/dist/img/hnumcfi.jpg" alt="HNU Medical Center" class="h-full w-full rounded-xl object-cover">
          </span>
          <span class="min-w-0">
            <strong class="block truncate text-lg font-extrabold tracking-tight text-slate-950 dark:text-white">CAMS</strong>
            <span class="hidden truncate text-xs font-semibold uppercase tracking-[0.13em] text-slate-500 sm:block dark:text-slate-400">HNU Medical Center</span>
          </span>
        </a>

        <div class="flex items-center gap-2">
          <span class="hidden items-center gap-2 rounded-full border border-emerald-100 bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 md:inline-flex dark:border-emerald-900 dark:bg-emerald-950/50 dark:text-emerald-300">
            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
            Clinic access portal
          </span>

          <button
            type="button"
            onclick="toggleCamsTheme()"
            class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 text-sm font-bold text-slate-700 transition hover:border-emerald-300 hover:text-emerald-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:border-emerald-700 dark:hover:text-emerald-300"
            aria-label="Toggle color theme"
          >
            <svg class="h-4 w-4 dark:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.8A9 9 0 1 1 11.2 3 7 7 0 0 0 21 12.8Z"/>
            </svg>
            <svg class="hidden h-4 w-4 dark:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <circle cx="12" cy="12" r="4"/>
              <path stroke-linecap="round" d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>
            </svg>
            <span id="cams-theme-label">Dark</span>
          </button>
        </div>
      </div>
    </header>

    <main>
      <section class="px-4 pb-8 pt-4 sm:px-6 lg:px-8 lg:pb-10">
        <div class="relative overflow-hidden rounded-[2rem] bg-cams-950 shadow-lift">
          <div class="absolute inset-0">
            <img src="<?=base_url()?>hnu/clinic.jpg" alt="" class="h-full w-full object-cover opacity-55">
            <div class="absolute inset-0 bg-gradient-to-r from-cams-950 via-cams-950/92 to-cams-900/45"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_78%_18%,rgba(73,182,143,.24),transparent_34%)]"></div>
          </div>

          <div class="relative grid min-h-[500px] items-center gap-10 px-6 py-12 sm:px-10 lg:grid-cols-[1.05fr_.95fr] lg:px-14 lg:py-16 xl:min-h-[560px] xl:px-20">
            <div class="max-w-3xl">
              <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-2 text-xs font-bold uppercase tracking-[0.14em] text-emerald-100 backdrop-blur">
                <span class="h-2 w-2 rounded-full bg-emerald-300"></span>
                HNU Medical Center
              </div>

              <h1 class="m-0 max-w-3xl text-4xl font-extrabold leading-[1.03] tracking-[-0.045em] text-white sm:text-5xl lg:text-6xl xl:text-7xl">
                Clinic appointments, organized around better care.
              </h1>

              <p class="mt-6 max-w-2xl text-base leading-7 text-emerald-50/80 sm:text-lg">
                CAMS connects patients, physicians, secretaries, and administrators in one appointment workflow—from booking and clinic schedules to visit tracking and reports.
              </p>

              <div class="mt-8 flex flex-wrap gap-3">
                <a href="<?=base_url('login-p')?>" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-white px-5 text-sm font-extrabold text-cams-900 shadow-lg transition hover:-translate-y-0.5 hover:bg-emerald-50 no-underline">
                  Patient Login
                  <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/>
                  </svg>
                </a>
                <a href="#portals" class="inline-flex min-h-12 items-center justify-center rounded-xl border border-white/20 bg-white/10 px-5 text-sm font-bold text-white backdrop-blur transition hover:bg-white/15 no-underline">
                  View all portals
                </a>
              </div>

              <div class="mt-10 grid max-w-2xl grid-cols-1 gap-3 sm:grid-cols-3">
                <div class="rounded-2xl border border-white/10 bg-white/8 p-4 backdrop-blur">
                  <strong class="block text-base font-extrabold text-white">Schedule-aware</strong>
                  <span class="mt-1 block text-sm leading-5 text-emerald-50/65">Appointments follow physician clinic schedules.</span>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/8 p-4 backdrop-blur">
                  <strong class="block text-base font-extrabold text-white">Role-based</strong>
                  <span class="mt-1 block text-sm leading-5 text-emerald-50/65">Each user sees the workflow relevant to them.</span>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/8 p-4 backdrop-blur">
                  <strong class="block text-base font-extrabold text-white">Centralized</strong>
                  <span class="mt-1 block text-sm leading-5 text-emerald-50/65">Bookings, clinics, schedules, and reports in one system.</span>
                </div>
              </div>
            </div>

            <div class="hidden lg:block">
              <div class="ml-auto max-w-md rounded-[1.75rem] border border-white/15 bg-white/10 p-5 shadow-2xl backdrop-blur-xl">
                <div class="rounded-2xl bg-white p-5 dark:bg-slate-900">
                  <div class="flex items-center justify-between gap-4">
                    <div>
                      <span class="block text-xs font-bold uppercase tracking-[0.14em] text-emerald-700 dark:text-emerald-400">Patient journey</span>
                      <h2 class="mt-1 text-xl font-extrabold tracking-tight text-slate-950 dark:text-white">From concern to confirmed visit</h2>
                    </div>
                    <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                      <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M5 11h14M6 5h12a1 1 0 0 1 1 1v14H5V6a1 1 0 0 1 1-1Z"/>
                      </svg>
                    </span>
                  </div>

                  <div class="mt-6 space-y-3">
                    <div class="flex items-start gap-3 rounded-xl bg-slate-50 p-3 dark:bg-slate-800/70">
                      <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-cams-700 text-xs font-extrabold text-white">1</span>
                      <div><strong class="block text-sm text-slate-900 dark:text-white">Describe the concern</strong><span class="text-sm text-slate-500 dark:text-slate-400">CAMS narrows relevant specialties.</span></div>
                    </div>
                    <div class="flex items-start gap-3 rounded-xl bg-slate-50 p-3 dark:bg-slate-800/70">
                      <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-cams-700 text-xs font-extrabold text-white">2</span>
                      <div><strong class="block text-sm text-slate-900 dark:text-white">Choose physician and clinic</strong><span class="text-sm text-slate-500 dark:text-slate-400">Only relevant clinic assignments are shown.</span></div>
                    </div>
                    <div class="flex items-start gap-3 rounded-xl bg-slate-50 p-3 dark:bg-slate-800/70">
                      <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-cams-700 text-xs font-extrabold text-white">3</span>
                      <div><strong class="block text-sm text-slate-900 dark:text-white">Select a valid date</strong><span class="text-sm text-slate-500 dark:text-slate-400">Availability respects schedules and daily limits.</span></div>
                    </div>
                  </div>

                  <a href="<?=base_url('login-p')?>" class="mt-5 inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-cams-700 px-4 text-sm font-extrabold text-white transition hover:bg-cams-800 no-underline">
                    Continue to Patient Portal
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section id="portals" class="px-4 py-10 sm:px-6 lg:px-8 lg:py-14">
        <div class="w-full">
          <div class="mb-7 max-w-3xl">
            <span class="text-xs font-extrabold uppercase tracking-[0.15em] text-cams-700 dark:text-emerald-400">Choose your workspace</span>
            <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950 dark:text-white sm:text-4xl">One system, four role-specific portals</h2>
            <p class="mt-3 text-base leading-7 text-slate-500 dark:text-slate-400">Sign in to the workspace designed for your role. Existing CAMS authentication and account logic remain unchanged.</p>
          </div>

          <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <article class="group flex min-h-[280px] flex-col rounded-[1.5rem] border border-emerald-200 bg-gradient-to-br from-emerald-50 to-white p-6 shadow-soft transition hover:-translate-y-1 hover:shadow-lift dark:border-emerald-900/70 dark:from-emerald-950/40 dark:to-slate-900">
              <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-700 text-white shadow-sm">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19a6 6 0 0 0-12 0m6-8a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm6 1h6m-3-3v6"/></svg>
              </span>
              <span class="mt-5 text-xs font-extrabold uppercase tracking-[0.13em] text-emerald-700 dark:text-emerald-400">Patient</span>
              <h3 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-950 dark:text-white">Book and manage care</h3>
              <p class="mt-3 flex-1 text-sm leading-6 text-slate-600 dark:text-slate-400">Book appointments, review your visit history, and manage your patient account.</p>
              <a href="<?=base_url('login-p')?>" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl bg-emerald-700 px-4 text-sm font-extrabold text-white transition hover:bg-emerald-800 no-underline">Patient Login</a>
            </article>

            <article class="group flex min-h-[280px] flex-col rounded-[1.5rem] border border-slate-200 bg-white p-6 shadow-soft transition hover:-translate-y-1 hover:border-emerald-200 hover:shadow-lift dark:border-slate-800 dark:bg-slate-900 dark:hover:border-emerald-900">
              <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-3-3v6m7 5V8l-7-5-7 5v12h14Z"/></svg>
              </span>
              <span class="mt-5 text-xs font-extrabold uppercase tracking-[0.13em] text-blue-700 dark:text-blue-300">Physician</span>
              <h3 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-950 dark:text-white">Manage clinical workload</h3>
              <p class="mt-3 flex-1 text-sm leading-6 text-slate-600 dark:text-slate-400">Review daily queues, clinic schedules, appointment outcomes, limits, and reports.</p>
              <a href="<?=base_url('login-phy')?>" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-extrabold text-slate-800 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-800 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:hover:border-blue-800 dark:hover:bg-blue-950/40 no-underline">Physician Login</a>
            </article>

            <article class="group flex min-h-[280px] flex-col rounded-[1.5rem] border border-slate-200 bg-white p-6 shadow-soft transition hover:-translate-y-1 hover:border-amber-200 hover:shadow-lift dark:border-slate-800 dark:bg-slate-900 dark:hover:border-amber-900">
              <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5h16v14H4zM8 3v4m8-4v4M8 11h8m-8 4h5"/></svg>
              </span>
              <span class="mt-5 text-xs font-extrabold uppercase tracking-[0.13em] text-amber-700 dark:text-amber-300">Secretary</span>
              <h3 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-950 dark:text-white">Coordinate clinic flow</h3>
              <p class="mt-3 flex-1 text-sm leading-6 text-slate-600 dark:text-slate-400">Support physicians, organize patient bookings, maintain schedules, and track clinic activity.</p>
              <a href="<?=base_url('login-s')?>" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-extrabold text-slate-800 transition hover:border-amber-300 hover:bg-amber-50 hover:text-amber-800 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:hover:border-amber-800 dark:hover:bg-amber-950/40 no-underline">Secretary Login</a>
            </article>

            <article class="group flex min-h-[280px] flex-col rounded-[1.5rem] border border-slate-200 bg-white p-6 shadow-soft transition hover:-translate-y-1 hover:border-violet-200 hover:shadow-lift dark:border-slate-800 dark:bg-slate-900 dark:hover:border-violet-900">
              <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-50 text-violet-700 dark:bg-violet-950/50 dark:text-violet-300">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19V9l8-5 8 5v10H4Zm4-6h8m-8 3h8"/></svg>
              </span>
              <span class="mt-5 text-xs font-extrabold uppercase tracking-[0.13em] text-violet-700 dark:text-violet-300">Administrator</span>
              <h3 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-950 dark:text-white">Oversee the whole system</h3>
              <p class="mt-3 flex-1 text-sm leading-6 text-slate-600 dark:text-slate-400">Manage users and clinics, review reports, maintain assignments, and monitor system logs.</p>
              <a href="<?=base_url('login-a')?>" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-extrabold text-slate-800 transition hover:border-violet-300 hover:bg-violet-50 hover:text-violet-800 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:hover:border-violet-800 dark:hover:bg-violet-950/40 no-underline">Admin Login</a>
            </article>
          </div>
        </div>
      </section>

      <section class="px-4 pb-14 sm:px-6 lg:px-8">
        <div class="grid gap-4 rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-soft sm:grid-cols-3 lg:p-8 dark:border-slate-800 dark:bg-slate-900">
          <div>
            <span class="text-xs font-extrabold uppercase tracking-[0.14em] text-cams-700 dark:text-emerald-400">Appointments</span>
            <h3 class="mt-1 text-lg font-extrabold text-slate-950 dark:text-white">Schedule-safe booking</h3>
            <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">Booking dates follow physician schedules and daily appointment limits.</p>
          </div>
          <div>
            <span class="text-xs font-extrabold uppercase tracking-[0.14em] text-cams-700 dark:text-emerald-400">Operations</span>
            <h3 class="mt-1 text-lg font-extrabold text-slate-950 dark:text-white">Shared clinic workflow</h3>
            <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">Clinic assignments, schedules, and patient queues stay connected across roles.</p>
          </div>
          <div>
            <span class="text-xs font-extrabold uppercase tracking-[0.14em] text-cams-700 dark:text-emerald-400">Reporting</span>
            <h3 class="mt-1 text-lg font-extrabold text-slate-950 dark:text-white">Useful operational insight</h3>
            <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">Role-based dashboards and reports turn appointment records into practical breakdowns.</p>
          </div>
        </div>
      </section>
    </main>

    <footer class="border-t border-slate-200 bg-white px-5 py-6 dark:border-slate-800 dark:bg-slate-950 sm:px-8 lg:px-12">
      <div class="flex flex-col gap-3 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between dark:text-slate-400">
        <div>
          <strong class="font-extrabold text-slate-800 dark:text-slate-200">HNU Medical Center CAMS</strong>
          <span class="ml-2">Version 2.0</span>
        </div>
        <div>Copyright &copy; 2018 &middot; All rights reserved.</div>
      </div>
    </footer>
  </div>
</body>
</html>
