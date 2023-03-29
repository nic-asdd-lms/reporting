<?php

namespace App\Models;

use CodeIgniter\Model;

class UserEnrolmentCourse extends Model
{
    public function __construct() {
        parent::__construct();
        $db = \Config\Database::connect();
    }

    public function getCourseWiseEnrolmentReport() {

       $query = $this->db->query('SELECT distinct concat(first_name,\' \',last_name) as name, designation,org_name,email_id, status,
       completion_percentage,completed_on
           FROM public.master_user, public.user_enrolment_course
           where master_user.user_id= user_enrolment_course.user_id 
           order by name');
       return $query->getResult();
    }

    

}
?>