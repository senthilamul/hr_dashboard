<?php 
include('includes/config.php');
// include('includes/session_check.php');
if(isset($_POST['year'])){
	extract($_POST);
}else{
	$yearval= $commonobj->getQry("SELECT master_month from emp_master_table order by id desc limit 0,1");
	$yearfromtable = substr($yearval[0]['master_month'],4,2);
	$year = '20'.$yearfromtable.'-'.($yearfromtable+1);
}
$monthArray = $monthArr = $financialmonthArr[$year];
array_push($monthArray,'Mar/'.substr($year,2,2));
$currentmonthdays = $numberofdaysArr[$year];
$openhcmonth = array('Mar/'.substr($year,2,2));

$openingHC = getcountTable('emp_master_table' , 'master_month' ,'master_month' , "" ,$openhcmonth  );

$openHC = $openingHC['Mar/'.substr($year,2,2)];

$headcountArr = getcountTable('emp_master_table' , 'master_month' ,'master_month' , "" ,$monthArr  );
$attr_month = array_map("myfunction",$monthArr);
$monthlyAttr = getcountTable('attr_master_tbl' , 'attn_month_2' ,'attn_month_2' , "" ,$attr_month  );
$attrition_typeArr = $commonobj->getQry("SELECT count(*) as count, attrition_type,attn_month_2 from attr_master_tbl where attn_month_2 in ('".implode("','",$attr_month)."') group by attn_month_2,attrition_type");
foreach ($attrition_typeArr as $key => $value) {
	$attrition_type[$value['attn_month_2']][$value['attrition_type']] = $value['count'];
}
$openheadcount = $commonobj->arrayColumn($commonobj->getQry("SELECT count(*) as count, master_month from emp_master_table where master_month in ('".implode("','",$monthArray)."') group by master_month order by STR_TO_DATE(master_month, '%M/%y')"),'master_month','count');
foreach ($openheadcount as $key => $value) {
	$headcountAr[] = $value;
}
$i=0;
foreach ($monthArray as $key => $value) {
	if($headcountAr[$i] !='')
	$headcountarray[$value] = $headcountAr[$i];
$i++;
}
array_pop($headcountarray);
include('includes/header.php');
?>
<style>
	.table thead tr th {
		font-size: 13px !important;
	}
   .table-condensed>tbody>tr>td, .table-condensed>tbody>tr>th, .table-condensed>tfoot>tr>td, .table-condensed>tfoot>tr>th, .table-condensed>thead>tr>td, .table-condensed>thead>tr>th{
   		font-size: 12px !important;
	}
	.amcharts-chart-div a{
	display:none !important;
}
</style>
<script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
<script src="https://www.amcharts.com/lib/3/serial.js"></script>
<script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
<link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
<script src="https://www.amcharts.com/lib/3/themes/dark.js"></script>
<nav class="navbar navbar-expand-lg navbar-absolute fixed-top " id="navigation-example">
	   <div class="container-fluid">
		  <div class="navbar-wrapper">
			 <div class="navbar-minimize">
				<button id="minimizeSidebar" class="btn btn-just-icon btn-white btn-fab btn-round">
				<i class="material-icons text_align-center visible-on-sidebar-regular">more_vert</i>
				<i class="material-icons design_bullet-list-67 visible-on-sidebar-mini">view_list</i>
				</button>
			 </div>
			 <a class="navbar-brand" href="#pablo">Project wise Attrition</a>
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
	<div class="content">
	<div class="content">
	<div class="container-fluid">
	<form id="RegisterValidation" action="" method="post" novalidate="novalidate" enctype="multipart/form-data">
	<input type="hidden" name="_token" value="<?php echo $token; ?>">
	<div class="row">
	   <div class="col-md-12">
        <div class="card">
          <div class="card-header card-header-icon card-header-rose">
            <div class="card-icon">
              <i class="material-icons">accessibility</i>
            </div>
            <h5 class="card-title ">Attrition Analysis (ETAS - India)  as on 31st <?php echo $month." ' ".$year?></h5>
          </div>
          <div class="card-body">
          	<div class="row">
				<div class="col-md-3">
					<div class="form-group bmd-form-group">
			         	<div class="dropdown bootstrap-select show-tick dropup">
			             	<select class="selectpicker" data-style="select-with-transition" title="Choose Year" data-size="7" tabindex="-98" required name='year' id='year'>
			             		<?php  
			             		foreach ($financialmonthArr as $key => $value) {
			             			echo "<option value='$key'>".$key.'</option>';
			             		}
			             		?>
			              </select>
			              <label id="year-error" class="error" for="year" style='display: none'></label>
			          </div>
			        </div>
				</div>
				<div class='col-md-3'>
					<button class="btn btn-primary">Submit<div class="ripple-container"></div></button>
				</div>
			</div>
            <div class="table-responsive">
              <table class="table table-bordered table-hover table-condensed">
                <thead class=" text-primary">
                  <tr>
                  	<th>Month</th>
					<th>HC</th>
					<th>Total Attrition</th>
					<th>Voluntary</th>
					<th>Involuntary</th>
					<th>Overall YTD Attrition%</th>
					<th>Voluntary YTD Attrition%</th>
					<th>Involuntary YTD Attrition%</th>
					<th>Monthly Attn % - overall</th>
					<th>Monthly Attn % - Voluntary</th>
					<th>Monthly Attn % - Involuntary</th> 
                </tr>
                </thead>
                <tbody>
                  <tr>
                  <?php 
                  $i=0;
                  	foreach($headcountArr as $keys => $monthval){ ?>
                  		<td><?=$keys?></td>
                  		<td><?=$monthval?></td>
                  		<td><?php echo $totatt = $monthlyAttr[str_replace('/','-',$keys)]?></td>
                  		<td><?php echo $voluntartyCnt = $attrition_type[str_replace('/','-',$keys)]['Voluntary']?></td>
                  		<td><?php echo $involuntartyCnt =$attrition_type[str_replace('/','-',$keys)]['Involuntary']?></td>
                  		<?php
                  		$totattr += $totatt;
                  		$volattr +=$voluntartyCnt;
                  		$involattr +=$involuntartyCnt;
                  		
                  			$currentmonthdays[$keys];
                  			$totattrArr[str_replace('/','-',$keys)]['overall'] = (float)$ovrYTDattr = number_format(($totattr/(($monthval+$openHC)/2))*(365/$currentmonthdays[$keys])*100,1).'%';
                  			$totattrArr[str_replace('/','-',$keys)]['voluntary'] = (float)$voluYTDattr = number_format(($volattr/(($monthval+$openHC)/2))*(365/$currentmonthdays[$keys])*100,1).'%';
                  			$involuYTDattr = number_format(($involattr/(($monthval+$openHC)/2))*(365/$currentmonthdays[$keys])*100,1).'%';
                  			$monthattrArr[str_replace('/','-',$keys)]['overall'] = (float)$monthattr = number_format($totatt/(($headcountarray[$keys]+$monthval)/2)*100,1).'%';
                  			$monthattrArr[str_replace('/','-',$keys)]['voluntary'] =  (float)$monthvoluntary = number_format($voluntartyCnt/(($headcountarray[$keys]+$monthval)/2)*100,1).'%';
                  			$monthinvolattr = number_format($involuntartyCnt/(($headcountarray[$keys]+$monthval)/2)*100,1).'%';

                  		?>
                  		<td><?=$ovrYTDattr?></td>
                  		<td><?=$voluYTDattr?></td>
                  		<td><?=$involuYTDattr?></td>
                  		<td><?=$monthattr?></td>
                  		<td><?=$monthvoluntary?></td>
                  		<td><?=$monthinvolattr?></td>
	                </tr>
	                  	
                 <?php $i++;} 
                 	$totoverallvol = number_format(($totattr/((current($headcountArr)+$openHC)/2))*100,1).'%';
                 	$totvol = number_format(($volattr/((current($headcountArr)+$openHC)/2))*100,1).'%';
                 	$totinvol = number_format(($involattr/((current($headcountArr)+$openHC)/2))*100,1).'%';
                 ?> 
                  <tr>
              		<th colspan="2">Total</th>
              		<th><?=$totattr?></th>
              		<th><?=$volattr?></th>
              		<th><?=$involattr?></th>
              		<th colspan="3"></th>
              		<th><?=$totoverallvol?></th>
              		<th><?=$totvol?></th>
              		<th><?=$totinvol?></th>
                  </tr>             
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
	</div>
	<div class="row">
	   <div class="col-md-6">
        <div class="card">
          <div class="card-header card-header-icon card-header-rose">
            <div class="card-icon">
              <i class="material-icons">bar_chart</i>
            </div>
            <h5 class="card-title ">ETAS Monthly YTD Attrition % (Vol & Overall)</h5>
          </div>
          <div class="card-body">
          	<div class="row">
          		<div class='col-md-12'>
          			<div id="chartdiv"></div>
          		</div>
          	</div>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card">
          <div class="card-header card-header-icon card-header-rose">
            <div class="card-icon">
              <i class="material-icons">show_chart</i>
            </div>
            <h5 class="card-title ">ETAS Monthly Attrition % (Vol & Overall)</h5>
          </div>
          <div class="card-body">
          	<div class="row">
          		<div class='col-md-12'>
          			<div id="chartdiv1"></div>
          		</div>
          	</div>
          </div>
        </div>
      </div>
	</div>
	</form>
