<?php 
include('includes/config.php');
include('includes/session_check.php');
if(isset($_POST['hr_name'])){
	$trend_type = $_POST['trend_type'];
	$trend_wise = $_POST['trend_wise'];
	$hr_name = $_POST['hr_name'];
}else{

	$trend_type = empty($_POST['trend_type']) ? 'Week' : $_POST['trend_type'];
	$weeknum = $commonobj->arrayColumn($commonobj->getQry("SELECT distinct week from hr_task_activity_comments  order by cmd_id desc limit 0 ,1 "),'','week');
	$trend_wise = empty($_POST['trend_wise']) ? $weeknum[0] : $_POST['trend_wise']; 
	$hr_name = $_POST['hr_name'] = 'Overall';
}
$hrlistArr= $commonobj->arrayColumn($commonobj->getQry("SELECT * from hr_list order by name asc"),'email','name');
if($trend_type == 'Week'){
	$selectField = 'week';
}else if($trend_type == 'Month'){
	$selectField = 'month';
}else{
	$selectField = 'qtr';
}

$Qry= empty($trend_type) ?'':" where  $selectField = '$trend_wise'";
$Qry.= $hr_name == 'Overall' ?'' :" and create_by = '$hr_name'";

$clientArr = $commonobj->arrayColumn($commonobj->getQry("SELECT distinct client from master_alcon_table where client<>'' order by client asc"),'','client');
$overalltask = $commonobj->getQry("SELECT * from hr_task_activity_comments $Qry");
$i=0;

foreach ($overalltask as $key => $ovrvalue) {
	$taskcount[$ovrvalue['activity_id']] = $ovrvalue['status'];
	$casewise[$ovrvalue['activity_id']] = $ovrvalue['client'];
	$task_typeAr[$ovrvalue['activity_id']] = $ovrvalue['task_type'];
	$activity_typeAr[$ovrvalue['activity_id']] = $ovrvalue['activity_type'];
$i++;
}
$j =0;

$countArr = array_count_values($taskcount);

$projectwiseArr = converToJsonFormat($casewise , 'project');
$task_type = converToJsonFormat($task_typeAr , 'task');
$activity_type = converToJsonFormat($activity_typeAr , 'activity');

