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

        $builder = $this->db->table('user_course_enrolment');
        $builder->select('concat(master_user.first_name,\' \',master_user.last_name) as name, master_user.email, master_organization.org_name, master_user.designation, master_course.course_name,  user_course_enrolment.completion_status, user_course_enrolment.completion_percentage, user_course_enrolment.completed_on');
        $builder->join('master_user', 'master_user.user_id = user_course_enrolment.user_id ');
        $builder->join('master_course', 'master_course.course_id = user_course_enrolment.course_id ');
        $builder->join('master_organization', 'master_user.root_org_id = master_organization.root_org_id ');
        $builder->where('user_course_enrolment.course_id', $course);
        if($org != ''){
            $builder->where('master_user.root_org_id', $org);
        }
        $query = $builder->get();
    
           $template = [
            'table_open' => '<table id="tbl-result" class="display  report-table" style="width:90%">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Course Name','Completion Status', 'Completion Percentage', 'Completed On');

           return $table->generate($query);
       //return $query->getResult();
    }

    public function getCourseWiseEnrolmentCount($org) {
        $table = new \CodeIgniter\View\Table();
        if($org == ''){
            $query = $this->db->query('SELECT course_name, published_date, durationhms,COUNT(*) AS enrolled_count
      ,SUM(CASE WHEN user_course_enrolment.completion_status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count, avg_rating
  FROM user_course_enrolment
  INNER JOIN  master_course ON user_course_enrolment.course_id = master_course.course_id
  WHERE master_course.status=\'Live\'
  GROUP BY course_name,published_date, durationhms,avg_rating
  ORDER BY completed_count desc');
        }
        else {
            $query = $this->db->query('SELECT course_name, published_date, durationhms,COUNT(*) AS enrolled_count
      ,SUM(CASE WHEN user_course_enrolment.completion_status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count, avg_rating
  FROM user_course_enrolment
  INNER JOIN  master_course ON user_course_enrolment.course_id = master_course.course_id
  INNER JOIN  master_user ON master_user.user_id =user_course_enrolment.user_id
  WHERE master_user.root_org_id=\''.$org.'\'
  AND master_course.status=\'Live\'
  GROUP BY course_name,published_date, durationhms,avg_rating
  ORDER BY completed_count desc');
        }
        
       

           $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable report-table" style="width:90%">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Course Name', 'Published Date', 'Duration (HH:MM:SS)', 'Enrollment Count', 'Completion Count', 'Average Rating');

           return $table->generate($query);
       //return $query->getResult();
    }



    public function getCourseMinistrySummary($course) {
        $table = new \CodeIgniter\View\Table();
        $queryString = 'select distinct ms_name,COUNT(distinct user_course_enrolment.user_id) AS enrolled_count ,(CASE WHEN user_course_enrolment.completion_status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count 
        from master_org_hierarchy ,user_course_enrolment, master_user  
        where user_course_enrolment.user_id = master_user.user_id 
        and ms_id in 
        (select ms_id from master_org_hierarchy  
        join master_user on master_user.root_org_id = master_org_hierarchy.ms_id  
        join user_course_enrolment on master_user.user_id = user_course_enrolment.user_id 
        where course_id=\''.$course.'\')
        group by ms_name,user_course_enrolment.completion_status
        union
        select distinct ms_name,COUNT(distinct user_course_enrolment.user_id) AS enrolled_count ,(CASE WHEN user_course_enrolment.completion_status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count 
        from master_org_hierarchy ,user_course_enrolment   , master_user  
        where user_course_enrolment.user_id = master_user.user_id 
        and ms_id in 
        (select ms_id from master_org_hierarchy  
        join master_user on master_user.root_org_id = master_org_hierarchy.dept_id  
        join user_course_enrolment on master_user.user_id = user_course_enrolment.user_id 
        where course_id=\''.$course.'\')
        group by ms_name,user_course_enrolment.completion_status
        union
        select distinct ms_name,COUNT(distinct user_course_enrolment.user_id) AS enrolled_count ,(CASE WHEN user_course_enrolment.completion_status =\'Completed\'   THEN 1 ELSE 0 END) AS completed_count 
        from master_org_hierarchy ,user_course_enrolment , master_user   
        where user_course_enrolment.user_id = master_user.user_id 
        and ms_id in 
        (select ms_id from master_org_hierarchy  
        join master_user on master_user.root_org_id = master_org_hierarchy.org_id  
        join user_course_enrolment on master_user.user_id = user_course_enrolment.user_id 
        where course_id=\''.$course.'\')
        group by ms_name,user_course_enrolment.completion_status';
        
            $query = $this->db->query($queryString);
            
           $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable report-table" style="width:90%">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading( 'Ministry Name',  'Enrollment Count', 'Completion Count');

           return $table->generate($query);
       //return $query->getResult();
    }

    
    public function getCollectionWiseEnrolmentReport($collection,$org) {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('user_course_enrolment');
        $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, course_name, completion_status, completion_percentage, completed_on');
        $builder->join('master_user', 'master_user.user_id = user_course_enrolment.user_id ');
        $builder->join('master_organization', 'master_user.root_org_id = master_organization.root_org_id ');
        $builder->join('course_curated', 'course_curated.course_id = user_course_enrolment.course_id ');
        $builder->join('master_course', 'master_course.course_id = user_course_enrolment.course_id ');
        $builder->join('master_curated_collection', 'course_curated.curated_id = master_curated_collection.curated_id ');
        $builder->where('master_curated_collection.curated_id', $collection);
        if($org != ''){
            $builder->where('master_user.root_org_id', $org);
        }
        $builder->distinct();
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
            $query = $this->db->query('SELECT  course_name,  COUNT(*) AS enrolled_count
      ,SUM(CASE WHEN user_course_enrolment.completion_status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count
  FROM user_course_enrolment, master_curated_collection, course_curated, master_user, master_course
  WHERE user_course_enrolment.course_id = course_curated.course_id
  AND master_curated_collection.curated_id = course_curated.curated_id
  AND master_user.user_id = user_course_enrolment.user_id
  AND master_course.course_id = user_course_enrolment.course_id 
        GROUP BY  course_name
  ORDER BY completed_count desc');
        }
        else {
            $query = $this->db->query('SELECT  course_name,  COUNT(*) AS enrolled_count
            ,SUM(CASE WHEN user_course_enrolment.completion_status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count
        FROM user_course_enrolment, master_curated_collection, course_curated, master_user, master_course
        WHERE user_course_enrolment.course_id = course_curated.course_id
        AND master_curated_collection.curated_id = course_curated.curated_id
        AND master_user.user_id = user_course_enrolment.user_id
        AND master_course.course_id = user_course_enrolment.course_id 
 AND master_user.root_org_id=\''.$org.'\'
  GROUP BY course_name
  ORDER BY completed_count desc');
        }
           $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable report-table" style="width:90%">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Course Name',  'Enrolment Count', 'Completion Count');

           return $table->generate($query);
       //return $query->getResult();
    }


    public function getEnrolmentByOrg($org) {
        $table = new \CodeIgniter\View\Table();
        $builder = $this->db->table('user_course_enrolment');
        $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, course_name, user_course_enrolment.completion_status, completion_percentage, completed_on');
        $builder->join('master_user', 'master_user.user_id = user_course_enrolment.user_id ');
        $builder->join('master_organization', 'master_user.root_org_id = master_organization.root_org_id ');
        $builder->join('master_course', 'master_course.course_id = user_course_enrolment.course_id ');
        $builder->where('master_organization.org_name', $org);
        $builder->where('master_course.status','Live');
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
        $query = $this->db->query('SELECT concat(first_name,\' \',last_name) as name, email, org_name, designation,COUNT(*) AS enrolled_count
       ,SUM(CASE WHEN user_course_enrolment.completion_status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count
   FROM user_course_enrolment, master_user
   WHERE user_course_enrolment.user_id = master_user.user_id
   AND master_user.org_name=\''.$org.'\'
   GROUP BY name, email, org_name, designation
   UNION
   SELECT concat(first_name,\' \',last_name) as name, email, org_name, designation,0 AS enrolled_count
       ,0 AS completed_count
   FROM  master_user
   WHERE master_user.org_name=\''.$org.'\'
   AND master_user.user_id NOT IN (SELECT DISTINCT user_id from user_course_enrolment)
   ORDER BY completed_count desc');
 
            $template = [
             'table_open' => '<table id="tbl-result" class="display dataTable report-table" style="width:90%">'
         
         ];
         $table->setTemplate($template);
         $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'No. of Courses Enrolled', 'No. of Courses Completed');
 
            return $table->generate($query);
    }


    public function getCourseWiseEnrolmentReporExcelt($course , $org) {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('user_course_enrolment');
        $builder->select('concat(master_user.first_name,\' \',master_user.last_name) as name, master_user.email, master_user.org_name, master_user.designation, master_course.course_name,  user_course_enrolment.completion_status, user_course_enrolment.completion_percentage, user_course_enrolment.completed_on');
        $builder->join('master_user', 'master_user.user_id = user_course_enrolment.user_id ');
        $builder->join('master_course', 'master_course.course_id = user_course_enrolment.course_id ');
        $builder->where('user_course_enrolment.course_id', $course);
        if($org != ''){
            $builder->where('master_user.root_org_id', $org);
        }
        $query = $builder->get();
    
        return $query->getResultArray();

    }

    public function getCourseWiseEnrolmentCountExcel($course,$org) {
        $table = new \CodeIgniter\View\Table();
        if($org == ''){
            $query = $this->db->query('SELECT course_name, published_date, durationh,COUNT(*) AS enrolled_count
      ,SUM(CASE WHEN user_course_enrolment.completion_status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count, avg_rating
  FROM user_course_enrolment
  INNER JOIN  master_course ON user_course_enrolment.course_id = master_course.course_id
  GROUP BY course_name,published_date, durationh,avg_rating
  ORDER BY completed_count desc');
        }
        else {
            $query = $this->db->query('SELECT course_name, published_date, durationh,COUNT(*) AS enrolled_count
      ,SUM(CASE WHEN user_course_enrolment.completion_status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count, avg_rating
  FROM user_course_enrolment
  INNER JOIN  master_course ON user_course_enrolment.course_id = master_course.course_id
  INNER JOIN  master_user ON master_user.user_id =user_course_enrolment.user_id
  WHERE master_user.root_org_id=\''.$org.'\'
  GROUP BY course_name,published_date, durationh,avg_rating
  ORDER BY completed_count desc');
        }
        
        return $query->getResultArray();

    }



    public function getCourseMinistrySummaryExcel($course) {
        $table = new \CodeIgniter\View\Table();
        
            $query = $this->db->query('SELECT  distinct ms_name, COUNT(distinct user_course_enrolment.user_id) AS enrolled_count
            ,(CASE WHEN user_course_enrolment.completion_status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count
        FROM user_course_enrolment, master_course, master_user, master_org_hierarchy
          WHERE user_course_enrolment.user_id=master_user.user_id
          AND user_course_enrolment.course_id= \''.$course.'\'

          AND (master_user.root_org_id = master_org_hierarchy.org_id
        OR master_user.root_org_id = master_org_hierarchy.dept_id
        OR master_user.root_org_id = master_org_hierarchy.ms_id)
        GROUP BY course_name,  ms_name, user_course_enrolment.completion_status
        ORDER BY ms_name desc
        ');
       
        
        return $query->getResultArray();

    }

    
    public function getCollectionWiseEnrolmentReportExcel($collection,$org) {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('user_course_enrolment');
        $builder->select('concat(first_name,\' \',last_name) as name, email, org_name, designation, course_name, completion_status, completion_percentage, completed_on');
        $builder->join('master_user', 'master_user.user_id = user_course_enrolment.user_id ');
        $builder->join('course_curated', 'course_curated.course_id = user_course_enrolment.course_id ');
        $builder->join('master_curated_collection', 'course_curated.curated_id = master_curated_collection.curated_id ');
        $builder->where('master_curated_collection.curated_id', $collection);
        if($org != ''){
            $builder->where('master_user.root_org_id', $org);
        }
        $builder->distinct();
        $query = $builder->get();
    
        return $query->getResultArray();

    }

    public function getCollectionWiseEnrolmentCountExcel($course,$org) {
        $table = new \CodeIgniter\View\Table();
        if($org == ''){
            $query = $this->db->query('SELECT  course_name,  COUNT(*) AS enrolled_count
      ,SUM(CASE WHEN user_course_enrolment.completion_status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count
  FROM user_course_enrolment, master_curated_collection, course_curated, master_user
  WHERE user_course_enrolment.course_id = course_curated.course_id
  AND master_curated_collection.curated_id = course_curated.curated_id
  AND master_user.user_id = user_course_enrolment.user_id
  GROUP BY  course_name
  ORDER BY completed_count desc');
        }
        else {
            $query = $this->db->query('SELECT  course_name,  COUNT(*) AS enrolled_count
            ,SUM(CASE WHEN user_course_enrolment.completion_status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count
        FROM user_course_enrolment, master_curated_collection, course_curated, master_user
        WHERE user_course_enrolment.course_id = course_curated.course_id
        AND master_curated_collection.curated_id = course_curated.curated_id
        AND master_user.user_id = user_course_enrolment.user_id
    AND master_user.root_org_id=\''.$org.'\'
  GROUP BY course_name
  ORDER BY completed_count desc');
        }
        return $query->getResultArray();

    }


    public function getEnrolmentByOrgExcel($org) {
        $table = new \CodeIgniter\View\Table();
        $builder = $this->db->table('user_course_enrolment');
        $builder->select('concat(first_name,\' \',last_name) as name, email, master_user.org_name, designation, course_name, user_course_enrolment.completion_status, completion_percentage, completed_on');
        $builder->join('master_user', 'master_user.user_id = user_course_enrolment.user_id ');
        $builder->join('master_course', 'master_course.course_id = user_course_enrolment.course_id ');
        $builder->where('master_user.root_org_id', $org);
        $query = $builder->get();
        
        return $query->getResultArray();

    }


    public function getUserEnrolmentCountByMDOExcel($org) {
        $table = new \CodeIgniter\View\Table();
        $query = $this->db->query('SELECT concat(first_name,\' \',last_name) as name, email, org_name, designation,COUNT(*) AS enrolled_count
       ,SUM(CASE WHEN user_course_enrolment.completion_status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count
   FROM user_course_enrolment, master_user
   WHERE user_course_enrolment.user_id = master_user.user_id
   AND master_user.root_org_id=\''.$org.'\'
   GROUP BY name, email, org_name, designation
   UNION
   SELECT concat(first_name,\' \',last_name) as name, email, org_name, designation,0 AS enrolled_count
       ,0 AS completed_count
   FROM  master_user
   WHERE master_user.root_org_id=\''.$org.'\'
   AND master_user.user_id NOT IN (SELECT DISTINCT user_id from user_course_enrolment)
   ORDER BY completed_count desc');
 
   return $query->getResultArray();

    }

    

}

?>