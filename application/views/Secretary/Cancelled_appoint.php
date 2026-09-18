<?php
       $this->load->view('header/headerSec');
    ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div style="color:green" align="right" id="todaysDate"></div>
    
      <h1><i class="fa fa-medkit" aria-hidden="true"></i>
        Reports 
        <!-- <small>advanced tables</small> -->
      </h1>
      <!-- <ol class="breadcrumb float-right  nav_breadcrumb_top_align">
          <li class="breadcrumb-item">
            <a href="<?=base_url('dashboard')?>">
             <i class="fa fa-dashboard" data-pack="default" data-tags=""></i> Dashboard
            </a>
          </li>
          <li class="breadcrumb-item">
            <a href="#">Appointment</a>
              </li>
              <li class="active breadcrumb-item">Appointment View</li>

      </ol> -->
    </section>

    <div id="myModal" class="modal fade" role="dialog" style="border-radius: 10px">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content" style="border-radius: 10px">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button> 
                        <center><img src="<?=base_url()?>assets/dist/img/hnumcfi.jpg" class="img-circle"  alt="logo" style="height: 40px; width: 40px;"></center> 
                         <h4 style="text-align: center;" class="modal-title"><b>Choose Month for Cancelled Appointments</b></h4> 
                      </div>
                      <div class="modal-body">
                        <form role="form" action="<?php echo base_url() ?>Secretary/ViewCancelled" method="post" role="form">
                        <div class="box-body">
                            <div class="row">
                                                            
                                <?php
                              $date = date("Y-m");
                              $date = strtotime(date("Y-m", strtotime($date)) . " +2 month");
                              $date = date("Y-m",$date);
                              ?>              
                                                            
                                <div class="col-lg-6 input_field_sections">
                                    <h5>Month <span style="color: #cc0000">*</span></h5>
                                    <input required="" type="month" class="form-control" id="first" name="first"  max="<?php echo $date; ?>" name="first"/>
                                </div>
                                <script type="text/javascript">
                                document.getElementById("first").value = "<?php echo date("Y-m");?>";
                              </script>
                            </div>
                            

                            
                           
                        </div><!-- /.box-body -->
    
                        <div class="box-footer">
                            <button style="color: white" type="submit" class="btn btn-primary"  value="Submit" /><i class = "fa fa-print"></i> Generate</button> 
                            
                            <button style="color: white" type="reset" class="btn btn-danger"  value="Reset" /><i class = "fa fa-close"></i> Reset</button>
                        </div>
                    </form>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><i class = "fa fa-close"></i> Close</button>
                      </div>
        </div>

        </div>
    </div>

    

    <!-- Main content -->
    <section class="content">
      <div class="row">
            <div class="col-xs-12 text-left">
                <div class="form-group">
                    <a class="btn btn-primary" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus" aria-hidden="true"></i> Choose Month</a>
                </div>
            </div>
        </div>
     
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Cancelled Appointments </h3>
            </div>

             


            
            <div class="box-body table-responsive">
              <table  class="table cellpadding="3"  id="printTable" >
                <button type="button" class="btn btn-primary" onclick="myPrint()" ><span class="glyphicon glyphicon-print" aria-hidden="true"></span> PRINT </button>
                </div><br>
                <script>
				  function myPrint()
				  {
				  var divToPrint=document.getElementById("printTable");
				   newWin= window.open("");
				   newWin.document.write(divToPrint.outerHTML);
				   newWin.print();
				   newWin.close();
				  }           
				</script>
                <thead>
                <tr role="row">
                                                  
                  <th >Appointment Date</th>
                  <th >Purpose</th>
                  <th >Firstname</th>
                  <th >Lastname</th>                                
                  <th >Status</th>
                  
                </tr>
                </thead>
                <tbody>
                <?php
                  if(!empty($getDone))
                  {
                    foreach ($getDone as $value)
                    { ?>
                      <tr>
                        <td><?=$value->app_date?></td>
                        <td><?=$value->purpose?></td>
                        <td><?=$value->fname?></td>
                        <td><?=$value->lname?></td>
                        <td><?=$value->app_status?></td>
                        
                      </tr>   
                      <?php   }
                    }
                    ?>
                </tbody>
                
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
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
<script>
$(document).ready(function() {

});
</script>
<!-- page script -->

</body>
</html>
