<?php
       $this->load->view('header/headerSec');
    ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div style="color:green" align="right" id="todaysDate"></div>
    
      <h1><i class="fa fa-home" aria-hidden="true"></i>
        Manage Clinic 
        <!-- <small>advanced tables</small> -->
      </h1>
    </section>
    
    <section class="content">
    
        <div class="row">
            <!-- left column -->
            <div class="col-md-10">
              <!-- general form elements -->
                
                
                
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Update Clinic Account Details</h3>
                    </div><!-- /.box-header -->
                    <!-- form start -->
                    
                    <form role="form" action="<?php echo base_url() ?>Secretary/ClinicUpdate" method="post" role="form">
                        <div class="box-body">
                            <div class="row">
                                <input type="hidden" name="old_clinic_id" value="<?php echo $old_clinic_id;?>">
                                <input type="hidden" name="old_assignment_id" value="<?php echo !empty($assignment) ? (int)$assignment->id : 0;?>">                               
                                <div class="col-md-6">   
                                    <div class="form-group">
                                        <label for="name">Clinic Name</label>
                                        <input type="text" class="form-control" id="name"  value="<?php echo $getClinic[0]->name;?>" name ="name" required>
                                        
                                    </div>
                                </div>
                                
                                
                            </div>
                            <div class="row">
                                <div class="col-md-6">                             
                                    <div class="form-group">
                                        <label for="contact">Contact No.</label>
                                        <input type="text" class="form-control" id="contact"  value="<?php echo $getClinic[0]->contact;?>" name ="contact" required>    
                                    </div>
                                </div>

                            </div>
                            
                            <div class="row">
                                <div class="col-md-6"> 
                                                                  
                                    <div class="form-group">
                                        <label for="location">Location</label>
                                        <input type="text" class="form-control" id="location"  value="<?php echo $getClinic[0]->location;?>" name ="location" required>
                                        
                                    </div>
                                </div>
                                
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Clinic Specialization(s)</label>
                                        <div class="form-control" style="height:auto; min-height:42px; background:#f8fafc;">
                                            <?php if (!empty($clinicSpecializations)): ?>
                                                <?=htmlspecialchars(implode(', ', array_map(function($item){ return $item->special_name; }, $clinicSpecializations)), ENT_QUOTES, 'UTF-8')?>
                                            <?php else: ?>
                                                No specialization configured
                                            <?php endif; ?>
                                        </div>
                                        <p class="help-block">Specializations are managed by the physician or administrator.</p>
                                    </div>
                                </div>
                            </div>

                        </div><!-- /.box-body -->
    
                        <div class="box-footer">
                            <button style="color: white" type="submit" class="btn btn-primary"  value="Submit" /><i class = "fa fa-save"></i> Submit</button> 
                            
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



</body>
</html>