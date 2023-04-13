

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>iGOT Reports</title>
    <meta name="description" content="The small framework with powerful features">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/jpg" href="/assets/karmayogiLogo_thumbnail.jpg">
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

    


    <style {csp-style-nonce}>
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
    
    h2 {
        text-align: center;
        
        color: rgb(38, 64, 146);
        font-size:30px;

        
    }

    .further {
        background-color: rgba(247, 248, 249, 1);
        border-bottom: 1px solid rgba(242, 242, 242, 1);
        border-top: 1px solid rgba(242, 242, 242, 1);
        margin-bottom: 100px;
        padding-bottom: 30px;
    }

    .further h2:first-of-type {
        padding-top: 0;
    }

    .download-button {
        float: right;
        margin-right: 20px;
    }

    .report-table {
        margin: 20px;
        background-color: rgba(239, 149, 30, 0.46);
        color:rgb(38, 64, 146);
    }
    
    .h2 {
        margin-left:100px;
        margin-top:20px;
        margin-bottom:20px;
    }

    table {
        background-color: #e26b420a;
        border-left-style: solid;
border-left-width: thin;
border-left-color: #f4e4de;
border-right-style: solid;
border-right-width: thin;
border-right-color: #f4e4de;
border-block-start-style: solid;
border-block-start-width: thin;
border-block-start-color: #e26b4259
    }

    tr .odd {
        background-color: #e26b4200;
    }
    td .sorting_1 {
        background-color: #e26b4200;
    }
    td  {
        background-color: #e26b4200;
    }

    thead .sorting{
        background-color: #e26b421c;
    }
    thead .sorting_asc{
        background-color: #e26b421c;
    }
    thead .sorting_desc{
        background-color: #e26b421c;
    }

    label {
        font-weight: 50;
        font-size:25px
    }


    </style>
</head>
    <body>
        <div>
    <div class="h2">
        <h2><?php echo $reportTitle ?><h2>
        
        <label><?php echo $lastUpdated ?></label>
    <?php 
    echo '<a class="btn btn-success download-button" href="/reporting/getExcelReport?'. $params.'" target="_blank" > Download Excel </a>';

    ?>


    
    <!-- <form class="form-horizontal login_form" action="/reporting/download-report" method="post">
    <button class="btn btn-success download-button" >Download Excel</button>
</form> -->
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