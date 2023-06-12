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
    // if ($back)
    //   echo '<a class="btn btn-info back-button" href="' . base_url($backUrl) . '"  > <span class="glyphicon glyphicon-arrow-left"></span> </a>';
    
    ?>
    <!-- </div> -->


    <label class="title">
      <?php echo $reportTitle ?>
    </label>
    <div class="dashboard-summary">
      <label class="tab-label">Summary</label>
      <hr />
      <table>
        <tr>
          <td class="dashboard-summary-item">
            <div class="dashboard-item">
              <div>
                <label class="dashboard-item-value numformat">
                  <?php echo $orgCount; ?>
                </label>
              </div>
              <div>
                <label class="dashboard-item-header">Organisations Onboarded</label>
              </div>
            </div>
          </td>
          <td class="dashboard-summary-item">
            <div class="dashboard-item">
              <div>
                <label class="dashboard-item-value numformat">
                  <?php echo $userCount; ?>
                </label>
              </div>
              <div>
                <label class="dashboard-item-header">Users Onboarded</label>
              </div>
            </div>
          </td>
          <td class="dashboard-summary-item">
            <div class="dashboard-item">
              <div>
                <label class="dashboard-item-value numformat">
                  <?php echo $courseCount; ?>
                </label>
              </div>
              <div>
                <label class="dashboard-item-header">Courses Published</label>
              </div>
            </div>
          </td>
          <td class="dashboard-summary-item">
            <div class="dashboard-item">
              <div>
                <label class="dashboard-item-value numformat">
                  <?php echo $providerCount; ?>
                </label>
              </div>
              <div>
                <label class="dashboard-item-header">Course Providers</label>
              </div>
            </div>
          </td>
          <td>
            <div class="dashboard-item">
              <!-- <hr /> -->
              <label class="dashboard-item-value numformat">
                <?php echo $contentHours; ?>
              </label>
              <label class="dashboard-item-header">Content Hours</label>
            </div>
          </td>
          
        </tr>

        <tr>
          <td>
            <div class="dashboard-item">
              <!-- <hr /> -->
              <label class="dashboard-item-value numformat">
                <?php echo $enrolmentCount; ?>
              </label>
              <label class="dashboard-item-header">Enrolments</label>
            </div>
          </td>
          <td>
            <div class="dashboard-item">
              <!-- <hr /> -->
              <label class="dashboard-item-value numformat">
                <?php echo $completionCount; ?>
              </label>
              <label class="dashboard-item-header">Completions</label>
            </div>
          </td>
          <td>
            <div class="dashboard-item">
              <!-- <hr /> -->
              <label class="dashboard-item-value numformat">
                <?php echo $uniqueEnrolmentCount; ?>
              </label>
              <label class="dashboard-item-header">Unique Users Enrolled</label>
            </div>
          </td>
          <td>
            <div class="dashboard-item">
              <!-- <hr /> -->
              <label class="dashboard-item-value numformat">
                <?php echo $uniqueCompletionCount; ?>
              </label>
              <label class="dashboard-item-header">Unique Users Completed</label>
            </div>
          </td>
          <td>
            <div class="dashboard-item">
              <!-- <hr /> -->
              <label class="dashboard-item-value numformat">
                <?php echo $learningHours; ?>
              </label>
              <label class="dashboard-item-header">Learning Hours</label>
            </div>
          </td>
          
        </tr>


      </table>
    </div>

    <div class="dashboard-summary">
      <label class="tab-label">Learner Overview</label>
      <hr />
      <table>
        <tr>
          <td style="width:50%;padding: 70px; ">
            <div class="table-container">
              <?php
              echo $learner_overview;
              ?>
            </div>
          </td>
          <td style="width:50%;padding: 70px; ">
            <div class="chart-container" style="width:500px">

              <div class="pie-chart-container">

                <canvas id="learner-pie-chart"></canvas>

              </div>

            </div>
          </td>
        </tr>
        <tr>
          <td style="width:50%;padding: 70px;  ">
            <div class="chart-container" style="width:500px">

              <div class="line-chart-container" style="width:600px; height:300px">

                <canvas id="enrolment-line-chart"></canvas>

              </div>

            </div>
          </td>
          <td style="width:50%;padding: 70px; ">
            <div class="chart-container" style="width:500px">

              <div class="line-chart-container" style="width:600px; height:300px">

                <canvas id="completion-line-chart"></canvas>

              </div>

            </div>
          </td>
        </tr>
        <tr>
          <td style="width:50%;padding: 70px;  ">
            <div class="chart-container" style="width:500px">

              <div class="line-chart-container" style="width:600px; height:300px">

                <canvas id="learninghours-line-chart"></canvas>

              </div>

            </div>
          </td>
          <!-- <td style="width:50%;padding: 70px; ">
            <div class="chart-container" style="width:500px">

              <div class="line-chart-container" style="width:600px; height:300px">

                <canvas id="completion-line-chart"></canvas>

              </div>

            </div>
          </td> -->
        </tr>
      </table>

    </div>

    <div class="dashboard-summary">
      <label class="tab-label">Course Overview</label>
      <hr />
      <table>
        <tr>
          <td style="width:50%;padding-left: 80px;">
            <div class="table-container">
              <?php
              echo $course_overview;
              ?>
            </div>
          </td>
          <td style="width:50%;padding-left: 80px;">
            <div class="chart-container" style="width:500px">

              <div class="pie-chart-container">

                <canvas id="course-pie-chart"></canvas>

              </div>

            </div>
          </td>
        </tr>
        <tr>
          <!-- <td style="width:50%;padding-left: 80px;">
            <div class="table-container">
              <?php
              //echo $course_overview;
              ?>
            </div>
          </td> -->
          <td  style="width:50%;padding: 70px; ">
            <div class="chart-container">

              <div class="line-chart-container"  style="width:500px">

                <canvas id="coursePublished-line-chart" style="width:600px; height:350px"></canvas>

              </div>

            </div>
          </td>
        </tr>
      </table>
    </div>

    <div class="dashboard-summary">
      <label class="tab-label">User Overview</label>
      <hr />
      <table>
        <tr>
          <!-- <td style="width:50%;padding-left: 80px;">
            <div class="table-container">
              <?php
              //echo $course_overview;
              ?>
            </div>
          </td> -->
          <td>
            <div class="chart-container">

              <div class="line-chart-container">

                <canvas id="user-line-chart" style="width:800px; height:400px"></canvas>

              </div>

            </div>
          </td>
        </tr>
      </table>
    </div>




  </section>


  <!-- javascript -->
  <script>
    // $(document).ready(function () {

    //   $('#tbl-overview').DataTable({
    //     autoWidth: true,
    //     processing: true,
    //     serverSide: false
    <?php //echo base_url('/dashboard') ?>
    //   });
    // });



    $(function () {


      var cData = JSON.parse(`<?php echo $chart_data; ?>`);

      //  LEARNER OVERVIEW
      var learnerctx = $("#learner-pie-chart");
      var learnerdata = {
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
      var learneroptions = {
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
        }
      };
      var learnerchart = new Chart(learnerctx, {
        type: "doughnut",
        data: learnerdata,
        options: learneroptions
      });

      //  COURSE OVERVIEW

      var coursectx = $("#course-pie-chart");
      var coursedata = {
        labels: cData.courselabel,
        datasets: [
          {
            label: "Course Count",
            data: cData.coursedata,
            backgroundColor: [
              "#68bf7c",
              "#f4a05a",
              "#ad99bd",
              "#337ab7",
              "#d7cd7a"
            ],
            borderColor: [
              "#68bf7c",
              "#f4a05a",
              "#ad99bd",
              "#337ab7",
              "#d7cd7a"
            ],
            borderWidth: [1, 1, 1, 1, 1, 1, 1, 1, 1]
          }
        ]
      };
      var courseoptions = {
        responsive: true,
        title: {
          display: true,
          position: "top",
          text: "Course Overview",
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
      var coursechart = new Chart(coursectx, {
        type: "doughnut",
        data: coursedata,
        options: courseoptions
      });

      var coursePublishctx = $("#coursePublished-line-chart");
      var coursePublishdata = {
        labels: cData.coursePublishMonth,
        datasets: [
          {
            label: "Courses Published",
            data: cData.coursePublishCount,
            borderColor: "#ad99bd",
            pointBackgroundColor: "#ad99bd",
            pointBorderColor: "#ad99bd",
            pointHoverBackgroundColor: "#ad99bd",
            pointHoverBorderColor: "#ad99bd",
            pointStyle: 'crossRot'
          },
          {
            label: "Total Courses",
            data: cData.totalCoursePublishCount,
            borderColor: "#4bc0c0",
            pointBackgroundColor: "#4bc0c0",
            pointBorderColor: "#4bc0c0",
            pointHoverBackgroundColor: "#4bc0c0",
            pointHoverBorderColor: "#4bc0c0",
            pointStyle: 'crossRot'
          }
        ]
      };

      var coursePublishoptions = {
        responsive: true,
        title: {
          display: true,
          position: "top",
          text: "Month-wise Courses Published",
          fontSize: 16,
          fontColor: "#111"
        },

        legend: {
          display: true,
          position: "bottom",
          labels: {
            fontColor: "#333",
            fontSize: 16
          }
        },
        scales: {
          xAxes: [{
            gridLines: {
              display: false
            }
          }],
          yAxes: [{
            gridLines: {
              display: false
            }
          }]
        }
      };

      var coursePublishchart = new Chart(coursePublishctx, {
        type: "line",
        data: coursePublishdata,
        options: coursePublishoptions
      });
      //  USER OVERVIEW

      var userctx = $("#user-line-chart");
      var userdata = {
        labels: cData.onboardingMonth,
        datasets: [
          {
            label: "Users Onboarded",
            data: cData.onboardingCount,
            borderColor: "#36a2eb",
            pointBackgroundColor: "#36a2eb",
            pointBorderColor: "#36a2eb",
            pointHoverBackgroundColor: "#36a2eb",
            pointHoverBorderColor: "#36a2eb",
            pointStyle: 'crossRot'
          },
          {
            label: "Total Users",
            data: cData.totalUserCount,
            borderColor: "#ff6384",
            pointBackgroundColor: "#ff6384",
            pointBorderColor: "#ff6384",
            pointHoverBackgroundColor: "#ff6384",
            pointHoverBorderColor: "#ff6384",
            pointStyle: 'crossRot'
          }
        ]
      };

      var useroptions = {
        responsive: true,
        title: {
          display: true,
          position: "top",
          text: "Month-wise User Count",
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
        scales: {
          xAxes: [{
            gridLines: {
              display: false
            }
          }],
          yAxes: [{
            gridLines: {
              display: false
            }
          }]
        }
      };

      var userchart = new Chart(userctx, {
        type: "line",
        data: userdata,
        options: useroptions
      });


      //  ENROLMENT OVERVIEW

      var enrolmentctx = $("#enrolment-line-chart");
      var enrolmentdata = {
        labels: cData.monthWiseEnrolmentMonth,
        datasets: [
          {
            label: "Total Enrolment",
            data: cData.totalEnrolmentCount,
            borderColor: "#68bf7c",
            pointBackgroundColor: "#68bf7c",
            pointBorderColor: "#68bf7c",
            pointHoverBackgroundColor: "#68bf7c",
            pointHoverBorderColor: "#68bf7c",
            pointStyle: 'crossRot'
          },
          {
            label: "Enrolments in the month",
            data: cData.monthWiseEnrolmentCount,
            borderColor: "#f4a05a",
            pointBackgroundColor: "#f4a05a",
            pointBorderColor: "#f4a05a",
            pointHoverBackgroundColor: "#f4a05a",
            pointHoverBorderColor: "#f4a05a",
            pointStyle: 'crossRot'
          }
        ]
      };

      var enrolmentoptions = {
        responsive: true,
        title: {
          display: true,
          position: "top",
          text: "Month-wise Enrolment Count",
          fontSize: 16,
          fontColor: "#111"
        },

        legend: {
          display: true,
          position: "bottom",
          labels: {
            fontColor: "#333",
            fontSize: 16
          }
        },
        scales: {
          xAxes: [{
            gridLines: {
              display: false
            }
          }],
          yAxes: [{
            gridLines: {
              display: false
            }
          }]
        }
      };

      var enrolmentchart = new Chart(enrolmentctx, {
        type: "line",
        data: enrolmentdata,
        options: enrolmentoptions
      });

      //  COMPLETION OVERVIEW

      var completionctx = $("#completion-line-chart");
      var completiondata = {
        labels: cData.monthWiseCompletionMonth,
        datasets: [
          {
            label: "Total Completion",
            data: cData.totalCompletionCount,
            borderColor: "#36a2eb",
            pointBackgroundColor: "#36a2eb",
            pointBorderColor: "#36a2eb",
            pointHoverBackgroundColor: "#36a2eb",
            pointHoverBorderColor: "#36a2eb",
            pointStyle: 'crossRot'
          },
          {
            label: "Completions in the month",
            data: cData.monthWiseCompletionCount,
            borderColor: "#ff6384",
            pointBackgroundColor: "#ff6384",
            pointBorderColor: "#ff6384",
            pointHoverBackgroundColor: "#ff6384",
            pointHoverBorderColor: "#ff6384",
            pointStyle: 'crossRot'
          }
        ]
      };

      var completionoptions = {
        responsive: true,
        title: {
          display: true,
          position: "top",
          text: "Month-wise Completion Count",
          fontSize: 16,
          fontColor: "#111"
        },

        legend: {
          display: true,
          position: "bottom",
          labels: {
            fontColor: "#333",
            fontSize: 16
          }
        },
        scales: {
          xAxes: [{
            gridLines: {
              display: false
            }
          }],
          yAxes: [{
            gridLines: {
              display: false
            }
          }]
        }
      };

      var completionchart = new Chart(completionctx, {
        type: "line",
        data: completiondata,
        options: completionoptions
      });

      // LEARNING HOURS

      var learninghoursctx = $("#learninghours-line-chart");
      var learninghoursdata = {
        labels: cData.learningHoursMonth,
        datasets: [
          {
            label: "Total Learning Hours",
            data: cData.totalearningHours,
            borderColor: "#5874ce",
            pointBackgroundColor: "#5874ce",
            pointBorderColor: "#5874ce",
            pointHoverBackgroundColor: "#5874ce",
            pointHoverBorderColor: "#5874ce",
            pointStyle: 'crossRot'
          },
          {
            label: "Learning Hours in the month",
            data: cData.monthWiseLearningHours,
            borderColor: "#a2df95",
            pointBackgroundColor: "#a2df95",
            pointBorderColor: "#a2df95",
            pointHoverBackgroundColor: "#a2df95",
            pointHoverBorderColor: "#a2df95",
            pointStyle: 'crossRot'
          }
        ]
      };

      var learninghoursoptions = {
        responsive: true,
        title: {
          display: true,
          position: "top",
          text: "Month-wise Learning Hours",
          fontSize: 16,
          fontColor: "#111"
        },

        legend: {
          display: true,
          position: "bottom",
          labels: {
            fontColor: "#333",
            fontSize: 16
          }
        },
        scales: {
          xAxes: [{
            gridLines: {
              display: false
            }
          }],
          yAxes: [{
            gridLines: {
              display: false
            }
          }]
        }
      };

      var learninghourschart = new Chart(learninghoursctx, {
        type: "line",
        data: learninghoursdata,
        options: learninghoursoptions
      });




    });

    $(document).ready(function () {
      // var className = document.getElementsByClassName('dashboard-item-value');
      // for (var index = 0; index < className.length; index++) {
      //   var value = className[index].value;
      //   className[index].innerText = Number(className[index].innerText).toLocaleString('en-IN');
      // }
      var className = document.getElementsByClassName('numformat');
      for (var index = 0; index < className.length; index++) {
        var value = className[index].value;
        number = Number(className[index].innerText);
        if (!isNaN(number))
          className[index].innerText = number.toLocaleString('en-IN');
      }

    });

  </script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.js"></script>

  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

</body>