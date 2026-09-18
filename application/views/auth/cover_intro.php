<?php
$authRole = isset($authRole) ? $authRole : 'User';
$authHeadline = isset($authHeadline) ? $authHeadline : 'Welcome back';
$authDescription = isset($authDescription) ? $authDescription : 'Sign in to continue to CAMS.';
$authBadge = isset($authBadge) ? $authBadge : 'Secure access';
$authRoleClass = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $authRole));
?>
<div hidden
     x-cloak
     x-init="$el.removeAttribute('hidden')"
     x-show.important="$store.camsModal && $store.camsModal.open"
     x-transition.opacity
     @keydown.escape.window="$store.camsModal && $store.camsModal.close()"
     class="cams-alpine-modal-root fixed inset-0 z-[9999] flex items-center justify-center p-4 sm:p-6">
  <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm" @click="$store.camsModal.close()"></div>
  <div x-show.important="$store.camsModal.open" x-transition
       class="relative z-10 w-full max-w-3xl overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl">
    <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-6 py-5">
      <div>
        <p class="mb-1 text-[11px] font-bold uppercase tracking-[0.18em] text-emerald-600" x-text="$store.camsModal.eyebrow"></p>
        <h3 class="m-0 text-xl font-extrabold tracking-tight text-slate-900" x-text="$store.camsModal.title"></h3>
        <p class="mt-1 text-sm text-slate-500" x-show.important="$store.camsModal.description" x-text="$store.camsModal.description"></p>
      </div>
      <button type="button" @click="$store.camsModal.close()"
              class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-900"
              aria-label="Close modal">
        <i class="fa fa-times"></i>
      </button>
    </div>
    <div id="cams-alpine-modal-body" class="max-h-[72vh] overflow-y-auto p-6"></div>
  </div>
</div>

<div class="cams-auth-shell cams-auth-role-<?=htmlspecialchars($authRoleClass, ENT_QUOTES, 'UTF-8')?>">
  <aside class="cams-auth-cover" aria-label="CAMS introduction">
    <div class="cams-auth-cover-media" aria-hidden="true"></div>
    <div class="cams-auth-cover-overlay" aria-hidden="true"></div>

    <div class="cams-auth-cover-content">
      <div class="cams-auth-brand">
        <span class="cams-auth-brand-mark">
          <img src="<?=base_url()?>assets/dist/img/hnumcfi.jpg" alt="CAMS logo">
        </span>
        <div>
          <strong>HNU Medical Center</strong>
          <span>Clinics Appointment Management System</span>
        </div>
      </div>

      <div class="cams-auth-cover-copy">
        <span class="cams-auth-cover-badge"><i class="fa fa-shield"></i> <?=htmlspecialchars($authBadge, ENT_QUOTES, 'UTF-8')?></span>
        <h1><?=htmlspecialchars($authHeadline, ENT_QUOTES, 'UTF-8')?></h1>
        <p><?=htmlspecialchars($authDescription, ENT_QUOTES, 'UTF-8')?></p>

        <div class="cams-auth-cover-points">
          <div><i class="fa fa-calendar-check-o"></i><span><strong>Smarter appointments</strong><small>Manage bookings and schedules in one place.</small></span></div>
          <div><i class="fa fa-user-md"></i><span><strong>Connected clinic workflow</strong><small>Keep physicians, secretaries and patients aligned.</small></span></div>
          <div><i class="fa fa-lock"></i><span><strong>Role-based access</strong><small>Each account opens the tools relevant to its role.</small></span></div>
        </div>
      </div>

      <div class="cams-auth-cover-footer">
        <span>CAMS v2.0</span>
        <span>HNU Medical Center</span>
      </div>
    </div>
  </aside>

  <main class="cams-auth-login-pane">
