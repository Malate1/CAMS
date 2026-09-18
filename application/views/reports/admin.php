<?php
$this->load->view('header/header');

$type = isset($report['type']) ? $report['type'] : 'top_clinics';
$summary = isset($report['summary']) ? $report['summary'] : array();
$daily = isset($report['daily']) ? $report['daily'] : array();
$rows = isset($report['rows']) ? $report['rows'] : array();

$definitions = array(
    'top_clinics' => array(
        'eyebrow' => 'Clinic performance',
        'title' => 'Top Visited Clinics',
        'description' => 'Rank clinics by completed patient visits while keeping total bookings and outcomes visible.',
        'icon' => 'fa-hospital-o',
        'route' => $reportRoutes['top_clinics'],
    ),
    'top_purposes' => array(
        'eyebrow' => 'Consultation demand',
        'title' => 'Top Consulted Ailments',
        'description' => 'Rank appointment purposes by completed consultations and compare overall patient demand.',
        'icon' => 'fa-stethoscope',
        'route' => $reportRoutes['top_purposes'],
    ),
    'average' => array(
        'eyebrow' => 'Clinic workload',
        'title' => 'Monthly Clinic Average',
        'description' => 'Measure clinic appointment volume using calendar-day and active-day workload averages.',
        'icon' => 'fa-bar-chart',
        'route' => $reportRoutes['average'],
    ),
);
$definition = $definitions[$type];

$isAverage = $type === 'average';
$filterLabel = $isAverage
    ? (isset($report['month_label']) ? $report['month_label'] : date('F Y'))
    : (isset($report['range_label']) ? $report['range_label'] : '');

function cams_admin_report_e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function cams_admin_completion_rate($done, $cancelled) {
    $resolved = (int) $done + (int) $cancelled;
    return $resolved > 0 ? (int) round(((int) $done / $resolved) * 100) : 0;
}

$cards = array(
    array('label' => 'Appointments', 'value' => isset($summary['total']) ? $summary['total'] : 0, 'icon' => 'fa-calendar', 'tone' => 'emerald'),
    array('label' => 'Completed', 'value' => isset($summary['done']) ? $summary['done'] : 0, 'icon' => 'fa-check-circle', 'tone' => 'green'),
    array('label' => 'Pending', 'value' => isset($summary['pending']) ? $summary['pending'] : 0, 'icon' => 'fa-clock-o', 'tone' => 'amber'),
    array('label' => 'Cancelled', 'value' => isset($summary['cancelled']) ? $summary['cancelled'] : 0, 'icon' => 'fa-times-circle', 'tone' => 'rose'),
    array('label' => 'Unique Patients', 'value' => isset($summary['unique_patients']) ? $summary['unique_patients'] : 0, 'icon' => 'fa-users', 'tone' => 'blue'),
    array('label' => 'Completion Rate', 'value' => (isset($summary['completion_rate']) ? $summary['completion_rate'] : 0) . '%', 'icon' => 'fa-line-chart', 'tone' => 'violet'),
);

$toneClasses = array(
    'emerald' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300',
    'green' => 'bg-green-50 text-green-700 dark:bg-green-950/40 dark:text-green-300',
    'amber' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300',
    'rose' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300',
    'blue' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300',
    'violet' => 'bg-violet-50 text-violet-700 dark:bg-violet-950/40 dark:text-violet-300',
);
?>

