<!DOCTYPE html>

<html>

<head>



  <!-- Latest CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

  <script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>

  <script src="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css"></script>
  <script src="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lato">

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <link href="<?php echo ASSETS_URL . 'css/dashboard_style.css' ?>" rel="stylesheet" type="text/css">

</head>

<body>

  <section>

  <div class="report-date">
            <?php echo $lastUpdated ?>
        </div>
    <!-- <div class="div-button"> -->
    <!-- <label class="subtitle"><?php //echo $lastUpdated ?></label> -->
    <?php
    if ($back)
      echo '<a class="btn btn-info back-button" href="' . base_url($backUrl) . '"  > <span class="glyphicon glyphicon-arrow-left"></span> </a>';

    ?>
    <!-- </div> -->


    <label class="title">
      <?php echo $reportTitle ?>
    </label>
    <div class="dashboard-summary">
      <label class="tab-label">Learner Overview</label>
      <hr />
      <table>
        <tr>
          <td style="width:50%;padding-left: 80px;">
            <div class="table-container">
              <?php
              echo $learner_overview;
              ?>
            </div>
          </td>
          <td style="width:50%;padding-left: 80px;">
            <div class="chart-container" style="width:500px">

              <div class="pie-chart-container">

                <canvas id="pie-chart"></canvas>

              </div>

            </div>
          </td>
        </tr>
      </table>


    </div>
    <div class="dashboard-summary">
      <label class="tab-label">Current Month Overview</label>
      <hr />
      <table>
        <tr>
          <td style="width:50%;padding-left: 80px;">
            <div class="table-container">
              <?php
              echo $month_overview;
              ?>
            </div>
          </td>
          <td style="width:50%;padding-left: 80px;">
            <div class="chart-container" style="width:500px">

              <div class="pie-chart-container">

                <canvas id="month-pie-chart"></canvas>

              </div>

            </div>
          </td>
        </tr>
      </table>
    </div>

    <div class="dashboard-summary">
      <label class="tab-label"><?php echo $title; ?></label>
      <hr />
      <div class="table-container">
        <?php
        echo $overview;
        ?>
      </div>
      <div class="chart-container">

        <div class="pie-chart-container">



        </div>

      </div>
    </div>



  </section>


  <!-- javascript -->
  <script>
    $(document).ready(function () {

      $('#tbl-overview').DataTable({
        autoWidth: true,
        processing: true,
        serverSide: false
        // ajax: '<?php echo base_url('/dashboard') ?>'
        //columns: ['status', 'users']
      });
    }

    );



    $(function () {



      /*------------------------------------------

      --------------------------------------------

      Get the Pie Chart Canvas 

      --------------------------------------------

      --------------------------------------------*/

      var cData = JSON.parse(`<?php echo $chart_data; ?>`);

      var ctx = $("#pie-chart");
      var monthctx = $("#month-pie-chart");



      /*------------------------------------------

      --------------------------------------------

      Pie Chart Data 

      --------------------------------------------

      --------------------------------------------*/

      var data = {

        labels: cData.label,

        datasets: [

          {

            label: "Users Count",

            data: cData.data,

            backgroundColor: [
              "#68bf7c",
              "#f4a05a",
              "#d74d5a"
            ],

            borderColor: [
              "#68bf7c",
              "#f4a05a",
              "#d74d5a"
            ],

            borderWidth: [1, 1, 1, 1, 1, 1, 1]

          }

        ]

      };

      var monthdata = {

        labels: cData.monthlabel,

        datasets: [

          {

            label: "Users Count",

            data: cData.monthdata,

            backgroundColor: [
              "#68bf7c",
              "#f4a05a",
              "#d74d5a"
            ],

            borderColor: [
              "#68bf7c",
              "#f4a05a",
              "#d74d5a"
            ],

            borderWidth: [1, 1, 1, 1, 1, 1, 1]

          }

        ]

      };



      var options = {

        responsive: true,

        title: {

          display: true,

          position: "top",

          text: "Learner Overview",

          fontSize: 16,

          fontColor: "#111"

        },

        legend: {

          display: true,

          position: "left",

          labels: {

            fontColor: "#333",

            fontSize: 16

          }

        },
        animation: {
          onComplete: () => {
            delayed = true;
          },
          delay: (context) => {
            let delay = 0;
            if (context.type === 'data' && context.mode === 'default' && !delayed) {
              delay = context.dataIndex * 300 + context.datasetIndex * 100;
            }
            return delay;
          },
        }

      };
      var monthoptions = {

        responsive: true,

        title: {

          display: true,

          position: "top",

          text: "Current Month Overview",

          fontSize: 16,

          fontColor: "#111"

        },

        legend: {

          display: true,

          position: "left",

          labels: {

            fontColor: "#333",

            fontSize: 16

          }

        }

      };



      /*------------------------------------------
  
      --------------------------------------------
  
      create Pie Chart class object
  
      --------------------------------------------
  
      --------------------------------------------*/

      var chart1 = new Chart(ctx, {

        type: "doughnut",

        data: data,

        options: options

      });
      var monthchart = new Chart(monthctx, {

        type: "doughnut",

        data: monthdata,

        options: monthoptions

      });



    });

  </script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.js"></script>

  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

</body>