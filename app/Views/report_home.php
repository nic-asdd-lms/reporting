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
        background-color: rgba(221, 72, 20, .2);
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

    header {
        background-color: rgba(247, 248, 249, 1);
        padding: .4rem 0 0;
    }

    .menu {
        padding: .4rem 2rem;
    }

    header ul {
        border-bottom: 1px solid rgba(242, 242, 242, 1);
        list-style-type: none;
        margin: 0;
        overflow: hidden;
        padding: 0;
        text-align: right;
    }

    header li {
        display: inline-block;
    }

    header li a {
        border-radius: 5px;
        color: rgba(0, 0, 0, .5);
        display: block;
        height: 44px;
        text-decoration: none;
    }

    header li.menu-item a {
        border-radius: 5px;
        margin: 5px 0;
        height: 38px;
        line-height: 36px;
        padding: .4rem .65rem;
        text-align: center;
    }

    header li.menu-item a:hover,
    header li.menu-item a:focus {
        background-color: rgba(221, 72, 20, .2);
        color: rgba(221, 72, 20, 1);
    }

    header .logo {
        float: left;
        height: 64px;
        padding: .4rem .5rem;
        color: rgba(221, 72, 20, .6);
    }

    header .menu-toggle {
        display: none;
        float: right;
        font-size: 2rem;
        font-weight: bold;
    }

    header .menu-toggle button {
        background-color: rgba(221, 72, 20, .6);
        border: none;
        border-radius: 3px;
        color: rgba(255, 255, 255, 1);
        cursor: pointer;
        font: inherit;
        font-size: 1.3rem;
        height: 36px;
        padding: 0;
        margin: 11px 0;
        overflow: visible;
        width: 40px;
    }

    header .menu-toggle button:hover,
    header .menu-toggle button:focus {
        background-color: rgba(221, 72, 20, .8);
        color: rgba(255, 255, 255, .8);
    }

    header .heroe {
        margin: 0 auto;
        max-width: 1100px;
        padding: 1rem 1.75rem 1.75rem 1.75rem;
    }

    header .heroe h1 {
        font-size: 2.5rem;
        font-weight: 500;
    }

    header .heroe h2 {
        font-size: 1.5rem;
        font-weight: 300;
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
        width: 100%;
        padding: 10px;
        margin: 15px;
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
                <h3>Report type:</h3>
                <form class="form-horizontal login_form" action="/getMDOReport" method="post">

                <div>
                    <input type="radio" id="mdoUserList" class="form-check-input" name="mdoReportType" value="mdoUserList">
                    <label for="mdoUserList">MDO-wise user list</label>
                </div>
                <div>
                    <input type="radio" id="mdoUserEnrolment" name="mdoReportType" value="mdoUserEnrolment">
                    <label for="mdoUserList">MDO-wise user enrolment report</label>
                </div>
                <div>

                    <input type="radio" id="ministryUserEnrolment" name="mdoReportType" value="ministryUserEnrolment">
                    <label for="mdoUserList">Enrolment report for all organisations under a ministry</label>
                </div>
                <div>

                    <input type="radio" id="mdoEnrolmentCount" name="mdoReportType" value="mdoEnrolmentCount">
                    <label for="mdoUserList">MDO-wise user enrolment and completion count</label>
                </div>
                <div>

                    <input type="radio" id="userWiseCount" name="mdoReportType" value="userWiseCount">
                    <label for="mdoUserList">User-wise enrolment and completion count</label>
                </div>

                <hr />

                <div>
                    <table class="submitbutton">
                        <tr>
                            <td>
                                <!-- <label for="ministry">Ministry</label> -->
                                <select name="ministry" class="form-control" id="ministry">
                                    <option value="notSelected">--Select Ministry--</option>
                                    <?php
                                foreach($ministry as $row)
                                {
                                    echo '<option value="'.$row->ms_id.'">'.$row->ministry_state_name.'</option>';
                                }
                                ?>
                                </select>
                            </td>
                            <td>
                                <!-- <label for="dept">Department</label> -->
                                <select name="dept" class="form-control" id="dept">
                                    <option value="notSelected">--Select Department--</option>

                                </select>
                            </td>
                            <td>
                                <!-- <label for="org">Organisation</label> -->
                                <select name="org" class="form-control" id="org">
                                    <option value="notSelected">--Select Organisation--</option>

                                </select>
                            </td>
                        </tr>
                    </table>


                </div>
                <div class="col-xs-3 container submitbutton">
                    <button class="btn btn-primary " type="submit" name="Submit" value="Submit"
                        onclick="getMDOReport(event,'mdoReportType','org')"> Submit</button>
                </div>



            </div>


            <div id="Course-wise" class="tabcontent">
                <h3>Report type:</h3>

                <form class="form-horizontal login_form" action="/getCourseReport" method="post">

                    <div>
                        <input type="radio" id="courseEnrolmentReport" name="courseReportType"
                            value="courseEnrolmentReport">
                        <label for="mdoUserList">Course-wise enrolment report</label>

                    </div>
                    <div>
                        <input type="radio" id="courseEnrolmentCount" name="courseReportType"
                            value="courseEnrolmentCount">
                        <label for="mdoUserList">Course-wise enrolment and completion count</label>

                    </div>
                    <div>
                        <input type="radio" id="programEnrolmentReport" name="courseReportType"
                            value="programEnrolmentReport">
                        <label for="mdoUserList">Program-wise enrolment report</label>

                    </div>
                    <div>
                        <input type="radio" id="programEnrolmentCount" name="courseReportType"
                            value="programEnrolmentCount">
                        <label for="mdoUserList">Program-wise enrolment and completion count</label>
                    </div>
                    <div>
                        <input type="radio" id="collectionEnrolmentReport" name="courseReportType"
                            value="collectionEnrolmentReport">
                        <label for="mdoUserList">Curated Collection-wise enrolment report</label>

                    </div>
                    <div>
                        <input type="radio" id="collectionEnrolmentCount" name="courseReportType"
                            value="collectionEnrolmentCount">
                        <label for="mdoUserList">Curated Collection-wise enrolment and completion count</label>
                    </div>

                    <hr />

                    <div class="container submitbutton">
                        <label for="course" >Course/Program/Collection: </label>
                        <select name="course" id="course" class="form-control">
                            <option value="notSelected">--Select Course / Program / Collection--</option>
                            <?php
                                foreach($course as $row)
                                {
                                    echo '<option value="'.$row->course_id.'">'.$row->course_name.'</option>';
                                }
                                ?>
                        </select>


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
    </script>

    <!-- -->

</body>

</html>