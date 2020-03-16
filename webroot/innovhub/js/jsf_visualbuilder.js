
var jsfv_totalwd;
var jsfv_totalht;

var jsfv_metawd;
var jsfv_drawwd;

var jsfv_wd_id;
var jsfv_wd_row_id;
var jsfv_divid;
var jsfv_name;

var jsfv_userid;
var jsfv_token;

// Initialize tool
// wd_id: Name of jdata table with name/value pairs (Pagebuilder)
// name: unique name of this div/layer
// divid: where to show this tool on the screen
// wd: width of tool
// ht: height of tool
function jsfv_initadmin(wd_id,name,divid,wd,ht) {
   if(Boolean(jsfv_userid) && Boolean(jsfv_token)) {
      //alert('1. wdname: ' + wd_id);
      jsfv_wd_id = wd_id;
      jsfv_name = name;
      jsfv_divid = divid;
      if(!Boolean(wd)) wd = jQuery(window).width();
      if(!Boolean(ht)) ht = jQuery(window).height();
      
      jsfv_totalwd = wd;
      jsfv_totalht = ht;
      
      jsfv_metawd = 320;
      jsfv_drawwd = jsfv_totalwd - jsfv_metawd;
      if(jsfv_totalwd<600) {
         jsfv_metawd = jsfv_totalwd;
         jsfv_drawwd = jsfv_totalwd;
      } else if(jsfv_totalwd<760) {
         jsfv_metawd = 250;
         jsfv_drawwd = jsfv_totalwd - jsfv_metawd;
      }
      
      var str = '';
      str += '<div id=\"jsfv_outer\" style=\"position:relative;height:' + jsfv_totalht + 'px;width:' + jsfv_totalwd + 'px;\">';
      str += '<div id=\"jsfv_draw\" style=\"float:right;width:' + jsfv_drawwd + 'px;height:' + jsfv_totalht + 'px;overflow:hidden;\">';
      str += '<div id=\"jsfv_draw_inner\" style=\"position:relative;width:' + jsfv_drawwd + 'px;height:' + jsfv_totalht + 'px;overflow:auto;\">';
      str += '</div>';
      str += '</div>';
      str += '<div id=\"jsfv_meta\" style=\"float:right;width:' + jsfv_metawd + 'px;height:' + jsfv_totalht + 'px;\">';
      str += '<div id=\"jsfv_meta_inner\" style=\"position:relative;width:' + jsfv_metawd + 'px;height:' + (jsfv_totalht - 180) + 'px;overflow-x:hidden;overflow-y:auto;\">';
      str += '</div>';
      str += '<div id=\"jsfv_meta_layers\" style=\"position:relative;width:' + jsfv_metawd + 'px;height:180px;overflow-x:hidden;overflow-y:auto;background-color:#f2f2f2\">';
      str += '</div>';
      str += '</div>';
      str += '</div>';
      
      jQuery('#' + divid).html(str);
      
      var mvdiv = document.getElementById('jsfv_draw_inner');
      mvdiv.addEventListener("mouseup", function (e) {e.stopPropagation();jsfv_actionxy('up', e);}, false);
      mvdiv.addEventListener("mousemove", function (e) {e.stopPropagation();jsfv_actionxy('move', e);}, false);   
      mvdiv.addEventListener("mousedown", function (e) {e.stopPropagation();jsfv_actionxy('newdown', e);}, false);
      document.addEventListener("keyup", function (e) {e.stopPropagation();jsfv_actionxy('keyup', e);}, false);
      document.addEventListener("keydown", function (e) {e.stopPropagation();jsfv_actionxy('keydown', e);}, false);
      
      jsfv_drawadmindivs(wd_id,name);
   } else {
      var str = '';
      str += '<div style=\"padding:20px;font-size:16px;color:#333333;\">';
      str += 'Please log in before making changes.';
      str += '</div>';
      jQuery('#' + divid).html(str);
   }
}

function jsfv_getallpagenames() {
   var callback = 'jsfv_getallpagenames_return';
   var query = '';
   query += '&wd_id=' + jsfv_wd_id;
   query += '&namefilter=' + encodeURIComponent('Visual: ');
   query += '&userid=' + encodeURIComponent(jsfv_userid);
   query += '&token=' + encodeURIComponent(jsfv_token);
   query += '&reducebackups=1';

   jsfpb_QuickJSON('getnvpuniquenames',callback,query);
}

var jsfv_allnames;
var jsfv_allbackups;
var jsfv_backupsready = false;
function jsfv_getallpagenames_return(jsondata) {
   //alert('pages: ' + JSON.stringify(jsondata));
   jsfpb_ReturnJSON(jsondata);
   jsfv_allnames = [];
   jsfv_allbackups = [];
   var str = '';
   if(Boolean(jsondata) && Boolean(jsondata.results) && jsondata.results.length>0) {
      str += '<select id=\"jsfv_changepage\" onchange=\"location.href=\'jsf_visualbuilder.php?userid=' + jsfv_userid + '&token=' + jsfv_token + '&wd_id=' + encodeURIComponent(jsfv_wd_id) + '&name=\' + encodeURIComponent(jQuery(\'#jsfv_changepage\').val());\">';
      str += '<option value=\"\"></option>';
      for(var i=0;i<jsondata.results.length;i++) {
         var temp = jsondata.results[i].substr(8);
         if(!temp.endsWith('backup')) {
            jsfv_allnames.push(temp);
            var sel = '';
            if(temp.toLowerCase() == jsfv_name.toLowerCase()) sel = ' SELECTED';
            str += '<option value=\"' + temp + '\"' + sel + '>' + temp + '</option>';
         } else {
            // anything that ends in "backup" is not listed * be careful here
            jsfv_allbackups.push(temp);
         }
      }
      str += '</select>';
   }
   jsfv_backupsready = true;
   jQuery('#jsfv_pagedropdown').html(str);
}

// Ymd dateformat for today
function jsfv_getshortdate() {
   var dt = new Date();
   m = (dt.getMonth() + 1).toString().padStart(2, "0");
   d = dt.getDate().toString().padStart(2, "0");
   return dt.getFullYear() + m + d;
}

// Make a backup if one has not been made today.
function jsfv_checkbackup() {
   // if the list is not loaded yet, wait, otherwise iterate
   if(Boolean(jsfv_backupsready)) {
      var foundbu = false;
      var buname = jsfv_name + '_' + jsfv_getshortdate() + 'backup';
      for(var i=0;i<jsfv_allbackups.length;i++) {
         if(jsfv_allbackups[i] == buname) {
            foundbu = true;
            break;
         }
      }
      if(!foundbu) {
         jsfv_allbackups.push(buname);
         jsfv_makecopy(buname,true);
      }
      //alert('backups: ' + JSON.stringify(jsfv_allbackups));
   } else {
      // List is still loading... wait and retry
      setTimeout(jsfv_checkbackup,800);
   }
}

// Get divs, draw dropdown
function jsfv_drawadmindivs(wd_id,name,callback){
   jQuery('#jsfv_draw_inner').html('Loading...');
   
   if(!Boolean(callback)) callback = 'jsfv_drawadmindivs_return';
   var params ='';
   params += '&cmsenabled=1';
   params += '&maxcol=8';
   params += '&cmsq_' + jsfpb_shorterwdname(wd_id) + '_name=' + encodeURIComponent('Visual: ' + name);
   
   //alert('2. wdname: ' + wd_id + ' params: ' + params);
   jsfpb_getwebdata_jsonp(wd_id,callback,params);
}

// Remember divs, create a dropdown of available divs, and draw out the divs
var jsfv_divs;
var jsfv_dimensions;
function jsfv_drawadmindivs_return(jsondata) {
   //alert('jsfv_drawadmindivs_return divs: ' + JSON.stringify(jsondata));
   jsfv_divs = {};
   
   // New way to get dimensions...
   //jsfv_dimensions = jsfv_getdims(jsondata.rows);
   //alert('dims: ' + JSON.stringify(jsfv_dimensions));
   
   //alert('response: ' + JSON.stringify(jsondata));
   jQuery('#jsfv_draw_inner').html('');
   var infotext = '';
   var infotext_id = '';
   
   var coordsdiv = '<div id=\"jsfv_draw_coords\" style=\"z-index:998;position:absolute;left:5px;top:5px;font-size:8px;font-weight:bold;opacity:0.7;background-color:#FFFFFF;padding:4px;border-radius:3px;\"></div>';
   var zoomdiv1 = '<div id=\"jsfv_draw_zoomin\" style=\"z-index:998;position:absolute;right:25px;top:6px;\">' + jsfv_drawresize(true,14,2,'#222222','jsfv_resize(20.0);') + '</div>';
   var zoomdiv2 = '<div id=\"jsfv_draw_zoomout\" style=\"z-index:998;position:absolute;right:50px;top:6px;\">' + jsfv_drawresize(false,14,2,'#222222','jsfv_resize(-20.0);') + '</div>';
   var zoomdiv3 = '<div id=\"jsfv_draw_zoom\" style=\"z-index:998;position:absolute;right:75px;top:6px;font-size:14px;font-weight:normal;\">' + Math.round(jsfv_zoomparam) + '%</div>';
   var creatediv = '<div id=\"jsfv_temp_new\" style=\"position:absolute;display:none;border:1px solid #DD3333;overflow:hidden;\"></div>';
   var debugdiv = '<div id=\"jsfv_debug\" style=\"position:absolute;left:180px;top:4px;width:400px;height:40px;display:none;border:1px solid #333333;overflow:hidden;\"></div>';
   var savediv = '';
   savediv += '<div id=\"jsfv_draw_save_outer\" style=\"z-index:999;position:absolute;left:0px;top:0px;display:none;background-color:#222222;opacity:0.75;width:' + jQuery('#jsfv_draw_inner').width() + 'px;height:' + jQuery('#jsfv_draw_inner').height() + 'px;overflow:hidden;\">';
   savediv += '<div id=\"jsfv_draw_save\" style=\"position:relative;color:#FFFFFF;font-size:24px;text-align:center;margin-top:80px;font-weight:bold;\">Saving...</div>';
   savediv += '</div>';
   var lboxdiv = '';
   lboxdiv += '<div id=\"jsfv_draw_lbox_outer\" style=\"z-index:999;position:absolute;left:0px;top:0px;display:none;background-color:#FFFFFF;opacity:0.99;width:' + jQuery('#jsfv_draw_inner').width() + 'px;height:' + jQuery('#jsfv_draw_inner').height() + 'px;overflow:hidden;\">';
   lboxdiv += '<div id=\"jsfv_draw_lbox_xout\" onclick=\"jQuery(\'#jsfv_draw_lbox_outer\').hide();\" style=\"z-index:2;position:absolute;right:10px;top:10px;color:red;font-size:20px;font-family:courier;cursor:pointer;font-weight:bold\">x</div>';
   lboxdiv += '<div id=\"jsfv_draw_lbox\" style=\"z-index:1;position:relative;margin-top:10px;margin-left:10px;padding:10px;width:' + (jQuery('#jsfv_draw_inner').width() - 50) + 'px;height:' + (jQuery('#jsfv_draw_inner').height() - 50) + 'px;overflow-x:hidden;overflow-y:auto;\"></div>';
   lboxdiv += '</div>';
   var demodiv = '';
   demodiv += '<div id=\"jsfv_draw_demo_outer\" style=\"z-index:999;position:absolute;left:0px;top:0px;\">';
   demodiv += '<div id=\"jsfv_draw_demo\" style=\"position:relative;display:none;background-color:#E0E0E0;width:' + jQuery('#jsfv_draw_inner').width() + 'px;height:' + jQuery('#jsfv_draw_inner').height() + 'px;\">';
   demodiv += '<div id=\"jsfv_draw_demo_inner\" style=\"position:absolute;z-index:1;left:0px;top:0px;width:' + jQuery('#jsfv_draw_inner').width() + 'px;height:' + jQuery('#jsfv_draw_inner').height() + 'px;\"></div>';
   demodiv += '<div onclick=\"jQuery(\'#jsfv_draw_demo\').hide()\" id=\"jsfv_draw_demo_xout\" style=\"position:absolute;z-index:999;left:3px;top:3px;color:red;font-size:14px;font-weight:bold;cursor:pointer;\">Close</div>';
   demodiv += '</div>';
   demodiv += '</div>';
   
   
   jQuery('#jsfv_draw_inner').append(coordsdiv);
   jQuery('#jsfv_draw_inner').append(demodiv);
   jQuery('#jsfv_draw_inner').append(savediv);
   jQuery('#jsfv_draw_inner').append(lboxdiv);
   jQuery('#jsfv_draw_inner').append(zoomdiv1);
   jQuery('#jsfv_draw_inner').append(zoomdiv2);
   jQuery('#jsfv_draw_inner').append(zoomdiv3);
   jQuery('#jsfv_draw_inner').append(creatediv);
   jQuery('#jsfv_draw_inner').append(debugdiv);
   
   var mvdiv = document.getElementById('jsfv_draw_lbox_outer');
   mvdiv.addEventListener("mousedown", function (e) {e.stopPropagation();}, false);
   
   
   
   var dropdown = '';
   dropdown += '<div style=\"padding:10px;\">';
   dropdown += '<div id=\"jsfv_pagename\" style=\"margin-bottom:8px;font-size:10px;color:#292999;cursor:pointer;\" onclick=\"jQuery(\'#jsfv_pagename\').hide();jQuery(\'#jsfv_pagedropdown\').show();\">Name: ' + jsfv_name + '</div>';
   dropdown += '<div id=\"jsfv_pagedropdown\" style=\"margin-bottom:8px;font-size:10px;color:#292929;display:none;\"></div>';
   dropdown += '<div id=\"jsfv_meta_changes\" style=\"margin-bottom:5px;font-size:10px;color:red;display:none;\">';
   dropdown += '* Pending changes ';
   dropdown += '<span style=\"color:blue;font-weight:bold;cursor:pointer;\" onclick=\"if(confirm(\'Are you sure you want to undo recent changes?\')) location.href=\'jsf_visualbuilder.php?userid=' + jsfv_userid + '&token=' + jsfv_token + '&wd_id=' + encodeURIComponent(jsfv_wd_id) + '&name=' + encodeURIComponent(jsfv_name) + '\';\">Undo</span>';
   dropdown += '</div>';
   dropdown += '<div id=\"jsfv_meta_nochanges\" style=\"margin-bottom:5px;font-size:10px;\">No pending changes</div>';
   dropdown += '<div style=\"float:left;margin-right:5px;margin-bottom:5px;\">';
   dropdown += '<select onchange=\"jsfv_focusdiv(jQuery(\'#jsfv_metaselect\').val());jsfv_formatdivlayer(jsfv_wd_row_id);\" id=\"jsfv_metaselect\">';
   dropdown += '<option value=\"\"></option>';
   var rmn_arr = [];
   if(Boolean(jsondata) && Boolean(jsondata.rows) && jsondata.rows.length>0) {
      for(var i=0;i<jsondata.rows.length;i++) {
         var temppage = JSON.parse(jsondata.rows[i].value);
         jsfv_divs[jsondata.rows[i].wd_row_id] = temppage;
         if(temppage.type == 'header' || Boolean(temppage.infoonly) || Boolean(temppage.oright)) {
            infotext = temppage.infoonly;
            if(!Boolean(infotext)) infotext = '';
            if(Boolean(temppage.oright) && !isNaN(temppage.oright) && temppage.oright!='0') jsfv_totalht = parseInt(temppage.oright);
            infotext_id = jsondata.rows[i].wd_row_id;
         } else {         
            rmn_arr.push(jsondata.rows[i]);
            dropdown += '<option value=\"' + jsondata.rows[i].wd_row_id + '\">' + temppage.divname + '</option>';
         }
      }
   } else {
      jsfv_wd_row_id = '';
   }
   jsfv_dimensions = jsfv_getdimensions(jsfv_divs);
   
   var iter = 0;
   while(Boolean(rmn_arr) && rmn_arr.length>0 && iter<10) {
      rmn_arr = jsfv_addcomponents(rmn_arr);
      iter++;
   }
   jQuery('.noclick').click(function (e){e.preventDefault();});
   jQuery('.noclick').mousedown(function (e){e.preventDefault();});
   jQuery('.noclick').mouseup(function (e){e.preventDefault();});
   
   //dropdown += '<option value=\"new\">New Layer</option>';
   dropdown += '</select>';
   dropdown += '</div>';
   dropdown += '<div onclick=\"jsfv_focusdiv(\'new\');jsfv_savediv(\'new\');\" style=\"float:left;margin-bottom:5px;width:60px;font-size:10px;text-align:center;padding:3px;border:1px solid #555555;border-radius:3px;cursor:pointer;\">New Layer</div>';
   dropdown += '<div onclick=\"jsfv_createwdform();\" style=\"float:left;margin-left:5px;margin-bottom:5px;width:60px;font-size:10px;text-align:center;padding:3px;border:1px solid #555555;border-radius:3px;cursor:pointer;\">jData Record</div>';
   dropdown += '<div style=\"clear:both;\"></div>';
   dropdown += '<div id=\"jsfv_divinputs\" style=\"margin-top:10px;\"></div>';

   
   
   dropdown += '<div style=\"margin-top:15px;margin-bottom:15px;\">';
   dropdown += '<div ';
   //dropdown += 'onclick=\"jsfv_savediv(\'' + wd_row_id + '\');\" ';
   dropdown += 'onclick=\"jsfv_savechanged();\" ';
   dropdown += 'style=\"float:left;margin-right:10px;width:60px;padding:3px;font-size:8px;text-align:center;border:1px solid #333333;border-radius:3px;cursor:pointer;\" ';
   dropdown += '>';
   dropdown += 'Save</div>';
   dropdown += '<div ';
   //dropdown += 'onclick=\"jsfv_savediv(\'' + wd_row_id + '\');\" ';
   dropdown += 'onclick=\"jsfv_showdemo();\" ';
   dropdown += 'style=\"float:left;margin-right:10px;width:60px;padding:3px;font-size:8px;text-align:center;border:1px solid #333333;border-radius:3px;cursor:pointer;\" ';
   dropdown += '>';
   dropdown += 'See Demo</div>';
   dropdown += '<div style=\"clear:both;\"></div>';
   dropdown += '</div>';
      
   
   // Allow for saving information
   dropdown += '<div style=\"margin-top:15px;margin-bottom:15px;\">';
   dropdown += '<div style=\"font-size:12px;margin-bottom:2px;\">Comments</div>';
   dropdown += '<textarea onkeyup=\"event.stopPropagation();jsfv_changeinfotext=true;\" data-id=\"' + infotext_id + '\" id=\"jsfv_infoonly_txt\" style=\"width:240px;height:100px;font-size:12px;\">';
   //dropdown += jsfpb_convertback(infotext);
   dropdown += jsfpb_convertbackinput(infotext);
   dropdown += '</textarea>';
   dropdown += '</div>';
   
   
   dropdown += jsfpb_togglehtml('Additional Configuration','jsfv_extrapagemeta');
   dropdown += '<div id=\"jsfv_extrapagemeta\" style=\"display:none;\">';
   
   
   dropdown += '<div style=\"margin-top:15px;margin-bottom:15px;\">';
   dropdown += '<div ';
   dropdown += 'onclick=\"jsfv_copylayer();\" ';
   dropdown += 'style=\"margin-right:10px;width:140px;padding:5px;font-size:10px;text-align:center;border:1px solid #333333;border-radius:5px;cursor:pointer;\" ';
   dropdown += '>';
   dropdown += 'Copy From Another Page</div>';
   dropdown += '<div style=\"clear:both;\"></div>';
   dropdown += '</div>';
   
   
   
   // Allow for saving information
   dropdown += '<div style=\"margin-top:15px;margin-bottom:15px;\">';
   dropdown += '<div style=\"float:left;font-size:12px;margin-bottom:2px;margin-right:15px;\">Relative Height</div>';
   dropdown += '<input type=\"text\" onkeyup=\"event.stopPropagation();jsfv_changeinfotext=true;\" data-id=\"' + infotext_id + '\" id=\"jsfv_oright_txt\" style=\"float:left;width:90px;font-size:12px;\" value=\"' + jsfv_totalht + '\">';
   dropdown += '<div style=\"clear:both;\"></div>';
   dropdown += '</div>';
   
   dropdown += '<div style=\"margin-top:15px;margin-bottom:15px;\">';
   dropdown += '<div style=\"float:left;margin-right:12px;\">';
   dropdown += '<input type=\"text\" style=\"width:100px;font-size:12px;\" id=\"jsfv_copy_txtfld\" value=\"' + jsfv_name + ' Copy\" onkeyup=\"event.stopPropagation();\">';
   dropdown += '</div>';
   dropdown += '<div ';
   dropdown += 'onclick=\"var newname=jQuery(\'#jsfv_copy_txtfld\').val();if(Boolean(newname)) jsfv_makecopy(newname); else alert(\'Please enter a valid name for your new page.\');\" ';
   dropdown += 'style=\"float:left;margin-right:10px;width:70px;padding:3px;font-size:8px;text-align:center;border:1px solid #333333;border-radius:3px;cursor:pointer;\" ';
   dropdown += '>';
   dropdown += 'Copy Page</div>';
   dropdown += '<div style=\"clear:both;\"></div>';
   dropdown += '</div>';
   
   
   
   dropdown += '<div ';
   //dropdown += 'onclick=\"jsfv_savediv(\'' + wd_row_id + '\');\" ';
   dropdown += 'onclick=\"jsfv_removethispage();\" ';
   dropdown += 'style=\"margin-top:10px;margin-bottom:10px;width:80px;padding:5px;font-size:10px;text-align:center;border:1px solid #333333;background-color:#DD9999;border-radius:5px;cursor:pointer;\" ';
   dropdown += '>';
   dropdown += 'Delete this page</div>';
   
   
   dropdown += '</div>';
   
   
   
   
   dropdown += '</div>';
   jQuery('#jsfv_meta_inner').html(dropdown);
   jsfv_grouplayersinput();
   
   jsfv_getallpagenames();
   jsfv_checkbackup();
   
   //alert('after getting rows: ' + JSON.stringify(jsfv_divs));
   //alert('current row: ' + jsfv_wd_row_id);
   
   if(Boolean(jsfv_wd_row_id)) {
      // focus div needst o know that we reloaded, so remove 
      // the current layer id because it's reloaded
      var tempid = jsfv_wd_row_id;
      jsfv_wd_row_id = '';
      jsfv_focusdiv(tempid);
   }
}

