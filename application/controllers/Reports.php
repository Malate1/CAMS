<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Report_Model', 'Report_Model');
    }

    public function adminTopClinics()
    {
        $this->renderAdmin('top_clinics');
    }

    public function adminTopPurposes()
    {
        $this->renderAdmin('top_purposes');
    }

    public function adminAverage()
    {
        $this->renderAdmin('average');
    }

    public function physicianAverage()
    {
        $this->renderPhysician('average');
    }

    public function physicianDone()
    {
        $this->renderPhysician('done');
    }

    public function physicianCancelled()
    {
        $this->renderPhysician('cancelled');
    }

    public function secretaryAverage()
    {
        $this->renderSecretary('average');
    }

    public function secretaryDone()
    {
        $this->renderSecretary('done');
    }

    public function secretaryCancelled()
    {
        $this->renderSecretary('cancelled');
    }

    private function selectedMonth()
    {
        $month = $this->input->get('month', true);
        if (!$month) $month = $this->input->post('month', true);
        if (!$month) $month = $this->input->post('first', true);
        return $this->Report_Model->normalizeMonth($month);
    }

    private function renderAdmin($type)
    {
        if ($this->session->email == '' || $this->session->role !== 'Admin') {
            redirect('login');
            return;
        }

        if ($type === 'average') {
            $report = $this->Report_Model->adminMonthlyAverage($this->selectedMonth());
        } else {
            $start = $this->input->get('start', true);
            if (!$start) $start = $this->input->post('start', true);
            if (!$start) $start = $this->input->post('first', true);

            $end = $this->input->get('end', true);
            if (!$end) $end = $this->input->post('end', true);
            if (!$end) $end = $this->input->post('last', true);

            $report = $this->Report_Model->adminRangeReport($start, $end, $type);
        }

        $this->load->view('reports/admin', array(
            'report' => $report,
            'reportRoutes' => array(
                'top_clinics' => 'view-top',
                'top_purposes' => 'view-topC',
                'average' => 'view-avg',
            ),
        ));
    }

    private function renderPhysician($type)
    {
        if ($this->session->email == '' || $this->session->role !== 'Physician') {
            redirect('login-phy');
            return;
        }

        $physicianId = (int) $this->session->userdata['userid'];
        $report = $this->Report_Model->monthlyReport(
            $physicianId,
            $this->selectedMonth(),
            $type,
            null
        );

        $this->load->view('reports/monthly', array(
            'report' => $report,
            'reportRole' => 'Physician',
            'reportRoleKey' => 'physician',
            'reportHeader' => 'header/headerPhysician',
            'reportRoutes' => array(
                'average' => 'view-avg-p',
                'done' => 'view-done-p',
                'cancelled' => 'view-cancel-p',
            ),
            'reportScopeLabel' => 'Your appointments across all assigned clinics',
            'reportClinicCount' => null,
        ));
    }

    private function renderSecretary($type)
    {
        if ($this->session->email == '' || $this->session->role !== 'Secretary') {
            redirect('login-s');
            return;
        }

        $secretaryId = (int) $this->session->userdata['userid'];
        $physicianId = (int) $this->session->userdata['physician_id'];
        $clinicIds = $this->Report_Model->secretaryClinicIds($secretaryId, $physicianId);

        $report = $this->Report_Model->monthlyReport(
            $physicianId,
            $this->selectedMonth(),
            $type,
            $clinicIds
        );

        $this->load->view('reports/monthly', array(
            'report' => $report,
            'reportRole' => 'Secretary',
            'reportRoleKey' => 'secretary',
            'reportHeader' => 'header/headerSec',
            'reportRoutes' => array(
                'average' => 'view-avg-s',
                'done' => 'view-done-s',
                'cancelled' => 'view-cancel-s',
            ),
            'reportScopeLabel' => $clinicIds
                ? 'Appointments within your assigned clinic' . (count($clinicIds) === 1 ? '' : 's')
                : 'No clinics are assigned to this secretary account',
            'reportClinicCount' => count($clinicIds),
        ));
    }
}
