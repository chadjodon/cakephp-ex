<?php
$htags = getParameter("htags");
$searchtxt = getParameter("searchtxt");
$ua = new UserAcct();

print "\n<!-- user info:\n";
print_r($_SESSION['s_user']);
print "\n-->\n";

?>

<!-- wd_listwebdata_htags.php -->






<div style="position:relative;padding:5px;z-index:1;">
<table cellpadding="5" cellspacing="2">
<tr valign="top">
   <td id="wd_menu" style="width:200px;background-color:#F2F2F2;"></td>
   <td id="wd_body" style="min-width:700px;"></td>
</tr>
</table>
</div>

<div id="htags_loading" style="position:absolute;padding:40px;left:0px;top:0px;z-index:1;display:none;width:100%;height:100%;font-size:22px;font-family:verdana;color:#222222;background-color:#FDFDFD;opacity:0.8;">Loading...</div>







<script>
var curr_id;

function searchhtags(htags,searchtxt,orderby){
   jQuery('#htags_loading').show();
   var url = '<?php echo getBaseURL().$GLOBALS['codeFolder']; ?>jsoncontroller.php?action=jsfhashtag&tb=webdata&col=htags&prk=wd_id&htaction=search';
   url += '&userid=<?php echo isLoggedOn(); ?>&token=<?php echo $_SESSION['s_user']['token']; ?>';
   url += '&searchcols=' + encodeURIComponent('wd_id,name,htags,info,privatesrvy,createdon');
   url += '&excludeht=' + encodeURIComponent('hidden');
   if(Boolean(htags)) url += '&searchht=' + encodeURIComponent(htags);
   if(Boolean(searchtxt)) url += '&searchtxt=' + encodeURIComponent(searchtxt);
   if(Boolean(orderby)) url += '&orderby=' + encodeURIComponent(orderby);
   //alert('url: ' + url);
   jsf_json_sendRequest(url,setuphtags);
}

