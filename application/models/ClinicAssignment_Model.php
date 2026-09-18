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

        if ($createdScheduleTable || $createdSpecializationTable) {
            $this->backfillLegacyAssignments();
        }
    }

    public function backfillLegacyAssignments()
    {
        $this->db->query("INSERT IGNORE INTO physician_clinic_schedule (physician_clinic_id, schedule_id)
            SELECT pc.id, ps.schedule_id
            FROM physician_clinic pc
            INNER JOIN physician_sched ps ON ps.physician_id = pc.physician_id");

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
}
