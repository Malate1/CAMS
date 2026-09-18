<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_Model extends CI_Model
{
    public function normalizeMonth($month)
    {
        $month = trim((string) $month);
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            return date('Y-m');
        }

        list($year, $number) = array_map('intval', explode('-', $month));
        if ($year < 2000 || $year > 2100 || $number < 1 || $number > 12) {
            return date('Y-m');
        }

        return sprintf('%04d-%02d', $year, $number);
    }

    public function secretaryClinicIds($secretaryId, $physicianId)
    {
        $rows = $this->db
            ->select('clinic_id')
            ->from('physician_clinic')
            ->where('secretary_id', (int) $secretaryId)
            ->where('physician_id', (int) $physicianId)
            ->order_by('clinic_id', 'ASC')
            ->get()
            ->result();

        $ids = array();
        foreach ($rows as $row) {
            $ids[] = (int) $row->clinic_id;
        }
        return array_values(array_unique($ids));
    }

    public function monthlyReport($physicianId, $month, $type = 'average', $clinicIds = null)
    {
        $physicianId = (int) $physicianId;
        $month = $this->normalizeMonth($month);
        $type = in_array($type, array('average', 'done', 'cancelled'), true) ? $type : 'average';

        list($startDate, $endDate, $daysInMonth) = $this->monthBounds($month);

        $summary = $this->monthlySummary($physicianId, $startDate, $endDate, $clinicIds);
        $daily = $this->dailySeries($physicianId, $startDate, $endDate, $clinicIds);

        $data = array(
            'month' => $month,
            'month_label' => date('F Y', strtotime($month . '-01')),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'days_in_month' => $daysInMonth,
            'type' => $type,
            'summary' => $summary,
            'daily' => $daily,
            'clinic_average' => array(),
            'appointments' => array(),
        );

        if ($type === 'average') {
            $data['clinic_average'] = $this->clinicAverage(
                $physicianId,
                $startDate,
                $endDate,
                $daysInMonth,
                $clinicIds
            );
        } else {
            $status = $type === 'done' ? 'Done' : 'Cancelled';
            $data['appointments'] = $this->appointmentsByStatus(
                $physicianId,
                $startDate,
                $endDate,
                $status,
                $clinicIds
            );
        }

        return $data;
    }

    private function monthBounds($month)
    {
        $start = $month . '-01';
        $days = (int) date('t', strtotime($start));
        $end = date('Y-m-t', strtotime($start));

        return array($start, $end, $days);
    }

    private function applyScope($physicianId, $startDate, $endDate, $clinicIds = null)
    {
        $this->db
            ->where('appointment.physician_id', (int) $physicianId)
            ->where('appointment.app_date >=', $startDate)
            ->where('appointment.app_date <=', $endDate);

        if ($clinicIds !== null) {
            $clinicIds = array_values(array_filter(array_map('intval', (array) $clinicIds)));
            if (!$clinicIds) {
                $this->db->where('1 = 0', null, false);
            } else {
                $this->db->where_in('appointment.clinic_id', $clinicIds);
            }
        }
    }

    private function monthlySummary($physicianId, $startDate, $endDate, $clinicIds = null)
    {
        $this->db
            ->select("COUNT(appointment.appointment_id) AS total", false)
            ->select("SUM(CASE WHEN appointment.app_status = 'Pending' THEN 1 ELSE 0 END) AS pending", false)
            ->select("SUM(CASE WHEN appointment.app_status = 'Done' THEN 1 ELSE 0 END) AS done", false)
            ->select("SUM(CASE WHEN appointment.app_status = 'Cancelled' THEN 1 ELSE 0 END) AS cancelled", false)
            ->select("COUNT(DISTINCT appointment.patient_id) AS unique_patients", false)
            ->from('appointment');

        $this->applyScope($physicianId, $startDate, $endDate, $clinicIds);
        $row = $this->db->get()->row();

        $total = $row ? (int) $row->total : 0;
        $done = $row ? (int) $row->done : 0;
        $cancelled = $row ? (int) $row->cancelled : 0;
        $resolved = $done + $cancelled;

        return array(
            'total' => $total,
            'pending' => $row ? (int) $row->pending : 0,
            'done' => $done,
            'cancelled' => $cancelled,
            'unique_patients' => $row ? (int) $row->unique_patients : 0,
            'completion_rate' => $resolved > 0 ? (int) round(($done / $resolved) * 100) : 0,
        );
    }

    private function dailySeries($physicianId, $startDate, $endDate, $clinicIds = null)
    {
        $this->db
            ->select('appointment.app_date, COUNT(appointment.appointment_id) AS total', false)
            ->select("SUM(CASE WHEN appointment.app_status = 'Done' THEN 1 ELSE 0 END) AS done", false)
            ->select("SUM(CASE WHEN appointment.app_status = 'Cancelled' THEN 1 ELSE 0 END) AS cancelled", false)
            ->from('appointment');

        $this->applyScope($physicianId, $startDate, $endDate, $clinicIds);
        $rows = $this->db
            ->group_by('appointment.app_date')
            ->order_by('appointment.app_date', 'ASC')
            ->get()
            ->result();

        $byDate = array();
        foreach ($rows as $row) {
            $byDate[$row->app_date] = array(
                'total' => (int) $row->total,
                'done' => (int) $row->done,
                'cancelled' => (int) $row->cancelled,
            );
        }

        $series = array();
        $cursor = strtotime($startDate);
        $last = strtotime($endDate);
        while ($cursor <= $last) {
            $date = date('Y-m-d', $cursor);
            $counts = isset($byDate[$date])
                ? $byDate[$date]
                : array('total' => 0, 'done' => 0, 'cancelled' => 0);

            $series[] = array(
                'date' => $date,
                'label' => date('M j', $cursor),
                'total' => $counts['total'],
                'done' => $counts['done'],
                'cancelled' => $counts['cancelled'],
            );

            $cursor = strtotime('+1 day', $cursor);
        }

        return $series;
    }

    private function clinicAverage($physicianId, $startDate, $endDate, $daysInMonth, $clinicIds = null)
    {
        $this->db
            ->select('clinic.clinic_id, clinic.name, clinic.location')
            ->select('COUNT(appointment.appointment_id) AS total_appointments', false)
            ->select('COUNT(DISTINCT appointment.app_date) AS active_days', false)
            ->select("SUM(CASE WHEN appointment.app_status = 'Done' THEN 1 ELSE 0 END) AS done", false)
            ->select("SUM(CASE WHEN appointment.app_status = 'Cancelled' THEN 1 ELSE 0 END) AS cancelled", false)
            ->from('appointment')
            ->join('clinic', 'clinic.clinic_id = appointment.clinic_id', 'left');

        $this->applyScope($physicianId, $startDate, $endDate, $clinicIds);
        $rows = $this->db
            ->group_by(array('clinic.clinic_id', 'clinic.name', 'clinic.location'))
            ->order_by('total_appointments', 'DESC')
            ->get()
            ->result();

        $result = array();
        foreach ($rows as $row) {
            $total = (int) $row->total_appointments;
            $activeDays = (int) $row->active_days;
            $result[] = array(
                'clinic_id' => (int) $row->clinic_id,
                'name' => (string) $row->name,
                'location' => (string) $row->location,
                'total' => $total,
                'active_days' => $activeDays,
                'done' => (int) $row->done,
                'cancelled' => (int) $row->cancelled,
                'average_per_calendar_day' => $daysInMonth > 0 ? round($total / $daysInMonth, 2) : 0,
                'average_per_active_day' => $activeDays > 0 ? round($total / $activeDays, 2) : 0,
            );
        }

        return $result;
    }

    private function appointmentsByStatus($physicianId, $startDate, $endDate, $status, $clinicIds = null)
    {
        $this->db
            ->select('appointment.appointment_id, appointment.app_date, appointment.purpose, appointment.app_status, appointment.queueNum')
            ->select('patient.patient_id, patient.fname, patient.mname, patient.lname')
            ->select('clinic.clinic_id, clinic.name AS clinic_name, clinic.location AS clinic_location')
            ->from('appointment')
            ->join('patient', 'patient.patient_id = appointment.patient_id', 'left')
            ->join('clinic', 'clinic.clinic_id = appointment.clinic_id', 'left')
            ->where('appointment.app_status', $status);

        $this->applyScope($physicianId, $startDate, $endDate, $clinicIds);

        return $this->db
            ->order_by('appointment.app_date', 'DESC')
            ->order_by('appointment.queueNum', 'ASC')
            ->order_by('appointment.appointment_id', 'DESC')
            ->get()
            ->result_array();
    }

    public function normalizeDate($date, $fallback)
    {
        $date = trim((string) $date);
        $parsed = DateTime::createFromFormat('Y-m-d', $date);
        if (!$parsed || $parsed->format('Y-m-d') !== $date) {
            return $fallback;
        }
        return $date;
    }

    public function adminRangeReport($startDate, $endDate, $type)
    {
        $today = date('Y-m-d');
        $defaultStart = date('Y-m-01');
        $startDate = $this->normalizeDate($startDate, $defaultStart);
        $endDate = $this->normalizeDate($endDate, $today);

        if ($startDate > $endDate) {
            $tmp = $startDate;
            $startDate = $endDate;
            $endDate = $tmp;
        }

        $type = $type === 'top_purposes' ? 'top_purposes' : 'top_clinics';

        return array(
            'type' => $type,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'range_label' => date('M j, Y', strtotime($startDate)) . ' – ' . date('M j, Y', strtotime($endDate)),
            'summary' => $this->adminSummary($startDate, $endDate),
            'daily' => $this->adminDailySeries($startDate, $endDate),
            'rows' => $type === 'top_purposes'
                ? $this->adminPurposeBreakdown($startDate, $endDate)
                : $this->adminClinicVisitBreakdown($startDate, $endDate),
        );
    }

    public function adminMonthlyAverage($month)
    {
        $month = $this->normalizeMonth($month);
        list($startDate, $endDate, $daysInMonth) = $this->monthBounds($month);

        return array(
            'type' => 'average',
            'month' => $month,
            'month_label' => date('F Y', strtotime($month . '-01')),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'days_in_month' => $daysInMonth,
            'summary' => $this->adminSummary($startDate, $endDate),
            'daily' => $this->adminDailySeries($startDate, $endDate),
            'rows' => $this->adminClinicAverage($startDate, $endDate, $daysInMonth),
        );
    }

    private function applyAdminDateRange($startDate, $endDate)
    {
        $this->db
            ->where('appointment.app_date >=', $startDate)
            ->where('appointment.app_date <=', $endDate);
    }

    private function adminSummary($startDate, $endDate)
    {
        $this->db
            ->select('COUNT(appointment.appointment_id) AS total', false)
            ->select("SUM(CASE WHEN appointment.app_status = 'Pending' THEN 1 ELSE 0 END) AS pending", false)
            ->select("SUM(CASE WHEN appointment.app_status = 'Done' THEN 1 ELSE 0 END) AS done", false)
            ->select("SUM(CASE WHEN appointment.app_status = 'Cancelled' THEN 1 ELSE 0 END) AS cancelled", false)
            ->select('COUNT(DISTINCT appointment.patient_id) AS unique_patients', false)
            ->select('COUNT(DISTINCT appointment.clinic_id) AS clinics', false)
            ->select('COUNT(DISTINCT appointment.physician_id) AS physicians', false)
            ->from('appointment');
        $this->applyAdminDateRange($startDate, $endDate);
        $row = $this->db->get()->row();

        $done = $row ? (int) $row->done : 0;
        $cancelled = $row ? (int) $row->cancelled : 0;
        $resolved = $done + $cancelled;

        return array(
            'total' => $row ? (int) $row->total : 0,
            'pending' => $row ? (int) $row->pending : 0,
            'done' => $done,
            'cancelled' => $cancelled,
            'unique_patients' => $row ? (int) $row->unique_patients : 0,
            'clinics' => $row ? (int) $row->clinics : 0,
            'physicians' => $row ? (int) $row->physicians : 0,
            'completion_rate' => $resolved > 0 ? (int) round(($done / $resolved) * 100) : 0,
        );
    }

    private function adminDailySeries($startDate, $endDate)
    {
        $this->db
            ->select('appointment.app_date, COUNT(appointment.appointment_id) AS total', false)
            ->select("SUM(CASE WHEN appointment.app_status = 'Done' THEN 1 ELSE 0 END) AS done", false)
            ->select("SUM(CASE WHEN appointment.app_status = 'Cancelled' THEN 1 ELSE 0 END) AS cancelled", false)
            ->from('appointment');
        $this->applyAdminDateRange($startDate, $endDate);
        $rows = $this->db
            ->group_by('appointment.app_date')
            ->order_by('appointment.app_date', 'ASC')
            ->get()
            ->result();

        $byDate = array();
        foreach ($rows as $row) {
            $byDate[$row->app_date] = array(
                'total' => (int) $row->total,
                'done' => (int) $row->done,
                'cancelled' => (int) $row->cancelled,
            );
        }

        $series = array();
        $cursor = strtotime($startDate);
        $last = strtotime($endDate);
        while ($cursor <= $last) {
            $date = date('Y-m-d', $cursor);
            $counts = isset($byDate[$date])
                ? $byDate[$date]
                : array('total' => 0, 'done' => 0, 'cancelled' => 0);
            $series[] = array(
                'date' => $date,
                'label' => date('M j', $cursor),
                'total' => $counts['total'],
                'done' => $counts['done'],
                'cancelled' => $counts['cancelled'],
            );
            $cursor = strtotime('+1 day', $cursor);
        }
        return $series;
    }

    private function adminClinicVisitBreakdown($startDate, $endDate)
    {
        $this->db
            ->select('clinic.clinic_id, clinic.name, clinic.location')
            ->select('COUNT(appointment.appointment_id) AS total_bookings', false)
            ->select("SUM(CASE WHEN appointment.app_status = 'Done' THEN 1 ELSE 0 END) AS completed_visits", false)
            ->select("SUM(CASE WHEN appointment.app_status = 'Pending' THEN 1 ELSE 0 END) AS pending", false)
            ->select("SUM(CASE WHEN appointment.app_status = 'Cancelled' THEN 1 ELSE 0 END) AS cancelled", false)
            ->select('COUNT(DISTINCT appointment.patient_id) AS unique_patients', false)
            ->from('appointment')
            ->join('clinic', 'clinic.clinic_id = appointment.clinic_id', 'left');
        $this->applyAdminDateRange($startDate, $endDate);

        return $this->db
            ->group_by(array('clinic.clinic_id', 'clinic.name', 'clinic.location'))
            ->order_by('completed_visits', 'DESC')
            ->order_by('total_bookings', 'DESC')
            ->get()
            ->result_array();
    }

    private function adminPurposeBreakdown($startDate, $endDate)
    {
        $this->db
            ->select('appointment.purpose')
            ->select('COUNT(appointment.appointment_id) AS total_requests', false)
            ->select("SUM(CASE WHEN appointment.app_status = 'Done' THEN 1 ELSE 0 END) AS completed_consultations", false)
            ->select("SUM(CASE WHEN appointment.app_status = 'Pending' THEN 1 ELSE 0 END) AS pending", false)
            ->select("SUM(CASE WHEN appointment.app_status = 'Cancelled' THEN 1 ELSE 0 END) AS cancelled", false)
            ->select('COUNT(DISTINCT appointment.patient_id) AS unique_patients', false)
            ->from('appointment')
            ->where("TRIM(appointment.purpose) <> ''", null, false);
        $this->applyAdminDateRange($startDate, $endDate);

        return $this->db
            ->group_by('appointment.purpose')
            ->order_by('completed_consultations', 'DESC')
            ->order_by('total_requests', 'DESC')
            ->get()
            ->result_array();
    }

    private function adminClinicAverage($startDate, $endDate, $daysInMonth)
    {
        $this->db
            ->select('clinic.clinic_id, clinic.name, clinic.location')
            ->select('COUNT(appointment.appointment_id) AS total_appointments', false)
            ->select('COUNT(DISTINCT appointment.app_date) AS active_days', false)
            ->select("SUM(CASE WHEN appointment.app_status = 'Done' THEN 1 ELSE 0 END) AS done", false)
            ->select("SUM(CASE WHEN appointment.app_status = 'Pending' THEN 1 ELSE 0 END) AS pending", false)
            ->select("SUM(CASE WHEN appointment.app_status = 'Cancelled' THEN 1 ELSE 0 END) AS cancelled", false)
            ->select('COUNT(DISTINCT appointment.patient_id) AS unique_patients', false)
            ->from('appointment')
            ->join('clinic', 'clinic.clinic_id = appointment.clinic_id', 'left');
        $this->applyAdminDateRange($startDate, $endDate);

        $rows = $this->db
            ->group_by(array('clinic.clinic_id', 'clinic.name', 'clinic.location'))
            ->order_by('total_appointments', 'DESC')
            ->get()
            ->result();

        $result = array();
        foreach ($rows as $row) {
            $total = (int) $row->total_appointments;
            $activeDays = (int) $row->active_days;
            $result[] = array(
                'clinic_id' => (int) $row->clinic_id,
                'name' => (string) $row->name,
                'location' => (string) $row->location,
                'total' => $total,
                'active_days' => $activeDays,
                'done' => (int) $row->done,
                'pending' => (int) $row->pending,
                'cancelled' => (int) $row->cancelled,
                'unique_patients' => (int) $row->unique_patients,
                'average_per_calendar_day' => $daysInMonth > 0 ? round($total / $daysInMonth, 2) : 0,
                'average_per_active_day' => $activeDays > 0 ? round($total / $activeDays, 2) : 0,
            );
        }
        return $result;
    }
}
