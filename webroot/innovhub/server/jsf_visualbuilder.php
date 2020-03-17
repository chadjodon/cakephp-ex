<?php
include "Classes.php";

$userid = getParameter("userid");
$token = getParameter("token");
$wd_id = getParameter("wd_id");
$name = getParameter("name");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta charset="utf-8" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no"/>
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-touch-fullscreen" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <link rel="stylesheet" href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['styleFolder']; ?>jsf_websitedata.css" type="text/css" title="Main Styles" charset="utf-8">    
    <link rel="stylesheet" href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['styleFolder']; ?>style.css" type="text/css" title="Main Styles" charset="utf-8">    
    <meta name="msapplication-tap-highlight" content="no" />
    <title>Visual Builder</title>
    <meta http-equiv="cache-control" content="no-cache">
    <meta http-equiv="pragma" content="no-cache">
</head>
<body style="margin:0px;padding:0px;font-family:Roboto;">
<div id="pmcs"></div>   
<script language="javascript" type="text/javascript" src="<?php echo $GLOBALS['baseURL'].$GLOBALS['jsFolder']; ?>jquery-1.11.2.min.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $GLOBALS['baseURL'].$GLOBALS['jsFolder']; ?>jsf_pagebuilder.js"></script>  
<script language="javascript" type="text/javascript" src="<?php echo $GLOBALS['baseURL'].$GLOBALS['jsFolder']; ?>jsf_pagebuilder_admin.js"></script>  
<script language="javascript" type="text/javascript" src="<?php echo $GLOBALS['baseURL'].$GLOBALS['jsFolder']; ?>jsf_pagebuilder_widgets.js"></script>  
<script language="javascript" type="text/javascript" src="<?php echo $GLOBALS['baseURL'].$GLOBALS['jsFolder']; ?>jsf_visualbuilder.js"></script>  
<script language="javascript" type="text/javascript" src="<?php echo $GLOBALS['baseURL'].$GLOBALS['jsFolder']; ?>jsf_search_v2.js"></script>  
<script language="javascript" type="text/javascript" src="<?php echo $GLOBALS['baseURL'].$GLOBALS['jsFolder']; ?>jsf_adatood.js"></script>  
<script language="javascript" type="text/javascript" src="<?php echo $GLOBALS['baseURL'].$GLOBALS['jsFolder']; ?>jsf_core.js"></script>  
<script type="text/javascript">


jsfpb_domain = '<?php echo $GLOBALS['baseURLSSL']; ?>';
jsfv_userid = '<?php echo $userid; ?>';
jsfv_token = '<?php echo $token; ?>';
jsfpb_codedir = 'server/';
jsfcore_servercontroller = jsfpb_codedir + 'jsoncontroller.php?format=jsonp';
jsfada_servercontroller = jsfcore_servercontroller;
jsfpb_servercontroller = jsfcore_servercontroller;
//jsfwd_servercontroller = jsfcore_servercontroller;
jsfpb_jsoncontroller = jsfpb_codedir + 'jsoncontroller.php?format=json';


jQuery(document).ready(function() {
   jsfv_initadmin('<?php echo $wd_id; ?>','<?php echo $name; ?>','pmcs');
 });
</script>

</body>
</html>