function jsfv_drawresize(showplus,wd,thick,color,oc) {
   var str = '<div style=\"';
   str += 'width:' + wd + 'px;height:' + wd + 'px;';
   str += 'position:relative;overflow:hidden;';
   str += 'cursor:pointer;\" ';
   str += 'onclick=\"' + oc + '\">';
   
   str += '<div style=\"position:absolute;';
   str += 'left:0px;top:' + (Math.round((wd - thick)/2)) + 'px;';
   str += 'width:' + wd + 'px;height:' + thick + 'px;';
   str += 'background-color:' + color + ';overflow:hidden;\">';
   str += '</div>';
   
   if(Boolean(showplus)) {
      str += '<div style=\"position:absolute;';
      str += 'top:0px;left:' + (Math.round((wd - thick)/2)) + 'px;';
      str += 'height:' + wd + 'px;width:' + thick + 'px;';
      str += 'background-color:' + color + ';overflow:hidden;\">';
      str += '</div>';
   }
   
   str += '</div>';
   
   return str;
}

function jsfv_addcomponents(rows) {
   var rmn_arr = [];
   
   for(var i=0;i<rows.length;i++) {
      var temppage = JSON.parse(rows[i].value);
      
      // Give default name if none exists
      if(!Boolean(temppage.divname)) temppage.divname = 'Layer ' + (i+1);
      
      // Determine where to add this div
      var tempdivid = 'jsfv_draw_inner';
      if(Boolean(temppage.parent)) tempdivid = 'jsfv_' + temppage.parent;
      
      // If parent element is not created yet, move along, come back to it
      if(jQuery('#' + tempdivid).length == 0) {
         rmn_arr.push(rows[i]);
      } else {
         var str = jsfv_getdivlayer(rows[i].wd_row_id);
         jQuery('#' + tempdivid).append(str);
         jsfv_formatdivlayer(rows[i].wd_row_id);
         
         var mvdiv = document.getElementById('jsfv_' + rows[i].wd_row_id + '_outer');
         mvdiv.addEventListener("mousedown", function (e) {e.stopPropagation();jsfv_actionxy('down', e);}, false);
          
         var rsdiv = document.getElementById('jsfv_' + rows[i].wd_row_id + '_resize');	 
         rsdiv.addEventListener("mousedown", function (e) {e.stopPropagation();jsfv_actionxy('rsdown', e);}, false);
         
         var mvdiv = document.getElementById('jsfv_' + rows[i].wd_row_id + '_edit');
         mvdiv.addEventListener("mousedown", function (e) {e.stopPropagation();}, false);          
         mvdiv.addEventListener("mouseup", function (e) {e.stopPropagation();}, false);
         mvdiv.addEventListener("mousemove", function (e) {e.stopPropagation();}, false);   
         mvdiv.addEventListener("mouseout", function (e) {e.stopPropagation();}, false);        
      }
      
   }
   return rmn_arr;
}

function jsfv_showdemo() {
   // first clear out the currently selected layer (if here is one)
   jQuery('#jsfv_metaselect').val('');
   jsfv_focusdiv('');
   
   // Next, make sure the latest data is loaded
   window.localStorage.clear();
   
   // Set the correct jdata table name
   jsfpb_wdname = jsfv_wd_id;
   
   // initialize the screen
   jQuery('#jsfv_draw_demo_inner').html('');
   jQuery('#jsfv_draw_demo').show();
   jsfpb_drawvisualcomponents('jsfv_draw_demo_inner',jsfv_name);
}

// Create display of a div for screen
function jsfv_getdivlayer(wd_row_id) {
   var temppage = jsfv_divs[wd_row_id];
   //alert('txt: ' + temppage.txt);
   
   var str = '';
   str += '<div';
   str += ' id=\"jsfv_' + wd_row_id + '_outer\"';
   str += ' data-id=\"' + wd_row_id + '\"';
   str += '>';

   str += '<div ';
   str += ' class=\"jsfv_div' + temppage.type + ' jsfv_divlayer noclick\"';
   str += ' id=\"jsfv_' + wd_row_id + '\"';
   str += ' data-id=\"' + wd_row_id + '\"';
   str += '>';
   str += '<img';
   str += ' id=\"jsfv_' + wd_row_id + '_imgfld\"';
   str += ' class=\"noclick\"';
   str += ' src=\"\"';
   str += ' data-id=\"' + wd_row_id + '\"';
   str += ' style=\"z-index:1;position:absolute;left:0px;top:0px;\"';
   str += '>';
   
   str += '<textarea';
   str += ' data-id=\"' + wd_row_id + '\"';
   str += ' id=\"jsfv_' + wd_row_id + '_edit\"';
   str += ' class=\"jsfv_inline_edit\"';
   str += ' style=\"z-index:20;display:none;resize:none;outline:none;border:1px solid #676767;overflow:auto;white-space:pre;\"';
   str += ' onkeyup=\"event.stopPropagation();jQuery(\'#jsfv_div_txt\').val(jQuery(\'#jsfv_' + wd_row_id + '_edit\').val());jsfv_changediv();\"';
   str += '>';
   str += '</textarea>';
   
   str += '<div';
   str += ' data-id=\"' + wd_row_id + '\"';
   str += ' id=\"jsfv_' + wd_row_id + '_txtfld\"';
   str += ' class=\"noclick\"';
   str += ' style=\"z-index:2;\"';
   str += '>';
   str += '</div>';
   
   str += '<div';
   str += ' class=\"jsfv_divresize\"';
   str += ' data-id=\"' + wd_row_id + '\"';
   str += ' id=\"jsfv_' + wd_row_id + '_resize\"';
   str += ' style=\"z-index:4;position:absolute;bottom:0px;right:0px;width:16px;height:16px;background-image:URL(/jsfimages/resize.png);overflow:hidden;display:none;cursor:nwse-resize;\"';
   str += '>';
   str += '</div>';

   str += '</div>';
   str += '</div>';
   //alert('returning: ' + str);
   return str;
}

