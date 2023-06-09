<html>
<head>
        <script type="text/javascript" src="<?php echo ASSETS_URL.'keycloak_client_adaptor/dist/keycloak.js'?>"></script>
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
                        //  alert(keycloak);

                        var subject = keycloak.subject ; 
                        
                        myarr = subject.split(":");
                        Cookies.set('uid', myarr[2]);
                        Cookies.set('token',keycloak.token);
                        Cookies.set('refreshToken',keycloak.refreshToken);
                        //alert(keycloak.token);
                        // Cookies.set('role', 'SPV_ADMIN');
                        //Cookies.set('callback',JSON.stringify(keycloak.tokenParsed.resource_access.php_service.permission));
                        if(authenticated){
                            //document.getElementById("test").innerHTML = Cookies.get('uid');
                            console.log('Init Success (' + (authenticated ? 'Authenticated token : '+JSON.stringify(keycloak) : 'Not Authenticated') + ')');
                            window.location.replace("/login");
                        }
                        else 
                        {
                            window.location.replace("/unauthorized");
                        }
                }).error(function() {
                    alert('failed to initialize');
             });
            }
         </script>
</head>
<body onload="initKeycloak()">
    <!-- your page content goes here -->

     <?php  
           //   echo $this->config->item('base_url_other')."/Training/register?email=".$userID ;
           //   die ; 
     ?>

</body>
</html>