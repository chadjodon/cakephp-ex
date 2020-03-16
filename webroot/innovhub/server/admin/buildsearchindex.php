<div style="padding:20px;margin:20px;border:1px solid #BBBBBB;border-radius:8px;font-family:verdana;font-size:14px;color:#4e565a;">

<div style="font-size:18px;font-weight:bold;margin-bottom:10px;">
Building Search Index
</div>

<div style="color:#2e2e2e;font-size:12px;font-weight:normal;margin-bottom:15px;">
Be patient and scroll to the bottom - look for the "Finished." indication at the bottom.
<br><span style="font-size:10px;color:#AAAAAA;"><?php echo date("M/d/Y H:i:s"); ?></span>
</div>

<?php
//error_reporting(E_ALL);
$wd_id = getParameter("wd_id");
$keywordsfield = getParameter("keywordsfield");

$wd = new WebsiteData();
$webdata = $wd->getWebData($wd_id);
?>

<div style="font-weight:bold;margin-bottom:2px;">
<?php echo $webdata['name']; ?>
</div>

<div style="font-size:12px;font-weight:normal;margin-bottom:12px;">
(ID: <?php echo $webdata['wd_id']; ?>)
<br><span style="font-size:10px;color:#AAAAAA;"><?php echo date("M/d/Y H:i:s"); ?></span>
</div>


<div>

<?php
   // This loads the entire index from jdata
   if($webdata['privatesrvy']==10) {
      $wd->indexTableWD($webdata['wd_id'],TRUE);
      
      if (class_exists("CustomSearchIndex")) {
         $customObj = new CustomSearchIndex();
         $customObj->customIndexing($webdata['wd_id'],TRUE);
      }
   } else if($keywordsfield!=NULL) {
      $wd->indexTableSimpleAutosuggest($webdata['wd_id'],$keywordsfield);
   }
?>
</div>

<div style="font-weight:bold;margin-top:12px;margin-bottom:12px;">
<span style="font-size:10px;color:#AAAAAA;font-weight:normal;"><?php echo date("M/d/Y H:i:s"); ?></span>
<br>Finished.
</div>

<div style="margin-top:25px;">
Test the newly built index:
<script language="javascript" type="text/javascript" src="/jsfcode/jsf_search_v2.js"></script>
<div id="testinput"></div>
<script>
jsfsearch_domain = '<?php echo getBaseURL(); ?>';
jsfsearch_testinput('testinput',<?php echo $webdata['wd_id']; ?>);
</script>
</div>

</div>