// Create display of a div for screen
function jsfv_formatdivlayer(wd_row_id) {
   var temppage = {};
   if(Boolean(jsfv_divs[wd_row_id])) temppage = jsfv_divs[wd_row_id];
   else temppage = jsfv_starterdivobj();
   
   //alert('txt: ' + temppage.txt);
   
   jQuery('#jsfv_' + wd_row_id + '_edit').hide();
   
   jQuery('#jsfv_' + wd_row_id + '_outer').css('position','absolute');
   jQuery('#jsfv_' + wd_row_id + '_outer').css('left',jsfv_convertonheight(temppage.lf) + 'px');
   jQuery('#jsfv_' + wd_row_id + '_outer').css('top',jsfv_convertonheight(temppage.tp) + 'px');
   
   jQuery('#jsfv_' + wd_row_id).css('border','1px solid #FFFFFF');
   
   var twd = jsfv_convertonheight(temppage.wd);
   var tht = jsfv_convertonheight(temppage.ht);
   if(twd<3) twd = 3;
   if(tht!='auto' && tht<3) tht = 3;
   
   jQuery('#jsfv_' + wd_row_id + '_outer').css('width',twd + 'px');
   
   if(tht!='auto') jQuery('#jsfv_' + wd_row_id + '_outer').css('height',tht + 'px').css('overflow','hidden');
   else jQuery('#jsfv_' + wd_row_id + '_outer').css('min-height',(jsfv_convertonheight(jsfv_dimensions.maxbot) - jsfv_convertonheight(temppage.tp) + 50) + 'px');
   
   if(Boolean(temppage.fsz)) jQuery('#jsfv_' + wd_row_id + '_outer').css('font-size',jsfv_convertonheight(temppage.fsz) + 'px');
   else jQuery('#jsfv_' + wd_row_id + '_outer').css('font-size','inherit');
   
   if(Boolean(temppage.zindex)) jQuery('#jsfv_' + wd_row_id + '_outer').css('z-index',temppage.zindex);
   else jQuery('#jsfv_' + wd_row_id + '_outer').css('z-index','1');
   
   if(Boolean(temppage.ffam)) jQuery('#jsfv_' + wd_row_id + '_outer').css('font-family',temppage.ffam);
   else jQuery('#jsfv_' + wd_row_id + '_outer').css('font-family','Arial');
   
   if(Boolean(temppage.fclr)) jQuery('#jsfv_' + wd_row_id + '_outer').css('color',temppage.fclr);
   else jQuery('#jsfv_' + wd_row_id + '_outer').css('color','#000000');
   
   if(Boolean(temppage.fbld) && temppage.fbld=='1') jQuery('#jsfv_' + wd_row_id + '_outer').css('font-weight','bold');
   else if(Boolean(temppage.fbld) && temppage.fbld=='2') jQuery('#jsfv_' + wd_row_id + '_outer').css('font-weight','100');
   else jQuery('#jsfv_' + wd_row_id + '_outer').css('font-weight','normal');
   
   if(Boolean(temppage.fund) && temppage.fund=='1') jQuery('#jsfv_' + wd_row_id + '_outer').css('text-decoration','underline');
   else jQuery('#jsfv_' + wd_row_id + '_outer').css('text-decoration','none');
   
   if(Boolean(temppage.fitl) && temppage.fitl=='1') jQuery('#jsfv_' + wd_row_id + '_outer').css('font-style','italic');
   else jQuery('#jsfv_' + wd_row_id + '_outer').css('font-style','none');
   
   if(Boolean(temppage.faln)) jQuery('#jsfv_' + wd_row_id + '_outer').css('text-align',temppage.faln);
   else jQuery('#jsfv_' + wd_row_id + '_outer').css('text-align','left');
   
   if(Boolean(temppage.opacity)) jQuery('#jsfv_' + wd_row_id + '_outer').css('opacity',temppage.opacity);
   else jQuery('#jsfv_' + wd_row_id + '_outer').css('opacity','1');
   
   if(Boolean(temppage.bgclr)) jQuery('#jsfv_' + wd_row_id + '_outer').css('background-color',temppage.bgclr);
   else jQuery('#jsfv_' + wd_row_id + '_outer').css('background-color','transparent');
   
   if(Boolean(temppage.rad)) jQuery('#jsfv_' + wd_row_id + '_outer').css('border-radius',jsfv_convertonheight(temppage.rad) + 'px');
   else jQuery('#jsfv_' + wd_row_id + '_outer').css('border-radius','0px');
   
   var txtstr = '';
   if(Boolean(temppage.txt)) txtstr += jsfpb_convertdisplay(temppage.txt);

   jQuery('#jsfv_div_classname_input').hide();
   jQuery('#jsfv_div_pad_input').hide();
   jQuery('#jsfv_div_fsz_input').hide();
   jQuery('#jsfv_div_fclr_input').hide();
   jQuery('#jsfv_div_ffam_input').hide();
   jQuery('#jsfv_div_faln_input').hide();
   jQuery('#jsfv_div_fbld_input').hide();
   jQuery('#jsfv_div_fund_input').hide();
   jQuery('#jsfv_div_fitl_input').hide();
   
   jQuery('#jsfv_div_wdid_input').hide();
   jQuery('#jsfv_div_fieldid_input').hide();
   jQuery('#jsfv_div_wdtype_input').hide();
   jQuery('#jsfv_div_section_input').hide();
   
   jQuery('#jsfv_div_rqd_input').hide();
   jQuery('#jsfv_div_tabi_input').hide();
   
   if(Boolean(temppage.type) && (temppage.type=='textbox' || temppage.type=='textarea' || temppage.type=='password' || temppage.type=='dropdown' || temppage.type=='statedropdown' || temppage.type=='searchbox')) {
      jQuery('#jsfv_' + wd_row_id).css('border','1px solid #000000');
      jQuery('#jsfv_' + wd_row_id).addClass('jsfv_divlayer_input');
      
      jQuery('#jsfv_' + wd_row_id + '_outer').css('color','#999999');
      jQuery('#jsfv_' + wd_row_id + '_outer').css('font-style','italic');
      
      jQuery('#jsfv_' + wd_row_id + '_txtfld').css('padding','2px');
      jQuery('#jsfv_div_rqd_input').show();
      jQuery('#jsfv_div_tabi_input').show();
      
      if(temppage.type=='searchbox') jQuery('#jsfv_div_wdid_input').show();

   } else if(Boolean(temppage.type) && temppage.type=='youtube') {
      //keep it simple if it's just a video reference
      txtstr = 'YouTube Video ' + txtstr;
      jQuery('#jsfv_' + wd_row_id + '_outer').css('color','#000000');
      jQuery('#jsfv_' + wd_row_id + '_outer').css('background-color','#888888');
      jQuery('#jsfv_' + wd_row_id + '_txtfld').css('text-align','center');
      jQuery('#jsfv_' + wd_row_id + '_txtfld').css('font-size','20px');
      jQuery('#jsfv_' + wd_row_id + '_txtfld').css('color','#FFFFFF');
      jQuery('#jsfv_' + wd_row_id + '_txtfld').css('padding','20px');
      
   } else if(Boolean(temppage.type) && temppage.type=='code') {
      jQuery('#jsfv_' + wd_row_id).css('border','1px dotted #CCCCCC');
      jQuery('#jsfv_' + wd_row_id).removeClass('jsfv_divlayer_input');
      
      jQuery('#jsfv_' + wd_row_id + '_outer').css('color','#CCCCCC');
      jQuery('#jsfv_' + wd_row_id + '_outer').css('font-weight','bold');
      
      jQuery('#jsfv_' + wd_row_id + '_txtfld').css('padding','2px');
      
      txtstr = 'Code Block';
   } else if(Boolean(temppage.type) && temppage.type=='wdata') {
      jQuery('#jsfv_' + wd_row_id).css('border','1px dotted #CCCCCC');
      jQuery('#jsfv_' + wd_row_id).removeClass('jsfv_divlayer_input');
      
      txtstr = 'jData: ' + temppage.wd_id + ' field: ' + temppage.field_id;
      
      jQuery('#jsfv_div_wdid_input').show();
      jQuery('#jsfv_div_fieldid_input').show();
      jQuery('#jsfv_div_wdtype_input').show();
   } else if(Boolean(temppage.type) && temppage.type=='jdataform') {
      jQuery('#jsfv_div_wdid_input').show();
      jQuery('#jsfv_div_section_input').show();
      jQuery('#jsfv_div_wdtype_input').show();
   } else if(Boolean(temppage.type) && temppage.type=='jdatacustomlist') {
      txtstr = '<span style=\"color:#7777FF;font-size:8px;font-weight:bold;opacity:0.9;\">jData Record List</span>';
      jQuery('#jsfv_div_wdid_input').show();
      jQuery('#jsfv_' + wd_row_id).css('border','1px dotted #9999FF');
   } else if(Boolean(temppage.type) && temppage.type=='jdatalist') {
      jQuery('#jsfv_div_wdid_input').show();
   } else if(Boolean(temppage.type) && temppage.type=='user') {
      jQuery('#jsfv_' + wd_row_id).css('border','1px dotted #AAAAFF');
      jQuery('#jsfv_' + wd_row_id).removeClass('jsfv_divlayer_input');
      
      jQuery('#jsfv_' + wd_row_id + '_outer').css('color','#CCCCCC');
      jQuery('#jsfv_' + wd_row_id + '_outer').css('font-weight','bold');
      
      jQuery('#jsfv_' + wd_row_id + '_txtfld').css('padding','2px');
      
      txtstr = 'USER: ' + temppage.field_id;
      
      jQuery('#jsfv_div_fieldid_input').show();
      jQuery('#jsfv_div_wdtype_input').show();
   } else {
      jQuery('#jsfv_' + wd_row_id).removeClass('jsfv_divlayer_input');
      
      jQuery('#jsfv_div_classname_input').show();
      jQuery('#jsfv_div_pad_input').show();
      jQuery('#jsfv_div_fsz_input').show();
      jQuery('#jsfv_div_fclr_input').show();
      jQuery('#jsfv_div_ffam_input').show();
      jQuery('#jsfv_div_faln_input').show();
      jQuery('#jsfv_div_fbld_input').show();
      jQuery('#jsfv_div_fund_input').show();
      jQuery('#jsfv_div_fitl_input').show();
   }
   jQuery('#jsfv_' + wd_row_id).css('position','relative');
   jQuery('#jsfv_' + wd_row_id).css('width',(twd - 2) + 'px');
   
   if(tht!='auto') jQuery('#jsfv_' + wd_row_id).css('height',(tht - 2) + 'px').css('overflow','hidden');
   else jQuery('#jsfv_' + wd_row_id).css('min-height',(jsfv_convertonheight(jsfv_dimensions.maxbot) - jsfv_convertonheight(temppage.tp) + 48) + 'px');
   
   if(!Boolean(temppage.img)) temppage.img = '';
   //alert('image: ' + temppage.img);
   jQuery('#jsfv_' + wd_row_id + '_imgfld').attr('src',jsfpb_replaceAll('http:','https:',temppage.img));
   //jQuery('#jsfv_' + wd_row_id + 'imgfld').attr('src',temppage.img);
   jQuery('#jsfv_' + wd_row_id + '_imgfld').addClass('noclick');
   jQuery('#jsfv_' + wd_row_id + '_imgfld').css('max-width',(twd - 2) + 'px');
   //alert('exist? ' + jQuery('#jsfv_' + wd_row_id + 'imgfld').length);
   
   if(tht!='auto') jQuery('#jsfv_' + wd_row_id + 'imgfld').css('max-height',(tht - 2) + 'px');
   else jQuery('#jsfv_' + wd_row_id + 'imgfld').css('min-height',(jsfv_convertonheight(jsfv_dimensions.maxbot) - jsfv_convertonheight(temppage.tp) + 2) + 'px');
   
   jQuery('#jsfv_' + wd_row_id + '_txtfld').addClass('noclick');
   jQuery('#jsfv_' + wd_row_id + '_txtfld').css('z-index','2');
   if(Boolean(temppage.pad)) jQuery('#jsfv_' + wd_row_id + '_txtfld').css('padding',jsfv_convertonheight(temppage.pad) + 'px');

   //if(temppage.ht!='auto') jQuery('#jsfv_' + wd_row_id + '_resize').show();
   //else jQuery('#jsfv_' + wd_row_id + '_resize').hide();
   jQuery('#jsfv_' + wd_row_id + '_resize').hide();
   
   jQuery('#jsfv_' + wd_row_id + '_txtfld').html(txtstr);
   
   
   // If we're dealing with the currently selected div, do this
   if(wd_row_id==jsfv_wd_row_id) {
      if(temppage.ht!='auto') jQuery('#jsfv_' + wd_row_id + '_resize').show();
      jQuery('#jsfv_' + wd_row_id).css('border','1px dashed #CC2222');
      jQuery('#jsfv_' + wd_row_id + '_outer').css('z-index','999');
   
      if(Boolean(temppage.parent)) {
         jQuery('#jsfv_' + temppage.parent).css('border','1px dashed #999999');
      }
      
      if(Boolean(temppage.type) && temppage.type == 'code') {
         jQuery('#jsfv_' + wd_row_id + '_edit').css('position','absolute');
         jQuery('#jsfv_' + wd_row_id + '_edit').css('top','22px');
         jQuery('#jsfv_' + wd_row_id + '_edit').css('left','2px');
         var eht = jsfv_convertonheight(temppage.ht) - 45;
         if(!Boolean(temppage.ht) || temppage.ht=='auto') eht = 200;
         jQuery('#jsfv_' + wd_row_id + '_edit').css('height',eht + 'px');
         jQuery('#jsfv_' + wd_row_id + '_edit').css('width',(jsfv_convertonheight(temppage.wd) - 10) + 'px');
         jQuery('#jsfv_' + wd_row_id + '_edit').css('left','2px');
         if(!Boolean(temppage.txt)) temppage.txt = '';
         jQuery('#jsfv_' + wd_row_id + '_edit').val(jsfpb_convertbackinput(temppage.txt));
         jQuery('#jsfv_' + wd_row_id + '_edit').show();
         
         jQuery('#jsfv_' + wd_row_id + '_outer').css('color','#000000');
         jQuery('#jsfv_' + wd_row_id + '_outer').css('background-color','#CCCCCC');
      }
   }
   
}

// Set properties of a div layer - position it
//function jsfv_resetdivlayer(wd_row_id) {

