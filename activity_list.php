<?php
include('includes/config.php');
$taskAct = $commonobj->getQry("SELECT * from hr_task");
if($_POST['task_act']){
	extract($_POST);
	$activity = $task_act;
	if($task_act != 'Activity'){
		$activitylist = $commonobj->getQry("SELECT * from hr_task_activity");
		// foreach ($activitylist as $actkey => $actvalue) {
		// 	$activityArr[$actvalue['task_id']] =  $actvalue;
		// }
	}
}else{
	$activity ='Activity';
}

include('includes/header.php');
?>
<nav class="navbar navbar-expand-lg navbar-absolute fixed-top " id="navigation-example">
  <div class="container-fluid">
    <div class="navbar-wrapper">
      <div class="navbar-minimize">
        <button id="minimizeSidebar" class="btn btn-just-icon btn-white btn-fab btn-round">
        <i class="material-icons text_align-center visible-on-sidebar-regular">more_vert</i>
        <i class="material-icons design_bullet-list-67 visible-on-sidebar-mini">view_list</i>
        </button>
      </div>
      <a class="navbar-brand" href="#pablo">Activity-Task</a>
    </div>
    <marquee>CSS - Employee Engagement Survey</marquee>
  </div>
</nav>
<div class="content">
<div class="content">
<div class="container-fluid">
<form action="" method="Post" accept-charset="utf-8" id='activity_form'>
	<div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
          	<div class='row'>
          		<div class="col-md-3">
          			<div class="form-group bmd-form-group has-danger">
					    <select class="form-control selectpicker" data-style="btn btn-link" id="activity" title="Type of list" data-size="6" tabindex="-98" required name='task_act' aria-invalid="true">
					        <option value="Activity" <?=$activity=='Activity'?'Selected':'' ?>>Activity</option>
							<option value="Task" <?=$activity=='Task'?'Selected':'' ?>>Task</option>
					    </select>
					</div>
          		</div>
          		<div class="col-md-3">
          			<div class="form-group bmd-form-group has-danger">
		            	<input class="form-control datetimepicker" name='activity_date_time' value="" type="text"aria-required="true" aria-invalid="true" placeholder="Start Date">
		          </div>
          		</div>
          		<div class="col-md-3">
          			<div class="form-group bmd-form-group has-danger">
		            	<input class="form-control datetimepicker" name='activity_date_time' value="" type="text"  aria-required="true" aria-invalid="true"  placeholder="End Date">
		          </div>
          		</div>
          		<div class="col-md-3">
          			<button type="submit" class="btn btn-info btn-sm form-control">Submit<div class="ripple-container"></div></button>
          		</div>
          	</div>
            <div class="table-responsive">
            <?php if($activity == 'Activity'){ ?>
	              <table id='myTable' class="table table-striped table-no-bordered table-hover dataTable dtr-inline" role="grid" aria-describedby="datatables_info" width="100%" cellspacing="0">
	                <thead class=" text-primary">
	                  	<tr>
	                  	<th>ID</th>
	                  	<th>Title</th>
	                  	<th>Date</th>
	                  	<th>Activity Type</th>
	                  	<th>Client</th>
	                  	<th>Team</th>
	                	</tr>
	                </thead>
	                <tbody>
	                  <?php $i=1; foreach ($taskAct as $key => $value) { extract($value) ?>
                  		<tr>
	                  		<td><?=$i?></td>
	                  		<td><?=$activity_title?></td>
	                  		<td><?=date('d-m-Y',strtotime($activity_date_time))?></td>
	                  		<td><?=$activity_type?></td>
	                  		<td><?=$client?></td>
	                  		<td><?=$team?></td>
                  		</tr>
	                  	<?php $i++; }
	                  ?>
	                </tbody>
	              </table>
	            <?php } else { ?>
	            	<table id='myTable' class="table table-striped table-no-bordered table-hover dataTable dtr-inline" role="grid" aria-describedby="datatables_info" width="100%" cellspacing="0">
		                <thead class=" text-primary">
		                  	<tr>
		                  	<th>ID</th>
		                  	<th>Task Name</th>
		                  	<th>Start Date</th>
		                  	<th>End Date</th>
		                  	<th>Comments</th>
		                  	<th>Status</th>
		                  	<th>Action</th>
		                	</tr>
		                </thead>
		                <tbody>
		                  <?php $i=1; foreach ($activitylist as $key => $value) { extract($value) ?>
	                  		<tr>
		                  		<td><?=$i?></td>
		                  		<td><?=$task_title?></td>
		                  		<td><?=date('d-m-Y',strtotime($start_date))?></td>
		                  		<td><?=date('d-m-Y',strtotime($end_date))?></td>
		                  		<td><?=$task_cmd?></td>
		                  		<td><?=$status?></td>
		                  		<td><a href="#" title=""><i class="material-icons" data-toggle="modal" data-target="#myModal_<?=$i?>" style='font-size: .875rem;'>edit</i><div class="ripple-container"></a>
		                  		</td>
	                  		</tr>
	                  		<div class="modal fade" id="myModal_<?=$i?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
								<div class="modal-dialog">
								    <div class="modal-content">
								        <div class="modal-header">
								            <h4 class="modal-title">Update Task</h4>
								            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
								            <i class="material-icons">clear</i>
								            </button>
								        </div>
								        <div class="modal-body">
								            <div id="entry1" class="clonedInput">
								                <hr>
								                <fieldset>
								                    <div class='row'>
								                        <div class="col-sm-6">
								                            <div class="form-group has-danger bmd-form-group">
								                                <input class="form-control input_ed datetimepicker" id='ID1_end_date' name='ID1_end_date' type="text" placeholder="End date">
								                            </div>
								                        </div>
								                        <div class="col-sm-6">
								                            <div class="form-group has-danger bmd-form-group">
								                                 <select class="form-control input_st" data-style="btn btn-link" id='ID1_status'  name='ID1_status' data-size="7" tabindex="-98">
															    	<option value=''>-- Status -- </opiton>
															    	<option value='Open'>Open</opiton>
																	<option value='Work in progress'>Work in progress</opiton>
																	<option value='Closed'>Closed</opiton>
															    </select>
								                            </div>
								                        </div>
								                    </div>
								                    <div class='row'>
									                    <div class="col-md-12">
									                    	<div class="form-group has-danger bmd-form-group">
								                                <input class="form-control input_cmt" name="ID1_comments" id="ID1_comments" type="text" placeholder="Task comments" >
								                            </div>
									                    </div>
							                        </div>
								                </fieldset>
								            </div>
								        </div>
								        <div class="modal-footer">
								            <button type="submit" class="btn btn-rose btn-sm">Save<div class="ripple-container"></div></button>
								        </div>
								    </div>
								</div>
				      		</div>
		                  	<?php $i++; }
		                  ?>
		                </tbody>
		            </table>
	            <?php } ?>
            </div>
          </div>
        </div>
      </div>
    </div>
</form>

<?php include('includes/footer.php'); ?>
<script>
	$(document).ready( function () {
	    $('#myTable').DataTable();
	} );
	$('.datetimepicker').datetimepicker({
		format: 'L',
		format: 'DD/MM/YYYY',
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
</script>
<style>
	td , th{
		font-size: 13px;
	}
	.table thead tr th {
	    font-size: 15px;
	}
</style>