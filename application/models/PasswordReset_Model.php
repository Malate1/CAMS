<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PasswordReset_Model extends CI_Model
{
    private $accounts = array(
        'patient' => array('table' => 'patient', 'id' => 'patient_id'),
        'physician' => array('table' => 'physician', 'id' => 'physician_id'),
        'secretary' => array('table' => 'secretary', 'id' => 'secretary_id'),
    );

    public function ensureSchema()
    {
        $this->load->dbforge();

        foreach ($this->accounts as $config) {
            if (!$this->db->field_exists('must_change_password', $config['table'])) {
                $this->dbforge->add_column($config['table'], array(
                    'must_change_password' => array(
                        'type' => 'TINYINT',
                        'constraint' => 1,
                        'unsigned' => true,
                        'default' => 0,
                        'null' => false,
                        'after' => 'password',
                    ),
                ));
            }
        }
    }

    public function setRequired($type, $id, $required)
    {
        $type = strtolower((string) $type);
        if (!isset($this->accounts[$type])) return false;

        $config = $this->accounts[$type];
        return $this->db
            ->where($config['id'], (int) $id)
            ->update($config['table'], array(
                'must_change_password' => $required ? 1 : 0,
            ));
    }
}
