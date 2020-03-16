<?php
include_once("Classes.php");
//error_reporting(E_ALL);

$wd_id = getParameter("wd_id");
//$origemail = getParameter("origemail");
$userid = getParameter("userid");
$foruserid = getParameter("foruserid");
$wd_row_id = getParameter("wd_row_id");
$xtra = getParameter("xtra");
if($xtra==NULL && $foruserid!=NULL) $xtra = "&foruserid=".$foruserid;

unset($_SESSION['params']);
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<head>
  <title>JStoreFront</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta name="description" content="">
  <meta name="keywords" content="">
  <meta http-equiv='X-UA-Compatible' content='IE=8'>
  <link rel="stylesheet" href="<?php echo $GLOBALS['baseURLSSL']; ?>style/jsf_websitedata.css" type="text/css" title="Main Styles" charset="utf-8">
  <script language="javascript" type="text/javascript" src="<?php echo $GLOBALS['baseURLSSL']; ?>js/jsf_websitedata.js"></script>
  <script language="javascript" type="text/javascript" src="<?php echo $GLOBALS['baseURLSSL']; ?>js/calendar.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
</head>
<body>


<?php
      $ua = new UserAcct();
      $wd = new WebsiteData();
      $webdata = $wd->getWebData($wd_id);
      $sci = NULL;
      
      if($webdata['privatesrvy']<3 || $webdata['privatesrvy']==101){

         $row = $wd->getDetails($webdata['wd_id'],$wd_row_id);
         print "\n\n<!-- ***chj*** ROW from DB inline (not json):\n";
         print_r($row);
         print "\n-->\n\n";

         if ($userid==NULL && $row!=NULL) $userid = $row['userid'];
         $adminuserid = $userid;
         if ($userid!=NULL) {
            //$adminrel = $ua->getUsersRelated($userid,"to","SRVYADMIN");
            $adminrel = $wd->getUsersRelated($webdata,$userid);
            if ($adminrel!=NULL && $adminrel[0]['reluserid']>0) {
               $adminuserid = $adminrel[0]['reluserid'];
            }
         }

         $surveyuser = $ua->getUser($userid);
         $results = $wd->getDataByUserid("org properties", $surveyuser['userid']);
         $sci = $results[0];
         
         $adminuser = $ua->getUser($adminuserid);
         //print "<input type=\"hidden\" name=\"skipuser\" value=\"1\">";
         print "<div>";
         print "<div style=\"float:left;margin-right:20px;margin-bottom:10px;\">";
         print "<table cellpadding=\"2\" border=\"0\" cellspacing=\"1\">";
         print "<tr>";
         print "<td><b>Company:</b> </td><td><a href=\"".getBaseURL()."jsfadmin/admincontroller.php?action=usermodcloning&userid=".$userid."\" target=\"_bnew\">".$surveyuser['company']."</a></td>";
         print "<td><img src=\"".getBaseURL()."jsfimages/pixel.gif\" width=\"30\" height=\"1\"></td>";
         print "<td><b>Survey Admin:</b> </td><td><a href=\"".getBaseURL()."jsfadmin/admincontroller.php?action=usermodcloning&userid=".$adminuserid."\" target=\"_cnew\">".$adminuser['fname']." ".$adminuser['lname']."</a></td>";
         print "</tr>";
         print "<tr>";
         print "<td></td><td>".$surveyuser['addr1']."</td>";
         print "<td></td>";
         print "<td></td><td>".$adminuser['addr1']."</td>";
         print "</tr>";
         print "<tr>";
         print "<td></td><td>".$surveyuser['addr2']."</td>";
         print "<td></td>";
         print "<td></td><td>".$adminuser['addr2']."</td>";
         print "</tr>";
         print "<tr>";
         print "<td></td><td>".$surveyuser['city'].", ".$surveyuser['state']." ".$surveyuser['zip']." ".$surveyuser['country']."</td>";
         print "<td></td>";
         print "<td></td><td>".$adminuser['city'].", ".$adminuser['state']." ".$adminuser['zip']." ".$adminuser['country']."</td>";
         print "</tr>";
         print "<tr>";
         print "<td></td><td></td>";
         print "<td></td>";
         print "<td></td><td>".$adminuser['email']."</td>";
         print "</tr>";
         print "<tr>";
         print "<td></td><td>".$surveyuser['phonenum']."</td>";
         print "<td></td>";
         print "<td></td><td>".$adminuser['phonenum']."</td>";
         print "</tr>";
         print "<tr>";
         print "<td></td><td>".$surveyuser['phonenum1']."</td>";
         print "<td></td>";
         print "<td></td><td>".$adminuser['phonenum1']."</td>";
         print "</tr>";
         print "<tr>";
         print "<td></td><td>".$surveyuser['phonenum2']."</td>";
         print "<td></td>";
         print "<td></td><td>".$adminuser['phonenum2']."</td>";
         print "</tr>";
         print "<tr>";
         print "<td colspan=\"5\"><br></td>";
         print "</tr>";
         print "</table>";
         print "</div>";

         if(getParameter("admin")==1 && $ua->isUserAdmin(isLoggedOn())) {
            print "<div style=\"float:left;padding:0px 0px 5px 5px;margin:0px 0px 15px 5px;\">";
            print "<div><b>Admin Comments</b></div>";
            print "<div><textarea id=\"wd_comments\" style=\"width:230px;height:80px;font-size:12px;\">";
            print $row['comments'];
            print "</textarea></div>";
            print "<div><span onclick=\"savewdcomments(".$webdata['wd_id'].",".$row['wd_row_id'].",'originalwdfield_comments',jQuery('#wd_comments').val(),".$row['userid'].",'".$row['origemail']."');\" style=\"color:blue;cursor:pointer;\" id=\"wd_comments_button\">update</span></div>";
            print "</div>";
         }
         
         print "<div style=\"clear:both;\"></div>";
         print "</div>";
         print "<br><hr>";
         
         /*
         print "<div style=\"margin:3px;padding:3px;border:1px solid #EDEDED;border-radius:4px;\">";
         print "<table cellpadding=\"2\" border=\"1\" cellspacing=\"1\"><tr><td>";
         print "Update the survey admin information:<br>";
         //print $ua->printUserForm($adminuserid);
         print $ua->printUserProperties($userid);
         print "</td></tr></table>";
         print "</div>";
         */
         
      }

