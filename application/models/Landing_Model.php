<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Landing_Model extends CI_Model
{
    public function physicianClinicSchedules()
    {
        return $this->db
            ->distinct()
            ->select('clinic.clinic_id, clinic.name AS clinic_name, clinic.location AS clinic_location, clinic.contact AS clinic_contact')
            ->select('physician.physician_id, physician.fname, physician.mname, physician.lname, physician.image')
            ->select('schedule.schedule_id, schedule.day, schedule.time_in, schedule.time_out')
            ->from('physician_clinic')
            ->join('clinic', 'clinic.clinic_id = physician_clinic.clinic_id')
            ->join('physician', 'physician.physician_id = physician_clinic.physician_id')
            ->join('physician_clinic_schedule', 'physician_clinic_schedule.physician_clinic_id = physician_clinic.id')
            ->join('schedule', 'schedule.schedule_id = physician_clinic_schedule.schedule_id')
            ->where('physician.status', 'Active')
            ->order_by('clinic.name', 'ASC')
            ->order_by('physician.lname', 'ASC')
            ->order_by('physician.fname', 'ASC')
            ->order_by('schedule.day', 'ASC')
            ->order_by('schedule.time_in', 'ASC')
            ->get()
            ->result();
    }
}