function converToJsonFormat($array , $keypare)
{
	$j=0;
	foreach (array_count_values($array) as $key => $sumval) {
		$returnArr[$j][$keypare] = $key;
		$returnArr[$j]['value'] = $sumval;
		$j++; 
	}
	return $returnArr;
}
include('includes/header.php'); 
?>
<script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
<script src="https://www.amcharts.com/lib/3/pie.js"></script>
<script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
<link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
<script src="https://www.amcharts.com/lib/3/themes/dark.js"></script>
<!-- Navbar -->
	<nav class="navbar navbar-expand-lg navbar-absolute fixed-top " id="navigation-example">
	   <div class="container-fluid">
		  <div class="navbar-wrapper">
			 <div class="navbar-minimize">
				<button id="minimizeSidebar" class="btn btn-just-icon btn-white btn-fab btn-round">
				<i class="material-icons text_align-center visible-on-sidebar-regular">more_vert</i>
				<i class="material-icons design_bullet-list-67 visible-on-sidebar-mini">view_list</i>
				</button>
			 </div>
			 <a class="navbar-brand" href="#pablo">Dashboard</a>
		  </div>
	   </div>
	</nav>
	<!-- End Navbar -->
	<div class="content">
    <div class="content">
    <div class="container-fluid">
        <form action="dashboard.php" method="post" style="text-align:right;margin-bottom:0px;">
            <input type="hidden" name="_token" value="<?php echo $token; ?>">
            <select class="selectpicker" name="hr_name" id="hr_name" data-size="5" data-style="btn btn-info btn-round" title="HR Name">
                <?php
                	$select= $hr_name=='Overall'?'selected':'';
                	echo "<option value='Overall' $select>Overall HR</option>";
                 foreach($commonobj->arrayColumn($commonobj->getQry("SELECT email,name from hr_list order by name asc"),'email','name') as $email => $hr_names){ 
                    $selected = ($hr_name == $email)?"selected":""; ?>
                <option value="<?=$email?>" <?=$selected?>><?=ucwords($hr_names)?></option>
                <?php } ?>
            </select>
            <select class="selectpicker" onchange="getMonth(this.value)" name="trend_type" id="trend_type" data-size="5" data-style="btn btn-info btn-round" title="Trend" required>
                <option value="Week" <?=$trend_type=='Week'?'selected':''?> > Week</option>
                <option value="Month" <?=$trend_type=='Month'?'selected':''?>>Month</option>
                <option value="Quarterly" <?=$trend_type=='Quarterly'?'selected':''?>>Quarterly</option>
            </select>
            <select class="selectpicker" name="trend_wise" id="trend_wise" data-size="5" data-style="btn btn-info btn-round" title="Team">
            <?php 
            
			$teamnameArr = $commonobj->arrayColumn($commonobj->getQry("SELECT distinct $selectField from hr_task_activity_comments  order by cmd_id desc"),'',$selectField);
			foreach($teamnameArr as $key => $value) {
				$selected= $trend_wise == $value ? 'selected' :'';
				echo "<option value='$value' $selected>" . $value . "</option>";
			}
            ?>
            </select>
            <button type="submit" class="btn btn-fill btn-success btn-round">Submit</button>
	    	<div class="row">
			  <div class="col-lg-3 col-md-6 col-sm-6">
			    <div class="card card-stats">
			      <div class="card-header card-header-warning card-header-icon">
			        <div class="card-icon">
			          <i class="material-icons">layers</i>
			        </div>
			        <p class="card-category">Open</p>
			        <h3 class="card-title counter-count"><?=empty($countArr['Open'])?0:$countArr['Open']?></h3>
			      </div>
			      <div class="card-footer">
			        <div class="stats">
			          <!-- <i class="material-icons text-danger">warning</i> -->
			          <!-- <a href="#pablo">Get More Space...</a> -->
			        </div>
			      </div>
			    </div>
			  </div>
			  <div class="col-lg-3 col-md-6 col-sm-6">
			    <div class="card card-stats">
			      <div class="card-header card-header-rose card-header-icon">
			        <div class="card-icon">
			          <i class="material-icons">computer</i>
			        </div>
			        <p class="card-category">Work in progress</p>
			        <h3 class="card-title counter-count"><?=empty($countArr['Work in progress'])?0:$countArr['Work in progress']?></h3>
			      </div>
			      <div class="card-footer">
			        <div class="stats">
			          <!-- <i class="material-icons">local_offer</i> Tracked from Google Analytics -->
			        </div>
			      </div>
			    </div>
			  </div>
			  <div class="col-lg-3 col-md-6 col-sm-6">
			    <div class="card card-stats">
			      <div class="card-header card-header-success card-header-icon">
			        <div class="card-icon">
			          <i class="material-icons">date_range</i>
			        </div>
			        <p class="card-category">Closed</p>
			        <h3 class="card-title counter-count"><?=empty($countArr['Closed'])?0:$countArr['Closed']?></h3>
			      </div>
			      <div class="card-footer">
			         <!-- <div class="stats">
			         <i class="material-icons">date_range</i> Last 24 Hours
			        </div> -->
			      </div>
			    </div>
			  </div>
			  <div class="col-lg-3 col-md-6 col-sm-6">
			    <div class="card card-stats">
			      <div class="card-header card-header-info card-header-icon">
			        <div class="card-icon">
			          <i class="fa fa-twitter"></i>
			        </div>
			        <p class="card-category">Overall</p>
			        <h3 class="card-title counter-count"><?=empty($countArr)?0:array_sum($countArr)?></h3>
			      </div>
			      <div class="card-footer">
			        <!-- <div class="stats">
			          <i class="material-icons">update</i> Just Updated
			        </div> -->
			      </div>
			    </div>
			  </div>
			</div>
			<div class="row">
			   <div class="col-md-12">
		        <div class="card">
		          <div class="card-header card-header-icon card-header-rose">
		          </div>
		          <div class="card-body">
		          	<div class="row">
		          		<div class='col-md-4'>
		          		<h5>Project Wise - <?=$hr_name?></h5>
		          			<div id="chartdiv"></div>
		          		</div>
		          		<div class='col-md-4'>
		          			<h5>Activity Type - <?=$hr_name?></h5>
		          			<div id="chartdiv2"></div>
		          		</div>
		          		<div class='col-md-4'>
		          			<h5>Task Type - <?=$hr_name?></h5>
		          			<div id="chartdiv1"></div>
		          		</div>
		          	</div>
		          </div>
		        </div>
		      </div>
			</div>
        </form>
    
