<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterOrganizationModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        //$this->load->database();
        $db = \Config\Database::connect();
    }


    public function getOrganizations()
    {
        $query = $this->db->query('select distinct root_org_id, org_name from master_organization order by org_name');
        return $query->getResult();
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

    public function getOrganisationCount()
    {
        try {
            $builder = $this->db->table('master_organization');
            $builder->select('count(org_name)');
            $builder->where('status', 'Active');
            $query = $builder->get();

            // echo $org_id,json_encode($query);
            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function searchOrg($search_key)
    {
        try {
            $builder = $this->db->table('master_organization');
            $builder->select('root_org_id,org_name');
            $builder->where('(SIMILARITY(org_name,\''.$search_key.'\') > 0.1)', NULL, FALSE);
            $builder->orderBy('SIMILARITY(org_name,\'' . $search_key . '\') desc');
            $query = $builder->get();

            return $query->getResult();
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function getOrgList($limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            $builder = $this->db->table('master_organization');
            $builder->select('org_name');
            if ($search != '') {

                $builder->like('org_name', strtolower($search));
                $builder->orLike('org_name', strtoupper($search));
                $builder->orLike('org_name', ucfirst($search));
            }

            $builder->where('status','Active');
            $builder->orderBy((int) $orderBy + 1, $orderDir);
            if ($limit != -1)
                $builder->limit($limit, $offset);
            $query = $builder->get();

            // echo $org_id,json_encode($query);


            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getMonthWiseOrgOnboarding($limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            if ($search != '') {
                $likeQuery = " AND (creation_datemmyy LIKE '%" . strtolower($search) . "%' OR creation_datemmyy LIKE '%" . strtoupper($search) . "%' OR creation_datemmyy LIKE '%" . ucfirst($search) . "%' ) ";

            } else {
                $likeQuery = '';
            }
            if ($limit != -1) {
                $limitQuery = ' limit ' . $limit . ' offset ' . $offset;
            } else
                $limitQuery = '';


            $query = $this->db->query('select concat(split_part(creation_datemmyy::TEXT,\'/\', 2),\'/\' ,split_part(creation_datemmyy::TEXT,\'/\', 1)) as created_month,(count(root_org_id)) AS Month_wise_Org_Onboarded from master_organization where status=\'Active\'' . $likeQuery . ' group by created_month  order by ' . (int) $orderBy + 1 . ' ' . $orderDir . $limitQuery);

            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }


    public function getSummaryReport($limit, $offset, $search, $orderBy, $orderDir) {
        try {
            if ($search != '') {
                $andlikeQuery = " AND  (kpi LIKE '%" . strtolower($search) . "%' OR kpi LIKE '%" . strtoupper($search) . "%' OR kpi LIKE '%" . ucfirst($search) . "%' )";
                $wherelikeQuery = " WHERE  (kpi LIKE '%" . strtolower($search) . "%' OR kpi LIKE '%" . strtoupper($search) . "%' OR kpi LIKE '%" . ucfirst($search) . "%' )";
    
            } else {
                $andlikeQuery = '';
                $wherelikeQuery = '';
                
            }
            if ($limit != -1) {
                $limitQuery = ' limit ' . $limit . ' offset ' . $offset;
            } else
                $limitQuery = '';
    
            
            $query = $this->db->query
            ('SELECT * FROM (
                SELECT \'Organisations Onboarded\' AS kpi,count(org_name) FROM master_organization WHERE status = \'Active\'
                UNION SELECT \'Users Onboarded\'  AS kpi,count(*) FROM master_user 
                UNION SELECT \'Courses Published\'  AS kpi,count(course_id) FROM master_course WHERE status = \'Live\'
                UNION SELECT \'Course Providers\'  AS kpi,count(DISTINCT org_name) FROM master_course WHERE status = \'Live\'
                UNION SELECT \'Enrolment Count\'  AS kpi,count(*) FROM user_course_enrolment
                UNION SELECT \'Completion Count\'  AS kpi,count(*) FROM user_course_enrolment WHERE completion_status = \'Completed\'
                UNION SELECT \'In-Progress Count\'  AS kpi,count(*) FROM user_course_enrolment WHERE completion_status = \'In-Progress\'
                UNION SELECT \'Not Started Count\'  AS kpi,count(*) FROM user_course_enrolment WHERE completion_status = \'Not Started\'
                UNION SELECT \'Unique users enrolled\'  AS kpi,count(DISTINCT user_id) FROM user_course_enrolment 
                UNION SELECT \'Unique users completed\'  AS kpi,count(DISTINCT user_id) FROM user_course_enrolment WHERE completion_status = \'Completed\'
                UNION SELECT \'No. of MDO Admin\'  AS kpi,count(*) FROM master_user WHERE roles LIKE \'%MDO_ADMIN%\'
                UNION SELECT \'No. of Organisations having MDO Admin\'  AS kpi,count(DISTINCT root_org_id) FROM master_user WHERE roles LIKE \'%MDO_ADMIN%\'
                UNION SELECT \'No. of Organisations Enrolled for Courses\'  AS kpi,count(DISTINCT root_org_id) FROM master_user JOIN user_course_enrolment ON master_user.user_id = user_course_enrolment.user_id
                UNION SELECT \'Learning Hours\'  AS kpi,CAST(sum(durationh) AS integer)FROM master_course JOIN user_course_enrolment ON master_course.course_id = user_course_enrolment.course_id WHERE status = \'Live\' AND completion_status = \'Completed\'
                UNION SELECT \'Content Hours\'  AS kpi,CAST(sum(durationh)  AS integer)FROM master_course WHERE status = \'Live\' 
                UNION SELECT \'Courses Under Review\'  AS kpi,count(course_id) FROM master_course WHERE status = \'InReview\'
                UNION SELECT \'Courses Under Publish\'  AS kpi,count(course_id) FROM master_course WHERE status = \'Reviewed\'
                UNION SELECT \'Average Rating of Courses\'  AS kpi,ROUND(avg(avg_rating)::numeric,2) FROM master_course WHERE num_of_people_rated > 0
                UNION SELECT \'No. of Programs\'  AS kpi,count(program_id) FROM master_program WHERE program_status = \'Live\'
                UNION SELECT \'Program Duration (in hrs)\' AS kpi,CAST(sum(durationh) AS integer) FROM master_program WHERE program_status = \'Live\' 
                UNION SELECT \'Users Onboarded yesterday\'  AS kpi,count(*) FROM master_user WHERE to_date(created_date,\'DD/MM/YYYY\') = current_date - 1
                UNION SELECT \'Enrolled to Onboarding percentage\'  AS kpi,ROUND(((SELECT cast(count(DISTINCT user_course_enrolment.user_id) as double precision) FROM user_course_enrolment ) / ( SELECT cast( count(DISTINCT master_user.user_id) as double precision) FROM master_user ) * 100)::numeric,1)
                UNION SELECT \'Completion to Enrollment percentage\'  AS kpi,ROUND(((SELECT cast(count(user_course_enrolment.user_id) as double precision) FROM user_course_enrolment WHERE completion_status = \'Completed\') / (SELECT cast(count(user_course_enrolment.user_id) as double precision) FROM user_course_enrolment ) * 100)::numeric,1)
                UNION SELECT \'Not Started to Enrollment percentage\'  AS kpi,ROUND(((SELECT cast(count(user_course_enrolment.user_id) as double precision) FROM user_course_enrolment WHERE completion_status = \'Not Started\') / (SELECT cast(count(user_course_enrolment.user_id) as double precision) FROM user_course_enrolment ) * 100)::numeric,1)
                UNION SELECT \'In Progress to Enrollment percentage\'  AS kpi,ROUND(((SELECT cast(count(user_course_enrolment.user_id) as double precision) FROM user_course_enrolment WHERE completion_status = \'In-Progress\') / (SELECT cast(count(user_course_enrolment.user_id) as double precision) FROM user_course_enrolment ) * 100)::numeric,1)) a
                ORDER BY array_position(array[\'Organisations Onboarded\',\'Users Onboarded\',\'Courses Published\',\'Course Providers\',
                \'Enrolment Count\' ,\'Completion Count\',\'In-Progress Count\' ,\'Not Started Count\',\'Unique users enrolled\',\'Unique users completed\',
                    \'No. of MDO Admin\',\'No. of Organisations having MDO Admin\',\'No. of Organisations Enrolled for Courses\',\'Learning Hours\',\'Content Hours\',
                    \'Courses Under Review\',\'Courses Under Publish\',\'Average Rating of Courses\',\'No. of Programs\',\'Program Duration (in hrs)\',
                    \'Users Onboarded yesterday\',\'Enrolled to Onboarding percentage\',\'Completion to Enrollment percentage\',\'Not Started to Enrollment percentage\',
                    \'In Progress to Enrollment percentage\'], kpi::text)' . $limitQuery 
            );


            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }


}

?>