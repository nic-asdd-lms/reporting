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
        return view('header_view')
        .view('report_home', $data)
        .view('footer_view');


    }

    public function action()
	{
		if($this->request->getVar('action'))
		{
			$action = $this->request->getVar('action');

			if($action == 'get_dept')
			{
				$deptModel = new MasterStructureModel();

				$deptdata = $deptModel->getDepartment($this->request->getVar('ministry'));

				echo json_encode($deptdata);
			}

			if($action == 'get_org')
			{
				$orgModel = new MasterStructureModel();
				$orgdata = $orgModel->getOrganisation($this->request->getVar('dept'));

				echo json_encode($orgdata);
			}
		}
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

    public function getCourseReport() {
        $request = service('request');
        $courseReportType =$request->getPost('courseReportType');
        $course=$request->getPost('course');
       if($courseReportType == 'courseEnrolmentReport') {
        $data['result'] =$this->getCourseWiseEnrolmentReport($course);
        $data['reportTitle']='User Enrolment Report for Course - "'.$this->getCourseName($course).'"';
        $data['fileName']=$course.'_EnrolmentReport';
        return view('header_view')
        .view('report_result',$data)
        .view('footer_view');
       }

       $response = array();
    }

    public function getMDOReport() {
        $request = service('request');
        $courseReportType = $request->getPost('courseReportType');
        echo $courseReportType;
       $postData = $request->getPost();
       
        


       $response = array();
    }
    public function getCourseWiseEnrolmentReport($course) {
        $enrolment = new UserEnrolmentCourse();
        
				$enrolmentData = $enrolment->getCourseWiseEnrolmentReport($course);
				return $enrolmentData;
                
   }
   public function getCourseName($course_id) {
    $course = new MasterCourseModel();
    
            $course_name = $course->getCourseName($course_id);
           return $course_name;
            
            
}
    
}