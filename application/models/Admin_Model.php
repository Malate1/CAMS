<?php
	 class Admin_Model extends CI_Model {

	 	function __construct() {
	 		parent::__construct();
	 	}
	 	public function view_info ($userid) {
	 		$this->db->select("*");
	 		$this->db->where('admin_id',$userid);
	 		$this->db->from('admin');

	 		$query = $this->db->get();
	 		return $query->result();
	 	}

	 	public function GetSearchdataSec()
		{
		    $this->db->select("*");  
		    
		    $query = $this->db->get("secretary"); 
		    $this->db->like($this->input->get('search'));
		    return $query->result();
		}

	 	public function getAdmin()
	 	{
	 		$query = $this->db->get('admin');
	 		return $query->result();
	 	}
	 	public function getSecretary()
	 	{
	 		$query = $this->db->get('secretary');
	 		return $query->result();
	 	}

	 	public function getLogs()
	 	{
	 		$this->db->select('*');
	 		
			$this->db->from('logs');
			$this->db->order_by('id', 'DESC');
			$query = $this->db->get();
			return $query->result();
	 	}

	 	public function insert($data) {
	 		if ($this->db->insert("admin", $data)) {
	 			return true;
	 		}
	 	}

	 	public function delete($id) {
	 		if ($this->db->delete("admin", "admin_id = ".$id)) {
	 			return true;
	 		}
	 	}

	 	public function update($data,$old_admin_no) {
	 		$this->db->set($data);
	 		$this->db->where("admin_id", $old_admin_no);
	 		$this->db->update("admin", $data);
	 	}

	 	public function checkOld($id,$password)
	 	{
	 		$this -> db->select(' * ');
	 		$this -> db->from('admin');
	 		$this -> db->where('admin_id', $id);
	 		$this -> db->where('password', $password);
	 		$this -> db->limit(1);
	 		$query = $this->db-> get();
	 		return $query;
	 	} 


	 	public function updateLimit($data, $id, $userID = null, $newLimit = null) {
			if ($userID === null || $newLimit === null) {
				return false;
			}

			$limitRow = $this->db
				->select('dateLimit')
				->from('queuecount')
				->where('id', (int) $id)
				->where('physician_id', (int) $userID)
				->limit(1)
				->get()
				->row();

			if (!$limitRow) {
				return false;
			}

			$activeCount = $this->db
				->from('appointment')
				->where('app_date', $limitRow->dateLimit)
				->where('physician_id', (int) $userID)
				->where('app_status', 'Pending')
				->count_all_results();

			if ((int) $newLimit < $activeCount) {
				return false;
			}

			$this->db->where('id', (int) $id);
			$this->db->where('physician_id', (int) $userID);
			return $this->db->update('queuecount', $data);
	 	}



	 	public function changePassword($admin_id, $info)
	 	{
	 		$this->db->set($info);
	 		$this->db->where('admin_id', $admin_id);

	 		$this->db->update('admin', $info);

	 		return $this->db->affected_rows();
	 	}

	 	public function updateProfile($data,$admin_id)
	 	{
	 		$this->db->set($data);
	 		$this->db->where('admin_id', $admin_id);

	 		$this->db->update('admin', $data);  

	 	}

	 	public function updateProfilePic($data,$admin_id)
	 	{
	 		$this->db->set($data);
	 		$this->db->where('admin_id', $admin_id);

	 		$this->db->update('admin', $data);  
	 		return $this->db->affected_rows();

	 	}

	 	

	 	public function getTopVisited($first,$last){
		/*$first = $this->input->post('first');
		$last = $this->input->post('last');
		date_default_timezone_set("Asia/Manila");*/
		
		
		
		$this->db->select('clinic.name,appointment.app_date, COUNT(queueNum) as visits');
		$this->db->from('appointment');
		$this->db->join('clinic', 'clinic.clinic_id = appointment.clinic_id');
			//$this->db->where('app_date',$first);
		$this->db->where("app_date BETWEEN '$first' AND '$last'");
		$this->db->order_by('visits', 'DESC');
		$this->db->group_by('clinic.name');
		
		$query = $this->db->get();
		return $query->result();
	}

	public function getTopConsulted($first,$last){
		/*$first = $this->input->post('first');
		$last = $this->input->post('last');
		date_default_timezone_set("Asia/Manila");*/
		
		
		
		$this->db->select('appointment.purpose, COUNT(purpose) as num');
		$this->db->from('appointment');

			//$this->db->where('app_date',$first);
		$this->db->where("app_date BETWEEN '$first' AND '$last'");
		$this->db->order_by('num', 'DESC');
		$this->db->group_by( 'purpose');
		
		$query = $this->db->get();
		return $query->result();
	}

	public function getAverage($first){
    /*$first = $this->input->post('first');
    $last = $this->input->post('last');
    date_default_timezone_set("Asia/Manila");*/
    $first = explode('-', $first);
    $year = $first[0];
    if ( ! isset($first[1])) {
      $first[1] = null;
	  }
	  $m = $first[1];
		  $this->db->select('clinic.name, COUNT(queueNum) as visits, AVG(queueNum) as average,app_date');
		  $this->db->from('appointment');
		  $this->db->join('clinic', 'clinic.clinic_id = appointment.clinic_id');
		      //$this->db->where('app_date',$first);
		  $this->db->where("YEAR(`app_date`) = '$year' AND MONTH(`app_date`) = '$m'");
		  $this->db->order_by('AVG(queueNum)', 'DESC');
		  $this->db->group_by('clinic.name');
		  
		  $query = $this->db->get();
		  return $query->result();
	}


	public function updateStatus($data,$old_patient_id) {
		$this->db->set($data);
		$this->db->where("patient_id", $old_patient_id);
		$this->db->update("patient", $data);
	} 
	public function updateStatusPhysician($data,$old_physician_id) {
		$this->db->set($data);
		$this->db->where("physician_id", $old_physician_id);
		$this->db->update("physician", $data);
	}
	public function updateStatusSec($data,$old_secretary_id) {
		$this->db->set($data);
		$this->db->where("secretary_id", $old_secretary_id);
		$this->db->update("secretary", $data);
	}  


	


}
?>
