<?php

namespace App\Models;

use CodeIgniter\Model;

class UserEnrolmentCourse extends Model
{
    public function __construct() {
        parent::__construct();
        $db = \Config\Database::connect();
    }

    public function getCourseWiseEnrolmentReport($course) {
        $table = new \CodeIgniter\View\Table();
       $query = $this->db->query('SELECT concat(first_name,\' \',last_name) as name, email_id, org_name, designation, status, completion_percentage, completed_on
           FROM public.master_user, public.user_enrolment_course
           where master_user.user_id = user_enrolment_course.user_id 
           AND user_enrolment_course.course_id = \''.$course.'\'
           order by name');

           $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Status', 'Completion Percentage', 'Completed On');

           return $table->generate($query);
       //return $query->getResult();
    }

    

}
?>