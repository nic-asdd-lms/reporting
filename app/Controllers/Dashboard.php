<?php

namespace App\Controllers;

use App\Models\MasterOrganizationModel;
use App\Models\MasterProgramModel;
use App\Models\MasterUserModel;
use App\Models\MasterCourseModel;
use App\Models\UserEnrolmentProgram;
use App\Models\UserEnrolmentCourse;
use App\Models\DataUpdateModel;
use App\Models\DashboardModel;
use CodeIgniter\I18n\Time;


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

                $table = new \CodeIgniter\View\Table();
                $table->setTemplate($GLOBALS['overviewTableTemplate']);

                $learnertable = new \CodeIgniter\View\Table();
                $learnertable->setTemplate($GLOBALS['learnerOverviewTableTemplate']);

                $coursetable = new \CodeIgniter\View\Table();
                $coursetable->setTemplate($GLOBALS['courseOverviewTableTemplate']);

                $monthtable = new \CodeIgniter\View\Table();
                $monthtable->setTemplate($GLOBALS['monthOverviewTableTemplate']);

                $current_date = Time::today();
                $pastYear = $current_date->getYear() - 1 . '/' . $current_date->getMonth();

                $enrolment = new UserEnrolmentProgram();
                $enrolmentCourse = new UserEnrolmentCourse();
                $userModel = new MasterUserModel();
                $courseModel = new MasterCourseModel();
                $orgModel = new MasterOrganizationModel();
                $lastUpdate = new DataUpdateModel();

                $summaryData = $courseModel->getKpis()->getResultArray();
                $data['orgCount'] = $summaryData[array_search('Organisations Onboarded', array_column($summaryData, 'kpi'))]['count'];
                $data['userCount'] = $summaryData[array_search('Users Onboarded', array_column($summaryData, 'kpi'))]['count'];
                $data['courseCount'] = $summaryData[array_search('Courses Published', array_column($summaryData, 'kpi'))]['count'];
                $data['providerCount'] = $summaryData[array_search('Course Providers', array_column($summaryData, 'kpi'))]['count'];
                $data['enrolmentCount'] = $summaryData[array_search('Enrolment Count', array_column($summaryData, 'kpi'))]['count'];
                $data['completionCount'] = $summaryData[array_search('Completion Count', array_column($summaryData, 'kpi'))]['count'];
                $data['uniqueEnrolmentCount'] = $summaryData[array_search('Unique users enrolled', array_column($summaryData, 'kpi'))]['count'];
                $data['uniqueCompletionCount'] = $summaryData[array_search('Unique users completed', array_column($summaryData, 'kpi'))]['count'];
                $data['contentHours'] = (int) ($summaryData[array_search('Content Hours', array_column($summaryData, 'kpi'))]['count']);
                $data['learningHours'] = (int) ($summaryData[array_search('Learning Hours', array_column($summaryData, 'kpi'))]['count']);

                // $userRoleChartData = $userModel->roleDashboardChart();


                // LEARNER OVERVIEW

                $data['enrolmentPercentage'] = $summaryData[array_search('Enrolled to Onboarding percentage', array_column($summaryData, 'kpi'))]['count'];
                $data['completionPercentage'] = $summaryData[array_search('Completion to Enrollment percentage', array_column($summaryData, 'kpi'))]['count'];
                $data['inProgressPercentage'] = $summaryData[array_search('In Progress to Enrollment percentage', array_column($summaryData, 'kpi'))]['count'];
                $data['notStartedPercentage'] = $summaryData[array_search('Not Started to Enrollment percentage', array_column($summaryData, 'kpi'))]['count'];

                $learnerheader = ['Status', ['data' => 'Count', 'class' => 'dashboard-table-header-count']];
                $learnertable->setHeading($learnerheader);
                $learnerTableData = $enrolmentCourse->dashboardTable('', '', false);
                $learnersarr = $learnerTableData->getResultArray();
                usort($learnersarr, fn($a, $b) => $b['users'] <=> $a['users']);
                
                $courseheader = ['Status', ['data' => 'Count', 'class' => 'course-dashboard-table-header-count']];
                $coursetable->setHeading($courseheader);

                $monthheader = ['Status', 'Count'];
                $monthtable->setHeading($monthheader);

                $learnerChartData = $enrolmentCourse->dashboardChart('', '', false);
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

                $monthWiseEnrolment = $enrolmentCourse->getMonthWiseEnrolmentCount();
                $enrol_month = [];
                foreach ($monthWiseEnrolment->getResult() as $row) {
                    if ($row->enrolled_month >= $pastYear)
                        array_push($enrol_month, $row->enrolled_month);
                }

                $monthWiseCompletion = $enrolmentCourse->getMonthWiseCompletionCount();
                $completion_month = [];
                foreach ($monthWiseCompletion->getResult() as $row) {
                    if ($row->completed_month >= $pastYear)
                        array_push($completion_month, $row->completed_month);
                }

                $monthWiseUserOnboarding = $userModel->getMonthWiseUserOnboardingChart();
                $onboarding_month = [];
                foreach ($monthWiseUserOnboarding->getResult() as $row) {
                    if ($row->creation_month >= $pastYear)
                        array_push($onboarding_month, $row->creation_month);
                }

                /* Merge the month values in onboarding, enrolment and completion data to prevent discrepancy 
                in multi-dataset charts [like Enrolment vs. Completion, Onboarding vs. Enroment etc.]

                For each dataset in a multi-dataset chart, check if value exist for each month
                If value does not exist, assign 0 for that month if the chart is a non-cumulative chart [like Enrolment count, Completion count etc.]. 
                Assign value of previous month if the chart is cumulative [like Total Enrolment count, Total Completion count etc.]
                */


                $months = array_unique(array_merge(array_merge($onboarding_month, $enrol_month), $completion_month));
                sort($months);


                $enrollArray = $monthWiseEnrolment->getResultArray();
                for ($i = 0; $i < sizeof($months); $i++) {
                    $data['monthWiseEnrolmentMonth'][] = $months[$i];
                    if (array_search($months[$i], array_column($enrollArray, 'enrolled_month')) >= 0) {
                        $data['monthWiseEnrolmentCount'][] = (int) $enrollArray[array_search($months[$i], array_column($enrollArray, 'enrolled_month'))]['count'];
                    } else {
                        $data['monthWiseEnrolmentCount'][] = 0;
                    }
                }

                for ($i = 0; $i < sizeof($months); $i++) {
                    $data['totalEnrolmentMonth'][] = $months[$i];
                    if (array_search($months[$i], array_column($enrollArray, 'enrolled_month')) >= 0) {
                        $data['totalEnrolmentCount'][] = (int) $enrollArray[array_search($months[$i], array_column($enrollArray, 'enrolled_month'))]['sum'];
                    } else {
                        $data['totalEnrolmentCount'][] = (int) ($i == 0 ? 0 : $enrollArray[array_search($months[$i - 1], array_column($enrollArray, 'enrolled_month'))]['sum']);
                    }
                }

                $completeArray = $monthWiseCompletion->getResultArray();
                for ($i = 0; $i < sizeof($months); $i++) {

                    $data['monthWiseCompletionMonth'][] = $months[$i];
                    if (array_search($months[$i], array_column($completeArray, 'completed_month')) >= 0) {
                        $data['monthWiseCompletionCount'][] = (int) $completeArray[array_search($months[$i], array_column($completeArray, 'completed_month'))]['count'];
                    } else {
                        $data['monthWiseCompletionCount'][] = 0;
                    }
                }


                for ($i = 0; $i < sizeof($months); $i++) {
                    $data['totalCompletionMonth'][] = $months[$i];
                    if (array_search($months[$i], array_column($completeArray, 'completed_month')) >= 0) {
                        $data['totalCompletionCount'][] = (int) $completeArray[array_search($months[$i], array_column($completeArray, 'completed_month'))]['sum'];
                    } else {
                        $data['totalCompletionCount'][] = (int) ($i == 0 ? 0 : $completeArray[array_search($months[$i - 1], array_column($completeArray, 'completed_month'))]['sum']);
                    }
                }

                $monthWiseLearningHours = $enrolmentCourse->getMonthWiseLearningHours();
                $learningHoursArray = $monthWiseLearningHours->getResultArray();
                for ($i = 0; $i < sizeof($months); $i++) {
                    $data['learningHoursMonth'][] = $months[$i];
                    if (array_search($months[$i], array_column($learningHoursArray, 'month')) >= 0) {
                        $data['monthWiseLearningHours'][] = (int) $learningHoursArray[array_search($months[$i], array_column($learningHoursArray, 'month'))]['sum'];
                    } else {
                        $data['monthWiseLearningHours'][] = 0;
                    }
                }

                $monthWiseTotalLearningHours = $enrolmentCourse->getMonthWiseTotalLearningHours();
                $totalLearningHoursArray = $monthWiseTotalLearningHours->getResultArray();
                for ($i = 0; $i < sizeof($months); $i++) {
                    $data['totalLearningHoursMonth'][] = $months[$i];

                    if (array_search($months[$i], array_column($totalLearningHoursArray, 'month')) >= 0) {
                        $data['totalearningHours'][] = (int) $totalLearningHoursArray[array_search($months[$i], array_column($totalLearningHoursArray, 'month'))]['sum'];
                    } else {
                        $data['totalearningHours'][] = (int) ($i == 0 ? 0 : $totalLearningHoursArray[array_search($months[$i - 1], array_column($totalLearningHoursArray, 'month'))]['sum']);
                    }
                }


                //  COURSE OVERVIEW

                $data['avgRating'] = $summaryData[array_search('Average Rating of Courses', array_column($summaryData, 'kpi'))]['count'];
                $data['programCount'] = $summaryData[array_search('No. of Programs', array_column($summaryData, 'kpi'))]['count'];
                $data['programDuration'] = $summaryData[array_search('Program Duration (in hrs)', array_column($summaryData, 'kpi'))]['count'];
                $data['coursesCurrentMonth'] = $summaryData[array_search('Courses published this month', array_column($summaryData, 'kpi'))]['count'];


                $courseChartData = $courseModel->dashboardChart(false);
                foreach ($courseChartData->getResult() as $row) {
                    $data['courselabel'][] = $row->status;
                    $data['coursedata'][] = (int) ($row->count);
                }

                $courseTableData = $courseModel->dashboardTable(false);
                $coursearr = $courseTableData->getResultArray();
                usort($coursearr, fn($a, $b) => $b['count'] <=> $a['count']);
                foreach ($coursearr as $row) {
                    $keyCell = ['data' => $row['status'], 'class' => 'dashboard-column'];
                    $valueCell = ['data' => $row['count'], 'class' => 'dashboard-value-column numformat'];
                    $coursetable->addRow($keyCell, $valueCell);
                }

                $monthWiseCoursePublished = $courseModel->getMonthWiseCoursePublished();
                $publish_month = [];
                foreach ($monthWiseCoursePublished->getResult() as $row) {
                    if ($row->publish_month >= $pastYear)
                        array_push($publish_month, $row->publish_month);

                }

                $publishedArray = $monthWiseCoursePublished->getResultArray();
                for ($i = 0; $i < sizeof($publish_month); $i++) {
                    $data['coursePublishMonth'][] = $publish_month[$i];
                    if (array_search($publish_month[$i], array_column($publishedArray, 'publish_month')) >= 0) {
                        $data['coursePublishCount'][] = (int) $publishedArray[array_search($publish_month[$i], array_column($publishedArray, 'publish_month'))]['count'];
                    } else {
                        $data['coursePublishCount'][] = 0;
                    }
                }

                for ($i = 0; $i < sizeof($publish_month); $i++) {
                    $data['totalCoursePublishMonth'][] = $publish_month[$i];
                    if (array_search($publish_month[$i], array_column($publishedArray, 'publish_month')) >= 0) {
                        $data['totalCoursePublishCount'][] = (int) $publishedArray[array_search($publish_month[$i], array_column($publishedArray, 'publish_month'))]['sum'];
                    } else {
                        $data['totalCoursePublishCount'][] = (int) ($i == 0 ? 0 : $publishedArray[array_search($publish_month[$i - 1], array_column($publishedArray, 'publish_month'))]['sum']);
                    }
                }

                $monthChartData = $enrolment->dashboardChart('', '', true);
                foreach ($monthChartData->getResult() as $row) {
                    $data['monthlabel'][] = $row->status;
                    $data['monthdata'][] = (int) ($row->users);
                }

                
                // USER OVERVIEW

                $data['usersOnboardedYesterday'] = $summaryData[array_search('Users Onboarded yesterday', array_column($summaryData, 'kpi'))]['count'];
                $data['mdoAdminCount'] = $summaryData[array_search('No. of MDO Admin', array_column($summaryData, 'kpi'))]['count'];


                $onboardingArray = $monthWiseUserOnboarding->getResultArray();
                for ($i = 0; $i < sizeof($months); $i++) {
                    $data['onboardingMonth'][] = $months[$i];
                    if (array_search($months[$i], array_column($onboardingArray, 'creation_month')) >= 0) {
                        $data['onboardingCount'][] = (int) $onboardingArray[array_search($months[$i], array_column($onboardingArray, 'creation_month'))]['count'];
                    } else {

                        $data['onboardingCount'][] = 0;
                    }
                }

                for ($i = 0; $i < sizeof($months); $i++) {
                    $data['totalUserMonth'][] = $months[$i];
                    if (array_search($months[$i], array_column($onboardingArray, 'creation_month')) >= 0) {
                        $data['totalUserCount'][] = (int) $onboardingArray[array_search($months[$i], array_column($onboardingArray, 'creation_month'))]['sum'];
                    } else {
                        $data['totalUserCount'][] = (int) ($i == 0 ? 0 : $onboardingArray[array_search($months[$i - 1], array_column($onboardingArray, 'creation_month'))]['sum']);
                    }
                }

                $totalUniqueEnrollArray = $enrolmentCourse->getMonthWiseTotalUniqueEnrolmentCount()->getResultArray();
                for ($i = 0; $i < sizeof($months); $i++) {
                    $data['totalEnrolmentMonth'][] = $months[$i];
                    if (array_search($months[$i], array_column($totalUniqueEnrollArray, 'enrolled_month')) >= 0) {
                        $data['totalUniqeEnrolmentCount'][] = (int) $totalUniqueEnrollArray[array_search($months[$i], array_column($totalUniqueEnrollArray, 'enrolled_month'))]['sum'];
                    } else {
                        $data['totalUniqueEnrolmentCount'][] = (int) ($i == 0 ? 0 : $totalUniqueEnrollArray[array_search($months[$i - 1], array_column($totalUniqueEnrollArray, 'enrolled_month'))]['sum']);
                    }
                }

                
                
                // foreach ($userRoleChartData->getResult() as $row) {
                //     $data['roleName'][] = $row->role;
                //     $data['roleCount'][] = (int) ($row->count);
                // }



                //  ORG OVERVIEW

                $data['orgEnrolled'] = $summaryData[array_search('No. of Organisations Enrolled for Courses', array_column($summaryData, 'kpi'))]['count'];
                $data['mdoAdminOrgCount'] = $summaryData[array_search('No. of Organisations having MDO Admin', array_column($summaryData, 'kpi'))]['count'];

                $monthWiseOrgOnboarding = $orgModel->getMonthWiseOrgOnboardingChart();
                
                $orgOnboardingArray = $monthWiseOrgOnboarding->getResultArray();
                for ($i = 0; $i < sizeof($months); $i++) {
                    $data['orgOnboardingMonth'][] = $months[$i];
                    if (array_search($months[$i], array_column($orgOnboardingArray, 'creation_month')) >= 0) {
                        $data['orgOnboardingCount'][] = (int) $orgOnboardingArray[array_search($months[$i], array_column($orgOnboardingArray, 'creation_month'))]['count'];
                    } else {
                        $data['orgOnboardingCount'][] = (int) ($i == 0 ? 0 : $orgOnboardingArray[array_search($months[$i - 1], array_column($orgOnboardingArray, 'creation_month'))]['count']);
                    }
                }

                for ($i = 0; $i < sizeof($months); $i++) {
                    $data['totalOrgMonth'][] = $months[$i];
                    if (array_search($months[$i], array_column($orgOnboardingArray, 'creation_month')) >= 0) {
                        $data['totalOrgCount'][] = (int) $orgOnboardingArray[array_search($months[$i], array_column($orgOnboardingArray, 'creation_month'))]['sum'];
                    } else {
                        $data['totalOrgCount'][] = (int) ($i == 0 ? 0 : $orgOnboardingArray[array_search($months[$i - 1], array_column($orgOnboardingArray, 'creation_month'))]['sum']);
                    }
                }

                $data['chart_data'] = json_encode($data);


                $data['lastUpdated'] = '[Data as on ' . $lastUpdate->getReportLastUpdatedTime() . ']';
                $data['reportTitle'] = 'Dashboard';



                $monthTableData = $enrolment->dashboardTable('', '', true);
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