<div class="content-wrapper bg-slate-50 dark:bg-slate-950">
  <section class="content-header !px-4 !pt-6 !pb-2 sm:!px-6 lg:!px-8">
    <div class="w-full">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
          <div class="mb-2 flex items-center gap-2 text-sm font-bold uppercase tracking-[0.14em] text-emerald-700 dark:text-emerald-400">
            <i class="fa <?=cams_admin_report_e($definition['icon'])?>"></i>
            <?=cams_admin_report_e($definition['eyebrow'])?>
          </div>
          <h1 class="m-0 text-3xl font-extrabold tracking-tight text-slate-950 dark:text-white">
            <?=cams_admin_report_e($definition['title'])?>
          </h1>
          <p class="mt-2 max-w-3xl text-base text-slate-500 dark:text-slate-400">
            <?=cams_admin_report_e($definition['description'])?>
          </p>
          <div class="mt-3 flex flex-wrap items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
            <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1.5 dark:border-slate-800 dark:bg-slate-900">
              <i class="fa fa-calendar text-emerald-600"></i>
              <?=cams_admin_report_e($filterLabel)?>
            </span>
            <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1.5 dark:border-slate-800 dark:bg-slate-900">
              <i class="fa fa-globe text-emerald-600"></i>
              System-wide appointment data
            </span>
          </div>
        </div>

        <div class="flex items-center gap-2 cams-report-print-hide">
          <button type="button" onclick="window.print()" class="btn btn-default">
            <i class="fa fa-print"></i>
            Print Report
          </button>
        </div>
      </div>
    </div>
  </section>

  <section class="content !px-4 !pb-10 sm:!px-6 lg:!px-8">
    <div class="w-full space-y-5">
      <div class="grid gap-4 xl:grid-cols-[1fr_auto] cams-report-print-hide">
        <nav class="flex flex-wrap items-center gap-2 rounded-2xl border border-slate-200 bg-white p-2 shadow-sm dark:border-slate-800 dark:bg-slate-900">
          <?php foreach ($definitions as $key => $item): ?>
            <?php
            $href = base_url($item['route']);
            if ($key === 'average') {
                $href .= '?month=' . urlencode($isAverage ? $report['month'] : date('Y-m'));
            } else {
                $start = $isAverage ? date('Y-m-01') : $report['start_date'];
                $end = $isAverage ? date('Y-m-d') : $report['end_date'];
                $href .= '?start=' . urlencode($start) . '&end=' . urlencode($end);
            }
            ?>
            <a
              href="<?=$href?>"
              class="<?= $key === $type
                ? 'inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-bold text-white shadow-sm'
                : 'inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' ?>"
            >
              <i class="fa <?=cams_admin_report_e($item['icon'])?>"></i>
              <?=cams_admin_report_e($item['title'])?>
            </a>
          <?php endforeach; ?>
        </nav>

        <?php if ($isAverage): ?>
          <form method="get" action="<?=base_url($definition['route'])?>" class="flex items-end gap-2 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div>
              <label for="report-month" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">Report month</label>
              <input id="report-month" name="month" type="month" value="<?=cams_admin_report_e($report['month'])?>" max="<?=date('Y-m', strtotime('+2 months'))?>" class="form-control !min-h-11 !min-w-[180px]" required>
            </div>
            <button type="submit" class="btn btn-primary !min-h-11"><i class="fa fa-refresh"></i> Apply</button>
          </form>
        <?php else: ?>
          <form method="get" action="<?=base_url($definition['route'])?>" class="flex flex-wrap items-end gap-2 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div>
              <label for="report-start" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">Start date</label>
              <input id="report-start" name="start" type="date" value="<?=cams_admin_report_e($report['start_date'])?>" class="form-control !min-h-11" required>
            </div>
            <div>
              <label for="report-end" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">End date</label>
              <input id="report-end" name="end" type="date" value="<?=cams_admin_report_e($report['end_date'])?>" class="form-control !min-h-11" required>
            </div>
            <button type="submit" class="btn btn-primary !min-h-11"><i class="fa fa-refresh"></i> Apply</button>
          </form>
        <?php endif; ?>
      </div>

      <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-6">
        <?php foreach ($cards as $card): ?>
          <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-start justify-between gap-3">
              <div>
                <p class="mb-0 text-sm font-semibold text-slate-500 dark:text-slate-400"><?=cams_admin_report_e($card['label'])?></p>
                <p class="mb-0 mt-2 text-3xl font-extrabold tracking-tight text-slate-950 dark:text-white"><?=cams_admin_report_e($card['value'])?></p>
              </div>
              <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl <?=$toneClasses[$card['tone']]?>">
                <i class="fa <?=cams_admin_report_e($card['icon'])?>"></i>
              </span>
            </div>
          </article>
        <?php endforeach; ?>
      </div>

      <div class="grid gap-5 xl:grid-cols-2">
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
          <div class="mb-4">
            <h2 class="m-0 text-xl font-extrabold text-slate-950 dark:text-white">Daily appointment activity</h2>
            <p class="mb-0 mt-1 text-sm text-slate-500 dark:text-slate-400">Appointment volume across the selected reporting period.</p>
          </div>
          <div class="relative h-[310px]">
            <canvas id="cams-admin-report-daily"></canvas>
          </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
          <div class="mb-4">
            <h2 class="m-0 text-xl font-extrabold text-slate-950 dark:text-white">
              <?=$type === 'top_clinics' ? 'Clinic ranking' : ($type === 'top_purposes' ? 'Consultation ranking' : 'Clinic workload ranking')?>
            </h2>
            <p class="mb-0 mt-1 text-sm text-slate-500 dark:text-slate-400">
              <?=$type === 'top_clinics'
                ? 'Ranked by completed patient visits.'
                : ($type === 'top_purposes' ? 'Ranked by completed consultations.' : 'Ranked by total monthly appointments.')?>
            </p>
          </div>
          <div class="relative h-[310px]">
            <canvas id="cams-admin-report-ranking"></canvas>
          </div>
        </section>
      </div>

      <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-800">
          <h2 class="m-0 text-xl font-extrabold text-slate-950 dark:text-white">
            <?=cams_admin_report_e($definition['title'])?> breakdown
          </h2>
          <?php if ($type === 'top_clinics'): ?>
            <p class="mb-0 mt-1 text-sm text-slate-500 dark:text-slate-400">A visit is counted when an appointment is marked Done. Total bookings are shown separately so pending and cancelled records do not inflate the visit ranking.</p>
          <?php elseif ($type === 'top_purposes'): ?>
            <p class="mb-0 mt-1 text-sm text-slate-500 dark:text-slate-400">Consultation ranking uses Done appointments. Total requests are shown separately for context.</p>
          <?php else: ?>
            <p class="mb-0 mt-1 text-sm text-slate-500 dark:text-slate-400">Calendar-day average = appointments ÷ days in month. Active-day average = appointments ÷ days that clinic recorded at least one appointment.</p>
          <?php endif; ?>
        </div>

        <div class="overflow-x-auto">
          <table class="table cams-modern-table !m-0">
            <?php if ($type === 'top_clinics'): ?>
              <thead>
                <tr>
                  <th>Rank</th>
                  <th>Clinic</th>
                  <th>Completed Visits</th>
                  <th>Total Bookings</th>
                  <th>Unique Patients</th>
                  <th>Pending</th>
                  <th>Cancelled</th>
                  <th>Completion</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($rows): ?>
                  <?php foreach ($rows as $index => $row): ?>
                    <tr>
                      <td><span class="cams-queue"><?=number_format($index + 1)?></span></td>
                      <td>
                        <strong><?=cams_admin_report_e($row['name'])?></strong>
                        <small><?=cams_admin_report_e($row['location'] ?: 'No location recorded')?></small>
                      </td>
                      <td><strong><?=number_format((int)$row['completed_visits'])?></strong></td>
                      <td><?=number_format((int)$row['total_bookings'])?></td>
                      <td><?=number_format((int)$row['unique_patients'])?></td>
                      <td><span class="cams-status cams-status-pending"><span></span><?=number_format((int)$row['pending'])?></span></td>
                      <td><span class="cams-status cams-status-cancelled"><span></span><?=number_format((int)$row['cancelled'])?></span></td>
                      <td><?=cams_admin_completion_rate($row['completed_visits'], $row['cancelled'])?>%</td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="8"><div class="cams-empty-state"><i class="fa fa-hospital-o"></i><strong>No clinic visits in this period</strong><span>Choose another date range to review completed clinic visits.</span></div></td></tr>
                <?php endif; ?>
              </tbody>
            <?php elseif ($type === 'top_purposes'): ?>
              <thead>
                <tr>
                  <th>Rank</th>
                  <th>Ailment / Purpose</th>
                  <th>Completed Consultations</th>
                  <th>Total Requests</th>
                  <th>Unique Patients</th>
                  <th>Pending</th>
                  <th>Cancelled</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($rows): ?>
                  <?php foreach ($rows as $index => $row): ?>
                    <tr>
                      <td><span class="cams-queue"><?=number_format($index + 1)?></span></td>
                      <td><strong><?=cams_admin_report_e($row['purpose'])?></strong></td>
                      <td><strong><?=number_format((int)$row['completed_consultations'])?></strong></td>
                      <td><?=number_format((int)$row['total_requests'])?></td>
                      <td><?=number_format((int)$row['unique_patients'])?></td>
                      <td><span class="cams-status cams-status-pending"><span></span><?=number_format((int)$row['pending'])?></span></td>
                      <td><span class="cams-status cams-status-cancelled"><span></span><?=number_format((int)$row['cancelled'])?></span></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="7"><div class="cams-empty-state"><i class="fa fa-stethoscope"></i><strong>No completed consultations in this period</strong><span>Choose another date range to review consultation demand.</span></div></td></tr>
                <?php endif; ?>
              </tbody>
            <?php else: ?>
              <thead>
                <tr>
                  <th>Rank</th>
                  <th>Clinic</th>
                  <th>Appointments</th>
                  <th>Active Days</th>
                  <th>Avg / Calendar Day</th>
                  <th>Avg / Active Day</th>
                  <th>Unique Patients</th>
                  <th>Done</th>
                  <th>Pending</th>
                  <th>Cancelled</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($rows): ?>
                  <?php foreach ($rows as $index => $row): ?>
                    <tr>
                      <td><span class="cams-queue"><?=number_format($index + 1)?></span></td>
                      <td>
                        <strong><?=cams_admin_report_e($row['name'])?></strong>
                        <small><?=cams_admin_report_e($row['location'] ?: 'No location recorded')?></small>
                      </td>
                      <td><strong><?=number_format((int)$row['total'])?></strong></td>
                      <td><?=number_format((int)$row['active_days'])?></td>
                      <td><?=number_format((float)$row['average_per_calendar_day'], 2)?></td>
                      <td><?=number_format((float)$row['average_per_active_day'], 2)?></td>
                      <td><?=number_format((int)$row['unique_patients'])?></td>
                      <td><span class="cams-status cams-status-done"><span></span><?=number_format((int)$row['done'])?></span></td>
                      <td><span class="cams-status cams-status-pending"><span></span><?=number_format((int)$row['pending'])?></span></td>
                      <td><span class="cams-status cams-status-cancelled"><span></span><?=number_format((int)$row['cancelled'])?></span></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="10"><div class="cams-empty-state"><i class="fa fa-bar-chart"></i><strong>No appointment activity for this month</strong><span>Choose another month to review clinic workload.</span></div></td></tr>
                <?php endif; ?>
              </tbody>
            <?php endif; ?>
          </table>
        </div>
      </section>
    </div>
  </section>
