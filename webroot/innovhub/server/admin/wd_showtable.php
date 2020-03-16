<style type="text/css">
#jsfwd_tables {padding:6px;}
.jsfwddisptables {float:left;width:350px;height:200px;overflow:auto;padding:4px;margin-right:10px;margin-bottom:10px;border:1px solid #888888;border-radius:3px;}
.jsfwddisptabletitle {font-size:12px;color:#000000;font-weight:bold;background-color:#EFEFEF;border-radius:2px;padding:2px;font-family:arial;}
</style>

<script src="/jsfcode/jsf_websitedata.js"></script>
<div id="jsfwdarea"></div>

<script>
//jsfwd_testing = true;
defaultremotedomain = 'https://www.plasticsmarkets.org/';
//domain = 
//jsf_searchforwd('<?php echo isLoggedOn(); ?>','<?php echo $_SESSION['s_user']['token']; ?>','#admindashboard','','','');
//jsf_searchforwd('<?php echo isLoggedOn(); ?>','<?php echo $_SESSION['s_user']['token']; ?>','#associatedtodb #display','','','');
//jsf_getwdtable_jsonp(wdname,wd_id,domain,userid,token,filterstr,limit,page,maxcol,callback,prefix,xtraurl,orderby,searchflds,testing,skipfiltering,foruserid)
jsf_getwdtable_jsonp('','<?php echo getParameter("wd_id"); ?>','','<?php echo isLoggedOn(); ?>','<?php echo $_SESSION['s_user']['token']; ?>','','',1,6,'','','','','',0,0);
</script>