<?php include("includes/footer.php");
function myfunction($v){
  return str_replace('/', '-', $v);
}
function getcountTable($tablename , $filed , $condition , $condition2 ='' ,$monthcod ){
	global $commonobj;
	if($tablename != 'emp_master_table'){
		return $commonobj->arrayColumn($commonobj->getQry("SELECT count(*) as count,$filed from $tablename where $condition in ('".implode("','",$monthcod)."')  $condition2 group by $condition"),$condition,'count');
	}else{
		return $commonobj->arrayColumn($commonobj->getQry("SELECT count(*) as count,$filed from $tablename where $condition in ('".implode("','",$monthcod)."')  $condition2 group by $condition order by STR_TO_DATE(master_month, '%M/%y')"),$condition,'count');
	}
}
function getdistinct($tablename , $disfield ,$condition ,$value){
	global $commonobj;
	return $commonobj->arrayColumn($commonobj->getQry("SELECT distinct $disfield from $tablename where $condition = '$value'"),'',$disfield);
}
function getcountTables($tablename , $filed , $condition , $condition2 ='' ,$monthcod ){
	global $commonobj;
	return "SELECT count(*) as count,$filed from $tablename where $condition in ('".implode("','",$monthcod)."')  $condition2 group by $condition";
}
?>

<style>
.error{
	color: red !important;
}
#chartdiv ,  #chartdiv1{
 /*  width: 100%; */
  height: 300px;
}							
</style>

<script>
	$('#RegisterValidation').validate();
	
	$('.alert').fadeOut(3000);

	var chart<?=$i?> = AmCharts.makeChart("chartdiv", {
		"theme": "light",
		"type": "serial",
		"startDuration": 2,
		"dataProvider": [
		<?php foreach($totattrArr as $resKey => $resArr){ 
		$voluntary = ($resArr['voluntary'])?$resArr['voluntary']:0;
		$overall = ($resArr['overall'])?$resArr['overall']:0;
		?>
		{
			"months": "<?=$resKey?>",
			"hc": <?=$overall?>,
			"retention": <?=$voluntary?>,
		},
		<?php } ?>
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
	var chart<?=$i?> = AmCharts.makeChart("chartdiv1", {
		"theme": "light",
		"type": "serial",
		"startDuration": 2,
		"dataProvider": [
		<?php foreach($monthattrArr as $resKey => $resArr){ 
		$voluntary = ($resArr['voluntary'])?$resArr['voluntary']:0;
		$overall = ($resArr['overall'])?$resArr['overall']:0;
		?>
		{
			"months": "<?=$resKey?>",
			"hc": <?=$overall?>,
			"retention": <?=$voluntary?>,
		},
		<?php } ?>
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
