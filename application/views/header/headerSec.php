<?php
$camsRole = 'Secretary';
$camsHome = 'profile-s';
$camsProfile = 'update-profile-s';
$camsLogout = 'logout-s';
$camsNav = array(
  array('label' => 'Dashboard', 'url' => 'profile-s', 'icon' => 'dashboard'),
  array('label' => 'Manage Clinics', 'url' => 'view-clinic-s', 'icon' => 'clinic'),
  array('label' => 'Manage Appointment', 'icon' => 'appointment', 'children' => array(
    array('label' => 'Book an Appointment', 'url' => 'app-register-s'),
    array('label' => 'View Appointments', 'url' => 'view-app-s'),
    array('label' => 'Calendar of Appointments', 'url' => 'calendar-s'),
    array('label' => 'Set Limit of Appointments', 'url' => 'limit-register-s', 'modal' => 'Set Limit of Appointments'),
    array('label' => 'View Limit of Appointments', 'url' => 'view-limit-s'),
  )),
  array('label' => 'Manage Schedule', 'url' => 'view-schedule-s', 'icon' => 'schedule'),
  array('label' => 'Tools', 'icon' => 'tools', 'children' => array(
    array('label' => 'Update Password', 'url' => 'change-pass-s'),
    array('label' => 'Update Profile Details', 'url' => 'update-profile-s'),
  )),
  array('label' => 'Reports', 'icon' => 'reports', 'children' => array(
    array('label' => 'Monthly Clinic Average', 'url' => 'view-avg-s'),
    array('label' => 'Done Appointments', 'url' => 'view-done-s'),
    array('label' => 'Cancelled Appointments', 'url' => 'view-cancel-s'),
  )),
  array('label' => 'Logs', 'url' => 'view-logs-s', 'icon' => 'logs'),
);
$this->load->view('header/appShell', compact('camsRole', 'camsHome', 'camsProfile', 'camsLogout', 'camsNav'));
