Files changed after previous deployment:

1. Create materiazed view enrolment_summary, course_enrolment_summary
1. Edit MV enrolment
1. app/Config/Contants.php                          --> Course table class changed
1. app/Controller/Dashboard.php                     --> Course table class changed
1. app/Controller/Report.php                        --> Removed redundant query for full result
1. app/Models/DashboardModel.php                    --> KPIs for dashboard
1. app/Models/MasterOrganisationModel.php           --> Materialized View, Org dashboard
1. app/Models/MasterUserModel.php                   --> Materialized View
1. app/Models/UserEnrolmentCourse.php               --> Materialized View
1. app/Views/dashboard_spv.php                      --> KPIs added
1. app/Views/report_home.php                        --> Removed large reports
1. public/assets/css/dashboard_style.css            --> Dashboard items
1. public/assets/scripts/home.js                    --> Org dropdown











