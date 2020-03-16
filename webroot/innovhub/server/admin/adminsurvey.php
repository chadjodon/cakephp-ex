<?php
$ua = new UserAcct();
$surveyOBJ = new Survey();
$survey = null;
if ($survey_id != NULL) $survey = $surveyOBJ->getSurvey($survey_id);
$admin = $ua->isUserAdmin(isLoggedOn());
?>

<!-- begin: jsfadmin/adminsurvey.php -->

<table border="0" cellspacing="0" cellpadding="0" align="center" width="850">
<tr><td valign="top" style="padding-left:25px;">
   <?php
      if ($survey == NULL) {
         print "<H2>Create a New Survey</H2>";
         $showEmail = $_SESSION['s_user']['emailAddress'];
      }
      else {
         print "<h2>Edit your Survey</h2>";
         $showEmail = $survey['adminemail'];
      }
   ?>

   [<a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=listsurveys"> Return to list of surveys </a>] &nbsp;&nbsp;&nbsp;&nbsp;
   <?php if ($survey != NULL) { ?>
      [<a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['formsrewrite'].$survey_id; ?>.html" target="_blank"> View survey </a>] &nbsp;&nbsp;&nbsp;&nbsp;
      [<a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=listemails&survey_id=<?php echo $survey_id; ?>&pageLimit=25"> List of survey records </a>] &nbsp;&nbsp;&nbsp;&nbsp;
   <?php } ?>
      
   <BR>

   <table bgcolor="#999999" border="1" cellpadding="3" cellspacing="0"><tr><TD>
   <table bgcolor="WHITE" border="0" cellpadding="5" cellspacing="0">
   <TR valign="TOP">
      <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" name="surveyMaster" method="POST">
      <input type="hidden" name="action" value="survey">
      <input type="hidden" name="survey_id" value="<?php echo $survey_id; ?>">
          <TD>Name:</td><td><input type="text" name="sname" size="105" value="<?php echo $survey['name']; ?>"></td>
        </tr><tr valign="TOP">
          <td>Description:</td><td><textarea name="sinfo" cols="80" rows="12"><?php echo $survey['info']; ?></textarea></td>
        </tr><tr valign="TOP">
          <TD>Storage Type:</td>
          <td>
             <select name="privatesrvy">
             <option value="2">Public form/survey/data entry</option>
             <option value="1" <?php if($survey['privatesrvy'] == 1) print "SELECTED"; ?>>Private form/survey/data entry</option>
             <option value="3" <?php if($survey['privatesrvy'] == 3) print "SELECTED"; ?>>Website Data</option>
             <option value="4" <?php if($survey['privatesrvy'] == 4) print "SELECTED"; ?>>Admin Data</option>
             <option value="5" <?php if($survey['privatesrvy'] == 5) print "SELECTED"; ?>>Other Data</option>
             <option value="6" <?php if($survey['privatesrvy'] == 6) print "SELECTED"; ?>>CSS Feed</option>
             <option value="7" <?php if($survey['privatesrvy'] == 7) print "SELECTED"; ?>>JSON Visual</option>
             <option value="52" <?php if($survey['privatesrvy'] == 52) print "SELECTED"; ?>>Special*</option>
<?php
      $customCode = new AdminUI();
      $opts = $customCode->getCustomSurveyOptions();
      foreach ($opts as $key => $value) {
         print "<option value=\"".$key."\"";
         if ($survey['privatesrvy']==$key) print " SELECTED";
         print ">".$value."</option>\n";
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
                   <option value="1" <?php if($survey['emailresults'] == 1) print "SELECTED"; ?>>Send admin an email when a new record is created.</option>
                   </select>
                </td>
        </tr><tr valign="TOP">
          <TD>Record Data:</td>
                <td>
                   <select name="saveresults">
                   <option value="2">Do not record data</option>
                   <option value="1" <?php if($survey['saveresults'] == 1) print "SELECTED"; ?>>Save data for later retreival.</option>
                   </select>
                </td>
        </tr><tr valign="TOP">
          <TD>Glossary:</td>
                <td>
            <?php
               $glossary = new Glossary($survey['glossaryid']);
               print $glossary->getGlossaryOptions();
            ?>
                </td>
        </tr><tr valign="TOP">
          <td colspan="2" align="RIGHT"><input type="submit" name="submit" value="Submit"></td>
      </form>
   </tr>
   </table>
   </td></tr></table>

<?php if ($survey != NULL) { ?>

<?php if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),12)) { ?>
   <BR>
   <table bgcolor="#999999" border="1" cellpadding="3" cellspacing="0"><tr><TD>
   <table bgcolor="WHITE" border="0" cellpadding="5" cellspacing="0">
      <tr><td colspan="2">Users that have Access to this survey</td></tr>
<?php
      //$accessUsers = $ua->usersAccessible("SURVEY",$survey_id);
      for ($i=0; $i<count($accessUsers); $i++) {
?>

      <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" name="surveyAccess" method="POST">
      <input type="hidden" name="action" value="survey">
      <input type="hidden" name="subaction" value="removeaccess">
      <input type="hidden" name="survey_id" value="<?php echo $survey_id; ?>">
      <input type="hidden" name="userid" value="<?php echo $accessUsers[$i]['userid']; ?>">
          <tr>
          <td><?php echo $accessUsers[$i]['email']; ?></td><TD><input type="submit" name="remove" value="remove"></td>
          </tr>
      </form>

<?php
      }
?>
      <tr><td colspan="2">&nbsp;</td></tr>
      <tr><td colspan="2">Users that do not have Access to this survey</td></tr>
<?php

      //$accessUsers = $ua->usersNotAccessible("SURVEY",$survey_id);
      for ($i=0; $i<count($accessUsers); $i++) {

         if (!$ua->doesUserHaveAccessToLevel($accessUsers[$i]['userid'],2)) {
?>
      <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" name="surveyAccess" method="POST">
      <input type="hidden" name="action" value="survey">
      <input type="hidden" name="subaction" value="addaccess">
      <input type="hidden" name="survey_id" value="<?php echo $survey_id; ?>">
      <input type="hidden" name="userid" value="<?php echo $accessUsers[$i]['userid']; ?>">
          <tr>
            <td><?php echo $accessUsers[$i]['email']; ?></td><TD><input type="submit" name="add" value="add"></td>
          </tr>
      </form>
<?php
         }
      }
   }
?>
   </table>
   </td></tr></table>

<?php
         print "<BR><a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=surveyxml&survey_id=".$survey_id."\">View survey xml structure</a><br>";
         $count = 0;
         $sections = $surveyOBJ->getQuestionSections($survey_id);
         for ($i=0; $i<count($sections); $i++) {
            //$shortLabel= substr($sections[$i]['label'], 0, 33)."...";  
            $shortLabel= "Sect ".$sections[$i]['sequence'];  
            $sectionOList[$shortLabel]=$sections[$i]['section'];
         }

         for ($i=0; $i<count($sections); $i++) {
            $s = $sections[$i];
            $dynamicSection = "";
            if ($s['dyna'] == 1) $dynamicSection = "CHECKED";
            $questions = $surveyOBJ->getQuestions($survey_id, $s['section']);
   ?>
   <BR>
         <a name="section<?php echo $s['section']; ?>"></a>
         <table bgcolor="#999999" border="1" cellpadding="3" cellspacing="0"><tr><TD>
         <table bgcolor="WHITE" border="0" cellpadding="5" cellspacing="0">
            <TR>
            <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php#section<?php echo $s['section']; ?>" name="surveySection<?php echo $s['section']; ?>" method="POST">
           <input type="hidden" name="action" value="survey">
           <input type="hidden" name="survey_id" value="<?php echo $s['survey_id']; ?>">
           <input type="hidden" name="section" value="<?php echo $s['section']; ?>">
            <TD valign="top" style="padding-left:25px;">
              <b><font size="+1">
                  Section <input type="text" size="1" name="sequence" value="<?php echo $s['sequence']; ?>">: 
                  <input type="text" size="90" name="label" value="<?php echo $s['label']; ?>">
              </font></b><br>
              <input type="checkbox" name="dyna" value="1" <?php echo $dynamicSection; ?>>Make this section dynamic: &nbsp;&nbsp;
              <input type="text" name="question" value="<?php echo $s['question']; ?>" size="60">&nbsp;&nbsp;&nbsp;&nbsp;
              <input type="submit" name="Update" value="Update">
                  
                  <?php if (count($questions)==0) { ?>
                     <input type="submit" name="Delete" value="Delete">
                  <?php } ?>

            </TD>
            </form>
            </TR>
   
            <?php if (count($sections)>0) {  ?>
            <TR><TD>
                 <table border="0" cellspacing="0" cellpadding="0">
                        <tr valign="top">
                           <td>Seq&nbsp;&nbsp;</td>
                           <td>Section</td>
                           <td>Form Display Field&nbsp;&nbsp;</td>
                           <td>Field Type&nbsp;&nbsp;</td>
                           <td>Choices/Selections&nbsp;&nbsp;</td>
                           <td>Privacy</td>
                           <td></td>
                           <td></td>
                         </tr>

                  <?php
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
                  
                     for ($j=0; $j<count($questions); $j++) {
                        $q = $questions[$j];
                        $selected = NULL;
                        //$selected[$q['question_type']] = "SELECTED";
                        $sectionDropDown = getOptionList("section", $sectionOList, $q['section']);
                        $privacyDropDown = getOptionList("privacy", $privacyList, $q['privacy']);
                  ?>
                        <TR><TD COLSPAN="8">
                              <hr noshade size="1" color="gray">
                        </TD></TR>

                        <tr valign="top">
                        <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php#section<?php echo $s['section']; ?>" name="surveyQuestion<?php echo $q['question_id']; ?>" method="POST">
                        <input type="hidden" name="action" value="survey">
                        <input type="hidden" name="survey_id" value="<?php echo $q['survey_id']; ?>">
                        <!-- input type="hidden" name="section" value="<?php echo $q['section']; ?>" -->
                        <input type="hidden" name="question_id" value="<?php echo $q['question_id']; ?>">
                        <td>
                          <input type="text" size="1" name="sequence" value="<?php echo $q['sequence']; ?>">&nbsp;&nbsp;  
                        </td><td>
                           <?php echo $sectionDropDown; ?>&nbsp;&nbsp;
                        </td><td>
                          <input type="text" size="25" name="label" value="<?php echo $q['label']; ?>">&nbsp;&nbsp; 
                        </td><td>
                           <?php echo $surveyOBJ->getQuestionOptions("question_type",$q['question_type']); ?>
                           &nbsp;&nbsp;
                        </td><td>
                           <input type="text" size="15" name="question" value="<?php echo $q['question']; ?>"> &nbsp;
                           &nbsp;&nbsp;
                        </td><td>
                           <?php echo $privacyDropDown; ?>&nbsp;&nbsp;
                        </td><td>
                           <input type="submit" name="Update" value="Update">
                        </td>
                        </form>
        
                       <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php#section<?php echo $s['section']; ?>" name="surveyQuestion<?php echo $q['question_id']; ?>" method="POST">
                       <input type="hidden" name="action" value="survey">
                       <input type="hidden" name="survey_id" value="<?php echo $q['survey_id']; ?>">
                       <input type="hidden" name="deleteQuestion" value="1">
                       <input type="hidden" name="question_id" value="<?php echo $q['question_id']; ?>">
                       <td>
                          <input type="submit" name="Delete" value="Delete">
                       </td>
                       </form>
                       </tr>
                  <?php } ?>

                     <TR><TD COLSPAN="8"><hr noshade size="1" color="gray"></TD></TR>

                     <TR valign="TOP" bgcolor="yellow">
                     <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php#section<?php echo $s['section']; ?>" name="surveyMaster" method="POST">
                     <input type="hidden" name="action" value="survey">
                     <input type="hidden" name="survey_id" value="<?php echo $survey_id; ?>">
                     <input type="hidden" name="section" value="<?php echo $s['section']; ?>">
                     <input type="hidden" name="newQuestion" value="1">
                     <td>
                        <input type="text" size="1" name="sequence" value="10">&nbsp;&nbsp;  
                     </td><td>
                        
                     </td><td>
                        <input type="text" size="25" name="label" value="">&nbsp;&nbsp; 
                     </td><td>
                        <?php echo $surveyOBJ->getQuestionOptions("question_type",""); ?>
                        &nbsp;&nbsp;
                     </td><td>
                        <input type="text" size="15" name="question" value=""> &nbsp;
                        &nbsp;&nbsp;
                     </td><td>
                        <?php echo getOptionList("privacy", $privacyList, 0); ?>
                     </td><td>
                        <input type="submit" name="Add" value="Add">
                     </td>
                     <td></td>
                     </form>
                     </tr>
                  </table>
                  </TD></TR>
            <?php } ?>

         </table>
         </td></tr></table>

      <?php } ?>

   <BR><HR>
   <BR>
   <table bgcolor="#999999" border="1" cellpadding="3" cellspacing="0">
   <tr><TD>
      
      <table bgcolor="WHITE" border="0" cellpadding="5" cellspacing="0">
      <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" name="surveyMaster" method="POST">
      <input type="hidden" name="action" value="survey">
      <input type="hidden" name="survey_id" value="<?php echo $survey_id; ?>">
      <input type="hidden" name="newSection" value="1">   
      <TR valign="TOP">
          <TD><h3>Add a New Section To Your Survey:</h3> &nbsp;&nbsp;</td>
          <td><input type="text" name="label" size="50" value=""></td>
      </tr>
      <tr valign="TOP">
          <td colspan="2" align="RIGHT"><input type="submit" name="submit" value="submit"></td>
      </tr>
      </form>
      </table>
   
   </td></tr>
   </table>
<?php } else {  ?>
      
      <br>
      <hr>
      <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" name="surveyXMLCreate" method="POST">
      <input type="hidden" name="action" value="survey">
      <h2>Or create a new survey with XML</h2>
      Paste XML below:<br>
      <textarea rows="30" cols="80" name="xml"></textarea><br>
      <input type="submit" name="submit" value="Create new survey based on XML">
      </form>
<?php } ?>

</td></tr>
</table>

<!-- end: jsfadmin/adminsurvey.php -->
