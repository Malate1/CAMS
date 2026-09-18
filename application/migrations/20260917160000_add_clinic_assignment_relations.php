<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_clinic_assignment_relations extends CI_Migration
{
    public function up()
    {
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
        }

        $this->db->query("INSERT IGNORE INTO physician_clinic_schedule (physician_clinic_id, schedule_id)
            SELECT pc.id, ps.schedule_id
            FROM physician_clinic pc
            INNER JOIN physician_sched ps ON ps.physician_id = pc.physician_id");

        $this->db->query("INSERT IGNORE INTO physician_clinic_specialization (physician_clinic_id, special_id)
            SELECT pc.id, ps.special_id
            FROM physician_clinic pc
            INNER JOIN physician_special ps ON ps.physician_id = pc.physician_id");
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS physician_clinic_schedule');
        $this->db->query('DROP TABLE IF EXISTS physician_clinic_specialization');
    }
}
