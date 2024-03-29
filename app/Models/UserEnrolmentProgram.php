<?php

namespace App\Models;

use CodeIgniter\Model;

class UserEnrolmentProgram extends Model
{
    public function __construct()
    {
        parent::__construct();
        $db = \Config\Database::connect();
    }

    public function getProgramWiseEnrolmentReport($program, $org, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            $table = new \CodeIgniter\View\Table();

            $builder = $this->db->table('user_program_enrolment');
            $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, batch_id, user_program_enrolment.status, completed_on');
            $builder->join('master_user', 'master_user.user_id = user_program_enrolment.user_id ');
            $builder->join('master_organization', 'master_user.root_org_id = master_organization.root_org_id ');
            $builder->where('program_id', $program);
            if ($search != '')
                $builder->where("(first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . ucfirst($search) . "%'
                            OR last_name LIKE '%" . strtolower($search) . "%' OR last_name LIKE '%" . strtoupper($search) . "%' OR last_name LIKE '%" . ucfirst($search) . "%'
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%'
                            OR batch_id LIKE '%" . strtolower($search) . "%' OR batch_id LIKE '%" . strtoupper($search) . "%' OR batch_id LIKE '%" . ucfirst($search) . "%'
                            OR master_organization.org_name LIKE '%" . strtolower($search) . "%' OR master_organization.org_name LIKE '%" . strtoupper($search) . "%' OR master_organization.org_name LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);
            if ($org != '') {
                $builder->where('master_user.root_org_id', $org);
            }
            $builder->orderBy((int) $orderBy + 1, $orderDir);
            if ($limit != -1)
                $builder->limit($limit, $offset);
            $query = $builder->get();

            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
        //return $query->getResult();
    }

