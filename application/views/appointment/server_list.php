<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$appointmentRole = isset($appointmentRole) ? $appointmentRole : 'patient';
$appointmentScope = isset($appointmentScope) ? $appointmentScope : 'all';
$appointmentDate = isset($appointmentDate) ? $appointmentDate : '';

$config = array(
    'patient' => array(
        'header' => 'header/headerPatient',
        'book' => 'app-register',
        'data' => 'appointments-data-patient',
        'edit' => 'appointment-edit-patient',
        'status' => 'appointment-status-patient',
        'availability' => 'appointment-availability',
        'all' => 'view-appointment',
        'today' => 'view-app-pto',
        'done' => 'view-app-do',
        'cancelled' => 'view-app-ca',
    ),
    'physician' => array(
        'header' => 'header/headerPhysician',
        'book' => 'app-register-p',
        'data' => 'appointments-data-physician',
        'edit' => 'appointment-edit-physician',
        'status' => 'appointment-status-physician',
        'availability' => 'appointment-availability-p',
        'all' => 'view-app-p',
        'today' => 'view-app-pt',
        'done' => 'view-app-pd',
        'cancelled' => 'view-app-pc',
    ),
    'secretary' => array(
        'header' => 'header/headerSec',
        'book' => 'app-register-s',
        'data' => 'appointments-data-secretary',
        'edit' => 'appointment-edit-secretary',
        'status' => 'appointment-status-secretary',
        'availability' => 'appointment-availability-s',
        'all' => 'view-app-s',
        'today' => 'view-app-st',
        'done' => 'view-app-sdone',
        'cancelled' => 'view-app-sc',
    ),
);
$roleConfig = $config[$appointmentRole];
$this->load->view($roleConfig['header']);

$scopeLabels = array(
    'all' => 'All appointments',
    'today' => "Today's appointments",
    'done' => 'Completed appointments',
    'cancelled' => 'Cancelled appointments',
    'date' => $appointmentDate !== '' ? 'Appointments for ' . date('M j, Y', strtotime($appointmentDate)) : 'Appointments by date',
);
$pageSubtitle = isset($scopeLabels[$appointmentScope]) ? $scopeLabels[$appointmentScope] : 'All appointments';
$flashSuccess = $this->session->flashdata('SUCCESSMSG') ?: $this->session->flashdata('SUCCESSMSG1');
$flashError = $this->session->flashdata('error') ?: $this->session->flashdata('error1');
?>

