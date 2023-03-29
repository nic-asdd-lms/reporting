<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\MasterStructureModel;
use App\Models\MasterCourseModel;
use App\Models\UserEnrolmentCourse;


class Home extends BaseController
{
    public function index()
    {
        helper(['form', 'url']);
        $masterStructureModel = new MasterStructureModel();
        $masterCourseModel = new MasterCourseModel();
        $data['ministry'] = $masterStructureModel->getMinistry();
        $data['course']=$masterCourseModel->getCourse();
        return view('report_home', $data);


    }

    public function getDepartment() {

        $masterStructureModel = new MasterStructureModel();

        $postData = array(
            'ms_id' => $this->request->getPost('ms_id'),
        );

        $data =$masterStructureModel->getDepartment($postData);
        echo $postData;
        echo json_encode($data);
    }

    public function getOrganisation() {

        $masterStructureModel = new MasterStructureModel();

        $postData = array(
            'dep_id' => $this->request->getPost('dep_id'),
        );

        $data = $masterStructureModel->getOrganisation($postData);

        echo json_encode($data);
    }

    public function getReport() {
        $request = service('request');
        $courseReportType = $this->input->post('courseReportType');
        echo $courseReportType;
       $postData = $request->getPost();
       
        


       $response = array();
    }
    public function getCourseWiseEnrolmentReport() {
        $request = service('request');
       $postData = $request->getPost();
       $dtpostData = $postData['data'];
       $response = array();

       ## Read value
       $draw = $dtpostData['draw'];
       $start = $dtpostData['start'];
       $rowperpage = $dtpostData['length']; // Rows display per page
       $columnIndex = $dtpostData['order'][0]['column']; // Column index
       $columnName = $dtpostData['columns'][$columnIndex]['data']; // Column name
       $columnSortOrder = $dtpostData['order'][0]['dir']; // asc or desc
       $searchValue = $dtpostData['search']['value']; // Search value

       ## Total number of records without filtering
       $userEnrolment = new UserEnrolmentCourse();
       $totalRecords = $userEnrolment->select('user_id')
                     ->countAllResults();

       ## Total number of records with filtering
       $totalRecordwithFilter = $userEnrolment->select('course_id')
            ->orLike('course_id', $searchValue)
            ->countAllResults();

       ## Fetch records
       $records = $userEnrolment->select('*')
            ->orLike('course_id', $searchValue)
            ->orderBy($columnName,$columnSortOrder)
            ->findAll($rowperpage, $start);

       $data = array();

       foreach($records as $record ){

          $data[] = array( 
             "course_id"=>$record['course_id'],
             "name"=>$record['name'],
             "email_id"=>$record['email_id'],
             "designation"=>$record['designation'],
             "org_name"=>$record['org_name'],
             "status"=>$record['status'],
             "completion_percentage"=>$record['completion_percentage'],
             "completed_on"=>$record['completed_on']
          ); 
       }
     
       ## Response
       $response = array(
        "draw" => intval($draw),
        "iTotalRecords" => $totalRecords,
        "iTotalDisplayRecords" => $totalRecordwithFilter,
        "aaData" => $data,
        "token" => csrf_hash() // New token hash
       );

       return $this->response->setJSON($response);
   }
    
}