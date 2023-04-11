<?php

namespace App\Models;

use CodeIgniter\Model;

class UserEnrolmentProgram extends Model
{
    public function __construct() {
        parent::__construct();
        $db = \Config\Database::connect();
    }

    public function getProgramWiseEnrolmentReport($program,$org) {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('user_enrolment_program');
        $builder->select('concat(first_name,\' \',last_name) as name, email_id, org_name, designation, status, completion_percentage, completed_on');
        $builder->join('master_user', 'master_user.user_id = user_enrolment_program.user_id ');
        $builder->where('program_id', $program);
        $query = $builder->get();
    
           $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable" style="width:90%">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Status', 'Completion Percentage', 'Completed On');

           return $table->generate($query);
       //return $query->getResult();
    }

    public function getProgramWiseEnrolmentCount($course,$org) {
        $table = new \CodeIgniter\View\Table();
       $query = $this->db->query('SELECT program_name, batch_id, COUNT(*) AS enrolled_count
      ,SUM(CASE WHEN user_enrolment_program.status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count
  FROM user_enrolment_program, master_program
  WHERE user_enrolment_program.program_id = master_program.program_id
  GROUP BY program_name,batch_id
  ORDER BY completed_count desc');

           $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable" style="width:90%">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Program Name', 'Batch ID',  'Enrollment Count', 'Completion Count');

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
            'table_open' => '<table id="tbl-result" class="display dataTable" style="width:90%">'
        
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
            'table_open' => '<table id="tbl-result" class="display dataTable" style="width:90%">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Status', 'Completion Percentage', 'Completed On');

           return $table->generate($query);
       //return $query->getResult();
    }


    public function getEnrolmentByOrg($org) {
        $table = new \CodeIgniter\View\Table();
        $builder = $this->db->table('user_enrolment_course');
        $builder->select('concat(first_name,\' \',last_name) as name, email_id, master_user.org_name, designation, course_name, user_enrolment_course.status, completion_percentage, completed_on');
        $builder->join('master_user', 'master_user.user_id = user_enrolment_course.user_id ');
        $builder->join('master_course', 'master_course.course_id = user_enrolment_course.course_id ');
        $builder->where('master_user.org_name', $org);
        $query = $builder->get();
           $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable" style="width:90%">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation','Course', 'Status', 'Completion Percentage', 'Completed On');

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
   UNION
   SELECT concat(first_name,\' \',last_name) as name, email_id, org_name, designation,0 AS enrolled_count
       ,0 AS completed_count
   FROM  master_user
   WHERE master_user.org_name=\''.$org.'\'
   AND master_user.user_id NOT IN (SELECT DISTINCT user_id from user_enrolment_course)
   ORDER BY completed_count desc');
 
            $template = [
             'table_open' => '<table id="tbl-result" class="display dataTable" style="width:90%">'
         
         ];
         $table->setTemplate($template);
         $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Enrollment Count', 'Completion Count');
 
            return $table->generate($query);
    }

    

}
?>