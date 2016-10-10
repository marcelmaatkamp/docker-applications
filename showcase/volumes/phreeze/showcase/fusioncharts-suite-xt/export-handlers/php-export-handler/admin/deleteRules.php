<?php 
ini_set('display_errors',1); 
error_reporting(E_ALL);
include('includes/config.php'); 
include('writehtaccess.php'); 
	$idArray = array ();
	if(empty($_POST['idString']))
	{
		echo 0;
	}
	else
	{
		$idArray = explode ( "," , trim($_POST['idString']) );
		$inClause = "" ;
		foreach ($idArray as $item)
		{
			if($inClause == "")
				$inClause  = "where id in (" . $item;
			else
				$inClause  = $inClause  . ", " .$item;
			
		}
		$inClause  = $inClause  . " )";
		
		$delQuery = "delete from ipRules " . $inClause;
		
		
		
		if(mysql_query($delQuery))
		{
			writeHtaccess();
			echo 1;
		}
		else
			echo 0;
	}
	 
	
?>