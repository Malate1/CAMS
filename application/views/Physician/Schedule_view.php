<?php
$this->load->view('header/headerPhysician');
$camsSuccess = $this->session->flashdata('SUCCESSMSG') ?: $this->session->flashdata('SUCCESSMSG1');
$camsError = $this->session->flashdata('error') ?: $this->session->flashdata('errormsg1');
$this->session->unset_userdata('SUCCESSMSG');
$this->session->unset_userdata('SUCCESSMSG1');
$this->session->unset_userdata('error');
$this->session->unset_userdata('errormsg1');
?>
<link rel="stylesheet" href="<?=base_url()?>assets/plugins/sweetalert2/sweetalert2.min.css">
<link rel="stylesheet" href="<?=base_url()?>assets/plugins/toastr/toastr.min.css">
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div style="color:green" align="right" id="todaysDate"></div>
    
    <h1><i class="fa fa-calendar" aria-hidden="true"></i>
      Manage Schedule
      <!-- <small>advanced tables</small> -->
  </h1>

</section>

<div id="myModal" class="modal fade cams-edit-modal cams-uniform-modal" role="dialog" aria-labelledby="add-schedule-title">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="<?=base_url('Physician/ScheduleRegister')?>" method="post" data-cams-ajax="true" data-confirm-title="Add this clinic schedule?" data-confirm-text="Please confirm the clinic, duty days, and time before saving." data-confirm-button="Yes, add schedule">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <span class="cams-modal-eyebrow">Clinic availability</span>
          <h4 class="modal-title" id="add-schedule-title">Add Schedule</h4>
          <p class="cams-modal-description">Schedules now belong to a specific physician-clinic assignment.</p>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Clinic <span class="text-danger">*</span></label>
            <select required class="form-control" name="clinic_id">
              <option value="">Select Clinic</option>
              <?php foreach ((isset($clinicAssignments) ? $clinicAssignments : array()) as $assignment): ?>
                <option value="<?=$assignment->clinic_id?>"><?=htmlspecialchars($assignment->name, ENT_QUOTES, 'UTF-8')?><?=!empty($assignment->location) ? ' · '.htmlspecialchars($assignment->location, ENT_QUOTES, 'UTF-8') : ''?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Days of Duty <span class="text-danger">*</span></label>
            <select required class="form-control" name="day">
              <option value="MTW">Monday–Wednesday</option>
              <option value="TTh">Tuesday & Thursday</option>
              <option value="Sat">Saturday</option>
              <option value="Sun">Sunday</option>
              <option value="MWF">Monday, Wednesday & Friday</option>
              <option value="ThF">Thursday & Friday</option>
              <option value="M-F">Monday–Friday</option>
            </select>
          </div>

          <div class="cams-modal-form-row">
            <div class="form-group">
              <label>Time-in <span class="text-danger">*</span></label>
              <input type="time" class="form-control" name="time_in" required>
            </div>
            <div class="form-group">
              <label>Time-out <span class="text-danger">*</span></label>
              <input type="time" class="form-control" name="time_out" required>
            </div>
          </div>
        </div>
        <div class="modal-footer cams-standard-form-footer">
          <button style="color: white" type="submit" class="btn btn-primary" value="Submit"><i class="fa fa-save"></i> Submit</button>
          <button style="color: white" type="reset" class="btn btn-danger" value="Reset"><i class="fa fa-close"></i> Reset</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Main content -->
<section class="content">
  <div class="row">
            <div class="col-xs-12 text-left">
                <div class="form-group">
                    <a class="btn btn-primary" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus" aria-hidden="true"></i> Add Schedule</a>
                </div>
            </div>
        </div>
        <div class="row">
          <div class="col-xs-12">
            <div class="box box-success">
              <div class="box-header">
                <h3 class="box-title">Schedule Record List</h3>
            </div>

             <!-- <div class="card-block m-t-35" id="user_body">
                                
              <?php if($this->session->flashdata('SUCCESSMSG')) { ?>
                <div role="alert" class="alert alert-success">
                  <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                  <strong>Well done!!</strong> <?=$this->session->flashdata('SUCCESSMSG')?>
                </div>
              <?php } ?>                         
          </div>  -->


          
          <div class="box-body table-responsive">
              <table class="table cams-modern-table" id="cams-management-table" width="100%" data-cams-server="true" data-kind="schedule" data-source="<?=base_url('table-data/physician-schedules')?>" data-edit-base="<?=base_url('schedule-p-edit')?>" data-flash-success="<?=htmlspecialchars((string)$camsSuccess, ENT_QUOTES, 'UTF-8')?>" data-flash-error="<?=htmlspecialchars((string)$camsError, ENT_QUOTES, 'UTF-8')?>">
                <thead>
                  <tr role="row">
                    <th >Schedule ID</th>
                    <th >Clinic</th>
                    <th >Days of Duty</th>
                    <th >Time-in </th>
                    <th >Time-out </th>
                    <th >Actions</th>
                </tr>
            </thead>
            <tbody>
              <?php
              if(!empty($getSchedule))
              {
                foreach ($getSchedule as $value)
                  { ?>
                    <tr>
                      <td style="display: none;"><?=$value->schedule_id?></td>
                      <td><?=$value->schedule_id?></td>
                      <td><?=$value->day?></td>
                      <td><?=date("h:i A", strtotime($value->time_in))?></td>
                      <td><?=date("h:i A", strtotime($value->time_out))?></td>

                      

                      <td>   
                        <a class="btn btn-warning swalDefaultSuccess"  onclick="return confirmDialogEdit();" href = "<?php echo base_url() . "/schedule-p-edit/" . $value->schedule_id; ?>"><i class="fa fa-edit"></i> Edit </a>
                    </td>
                </tr>   
            <?php }
        }
        ?>
    </tbody>
    