// Set the selection box to a particular div
// - show inputs for div
// - highlight div in draw area
function jsfv_focusdiv(wd_row_id,lf,tp,wd,ht) {
   // Set the new current div id
   var prev_id = jsfv_wd_row_id;
   jsfv_wd_row_id = wd_row_id;
   
   var temppage;
   
   // Reset the previous div's properties
   if(Boolean(prev_id) && prev_id!='new' && prev_id != wd_row_id) {
      temppage = jsfv_divs[prev_id];
      if(Boolean(temppage)) {
         //alert('resetting old div: ' + jsfv_wd_row_id + ' new div will be: ' + wd_row_id);
         if(Boolean(temppage.parent)) jsfv_formatdivlayer(temppage.parent);
         jsfv_formatdivlayer(prev_id);
      }
   }
   
   //alert('here');
   // Update the right (draw side)
   temppage = {};
   if(Boolean(wd_row_id)) {
      temppage = jsfv_divs[wd_row_id];
      if(!Boolean(temppage)) temppage = jsfv_starterdivobj(lf,tp,wd,ht);

      if(wd_row_id != prev_id || wd_row_id=='new') {
         // Update the left (input side) and then reformat divs afterwards
         
         // Clear out left-hand inputs
         jQuery('#jsfv_divinputs').html('');
         
         var sel = '';
         var str = '';
         
         str += '<div style=\"padding:5px;\">';

         
         if(!Boolean(temppage.divname)) temppage.divname = '';
         str += '<div style=\"margin-bottom:4px;\">';
         str += '<div style=\"float:left;width:70px;text-align:right;margin-right:10px;font-size:12px;\">';
         str += 'Layer Name';
         str += '</div>';
         str += '<div style=\"float:left;width:170px;text-align:left;\">';
         str += '<input id=\"jsfv_div_name\" onkeyup=\"event.stopPropagation();jsfv_changediv();\" type=\"text\" style=\"width:150px;font-size:12px;\" value=\"' + temppage.divname + '\">';
         str += '</div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
         
         
         if(!Boolean(temppage.type)) temppage.type = '';
         str += '<div id=\"jsfv_div_type_input\" style=\"margin-bottom:4px;\">';
         str += '<div style=\"float:left;width:70px;text-align:right;margin-right:10px;font-size:12px;\">';
         str += 'Type';
         str += '</div>';
         str += '<div style=\"float:left;width:170px;text-align:left;\">';
         str += '<select id=\"jsfv_div_type\" onchange=\"jsfv_changediv();\">';
         sel = '';
         if(temppage.type=='') sel = ' SELECTED';
         str += '<option value=\"\"' + sel + '>Text</option>';
         sel = '';
         if(temppage.type=='textbox') sel = ' SELECTED';
         str += '<option value=\"textbox\"' + sel + '>Input Textbox</option>';
         sel = '';
         if(temppage.type=='textarea') sel = ' SELECTED';
         str += '<option value=\"textarea\"' + sel + '>Input Text Area</option>';
         sel = '';
         if(temppage.type=='searchbox') sel = ' SELECTED';
         str += '<option value=\"searchbox\"' + sel + '>Input Search Textbox</option>';
         sel = '';
         if(temppage.type=='password') sel = ' SELECTED';
         str += '<option value=\"password\"' + sel + '>Password Input</option>';
         sel = '';
         if(temppage.type=='dropdown') sel = ' SELECTED';
         str += '<option value=\"dropdown\"' + sel + '>Dropdown List</option>';
         sel = '';
         if(temppage.type=='statedropdown') sel = ' SELECTED';
         str += '<option value=\"statedropdown\"' + sel + '>State Dropdown List</option>';
         sel = '';
         if(temppage.type=='youtube') sel = ' SELECTED';
         str += '<option value=\"youtube\"' + sel + '>YouTube Video</option>';
         sel = '';
         if(temppage.type=='code') sel = ' SELECTED';
         str += '<option value=\"code\"' + sel + '>Code Block</option>';
         sel = '';
         if(temppage.type=='user') sel = ' SELECTED';
         str += '<option value=\"user\"' + sel + '>User Field</option>';
         sel = '';
         if(temppage.type=='wdata') sel = ' SELECTED';
         str += '<option value=\"wdata\"' + sel + '>jData Field</option>';
         sel = '';
         if(temppage.type=='jdataform') sel = ' SELECTED';
         str += '<option value=\"jdataform\"' + sel + '>jData Form</option>';
         sel = '';
         if(temppage.type=='jdatacustomlist') sel = ' SELECTED';
         str += '<option value=\"jdatacustomlist\"' + sel + '>jData Custom Record List</option>';
         str += '</select>';
         str += '</div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
      
         
         var rqd = '';
         if(Boolean(temppage.rqd) && temppage.rqd == '1') rqd = ' CHECKED';
         str += '<div id=\"jsfv_div_rqd_input\" style=\"margin-bottom:4px;\">';
         str += '<div style=\"margin-left:110px;width:160px;font-size:12px;\">';
         str += '<input onclick=\"jsfv_changediv();\" id=\"jsfv_div_rqd\" type=\"checkbox\" value=\"1\"' + rqd + '> Required';
         str += '</div>';
         str += '</div>';
         
         if(!Boolean(temppage.tabi)) temppage.tabi='1';
         str += '<div id=\"jsfv_div_tabi_input\" style=\"margin-bottom:4px;\">';
         str += '<div style=\"float:left;width:100px;text-align:right;margin-right:10px;font-size:12px;\">';
         str += 'Input tab order';
         str += '</div>';
         str += '<div style=\"float:left;width:140px;text-align:left;\">';
         str += '<input id=\"jsfv_div_tabi\" onkeyup=\"event.stopPropagation();jsfv_changediv();\" type=\"text\" style=\"width:80px;font-size:12px;\" value=\"' + temppage.tabi + '\">';
         str += '</div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
         
         
         

         
         str += '<div style=\"margin-top:7px;margin-bottom:15px;\">';
         str += '<div ';
         //str += 'onclick=\"jsfv_savediv(\'' + wd_row_id + '\');\" ';
         str += 'onclick=\"jsfv_savechanged();\" ';
         str += 'style=\"float:left;margin-right:15px;width:80px;padding:5px;font-size:10px;text-align:center;border:1px solid #333333;border-radius:4px;cursor:pointer;\" ';
         str += '>';
         str += 'Save</div>';
         str += '<div ';
         //str += 'onclick=\"jsfv_savediv(\'' + wd_row_id + '\');\" ';
         str += 'onclick=\"jsfv_showdemo();\" ';
         str += 'style=\"float:left;margin-right:10px;width:80px;padding:5px;font-size:10px;text-align:center;border:1px solid #333333;border-radius:4px;cursor:pointer;\" ';
         str += '>';
         str += 'See Demo</div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
         
         
         
         
         
         
         
         // 4 cols of 70
         str += '<div style=\"margin-top:8px;margin-bottom:8px;\">';
         str += '<div style=\"float:left;width:66px;margin-right:5px;font-size:12px;\">';
         str += '<div>Left</div>';
         str += '<div><input id=\"jsfv_div_lf\" onkeyup=\"event.stopPropagation();jsfv_changediv();\" type=\"text\" style=\"width:50px;font-size:12px;\" value=\"' + jsfv_convertonheight(temppage.lf) + '\"></div>';
         str += '</div>';
         str += '<div style=\"float:left;width:66px;margin-right:5px;font-size:12px;\">';
         str += '<div>Top</div>';
         str += '<div><input id=\"jsfv_div_tp\" onkeyup=\"event.stopPropagation();jsfv_changediv();\" type=\"text\" style=\"width:50px;font-size:12px;\" value=\"' + jsfv_convertonheight(temppage.tp) + '\"></div>';
         str += '</div>';
         str += '<div style=\"float:left;width:66px;margin-right:5px;font-size:12px;\">';
         str += '<div>Width</div>';
         str += '<div><input id=\"jsfv_div_wd\" onkeyup=\"event.stopPropagation();jsfv_changediv();\" type=\"text\" style=\"width:50px;font-size:12px;\" value=\"' + jsfv_convertonheight(temppage.wd) + '\"></div>';
         str += '</div>';
         str += '<div style=\"float:left;width:66px;margin-right:5px;font-size:12px;\">';
         str += '<div>Height</div>';
         str += '<div><input id=\"jsfv_div_ht\" onkeyup=\"event.stopPropagation();jsfv_changediv();\" type=\"text\" style=\"width:50px;font-size:12px;\" value=\"' + jsfv_convertonheight(temppage.ht) + '\"></div>';
         str += '</div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
         
         
         // 4 cols of 70
         if(!Boolean(temppage.rad)) temppage.rad = '0';
         if(!Boolean(temppage.opacity)) temppage.opacity = '';
         if(!Boolean(temppage.hide)) temppage.hide = '0';
         str += '<div style=\"margin-top:8px;margin-bottom:8px;\">';
         str += '<div style=\"float:left;width:66px;margin-right:5px;font-size:12px;\">';
         str += '<div>Sequence</div>';
         str += '<div><input id=\"jsfv_div_zindex\" onkeyup=\"event.stopPropagation();jsfv_changediv();\" type=\"text\" style=\"width:50px;font-size:12px;\" value=\"' + temppage.zindex + '\"></div>';
         str += '</div>';
         str += '<div style=\"float:left;width:66px;margin-right:5px;font-size:12px;\">';
         str += '<div>Opacity</div>';
         str += '<div><input id=\"jsfv_div_opacity\" onkeyup=\"event.stopPropagation();jsfv_changediv();\" type=\"text\" style=\"width:50px;font-size:12px;\" value=\"' + temppage.opacity + '\"></div>';
         str += '</div>';
         str += '<div style=\"float:left;width:66px;margin-right:5px;font-size:12px;\">';
         str += '<div>Radius</div>';
         str += '<div><input id=\"jsfv_div_rad\" onkeyup=\"event.stopPropagation();jsfv_changediv();\" type=\"text\" style=\"width:50px;font-size:12px;\" value=\"' + jsfv_convertonheight(temppage.rad) + '\"></div>';
         str += '</div>';
         str += '<div style=\"float:left;width:66px;margin-right:5px;font-size:12px;\">';
         str += '<div>Hide?</div>';
         str += '<div><select id=\"jsfv_div_hide\" onchange=\"jsfv_changediv();\">';
         sel = '';
         if(temppage.hide=='0') sel = ' SELECTED';
         str += '<option value=\"0\"' + sel + '>No</option>';
         sel = '';
         if(temppage.hide=='1') sel = ' SELECTED';
         str += '<option value=\"1\"' + sel + '>Yes</option>';
         str += '</select></div>';
         str += '</div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
         
         

         
         /*
         str += '<div style=\"margin-bottom:4px;\">';
         str += '<div style=\"float:left;width:100px;text-align:right;margin-right:10px;font-size:12px;\">';
         str += 'Layer Sequence';
         str += '</div>';
         str += '<div style=\"float:left;width:140px;text-align:left;\">';
         str += '<input id=\"jsfv_div_zindex\" onkeyup=\"jsfv_changediv();\" type=\"text\" style=\"width:80px;font-size:12px;\" value=\"' + temppage.zindex + '\">';
         str += '</div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
         */
         
         if(!Boolean(temppage.bgclr)) temppage.bgclr = '';
         str += '<div style=\"margin-bottom:4px;\">';
         str += '<div style=\"float:left;width:100px;text-align:right;margin-right:10px;font-size:12px;\">';
         str += 'Background Color';
         str += '</div>';
         str += '<div style=\"float:left;width:120px;text-align:left;\">';
         str += '<input id=\"jsfv_div_bgclr\" onkeyup=\"event.stopPropagation();jsfv_changediv();jQuery(\'#jsfv_div_bgclr_sw\').css(\'background-color\',jQuery(\'#jsfv_div_bgclr\').val());\" type=\"text\" style=\"width:100px;font-size:12px;\" value=\"' + temppage.bgclr + '\">';
         str += '</div>';
         str += '<div id=\"jsfv_div_bgclr_sw\" style=\"float:left;width:20px;height:20px;overflow:hidden;background-color:' + temppage.bgclr + ';\"></div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
      
         /*
         if(!Boolean(temppage.rad)) temppage.rad = '0';
         str += '<div style=\"margin-bottom:4px;\">';
         str += '<div style=\"float:left;width:100px;text-align:right;margin-right:10px;font-size:12px;\">';
         str += 'Border Radius';
         str += '</div>';
         str += '<div style=\"float:left;width:140px;text-align:left;\">';
         str += '<input id=\"jsfv_div_rad\" onkeyup=\"jsfv_changediv();\" type=\"text\" style=\"width:80px;font-size:12px;\" value=\"' + jsfv_convertonheight(temppage.rad) + '\">';
         str += '</div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
         
         if(!Boolean(temppage.opacity)) temppage.opacity = '';
         str += '<div style=\"margin-bottom:4px;\">';
         str += '<div style=\"float:left;width:100px;text-align:right;margin-right:10px;font-size:12px;\">';
         str += 'Opacity Decimal';
         str += '</div>';
         str += '<div style=\"float:left;width:140px;text-align:left;\">';
         str += '<input id=\"jsfv_div_opacity\" onkeyup=\"jsfv_changediv();\" type=\"text\" style=\"width:80px;font-size:12px;\" value=\"' + temppage.opacity + '\">';
         str += '</div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
         
         if(!Boolean(temppage.hide)) temppage.hide = '0';
         str += '<div id=\"jsfv_div_hide_input\" style=\"margin-bottom:4px;\">';
         str += '<div style=\"float:left;width:100px;text-align:right;margin-right:10px;font-size:12px;\">';
         str += 'Hide from view?';
         str += '</div>';
         str += '<div style=\"float:left;width:140px;text-align:left;\">';
         str += '<select id=\"jsfv_div_hide\" onchange=\"jsfv_changediv();\">';
         sel = '';
         if(temppage.hide=='0') sel = ' SELECTED';
         str += '<option value=\"0\"' + sel + '>No</option>';
         sel = '';
         if(temppage.hide=='1') sel = ' SELECTED';
         str += '<option value=\"1\"' + sel + '>Yes</option>';
         str += '</select>';
         str += '</div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
         */
      
         
         str += '<div id=\"jsfv_div_wdid_input\" style=\"margin-bottom:4px;\">';
         str += '<div style=\"float:left;width:100px;text-align:right;margin-right:10px;font-size:12px;\">';
         str += 'jData ID';
         str += '</div>';
         str += '<div style=\"float:left;width:140px;text-align:left;\">';
         str += '<input id=\"jsfv_div_wdid\" onkeyup=\"event.stopPropagation();jsfv_changediv();\" type=\"text\" style=\"width:80px;font-size:12px;\" value=\"' + temppage.wd_id + '\">';
         str += '</div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
         
         str += '<div id=\"jsfv_div_section_input\" style=\"margin-bottom:4px;\">';
         str += '<div style=\"float:left;width:100px;text-align:right;margin-right:10px;font-size:12px;\">';
         str += 'jData Section';
         str += '</div>';
         str += '<div style=\"float:left;width:140px;text-align:left;\">';
         str += '<input id=\"jsfv_div_section\" onkeyup=\"event.stopPropagation();jsfv_changediv();\" type=\"text\" style=\"width:80px;font-size:12px;\" value=\"' + temppage.section + '\">';
         str += '</div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
         
         str += '<div id=\"jsfv_div_fieldid_input\" style=\"margin-bottom:4px;\">';
         str += '<div style=\"float:left;width:100px;text-align:right;margin-right:10px;font-size:12px;\">';
         str += 'Field ID';
         str += '</div>';
         str += '<div style=\"float:left;width:140px;text-align:left;\">';
         str += '<input id=\"jsfv_div_fieldid\" onkeyup=\"event.stopPropagation();jsfv_changediv();\" type=\"text\" style=\"width:80px;font-size:12px;\" value=\"' + temppage.field_id + '\">';
         str += '</div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
         
         if(!Boolean(temppage.wdtype)) temppage.wdtype = 'display';
         str += '<div id=\"jsfv_div_wdtype_input\" style=\"margin-bottom:4px;\">';
         str += '<div style=\"float:left;width:100px;text-align:right;margin-right:10px;font-size:12px;\">';
         str += 'jData Display Type';
         str += '</div>';
         str += '<div style=\"float:left;width:140px;text-align:left;\">';
         str += '<select id=\"jsfv_div_wdtype\" onchange=\"jsfv_changediv();\">';
         sel = '';
         if(temppage.wdtype=='display') sel = ' SELECTED';
         str += '<option value=\"display\"' + sel + '>Display Only</option>';
         sel = '';
         if(temppage.wdtype=='edit') sel = ' SELECTED';
         str += '<option value=\"edit\"' + sel + '>Display and edit</option>';
         sel = '';
         if(temppage.wdtype=='new') sel = ' SELECTED';
         str += '<option value=\"new\"' + sel + '>New data</option>';
         str += '</select>';
         str += '</div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
         
         
         //alert('div: ' + temppage.divname + ' txt: ' + temppage.txt);
         if(!Boolean(temppage.txt)) temppage.txt = '';
         str += '<div style=\"margin-bottom:4px;\">';
         str += '<div style=\"font-size:12px;margin-bottom:2px;\">Text</div>';
         str += '<div style=\"width:240px;text-align:left;\">';
         str += '<textarea onkeyup=\"event.stopPropagation();jsfv_changediv();\" id=\"jsfv_div_txt\" type=\"text\" style=\"height:60px;width:220px;font-size:12px;\">' + jsfpb_convertbackinput(temppage.txt) + '</textarea>';
         str += '</div>';
         str += '</div>';
      
         str += '<div id=\"jsfv_div_fsz_input\" style=\"margin-bottom:4px;\">';
         str += '<div style=\"float:left;width:100px;text-align:right;margin-right:10px;font-size:12px;\">';
         str += 'Font Size';
         str += '</div>';
         str += '<div style=\"float:left;width:140px;text-align:left;\">';
         str += '<input id=\"jsfv_div_fsz\" onkeyup=\"event.stopPropagation();jsfv_changediv();\" type=\"text\" style=\"width:80px;font-size:12px;\" value=\"' + jsfv_convertonheight(temppage.fsz) + '\">';
         str += '</div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
      
         var padval = '';
         if(Boolean(temppage.pad)) padval = jsfv_convertonheight(temppage.pad);
         str += '<div id=\"jsfv_div_pad_input\" style=\"margin-bottom:4px;\">';
         str += '<div style=\"float:left;width:100px;text-align:right;margin-right:10px;font-size:12px;\">';
         str += 'Padding';
         str += '</div>';
         str += '<div style=\"float:left;width:140px;text-align:left;\">';
         str += '<input id=\"jsfv_div_pad\" onkeyup=\"event.stopPropagation();jsfv_changediv();\" type=\"text\" style=\"width:80px;font-size:12px;\" value=\"' + padval + '\">';
         str += '</div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
      
         
         if(!Boolean(temppage.classname)) temppage.classname = '';
         str += '<div id=\"jsfv_div_classname_input\" style=\"margin-bottom:4px;\">';
         str += '<div style=\"float:left;width:100px;text-align:right;margin-right:10px;font-size:12px;\">';
         str += 'Class Name';
         str += '</div>';
         str += '<div style=\"float:left;width:140px;text-align:left;\">';
         str += '<input id=\"jsfv_div_classname\" onkeyup=\"event.stopPropagation();jsfv_changediv();\" type=\"text\" style=\"width:120px;font-size:12px;\" value=\"' + temppage.classname + '\">';
         str += '</div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
      
         if(!Boolean(temppage.ffam)) temppage.ffam = '';
         str += '<div id=\"jsfv_div_ffam_input\" style=\"margin-bottom:4px;\">';
         str += '<div style=\"float:left;width:100px;text-align:right;margin-right:10px;font-size:12px;\">';
         str += 'Font Family';
         str += '</div>';
         str += '<div style=\"float:left;width:140px;text-align:left;\">';
         str += '<input id=\"jsfv_div_ffam\" onkeyup=\"event.stopPropagation();jsfv_changediv();\" type=\"text\" style=\"width:120px;font-size:12px;\" value=\"' + temppage.ffam + '\">';
         str += '</div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
      
         if(!Boolean(temppage.fclr)) temppage.fclr = '#000000';
         str += '<div id=\"jsfv_div_fclr_input\" style=\"margin-bottom:4px;\">';
         str += '<div style=\"float:left;width:100px;text-align:right;margin-right:10px;font-size:12px;\">';
         str += 'Font Color';
         str += '</div>';
         str += '<div style=\"float:left;width:120px;text-align:left;\">';
         str += '<input id=\"jsfv_div_fclr\" onkeyup=\"event.stopPropagation();jsfv_changediv();jQuery(\'#jsfv_div_fclr_sw\').css(\'background-color\',jQuery(\'#jsfv_div_fclr\').val());\" type=\"text\" style=\"width:100px;font-size:12px;\" value=\"' + temppage.fclr + '\">';
         str += '</div>';
         str += '<div id=\"jsfv_div_fclr_sw\" style=\"float:left;width:20px;height:20px;overflow:hidden;background-color:' + temppage.fclr + ';\"></div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
      
         if(!Boolean(temppage.fbld)) temppage.fbld = '0';
         str += '<div id=\"jsfv_div_fbld_input\" style=\"margin-bottom:4px;\">';
         str += '<div style=\"float:left;width:100px;text-align:right;margin-right:10px;font-size:12px;\">';
         str += 'Font Weight';
         str += '</div>';
         str += '<div style=\"float:left;width:140px;text-align:left;\">';
         str += '<select id=\"jsfv_div_fbld\" onchange=\"jsfv_changediv();\">';
         sel = '';
         if(temppage.fbld=='0') sel = ' SELECTED';
         str += '<option value=\"0\"' + sel + '>Normal</option>';
         sel = '';
         if(temppage.fbld=='1') sel = ' SELECTED';
         str += '<option value=\"1\"' + sel + '>Bold</option>';
         sel = '';
         if(temppage.fbld=='2') sel = ' SELECTED';
         str += '<option value=\"2\"' + sel + '>Lite</option>';
         str += '</select>';
         str += '</div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
   
         var fund = '';
         if(Boolean(temppage.fund) && temppage.fund == '1') fund = ' CHECKED';
         str += '<div id=\"jsfv_div_fund_input\" style=\"margin-bottom:4px;\">';
         str += '<div style=\"margin-left:110px;width:160px;font-size:12px;\">';
         str += '<input onclick=\"jsfv_changediv();\" id=\"jsfv_div_fund\" type=\"checkbox\" value=\"1\"' + fund + '> Underline text';
         str += '</div>';
         str += '</div>';
      
         var fitl = '';
         if(Boolean(temppage.fitl) && temppage.fitl == '1') fitl = ' CHECKED';
         str += '<div id=\"jsfv_div_fitl_input\" style=\"margin-bottom:4px;\">';
         str += '<div style=\"margin-left:110px;width:160px;font-size:12px;\">';
         str += '<input onclick=\"jsfv_changediv();\" id=\"jsfv_div_fitl\" type=\"checkbox\" value=\"1\"' + fitl + '> Italicize';
         str += '</div>';
         str += '</div>';
      
         if(!Boolean(temppage.faln)) temppage.faln = 'left';
         str += '<div id=\"jsfv_div_faln_input\" style=\"margin-bottom:4px;\">';
         str += '<div style=\"float:left;width:100px;text-align:right;margin-right:10px;font-size:12px;\">';
         str += 'Font Alignment';
         str += '</div>';
         str += '<div style=\"float:left;width:140px;text-align:left;\">';
         str += '<select id=\"jsfv_div_faln\" onchange=\"jsfv_changediv();\">';
         sel = '';
         if(temppage.faln=='left') sel = ' SELECTED';
         str += '<option value=\"left\"' + sel + '>Left</option>';
         sel = '';
         if(temppage.faln=='center') sel = ' SELECTED';
         str += '<option value=\"center\"' + sel + '>Center</option>';
         sel = '';
         if(temppage.faln=='right') sel = ' SELECTED';
         str += '<option value=\"right\"' + sel + '>Right</option>';
         str += '</select>';
         str += '</div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
      
         
         
         str += '<div style=\"margin-bottom:4px;\">';
         str += '<div style=\"float:left;width:100px;text-align:right;margin-right:10px;font-size:12px;\">';
         str += 'Image';
         str += '</div>';
         str += '<div style=\"float:left;width:140px;text-align:left;\">';
         str += '<div id=\"jsfv_visualimgthumb\" style=\"width:140px;overflow:hidden;\">';
         if(Boolean(temppage.img)) {
            str += jsfpb_displayadminimg(135,80,temppage.img,'jsfv_visualimgthumb','jsfv_divs[\'' + jsfv_wd_row_id + '\'].img = \'\';jQuery(\'#jsfv_div_img\').val(\'\');jsfv_changediv();');
            //str += '<img src=\"' + temppage.img + '\" style=\"max-width:135px;max-height:60px;\">';
         }
         str += '</div>';
         str += '<div ';
         str += 'style=\"padding:3px;font-size:8px;text-align:center;border:1px solid #333333;border-radius:3px;width:70px;cursor:pointer;\" ';
         str += 'onclick=\"window.open(\'' + jsfpb_domain + 'jsfcode/uploadimage.php?imageonly=1&userid=9&token=9&prefix=jsfv&wd_id=visualimg&field_id=' + jsfv_wd_row_id + '\');\" ';
         str += '>Upload</div>';
         str += '</div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
         
         str += '<input id=\"jsfv_div_img\" type=\"hidden\" value=\"';
         if(Boolean(temppage.img)) str += temppage.img;
         str += '\">';
         
         
         var animate = '';
         var anihide = 'display:none;';
         if(Boolean(temppage.fin) || Boolean(temppage.fout) || Boolean(temppage.move) || Boolean(temppage.mvleft) || Boolean(temppage.mvtop)) {
            animate = ' CHECKED';
            anihide = '';
         }
         str += '<div style=\"margin-top:15px;\">';
         str += '<div style=\"font-size:12px;\">';
         str += '<input onclick=\"if(this.checked) jQuery(\'#jsfv_animatefields\').show(); else jQuery(\'#jsfv_animatefields\').hide();\" id=\"jsfv_animatechkbx\" type=\"checkbox\"' + animate + '> Add Animation';
         str += '</div>';
         str += '<div id=\"jsfv_animatefields\" style=\"margin-bottom:10px;' + anihide + '\">';
         
         if(!Boolean(temppage.fin)) temppage.fin='';
         str += '<div style=\"margin-bottom:4px;\">';
         str += '<div style=\"float:left;width:100px;text-align:right;margin-right:10px;font-size:12px;\">';
         str += 'Fade In (Seconds)';
         str += '</div>';
         str += '<div style=\"float:left;width:140px;text-align:left;\">';
         str += '<input id=\"jsfv_div_fin\" onkeyup=\"event.stopPropagation();jsfv_changediv();\" type=\"text\" style=\"width:80px;font-size:12px;\" value=\"' + temppage.fin + '\">';
         str += '</div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
         
         
         if(!Boolean(temppage.fout)) temppage.fout='';
         str += '<div style=\"margin-bottom:4px;\">';
         str += '<div style=\"float:left;width:100px;text-align:right;margin-right:10px;font-size:12px;\">';
         str += 'Fade Out (Seconds)';
         str += '</div>';
         str += '<div style=\"float:left;width:140px;text-align:left;\">';
         str += '<input id=\"jsfv_div_fout\" onkeyup=\"event.stopPropagation();jsfv_changediv();\" type=\"text\" style=\"width:80px;font-size:12px;\" value=\"' + temppage.fout + '\">';
         str += '</div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
         
         var move='';
         if(Boolean(temppage.move)) move = temppage.move;
         //alert('move: ' + move);
         var mvarr = move.split(';');
         //alert('move: ' + JSON.stringify(mvarr));
         for(var i=0;i<mvarr.length;i++) {
            //alert('i: ' + i + ' length: ' + mvarr.length);
            var currmv = [];
            if(Boolean(mvarr[i])) currmv = mvarr[i].split(',');
            var pos;
            if (i==0) pos = 'Starting ';
            else if (i==1) pos = '2nd ';
            else if (i==2) pos = '3rd ';
            else pos = (i+1) + 'th ';
            str += '<input id=\"jsfv_div_move' + i + '\" type=\"hidden\" value=\"yes\">';
            str += '<div style=\"margin-bottom:4px;\">';
            str += '<div style=\"float:left;width:100px;text-align:right;margin-right:10px;font-size:12px;\">';
            str += pos + 'Time (seconds)';
            str += '</div>';
            str += '<div style=\"float:left;width:140px;text-align:left;\">';
            str += '<input id=\"jsfv_div_movetm' + i + '\" onkeyup=\"event.stopPropagation();jsfv_changediv();\" type=\"text\" style=\"width:80px;font-size:12px;\" value=\"';
            if(Boolean(currmv[2])) str += currmv[2];
            str += '\">';
            str += '</div>';
            str += '<div style=\"clear:both;\"></div>';
            str += '</div>';
            str += '<input id=\"jsfv_div_move' + i + '\" type=\"hidden\" value=\"yes\">';
            str += '<div style=\"margin-bottom:4px;\">';
            str += '<div style=\"float:left;width:100px;text-align:right;margin-right:10px;font-size:12px;\">';
            str += pos + 'Left Position';
            str += '</div>';
            str += '<div style=\"float:left;width:140px;text-align:left;\">';
            str += '<input id=\"jsfv_div_movelf' + i + '\" onkeyup=\"event.stopPropagation();jsfv_changediv();\" type=\"text\" style=\"width:80px;font-size:12px;\" value=\"';
            if(Boolean(currmv[0])) str += jsfv_convertonheight(currmv[0]);
            str += '\">';
            str += '</div>';
            str += '<div style=\"clear:both;\"></div>';
            str += '</div>';
            str += '<div style=\"margin-bottom:4px;\">';
            str += '<div style=\"float:left;width:100px;text-align:right;margin-right:10px;font-size:12px;\">';
            str += pos + 'Top Position';
            str += '</div>';
            str += '<div style=\"float:left;width:140px;text-align:left;\">';
            str += '<input id=\"jsfv_div_movetp' + i + '\" onkeyup=\"event.stopPropagation();jsfv_changediv();\" type=\"text\" style=\"width:80px;font-size:12px;\" value=\"';
            if(Boolean(currmv[1])) str += jsfv_convertonheight(currmv[1]);
            str += '\">';
            str += '</div>';
            str += '<div style=\"clear:both;\"></div>';
            str += '</div>';
         }
         
         str += '</div>';
         str += '</div>';
         
         
         var parentchk = '';
         var parenthide = 'display:none;';
         if(Boolean(temppage.parent)) {
            parentchk = ' CHECKED';
            parenthide = '';
         }
         str += '<div style=\"margin-top:15px;\">';
         str += '<div style=\"font-size:12px;\">';
         str += '<input onclick=\"if(this.checked) jQuery(\'#jsfv_parentfield\').show(); else jQuery(\'#jsfv_parentfield\').hide();\" id=\"jsfv_parentchkbx\" type=\"checkbox\"' + parentchk + '> Group Element';
         str += '<div style=\"font-size:10px;color:red;\">* Changing the parent will automically save this page.</div>';
         str += '</div>';
         str += '<div id=\"jsfv_parentfield\" style=\"margin-bottom:10px;' + parenthide + '\">';
         str += '<div style=\"margin-bottom:4px;\">';
         str += '<div style=\"float:left;width:100px;text-align:right;margin-right:10px;font-size:12px;\">';
         str += 'Parent';
         str += '</div>';
         str += '<div style=\"float:left;width:140px;text-align:left;\">';
         str += '<select id=\"jsfv_div_parent\" onchange=\"jsfv_changerefresh=true;jsfv_changediv();\">';
         str += '<option value=\"\">No Parent</option>';
         for (var key in jsfv_divs) {
            if(key!=wd_row_id && jsfv_divs[key].type!='header') {
               sel = '';
               var selpage = jsfv_divs[key];
               if(Boolean(temppage.parent) && key == temppage.parent) sel = ' SELECTED';
               str += '<option value=\"' + key + '\"' + sel + '>' + selpage.divname + '</option>';
            }
         }
         str += '</select>';
         str += '</div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
         str += '</div>';
         str += '</div>';
         
         
         
         
         if(!Boolean(temppage.onclick)) temppage.onclick = '';
         str += '<div style=\"margin-bottom:4px;\">';
         str += '<div style=\"float:left;width:100px;text-align:right;margin-right:10px;font-size:12px;\">';
         str += 'On Click';
         str += '</div>';
         str += '<div style=\"float:left;width:140px;text-align:left;\">';
         str += '<input id=\"jsfv_div_onclick\" onkeyup=\"event.stopPropagation();jsfv_changediv();\" type=\"text\" style=\"width:120px;font-size:12px;\" value=\"' + temppage.onclick + '\">';
         str += '</div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
      

         
         
         
         
         
         str += '<div style=\"margin-top:15px;margin-bottom:15px;\">';
         str += '<div ';
         str += 'onclick=\"jsfv_wd_row_id=\'new\';jsfv_savediv(\'new\');\" ';
         str += 'style=\"float:left;margin-right:10px;width:60px;padding:3px;font-size:8px;text-align:center;border:1px solid #333333;border-radius:3px;cursor:pointer;\" ';
         str += '>';
         str += 'Copy Div</div>';
         str += '<div ';
         //str += 'onclick=\"jsfv_savediv(\'' + wd_row_id + '\');\" ';
         str += 'onclick=\"jsfv_removelayer(\'' + wd_row_id + '\');\" ';
         str += 'style=\"float:left;margin-right:10px;width:60px;padding:3px;font-size:8px;text-align:center;border:1px solid #333333;border-radius:3px;cursor:pointer;\" ';
         str += '>';
         str += 'Remove</div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
               
         str += '</div>';
         
         
         /*
         var tparent = temppage.parent;
         if(!Boolean(tparent)) tparent = '';
         str += '<div style=\"margin-top:15px;margin-bottom:15px;font-family:arial;font-size:10px;\">';
         str += '<div style=\"\">Move all peers of this layer</div>';
         str += '<div style=\"float:left;margin-right:5px;\">Right:</div>';
         str += '<div style=\"float:left;margin-right:20px;\"><input id=\"jsfv_moveright\" type=\"text\" style=\"width:30px;font-size:10px;\" value=\"0\" onkeyup=\"event.stopPropagation();\"></div>';
         str += '<div style=\"float:left;margin-right:5px;\">Down:</div>';
         str += '<div style=\"float:left;margin-right:20px;\"><input id=\"jsfv_movedown\" type=\"text\" style=\"width:30px;font-size:10px;\" value=\"0\" onkeyup=\"event.stopPropagation();\"></div>';
         str += '<div ';
         str += 'onclick=\"var lf=jQuery(\'#jsfv_moveright\').val();var tp=jQuery(\'#jsfv_movedown\').val();if(confirm(\'Are you sure you want to move all peer layers?\')) jsfv_movealllayersinparent(\'' + tparent + '\',lf,tp);\" ';
         str += 'style=\"float:left;margin-right:10px;width:60px;padding:3px;font-size:8px;text-align:center;border:1px solid #333333;border-radius:3px;cursor:pointer;\" ';
         str += '>';
         str += 'Move</div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
         */
         
         
         jQuery('#jsfv_divinputs').html(str);
      }
      
      
      // Automatically select this div in dropdown on left
      jQuery('#jsfv_metaselect').val(wd_row_id);
      if(Boolean(wd_row_id) && wd_row_id!='new') jsfv_formatdivlayer(wd_row_id);
      
      jQuery('.jsfv_lyrs').css('background-color','#f2f2f2');      
      jQuery('#jsfv_lyrs_' + wd_row_id).css('background-color','#BBDDEE');      
   }
   
   
}

