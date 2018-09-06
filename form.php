<?php
include('includes/config.php');
error_reporting(0);
$clientArr = $commonobj->arrayColumn($commonobj->getQry("SELECT distinct client from master_alcon_table where month = '$month' and client<>'' order by client asc"),'','client');
if(isset($_POST['title'])){
	extract($_POST);
	$cr_date       = date("d-M-y", strtotime($activity_date_time));
	$date       = date("Y-m-d H:i:s", strtotime($activity_date_time));
    $cr_addyear    = date("Y", strtotime($activity_date_time));
    $cr_month      = date("M", strtotime($activity_date_time));
    if($cr_month=='Jan' || $cr_month=='Feb' || $cr_month=='Mar'){
        $cr_qtr='Q1'." ".$cr_addyear;
    }else if($cr_month=='Apr' || $cr_month=='May' || $cr_month=='Jun'){
        $cr_qtr='Q2'." ".$cr_addyear;
    }else if($cr_month=='Jul' || $cr_month=='Aug' || $cr_month=='Sep'){
        $cr_qtr='Q3'." ".$cr_addyear;
    }else{
        $cr_qtr='Q4'." ".$cr_addyear;
    }
    $cr_month       = date("M", strtotime($cr_date))." ".$cr_addyear;
    $cr_week        ="Wk " . date("W", strtotime($cr_date))." ".$cr_addyear;
	if($activity != 'RAG' && $activity != 'Events/Fun activity'){
		$insertQry = $conn->prepare("INSERT INTO `hr_task`(`activity_title`, `activity_date_time`, `activity_type`, `client`, `team`, `present_engg`, `absent_engg`, `category`, `f_week`, `f_month`, `f_qtr`, `created_by`, `create_at`, `update_at`) VALUES ( ?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
		$absentname= count($ab_engg) > 0 ? implode(',',$ab_engg) :'';
		 $presentname = count($pr_engg) > 0 ? implode(',',$pr_engg): '';
		$insertstatus = $insertQry->execute(array($title , $date , $activity , $client , $team , $presentname ,$absentname,$issue , $cr_week ,  $cr_month , $cr_qtr , $username , $dbdatetime , $dbdatetime ));
		$taskID = $conn->lastInsertId();
		for ($i=1; $i <= $_POST['hidden_id'] ; $i++) { 
			$title = $_POST["ID".$i."_title"];
			$start_date = date('Y-m-d',strtotime($_POST["ID".$i."_start_date"]));
			$end_time = date('Y-m-d',strtotime($_POST["ID".$i."_end_date"]));
			$status = $_POST["ID".$i."_status"];
			$cmd = $_POST["ID".$i."_comments"];
			$taskActivity = $conn->prepare("INSERT INTO `hr_task_activity`(`task_id`, `task_title`, `start_date`, `end_date`, `status`, `task_cmd`, `create_at`, `update_at`) VALUES ( ?,?,?,?,?,?,?,?)");
			$taskstatus = $taskActivity->execute(array($taskID , $title , $start_date , $end_time , $status , $cmd , $dbdatetime , $dbdatetime ));
		}
	}else{
		
	}

	if($insertstatus){
		echo "hi";
		//header('location:form.php?msg=1');
		exit;
	}
}

include('includes/header.php');
?>
<link rel="stylesheet" type="text/css" href="../hr/assets/sumoselect.min.css">

<style>
	.error{color:red !important;}
	.SumoSelect{
		width: 100% !important;
	}
	.SumoSelect>.CaptionCont  {
		border: 0px solid #A4A4A4 !important;
		border-bottom: 1px solid #d2d2d2 !important;
	}
	.SumoSelect .select-all {
		height: 35px; 
	}
</style>
<nav class="navbar navbar-expand-lg navbar-absolute fixed-top " id="navigation-example">
  <div class="container-fluid">
    <div class="navbar-wrapper">
      <div class="navbar-minimize">
        <button id="minimizeSidebar" class="btn btn-just-icon btn-white btn-fab btn-round">
        <i class="material-icons text_align-center visible-on-sidebar-regular">more_vert</i>
        <i class="material-icons design_bullet-list-67 visible-on-sidebar-mini">view_list</i>
        </button>
      </div>
      <a class="navbar-brand" href="#pablo">Create Events</a>
    </div>
    <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation" data-target="#navigation-example">
    <span class="sr-only">Toggle navigation</span>
    <span class="navbar-toggler-icon icon-bar"></span>
    <span class="navbar-toggler-icon icon-bar"></span>
    <span class="navbar-toggler-icon icon-bar"></span>
    </button>
  </div>
</nav>
<!-- End Navbar -->
<div class="content">
<div class="content">
<div class="container-fluid">
  <input type="hidden" name="_token" value="<?php echo $token; ?>">
  <div class='row'>
  	<div class="col-md-12">
      <form id="TypeValidation" autocomplete="off" class="form-horizontal" action="" method="POST" novalidate="novalidate">
      <input type="hidden" id='hidden_id' name="hidden_id" value="1">
        <div class="card">
          <!-- <div class="card-header card-header-rose card-header-text">
            <div class="card-icon">
              <i class="material-icons">border_color</i>
            </div>
          </div> -->
          <div class="card-body">
            <div class="row">
              <label class="col-sm-3 col-form-label">Activity Name</label>
              <div class="col-sm-6">
                <div class="form-group bmd-form-group has-danger">
                  <input class="form-control" name="title" required="true" aria-required="true" aria-invalid="true" type="text">
                </div>
              </div>
            </div>
            <div class="row">
              <label class="col-sm-3 col-form-label">Activity Date & time</label>
              <div class="col-sm-6">
               <div class="form-group bmd-form-group has-danger">
	            <input class="form-control datetimepicker" name='activity_date_time' value="" type="text" required="true"  aria-required="true" aria-invalid="true">
	          </div>
              </div>
            </div>
            <div class="row">
			    <label class="col-sm-3 col-form-label">Activity</label>
			    <div class="col-sm-6">
				    <div class="form-group bmd-form-group has-danger">
					    <select class="form-control selectpicker" data-style="btn btn-link" id="activity" title="Choose Activity" data-size="6" tabindex="-98" required name='activity' aria-invalid="true" onchange="loadengg()">
					        <option value="Team Meeting">Team Meeting</option>
							<option value="L2 Connect">L2 Connect</option>
							<option value="TL/Manager connect">TL/Manager connect</option>
							<option value="Skip level meeting">Skip level meeting</option>
							<option value="One to One">One to One</option>
							<option value="RAG">RAG</option>
							<option value="Events/Fun activity">Events/Fun activity</option>
					    </select>
					</div>
				</div>
			</div>
	   		<div class="row">
			    <label class="col-sm-3 col-form-label">Client</label>
			    <div class="col-sm-6">
				    <div class="form-group bmd-form-group has-danger">
					    <select class="form-control selectpicker" data-style="btn btn-link" id="client" name="client"  data-size="7" tabindex="-98" required title="Choose Client" onchange="loadengg()">
					        <?php 
					        	foreach ($clientArr as $key => $cname) {
					        		echo "<option value='$cname'>".$cname."</option>";
					        	}
					        ?>
					    </select>
					    <!-- <label id="client-error" class="error" for="client"></label> -->
					</div>
				</div>
			</div>
			<div class="row">
			    <label class="col-sm-3 col-form-label">Team</label>
			    <div class="col-sm-6">
				    <div class="form-group bmd-form-group has-danger">
					    <select class="form-control selectpicker" data-style="btn btn-link" id="team" name='team' title="Choose Team" data-size="7" tabindex="-98"  onchange="loadengg1()">
					    </select>
					</div>
				</div>
			</div>
			<div class="present">
				<div class="row">
				    <label class="col-sm-3 col-form-label">Present list</label>
				    <div class="col-sm-6">
					    <div class="form-group bmd-form-group has-danger">
						    <select class="form-control select1" data-style="btn btn-link" id="engg" name='pr_engg[]' title="Choose Engineer" data-size="7" tabindex="-98" multiple>
						    </select>
						</div>
					</div>
				</div>
			</div>
			<div class='absent'>
				<div class="row">
				    <label class="col-sm-3 col-form-label">Absent list</label>
				    <div class="col-sm-6">
					    <div class="form-group bmd-form-group has-danger">
						    <select class="form-control select1" data-style="btn btn-link" id="ab_engg" name='ab_engg[]' title="Choose Absent Engineer" data-size="7" tabindex="-98" multiple>
						    </select>
						</div>
					</div>
				</div>
			</div>
			<div class="rag" style='display: none'>
				<div class='row'>
					<label class="col-sm-3 col-form-label label-checkbox">RAG</label>
					<div class="col-sm-6">
						<div class="col-sm-10 checkbox-radios">
			                <div class="form-check form-check-inline">
			                  <label class="form-check-label">
			                    <input class="form-check-input" name="exampleRadios" value="option1" type="radio"> Red
			                    <span class="circle">
			                      <span class="check"></span>
			                    </span>
			                  </label>
			                </div>
			                <div class="form-check form-check-inline">
			                 	<div class="form-check">
								  <label class="form-check-label">
								    <input class="form-check-input" name="exampleRadios" value="option2" type="radio"> Amber
								    <span class="circle">
								      <span class="check"></span>
								    </span>
								  </label>
								</div>
			                </div>
			                <div class="form-check form-check-inline">
			                 	<div class="form-check">
								  <label class="form-check-label">
								    <input class="form-check-input" name="exampleRadios" value="option3" checked=""  type="radio"> Green
								    <span class="circle">
								      <span class="check"></span>
								    </span>
								  </label>
								</div>
			                </div>
			            </div>
		            </div>
				</div>
			</div>
			<div class="issue_type">
				<div class="row">
				    <label class="col-sm-3 col-form-label">Type fo issue</label>
				    <div class="col-sm-6">
					    <div class="form-group bmd-form-group has-danger">
						    <select class="form-control selectpicker" data-style="btn btn-link" id="issue" name='issue' title="Choose type of issue " data-size="7" tabindex="-98">
						    	<option value='Admin'>Admin</opiton>
								<option value='IT'>IT</opiton>
								<option value='Operations'>Operations</opiton>
								<option value='Client'>Client</opiton>
								<option value='HR'>HR</opiton>
								<option value='Payroll'>Payroll</opiton>
								<option value='Others'>Others</opiton>
						    </select>
						</div>
					</div>
				</div>
			</div>
				
			<div class="row">
			    <label class="col-sm-3 col-form-label">Comments</label>
			    <div class="col-sm-6">
				    <div class="form-group bmd-form-group has-danger">
					    <textarea class="form-control" id="comments" rows="3" name='cmd'></textarea>
					</div>
				</div>
			</div>
			</div>
			<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
				<div class="modal-dialog  modal-lg">
				    <div class="modal-content">
				        <div class="modal-header">
				            <h4 class="modal-title">Add Task</h4>
				            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
				            <i class="material-icons">clear</i>
				            </button>
				        </div>
				        <div class="modal-body">
				            <div id="entry1" class="clonedInput">

				                <h6 id="reference" name="reference" class="heading-reference">Task #1</h6>
				                <hr>
				                <fieldset>
				                    <div class='row'>
				                        <div class="col-sm-4">
				                            <div class="form-group has-danger bmd-form-group">
				                                <input class="form-control select_ttl" name="ID1_title" id="ID1_title" type="text" placeholder="Task Title">
				                            </div>
				                        </div>
				                        <div class="col-sm-4">
				                            <div class="form-group has-danger bmd-form-group">
				                                <input class="form-control input_str datetimepicker" id='ID1_start_date' name='ID1_start_date' type="text" placeholder="Start date time">
				                            </div>
				                        </div>
				                        <div class="col-sm-4">
				                            <div class="form-group has-danger bmd-form-group">
				                                <input class="form-control input_ed datetimepicker" id='ID1_end_date' name='ID1_end_date' type="text" placeholder="Start End time">
				                            </div>
				                        </div>
				                    </div>
				                    <div class='row'>
				                        <div class="col-sm-4">
				                            <div class="form-group has-danger bmd-form-group">
				                                 <select class="form-control input_st" data-style="btn btn-link" id='ID1_status'  name='ID1_status' data-size="7" tabindex="-98">
											    	<option value=''>-- Status -- </opiton>
											    	<option value='Open'>Open</opiton>
													<option value='Work in progress'>Work in progress</opiton>
													<option value='Closed'>Closed</opiton>
											    </select>
				                            </div>
				                        </div>
				                        <div class="col-sm-8">
				                            <div class="form-group has-danger bmd-form-group">
				                                <input class="form-control input_cmt" name="ID1_comments" id="ID1_comments" type="text" placeholder="Task comments" >
				                            </div>
				                        </div>
				                    </div>
				                </fieldset>
				            </div>
				        </div>
				        <div class="modal-footer">
				        	<button type="button" id="btnAdd" name="btnAdd" class="btn btn-primary btn-sm">
				                Add section
				                <div class="ripple-container"></div>
				            </button>
				            <button type="button" id="btnDel" name="btnDel" class="btn btn-danger btn-sm float-left">
				                Remove Last Section<div class="ripple-container"></div>
				            </button>
				            <button type="submit" class="btn btn-rose btn-sm">Save<div class="ripple-container"></div></button>
				        </div>
				    </div>
				</div>
      		</div>
          	<div class="card-footer ml-auto mr-auto">
          		<button id='savebtn' type="submit" class="btn btn-rose btn-sm">Save<div class="ripple-container"></div></button>
	            <button id='addbtn' type='button' class="btn btn-warning" data-toggle="modal" data-target="#myModal">Add Task<div class="ripple-container"></div></button>
	            <button type="submit" class="btn btn-red">Cancel<div class="ripple-container"></div></button>
	            <a href="form.php"><button type="button" class="btn btn-info">Refresh<div class="ripple-container"></div></button></a>
            </div>
        </div>
      </form>
    </div>
   </div>
<?php include('includes/footer.php'); ?>
 <script type="text/javascript" src="../hr/assets/clone-path.js"></script>
<script src="../hr/assets/sumoselect.min.js" type="text/javascript"></script>
<script>
$('.select1').SumoSelect({ selectAll: true,search: true ,placeholder: 'Select Here'});
$('#TypeValidation').validate();
$('.datetimepicker').datetimepicker({
    icons: {
        time: "fa fa-clock-o",
        date: "fa fa-calendar",
        up: "fa fa-chevron-up",
        down: "fa fa-chevron-down",
        previous: 'fa fa-chevron-left',
        next: 'fa fa-chevron-right',
        today: 'fa fa-screenshot',
        clear: 'fa fa-trash',
        close: 'fa fa-remove',
    }
});
function loadengg(){
	$('#engg')[0].sumo.unSelectAll();
	$('#ab_engg')[0].sumo.unSelectAll();
	let acitityname = activity.value;
	if(acitityname == 'RAG'){
	 $('.rag').css('display','block')
	}else{
	 	$('.rag').css('display','none')
	}
	if(acitityname == 'One to One' || acitityname == 'RAG' || acitityname == 'Skip level meeting'){
		$('.list').css('display','block')
		$('.absent').css('display','none');
		$('.issue_type').css('display','block')
		$('.present').css('display','block')

	}else if(acitityname == 'Events/Fun activity'){
		$('.present').css('display','none')
		$('.absent').css('display','none');
		$('.issue_type').css('display','none')
		$('#savebtn').css(display,'block')
	}else{
		$('.list').css('display','none')
		$('.present').css('display','block')
		$('.absent').css('display','block');
		$('.issue_type').css('display','block')

	}

	$('#engg').selectpicker('refresh').html('').selectpicker('refresh')
	let client = $('#client').val();
	let team = $('#team').val();
	let client_team = client + '-' + team;
	dataqury = {'client_project':client_team,'comefrom':'teamname upload','activity':acitityname};
	ajaxfun(dataqury , dropdown)
}
function loadengg1(){
	let acitityname = activity.value;
	$('#engg')[0].sumo.reload();
	let client = $('#client').val();
	let team = $('#team').val();
	let client_team = client + '-' + team;
	dataqury = {'client_project':client_team,'comefrom':'teamname upload','activity':acitityname};
	ajaxfun(dataqury , engglist)
}

function dropdown(data , jsondata) {
	 let teamname = data[0];
	 let engg = data[1];
	 console.log(jsondata)
	$('#team').selectpicker('refresh').html(teamname).selectpicker('refresh');
}
// $('#team').change(function(){

// })
function engglist(data) {
	if(data[1] != null){
		$('#engg')[0].sumo.reload();
		$('#engg').html(data[1])
		$('#engg')[0].sumo.reload();

		$('#ab_engg')[0].sumo.reload();
		$('#ab_engg').html(data[1])
		$('#ab_engg')[0].sumo.reload();
	}
}
function ajaxfun(jsondata , functionname){
	$.ajax({
		url: 'ajax.php',
		type: 'post',
		data: jsondata,
		success: function (data) {
			let opdata = JSON.parse(data)
			functionname(opdata , jsondata);
			return opdata;
		},
		error: function() {
            alert('Error occured');
        }
	});
}
</script>
