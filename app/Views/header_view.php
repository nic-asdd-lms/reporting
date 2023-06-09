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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lato">
    <!-- ASSETS -->
    <link rel="shortcut icon" type="image/jpg" href="<?php echo ASSETS_URL . 'images/karmayogiLogo_thumbnail.jpg'; ?>">
    <link href="<?php echo ASSETS_URL . 'css/header_style.css' ?>" rel="stylesheet" type="text/css">
</head>
<header>

    <div class="menu">
        <ul>
            <li class="logo">

                <div class="header-title">
                    <img src="<?php echo ASSETS_URL . 'images/karmayogiLogo.svg'; ?>" />
                </div>
            </li>
            <li class="logo">

                <div class="header-title">
                    <label class="app-title">iGOT REPORTING</label>
                </div>
            </li>
            <li class="menu-toggle">
                <button onclick="toggleMenu();">&#9776;</button>
            </li>
            <?php
            $session = \Config\Services::session();
                    
            if ($session->get('logged_in') == true) {
                if ($session->get('role') == 'SPV_ADMIN') {
                    echo "<li class='menu-item '><a href=" . base_url('/home') . ">Home</a></li>
                        <li class='menu-item '><a href='" . base_url('/dashboard/spv') . "' >Dashboard</a></li>
                        <li class='menu-item '><a href='" . base_url('/logout') . "' >Logout</a></li>";
                } else if ($session->get('role') == 'MDO_ADMIN') {
                    echo "<li class='menu-item '><a href=" . base_url('/home') . ">Home</a></li>
                         <li class='menu-item '><a href='" . base_url('/logout') . "' >Logout</a></li>";
                } else if ($session->get('role') == 'DOPT_ADMIN') {
                    echo "<li class='menu-item '><a href=" . base_url('/dashboard/dopt?ati=&program=') . ">Home</a></li>
                            <li class='menu-item '><a href='" . base_url('/logout') . "' >Logout</a></li>";

                }

            }
            ?>


        </ul>

    </div>


    <!-- <div class="heroe">

<h1>Welcome to CodeIgniter <?= CodeIgniter\CodeIgniter::CI_VERSION ?></h1>

<h2>The small framework with powerful features</h2>

</div> -->





</header>