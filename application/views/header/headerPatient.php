<?php
$camsRole = 'Patient';
$camsHome = 'profile-p';
$camsProfile = 'update-profile-p';
$camsLogout = 'logout-p';
$camsNav = array(
  array('label' => 'Dashboard', 'url' => 'profile-p', 'icon' => 'dashboard'),
  array('label' => 'Manage Appointment', 'icon' => 'appointment', 'children' => array(
    array('label' => 'Book an Appointment', 'url' => 'app-register'),
    array('label' => 'View Appointments', 'url' => 'view-appointment'),
  )),
  array('label' => 'Tools', 'icon' => 'tools', 'children' => array(
    array('label' => 'Update Password', 'url' => 'account-tools/password', 'modal' => 'Update Password'),
    array('label' => 'Update Profile Details', 'url' => 'account-tools/profile', 'modal' => 'Update Profile'),
    array('label' => 'Set up Security Question', 'url' => 'q-register'),
  )),
);
$this->load->view('header/appShell', compact('camsRole', 'camsHome', 'camsProfile', 'camsLogout', 'camsNav'));
