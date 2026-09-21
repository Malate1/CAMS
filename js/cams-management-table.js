(function ($) {
  'use strict';

  if (!$) return;

  function escapeHtml(value) {
    return String(value == null ? '' : value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function joinUrl(base, value) {
    base = String(base || '');
    if (!base) return '#';
    return base.replace(/\/$/, '') + '/' + encodeURIComponent(String(value));
  }

  function editButton(base, id, label) {
    if (!base) return '';
    return '<a class="btn btn-warning btn-sm cams-row-edit" href="' + escapeHtml(joinUrl(base, id)) + '">' +
      '<i class="fa fa-edit"></i> ' + escapeHtml(label || 'Edit') + '</a>';
  }

  function configFor($table) {
    var kind = $table.data('kind');
    var editBase = $table.data('edit-base') || '';
    var imageBase = $table.data('image-base') || '';

    if (kind === 'user') {
      return {
        noun: 'users',
        search: 'Search users…',
        order: [[0, 'desc']],
        columns: [
          { data: 'id', className: 'text-center' },
          { data: 'fname' },
          { data: 'mname' },
          { data: 'lname' },
          { data: 'contact' },
          { data: 'address' },
          { data: 'email' },
          {
            data: 'image', orderable: false, searchable: false, className: 'text-center',
            render: function (value) {
              var src = String(imageBase).replace(/\/$/, '') + '/' + encodeURIComponent(String(value || ''));
              return '<img class="cams-user-avatar" src="' + escapeHtml(src) + '" alt="Profile image">';
            }
          },
          {
            data: null, orderable: false, searchable: false, className: 'text-right',
            render: function (data, type, row) { return editButton(editBase, row.id, 'Update Password'); }
          }
        ]
      };
    }

    if (kind === 'clinic-admin') {
      return {
        noun: 'clinics', search: 'Search clinics…', order: [[0, 'desc']],
        columns: [
          { data: 'clinic_id', className: 'text-center' },
          { data: 'name' },
          { data: 'physician_name', render: function (v) { return 'Dr. ' + escapeHtml(v); } },
          { data: 'specializations', orderable: false, render: function (v) { return escapeHtml(v || '—'); } },
          { data: 'secretary_name', render: function (v) { return escapeHtml(v || '—'); } },
          { data: 'contact' },
          { data: 'location' },
          { data: null, orderable: false, searchable: false, className: 'text-right', render: function (d, t, row) { return editButton(editBase, row.assignment_id || row.clinic_id, 'Edit'); } }
        ]
      };
    }

    if (kind === 'clinic') {
      return {
        noun: 'clinics', search: 'Search clinics…', order: [[0, 'desc']],
        columns: [
          { data: 'clinic_id', className: 'text-center' },
          { data: 'name' },
          { data: 'specializations', orderable: false, render: function (v) { return escapeHtml(v || '—'); } },
          { data: 'contact' },
          { data: 'location' },
          { data: null, orderable: false, searchable: false, className: 'text-right', render: function (d, t, row) { return editButton(editBase, row.assignment_id || row.clinic_id, 'Edit'); } }
        ]
      };
    }

    if (kind === 'schedule') {
      return {
        noun: 'schedules', search: 'Search schedules…', order: [[0, 'desc']],
        columns: [
          { data: 'schedule_id', className: 'text-center' },
          { data: 'clinic_name' },
          { data: 'day' },
          { data: 'time_in', render: function (v, t, row) { return escapeHtml(row.time_in_label || v); } },
          { data: 'time_out', render: function (v, t, row) { return escapeHtml(row.time_out_label || v); } },
          { data: null, orderable: false, searchable: false, className: 'text-right', render: function (d, t, row) { return editButton(editBase, row.link_id || row.schedule_id, 'Edit'); } }
        ]
      };
    }

    if (kind === 'limit') {
      return {
        noun: 'limits', search: 'Search appointment limits…', order: [[1, 'desc']],
        columns: [
          { data: 'id', className: 'text-center' },
          { data: 'dateLimit', render: function (v, t, row) { return escapeHtml(row.date_label || v); } },
          { data: 'queueLimit', className: 'text-center', render: function (v) { return '<span class="cams-queue">' + escapeHtml(v) + '</span>'; } },
          { data: null, orderable: false, searchable: false, className: 'text-right', render: function (d, t, row) { return editButton(editBase, row.id, 'Edit'); } }
        ]
      };
    }

    if (kind === 'logs') {
      return {
        noun: 'logs', search: 'Search activity logs…', order: [[0, 'desc']],
        columns: [
          { data: 'id', className: 'text-center' },
          { data: 'date', render: function (v, t, row) { return escapeHtml(row.date_label || v); } },
          { data: 'usertype', className: 'text-center', render: function (v) { return '<span class="cams-status-pill is-info">' + escapeHtml(v) + '</span>'; } },
          { data: 'action' }
        ]
      };
    }

    return null;
  }

  function showFlash($table) {
    var success = String($table.data('flash-success') || '');
    var error = String($table.data('flash-error') || '');
    if (!success && !error) return;

    $('.alert-success, .alert-danger, .alert-error').hide();
    if (window.CamsUI && typeof window.CamsUI.alert === 'function') {
      if (error) window.CamsUI.alert('error', 'Action failed', error);
      else window.CamsUI.alert('success', 'Success', success);
    }
  }

  function initialize($table) {
    if (!$table.length || !$.fn.DataTable || $.fn.DataTable.isDataTable($table[0])) return;

    var source = String($table.data('source') || '');
    var config = configFor($table);
    if (!source || !config) return;

    var hasButtons = !!($.fn.dataTable && $.fn.dataTable.Buttons);
    var options = {
      processing: true,
      serverSide: true,
      responsive: true,
      autoWidth: false,
      searchDelay: 350,
      paging: true,
      pagingType: 'full_numbers',
      pageLength: 10,
      lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
      order: config.order,
      ajax: {
        url: source,
        type: 'GET',
        dataType: 'json',
        error: function (xhr) {
          var message = 'The records could not be loaded.';
          if (xhr.responseJSON && xhr.responseJSON.message) message = xhr.responseJSON.message;
          if (window.CamsUI) window.CamsUI.alert('error', 'Unable to load data', message);
        }
      },
      columns: config.columns,
      dom: hasButtons ? '<"cams-dt-toolbar"<"cams-dt-length"l><"cams-dt-actions"Bf>>rt<"cams-dt-footer"ip>' : '<"cams-dt-toolbar"<"cams-dt-length"l><"cams-dt-actions"f>>rt<"cams-dt-footer"ip>',
      language: {
        processing: '<div class="cams-table-loader"><span></span><span></span><span></span><small>Loading records…</small></div>',
        search: '',
        searchPlaceholder: config.search,
        lengthMenu: 'Show _MENU_',
        info: 'Showing _START_–_END_ of _TOTAL_ ' + config.noun,
        infoEmpty: 'No ' + config.noun + ' found',
        emptyTable: '<div class="cams-empty-state"><i class="fa fa-inbox"></i><strong>No records found</strong><span>Records will appear here when available.</span></div>',
        paginate: { first: 'First', previous: 'Previous', next: 'Next', last: 'Last' }
      }
    };

    if (hasButtons) {
      options.buttons = [
        { extend: 'copy', text: '<i class="fa fa-copy"></i><span>Copy</span>', className: 'btn cams-dt-button' },
        { extend: 'csv', text: '<i class="fa fa-file-text-o"></i><span>CSV</span>', className: 'btn cams-dt-button' },
        { extend: 'print', text: '<i class="fa fa-print"></i><span>Print</span>', className: 'btn cams-dt-button' }
      ];
    }

    $table.DataTable(options);
    showFlash($table);
  }

  function modalMeta(title) {
    var value = $.trim(title || 'Manage Record');
    var lower = value.toLowerCase();
    if (lower.indexOf('add') !== -1 || lower.indexOf('register') !== -1) {
      return { eyebrow: 'Create record', text: 'Enter the required information below, then save the new record.' };
    }
    if (lower.indexOf('set limit') !== -1 || lower.indexOf('limit of appointment') !== -1) {
      return { eyebrow: 'Configure availability', text: 'Set the appointment capacity and effective date for this schedule.' };
    }
    if (lower.indexOf('choose') !== -1 || lower.indexOf('date range') !== -1) {
      return { eyebrow: 'Filter records', text: 'Choose the options below to update the records shown on this page.' };
    }
    return { eyebrow: 'Update record', text: 'Review the information below and save your changes when finished.' };
  }

  function styleModalHeader($modal, title) {
    var meta = modalMeta(title);
    var $header = $modal.find('.modal-header').first();
    var $title = $header.find('.modal-title').first();
    if (!$header.length || !$title.length) return;

    $modal.addClass('cams-edit-modal cams-uniform-modal');
    $modal.find('.modal-dialog').first().removeClass('modal-lg modal-sm');
    $title.text($.trim(title || $title.text() || 'Manage Record'));

    $header.find('.cams-modal-eyebrow, .cams-modal-description').remove();
    $('<span class="cams-modal-eyebrow"></span>').text(meta.eyebrow).insertBefore($title);
    $('<p class="cams-modal-description"></p>').text(meta.text).insertAfter($title);
  }

  function isAjaxTransactionAction(action) {
    return /(ClinicRegister|ClinicUpdate|ScheduleRegister|ScheduleUpdate|LimitRegister|LimitUpdate|PatientUpdate|PhysicianUpdate|SecretaryUpdate|AppointmentRegister)(?:$|\?|\/)/i.test(String(action || ''));
  }

  function applySubmitResetFooter($modal, $form) {
    if (!$form || !$form.length) return;

    if (isAjaxTransactionAction($form.attr('action'))) {
      $form.attr({
        'data-cams-ajax': 'true',
        'data-confirm-title': 'Are you sure?',
        'data-confirm-text': 'Please confirm before saving this transaction.',
        'data-confirm-button': 'Yes, proceed'
      });
    }

    $form.find('button[type="submit"], input[type="submit"], button[type="reset"], input[type="reset"]').remove();
    $modal.children('.modal-dialog').find('> .modal-content > .modal-footer').remove();
    $form.find('.modal-footer.cams-standard-form-footer').remove();

    var $footer = $('<div class="modal-footer cams-standard-form-footer"></div>');
    $footer.append('<button style="color: white" type="submit" class="btn btn-primary" value="Submit"><i class="fa fa-save"></i> Submit</button>');
    $footer.append('<button style="color: white" type="reset" class="btn btn-danger" value="Reset"><i class="fa fa-close"></i> Reset</button>');
    $form.append($footer);
  }

  function normalizeExistingModals() {
    $('.modal').not('#cams-edit-appointment-modal').each(function () {
      var $modal = $(this);
      var title = $.trim($modal.find('.modal-title').first().text()) || 'Manage Record';
      styleModalHeader($modal, title);

      var $form = $modal.find('.modal-body form').first();
      if ($form.length) applySubmitResetFooter($modal, $form);
    });
  }

  function alpineModalStore() {
    return window.Alpine && Alpine.store ? Alpine.store('camsModal') : null;
  }

  function closeAlpineModal() {
    var store = alpineModalStore();
    if (store && typeof store.close === 'function') store.close();
  }

  function resetClonedSelect2State($scope) {
    if (!$scope || !$scope.length) return;

    $scope.find('.select2-container').remove();
    $scope.find('select').each(function () {
      var $select = $(this);
      $select
        .removeClass('select2-hidden-accessible')
        .removeAttr('data-select2-id')
        .removeAttr('aria-hidden')
        .removeAttr('tabindex');

      $select.find('option').removeAttr('data-select2-id');
    });
  }

  function openRemoteForm(url, fallbackTitle, forceLocked) {
    var $body = $('#cams-alpine-modal-body');
    var store = alpineModalStore();

    if (!$body.length || !store) {
      window.location.href = url;
      return;
    }

    var initialMeta = modalMeta(fallbackTitle || 'Manage Record');
    store.show({
      title: fallbackTitle || 'Manage Record',
      eyebrow: initialMeta.eyebrow,
      description: initialMeta.text,
      locked: forceLocked === true
    });

    $body.html(
      '<div class="flex min-h-[180px] items-center justify-center text-slate-500 dark:text-slate-400">' +
        '<div class="text-center"><i class="fa fa-circle-o-notch fa-spin text-2xl"></i><p class="mt-3 text-sm">Loading form…</p></div>' +
      '</div>'
    );

    $.ajax({ url: url, type: 'GET', dataType: 'html' })
      .done(function (html) {
        var parsed = $.parseHTML(html, document, true);
        var $page = $('<div>').append(parsed);
        var $customContent = $page.find('[data-cams-modal-content]').first();

        if ($customContent.length) {
          var $customClone = $customContent.clone(true, true);
          resetClonedSelect2State($customClone);
          var customTitle = String($customContent.data('modal-title') || fallbackTitle || 'Manage Record');
          var customEyebrow = String($customContent.data('modal-eyebrow') || 'Account tools');
          var customDescription = String($customContent.data('modal-description') || '');
          var customWidth = String($customContent.data('modal-width') || '48rem');
          var customLocked = forceLocked === true || String($customContent.attr('data-modal-locked') || '').toLowerCase() === 'true';

          store.show({
            title: customTitle,
            eyebrow: customEyebrow,
            description: customDescription,
            maxWidth: customWidth,
            locked: customLocked
          });

          $body.empty().append($customClone);

          if (window.CamsTailwindUI && typeof window.CamsTailwindUI.enhance === 'function') {
            window.CamsTailwindUI.enhance($body[0]);
          }
          if (window.CamsSelect2 && typeof window.CamsSelect2.enhance === 'function') {
            window.CamsSelect2.enhance($body[0]);
          }
          return;
        }

        var title = $.trim($page.find('.box-title').first().text()) || $.trim($page.find('h1').first().text()) || fallbackTitle || 'Manage Record';
        var $form = $page.find('.box form').first();
        if (!$form.length) $form = $page.find('form').first();

        if (!$form.length) {
          closeAlpineModal();
          window.location.href = url;
          return;
        }

        var $clone = $form.clone(true, true);
        resetClonedSelect2State($clone);
        $clone.addClass('space-y-4');
        $clone.find('.box-body').removeClass('box-body');
        $clone.find('.box-footer').remove();
        $clone.find('.row').removeClass('row').addClass('cams-modal-form-row');
        $clone.find('[class*="col-md-"], [class*="col-lg-"]').removeClass(function (index, className) {
          return (className.match(/(^|\s)col-(md|lg)-\S+/g) || []).join(' ');
        });

        var meta = modalMeta(title);
        store.show({ title: title, eyebrow: meta.eyebrow, description: meta.text });
        $body.empty().append($clone);
        applySubmitResetFooter($body, $clone);
        if (window.CamsSelect2 && typeof window.CamsSelect2.enhance === 'function') {
          window.CamsSelect2.enhance($body[0]);
        }
      })
      .fail(function () {
        closeAlpineModal();
        if (window.CamsUI && typeof window.CamsUI.alert === 'function') {
          window.CamsUI.alert('error', 'Unable to open form', 'The form could not be loaded.');
        } else {
          window.location.href = url;
        }
      });
  }

  $(document).on('click', '.cams-row-edit, .cams-modal-form-link', function (event) {
    var href = this.href;
    if (!href || href === '#') return;
    event.preventDefault();
    var forceLocked = String($(this).attr('data-modal-locked') || '').toLowerCase() === 'true';
    openRemoteForm(href, $(this).data('modal-title') || $.trim($(this).text()), forceLocked);
  });

  $(document).on('click', '.cams-password-toggle', function (event) {
    event.preventDefault();
    var targetId = String($(this).data('password-target') || '');
    if (!targetId) return;

    var input = document.getElementById(targetId);
    if (!input) return;

    var showing = input.type === 'text';
    input.type = showing ? 'password' : 'text';
    $(this).attr('aria-label', showing ? 'Show password' : 'Hide password');
    $(this).find('i').toggleClass('fa-eye', showing).toggleClass('fa-eye-slash', !showing);
  });

  function updateStrengthRule($scope, rule, passed) {
    var $rule = $scope.find('[data-cams-rule="' + rule + '"]');
    if (!$rule.length) return;

    $rule
      .toggleClass('text-emerald-700 dark:text-emerald-300', passed)
      .toggleClass('text-slate-500 dark:text-slate-400', !passed);

    $rule.find('i')
      .toggleClass('fa-check-circle', passed)
      .toggleClass('fa-circle-o', !passed);
  }

  function updatePasswordStrength(input) {
    var value = String(input.value || '');
    var $scope = $(input).closest('form').find('[data-cams-password-strength]').first();
    if (!$scope.length) return;

    var hasLength = value.length >= 7;
    var hasLetter = /[A-Za-z]/.test(value);
    var hasNumber = /\d/.test(value);
    var hasSpecial = /[^A-Za-z0-9]/.test(value);
    var categoryCount = [hasLetter, hasNumber, hasSpecial].filter(Boolean).length;

    var label = 'Enter a password';
    var width = '0%';
    var color = '#cbd5e1';
    var labelClass = 'text-slate-400';

    if (value.length) {
      if (value.length <= 6) {
        label = 'Very Weak';
        width = '25%';
        color = '#dc2626';
        labelClass = 'text-rose-600 dark:text-rose-400';
      } else if (categoryCount <= 1) {
        label = 'Weak';
        width = '50%';
        color = '#f59e0b';
        labelClass = 'text-amber-600 dark:text-amber-400';
      } else if (categoryCount === 2) {
        label = 'Good';
        width = '75%';
        color = '#0ea5e9';
        labelClass = 'text-sky-600 dark:text-sky-400';
      } else {
        label = 'Strong';
        width = '100%';
        color = '#16a34a';
        labelClass = 'text-emerald-600 dark:text-emerald-400';
      }
    }

    $scope.find('[data-cams-strength-bar]').css({
      width: width,
      backgroundColor: color
    });

    $scope.find('[data-cams-strength-label]')
      .attr('class', 'text-sm font-extrabold ' + labelClass)
      .text(label);

    updateStrengthRule($scope, 'length', hasLength);
    updateStrengthRule($scope, 'letter', hasLetter);
    updateStrengthRule($scope, 'number', hasNumber);
    updateStrengthRule($scope, 'special', hasSpecial);
  }

  function updatePasswordMatch() {
    var $newPassword = $('#cams-new-password');
    var $confirm = $('#cams-confirm-password');
    var $message = $('[data-cams-password-match]').first();
    if (!$newPassword.length || !$confirm.length || !$message.length) return;

    var confirmation = String($confirm.val() || '');
    if (!confirmation) {
      $message.addClass('hidden').text('');
      return;
    }

    var matches = String($newPassword.val() || '') === confirmation;
    $message
      .removeClass('hidden text-rose-600 dark:text-rose-400 text-emerald-600 dark:text-emerald-400')
      .addClass(matches ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400')
      .html('<i class="fa ' + (matches ? 'fa-check-circle' : 'fa-times-circle') + '"></i> ' + (matches ? 'Passwords match' : 'Passwords do not match'));
  }

  $(document).on('input', '[data-cams-strength-input]', function () {
    updatePasswordStrength(this);
    updatePasswordMatch();
  });

  $(document).on('input', '#cams-confirm-password', updatePasswordMatch);

  $(document).on('change', '[data-cams-photo-input]', function () {
    var filename = this.files && this.files.length ? this.files[0].name : 'Choose a profile photo';
    $(this).closest('label').find('[data-cams-photo-label]').text(filename);
  });

  $(document).on('click', '[data-cams-copy-temp]', function () {
    var value = String($(this).closest('form').find('[data-cams-temp-password]').val() || '');
    if (!value) return;

    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(value).then(function () {
        if (window.CamsUI && typeof window.CamsUI.notify === 'function') {
          window.CamsUI.notify('success', 'Copied', 'Temporary password copied to clipboard.');
        }
      });
      return;
    }

    var $input = $(this).closest('form').find('[data-cams-temp-password]').first();
    $input.trigger('focus').trigger('select');
    document.execCommand('copy');
  });

  $(document).on('click', '[data-cams-regenerate-temp]', function () {
    var $button = $(this);
    var $form = $button.closest('form');
    var url = String($button.data('url') || '');
    if (!url || !$form.length) return;

    var $csrf = $form.find('input[type="hidden"]').filter(function () {
      return this.name && this.name !== 'reset_token' && this.name.indexOf('old_') !== 0;
    }).first();

    var payload = {};
    if ($csrf.length) payload[$csrf.attr('name')] = $csrf.val();

    $button.prop('disabled', true).addClass('disabled').html('<i class="fa fa-circle-o-notch fa-spin"></i> Generating...');

    $.ajax({
      url: url,
      type: 'POST',
      dataType: 'json',
      data: payload
    }).done(function (response) {
      if (!response || response.success === false) {
        if (window.CamsUI) window.CamsUI.notify('error', 'Unable to generate password', (response && response.message) || 'Please try again.');
        return;
      }

      $form.find('[data-cams-temp-password]').val(response.temporary_password || '');
      $form.find('[data-cams-reset-token]').val(response.reset_token || '');
      $form.find('[data-cams-reset-result]').addClass('hidden');

      if (response.csrf && response.csrf.name) {
        $('input[name="' + response.csrf.name.replace(/([:\.\[\],=@])/g, '\\$1') + '"]').val(response.csrf.hash);
      }

      if (window.CamsUI) window.CamsUI.notify('success', 'Temporary password generated', 'A new system-generated password is ready.');
    }).fail(function (xhr) {
      var response = xhr.responseJSON || {};
      if (window.CamsUI) window.CamsUI.notify('error', 'Unable to generate password', response.message || 'Please try again.');
    }).always(function () {
      $button.prop('disabled', false).removeClass('disabled').html('<i class="fa fa-refresh"></i> Generate Another Password');
    });
  });

  function populateClinicSpecializations(physicianId) {
    var $special = $('#clinic-special-id');
    if (!$special.length) return;

    var map = window.CAMS_PHYSICIAN_SPECIALIZATIONS || {};
    var normalizedPhysicianId = String(parseInt(physicianId, 10) || '');
    var items = map[normalizedPhysicianId] || map[String(physicianId)] || map[physicianId] || [];
    $special.empty();

    if (!physicianId) {
      $special.append('<option value="">Select physician first</option>').prop('disabled', true);
      if (window.CamsSelect2) window.CamsSelect2.refresh($special[0]);
      return;
    }

    if (!items.length) {
      $special.append('<option value="">No specialization configured</option>').prop('disabled', true);
      if (window.CamsSelect2) window.CamsSelect2.refresh($special[0]);
      return;
    }

    items.forEach(function (item) {
      $special.append($('<option>', { value: item.id, text: item.name }));
    });
    $special.prop('disabled', false);
    if (window.CamsSelect2) window.CamsSelect2.refresh($special[0]);
  }

  $(document).on('change', '#clinic-physician-id', function () {
    populateClinicSpecializations($(this).val());
  });

  function syncClinicAssignmentMode() {
    var $mode = $('#clinic-assignment-mode');
    if (!$mode.length) return;
    var existing = $mode.val() === 'existing';
    $('#clinic-existing-field').toggle(existing);
    var $existingClinic = $('#clinic-existing-id');
    $existingClinic.prop('disabled', !existing).prop('required', existing);
    if (window.CamsSelect2 && $existingClinic.length) window.CamsSelect2.refresh($existingClinic[0]);

    $('#clinic-new-fields').toggle(!existing).find('input, select, textarea').each(function () {
      var $field = $(this);
      if (!$field.data('cams-original-required')) {
        $field.data('cams-original-required', $field.prop('required') ? '1' : '0');
      }
      $field.prop('disabled', existing);
      $field.prop('required', !existing && $field.data('cams-original-required') === '1');
      if (this.tagName === 'SELECT' && window.CamsSelect2) {
        window.CamsSelect2.refresh(this);
      }
    });
  }

  $(document).on('change', '#clinic-assignment-mode', syncClinicAssignmentMode);

  $(document).on('reset', '#myModal form', function () {
    window.setTimeout(function () {
      populateClinicSpecializations('');
      syncClinicAssignmentMode();
    }, 0);
  });

  $(function () {
    normalizeExistingModals();
    populateClinicSpecializations($('#clinic-physician-id').val());
    syncClinicAssignmentMode();
    $('table[data-cams-server="true"]').each(function () {
      initialize($(this));
    });
  });
})(window.jQuery);
