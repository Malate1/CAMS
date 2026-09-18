<?php
	class Admin extends CI_Controller {

		function __construct()
		{
			parent::__construct();            
			if($this->session->email == "" || $this->session->role !== 'Admin')
			{
				redirect('login');
				return;
			}
			$this->load->model('Admin_Model','Admin_Model');
			$this->load->model('Secretary_Model','Secretary_Model');
			$this->load->model('Patient_Model','Patient_Model');
			$this->load->model('Physician_Model','Physician_Model');
			$this->load->model('Clinic_Model','Clinic_Model');
			$this->load->model('ClinicAssignment_Model','ClinicAssignment_Model');
			$this->load->model('Dashboard_Model','Dashboard_Model');
			$this->ClinicAssignment_Model->ensureSchema();
			$this->load->library('form_validation');
			//$this->load->library('security');
			$this->load->helper('url');
			$this->load->database();    
			$this->load->helper('form'); 
			$this->load->library('session');
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

		public function searchSecfunction()
	    {
	      $data['getSecretary']=$this->Admin_Model->GetSearchdataSec();
	      $this->load->view('Admin/Secretary_view',$data);
	    }   

		public function index() {

			$userid = $this->session->userdata['userid'];
			$data['records'] = $this->Admin_Model->view_info($userid);
			$data['dashboard'] = $this->Dashboard_Model->adminDashboard();
			$this->load->view('Admin/dashboard', $data);
		}
		/*Function for Admin*/

		public function ViewLogs()
		{
			$this->load->view('Admin/Logs_view');
		}  

		public function ViewSecretary()
		{
			$data['getPhysician'] = $this->Physician_Model->getPhysician();
			$this->load->view('Admin/Secretary_view',$data);
		}


		public function ViewPatient()
		{
			$this->load->view('Admin/Patient_view');
		}

		public function ViewPhysician()
		{
			$this->load->view('Admin/Physician_view');
		}



		public function ViewClinic()
		{
			$data['getPhysician'] = $this->Physician_Model->getPhysician();
			$data['getSecretary'] = $this->Secretary_Model->getSecretary();
			$data['allClinics'] = $this->db->order_by('name', 'ASC')->get('clinic')->result();
			$specialRows = $this->db
				->select('physician_special.physician_id, specialization.special_id, specialization.special_name')
				->from('physician_special')
				->join('specialization', 'specialization.special_id = physician_special.special_id')
				->order_by('specialization.special_name', 'ASC')
				->get()->result();
			$data['physicianSpecializations'] = array();
			foreach ($specialRows as $specialRow) {
				$physicianId = (int) $specialRow->physician_id;
				if (!isset($data['physicianSpecializations'][$physicianId])) {
					$data['physicianSpecializations'][$physicianId] = array();
				}
				$data['physicianSpecializations'][$physicianId][] = array(
					'id' => (int) $specialRow->special_id,
					'name' => trim((string) $specialRow->special_name),
				);
			}
			$this->load->view('Admin/Clinic_view',$data);
		}

		public function ClinicRegister()
		{
			date_default_timezone_set('Asia/Manila');

			if(!empty($_POST))
			{  
				$physician_id = (int) $this->security->xss_clean($this->input->post('physician_id'));
				$secretary_id = (int) $this->security->xss_clean($this->input->post('secretary_id'));
				$postedSpecialIds = $this->input->post('special_ids');
				if (!is_array($postedSpecialIds)) $postedSpecialIds = array();
				$specialIds = array_values(array_unique(array_filter(array_map('intval', $postedSpecialIds))));

				if (!$specialIds) {
					$message = 'Please select at least one specialization for this clinic.';
					if ($this->ajaxTransactionResponse(false, $message)) return;
					$this->session->set_flashdata('error', $message);
					redirect('view-clinic-a');
					return;
				}

				foreach ($specialIds as $specialId) {
					$validSpecialization = $this->db
						->where('physician_id', $physician_id)
						->where('special_id', $specialId)
						->count_all_results('physician_special') > 0;
					if (!$validSpecialization) {
						$message = 'One or more selected specializations are not assigned to the selected physician.';
						if ($this->ajaxTransactionResponse(false, $message)) return;
						$this->session->set_flashdata('error', $message);
						redirect('view-clinic-a');
						return;
					}
				}

				$existingClinicId = (int) $this->input->post('existing_clinic_id');
				if ($existingClinicId > 0) {
					$clinicExists = $this->db->where('clinic_id', $existingClinicId)->count_all_results('clinic') > 0;
					$duplicateAssignment = $this->db->where('physician_id', $physician_id)->where('clinic_id', $existingClinicId)->count_all_results('physician_clinic') > 0;
					if (!$clinicExists || $duplicateAssignment) {
						$message = !$clinicExists ? 'The selected clinic could not be found.' : 'This physician is already assigned to the selected clinic.';
						if ($this->ajaxTransactionResponse(false, $message)) return;
						$this->session->set_flashdata('error', $message);
						redirect('view-clinic-a');
						return;
					}
					$clinic = $existingClinicId;
				} else {
					$data1 = array(
						'name'            => $this->security->xss_clean($this->input->post('name')),
						'bir'             => $this->security->xss_clean($this->input->post('bir')),
						'businessPermit'  => $this->security->xss_clean($this->input->post('businessPermit')),
						'contact'         => $this->security->xss_clean($this->input->post('contact')),
						'location'        => $this->security->xss_clean($this->input->post('location'))
					);
					$this->Clinic_Model->insert_clinic($data1);
					$clinic = (int) $this->db->insert_id();
				}
				$data = array(
					'clinic_id' => $clinic,
					'physician_id' =>$physician_id,
					'secretary_id' =>$secretary_id,

				);

				$this->Clinic_Model->insert_pclinic($data);
				$assignment = $this->ClinicAssignment_Model->getAssignment($physician_id, $clinic);
				if ($assignment) {
					$this->ClinicAssignment_Model->replaceSpecializations($assignment->id, $specialIds);
				}

				$data2 = array(
					
					'physician_id' =>$physician_id
				);

				$this->Secretary_Model->insert_pid($data2,$secretary_id);

				$fname = $this->session->fname;
				$lname = $this->session->lname;
				$user = 'Admin';
				$date = date("Y-m-d h:i:sa");
				$action = 'Clinic added by ' . $fname . " ".$lname;

				$data1 = array(
					'usertype' => $user, 
					'userid'   => $this->session->userid, ////'date'=> $date,
					//'date'=> $date,
					'action'   => $action
				);
				$this->Patient_Model->addLogs($data1);


				if ($this->ajaxTransactionResponse(true, 'Clinic registered successfully.', base_url('view-clinic-a'))) return;
				$this->session->set_flashdata('SUCCESSMSG', "Clinic Register Successfully!!");
				redirect('view-clinic-a');

			}else{
				$data['getClinic'] = $this->Clinic_Model->getClinic();
				$this->load->view('Admin/Clinic_view');
			}

		}

		public function ClinicUpdateView() {

			$this->load->helper('form');
			$assignmentKey = (int) $this->uri->segment('2');
			$data['assignment'] = $this->db
				->select("physician_clinic.*, CONCAT(physician.fname, ' ', physician.lname) AS physician_name", false)
				->from('physician_clinic')
				->join('physician', 'physician.physician_id = physician_clinic.physician_id')
				->where('physician_clinic.id', $assignmentKey)
				->limit(1)->get()->row();
			if (!$data['assignment']) {
				$data['assignment'] = $this->db
					->select("physician_clinic.*, CONCAT(physician.fname, ' ', physician.lname) AS physician_name", false)
					->from('physician_clinic')
					->join('physician', 'physician.physician_id = physician_clinic.physician_id')
					->where('physician_clinic.clinic_id', $assignmentKey)
					->limit(1)->get()->row();
			}
			if (!$data['assignment']) {
				redirect('view-clinic-a');
				return;
			}
			$clinic_id = (int) $data['assignment']->clinic_id;
			$query = $this->db->get_where("clinic",array("clinic_id"=>$clinic_id));
			$data['getClinic'] = $query->result();
			$data['old_clinic_id'] = $clinic_id;
			$data['getSecretary'] = $this->Secretary_Model->getSecretary();
			$data['availableSpecializations'] = array();
			$data['selectedSpecialIds'] = array();
			if ($data['assignment']) {
				$data['availableSpecializations'] = $this->db
					->select('specialization.special_id, specialization.special_name')
					->from('physician_special')
					->join('specialization', 'specialization.special_id = physician_special.special_id')
					->where('physician_special.physician_id', (int) $data['assignment']->physician_id)
					->order_by('specialization.special_name', 'ASC')->get()->result();
				$selected = $this->db->select('special_id')->from('physician_clinic_specialization')
					->where('physician_clinic_id', (int) $data['assignment']->id)->get()->result();
				foreach ($selected as $item) $data['selectedSpecialIds'][] = (int) $item->special_id;
			}
			$this->load->view('Admin/Clinic_edit',$data);
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
				$secretaryId = (int) $this->input->post('secretary_id');
				$postedSpecialIds = $this->input->post('special_ids');
				if (!is_array($postedSpecialIds)) $postedSpecialIds = array();
				$specialIds = array_values(array_unique(array_filter(array_map('intval', $postedSpecialIds))));
				$assignment = $this->ClinicAssignment_Model->getAssignmentById($assignmentId);
				if (!$assignment || (int) $assignment->clinic_id !== $clinic_id) {
					if ($this->ajaxTransactionResponse(false, 'The clinic assignment could not be found.')) return;
					redirect('view-clinic-a');
					return;
				}
				if (!$specialIds) {
					if ($this->ajaxTransactionResponse(false, 'Please select at least one specialization.')) return;
					redirect('view-clinic-a');
					return;
				}
				foreach ($specialIds as $specialId) {
					$valid = $this->db->where('physician_id', (int) $assignment->physician_id)->where('special_id', $specialId)->count_all_results('physician_special') > 0;
					if (!$valid) {
						if ($this->ajaxTransactionResponse(false, 'One or more selected specializations are not assigned to this physician.')) return;
						redirect('view-clinic-a');
						return;
					}
				}
				$this->Clinic_Model->update($data,$clinic_id);
				$this->db->where('id', $assignmentId)->update('physician_clinic', array('secretary_id' => $secretaryId));
				$this->ClinicAssignment_Model->replaceSpecializations($assignmentId, $specialIds);
				if ($secretaryId > 0) {
					$this->Secretary_Model->insert_pid(array('physician_id' => (int) $assignment->physician_id), $secretaryId);
				}

				//date("m-d-Y | h:i:s A", strtotime($value->date))

				$fname = $this->session->fname;
				$lname = $this->session->lname;
				$user = 'Admin';
				date_default_timezone_set('Asia/Manila');
				$date = date("m-d-Y h:i:s A");

				$action = 'Clinic updated by ' . $fname . " ".$lname;


				$data1 = array(

					'usertype' => $user, 
					'userid'   => $this->session->userid,
					////'date'=> $date,
					'action'   => $action

				);
				$this->Patient_Model->addLogs($data1);
				if ($this->ajaxTransactionResponse(true, 'Clinic record updated successfully.', base_url('view-clinic-a'))) return;
				$this->session->set_flashdata('SUCCESSMSG', "Clinic Record Updated Successfully!!");
				redirect('view-clinic-a');
			}
			else
			{
				$clinic_id = $this->security->xss_clean($this->input->post('old_clinic_id'));
				$this->Clinic_Model->update($data,$clinic_id);
				$data['getClinic'] = $this->Clinic_Model->getClinic();
				$this->load->view('Admin/Clinic_edit',$data);
			}
		}




		public function ClinicDelete() {

			$Clinic_id = $this->uri->segment('3');
			$this->Clinic_Model->delete($Clinic_id);
			$this->session->set_flashdata('SUCCESSMSG', "Clinic Deleted Successfully!!");
			$data['getClinic'] = $this->Clinic_Model->getClinic();
			$this->load->view('Admin/Clinic_view',$data);
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
				$this->load->view('Admin/Change_pass');
			}
			else
			{
						//$old = $this->input->post('oldPassword');
				$old = $this->security->xss_clean(md5($this->input->post('oldPassword')));
				$admin_id = $this->session->userid;
				$info = array(
					'password' => $this->security->xss_clean(md5($this->input->post('newPassword'))),

				);
				$query = $this->Admin_Model->CheckOld($admin_id,$old);
				if($query -> num_rows() > 0){
					$result = $this->Admin_Model->changePassword($admin_id, $info);     
					$fname = $this->session->fname;
					$lname = $this->session->lname;
					$user = 'Admin';
					$date = date("Y-m-d h:i:sa");
					$action = 'Password updated by ' . $fname . " ".$lname;


					$data1 = array(

						'usertype' => $user, 
						'userid'   => $this->session->userid, ////'date'=> $date,
						//'date'=> $date,
						'action'   => $action

					);
					$this->Patient_Model->addLogs($data1);
					$successMessage = 'Password updated successfully.';
					if ($this->ajaxTransactionResponse(true, $successMessage)) return;
					$this->session->set_flashdata('success', $successMessage);
					redirect('change-pass');
				}else{
					if ($this->ajaxTransactionResponse(false, 'Old password is incorrect.')) return;
					$this->session->set_flashdata('errormsg','Old password is incorrect');
					$this->load->view('Admin/Change_pass');
				} 
			}
		}

		public function PatientUpdateView() {
			$this->load->helper('form');
			$patient_id = $this->uri->segment('2');
			$query = $this->db->get_where("patient",array("patient_id"=>$patient_id));
			$data['getPatient'] = $query->result();
			$data['old_patient_id'] = $patient_id;
			$this->load->view('Admin/Patient_edit',$data);
		}  
		public function PatientUpdate()
		{
			if(!empty($_POST))
			{
				$data = array(


					'password'     => $this->security->xss_clean(md5($this->input->post('newPassword'))),

				);
				$patient_id = $this->input->post('old_patient_id');
				$this->Patient_Model->update($data,$patient_id);
				$successMessage = "Patient password updated successfully.";

				$fname = $this->session->fname;
				$lname = $this->session->lname;
				$user = 'Admin';
				$date = date("Y-m-d h:i:sa");
				$action = 'Patient password updated by ' . $fname . " ".$lname;


				$data1 = array(

					'usertype' => $user, 
					'userid'   => $this->session->userid, ////'date'=> $date,
					//'date'=> $date,
					'action'   => $action

				);
				$this->Patient_Model->addLogs($data1);
				if ($this->ajaxTransactionResponse(true, $successMessage, base_url('view-patient-a'))) return;
				$this->session->set_flashdata('SUCCESSMSG', $successMessage);
				redirect('view-patient-a');


			}
			else
			{
				$patient_id = $this->input->post('old_patient_id');
				$this->Patient_Model->update($data,$patient_id);
				$data['getPatient'] = $this->Patient_Model->getPatient();
				$this->load->view('Admin/Patient_edit',$data);
			}
		}


		public function PhysicianUpdateView() {
			$this->load->helper('form');
			$physician_id = $this->uri->segment('2');
			$query = $this->db->get_where("physician",array("physician_id"=>$physician_id));
			$data['getPhysician'] = $query->result();
			$data['old_physician_id'] = $physician_id;
			$this->load->view('Admin/Physician_edit',$data);

		}


		public function PhysicianUpdate()
		{
			if(!empty($_POST))
			{
				$data = array(


					'password'     => $this->security->xss_clean(md5($this->input->post('newPassword'))),
				);
				$physician_id = $this->input->post('old_physician_id');
				$this->Physician_Model->update($data,$physician_id);
				$successMessage = 'Physician password updated successfully.';
				$fname = $this->session->fname;
				$lname = $this->session->lname;
				$user = 'Admin';
				$date = date("Y-m-d h:i:sa");
				$action = 'Physician password updated by ' . $fname . " ".$lname;


				$data1 = array(

					'usertype' => $user, 
					'userid'   => $this->session->userid, ////'date'=> $date,
					//'date'=> $date,
					'action'   => $action

				);
				$this->Patient_Model->addLogs($data1);
				if ($this->ajaxTransactionResponse(true, $successMessage, base_url('view-physician-a'))) return;
				$this->session->set_flashdata('SUCCESSMSG', $successMessage);
				redirect('view-physician-a');


			}
			else
			{
				$physician_id = $this->input->post('old_physician_id');
				$this->physician_Model->update($data,$physician_id);
				$data['getPhysician'] = $this->admin_Model->getPhysician();
				$this->load->view('Admin/Physician_edit',$data);
			}
		}

		public function SecretaryUpdateView() {
			$this->load->helper('form');
			$secretary_id = $this->uri->segment('2');
			$query = $this->db->get_where("secretary",array("secretary_id"=>$secretary_id));
			$data['getSecretary'] = $query->result();
			$data['old_secretary_id'] = $secretary_id;
			$this->load->view('Admin/Secretary_edit',$data);
		}




		public function SecretaryUpdate()
		{
			if(!empty($_POST))
			{
				$data = array(								 
					'password'     => $this->security->xss_clean(md5($this->input->post('newPassword'))),
				);
				$secretary_id = $this->input->post('old_secretary_id');
				$this->Secretary_Model->update($data,$secretary_id);
				$successMessage = 'Secretary password updated successfully.';
				$fname = $this->session->fname;
				$lname = $this->session->lname;
				$user = 'Admin';
				$date = date("Y-m-d h:i:sa");
				$action = 'Secretary password updated by ' . $fname . " ".$lname;


				$data1 = array(

					'usertype' => $user, 
'userid'   => $this->session->userid, ////'date'=> $date,
					//'date'=> $date,
					'action'   => $action

				);
				$this->Patient_Model->addLogs($data1);
				if ($this->ajaxTransactionResponse(true, $successMessage, base_url('view-secretary-a'))) return;
				$this->session->set_flashdata('SUCCESSMSG', $successMessage);
				redirect('view-secretary-a');
			}
			else
			{

				$secretary_id = $this->input->post('old_secretary_id');
				$this->Secretary_Model->update($data,$secretary_id);
				$data['getSecretary'] = $this->Secretary_Model->getSecretary();
				$this->load->view('Admin/Secretary_edit',$data);
			}
		}


		public function ProfileUpdateView() {

			$this->load->helper('form');
			$admin_id = $this->session->userid;
			$query = $this->db->get_where("admin",array("admin"=>$admin_id));
			$data['records'] = $query->result();

			$this->load->view('Admin/Update_profile',$data);
		}

		public function ProfileUpdate()
		{
			if(!empty($_POST))
			{ 
					//$imgUrl = $this->EdituploadImage();

					// if($img_url == '') {
					//   $img_url = $this->session->image;
					// }
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
				$admin_id = $this->session->userid;
				$this->Admin_Model->updateProfile($data,$admin_id);
				$this->session->set_userdata($data);
				$fname = $this->session->fname;
				$lname = $this->session->lname;
				$user = 'Admin';
				$date = date("Y-m-d h:i:sa");
				$action = 'Profile updated by ' . $fname . " ".$lname;


				$data1 = array(

					'usertype' => $user, 
'userid'   => $this->session->userid, ////'date'=> $date,
					//'date'=> $date,
					'action'   => $action

				);
				$this->Patient_Model->addLogs($data1);

				$successMessage = 'Profile updated successfully.';
				if ($this->ajaxTransactionResponse(true, $successMessage, '', array(
					'profile' => array(
						'display_name' => trim($data['fname'] . ' ' . $data['lname']),
					),
				))) return;

				$this->session->set_flashdata('success', $successMessage);
				redirect('update-profile');


			}
			else
			{
				$admin_id = $this->session->userid;
					//$this->Admin_Model->updateProfile($data,$admin_id);
				$data['records'] = $this->Admin_Model->view_info($admin_id);
				$this->load->view('Admin/Update_profile',$data);
			}
		}

		public function ProfilePicUpdateView() {

			$this->load->helper('form');
			$admin_id = $this->session->userid;
			$query = $this->db->get_where("admin",array("admin"=>$admin_id));
			$data['records'] = $query->result();

			$this->load->view('Admin/Update_profile',$data);
		}

		public function ProfilePicUpdate()
		{
			if(!empty($_POST))
			{ 
				$imgUrl = $this->EdituploadImage();
				if (!$imgUrl) {
					if ($this->ajaxTransactionResponse(false, 'Please select a valid image file.')) return;
					$this->session->set_flashdata('error', 'Please select a valid image file.');
					redirect('update-profile');
					return;
				}
				$data = array(

					'image'   => $imgUrl
				);

					// $admin_id = $this->input->post('old_admin_id');
					//  $this->Admin_Model->update($data,$admin_id);

				$admin_id = $this->session->userid;
				$this->Admin_Model->updateProfilePic($data,$admin_id);
				$this->session->set_userdata($data);
				$fname = $this->session->fname;
				$lname = $this->session->lname;
				$user = 'Admin';
				$date = date("Y-m-d h:i:sa");
				$action = 'Profile picture updated by ' . $fname . " ".$lname;


				$data1 = array(

					'usertype' => $user, 
'userid'   => $this->session->userid, ////'date'=> $date,
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
				redirect('update-profile');


			}
			else
			{
				$admin_id = $this->session->userid;
					//$this->Admin_Model->updateProfile($data,$admin_id);
				$data['records'] = $this->Admin_Model->view_info($admin_id);
				$this->load->view('Admin/Update_profilePic',$data);
			}
		}

		public function EdituploadImage() 
		{
			$type = explode('.', $_FILES['Editphoto']['name']);       
			$type = $type[count($type)-1];    
			$url = 'uploads/profile-pic/'.$_FILES['Editphoto']['name'];
			$filename = $_FILES['Editphoto']['name'];

			if(in_array($type, array('gif', 'jpg', 'jpeg', 'png', 'JPG', 'GIF', 'JPEG', 'PNG', 'pdf', 'docx', 'xls','csv', 'PDF',  'DOCX', 'XLS', 'DOC', 'CSV'))) {
				if(is_uploaded_file($_FILES['Editphoto']['tmp_name'])) {      
					if(move_uploaded_file($_FILES['Editphoto']['tmp_name'], $url)) {
						return $filename;
					} else {
						return false;
					}     
				}
			} 
		}

		public function ViewTopVisited() {

			$first = $this->input->post('first');
			$last = $this->input->post('last');
			$this->load->model('Admin_Model');
			$data['getTopVisited'] = $this->Admin_Model->getTopVisited($first,$last);
			$this->load->view('Admin/Top_visit',$data);
		}

		public function ViewTopConsulted() {

			$first = $this->input->post('first');
			$last = $this->input->post('last');
			$this->load->model('Admin_Model');
			$data['getTopConsulted'] = $this->Admin_Model->getTopConsulted($first,$last);
			$this->load->view('Admin/Top_consult',$data);
		}

		public function ViewAverage() {

			$first = $this->input->post('first');
			//$last = $this->input->post('last');
			$data['yearMonth'] = $first;
			$this->load->model('Admin_Model');
			$data['getAverage'] = $this->Admin_Model->getAverage($first);
			$this->load->view('Admin/Average_appoint',$data);
		}

		public function UpdateStatusP()
		{
			if(!empty($_POST))
			{
				$data = array(

					'status'       => $this->input->post('status')
				);
				$old_patient_id = $this->input->post('patient_id');
				$this->Admin_Model->updateStatus($data,$old_patient_id);
				$this->session->set_flashdata('SUCCESSMSG', " Status Updated Successfully!!");
				redirect('view-patient-a');
			}
			else
			{

				$old_patient_id = $this->input->post('patient_id');
				$this->Admin_Model->updateStatus($data,$old_patient_id);
				$data['getPatient'] = $this->Patient_Model->getPatient();   
				$this->load->view('Admin/Patient_view',$data);
			}
		}

		public function UpdateStatusPhy()
		{
			if(!empty($_POST))
			{
				$data = array(

					'status'       => $this->input->post('status')
				);
				$old_physician_id = $this->input->post('physician_id');
				$this->Admin_Model->updateStatusPhysician($data,$old_physician_id);
				$this->session->set_flashdata('SUCCESSMSG', " Status Updated Successfully!!");
				redirect('view-physician-a');
			}
			else
			{

				$old_physician_id = $this->input->post('physician_id');
				$this->Admin_Model->updateStatusPhysician($data,$old_physician_id);
				$data['getPhysician'] = $this->Physician_Model->getPhysician();   
				$this->load->view('Admin/Physician_view',$data);
			}
		} 
		public function UpdateStatusS()
		{
			if(!empty($_POST))
			{
				$data = array(

					'status'       => $this->input->post('status')
				);
				$old_secretary_id = $this->input->post('secretary_id');
				$this->Admin_Model->updateStatusSec($data,$old_secretary_id);
				$this->session->set_flashdata('SUCCESSMSG', " Status Updated Successfully!!");
				redirect('view-secretary-a');
			}
			else
			{

				$old_secretary_id = $this->input->post('secretary_id');
				$this->Admin_Model->updateStatusSec($data,$old_secretary_id);
				$data['getSecretary'] = $this->Secretary_Model->getSecretary();   
				$this->load->view('Admin/Secretary_view',$data);
			}
		}    
		






	}


	?>
