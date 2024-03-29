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
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <!-- Datatable JS -->
    <script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <!-- STYLES -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lato">

    <!-- ASSETS -->
    <link href="<?php echo ASSETS_URL . 'css/home_style.css'; ?>" rel="stylesheet" type="text/css">
    <script src="<?php echo ASSETS_URL . 'scripts/home.js' ?>" type="text/javascript"></script>
    <!-- for custom validation pop up boxes -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11">  </script>

    <style>

    </style>

    <script>

        $(document).ready(function () {

            $("#mdowisereportform").submit(function (event) {
                var mdoReportType = $('#mdoReportType').val();
                if (mdoReportType == 'notSelected') {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Please Select Report Type !',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }                                                   // If report type is not selected then do not submit the form
                else if (mdoReportType == 'mdoUserCount')              // Only one option is there no need to check sub dropdowns
                {
                    return true;
                }
                else if (mdoReportType == 'mdoUserList' || mdoReportType == 'mdoUserEnrolment' || mdoReportType == 'userWiseCount')                //  Report type 2nd option validation 
                {
                    // var ms = $('#ms_type').val();
                    // var ministry = $('#ministry').val();
                    // var dept = $('#dept').val();
                    var org = $('#org').val();

                    if (org == '') {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Please Select Organisation!',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }
                    else {
                        return true;
                    }
                }
                else if (mdoReportType == 'orgHierarchy' || mdoReportType == 'ministryUserEnrolment' || mdoReportType == 'ministryUserList')             //Report type 3rd option validation  
                {
                    var org = $('#org').val();

                    // var ms = $('#ms_type').val();
                    if (org == '') {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Please Select Ministry/State!',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }
                }
            });

            $("#coursereportform").submit(function () {                    // Tab 2 Validation 
                var courseReportType = $('#courseReportType').val();
                //alert(courseReportType) ; 
                if (courseReportType == 'notSelected') {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Please Select Course Report Type !',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return false;                                              // If report type is not selected then do not submit the form
                }
                else if (courseReportType == 'courseEnrolmentCount')              // Only one option is there no need to check sub dropdowns 2nd option validation
                {
                    return true;
                }
                else if (courseReportType == 'programEnrolmentCount')              // Only one option is there no need to check sub dropdowns 4th option validation
                {
                    return true;
                }

                else if (courseReportType == 'courseEnrolmentReport')                //  Report type 1st option validation 
                {

                    var course = $('#course').val();
                    if (course == '') {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Please Select Course',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }
                    else {
                        return true;
                    }
                }

                else if (courseReportType == 'programEnrolmentReport')                //  Report type 3rd option validation 
                {
                    var program = $('#course').val();
                    if (program == '') {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Please Select program',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }
                    else {
                        return true;
                    }
                }
                else if (courseReportType == 'collectionEnrolmentReport')                //  Report type 5th option validation 
                {
                    var curated = $('#course').val();
                    if (curated == '') {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Please Select Curated Collection',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }
                    else {
                        return true;
                    }
                }
                else if (courseReportType == 'collectionEnrolmentCount')                //  Report type 5th option validation 
                {
                    var collection = $('#course').val();
                    if (collection == '') {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Please Select Curated Collection',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }
                    else {
                        return true;
                    }
                }
                else if (courseReportType == 'cbpProviderWiseEnrolmentSummary')                //  Report type 5th option validation 
                {
                    var provider = $('#course').val();
                    if (provider == '') {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Please Select Course Provider',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }
                    else {
                        return true;
                    }
                }
                else if (courseReportType == 'courseMinistrySummary')                //  Report type 1st option validation 
                {

                    var course = $('#course').val();
                    if (course == '') {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Please Select Course',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }
                    else {
                        return true;
                    }
                }




            });

            $("#userreportform").submit(function (event) {
                var userReportType = $('#userReportType').val();
                if (userReportType == 'notSelected') {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Please Select Report Type !',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }                                                   // If report type is not selected then do not submit the form
                else if (userReportType == 'userList' || userReportType == 'userEnrolmentFull')              // Only one option is there no need to check sub dropdowns
                {
                    return true;
                }
                else if (userReportType == 'userProfile' || userReportType == 'userEnrolment')                //  Report type 2nd option validation 
                {
                    var email = $('#email').val();

                    if (email == '') {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Please Select User!',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }
                    else {
                        return true;
                    }
                }

            });

            $("#rolereportform").submit(function () {                     // Tab 3 Validation 
                var roleReportType = $('#roleReportType').val();
                if (roleReportType == 'notSelected') {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Please Select Report Type !',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }                                                       // If report type is not selected then do not submit the form
                else                                                    // Only one option is there no need to check sub dropdowns
                {
                    return true;
                }
            });

            $("#analyticsreportform").submit(function () {                    // Tab 4 Validation 
                var analyticsReportType = $('#analyticsReportType').val();
                if (analyticsReportType == 'notSelected') {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Please Select Analytics Report Type !',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }                                                           // If report type is not selected then do not submit the form
                else                                                        // Only one option is there no need to check sub dropdowns
                {
                    return true;
                }
            });

            $("#topreportform").submit(function () {                    // Tab 4 Validation 
                var topReportType = $('#topReportType').val();
                var topCount = $('#topCount').val();
                var topCourse = $('#topcoursename').val();
                var competencyType = $('#competencyType').val();

                if (topReportType == 'notSelected') {                   // If report type is not selected then do not submit the form
                    Swal.fire({
                        title: 'Error!',
                        text: 'Please Select Analytics Report Type !',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }
                else if (topCount == '') {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Please Select No. of Records to be shown !',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }
                else if (topReportType == 'topCompetency') {
                    if (competencyType == 'notSelected') {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Please Select Competency !',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }
                }
                else if (topReportType == 'topOrgCourseWise' || topReportType == 'topOrgProgramWise' || topReportType == 'topOrgCollectionWise') {
                    if (topCourse == '') {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Please Select Course !',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }
                }
                else                                                        // Only one option is there no need to check sub dropdowns
                {
                    return true;
                }
            });

            $("#miscreportform").submit(function () {                     // Tab 3 Validation 
                var miscReportType = $('#miscReportType').val();
                if (miscReportType == 'notSelected') {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Please Select Report Type !',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }                                                       // If report type is not selected then do not submit the form
                else                                                    // Only one option is there no need to check sub dropdowns
                {
                    return true;
                }
            });
        });







        $(document).ready(function () {
            $('#orgname').keyup(function () {
                var orgs = [];
                var action = 'org_search';
                search_key = document.getElementById('orgname').value;
                reportType = document.getElementById('mdoReportType').value;
                $.ajax({

                    url: "<?php echo base_url('/action') ?>",
                    method: "POST",
                    data: {
                        action: action,
                        search_key: search_key,
                        reportType: reportType
                    },
                    dataType: "JSON",
                    success: function (data) {
                        html = '';
                        if (reportType == "ministryUserEnrolment" || reportType == "orgHierarchy"|| reportType == "ministryUserList") {    //  Fetch ministry names from DB
                            for (var count = 0; count < data.length; count++) {
                                html += '<option class="datalist-options" data-value="' + data[count].ms_id + '">' + data[count].ms_name + '</option>';
                            }
                        }
                        else {
                            for (var count = 0; count < data.length; count++) {                         //  Fetch org names from DB
                                html += '<option class="datalist-options" data-value="' + data[count].root_org_id + '">' + data[count].org_name + '</option>';
                            }
                        }


                        $('#org_search_result').html(html);


                    }

                });
            });
        });

        $(document).ready(function () {


            $('input[type=text][name=orgname]').change(function () {
                var options = $('datalist#org_search_result')[0].options;
                var val = document.getElementById('orgname').value;
                var org = document.getElementById('org');
                for (var i = 0; i < options.length; i++) {

                    if (options[i].value === val) {
                        org.value = options[i].getAttribute('data-value');
                        break;
                    }
                }



            });
        });

        $(document).ready(function () {
            $('#coursename').keyup(function () {
                var orgs = [];
                var action = 'course_search';
                search_key = document.getElementById('coursename').value;
                reportType = document.getElementById('courseReportType').value;
                $.ajax({

                    url: "<?php echo base_url('/action') ?>",
                    method: "POST",
                    data: {
                        action: action,
                        search_key: search_key,
                        reportType: reportType
                    },
                    dataType: "JSON",
                    success: function (data) {
                        html = '';
                        if (reportType == 'courseEnrolmentReport' || reportType == 'courseMinistrySummary') {
                            for (var count = 0; count < data.length; count++) {
                                html += '<option class="datalist-options" data-value="' + data[count].course_id + '">' + data[count].course_name + ' [by ' + data[count].org_name + '] </option>';
                            }
                        } else if (reportType == 'programEnrolmentReport') {
                            for (var count = 0; count < data.length; count++) {
                                html += '<option class="datalist-options" data-value="' + data[count].program_id + '">' + data[count].program_name + ' [by ' + data[count].org_name + ']</option>';
                            }
                        } else if (reportType == 'collectionEnrolmentReport' || reportType == 'collectionEnrolmentCount') {
                            for (var count = 0; count < data.length; count++) {
                                html += '<option class="datalist-options" data-value="' + data[count].curated_id + '">' + data[count].curated_name + '</option>';
                            }
                        } else if (reportType == 'cbpProviderWiseEnrolmentSummary' ) {
                            for (var count = 0; count < data.length; count++) {
                                console.log(data[count].root_org_id );
                             html += '<option class="datalist-options" data-value="' + data[count].root_org_id + '">' + data[count].org_name + '</option>';
                            }
                        }

                        $('#course_search_result').html(html);

                    }

                });
            });
        });

        

        $(document).ready(function () {


            $('input[type=text][name=coursename]').change(function () {
                var options = $('datalist#course_search_result')[0].options;
                var val = document.getElementById('coursename').value;
                var course = document.getElementById('course');
                for (var i = 0; i < options.length; i++) {
                    console.log(val);
                    if (options[i].value === val) {
                        course.value = options[i].getAttribute('data-value');
                        break;
                    }
                }



            });
        });


        $(document).ready(function () {
            $('#topcoursename').keyup(function () {
                var orgs = [];
                var action = 'course_search';
                search_key = document.getElementById('topcoursename').value;
                reportType = document.getElementById('topReportType').value;
                $.ajax({

                    url: "<?php echo base_url('/action') ?>",
                    method: "POST",
                    data: {
                        action: action,
                        search_key: search_key,
                        reportType: reportType
                    },
                    dataType: "JSON",
                    success: function (data) {
                        html = '';
                        if (reportType == 'topOrgCourseWise') {
                            for (var count = 0; count < data.length; count++) {
                                html += '<option class="datalist-options" data-value="' + data[count].course_id + '">' + data[count].course_name + ' [by ' + data[count].org_name + ']</option>';
                            }
                        } else if (reportType == 'topOrgProgramWise') {
                            for (var count = 0; count < data.length; count++) {
                                html += '<option class="datalist-options" data-value="' + data[count].program_id + '">' + data[count].program_name + ' [by ' + data[count].org_name + ']</option>';
                            }
                        } else if (reportType == 'topOrgCollectionWise') {
                            for (var count = 0; count < data.length; count++) {
                                html += '<option class="datalist-options" data-value="' + data[count].curated_id + '">' + data[count].curated_name + '</option>';
                            }
                        }
                        $('#top_course_search_result').html(html);


                    }

                });
            });
        });

        $(document).ready(function () {


            $('input[type=text][name=topcoursename]').change(function () {
                var options = $('datalist#top_course_search_result')[0].options;
                var val = document.getElementById('topcoursename').value;
                var course = document.getElementById('topcourse');
                for (var i = 0; i < options.length; i++) {

                    if (options[i].value === val) {
                        course.value = options[i].getAttribute('data-value');
                        break;
                    }
                }



            });
        });

        $(document).ready(function () {
            $('#email').keyup(function () {
                var orgs = [];
                var action = 'user_search';
                search_key = document.getElementById('email').value;
                reportType = document.getElementById('userReportType').value;
                $.ajax({

                    url: "<?php echo base_url('/action') ?>",
                    method: "POST",
                    data: {
                        action: action,
                        search_key: search_key,
                        reportType: reportType
                    },
                    dataType: "JSON",
                    success: function (data) {
                        html = '';

                        for (var count = 0; count < data.length; count++) {
                            html += '<option class="datalist-options" data-value="' + data[count].name + '">' + data[count].email + '</option>';
                        }

                        $('#user_search_result').html(html);


                    }

                });
            });
        });

        $(document).ready(function () {


            $('input[type=text][name=email]').change(function () {
                var options = $('datalist#user_search_result')[0].options;
                var val = document.getElementById('email').value;
                var user = document.getElementById('userid');
                for (var i = 0; i < options.length; i++) {

                    if (options[i].value === val) {
                        user.value = options[i].getAttribute('data-value');
                        break;
                    }
                }



            });
        });


    </script>
</head>

<body>
    <!-- HEADER: MENU + HEROE SECTION -->
    <section>

        <div id="body">
            <div class="tab">
                <?php
                $session = \Config\Services::session();

                if ($session->get('role') == 'SPV_ADMIN' || $session->get('role') == 'IGOT_TEAM_MEMBER') {
                    echo '
                        <button class="tablinks" onclick="openTab(event, \'MDO-wise\')" id="defaultOpen">MDO-wise</button>
                        <button class="tablinks" onclick="openTab(event, \'Course-wise\')">Course-wise</button>
                        <button class="tablinks" onclick="openTab(event, \'User-wise\')" id="defaultOpen">User-wise</button>
                        <button class="tablinks" onclick="openTab(event, \'Role-wise\')">Role-wise</button>
                        <button class="tablinks" onclick="openTab(event, \'Analytics\')">Analytics</button>
                        <button class="tablinks" onclick="openTab(event, \'Top-Performers\')">Top Performers</button>
                        <button class="tablinks" onclick="openTab(event, \'Miscellaneous\')">Miscellaneous</button>
                ';
                } else if ($session->get('role') == 'MDO_ADMIN') {
                    echo '  
                        <button class="tablinks" onclick="openTab(event, \'MDO-wise\')" id="defaultOpen">MDO-wise</button>
                        <button class="tablinks" onclick="openTab(event, \'Course-wise\')">Course-wise</button>
                        <button class="tablinks" onclick="openTab(event, \'User-wise\')" id="defaultOpen">User-wise</button>
                        <button class="tablinks" onclick="openTab(event, \'Role-wise\')">Role-wise</button>
                    ';
                } else if ($session->get('role') == 'DOPT_ADMIN' || $session->get('role') == 'IGOT_TEAM_MEMBER') {

                    echo '<button class="tablinks" onclick="openTab(event, \'Dopt\')" id="defaultOpen">DoPT Reports</button>';
                } else if ($session->get('role') == 'ATI_ADMIN' || $session->get('role') == 'IGOT_TEAM_MEMBER') {

                    echo '<button class="tablinks" onclick="openTab(event, \'ATI\')" id="defaultOpen">ATI Reports</button>';
                }
                ?>
            </div>

            <div id="MDO-wise" class="tabcontent">
                <form id="mdowisereportform" class="form-horizontal login_form"
                    action="<?php echo base_url('/getMDOReport'); ?>" method="post">
                    <div class="report-type">
                        <label for="mdoReportType" class="lbl-reporttype required "> Report type: </label>
                        <select name="mdoReportType" class="form-control report-select" id="mdoReportType"
                            onchange="enable_disable_mdo(this)">
                            <option value="notSelected">-- Select Report Type --</option>
                            <?php
                            $session = \Config\Services::session();

                            if ($session->get('role') == 'SPV_ADMIN' || $session->get('role') == 'IGOT_TEAM_MEMBER') {

                                echo
                                    '

                                    <option class="options" value="orgList">Organisations onboarded</option>
                                    <option class="options" value="orgHierarchy">Organisation hierarchy</option>
                                    <option class="options" value="mdoUserCount">MDO-wise user count</option>
                                    <option class="options" value="mdoUserList">MDO-wise user list</option>
                                    <option class="options" value="mdoUserEnrolment">MDO-wise user enrolment report</option>
                                    <option class="options" value="userWiseCount">MDO-wise user enrolment summary</option>
                                    <option class="options" value="enrolmentPercentage">MDO-wise user enrolment percentage</option>
                                    <option class="options" value="ministryUserList">Ministry/State-wise user list</option>
                                    <option class="options" value="ministryUserEnrolment">Ministry/State-wise user enrolment report</option>
                                    

                        ';
                            } else if ($session->get('role') == 'MDO_ADMIN') {

                                echo '<option class="options" value="mdoUserList">User List</option>
                    <option value="mdoUserEnrolment">User enrolment report</option>
                    <option value="userWiseCount">User-wise enrolment summary</option>


                    ';
                            } ?>




                        </select>
                    </div>

                    <hr />

                    <div class="container ">


                        <div id="tbl">
                            <table class="tbl-input" id="tbl-mdo">
                                <?php
                                $session = \Config\Services::session();

                                if ($session->get('role') == 'SPV_ADMIN' || $session->get('role') == 'IGOT_TEAM_MEMBER') {

                                    echo '             <tr>
                                    <td style="width:1%"><label class="required"></label></td>
                                    <td>
                                                <div class="auto-widget">
                                                <input type="text" list="org_search_result" class="form-control" id="orgname" name = "orgname"  autocomplete="off" />
                                                <datalist id="org_search_result" >
                                            </datalist>
                                            <input type="hidden"  id="org"  name="org"/>
                                            
                                
                                    </div>
                                    
                                
                                    </td>
                                    </tr>

                                   ';


                                } ?>



                            </table>

                        </div>



                        <div class="col-xs-3 container submitbutton">
                            <button id="mdowisereport" class="btn btn-primary" type="submit" name="Submit"
                                value="Submit"> Submit </button>
                        </div>

                    </div>

                    <?php echo form_close(); ?>
                </form>
            </div>


            <div id="Course-wise" class="tabcontent">
                <form id="coursereportform" class="form-horizontal login_form"
                    action="<?php echo base_url('/getCourseReport'); ?>" method="post">

                    <div class="report-type">
                        <label for="courseReportType" class="lbl-reporttype required">Report type:</label>

                        <select name="courseReportType" class="form-control report-select"
                            onchange="enable_disable_course(this)" id="courseReportType">
                            <option value="notSelected">-- Select Report Type --</option>
                            <option value="liveCourseList">List of live courses</option>
                            <option value="courseEnrolmentReport">Course-wise enrolment report</option>
                            <option value="programEnrolmentReport">Program-wise enrolment report</option>
                            <option value="programEnrolmentCount">Program-wise summary</option>
                            <option value="collectionEnrolmentReport">Curated Collection-wise enrolment report</option>
                            <option value="collectionEnrolmentCount">Curated Collection course-wise summary</option>
                            <option value="cbpProviderWiseCourseCount">CBP Provider-wise course count</option>
                            <option value="cbpProviderWiseEnrolmentSummary">CBP Provider-wise enrolment summary</option>
                            <?php
                            $session = \Config\Services::session();

                            if ($session->get('role') == 'SPV_ADMIN' || $session->get('role') == 'IGOT_TEAM_MEMBER') {
                                echo '<option value="courseEnrolmentCount">Live courses summary</option>';
                                echo '<option value="courseMinistrySummary">Ministry-wise summary for a course</option>';
                                echo '<option value="underPublishCourses">Courses under publish</option>';
                                echo '<option value="underReviewCourses">Courses under review </option>';
                                echo '<option value="draftCourses">Draft courses</option>';
                               

                            }

                            ?>
                        </select>

                    </div>


                    <hr />

                    <div class="container">
                        <table class="tbl-input" id="tbl-course">
                            <!-- <tr>

                                <td colspan="2">
                                    <label for="course">Course/Program/Collection: </label>
                                </td>
                            </tr> -->

                            <tr>
                                <td style="width:1%"><label class="required"></label></td>
                                <td>
                                    <div class="auto-widget">
                                        <input type="text" list="course_search_result" class="form-control"
                                            id="coursename" name="coursename" placeholder="Search Course"
                                            autocomplete="off" />
                                        <datalist id="course_search_result">
                                        </datalist>
                                        <input type="hidden" id="course" name="course" />

                                    </div>
                                    
                                </td>
                            </tr>

                        </table>

                        <div class="col-xs-3 container submitbutton">
                            <button class="btn btn-primary " type="submit" name="Submit" value="Submit"> Submit</button>
                        </div>

                    </div>

                    <?php echo form_close(); ?>
                </form>
            </div>

            <div id="User-wise" class="tabcontent">


                <form id="userreportform" class="form-horizontal login_form"
                    action="<?php echo base_url('/getUserReport'); ?>" method="post">

                    <div class="report-type">
                        <label for="userReportType" class="lbl-reporttype  required">Report type:</label>

                        <select name="userReportType" class="form-control report-select" id="userReportType"
                            onchange="enable_disable_user(this)">
                            <option value="notSelected">-- Select Report Type --</option>
                            <?php
                            // $session = \Config\Services::session();
                            
                            // if ($session->get('role') == 'SPV_ADMIN' || $session->get('role') == 'IGOT_TEAM_MEMBER') {
                            
                            //     echo
                            //         '
                            // <option class="options" value="userList">User list</option>
                            // <option class="options" value="userEnrolmentFull">User enrolment report</option>
                            // <option class="options" value="userEnrolmentSummary">User-wise enrolment summary</option>';
                            // } 
                            ?>

                            <option value="userProfile">User profile</option>
                            <option value="userEnrolment">User-wise enrolment report</option>

                        </select>

                    </div>


                    <hr />

                    <div class="container">
                        <table class="tbl-input" id="tbl-user" style="display:none">

                            <tr>
                                <td style="width:1%"><label class="required"></label></td>
                                <td>
                                    <div class="auto-widget">
                                        <input type="text" list="user_search_result" class="form-control" id="email"
                                            name="email" placeholder="Search email/phone" autocomplete="off" />
                                        <datalist id="user_search_result">
                                        </datalist>
                                        <input type="hidden" id="userid" name="userid" />


                                    </div>

                                </td>
                            </tr>
                        </table>

                        <div class="col-xs-3 container submitbutton">
                            <button class="btn btn-primary " type="submit" name="Submit" value="Submit"> Submit</button>
                        </div>

                    </div>

                    <?php echo form_close(); ?>
                </form>
            </div>

            <div id="Role-wise" class="tabcontent">
                <form id="rolereportform" class="form-horizontal login_form"
                    action="<?php echo base_url('/getRoleReport'); ?>" method="post">
                    <div class="report-type">
                        <label for="roleReportType" class="lbl-reporttype required">Report type:</label>
                        <?php
                        $session = \Config\Services::session();

                        if ($session->get('role') == 'SPV_ADMIN' || $session->get('role') == 'IGOT_TEAM_MEMBER') {

                            echo '<select name="roleReportType" class="form-control report-select"  onchange="enable_disable_mdo(this)"  id="roleReportType">
                        <option value="notSelected">-- Select Report Type --</option>
                        <option value="roleWiseCount">Role-wise count</option>
                        <option value="monthWiseMDOAdminCount">Month-wise MDO ADMIN Creation Count</option>
                        <option value="mdoAdminList">MDO ADMIN List</option>
                        <option value="cbpAdminList">CBP ADMIN List</option>
                        <option value="creatorList">CONTENT CREATOR List</option>
                        <option value="reviewerList">CONTENT REVIEWER List</option>
                        <option value="publisherList">CONTENT PUBLISHER List</option>
                        <option value="fracAdminList">FRAC ADMIN List</option>
                        <option value="fracCompetencyMember">FRAC COMPETENCY MEMBER List</option>
                        <option value="fracOneList">FRAC REVIEWER L1 List</option>
                        <option value="fracTwoList">FRAC REVIEWER L2 List</option>
                        <option value="ifuMemberList">IFU MEMBER List</option>
                        <option value="spvAdminList">SPV ADMIN List</option>
                        <option value="stateAdminList">STATE ADMIN List</option>
                        <option value="watMemberList">WAT MEMBER List</option>
                        </select>
                
';
                        } else if ($session->get('role') == 'MDO_ADMIN') {

                            echo '<select name="roleReportType" class="form-control  report-select" id="roleReportType" >
                                    <option value="notSelected">-- Select Report Type --</option>
                                    <option value="roleWiseCount">Role-wise count</option>
                                    <option value="mdoAdminList">MDO ADMIN List</option>
                                    <option value="cbpAdminList">CBP ADMIN List</option>
                                    <option value="creatorList">CONTENT CREATOR List</option>
                                    <option value="reviewerList">CONTENT REVIEWER List</option>
                                    <option value="publisherList">CONTENT PUBLISHER List</option>
                                    <option value="publicList">PUBLIC User List</option>
                        
                                </select>

';
                        } ?>
                    </div>
                    <hr />

                    <div class="container ">



                        <div class="col-xs-3 container submitbutton">
                            <button class="btn btn-primary " type="submit" name="Submit" value="Submit"> Submit</button>
                        </div>

                    </div>

                    <?php echo form_close(); ?>
                </form>


            </div>

            <div id="Analytics" class="tabcontent">
                <form id="analyticsreportform" class="form-horizontal login_form"
                    action="<?php echo base_url('/getAnalytics'); ?>" method="post">
                    <div class="report-type">
                        <label for="analyticsReportType" class="lbl-reporttype required">Report type:</label>
                        <?php
                        $session = \Config\Services::session();

                        if ($session->get('role') == 'SPV_ADMIN' || $session->get('role') == 'IGOT_TEAM_MEMBER') {

                            echo '<select name="analyticsReportType" class="form-control report-select" id="analyticsReportType">
                        <option value="notSelected">-- Select Report Type --</option>
                        <option value="dayWiseUserOnboarding">Day-wise User Onboarding</option>
                        <option value="monthWiseUserOnboarding">Month-wise User Onboarding</option>
                        <option value="monthWiseOrgOnboarding">Month-wise Organisation Onboarding</option>
                        <option value="monthWiseCourses">Month-wise Courses Published</option>
                        <option value="monthWiseCompletion">Month-wise Course Completions</option>
                        </select>';

                        }
                        ?>
                    </div>
                    <hr />

                    <div class="container ">



                        <div class="col-xs-3 container submitbutton">
                            <button class="btn btn-primary " type="submit" name="Submit" value="Submit"> Submit</button>
                        </div>

                    </div>

                    <?php echo form_close(); ?>
                </form>

            </div>



            <div id="Top-Performers" class="tabcontent">
                <form id="topreportform" class="form-horizontal login_form"
                    action="<?php echo base_url('/getTopPerformers'); ?>" method="post">
                    <div class="report-type">
                        <label for="topReportType" class="lbl-reporttype required">Report type:</label>
                        <select name="topReportType" class="form-control report-select" id="topReportType"
                            onchange="enable_disable_top(this)">
                            <option value="notSelected">-- Select Report Type --</option>
                            <option value="topUserEnrolment">Top Users based on course enrolments</option>
                            <option value="topUserCompletion">Top Users based on course completions</option>
                            <option value="topUserNotStarted">Users having courses enrolled, but not started</option>
                            <option value="topUserInProgress">Users having courses in progress</option>
                            <option value="topOrgOnboarding">Top Organisations based on user onboarding</option>
                            <option value="topOrgEnrolment">Top Organisations based on course enrolments</option>
                            <option value="topOrgCompletion">Top Organisations based on course completions</option>
                            <option value="topOrgMdoAdmin">Top Organisations based on MDO Admin count</option>
                            <option value="topCbpLiveCourses">Top CBP Providers based on no. of live courses</option>
                            <option value="topCbpUnderPublish">CBP Providers whose courses are under publish</option>
                            <option value="topCbpUnderReview">CBP Providers whose courses are under review</option>
                            <option value="topCbpDraftCourses">CBP Providers whose courses are in draft</option>
                            <option value="topCourseEnrolment">Top Courses based on enrolment</option>
                            <option value="topCourseCompletion">Top Courses based on completion</option>
                            <option value="topCourseRating">Top Courses based on rating</option>
                            <option value="topCoursesCompetencyWise">Top Courses based on competencies tagged</option>
                            <option value="topOrgCourseWise">Top performing organisations course-wise</option>
                            <option value="topOrgProgramWise">Top performing organisations program-wise</option>
                            <option value="topOrgCollectionWise">Top performing organisations curated collection-wise
                            </option>
                            <option value="topCourseInMonth">Top courses in a month based on completion</option>
                            <option value="topCompetency">Top competencies</option>
                        </select>
                    </div>
                    <hr />

                    <div class="container">
                        <table class="tbl-topcount">
                            <tr>
                                <td><label for="topCount" class="topcountlabel required">No. of Top records to be
                                        displayed:</label></td>
                                <td><input type="text" class="form-control topcount" id="topCount" name="topCount"
                                        autocomplete="off" /></td>
                            </tr>
                        </table>

                        <table class="tbl-input" id="tbl-top-course" style="display:none">


                            <tr>
                                <td style="width:1%"><label class="required"></label></td>
                                <td colspan="2">
                                    <div class="auto-widget" id="top-course">
                                        <input type="text" list="top_course_search_result" class="form-control"
                                            id="topcoursename" name="topcoursename" placeholder="Search Course"
                                            autocomplete="off" />
                                        <datalist id="top_course_search_result">
                                        </datalist>
                                        <input type="hidden" id="topcourse" name="topcourse" />


                                    </div>

                                </td>
                            </tr>


                        </table>
                        <table class="tbl-input" id="tbl-top-competency" style="display:none">
                            <tr>
                                <td style="width:1%"><label class="required"></label></td>
                                <td colspan="2">
                                    <div class="auto-widget" id="top-competency-type">
                                        <select class="form-control" id="competencyType" name="competencyType">
                                            <option value="notSelected">--Select Competency Type--</option>
                                            <option value="Domain">Domain</option>
                                            <option value="Behavioural">Behavioural</option>
                                            <option value="Functional">Functional</option>
                                            <option value="All">Overall</option>
                                        </select>

                                    </div>
                                </td>
                            </tr>

                        </table>

                        <table class="tbl-month" id="tbl-top-monthyear" style="display:none">

                            <tr>
                                <td><label class="topcountlabel required">Month</label></td>
                                <td>
                                    <div class="auto-widget" id="top-month">
                                        <select class="form-control monthyear-select" id="month" name="month">
                                            <?php
                                            foreach ($months as $month) {
                                                echo '<option value="' . $month->completed_month . '">' . $month->completed_month . '</option>';
                                            }
                                            ?>
                                        </select>

                                    </div>

                                </td>
                                <td><label class="topcountlabel required">Year</label></td>
                                <td>
                                    <div class="auto-widget" id="top-month">
                                        <select class="form-control monthyear-select" id="year" name="year">
                                            <?php
                                            foreach ($years as $year) {
                                                echo '<option  value="' . $year->completed_year . '">' . $year->completed_year . '</option>';
                                            }
                                            ?>
                                        </select>

                                    </div>

                                </td>
                            </tr>

                        </table>

                        <div class="col-xs-3 container submitbutton">
                            <button class="btn btn-primary " type="submit" name="Submit" value="Submit"> Submit</button>
                        </div>

                    </div>

                    <?php echo form_close(); ?>
                </form>

            </div>

            <div id="Miscellaneous" class="tabcontent">


                <form id="miscreportform" class="form-horizontal login_form"
                    action="<?php echo base_url('/getMiscReport'); ?>" method="post">

                    <div class="report-type">
                        <label for="miscReportType" class="lbl-reporttype  required">Report type:</label>

                        <select name="miscReportType" class="form-control report-select"
                            onchange="enable_disable_misc(this)" id="miscReportType">
                            <option value="notSelected">-- Select Report Type --</option>
                            <option value="rozgarMelaUserList">Rozgar Mela user list</option>
                            <option value="rozgarMelaUserReport">Rozgar Mela user enrolment report</option>
                            <option value="rozgarMelaKpCollection">Rozgar Mela users enrolled in Karmayogi Prarambh
                                Module</option>
                            <!-- <option value="rozgarMelaKpProgram">Rozgar Mela users enrolled in Karmayogi Prarambh Program</option> -->
                            <option value="rozgarMelaReport">Rozgar Mela organisation-wise summary</option>
                            <option value="rozgarMelaSummary">Rozgar Mela Course-wise summary</option>
                            <option value="designationWiseCount">Designation-wise user count</option>
                            <option value="competencySummary">Competency Summary</option>
                            <option value="summaryReport">Summary Report</option>
                        </select>

                    </div>


                    <hr />

                    <div class="container">
                        <table class="tbl-input" id="tbl-program" style="display:none">
                            <tr>
                                <td>
                                    <label for="course">ATI: </label>

                                </td>
                            </tr>
                            <tr>
                                <td style="width:1%"><label class="required"></label></td>
                                <td>
                                    <div class="auto-widget">
                                        <input type="text" list="course_search_result" class="form-control"
                                            id="coursename" name="coursename" placeholder="Search Program"
                                            autocomplete="off" />
                                        <datalist id="course_search_result">
                                        </datalist>
                                        <input type="hidden" id="course" name="course" />


                                    </div>

                                </td>
                            </tr>
                        </table>

                        <div class="col-xs-3 container submitbutton">
                            <button class="btn btn-primary " type="submit" name="Submit" value="Submit"> Submit</button>
                        </div>

                    </div>

                    <?php echo form_close(); ?>
                </form>
            </div>

            <div id="Dopt" class="tabcontent">


                <form class="form-horizontal login_form" action="<?php echo base_url('/getDoptReport'); ?>"
                    method="post">

                    <div class="report-type">
                        <label for="doptReportType" class="lbl-reporttype required">Report type:</label>

                        <select name="doptReportType" class="form-control report-select"
                            onchange="enable_disable_program(this)" id="doptReportType">
                            <option value="notSelected">-- Select Report Type --</option>
                            <option value="atiWiseOverview">ATI-wise overview</option>
                        </select>

                    </div>


                    <hr />

                    <div class="container">
                        <table class="tbl-input" id="tbl-program" style="display:none">
                            <tr>
                                <td>
                                    <label for="course">Program: </label>

                                </td>
                            </tr>
                            <tr>
                                <td style="width:1%"><label class="required"></label></td>
                                <td>
                                    <div class="auto-widget">
                                        <input type="text" list="course_search_result" class="form-control"
                                            id="coursename" name="coursename" placeholder="Search Program"
                                            autocomplete="off" />
                                        <datalist id="course_search_result">
                                        </datalist>
                                        <input type="hidden" id="course" name="course" />


                                    </div>

                                </td>
                            </tr>
                        </table>

                        <div class="col-xs-3 container submitbutton">
                            <button class="btn btn-primary " type="submit" name="Submit" value="Submit"> Submit</button>
                        </div>

                    </div>

                    <?php echo form_close(); ?>
                </form>
            </div>


            <div id="ATI" class="tabcontent">


                <form class="form-horizontal login_form" action="<?php echo base_url('/getDoptReport'); ?>"
                    method="post">

                    <div class="report-type">
                        <label for="doptReportType" class="lbl-reporttype  required">Report type:</label>

                        <select name="doptReportType" class="form-control report-select"
                            onchange="enable_disable_program(this)" id="doptReportType">
                            <option value="notSelected">-- Select Report Type --</option>
                            <option value="atiWiseOverview">ATI-wise overview</option>

                        </select>

                    </div>


                    <hr />

                    <div class="container">
                        <table class="tbl-input" id="tbl-program" style="display:none">
                            <tr>
                                <td>
                                    <label for="course">ATI: </label>

                                </td>
                            </tr>
                            <tr>
                                <td style="width:1%"><label class="required"></label></td>
                                <td>
                                    <div class="auto-widget">
                                        <input type="text" list="course_search_result" class="form-control"
                                            id="coursename" name="coursename" placeholder="Search Program"
                                            autocomplete="off" />
                                        <datalist id="course_search_result">
                                        </datalist>
                                        <input type="hidden" id="course" name="course" />


                                    </div>

                                </td>
                            </tr>
                        </table>

                        <div class="col-xs-3 container submitbutton">
                            <button class="btn btn-primary " type="submit" name="Submit" value="Submit"> Submit</button>
                        </div>

                    </div>

                    <?php echo form_close(); ?>
                </form>
            </div>





        </div>


    </section>


    <!--SCRIPTS -->

    <script>



        document.getElementById("defaultOpen").click();




        $('.search').select2({
            placeholder: 'Search Organisation',
            ajax: {
                url: '<?php echo base_url('/search'); ?>',
                dataType: 'json',
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            }
        });


        $(document).ready(function () {

            $('#ms_type').change(function () {

                var ms = $('#ms_type').val();

                var action = 'get_ministry';

                if (ms != 'notSelected') {
                    $.ajax({
                        url: "<?php echo base_url('/action'); ?>",
                        method: "POST",
                        data: {
                            ms: ms,
                            action: action
                        },
                        dataType: "JSON",
                        success: function (data) {
                            if (ms == 'ministry') {
                                var html = '<option value="notSelected">--Select Ministry--</option>';
                            }
                            else {
                                var html = '<option value="notSelected">--Select State--</option>';
                            }

                            for (var count = 0; count < data.length; count++) {

                                html += '<option value="' + data[count].ms_id + '">' + data[count].ms_name + '</option>';

                            }

                            $('#ministry').html(html);
                        }
                    });
                } else {
                    $('#ministry').val('notSelected');
                }
                $('#dept').val('notSelected');
                $('#org').val('notSelected');
            });
            $(document).ready(function () {

                $('#ministry').change(function () {

                    var ministry = $('#ministry').val();

                    var action = 'get_dept';

                    if (ministry != 'notSelected') {
                        $.ajax({
                            url: "<?php echo base_url('/action'); ?>",
                            method: "POST",
                            data: {
                                ministry: ministry,
                                action: action
                            },
                            dataType: "JSON",
                            success: function (data) {
                                var html =
                                    '<option value="notSelected">--Select Department--</option>';

                                for (var count = 0; count < data.length; count++) {

                                    html += '<option value="' + data[count].dept_id +
                                        '">' + data[
                                            count].dept_name + '</option>';

                                }

                                $('#dept').html(html);
                            }
                        });
                    } else {
                        $('#dept').val('notSelected');
                    }
                    $('#org').val('notSelected');
                });

                $('#dept').change(function () {

                    var dept = $('#dept').val();


                    var action = 'get_org';

                    if (dept != 'notSelected') {
                        $.ajax({
                            url: "<?php echo base_url('/action'); ?>",
                            method: "POST",
                            data: {
                                dept: dept,
                                action: action
                            },
                            dataType: "JSON",
                            success: function (data) {
                                var html =
                                    '<option value="notSelected">--Select Organisation--</option>';

                                for (var count = 0; count < data.length; count++) {
                                    html += '<option value="' + data[count].org_id +
                                        '">' + data[
                                            count].org_name + '</option>';
                                }

                                $('#org').html(html);
                            }
                        });
                    } else {
                        $('#org').val('notSelected');
                    }

                });

            });
        });

        $(document).ready(function () {

            $('select[name=courseReportType]').change(function () {

                if (this.value == 'courseEnrolmentReport') {
                    var action = 'get_course';

                    $.ajax({
                        url: "<?php echo base_url('/action'); ?>",
                        method: "POST",
                        data: {
                            action: action
                        },
                        dataType: "JSON",
                        success: function (data) {
                            var html =
                                '<option value="notSelected">--Select Course--</option>';

                            for (var count = 0; count < data.length; count++) {

                                html += '<option value="' + data[count].course_id + '">' + data[
                                    count].course_name + '</option>';

                            }

                            $('#course').html(html);
                        }
                    });

                } else if (this.value == 'programEnrolmentReport') {

                    var action = 'get_program';

                    $.ajax({
                        url: "<?php echo base_url('/action'); ?>",
                        method: "POST",
                        data: {
                            action: action
                        },
                        dataType: "JSON",
                        success: function (data) {
                            var html =
                                '<option value="notSelected">--Select Program--</option>';

                            for (var count = 0; count < data.length; count++) {

                                html += '<option value="' + data[count].program_id + '">' +
                                    data[
                                        count].program_name + '</option>';

                            }

                            $('#course').html(html);
                        }
                    });

                } else if (this.value == 'collectionEnrolmentReport' || this.value ==
                    "collectionEnrolmentCount") {

                    var action = 'get_collection';

                    $.ajax({
                        url: "<?php echo base_url('/action'); ?>",
                        method: "POST",
                        data: {
                            action: action
                        },
                        dataType: "JSON",
                        success: function (data) {
                            var html =
                                '<option value="notSelected">--Select Curated Collection--</option>';

                            for (var count = 0; count < data.length; count++) {

                                html += '<option value="' + data[count].curated_id + '">' +
                                    data[count].curated_name + '</option>';

                            }

                            $('#course').html(html);
                        }
                    });


                }

            });



        });


        $(document).ready(function () {


            $('input[type=radio][name=courseReportType]').change(function () {

                if (this.value == 'courseEnrolmentReport' || this.value == 'courseEnrolmentCount') {
                    var action = 'get_course';

                    $.ajax({
                        url: "<?php echo base_url('/action'); ?>",
                        method: "POST",
                        data: {
                            action: action
                        },
                        dataType: "JSON",
                        success: function (data) {
                            var html =
                                '<option value="notSelected">--Select Course--</option>';

                            for (var count = 0; count < data.length; count++) {

                                html += '<option value="' + data[count].course_id + '">' + data[
                                    count].course_name + '</option>';

                            }

                            $('#course').html(html);
                        }
                    });

                } else if (this.value == 'programEnrolmentReport' || this.value == 'programEnrolmentCount') {

                    var action = 'get_program';

                    $.ajax({
                        url: "<?php echo base_url('/action'); ?>",
                        method: "POST",
                        data: {
                            action: action
                        },
                        dataType: "JSON",
                        success: function (data) {
                            var html =
                                '<option value="notSelected">--Select Program--</option>';

                            for (var count = 0; count < data.length; count++) {

                                html += '<option value="' + data[count].program_id + '">' +
                                    data[
                                        count].program_name + '</option>';

                            }

                            $('#course').html(html);
                        }
                    });

                } else if (this.value == 'collectionEnrolmentReport' || this.value == 'collectionEnrolmentCount') {

                    var action = 'get_collection';

                    $.ajax({
                        url: "<?php echo base_url('/action'); ?>",
                        method: "POST",
                        data: {
                            action: action
                        },
                        dataType: "JSON",
                        success: function (data) {
                            var html =
                                '<option value="notSelected">--Select Curated Collection--</option>';

                            for (var count = 0; count < data.length; count++) {

                                html += '<option value="' + data[count].curated_id + '">' + data[count].curated_name + '</option>';

                            }

                            $('#course').html(html);
                        }
                    });

                }

            });



        });


        $(document).ready(function () {



            $('input[type=radio][name=mdoReportType]').change(function () {

                if (this.value == 'mdoUserCount') {
                    var action = 'get_course';

                    $.ajax({
                        url: "<?php echo base_url('/action'); ?>",
                        method: "POST",
                        data: {
                            action: action
                        },
                        dataType: "JSON",
                        success: function (data) {
                            var html =
                                '<option value="notSelected">--Select Course--</option>';

                            for (var count = 0; count < data.length; count++) {

                                html += '<option value="' + data[count].course_id + '">' + data[
                                    count].course_name + '</option>';

                            }

                            $('#course').html(html);
                        }
                    });

                } else if (this.value == 'programEnrolmentReport' || this.value ==
                    'programEnrolmentCount') {

                    var action = 'get_program';

                    $.ajax({
                        url: "<?php echo base_url('/action'); ?>",
                        method: "POST",
                        data: {
                            action: action
                        },
                        dataType: "JSON",
                        success: function (data) {
                            var html =
                                '<option value="notSelected">--Select Program--</option>';

                            for (var count = 0; count < data.length; count++) {

                                html += '<option value="' + data[count].program_id + '">' +
                                    data[
                                        count].program_name + '</option>';

                            }

                            $('#course').html(html);
                        }
                    });

                } else if (this.value == 'collectionEnrolmentReport' || this.value ==
                    'collectionEnrolmentCount') {

                    var action = 'get_collection';

                    $.ajax({
                        url: "<?php echo base_url('/action'); ?>",
                        method: "POST",
                        data: {
                            action: action
                        },
                        dataType: "JSON",
                        success: function (data) {
                            var html =
                                '<option value="notSelected">--Select Curated Collection--</option>';

                            for (var count = 0; count < data.length; count++) {

                                html += '<option value="' + data[count].program_id + '">' +
                                    data[
                                        count].program_name + '</option>';

                            }

                            $('#course').html(html);
                        }
                    });

                }

            });
        });
    </script>

    <!-- -->

</body>

</html>