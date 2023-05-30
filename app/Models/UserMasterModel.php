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
 function validAPIUser($username,$password){
    $builder = $this->db->table('api_users');
    $builder->select('*');
    $builder->where('username', $username);
    $builder->where('password', $password);
    
    $query = $builder->get();

    if ($query->getNumRows() > 0) {
        return true;
    } 
    else {
        return false;
    }
}

 function getUserIDbyEmail($email){
    $builder = $this->db->table('master_user');
    $builder->select('user_id');
    $builder->where('email', $email);
    
    $query = $builder->get();

    if ($query->getNumRows() > 0) {
        return $query->getResultArray();
    } else {
    return false;
    }
}

//SELECT enrolment_id, certificate_status, completed_on, completed_onmmyy, completion_percentage, completion_status, course_id, enrolled_date, enrolled_datemmyy, rating, user_id
//	FROM public.user_course_enrolment where user_id='0bb26551-dfeb-4fbd-9c37-96016894b843';

 function login($email) {
    
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
function read_user_information($username) {

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