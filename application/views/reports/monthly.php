<?php
$this->load->view($reportHeader);

$type = isset($report['type']) ? $report['type'] : 'average';
$month = isset($report['month']) ? $report['month'] : date('Y-m');
$monthLabel = isset($report['month_label']) ? $report['month_label'] : date('F Y');
$summary = isset($report['summary']) ? $report['summary'] : array();
$daily = isset($report['daily']) ? $report['daily'] : array();
$clinicAverage = isset($report['clinic_average']) ? $report['clinic_average'] : array();
$appointments = isset($report['appointments']) ? $report['appointments'] : array();

$definitions = array(
    'average' => array(
        'eyebrow' => 'Clinic workload',
        'title' => 'Monthly Appointment Average',
        'description' => 'Review appointment volume and daily workload by clinic.',
        'icon' => 'fa-bar-chart',
    ),
    'done' => array(
        'eyebrow' => 'Completed visits',
        'title' => 'Done Appointments',
        'description' => 'Review completed patient appointments for the selected month.',
        'icon' => 'fa-check-circle',
    ),
    'cancelled' => array(
        'eyebrow' => 'Cancelled visits',
        'title' => 'Cancelled Appointments',
        'description' => 'Review cancelled patient appointments for the selected month.',
        'icon' => 'fa-times-circle',
    ),
);
$definition = $definitions[$type];

$currentRoute = isset($reportRoutes[$type]) ? $reportRoutes[$type] : '';
$chartMetric = $type === 'done' ? 'done' : ($type === 'cancelled' ? 'cancelled' : 'total');
$chartTitle = $type === 'done'
    ? 'Completed appointments by day'
    : ($type === 'cancelled' ? 'Cancelled appointments by day' : 'Appointment volume by day');

function cams_report_e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>

