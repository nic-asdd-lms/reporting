<?php

namespace App\Controllers;

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
use PHPExcel_IOFactory;
use PHPExcel_Reader_HTML;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Home extends BaseController
{
    public function index()
    {
        helper(['form', 'url']);
        $masterStructureModel = new MasterStructureModel();
        $masterOrganizationModel = new MasterOrganizationModel();
        $masterCourseModel = new MasterCourseModel();
        $data['mdoReportTypes'] = $this->getMDOReportTypes();
        $data['ministry'] = $masterStructureModel->getMinistry();
        $data['org'] = $masterOrganizationModel->getOrganizations();
        $data['course'] = $masterCourseModel->getCourse();
        $data['error'] = '';
        return view('header_view')
            . view('report_home', $data)
            . view('footer_view');


    }

    public function action()
    {
        if ($this->request->getVar('action')) {
            $action = $this->request->getVar('action');

            if ($action == 'get_dept') {
                $deptModel = new MasterStructureModel();

                $deptdata = $deptModel->getDepartment($this->request->getVar('ministry'));

                echo json_encode($deptdata);
            } else if ($action == 'get_org') {
                $orgModel = new MasterStructureModel();
                $orgdata = $orgModel->getOrganisation($this->request->getVar('dept'));

                echo json_encode($orgdata);
            } else if ($action == 'get_course') {
                $courseModel = new MasterCourseModel();
                $coursedata = $courseModel->getCourse();

                echo json_encode($coursedata);
            } else if ($action == 'get_program') {
                $programModel = new MasterProgramModel();
                $programData = $programModel->getProgram();

                echo json_encode($programData);
            } else if ($action == 'get_collection') {
                $collectionModel = new MasterCollectionModel();
                $collectionData = $collectionModel->getCollection();

                echo json_encode($collectionData);
            } else if ($action == 'search') {
                //Search box value assigning to $Name variable.
                $search_key = $_GET['term'];
                echo $search_key;


                //Search query.
                $orgModel = new MasterOrganizationModel();
                $orgdata = $orgModel->searchOrg($search_key);

                echo json_encode($orgdata);
                //Query execution

                //Creating unordered list to display result.



            }
        }
    }

    public function getCourseReport()
    {
        $request = service('request');
        $session = \Config\Services::session();

        $role = $session->get('role');
        $enrolment = new UserEnrolmentCourse();
        $enrolmentProgram = new UserEnrolmentProgram();
        $lastUpdate='';
        $data['lastUpdated'] = '[Report last updated on ' . $lastUpdate . '"]';
        
        $org = '';
        if ($role == 'MDO_ADMIN') {
            $org = $session->get('organisation');
        }
        $courseReportType = $request->getPost('courseReportType');
        $course = $request->getPost('course');
        if ($courseReportType == 'courseEnrolmentReport') {
            if ($role == 'MDO_ADMIN') {
                $data['params'] = 'reportType=courseEnrolmentReport&org=' . $org.'&course='.$course;
            }
            else if($role == 'SPV_ADMIN'){
                $data['params'] = 'reportType=courseEnrolmentReport&course='.$course;
            }
            $data['resultHTML'] = $enrolment->getCourseWiseEnrolmentReport($course, $org);
            $data['reportTitle'] = 'User Enrolment Report for Course - "' . $this->getCourseName($course) . '"';
            $data['fileName'] = $course . '_EnrolmentReport';

        } else if ($courseReportType == 'courseEnrolmentCount') {
            if ($role == 'MDO_ADMIN') {
                $data['params'] = 'reportType=courseEnrolmentCount&org=' . $org;
            }
            else if($role == 'SPV_ADMIN'){
                $data['params'] = 'reportType=courseEnrolmentCount';
            }
            $data['resultHTML'] = $enrolment->getCourseWiseEnrolmentCount($org);
            $data['reportTitle'] = 'Course-wise Enrolment/Completion Count';
            $data['fileName'] = $course . '_EnrolmentCompletionCount';

        } else if ($courseReportType == 'programEnrolmentReport') {
            if ($role == 'MDO_ADMIN') {
                $data['params'] = 'reportType=programEnrolmentReport&org=' . $org.'&course='.$course;
            }
            else if($role == 'SPV_ADMIN'){
                $data['params'] = 'reportType=programEnrolmentReport&course='.$course;
            }
            $data['resultHTML'] = $enrolmentProgram->getProgramWiseEnrolmentReport($course, $org);
            $data['reportTitle'] = 'User Enrolment Report for Program - "' . $this->getProgramName($course) . '"';
            $data['fileName'] = $course . '_EnrolmentReport';

        } else if ($courseReportType == 'programEnrolmentCount') {
            if ($role == 'MDO_ADMIN') {
                $data['params'] = 'reportType=programEnrolmentCount&org=' . $org;
            }
            else if($role == 'SPV_ADMIN'){
                $data['params'] = 'reportType=programEnrolmentCount';
            }
            $data['resultHTML'] = $enrolmentProgram->getProgramWiseEnrolmentCount($org);
            $data['reportTitle'] = 'Program-wise Enrolment/Completion Count';
            $data['fileName'] = $course . '_EnrolmentCompletionCount';

        } else if ($courseReportType == 'collectionEnrolmentReport') {
            if ($role == 'MDO_ADMIN') {
                $data['params'] = 'reportType=collectionEnrolmentReport&org=' . $org.'&course='.$course;
            }
            else if($role == 'SPV_ADMIN'){
                $data['params'] = 'reportType=collectionEnrolmentReport&course='.$course;
            }
            $data['resultHTML'] = $enrolment->getCollectionWiseEnrolmentReport($course, $org);
            $data['reportTitle'] = 'User Enrolment Report for Curated Collection - "' . $this->getCollectionName($course) . '"';
            $data['fileName'] = $course . '_EnrolmentReport';

        } else if ($courseReportType == 'collectionEnrolmentCount') {
            if ($role == 'MDO_ADMIN') {
                $data['params'] = 'reportType=collectionEnrolmentCount&org=' . $org.'&course='.$course;
            }
            else if($role == 'SPV_ADMIN'){
                $data['params'] = 'reportType=collectionEnrolmentCount&course='.$course;
            }
            $data['resultHTML'] = $enrolment->getCollectionWiseEnrolmentCount($course, $org);
            $data['reportTitle'] = 'Enrolment/Completion Count for Curated Collection - "' . $this->getCollectionName($course) . '"';
            $data['fileName'] = $course . '_EnrolmentCompletionCount';

        } else if ($courseReportType == 'courseMinistrySummary') {
            $data['params'] = 'reportType=courseMinistrySummary&course='.$course;
            $data['resultHTML'] = $enrolment->getCourseMinistrySummary($course);
            $data['reportTitle'] = 'Ministry-wise Summary for course - "' . $this->getCourseName($course) . '"';
            $data['fileName'] = $course . '_MinistrySummary';

        }
        return view('header_view')
            . view('report_result', $data)
            . view('footer_view');

    }

    public function getMDOReport()
    {
                
        $request = service('request');
        $session = \Config\Services::session();

        $mdoReportType = $request->getPost('mdoReportType');

        $role = $session->get('role');

        $user = new MasterUserModel();
        $enrolment = new UserEnrolmentCourse();
        $lastUpdate='';
        $data['lastUpdated'] = '[Report last updated on ' . $lastUpdate . '"]';
        
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
            $ministryName = $this->getOrgName($ministry);
        }
        if ($dept != "notSelected") {
            $deptName = $this->getOrgName($dept);

        }

        if ($org != "notSelected") {
            $orgName = $this->getOrgName($org);

        } else if ($dept != "notSelected") {
            $org = $dept;
            $orgName = $this->getOrgName($dept);
        } else if ($ministry != "notSelected") {
            $org = $ministry;
            $orgName = $this->getOrgName($ministry);
        }

        if ($mdoReportType == 'mdoUserList') {

            // if ($ministry == "notSelected"){
            //     $data['error']= 'Please Select Ministry';
            //     $data['resultHTML'] ='';
            //     $data['reportTitle']='';
            //     $data['fileName']='';
            // }
            // else 
            {
                // $data['error']='';
                // if($ministry != "notSelected" && $dept == "notSelected") {
                //     $orgName=$ministryName;

                // }
                // else if($ministry != "notSelected" && $dept == "notSelected" && $org == "notSelected") {
                //     $orgName=$deptName;

                // }



                $data['params'] = 'reportType=mdoUserList&org=' . $org;
                $data['resultHTML'] = $user->getUserByOrg($orgName);
                $data['reportTitle'] = 'Users onboarded from organisation - "' . $orgName . '"';
                $data['fileName'] = $orgName . '_UserList';
            }
        } else
            if ($mdoReportType == 'mdoUserCount') {
                $data['params'] = 'reportType=mdoUserCount';
                $data['resultHTML'] = $user->getUserCountByOrg();
                $data['reportTitle'] = 'MDO-wise user count ';
                $data['fileName'] = 'MDOWiseUserCount';
            } else if ($mdoReportType == 'mdoUserEnrolment') {
                $data['params'] = 'reportType=mdoUserEnrolment&org=' . $org;
                $data['resultHTML'] = $enrolment->getEnrolmentByOrg($orgName);
                $data['reportTitle'] = 'Users Enrolment Report for organisation - "' . $orgName . '"';
                $data['fileName'] = $orgName . '_UserEnrolmentReport';

            } else if ($mdoReportType == 'ministryUserEnrolment') {
                $data['params'] = 'reportType=ministryUserEnrolment&org=' . $org;
                $data['resultHTML'] = $user->getUserByMinistry($ministryName);
                $data['reportTitle'] = 'Users list for all organisations under ministry - "' . $ministryName . '"';
                $data['fileName'] = $orgName . '_UserList';

            } else if ($mdoReportType == 'userWiseCount') {
                $data['params'] = 'reportType=userWiseCount&org=' . $org;
                $data['resultHTML'] = $enrolment->getUserEnrolmentCountByMDO($orgName);
                $data['reportTitle'] = 'User-wise course enrolment/completion count for organisation - "' . $orgName . '"';
                $data['fileName'] = $orgName . '_UserList';

            }

        return view('header_view')
            . view('report_result', $data)
            . view('footer_view');
    }

    public function getRoleReport()
    {
        $request = service('request');
        $session = \Config\Services::session();

        $roleReportType = $request->getPost('roleReportType');
        $user = new MasterUserModel();
        $lastUpdate='';
        $data['lastUpdated'] = '[Report last updated on ' . $lastUpdate . '"]';
        
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
            $orgName = $this->getOrgName($org);

        }

        if ($roleReportType == 'roleWiseCount') {
            if ($role == 'SPV_ADMIN') {
                $data['params'] = 'reportType=roleWiseCount';
            } elseif ($role == 'MDO_ADMIN') {
                $data['params'] = 'reportType=roleWiseCount&org=' . $org;
            }
            $data['resultHTML'] = $user->getRoleWiseCount($orgName);
            $data['reportTitle'] = 'Role-wise count';
            $data['fileName'] = 'RoleWiseCount';

        } else if ($roleReportType == 'monthWiseMDOAdminCount') {
            if ($role == 'SPV_ADMIN') {
                $data['params'] = 'reportType=monthWiseMDOAdminCount';
            } elseif ($role == 'MDO_ADMIN') {
                $data['params'] = 'reportType=monthWiseMDOAdminCount&org=' . $org;
            }
            $data['resultHTML'] = $user->getMonthWiseMDOAdminCount($orgName);
            $data['reportTitle'] = 'Month-wise MDO Admin Creation Count';
            $data['fileName'] = 'RoleWiseCount';

        } else if ($roleReportType == 'cbpAdminList') {
            if ($role == 'SPV_ADMIN') {
                $data['params'] = 'reportType=cbpAdminList';
            } elseif ($role == 'MDO_ADMIN') {
                $data['params'] = 'reportType=cbpAdminList&org=' . $org;
            }
            $data['resultHTML'] = $user->getCBPAdminList($orgName);
            $data['reportTitle'] = 'MDO-wise user count ';
            $data['fileName'] = 'MDOWiseUserCount';

        } else if ($roleReportType == 'mdoAdminList') {
            if ($role == 'SPV_ADMIN') {
                $data['params'] = 'reportType=mdoAdminList';
            } elseif ($role == 'MDO_ADMIN') {
                $data['params'] = 'reportType=mdoAdminList&org=' . $org;
            }

            $data['resultHTML'] = $user->getMDOAdminList($orgName);
            $data['reportTitle'] = 'MDO Admin List ';
            $data['fileName'] = 'MDOAdminList';

        } else if ($roleReportType == 'creatorList') {
            if ($role == 'SPV_ADMIN') {
                $data['params'] = 'reportType=creatorList';
            } elseif ($role == 'MDO_ADMIN') {
                $data['params'] = 'reportType=creatorList&org=' . $org;
            }
            $data['resultHTML'] = $user->getCreatorList($orgName);
            $data['reportTitle'] = 'List of Content Creators';
            $data['fileName'] = '_UserEnrolmentReport';
        } else if ($roleReportType == 'reviewerList') {
            if ($role == 'SPV_ADMIN') {
                $data['params'] = 'reportType=reviewerList';
            } elseif ($role == 'MDO_ADMIN') {
                $data['params'] = 'reportType=reviewerList&org=' . $org;
            }
            $data['resultHTML'] = $user->getReviewerList($orgName);
            $data['reportTitle'] = 'List of Content Reviewers';
            $data['fileName'] = '_UserList';

        } else if ($roleReportType == 'publisherList') {
            if ($role == 'SPV_ADMIN') {
                $data['params'] = 'reportType=publisherList';
            } elseif ($role == 'MDO_ADMIN') {
                $data['params'] = 'reportType=publisherList&org=' . $org;
            }
            $data['resultHTML'] = $user->getPublisherList($orgName);
            $data['reportTitle'] = 'List of Content Publishers';
            $data['fileName'] = '_UserList';

        } else if ($roleReportType == 'editorList') {
            if ($role == 'SPV_ADMIN') {
                $data['params'] = 'reportType=editorList';
            } elseif ($role == 'MDO_ADMIN') {
                $data['params'] = 'reportType=editorList&org=' . $org;
            }
            $data['resultHTML'] = $user->getEditorList($orgName);
            $data['reportTitle'] = 'List of Editors';
            $data['fileName'] = '_UserList';

        } else if ($roleReportType == 'fracAdminList') {
            if ($role == 'SPV_ADMIN') {
                $data['params'] = 'reportType=fracAdminList';
            } elseif ($role == 'MDO_ADMIN') {
                $data['params'] = 'reportType=fracAdminList&org=' . $org;
            }
            $data['resultHTML'] = $user->getFracAdminList($orgName);
            $data['reportTitle'] = 'List of FRAC Admins';
            $data['fileName'] = '_UserList';

        } else if ($roleReportType == 'fracCompetencyMember') {
            if ($role == 'SPV_ADMIN') {
                $data['params'] = 'reportType=fracCompetencyMember';
            } elseif ($role == 'MDO_ADMIN') {
                $data['params'] = 'reportType=fracCompetencyMember&org=' . $org;
            }
            $data['resultHTML'] = $user->getFracCompetencyMemberList($orgName);
            $data['reportTitle'] = 'List of FRAC Competency Members';
            $data['fileName'] = '_UserList';

        } else if ($roleReportType == 'fracL1List') {
            if ($role == 'SPV_ADMIN') {
                $data['params'] = 'reportType=fracL1List';
            } elseif ($role == 'MDO_ADMIN') {
                $data['params'] = 'reportType=fracL1List&org=' . $org;
            }
            $data['resultHTML'] = $user->getFRACL1List($orgName);
            $data['reportTitle'] = 'MDO Admin List ';
            $data['fileName'] = 'MDOAdminList';

        } else if ($roleReportType == 'fracL2List') {
            if ($role == 'SPV_ADMIN') {
                $data['params'] = 'reportType=fracL2List';
            } elseif ($role == 'MDO_ADMIN') {
                $data['params'] = 'reportType=fracL2List&org=' . $org;
            }
            $data['resultHTML'] = $user->getFRACL2List($orgName);
            $data['reportTitle'] = 'List of Content Creators';
            $data['fileName'] = '_UserEnrolmentReport';

        } else if ($roleReportType == 'ifuMemberList') {
            if ($role == 'SPV_ADMIN') {
                $data['params'] = 'reportType=ifuMemberList';
            } elseif ($role == 'MDO_ADMIN') {
                $data['params'] = 'reportType=ifuMemberList&org=' . $org;
            }
            $data['resultHTML'] = $user->getIFUMemberList($orgName);
            $data['reportTitle'] = 'List of Content Reviewers';
            $data['fileName'] = '_UserList';

        } else if ($roleReportType == 'publicList') {
            if ($role == 'SPV_ADMIN') {
                $data['params'] = 'reportType=publicList';
            } elseif ($role == 'MDO_ADMIN') {
                $data['params'] = 'reportType=publicList&org=' . $org;
            }
            $data['resultHTML'] = $user->getPublicList($orgName);
            $data['reportTitle'] = 'List of Content Publishers';
            $data['fileName'] = '_UserList';

        } else if ($roleReportType == 'spvAdminList') {
            if ($role == 'SPV_ADMIN') {
                $data['params'] = 'reportType=spvAdminList';
            } elseif ($role == 'MDO_ADMIN') {
                $data['params'] = 'reportType=spvAdminList&org=' . $org;
            }
            $data['resultHTML'] = $user->getSPVAdminList($orgName);
            $data['reportTitle'] = 'List of Editors';
            $data['fileName'] = '_UserList';

        } else if ($roleReportType == 'stateAdminList') {
            if ($role == 'SPV_ADMIN') {
                $data['params'] = 'reportType=stateAdminList';
            } elseif ($role == 'MDO_ADMIN') {
                $data['params'] = 'reportType=stateAdminList&org=' . $org;
            }
            $data['resultHTML'] = $user->getStateAdminList($orgName);
            $data['reportTitle'] = 'List of FRAC Admins';
            $data['fileName'] = '_UserList';

        } else if ($roleReportType == 'watMemberList') {
            if ($role == 'SPV_ADMIN') {
                $data['params'] = 'reportType=watMemberList';
            } elseif ($role == 'MDO_ADMIN') {
                $data['params'] = 'reportType=watMemberList&org=' . $org;
            }
            $data['resultHTML'] = $user->getWATMemberList($orgName);
            $data['reportTitle'] = 'List of FRAC Competency Members';
            $data['fileName'] = '_UserList';

        }
        return view('header_view')
            . view('report_result', $data)
            . view('footer_view');
    }

    public function getAnalytics()
    {
        $request = service('request');
        $session = \Config\Services::session();

        $analyticsReportType = $request->getPost('analyticsReportType');
        $role = $session->get('role');

        $user = new MasterUserModel();
        $course = new MasterCourseModel();
        $lastUpdate='';
        $data['lastUpdated'] = '[Report last updated on ' . $lastUpdate . '"]';
        
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
            $orgName = $this->getOrgName($org);

        }

        if ($analyticsReportType == 'dayWiseUserOnboarding') {
            $data['params'] = 'reportType=dayWiseUserOnboarding';
            $data['resultHTML'] = $user->getDayWiseUserOnboarding();
            $data['reportTitle'] = 'Day-wise User Onboarding';
            $data['fileName'] = 'RoleWiseCount';

        } else if ($analyticsReportType == 'monthWiseUserOnboarding') {
            $data['params'] = 'reportType=monthWiseUserOnboarding';
            $data['resultHTML'] = $user->getMonthWiseUserOnboarding();
            $data['reportTitle'] = 'Month-wise User Onboarding';
            $data['fileName'] = 'MDOWiseUserCount';

        } else if ($analyticsReportType == 'monthWiseCourses') {
            $data['params'] = 'reportType=monthWiseCourses';
            $data['resultHTML'] = $course->getMonthWiseCourses();
            $data['reportTitle'] = 'Month-wise Courses Published';
            $data['fileName'] = 'MDOAdminList';

        }


        return view('header_view')
            . view('report_result', $data)
            . view('footer_view');
    }

    public function getDoptReport()
    {
        $request = service('request');
        $session = \Config\Services::session();

        $role = $session->get('role');
        $user = new UserEnrolmentProgram();
        $lastUpdate='';
        $data['lastUpdated'] = '[Report last updated on ' . $lastUpdate . '"]';
        
        $org = '';
        if ($role == 'ATI_ADMIN') {
            $org = $session->get('organisation');
        }
        $doptReportType = $request->getPost('doptReportType');
        $ati = $request->getPost('ati');
        if ($doptReportType == 'atiWiseOverview') {
            $data['params'] = 'reportType=atiWiseOverview';
            $data['resultHTML'] = $user->getATIWiseCount();
            $data['reportTitle'] = 'ATI-wise Overview';
            $data['fileName'] = 'ATIWiseOverview';

        }

        return view('header_view')
            . view('report_result', $data)
            . view('footer_view');

    }




    public function getExcelReport()
    {
        helper('array');

        $user = new MasterUserModel();
        $enrolmentCourse = new UserEnrolmentCourse();
        $enrolmentProgram = new UserEnrolmentProgram();
        $course = new MasterCourseModel();

        $query_params = explode("&", current_url(true)->getQuery());

        $params = array();
        $keys = array();
        $reportType = '';
        $course_id = '';
        $org_id = '';
        foreach ($query_params as $param) {
            list($k, $v) = explode('=', $param);
            $params[$k] = $v;
        }

        foreach ($params as $key => $value) {

            if ($key == 'reportType') {
                $reportType = $value;
            } else if ($key == 'course') {
                $course_id = $value;
            } else if ($key == 'org') {
                $org_id = $value;
            }

        }

        switch ($reportType) {
            case 'mdoUserList':
                $report = $user->getUserByOrgExcel($org_id);
                break;
            case 'mdoUserCount':
                $report = $user->getUserCountByOrgExcel();
                break;
            case 'mdoUserEnrolment':
                $report = $enrolmentCourse->getEnrolmentByOrgExcel($org_id);
                break;
            case 'ministryUserEnrolment':
                $report = $user->getUserByMinistryExcel($org_id);
                break;
            case 'userWiseCount':
                $report = $user->getUserEnrolmentCountByMDOExcel($org_id);
                break;
            case 'courseEnrolmentReport':
                $report = $enrolmentCourse->getCourseWiseEnrolmentReporExcelt($course_id, $org_id);
                break;
            case 'courseEnrolmentCount':
                $report = $enrolmentCourse->getCourseWiseEnrolmentCountExcel($course_id, $org_id);
                break;
            case 'programEnrolmentReport':
                $report = $enrolmentProgram->getProgramWiseEnrolmentReportExcel($course_id, $org_id);
                break;
            case 'programEnrolmentCount':
                $report = $enrolmentProgram->getProgramWiseEnrolmentCountExcel($course_id, $org_id);
                break;
            case 'collectionEnrolmentReport':
                $report = $enrolmentCourse->getCollectionWiseEnrolmentReportExcel($course_id, $org_id);
                break;
            case 'collectionEnrolmentCount':
                $report = $enrolmentCourse->getCollectionWiseEnrolmentCountExcel($course_id, $org_id);
                break;
            case 'courseMinistrySummary':
                $report = $enrolmentCourse->getCourseMinistrySummaryExcel($course_id);
                break;
            case 'roleWiseCount':
                $report = $user->getRoleWiseCountExcel($org_id);
                break;
            case 'monthWiseMDOAdminCount':
                $report = $user->getMonthWiseMDOAdminCountExcel($org_id);
                break;
            case 'mdoAdminList':
                $report = $user->getMDOAdminListExcel($org_id);
                break;
            case 'cbpAdminList':
                $report = $user->getCBPAdminListExcel($org_id);
                break;
            case 'creatorList':
                $report = $user->getCreatorListExcel($org_id);
                break;
            case 'reviewerList':
                $report = $user->getReviewerListExcel($org_id);
                break;
            case 'publisherList':
                $report = $user->getPublisherListExcel($org_id);
                break;
            case 'editorList':
                $report = $user->getEditorListExcel($org_id);
                break;
            case 'fracAdminList':
                $report = $user->getFracAdminListExcel($org_id);
                break;
            case 'fracCompetencyMember':
                $report = $user->getFracCompetencyMemberListExcel($org_id);
                break;
            case 'fracL1List':
                $report = $user->getFRACL1ListExcel($org_id);
                break;
            case 'fracL2List':
                $report = $user->getFRACL2ListExcel($org_id);
                break;
            case 'ifuMemberList':
                $report = $user->getIFUMemberListExcel($org_id);
                break;
            case 'publicList':
                $report = $user->getPublicListExcel($org_id);
                break;
            case 'spvAdminList':
                $report = $user->getSPVAdminListExcel($org_id);
                break;
            case 'stateAdminList':
                $report = $user->getStateAdminListExcel($org_id);
                break;
            case 'watMemberList':
                $report = $user->getWATMemberListExcel($org_id);
                break;
            case 'dayWiseUserOnboarding':
                $report = $user->getDayWiseUserOnboardingExcel();
                break;
            case 'monthWiseUserOnboarding':
                $report = $user->getMonthWiseUserOnboardingExcel();
                break;
            case 'monthWiseCourses':
                $report = $course->getMonthWiseCoursesExcel();
                break;
            case 'atiWiseOverview':
                $report = $user->getATIWiseCountExcel();
                break;
            
        }

        foreach ($report[0] as $key => $value) {
            array_push($keys, $key);
        }

        $fileName = $reportType .'_'. $org_id . '_' . $course_id . '.xlsx';
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
        //header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=".$fileName);
        header("Cache-Control: max-age=0");

        $writer = new Xlsx($spreadsheet);
        ob_clean();
        $writer->save('php://output');
        header("Content-Type: application/vnd.ms-excel");


    }
    
    public function getCourseName($course_id)
    {
        $course = new MasterCourseModel();

        $course_name = $course->getCourseName($course_id);
        return $course_name;


    }
    public function getCollectionName($collection_id)
    {
        $collection = new MasterCollectionModel();

        $collection_name = $collection->getCollectionName($collection_id);
        return $collection_name;


    }

    public function getProgramName($program_id)
    {
        $program = new MasterProgramModel();

        $program_name = $program->getProgramName($program_id);
        return $program_name;


    }
    public function getOrgName($org_id)
    {
        $org = new MasterOrganizationModel();

        $org_name = $org->getOrgName($org_id);
        return $org_name;


    }

    public function orgSearch($search_key)
    {
        if (isset($_POST['submit'])) {
            $skill = $_POST['org_search'];
            echo 'Selected Skill: ' . $skill;
        }


    }

    public function getMDOReportTypes()
    {
        $options = array();
        $options['mdoUserList'] = 'MDO-wise user list';
        $options['mdoUserCount'] = 'MDO-wise user count';
        $options['mdoAdminList'] = 'MDO Admin list';
        $options['mdoUserEnrolment'] = 'MDO-wise user enrolment report';
        $options['ministryUserEnrolment'] = 'User list for all organisations under a ministry';

        return $options;
    }


    public function search()
    {
        $returnData = array();
        $request = service('request');

        $searchKey = $request->getPost('key');
        $org = new MasterOrganizationModel();

        $org_name = $org->searchOrg($searchKey);

        // Generate array
        if (!empty($org_name)) {
            foreach ($org_name as $row) {
                $data['root_org_id'] = $row['root_org_id'];
                $data['org_name'] = $row['org_name'];
                array_push($returnData, $data);
            }
        }

        // Return results as json encoded array
        echo json_encode($returnData);
        die;

    }

    public function getExcel()
    {


    }

}