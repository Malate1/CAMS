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
  data-modal-description="Update your personal information or profile photo without leaving the current page."
  class="space-y-5"
>
  <div class="grid gap-5 lg:grid-cols-[220px_minmax(0,1fr)]">
    <aside class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-950">
      <div class="flex flex-col items-center text-center">
        <img
          src="<?=base_url('uploads/profile-pic/' . rawurlencode($image))?>"
          alt="<?=cams_account_e($displayName)?>"
          class="h-24 w-24 rounded-2xl border-4 border-white object-cover shadow-sm dark:border-slate-800"
          data-cams-account-avatar
        >
        <strong class="mt-3 text-base font-extrabold text-slate-900 dark:text-white" data-cams-account-name>
          <?=cams_account_e($displayName)?>
        </strong>
        <span class="mt-1 text-sm text-slate-500 dark:text-slate-400"><?=cams_account_e($role)?></span>
        <span class="mt-1 break-all text-sm text-slate-500 dark:text-slate-400"><?=cams_account_e($record->email)?></span>
      </div>

      <form
        action="<?=cams_account_e($photoAction)?>"
        method="post"
        enctype="multipart/form-data"
        class="mt-5 space-y-3"
        data-cams-ajax="true"
        data-reset-on-success="false"
        data-confirm-title="Update profile photo?"
        data-confirm-text="Your current profile photo will be replaced."
        data-confirm-button="Yes, update photo"
        data-success-title="Profile photo updated"
      >
        <input type="hidden" name="<?=cams_account_e($csrfName)?>" value="<?=cams_account_e($csrfHash)?>">
        <div>
          <label for="cams-account-photo" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">Profile photo</label>
          <input
            id="cams-account-photo"
            type="file"
            name="Editphoto"
            accept="image/png,image/jpeg,image/gif"
            class="form-control"
            required
          >
          <small class="mt-1.5 block text-slate-500 dark:text-slate-400">Use a JPG, PNG, or GIF image.</small>
        </div>
        <button type="submit" class="btn btn-default w-full">
          <i class="fa fa-camera"></i>
          Update Photo
        </button>
      </form>
    </aside>

    <form
      action="<?=cams_account_e($profileAction)?>"
      method="post"
      class="space-y-4"
      data-cams-ajax="true"
      data-reset-on-success="false"
      data-confirm-title="Save profile changes?"
      data-confirm-text="Please confirm that you want to update your personal information."
      data-confirm-button="Yes, save changes"
      data-success-title="Profile updated"
    >
      <input type="hidden" name="<?=cams_account_e($csrfName)?>" value="<?=cams_account_e($csrfHash)?>">

      <div>
        <h4 class="m-0 text-lg font-extrabold text-slate-900 dark:text-white">Personal Details</h4>
        <p class="mb-0 mt-1 text-sm text-slate-500 dark:text-slate-400">Keep your contact and personal information current.</p>
      </div>

      <div class="grid gap-4 sm:grid-cols-2">
        <div class="form-group !mb-0">
          <label for="cams-profile-fname">First Name</label>
          <input id="cams-profile-fname" type="text" class="form-control" name="fname" value="<?=cams_account_e($record->fname)?>" required>
        </div>
        <div class="form-group !mb-0">
          <label for="cams-profile-mname">Middle Name</label>
          <input id="cams-profile-mname" type="text" class="form-control" name="mname" value="<?=cams_account_e($record->mname)?>" required>
        </div>
        <div class="form-group !mb-0">
          <label for="cams-profile-lname">Last Name</label>
          <input id="cams-profile-lname" type="text" class="form-control" name="lname" value="<?=cams_account_e($record->lname)?>" required>
        </div>
        <div class="form-group !mb-0">
          <label for="cams-profile-contact">Contact</label>
          <input id="cams-profile-contact" type="text" class="form-control" name="contact" value="<?=cams_account_e($record->contact)?>" required>
        </div>
        <div class="form-group !mb-0">
          <label for="cams-profile-birthplace">Birth Place</label>
          <input id="cams-profile-birthplace" type="text" class="form-control" name="birthplace" value="<?=cams_account_e($record->birthplace)?>" required>
        </div>
        <div class="form-group !mb-0">
          <label for="cams-profile-birthdate">Birth Date</label>
          <input id="cams-profile-birthdate" type="date" class="form-control" name="birthdate" value="<?=cams_account_e($record->birthdate)?>" required>
        </div>
        <div class="form-group !mb-0">
          <label for="cams-profile-gender">Gender</label>
          <select id="cams-profile-gender" name="gender" class="form-control" required>
            <option value="Male" <?=$record->gender === 'Male' ? 'selected' : ''?>>Male</option>
            <option value="Female" <?=$record->gender === 'Female' ? 'selected' : ''?>>Female</option>
          </select>
        </div>
        <div class="form-group !mb-0">
          <label for="cams-profile-address">Address</label>
          <input id="cams-profile-address" type="text" class="form-control" name="address" value="<?=cams_account_e($record->address)?>" required>
        </div>
      </div>

      <div class="flex justify-end border-t border-slate-100 pt-4 dark:border-slate-800">
        <button type="submit" class="btn btn-primary">
          <i class="fa fa-save"></i>
          Save Profile
        </button>
      </div>
    </form>
  </div>
</div>