<?php include("includes/footer.php"); ?>
</body>
</html>
<style>
.copyright {
    font-size: 14px;
}
.chartdiv {
  width: 100%;
  height: 280px;
  *float:right;
}
.chartdivs{
	width: 100%;
   height: 350px;
}
#chartdiv ,  #chartdiv1 ,#chartdiv2{
 /*  width: 100%; */
  height: 250px;
}	
.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th
{
	padding:1px 1px;
}
th,td{
	font-size:12px;
}
/* .amcharts-chart-div a{
	display:none !important;
} */
.table>thead>tr>th{
	padding: 1px 1px;
    font-size: 12px;
    font-weight: bold;
}
.amcharts-export-menu-top-right {
  top: 10px;
  right: 0;
}
html{
	overflow-y: hidden;
}
.amcharts-chart-div {
    right: 5px;
}
td,th{
	text-align:center;
	font-size:11px;
}
.table thead th{
	border-top-width:1px;
}
</style>
<script>
$(document).ready( function () {
	$('.counter-count').each(function () {
        $(this).prop('Counter',0).animate({
            Counter: $(this).text()
        }, {
            duration: 3000,
            easing: 'swing',
            step: function (now) {
                $(this).text(Math.ceil(now));
            }
        });
    });
} );
function getMonth(type){
	$.ajax({
		url: 'ajax.php',
		type: 'POST',
		data: {'typeTrend':type,'ComeFrom':'Select Trend'},
		success: function(output) {
			console.log(output)
			var obj = jQuery.parseJSON( output);
			//obj.unshift("<option value='Overall'>Overall Team</option>");
			$("#trend_wise").html(obj);
			$("#trend_wise").selectpicker('refresh');
		}
	}); 
}
$('#RegisterValidation').validate();
	
	$('.alert').fadeOut(3000);
	var chart = AmCharts.makeChart( "chartdiv", {
	  "type": "pie",
	  "theme": "light",
	  "dataProvider":<?php echo json_encode($projectwiseArr,true)?>,
	  "valueField": "value",
	  "titleField": "project",
	  "outlineAlpha": 0.1,
	  "labelRadius": -35,
  	  "labelText": "[[title]]<br>[[[value]]]-[[percents]]%",
	  "depth3D": 15,
	  "balloonText": "[[title]]<br><span style='font-size:10px'><b>[[value]]</b> ([[percents]]%)</span>",
	  "angle": 30,
	  "export": {
	    "enabled": true
	  },
	  "hideCredits":true,
	} );

	var chart = AmCharts.makeChart( "chartdiv1", {
	  "type": "pie",
	  "theme": "light",
	  "dataProvider":<?php echo json_encode($task_type,true)?>,
	  "valueField": "value",
	  "titleField": "task",
	  "outlineAlpha": 0.1,
	  "labelRadius": -35,
  	  "labelText": "[[title]]<br>[[[value]]]-[[percents]]%",
	  "depth3D": 15,
	  "balloonText": "[[title]]<br><span style='font-size:10px'><b>[[value]]</b> ([[percents]]%)</span>",
	  "angle": 30,
	  "export": {
	    "enabled": true
	  },
	  "hideCredits":true,
	} );
	var chart = AmCharts.makeChart( "chartdiv2", {
	  "type": "pie",
	  "theme": "light",
	  "dataProvider":<?php echo json_encode($activity_type,true)?>,
	  "valueField": "value",
	  "titleField": "activity",
	  "outlineAlpha": 0.1,
	  "labelRadius": -35,
  	  "labelText": "[[title]]<br>[[[value]]]-[[percents]]%",
	  "depth3D": 15,
	  "balloonText": "[[title]]<br><span style='font-size:10px'><b>[[value]]</b> ([[percents]]%)</span>",
	  "angle": 30,
	  "export": {
	    "enabled": true
	  },
	  "hideCredits":true,
	} );
</script>