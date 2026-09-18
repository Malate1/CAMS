<?php
class Patient extends CI_Controller {

  function __construct()
  {
    parent::__construct();            
    if($this->session->email == "" || $this->session->role !== 'Patient')
    {
      redirect('login-p');
      return;
  }
  $this->load->model('Patient_Model','Patient_Model');
  $this->load->model('Physician_Model','Physician_Model');
  $this->load->model('Secretary_Model','Secretary_Model');
  $this->load->model('Dashboard_Model','Dashboard_Model');
}   

private function ajaxTransactionResponse($success, $message, $redirect = '', array $extra = array())
{
  if (!$this->input->is_ajax_request()) return false;
  $payload = array_merge(array(
    'success' => (bool) $success,
    'message' => (string) $message,
    'redirect' => $redirect,
    'csrf' => array('name' => $this->security->get_csrf_token_name(), 'hash' => $this->security->get_csrf_hash()),
  ), $extra);
  $this->output->set_status_header($success ? 200 : 422)->set_content_type('application/json')->set_output(json_encode($payload));
  return true;
}

public function index() {
    $data['count'] = $this->Patient_Model->Count();
    $data['countD'] = $this->Patient_Model->CountDone();
    $data['countC'] = $this->Patient_Model->CountCancel();
    $userid = $this->session->userdata['userid'];
    $data['records'] = $this->Patient_Model->view_info($userid);
    $data['dashboard'] = $this->Dashboard_Model->patientDashboard($userid);
    $this->load->view('Patient/dashboard_patient', $data);


}



public function AppointmentRegister(){
    $this->load->model('Appointment_Model', 'Appointment_Model');

    if (empty($_POST)) {
      $data['bookingProviders'] = $this->Appointment_Model->getBookingProviders();
      $data['complaints'] = $this->Appointment_Model->getComplaintCatalog();
      $data['specialties'] = $this->Appointment_Model->getActiveSpecialties();
      $this->load->view('Patient/Appointment_add', $data);
      return;
    }

    $result = $this->Appointment_Model->book(array(
      'patient_id' => (int) $this->session->userdata['userid'],
      'physician_id' => (int) $this->input->post('physician_id'),
      'clinic_id' => (int) $this->input->post('clinic_id'),
      'app_date' => trim((string) $this->input->post('app_date')),
      'purpose' => $this->security->xss_clean($this->input->post('purpose')),
      'specialty_name' => $this->security->xss_clean($this->input->post('specialty_name')),
      'enforce_specialty' => true,
    ));

    if (!$result['success']) {
      if ($this->ajaxTransactionResponse(false, $result['message'])) return;
      $this->session->set_flashdata('error1', $result['message']);
      redirect('app-register');
      return;
    }

    $fname = $this->session->fname;
    $lname = $this->session->lname;
    $this->Patient_Model->addLogs(array(
      'usertype' => 'Patient',
      'userid' => $this->session->userid,
      'action' => 'Appointment added by ' . $fname . ' ' . $lname,
    ));

    if (!empty($result['patient_contact'])) {
      $body = 'Hi ' . $result['patient_fname'] . ', your queue # is ' . $result['queue_number'] . ' and your appointment will be on ' . $result['app_date'] . '.';
      $message = 'From: ' . $result['clinic_name'] . "\n\n" . $body;
      $this->Patient_Model->itexmo($result['patient_contact'], $message, 'TR-CAMSR018596_LKKSD');
    }

    $successMessage = 'Appointment successfully added. Your queue number is ' . $result['queue_number'] . '.';
    if ($this->ajaxTransactionResponse(true, $successMessage, base_url('view-appointment'), array('queue_number' => $result['queue_number']))) return;
    $this->session->set_flashdata('SUCCESSMSG', $successMessage);
    redirect('view-appointment');
  }

public function AppointmentAvailability()
  {
    $this->load->model('Appointment_Model', 'Appointment_Model');

    $physicianId = (int) $this->input->get('physician_id');
    $clinicId = (int) $this->input->get('clinic_id');
    $dates = $this->Appointment_Model->getAvailableDates($physicianId, $clinicId);

    $this->output
      ->set_content_type('application/json')
      ->set_output(json_encode(array('success' => true, 'dates' => $dates)));
  }

private function AppointmentRegisterLegacy(){
    if(!empty($_POST))
    {
      $default = $this->input->post('default');
      $app_date = $this->input->post('app_date');
      $app_date = str_replace('/', '-', $app_date);
        // $app_date = date('Y-m-d', strtotime($app_date));

        // echo $app_date; die();

      $status = "Pending";

      $c_date = date("Y-m-d H:i:s");
      $userid = $this->session->userdata['userid'];
      $physician_id = $this->input->post('physician_id');
      $clinic_id   = $this->input->post('clinic_id');
      $sql = "SELECT MAX(queueNum) AS `queue` FROM `appointment` WHERE physician_id = $physician_id  AND app_date = '$app_date' AND clinic_id = '$clinic_id'";
      $queue =  $this->db->query($sql)->row()->queue;
      $query =  "SELECT Max(queueLimit) as `queueLimit` FROM `queuecount` WHERE physician_id = $physician_id AND dateLimit = '$app_date'";
      $limit =  $this->db->query($query)->row()->queueLimit;


      $dayQuery =  "SELECT * FROM `physician_sched`
      JOIN physician on physician.physician_id = physician_sched.physician_id
      JOIN schedule on schedule.schedule_id = physician_sched.schedule_id
      WHERE physician_sched.physician_id = $physician_id";
      $day_sched = $this->db->query($dayQuery)->row()->day;
      $dayofweek = date('w', strtotime($app_date));
      $dayBool = false;
      $case = "";
      switch((string)$day_sched)
      {
        case "M-F": if((int)$dayofweek != 6 && (int)$dayofweek != 0) {
          $dayBool = true;
      }
      break;

      case "MTW": if((int)$dayofweek == 1 || (int)$dayofweek == 2 || (int)$dayofweek == 3) {
          $dayBool = true;
      }
      break;

      case "TTh": if((int)$dayofweek == 2 || (int)$dayofweek == 4) {
          $dayBool = true;
      }
      break;

      case "Sat": if((int)$dayofweek == 6) {
          $dayBool = true;
      }
      break;

      case "Sun": if((int)$dayofweek == 0) {
          $dayBool = true;
      }
      break;

      case "MWF": if((int)$dayofweek == 1 || (int)$dayofweek == 3 || (int)$dayofweek == 5) {
          $dayBool = true;
      }
      break;

      case "ThF": if((int)$dayofweek == 4 || (int)$dayofweek == 5) {
          $dayBool = true;
      }
      break;

  }
///////////RESULTS AFTER COMPARING/////////////////////

  if($dayBool) {

    if ($limit == 0){
        //kung zero limit, automatic add dayun sa database ang 20 para dili na mag manually set limit
      $limit = 20;


      $data = array(
        'queueLimit'      => $limit,
        'physician_id'    => $physician_id,
        'dateLimit'            => $app_date
    );
      $this->Secretary_Model->insert_limit($data);

  }


        /*if ($limit == 0){
          $limit = 20; // set this value via admin in the application: tools > add set default limit
        }
*/

        $fn = $this->db->query("SELECT fname FROM `patient` WHERE patient_id = $userid")->row();

        $clinic = $this->db->query("SELECT name FROM `clinic` WHERE clinic_id = $clinic_id")->row();
       /* $number =  "SELECT contact FROM `patient` WHERE patient_id = $userid";
       $clinic =  "SELECT name FROM `clinic` WHERE clinic_id = $clinic_id";*/


       $fname_result = $fn->fname;
       /* $num_result =  $number->contact; */
       $clinic_result = $clinic->name; 

        //echo $limit; die();

       if($queue >= $limit ){

        $this->session->set_flashdata('error1', "Please try again for another date appointment is already full for this date: " .$this->input->post('app_date'));
        $data['getDirectory'] = $this->Patient_Model->getDirectory();   
        $this->load->view('Patient/Appointment_add',$data);
        redirect('app-register');
    }
    else{

     $data = array(
       'app_date'       => $app_date,
       'purpose'        => $this->security->xss_clean($this->input->post('purpose')),
       'date_created'   => $c_date,
       'patient_id'     => $userid,
       'physician_id'   => $this->security->xss_clean($this->input->post('physician_id')),
       'clinic_id'      => $this->security->xss_clean($this->input->post('clinic_id')),
       'queueNum'       => $queue+1,
       'app_status'     => $status
   );


       // print_r($sql); die();
     $this->Patient_Model->insert($data);

     $fname = $this->session->fname;
     $lname = $this->session->lname;
     $user = 'Patient';
     $date = date("Y-m-d h:i:sa");
     $action = 'Appointment added by ' . $fname . " ".$lname;


     $data1 = array(

       'usertype' => $user,
       'userid'   => $this->session->userid,
       //'date'=> $date,
       'action'   => $action

   );
     $this->Patient_Model->addLogs($data1);

     $body = 'Hi '.$fname_result.' your queue # is '.$queue.' and your appointment will be on '.$app_date.'.';
     $message = "From: ".$clinic_result."\n\n".$body."";

     $data = $this->Patient_Model->getRecepient($userid);
     $theNum = "";
     foreach($data as $key)
     {
        $theNum =  $key->contact;

    }


    $this->Patient_Model->itexmo( $theNum, $message,'TR-CAMSR018596_LKKSD');

    $this->session->set_flashdata('SUCCESSMSG', "Appointment Successfully Added!! SMS sent to ".$theNum);

    redirect('view-appointment');
}
}else {
    $this->session->set_flashdata('error', "The doctor won't be here in this chosen day");
    $data['getPatient'] = $this->Patient_Model->getPatient();
    $this->load->view('Patient/Appointment_add', $data);
    //redirect('app-register');
}//end of else
}
else
{

        //$this->ViewDirectory();
  /*$special = $this->input->post('specialization');*/
  $data['getDirectory'] = $this->Patient_Model->getDirectory();   
  $this->load->view('Patient/Appointment_add',$data);
}
}


public function ViewAppointment()
{
  $this->load->view('Patient/Appointment_view', array('appointmentScope' => 'all'));
}

public function ViewAppointmentToday()
{
  $this->load->view('Patient/Appointment_view', array('appointmentScope' => 'today'));
}

public function ViewAppointmentDone()
{
  $this->load->view('Patient/Appointment_view', array('appointmentScope' => 'done'));
}

public function ViewAppointmentCancelled()
{
  $this->load->view('Patient/Appointment_view', array('appointmentScope' => 'cancelled'));
}

public function AppointmentsData()
{
  $this->load->model('Appointment_Model', 'Appointment_Model');
  $scope = (string) $this->input->get('scope', true);
  $result = $this->Appointment_Model->getAppointmentsData('patient', (int) $this->session->userdata['userid'], $this->input->get(null, true) ?: array(), $scope);
  return $this->jsonResponse($result);
}

public function AppointmentEdit()
{
  $this->load->model('Appointment_Model', 'Appointment_Model');
  $appointmentId = (int) ($this->input->method(true) === 'GET' ? $this->input->get('id', true) : $this->input->post('appointment_id', true));

  if ($this->input->method(true) === 'GET') {
    $appointment = $this->Appointment_Model->getEditableAppointment($appointmentId, 'patient', (int) $this->session->userdata['userid']);
    if (!$appointment) {
      return $this->jsonResponse(array('success' => false, 'message' => 'This appointment is no longer editable.'), 404);
    }
    return $this->jsonResponse(array('success' => true, 'appointment' => $appointment));
  }

  $result = $this->Appointment_Model->updateAppointmentAuthorized(
    $appointmentId,
    trim((string) $this->input->post('app_date', true)),
    $this->security->xss_clean($this->input->post('purpose')),
    'patient',
    (int) $this->session->userdata['userid']
  );

  if ($result['success']) {
    $this->Patient_Model->addLogs(array(
      'usertype' => 'Patient',
      'userid' => $this->session->userid,
      'action' => 'Appointment #' . $appointmentId . ' updated by ' . $this->session->fname . ' ' . $this->session->lname,
    ));
    $result['message'] = 'Appointment updated successfully.';
  }
  return $this->jsonResponse($result, $result['success'] ? 200 : 422);
}

public function AppointmentStatusAjax()
{
  $this->load->model('Appointment_Model', 'Appointment_Model');
  $appointmentId = (int) $this->input->post('appointment_id', true);
  $updated = $this->Appointment_Model->updateStatusAuthorized($appointmentId, 'Cancelled', 'patient', (int) $this->session->userdata['userid']);
  if ($updated) {
    $this->Patient_Model->addLogs(array(
      'usertype' => 'Patient',
      'userid' => $this->session->userid,
      'action' => 'Appointment #' . $appointmentId . ' cancelled by ' . $this->session->fname . ' ' . $this->session->lname,
    ));
  }
  return $this->jsonResponse(array(
    'success' => $updated,
    'message' => $updated ? 'Appointment cancelled successfully.' : 'The appointment could not be cancelled.',
  ), $updated ? 200 : 422);
}

private function jsonResponse(array $payload, $status = 200)
{
  $payload['csrf'] = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash(),
  );
  return $this->output
    ->set_status_header($status)
    ->set_content_type('application/json', 'utf-8')
    ->set_output(json_encode($payload));
}


