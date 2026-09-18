<?php 
class LoginModel extends CI_Model
{
	function __construct() {
		parent::__construct();
	}
        
	function loginAdmin($email,$password)
	{
		$this -> db->select(' * ');
		$this -> db->from('admin');
		$this -> db->where('email', $email);
		$this -> db->where('password', $password);
		$this -> db->limit(1);
		$query = $this->db-> get();
		return $query;
	}
	function loginPatient($email,$password)
	{
		$this -> db->select(' * ');
		$this -> db->from('patient');
		$this -> db->where('email', $email);
		$this -> db->where('password',$password);
		$this -> db->limit(1);
		$query = $this->db-> get();
		
		return $query;
	}

	function getQuestion($id)
	{
		$this->db->select(' *, questions.question ');
		$this->db->from('answer');
		$this->db->join('patient', 'patient.patient_id = answer.patient_id');
		$this->db->join('questions', 'questions.q_id = answer.q_id');
		//$this->db->where('answer.answer',$answer);
		$this->db->where('answer.patient_id',$id);
		

		$this -> db->limit(1);
		$query = $this->db-> get();
		
		return $query;
	}
	public function view_info ($userid) {
		$this->db->select("*");
		$this->db->where('patient_id',$userid);
		$this->db->from('patient');

		$query = $this->db->get();
		return $query->result();
	}

	function checkAnswer($answer,$id)
	{
		$this->db->select(' * ');
		$this->db->from('answer');
		$this->db->join('patient', 'patient.patient_id = answer.patient_id');
		$this->db->join('questions', 'questions.q_id = answer.q_id');
		$this->db->where('answer.answer',$answer);
		$this->db->where('answer.patient_id',$id);
		
			
		// $this -> db->limit(1);
		$query = $this->db-> get();
		
		return $query;
	}

	

	function loginSec($email,$password)
	{
		$this -> db->select(' * ');
		$this -> db->from('secretary');
		$this -> db->where('email', $email);
		$this -> db->where('password', $password);
		$this -> db->limit(1);
		$query = $this->db-> get();
		return $query;
	}

	function loginDoctor($email,$password)
	{
		$this -> db->select(' * ');
		$this -> db->from('physician');
		$this -> db->where('email', $email);
		$this -> db->where('password', $password);
		$this -> db->limit(1);
		$query = $this->db-> get();
		return $query;
	}
	
}