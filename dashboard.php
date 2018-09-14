<?php 
include('includes/config.php');
//include('includes/session_check.php');
$clientArr = $commonobj->arrayColumn($commonobj->getQry("SELECT distinct client from master_alcon_table where client<>'' order by client asc"),'','client');
 $overalltask = $commonobj->getQry("SELECT * from hr_task_activity_comments");
 foreach ($overalltask as $key => $ovrvalue) {
 	$taskcount[$ovrvalue['activity_id']] = $ovrvalue['status'];
 }
$countArr = array_count_values($taskcount);

include('includes/header.php'); 
?>
<script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
<script src="https://www.amcharts.com/lib/3/serial.js"></script>
<script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
<link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
<script src="https://www.amcharts.com/lib/3/themes/light.js"></script>
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
                <option value='Overall'>Overall HR</option>
                <?php foreach($commonobj->arrayColumn($commonobj->getQry("SELECT email,name from hr_list order by name asc"),'email','name') as $hr_name){ 
                    $selected = ($month == $sel_month)?"Selected":""; ?>
                <option value="<?=$hr_name?>" <?=$selected?>><?=ucwords($hr_name)?></option>
                <?php } ?>
            </select>
            <select class="selectpicker" onchange="getMonth(this.value)" name="project" id="project" data-size="5" data-style="btn btn-info btn-round" title="Project" required>
                <?php
                	echo "<option value='Overall'>Overall Project</option>";
                	foreach ($clientArr as $key => $cname) {
		        		echo "<option value='$cname'>".$cname."</option>";
		        	}
                ?>
            </select>
            <select class="selectpicker" name="team" id="team" data-size="5" data-style="btn btn-info btn-round" title="Team">
                <option value='Overall'>Overall Team</option>
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
			<!-- <div class="row">
		      <div class="col-md-6">
		      <div class="card ">
		        <div class="card-header ">
		          <h4 class="card-title">Activity / Hr Mapping / Add Hr</h4>
		        </div>
		        <div class="card-body ">
		          <div class='' id='chartdiv'></div>
		        </div>
		      </div>
		    </div>
		  </div> -->
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
function getMonth(client){
	$.ajax({
		url: 'ajax.php',
		type: 'POST',
		data: {'projectArr':client,'ComeFrom':'project_activity'},
		success: function(output) {
			var obj = jQuery.parseJSON( output);
			obj.unshift("<option value='Overall'>Overall Team</option>");
			$("#team").html(obj);
			$("#team").selectpicker('refresh');
		}
	}); 
}
var chart4 = AmCharts.makeChart("chartdiv", {
		"theme": "light",
		"type": "serial",
		"startDuration": 2,
		"dataProvider": [
				{
			"months": "Apr-18",
			"hc": 21.2,
			"retention": 14.5,
		},
				{
			"months": "May-18",
			"hc": 23,
			"retention": 15.9,
		},
				{
			"months": "Jun-18",
			"hc": 25.1,
			"retention": 19.5,
		},
				{
			"months": "Jul-18",
			"hc": 26.1,
			"retention": 18.3,
		},
				],
		"synchronizeGrid":true,
		"valueAxes": [{
			"id": "v1",
			"axisColor": "#FCD202",
			"axisThickness": 2,
			"axisAlpha": 0.15,
			"position": "left",
			"unit":"%",
			"title": "Overall YTD %",
		  }, {
			"id": "v2",
			"axisColor": "#FCD202",
			"axisThickness": 2,
			"axisAlpha": 0.15,
			"position": "right",
			"unit":"%",
			"title": "Voluntary YTD %",
			"synchronizationMultiplier": 0.25
		  },],

		"graphs": [{
			"valueAxis": "v1",
			"balloonText": "Overall YTD % ([[category]]): <b>[[value]]%</b>",
			"lineThickness": 2,
			"bullet": "diamond",
			"bulletBorderAlpha": 1,
			"bulletBorderColor": "#fff",
			"valueField": "hc",
			"lineColor": "#8d1cc6",
			"type": "smoothedLine",
		  }, {
			"valueAxis": "v2",
			"balloonText": "Voluntary YTD % ([[category]]): <b>[[value]]%</b>",
			"lineThickness": 2,
			"bullet": "diamond",
			"bulletBorderAlpha": 1,
			"bulletBorderColor": "#fff",
			"valueField": "retention",
			"type": "smoothedLine",
		  }],
		"chartCursor": {
	        "categoryBalloonDateFormat": "YYYY",
	        "cursorAlpha": 0,
	        "valueLineEnabled":true,
	        "valueLineBalloonEnabled":true,
	        "valueLineAlpha":0.5,
	        "fullWidth":true
	    },
		"categoryField": "months",
		"categoryAxis": {
			"gridPosition": "start",
			"labelRotation": 90
		},
		"export": {
			"enabled": false
		 }
	});
	var chart4 = AmCharts.makeChart("chartdiv1", {
		"theme": "light",
		"type": "serial",
		"startDuration": 2,
		"dataProvider": [
				{
			"months": "Apr-18",
			"hc": 1.7,
			"retention": 1.2,
		},
				{
			"months": "May-18",
			"hc": 2.1,
			"retention": 1.5,
		},
				{
			"months": "Jun-18",
			"hc": 2.4,
			"retention": 2.2,
		},
				{
			"months": "Jul-18",
			"hc": 2.4,
			"retention": 1.2,
		},
				],
		"synchronizeGrid":true,
		"valueAxes": [{
			"id": "v1",
			"axisColor": "#FCD202",
			"axisThickness": 2,
			"axisAlpha": 0.15,
			"position": "left",
			"unit":"%",
			"title": "Monthly Attr % - Overall",
		  }, {
			"id": "v2",
			"axisColor": "#FCD202",
			"axisThickness": 2,
			"axisAlpha": 0.15,
			"position": "right",
			"unit":"%",
			"title": "Monthly Attr % - Voluntary",
			"synchronizationMultiplier": 0.25
		  },],
		"graphs": [{
			"valueAxis": "v1",
			"balloonText": "Monthly Attr Overall % ([[category]]): <b>[[value]]%</b>",
			"lineThickness": 2,
			"bullet": "diamond",
			"bulletBorderAlpha": 1,
			"bulletBorderColor": "#fff",
			"valueField": "hc",
			"lineColor": "#8d1cc6",
			"type": "smoothedLine",
		  }, {
			"valueAxis": "v2",
			"balloonText": "Monthly Attr voluntary % ([[category]]): <b>[[value]]%</b>",
			"lineThickness": 2,
			"bullet": "diamond",
			"bulletBorderAlpha": 1,
			"bulletBorderColor": "#fff",
			"valueField": "retention",
			"type": "smoothedLine",
		  }],
		"chartCursor": {
	        "categoryBalloonDateFormat": "YYYY",
	        "cursorAlpha": 0,
	        "valueLineEnabled":true,
	        "valueLineBalloonEnabled":true,
	        "valueLineAlpha":0.5,
	        "fullWidth":true
	    },
		"categoryField": "months",
		"categoryAxis": {
			"gridPosition": "start",
			"labelRotation": 90
		},
		"export": {
			"enabled": false
		 }
	});

</script>