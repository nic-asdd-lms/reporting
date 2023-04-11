<?php

namespace App\Controllers;

use App\Models\MasterUserModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\MasterStructureModel;
use App\Models\MasterOrganizationModel;
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
        
       }
       else if($courseReportType == 'courseEnrolmentCount') {
        $data['result'] =$this->getCourseWiseEnrolmentCount($course);
        $data['reportTitle']='Course-wise Enrolment/Completion Count';
        $data['fileName']=$course.'_EnrolmentCompletionCount';
        
       }
       else if($courseReportType == 'programEnrolmentReport') {
        $data['result'] =$this->getProgramWiseEnrolmentReport($course);
        $data['reportTitle']='User Enrolment Report for Program - "'.$this->getCourseName($course).'"';
        $data['fileName']=$course.'_EnrolmentReport';
        
       }
       else if($courseReportType == 'programEnrolmentCount') {
        $data['result'] =$this->getProgramWiseEnrolmentCount($course);
        $data['reportTitle']='Course-wise Enrolment/Completion Count';
        $data['fileName']=$course.'_EnrolmentCompletionCount';
        
       }
       else if($courseReportType == 'collectionEnrolmentReport') {
        $data['result'] =$this->getCollectionWiseEnrolmentReport($course);
        $data['reportTitle']='User Enrolment Report for Curated Collection - "'.$this->getCourseName($course).'"';
        $data['fileName']=$course.'_EnrolmentReport';
        
       }
       else if($courseReportType == 'collectionEnrolmentCount') {
        $data['result'] =$this->getCollectionWiseEnrolmentCount($course);
        $data['reportTitle']='Enrolment/Completion Count for Curated Collection - "'.$this->getCourseName($course).'"';
        $data['fileName']=$course.'_EnrolmentCompletionCount';
        
       }
       return view('header_view')
       .view('report_result',$data)
       .view('footer_view');
       
    }

    public function getMDOReport() {
        $request = service('request');
        $mdoReportType =$request->getPost('mdoReportType');
        $ministry=$request->getPost('ministry');
        $dept=$request->getPost('dept');
        $org=$request->getPost('org');
        $ministryName=$this->getOrgName($ministry);
        
        if($dept != "notSelected") {
            $deptName=$this->getOrgName($dept);
        
        }
        if($org != "notSelected") {
            $orgName=$this->getOrgName($org);

        }
        
        if($mdoReportType == 'mdoUserList') {
            $data['result'] =$this->getMDOUserList($orgName);
            $data['reportTitle']='Users onboarded from organisation - "'.$orgName.'"';
            $data['fileName']=$org.'_UserList';
            
           }
        
           else if($mdoReportType == 'mdoUserEnrolment') {
            $data['result'] =$this->getMDOUserEnrolment($orgName);
            $data['reportTitle']='Users Enrolment Report for organisation - "'.$orgName.'"';
            $data['fileName']=$org.'_UserEnrolmentReport';
            
           }
        
           else if($mdoReportType == 'ministryUserEnrolment') {
            $data['result'] =$this->getMinistryUserList($ministryName);
            $data['reportTitle']='Users list for all organisations under ministry - "'.$ministryName.'"';
            $data['fileName']=$org.'_UserList';
            
           }
        
           
           else if($mdoReportType == 'mdoEnrolmentCount') {
            $data['result'] =$this->getMDOEnrolmentCount($orgName);
            $data['reportTitle']='User-wise course enrolment/completion count for organisation - "'.$orgName.'"';
            $data['fileName']=$org.'_UserList';
            
           }
        
           return view('header_view')
           .view('report_result',$data)
           .view('footer_view');
    }


    public function getMDOUserList($org) {
        $user = new MasterUserModel();
        
				$userData = $user->getUserByOrg($org);
				return $userData;
                
   }

   public function getMDOUserEnrolment($org) {
    $enrolment = new UserEnrolmentCourse();
    
            $enrolmentData = $enrolment->getEnrolmentByOrg($org);
            return $enrolmentData;
            
}

public function getMinistryUserList($org) {
    $user = new MasterUserModel();
    
            $userData = $user->getUserByMinistry($org);
            return $userData;
            
}

public function getMDOEnrolmentCount($org) {
    $enrolment = new UserEnrolmentCourse();
    
            $enrolmentData = $enrolment->getUserEnrolmentCountByMDO($org);
            return $enrolmentData;
            
}

    public function getCourseWiseEnrolmentReport($course) {
        $enrolment = new UserEnrolmentCourse();
        
				$enrolmentData = $enrolment->getCourseWiseEnrolmentReport($course);
				return $enrolmentData;
                
   }

   public function getCourseWiseEnrolmentCount($course) {
    $enrolment = new UserEnrolmentCourse();
    
            $enrolmentData = $enrolment->getCourseWiseEnrolmentCount($course);
            return $enrolmentData;
            
}

public function getProgramWiseEnrolmentReport($course) {
    $enrolment = new UserEnrolmentCourse();
    
            $enrolmentData = $enrolment->getProgramWiseEnrolmentReport($course);
            return $enrolmentData;
            
}

public function getProgramWiseEnrolmentCount($course) {
$enrolment = new UserEnrolmentCourse();

        $enrolmentData = $enrolment->getProgramWiseEnrolmentCount($course);
        return $enrolmentData;
        
}

public function getCollectionWiseEnrolmentReport($course) {
    $enrolment = new UserEnrolmentCourse();
    
            $enrolmentData = $enrolment->getCollectionWiseEnrolmentReport($course);
            return $enrolmentData;
            
}

public function getCollectionWiseEnrolmentCount($course) {
$enrolment = new UserEnrolmentCourse();

        $enrolmentData = $enrolment->getCollectionWiseEnrolmentCount($course);
        return $enrolmentData;
        
}


   public function getCourseName($course_id) {
    $course = new MasterCourseModel();
    
            $course_name = $course->getCourseName($course_id);
           return $course_name;
            
            
}
public function getOrgName($org_id) {
    $org = new MasterOrganizationModel();
    
            $org_name = $org->getOrgName($org_id);
           return $org_name;
            
            
}
    
}