function setuphtags(jsondata){
   //alert('response: ' + JSON.stringify(jsondata));
   var str = '<div style=\"width:200px;\">';
   
   var usht = '';
   if(Boolean(jsondata.ushashtags) && jsondata.ushashtags.length>0){
      for(var i=0;i<jsondata.ushashtags.length;i++){
         usht += jsondata.ushashtags[i] + ',';
      }
   }
   
   str += '<div style=\"margin-bottom:5px;\">';   
   str += '<input type=\"text\" id=\"searchtxt\" style=\"width:100px;font-size:10px;font-family:verdana;margin-right:2px;\" value=\"' + jsondata.searchtxt + '\">';
   str += '<span ';
   str += 'style=\"cursor:pointer;font-size:10px;pointer:cursor;font-family:verdana;padding:2px;background-color:#DDDDDD;border:1px solid #AAAAAA;\" ';
   str += 'onclick=\"searchhtags(\'' + usht + '\',jQuery(\'#searchtxt\').val(),\'' + jsondata.orderby + '\');\" ';
   //str += 'onclick=\"searchhtags(\'' + usht + '\',\'' + jsondata.searchtxt + '\');\" ';
   str += '>Search</span><br>';
   str += '</div>';
   
   
   if(Boolean(jsondata.ushashtags) && jsondata.ushashtags.length>0){
      for(var i=0;i<jsondata.ushashtags.length;i++){
         var temp = '';
         for(var j=0;j<jsondata.ushashtags.length;j++){
            if(i!=j) temp += jsondata.ushashtags[j] + ',';
         }
         str += '<div ';
         str += 'style=\"position:relative;border:1px solid #CCCCCC;padding:2px;font-size:8px;color:#777777;font-family:verdana;margin-bottom:2px;cursor:pointer;\" ';
         //str += 'onClick=\"searchhtags(\'' + temp + '\',jQuery(\'#searchtxt\').val());\" ';
         str += 'onClick=\"searchhtags(\'' + temp + '\',\'' + jsondata.searchtxt + '\',\'' + jsondata.orderby + '\');\" ';
         str += '>';
         str += 'in ' + jsondata.ushashtags[i];
         str += '<div style=\"position:absolute;right:4px;top:0px;color:red;font-size:10px;font-weight:bold;\">x</div>';
         str += '</div>';
      }
      //str += '<div style=\"margin-top:4px;margin-bottom:4px;background-color:#CCCCCC;height:1px;overflow:hidden;\"></div>';
      str += '<div style=\"margin-top:4px;margin-bottom:4px;height:1px;overflow:hidden;\"></div>';
   }
   
   if(Boolean(jsondata.rehashtags) && jsondata.rehashtags.length>0){
      for(var i=0;i<jsondata.rehashtags.length;i++){
         str += '<div style=\"clear:both;cursor:pointer;\" ';
         //str += 'onClick=\"searchhtags(\'' + usht + jsondata.rehashtags[i] + '\',jQuery(\'#searchtxt\').val());\" ';
         str += 'onClick=\"searchhtags(\'' + usht + jsondata.rehashtags[i] + '\',\'' + jsondata.searchtxt + '\',\'' + jsondata.orderby + '\');\" ';
         str += '>';
         str += '<div style=\"float:left;margin-top:4px;margin-left:1px;margin-right:1px;width:8px;height:8px;overflow:hidden;border:0;border-radius:4px;background-color:#CCCCCC;\"></div>';
         str += '<div ';
         str += 'style=\"float:left;padding:1px;font-size:10px;color:#444488;font-family:verdana;margin-bottom:2px;\" ';
         str += '>';
         str += jsondata.rehashtags[i];
         str += '</div>';
         str += '<div style=\"clear:both;width:1px;height:1px;overflow:hidden;\"></div>';
         str += '</div>';
      }
      str += '<div style=\"margin-top:4px;margin-bottom:4px;background-color:#CCCCCC;height:1px;overflow:hidden;\"></div>';      
   }

   var wdlist = '';
   if(Boolean(jsondata.results) && jsondata.results.length>0){
      wdlist += '<div style=\"margin-top:3px;margin-bottom:3px;padding-top:2px;padding-bottom:8px;\">';
      
      var t_stl = 'color:#000000;';
      if (jsondata.orderby=='name') t_stl = 'color:#AAAAAA;';
      
      wdlist += '<div ';
      wdlist += 'style=\"' + t_stl + 'font-size:12px;font-family:verdana;cursor:pointer;width:300px;float:left;margin-right:13px;\" ';
      wdlist += 'onClick=\"searchhtags(\'' + usht + '\',\'' + jsondata.searchtxt + '\',\'name\');\" ';
      wdlist += '>';
      wdlist += 'Name';
      wdlist += '</div>';
      
      var t_stl = 'color:#000000;';
      if (jsondata.orderby=='createdon DESC') t_stl = 'color:#AAAAAA;';
            
      wdlist += '<div ';
      wdlist += 'style=\"' + t_stl + 'font-size:12px;font-family:verdana;cursor:pointer;width:75px;float:left;margin-right:13px;\" ';
      wdlist += 'onClick=\"searchhtags(\'' + usht + '\',\'' + jsondata.searchtxt + '\',\'createdon DESC\');\" ';
      wdlist += '>';
      wdlist += 'Created';
      wdlist += '</div>';
      wdlist += '<div style=\"clear:both;width:1px;height:1px;overflow:hidden;\"></div>';
      wdlist += '</div>';
      
      for(var i=0;i<jsondata.results.length;i++){
         var t_clr = 'background-color:#FFFFFF;';
         if((i%2)==1) t_clr = 'background-color:#EEEEFF;';
         wdlist += '<div style=\"' + t_clr + 'margin-top:3px;margin-bottom:3px;padding-top:2px;padding-bottom:2px;border-bottom:1px solid #DDDDDD;\">';
         wdlist += '<div ';
         wdlist += 'style=\"font-size:12px;color:#222222;font-family:verdana;cursor:pointer;width:300px;float:left;margin-right:13px;\" ';
         //wdlist += 'onClick=\"viewwd(\'' + jsondata.results[i].wd_id + '\',\'' + jsondata.results[i].name + '\',\'' + jsondata.results[i].htags + '\',\'' + jsondata.results[i].privatesrvy + '\');\" ';
         wdlist += 'onClick=\"viewwd(\'' + jsondata.results[i].wd_id + '\');\" ';
         wdlist += '>';
         wdlist += jsondata.results[i].name;
         wdlist += '</div>';
         wdlist += '<div ';
         wdlist += 'style=\"font-size:10px;color:#222222;font-family:verdana;cursor:pointer;width:75px;float:left;margin-right:13px;\" ';
         wdlist += '>';
         var t_date = jsondata.results[i].createdon.substring(5,7) + '/';
         t_date += jsondata.results[i].createdon.substring(8,10) + '/';
         t_date += jsondata.results[i].createdon.substring(0,4);
         wdlist += t_date;
         wdlist += '</div>';
         var t = '';
         t = ' onclick=\"location.href=\'<?php echo getBaseURL().$GLOBALS['adminFolder']; ?>admincontroller.php?action=wd_listrows&pageLimit=25&wd_id=' + jsondata.results[i].wd_id + '\';\"';
         wdlist += '<div' + t + ' style=\"float:left;font-size:10px;font-family:verdana;color:blue;cursor:pointer;margin-right:15px;\">List rows</div>';
         t = ' onclick=\"location.href=\'<?php echo getBaseURL().$GLOBALS['adminFolder']; ?>admincontroller.php?action=webdata&wd_id=' + jsondata.results[i].wd_id + '\';\"';
         wdlist += '<div' + t + ' style=\"float:left;font-size:10px;font-family:verdana;color:blue;cursor:pointer;margin-right:15px;\">Edit structure</div>';
         t = ' onclick=\"location.href=\'<?php echo getBaseURL().$GLOBALS['adminFolder']; ?>admincontroller.php?action=webdata&subaction=makecopy&wd_id=' + jsondata.results[i].wd_id + '\';\"';
         wdlist += '<div' + t + ' style=\"float:left;font-size:10px;font-family:verdana;color:blue;cursor:pointer;margin-right:15px;\">Copy</div>';
         
         <?php if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),12)) { ?>         
            t = ' onclick=\"if(confirm(\'Are you sure you want to permanently delete this table and its data?\')) location.href=\'<?php echo getBaseURL().$GLOBALS['adminFolder']; ?>admincontroller.php?action=webdata&subaction=remove&wd_id=' + jsondata.results[i].wd_id + '\';\"';
            wdlist += '<div' + t + ' style=\"float:left;font-size:10px;font-family:verdana;color:blue;cursor:pointer;margin-right:15px;\">Delete</div>';
         <?php } ?>
         wdlist += '<div style=\"clear:both;width:1px;height:1px;overflow:hidden;\"></div>';
         wdlist += '</div>';
         
         str += '<div ';
         str += 'class=\"wd_list\" ';
         str += 'id=\"wd_' + jsondata.results[i].wd_id + '\" ';
         str += 'style=\"padding:2px;font-size:10px;color:#222222;font-family:verdana;margin-bottom:2px;cursor:pointer;\" ';
         //str += 'onClick=\"viewwd(\'' + jsondata.results[i].wd_id + '\',\'' + jsondata.results[i].name + '\',\'' + jsondata.results[i].htags + '\',\'' + jsondata.results[i].privatesrvy + '\');\" ';
         str += 'onClick=\"viewwd(\'' + jsondata.results[i].wd_id + '\');\" ';
         str += '>';
         str += jsondata.results[i].name;
         str += '</div>';
      }      
   }
   
   str += '</div>';
   jQuery('#wd_menu').html(str);
   
   var temp = '';
   temp += '<div style=\"margin-left:10px;margin-bottom:12px;margin-top:4px;font-size:16px;font-family:verdana;\">';
   temp += 'JData Web Tables and Surveys<br>';
   temp += '<a style=\"font-size:10px;font-family:verdana;margin-top:2px;\" href=\"<?php echo getBaseURL().$GLOBALS['adminFolder']; ?>admincontroller.php?action=webdata\">New Data Table</a>';
   temp += '</div>';

   temp += '<div style=\"padding:10px;margin:10px;border:1px solid #DDDDDD;border-radius:8px;\">';   
   temp += wdlist;
   temp += '</div>';
   jQuery('#wd_body').html(temp);
   jQuery('#htags_loading').hide();
}

