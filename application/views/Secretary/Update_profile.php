<?php
       $this->load->view('header/headerSec');
    ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div style="color:green" align="right" id="todaysDate"></div>
    
      <h1><i class="fa fa-users" aria-hidden="true"></i>
        Manage Profile
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
                        <h3 class="box-title">Update Profile Details</h3>
                    </div><!-- /.box-header -->
                    
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Personal Details</a></li>
                        <li role="presentation" ><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Photo</a></li>      
                    </ul>    
                    <br>

                    <div class="tab-content">
                        
                        <div role="tabpanel" class="tab-pane active" id="home">

                            <form action="<?php echo base_url('Secretary/ProfileUpdate') ?>" method="post" id="editPatient" enctype="multipart/form-data">    
                        <div class="box-body">
                            <div class="row">
                                                               
                                <div class="col-md-6">   
                                    <div class="form-group">
                                        <label for="fname">First Name</label>
                                        <input type="text" class="form-control" id="fname"  value="<?php echo $records[0]->fname;?>" name ="fname" required>
                                        
                                    </div>
                                </div>
                                <div class="col-md-6">                             
                                    <div class="form-group">
                                        <label for="mname">Middle Name</label>
                                        <input type="text" class="form-control" id="mname"  value="<?php echo $records[0]->mname;?>" name ="mname" required>    
                                    </div>
                                </div>
                                
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6"> 
                                                                  
                                    <div class="form-group">
                                        <label for="lname">Last Name</label>
                                        <input type="text" class="form-control" id="lname"  value="<?php echo $records[0]->lname;?>" name ="lname" required>
                                        
                                    </div>
                                </div>
                                <div class="col-md-6"> 
                                                                  
                                    <div class="form-group">
                                        <label for="contact">Contact</label>
                                        <input type="text" class="form-control" id="contact"  value="<?php echo $records[0]->contact;?>" name ="contact" required>
                                        
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6"> 
                                                                  
                                    <div class="form-group">
                                        <label for="birthplace">Birth Place</label>
                                        <input type="text" class="form-control" id="birthplace"  value="<?php echo $records[0]->birthplace;?>" name ="birthplace" required>
                                        
                                    </div>
                                </div>
                            
                                <div class="col-md-6"> 
                                                                  
                                    <div class="form-group">
                                        <label for="birthdate">Birth Date</label>
                                        <input type="date" class="form-control" id="birthdate"  value="<?php echo $records[0]->birthdate;?>" name ="birthdate" required>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6"> 
                                                                  
                                    <div class="form-group">
                                        <label for="gender">Gender</label>                                   
                                        <select required="" name="gender" class="form-control">
                                            <option value="<?php echo $records[0]->gender;?>"><?php echo $records[0]->gender;?></option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                                
                                        </select>                                     
                                    </div>
                                </div>                            
                                <div class="col-md-6">                                  
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <input type="text" class="form-control" id="address"  value="<?php echo $records[0]->address;?>" name ="address" required>    
                                    </div>
                                </div>

                                <!-- <div class="col-md-6"> 
                                    <img src="<?php echo base_url(); ?>uploads/profile-pic/<?=$this->session->image ?>" class="img-circle" alt="User Image" style="height: 100px; width: 100px;">                                 
                                    <div class="form-group">
                                        <label for="image">Image</label>
                                        <input type="file" class="form-control" id="Editphoto" name ="Editphoto" value="<?php echo $records[0]->image;?>" required>    
                                    </div>
                                </div> -->
                            </div>
                        </div><!-- /.box-body -->
    
                        <div class="box-footer">
                            <button  style="color: white" type="submit" class="btn btn-primary swalDefaultSuccess"  value="Submit" /><i class = "fa fa-save"></i> Submit</button> 
                            
                            <button style="color: white" type="reset" class="btn btn-danger"  value="Reset" /><i class = "fa fa-close"></i> Reset</button>
                        </div>
                        </form>
                            
                        </div>

                        <div role="tabpanel" class="tab-pane" id="profile">
                            <form action="<?php echo base_url('Secretary/ProfilePicUpdate') ?>" role="form" method="post" id="editPatient" enctype="multipart/form-data">     
                        <div class="box-body">
                            
                            <div class="row">

                                <div class="col-md-6"> 
                                    <img src="<?php echo base_url(); ?>uploads/profile-pic/<?=$this->session->image ?>" class="img-thumbnail rounded mb-2" alt="User Image" style="height: 200px; width: 200px;">                                 
                                    <div class="form-group">
                                        <label for="image">Image</label>
                                        <input type="file" class="form-control" id="Editphoto" name ="Editphoto" value="<?php echo $records[0]->image;?>" required>    
                                    </div>
                                </div>
                                                      
                                <div class="col-md-6">   
                                    <div class="form-group">
                                        <label for="fname">First Name</label>
                                        <input type="text" class="form-control" id="fname"  value="<?php echo $records[0]->fname;?>" name ="fname" readonly>
                                        
                                    </div>
                                </div>
                                 <div class="col-md-6">                             
                                    <div class="form-group">
                                        <label for="mname">Middle Name</label>
                                        <input type="text" class="form-control" id="mname"  value="<?php echo $records[0]->mname;?>" name ="mname" readonly>    
                                    </div>
                                </div> 

                                <div class="col-md-6"> 
                                                                  
                                    <div class="form-group">
                                        <label for="lname">Last Name</label>
                                        <input type="text" class="form-control" id="lname"  value="<?php echo $records[0]->lname;?>" name ="lname" readonly>
                                        
                                    </div>
                                </div>

                                
                            </div>
                        </div><!-- /.box-body -->
    
                        <div class="box-footer">
                            <button  style="color: white" type="submit" class="btn btn-primary swalDefaultSuccess"  value="Submit" /><i class = "fa fa-save"></i> Submit</button> 
                            
                            <button style="color: white" type="reset" class="btn btn-danger"  value="Reset" /><i class = "fa fa-close"></i> Reset</button>
                        </div>
                        </form>

                        </div>
                    </div>

                    
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



 <script type="text/javascript">

<?php if($this->session->flashdata('success')) { ?>
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
        title: 'Profile Succcessfully Updated '
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