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
    

}
?>