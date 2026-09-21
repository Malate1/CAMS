<?php
class Physician extends CI_Controller {

	function __construct()
	{
		parent::__construct();            
		if($this->session->email == "" || $this->session->role !== 'Physician')
		{
			redirect('login-phy');
			return;
		}
		$this->load->model('Physician_Model','Physician_Model');
		$this->load->model('Patient_Model','Patient_Model');
		$this->load->model('Clinic_Model','Clinic_Model');
		$this->load->model('Secretary_Model','Secretary_Model');
		$this->load->model('Admin_Model','Admin_Model');
		$this->load->model('ClinicAssignment_Model','ClinicAssignment_Model');
		$this->load->model('Dashboard_Model','Dashboard_Model');
		$this->load->model('PasswordReset_Model','PasswordReset_Model');
		$this->ClinicAssignment_Model->ensureSchema();
		$this->PasswordReset_Model->ensureSchema();

		if ((int) $this->session->userdata('must_change_password') === 1) {
			$method = strtolower((string) $this->router->method);
			if (!in_array($method, array('index', 'change_pass'), true)) {
				redirect('profile-phy');
				return;
			}
		}
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

		$userid = $this->session->userdata['userid'];
		$data['records'] = $this->Physician_Model->view_info($userid);
		$data['count'] = $this->Physician_Model->Count();
		$data['countD'] = $this->Physician_Model->CountDone();
		$data['countC'] = $this->Physician_Model->CountCancel();
		$data['getSchedule'] = $this->Physician_Model->getSchedule();
		$data['dashboard'] = $this->Dashboard_Model->physicianDashboard($userid);
		$this->load->view('Physician/dashboard_physician', $data);

	}

	public function ViewLogs()
		{
			date_default_timezone_set('Asia/Manila');
			$this->load->view('Physician/Logs_view');
		}

