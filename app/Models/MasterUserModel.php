<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterUserModel extends Model
{
    public function __construct() {
        parent::__construct();
        //$this->load->database();
        $db = \Config\Database::connect();
        helper('array');
        
    }

    public function getUserByOrg($org) {
        try {
            $table = new \CodeIgniter\View\Table();

            $builder = $this->db->table('master_user');
            $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, phone,created_date, roles, profile_update_status');
            $builder->join('master_organization', 'master_organization.root_org_id = master_user.root_org_id');
            $builder->where('master_organization.org_name', $org);
            $query = $builder->get();
        
            return $query;
            
        }
        catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        } 
        
    }

    

    public function getMDOAdminList($orgName) {
        try {
            $table = new \CodeIgniter\View\Table();

            $builder = $this->db->table('master_user');
            $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, phone,created_date, roles');
            $builder->join(' master_organization','master_organization.root_org_id = master_user.root_org_id');
            $builder->like('roles', 'MDO_ADMIN');
            if($orgName != '')
            {
                $builder->where('org_name',$orgName);
                
            }
            $query = $builder->get();
        
            return $query;
        }
        catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        } 
        
    }

    
    public function getUserCountByOrg() {
        try {
            $table = new \CodeIgniter\View\Table();

            $builder = $this->db->table('master_user');
            $builder->select(' master_organization.org_name, count(*)');
            $builder->join(' master_organization','master_organization.root_org_id = master_user.root_org_id');
            // $builder->where(' org_name IS NOT NULL');
            $builder->groupBy('master_organization.org_name');
            $query = $builder->get();

            return $query;
            // $template = [
            //     'table_open' => '<table id="tbl-result" class="display dataTable report-table" style="width:90%">'
            
            // ];
            // $table->setTemplate($template);
            // $table->setHeading('Organisation', 'User Count');
    
            //    return $table->generate($query);
        }
        
      catch (\Exception $e) {
        throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
    } 
    }

    

    public function getUserByMinistry($org) {
        try {
            $table = new \CodeIgniter\View\Table();

            $builder = $this->db->table('master_user');
            $builder->select('concat(first_name,\' \',last_name) as name, email, master_org_hierarchy.ms_name, \'-\' as dept_name,\'-\' as org_name, designation, phone,created_date,roles');
            $builder->join('master_organization', 'master_organization.root_org_id = master_user.root_org_id ');
            $builder->join('master_org_hierarchy', 'master_org_hierarchy.ms_id = master_user.root_org_id ');
            $builder->where('master_org_hierarchy.ms_name',$org);
            
            $unionDept = $this->db->table('master_user')
                        ->select('concat(first_name,\' \',last_name) as name, email, master_org_hierarchy.ms_name, master_org_hierarchy.dept_name,  \'-\' as org_name, designation, phone,created_date,roles')
                        ->join('master_organization', 'master_organization.root_org_id = master_user.root_org_id ')
                        ->join('master_org_hierarchy', 'master_org_hierarchy.dept_id = master_user.root_org_id ')
                        ->where('master_org_hierarchy.ms_name',$org)
                        ->where('master_org_hierarchy.dept_id != master_org_hierarchy.ms_id');
            
            $unionOrg = $this->db->table('master_user')
                        ->select('concat(first_name,\' \',last_name) as name, email, master_org_hierarchy.ms_name, master_org_hierarchy.dept_name, master_organization.org_name, designation, phone,created_date,roles')
                        ->join('master_organization', 'master_organization.root_org_id = master_user.root_org_id ')
                        ->join('master_org_hierarchy', 'master_org_hierarchy.org_id = master_user.root_org_id ')
                        ->where('master_org_hierarchy.ms_name',$org)
                        ->where('master_org_hierarchy.dept_id != master_org_hierarchy.org_id')
                        ->where('master_org_hierarchy.org_id != master_org_hierarchy.ms_id');
            $query = $builder->union($unionDept)->union($unionOrg)->get();
        
            return $query;
        }
        catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        } 
        
    }
    
    

    public function getDayWiseUserOnboarding() {
        try {
            $table = new \CodeIgniter\View\Table();

            $query = $this->db->query('select split_part(created_date::TEXT,\'/\', 1) as DAY ,split_part(created_date::TEXT,\'/\', 2) AS MONTH,split_part(created_date::TEXT,\'/\', 3) AS YEAR ,(count(user_id)) AS Day_wise_User_Onboarded from master_user group by created_date order by YEAR,MONTH,DAY desc');
            
            return $query;
        }
        catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        } 
       
    }


    
    public function getMonthWiseUserOnboarding() {
        try {
            $table = new \CodeIgniter\View\Table();

            $query = $this->db->query('select split_part(created_datemmyy::TEXT,\'/\', 1) AS MONTH,split_part(created_datemmyy::TEXT,\'/\', 2) AS YEAR ,(count(user_id)) AS Day_wise_User_Onboarded from master_user group by created_datemmyy order by YEAR,MONTH desc');
            
            return $query;
        }
        catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        } 
        
    }

    
    

    public function getRoleWiseCount($orgName) {
        try {
            $table = new \CodeIgniter\View\Table();

            if($orgName == '')
            $query = $this->db->query('select unnest(string_to_array(roles,\' / \')) as role, count(*) from master_user group by role order by role');
           else
           $query = $this->db->query('select unnest(string_to_array(roles,\' / \')) as role, count(*) from master_user where org_name= \''.$orgName.'\' group by role order by role');
           
    
           return $query;
        }
        catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        } 
        
    }

    

    public function getMonthWiseMDOAdminCount($orgName) {
        try {
            $table = new \CodeIgniter\View\Table();

            $query = $this->db->query('select  split_part(created_datemmyy::TEXT,\'/\', 1) AS Month, split_part(created_datemmyy::TEXT,\'/\', 2) AS YEAR ,count(*) from master_user where roles ~\'MDO_ADMIN\' group by created_datemmyy order by YEAR, Month  Desc');
            //$query = $this->db->query('select distinct(created_datemmyy), count(*) from master_user where roles ~\'MDO_ADMIN\' group by  created_datemmyy order by created_datemmyy  DESC');
    
            return $query;
        }
        catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        } 
       
    }

    
    public function getCBPAdminList($orgName) {
        try{
            $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, phone,created_date,roles');
        $builder->join(' master_organization','master_organization.root_org_id = master_user.root_org_id');
        $builder->like('roles', 'CBP_ADMIN');
        if($orgName != '')
        {
            $builder->where('org_name',$orgName);
            
        }
        $query = $builder->get();
    
        return $query;
    }
    catch (\Exception $e) {
        throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
    } 
    }

    
    public function getCreatorList($orgName) {
        try{
            $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, phone,created_date,roles');
        $builder->join(' master_organization','master_organization.root_org_id = master_user.root_org_id');
        $builder->like('roles', 'CONTENT_CREATOR');
        if($orgName != '')
        {
            $builder->where('org_name',$orgName);
        }
        $query = $builder->get();
    
        return $query;
    }
    catch (\Exception $e) {
        throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
    } 
    }
    

    public function getReviewerList($orgName) {
        try{
            $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, phone,created_date,roles');
        $builder->join(' master_organization','master_organization.root_org_id = master_user.root_org_id');
        $builder->like('roles', 'CONTENT_REVIEWER');
        if($orgName != '')
        {
            $builder->where('org_name',$orgName);
        }
        $query = $builder->get();
    
        return $query;
    }
    catch (\Exception $e) {
        throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
    } 
    }
    

    public function getPublisherList($orgName) {
        try{
            $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, phone,created_date,roles');
        $builder->join(' master_organization','master_organization.root_org_id = master_user.root_org_id');
        $builder->like('roles', 'CONTENT_PUBLISHER');
        if($orgName != '')
        {
            $builder->where('org_name',$orgName);
        }
        $query = $builder->get();
    
        return $query;
    }
    catch (\Exception $e) {
        throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
    } 
    }
    

    public function getEditorList($orgName) {
       try{

       $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, phone,created_date,roles');
        $builder->join(' master_organization','master_organization.root_org_id = master_user.root_org_id');
        $builder->like('roles', 'EDITOR');
        if($orgName != '')
        {
            $builder->where('org_name',$orgName);
        }
        $query = $builder->get();
    
        return $query;
    }
    catch (\Exception $e) {
        throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
    } 
    }
    

    public function getFracAdminList($orgName) {
       try{

        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, phone,created_date,roles');
        $builder->join(' master_organization','master_organization.root_org_id = master_user.root_org_id');
        $builder->like('roles', 'FRAC_ADMIN');
        if($orgName != '')
        {
            $builder->where('org_name',$orgName);
        }
        $query = $builder->get();
    
        return $query;
    }
    catch (\Exception $e) {
        throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
    } 
    }

    public function getFracCompetencyMemberList($orgName) {
        try {
            $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, phone,created_date,roles');
        $builder->join(' master_organization','master_organization.root_org_id = master_user.root_org_id');
        $builder->like('roles', 'FRAC_COMPETENCY_MEMBER');
        if($orgName != '')
        {
            $builder->where('org_name',$orgName);
        }
        $query = $builder->get();
    
        return $query;
    }
    catch (\Exception $e) {
        throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
    } 
    }

    public function getFRACL1List($orgName) {
        try{
            $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, phone,created_date,roles');
        $builder->join(' master_organization','master_organization.root_org_id = master_user.root_org_id');
        $builder->like('roles', 'FRAC_REVIEWER_L1');
        if($orgName != '')
        {
            $builder->where('org_name',$orgName);
        }
        $query = $builder->get();
    
        return $query;
    }
    catch (\Exception $e) {
        throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
    } 
    }

    public function getFRACL2List($orgName) {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, phone,created_date,roles');
        $builder->join(' master_organization','master_organization.root_org_id = master_user.root_org_id');
        $builder->like('roles', 'FRAC_REVIEWER_L2');
        if($orgName != '')
        {
            $builder->where('org_name',$orgName);
        }
        $query = $builder->get();
    
        return $query;
    }

    public function getIFUMemberList($orgName) {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, phone,created_date,roles');
        $builder->join(' master_organization','master_organization.root_org_id = master_user.root_org_id');
        $builder->like('roles', 'IFU_MEMBER');
        if($orgName != '')
        {
            $builder->where('org_name',$orgName);
        }
        $query = $builder->get();
    
        return $query;
    }

    public function getPublicList($orgName) {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, phone,created_date,roles');
        $builder->join(' master_organization','master_organization.root_org_id = master_user.root_org_id');
        $builder->like('roles', 'PUBLIC');
        if($orgName != '')
        {
            $builder->where('org_name',$orgName);
        }
        $query = $builder->get();
    
        return $query;
    }

    public function getSPVAdminList($orgName) {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, phone,created_date,roles');
        $builder->join(' master_organization','master_organization.root_org_id = master_user.root_org_id');
        $builder->like('roles', 'SPV_ADMIN');
        if($orgName != '')
        {
            $builder->where('org_name',$orgName);
        }
        $query = $builder->get();
    
        return $query;
    }

    public function getStateAdminList($orgName) {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, phone,created_date,roles');
        $builder->join(' master_organization','master_organization.root_org_id = master_user.root_org_id');
        $builder->like('roles', 'STATE_ADMIN');
        if($orgName != '')
        {
            $builder->where('org_name',$orgName);
        }
        $query = $builder->get();
    
        return $query;
    }

    public function getWATMemberList($orgName) {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, phone,created_date,roles');
        $builder->join(' master_organization','master_organization.root_org_id = master_user.root_org_id');
        $builder->like('roles', 'WAT_MEMBER');
        if($orgName != '')
        {
            $builder->where('org_name',$orgName);
        }
        $query = $builder->get();
    return $query;
    }

    

}

?>