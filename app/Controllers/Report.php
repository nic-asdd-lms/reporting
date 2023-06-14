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
use App\Models\CourseCompetencyModel;

use PHPExcel_IOFactory;
use PHPExcel_Reader_HTML;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use CodeIgniter\Config\Services;
use DateTime;

class Report extends BaseController
{
    var $resultArray;
    protected $pager;

    public function __construct()
    {
        $this->pager = Services::pager();
    }

    /*
    Function getReport() - perform database query based on report type

    Each query is performed 3 times :
        1.  $result => parameters - ($limit, $offset, $search, $orderBy, $orderDir)
            perform query with given limit, offset, search, order

        2.  $fullResult => parameters - (limit = -1, offset = 0, search = '', $orderBy, $orderDir);
            query to get total no. of rows - to be shown below the table
        
        3.  $resultFiltered => parameters - (limit = -1, offset = 0, $search, $orderBy, $orderDir); 
            query to get count of filtered results when searched with key <$search>; Count will be shown below the table
    */

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
                $competencyModel = new CourseCompetencyModel();

                $reportType = $this->request->uri->getSegments()[1];
                $data['error'] = '';
                $session->setTempdata('error', '');
                $course = $session->getTempdata('course');
                $topCount = $session->getTempdata('topCount');
                $month = $session->getTempdata('monthYear');
                $competencyType = $session->getTempdata('competencyType');

                // Set report-specific inputs