// public function SecQValidate()
// {

//     $answer = $this->security->xss_clean($this->input->post('answer'));  
//     $q_id   = $this->security->xss_clean($this->input->post('question')); 


//     $result = $this->Patient_Model->CheckAnswer($answer, $q_id);
//         if($result -> num_rows() > 0)
//         {
//             redirect('login-p');     
//         }else{
//             $this->session->set_flashdata('errormsg1','Your answer is incorrect');
//             redirect('login-p');      
//         }
// }

public function SecQRegister()
{


    if(!empty($_POST))
    {  

        $email = $this->session->userdata['email'];

        $data = array(
            'answer'        => $this->security->xss_clean($this->input->post('answer')),
            'email'         => $email,
            'patient_id'    => $this->session->userid,
            'q_id'          => $this->security->xss_clean($this->input->post('question'))    
        );
        $result = $this->Patient_Model->CheckExists2($email);
        if($result -> num_rows() > 0)
        {
          $this->session->set_flashdata('errormsg1','You can only set up Security Question once');
          redirect('q-register');
      }else{

                //$newLimit = $this->security->xss_clean($this->input->post('limit'));
        $this->Patient_Model->insert_secQ($data);

        $fname = $this->session->fname;
        $lname = $this->session->lname;
        $user = 'Patient';

        $action = 'Security question added by ' . $fname . " ".$lname;


        $data1 = array(

            'usertype' => $user,
            'userid'   => $this->session->userid,
                        //'date'=> $date,
            'action'   => $action

        );
        $this->Patient_Model->addLogs($data1);

        $this->session->set_flashdata('SUCCESSMSG', "Security Question Registered Successfully!!");
        redirect('q-register');
    }
}else{
    $data['getPatient'] = $this->Patient_Model->getPatient();
    $this->load->view('Patient/SecQ_add',$data);
}

}