function jsfv_starterdivobj(lf,tp,wd,ht) {
   if(!Boolean(lf)) lf = 20;
   if(!Boolean(tp)) tp = 20;
   if(!Boolean(wd)) wd = 280;
   if(!Boolean(ht)) ht = 200;
   temppage = {};
   temppage.divname = 'Layer ' + (Object.keys(jsfv_divs).length + 1);
   temppage.lf = jsfv_convertbackonheight(lf);
   temppage.tp = jsfv_convertbackonheight(tp);
   temppage.wd = jsfv_convertbackonheight(wd);
   temppage.ht = jsfv_convertbackonheight(ht);
   temppage.fsz = jsfv_convertbackonheight(14);
   //temppage.pad = jsfv_convertbackonheight(4);
   temppage.zindex = 1;
   temppage.fbld = '0';
   temppage.faln = 'left';
   temppage.txt = '';
   temppage.wd_id = '';
   temppage.field_id = '';
   temppage.section = '';
   
   return temppage;
}

// Update made to an input box in metadata, redraw
function jsfv_changediv() {
   //alert('changediv()');
   if(jsfv_wd_row_id!='new') {
      if(!Boolean(jsfv_savelist_indx[jsfv_wd_row_id])) {
         jsfv_savelist_indx[jsfv_wd_row_id] = true;
         jsfv_savelist.push(jsfv_wd_row_id);
         jQuery('#jsfv_meta_nochanges').hide();
         jQuery('#jsfv_meta_changes').show();
      }

      var temppage = jsfv_getcurrentdivvalues();
      jsfv_divs[jsfv_wd_row_id] = temppage;
      if(!Boolean(jsfv_changerefresh)) {
         jsfv_focusdiv(jsfv_wd_row_id);
      } else {
         jsfv_savechanged();
      }
   }
   
}

