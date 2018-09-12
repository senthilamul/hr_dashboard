<?php 
include('includes/config.php');
// include('includes/session_check.php');
if(isset($_POST['type_activity'])){
   $actionType = $_POST['type_activity'];
   if($actionType == 'Add Activity'){
      $insertActivity = $conn->prepare("INSERT INTO `hr_activity_type` (`activity_name`,`create_by`,`create_at`) values( ?,?,?)");
      $insertRes = $insertActivity->execute(array($_POST['activity'] , $username , $dbdatetime));
   }else if($actionType == 'Add Hr'){
      $insertActivity = $conn->prepare("INSERT INTO `hr_list` (`name`,`email`,`created_at`) values( ?,?,?)");
      $insertRes = $insertActivity->execute(array($_POST['hr_name'] , $_POST['hr_email'] , $dbdatetime));
      print_r(array(ucfirst(trim($_POST['hr_name'])) , trim($_POST['hr_email']) , $dbdatetime));
   }else{
      echo "project_mapping";
      exit;
   }
   if($insertRes){
      header('location:add_project_activity.php?msg=1');
      exit;
   }else{
      //header('location:add_project_activity.php?msg=2');
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
         <a class="navbar-brand" href="#pablo">CSV File Upload</a>
      </div>
   </div>
</nav>
<!-- End Navbar -->
<div class="content">
<div class="content">
<div class="container-fluid">
<form id="RegisterValidation" action="" method="post" novalidate="novalidate" enctype="multipart/form-data">
   <input type="hidden" name="_token" value="<?php echo $token; ?>">
   <input type="hidden" name="type_activity" id='type_activity' value='Add Activity'>
   <div class="row">
    <div class="col-md-12">
      <div class="card ">
        <div class="card-header ">
          <h4 class="card-title">Activity / Hr Mapping / Add Hr</h4>
        </div>
        <div class="card-body ">
          <div class="row">
            <div class="col-md-4">
              <ul class="nav nav-pills nav-pills-rose flex-column" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active show" data-toggle="tab" href="#link4" role="tablist">
                    Add Activity
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link " data-toggle="tab" href="#link5" role="tablist">
                    Add Hr
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link " data-toggle="tab" href="#link6" role="tablist">
                    Assing Project
                  </a>
                </li>
              </ul>
            </div>
            <div class="col-md-8">
              <div class="tab-content">
                <div class="tab-pane active" id="link4">
                  <div class="form-horizontal">
                    <div class="row">
                      <label class="col-md-3 col-form-label">Activity</label>
                      <div class="col-md-9">
                        <div class="form-group has-default bmd-form-group">
                          <input class="form-control" type="text" name='activity' required>
                        </div>
                      </div>
                    </div>
                  </div>
                    <div class="row text-center">
                       <div class="col-md-6">
                          <button type="submit" class="btn btn-fill btn-rose form-co">Submit</button>
                       </div>
                    </div>
                </div>
                <div class="tab-pane  show" id="link5">
                  <div class="form-horizontal">
                    <div class="row">
                      <label class="col-md-3 col-form-label" >Name</label>
                      <div class="col-md-9">
                        <div class="form-group has-default bmd-form-group">
                          <input class="form-control" type="text" name='hr_name' id='hr_name' required>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <label class="col-md-3 col-form-label">Email</label>
                      <div class="col-md-9">
                        <div class="form-group has-default bmd-form-group">
                          <input class="form-control" type="email" name='hr_email' id='email' required>
                        </div>
                      </div>
                    </div>
                  </div>
                    <div class="row text-center">
                       <div class="col-md-6">
                          <button type="submit" class="btn btn-fill btn-rose form-co">Submit</button>
                       </div>
                    </div>
                </div>
                <div class="tab-pane" id="link6">
                 
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class='row'>
    <div class="col-md-6">
        <div class="card">
          <div class="card-header card-header-icon card-header-rose">
            <div class="card-icon">
              <i class="material-icons">assignment</i>
            </div>
            <h4 class="card-title ">Hr List</h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped table-bordered">
                <thead class=" text-primary">
                  <tr><th>
                    ID
                  </th>
                  <th>
                    Name
                  </th>
                  <th>
                    Email
                  </th>
                </tr>
                </thead>
                <tbody>
                  <?php $i=1; foreach($commonobj->getQry("SELECT * from hr_list") as $hrval){ ?>
                  <tr>
                    <td><?=$i?></td>
                    <td><?=ucwords($hrval['name'])?></td>
                    <td><?=ucwords($hrval['email'])?></td>
                  </tr>
                  <?php $i++; } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    <div class="col-md-6">
        <div class="card">
          <div class="card-header card-header-icon card-header-rose">
            <div class="card-icon">
              <i class="material-icons">assignment</i>
            </div>
            <h4 class="card-title ">Activity List</h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped table-bordered">
                <thead class=" text-primary">
                  <tr><th>
                    ID
                  </th>
                  <th>
                    Activity Name
                  </th>
                </tr>
                </thead>
                <tbody>
                  <?php $i=1; foreach($commonobj->getQry('SELECT activity_name from hr_activity_type') as $acval){ ?>
                  <tr>
                    <td><?=$i?></td>
                    <td><?=ucwords($acval['activity_name'])?></td>
                  </tr>
                  <?php $i++; } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
  </div>
</form>
<?php include("includes/footer.php"); ?>
<!-- <script src="https://unpkg.com/vue@2.1.6/dist/vue.js"></script> -->
<style>
.error{
	color: red !important;
}
.h1, .h2, .h3, .h4, body, h1, h2, h3, h4, h5, h6{
  line-height: 0.5em !important;
}
</style>
<script>
	$('#RegisterValidation').validate();
	function validation(){		
		if($('#year').val() == '' || $('#month').val() == ''){
			return false;
		}else{
			$.ajax({
				url: 'ajax.php',
				type: 'post',
				data: {'postdata': $('#year').val() +'-'+ $('#month').val(),'comefrom':'uploadForm'},
				success: function (data) {
					let op = jQuery.parseJSON(data)
					if(op[0]['count'] != 0){
						$('#notification').fadeIn(2000).css('display','block')
					}else{
						$('#notification').fadeOut(2000).css('display','none')
					}
				}
			});
		}
	}
	$('.alert').fadeOut(3000);
  $("a[role='tablist']").click(function(){
     let actiontype = $.trim($(this).text())
     actiontype
     $('#type_activity').val(actiontype);
  });
</script>
