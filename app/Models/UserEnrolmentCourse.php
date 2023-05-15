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
        $builder->select('concat(master_user.first_name,\' \',master_user.last_name) as name, master_user.email, master_organization.org_name, master_user.designation, master_course.course_name,  user_course_enrolment.completion_status, user_course_enrolment.completion_percentage, user_course_enrolment.completed_on');
        $builder->join('master_user', 'master_user.user_id = user_course_enrolment.user_id ');
        $builder->join('master_course', 'master_course.course_id = user_course_enrolment.course_id ');
        $builder->join('master_organization', 'master_user.root_org_id = master_organization.root_org_id ');
        $builder->where('user_course_enrolment.course_id', $course);
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

        if ($org == '') {
            $query = $this->db->query('SELECT course_name, master_course.org_name, to_date(published_date,\'DD-MM-YYYY\'), durationhms,COUNT(distinct user_course_enrolment.user_id) AS enrolled_count,
            SUM(CASE WHEN user_course_enrolment.completion_status =\'Not Started\' THEN 1 ELSE 0 END) AS not_started,
            SUM(CASE WHEN user_course_enrolment.completion_status =\'In-Progress\' THEN 1 ELSE 0 END) AS in_progress, 
            SUM(CASE WHEN user_course_enrolment.completion_status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count, avg_rating
            FROM user_course_enrolment
            INNER JOIN  master_course ON user_course_enrolment.course_id = master_course.course_id
            WHERE master_course.status=\'Live\'' . $likeQuery . '
            GROUP BY course_name,master_course.org_name,published_date, durationhms,avg_rating
            ORDER BY ' . (int) $orderBy + 1 . ' ' . $orderDir . $limitQuery);
        } else {
            $query = $this->db->query('SELECT course_name, master_course.org_name,  to_date(published_date,\'DD-MM-YYYY\'), durationhms,COUNT(distinct user_course_enrolment.user_id) AS enrolled_count,
            SUM(CASE WHEN user_course_enrolment.completion_status =\'Not Started\' THEN 1 ELSE 0 END) AS not_started,
            SUM(CASE WHEN user_course_enrolment.completion_status =\'In-Progress\' THEN 1 ELSE 0 END) AS in_progress, 
            SUM(CASE WHEN user_course_enrolment.completion_status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count, avg_rating
            FROM user_course_enrolment
            INNER JOIN  master_course ON user_course_enrolment.course_id = master_course.course_id
            INNER JOIN  master_user ON master_user.user_id =user_course_enrolment.user_id
            WHERE master_user.root_org_id=\'' . $org . '\'
            AND master_course.status=\'Live\'' . $likeQuery . '
            GROUP BY course_name,master_course.org_name,published_date, durationhms,avg_rating
            ORDER BY ' . (int) $orderBy + 1 . ' ' . $orderDir . $limitQuery);
        }

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

        $query = $this->db->query('SELECT DISTINCT concat(first_name, \' \', last_name) as name, "email", "master_organization"."org_name", "designation", "course_name", "completion_status", 
        "completion_percentage", "completed_on" 
        FROM "user_course_enrolment" 
        JOIN "master_user" ON "master_user"."user_id" = "user_course_enrolment"."user_id" 
        JOIN "master_organization" ON "master_user"."root_org_id" = "master_organization"."root_org_id" 
        JOIN "course_curated" ON "course_curated"."course_id" = "user_course_enrolment"."course_id" 
        JOIN "master_course" ON "master_course"."course_id" = "user_course_enrolment"."course_id" 
        JOIN "master_curated_collection" ON "course_curated"."curated_id" = "master_curated_collection"."curated_id" 
        WHERE "master_curated_collection"."curated_id" = \'' . $collection . '\' 
        AND "course_curated"."type" = \'Course\' ' . $likeQuery . $whereOrg. '
        
        UNION
        
        SELECT DISTINCT concat(first_name, \' \', last_name) as name, "email", "master_organization"."org_name", "designation", "course_name", "completion_status", 
        "completion_percentage", "completed_on" 
        FROM "user_course_enrolment" 
		JOIN "master_user" ON "master_user"."user_id" = "user_course_enrolment"."user_id" 
        JOIN "master_organization" ON "master_user"."root_org_id" = "master_organization"."root_org_id" 
        JOIN "master_course" ON "master_course"."course_id" = "user_course_enrolment"."course_id" 
		JOIN "course_curated" a ON "a"."course_id" = "user_course_enrolment"."course_id" 
        JOIN "course_curated" b ON "a"."curated_id" = "b"."course_id" 
        WHERE "b"."curated_id" = \'' . $collection . '\'  
        AND "b"."type" = \'CuratedCollections\' ' . $likeQuery . $whereOrg.'
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
            $query = $this->db->query('SELECT  course_name,  COUNT(*) AS enrolled_count
      ,SUM(CASE WHEN user_course_enrolment.completion_status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count
            FROM user_course_enrolment, master_curated_collection, course_curated, master_user, master_course
            WHERE master_curated_collection.curated_id=\'' . $course . '\'
			AND user_course_enrolment.course_id = course_curated.course_id
            AND master_curated_collection.curated_id = course_curated.curated_id
            AND master_user.user_id = user_course_enrolment.user_id
            AND master_course.course_id = user_course_enrolment.course_id 
            AND "course_curated"."type" = \'Course\' ' . $likeQuery . '
            GROUP BY  course_name
        
            UNION
        
            SELECT  course_name,  COUNT(*) AS enrolled_count
      ,SUM(CASE WHEN user_course_enrolment.completion_status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count
            FROM user_course_enrolment, master_curated_collection, course_curated a, course_curated b, master_user, master_course
            WHERE  master_curated_collection.curated_id=\'' . $course . '\'
			AND user_course_enrolment.course_id = b.course_id
            AND master_curated_collection.curated_id = a.curated_id
            AND master_user.user_id = user_course_enrolment.user_id
            AND master_course.course_id = user_course_enrolment.course_id 
            AND "b"."curated_id" = a.course_id  
        
        AND "a"."type" = \'CuratedCollections\' ' . $likeQuery . '
        GROUP BY  course_name
            ORDER BY ' . (int) $orderBy + 1 . ' ' . $orderDir . $limitQuery);
        } else {
            $query = $this->db->query('SELECT  course_name,  COUNT(*) AS enrolled_count
            ,SUM(CASE WHEN user_course_enrolment.completion_status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count
                  FROM user_course_enrolment, master_curated_collection, course_curated, master_user, master_course
                  WHERE master_curated_collection.curated_id=\'' . $course . '\'
                  AND user_course_enrolment.course_id = course_curated.course_id
                  AND master_curated_collection.curated_id = course_curated.curated_id
                  AND master_user.user_id = user_course_enrolment.user_id
                  AND master_course.course_id = user_course_enrolment.course_id 
                  AND master_user.root_org_id=\'' . $org . '\'
                    AND "course_curated"."type" = \'Course\' ' . $likeQuery . '
                  GROUP BY  course_name
              
                  UNION
              
                  SELECT  course_name,  COUNT(*) AS enrolled_count
                    ,SUM(CASE WHEN user_course_enrolment.completion_status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count
                    FROM user_course_enrolment, master_curated_collection, course_curated a, course_curated b, master_user, master_course
                    WHERE  master_curated_collection.curated_id=\'' . $course . '\'
                    AND user_course_enrolment.course_id = b.course_id
                    AND master_curated_collection.curated_id = a.curated_id
                    AND master_user.user_id = user_course_enrolment.user_id
                    AND master_course.course_id = user_course_enrolment.course_id 
                    AND "b"."curated_id" = a.course_id 
                    AND master_user.root_org_id=\'' . $org . '\'
                    AND "a"."curated_id" = b.course_id  
                    AND "a"."type" = \'CuratedCollections\' ' . $likeQuery . '
                    GROUP BY  course_name
                    ORDER BY ' . (int) $orderBy + 1 . ' ' . $orderDir . $limitQuery);



        }
        return $query;

    }


    public function getEnrolmentByOrg($org, $limit, $offset, $search, $orderBy, $orderDir)
    {
        $table = new \CodeIgniter\View\Table();
        $builder = $this->db->table('user_course_enrolment');
        $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, course_name, user_course_enrolment.completion_status, completion_percentage, completed_on');
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


        $query = $this->db->query('SELECT concat(first_name,\' \',last_name) as name, email, org_name, designation,COUNT(*) AS enrolled_count
            ,SUM(CASE WHEN user_course_enrolment.completion_status =\'Completed\' THEN 1 ELSE 0 END) AS completed_count
            FROM user_course_enrolment, master_user
            WHERE user_course_enrolment.user_id = master_user.user_id
            AND master_user.org_name=\'' . $org . '\'' . $likeQuery .
            'GROUP BY name, email, org_name, designation
        UNION
            SELECT concat(first_name,\' \',last_name) as name, email, org_name, designation,0 AS enrolled_count,0 AS completed_count
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
        $builder->select('concat(first_name,\' \',last_name) as name, email, master_organization.org_name, designation, course_name, user_course_enrolment.completion_status, completion_percentage, completed_on');
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





}

?>