</div>

<footer class="main-footer cams-report-print-hide">
  <strong>Copyright &copy; 2018</strong> All rights reserved.
</footer>
</div>

<style>
@media print {
  @page { margin: 12mm; }
  .main-header, .main-sidebar, .cams-report-print-hide { display: none !important; }
  .content-wrapper { margin-left: 0 !important; min-height: auto !important; background: #fff !important; }
  .content-header, .content { padding-left: 0 !important; padding-right: 0 !important; }
  body { background: #fff !important; }
}
</style>

<script>
(function () {
  var type = <?=json_encode($type)?>;
  var daily = <?=json_encode($daily)?>;
  var rows = <?=json_encode($rows)?>;
  var dailyChart = null;
  var rankingChart = null;

  function colors() {
    var dark = document.documentElement.classList.contains('dark');
    return {
      text: dark ? '#cbd5e1' : '#64748b',
      grid: dark ? 'rgba(148,163,184,.14)' : 'rgba(148,163,184,.18)',
      green: '#16845e',
      greenFill: 'rgba(22,132,94,.10)',
      blue: '#2563eb',
      amber: '#d97706'
    };
  }

  function rankingData() {
    return rows.slice(0, 10).map(function (row) {
      if (type === 'top_clinics') {
        return { label: row.name, value: Number(row.completed_visits || 0) };
      }
      if (type === 'top_purposes') {
        return { label: row.purpose, value: Number(row.completed_consultations || 0) };
      }
      return { label: row.name, value: Number(row.total || 0) };
    });
  }

  function renderCharts() {
    if (!window.Chart) return;
    var c = colors();

    var dailyCanvas = document.getElementById('cams-admin-report-daily');
    if (dailyCanvas) {
      if (dailyChart) dailyChart.destroy();
      dailyChart = new Chart(dailyCanvas, {
        type: 'line',
        data: {
          labels: daily.map(function (item) { return item.label; }),
          datasets: [{
            label: 'Appointments',
            data: daily.map(function (item) { return Number(item.total || 0); }),
            borderColor: c.green,
            backgroundColor: c.greenFill,
            fill: true,
            tension: .32,
            borderWidth: 2,
            pointRadius: 2,
            pointHoverRadius: 5
          }]
        },
        options: {
          maintainAspectRatio: false,
          responsive: true,
          plugins: { legend: { display: false } },
          scales: {
            x: { ticks: { color: c.text, maxTicksLimit: 12, maxRotation: 0 }, grid: { display: false } },
            y: { beginAtZero: true, ticks: { color: c.text, precision: 0 }, grid: { color: c.grid } }
          }
        }
      });
    }

    var ranked = rankingData();
    var rankingCanvas = document.getElementById('cams-admin-report-ranking');
    if (rankingCanvas) {
      if (rankingChart) rankingChart.destroy();
      rankingChart = new Chart(rankingCanvas, {
        type: 'bar',
        data: {
          labels: ranked.map(function (item) { return item.label; }),
          datasets: [{
            data: ranked.map(function (item) { return item.value; }),
            backgroundColor: c.green,
            borderRadius: 8,
            maxBarThickness: 34
          }]
        },
        options: {
          indexAxis: 'y',
          maintainAspectRatio: false,
          responsive: true,
          plugins: { legend: { display: false } },
          scales: {
            x: { beginAtZero: true, ticks: { color: c.text, precision: 0 }, grid: { color: c.grid } },
            y: { ticks: { color: c.text }, grid: { display: false } }
          }
        }
      });
    }
  }

  document.addEventListener('DOMContentLoaded', renderCharts);
  document.addEventListener('cams:theme-changed', renderCharts);
})();
</script>
</body>
</html>
