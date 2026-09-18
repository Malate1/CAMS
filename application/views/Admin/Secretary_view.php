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
      Manage Secretary Accounts
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
          <h3 class="box-title">Secretary Record List</h3>
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
      <table class="table cams-modern-table" id="cams-management-table" width="100%" data-cams-server="true" data-kind="user" data-source="<?=base_url('table-data/admin-secretaries')?>" data-edit-base="<?=base_url('secretary-a-edit')?>" data-image-base="<?=base_url('uploads/profile-pic')?>" data-flash-success="<?=htmlspecialchars((string)$camsSuccess, ENT_QUOTES, 'UTF-8')?>" data-flash-error="<?=htmlspecialchars((string)$camsError, ENT_QUOTES, 'UTF-8')?>">
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
      if(!empty($getSecretary))
      {
        foreach ($getSecretary as $value)
          { ?>
            <tr>
              <!-- <td style="display: none;"><?=$value->secretary_id?></td> -->

              <td><?=$value->secretary_id?></td>
              <td><?=$value->fname?></td>
              <td><?=$value->mname?></td>
              <td><?=$value->lname?></td>
              <td><?=$value->contact?></td>
              <td><?=$value->address?></td>
              <td><?=$value->email?></td>
              <td align="center"><img src="<?php echo base_url(); ?>uploads/profile-pic/<?=$value->image?>" class="img-circle" style="height: 45px; width: 48px;" align="center" alt="User Image"></td>
                        <!-- <td>Dr. <?=$value->lname?></td>
                          <td><?=$value->password?></td> -->
                          <td>

                            <a class="btn btn-warning swalDefaultSuccess" style="color: #ffff" onclick="return confirmDialogEdit();" href = "<?php echo base_url() . "/secretary-a-edit/" . $value->secretary_id; ?>"><i class="fa fa-edit"></i> Update Password </a>    
                        <!-- <form class="form-inline" method="post" action = "Admin/UpdateStatusS">
                            <input type="hidden" name="secretary_id" value="<?php echo $value->secretary_id;?>">
                            <div class="form-group mx-sm-3 mb-2">
                            <select class="form-control" id="exampleSelect1" name = "status">
                                  <option value="Inactive">Deactivate</option>
                                  <option value="Active">Activate</option>
                                  
                               </select>
                          
                                <button type="submit" class="btn btn-primary mb-2 ml-2 mt-2">Update</button>
                          </div>                         
                                           
                      </form> -->
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
</body>
</html>
