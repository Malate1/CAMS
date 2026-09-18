<?php
$this->load->view('header/headerPatient');
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div style="color:green" align="right" id="todaysDate"></div>

        <h1><i class="fa fa-medkit" aria-hidden="true"></i>
            Manage Security Question
            <!-- <small>advanced tables</small> -->
        </h1>
    </section>
    
    <section class="content">

        <div class="row">
            <!-- left column -->
            <div class="col-md-10">
              <!-- general form elements -->
              <!-- <?php if ($this->session->flashdata('success')) { ?>
                <div role="alert" class="alert alert-success">
                 <button data-dismiss="alert" class="close" type="button">
                     <span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                     <strong>Well done!</strong>
                     <?= $this->session->flashdata('success') ?>
                 </div>
             <?php } ?> -->



             <div class="box box-success">
                <div class="box-header">
                    <h3 class="box-title">Set Security Question</h3>
                </div><!-- /.box-header -->
                <!-- form start -->

                <form role="form" action="<?php echo base_url() ?>Patient/SecQRegister" method="post" role="form">
                    <div class="box-body">
                        <div class="row">                   
                            <div class="col-lg-4 input_field_sections">
                            <h5>Choose a Security Question <span style="color: #cc0000">*</span></h5>
                                <select class="form-control" required="" name = "question">
                                    <option value="1">What is your mother's maiden name?</option>
                                    <option value="2">In what school did you finished elementary?</option>
                                    <option value="3">What is your favorite color? </option>
                                    <option value="4">What is your birth year? </option>
                                    <option value="5">What is your father's first name? </option>          
                                </select>  
                            </div>
                            <div class="col-lg-4 input_field_sections">
                            <h5>Answer <span style="color: #cc0000">*</span></h5>
                                <input required="" type="text" class="form-control" name="answer" value=""/>
                            </div>  
                            <br>
                        </div>
                    </div><!-- /.box-body -->

                    <div class="box-footer">
                        <button  style="color: white" type="submit" class="btn btn-primary swalDefaultError swalDefaultSuccess"  value="Submit" /><i class = "fa fa-save"></i> Submit</button> 

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
    <!-- <script type="text/javascript" src="<?=base_url()?>vendors/select2/js/select2.js"></script> Maguba ag buttons sa print-->
    <script src="<?=base_url()?>assets/js/password.js"></script>




    <script type="text/javascript">

      <?php if($this->session->flashdata('SUCCESSMSG')) { ?>
        var newpass = <?php $this->input->post('newPassword') ?>
        $(function() {


          const Toast = Swal.mixin({
            toast: false,
            position: 'top',
            showConfirmButton: false,
            timer: 3000
        });

          $('.swalDefaultSuccess').fadeIn(function() {
            Toast.fire({

              type: 'success',
              title: 'Security Question Registered Successfully '
          })
        });
          
      });
    <?php } ?>

    <?php if($this->session->flashdata('errormsg1')) { ?>
    var newpass = <?php $this->input->post('newPassword') ?>
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
          title: 'You can only set up Security Question once'
          
      })
    });
      

      
  });
<?php } ?>    
</script>


</body>
</html>