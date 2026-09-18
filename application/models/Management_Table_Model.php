<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Management_Table_Model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function users($type, array $request)
    {
        $map = array(
            'patient' => array('table' => 'patient', 'pk' => 'patient_id'),
            'physician' => array('table' => 'physician', 'pk' => 'physician_id'),
            'secretary' => array('table' => 'secretary', 'pk' => 'secretary_id'),
        );

        if (!isset($map[$type])) {
            return $this->emptyResponse($request);
        }

        $table = $map[$type]['table'];
        $pk = $map[$type]['pk'];
        $apply = function ($search) use ($table, $pk) {
            $this->db->from($table);
            if ($search !== '') {
                $this->db->group_start()
                    ->like($pk, $search)
                    ->or_like('fname', $search)
                    ->or_like('mname', $search)
                    ->or_like('lname', $search)
                    ->or_like('contact', $search)
                    ->or_like('address', $search)
                    ->or_like('email', $search)
                    ->or_like('status', $search)
                    ->group_end();
            }
        };

        return $this->paged(
            $request,
            $apply,
            $pk . ' AS id, fname, mname, lname, contact, address, email, image, status',
            array(
                'id' => $pk,
                'fname' => 'fname',
                'mname' => 'mname',
                'lname' => 'lname',
                'contact' => 'contact',
                'address' => 'address',
                'email' => 'email',
                'status' => 'status',
            ),
            $pk,
            'DESC',
            function ($row) {
                return array(
                    'id' => (int) $row->id,
                    'fname' => (string) $row->fname,
                    'mname' => (string) $row->mname,
                    'lname' => (string) $row->lname,
                    'contact' => (string) $row->contact,
                    'address' => (string) $row->address,
                    'email' => (string) $row->email,
                    'image' => (string) $row->image,
                    'status' => isset($row->status) ? (string) $row->status : '',
                );
            }
        );
    }

    public function clinics(array $request, $physicianId = null, $secretaryId = null)
    {
        $physicianId = $physicianId !== null ? (int) $physicianId : null;
        $secretaryId = $secretaryId !== null ? (int) $secretaryId : null;
        $apply = function ($search) use ($physicianId, $secretaryId) {
            $this->db->from('physician_clinic');
            $this->db->join('physician', 'physician.physician_id = physician_clinic.physician_id');
            $this->db->join('clinic', 'clinic.clinic_id = physician_clinic.clinic_id');
            $this->db->join('secretary', 'secretary.secretary_id = physician_clinic.secretary_id', 'left');
            if ($physicianId !== null) {
                $this->db->where('physician_clinic.physician_id', $physicianId);
            }
            if ($secretaryId !== null) {
                $this->db->where('physician_clinic.secretary_id', $secretaryId);
            }
            if ($search !== '') {
                $this->db->group_start()
                    ->like('clinic.clinic_id', $search)
                    ->or_like('clinic.name', $search)
                    ->or_like('clinic.contact', $search)
                    ->or_like('clinic.location', $search)
                    ->or_like('physician.fname', $search)
                    ->or_like('physician.lname', $search)
                    ->or_like('secretary.fname', $search)
                    ->or_like('secretary.lname', $search)
                    ->group_end();
            }
        };

        return $this->paged(
            $request,
            $apply,
            "physician_clinic.id AS assignment_id, clinic.clinic_id, clinic.name, clinic.contact, clinic.location, CONCAT(physician.fname, ' ', physician.lname) AS physician_name, CONCAT(COALESCE(secretary.fname, ''), ' ', COALESCE(secretary.lname, '')) AS secretary_name, (SELECT GROUP_CONCAT(DISTINCT specialization.special_name ORDER BY specialization.special_name SEPARATOR ', ') FROM physician_clinic_specialization pcs INNER JOIN specialization ON specialization.special_id = pcs.special_id WHERE pcs.physician_clinic_id = physician_clinic.id) AS specializations",
            array(
                'clinic_id' => 'clinic.clinic_id',
                'name' => 'clinic.name',
                'physician_name' => 'physician.lname',
                'secretary_name' => 'secretary.lname',
                'contact' => 'clinic.contact',
                'location' => 'clinic.location',
            ),
            'clinic.clinic_id',
            'DESC',
            function ($row) {
                return array(
                    'assignment_id' => (int) $row->assignment_id,
                    'clinic_id' => (int) $row->clinic_id,
                    'name' => (string) $row->name,
                    'physician_name' => (string) $row->physician_name,
                    'secretary_name' => trim((string) $row->secretary_name),
                    'specializations' => (string) $row->specializations,
                    'contact' => (string) $row->contact,
                    'location' => (string) $row->location,
                );
            }
        );
    }

    public function schedules($physicianId, array $request, $secretaryId = null)
    {
        $physicianId = (int) $physicianId;
        $secretaryId = $secretaryId !== null ? (int) $secretaryId : null;
        $apply = function ($search) use ($physicianId, $secretaryId) {
            $this->db->from('physician_clinic');
            $this->db->join('clinic', 'clinic.clinic_id = physician_clinic.clinic_id');
            $this->db->join('physician_clinic_schedule', 'physician_clinic_schedule.physician_clinic_id = physician_clinic.id');
            $this->db->join('schedule', 'schedule.schedule_id = physician_clinic_schedule.schedule_id');
            $this->db->where('physician_clinic.physician_id', $physicianId);
            if ($secretaryId !== null) {
                $this->db->where('physician_clinic.secretary_id', $secretaryId);
            }
            if ($search !== '') {
                $this->db->group_start()
                    ->like('schedule.schedule_id', $search)
                    ->or_like('clinic.name', $search)
                    ->or_like('schedule.day', $search)
                    ->or_like('schedule.time_in', $search)
                    ->or_like('schedule.time_out', $search)
                    ->group_end();
            }
        };

        return $this->paged(
            $request,
            $apply,
            'physician_clinic_schedule.id AS link_id, schedule.schedule_id, clinic.name AS clinic_name, schedule.day, schedule.time_in, schedule.time_out',
            array(
                'schedule_id' => 'schedule.schedule_id',
                'clinic_name' => 'clinic.name',
                'day' => 'schedule.day',
                'time_in' => 'schedule.time_in',
                'time_out' => 'schedule.time_out',
            ),
            'schedule.schedule_id',
            'DESC',
            function ($row) {
                return array(
                    'link_id' => (int) $row->link_id,
                    'schedule_id' => (int) $row->schedule_id,
                    'clinic_name' => (string) $row->clinic_name,
                    'day' => (string) $row->day,
                    'time_in' => (string) $row->time_in,
                    'time_out' => (string) $row->time_out,
                    'time_in_label' => date('h:i A', strtotime($row->time_in)),
                    'time_out_label' => date('h:i A', strtotime($row->time_out)),
                );
            }
        );
    }

    public function limits($physicianId, array $request)
    {
        $physicianId = (int) $physicianId;
        $params = $this->params($request);

        $this->db->select('COUNT(DISTINCT dateLimit) AS total_count', false)
            ->from('queuecount')
            ->where('physician_id', $physicianId);
        $totalRow = $this->db->get()->row();
        $recordsTotal = $totalRow ? (int) $totalRow->total_count : 0;

        $this->db->select('COUNT(DISTINCT dateLimit) AS total_count', false)
            ->from('queuecount')
            ->where('physician_id', $physicianId);
        $this->applyLimitSearch($params['search']);
        $filteredRow = $this->db->get()->row();
        $recordsFiltered = $filteredRow ? (int) $filteredRow->total_count : 0;

        $this->db->select('MIN(id) AS id, dateLimit, MAX(queueLimit) AS queueLimit', false)
            ->from('queuecount')
            ->where('physician_id', $physicianId);
        $this->applyLimitSearch($params['search']);
        $this->db->group_by(array('dateLimit', 'physician_id'));

        $sortMap = array(
            'id' => 'MIN(id)',
            'dateLimit' => 'dateLimit',
            'queueLimit' => 'MAX(queueLimit)',
        );
        list($orderColumn, $orderDirection) = $this->order($request, $sortMap, 'dateLimit', 'DESC');
        $rows = $this->db
            ->order_by($orderColumn, $orderDirection, false)
            ->limit($params['length'], $params['start'])
            ->get()
            ->result();

        $data = array();
        foreach ($rows as $row) {
            $data[] = array(
                'id' => (int) $row->id,
                'dateLimit' => (string) $row->dateLimit,
                'date_label' => date('M j, Y', strtotime($row->dateLimit)),
                'queueLimit' => (int) $row->queueLimit,
            );
        }

        return array(
            'draw' => $params['draw'],
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        );
    }

    public function logs(array $request, $userType = null, $userId = null)
    {
        $userType = $userType !== null ? (string) $userType : null;
        $userId = $userId !== null ? (int) $userId : null;
        $apply = function ($search) use ($userType, $userId) {
            $this->db->from('logs');
            if ($userType !== null) {
                $this->db->where('logs.usertype', $userType);
            }
            if ($userId !== null) {
                $this->db->where('logs.userid', $userId);
            }
            if ($search !== '') {
                $this->db->group_start()
                    ->like('logs.id', $search)
                    ->or_like('logs.date', $search)
                    ->or_like('logs.usertype', $search)
                    ->or_like('logs.action', $search)
                    ->group_end();
            }
        };

        return $this->paged(
            $request,
            $apply,
            'logs.id, logs.date, logs.usertype, logs.action',
            array(
                'id' => 'logs.id',
                'date' => 'logs.date',
                'usertype' => 'logs.usertype',
                'action' => 'logs.action',
            ),
            'logs.id',
            'DESC',
            function ($row) {
                return array(
                    'id' => (int) $row->id,
                    'date' => (string) $row->date,
                    'date_label' => date('m-d-Y | h:i:s A', strtotime($row->date)),
                    'usertype' => (string) $row->usertype,
                    'action' => (string) $row->action,
                );
            }
        );
    }

    private function paged(array $request, callable $apply, $select, array $sortMap, $defaultOrder, $defaultDirection, callable $formatter)
    {
        $params = $this->params($request);

        $apply('');
        $recordsTotal = (int) $this->db->count_all_results();

        $apply($params['search']);
        $recordsFiltered = (int) $this->db->count_all_results();

        $this->db->select($select, false);
        $apply($params['search']);
        list($orderColumn, $orderDirection) = $this->order($request, $sortMap, $defaultOrder, $defaultDirection);
        $rows = $this->db
            ->order_by($orderColumn, $orderDirection)
            ->limit($params['length'], $params['start'])
            ->get()
            ->result();

        $data = array();
        foreach ($rows as $row) {
            $data[] = $formatter($row);
        }

        return array(
            'draw' => $params['draw'],
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        );
    }

    private function params(array $request)
    {
        $length = isset($request['length']) ? (int) $request['length'] : 10;
        if ($length < 1 || $length > 100) {
            $length = 10;
        }

        return array(
            'draw' => isset($request['draw']) ? (int) $request['draw'] : 0,
            'start' => isset($request['start']) ? max(0, (int) $request['start']) : 0,
            'length' => $length,
            'search' => isset($request['search']['value']) ? trim((string) $request['search']['value']) : '',
        );
    }

    private function order(array $request, array $sortMap, $defaultColumn, $defaultDirection)
    {
        $column = $defaultColumn;
        $direction = strtoupper($defaultDirection) === 'ASC' ? 'ASC' : 'DESC';

        if (!empty($request['order'][0])) {
            $index = (int) $request['order'][0]['column'];
            $requestedDirection = strtolower((string) $request['order'][0]['dir']) === 'asc' ? 'ASC' : 'DESC';
            $name = isset($request['columns'][$index]['data']) ? (string) $request['columns'][$index]['data'] : '';
            if (isset($sortMap[$name])) {
                $column = $sortMap[$name];
                $direction = $requestedDirection;
            }
        }

        return array($column, $direction);
    }

    private function applyLimitSearch($search)
    {
        if ($search === '') {
            return;
        }

        $this->db->group_start()
            ->like('dateLimit', $search)
            ->or_like('queueLimit', $search)
            ->group_end();
    }

    private function emptyResponse(array $request)
    {
        $params = $this->params($request);
        return array(
            'draw' => $params['draw'],
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => array(),
        );
    }
}
