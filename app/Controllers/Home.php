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

class Home extends BaseController
{
    public function index()
    {
        helper(['form', 'url']);
        $masterStructureModel = new MasterStructureModel();
        $masterOrganizationModel = new MasterOrganizationModel();
        $masterCourseModel = new MasterCourseModel();
        $data['mdoReportTypes']=$this->getMDOReportTypes();
        $data['ministry'] = $masterStructureModel->getMinistry();
        $data['org'] = $masterOrganizationModel->getOrganizations();
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

			else if($action == 'get_org')
			{
				$orgModel = new MasterStructureModel();
				$orgdata = $orgModel->getOrganisation($this->request->getVar('dept'));

				echo json_encode($orgdata);
			}

            else if($action == 'get_course')
			{
				$courseModel = new MasterCourseModel();
				$coursedata = $courseModel->getCourse();

				echo json_encode($coursedata);
			}
            else if($action == 'get_program')
			{
				$programModel = new MasterProgramModel();
				$programData = $programModel->getProgram();

				echo json_encode($programData);
			}
            else if($action == 'get_collection')
			{
				$collectionModel = new MasterCollectionModel();
				$collectionData = $collectionModel->getCollection();

				echo json_encode($collectionData);
			}
            else if($action == 'search')
			{
				if (isset($_POST['search'])) {
                    //Search box value assigning to $Name variable.
                       $search_key=$this->request->getVar('search');
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
        $session = \Config\Services::session();

        $role= $session->get('role');
        $org='';
        if ($role == 'MDO_ADMIN') {
            $org = $session->get('organisation');
        }
        $courseReportType =$request->getPost('courseReportType');
        $course=$request->getPost('course');
       if($courseReportType == 'courseEnrolmentReport') {
        $data['result'] =$this->getCourseWiseEnrolmentReport($course,$org);
        $data['reportTitle']='User Enrolment Report for Course - "'.$this->getCourseName($course).'"';
        $data['fileName']=$course.'_EnrolmentReport';
        
       }
       else if($courseReportType == 'courseEnrolmentCount') {
        $data['result'] =$this->getCourseWiseEnrolmentCount($course,$org);
        $data['reportTitle']='Course-wise Enrolment/Completion Count';
        $data['fileName']=$course.'_EnrolmentCompletionCount';
        
       }
       else if($courseReportType == 'programEnrolmentReport') {
        $data['result'] =$this->getProgramWiseEnrolmentReport($course,$org);
        $data['reportTitle']='User Enrolment Report for Program - "'.$this->getProgramName($course).'"';
        $data['fileName']=$course.'_EnrolmentReport';
        
       }
       else if($courseReportType == 'programEnrolmentCount') {
        $data['result'] =$this->getProgramWiseEnrolmentCount($course,$org);
        $data['reportTitle']='Program-wise Enrolment/Completion Count';
        $data['fileName']=$course.'_EnrolmentCompletionCount';
        
       }
       else if($courseReportType == 'collectionEnrolmentReport') {
        $data['result'] =$this->getCollectionWiseEnrolmentReport($course,$org);
        $data['reportTitle']='User Enrolment Report for Curated Collection - "'.$this->getCollectionName($course).'"';
        $data['fileName']=$course.'_EnrolmentReport';
        
       }
       else if($courseReportType == 'collectionEnrolmentCount') {
        $data['result'] =$this->getCollectionWiseEnrolmentCount($course,$org);
        $data['reportTitle']='Enrolment/Completion Count for Curated Collection - "'.$this->getCollectionName($course).'"';
        $data['fileName']=$course.'_EnrolmentCompletionCount';
        
       }
       return view('header_view')
       .view('report_result',$data)
       .view('footer_view');
       
    }

    public function getMDOReport() {
        $request = service('request');
        $session = \Config\Services::session();
		               
        $mdoReportType =$request->getPost('mdoReportType');
        $role =$session->get('role');

        if($role == 'SPV_ADMIN') {
            $ministry=$request->getPost('ministry');
            $dept=$request->getPost('dept');
            $org=$request->getPost('org');
        }
        else if($role == 'MDO_ADMIN') {
            $ministry=$session->get('ministry');
            $dept=$session->get('department');
            $org=$session->get('organisation');
        }
         
        if($ministry != "notSelected") {
            $ministryName=$this->getOrgName($ministry);
        }
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
        else if($mdoReportType == 'mdoUserCount') {
            $data['result'] =$this->getMDOWiseUserCount();
            $data['reportTitle']='MDO-wise user count ';
            $data['fileName']='MDOWiseUserCount';
            
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
        
           
           else if($mdoReportType == 'userWiseCount') {
            $data['result'] =$this->getMDOEnrolmentCount($orgName);
            $data['reportTitle']='User-wise course enrolment/completion count for organisation - "'.$orgName.'"';
            $data['fileName']=$org.'_UserList';
            
           }
        
           return view('header_view')
           .view('report_result',$data)
           .view('footer_view');
    }

    public function getRoleReport() {
        $request = service('request');
        $session = \Config\Services::session();
		               
        $roleReportType =$request->getPost('roleReportType');
        $role =$session->get('role');
        if($role == 'SPV_ADMIN') {
            $ministry='';
            $dept='';
            $org='';
            $orgName='';
        }
        else if($role == 'MDO_ADMIN') {
            $ministry=$session->get('ministry');
            $dept=$session->get('department');
            $org=$session->get('organisation');
        }
       if($org != "") {
            $orgName=$this->getOrgName($org);

        }
        
        if($roleReportType == 'roleWiseCount') {
            $data['result'] =$this->getRoleWiseCount($orgName);
            $data['reportTitle']='Role-wise count';
            $data['fileName']='RoleWiseCount';
            
           }
        else if($roleReportType == 'monthWiseMDOAdminCount') {
            $data['result'] =$this->getMonthWiseMDOAdminCount($orgName);
            $data['reportTitle']='Month-wise MDO Admin Creation Count';
            $data['fileName']='RoleWiseCount';
            
           }
        
        
           else if($roleReportType == 'cbpAdminList') {
            $data['result'] =$this->getCBPAdminList($orgName);
            $data['reportTitle']='MDO-wise user count ';
            $data['fileName']='MDOWiseUserCount';
            
           }
           else if($roleReportType == 'mdoAdminList') {
            $data['result'] =$this->getMDOAdminList($orgName);
            $data['reportTitle']='MDO Admin List ';
            $data['fileName']='MDOAdminList';
            
           }
        
           else if($roleReportType == 'creatorList') {
            $data['result'] =$this->getCreatorList($orgName);
            $data['reportTitle']='List of Content Creators';
            $data['fileName']='_UserEnrolmentReport';
            
           }
        
           else if($roleReportType == 'reviewerList') {
            $data['result'] =$this->getReviewerList($orgName);
            $data['reportTitle']='List of Content Reviewers';
            $data['fileName']='_UserList';
            
           }
        
           
           else if($roleReportType == 'publisherList') {
            $data['result'] =$this->getPublisherList($orgName);
            $data['reportTitle']='List of Content Publishers';
            $data['fileName']='_UserList';
            
           }
           else if($roleReportType == 'editorList') {
            $data['result'] =$this->getEditorList($orgName);
            $data['reportTitle']='List of Editors';
            $data['fileName']='_UserList';
            
           }
           else if($roleReportType == 'fracAdminList') {
            $data['result'] =$this->getFracAdminList($orgName);
            $data['reportTitle']='List of FRAC Admins';
            $data['fileName']='_UserList';
            
           }
           else if($roleReportType == 'fracCompetencyMember') {
            $data['result'] =$this->getFracCompetencyMemberList($orgName);
            $data['reportTitle']='List of FRAC Competency Members';
            $data['fileName']='_UserList';
            
           }
           else if($roleReportType == 'fracL1List') {
            $data['result'] =$this->getFRACL1List($orgName);
            $data['reportTitle']='MDO Admin List ';
            $data['fileName']='MDOAdminList';
            
           }
        
           else if($roleReportType == 'fracL2List') {
            $data['result'] =$this->getFRACL2List($orgName);
            $data['reportTitle']='List of Content Creators';
            $data['fileName']='_UserEnrolmentReport';
            
           }
        
           else if($roleReportType == 'ifuMemberList') {
            $data['result'] =$this->getIFUMemberList($orgName);
            $data['reportTitle']='List of Content Reviewers';
            $data['fileName']='_UserList';
            
           }
        
           
           else if($roleReportType == 'publicList') {
            $data['result'] =$this->getPublicList($orgName);
            $data['reportTitle']='List of Content Publishers';
            $data['fileName']='_UserList';
            
           }
           else if($roleReportType == 'spvAdminList') {
            $data['result'] =$this->getSPVAdminList($orgName);
            $data['reportTitle']='List of Editors';
            $data['fileName']='_UserList';
            
           }
           else if($roleReportType == 'stateAdminList') {
            $data['result'] =$this->getStateAdminList($orgName);
            $data['reportTitle']='List of FRAC Admins';
            $data['fileName']='_UserList';
            
           }
           else if($roleReportType == 'watMemberList') {
            $data['result'] =$this->getWATMemberList($orgName);
            $data['reportTitle']='List of FRAC Competency Members';
            $data['fileName']='_UserList';
            
           }
           return view('header_view')
           .view('report_result',$data)
           .view('footer_view');
    }


    public function getRoleWiseCount($orgName) {
        $user = new MasterUserModel();
        
                $userData = $user->getRoleWiseCount($orgName);
                return $userData;
                
    }

    public function getMonthWiseMDOAdminCount($orgName) {
        $user = new MasterUserModel();
        
                $userData = $user->getMonthWiseMDOAdminCount($orgName);
                return $userData;
                
    }

    public function getCBPAdminList($orgName) {
        $user = new MasterUserModel();
        
                $userData = $user->getCBPAdminList($orgName);
                return $userData;
                
    }
    public function getCreatorList($orgName) {
        $user = new MasterUserModel();
        
                $userData = $user->getCreatorList($orgName);
                return $userData;
                
    }public function getReviewerList($orgName) {
        $user = new MasterUserModel();
        
                $userData = $user->getReviewerList($orgName);
                return $userData;
                
    }
    
    public function getPublisherList($orgName) {
        $user = new MasterUserModel();
        
                $userData = $user->getPublisherList($orgName);
                return $userData;
                
    }
    
    public function getEditorList($orgName) {
        $user = new MasterUserModel();
        
                $userData = $user->getEditorList($orgName);
                return $userData;
                
    }
    
    public function getFRACAdminList($orgName) {
        $user = new MasterUserModel();
        
                $userData = $user->getFRACAdminList($orgName);
                return $userData;
                
    }
    
    public function getFracCompetencyMemberList($orgName) {
        $user = new MasterUserModel();
        
                $userData = $user->getFracCompetencyMemberList($orgName);
                return $userData;
                
    }
    
    public function getFRACL1List($orgName) {
        $user = new MasterUserModel();
        
                $userData = $user->getFRACL1List($orgName);
                return $userData;
                
    }
    
    public function getFRACL2List($orgName) {
        $user = new MasterUserModel();
        
                $userData = $user->getFRACL2List($orgName);
                return $userData;
                
    }
    
    public function getIFUMemberList($orgName) {
        $user = new MasterUserModel();
        
                $userData = $user->getIFUMemberList($orgName);
                return $userData;
                
    }
    
    public function getPublicList($orgName) {
        $user = new MasterUserModel();
        
                $userData = $user->getPublicList($orgName);
                return $userData;
                
    }
    
    public function getSPVAdminList($orgName) {
        $user = new MasterUserModel();
        
                $userData = $user->getSPVAdminList($orgName);
                return $userData;
                
    }
    
    public function getStateAdminList($orgName) {
        $user = new MasterUserModel();
        
                $userData = $user->getStateAdminList($orgName);
                return $userData;
                
    }
    
    public function getWATMemberList($orgName) {
        $user = new MasterUserModel();
        
                $userData = $user->getWATMemberList($orgName);
                return $userData;
                
    }
    
    
    public function getMDOUserList($org) {
        $user = new MasterUserModel();
        
				$userData = $user->getUserByOrg($org);
				return $userData;
                
   }

   
    
    
    
    
    
    
    
    
    
   public function getMDOAdminList($orgName) {
    $user = new MasterUserModel();
    
            $userData = $user->getMDOAdminList($orgName);
            return $userData;
            
}
   public function getMDOWiseUserCount() {
    $user = new MasterUserModel();
    
            $userData = $user->getUserCountByOrg();
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

    public function getCourseWiseEnrolmentReport($course,$org) {
        $enrolment = new UserEnrolmentCourse();
        
				$enrolmentData = $enrolment->getCourseWiseEnrolmentReport($course,$org);
				return $enrolmentData;
                
   }

   public function getCourseWiseEnrolmentCount($course,$org) {
    $enrolment = new UserEnrolmentCourse();
    
            $enrolmentData = $enrolment->getCourseWiseEnrolmentCount($course,$org);
            return $enrolmentData;
            
}

public function getProgramWiseEnrolmentReport($course,$org) {
    $enrolment = new UserEnrolmentProgram();
    
            $enrolmentData = $enrolment->getProgramWiseEnrolmentReport($course,$org);
            return $enrolmentData;
            
}

public function getProgramWiseEnrolmentCount($program,$org) {
$enrolment = new UserEnrolmentProgram();

        $enrolmentData = $enrolment->getProgramWiseEnrolmentCount($program,$org);
        return $enrolmentData;
        
}

public function getCollectionWiseEnrolmentReport($course,$org) {
    $enrolment = new UserEnrolmentCourse();
    
            $enrolmentData = $enrolment->getCollectionWiseEnrolmentReport($course,$org);
            return $enrolmentData;
            
}

public function getCollectionWiseEnrolmentCount($course,$org) {
$enrolment = new UserEnrolmentCourse();

        $enrolmentData = $enrolment->getCollectionWiseEnrolmentCount($course,$org);
        return $enrolmentData;
        
}


   public function getCourseName($course_id) {
    $course = new MasterCourseModel();
    
            $course_name = $course->getCourseName($course_id);
           return $course_name;
            
            
}
public function getCollectionName($collection_id) {
    $collection = new MasterCollectionModel();
    
            $collection_name = $collection->getCollectionName($collection_id);
           return $collection_name;
            
            
}

public function getProgramName($program_id) {
    $program = new MasterProgramModel();
    
            $program_name = $program->getProgramName($program_id);
           return $program_name;
            
            
}
public function getOrgName($org_id) {
    $org = new MasterOrganizationModel();
    
            $org_name = $org->getOrgName($org_id);
           return $org_name;
            
            
}

public function getMDOReportTypes() {
    $options = array();
    $options['mdoUserList']='MDO-wise user list';
    $options['mdoUserCount']='MDO-wise user count';
    $options['mdoAdminList']='MDO Admin list';
    $options['mdoUserEnrolment']='MDO-wise user enrolment report';
    $options['ministryUserEnrolment']='User list for all organisations under a ministry';
    
    return $options;
}
    

public function search()
    {
        helper(['form', 'url']);
 
        $data = [];
 
        $db      = \Config\Database::connect();
        $builder = $db->table('master_organization');   
 
        $query = $builder->like('org_name', $this->request->getVar('q'))
                    ->select('root_org_id, org_name')
                    ->limit(10)->get();
        $data = $query->getResult();
         
        echo json_encode($data);
        
    }

}
