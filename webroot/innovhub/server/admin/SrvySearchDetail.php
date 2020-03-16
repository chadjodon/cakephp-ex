<?php
//error_reporting(8191);

   $surveyOBJ = new Survey();
if (!$vars['topbottomincluded']) include($GLOBALS['rootDir'].$GLOBALS['adminFolder']."top.php");
?>
    <a href="<?php echo $GLOBALS['baseURLSSL']."jsfadmin/admincontroller.php?action=srvysearchresults&survey_id=".$vars['survey_id']; ?>">Return to list of surveys.</a><br>
<?php
   $surveyObj->printSurvey(null,$vars['srvy_person_id'],$vars['survey_id'],NULL,"","../jsfadmin/SrvySearchDetail.php");
   
if (!$vars['topbottomincluded']) include($GLOBALS['rootDir'].$GLOBALS['adminFolder']."bottom.php");
?>

