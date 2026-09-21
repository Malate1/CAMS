<?php
$landingClinics = isset($landingClinics) && is_array($landingClinics) ? $landingClinics : array();
$landingScheduleCount = isset($landingScheduleCount) ? (int) $landingScheduleCount : 0;
$landingPhysicianCount = isset($landingPhysicianCount) ? (int) $landingPhysicianCount : 0;

if (!function_exists('cams_landing_e')) {
    function cams_landing_e($value) {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('cams_landing_day')) {
    function cams_landing_day($day) {
        $map = array(
            'M-F' => 'Mon–Fri',
            'MTW' => 'Mon–Wed',
            'ThF' => 'Thu–Fri',
            'Sat' => 'Saturday',
            'Sun' => 'Sunday',
        );
        return isset($map[$day]) ? $map[$day] : $day;
    }
}

if (!function_exists('cams_landing_time')) {
    function cams_landing_time($time) {
        $stamp = strtotime((string) $time);
        return $stamp ? date('g:i A', $stamp) : $time;
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full scroll-smooth">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CAMS | HNU Medical Center</title>
  <meta name="description" content="HNU Medical Center Clinics Appointment Management System">
  <meta name="theme-color" content="#ffffff">

  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="<?=base_url()?>assets/fonts/google-sans/google-sans.css?v=<?=@filemtime(FCPATH.'assets/fonts/google-sans/google-sans.css')?>">
  <link rel="stylesheet" href="<?=base_url()?>assets/bower_components/select2/dist/css/select2.min.css">
  <link rel="stylesheet" href="<?=base_url()?>css/cams-select2.css?v=<?=@filemtime(FCPATH.'css/cams-select2.css')?>">

  <script>
    tailwind = window.tailwind || {};
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['Google Sans', 'Arial', 'sans-serif']
          },
          colors: {
            cams: {
              50: '#eefbf5',
              100: '#d7f5e8',
              200: '#afe9d1',
              500: '#1f9d70',
              600: '#16845e',
              700: '#126b4d',
              800: '#0d503b',
              900: '#0b2d23',
              950: '#061d17'
            }
          },
          boxShadow: {
            soft: '0 14px 40px rgba(15,23,42,.08)',
            lift: '0 24px 60px rgba(15,23,42,.12)'
          }
        }
      }
    };
  </script>
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    body { font-family: "Google Sans", Arial, sans-serif; }
    [data-schedule-card][hidden] { display: none !important; }
    .cams-glass-card {
      background: rgba(255,255,255,.62);
      border: 1px solid rgba(255,255,255,.82);
      box-shadow: 0 18px 45px rgba(15,23,42,.08);
      backdrop-filter: blur(18px);
      -webkit-backdrop-filter: blur(18px);
    }
    .cams-glass-card:hover {
      background: rgba(255,255,255,.76);
      border-color: rgba(16,185,129,.28);
      box-shadow: 0 24px 55px rgba(15,23,42,.12);
    }
  </style>
</head>

