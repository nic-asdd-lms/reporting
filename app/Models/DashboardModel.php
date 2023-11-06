<?php

namespace App\Models;

use CodeIgniter\Model;

class DashboardModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        //$this->load->database();
        $db = \Config\Database::connect();
    }


    public function getEnrolmentPercentage()
    {
        $builder = $this->db->table('summary');
        $builder->select('count');
        $builder->where('kpi', 'Enrolled to Onboarding percentage');
        $query = $builder->get();
        return $query;
    }

    public function getCompletionPercentage()
    {
        $builder = $this->db->table('summary');
        $builder->select('count');
        $builder->where('kpi', 'Completion to Enrollment percentage');
        $query = $builder->get();
        return $query;
    }
    public function getInProgressPercentage()
    {
        $builder = $this->db->table('summary');
        $builder->select('count');
        $builder->where('kpi', 'In Progress to Enrollment percentage');
        $query = $builder->get();
        return $query;
    }
    public function getNotStartedPercentage()
    {
        $builder = $this->db->table('summary');
        $builder->select('count');
        $builder->where('kpi', 'Not Started to Enrollment percentage');
        $query = $builder->get();
        return $query;
    }

    public function getAvgRating()
    {
        $builder = $this->db->table('summary');
        $builder->select('count');
        $builder->where('kpi', 'Average Rating of Courses');
        $query = $builder->get();
        return $query;
    }


    public function getOrgName($org_id)
    {
        try {
            $builder = $this->db->table('master_organization');
            $builder->select('org_name');
            $builder->where('root_org_id', $org_id);
            $query = $builder->get();

            // echo $org_id,json_encode($query);
            return $query->getRow()->org_name;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getProgramCount()
    {
        try {
            $builder = $this->db->table('summary');
            $builder->select('count');
            $builder->where('kpi', 'No. of Programs');
            $query = $builder->get();

            // echo $org_id,json_encode($query);
            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getProgramDuration()
    {
        try {
            $builder = $this->db->table('summary');
            $builder->select('count');
            $builder->where('kpi', 'Program Duration (in hrs)');
            $query = $builder->get();

            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function getCourseCountCurrentMonth()
    {
        try {
            $builder = $this->db->table('master_course');
            $builder->select('count(*)');
            $builder->where('status', 'Live');
            $builder->where('to_char(to_date(published_date,\'DD/MM/YYYY\'), \'MONTH YYYY\')  = to_char(current_date, \'MONTH YYYY\')');


            $query = $builder->get();

            // echo $org_id,json_encode($query);


            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getUserOnboardingCountYesterday()
    {
        try {
            $builder = $this->db->table('summary');
            $builder->select('count');
            $builder->where('kpi', 'Users Onboarded yesterday');
            $query = $builder->get();
            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }


    public function getMDOAdminCount()
    {
        try {
            $builder = $this->db->table('summary');
            $builder->select('count');
            $builder->where('kpi', 'No. of MDO Admin');
            $query = $builder->get();
            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getOrganisationEnrolled()
    {
        try {
            $builder = $this->db->table('summary');
            $builder->select('count');
            $builder->where('kpi', 'No. of Organisations Enrolled for Courses');
            $query = $builder->get();
            // echo $org_id,json_encode($query);
            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }


    public function getOrganisationWithMDOAdmin()
    {
        try {
            $builder = $this->db->table('summary');
            $builder->select('count');
            $builder->where('kpi', 'No. of Organisations having MDO Admin');
            $query = $builder->get();
            // echo $org_id,json_encode($query);
            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }



}

?>