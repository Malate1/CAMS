<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AccountTools extends CI_Controller
{
    private function roleConfig()
    {
        $role = (string) $this->session->userdata('role');

        $map = array(
            'Admin' => array(
                'table' => 'admin',
                'id_column' => 'admin_id',
                'profile_action' => 'Admin/ProfileUpdate',
                'photo_action' => 'Admin/ProfilePicUpdate',
                'password_action' => 'Admin/Change_pass',
                'login_route' => 'login-a',
            ),
            'Patient' => array(
                'table' => 'patient',
                'id_column' => 'patient_id',
                'profile_action' => 'Patient/ProfileUpdate',
                'photo_action' => 'Patient/ProfilePicUpdate',
                'password_action' => 'Patient/Change_pass',
                'login_route' => 'login-p',
            ),
            'Physician' => array(
                'table' => 'physician',
                'id_column' => 'physician_id',
                'profile_action' => 'Physician/ProfileUpdate',
                'photo_action' => 'Physician/ProfilePicUpdate',
                'password_action' => 'Physician/Change_pass',
                'login_route' => 'login-phy',
            ),
            'Secretary' => array(
                'table' => 'secretary',
                'id_column' => 'secretary_id',
                'profile_action' => 'Secretary/ProfileUpdate',
                'photo_action' => 'Secretary/ProfilePicUpdate',
                'password_action' => 'Secretary/Change_pass',
                'login_route' => 'login-s',
            ),
        );

        return isset($map[$role]) ? $map[$role] : null;
    }

    private function requireAuthenticatedRole()
    {
        $config = $this->roleConfig();
        if (!$config || !$this->session->userdata('userid') || !$this->session->userdata('email')) {
            if ($config && $config['login_route']) {
                redirect($config['login_route']);
            } else {
                redirect('');
            }
            return null;
        }

        return $config;
    }

    public function profile()
    {
        $config = $this->requireAuthenticatedRole();
        if (!$config) return;

        $userId = (int) $this->session->userdata('userid');
        $record = $this->db
            ->get_where($config['table'], array($config['id_column'] => $userId))
            ->row();

        if (!$record) {
            show_error('Account profile could not be found.', 404);
            return;
        }

        $this->load->view('account_tools/profile_modal', array(
            'record' => $record,
            'role' => (string) $this->session->userdata('role'),
            'profileAction' => base_url($config['profile_action']),
            'photoAction' => base_url($config['photo_action']),
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ));
    }

    public function password()
    {
        $config = $this->requireAuthenticatedRole();
        if (!$config) return;

        $this->load->view('account_tools/password_modal', array(
            'role' => (string) $this->session->userdata('role'),
            'passwordAction' => base_url($config['password_action']),
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ));
    }
}
