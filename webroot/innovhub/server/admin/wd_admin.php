<?php
$ua = new UserAcct();
$wdOBJ = new WebsiteData();
$webdata = null;

$wd_id = $vars['wd_id'];
if($wd_id==NULL) $wd_id = getParameter("wd_id");
if ($wd_id != NULL) {
   unset($_SESSION['allrelsindexed_'.$wd_id]);
   $webdata = $wdOBJ->getWebData($wd_id);
   
   /*
   $rels = $wdOBJ->getAllFieldRels($webdata['wd_id']);
   $qs = $wdOBJ->getFieldNames($webdata['wd_id']);
   $exs = array();
   for($i=0;$i<count($rels);$i++){
      if(!isset($qs[$rels[$i]['fid1']]) || !isset($qs[$rels[$i]['fid2']]) || isset($exs[$rels[$i]['fid1'].$rels[$i]['fid2']])){
         //$wdOBJ->removeFieldRel($rels[$i]['rel_id']);
         print "\n<!-- removing field relationship ".$rels[$i]['fid1']." to ".$rels[$i]['fid2']." -->\n";
      } else {
         print "\n<!-- found field relationship ".$rels[$i]['fid1']." to ".$rels[$i]['fid2']." -->\n";
         $exs[$rels[$i]['fid1'].$rels[$i]['fid2']] = 1;
      }
   }
   */
   
}
$admin = $ua->isUserAdmin(isLoggedOn());
?>

<!-- begin: jsfadmin/wd_admin.php -->

