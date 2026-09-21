<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	function __construct()
    {
        parent::__construct();				
        $this->load->model('LoginModel','LoginModel');
        $this->load->model('Patient_Model','Patient_Model');
        $this->load->model('PasswordReset_Model','PasswordReset_Model');
        $this->PasswordReset_Model->ensureSchema();
        
    } 

    public function index()
    {
        $this->load->model('Landing_Model', 'Landing_Model');

        $scheduleRows = $this->Landing_Model->physicianClinicSchedules();
        $clinics = array();
        $physicianIds = array();

        foreach ($scheduleRows as $row) {
            $clinicId = (int) $row->clinic_id;
            $physicianId = (int) $row->physician_id;
            $physicianIds[$physicianId] = true;

            if (!isset($clinics[$clinicId])) {
                $clinics[$clinicId] = array(
                    'clinic_id' => $clinicId,
                    'name' => (string) $row->clinic_name,
                    'location' => (string) $row->clinic_location,
                    'contact' => (string) $row->clinic_contact,
                    'physicians' => array(),
                );
            }

            if (!isset($clinics[$clinicId]['physicians'][$physicianId])) {
                $clinics[$clinicId]['physicians'][$physicianId] = array(
                    'physician_id' => $physicianId,
                    'name' => trim((string) $row->fname . ' ' . (string) $row->lname),
                    'image' => (string) $row->image,
                    'schedules' => array(),
                );
            }

            $clinics[$clinicId]['physicians'][$physicianId]['schedules'][] = array(
                'schedule_id' => (int) $row->schedule_id,
                'day' => (string) $row->day,
                'time_in' => (string) $row->time_in,
                'time_out' => (string) $row->time_out,
            );
        }

        foreach ($clinics as &$clinic) {
            $clinic['physicians'] = array_values($clinic['physicians']);
        }
        unset($clinic);

        $this->load->view('loginPage', array(
            'landingClinics' => array_values($clinics),
            'landingScheduleCount' => count($scheduleRows),
            'landingPhysicianCount' => count($physicianIds),
        ));
    }

    /**
     * Clear stale login/recovery flash feedback after successful authentication.
     */
    private function clearLoginFeedback()
    {
        foreach (array(
            'errormsg',
            'errormsg1',
            'errormsg2',
            'errorR',
            'errorR1',
            'successR',
            'success',
            'SUCCESSMSG'
        ) as $key) {
            $this->session->unset_userdata($key);
        }
    }

    private function generateTemporaryPassword($length = 12)
    {
        $length = max(10, min(20, (int) $length));
        $lower = 'abcdefghijkmnopqrstuvwxyz';
        $upper = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $digits = '23456789';
        $special = '!@#$%?';
        $all = $lower . $upper . $digits . $special;

        $password = array(
            $lower[random_int(0, strlen($lower) - 1)],
            $upper[random_int(0, strlen($upper) - 1)],
            $digits[random_int(0, strlen($digits) - 1)],
            $special[random_int(0, strlen($special) - 1)],
        );

        while (count($password) < $length) {
            $password[] = $all[random_int(0, strlen($all) - 1)];
        }

        for ($i = count($password) - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            $tmp = $password[$i];
            $password[$i] = $password[$j];
            $password[$j] = $tmp;
        }

        return implode('', $password);
    }

    public function SecQValidateView()
    {

        if(!empty($_POST))
        {    

            $email = $this->security->xss_clean($this->input->post('email'));  
            $answer = $this->security->xss_clean($this->input->post('answer'));  
            $q_id   = $this->security->xss_clean($this->input->post('question')); 
   
            $result = $this->Patient_Model->CheckExists2($email);
            if($result -> num_rows() > 0)
            {
                $result1 = $this->Patient_Model->CheckAnswer($answer, $q_id,$email);
                if($result1 -> num_rows() > 0)
                {
                    $email = $this->security->xss_clean($this->input->post('email'));
                    $password = $this->generateTemporaryPassword(12);
                    //$defaultPass = '123malate';
                    $data = array(

                        'password'     => $this->security->xss_clean(md5($password))

                    );
                //$patient_id = $this->input->post('old_patient_id');
                    $this->Patient_Model->updateRecovery($data,$email);
                    $patientRow = $this->db->select('patient_id')->where('email', $email)->get('patient')->row();
                    if ($patientRow) {
                        $this->PasswordReset_Model->setRequired('patient', (int) $patientRow->patient_id, true);
                    }
                    $this->session->set_flashdata('successR','Your temporary password is: ' .$password . '. Please change it after signing in.');
                    $this->load->view('Patient/loginPatient');
            //redirect('login-p');  

                }else{

                    $this->session->set_flashdata('errorR','Your security question or your answer is incorrect');
                    $this->load->view('Patient/loginPatient');

                }   
            }else
            {
                $this->session->set_flashdata('errorR1','Email address is not valid for recovery');
                $this->load->view('Patient/loginPatient');      
            }
        }else{
            $data['getPatient'] = $this->Patient_Model->getPatient();
            $this->load->view('Patient/SecQ_add',$data);
        }

    }   

    public function logAdmin()
    {
      if(!empty($_POST))
      {
        $email = $this->security->xss_clean($this->input->post('email'));
        $password = $this->security->xss_clean(md5($this->input->post('password')));
            //$password = $this->input->post('password');
        $result = $this->LoginModel->loginAdmin($email,$password);

        if($result -> num_rows() > 0 )
        {
            $this->clearLoginFeedback();

            foreach ($result->result() as $row)
            {
                $this->session->userid = $row->admin_id;
                $this->session->email =  $row->email;
                $this->session->fname =  $row->fname;
                $this->session->lname =  $row->lname;
                $this->session->image =  $row->image;
                $this->session->role = 'Admin';

                $fname = $this->session->fname;
                $lname = $this->session->lname;
                $user = 'Admin';
                $userid = $this->session->userid;
                $date = date("Y-m-d h:i:sa");
                $action = 'Logged in ' . $fname . " ".$lname;


                $data1 = array(

                    'usertype' => $user,
                    'userid'   => $userid,
                        //'date'=> $date,
                    'action'   => $action

                );
                $this->Patient_Model->addLogs($data1);

                redirect('profile');
            }
        }
        else
        {
            $data['email'] = $email;
            $data['password'] = $password;
            $this->session->set_flashdata('errormsg','Email and Password is Wrong');
            $this->load->view('Admin/loginAdmin',$data);
        }
    }
    else
    {
       $this->load->view('Admin/loginAdmin');
   }
}

