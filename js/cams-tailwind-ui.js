(function () {
  'use strict';

  function addClasses(node, classes) {
    if (!node || !classes) return;
    classes.split(/\s+/).filter(Boolean).forEach(function (name) {
      try { node.classList.add(name); } catch (e) {}
    });
  }

  function all(root, selector) {
    var scope = root && root.querySelectorAll ? root : document;
    return Array.prototype.slice.call(scope.querySelectorAll(selector));
  }

  function enhance(root) {
    all(root, '.content-wrapper, .right-side').forEach(function (el) {
      addClasses(el, '!bg-slate-50 dark:!bg-slate-950');
    });

    all(root, '.content-header h1, .cams-page-heading h1').forEach(function (el) {
      addClasses(el, '!font-heading !text-2xl !font-extrabold !tracking-tight !text-slate-900 dark:!text-white');
    });

    all(root, '.main-footer').forEach(function (el) {
      addClasses(el, '!border-t !border-slate-200 !bg-white !px-6 !py-4 !text-xs !text-slate-500 dark:!border-slate-800 dark:!bg-slate-900 dark:!text-slate-400');
    });

    all(root, '.content-header').forEach(function (el) {
      addClasses(el, '!px-4 !pt-6 !pb-2 sm:!px-6 lg:!px-8');
    });

    all(root, '.content').forEach(function (el) {
      addClasses(el, '!px-4 !pb-10 sm:!px-6 lg:!px-8');
    });

    all(root, '.box, .panel, .nav-tabs-custom, .card').forEach(function (el) {
      addClasses(el, '!rounded-2xl !border !border-slate-200 !bg-white !shadow-sm dark:!border-slate-800 dark:!bg-slate-900');
    });

    all(root, '.box-header, .panel-heading').forEach(function (el) {
      addClasses(el, '!border-slate-100 !bg-white !px-5 !py-4 dark:!border-slate-800 dark:!bg-slate-900');
    });

    all(root, '.box-body, .panel-body, .card-block').forEach(function (el) {
      addClasses(el, '!p-5 !text-slate-700 dark:!text-slate-200');
    });

    all(root, '.box-footer, .panel-footer').forEach(function (el) {
      addClasses(el, '!border-slate-100 !bg-slate-50 !px-5 !py-4 dark:!border-slate-800 dark:!bg-slate-900');
    });

    all(root, '.form-control, .select2-selection').forEach(function (el) {
      addClasses(el, '!min-h-11 !rounded-xl !border-slate-200 !bg-white !px-3.5 !text-base !text-slate-800 !shadow-none focus:!border-emerald-500 focus:!ring-4 focus:!ring-emerald-500/10 dark:!border-slate-700 dark:!bg-slate-950 dark:!text-slate-100');
    });

    all(root, '.input-group-addon').forEach(function (el) {
      addClasses(el, '!border-slate-200 !bg-slate-50 !text-slate-500 dark:!border-slate-700 dark:!bg-slate-800 dark:!text-slate-300');
    });

    all(root, '.form-group > label, .input_field_sections > h5').forEach(function (el) {
      addClasses(el, '!mb-2 !block !text-sm !font-bold !text-slate-600 dark:!text-slate-300');
    });

    all(root, '.btn').forEach(function (el) {
      addClasses(el, '!inline-flex !min-h-10 !items-center !justify-center !gap-2 !rounded-xl !border !px-4 !py-2 !text-sm !font-semibold !shadow-none !transition');
    });

    all(root, '.btn-primary, .btn-success').forEach(function (el) {
      addClasses(el, '!border-emerald-700 !bg-emerald-700 !text-white hover:!border-emerald-800 hover:!bg-emerald-800 hover:!text-white');
    });

    all(root, '.btn-default').forEach(function (el) {
      addClasses(el, '!border-slate-200 !bg-white !text-slate-700 hover:!bg-slate-50 dark:!border-slate-700 dark:!bg-slate-800 dark:!text-slate-200 dark:hover:!bg-slate-700');
    });

    all(root, '.btn-warning').forEach(function (el) {
      addClasses(el, '!border-amber-500 !bg-amber-500 !text-white hover:!bg-amber-600');
    });

    all(root, '.btn-danger').forEach(function (el) {
      addClasses(el, '!border-rose-600 !bg-rose-600 !text-white hover:!bg-rose-700');
    });

    all(root, '.table-responsive').forEach(function (el) {
      addClasses(el, '!overflow-x-auto !rounded-2xl !border !border-slate-200 dark:!border-slate-800');
    });

    all(root, 'table.table').forEach(function (el) {
      addClasses(el, '!w-full !border-collapse !bg-white !text-base dark:!bg-slate-900');
    });

    all(root, 'table.table thead th').forEach(function (el) {
      addClasses(el, '!border-b !border-slate-200 !bg-slate-50 !px-4 !py-3 !text-sm !font-bold !uppercase !tracking-wider !text-slate-500 dark:!border-slate-800 dark:!bg-slate-800/70 dark:!text-slate-400');
    });

    all(root, 'table.table tbody td').forEach(function (el) {
      addClasses(el, '!border-t !border-slate-100 !px-4 !py-3 !text-slate-700 dark:!border-slate-800 dark:!text-slate-200');
    });

    all(root, '.alert').forEach(function (el) {
      addClasses(el, '!rounded-xl !border !px-4 !py-3 !text-sm !font-medium');
    });

    all(root, '.modal-content').forEach(function (el) {
      addClasses(el, '!overflow-hidden !rounded-3xl !border !border-slate-200 !bg-white !shadow-2xl dark:!border-slate-700 dark:!bg-slate-900');
    });

    all(root, '.modal-header').forEach(function (el) {
      addClasses(el, '!border-slate-100 !px-6 !py-5 dark:!border-slate-800');
    });

    all(root, '.modal-body').forEach(function (el) {
      addClasses(el, '!p-6 dark:!bg-slate-900 dark:!text-slate-200');
    });

    all(root, '.modal-footer').forEach(function (el) {
      addClasses(el, '!border-slate-100 !bg-slate-50 !px-6 !py-4 dark:!border-slate-800 dark:!bg-slate-900');
    });
  }

  function markTailwindMode() {
    document.documentElement.classList.add('cams-tailwind-cdn');
    if (document.body) document.body.classList.add('cams-tailwind-cdn');
  }

  document.addEventListener('DOMContentLoaded', function () {
    markTailwindMode();
    enhance(document);

    var observer = new MutationObserver(function (mutations) {
      mutations.forEach(function (mutation) {
        Array.prototype.forEach.call(mutation.addedNodes || [], function (node) {
          if (node && node.nodeType === 1) enhance(node);
        });
      });
    });

    observer.observe(document.body, { childList: true, subtree: true });
  });

  window.CamsTailwindUI = { enhance: enhance };
})();