<table border="0" cellspacing="0" cellpadding="0" align="center" width="95%">
<tr><td valign="top">
   <?php
      if ($webdata['wd_id'] == NULL) {
         print "<H2>Create a New Data Table</H2>";
         $showEmail = $_SESSION['s_user']['emailAddress'];
      } else {
         print "<h2>Edit your Data Table</h2>";
         $showEmail = $webdata['adminemail'];
      }
   ?>

   [<a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=wd_listwebdata"> Return to list of data tables </a>] &nbsp;&nbsp;&nbsp;&nbsp;
   <?php if ($webdata['wd_id'] != NULL) { ?>
      [<a href="<?php echo $GLOBALS['baseURLSSL']."sid/w".$wd_id; ?>.html" target="_blank"> View form </a>] &nbsp;&nbsp;&nbsp;&nbsp;
      [<a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=wd_listrows&wd_id=<?php echo $wd_id; ?>&pageLimit=25"> List of data records </a>] &nbsp;&nbsp;&nbsp;&nbsp;
      [<a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=wd_fieldposition&wd_id=<?php echo $wd_id; ?>"> Field Positioning </a>] &nbsp;&nbsp;&nbsp;&nbsp;
   <?php } else { ?>
      [<a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=webdata&subaction=newglossary"> Create a new Glossary </a>] &nbsp;&nbsp;&nbsp;&nbsp;
      [<a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=webdata&subaction=newsearchindex"> Create a new Search Index </a>] &nbsp;&nbsp;&nbsp;&nbsp;
   <?php } ?>
      
   <BR>

   <table bgcolor="#999999" border="1" cellpadding="3" cellspacing="0"><tr><TD>
   <table bgcolor="#FFFFFF" border="0" cellpadding="5" cellspacing="0">
      <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" name="dataMaster" method="POST">
      <input type="hidden" name="action" value="webdata">
      <input type="hidden" name="wd_id" value="<?php echo $wd_id; ?>">
      <TR valign="TOP">
          <TD>Name:</td><td><input type="text" name="sname" size="105" value="<?php echo convertBack($webdata['name']); ?>"></td>
        </tr><tr valign="TOP">
          <TD>Shortname:</td><td><input type="text" name="shortname" size="50" value="<?php echo convertBack($webdata['shortname']); ?>"></td>
        </tr><tr valign="TOP">
          <td>Description:</td><td><textarea name="sinfo" cols="80" rows="8"><?php echo convertBack($webdata['info']); ?></textarea></td>
        </tr><tr valign="TOP">
          <td>Coded Row Display:</td><td><textarea name="rowdisplay" cols="80" rows="4"><?php echo convertBack($webdata['rowdisplay']); ?></textarea></td>
        </tr><tr valign="TOP">
          <TD>Storage Type:</td>
          <td>
             <select name="privatesrvy">
             <option value="2">Public form/survey/data entry</option>
             <option value="1" <?php if($webdata['privatesrvy'] == 1) print "SELECTED"; ?>>Private form/survey/data entry</option>
             <option value="3" <?php if($webdata['privatesrvy'] == 3) print "SELECTED"; ?>>Website Data</option>
             <option value="4" <?php if($webdata['privatesrvy'] == 4) print "SELECTED"; ?>>Admin Data</option>
             <option value="5" <?php if($webdata['privatesrvy'] == 5) print "SELECTED"; ?>>Other Data</option>
             <option value="7" <?php if($webdata['privatesrvy'] == 7) print "SELECTED"; ?>>Mobile Survey Data</option>
             <option value="8" <?php if($webdata['privatesrvy'] == 8) print "SELECTED"; ?>>Mobile Internal Data</option>
             <option value="9" <?php if($webdata['privatesrvy'] == 9) print "SELECTED"; ?>>Mobile Secured Data</option>
             <option value="10" <?php if($webdata['privatesrvy'] == 10) print "SELECTED"; ?>>Search Index</option>
             <option value="11" <?php if($webdata['privatesrvy'] == 11) print "SELECTED"; ?>>Glossary</option>
<?php
      if (class_exists("AdminUI")) {
         $customCode = new AdminUI();
         $opts = $customCode->getCustomSurveyOptions();
         foreach ($opts as $key => $value) {
            print "<option value=\"".$key."\"";
            if ($webdata['privatesrvy']==$key) print " SELECTED";
            print ">".$value."</option>\n";
         }
      }
?>
             </select>
          </td>
        </tr><tr valign="TOP">
          <TD>Admin Email address</td>
          <td><input type="text" name="adminemail" size="50" value="<?php echo $showEmail; ?>"></td>
        </tr><tr valign="TOP">
          <TD>Email Option</td>
                <td>
                   <select name="emailresults">
                   <option value="2">No email on new records.</option>
                   <option value="1" <?php if($webdata['emailresults'] == 1) print "SELECTED"; ?>>Send admin an email when a new record is created.</option>
                   </select>
                </td>
        </tr><tr valign="TOP">
          <TD>Record Data:</td>
                <td>
                   <select name="saveresults">
                   <option value="1" >Save data for later retreival.</option>
                   <option value="2" <?php if($webdata['saveresults'] == 2) print "SELECTED"; ?>>Do not record data</option>
                   </select>
                </td>
        </tr><tr valign="TOP">
         <td colspan="2">
            <input type="checkbox" name="captcha" value="1" <?php if ($webdata['captcha']==1) print "CHECKED"; ?>> Add Captcha
            &nbsp; &nbsp;
            <input type="checkbox" name="esign" value="1" <?php if ($webdata['esign']==1) print "CHECKED"; ?>> Add eSignature
         </td>
        </tr><tr valign="TOP">
          <TD>Glossary:</td>
                <td>
            <?php
               $glossary = new Glossary($webdata['glossaryid']);
               print $glossary->getGlossaryOptions();
            ?>
                </td>
        </tr><tr valign="TOP">
<?php
         $ss = new Version();
         $shortcuts = $ss->getAllShortcuts(5);
         $shortcuts2 = $ss->getAllShortcuts(6);
         
         if($shortcuts!=NULL && $shortcuts2!=NULL) $shortcuts = array_merge($shortcuts,$shortcuts2);
         else if($shortcuts==NULL && $shortcuts2!=NULL) $shortcuts = $shortcuts2;
         
         //print "\n<!-- Shortcuts (email combined): \n";
         //print_r($shortcuts);
         //print "\n-->\n";
         
         $options = array();
         //for ($i=0; $i<count($shortcuts); $i++) $options[$shortcuts[$i]['title']." (".$shortcuts[$i]['filename'].")"] = $shortcuts[$i]['filename'];
         for ($i=0; $i<count($shortcuts); $i++) $options[$shortcuts[$i]['filename']] = $shortcuts[$i]['filename'];
         $sel = getOptionList("filename", $options, $webdata['filename'], TRUE);
?>
         <td>Email template:</td><td><?php echo $sel; ?></td>
        </tr><tr valign="TOP">
<?php
         if ($webdata['userrel']==NULL) $webdata['userrel']="SRVYADMIN";
         $rels = $ua->getUserRelations();
         $sel = getOptionList("userrel", $rels, $webdata['userrel']);
         
         $uopts = $ua->getUserTypes();
         $uopts_str = getOptionList("usertype", $uopts, convertBack($webdata['usertype']),TRUE);
?>
         <td>Relationship Type</td><td><?php echo $sel; ?></td>
        </tr><tr valign="TOP">
          <TD>User Type:</td>
          <!-- td><input type="text" name="usertype" size="10" value="<?php echo convertBack($webdata['usertype']); ?>"></td-->
          <td><?php echo $uopts_str; ?></td>
        </tr><tr valign="TOP">
          <td colspan="2" align="RIGHT"><input type="submit" name="submit" value="Submit"></td>
      </tr>
      </form>
   </table>
   </td></tr></table>

<?php if ($webdata['wd_id'] != NULL) { ?>
   
   
   <div style="border:1px solid #DADADA;border-radius:5px;padding:10px;margin:10px 2px 10px 2px;">
   <div style="color:blue;font-size:10px;cursor:pointer;" onclick="jQuery('#xmlsectionoption').show();">Add a section with XML</div>
   <div id="xmlsectionoption" style="display:none;margin-top:10px;">
   <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" name="xmlsection" method="POST">
   <input type="hidden" name="action" value="webdata">
   <input type="hidden" name="copySectionFromXML" value="1">
   <input type="hidden" name="wd_id" value="<?php echo $wd_id; ?>">
   <div>
   <textarea id="sect_xml_ta" name="xml" style="width:380px;height:250px;font-size:10px;" onkeyup="jQuery('#clearxmlexample').show();">
   </textarea>
   </div>
   <div style="margin:5px 0px 5px 0px;">
   <input type="submit" name="submit" value="submit">
   </div>
   </form>
   <script>
   function showXMLExample(divid) {
      var str = '';
      str += '<webdata>\n';
      str += '  <structure>\n';
      str += '    <wd_section>\n';
      str += '      <label>Test Section</label>\n';
      str += '      <sequence>100</sequence>\n';
      str += '      <wd_field>\n';
      str += '        <sequence>10</sequence>\n';
      str += '        <label>Contact Name</label>\n';
      str += '        <field_type>TEXT</field_type>\n';
      str += '        <header>1</header>\n';
      str += '        <required>1</required>\n';
      str += '      </wd_field>\n';
      str += '      <wd_field>\n';
      str += '        <sequence>20</sequence>\n';
      str += '        <label>Dropdown example</label>\n';
      str += '        <field_type>DROPDOWN</field_type>\n';
      str += '        <question>Option 1,Option 2,Option 3</question>\n';
      str += '        <header>1</header>\n';
      str += '        <required>0</required>\n';
      str += '      </wd_field>\n';
      str += '    </wd_section>\n';
      str += '  </structure>\n';
      str += '</webdata>\n';
      jQuery('#' + divid).val(str);
   }
   </script>
   <div style="margin:5px 0px 5px 0px;">
   <div onclick="showXMLExample('sect_xml_ta');jQuery('#seexmlexample').hide();jQuery('#clearxmlexample').show();" style="font-size:8px;cursor:pointer;" id="seexmlexample">See example</div>
   <div onclick="jQuery('#sect_xml_ta').val('');jQuery('#clearxmlexample').hide();jQuery('#seexmlexample').show();" style="display:none;font-size:8px;cursor:pointer;" id="clearxmlexample">Clear text area</div>
   </div>
   </div>
   </div>
   

<?php
print "\n<!-- ***chj*** webdata is not null -->\n";
         print "<BR>";
         print "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=webdataxml&structureonly=1&wd_id=".$wd_id."\">View xml structure only</a>";
         print " &nbsp; &nbsp;";
         print "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=webdataxml&wd_id=".$wd_id."\">View xml structure and data for backup</a>";
         print " &nbsp; &nbsp;";
         print "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=webdata&resequence=1&wd_id=".$wd_id."\">Resequence Sections and Fields</a>";
         print " &nbsp; &nbsp;";
         print "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=webdata&refinerels=1&wd_id=".$wd_id."\">Remove unnecessary field rels</a>";
         print "<br><br>";
         
         if($vars['commandoutput']!=NULL) {
            print "RESULT:";
            print $vars['commandoutput'];
            print "<br><br>";
         }
         
         //$wdOBJ->printAdminSection($wd_id,-1);
         $wdOBJ->printAdminSectionSmall($wd_id,-1);

         $allsections = $wdOBJ->getAllDataSections($wd_id);
         $allsectionsopts['Main Sect'] = -1;
         for ($i=0; $i<count($allsections); $i++) $allsectionsopts['Sect '.$allsections[$i]['sequence']] = $allsections[$i]['section'];
         $sectsel = getOptionList("parent_s", $allsectionsopts, -1);
?>
   <BR><HR><BR>
   <a name="new_stuff"></a>
   <table bgcolor="#FFFF00" border="1" cellpadding="3" cellspacing="0" width="100%">
   <tr><TD>
      <table width="100%" bgcolor="#FFFFFF" border="0" cellpadding="5" cellspacing="0">
               <tr bgcolor="#DDDDDD" valign="top">
                 <td colspan="4" align="center"><b>New Section:</b></td>
               </tr>
      <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php#new_stuff" name="newsection" method="POST">
      <input type="hidden" name="action" value="webdata">
      <input type="hidden" name="wd_id" value="<?php echo $wd_id; ?>">
      <input type="hidden" name="newSection" value="1">   
      <tr>
        <td colspan="2">Section: <input type="text" size="1" name="sequence" value=""></td> 
        <td colspan="2">Title: <input type="text" size="60" name="label" value=""></td>
      </tr><tr>
        <td colspan="2"><input type="checkbox" name="dyna" value="1">Make this section dynamic.</td>
        <td colspan="2">Dynamic Question: <input type="text" name="question" value="" size="50"> </td>
      </tr><tr>
        <td colspan="3">Parent Section: <?php echo $sectsel; ?></td><td align="right"><input type="submit" name="submit" value="submit"></td>
      </tr>
      </form>
               <tr>
                 <td colspan="4" align="center"><br></td>
               </tr>
      </table>

<?php
      if (count($allsections)>0) {
         unset($allsectionsopts['Main Sect']);
         $privacyList["Public"] = 0;
         $privacyList["Admin"] = -1;
         $privacyList["Superadmin"] = -2;
         $privacyList["Approved website users level 1"] = 1;
         $privacyList["Approved website users level 2"] = 2;
         $privacyList["Approved website users level 3"] = 3;
         $privacyList["Approved website users level 4"] = 4;
         $privacyList["Approved website users level 5"] = 5;
         $privacyList["Approved website users level 6"] = 6;
         $privacyList["Approved website users level 7"] = 7;
         $privacyList["Approved website users level 8"] = 8;
         $privacyList["Approved website users level 9"] = 9;
         $privacyList["Approved website users level 10"] = 10;
      
         $sectsel = getOptionList("parent_s", $allsectionsopts);
         $privacyDropDown = getOptionList("privacy", $privacyList);
         ?>
               <table width="100%" bgcolor="#FFFFFF" border="0" cellpadding="5" cellspacing="0">
               <tr bgcolor="#DDDDDD" valign="top">
                 <td colspan="4" align="center"><b>New Field:</b></td>
               </tr><tr>
               <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php#new_stuff" name="newfield" method="POST">
               <input type="hidden" name="action" value="webdata">
               <input type="hidden" name="newField" value="1">
               <input type="hidden" name="wd_id" value="<?php echo $wd_id; ?>">
               <td colspan="3">Sequence: <input type="text" size="1" name="sequence" value=""> &nbsp; Field label: <input type="text" size="50" name="label" value=""></td>
               <td><input type="checkbox" name="header" value="1">Show this field in header</td>
               </tr><tr>
               <td colspan="2">Type: <?php echo $wdOBJ->getQuestionOptions("field_type",NULL); ?> </td>
               <td colspan="2">Privacy: <?php echo $privacyDropDown; ?> </td>
               </tr><tr>
               <td colspan="2">Values: <input type="text" size="60" name="question" value=""> </td>
               <td colspan="2">Default Value: <input type="text" size="10" name="defaultval" value=""> </td>
               </tr><tr>
               <td colspan="2">Parent Section: <?php echo $sectsel; ?></td>
               <td colspan="2" align="right"><input type="submit" name="submit" value="submit"></td>
               </form>
               </tr>
               <tr>
                 <td colspan="4" align="center"><br></td>
               </tr>
               </table>
         <?php 
            $allFields = $wdOBJ->getAllFieldsSystem($wd_id);
            if ($allFields != NULL && count($allFields)>1) {
               $allFieldsOpts = NULL;
               for ($i=0; $i<count($allFields); $i++) $allFieldsOpts[$allFields[$i]['field_id']]=$allFields[$i]['field_id'];
               $f1DropDown = getOptionList("fid1", $allFieldsOpts);
               $f2DropDown = getOptionList("fid2", $allFieldsOpts);
         ?>
               <table width="100%" bgcolor="#FFFFFF" border="0" cellpadding="5" cellspacing="0">
               <tr bgcolor="#DDDDDD" valign="top">
                 <td colspan="4" align="center"><b>New Field Relationship:</b></td>

               </tr><tr>
               <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php#new_stuff" name="newrelationship" method="POST">
               <input type="hidden" name="action" value="webdata">
               <input type="hidden" name="newFieldRel" value="1">
               <input type="hidden" name="rel_type" value="VALUE">
               <input type="hidden" name="wd_id" value="<?php echo $wd_id; ?>">
               <td colspan="3">Parent Field: <?php echo $f1DropDown; ?> &nbsp; Value: <input type="text" size="10" name="f1value" value=""> </td>
               <td>Affected Field: <?php echo $f2DropDown; ?></td>
               </tr><tr>
               <td colspan="4" align="right"><input type="submit" name="submit" value="submit"></td>
               </form>

               </tr><tr>
               <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php#new_stuff" name="newrelationship" method="POST">
               <input type="hidden" name="action" value="webdata">
               <input type="hidden" name="newFieldRelSect" value="1">
               <input type="hidden" name="rel_type" value="VALUE">
               <input type="hidden" name="wd_id" value="<?php echo $wd_id; ?>">
               <td colspan="3">Parent Field: <?php echo $f1DropDown; ?> &nbsp; Value: <input type="text" size="10" name="f1value" value=""> </td>
               <td>Section: <?php echo $sectsel; ?></td>
               </tr><tr>
               <td colspan="4" align="right"><input type="submit" name="submit" value="submit"></td>
               </form>

               
               </tr><tr>               
               <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php#new_stuff" name="newrelationship" method="POST">
               <input type="hidden" name="action" value="webdata">
               <input type="hidden" name="newFieldRel" value="1">
               <input type="hidden" name="rel_type" value="VALUE">
               <input type="hidden" name="wd_id" value="<?php echo $wd_id; ?>">
               <td colspan="3">Parent Field: <input type="text" size="10" name="fid1"> &nbsp; Value: <input type="text" size="10" name="f1value" value=""> </td>
               <td>Affected Field: <input type="text" size="10" name="fid2"></td>
               </tr><tr>
               <td colspan="4" align="right"><input type="submit" name="submit" value="submit"></td>
               </form>
               
               
               
               </tr><tr>
               <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php#new_stuff" name="newrelationship" method="POST">
               <input type="hidden" name="action" value="webdata">
               <input type="hidden" name="newFieldRelSect" value="1">
               <input type="hidden" name="rel_type" value="VALUE">
               <input type="hidden" name="wd_id" value="<?php echo $wd_id; ?>">
               <td colspan="3">Parent Field: <input type="text" size="10" name="fid1"> &nbsp; Value: <input type="text" size="10" name="f1value" value=""> </td>
               <td>Section: <?php echo $sectsel; ?></td>
               </tr><tr>
               <td colspan="4" align="right"><input type="submit" name="submit" value="submit"></td>
               </form>

               
               </tr>
               </table>
         <?php
            }
         } 
         ?>

   </td></tr>
   </table>
<?php } else {  ?>
<?php print "\n<!-- ***chj*** webdata is null -->\n"; ?>
      
      <br>
      <hr>
      <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" name="webdataXMLCreate" method="POST">
      <input type="hidden" name="action" value="webdata">
      <h2>Or create a new survey with XML</h2>
      Paste XML below:<br>
      <textarea rows="30" cols="80" name="xml"></textarea><br>
      <input type="submit" name="submit" value="Create new data list based on XML">
      </form>

      <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" name="webdataXMLCreate" method="POST">
      <input type="hidden" name="action" value="webdata">
      <h2>Or, Paste XML from an old survey XML to be migrated to jData:</h2>
      Paste XML below:<br>
      <textarea rows="30" cols="80" name="migratexml"></textarea><br>
      <input type="submit" name="submit" value="Create new data list based on XML">
      </form>
<?php } ?>

</td></tr>
</table>

<!-- end: jsfadmin/wd_admin.php -->
