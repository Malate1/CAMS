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
              <?php if ($this->session->flashdata('success')) { ?>
                <div role="alert" class="alert alert-success">
                 <button data-dismiss="alert" class="close" type="button">
                     <span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                     <strong>Well done!</strong>
                     <?= $this->session->flashdata('success') ?>
                 </div>
             <?php } ?>
             
             
             
             <div class="box box-success">
                <div class="box-header">
                    <h3 class="box-title">Set Limit of Appointments</h3>
                </div><!-- /.box-header -->
                <!-- form start -->
                
                <form role="form" action="<?php echo base_url() ?>Physician/LimitRegister" method="post" role="form">
                    <div class="box-body">
                        <div class="row">

                            <div class="col-lg-4 input_field_sections">
                                <h5>Date for this limit <span style="color: #cc0000">*</span></h5>
                                <input required type="date" min="<?php echo date('Y-m-d');?>" class="form-control" name="date"  value=""/>
                            </div>
                            
                            <div class="col-lg-4 input_field_sections">
                                <h5>Max number of Appointment <span style="color: #cc0000">*</span></h5>
                                <input required type="number" min="1" class="form-control" name="limit" value=""/>
                            </div>

                            
                            
                            
                        </div>

                        
                        
                    </div><!-- /.box-body -->
                    
                    <div class="box-footer">
                        <button  style="color: white" type="submit" class="btn btn-primary swalDefaultError"  value="Submit" /><i class = "fa fa-save"></i> Submit</button> 
                        
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


<script type="text/javascript">
  <?php if($this->session->flashdata('error')) { ?>
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
          title: 'Queue limit already existed for this date, proceed with Edit Queue Limit'
        })
      });
    });
  <?php } ?>
</script>

</body>
</html>