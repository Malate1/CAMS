(function () {
  'use strict';

  function init() {
    var root = document.querySelector('[data-cams-staff-booking]');
    if (!root) return;

    var form = root.querySelector('#appointment_form');
    var panels = [].slice.call(root.querySelectorAll('.cams-wizard-panel'));
    var tabs = [].slice.call(root.querySelectorAll('.cams-step-tab'));
    var patients = [].slice.call(root.querySelectorAll('[data-patient-card]'));
    var complaints = [].slice.call(root.querySelectorAll('.cams-complaint-card'));
    var clinics = [].slice.call(root.querySelectorAll('[data-clinic-card]'));
    var dateGrid = root.querySelector('#cams-date-grid');
    var loading = root.querySelector('#cams-date-loading');
    var customBox = root.querySelector('#cams-custom-box');
    var customPurpose = root.querySelector('#custom_purpose');
    var patientInput = form.querySelector('[name="patient_id"]');
    var purposeInput = form.querySelector('[name="purpose"]');
    var clinicInput = form.querySelector('[name="clinic_id"]');
    var dateInput = form.querySelector('[name="app_date"]');
    var dateAdvanceTimer = null;

    var state = {
      step: 1,
      patient: null,
      purpose: '',
      clinic: null,
      date: null
    };

    var patientSearch = root.querySelector('[data-patient-search]');
    if (patientSearch) {
      patientSearch.addEventListener('input', function () {
        var term = (patientSearch.value || '').trim().toLowerCase();
        patients.forEach(function (card) {
          card.style.display = !term || String(card.getAttribute('data-search') || '').indexOf(term) !== -1 ? '' : 'none';
        });
      });
    }

    function showStep(n) {
      state.step = n;
      panels.forEach(function (panel, index) {
        panel.classList.toggle('is-active', index === n - 1);
      });
      tabs.forEach(function (tab, index) {
        tab.classList.toggle('is-active', index === n - 1);
        tab.classList.toggle('is-complete', index < n - 1);
      });
      root.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function summary(n, text) {
      var el = root.querySelector('[data-step-summary="' + n + '"]');
      if (el) el.textContent = text || '';
    }

    function valid(n) {
      if (n === 1) return !!state.patient;
      if (n === 2) return purposeInput.value.trim() !== '';
      if (n === 3) return !!state.clinic && !!state.date;
      return true;
    }

    function updateButtons() {
      [].slice.call(root.querySelectorAll('[data-next-step]')).forEach(function (button) {
        var current = parseInt(button.getAttribute('data-next-step'), 10) - 1;
        button.disabled = !valid(current);
      });
    }

    function populateReview() {
      root.querySelector('[data-review-patient]').textContent = state.patient ? state.patient.name : '';
      root.querySelector('[data-review-purpose]').textContent = purposeInput.value;
      root.querySelector('[data-review-doctor]').textContent = state.clinic ? state.clinic.doctor : '';
      root.querySelector('[data-review-clinic]').textContent = state.clinic ? state.clinic.name : '';
      root.querySelector('[data-review-location]').textContent = state.clinic ? state.clinic.location : '';
      root.querySelector('[data-review-date]').textContent = state.date ? state.date.label : '';
      root.querySelector('[data-review-schedule]').textContent =
        state.date && state.date.schedule ? state.date.schedule : (state.clinic ? state.clinic.schedule : '');
    }

    patients.forEach(function (card) {
      card.addEventListener('click', function () {
        patients.forEach(function (item) { item.classList.remove('is-selected'); });
        card.classList.add('is-selected');
        state.patient = {
          id: card.getAttribute('data-patient-id'),
          name: card.getAttribute('data-patient-name')
        };
        patientInput.value = state.patient.id;
        summary(1, state.patient.name);
        updateButtons();
      });
    });

    complaints.forEach(function (card) {
      card.addEventListener('click', function () {
        complaints.forEach(function (item) { item.classList.remove('is-selected'); });
        card.classList.add('is-selected');
        var custom = card.getAttribute('data-custom') === '1';
        customBox.style.display = custom ? '' : 'none';

        if (custom) {
          purposeInput.value = customPurpose.value.trim();
        } else {
          purposeInput.value = card.getAttribute('data-value') || '';
          customPurpose.value = '';
        }

        state.purpose = purposeInput.value;
        summary(2, state.purpose || 'Other concern');
        updateButtons();
      });
    });

    customPurpose.addEventListener('input', function () {
      purposeInput.value = customPurpose.value.trim();
      state.purpose = purposeInput.value;
      summary(2, state.purpose || 'Other concern');
      updateButtons();
    });

    function loadDates() {
      if (!state.clinic) return;
      dateGrid.innerHTML = '';
      loading.style.display = '';
      state.date = null;
      dateInput.value = '';

      if (dateAdvanceTimer) {
        window.clearTimeout(dateAdvanceTimer);
        dateAdvanceTimer = null;
      }

      var url = root.getAttribute('data-availability-url') +
        '?clinic_id=' + encodeURIComponent(state.clinic.id) +
        '&patient_id=' + encodeURIComponent(state.patient ? state.patient.id : '');

      fetch(url, {
        credentials: 'same-origin',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
        .then(function (response) {
          if (!response.ok) throw new Error();
          return response.json();
        })
        .then(function (payload) {
          renderDates(payload && payload.dates ? payload.dates : []);
        })
        .catch(function () {
          dateGrid.innerHTML = '<div class="cams-empty-state" style="grid-column:1/-1"><i class="fa fa-exclamation-circle"></i>Available dates could not be loaded.</div>';
        })
        .then(function () {
          loading.style.display = 'none';
        });
    }

    function renderDates(dates) {
      dateGrid.innerHTML = '';

      if (!dates.length) {
        dateGrid.innerHTML = '<div class="cams-empty-state" style="grid-column:1/-1"><i class="fa fa-calendar-times-o"></i>No valid appointment dates are available within the next two months.</div>';
        updateButtons();
        return;
      }

      var pageSize = window.matchMedia && window.matchMedia('(max-width: 767px)').matches ? 6 : 8;
      var visibleCount = pageSize;

      function drawDates() {
        dateGrid.innerHTML = '';

        dates.slice(0, visibleCount).forEach(function (item) {
          var button = document.createElement('button');
          button.type = 'button';
          button.className = 'cams-date-card';
          button.disabled = !!item.full;
          if (item.unavailable_reason) button.title = item.unavailable_reason;

          var availabilityLabel = item.patient_booked
            ? 'Already booked'
            : (item.schedule_conflict
                ? 'Schedule conflict'
                : (item.capacity_full
                    ? 'Fully booked'
                    : item.remaining + ' slot' + (item.remaining === 1 ? '' : 's') + ' left'));

          button.innerHTML =
            '<span class="dow">' + item.weekday + '</span>' +
            '<span class="day">' + item.day + '</span>' +
            '<span class="month">' + item.month + '</span>' +
            '<span class="slot">' + availabilityLabel + '</span>';

          button.addEventListener('click', function () {
            [].slice.call(dateGrid.querySelectorAll('.cams-date-card')).forEach(function (itemButton) {
              itemButton.classList.remove('is-selected');
            });

            button.classList.add('is-selected');
            state.date = item;
            dateInput.value = item.date;
            summary(3, item.label);
            updateButtons();

            if (dateAdvanceTimer) window.clearTimeout(dateAdvanceTimer);
            dateAdvanceTimer = window.setTimeout(function () {
              if (!state.date || state.date.date !== item.date) return;
              populateReview();
              showStep(4);
            }, 320);
          });

          dateGrid.appendChild(button);
        });

        if (visibleCount < dates.length) {
          var moreWrap = document.createElement('div');
          moreWrap.className = 'cams-date-more';

          var moreButton = document.createElement('button');
          moreButton.type = 'button';
          moreButton.className = 'btn';
          moreButton.innerHTML = 'Show More Dates <i class="fa fa-chevron-down"></i>';
          moreButton.addEventListener('click', function () {
            visibleCount = Math.min(visibleCount + pageSize, dates.length);
            drawDates();
          });

          moreWrap.appendChild(moreButton);
          dateGrid.appendChild(moreWrap);
        }

        var note = document.createElement('div');
        note.className = 'cams-date-auto-note';
        note.textContent = 'Selecting an available date will continue to review automatically.';
        dateGrid.appendChild(note);
      }

      drawDates();
    }

    clinics.forEach(function (card) {
      card.addEventListener('click', function () {
        clinics.forEach(function (item) { item.classList.remove('is-selected'); });
        card.classList.add('is-selected');

        state.clinic = {
          id: card.getAttribute('data-clinic-id'),
          name: card.getAttribute('data-clinic'),
          location: card.getAttribute('data-location'),
          schedule: card.getAttribute('data-schedule'),
          doctor: card.getAttribute('data-doctor')
        };

        clinicInput.value = state.clinic.id;
        root.querySelector('[data-selected-doctor]').textContent = state.clinic.doctor;
        root.querySelector('[data-selected-clinic]').textContent = state.clinic.name + ' · ' + state.clinic.schedule;
        loadDates();
        updateButtons();
      });
    });

    [].slice.call(root.querySelectorAll('[data-next-step]')).forEach(function (button) {
      button.addEventListener('click', function () {
        var target = parseInt(button.getAttribute('data-next-step'), 10);
        var current = target - 1;
        if (!valid(current)) return;
        if (target === 4) populateReview();
        showStep(target);
      });
    });

    [].slice.call(root.querySelectorAll('[data-prev-step]')).forEach(function (button) {
      button.addEventListener('click', function () {
        showStep(parseInt(button.getAttribute('data-prev-step'), 10));
      });
    });

    form.addEventListener('submit', function (event) {
      if (!valid(1) || !valid(2) || !valid(3)) {
        event.preventDefault();
        return;
      }
      if (form.getAttribute('data-cams-ajax') === 'true') return;

      var submit = form.querySelector('[type="submit"]');
      if (submit) {
        submit.disabled = true;
        submit.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Booking appointment...';
      }
    });

    updateButtons();
    showStep(1);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