// Get data from input boxes
function jsfv_getcurrentdivvalues() {
   var temppage = {};
   temppage.divname = jQuery('#jsfv_div_name').val();
   temppage.lf = jsfv_convertbackonheight(jQuery('#jsfv_div_lf').val());
   temppage.tp = jsfv_convertbackonheight(jQuery('#jsfv_div_tp').val());
   temppage.wd = jsfv_convertbackonheight(jQuery('#jsfv_div_wd').val());
   temppage.ht = jsfv_convertbackonheight(jQuery('#jsfv_div_ht').val());
   temppage.rad = jsfv_convertbackonheight(jQuery('#jsfv_div_rad').val());
   temppage.zindex = jQuery('#jsfv_div_zindex').val();
   temppage.fsz = jsfv_convertbackonheight(jQuery('#jsfv_div_fsz').val());
   if(Boolean(jQuery('#jsfv_div_pad').val())) temppage.pad = jsfv_convertbackonheight(jQuery('#jsfv_div_pad').val());
   temppage.fclr = jQuery('#jsfv_div_fclr').val();
   temppage.ffam = jQuery('#jsfv_div_ffam').val();
   temppage.classname = jQuery('#jsfv_div_classname').val();
   temppage.fbld = jQuery('#jsfv_div_fbld').val();
   
   temppage.rqd = '0';
   if(document.getElementById('jsfv_div_rqd').checked) temppage.rqd = '1';

   temppage.tabi = jQuery('#jsfv_div_tabi').val();
   
   temppage.fund = '0';
   if(document.getElementById('jsfv_div_fund').checked) temppage.fund = '1';
   temppage.fitl = '0';
   if(document.getElementById('jsfv_div_fitl').checked) temppage.fitl = '1';
   
   temppage.faln = jQuery('#jsfv_div_faln').val();
   temppage.bgclr = jQuery('#jsfv_div_bgclr').val();
   temppage.opacity = jQuery('#jsfv_div_opacity').val();
   temppage.hide = jQuery('#jsfv_div_hide').val();
   temppage.onclick = jQuery('#jsfv_div_onclick').val();
   
   temppage.type = jQuery('#jsfv_div_type').val();
   temppage.wd_id = jQuery('#jsfv_div_wdid').val();
   temppage.section = jQuery('#jsfv_div_section').val();
   temppage.field_id = jQuery('#jsfv_div_fieldid').val();
   temppage.wdtype = jQuery('#jsfv_div_wdtype').val();
   
   temppage.txt = jsfpb_convertstring(jQuery('#jsfv_div_txt').val());
   if(!Boolean(temppage.txt)) temppage.txt = '';
   
   temppage.img = jQuery('#jsfv_div_img').val();
   temppage.fin = jQuery('#jsfv_div_fin').val();
   temppage.fout = jQuery('#jsfv_div_fout').val();
   if(Boolean(jQuery('#jsfv_div_mvleft').val())) temppage.mvleft = jsfv_convertbackonheight(jQuery('#jsfv_div_mvleft').val());
   if(Boolean(jQuery('#jsfv_div_mvtop').val())) temppage.mvtop = jsfv_convertbackonheight(jQuery('#jsfv_div_mvtop').val());
   
   // Save all animated moves
   var contd = true;
   var i = 0;
   //var cnt = 0;
   temppage.move = '';
   while(contd) {
      if($('#jsfv_div_move' + i).length == 0) {
         contd = false;
      } else {
         var tm = jQuery('#jsfv_div_movetm' + i).val();
         var lf = jsfv_convertbackonheight(jQuery('#jsfv_div_movelf' + i).val());
         var tp = jsfv_convertbackonheight(jQuery('#jsfv_div_movetp' + i).val());
         if(Boolean(lf) && Boolean(tp) && lf!=0 && tp!=0) {
            //if(cnt>0) temppage.move += ';';
            //temppage.move += lf + ',' + tp + ',' + tm;
            temppage.move += lf + ',' + tp + ',' + tm + ';';
            //cnt++;
         }
         i++;
      }
   }
   
   //Parent
   temppage.parent = '';
   if(document.getElementById('jsfv_parentchkbx').checked) {
      temppage.parent = jQuery('#jsfv_div_parent').val();
   }
   
   //alert('move: ' + temppage.move);
   
   return temppage;
}

var jsfv_changerefresh = false;
var jsfv_changeinfotext = false;
var jsfv_savelist = [];
var jsfv_savelist_indx = {};

function jsfv_savechanged(jsondataignore) {
   // Make sure the screen shows the user that we're saving
   jQuery('#jsfv_draw_save_outer').show();
   
   jsfv_changerefresh = false;
   if(jsfv_changeinfotext){
      // if info was changed, save it first
      jsfv_changeinfotext = false;
      var temppage = {};
      temppage.type = 'header';
      temppage.infoonly = jQuery('#jsfv_infoonly_txt').val();
      temppage.oright = jQuery('#jsfv_oright_txt').val();
      var callback = 'jsfv_savechanged';
      var params = '';
      params += '&wd_id=' + encodeURIComponent(jsfv_wd_id);
      params += '&ignorenull=1';
      
      var wd_row_id = jQuery('#jsfv_infoonly_txt').data('id');
      if(Boolean(wd_row_id) && wd_row_id!='new') params += '&wd_row_id=' + wd_row_id;
      else params += '&enabled=yes';
         
      params += '&sequence=1';
      params += '&name=' + encodeURIComponent('Visual: ' + jsfv_name);
      params += '&value=' + encodeURIComponent(JSON.stringify(temppage));
      params += '&userid=' + encodeURIComponent(jsfv_userid);
      params += '&token=' + encodeURIComponent(jsfv_token);
      jsfpb_QuickJSON('submitwd',callback,params);      
   } else {
      // Save all the divs iteratively until they're all gone, then reload
      if(Boolean(jsfv_savelist) && jsfv_savelist.length>0) {
         var popid = jsfv_savelist.pop();
         jsfv_savediv(popid);
      } else {
         //alert('here...');
         // hide 'saving' screen
         jQuery('#jsfv_draw_save_outer').hide();
         
         jsfv_savelist = [];
         jsfv_savelist_indx = {};
         jsfv_drawadmindivs(jsfv_wd_id,jsfv_name);
      }
   }
}

// Save current state of the div
function jsfv_savediv(wd_row_id,temppage,callback) {
   if(!Boolean(callback)) callback = 'jsfv_savediv_return';
   
   var params = '';
   params += '&wd_id=' + encodeURIComponent(jsfv_wd_id);
   params += '&ignorenull=1';
   
   if(Boolean(wd_row_id) && wd_row_id!='new' && Boolean(jsfv_divs[wd_row_id])) {
      temppage = jsfv_divs[wd_row_id];
      params += '&wd_row_id=' + wd_row_id;
   } else if(Boolean(temppage)) {
      //alert('found this...');
      params += '&enabled=yes';
   } else {
      temppage = jsfv_getcurrentdivvalues();
      //alert('here: ' + JSON.stringify(temppage));
      params += '&enabled=yes';
   }
      
   params += '&name=' + encodeURIComponent('Visual: ' + jsfv_name);
   params += '&value=' + encodeURIComponent(JSON.stringify(temppage));
   params += '&userid=' + encodeURIComponent(jsfv_userid);
   params += '&token=' + encodeURIComponent(jsfv_token);
   
   //alert('jsfv_savediv: ' + params);
   jsfpb_QuickJSON('submitwd',callback,params);
}


var jsfv_copylist;
var jsfv_copymsg;
function jsfv_makecopy(newname,skipmsg) {
   jsfv_copylist = [];
   jsfv_copymsg = '';
   if(!Boolean(skipmsg)) jsfv_copymsg = 'Your visual components have been copied successfully to: \"' + newname + '\"';
   
   for (var key in jsfv_divs) {
      var temppage = jsfv_divs[key];
      
      var obj = {};
      
      obj.callback = 'jsfv_executelist';
      obj.params = '';
      obj.params += '&wd_id=' + encodeURIComponent(jsfv_wd_id);
      obj.params += '&ignorenull=1';
      obj.params += '&enabled=yes';
      obj.params += '&name=' + encodeURIComponent('Visual: ' + newname);
      obj.params += '&value=' + encodeURIComponent(JSON.stringify(temppage));
      obj.params += '&userid=' + encodeURIComponent(jsfv_userid);
      obj.params += '&token=' + encodeURIComponent(jsfv_token);
      obj.action = 'submitwd';
      
      jsfv_copylist.push(obj);      
   }
   jsfv_executelist();
}

function jsfv_executelist(jsondata){
   // Make sure the screen shows the user that we're saving
   jQuery('#jsfv_draw_save_outer').show();
   
   if(Boolean(jsfv_copylist) && jsfv_copylist.length>0) {
      var obj = jsfv_copylist.shift();
      jsfpb_QuickJSON(obj.action,obj.callback,obj.params);
   } else {
      if(Boolean(jsfv_copymsg)) alert(jsfv_copymsg);
      
      // hide 'saving' screen
      jQuery('#jsfv_draw_save_outer').hide();
   }
}

// After saving a div (or creating one), redraw
function jsfv_savediv_return(jsondata) {
   // remember most recent layer to focus
   if(!Boolean(jsfv_wd_row_id) || jsfv_wd_row_id=='new') jsfv_wd_row_id = jsondata.wd_row_id;
   
   // original called this
   jsfv_savechanged();
}


var jsfv_removinglayers;
function jsfv_removethispage() {
   jsfv_removinglayers = [];
   if(confirm('Are you sure you want to permanently delete this entire page?')) {
      for (var key in jsfv_divs) {
         jsfv_removinglayers.push(key);
      }
      jsfv_removelayers();
   }
}

function jsfv_removelayers(jsondata){
   if(Boolean(jsondata)) jsfpb_ReturnJSON(jsondata);
   if(Boolean(jsfv_removinglayers) && jsfv_removinglayers.length>0) {
      var obj = jsfv_removinglayers.shift();
      var params = '';
      params += '&wd_id=' + encodeURIComponent(jsfv_wd_id);
      params += '&wd_row_id=' + obj;
      params += '&enabled=no';
      params += '&userid=' + encodeURIComponent(jsfv_userid);
      params += '&token=' + encodeURIComponent(jsfv_token);
      alert('removing layer: ' + params);
      jsfpb_QuickJSON('submitwd','jsfv_removelayers',params);
   } else {
      //refresh page and pick a new page to edit
      // do nothing?
      jsfv_initadmin(jsfv_wd_id,jsfv_name,jsfv_divid,jsfv_totalwd,jsfv_totalht);
   }
}

function jsfv_removelayer(wd_row_id) {
   if(Boolean(wd_row_id) && !isNaN(wd_row_id)) {
      if(confirm('Are you sure you want to permanently delete this layer?')) {
         // Make sure the screen shows the user that we're saving
         jQuery('#jsfv_draw_save_outer').show();
         
         jsfv_divs[wd_row_id] = '';
         jsfv_wd_row_id = '';
         var temppage;
         var callback = 'jsfv_removelayer_return';
         var params = '';
         params += '&wd_id=' + encodeURIComponent(jsfv_wd_id);
         params += '&wd_row_id=' + wd_row_id;
         params += '&enabled=no';
         params += '&userid=' + encodeURIComponent(jsfv_userid);
         params += '&token=' + encodeURIComponent(jsfv_token);
         jsfpb_QuickJSON('submitwd',callback,params);
      }
   }
}

function jsfv_removelayer_return(jsondata) {
   if(Boolean(jsondata)) jsfpb_ReturnJSON(jsondata);
   // hide 'saving' screen
   jQuery('#jsfv_draw_save_outer').hide();
   
   jsfv_drawadmindivs(jsfv_wd_id,jsfv_name);
}

function jsfv_resize(amt){
   jsfv_zoomparam += amt;
   jsfv_drawadmindivs(jsfv_wd_id,jsfv_name);
   //jQuery('#jsfv_draw_zoom').html(Math.round(jsfv_zoomparam) + '%');
}


// Given a percentage of height, return pixels
var jsfv_zoomparam = 100.0;
function jsfv_convertonheight(num,def){
   var x = num;
   if(num!='auto') {
      if(!Boolean(num) || isNaN(num)) num=def;
      if(!Boolean(num) || isNaN(num)) num=0;
      
      //alert('jsfv_totalht: ' + jsfv_totalht);
      
      var x = Math.round(((jsfv_totalht * parseInt(num)) / 10000.0) * (jsfv_zoomparam / 100.0));
      if(x<1) x = 0;
   }
   return x;
}

// Given pixels, get percentage
function jsfv_convertbackonheight(num,def){
   var x = def;
   if(num!='auto' && def!='auto') {
      if(!Boolean(num) || isNaN(num)) num=def;
      if(!Boolean(num) || isNaN(num)) num=0;
      
      x = Math.round(((parseInt(num) * 10000.0) / jsfv_totalht) * (100.0 / jsfv_zoomparam));
      //if(x<1) x = 0;
   }
   if(!Boolean(x) && x!=0) x='auto';
   return x;
}





var jsfv_currx;
var jsfv_curry;
var jsfv_prevx;
var jsfv_prevy;
var jsfv_currid;
var jsfv_currtype;

function jsfv_actionxy(res, e) {
   //jQuery('#jsfv_debug').show();      

   var tempx = e.clientX;
   var tempy = e.clientY;
   
    if (res == 'down' || res == 'rsdown' || res == 'newdown') {
       jsfv_currid = e.target.getAttribute('data-id');
       if(res!='newdown' && (!Boolean(jsfv_wd_row_id) || jsfv_currid!=jsfv_wd_row_id)) {
          jsfv_focusdiv(jsfv_currid);
          jsfv_currtype = '';
       } else {
          jsfv_currtype = 'move';
          if(res == 'rsdown') jsfv_currtype = 'resize';
          else if(res == 'newdown') jsfv_currtype = 'newlayer';
          jsfv_currx = tempx;
          jsfv_curry = tempy;
       }
    } else if (res == 'up') {
      if(jsfv_currtype=='newlayer') {
         jsfv_prevx = jsfv_currx;
         jsfv_prevy = jsfv_curry;
         jsfv_currx = tempx;
         jsfv_curry = tempy;
         var diffx = jsfv_currx - jsfv_prevx;
         var diffy = jsfv_curry - jsfv_prevy;
         if(diffx>50 && diffy>50) {
            jsfv_currid = '';
            jsfv_focusdiv('new',(jsfv_prevx - 320),jsfv_prevy,diffx,diffy);
            jsfv_savediv('new');            
         }
      } else if(Boolean(jsfv_currid) && Boolean(jsfv_currtype)) {
         jsfv_changediv();
      }
      jsfv_currid = '';
      jsfv_currtype = '';
    } else if (res == 'move') {
      if(jsfv_currtype=='newlayer') {
         var tlf = jsfv_currx - 320;
         var ttp = jsfv_curry;
         var twd = (tempx - jsfv_currx);
         var tht = (tempy - jsfv_curry);
         jQuery('#jsfv_temp_new').css('left',tlf + 'px');
         jQuery('#jsfv_temp_new').css('top',ttp + 'px');
         jQuery('#jsfv_temp_new').css('width',twd + 'px');
         jQuery('#jsfv_temp_new').css('height',tht + 'px');
         jQuery('#jsfv_temp_new').css('z-index','999');
         jQuery('#jsfv_temp_new').show();
      } else if(Boolean(jsfv_currid) && Boolean(jsfv_currtype)) {
         jsfv_prevx = jsfv_currx;
         jsfv_prevy = jsfv_curry;
         jsfv_currx = tempx;
         jsfv_curry = tempy;
         var diffx = jsfv_currx - jsfv_prevx;
         var diffy = jsfv_curry - jsfv_prevy;
         
         if(jsfv_currtype=='move') {
            var id_el = document.getElementById('jsfv_' + jsfv_currid + '_outer');
               
            var newx = parseInt(id_el.style.left.substring(0,(id_el.style.left.length - 2))) + diffx;
            var newy = parseInt(id_el.style.top.substring(0,(id_el.style.top.length - 2))) + diffy;
               
            id_el.style.left = newx + 'px';
            id_el.style.top = newy + 'px';
            
            jsfv_divs[jsfv_currid].lf = jsfv_convertbackonheight(newx);
            jsfv_divs[jsfv_currid].tp = jsfv_convertbackonheight(newy);
            
            jQuery('#jsfv_div_lf').val(newx);
            jQuery('#jsfv_div_tp').val(newy);
         } else if(jsfv_currtype=='resize') {
            var origwd = jQuery('#jsfv_' + jsfv_currid + '_outer').css('width');
            //alert('origwd: ' + origwd);
            var oright = jQuery('#jsfv_' + jsfv_currid + '_outer').css('height');
            var newx = parseInt(origwd.substring(0,(origwd.length - 2))) + diffx;
            var newy = parseInt(oright.substring(0,(oright.length - 2))) + diffy;
               
            jQuery('#jsfv_' + jsfv_currid + '_outer').css('width',newx + 'px');
            jQuery('#jsfv_' + jsfv_currid + '_outer').css('height',newy + 'px');
            jQuery('#jsfv_' + jsfv_currid).css('width',(newx - 2) + 'px');
            jQuery('#jsfv_' + jsfv_currid).css('height',(newy - 2) + 'px');
            if(Boolean(jsfv_divs[jsfv_currid].img)) {
               jQuery('#jsfv_' + jsfv_currid + '_imgfld').css('max-width',(newx - 2) + 'px');
               jQuery('#jsfv_' + jsfv_currid + '_imgfld').css('max-height',(newy - 2) + 'px');
            }
            
            jsfv_divs[jsfv_currid].wd = jsfv_convertbackonheight(newx);
            jsfv_divs[jsfv_currid].ht = jsfv_convertbackonheight(newy);
            
            jQuery('#jsfv_div_wd').val(newx);
            jQuery('#jsfv_div_ht').val(newy);
         }
      }
      
      //always show coords on screen - even if mouse was never clicked
      var coords = (tempx - 320) + ', ' + tempy;
      jQuery('#jsfv_draw_coords').html(coords);
   } else if (res == 'keydown') {
      // Don't allow backspace to take the browser back a page
      var code = e.keyCode;
      if (code == 8 &&
		((e.target || e.srcElement).tagName != "TEXTAREA") && 
		((e.target || e.srcElement).tagName != "INPUT")) { 
         e.preventDefault();
      } else if((e.ctrlKey || e.metaKey) && code == 83) {
         // Save Function
         jsfv_savechanged();
         event.preventDefault();
         return false;
      }
		      
   } else if (res == 'keyup') {
      if(Boolean(jsfv_wd_row_id)) {
         var code = e.keyCode;
         //alert('code: ' + code);
         if(code==8 || code==46) jsfv_removelayer(jsfv_wd_row_id);
      }
   }
}





