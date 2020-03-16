<?php
   $wdOBJ = new WebsiteData();
   $wd = $wdOBJ->getWebData(getParameter("wd_id"));
   
   $errorstr = "";
   $subaction = getParameter("subaction");
   if(0==strcmp($subaction,"savedsearch")) {
      $qstr = getParameter("searchuri");
      if(getParameter("filterStr")!=NULL) $qstr .= "&filterStr=".urlencode(getParameter("filterStr"));
      $resultstr = $wdOBJ->savedsearch($wd['wd_id'],getParameter("title"),$qstr,getParameter("descr"),getParameter("operation"));
      if(strlen($resultstr) > 0) {
         $errorstr = "<div style=\"margin:5px;padding:5px;border-radius:3px;border:1px solid #CC5555;background-color:#CC9999;color:#CC5555;\">";
         $errorstr .= $resultstr;
         $errorstr .= "</div>";
      }
   }
   
   $allsearches = $wdOBJ->savedsearch($wd['wd_id']);
?>
<style type="text/css">
.wdsearch_body {font-size:12px;font-family:verdana;color:#2e2e2e;}
.wdsearch_filters {margin:10px 10px 20px 10px;padding:8px;border:1px solid #F1F1F1;border-radius:3px;}
.wdsearch_props {margin:10px 10px 20px 10px;padding:8px;border:1px solid #F1F1F1;border-radius:3px;}
.wdsearch_label {float:left;width:160px;overflow:hidden;margin-right:10px;margin-bottom:5px;}
.wdsearch_value {float:left;width:160px;overflow:hidden;margin-right:10px;margin-bottom:5px;}
.wdsearch_separator{clear:both;margin-bottom:10px;}
.wdsearch_title{color:#333333;font-size:16px;margin-bottom:15px;}
.wdsearch_saved {font-size:10px;font-family:arial;color:#757575;margin:10px 10px 20px 10px;padding:8px;border:1px solid #F1F1F1;border-radius:3px;}
</style>

<div class="wdsearch_body" style="padding:5px;margin:5px;">
<?php echo $errorstr; ?>
<div onclick="location.href='admincontroller.php?action=wd_listrows&wd_id=<?php echo $wd['wd_id']; ?>';" style="color:blue;font-size:8px;cursor:pointer;margin-bottom:5px;">Cancel and return</div>
<div class="wdsearch_title"><?php echo convertBack($wd['name']); ?> Advanced Search</div>

<?php if(count($allsearches)) { ?>
   <div class="wdsearch_saved">
   <div style="font-size:12px;color:#333333;margin-bottom:5px;">Saved Searches</div>
   <?php for($i=0;$i<count($allsearches);$i++) { ?>
      <div style="float:left;margin-right:15px;">
      <span onclick="removesavedsearch('<?php echo $allsearches[$i]['title']; ?>');" style="color:#BB7070;cursor:pointer;font-weight:bold;margin-right:1px;">x</span>
      <span onclick="loadsavedsearchpage('<?php echo convertBack($allsearches[$i]['querystr']); ?>');" style="cursor:pointer;">
         <?php echo $allsearches[$i]['title']; ?>
      </span>
      </div>
   <?php } ?>
   <div style="clear:both;"></div>
   </div>
<?php } ?>

<div class="wdsearch_label">General Search</div>
<div class="wdsearch_value"><input type="text" style="width:150px;" id="jsfwdfilterstrdiv<?php echo $wd['wd_id']; ?>"></div>
<div class="wdsearch_separator"></div>

<div class="wdsearch_label">Record Limit</div>
<div class="wdsearch_value">
<select id="jsfwdlimitstrdiv">
<option value="25">25</option>
<option value="50">50</option>
<option value="100">100</option>
<option value="200">200</option>
</select>
</div>
<div class="wdsearch_separator"></div>


<div style="width:10px;height:20px;overflow:hidden;"></div>

<?php
   $search = $wdOBJ->getSearchFilters($wd['wd_id'],NULL,TRUE,FALSE,(getParameter("includeallfields")==1));
   print $search['filterhtml'];
?>

<div style="margin:10px;">
<?php if(getParameter("includeallfields")!=1) { ?>
   <div style="font-size:8px;margin:10px;color:blue;cursor:pointer;" onclick="reloadthispage(1);">
   show all filters
   </div>
<?php } else { ?>
   <div style="font-size:8px;margin:10px;color:blue;cursor:pointer;" onclick="reloadthispage();">
   show less filters
   </div>
<?php } ?>
</div>



<div id="submitbtn" style="margin:10px;padding:6px;width:120px;text-align:center;font-size:12px;font-family:verdana;border:1px solid #111111;border-radius:4px;cursor:pointer;" onclick="loadresultspage();">
Submit
</div>
<div id="submitloading" style="display:none;margin:10px;padding:6px;">
Loading... please wait
</div>


<div style="margin:20px 10px 50px 10px;">
   Save this search &nbsp; &nbsp; 
   Title: <input type="text" id="ss_title" style="font-size:10px;width:90px;">
   Description: <input type="text" id="ss_descr" style="font-size:10px;width:140px;">
   <span onclick="savesavedsearch(jQuery('#ss_title').val(),jQuery('#ss_descr').val());" style="margin-left:10px;padding:4px 8px 4px 8px;font-size:10px;border-radius:4px;border:1px solid #222222;cursor:pointer;">Save</span>
</div>



<div id="testsubmitbtn" style="margin:10px;padding:6px;width:120px;text-align:center;font-size:12px;font-family:verdana;border:1px solid #111111;border-radius:4px;cursor:pointer;" onclick="submitsearch();">
Test
</div>
<div id="testsubmitloading" style="display:none;margin:10px;padding:6px;">
Loading... please wait
</div>




<div id="searchresults" style="margin-top:25px;"></div>

</div>

<script>
var jsfwd_xtraurl='<?php echo getParameter("searchuri"); ?>';
var jsfwd_filterstr='<?php echo getParameter("filterStr"); ?>';

<?php echo $search['filterinit']; ?>
<?php echo $search['filterget']; ?>

jsfwd_initsearchuri();

function getfullsearchuri(allfields) {
   jsfwd_getsearchuri();
   var uri = '';
   uri += 'admincontroller.php?action=wd_search2';
   uri += '&wd_id=<?php echo $wd['wd_id']; ?>';
   uri += '&filterStr=' + encodeURIComponent(jsfwd_filterstr);
   uri += '&searchuri=' + encodeURIComponent(jsfwd_xtraurl);
   uri += '&includeallfields=';
   if(Boolean(allfields)) uri += '1';
   return uri;
}
   
function reloadthispage(allfields) {
   var uri = getfullsearchuri(allfields);
   location.href=uri;
}

function removesavedsearch(title) {
   if(confirm('Are you sure you wish to delete \"' + title + '\"?')) {
      var uri = getfullsearchuri('<?php echo getParameter("includeallfields"); ?>');
      uri += '&subaction=savedsearch';
      uri += '&operation=delete';
      uri += '&title=' + encodeURIComponent(title);
      location.href=uri;
   }
}

function savesavedsearch(title,descr) {
   var uri = getfullsearchuri('<?php echo getParameter("includeallfields"); ?>');
   uri += '&subaction=savedsearch';
   uri += '&operation=create';
   uri += '&title=' + encodeURIComponent(title);
   uri += '&descr=' + encodeURIComponent(descr);
   location.href=uri;
}

function loadsavedsearchpage(query) {
   var uri = '';
   uri += 'admincontroller.php';
   uri += '?action=wd_listrows';
   uri += '&wd_id=<?php echo $wd['wd_id']; ?>';
   uri += query;
   
   location.href=uri;
}

function loadresultspage() {
   jQuery('#submitbtn').hide();
   jQuery('#submitloading').show();
   jsfwd_getsearchuri();
   var uri = '';
   
   uri += 'admincontroller.php';
   uri += '?action=wd_listrows';
   uri += '&wd_id=<?php echo $wd['wd_id']; ?>';
   uri += '&filterStr=' + encodeURIComponent(jsfwd_filterstr);
   uri += '&pageLimit=' + encodeURIComponent(jQuery('#jsfwdlimitstrdiv').val());
   uri += jsfwd_xtraurl;
   
   location.href=uri;
}

function submitsearch() {
   jQuery('#testsubmitbtn').hide();
   jQuery('#testsubmitloading').show();
   jsfwd_getsearchuri();
   var uri = '';
   
   uri += 'https://www.plasticsmarkets.org/jsfcode/jsoncontroller.php';
   //uri += '?action=searchwdrows';
   uri += '?action=getwdandrows';
   uri += '&adduser=1';

   
   uri += jsfwd_xtraurl;
   uri += '&wd_id=<?php echo $wd['wd_id']; ?>';
   uri += '&filterstr=' + encodeURIComponent(jsfwd_filterstr);
   uri += '&limit=' + encodeURIComponent(jQuery('#jsfwdlimitstrdiv').val());
   
   //alert('JSON URL: ' + uri);
   jsf_json_sendRequest(uri,returnsearch);
}

function returnsearch(jsondata) {
   //alert('return: ' + JSON.stringify(jsondata));
   //alert('return: ' + JSON.stringify(jsondata.results));
   
   var str = '';
   
   if(Boolean(jsondata.results) && jsondata.results.length>0) {
      for(var i=0;i<jsondata.results.length;i++) {
         str += '<div style=\"margin-bottom:20px;\">';
         //str += 'wd_row_id: ' + jsondata.results[i].wd_row_id;
         str += JSON.stringify(jsondata.results[i]);
         str += '</div>';
      }
   } else {   
      if(Boolean(jsondata.rows) && jsondata.rows.length>0) {
         for(var i=0;i<jsondata.rows.length;i++) {
            str += '<div style=\"margin-bottom:20px;\">';
            //str += 'wd_row_id: ' + jsondata.results[i].wd_row_id;
            str += jsondata.rows[i].userid + ': ';
            str += JSON.stringify(jsondata.rows[i]);
            str += '</div>';
         }
      } else {
         str = 'sorry, no results found';
      }
   }
   
   jQuery('#searchresults').html(str);
   
   jQuery('#testsubmitloading').hide();
   jQuery('#testsubmitbtn').show();
   
}
</script>


