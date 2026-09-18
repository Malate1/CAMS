<?php
	 class Patient_Model extends CI_Model {

		function __construct() {
		 parent::__construct();
	 }
	 public function view_info ($userid) {
		$this->db->select("*");
		$this->db->where('patient_id',$userid);
		$this->db->from('patient');

		$query = $this->db->get();
		return $query->result();
	}
	public function CheckExists($email)
	{
		$this ->db->select(' * ');
		$this ->db->from('patient');
		$this ->db->where('email', $email);
		
		$this ->db->limit(1);
		$query = $this->db-> get();
		return $query;
	}

	public function CheckExists2($email)
	{
		$this ->db->select(' * ');
		$this ->db->from('answer');
		$this ->db->where('email', $email);
		
		$this ->db->limit(1);
		$query = $this->db-> get();
		return $query;
	}

	public function checkOld($id,$password)
	{
		$this -> db->select(' * ');
		$this -> db->from('patient');
		$this -> db->where('patient_id', $id);
		$this -> db->where('password', $password);
		$this -> db->limit(1);
		$query = $this->db-> get();
		return $query;
	}

	public function checkAnswer($answer,$q_id,$email)
	{
		$this -> db->select(' * ');
		$this -> db->from('answer');
		$this -> db->where('email', $email);
		
		$this -> db->where('answer', $answer);
		$this -> db->where('q_id', $q_id);
		$this -> db->limit(1);
		$query = $this->db-> get();
		return $query;
	} 


	public function getPatient()
	{
		$query = $this->db->get('patient');
		return $query->result();
	}



	public function itexmo($number,$message,$apicode){
		$ch = curl_init();
		$itexmo = array('1' => $number, '2' => $message, '3' => $apicode);
		curl_setopt($ch, CURLOPT_URL,"https://www.itexmo.com/php_api/api.php");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 
			http_build_query($itexmo));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		return curl_exec ($ch);
		curl_close ($ch);
	}

	public function getRecepient($id)
	{
		$this->db->select("contact");
		$this->db->from("patient");
		$this->db->where("patient_id", $id);
		$query = $this->db->get();
		return $query->result();

	}

	public function getQuestion() {

		$userid = $this->session->userdata['userid'];
		$this->db->select('*, questions.question');
		$this->db->from('answer');
		$this->db->join('questions', 'questions.q_id = answer.q_id');
		$this->db->join('patient', 'patient.patient_id = answer.patient_id');
		$this->db->where("answer.patient_id = $userid");
		$query = $this->db->get();
		return $query->result();
	}

	
	
	public function getAppointment() {
		
		date_default_timezone_set('Asia/Manila');
		
		$userid = $this->session->userdata['userid'];
		$date = date('Y-m-d');
		$this->db->select('*, physician.lname, patient.fname');
		$this->db->from('appointment');
		$this->db->join('physician', 'physician.physician_id = appointment.physician_id');
		$this->db->join('patient', 'patient.patient_id = appointment.patient_id');
		$this->db->where(" appointment.patient_id = $userid");


		$query = $this->db->get();
		return $query->result();
	}

	public function getAppointmentToday() {
		
		date_default_timezone_set('Asia/Manila');
		
		$userid = $this->session->userdata['userid'];
		$date = date('Y-m-d');
		$this->db->select('*, physician.lname, patient.fname');
		$this->db->from('appointment');
		$this->db->join('physician', 'physician.physician_id = appointment.physician_id');
		$this->db->join('patient', 'patient.patient_id = appointment.patient_id');
		$this->db->where(" appointment.patient_id = '$userid' AND appointment.app_date = '$date' " );
		//$this->db->where("appointment.physician_id = '$userid' AND appointment.app_date = '$date'");

		$query = $this->db->get();
		return $query->result();
	}

	public function getAppointmentDone() {
		
		date_default_timezone_set('Asia/Manila');
		
		$userid = $this->session->userdata['userid'];
		$date = date('Y-m-d');
		$this->db->select('*, physician.lname, patient.fname');
		$this->db->from('appointment');
		$this->db->join('physician', 'physician.physician_id = appointment.physician_id');
		$this->db->join('patient', 'patient.patient_id = appointment.patient_id');
		$this->db->where(" appointment.patient_id = '$userid' AND appointment.app_status = 'Done' " );
		//$this->db->where("appointment.physician_id = '$userid' AND appointment.app_date = '$date'");

		$query = $this->db->get();
		return $query->result();
	}

	public function getAppointmentCancelled() {
		
		date_default_timezone_set('Asia/Manila');
		
		$userid = $this->session->userdata['userid'];
		$date = date('Y-m-d');
		$this->db->select('*, physician.lname, patient.fname');
		$this->db->from('appointment');
		$this->db->join('physician', 'physician.physician_id = appointment.physician_id');
		$this->db->join('patient', 'patient.patient_id = appointment.patient_id');
		$this->db->where(" appointment.patient_id = '$userid' AND appointment.app_status = 'Cancelled' " );
		//$this->db->where("appointment.physician_id = '$userid' AND appointment.app_date = '$date'");

		$query = $this->db->get();
		return $query->result();
	}

	
	public function insertP($data1) {
	 if ($this->db->insert("patient", $data1)) {
		return true;
	}
}

