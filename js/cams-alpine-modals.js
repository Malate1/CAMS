(function () {
  'use strict';

  var active = null;

  function store() {
    return window.Alpine && Alpine.store ? Alpine.store('camsModal') : null;
  }

  function targetFrom(trigger) {
    var selector = trigger.getAttribute('data-target') || trigger.getAttribute('href');
    if (!selector || selector.charAt(0) !== '#') return null;
    return document.querySelector(selector);
  }

  function metaFrom(modal) {
    var titleNode = modal.querySelector('.modal-title');
    var eyebrowNode = modal.querySelector('.cams-modal-eyebrow');
    var descriptionNode = modal.querySelector('.cams-modal-description') || modal.querySelector('.modal-header p');
    return {
      title: titleNode ? titleNode.textContent.trim() : 'Manage record',
      eyebrow: eyebrowNode ? eyebrowNode.textContent.trim() : 'Manage',
      description: descriptionNode ? descriptionNode.textContent.trim() : ''
    };
  }

  function restore() {
    if (!active) return;

    var content = active.content;
    var placeholder = active.placeholder;
    if (content) {
      content.classList.remove('cams-alpine-embedded');
      if (placeholder && placeholder.parentNode) {
        placeholder.parentNode.insertBefore(content, placeholder);
        placeholder.parentNode.removeChild(placeholder);
      }

      // Select2 binds its dropdown parent at initialization time. Rebuild any
      // controls after moving the content back so they point at the restored
      // Bootstrap modal instead of the shared Alpine modal.
      if (window.CamsSelect2 && typeof window.CamsSelect2.refresh === 'function') {
        window.CamsSelect2.refresh(content);
      }
    }

    active = null;
  }

  function openLegacyModal(modal) {
    var modalStore = store();
    var host = document.getElementById('cams-alpine-modal-body');
    if (!modalStore || !host || !modal) return false;

    restore();

    var content = modal.querySelector('.modal-content');
    if (!content) return false;

    // Read metadata before moving .modal-content out of the Bootstrap modal.
    // Otherwise modal.querySelector() can no longer see its own header/title.
    var meta = metaFrom(modal);

    var placeholder = document.createComment('cams-modal-content');
    content.parentNode.insertBefore(placeholder, content);
    content.classList.add('cams-alpine-embedded');

    active = { modal: modal, content: content, placeholder: placeholder };
    host.innerHTML = '';
    host.setAttribute('data-cams-preserve-on-close', 'true');
    host.appendChild(content);

    modalStore.show(meta);

    if (window.CamsTailwindUI && typeof window.CamsTailwindUI.enhance === 'function') {
      window.CamsTailwindUI.enhance(host);
    }

    // Existing Select2 instances were initialized while the fields belonged to
    // the hidden Bootstrap modal. Rebuild them now that the form lives inside
    // the shared Alpine modal so dropdownParent resolves to the visible modal.
    if (window.CamsSelect2 && typeof window.CamsSelect2.refresh === 'function') {
      window.CamsSelect2.refresh(host);
    }

    return true;
  }

  document.addEventListener('click', function (event) {
    var trigger = event.target.closest('[data-toggle="modal"]');
    if (trigger) {
      var modal = targetFrom(trigger);
      if (modal && modal.closest('body')) {
        event.preventDefault();
        event.stopPropagation();
        openLegacyModal(modal);
        return;
      }
    }

    var dismiss = event.target.closest('[data-dismiss="modal"]');
    if (dismiss && document.getElementById('cams-alpine-modal-body').contains(dismiss)) {
      event.preventDefault();
      event.stopPropagation();
      var modalStore = store();
      if (modalStore) modalStore.close();
    }
  }, true);

  window.addEventListener('cams:alpine-modal-close', function () {
    var host = document.getElementById('cams-alpine-modal-body');
    if (host) host.removeAttribute('data-cams-preserve-on-close');
    restore();
  });

  window.CamsAlpineModal = {
    openLegacy: openLegacyModal,
    close: function () {
      var modalStore = store();
      if (modalStore) modalStore.close();
    }
  };
})();