	public function PatientRegister()
	{
		if(!empty($_POST))
		{

			$status = "Active";
			$email    = $this->input->post('email');
			$imgUrl = $this->uploadImage();
				/*$password = $this->input->post('password');
				$encrypt_password = password_hash($password,PASSWORD_DEFAULT);*/
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
				$result = $this->Patient_Model->CheckExists($email);
				if($result -> num_rows() > 0)
				{
					if ($this->ajaxTransactionResponse(false, 'This email account is already taken.')) return;
					$this->session->set_flashdata('errormsg1','This email account is already taken');
					redirect('profile-phy');
				}else{

					$fname = $this->session->fname;
				    $lname = $this->session->lname;
				    $user = 'Physician';
				    $date = date("Y-m-d h:i:sa");
				    $action = 'Patient added by ' . $fname . " ".$lname;
				       

				    $data1 = array(

				    	'usertype' => $user, 
				    	'userid'   => $this->session->userid,
				    	//'date'=> $date,
				    	'action'   => $action
				     
				    );
				    $this->Patient_Model->addLogs($data1);

					$this->Patient_Model->insertP($data);
					if ($this->ajaxTransactionResponse(true, 'Patient registered successfully.', base_url('profile-phy'))) return;
					$this->session->set_flashdata('SUCCESSMSG', "Patient Register Successfully!! ");
					redirect('profile-phy');
				}
			}
			else
			{

				$data['getPatient'] = $this->Patient_Model->getPatient();
				$this->load->view('dashboard_physician');
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




		public function ViewClinic()
		{
			$this->load->view('Physician/Clinic_view');
		}


		public function ClinicUpdateView() {
			$this->load->helper('form');
			$key = (int) $this->uri->segment('2');
			$physicianId = (int) $this->session->userdata['userid'];
			$assignment = $this->db->from('physician_clinic')->where('id', $key)->where('physician_id', $physicianId)->limit(1)->get()->row();
			if (!$assignment) {
				$assignment = $this->db->from('physician_clinic')->where('clinic_id', $key)->where('physician_id', $physicianId)->limit(1)->get()->row();
			}
			if (!$assignment) {
				redirect('view-clinic-p');
				return;
			}
			$clinic_id = (int) $assignment->clinic_id;
			$query = $this->db->get_where("clinic",array("clinic_id"=>$clinic_id));
			$data['getClinic'] = $query->result();
			$data['old_clinic_id'] = $clinic_id;
			$data['assignment'] = $assignment;
			$data['availableSpecializations'] = $this->db
				->select('specialization.special_id, specialization.special_name')
				->from('physician_special')
				->join('specialization', 'specialization.special_id = physician_special.special_id')
				->where('physician_special.physician_id', $physicianId)
				->order_by('specialization.special_name', 'ASC')->get()->result();
			$data['selectedSpecialIds'] = array();
			$selected = $this->db->select('special_id')->from('physician_clinic_specialization')->where('physician_clinic_id', (int) $assignment->id)->get()->result();
			foreach ($selected as $item) $data['selectedSpecialIds'][] = (int) $item->special_id;
			$this->load->view('Physician/Clinic_edit',$data);
		}


		public function ClinicUpdate()
		{
			if(!empty($_POST))
			{
				$data = array(

					'name'            => $this->security->xss_clean($this->input->post('name')),

					'contact'         => $this->security->xss_clean($this->input->post('contact')),
					'location'        => $this->security->xss_clean($this->input->post('location'))
				);

				$fname = $this->session->fname;
				$lname = $this->session->lname;
				$user = 'Physician';
				$date = date("Y-m-d h:i:s A");
				$action = 'Clinic updated by ' . $fname . " ".$lname;
				    $data1 = array(

				    	'usertype' => $user, 
				    	'userid'   => $this->session->userid,
				    	//'date'=> $date,
				    	'action'   => $action    
				    );
				$this->Patient_Model->addLogs($data1);

				$clinic_id = (int) $this->security->xss_clean($this->input->post('old_clinic_id'));
				$assignmentId = (int) $this->input->post('old_assignment_id');
				$assignment = $this->ClinicAssignment_Model->getAssignmentById($assignmentId);
				if (!$assignment || (int) $assignment->physician_id !== (int) $this->session->userdata['userid'] || (int) $assignment->clinic_id !== $clinic_id) {
					if ($this->ajaxTransactionResponse(false, 'The clinic assignment could not be found.')) return;
					redirect('view-clinic-p');
					return;
				}
				$postedSpecialIds = $this->input->post('special_ids');
				if (!is_array($postedSpecialIds)) $postedSpecialIds = array();
				$specialIds = array_values(array_unique(array_filter(array_map('intval', $postedSpecialIds))));
				if (!$specialIds) {
					if ($this->ajaxTransactionResponse(false, 'Please select at least one specialization for this clinic.')) return;
					redirect('view-clinic-p');
					return;
				}
				foreach ($specialIds as $specialId) {
					$valid = $this->db->where('physician_id', (int) $assignment->physician_id)->where('special_id', $specialId)->count_all_results('physician_special') > 0;
					if (!$valid) {
						if ($this->ajaxTransactionResponse(false, 'One or more selected specializations are not configured on your physician profile.')) return;
						redirect('view-clinic-p');
						return;
					}
				}
				$this->Clinic_Model->update($data,$clinic_id);
				$this->ClinicAssignment_Model->replaceSpecializations($assignmentId, $specialIds);
				if ($this->ajaxTransactionResponse(true, 'Clinic record and services updated successfully.', base_url('view-clinic-p'))) return;
				$this->session->set_flashdata('SUCCESSMSG', "Clinic Record Updated Successfully!!");
				redirect('view-clinic-p');
			}
			else
			{

				$clinic_id = $this->security->xss_clean($this->input->post('old_clinic_id'));
				$this->Clinic_Model->update($data,$clinic_id);
				$data['getClinic'] = $this->Clinic_Model->getClinic();
				$this->load->view('Physician/Clinic_edit',$data);
			}
		}



		public function ClinicDelete() {

			$Clinic_id = $this->uri->segment('3');
			$this->Clinic_Model->delete($Clinic_id);
			$this->session->set_flashdata('SUCCESSMSG', "Clinic Deleted Successfully!!");
			$data['getClinic'] = $this->Clinic_Model->getClinic();
			$this->load->view('Physician/Clinic_view',$data);
		}

		public function ViewSchedule()
		{
			$data['clinicAssignments'] = $this->ClinicAssignment_Model->getClinicAssignmentsForPhysician((int) $this->session->userdata['userid']);
			$this->load->view('Physician/Schedule_view', $data);
		}
		public function ViewSpecialization()
		{
			$data['getSpecialization'] = $this->Physician_Model->getSpecialization();   

			$this->load->view('Physician/Specialization_view',$data);
		}



		public function ScheduleRegister(){

			if(!empty($_POST))
			{  
				$userid = $this->session->userdata['userid'];
				$clinicId = (int) $this->input->post('clinic_id');
				$assignment = $this->ClinicAssignment_Model->getAssignment($userid, $clinicId);
				if (!$assignment) {
					if ($this->ajaxTransactionResponse(false, 'Please select a clinic assigned to your account.')) return;
					$this->session->set_flashdata('error', 'Please select a clinic assigned to your account.');
					redirect('view-schedule-p');
					return;
				}

				$data3 = array(
					'day' => $this->security->xss_clean($this->input->post('day')) ,
					'time_in' => $this->security->xss_clean($this->input->post('time_in')),
					'time_out' => $this->security->xss_clean($this->input->post('time_out'))    
				);
				$this->Physician_Model->insert_schedule($data3);

				$sched = (int) $this->db->insert_id();
				$data4 = array(
					'schedule_id' => $sched,
					'physician_id' =>$userid
				);
					$fname = $this->session->fname;
				    $lname = $this->session->lname;
				    $user = 'Physician';
				    $date = date("Y-m-d h:i:sa");
				    $action = 'Schedule added by ' . $fname . " ".$lname;
				       

				    $data1 = array(

				    	'usertype' => $user, 'userid'   => $this->session->userid,
				    	//'date'=> $date,
				    	'action'   => $action
				     
				    );
				    $this->Patient_Model->addLogs($data1);

				$this->Physician_Model->insert_pschedule($data4);
				$this->ClinicAssignment_Model->assignSchedule($assignment->id, $sched);
				if ($this->ajaxTransactionResponse(true, 'Clinic schedule registered successfully.', base_url('view-schedule-p'))) return;
				$this->session->set_flashdata('SUCCESSMSG', "Physician Schedule Registered Successfully!!");
				redirect('view-schedule-p');

			}else{
				$data['getSchedule'] = $this->Physician_Model->getSchedule();
				$this->load->view('Physician/Schedule_view');
			}

		}


		public function SpecializationRegister(){

			if(!empty($_POST))
			{  
				$userid = $this->session->userdata['userid'];


				$data1 = array(

					'special_name' => $this->input->post('special_name')    
				);
				$this->Physician_Model->insert_special($data1);

				$special = $this->db->query('SELECT MAX(special_id) AS `maxid` FROM `specialization`')->row()->maxid;
				$data = array(
					'special_id' => $special,
					'physician_id' =>$userid
				);

				$this->Physician_Model->insert_pspecial($data);
				$this->session->set_flashdata('SUCCESSMSG', "Physician Specialization Registered Successfully!!");
				redirect('view-specialization-p');

			}else{
				$data['getSchedule'] = $this->Physician_Model->getSchedule();
				$this->load->view('Physician/Schedule_view');
			}

		}

		public function ScheduleUpdateView() {
			$this->load->helper('form');
			$linkId = (int) $this->uri->segment('2');
			$row = $this->db
				->select('physician_clinic_schedule.id AS link_id, schedule.schedule_id, schedule.day, schedule.time_in, schedule.time_out, clinic.name AS clinic_name, clinic.location AS clinic_location')
				->from('physician_clinic_schedule')
				->join('physician_clinic', 'physician_clinic.id = physician_clinic_schedule.physician_clinic_id')
				->join('clinic', 'clinic.clinic_id = physician_clinic.clinic_id')
				->join('schedule', 'schedule.schedule_id = physician_clinic_schedule.schedule_id')
				->where('physician_clinic_schedule.id', $linkId)
				->where('physician_clinic.physician_id', (int) $this->session->userdata['userid'])
				->limit(1)->get()->row();
			if (!$row) {
				redirect('view-schedule-p');
				return;
			}
			$data['getSchedule'] = array($row);
			$data['old_schedule_id'] = (int) $row->schedule_id;
			$data['old_schedule_link_id'] = (int) $row->link_id;
			$this->load->view('Physician/Schedule_edit',$data);
		}




		public function ScheduleUpdate()
		{
			if(!empty($_POST))
			{
				$data = array(

					'day'     => $this->security->xss_clean($this->input->post('day')),
					'time_in'     => $this->security->xss_clean($this->input->post('time_in')),
					'time_out'   => $this->security->xss_clean($this->input->post('time_out'))
				);
				$linkId = (int) $this->input->post('old_schedule_link_id');
				$link = $this->db
					->select('physician_clinic_schedule.id, physician_clinic_schedule.schedule_id, physician_clinic.physician_id')
					->from('physician_clinic_schedule')
					->join('physician_clinic', 'physician_clinic.id = physician_clinic_schedule.physician_clinic_id')
					->where('physician_clinic_schedule.id', $linkId)
					->where('physician_clinic.physician_id', (int) $this->session->userdata['userid'])
					->limit(1)->get()->row();
				if (!$link) {
					if ($this->ajaxTransactionResponse(false, 'The clinic schedule could not be found.')) return;
					redirect('view-schedule-p');
					return;
				}

				$schedule_id = (int) $link->schedule_id;
				$linkCount = (int) $this->db->where('schedule_id', $schedule_id)->count_all_results('physician_clinic_schedule');
				if ($linkCount > 1) {
					$this->db->insert('schedule', $data);
					$newScheduleId = (int) $this->db->insert_id();
					$this->db->where('id', $linkId)->update('physician_clinic_schedule', array('schedule_id' => $newScheduleId));
					$legacyExists = $this->db->where('physician_id', (int) $link->physician_id)->where('schedule_id', $newScheduleId)->count_all_results('physician_sched') > 0;
					if (!$legacyExists) $this->Physician_Model->insert_pschedule(array('physician_id' => (int) $link->physician_id, 'schedule_id' => $newScheduleId));
				} else {
					$this->Physician_Model->updateSched($data,$schedule_id);
				}


				$fname = $this->session->fname;
				    $lname = $this->session->lname;
				    $user = 'Physician';
				    $date = date("Y-m-d h:i:sa");
				    $action = 'Schedule updated by ' . $fname . " ".$lname;
				       

				    $data1 = array(

				    	'usertype' => $user, 'userid'   => $this->session->userid,
				    	//'date'=> $date,
				    	'action'   => $action
				     
				    );
				    $this->Patient_Model->addLogs($data1);
				if ($this->ajaxTransactionResponse(true, 'Schedule updated successfully.', base_url('view-schedule-p'))) return;
				$this->session->set_flashdata('SUCCESSMSG', "Schedule Updated Successfully!!");
				redirect('view-schedule-p');
			}
			else
			{

				$schedule_id = $this->security->xss_clean($this->input->post('old_schedule_id'));
				$this->Physician_Model->updateSched($data,$schedule_id);
				$data['getSchedule'] = $this->Physician_Model->getSchedule();
				$this->load->view('Physician/Schedule_edit',$data);
			}
		}


		public function SpecializationUpdateView() {
			$this->load->helper('form');
			$special_id = $this->uri->segment('2');
			$query = $this->db->get_where("specialization",array("special_id"=>$special_id));
			$data['getSpecialization'] = $query->result();
			$data['old_special_id'] = $special_id;
			$this->load->view('Physician/Specialization_edit',$data);
		}




		public function SpecializationUpdate()
		{
			if(!empty($_POST))
			{
				$data = array(

					'special_name'   => $this->input->post('special_name')
				);
				$special_id = $this->input->post('old_special_id');
				$this->Physician_Model->updateSpecial($data,$special_id);
				$this->session->set_flashdata('SUCCESSMSG', "Specialization Updated Successfully!!");
				redirect('view-specialization-p');
			}
			else
			{

				$special_id = $this->input->post('old_special_id');
				$this->Physician_Model->updateSpecial($data,$special_id);
				$data['getSpecialization'] = $this->Physician_Model->getSpecialization();
				$this->load->view('Physician/Specialization_edit',$data);
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
				$this->load->view('Physician/Change_pass');
			}
			else
			{
				
				$physician_id = $this->session->userid;
				//$old = $this->input->post('oldPassword');
				$old = md5($this->input->post('oldPassword'));
				$info = array(
					'password' => $this->security->xss_clean(md5($this->input->post('newPassword'))),
				);
				$query = $this->Physician_Model->CheckOld($physician_id,$old);
				if($query -> num_rows() > 0)
				{
					$result = $this->Physician_Model->changePassword($physician_id, $info);
					$this->PasswordReset_Model->setRequired('physician', $physician_id, false);
					$this->session->must_change_password = 0;

					$fname = $this->session->fname;
				    $lname = $this->session->lname;
				    $user = 'Physician';
				    $date = date("Y-m-d h:i:sa");
				    $action = 'Password updated by ' . $fname . " ".$lname;
				       

				    $data1 = array(

				    	'usertype' => $user, 'userid'   => $this->session->userid,
				    	//'date'=> $date,
				    	'action'   => $action
				     
				    );
				    $this->Patient_Model->addLogs($data1);

					$successMessage = 'Password updated successfully.';
					if ($this->ajaxTransactionResponse(true, $successMessage, '', array(
						'password_change_completed' => true,
					))) return;
					$this->session->set_flashdata('success', $successMessage);
					redirect('profile-phy');
				}else{


					if ($this->ajaxTransactionResponse(false, 'Old password is incorrect.')) return;
					$this->session->set_flashdata('errormsg','Old password is incorrect');
					$this->load->view('Physician/Change_pass');

				}
			}
		}
		public function ProfileUpdateView() {

			$this->load->helper('form');
			$physician_id = $this->session->userid;
			$query = $this->db->get_where("physician",array("physician"=>$physician_id));
			$data['records'] = $query->result();

			$this->load->view('Physician/Update_profile',$data);
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
					'birthplace' => $this->security->xss_clean($this->input->post('birthplace')),
					'birthdate' => $this->security->xss_clean($this->input->post('birthdate')),
					'gender'    => $this->security->xss_clean($this->input->post('gender')),
					'contact'   => $this->security->xss_clean($this->input->post('contact')),
					'address'   => $this->security->xss_clean($this->input->post('address')),
					//'image'   => $imgUrl
				);
				$physician_id = $this->session->userid;

				$fname = $this->session->fname;
				$lname = $this->session->lname;
				$user = 'Physician';
				$date = date("Y-m-d h:i:sa");
				$action = 'Profile updated by ' . $fname . " ".$lname;
				       
				$data1 = array(

				    'usertype' => $user, 'userid'   => $this->session->userid,
				    //'date'=> $date,
				    'action'   => $action
				     
				    );
				    $this->Patient_Model->addLogs($data1);
				$this->Physician_Model->updateProfile($data,$physician_id);
				$this->session->set_userdata($data);

				$successMessage = 'Profile updated successfully.';
				if ($this->ajaxTransactionResponse(true, $successMessage, '', array(
					'profile' => array(
						'display_name' => trim($data['fname'] . ' ' . $data['lname']),
					),
				))) return;

				$this->session->set_flashdata('success', $successMessage);
				redirect('update-profile-phy');
			}
			else
			{
				$physician_id = $this->session->userid;
				//$this->Admin_Model->updateProfile($data,$admin_id);
				$data['records'] = $this->Physician_Model->view_info($physician_id);
				$this->load->view('Physician/Update_profile',$data);
			}
		}
		public function ProfilePicUpdateView() {

			$this->load->helper('form');
			$physician_id = $this->session->userid;
			$query = $this->db->get_where("physician",array("physician"=>$physician_id));
			$data['records'] = $query->result();

			$this->load->view('Physician/Update_profile',$data);
		}

