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

    public function getUserByOrg($org, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            $table = new \CodeIgniter\View\Table();

            $builder = $this->db->table('master_user');
            $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, phone,created_date, roles, profile_update_status');
            $builder->join('master_organization', 'master_organization.root_org_id = master_user.root_org_id');
            $builder->where('master_organization.org_name', $org);

            if ($search != '')
                $builder->where("(first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . ucfirst($search) . "%'
                            OR last_name LIKE '%" . strtolower($search) . "%' OR last_name LIKE '%" . strtoupper($search) . "%' OR last_name LIKE '%" . ucfirst($search) . "%'
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%'
                            OR roles LIKE '%" . strtolower($search) . "%' OR roles LIKE '%" . strtoupper($search) . "%' OR roles LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);


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


    public function getMDOAdminList($orgName, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            $table = new \CodeIgniter\View\Table();

            $builder = $this->db->table('master_user');
            $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, phone,created_date, roles');
            $builder->join(' master_organization', 'master_organization.root_org_id = master_user.root_org_id');
            $builder->like('roles', 'MDO_ADMIN');
            if ($orgName != '') {
                $builder->where('org_name', $orgName);

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


    public function getUserCountByOrgClientSide()
    {
        try {
            $table = new \CodeIgniter\View\Table();

            $builder = $this->db->table('master_user');
            $builder->select(' master_organization.org_name, count(*)');
            $builder->join(' master_organization', 'master_organization.root_org_id = master_user.root_org_id');
            // $builder->where(' org_name IS NOT NULL');
            $builder->groupBy('master_organization.org_name');
            $query = $builder->get();

            return $query;
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
            if ($search != '') {
                $likeQuery = " AND (first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . strtoupper($search) . "%' OR first_name LIKE '%" . ucfirst($search) . "%' 
                                OR last_name LIKE '%" . strtolower($search) . "%' OR last_name LIKE '%" . strtoupper($search) . "%' OR last_name LIKE '%" . ucfirst($search) . "%'
                                OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                                OR master_organization.org_name LIKE '%" . strtolower($search) . "%' OR master_organization.org_name LIKE '%" . strtoupper($search) . "%' OR master_organization.org_name LIKE '%" . ucfirst($search) . "%'
                                OR dept_name LIKE '%" . strtolower($search) . "%' OR dept_name LIKE '%" . strtoupper($search) . "%' OR dept_name LIKE '%" . ucfirst($search) . "%'
                                OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%') ";

            } else {
                $likeQuery = '';
            }
            if ($limit != -1) {
                $limitQuery = ' limit ' . $limit . ' offset ' . $offset;
            } else
                $limitQuery = '';


            $query = $this->db->query('SELECT * FROM 
            (SELECT concat(first_name, \' \', last_name) as name, "email", "master_org_hierarchy"."ms_name", \'-\' as dept_name, \'-\' as org_name, 
             "designation", "phone", "created_date", "roles" 
             FROM "master_user" JOIN "master_organization" ON "master_organization"."root_org_id" = "master_user"."root_org_id" 
             JOIN "master_org_hierarchy" ON "master_org_hierarchy"."ms_id" = "master_user"."root_org_id" 
             WHERE "master_org_hierarchy"."ms_name" = \'' . $org . '\'' . $likeQuery . ' ) ms 
             UNION 
             SELECT * FROM 
             (SELECT concat(first_name, \' \', last_name) as name, "email", "master_org_hierarchy"."ms_name", "master_org_hierarchy"."dept_name", \'-\' as org_name, 
              "designation", "phone", "created_date", "roles" 
              FROM "master_user" JOIN "master_organization" ON "master_organization"."root_org_id" = "master_user"."root_org_id" 
              JOIN "master_org_hierarchy" ON "master_org_hierarchy"."dept_id" = "master_user"."root_org_id" 
              WHERE "master_org_hierarchy"."ms_name" = \'' . $org . '\' ' . $likeQuery . '
              AND "master_org_hierarchy"."dept_id" != "master_org_hierarchy"."ms_id")  dept 
              UNION 
              SELECT * FROM 
              (SELECT concat(first_name, \' \', last_name) as name, "email", "master_org_hierarchy"."ms_name", "master_org_hierarchy"."dept_name", "master_organization"."org_name", 
               "designation", "phone", "created_date", "roles" 
               FROM "master_user" JOIN "master_organization" ON "master_organization"."root_org_id" = "master_user"."root_org_id" 
               JOIN "master_org_hierarchy" ON "master_org_hierarchy"."org_id" = "master_user"."root_org_id" 
               WHERE "master_org_hierarchy"."ms_name" = \'' . $org . '\' ' . $likeQuery . '
               AND "master_org_hierarchy"."dept_id" != "master_org_hierarchy"."org_id" 
               AND "master_org_hierarchy"."org_id" != "master_org_hierarchy"."ms_id") org order by '. (int) $orderBy+1 . ' '.$orderDir  . $limitQuery);


            // $builder = $this->db->table('master_user');
            // $builder->select('concat(first_name,\' \',last_name) as name, email, master_org_hierarchy.ms_name, \'-\' as dept_name,\'-\' as org_name, designation, phone,created_date,roles');
            // $builder->join('master_organization', 'master_organization.root_org_id = master_user.root_org_id ');
            // $builder->join('master_org_hierarchy', 'master_org_hierarchy.ms_id = master_user.root_org_id ');
            // $builder->where('master_org_hierarchy.ms_name', $org);
            // if($limit != -1)
            // {
            //     $builder->limit($limit, $offset);
            //     print_r($builder);
            // }    
            // $unionDept = $this->db->table('master_user')
            //     ->select('concat(first_name,\' \',last_name) as name, email, master_org_hierarchy.ms_name, master_org_hierarchy.dept_name,  \'-\' as org_name, designation, phone,created_date,roles')
            //     ->join('master_organization', 'master_organization.root_org_id = master_user.root_org_id ')
            //     ->join('master_org_hierarchy', 'master_org_hierarchy.dept_id = master_user.root_org_id ')
            //     ->where('master_org_hierarchy.ms_name', $org)
            //     ->where('master_org_hierarchy.dept_id != master_org_hierarchy.ms_id');

            // $builder->union($unionDept);
            // if($limit != -1)
            //     $builder->limit($limit, $offset);


            // $unionOrg = $this->db->table('master_user')
            //     ->select('concat(first_name,\' \',last_name) as name, email, master_org_hierarchy.ms_name, master_org_hierarchy.dept_name, master_organization.org_name, designation, phone,created_date,roles')
            //     ->join('master_organization', 'master_organization.root_org_id = master_user.root_org_id ')
            //     ->join('master_org_hierarchy', 'master_org_hierarchy.org_id = master_user.root_org_id ')
            //     ->where('master_org_hierarchy.ms_name', $org)
            //     ->where('master_org_hierarchy.dept_id != master_org_hierarchy.org_id')
            //     ->where('master_org_hierarchy.org_id != master_org_hierarchy.ms_id');

            // $builder->union($unionOrg);
            // if($limit != -1)
            //     $builder->limit($limit, $offset);



            // $unionDept->where("(first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . strtoupper($search) . "%' 
            //                 OR last_name LIKE '%" . strtolower($search) . "%' OR last_name LIKE '%" . strtoupper($search) . "%'
            //                 OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%'
            //                 OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%')", NULL, FALSE);
            // $unionOrg->where("(first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . strtoupper($search) . "%' 
            //                 OR last_name LIKE '%" . strtolower($search) . "%' OR last_name LIKE '%" . strtoupper($search) . "%'
            //                 OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%'
            //                 OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%')", NULL, FALSE);
            // }

            // $builder = $builder->union($unionDept)->union($unionOrg);
            // print_r($builder);
            // if ($limit != -1){
            //     $builder->limit($limit, $offset);

            // }
            // $query = $builder->get();

            //print_r($limit);
            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }



    public function getDayWiseUserOnboarding($limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            if ($search != '') {
                $likeQuery = " WHERE (creation_date LIKE '%" . strtolower($search) . "%' OR creation_date LIKE '%" . strtoupper($search) . "%' OR creation_date LIKE '%" . ucfirst($search) . "%' ) ";

            } else {
                $likeQuery = '';
            }
            if ($limit != -1) {
                $limitQuery = ' limit ' . $limit . ' offset ' . $offset;
            } else
                $limitQuery = '';

            $query = $this->db->query('select TO_DATE(created_date,\'DD-MM-YYYY\') as creation_date ,count(user_id) AS Day_wise_User_Onboarded from master_user '. $likeQuery.' group by created_date order by '. (int) $orderBy+1 . ' '.$orderDir .$limitQuery);

            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }



    public function getMonthWiseUserOnboarding($limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            if ($search != '') {
                $likeQuery = " WHERE (created_datemmyy LIKE '%" . strtolower($search) . "%' OR created_datemmyy LIKE '%" . strtoupper($search) . "%' OR created_datemmyy LIKE '%" . ucfirst($search) . "%' ) ";

            } else {
                $likeQuery = '';
            }
            if ($limit != -1) {
                $limitQuery = ' limit ' . $limit . ' offset ' . $offset;
            } else
                $limitQuery = '';

            
            $query = $this->db->query('select concat(split_part(created_datemmyy::TEXT,\'/\', 2),\'/\' ,split_part(created_datemmyy::TEXT,\'/\', 1)) as created_month,(count(user_id)) AS Month_wise_User_Onboarded from master_user '. $likeQuery.' group by created_month  order by '. (int) $orderBy+1 . ' '.$orderDir.$limitQuery);

            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }




    public function getRoleWiseCount($orgName, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            if ($search != '') {
                if($orgName == '')
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
                $query = $this->db->query('select unnest(string_to_array(roles,\' / \')) as role, count(*) from master_user '. $likeQuery.' group by role  order by '. (int) $orderBy+1 . ' '.$orderDir.$limitQuery);
            else
                $query = $this->db->query('select unnest(string_to_array(roles,\' / \')) as role, count(*) from master_user where org_name= \'' . $orgName . '\''. $likeQuery.' group by role  order by '. (int) $orderBy+1 . ' '.$orderDir .$limitQuery);


            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }



    public function getMonthWiseMDOAdminCount($orgName, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            if ($search != '') {
                $likeQuery = " AND (creation_date LIKE '%" . strtolower($search) . "%' OR creation_date LIKE '%" . strtoupper($search) . "%' OR creation_date LIKE '%" . ucfirst($search) . "%' ) ";

            } else {
                $likeQuery = '';
            }
            if ($limit != -1) {
                $limitQuery = ' limit ' . $limit . ' offset ' . $offset;
            } else
                $limitQuery = '';

            
            $query = $this->db->query('select  concat(split_part(created_datemmyy::TEXT,\'/\', 2),\'/\', split_part(created_datemmyy::TEXT,\'/\', 1)) AS month ,count(*) from master_user where roles ~\'MDO_ADMIN\' group by created_datemmyy  order by '. (int) $orderBy+1 . ' '.$orderDir .$limitQuery);
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
            $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, phone,created_date,roles');
            $builder->join(' master_organization', 'master_organization.root_org_id = master_user.root_org_id');
            $builder->like('roles', 'CBP_ADMIN');
            if ($orgName != '') {
                $builder->where('org_name', $orgName);

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


    public function getCreatorList($orgName, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            $table = new \CodeIgniter\View\Table();

            $builder = $this->db->table('master_user');
            $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, phone,created_date,roles');
            $builder->join(' master_organization', 'master_organization.root_org_id = master_user.root_org_id');
            $builder->like('roles', 'CONTENT_CREATOR');
            if ($orgName != '') {
                $builder->where('org_name', $orgName);
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


    public function getReviewerList($orgName, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            $table = new \CodeIgniter\View\Table();

            $builder = $this->db->table('master_user');
            $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, phone,created_date,roles');
            $builder->join(' master_organization', 'master_organization.root_org_id = master_user.root_org_id');
            $builder->like('roles', 'CONTENT_REVIEWER');
            if ($orgName != '') {
                $builder->where('org_name', $orgName);
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


    public function getPublisherList($orgName, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            $table = new \CodeIgniter\View\Table();

            $builder = $this->db->table('master_user');
            $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, phone,created_date,roles');
            $builder->join(' master_organization', 'master_organization.root_org_id = master_user.root_org_id');
            $builder->like('roles', 'CONTENT_PUBLISHER');
            if ($orgName != '') {
                $builder->where('org_name', $orgName);
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


    public function getEditorList($orgName, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {

            $table = new \CodeIgniter\View\Table();

            $builder = $this->db->table('master_user');
            $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, phone,created_date,roles');
            $builder->join(' master_organization', 'master_organization.root_org_id = master_user.root_org_id');
            $builder->like('roles', 'EDITOR');
            if ($orgName != '') {
                $builder->where('org_name', $orgName);
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


    public function getFracAdminList($orgName, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {

            $table = new \CodeIgniter\View\Table();

            $builder = $this->db->table('master_user');
            $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, phone,created_date,roles');
            $builder->join(' master_organization', 'master_organization.root_org_id = master_user.root_org_id');
            $builder->like('roles', 'FRAC_ADMIN');
            if ($orgName != '') {
                $builder->where('org_name', $orgName);
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

    public function getFracCompetencyMemberList($orgName, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            $table = new \CodeIgniter\View\Table();

            $builder = $this->db->table('master_user');
            $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, phone,created_date,roles');
            $builder->join(' master_organization', 'master_organization.root_org_id = master_user.root_org_id');
            $builder->like('roles', 'FRAC_COMPETENCY_MEMBER');
            if ($orgName != '') {
                $builder->where('org_name', $orgName);
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

    public function getFRACL1List($orgName, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            $table = new \CodeIgniter\View\Table();

            $builder = $this->db->table('master_user');
            $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, phone,created_date,roles');
            $builder->join(' master_organization', 'master_organization.root_org_id = master_user.root_org_id');
            $builder->like('roles', 'FRAC_REVIEWER_L1');
            if ($orgName != '') {
                $builder->where('org_name', $orgName);
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

    public function getFRACL2List($orgName, $limit, $offset, $search, $orderBy, $orderDir)
    {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, phone,created_date,roles');
        $builder->join(' master_organization', 'master_organization.root_org_id = master_user.root_org_id');
        $builder->like('roles', 'FRAC_REVIEWER_L2');
        if ($orgName != '') {
            $builder->where('org_name', $orgName);
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
    }

    public function getIFUMemberList($orgName, $limit, $offset, $search, $orderBy, $orderDir)
    {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, phone,created_date,roles');
        $builder->join(' master_organization', 'master_organization.root_org_id = master_user.root_org_id');
        $builder->like('roles', 'IFU_MEMBER');
        if ($orgName != '') {
            $builder->where('org_name', $orgName);
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
    }

    public function getPublicList($orgName, $limit, $offset, $search, $orderBy, $orderDir)
    {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, phone,created_date,roles');
        $builder->join(' master_organization', 'master_organization.root_org_id = master_user.root_org_id');
        $builder->like('roles', 'PUBLIC');
        if ($orgName != '') {
            $builder->where('org_name', $orgName);
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
    }

    public function getSPVAdminList($orgName, $limit, $offset, $search, $orderBy, $orderDir)
    {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, phone,created_date,roles');
        $builder->join(' master_organization', 'master_organization.root_org_id = master_user.root_org_id');
        $builder->like('roles', 'SPV_ADMIN');
        if ($orgName != '') {
            $builder->where('org_name', $orgName);
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
    }

    public function getStateAdminList($orgName, $limit, $offset, $search, $orderBy, $orderDir)
    {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, phone,created_date,roles');
        $builder->join(' master_organization', 'master_organization.root_org_id = master_user.root_org_id');
        $builder->like('roles', 'STATE_ADMIN');
        if ($orgName != '') {
            $builder->where('org_name', $orgName);
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
    }

    public function getWATMemberList($orgName, $limit, $offset, $search, $orderBy, $orderDir)
    {
        $table = new \CodeIgniter\View\Table();

        $builder = $this->db->table('master_user');
        $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, phone,created_date,roles');
        $builder->join(' master_organization', 'master_organization.root_org_id = master_user.root_org_id');
        $builder->like('roles', 'WAT_MEMBER');
        if ($orgName != '') {
            $builder->where('org_name', $orgName);
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
    }



}

?>