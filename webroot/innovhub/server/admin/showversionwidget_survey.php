<?php
   $wd = new WebsiteData();
   $surveyName = "CMS_Survey_".$cmsid."_".$version;
   $webdata = $wd->getWebDataByName($surveyName);
   $wd_id = $webdata['wd_id'];
   if ($webdata==NULL || $wd_id==NULL) {
      $wd_id = $wd->newWebData($surveyName, "This survey is used as content in the CMS.", 50, NULL, NULL, 1, 1,NULL,NULL,$cmsid);
      $sec_id = $wd->addSection ($wd_id, -1, "", "", 1);
      $field_id = $wd->addField ($wd_id, $sec_id, NULL, "Temp Label", "", "TEXTBOX", 1, 1);
      $wd->deleteField($wd_id,$field_id);
      $webdata = $wd->getWebData($wd_id);
   }
   
   if ($webdata['wd_id']!=NULL) {
         $rowsObj = $wd->getRowsUser($wd_id,"u.lname");
         $rows = $rowsObj['results'];
         $opts_status['Active']="ACTIVE";
         $opts_status['Inactive']="INACTIVE";
         $sel = getOptionList("status", $opts_status, $webdata['status']);

         $typeopts['Open to everyone']="50";
         $typeopts['One time survey']="52";
         $typeopts['Require Log-in']="51";
         $typesel = getOptionList("privatesrvy", $typeopts, $webdata['privatesrvy']);

         $tab = getParameter("tab");
         if ($tab==NULL) $tab = "vheader";

         $tabsArr[0]['id']="vheader";
         $tabsArr[0]['name']="Survey Settings";
         $tabsArr[1]['id']="questions";
         $tabsArr[1]['name']="Survey Quetions";
         $tabsArr[2]['id']="results";
         $tabsArr[2]['name']="Survey Results";

         $tabbedbar = getTabs($tabsArr,$tab,"admbtn1","admbtn2","cms_".$cmsid."_".$version);
         echo $tabbedbar['javascript'];
      ?>
         <a name="tabs<?php echo 'cms_'.$cmsid.'_'.$version; ?>"></a>
         <table width="100%" cellpadding="0" cellspacing="0">
         <tr><td bgcolor="#FFFFFF"><div style="height:20px;width:1px;overflow:hidden;margin:0;padding:0;"></div></td></tr>
         <tr><td><?php echo $tabbedbar['links']; ?></td></tr>
         <tr><td bgcolor="#222222"><div style="height:1px;width:1px;overflow:hidden;margin:0;padding:0;"></div></td></tr>
         <tr><td><div style="height:10px;width:1px;"></div></td></tr>
         <tr><td>

         <div id="vheader" <?php if (strcmp($tab,"vheader")!=0) echo "style=\"display: none;\""; ?>>
         <b>Your Survey:</b><br>
         <table cellpadding="2" cellspacing="1" bgcolor="#222222"><tr><td>         
         <table cellpadding="3" cellspacing="1" bgcolor="#96a5f3">         
            <form action="<?php echo $contextURL; ?>" method="POST">
            <input type="hidden" name="cmsid" value="<?php echo $cmsid; ?>">
            <input type="hidden" name="wd_id" value="<?php echo $wd_id; ?>">
            <input type="hidden" name="name" value="<?php echo $surveyName; ?>">
            <input type="hidden" name="tab" value="vheader">
            <input type="hidden" name="updatesurvey" value="1">
         <tr><td colspan="2">Description:<br><textarea name="info" rows="4" cols="45"><?php echo convertBack($webdata['info']); ?></textarea></td></tr>
         <tr><td colspan="2"><input type="checkbox" name="emailresults" value="1" <?php if ($webdata['emailresults']==1) print "CHECKED"; ?> onClick="if(this.checked) document.getElementById('adminemailfld').style.display=''; else document.getElementById('adminemailfld').style.display='none';">Send an email to me when someone fills this out.</td></tr>
         <tr id="adminemailfld" <?php if ($webdata['emailresults']!=1) print "style=\"display:none;\""; ?>><td>Email address to receive:</td><td><input type="text" name="adminemail" value="<?php echo $webdata['adminemail']; ?>"></td></tr>
         <tr><td colspan="2"><input type="checkbox" name="usepassword" value="1" <?php if ($webdata['password']!=NULL) print "CHECKED"; ?> onClick="if(this.checked) document.getElementById('passwordfld').style.display=''; else document.getElementById('passwordfld').style.display='none';">Require a password to fill this out.</td></tr>
         <tr id="passwordfld" <?php if ($webdata['password']==NULL) print "style=\"display:none;\""; ?>><td>Form Password:</td><td><input type="text" name="password" value="<?php echo $webdata['password']; ?>"></td></tr>
         <tr><td>Status:</td><td><?php echo $sel; ?></td></tr>
         <tr><td>Type:</td><td><?php echo $typesel; ?></td></tr>
         <tr><td colspan="2" align="right"><input type="submit" name="submit" value="Save Survey"></td></tr>
            </form>
         </table>
         </td></tr></table>
         </div>

         
         <div id="questions" <?php if (strcmp($tab,"questions")!=0) echo "style=\"display: none;\""; ?>>
         <b>Your current survey fields/questions:</b><br>
      <?php
         $qtypes = array();
         $qtypes['TEXT']=TRUE;
         $qtypes['TEXTAREA']=TRUE;
         $qtypes['INT']=TRUE;
         $qtypes['DEC']=TRUE;
         $qtypes['CHECKBOX']=TRUE;
         $qtypes['SNGLCHKBX']=TRUE;
         $qtypes['RADIO']=TRUE;
         $qtypes['POLLRADIO']=TRUE;
         $qtypes['DROPDOWN']=TRUE;
         $qtypes['DATE']=TRUE;
         $qtypes['DATETIME']=TRUE;
         $qtypes['NEWLIKERT']=TRUE;
         $qtypes['NEWPRCNT']=TRUE;
         $qtypes['FILE']=TRUE;
         $qtypes['INFO']=TRUE;
         $qtypes['SPACER']=TRUE;
         $qtypes['IMAGE']=TRUE;
         $wd->printAdminSection($wd_id,-1,$contextURL."&cmsid=".$cmsid."&tab=questions",FALSE,$qtypes);
      ?>
         <br><br>
         <b>Add a field/question to your survey:</b><br>
         <table cellpadding="2" cellspacing="1" bgcolor="#222222"><tr><td>         
         <table cellpadding="3" cellspacing="1" bgcolor="#96a5f3">         
            <form action="<?php echo $contextURL; ?>" method="POST">
            <input type="hidden" name="vheader" value="1" >
            <input type="hidden" name="tab" value="questions" >
            <input type="hidden" name="addquestion" value="1">
            <input type="hidden" name="cmsid" value="<?php echo $cmsid; ?>">
            <input type="hidden" name="wd_id" value="<?php echo $wd_id; ?>">
            <td colspan="3">
               Sequence: <input type="text" size="1" name="sequence" value=""> &nbsp; 
               Field label: <input type="text" size="30" name="label" value="">
            </td>
            </tr><tr>
            <td colspan="2">Type: 
               <select name="field_type"  id="typeopt_new" onChange="changeType('new');">
               <option value=" "> </option> 
               <option value="TEXT">Text box</option> 
               <option value="TEXTAREA">Text area</option> 
               <option value="INT">Integer</option> 
               <option value="DEC">Decimal</option> 
               <option value="CHECKBOX">Checkboxes</option> 
               <option value="SNGLCHKBX">Single chckbx</option> 
               <option value="RADIO">Radio Buttons</option> 
               <option value="POLLRADIO">Poll</option> 
               <option value="DROPDOWN">Drop Down List</option> 
               <option value="DATE">Date</option> 
               <option value="DATETIME">Date And Time</option> 
               <option value="NEWLIKERT">Likert Scales</option> 
               <option value="NEWPRCNT">Percentage Scale</option> 
               <option value="FILE">File Upload</option> 
               <option value="INFO">Information only field</option> 
               <option value="SPACER">Spacer</option> 
               </select> 
            </td>
            <td colspan="2"></td>
            </tr><tr>
            <td colspan="4" id="val_label_new" style="display: none;"></td>
            </tr><tr>
            <td colspan="4" id="values_new" style="display: none;">
               <input type="text" size="100" name="question" value=""><br><br> 
            </td>
            </tr><tr>
            <td colspan="3">
               <table cellpadding="0" cellspacing="0"><tr>
               <td id="default_new" style="display: none;">
               Default Value: <input type="text" size="10" name="defaultval" value=""> 
               </td>
               </tr></table>
            </td>
            <td align="right"><input type="submit" name="Add Question" value="Add Survey Question"></td>
            </form>
         </table>
         </td></tr></table>





         <?php 
            $allFields = $wd->getAllFieldsSystem($wd_id);
            if ($allFields != NULL && count($allFields)>1) {
               $allFieldsOpts = NULL;
               for ($i=0; $i<count($allFields); $i++) $allFieldsOpts[$allFields[$i]['label']]=$allFields[$i]['field_id'];
               $f1DropDown = getOptionList("fid1", $allFieldsOpts);
               $f2DropDown = getOptionList("fid2", $allFieldsOpts);
         ?>
               <table bgcolor="#FFFFFF" border="0" cellpadding="5" cellspacing="0">
               <tr bgcolor="#DDDDDD" valign="top"><td align="center" colspan="2"><b>New Field Relationships:</b></td></tr>
               <tr bgcolor="#DDDDDD" valign="top"><td align="center" colspan="2">If you want the answer of one question to affect the display of another, you'll need to set up field relationships</td></tr>
               <form action="<?php echo $contextURL; ?>#section1" name="newrelationship" method="POST">
               <input type="hidden" name="tab" value="questions">
               <input type="hidden" name="newFieldRel" value="1">
               <input type="hidden" name="rel_type" value="VALUE">
               <input type="hidden" name="wd_id" value="<?php echo $wd_id; ?>">
               <input type="hidden" name="cmsid" value="<?php echo $cmsid; ?>">
               <tr>
               <td>Parent Field: </td><td><?php echo $f1DropDown; ?></td>
               </tr>
               <tr>
               <td>Value:</td><td><input type="text" size="10" name="f1value" value=""> </td>
               </tr>
               <tr>
               <td>Affected Field:</td><td><?php echo $f2DropDown; ?></td>
               </tr>
               <tr>
               <td colspan="2" align="right"><input type="submit" name="submit" value="submit"></td>
               </tr>
               </form>
               </table>
         <?php
            }
         ?>







         </div>


         <div id="results" <?php if (strcmp($tab,"results")!=0) echo "style=\"display: none;\""; ?>>
            <b>Survey Summary</b><br><br>
            <?php
               $results = $wd->getStats($wd_id);
               $totals = $results['totals'];
               $info = $results['info'];
               foreach($totals as $key => $value) {
                  print "<table cellpadding=\"0\" cellspacing=\"1\" bgcolor=\"#000000\"><tr><td>";
                  print "<table cellpadding=\"2\" cellspacing=\"1\" bgcolor=\"#FFFFFF\">";
                  print "<tr bgcolor=\"#AACCEE\"><td colspan=\"3\">".$info[$key]['label']."</td></tr>";
                  print "<tr>";
                  print "<td><img src=\"".getBaseURL()."jsfimages/pixel.gif\" width=\"150\" height=\"1\"></td>";
                  print "<td><img src=\"".getBaseURL()."jsfimages/pixel.gif\" width=\"20\" height=\"1\"></td>";
                  print "<td><img src=\"".getBaseURL()."jsfimages/pixel.gif\" width=\"400\" height=\"1\"></td>";
                  print "</tr>";
                  $sum = 0;
                  foreach($value as $key1 => $value1) {
                     if (0!=strcmp($key1,"totalnumberanswered")) {
                        $sum += $value1;
                     }
                  }
                  foreach($value as $key1 => $value1) {
                     if (0!=strcmp($key1,"totalnumberanswered")) {
                        $numpx = round(($value1/$sum)*400);
                        print "<tr>"; 
                        print "<td>".$key1."</td>"; 
                        print "<td>".$value1."</td>"; 
                        print "<td><table cellpadding=\"0\" cellspacing=\"0\"><tr><td bgcolor=\"blue\"><img src=\"".getBaseURL()."jsfimages/pixel.gif\" width=\"".$numpx."\" height=\"12\"></td></tr></table></td>"; 
                        print "</tr>"; 
                     }
                  }
                  print "<tr bgcolor=\"#CCCCCC\"><td>Total Responses</td><td>".$sum."</td><td></td></tr>";
                  print "</table>";
                  print "</td></tr></table><br><br>";
                  
               }
            ?>
         </div>

         </td></tr>
         </table>
<?php } ?>