		public function ProfilePicUpdate()
		{
			if(!empty($_POST))
			{
				$imgUrl = $this->EdituploadImage();
				if (!$imgUrl) {
					if ($this->ajaxTransactionResponse(false, 'Please select a valid image file.')) return;
					$this->session->set_flashdata('error', 'Please select a valid image file.');
					redirect('update-profile-phy');
					return;
				}
				$data = array(

					'image'   => $imgUrl
				);
				$physician_id = $this->session->userid;

				$fname = $this->session->fname;
				$lname = $this->session->lname;
				$user = 'Physician';
				$date = date("Y-m-d h:i:sa");
				$action = 'Profile picture updated by ' . $fname . " ".$lname;
				       
				$data1 = array(

				    'usertype' => $user, 'userid'   => $this->session->userid,
				    //'date'=> $date,
				    'action'   => $action
				     
				    );
				    $this->Patient_Model->addLogs($data1);
				$this->Physician_Model->updateProfilePic($data,$physician_id);
				$this->session->set_userdata($data);

				$successMessage = 'Profile photo updated successfully.';
				if ($this->ajaxTransactionResponse(true, $successMessage, '', array(
					'profile' => array(
						'image_url' => base_url('uploads/profile-pic/' . rawurlencode($imgUrl)),
					),
				))) return;

				$this->session->set_flashdata('success', $successMessage);
				redirect('update-profile-phy');
			}
			else
			{
				$physician_id = $this->session->userid;
				//$this->Admin_Model->updateProfile($data,$admin_id);
				$data['records'] = $this->Physician_Model->view_info($physician_id);
				$this->load->view('Physician/Update_profile',$data);
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

		public function AppointmentRegister(){
			$this->load->model('Appointment_Model', 'Appointment_Model');
			$physicianId = (int) $this->session->userdata['userid'];

			if (empty($_POST)) {
				$data['getPatient'] = $this->Patient_Model->getPatient();
				$data['complaints'] = $this->Appointment_Model->getComplaintCatalog();
				$data['bookingProviders'] = array_values(array_filter($this->Appointment_Model->getBookingProviders(), function ($provider) use ($physicianId) {
					return (int) $provider->physician_id === $physicianId;
				}));
				$data['bookingRole'] = 'Physician';
				$data['availabilityUrl'] = base_url('appointment-availability-p');
				$data['formAction'] = base_url('Physician/AppointmentRegister');
				$this->load->view('Physician/Appointment_add', $data);
				return;
			}

			$clinicId = (int) $this->input->post('clinic_id');
			if ($clinicId <= 0) {
				$clinicId = $this->Appointment_Model->getPrimaryClinicId($physicianId);
			}

			$result = $this->Appointment_Model->book(array(
				'patient_id' => (int) $this->input->post('patient_id'),
				'physician_id' => $physicianId,
				'clinic_id' => $clinicId,
				'app_date' => trim((string) $this->input->post('app_date')),
				'purpose' => $this->security->xss_clean($this->input->post('purpose')),
			));

			if (!$result['success']) {
				if ($this->ajaxTransactionResponse(false, $result['message'])) return;
				$this->session->set_flashdata('error1', $result['message']);
				redirect('app-register-p');
				return;
			}

			$fname = $this->session->fname;
			$lname = $this->session->lname;
			$this->Patient_Model->addLogs(array(
				'usertype' => 'Physician',
				'userid' => $this->session->userid,
				'action' => 'Appointment added by ' . $fname . ' ' . $lname,
			));

			$successMessage = 'Appointment successfully added. Queue number: ' . $result['queue_number'] . '.';
			if ($this->ajaxTransactionResponse(true, $successMessage, base_url('view-app-p'), array('queue_number' => $result['queue_number']))) return;
			$this->session->set_flashdata('SUCCESSMSG', $successMessage);
			redirect('view-app-p');
		}

		public function AppointmentAvailability()
		{
			$this->load->model('Appointment_Model', 'Appointment_Model');
			$physicianId = (int) $this->session->userdata['userid'];
			$clinicId = (int) $this->input->get('clinic_id');
			$dates = $this->Appointment_Model->getAvailableDates($physicianId, $clinicId);
			$this->output->set_content_type('application/json')->set_output(json_encode(array('success' => true, 'dates' => $dates)));
		}

		private function AppointmentRegisterLegacy(){



			if(!empty($_POST))
			{

				$app_date = $this->security->xss_clean($this->input->post('app_date'));
				$app_date = str_replace('/', '-', $app_date);
				// $app_date = date('Y-m-d', strtotime($app_date));

				// echo $app_date; die();

				$status = "Pending";
				
				$c_date = date("Y-m-d H:i:s");
				$physician_id = $this->session->userdata['userid'];
				$patient_id = $this->security->xss_clean($this->input->post('patient_id'));
				$clinic = "SELECT clinic.clinic_id as clinic
				FROM `physician` 
				INNER JOIN physician_clinic on physician_clinic.physician_id = physician.physician_id
				INNER JOIN clinic on clinic.clinic_id = physician_clinic.clinic_id
				WHERE physician_clinic.physician_id = $physician_id";
				$clinic_id = $this->db->query($clinic)->row()->clinic;
				$sql = "SELECT MAX(queueNum) AS `queue` FROM `appointment` WHERE physician_id = $physician_id  AND app_date = '$app_date' AND clinic_id = '$clinic_id'";
				$queue =  $this->db->query($sql)->row()->queue;
				$query =  "SELECT Max(queueLimit) as `queueLimit` FROM `queuecount` WHERE physician_id = $physician_id AND dateLimit = '$app_date'";
				$limit =  $this->db->query($query)->row()->queueLimit;


				/*echo $limit; 
				echo "<br>";
				echo $queue;
				echo "<br>";
				echo $physician_id; 
				echo "<br>";
				echo $clinic_id;
				echo "<br>";
				echo $app_date; die(); */

////////////////////////COMPARING DAYS//////////////////////
				$physician_id = $this->session->userdata['userid'];
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

					
					if ($queue >= $limit) {

						$this->session->set_flashdata('error1', "Please try again for another date appointment is already full for this date: " .$this->input->post('app_date'));
						$data['getPatient'] = $this->Patient_Model->getPatient();
						$this->load->view('Physician/Appointment_add', $data);
				//redirect('app-register');
					} else {

						$data = array(
							'app_date' => $app_date,
							'purpose' => $this->security->xss_clean($this->input->post('purpose')),
							'date_created' => $c_date,
							'patient_id' => $this->security->xss_clean($this->input->post('patient_id')),
							'physician_id' => $physician_id,
							'queueNum' => $queue + 1,
							'clinic_id' => $clinic_id,
							'app_status' => $status
						);

				//print_r($data); die();
						$fname = $this->session->fname;
				    $lname = $this->session->lname;
				    $user = 'Physician';
				    $date = date("Y-m-d h:i:sa");
				    $action = 'Appointment added by ' . $fname . " ".$lname;
				       

				    $data1 = array(

				    	'usertype' => $user, 'userid'   => $this->session->userid,
				    	//'date'=> $date,
				    	'action'   => $action
				     
				    );
				    $this->Patient_Model->addLogs($data1);

						$this->Patient_Model->insert($data);

						$this->session->set_flashdata('SUCCESSMSG', "Appointment Successfully Added!! The queue number is:" .$queue);
				//sms
						redirect('view-app-p');
					}
				}else {
					$this->session->set_flashdata('error', "The doctor won't be here in this chosen day");
					$data['getPatient'] = $this->Patient_Model->getPatient();
					$this->load->view('Physician/Appointment_add', $data);
		//redirect('app-register');
					}//end of else
			}//end of IF POST
			else
			{

				$data['getPatient'] = $this->Patient_Model->getPatient();   
				$this->load->view('Physician/Appointment_add',$data);
			}
		}


		public function ViewAppointment()
		{
			$selectedDate = trim((string) $this->input->get('app_date', true));
			$this->load->view('Physician/Appointment_view', array(
				'appointmentScope' => $selectedDate !== '' ? 'date' : 'all',
				'appointmentDate' => $selectedDate,
			));
		}

		public function ViewAppointmentToday()
		{
			$this->load->view('Physician/Appointment_view', array('appointmentScope' => 'today'));
		}

		public function ViewAppointmentDone()
		{
			$this->load->view('Physician/Appointment_view', array('appointmentScope' => 'done'));
		}

		public function ViewAppointmentCancelled()
		{
			$this->load->view('Physician/Appointment_view', array('appointmentScope' => 'cancelled'));
		}

		public function AppointmentsData()
		{
			$this->load->model('Appointment_Model', 'Appointment_Model');
			$request = $this->input->get(null, true) ?: array();
			$result = $this->Appointment_Model->getAppointmentsData('physician', (int) $this->session->userdata['userid'], $request, isset($request['scope']) ? $request['scope'] : 'all');
			return $this->jsonResponse($result);
		}

		public function AppointmentEdit()
		{
			$this->load->model('Appointment_Model', 'Appointment_Model');
			$appointmentId = (int) ($this->input->method(true) === 'GET' ? $this->input->get('id', true) : $this->input->post('appointment_id', true));
			if ($this->input->method(true) === 'GET') {
				$appointment = $this->Appointment_Model->getEditableAppointment($appointmentId, 'physician', (int) $this->session->userdata['userid']);
				if (!$appointment) {
					return $this->jsonResponse(array('success' => false, 'message' => 'This appointment is no longer editable.'), 404);
				}
				return $this->jsonResponse(array('success' => true, 'appointment' => $appointment));
			}

			$result = $this->Appointment_Model->updateAppointmentAuthorized(
				$appointmentId,
				trim((string) $this->input->post('app_date', true)),
				$this->security->xss_clean($this->input->post('purpose')),
				'physician',
				(int) $this->session->userdata['userid']
			);
			if ($result['success']) {
				$this->Patient_Model->addLogs(array(
					'usertype' => 'Physician',
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
			$status = (string) $this->input->post('status', true);
			$updated = $this->Appointment_Model->updateStatusAuthorized($appointmentId, $status, 'physician', (int) $this->session->userdata['userid']);
			if ($updated) {
				$this->Patient_Model->addLogs(array(
					'usertype' => 'Physician',
					'userid' => $this->session->userid,
					'action' => 'Appointment #' . $appointmentId . ' marked ' . $status . ' by ' . $this->session->fname . ' ' . $this->session->lname,
				));
			}
			return $this->jsonResponse(array(
				'success' => $updated,
				'message' => $updated ? 'Appointment status updated successfully.' : 'The appointment status could not be updated.',
			), $updated ? 200 : 422);
		}

		private function jsonResponse(array $payload, $status = 200)
		{
			$payload['csrf'] = array(
				'name' => $this->security->get_csrf_token_name(),
				'hash' => $this->security->get_csrf_hash(),
			);
			return $this->output->set_status_header($status)->set_content_type('application/json', 'utf-8')->set_output(json_encode($payload));
		}

		public function Calendar()
		{
			$physicianId = (int) $this->session->userdata['userid'];
			$data = array(
				'calendarHeader' => 'header/headerPhysician',
				'calendarRole' => 'Physician',
				'calendarRoleKey' => 'physician',
				'calendarDataUrl' => base_url('calendar-events-p'),
				'calendarListUrl' => base_url('view-app-p'),
				'calendarBookUrl' => base_url('app-register-p'),
				'calendarClinics' => $this->ClinicAssignment_Model->getClinicAssignmentsForPhysician($physicianId),
			);

			$this->load->view('appointment/calendar', $data);
		}

		public function CalendarEvents()
		{
			$this->load->model('Calendar_Model', 'Calendar_Model');

			$start = (string) $this->input->get('start', true);
			$end = (string) $this->input->get('end', true);
			$clinicId = (int) $this->input->get('clinic_id', true);
			$status = (string) $this->input->get('status', true);

			if (!preg_match('/^\d{4}-\d{2}-\d{2}/', $start) || !preg_match('/^\d{4}-\d{2}-\d{2}/', $end)) {
				return $this->jsonResponse(array('success' => false, 'message' => 'Invalid calendar date range.'), 422);
			}

			$events = $this->Calendar_Model->events(
				(int) $this->session->userdata['userid'],
				substr($start, 0, 10),
				substr($end, 0, 10),
				null,
				$clinicId,
				$status
			);

			return $this->output
				->set_content_type('application/json', 'utf-8')
				->set_output(json_encode($events));
		}

		public function ViewAverage() {

			$first = $this->input->post('first');
		//$last = $this->input->post('last');
			$data['yearMonth'] = $first;
			$this->load->model('Secretary_Model');
			$data['getAverage'] = $this->Secretary_Model->getAverage($first);
			$this->load->view('Physician/Average_appoint',$data);
		}

		public function ViewDone() {

			$first = $this->input->post('first');
		//$last = $this->input->post('last');
			$this->load->model('Secretary_Model');
			$data['getDone'] = $this->Secretary_Model->getDone($first);
			$this->load->view('Physician/Done_appoint',$data);
		}
		public function ViewCancelled() {

			$first = $this->input->post('first');
		//$last = $this->input->post('last');
			$this->load->model('Secretary_Model');
			$data['getDone'] = $this->Secretary_Model->getCancelled($first);
			$this->load->view('Physician/Cancelled_appoint',$data);
		}

		public function ViewLimit()
		{
			$this->load->view('Physician/Limit_view');
		}

		
		public function LimitRegister()
		{
			if (empty($_POST)) {
				$data['getPatient'] = $this->Patient_Model->getPatient();
				$this->load->view('Physician/Set_limit', $data);
				return;
			}

			$this->load->model('Appointment_Model', 'Appointment_Model');
			$result = $this->Appointment_Model->saveDailyLimit(
				(int) $this->session->userdata['userid'],
				(string) $this->input->post('date'),
				(int) $this->input->post('limit')
			);

			if (!$result['success']) {
				if ($this->ajaxTransactionResponse(false, $result['message'])) return;
				$this->session->set_flashdata('error', $result['message']);
				redirect('limit-register-p');
				return;
			}

			$this->Patient_Model->addLogs(array(
				'usertype' => 'Physician',
				'userid' => $this->session->userid,
				'action' => 'Queue limit added by ' . $this->session->fname . ' ' . $this->session->lname,
			));
			if ($this->ajaxTransactionResponse(true, 'Appointment limit registered successfully.', base_url('view-limit-p'))) return;
			$this->session->set_flashdata('SUCCESSMSG', 'Appointment limit registered successfully.');
			redirect('view-limit-p');
		}

		private function LimitRegisterLegacy()
		{


			if(!empty($_POST))
			{  
				$physician_id = $this->session->userdata['userid'];

				$date = $this->security->xss_clean($this->input->post('date'));

				$data = array(
					'queueLimit'      => $this->security->xss_clean($this->input->post('limit')),
					'physician_id'    => $physician_id,
					'dateLimit'       => $this->security->xss_clean($this->input->post('date'))    
				);

				$result = $this->Physician_Model->CheckExistsLimit($date, $physician_id);
			    if($result -> num_rows() > 0)
			     {
			      $this->session->set_flashdata('error','Queue limit already existed for this date');
			      redirect('limit-register-p');
			  }else{
				$newLimit = $this->security->xss_clean($this->input->post('limit'));
				$this->Secretary_Model->insert_limit($data,$physician_id,$newLimit);

				$fname = $this->session->fname;
				    $lname = $this->session->lname;
				    $user = 'Physician';
				    $date = date("Y-m-d h:i:sa");
				    $action = 'Queue limit added by ' . $fname . " ".$lname;
				       

				    $data1 = array(

				    	'usertype' => $user, 
				    	'userid'   => $this->session->userid,
				    	//'date'=> $date,
				    	'action'   => $action
				     
				    );
				    $this->Patient_Model->addLogs($data1);

				$this->session->set_flashdata('SUCCESSMSG', "Appointment Limit Registered Successfully!!");
				redirect('view-limit-p');
			}
			}else{
				$data['getPatient'] = $this->Patient_Model->getPatient();
				$this->load->view('Physician/Set_limit',$data);
			}

		}


		public function UpdateStatus()
		{
			if (empty($_POST)) {
				redirect('view-app-p');
				return;
			}

			$this->load->model('Appointment_Model', 'Appointment_Model');
			$updated = $this->Appointment_Model->updateStatusAuthorized(
				(int) $this->input->post('appointment_id'),
				(string) $this->input->post('status'),
				'physician',
				(int) $this->session->userdata['userid']
			);

			if ($updated) {
				$this->Patient_Model->addLogs(array(
					'usertype' => 'Physician',
					'userid' => $this->session->userid,
					'action' => 'Appointment #' . (int) $this->input->post('appointment_id') . ' marked ' . (string) $this->input->post('status') . ' by ' . $this->session->fname . ' ' . $this->session->lname,
				));
				$this->session->set_flashdata('SUCCESSMSG1', 'Appointment status updated successfully.');
			} else {
				$this->session->set_flashdata('error', 'The appointment could not be updated. It may already be completed/cancelled or it does not belong to you.');
			}

			redirect('view-app-p');
		}

		private function UpdateStatusLegacy()
		{
			if(!empty($_POST))
			{
				$data = array(

					'app_status'       => $this->security->xss_clean($this->input->post('status'))

				);
				$old_app_id = $this->security->xss_clean($this->input->post('appointment_id'));
				$this->Physician_Model->updateStatus($data,$old_app_id);
				$this->session->set_flashdata('SUCCESSMSG1', "Appointment Status Updated Successfully!!");
				redirect('view-app-p');
			}
			else
			{

				$old_app_id = $this->security->xss_clean($this->input->post('appointment_id'));
				$this->Physician_Model->updateStatus($data,$old_app_id);
				$data['getAppointment'] = $this->Physician_Model->getAppointment();
				$this->load->view('Physician/Appointment_view',$data);
			}
		}

		public function LimitUpdateView() {
			$this->load->helper('form');
			$id = $this->uri->segment('2');
			$query = $this->db->get_where('queuecount', array(
				'id' => (int) $id,
				'physician_id' => (int) $this->session->userdata['userid'],
			));
			$data['getLimit'] = $query->result();
			$data['old_limit_id'] = $id;
			$this->load->view('Physician/Limit_edit',$data);
		}  
		public function LimitUpdate()
		{
			if (empty($_POST)) {
				redirect('view-limit-p');
				return;
			}

			$this->load->model('Appointment_Model', 'Appointment_Model');
			$result = $this->Appointment_Model->saveDailyLimit(
				(int) $this->session->userdata['userid'],
				'',
				(int) $this->input->post('queueLimit'),
				(int) $this->input->post('old_limit_id')
			);

			if (!$result['success']) {
				if ($this->ajaxTransactionResponse(false, $result['message'])) return;
				$this->session->set_flashdata('error', $result['message']);
				redirect('view-limit-p');
				return;
			}

			$this->Patient_Model->addLogs(array(
				'usertype' => 'Physician',
				'userid' => $this->session->userid,
				'action' => 'Queue limit updated by ' . $this->session->fname . ' ' . $this->session->lname,
			));
			if ($this->ajaxTransactionResponse(true, 'Appointment limit updated successfully.', base_url('view-limit-p'))) return;
			$this->session->set_flashdata('SUCCESSMSG1', 'Appointment limit updated successfully.');
			redirect('view-limit-p');
		}

		private function LimitUpdateLegacy()
		{
			$physician_id = $this->session->userdata['userid'];
			if(!empty($_POST))
			{
				$data = array(


					'queueLimit' => $this->security->xss_clean($this->input->post('queueLimit')),

				);
				$newLimit = $this->input->post('queueLimit');
				$id = $this->security->xss_clean($this->input->post('old_limit_id'));
				$this->Admin_Model->updateLimit($data,$id, $physician_id, $newLimit);

				$fname = $this->session->fname;
				    $lname = $this->session->lname;
				    $user = 'Physician';
				    $date = date("Y-m-d h:i:sa");
				    $action = 'Queue limit updated by ' . $fname . " ".$lname;
				       

				    $data1 = array(

				    	'usertype' => $user, 'userid'   => $this->session->userid,
				    	//'date'=> $date,
				    	'action'   => $action
				     
				    );
				    $this->Patient_Model->addLogs($data1);
				$this->session->set_flashdata('SUCCESSMSG1', "Appointment Limit Updated Successfully!!");
				redirect('view-limit-p');


			}
			else
			{
				$id = $this->security->xss_clean($this->input->post('old_limit_id'));
				$this->Admin_Model->updateLimit($data,$id);
				$data['getLimit'] = $this->Physician_Model->getLimit();   
				$this->load->view('Physician/Limit_view',$data);
			}
		}


	}
	?>
