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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <!-- Datatable JS -->
    <script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <!-- STYLES -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>


    <style {csp-style-nonce}>
    * {
        transition: background-color 300ms ease, color 300ms ease;
    }

    *:focus {
        outline: none;
    }

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

    .lbl-reporttype {
        font-weight: 600;
        margin: 20px;
    }


    section {
        margin: 0 auto;
        max-width: 1100px;
        padding: 2.5rem 1.75rem 3.5rem 1.75rem;
    }

    section h1 {
        margin-bottom: 2.5rem;
    }

    section h2 {
        font-size: 120%;
        line-height: 2.5rem;
        padding-top: 1.5rem;
    }

    section pre {
        background-color: rgba(247, 248, 249, 1);
        border: 1px solid rgba(242, 242, 242, 1);
        display: block;
        font-size: .9rem;
        margin: 2rem 0;
        padding: 1rem 1.5rem;
        white-space: pre-wrap;
        word-break: break-all;
    }

    section code {
        display: block;
    }

    section a {
        color: rgba(221, 72, 20, 1);
    }

    section svg {
        margin-bottom: -5px;
        margin-right: 5px;
        width: 25px;
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







    .tab {
        overflow: hidden;
        border: 1px solid #e2693f24;
        background-color: #f4d3a7;
    }

    /* Style the buttons that are used to open the tab content */
    .tab button {
        background-color: #f0a54200;
        color: rgb(38, 64, 146);
        float: left;
        border: none;
        outline: none;
        cursor: pointer;
        padding: 14px 16px;
        transition: 0.3s;
    }

    /* Change background color of buttons on hover */
    .tab button:hover {
        background-color: #f0a54263;
    }

    /* Create an active/current tablink class */
    .tab button.active {
        background-color: #ef951eba;
        color: #fff;
    }

    /* Style the tab content */
    .tabcontent {
        display: none;
        padding: 6px 12px;
        border: 1px solid #c6562f08;
        border-top: none;
        background-color: #ef951e0f;
        color: rgb(38, 64, 146);
    }

    .submitbutton {
        width: 80%;
        padding: 10px;
        margin: 15px;
        text-align: center;
    }

    .radio {
        font-weight: 500;
        display: inline;
    }

    .report-select {
        width: 50%;
        display: inline;
        margin-left: 40px;
    }

    .report-type {
        padding: 10px;
        margin: 15px;

    }
    </style>
</head>

<body  onload="initKeycloak()">

    <!-- HEADER: MENU + HEROE SECTION -->


    <!-- CONTENT -->

    <section>



        <div id="body">
            <div class="tab">
                <?php
                $session = \Config\Services::session();

                if ($session->get('role') == 'SPV_ADMIN') {

                    echo '
                <button class="tablinks" onclick="openTab(event, \'MDO-wise\')" id="defaultOpen">MDO-wise Reports</button>
                <button class="tablinks" onclick="openTab(event, \'Course-wise\')">Course-wise Reports</button>
                <button class="tablinks" onclick="openTab(event, \'Role-wise\')">Role-wise Reports</button>
                <button class="tablinks" onclick="openTab(event, \'Analytics\')">Analytics</button>
                ';
                } else if ($session->get('role') == 'MDO_ADMIN') {
                    echo '<button class="tablinks" onclick="openTab(event, \'MDO-wise\')" id="defaultOpen">MDO-wise Reports</button>
                    <button class="tablinks" onclick="openTab(event, \'Course-wise\')">Course-wise Reports</button>
                    <button class="tablinks" onclick="openTab(event, \'Role-wise\')">Role-wise Reports</button>
                    ';
                }
                else if ($session->get('role') == 'DOPT_ADMIN') {

                    echo '<button class="tablinks" onclick="openTab(event, \'Dopt\')" id="defaultOpen">DoPT Reports</button>';
                }
                ?>
            </div>

            <div id="MDO-wise" class="tabcontent">
                <form class="form-horizontal login_form" action="<?php echo base_url('/getMDOReport');?>"
                    method="post">
                    <div class="report-type">
                        <label for="mdoReportType" class="lbl-reporttype">Report type:</label>
                        <select name="mdoReportType" class="form-control report-select"
                            onchange="enable_disable_mdo(this)" id="mdoReportType">
                            <option value="notSelected">-- Select Report Type --</option>
                            <?php
                            $session = \Config\Services::session();

                            if ($session->get('role') == 'SPV_ADMIN') {

                                echo
                                    '
                    <option value="mdoUserCount">MDO-wise user count</option>
                        <option value="mdoUserList">MDO-wise user List</option>
                        <option value="mdoUserEnrolment">MDO-wise user enrolment report</option>
                        <option value="ministryUserEnrolment">User List for all organisations under a Ministry/State</option>
                        ';
                            } else if ($session->get('role') == 'MDO_ADMIN') {

                                echo '<option value="mdoUserList">User List</option>
    <option value="mdoUserEnrolment">User enrolment report</option>
    <option value="userWiseCount">User-wise enrolment and completion count</option>
    

';
                            } ?>


                        </select>
                    </div>
                    <hr />

                    <div class="container ">
                        <!-- <div class="auto-widget">
    <p>Organisation: <input type="text" id="org_search" placeholder="Search Organisation" /></p>
</div> -->

                        <!-- <input type="text" id="search" placeholder="Search" class="form-control" /> -->

                        <div id="tbl">


                            <table class="submitbutton" id="tbl-mdo">
                                <?php
                                $session = \Config\Services::session();

                                if ($session->get('role') == 'SPV_ADMIN') {
                                    echo '

                                    <tr>
                                    <td  class="submitbutton">
                                    
                                    <select name="ms_type" class="form-control"  id="ms_type">
                                <option value="notSelected">--Ministry/State-</option>
                                <option value="ministry">Ministry</option>
                                <option value="state">State</option>
        
                                            
                                            
                            </select>
                                
                                </td>
                                    </tr>
                    <tr>
                            <td  class="submitbutton">
                            
                            <select name="ministry" class="form-control"  id="ministry">
                         <option value="notSelected">--Select Ministry/State--</option>';

                        //             foreach ($ministry as $row) {
                        //                 echo '<option value="' . $row->ms_id . '">' . $row->ms_name . '</option>';
                        //             }

                                     echo 
                                    '	
                    </select>
                        
                        </td>
                            </tr>
                            <tr>
                            <td class="submitbutton">
                                <select name="dept" class="form-control" id="dept">
                        <option value="notSelected">--Select Department--</option>
                        </select>
                            </td>
                            </tr>
                            <tr>
                             <td class="submitbutton">
                               <select name="org" class="form-control" id="org">
                        <option value="notSelected">--Select Organisation--</option>
                        </select>
                        
                            </td>
                        </tr>';

                                } ?>

                            </table>
                        </div>

                        <!-- <div>
                            <label class="error">
                                <?php //if ($error != null) {
                                  //  echo $error;
                               // } ?>
                            </label>
                        </div> -->

                        <div class="col-xs-3 container submitbutton">
                            <button class="btn btn-primary " type="submit" name="Submit" value="Submit"> Submit</button>
                        </div>

                    </div>

                    <?php echo form_close(); ?>
                </form>


            </div>


            <div id="Course-wise" class="tabcontent">


                <form class="form-horizontal login_form" action="<?php echo base_url('/getCourseReport');?>"
                    method="post">

                    <div class="report-type">
                        <label for="courseReportType" class="lbl-reporttype">Report type:</label>

                        <select name="courseReportType" class="form-control report-select"
                            onchange="enable_disable_course(this)" id="courseReportType">
                            <option value="notSelected">-- Select Report Type --</option>
                            <option value="courseEnrolmentReport">Course-wise enrolment report</option>
                            <option value="courseEnrolmentCount">Course-wise summary</option>
                            <option value="programEnrolmentReport">Program-wise enrolment report</option>
                            <option value="programEnrolmentCount">Program-wise summary</option>
                            <option value="collectionEnrolmentReport">Curated Collection-wise enrolment report</option>
                            <option value="collectionEnrolmentCount">Curated Collection-wise summary</option>
                            <?php
                            $session = \Config\Services::session();

                            if ($session->get('role') == 'SPV_ADMIN') {
                                echo '<option value="courseMinistrySummary">Ministry-wise summary for course</option>';
                            } ?>
                        </select>

                    </div>


                    <hr />

                    <div class="container">
                        <table class="submitbutton" id="tbl-course">
                            <tr>
                                <td>
                                    <label for="course">Course/Program/Collection: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="submitbutton">
                                    <select name="course" id="course" class="form-control">
                                        <option value="notSelected">--Select Course--</option>
                                        <?php
                                        foreach ($course as $row) {
                                            echo '<option value="' . $row->course_id . '">' . $row->course_name . '</option>';
                                        }
                                        ?>
                                    </select>
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
                <form class="form-horizontal login_form" action="<?php echo base_url('/getRoleReport');?>"
                    method="post">
                    <div class="report-type">
                        <label for="roleReportType" class="lbl-reporttype">Report type:</label>
                        <?php
                        $session = \Config\Services::session();

                        if ($session->get('role') == 'SPV_ADMIN') {

                            echo '<select name="roleReportType" class="form-control report-select"  onchange="enable_disable_mdo(this)"  id="roleReportType">
                        <option value="notSelected">-- Select Report Type --</option>
                        <option value="roleWiseCount">Role-wise count</option>
                        <option value="monthWiseMDOAdminCount">Month-wise MDO ADMIN Creation Count</option>
                        <option value="mdoAdminList">MDO ADMIN List</option>
                        <option value="cbpAdminList">CBP ADMIN List</option>
                        <option value="creatorList">CONTENT CREATOR List</option>
                        <option value="reviewerList">CONTENT REVIEWER List</option>
                        <option value="publisherList">CONTENT PUBLISHER List</option>
                        <option value="editorList">EDITOR List</option>
                        <option value="fracAdminList">FRAC ADMIN List</option>
                        <option value="fracCompetencyMember">FRAC COMPETENCY MEMBER List</option>
                        <option value="fracL1List">FRAC REVIEWER L1 List</option>
                        <option value="fracL2List">FRAC REVIEWER L2 List</option>
                        <option value="ifuMemberList">IFU MEMBER List</option>
                        <option value="publicList">PUBLIC User List</option>
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
                        <!-- <input type="text" id="search" placeholder="Search" class="form-control" /> -->



                        <div class="col-xs-3 container submitbutton">
                            <button class="btn btn-primary " type="submit" name="Submit" value="Submit"> Submit</button>
                        </div>

                    </div>

                    <?php echo form_close(); ?>
                </form>


            </div>

            <div id="Analytics" class="tabcontent">
                <form class="form-horizontal login_form" action="<?php echo base_url('/getAnalytics');?>"
                    method="post">
                    <div class="report-type">
                        <label for="analyticsReportType" class="lbl-reporttype">Report type:</label>
                        <?php
                        $session = \Config\Services::session();

                        if ($session->get('role') == 'SPV_ADMIN') {

                            echo '<select name="analyticsReportType" class="form-control report-select" id="analyticsReportType">
                        <option value="notSelected">-- Select Report Type --</option>
                        <option value="dayWiseUserOnboarding">Day-wise User Onboarding</option>
                        <option value="monthWiseUserOnboarding">Month-wise User Onboarding</option>
                        <option value="monthWiseCourses">Month-wise Courses Published</option>
                        </select>
                
';
                        }
                        ?>
                    </div>
                    <hr />

                    <div class="container ">
                        <!-- <input type="text" id="search" placeholder="Search" class="form-control" /> -->



                        <div class="col-xs-3 container submitbutton">
                            <button class="btn btn-primary " type="submit" name="Submit" value="Submit"> Submit</button>
                        </div>

                    </div>

                    <?php echo form_close(); ?>
                </form>


            </div>

            <div id="Dopt" class="tabcontent">


                <form class="form-horizontal login_form" action="<?php echo base_url('/getDoptReport');?>"
                    method="post">

                    <div class="report-type">
                        <label for="doptReportType" class="lbl-reporttype">Report type:</label>

                        <select name="doptReportType" class="form-control report-select"
                            onchange="enable_disable_program(this)" id="doptReportType">
                            <option value="notSelected">-- Select Report Type --</option>
                            <option value="atiWiseOverview">ATI-wise overview</option>
                        </select>

                    </div>


                    <hr />

                    <div class="container">
                        <table class="submitbutton" id="tbl-program" style="display:none">
                            <tr>
                                <td>
                                    <label for="course">ATI: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="submitbutton">
                                    <select name="course" id="course" class="form-control">
                                        <option value="notSelected">--Select ATI --</option>
                                        <?php
                                        foreach ($course as $row) {
                                            echo '<option value="' . $row->course_id . '">' . $row->course_name . '</option>';
                                        }
                                        ?>
                                    </select>
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

            <div id="Program-wise" class="tabcontent">


                <form class="form-horizontal login_form" action="<?php echo base_url('/getCourseReport');?>"
                    method="post">

                    <div class="report-type">
                        <label for="courseReportType" class="lbl-reporttype">Report type:</label>

                        <select name="courseReportType" class="form-control report-select"
                            onchange="enable_disable_course(this)" id="mdoReportType">
                            <option value="notSelected">-- Select Report Type --</option>
                            <option value="courseEnrolmentReport">Course-wise enrolment report</option>
                            <option value="courseEnrolmentCount">Course-wise enrolment and completion count</option>
                            <option value="programEnrolmentReport">Program-wise enrolment report</option>
                            <option value="programEnrolmentCount">Program-wise enrolment and completion count</option>
                            <option value="collectionEnrolmentReport">Curated Collection-wise enrolment report</option>
                            <option value="collectionEnrolmentCount">Curated Collection-wise enrolment and completion
                                count</option>
                        </select>

                    </div>


                    <hr />

                    <div class="container">
                        <table class="submitbutton" id="tbl-course">
                            <tr>
                                <td>
                                    <label for="course">Course/Program/Collection: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="submitbutton">
                                    <select name="course" id="course" class="form-control">
                                        <option value="notSelected">--Select Course / Program / Collection--</option>
                                        <?php
                                        foreach ($course as $row) {
                                            echo '<option value="' . $row->course_id . '">' . $row->course_name . '</option>';
                                        }
                                        ?>
                                    </select>
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


    <!-- SCRIPTS -->

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
                        if(authenticated){
                            window.location.replace("/");
                        }
                        else 
                        {
                            alert('Show error page'); 
                        }
            }).catch(function() {
                    alert('failed to initialize');
            });
        }
    function toggleMenu() {
        var menuItems = document.getElementsByClassName('menu-item');
        for (var i = 0; i < menuItems.length; i++) {
            var menuItem = menuItems[i];
            menuItem.classList.toggle("hidden");
        }
    }
    document.getElementById("defaultOpen").click();

    function openTab(evt, reprotName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(reprotName).style.display = "block";
        evt.currentTarget.className += " active";
    }

    function enable_disable_mdo(value) {

        mdo = document.getElementById('tbl');
        dept = document.getElementById('dept');
        org = document.getElementById('org');

        mdo.style.display = value.value == "mdoUserCount" ? "none" : "block";
        dept.style.display = value.value == "ministryUserEnrolment" ? "none" : "block";
        org.style.display = value.value == "ministryUserEnrolment" ? "none" : "block";
    }

    function enable_disable_course(value) {
        course = document.getElementById("tbl-course");
        if (value.value == "courseEnrolmentCount" || value.value == "programEnrolmentCount") {
            course.style.display = "none";
        } else {
            course.style.display = "block";
        }

    }

    function enable_disable_program(value) {
        course = document.getElementById("tbl-program");
        if (value.value == "atiWiseOverview") {
            course.style.display = "none";
        } else {
            course.style.display = "block";
        }

    }

    $('.search').select2({
        placeholder: 'Search Organisation',
        ajax: {
            url: '<?php echo base_url('/search'); ?>',
            dataType: 'json',
            processResults: function(data) {
                return {
                    results: data
                };
            },
            cache: true
        }
    });

    function getSuggestions(value) {
        var searchKey = value.value;
        var action = 'search';

        //alert(value.value);
        display = document.getElementById('display');
        $.ajax({
            url: "<?php echo base_url('/action'); ?>",
            method: "POST",
            data: {
                key: searchKey,
                action: action
            },
            dataType: "JSON",
            success: function(data) {
                html = '<ul>';
                for (var count = 0; count < data.length; count++) {

                    html += '<li>' + data[count].org_name + '</li>';

                }
                html += '</ul>';
                $('#display').html(html);
            }
        });


    }

    $(document).ready(function() {

        $('#ms_type').change(function() {

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
                    success: function(data) {
                        if(ms == 'ministry') {
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
        $(document).ready(function() {

            $('#ministry').change(function() {

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
                        success: function(data) {
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

            $('#dept').change(function() {

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
                        success: function(data) {
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

    $(document).ready(function() {



        $('select[name=courseReportType]').change(function() {

            if (this.value == 'courseEnrolmentReport') {
                var action = 'get_course';

                $.ajax({
                    url: "<?php echo base_url('/action'); ?>",
                    method: "POST",
                    data: {
                        action: action
                    },
                    dataType: "JSON",
                    success: function(data) {
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
                    success: function(data) {
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
                    success: function(data) {
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


    $(function() {
        $("#org_search").autocomplete({
            source: "<?php echo base_url('/search'); ?>",
            select: function(event, ui) {
                event.preventDefault();
                $("#org_search").val(ui.item.id);
            }
        });
    });
    </script>

    <!-- -->

</body>

</html>