<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$forcedPasswordChange = !empty($forcedPasswordChange);

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
  data-modal-description="<?=$forcedPasswordChange ? 'Your current password is temporary. Set a new password before continuing in CAMS.' : 'Confirm your current password, then choose a stronger password for your CAMS account.'?>"
  data-modal-width="720px"
  data-modal-locked="<?=$forcedPasswordChange ? 'true' : 'false'?>"
>
  <div class="space-y-5">
    <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4 text-sm text-emerald-900 dark:border-emerald-900/60 dark:bg-emerald-950/30 dark:text-emerald-200">
      <div class="flex gap-3">
        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-emerald-700 shadow-sm dark:bg-slate-900 dark:text-emerald-300">
          <i class="fa fa-lock"></i>
        </span>
        <div>
          <strong class="block text-base"><?=$forcedPasswordChange ? 'Replace your temporary password' : 'Secure your ' . cams_password_e($role) . ' account'?></strong>
          <span class="mt-1 block leading-6 text-emerald-800/80 dark:text-emerald-200/75">
            <?=$forcedPasswordChange ? 'Enter the temporary password as your current password, then create your own password to continue.' : 'Use a password that combines letters, numbers, and special characters.'?>
          </span>
        </div>
      </div>
    </div>

    <form
      action="<?=cams_password_e($passwordAction)?>"
      method="post"
      class="space-y-5"
      data-cams-ajax="true"
      data-reset-on-success="true"
      data-confirm-title="Update your password?"
      data-confirm-text="You will use the new password the next time you sign in."
      data-confirm-button="Yes, update password"
      data-success-title="Password updated"
    >
      <input type="hidden" name="<?=cams_password_e($csrfName)?>" value="<?=cams_password_e($csrfHash)?>">

      <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-900">
        <div class="form-group !mb-0">
          <label for="cams-old-password" class="!mb-2">Current Password</label>
          <div class="relative">
            <input id="cams-old-password" name="oldPassword" class="form-control !min-h-12 !pr-12 !text-base" type="password" maxlength="20" autocomplete="current-password" required>
            <button type="button" class="cams-password-toggle absolute right-2 top-1/2 -translate-y-1/2 rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-slate-200" data-password-target="cams-old-password" aria-label="Show current password">
              <i class="fa fa-eye"></i>
            </button>
          </div>
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-700 dark:bg-slate-950">
        <div class="form-group !mb-0">
          <label for="cams-new-password" class="!mb-2">New Password</label>
          <div class="relative">
            <input
              id="cams-new-password"
              name="newPassword"
              class="form-control !min-h-12 !pr-12 !text-base"
              type="password"
              maxlength="20"
              autocomplete="new-password"
              data-cams-strength-input
              required
            >
            <button type="button" class="cams-password-toggle absolute right-2 top-1/2 -translate-y-1/2 rounded-lg p-2 text-slate-400 transition hover:bg-white hover:text-slate-700 dark:hover:bg-slate-900 dark:hover:text-slate-200" data-password-target="cams-new-password" aria-label="Show new password">
              <i class="fa fa-eye"></i>
            </button>
          </div>
        </div>

        <div class="mt-4" data-cams-password-strength>
          <div class="flex items-center justify-between gap-3">
            <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Password strength</span>
            <span class="text-sm font-extrabold text-slate-400" data-cams-strength-label>Enter a password</span>
          </div>

          <div class="mt-2 h-2.5 overflow-hidden rounded-full bg-slate-200 dark:bg-slate-800">
            <div class="h-full w-0 rounded-full transition-all duration-300" data-cams-strength-bar style="background:#cbd5e1"></div>
          </div>

          <div class="mt-4 grid gap-2 text-sm sm:grid-cols-2">
            <span class="flex items-center gap-2 text-slate-500 dark:text-slate-400" data-cams-rule="length">
              <i class="fa fa-circle-o text-xs"></i> At least 7 characters
            </span>
            <span class="flex items-center gap-2 text-slate-500 dark:text-slate-400" data-cams-rule="letter">
              <i class="fa fa-circle-o text-xs"></i> Contains a letter
            </span>
            <span class="flex items-center gap-2 text-slate-500 dark:text-slate-400" data-cams-rule="number">
              <i class="fa fa-circle-o text-xs"></i> Contains a number
            </span>
            <span class="flex items-center gap-2 text-slate-500 dark:text-slate-400" data-cams-rule="special">
              <i class="fa fa-circle-o text-xs"></i> Contains a special character
            </span>
          </div>
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-900">
        <div class="form-group !mb-0">
          <label for="cams-confirm-password" class="!mb-2">Confirm New Password</label>
          <div class="relative">
            <input id="cams-confirm-password" name="cNewPassword" class="form-control !min-h-12 !pr-12 !text-base" type="password" maxlength="20" autocomplete="new-password" required>
            <button type="button" class="cams-password-toggle absolute right-2 top-1/2 -translate-y-1/2 rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-slate-200" data-password-target="cams-confirm-password" aria-label="Show confirmed password">
              <i class="fa fa-eye"></i>
            </button>
          </div>
          <p class="mb-0 mt-2 hidden text-sm font-semibold" data-cams-password-match></p>
        </div>
      </div>

      <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:items-center sm:justify-between dark:border-slate-800">
        <p class="m-0 text-sm text-slate-500 dark:text-slate-400">
          <i class="fa fa-shield mr-1 text-emerald-600"></i>
          Your current password is required before this change can be saved.
        </p>
        <button type="submit" class="btn btn-primary !min-h-11 !px-5">
          <i class="fa fa-key"></i>
          Update Password
        </button>
      </div>
    </form>
  </div>
</div>
