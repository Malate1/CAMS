<?php
   class Secretary_Model extends CI_Model {

    function __construct() {
       parent::__construct();
   }
   public function view_info ($userid) {
    $this->db->select("*");
    $this->db->where('secretary_id',$userid);
    $this->db->from('secretary');
    
    $query = $this->db->get();
    return $query->result();
}

public function getLogs() {

  $userid = $this->session->userdata['userid'];
  $this->db->select('*');
  $this->db->from('logs');
  $this->db->join('secretary', 'secretary.secretary_id = logs.userid');
  $this->db->where("logs.userid = '$userid' AND logs.usertype = 'Secretary'");
  $this->db->order_by('id', 'DESC');
  $query = $this->db->get();
  return $query->result();
}
public function CheckExists($email)
{
    $this ->db->select(' * ');
    $this ->db->from('secretary');
    $this ->db->where('email', $email);
    
    $this ->db->limit(1);
    $query = $this->db-> get();
    return $query;
}

public function checkOld($id,$password)
{
    $this -> db->select(' * ');
    $this -> db->from('secretary');
    $this -> db->where('secretary_id', $id);
    $this -> db->where('password', $password);
    $this -> db->limit(1);
    $query = $this->db-> get();
    return $query;
} 

public function Count(){
    date_default_timezone_set('Asia/Manila');
    
      //$userid = $this->session->userdata['userid'];
    $date = date('Y-m-d');
    
    $userid = $this->session->userdata['physician_id'];
    
    $this->db->select('COUNT(queueNum) as visits, appointment.app_date');
    $this->db->from('appointment');
    $this->db->join('physician', 'physician.physician_id = appointment.physician_id');
    $this->db->join('patient', 'patient.patient_id = appointment.patient_id');
    $this->db->where("appointment.physician_id = '$userid' AND appointment.app_date = '$date'AND appointment.app_status = 'Pending' ");
      //$this->db->group_by('clinic.name');
      //$this->db->order_by('visits', 'DESC');
    $query = $this->db->get();
    return $query->result();
}

public function CountDone(){
    date_default_timezone_set('Asia/Manila');
    
    $userid = $this->session->userdata['physician_id'];
    $date = date('Y-m-d');
    
      //$userid = $this->session->userdata['userid'];
    
    $this->db->select('*,COUNT(queueNum) as visits, appointment.app_date');
    $this->db->from('appointment');
    $this->db->join('physician', 'physician.physician_id = appointment.physician_id');
    $this->db->join('patient', 'patient.patient_id = appointment.patient_id');
    $this->db->where("appointment.physician_id = '$userid'  AND appointment.app_status = 'Done'");
      //$this->db->group_by('clinic.name');
      //$this->db->order_by('visits', 'DESC');
    $query = $this->db->get();
    return $query->result();
}

public function CountCancel(){
    date_default_timezone_set('Asia/Manila');
    
    $userid = $this->session->userdata['physician_id'];
    $date = date('Y-m-d');
    
      //$userid = $this->session->userdata['userid'];
    
    $this->db->select('*,COUNT(queueNum) as visits, appointment.app_date');
    $this->db->from('appointment');
    $this->db->join('physician', 'physician.physician_id = appointment.physician_id');
    $this->db->join('patient', 'patient.patient_id = appointment.patient_id');
    $this->db->where("appointment.physician_id = '$userid'  AND appointment.app_status = 'Cancelled'");
      //$this->db->group_by('clinic.name');
      //$this->db->order_by('visits', 'DESC');
    $query = $this->db->get();
    return $query->result();
}

public function insert_pid($data2,$secretary_id) {
 $this->db->set("physician_id",$data2);
 $this->db->where("secretary_id", $secretary_id);
 $this->db->update("secretary", $data2);
}

public function getSec()
{

  $this->db->select('physician.lname,physician.lname');
  $this->db->from('secretary');
  $this->db->join('physician', 'physician.physician_id = secretary.physician_id');
  
  $query = $this->db->get();
  return $query->result();
}

public function getSecretary()
{

  $this->db->select('*');
  $this->db->from('secretary');
      //$this->db->join('physician', 'physician.physician_id = secretary.physician_id');
  
  $query = $this->db->get();
  return $query->result();
}

public function insert($data) {
   if ($this->db->insert("secretary", $data)) {
      return true;
  }
}

public function delete($id) {
   if ($this->db->delete("secretary", "secretary_id = ".$id)) {
      return true;
  }
}

public function update($data,$old_secretary_id) {
   $this->db->set($data);
   $this->db->where("secretary_id", $old_secretary_id);
   $this->db->update("secretary", $data);
}
public function insert_limit($data) {
   if ($this->db->insert("queuecount", $data)) {
      return true;
  }
}

     /* public function insert_limit($data, $userID, $newLimit) {

          //get the date
          $this->db->select('dateLimit');
          $this->db->from('queuecount');
          $this->db->where('physician_id', $userID);
          $date = $this->db->get();
          $dateResult = $date->row()->dateLimit;
          //get count
          $this->db->select('*');
          $this->db->from('appointment');
          $this->db->where("app_date", $dateResult);
          $this->db->where('physician_id', $userID);
          $query = $this->db->get();
          $rowcount = $query->num_rows();

         
         $this->db->insert("queuecount", $data);


         //if no. off appointments is greater than the new limit
         if($newLimit < $rowcount)
         {

             $this->db->limit($rowcount-$newLimit);
             $this->db->set("app_status", "Cancelled");
             $this->db->where("app_date", $dateResult);
             $this->db->where('physician_id', $userID);
             $this->db->order_by("appointment_id", "desc");
             $this->db->update("appointment");
         }

     }*/

     public function getClinic()
     {
        $userid = $this->session->userdata['physician_id'];
        $this->db->select('*');
        $this->db->from('physician_clinic');
        $this->db->join('physician', 'physician.physician_id = physician_clinic.physician_id');
        $this->db->join('clinic', 'clinic.clinic_id = physician_clinic.clinic_id');
        $this->db->where("physician_clinic.physician_id", $userid);
        $this->db->where("physician_clinic.secretary_id", (int) $this->session->userdata['userid']);
        $query = $this->db->get();
        return $query->result();
    }

    
    public function getSchedule()
    {
        $userid = $this->session->userdata['physician_id'];
        $this->db->select('physician_clinic_schedule.id AS link_id, physician_clinic.physician_id, clinic.clinic_id, clinic.name AS clinic_name, schedule.*');
        $this->db->from('physician_clinic');
        $this->db->join('clinic', 'clinic.clinic_id = physician_clinic.clinic_id');
        $this->db->join('physician_clinic_schedule', 'physician_clinic_schedule.physician_clinic_id = physician_clinic.id');
        $this->db->join('schedule', 'schedule.schedule_id = physician_clinic_schedule.schedule_id');
        $this->db->where("physician_clinic.physician_id", $userid);
        $this->db->where("physician_clinic.secretary_id", (int) $this->session->userdata['userid']);
        $query = $this->db->get();
        return $query->result();
    }

    public function getSpecialization()
    {
        $userid = $this->session->userdata['physician_id'];
        $this->db->select('physician_special.*,specialization.special_name');
        $this->db->from('physician_special');
        $this->db->join('physician', 'physician.physician_id = physician_special.physician_id');
        $this->db->join('specialization', 'specialization.special_id = physician_special.special_id');
        $this->db->where("physician_special.physician_id", $userid);
        $query = $this->db->get();
        return $query->result();
    }

    public function getAppointmentCalendar() {

        date_default_timezone_set('Asia/Manila');


        $userid = $this->session->userdata['physician_id'];
        $date = date('Y-m-d');
        $this->db->select("COUNT(app_date) as appointD");
        $this->db->select("app_date as count_date");
        $this->db->from('appointment');
        $this->db->where("appointment.physician_id", $userid);
        $this->db->where("app_status !=", "Done");
        $this->db->where("app_status !=", "Cancelled");
        $this->db->where('app_date >= CURDATE()');
        $this->db->group_by('app_date');
          //$this->db->from('appointment');
  /*    $this->db->join('physician', 'physician.physician_id = appointment.physician_id');
  $this->db->join('patient', 'patient.patient_id = appointment.patient_id');*/
     // $this->db->where("appointment.physician_id", $userid);
     // $this->db->where("app_status !=", "Done");
      //$this->db->order_by('appointment.app_date', 'DESC');

  $query = $this->db->get();
  return $query->result();
}

public function getAppointment() {

  date_default_timezone_set('Asia/Manila');
  
  $userid = $this->session->userdata['physician_id'];
  $date = date('Y-m-d');
  $this->db->select('*, patient.lname, patient.fname');
  $this->db->from('appointment');
  $this->db->join('physician', 'physician.physician_id = appointment.physician_id');
  $this->db->join('patient', 'patient.patient_id = appointment.patient_id');
  $this->db->where("appointment.physician_id = '$userid' ");
  $this->db->order_by('appointment.app_date', 'DESC');

  $query = $this->db->get();
  return $query->result();
}

public function getAppointmentToday() {

  date_default_timezone_set('Asia/Manila');
  
  $userid = $this->session->userdata['physician_id'];
  $date = date('Y-m-d');
  $this->db->select('*, patient.lname, patient.fname');
  $this->db->from('appointment');
  $this->db->join('physician', 'physician.physician_id = appointment.physician_id');
  $this->db->join('patient', 'patient.patient_id = appointment.patient_id');
  $this->db->where("appointment.physician_id = '$userid' AND appointment.app_date = '$date'");
  $this->db->order_by('appointment.app_date', 'DESC');

  $query = $this->db->get();
  return $query->result();
}

public function getAppointmentDone() {

  date_default_timezone_set('Asia/Manila');
  
  $userid = $this->session->userdata['physician_id'];
  $date = date('Y-m-d');
  $this->db->select('*, patient.lname, patient.fname');
  $this->db->from('appointment');
  $this->db->join('physician', 'physician.physician_id = appointment.physician_id');
  $this->db->join('patient', 'patient.patient_id = appointment.patient_id');
  $this->db->where("appointment.physician_id = '$userid' AND appointment.app_status = 'Done'");
  $this->db->order_by('appointment.app_date', 'DESC');


  $query = $this->db->get();
  return $query->result();
}

public function getAppointmentCancelled() {

  date_default_timezone_set('Asia/Manila');
  
  $userid = $this->session->userdata['physician_id'];
  $date = date('Y-m-d');
  $this->db->select('*, patient.lname, patient.fname');
  $this->db->from('appointment');
  $this->db->join('physician', 'physician.physician_id = appointment.physician_id');
  $this->db->join('patient', 'patient.patient_id = appointment.patient_id');
  $this->db->where("appointment.physician_id = '$userid' AND appointment.app_status = 'Cancelled'");
  $this->db->order_by('appointment.app_date', 'DESC');

  $query = $this->db->get();
  return $query->result();
}
    //PARA GIKAN SA CALENDAR BUTTON
public function getAppointmentTwo($selectedDate) {

   date_default_timezone_set('Asia/Manila');

   $userid = $this->session->userdata['physician_id'];
   $date = $selectedDate;
   $this->db->select('*, patient.lname, patient.fname');
   $this->db->from('appointment');
   $this->db->join('physician', 'physician.physician_id = appointment.physician_id');
   $this->db->join('patient', 'patient.patient_id = appointment.patient_id');
   $this->db->where("appointment.physician_id = '$userid' AND appointment.app_date = '$date' ");
   $this->db->order_by('appointment.app_date', 'DESC');

   $query = $this->db->get();
   return $query->result();
}


public function getLimit()
{
  $userid = $this->session->userdata['physician_id'];
  $this->db->select('MIN(id) AS id, dateLimit, physician_id, MAX(queueLimit) AS queueLimit');
  $this->db->from('queuecount');
  $this->db->where('physician_id', $userid);
  $this->db->group_by(array('dateLimit', 'physician_id'));
  $this->db->order_by('dateLimit', 'DESC');
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

  $this->db->group_by('clinic.name');
  $this->db->order_by('average', 'DESC');
  $query = $this->db->get();
  return $query->result();
}

public function getDone($first){
    /*$first = $this->input->post('first');
    $last = $this->input->post('last');
    date_default_timezone_set("Asia/Manila");*/
    $first = explode('-', $first);
    $year = $first[0];
    if ( ! isset($first[1])) {
      $first[1] = null;
  }
  $m = $first[1];
  
  
  $this->db->select('*, patient.lname, patient.fname');
  $this->db->from('appointment');

  $this->db->join('patient', 'patient.patient_id = appointment.patient_id');
  $this->db->where("app_status = 'Done' AND YEAR(`app_date`) = '$year' AND MONTH(`app_date`) = '$m'");
      //$this->db->group_by('clinic.name');
  $query = $this->db->get();
  return $query->result();
}

public function getCancelled($first){
    /*$first = $this->input->post('first');
    $last = $this->input->post('last');
    date_default_timezone_set("Asia/Manila");*/
    $first = explode('-', $first);
    $year = $first[0];
    if ( ! isset($first[1])) {
      $first[1] = null;
  }
  $m = $first[1];
  
  
  $this->db->select('*, patient.lname, patient.fname');
  $this->db->from('appointment');

  $this->db->join('patient', 'patient.patient_id = appointment.patient_id');
  $this->db->where("app_status = 'Cancelled' AND YEAR(`app_date`) = '$year' AND MONTH(`app_date`) = '$m'");
      //$this->db->group_by('clinic.name');
  $query = $this->db->get();
  return $query->result();
}



public function changePassword($secretary_id, $info)
{
    $this->db->set($info);
    $this->db->where('secretary_id', $secretary_id);
    
    $this->db->update('secretary', $info);
    
    return $this->db->affected_rows();
}

public function updateProfile($data,$secretary_id)
{
    $this->db->set($data);
    $this->db->where('secretary_id', $secretary_id);
    
    $this->db->update('secretary', $data);
    

    return $this->db->affected_rows();
}

public function updateStatus($data,$old_app_id) {
 $this->db->set($data);
 $this->db->where("appointment_id", $old_app_id);
 $this->db->update("appointment", $data);
} 


}
?>