<div class="content-wrapper bg-slate-50 dark:bg-slate-950">
  <section class="content-header !px-4 !pt-6 !pb-2 sm:!px-6 lg:!px-8">
    <div class="w-full">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
          <div class="mb-2 flex items-center gap-2 text-sm font-bold uppercase tracking-[0.14em] text-emerald-700 dark:text-emerald-400">
            <i class="fa <?=cams_report_e($definition['icon'])?>"></i>
            <?=cams_report_e($definition['eyebrow'])?>
          </div>
          <h1 class="m-0 text-3xl font-extrabold tracking-tight text-slate-950 dark:text-white">
            <?=cams_report_e($definition['title'])?>
          </h1>
          <p class="mt-2 max-w-3xl text-base text-slate-500 dark:text-slate-400">
            <?=cams_report_e($definition['description'])?>
          </p>
          <div class="mt-3 flex flex-wrap items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
            <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1.5 dark:border-slate-800 dark:bg-slate-900">
              <i class="fa fa-calendar text-emerald-600"></i>
              <?=cams_report_e($monthLabel)?>
            </span>
            <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1.5 dark:border-slate-800 dark:bg-slate-900">
              <i class="fa fa-filter text-emerald-600"></i>
              <?=cams_report_e($reportScopeLabel)?>
            </span>
          </div>
        </div>

        <div class="flex flex-wrap items-center gap-2 print:hidden">
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
      <div class="grid gap-4 xl:grid-cols-[1fr_auto]">
        <nav class="flex flex-wrap items-center gap-2 rounded-2xl border border-slate-200 bg-white p-2 shadow-sm dark:border-slate-800 dark:bg-slate-900 print:hidden">
          <?php foreach ($definitions as $key => $item): ?>
            <a
              href="<?=base_url($reportRoutes[$key])?>?month=<?=urlencode($month)?>"
              class="<?= $key === $type
                ? 'inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-bold text-white shadow-sm'
                : 'inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' ?>"
            >
              <i class="fa <?=cams_report_e($item['icon'])?>"></i>
              <?=cams_report_e($item['title'])?>
            </a>
          <?php endforeach; ?>
        </nav>

        <form method="get" action="<?=base_url($currentRoute)?>" class="flex items-end gap-2 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm dark:border-slate-800 dark:bg-slate-900 print:hidden">
          <div>
            <label for="report-month" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">Report month</label>
            <input
              id="report-month"
              name="month"
              type="month"
              value="<?=cams_report_e($month)?>"
              max="<?=date('Y-m', strtotime('+2 months'))?>"
              class="form-control !min-h-11 !min-w-[180px]"
              required
            >
          </div>
          <button type="submit" class="btn btn-primary !min-h-11">
            <i class="fa fa-refresh"></i>
            Apply
          </button>
        </form>
      </div>

      <?php if ($reportRoleKey === 'secretary' && (int) $reportClinicCount === 0): ?>
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-amber-900 dark:border-amber-900/70 dark:bg-amber-950/30 dark:text-amber-200">
          <div class="flex items-start gap-3">
            <i class="fa fa-exclamation-triangle mt-0.5"></i>
            <div>
              <strong class="block text-base">No assigned clinics</strong>
              <p class="mb-0 mt-1 text-sm">This secretary account has no clinic assignment for its linked physician, so the report is intentionally empty.</p>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-6">
        <?php
        $cards = array(
            array('label' => 'Total appointments', 'value' => isset($summary['total']) ? $summary['total'] : 0, 'icon' => 'fa-calendar', 'tone' => 'emerald'),
            array('label' => 'Pending', 'value' => isset($summary['pending']) ? $summary['pending'] : 0, 'icon' => 'fa-clock-o', 'tone' => 'amber'),
            array('label' => 'Done', 'value' => isset($summary['done']) ? $summary['done'] : 0, 'icon' => 'fa-check', 'tone' => 'green'),
            array('label' => 'Cancelled', 'value' => isset($summary['cancelled']) ? $summary['cancelled'] : 0, 'icon' => 'fa-times', 'tone' => 'rose'),
            array('label' => 'Unique patients', 'value' => isset($summary['unique_patients']) ? $summary['unique_patients'] : 0, 'icon' => 'fa-users', 'tone' => 'blue'),
            array('label' => 'Completion rate', 'value' => (isset($summary['completion_rate']) ? $summary['completion_rate'] : 0) . '%', 'icon' => 'fa-line-chart', 'tone' => 'violet'),
        );
        $toneClasses = array(
            'emerald' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300',
            'amber' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300',
            'green' => 'bg-green-50 text-green-700 dark:bg-green-950/40 dark:text-green-300',
            'rose' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300',
            'blue' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300',
            'violet' => 'bg-violet-50 text-violet-700 dark:bg-violet-950/40 dark:text-violet-300',
        );
        ?>
        <?php foreach ($cards as $card): ?>
          <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-start justify-between gap-3">
              <div>
                <p class="mb-0 text-sm font-semibold text-slate-500 dark:text-slate-400"><?=cams_report_e($card['label'])?></p>
                <p class="mb-0 mt-2 text-3xl font-extrabold tracking-tight text-slate-950 dark:text-white"><?=cams_report_e($card['value'])?></p>
              </div>
              <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl <?=$toneClasses[$card['tone']]?>">
                <i class="fa <?=cams_report_e($card['icon'])?>"></i>
              </span>
            </div>
          </article>
        <?php endforeach; ?>
      </div>

      <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="mb-4 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h2 class="m-0 text-xl font-extrabold text-slate-950 dark:text-white"><?=cams_report_e($chartTitle)?></h2>
            <p class="mb-0 mt-1 text-sm text-slate-500 dark:text-slate-400"><?=cams_report_e($monthLabel)?> · daily distribution</p>
          </div>
          <span class="text-sm font-semibold text-slate-500 dark:text-slate-400"><?=count($daily)?> calendar days</span>
        </div>
        <div class="relative h-[300px]">
          <canvas id="cams-report-trend"></canvas>
        </div>
      </section>

      <?php if ($type === 'average'): ?>
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
          <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-800">
            <h2 class="m-0 text-xl font-extrabold text-slate-950 dark:text-white">Clinic workload breakdown</h2>
            <p class="mb-0 mt-1 text-sm text-slate-500 dark:text-slate-400">
              Calendar-day average = total appointments ÷ <?=number_format((int)$report['days_in_month'])?> days. Active-day average = total appointments ÷ days where that clinic had at least one appointment.
            </p>
          </div>
          <div class="overflow-x-auto">
            <table class="table cams-modern-table !m-0">
              <thead>
                <tr>
                  <th>Clinic</th>
                  <th>Appointments</th>
                  <th>Active Days</th>
                  <th>Avg / Calendar Day</th>
                  <th>Avg / Active Day</th>
                  <th>Done</th>
                  <th>Cancelled</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($clinicAverage): ?>
                  <?php foreach ($clinicAverage as $row): ?>
                    <tr>
                      <td>
                        <strong><?=cams_report_e($row['name'])?></strong>
                        <small><?=cams_report_e($row['location'] ?: 'No location recorded')?></small>
                      </td>
                      <td><strong><?=number_format((int)$row['total'])?></strong></td>
                      <td><?=number_format((int)$row['active_days'])?></td>
                      <td><?=number_format((float)$row['average_per_calendar_day'], 2)?></td>
                      <td><?=number_format((float)$row['average_per_active_day'], 2)?></td>
                      <td><span class="cams-status cams-status-done"><span></span><?=number_format((int)$row['done'])?></span></td>
                      <td><span class="cams-status cams-status-cancelled"><span></span><?=number_format((int)$row['cancelled'])?></span></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="7">
                      <div class="cams-empty-state">
                        <i class="fa fa-bar-chart"></i>
                        <strong>No appointment activity for <?=cams_report_e($monthLabel)?></strong>
                        <span>Choose another month to review clinic workload.</span>
                      </div>
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </section>
      <?php else: ?>
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
          <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-800">
            <h2 class="m-0 text-xl font-extrabold text-slate-950 dark:text-white">
              <?= $type === 'done' ? 'Completed appointment records' : 'Cancelled appointment records' ?>
            </h2>
            <p class="mb-0 mt-1 text-sm text-slate-500 dark:text-slate-400">
              <?=number_format(count($appointments))?> matching record<?=count($appointments) === 1 ? '' : 's'?> for <?=cams_report_e($monthLabel)?>.
            </p>
          </div>
          <div class="overflow-x-auto">
            <table class="table cams-modern-table !m-0">
              <thead>
                <tr>
                  <th>Appointment</th>
                  <th>Date</th>
                  <th>Patient</th>
                  <th>Purpose</th>
                  <th>Clinic</th>
                  <th>Queue</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($appointments): ?>
                  <?php foreach ($appointments as $row): ?>
                    <tr>
                      <td><span class="cams-id">#<?=str_pad((string)$row['appointment_id'], 8, '0', STR_PAD_LEFT)?></span></td>
                      <td>
                        <strong><?=date('M j, Y', strtotime($row['app_date']))?></strong>
                        <small><?=date('l', strtotime($row['app_date']))?></small>
                      </td>
                      <td>
                        <strong><?=cams_report_e(trim($row['fname'] . ' ' . $row['mname'] . ' ' . $row['lname']))?></strong>
                      </td>
                      <td><span class="cams-purpose"><?=cams_report_e($row['purpose'])?></span></td>
                      <td>
                        <strong><?=cams_report_e($row['clinic_name'])?></strong>
                        <small><?=cams_report_e($row['clinic_location'])?></small>
                      </td>
                      <td><span class="cams-queue"><?=number_format((int)$row['queueNum'])?></span></td>
                      <td>
                        <span class="cams-status <?=$type === 'done' ? 'cams-status-done' : 'cams-status-cancelled'?>">
                          <span></span>
                          <?=cams_report_e($row['app_status'])?>
                        </span>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="7">
                      <div class="cams-empty-state">
                        <i class="fa <?=$type === 'done' ? 'fa-check-circle' : 'fa-times-circle'?>"></i>
                        <strong>No <?=$type === 'done' ? 'completed' : 'cancelled'?> appointments for <?=cams_report_e($monthLabel)?></strong>
                        <span>Choose another month to review historical records.</span>
                      </div>
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </section>
      <?php endif; ?>
    </div>
  </section>
