(function ($) {
  'use strict';

  if (!$) return;

  function updateCsrf(response) {
    if (!response || !response.csrf || !response.csrf.name) return;
    $('input[name="' + response.csrf.name.replace(/([:\.\[\],=@])/g, '\\$1') + '"]').val(response.csrf.hash);
  }

  function applyProfileResponse(response) {
    if (!response || !response.profile) return;

    var profile = response.profile;
    if (profile.display_name) {
      $('[data-cams-profile-name]').text(profile.display_name);
      $('[data-cams-account-name]').text(profile.display_name);
    }

    if (profile.image_url) {
      $('[data-cams-profile-avatar], [data-cams-account-avatar]').attr('src', profile.image_url);
    }
  }

  function alertBox(icon, title, text) {
    if (window.CamsUI && typeof window.CamsUI.alert === 'function') {
      return window.CamsUI.alert(icon, title, text || '');
    }
    window.alert((title ? title + '\n' : '') + (text || ''));
    return $.Deferred().resolve().promise();
  }

  function confirmBox(title, text, confirmText) {
    if (window.CamsUI && typeof window.CamsUI.confirm === 'function') {
      return window.CamsUI.confirm({
        icon: 'question',
        title: title || 'Are you sure?',
        text: text || 'Please confirm before proceeding.',
        confirmButtonText: confirmText || 'Yes, proceed',
        cancelButtonText: 'Cancel'
      });
    }
    return Promise.resolve({ isConfirmed: window.confirm(text || title || 'Are you sure?') });
  }

  function submitAjaxForm($form) {
    if ($form.data('camsSubmitting')) return;

    var title = String($form.data('confirm-title') || 'Are you sure?');
    var text = String($form.data('confirm-text') || 'Please confirm that you want to save these changes.');
    var confirmText = String($form.data('confirm-button') || 'Yes, proceed');
    var successTitle = String($form.data('success-title') || 'Success');
    var $submit = $form.find('[type="submit"]').first();
    var originalSubmitHtml = $submit.length ? $submit.html() : '';
    var temporaryResetCompleted = false;

    confirmBox(title, text, confirmText).then(function (result) {
      if (!(result.isConfirmed || result.value === true)) return;

      var method = String($form.attr('method') || 'POST').toUpperCase();
      var action = $form.attr('action') || window.location.href;
      var enctype = String($form.attr('enctype') || '').toLowerCase();
      var hasFile = enctype.indexOf('multipart/form-data') !== -1 || $form.find('input[type="file"]').length > 0;
      var ajaxOptions = {
        url: action,
        type: method,
        dataType: 'json'
      };

      if (hasFile) {
        ajaxOptions.data = new FormData($form[0]);
        ajaxOptions.processData = false;
        ajaxOptions.contentType = false;
      } else {
        ajaxOptions.data = $form.serialize();
      }

      $form.data('camsSubmitting', true);
      $submit
        .prop('disabled', true)
        .addClass('disabled')
        .html('<i class="fa fa-circle-o-notch fa-spin"></i> Processing...');

      $.ajax(ajaxOptions)
        .done(function (response) {
          updateCsrf(response);
          applyProfileResponse(response);
          if (response && response.success === false) {
            alertBox('error', 'Action failed', response.message || 'The transaction could not be completed.');
            return;
          }

          if (response && response.password_change_completed && window.Alpine && Alpine.store) {
            var completedPasswordModal = Alpine.store('camsModal');
            if (completedPasswordModal) completedPasswordModal.locked = false;
            $('#cams-required-password-modal').remove();
          }

          if (response && response.temporary_password) {
            temporaryResetCompleted = true;
            $form.find('[data-cams-temp-password]').val(response.temporary_password);
            $form.find('[data-cams-reset-result]').removeClass('hidden');
            $form.find('[data-cams-regenerate-temp]').prop('disabled', true).addClass('disabled');
            $submit.prop('disabled', true).addClass('disabled').html('<i class="fa fa-check"></i> Password Reset');
          } else if ($form.data('keep-open-on-success') !== true) {
            var $modal = $form.closest('.modal');
            if ($modal.length && $.fn.modal) $modal.modal('hide');
            if ($form.closest('#cams-alpine-modal-body').length && window.Alpine && Alpine.store) {
              var modalStore = Alpine.store('camsModal');
              if (modalStore && typeof modalStore.close === 'function') modalStore.close();
            }
          }

          var table = $('#cams-management-table');
          if (table.length && $.fn.DataTable && $.fn.DataTable.isDataTable(table[0])) {
            table.DataTable().ajax.reload(null, false);
          }

          alertBox('success', successTitle, (response && response.message) || 'The transaction was completed successfully.')
            .then(function () {
              if ($form.data('reset-on-success') !== false && $form.attr('id') !== 'cams-edit-appointment-form') {
                try { $form[0].reset(); } catch (e) {}
              }
              if (response && response.redirect && $form.data('follow-redirect') === true) {
                window.location.href = response.redirect;
              }
            });
        })
        .fail(function (xhr) {
          var response = xhr.responseJSON || {};
          updateCsrf(response);
          alertBox('error', 'Action failed', response.message || 'The transaction could not be completed. Please try again.');
        })
        .always(function () {
          $form.data('camsSubmitting', false);
          if (!temporaryResetCompleted) {
            $submit.prop('disabled', false).removeClass('disabled');
            if ($submit.length) $submit.html(originalSubmitHtml || 'Submit');
          }
        });
    }).catch(function (error) {
      $form.data('camsSubmitting', false);
      $submit.prop('disabled', false).removeClass('disabled');
      if ($submit.length) $submit.html(originalSubmitHtml || 'Submit');
      alertBox('error', 'Unable to continue', 'The confirmation dialog could not be completed. Please try again.');
      if (window.console && console.error) console.error('CAMS confirmation error:', error);
    });
  }

  $(document).on('submit', 'form[data-cams-ajax="true"]', function (event) {
    event.preventDefault();
    event.stopImmediatePropagation();
    submitAjaxForm($(this));
  });

  window.CamsAjaxTransactions = {
    submit: submitAjaxForm
  };
})(window.jQuery);
