<?php
include 'config.php';

## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = $_POST['search']['value']; // Search value

$searchArray = array();

## Search 
$searchQuery = " ";
if($searchValue != ''){
   $searchQuery = " AND (course_id = :course_id ) ";
   $searchArray = array( 
        'course_id'=>"%$searchValue%"
   );
}

## Total number of records without filtering
$stmt = $conn->prepare("SELECT COUNT(*) AS allcount FROM user_enrolment_course ");
$stmt->execute();
$records = $stmt->fetch();
$totalRecords = $records['allcount'];

## Total number of records with filtering
$stmt = $conn->prepare("SELECT COUNT(*) AS allcount FROM user_enrolment_course WHERE 1 ".$searchQuery);
$stmt->execute($searchArray);
$records = $stmt->fetch();
$totalRecordwithFilter = $records['allcount'];



## Fetch records
$stmt = $conn->prepare("SELECT concat(first_name,' ',last_name) as name, designation,org_name,email_id, status,completion_percentage,completed_on FROM master_user, user_enrolment_course WHERE master_user.user_id= user_enrolment_course.user_id  ".$searchQuery." ORDER BY ".$name." ".$columnSortOrder);

// Bind values
foreach($searchArray as $key=>$search){
   $stmt->bindValue(':'.$key, $search,PDO::PARAM_STR);
}

$stmt->execute();
$report = $stmt->fetchAll();

$data = array();

foreach($report as $row){
   $data[] = array(
      "name"=>$row['name'],
      "designation"=>$row['designation'],
      "org_name"=>$row['org_name'],
      "email_id"=>$row['email_id'],
      "status"=>$row['status'],
      "completion_percentage"=>$row['completion_percentage'],
      "completed_on"=>$row['completed_on']
   );
}

## Response
$response = array(
   "draw" => intval($draw),
   "iTotalRecords" => $totalRecords,
   "iTotalDisplayRecords" => $totalRecordwithFilter,
   "aaData" => $data
);

echo json_encode($response);