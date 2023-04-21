function fill(Value) {
    //Assigning value to "search" div in "search.php" file.
    $('#search').val(Value);
    //Hiding "display" div in "search.php" file.
    $('#display').hide();
 }

$(document).ready(function() {
//On pressing a key on "Search box" in "search.php" file. This function will be called.
$("#search").keyup(function() {
   //Assigning search box value to javascript variable named as "name".
   var name = $('#search').val();
   var action = 'search';

   //Validating, if "name" is empty.
   if (name == "") {
       //Assigning empty value to "display" div in "search.php" file.
       $("#display").html("");
   }
   //If name is not empty.
   else {
       //AJAX is called.
       $.ajax({
           //AJAX type is "Post".
           type: "POST",
           //Data will be sent to "ajax.php".
           url: "<?php echo base_url('/action'); ?>",
           //Data, that will be sent to "ajax.php".
           data: {
               //Assigning value of "name" into "search" variable.
               search: name,
               action: action
           },
           dataFilter:'JSON',
           //If result found, this funtion will be called.
           success: function(data) {
               //Assigning result to "display" div in "search.php" file.
               var html='<ul>';
                
                   
                   //Fetching result from database.
                   for (var count = 0; count < data.length; count++){
                    html += '<li onclick='+fill(data[count].org_name)+'><a>'+data[count].org_name+'</li></a>';

                   
                       
                
                   
                }
                html+='</ul>';
            },
                
               
            //    $("#display").html(html).show();
           });
       }
   });
});