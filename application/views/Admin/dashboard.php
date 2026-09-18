<?php
$this->load->view('header/header');

$summary = isset($dashboard['summary']) ? $dashboard['summary'] : array();
$dashRole = 'Admin';
$dashTitle = 'Operations overview';
$dashSubtitle = 'Monitor users, clinic activity and appointment demand across CAMS.';
$dashKpis = array(
    array('label' => 'Patients', 'value' => isset($summary['patients']) ? $summary['patients'] : 0, 'hint' => 'Registered patient accounts', 'icon' => 'fa-users'),
    array('label' => 'Physicians', 'value' => isset($summary['physicians']) ? $summary['physicians'] : 0, 'hint' => 'Active physician records', 'icon' => 'fa-user-md'),
    array('label' => 'Secretaries', 'value' => isset($summary['secretaries']) ? $summary['secretaries'] : 0, 'hint' => 'Registered secretary accounts', 'icon' => 'fa-user'),
    array('label' => 'Clinics', 'value' => isset($summary['clinics']) ? $summary['clinics'] : 0, 'hint' => 'Configured clinic locations', 'icon' => 'fa-hospital-o'),
    array('label' => 'Appointments today', 'value' => isset($summary['today']) ? $summary['today'] : 0, 'hint' => 'All appointments scheduled today', 'icon' => 'fa-calendar-check-o'),
    array('label' => 'Next 7 days', 'value' => isset($summary['next7']) ? $summary['next7'] : 0, 'hint' => 'Appointments scheduled this week', 'icon' => 'fa-calendar'),
    array('label' => 'Completion rate', 'value' => isset($summary['completion_rate']) ? $summary['completion_rate'] : 0, 'suffix' => '%', 'hint' => 'Done vs resolved appointments', 'icon' => 'fa-line-chart'),
);
$dashActions = array(
    array('label' => 'Manage clinics', 'url' => 'view-clinic-a', 'icon' => 'fa-medkit', 'primary' => true),
    array('label' => 'View reports', 'url' => 'view-top', 'icon' => 'fa-bar-chart', 'primary' => false),
);
$this->load->view('dashboard/overview', compact('dashboard', 'dashRole', 'dashTitle', 'dashSubtitle', 'dashKpis', 'dashActions'));
?>
<footer class="main-footer"><strong>Copyright &copy; 2018</strong> All rights reserved.</footer>
</div>
</body>
</html>
