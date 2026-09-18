<?php
$dashboard = isset($dashboard) && is_array($dashboard) ? $dashboard : array();
$dashRole = isset($dashRole) ? $dashRole : 'User';
$dashTitle = isset($dashTitle) ? $dashTitle : 'Dashboard';
$dashSubtitle = isset($dashSubtitle) ? $dashSubtitle : 'Overview of your CAMS workspace.';
$dashKpis = isset($dashKpis) && is_array($dashKpis) ? $dashKpis : array();
$dashActions = isset($dashActions) && is_array($dashActions) ? $dashActions : array();
$statusData = isset($dashboard['status']) ? $dashboard['status'] : array();
$trendData = isset($dashboard['trend']) ? $dashboard['trend'] : array();
$clinicData = isset($dashboard['clinics']) ? $dashboard['clinics'] : array();
$secondary = isset($dashboard['secondary']) && is_array($dashboard['secondary']) ? $dashboard['secondary'] : array();
$secondaryData = isset($secondary['data']) && is_array($secondary['data']) ? $secondary['data'] : array();
$secondaryHasActivity = false;
foreach ($secondaryData as $secondaryItem) {
    if (!empty($secondaryItem['total'])) {
        $secondaryHasActivity = true;
        break;
    }
}
$upcoming = isset($dashboard['upcoming']) ? $dashboard['upcoming'] : array();
?>
<div class="content-wrapper !bg-slate-50 dark:!bg-slate-950">
  <section class="px-4 pb-10 pt-6 sm:px-6 lg:px-8">
    <div class="w-full">
      <div class="mb-6 flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
          <div class="mb-2 flex items-center gap-2">
            <span class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.16em] text-emerald-700 dark:border-emerald-900/70 dark:bg-emerald-950/50 dark:text-emerald-300">
              <?=htmlspecialchars($dashRole, ENT_QUOTES, 'UTF-8')?> workspace
            </span>
            <span class="text-xs font-medium text-slate-400"><?=date('l, F j, Y')?></span>
          </div>
          <h1 class="m-0 font-heading text-3xl font-extrabold tracking-tight text-slate-950 dark:text-white sm:text-4xl">
            <?=htmlspecialchars($dashTitle, ENT_QUOTES, 'UTF-8')?>
          </h1>
          <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500 dark:text-slate-400">
            <?=htmlspecialchars($dashSubtitle, ENT_QUOTES, 'UTF-8')?>
          </p>
        </div>

        <?php if ($dashActions): ?>
          <div class="flex flex-wrap gap-2">
            <?php foreach ($dashActions as $action): ?>
              <a href="<?=base_url($action['url'])?>"
                 class="inline-flex min-h-10 items-center gap-2 rounded-xl border px-4 py-2 text-sm font-semibold no-underline transition
                   <?=!empty($action['primary'])
                     ? 'border-emerald-700 bg-emerald-700 text-white hover:bg-emerald-800 hover:text-white'
                     : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50 hover:text-slate-900 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800'?>">
                <i class="fa <?=htmlspecialchars($action['icon'], ENT_QUOTES, 'UTF-8')?>"></i>
                <?=htmlspecialchars($action['label'], ENT_QUOTES, 'UTF-8')?>
              </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <div class="mb-6 grid grid-cols-[repeat(auto-fit,minmax(190px,1fr))] gap-4">
        <?php foreach ($dashKpis as $kpi): ?>
          <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-start justify-between gap-4">
              <div>
                <p class="m-0 text-xs font-bold uppercase tracking-[0.11em] text-slate-400"><?=htmlspecialchars($kpi['label'], ENT_QUOTES, 'UTF-8')?></p>
                <p class="mb-0 mt-3 text-3xl font-extrabold tracking-tight text-slate-950 dark:text-white"><?=number_format((int)$kpi['value'])?><?=!empty($kpi['suffix']) ? htmlspecialchars($kpi['suffix'], ENT_QUOTES, 'UTF-8') : ''?></p>
                <?php if (!empty($kpi['hint'])): ?>
                  <p class="mb-0 mt-2 text-xs leading-5 text-slate-500 dark:text-slate-400"><?=htmlspecialchars($kpi['hint'], ENT_QUOTES, 'UTF-8')?></p>
                <?php endif; ?>
              </div>
              <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300">
                <i class="fa <?=htmlspecialchars($kpi['icon'], ENT_QUOTES, 'UTF-8')?>"></i>
              </span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="mb-6 grid grid-cols-1 gap-5 xl:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 xl:col-span-2">
          <div class="mb-5 flex items-start justify-between gap-4">
            <div>
              <h2 class="m-0 text-base font-bold text-slate-900 dark:text-white">Appointment activity</h2>
              <p class="mb-0 mt-1 text-xs text-slate-500 dark:text-slate-400">Recent activity plus upcoming scheduled demand around today.</p>
            </div>
            <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-500 dark:bg-slate-800 dark:text-slate-300">
              <?=count($trendData)?>-day window
            </span>
          </div>
          <div class="relative h-[300px]">
            <canvas id="cams-dashboard-trend-chart"></canvas>
          </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
          <div class="mb-5">
            <h2 class="m-0 text-base font-bold text-slate-900 dark:text-white">Appointment status</h2>
            <p class="mb-0 mt-1 text-xs text-slate-500 dark:text-slate-400">Current distribution of appointment outcomes.</p>
          </div>
          <div class="relative mx-auto h-[245px] max-w-[300px]">
            <canvas id="cams-dashboard-status-chart"></canvas>
          </div>
          <div class="mt-5 grid grid-cols-3 gap-2">
            <?php foreach (array('Pending', 'Done', 'Cancelled') as $status): ?>
              <div class="rounded-xl bg-slate-50 px-3 py-2 text-center dark:bg-slate-800/70">
                <span class="block text-lg font-extrabold text-slate-900 dark:text-white"><?=number_format(isset($statusData[$status]) ? (int)$statusData[$status] : 0)?></span>
                <span class="block text-[10px] font-bold uppercase tracking-wide text-slate-400"><?=htmlspecialchars($status, ENT_QUOTES, 'UTF-8')?></span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-5 xl:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
          <div class="mb-5">
            <h2 class="m-0 text-base font-bold text-slate-900 dark:text-white">Clinic breakdown</h2>
            <p class="mb-0 mt-1 text-xs text-slate-500 dark:text-slate-400">Appointments grouped by clinic.</p>
          </div>
          <?php if ($clinicData): ?>
            <div class="relative h-[290px]">
              <canvas id="cams-dashboard-clinic-chart"></canvas>
            </div>
          <?php else: ?>
            <div class="flex min-h-[250px] items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-slate-50 text-center dark:border-slate-700 dark:bg-slate-800/50">
              <div class="px-8">
                <i class="fa fa-bar-chart mb-3 text-2xl text-slate-300 dark:text-slate-600"></i>
                <p class="m-0 text-sm font-semibold text-slate-500 dark:text-slate-400">No clinic activity to chart yet.</p>
              </div>
            </div>
          <?php endif; ?>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
          <div class="mb-5">
            <h2 class="m-0 font-heading text-base font-bold text-slate-900 dark:text-white">
              <?=htmlspecialchars(isset($secondary['title']) ? $secondary['title'] : 'Workload breakdown', ENT_QUOTES, 'UTF-8')?>
            </h2>
            <p class="mb-0 mt-1 text-xs text-slate-500 dark:text-slate-400">
              <?=htmlspecialchars(isset($secondary['description']) ? $secondary['description'] : 'Additional appointment activity breakdown.', ENT_QUOTES, 'UTF-8')?>
            </p>
          </div>

          <?php if ($secondaryHasActivity): ?>
            <div class="relative h-[290px]">
              <canvas id="cams-dashboard-secondary-chart"></canvas>
            </div>
          <?php else: ?>
            <div class="flex min-h-[250px] items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-slate-50 text-center dark:border-slate-700 dark:bg-slate-800/50">
              <div class="px-8">
                <i class="fa fa-line-chart mb-3 text-2xl text-slate-300 dark:text-slate-600"></i>
                <p class="m-0 text-sm font-semibold text-slate-500 dark:text-slate-400">No activity available for this breakdown yet.</p>
              </div>
            </div>
          <?php endif; ?>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 xl:col-span-2">
          <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-5 py-5 dark:border-slate-800">
            <div>
              <h2 class="m-0 text-base font-bold text-slate-900 dark:text-white">Upcoming queue</h2>
              <p class="mb-0 mt-1 text-xs text-slate-500 dark:text-slate-400">Next appointments requiring attention.</p>
            </div>
          </div>

          <?php if ($upcoming): ?>
            <div class="overflow-x-auto">
              <table class="w-full border-collapse text-left">
                <thead>
                  <tr class="bg-slate-50 dark:bg-slate-800/60">
                    <th class="px-5 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">Date</th>
                    <th class="px-5 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">Clinic</th>
                    <th class="px-5 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-400"><?=($dashRole === 'Patient' ? 'Physician' : 'Patient')?></th>
                    <th class="px-5 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">Queue</th>
                    <th class="px-5 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($upcoming as $item): ?>
                    <tr class="border-t border-slate-100 dark:border-slate-800">
                      <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-slate-800 dark:text-slate-100"><?=date('M j, Y', strtotime($item['app_date']))?></td>
                      <td class="px-5 py-4 text-sm text-slate-500 dark:text-slate-400"><?=htmlspecialchars($item['clinic_name'] ?: '—', ENT_QUOTES, 'UTF-8')?></td>
                      <td class="px-5 py-4 text-sm text-slate-500 dark:text-slate-400">
                        <?php if ($dashRole === 'Patient'): ?>
                          Dr. <?=htmlspecialchars(trim(($item['physician_fname'] ?: '') . ' ' . ($item['physician_lname'] ?: '')), ENT_QUOTES, 'UTF-8')?>
                        <?php else: ?>
                          <?=htmlspecialchars(trim(($item['patient_fname'] ?: '') . ' ' . ($item['patient_lname'] ?: '')), ENT_QUOTES, 'UTF-8')?>
                        <?php endif; ?>
                      </td>
                      <td class="px-5 py-4 text-sm font-bold text-slate-800 dark:text-slate-100">#<?=htmlspecialchars($item['queueNum'], ENT_QUOTES, 'UTF-8')?></td>
                      <td class="px-5 py-4">
                        <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-bold text-amber-700 dark:bg-amber-950/40 dark:text-amber-300">
                          <?=htmlspecialchars($item['app_status'], ENT_QUOTES, 'UTF-8')?>
                        </span>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <div class="px-6 py-16 text-center">
              <i class="fa fa-calendar-check-o mb-3 text-3xl text-slate-300 dark:text-slate-600"></i>
              <p class="m-0 text-sm font-semibold text-slate-500 dark:text-slate-400">No upcoming appointments.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