public function Change_pass ()
{
  $this->load->library('form_validation');

  $this->form_validation->set_rules('oldPassword','Old password','required|max_length[20]');
  $this->form_validation->set_rules('newPassword','New password','required|max_length[20]');
  $this->form_validation->set_rules('cNewPassword','Confirm new password','required|matches[newPassword]|max_length[20]');

  if($this->form_validation->run() == FALSE)
  {
     $validationMessage = trim(strip_tags(validation_errors(' ', ' ')));
     if ($this->ajaxTransactionResponse(false, $validationMessage ?: 'Please review the password fields and try again.')) return;
     $this->load->view('Patient/Change_pass');
 }
 else
 {

  $patient_id = $this->session->userid;
  $old = md5($this->input->post('oldPassword'));
          //$old = $this->input->post('oldPassword');
  $info = array(
    'password' => $this->security->xss_clean(md5($this->input->post('newPassword'))),

);
  $query = $this->Patient_Model->CheckOld($patient_id,$old);
  if($query -> num_rows() > 0)
  { 

    $result = $this->Patient_Model->changePassword($patient_id, $info);     
    $fname = $this->session->fname;
    $lname = $this->session->lname;
    $user = 'Patient';
    $date = date("Y-m-d h:i:sa");
    $action = 'Password changed by ' . $fname . " ".$lname;


    $data1 = array(

       'usertype' => $user,
       'userid'   => $this->session->userid,
       //'date'=> $date,
       'action'   => $action

   );
    $this->Patient_Model->addLogs($data1);
    $successMessage = 'Password updated successfully.';
    if ($this->ajaxTransactionResponse(true, $successMessage)) return;
    $this->session->set_flashdata('success', $successMessage);
    redirect('change-pass-p');

}else{


    if ($this->ajaxTransactionResponse(false, 'Old password is incorrect.')) return;
    $this->session->set_flashdata('errormsg','Old password is incorrect');
    $this->load->view('Patient/Change_pass');

}

}
}

