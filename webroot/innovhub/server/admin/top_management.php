<?php
//include_once "../jsfcode/Classes.php";
//include_once $GLOBALS['rootDir'].$GLOBALS['customCodeFolder']."CustomCMS.php";
//error_reporting(E_ALL);

   $ss = new Version();
   $template = new Template();
   $ua = new UserAcct();

   $jsfversion = $ss->getValue("jsfversion");
   $subaction = getParameter("subaction");
   $action = getParameter("action");
   $adminmid = getParameter("adminmid");   
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<LINK REL="STYLESHEET" HREF="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['styleDir']; ?>style.css" TYPE="text/css">
<link rel="stylesheet" href="/style/jsf_websitedata.css" type="text/css" title="Main Styles" charset="utf-8">
<LINK REL="STYLESHEET" HREF="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.0/themes/base/jquery-ui.css" TYPE="text/css">

<style type="text/css">
   html, body {margin:0; padding:0; overflow:hidden;}
   #wcome   {position:absolute;right:10px;top:5px;color:#FFFFFF;font-family:arial;font-size:12px;}
   #wcome a {color:#DFDFDF;font-family:arial;font-size:12px;}

   .adminfooter {font-size:10px;font-family:arial;color:#444444;padding:5px;}
   .clear {clear:both;}
   .adminlink     {position:relative;padding:6px 0px 4px 10px;margin:0;cursor:pointer;                          width:172px; font-size:12px; font-family:arial; text-decoration:none;color:#222222;}
   .adminhover    {position:relative;padding:6px 0px 4px 10px;margin:0;cursor:pointer;background-color:#8f8f8f; width:172px; font-size:12px; font-family:arial; text-decoration:none;color:#FFFFFF;}
   .adminnolink   {position:relative;padding:6px 0px 4px  4px;margin:0;                                         width:172px; font-size:12px; font-family:arial; text-decoration:none;color:#000000; font-weight:bold;}
   .adminhdrlink  {position:relative;padding:6px 0px 4px  4px;margin:0;cursor:pointer; background-color:#E0E0E0; width:172px; font-size:12px; font-family:arial; text-decoration:none;color:#112299; font-weight:bold;}
   .adminhdrhover  {position:relative;padding:6px 0px 4px  4px;margin:0;cursor:pointer;background-color:#8f8f8f; width:172px; font-size:12px; font-family:arial; text-decoration:none;color:#FFFFFF; font-weight:bold;}

   .adminui_even     {position:relative;background-color:#FFFFFF;width:395px;height:31px;background-image:URL('<?php echo getBaseURL(); ?>server/admin/bullet.png');background-repeat:no-repeat;}
   .adminui_odd      {position:relative;background-color:#DEDEDE;width:395px;height:31px;background-image:URL('<?php echo getBaseURL(); ?>server/admin/bullet.png');;background-repeat:no-repeat;}
   .adminui_empty    {position:relative;background-color:#FFFFFF;width:395px;height:31px;font-size:12px;font-family:verdana;}
   .adminui_user     {position:absolute;left:0px;top:0px;width:155px;height:12px;font-size:10px;font-family:arial;color:#333333;overflow:hidden;margin:0;padding:0;}
   .adminui_date     {position:absolute;left:160px;top:0px;width:75px;height:12px;font-size:10px;font-family:arial;color:#333333;overflow:hidden;margin:0;padding:0;}
   .adminui_delete   {position:absolute;left:240px;top:0px;width:150px;height:12px;font-size:10px;font-family:arial;color:#333333;overflow:hidden;margin:0;padding:0;text-align:right;}
   .adminui_delete A {font-size:10px;font-family:arial;color:blue;overflow:hidden;margin:0;padding:0;text-align:right;}
   .adminui_content  {position:absolute;left:8px;top:14px;width:378px;height:13px;font-size:10px;font-family:arial;color:#000000;overflow:hidden;margin:0;padding:0;}

   .whitebg {background-color:#FFFFFF;}
   
   .spacer  {position:relative; margin:0; padding:0; height:20px; width:172px; overflow:hidden;}
   .divider {position:relative; margin:0; padding:0; height:2px; width:172px; overflow:hidden; background-image:URL('<?php echo getBaseURL(); ?>server/admin/divider.png'); background-repeat:repeat-x;}
   

   TR.list_row1 { background-color: #FFFFFF; font-family :  Helvetica; font-size : 11px;}
   TR.list_row2 { background-color: #B8C9F4; font-family :  Helvetica; font-size : 11px;}
   TR.list_row3 { background-color: #B0B0B0; font-family :  Helvetica; font-size : 11px;}
   
   TR.list_row1 A:link { background-color: #FFFFFF; font-family :  Helvetica; font-size : 11px; color : blue; text-decoration:none;}
   TR.list_row1 A:visited { background-color: #FFFFFF; font-family :  Helvetica; font-size : 11px; color : blue; text-decoration:none;}
   TR.list_row1 A:active { background-color: #FFFFFF; font-family : Helvetica; font-size : 11px; color : blue; text-decoration:none;}
   TR.list_row1 A:hover { background-color: #FFFFFF; font-family :  Helvetica; font-size : 11px; color : blue; text-decoration:underline;}
   
   TR.list_row2 A:link { background-color: #B8C9F4; font-family :  Helvetica; font-size : 11px; color : blue; text-decoration:none;}
   TR.list_row2 A:visited { background-color: #B8C9F4; font-family :  Helvetica; font-size : 11px; color : blue; text-decoration:none;}
   TR.list_row2 A:active { background-color: #B8C9F4; font-family : Helvetica; font-size : 11px; color : blue; text-decoration:none;}
   TR.list_row2 A:hover { background-color: #B8C9F4; font-family :  Helvetica; font-size : 11px; color : blue; text-decoration:underline;}
   
   TR.list_row3 A { background-color: #B0B0B0; font-family :  Helvetica; font-size : 11px; color : blue; text-decoration:underline;}

   TR.list_rowheader{ color : white; background-color : black; font-weight : bold; font-family :  Helvetica; font-size : 14px;}
   TD.menu {background-color : #000000;}
   tr.agentRow1 { background-color: #c0c0c0 }
   tr.agentRow2 { background-color: #ffffff }

   .list_rowlabel{ color : #404040; background-color : #DDDDDD; font-weight : normal; font-family :  Helvetica; font-size : 11px;}
   .list_row{ color : #404040; font-weight : normal; font-family :  Helvetica; font-size : 11px;}
   .list_row_highlight { color : #000000; font-weight : bold; font-family :  Helvetica; font-size : 11px;}
   .list_row_highlight A { color : #000000; font-weight : bold; font-family :  Helvetica; font-size : 11px;}
   .list_row A    { color : #404040; font-weight : normal; font-family :  Helvetica; font-size : 11px;}
   
.tinytext A:link { font-family :  Helvetica; font-size : 11px; color : blue; text-decoration:none;}
   .tinytext A:visited { font-family :  Helvetica; font-size : 11px; color : blue; text-decoration:none;}
   .tinytext A:active { font-family : Helvetica; font-size : 11px; color : blue; text-decoration:none;}
   .tinytext A:hover { font-family :  Helvetica; font-size : 11px; color : blue; text-decoration:underline;}
   .tinytext { font-family :  Helvetica; font-size : 11px;}
   .normal { font-family :  Helvetica; font-size : 14px;}

.small_table A:link    { color:BLUE; font-size:10px; font-family : arial, helvetica, sans-serif; font-weight:normal; vertical-align:top; text-decoration:none;}
.small_table A:visited { color:BLUE; font-size:10px; font-family : arial, helvetica, sans-serif; font-weight:normal; vertical-align:top; text-decoration:none;}
.small_table A:active  { color:BLUE; font-size:10px; font-family : arial, helvetica, sans-serif; font-weight:normal; vertical-align:top; text-decoration:none;}
.small_table A:hover   { color:BLUE; font-size:10px; font-family : arial, helvetica, sans-serif; font-weight:normal; vertical-align:top; text-decoration:underline;}
.small_table   { color:#000000; font-size:10px; font-family : arial, helvetica, sans-serif; font-weight:normal; vertical-align:top; text-decoration:none;}

.small_table_header    {text-align: center; color:#000000; font-size:12px; font-family : arial, helvetica, sans-serif; font-weight:bold; vertical-align:top; text-decoration:none;}
.small_table_header A:link    { text-align: center; color:BLUE; font-size:12px; font-family : arial, helvetica, sans-serif; font-weight:bold; vertical-align:top; text-decoration:none;}
.small_table_header A:visited { text-align: center; color:BLUE; font-size:12px; font-family : arial, helvetica, sans-serif; font-weight:bold; vertical-align:top; text-decoration:none;}
.small_table_header A:active  { text-align: center; color:BLUE; font-size:12px; font-family : arial, helvetica, sans-serif; font-weight:bold; vertical-align:top; text-decoration:none;}
.small_table_header A:hover   { text-align: center; color:BLUE; font-size:12px; font-family : arial, helvetica, sans-serif; font-weight:bold; vertical-align:top; text-decoration:underline;}

.tableinput {font-size:10px;font-family: Arial;}


.reg_table A:link    { color:BLUE; font-size:12px; font-family : arial, helvetica, sans-serif; font-weight:normal; vertical-align:top; text-decoration:none;}
.reg_table A:visited { color:BLUE; font-size:12px; font-family : arial, helvetica, sans-serif; font-weight:normal; vertical-align:top; text-decoration:none;}
.reg_table A:active  { color:BLUE; font-size:12px; font-family : arial, helvetica, sans-serif; font-weight:normal; vertical-align:top; text-decoration:none;}
.reg_table A:hover   { color:BLUE; font-size:12px; font-family : arial, helvetica, sans-serif; font-weight:normal; vertical-align:top; text-decoration:underline;}
.reg_table   { color:#000000; font-size:12px; font-family : arial, helvetica, sans-serif; font-weight:normal; vertical-align:top; text-decoration:none;}

.reg_table_header    {text-align: center; color:#000000; font-size:14px; font-family : arial, helvetica, sans-serif; font-weight:bold; vertical-align:top; text-decoration:none;}
.reg_table_header A:link    { text-align: center; color:BLUE; font-size:14px; font-family : arial, helvetica, sans-serif; font-weight:bold; vertical-align:top; text-decoration:none;}
.reg_table_header A:visited { text-align: center; color:BLUE; font-size:14px; font-family : arial, helvetica, sans-serif; font-weight:bold; vertical-align:top; text-decoration:none;}
.reg_table_header A:active  { text-align: center; color:BLUE; font-size:14px; font-family : arial, helvetica, sans-serif; font-weight:bold; vertical-align:top; text-decoration:none;}
.reg_table_header A:hover   { text-align: center; color:BLUE; font-size:14px; font-family : arial, helvetica, sans-serif; font-weight:bold; vertical-align:top; text-decoration:underline;}


.admbtn2 a    { 
   display:inline-block;
   margin: 0px 3px 2px 3px; 
   padding: 3px 5px 3px 5px; 
   text-decoration:none; 
   font-family:arial; 
   font-weight:bold; 
   text-align:center; 
   background-image:url('<?php echo getBaseURL(); ?>jsfimages/btn2_bg.png');
   background-repeat:repeat-x;
   color:#000000; 
   font-size:10pt; 
   border: 1px solid #AAAAAA; 
}

.admbtn1 a    { 
   display:inline-block; 
   margin: 0px 3px 2px 3px; 
   padding: 3px 5px 3px 5px; 
   text-decoration:none; 
   font-family:arial; 
   font-weight:bold; 
   text-align:center; 
   background-image:url('<?php echo getBaseURL(); ?>jsfimages/btn_bg.png');
   background-repeat:repeat-x;
   color:#EEEEEE; 
   font-size:10pt; 
   border: 1px solid #AAAAAA; 
}

.btn1 a { 
        display:inline-block; 
        margin: 0px 0px 0px 8px; 
        padding: 3px 8px 3px 8px; 
        text-decoration:none; 
        font-family:arial; 
        font-weight:bold; 
        text-align:center; 
        color:#222222; 
        background-color:#CCCCCC;
        font-size:16px;      
        border-top:2px solid #222222;
        border-left:2px solid #222222;
        border-right:2px solid #222222;
        border-bottom:2px solid #CCCCCC;
        border-top-left-radius:10px;
}

.btn2 a { 
        display:inline-block; 
        margin: 0px 0px 0px 8px; 
        padding: 3px 8px 3px 8px; 
        text-decoration:none; 
        font-family:arial; 
        font-weight:bold; 
        text-align:center; 
        color:#FFFFFF; 
        background-color:#248beb;
        font-size:16px;      
        border-top:2px solid #222222;
        border-left:2px solid #222222;
        border-right:2px solid #222222;
        border-bottom:2px solid #248beb;
        border-top-left-radius:10px;
 }


.error {
  color:RED;
  font-size:12px;
  font-family:arial;
  font-weight:bold;
}

.msg {
  color:GREEN;
  font-size:12px;
  font-family:arial;
  font-weight:bold;
}

.ui-datepicker {background-color: #E1E1E1;}

</style>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script src="/js/jsf_jsonrequest.js"></script>
<script type="text/javascript" language="javascript">

function popup(url, width, height) {
  var left = (screen.width - width) / 2;
  var top = (screen.height - height) / 2;
  new_win = window.open(url, "new_win", "width="+width+",height="+height+",left="+left+",top="+top+",scrollbars=yes,resizable=yes");
  return new_win;
}

//var _maxColHeight = 0;
var colwidth = 250;
var colpadding = 7;
var topheaderheight = 31;
var subheaderheight = 25;
var sidebarwidth = 172;
var subheaderheight = 25;
var sidefooterheight = 32;
//var jsfadmin_containerwidth;

var winwidth = $(window).width();
var winheight = $(window).height();
var innerpadding = 5;

var _maxColHeight = (winheight - (innerpadding + topheaderheight + subheaderheight));
var jsfadmin_containerwidth = (winwidth - (2 * innerpadding))-(sidebarwidth + innerpadding);

function fixWidths() {
   winwidth = $(window).width();
   winheight = $(window).height();
   $('#outercontainer').css('position','relative');
   $('#outercontainer').css('left',0);
   $('#outercontainer').css('top',0);
   $('#outercontainer').css('background-color','#0768b1');
   $('#outercontainer').css('height',winheight);
   $('#outercontainer').css('width',winwidth);
   $('#outercontainer').css('overflow','hidden');

   $('#header').css('position','relative');
   $('#header').css('background-image','URL(<?php echo getBaseURL(); ?>server/admin/topbg.png)');
   $('#header').css('height',topheaderheight);
   $('#header').css('width',winwidth);
   $('#header').css('overflow','hidden');

   $('#innercontainer').css('position','relative');
   $('#innercontainer').css('background-color','#0768b1');
   $('#innercontainer').css('height',winheight-topheaderheight);
   $('#innercontainer').css('width',winwidth);
   $('#innercontainer').css('overflow','hidden');

   $('#innerheader').css('position','relative');
   $('#innerheader').css('background-color','#0768b1');
   $('#innerheader').css('height',subheaderheight);
   $('#innerheader').css('width',winwidth);
   $('#innerheader').css('overflow','hidden');

   $('#innercontent').css('position','relative');
   $('#innercontent').css('left',innerpadding);
   $('#innercontent').css('top',0);
   $('#innercontent').css('background-color','#FFFFFF');
   $('#innercontent').css('height',(winheight - (innerpadding + topheaderheight + subheaderheight)));
   $('#innercontent').css('width',(winwidth - (2 * innerpadding)));
   $('#innercontent').css('overflow','hidden');

   $('#leftside').css('position','absolute');
   $('#leftside').css('left',0);
   $('#leftside').css('top',0);
   $('#leftside').css('background-color','#CCCCCC');
   $('#leftside').css('height',(winheight - (innerpadding + topheaderheight + subheaderheight) - sidefooterheight));
   $('#leftside').css('width',sidebarwidth);
   $('#leftside').css('overflow-x','hidden');
   $('#leftside').css('overflow-y','auto');

   $('#leftfooter').css('position','absolute');
   $('#leftfooter').css('left',0);
   $('#leftfooter').css('top',(winheight - (innerpadding + topheaderheight + subheaderheight) - sidefooterheight));
   $('#leftfooter').css('background-color','#CCCCCC');
   $('#leftfooter').css('height',sidefooterheight);
   $('#leftfooter').css('width',sidebarwidth);
   $('#leftfooter').css('overflow','hidden');

   $('#leftsideborder').css('position','absolute');
   $('#leftsideborder').css('left',sidebarwidth);
   $('#leftsideborder').css('top',0);
   $('#leftsideborder').css('background-color','#0768b1');
   $('#leftsideborder').css('height',(winheight - (innerpadding + topheaderheight + subheaderheight)));
   $('#leftsideborder').css('width',innerpadding);
   $('#leftsideborder').css('overflow','hidden');

   $('#container').css('position','absolute');
   $('#container').css('left',(sidebarwidth + innerpadding));
   $('#container').css('top',0);
   $('#container').css('background-color','#FFFFFF');
   $('#container').css('height',_maxColHeight);
   $('#container').css('width',jsfadmin_containerwidth);
   $('#container').css('overflow','auto');
}

$(document).ready(fixWidths);
window.onresize = fixWidths;
</script>
</head>
<body style="border:0px;padding:0px;margin:0px;">

<div id="outercontainer">
   <div id="header"><a href="<?php echo getBaseURL(); ?>server/admin/admincontroller.php"><IMG SRC="<?php echo getBaseURL(); ?>server/admin/logo.png" border="0"></a></div>
   <div id="innercontainer">
      <div id="innerheader">
         <div id="wcome">
         <?php
            $user = array();
            if (isLoggedOn()){
               $user = $ua->getFullUserInfo(isLoggedOn());
               print "Welcome ".$user['fname']." ".$user['lname']." ( ".$user['email']." ) &nbsp; &nbsp; <a href=\"admincontroller.php?action=usermod&userid=".$user['userid']."\">Account</a> &nbsp; &nbsp; <a href=\"admincontroller.php?action=logout\">Log out</a> ";
            } else {
               print " ";
            }
         ?>
         </div>
      </div>
      <div id="innercontent">
         <div id="leftside">



<!--------------------------------------------------------------------------->
<!-- Start of left side -->
   
   <!--div class="adminnolink"><?php echo $vars['defaultTitle']; ?></div-->
<?php
   //error_reporting(E_ALL);
   if ($ua->isUserAdmin(isLoggedOn())) {
      $menuid = getParameter("menuid");
      if($menuid==NULL) $menuid = $_SESSION['menuid'];
      else $_SESSION['menuid'] = $menuid;
      $wd = new WebsiteData();
      $wdname = "Admin Menu";
      $rows = $wd->runFullWDSQL($wdname,FALSE);
      $fld = $wd->getField($wdname,"parent");
      //print_r($fld);
      $newmenu = $wd->structureParentChild($rows,$fld);
      //print_r($newmenu);
      
      for ($i=0; $i<count($newmenu); $i++) {
         $line = $newmenu[$i];
         if($line['url']==NULL) $line['url'] = $line['link'];
         if($line['url']==NULL) $line['url'] = $line['onclick'];
         $selected = FALSE;
         if(strpos($line['url'],"?")!==FALSE) $line['url'] .= "&menuid=".$line['wd_row_id'];
         else $line['url'] .= "?menuid=".$line['wd_row_id'];
         if($line['wd_row_id'] == $menuid) $selected = TRUE;
         //print_r($line);
         // Is this a parent item or a child?
         if($line['structure_depth']==0) {
            print "<div class=\"divider\"></div>";
            if ($selected) {
?>
               <div CLASS="adminhdrhover" onClick="window.location.href='<?php echo $line['url']; ?>';">
                  <?php echo $line['title']; ?>
               </div>
<?php
            } else {
?>
               <div CLASS="adminhdrlink" onClick="window.location.href='<?php echo $line['url']; ?>';" ONMOUSEOVER="this.className='adminhdrhover';" ONMOUSEOUT="this.className='adminhdrlink';">
                  <?php echo $line['title']; ?>
               </div>
<?php
            }
         } else {
               $linedisplay = "&bull; ".$line['title'];
               $cls = "adminlink";
               if ($selected) $cls = "adminhover";
?>
               <div CLASS="<?php echo $cls; ?>" onClick="window.location.href='<?php echo $line['url']; ?>';" ONMOUSEOVER="this.className='adminhover';" ONMOUSEOUT="this.className='<?php echo $cls; ?>';">
                  <?php echo $linedisplay; ?>
               </div>
<?php
         }
      }
   }

?>

<!-- End of left side -->
<!--------------------------------------------------------------------------->


         
         </div>
         <div id="leftfooter">
            <div class="adminfooter">Copyright &copy; <?php echo date("Y"); ?> jStorefront<br>ver. <?php echo $jsfversion; ?></div>
         </div>
         
         <div id="leftsideborder"></div>
         <div id="container">
         <div id="panels" style="position:relative;padding:5px;">
<?php
  //if (isset($vars['msg'])) print "<div class=\"msg\">".$vars['msg']."</div>";
  //if (isset($vars['error'])) print "<div class=\"error\">".$vars['error']."</div>";
   $error = "";
   $msg="";
   if (isset($vars['error'])) $error = "<div id=\"cms_jsfa_error\" onclick=\"document.getElementById('cms_jsfa_error').style.display='none';\" style=\"padding:6px;color:red;background-color:#FFBBBB;border:1px solid RED;font-family:arial;font-size:12px;\">".$vars['error']." &nbsp; <span style=\"text-decoration:underline;font-size:10px;color:blue;cursor:pointer;\">close</span></div>";
   if (isset($vars['msg'])) $msg = "<div id=\"cms_jsfa_msg\" onclick=\"document.getElementById('cms_jsfa_msg').style.display='none';\" style=\"padding:6px;color:GREEN;background-color:#BBFFBB;border:1px solid GREEN;font-family:arial;font-size:12px;\">".$vars['msg']." &nbsp; <span style=\"text-decoration:underline;font-size:10px;color:blue;cursor:pointer;\">close</span></div>";
   print $error.$msg;
?>

