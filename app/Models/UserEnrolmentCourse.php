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

    public function getCourseWiseEnrolmentReport($course, $org, $limit, $offset, $search, $orderBy, $orderDir)
    {

        $builder = $this->db->table('user_course_enrolment');
        $builder->select('concat(INITCAP(master_user.first_name),\' \',INITCAP(master_user.last_name)) as name, master_user.email, master_organization.org_name, master_user.designation, master_course.course_name, master_course.org_name as cbp_provider, user_course_enrolment.completion_status, user_course_enrolment.completion_percentage, user_course_enrolment.completed_on');
        $builder->join('master_user', 'master_user.user_id = user_course_enrolment.user_id ');
        $builder->join('master_course', 'master_course.course_id = user_course_enrolment.course_id ');
        $builder->join('master_organization', 'master_user.root_org_id = master_organization.root_org_id ');
        $builder->where('user_course_enrolment.course_id', $course);
        $builder->where('master_course.status', 'Live');
        if ($search != '')
            $builder->where("(first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . ucfirst($search) . "%'
                            OR last_name LIKE '%" . strtolower($search) . "%' OR last_name LIKE '%" . strtoupper($search) . "%' OR last_name LIKE '%" . ucfirst($search) . "%'
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR designation LIKE '%" . strtolower($search) . "%' OR designation LIKE '%" . strtoupper($search) . "%' OR designation LIKE '%" . ucfirst($search) . "%'
                            OR master_organization.org_name LIKE '%" . strtolower($search) . "%' OR master_organization.org_name LIKE '%" . strtoupper($search) . "%' OR master_organization.org_name LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);

        if ($org != '') {
            $builder->where('master_user.root_org_id', $org);
        }
        $builder->orderBy((int) $orderBy + 1, $orderDir);
        if ($limit != -1)
            $builder->limit($limit, $offset);

           
        $query = $builder->get();
        
        return $query;

    }

    public function getCourseWiseEnrolmentCount($org, $limit, $offset, $search, $orderBy, $orderDir)
    {
        if ($search != '') {
            $likeQuery = " AND  (course_name LIKE '%" . strtolower($search) . "%' OR course_name LIKE '%" . strtoupper($search) . "%' OR course_name LIKE '%" . ucfirst($search) . "%' )";

        } else {
            $likeQuery = '';
        }
        if ($limit != -1) {
            $limitQuery = ' limit ' . $limit . ' offset ' . $offset;
        } else
            $limitQuery = '';

        if($org != '') {
            $orgQuery = 'AND master_user.root_org_id=\'' . $org . '\'';
        }
        else
            $orgQuery = '';

        
            $query = $this->db->query('SELECT course_name, master_course.org_name, to_date(published_date,\'DD-MM-YYYY\'), durationhms,COUNT(distinct user_course_enrolment.user_id) AS enrolled_count,
            SUM(CASE WHEN user_course_enrolment.completion_status =\'Not Started\' THEN 1 ELSE 0 END) AS not_started,
            SUM(CASE WHEN user_course_enrolment.completion_status =\'In-Progress\' THEN 1 ELSE 0 END) AS in_progress, 
            SUM(CASE WHEN user_course_enrolment.completion_status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count, avg_rating
            FROM user_course_enrolment
            INNER JOIN  master_course ON user_course_enrolment.course_id = master_course.course_id
            INNER JOIN  master_user ON master_user.user_id =user_course_enrolment.user_id
            WHERE master_course.status=\'Live\'' . $orgQuery . $likeQuery . '
            GROUP BY course_name,master_course.org_name,published_date, durationhms,avg_rating
            
            UNION

            SELECT course_name, master_course.org_name, to_date(published_date,\'DD-MM-YYYY\'), durationhms, \'0\' AS enrolled_count, \'0\' AS not_started, \'0\' AS in_progress, 
            \'0\' AS completed_count, \'0\' AS avg_rating 
            FROM master_course
            WHERE status= \'Live\'
            AND course_id NOT IN ( SELECT course_id FROM user_course_enrolment)
            ORDER BY ' . (int) $orderBy + 1 . ' ' . $orderDir . $limitQuery);
        
            

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
        }
        else {
            $orgQuery = ' AND master_user.root_org_id=\'' . $org . '\' ';
        }
            
            $query = $this->db->query('SELECT   course_name,  COUNT(*) AS enrolled_count
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
        $builder = $this->db->table('user_course_enrolment');
        $builder->select('concat(INITCAP(master_user.first_name),\' \',INITCAP(master_user.last_name)) as name, master_user.email, master_organization.org_name, master_user.designation,  course_name, user_course_enrolment.completion_status, completion_percentage, completed_on');
        $builder->join('master_user', 'master_user.user_id = user_course_enrolment.user_id ');
        $builder->join('master_organization', 'master_user.root_org_id = master_organization.root_org_id ');
        $builder->join('master_course', 'master_course.course_id = user_course_enrolment.course_id ');
        $builder->where('master_organization.org_name', $org);
        $builder->where('master_course.status', 'Live');
        if ($search != '')
            $builder->where("(first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . ucfirst($search) . "%' 
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


    public function getUserEnrolmentCountByMDO($org, $limit, $offset, $search, $orderBy, $orderDir)
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


        $query = $this->db->query('SELECT concat(INITCAP(master_user.first_name),\' \',INITCAP(master_user.last_name)) as name, master_user.email, org_name, master_user.designation,COUNT(*) AS enrolled_count
            ,SUM(CASE WHEN user_course_enrolment.completion_status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count
            FROM user_course_enrolment, master_user
            WHERE user_course_enrolment.user_id = master_user.user_id
            AND master_user.org_name=\'' . $org . '\'' . $likeQuery .
            'GROUP BY name, email, org_name, designation
        UNION
            SELECT concat( INITCAP(master_user.first_name),\' \',INITCAP(master_user.last_name)) as name, master_user.email, org_name, master_user.designation,0 AS enrolled_count,0 AS completed_count
            FROM  master_user
            WHERE master_user.org_name=\'' . $org . '\' ' . $likeQuery .
            'AND master_user.user_id NOT IN (SELECT DISTINCT user_id from user_course_enrolment)
            ORDER BY ' . (int) $orderBy + 1 . ' ' . $orderDir . $limitQuery);


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
            $builder->where("(first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . ucfirst($search) . "%' 
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

    public function getTopUserEnrolment($topCount, $limit, $offset, $search, $orderBy, $orderDir)
    {
        if ($search != '') {
            $likeQuery = " AND (first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . strtoupper($search) . "%' OR first_name LIKE '%" . ucfirst($search) . "%' 
                            OR last_name LIKE '%" . strtolower($search) . "%' OR last_name LIKE '%" . strtoupper($search) . "%' OR last_name LIKE '%" . ucfirst($search) . "%'
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR master_organization.org_name LIKE '%" . strtolower($search) . "%' OR master_organization.org_name LIKE '%" . strtoupper($search) . "%' OR master_organization.org_name LIKE '%" . ucfirst($search) . "%') ";

        } else {
            $likeQuery = '';
        }
        if ($limit != -1) {
            $limitQuery = ' limit ' . min($limit, $topCount - $offset) . ' offset ' . $offset;
        } else
            $limitQuery = ' limit ' . $topCount - $offset . ' offset ' . $offset;


        $query = $this->db->query('SELECT concat(INITCAP(master_user.first_name),\' \',INITCAP(master_user.last_name)) as name, email, master_organization.org_name, COUNT(*) AS enrolled_count
            FROM user_course_enrolment, master_user, master_organization
            WHERE user_course_enrolment.user_id = master_user.user_id
            AND master_organization.root_org_id = master_user.root_org_id ' . $likeQuery .
            'GROUP BY name, email, master_organization.org_name
            ORDER BY enrolled_count desc ' . $limitQuery);


        return $query;
    }

    public function getTopUserCompletion($topCount, $limit, $offset, $search, $orderBy, $orderDir)
    {
        if ($search != '') {
            $likeQuery = " AND (first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . strtoupper($search) . "%' OR first_name LIKE '%" . ucfirst($search) . "%' 
                            OR last_name LIKE '%" . strtolower($search) . "%' OR last_name LIKE '%" . strtoupper($search) . "%' OR last_name LIKE '%" . ucfirst($search) . "%'
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR master_organization.org_name LIKE '%" . strtolower($search) . "%' OR master_organization.org_name LIKE '%" . strtoupper($search) . "%' OR master_organization.org_name LIKE '%" . ucfirst($search) . "%') ";

        } else {
            $likeQuery = '';
        }
        if ($limit != -1) {
            $limitQuery = ' limit ' . min($limit, $topCount - $offset) . ' offset ' . $offset;
        } else
            $limitQuery = ' limit ' . $topCount - $offset . ' offset ' . $offset;


        $query = $this->db->query('SELECT concat(INITCAP(master_user.first_name),\' \',INITCAP(master_user.last_name)) as name,  email, master_organization.org_name, 
        SUM(CASE WHEN user_course_enrolment.completion_status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count
            FROM user_course_enrolment, master_user, master_organization
            WHERE user_course_enrolment.user_id = master_user.user_id
            AND master_organization.root_org_id = master_user.root_org_id ' . $likeQuery .
            'GROUP BY name, email, master_organization.org_name
            ORDER BY completed_count desc ' . $limitQuery);


        return $query;
    }

    public function getTopUserNotStarted($topCount, $limit, $offset, $search, $orderBy, $orderDir)
    {
        if ($search != '') {
            $likeQuery = " AND (first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . strtoupper($search) . "%' OR first_name LIKE '%" . ucfirst($search) . "%' 
                            OR last_name LIKE '%" . strtolower($search) . "%' OR last_name LIKE '%" . strtoupper($search) . "%' OR last_name LIKE '%" . ucfirst($search) . "%'
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR master_organization.org_name LIKE '%" . strtolower($search) . "%' OR master_organization.org_name LIKE '%" . strtoupper($search) . "%' OR master_organization.org_name LIKE '%" . ucfirst($search) . "%') ";

        } else {
            $likeQuery = '';
        }
        if ($limit != -1) {
            $limitQuery = ' limit ' . min($limit, $topCount - $offset) . ' offset ' . $offset;
        } else
            $limitQuery = ' limit ' . $topCount - $offset . ' offset ' . $offset;


        $query = $this->db->query('SELECT concat(INITCAP(master_user.first_name),\' \',INITCAP(master_user.last_name)) as name, email, master_organization.org_name, 
        SUM(CASE WHEN user_course_enrolment.completion_status =\'Not Started\' THEN 1 ELSE 0 END) AS not_started
            FROM user_course_enrolment, master_user, master_organization
            WHERE user_course_enrolment.user_id = master_user.user_id
            AND master_organization.root_org_id = master_user.root_org_id ' . $likeQuery .
            'GROUP BY name, email, master_organization.org_name
            ORDER BY not_started desc ' . $limitQuery);


        return $query;
    }

    public function getTopUserInProgress($topCount, $limit, $offset, $search, $orderBy, $orderDir)
    {
        if ($search != '') {
            $likeQuery = " AND (first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . strtoupper($search) . "%' OR first_name LIKE '%" . ucfirst($search) . "%' 
                            OR last_name LIKE '%" . strtolower($search) . "%' OR last_name LIKE '%" . strtoupper($search) . "%' OR last_name LIKE '%" . ucfirst($search) . "%'
                            OR email LIKE '%" . strtolower($search) . "%' OR email LIKE '%" . strtoupper($search) . "%' OR email LIKE '%" . ucfirst($search) . "%'
                            OR master_organization.org_name LIKE '%" . strtolower($search) . "%' OR master_organization.org_name LIKE '%" . strtoupper($search) . "%' OR master_organization.org_name LIKE '%" . ucfirst($search) . "%') ";

        } else {
            $likeQuery = '';
        }
        if ($limit != -1) {
            $limitQuery = ' limit ' . min($limit, $topCount - $offset) . ' offset ' . $offset;
        } else
            $limitQuery = ' limit ' . $topCount - $offset . ' offset ' . $offset;


        $query = $this->db->query('SELECT concat(INITCAP(master_user.first_name),\' \',INITCAP(master_user.last_name)) as name, email, master_organization.org_name, 
        SUM(CASE WHEN user_course_enrolment.completion_status =\'In-Progress\' THEN 1 ELSE 0 END) AS in_progress
            FROM user_course_enrolment, master_user, master_organization
            WHERE user_course_enrolment.user_id = master_user.user_id
            AND master_organization.root_org_id = master_user.root_org_id ' . $likeQuery .
            'GROUP BY name, email, master_organization.org_name
            ORDER BY in_progress desc ' . $limitQuery);


        return $query;
    }

    public function getTopOrgEnrolment($topCount, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {

            $builder = $this->db->table('user_course_enrolment');
            $builder->join('master_user', 'master_user.user_id = user_course_enrolment.user_id');
            $builder->join('master_organization', 'master_user.root_org_id = master_organization.root_org_id ');
            $builder->select('master_organization.org_name, count(distinct user_course_enrolment.user_id) as enrol_count');
            // $builder->where(' org_name IS NOT NULL');
            if ($search != '') {

                $builder->like('master_organization.org_name', strtolower($search));
                $builder->orLike('master_organization.org_name', strtoupper($search));
                $builder->orLike('master_organization.org_name', ucfirst($search));
            }

            $builder->groupBy('master_organization.org_name');
            $builder->orderBy('enrol_count', 'desc');
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

    public function getTopOrgCompletion($topCount, $limit, $offset, $search, $orderBy, $orderDir)
    {
        try {
            $table = new \CodeIgniter\View\Table();

            $builder = $this->db->table('user_course_enrolment');
            $builder->join('master_user', 'master_user.user_id = user_course_enrolment.user_id');
            $builder->join('master_organization', 'master_user.root_org_id = master_organization.root_org_id ');
            $builder->select('master_organization.org_name, SUM(CASE WHEN user_course_enrolment.completion_status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count');
            // $builder->where(' org_name IS NOT NULL');
            if ($search != '') {

                $builder->like('master_organization.org_name', strtolower($search));
                $builder->orLike('master_organization.org_name', strtoupper($search));
                $builder->orLike('master_organization.org_name', ucfirst($search));
            }

            $builder->groupBy('master_organization.org_name');
            $builder->orderBy('completed_count', 'desc');
            if ($limit != -1)
                $builder->limit(min($topCount-$offset, $limit), $offset);
            else
                $builder->limit($topCount-$offset, $offset);
            $query = $builder->get();
            // print_r($builder);
            return $query;

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getTopCourseEnrolment($topCount, $limit, $offset, $search, $orderBy, $orderDir)
    {
        $builder = $this->db->table('user_course_enrolment');
        $builder->join('master_user', 'master_user.user_id = user_course_enrolment.user_id');
        $builder->join('master_course', 'master_course.course_id = user_course_enrolment.course_id ');
        $builder->select('master_course. course_name, count(distinct user_course_enrolment.user_id) as enroll_count');
        // $builder->where(' org_name IS NOT NULL');
        if ($search != '') {

            $builder->like('master_course.course_name', strtolower($search));
            $builder->orLike('master_course.course_name', strtoupper($search));
            $builder->orLike('master_course.course_name', ucfirst($search));
        }

        $builder->groupBy('master_course.course_name');
        $builder->orderBy('enroll_count', 'desc');
        if ($limit != -1)
            $builder->limit(min($topCount - $offset, $limit), $offset);
        else
            $builder->limit($topCount - $offset, $offset);

        $query = $builder->get();
        // print_r($builder);
        return $query;
    }

    public function getTopCourseCompletion($topCount, $limit, $offset, $search, $orderBy, $orderDir)
    {
        $builder = $this->db->table('user_course_enrolment');
        $builder->join('master_user', 'master_user.user_id = user_course_enrolment.user_id');
        $builder->join('master_course', 'master_course.course_id = user_course_enrolment.course_id ');
        $builder->select('master_course.course_name, SUM(CASE WHEN user_course_enrolment.completion_status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count');
        // $builder->where(' org_name IS NOT NULL');
        if ($search != '') {

            $builder->like('master_course.course_name', strtolower($search));
            $builder->orLike('master_course.course_name', strtoupper($search));
            $builder->orLike('master_course.course_name', ucfirst($search));
        }

        $builder->groupBy('master_course.course_name');
        $builder->orderBy('completed_count', 'desc');
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

            $builder = $this->db->table('user_course_enrolment');
            $builder->join('master_user', 'master_user.user_id = user_course_enrolment.user_id');
            $builder->join('master_organization', 'master_user.root_org_id = master_organization.root_org_id ');
            $builder->join('master_course', 'master_course.course_id = user_course_enrolment.course_id ');
            $builder->select('master_organization.org_name, SUM(CASE WHEN user_course_enrolment.completion_status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count');
            $builder->where('user_course_enrolment.course_id', $course);
            $builder->where('master_course.status', 'Live');
            $builder->groupBy('master_organization.org_name');
            $builder->orderBy('completed_count', 'desc');

            if ($search != '') {

                $builder->like('master_organization.org_name', strtolower($search));
                $builder->orLike('master_organization.org_name', strtoupper($search));
                $builder->orLike('master_organization.org_name', ucfirst($search));
            }

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


            if ($search != '') {

                $builder->like('master_organization.org_name', strtolower($search));
                $builder->orLike('master_organization.org_name', strtoupper($search));
                $builder->orLike('master_organization.org_name', ucfirst($search));
            }

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
            $builder->where(" (course_name LIKE '%" . strtolower($search) . "%' OR course_name LIKE '%" . strtolower($search) . "%' OR course_name LIKE '%" . ucfirst($search) . "%'
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

    public function getUserEnrolmentFull( $limit, $offset, $search, $orderBy, $orderDir)
    {

        $builder = $this->db->table('enrolment');
        $builder->select('*');
        if ($search != '')
            $builder->where("(first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . strtolower($search) . "%' OR first_name LIKE '%" . ucfirst($search) . "%' 
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

    public function getRozgarMelaReport( $limit, $offset, $search, $orderBy, $orderDir)
    {

        $builder = $this->db->table('user_course_enrolment');
        $builder->select('  master_organization.org_name, count(distinct user_course_enrolment.user_id) as rozgarmela_users ,
                            count(user_course_enrolment.course_id) as kp_course_enrolments,
                            SUM(CASE WHEN user_course_enrolment.completion_status =\'Not Started\' THEN 1 ELSE 0 END) AS not_started,
                            SUM(CASE WHEN user_course_enrolment.completion_status =\'In-Progress\' THEN 1 ELSE 0 END) AS in_progress,
                            SUM(CASE WHEN user_course_enrolment.completion_status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count');
        $builder->join('master_user', 'master_user.user_id = user_course_enrolment.user_id ');
        $builder->join('course_curated', 'course_curated.course_id = user_course_enrolment.course_id ');
        $builder->join('master_organization', 'master_user.root_org_id = master_organization.root_org_id ');
        $builder->where('curated_id', 'do_11367399557473075211');
        $builder->like('email', '.kb@karmayogi.in');
        if ($search != '')
            $builder->where("(master_organization.org_name LIKE '%" . strtolower($search) . "%' OR master_organization.org_name LIKE '%" . strtoupper($search) . "%' OR master_organization.org_name LIKE '%" . ucfirst($search) . "%')", NULL, FALSE);

        $builder->groupBy('master_organization.org_name');
        $builder->orderBy((int) $orderBy + 1, $orderDir);
        if ($limit != -1)
            $builder->limit($limit, $offset);

        
        $query = $builder->get();
        
        return $query;

    }

    public function getRozgarMelaSummary( $limit, $offset, $search, $orderBy, $orderDir)
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
            AND master_course.course_id = user_course_enrolment.course_id 
            GROUP BY  course_name
            ORDER BY ' . (int) $orderBy + 1 . ' ' . $orderDir . $limitQuery);
        
        return $query;

    }
}

?>