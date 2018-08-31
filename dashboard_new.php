<?php 
include('includes/config.php');
//include('includes/session_check.php');	
 
$master_tbl_last_record = $commonobj->getQry("select * from `emp_master_table` order by id desc limit 0, 1");
$last_row_of_ket = $master_tbl_last_record[0]['master_month'];

if($_POST['month'] || $_POST['year'])
{
	$sel_month = $_POST['month'];
	$sel_year = $_POST['year'];
}else{
	list($sel_month,$sel_year) = explode("/",$last_row_of_ket);
	
}
$current_month_of_ket = $sel_month."/".$sel_year;
$start = array_search($current_month_of_ket,$financialmonthresArroll);
$three_yr_monthArr = array_slice($financialmonthresArroll,$start,37,false); 
$master_tblArr = $commonobj->arrayColumn($commonobj->getQry("SELECT distinct master_month from emp_master_table where master_month like '%$sel_year'"),"","master_month");

foreach($financialmonthresArr as $financialyrKey => $financialMonthArr){
	foreach($financialMonthArr as $financialMonth){
		$hc_count = $commonobj->getQry("select count(*) as hc_count from `emp_master_table` where master_month = '$financialMonth'");
		$trendresArr[$financialyrKey][$financialMonth]['hc_count'] = ($hc_count[0]['hc_count'])?$hc_count[0]['hc_count']:0;
		$oneplusyr_count = $commonobj->getQry("select count(*) as oneplusyr_count from `emp_master_table` where master_month = '$financialMonth' and 1_yrs = '1 + Yrs'");
		$trendresArr[$financialyrKey][$financialMonth]['oneplusyr_count'] = ($oneplusyr_count[0]['oneplusyr_count'])?$oneplusyr_count[0]['oneplusyr_count']:0;
		$trendresArr[$financialyrKey][$financialMonth]['retention'] = ($hc_count[0]['hc_count'])?round(($oneplusyr_count[0]['oneplusyr_count']/$hc_count[0]['hc_count'])*100):0;
	}
}

