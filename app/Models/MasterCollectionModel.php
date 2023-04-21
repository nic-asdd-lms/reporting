<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterCollectionModel extends Model
{
    public function __construct() {
        parent::__construct();
        //$this->load->database();
        $db = \Config\Database::connect();
    }

    public function getCollection() {
try {
    $builder = $this->db->table('master_curated_collection');
    $builder->select('curated_id, curated_name');
    $builder->orderBy('curated_name');
    $query = $builder->get();

   return $query->getResult();
}
catch (\Exception $e) {
    throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
} 
        
    }

    public function getCollectionName($course_id) {
        try {
            $builder = $this->db->table('master_curated_collection');
            $builder->select('curated_name');
            $builder->where('curated_id',$course_id);
            $query = $builder->get();
        
            //echo $course_id,json_encode($query->getResult());
            return $query->getRow()->curated_name;
        }
        catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        } 
        
    }
    

}
?>