</table>
</div>
<!-- /.box-body -->
</div>
<!-- /.box -->
</div>
<!-- /.col -->
</div>
<!-- /.row -->
</section>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->
<footer class="main-footer">
  <div class="pull-right hidden-xs">
    
  </div>
  <strong>Copyright &copy; 2018 </strong> All rights
  reserved.
</footer>

</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->

<!-- Bootstrap 3.3.7 -->

<!-- <script src="<?=base_url()?>assets/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
  <script src="<?=base_url()?>assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script> -->
  <!-- SlimScroll -->
  <!-- FastClick -->
  <!-- AdminLTE App -->
  <!-- AdminLTE for demo purposes -->
  <!-- <script type="text/javascript" src="<?=base_url()?>vendors/select2/js/select2.js"></script> Maguba ag buttons sa print-->

  <script type="text/javascript" src="<?=base_url()?>vendors/datatables/js/jquery.dataTables.min.js"></script>
  <script type="text/javascript" src="<?=base_url()?>vendors/datatables/js/dataTables.responsive.min.js"></script>
  <script type="text/javascript" src="<?=base_url()?>vendors/datatables/js/dataTables.buttons.min.js"></script>
  <script type="text/javascript" src="<?=base_url()?>vendors/datatables/js/buttons.colVis.min.js"></script>
  <script type="text/javascript" src="<?=base_url()?>vendors/datatables/js/buttons.html5.min.js"></script>
  <script type="text/javascript" src="<?=base_url()?>vendors/datatables/js/buttons.print.min.js"></script>
  <script type="text/javascript" src="<?=base_url()?>js/pages/users.js"></script>  
  <script src="<?=base_url()?>assets/js/password.js"></script>
  <script>
    $(document).ready(function() {

    });
</script>
<!-- page script -->
<script>
    function confirmDialog() {
      return confirm("Are you sure you want to delete this record?")
  }
</script>

<script>
    function confirmDialogEdit() {
      return confirm("Are you sure you want to edit this record?")
  }
</script>



<script type="text/javascript">

    <?php if($this->session->flashdata('SUCCESSMSG')) { ?>
      var newpass = <?php $this->input->post('newPassword') ?>
      $(function() {


        const Toast = Swal.mixin({
          toast: false,
          position: 'top',
          showConfirmButton: false,
          timer: 3000
      });

        $('.swalDefaultSuccess').fadeIn(function() {
          Toast.fire({

            type: 'success',
            title: 'Schedule Succcessfully Updated '
        })
      });
        $('.swalDefaultInfo').click(function() {
          Toast.fire({
            type: 'info',
            title: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'
        })
      });
        $('.swalDefaultError').click(function() {
          Toast.fire({
            type: 'error',
            title: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'
        })
      });
        $('.swalDefaultWarning').click(function() {
          Toast.fire({
            type: 'warning',
            title: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'
        })
      });
        $('.swalDefaultQuestion').click(function() {
          Toast.fire({
            type: 'question',
            title: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'
        })
      });

        $('.toastrDefaultSuccess').fadeIn(function() {
          toastr.success('Password Succcessfully Updated')
      });
        $('.toastrDefaultInfo').click(function() {
          toastr.info('Lorem ipsum dolor sit amet, consetetur sadipscing elitr.')
      });
        $('.toastrDefaultError').click(function() {
          toastr.error('Lorem ipsum dolor sit amet, consetetur sadipscing elitr.')
      });
        $('.toastrDefaultWarning').click(function() {
          toastr.warning('Lorem ipsum dolor sit amet, consetetur sadipscing elitr.')
      });
    });
  <?php } ?>    
</script>

</body>
</html>
