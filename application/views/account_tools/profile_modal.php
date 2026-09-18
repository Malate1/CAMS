<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$image = !empty($record->image) ? $record->image : 'default.png';
$displayName = trim((string)$record->fname . ' ' . (string)$record->lname);

if (!function_exists('cams_account_e')) {
    function cams_account_e($value) {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}
?>
<div
  data-cams-modal-content
  data-modal-title="Update Profile"
  data-modal-eyebrow="Account tools"
  data-modal-description="Update your personal information and profile photo without leaving the current page."
  data-modal-width="1050px"
>
  <div class="grid gap-6 lg:grid-cols-[280px_minmax(0,1fr)]">
    <aside class="self-start overflow-hidden rounded-3xl border border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-950">
      <div class="border-b border-slate-200 p-6 text-center dark:border-slate-800">
        <div class="mx-auto h-28 w-28 overflow-hidden rounded-3xl border-4 border-white bg-white shadow-md dark:border-slate-800 dark:bg-slate-900">
          <img
            src="<?=base_url('uploads/profile-pic/' . rawurlencode($image))?>"
            alt="<?=cams_account_e($displayName)?>"
            class="h-full w-full object-cover"
            data-cams-account-avatar
          >
        </div>

        <strong class="mt-4 block text-xl font-extrabold tracking-tight text-slate-950 dark:text-white" data-cams-account-name>
          <?=cams_account_e($displayName)?>
        </strong>
        <span class="mt-1 block text-sm font-semibold text-emerald-700 dark:text-emerald-400"><?=cams_account_e($role)?></span>
        <span class="mt-2 block break-all text-sm leading-5 text-slate-500 dark:text-slate-400"><?=cams_account_e($record->email)?></span>
      </div>

      <form
        action="<?=cams_account_e($photoAction)?>"
        method="post"
        enctype="multipart/form-data"
        class="space-y-4 p-5"
        data-cams-ajax="true"
        data-reset-on-success="false"
        data-confirm-title="Update profile photo?"
        data-confirm-text="Your current profile photo will be replaced."
        data-confirm-button="Yes, update photo"
        data-success-title="Profile photo updated"
      >
        <input type="hidden" name="<?=cams_account_e($csrfName)?>" value="<?=cams_account_e($csrfHash)?>">

        <div>
          <span class="block text-sm font-extrabold text-slate-800 dark:text-slate-100">Profile Photo</span>
          <span class="mt-1 block text-sm leading-5 text-slate-500 dark:text-slate-400">JPG, PNG, or GIF. Choose a clear photo for easier identification.</span>
        </div>

        <label for="cams-account-photo"
               class="flex min-h-[116px] cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-300 bg-white px-4 py-4 text-center transition hover:border-emerald-400 hover:bg-emerald-50/60 dark:border-slate-700 dark:bg-slate-900 dark:hover:border-emerald-700 dark:hover:bg-emerald-950/20">
          <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300">
            <i class="fa fa-camera"></i>
          </span>
          <strong class="mt-2 text-sm text-slate-800 dark:text-slate-100" data-cams-photo-label>Choose a profile photo</strong>
          <span class="mt-1 text-xs text-slate-400">Click to browse</span>
          <input
            id="cams-account-photo"
            type="file"
            name="Editphoto"
            accept="image/png,image/jpeg,image/gif"
            class="sr-only"
            data-cams-photo-input
            required
          >
        </label>

        <button type="submit" class="btn btn-default !min-h-11 !w-full">
          <i class="fa fa-camera"></i>
          Update Photo
        </button>
      </form>
    </aside>

    <section class="rounded-3xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-900 sm:p-7">
      <div class="mb-6 border-b border-slate-100 pb-5 dark:border-slate-800">
        <div class="flex items-start gap-3">
          <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300">
            <i class="fa fa-user"></i>
          </span>
          <div>
            <h4 class="m-0 text-xl font-extrabold tracking-tight text-slate-950 dark:text-white">Personal Details</h4>
            <p class="mb-0 mt-1 text-sm leading-6 text-slate-500 dark:text-slate-400">Keep your contact and personal information accurate and up to date.</p>
          </div>
        </div>
      </div>

      <form
        action="<?=cams_account_e($profileAction)?>"
        method="post"
        class="space-y-6"
        data-cams-ajax="true"
        data-reset-on-success="false"
        data-confirm-title="Save profile changes?"
        data-confirm-text="Please confirm that you want to update your personal information."
        data-confirm-button="Yes, save changes"
        data-success-title="Profile updated"
      >
        <input type="hidden" name="<?=cams_account_e($csrfName)?>" value="<?=cams_account_e($csrfHash)?>">

        <div class="grid gap-x-5 gap-y-5 sm:grid-cols-2">
          <div class="form-group !mb-0">
            <label for="cams-profile-fname" class="!mb-2">First Name</label>
            <input id="cams-profile-fname" type="text" class="form-control !min-h-12 !text-base" name="fname" value="<?=cams_account_e($record->fname)?>" required>
          </div>

          <div class="form-group !mb-0">
            <label for="cams-profile-mname" class="!mb-2">Middle Name</label>
            <input id="cams-profile-mname" type="text" class="form-control !min-h-12 !text-base" name="mname" value="<?=cams_account_e($record->mname)?>" required>
          </div>

          <div class="form-group !mb-0">
            <label for="cams-profile-lname" class="!mb-2">Last Name</label>
            <input id="cams-profile-lname" type="text" class="form-control !min-h-12 !text-base" name="lname" value="<?=cams_account_e($record->lname)?>" required>
          </div>

          <div class="form-group !mb-0">
            <label for="cams-profile-contact" class="!mb-2">Contact Number</label>
            <input id="cams-profile-contact" type="text" class="form-control !min-h-12 !text-base" name="contact" value="<?=cams_account_e($record->contact)?>" required>
          </div>

          <div class="form-group !mb-0">
            <label for="cams-profile-birthplace" class="!mb-2">Birth Place</label>
            <input id="cams-profile-birthplace" type="text" class="form-control !min-h-12 !text-base" name="birthplace" value="<?=cams_account_e($record->birthplace)?>" required>
          </div>

          <div class="form-group !mb-0">
            <label for="cams-profile-birthdate" class="!mb-2">Birth Date</label>
            <input id="cams-profile-birthdate" type="date" class="form-control !min-h-12 !text-base" name="birthdate" value="<?=cams_account_e($record->birthdate)?>" required>
          </div>

          <div class="form-group !mb-0">
            <label for="cams-profile-gender" class="!mb-2">Gender</label>
            <select id="cams-profile-gender" name="gender" class="form-control !min-h-12 !text-base" required>
              <option value="Male" <?=$record->gender === 'Male' ? 'selected' : ''?>>Male</option>
              <option value="Female" <?=$record->gender === 'Female' ? 'selected' : ''?>>Female</option>
            </select>
          </div>

          <div class="form-group !mb-0">
            <label for="cams-profile-address" class="!mb-2">Address</label>
            <input id="cams-profile-address" type="text" class="form-control !min-h-12 !text-base" name="address" value="<?=cams_account_e($record->address)?>" required>
          </div>
        </div>

        <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:items-center sm:justify-between dark:border-slate-800">
          <p class="m-0 text-sm text-slate-500 dark:text-slate-400">
            <i class="fa fa-info-circle mr-1 text-emerald-600"></i>
            Changes appear in your CAMS header immediately after saving.
          </p>
          <button type="submit" class="btn btn-primary !min-h-11 !px-5">
            <i class="fa fa-save"></i>
            Save Profile
          </button>
        </div>
      </form>
    </section>
  </div>
</div>