function viewwd(wd_id){
   jQuery('#htags_loading').show();   
   var url = '<?php echo getBaseURL().$GLOBALS['codeFolder']; ?>jsoncontroller.php?action=getrelatedwdtables';
   url += '&userid=<?php echo isLoggedOn(); ?>&token=<?php echo $_SESSION['s_user']['token']; ?>';
   url += '&wd_id=' + wd_id;
   //alert('url: ' + url);
   jsf_json_sendRequest(url,return_viewwd);
   
}

function return_viewwd(jsondata){
   var wd_id = jsondata.webdata.wd_id;
   var name = jsondata.webdata.name;
   var hts = jsondata.webdata.htags;
   var prv = jsondata.webdata.privatesrvy;
   
   curr_id = wd_id;
   jQuery('.wd_list').css('background-color','#F2F2F2').css('color','#111111');
   jQuery('#wd_' + wd_id).css('background-color','#111188').css('color','#FFFFFF');
   var str = '';
   
   str += '<div style=\"padding:10px;margin-left:20px;margin-right:20px;margin-top:10px;margin-bottom:10px;border:1px solid #DEDEDE;border-radius:8px;width:100%;\">';
   
   str += '<div style=\"padding-left:3px;padding-right:8px;padding-top:3px;margin-bottom:6px;font-size:24px;color:#111111;font-family:verdana;\">';
   str += name;
   str += '</div>';
   
   var type = 'JData';
   if(prv=='1') type='Private Survey';
   else if(prv=='2') type = 'Public Survey';
   else if(prv=='3') type = 'Website Data';
   else if(prv=='4') type = 'Admin Data';
   else if(prv=='5') type = 'Other Data';
   else if(prv=='7') type = 'Mobile Internal';
   else if(prv=='8') type = 'Mobile Survey';
   else if(prv=='9') type = 'Mobile Secure';
   str += '<div style=\"padding-left:3px;padding-right:8px;padding-top:3px;margin-bottom:24px;font-size:10px;color:#555555;font-family:verdana;\">';
   str += type;
   str += '</div>';
   
   str += '<div style=\"position:relative;margin-bottom:30px;margin-top:2px;\">';
   str += '<div ';
   str += 'style=\"float:left;cursor:pointer;width:100px;margin-right:8px;padding:5px;background-color:#DDDDDD;border:1px solid #BBBBBB;border-radius:5px;font-size:10px;font-family:verdana;text-align:center;\" ';
   str += 'onclick=\"location.href=\'<?php echo getBaseURL().$GLOBALS['adminFolder']; ?>admincontroller.php?action=wd_listrows&wd_id=' + wd_id + '&pageLimit=25\';\" ';
   str += '>View Records</div>';
   str += '<div ';
   str += 'style=\"float:left;cursor:pointer;width:100px;margin-right:8px;padding:5px;background-color:#DDDDDD;border:1px solid #BBBBBB;border-radius:5px;font-size:10px;font-family:verdana;text-align:center;\" ';
   str += 'onclick=\"location.href=\'<?php echo getBaseURL().$GLOBALS['adminFolder']; ?>admincontroller.php?action=webdata&wd_id=' + wd_id + '\';\" ';
   str += '>Edit Structure</div>';
   str += '<div ';
   str += 'style=\"float:left;cursor:pointer;width:100px;margin-right:8px;padding:5px;background-color:#DDDDDD;border:1px solid #BBBBBB;border-radius:5px;font-size:10px;font-family:verdana;text-align:center;\" ';
   str += 'onclick=\"location.href=\'<?php echo getBaseURL().$GLOBALS['adminFolder']; ?>admincontroller.php?action=webdata&subaction=makecopy&wd_id=' + wd_id + '\';\" ';
   str += '>Copy</div>';
   <?php if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),12)) { ?>         
      str += '<div ';
      str += 'style=\"float:left;cursor:pointer;width:100px;margin-right:8px;padding:5px;background-color:#DDDDDD;border:1px solid #BBBBBB;border-radius:5px;font-size:10px;font-family:verdana;text-align:center;\" ';
      str += 'onclick=\"if(confirm(\'Are you sure you want to permanently delete this table and its data?\')) location.href=\'<?php echo getBaseURL().$GLOBALS['adminFolder']; ?>admincontroller.php?action=webdata&subaction=remove&wd_id=' + wd_id + '\';\" ';
      str += '>Delete</div>';
   <?php } ?>
   str += '<div style=\"width:1px;height:1px;overflow:hidden;clear:both;\"></div>';
   str += '</div>';
   
   if(Boolean(jsondata.foreignsry) && jsondata.foreignsry.length>0){
      str += '<div style=\"background-color:#CCCCCC;height:1px;overflow:hidden;margin-bottom:2px;margin-top:10px;\"></div>';
      str += '<div style=\"padding:10px;\">';
      str += '<div style=\"font-size:12px;color:#888888;font-family:verdana;margin-bottom:1px;\">';
      str += 'Internally Referenced Tables</div>';
      
      str += '<div style=\"margin-bottom:5px;padding:5px;\">';
      for(var i=0;i<jsondata.foreignsry.length;i++){
         str += '<div ';
         str += 'style=\"padding:2px;font-size:12px;color:#444444;font-family:verdana;margin-bottom:2px;cursor:pointer;\" ';
         str += 'onclick=\"viewwd(\'' + jsondata.foreignsry[i].wd_id + '\');\" ';
         str += '>';
         str += jsondata.foreignsry[i].name;
         str += '</div>';            
      }      
      str += '</div>';            
      str += '</div>';            
   }
   
   str += '<div style=\"background-color:#CCCCCC;height:1px;overflow:hidden;margin-bottom:2px;margin-top:10px;\"></div>';
   
   str += '<div id=\"htags_default\" style=\"position:relative;padding:10px;font-size:10px;font-family:verdana;\"></div>';
   
   str += '<div id=\"htags_list_title\" style=\"padding:10px;font-size:12px;color:#888888;font-family:verdana;margin-bottom:1px;\">';
   str += 'Hashtags</div>';
   str += '<div id=\"htags_list\" style=\"padding:12px;font-size:12px;color:#222222;font-family:verdana;margin-bottom:2px;\">';
   str += '<input type=\"text\" id=\"newwdhtag\" style=\"width:140px;font-size:10px;font-family:verdana;margin-right:2px;\" value=\"\">';
   str += '<span ';
   str += 'style=\"cursor:pointer;font-size:10px;pointer:cursor;font-family:verdana;padding:2px;background-color:#DDDDDD;border:1px solid #AAAAAA;\" ';
   str += 'onclick=\"addHashTag(' + wd_id + ',jQuery(\'#newwdhtag\').val());\" ';
   str += '>Go</span><br>';
   
   str += '<div id=\"allhtags\"></div>';

   str += '</div>';
   
   
   str += '</div>';
   
   jQuery('#wd_body').html(str);
   showHashTags(hts);
}

