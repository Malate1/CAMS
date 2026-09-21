(function ($) {
  'use strict';

  function swalResult(result) {
    return !!(result && (result.isConfirmed || result.value === true));
  }

  function getTheme() {
    return document.documentElement.getAttribute('data-cams-theme') === 'dark' ? 'dark' : 'light';
  }

  function updateThemeToggle() {
    var theme = getTheme();
    var nextTheme = theme === 'dark' ? 'light' : 'dark';
    $('[data-cams-theme-toggle]')
      .attr('aria-label', 'Switch to ' + nextTheme + ' mode')
      .attr('title', 'Switch to ' + nextTheme + ' mode')
      .attr('aria-pressed', theme === 'dark' ? 'true' : 'false');

    $('.cams-theme-toggle-label').text(theme === 'dark' ? 'Light' : 'Dark');
  }

  function applyTheme(theme, persist) {
    theme = theme === 'dark' ? 'dark' : 'light';
    document.documentElement.setAttribute('data-cams-theme', theme);
    document.documentElement.classList.toggle('dark', theme === 'dark');
    document.documentElement.style.colorScheme = theme;

    if (persist !== false) {
      try {
        localStorage.setItem('cams-theme', theme);
      } catch (e) {}
    }

    updateThemeToggle();
    $(document).trigger('cams:theme-changed', [theme]);
  }

  $(function () {
    updateThemeToggle();

    var current = window.location.href.replace(/\/$/, '');
    $('.sidebar-menu a[href]').each(function () {
      var href = (this.href || '').replace(/\/$/, '');
      if (!href || href === '#') return;
      if (href === current) {
        $(this).parent('li').addClass('active');
        $(this).parents('li.treeview').addClass('active menu-open');
      }
    });
  });

  $(document).on('click', '[data-cams-theme-toggle]', function () {
    applyTheme(getTheme() === 'dark' ? 'light' : 'dark', true);
  });

  $(document).on('click', 'a[href*="logout"], a[href*="log-out"]', function (event) {
    var href = $(this).attr('href');
    if (!href || href === '#') return;

    event.preventDefault();

    if (window.Swal) {
      Swal.fire({
        type: 'question',
        icon: 'question',
        title: 'Sign out of CAMS?',
        text: 'You will need to sign in again to continue.',
        showCancelButton: true,
        confirmButtonText: 'Sign out',
        cancelButtonText: 'Stay signed in',
        reverseButtons: true
      }).then(function (result) {
        if (swalResult(result)) window.location.href = href;
      });
      return;
    }

    if (window.confirm('Are you sure you want to sign out?')) {
      window.location.href = href;
    }
  });

  function notify(type, title, message) {
    type = ['success', 'error', 'warning', 'info'].indexOf(type) !== -1 ? type : 'info';

    if (window.toastr && typeof window.toastr[type] === 'function') {
      window.toastr.options = $.extend({
        closeButton: true,
        progressBar: true,
        newestOnTop: true,
        positionClass: 'toast-top-right',
        timeOut: type === 'error' ? 6500 : 4200,
        extendedTimeOut: 1200,
        preventDuplicates: true
      }, window.toastr.options || {});

      window.toastr[type](message || '', title || '');
      return Promise.resolve({ shown: true });
    }

    window.alert((title ? title + '\n' : '') + (message || ''));
    return Promise.resolve({ shown: true });
  }

  window.CamsUI = {
    setTheme: function (theme) {
      applyTheme(theme, true);
    },

    getTheme: getTheme,

    alert: function (icon, title, text) {
      return notify(icon, title, text || '');
    },

    notify: notify,

    confirm: function (options) {
      options = options || {};

      if (window.Swal) {
        return Swal.fire({
          type: options.icon || 'question',
          icon: options.icon || 'question',
          title: options.title || 'Are you sure?',
          text: options.text || '',
          showCancelButton: true,
          confirmButtonText: options.confirmButtonText || 'Confirm',
          cancelButtonText: options.cancelButtonText || 'Cancel',
          reverseButtons: true
        });
      }

      return Promise.resolve({
        isConfirmed: window.confirm(options.text || options.title || 'Are you sure?')
      });
    }
  };

  if (window.Swal && typeof window.Swal.mixin === 'function' && !window.Swal.__camsFeedbackBridge) {
    var originalMixin = window.Swal.mixin.bind(window.Swal);
    window.Swal.mixin = function (options) {
      options = options || {};
      if (options.showConfirmButton === false) {
        return {
          fire: function (payload) {
            if (window.__CAMS_FLASH_TOAST_ACTIVE) {
              return Promise.resolve({ shown: false, duplicate: true });
            }
            payload = payload || {};
            var type = payload.icon || payload.type || 'info';
            var title = payload.title || '';
            var text = payload.text || '';
            return notify(type, title, text);
          }
        };
      }
      return originalMixin(options);
    };
    window.Swal.__camsFeedbackBridge = true;
  }
})(window.jQuery);