public function insert_answer($data2) {
	 if ($this->db->insert("answer", $data2)) {
		return true;
	}
}

public function insert($data) {
 if ($this->db->insert("appointment", $data)) {
	return true;
}
}

public function insert_queue($data1) {
 if ($this->db->insert("queuecount", $data1)) {
	return true;
}
}

public function insert_secQ($data) {
 if ($this->db->insert("answer", $data)) {
	return true;
}
}

public function update($data,$old_patient_id) {
 $this->db->set($data);
 $this->db->where("patient_id", $old_patient_id);
 $this->db->update("patient", $data);
}

public function updateRecovery($data,$email) {
 $this->db->set($data);
 $this->db->where("email", $email);
 $this->db->update("patient", $data);
}

public function addLogs($data) {

date_default_timezone_set('Asia/Manila');
 if ($this->db->insert("logs", $data)) {
	return true;
}
}

public function changePassword($patient_id, $info)
{
	$this->db->set($info);
	$this->db->where('patient_id', $patient_id);
	
	$this->db->update('patient', $info);
	
	return $this->db->affected_rows();
}

public function updateProfile($data,$patient_id)
{
	$this->db->set($data);
	$this->db->where('patient_id', $patient_id);
	
	$this->db->update('patient', $data);
	
}  


public function updateProfilePic($data,$patient_id)
	 	{
	 		$this->db->set($data);
	 		$this->db->where('patient_id', $patient_id);

	 		$this->db->update('patient', $data);  
	 		return $this->db->affected_rows();

	 	}

public function getDirectory(){
	
				//$id = $_SESSION['Broker_Id'];
				//$special = $this->input->post('specialization');
	$this->db->select('physician.physician_id, physician.lname,schedule.day,schedule.time_in,
		schedule.time_out, physician.fname,clinic.location,clinic.clinic_id');
	$this->db->from('physician');
	
	$this->db->join('physician_clinic', 'physician.physician_id = physician_clinic.physician_id');
	$this->db->join('clinic', 'physician_clinic.clinic_id = clinic.clinic_id');
	$this->db->join('physician_sched','physician.physician_id = physician_sched.physician_id');
	$this->db->join('schedule', 'physician_sched.schedule_id = schedule.schedule_id');
				 //$this->db->where('specialization.special_name', $special);
	$query = $this->db->get();
	return $query->result();
}

public function getSchedule()
{
	
	$this->db->select('schedule.day,schedule.time_in,schedule.time_out, physician.lname');
	$this->db->from('physician_sched');
	$this->db->join('physician', 'physician.physician_id = physician_sched.physician_id');
	$this->db->join('schedule', 'schedule.schedule_id = physician_sched.schedule_id');
			//$this->db->where("physician_sched.physician_id", $userid);
	$query = $this->db->get();
	return $query->result();
}

public function updateStatus($data,$old_app_id) {
 $this->db->set($data);
 $this->db->where("appointment_id", $old_app_id);
 $this->db->update("appointment", $data);
}

public function Count(){
	date_default_timezone_set('Asia/Manila');

	$userid = $this->session->userdata['userid'];
	$date = date('Y-m-d');

	$userid = $this->session->userdata['userid'];

	$this->db->select('COUNT(queueNum) as visits, appointment.app_date');
	$this->db->from('appointment');
	$this->db->join('physician', 'physician.physician_id = appointment.physician_id');
	$this->db->join('patient', 'patient.patient_id = appointment.patient_id');
	$this->db->where("appointment.patient_id = '$userid' AND appointment.app_date = '$date' AND appointment.app_status = 'Pending' ");
			//$this->db->group_by('clinic.name');
			//$this->db->order_by('visits', 'DESC');
	$query = $this->db->get();
	return $query->result();
}

public function CountDone(){
	date_default_timezone_set('Asia/Manila');

	$userid = $this->session->userdata['userid'];
	$date = date('Y-m-d');

			//$userid = $this->session->userdata['userid'];

	$this->db->select('*,COUNT(queueNum) as visits, appointment.app_date');
	$this->db->from('appointment');
	$this->db->join('physician', 'physician.physician_id = appointment.physician_id');
	$this->db->join('patient', 'patient.patient_id = appointment.patient_id');
	$this->db->where("appointment.patient_id = '$userid'  AND appointment.app_status = 'Done'");
			//$this->db->group_by('clinic.name');
			//$this->db->order_by('visits', 'DESC');
	$query = $this->db->get();
	return $query->result();
}

public function CountCancel(){
	date_default_timezone_set('Asia/Manila');

	$userid = $this->session->userdata['userid'];
	$date = date('Y-m-d');

			//$userid = $this->session->userdata['userid'];

	$this->db->select('COUNT(queueNum) as visits, appointment.app_date, patient.lname, patient.fname');
	$this->db->from('appointment');
	$this->db->join('physician', 'physician.physician_id = appointment.physician_id');
	$this->db->join('patient', 'patient.patient_id = appointment.patient_id');
	$this->db->where("appointment.patient_id = '$userid'  AND appointment.app_status = 'Cancelled'");
			//$this->db->group_by('clinic.name');
			//$this->db->order_by('visits', 'DESC');
	$query = $this->db->get();
	return $query->result();
} 







}
?>
