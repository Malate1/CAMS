(function (window, document, $) {
  'use strict';

  if (!$ || !$.fn || !$.fn.datepicker) return;

  var SELECTOR = 'input[type="date"]:not([data-no-datepicker]), input[data-cams-datepicker="true"]';

  function parseIsoDate(value) {
    value = String(value || '').trim();
    var match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(value);
    if (!match) return null;

    var date = new Date(
      parseInt(match[1], 10),
      parseInt(match[2], 10) - 1,
      parseInt(match[3], 10)
    );

    if (
      date.getFullYear() !== parseInt(match[1], 10) ||
      date.getMonth() !== parseInt(match[2], 10) - 1 ||
      date.getDate() !== parseInt(match[3], 10)
    ) {
      return null;
    }

    return date;
  }

  function datepickerOptions(input) {
    var $input = $(input);
    var minDate = parseIsoDate($input.attr('min'));
    var maxDate = parseIsoDate($input.attr('max'));

    return {
      dateFormat: 'yy-mm-dd',
      changeMonth: true,
      changeYear: true,
      showButtonPanel: true,
      showAnim: 'fadeIn',
      duration: 120,
      firstDay: 1,
      minDate: minDate,
      maxDate: maxDate,
      yearRange: 'c-100:c+20',
      beforeShow: function () {
        var currentMin = parseIsoDate($input.attr('min'));
        var currentMax = parseIsoDate($input.attr('max'));

        window.setTimeout(function () {
          $('#ui-datepicker-div').addClass('cams-ui-datepicker');
        }, 0);

        return {
          minDate: currentMin,
          maxDate: currentMax
        };
      },
      onSelect: function () {
        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.dispatchEvent(new Event('change', { bubbles: true }));
      }
    };
  }

  function enhanceInput(input) {
    if (!input || input.nodeType !== 1 || input.tagName !== 'INPUT') return;
    if (input.matches('[data-no-datepicker]')) return;
    if ($(input).hasClass('hasDatepicker')) return;

    var currentValue = input.value || '';

    input.setAttribute('data-cams-original-type', input.getAttribute('type') || 'text');
    input.setAttribute('data-cams-datepicker', 'true');
    input.setAttribute('autocomplete', 'off');

    // Remove the native browser picker so jQuery UI is the only date UI.
    try {
      input.type = 'text';
    } catch (e) {
      input.setAttribute('type', 'text');
    }

    $(input).datepicker(datepickerOptions(input));

    if (currentValue) {
      var parsedCurrent = parseIsoDate(currentValue);
      if (parsedCurrent) {
        $(input).datepicker('setDate', parsedCurrent);
      }
      input.value = currentValue;
    }
  }

  function enhance(root) {
    var scope = root && root.querySelectorAll ? root : document;

    if (scope.nodeType === 1 && scope.matches && scope.matches(SELECTOR)) {
      enhanceInput(scope);
    }

    Array.prototype.forEach.call(scope.querySelectorAll(SELECTOR), enhanceInput);
  }

  function refresh(root) {
    var scope = root && root.querySelectorAll ? root : document;
    var inputs = [];

    if (scope.nodeType === 1 && scope.matches && scope.matches('input[data-cams-datepicker="true"]')) {
      inputs.push(scope);
    }

    Array.prototype.push.apply(
      inputs,
      Array.prototype.slice.call(scope.querySelectorAll('input[data-cams-datepicker="true"]'))
    );

    inputs.forEach(function (input) {
      var $input = $(input);
      if (!$input.hasClass('hasDatepicker')) {
        enhanceInput(input);
        return;
      }

      $input.datepicker('option', {
        minDate: parseIsoDate($input.attr('min')),
        maxDate: parseIsoDate($input.attr('max'))
      });
    });
  }

  $(function () {
    enhance(document);

    var observer = new MutationObserver(function (mutations) {
      mutations.forEach(function (mutation) {
        Array.prototype.forEach.call(mutation.addedNodes || [], function (node) {
          if (node && node.nodeType === 1) enhance(node);
        });

        if (
          mutation.type === 'attributes' &&
          mutation.target &&
          mutation.target.matches &&
          mutation.target.matches('input[data-cams-datepicker="true"]')
        ) {
          refresh(mutation.target);
        }
      });
    });

    if (document.body) {
      observer.observe(document.body, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['min', 'max', 'disabled', 'readonly']
      });
    }

    window.addEventListener('cams:alpine-modal-open', function () {
      var body = document.getElementById('cams-alpine-modal-body');
      if (body) window.setTimeout(function () { enhance(body); }, 0);
    });
  });

  window.CamsDatepicker = {
    enhance: enhance,
    refresh: refresh
  };
})(window, document, window.jQuery);
