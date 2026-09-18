<?php
$this->load->view('header/headerPatient');
?>
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div style="color:green" align="right" id="todaysDate"></div>
    
    <h1><i class="fa fa-users" aria-hidden="true"></i>
      Manage Accounts
      <!-- <small>advanced tables</small> -->
  </h1>
</section>

<section class="content">

  <div class="row">
    <!-- left column -->
    <div class="col-md-10">
      <!-- general form elements -->
      <!-- <?php if ($this->session->flashdata('success')) { ?>
        <div role="alert" class="alert alert-success">
         <button data-dismiss="alert" class="close" type="button">
           <span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
           <strong>Well done!</strong>
           <?= $this->session->flashdata('success') ?>
         </div>
       <?php } ?>

       <?php if($this->session->flashdata('errormsg')) { ?>
            <div role="alert" class="alert alert-danger">
              <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
              <?=$this->session->flashdata('errormsg')?>
          </div>
          <?php } ?> -->


          <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title">Update Password</h3>
          </div><!-- /.box-header -->
          <!-- form start -->

          <form role="form" action="<?php echo base_url() ?>Patient/Change_pass" method="post" role="form">
              <div class="box-body">
                <div class="row">
                    <div class="col-md-6">   
                        <div class="form-group">
                            <label>Old Password</label>
                            <div class="input-group" id="show_hide_Opassword">
                                <input class="form-control" type="password" id="oldPassword" name ="oldPassword" required>
                                <div class="input-group-addon">
                                    <a style="color: #333"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>

                <div class="row">
                    <div class="col-md-6">                             
                        <div class="form-group">
                            <label for="newPassword">New Password</label>
                            <div class="input-group" id="show_hide_Npassword">
                                <input name ="newPassword" class="form-control" type="password" id="pass" required>
                                <div class="input-group-addon">
                                    <a style="color: #333;"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                                </div>
                            </div>
                            <div id="meter_wrapper"> 
                                <br>
                                <div id="meter"></div>

                                <span style="color: red" id="pass_type"></span>
                            </div>     
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">                                                                 
                        <div class="form-group">
                            <label for="cNewPassword">Confirm New Password</label>
                            <div class="input-group" id="show_hide_Cpassword">
                                <input type="password" class="form-control" id="cNewPassword" name ="cNewPassword" required> 
                                <div class="input-group-addon">
                                    <a style="color: #333"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



            </div><!-- /.box-body -->

            <div class="box-footer">
              <button onclick="return Validate()" style="color: white" type="submit" class="btn btn-primary swalDefaultSuccess swalDefaultError"  value="Submit" /><i class = "fa fa-save"></i> Submit</button> 

              <button style="color: white" type="reset" class="btn btn-danger"  value="Reset" /><i class = "fa fa-close"></i> Reset</button>
          </div>
      </form>
  </div>
</div>

</div>    
</section>

</div>

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



  <script type="text/javascript">

    <?php if($this->session->flashdata('success')) { ?>
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
                title: 'Password Succcessfully Updated '
            })
          });

        });
    <?php } ?>

    <?php if($this->session->flashdata('errormsg')) { ?>
        var newpass = <?php $this->input->post('newPassword') ?>
        $(function() {


          const Toast = Swal.mixin({
            toast: false,
            position: 'top',
            showConfirmButton: false,
            timer: 5000
        });

          
          $('.swalDefaultError').fadeIn(function() {
            Toast.fire({
              type: 'error',
              title: 'Old Password is Incorrect'
              
          })
        });
          

          
      });
    <?php } ?>       
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $("#show_hide_Opassword a").on('click', function(event) {
            event.preventDefault();
            if($('#show_hide_Opassword input').attr("type") == "text"){
                $('#show_hide_Opassword input').attr('type', 'password');
                $('#show_hide_Opassword i').addClass( "fa-eye-slash" );
                $('#show_hide_Opassword i').removeClass( "fa-eye" );
            }else if($('#show_hide_Opassword input').attr("type") == "password"){
                $('#show_hide_Opassword input').attr('type', 'text');
                $('#show_hide_Opassword i').removeClass( "fa-eye-slash" );
                $('#show_hide_Opassword i').addClass( "fa-eye" );
            }
        });
        $("#show_hide_Npassword a").on('click', function(event) {
            event.preventDefault();
            if($('#show_hide_Npassword input').attr("type") == "text"){
                $('#show_hide_Npassword input').attr('type', 'password');
                $('#show_hide_Npassword i').addClass( "fa-eye-slash" );
                $('#show_hide_Npassword i').removeClass( "fa-eye" );
            }else if($('#show_hide_Npassword input').attr("type") == "password"){
                $('#show_hide_Npassword input').attr('type', 'text');
                $('#show_hide_Npassword i').removeClass( "fa-eye-slash" );
                $('#show_hide_Npassword i').addClass( "fa-eye" );
            }
        });
        $("#show_hide_Cpassword a").on('click', function(event) {
            event.preventDefault();
            if($('#show_hide_Cpassword input').attr("type") == "text"){
                $('#show_hide_Cpassword input').attr('type', 'password');
                $('#show_hide_Cpassword i').addClass( "fa-eye-slash" );
                $('#show_hide_Cpassword i').removeClass( "fa-eye" );
            }else if($('#show_hide_Cpassword input').attr("type") == "password"){
                $('#show_hide_Cpassword input').attr('type', 'text');
                $('#show_hide_Cpassword i').removeClass( "fa-eye-slash" );
                $('#show_hide_Cpassword i').addClass( "fa-eye" );
            }
        });
    });
</script>

</body>
</html>