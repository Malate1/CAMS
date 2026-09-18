<?php
       $this->load->view('header/header');
    ?>
<link rel="stylesheet" href="<?=base_url()?>assets/plugins/sweetalert2/sweetalert2.min.css">
<link rel="stylesheet" href="<?=base_url()?>assets/plugins/toastr/toastr.min.css">
<!-- <link type="text/css" rel="stylesheet" href="<?=base_url()?>css/password.css" /> -->
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
                
                <div class="box box-success">
                    <div class="box-header">
                        <h3 class="box-title">Update Password</h3>
                    </div><!-- /.box-header -->
                    <!-- form start -->
                    
                    <form role="form" action="<?php echo base_url() ?>Admin/PatientUpdate" method="post" role="form">
                        <div class="box-body">
                            <div class="row">
                              <input type="hidden" name="old_patient_id" value="<?php echo $old_patient_id;?>">
                                                                                   
                            </div>
                            <div class="row">
                                <div class="col-md-6">                             
                                    <div class="form-group">

                                         <label for="newPassword">Your New Password</label>
                                        
                                        <?php
                                        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                                        $password = substr( str_shuffle( $chars ), 0, 8 );?>
                                        <input  name ="newPassword" value="<?php echo $password;?>" class="form-control" type="text" id="pass" readonly>

                                             <div id="meter_wrapper"> 
                                             <div id="meter"></div>
                                            </div>
                                            <br>
                                            <span style="color: red" id="pass_type"></span>

                                            <button  onClick="history.go(0)" style="color: white" type="button" class="btn btn-success "  value="Submit" /><i class = "fa fa-refresh"></i> Try Again</button> 

                                            
                                    </div>
                                </div>
                            </div>                           
                           
                        </div><!-- /.box-body -->
                        <div class="box-footer">
                            <button data-toggle="modal" data-target="#spinner_modal" onclick="return Validate()" style="color: white" type="submit" class="btn btn-primary swalDefaultSuccess"  value="Submit" /><i class = "fa fa-save"></i> Submit</button> 
                            
                            <a class="btn btn-danger"  href="<?=base_url('view-patient-a')?>"><i class="fa fa-close"></i> Cancel</a>
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
<!-- <script type="text/javascript" src="<?=base_url()?>vendors/select2/js/select2.js"></script> Maguba ag buttons sa print-->
<script src="<?=base_url()?>assets/js/password.js"></script>
</body>
</html>