public function logPatient()
{
    if(!empty($_POST))
    {
        $email = $this->security->xss_clean($this->input->post('email'));
        $password = $this->security->xss_clean(md5($this->input->post('password')));
            //$password = $this->input->post('password');
        $result = $this->LoginModel->loginPatient($email,$password);
        if($result -> num_rows() > 0)
        {
            foreach ($result->result() as $row)
            {
                $this->session->userid = $row->patient_id;
                $this->session->email =  $row->email;
                $this->session->fname =  $row->fname;
                $this->session->lname =  $row->lname;
                $this->session->status =  $row->status;
                $this->session->image =  $row->image;
                $this->session->must_change_password = isset($row->must_change_password) ? (int) $row->must_change_password : 0;



                if ($this->session->status == "Inactive") {
                    $this->session->set_flashdata('errormsg1','This account is deactivated');
                    $this->load->view('Patient/loginPatient');
                }
                else{
                    $this->clearLoginFeedback();

                    $this->session->role = 'Patient';
                    $fname = $this->session->fname;
                    $lname = $this->session->lname;
                    $user = 'Patient';
                    $userid = $this->session->userid;
                    $date = date("Y-m-d h:i:sa");
                    $action = 'Logged in ' . $fname . " ".$lname;


                    $data1 = array(

                        'usertype' => $user,
                        'userid'   => $userid,
                        //'date'=> $date,
                        'action'   => $action

                    );
                    $this->Patient_Model->addLogs($data1);
                    redirect('profile-p');
                }

            }
        }
        else
        {
            $ctr ="3";
            $tries = $this->session->set_flashdata('errormsg','Email and Password is Wrong');
            $data['email'] = $email;
            $data['password'] = $password;
            $this->session->set_flashdata('errormsg','Email and Password is Wrong');
            $this->load->view('Patient/loginPatient',$data);
                //$tries = $ctr;
            if ($tries > $ctr){
                $this->session->set_flashdata('errormsg2','You reach the maximum number of tries allowed');
                $this->load->view('Patient/loginPatient',$data);
            }
        }
    }
    else
    {
        $this->load->view('Patient/loginPatient');
    }
}

