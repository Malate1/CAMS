<?php
       $this->load->view('header/headerPhysician');
    ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div style="color:green" align="right" id="todaysDate"></div>
    
      <h1><i class="fa fa-medkit" aria-hidden="true"></i>
        Manage Appointment
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
                        <h3 class="box-title">Update Limit</h3>
                    </div><!-- /.box-header -->
                    <!-- form start -->
                    
                    <form role="form" action="<?php echo base_url() ?>Physician/LimitUpdate" method="post" role="form">
                        <div class="box-body">
                            <div class="row">
                                <input type="hidden" name="old_limit_id" value="<?php echo $old_limit_id;?>">                               
                                <div class="col-md-6">   
                                    <div class="form-group">
                                        <label for="name">Limit</label>
                                        <input type="text" class="form-control" id="limit"  value="<?php echo $getLimit[0]->queueLimit;?>" name ="queueLimit" required>
                                        
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