<?php
$this->load->view('header/headerPhysician');
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
    
    <h1><i class="fa fa-clipboard" aria-hidden="true"></i>
      Manage Logs 
      <!-- <small>advanced tables</small> -->
    </h1>
    
  </section>
  <style type="text/css">

    @media print {
     table td:last-child, 
     table th:last-child {display:show}


   }
 </style>

 <!-- Main content -->
 <section class="content">
       <!-- <div class="row">
            <div class="col-xs-12 text-left">
                <div class="form-group">
                    <a class="btn btn-primary" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus" aria-hidden="true"></i> Add Clinic</a>
                </div>
            </div>
          </div>  -->
          <div class="row">
            <div class="col-xs-12">
              <div class="box box-success">
                <div class="box-header">
                  <h3 class="box-title">Logs Record List</h3>
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
              <table class="table cams-modern-table" id="cams-management-table" width="100%" data-cams-server="true" data-kind="logs" data-source="<?=base_url('table-data/physician-logs')?>" data-flash-success="<?=htmlspecialchars((string)$camsSuccess, ENT_QUOTES, 'UTF-8')?>" data-flash-error="<?=htmlspecialchars((string)$camsError, ENT_QUOTES, 'UTF-8')?>">
                <thead>
                  <tr role="row">
                    <!-- <th style="display: none;">id</th> -->
                    <th style="text-align: center;"> ID</th> 
                    <th style="text-align: center;">Date/Time</th>
                    <th style="text-align: center;">User Type</th>
                    <th style="text-align: center;">Action Taken</th>
                    
                  </tr>
                </thead>
                <tbody>
                  <?php
                  if(!empty($getLogs))
                  {
                    foreach ($getLogs as $value)
                      { ?>
                        <tr style="text-align: center;">


                          <!-- <td style="display: none;"><?=$value->clinic_id?></td> -->
                          
                          <td><?=$value->id?></td> 
                          <td style="text-align: center;"><?=date("m-d-Y | h:i:s A", strtotime($value->date))?></td>
                          
                          <td><?=$value->usertype?></td>                     
                          <td style="text-align: left;"><?=$value->action?></td>
                        <!--<td><?=$value->location?></td>
                         <td><?=$value->day?></td>
                        <td><?=$value->time_in?></td>
                        <td><?=$value->time_out?></td> -->
                        <!--<td>
                         <a class="btn btn-warning swalDefaultSuccess" style="color: #ffff" onclick="return confirmDialogEdit();" href = "<?php echo base_url() . "/clinic-a-edit/" . $value->clinic_id; ?>">Edit <i class="fa fa-edit"></i></a>

                         <a class="btn btn-warning" href = "<?php echo base_url() . "/clinic-p-edit/" . $value->clinic_id; ?>" title="Edit" onclick="edit_person('$value->clinic_id')">Edit <i class="fa fa-edit"></i> </a> -->
                         
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
  
  <!-- Bootstrap 3.3.7 -->

<!-- <script src="<?=base_url()?>assets/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
  <script src="<?=base_url()?>assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script> -->
  <!-- SlimScroll -->
  <!-- FastClick -->
  <!-- AdminLTE App -->
  <!-- AdminLTE for demo purposes -->
  <script src="<?=base_url()?>assets/js/password.js"></script>
  <!-- <script type="text/javascript" src="<?=base_url()?>vendors/select2/js/select2.js"></script> Maguba ag buttons sa print-->


  <script type="text/javascript" src="<?=base_url()?>vendors/datatables/js/jquery.dataTables.min.js"></script>
  <script type="text/javascript" src="<?=base_url()?>vendors/datatables/js/dataTables.responsive.min.js"></script>
  <script type="text/javascript" src="<?=base_url()?>vendors/datatables/js/dataTables.buttons.min.js"></script>
  <script type="text/javascript" src="<?=base_url()?>vendors/datatables/js/buttons.colVis.min.js"></script>
  <script type="text/javascript" src="<?=base_url()?>vendors/datatables/js/buttons.html5.min.js"></script>
  <script type="text/javascript" src="<?=base_url()?>vendors/datatables/js/buttons.print.min.js"></script>
  <script type="text/javascript" src="<?=base_url()?>js/pages/users.js"></script>  

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
