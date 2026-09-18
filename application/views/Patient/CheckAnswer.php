<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>CAMS | Login </title>
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<link rel="stylesheet" href="<?=base_url()?>assets/bower_components/bootstrap/dist/css/bootstrap.min.css"/>
	<link rel="stylesheet" href="<?=base_url()?>assets/bower_components/font-awesome/css/font-awesome.min.css"/>
	<link rel="stylesheet" href="<?=base_url()?>assets/bower_components/Ionicons/css/ionicons.min.css"/>
	<link rel="stylesheet" href="<?=base_url()?>assets/dist/css/AdminLTE.min.css"/>
	<link rel="stylesheet" href="<?=base_url()?>assets/dist/css/skins/_all-skins.min.css"/>
	<link rel="stylesheet" href="<?=base_url()?>assets/plugins/sweetalert2/sweetalert2.min.css"/>
	<link rel="stylesheet" href="<?=base_url()?>assets/plugins/toastr/toastr.min.css"/>
	<link rel="stylesheet" href="<?=base_url()?>css/cams-modern.css"/>
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
			<div class="login-box" >
				<div class="login-logo">
					<a href="#"><img src="<?=base_url()?>assets/dist/img/hnumcfi.jpg" class="img-circle"  alt="logo" style="height: 100px; width: 100px;"><br><h3>Clinics Appointment Management System<h3></a>
					</div><!-- /.login-logo -->
					<div class="login-box-body" style="border-radius: 5px">
						<p class="login-box-msg">Account Recovery</p>
						<?php if($this->session->flashdata('errormsg')) { ?>
							<div role="alert" class="alert alert-danger">
								<button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
								<?=$this->session->flashdata('errormsg')?>
							</div>
						<?php } ?>

						<?php if($this->session->flashdata('errormsg1')) { ?>
							<div role="alert" class="alert alert-danger">
								<button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
								<?=$this->session->flashdata('errormsg1')?>
							</div>
						<?php } ?>

						<?php if($this->session->flashdata('errormsg2')) { ?>
							<div role="alert" class="alert alert-danger">
								<button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
								<?=$this->session->flashdata('errormsg2')?>
							</div>
						<?php } ?>

						<?php if($this->session->flashdata('SUCCESSMSG')) { ?>
							<div role="alert" class="alert alert-success">
								<button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
								<?=$this->session->flashdata('SUCCESSMSG')?>
							</div>
						<?php } ?>

						<form action="<?php echo base_url(); ?>Patient/SecQValidateView" method="post">

							<div class="form-group has-feedback">
								<input  type="email" class="form-control" placeholder="Email" value="email"  name="email" required />					 
								
							</div>

							<div class="form-group has-feedback">
								<select class="form-control" required="" name = "question">
                                    <option value="1">What is your mother's maiden name?</option>
                                    <option value="2">In what school did you finished elementary?</option>
                                    <option value="3">What is your favorite color? </option>
                                    <option value="4">What is your birth year? </option>
                                    <option value="5">What is your father's first name? </option>          
                                </select>
							</div>



							<div class="form-group has-feedback">
								<input  type="text" class="form-control" placeholder="Answer" value="answer"  name="answer" required />					 
								
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
							<input type="submit" class="btn btn-success btn-block " value="Submit" />
						</div><!-- /.col -->
					</div>
				</form>
				
				
				
				

			</div><!-- /.login-box-body -->
			<div class="text-muted" style="text-align: center;"><br>
				Version 2.0 <strong>Copyright &copy; 2018 </strong> All rights
				reserved.
			</div>
		</div><!-- /.login-box -->

		<script src="<?php echo base_url(); ?>assets/js/jQuery-2.1.4.min.js"></script>
<script src="<?php echo base_url(); ?>assets/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>

		<script type="text/javascript">
			function Validate() {
				var password = document.getElementById("pass").value;
				var confirmPassword = document.getElementById("cpassword").value;
				if (password != confirmPassword) {
					alert("Passwords do not match.");
					return false;
				}
				return true;
			}
		</script>
		<script type="text/javascript">
			$(document).ready(function(){
				$("#pass").keyup(function(){
					check_pass();
				});
			});


		</script> <script src="<?=base_url()?>assets/js/password.js"></script>

	</body>
	</html>