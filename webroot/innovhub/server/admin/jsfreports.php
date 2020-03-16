<?php
//error_reporting(E_ALL);
$ua = new UserAcct();
if (!$ua->isUserAdmin(isLoggedOn())) {
   print "<div style=\"margin:10px;padding:10px;font-size:20px;font-weight:bold;\">Error occurred</div>";
} else {
   
   // in case needed for sql
   $wd = new WebsiteData();
   $reportswd = $wd->getWebData("Tools and Widgets Dynamic Reports");
   $reportsqs = $wd->getFieldLabels($reportswd['wd_id'],TRUE,TRUE);
   $datawd = $wd->getWebData("Tools and Widgets Dynamic Reports Data");
   $dataqs = $wd->getFieldLabels($datawd['wd_id'],TRUE,TRUE);
   $query = "SELECT ".$dataqs['ydisp']." as ydisp";
   $query .= ", ".$dataqs['xdisp']." as xdisp";
   $query .= ", ".$dataqs['val']." as val";
   $query .= " FROM wd_".$datawd['wd_id'];
   $query .= " WHERE externalid=\\'".$reportswd['wd_id']."_".$reportsqs['simpledata']."_%%%rowid%%%\\' ";
   $query .= " AND LOWER(".$dataqs['enabled'].")=\\'yes\\' ";
   $query .= " ORDER BY ".$dataqs['sequence'];

   $reportid = getParameter("reportid");
   $toolid = getParameter("toolid");
   $htag = getParameter("htag");
   $sqlquery = str_replace("\n"," ",convertBack(getParameter("sqlquery")));
   $subject = getParameter("subject");
   //$subaction = getParameter("subaction");
   
   $backlink = getParameter("backlink");
   if($backlink==NULL) {
      $backid = $toolid;
      if($backid==NULL) $backid=$htag;
      $backlink = "admincontroller.php?action=jsftools&toolid=".$backid;
   }
   
   //if(0==strcmp($subaction,"")) {
   //}
?>

<div onclick="location.href='<?php echo $backlink; ?>';" style="margin:5px;font-size:10px;color:blue;cursor:pointer;">
&lt; Back
</div>

<form action="admincontroller.php" method="post">
<input type="hidden" name="action" value="jsfreports">
<input type="hidden" name="sqlquery" id="sqlquery" value="">
<input type="hidden" name="reportid" value="<?php echo $reportid; ?>">
<input type="hidden" name="toolid" value="<?php echo $toolid; ?>">
<div id="jsfr_search"></div>
</form>
<div id="jsfr_graph"></div>
<div id="jsfr_results" style="max-height:300px;overflow-y:auto;margin:5px;padding:5px;border:1px solid #E2E2E2;border-radius:3px;">
<?php
   // print output if a query was sent in to this script
   if($sqlquery !=NULL) {
      $runquery = trim($sqlquery);
      if(0==strcmp(substr($runquery,(strlen($runquery)-1),1),";")) $runquery = substr($runquery,0,(strlen($runquery)-1));
	   if(strpos(strtolower($runquery)," limit ")===FALSE) $runquery .= " LIMIT 0,50";
	   print "\n<!-- SQL Query: ".$sqlquery." -->\n<!--running query: ".$runquery." -->\n";
	   $sql = new MYSQLaccess();
	   $results = $sql->queryGetResults($runquery);
	   
	   if($results!=NULL && count($results)>0) {
		   if(getParameter("getcsv")==1) {
		     if($subject==NULL) $subject = "Report: ".$reportid.", ".$toolid.", ".$htag;
			  $obj = new ScheduledSQLCSV();
			  $obj->createJob($sqlquery,$subject,1);
			  print "<div style=\"margin:12px;padding:1px;color:darkgreen;font-size:14px;\">Your job was scheduled.</div>";
		   }
	
		   print "<table cellpadding=\"5\" cellspacing=\"1\">";
		   $countindex = NULL;
		   $countwidth = 250;
		   print "<tr style=\"background-color:#EDEDED;\">";
		   foreach($results[0] as $key => $val) {
			  if(0==strcmp(strtolower($key),"total_clicks") || 0==strcmp(strtolower($key),"count(*)") || 0==strcmp(strtolower($key),"total")) {
				 $countindex = $key;
			  } else {
				 print "<td>".$key."</td>";
			  }
		   }
		   if($countindex!=NULL) print "<td>".$countindex."</td>";
		   print "</tr>";
		   
		   $countmax = 0;
		   if($countindex!=NULL) {
			  for($i=0;$i<count($results);$i++) {
				 if($results[$i][$countindex]>$countmax) $countmax = $results[$i][$countindex];
			  }
		   }
		   
		   for($i=0;$i<count($results);$i++) {
			  $bg = '#FFFFFF';
			  if(($i%2)==1) $bg = '#F6FBFF';
			  print "<tr style=\"background-color:".$bg.";\">";
			  foreach($results[$i] as $key => $val) {
				 if($countindex==NULL || 0!=strcmp($key,$countindex)) print "<td>".$val."</td>";
			  }
			  if($countindex!=NULL) {
				 print "<td><div style=\"position:relative;z-index:1;width:".$countwidth."px;height:20px;overflowhidden;\">";
				 print "<div style=\"position:absolute;left:2px;top:2px;color:#000000;font-weight:bold;z-index:3;\">".$results[$i][$countindex]."</div>";
				 if($countmax>0) print "<div style=\"position:absolute;z-index:2;background-color:#AAAAFF;left:0px;top:0px;height:20px;width:".round(($results[$i][$countindex]/$countmax) * $countwidth)."px;overflow:hidden;\"></div>";
				 print "</div></td>";
			  }
			  print "</tr>";
		   }
		   print "</table>";
	   }
	   
   }
?>
</div>


<div style="margin-top:25px;margin-bottom:25px;padding:15px;border-top:1px solid #dedede;" id="jsfr_listsavedsearches"></div>

<script src="/jsfcode/calendar.js"></script>
<script src="/jsfcustomcode/pm_pricegraph.js"></script>
<script>

   var jsfr_toolid = '<?php echo $toolid; ?>';
   var jsfr_jsonurl;
   var jsfr_report;
   var jsfr_searchinput;
   var jsfr_dynajson;
   var jsfr_dynajson_rev;
   
   var jsfr_params;
   var jsfr_params_query;
   
   // Step 1, get the general report to run based on paramter sent in to this php file
   function jsfr_initreports() {
   	   
   	   jsfr_params = {};
   	   jsfr_params_query = '';
   <?php
      $allparams = getParameters($toolid."_");
      foreach($allparams as $key => $val) {
         if($key!=NULL && $val!=NULL) {
            print "jsfr_params.".$key."='".$val."';\n";
            print "jsfr_params_query += '&".$key."=' + encodeURIComponent('".$val."');\n";
         }
      }
   ?>
   
        var url = '<?php echo getBaseURL(); ?>jsfcode/jsoncontroller.php?action=getwdandrows';
        url += '&userid=<?php echo isLoggedOn(); ?>&token=<?php echo $_SESSION['s_user']['token']; ?>';
        url += '&wd_id=' + encodeURIComponent('Tools and Widgets Dynamic Reports');
        url += '&cmsenabled=1';
        url += '&maxcol=20';
        url += '&cmsrowid=<?php echo $reportid; ?>';
        //url += '&cmsz_toolsandwidgetsdynamicreports_hashtags=' + ;
        //alert('url: ' + url);
        jsf_json_sendRequest(url,jsfr_returnreport);
   }
   
   
   // Step 2: query the individual search parameters
   function jsfr_returnreport(jsondata) {
      if(Boolean(jsondata.rows) && jsondata.rows.length>0) {
         jsfr_report = jsondata.rows[0];
         
         if(jsfr_report.type=='Website Data') {
            jsfr_report.json = 'jsfcode/jsoncontroller.php?action=getwdreport';
            jsfr_report.json += '&wd_id=' + encodeURIComponent(jsfr_report.wdparam);
            if(Boolean(jsfr_report.groupparam)) jsfr_report.json += '&groupby=' + encodeURIComponent(jsfr_report.groupparam);
            if(Boolean(jsfr_report.avgfld)) jsfr_report.json += '&avgfld=' + encodeURIComponent(jsfr_report.avgfld);
            if(Boolean(jsfr_report.orderparam)) jsfr_report.json += '&orderby=' + encodeURIComponent(jsfr_report.orderparam);
            if(Boolean(jsfr_report.addlwhere)) jsfr_report.json += '&addlwhere=' + encodeURIComponent(jsfr_report.addlwhere);
            if(Boolean(jsfr_report.addlselect)) jsfr_report.json += '&addlselect=' + encodeURIComponent(jsfr_report.addlselect);
         } else if(jsfr_report.type=='Simple Data') {
            jsfr_report.sql = jsfr_replaceAll('%%%rowid%%%',jsfr_report.wd_row_id,'<?php echo $query; ?>');
         }
         
         var url = '<?php echo getBaseURL(); ?>jsfcode/jsoncontroller.php?action=getwdandrows';
         url += '&userid=<?php echo isLoggedOn(); ?>&token=<?php echo $_SESSION['s_user']['token']; ?>';
         url += '&wd_id=' + encodeURIComponent('Tools and Widgets Dynamic Report Search');
         url += '&o_wd_id=' + encodeURIComponent('Tools and Widgets Dynamic Reports');
         url += '&o_field_id=parameters';
         url += '&o_wd_row_id=' + jsfr_report.wd_row_id;
         url += '&cmsenabled=1';
         url += '&maxcol=20';
         //alert('url: ' + url);
         jsf_json_sendRequest(url,jsfr_setupusersearchform);
         
      }
   }
   
   // Get the saved searches for this report (once report is loaded)
   function jsfr_getsavedsearches() {
         var url = '<?php echo getBaseURL(); ?>jsfcode/jsoncontroller.php?action=getwdandrows';
         url += '&userid=<?php echo isLoggedOn(); ?>&token=<?php echo $_SESSION['s_user']['token']; ?>';
         url += '&wd_id=' + encodeURIComponent('Tools and Widgets Dynamic Reports Saved');
         url += '&o_wd_id=' + encodeURIComponent('Tools and Widgets Dynamic Reports');
         url += '&o_field_id=savedsearch';
         url += '&o_wd_row_id=' + jsfr_report.wd_row_id;
         url += '&cmsenabled=1';
         url += '&maxcol=20';
         //alert('url: ' + url);
         jsf_json_sendRequest(url,jsfr_setupsavedsearches);
   }
   
   function jsfr_setupsavedsearches(jsondata) {
      var str = '';
      if(Boolean(jsondata.rows) && jsondata.rows.length>0) {
         //alert('you have saved searches for this report, coming soon.');
         str += '<div style=\"font-size:14px;font-weight:bold;color:#2e2e2e;\">Saved Searches:</div>';
         for(var i=0;i<jsondata.rows.length;i++) {
            var r = jsondata.rows[i];
            str += '<div style=\"margin-bottom:5px;font-size:12px;font-family:verdana;\">';
            str += '<div onclick=\"location.href=\'admincontroller.php?action=jsfreports&reportid=<?php echo $reportid; ?>&toolid=<?php echo $toolid; ?>&htag=<?php echo $htag; ?>' + r.parameters + '\';\" style=\"float:left;width:200px;overflow-x:hidden;margin-right:10px;cursor:pointer;color:blue;\">';
            str += r.subject;
            str += '</div>';
            str += '<div onclick=\"if(confirm(\'Permanently delete this saved search?\')) jsfr_removesavedsearch(' + r.wd_row_id + ');\" style=\"float:left;cursor:pointer;color:red;\">';
            str += 'remove';
            str += '</div>';
            str += '<div style=\"clear:both;\"></div>';
            str += '</div>';
         }
      } else {
         //alert('you have no saved searches for this report yet.');
         str += '<div style=\"padding:10px;font-size:10px;font-decoration:italics;color:#656565;\">No Saved searches to display.</div>';
      }
      jQuery('#jsfr_listsavedsearches').html(str);
   }
   
   function jsfr_removesavedsearch(wd_row_id) {
      jQuery('#jsfr_listsavedsearches').html('Loading...');
      var url = '<?php echo getBaseURL(); ?>jsfcode/jsoncontroller.php?action=deletesinglewdrow';
      url += '&userid=<?php echo isLoggedOn(); ?>&token=<?php echo $_SESSION['s_user']['token']; ?>';
      url += '&wd_id=' + encodeURIComponent('Tools and Widgets Dynamic Reports Saved');
      url += '&wd_row_id=' + wd_row_id;
      //alert('url: ' + url);
      jsf_json_sendRequest(url,jsfr_return_removesavedsearch);
   }
   
   function jsfr_return_removesavedsearch(jsondata){
      jsfr_getsavedsearches();
   }

   
   // Step 3: print out the input for searching
   function jsfr_setupusersearchform(jsondata) {
      var jsoncalls = [];
      var jsonoptcalls = [];
      var str = '';
      str += '<div style=\"margin-top:10px;padding-top:10px;border-top:1px solid #F0F0F0;color:#333333;font-size:12px;font-family:verdana;\">';
      str += '<div>';
      str += '<div style=\"margin-bottom:5px;font-size:16px;color:#2e2e2e;font-weight:bold;\">' + jsfr_report.name + '</div>';
      str += '<div style=\"margin-bottom:15px;font-size:12px;color:#777777;\">' + jsfr_report.description + '</div>';
      
      var js = '';
      //alert('sql: ' + jsfr_report.sql);
      js += 'function jsfr_search(sendemail){\n';
      js += '  jsfr_params_query = \'\';\n';
      js += '  var val;\n';
      js += '  var val2;\n';
      js += '  var e;\n';
      js += '  var msg;\n';
      js += '  var retval = true;\n';
      js += '  var sql = jsfr_report.sql;\n';
      js += '  jsfr_jsonurl = \'\';\n';
      js += '  var url = \'<?php echo getBaseURL(); ?>\' + jsfr_report.json;\n';
      js += '  url += \'&userid=<?php echo isLoggedOn(); ?>\';\n';
      js += '  url += \'&token=<?php echo $_SESSION['s_user']['token']; ?>\';\n';
      js += '  url += \'&divid=jsfr_results\';\n';
      
      //build dependency lists
      jsfr_searchinput = {};
      jsfr_dynajson = {};
      jsfr_dynajson_rev = {};
      for(var j=0;j<jsondata.rows.length;j++) {
        var tempobjdivid = jsfr_toolid + '_' + jsondata.rows[j].param;
        //alert('tempid: ' + tempobjdivid);
        jsfr_searchinput[tempobjdivid] = jsondata.rows[j];
        if(Boolean(jsondata.rows[j].dependencies)) {
          var deps = jsondata.rows[j].dependencies.split(';');
          for(var k=0;k<deps.length;k++){
            var dinfo = deps[k].split(',');
            if(!Boolean(jsfr_dynajson[tempobjdivid])) jsfr_dynajson[tempobjdivid] = [];
            var tempobj = {};
            tempobj.relto = dinfo[0];
            tempobj.param = dinfo[1];
            
            for(var m=0;m<jsondata.rows.length;m++) {
              if(dinfo[0]==jsondata.rows[m].param) {
                var tempobjdivid2 = jsfr_toolid + '_' + jsondata.rows[m].param;
                tempobj.divid = tempobjdivid2;
                tempobj.json = jsondata.rows[m].json;
                if(!Boolean(jsfr_dynajson_rev[tempobjdivid2])) jsfr_dynajson_rev[tempobjdivid2] = [];
                
                var tempobj2 = {};
                tempobj2.divid = tempobjdivid;
                tempobj2.param = dinfo[1];
                jsfr_dynajson_rev[tempobjdivid2].push(tempobj2);
                break;
              }
            }
            jsfr_dynajson[tempobjdivid].push(tempobj);               
          }
        }
      }
      
      //create form input elements on page
      for(var j=0;j<jsondata.rows.length;j++) {
         if(Boolean(jsondata.rows[j].name)) {
            js += 'val = jQuery(\'#' + jsfr_convertid(jsfr_toolid + '_' + jsondata.rows[j].param) + '\').val();\n';
            if(Boolean(jsondata.rows[j].required) && jsondata.rows[j].required.toLowerCase()=='yes') {
               js += 'if(!Boolean(val)) {\n';
               js += '  e=true;\n';
               js += '  msg = \'Please enter a value for ' + jsondata.rows[j].name + '\';\n';
               js += '}\n';
            }
            //js += ' alert(\'val: \' + val);\n';
            js += 'if(Boolean(val)) {\n';
            js += '   jsfr_params_query += \'&' + jsondata.rows[j].param + '=\' + encodeURIComponent(val);\n';
            js += '   url += \'&' + jsondata.rows[j].param + '=\' + encodeURIComponent(val);\n';
            var stmnt = ' AND ' + jsondata.rows[j].param + '=\\\'' + jsondata.rows[j].prefix + '\' + val.trim() + \'\\\'';
      
            str += '<div style=\"margin-top:8px;\">';
            str += '<div style=\"float:left;width:150px;\">' + jsondata.rows[j].name + '</div>';
            str += '<div id=\"formdiv_' + jsfr_convertid(jsfr_toolid + '_' + jsondata.rows[j].param) + '\" style=\"float:left;\">';
            if(jsondata.rows[j].type=='text') {
               stmnt = ' AND LOWER(' + jsondata.rows[j].param + ') LIKE \\\'%' + jsondata.rows[j].prefix + '\' + val.trim().toLowerCase() + \'%\\\'';
               str += '<input type=\"text\"';
               str += ' name=\"' + jsfr_convertid(jsfr_toolid + '_' + jsondata.rows[j].param) + '\"';
               str += ' id=\"' + jsfr_convertid(jsfr_toolid + '_' + jsondata.rows[j].param) + '\"';
               str += ' style=\"width:190px;\"';
               if(Boolean(jsfr_params[jsfr_convertid(jsfr_toolid + '_' + jsondata.rows[j].param)])) {
                 str += ' value=\"' + jsfr_params[jsfr_convertid(jsfr_toolid + '_' + jsondata.rows[j].param)] + '\"';
               }
               str += '>';
            } else if(jsondata.rows[j].type=='int') {
               stmnt = ' AND ' + jsondata.rows[j].param + '=\\\'\' + val.trim() + \'\\\'';
               str += '<input type=\"text\"';
               str += ' name=\"' + jsfr_convertid(jsfr_toolid + '_' + jsondata.rows[j].param) + '\"';
               str += ' id=\"' + jsfr_convertid(jsfr_toolid + '_' + jsondata.rows[j].param) + '\"';
               str += ' style=\"width:90px;\"';
               if(Boolean(jsfr_params[jsfr_convertid(jsfr_toolid + '_' + jsondata.rows[j].param)])) {
                 str += ' value=\"' + jsfr_params[jsfr_convertid(jsfr_toolid + '_' + jsondata.rows[j].param)] + '\"';
               }
               str += '>';
            } else if(jsondata.rows[j].type=='opts') {
               js += 'var tempopts = val.trim().split(\';\');\n';
               js += 'var optsval = \'\';\n';
               js += 'for(var i=0;i<tempopts.length;i++){\n';
               js += '  if(i>0) optsval += \' OR \';\n';
               js += '  optsval += \'' + jsondata.rows[j].param + '=\\\'\' + tempopts[i].trim() + \'\\\'\';\n';
               js += '}\n';
               stmnt = ' AND (\' + optsval + \')';
               str += '<input type=\"hidden\"';
               str += ' name=\"' + jsfr_convertid(jsfr_toolid + '_' + jsondata.rows[j].param) + '\"';
               str += ' id=\"' + jsfr_convertid(jsfr_toolid + '_' + jsondata.rows[j].param) + '\"';
               if(Boolean(jsfr_params[jsfr_convertid(jsfr_toolid + '_' + jsondata.rows[j].param)])) {
                 str += ' value=\"' + jsfr_params[jsfr_convertid(jsfr_toolid + '_' + jsondata.rows[j].param)] + '\"';
               }
               str += '>';
               str += '<div>';
               var tempopts = jsondata.rows[j].values.split(',');
               for(var i=0;i<tempopts.length;i++){
                  str += '<div style=\"float:left;margin-right:10px;width:120px;height:18px;overflow:hidden;\">';
                  str += '<input type=\"checkbox\"';
                  str += ' name=\"cb_' + jsfr_convertid(jsfr_toolid + '_' + jsondata.rows[j].param) + '\"';
                  str += ' id=\"cb_' + jsfr_convertid(jsfr_toolid + '_' + jsondata.rows[j].param) + '\"';
                  str += ' value=\"' + tempopts[i].trim() + '\"';
                  str += ' onchange=\"setcheckboxvalues(\'cb_' + jsfr_convertid(jsfr_toolid + '_' + jsondata.rows[j].param) + '\',\'' + jsfr_convertid(jsfr_toolid + '_' + jsondata.rows[j].param) + '\');\"';
                  var ck = '';
                  if(Boolean(jsfr_params[jsfr_convertid(jsfr_toolid + '_' + jsondata.rows[j].param)])) {
                     var valarr = jsfr_params[jsfr_convertid(jsfr_toolid + '_' + jsondata.rows[j].param)].split(';');
                     for(var j=0;j<valarr.length;j++) {
                        if(valarr[j].trim()==tempopts[i].trim()) ck = ' CHECKED';
                     }
                  }
                  str += ck + '>' + tempopts[i].trim() + '</div>';
               }
               str += '<div style=\"clear:both;\"></div>';
               str += '</div>';
            } else if(jsondata.rows[j].type=='date') {
               stmnt = ' AND ' + jsondata.rows[j].param + '>=\\\'\' + val.trim() + \'\\\'';
               str += showCalendarInput(jsfr_convertid(jsfr_toolid + '_' + jsondata.rows[j].param),jsfr_params[jsfr_convertid(jsfr_toolid + '_' + jsondata.rows[j].param)],'',1);
               if(jsfr_report.type=='Database') {
                  js += 'val2 = jQuery(\'#' + jsfr_convertid(jsfr_toolid + '_' + jsondata.rows[j].param) + '_end\').val();\n';
                  if(Boolean(jsondata.rows[j].required) && jsondata.rows[j].required.toLowerCase()=='yes') {
                    js += 'if(!Boolean(val2)) {\n';
                    js += '  e=true;\n';
                    js += '  msg = \'Please enter a value for ' + jsondata.rows[j].name + ' (end)\';\n';
                    js += '}\n';
                  }
                  js += 'if(Boolean(val2)) {\n';
                  //js += '   url += \'&' + jsondata.rows[j].param + '=\' + encodeURIComponent(val);\n';
                  var stmnt2 = ' AND ' + jsondata.rows[j].param + '<=\\\'\' + val2.trim() + \'\\\'';
                  js += '   sql = jsfr_addstatment(sql,\'' + stmnt2 + '\');\n';
                  js += '   }\n';
                  str += showCalendarInput(jsfr_convertid(jsfr_toolid + '_' + jsondata.rows[j].param) + '_end',jsfr_params[jsfr_convertid(jsfr_toolid + '_' + jsondata.rows[j].param) + '_end'],'',1);
               }
           } else if(jsondata.rows[j].type=='bool') {
               var t_yes = '';
               var t_no = '';
               if(Boolean(jsfr_params[jsfr_convertid(jsfr_toolid + '_' + jsondata.rows[j].param)])) {
                 if(jsfr_params[jsfr_convertid(jsfr_toolid + '_' + jsondata.rows[j].param)]=='YES') t_yes = ' SELECTED';
                 else if(jsfr_params[jsfr_convertid(jsfr_toolid + '_' + jsondata.rows[j].param)]=='NO') t_no = ' SELECTED';					  
               }
               str += '<select name=\"' + jsfr_convertid(jsfr_toolid + '_' + jsondata.rows[j].param) + '\" id=\"' + jsfr_convertid(jsfr_toolid + '_' + jsondata.rows[j].param) + '\" style=\"\">';
               str += '<option value=\"\"></option>';
               str += '<option value=\"YES\"' + t_yes + '>Yes</option>';
               str += '<option value=\"NO\"' + t_no + '>No</option>';
               str += '</select>';
            } else if(jsondata.rows[j].type=='json opts') {
               js += 'var tempopts = val.trim().split(\';\');\n';
               js += 'var optsval = \'\';\n';
               js += 'for(var i=0;i<tempopts.length;i++){\n';
               js += '  if(i>0) optsval += \' OR \';\n';
               js += '  optsval += \'' + jsondata.rows[j].param + '=\\\'\' + tempopts[i].trim() + \'\\\'\';\n';
               js += '}\n';
               stmnt = ' AND (\' + optsval + \')';
                              
               var tempurl = jsondata.rows[j].json;
               tempurl += '&divid=' + jsfr_toolid + '_' + jsondata.rows[j].param;
               //alert('pushing: ' + tempurl);
               jsonoptcalls.push(tempurl);
               
            } else if(jsondata.rows[j].type=='json') {
               //stmnt = ' AND LOWER(' + jsondata.rows[j].param + ') LIKE \\\'%' + jsondata.rows[j].prefix + '\' + val.trim().toLowerCase() + \'%\\\'';
               stmnt = ' AND LOWER(' + jsondata.rows[j].param + ')=\\\'' + jsondata.rows[j].prefix + '\' + val.trim().toLowerCase() + \'\\\'';
               //This is NOT a jsonp... just jsoncontroller.php
               var tempurl = jsondata.rows[j].json;
               tempurl += '&divid=' + jsfr_toolid + '_' + jsondata.rows[j].param;
               //alert('pushing: ' + tempurl);
               jsoncalls.push(tempurl);
            }
            str += '</div>';
            str += '<div style=\"clear:both;\"></div>';
            str += '</div>';
            
            js += 'sql = jsfr_addstatment(sql,\'' + stmnt + '\');\n';
            //js += 'alert(\'SQL after stmnt: \' + sql);\n';
            js += '}\n';
         }
      }
      
      if(jsfr_report.forusers.toLowerCase()=='yes') {
         js += 'if(Boolean(sendemail)) {\n';
         js += '  var cmsid = jQuery(\'#form_' + jsfr_convertid(jsfr_toolid) + '_cmsid\').val();\n';
         js += '  if(Boolean(cmsid)) {\n';
         js += '    url += \'&sendemail=1\';\n';
         js += '    url += \'&cmsid=\' + cmsid;\n';
         js += '  } else {\n';
         js += '    alert(\'Please select content and set the from email address before sending email.\');\n';
         js += '  }\n';
         js += '}\n';
         
         str += '<div style=\"margin-top:8px;\">';
         str += '<div style=\"float:left;width:150px;\">Email</div>';
         str += '<div id=\"formdiv_' + jsfr_convertid(jsfr_toolid) + '_cmsid\" style=\"float:left;\">';
         str += '<select id=\"form_' + jsfr_convertid(jsfr_toolid) + '_cmsid\" style=\"\">';
         str += '<option value=\"\"></option>';
         <?php
         $ss = new Version();
         $shortcuts = $ss->getAllShortcuts(6);
         for ($i=0; $i<count($shortcuts); $i++) {
         ?>
         str += '<option value=\"<?php echo $shortcuts[$i]['cmsid']; ?>\">';
         str += '<?php echo $shortcuts[$i]['title']." (".$shortcuts[$i]['filename'].")"; ?>';
         str += '</option>';
         <?php
         }
         ?>
         str += '</select>';
         str += '</div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
      }
         
      //js += 'alert(\'url: \' + url);\n';
      js += 'if(Boolean(e)) {\n';
      js += '  retval = false;\n';
      js += '  alert(msg);\n';
      js += '} else {\n';
      js += '  jQuery(\'#jsfr_results\').html(\'Loading...\');\n';
      //js += '  alert(\'url: \' + url);\n';
      if(jsfr_report.type=='Database' || jsfr_report.type=='Simple Data') {
         js += '  jQuery(\'#sqlquery\').val(sql);\n';
      } else if(jsfr_report.type=='JSON' || jsfr_report.type=='Website Data') {
         if(jsfr_report.countonly.toLowerCase()=='yes') js += '   url += \'&countonly=1\';\n';
         js += '  jsfr_jsonurl = url;\n';
         //js += '  alert(\'url: \' + url);\n';
         js += '  jsf_json_sendRequest(url,jsfr_searchresults);\n';
      }
      js += '}\n';
      
      js += 'return retval;\n';
      js += '}\n';
      
      str += '<div style=\"margin-top:4px;margin-bottom:2px;\">';
      
      // Ask if user would like a CSV download
      str += '<div>';
      str += '<input onclick=\"jsfr_checkbox();\" type=\"checkbox\" id=\"jsfr_getcsv\" name=\"getcsv\" value=\"1\"> Get CSV download';
      str += '</div>';
      str += '<div>';
      str += '<input onclick=\"jsfr_checkbox();\" type=\"checkbox\" id=\"jsfr_savesearch\" name=\"savesearch\" value=\"1\"> Save this search';
      str += '</div>';
      str += '<div id=\"jsfr_subject\" style=\"position:relative;display:none;\">';
      str += '<div id=\"jsfr_subject_lbl\" style=\"float:left;margin-right:20px;\">Subject:</div>';
      str += '<div style=\"float:left;margin-right:20px;\"><input type=\"text\" id=\"jsfr_subject_fld\" name=\"subject\" value=\"<?php echo $subject; ?>\" style=\"width:190px;font-size:14px;font-family:arial;\"></div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
      if(jsfr_report.type=='Database' || jsfr_report.type=='Simple Data') {
          str += '<input type=\"submit\" name=\"Submit\" value=\"Submit\" onclick=\"return jsfr_search();\">';
      } else {
         str += '<div onclick=\"jsfr_search();\" style=\"float:left;cursor:pointer;border:1px solid #777777;background-color:#F1F1F1;border-radius:4px;padding:4px;width:100px;text-align:center;margin:8px;\">Search</div>';
         if(jsfr_report.forusers.toLowerCase()=='yes') str += '<div onclick=\"jsfr_search(1);\" style=\"float:left;cursor:pointer;border:1px solid #777777;background-color:#F1F1F1;border-radius:4px;padding:4px;width:100px;text-align:center;margin:8px;\">Send Email</div>';
      }
      
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      str += '</div>';
      str += '</div>';
      str += '\n<scr';
      str += 'ipt>\n' + js + '\n</sc';
      str += 'ript>\n';
      //alert('str: ' + str);
      
      jQuery('#jsfr_search').html(str);
      
      //alert('jsons to call: ' + jsoncalls.length);
      //alert('json obj: ' + JSON.stringify(jsfr_dynajson));
      while(jsoncalls.length>0) {
         var query = jsoncalls.shift();
         //alert('calling: ' + query);
         jsf_json_sendRequest('<?php echo getBaseURL(); ?>' + query,jsfr_formdropdown);         
      }
      
      while(jsonoptcalls.length>0) {
         var query = jsonoptcalls.shift();
         //alert('calling: ' + query);
         jsf_json_sendRequest('<?php echo getBaseURL(); ?>' + query,jsfr_formcheckboxes);         
      }
      
<?php
   // if this was a database query submitted only
   if(getParameter("savesearch")==1) {
      print "jsfr_savesearch();";
   } else {
      print "jsfr_getsavedsearches();";      
   }
?>
    
  }
  
  function setcheckboxvalues(nm,id) {
      var output = jQuery.map(jQuery(":checkbox[name='" + nm + "']:checked"), function(n, i){
            return n.value;
      }).join(';');
      jQuery('#' + id).val(output);
  }
  
  // Dynamically check if subject is required on form
  function jsfr_checkbox() {
     if(document.getElementById('jsfr_getcsv').checked || document.getElementById('jsfr_savesearch').checked) {
        jQuery('#jsfr_subject').show();
     } else {
        jQuery('#jsfr_subject').hide();
     }
  }
  
   // Save a search dynamically
   function jsfr_savesearch(query,subj) {
      var now = new Date();
      if(!Boolean(subj)) subj = jQuery('#jsfr_subject_fld').val();
      if(!Boolean(subj)) subj = 'Saved Search ' + now.getFullYear() + '_' + (now.getMonth() + 1) + '_' + now.getDay() + '_' + now.getHour() + now.getMinute();
      
      if(!Boolean(query)) query = jsfr_params_query;
      
      var url = '<?php echo getBaseURL(); ?>jsfcode/jsoncontroller.php?action=submitwebdata';
      url += '&userid=<?php echo isLoggedOn(); ?>&token=<?php echo $_SESSION['s_user']['token']; ?>';
      url += '&wd_id=' + encodeURIComponent('Tools and Widgets Dynamic Reports Saved');
      url += '&o_wd_id=' + encodeURIComponent('Tools and Widgets Dynamic Reports');
      url += '&o_field_id=savedsearch';
      url += '&o_wd_row_id=' + jsfr_report.wd_row_id;
      url += '&parameters=' + encodeURIComponent(query);
      url += '&subject=' + encodeURIComponent(subj);
      jsf_json_sendRequest(url,jsfr_savesearch_return);      
   }
   
   // once save has finished, refresh screen
   function jsfr_savesearch_return() {
      alert('Your search was saved.');
      jsfr_getsavedsearches();
   }
   
   function jsfr_runcsv_return() {
      alert('Your csv was submitted.');
   }
   

   // Step 4: populate the dynamic dropdowns (for init and dependent dropdowns)
   function jsfr_formdropdown(jsondata){
      //alert('2nd json obj: ' + JSON.stringify(jsfr_dynajson));
      //alert('dropdown results: ' + JSON.stringify(jsondata));
      var str = '';
      str += '<select name=\"' + jsfr_convertid(jsondata.divid) + '\" id=\"' + jsfr_convertid(jsondata.divid) + '\" ';
      //alert('divid: ' + jsondata.divid);
      // complexity if there is another dropdown dependent on this...
      if(Boolean(jsfr_dynajson[jsondata.divid])) {
         str += 'onchange=\"';
         
         for (var n=0;n<jsfr_dynajson[jsondata.divid].length;n++) {
           var tempurl = '';
           tempurl += 'jsf_json_sendRequest(\'<?php echo getBaseURL(); ?>';
           tempurl += jsfr_dynajson[jsondata.divid][n].json;
           tempurl += '&divid=' + jsfr_dynajson[jsondata.divid][n].divid;
           for(var o=0;o<jsfr_dynajson_rev[jsfr_dynajson[jsondata.divid][n].divid].length;o++) {
             tempurl += '&' + jsfr_dynajson_rev[jsfr_dynajson[jsondata.divid][n].divid][o].param + '=';
             tempurl += '\' + encodeURIComponent(jQuery(\'#' + jsfr_convertid(jsondata.divid) + '\').val())';
           }
           tempurl += ',jsfr_formdropdown);';
           //alert('tempurl: ' + tempurl);
           str += tempurl;
         }
         
         str += '\" ';
      }
      
      str += '>';
      str += '<option value=\"\"></option>';
      //alert('divid: ' + jsondata.divid + ' jsfr_searchinput: ' + JSON.stringify(jsfr_searchinput));
      //alert('divid: ' + jsondata.divid);
      var t_check = Boolean(jsfr_params[jsfr_convertid(jsfr_toolid + '_' + jsfr_searchinput[jsondata.divid].param)]);
      for(var i=0;i<jsondata.rows.length;i++) {
         // check if this is the option previously selected
         var t_sel = '';
         
         var name = '';
         if(Boolean(jsondata.rows[i].name)) name = jsondata.rows[i].name;
         else if(Boolean(jsondata.rows[i].title)) name = jsondata.rows[i].title;
         else if(Boolean(jsondata.rows[i].value)) name = jsondata.rows[i].value;
         
         var val = '';
         if(Boolean(jsondata.rows[i].wd_row_id)) val = jsondata.rows[i].wd_row_id;
         else if(Boolean(jsondata.rows[i].value)) val = jsondata.rows[i].value;
         else if(Boolean(jsondata.rows[i].val)) val = jsondata.rows[i].val;
         else val = name;
         
         if(t_check && jsfr_params[jsfr_convertid(jsfr_toolid + '_' + jsfr_searchinput[jsondata.divid].param)]==val) t_sel=' SELECTED';
         str += '<option value=\"' + val + '\"' + t_sel + '>';
         if(name.length>40) str += name.substring(0,40);
         else str += name;
         str += '</option>';
      }
      str += '</select>';
      jQuery('#formdiv_' + jsfr_convertid(jsondata.divid)).html(str);
      
      // Now default the dropdown to the input of this form
      //if(Boolean(jsfr_params[jsfr_toolid + '_' + jsfr_searchinput[jsondata.divid].param])) {
      //	  jQuery('#form_' + jsondata.divid).val(jsfr_params[jsfr_toolid + '_' + jsfr_searchinput[jsondata.divid].param]);
      //}
   }
   

   // Step 4b: populate the dynamic checkboxes opts
   function jsfr_formcheckboxes(jsondata){
      //alert('2nd json obj: ' + JSON.stringify(jsfr_dynajson));
      //alert('dropdown results: ' + JSON.stringify(jsondata));
      
      var str = '';
      str += '<input type=\"hidden\"';
      str += ' name=\"' + jsfr_convertid(jsondata.divid) + '\"';
      str += ' id=\"' + jsfr_convertid(jsondata.divid) + '\"';
      if(Boolean(jsfr_params[jsfr_convertid(jsondata.divid)])) {
        str += ' value=\"' + jsfr_params[jsfr_convertid(jsondata.divid)] + '\"';
      } else {
        str += ' value=\"\"';
      }
      str += '>';
      
      
      str += '<div>';
      for(var i=0;i<jsondata.rows.length;i++) {
         var name = '';
         if(Boolean(jsondata.rows[i].name)) name = jsondata.rows[i].name;
         else if(Boolean(jsondata.rows[i].title)) name = jsondata.rows[i].title;
         else if(Boolean(jsondata.rows[i].value)) name = jsondata.rows[i].value;
         
         var val = '';
         if(Boolean(jsondata.rows[i].wd_row_id)) val = jsondata.rows[i].wd_row_id;
         else if(Boolean(jsondata.rows[i].value)) val = jsondata.rows[i].value;
         else if(Boolean(jsondata.rows[i].val)) val = jsondata.rows[i].val;
         else val = name;
      
         str += '<div style=\"float:left;margin-right:10px;width:120px;height:18px;overflow:hidden;\">';
         str += '<input type=\"checkbox\"';
         str += ' name=\"cb_' + jsfr_convertid(jsondata.divid) + '\"';
         str += ' id=\"cb_' + jsfr_convertid(jsondata.divid) + '\"';
         str += ' value=\"' + val + '\"';
         str += ' onchange=\"setcheckboxvalues(\'cb_' + jsfr_convertid(jsondata.divid) + '\',\'' + jsfr_convertid(jsondata.divid) + '\');\"';
         var ck = '';
         if(Boolean(jsfr_params[jsfr_convertid(jsondata.divid)])) {
            var valarr = jsfr_params[jsfr_convertid(jsondata.divid)].split(';');
            for(var j=0;j<valarr.length;j++) {
               if(valarr[j].trim()==val) ck = ' CHECKED';
            }
         }
         str += ck + '>' + name + '</div>';
      }
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
      jQuery('#formdiv_' + jsfr_convertid(jsondata.divid)).html(str);
   }
   

   
   // Step 5: after running json search, print results
   function jsfr_searchresults(jsondata){
      var str = '';
      var results = [];
      if(Boolean(jsondata) && Boolean(jsondata.results) && jsondata.results.length>0) {
         results = jsondata.results;
      } else if(Boolean(jsondata) && Boolean(jsondata.rows) && jsondata.rows.length>0) {
         results = jsondata.rows;
      } else if(Boolean(jsondata) && Boolean(jsondata.users) && jsondata.users.length>0) {
         results = jsondata.users;
      } else if(Boolean(jsondata) && Boolean(jsondata.records) && jsondata.records.length>0) {
         results = jsondata.records;
      }
	 var uids = '';
	 
	 if(jsfr_report.countonly.toLowerCase()=='yes' && Boolean(results[0]) && Boolean(results[0]['count(*)'])) {
	    str += '<div style=\"padding:10px;margin:5px;border:2px solid #333333;border-radius:4px;\">';
	    str += '<div style=\"text-align:center;font-size:14px;color:#222222;font-weight:bold;margin-bottom:3px;\">';
	    str += jsfr_report.name;
	    str += '</div>';
	    str += '<div style=\"text-align:center;font-size:10px;color:#444444;font-weight:normal;margin-bottom:5px;\">';
	    str += jsfr_report.name;
	    str += '</div>';
	    str += '<div style=\"text-align:center;font-size:20px;color:#444444;font-weight:bold;margin-bottom:2px;\">';
	    str += results[0]['count(*)'];
	    str += '</div>';
	    str += '</div>';
	 } else {
       var keys = [];
       str += '<table cellpadding=\"3\" cellspacing=\"1\">';
       str += '<tr style=\"background-color:#EDEDED;\">';
       for (var key in results[0]) {
          if(key!='password' && key!='token' && key!='useremail') {
             keys.push(key);
             str += '<td>' + key + '</td>';
          }
       }
       str += '</tr>';
       
       for(var i=0;i<results.length;i++) {
         if(Boolean(results[i].userid)) uids += results[i].userid + ',';
         var bg = '#FFFFFF';
         if((i%2)==1) bg = '#F6FBFF';
         
         str += '<tr style=\"background-color:' + bg + ';\">';
         for(var j=0;j<keys.length;j++) {
            str += '<td>' + results[i][keys[j]] + '</td>';
         }
         str += '</tr>';
       }
       str += '</table>';
       
       //Check if this should show display a graph
       if(jsfr_report.showgraph.toLowerCase()=='yes'){
          var hi = 0;
          var lo = 9999999999;
          var setlabels = [];
          var setlabelsindx = {};
          var setlabelsset = {};
          for(var i=0;i<results.length;i++) {
             var x = parseInt(results[i][jsfr_report.graphx]);
             //alert('graphx: ' +  jsfr_report.graphx + ' data: ' + results[i][jsfr_report.graphx] + ' x: ' + x);
             if(x>hi) hi = x;
             if(x<lo) lo = x;
             if(setlabels.length<5 && !Boolean(setlabelsset[results[i][jsfr_report.graphdata]])) {
                setlabelsset[results[i][jsfr_report.graphdata]] = true;
                setlabelsindx[results[i][jsfr_report.graphdata]] = setlabels.length;
                setlabels.push(results[i][jsfr_report.graphdata]);
             }
          }
          //alert('hi: ' + hi + ' lo: ' + lo);
          
          var xlabels = [];
          var xlabelsindx = {};
          var xstart = lo;
          var xend = hi;
          var xcurr = xstart;
          while(xcurr<=xend && xlabels.length<20) {
             var ty = parseInt(xcurr.toString().slice(0,4));
             var tm = parseInt(xcurr.toString().slice(4));
             
             var tstr = '';
             if(tm==1) tstr = 'Jan ' + ty;
             else if(tm==2) tstr = 'Feb ' + ty;
             else if(tm==3) tstr = 'Mar ' + ty;
             else if(tm==4) tstr = 'Apr ' + ty;
             else if(tm==5) tstr = 'May ' + ty;
             else if(tm==6) tstr = 'Jun ' + ty;
             else if(tm==7) tstr = 'Jul ' + ty;
             else if(tm==8) tstr = 'Aug ' + ty;
             else if(tm==9) tstr = 'Sep ' + ty;
             else if(tm==10) tstr = 'Oct ' + ty;
             else if(tm==11) tstr = 'Nov ' + ty;
             else if(tm==12) tstr = 'Dec ' + ty;
             
             //alert('xcurr: ' + xcurr + ' tstr: ' + tstr + ' ty: ' + ty + ' tm: ' + tm);
             
             xlabelsindx[xcurr.toString()] = xlabels.length;
             xlabels.push(tstr);
             
             tm++;
             if(tm>12) {
                tm = tm - 12;
                ty++;
             }
             if(tm<10) xcurr = parseInt(ty.toString() + '0' + tm.toString());
             else xcurr = parseInt(ty.toString() + tm.toString());
          }
          
          var sets = [];
          for(var i=0;i<results.length;i++) {
             var di = setlabelsindx[results[i][jsfr_report.graphdata]];
             if(!Boolean(sets[di])) sets[di] = [];
             
             var mi = xlabelsindx[results[i][jsfr_report.graphx]];
             sets[di][mi] = results[i][jsfr_report.graphy];
          }
          
          for(var i=0;i<setlabels.length;i++) {
             for(var j=0;j<xlabels.length;j++) {
                if(!Boolean(sets[i][j])) sets[i][j] = '0.0';
             }
          }
          pmgr_AddCustomGraph('jsfr_graph',sets,xlabels,setlabels,(jsfadmin_containerwidth-40),400);
       }
       
       //Check if this is a user list
       if(jsfr_report.forusers.toLowerCase()=='yes' && Boolean(uids) && uids.length>0) {
          var url = '';
          url += 'https://www.plasticsmarkets.org/jsfadmin/admincontroller.php';
          url += '?action=listuserscloning';
          url += '&s_filter=' + encodeURIComponent(uids);
          
          if(Boolean(jsondata.emailsscheduled)){
            str += '*Emails were sent successfully.<br>';
          }
          str += '<a href=\"' + url + '\" target=\"_new\">';
          str += 'View ' + results.length + ' user results.';
          str += '</a>';
       }
    }
     jQuery('#jsfr_results').html(str);
     
     //Check if we need to save this search now
     if(document.getElementById('jsfr_savesearch').checked) {
        jsfr_savesearch();
     }
     
     //Check to see if we should submit a csv request
     if(document.getElementById('jsfr_getcsv').checked) {
        var subj = jQuery('#jsfr_subject_fld').val();
        var now = new Date();
        if(!Boolean(subj)) subj = 'CSV Download ' + now.getFullYear() + '_' + (now.getMonth() + 1) + '_' + now.getDay() + '_' + now.getHour() + now.getMinute();
         var url = '<?php echo getBaseURL(); ?>jsfcode/jsoncontroller.php?action=requestjsoncsv';
         url += '&userid=<?php echo isLoggedOn(); ?>&token=<?php echo $_SESSION['s_user']['token']; ?>';
         //url += '&limit=pageLimit';
         //url += '&page=pageNum';
         url += '&subj=' + encodeURIComponent(subj);
         url += '&json=' + encodeURIComponent(jsfr_jsonurl);
         jsf_json_sendRequest(url,jsfr_runcsv_return);      
     }
   }
   

   
   
   // utility functions
   // add a SQL parameter to the SQL statement
   function jsfr_addstatment(query,stmnt) {
   	   query = jsfr_removebreaks(jsfr_replaceAll(';','',query));
   	   
   	   var tstart = query;
   	   var tend = '';
   	   var tquery = query.toLowerCase();
   	   
   	   var tindexl = tquery.indexOf(' limit ');
   	   var tindexo = tquery.indexOf(' order by ');
   	   var tindexg = tquery.indexOf(' group by ');
   	   //alert('Query: ' + tquery);
   	   if(tindexg>=0) {
   	   	   tstart = query.substr(0,tindexg);
   	   	   tend = query.substr(tindexg);
   	   	   //alert('group by found');
   	   } else if(tindexo>=0) {
   	   	   tstart = query.substr(0,tindexo);
   	   	   tend = query.substr(tindexo);
   	   	   //alert('order by found');
   	   } else if (tindexl>=0) {
   	   	   tstart = query.substr(0,tindexl);
   	   	   tend = query.substr(tindexl);
   	   	   //alert('limit found');
   	   } else {
   	   	   //alert('nothing found');
   	   }
   	   
   	   var retquery = tstart + ' ' + stmnt + tend;
   	   //alert('Query: ' + query + ' statment: ' + stmnt + ' new: ' + retquery);
   	   return retquery;
   }

   
   // Replace all occurrences of a substring
   function jsfr_replaceAll(findstr, replacestr, fstr) {
      if(!Boolean(fstr)) fstr = '';
      var retstr = fstr.split(findstr).join(replacestr);
      return retstr;
   }

   // Remove certain characters from div id attribute
	function jsfr_convertid(str) {
	   str = jsfr_replaceAll('.','_',str);
	   str = jsfr_replaceAll(' ','',str);
	   str = jsfr_replaceAll('#','',str);
	   str = jsfr_replaceAll(',','',str);
	   return str;
	}
   
   // Remove line breaks
	function jsfr_removebreaks(str) {
	   str = jsfr_replaceAll('<br>',' ',str);
	   str = jsfr_replaceAll('<BR>',' ',str);
	   str = jsfr_replaceAll('<br/>',' ',str);
	   str = jsfr_replaceAll('<BR/>',' ',str);
	   return str;
	}
   
	
   // Build initial screen
   jsfr_initreports();
</script>


<?php
}
?>