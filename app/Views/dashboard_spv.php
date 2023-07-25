<!DOCTYPE html>

<html>

<head>



  <!-- Latest CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

  <script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
  <script src="https://unpkg.com/chart.js@2.8.0/dist/Chart.bundle.js"></script>
  <script src="https://unpkg.com/chartjs-gauge@0.3.0/dist/chartjs-gauge.js"></script>
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
      <label class="tab-label">At a glance</label>
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
      <label class="learner-tab-label">Learner Overview</label>
      <hr />
      <table>
        <tr>

          <td class="learner-dashboard-summary-item">
            <div class="learner-dashboard-item">
              <div>
                <label class="learner-dashboard-item-value numformat">
                  <?php echo $enrolmentPercentage; ?>%
                </label>
              </div>
              <div>
                <label class="learner-dashboard-item-header">Onboarded users enrolled</label>
              </div>
            </div>
          </td>
          <td class="learner-dashboard-summary-item">
            <div class="learner-dashboard-item">
              <div>
                <label class="learner-dashboard-item-value numformat">
                  <?php echo $completionPercentage; ?>%
                </label>
              </div>
              <div>
                <label class="learner-dashboard-item-header">Completion vs. Enrolment </label>
              </div>
            </div>
          </td>
          <td class="learner-dashboard-summary-item">
            <div class="learner-dashboard-item">
              <!-- <hr /> -->
              <label class="learner-dashboard-item-value numformat">
                <?php echo $notStartedPercentage; ?>%
              </label>
              <label class="learner-dashboard-item-header">Not Started vs. Enrolment </label>
            </div>
          </td>
          <td class="learner-dashboard-summary-item">
            <div class="learner-dashboard-item">
              <!-- <hr /> -->
              <label class="learner-dashboard-item-value numformat">
                <?php echo $inProgressPercentage; ?>%
              </label>
              <label class="learner-dashboard-item-header">In Progress vs. Enrolment </label>
            </div>
          </td>
        </tr>
        <tr>
          <td colspan="2" style="width:50%;padding: 50px 70px 50px 70px; ">
            <div class="table-container">
              <?php
              echo $learner_overview;
              ?>
            </div>
          </td>
          <td colspan="2" style="width:50%;padding: 50px 70px 50px 70px; ">
            <div class="chart-container" style="width:500px">

              <div class="pie-chart-container">

                <canvas id="learner-pie-chart"></canvas>

              </div>

            </div>
          </td>
        </tr>
        <tr>
          <td colspan="2" style="width:50%;padding:  50px 70px 50px 70px;  ">
            <div class="chart-container" style="width:500px">

              <div class="line-chart-container" style="width:600px; height:300px">

                <canvas id="enrolment-line-chart"></canvas>

              </div>

            </div>
          </td>
          <td colspan="2" style="width:50%;padding:  50px 70px 50px 70px;">
            <div class="chart-container" style="width:500px">

              <div class="line-chart-container" style="width:600px; height:300px">

                <canvas id="completion-line-chart"></canvas>

              </div>

            </div>
          </td>
        </tr>
        <tr>
          <td colspan="2" style="width:50%;padding:  50px 70px 50px 70px; ">
            <div class="chart-container" style="width:500px">

              <div class="line-chart-container" style="width:600px; height:300px">

                <canvas id="enrolment-completion-bar"></canvas>

              </div>

            </div>
          </td>
          <td colspan="2" style="width:50%;padding:  50px 70px 50px 70px; ">
            <div class="chart-container" style="width:500px">

              <div class="line-chart-container" style="width:600px; height:300px">

                <canvas id="learninghours-line-chart"></canvas>

              </div>

            </div>
          </td>

        </tr>
      </table>

    </div>

    <div class="dashboard-summary">
      <label class="course-tab-label">Course Overview</label>
      <hr />
      <table>
        <tr>
          <td class="course-dashboard-summary-item">
            <div class="course-dashboard-item">
              <div>
                <label class="course-dashboard-item-value numformat">
                  <?php echo $avgRating; ?>
                </label>
              </div>
              <div>
                <label class="course-dashboard-item-header">Avg rating of courses</label>
              </div>
            </div>
          </td>
          <td class="course-dashboard-summary-item">
            <div class="course-dashboard-item">
              <div>
                <label class="course-dashboard-item-value numformat">
                  <?php echo $programCount; ?>
                </label>
              </div>
              <div>
                <label class="course-dashboard-item-header">Programs created</label>
              </div>
            </div>
          </td>
          <td class="course-dashboard-summary-item">
            <div class="course-dashboard-item">
              <div>
                <label class="course-dashboard-item-value numformat">
                  <?php echo $programDuration; ?>
                </label>
                <label class="course-dashboard-item-header">Program Duration (in hrs)</label>
              </div>
          </td>
          <td class="course-dashboard-summary-item">
            <div class="course-dashboard-item">
              <!-- <hr /> -->
              <label class="course-dashboard-item-value numformat">
                <?php echo $coursesCurrentMonth; ?>
              </label>
              <label class="course-dashboard-item-header">Courses published this month</label>
            </div>
          </td>
        </tr>

        <tr>
          <td colspan="2" style="width:50%;padding:  50px 70px 50px 70px; ">
            <div class="table-container">
              <?php
              echo $course_overview;
              ?>
            </div>
          </td>
          <td colspan="2" style="width:50%; padding:  50px 70px 50px 70px; ">
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
          <td colspan="2" style="width:50%; padding:  50px 70px 50px 70px;  ">
            <div class="chart-container">

              <div class="line-chart-container" style="width:500px">

                <canvas id="coursePublished-line-chart" style="width:600px; height:350px"></canvas>

              </div>

            </div>
          </td>
        </tr>
      </table>
    </div>

    <div class="dashboard-summary">
      <label class="user-tab-label">User Overview</label>
      <hr />
      <table>
        <tr>
          <td>
            <div class="user-dashboard-item">
              <div>
                <label class="user-dashboard-item-value numformat">
                  <?php echo $usersOnboardedYesterday; ?>
                </label>
              </div>
              <div>
                <label class="user-dashboard-item-header">Users onboarded yesterday</label>
              </div>
            </div>
          </td>
          <td>
            <div class="user-dashboard-item">
              <div>
                <label class="user-dashboard-item-value numformat">
                  <?php echo $mdoAdminCount; ?>
                </label>
              </div>


              <div>
                <label class="user-dashboard-item-header">MDO Admins onboarded</label>
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
          <td colspan="2" style="padding:  50px 70px 50px 70px; ">
            <div class="chart-container">

              <div class="line-chart-container">

                <canvas id="user-line-chart" style="width:800px; height:400px"></canvas>

              </div>

            </div>
          </td>
          <!-- <td style="width:50%;padding: 70px; ">
            <div class="chart-container" style="width:500px">

              <div class="line-chart-container" style="width:600px; height:300px">

                <canvas id="onboarding-enrolment-bar"></canvas>

              </div>

            </div>
          </td> -->
        </tr>
      </table>
    </div>


    <div class="dashboard-summary">
      <label class="org-tab-label">Organisation Overview</label>
      <hr />
      <table>
        <tr>
          <td>
            <div class="org-dashboard-item">
              <div>
                <label class="org-dashboard-item-value numformat">
                  <?php echo $orgEnrolled; ?>
                </label>
              </div>
              <div>
                <label class="org-dashboard-item-header">Organisations Enrolled for Courses</label>
              </div>
            </div>
          </td>
          <td>
            <div class="org-dashboard-item">
              <div>
                <label class="org-dashboard-item-value numformat">
                  <?php echo $mdoAdminOrgCount; ?>
                </label>
              </div>
              <div>
                <label class="org-dashboard-item-header">Organisations with MDO Admin</label>
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
          <td colspan="2">
            <div class="chart-container">

              <div class="line-chart-container">

                <canvas id="org-line-chart" style="width:800px; height:400px"></canvas>

              </div>

            </div>
          </td>
          <!-- <td style="width:50%;padding: 70px; ">
            <div class="chart-container" style="width:500px">

              <div class="line-chart-container" style="width:600px; height:300px">

                <canvas id="onboarding-enrolment-bar"></canvas>

              </div>

            </div>
          </td> -->
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
            borderColor: "#fe96a9",
            pointBackgroundColor: "#fe96a9",
            pointBorderColor: "#fe96a9",
            pointHoverBackgroundColor: "#fe96a9",
            pointHoverBorderColor: "#fe96a9",
            pointStyle: 'crossRot',
            backgroundColor: '#fe96a929'
          },
          {
            label: "Total Courses",
            data: cData.totalCoursePublishCount,
            borderColor: "#aad6c8",
            pointBackgroundColor: "#aad6c8",
            pointBorderColor: "#aad6c8",
            pointHoverBackgroundColor: "#aad6c8",
            pointHoverBorderColor: "#aad6c8",
            pointStyle: 'crossRot',
            backgroundColor: '#aad6c83d'
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
            label: "Users Onboarded in the month",
            data: cData.onboardingCount,
            borderColor: "#36a2eb",
            pointBackgroundColor: "#36a2eb",
            pointBorderColor: "#36a2eb",
            pointHoverBackgroundColor: "#36a2eb",
            pointHoverBorderColor: "#36a2eb",
            pointStyle: 'crossRot',
            backgroundColor: '#36a2eb0f'
          },
          {
            label: "Total Users",
            data: cData.totalUserCount,
            borderColor: "#ff6384",
            pointBackgroundColor: "#ff6384",
            pointBorderColor: "#ff6384",
            pointHoverBackgroundColor: "#ff6384",
            pointHoverBorderColor: "#ff6384",
            pointStyle: 'crossRot',
            backgroundColor: '#ff63840f'
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

      var userchart = new Chart(userctx, {
        type: "line",
        data: userdata,
        options: useroptions
      });

      // ONBOARDED-ENROLMENT RATIO

      var completionratioctx = $("#onboarding-enrolment-bar");
      var completionratiodata = {
        labels: cData.onboardingMonth,
        datasets: [
          {
            label: "Unique users enrolled",
            data: cData.totalUniqeEnrolmentCount,
            backgroundColor: '#ffc87d'
          },
          {
            label: "User count",
            data: cData.totalUserCount,
            backgroundColor: '#829ccf'
          }

        ]
      };

      var completionratiooptions = {
        responsive: true,
        title: {
          display: true,
          position: "top",
          text: "Users Onboarded vs Users Enrolled",
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
            },
            stacked: true
          }],
          yAxes: [{
            gridLines: {
              display: false
            },
            stacked: true
          }]
        }
      };

      var completionratiochart = new Chart(completionratioctx, {
        type: "bar",
        data: completionratiodata,
        options: completionratiooptions
      });

      //  ENROLMENT OVERVIEW

      var enrolmentctx = $("#enrolment-line-chart");
      var enrolmentdata = {
        labels: cData.monthWiseEnrolmentMonth,
        datasets: [
          {
            label: "Enrolments in the month",
            data: cData.monthWiseEnrolmentCount,
            borderColor: "#94cef5",
            pointBackgroundColor: "#94cef5",
            pointBorderColor: "#94cef5",
            pointHoverBackgroundColor: "#94cef5",
            pointHoverBorderColor: "#94cef5",
            pointStyle: 'crossRot',
            backgroundColor: '#94cef536'
          },
          {
            label: "Total Enrolment",
            data: cData.totalEnrolmentCount,
            borderColor: "#ffaf91",
            pointBackgroundColor: "#ffaf91",
            pointBorderColor: "#ffaf91",
            pointHoverBackgroundColor: "#ffaf91",
            pointHoverBorderColor: "#ffaf91",
            pointStyle: 'crossRot',
            backgroundColor: '#ffaf912b'
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
            label: "Completions in the month",
            data: cData.monthWiseCompletionCount,
            borderColor: "#e39cb9",
            pointBackgroundColor: "#e39cb9",
            pointBorderColor: "#e39cb9",
            pointHoverBackgroundColor: "#e39cb9",
            pointHoverBorderColor: "#e39cb9",
            pointStyle: 'crossRot',
            backgroundColor: '#e39cb93d'
          },
          {
            label: "Total Completion",
            data: cData.totalCompletionCount,
            borderColor: "#aad09a",
            pointBackgroundColor: "#aad09a",
            pointBorderColor: "#aad09a",
            pointHoverBackgroundColor: "#aad09a",
            pointHoverBorderColor: "#aad09a",
            pointStyle: 'crossRot',
            backgroundColor: '#aad09a3b'
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


      // ENROLMENT-COMPLETION RATIO


      var completionratioctx = $("#enrolment-completion-bar");
      var completionratiodata = {
        labels: cData.monthWiseCompletionMonth,
        datasets: [
          {
            label: "Completion Count",
            data: cData.totalCompletionCount,
            backgroundColor: '#829ccf'
          },
          {
            label: "Enrolment Count",
            data: cData.totalEnrolmentCount,
            backgroundColor: '#ffc87d'
          }
        ]
      };

      var completionratiooptions = {
        responsive: true,
        title: {
          display: true,
          position: "top",
          text: "Enrolment vs. Completion",
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
            },
            stacked: true,
            ticks: {
              autoSkip: false
            }
          }],
          yAxes: [{
            gridLines: {
              display: false
            },
            stacked: true
          }]
        }
      };

      var completionratiochart = new Chart(completionratioctx, {
        type: "bar",
        data: completionratiodata,
        options: completionratiooptions
      });



      // LEARNING HOURS

      var learninghoursctx = $("#learninghours-line-chart");
      var learninghoursdata = {
        labels: cData.learningHoursMonth,
        datasets: [
          {
            label: "Learning Hours in the month",
            data: cData.monthWiseLearningHours,
            borderColor: "#ffa6b9",
            pointBackgroundColor: "#ffa6b9",
            pointBorderColor: "#ffa6b9",
            pointHoverBackgroundColor: "#ffa6b9",
            pointHoverBorderColor: "#ffa6b9",
            pointStyle: 'crossRot',
            backgroundColor: '#ffa6b929'
          },
          {
            label: "Total Learning Hours",
            data: cData.totalearningHours,
            borderColor: "#7abfee",
            pointBackgroundColor: "#7abfee",
            pointBorderColor: "#7abfee",
            pointHoverBackgroundColor: "#7abfee",
            pointHoverBorderColor: "#7abfee",
            pointStyle: 'crossRot',
            backgroundColor: '#7abfee29'
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

      //  ORG OVERVIEW

      var orgctx = $("#org-line-chart");
      var orgdata = {
        labels: cData.orgOnboardingMonth,
        datasets: [
          {
            label: "Organisations Onboarded in the month",
            data: cData.orgOnboardingCount,
            borderColor: "#36a2eb",
            pointBackgroundColor: "#36a2eb",
            pointBorderColor: "#36a2eb",
            pointHoverBackgroundColor: "#36a2eb",
            pointHoverBorderColor: "#36a2eb",
            pointStyle: 'crossRot',
            backgroundColor: '#36a2eb0f'
          },
          {
            label: "Total Organisations",
            data: cData.totalOrgCount,
            borderColor: "#ff6384",
            pointBackgroundColor: "#ff6384",
            pointBorderColor: "#ff6384",
            pointHoverBackgroundColor: "#ff6384",
            pointHoverBorderColor: "#ff6384",
            pointStyle: 'crossRot',
            backgroundColor: '#ff63840f'
          }
        ]
      };

      var orgoptions = {
        responsive: true,
        title: {
          display: true,
          position: "top",
          text: "Month-wise Organisation Count",
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

      var orgchart = new Chart(orgctx, {
        type: "line",
        data: orgdata,
        options: orgoptions
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