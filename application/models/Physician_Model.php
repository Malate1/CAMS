<?php
   class Physician_Model extends CI_Model {

    function __construct() {
       parent::__construct();
   }
   public function view_info ($userid) {
    $this->db->select("*");
    $this->db->where('physician_id',$userid);
    $this->db->from('physician');

    $query = $this->db->get();
    return $query->result();
}
public function getPhysician()
{
    $query = $this->db->get('physician');
    return $query->result();
}
public function CheckExists($email)
{
    $this ->db->select(' * ');
    $this ->db->from('physician');
    $this ->db->where('email', $email);
    
    $this ->db->limit(1);
    $query = $this->db-> get();
    return $query;
}
public function CheckExistsLimit($date, $physicianId = null)
{
    $this->db->select('*');
    $this->db->from('queuecount');
    $this->db->where('dateLimit', $date);
    if ($physicianId !== null) {
        $this->db->where('physician_id', (int) $physicianId);
    }
    $this->db->limit(1);
    return $this->db->get();
}

public function insert($data) {
 if ($this->db->insert("physician", $data)) {
    return true;
}
}

public function insert_schedule($data3) {
   if ($this->db->insert("schedule", $data3)) {
      return true;
  }
}

public function insert_pschedule($data4) {
   if ($this->db->insert("physician_sched", $data4)) {
      return true;
  }
}

public function insert_special($data1) {
   if ($this->db->insert("specialization", $data1)) {
      return true;
  }
}

public function insert_pspecial($data) {
   if ($this->db->insert("physician_special", $data)) {
      return true;
  }
}


public function update($data,$old_physician_id) {
   $this->db->set($data);
   $this->db->where("physician_id", $old_physician_id);
   $this->db->update("physician", $data);
}

public function updateSched($data,$old_schedule_id) {
   $this->db->set($data);
   $this->db->where("schedule_id", $old_schedule_id);
   $this->db->update("schedule", $data);
}

public function updateSpecial($data,$old_special_id) {
   $this->db->set($data);
   $this->db->where("special_id", $old_special_id);
   $this->db->update("specialization", $data);
}

public function getClinic()
{
  $userid = $this->session->userdata['userid'];
  $this->db->select('*');
  $this->db->from('physician_clinic');
  $this->db->join('physician', 'physician.physician_id = physician_clinic.physician_id');
  $this->db->join('clinic', 'clinic.clinic_id = physician_clinic.clinic_id');
      // $this->db->join('physician_sched', 'physician_sched.physician_id = physician_clinic.physician_id');
      // $this->db->join('schedule', 'schedule.schedule_id = physician_sched.schedule_id');
  $this->db->where("physician_clinic.physician_id", $userid);
  $query = $this->db->get();
  return $query->result();
}


public function getSchedule()
{
  $userid = $this->session->userdata['userid'];
  $this->db->select('physician_clinic_schedule.id AS link_id, physician_clinic.physician_id, clinic.clinic_id, clinic.name AS clinic_name, schedule.*');
  $this->db->from('physician_clinic');
  $this->db->join('clinic', 'clinic.clinic_id = physician_clinic.clinic_id');
  $this->db->join('physician_clinic_schedule', 'physician_clinic_schedule.physician_clinic_id = physician_clinic.id');
  $this->db->join('schedule', 'schedule.schedule_id = physician_clinic_schedule.schedule_id');
  $this->db->where("physician_clinic.physician_id", $userid);
  $query = $this->db->get();
  return $query->result();
}

public function getSpecialization()
{
  $userid = $this->session->userdata['userid'];
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


  $userid = $this->session->userdata['userid'];
  $date = date('Y-m-d');
  $this->db->select("COUNT(app_date) as appointD");
  $this->db->select("app_date as count_date");
  $this->db->from('appointment');
  $this->db->where("appointment.physician_id", $userid);
  $this->db->where("app_status !=", "Done");
  $this->db->where("app_status !=", "Cancelled");
  $this->db->where('app_date >= CURDATE()' );
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

public function getLogs() {



  $userid = $this->session->userdata['userid'];
  
  $this->db->select('*');
  $this->db->from('logs');
  $this->db->join('physician', 'physician.physician_id = logs.userid');
  $this->db->where("logs.userid = '$userid' AND logs.usertype = 'Physician'");
  $this->db->order_by('id', 'DESC');
  $query = $this->db->get();
  return $query->result();
}

public function getAppointment() {

  date_default_timezone_set('Asia/Manila');

  $userid = $this->session->userdata['userid'];
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

  $userid = $this->session->userdata['userid'];
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

  $userid = $this->session->userdata['userid'];
  $date = date('Y-m-d');
  $this->db->select('*, patient.lname, patient.fname');
  $this->db->from('appointment');
  $this->db->join('physician', 'physician.physician_id = appointment.physician_id');
  $this->db->join('patient', 'patient.patient_id = appointment.patient_id');
  $this->db->where("appointment.physician_id = '$userid' AND appointment.app_status = 'Done'");

  //$this->db->order_by('appointment.app_date', 'DESC');

  $query = $this->db->get();
  return $query->result();
}

public function getAppointmentCancelled() {

  date_default_timezone_set('Asia/Manila');

  $userid = $this->session->userdata['userid'];
  $date = date('Y-m-d');
  $this->db->select('*, patient.lname, patient.fname');
  $this->db->from('appointment');
  $this->db->join('physician', 'physician.physician_id = appointment.physician_id');
  $this->db->join('patient', 'patient.patient_id = appointment.patient_id');
  $this->db->where("appointment.physician_id = '$userid' AND appointment.app_status = 'Cancelled'");
  
  //$this->db->order_by('appointment.app_date', 'DESC');

  $query = $this->db->get();
  return $query->result();
}


    //PARA GIKAN SA CALENDAR BUTTON
public function getAppointmentTwo($selectedDate) {

   date_default_timezone_set('Asia/Manila');

   $userid = $this->session->userdata['userid'];
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
  $userid = $this->session->userdata['userid'];
  $this->db->select('MIN(id) AS id, dateLimit, physician_id, MAX(queueLimit) AS queueLimit');
  $this->db->from('queuecount');
  $this->db->where('physician_id', $userid);
  $this->db->group_by(array('dateLimit', 'physician_id'));
  $this->db->order_by('dateLimit', 'DESC');
  $query = $this->db->get();
  return $query->result();
}


public function changePassword($physician_id, $info)
{
  $this->db->set($info);
  $this->db->where('physician_id', $physician_id);

  $this->db->update('physician', $info);

  return $this->db->affected_rows();
}

public function checkOld($id,$password)
{
  $this -> db->select(' * ');
  $this -> db->from('physician');
  $this -> db->where('physician_id', $id);
  $this -> db->where('password', $password);
  $this -> db->limit(1);
  $query = $this->db-> get();
  return $query;
} 


public function updateProfile($data,$physician_id)
{
  $this->db->set($data);
  $this->db->where('physician_id', $physician_id);

  $this->db->update('physician', $data);


} 

public function updateProfilePic($data,$physician_id)
{
  $this->db->set($data);
  $this->db->where('physician_id', $physician_id);

  $this->db->update('physician', $data);
  return $this->db->affected_rows();
} 

public function updateStatus($data,$old_app_id) {
   $this->db->set($data);
   $this->db->where("appointment_id", $old_app_id);
   $this->db->update("appointment", $data);
} 
    // count appointment ana nga date
public function Count(){
  date_default_timezone_set('Asia/Manila');

  $userid = $this->session->userdata['userid'];
  $date = date('Y-m-d');

  $userid = $this->session->userdata['userid'];

  $this->db->select('COUNT(queueNum) as visits, appointment.app_date');
  $this->db->from('appointment');
  $this->db->join('physician', 'physician.physician_id = appointment.physician_id');
  $this->db->join('patient', 'patient.patient_id = appointment.patient_id');
  $this->db->where("appointment.physician_id = '$userid' AND appointment.app_date = '$date' AND appointment.app_status = 'Pending' ");
      //$this->db->group_by('clinic.name');
      //$this->db->order_by('visits', 'DESC');
  $query = $this->db->get();
  return $query->result();
}

public function CountDone(){
  date_default_timezone_set('Asia/Manila');

  $userid = $this->session->userdata['userid'];
  $date = date('Y-m-d');
  $this->db->select('*,COUNT(queueNum) as visits, patient.lname, patient.fname');
  $this->db->from('appointment');
  $this->db->join('physician', 'physician.physician_id = appointment.physician_id');
  $this->db->join('patient', 'patient.patient_id = appointment.patient_id');
  $this->db->where("appointment.physician_id = '$userid'  AND appointment.app_status = 'Done'");

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
  $this->db->where("appointment.physician_id = '$userid'  AND appointment.app_status = 'Cancelled'");
      //$this->db->group_by('clinic.name');
      //$this->db->order_by('visits', 'DESC');
  $query = $this->db->get();
  return $query->result();
}


}
?>