public function ProfileUpdateView() {

  $this->load->helper('form');
  $patient_id = $this->session->userid;
  $query = $this->db->get_where("patient",array("patient"=>$patient_id));
  $data['records'] = $query->result();

  $this->load->view('Patient/Update_profile',$data);
}

public function ProfileUpdate()
{
  if(!empty($_POST))
  {

     //$imgUrl = $this->EdituploadImage();
   $data = array(

     'fname'     => $this->security->xss_clean($this->input->post('fname')),
     'mname'     => $this->security->xss_clean($this->input->post('mname')),
     'lname'     => $this->security->xss_clean($this->input->post('lname')),

     'contact'   => $this->security->xss_clean($this->input->post('contact')),
     'birthdate' => $this->security->xss_clean($this->input->post('birthdate')),
     'birthplace'=> $this->security->xss_clean($this->input->post('birthplace')),
     'gender'    => $this->security->xss_clean($this->input->post('gender')),
     'address'   => $this->security->xss_clean($this->input->post('address')),
         //'image'   => $imgUrl
 );
   $patient_id = $this->session->userid;
   $this->Patient_Model->updateProfile($data,$patient_id);


   $fname = $this->session->fname;
   $lname = $this->session->lname;
   $user = 'Patient';
   $date = date("Y-m-d H:i:s");
   $action = 'Profile updated by ' . $fname . " ".$lname;

   $data1 = array(

       'usertype' => $user,
       'userid'   => $this->session->userid,
       //'date'=> $date,
       'action'   => $action

   );
   $this->Patient_Model->addLogs($data1);

   $this->session->set_userdata($data);

   $successMessage = 'Profile updated successfully.';
   if ($this->ajaxTransactionResponse(true, $successMessage, '', array(
       'profile' => array(
           'display_name' => trim($data['fname'] . ' ' . $data['lname']),
       ),
   ))) return;

   $this->session->set_flashdata('success', $successMessage);
   redirect('update-profile-p');


}
else
{
    $patient_id = $this->session->userid;
        //$this->Admin_Model->updateProfile($data,$admin_id);
    $data['records'] = $this->Patient_Model->view_info($patient_id);
    $this->load->view('Patient/Update_profile',$data);
}
}

