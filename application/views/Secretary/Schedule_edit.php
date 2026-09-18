<?php
       $this->load->view('header/headerSec');
    ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div style="color:green" align="right" id="todaysDate"></div>
    
      <h1><i class="fa fa-calendar" aria-hidden="true"></i>
        Manage Schedule
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
                        <h3 class="box-title">Update Schedule Details</h3>
                    </div><!-- /.box-header -->
                    <!-- form start -->
                    
                    <form role="form" action="<?php echo base_url() ?>Secretary/ScheduleUpdate" method="post" role="form">
                        <div class="box-body">
                            <div class="row">
                                <input type="hidden" name="old_schedule_id" value="<?php echo $old_schedule_id;?>">
                                <input type="hidden" name="old_schedule_link_id" value="<?php echo isset($old_schedule_link_id) ? (int)$old_schedule_link_id : 0;?>">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Clinic</label>
                                        <input type="text" class="form-control" value="<?=htmlspecialchars($getSchedule[0]->clinic_name . (!empty($getSchedule[0]->clinic_location) ? ' · '.$getSchedule[0]->clinic_location : ''), ENT_QUOTES, 'UTF-8')?>" readonly>
                                    </div>
                                </div>                              
                                <div class="col-md-6">   
                                    <div class="form-group">
                                        <label for="day">Days of Duty</label>
                                        <select required="" class="form-control" required="" name = "day">
                                                <option value="MTW" <?=$getSchedule[0]->day === 'MTW' ? 'selected' : ''?>>MTW</option>
                                                <option value="TTh" <?=$getSchedule[0]->day === 'TTh' ? 'selected' : ''?>>TTh</option>
                                                <option value="Sat" <?=$getSchedule[0]->day === 'Sat' ? 'selected' : ''?>>Saturday</option>
                                                <option value="Sun" <?=$getSchedule[0]->day === 'Sun' ? 'selected' : ''?>>Sunday</option>
                                                <option value="MWF" <?=$getSchedule[0]->day === 'MWF' ? 'selected' : ''?>>MWF</option>
                                                <option value="ThF" <?=$getSchedule[0]->day === 'ThF' ? 'selected' : ''?>>ThF</option>
                                                <option value="M-F" <?=$getSchedule[0]->day === 'M-F' ? 'selected' : ''?>>M-F</option>
                                                
                                             </select>
                                        
                                    </div>
                                </div>
                                
                                
                            </div>
                            <div class="row">
                                <div class="col-md-6">                             
                                    <div class="bootstrap-timepicker">
                                        <div class="form-group">
                                          <label>Time in:</label>

                                          <div class="input-group">
                                            <input type="time" class="form-control timepicker" id="time_in"  value="<?php echo $getSchedule[0]->time_in;?>" name ="time_in" required>

                                            <div class="input-group-addon">
                                              <i class="fa fa-clock-o"></i>
                                            </div>
                                          </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">                             
                                    <div class="bootstrap-timepicker">
                                        <div class="form-group">
                                          <label>Time out:</label>

                                          <div class="input-group">
                                            <input type="time" class="form-control timepicker" id="time_out"  value="<?php echo $getSchedule[0]->time_out;?>" name ="time_out" required>

                                            <div class="input-group-addon">
                                              <i class="fa fa-clock-o"></i>
                                            </div>
                                          </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            
                            
                        </div><!-- /.box-body -->
    
                        <div class="box-footer">
                            <button  style="color: white" type="submit" class="btn btn-primary"  value="Submit" /><i class = "fa fa-save"></i> Submit</button> 
                            
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

<!-- <script src="<?=base_url()?>assets/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?=base_url()?>assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script> -->
<!-- <script type="text/javascript" src="<?=base_url()?>vendors/select2/js/select2.js"></script> Maguba ag buttons sa print-->
 <script src="<?=base_url()?>assets/js/password.js"></script>


</body>
</html>