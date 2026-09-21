<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'Login';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
// routes for log out
$route['logout-a']			= 'Login/logoutAdmin';
$route['logout-s']			= 'Login/logoutSec';
$route['logout-phy']		= 'Login/logoutPhysician';
$route['logout-p']			= 'Login/logoutPatient';

// Account tools are modal-driven. Password changes post directly to the
// role controller handlers; there are no standalone change-password pages.
$route['account-tools/profile']	= 'AccountTools/profile';
$route['account-tools/password']	= 'AccountTools/password';
$route['check-q']			= 'Login/checkAnswer';
$route['check-patient']		= 'Login/checkPatient';


//routes for sign-up
$route['sign-up']			= 'SignUp/patient';
$route['sign-up-s']			= 'SignUp/secretary';
$route['sign-up-phy']		= 'SignUp/physician';
$route['sign-up-a']			= 'SignUp/admin';
//$route['forgot']			= 'SignUp/Forgot_pass';
//routes for login
$route['login-s']			= 'Login/logSec';
$route['login-a']			= 'Login/logAdmin';
$route['login-phy']			= 'Login/logDoctor';
$route['login-p']			= 'Login/logPatient';
//routes for profile
$route['profile'] 			= 'Admin';
$route['profile-s'] 		= 'Secretary';
$route['profile-p'] 		= 'Patient';
$route['profile-phy'] 		= 'Physician';

//routes for change profile
$route['update-profile']		= 'Admin/ProfileUpdate';
$route['update-profile/(\d+)'] 	= 'Admin/ProfileUpdateView/$1';
$route['update-profile-s']		= 'Secretary/ProfileUpdate';
$route['update-profile-s/(\d+)']= 'Secretary/ProfileUpdateView/$1';
$route['update-profile-phy']	= 'Physician/ProfileUpdate';
$route['update-profile/(\d+)'] 	= 'Physician/ProfileUpdateView/$1';
$route['update-profile-p']		= 'Patient/ProfileUpdate';
$route['update-profile/(\d+)'] 	= 'Patient/ProfileUpdateView/$1';

//routes for change profile pic
$route['update-profile-pic']			= 'Admin/ProfilePicUpdate';
$route['update-profile-pic/(\d+)'] 		= 'Admin/ProfilePicUpdateView/$1';
$route['update-profile-pic-s']			= 'Secretary/ProfilePicUpdate';
$route['update-profile-pic-s/(\d+)'] 	= 'Secretary/ProfilePicUpdateView/$1';
$route['update-profile-pic-phy']		= 'Physician/ProfilePicUpdate';
$route['update-profile-pic-phy/(\d+)'] 	= 'Physician/ProfilePicUpdateView/$1';
$route['update-profile-pic-p']			= 'Patient/ProfilePicUpdate';
$route['update-profile-pic-p/(\d+)'] 	= 'Patient/ProfilePicUpdateView/$1';

//routes for admin - manage patient
$route['view-patient-a'] 			    = 'Admin/ViewPatient';
$route['patient-register-a'] 			= 'Secretary/PatientRegister';
$route['patient-a-edit'] 				= 'Admin/PatientUpdate';
$route['patient-a-edit/(\d+)'] 		    = 'Admin/PatientUpdateView/$1';
$route['admin-password-reset-token/(:any)/(:num)'] = 'Admin/RegenerateTemporaryPassword/$1/$2';
$route['patient-a-delete/(\d+)'] 		= 'Admin/PatientDelete/$1';
//manage physician
$route['view-physician-a'] 			    = 'Admin/ViewPhysician';
$route['physician-register-a'] 			= 'Admin/PhysicianRegister';
$route['physician-a-edit'] 				= 'Admin/PhysicianUpdate';
$route['physician-a-edit/(\d+)'] 		= 'Admin/PhysicianUpdateView/$1';
$route['physician-a-delete/(\d+)'] 		= 'Admin/PhysicianDelete/$1';
//manage secretary
$route['view-secretary-a'] 			    = 'Admin/ViewSecretary';
$route['secretary-register-a'] 			= 'Admin/SecretaryRegister';
$route['secretary-a-edit'] 				= 'Admin/SecretaryUpdate';
$route['secretary-a-edit/(\d+)'] 		= 'Admin/SecretaryUpdateView/$1';
$route['secretary-a-delete/(\d+)'] 		= 'Admin/SecretaryDelete/$1';
//manage clinic
$route['view-clinic-a'] 			    = 'Admin/ViewClinic';
$route['clinic-register-a'] 			= 'Admin/ClinicRegister';
$route['clinic-a-edit'] 				= 'Admin/ClinicUpdate';
$route['clinic-a-edit/(\d+)'] 			= 'Admin/ClinicUpdateView/$1';
$route['clinic-a-delete/(\d+)'] 		= 'Admin/ClinicDelete/$1';
//reports
$route['view-top'] 				        = 'Reports/adminTopClinics';
$route['view-topC'] 				    = 'Reports/adminTopPurposes';
$route['view-avg'] 				        = 'Reports/adminAverage';