function showHashTags(hts){
   var str = '';
   
   //Defaults First (these are just shortcuts)
   var dflts = [];
   var dflt_temp = {};
   dflt_temp.name = 'Show in List';
   dflt_temp.value = '#display';
   dflts.push(dflt_temp);
   dflt_temp = {};
   dflt_temp.name = 'Survey';
   dflt_temp.value = '#survey';
   dflts.push(dflt_temp);
   dflt_temp = {};
   dflt_temp.name = 'Active';
   dflt_temp.value = '#active';
   dflts.push(dflt_temp);
   dflt_temp = {};
   dflt_temp.name = 'Associate to user DB';
   dflt_temp.value = '#associatedtodb';
   dflts.push(dflt_temp);
   
   for(var j=0;j<dflts.length;j++) {
      str += '<div style=\"float:left;width:130px;overflow:hidden;margin-right:15px;\">';
      str += '<table cellpadding=\"0\" cellspacing=\"0\"><tr valign=\"top\">';
      str += '<td><input type=\"checkbox\"';
      if(doesHashTagExist(hts,dflts[j].value)) str += ' onclick=\"removeHashTag(' + curr_id + ',\'' + dflts[j].value + '\');\" CHECKED>';
      else str += ' onclick=\"addHashTag(' + curr_id + ',\'' + dflts[j].value + '\');\">';
      str += '</td><td><div style=\"position:relative;margin-top:4px;\">' + dflts[j].name + '</div></td>';
      str += '</tr></table>';
      str += '</div>';
   }
   str += '<div style=\"clear:both;\"></div>';
   jQuery('#htags_default').html(str);
   
   //Now display all hashtags
   str = '';
   if(Boolean(hts)){
      var hta = hts.split(' ');
      if(Boolean(hta) && hta.length>0){
         for(var i=0;i<hta.length;i++){
            str += '<div ';
            str += 'style=\"padding:2px;font-size:12px;color:#444444;font-family:verdana;margin-bottom:2px;cursor:no-drop;\" ';
            str += 'onClick=\"if (confirm(\'Are you sure you want to remove this hashtag?\')) removeHashTag(curr_id,\'' + hta[i] + '\');\" ';
            str += '>';
            str += hta[i];
            str += '</div>';            
         }
      }
   }
   jQuery('#allhtags').html(str);
   jQuery('#htags_loading').hide();
}