public function ProfilePicUpdateView() {

    $this->load->helper('form');
    $patient_id = $this->session->userid;
    $query = $this->db->get_where("patient",array("patient"=>$patient_id));
    $data['records'] = $query->result();

    $this->load->view('Patient/Update_profile',$data);
}

public function ProfilePicUpdate()
{
    if(!empty($_POST))
    { 
        $imgUrl = $this->EdituploadImage();
        if (!$imgUrl) {
            if ($this->ajaxTransactionResponse(false, 'Please select a valid image file.')) return;
            $this->session->set_flashdata('error', 'Please select a valid image file.');
            redirect('update-profile-p');
            return;
        }
        $data = array(

            'image'   => $imgUrl
        );

        $patient_id = $this->session->userid;
        $this->Patient_Model->updateProfilePic($data,$patient_id);
        $this->session->set_userdata($data);
        $fname = $this->session->fname;
        $lname = $this->session->lname;
        $user = 'Patient';
        $date = date("Y-m-d h:i:sa");
        $action = 'Profile picture updated by ' . $fname . " ".$lname;


        $data1 = array(

            'usertype' => $user,
            'userid'   => $this->session->userid,
            //'date'=> $date,
            'action'   => $action

        );
        $this->Patient_Model->addLogs($data1);

        $successMessage = 'Profile photo updated successfully.';
        if ($this->ajaxTransactionResponse(true, $successMessage, '', array(
            'profile' => array(
                'image_url' => base_url('uploads/profile-pic/' . rawurlencode($imgUrl)),
            ),
        ))) return;

        $this->session->set_flashdata('success', $successMessage);
        redirect('update-profile-p');


    }
    else
    {
        $patient_id = $this->session->userid;               
        $data['records'] = $this->Patient_Model->view_info($patient_id);
        $this->load->view('Patient/Update_profile',$data);
    }
}