// Legacy Admin report URLs use the corrected report engine.
$route['Admin/ViewTopVisited']             = 'Reports/adminTopClinics';
$route['Admin/ViewTopConsulted']           = 'Reports/adminTopPurposes';
$route['Admin/ViewAverage']                = 'Reports/adminAverage';

$route['view-logs'] 			    	= 'Admin/ViewLogs';
$route['view-logs-s'] 			    	= 'Secretary/ViewLogs';
$route['view-logs-phy'] 			    = 'Physician/ViewLogs';
$route['table-data/(:any)']             = 'TableData/fetch/$1';

//routes for patient
$route['view-clinic-p'] 			    = 'Physician/ViewClinic';
$route['view-appointment'] 				= 'Patient/ViewAppointment';
$route['app-register'] 		            = 'Patient/AppointmentRegister';
$route['appointment-availability']          = 'Patient/AppointmentAvailability';
$route['appointments-data-patient']          = 'Patient/AppointmentsData';
$route['appointment-edit-patient']           = 'Patient/AppointmentEdit';
$route['appointment-status-patient']         = 'Patient/AppointmentStatusAjax';
$route['q-register'] 		            = 'Patient/SecQRegister';
$route['q-check'] 		            	= 'Patient/SecQValidateView';
$route['view-history-p'] 				= 'Patient/ViewHistory';
//$route['limit-register-p'] 				= 'Patient/LimitRegister';
$route['view-app-pto'] 			        = 'Patient/ViewAppointmentToday';
$route['view-app-do'] 			        = 'Patient/ViewAppointmentDone';
$route['view-app-ca'] 			        = 'Patient/ViewAppointmentCancelled';

//routes for physician - manage clinics
$route['view-clinic-p'] 			    = 'Physician/ViewClinic';
$route['clinic-register-p'] 			= 'Physician/ClinicRegister';
$route['clinic-p-edit'] 				= 'Physician/ClinicUpdate';
$route['clinic-p-edit/(\d+)'] 			= 'Physician/ClinicUpdateView/$1';
$route['clinic-p-delete/(\d+)'] 		= 'Physician/ClinicDelete/$1';
//manage schedules
$route['view-schedule-p'] 			    = 'Physician/ViewSchedule';
$route['schedule-register-p'] 			= 'Physician/ScheduleRegister';
$route['schedule-p-edit'] 				= 'Physician/ScheduleUpdate';
$route['schedule-p-edit/(\d+)'] 		= 'Physician/ScheduleUpdateView/$1';
//manage specialization
$route['view-specialization-p'] 		= 'Physician/ViewSpecialization';
$route['specialization-register-p'] 	= 'Physician/SpecializationRegister';
$route['specialization-p-edit'] 		= 'Physician/SpecializationUpdate';
$route['specialization-p-edit/(\d+)'] 	= 'Physician/SpecializationUpdateView/$1';
//manage appointment
$route['view-app-p'] 			        = 'Physician/ViewAppointment';
$route['view-app-pt'] 			        = 'Physician/ViewAppointmentToday';
$route['view-app-pd'] 			        = 'Physician/ViewAppointmentDone';
$route['view-app-pc'] 			        = 'Physician/ViewAppointmentCancelled';
$route['app-register-p'] 		        = 'Physician/AppointmentRegister';
$route['appointment-availability-p']      = 'Physician/AppointmentAvailability';
$route['appointments-data-physician']      = 'Physician/AppointmentsData';
$route['appointment-edit-physician']       = 'Physician/AppointmentEdit';
$route['appointment-status-physician']     = 'Physician/AppointmentStatusAjax';
$route['view-history-p'] 				= 'Physician/ViewHistory';
$route['limit-register-p'] 				= 'Physician/LimitRegister';
$route['view-limit-p'] 				    = 'Physician/ViewLimit';
$route['calendar-p']					= 'Physician/Calendar';
$route['calendar-events-p']              = 'Physician/CalendarEvents';
$route['limit-p-edit'] 					= 'Physician/LimitUpdate';
$route['limit-p-edit/(\d+)'] 			= 'Physician/LimitUpdateView/$1';

