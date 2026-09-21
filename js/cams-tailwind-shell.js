(function () {
  'use strict';

  function ready(fn) {
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', fn);
    else fn();
  }

  ready(function () {
    var body = document.body;
    var sidebar = document.querySelector('.main-sidebar');
    var toggle = document.querySelector('[data-cams-sidebar-toggle]');

    var overlay = document.createElement('button');
    overlay.type = 'button';
    overlay.id = 'cams-sidebar-overlay';
    overlay.setAttribute('aria-label', 'Close navigation');
    overlay.className = 'fixed inset-0 z-40 hidden bg-slate-950/50 backdrop-blur-sm md:hidden';
    body.appendChild(overlay);

    function setSidebar(open) {
      body.classList.toggle('cams-sidebar-open', open);
      overlay.classList.toggle('hidden', !open);
      if (toggle) toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    }

    if (toggle && sidebar) {
      toggle.addEventListener('click', function (event) {
        event.preventDefault();
        setSidebar(!body.classList.contains('cams-sidebar-open'));
      });
      overlay.addEventListener('click', function () { setSidebar(false); });
    }

    document.querySelectorAll('.sidebar-menu .treeview > a').forEach(function (trigger) {
      trigger.addEventListener('click', function (event) {
        var href = trigger.getAttribute('href') || '';
        if (href === '#' || href === '') event.preventDefault();
        var item = trigger.closest('.treeview');
        if (!item) return;
        var willOpen = !item.classList.contains('menu-open');
        var parent = item.parentElement;
        if (parent) {
          parent.querySelectorAll(':scope > .treeview.menu-open').forEach(function (openItem) {
            if (openItem !== item) openItem.classList.remove('menu-open');
          });
        }
        item.classList.toggle('menu-open', willOpen);
        trigger.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
      });
    });

    var userToggle = document.querySelector('[data-cams-user-menu]');
    if (userToggle) {
      var userItem = userToggle.closest('.user-menu');
      userToggle.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();
        if (userItem) userItem.classList.toggle('open');
      });
      document.addEventListener('click', function (event) {
        if (userItem && !userItem.contains(event.target)) userItem.classList.remove('open');
      });
    }

    window.addEventListener('resize', function () {
      if (window.innerWidth >= 768) setSidebar(false);
    });
  });
})();
