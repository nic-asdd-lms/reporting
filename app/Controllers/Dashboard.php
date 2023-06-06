<?php

namespace App\Controllers;

use App\Models\MasterOrganizationModel;
use App\Models\MasterProgramModel;
use App\Models\MasterUserModel;
use App\Models\MasterCourseModel;
use App\Models\UserEnrolmentProgram;
use App\Models\UserEnrolmentCourse;
use App\Models\DataUpdateModel;


class Dashboard extends BaseController
{


    public function __construct()
    {
        //$this->load->database();
        $db = \Config\Database::connect();
        helper(array('url', 'html', 'form'));

    }
    public function getDashboard()
    {
        try {
            helper('session');
            $session = \Config\Services::session();
            
            if (session_exists() && $session->get('role') == 'DOPT_ADMIN') {

                $request = $this->request->getVar();
                $ati = $request['ati'] != null ? $request['ati'] : '';
                $program = $request['program'] ? $request['program'] : '';

                $table = new \CodeIgniter\View\Table();
                $table->setTemplate($GLOBALS['overviewTableTemplate']);

                $learnertable = new \CodeIgniter\View\Table();
                $learnertable->setTemplate($GLOBALS['learnerOverviewTableTemplate']);

                $monthtable = new \CodeIgniter\View\Table();
                $monthtable->setTemplate($GLOBALS['monthOverviewTableTemplate']);

                $enrolment = new UserEnrolmentProgram();
                $orgModel = new MasterOrganizationModel();
                $programModel = new MasterProgramModel();
                $lastUpdate = new DataUpdateModel();

                
                $learnerTableData = $enrolment->dashboardTable($ati, $program, false);
                $learnerChartData = $enrolment->dashboardChart($ati, $program, false);

                $monthTableData = $enrolment->dashboardTable($ati, $program, true);
                $monthChartData = $enrolment->dashboardChart($ati, $program, true);

                $data = [];

                $learnerheader = ['Status', 'Count'];
                $learnertable->setHeading($learnerheader);

                $monthheader = ['Status', 'Count'];
                $monthtable->setHeading($monthheader);

                foreach ($learnerChartData->getResult() as $row) {
                    $data['label'][] = $row->status;
                    $data['data'][] = (int) $row->users;

                }

                foreach ($monthChartData->getResult() as $row) {
                    $data['monthlabel'][] = $row->status;
                    $data['monthdata'][] = (int) $row->users;

                }
                $data['chart_data'] = json_encode($data);
                $tabledata = [];

                if ($ati == '') {
                    $header = ['Institute ID', 'Institute', 'Enrolled', 'Not Started', 'In Progress', 'Completed'];
                    $table->setHeading($header);
                    $instituteData = $enrolment->getInstituteWiseCount('', -1, 0, '', 1, 'asc')->getResultArray();

                    for ($i = 0; $i < sizeof($instituteData); $i++) {
                        $instituteData[$i]['org_name'] = '<a href="'.base_url('/dashboard/dopt?ati=' . $instituteData[$i]['root_org_id'] . '&program=').'">' . $instituteData[$i]['org_name'] . '</a>';
                    }
                    $data['overview'] = $table->generate($instituteData);
                    $data['reportTitle'] = 'Enrolment Summary';
                    $data['back']=false;
                    $data['title'] = 'Institute Overview';
                    
                } else if ($program == '') {
                    $header = ['Program ID', 'Program', 'Enrolled', 'Not Started', 'In Progress', 'Completed'];
                    $table->setHeading($header);
                    $programData = $enrolment->getProgramWiseATIWiseCount($ati, -1, 0, '', 1, 'asc')->getResultArray();

                    for ($i = 0; $i < sizeof($programData); $i++) {
                        $programData[$i]['program_name'] = '<a href="'.base_url('/dashboard/dopt?ati=' . $ati . '&program=' . $programData[$i]['program_id']) . '">' . $programData[$i]['program_name'] . '</a>';
                    }
                    $data['overview'] = $table->generate($programData);
                    $atiName = $orgModel->getOrgName($ati);
                    $data['reportTitle'] = 'Enrolment Summary of Programs by "'.$atiName.'"';
                    $data['backUrl']='dashboard/dopt?ati=&program=';
                    $data['back']=true;
                    $data['title'] = 'Program Overview';

                } else {
                    $header = ['Name', 'Email', 'Organisation', 'Designation', 'BatchID', 'Status','Completed On'];
                    $table->setHeading($header);
                    $userData = $enrolment->getProgramWiseATIWiseReport($program, $ati, -1, 0, '', 1, 'asc')->getResultArray();
                    $atiName = $orgModel->getOrgName($ati);
                    $programName = $programModel->getProgramName($program);
                    $data['overview'] = $table->generate($userData);
                    $data['reportTitle'] = 'Enrolment Summary of "'.$programName.'" by "'.$atiName.'"';
                    $data['backUrl']='dashboard/dopt?ati='.$ati.'&program=';
                    $data['back']=true;
                    $data['title'] = 'User List';
                    
                }
                $data['lastUpdated'] = '[Data as on ' . $lastUpdate->getReportLastUpdatedTime() . ']';
                
                $learnersarr = $learnerTableData->getResultArray(); 
                usort($learnersarr, fn($a, $b) => $b['users'] <=> $a['users']);

                $montharr = $monthTableData->getResultArray(); 
                usort($montharr, fn($a, $b) => $b['users'] <=> $a['users']);

                $data['learner_overview'] = $learnertable->generate($learnersarr);
                $data['month_overview'] = $monthtable->generate($montharr);

                return view('header_view')
                    . view('dashboard', $data)
                    . view('footer_view');

            } else if ($session->get('role') != 'DOPT_ADMIN') {

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
            // return view('header_view') . view('error_general').view('footer_view');
        }

    }

    public function getSPVDashboard()
    {
        try {
            helper('session');
            $session = \Config\Services::session();
            
            if (session_exists() && $session->get('role') == 'SPV_ADMIN') {

                $request = $this->request->getVar();
                // $ati = $request['ati'] != null ? $request['ati'] : '';
                // $program = $request['program'] ? $request['program'] : '';

                $table = new \CodeIgniter\View\Table();
                $table->setTemplate($GLOBALS['overviewTableTemplate']);

                $learnertable = new \CodeIgniter\View\Table();
                $learnertable->setTemplate($GLOBALS['learnerOverviewTableTemplate']);

                $coursetable = new \CodeIgniter\View\Table();
                $coursetable->setTemplate($GLOBALS['courseOverviewTableTemplate']);

                $monthtable = new \CodeIgniter\View\Table();
                $monthtable->setTemplate($GLOBALS['monthOverviewTableTemplate']);

                $enrolment = new UserEnrolmentProgram();
                $enrolmentCourse = new UserEnrolmentCourse();
                $userModel = new MasterUserModel();
                $courseModel = new MasterCourseModel();
                $orgModel = new MasterOrganizationModel();
                $programModel = new MasterProgramModel();
                $lastUpdate = new DataUpdateModel();

                $orgData = $orgModel->getOrganisationCount();
                $data['orgCount'] = $orgData->getResultArray()[0]['count'];

                $userData = $userModel->getUserCount();
                $data['userCount'] = $userData->getResultArray()[0]['count'];

                $courseData = $courseModel->getCourseCount();
                $data['courseCount'] = $courseData->getResultArray()[0]['count'];

                $providerData = $courseModel->getProviderCount();
                $data['providerCount'] = $providerData->getResultArray()[0]['count'];

                $enrolmentData = $enrolmentCourse->getEnrolmentCount();
                $data['enrolmentCount'] = $enrolmentData->getResultArray()[0]['count'];

                $completionData = $enrolmentCourse->getCompletionCount();
                $data['completionCount'] = $completionData->getResultArray()[0]['count'];

                $uniqueEnrolmentData = $enrolmentCourse->getUniqueEnrolmentCount();
                $data['uniqueEnrolmentCount'] = $uniqueEnrolmentData->getResultArray()[0]['count'];

                $uniqueCompletionData = $enrolmentCourse->getUniqueCompletionCount();
                $data['uniqueCompletionCount'] = $uniqueCompletionData->getResultArray()[0]['count'];

                $durationtData = $courseModel->getContentHours();
                $data['contentHours'] = (int) $durationtData->getResultArray()[0]['sum'];

                $learnerTableData = $enrolmentCourse->dashboardTable('', '', false);
                $learnerChartData = $enrolmentCourse->dashboardChart('', '', false);

                $courseTableData = $courseModel->dashboardTable(false);
                $courseChartData = $courseModel->dashboardChart(false);

                $monthTableData = $enrolment->dashboardTable('', '', true);
                $monthChartData = $enrolment->dashboardChart('', '', true);

                $monthWiseUserOnboarding = $userModel->getMonthWiseUserOnboardingChart();
                $monthWiseTotalUsers = $userModel->getMonthWiseTotalUserChart();
                // $userChartData = $userModel->dashboardChart(false);


                $monthWiseEnrolment = $enrolmentCourse->getMonthWiseEnrolmentCount();
                $monthWiseCompletion = $enrolmentCourse->getMonthWiseCompletionCount();

                $monthWiseTotalEnrolment = $enrolmentCourse->getMonthWiseTotalEnrolmentCount();
                $monthWiseTotalCompletion = $enrolmentCourse->getMonthWiseTotalCompletionCount();

                
                // $data = [];

                $learnerheader = ['Status', 'Count'];
                $learnertable->setHeading($learnerheader);

                $courseheader = ['Status', 'Count'];
                $coursetable->setHeading($courseheader);

                $monthheader = ['Status', 'Count'];
                $monthtable->setHeading($monthheader);

                foreach ($learnerChartData->getResult() as $row) {
                    $data['label'][] = $row->completion_status;
                    $data['data'][] = (int) $row->users;
                }
                
                foreach ($courseChartData->getResult() as $row) {
                    $data['courselabel'][] = $row->status;
                    $data['coursedata'][] = (int) $row->count;
                }

                foreach ($monthChartData->getResult() as $row) {
                    $data['monthlabel'][] = $row->status;
                    $data['monthdata'][] = (int) $row->users;
                }

                foreach ($monthWiseUserOnboarding->getResult() as $row) {
                    $data['onboardingMonth'][] = $row->creation_month;
                    $data['onboardingCount'][] = $row->count;
                }

                foreach ($monthWiseTotalUsers->getResult() as $row) {
                    $data['totalUserMonth'][] = $row->creation_month;
                    $data['totalUserCount'][] = $row->sum;
                }

                foreach ($monthWiseEnrolment->getResult() as $row) {
                    $data['monthWiseEnrolmentMonth'][] = $row->enrolled_month;
                    $data['monthWiseEnrolmentCount'][] = $row->count;
                }
                foreach ($monthWiseCompletion->getResult() as $row) {
                    $data['monthWiseCompletionMonth'][] = $row->completed_month;
                    $data['monthWiseCompletionCount'][] = $row->count;
                }
                
                foreach ($monthWiseTotalEnrolment->getResult() as $row) {
                    $data['totalEnrolmentMonth'][] = $row->enrolled_month;
                    $data['totalEnrolmentCount'][] = $row->sum;
                }
                foreach ($monthWiseTotalCompletion->getResult() as $row) {
                    $data['totalCompletionMonth'][] = $row->completed_month;
                    $data['totalCompletionCount'][] = $row->sum;
                }
                
                $data['chart_data'] = json_encode($data);
                
                $tabledata = [];

                // if ($ati == '') {
                //     $header = ['Institute ID', 'Institute', 'Enrolled', 'Not Started', 'In Progress', 'Completed'];
                //     $table->setHeading($header);
                //     $instituteData = $enrolment->getInstituteWiseCount('', -1, 0, '', 1, 'asc')->getResultArray();

                //     for ($i = 0; $i < sizeof($instituteData); $i++) {
                //         $instituteData[$i]['org_name'] = '<a href="dashboard?ati=' . $instituteData[$i]['root_org_id'] . '&program=">' . $instituteData[$i]['org_name'] . '</a>';
                //     }
                //     $data['overview'] = $table->generate($instituteData);
                //     $data['reportTitle'] = 'Enrolment Summary';
                //     $data['back']=false;
                //     $data['title'] = 'Institute Overview';
                    
                // } else if ($program == '') {
                //     $header = ['Program ID', 'Program', 'Enrolled', 'Not Started', 'In Progress', 'Completed'];
                //     $table->setHeading($header);
                //     $programData = $enrolment->getProgramWiseATIWiseCount($ati, -1, 0, '', 1, 'asc')->getResultArray();

                //     for ($i = 0; $i < sizeof($programData); $i++) {
                //         $programData[$i]['program_name'] = '<a href="dashboard?ati=' . $ati . '&program=' . $programData[$i]['program_id'] . '">' . $programData[$i]['program_name'] . '</a>';
                //     }
                //     $data['overview'] = $table->generate($programData);
                //     $atiName = $orgModel->getOrgName($ati);
                //     $data['reportTitle'] = 'Enrolment Summary of Programs by "'.$atiName.'"';
                //     $data['backUrl']='/dashboard?ati=&program=';
                //     $data['back']=true;
                //     $data['title'] = 'Program Overview';

                // } else {
                //     $header = ['Name', 'Email', 'Organisation', 'Designation', 'BatchID', 'Status','Completed On'];
                //     $table->setHeading($header);
                //     $userData = $enrolment->getProgramWiseATIWiseReport($program, $ati, -1, 0, '', 1, 'asc')->getResultArray();
                //     $atiName = $orgModel->getOrgName($ati);
                //     $programName = $programModel->getProgramName($program);
                //     $data['overview'] = $table->generate($userData);
                //     $data['reportTitle'] = 'Enrolment Summary of "'.$programName.'" by "'.$atiName.'"';
                //     $data['backUrl']='/dashboard?ati='.$ati.'&program=';
                //     $data['back']=true;
                //     $data['title'] = 'User List';
                    
                // }
                $data['lastUpdated'] = '[Data as on ' . $lastUpdate->getReportLastUpdatedTime() . ']';
                $data['reportTitle'] = 'Dashboard';

                $learnersarr = $learnerTableData->getResultArray(); 
                usort($learnersarr, fn($a, $b) => $b['users'] <=> $a['users']);

                $coursearr = $courseTableData->getResultArray(); 
                usort($coursearr, fn($a, $b) => $b['count'] <=> $a['count']);

                $montharr = $monthTableData->getResultArray(); 
                usort($montharr, fn($a, $b) => $b['users'] <=> $a['users']);

                $data['learner_overview'] = $learnertable->generate($learnersarr);
                $data['course_overview'] = $coursetable->generate($coursearr);
                $data['month_overview'] = $monthtable->generate($montharr);

                return view('header_view')
                    . view('dashboard_spv', $data)
                    . view('footer_view');

            } else if ($session->get('role') != 'SPV_ADMIN') {

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
                $table->setTemplate($GLOBALS['instituteOverviewTableTemplate']);

                $learnertable = new \CodeIgniter\View\Table();
                $learnertable->setTemplate($GLOBALS['learnerOverviewTableTemplate']);

                $monthtable = new \CodeIgniter\View\Table();
                $monthtable->setTemplate($GLOBALS['monthOverviewTableTemplate']);

                $role = $session->get('role');
                $user = new UserEnrolmentProgram();
                $lastUpdate = new DataUpdateModel();

                $data['lastUpdated'] = '[Report as on ' . $lastUpdate->getReportLastUpdatedTime() . ']';
                $session->setTempdata('error', '');

                $org = '';
                if ($role == 'ATI_ADMIN') {
                    $org = $session->get('organisation');
                }
                // if ($reportType == 'atiWiseOverview') {
                $header = ['Institute', 'Enrolled', 'Not Started', 'In Progress', 'Completed'];
                $session->setTempdata('fileName', 'ATI-wise Overview', 300);
                $reportTitle = 'ATI-wise Overview';
                // }

                $table->setHeading($header);

                $session->setTempdata('reportHeader', $header);

                $data['resultHTML'] = $table->generate();

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

}

?>