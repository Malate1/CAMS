<?php
$this->load->view('header/headerPhysician');

$summary = isset($dashboard['summary']) ? $dashboard['summary'] : array();
$dashRole = 'Physician';
$dashTitle = 'Clinical workload overview';
$dashSubtitle = 'Track today’s queue, upcoming demand, completed visits and clinic distribution.';
$dashKpis = array(
    array('label' => 'Today pending', 'value' => isset($summary['today']) ? $summary['today'] : 0, 'hint' => 'Patients waiting today', 'icon' => 'fa-clock-o'),
    array('label' => 'Upcoming', 'value' => isset($summary['upcoming']) ? $summary['upcoming'] : 0, 'hint' => 'Pending appointments ahead', 'icon' => 'fa-calendar'),
    array('label' => 'Completed', 'value' => isset($summary['done']) ? $summary['done'] : 0, 'hint' => 'Visits marked done', 'icon' => 'fa-check-circle'),
    array('label' => 'Cancelled', 'value' => isset($summary['cancelled']) ? $summary['cancelled'] : 0, 'hint' => 'Cancelled appointments', 'icon' => 'fa-times-circle'),
    array('label' => 'Next 7 days', 'value' => isset($summary['next7']) ? $summary['next7'] : 0, 'hint' => 'Pending demand this week', 'icon' => 'fa-calendar-check-o'),
    array('label' => 'Completion rate', 'value' => isset($summary['completion_rate']) ? $summary['completion_rate'] : 0, 'suffix' => '%', 'hint' => 'Done vs resolved visits', 'icon' => 'fa-line-chart'),
);
$dashActions = array(
    array('label' => 'Book appointment', 'url' => 'app-register-p', 'icon' => 'fa-plus', 'primary' => true),
    array('label' => 'View queue', 'url' => 'view-app-p', 'icon' => 'fa-list', 'primary' => false),
    array('label' => 'Schedule', 'url' => 'view-schedule-p', 'icon' => 'fa-calendar-o', 'primary' => false),
);
$this->load->view('dashboard/overview', compact('dashboard', 'dashRole', 'dashTitle', 'dashSubtitle', 'dashKpis', 'dashActions'));
?>
<footer class="main-footer"><strong>Copyright &copy; 2018</strong> All rights reserved.</footer>
</div>
</body>
</html>
