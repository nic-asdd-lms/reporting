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

       $query = $this->db->query('select course_id, course_name from master_course order by course_name');
       return $query->getResult();
    }

    

}
?>