$hc_countOverall = $commonobj->arrayColumn($commonobj->getQry("select count(*) as hc_count,master_month from `emp_master_table` where master_month in ('".implode("','",$three_yr_monthArr)."') group by master_month"),'master_month','hc_count');
$oneplusyr_countOverall = $commonobj->arrayColumn($commonobj->getQry("select count(*) as oneplusyr_count,master_month from `emp_master_table` where master_month in ('".implode("','",$three_yr_monthArr)."') and 1_yrs = '1 + Yrs' group by master_month"),'master_month','oneplusyr_count');
//print_r($hc_countOverall);
$three_yr_monthArrreversed = array_reverse($three_yr_monthArr);
foreach($three_yr_monthArrreversed as $montholStr){
	$monthOlArr[$montholStr]['hc_count'] = $hc_countOverall[$montholStr];
	$monthOlArr[$montholStr]['oneplusyr_count'] = $oneplusyr_countOverall[$montholStr];
	$monthOlArr[$montholStr]['retention'] = round(($oneplusyr_countOverall[$montholStr]/$hc_countOverall[$montholStr])*100);
}
//$colors = array("#FF0F00","#FF6600","#FF9E01","#FCD202","#F8FF01","#B0DE09","#04D215","#0D8ECF","#0D52D1","#2A0CD0","#8A0CCF","#CD0D74");
$colors = array(array("#330000","#660000","#990000","#CC0000","#FF0000","#FF3333","#FF6666","#FF9999","#FFCCCC","#FA8072","#E9967A","#FFA07A"),
array("#006400","#008000","#228B22","#556B2F","#808000","#6B8E23","#3CB371","#00FF00","#32CD32","#7FFF00","#7CFC00","#98FB98"),
array("#000033","#000066","#000099","#0000cc","#0000ff","#3333ff","#6666ff","#004c99","#0066cc","#0080ff","#3399ff","#66b2ff"),
array("#660033","#660066","#99004c","#990099","#cc0066","#cc00cc","#ff007f","#ff3399","#ff33ff","#ff66b2","#ff66ff","#ff99cc"));

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
		  <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation" data-target="#navigation-example">
		  <span class="sr-only">Toggle navigation</span>
		  <span class="navbar-toggler-icon icon-bar"></span>
		  <span class="navbar-toggler-icon icon-bar"></span>
		  <span class="navbar-toggler-icon icon-bar"></span>
		  </button>
		  <!--<div class="collapse navbar-collapse justify-content-end">
			 <ul class="navbar-nav">
				<li class="nav-item">
				   <a class="nav-link" href="#pablo">
					  <i class="material-icons">person</i>
					  <p class="d-lg-none d-md-block">
						 Account
					  </p>
					  <div class="ripple-container"></div>
				   </a>
				</li>
			 </ul>
		  </div>-->
	   </div>
	</nav>
	<!-- End Navbar -->
	   <div class="content">
		  <div class="content">
			 <div class="container-fluid">
			 <form action="dashboard_new.php" method="post" style="text-align:right;margin-bottom:0px;">
			  <input type="hidden" name="_token" value="<?php echo $token; ?>">
			  <select class="selectpicker" onchange="getMonth(this.value)" name="year" id="year" data-size="5" data-style="btn btn-primary btn-round" title="Year">
				<option disabled>Year</option>
				<?php foreach($yrArr as $yr){ 
				$dt = DateTime::createFromFormat('y', $yr);
				$yearFull = $dt->format('Y');
				?>
				<option value="<?=$yr?>" <?php if($sel_year == $yr){ echo "Selected"; }?>><?=$yearFull?></option>
				<?php } ?>
			  </select>
			  <select class="selectpicker" name="month" id="month" data-size="5" data-style="btn btn-primary btn-round" title="Month">
				<option disabled>Month</option>
				<?php foreach($master_tblArr as $mast_month){ 
					list($month,$year) = explode("/",$mast_month);
					$selected = ($month == $sel_month)?"Selected":""; ?>
					<option value="<?=$month?>" <?=$selected?>><?=$month?></option>
				<?php } ?>
			  </select>
			  <button type="submit" class="btn btn-fill btn-primary btn-round">Submit</button>        
			  </form>
			  <div class="row">
				  <div class="col-md-12">
					<div class="card">
					  <div class="card-header card-header-icon card-header-rose">
						<div class="card-icon">
						  <i class="material-icons">assignment</i>
						</div>
						<h4 class="card-title ">Overall Three Years Trending</h4>
					  </div>
					  <div class="card-body">
						<div id="chartdivOl" class="chartdiv col-md-12" style="padding:0px;"></div>
					  </div>
					</div>
				  </div>
				  
				</div>
		<?php 
		$i=1;$k=0;
		foreach($trendresArr as $key => $trendArr){ 
		$dt = DateTime::createFromFormat('y', $key);
		$year = $dt->format('Y');
		?>
		<div class="row">
		  <div class="col-md-12">
			<div class="card">
			  <div class="card-header card-header-icon card-header-rose">
				<div class="card-icon">
				  <i class="material-icons">assignment</i>
				</div>
				<h4 class="card-title "><?=$year?> Financial Year Trending</h4>
			  </div>
			  <div class="card-body">
				<div class="table-responsive col-md-3" style="display:inline-block;">
				  <table class="table table-bordered">
					<thead class=" text-primary">
						<tr>
							<th>Actual HC</th>
							<th>Month</th>
							<th>1 Year + #</th>
							<th>Retention %</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($trendArr as $resKey => $resArr){ 
						?>
						<tr>
							<td><?=$resArr['hc_count']?></td>
							<td><?=$resKey?></td>
							<td><?=$resArr['oneplusyr_count']?></td>
							<td><?=$resArr['retention'].'%'?></td>
						</tr>
						<?php } ?>
					</tbody>
				  </table>
				</div>
				<div id="chartdiv<?=$i?>" class="chartdiv col-md-8" style="padding:0px;"></div>
			  </div>
			</div>
		  </div>
		  
		</div>
		<script>
		
		var chart<?=$i?> = AmCharts.makeChart("chartdiv<?=$i?>", {
			"theme": "light",
			"type": "serial",
			"startDuration": 2,
			"dataProvider": [
			<?php $j=0;foreach($trendArr as $resKey => $resArr){ 
			$retention = ($resArr['retention'])?$resArr['retention']:0;
			?>
			{
				"months": "<?=$resKey?>",
				"hc": <?=$resArr['oneplusyr_count']?>,
				"retention": <?=$retention?>,
				"color": "<?=$colors[$k][$j]?>"
			},
			<?php $j++; } ?>
			],
			"synchronizeGrid":true,
			"valueAxes": [{
				"id": "v1<?=$i?>",
				"axisColor": "#FF6600",
				"axisThickness": 2,
				"axisAlpha": 1,
				"position": "left",
				"title": "HC"
			  }, {
				"id": "v2<?=$i?>",
				"axisColor": "#FCD202",
				"axisThickness": 2,
				"axisAlpha": 0.15,
				"position": "right",
				"unit":"%",
				"synchronizeWith": "v1",
				"title": "Retention %",
				"synchronizationMultiplier": 0.25
			  }],
			"graphs": [{
				"valueAxis": "v1<?=$i?>",
				"balloonText": "1 Year + # ([[category]]): <b>[[value]]</b>",
				"fillColorsField": "color",
				"fillAlphas": 1,
				"lineAlpha": 0.1,
				"type": "column",
				"valueField": "hc",
				"fixedColumnWidth": 20
			}, {
				"valueAxis": "v2<?=$i?>",
				"balloonText": "Retention % ([[category]]): <b>[[value]]%</b>",
				"lineThickness": 2,
				"bullet": "diamond",
				"bulletBorderAlpha": 1,
				"bulletBorderColor": "#fff",
				"valueField": "retention"
			  }],
			"depth3D": 20,
			"angle": 30,
			"chartCursor": {
				"categoryBalloonEnabled": false,
				"cursorAlpha": 0,
				"zoomable": false
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
		<?php $i++; $k=($k>=3)?0:$k+1; } ?>
   </div>
   <div id="chartdiv" class="chartdiv col-md-12" style="padding:0px;"></div>
</div>
</div>
<?php include("includes/footer.php"); ?>

</body>
</html>
<style>
.copyright {
    font-size: 14px;
}
.chartdiv {
  width: 100%;
  height: 250px;
  float:right;
}
.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th
{
	padding:1px 1px;
}
th,td{
	font-size:12px;
}
.amcharts-chart-div a{
	display:none !important;
}
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
	$('.datatable').DataTable({
		"pageLength": 5,
		"bFilter": false,
		"bInfo": false,
		"bLengthChange":false,
		"ordering": false
	});
} );
function getMonth(yr){
	console.log(yr);
	$("#month").html("");
	$.ajax({
		url: 'ajax_new.php',
		type: 'POST',
		data: {'yr':yr,'comefrom':'KEtrends'},
		success: function(output) {
			var obj = jQuery.parseJSON( output);
			console.log(obj);
			
			$("#month").html(obj);
			$("#month").selectpicker('refresh');
		}
	}); 
}
var chartOl = AmCharts.makeChart("chartdivOl", {
	"theme": "light",
	"type": "serial",
	"startDuration": 2,
	"dataProvider": [
	<?php $j=0;$l=0;foreach($monthOlArr as $resOlKey => $resOlArr){ 
	$retention = ($resOlArr['retention'])?$resOlArr['retention']:0;
	?>
	{
		"months": "<?=$resOlKey?>",
		"hc": <?=$resOlArr['oneplusyr_count']?>,
		"retention": <?=$retention?>,
		"color": "<?=$colors[$l][$j]?>"
	},
	<?php $l=($j>=11)?$l+1:$l; $j=($j>=11)?0:$j+1; } ?>
	],
	"synchronizeGrid":true,
	"valueAxes": [{
		"id": "v1<?=$i?>",
		"axisColor": "#FF6600",
		"axisThickness": 2,
		"axisAlpha": 1,
		"position": "left",
		"title": "HC"
	  }, {
		"id": "v2<?=$i?>",
		"axisColor": "#FCD202",
		"axisThickness": 2,
		"axisAlpha": 0.15,
		"position": "right",
		"unit":"%",
		"synchronizeWith": "v1",
		"title": "Retention %",
		"synchronizationMultiplier": 0.25
	  }],
	"graphs": [{
		"valueAxis": "v1<?=$i?>",
		"balloonText": "1 Year + # ([[category]]): <b>[[value]]</b>",
		"fillColorsField": "color",
		"fillAlphas": 1,
		"lineAlpha": 0.1,
		"type": "column",
		"valueField": "hc",
		"fixedColumnWidth": 20
	}, {
		"valueAxis": "v2<?=$i?>",
		"balloonText": "Retention % ([[category]]): <b>[[value]]%</b>",
		"lineThickness": 2,
		"bullet": "diamond",
		"bulletBorderAlpha": 1,
		"bulletBorderColor": "#fff",
		"valueField": "retention"
	  }],
	"depth3D": 20,
	"angle": 30,
	"chartCursor": {
		"categoryBalloonEnabled": false,
		"cursorAlpha": 0,
		"zoomable": false
	},
	"categoryField": "months",
	"categoryAxis": {
		"gridPosition": "start",
		"labelRotation": 90
	},
	"categoryAxesSettings": {
		"minPeriod": "mm",
		"autoGridCount": false,
		"equalSpacing": true,
		"gridCount": 1000,
		"labelRotation": 90, //recommended if you have a lot of labels
		"axisHeight": 50  //recommended to avoid overlap with the scrollbar
	  },
	"export": {
		"enabled": false
	 }

});
</script>