                if ($reportType == 'ministryUserList' || $reportType == 'ministryUserEnrolment' || $reportType == 'orgHierarchy') {
                    if ($session->get('role') == 'MDO_ADMIN') {
                        $ministry = $session->get('organisation');
                    } else {
                        $ministry = $session->getTempdata('org');

                    }
                    $ministryName = $org_hierarchy->getMinistryStateName($ministry);

                } else if ($reportType == 'mdoUserList' || $reportType == 'moUserCount' || $reportType == 'mdoUserEnrolment' || $reportType == 'userWiseCount') {
                    if ($session->get('role') == 'SPV_ADMIN' || $session->get('role') == 'IGOT_TEAM_MEMBER') { // SPV_ADMIN => ministry, department, organisation = values selected from dropdown or searchbox
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

                } else if ($reportType == 'roleWiseCount' || $reportType == 'monthWiseMDOAdminCount' || $reportType == 'cbpAdminList' || $reportType == 'mdoAdminList' || $reportType == 'creatorList' || $reportType == 'reviewerList' || $reportType == 'publisherList' || $reportType == 'editorList' || $reportType == 'fracAdminList' || $reportType == 'fracCompetencyMember' || $reportType == 'fracOneList' || $reportType == 'fracTwoList' || $reportType == 'ifuMemberList' || $reportType == 'publicList' || $reportType == 'spvAdminList' || $reportType == 'stateAdminList' || $reportType == 'watMemberList') {
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

                } else if ($reportType == 'userProfile') {
                    $email = $session->getTempdata('email');
                    if ($session->get('role') == 'MDO_ADMIN') {
                        $org = $session->get('organisation');
                        $orgName = $orgModel->getOrgName($org);
                    } else
                        $orgName = '';

                } else if ($reportType == 'userEnrolment') {
                    $userId = $session->getTempdata('userid');
                    if ($session->get('role') == 'MDO_ADMIN')
                        $org = $session->get('organisation');
                    else
                        $org = '';
                } else if ($reportType == 'topCompetency') {
                    $competencyType = $session->getTempdata('competencyType');

                }
                else if ($reportType == 'userEnrolmentSummary') {
                    $orgName  = '';

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

                } else if ($reportType == 'ministryUserList') {
                    $result = $user->getUserByMinistry($ministryName, $limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $user->getUserByMinistry($ministryName, -1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $user->getUserByMinistry($ministryName, -1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'ministryUserEnrolment') {
                    $result = $enrolment->getUserEnrolmentByMinistry($ministryName, $limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $enrolment->getUserEnrolmentByMinistry($ministryName, -1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $enrolment->getUserEnrolmentByMinistry($ministryName, -1, 0, $search, $orderBy, $orderDir);

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

                } else if ($reportType == 'liveCourseList') {
                    $result = $courseModel->getLiveCourseList($limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $courseModel->getLiveCourseList(-1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $courseModel->getLiveCourseList(-1, 0, $search, $orderBy, $orderDir);

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

                } else if ($reportType == 'userProfile') {
                    $result = $user->getProfile($email, $orgName, $limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $user->getProfile($email, $orgName, -1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $user->getProfile($email, $orgName, -1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'userEnrolment') {
                    $result = $enrolment->getUserWiseEnrolmentReport($userId, $org, $limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $enrolment->getUserWiseEnrolmentReport($userId, $org, -1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $enrolment->getUserWiseEnrolmentReport($userId, $org, -1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'userEnrolmentFull') {
                    $result = $enrolment->getUserEnrolmentFull($limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $enrolment->getUserEnrolmentFull(-1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $enrolment->getUserEnrolmentFull(-1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'userEnrolmentSummary') {
                    $result = $enrolment->getUserEnrolmentCountByMDO($orgName, $limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $enrolment->getUserEnrolmentCountByMDO($orgName, -1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $enrolment->getUserEnrolmentCountByMDO($orgName, -1, 0, $search, $orderBy, $orderDir);
                }  else if ($reportType == 'roleWiseCount') {
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

                } else if ($reportType == 'fracOneList') {
                    $result = $user->getFRACL1List($orgName, $limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $user->getFRACL1List($orgName, -1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $user->getFRACL1List($orgName, -1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'fracTwoList') {
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
                    $fullResult = $user->getSPVAdminList($orgName, -1, 0, '', $orderBy, $orderDir);
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

                } else if ($reportType == 'monthWiseCompletion') {
                    $result = $enrolment->getMonthWiseCourseCompletion($limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $enrolment->getMonthWiseCourseCompletion(-1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $enrolment->getMonthWiseCourseCompletion(-1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'atiWiseOverview') {
                    $result = $enrolmentProgram->getATIWiseCount($org, $limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $enrolmentProgram->getATIWiseCount($org, -1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $enrolmentProgram->getATIWiseCount($org, -1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'topUserEnrolment') {
                    $result = $enrolment->getTopUserEnrolment($topCount, $limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $enrolment->getTopUserEnrolment($topCount, -1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $enrolment->getTopUserEnrolment($topCount, -1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'topUserCompletion') {
                    $result = $enrolment->getTopUserCompletion($topCount, $limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $enrolment->getTopUserCompletion($topCount, -1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $enrolment->getTopUserCompletion($topCount, -1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'topUserNotStarted') {
                    $result = $enrolment->getTopUserNotStarted($topCount, $limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $enrolment->getTopUserNotStarted($topCount, -1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $enrolment->getTopUserNotStarted($topCount, -1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'topUserInProgress') {
                    $result = $enrolment->getTopUserInProgress($topCount, $limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $enrolment->getTopUserInProgress($topCount, -1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $enrolment->getTopUserInProgress($topCount, -1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'topOrgOnboarding') {
                    $result = $user->getTopOrgOnboarding($topCount, $limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $user->getTopOrgOnboarding($topCount, -1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $user->getTopOrgOnboarding($topCount, -1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'topOrgEnrolment') {
                    $result = $enrolment->getTopOrgEnrolment($topCount, $limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $enrolment->getTopOrgEnrolment($topCount, -1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $enrolment->getTopOrgEnrolment($topCount, -1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'topOrgCompletion') {
                    $result = $enrolment->getTopOrgCompletion($topCount, $limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $enrolment->getTopOrgCompletion($topCount, -1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $enrolment->getTopOrgCompletion($topCount, -1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'topOrgMdoAdmin') {
                    $result = $user->getTopOrgMdoAdmin($topCount, $limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $user->getTopOrgMdoAdmin($topCount, -1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $user->getTopOrgMdoAdmin($topCount, -1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'topCbpLiveCourses') {
                    $result = $courseModel->getTopCbpLiveCourses($topCount, $limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $courseModel->getTopCbpLiveCourses($topCount, -1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $courseModel->getTopCbpLiveCourses($topCount, -1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'topCbpUnderPublish') {
                    $result = $courseModel->getTopCbpUnderPublish($topCount, $limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $courseModel->getTopCbpUnderPublish($topCount, -1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $courseModel->getTopCbpUnderPublish($topCount, -1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'topCbpUnderReview') {
                    $result = $courseModel->getTopCbpUnderReview($topCount, $limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $courseModel->getTopCbpUnderReview($topCount, -1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $courseModel->getTopCbpUnderReview($topCount, -1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'topCbpDraftCourses') {
                    $result = $courseModel->getTopCbpDraftCourses($topCount, $limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $courseModel->getTopCbpDraftCourses($topCount, -1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $courseModel->getTopCbpDraftCourses($topCount, -1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'topCourseEnrolment') {
                    $result = $enrolment->getTopCourseEnrolment($topCount, $limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $enrolment->getTopCourseEnrolment($topCount, -1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $enrolment->getTopCourseEnrolment($topCount, -1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'topCourseCompletion') {
                    $result = $enrolment->getTopCourseCompletion($topCount, $limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $enrolment->getTopCourseCompletion($topCount, -1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $enrolment->getTopCourseCompletion($topCount, -1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'topCourseRating') {
                    $result = $courseModel->getTopCourseRating($topCount, $limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $courseModel->getTopCourseRating($topCount, -1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $courseModel->getTopCourseRating($topCount, -1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'topOrgCourseWise') {
                    $result = $enrolment->getTopOrgCourseWise($course, $topCount, $limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $enrolment->getTopOrgCourseWise($course, $topCount, -1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $enrolment->getTopOrgCourseWise($course, $topCount, -1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'topOrgProgramWise') {
                    $result = $enrolmentProgram->getTopOrgProgramWise($course, $topCount, $limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $enrolmentProgram->getTopOrgProgramWise($course, $topCount, -1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $enrolmentProgram->getTopOrgProgramWise($course, $topCount, -1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'topOrgCollectionWise') {
                    $result = $enrolment->getTopOrgCollectionWise($course, $topCount, $limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $enrolment->getTopOrgCollectionWise($course, $topCount, -1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $enrolment->getTopOrgCollectionWise($course, $topCount, -1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'topCourseInMonth') {
                    $result = $enrolment->getTopCourseInMonth($month, $topCount, $limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $enrolment->getTopCourseInMonth($month, $topCount, -1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $enrolment->getTopCourseInMonth($month, $topCount, -1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'topCompetency') {
                    $result = $competencyModel->getTopCompetencies($competencyType, $topCount, $limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $competencyModel->getTopCompetencies($competencyType, $topCount, -1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $competencyModel->getTopCompetencies($competencyType, $topCount, -1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'topCoursesCompetencyWise') {
                    $result = $competencyModel->getTopCoursesCompetencyWise($topCount, $limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $competencyModel->getTopCoursesCompetencyWise($topCount, -1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $competencyModel->getTopCoursesCompetencyWise($topCount, -1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'rozgarMelaUserReport') {
                    $result = $enrolment->getRozgarMelaUserEnrolmentReport($limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $enrolment->getRozgarMelaUserEnrolmentReport(-1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $enrolment->getRozgarMelaUserEnrolmentReport(-1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'rozgarMelaReport') {
                    $result = $enrolment->getRozgarMelaReport($limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $enrolment->getRozgarMelaReport(-1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $enrolment->getRozgarMelaReport(-1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'rozgarMelaSummary') {
                    $result = $enrolment->getRozgarMelaSummary($limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $enrolment->getRozgarMelaSummary(-1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $enrolment->getRozgarMelaSummary(-1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'rozgarMelaUserList') {
                    $result = $user->getRozgarMelaUserList($limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $user->getRozgarMelaUserList(-1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $user->getRozgarMelaUserList(-1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'rozgarMelaKpCollection') {
                    $result = $enrolment->getRozgarMelaKpCollectionReport($limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $enrolment->getRozgarMelaKpCollectionReport(-1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $enrolment->getRozgarMelaKpCollectionReport(-1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'rozgarMelaKpProgram') {
                    $result = $enrolmentProgram->getRozgarMelaKpProgramReport($limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $enrolmentProgram->getRozgarMelaKpProgramReport(-1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $enrolmentProgram->getRozgarMelaKpProgramReport(-1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'designationWiseCount') {
                    $result = $user->getDesignationWiseUserCount($limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $user->getDesignationWiseUserCount(-1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $user->getDesignationWiseUserCount(-1, 0, $search, $orderBy, $orderDir);

                } else if ($reportType == 'competencySummary') {
                    $result = $competencyModel->getCompetencySummary($limit, $offset, $search, $orderBy, $orderDir);
                    $fullResult = $competencyModel->getCompetencySummary(-1, 0, '', $orderBy, $orderDir);
                    $resultFiltered = $competencyModel->getCompetencySummary(-1, 0, $search, $orderBy, $orderDir);

                }




                $session->remove('resultArray');
                $session->remove('filteredResultArray');

                $session->setTempdata('resultArray', $fullResult->getResultArray(), 300);
                $session->setTempdata('filteredResultArray', $resultFiltered->getResultArray(), 300);

                if ($fullResult->getNumRows() == 0)
                    $session->setTempdata('error', 'No matching records found', 300);


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


    /*

    Functions getMDOReport()/getCourseReport()/... - separate function for each tab
    
    Based on report type:

        1. Set table header
        2. Set report title
        3. Set excel filename
        4. Generate HTML Table template, including header (data will be generated in getReport())
        5. Set Last Update Time

    */
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

                if ($role == 'SPV_ADMIN' || $role == 'IGOT_TEAM_MEMBER') { // SPV_ADMIN => ministry, department, organisation = values selected from dropdown or searchbox
                    $org = $request->getPost('org');

                } else if ($role == 'MDO_ADMIN') { // MDO_ADMIN => ministry, department, organisation = values from session (MDO of the particular user)

                    $ministry = $session->get('ministry');
                    $dept = $session->get('department');
                    $org = $session->get('organisation');
                }


                /*  For reports 'MDO-wise User List' and 'MDO-wise user Enrolment', only Ministry/State name is mandatory. Set orgName accordingly.
                If org is not selected, org = dept. If dept is also not selected, org = ministry */

                if ($org != "") {
                    if ($reportType == 'ministryUserList' || $reportType == 'orgHierarchy' || $reportType == 'ministryUserEnrolment')
                        $orgName = $org_hierarchy->getMinistryStateName($org);
                    else
                        $orgName = $orgModel->getOrgName($org);

                }


                /* Set table header, filename and report tilte based on report type */

                if ($reportType == 'mdoUserList') {
                    $header = ['Name', 'Email ID', 'Organisation', 'Designation',  'Created Date', 'Roles', 'Profile Update Status'];
                    $session->setTempdata('fileName', $orgName . '_UserList', 300);
                    $reportTitle = 'Users onboarded from organisation - "' . $orgName . '"';

                } else if ($reportType == 'mdoUserCount') {
                    $header = ['Organisation', 'User Count'];
                    $session->setTempdata('fileName', 'MDOWiseUserCount', 300);
                    $reportTitle = 'MDO-wise user count ';

                } else if ($reportType == 'mdoUserEnrolment') {
                    $header = ['Name', 'Email ID', 'Organisation', 'Designation', 'Course', 'Status', 'Completion Percentage', 'Completed On'];
                    $session->setTempdata('fileName', $orgName . '_UserEnrolmentReport', 300);
                    $reportTitle = 'Users Enrolment Report for organisation - "' . $orgName . '"';

                } else if ($reportType == 'ministryUserList') {
                    $header = ['Name', 'Email', 'Ministry', 'Department', 'Organization', 'Designation',  'Created Date', 'Roles'];
                    $session->setTempdata('fileName', $orgName . '_UserList', 300);
                    $reportTitle = 'Users list for all organisations under ministry/state - "' . $orgName . '"';

                } else if ($reportType == 'ministryUserEnrolment') {
                    $header = ['Name', 'Email', 'Ministry', 'Department', 'Organization', 'Designation', 'Course', 'Status', 'Completion Percentage', 'Completed On'];
                    $session->setTempdata('fileName', $orgName . '_UserEnrolment', 300);
                    $reportTitle = 'Users enrolement report for all organisations under ministry/state - "' . $orgName . '"';

                } else if ($reportType == 'userWiseCount') {
                    $header = ['Name', 'Email ID', 'Organisation', 'Designation', 'Enrolled','Not started',' In Progress', 'Completed'];
                    $session->setTempdata('fileName', $orgName . '_UserWiseSummary', 300);
                    $reportTitle = 'User-wise course enrolment/completion count for organisation - "' . $orgName . '"';

                } else if ($reportType == 'orgList') {
                    $header = ['Organisation'];
                    $session->setTempdata('fileName', 'OrgList', 300);
                    $reportTitle = 'Organisations Onboarded';

                } else if ($reportType == 'orgHierarchy') {
                    $header = ['Department', 'Organisation'];
                    $session->setTempdata('fileName', $orgName . '_Hierarchy', 300);
                    $reportTitle = 'Organisation Hierarchy - "' . $orgName . '"';

                }

                $table->setHeading($header);

                $session->setTempdata('reportHeader', $header);
                $session->setTempdata('reportTitle', $reportTitle);

                $data['reportTitle'] = $reportTitle;
                $data['resultHTML'] = $table->generate();
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
                if ($courseReportType == 'liveCourseList') {
                    $header = ['Course Name'];
                    $session->setTempdata('fileName', 'LiveCourseList', 300);
                    $reportTitle = 'Live Courses';

                } else if ($courseReportType == 'liveCourses') {
                    $header = ['Course Name', 'Course Provider', 'Duration (HH:MM:SS)', 'Published Date', 'No. of Ratings', 'Average Rating'];
                    $session->setTempdata('fileName', 'LiveCourses', 300);
                    $reportTitle = 'Live Courses';

                } else if ($courseReportType == 'underPublishCourses') {
                    $header = ['Course Name', 'Course Provider'];
                    $session->setTempdata('fileName', 'CoursesUnderPublish', 300);
                    $reportTitle = 'Courses under Publish';

                } else if ($courseReportType == 'underReviewCourses') {
                    $header = ['Course Name', 'Course Provider'];
                    $session->setTempdata('fileName', 'CoursesUnderReview', 300);
                    $reportTitle = 'Courses under review';

                } else if ($courseReportType == 'draftCourses') {
                    $header = ['Course Name', 'Course Provider'];
                    $session->setTempdata('fileName', 'DraftCourses', 300);
                    $reportTitle = 'Courses in draft';

                } else if ($courseReportType == 'courseEnrolmentReport') {
                    $header = ['Name', 'Email ID', 'Organisation', 'Designation', 'Course Name', 'Course Provider', 'Completion Status', 'Completion Percentage', 'Completed On'];
                    $session->setTempdata('fileName', $course . '_EnrolmentReport', 300);
                    $reportTitle = 'User Enrolment Report for Course - "' . $home->getCourseName($course) . '"';

                } else if ($courseReportType == 'courseEnrolmentCount') {
                    $header = ['Course Name', 'Course Provider', 'Published Date', 'Duration (HH:MM:SS)', 'Enrolled', 'Not Started', 'In Progress', 'Completed', 'Average Rating'];
                    $session->setTempdata('fileName', 'Course-wiseSummary', 300);
                    $reportTitle = 'Course-wise  Summary';

                } else if ($courseReportType == 'programEnrolmentReport') {
                    $header = ['Name', 'Email ID', 'Organisation', 'Designation', 'Batch ID', 'Status', 'Completed On'];
                    $session->setTempdata('fileName', $course . '_EnrolmentReport', 300);
                    $reportTitle = 'User Enrolment Report for Program - "' . $home->getProgramName($course) . '"';

                } else if ($courseReportType == 'programEnrolmentCount') {
                    $header = ['Program Name', 'Batch ID', 'Enrollment Count', 'Completion Count'];
                    $session->setTempdata('fileName', 'Program-wiseSummary', 300);
                    $reportTitle = 'Program-wise Summary';

                } else if ($courseReportType == 'collectionEnrolmentReport') {
                    $header = ['Name', 'Email ID', 'Organisation', 'Designation', 'Course Name', 'Status', 'Completion Percentage', 'Completed On'];
                    $session->setTempdata('fileName', $course . '_EnrolmentReport', 300);
                    $reportTitle = 'User Enrolment Report for Curated Collection - "' . $home->getCollectionName($course) . '"';

                } else if ($courseReportType == 'collectionEnrolmentCount') {
                    $header = ['Course Name', 'Enrolled', 'Not Started', 'In Progress', 'Completed'];
                    $session->setTempdata('fileName', $course . '_Summary', 300);
                    $reportTitle = 'Curated collection summary - "' . $home->getCollectionName($course) . '"';

                } else if ($courseReportType == 'cbpProviderWiseCourseCount') {
                    $header = ['CBP Provider Name', 'Course Count'];
                    $session->setTempdata('fileName', 'CBPProviderSummary', 300);
                    $reportTitle = 'CBP Provider-wise course count';

                } else if ($courseReportType == 'courseMinistrySummary') {
                    $header = ['Ministry/State Name', 'Enrolled', 'Not Started', 'In Progress', 'Completed'];
                    $session->setTempdata('fileName', $course . '_MinistrySummary', 300);
                    $reportTitle = 'Ministry-wise Summary for course - "' . $home->getCourseName($course) . '"';

                }
                $table->setHeading($header);

                $session->setTempdata('reportHeader', $header);
                $session->setTempdata('reportTitle', $reportTitle);

                $data['reportTitle'] = $reportTitle;
                $data['resultHTML'] = $table->generate();
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

    public function getUserReport()
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
                $reportType = $request->getPost('userReportType') ? $request->getPost('userReportType') : $segments[1];

                $session->setTempdata('reportType', $request->getPost('userReportType'), 600);
                $session->setTempdata('email', $request->getPost('email'), 300);
                $session->setTempdata('userid', $request->getPost('userid'), 300);

                $role = $session->get('role');
                $reportType = $request->getPost('userReportType');
                $email = $request->getPost('email');

                $lastUpdate = new DataUpdateModel();

                $data['lastUpdated'] = '[Report as on ' . $lastUpdate->getReportLastUpdatedTime() . ']';
                $session->setTempdata('error', '');

                if ($reportType == 'userList') {
                    $header = ['Name', 'Email ID', 'Organisation', 'Designation',  'Created Date', 'Roles', 'Profile Update Status'];
                    $session->setTempdata('fileName', 'UserList', 300);
                    $reportTitle = 'Users onboarded on iGOT';

                } else if ($reportType == 'userProfile') {
                    $header = ['Name', 'Email', 'Organisation', 'Designation', 'Created Date', 'Roles', 'Profile Update Status'];
                    $session->setTempdata('fileName', 'UserProfile_' . $email, 300);
                    $reportTitle = 'User Profile of - "' . $email . '"';

                } else if ($reportType == 'userEnrolment') {
                    $header = ['Course Name', 'Course Provider', 'Completion Status', 'Completion Percentage', 'Completed On'];
                    $session->setTempdata('fileName', 'UserEnrolment_' . $email, 300);
                    $reportTitle = 'Enrolment report of - "' . $email . '"';
                } else if ($reportType == 'userEnrolmentFull') {
                    $header = ['Name', 'Email ID', 'Organisation', 'Designation', 'Course', 'Status', 'Completion Percentage', 'Completed On'];
                    $session->setTempdata('fileName', 'UserEnrolmentFullReport', 300);
                    $reportTitle = 'Full Enrolment Report';

                } else if ($reportType == 'userEnrolmentSummary') {
                    $header = ['Name', 'Email ID', 'Organisation', 'Designation', 'Contact No.','Enrolled','Not started',' In Progress', 'Completed'];
                    $session->setTempdata('fileName',  'UserEnrolmentSummary', 300);
                    $reportTitle = 'User-wise enrolment summary';

                } 
                $table->setHeading($header);

                $session->setTempdata('reportHeader', $header);
                $session->setTempdata('reportTitle', $reportTitle);

                $data['reportTitle'] = $reportTitle;
                $data['resultHTML'] = $table->generate();
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
                if ($role == 'SPV_ADMIN' || $role == 'IGOT_TEAM_MEMBER') {
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
                    $header = ['Role', 'Count'];
                    $session->setTempdata('fileName', 'Role-wise count', 300);
                    $reportTitle = 'Role-wise count';

                } else if ($roleReportType == 'monthWiseMDOAdminCount') {
                    $header = ['Month', 'Count'];
                    $session->setTempdata('fileName', 'Month-wise MDO Admin Creation Count', 300);
                    $reportTitle = 'Month-wise MDO Admin Creation Count';

                } else if ($roleReportType == 'cbpAdminList') {
                    $header = ['Name', 'Email', 'Organization', 'Designation', 'Contact No.', 'Creation Date', 'Roles'];
                    $session->setTempdata('fileName', 'List of CBP Admins', 300);
                    $reportTitle = 'List of CBP Admins';

                } else if ($roleReportType == 'mdoAdminList') {
                    $header = ['Name', 'Email', 'Organization', 'Designation', 'Contact No.', 'Creation Date', 'Roles'];
                    $session->setTempdata('fileName', 'List of MDO Admins', 300);
                    $reportTitle = 'List of MDO Admins';

                } else if ($roleReportType == 'creatorList') {
                    $header = ['Name', 'Email', 'Organization', 'Designation', 'Contact No.', 'Creation Date', 'Roles'];
                    $session->setTempdata('fileName', 'List of Content Creators', 300);
                    $reportTitle = 'List of Content Creators';

                } else if ($roleReportType == 'reviewerList') {
                    $header = ['Name', 'Email', 'Organization', 'Designation', 'Contact No.', 'Creation Date', 'Roles'];
                    $session->setTempdata('fileName', 'List of Content Reviewers', 300);
                    $reportTitle = 'List of Content Reviewers';

                } else if ($roleReportType == 'publisherList') {
                    $header = ['Name', 'Email', 'Organization', 'Designation', 'Contact No.', 'Creation Date', 'Roles'];
                    $session->setTempdata('fileName', 'List of Content Publishers', 300);
                    $reportTitle = 'List of Content Publishers';

                } else if ($roleReportType == 'editorList') {
                    $header = ['Name', 'Email', 'Organization', 'Designation', 'Contact No.', 'Creation Date', 'Roles'];
                    $session->setTempdata('fileName', 'List of Editors', 300);
                    $reportTitle = 'List of Editors';

                } else if ($roleReportType == 'fracAdminList') {
                    $header = ['Name', 'Email', 'Organization', 'Designation', 'Contact No.', 'Creation Date', 'Roles'];
                    $session->setTempdata('fileName', 'List of FRAC Admins', 300);
                    $reportTitle = 'List of FRAC Admins';

                } else if ($roleReportType == 'fracCompetencyMember') {
                    $header = ['Name', 'Email', 'Organization', 'Designation', 'Contact No.', 'Creation Date', 'Roles'];
                    $session->setTempdata('fileName', 'List of FRAC Competency Members', 300);
                    $reportTitle = 'List of FRAC Competency Members';

                } else if ($roleReportType == 'fracOneList') {
                    $header = ['Name', 'Email', 'Organization', 'Designation', 'Contact No.', 'Creation Date', 'Roles'];
                    $session->setTempdata('fileName', 'List of FRAC_Reviewer_L1', 300);
                    $reportTitle = 'List of FRAC_Reviewer_L1';

                } else if ($roleReportType == 'fracTwoList') {
                    $header = ['Name', 'Email', 'Organization', 'Designation', 'Contact No.', 'Creation Date', 'Roles'];
                    $session->setTempdata('fileName', 'List of FRAC_Reviewer_L2', 300);
                    $reportTitle = 'List of FRAC_Reviewer_L2';

                } else if ($roleReportType == 'ifuMemberList') {
                    $header = ['Name', 'Email', 'Organization', 'Designation', 'Contact No.', 'Creation Date', 'Roles'];
                    $session->setTempdata('fileName', 'List of IFU Members', 300);
                    $reportTitle = 'List of IFU Members';

                } else if ($roleReportType == 'publicList') {
                    $header = ['Name', 'Email', 'Organization', 'Designation', 'Contact No.', 'Creation Date', 'Roles'];
                    $session->setTempdata('fileName', 'List of CBP Admins', 300);
                    $reportTitle = 'List of Public users';

                } else if ($roleReportType == 'spvAdminList') {
                    $header = ['Name', 'Email', 'Organization', 'Designation', 'Contact No.', 'Creation Date', 'Roles'];
                    $session->setTempdata('fileName', 'List of SPV Admins', 300);
                    $reportTitle = 'List of SPV Admins';

                } else if ($roleReportType == 'stateAdminList') {
                    $header = ['Name', 'Email', 'Organization', 'Designation', 'Contact No.', 'Creation Date', 'Roles'];
                    $session->setTempdata('fileName', 'List of State Admins', 300);
                    $reportTitle = 'List of State Admins';

                } else if ($roleReportType == 'watMemberList') {
                    $header = ['Name', 'Email', 'Organization', 'Designation', 'Contact No.', 'Creation Date', 'Roles'];
                    $session->setTempdata('fileName', 'List of WAT Members', 300);
                    $reportTitle = 'List of WAT Members';

                }
                $table->setHeading($header);

                $session->setTempdata('reportHeader', $header);
                $session->setTempdata('reportTitle', $reportTitle);

                $data['reportTitle'] = $reportTitle;
                $data['resultHTML'] = $table->generate();
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

                if ($role == 'SPV_ADMIN' || $role == 'IGOT_TEAM_MEMBER') {
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
                    $header = ['Date', 'No. of Users Onboarded'];
                    $session->setTempdata('fileName', 'Day-wise User Onboarding', 300);
                    $reportTitle = 'Day-wise User Onboarding';

                } else if ($analyticsReportType == 'monthWiseUserOnboarding') {
                    $header = ['Month', 'No. of Users Onboarded'];
                    $session->setTempdata('fileName', 'Month-wise User Onboarding', 300);
                    $reportTitle = 'Month-wise User Onboarding';

                } else if ($analyticsReportType == 'monthWiseOrgOnboarding') {
                    $header = ['Month', 'No. of Organisations Onboarded'];
                    $session->setTempdata('fileName', 'Month-wise Org Onboarding', 300);
                    $reportTitle = 'Month-wise Organisation Onboarding';

                } else if ($analyticsReportType == 'monthWiseCourses') {
                    $header = ['Month', 'No. of Courses Published'];
                    $session->setTempdata('fileName', 'Month-wise Courses Published', 300);
                    $reportTitle = 'Month-wise Courses Published';

                } else if ($analyticsReportType == 'monthWiseCompletion') {
                    $header = ['Month', 'No. of Course Completions'];
                    $session->setTempdata('fileName', 'Month-wise Course Completion', 300);
                    $reportTitle = 'Month-wise Course Completion';

                }

                $table->setHeading($header);

                $session->setTempdata('reportHeader', $header);
                $session->setTempdata('reportTitle', $reportTitle);

                $data['reportTitle'] = $reportTitle;
                $data['resultHTML'] = $table->generate();
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

    public function getTopPerformers()
    {
        try {
            helper('session');
            if (session_exists()) {


                $request = service('request');
                $session = \Config\Services::session();

                $table = new \CodeIgniter\View\Table();
                $table->setTemplate($GLOBALS['tableTemplate']);

                $role = $session->get('role');

                $segments = $request->uri->getSegments();
                $reportType = $request->getPost('topReportType') ? $request->getPost('topReportType') : $segments[1];
                $course = $request->getPost('course') ? $request->getPost('course') : $request->getPost('topcourse');
                $topCount = $request->getPost('topCount');
                $month = $request->getPost('month');
                $competencyType = $request->getPost('competencyType') == 'All' ? '' : $request->getPost('competencyType');

                $year = $request->getPost('year');
                $dateObj = DateTime::createFromFormat('m', $month);

                $monthName = $dateObj->format('F');


                $session->setTempdata('reportType', $request->getPost('topReportType'), 600);
                $session->setTempdata('course', $request->getPost('topcourse'), 300);
                $session->setTempdata('topCount', $request->getPost('topCount'), 300);
                $session->setTempdata('monthYear', $request->getPost('year') . '/' . $request->getPost('month'), 300);
                $session->setTempdata('competencyType', $request->getPost('competencyType'), 300);

                $home = new Home();
                $user = new MasterUserModel();
                $courseModel = new MasterCourseModel();
                $lastUpdate = new DataUpdateModel();
                $programModel = new MasterProgramModel();
                $collectionModel = new MasterCollectionModel();

                $data['lastUpdated'] = '[Report as on ' . $lastUpdate->getReportLastUpdatedTime() . ']';
                $session->setTempdata('error', '');


                if ($reportType == 'topUserEnrolment') {
                    $header = ['Name', 'Email ID', 'Organisation', 'No. of Courses Enrolled'];
                    $session->setTempdata('fileName', 'TopUsers_Enrolment', 300);
                    $reportTitle = 'Top ' . $topCount . ' users based on enrolment in courses';

                } else if ($reportType == 'topUserCompletion') {
                    $header = ['Name', 'Email ID', 'Organisation', 'No. of Courses Completed'];
                    $session->setTempdata('fileName', 'TopUsers_Completion', 300);
                    $reportTitle = 'Top ' . $topCount . ' users based on course completion';

                } else if ($reportType == 'topUserNotStarted') {
                    $header = ['Name', 'Email ID', 'Organisation', 'No. of Courses Enrolled and Not Started'];
                    $session->setTempdata('fileName', 'TopUsers_NotStarted', 300);
                    $reportTitle = 'Users with highest no. of courses enrolled and not started';

                } else if ($reportType == 'topUserInProgress') {
                    $header = ['Name', 'Email ID', 'Organisation', 'No. of Courses In Progress'];
                    $session->setTempdata('fileName', 'TopUsers_InProgress', 300);
                    $reportTitle = 'Users with highest no. of courses in progress';

                } else if ($reportType == 'topOrgOnboarding') {
                    $header = ['Organisation', 'No. of Users Onboarded'];
                    $session->setTempdata('fileName', 'TopOrg_Onboarding', 300);
                    $reportTitle = 'Top ' . $topCount . ' organisations based on user onboarding';

                } else if ($reportType == 'topOrgEnrolment') {
                    $header = ['Organisation', 'No. of Course Enrolments'];
                    $session->setTempdata('fileName', 'TopOrg_Enrolment', 300);
                    $reportTitle = 'Top ' . $topCount . ' organisations based on course enrolment';

                } else if ($reportType == 'topOrgCompletion') {
                    $header = ['Organisation', 'No. of Course Completions'];
                    $session->setTempdata('fileName', 'TopOrg_Completion', 300);
                    $reportTitle = 'Top ' . $topCount . ' organisations based on course completion';

                } else if ($reportType == 'topOrgMdoAdmin') {
                    $header = ['Organisation', 'No. of MDO Admins'];
                    $session->setTempdata('fileName', 'TopOrg_MDOAdmin', 300);
                    $reportTitle = 'Top ' . $topCount . ' organisations based on MDO Admin count';

                } else if ($reportType == 'topCbpLiveCourses') {
                    $header = ['CBP Provider', 'No. of Courses Published'];
                    $session->setTempdata('fileName', 'TopCBP_LiveCourses', 300);
                    $reportTitle = 'Top ' . $topCount . ' Course Publishers';

                } else if ($reportType == 'topCbpUnderPublish') {
                    $header = ['CBP Provider', 'No. of Courses  Under Publish'];
                    $session->setTempdata('fileName', 'TopCBP_UnderPublish', 300);
                    $reportTitle = 'CBP Providers with highest no. of courses under publish';

                } else if ($reportType == 'topCbpUnderReview') {
                    $header = ['CBP Provider', 'No. of Courses  Under Review'];
                    $session->setTempdata('fileName', 'TopCBP_UnderReview', 300);
                    $reportTitle = 'CBP Providers with highest no. of courses in progress';

                } else if ($reportType == 'topCbpDraftCourses') {
                    $header = ['CBP Provider', 'No. of Courses In Draft'];
                    $session->setTempdata('fileName', 'TopCBP_DraftCourses', 300);
                    $reportTitle = 'CBP Providers with highest no. of draft courses';

                } else if ($reportType == 'topCourseEnrolment') {
                    $header = ['Course', 'No. of Users Enrolled'];
                    $session->setTempdata('fileName', 'TopCourse_Enrolment', 300);
                    $reportTitle = 'Top ' . $topCount . ' courses based on enrolment';

                } else if ($reportType == 'topCourseCompletion') {
                    $header = ['Course', 'No. of Users Completed'];
                    $session->setTempdata('fileName', 'TopCourse_Completion', 300);
                    $reportTitle = 'Top ' . $topCount . ' courses based on completion';

                } else if ($reportType == 'topCourseRating') {
                    $header = ['Course', 'Average Rating', 'No. of Ratings'];
                    $session->setTempdata('fileName', 'TopCourse_Rating', 300);
                    $reportTitle = 'Top ' . $topCount . ' courses based on rating';

                } else if ($reportType == 'topOrgCourseWise') {
                    $header = ['Organisation', 'No. of Users Completed'];
                    $session->setTempdata('fileName', 'TopOrg_' . $course, 300);
                    $reportTitle = 'Top ' . $topCount . ' organisations based on completion of course - "' . $courseModel->getCourseName($course) . '"';

                } else if ($reportType == 'topOrgProgramWise') {
                    $header = ['Organisation', 'No. of Users Completed'];
                    $session->setTempdata('fileName', 'TopOrg_' . $course, 300);
                    $reportTitle = 'Top ' . $topCount . ' organisations based on completion of program - "' . $programModel->getProgramName($course) . '"';

                } else if ($reportType == 'topOrgCollectionWise') {
                    $header = ['Organisation', 'No. of Users Completed'];
                    $session->setTempdata('fileName', 'TopOrg_' . $course, 300);
                    $reportTitle = 'Top ' . $topCount . ' organisations based on completion of curated collection - "' . $collectionModel->getCollectionName($course) . '"';

                } else if ($reportType == 'topCourseInMonth') {
                    $header = ['Course', 'Completion Count'];
                    $session->setTempdata('fileName', 'TopCourse_' . $month . '_' . $year, 300);
                    $reportTitle = 'Top ' . $topCount . ' Courses of ' . $monthName . ', ' . $year . ' based on Completion';

                } else if ($reportType == 'topCoursesCompetencyWise') {
                    $header = ['Course', 'Competency Count'];
                    $session->setTempdata('fileName', 'TopCourse_Competency', 300);
                    $reportTitle = 'Top ' . $topCount . ' Courses based on Competencies tagged';

                } else if ($reportType == 'topCompetency') {
                    $header = ['Competency', 'No. of Courses Tagegd'];
                    $session->setTempdata('fileName', 'TopCompetencies', 300);
                    $reportTitle = 'Top ' . $topCount . $competencyType . ' Competencies';

                }

                $table->setHeading($header);

                $session->setTempdata('reportHeader', $header);
                $session->setTempdata('reportTitle', $reportTitle);

                $data['reportTitle'] = $reportTitle;
                $data['resultHTML'] = $table->generate();
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

    public function getMiscReport()
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
                $reportType = $request->getPost('miscReportType') ? $request->getPost('miscReportType') : $segments[1];


                $session->setTempdata('reportType', $request->getPost('miscReportType'), 600);
                $session->setTempdata('course', $request->getPost('course'), 300);

                $role = $session->get('role');
                $reportType = $request->getPost('miscReportType');
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
                if ($reportType == 'rozgarMelaReport') {
                    $header = ['Organisation Name', 'No. of Rozgar Mela Users', 'Karmayogi Prarambh Course Enrolments', 'Not Started', 'In Progress', 'Completed'];
                    $session->setTempdata('fileName', 'RozgarMelaReport', 300);
                    $reportTitle = 'Organisation-wise Enrolment Summary of Rozgar Mela Users in Karmayogi Prarambh Module';

                } else if ($reportType == 'rozgarMelaSummary') {
                    $header = ['Course Name', 'Enrolled', 'Not Started', 'In Progress', 'Completed'];
                    $session->setTempdata('fileName', 'RozgarMelaSummary', 300);
                    $reportTitle = 'Course-wise Enrolment Summary of Rozgar Mela Users in Karmayogi Prarambh Module';

                } else if ($reportType == 'rozgarMelaUserReport') {
                    $header = ['Name', 'Email', 'Organisation', 'Designation', 'Enrolled', 'Not Started', 'In Progress', 'Completed'];
                    $session->setTempdata('fileName', 'RozgarMelaSummary', 300);
                    $reportTitle = 'Rozgar Mela user Enrolment Report';

                } else if ($reportType == 'rozgarMelaUserList') {
                    $header = ['Name', 'Email', 'Organisation', 'Designation'];
                    $session->setTempdata('fileName', 'RozgarMelaUserList', 300);
                    $reportTitle = 'Rozgar Mela User List';

                } else if ($reportType == 'rozgarMelaKpCollection') {
                    $header = ['Name', 'Email', 'Organisation', 'Designation', 'Enrolled', 'Not Started', 'In Progress', 'Completed'];
                    $session->setTempdata('fileName', 'RozgarMelaUsers_KarmayogiPrarambhCollectionEnrolmentReport', 300);
                    $reportTitle = 'Rozgar Mela Users Enrolled in Karmayogi Prarambh Module';

                } else if ($reportType == 'rozgarMelaKpProgram') {
                    $header = ['Name', 'Email', 'Organisation', 'Designation', 'Batch ID', 'Status', 'Completed On'];
                    $session->setTempdata('fileName', 'RozgarMelaUsers_KarmayogiPrarambhProgramEnrolmentReport', 300);
                    $reportTitle = 'Rozgar Mela User Enrolled in Karmayogi Prarambh Program';

                } else if ($reportType == 'designationWiseCount') {
                    $header = ['Designation', 'User Count'];
                    $session->setTempdata('fileName', 'DesignationWiseUserCount', 300);
                    $reportTitle = 'Designation-wise User Count';

                } else if ($reportType == 'competencySummary') {
                    $header = ['Competency', 'Competency Type', 'Courses tagged'];
                    $session->setTempdata('fileName', 'CompetencySummary', 300);
                    $reportTitle = 'Competency Summary';

                }
                $table->setHeading($header);

                $session->setTempdata('reportHeader', $header);
                $session->setTempdata('reportTitle', $reportTitle);

                $data['reportTitle'] = $reportTitle;
                $data['resultHTML'] = $table->generate();
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
                $doptReportType = $request->getPost('doptReportType');

                $ati = $request->getPost('ati');
                if ($reportType == 'atiWiseOverview') {
                    $header = ['Program Name', 'Batch ID', 'Institute', 'Enrolled', 'Not Started', 'In Progress', 'Completed'];
                    $session->setTempdata('fileName', 'ATI-wise Overview', 300);
                    $reportTitle = 'ATI-wise Overview';
                }

                $table->setHeading($header);

                $session->setTempdata('reportHeader', $header);

                $data['resultHTML'] = $table->generate();
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

    /*
    Function downloadExcel() - generate and download Excel file

        1.  Data will be in :
            a.  Temporary session variable 'resultArray' for full report download
            b.  Temporary session variable 'filteredResultArray' for filtered report download
        2.  Filename will be in temporary session variable 'fileName'
    */

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

                $spreadsheet = new Spreadsheet();

                $styleArray = [
                    'font' => [
                        'bold' => true,
                        'size' => 15,
                        'name' => 'Verdana'
                    ],
                    'alignment' => [
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'd5d5d5',
                        ],
                        'endColor' => [
                            'argb' => 'd5d5d5',
                        ],
                    ],
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                            'color' => [
                                'argb' => '000000',
                            ],
                        ]

                    ],
                ];

                $keys = $session->getTempdata('reportHeader');

                $noOfColumns = count($keys);
                $titleColumnsToMerge = ord('A') + ($noOfColumns - 1);
                $titleColumnsToMerge = chr($titleColumnsToMerge);

                $sheet = $spreadsheet->getActiveSheet();

                $spreadsheet->getDefaultStyle()->getFont()->setSize(11);
                $spreadsheet->getDefaultStyle()->getFont()->setName('Verdana');

                $sheet->mergeCells('A1:' . $titleColumnsToMerge . '1');
                $sheet->getStyle('A1:' . $titleColumnsToMerge . '1')->applyFromArray($styleArray);
                $sheet->setCellValue('A1', $session->getTempdata('reportTitle'));
                $sheet->getRowDimension('1')->setRowHeight(30);
                $sheet->getRowDimension('2')->setRowHeight(25);


                //  Write Header

                $column = 'A';
                foreach ($keys as $key) {
                    $sheet->setCellValue($column . '2', $key);
                    $sheet->getStyle($column . '2')->getFont()->setName('Verdana');
                    $sheet->getStyle($column . '2')->getFont()->setSize(11);
                    $sheet->getStyle($column . '2')->getFont()->setBold(true);
                    $sheet->getColumnDimension($column)->setWidth(20);
                    $column++;
                }


                //  Write data

                $rows = 3;

                foreach ($report as $row) {
                    $column = 'A';
                    foreach ($row as $key => $val) {
                        $sheet->setCellValue($column . $rows, $val);
                        $column++;
                        // print_r($val);

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

    public function getCsvReport()
    {
        try {
            helper('session');
            if (session_exists()) {

                $session = \Config\Services::session();

                helper('array');

                $keys = $session->getTempdata('reportHeader');
                $report = $session->getTempdata('resultArray');
                $fileName = $session->getTempdata('fileName') . '.csv';

                // $data[] = array('x'=> $x, 'y'=> $y, 'z'=> $z, 'a'=> $a);
                header("Content-type: application/csv");
                header("Content-Disposition: attachment; filename=" . $fileName);
                header("Pragma: no-cache");
                header("Expires: 0");

                $handle = fopen('php://output', 'w');
fputcsv($handle, $keys);
                foreach ($report as $data_array) {
                    fputcsv($handle, $data_array);
                }
                fclose($handle);
                exit;
            }
        }
        catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
            // return view('header_view') . view('error_general').view('footer_view');
        }
    }
}