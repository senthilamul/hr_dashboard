<?php
include('includes/config.php');
$msg = $_GET['msg'];
$taskAct = $commonobj->getQry("SELECT * from hr_task");
if(isset($_POST['hdn_task_id'])){
	extract($_POST);
	$taskdetailsArr = $commonobj->getQry("SELECT * from hr_task_activity_comments WHERE activity_id='$hdn_task_id'");
	print_r($taskdetailsArr);
	//unset($taskdetailsArr[0]['end_date']);
	unset($taskdetailsArr[0]['status']);
	unset($taskdetailsArr[0]['comments']);
	unset($taskdetailsArr[0]['comments']);
	extract($taskdetailsArr[0]);

	$actionDate = str_replace('/','-',$end_date);
	$cr_date       = date("d-M-y", strtotime($actionDate));
	$date          = date("Y-m-d", strtotime($actionDate));
    $cr_addyear    = date("Y", strtotime($actionDate));
    $cr_month      = date("M", strtotime($actionDate));
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
    exit;
	$insertQry = $conn->prepare("INSERT INTO `hr_task_activity_comments`(`task_id`, `activity_id`,`activity_type`,`task_type`, `start_date`, `end_date`, `comments`, `status`, `create_by`, `created_at`) VALUES (?,?,?,?,?,?,?,?,?)");
	$insertedres= $insertQry->execute(array($task_id , $activity_id , $activity_title , $start_date , date('Y-m-d',strtotime($end_date)) , $comments ,$status, $username , $dbdatetime));
	if($insertedres){
		header('location:activity_list.php?msg=1');
		exit;
	}else{
		header('location:activity_list.php?msg=2');
		exit;
	}
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
<form method="Post" id='activity_form' autocomplete="off">
<input type="hidden" id="hdn_task" name='hdn_task_id'>
	<div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
          <?php
          	if(!empty($msg)){ ?>
          		<div class="alert <?=$msg==1?'alert-success':'alert-danger'?>">
		            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
		              <i class="material-icons">close</i>
		            </button>
		            <span>
		              <b><?=$msg==1?'Success':'Error'?> - </b> <?=$msg==1 ? 'Task Status Updated Successfully':'Task Status Not Updated'?></span>
		          </div>
          	<?php } ?>
          	<div class='row'>
          		<div class="col-md-3">
          			<input type="text" id="search"/ class="form-control" placeholder="Search Task..." style="margin-bottom:15px;">
          		</div>
          		<!-- <div class="col-md-3">
          			<div class="form-group bmd-form-group has-danger">
		            	<input class="form-control datetimepicker" name='activity_date_time' value="" type="text"aria-required="true" aria-invalid="true" placeholder="Start Date">
		          </div>
          		</div>
          		<div class="col-md-3">
          			<div class="form-group bmd-form-group has-danger">
		            	<input class="form-control datetimepicker" name='activity_end_time' value="" type="text"  aria-required="true" aria-invalid="true"  placeholder="End Date">
		          </div>
          		</div>
          		<div class="col-md-3">
          			<button type="submit" class="btn btn-info btn-sm form-control">Submit<div class="ripple-container"></div></button>
          		</div> -->
          	</div>
            <div class="table-responsive">

	              <table id='myTable' class="table table-striped table-bordered table-hover dataTable  text-center" role="grid" aria-describedby="datatables_info" width="100%" cellspacing="0">
				<thead class=" text-primary">
	                  	<tr>
	                  	<th>ID</th>
	                  	<th>Activity</th>
	                  	<th>Date</th>
	                  	<th>Client</th>
	                  	<th>Team</th>
	                  	<th>Status</th>
	                  	<th>Action</th>
	                	</tr>
	                </thead>
				</thead>
					<tbody>
	                  <?php $i=1; foreach ($taskAct as $key => $values) { extract($values); 
	                  	$idArr = $commonobj->arrayColumn($commonobj->getQry("SELECT id from hr_task_activity where task_id=".$id),'','id');
	                  	$taskAct = $commonobj->getQry("SELECT * from hr_task_activity_comments where activity_id in (".implode(',',$idArr).") ");

	                  	$lastrecordArr = tasklastRecord($taskAct);
	                  	$statusArr = statusAtt($taskAct);
	                  	if(in_array('Open',$statusArr )){
	                  		$caseStatus = 'Open';
	                  	}else if(in_array('Work in progress',$statusArr)){
	                  		$caseStatus = 'Work in progress';
	                  	}else{
	                  		$caseStatus = 'Closed';
	                  	}
	                  	?>
                  		<tr class="portlet box green" style='background-color: #D8EDEF;'>
	                  		<td><?=$i?></td>
	                  		<td><?=$activity_type?></td>
	                  		<td><?=date('d-M-Y',strtotime($activity_date_time))?></td>
	                  		<td><?=$client?></td>
	                  		<td><?=$team?></td>
	                  		<td><?=$caseStatus?></td>
	                  		<td class="buttontd">
	                  		<?php
	                  		if(count($lastrecordArr) > 0){ ?>
								<button type="button" rel="tooltip" id="activity_<?=$i?>" class="btn btn-success btn-link btn-sm" data-original-title="" title=""><i class="material-icons">toc</i><div class="ripple-container"></div></button>
								<?php } ?>
							</td>
                  		</tr>
							<thead id="task_<?php echo $i;?>" class='text-center table table-striped table-bordered table-hover' style='display: none'>
							<tr class="portlet box green" style='background-color: #32c5d2;'>
							   <td>ID</td>
			                  	<td>Task Type</td>
			                  	<td>Start Date</td>
			                  	<td>End Date</td>
			                  	<td>Comments</td>
			                  	<td>Status</td>
			                  	<td>Action</td>
							</tr>
							<?php
							   if(count($lastrecordArr) > 0){
							   	$j=1;
							    foreach ($lastrecordArr as $key => $val) {							   		
							   ?>
							<tr class='text-center' style='background-color:#D8EDEF'>
								
							   <td><?=$j?></td>
		                  		<td><?=$val['task_type']?></td>
		                  		<td><?=date('d-m-Y',strtotime($val['start_date']))?></td>
		                  		<td><?=date('d-m-Y',strtotime($val['end_date']))?></td>
		                  		<td><?=$val['task_comments']?></td>
		                  		<td><?=$val['status']?></td>
		                  		<td>
		                  			<?php if($val['status'] !='Closed' ) { ?>
		                  			<a href="#" title=""><i class="material-icons taskbtn" data-toggle="modal" style='font-size: .875rem;' id="tsk_id_<?=$val[activity_id]?>">edit</i><div class="ripple-container"></a>
		                  			<?php } ?>
		                  		</td>
							</tr>
							<?php
							   $j++;
							   }
							   } else {
							   echo "<tr><td colspan=\"6\"><center>No Records found</center></td></tr>";
							   }
							   ?>
							</thead>
	                  	<?php   $i++; } ?>
	                </tbody>
				</table>
				<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
						<div class="modal-dialog">
						    <div class="modal-content">
						        <div class="modal-header">
						            <h4 class="modal-title" id='titlehead'></h4>
						            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						            <i class="material-icons">clear</i>
						            </button>
						        </div>
						        <div class="modal-body">
					                <fieldset>
					                    <div class='row'>
					                        <!--<div class="col-sm-6">
					                            <div class="form-group has-danger bmd-form-group">
					                                <input class="form-control input_ed datetimepicker" id='end_date' name='end_date' type="text" placeholder="End date" value="">
					                            </div>
					                        </div> -->
					                        <div class="col-md-12">
					                            <div class="form-group has-danger bmd-form-group">
					                                 <select class="form-control input_st" data-style="btn btn-link" id='status'  name='status' data-size="7" tabindex="-98">
												    	<option value='Open' <?=$status=='Open'?'selected':''?>>Open</opiton>
														<option value='Work in progress' <?=$status=='Work in progress'?'selected':''?>>Work in progress</opiton>
														<option value='Closed' <?=$status=='Closed'?'selected':''?>>Closed</opiton>
												    </select>
					                            </div>
					                        </div>
					                        <div class="col-md-12">
						                    	<div class="form-group has-danger bmd-form-group">
					                                <input class="form-control input_cmt" name="comments" id="comments" type="text" placeholder="Task comments" >
					                            </div>
						                    </div>
					                    </div>
					                    
					                </fieldset>
						        </div>
						        <div class="modal-footer">
						            <button type="submit" class="btn btn-rose btn-sm" click='savefun()'>Save<div class="ripple-container"></div></button>
						        </div>
						    </div>
						</div>
	      			</div>	
            </div>
          </div>
        </div>
      </div>
    </div>
</form>
<?php include('includes/footer.php');
	function tasklastRecord($taskAct){
		foreach ($taskAct as $key => $value) {
			$lastrecordArr[$value['activity_id']] = $value;
		}
		return $lastrecordArr;
	} 
	function statusAtt($taskAct){
		foreach ($taskAct as $key => $value) {
			$statusupdate[$value['activity_id']] = $value['status'];
		}
		return $statusupdate;
	}
	
 ?>
<script>
	$(document).ready( function () {
		//$('#myTable').DataTable();
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
});
</script>

<script>

$(document).ready(function(){
	$(".dataTable tr .buttontd button").click(function(){
		//console.log($(this).attr('id'));
		var buttonid = $(this).attr('id');
		var arr = buttonid.split('_');
		var id = arr[1];
    	$("#task_"+id).slideToggle();
    	return false;
	});
});
	// function funexport(){
	//     document.getElementById("frmsrch").action = "importtaskstatus.php"; 
	//     document.getElementById("frmsrch").submit();
	//     return true;
	// }
	$(document).ready(function()
	{
		$('#search').keyup(function()
		{
			searchTable($(this).val());
		});
	});
	function searchTable(inputVal)
	{
		var table = $('#myTable');
		table.find('tr').each(function(index, row)
		{
			var allCells = $(row).find('td');
			if(allCells.length > 0)
			{
				var found = false;
				allCells.each(function(index, td)
				{
					var regExp = new RegExp(inputVal, 'i');
					if(regExp.test($(td).text()))
					{
						found = true;
						return false;
					}
				});
				if(found == true)$(row).show();else $(row).hide();
			}
		});
	}

	$('.taskbtn').click(function(){
		let attr_id= $(this).attr('id');
		 id = attr_id.split('_');
		 $.ajax({
	 		url: 'ajax.php',
	 		type: 'post',
	 		data: {'task_id':id[2],'come_form':'activity_list'},
	 		success: function (data) {
	 			let op = JSON.parse(data)
	 			console.log(op)
	 			hdn_task.value = op[0]['activity_id']
	 			/*if(op[0]['end_date'] != ''){
	 				console.log(op[0]['end_date'])
	 				let date = new Date(op[0]['end_date']);
	 				end_date.value = n(date.getDate())+'/'+ n(date.getMonth()+1)+'/'+date.getFullYear()
	 			}*/
				
	 			status.value = op[0]['status']
	 			// comments.value = op[0]['task_cmd']
	 			$('#titlehead').html(op[0]['task_type'] !='' ? op[0]['task_type'].toUpperCase() : 'Title')
	 			$('#myModal').modal({show:true});

	 		}
	 	});
	});
	function n(n){
	    return n > 9 ? "" + n: "0" + n;
	}
</script>