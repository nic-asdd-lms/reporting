<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterOrganizationModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        //$this->load->database();
        $db = \Config\Database::connect();
    }


    public function getOrganizations()
    {
        $query = $this->db->query('select distinct root_org_id, org_name from master_organization order by org_name');
        return $query->getResult();
    }


    public function getOrgName($org_id)
    {
        try {
            $builder = $this->db->table('master_organization');
            $builder->select('org_name');
            $builder->where('root_org_id', $org_id);
            $query = $builder->get();

            // echo $org_id,json_encode($query);
            return $query->getRow()->org_name;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getOrganisationCount()
    {
        try {
            $builder = $this->db->table('summary');
            $builder->select('count');
            $builder->where('kpi', 'Organisations Onboarded');
            $query = $builder->get();

            // echo $org_id,json_encode($query);
            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function searchOrg($search_key)
    {
        try {
            $builder = $this->db->table('master_organization');
            $builder->select('root_org_id,org_name');
            $builder->where('(SIMILARITY(org_name,\'' . $search_key . '\') > 0.1)', NULL, FALSE);
            $builder->orderBy('SIMILARITY(org_name,\'' . $search_key . '\') desc');
            $query = $builder->get();

            return $query->getResult();
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function getOrgList($limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            $builder = $this->db->table('master_organization');
            $builder->select('org_name');
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

            // echo $org_id,json_encode($query);


            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getMonthWiseOrgOnboarding($limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            if ($search != '') {
                $likeQuery = " AND (creation_datemmyy LIKE '%" . strtolower($search) . "%' OR creation_datemmyy LIKE '%" . strtoupper($search) . "%' OR creation_datemmyy LIKE '%" . ucfirst($search) . "%' ) ";

            } else {
                $likeQuery = '';
            }
            if ($limit != -1) {
                $limitQuery = ' limit ' . $limit . ' offset ' . $offset;
            } else
                $limitQuery = '';


            $query = $this->db->query('select concat(split_part(creation_datemmyy::TEXT,\'/\', 2),\'/\' ,split_part(creation_datemmyy::TEXT,\'/\', 1)) as created_month,(count(root_org_id)) AS Month_wise_Org_Onboarded from master_organization where status=\'Active\'' . $likeQuery . ' group by created_month  order by ' . (int) $orderBy + 1 . ' ' . $orderDir . $limitQuery);

            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }


    public function getSummaryReport($limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            $builder = $this->db->table('summary');
            $builder->select('*');
            

            if ($search != '') {
                $builder->where("(kpi LIKE '%" . strtolower($search) . "%' OR kpi LIKE '%" . strtoupper($search) . "%' OR kpi LIKE '%" . ucfirst($search) . "%' )", NULL, FALSE);

                
            }
            if ($limit != -1) {
                $builder->limit($limit, $offset);
            } 

            $query = $builder->get();
            


            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getMonthWiseOrgOnboardingChart()
    {
        try {
            $builder = $this->db->table('master_organization');
            $builder->select('to_char(date_trunc(\'MONTH\',to_date(creation_date,\'DD/MM/YYYY\')),\'YYYY/MM\') as creation_month, count(*)');
            $builder->groupBy('creation_month');
            $builder->orderBy('creation_month');
            $query = $builder->get();

            return $query;

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function getMonthWiseTotalOrgChart()
    {
        try {
            $builder = $this->db->table('master_organization');
            $builder->select('distinct to_char(date_trunc(\'month\',to_date(creation_date,\'DD/MM/YYYY\')),\'YYYY/MM\') as creation_month, 
            sum(count(*) ) over (order by to_char(date_trunc(\'month\',to_date(creation_date,\'DD/MM/YYYY\')),\'YYYY/MM\'))');
            $builder->groupBy('creation_month');
            $builder->orderBy('creation_month');
            $query = $builder->get();

            return $query;

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }


}

?>