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

       $query = $this->db->query('select distinct ms_id, ministry_state_name from master_structure');
       return $query->getResult();
    }

    public function getDepartment($postData) {
      $sql = 'select distinct dep_id, dep_name from master_structure where ms_id ='.$postData['ms_id'] ;
      $query =  $this->db->query($sql);

      return $query->getResult();
    }

    public function getOrganisation($postData) {
      $sql = 'select distinct org_id, org_name from master_structure where dep_id ='.$postData['dep_id'] ;
      $query =  $this->db->query($sql);

      return $query->getResult();
    }

}
?>