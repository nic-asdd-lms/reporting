<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterStructureModel extends Model
{
    public function __construct() {
        parent::__construct();
        //$this->load->database();
        $db = \Config\Database::connect();
    }



    public function getMinistry() {
      try{
        $builder =$this->db->table('master_org_hierarchy');
        $builder->select('ms_id, ms_name');
        $builder->where('ms_type','ministry');
        $builder->orderBy('ms_name');
        $builder->distinct();
        return $builder->get()->getResult();
      }
      catch (\Exception $e) {
          throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
      } 
     
      
    }
    
    public function getState() {
      try{
        $builder =$this->db->table('master_org_hierarchy');
        $builder->select('ms_id, ms_name');
        $builder->where('ms_type','state');
        $builder->orderBy('ms_name');
        $builder->distinct();
        return $builder->get()->getResult();
      }
      catch (\Exception $e) {
          throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
      } 
     
      
    }
    public function getDepartment($ministry) {
      try {
        $builder =$this->db->table('master_org_hierarchy');
        $builder->select('dept_id, dept_name');
        $builder->where('ms_id',$ministry);
        $builder->orderBy('dept_name');
        $builder->distinct();
        
        return $builder->get()->getResult();
      }
      catch (\Exception $e) {
          throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
      } 
     
      
    }

    public function getOrganisation($dept) {
      try {
        $builder =$this->db->table('master_org_hierarchy');
        $builder->select('org_id, org_name');
        $builder->where('dept_id',$dept);
        $builder->orderBy('org_name');
        $builder->distinct();
        
        return $builder->get()->getResult();
      }
      catch (\Exception $e) {
          throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
      } 
     
      
    }


    public function getMinistryStateName($ms_id) {
      try {
        $builder =$this->db->table('master_org_hierarchy');
        $builder->select('ms_name');
        $builder->where('ms_id',$ms_id);
        return $builder->get()->getRow()->ms_name;
      }
      catch (\Exception $e) {
          throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
      } 
     
      
    }
    

    public function getDeptName($dept_id) {
      try {
        $builder =$this->db->table('master_org_hierarchy');
        $builder->select('dept_name');
        $builder->where('dept_id',$dept_id);
        return $builder->get()->getRow()->dept_name;
      }
      catch (\Exception $e) {
          throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
      } 
     
      
    }

    public function getHierarchy($ministry,$limit, $offset, $search, $orderBy, $orderDir) {
      try {
        $builder =$this->db->table('master_org_hierarchy');
        $builder->select('dept_name, org_name');
        $builder->where('ms_id',$ministry);
        if ($search != '') {
          $builder->where("(org_name LIKE '%" . strtolower($search) . "%' OR org_name LIKE '%" . strtolower($search) . "%' OR org_name LIKE '%" . ucfirst($search) . "%'
          OR dept_name LIKE '%" . strtolower($search) . "%' OR dept_name LIKE '%" . strtoupper($search) . "%' OR dept_name LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);

          
      }

      $builder->orderBy((int) $orderBy + 1, $orderDir);
      if ($limit != -1)
          $builder->limit($limit, $offset);
      $builder->distinct();
        
        return $builder->get();
      }
      catch (\Exception $e) {
          throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
      } 
     
      
    }

    public function getM_D_O($org) {
      $builder =$this->db->table('master_org_hierarchy');
        $builder->select('ms_type, ms_id, ms_name, dept_id, dept_name, org_id, org_name');
        $builder->where('org_name', $org);
        $builder->distinct();
        return $builder->get()->getResult();
    }

    public function getMDOHierarchy($org) {
                    
      try {
        $builder =$this->db->table('master_org_hierarchy');
        $builder->select('ms_type,ms_id, ms_name, dept_id, dept_name, org_id, org_name');
        $builder->where('org_name', $org);
        $builder->distinct();
        return $builder->get()->getResult();
        if($builder->get()->getNumRows() == 0)
        {
         
          $builder->select('ms_id, ms_name, dept_id, dept_name');
          $builder->where('dept_name', $org);
          $builder->distinct();
          if($builder->get()->getResultArray() == 0)
          {
            
            $builder->select('ms_id, ms_name');
            $builder->where('ms_name', $org);
            $builder->distinct();
          
          }
        }
        return $builder->get()->getResult();
      }
      catch (\Exception $e) {
          throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
      } 
     
      
    }


}
?>