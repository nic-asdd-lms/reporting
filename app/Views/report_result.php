

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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>

    <!-- STYLES -->
    <script src="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css"></script>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

	<script src="src/jquery.table2excel.js"></script>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    


    <!-- ASSETS -->

    <link href="<?php echo base_url('/assets/css/result_style.css');?>" rel="stylesheet" type="text/css">

</head>
    <body  onload="initKeycloak()">
        <div>
    <div class="h2">
        
        <label class="title"><?php echo $reportTitle ?></label>
        
        <label class="subtitle"><?php echo $lastUpdated ?></label>
    <?php 
    echo '<a class="btn btn-success download-button" href="'.base_url('/getExcelReport').'?'. $params.'" target="_blank" > Download Excel </a>';

    ?>
 
   
</div>
    <div class="further">

<section>
    
<?php 
echo $resultHTML;

?>

</section>

</div>
</div>
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
                        if(!authenticated){
                            alert('Not iGOT user'); 
                        }
                        
            }).catch(function() {
                    alert('failed to initialize');
            });
        }
    

  $(document).ready(function() {
    $('#tbl-result').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    } );
} );

function tableToCSV(tableID, filename ) {
 // Variable to store the final csv data
 var csv_data = [];

 // Get each row data
 var rows = document.getElementsByTagName('tr');
//  alert(rows);
 
 for (var i = 0; i < rows.length; i++) {

     // Get each column data
     var cols = rows[i].querySelectorAll('td,th');

     // Stores each csv row data
     var csvrow = [];
     for (var j = 0; j < cols.length; j++) {

         // Get the text data of each cell
         // of a row and push it to csvrow
         csvrow.push(cols[j].innerHTML);
     }

     // Combine each column value with comma
     csv_data.push(csvrow.join(","));
 }

 // Combine each row data with new line character
 csv_data = csv_data.join('\n');

 // Call this function to download csv file 
 downloadCSVFile(csv_data);

}

function downloadCSVFile(csv_data) {

 // Create CSV file object and feed
 // our csv_data into it
 CSVFile = new Blob([csv_data], {
     type: "text/csv"
 });

 // Create to temporary link to initiate
 // download process
 var temp_link = document.createElement('a');

 // Download csv file
 temp_link.download = "GfG.csv";
 var url = window.URL.createObjectURL(CSVFile);
 temp_link.href = url;

 // This link should not be displayed
 temp_link.style.display = "none";
 document.body.appendChild(temp_link);

 // Automatically click the link to
 // trigger download
 temp_link.click();
 document.body.removeChild(temp_link);
}


</script>
</body>
</html>