(function () {
  const trendData = <?=json_encode(array_values($trendData), JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT)?>;
  const statusData = <?=json_encode($statusData, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT)?>;
  const clinicData = <?=json_encode(array_values($clinicData), JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT)?>;
  const secondaryData = <?=json_encode(array_values($secondaryData), JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT)?>;
  let charts = [];

  function themeColors() {
    const dark = document.documentElement.classList.contains('dark');
    return {
      text: dark ? '#cbd5e1' : '#64748b',
      grid: dark ? 'rgba(148,163,184,.10)' : 'rgba(148,163,184,.16)',
      line: dark ? '#34d399' : '#16845e',
      lineFill: dark ? 'rgba(52,211,153,.12)' : 'rgba(22,132,94,.10)'
    };
  }

  function destroyCharts() {
    charts.forEach(chart => chart && chart.destroy());
    charts = [];
  }

  function renderCharts() {
    if (!window.Chart) return;
    destroyCharts();
    const c = themeColors();

    const trendCanvas = document.getElementById('cams-dashboard-trend-chart');
    if (trendCanvas) {
      charts.push(new Chart(trendCanvas, {
        type: 'line',
        data: {
          labels: trendData.map(item => item.label),
          datasets: [{
            label: 'Appointments',
            data: trendData.map(item => item.total),
            borderColor: c.line,
            backgroundColor: c.lineFill,
            fill: true,
            tension: .35,
            borderWidth: 2,
            pointRadius: trendData.map(item => item.is_today ? 5 : 2),
            pointHoverRadius: 6,
            pointBackgroundColor: trendData.map(item => item.is_today ? '#f59e0b' : c.line),
            pointBorderColor: trendData.map(item => item.is_today ? '#ffffff' : c.line),
            pointBorderWidth: trendData.map(item => item.is_today ? 2 : 0)
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: {
            x: { grid: { display: false }, ticks: { color: c.text, maxRotation: 0, autoSkip: true, maxTicksLimit: 8 } },
            y: { beginAtZero: true, ticks: { color: c.text, precision: 0 }, grid: { color: c.grid } }
          }
        }
      }));
    }

    const statusCanvas = document.getElementById('cams-dashboard-status-chart');
    if (statusCanvas) {
      charts.push(new Chart(statusCanvas, {
        type: 'doughnut',
        data: {
          labels: ['Pending', 'Done', 'Cancelled'],
          datasets: [{
            data: [statusData.Pending || 0, statusData.Done || 0, statusData.Cancelled || 0],
            backgroundColor: ['#f59e0b', '#10b981', '#ef4444'],
            borderWidth: 0,
            hoverOffset: 5
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: '70%',
          plugins: {
            legend: { position: 'bottom', labels: { color: c.text, boxWidth: 10, boxHeight: 10, usePointStyle: true, padding: 16 } }
          }
        }
      }));
    }

    const clinicCanvas = document.getElementById('cams-dashboard-clinic-chart');
    if (clinicCanvas && clinicData.length) {
      charts.push(new Chart(clinicCanvas, {
        type: 'bar',
        data: {
          labels: clinicData.map(item => item.name || 'Unknown'),
          datasets: [{
            label: 'Appointments',
            data: clinicData.map(item => Number(item.total || 0)),
            backgroundColor: c.line,
            borderRadius: 8,
            borderSkipped: false
          }]
        },
        options: {
          indexAxis: 'y',
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: {
            x: { beginAtZero: true, ticks: { color: c.text, precision: 0 }, grid: { color: c.grid } },
            y: { ticks: { color: c.text }, grid: { display: false } }
          }
        }
      }));
    }

    const secondaryCanvas = document.getElementById('cams-dashboard-secondary-chart');
    if (secondaryCanvas && secondaryData.some(item => Number(item.total || 0) > 0)) {
      charts.push(new Chart(secondaryCanvas, {
        type: 'bar',
        data: {
          labels: secondaryData.map(item => item.name || 'Unknown'),
          datasets: [{
            label: 'Appointments',
            data: secondaryData.map(item => Number(item.total || 0)),
            backgroundColor: c.line,
            borderRadius: 8,
            borderSkipped: false
          }]
        },
        options: {
          indexAxis: 'y',
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: {
            x: { beginAtZero: true, ticks: { color: c.text, precision: 0 }, grid: { color: c.grid } },
            y: { ticks: { color: c.text }, grid: { display: false } }
          }
        }
      }));
    }
  }

  document.addEventListener('DOMContentLoaded', renderCharts);
  if (window.jQuery) {
    jQuery(document).on('cams:theme-changed', function () { window.setTimeout(renderCharts, 0); });
  }
})();
</script>
