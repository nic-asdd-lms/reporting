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

       $query = $this->db->query('select distinct ms_id, ms_name from master_org_hierarchy');
       return $query->getResult();
    }

    public function getDepartment($ministry) {
      $sql = 'select distinct dept_id, dept_name from master_org_hierarchy where ms_id =\''.$ministry.'\';' ;
      $query =  $this->db->query($sql);

      return $query->getResult();
    }

    public function getOrganisation($dept) {
      $sql = 'select distinct org_id, org_name from master_org_hierarchy where dep_id =\''.$dept.'\';' ;
      $query =  $this->db->query($sql);
      return $query->getResult();
    }

    

}
?>