<?php
	ini_set('display_errors',1); 
	error_reporting(E_ALL);
	include('includes/config.php'); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Export List </title>
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
	$selectRules = "select * from exportData";
	$selectRes = mysql_query($selectRules);
?>

<div class="table-holder">
<table class="table table-striped table-hover table-condensed">	
	<thead>
        <tr>
			<td><strong>ChartType</strong></td>
			<!--td><strong>StreamType</strong></td-->
			<td><strong>MetaBgColor</strong></td> 
			<td><strong>MetaDomID</strong></td>
			<td><strong>MetaWidth</strong></td>
			<td><strong>MetaHeight</strong></td>
			<!--td><strong>Params</strong></td-->
			<!--td><strong>FileName</strong></td-->
			<td><strong>SourceIP</strong></td>
			<!--td><strong>SourceUseragent</strong></td-->
			<td><strong>Date</strong></td>
			<!--td><strong>Referrer</strong></td-->
			<td><strong>RemoteADDR</strong></td>
		</tr>
        </thead> 
    <tbody>
	
	<?php 
		if(mysql_num_rows($selectRes)>0)
		{
			while($selectRows = mysql_fetch_assoc($selectRes))
			{
	?>
			<tr>
			<td><?php echo $selectRows['ChartType']; ?></td>
			<!--td><?php echo $selectRows['StreamType']; ?></td-->
			<td><?php echo $selectRows['MetaBgColor']; ?></td> 
			<td><?php echo $selectRows['MetaDomID']; ?></td>
			<td><?php echo $selectRows['MetaWidth']; ?></td>
			<td><?php echo $selectRows['MetaHeight']; ?></td>
			<!--td><?php echo $selectRows['Params']; ?></td-->
			<!--td><?php echo $selectRows['FileName']; ?></td-->
			<td><?php echo $selectRows['SourceIP']; ?></td>
			<!--td><?php echo $selectRows['SourceUseragent']; ?></td-->
			<td><?php echo $selectRows['ExportDateTime']; ?></td>
			<!--td><?php echo $selectRows['Referrer']; ?></td-->
			<td><?php echo $selectRows['RemoteADDR']; ?></td>
			</tr>
			
	<?php 
			}
		}
		else
		{
			echo '<tr><td colspan="11" style="text-align:center;">None</td></tr>';
		}
		
	?>
	
    	
		
    </tbody>
    
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

</body>
</html>