function jsfv_getdimensions(flds){
   // Calculate the left and top of total area
   var minleft = 10000000;
   var maxright = 0;
   var mintop = 10000000;
   var maxbot = 0;
   var oright;
   var variableht = false;
   var parenttops = {};
   var rmnrows = flds;
   var dimcounter = 0;
   while(dimcounter<50 && Boolean(rmnrows) && Object.keys(rmnrows).length>0) {
      dimcounter++;
      var newrmnrows = {};
      for (var key in rmnrows) {      
         var lyr = rmnrows[key];
         if(lyr.type=='header') {
            //alert('layer header found: ' + JSON.stringify(lyr));
            if(Boolean(lyr.oright) && !isNaN(lyr.oright) && parseInt(lyr.oright)>200) {
               oright = parseInt(lyr.oright);
            }
         } else if(lyr.type!='code' && (!Boolean(lyr.hide) || lyr.hide!='1')) {
            if(dimcounter<50 && Boolean(lyr.parent) && !Boolean(parenttops[lyr.parent])) {
               newrmnrows[key] = rmnrows[key];
            } else {
               var tht = 0;
               if(lyr.ht != 'auto' && !isNaN(lyr.ht)) tht = parseInt(lyr.ht);
               else variableht = true;
               
               var lf = parseInt(lyr.lf);
               var tp = parseInt(lyr.tp);
               if(lyr.parent && Boolean(parenttops[lyr.parent])) {
                  lf += parseInt(parenttops[lyr.parent].lf);
                  tp += parseInt(parenttops[lyr.parent].tp);
               }
               var obj = {};
               obj.lf = lf;
               obj.tp = tp;
               parenttops[key] = obj;
               
               //alert('layer: ' + JSON.stringify(lyr));
               if(lf < minleft) minleft = lf;
               if((lf + parseFloat(lyr.wd)) > maxright) maxright = Math.round(lf + parseFloat(lyr.wd));
               if(tp < mintop) mintop = tp;
               if((tp + tht) > maxbot) maxbot = tp + tht;
            }
         }
      }
      rmnrows = newrmnrows;
   }
   var obj = {};
   obj.minleft = minleft;
   obj.maxright = maxright;
   obj.mintop = mintop;
   obj.maxbot = maxbot;
   obj.oright = oright;
   obj.variableht = variableht;
   
   obj.wd = maxright - minleft;
   obj.ht = maxbot - mintop;
   
   return obj;
}



// group items together.
var jsfv_workingflds;
var jsfv_workingfldsdims;
function jsfv_grouplayers(){
   // add 5px of padding around elements for container
   var padding = 5;
   
   // get all selected divs
   jsfv_workingflds = {};
   jQuery('input.jsfvlayer:checkbox:checked').each(function () {
      var tid = jQuery(this).val();
      jsfv_workingflds[tid] = jsfv_divs[tid];
   });
   
   // get current dimensions and create a container
   var obj = jsfv_getdimensions(jsfv_workingflds);
   jsfv_workingfldsdims = obj;
   var temppage = jsfv_starterdivobj(jsfv_convertonheight(obj.minleft),jsfv_convertonheight(obj.mintop),jsfv_convertonheight(obj.wd + (2 * jsfv_convertbackonheight(padding))),jsfv_convertonheight(obj.ht + (2 * jsfv_convertbackonheight(padding))));
   
   jsfv_savediv('',temppage,'jsfv_grouplayers_step2')
   //alert('new div: ' + JSON.stringify(temppage));
}

function jsfv_grouplayers_step2(jsondata){
   jsfpb_ReturnJSON(jsondata);
   var padding = 5;
   
   //alert('row id: ' + jsondata.wd_row_id + ' fields: ' + JSON.stringify(jsfv_workingflds));
   
   
   for (var key in jsfv_workingflds) {   
      jsfv_divs[key].lf = parseInt(jsfv_divs[key].lf) + jsfv_convertbackonheight(padding) - jsfv_workingfldsdims.minleft;
      jsfv_divs[key].tp = parseInt(jsfv_divs[key].tp) + jsfv_convertbackonheight(padding) - jsfv_workingfldsdims.mintop;
      jsfv_divs[key].parent = jsondata.wd_row_id;
      if(!Boolean(jsfv_savelist_indx[key])) {
         jsfv_savelist_indx[key] = true;
         jsfv_savelist.push(key);
      }
   }
   jsfv_savechanged();   
   
}

function jsfv_grouplayersinput() {
   var str = '';
   str += '<div style=\"margin-top:1px;padding:2px;border-top:1px solid #EFEFEF;font-family:arial;font-size:12px;\" id=\"\">';
   str += jsfv_grouplayersinputrecur();
   str += '<div style=\"margin:8px;\">';
   str += '<div ';
   str += 'onclick=\"jsfv_grouplayers();\" ';
   str += 'style=\"float:left;margin-right:10px;width:60px;padding:3px;font-size:8px;text-align:center;border:1px solid #333333;border-radius:3px;cursor:pointer;\" ';
   str += '>Group</div>';
   str += '<div style=\"clear:both;\"></div>';   
   str += '</div>';
   str += '</div>';
   jQuery('#jsfv_meta_layers').html(str);
}

function jsfv_grouplayersinputrecur(parentid,depth){
   var str = '';
   if(!Boolean(depth)) depth = 0;
   for (var key in jsfv_divs) {
      if(jsfv_divs[key].type!='header') {
         if((!Boolean(parentid) && !Boolean(jsfv_divs[key].parent)) || jsfv_divs[key].parent == parentid) {
            str += '<div class=\"jsfv_lyrs\" id=\"jsfv_lyrs_' + key + '\" style=\"margin-bottom:2px;padding-bottom:2px;border-bottom:1px solid #E0E0E0;cursor:pointer;\" onclick=\"jsfv_focusdiv(\'' + key + '\');\">';
            str += '<div style=\"float:left;margin-right:5px;width:25px;\">';
            str += '<input class=\"jsfvlayer\" type=\"checkbox\" value=\"' + key + '\" onclick=\"event.stopPropagation();\">';
            str += '</div>';
            str += '<div id=\"jsfv_hide_' + key + '\" style=\"float:left;width:27px;margin-right:5px;padding-top:2px;color:blue;font-size:10px;cursor:pointer;\" onclick=\"event.stopPropagation();jQuery(\'#jsfv_' + key + '_outer\').hide();jQuery(\'#jsfv_hide_' + key + '\').hide();jQuery(\'#jsfv_view_' + key + '\').show();\">';
            str += 'hide';
            str += '</div>';
            str += '<div id=\"jsfv_view_' + key + '\" style=\"float:left;width:27px;margin-right:5px;padding-top:2px;color:green;font-size:10px;cursor:pointer;display:none;\" onclick=\"event.stopPropagation();jQuery(\'#jsfv_' + key + '_outer\').show();jQuery(\'#jsfv_hide_' + key + '\').show();jQuery(\'#jsfv_view_' + key + '\').hide();\">';
            str += 'view';
            str += '</div>';
            str += '<div style=\"float:left;padding-left:' + (depth * 10) + 'px;margin-right:5px;cursor:pointer;\">';
            str += jsfv_divs[key].divname;
            str += '</div>';
            str += '<div style=\"clear:both;\"></div>';
            str += '</div>';
            str += jsfv_grouplayersinputrecur(key,(depth + 1));
         }
      }
   }
   return str;
}



function jsfv_movealllayersinparent(parent,lf,tp){
   if(!Boolean(lf)) lf = 0;
   if(!Boolean(tp)) tp = 0;
   lf = parseInt(lf);
   tp = parseInt(tp);
   
   if(lf!=0 || tp!=0) {
      //alert('parent: ' + parent + ' lf: ' + lf);
      for (var key in jsfv_divs) {
         if(jsfv_divs[key].type != 'header') {
            //alert('found: ' + key + ' parent: ' + jsfv_divs[key].parent);
            if((Boolean(jsfv_divs[key].parent) && jsfv_divs[key].parent == parent) || (!Boolean(parent) && !Boolean(jsfv_divs[key].parent))) {
               //alert('before: ' + jsfv_divs[key].lf);
               jsfv_divs[key].lf = parseInt(jsfv_divs[key].lf) + jsfv_convertbackonheight(lf);
               jsfv_divs[key].tp = parseInt(jsfv_divs[key].tp) + jsfv_convertbackonheight(tp);
               if(!Boolean(jsfv_savelist_indx[key])) {
                  jsfv_savelist_indx[key] = true;
                  jsfv_savelist.push(key);
               }
               //alert('after: ' + jsfv_divs[key].lf);
            }
         }
      }
      //jsfv_changediv();
      jsfv_savechanged();
   }
}




// Copy a div from another page
function jsfv_copylayer() {
   //alert('allnames: ' + JSON.stringify(jsfv_allnames));
   if(Boolean(jsfv_allnames) && jsfv_allnames.length>0) {
      var str = '';
      str += '<div style=\"margin-top:25px;\">Pick a page to pull a layer from</div>';
      str += '<div style=\"margin-top:15px;\">';
      str += '<select id=\"jsfv_importfrompage\" onchange=\"jsfv_selectlayers(jQuery(\'#jsfv_importfrompage\').val());\">';
      str += '<option value=\"\"></option>';
      for(var i=0;i<jsfv_allnames.length;i++) {
         //alert('name: ' + jsfv_allnames[i]);
         str += '<option value=\"' + jsfv_allnames[i] + '\">' + jsfv_allnames[i] + '</option>';
      }
      str += '</select>';
      str += '</div>';
      str += '<div id=\"jsfv_listoflayers\" style=\"margin-top:15px;\"></div>';
      jQuery('#jsfv_draw_lbox').html(str);
      jQuery('#jsfv_draw_lbox_outer').show();
   }
}

function jsfv_selectlayers(pagename) {
   if(Boolean(pagename)) {
      var callback = 'jsfv_selectlayers_return';
      var params ='';
      params += '&cmsenabled=1';
      params += '&maxcol=8';
      params += '&cmsz_' + jsfpb_shorterwdname(jsfv_wd_id) + '_name=' + encodeURIComponent('Visual: ' + pagename);
      //alert('pagename: ' + pagename + ' params: ' + params);
      jsfpb_getwebdata_jsonp(jsfv_wd_id,callback,params);
   }
}

var jsfv_copylayerids;
function jsfv_selectlayers_return(jsondata) {
   //alert('return with ' + JSON.stringify(jsondata));
   jsfv_copylayerids = [];
   if(Boolean(jsondata) && Boolean(jsondata.rows) && jsondata.rows.length>0) {
      var str = '';
      str += '<select id=\"jsfv_importlayer\" onchange=\"jsfv_selectlayer_savediv(jQuery(\'#jsfv_importlayer\').val());\">';
      str += '<option value=\"\"></option>';
      for(var i=0;i<jsondata.rows.length;i++) {
         var temppage = JSON.parse(jsondata.rows[i].value);
         jsfv_copylayerids[jsondata.rows[i].wd_row_id]=temppage;
         str += '<option value=\"' + jsondata.rows[i].wd_row_id + '\">' + temppage.divname + '</option>';
      }
      str += '</select>';
      jQuery('#jsfv_listoflayers').html(str);
   }
}

function jsfv_selectlayer_savediv(wd_row_id) {
   var temppage = jsfv_copylayerids[wd_row_id];
   temppage.parent = '';
   jsfv_savediv('new',temppage);
   jQuery('#jsfv_draw_lbox_outer').show();   
}


   



function jsfv_createwdform() {
   var divid = 'lbox_enterwdname';
   var str = '';
   str += '<div style=\"margin-top:25px;\">jData Type</div>';
   str += '<div style=\"margin-top:15px;\">';
   str += '<select id=\"lbox_enterwdtype\">';
   str += '<option value=\"fields\">Only specific Fields</option>';
   str += '<option value=\"section\">A specific section</option>';
   str += '<option value=\"form\">Entire form</option>';
   str += '<option value=\"list\">List of rows</option>';
   str += '</select>';
   str += '</div>';
   str += '<div style=\"margin-top:25px;\">Enter JData name</div>';
   str += '<div id=\"' + divid + '\" style=\"margin-top:15px;\"></div>';
   //str += '<div style=\"margin-top:15px;width:80px;padding:6px;border:1px solid #000000;border-radius:4px;cursor:pointer;\">Next</div>';
   jQuery('#jsfv_draw_lbox').html(str);
   
   jQuery('#jsfv_draw_lbox_outer').show();
   
   // requires jsf_search_v2.js
   jsfsearch_testinput(divid,-1,'Search','jsfv_createwdform_step1',false,222,16);
}

function jsfv_createwdform_step1(divid) {
   var wdname = jQuery('#' + divid + '_searchtext').val();
   jsfv_vis_wd_id = wdname;
   var wdtype = jQuery('#lbox_enterwdtype').val();
   if(Boolean(wdname)) {
      if(Boolean(wdtype) && wdtype=='form') jsfv_createwdform_step1b(wdname);
      else if(Boolean(wdtype) && wdtype=='section') jsfada_getFieldPos(wdname,'',jsfv_createwdform_step_sect);
      else if(Boolean(wdtype) && wdtype=='list') jsfada_getFieldPos(wdname,'',jsfv_createwdform_list);
      else jsfada_getFieldPos(wdname,'',jsfv_createwdform_step2);
   } else {
      alert('Please select a jData table before continuing');
   }
}

function jsfv_createwdform_step1b(wdname) {
   jsfv_vis_wd_id = wdname;
   jsfv_vis_newdivs = [];

   //alert('step 1b');
   var temppage = jsfv_starterdivobj(40,40,280,200);
   temppage.divname = 'jData: ' + jsfv_vis_wd_id;
   temppage.type = 'jdataform';
   temppage.wd_id = jsfv_vis_wd_id;
   temppage.zindex = 2;
   jsfv_savediv('new',temppage,'jsfv_createwdform_step5');
}

