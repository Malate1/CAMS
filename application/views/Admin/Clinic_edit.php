<?php
       $this->load->view('header/header');
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
                
                
                
                <div class="box box-success">
                    <div class="box-header">
                        <h3 class="box-title">Update Clinic Account Details</h3>
                    </div><!-- /.box-header -->
                    <!-- form start -->
                    
                    <form role="form" action="<?php echo base_url() ?>Admin/ClinicUpdate" method="post" role="form">
                        <div class="box-body">
                            <div class="row">
                                <input type="hidden" name="old_clinic_id" value="<?php echo $old_clinic_id;?>">
                                <input type="hidden" name="old_assignment_id" value="<?php echo !empty($assignment) ? (int)$assignment->id : 0; ?>">                               
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

                            <?php if (!empty($assignment)): ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Physician</label>
                                        <input type="text" class="form-control" value="Dr. <?=htmlspecialchars($assignment->physician_name, ENT_QUOTES, 'UTF-8')?>" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="special_ids">Specialization(s)</label>
                                        <select class="form-control" id="special_ids" name="special_ids[]" multiple size="4" required>
                                            <?php foreach ((isset($availableSpecializations) ? $availableSpecializations : array()) as $specialization): ?>
                                                <option value="<?=$specialization->special_id?>" <?=in_array((int)$specialization->special_id, (isset($selectedSpecialIds) ? $selectedSpecialIds : array()), true) ? 'selected' : ''?>><?=htmlspecialchars($specialization->special_name, ENT_QUOTES, 'UTF-8')?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <p class="help-block">These are the services this physician provides specifically at this clinic.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="secretary_id">Secretary</label>
                                        <select class="form-control" id="secretary_id" name="secretary_id" required>
                                            <option value="">Select Secretary</option>
                                            <?php foreach ((isset($getSecretary) ? $getSecretary : array()) as $secretary): ?>
                                                <option value="<?=$secretary->secretary_id?>" <?=(int)$assignment->secretary_id === (int)$secretary->secretary_id ? 'selected' : ''?>><?=htmlspecialchars($secretary->fname.' '.$secretary->lname, ENT_QUOTES, 'UTF-8')?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                        </div><!-- /.box-body -->
    
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary"  value="Submit"><i class = "fa fa-save"></i> Submit</button> 
                            
                            <button type="reset" class="btn btn-danger"  value="Reset"><i class = "fa fa-close"></i> Reset</button>
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



</body>
</html>