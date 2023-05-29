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
use App\Models\DataUpdateModel;

use PHPExcel_IOFactory;
use PHPExcel_Reader_HTML;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class Home extends BaseController
{


    public function index()
    {
        try {
            helper('session');
            if (session_exists()) {

                helper(['form', 'url']);
                
                $data['error'] = '';
                return view('header_view')
                    . view('report_home', $data)
                    . view('footer_view');
            } else {
                return $this->response->redirect(base_url('/'));
            }



        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function checkOrgOnboarded()
    {
        helper('session');
        if (session_exists()) {


            echo $this->request->getVar('org');
            // die ; 
            $orgModel = new MasterOrganizationModel();
            $orgdata = $orgModel->getOrgName($this->request->getVar('org'));
            echo "<pre>org";
            if (isset($orgdata)) {
                $response = "{'status':'1','orgdata':" . $orgdata->org_name . "}";
            } else {
                $response = "{'status':'0','orgdata':''}";
            }
            echo "working " . $response;
            echo json_encode($response);
        } else {
            return $this->response->redirect(base_url('/'));
        }
    }
    public function action()
    {
        try {

            helper('session');
            if (session_exists()) {

                if ($this->request->getVar('action')) {
                    $session = \Config\Services::session();
                
                    $action = $this->request->getVar('action');

                    if ($action == 'get_ministry') {
                        $ministryModel = new MasterStructureModel();
                        if ($this->request->getVar('ms') == 'ministry') {
                            $msdata = $ministryModel->getMinistry();
                        } else if ($this->request->getVar('ms') == 'state') {
                            $msdata = $ministryModel->getState();
                        }
                        echo json_encode($msdata);
                    } else if ($action == 'get_dept') {
                        $deptModel = new MasterStructureModel();
                        $deptdata = $deptModel->getDepartment($this->request->getVar('ministry'));

                        echo json_encode($deptdata);
                    } else if ($action == 'get_org') {
                        $orgModel = new MasterStructureModel();
                        $orgdata = $orgModel->getOrganisation($this->request->getVar('dept'));

                        echo json_encode($orgdata);
                    } else if ($action == 'get_orgname') {
                        $orgModel = new MasterOrganizationModel();
                        $orgdata = $orgModel->getOrgName($this->request->getVar('org'));

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
                    } else if ($action == 'org_search') {
                        $search_key = $this->request->getVar('search_key');
                        $reportType = $this->request->getVar('reportType');
                        $orgModel = new MasterOrganizationModel();
                        $ministryModel = new MasterStructureModel();
                        
                        if ($reportType == 'ministryUserEnrolment' || $reportType == 'orgHierarchy')
                            $orgdata = $ministryModel->getMinistry();
                        else
                            $orgdata = $orgModel->getOrganizations();

                        echo json_encode($orgdata);
                    } else if ($action == 'course_search') {
                        $search_key =  $this->request->getVar('search_key')  ;
                        $courseModel = new MasterCourseModel();
                        $programModel = new MasterProgramModel();
                        $collectionModel = new MasterCollectionModel();
                        $reportType = $this->request->getVar('reportType')  ;

                        if ($reportType == 'courseEnrolmentReport' || $reportType == 'courseMinistrySummary' || $reportType == 'topOrgCourseWise' )
                            $courseData = $courseModel->courseSearch($search_key);
                        else if ($reportType == 'programEnrolmentReport'|| $reportType == 'topOrgProgramWise')
                            $courseData = $programModel->programSearch($search_key);
                        else if ($reportType == 'collectionEnrolmentReport' || $reportType == 'collectionEnrolmentCount'|| $reportType == 'topOrgCollectionWise')
                            $courseData = $collectionModel->collectionSearch($search_key);

                        echo json_encode($courseData);
                    } else if ($action == 'get_hierarchy') {
                        $orgModel = new MasterStructureModel();
                        $orgdata = $orgModel->getMDOHierarchy($this->request->getVar('org'));

                        echo json_encode($orgdata);
                    } else if ($action == 'user_search') {
                        $search_key =  $this->request->getVar('search_key')  ;
                        if($session->get('role') == 'MDO_ADMIN')
                            $org = $session->get('organisation');
                        else   
                            $org = '';
                        
                        $userModel = new MasterUserModel();
                        $userData = $userModel->userSearch($search_key, $org);
                        
                        echo json_encode($userData);
                        
                    }
                }
            } else {
                return $this->response->redirect(base_url('/'));
            }
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }



    public function getCourseName($course_id)
    {
        try {
            helper('session');
            if (session_exists()) {

                $course = new MasterCourseModel();

                $course_name = $course->getCourseName($course_id);
                return $course_name;
            } else {
                return $this->response->redirect(base_url('/'));
            }
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }


    }
    public function getCollectionName($collection_id)
    {
        try {
            helper('session');
            if (session_exists()) {

                $collection = new MasterCollectionModel();

                $collection_name = $collection->getCollectionName($collection_id);
                return $collection_name;
            } else {
                return $this->response->redirect(base_url('/'));
            }
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function getProgramName($program_id)
    {
        try {
            helper('session');
            if (session_exists()) {

                $program = new MasterProgramModel();

                $program_name = $program->getProgramName($program_id);
                return $program_name;
            } else {
                return $this->response->redirect(base_url('/'));
            }
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }
    public function getOrgName($org_id)
    {
        try {
            helper('session');
            if (session_exists()) {

                $org = new MasterOrganizationModel();

                $org_name = $org->getOrgName($org_id);
                return $org_name;
            } else {
                return $this->response->redirect(base_url('/'));
            }
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function orgSearch($search_key)
    {
        try {
            helper('session');
            if (session_exists()) {

                if (isset($_POST['submit'])) {
                    $skill = $_POST['org_search'];
                    echo 'Selected Skill: ' . $skill;
                }
            } else {
                return $this->response->redirect(base_url('/'));
            }
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }



    }

    public function getMDOReportTypes()
    {
        try {
            helper('session');
            if (session_exists()) {

                $options = array();
                $options['mdoUserList'] = 'MDO-wise user list';
                $options['mdoUserCount'] = 'MDO-wise user count';
                $options['mdoAdminList'] = 'MDO Admin list';
                $options['mdoUserEnrolment'] = 'MDO-wise user enrolment report';
                $options['ministryUserEnrolment'] = 'User list for all organisations under a ministry';

                return $options;
            } else {
                return $this->response->redirect(base_url('/'));
            }
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }


    }


    public function search()
    {
        try {

            helper('session');
            if (session_exists()) {

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

            } else {
                return $this->response->redirect(base_url('/'));
            }
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function getExcel()
    {
        try {
            helper('session');
            if (session_exists()) {

            } else {
                return $this->response->redirect(base_url('/'));
            }
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }


    }


    public function result()
    {
        helper('session');
        if (session_exists()) {

            $request = service('request');

            $lastUpdate = new DataUpdateModel();
            $data['mdoReportType'] = $request->getPost('mdoReportType');
            $data['lastUpdated'] = '[Report as on ' . $lastUpdate->getReportLastUpdatedTime() . ']';
            $data['reportTitle'] = 'MDO-wise user count ';
            return view('header_view')
                . view('report_result', $data)
                . view('footer_view');
        } else {
            return $this->response->redirect(base_url('/'));
        }
    }
}