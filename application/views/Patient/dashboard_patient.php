<?php
$this->load->view('header/headerPatient');

$summary = isset($dashboard['summary']) ? $dashboard['summary'] : array();
$dashRole = 'Patient';
$dashTitle = 'My appointment overview';
$dashSubtitle = 'See what is coming up, review your appointment history and track where you receive care.';
$dashKpis = array(
    array('label' => 'Upcoming', 'value' => isset($summary['upcoming']) ? $summary['upcoming'] : 0, 'hint' => 'Scheduled appointments ahead', 'icon' => 'fa-calendar'),
    array('label' => 'Completed', 'value' => isset($summary['done']) ? $summary['done'] : 0, 'hint' => 'Visits marked done', 'icon' => 'fa-check-circle'),
    array('label' => 'Cancelled', 'value' => isset($summary['cancelled']) ? $summary['cancelled'] : 0, 'hint' => 'Cancelled appointments', 'icon' => 'fa-times-circle'),
    array('label' => 'Total history', 'value' => isset($summary['total']) ? $summary['total'] : 0, 'hint' => 'All recorded appointments', 'icon' => 'fa-history'),
    array('label' => 'Next 7 days', 'value' => isset($summary['next7']) ? $summary['next7'] : 0, 'hint' => 'Visits scheduled this week', 'icon' => 'fa-calendar-check-o'),
    array('label' => 'Visit completion', 'value' => isset($summary['completion_rate']) ? $summary['completion_rate'] : 0, 'suffix' => '%', 'hint' => 'Completed vs resolved visits', 'icon' => 'fa-pie-chart'),
);
$dashActions = array(
    array('label' => 'Book appointment', 'url' => 'app-register', 'icon' => 'fa-plus', 'primary' => true),
    array('label' => 'View appointments', 'url' => 'view-appointment', 'icon' => 'fa-list', 'primary' => false),
);
$this->load->view('dashboard/overview', compact('dashboard', 'dashRole', 'dashTitle', 'dashSubtitle', 'dashKpis', 'dashActions'));
?>
<footer class="main-footer"><strong>Copyright &copy; 2018</strong> All rights reserved.</footer>
</div>
</body>
</html>
