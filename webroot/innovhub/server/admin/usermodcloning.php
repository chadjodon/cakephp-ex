<?php
   //$_SESSION['showdebug'] = TRUE;

   $ctx = new Context();
   $ss = new Version();

   $hiddenfields = $ss->getValue("user_hide_fields");
   
   
   $tab = getParameter("tab");
   if ($tab==NULL) $tab="usersect";
   print "\n<!-- tab: ".$tab." -->\n";

   if ($GLOBALS['usermod_override']!=NULL) {
       include $GLOBALS['usermod_override'];
   } else {   
       $ua = new UserAcct;
       $userid = $vars['userid'];
       if ($userid==NULL) $userid=getParameter("userid");
       $user = $ua->getFullUserInfo($userid,FALSE,TRUE);
       //print "\n<!-- user: ";
       //print_r($user);
       //print " -->\n";
      $parentuser = NULL;
      if($user['parentid']!=NULL && $user['parentid']>0) {
         // This account has a parent
         $parentuser = $ua->getFullUserInfo($user['parentid']);
      }
       
       $displayusertype = strtoupper(substr($user['usertype'],0,1)).strtolower(substr($user['usertype'],1));
?>

<script src="/js/jsf_websitedata.js"></script>
<script src="/js/calendar.js"></script>
<script type="text/javascript">
      function expanduser() {
         document.getElementById('usersect').style.display = "";
         document.getElementById('properties2sect').style.display = "none";
         document.getElementById('propertiessect').style.display = "none";
         document.getElementById('objectssect').style.display = "none";
         document.getElementById('addlsect').style.display = "none";
         document.getElementById('adminsect').style.display = "none";
         document.getElementById('relsect').style.display = "none";
         document.getElementById('usertab').style.backgroundColor="#AAAAAA";
         document.getElementById('propertiestab').style.backgroundColor="#b8ceea";
         document.getElementById('properties2tab').style.backgroundColor="#b8ceea";
         document.getElementById('objectstab').style.backgroundColor="#b8ceea";
         document.getElementById('addltab').style.backgroundColor="#b8ceea";
         document.getElementById('admintab').style.backgroundColor="#b8ceea";
         document.getElementById('reltab').style.backgroundColor="#b8ceea";
      }

      function expandproperties() {
         document.getElementById('usersect').style.display = "none";
         document.getElementById('properties2sect').style.display = "none";
         document.getElementById('propertiessect').style.display = "";
         document.getElementById('objectssect').style.display = "none";
         document.getElementById('addlsect').style.display = "none";
         document.getElementById('adminsect').style.display = "none";
         document.getElementById('relsect').style.display = "none";
         document.getElementById('usertab').style.backgroundColor="#b8ceea";
         document.getElementById('propertiestab').style.backgroundColor="#AAAAAA";
         document.getElementById('properties2tab').style.backgroundColor="#b8ceea";
         document.getElementById('objectstab').style.backgroundColor="#b8ceea";
         document.getElementById('addltab').style.backgroundColor="#b8ceea";
         document.getElementById('admintab').style.backgroundColor="#b8ceea";
         document.getElementById('reltab').style.backgroundColor="#b8ceea";
      }

      function expandproperties2() {
         document.getElementById('usersect').style.display = "none";
         document.getElementById('properties2sect').style.display = "";
         document.getElementById('propertiessect').style.display = "none";
         document.getElementById('objectssect').style.display = "none";
         document.getElementById('addlsect').style.display = "none";
         document.getElementById('adminsect').style.display = "none";
         document.getElementById('relsect').style.display = "none";
         document.getElementById('usertab').style.backgroundColor="#b8ceea";
         document.getElementById('propertiestab').style.backgroundColor="#b8ceea";
         document.getElementById('properties2tab').style.backgroundColor="#AAAAAA";
         document.getElementById('objectstab').style.backgroundColor="#b8ceea";
         document.getElementById('addltab').style.backgroundColor="#b8ceea";
         document.getElementById('admintab').style.backgroundColor="#b8ceea";
         document.getElementById('reltab').style.backgroundColor="#b8ceea";
      }

      function expandobjects() {
         document.getElementById('usersect').style.display = "none";
         document.getElementById('properties2sect').style.display = "none";
         document.getElementById('propertiessect').style.display = "none";
         document.getElementById('objectssect').style.display = "";
         document.getElementById('addlsect').style.display = "none";
         document.getElementById('adminsect').style.display = "none";
         document.getElementById('relsect').style.display = "none";
         document.getElementById('usertab').style.backgroundColor="#b8ceea";
         document.getElementById('propertiestab').style.backgroundColor="#b8ceea";
         document.getElementById('properties2tab').style.backgroundColor="#b8ceea";
         document.getElementById('objectstab').style.backgroundColor="#AAAAAA";
         document.getElementById('addltab').style.backgroundColor="#b8ceea";
         document.getElementById('admintab').style.backgroundColor="#b8ceea";
         document.getElementById('reltab').style.backgroundColor="#b8ceea";
      }

      function expandaddl() {
         document.getElementById('usersect').style.display = "none";
         document.getElementById('properties2sect').style.display = "none";
         document.getElementById('propertiessect').style.display = "none";
         document.getElementById('objectssect').style.display = "none";
         document.getElementById('addlsect').style.display = "";
         document.getElementById('adminsect').style.display = "none";
         document.getElementById('relsect').style.display = "none";
         document.getElementById('usertab').style.backgroundColor="#b8ceea";
         document.getElementById('propertiestab').style.backgroundColor="#b8ceea";
         document.getElementById('properties2tab').style.backgroundColor="#b8ceea";
         document.getElementById('objectstab').style.backgroundColor="#b8ceea";
         document.getElementById('addltab').style.backgroundColor="#AAAAAA";
         document.getElementById('admintab').style.backgroundColor="#b8ceea";
         document.getElementById('reltab').style.backgroundColor="#b8ceea";
      }

      function expandadmin() {
         document.getElementById('usersect').style.display = "none";
         document.getElementById('properties2sect').style.display = "none";
         document.getElementById('propertiessect').style.display = "none";
         document.getElementById('objectssect').style.display = "none";
         document.getElementById('addlsect').style.display = "none";
         document.getElementById('adminsect').style.display = "";
         document.getElementById('relsect').style.display = "none";
         document.getElementById('usertab').style.backgroundColor="#b8ceea";
         document.getElementById('propertiestab').style.backgroundColor="#b8ceea";
         document.getElementById('properties2tab').style.backgroundColor="#b8ceea";
         document.getElementById('objectstab').style.backgroundColor="#b8ceea";
         document.getElementById('addltab').style.backgroundColor="#b8ceea";
         document.getElementById('admintab').style.backgroundColor="#AAAAAA";
         document.getElementById('reltab').style.backgroundColor="#b8ceea";
      }

      function expandrel() {
         document.getElementById('usersect').style.display = "none";
         document.getElementById('properties2sect').style.display = "none";
         document.getElementById('propertiessect').style.display = "none";
         document.getElementById('objectssect').style.display = "none";
         document.getElementById('addlsect').style.display = "none";
         document.getElementById('adminsect').style.display = "none";
         document.getElementById('relsect').style.display = "";
         document.getElementById('usertab').style.backgroundColor="#b8ceea";
         document.getElementById('propertiestab').style.backgroundColor="#b8ceea";
         document.getElementById('properties2tab').style.backgroundColor="#b8ceea";
         document.getElementById('objectstab').style.backgroundColor="#b8ceea";
         document.getElementById('addltab').style.backgroundColor="#b8ceea";
         document.getElementById('admintab').style.backgroundColor="#b8ceea";
         document.getElementById('reltab').style.backgroundColor="#AAAAAA";
      }

<?php
   $sumtab = getParameter("selectusermodtab");
   if ($sumtab!=NULL && (0==strcmp($sumtab,"user") || 0==strcmp($sumtab,"properties") || 0==strcmp($sumtab,"properties2") || 0==strcmp($sumtab,"objects") || 0==strcmp($sumtab,"addl") || 0==strcmp($sumtab,"admin") || 0==strcmp($sumtab,"rel"))) print "window.onload = function() {\nexpand".$sumtab."();\n}\n";
?>
 </script>


  <table cellpadding="10" cellspacing="0">
  <tr align="left" valign="top"><td>

  <table cellpadding="0" cellspacing="0" style="margin-top:10px;">
  <tr>
  <td style="background-color: #FFFFFF;"><div style="width:1px;height:1px;overflow:hidden;"></div></td>
  <td id="usertab" style="background-color:#AAAAAA;padding:5px 12px 5px 8px;border-left:1px solid #AAAAAA;border-top:1px solid #AAAAAA;border-top-left-radius:10px;"><div onclick="expanduser();" style="cursor:pointer;font-family:verdana;font-size:14px;color:#0d468d;font-weight:bold;"><?php echo $displayusertype; ?> info</div></td>
  <td style="background-color: #FFFFFF;"><div style="width:15px;height:1px;overflow:hidden;"></div></td>
  
  <!-- td nowrap="nowrap" id="propertiestab" style="background-color:#b8ceea;padding:5px 12px 5px 8px;border-left:1px solid #AAAAAA;border-top:1px solid #AAAAAA;border-top-left-radius:10px;"><div onclick="expandproperties();"  style="cursor:pointer;font-family:verdana;font-size:14px;color:#0d468d;font-weight:bold;"><?php echo $displayusertype; ?> Properties</div></td>
  <td style="background-color: #FFFFFF;"><div style="width:15px;height:1px;overflow:hidden;"></div></td -->
  <td id="propertiestab" style="display:none;"></td>
  
  <td nowrap="nowrap" id="properties2tab" style="background-color:#b8ceea;padding:5px 12px 5px 8px;border-left:1px solid #AAAAAA;border-top:1px solid #AAAAAA;border-top-left-radius:10px;"><div onclick="expandproperties2();"  style="cursor:pointer;font-family:verdana;font-size:14px;color:#0d468d;font-weight:bold;"><?php echo $displayusertype; ?> Properties</div></td>
  <td style="background-color: #FFFFFF;"><div style="width:15px;height:1px;overflow:hidden;"></div></td>
  
  <td nowrap="nowrap" id="objectstab" style="background-color:#b8ceea;padding:5px 12px 5px 8px;border-left:1px solid #AAAAAA;border-top:1px solid #AAAAAA;border-top-left-radius:10px;"><div onclick="expandobjects();" style="cursor:pointer;font-family:verdana;font-size:14px;color:#0d468d;font-weight:bold;">Tables</div></td>
  <td style="background-color: #FFFFFF;"><div style="width:15px;height:1px;overflow:hidden;"></div></td>
  
  <td nowrap="nowrap" id="addltab" style="background-color:#b8ceea;padding:5px 12px 5px 8px;border-left:1px solid #AAAAAA;border-top:1px solid #AAAAAA;border-top-left-radius:10px;"><div onclick="expandaddl();" style="cursor:pointer;font-family:verdana;font-size:14px;color:#0d468d;font-weight:bold;">Other</div></td>
  <td style="background-color: #FFFFFF;"><div style="width:15px;height:1px;overflow:hidden;"></div></td>
  <td nowrap="nowrap" id="admintab" style="background-color:#b8ceea;padding:5px 12px 5px 8px;border-left:1px solid #AAAAAA;border-top:1px solid #AAAAAA;border-top-left-radius:10px;"><div onclick="expandadmin();" style="cursor:pointer;font-family:verdana;font-size:14px;color:#0d468d;font-weight:bold;"><?php echo $displayusertype; ?> Admin</div></td>
  <td style="background-color: #FFFFFF;"><div style="width:15px;height:1px;overflow:hidden;"></div></td>
  <td nowrap="nowrap" id="reltab" style="background-color:#b8ceea;padding:5px 12px 5px 8px;border-left:1px solid #AAAAAA;border-top:1px solid #AAAAAA;border-top-left-radius:10px;"><div onclick="expandrel();" style="cursor:pointer;font-family:verdana;font-size:14px;color:#0d468d;font-weight:bold;"><?php echo $displayusertype; ?> Relationships</div></td>
  <td style="background-color: #FFFFFF;"><div style="width:15px;height:1px;overflow:hidden;"></div></td>
  
  </tr>
  </table>
  <table cellpadding="0" cellspacing="0">
  <tr><td style="background-color: #AAAAAA;"><img src="<?php echo $GLOBALS['baseURL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="800" height="2"></td></tr> 
  </table>
  <br>


  <div id="usersect" style="display:none;">
  <div style="padding:10px;font-size:14px;font-family:verdana;font-color:#333333;">
  <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
  <input type="hidden" name="action" value="modifyusercloning">
  <input type="hidden" name="userid" value="<?= $user['userid'] ?>">
  <input type="hidden" name="ulevel" value="<?= $user['ulevel'] ?>">

  Record <?php echo $user['userid']; ?> &nbsp; &nbsp; 
  (<?php echo $user['dbmode']; ?>) &nbsp; &nbsp; 
  Created <?php echo date("m/d/Y",strtotime($user['created'])); ?> &nbsp; &nbsp;
  <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=useroverride&userid=".$user['userid']; ?>">View Site as this user (this will log you out)</a><br>
  <div style="font-size:10px;max-height:12px;overflow:hidden;font-family:arial;color:#565656;margin-top:2px;margin-bottom:2px;">Last updated: <?php echo date("m/d/Y",strtotime($user['lastupdated'])); ?></div>
  <div style="font-size:10px;font-family:arial;color:#898989;margin-top:2px;margin-bottom:2px;">
  <div onclick="jQuery('#usermod_lub').show();" style="cursor:pointer;color:blue;">Show Last updated by</div>
  <div id="usermod_lub" style="display:none;">
  <?php 
     $lubarr1 = separateStringBy($user['lastupdatedby'],",",NULL,TRUE);
     //echo "<div style=\"font-size:14px;margin:10px;\">";
     //print_r($lubarr1);
     //echo "</div>";
     $lubarr2 = separateStringBy($user['lastupdateby'],",",NULL,TRUE);
     $lubarr = array_values(array_unique(array_merge($lubarr1,$lubarr2)));
     
     $newlub = array();
     for($i=0;$i<count($lubarr);$i++) {
        $temp = separateStringBy($lubarr[$i],"-",NULL,TRUE);
        //print "<br>Temp 1: ".$temp[1];
        if(strlen($temp[1]) == 8 && is_numeric($temp[1])) {
           //print "<br>found Temp 1: ".$temp[1];
           if(isset($newlub[$temp[1].$temp[0]])) {
              $newlub[$temp[1].$temp[0]]['text'] .= " ".$temp[2];
           } else {
              $obj = array();
              $obj['date'] = substr($temp[1],4,2)."/".substr($temp[1],6,2)."/".substr($temp[1],0,4);
              $obj['userid'] = $temp[0];
              $obj['text'] = $temp[2];
              $newlub[$temp[1].$temp[0]] = $obj;
           }
        } else {
           $temp = separateStringBy($lubarr[$i]," ",NULL,TRUE);
           if(strlen($temp[1]) == 10 && strlen($temp[2]) == 8) {
              $t = substr($temp[1],0,4).substr($temp[1],5,2).substr($temp[1],8,2).$temp[0];
              if(isset($newlub[$t])) {
                 $newlub[$t]['text'] .= " ".$temp[3];
              } else {
                 $obj = array();
                 $obj['date'] = substr($temp[1],5,2)."/".substr($temp[1],8,2)."/".substr($temp[1],0,4);
                 $obj['userid'] = $temp[0];
                 $obj['text'] = $temp[3];
                 $newlub[$t] = $obj;
              }
           }
           
        }
     }
     //echo "<div style=\"font-size:14px;margin:10px;\">";
     //print_r($newlub);
     //echo "</div>";
     krsort($newlub);
     
     echo "<table cellpadding=\"1\" cellspacing=\"3\">";
     echo "<tr style=\"color:#000000;font-weight:bold;background-color:#EDEDED;\"><td>Date</td><td>User ID</td><td>Extra</td></tr>";
     $bg = "#FFFFFF";
     foreach($newlub as $key=>$val) {
        echo "<tr style=\"color:#282828;background-color:".$bg.";\">";
        echo "<td>".$val['date']."</td>";
        echo "<td>".$val['userid']."</td>";
        echo "<td>".$val['text']."</td>";
        echo "</tr>";
        if(0!=strcmp($bg,"#FFFFFF")) $bg = "#FFFFFF";
        else $bg = "#AACCEE";
     }
     echo "</table>";
     
     echo "<div style=\"margin-top:10px;\">";
     if($user['lastupdatedby']!=NULL) echo "<br>User account: ".$user['lastupdatedby'];
     if($user['lastupdateby']!=NULL) echo "<br>Properties: ".$user['lastupdateby'];
     if($user['lastupdateby2']!=NULL) echo "<br>Properties overflow: ".$user['lastupdateby2'];
     //else echo $user['q26'];
     echo "</div>";
  ?>
  </div>
  </div>
  
  <div style="font-size:10px;max-height:12px;overflow:hidden;font-family:arial;color:#aaaaaa;margin-top:2px;margin-bottom:2px;">
  <?php if ($user['activatedstr']!=NULL) echo "[".$user['activatedstr']."]"; ?>
  </div>
  
   <!--div style="clear:both;height:15px;width:1px;overflow:hidden;"></div-->

   <!-- First section - user general settings -->
   <div style="border:1px solid #444444;border-radius:5px;padding:8px;background-color:#DEDEDE;margin-right:15px;margin-top:12px;margin-bottom:15px;float:left;">

      <div style="clear:both;margin-top:3px;height:20px;">
      <div style="float:left;margin-right:5px;width:160px;">Email</div>
      <div style="float:left;margin-right:20px;width:250px;"><input style="font-size:12px;font-family:verdana;width:240px;color:#222222;" type="text" name="email" value="<?php echo $user['email'] ?>"></div>
      </div>

     <?php if($GLOBALS['usertypeview'] && 0==strcmp($user['usertype'],"org")) { ?> 
        <input type="hidden" name="fname" value="<?= $user['fname'] ?>">
        <input type="hidden" name="title" value="<?= $user['title'] ?>">
        <input type="hidden" name="gender" value="<?= $user['gender'] ?>">
         <!--div style="clear:both;margin-top:3px;height:20px;">
         <div style="float:left;margin-right:5px;width:160px;">Parent name</div>
         <div style="float:left;margin-right:20px;width:250px;"><input style="font-size:12px;font-family:verdana;width:240px;color:#222222;" type="text" name="lname" value="<?php echo $user['lname'] ?>"></div>
         </div-->
         <div style="clear:both;margin-top:3px;height:20px;">
         <div style="float:left;margin-right:5px;width:160px;">Former name</div>
         <div style="float:left;margin-right:20px;width:250px;"><input style="font-size:12px;font-family:verdana;width:240px;color:#222222;" type="text" name="username" value="<?php echo $user['username'] ?>"></div>
         </div>
         <?php
            if($parentuser!=NULL && $parentuser['username']!=NULL) {
               print "<div style=\"clear:both;\"></div>";
               print "<div style=\"font-size:8px;color:#999999;margin-bottom:5px;\">";
               print "Inherited: <span style=\"font-weight:bold;\">".$parentuser['username']."</span>";
               print "</div>";
            }
         ?>
     <?php } else { ?>
         <div style="clear:both;margin-top:3px;height:20px;">
         <div style="float:left;margin-right:5px;width:160px;">First name</div>
         <div style="float:left;margin-right:20px;width:250px;"><input style="font-size:12px;font-family:verdana;width:240px;color:#222222;" type="text" name="fname" value="<?php echo $user['fname'] ?>"></div>
         </div>
         <?php
            if($parentuser!=NULL && $parentuser['fname']!=NULL) {
               print "<div style=\"clear:both;\"></div>";
               print "<div style=\"font-size:8px;color:#999999;margin-bottom:5px;\">";
               print "Inherited: <span style=\"font-weight:bold;\">".$parentuser['fname']."</span>";
               print "</div>";
            }
         ?>
         
         <div style="clear:both;margin-top:3px;height:20px;">
         <div style="float:left;margin-right:5px;width:160px;">Last name</div>
         <div style="float:left;margin-right:20px;width:250px;"><input style="font-size:12px;font-family:verdana;width:240px;color:#222222;" type="text" name="lname" value="<?php echo $user['lname'] ?>"></div>
         </div>
         <?php
            if($parentuser!=NULL && $parentuser['lname']!=NULL) {
               print "<div style=\"clear:both;\"></div>";
               print "<div style=\"font-size:8px;color:#999999;margin-bottom:5px;\">";
               print "Inherited: <span style=\"font-weight:bold;\">".$parentuser['lname']."</span>";
               print "</div>";
            }
         ?>

         <?php if (strpos($hiddenfields,"username")===FALSE) { ?>
         <div style="clear:both;margin-top:3px;height:20px;">
         <div style="float:left;margin-right:5px;width:160px;">User name</div>
         <div style="float:left;margin-right:20px;width:250px;"><input style="font-size:12px;font-family:verdana;width:240px;color:#222222;" type="text" name="username" value="<?php echo $user['username'] ?>"></div>
         </div>
         <?php
            if($parentuser!=NULL && $parentuser['username']!=NULL) {
               print "<div style=\"clear:both;\"></div>";
               print "<div style=\"font-size:8px;color:#999999;margin-bottom:5px;\">";
               print "Inherited: <span style=\"font-weight:bold;\">".$parentuser['username']."</span>";
               print "</div>";
            }
         ?>
         <?php } ?>

         <div style="clear:both;margin-top:3px;height:20px;">
         <div style="float:left;margin-right:5px;width:160px;">Title</div>
         <div style="float:left;margin-right:20px;width:250px;"><input style="font-size:12px;font-family:verdana;width:240px;color:#222222;" type="text" name="title" value="<?php echo $user['title'] ?>"></div>
         </div>
         <?php
            if($parentuser!=NULL && $parentuser['title']!=NULL) {
               print "<div style=\"clear:both;\"></div>";
               print "<div style=\"font-size:8px;color:#999999;margin-bottom:5px;\">";
               print "Inherited: <span style=\"font-weight:bold;\">".$parentuser['title']."</span>";
               print "</div>";
            }
         ?>
         <div style="clear:both;margin-top:3px;height:20px;">
         <div style="float:left;margin-right:5px;width:160px;">Gender</div>
         <div style="float:left;margin-right:20px;width:250px;">
            <input type="radio" name="gender" value="M" <?php if (0==strcmp($user['gender'],"M")) echo "CHECKED"; ?>>Male 
            &nbsp; &nbsp
            <input type="radio" name="gender" value="F" <?php if (0==strcmp($user['gender'],"F")) echo "CHECKED"; ?>>Female
         </div>
         </div>
     <?php } ?>
      <div style="clear:both;margin-top:3px;height:20px;">
      <div style="float:left;margin-right:5px;width:160px;">Company name</div>
      <div style="float:left;margin-right:20px;width:250px;"><input style="font-size:12px;font-family:verdana;width:240px;color:#222222;" type="text" name="company" value="<?php echo $user['company'] ?>"></div>
      </div>
         <?php
            if($parentuser!=NULL && $parentuser['company']!=NULL) {
               print "<div style=\"clear:both;\"></div>";
               print "<div style=\"font-size:8px;color:#999999;margin-bottom:5px;\">";
               print "Inherited: <span style=\"font-weight:bold;\">".$parentuser['company']."</span>";
               print "</div>";
            }
         ?>
      <div style="clear:both;margin-top:3px;height:20px;">
      <div style="float:left;margin-right:5px;width:160px;">User type</div>
      <div style="float:left;margin-right:20px;width:250px;"><?php echo getRadioBtnList("usertype", $ua->getUserTypes(), $user['usertype']); ?></div>
      </div>

      <?php if (strpos($hiddenfields,"alive")===FALSE) { ?>
      <div style="clear:both;margin-top:3px;height:20px;">
      <div style="float:left;margin-right:5px;width:160px;">Account</div>
      <div style="float:left;margin-right:20px;width:250px;">
         <input type="radio" name="alive" value="0" <?php if (0==$user['alive']) echo "CHECKED"; ?>>Dormant 
         &nbsp; &nbsp 
         <input type="radio" name="alive" value="1" <?php if (1==$user['alive']) echo "CHECKED"; ?>>Active
      </div>
      </div>
      <?php } ?>

      <?php if (strpos($hiddenfields,"activated")===FALSE) { ?>
      <div style="clear:both;margin-top:5px;margin-bottom:5px;height:20px;">
      <div style="float:left;margin-right:5px;width:160px;">Status</div>
      <div style="float:left;margin-right:20px;width:250px;">
         <?php if (1==$user['activated']) { ?>
            This account is currently active.  
            <div style="border:1px solid #000000;background-color:#BBBBBB;color:#000000;font-size:12px;font-family:arial;padding:4px;border-radius:3px;cursor:pointer;text-align:center;width:140px;margin:5px;" onclick="window.open('<?php echo getBaseURL(); ?>jsfadmin/usermodcloning_activated.php?userid=<?php echo $user['userid']; ?>', 'newwindow', 'width=300, height=250');">
            Deactivate
            </div>
         <?php } else { ?>
            This account is currently inactive<?php if ($user['activatedstr']!=NULL) echo " (".$user['activatedstr'].")"; ?>.
            <div style="border:1px solid #000000;background-color:#BBBBBB;color:#000000;font-size:12px;font-family:arial;padding:4px;border-radius:3px;cursor:pointer;text-align:center;width:140px;margin:5px;" onclick="window.open('<?php echo getBaseURL(); ?>jsfadmin/usermodcloning_activated.php?userid=<?php echo $user['userid']; ?>', 'newwindow', 'width=300, height=250');">
            Activate
            </div>
         <?php } ?>
      </div>
      </div>

      <?php if ($ss->getValue("RequireActivation")==1 && $ua->userProfileExists($user['email'])) { ?>
      <div style="clear:both;margin-top:3px;height:20px;">
         <div style="margin-right:5px;width:400px;">
         <?php if ($user['activated']==1) { ?>
               This user has activated their account.  <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=deactivateusercloning&userid=".$user['userid']; ?>">Deactivate</a>
         <?php } else { ?>
               This user has not yet activated their account.  <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=activateusercloning&userid=".$user['userid']; ?>">Activate</a>
         <?php } ?>
         </div>
      </div>
     <?php } ?>
     <?php } ?>

     
      <div style="clear:both;margin-top:3px;height:100px;">
      <div style="float:left;margin-right:5px;width:160px;">Notes</div>
      <div style="float:left;margin-right:20px;width:250px;">
         <textarea name="notes" style="width:235px;height:90px;font-size:12px;font-family:verdana;color:#333333;"><?php echo $user['notes']; ?></textarea>
      </div>
      </div>
         <?php
            if($parentuser!=NULL && $parentuser['notes']!=NULL) {
               print "<div style=\"clear:both;\"></div>";
               print "<div style=\"font-size:8px;color:#999999;margin-bottom:5px;\">";
               print "Inherited Notes: ".$parentuser['notes'];
               print "</div>";
            }
         ?>

      <?php if(strpos(strtolower($user['email']),"dummy")===FALSE) { ?>
      <div style="clear:both;margin-top:3px;">
      <div style="margin-right:20px;width:300px;">
         <input type="checkbox" name="emailflag" value="2" <?php if($user['emailflag']==2) echo "CHECKED"; ?> >
         Do not send any promotional email
      </div>
      </div>
      <?php } ?>

   </div>


   <!-- Section 2 - address settings -->
   <div style="border:1px solid #444444;border-radius:5px;padding:8px;background-color:#DEDEDE;margin-right:15px;margin-bottom:15px;float:left;">
      <div style="clear:both;margin-top:3px;height:20px;">
      <div style="float:left;margin-right:5px;width:160px;">Address1</div>
      <div style="float:left;margin-right:20px;width:250px;"><input style="font-size:12px;font-family:verdana;width:240px;color:#222222;" type="text" name="addr1" value="<?php echo $user['addr1'] ?>"></div>
      </div>
         <?php
            if($parentuser!=NULL && $parentuser['addr1']!=NULL) {
               print "<div style=\"clear:both;\"></div>";
               print "<div style=\"font-size:8px;color:#999999;margin-bottom:5px;\">";
               print "Inherited: <span style=\"font-weight:bold;\">".$parentuser['addr1']." ".$parentuser['addr2']."</span>";
               print "</div>";
            }
         ?>
      <div style="clear:both;margin-top:3px;height:20px;">
      <div style="float:left;margin-right:5px;width:160px;">Address2</div>
      <div style="float:left;margin-right:20px;width:250px;"><input style="font-size:12px;font-family:verdana;width:240px;color:#222222;" type="text" name="addr2" value="<?php echo $user['addr2'] ?>"></div>
      </div>
      <div style="clear:both;margin-top:3px;height:20px;">
      <div style="float:left;margin-right:5px;width:160px;">City</div>
      <div style="float:left;margin-right:20px;width:250px;"><input style="font-size:12px;font-family:verdana;width:240px;color:#222222;" type="text" name="city" value="<?php echo $user['city'] ?>"></div>
      </div>
         <?php
            if($parentuser!=NULL && $parentuser['city']!=NULL) {
               print "<div style=\"clear:both;\"></div>";
               print "<div style=\"font-size:8px;color:#999999;margin-bottom:5px;\">";
               print "Inherited: <span style=\"font-weight:bold;\">".$parentuser['city']."</span>";
               print "</div>";
            }
         ?>
      <div style="clear:both;margin-top:3px;height:20px;">
      <div style="float:left;margin-right:5px;width:160px;">State</div>
      <div style="float:left;margin-right:20px;width:250px;">
         <?php echo getStateOptions($user['state'],"state",TRUE) ?>
         Zip Code<input type="text" name="zip" size="10" value="<?php echo $user['zip'] ?>">
      </div>
      </div>
         <?php
            if($parentuser!=NULL && $parentuser['state']!=NULL) {
               print "<div style=\"clear:both;\"></div>";
               print "<div style=\"font-size:8px;color:#999999;margin-bottom:5px;\">";
               print "Inherited: <span style=\"font-weight:bold;\">".$parentuser['state']."</span>";
               print "</div>";
            }
         ?>
      <div style="clear:both;margin-top:3px;height:20px;">
      <div style="float:left;margin-right:5px;width:160px;">Country</div>
      <div style="float:left;margin-right:20px;width:250px;">
         <?php echo listCountries($user['country'],"country",TRUE); ?>
      </div>
      </div>
         <?php
            if($parentuser!=NULL && $parentuser['country']!=NULL) {
               print "<div style=\"clear:both;\"></div>";
               print "<div style=\"font-size:8px;color:#999999;margin-bottom:5px;\">";
               print "Inherited: <span style=\"font-weight:bold;\">".$parentuser['country']."</span>";
               print "</div>";
            }
         ?>
      <div style="clear:both;margin-top:3px;height:20px;">
      <div style="float:left;margin-right:5px;width:160px;">Location</div>
      <div style="float:left;margin-right:20px;width:250px;">
      <span style="width:18px;height:18px;padding:3px;border:1px solid #333333;border-radius:3px;background-color:#FDFDFD;font-size:10px;cursor:pointer;margin-right:5px;" onclick="if(confirm('Are you sure you want to recalculate this address coordiantes?')) location.href='admincontroller.php?action=usermodcloning&refreshgeo=1&userid=<?php echo $user['userid']; ?>';">Re</span>
      <span style="width:18px;height:18px;padding:3px;border:1px solid #333333;border-radius:3px;background-color:#FDFDFD;font-size:10px;cursor:pointer;margin-right:5px;" onclick="jQuery('#usermodcloning_manualgeo').show();">Mn</span>
      <?php
         echo $user['lat'].",".$user['lng'];
      ?>
      
      <div id="usermodcloning_manualgeo" style="display:none;">
      <table cellpadding="3" cellspacing="2">
      <tr><td>Latitude:</td><td><input type="text" value="<?php echo $user['lat']; ?>" id="usermodcloning_manuallat" style="font-size:10px;width:80px;"></td></tr>
      <tr><td>Longitude:</td><td><input type="text" value="<?php echo $user['lng']; ?>" id="usermodcloning_manuallng" style="font-size:10px;width:80px;"></td></tr>
      <tr><td colspan="2"><div onclick="location.href='/jsfadmin/admincontroller.php?action=usermodcloning&subaction=manualgeo&userid=<?php echo $user['userid']; ?>&lat=' + jQuery('#usermodcloning_manuallat').val() + '&lng=' + jQuery('#usermodcloning_manuallng').val();" style="margin:5px;padding:5px;border:1px solid #333333;border-radius:3px;font-size:10px;background-color:#FDFDFD;color:black;width:60px;text-align:center;cursor:pointer;">Update</div></td></tr>
      </table>
      </div>
      
      </div>
      </div>
      <div style="clear:both;margin-top:3px;height:20px;">
      <div style="float:left;margin-right:5px;width:160px;">Website</div>
      <div style="float:left;margin-right:20px;width:250px;"><input style="font-size:12px;font-family:verdana;width:240px;color:#222222;" type="text" name="website" value="<?php echo $user['website'] ?>"></div>
      </div>
         <?php
            if($parentuser!=NULL && $parentuser['website']!=NULL) {
               print "<div style=\"clear:both;\"></div>";
               print "<div style=\"font-size:8px;color:#999999;margin-bottom:5px;\">";
               print "Inherited: <span style=\"font-weight:bold;\">".$parentuser['website']."</span>";
               print "</div>";
            }
         ?>
      <div style="clear:both;margin-top:3px;height:20px;">
      <div style="float:left;margin-right:5px;width:160px;">Phone</div>
      <div style="float:left;margin-right:20px;width:250px;"><input style="font-size:12px;font-family:verdana;width:240px;color:#222222;" type="text" name="phonenum" value="<?php echo $user['phonenum'] ?>"></div>
      </div>
         <?php
            if($parentuser!=NULL && $parentuser['phonenum']!=NULL) {
               print "<div style=\"clear:both;\"></div>";
               print "<div style=\"font-size:8px;color:#999999;margin-bottom:5px;\">";
               print "Inherited: <span style=\"font-weight:bold;\">".$parentuser['phonenum']."</span>";
               print "</div>";
            }
         ?>
      <div style="clear:both;margin-top:3px;height:20px;">
      <div style="float:left;margin-right:5px;width:160px;">Fax</div>
      <div style="float:left;margin-right:20px;width:250px;"><input style="font-size:12px;font-family:verdana;width:240px;color:#222222;" type="text" name="phonenum2" value="<?php echo $user['phonenum2'] ?>"></div>
      </div>
         <?php
            if($parentuser!=NULL && $parentuser['phonenum2']!=NULL) {
               print "<div style=\"clear:both;\"></div>";
               print "<div style=\"font-size:8px;color:#999999;margin-bottom:5px;\">";
               print "Inherited: <span style=\"font-weight:bold;\">".$parentuser['phonenum2']."</span>";
               print "</div>";
            }
         ?>
      <div style="clear:both;margin-top:3px;height:20px;">
      <div style="float:left;margin-right:5px;width:160px;">Alternate</div>
      <div style="float:left;margin-right:20px;width:250px;"><input style="font-size:12px;font-family:verdana;width:240px;color:#222222;" type="text" name="phonenum3" value="<?php echo $user['phonenum3'] ?>"></div>
      </div>
         <?php
            if($parentuser!=NULL && $parentuser['phonenum3']!=NULL) {
               print "<div style=\"clear:both;\"></div>";
               print "<div style=\"font-size:8px;color:#999999;margin-bottom:5px;\">";
               print "Inherited: <span style=\"font-weight:bold;\">".$parentuser['phonenum3']."</span>";
               print "</div>";
            }
         ?>
      <div style="clear:both;margin-top:3px;height:20px;">
      <div style="float:left;margin-right:5px;width:160px;">Phone 4</div>
      <div style="float:left;margin-right:20px;width:250px;"><input style="font-size:12px;font-family:verdana;width:240px;color:#222222;" type="text" name="phonenum4" value="<?php echo $user['phonenum4'] ?>"></div>
      </div>
         <?php
            if($parentuser!=NULL && $parentuser['phonenum4']!=NULL) {
               print "<div style=\"clear:both;\"></div>";
               print "<div style=\"font-size:8px;color:#999999;margin-bottom:5px;\">";
               print "Inherited: <span style=\"font-weight:bold;\">".$parentuser['phonenum4']."</span>";
               print "</div>";
            }
         ?>
         
      <div style="clear:both;margin-top:3px;height:20px;">
      <div style="float:left;margin-right:5px;width:160px;">Last Verified</div>
      <div style="float:left;margin-right:20px;width:250px;">
         <span style="width:18px;height:18px;padding:3px;border:1px solid #333333;border-radius:3px;background-color:#FDFDFD;font-size:10px;cursor:pointer;" onclick="if(confirm('Are you sure you want to verify this record?')) location.href='admincontroller.php?action=usermodcloning&verifyuser=1&userid=<?php echo $user['userid']; ?>';">Re</span>
         <?php 
            if($user['lastverified']!=NULL && 0!=strcmp(substr($user['lastverified'],0,4),"0000")) echo date("m/d/Y",strtotime($user['lastverified']));
            else echo "N/A";
         ?>
      </div>
      </div>
   </div>
   
   <div style="clear:both;"></div>

   <!-- Section: miscellaneous -->
   <div style="border:1px solid #444444;border-radius:5px;padding:8px;background-color:#DEDEDE;margin-right:15px;margin-bottom:15px;float:left;">
      <div style="clear:both;margin-top:3px;height:20px;">
      <div style="float:left;margin-right:5px;width:160px;">Source</div>
      <div style="float:left;margin-right:20px;width:300px;"><?php echo $user['refsrc'] ?></div>
      </div>
      
      
      <!-- div style="clear:both;margin-top:3px;height:20px;">
      <div style="float:left;margin-right:5px;width:160px;">Parent</div>
      <div style="float:left;margin-right:20px;width:300px;"><input style="font-size:12px;font-family:verdana;width:240px;color:#222222;" type="text" name="parentid" value="<?php echo $user['parentid'] ?>"></div>
      </div>
      <div style="clear:both;margin-top:3px;height:20px;">
      <div style="float:left;margin-right:5px;width:160px;">Parent 2</div>
      <div style="float:left;margin-right:20px;width:300px;"><input style="font-size:12px;font-family:verdana;width:240px;color:#222222;" type="text" name="parentid2" value="<?php echo $user['parentid2'] ?>"></div>
      </div -->

   <?php
         $opts = $ctx->getSiteOptions(-1, 0, NULL, TRUE);
         $cityOptions = getOptionList("siteid", $opts,$user['siteid'],TRUE);
         $field1Lbl = $ss->getValue("user_field1_label");
         $field1Def = $ss->getValue("user_field1_default");
         $field2Lbl = $ss->getValue("user_field2_label");
         $field3Lbl = $ss->getValue("user_field3_label");
         $field4Lbl = $ss->getValue("user_field4_label");
         $field5Lbl = $ss->getValue("user_field5_label");
         $field6Lbl = $ss->getValue("user_field6_label");
         $field6Ops = array();
         for ($i=1; $i<=10; $i++) $field6Ops[$i] = $i;
   ?>
      <div style="clear:both;margin-top:3px;height:20px;">
      <div style="float:left;margin-right:5px;width:160px;">Site</div>
      <div style="float:left;margin-right:20px;width:300px;"><?php echo $cityOptions; ?></div>
      </div>


     <?php if ($field1Lbl!=NULL) { ?>
      <?php
         if ($user['field1']==NULL &&  $field1Def!=NULL) 
            $user['field1']=$field1Def;
      ?>
      <div style="clear:both;margin-top:3px;height:20px;">
      <div style="float:left;margin-right:5px;width:160px;"><?php echo $field1Lbl; ?></div>
      <div style="float:left;margin-right:20px;width:300px;"><input style="font-size:12px;font-family:verdana;width:240px;color:#222222;" type="text" name="field1" value="<?php echo $user['field1'] ?>"></div>
      </div>
         <?php
            if($parentuser!=NULL && $parentuser['field1']!=NULL) {
               print "<div style=\"clear:both;\"></div>";
               print "<div style=\"font-size:8px;color:#999999;margin-bottom:5px;\">";
               print "Inherited: <span style=\"font-weight:bold;\">".$parentuser['field1']."</span>";
               print "</div>";
            }
         ?>
     <?php } ?>
     <?php if ($field2Lbl!=NULL) { ?>
      <div style="clear:both;margin-top:3px;height:20px;">
      <div style="float:left;margin-right:5px;width:160px;"><?php echo $field2Lbl; ?></div>
      <div style="float:left;margin-right:20px;width:300px;"><input style="font-size:12px;font-family:verdana;width:240px;color:#222222;" type="text" name="field2" value="<?php echo $user['field2'] ?>"></div>
      </div>
         <?php
            if($parentuser!=NULL && $parentuser['field2']!=NULL) {
               print "<div style=\"clear:both;\"></div>";
               print "<div style=\"font-size:8px;color:#999999;margin-bottom:5px;\">";
               print "Inherited: <span style=\"font-weight:bold;\">".$parentuser['field2']."</span>";
               print "</div>";
            }
         ?>
     <?php } ?>
     <?php if ($field3Lbl!=NULL) { ?>
      <div style="clear:both;margin-top:3px;height:20px;">
      <div style="float:left;margin-right:5px;width:160px;"><?php echo $field3Lbl; ?></div>
      <div style="float:left;margin-right:20px;width:300px;"><input style="font-size:12px;font-family:verdana;width:240px;color:#222222;" type="text" name="field3" value="<?php echo $user['field3'] ?>"></div>
      </div>
         <?php
            if($parentuser!=NULL && $parentuser['field3']!=NULL) {
               print "<div style=\"clear:both;\"></div>";
               print "<div style=\"font-size:8px;color:#999999;margin-bottom:5px;\">";
               print "Inherited: <span style=\"font-weight:bold;\">".$parentuser['field3']."</span>";
               print "</div>";
            }
         ?>
     <?php } ?>
     <?php if ($field4Lbl!=NULL) { ?>
      <div style="clear:both;margin-top:3px;height:20px;">
      <div style="float:left;margin-right:5px;width:160px;"><?php echo $field4Lbl; ?></div>
      <div style="float:left;margin-right:20px;width:300px;"><input style="font-size:12px;font-family:verdana;width:240px;color:#222222;" type="text" name="field4" value="<?php echo $user['field4'] ?>"></div>
      </div>
         <?php
            if($parentuser!=NULL && $parentuser['field4']!=NULL) {
               print "<div style=\"clear:both;\"></div>";
               print "<div style=\"font-size:8px;color:#999999;margin-bottom:5px;\">";
               print "Inherited: <span style=\"font-weight:bold;\">".$parentuser['field4']."</span>";
               print "</div>";
            }
         ?>
      <?php } ?>
      <?php 
         if ($field5Lbl!=NULL) {
            $field5Input = "<input type=\"text\" name=\"field5\" size=\"15\" value=\"".$user['field5']."\">";
            if (strpos(strtolower($field5Lbl),"user")!==FALSE) {
               $allUsers = $ua->getAdminUsers();
               $userOpts = NULL;
               for ($i=0; $i<count($allUsers); $i++) {
                  $userOpts[$allUsers[$i]['email']] = $allUsers[$i]['userid'];
               }
               $field5Input = getOptionList("field5", $userOpts, $user['field5'], TRUE);
            }
      ?>
         <div style="clear:both;margin-top:3px;height:20px;">
         <div style="float:left;margin-right:5px;width:160px;"><?php echo $field5Lbl; ?></div>
         <div style="float:left;margin-right:20px;width:300px;"><?php echo $field5Input; ?></div>
         </div>
         <?php
            if($parentuser!=NULL && $parentuser['field5']!=NULL) {
               print "<div style=\"clear:both;\"></div>";
               print "<div style=\"font-size:8px;color:#999999;margin-bottom:5px;\">";
               print "Inherited: <span style=\"font-weight:bold;\">".$parentuser['field5']."</span>";
               print "</div>";
            }
         ?>
      <?php } ?>

     <?php if ($field6Lbl!=NULL) { ?>
         <div style="clear:both;margin-top:3px;height:20px;">
         <div style="float:left;margin-right:5px;width:160px;"><?php echo $field6Lbl; ?></div>
         <div style="float:left;margin-right:20px;width:300px;"><?php echo getOptionList("field6", $field6Ops, $user['field6'], TRUE); ?></div>
         </div>
         <?php
            if($parentuser!=NULL && $parentuser['field6']!=NULL) {
               print "<div style=\"clear:both;\"></div>";
               print "<div style=\"font-size:8px;color:#999999;margin-bottom:5px;\">";
               print "Inherited: <span style=\"font-weight:bold;\">".$parentuser['field6']."</span>";
               print "</div>";
            }
         ?>
     <?php } ?>
   </div>
   
   
   
   <!-- Section: parent section -->
   <div style="border:1px solid #444444;border-radius:5px;padding:8px;background-color:#DEDEDE;margin-right:15px;margin-bottom:15px;float:left;">
      <?php
         if($parentuser!=NULL) {
            // This account has a parent
      ?>
         <div style="font-size:12px;color:#333333;font-weight:bold;">Parent <?php echo $parentuser['usertype']; ?>:</div>
         <div style="font-size:14px;color:#555555;cursor:pointer;" onclick="window.open('<?php echo getBaseURL(); ?>jsfadmin/admincontroller.php?action=usermodcloning&userid=<?php echo $parentuser['userid']; ?>');">
         <?php echo $parentuser['fname']." ".$parentuser['lname']." ".$parentuser['company']; ?>
         </div>
         <div style="font-size:12px;color:#555555;cursor:pointer;" onclick="window.open('<?php echo getBaseURL(); ?>jsfadmin/admincontroller.php?action=usermodcloning&userid=<?php echo $parentuser['userid']; ?>');">
         <?php echo $parentuser['addr1']." ".$parentuser['addr2']." ".$parentuser['city']." ".$parentuser['state']." ".$parentuser['zip']." ".$parentuser['country']; ?>
         <?php echo $parentuser['phonenum']." ".$parentuser['website']; ?>
         </div>
         <div onclick="if(confirm('Are you sure you want to permanently remove this parent record?')) location.href='<?php echo getBaseURL(); ?>jsfadmin/admincontroller.php?action=usermodcloning&userid=<?php echo $userid; ?>&parentid=<?php echo $parentuser['userid']; ?>&removeparentid=1';" style="font-size:8px;color:blue;margin-top:2px;cursor:pointer;">Remove Parent</div>
         
         <div onclick="window.open('/jsfadmin/admincontroller.php?action=listuserscloning&s_parentid=<?php echo $user['parentid']; ?>');" style="margin-top:10px;margin-bottom:8px;color:blue;cursor:pointer;">
         List all the sibling accounts
         </div>

         
      <?php
         } else if($user['parentid']== -1001){
      ?>
         <div style="clear:both;margin-top:3px;height:20px;font-weight:bold;">
         This is a parent account
         <span onclick="if(confirm('Are you sure you would like to make this a non-parent account?')) location.href='/jsfadmin/admincontroller.php?action=usermodcloning&userid=<?php echo $user['userid']; ?>&addparentid=1&parentid=0';" style="margin-left:15px;font-size:8px;color:blue;cursor:pointer;">
         remove
         </span>
         </div>
         <div onclick="window.open('/jsfadmin/admincontroller.php?action=listuserscloning&s_parentid=<?php echo $user['userid']; ?>');" style="margin-top:5px;margin-bottom:8px;color:blue;cursor:pointer;">
         List all the children of this account
         </div>
         <div style="margin-top:5px;">
         <input type="text" id="usermod_addchildren" name="usermod_addchildren" style="font-size:10px;font-family:arial;width:150px;">
         <span onclick="location.href='/jsfadmin/admincontroller.php?action=usermodcloning&userid=<?php echo $user['userid']; ?>&subaction=addchildren&userids=' + jQuery('#usermod_addchildren').val();" style="margin-left:10px;font-size:8px;color:#222222;cursor:pointer;padding:5px;border:1px solid #222222;border-radius:4px;background-color:#CCCCCC;">
         add children
         </span>
         </div>
      <?php
         } else {
      ?>
      
         <div onclick="if(confirm('Are you sure you would like to make this a parent account?')) location.href='/jsfadmin/admincontroller.php?action=usermodcloning&userid=<?php echo $user['userid']; ?>&addparentid=1&parentid=-1001';" style="clear:both;margin:5px;font-size:10px;color:blue;cursor:pointer;">
         Make this a parent account
         </div>
         <div style="clear:both;margin-top:3px;height:20px;font-weight:bold;">
         Or add a parent to this account
         </div>
         <div style="clear:both;margin-top:3px;height:20px;">
            <input id="usermod_searchtxt" style="font-size:12px;font-family:verdana;width:240px;color:#222222;" type="text" name="searchtxt" value="">
            <span onclick="usermod_searchforaccount();" style="margin-left:15px;margin-top:4px;padding:5px;font-size:10px;border:1px solid #000000;border-radius:4px;background-color:#BBBBBB;cursor:pointer;">Search</span>
         </div>
         <div style="margin-top:10px;height:20px;" id="usermod_addparentid">
         </div>
         <script>
           function usermod_searchforaccount() {
              jQuery('#usermod_addparentid').html('<img src=\"/jsfimages/loading.gif\">');
              var searchtxt = jQuery('#usermod_searchtxt').val();
              var searchurl = '<?php echo getBaseURL().$GLOBALS['codeFolder']; ?>jsoncontroller.php?action=searchusers';
              searchurl += '&s_filter=' + encodeURIComponent(searchtxt);
              searchurl += '&s_parentid=ignore';
              searchurl += '&limit=50';
              //alert('url: ' + searchurl);
              jsf_json_sendRequest(searchurl,usermod_displayuseroptions);
           }
           
           
           function usermod_displayuseroptions(jsondata){
              //alert('return from url');
              var str = '';
              
              if(Boolean(jsondata.users)) {
                 str += '<select id=\"usermod_parentid_opt\">';
                 str += '<option value=\"\"></option>';
                 for(var i=0;i<jsondata.users.length;i++) {
                    str += '<option value=\"' + jsondata.users[i].userid + '\">';
                    str += jsondata.users[i].userid + '. ' + jsondata.users[i].fname + ' ' + jsondata.users[i].lname + ' ' + jsondata.users[i].company;
                    str += '</option>';
                 }
                 str += '</select>';
                 str += '<span ';
                 str += 'style=\"margin-left:5px;padding:5px;border:1px solid #000000;border-radius:4px;font-size:10px;background-color:#AAAAAA;color:#222222;font-family:arial;cursor:pointer;\" ';
                 str += 'onclick=\"location.href=\'<?php echo getBaseURL(); ?>jsfadmin/admincontroller.php?action=usermodcloning&userid=<?php echo $user['userid']; ?>&addparentid=1&parentid=\' + jQuery(\'#usermod_parentid_opt\').val();\" ';
                 str += '>Add Parent</span>';
              }
              
              jQuery('#usermod_addparentid').html(str);
           }
           
         </script>
      <?php
         }
      ?>
   </div>   
   
   
   

  <div style="clear:both;height:5px;width:1px;overflow:hidden;"></div>

   <?php if (0!=strcmp($user['dbmode'],"REJECTED") && 0!=strcmp($user['dbmode'],"DELETED")) { ?>
      <!-- BR><input type="submit" name="submit" value="Save <?php echo $displayusertype; ?> Info" -->
      <BR><input type="submit" name="submit" value="Save Changes"> &nbsp; &nbsp; <input type="submit" name="submit" value="Save and Approve">
   <?php } else { ?>
      <BR><span style="font-size:12px;font-style:italic;">Records can not be modified in REJECTED/DELETED status, please approve if you'd like to update this record</span><br>
   <?php } ?>

 </form>
 <button onclick="if (confirm('You are about to create a new record copying the values of this <?php echo $displayusertype; ?>.')) location.href='<?php echo getBaseURL(); ?>jsfadmin/admincontroller.php?action=usermodcloning&userid=<?php echo $userid; ?>&copyuser=1';">Copy account</button>
 
 
   <table width="800" cellpadding="0" cellspacing="0"><tr align="right"><td align="right">
   <table cellpadding="3" cellspacing="1"><tr>
   
   <!-- APPROVE button -->
   <?php if (0!=strcmp($user['dbmode'],"APPROVED")) { ?>
      <td><button onclick="if (confirm('If you approve this record, you also approve all related accounts.  Continue?')) location.href='<?php echo getBaseURL(); ?>jsfadmin/admincontroller.php?action=usermodcloning&userid=<?php echo $userid; ?>&approveuser=1';">Approve</button></td>
   <?php } ?>

   <!-- REJECT buttons -->
   <?php if (0==strcmp($user['dbmode'],"APPROVED")) { ?>
      <td><button onclick="window.open('<?php echo getBaseURL(); ?>jsfadmin/usermodcloning_reject.php?userid=<?php echo $user['userid']; ?>', 'newwindow', 'width=300, height=250');">Inactivate</button></td>
   <?php } else if (0==strcmp($user['dbmode'],"UPDATED")) { ?>
      <td><button onclick="if (confirm('Are you sure you want to reject changes to this record?')) location.href='<?php echo getBaseURL(); ?>jsfadmin/admincontroller.php?action=usermodcloning&userid=<?php echo $userid; ?>&rejectuser=1';">Reject Changes</button></td>
   <?php } else if (0==strcmp($user['dbmode'],"NEW")) { ?>
      <td><button onclick="if (confirm('Are you sure you want to reject this new record?')) location.href='<?php echo getBaseURL(); ?>jsfadmin/admincontroller.php?action=usermodcloning&userid=<?php echo $userid; ?>&rejectuser=1';">Reject</button></td>
   <?php } else if (0==strcmp($user['dbmode'],"REJECTED") || 0==strcmp($user['dbmode'],"DELETED")) { ?>
      <td><button onclick="window.open('<?php echo getBaseURL(); ?>jsfadmin/usermodcloning_reject.php?userid=<?php echo $user['userid']; ?>', 'newwindow', 'width=300, height=250');">Remove</button></td>
   <?php } ?>
   </tr></table>
   </td></tr></table>
 </div>
 </div> <!-- end usersect -->




  <div id="propertiessect" style="display:none;">
         <?php
               $wdObj = new WebsiteData();
               $webdata = $wdObj->getWebDataByName($user['usertype']." Properties");
               if ($webdata != NULL) {
         ?>
         <div style="margin:8px;padding:5px;border:1px solid #AAAAAA;background-color:#F1F1F1;border-radius:4px;">
         <!--div style="font-family:verdana;font-size:14px;font-weight:bold;">Properties</div -->
         <?php
                  $results = $wdObj->getDataByUserid($webdata['wd_id'], $user['userid']);
                  $sci = $results[0];
                  //$wdObj->printWebData($webdata['wd_id'], NULL, $user['userid'], $sci['wd_row_id'], NULL, "Short", "usermodcloning.php", $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php", TRUE,NULL,NULL,FALSE);
                  
         ?>

         
            <form name="cmssurveyform" enctype="multipart/form-data" action="/jsfadmin/admincontroller.php" method="POST">
            <input type="hidden" name="action" value="usermodupdatepropscloning">
            <input type="hidden" name="userid" value="<?php echo $user['userid']; ?>">
            <input type="hidden" name="tab" value="propertiessect">
         <?php $wdObj->printWebData($webdata['wd_id'],NULL,$user['userid'],$sci['wd_row_id'],NULL,"Short",NULL,NULL,FALSE); ?>
           <input type="submit" name="submit" value="Save">
           &nbsp; &nbsp;
           <input type="submit" name="submit" value="Save and Approve">
           </form>
         </div>
         <?php } ?>

   <?php
		 $webdata_arr = $wdObj->getWebDataByFuzzyName($user['usertype']." objects%");
		 if ($webdata_arr != NULL && count($webdata_arr)>0) {
			 for ($i=0; $i<count($webdata_arr); $i++) {
				 print "\n<br><a href=\"".getBaseURL().$GLOBALS['adminFolder']."admincontroller.php?s_userid=".$user['userid']."&userid=".$user['userid']."&wd_id=".$webdata_arr[$i]['wd_id']."&simpledisplay=1&action=wd_listrows\">".$webdata_arr[$i]['name']."</a>";
			 }
		 }
   ?>
   </div>


   
   
   
   
   
   
   
   
   
   

  <div id="properties2sect" style="display:none;">
         <?php
               $wdObj = new WebsiteData();
               $webdata = $wdObj->getWebDataByName($user['usertype']." Properties");
               if ($webdata != NULL) {
                  $results = $wdObj->getDataByUserid($webdata['wd_id'], $user['userid']);
                  $sci = $results[0];
         ?>
         
<style>
#jsfwdwdsubmitbtn{display:none;}
</style>         
         
            <div style="margin:5px;padding:5px;" id="jsfwdarea"></div>
            <div id="usermodpropbuttons" style="margin-bottom:10px;">
               <div class="jsfwdwdsubmitbtn" onclick="jsfusermod_approve=false;jsfwdSubmitWDForm(2);jQuery('#usermodpropbuttons').hide();">Save</div>
               <div class="jsfwdwdsubmitbtn" onclick="jsfusermod_approve=true;jsfwdSubmitWDForm(2);jQuery('#usermodpropbuttons').hide();">Save and Approve</div>
               <div style="clear:both;"></div>
            </div>
            <script>
            jsfwd_servercontroller = 'server/jsoncontroller.php?format=jsonp';
            //jsf_getwebdata_jsonp('','<?php echo getBaseURL(); ?>','','<?php echo $webdata['wd_id']; ?>','',<?php echo $user['userid']; ?>,<?php echo $sci['wd_row_id']; ?>,1);
            jsf_getwebdata_jsonp('','<?php echo getBaseURL(); ?>','','<?php echo $webdata['wd_id']; ?>','',<?php echo $user['userid']; ?>,<?php echo $sci['wd_row_id']; ?>);
            
            var jsfusermod_approve = false;
            function jsfwdcallback_end(jsondata) {
               if(Boolean(jsfusermod_approve)) {
                  //https://www.plasticsmarkets.org/jsfadmin/admincontroller.php?action=usermodcloning&userid=22
                  location.href='<?php echo getBaseURL().$GLOBALS['adminFolder']; ?>admincontroller.php?action=usermodcloning&userid=<?php echo $user['userid']; ?>&tab=properties2sect&approveuser=1';
               } else {
                  location.href='<?php echo getBaseURL().$GLOBALS['adminFolder']; ?>admincontroller.php?action=usermodcloning&userid=<?php echo $user['userid']; ?>&tab=properties2sect&markupdated=1';
               }
            }
            </script>
         <?php } ?>

   <?php
		 $webdata_arr = $wdObj->getWebDataByFuzzyName($user['usertype']." objects%");
		 if ($webdata_arr != NULL && count($webdata_arr)>0) {
			 for ($i=0; $i<count($webdata_arr); $i++) {
				 print "\n<br><a href=\"".getBaseURL().$GLOBALS['adminFolder']."admincontroller.php?s_userid=".$user['userid']."&userid=".$user['userid']."&wd_id=".$webdata_arr[$i]['wd_id']."&simpledisplay=1&action=wd_listrows\">".$webdata_arr[$i]['name']."</a>";
			 }
		 }
   ?>
   </div>


   
   
   
   
   
   
   
   
   
   

   <div id="objectssect" style="display:none;">
   </div>
   <script>
   
   function searchwdhtags(htags,orderby,searchtxt){
      var url = '<?php echo getBaseURL().$GLOBALS['codeFolder']; ?>jsoncontroller.php?action=jsfhashtag&tb=webdata&col=htags&prk=wd_id&htaction=search';
      url += '&userid=<?php echo isLoggedOn(); ?>&token=<?php echo $_SESSION['s_user']['token']; ?>';
      url += '&searchcols=' + encodeURIComponent('wd_id,name,htags');
      if(Boolean(htags)) url += '&searchht=' + encodeURIComponent(htags);
      if(Boolean(searchtxt)) url += '&searchtxt=' + encodeURIComponent(searchtxt);
      if(Boolean(orderby)) url += '&orderby=' + encodeURIComponent(orderby);
      //alert('url: ' + url);
      jsf_json_sendRequest(url,setupwdhtags);
   }
   
   
   var htags_wd_urls = [];
   function setupwdhtags(jsondata){
      //alert('jsondata: ' + JSON.stringify(jsondata));
      var css = 'float:left;font-family:verdana;color:#772222;margin-bottom:10px;margin-right:5px;padding:5px;border:1px solid #DEDEDE;border-radius:4px;width:320px;height:160px;overflow:auto;';
      var str = '';
      htags_wd_urls = [];
      str += '<div style=\"padding:10px;position:relative;\">';
      if(Boolean(jsondata.results) && jsondata.results.length>0){
         str += '<div id=\"htags_wd_survey\" ';
         str += 'style=\"' + css + 'display:none;\"';
         str += '>';
         str += '<div style=\"font-size:18px;\">Surveys</div>';
         str += '<div id=\"htags_wd_survey_list\" style=\"font-size:12px;\"></div>';
         str += '</div>';
         
         for(var i=0;i<jsondata.results.length;i++){
            var addlcss = 'display:none;';
            if(jsondata.results[i].htags.indexOf('#survey') === -1) {
               if(jsondata.results[i].htags.indexOf('#display') !== -1) addlcss = '';
               str += '<div id=\"htags_wd_' + jsondata.results[i].wd_id + '\" ';
               str += 'style=\"' + css + addlcss + '\"';
               str += '>';
               str += '<div style=\"margin-bottom:4px;font-size:12px;\" onclick=\"window.open(\'<?php echo getBaseURL(); ?>jsfadmin/admincontroller.php?action=webdata&wd_id=' + jsondata.results[i].wd_id + '\');\">' + jsondata.results[i].name + '</div>';
               str += '<div id=\"htags_wd_' + jsondata.results[i].wd_id + '_list\" style=\"font-size:12px;\"></div>';
               str += '<div onclick=\"window.open(\'<?php echo getBaseURL().$GLOBALS['codeFolder']; ?>ViewWDataJSON.php?admin=1&wd_id=' + jsondata.results[i].wd_id + '&foruserid=<?php echo $user['userid']; ?>\');\" style=\"font-size:10px;cursor:pointer;color:blue;\">+ Add</div>';
               str += '</div>';
            }
            var url = '';
            url = '<?php echo getBaseURL().$GLOBALS['codeFolder']; ?>jsoncontroller.php?action=getwdandrows';
            url += '&wd_id=' + jsondata.results[i].wd_id;
            url += '&foruserid=<?php echo $user['userid']; ?>';
            url += '&addrowdisplay=1';
            url += '&orderby=' + encodeURIComponent('d.created DESC');
            url += '&userid=<?php echo isLoggedOn(); ?>&token=<?php echo $_SESSION['s_user']['token']; ?>';
            htags_wd_urls.push(url);
            //jsf_json_sendRequest(url,);
         }
      }
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      jQuery('#objectssect').html(str);
      htags_populate_rows();
   }
   
   function htags_populate_rows() {
      if(htags_wd_urls.length>0) {
         var wdurl = htags_wd_urls.shift();
         //alert('url: ' + wdurl);
         jsf_json_sendRequest(wdurl,htags_populate_rows_return);
      }
   }
   
   function htags_populate_rows_return(jsondata) {
      //alert('jsondata: ' + JSON.stringify(jsondata));
      if(Boolean(jsondata.rows) && jsondata.rows.length > 0) {
         var str = '';
         var divid1 = '#htags_wd_' + jsondata.wd_id;
         var divid2 = '#htags_wd_' + jsondata.wd_id + '_list';
         
         if(jsondata.htags.indexOf('#survey') !== -1) {
            divid1 = '#htags_wd_survey';
            divid2 = '#htags_wd_survey_list';
            str += '<div onclick=\"window.open(\'<?php echo getBaseURL().$GLOBALS['codeFolder']; ?>ViewWDataJSON.php?admin=1&wd_id=' + jsondata.wd_id + '&wd_row_id=' + jsondata.rows[0].wd_row_id + '\');\" style=\"cursor:pointer;color:blue;\">';
            str += jsondata.wdname;
            str += '</div>';
         } else {
            for(var i=0;i<jsondata.rows.length;i++) {
               str += '<div onclick=\"window.open(\'<?php echo getBaseURL().$GLOBALS['codeFolder']; ?>ViewWDataJSON.php?admin=1&wd_id=' + jsondata.wd_id + '&wd_row_id=' + jsondata.rows[i].wd_row_id + '\');\" style=\"cursor:pointer;color:blue;\">';
               str += jsondata.rows[i].display;
               str += '</div>';            
            }
         }
         
         jQuery(divid2).append(str);
         jQuery(divid1).show();
      }
      htags_populate_rows();
   }
   
   searchwdhtags('associatedtodb','createdon DESC');
   
   //jsf_searchforwd(userid,token,searchtxt,prvsrvy,externalid,foruserid,limit);
   </script>
   
   
   
   
   
   
   

   <div id="adminsect" style="display:none;">
      <div style="padding:10px;font-size:14px;font-family:verdana;font-color:#333333;">

        <!-- User password --> 
        <div style="border:1px solid #444444;border-radius:5px;padding:8px;background-color:#DEDEDE;margin-right:15px;margin-bottom:15px;">
	     <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
	     <input type="hidden" name="action" value="modifypasswordcloning">
	     <input type="hidden" name="email" value="<?= $vars['email'] ?>">
	     <input type="hidden" name="userid" value="<?= $user['userid'] ?>">
	     <input type="hidden" name="p_userid" value="<?= $user['userid'] ?>">
        <div style="font-size:14px;font-weight:bold;">Update Password</div>
        <table cellpadding="2" cellspacing="2">
        <tr><td>New Password</td><td><input type="password" name="password" size="20"></td></tr>
        <tr><td>Confirm Password</td><td><input type="password" name="cpassword" size="20"></td></tr>
        </table>
		  <div style="margin:8px;"><input type="submit" name="submit" value="Modify Password"></div>
        </form>
        </div>


   <?php if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),11)) { ?>
      <div style="border:1px solid #444444;border-radius:5px;padding:8px;background-color:#DEDEDE;margin-right:15px;margin-bottom:15px;">
      <table cellspacing="2" cellpadding="2">
      <tr><th>User Authority Settings</th></tr>
      <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
      <input type="hidden" name="action" value="changeuseraccesscloning">
      <input type="hidden" name="email" value="<?= $user['email'] ?>">
      <input type="hidden" name="userid" value="<?= $user['userid'] ?>">
      <tr><td>
      <?php print getCheckboxList2Across("useraccess", $ua->getLevels(), $ua->getUserAccessLevels($user['userid'])); ?>
      </td></tr>
      <tr><td align="center"><input type="submit" name="submit" value="submit"></td></tr>
      </form>
      </table>
      </div>

      <div style="border:1px solid #444444;border-radius:5px;padding:8px;background-color:#DEDEDE;margin-right:15px;margin-bottom:15px;">
      <?php $points = $ua->getUsersAccessPoints($user['userid']); ?>
      <table cellspacing="2" cellpadding="2">
      <tr><th colspan="3">Additional User Authority Settings</th></tr>
      <tr><td>System Name</td><td>System ID</td><td>&nbsp;</td></tr>
      
      <?php
          for ($i=0; $i<count($points); $i++) {
            $line = $points[$i];   
      ?>      
         <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
         <input type="hidden" name="action" value="useraccesspointscloning">
         <input type="hidden" name="email" value="<?= $user['email'] ?>">
         <input type="hidden" name="userid" value="<?= $user['userid'] ?>">
         <input type="hidden" name="remove" value="1">
         <input type="hidden" name="sys" value="<?php echo $line['sys']; ?>">
         <input type="hidden" name="id" value="<?php echo $line['id']; ?>">
         <tr>
         <td><?php echo $line['sys']; ?></td>
         <td><?php echo $line['id']; ?></td>
         <td><input type="submit" name="submit" value="Remove"></td>
         </tr>
         </form>
      <?php } ?>
      <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
      <input type="hidden" name="action" value="useraccesspointscloning">
      <input type="hidden" name="email" value="<?= $user['email'] ?>">
      <input type="hidden" name="userid" value="<?= $user['userid'] ?>">
      <input type="hidden" name="add" value="1">
      <tr>
      <td><input type="text" name="sys" size="15" value="<?php echo ""; ?>"></td>
      <td><input type="text" name="id" size="10" value="<?php echo ""; ?>"></td>
      <td><input type="submit" name="submit" value="Add"></td>
      </tr>
      </form>
      </table>
      </div>
   <?php } ?>
   </div>
  </div>



  
  
  
  
  
  
  
  

   <div id="relsect" style="display:none;">

   <?php if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),11)) { ?>
      <!-- user relationships -->
      <br>
      <?php
         $show_usertype_count = getParameter("show_usertype_count");
         if ($show_usertype_count==NULL) $show_usertype_count = 20;
         $show_usertype = getParameter("show_usertype");
         $show_reltype = getParameter("show_reltype");

         $relTypeOpt = $ua->getUserRelations();
         $relTypeSel = getOptionList("rel_type", $relTypeOpt);
         //$rel_count = $ua->getUsersRelated($user['userid'],"to",$show_reltype,$show_usertype,"useracct",NULL,TRUE);
         $rels = $ua->getUsersRelated($user['userid'],"to",$show_reltype,$show_usertype,"useracct",$show_usertype_count,FALSE);
      ?>
      <script type="text/javascript">
         if (typeof showcmstxtonly == 'undefined') { 
            var e = document.createElement("script");
            e.src = "<?php echo $GLOBALS['baseURLSSL']; ?>jsfcode/getcms.js";
            e.type = "text/javascript";
            document.getElementsByTagName("head")[0].appendChild(e);
         }
      </script>

      
      <div style="border:1px solid #444444;border-radius:5px;padding:8px;margin-right:15px;margin-bottom:15px;">
      <div style="max-height:250px;overflow-y:auto;margin-bottom:10px;">
      <table border="0" cellspacing="2" cellpadding="4">
      <tr><td colspan="2"><b>Related Users</b></td></tr>
      <?php
          if (count($rels)<1) {
            print "<tr><td colspan=\"2\"><i>This account has no related users.</i></td></tr>";
          } else {
            for ($i=0; $i<count($rels); $i++) {
               $line = $rels[$i];
               $user2 = $ua->getUser($rels[$i]['reluserid']);
               $bgcolor="#DEDEDE";
               if ($i%2==0) $bgcolor="#FFFFFF";
      ?> 
         <tr bgcolor="<?php echo $bgcolor; ?>"><td colspan="2">
            <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
            <input type="hidden" name="action" value="userrelationcloning">
            <input type="hidden" name="userid" value="<?= $user['userid'] ?>">
            <input type="hidden" name="userrel_id" value="<?= $line['userrel_id'] ?>">
            <input type="hidden" name="update" value="1">
            <input type="hidden" name="tab" value="relsect">

            <div style="clear:both;margin-bottom:4px;margin-top:7px;font-size:12px;font-family:arial;">
               <div style="float:left;width:220px;">
                  <?php 
                     //echo "<a href=\"".$GLOBALS['baseURLSSL']."jsfadmin/admincontroller.php?action=usermodcloning&userid=".$user2['userid']."\">";
                     //echo $line['reluserid']." ".$user2['fname']." ".$user2['lname']." ".$user2['company'];
                     //echo "</a><br>";
                     echo "<div onclick=\"relupdateprefix='rel_';showRelAccount('".$user2['userid']."','".$user2['token']."');\" style=\"color:blue;font-family:verdana;cursor:pointer;\">";
                     echo $line['reluserid']." ".$user2['fname']." ".$user2['lname']." ".$user2['company'];
                     echo "</div><br>";
                     if ($user2['email']!=NULL && strpos($user2['email'],"dummy")===FALSE) echo $user2['email']."<br>";
                     if ($user2['phonenum']!=NULL) echo $user2['phonenum']."<br>";
                  ?>
               </div>
               <div style="float:left;width:130px;margin-left:15px;">
                  <?php 
                     if ($user2['addr1']!=NULL) echo $user2['addr1']."<br>";
                     if ($user2['addr2']!=NULL) echo $user2['addr2']."<br>";
                     echo $user2['city']." ".$user2['state']." ".$user2['zip'];
                  ?>
               </div>
               <div style="float:left;width:160px;margin-left:15px;">
                  <?php 
                     echo getOptionList("rel_type", $relTypeOpt,$line['rel_type']);
                     //echo $ua->getRelTypeString($line['rel_type']);
                  ?>
               </div>
               <div style="float:left;width:190px;margin-left:15px;">
                  <input type="submit" name="submit" value="Update"> &nbsp;
                  <input type="submit" name="submit" value="Remove">
               </div>
               <div style="clear:both;"></div>
            </div>
            </form>
         </td></tr>
      <?php 
            } 
          }
      ?>
      </table>
      </div>

      <script>
      var relupdateprefix = "rel_";
      
      function showRelAccount(uid,token){
         //get the user information and display it below
         var url = '<?php echo getBaseURL().$GLOBALS['codeFolder']; ?>jsoncontroller.php?action=acctinfo&token=' + token + '&userid=' + uid;
         //alert('url: ' + url);
         jsf_json_sendRequest(url,returnShowRelAccount);
      }
      
      function returnShowRelAccount(jsondata){
         //alert(JSON.stringify(jsondata));
         var str = 'Record # ' + jsondata.user.userid;
         if(jsondata.user.email.indexOf("dummy") == -1) str = str + ' (' + jsondata.user.email + ')';
         $('#' + relupdateprefix + 'useridfield_title').html(str);
         
         if (jsondata.user.usertype=='org') {
         	  $('#' + relupdateprefix + 'fname_div1').hide();
         	  $('#' + relupdateprefix + 'fname_div2').hide();
         	  $('#' + relupdateprefix + 'lname_div1').hide();
         	  $('#' + relupdateprefix + 'lname_div2').hide();
         } else {
         	  $('#' + relupdateprefix + 'fname_div1').show();
         	  $('#' + relupdateprefix + 'fname_div2').show();
         	  $('#' + relupdateprefix + 'lname_div1').show();
         	  $('#' + relupdateprefix + 'lname_div2').show();
         }
         
         $('#' + relupdateprefix + 'useridfield').val(jsondata.user.userid);
         $('#' + relupdateprefix + 'updateuserid').val(jsondata.user.userid);
         $('#' + relupdateprefix + 'profilefname').val(jsondata.user.fname);
         $('#' + relupdateprefix + 'profilelname').val(jsondata.user.lname);
         $('#' + relupdateprefix + 'profilecompany').val(jsondata.user.company);
         $('#' + relupdateprefix + 'profiletitle').val(jsondata.user.title);
         $('#' + relupdateprefix + 'profileaddr1').val(jsondata.user.addr1);
         $('#' + relupdateprefix + 'profileaddr2').val(jsondata.user.addr2);
         $('#' + relupdateprefix + 'profilecity').val(jsondata.user.city);
         $('#' + relupdateprefix + 'profilestate option[value=\"' + jsondata.user.state + '\"]').prop('selected',true)
         $('#' + relupdateprefix + 'profilecountry option[value=\"' + jsondata.user.country + '\"]').prop('selected',true)
         $('#' + relupdateprefix + 'profilezip').val(jsondata.user.zip);
         $('#' + relupdateprefix + 'profilewebsite').val(jsondata.user.website);
         $('#' + relupdateprefix + 'profilephonenum').val(jsondata.user.phonenum);
         //$('#' + relupdateprefix + 'profile').val(jsondata.user.);
         $('#' + relupdateprefix + 'updatereluser').show();
      }
      </script>
      
      <div style="margin:10px;padding:7px;display:none;background-color:#FFFFF1;border-radius:5px;border:1px solid #999999;" id="rel_updatereluser">
      <div style="font-size:14px;font-weight:bold;font-family:verdana;color:#7777EE;margin-bottom:5px;" id="rel_useridfield_title">Record number</div>
      <form action="/jsfadmin/admincontroller.php" method="post">
      <input type="hidden" name="action" value="usermodrelupdatecloning">
      <input type="hidden" name="tab" value="relsect">
      <input type="hidden" name="userid" value="<?php echo $userid; ?>">
      <input type="hidden" name="updatereluser" value="1">
      <input type="hidden" name="prefix" value="rel_">
      <?php
      	$exceptions = array();
      	$exceptions[] = "email";
      	$exceptions[] = "phonenum2";
      	$exceptions[] = "phonenum3";
      	$renames = array();
         echo $ua->printUserForm($userid,$exceptions,$renames,"rel_",2);
      ?>
      <input type="submit" name="submit" value="Save" style="font-size:14px;font-family:verdana;">
      &nbsp; &nbsp;
      <input type="submit" name="submit" value="Save and Approve" style="font-size:14px;font-family:verdana;">
      </form>

      <form action="/jsfadmin/admincontroller.php" method="post">
      <input type="hidden" name="userid" value="" id="rel_useridfield">
      <input type="hidden" name="action" value="usermodcloning">
      <input type="submit" name="submit" style="margin-top:7px;margin-bottom:7px;font-size:12px;font-family:verdana;" value="Open Full Record">
      </form>
      
      </div>

      </div>


      <div style="border:1px solid #444444;border-radius:5px;padding:8px;margin-right:15px;margin-bottom:15px;">
      <div style="max-height:250px;overflow-y:auto;margin-bottom:10px;">
      <table border="0" cellspacing="2" cellpadding="4">
      <TR><td colspan="2"><b>Accounts that reference this</b></td></tr>
      <TR><td colspan="2">
      <?php
          //getUsersRelated($userid,$direction="to",$rel_type=NULL,$usertype=NULL,$table="useracct",$limit=NULL,$countonly=FALSE){
          //$reverserels_count = $ua->getUsersRelated($user['userid'],"to",$show_reltype,$show_usertype,"useracct",NULL,TRUE);
          $reverserels = $ua->getUsersRelated($user['userid'],"from",$show_reltype,$show_usertype,"useracct",$show_usertype_count,FALSE);

          if (count($reverserels)>0) {
             for ($i=0; $i<count($reverserels); $i++) {
               $line = $reverserels[$i];
               $user2 = $ua->getUser($line['userid']);
               $bgcolor="#DEDEDE";
               if ($i%2==0) $bgcolor="#FFFFFF";
      ?> 
            <div style="clear:both;margin-bottom:4px;margin-top:7px;padding:4px;font-size:14px;font-family:arial;background-color:<?php echo $bgcolor; ?>;">
               <div style="float:left;width:240px;">
                  <?php 
                     //echo "<a href=\"".$GLOBALS['baseURLSSL']."jsfadmin/admincontroller.php?action=usermodcloning&userid=".$user2['userid']."\">";
                     //echo $user2['userid']." ".$user2['fname']." ".$user2['lname']." ".$user2['company'];
                     //echo "</a><br>";
                     echo "<div onclick=\"relupdateprefix='ref_';showRelAccount('".$user2['userid']."','".$user2['token']."');\" style=\"color:blue;font-family:verdana;cursor:pointer;\">";
                     echo $line['userid']." ".$user2['fname']." ".$user2['lname']." ".$user2['company'];
                     echo "</div><br>";                     
                     if ($user2['email']!=NULL && strpos($user2['email'],"dummy")===FALSE) echo $user2['email']."<br>";
                     if ($user2['phonenum']!=NULL) echo $user2['phonenum']."<br>";
                  ?>
               </div>
               <div style="float:left;width:180px;margin-left:15px;">
                  <?php 
                     if ($user2['addr1']!=NULL) echo $user2['addr1']."<br>";
                     if ($user2['addr2']!=NULL) echo $user2['addr2']."<br>";
                     echo $user2['city']." ".$user2['state']." ".$user2['zip'];
                  ?>
               </div>
               <div style="float:left;width:200px;margin-left:15px;">
               <?php echo $ua->getRelTypeString($line['rel_type']); ?>
               </div>
               <div style="clear:both;"></div>
            </div>
      <?php 
            }
         } else {
             print "<TR><td colspan=\"2\"><i>No references are made to this account.</i></td></tr>";            
         }
      ?>
      </td></tr>
      </table>
      </div>
      
      <div style="margin:10px;padding:7px;display:none;background-color:#FFFFF1;border-radius:5px;border:1px solid #999999;" id="ref_updatereluser">
      <div style="font-size:14px;font-weight:bold;font-family:verdana;color:#7777EE;margin-bottom:5px;" id="ref_useridfield_title">Record number</div>
      <form action="/jsfadmin/admincontroller.php" method="post">
      <input type="hidden" name="action" value="usermodrelupdatecloning">
      <input type="hidden" name="tab" value="relsect">
      <input type="hidden" name="userid" value="<?php echo $userid; ?>">
      <input type="hidden" name="updatereluser" value="1">
      <input type="hidden" name="prefix" value="ref_">
      <?php
      	$exceptions = array();
      	$exceptions[] = "email";
      	$exceptions[] = "phonenum2";
      	$exceptions[] = "phonenum3";
      	$renames = array();
         echo $ua->printUserForm($userid,$exceptions,$renames,"ref_",2);
      ?>
      <input type="submit" name="submit" value="Save" style="font-size:14px;font-family:verdana;">
      &nbsp; &nbsp;
      <input type="submit" name="submit" value="Save and Approve" style="font-size:14px;font-family:verdana;">
      </form>

      <form action="/jsfadmin/admincontroller.php" method="post">
      <input type="hidden" name="userid" value="" id="ref_useridfield">
      <input type="hidden" name="action" value="usermodcloning">
      <input type="submit" name="submit" style="margin-top:7px;margin-bottom:7px;font-size:12px;font-family:verdana;" value="Open Full Record">
      </form>
      
      </div>
      
      
      </div>

      <div style="border:1px solid #444444;border-radius:5px;padding:8px;margin-right:15px;margin-bottom:15px;">
      <table border="0" cellspacing="2" cellpadding="4">
      <tr><td colspan="2"><b>Find contact/relationship</b></td></tr>
      <tr>
      <td><input type="text" id="usersearchajax" name="usearsearchajax" value="" size="30"></td>
      <td><input type="button" name="usersearchbtn" value="Search" onClick="var urlaj='<?php echo $GLOBALS['baseURLSSL']; ?>jsfcode/ajaxcontroller.php?action=listusers&userid1=<?php echo $user['userid']; ?>&display=intelligent&search=' + document.getElementById('usersearchajax').value;showcmstxtonly(urlaj,3);"></td>
      </tr>
      <tr><td colspan="2"><div id="ajaxrechtml"></div></td></tr>
      <form id="userrel_aj" name="userrel_aj" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
      <input type="hidden" name="action" value="userrelationcloning">
      <input type="hidden" name="userid" value="<?= $user['userid'] ?>">
      <input type="hidden" name="reluserid" value="">
      <input type="hidden" name="add" value="1">
      <input type="hidden" name="tab" value="relsect">
      <tr><td><?php echo $relTypeSel; ?></td><td><input type="submit" name="submit" value="Add"></td></tr>
      </form>
      </table>
      </div>

      <div style="border:1px solid #444444;border-radius:5px;padding:8px;margin-right:15px;margin-bottom:15px;">
      <table border="0" cellspacing="2" cellpadding="4">
      <tr><td colspan="2"><br>
         <table cellpadding="0" cellspacing="2">
         <tr><td colspan="2"><b>Create a new contact/relationship</b></td></tr>
         <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
         <input type="hidden" name="tab" value="relsect">
         <input type="hidden" name="action" value="adduserandrelationcloning">
         <input type="hidden" name="userid" value="<?php echo $user['userid']; ?>">
         <input type="hidden" name="refsrc" value="ADMINISTRATION:<?php echo isLoggedOn(); ?>">
         <tr><td></td><td><?php echo $relTypeSel; ?></td></tr>
         <tr><td>First Name </td><td><input type="text" name="fname" size="25" value=""></td></tr>
         <tr><td>Last Name </td><td><input type="text" name="lname" size="25" value=""></td></tr>
         <tr><td>Company Name</td><td><input type="text" name="company" size="25" value=""></td></tr>
         <tr><td>Gender </td><td><input type="radio" name="gender" value="M">Male &nbsp; &nbsp <input type="radio" name="gender" value="F">Female</td></tr>
         <tr><td>Website</td><td><input type="text" name="website" size="25"></td></tr>
         <tr><td>Phone Number </td><td><input type="text" name="phonenum" size="25"></td></tr>
         <tr><td>Fax</td><td><input type="text" name="phonenum2" size="25"></td></tr>
         <tr><td>Address </td><td><input type="text" name="addr1" size="25"></td></tr>
         <tr><td>Address2</td><td><input type="text" name="addr2" size="25"></td></tr>
         <tr><td>City </td><td><input type="text" name="city" size="25"></td></tr>
         <tr><td>State </td><td>
               <?= getStateOptions("BL","state") ?>&nbsp;&nbsp;Zip Code &nbsp;&nbsp;<input type="text" name="zip" size="5">
         </td></tr>
         <tr><td>Country </td><td><?php echo listCountries("","country",TRUE); ?></td></tr>
         <TR><TD>Email Address </TD><TD><input type="text" name="email"></TD></TR>
         <tr><td></td><td align="right"><input type="submit" name="submit" value="Add"></td></tr>
         </form>
         </table>
      </td></tr>
      </table>
      </div>

   <?php } ?>
      </div>





   <div id="addlsect" style="display:none;">
   <?php if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),11)) { ?>
      <!-- custom area -->
      <?php
//error_reporting(E_ALL);
         if (class_exists("CustomUserDetails")) {
            $customObj = new CustomUserDetails();
            print $customObj->getUserDetailsHTML($user);
         }
      ?>

   <?php } ?>
   </div>


</td></tr>
</table>


<script>
<?php
   if (0==strcmp($tab,"usersect")) echo "expanduser();";
   else if (0==strcmp($tab,"propertiessect")) echo "expandproperties();";
   else if (0==strcmp($tab,"properties2sect")) echo "expandproperties2();";
   else if (0==strcmp($tab,"addlsect")) echo "expandaddl();";
   else if (0==strcmp($tab,"adminsect")) echo "expandadmin();";
   else if (0==strcmp($tab,"relsect")) echo "expandrel();";
?>
</script>


<?php } ?>
