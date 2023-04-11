<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterUserModel extends Model
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

    public function getCourseName($course_id) {
        $result = $this->db->query('select  course_name from master_course where course_id = \''.$course_id.'\'')->getRow()->course_name;
       return $result;
    }
    
    public function getUserByOrg($org) {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email_id, org_name, designation, phone_no,created_date, roles');
        $builder->where('org_name', $org);
        $query = $builder->get();
    
        $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable report-table">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Contact No.', 'Created Date', 'Roles');

           return $table->generate($query);
    }

    public function getMDOAdminList() {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email_id, org_name, designation, phone_no,created_date');
        $builder->like('roles', 'MDO_ADMIN');
        $query = $builder->get();
    
        $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable report-table">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Contact No.', 'Created Date');

           return $table->generate($query);
    }
    public function getUserCountByOrg() {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select(' org_name, count(*)');
        $builder->groupBy('org_name');
        $query = $builder->get();
    
        $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable " style="width:90%">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Organisation', 'User Count');

           return $table->generate($query);
    }
    

    public function getUserByMinistry($org) {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email_id, master_user.org_name, designation, phone_no,created_date');
        $builder->join('master_structure', 'master_structure.ministry_state_name = master_user.org_name ');
        $builder->where('master_structure.ministry_state_name',$org);
        
        $unionDept = $this->db->table('master_user')
                    ->select('concat(first_name,\' \',last_name) as name, email_id, master_user.org_name, designation, phone_no,created_date')
                    ->join('master_structure', 'master_structure.dep_name = master_user.org_name ')
                    ->where('master_structure.ministry_state_name',$org);
        
        $unionOrg = $this->db->table('master_user')
                    ->select('concat(first_name,\' \',last_name) as name, email_id, master_user.org_name, designation, phone_no,created_date')
                    ->join('master_structure', 'master_structure.org_name = master_user.org_name ')
                    ->where('master_structure.ministry_state_name',$org);
        $query = $builder->union($unionDept)->union($unionOrg)->get();
    
        $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable report-table" style="width:90%">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Contact No.', 'Created Date');

        return $table->generate($query);
    }
    

}
?>