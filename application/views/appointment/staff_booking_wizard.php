<?php
$getPatient = isset($getPatient) ? $getPatient : array();
$complaints = isset($complaints) ? $complaints : array();
$bookingProviders = isset($bookingProviders) ? $bookingProviders : array();
$bookingRole = isset($bookingRole) ? $bookingRole : 'Staff';
$availabilityUrl = isset($availabilityUrl) ? $availabilityUrl : '';
$formAction = isset($formAction) ? $formAction : '';
?>
<div class="content-wrapper">
  <section class="content">
    <div class="cams-booking-shell" data-cams-staff-booking data-availability-url="<?=htmlspecialchars($availabilityUrl, ENT_QUOTES, 'UTF-8')?>">
      <div class="cams-booking-hero">
        <div>
          <h1><i class="fa fa-calendar-plus-o"></i> Create Patient Appointment</h1>
          <p><?=htmlspecialchars($bookingRole, ENT_QUOTES, 'UTF-8')?> booking flow. Select the patient and concern, then choose the clinic and only a date that matches the physician's configured schedule.</p>
        </div>
        <span class="cams-booking-hero-badge"><i class="fa fa-user-md"></i> Staff assisted booking</span>
      </div>

      <?php if ($this->session->flashdata('error1')) { ?>
        <div class="cams-flash cams-flash-error"><i class="fa fa-exclamation-circle"></i> <?=htmlspecialchars($this->session->flashdata('error1'), ENT_QUOTES, 'UTF-8')?></div>
      <?php } ?>

      <div class="cams-wizard">
        <div class="cams-wizard-nav">
          <div class="cams-step-tab is-active"><span class="cams-step-number">1</span><div><strong>Patient</strong><small>Who is booking?</small><span class="cams-step-summary" data-step-summary="1"></span></div></div>
          <div class="cams-step-tab"><span class="cams-step-number">2</span><div><strong>Concern</strong><small>Reason for visit</small><span class="cams-step-summary" data-step-summary="2"></span></div></div>
          <div class="cams-step-tab"><span class="cams-step-number">3</span><div><strong>Clinic & Date</strong><small>Valid schedule only</small><span class="cams-step-summary" data-step-summary="3"></span></div></div>
          <div class="cams-step-tab"><span class="cams-step-number">4</span><div><strong>Review</strong><small>Confirm details</small></div></div>
        </div>

        <form id="appointment_form" action="<?=htmlspecialchars($formAction, ENT_QUOTES, 'UTF-8')?>" method="post" data-cams-ajax="true" data-confirm-title="Confirm appointment?" data-confirm-text="Are you sure you want to book this appointment?" data-confirm-button="Yes, book appointment" data-success-title="Appointment booked" data-follow-redirect="true">
          <input type="hidden" name="patient_id" value="">
          <input type="hidden" name="purpose" value="">
          <input type="hidden" name="clinic_id" value="">
          <input type="hidden" name="app_date" value="">

          <div class="cams-wizard-body">
            <section class="cams-wizard-panel is-active">
              <div class="cams-panel-heading"><div><h2>Select a patient</h2><p>Choose the patient this appointment is being created for.</p></div></div>
              <div class="cams-field" style="max-width:420px;margin-bottom:16px"><label for="cams-patient-search">Search patients</label><input id="cams-patient-search" data-patient-search type="search" class="form-control" placeholder="Search by name"></div>
              <div class="cams-provider-grid" data-patient-grid>
                <?php foreach ($getPatient as $patient) {
                  if (isset($patient->status) && $patient->status !== 'Active') continue;
                  $name = trim($patient->fname . ' ' . $patient->lname);
                  $image = base_url('uploads/profile-pic/' . (!empty($patient->image) ? $patient->image : 'default-pic.jpg'));
                ?>
                  <button type="button" class="cams-provider-card" data-patient-card data-patient-id="<?=intval($patient->patient_id)?>" data-patient-name="<?=htmlspecialchars($name, ENT_QUOTES, 'UTF-8')?>" data-search="<?=htmlspecialchars(strtolower($name), ENT_QUOTES, 'UTF-8')?>">
                    <span class="cams-provider-top"><img class="cams-provider-avatar" src="<?=htmlspecialchars($image, ENT_QUOTES, 'UTF-8')?>" alt=""><span><span class="cams-provider-name"><?=htmlspecialchars($name, ENT_QUOTES, 'UTF-8')?></span><span class="cams-provider-specialties">Patient #<?=htmlspecialchars($patient->patient_id, ENT_QUOTES, 'UTF-8')?></span></span></span>
                    <?php if (!empty($patient->contact)) { ?><span class="cams-provider-meta"><div><i class="fa fa-phone"></i><span><?=htmlspecialchars($patient->contact, ENT_QUOTES, 'UTF-8')?></span></div></span><?php } ?>
                  </button>
                <?php } ?>
              </div>
              <div class="cams-wizard-actions"><span></span><div class="cams-next-wrap"><button type="button" class="btn btn-success" data-next-step="2" disabled>Continue <i class="fa fa-arrow-right"></i></button></div></div>
            </section>

            <section class="cams-wizard-panel">
              <div class="cams-panel-heading"><div><h2>Reason for appointment</h2><p>Select a common concern or enter a custom reason.</p></div></div>
              <div class="cams-choice-grid">
                <?php foreach ($complaints as $complaint) { ?>
                  <button type="button" class="cams-complaint-card" data-value="<?=htmlspecialchars($complaint['value'], ENT_QUOTES, 'UTF-8')?>">
                    <span class="cams-complaint-icon"><i class="fa <?=htmlspecialchars($complaint['icon'], ENT_QUOTES, 'UTF-8')?>"></i></span>
                    <span><strong><?=htmlspecialchars($complaint['label'], ENT_QUOTES, 'UTF-8')?></strong><span>Use this as the chief complaint</span></span>
                  </button>
                <?php } ?>
                <button type="button" class="cams-complaint-card" data-custom="1" data-value=""><span class="cams-complaint-icon"><i class="fa fa-plus"></i></span><span><strong>Other concern</strong><span>Enter a custom reason</span></span></button>
              </div>
              <div class="cams-custom-box" id="cams-custom-box" style="display:none"><div class="cams-field"><label for="custom_purpose">Describe the concern</label><input id="custom_purpose" maxlength="100" type="text" class="form-control" placeholder="Enter the reason for this appointment"></div></div>
              <div class="cams-wizard-actions"><button type="button" class="btn cams-btn-secondary" data-prev-step="1"><i class="fa fa-arrow-left"></i> Back</button><div class="cams-next-wrap"><button type="button" class="btn btn-success" data-next-step="3" disabled>Choose clinic and date <i class="fa fa-arrow-right"></i></button></div></div>
            </section>

            <section class="cams-wizard-panel">
              <div class="cams-panel-heading"><div><h2>Choose clinic and available date</h2><p>The date list is generated from the physician's configured schedule. Invalid weekdays are never offered.</p></div></div>
              <div class="cams-provider-grid" style="margin-bottom:20px">
                <?php foreach ($bookingProviders as $provider) {
                  $doctor = 'Dr. ' . trim($provider->fname . ' ' . $provider->lname);
                ?>
                  <button type="button" class="cams-provider-card" data-clinic-card data-clinic-id="<?=intval($provider->clinic_id)?>" data-clinic="<?=htmlspecialchars($provider->clinic_name, ENT_QUOTES, 'UTF-8')?>" data-location="<?=htmlspecialchars($provider->location, ENT_QUOTES, 'UTF-8')?>" data-schedule="<?=htmlspecialchars($provider->schedule_label, ENT_QUOTES, 'UTF-8')?>" data-doctor="<?=htmlspecialchars($doctor, ENT_QUOTES, 'UTF-8')?>">
                    <span class="cams-provider-name"><?=htmlspecialchars($provider->clinic_name, ENT_QUOTES, 'UTF-8')?></span>
                    <span class="cams-provider-meta"><div><i class="fa fa-map-marker"></i><span><?=htmlspecialchars($provider->location, ENT_QUOTES, 'UTF-8')?></span></div><div><i class="fa fa-clock-o"></i><span><?=htmlspecialchars($provider->schedule_label, ENT_QUOTES, 'UTF-8')?></span></div></span>
                  </button>
                <?php } ?>
              </div>
              <div class="cams-selected-provider" id="cams-selected-provider"><div><strong data-selected-doctor>Select a clinic above</strong><span data-selected-clinic>The valid date options will appear below.</span></div></div>
              <div class="cams-loading" id="cams-date-loading" style="display:none"><i class="fa fa-spinner fa-spin"></i> Checking appointment availability...</div>
              <div class="cams-date-grid" id="cams-date-grid"></div>
              <div class="cams-wizard-actions"><button type="button" class="btn cams-btn-secondary" data-prev-step="2"><i class="fa fa-arrow-left"></i> Back</button><div class="cams-next-wrap"><button type="button" class="btn btn-success" data-next-step="4" disabled>Review appointment <i class="fa fa-arrow-right"></i></button></div></div>
            </section>

            <section class="cams-wizard-panel">
              <div class="cams-panel-heading"><div><h2>Review appointment</h2><p>Verify the patient, clinic and schedule before saving.</p></div></div>
              <div class="cams-review">
                <div class="cams-review-card"><h4>Patient & concern</h4><div class="cams-review-row"><span>Patient</span><strong data-review-patient></strong></div><div class="cams-review-row"><span>Chief complaint</span><strong data-review-purpose></strong></div><div class="cams-review-row"><span>Physician</span><strong data-review-doctor></strong></div></div>
                <div class="cams-review-card"><h4>Visit details</h4><div class="cams-review-row"><span>Clinic</span><strong data-review-clinic></strong></div><div class="cams-review-row"><span>Location</span><strong data-review-location></strong></div><div class="cams-review-row"><span>Date</span><strong data-review-date></strong></div><div class="cams-review-row"><span>Schedule</span><strong data-review-schedule></strong></div></div>
              </div>
              <div class="cams-booking-note"><i class="fa fa-info-circle"></i> CAMS rechecks capacity before saving and assigns the queue number only after the booking succeeds.</div>
              <div class="cams-wizard-actions"><button type="button" class="btn cams-btn-secondary" data-prev-step="3"><i class="fa fa-arrow-left"></i> Back</button><div class="cams-next-wrap"><button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Confirm appointment</button></div></div>
            </section>
          </div>
        </form>
      </div>
    </div>
  </section>
</div>
