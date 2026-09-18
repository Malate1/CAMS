<?php
       $this->load->view('header/headerSec');
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
    
      <h1><i class="fa fa-home" aria-hidden="true"></i>
        Manage Clinic 
        <!-- <small>advanced tables</small> -->
      </h1>
      
    </section>

    

    <!-- Main content -->
    <section class="content">
      
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
            </div> --> 


            
            <div class="box-body table-responsive">
              <table class="table cams-modern-table" id="cams-management-table" width="100%" data-cams-server="true" data-kind="clinic" data-source="<?=base_url('table-data/secretary-clinics')?>" data-edit-base="<?=base_url('clinic-s-edit')?>" data-flash-success="<?=htmlspecialchars((string)$camsSuccess, ENT_QUOTES, 'UTF-8')?>" data-flash-error="<?=htmlspecialchars((string)$camsError, ENT_QUOTES, 'UTF-8')?>">
                <thead>
                <tr role="row">
                  <!-- <th style="display: none;">id</th> -->
                  <th >Clinic ID</th>
                  <th >Clinic Name</th>
                  <th >Specialization(s)</th>
                  <th >Contact No.</th>                                  
                  <th >Location</th>
                  <th >Actions</th>
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
                        
                        
                        <td><?=$value->contact?></td>
                        <td><?=$value->location?></td>

                        <td>
                        <a class="btn btn-warning swalDefaultSuccess" style="color: #ffff" onclick="return confirmDialogEdit();" href = "<?php echo base_url() . "/clinic-s-edit/" . $value->clinic_id; ?>">Edit <i class="fa fa-edit"></i></a>

                                                           
                        <!-- <a class="btn btn-danger" onclick="return confirmDialog();" href = "<?php echo base_url("Secretary/ClinicDelete/$value->clinic_id"); ?>">Delete <i class="fa fa-trash"></a> --></td>
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
              title: 'Clinic Record Succcessfully Updated '
          })
        });
          
      });
    <?php } ?>    
</script>

</body>
</html>