function doesHashTagExist(hts,ht){
   var ans = false;
   if(Boolean(hts)){
      var hta = hts.split(' ');
      if(Boolean(hta) && hta.length>0){
         for(var i=0;i<hta.length;i++){
            if(hta[i]==ht) {
               ans = true;
               break;
            }
         }
      }
   }
   return ans;
}

function addHashTag(wd_id,htag){
   jQuery('#htags_loading').show();
   var url = '<?php echo getBaseURL().$GLOBALS['codeFolder']; ?>jsoncontroller.php?action=jsfhashtag';
   url += '&tb=webdata';
   url += '&col=htags';
   url += '&prk=wd_id';
   url += '&prv=' + wd_id;
   url += '&ht=' + encodeURIComponent(htag);
   url += '&htaction=add';
   url += '&userid=<?php echo isLoggedOn(); ?>&token=<?php echo $_SESSION['s_user']['token']; ?>';
   if(Boolean(htag)) url += '&searchht=' + encodeURIComponent(htag);
   jsf_json_sendRequest(url,return_htagmodify);
   jQuery('#newwdhtag').val('');
}

function removeHashTag(wd_id,htag){
   jQuery('#htags_loading').show();
   var url = '<?php echo getBaseURL().$GLOBALS['codeFolder']; ?>jsoncontroller.php?action=jsfhashtag';
   url += '&tb=webdata';
   url += '&col=htags';
   url += '&prk=wd_id';
   url += '&prv=' + wd_id;
   url += '&ht=' + encodeURIComponent(htag);
   url += '&htaction=delete';
   url += '&userid=<?php echo isLoggedOn(); ?>&token=<?php echo $_SESSION['s_user']['token']; ?>';
   if(Boolean(htag)) url += '&searchht=' + encodeURIComponent(htag);
   jsf_json_sendRequest(url,return_htagmodify);
   jQuery('#newwdhtag').val('');
}

function return_htagmodify(jsondata){
   showHashTags(jsondata.curht);
}


searchhtags('<?php echo $htags; ?>','<?php echo $searchtxt; ?>','createdon DESC');
</script>