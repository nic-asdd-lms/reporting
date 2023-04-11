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
        $builder = $this->db->table('master_organization');
        $builder->select('org_name');
        $builder->where('root_org_id', $org_id);
        $query = $builder->get();
        
       // echo $org_id,json_encode($query);
        return $query->getRow()->org_name;
    }

    public function searchOrg($search_key) {
        $builder = $this->db->table('master_organization');
        $builder->select('org_name');
        $builder->where('root_org_id', $search_key);
        $query = $builder->get();
        
        $result = $this->db->query('SELECT org_name FROM master_organization WHERE SIMILARITY(org_name,\''.$search_key.'\') > 0.4 ;')->getResultObject();
        echo $search_key,json_encode($query);
        return $result;
    }

    
}
?>