//routes for secretary - manage clinics
$route['view-clinic-s'] 			    = 'Secretary/ViewClinic';
$route['clinic-register-s'] 			= 'Secretary/ClinicRegister';
$route['clinic-s-edit'] 				= 'Secretary/ClinicUpdate';
$route['clinic-s-edit/(\d+)'] 			= 'Secretary/ClinicUpdateView/$1';
$route['clinic-s-delete/(\d+)'] 		= 'Secretary/ClinicDelete/$1';
//manage schedule
$route['view-schedule-s'] 				= 'Secretary/ViewSchedule';
$route['schedule-register-s'] 			= 'Secretary/ScheduleRegister';
$route['schedule-s-edit'] 				= 'Secretary/ScheduleUpdate';
$route['schedule-s-edit/(\d+)'] 		= 'Secretary/ScheduleUpdateView/$1';
//manage specialization
$route['view-specialization-s'] 		= 'Secretary/ViewSpecialization';
$route['specialization-register-s'] 	= 'Secretary/SpecializationRegister';
$route['specialization-s-edit'] 		= 'Secretary/SpecializationUpdate';
$route['specialization-s-edit/(\d+)'] 	= 'Secretary/SpecializationUpdateView/$1';
//manage appointment
$route['view-app-s'] 				    = 'Secretary/ViewAppointment';
$route['view-app-st'] 			        = 'Secretary/ViewAppointmentToday';
$route['view-app-sdone'] 			    = 'Secretary/ViewAppointmentDone';
$route['view-app-sc'] 			        = 'Secretary/ViewAppointmentCancelled';
$route['app-register-s'] 		        = 'Secretary/AppointmentRegister';
$route['appointment-availability-s']      = 'Secretary/AppointmentAvailability';
$route['appointments-data-secretary']      = 'Secretary/AppointmentsData';
$route['appointment-edit-secretary']       = 'Secretary/AppointmentEdit';
$route['appointment-status-secretary']     = 'Secretary/AppointmentStatusAjax';
$route['view-history-s'] 				= 'Secretary/ViewHistory';
$route['limit-register-s'] 				= 'Secretary/LimitRegister';
$route['view-limit-s'] 				    = 'Secretary/ViewLimit';
$route['calendar-s']					= 'Secretary/Calendar';
$route['calendar-events-s']              = 'Secretary/CalendarEvents';
$route['limit-s-edit'] 					= 'Secretary/LimitUpdate';
$route['limit-s-edit/(\d+)'] 			= 'Secretary/LimitUpdateView/$1';

//reports
$route['view-avg-s'] 				    = 'Reports/secretaryAverage';
$route['view-done-s'] 				    = 'Reports/secretaryDone';
$route['view-cancel-s'] 				= 'Reports/secretaryCancelled';

$route['view-avg-p'] 				    = 'Reports/physicianAverage';
$route['view-done-p'] 				    = 'Reports/physicianDone';
$route['view-cancel-p'] 				= 'Reports/physicianCancelled';

// Legacy report URLs are kept for compatibility but use the corrected report engine.
$route['Physician/ViewAverage']            = 'Reports/physicianAverage';
$route['Physician/ViewDone']               = 'Reports/physicianDone';
$route['Physician/ViewCancelled']          = 'Reports/physicianCancelled';
$route['Secretary/ViewAverage']            = 'Reports/secretaryAverage';
$route['Secretary/ViewDone']               = 'Reports/secretaryDone';
$route['Secretary/ViewCancelled']          = 'Reports/secretaryCancelled';