?>



<script type="text/javascript">
jsfwd_servercontroller = 'server/jsoncontroller.php?format=jsonp';
var jsfwdorgcallback;

 $(document).ready(function() {
       
       
   <?php 
      if(getParameter("admin")==1 && $ua->isUserAdmin(isLoggedOn())) {
         print "jsfwd_adminflag = true;\n";
         print "jsfwd_userid = '".isLoggedOn()."';\n";
         print "jsfwd_token = '".$_SESSION['s_user']['token']."';\n";
      }
   ?>
       
       
       
       
    var domain = '<?php echo getBaseURL(TRUE); ?>';
    //jsf_getwebdatasimple_jsonp(wdname,domain,callback,wd_id,prefix,userid,wd_row_id,testing,admin)
    //jsf_getwebdatasimple_jsonp('',domain,'','<?php echo $wd_id; ?>','','<?php echo $userid; ?>','<?php echo $wd_row_id; ?>',true,1);
    //jsf_getwebdatasimple_jsonp('',domain,'','<?php echo $wd_id; ?>','','<?php echo $userid; ?>','<?php echo $wd_row_id; ?>',false,1);
    //jsf_getwebdata_jsonp('',domain,'','<?php echo $wd_id; ?>','','<?php echo $userid; ?>','<?php echo $wd_row_id; ?>');
    
    <?php if($sci!=NULL) { ?>
       //jsf_getwebdata2_jsonp('org properties',domain,'','','jsfwdorg','<?php echo $surveyuser['userid']; ?>','<?php echo $sci['wd_row_id']; ?>',false,false,'','');
    <?php } ?>
    
    jsf_getwebdata_jsonp('',domain,'','<?php echo $wd_id; ?>','','<?php echo $userid; ?>','<?php echo $wd_row_id; ?>',false,false,'','<?php echo $xtra; ?>');
    //jsf_getwebdata_jsonp('',domain,'','<?php echo $wd_id; ?>','','<?php echo $userid; ?>','<?php echo $wd_row_id; ?>',true,false,'','<?php echo $xtra; ?>');
    jQuery('#jsfwdorgarea').css('width',(jQuery(window).width() - 100) + 'px');
 });
 
 function savewdcomments(wd_id,wd_row_id,fld,val,userid,origemail) {
    jQuery('#wd_comments_button').html('Loading...');
    jQuery('#wd_comments_button').css('color','#999999');
    var callback='return_savewdcomments';
    var url = defaultremotedomain + 'server/jsoncontroller.php?format=jsonp&action=submitwdfield';
    url += '&callback=' + encodeURIComponent(callback);
    url += '&wd_id=' + wd_id;
    url += '&wd_row_id=' + wd_row_id;
    url += '&field=' + fld;
    url += '&value=' + encodeURIComponent(jsfwebdata_convertstring(val));
    url += '&userid=' + userid;
    url += '&origemail=' + origemail;
    jsfwebdata_CallJSONP(url);
 }
 
 function return_savewdcomments() {
    alert('Admin comments saved successfully.');
    jQuery('#wd_comments_button').html('update');
    jQuery('#wd_comments_button').css('color','blue');
 }
</script>

<?php if($sci!=NULL) { ?>
<!-- div id="jsfwdorgarea" style="padding:10px;margin:10px 10px 40px 10px;border:4px solid #828282;border-radius:8px;width:620px;height:400px;overflow-x:hidden;overflow-y:auto;"></div -->
<?php } ?>

<div id="jsfwdarea">Loading...</div>

</body>
</html>
