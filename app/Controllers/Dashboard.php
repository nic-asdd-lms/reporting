<?php

namespace App\Controllers;

use App\Models\MasterOrganizationModel;
use App\Models\MasterProgramModel;
use App\Models\UserEnrolmentProgram;
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
            if (session_exists()) {

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
                        $instituteData[$i]['org_name'] = '<a href="dashboard?ati=' . $instituteData[$i]['root_org_id'] . '&program=">' . $instituteData[$i]['org_name'] . '</a>';
                    }
                    $data['overview'] = $table->generate($instituteData);
                    $data['reportTitle'] = 'Enrolment Summary';
                    $data['back']=false;

                } else if ($program == '') {
                    $header = ['Program ID', 'Program', 'Enrolled', 'Not Started', 'In Progress', 'Completed'];
                    $table->setHeading($header);
                    $programData = $enrolment->getProgramWiseATIWiseCount($ati, -1, 0, '', 1, 'asc')->getResultArray();

                    for ($i = 0; $i < sizeof($programData); $i++) {
                        $programData[$i]['program_name'] = '<a href="dashboard?ati=' . $ati . '&program=' . $programData[$i]['program_id'] . '">' . $programData[$i]['program_name'] . '</a>';
                    }
                    $data['overview'] = $table->generate($programData);
                    $atiName = $orgModel->getOrgName($ati);
                    $data['reportTitle'] = 'Enrolment Summary of Programs by "'.$atiName.'"';
                    $data['backUrl']='/dashboard?ati=&program=';
                    $data['back']=true;
                } else {
                    $header = ['Name', 'Email', 'Organisation', 'Designation', 'BatchID', 'Status', 'Completed On'];
                    $table->setHeading($header);
                    $userData = $enrolment->getProgramWiseATIWiseReport($program, $ati, -1, 0, '', 1, 'asc')->getResultArray();
                    $atiName = $orgModel->getOrgName($ati);
                    $programName = $programModel->getProgramName($program);
                    $data['overview'] = $table->generate($userData);
                    $data['reportTitle'] = 'Enrolment Summary of "'.$programName.'" by "'.$atiName.'"';
                    $data['backUrl']='/dashboard?ati='.$ati.'&program=';
                    $data['back']=true;
                }
                $learnersarr = $learnerTableData->getResultArray(); 
                usort($learnersarr, fn($a, $b) => $b['users'] <=> $a['users']);

                $montharr = $monthTableData->getResultArray(); 
                usort($montharr, fn($a, $b) => $b['users'] <=> $a['users']);

                $data['learner_overview'] = $learnertable->generate($learnersarr);
                $data['month_overview'] = $monthtable->generate($montharr);

                return view('header_view')
                    . view('dashboard', $data)
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