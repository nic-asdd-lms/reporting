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

    
    public function getOrgName($org_id) {
        $builder = $this->db->table('master_organization');
        $builder->select('org_name');
        $builder->where('org_id', $org_id);
        $query = $builder->get();
        
        return $query->getRow()->org_name;
    }

    
}
?>