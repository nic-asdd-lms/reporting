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

        $builder = $this->db->table('user_enrolment_course');
        $builder->select('concat(first_name,\' \',last_name) as name, email_id, org_name, designation, status, completion_percentage, completed_on');
        $builder->join('master_user', 'master_user.user_id = user_enrolment_course.user_id ');
        $builder->where('course_id', $course);
        $query = $builder->get();
    
           $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Status', 'Completion Percentage', 'Completed On');

           return $table->generate($query);
       //return $query->getResult();
    }

    public function getCourseWiseEnrolmentCount($course) {
        $table = new \CodeIgniter\View\Table();
       $query = $this->db->query('SELECT course_name, published_date, duration_h,COUNT(*) AS enrolled_count
      ,SUM(CASE WHEN user_enrolment_course.status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count
  FROM user_enrolment_course, master_course
  WHERE user_enrolment_course.course_id = master_course.course_id
  GROUP BY course_name,published_date, duration_h
  ORDER BY completed_count desc');

           $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Course Name', 'Published Date', 'Duration', 'Enrollment Count', 'Completion Count');

           return $table->generate($query);
       //return $query->getResult();
    }

    public function getProgramWiseEnrolmentReport($course) {
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

    public function getProgramWiseEnrolmentCount($course) {
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

    public function getCollectionWiseEnrolmentReport($course) {
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

    public function getCollectionWiseEnrolmentCount($course) {
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


    public function getEnrolmentByOrg($org) {
        $table = new \CodeIgniter\View\Table();
        $builder = $this->db->table('user_enrolment_course');
        $builder->select('concat(first_name,\' \',last_name) as name, email_id, org_name, designation, status, completion_percentage, completed_on');
        $builder->join('master_user', 'master_user.user_id = user_enrolment_course.user_id ');
        $builder->where('org_name', $org);
        $query = $builder->get();
           $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Status', 'Completion Percentage', 'Completed On');

           return $table->generate($query);
    }


    public function getUserEnrolmentCountByMDO($org) {
        $table = new \CodeIgniter\View\Table();
        $query = $this->db->query('SELECT concat(first_name,\' \',last_name) as name, email_id, org_name, designation,COUNT(*) AS enrolled_count
       ,SUM(CASE WHEN user_enrolment_course.status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count
   FROM user_enrolment_course, master_user
   WHERE user_enrolment_course.user_id = master_user.user_id
   AND master_user.org_name=\''.$org.'\'
   GROUP BY name, email_id, org_name, designation
   ORDER BY completed_count desc');
 
            $template = [
             'table_open' => '<table id="tbl-result" class="display dataTable">'
         
         ];
         $table->setTemplate($template);
         $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Enrollment Count', 'Completion Count');
 
            return $table->generate($query);
    }

    

}
?>