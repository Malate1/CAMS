(function () {
  'use strict';

  function initBookingWizard() {
    var root = document.querySelector('[data-cams-booking]');
    if (!root) return;

    var form = root.querySelector('#appointment_form');
    var panels = Array.prototype.slice.call(root.querySelectorAll('.cams-wizard-panel'));
    var tabs = Array.prototype.slice.call(root.querySelectorAll('.cams-step-tab'));
    var complaintCards = Array.prototype.slice.call(root.querySelectorAll('.cams-complaint-card'));
    var providerCards = Array.prototype.slice.call(root.querySelectorAll('.cams-provider-card'));
    var dateGrid = root.querySelector('#cams-date-grid');
    var providerGrid = root.querySelector('#cams-provider-grid');
    var providerEmpty = root.querySelector('#cams-provider-empty');
    var customBox = root.querySelector('#cams-custom-box');
    var customPurpose = root.querySelector('#custom_purpose');
    var customSpecialty = root.querySelector('#custom_specialty');
    var recommendation = root.querySelector('#cams-recommendation');
    var selectedProviderBox = root.querySelector('#cams-selected-provider');
    var loading = root.querySelector('#cams-date-loading');
    var nextButtons = Array.prototype.slice.call(root.querySelectorAll('[data-next-step]'));
    var backButtons = Array.prototype.slice.call(root.querySelectorAll('[data-prev-step]'));

    var purposeInput = form.querySelector('[name="purpose"]');
    var physicianInput = form.querySelector('[name="physician_id"]');
    var clinicInput = form.querySelector('[name="clinic_id"]');
    var dateInput = form.querySelector('[name="app_date"]');
    var specialtyInput = form.querySelector('[name="specialty_name"]');

    var state = {
      step: 1,
      complaint: '',
      requiredSpecialties: [],
      isCustom: false,
      provider: null,
      date: null
    };
    var dateAdvanceTimer = null;

    function normalize(value) {
      return String(value || '').trim().toLowerCase();
    }

    function safeParse(value, fallback) {
      try { return JSON.parse(value); } catch (e) { return fallback; }
    }

    function intersection(a, b) {
      var bNormalized = b.map(normalize);
      return a.filter(function (item) { return bNormalized.indexOf(normalize(item)) !== -1; });
    }

    function showStep(step) {
      state.step = step;
      panels.forEach(function (panel, index) {
        panel.classList.toggle('is-active', index === step - 1);
      });
      tabs.forEach(function (tab, index) {
        tab.classList.toggle('is-active', index === step - 1);
        tab.classList.toggle('is-complete', index < step - 1);
      });
      root.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function setStepSummary(step, text) {
      var target = root.querySelector('[data-step-summary="' + step + '"]');
      if (target) target.textContent = text || '';
    }

    function clearProviderSelection() {
      state.provider = null;
      physicianInput.value = '';
      clinicInput.value = '';
      specialtyInput.value = '';
      providerCards.forEach(function (card) { card.classList.remove('is-selected'); });
      dateInput.value = '';
      state.date = null;
      dateGrid.innerHTML = '';
    }

    function filterProviders() {
      clearProviderSelection();
      var required = state.requiredSpecialties;
      var visible = 0;
      providerCards.forEach(function (card) {
        var specialties = safeParse(card.getAttribute('data-specialties'), []);
        var matches = required.length === 0 || intersection(specialties, required).length > 0;
        card.style.display = matches ? '' : 'none';
        if (matches) visible++;
      });
      providerEmpty.style.display = visible ? 'none' : '';

      recommendation.innerHTML = '';
      required.forEach(function (specialty) {
        var chip = document.createElement('span');
        chip.className = 'cams-specialty-chip';
        chip.textContent = specialty;
        recommendation.appendChild(chip);
      });
    }

    function applyComplaint(card) {
      complaintCards.forEach(function (item) { item.classList.remove('is-selected'); });
      card.classList.add('is-selected');

      state.isCustom = card.getAttribute('data-custom') === '1';
      state.complaint = card.getAttribute('data-value') || '';
      state.requiredSpecialties = safeParse(card.getAttribute('data-specialties'), []);
      customBox.style.display = state.isCustom ? '' : 'none';

      if (!state.isCustom) {
        purposeInput.value = state.complaint;
        customPurpose.value = '';
        customSpecialty.value = '';
        setStepSummary(1, state.complaint);
        filterProviders();
      } else {
        purposeInput.value = customPurpose.value.trim();
        state.requiredSpecialties = customSpecialty.value ? [customSpecialty.value] : [];
        setStepSummary(1, purposeInput.value || 'Other concern');
        filterProviders();
      }
      updateButtons();
    }

    function updateCustomComplaint() {
      if (!state.isCustom) return;
      purposeInput.value = customPurpose.value.trim();
      state.complaint = purposeInput.value;
      state.requiredSpecialties = customSpecialty.value ? [customSpecialty.value] : [];
      setStepSummary(1, state.complaint || 'Other concern');
      filterProviders();
      updateButtons();
    }

    function selectProvider(card) {
      providerCards.forEach(function (item) { item.classList.remove('is-selected'); });
      card.classList.add('is-selected');

      var specialties = safeParse(card.getAttribute('data-specialties'), []);
      var matched = intersection(specialties, state.requiredSpecialties);
      var specialty = state.isCustom ? customSpecialty.value : (matched[0] || specialties[0] || '');

      state.provider = {
        physicianId: card.getAttribute('data-physician-id'),
        clinicId: card.getAttribute('data-clinic-id'),
        doctor: card.getAttribute('data-doctor'),
        clinic: card.getAttribute('data-clinic'),
        location: card.getAttribute('data-location'),
        schedule: card.getAttribute('data-schedule'),
        specialty: specialty
      };

      physicianInput.value = state.provider.physicianId;
      clinicInput.value = state.provider.clinicId;
      specialtyInput.value = specialty;
      setStepSummary(2, state.provider.doctor + ' · ' + state.provider.clinic);
      selectedProviderBox.querySelector('[data-selected-doctor]').textContent = state.provider.doctor;
      selectedProviderBox.querySelector('[data-selected-clinic]').textContent = state.provider.clinic + ' · ' + state.provider.schedule;
      updateButtons();
    }

    function loadDates() {
      if (!state.provider) return;
      dateGrid.innerHTML = '';
      loading.style.display = '';
      var url = root.getAttribute('data-availability-url') + '?physician_id=' + encodeURIComponent(state.provider.physicianId) + '&clinic_id=' + encodeURIComponent(state.provider.clinicId);

      fetch(url, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(function (response) {
          if (!response.ok) throw new Error('Unable to load appointment dates.');
          return response.json();
        })
        .then(function (payload) {
          renderDates(payload && payload.dates ? payload.dates : []);
        })
        .catch(function () {
          dateGrid.innerHTML = '<div class="cams-empty-state" style="grid-column:1/-1"><i class="fa fa-exclamation-circle"></i>Available dates could not be loaded. Please refresh and try again.</div>';
        })
        .then(function () { loading.style.display = 'none'; });
    }

    function renderDates(dates) {
      dateGrid.innerHTML = '';
      state.date = null;
      dateInput.value = '';
      if (dateAdvanceTimer) {
        window.clearTimeout(dateAdvanceTimer);
        dateAdvanceTimer = null;
      }
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
          button.setAttribute('data-date', item.date);
          button.setAttribute('data-label', item.label);
          button.setAttribute('data-schedule', item.schedule || '');
          if (item.unavailable_reason) button.title = item.unavailable_reason;
          var availabilityLabel = item.patient_booked
            ? 'Already booked'
            : (item.schedule_conflict
                ? 'Schedule conflict'
                : (item.capacity_full ? 'Fully booked' : item.remaining + ' slot' + (item.remaining === 1 ? '' : 's') + ' left'));
          button.innerHTML = '<span class="dow">' + item.weekday + '</span><span class="day">' + item.day + '</span><span class="month">' + item.month + '</span><span class="slot">' + availabilityLabel + '</span>';
          button.addEventListener('click', function () {
            Array.prototype.slice.call(dateGrid.querySelectorAll('.cams-date-card')).forEach(function (b) { b.classList.remove('is-selected'); });
            button.classList.add('is-selected');
            state.date = { value: item.date, label: item.label, schedule: item.schedule };
            dateInput.value = item.date;
            setStepSummary(3, item.label);
            updateButtons();

            if (dateAdvanceTimer) window.clearTimeout(dateAdvanceTimer);
            dateAdvanceTimer = window.setTimeout(function () {
              if (!state.date || state.date.value !== item.date) return;
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

    function populateReview() {
      root.querySelector('[data-review-purpose]').textContent = purposeInput.value;
      root.querySelector('[data-review-specialty]').textContent = specialtyInput.value || 'General care';
      root.querySelector('[data-review-doctor]').textContent = state.provider ? state.provider.doctor : '';
      root.querySelector('[data-review-clinic]').textContent = state.provider ? state.provider.clinic : '';
      root.querySelector('[data-review-location]').textContent = state.provider ? state.provider.location : '';
      root.querySelector('[data-review-date]').textContent = state.date ? state.date.label : '';
      root.querySelector('[data-review-schedule]').textContent = state.date && state.date.schedule ? state.date.schedule : (state.provider ? state.provider.schedule : '');
    }

    function validForStep(step) {
      if (step === 1) {
        if (state.isCustom) return customPurpose.value.trim().length > 0 && customSpecialty.value !== '';
        return purposeInput.value !== '';
      }
      if (step === 2) return !!state.provider;
      if (step === 3) return !!state.date && dateInput.value !== '';
      return true;
    }

    function updateButtons() {
      nextButtons.forEach(function (button) {
        var currentStep = parseInt(button.getAttribute('data-next-step'), 10) - 1;
        button.disabled = !validForStep(currentStep);
      });
    }

    complaintCards.forEach(function (card) {
      card.addEventListener('click', function () { applyComplaint(card); });
    });
    customPurpose.addEventListener('input', updateCustomComplaint);
    customSpecialty.addEventListener('change', updateCustomComplaint);
    if (window.jQuery) {
      window.jQuery(customSpecialty).off('change.camsBooking').on('change.camsBooking', updateCustomComplaint);
    }
    providerCards.forEach(function (card) {
      card.addEventListener('click', function () { selectProvider(card); });
    });

    nextButtons.forEach(function (button) {
      button.addEventListener('click', function () {
        var target = parseInt(button.getAttribute('data-next-step'), 10);
        var current = target - 1;
        if (!validForStep(current)) return;
        if (target === 3) loadDates();
        if (target === 4) populateReview();
        showStep(target);
      });
    });

    backButtons.forEach(function (button) {
      button.addEventListener('click', function () {
        showStep(parseInt(button.getAttribute('data-prev-step'), 10));
      });
    });

    form.addEventListener('submit', function (event) {
      if (!validForStep(1) || !validForStep(2) || !validForStep(3)) {
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
    document.addEventListener('DOMContentLoaded', initBookingWizard);
  } else {
    initBookingWizard();
  }
})();
