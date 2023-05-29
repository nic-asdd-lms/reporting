<?php
namespace App\Models;

use CodeIgniter\Model;

Class UserMasterModel extends Model {

public function __construct() {
    parent::__construct();
    //$this->load->database();
    $db = \Config\Database::connect();
}


// Read data using username and password

public function login($email) {
    
    $builder = $this->db->table('user_account');
    $builder->select('*');
    $builder->where('username', $email);
    
    $query = $builder->get();

// $condition = "username =" . "'" . $data['username'] . "' AND " . "password =" . "'" . $data['password']."'" ;
// $this->db->select('*');
// $this->db->from('user_master');
// $this->db->where($condition);
// $this->db->limit(1);
// $query = $this->db->get();

if ($query->getNumRows() > 0) {
    
		
return true;
} else {
    
return false;
}
}

// Read data from database to show data in admin page
public function read_user_information($username) {

    $builder = $this->db->table('user_account');
    $builder->select('*');
    $builder->where('username', $username);
    $query = $builder->get();

if ($query->getNumRows() > 0) {
return $query->getResult();
} else {
return false;
}
}
}


?>