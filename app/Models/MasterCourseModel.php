<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterCourseModel extends Model
{
    public function __construct() {
        parent::__construct();
        //$this->load->database();
        $db = \Config\Database::connect();
    }

    public function getCourse() {
        try {
            $builder = $this->db->table('master_course');
            $builder->select('course_id, course_name');
            $builder->where('status','Live');
            $builder->orderBy('course_name');
            $query = $builder->get();
        
           return $query->getResult();
        }
        catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        } 
        
    }

    public function getCourseName($course_id) {
        try {
            $builder = $this->db->table('master_course');
            $builder->select('course_name');
            $builder->where('course_id',$course_id);
            $query = $builder->get();
        
            return $query->getRow()->course_name;
        }
        catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        } 
        
        //$result = $this->db->query('select  course_name from master_course where course_id = \''.$course_id.'\'')->getRow()->course_name;
    //    return $result;
    }

    public function getMonthWiseCourses($limit, $offset, $search, $orderBy, $orderDir) {
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
            where status=\'Live\' '.$likeQuery.'
            group by published_month  
             order by '. (int) $orderBy+1 . ' '.$orderDir.$limitQuery);
            return $query;
        }
        catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        } 
        
    }

    
    

}

?>