public function EdituploadImage() 
{
    $type = explode('.', $_FILES['Editphoto']['name']);       
    $type = $type[count($type)-1];    
    $url = 'uploads/profile-pic/'.$_FILES['Editphoto']['name'];
    $filename = $_FILES['Editphoto']['name'];

    if(in_array($type, array('gif', 'jpg', 'jpeg', 'png', 'JPG', 'GIF', 'JPEG', 'PNG'))) {
      if(is_uploaded_file($_FILES['Editphoto']['tmp_name'])) {      
        if(move_uploaded_file($_FILES['Editphoto']['tmp_name'], $url)) {
            return $filename;
        } else {
            return false;
        }     
    }
} 
}

public function ViewDirectory()
{
  $special = $this->input->post('specialization');
  $data['getDirectory'] = $this->Patient_Model->getDirectory($special);   
  $this->load->view('Patient/Appointment_add',$data);
}

public function UpdateStatus()
{
  if (empty($_POST)) {
    redirect('view-appointment');
    return;
  }

  $this->load->model('Appointment_Model', 'Appointment_Model');
  $updated = $this->Appointment_Model->updateStatusAuthorized(
    (int) $this->input->post('appointment_id'),
    (string) $this->input->post('status'),
    'patient',
    (int) $this->session->userdata['userid']
  );

  if ($updated) {
    $this->Patient_Model->addLogs(array(
      'usertype' => 'Patient',
      'userid' => $this->session->userid,
      'action' => 'Appointment #' . (int) $this->input->post('appointment_id') . ' cancelled by ' . $this->session->fname . ' ' . $this->session->lname,
    ));
    $this->session->set_flashdata('SUCCESSMSG1', 'Appointment status updated successfully.');
  } else {
    $this->session->set_flashdata('error', 'The appointment could not be updated. It may already be completed/cancelled or it does not belong to your account.');
  }

  redirect('view-appointment');
}

private function UpdateStatusLegacy()
{
  if(!empty($_POST))
  {
     $data = array(

        'app_status'       => $this->security->xss_clean($this->input->post('status'))
    );
     $old_app_id = $this->input->post('appointment_id');
     $this->Patient_Model->updateStatus($data,$old_app_id);
     $this->session->set_flashdata('SUCCESSMSG1', "Appointment Status Updated Successfully!!");
     redirect('view-appointment');
 }
 else
 {

  $old_app_id = $this->security->xss_clean($this->input->post('appointment_id'));
  $this->Patient_Model->updateStatus($data,$old_app_id);
  $data['getAppointment'] = $this->Patient_Model->getAppointment();
  $this->load->view('Patient/Appointment_view',$data);
}
}

}
?>
