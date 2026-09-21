(function (window, document, $) {
  'use strict';

  if (!$ || !$.fn || !$.fn.select2) return;

  var DEFAULT_SELECTOR = 'select:not([data-no-select2]):not(.no-select2)';

  function optionCount(select) {
    return select && select.options ? select.options.length : 0;
  }

  function getDropdownParent($select) {
    var $alpineModal = $select.closest('.cams-alpine-modal-root');
    if ($alpineModal.length) return $alpineModal;

    var $bootstrapModal = $select.closest('.modal');
    if ($bootstrapModal.length) return $bootstrapModal;

    return $(document.body);
  }

  function getPlaceholder($select) {
    var explicit = $select.attr('data-placeholder') || $select.attr('placeholder');
    if (explicit) return explicit;

    var first = $select.find('option').first();
    if (first.length && String(first.val() || '') === '') {
      return $.trim(first.text()) || 'Select an option';
    }

    return '';
  }

  function buildOptions(select) {
    var $select = $(select);
    var isMultiple = !!select.multiple;
    var forceSearch = String($select.attr('data-select2-search') || '').toLowerCase();

    // CAMS uses Select2 as the standard searchable dropdown. Search stays
    // available by default even for short lists; opt out only when explicitly
    // requested with data-select2-search="never".
    var minimumResultsForSearch = forceSearch === 'never' ? Infinity : 0;

    var options = {
      width: '100%',
      dropdownAutoWidth: false,
      dropdownParent: getDropdownParent($select),
      minimumResultsForSearch: minimumResultsForSearch
    };

    var placeholder = getPlaceholder($select);
    if (placeholder) options.placeholder = placeholder;

    if (!isMultiple && placeholder) {
      options.allowClear = !$select.prop('required');
    }

    if (isMultiple) {
      options.closeOnSelect = false;
    }

    return options;
  }

  function syncDisabled(select) {
    var $select = $(select);
    if (!$select.hasClass('select2-hidden-accessible')) return;
    $select.trigger('change.select2');
  }

  function rebuildSelect(select) {
    if (!select || select.nodeType !== 1 || select.tagName !== 'SELECT') return;
    if (select.matches('[data-no-select2], .no-select2')) return;

    if (select.__camsSelect2RefreshTimer) {
      window.clearTimeout(select.__camsSelect2RefreshTimer);
      select.__camsSelect2RefreshTimer = null;
    }

    var $select = $(select);
    var currentValue = $select.val();

    try {
      if ($select.hasClass('select2-hidden-accessible')) {
        $select.select2('destroy');
      }

      $select.select2(buildOptions(select));

      if (currentValue !== null && typeof currentValue !== 'undefined') {
        $select.val(currentValue).trigger('change.select2');
      }

      $select.attr('data-cams-select2-ready', 'true');
    } catch (error) {
      if (window.console && console.warn) {
        console.warn('CAMS Select2 refresh skipped:', error);
      }
    }
  }

  function scheduleRebuild(select) {
    if (!select || select.tagName !== 'SELECT') return;
    if (select.__camsSelect2RefreshTimer) {
      window.clearTimeout(select.__camsSelect2RefreshTimer);
    }

    select.__camsSelect2RefreshTimer = window.setTimeout(function () {
      select.__camsSelect2RefreshTimer = null;
      rebuildSelect(select);
    }, 0);
  }

  function initSelect(select) {
    if (!select || select.nodeType !== 1 || select.tagName !== 'SELECT') return;
    if (select.matches('[data-no-select2], .no-select2')) return;

    var $select = $(select);

    if ($select.hasClass('select2-hidden-accessible')) {
      syncDisabled(select);
      return;
    }

    try {
      $select.select2(buildOptions(select));
      $select.attr('data-cams-select2-ready', 'true');
    } catch (error) {
      if (window.console && console.warn) {
        console.warn('CAMS Select2 initialization skipped:', error);
      }
    }
  }

  function enhance(root) {
    var scope = root && root.querySelectorAll ? root : document;

    if (scope.nodeType === 1 && scope.tagName === 'SELECT') {
      initSelect(scope);
    }

    Array.prototype.forEach.call(scope.querySelectorAll(DEFAULT_SELECTOR), initSelect);
  }

  function refresh(root) {
    if (root && root.nodeType === 1 && root.tagName === 'SELECT') {
      rebuildSelect(root);
      return;
    }

    var scope = root && root.querySelectorAll ? root : document;
    Array.prototype.forEach.call(scope.querySelectorAll(DEFAULT_SELECTOR), function (select) {
      rebuildSelect(select);
    });
  }

  $(function () {
    enhance(document);

    $(document)
      .on('shown.bs.modal', '.modal', function () {
        enhance(this);
      })
      .on('change', DEFAULT_SELECTOR, function () {
        syncDisabled(this);
      });

    window.addEventListener('cams:alpine-modal-open', function () {
      var body = document.getElementById('cams-alpine-modal-body');
      if (body) window.setTimeout(function () { enhance(body); }, 0);
    });

    window.addEventListener('cams:theme-changed', function () {
      refresh(document);
    });

    var observer = new MutationObserver(function (mutations) {
      mutations.forEach(function (mutation) {
        Array.prototype.forEach.call(mutation.addedNodes || [], function (node) {
          if (node && node.nodeType === 1) enhance(node);
        });

        if (mutation.target && mutation.target.tagName === 'SELECT') {
          if (mutation.type === 'attributes' || mutation.type === 'childList') {
            scheduleRebuild(mutation.target);
          }
        } else if (mutation.type === 'childList' && mutation.target && mutation.target.closest) {
          var ownerSelect = mutation.target.closest('select');
          if (ownerSelect) scheduleRebuild(ownerSelect);
        }
      });
    });

    if (document.body) {
      observer.observe(document.body, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['disabled', 'multiple']
      });
    }
  });

  window.CamsSelect2 = {
    enhance: enhance,
    refresh: refresh,
    rebuild: rebuildSelect
  };
})(window, document, window.jQuery);
