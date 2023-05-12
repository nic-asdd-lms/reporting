<?php 


if(!function_exists('session_exists')){
    function session_exists(){
        $session = \Config\Services::session();
        
        if ($session->get('logged_in') == true)
            return true;
        else
            return false;
    }
}

?>