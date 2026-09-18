<?php
$camsRole = 'System Admin';
$camsHome = 'profile';
$camsProfile = 'update-profile';
$camsLogout = 'logout-a';
$camsNav = array(
  array('label' => 'Dashboard', 'url' => 'profile', 'icon' => 'dashboard'),
  array('label' => 'Manage Users', 'icon' => 'users', 'children' => array(
    array('label' => 'Patients', 'url' => 'view-patient-a'),
    array('label' => 'Secretaries', 'url' => 'view-secretary-a'),
    array('label' => 'Physicians', 'url' => 'view-physician-a'),
  )),
  array('label' => 'Manage Clinics', 'url' => 'view-clinic-a', 'icon' => 'clinic'),
  array('label' => 'Tools', 'icon' => 'tools', 'children' => array(
    array('label' => 'Update Password', 'url' => 'change-pass'),
    array('label' => 'Update Profile Details', 'url' => 'update-profile'),
  )),
  array('label' => 'Reports', 'icon' => 'reports', 'children' => array(
    array('label' => 'Top Visited Clinics', 'url' => 'view-top'),
    array('label' => 'Top Consulted Ailments', 'url' => 'view-topC'),
    array('label' => 'Monthly Clinic Average', 'url' => 'view-avg'),
  )),
  array('label' => 'Logs', 'url' => 'view-logs', 'icon' => 'logs'),
);
$this->load->view('header/appShell', compact('camsRole', 'camsHome', 'camsProfile', 'camsLogout', 'camsNav'));
