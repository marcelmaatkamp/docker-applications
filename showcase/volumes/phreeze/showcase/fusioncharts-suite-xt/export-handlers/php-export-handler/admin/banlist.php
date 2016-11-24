<?php
	ini_set('display_errors',1); 
	error_reporting(E_ALL);
	include('includes/config.php'); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Export Server Admin Console</title>
<link href="bootstrap/css/bootstrap.min.css" type="text/css" rel="stylesheet" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
<link href="css/style.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery.fancybox-1.3.4/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="js/jquery.fancybox-1.3.4/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
<script language="javascript">
	jQuery(document).ready(function(){
			$(".fancy-add").fancybox({
				
				'autoScale'			: false,
				'transitionIn'		: 'none',
				'transitionOut'		: 'none',
				'type'				: 'iframe'
			});
	});
	
</script>

</head>

<body>
<div class="container">
<header>
	<a href="./" title="FusionCharts - Delightful charts and gauges in JavaScript &amp; Flash" ><img src="fusioncharts-logo.png" width="" /></a>
</header>
<hr />
<div class="row">
	<div class="span3">
		<?php include('./includes/leftmenu.php'); ?>
	</div>

<div class="span9">

<?php 
	$selectRules = "select * from ipRules";
	$selectRes = mysql_query($selectRules);
?>
<a href="addRule.php" class="btn btn-primary fancy-add">Add</a>
<div class="table-holder">
<table class="table table-striped table-hover table-condensed">	
	<thead>
    	<tr><td colspan="4"><h4>Disallowed IPs</h4></td><td colspan="2" style="text-align:right"><a href="javascript:void('0');" onclick="deleteSelected();" class="btn btn-danger">Delete</a> </td></tr>
    
        <tr><td><strong>Ip Disallowed</strong></td><td><strong>User who added</strong></td><td><strong>Date</strong></td> <td><strong>Reason</strong></td><td><strong>Edit</strong></td><td><input type="checkbox" id="selectall"/></td></tr>
        </thead> 
    <tbody>
	
	<?php 
		if(mysql_num_rows($selectRes)>0)
		{
			while($selectRows = mysql_fetch_assoc($selectRes))
			{
	?>
			<tr><td><?php echo $selectRows['ip'];?></td><td><?php echo $selectRows['userAdded'];?></td><td><?php echo $selectRows['dateAdded'];?></td><td><?php echo $selectRows['reason'];?></td><td><a href="editRule.php?id=<?php echo $selectRows['id']; ?>" class="btn btn-mini fancy-add" alt=""><i class="icon-edit"></i> EDIT</a></td><td><input type="checkbox" class="checkbox" name="selectDelete" id = "<?php echo $selectRows['id'];?>"/></td></tr>
			
	<?php 
			}
		}
		else
		{
			echo '<tr><td colspan="6" style="text-align:center;">No Rules found</td></tr>';
		}
		
	?>
	
    	
		
    </tbody>
    <tfoot>
    	<tr><td colspan="6" style="text-align:right"><a href="javascript:void('0');" onclick="deleteSelected();" class="btn btn-danger">Delete</a> </td></tr>
    </tfoot>
</table>
</div>
</div>
</div>
<hr />
<footer>
	<p><small>&copy; Copyright <script type="text/javascript">
var d=new Date();
document.write(d.getFullYear());
</script>, FusionCharts PPL.</small></p>
</footer>
</div>
<script type="text/javascript">
	function deleteSelected()
	{
		
	
		var countCheck = document.getElementsByName('selectDelete').length;
		var idArray = new Array();
		var idString, oneChecked=0, j=0 ;
		for(i=0;i<countCheck;i++)
		{
			if(document.getElementsByName('selectDelete')[i].checked)
			{
				oneChecked = oneChecked + 1;
				idArray[j] = document.getElementsByName('selectDelete')[i].id;
				j++;
			}
			
		}
		
		
		if(oneChecked>0)
		{
			var confirmFlag = confirm("Do you really want to remove these IP from ban list?");
			if(confirmFlag == true)
			{
				if(oneChecked > 1)
					idString = idArray.join();
				else
					idString = idArray[0];
				//console.log( oneChecked + ":::::"+idArray[1]);				
				$.ajax({
							type: "POST",
							url: 'deleteRules.php',
							data: { idString:  idString},					
							success: function(data) {
								if(data==1)
								{
									window.location.reload();
								}
								else
									alert("Deletion Unsuccessfull! Please Contact the administrator");
							}
						});
				//console.log(idString);
			}
		}
		else
		{
			 alert("Please select atleast One record to delete");
		}
		
		
	}
	jQuery(document).ready(function() {	
			jQuery(":checkbox").bind("click", function(e){
			$( e.target ).closest('tr').toggleClass("warning") });
			
			// add multiple select / deselect functionality
			$("#selectall").click(function () {
				  $('.checkbox').attr('checked', this.checked);
				  $('.checkbox').closest('tr').toggleClass("warning");
			});

		// if all checkbox are selected, check the selectall checkbox
		// and viceversa
			$(".checkbox").click(function(){

				if($(".checkbox").length == $(".checkbox:checked").length) {
					$("#selectall").attr("checked", "checked");			
				} else {
					$("#selectall").removeAttr("checked");
				}

			});
					
			
	});

</script>
</body>
</html>
