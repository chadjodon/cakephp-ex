<?php
include_once "../jsfcode/Classes.php";
include_once $GLOBALS['rootDir'].$GLOBALS['customCodeFolder']."CustomCMS.php";

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

<style type="text/css">
   html, body {margin:0; padding:0;}
   #wcome   {position:absolute;right:10px;top:5px;color:#FFFFFF;font-family:arial;font-size:12px;}
   #wcome a {color:#DFDFDF;font-family:arial;font-size:12px;}

   .adminfooter {font-size:10px;font-family:arial;color:#444444;padding:5px;}
   .clear {clear:both;}
   .adminlink     {position:relative;padding:6px 0px 4px 10px;margin:0;cursor:pointer;                          width:172px; font-size:12px; font-family:arial; text-decoration:none;color:#222222;}
   .adminhover    {position:relative;padding:6px 0px 4px 10px;margin:0;cursor:pointer;background-color:#8f8f8f; width:172px; font-size:12px; font-family:arial; text-decoration:none;color:#FFFFFF;}
   .adminnolink   {position:relative;padding:6px 0px 4px  4px;margin:0;                                         width:172px; font-size:12px; font-family:arial; text-decoration:none;color:#000000; font-weight:bold;}
   .adminhdrlink  {position:relative;padding:6px 0px 4px  4px;margin:0;cursor:pointer;                          width:172px; font-size:12px; font-family:arial; text-decoration:none;color:#000000; font-weight:bold;}
   .adminhdrhover  {position:relative;padding:6px 0px 4px  4px;margin:0;cursor:pointer;background-color:#8f8f8f; width:172px; font-size:12px; font-family:arial; text-decoration:none;color:#FFFFFF; font-weight:bold;}

   .adminui_even     {position:relative;background-color:#FFFFFF;width:395px;height:31px;background-image:URL('<?php echo getBaseURL(); ?>jsfadmin/bullet.png');background-repeat:no-repeat;}
   .adminui_odd      {position:relative;background-color:#DEDEDE;width:395px;height:31px;background-image:URL('<?php echo getBaseURL(); ?>jsfadmin/bullet.png');;background-repeat:no-repeat;}
   .adminui_empty    {position:relative;background-color:#FFFFFF;width:395px;height:31px;font-size:12px;font-family:verdana;}
   .adminui_user     {position:absolute;left:0px;top:0px;width:155px;height:12px;font-size:10px;font-family:arial;color:#333333;overflow:hidden;margin:0;padding:0;}
   .adminui_date     {position:absolute;left:160px;top:0px;width:75px;height:12px;font-size:10px;font-family:arial;color:#333333;overflow:hidden;margin:0;padding:0;}
   .adminui_delete   {position:absolute;left:240px;top:0px;width:150px;height:12px;font-size:10px;font-family:arial;color:#333333;overflow:hidden;margin:0;padding:0;text-align:right;}
   .adminui_delete A {font-size:10px;font-family:arial;color:blue;overflow:hidden;margin:0;padding:0;text-align:right;}
   .adminui_content  {position:absolute;left:8px;top:14px;width:378px;height:13px;font-size:10px;font-family:arial;color:#000000;overflow:hidden;margin:0;padding:0;}

   .whitebg {background-color:#FFFFFF;}
   
   .spacer  {position:relative; margin:0; padding:0; height:20px; width:172px; overflow:hidden;}
   .divider {position:relative; margin:0; padding:0; height:2px; width:172px; overflow:hidden; background-image:URL('<?php echo getBaseURL(); ?>jsfadmin/divider.png'); background-repeat:no-repeat;}
   .arrow {position:relative;margin:0;padding:0;overflow:hidden;width:172px;background-image:URL('<?php echo getBaseURL(); ?>jsfadmin/arrow.png');background-repeat:no-repeat;}
   

   TR.list_row1 { background-color: #FFFFFF; font-family :  Helvetica; font-size : 11px;}
   TR.list_row2 { background-color: #B8C9F4; font-family :  Helvetica; font-size : 11px;}
   
   TR.list_row1 A:link { background-color: #FFFFFF; font-family :  Helvetica; font-size : 11px; color : blue; text-decoration:none;}
   TR.list_row1 A:visited { background-color: #FFFFFF; font-family :  Helvetica; font-size : 11px; color : blue; text-decoration:none;}
   TR.list_row1 A:active { background-color: #FFFFFF; font-family : Helvetica; font-size : 11px; color : blue; text-decoration:none;}
   TR.list_row1 A:hover { background-color: #FFFFFF; font-family :  Helvetica; font-size : 11px; color : blue; text-decoration:underline;}
   
   TR.list_row2 A:link { background-color: #B8C9F4; font-family :  Helvetica; font-size : 11px; color : blue; text-decoration:none;}
   TR.list_row2 A:visited { background-color: #B8C9F4; font-family :  Helvetica; font-size : 11px; color : blue; text-decoration:none;}
   TR.list_row2 A:active { background-color: #B8C9F4; font-family : Helvetica; font-size : 11px; color : blue; text-decoration:none;}
   TR.list_row2 A:hover { background-color: #B8C9F4; font-family :  Helvetica; font-size : 11px; color : blue; text-decoration:underline;}
   
   TR.list_rowheader{ color : white; background-color : black; font-weight : bold; font-family :  Helvetica; font-size : 14px;}
   TD.menu {background-color : #000000;}
   tr.agentRow1 { background-color: #c0c0c0 }
   tr.agentRow2 { background-color: #ffffff }

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


.admbtn2 a:link    { 
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

.admbtn2 a:visited    { 
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

.admbtn2 a:active    { 
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

.admbtn2 a:hover    { 
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



.admbtn1 a:link    { 
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

.admbtn1 a:visited    { 
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

.admbtn1 a:active    { 
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

.admbtn1 a:hover    { 
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

</style>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" language="javascript">

var _maxColHeight = 0;
var colwidth = 250;
var colpadding = 7;
var topheaderheight = 31;
var subheaderheight = 25;
var sidebarwidth = 172;
var subheaderheight = 25;
var sidefooterheight = 32;


function fixWidths() {
   var winwidth = $(window).width();
   var winheight = $(window).height();
   var innerpadding = 5;
   $('#outercontainer').css('position','relative');
   $('#outercontainer').css('left',0);
   $('#outercontainer').css('top',0);
   $('#outercontainer').css('background-color','#0768b1');
   $('#outercontainer').css('height',winheight);
   $('#outercontainer').css('width',winwidth);
   $('#outercontainer').css('overflow','hidden');

   $('#header').css('position','relative');
   $('#header').css('background-image','URL(<?php echo getBaseURL(); ?>jsfadmin/topbg.png)');
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

   _maxColHeight = (winheight - (innerpadding + topheaderheight + subheaderheight));
   $('#container').css('position','absolute');
   $('#container').css('left',(sidebarwidth + innerpadding));
   $('#container').css('top',0);
   $('#container').css('background-color','#FFFFFF');
   $('#container').css('height',_maxColHeight);
   $('#container').css('width',(winwidth - (2 * innerpadding))-(sidebarwidth + innerpadding));
   $('#container').css('overflow','auto');
}

$(document).ready(fixWidths);
window.onresize = fixWidths;
</script>
</head>
<body style="border:0px;padding:0px;margin:0px;">

<div id="outercontainer">
   <div id="header"><a href="<?php echo getBaseURL(); ?>jsfadmin/admincontroller.php"><IMG SRC="<?php echo getBaseURL(); ?>jsfadmin/logo.png" border="0"></a></div>
   <div id="innercontainer">
      <div id="innerheader">
         <div id="wcome">
         <?php
            if (isLoggedOn()){
               $user = $ua->getUser(isLoggedOn());
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
   
   <div class="adminnolink"><?php echo $vars['defaultTitle']; ?></div>
<?php
   if ($ua->isUserAdmin(isLoggedOn())) {
      $menu = new Menu($GLOBALS['baseURLSSL']);
      $menuname = $ss->getValue("AdminMenu");
      if ($menuname==NULL) $menuname = "ManagementMenu";
      $menuid = $menu->getMenuIdFromName($menuname);
      //$menuid = $menu->getMenuIdFromName("AdminMenu");
      $children = $menu->getChildrenItems(-1,TRUE,$menuid);
   
      for ($i=0; $i<count($children); $i++) {
         $line = $children[$i];
         if ($line['menuname']!= NULL && strcmp(trim($line['menuname']),"")!=0) {
            print "<div class=\"divider\"></div>";
            $grandchildren = $menu->getChildrenItems($line['itemid'],TRUE,$menuid);
            $lineURL = $template->doBasicSubstitutions($line['url']);
            $params = parseURLParams($lineURL);

            $selected = ($subaction==NULL && 0==strcmp($params['action'],$action));
            if (!$selected) $selected = (0==strcmp($params['subaction'],$subaction) && 0==strcmp($params['action'],$action));
            $sel_index = -1;
            $lineselected = FALSE;
            for ($cnt=0; $cnt< count($grandchildren); $cnt++) {
               $glineURL = $template->doBasicSubstitutions($grandchildren[$cnt]['url']);
               $params = parseURLParams($glineURL);
               if ($adminmid!=NULL && 0==strcmp($params['adminmid'],$adminmid)) {
                  $lineselected = TRUE;
                  $selected = TRUE;
                  $sel_index = $cnt;
               } else if (!$lineselected) {
                  $lineselected = ($subaction==NULL && 0==strcmp($params['action'],$action));
                  if (!$lineselected) $lineselected = (0==strcmp($params['subaction'],$subaction) && 0==strcmp($params['action'],$action));
                  if ($lineselected) {
                     $selected = TRUE;
                     $sel_index = $cnt;
                  }
               }
            }

            if ($selected) {
?>
               <DIV class="whitebg">
               <DIV CLASS="adminhdrhover" onClick="window.location.href='<?php echo $lineURL; ?>';">
                  <?php echo $line['menuname']; ?>
               </Div>
<?php
            } else {
?>
               <DIV>
               <DIV CLASS="adminhdrlink" onClick="window.location.href='<?php echo $lineURL; ?>';" ONMOUSEOVER="this.className='adminhdrhover';" ONMOUSEOUT="this.className='adminhdrlink';">
                  <?php echo $line['menuname']; ?>
               </Div>
<?php
            }
            
            for ($j=0; $j<count($grandchildren); $j++) {
               $gline=$grandchildren[$j];
               $linedisplay = "&bull; ".$gline['menuname'];
               if ($j==$sel_index) $linedisplay = " <div class=\"arrow\">".$linedisplay."</div>";
               $glineURL = $template->doBasicSubstitutions($gline['url']);
?>
               <DIV CLASS="adminlink" onClick="window.location.href='<?php echo $glineURL; ?>';" ONMOUSEOVER="this.className='adminhover';" ONMOUSEOUT="this.className='adminlink';">
                  <?php echo $linedisplay; ?>
               </Div>
<?php
            }
            print "\n</div>\n";
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
  if (isset($vars['msg'])) print "<div class=\"msg\">".$vars['msg']."</div>";
  if (isset($vars['error'])) print "<div class=\"error\">".$vars['error']."</div>";
?>

         </div>
         </div>
      </div>
   </div>
</div>

</body>
</html>
