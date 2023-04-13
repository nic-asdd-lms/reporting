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
        $builder = $this->db->table('master_course');
        $builder->select('course_id, course_name');
        $builder->orderBy('course_name');
        $query = $builder->get();
    
       return $query->getResult();
    }

    public function getCourseName($course_id) {
        $builder = $this->db->table('master_course');
        $builder->select('course_name');
        $builder->where('course_id',$course_id);
        $query = $builder->get();
    
        return $query->getRow()->course_name;
        //$result = $this->db->query('select  course_name from master_course where course_id = \''.$course_id.'\'')->getRow()->course_name;
    //    return $result;
    }

    public function getMonthWiseCourses() {
        $table = new \CodeIgniter\View\Table();

        $query = $this->db->query('select split_part(publishedmmyy::TEXT,\'/\', 1) AS Month,  split_part(publishedmmyy::TEXT,\'/\', 2) AS YEAR ,count(*) as Live_course from master_course where status=\'Live\' group by publishedmmyy  order by YEAR,MONTH');
        
        $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable " style="width:90%">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Month', 'Year','Courses Published');

           return $table->generate($query);
    }

    public function getMonthWiseCoursesExcel() {
        $table = new \CodeIgniter\View\Table();

        $query = $this->db->query('select split_part(publishedmmyy::TEXT,\'/\', 1) AS Month,  split_part(publishedmmyy::TEXT,\'/\', 2) AS YEAR ,count(*) as Live_course from master_course where status=\'Live\' group by publishedmmyy  order by YEAR,MONTH');
        
        return $query->getResultArray();

    }
    

}
?>