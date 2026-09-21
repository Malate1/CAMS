<?php
$this->load->view('header/header');
$camsSuccess = $this->session->flashdata('SUCCESSMSG') ?: $this->session->flashdata('SUCCESSMSG1');
$camsError = $this->session->flashdata('error') ?: $this->session->flashdata('errormsg1');
$this->session->unset_userdata('SUCCESSMSG');
$this->session->unset_userdata('SUCCESSMSG1');
$this->session->unset_userdata('error');
$this->session->unset_userdata('errormsg1');
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div style="color:green" align="right" id="todaysDate"></div>

    <h1><i class="fa fa-heartbeat" aria-hidden="true"></i>
      Manage Clinic 
      <!-- <small>advanced tables</small> -->
  </h1>

</section>
<style type="text/css">

    @media print {
       table td:last-child {display:none}
       table th:last-child {display:none}
   }
</style>

<div id="myModal" class="modal fade cams-edit-modal cams-uniform-modal" role="dialog" aria-labelledby="add-clinic-title">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="<?=base_url('Admin/ClinicRegister')?>" method="post" data-cams-ajax="true" data-confirm-title="Add clinic assignment?" data-confirm-text="Please confirm the clinic, physician, specialization, and secretary assignment before saving." data-confirm-button="Yes, save clinic" data-success-title="Clinic saved" data-transaction-label="clinic" data-refresh-table="#cams-management-table">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <span class="cams-modal-eyebrow">New clinic</span>
          <h4 class="modal-title" id="add-clinic-title">Add Clinic</h4>
          <p class="cams-modal-description">Enter the clinic details, then assign the physician, specialization, and secretary.</p>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Clinic Setup <span class="text-danger">*</span></label>
            <select class="form-control" id="clinic-assignment-mode" name="assignment_mode" required>
              <option value="new">Create a new clinic</option>
              <option value="existing">Assign physician to an existing clinic</option>
            </select>
          </div>

          <div class="form-group" id="clinic-existing-field" style="display:none;">
            <label>Existing Clinic <span class="text-danger">*</span></label>
            <select class="form-control" id="clinic-existing-id" name="existing_clinic_id" disabled>
              <option value="">Select clinic</option>
              <?php foreach ((isset($allClinics) ? $allClinics : array()) as $clinicOption): ?>
                <option value="<?=$clinicOption->clinic_id?>"><?=htmlspecialchars($clinicOption->name, ENT_QUOTES, 'UTF-8')?><?=!empty($clinicOption->location) ? ' · '.htmlspecialchars($clinicOption->location, ENT_QUOTES, 'UTF-8') : ''?></option>
              <?php endforeach; ?>
            </select>
            <p class="help-block">Use this when another physician will also practice at an existing clinic location.</p>
          </div>

          <div id="clinic-new-fields">
          <div class="form-group">
            <label>Clinic Name <span class="text-danger">*</span></label>
            <input required type="text" class="form-control" name="name" autocomplete="organization">
          </div>

          <div class="cams-modal-form-row">
            <div class="form-group">
              <label>BIR / TIN <span class="text-danger">*</span></label>
              <input required type="text" class="form-control" name="bir">
            </div>
            <div class="form-group">
              <label>Business Permit No. <span class="text-danger">*</span></label>
              <input required type="text" class="form-control" name="businessPermit">
            </div>
          </div>

          <div class="cams-modal-form-row">
            <div class="form-group">
              <label>Contact No. <span class="text-danger">*</span></label>
              <input required type="text" class="form-control" name="contact" autocomplete="tel">
            </div>
            <div class="form-group">
              <label>Location <span class="text-danger">*</span></label>
              <input required type="text" class="form-control" name="location" autocomplete="street-address">
            </div>
          </div>
          </div>

          <div class="form-group">
            <label>Physician <span class="text-danger">*</span></label>
            <select required name="physician_id" id="clinic-physician-id" class="form-control">
              <option value="">Select physician</option>
              <?php foreach ($getPhysician as $value) {
                $physicianId = (int) $value->physician_id;
                $physicianSpecials = isset($physicianSpecializations[$physicianId]) ? $physicianSpecializations[$physicianId] : array();
              ?>
                <option
                  value="<?=$physicianId?>"
                  data-specializations="<?=htmlspecialchars(json_encode($physicianSpecials, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8')?>"
                ><?=htmlspecialchars($value->fname.' '.$value->lname, ENT_QUOTES, 'UTF-8')?></option>
              <?php } ?>
            </select>
          </div>

          <div class="form-group">
            <label>Specialization(s) <span class="text-danger">*</span></label>
            <select required name="special_ids[]" id="clinic-special-id" class="form-control" multiple size="4" disabled>
              <option value="">Select physician first</option>
            </select>
            <p class="help-block">Choose one or more services this physician will provide at this clinic. Choices come from the physician's configured specialization(s).</p>
          </div>

          <div class="form-group">
            <label>Secretary <span class="text-danger">*</span></label>
            <select required name="secretary_id" class="form-control">
              <option value="">Select secretary</option>
              <?php foreach ($getSecretary as $value) { ?>
                <option value="<?=$value->secretary_id?>"><?=$value->fname?> <?=$value->lname?></option>
              <?php } ?>
            </select>
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
<script>
window.CAMS_PHYSICIAN_SPECIALIZATIONS = <?=json_encode(isset($physicianSpecializations) ? $physicianSpecializations : array(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)?>;
</script>

<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-xs-12 text-left">
        <div class="form-group">
          <a class="btn btn-primary" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus" aria-hidden="true"></i> Add Clinic</a>
      </div>
  </div>
</div> 
<div class="row">
  <div class="col-xs-12">
    <div class="box box-success">
      <div class="box-header">
        <h3 class="box-title">Clinic Record List</h3>
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
              <table class="table cams-modern-table" id="cams-management-table" width="100%" data-cams-server="true" data-kind="clinic-admin" data-source="<?=base_url('table-data/admin-clinics')?>" data-edit-base="<?=base_url('clinic-a-edit')?>" data-flash-success="<?=htmlspecialchars((string)$camsSuccess, ENT_QUOTES, 'UTF-8')?>" data-flash-error="<?=htmlspecialchars((string)$camsError, ENT_QUOTES, 'UTF-8')?>">
                <thead>
                  <tr role="row">
                    <!-- <th style="display: none;">id</th> -->
                    <th>Clinic ID</th>
                    <th>Clinic Name</th>
                    <th>Physician Name</th>
                    <th>Specialization(s)</th>
                    <th>Secretary</th>
                    <th>Contact No.</th>                                  
                      <th>Location</th>
                  <!-- <th>Day/s</th>
                  <th>Time-in</th>
                  <th>Time-out</th> -->
                  <th>Actions</th>
              </tr>
          </thead>
          <tbody>
            <?php
            if(!empty($getClinic))
            {
              foreach ($getClinic as $value)
                { ?>
                  <tr>
                    <!-- <td style="display: none;"><?=$value->clinic_id?></td> -->

                    <td><?=$value->clinic_id?></td>
                    <td><?=$value->name?></td>
                    <td>Dr. <?=$value->lname?></td>

                    <td><?=$value->contact?></td>
                    <td><?=$value->location?></td>
                        <!-- <td><?=$value->day?></td>
                        <td><?=$value->time_in?></td>
                        <td><?=$value->time_out?></td> -->
                        <td>
                           <a class="btn btn-warning swalDefaultSuccess" style="color: #ffff" onclick="return confirmDialogEdit();" href = "<?php echo base_url() . "/clinic-a-edit/" . $value->clinic_id; ?>"><i class="fa fa-edit"></i> Edit </a>

                           <!-- <a class="btn btn-warning" href = "<?php echo base_url() . "/clinic-p-edit/" . $value->clinic_id; ?>" title="Edit" onclick="edit_person('$value->clinic_id')">Edit <i class="fa fa-edit"></i> </a> -->

                           <!-- <a class="btn btn-danger" onclick="return confirmDialog();" href = "<?php echo base_url("Admin/ClinicDelete/$value->clinic_id"); ?>">Delete <i class="fa fa-trash"></a> --></td>
                           </tr>   
                       <?php   }
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

<script type="text/javascript" src="<?=base_url()?>vendors/datatables/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>vendors/datatables/js/dataTables.responsive.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>vendors/datatables/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>vendors/datatables/js/buttons.colVis.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>vendors/datatables/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>vendors/datatables/js/buttons.print.min.js"></script>

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
          timer: 5000
      });

        $('.swalDefaultSuccess').fadeIn(function() {
          Toast.fire({

            type: 'success',
            title: 'Clinic Succcessfully Updated '
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
