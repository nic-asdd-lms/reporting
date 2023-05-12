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

    public function getATIWiseCount($org,$limit, $offset, $search, $orderBy, $orderDir)
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

                if($org == '')
                    $whereOrg = '';
                else 
                    $whereOrg = ' AND master_organization.root_org_id = \''.$org.'\' ' ;

            $query = $this->db->query('SELECT distinct program_name,batch_id,master_organization.org_name,  COUNT(*) AS enrolled_count
                                        ,SUM(CASE WHEN user_program_enrolment.status =\'Not Started\' THEN 1 ELSE 0 END) AS Not_Started,
                                        SUM(CASE WHEN user_program_enrolment.status =\'In-Progress\' THEN 1 ELSE 0 END) AS In_Progress,
                                        SUM(CASE WHEN user_program_enrolment.status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count      
                                        FROM user_program_enrolment, master_program, master_organization
                                        WHERE user_program_enrolment.program_id = master_program.program_id
                                        AND master_program.root_org_id=master_organization.root_org_id
                                        AND is_ati=true ' .$whereOrg . $likeQuery . '
                                        GROUP BY program_name,master_organization.org_name,batch_id
                                        ORDER BY ' . (int) $orderBy + 1 . ' ' . $orderDir . $limitQuery);

            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }


}

?>