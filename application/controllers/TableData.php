<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TableData extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Management_Table_Model', 'ManagementTable');
        $this->load->model('ClinicAssignment_Model', 'ClinicAssignmentModel');
        $this->ClinicAssignmentModel->ensureSchema();
        $this->load->helper('url');
    }

    public function fetch($dataset = '')
    {
        $dataset = strtolower(trim((string) $dataset));
        $request = $this->input->get(NULL, true);
        if (!is_array($request)) {
            $request = array();
        }

        $role = (string) $this->session->userdata('role');
        $userId = (int) $this->session->userdata('userid');
        $physicianId = (int) $this->session->userdata('physician_id');

        $adminDatasets = array('admin-patients', 'admin-physicians', 'admin-secretaries', 'admin-clinics', 'admin-logs');
        $physicianDatasets = array('physician-clinics', 'physician-schedules', 'physician-limits', 'physician-logs');
        $secretaryDatasets = array('secretary-clinics', 'secretary-schedules', 'secretary-limits', 'secretary-logs');

        if (in_array($dataset, $adminDatasets, true) && $role !== 'Admin') {
            return $this->forbidden();
        }
        if (in_array($dataset, $physicianDatasets, true) && $role !== 'Physician') {
            return $this->forbidden();
        }
        if (in_array($dataset, $secretaryDatasets, true) && $role !== 'Secretary') {
            return $this->forbidden();
        }

        switch ($dataset) {
            case 'admin-patients':
                $payload = $this->ManagementTable->users('patient', $request);
                break;
            case 'admin-physicians':
                $payload = $this->ManagementTable->users('physician', $request);
                break;
            case 'admin-secretaries':
                $payload = $this->ManagementTable->users('secretary', $request);
                break;
            case 'admin-clinics':
                $payload = $this->ManagementTable->clinics($request);
                break;
            case 'admin-logs':
                $payload = $this->ManagementTable->logs($request);
                break;
            case 'physician-clinics':
                $payload = $this->ManagementTable->clinics($request, $userId);
                break;
            case 'physician-schedules':
                $payload = $this->ManagementTable->schedules($userId, $request);
                break;
            case 'physician-limits':
                $payload = $this->ManagementTable->limits($userId, $request);
                break;
            case 'physician-logs':
                $payload = $this->ManagementTable->logs($request, 'Physician', $userId);
                break;
            case 'secretary-clinics':
                $payload = $this->ManagementTable->clinics($request, $physicianId, $userId);
                break;
            case 'secretary-schedules':
                $payload = $this->ManagementTable->schedules($physicianId, $request, $userId);
                break;
            case 'secretary-limits':
                $payload = $this->ManagementTable->limits($physicianId, $request);
                break;
            case 'secretary-logs':
                $payload = $this->ManagementTable->logs($request, 'Secretary', $userId);
                break;
            default:
                return $this->output
                    ->set_status_header(404)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Unknown data source.')));
        }

        $payload['csrf'] = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash(),
        );

        return $this->output
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($payload));
    }

    private function forbidden()
    {
        return $this->output
            ->set_status_header(403)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(array('message' => 'You are not allowed to view this data.')));
    }
}
