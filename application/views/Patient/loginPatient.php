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
	<link rel="stylesheet" href="<?=base_url()?>assets/plugins/sweetalert2/sweetalert2.min.css">
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




		<div id="myModal" class="modal fade" tabindex="-1"  aria-labelledby="myModalLabel" role="dialog">
			<div class="modal-dialog" role="document" >

				<!-- Modal content-->
				<div class="modal-content" style="border-radius: 10px">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button> 
						<center><img src="<?=base_url()?>assets/dist/img/hnumcfi.jpg" class="img-circle"  alt="logo" style="height: 40px; width: 40px;"></center> 
						<h4 style="text-align: center;" class="modal-title"><b>Sign up Form for Patients</b></h4> 
					</div>
					<div class="modal-body">
						<!-- <?php echo form_open('SignUp/admin'); ?> -->

						<form action="<?php echo base_url('SignUp/patient') ?>" method="post" id="addPatient" enctype="multipart/form-data">  

							<!-- <form action="<?php echo base_url() ?>SignUp/patient" method="post"> -->


								<h5 style="text-align: center;"><b>Personal Details</b></h5>
								<div class="row">

									<div class="col-lg-6 input_field_sections">

										<h5>First Name <span style="color: #cc0000">*</span></h5>
										<input required type="text" class="form-control" name="fname" value=""/>
									</div>
									<div class="col-lg-6 input_field_sections">
										<h5>Middle Name <span style="color: #cc0000">*</span></h5>
										<input required type="text" class="form-control" name="mname" value=""/>
									</div>
								</div> 

								<div class="row">  
									<div class="col-lg-6 input_field_sections">
										<h5>Last Name <span style="color: #cc0000">*</span></h5>
										<input required type="text" class="form-control" name="lname" value=""/>
									</div>


									<div class="col-lg-6 input_field_sections">
										<h5>Contact No <span style="color: #cc0000">*</span></h5>
										<input required="" type="text" class="form-control" limit="4" name="contact" value=""/>
									</div>
								</div>

								<div class="row">   
									<div class="col-lg-6 input_field_sections">
										<h5>Birth Date <span style="color: #cc0000">*</span></h5>
										<input required="" type="date" class="form-control"  name="birthdate" value=""/>
									</div>

									<div class="col-lg-6 input_field_sections">
										<h5>Birthplace <span style="color: #cc0000">*</span></h5>
										<input required type="text" class="form-control" name="birthplace" value=""/>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-6 input_field_sections">
										<h5>Gender<span style="color: #cc0000">*</span></h5>

										<label class="radio-button"><input type="radio" id ="gender" name="gender" value="Female" > Female</label> &nbsp;&nbsp;&nbsp;&nbsp;
										<label class="radio-button"><input type="radio" id="genderM" name="gender" value="Male" > Male</label>
									</div>

									<div class="col-lg-6 input_field_sections">
										<h5>Address <span style="color: #cc0000">*</span></h5>
										<input required="" name="address" class="form-control"/>
									</div>
								</div>
								<hr>
								<h5 style="text-align: center;"><b>Account Details</b></h5>
								<div class="row">
									<div class="col-lg-6 input_field_sections">
										<h5>Email <span style="color: #cc0000">*</span></h5>
										<input required  type="email" class="form-control" name="email" value=""/>
									</div>

									<div class="col-lg-6 input_field_sections">
										<h5>Password <span style="color: #cc0000">*</span></h5>
										<input required type="password" class="form-control" name="password" id="pass" value=""/>

										<div id="meter_wrapper"> 
											<div id="meter"></div>
										</div>
										<br>
										<span style="color: red" id="pass_type"></span>
									</div>

									<div class="col-lg-6 input_field_sections">
										<h5>Confirm Password <span style="color: #cc0000">*</span></h5>
										<input required type="password" class="form-control" name="cpassword" id="cpassword" value=""/>
									</div>

									<div class="col-lg-6 input_field_sections">
										<h5>Image <span style="color: #cc0000">*</span></h5>
										<!-- the avatar markup -->
										<div id="kv-avatar-errors-1" class="center-block" style="max-width:500px;display:none"></div>             
										<div class="kv-avatar center-block" style="width:100%">
											<input required type="file" id="photo" name="photo" class="file-loading"/>                       
										</div>
									</div>

								</div>
								<!-- <hr>
								<div class="row">
									<h5 style="text-align: center;"><b>Password Recovery Details</b></h5>
									<div class="col-lg-6 input_field_sections">


										<h5>Choose a Security Question <span style="color: #cc0000">*</span></h5>
										<select class="form-control" required="" name = "question">
											<option value="1">What is your mother's maiden name?</option>
											<option value="2">In what school did you finished elementary?</option>
											<option value="3">What is your favorite color? </option>
											<option value="4">What is your birth year? </option>
											<option value="5">What is your father's first name? </option>          
										</select>  
									</div>
									<div class="col-lg-6 input_field_sections">
										<h5>Answer <span style="color: #cc0000">*</span></h5>
										<input required="" type="text" class="form-control" name="answer" value=""/>
									</div>  
									<br>

									



								 <div class="col-lg-6 input_field_sections">
									<h5>Image <span style="color: #cc0000">*</span></h5>
									<input type="file" required="" name="image" class="form-control"/>
								</div>
							</div>  -->

							<br> 
							<div class="row">
								<div class="col-lg-10 input_field_sections">
									<button onclick="return Validate()" style="color: white" type="submit" class="btn btn-primary"  value="Submit" /><i class = "fa fa-save"></i> Submit</button> 

									<button style="color: white" type="reset" class="btn btn-danger"  value="Reset" /><i class = "fa fa-close"></i> Reset</button>
								</div>

							</div>
						</form>
					</div>
					<div class="modal-footer" style="border-radius: 10px">
						<button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
					</div>
				</div>

			</div>
		</div>

		<div id="myModal1" class="modal fade" tabindex="-1"  aria-labelledby="myModalLabel" role="dialog">
			<div class="modal-dialog" role="document" >

				<!-- Modal content-->
				<div class="modal-content" style="border-radius: 10px">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button> 
						<center><img src="<?=base_url()?>assets/dist/img/hnumcfi.jpg" class="img-circle"  alt="logo" style="height: 40px; width: 40px;"></center> 
						<h4 style="text-align: center;" class="modal-title"><b>Account Recovery</b></h4> 
					</div>
					<div class="modal-body">
						<!-- <?php echo form_open('SignUp/admin'); ?> -->

						<form action="<?php echo base_url('Login/SecQValidateView') ?>" method="post" id="addPatient">  

							<!-- <form action="<?php echo base_url() ?>SignUp/patient" method="post"> -->


								<!-- <h5 style="text-align: center;"><b>Personal Details</b></h5> -->
								<div class="row">

									<div class="col-lg-8 input_field_sections">

										<h5>Enter your email address for account recovery: <span style="color: #cc0000">*</span></h5>
										<input required type="email" class="form-control" name="email" value=""/>
									</div>

									<div class="col-lg-8 input_field_sections">
										<h5>Select your security question: <span style="color: #cc0000">*</span></h5>
										<select class="form-control" required="" name = "question">
											<option value="1">What is your mother's maiden name?</option>
											<option value="2">In what school did you finished elementary?</option>
											<option value="3">What is your favorite color? </option>
											<option value="4">What is your birth year? </option>
											<option value="5">What is your father's first name? </option>          
										</select>
									</div>

									<div class="col-lg-8 input_field_sections">
										<h5>Answer <span style="color: #cc0000">*</span></h5>
										<input type="text" class="form-control"   name="answer" required />					 
									</div>
									
								</div> 

								

								<br> 
								<div class="row">
									<div class="col-lg-10 input_field_sections">
										<button onclick="return Validate()" style="color: white" type="submit" class="btn btn-primary"  value="Submit" /><i class = "fa fa-save"></i> Submit</button> 

										<button style="color: white" type="reset" class="btn btn-danger"  value="Reset" /><i class = "fa fa-close"></i> Reset</button>
									</div>

								</div>
							</form>
						</div>
						<div class="modal-footer" style="border-radius: 10px">
							<button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
						</div>
					</div>

				</div>
			</div>

				<style type="text/css">

					body {

						font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";

					}
				</style>
				<?php
				$authRole = 'Patient';
				$authHeadline = 'Your care, easier to schedule.';
				$authDescription = 'Find the right clinic, choose an available physician schedule and keep track of your appointments.';
				$authBadge = 'Patient portal';
				$this->load->view('auth/cover_intro', compact('authRole', 'authHeadline', 'authDescription', 'authBadge'));
				?>
				<div class="login-box">
					<div class="login-logo">
						<a href="#"><img src="<?=base_url()?>assets/dist/img/hnumcfi.jpg" class="img-circle"  alt="logo" style="height: 100px; width: 100px;"><br><h3>Clinics Appointment Management System<h3></a>
						</div><!-- /.login-logo -->
						<div class="login-box-body" style="border-radius: 5px">
							<p class="login-box-msg">Patient Login</p>
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

							<?php if($this->session->flashdata('success')) { ?>
								<div role="alert" class="alert alert-success">
									<button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
									<?=$this->session->flashdata('success')?>
								</div>
							<?php } ?>

							<?php if($this->session->flashdata('successR')) { ?>
								<div role="alert" class="alert alert-success">
									<button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
									<?=$this->session->flashdata('successR')?>
								</div>
							<?php } ?>

							<form action="<?php echo base_url(); ?>Login/logPatient" method="post">
								<div class="form-group has-feedback">
									<input type="email" class="form-control" placeholder="Email" name="email" required autofocus />
									<span onclick="Toggle()" class="glyphicon glyphicon-envelope form-control-feedback"></span>
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
							<input type="submit" class="btn btn-success btn-block swalDefaultError swalDefaultSuccess" value="Sign In" />
						</div><!-- /.col -->
					</div>
				</form>
				<br>
				<a style="color: gray;" data-toggle="modal" data-target="#myModal" > Don't you have an Account? <b>Sign up</b> </a><br>

				<a style="color: gray;" data-toggle="modal" data-target="#myModal1"> <b>Forgot Password?</b> </a>
				<br><br>
				
				
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
<script src="<?php echo base_url(); ?>assets/bower_components/jquery-ui/jquery-ui.min.js"></script>
<script src="<?php echo base_url(); ?>assets/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<?php $this->load->view('auth/runtime_scripts'); ?>

		<script type="text/javascript">
			function Validate() {
				var password = document.getElementById("pass").value;
				var confirmPassword = document.getElementById("cpassword").value;
				if (password != confirmPassword) {
					if (window.CamsUI) CamsUI.notify('error', 'Password mismatch', 'Passwords do not match.');
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


		</script>
		<script src="<?=base_url()?>assets/js/password.js"></script>

	</body>
	</html>