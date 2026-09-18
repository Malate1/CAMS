<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_Model extends CI_Model
{
    private function statusCounts(array $where = array(), array $clinicIds = array())
    {
        $this->db->select('app_status, COUNT(*) AS total', false)
            ->from('appointment');
        foreach ($where as $column => $value) {
            $this->db->where($column, $value);
        }
        if ($clinicIds) {
            $this->db->where_in('clinic_id', $clinicIds);
        }
        $rows = $this->db->group_by('app_status')->get()->result();

        $counts = array('Pending' => 0, 'Done' => 0, 'Cancelled' => 0);
        foreach ($rows as $row) {
            $counts[$row->app_status] = (int) $row->total;
        }
        return $counts;
    }

    private function emptyDailySeries($days)
    {
        $days = max(7, min(60, (int) $days));
        $series = array();
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime('-' . $i . ' days'));
            $series[] = array('date' => $date, 'label' => date('M j', strtotime($date)), 'total' => 0);
        }
        return $series;
    }

    private function dailySeries(array $where = array(), array $clinicIds = array(), $daysBefore = 7, $daysAfter = 14)
    {
        $daysBefore = max(0, min(30, (int) $daysBefore));
        $daysAfter = max(0, min(45, (int) $daysAfter));
        $start = date('Y-m-d', strtotime('-' . $daysBefore . ' days'));
        $end = date('Y-m-d', strtotime('+' . $daysAfter . ' days'));

        $this->db->select('app_date, COUNT(*) AS total', false)
            ->from('appointment')
            ->where('app_date >=', $start)
            ->where('app_date <=', $end);
        foreach ($where as $column => $value) {
            $this->db->where($column, $value);
        }
        if ($clinicIds) {
            $this->db->where_in('clinic_id', $clinicIds);
        }
        $rows = $this->db->group_by('app_date')->order_by('app_date', 'ASC')->get()->result();

        $byDate = array();
        foreach ($rows as $row) {
            $byDate[$row->app_date] = (int) $row->total;
        }

        $series = array();
        for ($offset = -$daysBefore; $offset <= $daysAfter; $offset++) {
            $date = date('Y-m-d', strtotime(($offset >= 0 ? '+' : '') . $offset . ' days'));
            $series[] = array(
                'date' => $date,
                'label' => date('M j', strtotime($date)),
                'total' => isset($byDate[$date]) ? $byDate[$date] : 0,
                'is_today' => $offset === 0,
            );
        }
        return $series;
    }

    private function scopedCount(array $where = array(), array $clinicIds = array(), $startDate = null, $endDate = null, $status = null)
    {
        $this->db->from('appointment');
        foreach ($where as $column => $value) {
            $this->db->where($column, $value);
        }
        if ($clinicIds) {
            $this->db->where_in('clinic_id', $clinicIds);
        }
        if ($startDate) {
            $this->db->where('app_date >=', $startDate);
        }
        if ($endDate) {
            $this->db->where('app_date <=', $endDate);
        }
        if ($status !== null) {
            $this->db->where('app_status', $status);
        }
        return (int) $this->db->count_all_results();
    }

    private function completionRate(array $status)
    {
        $resolved = (int) (isset($status['Done']) ? $status['Done'] : 0)
            + (int) (isset($status['Cancelled']) ? $status['Cancelled'] : 0);
        if ($resolved <= 0) {
            return 0;
        }
        return (int) round(((int) $status['Done'] / $resolved) * 100);
    }

    private function clinicBreakdown(array $where = array(), array $clinicIds = array(), $limit = 6)
    {
        $this->db->select('clinic.name, COUNT(appointment.appointment_id) AS total', false)
            ->from('appointment')
            ->join('clinic', 'clinic.clinic_id = appointment.clinic_id', 'left');
        foreach ($where as $column => $value) {
            $this->db->where($column, $value);
        }
        if ($clinicIds) {
            $this->db->where_in('appointment.clinic_id', $clinicIds);
        }

        return $this->db
            ->group_by('appointment.clinic_id')
            ->order_by('total', 'DESC')
            ->limit((int) $limit)
            ->get()
            ->result_array();
    }

    private function physicianBreakdown(array $where = array(), array $clinicIds = array(), $limit = 6)
    {
        $this->db
            ->select("CONCAT(physician.fname, ' ', physician.lname) AS name, COUNT(appointment.appointment_id) AS total", false)
            ->from('appointment')
            ->join('physician', 'physician.physician_id = appointment.physician_id', 'left');

        foreach ($where as $column => $value) {
            $this->db->where($column, $value);
        }
        if ($clinicIds) {
            $this->db->where_in('appointment.clinic_id', $clinicIds);
        }

        return $this->db
            ->group_by('appointment.physician_id')
            ->order_by('total', 'DESC')
            ->limit((int) $limit)
            ->get()
            ->result_array();
    }

    private function emptyWeekdayBreakdown()
    {
        return array(
            array('name' => 'Sunday', 'total' => 0),
            array('name' => 'Monday', 'total' => 0),
            array('name' => 'Tuesday', 'total' => 0),
            array('name' => 'Wednesday', 'total' => 0),
            array('name' => 'Thursday', 'total' => 0),
            array('name' => 'Friday', 'total' => 0),
            array('name' => 'Saturday', 'total' => 0),
        );
    }

    private function weekdayBreakdown(array $where = array(), array $clinicIds = array())
    {
        $this->db
            ->select('DAYOFWEEK(appointment.app_date) AS weekday_num, COUNT(appointment.appointment_id) AS total', false)
            ->from('appointment');

        foreach ($where as $column => $value) {
            $this->db->where($column, $value);
        }
        if ($clinicIds) {
            $this->db->where_in('appointment.clinic_id', $clinicIds);
        }

        $rows = $this->db
            ->group_by('DAYOFWEEK(appointment.app_date)')
            ->get()
            ->result_array();

        $labels = array(
            1 => 'Sunday',
            2 => 'Monday',
            3 => 'Tuesday',
            4 => 'Wednesday',
            5 => 'Thursday',
            6 => 'Friday',
            7 => 'Saturday',
        );

        $counts = array();
        foreach ($rows as $row) {
            $counts[(int) $row['weekday_num']] = (int) $row['total'];
        }

        $result = array();
        foreach ($labels as $number => $label) {
            $result[] = array(
                'name' => $label,
                'total' => isset($counts[$number]) ? $counts[$number] : 0,
            );
        }
        return $result;
    }

    private function upcoming(array $where = array(), array $clinicIds = array(), $limit = 6)
    {
        $this->db
            ->select('appointment.appointment_id, appointment.app_date, appointment.queueNum, appointment.app_status, appointment.purpose, clinic.name AS clinic_name, physician.fname AS physician_fname, physician.lname AS physician_lname, patient.fname AS patient_fname, patient.lname AS patient_lname')
            ->from('appointment')
            ->join('clinic', 'clinic.clinic_id = appointment.clinic_id', 'left')
            ->join('physician', 'physician.physician_id = appointment.physician_id', 'left')
            ->join('patient', 'patient.patient_id = appointment.patient_id', 'left')
            ->where('appointment.app_date >=', date('Y-m-d'))
            ->where('appointment.app_status !=', 'Cancelled');
        foreach ($where as $column => $value) {
            $this->db->where($column, $value);
        }
        if ($clinicIds) {
            $this->db->where_in('appointment.clinic_id', $clinicIds);
        }

        return $this->db
            ->order_by('appointment.app_date', 'ASC')
            ->order_by('appointment.queueNum', 'ASC')
            ->limit((int) $limit)
            ->get()
            ->result_array();
    }

    public function adminDashboard()
    {
        $status = $this->statusCounts();
        return array(
            'summary' => array(
                'patients' => (int) $this->db->count_all('patient'),
                'physicians' => (int) $this->db->count_all('physician'),
                'secretaries' => (int) $this->db->count_all('secretary'),
                'clinics' => (int) $this->db->count_all('clinic'),
                'today' => $this->scopedCount(array(), array(), date('Y-m-d'), date('Y-m-d')),
                'next7' => $this->scopedCount(array(), array(), date('Y-m-d'), date('Y-m-d', strtotime('+6 days'))),
                'completion_rate' => $this->completionRate($status),
            ),
            'status' => $status,
            'trend' => $this->dailySeries(array(), array(), 7, 14),
            'clinics' => $this->clinicBreakdown(),
            'secondary' => array(
                'title' => 'Top physicians',
                'description' => 'Appointments handled by physician.',
                'data' => $this->physicianBreakdown(),
            ),
            'upcoming' => $this->upcoming(array(), array(), 6),
        );
    }

    public function patientDashboard($patientId)
    {
        $where = array('appointment.patient_id' => (int) $patientId);
        $status = $this->statusCounts($where);
        return array(
            'summary' => array(
                'upcoming' => (int) $this->db
                    ->where('patient_id', (int) $patientId)
                    ->where('app_date >=', date('Y-m-d'))
                    ->where('app_status !=', 'Cancelled')
                    ->from('appointment')->count_all_results(),
                'done' => $status['Done'],
                'cancelled' => $status['Cancelled'],
                'total' => array_sum($status),
                'next7' => $this->scopedCount($where, array(), date('Y-m-d'), date('Y-m-d', strtotime('+6 days'))),
                'completion_rate' => $this->completionRate($status),
            ),
            'status' => $status,
            'trend' => $this->dailySeries($where, array(), 21, 14),
            'clinics' => $this->clinicBreakdown($where),
            'secondary' => array(
                'title' => 'Physicians visited',
                'description' => 'Your appointment history grouped by physician.',
                'data' => $this->physicianBreakdown($where),
            ),
            'upcoming' => $this->upcoming($where, array(), 6),
        );
    }

    public function physicianDashboard($physicianId)
    {
        $where = array('appointment.physician_id' => (int) $physicianId);
        $status = $this->statusCounts($where);
        return array(
            'summary' => array(
                'today' => (int) $this->db
                    ->where('physician_id', (int) $physicianId)
                    ->where('app_date', date('Y-m-d'))
                    ->where('app_status', 'Pending')
                    ->from('appointment')->count_all_results(),
                'upcoming' => (int) $this->db
                    ->where('physician_id', (int) $physicianId)
                    ->where('app_date >=', date('Y-m-d'))
                    ->where('app_status', 'Pending')
                    ->from('appointment')->count_all_results(),
                'done' => $status['Done'],
                'cancelled' => $status['Cancelled'],
                'next7' => $this->scopedCount($where, array(), date('Y-m-d'), date('Y-m-d', strtotime('+6 days')), 'Pending'),
                'completion_rate' => $this->completionRate($status),
            ),
            'status' => $status,
            'trend' => $this->dailySeries($where, array(), 7, 14),
            'clinics' => $this->clinicBreakdown($where),
            'secondary' => array(
                'title' => 'Demand by weekday',
                'description' => 'Historical appointment volume by day of the week.',
                'data' => $this->weekdayBreakdown($where),
            ),
            'upcoming' => $this->upcoming($where, array(), 6),
        );
    }

    public function secretaryDashboard($secretaryId, $physicianId)
    {
        $clinicRows = $this->db
            ->select('clinic_id')
            ->from('physician_clinic')
            ->where('secretary_id', (int) $secretaryId)
            ->where('physician_id', (int) $physicianId)
            ->get()
            ->result();

        $clinicIds = array();
        foreach ($clinicRows as $row) {
            $clinicIds[] = (int) $row->clinic_id;
        }

        $where = array('appointment.physician_id' => (int) $physicianId);

        if (!$clinicIds) {
            return array(
                'summary' => array('today' => 0, 'upcoming' => 0, 'done' => 0, 'cancelled' => 0, 'next7' => 0, 'completion_rate' => 0),
                'status' => array('Pending' => 0, 'Done' => 0, 'Cancelled' => 0),
                'trend' => $this->dailySeries($where, array(), 7, 14),
                'clinics' => array(),
                'secondary' => array(
                    'title' => 'Demand by weekday',
                    'description' => 'Historical appointment volume by day of the week.',
                    'data' => $this->emptyWeekdayBreakdown(),
                ),
                'upcoming' => array(),
                'assigned_clinics' => 0,
            );
        }

        $status = $this->statusCounts($where, $clinicIds);

        $this->db
            ->where('physician_id', (int) $physicianId)
            ->where('app_date', date('Y-m-d'))
            ->where('app_status', 'Pending');
        if ($clinicIds) {
            $this->db->where_in('clinic_id', $clinicIds);
        }
        $todayCount = (int) $this->db->from('appointment')->count_all_results();

        $this->db
            ->where('physician_id', (int) $physicianId)
            ->where('app_date >=', date('Y-m-d'))
            ->where('app_status', 'Pending');
        if ($clinicIds) {
            $this->db->where_in('clinic_id', $clinicIds);
        }
        $upcomingCount = (int) $this->db->from('appointment')->count_all_results();

        return array(
            'summary' => array(
                'today' => $todayCount,
                'upcoming' => $upcomingCount,
                'done' => $status['Done'],
                'cancelled' => $status['Cancelled'],
                'next7' => $this->scopedCount($where, $clinicIds, date('Y-m-d'), date('Y-m-d', strtotime('+6 days')), 'Pending'),
                'completion_rate' => $this->completionRate($status),
            ),
            'status' => $status,
            'trend' => $this->dailySeries($where, $clinicIds, 7, 14),
            'clinics' => $this->clinicBreakdown($where, $clinicIds),
            'secondary' => array(
                'title' => 'Demand by weekday',
                'description' => 'Historical appointment volume by day of the week.',
                'data' => $this->weekdayBreakdown($where, $clinicIds),
            ),
            'upcoming' => $this->upcoming($where, $clinicIds, 6),
            'assigned_clinics' => count($clinicIds),
        );
    }
}
