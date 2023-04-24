
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>iGOT Reports</title>
    <meta name="description" content="The small framework with powerful features">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Datatable CSS -->
    <link href='//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css' rel='stylesheet' type='text/css'>

    <!-- jQuery Library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Datatable JS -->
    <script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
   
    <!-- ASSETS -->
    <link rel="shortcut icon" type="image/jpg" href="<?php echo base_url('assets/images/karmayogiLogo_thumbnail.jpg');?>">
    <link href="<?php echo base_url('/assets/css/header_style.css');?>" rel="stylesheet" type="text/css">
</head>
    <header>
    
<div class="menu">
    <ul>
        <li class="logo">
        
            <div class="title">
                <img  src="/assets/images/karmayogiLogo.svg" alt="iGOT Reporting"/> 
            </div>
        </li>
        <li class="logo">
        
            <div class="title">
                <label class="app-title">iGOT Reporting</label> 
            </div>
        </li>
        <li class="menu-toggle">
            <button onclick="toggleMenu();">&#9776;</button>
        </li>
        <?php 
        $session = \Config\Services::session();
		
       if ($session->get('logged_in') == true) {
            echo "<li class='menu-item '><a href=".base_url('/home').">Home</a></li>
            <li class='menu-item '>Dashboard</li>
            <li class='menu-item '><a href='".base_url('/logout')."' >Logout</a></li>";
        }
        ?>
        

    </ul>
</div>

<!-- <div class="heroe">

<h1>Welcome to CodeIgniter <?= CodeIgniter\CodeIgniter::CI_VERSION ?></h1>

<h2>The small framework with powerful features</h2>

</div> -->





</header>