public function checkPatient()
{
    if(!empty($_POST))
    {
        $email = $this->input->post('email');
            //$password = md5($this->input->post('password'));
        $patient_id = $this->input->post('patient_id');
        $result = $this->LoginModel->checkPatient($patient_id,$email);
        if($result -> num_rows() > 0)
        {


            $this->checkAnswer($patient_id);
            redirect('check-q');

        }
        else
        {
            $data['email'] = $email;
            $data['patient_id'] = $patient_id;
            $this->session->set_flashdata('errormsg','Email and ID is Wrong');
            $this->load->view('Patient/checkPatient',$data);
            
        }

    }
    else
    {
        $this->load->view('Patient/checkPatient');
    }
}

public function checkAnswer()
{
    if(!empty($_POST))
    {

        $answer = $this->input->post('answer');
            //$password = md5($this->input->post('password'));
        $patient_id = $this->input->post('patient_id');
        $result = $this->LoginModel->checkAnswer($answer,$patient_id);
        $q_result = $result->question;
        if($result -> num_rows() > 0)
        {
            foreach ($result->result() as $row)
            {
                $this->session->userid = $row->patient_id;
                $this->session->email =  $row->email;
                $this->session->fname =  $row->fname;
                $this->session->lname =  $row->lname;
                $this->session->status =  $row->status;
                $this->session->question =  $row->question;
                    //$this->question =  $row->question;


                if ($this->session->status == "Inactive") {
                    $this->session->set_flashdata('errormsg1','This account is deactivated');
                    $this->load->view('Secretary/loginSec');
                }
                else{

                    $this->session->role = 'Patient';
                    $fname = $this->session->fname;
                    $lname = $this->session->lname;
                    $user = 'Patient';
                    $userid = $this->session->userid;
                    $date = date("Y-m-d h:i:sa");
                    $action = 'Logged in ' . $fname . " ".$lname;


                    $data1 = array(

                        'usertype' => $user,
                        'userid'   => $userid,
                        //'date'=> $date,
                        'action'   => $action

                    );
                    $this->Patient_Model->addLogs($data1);    
                    $result = $this->LoginModel->getQuestion($patient_id);
                    

                    redirect('profile-p');
                }
            }


        }
        else
        {
            $data['answer'] = $answer;
            $data['patient_id'] = $patient_id;
            $this->session->set_flashdata('errormsg','Your answer is Wrong');
            $this->load->view('Patient/checkPatient',$q_result);
            
        }

    }
    else
    {
        $this->load->view('Patient/checkPatient',$q_result);
    }
}

public function logSec()
{
    if(!empty($_POST))
    {
        $email = $this->security->xss_clean($this->input->post('email'));
            //$password = $this->input->post('password');
        $password = $this->security->xss_clean(md5($this->input->post('password')));
        $result = $this->LoginModel->loginSec($email,$password);
        if($result -> num_rows() > 0)
        {
            foreach ($result->result() as $row)
            {
                $this->session->userid = $row->secretary_id;
                $this->session->email =  $row->email;
                $this->session->fname =  $row->fname;
                $this->session->lname =  $row->lname;
                $this->session->status =  $row->status;
                $this->session->physician_id =  $row->physician_id;
                $this->session->image =  $row->image;
                $this->session->must_change_password = isset($row->must_change_password) ? (int) $row->must_change_password : 0;

                if ($this->session->status == "Inactive") {
                    $this->session->set_flashdata('errormsg1','This account is deactivated');
                    $this->load->view('Secretary/loginSec');
                }
                else{
                    $this->clearLoginFeedback();

                    $this->session->role = 'Secretary';
                    $fname = $this->session->fname;
                    $lname = $this->session->lname;
                    $user = 'Secretary';
                    $userid = $this->session->userid;
                    $date = date("Y-m-d h:i:sa");
                    $action = 'Logged in ' . $fname . " ".$lname;


                    $data1 = array(

                        'usertype' => $user,
                        'userid'   => $userid,
                        //'date'=> $date,
                        'action'   => $action

                    );
                    $this->Patient_Model->addLogs($data1);
                    redirect('profile-s');
                }
            }
        }
        else
        {
            $data['email'] = $email;
            $data['password'] = $password;
            $this->session->set_flashdata('errormsg','Email and Password is Wrong');
            $this->load->view('Secretary/loginSec',$data);
        }
    }
    else
    {
        $this->load->view('Secretary/loginSec');
    }
}

