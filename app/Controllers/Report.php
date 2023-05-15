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
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use CodeIgniter\Config\Services;

class Report extends BaseController
{
    var $resultArray;
    protected $pager;

    public function __construct()
    {
        $this->pager = Services::pager();
    }



    public function getReport()
    {

        try {
            helper('session');
            if (session_exists()) {

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
                $courseModel = new MasterCourseModel();

                $reportType = $this->request->uri->getSegments()[1];
                $data['error'] = '';
                $session->setTempdata('error', '');
                $course = $session->getTempdata('course');




                // Set report-specific inputs

                if ($reportType == 'ministryUserEnrolment' || $reportType == 'orgHierarchy') {
                    if ($session->get('role') == 'MDO_ADMIN') {
                        $ministry = $session->get('organisation');
                    } else {
                        $ministry = $session->getTempdata('org');

                    }
                    $ministryName = $org_hierarchy->getMinistryStateName($ministry);

                } else if ($reportType == 'mdoUserList' || $reportType == 'moUserCount' || $reportType == 'mdoUserEnrolment' || $reportType == 'userWiseCount') {
                    if ($session->get('role') == 'SPV_ADMIN') { // SPV_ADMIN => ministry, department, organisation = values selected from dropdown or searchbox
                        $org = $session->getTempdata('org');

                    } else if ($session->get('role') == 'MDO_ADMIN') { // MDO_ADMIN => ministry, department, organisation = values from session (MDO of the particular user)
                        $org = $session->get('organisation');
                    }
                    $orgName = $orgModel->getOrgName($org);

                } else if ($reportType == 'courseEnrolmentReport' || $reportType == 'courseEnrolmentCount' || $reportType == 'collectionEnrolmentCount' || $reportType == 'programEnrolmentReport' || $reportType == 'programEnrolmentCount' || $reportType == 'collectionEnrolmentReport' || $reportType == 'courseMinistrySummary') {
                    if ($session->get('role') == 'MDO_ADMIN') {
                        $org = $session->get('organisation');
                    } else
                        $org = '';

                } else if ($reportType == 'roleWiseCount' || $reportType == 'monthWiseMDOAdminCount' || $reportType == 'cbpAdminList' || $reportType == 'mdoAdminList' || $reportType == 'creatorList' || $reportType == 'reviewerList' || $reportType == 'publisherList' || $reportType == 'editorList' || $reportType == 'fracAdminList' || $reportType == 'fracCompetencyMember' || $reportType == 'fracL1List' || $reportType == 'fracL2List' || $reportType == 'ifuMemberList' || $reportType == 'publicList' || $reportType == 'spvAdminList' || $reportType == 'stateAdminList' || $reportType == 'watMemberList') {
                    if ($session->get('role') == 'MDO_ADMIN') {
                        $org = $session->get('organisation');
                        $orgName = $orgModel->getOrgName($org);
                    } else {
                        $org = '';
                        $orgName = '';
                    }

                } else if ($reportType == 'atiWiseOverview') {
                    if ($session->get('role') == 'ATI_ADMIN') {
                        $org = $session->get('organisation');
                        $orgName = $orgModel->getOrgName($org);
                    } else {
                        $org = '';
                        $orgName = '';
                    }

                }


                if ($reportType == 'userList') {
                    $result = $user->getAllUsers($limit, $offset, $search, $orderBy, $orderDir); //  query with given limit, offset, search, order
                    $fullResult = $user->getAllUsers(-1, 0, '', $orderBy, $orderDir); //  query to get total no. of rows
                    $resultFiltered = $user->getAllUsers(-1, 0, $search, $orderBy, $orderDir); //  query to get count of filtered result

                } else if ($reportType == 'mdoUserList') {
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
                } else if ($reportType == 'orgList') {
                    $result = $orgModel->getOrgList($limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $orgModel->getOrgList(-1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $orgModel->getOrgList(-1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'orgHierarchy') {
                    $result = $org_hierarchy->getHierarchy($ministry, $limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $org_hierarchy->getHierarchy($ministry, -1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $org_hierarchy->getHierarchy($ministry, -1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'liveCourses') {
                    $result = $courseModel->getLiveCourses($limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $courseModel->getLiveCourses(-1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $courseModel->getLiveCourses(-1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'underPublishCourses') {
                    $result = $courseModel->getCoursesUnderPublish($limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $courseModel->getCoursesUnderPublish(-1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $courseModel->getCoursesUnderPublish(-1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'underReviewCourses') {
                    $result = $courseModel->getCoursesUnderReview($limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $courseModel->getCoursesUnderReview(-1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $courseModel->getCoursesUnderReview(-1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'draftCourses') {
                    $result = $courseModel->getDraftCourses($limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $courseModel->getDraftCourses(-1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $courseModel->getDraftCourses(-1, 0, $search, $orderBy, $orderDir);

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

                } else if ($reportType == 'cbpProviderWiseCourseCount') {
                    $result = $courseModel->getCourseCountByCBPProvider($limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $courseModel->getCourseCountByCBPProvider(-1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $courseModel->getCourseCountByCBPProvider(-1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'courseMinistrySummary') {
                    $result = $enrolment->getCourseMinistrySummary($course, $limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $enrolment->getCourseMinistrySummary($course, -1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $enrolment->getCourseMinistrySummary($course, -1, 0, $search, $orderBy, $orderDir);
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
                    $result = $user->getDayWiseUserOnboarding($limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $user->getDayWiseUserOnboarding(-1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $user->getDayWiseUserOnboarding(-1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'monthWiseUserOnboarding') {
                    $result = $user->getMonthWiseUserOnboarding($limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $user->getMonthWiseUserOnboarding(-1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $user->getMonthWiseUserOnboarding(-1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'monthWiseOrgOnboarding') {
                    $result = $orgModel->getMonthWiseOrgOnboarding($limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $orgModel->getMonthWiseOrgOnboarding(-1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $orgModel->getMonthWiseOrgOnboarding(-1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'monthWiseCourses') {
                    $result = $courseModel->getMonthWiseCourses($limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $courseModel->getMonthWiseCourses(-1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $courseModel->getMonthWiseCourses(-1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'atiWiseOverview') {
                    $result = $enrolmentProgram->getATIWiseCount($org, $limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $enrolmentProgram->getATIWiseCount($org, -1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $enrolmentProgram->getATIWiseCount($org, -1, 0, $search, $orderBy, $orderDir);

                }

                $session->remove('resultArray');
                $session->remove('filteredResultArray');

                $session->setTempdata('resultArray', $fullResult->getResultArray(), 300);
                $session->setTempdata('filteredResultArray', $resultFiltered->getResultArray(), 300);
                
                if($fullResult->getNumRows() == 0)
                    $session->setTempdata('error','No matching records found' , 300);
               
                
                $response = array(
                    "draw" => intval($request['draw']),
                    "recordsTotal" => $fullResult->getNumRows(),
                    "recordsFiltered" => $resultFiltered->getNumRows(),
                    "data" => $result->getResultArray()
                );


                return $this->response->setJSON($response);
            } else {
                return $this->response->redirect(base_url('/'));
            }
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
            // return view('header_view') . view('error_general').view('footer_view');
        }


    }
    public function getMDOReport()
    {
        try {
            helper('session');
            if (session_exists()) {

                $request = service('request');
                $session = \Config\Services::session();
                $table = new \CodeIgniter\View\Table();
                $table->setTemplate($GLOBALS['tableTemplate']);

                $segments = $request->uri->getSegments();

                $reportType = $request->getPost('mdoReportType') ? $request->getPost('mdoReportType') : $segments[1];


                $session->setTempdata('reportType', $request->getPost('mdoReportType'), 600);
                // $session->setTempdata('ministry', $request->getPost('ministry') ? $request->getPost('ministry') : $session->get('ministry'), 600);
                // $session->setTempdata('dept', $request->getPost('dept') ? $request->getPost('dept') : $session->get('department'), 600);
                $session->setTempdata('org', $request->getPost('org') ? $request->getPost('org') : $session->get('organisation'), 600);

                $role = $session->get('role');

                $home = new Home();
                $user = new MasterUserModel();
                $enrolment = new UserEnrolmentCourse();
                $org_hierarchy = new MasterStructureModel();
                $lastUpdate = new DataUpdateModel();
                $orgModel = new MasterOrganizationModel();




                $data['lastUpdated'] = '[Report as on ' . $lastUpdate->getReportLastUpdatedTime() . ']';
                $session->setTempdata('error', '');

                if ($role == 'SPV_ADMIN') { // SPV_ADMIN => ministry, department, organisation = values selected from dropdown or searchbox
                    $org = $request->getPost('org');

                } else if ($role == 'MDO_ADMIN') { // MDO_ADMIN => ministry, department, organisation = values from session (MDO of the particular user)

                    $ministry = $session->get('ministry');
                    $dept = $session->get('department');
                    $org = $session->get('organisation');
                }


                /*  For reports 'MDO-wise User List' and 'MDO-wise user Enrolment', only Ministry/State name is mandatory. Set orgName accordingly.
                If org is not selected, org = dept. If dept is also not selected, org = ministry */

                if ($org != "") {
                    if ($reportType == 'ministryUserEnrolment' || $reportType == 'orgHierarchy')
                        $orgName = $org_hierarchy->getMinistryStateName($org);
                    else
                        $orgName = $orgModel->getOrgName($org);

                }


                /* Set table header, filename and report tilte based on report type */

                if ($reportType == 'userList') {
                    $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Contact No.', 'Created Date', 'Roles', 'Profile Update Status');

                    $session->setTempdata('fileName', 'UserList', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'Users onboarded on iGOT';

                } else if ($reportType == 'mdoUserList') {
                    $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Contact No.', 'Created Date', 'Roles', 'Profile Update Status');

                    $session->setTempdata('fileName', $orgName . '_UserList', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'Users onboarded from organisation - "' . $orgName . '"';

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

                    $session->setTempdata('fileName', $orgName . '_UserList', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'Users list for all organisations under ministry/state - "' . $orgName . '"';

                } else if ($reportType == 'userWiseCount') {

                    $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'No. of Courses Enrolled', 'No. of Courses Completed');

                    $session->setTempdata('fileName', $orgName . '_UserWiseSummary', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'User-wise course enrolment/completion count for organisation - "' . $orgName . '"';

                } else if ($reportType == 'orgList') {

                    $table->setHeading('Organisation');

                    $session->setTempdata('fileName', 'OrgList', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'Organisations Onboarded';

                } else if ($reportType == 'orgHierarchy') {

                    $table->setHeading('Department', 'Organisation');

                    $session->setTempdata('fileName', $orgName . '_Hierarchy', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'Organisation Hierarchy - "' . $orgName . '"';

                }

                $data['reportType'] = $reportType;
                $data['org'] = $org;

                return view('header_view')
                    . view('report_result', $data)
                    . view('footer_view');
            } else {
                return $this->response->redirect(base_url('/'));
            }
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
            // return view('header_view') . view('error_general').view('footer_view');
        }

    }

    public function getCourseReport()
    {
        try {
            helper('session');
            if (session_exists()) {


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



                $data['lastUpdated'] = '[Report as on ' . $lastUpdate->getReportLastUpdatedTime() . ']';
                $session->setTempdata('error', '');
                $org = '';
                if ($role == 'MDO_ADMIN') {
                    $org = $session->get('organisation');
                }
                if ($courseReportType == 'liveCourses') {
                    $table->setHeading('Course Name', 'Course Provider', 'Duration (HH:MM:SS)', 'Published Date', 'No. of Ratings', 'Average Rating');

                    $session->setTempdata('fileName', 'LiveCourses', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'Live Courses';


                } else if ($courseReportType == 'underPublishCourses') {
                    $table->setHeading('Course Name', 'Course Provider');

                    $session->setTempdata('fileName', 'CoursesUnderPublish', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'Courses under Publish';


                } else if ($courseReportType == 'underReviewCourses') {
                    $table->setHeading('Course Name', 'Course Provider');

                    $session->setTempdata('fileName', 'CoursesUnderReview', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'Courses under review';


                } else if ($courseReportType == 'draftCourses') {
                    $table->setHeading('Course Name', 'Course Provider');

                    $session->setTempdata('fileName', 'DraftCourses', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'Courses in draft';


                } else if ($courseReportType == 'courseEnrolmentReport') {
                    $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Course Name', 'Completion Status', 'Completion Percentage', 'Completed On');

                    $session->setTempdata('fileName', $course . '_EnrolmentReport', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'User Enrolment Report for Course - "' . $home->getCourseName($course) . '"';


                } else if ($courseReportType == 'courseEnrolmentCount') {
                    $table->setHeading('Course Name', 'Course Provider', 'Published Date', 'Duration (HH:MM:SS)', 'Enrolled', 'Not Started', 'In Progress', 'Completed', 'Average Rating');

                    $session->setTempdata('fileName', 'Course-wiseSummary', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'Course-wise  Summary';

                } else if ($courseReportType == 'programEnrolmentReport') {
                    $table->setHeading('Name', 'Email ID', 'Organisation', 'Designation', 'Batch ID', 'Status', 'Completed On');

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


                } else if ($courseReportType == 'cbpProviderWiseCourseCount') {
                    $table->setHeading('CBP Provider Name', 'Course Count');

                    $session->setTempdata('fileName', 'CBPProviderSummary', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'CBP Provider-wise course count';


                } else if ($courseReportType == 'courseMinistrySummary') {
                    $table->setHeading('Ministry/State Name', 'Enrolled', 'Not Started', 'In Progress', 'Completed');

                    $session->setTempdata('fileName', $course . '_MinistrySummary', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'Ministry-wise Summary for course - "' . $home->getCourseName($course) . '"';

                }
                $data['reportType'] = $reportType;

                return view('header_view')
                    . view('report_result', $data)
                    . view('footer_view');
            } else {
                return $this->response->redirect(base_url('/'));
            }
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
            // return view('header_view') . view('error_general').view('footer_view');
        }
    }

    public function getRoleReport()
    {
        try {

            helper('session');
            if (session_exists()) {

                $request = service('request');
                $session = \Config\Services::session();

                $table = new \CodeIgniter\View\Table();
                $table->setTemplate($GLOBALS['tableTemplate']);

                $roleReportType = $request->getPost('roleReportType');
                $home = new Home();
                $user = new MasterUserModel();
                $lastUpdate = new DataUpdateModel();

                $segments = $request->uri->getSegments();
                $reportType = $request->getPost('roleReportType') ? $request->getPost('roleReportType') : $segments[1];

                $data['lastUpdated'] = '[Report as on ' . $lastUpdate->getReportLastUpdatedTime() . ']';
                $session->setTempdata('error', '');

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

            } else {
                return $this->response->redirect(base_url('/'));
            }
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
            // return view('header_view') . view('error_general').view('footer_view');
        }

    }

    public function getAnalytics()
    {
        try {
            helper('session');
            if (session_exists()) {


                $request = service('request');
                $session = \Config\Services::session();

                $table = new \CodeIgniter\View\Table();
                $table->setTemplate($GLOBALS['tableTemplate']);

                $analyticsReportType = $request->getPost('analyticsReportType');
                $role = $session->get('role');

                $segments = $request->uri->getSegments();
                $reportType = $request->getPost('analyticsReportType') ? $request->getPost('analyticsReportType') : $segments[1];

                $home = new Home();
                $user = new MasterUserModel();
                $course = new MasterCourseModel();
                $lastUpdate = new DataUpdateModel();

                $data['lastUpdated'] = '[Report as on ' . $lastUpdate->getReportLastUpdatedTime() . ']';
                $session->setTempdata('error', '');

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
                    $table->setHeading('Date', 'No. of Users Onboarded');

                    $session->setTempdata('fileName', 'Day-wise User Onboarding', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'Day-wise User Onboarding';

                } else if ($analyticsReportType == 'monthWiseUserOnboarding') {
                    $table->setHeading('Month', 'No. of Users Onboarded');

                    $session->setTempdata('fileName', 'Month-wise User Onboarding', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'Month-wise User Onboarding';

                } else if ($analyticsReportType == 'monthWiseOrgOnboarding') {
                    $table->setHeading('Month', 'No. of Organisations Onboarded');

                    $session->setTempdata('fileName', 'Month-wise Org Onboarding', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'Month-wise Organisation Onboarding';

                } else if ($analyticsReportType == 'monthWiseCourses') {
                    $table->setHeading('Month', 'No. of Courses Published');

                    $session->setTempdata('fileName', 'Month-wise Courses Published', 300);

                    $data['resultHTML'] = $table->generate();
                    $data['reportTitle'] = 'Month-wise Courses Published';

                }

                $data['reportType'] = $analyticsReportType;

                return view('header_view')
                    . view('report_result', $data)
                    . view('footer_view');

            } else {
                return $this->response->redirect(base_url('/'));
            }
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
            // return view('header_view') . view('error_general').view('footer_view');
        }

    }

    public function getDoptReport()
    {
        try {
            helper('session');
            if (session_exists()) {


                $request = service('request');
                $session = \Config\Services::session();

                $table = new \CodeIgniter\View\Table();
                $table->setTemplate($GLOBALS['tableTemplate']);

                $role = $session->get('role');
                $user = new UserEnrolmentProgram();
                $lastUpdate = new DataUpdateModel();

                $segments = $request->uri->getSegments();
                $reportType = $request->getPost('doptReportType') ? $request->getPost('doptReportType') : $segments[1];

                $data['lastUpdated'] = '[Report as on ' . $lastUpdate->getReportLastUpdatedTime() . ']';
                $session->setTempdata('error', '');

                $org = '';
                if ($role == 'ATI_ADMIN') {
                    $org = $session->get('organisation');
                }
                $doptReportType = $request->getPost('doptReportType'); {

                    $ati = $request->getPost('ati');
                    if ($reportType == 'atiWiseOverview') {
                        $table->setHeading('Program Name', 'Batch ID', 'Institute', 'Enrolled', 'Not Started', 'In Progress', 'Completed');

                        $session->setTempdata('fileName', 'ATI-wise Overview', 300);

                        $data['resultHTML'] = $table->generate();
                        $data['reportTitle'] = 'ATI-wise Overview';
                    }

                    $data['reportType'] = $reportType;

                    return view('header_view')
                        . view('report_result', $data)
                        . view('footer_view');
                }
            } else {
                return $this->response->redirect(base_url('/'));
            }
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
            // return view('header_view') . view('error_general').view('footer_view');
        }

    }

    public function downloadExcel()
    {
        try {
            helper('session');
            if (session_exists()) {


                $session = \Config\Services::session();

                helper('array');
                $query_param = current_url(true)->getQuery();
                $filtered = explode('=', $query_param)[1];

                if ($filtered == 'false')
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
                // header("Content-Type: text/csv");
                header("Content-Disposition: attachment; filename=" . $fileName);
                // header("Cache-Control: max-age=0");
                // ob_end_clean();

                $writer = new Xls($spreadsheet);
                $writer->save('php://output');

                die;
            } else {
                return $this->response->redirect(base_url('/'));
            }
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
            // return view('header_view') . view('error_general').view('footer_view');
        }


    }



}