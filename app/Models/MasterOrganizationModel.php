<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterOrganizationModel extends Model
{
    public function __construct() {
        parent::__construct();
        //$this->load->database();
        $db = \Config\Database::connect();
    }


    public function getOrganizations() {
        $query = $this->db->query('select distinct root_org_id, org_name from master_organization order by org_name');
        return $query->getResult();
    }

    
    public function getOrgName($org_id) {
        try{
            $builder = $this->db->table('master_organization');
            $builder->select('org_name');
            $builder->where('root_org_id', $org_id);
            $query = $builder->get();
            
           // echo $org_id,json_encode($query);
           if($query->getRow() == null){
            echo '<script>alert("Organization not yet onboarded!");</script>';
                return null;
           }

            return $query->getRow()->org_name;
        }
        
        catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        } 
    }

    public function searchOrg($search_key) {
        try {
            $builder = $this->db->table('master_organization');
        $builder->select('org_name');
        $builder->where('root_org_id', $search_key);
        $query = $builder->get();
        
        $result = $this->db->query('SELECT org_name FROM master_organization WHERE SIMILARITY(org_name,\''.$search_key.'\') > 0.4 ;')->getResultObject();
        echo $search_key,json_encode($query);
        return $result;
        }
        catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        } 
        
    }

    public function getOrgList($limit, $offset, $search, $orderBy, $orderDir) {
        try{
            $builder = $this->db->table('master_organization');
            $builder->select('org_name');
            if ($search != '') {

                $builder->like('org_name', strtolower($search));
                $builder->orLike('org_name', strtoupper($search));
                $builder->orLike('org_name', ucfirst($search));
            }

            $builder->orderBy((int) $orderBy + 1, $orderDir);
            if ($limit != -1)
                $builder->limit($limit, $offset);
            $query = $builder->get();
            
           // echo $org_id,json_encode($query);
           

            return $query;
        }
        
        catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        } 
    }
    
}

?>