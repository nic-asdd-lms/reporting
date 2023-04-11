<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterProgramModel extends Model
{
    public function __construct() {
        parent::__construct();
        //$this->load->database();
        $db = \Config\Database::connect();
    }

    public function getProgram() {

       $query = $this->db->query('select program_id, program_name from master_program order by program_name');
       return $query->getResult();
    }

    public function getProgramName($program_id) {
        $result = $this->db->query('select  program_name from master_program where program_id = \''.$program_id.'\'')->getRow()->program_name;
       return $result;
    }
    

}
?>