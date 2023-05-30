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

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lato">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11">  </script>

    <!-- ASSETS -->
    <link href="<?php echo ASSETS_URL . 'css/result_style.css' ?>" rel="stylesheet" type="text/css">

</head>

<script>
    
    $(document).ready(function () {
        var columns = [];
        reportType = document.getElementById('reportType').value;
        var error =document.getElementById('error');
        
        $.ajax({
            
            url: "<?php echo base_url('/getReport') ?>" + '/' + reportType + '?length=1&start=0&draw=1&search[value]=&order[0][column]=&order[0][dir]=',
            success: function (data) {
                if (!data.recordsTotal){   
                    Swal.fire({
                        text: 'No matching data found !',
                        icon: 'info',
                        confirmButtonText: 'OK',
                        timer: 1500
                    });
                    error.innerHTML = 'No matching data found !';
                    //alert("No matching data found");
                }
                else{   
                    error.innerHTML = '';
                    tableData = JSON.parse(JSON.stringify(data));
                    columnNames = Object.keys(tableData.data[0]);
                    for (var i in columnNames) {
                        columns.push({
                            data: columnNames[i],
                            searchable: true
                        });
                }
                
                $('#tbl-result').DataTable({
                    autoWidth: true,
                    processing: true,
                    serverSide: true,
                    ajax: '<?php echo base_url('/getReport') ?>' + '/' + reportType,
                    columns: columns
                });
                }
            }
        });
    });
    
</script>

<body>
    <div>
        <div class="report-date">
            <?php echo $lastUpdated ?>
        </div>
        <div class="h2">

            <label class="title">
                <?php echo $reportTitle ?>
            </label>
<div class="div-button">
            <!-- <label class="subtitle"><?php //echo $lastUpdated ?></label> -->
            <?php
            echo '<a class="btn btn-info back-button" href="' . base_url('/home') . '"  > <span class="glyphicon glyphicon-arrow-left"></span> </a>';
            echo '<a class="btn btn-success download-button" href="' . base_url('/getExcelReport') . '?filter=false" target="_blank" > <span class="glyphicon glyphicon-download-alt"></span> Download Full Result </a>';
            echo '<a class="btn btn-success download-button" href="' . base_url('/getExcelReport') . '?filter=true" target="_blank" > <span class="glyphicon glyphicon-download-alt"></span> Download Filtered Result </a>';

            ?>
</div>

        </div>
        <div class="further">


            <section>
                <form id="mdoWise" class="form-horizontal login_form" action="" method="post">

                    <input type="hidden" id="reportType" value=<?php echo $reportType ?> />
                    
                    <?php
                    //echo $page;
                    echo $resultHTML;

                    ?>
                    <div class="error">
                        <label  class="error" id="error"></label>
                    </div>

                </form>
            </section>

        </div>
    </div>

</body>
</html>