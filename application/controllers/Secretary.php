<?php
class Secretary extends CI_Controller {

	function __construct()
	{
		parent::__construct();            
		if($this->session->email == "" || $this->session->role !== 'Secretary')
		{
			redirect('login-s');
			return;
		}
		$this->load->model('Secretary_Model','Secretary_Model');
		$this->load->model('Physician_Model','Physician_Model');
		$this->load->model('Patient_Model','Patient_Model');
		$this->load->model('Clinic_Model','Clinic_Model');
		$this->load->model('Admin_Model','Admin_Model');
		$this->load->model('ClinicAssignment_Model','ClinicAssignment_Model');
		$this->load->model('Dashboard_Model','Dashboard_Model');
		$this->ClinicAssignment_Model->ensureSchema();
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
		$data['count'] = $this->Secretary_Model->Count();
		$data['countD'] = $this->Secretary_Model->CountDone();
		$data['countC'] = $this->Secretary_Model->CountCancel();
		$data['records'] = $this->Secretary_Model->view_info($userid);
		$data['getSchedule'] = $this->Secretary_Model->getSchedule();
		$data['dashboard'] = $this->Dashboard_Model->secretaryDashboard($userid, (int) $this->session->userdata['physician_id']);
		$this->load->view('Secretary/dashboard_secretary', $data);

	}

