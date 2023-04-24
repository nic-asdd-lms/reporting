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

    
    <!-- ASSETS -->
  <link href="<?php echo base_url('/assets/css/login_style.css');?>" rel="stylesheet" type="text/css">

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

