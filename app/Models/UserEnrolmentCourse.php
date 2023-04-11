<?php

namespace App\Models;

use CodeIgniter\Model;

class UserEnrolmentCourse extends Model
{
    public function __construct() {
        parent::__construct();
        $db = \Config\Database::connect();
    }

    public function getCourseWiseEnrolmentReport($course , $org) {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('user_enrolment_course');
        $builder->select('concat(first_name,\' \',last_name) as name, email_id, org_name, designation, status, completion_percentage, completed_on');
        $builder->join('master_user', 'master_user.user_id = user_enrolment_course.user_id ');
        $builder->where('course_id', $course);
        if($org != ''){
            $builder->where('org_id', $org);
        }
        $query = $builder->get();
    
           $template = [
            'table_open' => '<table id="tbl-result" class="display  report-table" style="width:90%">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Status', 'Completion Percentage', 'Completed On');

           return $table->generate($query);
       //return $query->getResult();
    }

    public function getCourseWiseEnrolmentCount($course,$org) {
        $table = new \CodeIgniter\View\Table();
        if($org != ''){
            $query = $this->db->query('SELECT course_name, published_date, duration_h,COUNT(*) AS enrolled_count
      ,SUM(CASE WHEN user_enrolment_course.status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count
  FROM user_enrolment_course, master_course
  WHERE user_enrolment_course.course_id = master_course.course_id
  GROUP BY course_name,published_date, duration_h
  ORDER BY completed_count desc');
        }
        else {
            $query = $this->db->query('SELECT course_name, published_date, duration_h,COUNT(*) AS enrolled_count
      ,SUM(CASE WHEN user_enrolment_course.status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count
  FROM user_enrolment_course, master_course, master_user
  WHERE user_enrolment_course.course_id = master_course.course_id
  AND master_user.user_id =user_enrolment_course.user_id
  AND master_user.org_id=\''.$org.'\'
  GROUP BY course_name,published_date, duration_h
  ORDER BY completed_count desc');
        }
        
       

           $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable report-table" style="width:90%">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Course Name', 'Published Date', 'Duration', 'Enrollment Count', 'Completion Count');

           return $table->generate($query);
       //return $query->getResult();
    }

    
    public function getCollectionWiseEnrolmentReport($collection,$org) {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('user_enrolment_course');
        $builder->select('concat(first_name,\' \',last_name) as name, email_id, org_name, designation, course_name, status, completion_percentage, completed_on');
        $builder->join('master_user', 'master_user.user_id = user_enrolment_course.user_id ');
        $builder->join('curated_collection_courses', 'curated_collection_courses.course_id = user_enrolment_course.course_id ');
        $builder->join('master_curated_collection', 'curated_collection_courses.curated_id = master_curated_collection.curated_id ');
        $builder->where('master_curated_collection.curated_id', $collection);
        if($org != ''){
            $builder->where('org_id', $org);
        }
        $query = $builder->get();
    
           $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable report-table" style="width:90%">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation','Course Name', 'Status', 'Completion Percentage', 'Completed On');

           return $table->generate($query);
       //return $query->getResult();
    }

    public function getCollectionWiseEnrolmentCount($course,$org) {
        $table = new \CodeIgniter\View\Table();
        if($org == ''){
            $query = $this->db->query('SELECT concat(first_name,\' \',last_name) as name, email_id, org_name, designation, course_name,  COUNT(*) AS enrolled_count
      ,SUM(CASE WHEN user_enrolment_course.status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count
  FROM user_enrolment_course, master_curated_collection, curated_collection_courses, master_user
  WHERE user_enrolment_course.course_id = curated_collection_courses.course_id
  AND master_curated_collection.curated_id = curated_collection_courses.curated_id
  AND master_user.user_id = user_enrolment_course.user_id
  GROUP BY name,email_id, org_name, designation, course_name
  ORDER BY completed_count desc');
        }
        else {
            $query = $this->db->query('SELECT concat(first_name,\' \',last_name) as name, email_id, org_name, designation, course_name,  COUNT(*) AS enrolled_count
            ,SUM(CASE WHEN user_enrolment_course.status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count
        FROM user_enrolment_course, master_curated_collection, curated_collection_courses, master_user
        WHERE user_enrolment_course.course_id = curated_collection_courses.course_id
        AND master_curated_collection.curated_id = curated_collection_courses.curated_id
        AND master_user.user_id = user_enrolment_course.user_id
    AND master_user.org_id=\''.$org.'\'
  GROUP BY name, email_id, org_name, designation, course_name
  ORDER BY completed_count desc');
        }
           $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable report-table" style="width:90%">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation','Course Name',  'Enrolment Count', 'Completion Count');

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
            'table_open' => '<table id="tbl-result" class="display dataTable report-table" style="width:90%">'
        
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
             'table_open' => '<table id="tbl-result" class="display dataTable report-table" style="width:90%">'
         
         ];
         $table->setTemplate($template);
         $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Enrollment Count', 'Completion Count');
 
            return $table->generate($query);
    }

    

}
?>