	public function ViewLogs()
		{
			$this->load->view('Secretary/Logs_view');
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
					redirect('profile-s');
				}else{

					$fname = $this->session->fname;
				    $lname = $this->session->lname;
				    $user = 'Secretary';
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
					if ($this->ajaxTransactionResponse(true, 'Patient registered successfully.', base_url('profile-s'))) return;
					$this->session->set_flashdata('SUCCESSMSG', "Patient Register Successfully!! ");
					redirect('profile-s');
				}
			}
			else
			{

				$data['getPatient'] = $this->Patient_Model->getPatient();
				$this->load->view('dashboard_secretary');
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
			$this->load->view('Secretary/Clinic_view');
		}

		public function ClinicRegister()
		{


			if(!empty($_POST))
			{  
				$physician_id = $this->session->userdata['physician_id'];


				$data1 = array(
					'name'            => $this->input->post('name'),
					'dti'             => $this->input->post('dti'),
					'bir'             => $this->input->post('bir'),
					'businessPermit'  => $this->input->post('businessPermit'),
					'contact'         => $this->input->post('contact'),
					'location'        => $this->input->post('location')    
				);
				$this->Clinic_Model->insert_clinic($data1);

				$clinic = (int) $this->db->insert_id();
				$data = array(
					'clinic_id' => $clinic,
					'physician_id' => $physician_id,
					'secretary_id' => (int) $this->session->userdata['userid']
				);

				$this->Clinic_Model->insert_pclinic($data);
				$assignment = $this->ClinicAssignment_Model->getAssignment($physician_id, $clinic);
				if ($assignment) {
					$specialRows = $this->db->select('special_id')->from('physician_special')->where('physician_id', $physician_id)->get()->result();
					$specialIds = array();
					foreach ($specialRows as $specialRow) $specialIds[] = (int) $specialRow->special_id;
					$this->ClinicAssignment_Model->replaceSpecializations($assignment->id, $specialIds);
				}
				if ($this->ajaxTransactionResponse(true, 'Clinic registered successfully.', base_url('view-clinic-s'))) return;
				$this->session->set_flashdata('SUCCESSMSG', "Clinic Register Successfully!!");
				redirect('view-clinic-s');

			}else{
				$data['getClinic'] = $this->Physician_Model->getClinic();
				$this->load->view('Secretary/Clinic_view');
			}

		}

		public function ClinicUpdateView() {
			$this->load->helper('form');
			$key = (int) $this->uri->segment('2');
			$secretaryId = (int) $this->session->userdata['userid'];
			$physicianId = (int) $this->session->userdata['physician_id'];
			$assignment = $this->db->from('physician_clinic')->where('id', $key)->where('physician_id', $physicianId)->where('secretary_id', $secretaryId)->limit(1)->get()->row();
			if (!$assignment) {
				$assignment = $this->db->from('physician_clinic')->where('clinic_id', $key)->where('physician_id', $physicianId)->where('secretary_id', $secretaryId)->limit(1)->get()->row();
			}
			if (!$assignment) {
				redirect('view-clinic-s');
				return;
			}
			$clinic_id = (int) $assignment->clinic_id;
			$query = $this->db->get_where("clinic",array("clinic_id"=>$clinic_id));
			$data['getClinic'] = $query->result();
			$data['old_clinic_id'] = $clinic_id;
			$data['assignment'] = $assignment;
			$data['clinicSpecializations'] = $this->ClinicAssignment_Model->getClinicSpecializations($physicianId, $clinic_id);
			$this->load->view('Secretary/Clinic_edit',$data);
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
				$clinic_id = (int) $this->security->xss_clean($this->input->post('old_clinic_id'));
				$assignmentId = (int) $this->input->post('old_assignment_id');
				$assignment = $this->ClinicAssignment_Model->getAssignmentById($assignmentId);
				if (!$assignment || (int) $assignment->clinic_id !== $clinic_id || (int) $assignment->physician_id !== (int) $this->session->userdata['physician_id'] || (int) $assignment->secretary_id !== (int) $this->session->userdata['userid']) {
					if ($this->ajaxTransactionResponse(false, 'The clinic assignment could not be found.')) return;
					redirect('view-clinic-s');
					return;
				}
				$this->Clinic_Model->update($data,$clinic_id);

				$fname = $this->session->fname;
				    $lname = $this->session->lname;
				    $user = 'Secretary';
				    $date = date("Y-m-d h:i:sa");
				    $action = 'Clinic updated by ' . $fname . " ".$lname;
				       

				    $data1 = array(

				    	'usertype' => $user,
						'userid'   => $this->session->userid,
				    	//'date'=> $date,
				    	'action'   => $action
				     
				    );
				    $this->Patient_Model->addLogs($data1);
				if ($this->ajaxTransactionResponse(true, 'Clinic record updated successfully.', base_url('view-clinic-s'))) return;
				$this->session->set_flashdata('SUCCESSMSG', "Clinic Record Updated Successfully!!");
				redirect('view-clinic-s');
			}
			else
			{

				$clinic_id = $this->security->xss_clean($this->input->post('old_clinic_id'));
				$this->Clinic_Model->update($data,$clinic_id);
				$data['getClinic'] = $this->Clinic_Model->getClinic();
				$this->load->view('Secretary/Clinic_edit',$data);
			}
		}




		public function ClinicDelete() {

			$Clinic_id = $this->uri->segment('3');
			$this->Clinic_Model->delete($Clinic_id);
			$this->session->set_flashdata('SUCCESSMSG', "Clinic Deleted Successfully!!");
			$data['getClinic'] = $this->Clinic_Model->getClinic();
			$this->load->view('Secretary/Clinic_view',$data);
		}



		public function ViewSchedule()
		{
			$physicianId = (int) $this->session->userdata['physician_id'];
			$data['clinicAssignments'] = $this->ClinicAssignment_Model->getClinicAssignmentsForPhysician($physicianId, (int) $this->session->userdata['userid']);
			$this->load->view('Secretary/Schedule_view', $data);
		}


		public function ViewSpecialization()
		{

			$data['getSpecialization'] = $this->Secretary_Model->getSpecialization();   

			$this->load->view('Secretary/Specialization_view',$data);
		}



		public function ScheduleRegister(){

			if(!empty($_POST))
			{  

				$physician_id = $this->session->userdata['physician_id'];
				$clinicId = (int) $this->input->post('clinic_id');
				$assignment = $this->ClinicAssignment_Model->getAssignment($physician_id, $clinicId);
				if (!$assignment || (int) $assignment->secretary_id !== (int) $this->session->userdata['userid']) {
					if ($this->ajaxTransactionResponse(false, 'Please select a clinic assigned to your secretary account.')) return;
					$this->session->set_flashdata('error', 'Please select a clinic assigned to your secretary account.');
					redirect('view-schedule-s');
					return;
				}

				$data1 = array(
					'day' => $this->security->xss_clean($this->input->post('day')) ,
					'time_in' => $this->security->xss_clean($this->input->post('time_in')),
					'time_out' => $this->security->xss_clean($this->input->post('time_out'))    
				);
				$this->Physician_Model->insert_schedule($data1);

				$sched = (int) $this->db->insert_id();
				$data = array(
					'schedule_id' => $sched,
					'physician_id' =>$physician_id
				);

				$this->Physician_Model->insert_pschedule($data);
				$this->ClinicAssignment_Model->assignSchedule($assignment->id, $sched);

				$fname = $this->session->fname;
				    $lname = $this->session->lname;
				    $user = 'Secretary';
				    $date = date("Y-m-d h:i:sa");
				    $action = 'Schedule added by ' . $fname . " ".$lname;
				       

				    $data2 = array(

				    	'usertype' => $user,
						'userid'   => $this->session->userid,
				    	//'date'=> $date,
				    	'action'   => $action
				     
				    );
				    $this->Patient_Model->addLogs($data2);
				if ($this->ajaxTransactionResponse(true, 'Clinic schedule registered successfully.', base_url('view-schedule-s'))) return;
				$this->session->set_flashdata('SUCCESSMSG', "Physician Schedule Registered Successfully!!");
				redirect('view-schedule-s');

			}else{
				$data['getSchedule'] = $this->Secretary_Model->getSchedule();
				$this->load->view('Secretary/Schedule_view');
			}

		}


		public function SpecializationRegister(){

			if(!empty($_POST))
			{  
				$physician_id = $this->session->userdata['physician_id'];


				$data1 = array(

					'special_name' => $this->input->post('special_name')    
				);
				$this->Physician_Model->insert_special($data1);

				$special = $this->db->query('SELECT MAX(special_id) AS `maxid` FROM `specialization`')->row()->maxid;
				$data = array(
					'special_id' => $special,
					'physician_id' =>$physician_id
				);

				$this->Physician_Model->insert_pspecial($data);
				$this->session->set_flashdata('SUCCESSMSG', "Physician Specialization Registered Successfully!!");
				redirect('view-specialization-s');

			}else{
				$data['getSpecialization'] = $this->Physician_Model->getSpecialization();
				$this->load->view('Secretary/Specialization_view');
			}

		}

		public function ScheduleUpdateView() {
			$this->load->helper('form');
			$linkId = (int) $this->uri->segment('2');
			$physicianId = (int) $this->session->userdata['physician_id'];
			$row = $this->db
				->select('physician_clinic_schedule.id AS link_id, schedule.schedule_id, schedule.day, schedule.time_in, schedule.time_out, clinic.name AS clinic_name, clinic.location AS clinic_location')
				->from('physician_clinic_schedule')
				->join('physician_clinic', 'physician_clinic.id = physician_clinic_schedule.physician_clinic_id')
				->join('clinic', 'clinic.clinic_id = physician_clinic.clinic_id')
				->join('schedule', 'schedule.schedule_id = physician_clinic_schedule.schedule_id')
				->where('physician_clinic_schedule.id', $linkId)
				->where('physician_clinic.physician_id', $physicianId)
				->where('physician_clinic.secretary_id', (int) $this->session->userdata['userid'])
				->limit(1)->get()->row();
			if (!$row) {
				redirect('view-schedule-s');
				return;
			}
			$data['getSchedule'] = array($row);
			$data['old_schedule_id'] = (int) $row->schedule_id;
			$data['old_schedule_link_id'] = (int) $row->link_id;
			$this->load->view('Secretary/Schedule_edit',$data);
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
				$physicianId = (int) $this->session->userdata['physician_id'];
				$link = $this->db
					->select('physician_clinic_schedule.id, physician_clinic_schedule.schedule_id, physician_clinic.physician_id')
					->from('physician_clinic_schedule')
					->join('physician_clinic', 'physician_clinic.id = physician_clinic_schedule.physician_clinic_id')
					->where('physician_clinic_schedule.id', $linkId)
					->where('physician_clinic.physician_id', $physicianId)
					->where('physician_clinic.secretary_id', (int) $this->session->userdata['userid'])
					->limit(1)->get()->row();
				if (!$link) {
					if ($this->ajaxTransactionResponse(false, 'The clinic schedule could not be found.')) return;
					redirect('view-schedule-s');
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
				    $user = 'Secretary';
				    $date = date("Y-m-d h:i:sa");
				    $action = 'Schedule updated by ' . $fname . " ".$lname;
				       

				    $data1 = array(

				    	'usertype' => $user,
						'userid'   => $this->session->userid,
				    	//'date'=> $date,
				    	'action'   => $action
				     
				    );
				    $this->Patient_Model->addLogs($data1);
				if ($this->ajaxTransactionResponse(true, 'Schedule updated successfully.', base_url('view-schedule-s'))) return;
				$this->session->set_flashdata('SUCCESSMSG', "Schedule Updated Successfully!!");
				redirect('view-schedule-s');
			}
			else
			{

				$schedule_id = $this->security->xss_clean($this->input->post('old_schedule_id'));
				$this->Physician_Model->updateSched($data,$schedule_id);
				$data['getSchedule'] = $this->Physician_Model->getSchedule();
				$this->load->view('Secretary/Schedule_edit',$data);
			}
		}


		public function SpecializationUpdateView() {
			$this->load->helper('form');
			$special_id = $this->uri->segment('2');
			$query = $this->db->get_where("specialization",array("special_id"=>$special_id));
			$data['getSpecialization'] = $query->result();
			$data['old_special_id'] = $special_id;
			$this->load->view('Secretary/Specialization_edit',$data);
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
				redirect('view-specialization-s');
			}
			else
			{

				$special_id = $this->input->post('old_special_id');
				$this->Physician_Model->updateSpecial($data,$special_id);
				$data['getSpecialization'] = $this->Physician_Model->getSpecialization();
				$this->load->view('Secretary/Specialization_edit',$data);
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
				$this->load->view('Secretary/Change_pass');
			}
			else
			{

				$secretary_id = $this->session->userid;
				$old = md5($this->input->post('oldPassword'));
				$info = array(
					'password' => $this->security->xss_clean(md5($this->input->post('newPassword'))),
				);
				$query = $this->Secretary_Model->CheckOld($secretary_id,$old);
				if($query -> num_rows() > 0)
				{     
					$result = $this->Secretary_Model->changePassword($secretary_id, $info);  

					$fname = $this->session->fname;
				    $lname = $this->session->lname;
				    $user = 'Secretary';
				    $date = date("Y-m-d h:i:sa");
				    $action = 'Password updated by ' . $fname . " ".$lname;
				       

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
					redirect('change-pass-s');

				}else{


					if ($this->ajaxTransactionResponse(false, 'Old password is incorrect.')) return;
					$this->session->set_flashdata('errormsg','Old password is incorrect');
					$this->load->view('Secretary/Change_pass');

				}

			}
		}


		public function ProfileUpdateView() {

			$this->load->helper('form');
			$physician_id = $this->session->userid;
			$query = $this->db->get_where("secretary",array("secretary_id"=>$secretary_id));
			$data['records'] = $query->result();

			$this->load->view('Secretary/Update_profile',$data);
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
				$secretary_id = $this->session->userdata['userid'];
				$this->Secretary_Model->updateProfile($data,$secretary_id);

				$fname = $this->session->fname;
				    $lname = $this->session->lname;
				    $user = 'Secretary';
				    $date = date("Y-m-d h:i:sa");
				    $action = 'Profile updated by ' . $fname . " ".$lname;
				       

				    $data1 = array(

				    	'usertype' => $user,
						'userid'   => $this->session->userid,
				    	//'date'=> $date,
				    	'action'   => $action
				     
				    );
				    $this->Patient_Model->addLogs($data1);

				$this->session->set_flashdata('success', "Secretary Record Updated Successfully!!");
				$this->session->set_userdata($data);
				redirect('update-profile-s');


			}
			else
			{
				$secretary_id = $this->session->userid;
				//$this->Secretary_Model->updateProfile($data,$Secretary_id);
				$data['records'] = $this->Secretary_Model->view_info($secretary_id);
				$this->load->view('Secretary/Update_profile',$data);
			}
		}

		public function ProfilePicUpdateView() {

			$this->load->helper('form');
			$physician_id = $this->session->userid;
			$query = $this->db->get_where("secretary",array("secretary_id"=>$secretary_id));
			$data['records'] = $query->result();

			$this->load->view('Secretary/Update_profile',$data);
		}

		public function ProfilePicUpdate()
		{
			if(!empty($_POST))
			{
				$imgUrl = $this->EdituploadImage();
				$data = array(

					'image'   => $imgUrl
				);
				$secretary_id = $this->session->userdata['userid'];
				$this->Secretary_Model->updateProfile($data,$secretary_id);

					$fname = $this->session->fname;
				    $lname = $this->session->lname;
				    $user = 'Secretary';
				    $date = date("Y-m-d h:i:sa");
				    $action = 'Profile picture updated by ' . $fname . " ".$lname;
				       

				    $data1 = array(

				    	'usertype' => $user,
						'userid'   => $this->session->userid,
				    	//'date'=> $date,
				    	'action'   => $action
				     
				    );
				    $this->Patient_Model->addLogs($data1);
				$this->session->set_flashdata('success', "Secretary Record Updated Successfully!!");
				$this->session->set_userdata($data);
				redirect('update-profile-s');


			}
			else
			{
				$secretary_id = $this->session->userid;
				//$this->Secretary_Model->updateProfile($data,$Secretary_id);
				$data['records'] = $this->Secretary_Model->view_info($secretary_id);
				$this->load->view('Secretary/Update_profile',$data);
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
			$physicianId = (int) $this->session->userdata['physician_id'];

			if (empty($_POST)) {
				$data['getPatient'] = $this->Patient_Model->getPatient();
				$data['complaints'] = $this->Appointment_Model->getComplaintCatalog();
				$secretaryId = (int) $this->session->userdata['userid'];
				$data['bookingProviders'] = array_values(array_filter($this->Appointment_Model->getBookingProviders(), function ($provider) use ($physicianId, $secretaryId) {
					return (int) $provider->physician_id === $physicianId && (int) $provider->secretary_id === $secretaryId;
				}));
				$data['bookingRole'] = 'Secretary';
				$data['availabilityUrl'] = base_url('appointment-availability-s');
				$data['formAction'] = base_url('Secretary/AppointmentRegister');
				$this->load->view('Secretary/Appointment_add', $data);
				return;
			}

			$clinicId = (int) $this->input->post('clinic_id');
			$assignment = $this->ClinicAssignment_Model->getAssignment($physicianId, $clinicId);
			if (!$assignment || (int) $assignment->secretary_id !== (int) $this->session->userdata['userid']) {
				if ($this->ajaxTransactionResponse(false, 'You are not assigned to the selected clinic.')) return;
				$this->session->set_flashdata('error1', 'You are not assigned to the selected clinic.');
				redirect('app-register-s');
				return;
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
				redirect('app-register-s');
				return;
			}

			$fname = $this->session->fname;
			$lname = $this->session->lname;
			$this->Patient_Model->addLogs(array(
				'usertype' => 'Secretary',
				'userid' => $this->session->userid,
				'action' => 'Appointment added by ' . $fname . ' ' . $lname,
			));

			$successMessage = 'Appointment successfully added. Queue number: ' . $result['queue_number'] . '.';
			if ($this->ajaxTransactionResponse(true, $successMessage, base_url('view-app-s'), array('queue_number' => $result['queue_number']))) return;
			$this->session->set_flashdata('SUCCESSMSG', $successMessage);
			redirect('view-app-s');
		}

		public function AppointmentAvailability()
		{
			$this->load->model('Appointment_Model', 'Appointment_Model');
			$physicianId = (int) $this->session->userdata['physician_id'];
			$clinicId = (int) $this->input->get('clinic_id');
			$assignment = $this->ClinicAssignment_Model->getAssignment($physicianId, $clinicId);
			$dates = (!$assignment || (int) $assignment->secretary_id !== (int) $this->session->userdata['userid'])
				? array()
				: $this->Appointment_Model->getAvailableDates($physicianId, $clinicId);
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
				$physician_id = $this->session->userdata['physician_id'];
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
				$dayQuery =  "SELECT * FROM `physician_sched`
				JOIN secretary on secretary.physician_id = physician_sched.physician_id
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
						$this->load->view('Secretary/Appointment_add', $data);
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


						$this->Patient_Model->insert($data);

						$fname = $this->session->fname;
				    $lname = $this->session->lname;
				    $user = 'Secretary';
				    $date = date("Y-m-d h:i:sa");
				    $action = 'Appointment added by ' . $fname . " ".$lname;
				       

				    $data1 = array(

				    	'usertype' => $user,
						'userid'   => $this->session->userid,
				    	//'date'=> $date,
				    	'action'   => $action
				     
				    );
				    $this->Patient_Model->addLogs($data1);

						$this->session->set_flashdata('SUCCESSMSG', "Appointment Successfully Added!!");
				//sms
						redirect('view-app-s');
					}
				}else {
					$this->session->set_flashdata('error', "The doctor won't be here in this chosen day");
					$data['getPatient'] = $this->Patient_Model->getPatient();
					$this->load->view('Secretary/Appointment_add', $data);
		//redirect('app-register');
}//end of else
			}//end of IF POST
			else
			{

				$data['getPatient'] = $this->Patient_Model->getPatient();   
				$this->load->view('Secretary/Appointment_add',$data);
			}
		}


		public function ViewAppointment()
		{
			$selectedDate = trim((string) $this->input->get('app_date', true));
			$this->load->view('Secretary/Appointment_view', array(
				'appointmentScope' => $selectedDate !== '' ? 'date' : 'all',
				'appointmentDate' => $selectedDate,
			));
		}

		public function ViewAppointmentToday()
		{
			$this->load->view('Secretary/Appointment_view', array('appointmentScope' => 'today'));
		}

		public function ViewAppointmentDone()
		{
			$this->load->view('Secretary/Appointment_view', array('appointmentScope' => 'done'));
		}

		public function ViewAppointmentCancelled()
		{
			$this->load->view('Secretary/Appointment_view', array('appointmentScope' => 'cancelled'));
		}

		public function AppointmentsData()
		{
			$this->load->model('Appointment_Model', 'Appointment_Model');
			$request = $this->input->get(null, true) ?: array();
			$result = $this->Appointment_Model->getAppointmentsData('secretary', (int) $this->session->userdata['physician_id'], $request, isset($request['scope']) ? $request['scope'] : 'all');
			return $this->jsonResponse($result);
		}

		public function AppointmentEdit()
		{
			$this->load->model('Appointment_Model', 'Appointment_Model');
			$appointmentId = (int) ($this->input->method(true) === 'GET' ? $this->input->get('id', true) : $this->input->post('appointment_id', true));
			if ($this->input->method(true) === 'GET') {
				$appointment = $this->Appointment_Model->getEditableAppointment($appointmentId, 'secretary', (int) $this->session->userdata['physician_id']);
				if (!$appointment) {
					return $this->jsonResponse(array('success' => false, 'message' => 'This appointment is no longer editable.'), 404);
				}
				return $this->jsonResponse(array('success' => true, 'appointment' => $appointment));
			}

			$result = $this->Appointment_Model->updateAppointmentAuthorized(
				$appointmentId,
				trim((string) $this->input->post('app_date', true)),
				$this->security->xss_clean($this->input->post('purpose')),
				'secretary',
				(int) $this->session->userdata['physician_id']
			);
			if ($result['success']) {
				$this->Patient_Model->addLogs(array(
					'usertype' => 'Secretary',
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
			$updated = $this->Appointment_Model->updateStatusAuthorized($appointmentId, $status, 'secretary', (int) $this->session->userdata['physician_id']);
			if ($updated) {
				$this->Patient_Model->addLogs(array(
					'usertype' => 'Secretary',
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
			$secretaryId = (int) $this->session->userdata['userid'];
			$physicianId = (int) $this->session->userdata['physician_id'];
			$data = array(
				'calendarHeader' => 'header/headerSec',
				'calendarRole' => 'Secretary',
				'calendarRoleKey' => 'secretary',
				'calendarDataUrl' => base_url('calendar-events-s'),
				'calendarListUrl' => base_url('view-app-s'),
				'calendarBookUrl' => base_url('app-register-s'),
				'calendarClinics' => $this->ClinicAssignment_Model->getClinicAssignmentsForPhysician($physicianId, $secretaryId),
			);

			$this->load->view('appointment/calendar', $data);
		}

		public function CalendarEvents()
		{
			$this->load->model('Calendar_Model', 'Calendar_Model');

			$secretaryId = (int) $this->session->userdata['userid'];
			$physicianId = (int) $this->session->userdata['physician_id'];
			$assignments = $this->ClinicAssignment_Model->getClinicAssignmentsForPhysician($physicianId, $secretaryId);
			$clinicIds = array();
			foreach ($assignments as $assignment) {
				$clinicIds[] = (int) $assignment->clinic_id;
			}

			$start = (string) $this->input->get('start', true);
			$end = (string) $this->input->get('end', true);
			$clinicId = (int) $this->input->get('clinic_id', true);
			$status = (string) $this->input->get('status', true);

			if (!preg_match('/^\d{4}-\d{2}-\d{2}/', $start) || !preg_match('/^\d{4}-\d{2}-\d{2}/', $end)) {
				return $this->jsonResponse(array('success' => false, 'message' => 'Invalid calendar date range.'), 422);
			}

			$events = $this->Calendar_Model->events(
				$physicianId,
				substr($start, 0, 10),
				substr($end, 0, 10),
				$clinicIds,
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
			$this->load->view('Secretary/Average_appoint',$data);
		}

		public function ViewDone() {

			$first = $this->input->post('first');
		//$last = $this->input->post('last');
			$this->load->model('Secretary_Model');
			$data['getDone'] = $this->Secretary_Model->getDone($first);
			$this->load->view('Secretary/Done_appoint',$data);
		}

		public function ViewCancelled() {

			$first = $this->input->post('first');
		//$last = $this->input->post('last');
			$this->load->model('Secretary_Model');
			$data['getDone'] = $this->Secretary_Model->getCancelled($first);
			$this->load->view('Secretary/Cancelled_appoint',$data);
		}

		public function ViewLimit()
		{
			$this->load->view('Secretary/Limit_view');
		}

		public function LimitRegister()
		{
			if (empty($_POST)) {
				$data['getPatient'] = $this->Patient_Model->getPatient();
				$this->load->view('Secretary/Set_limit', $data);
				return;
			}

			$this->load->model('Appointment_Model', 'Appointment_Model');
			$result = $this->Appointment_Model->saveDailyLimit(
				(int) $this->session->userdata['physician_id'],
				(string) $this->input->post('date'),
				(int) $this->input->post('limit')
			);

			if (!$result['success']) {
				if ($this->ajaxTransactionResponse(false, $result['message'])) return;
				$this->session->set_flashdata('error', $result['message']);
				redirect('limit-register-s');
				return;
			}

			$this->Patient_Model->addLogs(array(
				'usertype' => 'Secretary',
				'userid' => $this->session->userid,
				'action' => 'Queue limit added by ' . $this->session->fname . ' ' . $this->session->lname,
			));
			if ($this->ajaxTransactionResponse(true, 'Appointment limit registered successfully.', base_url('view-limit-s'))) return;
			$this->session->set_flashdata('SUCCESSMSG', 'Appointment limit registered successfully.');
			redirect('view-limit-s');
		}

		private function LimitRegisterLegacy()
		{


			if(!empty($_POST))
			{  
				$physician_id = $this->session->userdata['physician_id'];


				$date = $this->security->xss_clean($this->input->post('date'));
				$data = array(
					'queueLimit'      => $this->security->xss_clean($this->input->post('limit')),
					'physician_id'    => $physician_id,
					'dateLimit'       => $date
				);
				$result = $this->Physician_Model->CheckExistsLimit($date, $physician_id);
			    if($result -> num_rows() > 0)
			     {
			      $this->session->set_flashdata('error','Queue limit already existed for this date');
			      redirect('limit-register-s');
			  }else{
				$newLimit = $this->security->xss_clean($this->input->post('limit'));
				$this->Secretary_Model->insert_limit($data,$physician_id,$newLimit);

				$fname = $this->session->fname;
				    $lname = $this->session->lname;
				    $user = 'Secretary';
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
				redirect('view-limit-s');
				}
			}else{
				$data['getPatient'] = $this->Patient_Model->getPatient();
				$this->load->view('Secretary/Set_limit',$data);
			}

		}

		public function UpdateStatus()
		{
			if (empty($_POST)) {
				redirect('view-app-s');
				return;
			}

			$this->load->model('Appointment_Model', 'Appointment_Model');
			$updated = $this->Appointment_Model->updateStatusAuthorized(
				(int) $this->input->post('appointment_id'),
				(string) $this->input->post('status'),
				'secretary',
				(int) $this->session->userdata['physician_id']
			);

			if ($updated) {
				$this->Patient_Model->addLogs(array(
					'usertype' => 'Secretary',
					'userid' => $this->session->userid,
					'action' => 'Appointment #' . (int) $this->input->post('appointment_id') . ' marked ' . (string) $this->input->post('status') . ' by ' . $this->session->fname . ' ' . $this->session->lname,
				));
				$this->session->set_flashdata('SUCCESSMSG1', 'Appointment status updated successfully.');
			} else {
				$this->session->set_flashdata('error', 'The appointment could not be updated. It may already be completed/cancelled or it is outside your assigned physician.');
			}

			redirect('view-app-s');
		}

		private function UpdateStatusLegacy()
		{
			if(!empty($_POST))
			{
				$data = array(

					'app_status'       => $this->input->post('status')
				);
				$old_app_id = $this->input->post('appointment_id');
				$this->Secretary_Model->updateStatus($data,$old_app_id);
				$this->session->set_flashdata('SUCCESSMSG', "Appointment Status Updated Successfully!!");
				redirect('view-app-s');
			}
			else
			{

				$old_app_id = $this->input->post('appointment_id');
				$this->Secretary_Model->updateStatus($data,$old_app_id);
				$data['getAppointment'] = $this->Secretary_Model->getAppointment();
				$this->load->view('Secretary/Appointment_view',$data);
			}
		}


		public function LimitUpdateView() {
			$this->load->helper('form');
			$id = $this->uri->segment('2');
			$query = $this->db->get_where('queuecount', array(
				'id' => (int) $id,
				'physician_id' => (int) $this->session->userdata['physician_id'],
			));
			$data['getLimit'] = $query->result();
			$data['old_limit_id'] = $id;
			$this->load->view('Secretary/Limit_edit',$data);
		}  
		public function LimitUpdate()
		{
			if (empty($_POST)) {
				redirect('view-limit-s');
				return;
			}

			$this->load->model('Appointment_Model', 'Appointment_Model');
			$result = $this->Appointment_Model->saveDailyLimit(
				(int) $this->session->userdata['physician_id'],
				'',
				(int) $this->input->post('queueLimit'),
				(int) $this->input->post('old_limit_id')
			);

			if (!$result['success']) {
				if ($this->ajaxTransactionResponse(false, $result['message'])) return;
				$this->session->set_flashdata('error', $result['message']);
				redirect('view-limit-s');
				return;
			}

			$this->Patient_Model->addLogs(array(
				'usertype' => 'Secretary',
				'userid' => $this->session->userid,
				'action' => 'Queue limit updated by ' . $this->session->fname . ' ' . $this->session->lname,
			));
			if ($this->ajaxTransactionResponse(true, 'Appointment limit updated successfully.', base_url('view-limit-s'))) return;
			$this->session->set_flashdata('SUCCESSMSG1', 'Appointment limit updated successfully.');
			redirect('view-limit-s');
		}

		private function LimitUpdateLegacy()
		{
			$physician_id = $this->session->userdata['physician_id'];
			if(!empty($_POST))
			{
				$data = array(


					'queueLimit'     => $this->security->xss_clean($this->input->post('queueLimit')),

				);
				$newLimit = $this->input->post('queueLimit');
				$id = $this->security->xss_clean($this->input->post('old_limit_id'));
				$this->Admin_Model->updateLimit($data,$id, $physician_id, $newLimit);

				$fname = $this->session->fname;
				    $lname = $this->session->lname;
				    $user = 'Secretary';
				    $date = date("Y-m-d h:i:sa");
				    $action = 'Queue limit updated by ' . $fname . " ".$lname;
				       

				    $data1 = array(

				    	'usertype' => $user,
						'userid'   => $this->session->userid,
				    	//'date'=> $date,
				    	'action'   => $action
				     
				    );
				    $this->Patient_Model->addLogs($data1);

				$this->session->set_flashdata('SUCCESSMSG1', "Patient Updated Successfully!!");
				redirect('view-limit-s');


			}
			else
			{
				$id = $this->security->xss_clean($this->input->post('old_limit_id'));
				$this->Admin_Model->updateLimit($data,$id);
				$data['getLimit'] = $this->Secretary_Model->getLimit();   
				$this->load->view('Secretary/Limit_view',$data);
			}
		}





	}
	?>
