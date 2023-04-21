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
       try{
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('user_program_enrolment');
        $builder->select('concat(first_name,\' \',last_name) as name, email, org_name, designation, status, completion_percentage, completed_on');
        $builder->join('master_user', 'master_user.user_id = user_program_enrolment.user_id ');
        $builder->where('program_id', $program);
        $query = $builder->get();
    
           $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable" style="width:90%">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Status', 'Completion Percentage', 'Completed On');

           return $table->generate($query);
       } 
       catch (\Exception $e) {
           throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
       } 
       //return $query->getResult();
    }

    public function getProgramWiseEnrolmentCount($org) {
        //try{

        
        $table = new \CodeIgniter\View\Table();
       $query = $this->db->query('SELECT program_name, batch_id, COUNT(*) AS enrolled_count
      ,SUM(CASE WHEN user_program_enrolment.status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count
  FROM user_program_enrolment, master_program
  WHERE user_program_enrolment.program_id = master_program.program_id
  GROUP BY program_name,batch_id
  ORDER BY completed_count desc');

           $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable" style="width:90%">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Program Name', 'Batch ID',  'Enrollment Count', 'Completion Count');

           return $table->generate($query);
       //return $query->getResult();
    // }
    // catch (\Exception $e) {
    //     throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
    // } 
    }

    public function getATIWiseCount() {
        try{

        
        $table = new \CodeIgniter\View\Table();
       $query = $this->db->query('SELECT distinct org_name,  COUNT(*) AS enrolled_count
      ,SUM(CASE WHEN user_program_enrolment.status =\'Not Started\' THEN 1 ELSE 0 END) AS Not_Started,
      SUM(CASE WHEN user_program_enrolment.status =\'In-Progress\' THEN 1 ELSE 0 END) AS In_Progress,
      SUM(CASE WHEN user_program_enrolment.status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count      
  FROM user_program_enrolment, master_program, master_organization
  WHERE user_program_enrolment.program_id = master_program.program_id
  AND master_program.root_org_id=master_organization.root_org_id
  AND is_ati=true
  GROUP BY program_name,batch_id
  ORDER BY completed_count desc');

           $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable" style="width:90%">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Institute', 'Enrolled',  'Not Started','In Progress', 'Completed');

           return $table->generate($query);
    }
    catch (\Exception $e) {
        throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
    } 
       //return $query->getResult();
    }

    public function getProgramWiseEnrolmentReportExcel($program,$org) {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('user_program_enrolment');
        $builder->select('concat(first_name,\' \',last_name) as name, email, org_name, designation, status, completion_percentage, completed_on');
        $builder->join('master_user', 'master_user.user_id = user_program_enrolment.user_id ');
        $builder->where('program_id', $program);
        $query = $builder->get();
    
        return $query->getResultArray();

    }

    public function getProgramWiseEnrolmentCountExcel($course,$org) {
        $table = new \CodeIgniter\View\Table();
       $query = $this->db->query('SELECT program_name, batch_id, COUNT(*) AS enrolled_count
      ,SUM(CASE WHEN user_program_enrolment.status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count
  FROM user_program_enrolment, master_program
  WHERE user_program_enrolment.program_id = master_program.program_id
  GROUP BY program_name,batch_id
  ORDER BY completed_count desc');

return $query->getResultArray();

    }

    public function getATIWiseCountExcel() {
        $table = new \CodeIgniter\View\Table();
       $query = $this->db->query('SELECT distinct org_name,  COUNT(*) AS enrolled_count
      ,SUM(CASE WHEN user_program_enrolment.status =\'Not Started\' THEN 1 ELSE 0 END) AS Not_Started,
      SUM(CASE WHEN user_program_enrolment.status =\'In-Progress\' THEN 1 ELSE 0 END) AS In_Progress,
      SUM(CASE WHEN user_program_enrolment.status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count      
  FROM user_program_enrolment, master_program, master_organization
  WHERE user_program_enrolment.program_id = master_program.program_id
  AND master_program.root_org_id=master_organization.root_org_id
  AND is_ati=true
  GROUP BY program_name,batch_id
  ORDER BY completed_count desc');

return $query->getResultArray();

    }

    

    

    

    

}
?>