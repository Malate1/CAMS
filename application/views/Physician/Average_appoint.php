<?php
$this->load->view('header/headerPhysician');
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

    </section>

    <div id="myModal" class="modal fade" role="dialog" style="border-radius: 10px">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content" style="border-radius: 10px">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button> 
            <center><img src="<?=base_url()?>assets/dist/img/hnumcfi.jpg" class="img-circle"  alt="logo" style="height: 40px; width: 40px;"></center> 
            <h4 style="text-align: center;" class="modal-title"><b>Choose a Month for Average Number of Appointment of Clinics by Month</b></h4> 
        </div>
        <div class="modal-body">
            <form role="form" action="<?php echo base_url() ?>Physician/ViewAverage" method="post" role="form">
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
      <div class="box box-success">
        <div class="box-header">
          <h3 class="box-title">Average Number of Appointment of Clinics by Month</h3>
      </div>





      <div class="box-body table-responsive">
          <table  class="table cellpadding=3"  id="printTable" >
            <button type="button" class="btn btn-primary" onclick="myPrint()" ><span class="fa fa-print" aria-hidden="true"></span> PRINT </button>
        </div><br>

        <thead>
            <tr role="row">

              <th >Clinic Name</th>
              <th >Average Number of Visits</th>

          </tr>
      </thead>
      <tbody>
        <?php
        if(!empty($getAverage) && !empty($yearMonth))
        {
            foreach ($getAverage as $value)
            { 

                   // $yearMonth = explode('-', $yearMonth);
                   //         $year = $yearMonth[0];
                   //           if ( ! isset($yearMonth[1])) {
                   //            $yearMonth[1] = null;
                   //            }
                   //           $m = $yearMonth[1];

                   //      $days = cal_days_in_month(CAL_GREGORIAN, $m, $year);
                   //      $aver = ($value->visits / $days) * 100;

              ?>
              <tr>



                <td><?=$value->name?></td>
                <td><?= number_format($value->average,1)  ?> &#37;</td>


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
</body>
</html>
