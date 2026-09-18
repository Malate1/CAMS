<?php
   class Clinic_Model extends CI_Model {

      function __construct() {
         parent::__construct();
      }

      public function getClinic()
      {

      $this->db->select('*,physician.fname,physician.lname');
      $this->db->from('physician_clinic');
      $this->db->join('physician', 'physician.physician_id = physician_clinic.physician_id');
      $this->db->join('clinic', 'clinic.clinic_id = physician_clinic.clinic_id');
      // $this->db->join('physician_sched', 'physician_sched.physician_id = physician_clinic.physician_id');
      // $this->db->join('schedule', 'schedule.schedule_id = physician_sched.schedule_id');
      

     
      $query = $this->db->get();
      return $query->result();
      }

      

      public function insert_clinic($data1) {
         if ($this->db->insert("clinic", $data1)) {
            return true;
         }
      }

      public function insert_pclinic($data) {
         if ($this->db->insert("physician_clinic", $data)) {
            return true;
         }
      }

      public function delete($id) {
         if ($this->db->delete("clinic", "clinic_id = ".$id)) {
            return true;
         }
      }

      public function update($data,$old_clinic_no) {
         $this->db->set($data);
         $this->db->where("clinic_id", $old_clinic_no);
         $this->db->update("clinic", $data);
      }



       



   }
?>
