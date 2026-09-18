<?php
       $this->load->view('header/headerSec');
    ?>
    <link type="text/css" rel="stylesheet" href="<?=base_url()?>vendors/fullcalendar/css/fullcalendar.min.css" />
    <link type="text/css" rel="stylesheet" href="<?=base_url()?>css/pages/calendar_custom.css" />
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div style="color:green" align="right" id="todaysDate"></div>
    
      <h1><i class="fa fa-medkit" aria-hidden="true"></i>
        Manage Appointment 
        <!-- <small>advanced tables</small> -->
      </h1>
      
    </section>

    

    <!-- Main content -->
    <section class="content">
      
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Appointment Record List</h3>
            </div>

             <div id="calendar"></div>
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

<!-- jQuery 3 --><!-- 
<script src="<?=base_url()?>assets/bower_components/jquery/dist/jquery.min.js"></script> -->
<!-- Bootstrap 3.3.7 -->
<!-- <script src="<?=base_url()?>assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script> -->
 
<script type="text/javascript" src="<?=base_url()?>js/components.js"></script>
<script type="text/javascript" src="<?=base_url()?>js/custom.js"></script>
<!-- end of global scripts-->
<!--plugin script-->
<script type="text/javascript" src="<?=base_url()?>vendors/moment/js/moment.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>vendors/fullcalendar/js/fullcalendar.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>js/pluginjs/calendarcustom.js" ></script>
<!-- page script -->
 <script src="<?=base_url()?>assets/js/password.js"></script>
<!-- end of plugin scripts -->
<!-- <script type="text/javascript" src="<?=base_url()?>js/pages/calendar.js"></script> -->
<script>
   if ($('#calendar').length) {
                var date = new Date();
                var d = date.getDate();
                var m = date.getMonth();
                var y = date.getFullYear();
                var calendar = $('#calendar').fullCalendar({
         
          displayEventTime: false,
        
          header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
          },
          buttonText: {
            prev: "",
            next: "",
            today: 'Today',
            month: 'Month',
            week: 'Week',
            day: 'Day'
          },
        
        
        events: [
          
          
                  
          <?php
            foreach ($getAppointment as $r)
            {
              $start_day = date('d', strtotime($r->count_date));
              $smonth = date('n', strtotime($r->count_date));
              $start_month = $smonth - 1;
              $start_year = date('Y', strtotime($r->count_date));
              $end_year = date('Y', strtotime($r->count_date));
              $end_day = date('d', strtotime($r->count_date));
              $emonth = date('n', strtotime($r->count_date));
              $end_month = $emonth - 1;

              ?>
              {
                title: "<?=$r->appointD?> Click to view",
                start: new Date(<?php echo $start_year . ',' . $start_month . ',' . $start_day; ?>),
                end: new Date(<?php echo $end_year . ',' . $end_month . ',' . $end_day; ?>),
                color: 'green',
                url: '<?= base_url('view-app-s')?>?app_date=<?= $r->count_date ?>',
              },

          <?php }
          ?>


        ],
        eventColor: '#3A87AD',
      });
    }
</script>
</body>
</html>
