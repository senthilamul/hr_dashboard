<?php 
include('includes/config.php');
// include('includes/session_check.php');
if(isset($_POST['year'])){
	$TableNameArr =array('Attrition'=>'attr_master_tbl','Transfers'=>'transfers_tbl','New Joinee'=>'new_joinee_tbl','CMA list'=>'emp_master_table','Noticeperiod'=>'noticeperoid');
    if(count($_FILES['upload']) > 0){
        $tmpFilePath = $_FILES['upload']['tmp_name'];
        if($tmpFilePath != ""){
            $shortname = explode(".",$_FILES['upload']['name']);
           $filename= $_POST['filenmae'];
            echo $filePath = CSV_ROOT_PATH."csv/" . $_FILES['upload']['name'];
            if(move_uploaded_file($tmpFilePath,$filePath)) {
                try {
					$conn = new PDO("mysql:host=".SERVER.";dbname=".DATABASE, DBUSER, DBPASS);
					if($_POST['filenmae'] != 'CMA list'){
	                    $stmt=$conn->exec("TRUNCATE TABLE $TableNameArr[$filename]");
	                    $conn->exec($stmt);
	                }else{
	                	$year = $_POST['year'];
	                	$month = $_POST['month'];
  						$selectmonth = $month.'/'.substr($year ,2);
	                	$stmt=$conn->prepare("DELETE FROM `attr_master_tbl` WHERE emp_master_table where master_month ='$selectmonth'");
	                    $stmt->execute();
	                }
                    $conn->exec('LOAD DATA '.$localkeyword.' INFILE "'.$filePath.'" INTO TABLE  '. $TableNameArr[$filename]. ' FIELDS TERMINATED BY ","   OPTIONALLY ENCLOSED BY """" LINES TERMINATED BY "\n" IGNORE 1 LINES ');
                }catch(PDOException $e){  
                    echo $e->getMessage(); 
                }
                //unlink($filePath);
               	$msg = "1";
            }else{
                $msg = "2";
            }
        }
	}
}
include('includes/header.php');
?>

<form id="RegisterValidation" action="" method="post" novalidate="novalidate" enctype="multipart/form-data">
<input type="hidden" name="_token" value="<?php echo $token; ?>">
 <div class="row"> 
	<div class="card">
        <div class="card-header card-header-rose card-header-icon">
          <div class="card-icon">
            <i class="material-icons">file_upload</i>
          </div>
          <h4 class="card-title">CSV File Upload</h4>
        </div>
        <div class="card-body ">
        	<div class="alert alert-danger" id='notification' style='display: none'>
	            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
	              <i class="material-icons">close</i>
	            </button>
	            <span>
	              <b> Alert - </b> This month Already Uploaded if you want overwrite the data </span>
          	</div>  
          	<?php if(!empty($msg)){ ?>
          	<div class="alert <?=$msg=='2'?'alert-danger':'alert-success';?>">
	            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
	              <i class="material-icons">close</i>
	            </button>
	            <span>
	              <b> <?=$msg=='2'?'Error':'Sucess';?> ! </b> <?=$msg =='2'?"$filename File Not Uploaded":"$filename File Uploaded Successfully";?></span>
          	</div> 
          	<?php } ?>         
            <div class="row">
              <label class="col-md-3 col-form-label">Select Year*</label>
              <div class="col-md-6">
                <div class="form-group bmd-form-group">
                 	<div class="dropdown bootstrap-select show-tick dropup">
	                 	<select class="selectpicker" data-style="select-with-transition" title="Choose Year" data-size="7" tabindex="-98" required name='year' id='year' onchange="validation()">
		                    <option value="2018">2018</option>
		                    <option value="2019">2019</option>
		                    <option value="2020">2020</option>
		                    <option value="2021">2021</option>
		                    <option value="2022">2022</option>
		                    <option value="2023">2023</option>
		                    <option value="2024">2024</option>
		                    <option value="2025">2025</option>
	                  </select>
	                  <label id="year-error" class="error" for="year" style='display: none'></label>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <label class="col-md-3 col-form-label">Select Month</label>
              <div class="col-md-6">
                <div class="form-group bmd-form-group">
                 	<div class="dropdown bootstrap-select show-tick dropup">
	                 	<select class="selectpicker" data-style="select-with-transition" title="Choose Month" data-size="7" tabindex="-98" name='month' required id='month' onchange="validation()">
		                    <option value='Jan'>January</option>
							<option value='Feb'>February</option>
							<option value='Mar'>March</option>
							<option value='Apr'>April</option>
							<option value='May'>May</option>
							<option value='Jun'>June</option>
							<option value='Jul'>July</option>
							<option value='Aug'>August</option>
							<option value='Sep'>September</option>
							<option value='Oct'>October</option>
							<option value='Nov'>November</option>
							<option value='Dec'>December</option>
	                  </select>
	                  <label id="month-error" class="error" for="month" style='display: none'></label>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <label class="col-md-3 col-form-label">Select File Name</label>
              <div class="col-md-6">
                <div class="form-group bmd-form-group">
                 	<div class="dropdown bootstrap-select show-tick dropup">
	                 	<select class="selectpicker" data-style="select-with-transition" title="Choose File Name" data-size="7" tabindex="-98"  name='filenmae' required id='filenmae' onchange="validation()">
		                    <option value="CMA list">CMA list</option>
		                    <option value="New Joinee">New Joinee</option>
		                    <option value="Transfers">Transfers</option>
		                    <option value="Attrition">Attrition</option>
		                    <option value="Noticeperiod">Notice Period</option>
	                  </select>
	                  <label id="filenmae-error" class="error" for="filenmae" style='display: none'></label>
                  </div>
                </div>
              </div>
            </div>
           <!--  <button class="btn btn-primary btn-block" onclick="md.showNotification('top','right')">Top Right<div class="ripple-container"></div></button> -->
            <div class="row">
              <label class="col-md-3 col-form-label">Select File Name</label>
              <div class="col-md-3">
	            <!-- <input type="file" name="" class='form-control'> -->
	            <div class="bmd-form-group">
                  <input class="form-control" type="file" name='upload' required >
                  <label id="upload-error" class="error" for="upload" style='display: none'>Plese Select</label>
                </div>
            </div>
          </div>
        </div>
        <div class="card-footer ">
          <div class="row">
            <div class="col-md-9">
              <button type="submit" class="btn btn-fill btn-rose">Sign in</button>
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
</style>
<script>
	$('#RegisterValidation').validate();
	function validation(){
		$('#filenmae').val() !=  'CMA list' ? $('.alert').fadeOut(3000).css('display','none'): '';
		if($('#filenmae').val() ==  'CMA list' && $('year').val() !='' && $('#month').val() !=''){
			$.ajax({
					url: 'ajax.php',
					type: 'post',
					data: {'postdata': $('#year').val() +'-'+ $('#month').val(),'comefrom':'uploadForm'},
					success: function (data) {
						let op = jQuery.parseJSON(data)
						console.log(op)
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
</script>