</div>

<footer class="main-footer print:hidden">
  <strong>Copyright &copy; 2018</strong> All rights reserved.
</footer>
</div>

<style>
@media print {
  @page { margin: 12mm; }
  .main-header, .main-sidebar, .main-footer, .print\:hidden { display: none !important; }
  .content-wrapper { margin-left: 0 !important; min-height: auto !important; background: #fff !important; }
  .content-header, .content { padding-left: 0 !important; padding-right: 0 !important; }
  body { background: #fff !important; }
  .rounded-2xl { break-inside: avoid; }
}
</style>

<script>
(function () {
  var daily = <?=json_encode($daily)?>;
  var metric = <?=json_encode($chartMetric)?>;
  var title = <?=json_encode($chartTitle)?>;
  var chart = null;

  function palette() {
    var dark = document.documentElement.classList.contains('dark');
    return {
      text: dark ? '#cbd5e1' : '#64748b',
      grid: dark ? 'rgba(148,163,184,.14)' : 'rgba(148,163,184,.18)',
      line: metric === 'cancelled' ? '#e11d48' : (metric === 'done' ? '#16a34a' : '#16845e'),
      fill: metric === 'cancelled' ? 'rgba(225,29,72,.10)' : (metric === 'done' ? 'rgba(22,163,74,.10)' : 'rgba(22,132,94,.10)')
    };
  }

  function renderChart() {
    var canvas = document.getElementById('cams-report-trend');
    if (!canvas || !window.Chart) return;
    var c = palette();

    if (chart) chart.destroy();
    chart = new Chart(canvas, {
      type: 'line',
      data: {
        labels: daily.map(function (item) { return item.label; }),
        datasets: [{
          label: title,
          data: daily.map(function (item) { return Number(item[metric] || 0); }),
          borderColor: c.line,
          backgroundColor: c.fill,
          borderWidth: 2,
          fill: true,
          tension: .32,
          pointRadius: 2,
          pointHoverRadius: 5
        }]
      },
      options: {
        maintainAspectRatio: false,
        responsive: true,
        plugins: {
          legend: { display: false },
          tooltip: {
            intersect: false,
            mode: 'index'
          }
        },
        scales: {
          x: {
            ticks: { color: c.text, maxTicksLimit: 12, maxRotation: 0 },
            grid: { display: false }
          },
          y: {
            beginAtZero: true,
            precision: 0,
            ticks: { color: c.text, precision: 0 },
            grid: { color: c.grid }
          }
        }
      }
    });
  }

  document.addEventListener('DOMContentLoaded', renderChart);
  document.addEventListener('cams:theme-changed', renderChart);
})();
</script>
</body>
</html>
