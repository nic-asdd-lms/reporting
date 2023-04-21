
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>iGOT Reports</title>
    <meta name="description" content="The small framework with powerful features">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/jpg" href="/assets/images/karmayogiLogo_thumbnail.jpg">
    <!-- Datatable CSS -->
    <link href='//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css' rel='stylesheet' type='text/css'>

    <!-- jQuery Library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Datatable JS -->
    <script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <!-- STYLES -->

    <style {csp-style-nonce}>
        header {
        background-color: rgba(247, 248, 249, 1);
        /* padding: .4rem .4rem; */
    }

    .menu {
        /* padding: .4rem .4rem; */
    }

    header ul {
        border-bottom: 1px solid rgba(242, 242, 242, 1);
        list-style-type: none;
        margin: 0;
        overflow: hidden;
        /* padding: .65rem .65rem; */
        text-align: right;
    }

    header li {
        display: inline-block;
    }

    header li a {
        border-radius: 5px;
        color: rgba(0, 0, 0, .5);
        display: block;
        height: 44px;
        text-decoration: none;
    }

    header li.menu-item a {
        border-radius: 5px;
        margin: 25px 0;
        height: 55px;
        line-height: 36px;
        padding: .65rem .65rem;
        text-align: center;
    }

    header li.menu-item a:hover,
    header li.menu-item a:focus {
        background-color:rgba(239, 149, 30, 0.17);
            color: rgb(239, 149, 30);
        
    }

    header .logo {
        float: left;
        height: 100px;
        padding: 0;
        color: rgba(221, 72, 20, .6);
    }

    header .menu-toggle {
        display: none;
        float: right;
        font-size: 2rem;
        font-weight: bold;
    }

    header .menu-toggle button {
        background-color: rgba(221, 72, 20, .6);
        border: none;
        border-radius: 3px;
        color: rgba(255, 255, 255, 1);
        cursor: pointer;
        font: inherit;
        font-size: 1.3rem;
        height: 36px;
        padding: 0;
        margin: 11px 0;
        overflow: visible;
        width: 40px;
    }

    header .menu-toggle button:hover,
    header .menu-toggle button:focus {
        background-color: rgba(221, 72, 20, .8);
        color: rgba(255, 255, 255, .8);
    }

    header .heroe {
        margin: 0 auto;
        max-width: 1100px;
        padding: 1rem 1.75rem 1.75rem 1.75rem;
    }

    header .heroe h1 {
        font-size: 2.5rem;
        font-weight: 500;
    }

    header .heroe h2 {
        font-size: 1.5rem;
        font-weight: 300;
    }
    .title {
        font-size: 40px;
        font-weight: 1000;
        padding: 1rem;
    }
@media (max-width: 629px) {
        header ul {
            padding: 0;
        }

        header .menu-toggle {
            padding: 0 1rem;
        }

        header .menu-item {
            background-color: rgba(244, 245, 246, 1);
            border-top: 1px solid rgba(242, 242, 242, 1);
            margin: 0 15px;
            width: calc(100% - 30px);
        }

        header .menu-toggle {
            display: block;
        }

        header .hidden {
            display: none;
        }

        header li.menu-item a {
            background-color: rgba(221, 72, 20, .1);
        }

        header li.menu-item a:hover,
        header li.menu-item a:focus {
            background-color:rgba(239, 149, 30, 0.17);
            color: rgb(239, 149, 30);
        }
    }
    .app-title {
        font-size: 40px;
        font-weight: 1000;
        padding: 1rem;
        color: rgb(38, 64, 146);
        vertical-align: middle;
    }
    .div-title {
        font-size: 40px;
        font-weight: 1000;
        width: 70%;
    }
    

    
    </style>
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
            echo "<li class='menu-item '><a href=".base_url('/').">Home</a></li>
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