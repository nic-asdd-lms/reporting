<?php

namespace App\Models;

use CodeIgniter\Model;

class UserEnrolmentCourse extends Model
{
    public function __construct()
    {
        parent::__construct();
        $db = \Config\Database::connect();
    }

    public function getEnrolmentCount()
    {
        try {
            $builder = $this->db->table('summary');
            $builder->select('count');
            $builder->where('kpi', 'Enrolment Count');
            $query = $builder->get();

            // echo $org_id,json_encode($query);
            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getCompletionCount()
    {
        try {
            $builder = $this->db->table('summary');
            $builder->select('count');
            $builder->where('kpi', 'Completion Count');
            $query = $builder->get();

            // echo $org_id,json_encode($query);
            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
    public function getUniqueEnrolmentCount()
    {
        try {
            $builder = $this->db->table('summary');
            $builder->select('count');
            $builder->where('kpi', 'Unique users enrolled');
            $query = $builder->get();

            // echo $org_id,json_encode($query);
            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getUniqueCompletionCount()
    {
        try {
            $builder = $this->db->table('summary');
            $builder->select('count');
            $builder->where('kpi', 'Unique users completed');
            $query = $builder->get();

            // echo $org_id,json_encode($query);
            return $query;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }


    public function getCourseWiseEnrolmentReport($course, $org, $limit, $offset, $search, $orderBy, $orderDir)
    {

        $builder = $this->db->table('enrolment');
        $builder->select('name, email, org_name, designation,  completion_status, completion_percentage, completed_on');
        $builder->where('course_id', $course);
        if ($search != '')
            $builder->where("(name LIKE '%" . strtolower($search) . "%' OR name LIKE '%" . strtoupper($search) . "%' OR name LIKE '%" . ucfirst($search) . "%'
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%'
                            OR org_name LIKE '%" . strtolower($search) . "%' OR org_name LIKE '%" . strtoupper($search) . "%' OR org_name LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);

        if ($org != '') {
            $builder->where('root_org_id', $org);
        }
        $builder->orderBy((int) $orderBy + 1, $orderDir);
        if ($limit != -1)
            $builder->limit($limit, $offset);


        $query = $builder->get();

        return $query;

    }

    public function getCourseWiseEnrolmentCount($org, $limit, $offset, $search, $orderBy, $orderDir)
    {
        $builder = $this->db->table('course_enrolment_summary');
        $builder->select('course_name, org_name, published_date, durationhms,enrolled_count, not_started_count, in_progress_count, completed_count, avg_rating');
        if ($search != '')
            $builder->where("(course_name LIKE '%" . strtolower($search) . "%' OR course_name LIKE '%" . strtoupper($search) . "%' OR course_name LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);

        $builder->orderBy((int) $orderBy + 1, $orderDir);
        if ($limit != -1)
            $builder->limit($limit, $offset);


        $query = $builder->get();

        return $query;

    }


    public function getCourseMinistrySummary($course, $limit, $offset, $search, $orderBy, $orderDir)
    {
        if ($search != '') {
            $likeQuery = " WHERE (ministry_name LIKE '%" . strtolower($search) . "%' OR ministry_name LIKE '%" . strtoupper($search) . "%' OR ministry_name LIKE '%" . ucfirst($search) . "%') ";

        } else {
            $likeQuery = '';
        }
        if ($limit != -1) {
            $limitQuery = ' limit ' . $limit . ' offset ' . $offset;
        } else
            $limitQuery = '';

        $queryString = 'with master_ministry as (select distinct(ms_id) as mdo_id, ms_name as mdo_name, ms_name as ministry_name
        from master_org_hierarchy where ms_id is not null),
    master_department as (select distinct(dept_id) as mdo_id, dept_name as mdo_name, ms_name as ministry_name
        from master_org_hierarchy where dept_id is not null),
    master_organisation as (select distinct(org_id) as mdo_id, org_name as mdo_name, ms_name as ministry_name
        from master_org_hierarchy where org_id is not null),
    mdo_master as (select * from master_ministry union select * from master_department union select * from master_organisation),
    course_user_detail as (select mu.user_id, root_org_id as mdo_id, completion_status
        from user_course_enrolment as uce, master_user as mu 
        where uce.user_id = mu.user_id and course_id = \'' . $course . '\')
    select 
        mm.ministry_name, 
        count(cud.user_id)  as enrolled_count,
        count(cud.user_id) filter (where cud.completion_status = \'Not Started\') as not_started,
        count(cud.user_id) filter (where cud.completion_status = \'In-Progress\') as in_progress,
        count(cud.user_id) filter (where cud.completion_status = \'Completed\') as completed_count
    from mdo_master as mm left outer join course_user_detail as cud
    on mm.mdo_id = cud.mdo_id ' . $likeQuery . '
    group by mm.ministry_name
    order by ' . (int) $orderBy + 1 . ' ' . $orderDir . $limitQuery;

        $query = $this->db->query($queryString);


        return $query;

    }


    public function getCollectionWiseEnrolmentReport($collection, $org, $limit, $offset, $search, $orderBy, $orderDir)
    {
        if ($search != '') {
            $likeQuery = " AND (first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . strtoupper($search) . "%' OR first_name LIKE '%" . ucfirst($search) . "%' 
                            OR last_name LIKE '%" . strtolower($search) . "%' OR last_name LIKE '%" . strtoupper($search) . "%' OR last_name LIKE '%" . ucfirst($search) . "%'
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR master_organization.org_name LIKE '%" . strtolower($search) . "%' OR master_organization.org_name LIKE '%" . strtoupper($search) . "%' OR master_organization.org_name LIKE '%" . ucfirst($search) . "%'
                            OR course_name LIKE '%" . strtolower($search) . "%' OR course_name LIKE '%" . strtoupper($search) . "%' OR course_name LIKE '%" . ucfirst($search) . "%'
                            OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%') ";

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
            $whereOrg = 'AND master_user.root_org_id = \'' . $org . '\'';

        $query = $this->db->query('SELECT DISTINCT concat(INITCAP(master_user.first_name),\' \',INITCAP(master_user.last_name)) as name, master_user.email, master_organization.org_name, master_user.designation, "course_name", "completion_status", 
        "completion_percentage", "completed_on" 
        FROM "user_course_enrolment" 
        JOIN "master_user" ON "master_user"."user_id" = "user_course_enrolment"."user_id" 
        JOIN "master_organization" ON "master_user"."root_org_id" = "master_organization"."root_org_id" 
        JOIN "course_curated" ON "course_curated"."course_id" = "user_course_enrolment"."course_id" 
        JOIN "master_course" ON "master_course"."course_id" = "user_course_enrolment"."course_id" 
        JOIN "master_curated_collection" ON "course_curated"."curated_id" = "master_curated_collection"."curated_id" 
        WHERE "master_curated_collection"."curated_id" = \'' . $collection . '\' 
        AND "course_curated"."type" = \'Course\' ' . $likeQuery . $whereOrg . '
        
        UNION
        
        SELECT DISTINCT concat(INITCAP(master_user.first_name),\' \',INITCAP(master_user.last_name)) as name, master_user.email, master_organization.org_name, master_user.designation, "course_name", "completion_status", 
        "completion_percentage", "completed_on" 
        FROM "user_course_enrolment" 
		JOIN "master_user" ON "master_user"."user_id" = "user_course_enrolment"."user_id" 
        JOIN "master_organization" ON "master_user"."root_org_id" = "master_organization"."root_org_id" 
        JOIN "master_course" ON "master_course"."course_id" = "user_course_enrolment"."course_id" 
		JOIN "course_curated" a ON "a"."course_id" = "user_course_enrolment"."course_id" 
        JOIN "course_curated" b ON "a"."curated_id" = "b"."course_id" 
        WHERE "b"."curated_id" = \'' . $collection . '\'  
        AND "b"."type" = \'CuratedCollections\' ' . $likeQuery . $whereOrg . '
        ORDER BY ' . (int) $orderBy + 1 . ' ' . $orderDir . $limitQuery);


        // $builder = $this->db->table('user_course_enrolment');
        // $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, course_name, completion_status, completion_percentage, completed_on');
        // $builder->join('master_user', 'master_user.user_id = user_course_enrolment.user_id ');
        // $builder->join('master_organization', 'master_user.root_org_id = master_organization.root_org_id ');
        // $builder->join('course_curated', 'course_curated.course_id = user_course_enrolment.course_id ');
        // $builder->join('master_course', 'master_course.course_id = user_course_enrolment.course_id ');
        // $builder->join('master_curated_collection', 'course_curated.curated_id = master_curated_collection.curated_id ');
        // $builder->where('master_curated_collection.curated_id', $collection);
        // $builder->where('course_curated.type', 'Course');

        // if ($org != '') {
        //     $builder->where('master_user.root_org_id', $org);
        // }
        // $builder->distinct();

        // $builder->orderBy((int) $orderBy + 1, $orderDir);
        // $query = $builder->get();
        
        return $query;

    }

    public function getCollectionWiseEnrolmentCount($course, $org, $limit, $offset, $search, $orderBy, $orderDir)
    {
        if ($search != '') {
            $likeQuery = " AND (course_name LIKE '%" . strtolower($search) . "%' OR course_name LIKE '%" . strtoupper($search) . "%' OR course_name LIKE '%" . ucfirst($search) . "%') ";

        } else {
            $likeQuery = '';
        }
        if ($limit != -1) {
            $limitQuery = ' limit ' . $limit . ' offset ' . $offset;
        } else
            $limitQuery = '';


        if ($org == '') {
            $orgQuery = '';
        } else {
            $orgQuery = ' AND master_user.root_org_id=\'' . $org . '\' ';
        }

        $query = $this->db->query('SELECT   course_name,  COUNT(*) AS enrolled_count
            ,SUM(CASE WHEN user_course_enrolment.completion_status =\'Not Started\' THEN 1 ELSE 0 END) AS not_started_count
            ,SUM(CASE WHEN user_course_enrolment.completion_status =\'In-Progress\' THEN 1 ELSE 0 END) AS in_progress_count
            ,SUM(CASE WHEN user_course_enrolment.completion_status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count
            FROM user_course_enrolment, master_curated_collection, course_curated, master_user, master_course
            WHERE master_curated_collection.curated_id=\'' . $course . '\'
			AND user_course_enrolment.course_id = course_curated.course_id
            AND master_curated_collection.curated_id = course_curated.curated_id
            AND master_user.user_id = user_course_enrolment.user_id
            AND master_course.course_id = user_course_enrolment.course_id 
            AND "course_curated"."type" = \'Course\' ' . $orgQuery . $likeQuery . '
            GROUP BY  course_name
        
            UNION
        
            SELECT   course_name,  COUNT(*) AS enrolled_count
            ,SUM(CASE WHEN user_course_enrolment.completion_status =\'Not Started\' THEN 1 ELSE 0 END) AS not_started_count
            ,SUM(CASE WHEN user_course_enrolment.completion_status =\'In-Progress\' THEN 1 ELSE 0 END) AS in_progress_count
            ,SUM(CASE WHEN user_course_enrolment.completion_status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count
            FROM user_course_enrolment, master_curated_collection, course_curated a, course_curated b, master_user, master_course
            WHERE  master_curated_collection.curated_id=\'' . $course . '\'
			AND user_course_enrolment.course_id = b.course_id
            AND master_curated_collection.curated_id = a.curated_id
            AND master_user.user_id = user_course_enrolment.user_id
            AND master_course.course_id = user_course_enrolment.course_id 
            AND "b"."curated_id" = a.course_id  
        
        AND "a"."type" = \'CuratedCollections\' ' . $orgQuery . $likeQuery . '
        GROUP BY  course_name
            ORDER BY ' . (int) $orderBy + 1 . ' ' . $orderDir . $limitQuery);

        return $query;

    }


    public function getEnrolmentByOrg($org, $limit, $offset, $search, $orderBy, $orderDir)
    {
        $table = new \CodeIgniter\View\Table();
        $builder = $this->db->table('enrolment');
        $builder->select('name, email, org_name, designation,  course_name, completion_status, completion_percentage, completed_on');
        $builder->where('org_name', $org);
        if ($search != '')
            $builder->where("(name LIKE '%" . strtolower($search) . "%' OR name LIKE '%" . strtoupper($search) . "%' OR name LIKE '%" . ucfirst($search) . "%' 
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%'
                            OR course_name LIKE '%" . strtolower($search) . "%' OR course_name LIKE '%" . strtoupper($search) . "%' OR course_name LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);

        $builder->orderBy((int) $orderBy + 1, $orderDir);

        if ($limit != -1)
            $builder->limit($limit, $offset);

        $query = $builder->get();

        return $query;
    }

    public function getUserEnrolmentByMinistry($org, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {

            $builder = $this->db->table('ministry_enrolment');
            $builder->select('name, "email", "ms_name", dept_name,org_name,"designation",  course_name, completion_status, completion_percentage, completed_on');
            $builder->where('ms_name', $org);
            if ($search != '') {
                $builder->where("(name LIKE '%" . strtolower($search) . "%' OR name LIKE '%" . strtoupper($search) . "%' OR name LIKE '%" . ucfirst($search) . "%'
                            OR course_name LIKE '%" . strtolower($search) . "%' OR course_name LIKE '%" . strtoupper($search) . "%' OR course_name LIKE '%" . ucfirst($search) . "%'
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%'
                            OR org_name LIKE '%" . strtolower($search) . "%' OR org_name LIKE '%" . strtoupper($search) . "%' OR org_name LIKE '%" . ucfirst($search) . "%'
                            OR dept_name LIKE '%" . strtolower($search) . "%' OR dept_name LIKE '%" . strtoupper($search) . "%' OR dept_name LIKE '%" . ucfirst($search) . "%'
                                OR completion_status LIKE '%" . strtolower($search) . "%' OR completion_status LIKE '%" . strtoupper($search) . "%' OR completion_status LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);
            }
            $builder->orderBy((int) $orderBy + 1, $orderDir);

            if ($limit != -1) {
                $builder->limit($limit, $offset);
            }

            $query = $builder->get();

            return $query;

            
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }


    public function getUserEnrolmentCountByMDO($org, $limit, $offset, $search, $orderBy, $orderDir)
    {
        $builder = $this->db->table('enrolment_summary');
        $builder->select('*');
        if ($org != '')
            $builder->where('org_name', $org);
        if ($search != '')
            $builder->where("(name LIKE '%" . strtolower($search) . "%' OR name LIKE '%" . strtoupper($search) . "%' OR name LIKE '%" . ucfirst($search) . "%' 
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%'
                            OR org_name LIKE '%" . strtolower($search) . "%' OR org_name LIKE '%" . strtoupper($search) . "%' OR org_name LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);

        if ($limit != -1)
            $builder->limit($limit, $offset);
        $builder->orderBy((int) $orderBy + 1, $orderDir);

        $query = $builder->get();


        return $query;
    }

    public function getEnrolmentCountByOrg($org, $limit, $offset, $search, $orderBy, $orderDir)
    {
        $table = new \CodeIgniter\View\Table();
        $builder = $this->db->table('user_course_enrolment');
        $builder->select('INITCAP(master_user.first_name),\' \',INITCAP(master_user.last_name)) as name, master_user.email, master_organization.org_name, master_user.designation,  course_name, user_course_enrolment.completion_status, completion_percentage, completed_on');
        $builder->join('master_user', 'master_user.user_id = user_course_enrolment.user_id ');
        $builder->join('master_organization', 'master_user.root_org_id = master_organization.root_org_id ');
        $builder->join('master_course', 'master_course.course_id = user_course_enrolment.course_id ');
        $builder->where('master_organization.org_name', $org);
        $builder->where('master_course.status', 'Live');
        if ($search != '')
            $builder->where("(first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . strtoupper($search) . "%' OR first_name LIKE '%" . ucfirst($search) . "%' 
                            OR last_name LIKE '%" . strtolower($search) . "%' OR last_name LIKE '%" . strtoupper($search) . "%' OR last_name LIKE '%" . ucfirst($search) . "%'
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%'
                            OR course_name LIKE '%" . strtolower($search) . "%' OR course_name LIKE '%" . strtoupper($search) . "%' OR course_name LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);

        $builder->orderBy((int) $orderBy + 1, $orderDir);

        if ($limit != -1)
            $builder->limit($limit, $offset);
        $query = $builder->get();

        return $query;
    }

    public function getMonthWiseCourseCompletion($limit, $offset, $search, $orderBy, $orderDir)
    {
        try {

            $builder = $this->db->table('enrolment');
            $builder->select('distinct to_char(date_trunc(\'month\',to_date(completed_on,\'DD/MM/YYYY\')),\'YYYY/MM\') as completed_month, count(*) ');
            $builder->where('completion_status', 'Completed');
            $builder->where('completed_on != \'\'');

            if ($search != '')
                $builder->where('(to_char(date_trunc(\'month\',to_date(completed_on,\'DD/MM/YYYY\')),\'YYYY/MM\') LIKE \'%' . $search . '%\'  )', NULL, FALSE);
            if ($limit != -1)
                $builder->limit($limit, $offset);

            $builder->groupBy('completed_month');
            $builder->orderBy((int) $orderBy + 1, $orderDir);
            
            $query = $builder->get();

            return $query;

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }
    public function getTopUserEnrolment($topCount, $limit, $offset, $search, $orderBy, $orderDir)
    {
        $builder = $this->db->table('enrolment_summary');
        $builder->select('name, email, org_name, enrolled_count');

        // if ($search != '') {
        //     $builder->where("(name LIKE '%" . strtolower($search) . "%' OR name LIKE '%" . strtoupper($search) . "%' OR name LIKE '%" . ucfirst($search) . "%' 
        //                     OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
        //                     OR org_name LIKE '%" . strtolower($search) . "%' OR org_name LIKE '%" . strtoupper($search) . "%' OR org_name LIKE '%" . ucfirst($search) . "%') ", NULL, FALSE);

        // }
        $builder->orderBy('enrolled_count', 'desc');

        if ($limit != -1) {
            $builder->limit(min($limit, $topCount - $offset), $offset);
        } else
            $builder->limit($topCount - $offset, $offset);

        $query = $builder->get();


        return $query;
    }

    public function getTopUserCompletion($topCount, $limit, $offset, $search, $orderBy, $orderDir)
    {
        $builder = $this->db->table('enrolment_summary');
        $builder->select('name, email, org_name, completed_count');

        // if ($search != '') {
        //     $builder->where("(name LIKE '%" . strtolower($search) . "%' OR name LIKE '%" . strtoupper($search) . "%' OR name LIKE '%" . ucfirst($search) . "%' 
        //                     OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
        //                     OR org_name LIKE '%" . strtolower($search) . "%' OR org_name LIKE '%" . strtoupper($search) . "%' OR org_name LIKE '%" . ucfirst($search) . "%') ", NULL, FALSE);

        // }
        $builder->orderBy('completed_count', 'desc');

        if ($limit != -1) {
            $builder->limit(min($limit, $topCount - $offset), $offset);
        } else
            $builder->limit($topCount - $offset, $offset);

        $query = $builder->get();


        return $query;
    }

    public function getTopUserNotStarted($topCount, $limit, $offset, $search, $orderBy, $orderDir)
    {
        $builder = $this->db->table('enrolment_summary');
        $builder->select('name, email, org_name, not_started_count');

        // if ($search != '') {
        //     $builder->where("(name LIKE '%" . strtolower($search) . "%' OR name LIKE '%" . strtoupper($search) . "%' OR name LIKE '%" . ucfirst($search) . "%' 
        //                     OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
        //                     OR org_name LIKE '%" . strtolower($search) . "%' OR org_name LIKE '%" . strtoupper($search) . "%' OR org_name LIKE '%" . ucfirst($search) . "%') ", NULL, FALSE);

        // }
        $builder->orderBy('not_started_count', 'desc');

        if ($limit != -1) {
            $builder->limit(min($limit, $topCount - $offset), $offset);
        } else
            $builder->limit($topCount - $offset, $offset);

        $query = $builder->get();


        return $query;
    }

    public function getTopUserInProgress($topCount, $limit, $offset, $search, $orderBy, $orderDir)
    {
        $builder = $this->db->table('enrolment_summary');
        $builder->select('name, email, org_name, in_progress_count');

        // if ($search != '') {
        //     $builder->where("(name LIKE '%" . strtolower($search) . "%' OR name LIKE '%" . strtoupper($search) . "%' OR name LIKE '%" . ucfirst($search) . "%' 
        //                     OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
        //                     OR org_name LIKE '%" . strtolower($search) . "%' OR org_name LIKE '%" . strtoupper($search) . "%' OR org_name LIKE '%" . ucfirst($search) . "%') ", NULL, FALSE);

        // }
        $builder->orderBy('in_progress_count', 'desc');

        if ($limit != -1) {
            $builder->limit(min($limit, $topCount - $offset), $offset);
        } else
            $builder->limit($topCount - $offset, $offset);

        $query = $builder->get();


        return $query;
    }

    public function getTopOrgEnrolment($topCount, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {

            $builder = $this->db->table('enrolment_summary');
            $builder->select('org_name, sum(enrolled_count) as enrol_count');
            // $builder->where(' org_name IS NOT NULL');
            // if ($search != '') {

            //     $builder->like('org_name', strtolower($search));
            //     $builder->orLike('org_name', strtoupper($search));
            //     $builder->orLike('org_name', ucfirst($search));
            // }

            $builder->groupBy('org_name');
            $builder->orderBy('enrol_count', 'desc');
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

    public function getTopOrgCompletion($topCount, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            $builder = $this->db->table('enrolment_summary');
            $builder->select('org_name, sum(completed_count) as completions');
            // $builder->where(' org_name IS NOT NULL');
            // if ($search != '') {

            //     $builder->like('org_name', strtolower($search));
            //     $builder->orLike('org_name', strtoupper($search));
            //     $builder->orLike('org_name', ucfirst($search));
            // }

            $builder->groupBy('org_name');
            $builder->orderBy('completions', 'desc');
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

    public function getTopCourseEnrolment($topCount, $limit, $offset, $search, $orderBy, $orderDir)
    {
        $builder = $this->db->table('course_enrolment_summary');
        $builder->select('course_name, org_name, sum(enrolled_count) as enroll_count');
        // $builder->where(' org_name IS NOT NULL');
        // if ($search != '') {

        //     $builder->like('course_name', strtolower($search));
        //     $builder->orLike('course_name', strtoupper($search));
        //     $builder->orLike('course_name', ucfirst($search));
        // }

        $builder->groupBy('course_name, org_name');
        $builder->orderBy('enroll_count', 'desc');
        if ($limit != -1)
            $builder->limit(min($topCount - $offset, $limit), $offset);
        else
            $builder->limit($topCount - $offset, $offset);

        $query = $builder->get();
        return $query;
    }

    public function getTopCourseCompletion($topCount, $limit, $offset, $search, $orderBy, $orderDir)
    {
        $builder = $this->db->table('course_enrolment_summary');
        $builder->select('course_name, org_name, sum(completed_count) as completions');
        // $builder->where(' org_name IS NOT NULL');
        // if ($search != '') {

        //     $builder->like('course_name', strtolower($search));
        //     $builder->orLike('course_name', strtoupper($search));
        //     $builder->orLike('course_name', ucfirst($search));
        // }

        $builder->groupBy('course_name, org_name');
        $builder->orderBy('completions', 'desc');
        if ($limit != -1)
            $builder->limit(min($topCount - $offset, $limit), $offset);
        else
            $builder->limit($topCount - $offset, $offset);

        $query = $builder->get();
        return $query;

    }

    public function getTopOrgCourseWise($course, $topCount, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {

            $builder = $this->db->table('enrolment');
            $builder->select('org_name, SUM(CASE WHEN completion_status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count');
            $builder->where('course_id', $course);
            $builder->groupBy('org_name');
            $builder->orderBy('completed_count', 'desc');

            // if ($search != '') {

            //     $builder->like('org_name', strtolower($search));
            //     $builder->orLike('org_name', strtoupper($search));
            //     $builder->orLike('org_name', ucfirst($search));
            // }

            $builder->groupBy('org_name');
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

    public function getTopOrgCollectionWise($course, $topCount, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {

            $db = \Config\Database::connect();
            $courseBuilder = $this->db->table('course_curated'); //  Select course_id where type = course
            $courseBuilder->select('course_id');
            $courseBuilder->where('curated_id', $course);
            $courseBuilder->where('type', 'Course');

            $curatedCourseBuilder = $this->db->table('course_curated a'); //  For nested curated collections 
            $curatedCourseBuilder->join('course_curated b', 'a.course_id = b.curated_id');
            $curatedCourseBuilder->select(' b.course_id');
            $curatedCourseBuilder->where('a.curated_id', $course);
            $curatedCourseBuilder->where('a.type', 'CuratedCollections');

            $courses = $courseBuilder->union($curatedCourseBuilder)->get()->getResultObject();

            $withQuery = 'with completed_users as 
            (select user_course_enrolment.user_id, master_user.root_org_id, master_organization.org_name, user_course_enrolment.course_id , master_course.status
             from user_course_enrolment 
             join master_user on master_user.user_id = user_course_enrolment.user_id 
             join master_course on master_course.course_id = user_course_enrolment.course_id 
             join master_organization on master_user.root_org_id = master_organization.root_org_id 
             where user_course_enrolment.completion_status=\'Completed\'
             and master_course.status = \'Live\'
            ) ';


            $builder = $this->db->table('completed_users a');

            $builder->select('a.org_name, count(distinct a.user_id) as completion_count');

            foreach ($courses as $courseId) {
                $builder->where('exists (select user_id  from completed_users b  where course_id =\'' . $courseId->course_id . '\'  and a.user_id = b.user_id)');

            }
            $builder->groupBy('a.org_name');
            $builder->orderBy('completion_count', 'desc');


            // if ($search != '') {

            //     $builder->like('master_organization.org_name', strtolower($search));
            //     $builder->orLike('master_organization.org_name', strtoupper($search));
            //     $builder->orLike('master_organization.org_name', ucfirst($search));
            // }

            if ($limit != -1)
                $builder->limit(min($topCount - $offset, $limit), $offset);
            else
                $builder->limit($topCount - $offset, $offset);

            $query = $db->query($withQuery . " " . $builder->getCompiledSelect());

            return $query;

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getUserWiseEnrolment($userId, $org, $limit, $offset, $search, $orderBy, $orderDir)
    {

        $builder = $this->db->table('user_course_enrolment');
        $builder->select('master_user.user_id,master_course.course_name, master_course.org_name as cbp_provider, user_course_enrolment.course_id , user_course_enrolment.completion_status, user_course_enrolment.completion_percentage, user_course_enrolment.completed_on, user_course_enrolment.enrolled_date, user_course_enrolment.batch_id');
        $builder->join('master_course', 'master_course.course_id = user_course_enrolment.course_id ');
        $builder->join('master_user', 'master_user.user_id = user_course_enrolment.user_id ');
        $builder->where('user_course_enrolment.user_id', $userId);
        $builder->where('master_course.status', 'Live');
        if ($search != '')
            $builder->where(" (course_name LIKE '%" . strtolower($search) . "%' OR course_name LIKE '%" . strtoupper($search) . "%' OR course_name LIKE '%" . ucfirst($search) . "%'
                            OR completion_status LIKE '%" . strtolower($search) . "%' OR completion_status LIKE '%" . strtoupper($search) . "%' OR completion_status LIKE '%" . ucfirst($search) . "%'
                            OR org_name LIKE '%" . strtolower($search) . "%' OR org_name LIKE '%" . strtoupper($search) . "%' OR org_name LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);

        if ($org != '') {
            $builder->where('master_user.root_org_id', $org);
        }
        $builder->orderBy((int) $orderBy + 1, $orderDir);
        if ($limit != -1)
            $builder->limit($limit, $offset);

        $query = $builder->get();

        return $query;

    }

    public function getUserWiseEnrolmentReport($email, $org, $limit, $offset, $search, $orderBy, $orderDir)
    {

        $builder = $this->db->table('enrolment');
        $builder->select('course_name, course_provider,  completion_status, completion_percentage, completed_on');
        $builder->where('(email = \''. $email.'\' OR phone = \''. $email.'\')' );
        $builder->distinct();
        if ($search != '')
            $builder->where(" (course_name LIKE '%" . strtolower($search) . "%' OR course_name LIKE '%" . strtoupper($search) . "%' OR course_name LIKE '%" . ucfirst($search) . "%'
                            OR completion_status LIKE '%" . strtolower($search) . "%' OR completion_status LIKE '%" . strtoupper($search) . "%' OR completion_status LIKE '%" . ucfirst($search) . "%'
                            OR course_provider LIKE '%" . strtolower($search) . "%' OR course_provider LIKE '%" . strtoupper($search) . "%' OR course_provider LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);

        if ($org != '') {
            $builder->where('root_org_id', $org);
        }
        $builder->orderBy((int) $orderBy + 1, $orderDir);
        if ($limit != -1)
            $builder->limit($limit, $offset);

            
        $query = $builder->get();

        return $query;

    }

    public function getUserEnrolmentFull($limit, $offset, $search, $orderBy, $orderDir)
    {

        $builder = $this->db->table('enrolment');
        $builder->select('*');
        if ($search != '')
            $builder->where("(first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . strtoupper($search) . "%' OR first_name LIKE '%" . ucfirst($search) . "%' 
                            OR last_name LIKE '%" . strtolower($search) . "%' OR last_name LIKE '%" . strtoupper($search) . "%' OR last_name LIKE '%" . ucfirst($search) . "%'
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%'
                            OR course_name LIKE '%" . strtolower($search) . "%' OR course_name LIKE '%" . strtoupper($search) . "%' OR course_name LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);

        $builder->orderBy((int) $orderBy + 1, $orderDir);

        if ($limit != -1)
            $builder->limit($limit, $offset);
        $query = $builder->get();

        return $query;
    }

    public function getRozgarMelaReport($limit, $offset, $search, $orderBy, $orderDir)
    {
        if ($search != '')
            $searchQuery = " AND (master_organization.org_name LIKE '%" . strtolower($search) . "%' OR master_organization.org_name LIKE '%" . strtoupper($search) . "%' OR master_organization.org_name LIKE '%" . ucfirst($search) . "%')";
        else
            $searchQuery = '';

        if ($limit != -1) {
            $limitQuery = ' limit ' . $limit . ' offset ' . $offset;
        } else
            $limitQuery = '';


        $query = $this->db->query('SELECT master_organization.org_name, count(distinct user_course_enrolment.user_id) as rozgarmela_users ,
                            count(user_course_enrolment.course_id) as kp_course_enrolments,
                            SUM(CASE WHEN user_course_enrolment.completion_status =\'Not Started\' THEN 1 ELSE 0 END) AS not_started,
                            SUM(CASE WHEN user_course_enrolment.completion_status =\'In-Progress\' THEN 1 ELSE 0 END) AS in_progress,
                            SUM(CASE WHEN user_course_enrolment.completion_status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count
                            FROM user_course_enrolment
                            JOIN master_user ON master_user.user_id = user_course_enrolment.user_id
                            JOIN course_curated ON course_curated.course_id = user_course_enrolment.course_id 
                            JOIN master_organization ON master_user.root_org_id = master_organization.root_org_id 
                            WHERE curated_id = \'do_11367399557473075211\'
                            AND email LIKE \'%.kb@karmayogi.in\' ' . $searchQuery . '
                            GROUP BY master_organization.org_name

                            UNION

                            SELECT master_organization.org_name, count(distinct user_program_enrolment.user_id) as rozgarmela_users ,
                            count(*) as kp_course_enrolments,
                            SUM(CASE WHEN user_program_enrolment.status =\'Not Started\' THEN 1 ELSE 0 END) AS not_started,
                            SUM(CASE WHEN user_program_enrolment.status =\'In-Progress\' THEN 1 ELSE 0 END) AS in_progress,
                            SUM(CASE WHEN user_program_enrolment.status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count
                            FROM user_program_enrolment
                            JOIN master_user ON master_user.user_id = user_program_enrolment.user_id
                            JOIN master_organization ON master_user.root_org_id = master_organization.root_org_id 
                            WHERE program_id = \'do_1137731307613716481132\' ' . $searchQuery . '
                            GROUP BY master_organization.org_name
                            ORDER BY ' . (int) $orderBy + 1 . ' ' . $orderDir . $limitQuery


        );

        // $builder = $this->db->table('user_course_enrolment');
        // $builder->select('  master_organization.org_name, count(distinct user_course_enrolment.user_id) as rozgarmela_users ,
        //                     count(user_course_enrolment.course_id) as kp_course_enrolments,
        //                     SUM(CASE WHEN user_course_enrolment.completion_status =\'Not Started\' THEN 1 ELSE 0 END) AS not_started,
        //                     SUM(CASE WHEN user_course_enrolment.completion_status =\'In-Progress\' THEN 1 ELSE 0 END) AS in_progress,
        //                     SUM(CASE WHEN user_course_enrolment.completion_status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count');
        // $builder->join('master_user', 'master_user.user_id = user_course_enrolment.user_id ');
        // $builder->join('course_curated', 'course_curated.course_id = user_course_enrolment.course_id ');
        // $builder->join('master_organization', 'master_user.root_org_id = master_organization.root_org_id ');
        // $builder->where('curated_id', 'do_11367399557473075211');
        // $builder->like('email', '.kb@karmayogi.in');

        // if ($search != '')
        //     $builder->where("(master_organization.org_name LIKE '%" . strtolower($search) . "%' OR master_organization.org_name LIKE '%" . strtoupper($search) . "%' OR master_organization.org_name LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);

        // $builder->groupBy('master_organization.org_name');

        // $program = $this->db->table('user_program_enrolment');
        // $program->select('master_organization.org_name, count(distinct user_program_enrolment.user_id) as rozgarmela_users ,
        //                     count(*) as kp_course_enrolments,
        //                     SUM(CASE WHEN user_program_enrolment.status =\'Not Started\' THEN 1 ELSE 0 END) AS not_started,
        //                     SUM(CASE WHEN user_program_enrolment.status =\'In-Progress\' THEN 1 ELSE 0 END) AS in_progress,
        //                     SUM(CASE WHEN user_program_enrolment.status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count');
        // $program->join('master_user', 'master_user.user_id = user_program_enrolment.user_id ');
        // $program->join('master_organization', 'master_user.root_org_id = master_organization.root_org_id ');
        // $program->where('user_program_enrolment.program_id', 'do_1137731307613716481132');
        // if ($search != '')
        //     $program->where("(master_organization.org_name LIKE '%" . strtolower($search) . "%' OR master_organization.org_name LIKE '%" . strtoupper($search) . "%' OR master_organization.org_name LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);

        // $program->groupBy('master_organization.org_name');

        // $builder->union($program);


        // $builder->orderBy((int) $orderBy + 1, $orderDir);
        // if ($limit != -1)
        //     $builder->limit($limit, $offset);


        // $query = $builder->get();

        return $query;

    }

    public function getRozgarMelaSummary($limit, $offset, $search, $orderBy, $orderDir)
    {
        if ($search != '') {
            $likeQuery = " AND (course_name LIKE '%" . strtolower($search) . "%' OR course_name LIKE '%" . strtoupper($search) . "%' OR course_name LIKE '%" . ucfirst($search) . "%') ";

        } else 
        {
            $likeQuery = '';
        }
        if ($limit != -1) {
            $limitQuery = ' limit ' . $limit . ' offset ' . $offset;
        } else
            $limitQuery = '';


        $query = $this->db->query('SELECT course_name,  COUNT(*) AS enrolled_count
      ,SUM(CASE WHEN user_course_enrolment.completion_status =\'Not Started\' THEN 1 ELSE 0 END) AS not_started
      ,SUM(CASE WHEN user_course_enrolment.completion_status =\'In-Progress\' THEN 1 ELSE 0 END) AS in_progress
      ,SUM(CASE WHEN user_course_enrolment.completion_status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count
            FROM user_course_enrolment, master_curated_collection, course_curated, master_user, master_course
            WHERE master_curated_collection.curated_id=\'do_11367399557473075211\'
			AND user_course_enrolment.course_id = course_curated.course_id
            AND master_curated_collection.curated_id = course_curated.curated_id
            AND master_user.user_id = user_course_enrolment.user_id
            AND master_user.email LIKE \'%.kb@karmayogi.in\'
            AND master_course.course_id = user_course_enrolment.course_id ' . $likeQuery. '
            GROUP BY  course_name
            ORDER BY ' . (int) $orderBy + 1 . ' ' . $orderDir . $limitQuery);

        return $query;

    }

    public function getRozgarMelaUserReport($limit, $offset, $search, $orderBy, $orderDir)
    {
        if ($search != '') {
            $likeQuery = " AND (first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . strtoupper($search) . "%' OR first_name LIKE '%" . ucfirst($search) . "%' 
                            OR last_name LIKE '%" . strtolower($search) . "%' OR last_name LIKE '%" . strtoupper($search) . "%' OR last_name LIKE '%" . ucfirst($search) . "%'
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR master_organization.org_name LIKE '%" . strtolower($search) . "%' OR master_organization.org_name LIKE '%" . strtoupper($search) . "%' OR master_organization.org_name LIKE '%" . ucfirst($search) . "%'
                            OR course_name LIKE '%" . strtolower($search) . "%' OR course_name LIKE '%" . strtoupper($search) . "%' OR course_name LIKE '%" . ucfirst($search) . "%'
                            OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%') ";

        } else {
            $likeQuery = '';
        }
        if ($limit != -1) {
            $limitQuery = ' limit ' . $limit . ' offset ' . $offset;
        } else
            $limitQuery = '';

        $courseBuilder = $this->db->table('course_curated'); //  Select course_id where type = course
        $courseBuilder->select('course_id');
        $courseBuilder->where('curated_id', 'do_11367399557473075211');
        $courses = $courseBuilder->get()->getResultObject();


        $withQuery = 'with completed_users as 
        (select user_course_enrolment.user_id, concat(INITCAP(master_user.first_name),\' \',INITCAP(master_user.last_name)) as name, 
         master_user.email, master_organization.org_name, master_user.designation, user_course_enrolment.course_id,completion_status, completed_on
         from user_course_enrolment 
         join master_user on master_user.user_id = user_course_enrolment.user_id 
         join master_course on master_course.course_id = user_course_enrolment.course_id 
         join master_organization on master_user.root_org_id = master_organization.root_org_id

    WHERE "email" LIKE \'%.kb@karmayogi.in\' 
    and user_course_enrolment.completion_status=\'Completed\'
         and master_course.status = \'Live\'
        ),
in_progress_users as 
        (select user_course_enrolment.user_id, concat(INITCAP(master_user.first_name),\' \',INITCAP(master_user.last_name)) as name, 
         master_user.email, master_organization.org_name, master_user.designation,user_course_enrolment.course_id,completion_status, completed_on
         from user_course_enrolment 
         join master_user on master_user.user_id = user_course_enrolment.user_id 
         join master_course on master_course.course_id = user_course_enrolment.course_id 
         join master_organization on master_user.root_org_id = master_organization.root_org_id

         WHERE "email" LIKE \'%.kb@karmayogi.in\' 
    and user_course_enrolment.completion_status=\'In-Progress\'
         and master_course.status = \'Live\'
        ),
not_started_users as 
        (select user_course_enrolment.user_id, concat(INITCAP(master_user.first_name),\' \',INITCAP(master_user.last_name)) as name, 
         master_user.email, master_organization.org_name, master_user.designation,user_course_enrolment.course_id,completion_status, completed_on
         from user_course_enrolment 
         join master_user on master_user.user_id = user_course_enrolment.user_id 
         join master_course on master_course.course_id = user_course_enrolment.course_id 
         join master_organization on master_user.root_org_id = master_organization.root_org_id

         WHERE "email" LIKE \'%.kb@karmayogi.in\' 
    and user_course_enrolment.completion_status=\'Not Started\'
         and master_course.status = \'Live\'
        )';

        $completedBuilder = $this->db->table('completed_users a');

        $completedBuilder->select('name, email, org_name, designation, \'Completed\' as status');
        // $completedBuilder->join('master_user', 'master_user.user_id = a.user_id');
        // $completedBuilder->join('master_organization', 'master_user.root_org_id = master_organization.root_org_id');

        $completedBuilder->distinct();
        foreach ($courses as $courseId) {
            $completedBuilder->where('exists (select user_id  from completed_users b  where course_id =\'' . $courseId->course_id . '\'  and a.user_id = b.user_id)');

        }

        $inProgressBuilder = $this->db->table('in_progress_users a');
        $inProgressBuilder->select('name, email, org_name, designation, \'In-Progress\' as status');
        // $inProgressBuilder->join('master_user', 'master_user.user_id = a.user_id');
        // $inProgressBuilder->join('master_organization', 'master_user.root_org_id = master_organization.root_org_id');

        $inProgressBuilder->distinct();
        $courseArr = [];
        foreach ($courses as $courseId) {
            array_push($courseArr, $courseId->course_id);
        }
        $inProgressBuilder->whereIn('course_id', $courseArr);

        $notStartedBuilder = $this->db->table('not_started_users a');

        $notStartedBuilder->select('name, email, org_name, designation, \'Not Started\' as status');
        // $notStartedBuilder->join('master_user', 'master_user.user_id = a.user_id');
        // $notStartedBuilder->join('master_organization', 'master_user.root_org_id = master_organization.root_org_id');
        $notStartedBuilder->distinct();
        foreach ($courses as $courseId) {
            $notStartedBuilder->where('exists (select user_id  from not_started_users b  where course_id =\'' . $courseId->course_id . '\'  and a.user_id = b.user_id)');

        }

        $programBuilder = $this->db->table('user_program_enrolment');
        $programBuilder->select('concat(INITCAP(master_user.first_name),\' \',INITCAP(master_user.last_name)) as name, master_user.email, 
                                master_organization.org_name, master_user.designation, user_program_enrolment.status');
        $programBuilder->join('master_user', 'master_user.user_id = user_program_enrolment.user_id');
        $programBuilder->join('master_organization', 'master_user.root_org_id = master_organization.root_org_id');
        $programBuilder->where('program_id', 'do_1137731307613716481132');
        $programBuilder->distinct();

        $completedBuilder->union($inProgressBuilder);
        $completedBuilder->union($notStartedBuilder);
        $completedBuilder->union($programBuilder);

        // print_r($withQuery . " " . $completedBuilder->getCompiledSelect() . ' ORDER BY ' . (int) $orderBy + 1 . ' ' . $orderDir . $limitQuery);
        // die;

        $query = $this->db->query($withQuery . " " . $completedBuilder->getCompiledSelect() . ' ORDER BY ' . (int) $orderBy + 1 . ' ' . $orderDir . $limitQuery);

        return $query;



        // $query = $this->db->query($withQuery.'select distinct a.user_id, \'Completed\' as status
        // from completed_users a
        // where exists (select user_id  from completed_users b  where course_id ='do_11359618144357580811'  and a.user_id = b.user_id)
        // and exists (select user_id  from completed_users b  where course_id ='do_113569878939262976132'  and a.user_id = b.user_id)
        // and exists (select user_id  from completed_users b  where course_id ='do_113474579909279744117'  and a.user_id = b.user_id)
        // and exists (select user_id  from completed_users b  where course_id ='do_113651330692145152128'  and a.user_id = b.user_id)
        // and exists (select user_id  from completed_users b  where course_id ='do_1134122937914327041177'  and a.user_id = b.user_id)
        // and exists (select user_id  from completed_users b  where course_id ='do_113473120005832704152'  and a.user_id = b.user_id)
        // and exists (select user_id  from completed_users b  where course_id ='do_1136364244148060161889'  and a.user_id = b.user_id)
        // and exists (select user_id  from completed_users b  where course_id ='do_1136364937253437441916'  and a.user_id = b.user_id)

        // Union

        // select distinct a.user_id, 'In-Progress' as status
        // from in_progress_users a
        // where course_id in ('do_11359618144357580811'  ,'do_113569878939262976132','do_113474579909279744117','do_113651330692145152128',
        // 					'do_1134122937914327041177','do_113473120005832704152','do_1136364244148060161889' ,'do_1136364937253437441916')

        // union 

        // select distinct a.user_id, 'Not Started' as status
        // from not_started_users a
        // where exists (select user_id  from not_started_users b  where course_id ='do_11359618144357580811'  and a.user_id = b.user_id)
        // and exists (select user_id  from not_started_users b  where course_id ='do_113569878939262976132'  and a.user_id = b.user_id)
        // and exists (select user_id  from not_started_users b  where course_id ='do_113474579909279744117'  and a.user_id = b.user_id)
        // and exists (select user_id  from not_started_users b  where course_id ='do_113651330692145152128'  and a.user_id = b.user_id)
        // and exists (select user_id  from not_started_users b  where course_id ='do_1134122937914327041177'  and a.user_id = b.user_id)
        // and exists (select user_id  from not_started_users b  where course_id ='do_113473120005832704152'  and a.user_id = b.user_id)
        // and exists (select user_id  from not_started_users b  where course_id ='do_1136364244148060161889'  and a.user_id = b.user_id)
        // and exists (select user_id  from not_started_users b  where course_id ='do_1136364937253437441916'  and a.user_id = b.user_id) ' . $likeQuery . '

        // UNION

        // SELECT DISTINCT concat(INITCAP(master_user.first_name),\' \',INITCAP(master_user.last_name)) as name, 
        // master_user.email, master_organization.org_name, master_user.designation, "program_name", user_program_enrolment.status, "completed_on" 
        // FROM "user_program_enrolment" 
        // JOIN "master_user" ON "master_user"."user_id" = "user_program_enrolment"."user_id" 
        // JOIN "master_organization" ON "master_user"."root_org_id" = "master_organization"."root_org_id" 
        // JOIN "master_program" ON "master_program"."program_id" = "user_program_enrolment"."program_id" 
        // WHERE "master_program"."program_id" = \'do_1137731307613716481132\'  ' . $likeQuery. '
        // ORDER BY ' . (int) $orderBy + 1 . ' ' . $orderDir . $limitQuery);

        // return $query;

    }

    public function getRozgarMelaUserEnrolmentReport($limit, $offset, $search, $orderBy, $orderDir)
    {
        $table = new \CodeIgniter\View\Table();
        $builder = $this->db->table('rozgar_mela_enrolment');
        $builder->select('name, email, org_name, designation,COUNT(distinct course_id) AS enrolled_count,
        SUM(CASE WHEN completion_status =\'Not Started\' THEN 1 ELSE 0 END) AS not_started,
        SUM(CASE WHEN completion_status =\'In-Progress\' THEN 1 ELSE 0 END) AS in_progress, 
        SUM(CASE WHEN completion_status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count');
        if ($search != '')
            $builder->where("(name LIKE '%" . strtolower($search) . "%' OR name LIKE '%" . strtoupper($search) . "%' OR name LIKE '%" . ucfirst($search) . "%' 
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%'
                            OR org_name LIKE '%" . strtolower($search) . "%' OR org_name LIKE '%" . strtoupper($search) . "%' OR org_name LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);
        $builder->groupBy('name, email,org_name, designation');

        $builder->orderBy((int) $orderBy + 1, $orderDir);

        if ($limit != -1)
            $builder->limit($limit, $offset);
        $query = $builder->get();

        return $query;
    }

    public function getRozgarMelaKpCollectionReport($limit, $offset, $search, $orderBy, $orderDir)
    {
        $builder = $this->db->table('rozgar_mela_enrolment');
        $builder->select('name, email, org_name, designation,
        COUNT(distinct rozgar_mela_enrolment.course_id) AS enrolled_count,
            SUM(CASE WHEN completion_status =\'Not Started\' THEN 1 ELSE 0 END) AS not_started,
            SUM(CASE WHEN completion_status =\'In-Progress\' THEN 1 ELSE 0 END) AS in_progress, 
            SUM(CASE WHEN completion_status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count');
        $builder->join('course_curated', 'course_curated.course_id = rozgar_mela_enrolment.course_id ');
        $builder->join('master_curated_collection', 'course_curated.curated_id = master_curated_collection.curated_id ');
        $builder->where('master_curated_collection.curated_id', 'do_11367399557473075211');
        
        $builder->distinct();

        if ($search != '')
            $builder->where("(name LIKE '%" . strtolower($search) . "%' OR name LIKE '%" . strtoupper($search) . "%' OR name LIKE '%" . ucfirst($search) . "%' 
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%'
                            OR org_name LIKE '%" . strtolower($search) . "%' OR org_name LIKE '%" . strtoupper($search) . "%' OR org_name LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);

        $builder->groupBy('name, email,org_name, designation');

        $builder->orderBy((int) $orderBy + 1, $orderDir);

        if ($limit != -1)
            $builder->limit($limit, $offset);
        $query = $builder->get();

        return $query;

    }

    public function dashboardChart($ati, $program, $isMonthWise)
    {
        $builder = $this->db->table('enrolment');

        $builder->select('completion_status,count(*) as users');

        if ($isMonthWise == true) {
            $builder->where('to_char(to_date(enrolled_date,\'DD/MM/YYYY\'), \'MONTH YYYY\')  = to_char(current_date, \'MONTH YYYY\')');

        }

        $builder->groupBy('completion_status');
        $builder->orderBy('completion_status', 'asc');

        return $builder->get();

    }

    public function dashboardTable($ati, $program, $isMonthWise)
    {
        $builder = $this->db->table('enrolment');
        // $enrolled = $this->db->table('user_course_enrolment');

        $builder->select('completion_status,count(*) as users');
        // $enrolled->select('\'Total Enrolments\' as completion_status,count(*)  as users');
        if ($isMonthWise == true) {
            $builder->where('to_char(to_date(enrolled_date,\'DD/MM/YYYY\'), \'MONTH YYYY\')  = to_char(current_date, \'MONTH YYYY\')');
            // $enrolled->where('to_char(to_date(enrolled_date,\'DD/MM/YYYY\'), \'MONTH YYYY\')  = to_char(current_date, \'MONTH YYYY\')');

        }

        // $builder->union($enrolled);
        $builder->groupBy('completion_status');
        $builder->orderBy('array_position(array[\'Not Started\',\'In-Progress\',\'Completed\'], completion_status::text)');

        return $builder->get();

    }

    public function learnerDashboardTableFooter()
    {
        $builder = $this->db->table('user_course_enrolment');

        $builder->select('count(*)  as users');

        return $builder->get();

    }

    public function getMonthWiseEnrolmentCount()
    {
        try {
            $builder = $this->db->table('enrolment');
            $builder->select('distinct to_char(date_trunc(\'month\',to_date(enrolled_date,\'DD/MM/YYYY\')),\'YYYY/MM\') as enrolled_month, 
            count(*) ');
            // $builder->where('to_date(enrolled_date,\'DD/MM/YYYY\') > current_date - INTERVAL \'1 year\'');
            $builder->groupBy('enrolled_month');
            $builder->orderBy('enrolled_month');
            // echo '<pre>';
            // print_r($builder->getCompiledSelect());
            // die;
            $query = $builder->get();

            return $query;

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    
    public function getEnrolmentMonths()
    {
        try {
            $builder = $this->db->table('enrolment');
            $builder->select('distinct to_char(date_trunc(\'month\',to_date(enrolled_date,\'DD/MM/YYYY\')),\'YYYY/MM\') as enrolled_month');
            $builder->where('to_date(enrolled_date,\'DD/MM/YYYY\') > current_date - INTERVAL \'1 year\'');
            $builder->groupBy('enrolled_month');
            $builder->orderBy('enrolled_month');
            // echo '<pre>';
            // print_r($builder->getCompiledSelect());
            // die;
            $query = $builder->get();

            return $query;

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function getMonthWiseCompletionCount()
    {
        try {
            $builder = $this->db->table('enrolment');
            $builder->select('distinct to_char(date_trunc(\'month\',to_date(completed_on,\'DD/MM/YYYY\')),\'YYYY/MM\') as completed_month, 
            count(*) ');
            $builder->where('completion_status', 'Completed');
            // $builder->where('to_date(completed_on,\'DD/MM/YYYY\') > current_date - INTERVAL \'1 year\'');
            $builder->groupBy('completed_month');
            $builder->orderBy('completed_month');
            // echo '<pre>';
            // print_r($builder->getCompiledSelect());
            // die;
            $query = $builder->get();

            return $query;

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function getCompletionMonths()
    {
        try {
            $builder = $this->db->table('enrolment');
            $builder->select('distinct to_char(date_trunc(\'month\',to_date(completed_on,\'DD/MM/YYYY\')),\'YYYY/MM\') as completed_month ');
            $builder->where('completion_status', 'Completed');
            $builder->where('to_date(completed_on,\'DD/MM/YYYY\') > current_date - INTERVAL \'1 year\'');
            $builder->groupBy('completed_month');
            $builder->orderBy('completed_month');
            // echo '<pre>';
            // print_r($builder->getCompiledSelect());
            // die;
            $query = $builder->get();

            return $query;

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }


    public function getMonthWiseTotalEnrolmentCount()
    {
        try {
            $builder = $this->db->table('enrolment');
            $builder->select('distinct to_char(date_trunc(\'month\',to_date(enrolled_date,\'DD/MM/YYYY\')),\'YYYY/MM\') as enrolled_month, 
            sum(count(*) ) over (order by to_char(date_trunc(\'month\',to_date(enrolled_date,\'DD/MM/YYYY\')),\'YYYY/MM\'))');
            // $builder->where('to_date(enrolled_date,\'DD/MM/YYYY\') > current_date - INTERVAL \'1 year\'');
            $builder->groupBy('enrolled_month');
            $builder->orderBy('enrolled_month');
            $query = $builder->get();

            return $query;

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function getMonthWiseTotalUniqueEnrolmentCount()
    {
        try {
            $builder = $this->db->table('enrolment');
            $builder->select('distinct to_char(date_trunc(\'month\',to_date(enrolled_date,\'DD/MM/YYYY\')),\'YYYY/MM\') as enrolled_month, 
            sum(count(distinct email) ) over (order by to_char(date_trunc(\'month\',to_date(enrolled_date,\'DD/MM/YYYY\')),\'YYYY/MM\'))');
            // $builder->where('to_date(enrolled_date,\'DD/MM/YYYY\') > current_date - INTERVAL \'1 year\'');
            $builder->groupBy('enrolled_month');
            $builder->orderBy('enrolled_month');
            // echo '<pre>';
            // print_r($builder->getCompiledSelect());
            // die;
            $query = $builder->get();

            return $query;

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function getMonthWiseTotalCompletionCount()
    {
        try {
            $builder = $this->db->table('enrolment');
            $builder->select('distinct to_char(date_trunc(\'month\',to_date(completed_on,\'DD/MM/YYYY\')),\'YYYY/MM\') as completed_month, 
            sum(count(*) ) over (order by to_char(date_trunc(\'month\',to_date(completed_on,\'DD/MM/YYYY\')),\'YYYY/MM\'))');
            $builder->where('completion_status', 'Completed');
            // $builder->where('to_date(completed_on,\'DD/MM/YYYY\') > current_date - INTERVAL \'1 year\'');
            $builder->groupBy('completed_month');
            $builder->orderBy('completed_month');
            // echo '<pre>';
            // print_r($builder->getCompiledSelect());
            // die;
            $query = $builder->get();

            return $query;

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function getMonthWiseLearningHours()
    {
        try {
            $builder = $this->db->table('master_course');
            $builder->join('user_course_enrolment', 'user_course_enrolment.course_id = master_course.course_id');
            $builder->select('to_char(date_trunc(\'MONTH\',to_date(completed_on,\'DD/MM/YYYY\')),\'YYYY/MM\') as month, sum(durationh)');
            $builder->where('(status = \'Live\' OR status = \'Retired\')');
            $builder->where('completion_status', 'Completed');
            // $builder->where('to_date(completed_on,\'DD/MM/YYYY\') > current_date - INTERVAL \'1 year\'');
            $builder->groupBy('month');
            $builder->orderBy('month');
            // echo '<pre>';
            // print_r($builder->getCompiledSelect());
            // die;
            $query = $builder->get();

            return $query;

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function getMonthWiseTotalLearningHours()
    {
        try {

            $query = $this->db->query('SELECT month, sum(learninghours) over (order by month ) from (
                SELECT to_char(date_trunc(\'MONTH\',to_date(completed_on,\'DD/MM/YYYY\')),\'YYYY/MM\') as month, sum(durationh) as learninghours
                FROM public.user_course_enrolment
                JOIN master_course on user_course_enrolment.course_id = master_course.course_id
                WHERE (status=\'Live\' OR status = \'Retired\')
                AND completion_status=\'Completed\'
                -- AND to_date(completed_on,\'DD/MM/YYYY\') > current_date - INTERVAL \'1 year\'
                GROUP BY month) a
                ORDER BY month;');

            return $query;

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function getCompletionMonth()
    {
        try {
            $builder = $this->db->table('user_course_enrolment');
            $builder->select('distinct to_char(date_trunc(\'month\',to_date(completed_on,\'DD/MM/YYYY\')),\'MM\') as completed_month');
            $builder->where('completed_on != \'\'');
            // $builder->where('to_date(completed_on,\'DD/MM/YYYY\') > current_date - INTERVAL \'1 year\'');
            $builder->orderBy('completed_month');
            
            $query = $builder->get();

            return $query;

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function getCompletionYear()
    {
        try {
            $builder = $this->db->table('user_course_enrolment');
            $builder->select('distinct to_char(date_trunc(\'month\',to_date(completed_on,\'DD/MM/YYYY\')),\'YYYY\') as completed_year');
            $builder->where('completed_on != \'\'');
            // $builder->where('to_date(completed_on,\'DD/MM/YYYY\') > current_date - INTERVAL \'1 year\'');
            $builder->orderBy('completed_year');
            $query = $builder->get();

            return $query;

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function getTopCourseInMonth($month, $topCount, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            $builder = $this->db->table('user_course_enrolment');
            $builder->join('master_course', 'master_course.course_id = user_course_enrolment.course_id');
            $builder->select('course_name, count(*) ');
            $builder->where('to_char(date_trunc(\'month\',to_date(completed_on,\'DD/MM/YYYY\')),\'YYYY/MM\') = \'' . $month . '\'');
            $builder->where('status', 'Live');
            $builder->groupBy('course_name');
            $builder->orderBy('count', 'desc');
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

}

?>