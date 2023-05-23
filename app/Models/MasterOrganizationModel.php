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

    public function searchOrg($search_key)
    {
        try {
            $builder = $this->db->table('master_organization');
            $builder->select('root_org_id,org_name');
            $builder->where('(SIMILARITY(org_name,\''.$search_key.'\') > 0.1)', NULL, FALSE);
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

            $builder->where('status','Active');
            $builder->distinct();
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

}

?>