<?php
include ('includes/config.php');
if (isset($_POST['client_project']) && $_POST['comefrom'] == 'teamname upload') {
	list($client, $team) = explode('-', $_POST['client_project']);
	$teamQry = $team == '' ? '' : " and team = '$team' ";
	$activity = $_POST['activity'];
	$teamArr = $commonobj->arrayColumn($commonobj->getQry("SELECT distinct team from master_alcon_table where client = '$client' and month='$month' order by team asc") , '', 'team');
	foreach($teamArr as $key => $value) {
		$teamArray[] = "<option value='$value'>" . $value . "</option>";
	}

	if ($activity == 'L2 Connect') {
		$Qry = " and role='L2'";
	} else if ($activity == 'TL/Manager connect') {
		$Qry = " and role='TL'";
	}

	$enggArr = $commonobj->arrayColumn($commonobj->getQry("SELECT distinct emp_name from master_alcon_table where team = '$team' and month='$month' $Qry order by emp_name asc") , '', 'emp_name');
	foreach($enggArr as $key => $value) {
		$enggArray[] = "<option value='$value'>" . $value . "</option>";
	}

	echo json_encode(array(
		$teamArray,
		$enggArray
	));
}
if(isset($_POST['task_id']) && $_POST['come_form'] == 'activity_list'){
	$taskid = $_POST['task_id'];
	echo json_encode($commonobj->getQry("SELECT * from hr_task_activity_comments where activity_id='$taskid'"));
}
// if(isset($_POST['teamname']) && $_POST['comefrom'] == 'engg name map'){
//  $team = $_POST['teamname'];
//  $activity = $_POST['activity'];
//  if($activity == 'L2 Connect'){
//  	$Qry = " and role='L2'";
//  }else if($activity == 'TL/Manager connect'){
// 	$Qry = " and role='TL'";
//  }
//  $enggArr = $commonobj->arrayColumn($commonobj->getQry("SELECT distinct emp_name from master_alcon_table where team = '$team' and month='$month' $Qry order by emp_name asc"),'','emp_name');
// foreach ($enggArr as $key => $value) {
// 	$enggArray[] = "<option value='$value'>".$value."</option>";
// }
// echo json_encode($enggArray);
// }
?>