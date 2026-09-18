<?php
$this->load->view('header/headerPatient');
$complaints = isset($complaints) ? $complaints : array();
$bookingProviders = isset($bookingProviders) ? $bookingProviders : array();
$specialties = isset($specialties) ? $specialties : array();
?>

<div class="content-wrapper">
  <section class="content">
    <div class="cams-booking-shell" data-cams-booking data-availability-url="<?=htmlspecialchars(base_url('appointment-availability'), ENT_QUOTES, 'UTF-8')?>">
      <div class="cams-booking-hero">
        <div>
          <h1><i class="fa fa-calendar-check-o" aria-hidden="true"></i> Book an Appointment</h1>
          <p>Tell us what you need help with. CAMS will recommend doctors based on their specialization, show the clinic and physician schedule, and let you choose only dates when that physician is actually available.</p>
        </div>
        <span class="cams-booking-hero-badge"><i class="fa fa-shield"></i> 4 simple steps</span>
      </div>

      <?php if ($this->session->flashdata('error1')) { ?>
        <div class="cams-flash cams-flash-error"><i class="fa fa-exclamation-circle"></i> <?=htmlspecialchars($this->session->flashdata('error1'), ENT_QUOTES, 'UTF-8')?></div>
      <?php } ?>

      <div class="cams-wizard">
        <div class="cams-wizard-nav" aria-label="Appointment booking steps">
          <div class="cams-step-tab is-active">
            <span class="cams-step-number">1</span>
            <div><strong>Concern</strong><small>What brings you in?</small><span class="cams-step-summary" data-step-summary="1"></span></div>
          </div>
          <div class="cams-step-tab">
            <span class="cams-step-number">2</span>
            <div><strong>Doctor & Clinic</strong><small>Matched by specialty</small><span class="cams-step-summary" data-step-summary="2"></span></div>
          </div>
          <div class="cams-step-tab">
            <span class="cams-step-number">3</span>
            <div><strong>Available Date</strong><small>Schedule days only</small><span class="cams-step-summary" data-step-summary="3"></span></div>
          </div>
          <div class="cams-step-tab">
            <span class="cams-step-number">4</span>
            <div><strong>Review</strong><small>Confirm your booking</small></div>
          </div>
        </div>

        <form id="appointment_form" action="<?=base_url('Patient/AppointmentRegister')?>" method="post" data-cams-ajax="true" data-confirm-title="Confirm appointment?" data-confirm-text="Are you sure you want to book this appointment?" data-confirm-button="Yes, book appointment" data-success-title="Appointment booked" data-follow-redirect="true">
          <input type="hidden" name="purpose" value="">
          <input type="hidden" name="physician_id" value="">
          <input type="hidden" name="clinic_id" value="">
          <input type="hidden" name="app_date" value="">
          <input type="hidden" name="specialty_name" value="">

          <div class="cams-wizard-body">
            <section class="cams-wizard-panel is-active" data-step="1">
              <div class="cams-panel-heading">
                <div>
                  <h2>What can we help you with?</h2>
                  <p>Select the chief complaint that best matches your concern. We use it only to match you with relevant physician specializations.</p>
                </div>
              </div>

              <div class="cams-choice-grid">
                <?php foreach ($complaints as $complaint) { ?>
                  <button type="button" class="cams-complaint-card"
                          data-value="<?=htmlspecialchars($complaint['value'], ENT_QUOTES, 'UTF-8')?>"
                          data-specialties='<?=htmlspecialchars(json_encode($complaint['specialties']), ENT_QUOTES, 'UTF-8')?>'>
                    <span class="cams-complaint-icon"><i class="fa <?=htmlspecialchars($complaint['icon'], ENT_QUOTES, 'UTF-8')?>"></i></span>
                    <span>
                      <strong><?=htmlspecialchars($complaint['label'], ENT_QUOTES, 'UTF-8')?></strong>
                      <span><?=htmlspecialchars(implode(' · ', $complaint['specialties']), ENT_QUOTES, 'UTF-8')?></span>
                    </span>
                  </button>
                <?php } ?>

                <button type="button" class="cams-complaint-card" data-custom="1" data-value="" data-specialties="[]">
                  <span class="cams-complaint-icon"><i class="fa fa-plus"></i></span>
                  <span><strong>Other concern</strong><span>Describe it and choose the most relevant specialty</span></span>
                </button>
              </div>

              <div class="cams-custom-box" id="cams-custom-box" style="display:none">
                <div class="cams-custom-row">
                  <div class="cams-field">
                    <label for="custom_purpose">Describe your concern</label>
                    <input id="custom_purpose" type="text" maxlength="100" class="form-control" placeholder="Example: recurring eye irritation">
                  </div>
                  <div class="cams-field">
                    <label for="custom_specialty">Choose a specialty</label>
                    <select id="custom_specialty" class="form-control">
                      <option value="">Select specialty</option>
                      <?php foreach ($specialties as $specialty) { ?>
                        <option value="<?=htmlspecialchars($specialty, ENT_QUOTES, 'UTF-8')?>"><?=htmlspecialchars($specialty, ENT_QUOTES, 'UTF-8')?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
              </div>

              <div class="cams-wizard-actions">
                <span></span>
                <div class="cams-next-wrap"><button type="button" class="btn btn-success" data-next-step="2" disabled>Find matching doctors <i class="fa fa-arrow-right"></i></button></div>
              </div>
            </section>

            <section class="cams-wizard-panel" data-step="2">
              <div class="cams-panel-heading">
                <div>
                  <h2>Choose a physician and clinic</h2>
                  <p>Only physicians whose specialization matches your selected concern are shown.</p>
                </div>
              </div>

              <div class="cams-provider-toolbar">
                <div>
                  <strong>Recommended specialties</strong>
                  <div class="cams-recommendation" id="cams-recommendation"></div>
                </div>
              </div>

              <div class="cams-provider-grid" id="cams-provider-grid">
                <?php foreach ($bookingProviders as $provider) {
                  $doctorName = 'Dr. ' . trim($provider->fname . ' ' . $provider->lname);
                  $image = base_url('uploads/profile-pic/' . ($provider->image ? $provider->image : 'default-pic.jpg'));
                ?>
                  <button type="button" class="cams-provider-card"
                          data-physician-id="<?=intval($provider->physician_id)?>"
                          data-clinic-id="<?=intval($provider->clinic_id)?>"
                          data-doctor="<?=htmlspecialchars($doctorName, ENT_QUOTES, 'UTF-8')?>"
                          data-clinic="<?=htmlspecialchars($provider->clinic_name, ENT_QUOTES, 'UTF-8')?>"
                          data-location="<?=htmlspecialchars($provider->location, ENT_QUOTES, 'UTF-8')?>"
                          data-schedule="<?=htmlspecialchars($provider->schedule_label, ENT_QUOTES, 'UTF-8')?>"
                          data-specialties='<?=htmlspecialchars(json_encode($provider->specialty_list), ENT_QUOTES, 'UTF-8')?>'>
                    <span class="cams-provider-top">
                      <img class="cams-provider-avatar" src="<?=htmlspecialchars($image, ENT_QUOTES, 'UTF-8')?>" alt="">
                      <span>
                        <span class="cams-provider-name"><?=htmlspecialchars($doctorName, ENT_QUOTES, 'UTF-8')?></span>
                        <span class="cams-provider-specialties"><?=htmlspecialchars(implode(' · ', $provider->specialty_list), ENT_QUOTES, 'UTF-8')?></span>
                      </span>
                    </span>
                    <span class="cams-provider-meta">
                      <div><i class="fa fa-hospital-o"></i><span><strong><?=htmlspecialchars($provider->clinic_name, ENT_QUOTES, 'UTF-8')?></strong><br><?=htmlspecialchars($provider->location, ENT_QUOTES, 'UTF-8')?></span></div>
                      <div><i class="fa fa-clock-o"></i><span><?=htmlspecialchars($provider->schedule_label, ENT_QUOTES, 'UTF-8')?></span></div>
                    </span>
                  </button>
                <?php } ?>
              </div>

              <div class="cams-empty-state" id="cams-provider-empty" style="display:none">
                <i class="fa fa-user-md"></i>
                No active physician currently matches this specialty. Go back and choose another specialty or contact the clinic for assistance.
              </div>

              <div class="cams-wizard-actions">
                <button type="button" class="btn cams-btn-secondary" data-prev-step="1"><i class="fa fa-arrow-left"></i> Back</button>
                <div class="cams-next-wrap"><button type="button" class="btn btn-success" data-next-step="3" disabled>Choose a date <i class="fa fa-arrow-right"></i></button></div>
              </div>
            </section>

            <section class="cams-wizard-panel" data-step="3">
              <div class="cams-panel-heading">
                <div>
                  <h2>Choose an available date</h2>
                  <p>Only dates that match the physician's actual clinic schedule are shown. Fully booked dates cannot be selected.</p>
                </div>
              </div>

              <div class="cams-selected-provider" id="cams-selected-provider">
                <div><strong data-selected-doctor></strong><span data-selected-clinic></span></div>
                <span class="cams-specialty-chip"><i class="fa fa-calendar-check-o"></i>&nbsp; Schedule matched</span>
              </div>

              <div class="cams-loading" id="cams-date-loading" style="display:none"><i class="fa fa-spinner fa-spin"></i> Checking live appointment availability...</div>
              <div class="cams-date-grid" id="cams-date-grid"></div>

              <div class="cams-wizard-actions">
                <button type="button" class="btn cams-btn-secondary" data-prev-step="2"><i class="fa fa-arrow-left"></i> Back</button>
                <div class="cams-next-wrap"><button type="button" class="btn btn-success" data-next-step="4" disabled>Review appointment <i class="fa fa-arrow-right"></i></button></div>
              </div>
            </section>

            <section class="cams-wizard-panel" data-step="4">
              <div class="cams-panel-heading">
                <div>
                  <h2>Review your appointment</h2>
                  <p>Check the details below before booking. Your queue number will be assigned after the appointment is successfully saved.</p>
                </div>
              </div>

              <div class="cams-review">
                <div class="cams-review-card">
                  <h4>Your concern</h4>
                  <div class="cams-review-row"><span>Chief complaint</span><strong data-review-purpose></strong></div>
                  <div class="cams-review-row"><span>Matched specialty</span><strong data-review-specialty></strong></div>
                  <div class="cams-review-row"><span>Physician</span><strong data-review-doctor></strong></div>
                </div>
                <div class="cams-review-card">
                  <h4>Visit details</h4>
                  <div class="cams-review-row"><span>Clinic</span><strong data-review-clinic></strong></div>
                  <div class="cams-review-row"><span>Location</span><strong data-review-location></strong></div>
                  <div class="cams-review-row"><span>Date</span><strong data-review-date></strong></div>
                  <div class="cams-review-row"><span>Schedule</span><strong data-review-schedule></strong></div>
                </div>
              </div>

              <div class="cams-booking-note"><i class="fa fa-info-circle"></i> Availability is checked again when you confirm, so a slot cannot be overbooked if another patient books at the same moment.</div>

              <div class="cams-wizard-actions">
                <button type="button" class="btn cams-btn-secondary" data-prev-step="3"><i class="fa fa-arrow-left"></i> Back</button>
                <div class="cams-next-wrap"><button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Confirm appointment</button></div>
              </div>
            </section>
          </div>
        </form>
      </div>
    </div>
  </section>
</div>

<footer class="main-footer">
  <strong>CAMS</strong> · Clinic Appointment Management System
</footer>
</div>


<script src="<?=base_url()?>js/cams-booking.js"></script>
</body>
</html>
