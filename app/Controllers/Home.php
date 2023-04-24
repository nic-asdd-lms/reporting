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


        } catch (\Exception $e) {
            return view('header_view') . view('error_general') . view('footer_view');
        }

    }

    public function action()
    {
        try {


            if ($this->request->getVar('action')) {
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
                    //Search query.
                    $orgModel = new MasterOrganizationModel();
                    $orgdata = $orgModel->searchOrg($search_key);

                    //Query execution

                    //Creating unordered list to display result.
                }
            }
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    

    public function getCourseName($course_id)
    {
        try {
            $course = new MasterCourseModel();

            $course_name = $course->getCourseName($course_id);
            return $course_name;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }


    }
    public function getCollectionName($collection_id)
    {
        try {
            $collection = new MasterCollectionModel();

            $collection_name = $collection->getCollectionName($collection_id);
            return $collection_name;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function getProgramName($program_id)
    {
        try {
            $program = new MasterProgramModel();

            $program_name = $program->getProgramName($program_id);
            return $program_name;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }
    public function getOrgName($org_id)
    {
        try {
            $org = new MasterOrganizationModel();

            $org_name = $org->getOrgName($org_id);
            return $org_name;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function orgSearch($search_key)
    {
        try {
            if (isset($_POST['submit'])) {
                $skill = $_POST['org_search'];
                echo 'Selected Skill: ' . $skill;
            }
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }



    }

    public function getMDOReportTypes()
    {
        try {
            $options = array();
            $options['mdoUserList'] = 'MDO-wise user list';
            $options['mdoUserCount'] = 'MDO-wise user count';
            $options['mdoAdminList'] = 'MDO Admin list';
            $options['mdoUserEnrolment'] = 'MDO-wise user enrolment report';
            $options['ministryUserEnrolment'] = 'User list for all organisations under a ministry';

            return $options;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }


    }


    public function search()
    {
        try {

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
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function getExcel()
    {
        try {

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }


    }

}