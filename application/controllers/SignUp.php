<?php
class SignUp extends CI_Controller {

  function __construct()
  {
    parent::__construct();            

    $this->load->model('Patient_Model','Patient_Model');
    $this->load->model('Secretary_Model','Secretary_Model');
    $this->load->model('Physician_Model','Physician_Model');
    $this->load->model('Admin_Model','Admin_Model');
}   

public function patient() {

    //$userid = $this->session->userdata['userid'];
    if(!empty($_POST))
    {
       $status = "Active";
       $password = $this->security->xss_clean($this->input->post('password'));
     //$hash =  md5 ($this->input->post('password');
       $email    = $this->security->xss_clean($this->input->post('email'));
       $imgUrl = $this->uploadImage();

       $data1 = array(

         'fname'     => $this->security->xss_clean($this->input->post('fname')),
         'mname'     => $this->security->xss_clean($this->input->post('mname')),
         'lname'     => $this->security->xss_clean($this->input->post('lname')),
         'email'     => $this->security->xss_clean($this->input->post('email')),
         'password'  => $this->security->xss_clean(md5 ($this->input->post('password'))),
         'contact'   => $this->security->xss_clean($this->input->post('contact')),
         'birthdate' => $this->security->xss_clean($this->input->post('birthdate')),
         'birthplace'=> $this->security->xss_clean($this->input->post('birthplace')),
         'gender'    => $this->security->xss_clean($this->input->post('gender')),
         'address'   => $this->security->xss_clean($this->input->post('address')),
         'image'     => $imgUrl,
         'status'    => $status

     );
       $result = $this->Patient_Model->CheckExists($email);
       if($result -> num_rows() > 0)
       {
          $this->session->set_flashdata('errormsg1','This email account is already taken');
          redirect('login-p');
      }else{
     //$imgUrl = $this->uploadImage();
      //$create = $this->model_student->create($imgUrl);
          $this->Patient_Model->insertP($data1);
          $this->session->set_flashdata('SUCCESSMSG', "Patient Register Successfully!! Try logging in to your account");
          $fname = $this->session->fname;
          $lname = $this->session->lname;
          $user = 'Patient';
          $date = date("Y-m-d h:i:sa");
          $action = 'Patient registered by ' . $fname . " ".$lname;


          $data1 = array(

            'usertype' => $user,
            'userid'   => $this->session->userid,
        //'date'=> $date,
            'action'   => $action

        );
          $this->Patient_Model->addLogs($data1);

      // $data2 = array(

      //   'answer' => $this->security->xss_clean($this->input->post('answer')),
      //   'patient_id'   => $this->session->userid,
      //   'q_id'   => $this->security->xss_clean($this->input->post('question'))

      // );
      // $this->Patient_Model->addSecQ($data2);
          redirect('login-p');
      }

  }
  else
  {

     $data['getPatient'] = $this->Patient_Model->getPatient();
     $this->load->view('Patient/loginPatient');
 }
}


public function uploadImage() 
{
  $type = explode('.', $_FILES['photo']['name']);       
  $type = $type[count($type)-1];    
  $url = 'uploads/profile-pic/'.$_FILES['photo']['name'];
  $filename = $_FILES['photo']['name'];

  if(in_array($type, array('gif', 'jpg', 'jpeg', 'png', 'JPG', 'GIF', 'JPEG', 'PNG'))) {
    if(is_uploaded_file($_FILES['photo']['tmp_name'])) {      
      if(move_uploaded_file($_FILES['photo']['tmp_name'], $url)) {
        return $filename;
    } else {
        return false;
    }     
}
} 
}




public function secretary() {


  if(!empty($_POST))
  {  
     $imgUrl = $this->uploadImage(); 
     $status = "Active";
     $email    = $this->security->xss_clean($this->input->post('email'));
     $data = array(

       'fname'     => $this->security->xss_clean($this->input->post('fname')),
       'mname'     => $this->security->xss_clean($this->input->post('mname')),
       'lname'     => $this->security->xss_clean($this->input->post('lname')),
       'email'     => $this->security->xss_clean($this->input->post('email')),
       'password'  => $this->security->xss_clean(md5 ($this->input->post('password'))),
       'contact'   => $this->security->xss_clean($this->input->post('contact')),
       'birthdate' => $this->security->xss_clean($this->input->post('birthdate')),
       'birthplace'=> $this->security->xss_clean($this->input->post('birthplace')),
       'gender'    => $this->security->xss_clean($this->input->post('gender')),
       'address'   => $this->security->xss_clean($this->input->post('address')),
       'image'     => $imgUrl,
       'status'    => $status
   );

     $result = $this->Secretary_Model->CheckExists($email);
     if($result -> num_rows() > 0)
     {
        $this->session->set_flashdata('errormsg1','This email account is already taken');
        redirect('login-s');
    }else{
        $this->Secretary_Model->insert($data);
        $this->session->set_flashdata('SUCCESSMSG', "Secretary Register Successfully!! Try logging in to your account");
        $fname = $this->session->fname;
        $lname = $this->session->lname;
        $user = 'Secretary';
        $date = date("Y-m-d h:i:sa");
        $action = 'Secretary registered by ' . $fname . " ".$lname;


        $data1 = array(

          'usertype' => $user,
          'userid'   => $this->session->userid,
        //'date'=> $date,
          'action'   => $action

      );
        $this->Patient_Model->addLogs($data1);
        redirect('login-s');
    }
}
else
{

   $data['getSecretary'] = $this->Secretary_Model->getSecretary();
   $this->load->view('Secretary/loginSec');
}
}
public function physician() {


  if(!empty($_POST))
  {
    $email    = $this->security->xss_clean($this->input->post('email'));
    $status = "Active";
    $imgUrl = $this->uploadImage();
    $data = array(

       'fname'     => $this->security->xss_clean($this->input->post('fname')),
       'mname'     => $this->security->xss_clean($this->input->post('mname')),
       'lname'     => $this->security->xss_clean($this->input->post('lname')),
       'email'     => $this->security->xss_clean($this->input->post('email')),
       'password'  => $this->security->xss_clean(md5 ($this->input->post('password'))),
       'contact'   => $this->security->xss_clean($this->input->post('contact')),
       'birthdate' => $this->security->xss_clean($this->input->post('birthdate')),
       'birthplace'=> $this->security->xss_clean($this->input->post('birthplace')),
       'gender'    => $this->security->xss_clean($this->input->post('gender')),
       'address'   => $this->security->xss_clean($this->input->post('address')),
       'image'     => $imgUrl,
       'status'    => $status
   );
    $result = $this->Physician_Model->CheckExists($email);
    if($result -> num_rows() > 0)
    {
      $this->session->set_flashdata('errormsg1','This email account is already taken');
      redirect('login-phy');
  }else{
   $this->Physician_Model->insert($data);
         //for the specialization table
   $data1 = array(

      'special_name' => $this->security->xss_clean($this->input->post('special_name'))    
  );
   $this->Physician_Model->insert_special($data1);
         //for physician_special table
   $physician_id = $this->db->query('SELECT MAX(physician_id) AS `maxid` FROM `physician`')->row()->maxid;
   $special = $this->db->query('SELECT MAX(special_id) AS `maxid` FROM `specialization`')->row()->maxid;
   $data2 = array(

      'special_id' => $special,
      'physician_id' =>$physician_id
  );

   $this->Physician_Model->insert_pspecial($data2);

   $data3 = array(

     'day' => $this->security->xss_clean($this->input->post('day')),
     'time_in' => $this->security->xss_clean($this->input->post('time_in')),
     'time_out' => $this->security->xss_clean($this->input->post('time_out'))

 );
   $this->Physician_Model->insert_schedule($data3);

   $schedule = $this->db->query('SELECT MAX(schedule_id) AS `maxid` FROM `schedule`')->row()->maxid;
   $data4 = array(

      'schedule_id' => $schedule,
      'physician_id' =>$physician_id
  ); 
   $this->Physician_Model->insert_pschedule($data4);  

   $this->session->set_flashdata('SUCCESSMSG', "Physician Register Successfully!! Try logging in to your account");

   $fname = $this->session->fname;
   $lname = $this->session->lname;
   $user = 'Physician';
   $date = date("Y-m-d h:i:sa");
   $action = 'Physician registered by ' . $fname . " ".$lname;


   $data5 = array(

      'usertype' => $user,
      'userid'   => $this->session->userid,
        //'date'=> $date,
      'action'   => $action

  );
   $this->Patient_Model->addLogs($data5);
   redirect('login-phy');
}
}
else
{

 $data['getPhysician'] = $this->Physician_Model->getPhysician();
 $this->load->view('Physician/LoginPhysician');
}
}

public function admin() {


  if(!empty($_POST))
  {
    // $password = $this->input->post('password');
    // $encrypt_password = password_hash($password,PASSWORD_DEFAULT);
     $imgUrl = $this->uploadImage();
     $data = array(

        'fname'     => $this->security->xss_clean($this->input->post('fname')),
        'mname'     => $this->security->xss_clean($this->input->post('mname')),
        'lname'     => $this->security->xss_clean($this->input->post('lname')),
        'email'     => $this->security->xss_clean($this->input->post('email')),
        'password'  => $this->security->xss_clean(md5 ($this->input->post('password'))),
        'contact'   => $this->security->xss_clean($this->input->post('contact')),
        'birthdate' => $this->security->xss_clean($this->input->post('birthdate')),
        'birthplace'=> $this->security->xss_clean($this->input->post('birthplace')),
        'gender'    => $this->security->xss_clean($this->input->post('gender')),
        'address'   => $this->security->xss_clean($this->input->post('address')),
        'image'     => $imgUrl

    );
     $result = $this->Admin_Model->CheckExists($email);
     if($result -> num_rows() > 0)
     {
        $this->session->set_flashdata('errormsg1','This email account is already taken');
        redirect('login-a');
    }else{

        $this->Admin_Model->insert($data);
        $this->session->set_flashdata('SUCCESSMSG', "Admin Register Successfully!! Try logging in to your account");
        redirect('login-a');
    }
    
}
else
{

   $data['getAdmin'] = $this->Admin_Model->getAdmin();
   $this->load->view('Admin/loginAdmin');
}
}

}

?>
