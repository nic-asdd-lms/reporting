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
try{
    $query = $this->db->query('select program_id, program_name from master_program order by program_name');
    return $query->getResult();
}
        catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        } 
      
    }

    public function getProgramName($program_id) {
        try {
            $result = $this->db->query('select  program_name from master_program where program_id = \''.$program_id.'\'')->getRow()->program_name;
            return $result;
        }
        catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        } 
       
    }

    public function getCommitProgramName($program_id) {
        try {
            $result = $this->db->query('select cbp_name from commit_programs_enrolment where courseid = \''.$program_id.'\'')->getRow()->cbp_name;
            return $result;
        }
        catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        } 
       
    }

    public function programSearch($search_key) {
        try {
            $builder = $this->db->table('master_program');
        $builder->select('program_id, program_name, org_name');
        $builder->where('(SIMILARITY(program_name,\''.$search_key.'\') > 0.1)', NULL, FALSE);
        $builder->where('program_status', 'Live');
        $builder->orderBy('SIMILARITY(program_name,\''.$search_key.'\') desc');
        $query = $builder->get();
        
        return $query->getResult();
        }
        catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        } 
        
    }

    

}

?>