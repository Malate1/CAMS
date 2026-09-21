<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Appointment_Model extends CI_Model
{
    private const DEFAULT_DAILY_LIMIT = 20;
    private const BOOKING_LOCK_TIMEOUT = 10;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('ClinicAssignment_Model', 'ClinicAssignment_Model');
        $this->ClinicAssignment_Model->ensureSchema();
        $this->ensureDailyLimitSchema();
    }

    /**
     * A daily appointment limit has one source of truth per physician/date.
     *
     * Older CAMS data can contain duplicate queuecount rows for the same
     * physician and date. Normalize those rows once, preserving the highest
     * configured limit, then protect the pair with a unique index so future
     * inserts cannot accidentally create ambiguous daily limits.
     */
    private function ensureDailyLimitSchema()
    {
        if (!$this->db->table_exists('queuecount')) {
            return;
        }

        $index = $this->db
            ->query("SHOW INDEX FROM `queuecount` WHERE Key_name = 'uq_queuecount_physician_date'")
            ->row();
        if ($index) {
            return;
        }

        $schemaLockName = 'cams_queuecount_daily_limit_schema';
        $schemaLock = $this->db
            ->query('SELECT GET_LOCK(?, ?) AS acquired', array($schemaLockName, self::BOOKING_LOCK_TIMEOUT))
            ->row();
        if (!$schemaLock || (int) $schemaLock->acquired !== 1) {
            return;
        }

        try {
            // Another request may have completed the normalization while this
            // request was waiting for the schema lock.
            $index = $this->db
                ->query("SHOW INDEX FROM `queuecount` WHERE Key_name = 'uq_queuecount_physician_date'")
                ->row();
            if ($index) {
                return;
            }

            $this->db->trans_begin();

            $this->db->query("
                UPDATE queuecount keep_row
                INNER JOIN (
                    SELECT physician_id, dateLimit, MIN(id) AS keep_id, MAX(queueLimit) AS max_limit
                    FROM queuecount
                    GROUP BY physician_id, dateLimit
                    HAVING COUNT(*) > 1
                ) duplicates ON duplicates.keep_id = keep_row.id
                SET keep_row.queueLimit = duplicates.max_limit
            ");

            $this->db->query("
                DELETE duplicate_row
                FROM queuecount duplicate_row
                INNER JOIN (
                    SELECT physician_id, dateLimit, MIN(id) AS keep_id
                    FROM queuecount
                    GROUP BY physician_id, dateLimit
                    HAVING COUNT(*) > 1
                ) duplicates
                  ON duplicates.physician_id = duplicate_row.physician_id
                 AND duplicates.dateLimit = duplicate_row.dateLimit
                 AND duplicate_row.id <> duplicates.keep_id
            ");

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                return;
            }

            $this->db->trans_commit();

            $this->db->query(
                'ALTER TABLE `queuecount` ADD UNIQUE KEY `uq_queuecount_physician_date` (`physician_id`, `dateLimit`)'
            );
        } finally {
            $this->db->query('SELECT RELEASE_LOCK(?)', array($schemaLockName));
        }
    }

    /**
     * Central appointment booking operation used by patients, physicians and secretaries.
     *
     * MySQL named locks serialize both patient/date and physician/date booking
     * operations so same-day patient checks, capacity checks and queue assignment
     * cannot race each other.
     */
    public function book(array $input)
    {
        $patientId = isset($input['patient_id']) ? (int) $input['patient_id'] : 0;
        $physicianId = isset($input['physician_id']) ? (int) $input['physician_id'] : 0;
        $clinicId = isset($input['clinic_id']) ? (int) $input['clinic_id'] : 0;
        $appDate = isset($input['app_date']) ? trim((string) $input['app_date']) : '';
        $purpose = isset($input['purpose']) ? trim((string) $input['purpose']) : '';
        $specialtyName = isset($input['specialty_name']) ? trim((string) $input['specialty_name']) : '';
        $enforceSpecialty = !empty($input['enforce_specialty']);

        $validation = $this->validateBookingRequest($patientId, $physicianId, $clinicId, $appDate, $purpose, $specialtyName, $enforceSpecialty);
        if ($validation['success'] === false) {
            return $validation;
        }

        $patient = $validation['patient'];
        $clinic = $validation['clinic'];
        $dateKey = str_replace('-', '', $appDate);
        $lockNames = array(
            'cams_patient_' . $patientId . '_' . $dateKey,
            'cams_appt_' . $physicianId . '_' . $dateKey,
        );
        $acquiredLocks = $this->acquireNamedLocks($lockNames);

        if ($acquiredLocks === false) {
            return $this->failure('The appointment date is being updated. Please try again.');
        }

        $this->db->trans_begin();

        try {
            $existingPatientAppointment = $this->getPendingPatientAppointment($patientId, $appDate);
            if ($existingPatientAppointment) {
                $this->db->trans_rollback();
                return $this->failure($this->patientDateConflictMessage($existingPatientAppointment, $appDate));
            }

            $limit = $this->getOrCreateDailyLimit($physicianId, $appDate);
            $activeCount = $this->getDailyBookedCount($physicianId, $appDate);

            if ($activeCount >= $limit) {
                $this->db->trans_rollback();
                return $this->failure('This physician is fully booked for the selected date. Please choose another date.');
            }

            $queueNumber = $this->getNextQueueNumber($physicianId, $clinicId, $appDate);

            $appointment = array(
                'app_date' => $appDate,
                'purpose' => $purpose,
                'app_status' => 'Pending',
                'date_created' => date('Y-m-d H:i:s'),
                'patient_id' => $patientId,
                'physician_id' => $physicianId,
                'clinic_id' => $clinicId,
                'queueNum' => $queueNumber,
            );

            if (!$this->db->insert('appointment', $appointment)) {
                $this->db->trans_rollback();
                return $this->failure('The appointment could not be saved. Please try again.');
            }

            $appointmentId = (int) $this->db->insert_id();

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                return $this->failure('The appointment could not be completed. Please try again.');
            }

            $this->db->trans_commit();

            return array(
                'success' => true,
                'appointment_id' => $appointmentId,
                'queue_number' => $queueNumber,
                'daily_limit' => $limit,
                'active_count' => $activeCount + 1,
                'patient_fname' => $patient->fname,
                'patient_contact' => isset($patient->contact) ? $patient->contact : '',
                'clinic_name' => $clinic->name,
                'app_date' => $appDate,
            );
        } finally {
            $this->releaseNamedLocks($acquiredLocks);
        }
    }

    public function getPrimaryClinicId($physicianId)
    {
        $row = $this->db
            ->select('clinic_id')
            ->from('physician_clinic')
            ->where('physician_id', (int) $physicianId)
            ->order_by('id', 'ASC')
            ->limit(1)
            ->get()
            ->row();

        return $row ? (int) $row->clinic_id : 0;
    }

    /**
     * Create or update a physician's daily queue limit without silently
     * cancelling existing appointments.
     */
    public function saveDailyLimit($physicianId, $appDate, $limit, $queueCountId = null)
    {
        $physicianId = (int) $physicianId;
        $limit = (int) $limit;
        $appDate = trim((string) $appDate);
        $queueCountId = $queueCountId !== null ? (int) $queueCountId : null;

        if ($physicianId <= 0) {
            return $this->failure('A valid physician is required.');
        }

        if ($limit < 1 || $limit > 500) {
            return $this->failure('The daily appointment limit must be between 1 and 500.');
        }

        if ($queueCountId !== null) {
            $existing = $this->db
                ->select('id, dateLimit')
                ->from('queuecount')
                ->where('id', $queueCountId)
                ->where('physician_id', $physicianId)
                ->limit(1)
                ->get()
                ->row();

            if (!$existing) {
                return $this->failure('The selected queue limit could not be found.');
            }
            $appDate = $existing->dateLimit;
        }

        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $appDate);
        $dateErrors = DateTimeImmutable::getLastErrors();
        if (!$date || ($dateErrors !== false && ($dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0)) || $date->format('Y-m-d') !== $appDate) {
            return $this->failure('Please select a valid limit date.');
        }

        // Use the same physician/date lock as booking and rescheduling so
        // daily capacity cannot change while a slot is being claimed.
        $lockName = 'cams_appt_' . $physicianId . '_' . str_replace('-', '', $appDate);
        $lock = $this->db->query('SELECT GET_LOCK(?, ?) AS acquired', array($lockName, self::BOOKING_LOCK_TIMEOUT))->row();
        if (!$lock || (int) $lock->acquired !== 1) {
            return $this->failure('The queue limit is being updated. Please try again.');
        }

        try {
            $activeCount = $this->getDailyBookedCount($physicianId, $appDate);
            if ($limit < $activeCount) {
                return $this->failure('The limit cannot be lower than the ' . $activeCount . ' non-cancelled appointment(s) already booked for this date.');
            }

            if ($queueCountId === null) {
                $exists = $this->db
                    ->from('queuecount')
                    ->where('physician_id', $physicianId)
                    ->where('dateLimit', $appDate)
                    ->count_all_results();

                if ($exists > 0) {
                    return $this->failure('A queue limit is already configured for this physician and date.');
                }

                $saved = $this->db->insert('queuecount', array(
                    'dateLimit' => $appDate,
                    'physician_id' => $physicianId,
                    'queueLimit' => $limit,
                ));
            } else {
                // Update the single physician/date limit row.
                $saved = $this->db
                    ->where('physician_id', $physicianId)
                    ->where('dateLimit', $appDate)
                    ->update('queuecount', array('queueLimit' => $limit));
            }

            if (!$saved) {
                return $this->failure('The queue limit could not be saved.');
            }

            return array(
                'success' => true,
                'date' => $appDate,
                'limit' => $limit,
                'active_count' => $activeCount,
            );
        } finally {
            $this->db->query('SELECT RELEASE_LOCK(?)', array($lockName));
        }
    }

    /**
     * Update a Pending appointment only when the current role owns the record.
     */
    public function updateStatusAuthorized($appointmentId, $targetStatus, $actorRole, $actorId)
    {
        $appointmentId = (int) $appointmentId;
        $actorId = (int) $actorId;
        $targetStatus = (string) $targetStatus;
        $actorRole = strtolower((string) $actorRole);

        $allowedTargets = array(
            'patient' => array('Cancelled'),
            'physician' => array('Done', 'Cancelled'),
            'secretary' => array('Done', 'Cancelled'),
        );

        if ($appointmentId <= 0 || $actorId <= 0 || !isset($allowedTargets[$actorRole]) || !in_array($targetStatus, $allowedTargets[$actorRole], true)) {
            return false;
        }

        $this->db->where('appointment_id', $appointmentId);
        $this->db->where('app_status', 'Pending');

        if ($actorRole === 'patient') {
            $this->db->where('patient_id', $actorId);
        } else {
            // For physicians actorId is the logged-in physician ID. For secretaries
            // actorId is the physician ID assigned to that secretary.
            $this->db->where('physician_id', $actorId);
        }

        $this->db->update('appointment', array('app_status' => $targetStatus));

        return $this->db->affected_rows() === 1;
    }

    public function getComplaintCatalog()
    {
        return array(
            array('value' => 'Rashes, Allergy, Itchiness', 'label' => 'Rashes, Allergy, Itchiness', 'icon' => 'fa-leaf', 'specialties' => array('Dermatologist')),
            array('value' => 'Fever', 'label' => 'Fever', 'icon' => 'fa-thermometer-half', 'specialties' => array('Family Medicine', 'Internal Medicine', 'Pediatrician')),
            array('value' => 'Back Pain and Low Back', 'label' => 'Back Pain and Low Back', 'icon' => 'fa-user-md', 'specialties' => array('Rheumatologist', 'Neurologist', 'Family Medicine')),
            array('value' => 'Cough and Colds', 'label' => 'Cough and Colds', 'icon' => 'fa-stethoscope', 'specialties' => array('Family Medicine', 'Internal Medicine', 'Pediatrician')),
            array('value' => 'Abdominal Pain', 'label' => 'Abdominal Pain', 'icon' => 'fa-medkit', 'specialties' => array('Internal Medicine', 'Family Medicine')),
            array('value' => 'High Blood and Pressure', 'label' => 'High Blood Pressure', 'icon' => 'fa-heartbeat', 'specialties' => array('Cardiologist', 'Internal Medicine')),
            array('value' => 'Dizziness', 'label' => 'Dizziness', 'icon' => 'fa-refresh', 'specialties' => array('Neurologist', 'Internal Medicine', 'Family Medicine')),
            array('value' => 'Chest pain', 'label' => 'Chest Pain', 'icon' => 'fa-heart', 'specialties' => array('Cardiologist', 'Internal Medicine')),
            array('value' => 'Headache', 'label' => 'Headache', 'icon' => 'fa-user-md', 'specialties' => array('Neurologist', 'Family Medicine')),
            array('value' => 'Accidents and Trauma', 'label' => 'Accidents and Trauma', 'icon' => 'fa-ambulance', 'specialties' => array('Family Medicine', 'Internal Medicine')),
        );
    }

    public function getActiveSpecialties()
    {
        $rows = $this->db
            ->select('TRIM(specialization.special_name) AS special_name', false)
            ->from('specialization')
            ->join('physician_clinic_specialization', 'physician_clinic_specialization.special_id = specialization.special_id')
            ->join('physician_clinic', 'physician_clinic.id = physician_clinic_specialization.physician_clinic_id')
            ->join('physician', 'physician.physician_id = physician_clinic.physician_id')
            ->where('physician.status', 'Active')
            ->group_by('TRIM(specialization.special_name)', false)
            ->order_by('special_name', 'ASC')
            ->get()
            ->result();

        $items = array();
        foreach ($rows as $row) {
            $name = trim((string) $row->special_name);
            if ($name !== '') {
                $items[] = $name;
            }
        }
        return array_values(array_unique($items));
    }

    public function getBookingProviders()
    {
        $rows = $this->db
            ->select("physician.physician_id, physician.fname, physician.lname, physician.image, physician_clinic.id AS physician_clinic_id, physician_clinic.secretary_id, clinic.clinic_id, clinic.name AS clinic_name, clinic.location, GROUP_CONCAT(DISTINCT CONCAT(schedule.day, '~', schedule.time_in, '~', schedule.time_out) ORDER BY schedule.schedule_id SEPARATOR '|') AS schedule_rows, GROUP_CONCAT(DISTINCT TRIM(specialization.special_name) ORDER BY specialization.special_name SEPARATOR '|') AS specializations", false)
            ->from('physician')
            ->join('physician_clinic', 'physician_clinic.physician_id = physician.physician_id')
            ->join('clinic', 'clinic.clinic_id = physician_clinic.clinic_id')
            ->join('physician_clinic_schedule', 'physician_clinic_schedule.physician_clinic_id = physician_clinic.id')
            ->join('schedule', 'schedule.schedule_id = physician_clinic_schedule.schedule_id')
            ->join('physician_clinic_specialization', 'physician_clinic_specialization.physician_clinic_id = physician_clinic.id', 'left')
            ->join('specialization', 'specialization.special_id = physician_clinic_specialization.special_id', 'left')
            ->where('physician.status', 'Active')
            ->group_by(array('physician.physician_id', 'physician_clinic.id', 'clinic.clinic_id'))
            ->order_by('physician.lname', 'ASC')
            ->get()
            ->result();

        foreach ($rows as $row) {
            $row->specialty_list = array_values(array_filter(array_map('trim', explode('|', (string) $row->specializations))));
            $scheduleLabels = array();
            foreach (array_filter(explode('|', (string) $row->schedule_rows)) as $scheduleRow) {
                $parts = explode('~', $scheduleRow);
                if (count($parts) !== 3) continue;
                $scheduleLabels[] = $this->formatScheduleCode($parts[0]) . ' · ' . date('g:i A', strtotime($parts[1])) . '–' . date('g:i A', strtotime($parts[2]));
            }
            $row->schedule_label = implode(' | ', $scheduleLabels);
        }

        return $rows;
    }

    public function getAvailableDates($physicianId, $clinicId, $patientId = 0)
    {
        $physicianId = (int) $physicianId;
        $clinicId = (int) $clinicId;
        $patientId = (int) $patientId;

        $validClinic = $this->db
            ->from('physician_clinic')
            ->where('physician_id', $physicianId)
            ->where('clinic_id', $clinicId)
            ->count_all_results() > 0;
        if (!$validClinic) {
            return array();
        }

        $scheduleRows = $this->db
            ->select('schedule.day, schedule.time_in, schedule.time_out')
            ->from('physician_clinic')
            ->join('physician_clinic_schedule', 'physician_clinic_schedule.physician_clinic_id = physician_clinic.id')
            ->join('schedule', 'schedule.schedule_id = physician_clinic_schedule.schedule_id')
            ->where('physician_clinic.physician_id', $physicianId)
            ->where('physician_clinic.clinic_id', $clinicId)
            ->get()
            ->result();
        if (!$scheduleRows) {
            return array();
        }

        $tz = new DateTimeZone('Asia/Manila');
        $cursor = new DateTimeImmutable('tomorrow', $tz);
        $end = (new DateTimeImmutable('today', $tz))->modify('+2 months');
        $pendingByDate = $patientId > 0
            ? $this->getPendingPatientAppointmentsByDate($patientId, $cursor->format('Y-m-d'), $end->format('Y-m-d'))
            : array();
        $scheduleConflictByWeekday = $this->ClinicAssignment_Model->getPhysicianClinicConflictWeekdays($physicianId, $clinicId);
        $dates = array();

        while ($cursor <= $end) {
            $weekday = (int) $cursor->format('w');
            $matching = array();
            foreach ($scheduleRows as $schedule) {
                if ($this->scheduleIncludesDay($schedule->day, $weekday)) {
                    $matching[] = date('g:i A', strtotime($schedule->time_in)) . '–' . date('g:i A', strtotime($schedule->time_out));
                }
            }

            if ($matching) {
                $dateValue = $cursor->format('Y-m-d');
                $limit = $this->getConfiguredDailyLimit($physicianId, $dateValue);
                $booked = $this->getDailyBookedCount($physicianId, $dateValue);
                $remaining = max(0, $limit - $booked);
                $patientAppointment = isset($pendingByDate[$dateValue]) ? $pendingByDate[$dateValue] : null;
                $patientBooked = $patientAppointment !== null;
                $scheduleConflict = isset($scheduleConflictByWeekday[$weekday])
                    ? $scheduleConflictByWeekday[$weekday]
                    : '';
                $dates[] = array(
                    'date' => $dateValue,
                    'weekday' => $cursor->format('D'),
                    'day' => $cursor->format('j'),
                    'month' => $cursor->format('M'),
                    'label' => $cursor->format('l, M j, Y'),
                    'schedule' => implode(', ', $matching),
                    'remaining' => $remaining,
                    'limit' => $limit,
                    'capacity_full' => $remaining <= 0,
                    'patient_booked' => $patientBooked,
                    'schedule_conflict' => $scheduleConflict !== '',
                    'unavailable_reason' => $patientBooked
                        ? $this->patientDateConflictMessage($patientAppointment, $dateValue)
                        : ($scheduleConflict !== ''
                            ? $scheduleConflict
                            : ($remaining <= 0 ? 'This physician is fully booked for this date.' : '')),
                    'full' => $remaining <= 0 || $patientBooked || $scheduleConflict !== '',
                );
            }

            $cursor = $cursor->modify('+1 day');
        }

        return $dates;
    }

    public function physicianSupportsSpecialty($physicianId, $specialtyName, $clinicId = null)
    {
        $specialtyName = trim((string) $specialtyName);
        if ($specialtyName === '') {
            return true;
        }

        $this->db
            ->from('physician_clinic')
            ->join('physician_clinic_specialization', 'physician_clinic_specialization.physician_clinic_id = physician_clinic.id')
            ->join('specialization', 'specialization.special_id = physician_clinic_specialization.special_id')
            ->where('physician_clinic.physician_id', (int) $physicianId)
            ->where('TRIM(specialization.special_name) = ' . $this->db->escape($specialtyName), null, false);
        if ($clinicId !== null) {
            $this->db->where('physician_clinic.clinic_id', (int) $clinicId);
        }
        return $this->db->count_all_results() > 0;
    }

    private function getConfiguredDailyLimit($physicianId, $appDate)
    {
        $row = $this->db
            ->select_max('queueLimit', 'queue_limit')
            ->from('queuecount')
            ->where('physician_id', (int) $physicianId)
            ->where('dateLimit', $appDate)
            ->get()
            ->row();

        $limit = ($row && $row->queue_limit !== null) ? (int) $row->queue_limit : 0;
        return $limit > 0 ? $limit : self::DEFAULT_DAILY_LIMIT;
    }

    private function specialtiesForComplaint($purpose)
    {
        foreach ($this->getComplaintCatalog() as $complaint) {
            if (strcasecmp(trim($complaint['value']), trim((string) $purpose)) === 0) {
                return $complaint['specialties'];
            }
        }
        return array();
    }

    private function formatScheduleCode($code)
    {
        $labels = array(
            'M-F' => 'Monday–Friday',
            'MTW' => 'Monday–Wednesday',
            'TTh' => 'Tuesday & Thursday',
            'Sat' => 'Saturday',
            'Sun' => 'Sunday',
            'MWF' => 'Monday, Wednesday & Friday',
            'ThF' => 'Thursday & Friday',
        );
        return isset($labels[$code]) ? $labels[$code] : $code;
    }

    private function validateBookingRequest($patientId, $physicianId, $clinicId, $appDate, $purpose, $specialtyName = '', $enforceSpecialty = false, $enforceScheduleConflict = true)
    {
        if ($patientId <= 0 || $physicianId <= 0 || $clinicId <= 0) {
            return $this->failure('Please select a valid patient, physician and clinic.');
        }

        if ($purpose === '') {
            return $this->failure('Please provide the reason for the appointment.');
        }

        $purposeLength = function_exists('mb_strlen') ? mb_strlen($purpose) : strlen($purpose);
        if ($purposeLength > 100) {
            return $this->failure('The appointment reason must be 100 characters or fewer.');
        }

        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $appDate);
        $dateErrors = DateTimeImmutable::getLastErrors();
        if (!$date || ($dateErrors !== false && ($dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0)) || $date->format('Y-m-d') !== $appDate) {
            return $this->failure('Please select a valid appointment date.');
        }

        $timezone = new DateTimeZone('Asia/Manila');
        $today = new DateTimeImmutable('today', $timezone);
        $minimum = $today->modify('+1 day');
        $maximum = $today->modify('+2 months');

        if ($date < $minimum) {
            return $this->failure('Appointments must be booked at least one day in advance.');
        }

        if ($date > $maximum) {
            return $this->failure('Appointments can only be booked up to two months in advance.');
        }

        $patient = $this->db
            ->select('patient_id, fname, contact')
            ->from('patient')
            ->where('patient_id', $patientId)
            ->limit(1)
            ->get()
            ->row();

        if (!$patient) {
            return $this->failure('The selected patient could not be found.');
        }

        $clinic = $this->db
            ->select('clinic.clinic_id, clinic.name')
            ->from('physician_clinic')
            ->join('clinic', 'clinic.clinic_id = physician_clinic.clinic_id')
            ->where('physician_clinic.physician_id', $physicianId)
            ->where('physician_clinic.clinic_id', $clinicId)
            ->limit(1)
            ->get()
            ->row();

        if (!$clinic) {
            return $this->failure('The selected physician is not assigned to this clinic.');
        }

        if ($enforceSpecialty) {
            $recommended = $this->specialtiesForComplaint($purpose);

            if ($recommended) {
                $matched = false;
                foreach ($recommended as $recommendedSpecialty) {
                    if ($this->physicianSupportsSpecialty($physicianId, $recommendedSpecialty, $clinicId)) {
                        $matched = true;
                        break;
                    }
                }
                if (!$matched) {
                    return $this->failure('The selected physician does not match the recommended specialization for this complaint.');
                }
                if ($specialtyName !== '' && !in_array($specialtyName, $recommended, true)) {
                    return $this->failure('The selected specialty does not match this complaint.');
                }
            } else {
                if ($specialtyName === '') {
                    return $this->failure('Please select a specialty for this concern.');
                }
                if (!$this->physicianSupportsSpecialty($physicianId, $specialtyName, $clinicId)) {
                    return $this->failure('The selected physician does not match the chosen specialty.');
                }
            }
        } elseif ($specialtyName !== '' && !$this->physicianSupportsSpecialty($physicianId, $specialtyName, $clinicId)) {
            return $this->failure('The selected physician does not match the selected specialty.');
        }

        $scheduleRows = $this->db
            ->select('schedule.day')
            ->from('physician_clinic')
            ->join('physician_clinic_schedule', 'physician_clinic_schedule.physician_clinic_id = physician_clinic.id')
            ->join('schedule', 'schedule.schedule_id = physician_clinic_schedule.schedule_id')
            ->where('physician_clinic.physician_id', $physicianId)
            ->where('physician_clinic.clinic_id', $clinicId)
            ->get()
            ->result();

        if (!$scheduleRows) {
            return $this->failure('This physician does not have an appointment schedule configured.');
        }

        $dayOfWeek = (int) $date->format('w');
        $available = false;
        foreach ($scheduleRows as $schedule) {
            if ($this->scheduleIncludesDay($schedule->day, $dayOfWeek)) {
                $available = true;
                break;
            }
        }

        if (!$available) {
            return $this->failure('The physician is not scheduled at the clinic on the selected day.');
        }

        if ($enforceScheduleConflict) {
            $scheduleConflicts = $this->ClinicAssignment_Model->getPhysicianClinicConflictWeekdays($physicianId, $clinicId);
            if (isset($scheduleConflicts[$dayOfWeek])) {
                return $this->failure($scheduleConflicts[$dayOfWeek]);
            }
        }

        return array(
            'success' => true,
            'patient' => $patient,
            'clinic' => $clinic,
        );
    }

    /**
     * Server-side DataTables source for appointment lists.
     */
    public function getAppointmentsData($actorRole, $actorId, array $request, $scope = 'all')
    {
        $actorRole = strtolower((string) $actorRole);
        $actorId = (int) $actorId;
        $scope = in_array($scope, array('all', 'today', 'done', 'cancelled', 'date'), true) ? $scope : 'all';
        $filterDate = isset($request['date']) ? trim((string) $request['date']) : '';
        $draw = isset($request['draw']) ? (int) $request['draw'] : 0;
        $start = isset($request['start']) ? max(0, (int) $request['start']) : 0;
        $length = isset($request['length']) ? (int) $request['length'] : 10;
        $length = ($length < 1 || $length > 100) ? 10 : $length;
        $search = isset($request['search']['value']) ? trim((string) $request['search']['value']) : '';

        $this->applyAppointmentListQuery($actorRole, $actorId, $scope, '', $filterDate);
        $recordsTotal = (int) $this->db->count_all_results();

        $this->applyAppointmentListQuery($actorRole, $actorId, $scope, $search, $filterDate);
        $recordsFiltered = (int) $this->db->count_all_results();

        $this->db->select("appointment.appointment_id, appointment.app_date, appointment.purpose, appointment.app_status, appointment.queueNum, appointment.patient_id, appointment.physician_id, appointment.clinic_id, CONCAT(patient.fname, ' ', patient.lname) AS patient_name, CONCAT(physician.fname, ' ', physician.lname) AS physician_name, clinic.name AS clinic_name, clinic.location AS clinic_location", false);
        $this->applyAppointmentListQuery($actorRole, $actorId, $scope, $search, $filterDate);

        $sortMap = array(
            'appointment_id' => 'appointment.appointment_id',
            'app_date' => 'appointment.app_date',
            'purpose' => 'appointment.purpose',
            'queue_number' => 'appointment.queueNum',
            'patient_name' => 'patient.lname',
            'physician_name' => 'physician.lname',
            'clinic_name' => 'clinic.name',
            'status' => 'appointment.app_status',
        );
        $orderColumn = 'appointment.app_date';
        $orderDirection = 'DESC';
        if (!empty($request['order'][0])) {
            $columnIndex = (int) $request['order'][0]['column'];
            $direction = strtolower((string) $request['order'][0]['dir']) === 'asc' ? 'ASC' : 'DESC';
            $columnName = isset($request['columns'][$columnIndex]['data']) ? (string) $request['columns'][$columnIndex]['data'] : '';
            if (isset($sortMap[$columnName])) {
                $orderColumn = $sortMap[$columnName];
                $orderDirection = $direction;
            }
        }

        $rows = $this->db
            ->order_by($orderColumn, $orderDirection)
            ->order_by('appointment.appointment_id', 'DESC')
            ->limit($length, $start)
            ->get()
            ->result();

        $today = date('Y-m-d');
        $data = array();
        foreach ($rows as $row) {
            $data[] = array(
                'appointment_id' => str_pad((string) $row->appointment_id, 8, '0', STR_PAD_LEFT),
                'app_date' => $row->app_date,
                'purpose' => $row->purpose,
                'queue_number' => (int) $row->queueNum,
                'patient_name' => $row->patient_name,
                'physician_name' => $row->physician_name,
                'clinic_name' => $row->clinic_name,
                'clinic_location' => $row->clinic_location,
                'status' => $row->app_status,
                'can_edit' => $row->app_status === 'Pending',
                'can_cancel' => $row->app_status === 'Pending',
                'can_done' => $row->app_status === 'Pending' && $actorRole !== 'patient',
                'is_past' => $row->app_date < $today,
            );
        }

        return array(
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        );
    }

    public function getEditableAppointment($appointmentId, $actorRole, $actorId)
    {
        $appointmentId = (int) $appointmentId;
        $actorId = (int) $actorId;
        $actorRole = strtolower((string) $actorRole);

        $this->db
            ->select("appointment.appointment_id, appointment.app_date, appointment.purpose, appointment.app_status, appointment.queueNum, appointment.patient_id, appointment.physician_id, appointment.clinic_id, CONCAT(patient.fname, ' ', patient.lname) AS patient_name, CONCAT(physician.fname, ' ', physician.lname) AS physician_name, clinic.name AS clinic_name, clinic.location AS clinic_location, schedule.day, schedule.time_in, schedule.time_out", false)
            ->from('appointment')
            ->join('patient', 'patient.patient_id = appointment.patient_id')
            ->join('physician', 'physician.physician_id = appointment.physician_id')
            ->join('clinic', 'clinic.clinic_id = appointment.clinic_id')
            ->join('physician_clinic', 'physician_clinic.physician_id = appointment.physician_id AND physician_clinic.clinic_id = appointment.clinic_id', 'left')
            ->join('physician_clinic_schedule', 'physician_clinic_schedule.physician_clinic_id = physician_clinic.id', 'left')
            ->join('schedule', 'schedule.schedule_id = physician_clinic_schedule.schedule_id', 'left')
            ->where('appointment.appointment_id', $appointmentId)
            ->where('appointment.app_status', 'Pending');

        if ($actorRole === 'patient') {
            $this->db->where('appointment.patient_id', $actorId);
        } elseif ($actorRole === 'physician' || $actorRole === 'secretary') {
            $this->db->where('appointment.physician_id', $actorId);
        } else {
            return null;
        }

        $row = $this->db->limit(1)->get()->row();
        if (!$row) {
            return null;
        }

        return array(
            'appointment_id' => (int) $row->appointment_id,
            'patient_id' => (int) $row->patient_id,
            'app_date' => $row->app_date,
            'purpose' => $row->purpose,
            'queue_number' => (int) $row->queueNum,
            'patient_name' => $row->patient_name,
            'physician_name' => $row->physician_name,
            'physician_id' => (int) $row->physician_id,
            'clinic_id' => (int) $row->clinic_id,
            'clinic_name' => $row->clinic_name,
            'clinic_location' => $row->clinic_location,
            'schedule' => $row->day ? $this->formatScheduleCode($row->day) . ' · ' . date('g:i A', strtotime($row->time_in)) . '–' . date('g:i A', strtotime($row->time_out)) : '',
        );
    }

    /**
     * Edit a Pending appointment without allowing ownership/provider tampering.
     * If the date changes, capacity and queue assignment are recalculated safely.
     */
    public function updateAppointmentAuthorized($appointmentId, $appDate, $purpose, $actorRole, $actorId)
    {
        $appointmentId = (int) $appointmentId;
        $actorId = (int) $actorId;
        $actorRole = strtolower((string) $actorRole);
        $appDate = trim((string) $appDate);
        $purpose = trim((string) $purpose);

        $current = $this->db
            ->select('appointment_id, patient_id, physician_id, clinic_id, app_date, purpose, app_status, queueNum')
            ->from('appointment')
            ->where('appointment_id', $appointmentId)
            ->where('app_status', 'Pending')
            ->limit(1)
            ->get()
            ->row();

        if (!$current) {
            return $this->failure('This appointment is no longer editable.');
        }
        if ($actorRole === 'patient' && (int) $current->patient_id !== $actorId) {
            return $this->failure('You are not allowed to edit this appointment.');
        }
        if (($actorRole === 'physician' || $actorRole === 'secretary') && (int) $current->physician_id !== $actorId) {
            return $this->failure('You are not allowed to edit this appointment.');
        }
        if (!in_array($actorRole, array('patient', 'physician', 'secretary'), true)) {
            return $this->failure('You are not allowed to edit this appointment.');
        }

        $dateChanged = $appDate !== $current->app_date;

        $validation = $this->validateBookingRequest(
            (int) $current->patient_id,
            (int) $current->physician_id,
            (int) $current->clinic_id,
            $appDate,
            $purpose,
            '',
            false,
            $dateChanged
        );
        if (!$validation['success']) {
            return $validation;
        }

        // Keep edits consistent with the booking wizard: when the edited reason
        // maps to known specialties, the existing physician must still match it.
        $requiredSpecialties = $this->specialtiesForComplaint($purpose);
        if ($requiredSpecialties) {
            $matchesSpecialty = false;
            foreach ($requiredSpecialties as $specialty) {
                if ($this->physicianSupportsSpecialty((int) $current->physician_id, $specialty, (int) $current->clinic_id)) {
                    $matchesSpecialty = true;
                    break;
                }
            }
            if (!$matchesSpecialty) {
                return $this->failure('This concern is not covered by the assigned physician. Please cancel this appointment and create a new booking with a matching specialist.');
            }
        }

        if (!$dateChanged) {
            $this->db
                ->where('appointment_id', $appointmentId)
                ->where('app_status', 'Pending')
                ->update('appointment', array('purpose' => $purpose));
            return array('success' => true, 'queue_number' => (int) $current->queueNum, 'app_date' => $appDate);
        }

        $dateKey = str_replace('-', '', $appDate);
        $lockNames = array(
            'cams_patient_' . (int) $current->patient_id . '_' . $dateKey,
            'cams_appt_' . (int) $current->physician_id . '_' . $dateKey,
        );
        $acquiredLocks = $this->acquireNamedLocks($lockNames);
        if ($acquiredLocks === false) {
            return $this->failure('The selected date is being updated. Please try again.');
        }

        $this->db->trans_begin();
        try {
            $existingPatientAppointment = $this->getPendingPatientAppointment(
                (int) $current->patient_id,
                $appDate,
                $appointmentId
            );
            if ($existingPatientAppointment) {
                $this->db->trans_rollback();
                return $this->failure($this->patientDateConflictMessage($existingPatientAppointment, $appDate));
            }

            $limit = $this->getOrCreateDailyLimit((int) $current->physician_id, $appDate);
            $activeCount = $this->getDailyBookedCount((int) $current->physician_id, $appDate);
            if ($activeCount >= $limit) {
                $this->db->trans_rollback();
                return $this->failure('The physician is fully booked for the selected date.');
            }

            $queueNumber = $this->getNextQueueNumber((int) $current->physician_id, (int) $current->clinic_id, $appDate);
            $this->db
                ->where('appointment_id', $appointmentId)
                ->where('app_status', 'Pending')
                ->update('appointment', array(
                    'app_date' => $appDate,
                    'purpose' => $purpose,
                    'queueNum' => $queueNumber,
                ));

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                return $this->failure('The appointment could not be updated. Please try again.');
            }
            $this->db->trans_commit();
            return array('success' => true, 'queue_number' => $queueNumber, 'app_date' => $appDate);
        } finally {
            $this->releaseNamedLocks($acquiredLocks);
        }
    }

    private function applyAppointmentListQuery($actorRole, $actorId, $scope, $search = '', $filterDate = '')
    {
        $this->db->from('appointment');
        $this->db->join('patient', 'patient.patient_id = appointment.patient_id');
        $this->db->join('physician', 'physician.physician_id = appointment.physician_id');
        $this->db->join('clinic', 'clinic.clinic_id = appointment.clinic_id');

        if ($actorRole === 'patient') {
            $this->db->where('appointment.patient_id', (int) $actorId);
        } else {
            $this->db->where('appointment.physician_id', (int) $actorId);
        }

        if ($scope === 'today') {
            $this->db->where('appointment.app_date', date('Y-m-d'));
        } elseif ($scope === 'done') {
            $this->db->where('appointment.app_status', 'Done');
        } elseif ($scope === 'cancelled') {
            $this->db->where('appointment.app_status', 'Cancelled');
        } elseif ($scope === 'date' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $filterDate)) {
            $this->db->where('appointment.app_date', $filterDate);
        }

        if ($search !== '') {
            $this->db->group_start()
                ->like('appointment.appointment_id', $search)
                ->or_like('appointment.app_date', $search)
                ->or_like('appointment.purpose', $search)
                ->or_like('appointment.app_status', $search)
                ->or_like('appointment.queueNum', $search)
                ->or_like('patient.fname', $search)
                ->or_like('patient.lname', $search)
                ->or_like('physician.fname', $search)
                ->or_like('physician.lname', $search)
                ->or_like('clinic.name', $search)
                ->group_end();
        }
    }

    private function scheduleIncludesDay($scheduleCode, $dayOfWeek)
    {
        $days = array(
            'M-F' => array(1, 2, 3, 4, 5),
            'MTW' => array(1, 2, 3),
            'TTh' => array(2, 4),
            'Sat' => array(6),
            'Sun' => array(0),
            'MWF' => array(1, 3, 5),
            'ThF' => array(4, 5),
        );

        return isset($days[$scheduleCode]) && in_array((int) $dayOfWeek, $days[$scheduleCode], true);
    }

    private function getPendingPatientAppointment($patientId, $appDate, $excludeAppointmentId = 0)
    {
        $this->db
            ->select("appointment.appointment_id, appointment.app_date, appointment.purpose, appointment.queueNum, clinic.name AS clinic_name, CONCAT(physician.fname, ' ', physician.lname) AS physician_name", false)
            ->from('appointment')
            ->join('clinic', 'clinic.clinic_id = appointment.clinic_id')
            ->join('physician', 'physician.physician_id = appointment.physician_id')
            ->where('appointment.patient_id', (int) $patientId)
            ->where('appointment.app_date', (string) $appDate)
            ->where('appointment.app_status', 'Pending');

        if ((int) $excludeAppointmentId > 0) {
            $this->db->where('appointment.appointment_id !=', (int) $excludeAppointmentId);
        }

        return $this->db
            ->order_by('appointment.appointment_id', 'ASC')
            ->limit(1)
            ->get()
            ->row();
    }

    private function getPendingPatientAppointmentsByDate($patientId, $startDate, $endDate)
    {
        if ((int) $patientId <= 0) {
            return array();
        }

        $rows = $this->db
            ->select("appointment.appointment_id, appointment.app_date, clinic.name AS clinic_name, CONCAT(physician.fname, ' ', physician.lname) AS physician_name", false)
            ->from('appointment')
            ->join('clinic', 'clinic.clinic_id = appointment.clinic_id')
            ->join('physician', 'physician.physician_id = appointment.physician_id')
            ->where('appointment.patient_id', (int) $patientId)
            ->where('appointment.app_status', 'Pending')
            ->where('appointment.app_date >=', (string) $startDate)
            ->where('appointment.app_date <=', (string) $endDate)
            ->order_by('appointment.app_date', 'ASC')
            ->order_by('appointment.appointment_id', 'ASC')
            ->get()
            ->result();

        $byDate = array();
        foreach ($rows as $row) {
            if (!isset($byDate[$row->app_date])) {
                $byDate[$row->app_date] = $row;
            }
        }

        return $byDate;
    }

    private function patientDateConflictMessage($appointment, $appDate)
    {
        $label = date('F j, Y', strtotime((string) $appDate));
        $clinic = !empty($appointment->clinic_name) ? $appointment->clinic_name : 'another clinic';
        $physician = !empty($appointment->physician_name) ? ' with Dr. ' . $appointment->physician_name : '';

        return 'This patient already has a pending appointment on ' . $label
            . ' at ' . $clinic . $physician
            . '. Cancel or reschedule that appointment before booking another appointment on the same date.';
    }

    private function acquireNamedLocks(array $lockNames)
    {
        $lockNames = array_values(array_unique(array_filter(array_map('strval', $lockNames))));
        sort($lockNames, SORT_STRING);
        $acquired = array();

        foreach ($lockNames as $lockName) {
            $row = $this->db
                ->query('SELECT GET_LOCK(?, ?) AS acquired', array($lockName, self::BOOKING_LOCK_TIMEOUT))
                ->row();

            if (!$row || (int) $row->acquired !== 1) {
                $this->releaseNamedLocks($acquired);
                return false;
            }

            $acquired[] = $lockName;
        }

        return $acquired;
    }

    private function releaseNamedLocks(array $lockNames)
    {
        foreach (array_reverse($lockNames) as $lockName) {
            $this->db->query('SELECT RELEASE_LOCK(?)', array($lockName));
        }
    }

    private function getOrCreateDailyLimit($physicianId, $appDate)
    {
        $rows = $this->db
            ->select('id, queueLimit')
            ->from('queuecount')
            ->where('physician_id', $physicianId)
            ->where('dateLimit', $appDate)
            ->get()
            ->result();

        $limit = 0;
        foreach ($rows as $row) {
            $limit = max($limit, (int) $row->queueLimit);
        }

        if ($limit > 0) {
            return $limit;
        }

        if ($rows) {
            // Normalize existing zero-value rows instead of inserting another duplicate.
            $this->db
                ->where('physician_id', $physicianId)
                ->where('dateLimit', $appDate)
                ->update('queuecount', array('queueLimit' => self::DEFAULT_DAILY_LIMIT));
        } else {
            $this->db->insert('queuecount', array(
                'queueLimit' => self::DEFAULT_DAILY_LIMIT,
                'physician_id' => $physicianId,
                'dateLimit' => $appDate,
            ));
        }

        return self::DEFAULT_DAILY_LIMIT;
    }

    /**
     * Count only appointments that consume capacity on one specific calendar day.
     *
     * The date predicate is intentionally part of this helper so every caller
     * uses the daily limit against that date only. Cancelled appointments free
     * their slot and therefore do not count toward the day's capacity.
     */
    private function getDailyBookedCount($physicianId, $appDate)
    {
        return (int) $this->db
            ->from('appointment')
            ->where('physician_id', (int) $physicianId)
            ->where('app_date', (string) $appDate)
            ->where('app_status !=', 'Cancelled')
            ->count_all_results();
    }

    private function getNextQueueNumber($physicianId, $clinicId, $appDate)
    {
        $row = $this->db
            ->select_max('queueNum', 'max_queue')
            ->from('appointment')
            ->where('physician_id', $physicianId)
            ->where('clinic_id', $clinicId)
            ->where('app_date', $appDate)
            ->get()
            ->row();

        return ($row && $row->max_queue !== null ? (int) $row->max_queue : 0) + 1;
    }

    private function failure($message)
    {
        return array(
            'success' => false,
            'message' => $message,
        );
    }
}
