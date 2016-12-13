<?php 
ini_set('display_errors',1); 
error_reporting(E_ALL);
include('includes/config.php'); 
 function writeHtaccess()
 {
	$ipArray = array();
	$selectQry = "select ip from ipRules";
	$selectRes = mysql_query($selectQry);
	$i = 0;
	$string  = "Order Deny,Allow\n";
	$file = '../.htaccess';
	if(mysql_num_rows($selectRes)>0)
	{
		
		while($selectRow =mysql_fetch_assoc($selectRes))
		{
			// do nothing 
			//$ipArray[$i] = $selectRow['ip'];
			//$i++;
			
			$string .= "\nDeny from " . $selectRow['ip'];
		}
		
		// Open the file to get existing content
		//$current = file_get_contents($file);
		// Append a new person to the file
		
		
		
		// Write the contents back to the file
		
		
	}
	else
	{
		// do nothing
	}
	file_put_contents($file, $string);
 }
?>