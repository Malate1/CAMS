<?php
$camsRole = 'Physician';
$camsHome = 'profile-phy';
$camsProfile = 'update-profile-phy';
$camsLogout = 'logout-phy';
$camsNav = array(
  array('label' => 'Dashboard', 'url' => 'profile-phy', 'icon' => 'dashboard'),
  array('label' => 'Manage Clinics', 'url' => 'view-clinic-p', 'icon' => 'clinic'),
  array('label' => 'Manage Appointment', 'icon' => 'appointment', 'children' => array(
    array('label' => 'Book an Appointment', 'url' => 'app-register-p'),
    array('label' => 'View Appointments', 'url' => 'view-app-p'),
    array('label' => 'Calendar of Appointments', 'url' => 'calendar-p'),
    array('label' => 'Set Limit of Appointments', 'url' => 'limit-register-p', 'modal' => 'Set Limit of Appointments'),
    array('label' => 'View Limit of Appointments', 'url' => 'view-limit-p'),
  )),
  array('label' => 'Manage Schedule', 'url' => 'view-schedule-p', 'icon' => 'schedule'),
  array('label' => 'Tools', 'icon' => 'tools', 'children' => array(
    array('label' => 'Update Password', 'url' => 'change-pass-phy'),
    array('label' => 'Update Profile Details', 'url' => 'update-profile-phy'),
  )),
  array('label' => 'Reports', 'icon' => 'reports', 'children' => array(
    array('label' => 'Monthly Clinic Average', 'url' => 'view-avg-p'),
    array('label' => 'Done Appointments', 'url' => 'view-done-p'),
    array('label' => 'Cancelled Appointments', 'url' => 'view-cancel-p'),
  )),
  array('label' => 'Logs', 'url' => 'view-logs-phy', 'icon' => 'logs'),
);
$this->load->view('header/appShell', compact('camsRole', 'camsHome', 'camsProfile', 'camsLogout', 'camsNav'));
