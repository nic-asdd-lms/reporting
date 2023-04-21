<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>iGOT Reports</title>
    <meta name="description" content="The small framework with powerful features">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="shortcut icon" type="image/jpg" href="/assets/images/karmayogiLogo_thumbnail.jpg"> -->
    <!-- Datatable CSS -->
    <link href='//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css' rel='stylesheet' type='text/css'>

    <!-- jQuery Library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Datatable JS -->
    <script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <!-- STYLES -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <style {csp-style-nonce}>
    * {
        transition: background-color 300ms ease, color 300ms ease;
    }

    *:focus {
        outline: none;
    }

    html,
    body {
        color: rgba(33, 37, 41, 1);
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji";
        font-size: 16px;
        margin: 0;
        padding: 0;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        text-rendering: optimizeLegibility;
    }

    
    section {
        margin: 0 auto;
        max-width: 1100px;
        padding: 2.5rem 1.75rem 3.5rem 1.75rem;
    }

    section h1 {
        margin-bottom: 2.5rem;
    }

    section h2 {
        font-size: 120%;
        line-height: 2.5rem;
        padding-top: 1.5rem;
    }

    section pre {
        background-color: rgba(247, 248, 249, 1);
        border: 1px solid rgba(242, 242, 242, 1);
        display: block;
        font-size: .9rem;
        margin: 2rem 0;
        padding: 1rem 1.5rem;
        white-space: pre-wrap;
        word-break: break-all;
    }

    section code {
        display: block;
    }

    section a {
        color: rgba(221, 72, 20, 1);
    }

    section svg {
        margin-bottom: -5px;
        margin-right: 5px;
        width: 25px;
    }

    .further {
        background-color: rgba(247, 248, 249, 1);
        border-bottom: 1px solid rgba(242, 242, 242, 1);
        border-top: 1px solid rgba(242, 242, 242, 1);
    }

    .further h2:first-of-type {
        padding-top: 0;
    }

    footer {
        background-color: rgba(221, 72, 20, .8);
        text-align: center;
    }

    footer .environment {
        color: rgba(255, 255, 255, 1);
        padding: 2rem 1.75rem;
    }

    footer .copyrights {
        background-color: rgba(62, 62, 62, 1);
        color: rgba(200, 200, 200, 1);
        padding: .25rem 1.75rem;
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

    .tab {
        overflow: hidden;
        border: 1px solid #ccc;
        background-color: #f1f1f1;
    }

    /* Style the buttons that are used to open the tab content */
    .tab button {
        background-color: inherit;
        float: left;
        border: none;
        outline: none;
        cursor: pointer;
        padding: 14px 16px;
        transition: 0.3s;
    }

    /* Change background color of buttons on hover */
    .tab button:hover {
        background-color: #ddd;
    }

    /* Create an active/current tablink class */
    .tab button.active {
        background-color: #ccc;
    }

    /* Style the tab content */
    .tabcontent {
        display: none;
        padding: 6px 12px;
        border: 1px solid #ccc;
        border-top: none;
    }

    .submitbutton {
        width: 80%;
        padding: 10px;
        margin: 15px;
        text-align: center;
    }

    .login-container {
        text-align: center;
        
    }
    label {
        font-weight: 400;
    }

    .login-div {
        align-items: center;
        align-self: center;
        background-color: #f1f1f1;
        width: 70%;
        margin: 30px;
        display: grid;
        padding: 30px;
    }
    </style>
</head>

<body  onload="initKeycloak()">
<?= validation_list_errors() ?>
    <!-- HEADER: MENU + HEROE SECTION -->
   

    <!-- CONTENT -->

    <section>



        <div id="body" style="display: grid">
        <div class='login-container'>
	<div class='row'>
		<div class='col-md-8 col-md-offset-2'>
			<br/> <br/>

 			<h3 align="center">Login  </h3>
		</div>
	</div>
	<div class='row'>
		<div class='col-md-8 col-md-offset-2 imgcontainer ' >
			<form class="form-horizontal login_form" action="<?php echo base_url('/user_login_process');?>" method="post">
               
			<?php //echo form_open('login//user_login_process'); ?>
			
			<?php
			echo "<div class='error_msg'>";
			if (isset($error_message)) {
			echo $error_message;
			    }
			//echo validation_errors();
			echo "</div>";
			?>	
       
			<div class='row'>
					<div class='form-group col-md-12'>
						
					</div>
			</div>
			<div class='form-group'>
					<label class='control-label col-md-4'>Username: </label>
					<div class='col-md-6'><input type="text" class="form-control" placeholder="Enter Username" name="username" value="<?= set_value('username') ?>" required></div>
				</div>
				<div class='form-group'>
					<label class='control-label col-md-4'>Password: </label>
					<div class='col-md-6'><input type="password" class="form-control" placeholder="Enter Password" name="password" value="<?= set_value('password') ?>" required></div>
				</div>
				<div class='form-group'>
					<div class='col-md-4 col-md-offset-4'>
						<button type="submit" class="btn btn-primary login_button">Login</button>
					</div>
			</div>
			<?php echo form_close(); ?>
			</form>
		</div>
	</div>
</div>
            

            </div>
        </div>


    </section>
 
    <!-- FOOTER: DEBUG INFO + COPYRIGHTS -->

    

    <!-- SCRIPTS -->

    <script>
   function initKeycloak() { 
            const keycloak = Keycloak('/assets/keycloak.json');
            const initOptions = {
                responseMode: 'fragment',
                flow: 'standard',
                onLoad: 'login-required'
            };
            keycloak.init(initOptions).success(function(authenticated) {
                        //alert(authenticated ? 'authenticated' : 'not authenticated');
                        if(authenticated){
                            window.location.replace("/login");
                        }
                        else 
                        {
                            alert('Not an iGOT user'); 
                        }
            }).catch(function() {
                    alert('failed to initialize');
            });
        }
    </script>

    <!-- -->

</body>

</html>

