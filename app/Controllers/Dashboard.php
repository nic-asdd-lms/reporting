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
                    $data['data'][] = $row->users;

                }

                foreach ($monthChartData->getResult() as $row) {
                    $data['monthlabel'][] = $row->status;
                    $data['monthdata'][] = $row->users;

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
                $data['orgCount'] = ($orgData->getResultArray()[0]['count']);

                $userData = $userModel->getUserCount();
                $data['userCount'] = ($userData->getResultArray()[0]['count']);

                $courseData = $courseModel->getCourseCount();
                $data['courseCount'] = ($courseData->getResultArray()[0]['count']);

                $providerData = $courseModel->getProviderCount();
                $data['providerCount'] = ($providerData->getResultArray()[0]['count']);

                $enrolmentData = $enrolmentCourse->getEnrolmentCount();
                $data['enrolmentCount'] = ($enrolmentData->getResultArray()[0]['count']);

                $completionData = $enrolmentCourse->getCompletionCount();
                $data['completionCount'] = ($completionData->getResultArray()[0]['count']);

                $uniqueEnrolmentData = $enrolmentCourse->getUniqueEnrolmentCount();
                $data['uniqueEnrolmentCount'] = ($uniqueEnrolmentData->getResultArray()[0]['count']);

                $uniqueCompletionData = $enrolmentCourse->getUniqueCompletionCount();
                $data['uniqueCompletionCount'] = ($uniqueCompletionData->getResultArray()[0]['count']);

                $durationtData = $courseModel->getContentHours();
                $data['contentHours'] = (int) ($durationtData->getResultArray()[0]['sum']);
                
                $learningHoursData = $courseModel->getlearningHours();
                $data['learningHours'] = (int)($learningHoursData->getResultArray()[0]['sum']);

                $learnerTableData = $enrolmentCourse->dashboardTable('', '', false);
                $learnerChartData = $enrolmentCourse->dashboardChart('', '', false);

                $courseTableData = $courseModel->dashboardTable(false);
                $courseChartData = $courseModel->dashboardChart(false);
                $monthWiseCoursePublished = $courseModel->getMonthWiseCoursePublished();
                $monthWiseTotalCoursePublished = $courseModel->getMonthWiseTotalCoursePublished();
                

                $monthTableData = $enrolment->dashboardTable('', '', true);
                $monthChartData = $enrolment->dashboardChart('', '', true);

                $monthWiseUserOnboarding = $userModel->getMonthWiseUserOnboardingChart();
                $monthWiseTotalUsers = $userModel->getMonthWiseTotalUserChart();
                $userRoleChartData = $userModel->roleDashboardChart();


                $monthWiseEnrolment = $enrolmentCourse->getMonthWiseEnrolmentCount();
                $monthWiseCompletion = $enrolmentCourse->getMonthWiseCompletionCount();

                $monthWiseTotalEnrolment = $enrolmentCourse->getMonthWiseTotalEnrolmentCount();
                $monthWiseTotalCompletion = $enrolmentCourse->getMonthWiseTotalCompletionCount();

                $monthWiseLearningHours = $enrolmentCourse->getMonthWiseLearningHours();
                $monthWiseTotalLearningHours = $enrolmentCourse->getMonthWiseTotalLearningHours();

                
                // $data = [];

                $learnerheader = ['Status', ['data'=>'Count','class'=>'dashboard-table-header-count']];
                $learnertable->setHeading($learnerheader);
                $learnersarr = $learnerTableData->getResultArray(); 
                usort($learnersarr, fn($a, $b) => $b['users'] <=> $a['users']);
                // array_push($learnersarr,['Total Enrolments',$enrolmentCourse->learnerDashboardTableFooter()->getResultArray()[0]['users']]);
                
                // $learnertable->setFooting('Total Enrolments',$enrolmentCourse->learnerDashboardTableFooter()->getResultArray()[0]['users']);

                $courseheader = ['Status', ['data'=>'Count','class'=>'dashboard-table-header-count']];
                $coursetable->setHeading($courseheader);

                $monthheader = ['Status', 'Count'];
                $monthtable->setHeading($monthheader);

                foreach ($learnerChartData->getResult() as $row) {
                    $data['label'][] = $row->completion_status;
                    $data['data'][] = (int) $row->users;
                }

                foreach ($learnersarr as $row) {
                    $keyCell = ['data'=>$row['completion_status'],'class'=>'dashboard-column'];
                    $valueCell = ['data'=>$row['users'],'class'=>'dashboard-value-column numformat'];
                    $learnertable->addRow($keyCell,$valueCell);
                }
                $keyCell = ['data'=>'Total Enrolments','class'=>'dashboard-footer'];
                $valueCell = ['data'=>$enrolmentCourse->learnerDashboardTableFooter()->getResultArray()[0]['users'],'class'=>'dashboard-footer dashboard-value-column numformat'];
                $learnertable->addRow($keyCell,$valueCell);
                
                foreach ($courseChartData->getResult() as $row) {
                    $data['courselabel'][] = $row->status;
                    $data['coursedata'][] = (int) ($row->count);
                }
                $coursearr = $courseTableData->getResultArray(); 
                usort($coursearr, fn($a, $b) => $b['count'] <=> $a['count']);
                foreach ($coursearr as $row) {
                    $keyCell = ['data'=>$row['status'],'class'=>'dashboard-column'];
                    $valueCell = ['data'=>$row['count'],'class'=>'dashboard-value-column numformat'];
                    $coursetable->addRow($keyCell,$valueCell);
                }
                
                foreach ($monthWiseCoursePublished->getResult() as $row) {
                    $data['coursePublishMonth'][] = $row->publish_month;
                    $data['coursePublishCount'][] = (int)($row->count);
                }
                foreach ($monthWiseTotalCoursePublished->getResult() as $row) {
                    $data['totalCoursePublishMonth'][] = $row->publish_month;
                    $data['totalCoursePublishCount'][] = (int)($row->sum);
                }
                
                foreach ($monthChartData->getResult() as $row) {
                    $data['monthlabel'][] = $row->status;
                    $data['monthdata'][] = (int)($row->users);
                }

                foreach ($monthWiseUserOnboarding->getResult() as $row) {
                    $data['onboardingMonth'][] = $row->creation_month;
                    $data['onboardingCount'][] = (int)($row->count);
                }

                foreach ($monthWiseTotalUsers->getResult() as $row) {
                    $data['totalUserMonth'][] = $row->creation_month;
                    $data['totalUserCount'][] = (int)($row->sum);
                }

                foreach ($monthWiseEnrolment->getResult() as $row) {
                    $data['monthWiseEnrolmentMonth'][] = $row->enrolled_month;
                    $data['monthWiseEnrolmentCount'][] = (int)($row->count);
                }
                foreach ($monthWiseCompletion->getResult() as $row) {
                    $data['monthWiseCompletionMonth'][] = $row->completed_month;
                    $data['monthWiseCompletionCount'][] = (int)($row->count);
                }
                
                foreach ($monthWiseTotalEnrolment->getResult() as $row) {
                    $data['totalEnrolmentMonth'][] = $row->enrolled_month;
                    $data['totalEnrolmentCount'][] = (int)($row->sum);
                }
                foreach ($monthWiseTotalCompletion->getResult() as $row) {
                    $data['totalCompletionMonth'][] = $row->completed_month;
                    $data['totalCompletionCount'][] = (int)($row->sum);
                }
                foreach ($userRoleChartData->getResult() as $row) {
                    $data['roleName'][] = $row->role;
                    $data['roleCount'][] = (int)($row->count);
                }
                foreach ($monthWiseLearningHours->getResult() as $row) {
                    $data['learningHoursMonth'][] = $row->month;
                    $data['monthWiseLearningHours'][] = (int)($row->sum);
                }
                
                foreach ($monthWiseTotalLearningHours->getResult() as $row) {
                    $data['totalLearningHoursMonth'][] = $row->month;
                    $data['totalearningHours'][] = (int)($row->sum);
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

                
                
                $montharr = $monthTableData->getResultArray(); 
                usort($montharr, fn($a, $b) => $b['users'] <=> $a['users']);

                $data['learner_overview'] = $learnertable->generate();
                $data['course_overview'] = $coursetable->generate();
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

    public function format_number($number) 
    {
        //123456789 => 12,34,56,789
        $crores = floor($number / 10000000);

        $lakhs = floor(($number % 10000000)/100000);
$thousands = $number % 100000;

$formatted = number_format($lakhs) . ','.number_format($lakhs) . ',' . number_format($thousands);

return $formatted;
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