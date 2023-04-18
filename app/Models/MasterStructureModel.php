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
      $builder =$this->db->table('master_org_hierarchy');
      $builder->select('ms_id, ms_name');
      $builder->where('ms_type','ministry');
      $builder->orderBy('ms_name');
      $builder->distinct();
      return $builder->get()->getResult();
    }
    
    public function getState() {
      $builder =$this->db->table('master_org_hierarchy');
      $builder->select('ms_id, ms_name');
      $builder->where('ms_type','state');
      $builder->orderBy('ms_name');
      $builder->distinct();
      return $builder->get()->getResult();
    }
    public function getDepartment($ministry) {
      $sql = 'select distinct dept_id, dept_name from master_org_hierarchy where ms_id =\''.$ministry.'\' order by dept_name;' ;
      $query =  $this->db->query($sql);

      return $query->getResult();
    }

    public function getOrganisation($dept) {
      $sql = 'select distinct org_id, org_name from master_org_hierarchy where dept_id =\''.$dept.'\' order by org_name;' ;
      $query =  $this->db->query($sql);
      return $query->getResult();
    }

    public function getMinistryStateName($ms_id) {
      $builder =$this->db->table('master_org_hierarchy');
      $builder->select('ms_name');
      $builder->where('ms_id',$ms_id);
      return $builder->get()->getRow()->ms_name;
    }
    

    public function getDeptName($dept_id) {
      $builder =$this->db->table('master_org_hierarchy');
      $builder->select('dept_name');
      $builder->where('dept_id',$dept_id);
      return $builder->get()->getRow()->dept_name;
    }


}
?>