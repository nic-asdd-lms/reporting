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

    public function getUserByOrg($org) {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email, org_name, designation, phone,created_date, roles, profile_update_status');
        $builder->where('org_name', $org);
        $query = $builder->get();
    
        $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable report-table">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Contact No.', 'Created Date', 'Roles', 'Profile Update Status');

           return $table->generate($query);
    }

    public function getMDOAdminList($orgName) {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email, org_name, designation, phone,created_date, roles');
        $builder->like('roles', 'MDO_ADMIN');
        if($orgName != '')
        {
            $builder->where('org_name',$orgName);
            
        }
        $query = $builder->get();
    
        $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable report-table">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Contact No.', 'Created Date', 'Roles');

           return $table->generate($query);
    }
    public function getUserCountByOrg() {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select(' org_name, count(*)');
       // $builder->where(' org_name IS NOT NULL');
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
        $builder->select('concat(first_name,\' \',last_name) as name, email, master_user.org_name, designation, phone,created_date,roles');
        $builder->join('master_structure', 'master_structure.ministry_state_name = master_user.org_name ');
        $builder->where('master_structure.ministry_state_name',$org);
        
        $unionDept = $this->db->table('master_user')
                    ->select('concat(first_name,\' \',last_name) as name, email, master_user.org_name, designation, phone,created_date,roles')
                    ->join('master_structure', 'master_structure.dep_name = master_user.org_name ')
                    ->where('master_structure.ministry_state_name',$org);
        
        $unionOrg = $this->db->table('master_user')
                    ->select('concat(first_name,\' \',last_name) as name, email, master_user.org_name, designation, phone,created_date,roles')
                    ->join('master_structure', 'master_structure.org_name = master_user.org_name ')
                    ->where('master_structure.ministry_state_name',$org);
        $query = $builder->union($unionDept)->union($unionOrg)->get();
    
        $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable report-table" style="width:90%">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Contact No.', 'Created Date', 'Roles');

        return $table->generate($query);
    }
    

    public function getRoleWiseCount($orgName) {
        $table = new \CodeIgniter\View\Table();

        if($orgName == '')
        $query = $this->db->query('select unnest(string_to_array(roles,\' / \')) as role, count(*) from master_user group by role order by role');
       else
       $query = $this->db->query('select unnest(string_to_array(roles,\' / \')) as role, count(*) from master_user where org_name= \''.$orgName.'\' group by role order by role');
       

        $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable " style="width:90%">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Role', 'User Count');

           return $table->generate($query);
    }

    public function getMonthWiseMDOAdminCount($orgName) {
        $table = new \CodeIgniter\View\Table();

        $query = $this->db->query('select  split_part(created_datemmyy::TEXT,\'/\', 1) AS Month, split_part(created_datemmyy::TEXT,\'/\', 2) AS YEAR ,count(*) from master_user where roles ~\'MDO_ADMIN\' group by created_datemmyy order by YEAR, Month  Desc');
       

        $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable " style="width:90%">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Month','Year', 'MDO ADMINs created');

           return $table->generate($query);
    }

    public function getCBPAdminList($orgName) {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email, org_name, designation, phone,created_date,roles');
        $builder->like('roles', 'CBP_ADMIN');
        if($orgName != '')
        {
            $builder->where('org_name',$orgName);
            
        }
        $query = $builder->get();
    
        $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable report-table">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Contact No.', 'Created Date', 'Roles');

           return $table->generate($query);
    }
    

    public function getCreatorList($orgName) {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email, org_name, designation, phone,created_date,roles');
        $builder->like('roles', 'CONTENT_CREATOR');
        if($orgName != '')
        {
            $builder->where('org_name',$orgName);
        }
        $query = $builder->get();
    
        $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable report-table">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Contact No.', 'Created Date', 'Roles');

           return $table->generate($query);
    }
    

    public function getReviewerList($orgName) {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email, org_name, designation, phone,created_date,roles');
        $builder->like('roles', 'CONTENT_REVIEWER');
        if($orgName != '')
        {
            $builder->where('org_name',$orgName);
        }
        $query = $builder->get();
    
        $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable report-table">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Contact No.', 'Created Date', 'Roles');

           return $table->generate($query);
    }
    

    public function getPublisherList($orgName) {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email, org_name, designation, phone,created_date,roles');
        $builder->like('roles', 'CONTENT_PUBLISHER');
        if($orgName != '')
        {
            $builder->where('org_name',$orgName);
        }
        $query = $builder->get();
    
        $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable report-table">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Contact No.', 'Created Date', 'Roles');

           return $table->generate($query);
    }
    

    public function getEditorList($orgName) {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email, org_name, designation, phone,created_date,roles');
        $builder->like('roles', 'EDITOR');
        if($orgName != '')
        {
            $builder->where('org_name',$orgName);
        }
        $query = $builder->get();
    
        $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable report-table">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Contact No.', 'Created Date', 'Roles');

           return $table->generate($query);
    }
    

    public function getFracAdminList($orgName) {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email, org_name, designation, phone,created_date,roles');
        $builder->like('roles', 'FRAC_ADMIN');
        if($orgName != '')
        {
            $builder->where('org_name',$orgName);
        }
        $query = $builder->get();
    
        $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable report-table">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Contact No.', 'Created Date', 'Roles');

           return $table->generate($query);
    }

    public function getFracCompetencyMemberList($orgName) {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email, org_name, designation, phone,created_date,roles');
        $builder->like('roles', 'FRAC_COMPETENCY_MEMBER');
        if($orgName != '')
        {
            $builder->where('org_name',$orgName);
        }
        $query = $builder->get();
    
        $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable report-table">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Contact No.', 'Created Date', 'Roles');

           return $table->generate($query);
    }

    public function getFRACL1List($orgName) {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email, org_name, designation, phone,created_date,roles');
        $builder->like('roles', 'FRAC_REVIEWER_L1');
        if($orgName != '')
        {
            $builder->where('org_name',$orgName);
        }
        $query = $builder->get();
    
        $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable report-table">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Contact No.', 'Created Date', 'Roles');

           return $table->generate($query);
    }

    public function getFRACL2List($orgName) {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email, org_name, designation, phone,created_date,roles');
        $builder->like('roles', 'FRAC_REVIEWER_L2');
        if($orgName != '')
        {
            $builder->where('org_name',$orgName);
        }
        $query = $builder->get();
    
        $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable report-table">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Contact No.', 'Created Date', 'Roles');

           return $table->generate($query);
    }

    public function getIFUMemberList($orgName) {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email, org_name, designation, phone,created_date,roles');
        $builder->like('roles', 'IFU_MEMBER');
        if($orgName != '')
        {
            $builder->where('org_name',$orgName);
        }
        $query = $builder->get();
    
        $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable report-table">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Contact No.', 'Created Date', 'Roles');

           return $table->generate($query);
    }

    public function getPublicList($orgName) {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email, org_name, designation, phone,created_date,roles');
        $builder->like('roles', 'PUBLIC');
        if($orgName != '')
        {
            $builder->where('org_name',$orgName);
        }
        $query = $builder->get();
    
        $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable report-table">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Contact No.', 'Created Date', 'Roles');

           return $table->generate($query);
    }

    public function getSPVAdminList($orgName) {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email, org_name, designation, phone,created_date,roles');
        $builder->like('roles', 'SPV_ADMIN');
        if($orgName != '')
        {
            $builder->where('org_name',$orgName);
        }
        $query = $builder->get();
    
        $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable report-table">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Contact No.', 'Created Date', 'Roles');

           return $table->generate($query);
    }

    public function getStateAdminList($orgName) {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email, org_name, designation, phone,created_date,roles');
        $builder->like('roles', 'STATE_ADMIN');
        if($orgName != '')
        {
            $builder->where('org_name',$orgName);
        }
        $query = $builder->get();
    
        $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable report-table">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Contact No.', 'Created Date', 'Roles');

           return $table->generate($query);
    }

    public function getWATMemberList($orgName) {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email, org_name, designation, phone,created_date,roles');
        $builder->like('roles', 'WAT_MEMBER');
        if($orgName != '')
        {
            $builder->where('org_name',$orgName);
        }
        $query = $builder->get();
    
        $template = [
            'table_open' => '<table id="tbl-result" class="display dataTable report-table">'
        
        ];
        $table->setTemplate($template);
        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Contact No.', 'Created Date', 'Roles');

           return $table->generate($query);
    }

    

}
?>