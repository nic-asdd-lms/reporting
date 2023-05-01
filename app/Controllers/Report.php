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
use CodeIgniter\Config\Services;

class Report extends BaseController
{
    var $resultArray;
    protected $pager;

    public function __construct()
    {
        $this->pager = Services::pager();
    }


    public function getMDOReportClientSide()
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
            $orgModel = new MasterOrganizationModel();

            if ($mdoReportType == 'notSelected') {
                echo '<script>alert("Please select report type!");</script>';
                return view('header_view')
                    . view('footer_view');
            } else {
                $data['lastUpdated'] = '[Report as on ' . $lastUpdate->getReportLastUpdatedTime() . ']';

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
                    $orgName = $orgModel->getOrgName($org);
                    if ($orgName == null) {
                        return view('header_view')

                            . view('footer_view');
                    }

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

                        $session->setTempdata('resultArray', $result->getResultArray(), 300);
                        $session->setTempdata('fileName', $orgName . '_UserList', 300);

                        $data['resultHTML'] = $table->generate($result);
                        ;
                        $data['reportTitle'] = 'Users onboarded from organisation - "' . $orgName . '"';

                    }

                } else
                    if ($mdoReportType == 'mdoUserCount') {
                        $table->setHeading('Organisation', 'User Count');

                        $result = $user->getUserCountByOrg();

                        $session->setTempdata('resultArray', $result->getResultArray(), 300);
                        $session->setTempdata('fileName', 'MDOWiseUserCount', 300);

                        $data['resultHTML'] = $table->generate($result);
                        ;
                        $data['reportTitle'] = 'MDO-wise user count ';

                    } else if ($mdoReportType == 'mdoUserEnrolment') {
                        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Course', 'Status', 'Completion Percentage', 'Completed On');

                        $result = $enrolment->getEnrolmentByOrg($orgName);

                        $session->setTempdata('resultArray', $result->getResultArray(), 300);
                        $session->setTempdata('fileName', $orgName . '_UserEnrolmentReport', 300);

                        $data['resultHTML'] = $table->generate($result);
                        ;
                        $data['reportTitle'] = 'Users Enrolment Report for organisation - "' . $orgName . '"';

                    } else if ($mdoReportType == 'ministryUserEnrolment') {
                        $table->setHeading('Name', 'Email', 'Ministry', 'Department', 'Organization', 'Designation', 'Contact No.', 'Created Date', 'Roles');

                        $result = $user->getUserByMinistry($ministryName);

                        $session->setTempdata('resultArray', $result->getResultArray(), 300);
                        $session->setTempdata('fileName', $ministryName . '_UserList', 300);

                        $data['resultHTML'] = $table->generate($result);
                        $data['reportTitle'] = 'Users list for all organisations under ministry/state - "' . $ministryName . '"';

                    } else if ($mdoReportType == 'userWiseCount') {

                        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Course', 'Status', 'Completion Percentage', 'Completed On');

                        $result = $enrolment->getUserEnrolmentCountByMDO($orgName);

                        $session->setTempdata('resultArray', $result->getResultArray(), 300);
                        $session->setTempdata('fileName', $orgName . '_UserList', 300);

                        $data['resultHTML'] = $table->generate($result);
                        ;
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

    public function getReport()
    {

        $request = $this->request->getVar();
        $limit = (int) $request['length'];
        $offset = (int) $request['start'];
        $search = $request['search']['value'];
        $orderBy = $request['order'][0]['column'];
        $orderDir = $request['order'][0]['dir'];

        $session = \Config\Services::session();
        $user = new MasterUserModel();
        $enrolment = new UserEnrolmentCourse();
        $enrolmentProgram = new UserEnrolmentProgram();
        $org_hierarchy = new MasterStructureModel();
        $lastUpdate = new DataUpdateModel();
        $orgModel = new MasterOrganizationModel();
        $course = new MasterCourseModel();
            
        $reportType = $this->request->uri->getSegments()[1];


        // Set report-specific inputs
        if ($reportType == 'mdoUserList' || $reportType == 'moUserCount' || $reportType == 'mdoUserEnrolment' || $reportType == 'ministryUserEnrolment' || $reportType == 'userWiseCount') {
            $ministry = $session->getTempdata('ministry');
            $dept = $session->getTempdata('dept');
            $org = $session->getTempdata('org');

            if ($ministry != "notSelected") {
                $ministryName = $org_hierarchy->getMinistryStateName($ministry);
            }
            if ($dept != "notSelected") {
                $deptName = $org_hierarchy->getDeptName($dept);

            }

            if ($org != "notSelected") {
                $orgName = $orgModel->getOrgName($org);

            } else if ($dept != "notSelected") {
                $org = $dept;
                $orgName = $org_hierarchy->getDeptName($dept);
            } else if ($ministry != "notSelected") {
                $org = $ministry;
                $orgName = $org_hierarchy->getMinistryStateName($ministry);
            }
        } else if ($reportType == 'courseEnrolmentReport' || $reportType == 'courseEnrolmentCount' || $reportType == 'collectionEnrolmentCount' || $reportType == 'programEnrolmentReport' || $reportType == 'programEnrolmentCount' || $reportType == 'collectionEnrolmentReport' || $reportType == 'courseMinistrySummary') {
            $course = $session->getTempdata('course');
            if ($session->get('role') == 'MDO_ADMIN') {
                $org = $session->get('organisation');
            } else
                $org = '';

        }
        else if ($reportType == 'roleWiseCount' || $reportType == 'monthWiseMDOAdminCount' || $reportType == 'cbpAdminList' || $reportType == 'mdoAdminList'  || $reportType == 'creatorList' || $reportType == 'reviewerList'  || $reportType == 'publisherList' || $reportType == 'editorList'  || $reportType == 'fracAdminList' || $reportType == 'fracCompetencyMember'  || $reportType == 'fracL1List' || $reportType == 'fracL2List'  || $reportType == 'ifuMemberList' || $reportType == 'publicList'  || $reportType == 'spvAdminList' || $reportType == 'stateAdminList' || $reportType == 'watMemberList' )
        {
            if ($session->get('role') == 'MDO_ADMIN') {
                $org = $session->get('organisation');
                $orgName = $orgModel->getOrgName($org);
            } else{
                $org = '';
                $orgName = '';
            }

        }


        if ($reportType == 'mdoUserList') {
            $result = $user->getUserByOrg($orgName, $limit, $offset, $search, $orderBy, $orderDir);
            $fullResult = $user->getUserByOrg($orgName, -1, 0, '', $orderBy, $orderDir);
            $resultFiltered = $user->getUserByOrg($orgName, -1, 0, $search, $orderBy, $orderDir);

        } else if ($reportType == 'mdoUserCount') {
            $result = $user->getUserCountByOrg($limit, $offset, $search, $orderBy, $orderDir);
            $fullResult = $user->getUserCountByOrg(-1, 0, '', $orderBy, $orderDir);
            $resultFiltered = $user->getUserCountByOrg(-1, 0, $search, $orderBy, $orderDir);

        } else if ($reportType == 'mdoUserEnrolment') {
            $result = $enrolment->getEnrolmentByOrg($orgName, $limit, $offset, $search, $orderBy, $orderDir);
            $fullResult = $enrolment->getEnrolmentByOrg($orgName, -1, 0, '', $orderBy, $orderDir);
            $resultFiltered = $enrolment->getEnrolmentByOrg($orgName, -1, 0, $search, $orderBy, $orderDir);

        } else if ($reportType == 'ministryUserEnrolment') {
            $result = $user->getUserByMinistry($ministryName, $limit, $offset, $search, $orderBy, $orderDir);
            $fullResult = $user->getUserByMinistry($ministryName, -1, 0, '', $orderBy, $orderDir);
            $resultFiltered = $user->getUserByMinistry($ministryName, -1, 0, $search, $orderBy, $orderDir);

        } else if ($reportType == 'userWiseCount') {
            $result = $enrolment->getUserEnrolmentCountByMDO($orgName, $limit, $offset, $search, $orderBy, $orderDir);
            $fullResult = $enrolment->getUserEnrolmentCountByMDO($orgName, -1, 0, '', $orderBy, $orderDir);
            $resultFiltered = $enrolment->getUserEnrolmentCountByMDO($orgName, -1, 0, $search, $orderBy, $orderDir);
        } else if ($reportType == 'courseEnrolmentReport') {
            $result = $enrolment->getCourseWiseEnrolmentReport($course, $org, $limit, $offset, $search, $orderBy, $orderDir);
            $fullResult = $enrolment->getCourseWiseEnrolmentReport($course, $org, -1, 0, '', $orderBy, $orderDir);
            $resultFiltered = $enrolment->getCourseWiseEnrolmentReport($course, $org, -1, 0, $search, $orderBy, $orderDir);

        } else if ($reportType == 'courseEnrolmentCount') {
            $result = $enrolment->getCourseWiseEnrolmentCount($org, $limit, $offset, $search, $orderBy, $orderDir);
            $fullResult = $enrolment->getCourseWiseEnrolmentCount($org, -1, 0, '', $orderBy, $orderDir);
            $resultFiltered = $enrolment->getCourseWiseEnrolmentCount($org, -1, 0, $search, $orderBy, $orderDir);

        } else if ($reportType == 'programEnrolmentReport') {
            $result = $enrolmentProgram->getProgramWiseEnrolmentReport($course, $org, $limit, $offset, $search, $orderBy, $orderDir);
            $fullResult = $enrolmentProgram->getProgramWiseEnrolmentReport($course, $org, -1, 0, '', $orderBy, $orderDir);
            $resultFiltered = $enrolmentProgram->getProgramWiseEnrolmentReport($course, $org, -1, 0, $search, $orderBy, $orderDir);

        } else if ($reportType == 'programEnrolmentCount') {
            $result = $enrolmentProgram->getProgramWiseEnrolmentCount($org, $limit, $offset, $search, $orderBy, $orderDir);
            $fullResult = $enrolmentProgram->getProgramWiseEnrolmentCount($org, -1, 0, '', $orderBy, $orderDir);
            $resultFiltered = $enrolmentProgram->getProgramWiseEnrolmentCount($org, -1, 0, $search, $orderBy, $orderDir);

        } else if ($reportType == 'collectionEnrolmentReport') {
            $result = $enrolment->getCollectionWiseEnrolmentReport($course, $org, $limit, $offset, $search, $orderBy, $orderDir);
            $fullResult = $enrolment->getCollectionWiseEnrolmentReport($course, $org, -1, 0, '', $orderBy, $orderDir);
            $resultFiltered = $enrolment->getCollectionWiseEnrolmentReport($course, $org, -1, 0, $search, $orderBy, $orderDir);

        } else if ($reportType == 'collectionEnrolmentCount') {
            $result = $enrolment->getCollectionWiseEnrolmentCount($course, $org, $limit, $offset, $search, $orderBy, $orderDir);
            $fullResult = $enrolment->getCollectionWiseEnrolmentCount($course, $org, -1, 0, '', $orderBy, $orderDir);
            $resultFiltered = $enrolment->getCollectionWiseEnrolmentCount($course, $org, -1, 0, $search, $orderBy, $orderDir);

        } else if ($reportType == 'courseMinistrySummary') {
            $result = $enrolment->getUserEnrolmentCountByMDO($orgName, $limit, $offset, $search, $orderBy, $orderDir);
            $fullResult = $enrolment->getUserEnrolmentCountByMDO($orgName, -1, 0, '', $orderBy, $orderDir);
            $resultFiltered = $enrolment->getUserEnrolmentCountByMDO($orgName, -1, 0, $search, $orderBy, $orderDir);
        } else if ($reportType == 'roleWiseCount') {
            $result = $user->getRoleWiseCount($orgName, $limit, $offset, $search, $orderBy, $orderDir);
            $fullResult = $user->getRoleWiseCount($orgName, -1, 0, '', $orderBy, $orderDir);
            $resultFiltered = $user->getRoleWiseCount($orgName, -1, 0, $search, $orderBy, $orderDir);

        } else if ($reportType == 'monthWiseMDOAdminCount') {
            $result = $user->getMonthWiseMDOAdminCount($orgName, $limit, $offset, $search, $orderBy, $orderDir);
            $fullResult = $user->getMonthWiseMDOAdminCount($orgName, -1, 0, '', $orderBy, $orderDir);
            $resultFiltered = $user->getMonthWiseMDOAdminCount($orgName, -1, 0, $search, $orderBy, $orderDir);
        } else if ($reportType == 'cbpAdminList') {
            $result = $user->getCBPAdminList($orgName, $limit, $offset, $search, $orderBy, $orderDir);
            $fullResult = $user->getCBPAdminList($orgName, -1, 0, '', $orderBy, $orderDir);
            $resultFiltered = $user->getCBPAdminList($orgName, -1, 0, $search, $orderBy, $orderDir);

        } else if ($reportType == 'mdoAdminList') {
            $result = $user->getMDOAdminList($orgName, $limit, $offset, $search, $orderBy, $orderDir);
            $fullResult = $user->getMDOAdminList($orgName, -1, 0, '', $orderBy, $orderDir);
            $resultFiltered = $user->getMDOAdminList($orgName, -1, 0, $search, $orderBy, $orderDir);

        } else if ($reportType == 'creatorList') {
            $result = $user->getCreatorList($orgName, $limit, $offset, $search, $orderBy, $orderDir);
            $fullResult = $user->getCreatorList($orgName, -1, 0, '', $orderBy, $orderDir);
            $resultFiltered = $user->getCreatorList($orgName, -1, 0, $search, $orderBy, $orderDir);
        } else if ($reportType == 'reviewerList') {
            $result = $user->getReviewerList($orgName, $limit, $offset, $search, $orderBy, $orderDir);
            $fullResult = $user->getReviewerList($orgName, -1, 0, '', $orderBy, $orderDir);
            $resultFiltered = $user->getReviewerList($orgName, -1, 0, $search, $orderBy, $orderDir);

        } else if ($reportType == 'publisherList') {
            $result = $user->getPublisherList($orgName, $limit, $offset, $search, $orderBy, $orderDir);
            $fullResult = $user->getPublisherList($orgName, -1, 0, '', $orderBy, $orderDir);
            $resultFiltered = $user->getPublisherList($orgName, -1, 0, $search, $orderBy, $orderDir);

        } else if ($reportType == 'editorList') {
            $result = $user->getEditorList($orgName, $limit, $offset, $search, $orderBy, $orderDir);
            $fullResult = $user->getEditorList($orgName, -1, 0, '', $orderBy, $orderDir);
            $resultFiltered = $user->getEditorList($orgName, -1, 0, $search, $orderBy, $orderDir);

        } else if ($reportType == 'fracAdminList') {
            $result = $user->getFracAdminList($orgName, $limit, $offset, $search, $orderBy, $orderDir);
            $fullResult = $user->getFracAdminList($orgName, -1, 0, '', $orderBy, $orderDir);
            $resultFiltered = $user->getFracAdminList($orgName, -1, 0, $search, $orderBy, $orderDir);

        } else if ($reportType == 'fracCompetencyMember') {
            $result = $user->getFracCompetencyMemberList($orgName, $limit, $offset, $search, $orderBy, $orderDir);
            $fullResult = $user->getFracCompetencyMemberList($orgName, -1, 0, '', $orderBy, $orderDir);
            $resultFiltered = $user->getFracCompetencyMemberList($orgName, -1, 0, $search, $orderBy, $orderDir);

        } else if ($reportType == 'fracL1List') {
            $result = $user->getFRACL1List($orgName, $limit, $offset, $search, $orderBy, $orderDir);
            $fullResult = $user->getFRACL1List($orgName, -1, 0, '', $orderBy, $orderDir);
            $resultFiltered = $user->getFRACL1List($orgName, -1, 0, $search, $orderBy, $orderDir);

        } else if ($reportType == 'fracL2List') {
            $result = $user->getFRACL2List($orgName, $limit, $offset, $search, $orderBy, $orderDir);
            $fullResult = $user->getFRACL2List($orgName, -1, 0, '', $orderBy, $orderDir);
            $resultFiltered = $user->getFRACL2List($orgName, -1, 0, $search, $orderBy, $orderDir);

        } else if ($reportType == 'ifuMemberList') {
            $result = $user->getIFUMemberList($orgName, $limit, $offset, $search, $orderBy, $orderDir);
            $fullResult = $user->getIFUMemberList($orgName, -1, 0, '', $orderBy, $orderDir);
            $resultFiltered = $user->getIFUMemberList($orgName, -1, 0, $search, $orderBy, $orderDir);

        } else if ($reportType == 'publicList') {
            $result = $user->getPublicList($orgName, $limit, $offset, $search, $orderBy, $orderDir);
            $fullResult = $user->getPublicList($orgName, -1, 0, '', $orderBy, $orderDir);
            $resultFiltered = $user->getPublicList($orgName, -1, 0, $search, $orderBy, $orderDir);

        } else if ($reportType == 'spvAdminList') {
            $result = $user->getSPVAdminList($orgName, $limit, $offset, $search, $orderBy, $orderDir);
            $fullResult = $user->getRoleWigetSPVAdminListseCount($orgName, -1, 0, '', $orderBy, $orderDir);
            $resultFiltered = $user->getSPVAdminList($orgName, -1, 0, $search, $orderBy, $orderDir);
            
        } else if ($reportType == 'stateAdminList') {
            $result = $user->getStateAdminList($orgName, $limit, $offset, $search, $orderBy, $orderDir);
            $fullResult = $user->getStateAdminList($orgName, -1, 0, '', $orderBy, $orderDir);
            $resultFiltered = $user->getStateAdminList($orgName, -1, 0, $search, $orderBy, $orderDir);

        } else if ($reportType == 'watMemberList') {
            $result = $user->getWATMemberList($orgName, $limit, $offset, $search, $orderBy, $orderDir);
            $fullResult = $user->getWATMemberList($orgName, -1, 0, '', $orderBy, $orderDir);
            $resultFiltered = $user->getWATMemberList($orgName, -1, 0, $search, $orderBy, $orderDir);

        } else if ($reportType == 'dayWiseUserOnboarding') {
            $result = $user->getDayWiseUserOnboarding( $limit, $offset, $search, $orderBy, $orderDir);
            $fullResult = $user->getDayWiseUserOnboarding( -1, 0, '', $orderBy, $orderDir);
            $resultFiltered = $user->getDayWiseUserOnboarding( -1, 0, $search, $orderBy, $orderDir);

        } else if ($reportType == 'monthWiseUserOnboarding') {
            $result = $user->getMonthWiseUserOnboarding( $limit, $offset, $search, $orderBy, $orderDir);
            $fullResult = $user->getMonthWiseUserOnboarding( -1, 0, '', $orderBy, $orderDir);
            $resultFiltered = $user->getMonthWiseUserOnboarding( -1, 0, $search, $orderBy, $orderDir);

        } else if ($reportType == 'monthWiseCourses') {
            $result = $course->getMonthWiseCourses( $limit, $offset, $search, $orderBy, $orderDir);
            $fullResult = $course->getMonthWiseCourses( -1, 0, '', $orderBy, $orderDir);
            $resultFiltered = $course->getMonthWiseCourses( -1, 0, $search, $orderBy, $orderDir);
            
        }

        $session->setTempdata('resultArray', $fullResult->getResultArray(), 300);
        $session->setTempdata('filteredResultArray', $resultFiltered->getResultArray(), 300);

        $response = array(
            "draw" => intval($request['draw']),
            "recordsTotal" => $fullResult->getNumRows(),
            "recordsFiltered" => $resultFiltered->getNumRows(),
            "data" => $result->getResultArray()
        );

        return $this->response->setJSON($response);

    }
    public function getMDOReport()
    {
        try {
            $request = service('request');
            $session = \Config\Services::session();
            $table = new \CodeIgniter\View\Table();
            $table->setTemplate($GLOBALS['tableTemplate']);

            $segments = $request->uri->getSegments();
            //echo json_encode($segments);
            $reportType = $request->getPost('mdoReportType') ? $request->getPost('mdoReportType') : $segments[1];


            $session->setTempdata('reportType', $request->getPost('mdoReportType'), 600);
            $session->setTempdata('ministry', $request->getPost('ministry') ? $request->getPost('ministry') : $session->get('ministry'), 600);
            $session->setTempdata('dept', $request->getPost('dept') ? $request->getPost('dept') : $session->get('department'), 600);
            $session->setTempdata('org', $request->getPost('org') ? $request->getPost('org') : $session->get('organisation'), 600);

            $role = $session->get('role');

            $home = new Home();
            $user = new MasterUserModel();
            $enrolment = new UserEnrolmentCourse();
            $org_hierarchy = new MasterStructureModel();
            $lastUpdate = new DataUpdateModel();
            $orgModel = new MasterOrganizationModel();


            if ($reportType == 'notSelected') {
                echo '<script>alert("Please select report type!");</script>';
                return view('header_view')
                    . view('footer_view');
            } else {
                $data['lastUpdated'] = '[Report as on ' . $lastUpdate->getReportLastUpdatedTime() . ']';

                if ($role == 'SPV_ADMIN') {
                    $ministry = $request->getPost('ministry') ? $request->getPost('ministry') : $session->setTempdata('ministry');
                    $dept = $request->getPost('dept') ? $request->getPost('dept') : $session->setTempdata('dept');
                    $org = $request->getPost('org') ? $request->getPost('org') : $session->setTempdata('org');
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
                    $orgName = $orgModel->getOrgName($org);
                    if ($orgName == null) {
                        return view('header_view')

                            . view('footer_view');
                    }

                } else if ($dept != "notSelected") {
                    $org = $dept;
                    $orgName = $org_hierarchy->getDeptName($dept);
                } else if ($ministry != "notSelected") {
                    $org = $ministry;
                    $orgName = $org_hierarchy->getMinistryStateName($ministry);
                }

                if ($reportType == 'mdoUserList') {

                    if ($ministry == "notSelected") {
                        echo '<script>alert("Please select ministry!");</script>';
                        return view('header_view')
                            . view('footer_view');
                    } else {
                        $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Contact No.', 'Created Date', 'Roles', 'Profile Update Status');

                        $session->setTempdata('fileName', $orgName . '_UserList', 300);

                        $data['resultHTML'] = $table->generate();
                        $data['reportTitle'] = 'Users onboarded from organisation - "' . $orgName . '"';

                    }

                } else if ($reportType == 'mdoUserCount') {
                    $table->setHeading('Organisation', 'User Count');

                    $session->setTempdata('fileName', 'MDOWiseUserCount', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'MDO-wise user count ';

                } else if ($reportType == 'mdoUserEnrolment') {
                    $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Course', 'Status', 'Completion Percentage', 'Completed On');

                    $session->setTempdata('fileName', $orgName . '_UserEnrolmentReport', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'Users Enrolment Report for organisation - "' . $orgName . '"';

                } else if ($reportType == 'ministryUserEnrolment') {
                    $table->setHeading('Name', 'Email', 'Ministry', 'Department', 'Organization', 'Designation', 'Contact No.', 'Created Date', 'Roles');

                    $session->setTempdata('fileName', $ministryName . '_UserList', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'Users list for all organisations under ministry/state - "' . $ministryName . '"';

                } else if ($reportType == 'userWiseCount') {

                    $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Course', 'Status', 'Completion Percentage', 'Completed On');

                    $session->setTempdata('fileName', $orgName . '_UserList', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'User-wise course enrolment/completion count for organisation - "' . $orgName . '"';

                }
                $data['reportType'] = $reportType;
                $data['ministry'] = $ministry;
                $data['dept'] = $dept;
                $data['org'] = $org;

                //return $this->response->setJSON($data);
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

            $segments = $request->uri->getSegments();
            //echo json_encode($segments);
            $reportType = $request->getPost('courseReportType') ? $request->getPost('courseReportType') : $segments[1];


            $session->setTempdata('reportType', $request->getPost('courseReportType'), 600);
            $session->setTempdata('course', $request->getPost('course'), 300);

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

                $data['lastUpdated'] = '[Report as on ' . $lastUpdate->getReportLastUpdatedTime() . ']';

                $org = '';
                if ($role == 'MDO_ADMIN') {
                    $org = $session->get('organisation');
                }
                if ($courseReportType == 'courseEnrolmentReport') {
                    $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Course Name', 'Completion Status', 'Completion Percentage', 'Completed On');

                    $session->setTempdata('fileName', $course . '_EnrolmentReport', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'User Enrolment Report for Course - "' . $home->getCourseName($course) . '"';


                } else if ($courseReportType == 'courseEnrolmentCount') {
                    $table->setHeading('Course Name', 'Published Date', 'Duration (HH:MM:SS)', 'Enrollment Count', 'Completion Count', 'Average Rating');

                    $session->setTempdata('fileName', 'Course-wiseSummary', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'Course-wise  Summary';

                } else if ($courseReportType == 'programEnrolmentReport') {
                    $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Status', 'Completed On');

                    $session->setTempdata('fileName', $course . '_EnrolmentReport', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'User Enrolment Report for Program - "' . $home->getProgramName($course) . '"';

                } else if ($courseReportType == 'programEnrolmentCount') {
                    $table->setHeading('Program Name', 'Batch ID', 'Enrollment Count', 'Completion Count');

                    $session->setTempdata('fileName', 'Program-wiseSummary', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'Program-wise Summary';

                } else if ($courseReportType == 'collectionEnrolmentReport') {
                    $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Course Name', 'Status', 'Completion Percentage', 'Completed On');

                    $session->setTempdata('fileName', $course . '_EnrolmentReport', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'User Enrolment Report for Curated Collection - "' . $home->getCollectionName($course) . '"';

                } else if ($courseReportType == 'collectionEnrolmentCount') {
                    $table->setHeading('Course Name', 'Enrolment Count', 'Completion Count');

                    $session->setTempdata('fileName', $course . '_Summary', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'Curated collection summary - "' . $home->getCollectionName($course) . '"';


                } else if ($courseReportType == 'courseMinistrySummary') {
                    $table->setHeading('Ministry Name', 'Enrollment Count', 'Completion Count');

                    $session->setTempdata('fileName', $course . '_MinistrySummary', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'Ministry-wise Summary for course - "' . $home->getCourseName($course) . '"';

                }
                $data['reportType'] = $reportType;

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

                $data['lastUpdated'] = '[Report as on ' . $lastUpdate->getReportLastUpdatedTime() . ']';

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
                    $table->setHeading('Role', 'Count');

                    $session->setTempdata('fileName', 'Role-wise count', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'Role-wise count';

                } else if ($roleReportType == 'monthWiseMDOAdminCount') {
                    $table->setHeading('Month', 'Count');

                    $session->setTempdata('fileName', 'Month-wise MDO Admin Creation Count', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'Month-wise MDO Admin Creation Count';

                } else if ($roleReportType == 'cbpAdminList') {
                    $table->setHeading('Name', 'Email', 'Organization', 'Designation', 'Contact No.', 'Creation Date', 'Roles');

                    $session->setTempdata('fileName', 'List of CBP Admins', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'List of CBP Admins';

                } else if ($roleReportType == 'mdoAdminList') {
                    $table->setHeading('Name', 'Email', 'Organization', 'Designation', 'Contact No.', 'Creation Date', 'Roles');

                    $session->setTempdata('fileName', 'List of MDO Admins', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'List of MDO Admins';

                } else if ($roleReportType == 'creatorList') {
                    $table->setHeading('Name', 'Email', 'Organization', 'Designation', 'Contact No.', 'Creation Date', 'Roles');

                    $session->setTempdata('fileName', 'List of Content Creators', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'List of Content Creators';

                } else if ($roleReportType == 'reviewerList') {
                    $table->setHeading('Name', 'Email', 'Organization', 'Designation', 'Contact No.', 'Creation Date', 'Roles');

                    $session->setTempdata('fileName', 'List of Content Reviewers', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'List of Content Reviewers';

                } else if ($roleReportType == 'publisherList') {
                    $table->setHeading('Name', 'Email', 'Organization', 'Designation', 'Contact No.', 'Creation Date', 'Roles');

                    $session->setTempdata('fileName', 'List of Content Publishers', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'List of Content Publishers';

                } else if ($roleReportType == 'editorList') {
                    $table->setHeading('Name', 'Email', 'Organization', 'Designation', 'Contact No.', 'Creation Date', 'Roles');

                    $session->setTempdata('fileName', 'List of Editors', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'List of Editors';

                } else if ($roleReportType == 'fracAdminList') {
                    $table->setHeading('Name', 'Email', 'Organization', 'Designation', 'Contact No.', 'Creation Date', 'Roles');

                    $session->setTempdata('fileName', 'List of FRAC Admins', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'List of FRAC Admins';


                } else if ($roleReportType == 'fracCompetencyMember') {
                    $table->setHeading('Name', 'Email', 'Organization', 'Designation', 'Contact No.', 'Creation Date', 'Roles');

                    $session->setTempdata('fileName', 'List of FRAC Competency Members', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'List of FRAC Competency Members';


                } else if ($roleReportType == 'fracL1List') {
                    $table->setHeading('Name', 'Email', 'Organization', 'Designation', 'Contact No.', 'Creation Date', 'Roles');

                    $session->setTempdata('fileName', 'List of FRAC_Reviewer_L1', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'List of FRAC_Reviewer_L1';

                } else if ($roleReportType == 'fracL2List') {
                    $table->setHeading('Name', 'Email', 'Organization', 'Designation', 'Contact No.', 'Creation Date', 'Roles');

                    $session->setTempdata('fileName', 'List of FRAC_Reviewer_L2', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'List of FRAC_Reviewer_L2';


                } else if ($roleReportType == 'ifuMemberList') {
                    $table->setHeading('Name', 'Email', 'Organization', 'Designation', 'Contact No.', 'Creation Date', 'Roles');

                    $session->setTempdata('fileName', 'List of IFU Members', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'List of IFU Members';

                } else if ($roleReportType == 'publicList') {
                    $table->setHeading('Name', 'Email', 'Organization', 'Designation', 'Contact No.', 'Creation Date', 'Roles');

                    $session->setTempdata('fileName', 'List of CBP Admins', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'List of Public users';

                } else if ($roleReportType == 'spvAdminList') {
                    $table->setHeading('Name', 'Email', 'Organization', 'Designation', 'Contact No.', 'Creation Date', 'Roles');

                    $session->setTempdata('fileName', 'List of SPV Admins', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'List of SPV Admins';

                } else if ($roleReportType == 'stateAdminList') {
                    $table->setHeading('Name', 'Email', 'Organization', 'Designation', 'Contact No.', 'Creation Date', 'Roles');

                    $session->setTempdata('fileName', 'List of State Admins', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'List of State Admins';


                } else if ($roleReportType == 'watMemberList') {
                    $table->setHeading('Name', 'Email', 'Organization', 'Designation', 'Contact No.', 'Creation Date', 'Roles');

                    $session->setTempdata('fileName', 'List of WAT Members', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'List of WAT Members';

                }
                $data['reportType'] = $roleReportType;

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

                $data['lastUpdated'] = '[Report as on ' . $lastUpdate->getReportLastUpdatedTime() . ']';

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
                    $table->setHeading('Creation Date', 'Count');

                    $session->setTempdata('fileName', 'Day-wise User Onboarding', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'Day-wise User Onboarding';

                } else if ($analyticsReportType == 'monthWiseUserOnboarding') {
                    $table->setHeading('Creation Month', 'Count');

                    $session->setTempdata('fileName', 'Month-wise User Onboarding', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'Month-wise User Onboarding';

                } else if ($analyticsReportType == 'monthWiseCourses') {
                    $table->setHeading('Month of Publishing', 'Count');

                    $session->setTempdata('fileName', 'Month-wise Courses Published', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'Month-wise Courses Published';

                }

                $data['reportType'] = $analyticsReportType;

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


            $data['lastUpdated'] = '[Report as on ' . $lastUpdate->getReportLastUpdatedTime() . ']';

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
                    $table->setHeading('Program Name', 'Institute', 'Enrolled', 'Not Started', 'In Progress', 'Completed');

                    $result = $user->getATIWiseCount();

                    $session->setTempdata('resultArray', $result->getResultArray(), 300);
                    $session->setTempdata('fileName', 'ATI-wise Overview', 300);

                    $data['resultHTML'] = $table->generate($result);
                    ;
                    $data['reportTitle'] = 'ATI-wise Overview';
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
            $query_param = current_url(true)->getQuery();
            $filtered = explode('=',$query_param)[1];

            if($filtered == 'false')
                $report = $session->getTempdata('resultArray');
            else if ($filtered == 'true')
                $report = $session->getTempdata('filteredResultArray');

                
            $fileName = $session->getTempdata('fileName') . '.xls';

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
            
            die;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }


    }



}