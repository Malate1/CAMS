<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ClinicAssignment_Model extends CI_Model
{
    public function ensureSchema()
    {
        $createdScheduleTable = false;
        $createdSpecializationTable = false;

        if (!$this->db->table_exists('physician_clinic_schedule')) {
            $this->db->query("CREATE TABLE `physician_clinic_schedule` (
                `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
                `physician_clinic_id` BIGINT(5) NOT NULL,
                `schedule_id` BIGINT(5) NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uq_pc_schedule` (`physician_clinic_id`,`schedule_id`),
                KEY `idx_pc_schedule_schedule` (`schedule_id`),
                CONSTRAINT `fk_pc_schedule_assignment` FOREIGN KEY (`physician_clinic_id`) REFERENCES `physician_clinic` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk_pc_schedule_schedule` FOREIGN KEY (`schedule_id`) REFERENCES `schedule` (`schedule_id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
            $createdScheduleTable = true;
        }

        if (!$this->db->table_exists('physician_clinic_specialization')) {
            $this->db->query("CREATE TABLE `physician_clinic_specialization` (
                `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
                `physician_clinic_id` BIGINT(5) NOT NULL,
                `special_id` BIGINT(5) NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uq_pc_special` (`physician_clinic_id`,`special_id`),
                KEY `idx_pc_special_special` (`special_id`),
                CONSTRAINT `fk_pc_special_assignment` FOREIGN KEY (`physician_clinic_id`) REFERENCES `physician_clinic` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk_pc_special_specialization` FOREIGN KEY (`special_id`) REFERENCES `specialization` (`special_id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
            $createdSpecializationTable = true;
        }

        if ($createdScheduleTable) {
            $this->backfillLegacySchedules();
        }

        if ($createdSpecializationTable) {
            $this->backfillLegacySpecializations();
        }
    }

    public function backfillLegacyAssignments()
    {
        $this->backfillLegacySchedules();
        $this->backfillLegacySpecializations();
    }

    private function backfillLegacySchedules()
    {
        $physicians = $this->db
            ->select('physician_id')
            ->from('physician_clinic')
            ->group_by('physician_id')
            ->order_by('physician_id', 'ASC')
            ->get()
            ->result();

        foreach ($physicians as $physician) {
            $physicianId = (int) $physician->physician_id;

            $clinics = $this->db
                ->select('id')
                ->from('physician_clinic')
                ->where('physician_id', $physicianId)
                ->order_by('id', 'ASC')
                ->get()
                ->result();

            $schedules = $this->db
                ->select('schedule_id')
                ->from('physician_sched')
                ->where('physician_id', $physicianId)
                ->order_by('id', 'ASC')
                ->get()
                ->result();

            if (!$clinics || !$schedules) {
                continue;
            }

            // One clinic may legitimately have several schedules.
            if (count($clinics) === 1) {
                foreach ($schedules as $schedule) {
                    $this->assignSchedule((int) $clinics[0]->id, (int) $schedule->schedule_id);
                }
                continue;
            }

            // Legacy CAMS stored schedules only by physician. When several
            // clinics exist, pair clinic assignments and schedules by their
            // creation order instead of copying every schedule to every clinic.
            // This prevents historical multi-clinic data from recreating
            // overlapping physician schedules during migration.
            $pairCount = min(count($clinics), count($schedules));
            for ($index = 0; $index < $pairCount; $index++) {
                $this->assignSchedule(
                    (int) $clinics[$index]->id,
                    (int) $schedules[$index]->schedule_id
                );
            }
        }
    }

    private function backfillLegacySpecializations()
    {
        $this->db->query("INSERT IGNORE INTO physician_clinic_specialization (physician_clinic_id, special_id)
            SELECT pc.id, ps.special_id
            FROM physician_clinic pc
            INNER JOIN physician_special ps ON ps.physician_id = pc.physician_id");
    }

    public function getAssignment($physicianId, $clinicId)
    {
        return $this->db
            ->from('physician_clinic')
            ->where('physician_id', (int) $physicianId)
            ->where('clinic_id', (int) $clinicId)
            ->limit(1)
            ->get()
            ->row();
    }

    public function getAssignmentById($assignmentId)
    {
        return $this->db
            ->from('physician_clinic')
            ->where('id', (int) $assignmentId)
            ->limit(1)
            ->get()
            ->row();
    }

    public function assignSpecialization($assignmentId, $specialId)
    {
        if ((int) $assignmentId <= 0 || (int) $specialId <= 0) return false;
        return $this->db->query(
            'INSERT IGNORE INTO physician_clinic_specialization (physician_clinic_id, special_id) VALUES (?, ?)',
            array((int) $assignmentId, (int) $specialId)
        );
    }

    public function assignSchedule($assignmentId, $scheduleId)
    {
        if ((int) $assignmentId <= 0 || (int) $scheduleId <= 0) return false;
        return $this->db->query(
            'INSERT IGNORE INTO physician_clinic_schedule (physician_clinic_id, schedule_id) VALUES (?, ?)',
            array((int) $assignmentId, (int) $scheduleId)
        );
    }

    public function replaceSpecializations($assignmentId, array $specialIds)
    {
        $assignmentId = (int) $assignmentId;
        $this->db->where('physician_clinic_id', $assignmentId)->delete('physician_clinic_specialization');
        foreach (array_unique(array_map('intval', $specialIds)) as $specialId) {
            if ($specialId > 0) $this->assignSpecialization($assignmentId, $specialId);
        }
    }

    public function getClinicAssignmentsForPhysician($physicianId, $secretaryId = null)
    {
        $this->db
            ->select('physician_clinic.id, physician_clinic.clinic_id, physician_clinic.secretary_id, clinic.name, clinic.location')
            ->from('physician_clinic')
            ->join('clinic', 'clinic.clinic_id = physician_clinic.clinic_id')
            ->where('physician_clinic.physician_id', (int) $physicianId);
        if ($secretaryId !== null) {
            $this->db->where('physician_clinic.secretary_id', (int) $secretaryId);
        }
        return $this->db->order_by('clinic.name', 'ASC')->get()->result();
    }

    public function getClinicSpecializations($physicianId, $clinicId)
    {
        return $this->db
            ->select('specialization.special_id, specialization.special_name')
            ->from('physician_clinic')
            ->join('physician_clinic_specialization', 'physician_clinic_specialization.physician_clinic_id = physician_clinic.id')
            ->join('specialization', 'specialization.special_id = physician_clinic_specialization.special_id')
            ->where('physician_clinic.physician_id', (int) $physicianId)
            ->where('physician_clinic.clinic_id', (int) $clinicId)
            ->order_by('specialization.special_name', 'ASC')
            ->get()
            ->result();
    }

    public function validatePhysicianSchedule($physicianId, $clinicId, $day, $timeIn, $timeOut, $excludeLinkId = null)
    {
        $physicianId = (int) $physicianId;
        $clinicId = (int) $clinicId;
        $day = trim((string) $day);
        $timeIn = trim((string) $timeIn);
        $timeOut = trim((string) $timeOut);
        $excludeLinkId = $excludeLinkId !== null ? (int) $excludeLinkId : null;

        $days = $this->scheduleDays($day);
        if ($physicianId <= 0 || $clinicId <= 0 || !$days) {
            return array('success' => false, 'message' => 'Please provide a valid clinic schedule.');
        }

        $start = $this->timeToSeconds($timeIn);
        $end = $this->timeToSeconds($timeOut);
        if ($start === null || $end === null) {
            return array('success' => false, 'message' => 'Please provide valid schedule times.');
        }
        if ($end <= $start) {
            return array('success' => false, 'message' => 'Time-out must be later than time-in for the same clinic day.');
        }

        $this->db
            ->select('physician_clinic_schedule.id AS link_id, physician_clinic.clinic_id, clinic.name AS clinic_name, schedule.day, schedule.time_in, schedule.time_out')
            ->from('physician_clinic_schedule')
            ->join('physician_clinic', 'physician_clinic.id = physician_clinic_schedule.physician_clinic_id')
            ->join('clinic', 'clinic.clinic_id = physician_clinic.clinic_id')
            ->join('schedule', 'schedule.schedule_id = physician_clinic_schedule.schedule_id')
            ->where('physician_clinic.physician_id', $physicianId);

        if ($excludeLinkId !== null && $excludeLinkId > 0) {
            $this->db->where('physician_clinic_schedule.id !=', $excludeLinkId);
        }

        $existingRows = $this->db->get()->result();
        foreach ($existingRows as $row) {
            $existingDays = $this->scheduleDays($row->day);
            if (!array_intersect($days, $existingDays)) {
                continue;
            }

            $existingStart = $this->timeToSeconds($row->time_in);
            $existingEnd = $this->timeToSeconds($row->time_out);
            if ($existingStart === null || $existingEnd === null || $existingEnd <= $existingStart) {
                continue;
            }

            if ($start < $existingEnd && $end > $existingStart) {
                return array(
                    'success' => false,
                    'message' => 'This schedule overlaps an existing assignment at ' . $row->clinic_name
                        . ' (' . $this->formatScheduleDay($row->day)
                        . ', ' . date('g:i A', strtotime($row->time_in))
                        . '–' . date('g:i A', strtotime($row->time_out)) . ').',
                );
            }
        }

        return array('success' => true);
    }

    public function getPhysicianClinicConflictWeekdays($physicianId, $clinicId)
    {
        $physicianId = (int) $physicianId;
        $clinicId = (int) $clinicId;
        if ($physicianId <= 0 || $clinicId <= 0) {
            return array();
        }

        $rows = $this->db
            ->select('physician_clinic.clinic_id, clinic.name AS clinic_name, schedule.day, schedule.time_in, schedule.time_out')
            ->from('physician_clinic_schedule')
            ->join('physician_clinic', 'physician_clinic.id = physician_clinic_schedule.physician_clinic_id')
            ->join('clinic', 'clinic.clinic_id = physician_clinic.clinic_id')
            ->join('schedule', 'schedule.schedule_id = physician_clinic_schedule.schedule_id')
            ->where('physician_clinic.physician_id', $physicianId)
            ->get()
            ->result();

        $targetRows = array();
        $otherRows = array();
        foreach ($rows as $row) {
            if ((int) $row->clinic_id === $clinicId) {
                $targetRows[] = $row;
            } else {
                $otherRows[] = $row;
            }
        }

        $conflicts = array();
        foreach ($targetRows as $target) {
            $targetDays = $this->scheduleDays($target->day);
            $targetStart = $this->timeToSeconds($target->time_in);
            $targetEnd = $this->timeToSeconds($target->time_out);
            if (!$targetDays || $targetStart === null || $targetEnd === null || $targetEnd <= $targetStart) {
                continue;
            }

            foreach ($otherRows as $other) {
                $otherDays = $this->scheduleDays($other->day);
                $sharedDays = array_intersect($targetDays, $otherDays);
                if (!$sharedDays) {
                    continue;
                }

                $otherStart = $this->timeToSeconds($other->time_in);
                $otherEnd = $this->timeToSeconds($other->time_out);
                if ($otherStart === null || $otherEnd === null || $otherEnd <= $otherStart) {
                    continue;
                }

                if ($targetStart < $otherEnd && $targetEnd > $otherStart) {
                    $message = 'Physician schedule conflict with ' . $other->clinic_name
                        . ' (' . $this->formatScheduleDay($other->day)
                        . ', ' . date('g:i A', strtotime($other->time_in))
                        . '–' . date('g:i A', strtotime($other->time_out))
                        . '). Please correct the physician clinic schedules before booking.';

                    foreach ($sharedDays as $weekday) {
                        if (!isset($conflicts[(int) $weekday])) {
                            $conflicts[(int) $weekday] = $message;
                        }
                    }
                }
            }
        }

        return $conflicts;
    }

    private function scheduleDays($code)
    {
        $map = array(
            'M-F' => array(1, 2, 3, 4, 5),
            'MTW' => array(1, 2, 3),
            'TTh' => array(2, 4),
            'Sat' => array(6),
            'Sun' => array(0),
            'MWF' => array(1, 3, 5),
            'ThF' => array(4, 5),
        );

        return isset($map[$code]) ? $map[$code] : array();
    }

    private function formatScheduleDay($code)
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

    private function timeToSeconds($value)
    {
        $value = trim((string) $value);
        if ($value === '') return null;

        $timestamp = strtotime('1970-01-01 ' . $value);
        if ($timestamp === false) return null;

        return ((int) date('H', $timestamp) * 3600)
            + ((int) date('i', $timestamp) * 60)
            + (int) date('s', $timestamp);
    }
}
