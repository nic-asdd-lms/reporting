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
        $query = $this->db->query('SELECT ROUND(((SELECT cast(count(DISTINCT user_course_enrolment.user_id) as double precision) FROM user_course_enrolment ) / ( SELECT cast( count(DISTINCT master_user.user_id) as double precision) FROM master_user ) * 100)::numeric,1)');
        return $query;
    }

    public function getCompletionPercentage()
    {
        $query = $this->db->query('SELECT ROUND(((SELECT cast(count( user_course_enrolment.user_id) as double precision) FROM user_course_enrolment WHERE completion_status = \'Completed\') / ( SELECT cast( count( user_course_enrolment.user_id) as double precision) FROM user_course_enrolment ) * 100)::numeric,1)');
        return $query;
    }
    public function getInProgressPercentage()
    {
        $query = $this->db->query('SELECT ROUND(((SELECT cast(count( user_course_enrolment.user_id) as double precision) FROM user_course_enrolment WHERE completion_status = \'In-Progress\') / ( SELECT cast( count( user_course_enrolment.user_id) as double precision) FROM user_course_enrolment ) * 100)::numeric,1)');
        return $query;
    }
    public function getNotStartedPercentage()
    {
        $query = $this->db->query('SELECT ROUND(((SELECT cast(count( user_course_enrolment.user_id) as double precision) FROM user_course_enrolment WHERE completion_status = \'Not Started\') / ( SELECT cast( count( user_course_enrolment.user_id) as double precision) FROM user_course_enrolment ) * 100)::numeric,1)');
        return $query;
    }

    public function getAvgRating()
    {
        $query = $this->db->query('SELECT ROUND(avg(avg_rating)::numeric,2) FROM master_course WHERE num_of_people_rated > 0');
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
            $builder = $this->db->table('master_program');
            $builder->select('count(*)');
            $builder->where('program_status', 'Live');
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
            $builder = $this->db->table('master_program');
            $builder->select('CAST(sum(durationh) AS integer)');
            $builder->where('program_status', 'Live');
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
            $builder->where('status','Live');
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
            $builder = $this->db->table('master_user');
            $builder->select('count(*)');
            $builder->where('to_date(created_date,\'DD/MM/YYYY\')  = current_date-1');
            
            $query = $builder->get();

            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    
    public function getMDOAdminCount() {
        try {
            $builder = $this->db->table('master_user');
            $builder->select('count(*)');
            $builder->like('roles','MDO_ADMIN');
            
            $query = $builder->get();

            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getOrganisationEnrolled()
    {
        try {
            $builder = $this->db->table('master_user');
            $builder->join('user_course_enrolment','master_user.user_id = user_course_enrolment.user_id');
            $builder->select('count(DISTINCT root_org_id)');
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
            $builder = $this->db->table('master_user');
            $builder->select('count(DISTINCT root_org_id)');
            $builder->like('roles', 'MDO_ADMIN');
            $query = $builder->get();

            // echo $org_id,json_encode($query);
            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }



}

?>