public function logDoctor()
{
    if(!empty($_POST))
    {
        $email = $this->security->xss_clean($this->input->post('email'));
        $password = $this->security->xss_clean(md5($this->input->post('password')));
            //$password = $this->input->post('password');
        $result = $this->LoginModel->loginDoctor($email,$password);
        if($result -> num_rows() > 0)
        {
            foreach ($result->result() as $row)
            {
                $this->session->userid = $row->physician_id;
                $this->session->email =  $row->email;
                $this->session->fname =  $row->fname;
                $this->session->lname =  $row->lname;
                $this->session->status =  $row->status;
                $this->session->image =  $row->image;
                $this->session->must_change_password = isset($row->must_change_password) ? (int) $row->must_change_password : 0;

                if ($this->session->status == "Inactive") {
                    $this->session->set_flashdata('errormsg1','This account is deactivated');
                    $this->load->view('Physician/loginPhysician');
                }
                else{
                    $this->clearLoginFeedback();

                    $this->session->role = 'Physician';
                    $fname = $this->session->fname;
                    $lname = $this->session->lname;
                    $user = 'Physician';
                    $userid = $this->session->userid;
                    $date = date("Y-m-d h:i:sa");
                    $action = 'Logged in ' . $fname . " ".$lname;


                    $data1 = array(

                        'usertype' => $user,
                        'userid'   => $userid,
                        //'date'=> $date,
                        'action'   => $action

                    );
                    $this->Patient_Model->addLogs($data1);
                    redirect('profile-phy');
                }
            }
        }
        else
        {
            $data['email'] = $email;
            $data['password'] = $password;
            $this->session->set_flashdata('errormsg','Email and Password is Wrong');
            $this->load->view('Physician/loginPhysician',$data);
            
        }

    }
    else
    {
        $this->load->view('Physician/loginPhysician');
    }
}



public function logoutPhysician()
{
    $fname = $this->session->fname;
    $lname = $this->session->lname;
    $user = 'Physician';
    $date = date("Y-m-d h:i:sa");
    $action = 'Logged out ' . $fname . " ".$lname;


    $data1 = array(

        'usertype' => $user,
        'userid'   => $this->session->userid,
                        //'date'=> $date,
        'action'   => $action

    );
    $this->Patient_Model->addLogs($data1);
    $this->session->set_flashdata('SUCCESSMSG','You have successfully logged out');
    $this->session->sess_destroy();
    redirect('login-phy');

}
public function logoutSec()
{
    $fname = $this->session->fname;
    $lname = $this->session->lname;
    $user = 'Secretary';
    $date = date("Y-m-d h:i:sa");
    $action = 'Logged out ' . $fname . " ".$lname;


    $data1 = array(

        'usertype' => $user,
        'userid'   => $this->session->userid,
                        //'date'=> $date,
        'action'   => $action

    );
    $this->Patient_Model->addLogs($data1);
    $this->session->set_flashdata('SUCCESSMSG','You have successfully logged out');
    $this->session->sess_destroy();
    redirect('login-s');

}
public function logoutAdmin()
{
    $fname = $this->session->fname;
    $lname = $this->session->lname;
    $user = 'Admin';
    $date = date("Y-m-d h:i:sa");
    $action = 'Logged out ' . $fname . " ".$lname;


    $data1 = array(

        'usertype' => $user,
        'userid'   => $this->session->userid,
                        //'date'=> $date,
        'action'   => $action

    );
    $this->Patient_Model->addLogs($data1);

    $this->session->set_flashdata('SUCCESSMSG','You have successfully logged out');
    $this->session->sess_destroy();
    redirect('login-a');


}
public function logoutPatient()
{
    $email = $this->session->email;
    $fname = $this->session->fname;
    $lname = $this->session->lname;
    $user = 'Patient';
    $date = date("Y-m-d h:i:sa");
    $action = 'Logged out ' . $fname . " ".$lname;


    $data1 = array(

        'usertype' => $user,
        'userid'   => $this->session->userid,
                        //'date'=> $date,
        'action'   => $action

    );
    $this->Patient_Model->addLogs($data1);

    $this->session->set_flashdata('SUCCESSMSG','You have successfully logged out');
    $this->session->unset_userdata('email');
    //$this->session->unset_userdata('password');
    
    $this->session->sess_destroy();
    redirect('login-p');
    
    

}
}
