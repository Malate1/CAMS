<?php
$this->load->view($calendarHeader);

$calendarClinics = isset($calendarClinics) && is_array($calendarClinics) ? $calendarClinics : (array) $calendarClinics;
?>
<link rel="stylesheet" href="<?=base_url()?>vendors/fullcalendar/css/fullcalendar.min.css">
<link rel="stylesheet" href="<?=base_url()?>css/cams-calendar.css?v=<?=@filemtime(FCPATH.'css/cams-calendar.css')?>">

<div
  class="content-wrapper bg-slate-50 dark:bg-slate-950"
  x-data="{ drawerOpen: false, day: null }"
  @cams:calendar-day.window="day = $event.detail; drawerOpen = true"
  @keydown.escape.window="drawerOpen = false"
>
  <section class="content-header !px-4 !pt-6 !pb-2 sm:!px-6 lg:!px-8">
    <div class="w-full">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
          <div class="mb-2 flex items-center gap-2 text-sm font-bold uppercase tracking-[0.14em] text-emerald-700 dark:text-emerald-400">
            <i class="fa fa-calendar"></i>
            Appointments
          </div>
          <h1 class="m-0 text-3xl font-extrabold tracking-tight text-slate-950 dark:text-white">Appointment Calendar</h1>
          <p class="mt-2 max-w-3xl text-base text-slate-500 dark:text-slate-400">
            Review scheduled patient visits by date, clinic, and appointment status.
          </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
          <a href="<?=htmlspecialchars($calendarListUrl, ENT_QUOTES, 'UTF-8')?>" class="btn btn-default">
            <i class="fa fa-list"></i>
            View List
          </a>
          <a href="<?=htmlspecialchars($calendarBookUrl, ENT_QUOTES, 'UTF-8')?>" class="btn btn-primary">
            <i class="fa fa-plus"></i>
            Book Appointment
          </a>
        </div>
      </div>
    </div>
  </section>

  <section class="content !px-4 !pb-10 sm:!px-6 lg:!px-8">
    <div class="w-full space-y-4">
      <section class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="border-b border-slate-100 px-4 py-4 sm:px-5 dark:border-slate-800">
          <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div>
              <h2 class="m-0 text-xl font-extrabold text-slate-950 dark:text-white">Calendar Filters</h2>
              <p class="mb-0 mt-1 text-sm text-slate-500 dark:text-slate-400">
                Narrow the calendar without leaving the page.
              </p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-end">
              <div class="min-w-[220px]">
                <label for="cams-calendar-clinic" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">Clinic</label>
                <select id="cams-calendar-clinic" class="form-control !min-h-11">
                  <option value="">All clinics</option>
                  <?php foreach ($calendarClinics as $clinic): ?>
                    <option value="<?=htmlspecialchars((string)$clinic->clinic_id, ENT_QUOTES, 'UTF-8')?>">
                      <?=htmlspecialchars((string)$clinic->name, ENT_QUOTES, 'UTF-8')?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="min-w-[180px]">
                <label for="cams-calendar-status" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">Status</label>
                <select id="cams-calendar-status" class="form-control !min-h-11">
                  <option value="">All statuses</option>
                  <option value="Pending">Pending</option>
                  <option value="Done">Done</option>
                  <option value="Cancelled">Cancelled</option>
                </select>
              </div>

              <button id="cams-calendar-clear" type="button" class="btn btn-default !min-h-11">
                <i class="fa fa-undo"></i>
                Clear Filters
              </button>
            </div>
          </div>

          <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-slate-500 dark:text-slate-400">
            <span class="font-bold text-slate-700 dark:text-slate-200">Status legend</span>
            <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span>Pending</span>
            <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>Done</span>
            <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-rose-500"></span>Cancelled</span>
            <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-blue-500"></span>Mixed</span>
          </div>
        </div>

        <div class="p-3 sm:p-4">
          <div id="cams-calendar-loading" class="mb-3 hidden items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-600 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-300">
            <i class="fa fa-spinner fa-spin text-emerald-600"></i>
            Loading appointments…
          </div>

          <div id="calendar" class="cams-modern-calendar"></div>
        </div>
      </section>
    </div>
  </section>

  <div
    x-cloak
    x-show.important="drawerOpen"
    x-transition.opacity
    class="fixed inset-0 z-[10020] bg-slate-950/45 backdrop-blur-[2px]"
    @click="drawerOpen = false"
  ></div>

  <aside
    x-cloak
    x-show.important="drawerOpen"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="translate-x-full"
    class="fixed inset-y-0 right-0 z-[10030] flex w-full max-w-xl flex-col border-l border-slate-200 bg-white shadow-2xl dark:border-slate-800 dark:bg-slate-900"
  >
    <template x-if="day">
      <div class="flex h-full flex-col">
        <div class="border-b border-slate-100 px-5 py-5 dark:border-slate-800 sm:px-6">
          <div class="flex items-start justify-between gap-4">
            <div>
              <span class="text-xs font-extrabold uppercase tracking-[0.14em] text-emerald-700 dark:text-emerald-400">Daily appointments</span>
              <h2 class="mb-0 mt-1 text-2xl font-extrabold tracking-tight text-slate-950 dark:text-white" x-text="day.label"></h2>
              <p class="mb-0 mt-1 text-sm text-slate-500 dark:text-slate-400">
                <span x-text="day.count"></span>
                <span x-text="day.count === 1 ? ' appointment' : ' appointments'"></span>
              </p>
            </div>
            <button type="button" @click="drawerOpen = false" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-white" aria-label="Close appointment drawer">
              <i class="fa fa-times"></i>
            </button>
          </div>

          <div class="mt-4 flex flex-wrap gap-2">
            <span class="rounded-full bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-700 dark:bg-amber-950/40 dark:text-amber-300">
              <span x-text="day.pending"></span> Pending
            </span>
            <span class="rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">
              <span x-text="day.done"></span> Done
            </span>
            <span class="rounded-full bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-700 dark:bg-rose-950/40 dark:text-rose-300">
              <span x-text="day.cancelled"></span> Cancelled
            </span>
          </div>
        </div>

        <div class="flex-1 overflow-y-auto p-5 sm:p-6">
          <template x-if="day.appointments && day.appointments.length">
            <div class="space-y-3">
              <template x-for="appointment in day.appointments" :key="appointment.id">
                <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-950">
                  <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                      <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex min-h-7 min-w-7 items-center justify-center rounded-lg bg-emerald-50 px-2 text-xs font-extrabold text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300">
                          Q<span x-text="appointment.queue"></span>
                        </span>
                        <span
                          class="rounded-full px-2.5 py-1 text-xs font-extrabold"
                          :class="appointment.status === 'Done'
                            ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300'
                            : (appointment.status === 'Cancelled'
                              ? 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300'
                              : 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300')"
                          x-text="appointment.status"
                        ></span>
                      </div>

                      <h3 class="mb-0 mt-3 truncate text-lg font-extrabold text-slate-950 dark:text-white" x-text="appointment.patient || 'Patient'"></h3>
                      <p class="mb-0 mt-1 text-sm font-semibold text-slate-700 dark:text-slate-200" x-text="appointment.purpose || 'No purpose recorded'"></p>
                    </div>

                    <span class="text-xs font-bold text-slate-400" x-text="'#' + String(appointment.id).padStart(8, '0')"></span>
                  </div>

                  <div class="mt-4 grid gap-2 text-sm sm:grid-cols-2">
                    <div class="rounded-xl bg-slate-50 px-3 py-2.5 dark:bg-slate-900">
                      <span class="block text-xs font-bold uppercase tracking-wide text-slate-400">Clinic</span>
                      <strong class="mt-1 block text-slate-800 dark:text-slate-100" x-text="appointment.clinic || '—'"></strong>
                      <small class="mt-0.5 block text-slate-500 dark:text-slate-400" x-text="appointment.location || ''"></small>
                    </div>
                    <div class="rounded-xl bg-slate-50 px-3 py-2.5 dark:bg-slate-900">
                      <span class="block text-xs font-bold uppercase tracking-wide text-slate-400">Physician</span>
                      <strong class="mt-1 block text-slate-800 dark:text-slate-100" x-text="appointment.physician || '—'"></strong>
                    </div>
                  </div>
                </article>
              </template>
            </div>
          </template>

          <template x-if="!day.appointments || !day.appointments.length">
            <div class="flex min-h-[280px] flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center dark:border-slate-700 dark:bg-slate-950">
              <span class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-2xl text-slate-400 shadow-sm dark:bg-slate-900">
                <i class="fa fa-calendar-o"></i>
              </span>
              <strong class="mt-4 text-lg text-slate-900 dark:text-white">No appointments on this day</strong>
              <p class="mb-0 mt-2 text-sm text-slate-500 dark:text-slate-400">Try another date or clear the active filters.</p>
            </div>
          </template>
        </div>

        <div class="border-t border-slate-100 bg-slate-50 px-5 py-4 dark:border-slate-800 dark:bg-slate-950 sm:px-6">
          <div class="flex flex-wrap justify-end gap-2">
            <button type="button" @click="drawerOpen = false" class="btn btn-default">Close</button>
            <a :href="'<?=htmlspecialchars($calendarListUrl, ENT_QUOTES, 'UTF-8')?>?app_date=' + encodeURIComponent(day.date)" class="btn btn-primary">
              <i class="fa fa-list"></i>
              Manage Day
            </a>
          </div>
        </div>
      </div>
    </template>
  </aside>
</div>

<footer class="main-footer">
  <strong>Copyright &copy; 2018</strong> All rights reserved.
</footer>
</div>

<script src="<?=base_url()?>vendors/moment/js/moment.min.js"></script>
<script src="<?=base_url()?>vendors/fullcalendar/js/fullcalendar.min.js"></script>

<script>
(function ($) {
  'use strict';

  var calendarDataUrl = <?=json_encode($calendarDataUrl)?>;
  var $calendar = $('#calendar');
  var $clinic = $('#cams-calendar-clinic');
  var $status = $('#cams-calendar-status');
  var $loading = $('#cams-calendar-loading');

  function dayPayload(event, dateOverride) {
    var date = dateOverride || (event && event.start ? event.start.format('YYYY-MM-DD') : '');
    return {
      date: date,
      label: date ? moment(date, 'YYYY-MM-DD').format('dddd, MMMM D, YYYY') : 'Selected day',
      count: event ? Number(event.count || 0) : 0,
      pending: event ? Number(event.pending || 0) : 0,
      done: event ? Number(event.done || 0) : 0,
      cancelled: event ? Number(event.cancelled || 0) : 0,
      appointments: event && Array.isArray(event.appointments) ? event.appointments : []
    };
  }

  function openDay(event, dateOverride) {
    window.dispatchEvent(new CustomEvent('cams:calendar-day', {
      detail: dayPayload(event, dateOverride)
    }));
  }

  function refetch() {
    if ($calendar.length && $calendar.data('fullCalendar')) {
      $calendar.fullCalendar('refetchEvents');
    }
  }

  function renderEvent(event, element) {
    var details = [];
    if (Number(event.pending || 0) > 0) details.push('<span class="cams-cal-chip cams-cal-chip-pending">' + Number(event.pending) + ' Pending</span>');
    if (Number(event.done || 0) > 0) details.push('<span class="cams-cal-chip cams-cal-chip-done">' + Number(event.done) + ' Done</span>');
    if (Number(event.cancelled || 0) > 0) details.push('<span class="cams-cal-chip cams-cal-chip-cancelled">' + Number(event.cancelled) + ' Cancelled</span>');

    element.find('.fc-content').html(
      '<div class="cams-cal-event-title">' +
        '<strong>' + Number(event.count || 0) + ' appointment' + (Number(event.count || 0) === 1 ? '' : 's') + '</strong>' +
        '<span>View day</span>' +
      '</div>' +
      '<div class="cams-cal-event-statuses">' + details.join('') + '</div>'
    );
    element.attr('title', 'Open appointments for ' + event.start.format('MMMM D, YYYY'));
  }

  if ($calendar.length) {
    $calendar.fullCalendar({
      displayEventTime: false,
      fixedWeekCount: false,
      aspectRatio: 1.8,
      height: 'auto',
      navLinks: false,
      editable: false,
      selectable: false,
      eventLimit: true,
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay'
      },
      buttonText: {
        prev: '‹',
        next: '›',
        today: 'Today',
        month: 'Month',
        week: 'Week',
        day: 'Day'
      },
      events: function (start, end, timezone, callback) {
        $loading.removeClass('hidden').addClass('flex');

        $.ajax({
          url: calendarDataUrl,
          dataType: 'json',
          cache: false,
          data: {
            start: start.format('YYYY-MM-DD'),
            end: end.format('YYYY-MM-DD'),
            clinic_id: $clinic.val() || '',
            status: $status.val() || ''
          }
        }).done(function (events) {
          callback(Array.isArray(events) ? events : []);
        }).fail(function () {
          callback([]);
          if (window.CamsUI) {
            CamsUI.notify('error', 'Calendar unavailable', 'Appointment events could not be loaded. Please try again.');
          } else if (window.toastr) {
            toastr.error('Appointment events could not be loaded.', 'Calendar unavailable');
          }
        }).always(function () {
          $loading.addClass('hidden').removeClass('flex');
        });
      },
      eventRender: renderEvent,
      eventClick: function (event, jsEvent) {
        if (jsEvent) jsEvent.preventDefault();
        openDay(event);
        return false;
      },
      dayClick: function (date) {
        var dateString = date.format('YYYY-MM-DD');
        var events = $calendar.fullCalendar('clientEvents', function (event) {
          return event.start && event.start.format('YYYY-MM-DD') === dateString;
        });
        openDay(events.length ? events[0] : null, dateString);
      },
      loading: function (isLoading) {
        $loading.toggleClass('hidden', !isLoading).toggleClass('flex', isLoading);
      }
    });

    $clinic.on('change', refetch);
    $status.on('change', refetch);

    $('#cams-calendar-clear').on('click', function () {
      $clinic.val('');
      $status.val('');
      refetch();
    });

    $(document).on('cams:theme-changed', function () {
      $calendar.fullCalendar('rerenderEvents');
    });
  }
})(window.jQuery);
</script>
</body>
</html>
