<?php

namespace App\Controllers;

use App\Controllers\Home;

use App\Models\MasterUserModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\MasterStructureModel;
use App\Models\MasterOrganizationModel;
use App\Models\MasterCourseModel;
use App\Models\UserEnrolmentCourse;
use App\Models\UserEnrolmentProgram;
use App\Models\MasterProgramModel;
use App\Models\MasterCollectionModel;
use App\Models\DataUpdateModel;

use PHPExcel_IOFactory;
use PHPExcel_Reader_HTML;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class Report extends BaseController
{
    var $resultArray;
    
    public function getMDOReport()
    {
        try {
            $request = service('request');
            $session = \Config\Services::session();
            $table = new \CodeIgniter\View\Table();
            $table->setTemplate($GLOBALS['tableTemplate']);
                        
            $mdoReportType = $request->getPost('mdoReportType');


            $role = $session->get('role');

            $home = new Home();
            $user = new MasterUserModel();
            $enrolment = new UserEnrolmentCourse();
            $org_hierarchy = new MasterStructureModel();
            $lastUpdate = new DataUpdateModel();

            if ($mdoReportType == 'notSelected') {
                echo '<script>alert("Please select report type!");</script>';
                return view('header_view')
                    . view('footer_view');
            } else {
                $data['lastUpdated'] = '[Report last updated on ' . $lastUpdate->getReportLastUpdatedTime() . ']';

                if ($role == 'SPV_ADMIN') {
                    $ministry = $request->getPost('ministry');
                    $dept = $request->getPost('dept');
                    $org = $request->getPost('org');
                } else if ($role == 'MDO_ADMIN') {
                    $ministry = $session->get('ministry');
                    $dept = $session->get('department');
                    $org = $session->get('organisation');
                }

                if ($ministry != "notSelected") {
                    $ministryName = $org_hierarchy->getMinistryStateName($ministry);
                }
                if ($dept != "notSelected") {
                    $deptName = $org_hierarchy->getDeptName($dept);

                }

                if ($org != "notSelected") {
                    $orgName = $home->getOrgName($org);

                } else if ($dept != "notSelected") {
                    $org = $dept;
                    $orgName = $org_hierarchy->getDeptName($dept);
                } else if ($ministry != "notSelected") {
                    $org = $ministry;
                    $orgName = $org_hierarchy->getMinistryStateName($ministry);
                }

                if ($mdoReportType == 'mdoUserList') {

                    if ($ministry == "notSelected") {
                        echo '<script>alert("Please select ministry!");</script>';
                        return view('header_view')
                            . view('footer_view');
                    } else {
                        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Contact No.', 'Created Date', 'Roles', 'Profile Update Status');
    
                        $result = $user->getUserByOrg($orgName);
                        
                        $session->setTempdata('resultArray',$result->getResultArray(),300);
                        $session->setTempdata('fileName',$orgName . '_UserList',300);
                        
                        $data['resultHTML'] = $table->generate($result);;
                        $data['reportTitle'] = 'Users onboarded from organisation - "' . $orgName . '"';

                    }

                } else
                    if ($mdoReportType == 'mdoUserCount') {
                        $table->setHeading('Organisation', 'User Count');
    
                        $result = $user->getUserCountByOrg();
                        
                        $session->setTempdata('resultArray',$result->getResultArray(),300);
                        $session->setTempdata('fileName','MDOWiseUserCount',300);
                        
                        $data['resultHTML'] = $table->generate($result);;
                        $data['reportTitle'] = 'MDO-wise user count ';
                        
                    } else if ($mdoReportType == 'mdoUserEnrolment') {
                        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation','Course', 'Status', 'Completion Percentage', 'Completed On');
    
                        $result = $enrolment->getEnrolmentByOrg($orgName);
                        
                        $session->setTempdata('resultArray',$result->getResultArray(),300);
                        $session->setTempdata('fileName', $orgName . '_UserEnrolmentReport',300);
                        
                        $data['resultHTML'] = $table->generate($result);;
                        $data['reportTitle'] = 'Users Enrolment Report for organisation - "' . $orgName . '"';
                        
                    } else if ($mdoReportType == 'ministryUserEnrolment') {
                        $table->setHeading('Name', 'Email', 'Ministry', 'Department', 'Organization', 'Designation', 'Contact No.' ,'Created Date','Roles');
    
                        $result = $user->getUserByMinistry($ministryName);
                        
                        $session->setTempdata('resultArray',$result->getResultArray(),300);
                        $session->setTempdata('fileName',$ministryName . '_UserList',300);
                        
                        $data['resultHTML'] = $table->generate($result);
                        $data['reportTitle'] = 'Users list for all organisations under ministry/state - "' . $ministryName . '"';

                    } else if ($mdoReportType == 'userWiseCount') {

                        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation','Course', 'Status', 'Completion Percentage', 'Completed On');
    
                        $result = $enrolment->getUserEnrolmentCountByMDO($orgName);
                        
                        $session->setTempdata('resultArray',$result->getResultArray(),300);
                        $session->setTempdata('fileName',$orgName . '_UserList',300);
                        
                        $data['resultHTML'] = $table->generate($result);;
                        $data['reportTitle'] = 'User-wise course enrolment/completion count for organisation - "' . $orgName . '"';

                    }

                return view('header_view')
                    . view('report_result', $data)
                    . view('footer_view');
            }
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function getCourseReport()
    {
        try {


            $request = service('request');
            $session = \Config\Services::session();
            $table = new \CodeIgniter\View\Table();
            $table->setTemplate($GLOBALS['tableTemplate']);
            
            $role = $session->get('role');
            $courseReportType = $request->getPost('courseReportType');
            $course = $request->getPost('course');

            $home = new Home();
            $enrolment = new UserEnrolmentCourse();
            $enrolmentProgram = new UserEnrolmentProgram();
            $lastUpdate = new DataUpdateModel();

            if ($courseReportType == 'notSelected') {
                echo '<script>alert("Please select report type!");</script>';
                return view('header_view')
                    . view('footer_view');
            } else {

                $data['lastUpdated'] = '[Report last updated on ' . $lastUpdate->getReportLastUpdatedTime() . ']';

                $org = '';
                if ($role == 'MDO_ADMIN') {
                    $org = $session->get('organisation');
                }
                if ($courseReportType == 'courseEnrolmentReport') {
                    $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Course Name','Completion Status', 'Completion Percentage', 'Completed On');

                    $result = $enrolment->getCourseWiseEnrolmentReport($course, $org);
                    
                    $session->setTempdata('resultArray',$result->getResultArray(),300);
                    $session->setTempdata('fileName',$course . '_EnrolmentReport',300);
                    
                    $data['resultHTML'] = $table->generate($result);;
                    $data['reportTitle'] ='User Enrolment Report for Course - "' . $home->getCourseName($course) . '"';


                } else if ($courseReportType == 'courseEnrolmentCount') {
                    $table->setHeading('Course Name', 'Published Date', 'Duration (HH:MM:SS)', 'Enrollment Count', 'Completion Count', 'Average Rating');

                    $result = $enrolment->getCourseWiseEnrolmentCount($org);
                    
                    $session->setTempdata('resultArray',$result->getResultArray(),300);
                    $session->setTempdata('fileName', 'Course-wiseSummary',300);
                    
                    $data['resultHTML'] = $table->generate($result);;
                    $data['reportTitle'] ='Course-wise  Summary';

                } else if ($courseReportType == 'programEnrolmentReport') {
                    $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Status', 'Completed On');

                    $result = $enrolmentProgram->getProgramWiseEnrolmentReport($course, $org);
                    
                    $session->setTempdata('resultArray',$result->getResultArray(),300);
                    $session->setTempdata('fileName', $course . '_EnrolmentReport',300);
                    
                    $data['resultHTML'] = $table->generate($result);;
                    $data['reportTitle'] ='User Enrolment Report for Program - "' . $home->getProgramName($course) . '"';

                } else if ($courseReportType == 'programEnrolmentCount') {
                    $table->setHeading('Program Name', 'Batch ID',  'Enrollment Count', 'Completion Count');

                    $result = $enrolmentProgram->getProgramWiseEnrolmentCount($org);
                    
                    $session->setTempdata('resultArray',$result->getResultArray(),300);
                    $session->setTempdata('fileName', 'Program-wiseSummary',300);
                    
                    $data['resultHTML'] = $table->generate($result);;
                    $data['reportTitle'] ='Program-wise Summary';

                } else if ($courseReportType == 'collectionEnrolmentReport') {
                    $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation','Course Name', 'Status', 'Completion Percentage', 'Completed On');

                    $result = $enrolment->getCollectionWiseEnrolmentReport($course, $org);
                    
                    $session->setTempdata('resultArray',$result->getResultArray(),300);
                    $session->setTempdata('fileName',$course . '_EnrolmentReport',300);
                    
                    $data['resultHTML'] = $table->generate($result);;
                    $data['reportTitle'] ='User Enrolment Report for Curated Collection - "' . $home->getCollectionName($course) . '"';

                } else if ($courseReportType == 'collectionEnrolmentCount') {
                    $table->setHeading('Course Name',  'Enrolment Count', 'Completion Count');

                    $result =$enrolment->getCollectionWiseEnrolmentCount($course, $org);
                    
                    $session->setTempdata('resultArray',$result->getResultArray(),300);
                    $session->setTempdata('fileName',$course . '_Summary',300);
                    
                    $data['resultHTML'] = $table->generate($result);;
                    $data['reportTitle'] ='Curated collection summary - "' . $home->getCollectionName($course) . '"';


                } else if ($courseReportType == 'courseMinistrySummary') {
                    $table->setHeading( 'Ministry Name',  'Enrollment Count', 'Completion Count');

                    $result = $enrolment->getCourseMinistrySummary($course);
                    
                    $session->setTempdata('resultArray',$result->getResultArray(),300);
                    $session->setTempdata('fileName',$course . '_MinistrySummary',300);
                    
                    $data['resultHTML'] = $table->generate($result);;
                    $data['reportTitle'] ='Ministry-wise Summary for course - "' . $home->getCourseName($course) . '"';

                }
                return view('header_view')
                    . view('report_result', $data)
                    . view('footer_view');
            }
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function getRoleReport()
    {
        try {

            $request = service('request');
            $session = \Config\Services::session();

            $table = new \CodeIgniter\View\Table();
            $table->setTemplate($GLOBALS['tableTemplate']);
            
            $roleReportType = $request->getPost('roleReportType');
            $home = new Home();
            $user = new MasterUserModel();
            $lastUpdate = new DataUpdateModel();

            if ($roleReportType == 'notSelected') {
                echo '<script>alert("Please select report type!");</script>';
                return view('header_view')
                    . view('footer_view');
            } else {

                $data['lastUpdated'] = '[Report last updated on ' . $lastUpdate->getReportLastUpdatedTime() . ']';

                $role = $session->get('role');
                if ($role == 'SPV_ADMIN') {
                    $ministry = '';
                    $dept = '';
                    $org = '';
                    $orgName = '';
                } else if ($role == 'MDO_ADMIN') {
                    $ministry = $session->get('ministry');
                    $dept = $session->get('department');
                    $org = $session->get('organisation');
                }
                if ($org != "") {
                    $orgName = $home->getOrgName($org);

                }

                if ($roleReportType == 'roleWiseCount') {
                    $table->setHeading( 'Role',  'Count');

                    $result = $user->getRoleWiseCount($orgName);
                    
                    $session->setTempdata('resultArray',$result->getResultArray(),300);
                    $session->setTempdata('fileName','Role-wise count',300);
                    
                    $data['resultHTML'] = $table->generate($result);;
                    $data['reportTitle'] ='Role-wise count';

                } else if ($roleReportType == 'monthWiseMDOAdminCount') {
                    $table->setHeading( 'Month','Year',  'Count');

                    $result = $user->getMonthWiseMDOAdminCount($orgName);
                    
                    $session->setTempdata('resultArray',$result->getResultArray(),300);
                    $session->setTempdata('fileName','Month-wise MDO Admin Creation Count',300);
                    
                    $data['resultHTML'] = $table->generate($result);;
                    $data['reportTitle'] ='Month-wise MDO Admin Creation Count';

                } else if ($roleReportType == 'cbpAdminList') {
                    $table->setHeading( 'Name', 'Email', 'Organization', 'Designation', 'Contact No.' ,'Creation Date','Roles');

                    $result = $user->getCBPAdminList($orgName);
                    
                    $session->setTempdata('resultArray',$result->getResultArray(),300);
                    $session->setTempdata('fileName','List of CBP Admins',300);
                    
                    $data['resultHTML'] = $table->generate($result);;
                    $data['reportTitle'] ='List of CBP Admins';

                } else if ($roleReportType == 'mdoAdminList') {
                    $table->setHeading( 'Name', 'Email', 'Organization', 'Designation', 'Contact No.' ,'Creation Date','Roles');

                    $result = $user->getMDOAdminList($orgName);
                    
                    $session->setTempdata('resultArray',$result->getResultArray(),300);
                    $session->setTempdata('fileName','List of MDO Admins',300);
                    
                    $data['resultHTML'] = $table->generate($result);;
                    $data['reportTitle'] ='List of MDO Admins';

                } else if ($roleReportType == 'creatorList') {
                    $table->setHeading( 'Name', 'Email', 'Organization', 'Designation', 'Contact No.' ,'Creation Date','Roles');

                    $result = $user->getCreatorList($orgName);
                    
                    $session->setTempdata('resultArray',$result->getResultArray(),300);
                    $session->setTempdata('fileName','List of Content Creators',300);
                    
                    $data['resultHTML'] = $table->generate($result);;
                    $data['reportTitle'] ='List of Content Creators';

                } else if ($roleReportType == 'reviewerList') {
                    $table->setHeading( 'Name', 'Email', 'Organization', 'Designation', 'Contact No.' ,'Creation Date','Roles');

                    $result = $user->getReviewerList($orgName);
                    
                    $session->setTempdata('resultArray',$result->getResultArray(),300);
                    $session->setTempdata('fileName','List of Content Reviewers',300);
                    
                    $data['resultHTML'] = $table->generate($result);;
                    $data['reportTitle'] ='List of Content Reviewers';

                } else if ($roleReportType == 'publisherList') {
                    $table->setHeading( 'Name', 'Email', 'Organization', 'Designation', 'Contact No.' ,'Creation Date','Roles');

                    $result = $user->getPublisherList($orgName);
                    
                    $session->setTempdata('resultArray',$result->getResultArray(),300);
                    $session->setTempdata('fileName','List of Content Publishers',300);
                    
                    $data['resultHTML'] = $table->generate($result);;
                    $data['reportTitle'] ='List of Content Publishers';

                } else if ($roleReportType == 'editorList') {
                    $table->setHeading( 'Name', 'Email', 'Organization', 'Designation', 'Contact No.' ,'Creation Date','Roles');

                    $result = $user->getEditorList($orgName);
                    
                    $session->setTempdata('resultArray',$result->getResultArray(),300);
                    $session->setTempdata('fileName','List of Editors',300);
                    
                    $data['resultHTML'] = $table->generate($result);;
                    $data['reportTitle'] ='List of Editors';

                } else if ($roleReportType == 'fracAdminList') {
                    $table->setHeading( 'Name', 'Email', 'Organization', 'Designation', 'Contact No.' ,'Creation Date','Roles');

                    $result = $user->getFracAdminList($orgName);
                    
                    $session->setTempdata('resultArray',$result->getResultArray(),300);
                    $session->setTempdata('fileName','List of FRAC Admins',300);
                    
                    $data['resultHTML'] = $table->generate($result);;
                    $data['reportTitle'] ='List of FRAC Admins';


                } else if ($roleReportType == 'fracCompetencyMember') {
                    $table->setHeading( 'Name', 'Email', 'Organization', 'Designation', 'Contact No.' ,'Creation Date','Roles');

                    $result = $user->getFracCompetencyMemberList($orgName);
                    
                    $session->setTempdata('resultArray',$result->getResultArray(),300);
                    $session->setTempdata('fileName','List of FRAC Competency Members',300);
                    
                    $data['resultHTML'] = $table->generate($result);;
                    $data['reportTitle'] ='List of FRAC Competency Members';


                } else if ($roleReportType == 'fracL1List') {
                    $table->setHeading( 'Name', 'Email', 'Organization', 'Designation', 'Contact No.' ,'Creation Date','Roles');

                    $result = $user->getFRACL1List($orgName);
                    
                    $session->setTempdata('resultArray',$result->getResultArray(),300);
                    $session->setTempdata('fileName','List of FRAC_Reviewer_L1',300);
                    
                    $data['resultHTML'] = $table->generate($result);;
                    $data['reportTitle'] ='List of FRAC_Reviewer_L1';

                } else if ($roleReportType == 'fracL2List') {
                    $table->setHeading( 'Name', 'Email', 'Organization', 'Designation', 'Contact No.' ,'Creation Date','Roles');

                    $result = $user->getFRACL2List($orgName);
                    
                    $session->setTempdata('resultArray',$result->getResultArray(),300);
                    $session->setTempdata('fileName','List of FRAC_Reviewer_L2',300);
                    
                    $data['resultHTML'] = $table->generate($result);
                    $data['reportTitle'] ='List of FRAC_Reviewer_L2';


                } else if ($roleReportType == 'ifuMemberList') {
                    $table->setHeading( 'Name', 'Email', 'Organization', 'Designation', 'Contact No.' ,'Creation Date','Roles');

                    $result = $user->getIFUMemberList($orgName);
                    
                    $session->setTempdata('resultArray',$result->getResultArray(),300);
                    $session->setTempdata('fileName','List of IFU Members',300);
                    
                    $data['resultHTML'] = $table->generate($result);;
                    $data['reportTitle'] ='List of IFU Members';

                } else if ($roleReportType == 'publicList') {
                    $table->setHeading( 'Name', 'Email', 'Organization', 'Designation', 'Contact No.' ,'Creation Date','Roles');

                    $result = $user->getPublicList($orgName);
                    
                    $session->setTempdata('resultArray',$result->getResultArray(),300);
                    $session->setTempdata('fileName','List of CBP Admins',300);
                    
                    $data['resultHTML'] = $table->generate($result);;
                    $data['reportTitle'] ='List of Public users';

                } else if ($roleReportType == 'spvAdminList') {
                    $table->setHeading( 'Name', 'Email', 'Organization', 'Designation', 'Contact No.' ,'Creation Date','Roles');

                    $result = $user->getSPVAdminList($orgName);
                    
                    $session->setTempdata('resultArray',$result->getResultArray(),300);
                    $session->setTempdata('fileName','List of SPV Admins',300);
                    
                    $data['resultHTML'] = $table->generate($result);;
                    $data['reportTitle'] ='List of SPV Admins';

                } else if ($roleReportType == 'stateAdminList') {
                    $table->setHeading( 'Name', 'Email', 'Organization', 'Designation', 'Contact No.' ,'Creation Date','Roles');

                    $result = $user->getStateAdminList($orgName);
                    
                    $session->setTempdata('resultArray',$result->getResultArray(),300);
                    $session->setTempdata('fileName','List of State Admins',300);
                    
                    $data['resultHTML'] = $table->generate($result);;
                    $data['reportTitle'] ='List of State Admins';


                } else if ($roleReportType == 'watMemberList') {
                    $table->setHeading( 'Name', 'Email', 'Organization', 'Designation', 'Contact No.' ,'Creation Date','Roles');

                    $result = $user->getWATMemberList($orgName);
                    
                    $session->setTempdata('resultArray',$result->getResultArray(),300);
                    $session->setTempdata('fileName','List of WAT Members',300);
                    
                    $data['resultHTML'] = $table->generate($result);;
                    $data['reportTitle'] ='List of WAT Members';

                }
                return view('header_view')
                    . view('report_result', $data)
                    . view('footer_view');
            }
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function getAnalytics()
    {
        try {


            $request = service('request');
            $session = \Config\Services::session();

            $table = new \CodeIgniter\View\Table();
            $table->setTemplate($GLOBALS['tableTemplate']);
            
            $analyticsReportType = $request->getPost('analyticsReportType');
            $role = $session->get('role');

            $home = new Home();
            $user = new MasterUserModel();
            $course = new MasterCourseModel();
            $lastUpdate = new DataUpdateModel();
            if ($analyticsReportType == 'notSelected') {
                echo '<script>alert("Please select report type!");</script>';
                return view('header_view')
                    . view('footer_view');
            } else {

                $data['lastUpdated'] = '[Report last updated on ' . $lastUpdate->getReportLastUpdatedTime() . ']';

                if ($role == 'SPV_ADMIN') {
                    $ministry = '';
                    $dept = '';
                    $org = '';
                    $orgName = '';
                } else if ($role == 'MDO_ADMIN') {
                    $ministry = $session->get('ministry');
                    $dept = $session->get('department');
                    $org = $session->get('organisation');
                }
                if ($org != "") {
                    $orgName = $home->getOrgName($org);

                }

                if ($analyticsReportType == 'dayWiseUserOnboarding') {
                    $table->setHeading( 'Creation Date', 'Count');

                    $result = $user->getDayWiseUserOnboarding();
                    
                    $session->setTempdata('resultArray',$result->getResultArray(),300);
                    $session->setTempdata('fileName','Day-wise User Onboarding',300);
                    
                    $data['resultHTML'] = $table->generate($result);;
                    $data['reportTitle'] ='Day-wise User Onboarding';

                } else if ($analyticsReportType == 'monthWiseUserOnboarding') {
                    $table->setHeading( 'Creation Month', 'Count');

                    $result = $user->getMonthWiseUserOnboarding();
                    
                    $session->setTempdata('resultArray',$result->getResultArray(),300);
                    $session->setTempdata('fileName','Month-wise User Onboarding',300);
                    
                    $data['resultHTML'] = $table->generate($result);;
                    $data['reportTitle'] ='Month-wise User Onboarding';

                } else if ($analyticsReportType == 'monthWiseCourses') {
                    $table->setHeading( 'Month of Publishing', 'Count');

                    $result = $course->getMonthWiseCourses();
                    
                    $session->setTempdata('resultArray',$result->getResultArray(),300);
                    $session->setTempdata('fileName','Month-wise Courses Published',300);
                    
                    $data['resultHTML'] = $table->generate($result);;
                    $data['reportTitle'] ='Month-wise Courses Published';

                }


                return view('header_view')
                    . view('report_result', $data)
                    . view('footer_view');
            }
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function getDoptReport()
    {
        try {


            $request = service('request');
            $session = \Config\Services::session();

            $table = new \CodeIgniter\View\Table();
            $table->setTemplate($GLOBALS['tableTemplate']);
            
            $role = $session->get('role');
            $user = new UserEnrolmentProgram();
            $lastUpdate = new DataUpdateModel();


            $data['lastUpdated'] = '[Report last updated on ' . $lastUpdate->getReportLastUpdatedTime() . ']';

            $org = '';
            if ($role == 'ATI_ADMIN') {
                $org = $session->get('organisation');
            }
            $doptReportType = $request->getPost('doptReportType');
            if ($doptReportType == 'notSelected') {
                echo '<script>alert("Please select report type!");</script>';
                return view('header_view')
                    . view('footer_view');
            } else {

                $ati = $request->getPost('ati');
                if ($doptReportType == 'atiWiseOverview') {
                    $table->setHeading( 'Program Name','Institute', 'Enrolled',  'Not Started','In Progress', 'Completed');

                    $result = $user->getATIWiseCount();
                    
                    $session->setTempdata('resultArray',$result->getResultArray(),300);
                    $session->setTempdata('fileName','ATI-wise Overview',300);
                    
                    $data['resultHTML'] = $table->generate($result);;
                    $data['reportTitle'] ='ATI-wise Overview';
                }

                return view('header_view')
                    . view('report_result', $data)
                    . view('footer_view');
            }
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function downloadExcel()
    {
        try {

            $session = \Config\Services::session();
            
            helper('array');

            $report = $session->getTempdata('resultArray');
            $fileName = $session->getTempdata('fileName'). '.xls';
            
            
            $keys = array();
            

            foreach ($report[0] as $key => $value) {
                array_push($keys, $key);
            }

            $spreadsheet = new Spreadsheet();


            $sheet = $spreadsheet->getActiveSheet();
            $column = 'A';
            foreach ($keys as $key) {
                $sheet->setCellValue($column . '1', $key);
                $column++;
            }


            $rows = 2;



            foreach ($report as $row) {
                $column = 'A';
                foreach ($row as $key => $val) {
                    $sheet->setCellValue($column . $rows, $val);
                    $column++;

                }


                $rows++;
            }

            ob_end_clean();
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=" . $fileName);
            header("Cache-Control: max-age=0");
            // ob_end_clean();

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            $session->removeTempdata('resultArray');
            $session->removeTempdata('fileName');
            die;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }


    }

    

}