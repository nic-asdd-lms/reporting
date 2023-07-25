<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseCompetencyModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        //$this->load->database();
        $db = \Config\Database::connect();
    }

    public function getCompetencySummary($limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            $builder = $this->db->table('course_competency_collection');
            $builder->join('master_course', 'master_course.course_id = course_competency_collection.course_id');
            $builder->select('competency_name, competency_type ,count(distinct course_competency_collection.course_id)');
            $builder->where('status', 'Live');
            if ($search != '')
                $builder->where("(competency_name LIKE '%" . strtolower($search) . "%' OR competency_name LIKE '%" . strtoupper($search) . "%' OR competency_name LIKE '%" . ucfirst($search) . "%' 
                            OR competency_type LIKE '%" . strtolower($search) . "%' OR competency_type LIKE '%" . strtoupper($search) . "%' OR competency_type LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);


            $builder->groupBy('competency_name, competency_type');
            $builder->orderBy((int) $orderBy + 1, $orderDir);

            if ($limit != -1)
                $builder->limit($limit, $offset);
                $query = $builder->get();

            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function getTopCompetencies($competencytype,$topCount,$limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            $builder = $this->db->table('course_competency_collection');
            $builder->join('master_course', 'master_course.course_id = course_competency_collection.course_id');
            $builder->select('competency_name, count(distinct course_competency_collection.course_id)');
            $builder->where('status', 'Live');

            if($competencytype != 'All')
                $builder->where('competency_type',$competencytype);
                
            if ($search != '')
                $builder->where("(competency_name LIKE '%" . strtolower($search) . "%' OR competency_name LIKE '%" . strtoupper($search) . "%' OR competency_name LIKE '%" . ucfirst($search) . "%' )", NULL, FALSE);


            $builder->groupBy('competency_name');
            $builder->orderBy('count','desc');

            if ($limit != -1)
                $builder->limit(min($topCount - $offset, $limit), $offset);
            else
                $builder->limit($topCount - $offset, $offset);
                 
            $query = $builder->get();

            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function getTopCoursesCompetencyWise($topCount,$limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            $builder = $this->db->table('course_competency_collection');
            $builder->join('master_course', 'master_course.course_id = course_competency_collection.course_id');
            $builder->select('course_competency_collection.course_name ,count(distinct competency_name)');
            $builder->where('status', 'Live');
            if ($search != '')
                $builder->where("(course_competency_collection.course_name LIKE '%" . strtolower($search) . "%' OR course_competency_collection.course_name LIKE '%" . strtoupper($search) . "%' OR course_competency_collection.course_name LIKE '%" . ucfirst($search) . "%' )", NULL, FALSE);


            $builder->groupBy('course_competency_collection.course_name');
            $builder->orderBy('count','desc');

            if ($limit != -1)
                $builder->limit(min($topCount - $offset, $limit), $offset);
            else
                $builder->limit($topCount - $offset, $offset);
                
            $query = $builder->get();

            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }




}
?>