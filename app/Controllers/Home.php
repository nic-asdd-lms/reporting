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

        $org = '';
        if ($role == 'MDO_ADMIN') {
            $org = $session->get('organisation');
        }
        $courseReportType = $request->getPost('courseReportType');
        $course = $request->getPost('course');
        if ($courseReportType == 'courseEnrolmentReport') {
            $data['org'] = $org;
            $data['course'] = $course;
            $data['reportType'] = 'course';
            $data['resultHTML'] = $enrolment->getCourseWiseEnrolmentReport($course, $org);
            $data['reportTitle'] = 'User Enrolment Report for Course - "' . $this->getCourseName($course) . '"';
            $data['fileName'] = $course . '_EnrolmentReport';

        } else if ($courseReportType == 'courseEnrolmentCount') {
            $data['org'] = $org;
            $data['course'] = $course;
            $data['reportType'] = 'course';
            $data['resultHTML'] = $enrolment->getCourseWiseEnrolmentCount($course, $org);
            $data['reportTitle'] = 'Course-wise Enrolment/Completion Count';
            $data['fileName'] = $course . '_EnrolmentCompletionCount';

        } else if ($courseReportType == 'programEnrolmentReport') {
            $data['org'] = $org;
            $data['course'] = $course;
            $data['reportType'] = 'course';
            $data['resultHTML'] = $enrolmentProgram->getProgramWiseEnrolmentReport($course, $org);
            $data['reportTitle'] = 'User Enrolment Report for Program - "' . $this->getProgramName($course) . '"';
            $data['fileName'] = $course . '_EnrolmentReport';

        } else if ($courseReportType == 'programEnrolmentCount') {
            $data['org'] = $org;
            $data['course'] = $course;
            $data['reportType'] = 'course';
             $data['resultHTML'] = $enrolmentProgram->getProgramWiseEnrolmentCount($course, $org);
            $data['reportTitle'] = 'Program-wise Enrolment/Completion Count';
            $data['fileName'] = $course . '_EnrolmentCompletionCount';

        } else if ($courseReportType == 'collectionEnrolmentReport') {
            $data['org'] = $org;
            $data['course'] = $course;
            $data['reportType'] = 'course';
            $data['resultHTML'] = $enrolment->getCollectionWiseEnrolmentRepor($course, $org);
            $data['reportTitle'] = 'User Enrolment Report for Curated Collection - "' . $this->getCollectionName($course) . '"';
            $data['fileName'] = $course . '_EnrolmentReport';

        } else if ($courseReportType == 'collectionEnrolmentCount') {
            $data['org'] = $org;
            $data['course'] = $course;
            $data['reportType'] = 'course';
            $data['resultHTML'] = $enrolment->getCollectionWiseEnrolmentCount($course, $org);
            $data['reportTitle'] = 'Enrolment/Completion Count for Curated Collection - "' . $this->getCollectionName($course) . '"';
            $data['fileName'] = $course . '_EnrolmentCompletionCount';

        }

        else if ($courseReportType == 'courseMinistrySummary') {
            $data['org'] = $org;
            $data['course'] = $course;
            $data['reportType'] = 'course';
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
            $orgName = $this->getOrgName($dept);
        } else if ($ministry != "notSelected") {
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

                
                
                $data['orgName'] = $orgName;
                $data['reportType'] = 'org';
                $data['resultHTML'] = $user->getUserByOrg($orgName);
                $data['reportTitle'] = 'Users onboarded from organisation - "' . $orgName . '"';
                $data['fileName'] = $orgName . '_UserList';
            }
        } else
            if ($mdoReportType == 'mdoUserCount') {
                $data['resultHTML'] = $user->getUserCountByOrg();
                $data['reportType'] = 'no_param';
                $data['reportTitle'] = 'MDO-wise user count ';
                $data['fileName'] = 'MDOWiseUserCount';
            } else if ($mdoReportType == 'mdoUserEnrolment') {
                $data['orgName'] = $orgName;
                $data['reportType'] = 'org';
                $data['resultHTML'] = $enrolment->getEnrolmentByOrg($orgName);
                $data['reportTitle'] = 'Users Enrolment Report for organisation - "' . $orgName . '"';
                $data['fileName'] = $orgName . '_UserEnrolmentReport';

            } else if ($mdoReportType == 'ministryUserEnrolment') {
                $data['orgName'] = $ministryName;
                $data['reportType'] = 'org';
                $data['resultHTML'] = $user->getUserByMinistry($ministryName);
                $data['reportTitle'] = 'Users list for all organisations under ministry - "' . $ministryName . '"';
                $data['fileName'] = $orgName . '_UserList';

            } else if ($mdoReportType == 'userWiseCount') {
                $data['orgName'] = $ministryName;
                $data['reportType'] = 'org';
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
            $data['resultHTML'] = $user->getRoleWiseCount($orgName);
            $data['reportTitle'] = 'Role-wise count';
            $data['fileName'] = 'RoleWiseCount';

        } else if ($roleReportType == 'monthWiseMDOAdminCount') {
            $data['resultHTML'] = $user->getMonthWiseMDOAdminCount($orgName);
            $data['reportTitle'] = 'Month-wise MDO Admin Creation Count';
            $data['fileName'] = 'RoleWiseCount';

        } else if ($roleReportType == 'cbpAdminList') {
            $data['resultHTML'] = $user->getCBPAdminList($orgName);
            $data['reportTitle'] = 'MDO-wise user count ';
            $data['fileName'] = 'MDOWiseUserCount';

        } else if ($roleReportType == 'mdoAdminList') {
            $data['resultHTML'] = $user->getMDOAdminList($orgName);
            $data['reportTitle'] = 'MDO Admin List ';
            $data['fileName'] = 'MDOAdminList';

        } else if ($roleReportType == 'creatorList') {
            $data['resultHTML'] = $user->getCreatorList($orgName);
            $data['reportTitle'] = 'List of Content Creators';
            $data['fileName'] = '_UserEnrolmentReport';
        } else if ($roleReportType == 'reviewerList') {
            $data['resultHTML'] = $user->getReviewerList($orgName);
            $data['reportTitle'] = 'List of Content Reviewers';
            $data['fileName'] = '_UserList';

        } else if ($roleReportType == 'publisherList') {
            $data['resultHTML'] = $user->getPublisherList($orgName);
            $data['reportTitle'] = 'List of Content Publishers';
            $data['fileName'] = '_UserList';

        } else if ($roleReportType == 'editorList') {
            $data['resultHTML'] = $user->getEditorList($orgName);
            $data['reportTitle'] = 'List of Editors';
            $data['fileName'] = '_UserList';

        } else if ($roleReportType == 'fracAdminList') {
            $data['resultHTML'] = $user->getFracAdminList($orgName);
            $data['reportTitle'] = 'List of FRAC Admins';
            $data['fileName'] = '_UserList';

        } else if ($roleReportType == 'fracCompetencyMember') {
            $data['resultHTML'] = $user->getFracCompetencyMemberList($orgName);
            $data['reportTitle'] = 'List of FRAC Competency Members';
            $data['fileName'] = '_UserList';

        } else if ($roleReportType == 'fracL1List') {
            $data['resultHTML'] = $user->getFRACL1List($orgName);
            $data['reportTitle'] = 'MDO Admin List ';
            $data['fileName'] = 'MDOAdminList';

        } else if ($roleReportType == 'fracL2List') {
            $data['resultHTML'] = $user->getFRACL2List($orgName);
            $data['reportTitle'] = 'List of Content Creators';
            $data['fileName'] = '_UserEnrolmentReport';

        } else if ($roleReportType == 'ifuMemberList') {
            $data['resultHTML'] = $user->getIFUMemberList($orgName);
            $data['reportTitle'] = 'List of Content Reviewers';
            $data['fileName'] = '_UserList';

        } else if ($roleReportType == 'publicList') {
            $data['resultHTML'] = $user->getPublicList($orgName);
            $data['reportTitle'] = 'List of Content Publishers';
            $data['fileName'] = '_UserList';

        } else if ($roleReportType == 'spvAdminList') {
            $data['resultHTML'] = $user->getSPVAdminList($orgName);
            $data['reportTitle'] = 'List of Editors';
            $data['fileName'] = '_UserList';

        } else if ($roleReportType == 'stateAdminList') {
            $data['resultHTML'] = $user->getStateAdminList($orgName);
            $data['reportTitle'] = 'List of FRAC Admins';
            $data['fileName'] = '_UserList';

        } else if ($roleReportType == 'watMemberList') {
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
            $data['resultHTML'] = $user->getDayWiseUserOnboarding();
            $data['reportTitle'] = 'Day-wise User Onboarding';
            $data['fileName'] = 'RoleWiseCount';

        } else if ($analyticsReportType == 'monthWiseUserOnboarding') {
            $data['resultHTML'] = $user->getMonthWiseUserOnboarding();
            $data['reportTitle'] = 'Month-wise User Onboarding';
            $data['fileName'] = 'MDOWiseUserCount';

        } else if ($analyticsReportType == 'monthWiseCourses') {
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

        $org = '';
        if ($role == 'ATI_ADMIN') {
            $org = $session->get('organisation');
        }
        $doptReportType = $request->getPost('doptReportType');
        $ati = $request->getPost('ati');
        if ($doptReportType == 'atiWiseOverview') {
            $data['resultHTML'] = $user->getATIWiseOverview();
            $data['reportTitle'] = 'ATI-wise Overview';
            $data['fileName'] = 'ATIWiseOverview';

        }

        return view('header_view')
            . view('report_result', $data)
            . view('footer_view');

    }

    


    public function getUserByOrgReport()
    {
        $request = service('request');
$orgName = $request->getPost('orgName');
        
        $user = new MasterUserModel();

        $userDataReport = $user->getUserByOrgReport($orgName);


        $fileName = 'students'.time().'.xlsx';  
		$spreadsheet = new Spreadsheet();

		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'name');
		$sheet->setCellValue('B1', 'email');
		$sheet->setCellValue('C1', 'org_name');
		$sheet->setCellValue('D1', 'designation');
		$sheet->setCellValue('E1', 'phone');
		$sheet->setCellValue('F1', 'created_date');       
		$sheet->setCellValue('G1', 'roles');       
		$sheet->setCellValue('H1', 'profile_update_status');       
		$rows = 2;

		foreach ($userDataReport as $val){
		  $sheet->setCellValue('A' . $rows, $val['name']);
		  $sheet->setCellValue('B' . $rows, $val['email']);
		  $sheet->setCellValue('C' . $rows, $val['org_name']);
		  $sheet->setCellValue('D' . $rows, $val['designation']);
		  $sheet->setCellValue('E' . $rows, $val['phone']);
		  $sheet->setCellValue('F' . $rows, $val['created_date']);
		  $sheet->setCellValue('G' . $rows, $val['roles']);
		  $sheet->setCellValue('H' . $rows, $val['profile_update_status']);
		  $rows++;
		} 
		$writer = new Xlsx($spreadsheet);
		$writer->save("upload/".$fileName);
		header("Content-Type: application/vnd.ms-excel");
		//redirect(base_url()."/upload/".$fileName);




        // print_r($userDataReport);
        // die;

    }
    public function getOrgReports()
    {
        $request = service('request');

        $orgName = $request->getPost('orgName');

        $user = new MasterUserModel();

        $userDataReport = $user->getUserByOrgReport($orgName);


        $fileName = 'students.xlsx';  
		$spreadsheet = new Spreadsheet();

		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'name');
		$sheet->setCellValue('B1', 'email');
		$sheet->setCellValue('C1', 'org_name');
		$sheet->setCellValue('D1', 'designation');
		$sheet->setCellValue('E1', 'phone');
		$sheet->setCellValue('F1', 'created_date');       
		$sheet->setCellValue('G1', 'roles');       
		$sheet->setCellValue('H1', 'profile_update_status');       
		$rows = 2;

		foreach ($userDataReport as $val){
		  $sheet->setCellValue('A' . $rows, $val['name']);
		  $sheet->setCellValue('B' . $rows, $val['email']);
		  $sheet->setCellValue('C' . $rows, $val['org_name']);
		  $sheet->setCellValue('D' . $rows, $val['designation']);
		  $sheet->setCellValue('E' . $rows, $val['phone']);
		  $sheet->setCellValue('F' . $rows, $val['created_date']);
		  $sheet->setCellValue('G' . $rows, $val['roles']);
		  $sheet->setCellValue('H' . $rows, $val['profile_update_status']);
		  $rows++;
		} 
		$writer = new Xlsx($spreadsheet);
		$writer->save("upload/".$fileName);
		header("Content-Type: application/vnd.ms-excel");
		//redirect(base_url()."/upload/".$fileName);




        // print_r($userDataReport);
        // die;

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