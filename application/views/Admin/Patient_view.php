<?php
$this->load->view('header/header');
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
    
    <h1><i class="fa fa-users" aria-hidden="true"></i>
      Manage Patient Accounts
      <!-- <small>advanced tables</small> -->
  </h1>

</section>
<style type="text/css">
  
  @media print {
     table td:last-child {display:none}
     table th:last-child {display:none}
 }
</style>



<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-xs-12">
      <div class="box box-success">
        <div class="box-header">
          <h3 class="box-title">Patient Record List</h3>
      </div>

      <div  class="card-center"  style="padding-right: 400px; padding-left: 400px;">
          
          <?php if($this->session->flashdata('SUCCESSMSG')) { ?>
            <div role="alert" class="alert alert-success">
              <button data-dismiss="alert" class="close" type="button">&times;<span class="sr-only">Close</span></button>
              <?=$this->session->flashdata('SUCCESSMSG')?>
          </div>
      <?php } ?>                         
  </div> 

  <div class="box-body table-responsive">
      <table class="table cams-modern-table" id="cams-management-table" width="100%" data-cams-server="true" data-kind="user" data-source="<?=base_url('table-data/admin-patients')?>" data-edit-base="<?=base_url('patient-a-edit')?>" data-image-base="<?=base_url('uploads/profile-pic')?>" data-flash-success="<?=htmlspecialchars((string)$camsSuccess, ENT_QUOTES, 'UTF-8')?>" data-flash-error="<?=htmlspecialchars((string)$camsError, ENT_QUOTES, 'UTF-8')?>">
        <thead>
          <tr role="row">
            <!-- <th style="display: none;">id</th> -->
            <th >ID</th>
            <th >First Name</th>
            <th >Middle Name</th>
            <th >Last Name</th>
            <th >Contact</th>
            <th >Home Address</th>                                  
            <th >Email Address</th>
            <th >Image</th>
            <th >Update Password</th>
        </tr>
    </thead>
    <tbody>
      <?php
      if(!empty($getPatient))
      {
        foreach ($getPatient as $value)
          { ?>
            <tr>
              <!-- <td style="display: none;"><?=$value->patient_id?></td> -->

              <td><?=$value->patient_id?></td>
              <td><?=$value->fname?></td>
              <td><?=$value->mname?></td>
              <td><?=$value->lname?></td>
              <td><?=$value->contact?></td>
              <td><?=$value->address?></td>
              <td><?=$value->email?></td>
              <td align="center"><img src="<?php echo base_url(); ?>uploads/profile-pic/<?=$value->image?>" class="img-circle" style="height: 45px; width: 48px;" align="center" alt="User Image"></td>
              <td> 

                <a class="btn btn-warning swalDefaultSuccess" style="color: #ffff" onclick="return confirmDialogEdit();" href = "<?php echo base_url() . "/patient-a-edit/" . $value->patient_id; ?>"><i class="fa fa-edit"></i> Update Password </a>

                        <!-- <form class="form-inline" method="post" action = "Admin/UpdateStatusP">
                            <input type="hidden" name="patient_id" value="<?php echo $value->patient_id;?>">
                            <div class="form-group mx-sm-3 mb-2">
                            <select class="form-control" id="exampleSelect1" name = "status">
                                  <option value="Inactive">Deactivate</option>
                                  <option value="Active">Activate</option>
                                  
                               </select>
                          
                                <button type="sub  mit" class="btn btn-primary mb-2 ml-2 mt-2">Update</button>
                          </div>                         
                                           
                      </form> -->


                      <!-- Edit -->
                      <div class="modal fade" id="editmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <center><h4 class="modal-title" id="myModalLabel">Update Password</h4></center>
                                </div>
                                <div class="modal-body">
                                  <div class="container-fluid">
                                      <form id="editForm">
                                        <div class="row">
                                          <div class="col-md-3">
                                            <label class="control-label" style="position:relative; top:7px;">Email:</label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" name="email" id="email">
                                        </div>
                                    </div>
                                    <div style="height:10px;"></div>
                                    <div class="row">
                                      <div class="col-md-3">
                                        <label class="control-label" style="position:relative; top:7px;">Password:</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="password" id="password">
                                    </div>
                                </div>
                                <div style="height:10px;"></div>
                                <div class="row">
                                  <div class="col-md-3">
                                    <label class="control-label" style="position:relative; top:7px;">Full Name:</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="fname" id="fname">
                                </div>
                            </div>
                            <input type="hidden" name="id" id="userid">
                        </div> 
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancel</button>
                        <button type="submit" class="btn btn-warning"><span class="glyphicon glyphicon-check"></span> Update</a>
                        </form>
                    </div>
                    
                </div>
            </div>
        </div>


        
    </td>
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
<script src="<?=base_url()?>assets/bower_components/chart.js/Chart.min.js"></script>
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
<script>
    @media print {
       table td:last-child {display:none}
       table th:last-child {display:none}
   }
</script>
<!-- page script -->
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
                title: 'Password Succcessfully Updated '
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

 <!--  <script type="text/javascript">
      $(document).ready(function(){
        //create a global variable for our base url
        var url = '<?php echo base_url(); ?>';

        //fetch table data
        showTable();

        //show add modal
        $('#add').click(function(){
          $('#addnew').modal('show');
          $('#addForm')[0].reset();
        });

        //submit add form
        $('#addForm').submit(function(e){
          e.preventDefault();
          var user = $('#addForm').serialize();
            $.ajax({
              type: 'POST',
              url: url + 'user/insert',
              data: user,
              success:function(){
                $('#addnew').modal('hide');
                showTable();
              }
            });
        });

        //show edit modal
        $(document).on('click', '.edit', function(){
          var id = $(this).data('id');
          $.ajax({
            type: 'POST',
            url: url + 'user/getuser',
            dataType: 'json',
            data: {id: id},
            success:function(response){
              console.log(response);
              $('#email').val(response.email);
              $('#password').val(response.password);
              $('#fname').val(response.fname);
              $('#userid').val(response.id);
              $('#editmodal').modal('show');
            }
          });
        });

        //update selected user
        $('#editForm').submit(function(e){
          e.preventDefault();
          var user = $('#editForm').serialize();
          $.ajax({
            type: 'POST',
            url: url + 'user/update',
            data: user,
            success:function(){
              $('#editmodal').modal('hide');
              showTable();
            }
          });
        });

        //show delete modal
        $(document).on('click', '.delete', function(){
          var id = $(this).data('id');
          $.ajax({
            type: 'POST',
            url: url + 'user/getuser',
            dataType: 'json',
            data: {id: id},
            success:function(response){
              console.log(response);
              $('#delfname').html(response.fname);
              $('#delid').val(response.id);
              $('#delmodal').modal('show');
            }
          });
        });

        $('#delid').click(function(){
          var id = $(this).val();
          $.ajax({
            type: 'POST',
            url: url + 'user/delete',
            data: {id: id},
            success:function(){
              $('#delmodal').modal('hide');
              showTable();
            }
          });
        });

      });

      function showTable(){
        var url = '<?php echo base_url(); ?>';
        $.ajax({
          type: 'POST',
          url: url + 'user/show',
          success:function(response){
            $('#tbody').html(response);
          }
        });
      }
  </script> -->

  

</body>
</html>
