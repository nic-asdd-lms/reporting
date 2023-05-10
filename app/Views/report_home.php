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

            function functABC() {
                return new Promise(function (resolve, reject) {
                    //alert('ok') ; 
                    var org = $('#org').val();

                    $.get('<?php echo base_url('/checkOrgOnboarded'); ?>', { org: org }, function (data) {
                        //alert(data) ; 
                    });

                    // $.get({
                    //     url: "<?php //echo base_url('/checkOrgOnboarded'); ?>",
                    //     data: { org: org },
                    //     success: function (data) {
                    //         alert(data);
                    //         resolve(data) // Resolve promise and go to then()
                    //     },
                    //     error: function (err) {
                    //         alert(data);
                    //         reject(err) // Reject the promise and go to catch()
                    //     }
                    // });
                });
            }

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
                else if (mdoReportType == 'mdoUserList' || mdoReportType == 'mdoUserEnrolment')                //  Report type 2nd option validation 
                {
                    var ms = $('#ms_type').val();
                    var ministry = $('#ministry').val();
                    var dept = $('#dept').val();
                    var org = $('#org').val();

                    if (document.getElementById('ms_type').style.display != "none" && ms == 'notSelected') {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Please Select Ministry/State!',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }
                    else if (ms == 'ministry') {

                        if (ministry == 'notSelected') {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Please Select Ministry!',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                            return false;
                        }

                    }
                    else if (ms == 'state') {
                        var state = $('#ministry').val();
                        if (state == 'notSelected') {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Please Select State!',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                            return false;
                        }
                        else {
                            return true;
                        }
                    }



                }
                else if (mdoReportType == 'orgHierarchy')             //Report type 3rd option validation  
                {
                    var ms = $('#ms_type').val();
                    if (ms == 'notSelected') {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Please Select Ministry/State!',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }
                    else if (ms == 'ministry') {
                        var ministry = $('#ministry').val();
                        if (ministry == 'notSelected') {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Please Select Ministry!',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                            return false;
                        }
                        else {
                            return true;
                        }
                    }
                    else if (ms == 'state') {
                        var state = $('#ministry').val();
                        if (state == 'notSelected') {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Please Select State!',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                            return false;
                        }
                        else {
                            return true;
                        }
                    }
                }
                else if (mdoReportType == 'ministryUserEnrolment')       //  Report type 4th option validation 
                {
                    var ms = $('#ms_type').val();
                    if (ms == 'notSelected') {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Please Select Ministry/State!',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }
                    else if (ms == 'ministry') {
                        var ministry = $('#ministry').val();
                        if (ministry == 'notSelected') {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Please Select Ministry!',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                            return false;
                        }
                        else {
                            return true;
                        }
                    }
                    else if (ms == 'state') {
                        var state = $('#ministry').val();
                        if (state == 'notSelected') {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Please Select State!',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                            return false;
                        }
                        else {
                            return true;
                        }
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
                    if (course == 'notSelected') {
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
                    if (program == 'notSelected') {
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
                    if (curated == 'notSelected') {
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
                    if (collection == 'notSelected') {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Please Select Collection',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }
                    else {
                        return true;
                    }
                }
                else if (courseReportType == 'courseMinistrySummary')                //  Report type 5th option validation 
                {
                    var coursemin = $('#course').val();
                    if (coursemin == 'notSelected') {
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
        });

        function setMDO(data) {
            $('#ms_type').val(data.ms_type).trigger("change");
            // $('select[id^="ministry"] option:selected').attr("selected", null);
            // $('select[id^="ministry"] option[value="' + data.ms_id + '"]').prop("selected", "selected");
            //$('#ministry').find('option[value="' + data[0].ms_id + '"]').prop('selected', true).trigger("change");
            console.log($("#ministry option:selected").text());
            $('#ministry').find('option[value="' + data.ms_id + '"]').trigger("change");

            $('#dept').val(data.dept_id).trigger("change");
            $('#org').val(data.org_id);
        }

        $(document).on('change', 'input', function () {
            var options = $('datalist')[0].options;
            var val = $(this).val();
            var action = 'get_hierarchy';
            var ms = document.getElementById('ms_type');
            var ministry = document.getElementById('ministry');
            var dept = document.getElementById('dept');
            var organisation = document.getElementById('org');
            var org_search = document.getElementById('org_search');

            for (var i = 0; i < options.length; i++) {
                if (options[i].value === val) {
                    var selected = val;
                    break;
                }
            }

            $.ajax({

                url: "<?php echo base_url('/action') ?>",
                method: "POST",
                data: {
                    action: action,
                    org: selected
                },
                async: false,
                dataType: "JSON",
                success: function (data) {
                    $('input#ms_type_search').val(data.ms_type);
                    $('input#ministry_search').val(data.ms_id);
                    $('input#dept_search').val(data.dept_id);
                    $('input#org_search').val(data.org_id);

                    ms.style.display = "none";
                    ministry.style.display = "none";
                    dept.style.display = "none";
                    organisation.style.display = "none";
                    org_search.value = selected;

                    //setMDO(data[0]);
                    // $('#ms_type').val(data.ms_type).trigger("change");
                    // $('select[id^="ministry"] option:selected').attr("selected", null);
                    // $('select[id^="ministry"] option[value="' + data.ms_id + '"]').prop("selected", "selected");
                    // $('#ministry').find('option[value="' + data[0].ms_id + '"]').prop('selected', true).trigger("change");
                    // $('#ministry').find('option[value="' + data.ms_id + '"]').trigger("change");
                    // console.log($("#ministry option:selected").text());

                    // $('#dept').val(data.dept_id).trigger("change");
                    // $('#org').val(data.org_id);

                }

            });
        });


        $(document).ready(function () {
            $('#org_search').keyup(function () {
                var orgs = [];
                var action = 'search';
                search_key = document.getElementById('org_search').value;
                $.ajax({

                    url: "<?php echo base_url('/action') ?>",
                    method: "POST",
                    data: {
                        action: action,
                        search_key: search_key
                    },
                    dataType: "JSON",
                    success: function (data) {
                        html = '';
                        for (var count = 0; count < data.length; count++) {
                            html += '<option class="options" value="' + data[count].org_name + '">';
                        }

                        $('#search-result').html(html);


                    }

                });
            });
        });
    </script>
</head>

<body onload="initKeycloak()">
    <!-- HEADER: MENU + HEROE SECTION -->
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
                } else if ($session->get('role') == 'DOPT_ADMIN') {

                    echo '<button class="tablinks" onclick="openTab(event, \'Dopt\')" id="defaultOpen">DoPT Reports</button>';
                } else if ($session->get('role') == 'ATI_ADMIN') {

                    echo '<button class="tablinks" onclick="openTab(event, \'ATI\')" id="defaultOpen">ATI Reports</button>';
                }
                ?>
            </div>

            <div id="MDO-wise" class="tabcontent">
                <form id="mdowisereportform" class="form-horizontal login_form"
                    action="<?php echo base_url('/getMDOReport'); ?>" method="post">

                    <div class="report-type">
                        <label for="mdoReportType" class="lbl-reporttype required "> Report type: </label>
                        <select name="mdoReportType" class="form-control report-select"
                            onchange="enable_disable_mdo(this)" id="mdoReportType">
                            <option value="notSelected">-- Select Report Type --</option>
                            <?php
                            $session = \Config\Services::session();

                            if ($session->get('role') == 'SPV_ADMIN') {

                                echo
                                    '

                                    <option class="options" value="userList">User list</option>
                                    <option class="options" value="orgList">Organisations onboarded</option>
                                    <option class="options" value="orgHierarchy">Organisation hierarchy</option>
                                    <option class="options" value="mdoUserCount">MDO-wise user count</option>
                                    <option class="options" value="mdoUserList">MDO-wise user List</option>
                                    <option class="options" value="mdoUserEnrolment">MDO-wise user enrolment report</option>
                                    <option class="options" value="ministryUserEnrolment">User List for all organisations under a Ministry/State</option>
                        ';
                            } else if ($session->get('role') == 'MDO_ADMIN') {

                                echo '<option class="options" value="mdoUserList">User List</option>
    <option value="mdoUserEnrolment">User enrolment report</option>
    <option value="userWiseCount">User-wise enrolment and completion count</option>
    

';
                            } ?>

                        </select>
                    </div>
                    <hr />

                    <div class="container ">


                        <!-- <input type="text" id="search" placeholder="Search" class="form-control" /> -->
                        <div id="tbl">

                            <table class="submitbutton" id="tbl-mdo">
                                <?php
                                $session = \Config\Services::session();

                                if ($session->get('role') == 'SPV_ADMIN') {

                                    //              <tr>
                                    //             <td></td>
                                    //             <td>
                                    //             <div class="auto-widget" style="position:absolute">
                                    //             <input type="text" list="search-result" class="form-control" id="org_search" placeholder="Search Organisation" />
                                    //             <datalist id="search-result" >
                                    //         </datalist>
                                    //         <input type="hidden"  id="ms_type_search"  name="ms_type_search" value="" />
                                    //         <input type="hidden"  id="ministry_search"  name="ministry_search" value="" />
                                    //         <input type="hidden"  id="dept_search"  name="dept_search" value="" />
                                    //         <input type="hidden"  id="organisation_search"  name="org_search" value="" />
                                
                                    // </div>
                                
                                    // </td>
                                    // </tr>
                                
                                    echo '
                                    <tr>
                                    <td style="width:1%"><label class="required" ></label></td>
                                    <td  class="submitbutton">
                                    <select name="ms_type" class="form-control"  id="ms_type">
                                    <option value="notSelected">    --Ministry/State--  </option>
                                    <option value="ministry">   Ministry    </option>
                                    <option value="state">  State   </option>
                                    </select> 
                                    </td>
                                    </tr>
                    <tr>
                    <td style="width:1%"><label class="required" ></label></td>
                            <td  class="submitbutton">
                            
                            <select name="ministry" class="form-control required"  id="ministry">
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
                            <td style="width:1%"></td>
                            <td class="submitbutton">
                                <select name="dept" class="form-control" id="dept">
                        <option value="notSelected">--Select Department--</option>
                        </select>
                            </td>
                            </tr>
                            <tr>
                            <td style="width:1%"></td>
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
                            <option value="courseEnrolmentCount">Live courses summary</option>
                            <option value="underPublishCourses">Courses under publish</option>
                            <option value="underReviewCourses">Courses under review </option>
                            <option value="draftCourses">Draft courses</option>
                            <option value="courseEnrolmentReport">Course-wise enrolment report</option>
                            <option value="programEnrolmentReport">Program-wise enrolment report</option>
                            <option value="programEnrolmentCount">Program-wise summary</option>
                            <option value="collectionEnrolmentReport">Curated Collection-wise enrolment report</option>
                            <option value="collectionEnrolmentCount">Curated Collection-wise summary</option>
                            <option value="cbpProviderWiseCourseCount">CBP Provider-wise course count</option>
                            <?php
                            // $session = \Config\Services::session();

                            // if ($session->get('role') == 'SPV_ADMIN') {
                            //     echo '<option value="courseMinistrySummary">Ministry-wise summary for course</option>';
                            // } ?>
                        </select>

                    </div>


                    <hr />

                    <div class="container">
                        <table class="submitbutton" id="tbl-course">
                            <tr>

                                <td colspan="2">
                                    <label for="course">Course/Program/Collection: </label>
                                </td>
                            </tr>
                            <tr>
                                <td style="width:1%"><label class="required"></label></td>
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
                <form id="rolereportform" class="form-horizontal login_form"
                    action="<?php echo base_url('/getRoleReport'); ?>" method="post">
                    <div class="report-type">
                        <label for="roleReportType" class="lbl-reporttype required">Report type:</label>
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
                <form id="analyticsreportform" class="form-horizontal login_form"
                    action="<?php echo base_url('/getAnalytics'); ?>" method="post">
                    <div class="report-type">
                        <label for="analyticsReportType" class="lbl-reporttype required">Report type:</label>
                        <?php
                        $session = \Config\Services::session();

                        if ($session->get('role') == 'SPV_ADMIN') {

                            echo '<select name="analyticsReportType" class="form-control report-select" id="analyticsReportType">
                        <option value="notSelected">-- Select Report Type --</option>
                        <option value="dayWiseUserOnboarding">Day-wise User Onboarding</option>
                        <option value="monthWiseUserOnboarding">Month-wise User Onboarding</option>
                        <option value="monthWiseCourses">Month-wise Courses Published</option>
                        </select>';

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


            <div id="ATI" class="tabcontent">


                <form class="form-horizontal login_form" action="<?php echo base_url('/getAtiReport'); ?>"
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

                <form class="form-horizontal login_form" action="<?php echo base_url('/getCourseReport'); ?>"
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
    <!-- <section>
        <table id="tbl-result">
        </table>
    </section> -->




    <!-- SCRIPTS -->

    <script>

        function initKeycloak() {
            const keycloak = Keycloak('/assets/keycloak.json');
            const initOptions = {
                responseMode: 'fragment',
                flow: 'standard',
                onLoad: 'login-required'
            };
            keycloak.init(initOptions).success(function (authenticated) {
                //  alert(keycloak);

                var subject = keycloak.subject;

                myarr = subject.split(":");
                Cookies.set('uid', myarr[2]);
                Cookies.set('token', keycloak.token);
                Cookies.set('refreshToken', keycloak.refreshToken);
                //alert(keycloak.token);
                // Cookies.set('role', 'SPV_ADMIN');
                //Cookies.set('callback',JSON.stringify(keycloak.tokenParsed.resource_access.php_service.permission));
                if (authenticated) {
                    //document.getElementById("test").innerHTML = Cookies.get('uid');
                    console.log('Init Success (' + (authenticated ? 'Authenticated token : ' + JSON.stringify(keycloak) : 'Not Authenticated') + ')');
                    window.location.replace("/home");
                }
                else {
                    window.location.replace("/unauthorized");
                }
            }).catch(function () {
                alert('failed to initialize');
            });
        }

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


        $(function () {
            $("#org_search").autocomplete({
                source: "<?php echo base_url('/search'); ?>",
                select: function (event, ui) {
                    event.preventDefault();
                    $("#org_search").val(ui.item.id);
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