<div class="content-wrapper cams-page-shell">
  <section class="content-header cams-page-heading">
    <div>
      <span class="cams-page-eyebrow">Appointments</span>
      <h1>Manage Appointments</h1>
      <p><?=htmlspecialchars($pageSubtitle, ENT_QUOTES, 'UTF-8')?></p>
    </div>
    <div class="cams-page-actions">
      <a class="btn btn-primary cams-primary-action" href="<?=base_url($roleConfig['book'])?>">
        <i class="fa fa-plus"></i> Book Appointment
      </a>
    </div>
  </section>

  <section class="content cams-page-content">
    <div class="cams-filter-tabs" aria-label="Appointment filters">
      <a class="<?= $appointmentScope === 'all' || $appointmentScope === 'date' ? 'active' : '' ?>" href="<?=base_url($roleConfig['all'])?>">All</a>
      <a class="<?= $appointmentScope === 'today' ? 'active' : '' ?>" href="<?=base_url($roleConfig['today'])?>">Today</a>
      <a class="<?= $appointmentScope === 'done' ? 'active' : '' ?>" href="<?=base_url($roleConfig['done'])?>">Done</a>
      <a class="<?= $appointmentScope === 'cancelled' ? 'active' : '' ?>" href="<?=base_url($roleConfig['cancelled'])?>">Cancelled</a>
    </div>

    <div class="box cams-data-card">
      <div class="box-header cams-data-card-header">
        <div>
          <h3 class="box-title">Appointment Records</h3>
          <p>Search, review, edit, or update appointment status without reloading the page.</p>
        </div>
        <div class="cams-live-indicator"><span></span> Live server data</div>
      </div>
      <div class="box-body">
        <div
          id="cams-appointment-app"
          data-role="<?=htmlspecialchars($appointmentRole, ENT_QUOTES, 'UTF-8')?>"
          data-scope="<?=htmlspecialchars($appointmentScope, ENT_QUOTES, 'UTF-8')?>"
          data-date="<?=htmlspecialchars($appointmentDate, ENT_QUOTES, 'UTF-8')?>"
          data-data-url="<?=htmlspecialchars(base_url($roleConfig['data']), ENT_QUOTES, 'UTF-8')?>"
          data-edit-url="<?=htmlspecialchars(base_url($roleConfig['edit']), ENT_QUOTES, 'UTF-8')?>"
          data-status-url="<?=htmlspecialchars(base_url($roleConfig['status']), ENT_QUOTES, 'UTF-8')?>"
          data-availability-url="<?=htmlspecialchars(base_url($roleConfig['availability']), ENT_QUOTES, 'UTF-8')?>"
          data-csrf-name="<?=htmlspecialchars($this->security->get_csrf_token_name(), ENT_QUOTES, 'UTF-8')?>"
          data-csrf-hash="<?=htmlspecialchars($this->security->get_csrf_hash(), ENT_QUOTES, 'UTF-8')?>"
          data-flash-success="<?=htmlspecialchars((string) $flashSuccess, ENT_QUOTES, 'UTF-8')?>"
          data-flash-error="<?=htmlspecialchars((string) $flashError, ENT_QUOTES, 'UTF-8')?>"
        >
          <div class="table-responsive cams-table-wrap">
            <table class="table cams-modern-table" id="cams-appointments-table" width="100%">
              <thead>
                <tr>
                  <th>Appointment ID</th>
                  <th>Date</th>
                  <th>Purpose</th>
                  <?php if ($appointmentRole === 'patient'): ?>
                    <th>Queue</th>
                    <th>Physician</th>
                  <?php else: ?>
                    <th>Patient</th>
                  <?php endif; ?>
                  <th>Clinic</th>
                  <th>Status</th>
                  <?php if ($appointmentRole !== 'patient'): ?><th>Queue</th><?php endif; ?>
                  <th class="text-right">Actions</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<div class="modal fade cams-edit-modal" id="cams-edit-appointment-modal" tabindex="-1" role="dialog" aria-labelledby="cams-edit-title">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="cams-edit-appointment-form">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <span class="cams-modal-eyebrow">Update booking</span>
          <h4 class="modal-title" id="cams-edit-title">Edit Appointment</h4>
          <p>Only pending appointments can be changed. Changing the date may assign a new queue number.</p>
        </div>
        <div class="modal-body">
          <input type="hidden" name="appointment_id" id="cams-edit-id">
          <div class="cams-edit-summary">
            <div><span>Patient</span><strong id="cams-edit-patient">—</strong></div>
            <div><span>Physician</span><strong id="cams-edit-physician">—</strong></div>
            <div><span>Clinic</span><strong id="cams-edit-clinic">—</strong></div>
            <div><span>Schedule</span><strong id="cams-edit-schedule">—</strong></div>
          </div>
          <div class="form-group">
            <label for="cams-edit-purpose">Reason / chief complaint</label>
            <input class="form-control" type="text" id="cams-edit-purpose" name="purpose" maxlength="100" required>
          </div>
          <div class="form-group">
            <label for="cams-edit-date">Appointment date</label>
            <select class="form-control" id="cams-edit-date" name="app_date" required>
              <option value="">Loading available dates…</option>
            </select>
            <p class="help-block">Only dates matching the physician's configured schedule are shown.</p>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="cams-save-appointment"><i class="fa fa-check"></i> Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<footer class="main-footer cams-footer">
  <strong>CAMS</strong> <span>Clinic Appointment Management System</span>
</footer>
</div>


<script src="<?=base_url()?>vendors/datatables/js/jquery.dataTables.min.js"></script>
<script src="<?=base_url()?>vendors/datatables/js/dataTables.responsive.min.js"></script>
<script src="<?=base_url()?>vendors/datatables/js/dataTables.buttons.min.js"></script>
<script src="<?=base_url()?>vendors/datatables/js/buttons.html5.min.js"></script>
<script src="<?=base_url()?>vendors/datatables/js/buttons.print.min.js"></script>
<script src="<?=base_url()?>js/cams-appointments-table.js?v=<?=@filemtime(FCPATH.'js/cams-appointments-table.js')?>"></script>
</body>
</html>
