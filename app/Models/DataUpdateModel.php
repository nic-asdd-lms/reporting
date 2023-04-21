<?php

namespace App\Models;

use CodeIgniter\Model;

class DataUpdateModel extends Model
{
    public function __construct() {
        parent::__construct();
        //$this->load->database();
        $db = \Config\Database::connect();
    }

    public function getReportLastUpdatedTime() {
        try {
            $builder = $this->db->table('data_update');
            $builder->select('last_updated');
            $query = $builder->get();
        
           return $query->getRow()->last_updated;
        }
        catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        } 
        
    }

    
    

}
?>