    public function getProgramWiseEnrolmentCount($org, $limit, $offset, $search, $orderBy, $orderDir)
    {
        //try{


        if ($search != '') {
            $likeQuery = " AND  (program_name LIKE '%" . strtolower($search) . "%' OR program_name LIKE '%" . strtoupper($search) . "%' OR program_name LIKE '%" . ucfirst($search) . "%' )";

        } else {
            $likeQuery = '';
        }
        if ($limit != -1) {
            $limitQuery = ' limit ' . $limit . ' offset ' . $offset;
        } else
            $limitQuery = '';

        if ($org == '') {
            $query = $this->db->query('SELECT program_name, batch_id, COUNT(*) AS enrolled_count
      ,SUM(CASE WHEN user_program_enrolment.status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count
  FROM user_program_enrolment, master_program
  WHERE user_program_enrolment.program_id = master_program.program_id ' . $likeQuery . '
  GROUP BY program_name,batch_id
  ORDER BY ' . (int) $orderBy + 1 . ' ' . $orderDir . $limitQuery);
        } else {
            $query = $this->db->query('SELECT program_name, batch_id, COUNT(*) AS enrolled_count
      ,SUM(CASE WHEN user_program_enrolment.status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count
  FROM user_program_enrolment, master_program, master_user
  WHERE user_program_enrolment.program_id = master_program.program_id 
  AND user_program_enrolment.user_id = master_user.user_id
  AND master_user.root_org_id=\'' . $org . '\'' . $likeQuery . '
  GROUP BY program_name,batch_id
  ORDER BY ' . (int) $orderBy + 1 . ' ' . $orderDir . $limitQuery);
        }
        return $query;
    }


    public function getATIWiseCount($org, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            if ($search != '') {
                $likeQuery = " AND ( program_name LIKE '%" . strtolower($search) . "%' OR program_name LIKE '%" . strtoupper($search) . "%' OR program_name LIKE '%" . ucfirst($search) . "%' 
                                OR master_organization.org_name LIKE '%" . strtolower($search) . "%' OR master_organization.org_name LIKE '%" . strtoupper($search) . "%' OR master_organization.org_name LIKE '%" . ucfirst($search) . "%' )";

            } else {
                $likeQuery = '';
            }
            if ($limit != -1) {
                $limitQuery = ' limit ' . $limit . ' offset ' . $offset;
            } else
                $limitQuery = '';

            if ($org == '')
                $whereOrg = '';
            else
                $whereOrg = ' AND master_organization.root_org_id = \'' . $org . '\' ';

            $query = $this->db->query('SELECT distinct program_name,batch_id,master_organization.org_name,  COUNT(*) AS enrolled_count
                                        ,SUM(CASE WHEN user_program_enrolment.status =\'Not Started\' THEN 1 ELSE 0 END) AS Not_Started,
                                        SUM(CASE WHEN user_program_enrolment.status =\'In-Progress\' THEN 1 ELSE 0 END) AS In_Progress,
                                        SUM(CASE WHEN user_program_enrolment.status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count      
                                        FROM user_program_enrolment, master_program, master_organization
                                        WHERE user_program_enrolment.program_id = master_program.program_id
                                        AND master_program.root_org_id=master_organization.root_org_id
                                        AND is_ati=true ' . $whereOrg . $likeQuery . '
                                        GROUP BY program_name,master_organization.org_name,batch_id
                                        ORDER BY ' . (int) $orderBy + 1 . ' ' . $orderDir . $limitQuery);

            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getTopOrgProgramWise($course, $topCount, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {

            $builder = $this->db->table('user_program_enrolment');
            $builder->join('master_user', 'master_user.user_id = user_program_enrolment.user_id');
            $builder->join('master_organization', 'master_user.root_org_id = master_organization.root_org_id ');
            $builder->join('master_program', 'master_program.program_id = user_program_enrolment.program_id ');
            $builder->select('master_organization.org_name, SUM(CASE WHEN user_program_enrolment.status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count');
            $builder->where('user_program_enrolment.program_id', $course);
            $builder->where('master_program.program_status', 'Live');
            $builder->where('user_program_enrolment.status', 'Completed');
            $builder->groupBy('master_organization.org_name');
            $builder->orderBy('completed_count', 'desc');

            // if ($search != '') {

            //     $builder->like('master_organization.org_name', strtolower($search));
            //     $builder->orLike('master_organization.org_name', strtoupper($search));
            //     $builder->orLike('master_organization.org_name', ucfirst($search));
            // }

            $builder->groupBy('master_organization.org_name');
            $builder->orderBy('completed_count', 'desc');
            if ($limit != -1)
                $builder->limit(min($topCount - $offset, $limit), $offset);
            else
                $builder->limit($topCount - $offset, $offset);

            $query = $builder->get();

            return $query;

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getRozgarMelaKpProgramReport($limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            $table = new \CodeIgniter\View\Table();

            $builder = $this->db->table('user_program_enrolment');
            $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, batch_id, user_program_enrolment.status, completed_on');
            $builder->join('master_user', 'master_user.user_id = user_program_enrolment.user_id ');
            $builder->join('master_organization', 'master_user.root_org_id = master_organization.root_org_id ');
            $builder->where('program_id', 'do_1137731307613716481132');
            $builder->like('email', '.kb@karmayogi.in');
            if ($search != '')
                $builder->where("(first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . ucfirst($search) . "%'
                            OR last_name LIKE '%" . strtolower($search) . "%' OR last_name LIKE '%" . strtoupper($search) . "%' OR last_name LIKE '%" . ucfirst($search) . "%'
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%'
                            OR batch_id LIKE '%" . strtolower($search) . "%' OR batch_id LIKE '%" . strtoupper($search) . "%' OR batch_id LIKE '%" . ucfirst($search) . "%'
                            OR master_organization.org_name LIKE '%" . strtolower($search) . "%' OR master_organization.org_name LIKE '%" . strtoupper($search) . "%' OR master_organization.org_name LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);

            $builder->orderBy((int) $orderBy + 1, $orderDir);
            if ($limit != -1)
                $builder->limit($limit, $offset);
            $query = $builder->get();

            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
        //return $query->getResult();
    }

    public function dashboardChart($ati, $program, $isMonthWise)
    {
        $builder = $this->db->table('user_program_enrolment');
        $builder->join('master_program', 'master_program.program_id = user_program_enrolment.program_id');

        $builder->select('status,count(*) as users');

        if ($ati != '')
            $builder->where('root_org_id', $ati);

        if ($program != '')
            $builder->where('user_program_enrolment.program_id', $program);

        if ($isMonthWise == true)
            $builder->where('to_char(to_date(enrolled_date,\'DD/MM/YYYY\'), \'MONTH YYYY\')  = to_char(current_date, \'MONTH YYYY\')');

        $builder->groupBy('status');
        $builder->orderBy('status', 'asc');

        return $builder->get();

    }

    public function commitDashboardChart($program, $isMonthWise)
    {
        $builder = $this->db->table('commit_programs_enrolment');
        // $builder->join('master_program', 'master_program.program_id = user_program_enrolment.program_id');

        $builder->select('status,count(*) as users');
        $builder->where('courseid is not null');
        
        if ($program != '')
            $builder->where('commit_programs_enrolment.courseid', $program);

        if ($isMonthWise == true)
            $builder->where('to_char(to_date(enrolled_on,\'DD-MON-YYYY\'), \'MONTH YYYY\')  = to_char(current_date, \'MONTH YYYY\')');
           
        $builder->groupBy('status');
        $builder->orderBy('status', 'asc');

        return $builder->get();

    }

    public function dashboardTable($ati, $program, $isMonthWise)
    {
        $builder = $this->db->table('user_program_enrolment');
        $builder->join('master_program', 'master_program.program_id = user_program_enrolment.program_id');

        // $enrolled = $this->db->table('user_program_enrolment');
        // $enrolled->join('master_program','master_program.program_id = user_program_enrolment.program_id');

        $builder->select('status,count(*) as users');
        // $enrolled->select('\'Enrolled\',count(*)  as users');

        if ($ati != '') {
            $builder->where('root_org_id', $ati);
            // $enrolled->where('root_org_id', $ati);
        }
        if ($program != '') {
            $builder->where('user_program_enrolment.program_id', $program);
            // $enrolled->where('user_program_enrolment.program_id', $program);
        }
        if ($isMonthWise == true) {
            $builder->where('to_char(to_date(enrolled_date,\'DD/MM/YYYY\'), \'MONTH YYYY\')  = to_char(current_date, \'MONTH YYYY\')');
            // $enrolled->where('to_char(to_date(enrolled_date,\'DD/MM/YYYY\'), \'MONTH YYYY\')  = to_char(current_date, \'MONTH YYYY\')');
        }

        // $builder->union($enrolled);
        $builder->groupBy('status');
        $builder->orderBy('status', 'asc');

        // echo '<pre>';
        // print_r($builder->getCompiledSelect());
        // die;

        return $builder->get();

    }

    public function commitDashboardTable( $program, $isMonthWise)
    {
        $builder = $this->db->table('commit_programs_enrolment');
        // $builder->join('master_program', 'master_program.program_id = user_program_enrolment.program_id');

        // $enrolled = $this->db->table('user_program_enrolment');
        // $enrolled->join('master_program','master_program.program_id = user_program_enrolment.program_id');

        $builder->select('status,count(*) as users');
        $builder->where('courseid is not null');
            // $enrolled->select('\'Enrolled\',count(*)  as users');
            $builder->groupBy('status');
            $builder->orderBy('status', 'asc');
    
        if ($program != '') {
            $builder->where('commit_programs_enrolment.courseid', $program);
            // $enrolled->where('user_program_enrolment.program_id', $program);
        }
        if ($isMonthWise == true) {
            $builder->where('to_char(to_date(enrolled_on,\'DD-MON-YYYY\'), \'MONTH YYYY\')  = to_char(current_date, \'MONTH YYYY\')');
            // $enrolled->where('to_char(to_date(enrolled_date,\'DD/MM/YYYY\'), \'MONTH YYYY\')  = to_char(current_date, \'MONTH YYYY\')');
        }

        // $builder->union($enrolled);
        
        // echo '<pre>';
        // print_r($builder->getCompiledSelect());
        // die;

        return $builder->get();

    }

    public function learnerDashboardTableFooter($ati, $program, $isMonthWise)
    {
        $enrolled = $this->db->table('user_program_enrolment');
        $enrolled->join('master_program', 'master_program.program_id = user_program_enrolment.program_id');

        $enrolled->select('\'Total Learners\',count(*)  as users');
        if ($ati != '') {
            $enrolled->where('root_org_id', $ati);
        }
        if ($program != '') {
            $enrolled->where('user_program_enrolment.program_id', $program);
        }
        if ($isMonthWise == true) {
            $enrolled->where('to_char(to_date(enrolled_date,\'DD/MM/YYYY\'), \'MONTH YYYY\')  = to_char(current_date, \'MONTH YYYY\')');
        }

        return $enrolled->get();

    }

    public function commitlearnerDashboardTableFooter($program, $isMonthWise)
    {
        $enrolled = $this->db->table('commit_programs_enrolment');
        // $enrolled->join('master_program', 'master_program.program_id = user_program_enrolment.program_id');

        $enrolled->select('\'Total Enrolments\',count(*)  as users');
        $enrolled->where('courseid is not null');
        if ($program != '') {
            $enrolled->where('commit_programs_enrolment.courseid', $program);
        }
        if ($isMonthWise == true) {
            $enrolled->where('to_char(to_date(enrolled_on,\'DD-MON-YYYY\'), \'MONTH YYYY\')  = to_char(current_date, \'MONTH YYYY\')');
        }

        return $enrolled->get();

    }


    public function getInstituteWiseCount($org, $limit, $offset, $search, $orderBy, $orderDir)
    {
        //try{


        if ($search != '') {
            $likeQuery = " AND  (program_name LIKE '%" . strtolower($search) . "%' OR program_name LIKE '%" . strtoupper($search) . "%' OR program_name LIKE '%" . ucfirst($search) . "%' )";

        } else {
            $likeQuery = '';
        }
        if ($limit != -1) {
            $limitQuery = ' limit ' . $limit . ' offset ' . $offset;
        } else
            $limitQuery = '';

        // if ($org == '') 
        {
            $query = $this->db->query('SELECT root_org_id, org_name,  COUNT(*) AS enrolled_count,
            SUM(CASE WHEN user_program_enrolment.status =\'Not Started\' THEN 1 ELSE 0 END) AS not_staretd,
            SUM(CASE WHEN user_program_enrolment.status =\'In-Progress\' THEN 1 ELSE 0 END) AS in_progress,
  SUM(CASE WHEN user_program_enrolment.status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count
  
  FROM user_program_enrolment, master_program
  WHERE user_program_enrolment.program_id = master_program.program_id ' . $likeQuery . '
  GROUP BY root_org_id, org_name
  ORDER BY ' . (int) $orderBy + 1 . ' ' . $orderDir . $limitQuery);
        }
        return $query;
    }

    public function getProgramWiseATIWiseCount($org, $limit, $offset, $search, $orderBy, $orderDir)
    {
        //try{


        if ($search != '') {
            $likeQuery = " AND  (program_name LIKE '%" . strtolower($search) . "%' OR program_name LIKE '%" . strtoupper($search) . "%' OR program_name LIKE '%" . ucfirst($search) . "%' )";

        } else {
            $likeQuery = '';
        }
        if ($limit != -1) {
            $limitQuery = ' limit ' . $limit . ' offset ' . $offset;
        } else
            $limitQuery = ''; {
            $query = $this->db->query('SELECT user_program_enrolment.program_id,program_name,  COUNT(*) AS enrolled_count
      ,SUM(CASE WHEN user_program_enrolment.status =\'Not Started\' THEN 1 ELSE 0 END) AS not_staretd,
            SUM(CASE WHEN user_program_enrolment.status =\'In-Progress\' THEN 1 ELSE 0 END) AS in_progress,
            SUM(CASE WHEN user_program_enrolment.status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count
  FROM user_program_enrolment, master_program
  WHERE user_program_enrolment.program_id = master_program.program_id 
  AND master_program.root_org_id=\'' . $org . '\'
  GROUP BY user_program_enrolment.program_id,program_name
  ORDER BY ' . (int) $orderBy + 1 . ' ' . $orderDir . $limitQuery);
        }
        return $query;
    }

    public function getProgramWiseATIWiseReport($program, $org, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            $table = new \CodeIgniter\View\Table();

            $builder = $this->db->table('user_program_enrolment');
            $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, batch_id, user_program_enrolment.status, enrolled_date, completed_on');
            $builder->join('master_user', 'master_user.user_id = user_program_enrolment.user_id ');
            $builder->join('master_program', 'user_program_enrolment.program_id = master_program.program_id ');
            $builder->join('master_organization', 'master_user.root_org_id = master_organization.root_org_id ');
            $builder->where('user_program_enrolment.program_id', $program);
            // if ($search != '')
            //     $builder->where("(first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . ucfirst($search) . "%'
            //                 OR last_name LIKE '%" . strtolower($search) . "%' OR last_name LIKE '%" . strtoupper($search) . "%' OR last_name LIKE '%" . ucfirst($search) . "%'
            //                 OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
            //                 OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%'
            //                 OR batch_id LIKE '%" . strtolower($search) . "%' OR batch_id LIKE '%" . strtoupper($search) . "%' OR batch_id LIKE '%" . ucfirst($search) . "%'
            //                 OR master_organization.org_name LIKE '%" . strtolower($search) . "%' OR master_organization.org_name LIKE '%" . strtoupper($search) . "%' OR master_organization.org_name LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);
            // if ($org != '') 
            {
                $builder->where('master_program.root_org_id', $org);
            }
            $builder->orderBy((int) $orderBy + 1, $orderDir);
            if ($limit != -1)
                $builder->limit($limit, $offset);

            // echo '<pre>';
            // print_r($builder->getCompiledSelect());
            // die;
            $query = $builder->get();


            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
        //return $query->getResult();
    }


    public function getCommitProgramWiseReport($program,  $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            $table = new \CodeIgniter\View\Table();

            $builder = $this->db->table('commit_programs_enrolment');
            $builder->select('full_name, email, organization, designation, enrolled_on, status,  rawcompletionpercentage, completed_on');
            $builder->where('courseid', $program);
            if ($search != '')
                $builder->where(" (full_name LIKE '%" . strtolower($search) . "%' OR full_name LIKE '%" . strtoupper($search) . "%' OR full_name LIKE '%" . ucfirst($search) . "%'
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%'
                            OR batch_id LIKE '%" . strtolower($search) . "%' OR batch_id LIKE '%" . strtoupper($search) . "%' OR batch_id LIKE '%" . ucfirst($search) . "%'
                            OR organization LIKE '%" . strtolower($search) . "%' OR organization LIKE '%" . strtoupper($search) . "%' OR organization LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);
            
            $builder->orderBy((int) $orderBy + 1, $orderDir);
            if ($limit != -1)
                $builder->limit($limit, $offset);

            // echo '<pre>';
            // print_r($builder->getCompiledSelect());
            // die;
            $query = $builder->get();


            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
        //return $query->getResult();
    }

    public function getCommitProgramWiseCount($limit, $offset, $search, $orderBy, $orderDir)
    {
        //try{


        if ($search != '') {
            $likeQuery = " AND  (cbp_name LIKE '%" . strtolower($search) . "%' OR cbp_name LIKE '%" . strtoupper($search) . "%' OR cbp_name LIKE '%" . ucfirst($search) . "%' )";

        } else {
            $likeQuery = '';
        }
        if ($limit != -1) {
            $limitQuery = ' limit ' . $limit . ' offset ' . $offset;
        } else
            $limitQuery = ''; 
            
            {
            $query = $this->db->query('SELECT commit_programs_enrolment.courseid, cbp_name,  COUNT(*) AS enrolled_count
      ,SUM(CASE WHEN commit_programs_enrolment.status =\'not-started\' THEN 1 ELSE 0 END) AS not_staretd,
            SUM(CASE WHEN commit_programs_enrolment.status =\'in-progress\' THEN 1 ELSE 0 END) AS in_progress,
            SUM(CASE WHEN commit_programs_enrolment.status =\'completed\' THEN 1 ELSE 0 END) AS completed_count
  FROM commit_programs_enrolment
  WHERE courseid IS NOT NULL
  GROUP BY commit_programs_enrolment.courseid,cbp_name
  ORDER BY ' . (int) $orderBy + 1 . ' ' . $orderDir . $limitQuery);
        }
        return $query;
    }



}

?>