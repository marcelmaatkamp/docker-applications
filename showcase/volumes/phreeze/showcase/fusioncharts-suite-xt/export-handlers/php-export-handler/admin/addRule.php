<?php
	ini_set('display_errors',1); 
	error_reporting(E_ALL);
	include('includes/config.php'); 
	include('writehtaccess.php'); 
	if(isset($_POST["submit"]))
	{
		$ip = trim(addslashes($_POST["ip"]));
		$reason = trim(addslashes($_POST["reason"]));
		
		if($ip == "")
		{
			//do nothing
				 showForm("Add", "Null Ip can't be disallowed!");
		}
		else 
		{
			$insertStatement = "insert into exportServerDb.ipRules (ip,accessRule,dateAdded,reason) values ('".$ip ."','Denied',convert_tz(now(), 'GMT', 'Asia/Kolkata'),'".$reason."')";
			if(mysql_query($insertStatement))
			{
				writeHtaccess();
				showForm("Add", "Added Successfully!");
			?>
				<script>
					 parent.location.reload(true);
				</script>
			<?php 
			}
			else
			{
				showForm("Add", "Error Occurred!");
			}
		}
		/*else  if(trim(addslashes($_POST["id"]))!="")
		{
			$editStatement = "update exportServerDb.ipRules  set ip = '".$ip ."',reason = '".$reason."' where id=".$_POST["id"];
			if(mysql_query($editStatement))
			{
				showForm("Edit", "Edited Successfully!");
			?>
				<script>
					 parent.location.reload(true);
				</script>
			<?php 
			}
			else
			{
				showForm("Edit", "Error Occurred!");
			}
		}*/
	}
	else
	{
		showForm("Add", "");
	}
	function showForm($mode,$msg,$params = array())
	{
		?>
			
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title><?php echo $mode;?> CIDR/IP</title>
			<link href="bootstrap/css/bootstrap.min.css" type="text/css" rel="stylesheet" />
			<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
			<script src="bootstrap/js/bootstrap.min.js"></script>
			</head>
			<body>
			<div class="container">
			<div style="color:red;text-align:center;"><?php echo $msg;?></div>
			<h3 style="text-align:center;">Add CIDR/IP to the Banlist</h3>
			<form class="form-horizontal" action="" method="post">
				<div class="control-group">
				<label class="control-label" for="ip">CIDR/IP to disallow</label>
				<div class="controls">
				  <input type="text" id="ip" name ="ip"  class="span2" ">
				  
				</div>
			  </div>
			  
			  <div class="control-group">
				<label class="control-label" for="reason">Reason</label>
				<div class="controls">
				  <textarea rows="3" id="reason" class="span4" name="reason"></textarea>
				</div>
			  </div>
			  <div class="control-group">
				<div class="controls">      
					<input type="hidden" name="id" id="id" >
				  <button type="submit" name="submit" class="btn btn-primary">Submit</button>
				</div>
			  </div>
			</form>
			</div>
			</body>
			</html>
		<?php 
	}
?>