<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterUserModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        //$this->load->database();
        $db = \Config\Database::connect();
        helper('array');

    }


    public function getAllUsers($limit, $offset, $search, $orderBy, $orderDir)
    {
        try {

            $builder = $this->db->table('user_list');
            $builder->select('name, email, org_name, designation, created_date, roles, profile_update_status');
            // $builder->distinct();

            if ($search != '')
                $builder->where("(name LIKE '%" . strtolower($search) . "%' OR name LIKE '%" . strtoupper($search) . "%' OR name LIKE '%" . ucfirst($search) . "%'
                            OR org_name LIKE '%" . strtolower($search) . "%' OR org_name LIKE '%" . strtoupper($search) . "%' OR org_name LIKE '%" . ucfirst($search) . "%'
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%'
                            OR roles LIKE '%" . strtolower($search) . "%' OR roles LIKE '%" . strtoupper($search) . "%' OR roles LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);


            $builder->orderBy((int) $orderBy + 1, $orderDir);
            if ($limit != -1)
                $builder->limit($limit, $offset);
            $query = $builder->get();

            return $query;

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function getUserCount()
    {
        try {
            $builder = $this->db->table('summary');
            $builder->select('count');
            $builder->where('kpi','Users Onboarded');
            $query = $builder->get();

            // echo $org_id,json_encode($query);
            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
    public function getUserByOrg($org, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {

            $builder = $this->db->table('user_list');
            $builder->select('name, email, org_name, designation, created_date, roles, profile_update_status');
            $builder->where('org_name', $org);

            if ($search != '')
                $builder->where("(name LIKE '%" . strtolower($search) . "%' OR name LIKE '%" . strtoupper($search) . "%' OR name LIKE '%" . ucfirst($search) . "%'
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%'
                            OR roles LIKE '%" . strtolower($search) . "%' OR roles LIKE '%" . strtoupper($search) . "%' OR roles LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);


            $builder->orderBy((int) $orderBy + 1, $orderDir);
            if ($limit != -1)
                $builder->limit($limit, $offset);

            $query = $builder->get();
            // 
            return $query;

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }


    public function getMDOAdminList($orgName, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            $table = new \CodeIgniter\View\Table();

            $builder = $this->db->table('master_user');
            $builder->select('concat(INITCAP(first_name),\' \',INITCAP(last_name)) as name, email, master_organization.org_name, designation,created_date, roles');
            $builder->join(' master_organization', 'master_organization.root_org_id = master_user.root_org_id');
            $builder->like('roles', 'MDO_ADMIN');
            if ($orgName != '') {
                $builder->where('master_organization.org_name', $orgName);

            }
            if ($search != '')
                $builder->where("(first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . ucfirst($search) . "%'
                            OR last_name LIKE '%" . strtolower($search) . "%' OR last_name LIKE '%" . strtoupper($search) . "%' OR last_name LIKE '%" . ucfirst($search) . "%'
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%'
                            OR master_organization.org_name LIKE '%" . strtolower($search) . "%' OR master_organization.org_name LIKE '%" . strtoupper($search) . "%' OR master_organization.org_name LIKE '%" . ucfirst($search) . "%'
                            OR roles LIKE '%" . strtolower($search) . "%' OR roles LIKE '%" . strtoupper($search) . "%' OR roles LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);


            $builder->orderBy((int) $orderBy + 1, $orderDir);
            if ($limit != -1)
                $builder->limit($limit, $offset);
            $query = $builder->get();

            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }




    public function getUserCountByOrg($limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            $table = new \CodeIgniter\View\Table();

            $builder = $this->db->table('master_organization');
            $builder->select(' org_name, user_count');
            // $builder->where(' org_name IS NOT NULL');
            if ($search != '') {

                $builder->like('org_name', strtolower($search));
                $builder->orLike('org_name', strtoupper($search));
                $builder->orLike('org_name', ucfirst($search));
            }

            $builder->where('status', 'Active');
            $builder->orderBy((int) $orderBy + 1, $orderDir);

            if ($limit != -1)
                $builder->limit($limit, $offset);
            $query = $builder->get();
            return $query;

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }


    public function getEnrolmentPercentageByOrg($limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            $table = new \CodeIgniter\View\Table();

            $builder = $this->db->table('master_organization');
            $builder->join('enrolment','enrolment.root_org_id = master_organization.root_org_id');
            $builder->select('enrolment.org_name, user_count, count(distinct enrolment.email) as enrol_count, round(cast((count(distinct enrolment.email)::float/user_count::float*100) as numeric),0) as enroll_pc ');
            if ($search != '') {
                $builder->where(" (enrolment.org_name LIKE '%" . strtolower($search) . "%' OR enrolment.org_name LIKE '%" . strtoupper($search) . "%' OR enrolment.org_name LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);

            }

            $builder->where('status', 'Active');
            $builder->groupBy('enrolment.root_org_id, enrolment.org_name, user_count');
            $builder->orderBy((int) $orderBy + 1, $orderDir);

            if ($limit != -1)
                $builder->limit($limit, $offset);

            $query = $builder->get();
            return $query;

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getOrgCount()
    {
        try {

            $builder = $this->db->table('master_user');
            $builder->select('root_org_id');
            $builder->distinct();
            // $builder->where(' org_name IS NOT NULL');

            $query = $builder->get();
            //$results = $query->getResultArray();

            // $pager = $this->pager;

            return $query;
            //return $query;
            // $template = [
            //     'table_open' => '<table id="tbl-result" class="display dataTable report-table" style="width:90%">'

            // ];
            // $table->setTemplate($template);
            // $table->setHeading('Organisation', 'User Count');

            //    return $table->generate($query);
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }



    public function getUserByMinistry($org, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {

            $builder = $this->db->table('ministry_user_list');
            $builder->select('name, email, ms_name,  dept_name, org_name, designation,created_date,roles');
            $builder->where('ms_name', $org);
            if ($search != '') {
                $builder->where("(name LIKE '%" . strtolower($search) . "%' OR name LIKE '%" . strtoupper($search) . "%' OR name LIKE '%" . ucfirst($search) . "%'
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%'
                            OR org_name LIKE '%" . strtolower($search) . "%' OR org_name LIKE '%" . strtoupper($search) . "%' OR org_name LIKE '%" . ucfirst($search) . "%'
                            OR dept_name LIKE '%" . strtolower($search) . "%' OR dept_name LIKE '%" . strtoupper($search) . "%' OR dept_name LIKE '%" . ucfirst($search) . "%'
                                OR roles LIKE '%" . strtolower($search) . "%' OR roles LIKE '%" . strtoupper($search) . "%' OR roles LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);
            }
            $builder->orderBy((int) $orderBy + 1, $orderDir);

            if ($limit != -1) {
                $builder->limit($limit, $offset);
            }
            // print_r($builder->getCompiledSelect());
            // die;
            $query = $builder->get();

            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }



    public function getDayWiseUserOnboarding($limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            if ($search != '') {
                $likeQuery = ' WHERE (TO_DATE(created_date,\'DD-MM-YYYY\')::text LIKE \'%' . $search . '%\' ) ';

            } else {
                $likeQuery = '';
            }
            if ($limit != -1) {
                $limitQuery = ' limit ' . $limit . ' offset ' . $offset;
            } else
                $limitQuery = '';

            $query = $this->db->query('select TO_DATE(created_date,\'DD-MM-YYYY\') as creation_date ,count(user_id) AS Day_wise_User_Onboarded from master_user ' . $likeQuery . ' group by created_date order by ' . (int) $orderBy + 1 . ' ' . $orderDir . $limitQuery);

            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function getMonthWiseUserOnboardingChart()
    {
        try {
            $builder = $this->db->table('user_list');
            $builder->select('to_char(date_trunc(\'MONTH\',to_date(created_date,\'DD/MM/YYYY\')),\'YYYY/MM\') as creation_month, count(*),
            sum(count(*) ) over (order by to_char(date_trunc(\'MONTH\',to_date(created_date,\'DD/MM/YYYY\')),\'YYYY/MM\'))');
            // $builder->where('to_date(created_date,\'DD/MM/YYYY\') > current_date - INTERVAL \'1 year\'');
            $builder->groupBy('creation_month');
            $builder->orderBy('creation_month');

            // echo '<pre>';
            // print_r($builder->getCompiledSelect());
            // die;
            $query = $builder->get();

            return $query;

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function getOnboardingMonth()
    {
        try {
            $builder = $this->db->table('user_list');
            $builder->select('to_char(date_trunc(\'MONTH\',to_date(created_date,\'DD/MM/YYYY\')),\'YYYY/MM\') as creation_month');
            $builder->where('to_date(created_date,\'DD/MM/YYYY\') > current_date - INTERVAL \'1 year\'');
            $builder->groupBy('creation_month');
            $builder->orderBy('creation_month');

            // echo '<pre>';
            // print_r($builder->getCompiledSelect());
            // die;
            $query = $builder->get();

            return $query;

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function getMonthWiseTotalUserChart()
    {
        try {
            $builder = $this->db->table('user_list');
            $builder->select('distinct to_char(date_trunc(\'month\',to_date(created_date,\'DD/MM/YYYY\')),\'YYYY/MM\') as creation_month, 
            sum(count(*) ) over (order by to_char(date_trunc(\'month\',to_date(created_date,\'DD/MM/YYYY\')),\'YYYY/MM\'))');
            // $builder->where('to_date(created_date,\'DD/MM/YYYY\') > current_date - INTERVAL \'1 year\'');
            $builder->groupBy('creation_month');
            $builder->orderBy('creation_month');

            // echo '<pre>';
            // print_r($builder->getCompiledSelect());
            // die;

            $query = $builder->get();

            return $query;

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }




    public function getMonthWiseUserOnboarding($limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            if ($search != '') {
                $likeQuery = ' WHERE (concat(split_part(created_datemmyy::TEXT,\'/\', 2),\'/\' ,split_part(created_datemmyy::TEXT,\'/\', 1)) LIKE \'%' . $search . '%\'  ) ';

            } else {
                $likeQuery = '';
            }
            if ($limit != -1) {
                $limitQuery = ' limit ' . $limit . ' offset ' . $offset;
            } else
                $limitQuery = '';


            $query = $this->db->query('select concat(split_part(created_datemmyy::TEXT,\'/\', 2),\'/\' ,split_part(created_datemmyy::TEXT,\'/\', 1)) as created_month,(count(user_id)) AS Month_wise_User_Onboarded from master_user ' . $likeQuery . ' group by created_month  order by ' . (int) $orderBy + 1 . ' ' . $orderDir . $limitQuery);

            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }




    public function getRoleWiseCount($orgName, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            if ($search != '') {
                if ($orgName == '')
                    $likeQuery = " WHERE (roles LIKE '%" . strtolower($search) . "%' OR roles LIKE '%" . strtoupper($search) . "%' OR roles LIKE '%" . ucfirst($search) . "%' ) ";
                else
                    $likeQuery = " AND (roles LIKE '%" . strtolower($search) . "%' OR roles LIKE '%" . strtoupper($search) . "%' OR roles LIKE '%" . ucfirst($search) . "%' ) ";

            } else {
                $likeQuery = '';
            }
            if ($limit != -1) {
                $limitQuery = ' limit ' . $limit . ' offset ' . $offset;
            } else
                $limitQuery = '';


            if ($orgName == '')
                $query = $this->db->query('select unnest(string_to_array(roles,\' / \')) as role, count(*) from master_user ' . $likeQuery . ' group by role  order by ' . (int) $orderBy + 1 . ' ' . $orderDir . $limitQuery);
            else
                $query = $this->db->query('select unnest(string_to_array(roles,\' / \')) as role, count(*) from master_user where org_name= \'' . $orgName . '\'' . $likeQuery . ' group by role  order by ' . (int) $orderBy + 1 . ' ' . $orderDir . $limitQuery);


            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }



    public function getMonthWiseMDOAdminCount($orgName, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            if ($search != '') {
                $likeQuery = " AND (created_datemmyy LIKE '%" . strtolower($search) . "%' OR created_datemmyy LIKE '%" . strtoupper($search) . "%' OR created_datemmyy LIKE '%" . ucfirst($search) . "%' ) ";

            } else {
                $likeQuery = '';
            }
            if ($limit != -1) {
                $limitQuery = ' limit ' . $limit . ' offset ' . $offset;
            } else
                $limitQuery = '';


            $query = $this->db->query('select  concat(split_part(created_datemmyy::TEXT,\'/\', 2),\'/\', split_part(created_datemmyy::TEXT,\'/\', 1)) AS month ,count(*) from master_user where roles ~\'MDO_ADMIN\' '.$likeQuery.' group by created_datemmyy  order by ' . (int) $orderBy + 1 . ' ' . $orderDir . $limitQuery);
            //$query = $this->db->query('select distinct(created_datemmyy), count(*) from master_user where roles ~\'MDO_ADMIN\' group by  created_datemmyy order by created_datemmyy  DESC');

            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }


    public function getCBPAdminList($orgName, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            $table = new \CodeIgniter\View\Table();

            $builder = $this->db->table('master_user');
            $builder->select('concat(INITCAP(first_name),\' \',INITCAP(last_name)) as name, email, master_organization.org_name, designation,created_date,roles');
            $builder->join(' master_organization', 'master_organization.root_org_id = master_user.root_org_id');
            $builder->like('roles', 'CBP_ADMIN');
            if ($orgName != '') {
                $builder->where('master_organization.org_name', $orgName);

            }
            if ($search != '')
                $builder->where("(first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . strtoupper($search) . "%' OR first_name LIKE '%" . ucfirst($search) . "%'
                            OR last_name LIKE '%" . strtolower($search) . "%' OR last_name LIKE '%" . strtoupper($search) . "%' OR last_name LIKE '%" . ucfirst($search) . "%'
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%'
                            OR master_organization.org_name LIKE '%" . strtolower($search) . "%' OR master_organization.org_name LIKE '%" . strtoupper($search) . "%' OR master_organization.org_name LIKE '%" . ucfirst($search) . "%'
                            OR roles LIKE '%" . strtolower($search) . "%' OR roles LIKE '%" . strtoupper($search) . "%' OR roles LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);

            $builder->orderBy((int) $orderBy + 1, $orderDir);
            if ($limit != -1)
                $builder->limit($limit, $offset);
            $query = $builder->get();

            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }


    public function getCreatorList($orgName, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            $table = new \CodeIgniter\View\Table();

            $builder = $this->db->table('master_user');
            $builder->select('concat(INITCAP(first_name),\' \',INITCAP(last_name)) as name, email, master_organization.org_name, designation,created_date,roles');
            $builder->join(' master_organization', 'master_organization.root_org_id = master_user.root_org_id');
            $builder->like('roles', 'CONTENT_CREATOR');
            if ($orgName != '') {
                $builder->where('master_organization.org_name', $orgName);
            }
            if ($search != '')
                $builder->where("(first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . strtoupper($search) . "%' OR first_name LIKE '%" . ucfirst($search) . "%'
                            OR last_name LIKE '%" . strtolower($search) . "%' OR last_name LIKE '%" . strtoupper($search) . "%' OR last_name LIKE '%" . ucfirst($search) . "%'
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%'
                            OR master_organization.org_name LIKE '%" . strtolower($search) . "%' OR master_organization.org_name LIKE '%" . strtoupper($search) . "%' OR master_organization.org_name LIKE '%" . ucfirst($search) . "%'
                            OR roles LIKE '%" . strtolower($search) . "%' OR roles LIKE '%" . strtoupper($search) . "%' OR roles LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);

            $builder->orderBy((int) $orderBy + 1, $orderDir);
            if ($limit != -1)
                $builder->limit($limit, $offset);
            $query = $builder->get();

            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }


    public function getReviewerList($orgName, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            $table = new \CodeIgniter\View\Table();

            $builder = $this->db->table('master_user');
            $builder->select('concat(INITCAP(first_name),\' \',INITCAP(last_name)) as name, email, master_organization.org_name, designation,created_date,roles');
            $builder->join(' master_organization', 'master_organization.root_org_id = master_user.root_org_id');
            $builder->like('roles', 'CONTENT_REVIEWER');
            if ($orgName != '') {
                $builder->where('master_organization.org_name', $orgName);
            }
            if ($search != '')
                $builder->where("(first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . strtoupper($search) . "%' OR first_name LIKE '%" . ucfirst($search) . "%'
                            OR last_name LIKE '%" . strtolower($search) . "%' OR last_name LIKE '%" . strtoupper($search) . "%' OR last_name LIKE '%" . ucfirst($search) . "%'
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%'
                            OR master_organization.org_name LIKE '%" . strtolower($search) . "%' OR master_organization.org_name LIKE '%" . strtoupper($search) . "%' OR master_organization.org_name LIKE '%" . ucfirst($search) . "%'
                            OR roles LIKE '%" . strtolower($search) . "%' OR roles LIKE '%" . strtoupper($search) . "%' OR roles LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);

            $builder->orderBy((int) $orderBy + 1, $orderDir);
            if ($limit != -1)
                $builder->limit($limit, $offset);
            $query = $builder->get();

            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }


    public function getPublisherList($orgName, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            $table = new \CodeIgniter\View\Table();

            $builder = $this->db->table('master_user');
            $builder->select('concat(INITCAP(first_name),\' \',INITCAP(last_name)) as name, email, master_organization.org_name, designation,created_date,roles');
            $builder->join(' master_organization', 'master_organization.root_org_id = master_user.root_org_id');
            $builder->like('roles', 'CONTENT_PUBLISHER');
            if ($orgName != '') {
                $builder->where('master_organization.org_name', $orgName);
            }
            if ($search != '')
                $builder->where("(first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . strtoupper($search) . "%' OR first_name LIKE '%" . ucfirst($search) . "%'
                            OR last_name LIKE '%" . strtolower($search) . "%' OR last_name LIKE '%" . strtoupper($search) . "%' OR last_name LIKE '%" . ucfirst($search) . "%'
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%'
                            OR master_organization.org_name LIKE '%" . strtolower($search) . "%' OR master_organization.org_name LIKE '%" . strtoupper($search) . "%' OR master_organization.org_name LIKE '%" . ucfirst($search) . "%'
                            OR roles LIKE '%" . strtolower($search) . "%' OR roles LIKE '%" . strtoupper($search) . "%' OR roles LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);

            $builder->orderBy((int) $orderBy + 1, $orderDir);
            if ($limit != -1)
                $builder->limit($limit, $offset);
            $query = $builder->get();

            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }


    public function getEditorList($orgName, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {

            $table = new \CodeIgniter\View\Table();

            $builder = $this->db->table('master_user');
            $builder->select('concat(INITCAP(first_name),\' \',INITCAP(last_name)) as name, email, master_organization.org_name, designation,created_date,roles');
            $builder->join(' master_organization', 'master_organization.root_org_id = master_user.root_org_id');
            $builder->like('roles', 'EDITOR');
            if ($orgName != '') {
                $builder->where('master_organization.org_name', $orgName);
            }
            if ($search != '')
                $builder->where("(first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . strtoupper($search) . "%' OR first_name LIKE '%" . ucfirst($search) . "%'
                            OR last_name LIKE '%" . strtolower($search) . "%' OR last_name LIKE '%" . strtoupper($search) . "%' OR last_name LIKE '%" . ucfirst($search) . "%'
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%'
                            OR master_organization.org_name LIKE '%" . strtolower($search) . "%' OR master_organization.org_name LIKE '%" . strtoupper($search) . "%' OR master_organization.org_name LIKE '%" . ucfirst($search) . "%'
                            OR roles LIKE '%" . strtolower($search) . "%' OR roles LIKE '%" . strtoupper($search) . "%' OR roles LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);

            $builder->orderBy((int) $orderBy + 1, $orderDir);
            if ($limit != -1)
                $builder->limit($limit, $offset);
            $query = $builder->get();

            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }


    public function getFracAdminList($orgName, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {

            $table = new \CodeIgniter\View\Table();

            $builder = $this->db->table('master_user');
            $builder->select('concat(INITCAP(first_name),\' \',INITCAP(last_name)) as name, email, master_organization.org_name, designation,created_date,roles');
            $builder->join(' master_organization', 'master_organization.root_org_id = master_user.root_org_id');
            $builder->like('roles', 'FRAC_ADMIN');
            if ($orgName != '') {
                $builder->where('master_organization.org_name', $orgName);
            }
            if ($search != '')
                $builder->where("(first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . strtoupper($search) . "%' OR first_name LIKE '%" . ucfirst($search) . "%'
                            OR last_name LIKE '%" . strtolower($search) . "%' OR last_name LIKE '%" . strtoupper($search) . "%' OR last_name LIKE '%" . ucfirst($search) . "%'
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%'
                            OR master_organization.org_name LIKE '%" . strtolower($search) . "%' OR master_organization.org_name LIKE '%" . strtoupper($search) . "%' OR master_organization.org_name LIKE '%" . ucfirst($search) . "%'
                            OR roles LIKE '%" . strtolower($search) . "%' OR roles LIKE '%" . strtoupper($search) . "%' OR roles LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);

            $builder->orderBy((int) $orderBy + 1, $orderDir);
            if ($limit != -1)
                $builder->limit($limit, $offset);
            $query = $builder->get();

            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getFracCompetencyMemberList($orgName, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            $table = new \CodeIgniter\View\Table();

            $builder = $this->db->table('master_user');
            $builder->select('concat(INITCAP(first_name),\' \',INITCAP(last_name)) as name, email, master_organization.org_name, designation,created_date,roles');
            $builder->join(' master_organization', 'master_organization.root_org_id = master_user.root_org_id');
            $builder->like('roles', 'FRAC_COMPETENCY_MEMBER');
            if ($orgName != '') {
                $builder->where('master_organization.org_name', $orgName);
            }
            if ($search != '')
                $builder->where("(first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . strtoupper($search) . "%' OR first_name LIKE '%" . ucfirst($search) . "%'
                            OR last_name LIKE '%" . strtolower($search) . "%' OR last_name LIKE '%" . strtoupper($search) . "%' OR last_name LIKE '%" . ucfirst($search) . "%'
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%'
                            OR master_organization.org_name LIKE '%" . strtolower($search) . "%' OR master_organization.org_name LIKE '%" . strtoupper($search) . "%' OR master_organization.org_name LIKE '%" . ucfirst($search) . "%'
                            OR roles LIKE '%" . strtolower($search) . "%' OR roles LIKE '%" . strtoupper($search) . "%' OR roles LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);

            $builder->orderBy((int) $orderBy + 1, $orderDir);
            if ($limit != -1)
                $builder->limit($limit, $offset);
            $query = $builder->get();

            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getFRACL1List($orgName, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            $table = new \CodeIgniter\View\Table();

            $builder = $this->db->table('master_user');
            $builder->select('concat(INITCAP(first_name),\' \',INITCAP(last_name)) as name, email, master_organization.org_name, designation,created_date,roles');
            $builder->join(' master_organization', 'master_organization.root_org_id = master_user.root_org_id');
            $builder->like('roles', 'FRAC_REVIEWER_L1');
            if ($orgName != '') {
                $builder->where('master_organization.org_name', $orgName);
            }
            if ($search != '')
                $builder->where("(first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . strtoupper($search) . "%' OR first_name LIKE '%" . ucfirst($search) . "%'
                            OR last_name LIKE '%" . strtolower($search) . "%' OR last_name LIKE '%" . strtoupper($search) . "%' OR last_name LIKE '%" . ucfirst($search) . "%'
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%'
                            OR master_organization.org_name LIKE '%" . strtolower($search) . "%' OR master_organization.org_name LIKE '%" . strtoupper($search) . "%' OR master_organization.org_name LIKE '%" . ucfirst($search) . "%'
                            OR roles LIKE '%" . strtolower($search) . "%' OR roles LIKE '%" . strtoupper($search) . "%' OR roles LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);

            $builder->orderBy((int) $orderBy + 1, $orderDir);
            if ($limit != -1)
                $builder->limit($limit, $offset);
            $query = $builder->get();

            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getFRACL2List($orgName, $limit, $offset, $search, $orderBy, $orderDir)
    {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(INITCAP(first_name),\' \',INITCAP(last_name)) as name, email, master_organization.org_name, designation,created_date,roles');
        $builder->join(' master_organization', 'master_organization.root_org_id = master_user.root_org_id');
        $builder->like('roles', 'FRAC_REVIEWER_L2');
        if ($orgName != '') {
            $builder->where('master_organization.org_name', $orgName);
        }
        if ($search != '')
            $builder->where("(first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . strtoupper($search) . "%' OR first_name LIKE '%" . ucfirst($search) . "%'
                            OR last_name LIKE '%" . strtolower($search) . "%' OR last_name LIKE '%" . strtoupper($search) . "%' OR last_name LIKE '%" . ucfirst($search) . "%'
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%'
                            OR master_organization.org_name LIKE '%" . strtolower($search) . "%' OR master_organization.org_name LIKE '%" . strtoupper($search) . "%' OR master_organization.org_name LIKE '%" . ucfirst($search) . "%'
                            OR roles LIKE '%" . strtolower($search) . "%' OR roles LIKE '%" . strtoupper($search) . "%' OR roles LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);

        $builder->orderBy((int) $orderBy + 1, $orderDir);
        if ($limit != -1)
            $builder->limit($limit, $offset);
        $query = $builder->get();

        return $query;
    }

    public function getIFUMemberList($orgName, $limit, $offset, $search, $orderBy, $orderDir)
    {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(INITCAP(first_name),\' \',INITCAP(last_name)) as name, email, master_organization.org_name, designation,created_date,roles');
        $builder->join(' master_organization', 'master_organization.root_org_id = master_user.root_org_id');
        $builder->like('roles', 'IFU_MEMBER');
        if ($orgName != '') {
            $builder->where('master_organization.org_name', $orgName);
        }
        if ($search != '')
            $builder->where("(first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . strtoupper($search) . "%' OR first_name LIKE '%" . ucfirst($search) . "%'
                            OR last_name LIKE '%" . strtolower($search) . "%' OR last_name LIKE '%" . strtoupper($search) . "%' OR last_name LIKE '%" . ucfirst($search) . "%'
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%'
                            OR master_organization.org_name LIKE '%" . strtolower($search) . "%' OR master_organization.org_name LIKE '%" . strtoupper($search) . "%' OR master_organization.org_name LIKE '%" . ucfirst($search) . "%'
                            OR roles LIKE '%" . strtolower($search) . "%' OR roles LIKE '%" . strtoupper($search) . "%' OR roles LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);

        $builder->orderBy((int) $orderBy + 1, $orderDir);
        if ($limit != -1)
            $builder->limit($limit, $offset);
        $query = $builder->get();

        return $query;
    }

    public function getPublicList($orgName, $limit, $offset, $search, $orderBy, $orderDir)
    {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(INITCAP(first_name),\' \',INITCAP(last_name)) as name, email, master_organization.org_name, designation,created_date,roles');
        $builder->join(' master_organization', 'master_organization.root_org_id = master_user.root_org_id');
        $builder->like('roles', 'PUBLIC');
        if ($orgName != '') {
            $builder->where('master_organization.org_name', $orgName);
        }
        if ($search != '')
            $builder->where("(first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . strtoupper($search) . "%' OR first_name LIKE '%" . ucfirst($search) . "%'
                            OR last_name LIKE '%" . strtolower($search) . "%' OR last_name LIKE '%" . strtoupper($search) . "%' OR last_name LIKE '%" . ucfirst($search) . "%'
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%'
                            OR master_organization.org_name LIKE '%" . strtolower($search) . "%' OR master_organization.org_name LIKE '%" . strtoupper($search) . "%' OR master_organization.org_name LIKE '%" . ucfirst($search) . "%'
                            OR roles LIKE '%" . strtolower($search) . "%' OR roles LIKE '%" . strtoupper($search) . "%' OR roles LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);

        $builder->orderBy((int) $orderBy + 1, $orderDir);
        if ($limit != -1)
            $builder->limit($limit, $offset);
        $query = $builder->get();

        return $query;
    }

    public function getSPVAdminList($orgName, $limit, $offset, $search, $orderBy, $orderDir)
    {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(INITCAP(first_name),\' \',INITCAP(last_name)) as name, email, master_organization.org_name, designation,created_date,roles');
        $builder->join(' master_organization', 'master_organization.root_org_id = master_user.root_org_id');
        $builder->like('roles', 'SPV_ADMIN');
        if ($orgName != '') {
            $builder->where('master_organization.org_name', $orgName);
        }
        if ($search != '')
            $builder->where("(first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . strtoupper($search) . "%' OR first_name LIKE '%" . ucfirst($search) . "%'
                            OR last_name LIKE '%" . strtolower($search) . "%' OR last_name LIKE '%" . strtoupper($search) . "%' OR last_name LIKE '%" . ucfirst($search) . "%'
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%'
                            OR master_organization.org_name LIKE '%" . strtolower($search) . "%' OR master_organization.org_name LIKE '%" . strtoupper($search) . "%' OR master_organization.org_name LIKE '%" . ucfirst($search) . "%'
                            OR roles LIKE '%" . strtolower($search) . "%' OR roles LIKE '%" . strtoupper($search) . "%' OR roles LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);

        $builder->orderBy((int) $orderBy + 1, $orderDir);
        if ($limit != -1)
            $builder->limit($limit, $offset);
        $query = $builder->get();

        return $query;
    }

    public function getStateAdminList($orgName, $limit, $offset, $search, $orderBy, $orderDir)
    {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(INITCAP(first_name),\' \',INITCAP(last_name)) as name, email, master_organization.org_name, designation,created_date,roles');
        $builder->join(' master_organization', 'master_organization.root_org_id = master_user.root_org_id');
        $builder->like('roles', 'STATE_ADMIN');
        if ($orgName != '') {
            $builder->where('master_organization.org_name', $orgName);
        }
        if ($search != '')
            $builder->where("(first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . strtoupper($search) . "%' OR first_name LIKE '%" . ucfirst($search) . "%'
                            OR last_name LIKE '%" . strtolower($search) . "%' OR last_name LIKE '%" . strtoupper($search) . "%' OR last_name LIKE '%" . ucfirst($search) . "%'
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%'
                            OR master_organization.org_name LIKE '%" . strtolower($search) . "%' OR master_organization.org_name LIKE '%" . strtoupper($search) . "%' OR master_organization.org_name LIKE '%" . ucfirst($search) . "%'
                            OR roles LIKE '%" . strtolower($search) . "%' OR roles LIKE '%" . strtoupper($search) . "%' OR roles LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);

        $builder->orderBy((int) $orderBy + 1, $orderDir);
        if ($limit != -1)
            $builder->limit($limit, $offset);
        $query = $builder->get();

        return $query;
    }

    public function getWATMemberList($orgName, $limit, $offset, $search, $orderBy, $orderDir)
    {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(INITCAP(first_name),\' \',INITCAP(last_name)) as name, email, master_organization.org_name, designation,created_date,roles');
        $builder->join(' master_organization', 'master_organization.root_org_id = master_user.root_org_id');
        $builder->like('roles', 'WAT_MEMBER');
        if ($orgName != '') {
            $builder->where('master_organization.org_name', $orgName);
        }
        if ($search != '')
            $builder->where("(first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . strtoupper($search) . "%' OR first_name LIKE '%" . ucfirst($search) . "%'
                            OR last_name LIKE '%" . strtolower($search) . "%' OR last_name LIKE '%" . strtoupper($search) . "%' OR last_name LIKE '%" . ucfirst($search) . "%'
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%'
                            OR master_organization.org_name LIKE '%" . strtolower($search) . "%' OR master_organization.org_name LIKE '%" . strtoupper($search) . "%' OR master_organization.org_name LIKE '%" . ucfirst($search) . "%'
                            OR roles LIKE '%" . strtolower($search) . "%' OR roles LIKE '%" . strtoupper($search) . "%' OR roles LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);

        $builder->orderBy((int) $orderBy + 1, $orderDir);
        if ($limit != -1)
            $builder->limit($limit, $offset);
        $query = $builder->get();
        return $query;
    }


    public function getTopOrgOnboarding($topCount, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            $table = new \CodeIgniter\View\Table();

            $builder = $this->db->table('master_organization');
            $builder->select(' org_name, user_count');
            // $builder->where(' org_name IS NOT NULL');
            // if ($search != '') {

            //     $builder->like('org_name', strtolower($search));
            //     $builder->orLike('org_name', strtoupper($search));
            //     $builder->orLike('org_name', ucfirst($search));
            // }

            $builder->orderBy('user_count', 'desc');
            if ($limit != -1)
                $builder->limit(min($topCount - $offset, $limit), $offset);
            else
                $builder->limit($topCount - $offset, $offset);
            $query = $builder->get();
            // print_r($builder);
            return $query;

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getTopOrgMdoAdmin($topCount, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            $table = new \CodeIgniter\View\Table();

            $builder = $this->db->table('master_user');
            $builder->join('master_organization', 'master_user.root_org_id = master_organization.root_org_id ');
            $builder->select('master_organization.org_name, count(distinct user_id) AS admin_count');
            $builder->like('roles', 'MDO_ADMIN');
            // $builder->where(' org_name IS NOT NULL');
            // if ($search != '') {

            //     $builder->like('master_organization.org_name', strtolower($search));
            //     $builder->orLike('master_organization.org_name', strtoupper($search));
            //     $builder->orLike('master_organization.org_name', ucfirst($search));
            // }

            $builder->groupBy('master_organization.org_name');
            $builder->orderBy('admin_count', 'desc');
            if ($limit != -1)
                $builder->limit(min($topCount - $offset, $limit), $offset);
            else
                $builder->limit($topCount - $offset, $offset);

            $query = $builder->get();
            // print_r($builder);
            return $query;

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function userSearch($search_key, $org)
    {
        try {
            $builderEmail = $this->db->table('master_user');
            $builderEmail->select('user_id,  email, concat(first_name,\' \',last_name) as name');
            $builderEmail->like('email', $search_key);
            if ($org != '')
                $builderEmail->where('root_org_id', $org);

            $builderPhone = $this->db->table('master_user');
            $builderPhone->select('user_id,  phone, concat(first_name,\' \',last_name) as name');
            $builderPhone->like('phone', $search_key);
            if ($org != '')
                $builderPhone->where('root_org_id', $org);
            
            $builderEmail->union($builderPhone);

            $query = $builderEmail->get();

            // $result = $this->db->query('SELECT org_name FROM master_organization WHERE SIMILARITY(org_name,\''.$search_key.'\') > 0.4 ;');
            // echo $search_key,json_encode($query);
            return $query->getResult();
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function getProfile($email, $orgName, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {

            $builder = $this->db->table('user_list');
            $builder->select('name, email, org_name, designation, created_date, roles, profile_update_status');
            $builder->where('email', $email);
            $builder->orWhere('phone', $email);

            if ($orgName != '')
                $builder->where('org_name', $orgName);

            if ($search != '')
                $builder->where("(name LIKE '%" . strtolower($search) . "%' OR name LIKE '%" . strtoupper($search) . "%' OR name LIKE '%" . ucfirst($search) . "%'
                            OR org_name LIKE '%" . strtolower($search) . "%' OR org_name LIKE '%" . strtoupper($search) . "%' OR org_name LIKE '%" . ucfirst($search) . "%'
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%'
                            OR roles LIKE '%" . strtolower($search) . "%' OR roles LIKE '%" . strtoupper($search) . "%' OR roles LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);


            $builder->orderBy((int) $orderBy + 1, $orderDir);
            if ($limit != -1)
                $builder->limit($limit, $offset);


            $query = $builder->get();

            return $query;

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function getRozgarMelaUserList($limit, $offset, $search, $orderBy, $orderDir)
    {
        try {

            $builder = $this->db->table('user_list');
            $builder->select('name, email, org_name, designation');
            $builder->like('email', '.kb@karmayogi.in');
            if ($search != '')
                $builder->where("(name LIKE '%" . strtolower($search) . "%' OR name LIKE '%" . strtoupper($search) . "%' OR name LIKE '%" . ucfirst($search) . "%'
                            OR org_name LIKE '%" . strtolower($search) . "%' OR org_name LIKE '%" . strtoupper($search) . "%' OR org_name LIKE '%" . ucfirst($search) . "%'
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);


            $builder->orderBy((int) $orderBy + 1, $orderDir);
            if ($limit != -1)
                $builder->limit($limit, $offset);
            $query = $builder->get();

            return $query;

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function getDesignationWiseUserCount($limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            $table = new \CodeIgniter\View\Table();

            $builder = $this->db->table('master_user');
            $builder->select('designation, count(*)');
            $builder->where('designation IS NOT NULL');
            if ($search != '') {

                $builder->like('designation', strtolower($search));
                $builder->orLike('designation', strtoupper($search));
                $builder->orLike('designation', ucfirst($search));
            }

            $builder->groupBy('designation');
            $builder->orderBy((int) $orderBy + 1, $orderDir);

            if ($limit != -1)
                $builder->limit($limit, $offset);
            $query = $builder->get();
            // print_r($builder);
            return $query;

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }


    public function dashboardChart($isMonthWise)
    {
        $builder = $this->db->table('master_user');

        $builder->select('status,count(*) as count');


        if ($isMonthWise == true)
            $builder->where('to_char(to_date(created_date,\'DD/MM/YYYY\'), \'MONTH YYYY\')  = to_char(current_date, \'MONTH YYYY\')');

        $builder->groupBy('status');
        $builder->orderBy('count', 'desc');

        return $builder->get();

    }

    public function roleDashboardChart()
    {
        $builder = $this->db->table('master_user');

        $builder->select('unnest(string_to_array(roles,\' / \')) as role, count(*)');

        $builder->groupBy('role');
        $builder->orderBy('count', 'desc');

        return $builder->get();

    }

}

?>