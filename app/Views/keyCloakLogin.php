<html>
<head>
        <script type="text/javascript" src="<?php echo base_url('public/assets/keycloak_client_adaptor/dist/keycloak.js');?>"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/js-cookie@beta/dist/js.cookie.min.js"></script>
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
                            alert('Show error page'); 
                        }
            }).catch(function() {
                    alert('failed to initialize');
            });
        }
    </script>
</head>
<body onload="initKeycloak()">
    <!-- your page content goes here -->
     <?php  
           // echo $this->config->item('base_url_other')."/Training/register?email=".$userID ; die ; 
     ?> 
</body>
</html>