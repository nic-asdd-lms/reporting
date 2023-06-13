<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterCourseModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        //$this->load->database();
        $db = \Config\Database::connect();
    }

    public function getCourse()
    {
        try {
            $builder = $this->db->table('master_course');
            $builder->select('course_id,  course_name');
            $builder->where('status', 'Live');
            $builder->orderBy('course_name');
            $query = $builder->get();

            return $query->getResult();
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
    public function getCourseCount()
    {
        try {
            $builder = $this->db->table('master_course');
            $builder->select('count(course_id)');
            $builder->where('status', 'Live');
            $query = $builder->get();

            // echo $org_id,json_encode($query);
            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
    public function getProviderCount()
    {
        try {
            $builder = $this->db->table('master_course');
            $builder->select('count(distinct org_name)');
            $builder->where('status', 'Live');
            $query = $builder->get();

            // echo $org_id,json_encode($query);
            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getContentHours()
    {
        try {
            $builder = $this->db->table('master_course');
            $builder->select('sum(durationh)');
            $builder->where('status', 'Live');
            $query = $builder->get();

            // echo $org_id,json_encode($query);
            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
    public function getLearningHours()
    {
        try {
            $builder = $this->db->table('master_course');
            $builder->join('user_course_enrolment','user_course_enrolment.course_id = master_course.course_id');
            $builder->select('sum(durationh)');
            $builder->where('status', 'Live');
            $builder->where('completion_status', 'Completed');
            $query = $builder->get();

            // echo $org_id,json_encode($query);
            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
    public function getCourseName($course_id)
    {
        try {
            $builder = $this->db->table('master_course');
            $builder->select('course_name');
            $builder->where('course_id', $course_id);
            $query = $builder->get();

            return $query->getRow()->course_name;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        //$result = $this->db->query('select  course_name from master_course where course_id = \''.$course_id.'\'')->getRow()->course_name;
        //    return $result;
    }

    public function getLiveCourseList($limit, $offset, $search, $orderBy, $orderDir)
    {
        try {

            $builder = $this->db->table('master_course');
            $builder->select('course_name');
            $builder->where('status', 'Live');
            if ($search != '')
                $builder->where("(course_name LIKE '%" . strtolower($search) . "%' OR course_name LIKE '%" . strtolower($search) . "%' OR course_name LIKE '%" . ucfirst($search) . "%' )", NULL, FALSE);

            $builder->orderBy((int) $orderBy + 1, $orderDir);

            if ($limit != -1)
                $builder->limit($limit, $offset);

            $query = $builder->get();

            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }
    public function getLiveCourses($limit, $offset, $search, $orderBy, $orderDir)
    {
        try {

            $builder = $this->db->table('master_course');
            $builder->select(' course_name,org_name, durationhms, published_date, num_of_people_rated, avg_rating');
            $builder->where('status', 'Live');
            if ($search != '')
                $builder->where("(course_name LIKE '%" . strtolower($search) . "%' OR course_name LIKE '%" . strtolower($search) . "%' OR course_name LIKE '%" . ucfirst($search) . "%' 
                            OR org_name LIKE '%" . strtolower($search) . "%' OR org_name LIKE '%" . strtoupper($search) . "%' OR org_name LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);

            $builder->orderBy((int) $orderBy + 1, $orderDir);

            if ($limit != -1)
                $builder->limit($limit, $offset);

            $query = $builder->get();

            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function getCoursesUnderPublish($limit, $offset, $search, $orderBy, $orderDir)
    {
        try {


            $builder = $this->db->table('master_course');
            $builder->select('  course_name,org_name');
            $builder->where('status', 'Reviewed');
            if ($search != '')
                $builder->where("(course_name LIKE '%" . strtolower($search) . "%' OR course_name LIKE '%" . strtolower($search) . "%' OR course_name LIKE '%" . ucfirst($search) . "%' 
                            OR org_name LIKE '%" . strtolower($search) . "%' OR org_name LIKE '%" . strtoupper($search) . "%' OR org_name LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);

            $builder->orderBy((int) $orderBy + 1, $orderDir);

            if ($limit != -1)
                $builder->limit($limit, $offset);

            $query = $builder->get();

            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }
    public function getCoursesUnderReview($limit, $offset, $search, $orderBy, $orderDir)
    {
        try {

            $builder = $this->db->table('master_course');
            $builder->select('  course_name,org_name');
            $builder->where('status', 'InReview');
            if ($search != '')
                $builder->where("(course_name LIKE '%" . strtolower($search) . "%' OR course_name LIKE '%" . strtolower($search) . "%' OR course_name LIKE '%" . ucfirst($search) . "%' 
                            OR org_name LIKE '%" . strtolower($search) . "%' OR org_name LIKE '%" . strtoupper($search) . "%' OR org_name LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);

            $builder->orderBy((int) $orderBy + 1, $orderDir);

            if ($limit != -1)
                $builder->limit($limit, $offset);

            $query = $builder->get();

            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }
    public function getDraftCourses($limit, $offset, $search, $orderBy, $orderDir)
    {
        try {

            $builder = $this->db->table('master_course');
            $builder->select('course_name,org_name');
            $builder->where('status', 'Draft');
            if ($search != '')
                $builder->where("(course_name LIKE '%" . strtolower($search) . "%' OR course_name LIKE '%" . strtolower($search) . "%' OR course_name LIKE '%" . ucfirst($search) . "%' 
                            OR org_name LIKE '%" . strtolower($search) . "%' OR org_name LIKE '%" . strtoupper($search) . "%' OR org_name LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);

            $builder->orderBy((int) $orderBy + 1, $orderDir);

            if ($limit != -1)
                $builder->limit($limit, $offset);

            
            $query = $builder->get();

            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }


    public function getMonthWiseCourses($limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            if ($search != '') {
                $likeQuery = " AND (publishedmmyy LIKE '%" . strtolower($search) . "%' OR publishedmmyy LIKE '%" . strtoupper($search) . "%' OR publishedmmyy LIKE '%" . ucfirst($search) . "%' ) ";

            } else {
                $likeQuery = '';
            }
            if ($limit != -1) {
                $limitQuery = ' limit ' . $limit . ' offset ' . $offset;
            } else
                $limitQuery = '';


            $query = $this->db->query('	select concat(split_part(publishedmmyy::TEXT,\'/\', 2),\'/\', split_part(publishedmmyy::TEXT,\'/\', 1) ) as published_month, count(*) as Live_course 
            from master_course 
            where status=\'Live\' ' . $likeQuery . '
            group by published_month  
             order by ' . (int) $orderBy + 1 . ' ' . $orderDir . $limitQuery);
            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function getCourseCountByCBPProvider($limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            $table = new \CodeIgniter\View\Table();

            $builder = $this->db->table('master_course');
            $builder->select(' org_name, count(*)');
            $builder->where('status', 'Live');
            $builder->groupBy('org_name');
            if ($search != '') {

                $builder->like('org_name', strtolower($search));
                $builder->orLike('org_name', strtoupper($search));
                $builder->orLike('org_name', ucfirst($search));
            }

            $builder->orderBy((int) $orderBy + 1, $orderDir);
            if ($limit != -1)
                $builder->limit($limit, $offset);
            $query = $builder->get();
            // print_r($builder);
            return $query;

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function courseSearch($search_key) {
        try {
            $builder = $this->db->table('master_course');
        $builder->select('course_id,  course_name,org_name');
        $builder->where('(SIMILARITY(course_name,\''.$search_key.'\') > 0.1)', NULL, FALSE);
        $builder->where('status', 'Live');
        $builder->orderBy('SIMILARITY(course_name,\''.$search_key.'\') desc');
        $query = $builder->get();
        
        // $result = $this->db->query('SELECT org_name FROM master_organization WHERE SIMILARITY(org_name,\''.$search_key.'\') > 0.4 ;');
        // echo $search_key,json_encode($query);
        return $query->getResult();
        }
        catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        } 
        
    }

    public function getTopCbpLiveCourses($topCount,$limit, $offset, $search, $orderBy, $orderDir)
    {
        try {

            $builder = $this->db->table('master_course');
            $builder->select('org_name, count(distinct course_id) as course_count' );
            $builder->where('status', 'Live');
            $builder->groupBy('org_name');
            if ($search != '') {

                $builder->like('org_name', strtolower($search));
                $builder->orLike('org_name', strtoupper($search));
                $builder->orLike('org_name', ucfirst($search));
            }

            $builder->orderBy('course_count' ,'desc');
            if ($limit != -1)
            $builder->limit(min($topCount-$offset,$limit), $offset);
        else
            $builder->limit($topCount-$offset, $offset);

        $query = $builder->get();
            // print_r($builder);
            return $query;

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getTopCbpUnderPublish($topCount,$limit, $offset, $search, $orderBy, $orderDir)
    {
        try {

            $builder = $this->db->table('master_course');
            $builder->select('org_name, count(distinct course_id) as course_count' );
            $builder->where('status', 'Reviewed');
            $builder->groupBy('org_name');
            if ($search != '') {

                $builder->like('org_name', strtolower($search));
                $builder->orLike('org_name', strtoupper($search));
                $builder->orLike('org_name', ucfirst($search));
            }

            $builder->orderBy('course_count' ,'desc');
            if ($limit != -1)
            $builder->limit(min($topCount-$offset,$limit), $offset);
        else
            $builder->limit($topCount-$offset, $offset);

        $query = $builder->get();
            // print_r($builder);
            return $query;

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getTopCbpUnderReview($topCount,$limit, $offset, $search, $orderBy, $orderDir)
    {
        try {

            $builder = $this->db->table('master_course');
            $builder->select('org_name, count(distinct course_id) as course_count' );
            $builder->where('status', 'InReview');
            $builder->groupBy('org_name');
            if ($search != '') {

                $builder->like('org_name', strtolower($search));
                $builder->orLike('org_name', strtoupper($search));
                $builder->orLike('org_name', ucfirst($search));
            }

            $builder->orderBy('course_count' ,'desc');
            if ($limit != -1)
            $builder->limit(min($topCount-$offset,$limit), $offset);
        else
            $builder->limit($topCount-$offset, $offset);

        $query = $builder->get();
            // print_r($builder);
            return $query;

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getTopCbpDraftCourses($topCount,$limit, $offset, $search, $orderBy, $orderDir)
    {
        try {

            $builder = $this->db->table('master_course');
            $builder->select('org_name, count(distinct course_id) as course_count' );
            $builder->where('status', 'Draft');
            $builder->groupBy('org_name');
            if ($search != '') {

                $builder->like('org_name', strtolower($search));
                $builder->orLike('org_name', strtoupper($search));
                $builder->orLike('org_name', ucfirst($search));
            }

            $builder->orderBy('course_count' ,'desc');
            if ($limit != -1)
            $builder->limit(min($topCount-$offset,$limit), $offset);
        else
            $builder->limit($topCount-$offset, $offset);

        $query = $builder->get();
            // print_r($builder);
            return $query;

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getTopCourseRating($topCount, $limit, $offset, $search, $orderBy, $orderDir)
    {
        $builder = $this->db->table('master_course');
        $builder->select(' course_name, avg_rating, total_rating');

        if ($search != '') {

            $builder->like('master_course.course_name', strtolower($search));
            $builder->orLike('master_course.course_name', strtoupper($search));
            $builder->orLike('master_course.course_name', ucfirst($search));
        }

        $builder->orderBy('avg_rating, total_rating', 'desc');
        if ($limit != -1)
            $builder->limit(min($topCount-$offset,$limit), $offset);
        else
            $builder->limit($topCount-$offset, $offset);

        $query = $builder->get();

        return $query;

    }

    public function dashboardChart($isMonthWise)
    {
        $builder = $this->db->table('master_course');
        
        $builder->select('status,count(*) as count');

        
        if ($isMonthWise == true)
            $builder->where('to_char(to_date(created_date,\'DD/MM/YYYY\'), \'MONTH YYYY\')  = to_char(current_date, \'MONTH YYYY\')');

        $builder->groupBy('status');
        $builder->orderBy('count', 'desc');

        return $builder->get();

    }

    public function dashboardTable($isMonthWise)
    {
        $builder = $this->db->table('master_course');
        
        
        $builder->select('status,count(*) as count');
        
        if ($isMonthWise == true) {
            $builder->where('to_char(to_date(created_date,\'DD/MM/YYYY\'), \'MONTH YYYY\')  = to_char(current_date, \'MONTH YYYY\')');
        }

        $builder->groupBy('status');
        $builder->orderBy('count', 'desc');

        // echo '<pre>';
        // print_r($builder);
        // die;

        return $builder->get();

    }

    public function getMonthWiseCoursePublished()
    {
        try {
            $builder = $this->db->table('master_course');
            $builder->select('to_char(date_trunc(\'MONTH\',to_date(published_date,\'DD/MM/YYYY\')),\'YYYY/MM\') as publish_month, count(*)');
            $builder->where('status','Live');
            $builder->groupBy('publish_month');
            $builder->orderBy('publish_month');
            $query = $builder->get();

            return $query;
            
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function getMonthWiseTotalCoursePublished()
    {
        try {
            $builder = $this->db->table('master_course');
            $builder->select('distinct to_char(date_trunc(\'month\',to_date(published_date,\'DD/MM/YYYY\')),\'YYYY/MM\') as publish_month, 
            sum(count(*) ) over (order by to_char(date_trunc(\'month\',to_date(published_date,\'DD/MM/YYYY\')),\'YYYY/MM\'))');
            $builder->where('status','Live');
            $builder->groupBy('publish_month');
            $builder->orderBy('publish_month');
            $query = $builder->get();

            return $query;
            
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }
}

?>