<body class="min-h-full bg-slate-50 text-slate-800 antialiased">
  <div class="min-h-screen">
    <header class="sticky top-0 z-50 border-b border-slate-200 bg-white/95 shadow-sm backdrop-blur-xl">
      <div class="flex min-h-[64px] w-full items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <a href="<?=base_url()?>" class="flex min-w-0 items-center gap-3 no-underline">
          <span class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-emerald-100 bg-white p-1 shadow-sm">
            <img src="<?=base_url()?>assets/dist/img/hnumcfi.jpg" alt="HNU Medical Center" class="h-full w-full rounded-lg object-cover">
          </span>
          <span class="min-w-0">
            <span class="block text-[11px] font-bold uppercase tracking-[0.14em] text-slate-500">Clinics Appointment Management System</span>
            <strong class="block truncate text-base font-extrabold tracking-tight text-slate-950">HNU Medical Center</strong>
          </span>
        </a>

        <a href="#schedules" class="hidden min-h-10 items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 text-sm font-bold text-slate-600 transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700 md:inline-flex">
          Clinic Schedules
        </a>
      </div>
    </header>

    <main>
      <section class="px-4 pb-8 pt-5 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-[2rem] shadow-lift">
          <div class="absolute inset-0">
            <img src="<?=base_url()?>hnu/clinic.jpg" alt="HNU Medical Center clinic" class="h-full w-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-slate-950/88 via-slate-950/68 to-slate-950/30"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_75%_18%,rgba(34,197,94,.18),transparent_34%)]"></div>
          </div>

          <div class="relative grid min-h-[520px] items-center gap-10 px-6 py-12 sm:px-10 lg:grid-cols-[1.04fr_.96fr] lg:px-14 lg:py-16 xl:px-20">
            <div class="max-w-3xl">
              <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-2 text-xs font-bold uppercase tracking-[0.14em] text-emerald-100 backdrop-blur">
                <span class="h-2 w-2 rounded-full bg-emerald-300"></span>
                HNU Medical Center CAMS
              </div>

              <h1 class="m-0 max-w-3xl text-4xl font-extrabold leading-[1.03] tracking-[-0.045em] text-white sm:text-5xl lg:text-6xl">
                Find the right clinic schedule before you book.
              </h1>

              <p class="mt-6 max-w-2xl text-base leading-7 text-slate-100/85 sm:text-lg">
                Review physician availability by clinic, then sign in to CAMS to book, manage, and track your appointments.
              </p>

              <div class="mt-8 flex flex-wrap gap-3">
                <a href="<?=base_url('login-p')?>" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-white px-5 text-sm font-extrabold text-emerald-800 shadow-lg transition hover:-translate-y-0.5 hover:bg-emerald-50">
                  Patient Portal
                  <span aria-hidden="true">→</span>
                </a>
                <a href="#schedules" class="inline-flex min-h-12 items-center justify-center rounded-xl border border-white/25 bg-white/10 px-5 text-sm font-bold text-white backdrop-blur transition hover:bg-white/15">
                  View Clinic Schedules
                </a>
              </div>

              <div class="mt-10 grid max-w-2xl grid-cols-1 gap-3 sm:grid-cols-3">
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                  <strong class="block text-2xl font-extrabold text-white"><?=number_format(count($landingClinics))?></strong>
                  <span class="mt-1 block text-sm text-slate-200">Clinics with schedules</span>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                  <strong class="block text-2xl font-extrabold text-white"><?=number_format($landingPhysicianCount)?></strong>
                  <span class="mt-1 block text-sm text-slate-200">Active physicians</span>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                  <strong class="block text-2xl font-extrabold text-white"><?=number_format($landingScheduleCount)?></strong>
                  <span class="mt-1 block text-sm text-slate-200">Schedule entries</span>
                </div>
              </div>
            </div>

            <div class="hidden lg:block">
              <div class="ml-auto max-w-md rounded-[1.75rem] border border-white/20 bg-white/12 p-5 shadow-2xl backdrop-blur-xl">
                <div class="rounded-2xl bg-white/92 p-5 shadow-sm backdrop-blur">
                  <div class="flex items-center justify-between gap-4">
                    <div>
                      <span class="block text-xs font-bold uppercase tracking-[0.14em] text-emerald-700">Patient journey</span>
                      <h2 class="mt-1 text-xl font-extrabold tracking-tight text-slate-950">From concern to confirmed visit</h2>
                    </div>
                    <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700">
                      <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M5 11h14M6 5h12a1 1 0 0 1 1 1v14H5V6a1 1 0 0 1 1-1Z"/>
                      </svg>
                    </span>
                  </div>

                  <div class="mt-6 space-y-3">
                    <div class="flex items-start gap-3 rounded-xl bg-slate-50 p-3">
                      <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-700 text-xs font-extrabold text-white">1</span>
                      <div>
                        <strong class="block text-sm text-slate-900">Describe the concern</strong>
                        <span class="text-sm text-slate-500">CAMS narrows relevant specialties.</span>
                      </div>
                    </div>
                    <div class="flex items-start gap-3 rounded-xl bg-slate-50 p-3">
                      <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-700 text-xs font-extrabold text-white">2</span>
                      <div>
                        <strong class="block text-sm text-slate-900">Choose physician and clinic</strong>
                        <span class="text-sm text-slate-500">Only relevant clinic assignments are shown.</span>
                      </div>
                    </div>
                    <div class="flex items-start gap-3 rounded-xl bg-slate-50 p-3">
                      <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-700 text-xs font-extrabold text-white">3</span>
                      <div>
                        <strong class="block text-sm text-slate-900">Select a valid date</strong>
                        <span class="text-sm text-slate-500">Availability follows schedules and daily limits.</span>
                      </div>
                    </div>
                  </div>

                  <a href="<?=base_url('login-p')?>" class="mt-5 inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-emerald-700 px-4 text-sm font-extrabold text-white transition hover:bg-emerald-800">
                    Continue to Patient Portal
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section id="portals" class="relative overflow-hidden px-4 py-10 sm:px-6 lg:px-8 lg:py-14">
        <div class="pointer-events-none absolute -left-24 top-16 h-72 w-72 rounded-full bg-emerald-100/70 blur-3xl"></div>
        <div class="pointer-events-none absolute -right-24 bottom-0 h-80 w-80 rounded-full bg-sky-100/60 blur-3xl"></div>

        <div class="relative">
          <div class="mb-7 max-w-3xl">
            <span class="text-xs font-extrabold uppercase tracking-[0.15em] text-emerald-700">Choose your workspace</span>
            <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">One system, four role-specific portals</h2>
            <p class="mt-3 text-base leading-7 text-slate-500">Sign in to the workspace designed for your role. Each portal uses the same CAMS workflow with the permissions and tools relevant to that user.</p>
          </div>

          <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
            <article class="cams-glass-card group flex min-h-[300px] flex-col rounded-[1.6rem] p-6 transition duration-200 hover:-translate-y-1">
              <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-700 text-white shadow-sm">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19a6 6 0 0 0-12 0m6-8a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm6 1h6m-3-3v6"/></svg>
              </span>
              <span class="mt-5 text-xs font-extrabold uppercase tracking-[0.13em] text-emerald-700">Patient</span>
              <h3 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-950">Book and manage care</h3>
              <p class="mt-3 flex-1 text-sm leading-6 text-slate-600">Book appointments, review your visit history, and manage your patient account.</p>
              <a href="<?=base_url('login-p')?>" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl bg-emerald-700 px-4 text-sm font-extrabold text-white transition hover:bg-emerald-800">
                Patient Login
              </a>
            </article>

            <article class="cams-glass-card group flex min-h-[300px] flex-col rounded-[1.6rem] p-6 transition duration-200 hover:-translate-y-1">
              <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/80 text-emerald-700 shadow-sm ring-1 ring-slate-200/80">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-3-3v6m7 5V8l-7-5-7 5v12h14Z"/></svg>
              </span>
              <span class="mt-5 text-xs font-extrabold uppercase tracking-[0.13em] text-emerald-700">Physician</span>
              <h3 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-950">Manage clinical workload</h3>
              <p class="mt-3 flex-1 text-sm leading-6 text-slate-600">Review daily queues, clinic schedules, appointment outcomes, limits, and reports.</p>
              <a href="<?=base_url('login-phy')?>" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-200/90 bg-white/75 px-4 text-sm font-extrabold text-slate-800 transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-800">
                Physician Login
              </a>
            </article>

            <article class="cams-glass-card group flex min-h-[300px] flex-col rounded-[1.6rem] p-6 transition duration-200 hover:-translate-y-1">
              <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/80 text-emerald-700 shadow-sm ring-1 ring-slate-200/80">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5h16v14H4zM8 3v4m8-4v4M8 11h8m-8 4h5"/></svg>
              </span>
              <span class="mt-5 text-xs font-extrabold uppercase tracking-[0.13em] text-emerald-700">Secretary</span>
              <h3 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-950">Coordinate clinic flow</h3>
              <p class="mt-3 flex-1 text-sm leading-6 text-slate-600">Support physicians, organize patient bookings, maintain schedules, and track clinic activity.</p>
              <a href="<?=base_url('login-s')?>" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-200/90 bg-white/75 px-4 text-sm font-extrabold text-slate-800 transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-800">
                Secretary Login
              </a>
            </article>

            <article class="cams-glass-card group flex min-h-[300px] flex-col rounded-[1.6rem] p-6 transition duration-200 hover:-translate-y-1">
              <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/80 text-emerald-700 shadow-sm ring-1 ring-slate-200/80">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19V9l8-5 8 5v10H4Zm4-6h8m-8 3h8"/></svg>
              </span>
              <span class="mt-5 text-xs font-extrabold uppercase tracking-[0.13em] text-emerald-700">Administrator</span>
              <h3 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-950">Oversee the whole system</h3>
              <p class="mt-3 flex-1 text-sm leading-6 text-slate-600">Manage users and clinics, review reports, maintain assignments, and monitor system logs.</p>
              <a href="<?=base_url('login-a')?>" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-200/90 bg-white/75 px-4 text-sm font-extrabold text-slate-800 transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-800">
                Admin Login
              </a>
            </article>
          </div>
        </div>
      </section>

      <section id="schedules" class="px-4 py-8 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
          <div class="border-b border-slate-200 px-5 py-5 sm:px-6 lg:px-7">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
              <div class="max-w-3xl">
                <span class="text-xs font-extrabold uppercase tracking-[0.14em] text-emerald-700">Public clinic schedule</span>
                <h2 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">Physician schedules by clinic</h2>
                <p class="mb-0 mt-2 text-sm leading-6 text-slate-500">Use this schedule as a guide before booking. Appointment dates are still validated against the current clinic schedule and daily limits inside CAMS.</p>
              </div>

              <div class="grid gap-3 sm:grid-cols-[minmax(230px,1fr)_220px] xl:w-[560px]">
                <div>
                  <label for="schedule-search" class="mb-1.5 block text-sm font-bold text-slate-700">Search clinic or physician</label>
                  <div class="relative">
                    <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">⌕</span>
                    <input id="schedule-search" type="search" placeholder="Search schedules..." class="min-h-11 w-full rounded-xl border border-slate-200 bg-white py-2 pl-10 pr-3.5 text-sm text-slate-800 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10">
                  </div>
                </div>

                <div>
                  <label for="schedule-clinic-filter" class="mb-1.5 block text-sm font-bold text-slate-700">Clinic</label>
                  <select id="schedule-clinic-filter" class="min-h-11 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-sm text-slate-800 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10">
                    <option value="">All clinics</option>
                    <?php foreach ($landingClinics as $clinic): ?>
                      <option value="<?=cams_landing_e($clinic['clinic_id'])?>"><?=cams_landing_e($clinic['name'])?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <div class="p-4 sm:p-5 lg:p-6">
            <?php if ($landingClinics): ?>
              <div id="schedule-grid" class="grid gap-4 xl:grid-cols-2">
                <?php foreach ($landingClinics as $clinic): ?>
                  <?php
                  $searchParts = array($clinic['name'], $clinic['location']);
                  foreach ($clinic['physicians'] as $physician) {
                      $searchParts[] = $physician['name'];
                  }
                  ?>
                  <article
                    data-schedule-card
                    data-clinic-id="<?=cams_landing_e($clinic['clinic_id'])?>"
                    data-search="<?=cams_landing_e(strtolower(implode(' ', $searchParts)))?>"
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-50/70"
                  >
                    <div class="flex flex-col gap-3 border-b border-slate-200 bg-white px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                      <div class="min-w-0">
                        <div class="flex items-center gap-2">
                          <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                            <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19V9l8-5 8 5v10H4Zm4-6h8m-8 3h8"/></svg>
                          </span>
                          <div class="min-w-0">
                            <h3 class="m-0 truncate text-lg font-extrabold text-slate-950"><?=cams_landing_e($clinic['name'])?></h3>
                            <p class="mb-0 mt-0.5 truncate text-sm text-slate-500"><?=cams_landing_e($clinic['location'] ?: 'Location not specified')?></p>
                          </div>
                        </div>
                      </div>

                      <?php if (!empty($clinic['contact'])): ?>
                        <span class="inline-flex w-fit items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-600">
                          <?=cams_landing_e($clinic['contact'])?>
                        </span>
                      <?php endif; ?>
                    </div>

                    <div class="divide-y divide-slate-200">
                      <?php foreach ($clinic['physicians'] as $physician): ?>
                        <?php $physicianImage = !empty($physician['image']) ? $physician['image'] : 'default.png'; ?>
                        <div class="p-5">
                          <div class="flex items-start gap-4">
                            <img src="<?=base_url('uploads/profile-pic/' . rawurlencode($physicianImage))?>" alt="<?=cams_landing_e($physician['name'])?>" class="h-12 w-12 shrink-0 rounded-xl border border-slate-200 bg-white object-cover shadow-sm">

                            <div class="min-w-0 flex-1">
                              <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                  <span class="text-xs font-bold uppercase tracking-[0.12em] text-emerald-700">Physician</span>
                                  <h4 class="m-0 mt-0.5 text-base font-extrabold text-slate-900">Dr. <?=cams_landing_e($physician['name'])?></h4>
                                </div>
                                <span class="text-xs font-semibold text-slate-400"><?=count($physician['schedules'])?> schedule<?=count($physician['schedules']) === 1 ? '' : 's'?></span>
                              </div>

                              <div class="mt-3 flex flex-wrap gap-2">
                                <?php foreach ($physician['schedules'] as $schedule): ?>
                                  <span class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
                                    <strong class="font-extrabold text-emerald-700"><?=cams_landing_e(cams_landing_day($schedule['day']))?></strong>
                                    <span class="text-slate-300">•</span>
                                    <span><?=cams_landing_e(cams_landing_time($schedule['time_in']))?> – <?=cams_landing_e(cams_landing_time($schedule['time_out']))?></span>
                                  </span>
                                <?php endforeach; ?>
                              </div>
                            </div>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  </article>
                <?php endforeach; ?>
              </div>

              <div id="schedule-empty" class="hidden min-h-[220px] items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
                <div>
                  <span class="mx-auto inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-slate-400 shadow-sm">⌕</span>
                  <strong class="mt-3 block text-lg text-slate-900">No matching clinic schedules</strong>
                  <p class="mb-0 mt-1 text-sm text-slate-500">Try another clinic or search term.</p>
                </div>
              </div>
            <?php else: ?>
              <div class="min-h-[240px] rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
                <span class="mx-auto inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-slate-400 shadow-sm">—</span>
                <strong class="mt-3 block text-lg text-slate-900">No public schedules are available yet</strong>
                <p class="mb-0 mt-1 text-sm text-slate-500">Please check again after clinic schedules have been configured.</p>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </section>

      <section class="px-4 pb-14 pt-4 sm:px-6 lg:px-8">
        <div class="grid gap-4 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:grid-cols-3 lg:p-6">
          <div class="rounded-2xl bg-slate-50 p-4">
            <span class="text-xs font-extrabold uppercase tracking-[0.12em] text-emerald-700">Appointments</span>
            <h3 class="mt-1 text-base font-extrabold text-slate-950">Schedule-safe booking</h3>
            <p class="mb-0 mt-2 text-sm leading-6 text-slate-500">Booking dates follow physician clinic schedules and daily appointment limits.</p>
          </div>
          <div class="rounded-2xl bg-slate-50 p-4">
            <span class="text-xs font-extrabold uppercase tracking-[0.12em] text-emerald-700">Operations</span>
            <h3 class="mt-1 text-base font-extrabold text-slate-950">Connected clinic workflow</h3>
            <p class="mb-0 mt-2 text-sm leading-6 text-slate-500">Physicians, secretaries, clinics, schedules, and queues stay connected across CAMS.</p>
          </div>
          <div class="rounded-2xl bg-slate-50 p-4">
            <span class="text-xs font-extrabold uppercase tracking-[0.12em] text-emerald-700">Reports</span>
            <h3 class="mt-1 text-base font-extrabold text-slate-950">Useful operational insight</h3>
            <p class="mb-0 mt-2 text-sm leading-6 text-slate-500">Dashboards and reports turn appointment records into practical workload breakdowns.</p>
          </div>
        </div>
      </section>
    </main>

    <footer class="border-t border-slate-200 bg-white px-4 py-6 sm:px-6 lg:px-8">
      <div class="flex flex-col gap-3 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <strong class="font-extrabold text-slate-800">HNU Medical Center CAMS</strong>
          <span class="ml-2">Version 2.0</span>
        </div>
        <div>Copyright &copy; 2018 &middot; All rights reserved.</div>
      </div>
    </footer>
  </div>

  <script src="<?=base_url()?>assets/bower_components/jquery/dist/jquery.min.js"></script>
  <script src="<?=base_url()?>assets/bower_components/select2/dist/js/select2.full.min.js"></script>
  <script src="<?=base_url()?>js/cams-select2.js?v=<?=@filemtime(FCPATH.'js/cams-select2.js')?>"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var search = document.getElementById('schedule-search');
      var clinic = document.getElementById('schedule-clinic-filter');
      var cards = Array.prototype.slice.call(document.querySelectorAll('[data-schedule-card]'));
      var empty = document.getElementById('schedule-empty');

      function applyScheduleFilters() {
        var term = search ? String(search.value || '').trim().toLowerCase() : '';
        var clinicId = clinic ? String(clinic.value || '') : '';
        var visible = 0;

        cards.forEach(function (card) {
          var matchesText = !term || String(card.getAttribute('data-search') || '').indexOf(term) !== -1;
          var matchesClinic = !clinicId || String(card.getAttribute('data-clinic-id') || '') === clinicId;
          var show = matchesText && matchesClinic;
          card.hidden = !show;
          if (show) visible++;
        });

        if (empty) {
          empty.classList.toggle('hidden', visible !== 0);
          empty.classList.toggle('flex', visible === 0);
        }
      }

      if (search) search.addEventListener('input', applyScheduleFilters);
      if (clinic) {
        clinic.addEventListener('change', applyScheduleFilters);
        if (window.jQuery) {
          window.jQuery(clinic).off('change.camsLanding').on('change.camsLanding', applyScheduleFilters);
        }
      }
    });
  </script>
</body>
</html>
