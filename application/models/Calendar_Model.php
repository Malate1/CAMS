<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Calendar_Model extends CI_Model
{
    public function events($physicianId, $startDate, $endDate, array $allowedClinicIds = null, $clinicFilter = 0, $status = '')
    {
        $physicianId = (int) $physicianId;
        $clinicFilter = (int) $clinicFilter;
        $status = ucfirst(strtolower(trim((string) $status)));
        $validStatuses = array('Pending', 'Done', 'Cancelled');

        $this->db
            ->select('appointment.appointment_id, appointment.app_date, appointment.queueNum, appointment.purpose, appointment.app_status, appointment.clinic_id')
            ->select('patient.patient_id, patient.fname AS patient_fname, patient.mname AS patient_mname, patient.lname AS patient_lname')
            ->select('clinic.name AS clinic_name, clinic.location AS clinic_location')
            ->select('physician.fname AS physician_fname, physician.lname AS physician_lname')
            ->from('appointment')
            ->join('patient', 'patient.patient_id = appointment.patient_id', 'left')
            ->join('clinic', 'clinic.clinic_id = appointment.clinic_id', 'left')
            ->join('physician', 'physician.physician_id = appointment.physician_id', 'left')
            ->where('appointment.physician_id', $physicianId)
            ->where('appointment.app_date >=', $startDate)
            ->where('appointment.app_date <', $endDate);

        if ($allowedClinicIds !== null) {
            $allowedClinicIds = array_values(array_unique(array_filter(array_map('intval', $allowedClinicIds))));
            if (!$allowedClinicIds) {
                $this->db->where('1 = 0', null, false);
            } else {
                $this->db->where_in('appointment.clinic_id', $allowedClinicIds);
            }
        }

        if ($clinicFilter > 0) {
            if ($allowedClinicIds !== null && !in_array($clinicFilter, $allowedClinicIds, true)) {
                $this->db->where('1 = 0', null, false);
            } else {
                $this->db->where('appointment.clinic_id', $clinicFilter);
            }
        }

        if (in_array($status, $validStatuses, true)) {
            $this->db->where('appointment.app_status', $status);
        }

        $rows = $this->db
            ->order_by('appointment.app_date', 'ASC')
            ->order_by('appointment.queueNum', 'ASC')
            ->order_by('appointment.appointment_id', 'ASC')
            ->get()
            ->result();

        $days = array();

        foreach ($rows as $row) {
            $date = (string) $row->app_date;
            if (!isset($days[$date])) {
                $days[$date] = array(
                    'date' => $date,
                    'count' => 0,
                    'pending' => 0,
                    'done' => 0,
                    'cancelled' => 0,
                    'appointments' => array(),
                );
            }

            $days[$date]['count']++;
            $statusKey = strtolower((string) $row->app_status);
            if (isset($days[$date][$statusKey])) {
                $days[$date][$statusKey]++;
            }

            $patientName = trim(implode(' ', array_filter(array(
                $row->patient_fname,
                $row->patient_mname,
                $row->patient_lname,
            ))));
            $physicianName = trim((string) $row->physician_fname . ' ' . (string) $row->physician_lname);

            $days[$date]['appointments'][] = array(
                'id' => (int) $row->appointment_id,
                'date' => $date,
                'queue' => (int) $row->queueNum,
                'purpose' => (string) $row->purpose,
                'status' => (string) $row->app_status,
                'patient_id' => (int) $row->patient_id,
                'patient' => $patientName,
                'physician' => $physicianName,
                'clinic_id' => (int) $row->clinic_id,
                'clinic' => (string) $row->clinic_name,
                'location' => (string) $row->clinic_location,
            );
        }

        $events = array();
        foreach ($days as $day) {
            $nonZeroStatuses = 0;
            $dayTone = 'mixed';
            foreach (array('pending', 'done', 'cancelled') as $statusKey) {
                if ($day[$statusKey] > 0) {
                    $nonZeroStatuses++;
                    $dayTone = $statusKey;
                }
            }
            if ($nonZeroStatuses !== 1) {
                $dayTone = 'mixed';
            }

            $events[] = array(
                'id' => 'cams-day-' . $day['date'],
                'title' => $day['count'] . ' appointment' . ($day['count'] === 1 ? '' : 's'),
                'start' => $day['date'],
                'allDay' => true,
                'count' => $day['count'],
                'pending' => $day['pending'],
                'done' => $day['done'],
                'cancelled' => $day['cancelled'],
                'appointments' => $day['appointments'],
                'className' => array('cams-calendar-event', 'cams-calendar-event-' . $dayTone),
            );
        }

        return $events;
    }
}
