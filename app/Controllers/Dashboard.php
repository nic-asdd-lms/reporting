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

                $learnerheader = ['Status', ['data' => 'Count', 'class' => 'dashboard-table-header-count']];
                $learnertable->setHeading($learnerheader);

                $monthheader = ['Status', ['data' => 'Count', 'class' => 'dashboard-table-header-count']];
                $monthtable->setHeading($monthheader);

                $learnersarr = $learnerTableData->getResultArray();
                usort($learnersarr, fn($a, $b) => $b['users'] <=> $a['users']);
                foreach ($learnersarr as $row) {
                    $keyCell = ['data' => $row['status'], 'class' => 'dashboard-column'];
                    $valueCell = ['data' => $row['users'], 'class' => 'dashboard-value-column numformat'];
                    $learnertable->addRow($keyCell, $valueCell);
                }
                $keyCell = ['data' => 'Total Enrolments', 'class' => 'dashboard-footer'];
                $valueCell = ['data' => $enrolment->learnerDashboardTableFooter($ati, $program, false)->getResultArray()[0]['users'], 'class' => 'dashboard-footer dashboard-value-column numformat'];
                $learnertable->addRow($keyCell, $valueCell);

                foreach ($learnerChartData->getResult() as $row) {
                    $data['label'][] = $row->status;
                    $data['data'][] = $row->users;

                }

                $montharr = $monthTableData->getResultArray();
                usort($montharr, fn($a, $b) => $b['users'] <=> $a['users']);
                foreach ($montharr as $row) {
                    $keyCell = ['data' => $row['status'], 'class' => 'dashboard-column'];
                    $valueCell = ['data' => $row['users'], 'class' => 'dashboard-value-column numformat'];
                    $monthtable->addRow($keyCell, $valueCell);
                }
                $keyCell = ['data' => 'Total Enrolments', 'class' => 'dashboard-footer'];
                $valueCell = ['data' => $enrolment->learnerDashboardTableFooter($ati, $program, true)->getResultArray()[0]['users'], 'class' => 'dashboard-footer dashboard-value-column numformat'];
                $monthtable->addRow($keyCell, $valueCell);

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
                        $instituteData[$i]['org_name'] = '<a href="' . base_url('/dashboard/dopt?ati=' . $instituteData[$i]['root_org_id'] . '&program=') . '">' . $instituteData[$i]['org_name'] . '</a>';
                    }
                    $data['overview'] = $table->generate($instituteData);
                    $data['reportTitle'] = 'Enrolment Summary';
                    $data['back'] = false;
                    $data['title'] = 'Institute Overview';

                } else if ($program == '') {
                    $header = ['Program ID', 'Program', 'Enrolled', 'Not Started', 'In Progress', 'Completed'];
                    $table->setHeading($header);
                    $programData = $enrolment->getProgramWiseATIWiseCount($ati, -1, 0, '', 1, 'asc')->getResultArray();

                    for ($i = 0; $i < sizeof($programData); $i++) {
                        $programData[$i]['program_name'] = '<a href="' . base_url('/dashboard/dopt?ati=' . $ati . '&program=' . $programData[$i]['program_id']) . '">' . $programData[$i]['program_name'] . '</a>';
                    }
                    $data['overview'] = $table->generate($programData);
                    $atiName = $orgModel->getOrgName($ati);
                    $data['reportTitle'] = 'Enrolment Summary of Programs by "' . $atiName . '"';
                    $data['backUrl'] = 'dashboard/dopt?ati=&program=';
                    $data['back'] = true;
                    $data['title'] = 'Program Overview';

                } else {
                    $header = ['Name', 'Email', 'Organisation', 'Designation', 'BatchID', 'Status', 'Enrolled On', 'Completed On'];
                    $table->setHeading($header);
                    $userData = $enrolment->getProgramWiseATIWiseReport($program, $ati, -1, 0, '', 1, 'asc')->getResultArray();
                    $atiName = $orgModel->getOrgName($ati);
                    $programName = $programModel->getProgramName($program);
                    $data['overview'] = $table->generate($userData);
                    $data['reportTitle'] = 'Enrolment Summary of "' . $programName . '" by "' . $atiName . '"';
                    $data['backUrl'] = 'dashboard/dopt?ati=' . $ati . '&program=';
                    $data['back'] = true;
                    $data['title'] = 'User List';

                }
                $data['lastUpdated'] = '[Data as on ' . $lastUpdate->getReportLastUpdatedTime() . ']';



                $data['learner_overview'] = $learnertable->generate();
                $data['month_overview'] = $monthtable->generate();

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
                $data['learningHours'] = (int) ($learningHoursData->getResultArray()[0]['sum']);

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

                $monthWiseTotalUniqueEnrolment = $enrolmentCourse->getMonthWiseTotalUniqueEnrolmentCount();


                // $data = [];

                $learnerheader = ['Status', ['data' => 'Count', 'class' => 'dashboard-table-header-count']];
                $learnertable->setHeading($learnerheader);
                $learnersarr = $learnerTableData->getResultArray();
                usort($learnersarr, fn($a, $b) => $b['users'] <=> $a['users']);
                // array_push($learnersarr,['Total Enrolments',$enrolmentCourse->learnerDashboardTableFooter()->getResultArray()[0]['users']]);

                // $learnertable->setFooting('Total Enrolments',$enrolmentCourse->learnerDashboardTableFooter()->getResultArray()[0]['users']);

                $courseheader = ['Status', ['data' => 'Count', 'class' => 'dashboard-table-header-count']];
                $coursetable->setHeading($courseheader);

                $monthheader = ['Status', 'Count'];
                $monthtable->setHeading($monthheader);

                foreach ($learnerChartData->getResult() as $row) {
                    $data['label'][] = $row->completion_status;
                    $data['data'][] = (int) $row->users;
                }

                foreach ($learnersarr as $row) {
                    $keyCell = ['data' => $row['completion_status'], 'class' => 'dashboard-column'];
                    $valueCell = ['data' => $row['users'], 'class' => 'dashboard-value-column numformat'];
                    $learnertable->addRow($keyCell, $valueCell);
                }
                $keyCell = ['data' => 'Total Enrolments', 'class' => 'dashboard-footer'];
                $valueCell = ['data' => $enrolmentCourse->learnerDashboardTableFooter()->getResultArray()[0]['users'], 'class' => 'dashboard-footer dashboard-value-column numformat'];
                $learnertable->addRow($keyCell, $valueCell);

                foreach ($courseChartData->getResult() as $row) {
                    $data['courselabel'][] = $row->status;
                    $data['coursedata'][] = (int) ($row->count);
                }
                $coursearr = $courseTableData->getResultArray();
                usort($coursearr, fn($a, $b) => $b['count'] <=> $a['count']);
                foreach ($coursearr as $row) {
                    $keyCell = ['data' => $row['status'], 'class' => 'dashboard-column'];
                    $valueCell = ['data' => $row['count'], 'class' => 'dashboard-value-column numformat'];
                    $coursetable->addRow($keyCell, $valueCell);
                }

                foreach ($monthWiseCoursePublished->getResult() as $row) {
                    $data['coursePublishMonth'][] = $row->publish_month;
                    $data['coursePublishCount'][] = (int) ($row->count);
                }
                foreach ($monthWiseTotalCoursePublished->getResult() as $row) {
                    $data['totalCoursePublishMonth'][] = $row->publish_month;
                    $data['totalCoursePublishCount'][] = (int) ($row->sum);
                }

                foreach ($monthChartData->getResult() as $row) {
                    $data['monthlabel'][] = $row->status;
                    $data['monthdata'][] = (int) ($row->users);
                }


                $enrol_month = [];
                foreach ($monthWiseEnrolment->getResult() as $row) {
                    array_push($enrol_month, $row->enrolled_month);
                    // $data['monthWiseEnrolmentMonth'][] = $row->enrolled_month;
                    // $data['monthWiseEnrolmentCount'][] = (int) ($row->count);
                }

                $completion_month = [];
                foreach ($monthWiseCompletion->getResult() as $row) {
                    array_push($completion_month, $row->completed_month);
                    // $data['monthWiseCompletionMonth'][] = $row->completed_month;
                    // $data['monthWiseCompletionCount'][] = (int) ($row->count);
                }

                $onboarding_month = [];
                foreach ($monthWiseUserOnboarding->getResult() as $row) {
                    array_push($onboarding_month, $row->creation_month);
                    // $data['monthWiseCompletionMonth'][] = $row->completed_month;
                    // $data['monthWiseCompletionCount'][] = (int) ($row->count);
                }

                $months = array_unique(array_merge(array_merge($onboarding_month, $enrol_month), $completion_month));
                sort($months);


                $enrollArray = $monthWiseEnrolment->getResultArray();
                for ($i = 0; $i < sizeof($months); $i++) {
                    $data['monthWiseEnrolmentMonth'][] = $months[$i];
                    if (array_search($months[$i], array_column($enrollArray, 'enrolled_month')) != null) {
                        $data['monthWiseEnrolmentCount'][] = (int) $enrollArray[array_search($months[$i], array_column($enrollArray, 'enrolled_month'))]['count'];
                    } else {
                        $data['monthWiseEnrolmentCount'][] = 0;
                    }
                }

                $completeArray = $monthWiseCompletion->getResultArray();
                for ($i = 0; $i < sizeof($months); $i++) {

                    $data['monthWiseCompletionMonth'][] = $months[$i];
                    if (array_search($months[$i], array_column($completeArray, 'completed_month')) != null) {
                        $data['monthWiseCompletionCount'][] = (int) $completeArray[array_search($months[$i], array_column($completeArray, 'completed_month'))]['count'];
                    } else {
                        $data['monthWiseCompletionCount'][] = 0;
                    }
                }

                $totalEnrollArray = $monthWiseTotalEnrolment->getResultArray();
                for ($i = 0; $i < sizeof($months); $i++) {
                    $data['totalEnrolmentMonth'][] = $months[$i];
                    if (array_search($months[$i], array_column($totalEnrollArray, 'enrolled_month')) != null) {
                        $data['totalEnrolmentCount'][] = (int) $totalEnrollArray[array_search($months[$i], array_column($totalEnrollArray, 'enrolled_month'))]['sum'];
                    } else {
                        $data['totalEnrolmentCount'][] = (int) ($i == 0 ? 0 : $totalEnrollArray[array_search($months[$i - 1], array_column($totalEnrollArray, 'enrolled_month'))]['sum']);
                    }
                }

                $totalCompleteArray = $monthWiseTotalCompletion->getResultArray();
                for ($i = 0; $i < sizeof($months); $i++) {
                    $data['totalCompletionMonth'][] = $months[$i];
                    if (array_search($months[$i], array_column($totalCompleteArray, 'completed_month')) != null) {
                        $data['totalCompletionCount'][] = (int) $totalCompleteArray[array_search($months[$i], array_column($totalCompleteArray, 'completed_month'))]['sum'];
                    } else {
                        $data['totalCompletionCount'][] = (int) ($i == 0 ? 0 : $totalCompleteArray[array_search($months[$i - 1], array_column($totalCompleteArray, 'completed_month'))]['sum']);
                    }
                }

                $onboardingArray = $monthWiseUserOnboarding->getResultArray();
                for ($i = 0; $i < sizeof($months); $i++) {
                    $data['onboardingMonth'][] = $months[$i];
                    if (array_search($months[$i], array_column($onboardingArray, 'creation_month')) != null) {
                        $data['onboardingCount'][] = (int) $onboardingArray[array_search($months[$i], array_column($onboardingArray, 'creation_month'))]['count'];
                    } else {
                        $data['onboardingCount'][] = (int) ($i == 0 ? 0 : $onboardingArray[array_search($months[$i - 1], array_column($onboardingArray, 'creation_month'))]['count']);
                    }
                }

                $totalOnboardingArray = $monthWiseTotalUsers->getResultArray();
                for ($i = 0; $i < sizeof($months); $i++) {
                    $data['totalUserMonth'][] = $months[$i];
                    if (array_search($months[$i], array_column($totalOnboardingArray, 'creation_month')) != null) {
                        $data['totalUserCount'][] = (int) $totalOnboardingArray[array_search($months[$i], array_column($totalOnboardingArray, 'creation_month'))]['sum'];
                    } else {
                        $data['totalUserCount'][] = (int) ($i == 0 ? 0 : $totalOnboardingArray[array_search($months[$i - 1], array_column($totalOnboardingArray, 'creation_month'))]['sum']);
                    }
                }

                $totalUniqueEnrollArray = $monthWiseTotalUniqueEnrolment->getResultArray();
                for ($i = 0; $i < sizeof($months); $i++) {
                    $data['totalEnrolmentMonth'][] = $months[$i];
                    if (array_search($months[$i], array_column($totalUniqueEnrollArray, 'enrolled_month')) != null) {
                        $data['totalUniqeEnrolmentCount'][] = (int) $totalUniqueEnrollArray[array_search($months[$i], array_column($totalUniqueEnrollArray, 'enrolled_month'))]['sum'];
                    } else {
                        $data['totalUniqueEnrolmentCount'][] = (int) ($i == 0 ? 0 : $totalUniqueEnrollArray[array_search($months[$i - 1], array_column($totalUniqueEnrollArray, 'enrolled_month'))]['sum']);
                    }
                }


                // foreach ($monthWiseTotalEnrolment->getResult() as $row) {
                //     $data['totalEnrolmentMonth'][] = $row->enrolled_month;
                //     $data['totalEnrolmentCount'][] = (int) ($row->sum);
                // }
                // foreach ($monthWiseTotalCompletion->getResult() as $row) {
                //     $data['totalCompletionMonth'][] = $row->completed_month;
                //     $data['totalCompletionCount'][] = (int) ($row->sum);
                // }

                foreach ($userRoleChartData->getResult() as $row) {
                    $data['roleName'][] = $row->role;
                    $data['roleCount'][] = (int) ($row->count);
                }
                foreach ($monthWiseLearningHours->getResult() as $row) {
                    $data['learningHoursMonth'][] = $row->month;
                    $data['monthWiseLearningHours'][] = (int) ($row->sum);
                }

                foreach ($monthWiseTotalLearningHours->getResult() as $row) {
                    $data['totalLearningHoursMonth'][] = $row->month;
                    $data['totalearningHours'][] = (int) ($row->sum);
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

        $lakhs = floor(($number % 10000000) / 100000);
        $thousands = $number % 100000;

        $formatted = number_format($lakhs) . ',' . number_format($lakhs) . ',' . number_format($thousands);

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