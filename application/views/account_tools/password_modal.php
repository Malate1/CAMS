<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('cams_password_e')) {
    function cams_password_e($value) {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}
?>
<div
  data-cams-modal-content
  data-modal-title="Update Password"
  data-modal-eyebrow="Account security"
  data-modal-description="Confirm your current password, then choose a new password for your CAMS account."
  class="space-y-5"
>
  <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4 text-sm text-emerald-900 dark:border-emerald-900/60 dark:bg-emerald-950/30 dark:text-emerald-200">
    <div class="flex gap-3">
      <i class="fa fa-lock mt-0.5 text-emerald-700 dark:text-emerald-300"></i>
      <div>
        <strong class="block">Secure your <?=cams_password_e($role)?> account</strong>
        <span class="mt-1 block text-emerald-800/80 dark:text-emerald-200/75">Your new password must match the confirmation field and can contain up to 20 characters.</span>
      </div>
    </div>
  </div>

  <form
    action="<?=cams_password_e($passwordAction)?>"
    method="post"
    class="space-y-4"
    data-cams-ajax="true"
    data-reset-on-success="true"
    data-confirm-title="Update your password?"
    data-confirm-text="You will use the new password the next time you sign in."
    data-confirm-button="Yes, update password"
    data-success-title="Password updated"
  >
    <input type="hidden" name="<?=cams_password_e($csrfName)?>" value="<?=cams_password_e($csrfHash)?>">

    <div class="form-group !mb-0">
      <label for="cams-old-password">Current Password</label>
      <div class="relative">
        <input id="cams-old-password" name="oldPassword" class="form-control !pr-12" type="password" maxlength="20" autocomplete="current-password" required>
        <button type="button" class="cams-password-toggle absolute right-2 top-1/2 -translate-y-1/2 rounded-lg p-2 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200" data-password-target="cams-old-password" aria-label="Show current password">
          <i class="fa fa-eye"></i>
        </button>
      </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
      <div class="form-group !mb-0">
        <label for="cams-new-password">New Password</label>
        <div class="relative">
          <input id="cams-new-password" name="newPassword" class="form-control !pr-12" type="password" maxlength="20" autocomplete="new-password" required>
          <button type="button" class="cams-password-toggle absolute right-2 top-1/2 -translate-y-1/2 rounded-lg p-2 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200" data-password-target="cams-new-password" aria-label="Show new password">
            <i class="fa fa-eye"></i>
          </button>
        </div>
      </div>

      <div class="form-group !mb-0">
        <label for="cams-confirm-password">Confirm New Password</label>
        <div class="relative">
          <input id="cams-confirm-password" name="cNewPassword" class="form-control !pr-12" type="password" maxlength="20" autocomplete="new-password" required>
          <button type="button" class="cams-password-toggle absolute right-2 top-1/2 -translate-y-1/2 rounded-lg p-2 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200" data-password-target="cams-confirm-password" aria-label="Show confirmed password">
            <i class="fa fa-eye"></i>
          </button>
        </div>
      </div>
    </div>

    <div class="flex justify-end border-t border-slate-100 pt-4 dark:border-slate-800">
      <button type="submit" class="btn btn-primary">
        <i class="fa fa-key"></i>
        Update Password
      </button>
    </div>
  </form>
</div>
