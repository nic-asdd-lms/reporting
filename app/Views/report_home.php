<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>iGOT Reports</title>
    <meta name="description" content="The small framework with powerful features">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/favicon.ico">
    <!-- Datatable CSS -->
    <link href='//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css' rel='stylesheet' type='text/css'>

    <!-- jQuery Library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Datatable JS -->
    <script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <!-- STYLES -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

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
    }

    .further h2:first-of-type {
        padding-top: 0;
    }

    footer {
        background-color: rgba(221, 72, 20, .8);
        text-align: center;
    }

    footer .environment {
        color: rgba(255, 255, 255, 1);
        padding: 2rem 1.75rem;
    }

    footer .copyrights {
        background-color: rgba(62, 62, 62, 1);
        color: rgba(200, 200, 200, 1);
        padding: .25rem 1.75rem;
    }



    @media (max-width: 629px) {
        header ul {
            padding: 0;
        }

        header .menu-toggle {
            padding: 0 1rem;
        }

        header .menu-item {
            background-color: rgba(244, 245, 246, 1);
            border-top: 1px solid rgba(242, 242, 242, 1);
            margin: 0 15px;
            width: calc(100% - 30px);
        }

        header .menu-toggle {
            display: block;
        }

        header .hidden {
            display: none;
        }

        header li.menu-item a {
            background-color: rgba(221, 72, 20, .1);
        }

        header li.menu-item a:hover,
        header li.menu-item a:focus {
            background-color: rgba(221, 72, 20, .7);
            color: rgba(255, 255, 255, .8);
        }
    }

    .tab {
        overflow: hidden;
        border: 1px solid #ccc;
        background-color: #f1f1f1;
    }

    /* Style the buttons that are used to open the tab content */
    .tab button {
        background-color: inherit;
        float: left;
        border: none;
        outline: none;
        cursor: pointer;
        padding: 14px 16px;
        transition: 0.3s;
    }

    /* Change background color of buttons on hover */
    .tab button:hover {
        background-color: #ddd;
    }

    /* Create an active/current tablink class */
    .tab button.active {
        background-color: #ccc;
    }

    /* Style the tab content */
    .tabcontent {
        display: none;
        padding: 6px 12px;
        border: 1px solid #ccc;
        border-top: none;
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
    </style>
</head>

<body>

    <!-- HEADER: MENU + HEROE SECTION -->


    <!-- CONTENT -->

    <section>



        <div id="body">
            <div class="tab">
                <button class="tablinks" onclick="openTab(event, 'MDO-wise')" id="defaultOpen">MDO-wise Reports</button>
                <button class="tablinks" onclick="openTab(event, 'Course-wise')">Course/Program-wise Reports</button>

            </div>

            <div id="MDO-wise" class="tabcontent">
                <label for="mdoReportType">Report type:</label>
                <form class="form-horizontal login_form" action="/reporting/getMDOReport" method="post">
                    <?php 
                    $session = \Config\Services::session();
		
                    if($session->get('role')=='SPV_ADMIN') {
                    
                        echo '<select name="mdoReportType" class="form-control" id="mdoReportType">
                        <option value="notSelected">-- Select Report Type --</option>
                        <option value="mdoUserList">MDO-wise user list</option>
                        <option value="mdoUserCount">MDO-wise user count</option>
                        <option value="mdoAdminList">MDO Admin list</option>
                        <option value="mdoUserEnrolment">MDO-wise user enrolment report</option>
                        <option value="ministryUserEnrolment">User list for all organisations under a ministry</option>
                        </select>
                
'; }
else if($session->get('role')=='MDO_ADMIN') {
                    
    echo '<select name="mdoReportType" class="form-control" id="mdoReportType">
    <option value="notSelected">-- Select Report Type --</option>
    <option value="mdoUserList">User list</option>
    <option value="mdoUserEnrolment">User enrolment report</option>
    <option value="userWiseCount">User-wise enrolment and completion count</option>
    </select>

'; } ?>
                    <hr />

                    <div class="container ">
                        <?php 
                    $session = \Config\Services::session();
		
                    if($session->get('role')=='SPV_ADMIN') {
                        echo '<table class="submitbutton" >
                        <tr>
                            <td  class="submitbutton">
                            <select name="ministry" class="form-control" id="ministry">
                        <option value="notSelected">--Select Ministry--</option>';
                        
                    foreach($ministry as $row)
                    {
                        echo '<option value="'.$row->ms_id.'">'.$row->ministry_state_name.'</option>';
                    }
                  
                    echo '</select>
                        
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
                        </tr>
                    </table>';
                }
                ?>

                        <div class="col-xs-3 container submitbutton">
                            <button class="btn btn-primary " type="submit" name="Submit" value="Submit"> Submit</button>
                        </div>

                    </div>

                    <?php echo form_close(); ?>
                </form>


            </div>


            <div id="Course-wise" class="tabcontent">
                <label for="courseReportType">Report type:</label>

                <form class="form-horizontal login_form" action="/reporting/getCourseReport" method="post">

                <select name="courseReportType" class="form-control" id="mdoReportType">
                        <option value="notSelected">-- Select Report Type --</option>
                        <option value="courseEnrolmentReport">Course-wise enrolment report</option>
                        <option value="courseEnrolmentCount">Course-wise enrolment and completion count</option>
                        <option value="programEnrolmentReport">Program-wise enrolment report</option>
                        <option value="programEnrolmentCount">Program-wise enrolment and completion count</option>
                        <option value="collectionEnrolmentReport">Curated Collection-wise enrolment report</option>
                        <option value="collectionEnrolmentCount">Curated Collection-wise enrolment and completion count</option>
                        </select>
                
 
                   

                    <hr />

                    <div class="container">
                        <table class="submitbutton">
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
                                foreach($course as $row)
                                {
                                    echo '<option value="'.$row->course_id.'">'.$row->course_name.'</option>';
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
    <!-- <div class="further">

        <section>
            <table id='report' class='display dataTable'>
                

                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Organisation</th>
                        <th>Email ID</th>
                        <th>Status</th>
                        <th>Completion Percentage</th>
                        <th>Completed On</th>
                    </tr>
                </thead>

            </table>


        </section>

    </div> -->

    <!-- FOOTER: DEBUG INFO + COPYRIGHTS -->



    <!-- SCRIPTS -->

    <script>
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

                            html += '<option value="' + data[count].dep_id + '">' + data[
                                count].dep_name + '</option>';

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
                            html += '<option value="' + data[count].org_id + '">' + data[
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


    $(document).ready(function() {



        $('input[type=radio][name=courseReportType]').change(function() {

            if (this.value == 'courseEnrolmentReport' || this.value == 'courseEnrolmentCount') {
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

            } else if (this.value == 'programEnrolmentReport' || this.value == 'programEnrolmentCount') {

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

            } else if (this.value == 'collectionEnrolmentReport' || this.value == 'collectionEnrolmentCount') {

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

                            html += '<option value="' + data[count].curated_id + '">' + data[count].curated_name + '</option>';

                        }

                        $('#course').html(html);
                    }
                });

            }

        });



    });


    $(document).ready(function() {



        $('input[type=radio][name=mdoReportType]').change(function() {

            if (this.value == 'mdoUserCount') {
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
                'collectionEnrolmentCount') {

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