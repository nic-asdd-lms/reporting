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
        $builder->select('concat(first_name,\' \',last_name) as name, email, org_name, designation, status, completion_percentage, completed_on');
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

    public function getProgramWiseEnrolmentCount($org) {
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

    public function getATIWiseCount() {
        $table = new \CodeIgniter\View\Table();
       $query = $this->db->query('SELECT distinct org_name,  COUNT(*) AS enrolled_count
      ,SUM(CASE WHEN user_enrolment_program.status =\'Not Started\' THEN 1 ELSE 0 END) AS Not_Started,
      SUM(CASE WHEN user_enrolment_program.status =\'In-Progress\' THEN 1 ELSE 0 END) AS In_Progress,
      SUM(CASE WHEN user_enrolment_program.status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count      
  FROM user_enrolment_program, master_program, master_organization
  WHERE user_enrolment_program.program_id = master_program.program_id
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
       //return $query->getResult();
    }

    public function getProgramWiseEnrolmentReportExcel($program,$org) {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('user_enrolment_program');
        $builder->select('concat(first_name,\' \',last_name) as name, email, org_name, designation, status, completion_percentage, completed_on');
        $builder->join('master_user', 'master_user.user_id = user_enrolment_program.user_id ');
        $builder->where('program_id', $program);
        $query = $builder->get();
    
        return $query->getResultArray();

    }

    public function getProgramWiseEnrolmentCountExcel($course,$org) {
        $table = new \CodeIgniter\View\Table();
       $query = $this->db->query('SELECT program_name, batch_id, COUNT(*) AS enrolled_count
      ,SUM(CASE WHEN user_enrolment_program.status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count
  FROM user_enrolment_program, master_program
  WHERE user_enrolment_program.program_id = master_program.program_id
  GROUP BY program_name,batch_id
  ORDER BY completed_count desc');

return $query->getResultArray();

    }

    public function getATIWiseCountExcel() {
        $table = new \CodeIgniter\View\Table();
       $query = $this->db->query('SELECT distinct org_name,  COUNT(*) AS enrolled_count
      ,SUM(CASE WHEN user_enrolment_program.status =\'Not Started\' THEN 1 ELSE 0 END) AS Not_Started,
      SUM(CASE WHEN user_enrolment_program.status =\'In-Progress\' THEN 1 ELSE 0 END) AS In_Progress,
      SUM(CASE WHEN user_enrolment_program.status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count      
  FROM user_enrolment_program, master_program, master_organization
  WHERE user_enrolment_program.program_id = master_program.program_id
  AND master_program.root_org_id=master_organization.root_org_id
  AND is_ati=true
  GROUP BY program_name,batch_id
  ORDER BY completed_count desc');

return $query->getResultArray();

    }

    

    

    

    

}
?>