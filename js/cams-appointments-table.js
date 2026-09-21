(function ($) {
  'use strict';

  var root = document.getElementById('cams-appointment-app');
  if (!root || !$.fn.DataTable) return;

  var role = root.dataset.role || 'patient';
  var scope = root.dataset.scope || 'all';
  var filterDate = root.dataset.date || '';
  var dataUrl = root.dataset.dataUrl;
  var editUrl = root.dataset.editUrl;
  var statusUrl = root.dataset.statusUrl;
  var availabilityUrl = root.dataset.availabilityUrl;
  var csrfName = root.dataset.csrfName || '';
  var csrfHash = root.dataset.csrfHash || '';

  function escapeHtml(value) {
    return $('<div>').text(value == null ? '' : String(value)).html();
  }

  function updateCsrf(payload) {
    if (payload && payload.csrf) {
      csrfName = payload.csrf.name || csrfName;
      csrfHash = payload.csrf.hash || csrfHash;
      root.dataset.csrfName = csrfName;
      root.dataset.csrfHash = csrfHash;
    }
  }

  function swal(icon, title, text) {
    if (window.CamsUI && typeof window.CamsUI.notify === 'function') {
      return window.CamsUI.notify(icon, title, text || '');
    }
    window.alert((title ? title + '\n' : '') + (text || ''));
    return Promise.resolve({ shown: true });
  }

  function statusBadge(status) {
    var key = String(status || '').toLowerCase();
    return '<span class="cams-status cams-status-' + escapeHtml(key) + '"><span></span>' + escapeHtml(status) + '</span>';
  }

  function actionButtons(row) {
    var buttons = [];
    if (row.can_edit) {
      buttons.push('<button type="button" class="btn cams-icon-btn cams-edit-appointment" data-id="' + escapeHtml(row.appointment_id) + '" title="Edit appointment"><i class="fa fa-pencil"></i><span>Edit</span></button>');
    }
    if (row.can_done) {
      buttons.push('<button type="button" class="btn cams-icon-btn cams-done-appointment" data-id="' + escapeHtml(row.appointment_id) + '" title="Mark as done"><i class="fa fa-check"></i><span>Done</span></button>');
    }
    if (row.can_cancel) {
      buttons.push('<button type="button" class="btn cams-icon-btn cams-danger-action cams-cancel-appointment" data-id="' + escapeHtml(row.appointment_id) + '" title="Cancel appointment"><i class="fa fa-times"></i><span>Cancel</span></button>');
    }
    if (!buttons.length) {
      return '<span class="cams-muted-action">No actions</span>';
    }
    return '<div class="cams-row-actions">' + buttons.join('') + '</div>';
  }

  var columns = [
    { data: 'appointment_id', name: 'appointment_id', render: function (v) { return '<span class="cams-id">#' + escapeHtml(v) + '</span>'; } },
    { data: 'app_date', name: 'app_date', render: function (v, type) {
      if (type !== 'display') return v;
      var d = new Date(v + 'T00:00:00');
      return isNaN(d.getTime()) ? escapeHtml(v) : '<strong>' + d.toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' }) + '</strong><small>' + d.toLocaleDateString(undefined, { weekday: 'long' }) + '</small>';
    } },
    { data: 'purpose', name: 'purpose', render: function (v) { return '<span class="cams-purpose">' + escapeHtml(v) + '</span>'; } }
  ];

  if (role === 'patient') {
    columns.push({ data: 'queue_number', name: 'queue_number', className: 'text-center', render: function (v) { return '<span class="cams-queue">' + escapeHtml(v) + '</span>'; } });
    columns.push({ data: 'physician_name', name: 'physician_name', render: function (v) { return '<span class="cams-person"><i class="fa fa-user-md"></i> Dr. ' + escapeHtml(v) + '</span>'; } });
  } else {
    columns.push({ data: 'patient_name', name: 'patient_name', render: function (v) { return '<span class="cams-person"><i class="fa fa-user"></i> ' + escapeHtml(v) + '</span>'; } });
  }

  columns.push({ data: 'clinic_name', name: 'clinic_name', render: function (v, type, row) {
    if (type !== 'display') return v;
    return '<span class="cams-clinic-name">' + escapeHtml(v) + '</span>' + (row.clinic_location ? '<small>' + escapeHtml(row.clinic_location) + '</small>' : '');
  } });
  columns.push({ data: 'status', name: 'status', className: 'text-center', render: function (v) { return statusBadge(v); } });

  if (role !== 'patient') {
    columns.push({ data: 'queue_number', name: 'queue_number', className: 'text-center', render: function (v) { return '<span class="cams-queue">' + escapeHtml(v) + '</span>'; } });
  }

  columns.push({ data: null, name: 'actions', orderable: false, searchable: false, className: 'text-right', render: function (data, type, row) { return actionButtons(row); } });

  var table = $('#cams-appointments-table').DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    autoWidth: false,
    searchDelay: 350,
    paging: true,
    pagingType: 'full_numbers',
    pageLength: 10,
    lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
    order: [[1, 'desc']],
    ajax: {
      url: dataUrl,
      type: 'GET',
      data: function (d) {
        d.scope = scope;
        if (filterDate) d.date = filterDate;
      },
      dataSrc: function (json) {
        updateCsrf(json);
        return json.data || [];
      },
      error: function (xhr) {
        var msg = 'Appointment records could not be loaded.';
        if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
        swal('error', 'Unable to load data', msg);
      }
    },
    columns: columns,
    dom: '<"cams-dt-toolbar"<"cams-dt-length"l><"cams-dt-actions"Bf>>rt<"cams-dt-footer"ip>',
    buttons: [
      { extend: 'copy', text: '<i class="fa fa-copy"></i><span>Copy</span>', className: 'btn cams-dt-button' },
      { extend: 'csv', text: '<i class="fa fa-file-text-o"></i><span>CSV</span>', className: 'btn cams-dt-button' },
      { extend: 'print', text: '<i class="fa fa-print"></i><span>Print</span>', className: 'btn cams-dt-button' }
    ],
    language: {
      processing: '<div class="cams-table-loader"><span></span><span></span><span></span><small>Loading appointments…</small></div>',
      search: '',
      searchPlaceholder: 'Search appointments…',
      lengthMenu: 'Show _MENU_',
      info: 'Showing _START_–_END_ of _TOTAL_ appointments',
      infoEmpty: 'No appointments found',
      emptyTable: '<div class="cams-empty-state"><i class="fa fa-calendar-o"></i><strong>No appointments yet</strong><span>New bookings will appear here automatically.</span></div>',
      paginate: { first: 'First', previous: 'Previous', next: 'Next', last: 'Last' }
    },
    drawCallback: function () {
      $('#cams-appointments-table').closest('.dataTables_wrapper').find('input[type="search"]').attr('aria-label', 'Search appointments');
    }
  });

  function fetchEditable(id) {
    return $.ajax({ url: editUrl, type: 'GET', dataType: 'json', data: { id: id } });
  }

  function loadAvailability(appointment) {
    var $select = $('#cams-edit-date');
    $select.prop('disabled', true).html('<option value="">Loading available dates…</option>');
    return $.ajax({
      url: availabilityUrl,
      type: 'GET',
      dataType: 'json',
      data: {
        physician_id: appointment.physician_id,
        clinic_id: appointment.clinic_id,
        patient_id: appointment.patient_id
      }
    }).done(function (response) {
      var options = [];
      var foundCurrent = false;
      (response.dates || []).forEach(function (item) {
        if (item.full && item.date !== appointment.app_date) return;
        if (item.date === appointment.app_date) foundCurrent = true;
        var suffix = item.full ? ' · Current booking' : ' · ' + item.remaining + ' slot' + (item.remaining === 1 ? '' : 's') + ' left';
        options.push('<option value="' + escapeHtml(item.date) + '" ' + (item.date === appointment.app_date ? 'selected' : '') + '>' + escapeHtml(item.label + ' · ' + item.schedule + suffix) + '</option>');
      });
      if (!foundCurrent && appointment.app_date) {
        options.unshift('<option value="' + escapeHtml(appointment.app_date) + '" selected>' + escapeHtml(appointment.app_date + ' · Current booking') + '</option>');
      }
      $select.html(options.length ? options.join('') : '<option value="">No dates available</option>').prop('disabled', false);
    }).fail(function () {
      $select.html('<option value="">Availability could not be loaded</option>');
      swal('error', 'Availability unavailable', 'Please close the editor and try again.');
    });
  }

  $('#cams-appointments-table').on('click', '.cams-edit-appointment', function () {
    var id = $(this).data('id');
    fetchEditable(id).done(function (response) {
      updateCsrf(response);
      if (!response.success) return swal('info', 'Appointment unavailable', response.message || 'This appointment is no longer editable.');
      var a = response.appointment;
      $('#cams-edit-id').val(a.appointment_id);
      $('#cams-edit-purpose').val(a.purpose);
      $('#cams-edit-patient').text(a.patient_name || '—');
      $('#cams-edit-physician').text(a.physician_name ? 'Dr. ' + a.physician_name : '—');
      $('#cams-edit-clinic').text(a.clinic_name || '—');
      $('#cams-edit-schedule').text(a.schedule || '—');
      if (window.CamsAlpineModal && typeof window.CamsAlpineModal.openLegacy === 'function') {
        window.CamsAlpineModal.openLegacy(document.getElementById('cams-edit-appointment-modal'));
      } else if ($.fn.modal) {
        $('#cams-edit-appointment-modal').modal('show');
      }
      loadAvailability(a);
    }).fail(function (xhr) {
      var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'This appointment is no longer editable.';
      swal('info', 'Appointment unavailable', msg);
    });
  });

  $('#cams-edit-appointment-form').on('submit', function (event) {
    event.preventDefault();
    var data = {
      appointment_id: $('#cams-edit-id').val(),
      purpose: $.trim($('#cams-edit-purpose').val()),
      app_date: $('#cams-edit-date').val()
    };
    if (!data.purpose || !data.app_date) {
      swal('info', 'Complete the form', 'Please provide a reason and select an appointment date.');
      return;
    }

    var runUpdate = function () {
      data[csrfName] = csrfHash;
      var $button = $('#cams-save-appointment').prop('disabled', true);
      $.ajax({ url: editUrl, type: 'POST', dataType: 'json', data: data })
        .done(function (response) {
          updateCsrf(response);
          if (window.CamsAlpineModal && typeof window.CamsAlpineModal.close === 'function') {
            window.CamsAlpineModal.close();
          } else if ($.fn.modal) {
            $('#cams-edit-appointment-modal').modal('hide');
          }
          table.ajax.reload(null, false);
          swal('success', 'Appointment updated', response.message || 'The appointment was updated successfully.');
        })
        .fail(function (xhr) {
          var response = xhr.responseJSON || {};
          updateCsrf(response);
          swal('error', 'Update failed', response.message || 'The appointment could not be updated.');
        })
        .always(function () { $button.prop('disabled', false); });
    };

    if (window.CamsUI && typeof window.CamsUI.confirm === 'function') {
      window.CamsUI.confirm({
        icon: 'question',
        title: 'Save appointment changes?',
        text: 'Are you sure you want to update this appointment?',
        confirmButtonText: 'Yes, save changes',
        cancelButtonText: 'Cancel'
      }).then(function (result) {
        if (result.isConfirmed || result.value === true) runUpdate();
      });
    } else if (window.confirm('Are you sure you want to update this appointment?')) {
      runUpdate();
    }
  });

  function updateStatus(id, status) {
    var isDone = status === 'Done';
    var title = isDone ? 'Mark appointment as done?' : 'Cancel this appointment?';
    var text = isDone ? 'This will complete the appointment and remove it from the pending queue.' : 'The appointment will be cancelled and the daily slot will become available again.';
    var confirmText = isDone ? 'Yes, mark done' : 'Yes, cancel appointment';
    var icon = isDone ? 'question' : 'warning';

    var run = function () {
      var data = { appointment_id: id, status: status };
      data[csrfName] = csrfHash;
      return $.ajax({ url: statusUrl, type: 'POST', dataType: 'json', data: data })
        .done(function (response) {
          updateCsrf(response);
          table.ajax.reload(null, false);
          swal('success', isDone ? 'Appointment completed' : 'Appointment cancelled', response.message || 'The appointment status was updated.');
        })
        .fail(function (xhr) {
          var response = xhr.responseJSON || {};
          updateCsrf(response);
          swal('error', 'Action failed', response.message || 'The appointment could not be updated.');
        });
    };

    if (window.Swal) {
      Swal.fire({
        icon: icon,
        type: icon,
        title: title,
        text: text,
        showCancelButton: true,
        confirmButtonText: confirmText,
        cancelButtonText: 'Keep appointment',
        reverseButtons: true,
        focusCancel: !isDone
      }).then(function (result) { if (result.isConfirmed || result.value === true) run(); });
    } else if (window.confirm(title + '\n\n' + text)) {
      run();
    }
  }

  $('#cams-appointments-table').on('click', '.cams-cancel-appointment', function () {
    updateStatus($(this).data('id'), 'Cancelled');
  });
  $('#cams-appointments-table').on('click', '.cams-done-appointment', function () {
    updateStatus($(this).data('id'), 'Done');
  });

  var flashSuccess = root.dataset.flashSuccess || '';
  var flashError = root.dataset.flashError || '';
  if (flashSuccess) swal('success', 'Success', flashSuccess);
  if (flashError) swal('error', 'Something needs attention', flashError);
})(jQuery);
