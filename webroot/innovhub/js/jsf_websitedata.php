<?php
include "Classes.php";

$wdname = getParameter("wdname");
if($wdname==NULL) {
?>
<!DOCTYPE HTML>
<html>
<head>
  <title>JStoreFront wd tester</title>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
</head>
<body onload="setFormValues();">

<form method="post" action="jsf_websitedata.php">
<table cellpadding="3" cellspacing="1" style="font-size:14px;font-family:verdana;">
<tr><td>Domain:</td><td><input type="text" id="domaintxt" name="domain" value="<?php echo getBaseURL(); ?>" style="width:250px;font-size:12px;font-family:verdana;"></td></tr>
<tr><td>WD name:</td><td><input type="text" id="wdnametxt" name="wdname" style="width:250px;font-size:12px;font-family:verdana;"></td></tr>
<tr><td>Type:</td><td><select id="typedd" name="type" style="width:250px;font-size:12px;font-family:verdana;"><option value="jsf_getwebdata_jsonp">Single</option><option value="jsf_getwebdatapage_jsonp">Paged</option></select></td></tr>
<tr><td colspan="2"><input type="submit" name="submit" value="Submit"></td></tr>
</table>
</form>

<script>
function setFormValues(){
   var d = window.localStorage.getItem('domain');
   var w = window.localStorage.getItem('wdname');
   var t = window.localStorage.getItem('type');
   //alert('variables w:' + w + ' d: ' + d + ' t: ' + t);
   if(!Boolean(d)) d = '<?php echo getBaseURL(); ?>';
   if(Boolean(d)) jQuery('#domaintxt').val(d);
   if(Boolean(w)) jQuery('#wdnametxt').val(w);
   if(Boolean(t)) jQuery('#typedd').val(t);
}
</script>
</body>
</html>


<?php
} else {
   $type = getParameter("type");
   if($type==NULL) $type = "jsf_getwebdatapage_jsonp";
   $domain = getParameter("domain");
   if($domain==NULL) $domain=getBaseURL();
?>
<!DOCTYPE HTML>
<HTML>
<head>
  <title>JStoreFront wd tester</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta name="description" content="">
  <meta name="keywords" content="">
  <meta http-equiv='X-UA-Compatible' content='IE=8'>

   <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no"/>
   <meta name="apple-mobile-web-app-capable" content="yes" />
   <meta name="apple-touch-fullscreen" content="yes" />
   <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" /> 


  <link rel="stylesheet" href="jsf_websitedata.css" type="text/css" title="Main Styles" charset="utf-8">
  <script language="javascript" type="text/javascript" src="jsf_websitedata.js"></script>
  <script language="javascript" type="text/javascript" src="calendar.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
  <!--script src="//ajax.googleapis.com/ajax/libs/jquerymobile/1.4.3/jquery.mobile.min.js"></script-->

  <script type="text/javascript">
   $(document).ready(function() {
      //var wdname = "Dr. Mann - Cary";
      //var wdname = "chad test";
      //var wdname = 'Shopping list';
      var wdname = '<?php echo $wdname; ?>';
      var dm = '<?php echo $domain; ?>';
      var type = '<?php echo $type; ?>';
      <?php echo $type; ?>(wdname,dm);
      //jsf_getwebdatapage_jsonp(wdname,'http://www.jstorefront.com/','','','','','','',true);
      //jsf_getwebdata_jsonp(wdname,'http://www.jstorefront.com/','','','','','','',true);
      window.localStorage.setItem('domain',dm);
      window.localStorage.setItem('wdname',wdname);
      window.localStorage.setItem('type',type);
   });
  </script>
</head>
<body style="margin:0;padding:0;">
<div style="padding:15px;">
<div id="jsfwdarea"></div>
</div>
</body>
</html>

<?php } ?>