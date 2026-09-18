<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>CAMS | Login </title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <?php $this->load->view('auth/runtime_head'); ?>
  <link rel="stylesheet" href="<?=base_url()?>assets/bower_components/bootstrap/dist/css/bootstrap.min.css"/>
	<link rel="stylesheet" href="<?=base_url()?>assets/bower_components/font-awesome/css/font-awesome.min.css"/>
	<link rel="stylesheet" href="<?=base_url()?>assets/bower_components/Ionicons/css/ionicons.min.css"/>
	<link rel="stylesheet" href="<?=base_url()?>assets/dist/css/AdminLTE.min.css"/>
	<link rel="stylesheet" href="<?=base_url()?>assets/dist/css/skins/_all-skins.min.css"/>
	<link rel="stylesheet" href="<?=base_url()?>assets/plugins/sweetalert2/sweetalert2.min.css"/>
	<link rel="stylesheet" href="<?=base_url()?>assets/plugins/toastr/toastr.min.css"/>
	<link rel="stylesheet" href="<?=base_url()?>css/cams-modern.css?v=<?=@filemtime(FCPATH.'css/cams-modern.css')?>"/>
  <!--End of plugin styles-->
  <!--Page level styles-->
  <!--<link type="text/css" rel="stylesheet" href="<?=base_url()?>css/pages/tables.css" /> Ug naa ni maguba ag pagination pero ug wla ni dili responsive -->
  

     <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
     <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
</head>



<body class="login-page cams-auth">
    <style type="text/css">
        
        body {

            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";

        }
    </style>
  <?php
  $authRole = 'Admin';
  $authHeadline = 'Run the clinic with clarity.';
  $authDescription = 'Access administrative tools for clinics, users, reports, logs and appointment operations.';
  $authBadge = 'Administrator portal';
  $this->load->view('auth/cover_intro', compact('authRole', 'authHeadline', 'authDescription', 'authBadge'));
  ?>
  <div class="login-box">
    <div class="login-logo">
      <a href="#"><img src="<?=base_url()?>assets/dist/img/hnumcfi.jpg" class="img-circle"  alt="logo" style="height: 100px; width: 100px;"><br><h3>Clinics Appointment Management System</h3></a>
  </div><!-- /.login-logo -->
  <div class="login-box-body" style="border-radius: 5px">
      <p class="login-box-msg">Admin Login</p>
      <?php if($this->session->flashdata('errormsg')) { ?>
        <div role="alert" class="alert alert-danger">
          <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
          <?=$this->session->flashdata('errormsg')?>
      </div>
  <?php } ?>

  <?php if($this->session->flashdata('SUCCESSMSG')) { ?>
    <div role="alert" class="alert alert-success">
      <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
      <?=$this->session->flashdata('SUCCESSMSG')?>
  </div>
<?php } ?>

<form action="<?php echo base_url(); ?>Login/logAdmin" method="post">
    <div class="form-group has-feedback">
      <input  type="email" class="form-control" placeholder="Email" name="email" required autofocus />
      <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
  </div>
  <div class="form-group has-feedback">
      <input id="typepass" type="password" class="form-control" placeholder="Password" name="password" required />
      <input type="checkbox" onclick="Toggle()"> 
      <b>Show Password</b> 

      <script> 
            // Change the type of input to password or text 
            function Toggle() { 
              var temp = document.getElementById("typepass"); 
              if (temp.type === "password") { 
                temp.type = "text"; 
            } 
            else { 
                temp.type = "password"; 
            } 
        } 
    </script> 
    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
</div>
<div class="row">
  <div class="col-xs-8">    
              <!-- <div class="checkbox icheck">
                <label>
                  <input type="checkbox"> Remember Me
                </label>
            </div>  -->                       
        </div><!-- /.col -->
        <div class="col-xs-4">
          <input  type="submit" class="btn btn-success btn-block " value="Sign In" />
      </div><!-- /.col -->
  </div>
</form>

<label for="name">Switch Accounts: </label>
<select style="border-radius: 10px" name="form" onchange="location = this.value;">
  <option value="">Select User</option>
  <option value="<?php echo base_url() ?>Login/logAdmin">Admin</option>
  <option value="<?php echo base_url() ?>Login/logSec">Secretary</option>
  <option value="<?php echo base_url() ?>Login/logPatient">Patient</option>
  <option value="<?php echo base_url() ?>Login/logDoctor">Physician</option>
</select>

</div><!-- /.login-box-body -->
<div class="text-muted" style="text-align: center;"><br>
    Version 2.0 <strong>Copyright &copy; 2018 </strong> All rights
    reserved.
</div>
</div><!-- /.login-box -->
<?php $this->load->view('auth/cover_outro'); ?>

<script src="<?php echo base_url(); ?>assets/js/jQuery-2.1.4.min.js"></script>
<script src="<?php echo base_url(); ?>assets/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<?php $this->load->view('auth/runtime_scripts'); ?>

</body>
</html>