var jsfv_vis_wd_id;
var jsfv_vis_wd_options = [];
var jsfv_vis_listurl;
function jsfv_createwdform_list(wd_id){
   var wdtype = jQuery('#lbox_enterwdtype').val();
   jsfv_vis_wd_id = wd_id;
   
   jsfv_vis_listurl = '';
   jsfv_vis_wd_options = [];
   
   
   jsondata = jsfada_tablesfields[jsfcore_flattenstr(wd_id,false,true)];
   var str = '';
   str += '<input type=\"hidden\" id=\"lbox_enterwdtype\" value=\"' + wdtype + '\">';
   str += '<div style=\"\">';
   if(Boolean(jsondata) && Boolean(jsondata.results) && jsondata.results.length>0) {
      str += '<div style=\"font-size:20px;\">Tell us how you want to filter the rows to display:</div>';
      
      str += '<div style=\"margin-top:5px;margin-bottom:10px;\">';
      str += '<div style=\"\">';
      str += '<input id=\"lbox_enterwdnocache\" type=\"checkbox\" value=\"1\"> Do not cache results (heavier load on server, but use this for data that updates hourly)';
      str += '</div>';
      str += '</div>';
      
      str += '<div style=\"margin-top:5px;margin-bottom:10px;\">';
      str += '<div style=\"\">';
      str += '<input id=\"lbox_enterwdhoriz\" type=\"checkbox\" value=\"1\"> Display fields horizontally';
      str += '</div>';
      str += '</div>';
      
      str += '<div style=\"margin-top:5px;margin-bottom:10px;\">';
      str += '<div style=\"float:left;width:150px;margin-right:20px;\"># of rows:</div>';
      str += '<div style=\"float:left;margin-right:20px;\"><input id=\"lbox_enterwdlimit\" type=\"text\" value=\"\"></div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
      str += '<div style=\"margin-top:5px;margin-bottom:10px;\">';
      str += '<div style=\"float:left;width:150px;margin-right:20px;\">General Search:</div>';
      str += '<div style=\"float:left;margin-right:20px;\"><input id=\"lbox_enterwdsearch\" type=\"text\" value=\"\"></div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
      str += '<div style=\"margin-top:5px;margin-bottom:10px;padding:8px;border:1px solid #DEDEDE;border-radius:5px;\">';
      str += '<select id=\"lbox_wdselfield\" onchange=\"jsfv_createwdform_list_selopt();\">';
      str += '<option value=\"\"></option>';
      str += '<option value=\"created\">Created</option>';
      for(var i=0;i<jsondata.results.length;i++){
         if(jsondata.results[i].field_type!='SPACER') {
            var lbl = jsondata.results[i].label;
            lbl = jsfpb_replaceAll('<br>',' ',lbl).trim();
            if(Boolean(lbl) && lbl.length > 0) {
               str += '<option value=\"' + jsondata.results[i].field_id + '\">' + jsondata.results[i].label.substr(0,36) + '</option>';
            }
         }
      }
      str += '</select>';
      str += '<div id=\"lbox_wdoptions\" style=\"margin-top:10px;\"></div>';
      str += '</div>';
      str += '<div onclick=\"jsfv_createwdform_list_submit();\" style=\"margin-top:20px;width:100px;font-size:14px;text-align:center;padding:6px;border:1px solid #222222;border-radius:3px;cursor:pointer;\">Next</div>';
   } else {
      str += '<div style=\"font-size:20px;\">Sorry there are no fields for that jData table.</div>';
   }
   str += '</div>';
   jQuery('#jsfv_draw_lbox').html(str);   
}

function jsfv_createwdform_list_selopt(){
   var fldid = jQuery('#lbox_wdselfield').val();
   var label = fldid;
   jsondata = jsfada_tablesfields[jsfcore_flattenstr(jsfv_vis_wd_id,false,true)];
   for(var i=0;i<jsondata.results.length;i++){
      if(fldid == jsondata.results[i].field_id) {
         label = jsondata.results[i].label;
         break;
      }
   }
   
   var opt = {};
   opt.field_id = fldid;
   opt.label = label;
   opt.valid = true;
   jsfv_vis_wd_options.push(opt);
   
   var str = '';
   str += '<div id=\"lbox_wd_option' + jsfv_vis_wd_options.length + '\">';
   str += '<div style=\"float:left;width:170px;font-size:12px;\">';
   //str += jsfv_vis_wd_options.length + '. ' + label;
   str += label;
   str += '</div>';
   
   str += '<div style=\"float:left;width:180px;\">';
   str += '<select id=\"lbox_wd_opttype' + jsfv_vis_wd_options.length + '\" onchange=\"var t=jQuery(\'#lbox_wd_opttype' + jsfv_vis_wd_options.length + '\').val();if(t==\'cmsq\' || t==\'cmsz\') jQuery(\'#lbox_wd_optsetting' + jsfv_vis_wd_options.length + '\').show(); else jQuery(\'#lbox_wd_optsetting' + jsfv_vis_wd_options.length + '\').hide();\">';
   str += '<option value=\"cmsq\">Equals</option>';
   str += '<option value=\"cmsz\">Contains</option>';
   str += '<option value=\"orderbyasc\">Order by (ascending)</option>';
   str += '<option value=\"orderbydesc\">Order by (descending)</option>';
   str += '</select>';
   str += '</div>';
   
   str += '<div id=\"lbox_wd_optsetting' + jsfv_vis_wd_options.length + '\" style=\"float:left;width:180px;margin-right:20px;\">';
   str += '<input type=\"text\" id=\"lbox_wd_optval' + jsfv_vis_wd_options.length + '\" value=\"\" style=\"width:165px;\">';
   str += '</div>';
   
   str += '<div onclick=\"jsfv_vis_wd_options[' + (jsfv_vis_wd_options.length - 1) + '].valid=false;jQuery(\'#lbox_wd_option' + jsfv_vis_wd_options.length + '\').hide();\" style=\"float:left;color:red;font-size:10px;cursor:pointer;\">';
   str += 'Remove';
   str += '</div>';
   
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';
   
   jQuery('#lbox_wdoptions').append(str);
   
   jQuery('#lbox_wdselfield').val('');
}

function jsfv_createwdform_list_submit(){
   var url = '';
   
   var s = jQuery('#lbox_enterwdsearch').val();
   if(Boolean(s)) url += '&filterstr=' + encodeURIComponent(s);
   
   var l = jQuery('#lbox_enterwdlimit').val();
   if(Boolean(l)) url += '&limit=' + encodeURIComponent(l);
   
   if(document.getElementById('lbox_enterwdnocache').checked) url += '&jsfnocache=1';
   if(document.getElementById('lbox_enterwdhoriz').checked) url += '&jsfhoriz=1';
   
   for(var i=0;i<jsfv_vis_wd_options.length;i++) {
      if(jsfv_vis_wd_options[i].valid) {
         var t = jQuery('#lbox_wd_opttype' + (i + 1)).val();
         var v = jQuery('#lbox_wd_optval' + (i + 1)).val();
         if(t=='orderbyasc'){
            url += '&orderby=';
            url += encodeURIComponent('d.' + jsfv_vis_wd_options[i].field_id + ' ASC');
         } else if(t=='orderbydesc') {
            url += '&orderby=';
            url += encodeURIComponent('d.' + jsfv_vis_wd_options[i].field_id + ' DESC');
         } else {
            url += '&' + t + '_';
            url += jsfcore_flattenstr(jsfv_vis_wd_id,false,true);
            url += '_' + jsfv_vis_wd_options[i].field_id;
            url += '=' + encodeURIComponent(v);
         }
      }
   }
   
   // Send URL into step 2 (select display fields)
   jsfv_vis_listurl = url;
   jsfv_vis_wd_options = [];
   jsfv_createwdform_step2(jsfv_vis_wd_id);
}

function jsfv_createwdform_step2(wd_id){
   var wdtype = jQuery('#lbox_enterwdtype').val();
   jsfv_vis_wd_id = wd_id;
   jsondata = jsfada_tablesfields[jsfcore_flattenstr(wd_id,false,true)];
   //alert('wd: ' + JSON.stringify(jsondata));
   var str = '';
   str += '<div style=\"max-width:600px;\">';
   if(Boolean(jsondata) && Boolean(jsondata.results) && jsondata.results.length>0) {
      str += '<div style=\"font-size:20px;\">Select the fields you would like to display:</div>';
      for(var i=0;i<jsondata.results.length;i++){
         if(jsondata.results[i].field_type!='SPACER') {
            var lbl = jsondata.results[i].label;
            lbl = jsfpb_replaceAll('<br>',' ',lbl).trim();
            if(Boolean(lbl) && lbl.length > 0) {
               str += '<div style=\"float:left;width:140px;height:20px;overflow:hidden;font-size:14px;font-family:arial;\">';
               str += '<div style=\"width:940px;height:20px;overflow:hidden;\">';
               str += '<input id=\"jsfv_wdfields_' + jsondata.results[i].field_id + '\" class=\"jsfv_wdfields\" type=\"checkbox\" name=\"' + jsondata.results[i].label + '\" value=\"' + jsondata.results[i].field_id + '\"> ' + jsondata.results[i].label.substr(0,28);
               str += '</div>';
               str += '</div>';
            }
         }
      }
      str += '<div style=\"clear:both;\"></div>';
      str += '<div onclick=\"jsfv_createwdform_step3(\'' + wdtype + '\');\" style=\"margin-top:20px;width:100px;font-size:14px;text-align:center;padding:6px;border:1px solid #222222;border-radius:3px;cursor:pointer;\">Next</div>';
   } else {
      str += '<div style=\"font-size:20px;\">Sorry there are no fields for that jData table.</div>';
   }
   str += '</div>';
   jQuery('#jsfv_draw_lbox').html(str);  
}

// Take care of specificsection
function jsfv_createwdform_step_sect(wd_id){
   jsfv_vis_wd_id = wd_id;
   jsondata = jsfada_tablesfields[jsfcore_flattenstr(wd_id,false,true)];
   //alert('wd: ' + JSON.stringify(jsondata));
   var str = '';
   str += '<div style=\"max-width:600px;\">';
   if(Boolean(jsondata) && Boolean(jsondata.sections) && jsondata.sections.length>0) {
      str += '<div style=\"font-size:20px;\">Select the Section you would like to display:</div>';
      str += '<select id=\"jsfv_jdataform_section\">';
      str += jsfv_createwdform_step_sect_recur(jsondata.sections);
      
      /*
      for(var i=0;i<jsondata.sections.length;i++){
         var lbl = jsondata.sections[i].label + ' section ' + jsondata.sections[i].sequence + '(' + jsondata.sections[i].section + ')';
         lbl = jsfpb_replaceAll('<br>',' ',lbl).trim();
         str += '<option value=\"' + jsondata.sections[i].section + '\">';
         str += lbl;
         str += '</option>';
      }
      */
      
      str += '</select>';
      str += '<div onclick=\"jsfv_createwdform_step_sect2();\" style=\"margin-top:20px;width:100px;font-size:14px;text-align:center;padding:6px;border:1px solid #222222;border-radius:3px;cursor:pointer;\">Next</div>';
   } else {
      str += '<div style=\"font-size:20px;\">Sorry there are no sections for that jData table.</div>';
   }
   str += '</div>';
   jQuery('#jsfv_draw_lbox').html(str);  
}

function jsfv_createwdform_step_sect_recur(sects,depth) {
   var str = '';
   if(!Boolean(depth)) depth = 0;
   if(Boolean(sects) && sects.length > 0) {
      for(var i=0;i<sects.length;i++){
         var lbl = sects[i].label.substr(0,18) + ' section ' + sects[i].sequence + '(' + sects[i].section + ')';
         lbl = jsfpb_replaceAll('<br>','',lbl).trim();
         str += '<option value=\"' + sects[i].section + '\">';
         for(var j=0; j<depth; j++) str += ' ';
         str += lbl;
         str += '</option>';
         str += jsfv_createwdform_step_sect_recur(sects[i].children,(depth + 1));
      }
   }
   return str;
}

function jsfv_createwdform_step3(wdtype){
   // create a parent div for all fields
   jQuery('#jsfv_draw_save_outer').show();
   jQuery('#jsfv_draw_lbox_outer').hide();
   
   //determine the height of the outer div
   var x = document.getElementsByClassName('jsfv_wdfields');
   var newtop = 10;
   if(Boolean(x) && x.length>0) {
      for(var i=0;i<x.length;i++) {
         if(x[i].checked) {
            var fld = jsfada_getfield(jsfv_vis_wd_id,x[i].value);
            var tpht = 25;
            if(fld.field_type=='TEXTAREA') {
               tpht = 55;
            } else if(fld.field_type=='MBL_UPL') {
               tpht = 75;
            } else if(fld.field_type=='NEWCHKBX' || fld.field_type=='HRZCHKBX' || fld.field_type=='CHECKBOX' || fld.field_type=='FOREIGNTBL' || fld.field_type=='FOREIGNCB') {
               tpht = 100;
            } else if(fld.field_type=='RADIO') {
               tpht = 100;
            }
            newtop = newtop + 5 + tpht;
         }
      }
   }
   newtop = newtop + 10;
   
   //alert('step 3');
   var temppage = jsfv_starterdivobj(40,40,280,newtop);   
   temppage.divname = 'jData: ' + jsfv_vis_wd_id;
   temppage.zindex = 2;
   if(wdtype == 'list') {
      temppage.type = 'jdatacustomlist';
      temppage.wd_id = jsfv_vis_wd_id;
      temppage.txt = jsfv_vis_listurl;
   }
   jsfv_savediv('new',temppage,'jsfv_createwdform_step4');
}

var jsfv_vis_parent;
var jsfv_vis_newdivs;
function jsfv_createwdform_step4(jsondata){
   jsfv_vis_parent = jsondata.wd_row_id;
   var x = document.getElementsByClassName('jsfv_wdfields');
   if(Boolean(x) && x.length>0) {
      var newtop = 10;
      jsfv_vis_newdivs = [];
      for(var i=0;i<x.length;i++) {
         if(x[i].checked) {
            //Create appropriate elements on the screen
            
            var fld = jsfada_getfield(jsfv_vis_wd_id,x[i].value);
            var tpht = 25;
            if(fld.field_type=='TEXTAREA') {
               tpht = 55;
            } else if(fld.field_type=='MBL_UPL') {
               tpht = 75;
            } else if(fld.field_type=='NEWCHKBX' || fld.field_type=='HRZCHKBX' || fld.field_type=='CHECKBOX' || fld.field_type=='FOREIGNTBL' || fld.field_type=='FOREIGNCB') {
               tpht = 100;
            } else if(fld.field_type=='RADIO') {
               tpht = 100;
            }
            var temppage = jsfv_starterdivobj(10,newtop,260,tpht);   
            temppage.divname = x[i].name.substr(0,28);
            temppage.txt = x[i].name.substr(0,28);
            //temppage.classname = x[i].value;
            
            temppage.type = 'wdata';
            temppage.field_id = fld.field_id;
            temppage.wd_id = jsfv_vis_wd_id;
            
            temppage.pad = jsfv_convertbackonheight(0);
            temppage.zindex = 2;
            temppage.parent = jsfv_vis_parent;
            jsfv_vis_newdivs.push(temppage);
            
            newtop = newtop + 5 + tpht;
         }
      }
      
      jsfv_createwdform_step5();
   }
}

function jsfv_createwdform_step_sect2() {
   jsfv_vis_newdivs = [];

   //alert('step 1b');
   var temppage = jsfv_starterdivobj(40,40,280,500);   
   temppage.divname = 'jData: ' + jsfv_vis_wd_id;
   temppage.type = 'jdataform';
   temppage.wd_id = jsfv_vis_wd_id;
   temppage.section = jQuery('#jsfv_jdataform_section').val();
   temppage.zindex = 2;
   jsfv_savediv('new',temppage,'jsfv_createwdform_step5');
}



function jsfv_createwdform_step5(jsondata){
   if(Boolean(jsfv_vis_newdivs) && jsfv_vis_newdivs.length>0) {
      jsfv_savediv('new',jsfv_vis_newdivs.shift(),'jsfv_createwdform_step5');
   } else {
      jsfv_savechanged();
   }
}