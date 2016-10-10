<?php
include('admin/includes/config.php');
function insertToDb($params)
{
    if(isset($params['filename']))
        $insertQuery = "insert into exportData (`ChartType`,`StreamType`,`MetaBgColor`,`MetaDomID`,`MetaWidth`,`MetaHeight`,`Params`,`FileName`,`SourceIP`,`SourceUseragent`,`ExportDateTime`,`Referrer`,`RemoteADDR`) values( '".trim($params['charttype'])."','".trim($params['type'])."','".trim($params['meta_bgColor'])."','".trim($params['meta_DOMId'])."','".trim($params['width'])."','".trim($params['meta_height'])."','".trim($params['parameters'])."','".trim($params['filename'])."', '".trim($_SERVER['HTTP_ORIGIN'])."','".trim($_SERVER['HTTP_USER_AGENT'])."', convert_tz(now(), 'GMT', 'Asia/Kolkata'), '".trim($_SERVER['HTTP_REFERER'])."', '".$_SERVER['REMOTE_ADDR']."'  )";
    else
        $insertQuery = "insert into exportData (`ChartType`,`StreamType`,`MetaBgColor`,`MetaDomID`,`MetaWidth`,`MetaHeight`,`Params`,`FileName`,`SourceIP`,`SourceUseragent`,`ExportDateTime`,`Referrer`,`RemoteADDR`) values( '".trim($params['charttype'])."','".trim($params['stream_type'])."','".trim($params['meta_bgColor'])."','".trim($params['meta_DOMId'])."','".trim($params['meta_width'])."','".trim($params['meta_height'])."','".trim($params['parameters'])."','".trim($params['parameters'])."', '".trim($_SERVER['HTTP_ORIGIN'])."','".trim($_SERVER['HTTP_USER_AGENT'])."', convert_tz(now(), 'GMT', 'Asia/Kolkata'), '".trim($_SERVER['HTTP_REFERER'])."', '".$_SERVER['REMOTE_ADDR']."'  )";
    mysql_query($insertQuery);
}
?>