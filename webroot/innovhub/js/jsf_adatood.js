var jsfada_debug = false;
var jsfada_menuitems = [];
var jsfada_tablesindex = {};
var jsfada_tablesfields = {};
var jsfada_screenheight;
var jsfada_currtableid;
var jsfada_currrecordid;
var jsfada_mostrecentrows = {};
var jsftada_cacheid;
var jsfada_urls;
var jsfada_ignoreforuser = false;

var jsfada_servercontroller = 'jsfcode/jsonpcontroller.php?jodon=1';

// Built in function to return menu items based on tables
function jsfcore_custommenu_adaoption() {
   //var callback = 'jsfcore_returnmenu';
   var callback = 'jsfcore_custommenu_return_adaoption';
   var params ='&cmsenabled=1';
   params += '&maxcol=20';
   //alert('jsfcore_getmenu');
   jsfcore_getwebdata_jsonp(jsfcore_wd_menu,callback,params,true,true);
}

function jsfcore_custommenu_return_adaoption(jsondata){
   //alert('menu items: ' + JSON.stringify(jsondata));
   jsfcore_ReturnJSON(jsondata);
   jsfada_menuitems = jsondata.rows;
   
   if (Boolean(jsfcore_loggedin)) {
      //use hashtag to gather tables
      var callback = 'jsfada_showtabletabs_return';
      var htag = jsfcore_ht;
      if(!htag.startsWith('#')) htag = '#' + jsfcore_ht;
      jsf_searchforwd(jsfcore_globaluser.userid,jsfcore_globaluser.token,htag,'','','',50,callback);
   } else {
      jsfada_showtabletabs_return();
   }
}

function jsfada_showtabletabs_return(jsondata) {
   var countitems = 0;
   
   var obj = {};
   obj.rows = [];
   
   // Even if not logged in, show menu
   if(Boolean(jsfada_menuitems)) {
      for(var i=0;i<jsfada_menuitems.length;i++) {
         if(countitems>=maxitems) break;
         obj.rows.push(jsfada_menuitems[i]);
         countitems++;
      }
   }
   
   if(Boolean(jsondata) && Boolean(jsondata.results)) {
      
      //Figure out haw many tabs we can display, don't go over
      var maxitems = Math.floor((jsfcore_globalwidth - 22 - 88)/88);
      //var maxitems = 20;
      if(Boolean(jsfcore_mobile)) maxitems = 20;
      
      jsfada_tablesindex = {};
      for(var i=0;i<jsondata.results.length;i++) {
         jsfada_tablesindex[jsondata.results[i].wd_id] = jsondata.results[i];
         if(countitems<maxitems) {
            var t = {};
            t.title = jsondata.results[i].name;
            
            // Choose carefully between URL and JS - js has no refresh
            //t.url = jsfcore_basedir + 'list/' + jsondata.rows[i].wd_row_id;
            //t.onclick = 'jsftodo_displaylist(\'' + jsondata.rows[i].wd_row_id + '\');';
            t.onclick = 'jsfada_currrecordid = \'\';jsfada_displaytable(\'' + jsondata.results[i].wd_id + '\',\'\',\'\',\'\',jsfada_ignoreforuser);';
            
            t.fortabs = 'YES';
            t.forheader = 'YES';
            //t.fortabs = jsondata.rows[i].fortabs;
            //t.forheader = jsondata.rows[i].forheader;
            //t.forfooter = jsondata.rows[i].forfooter;
            
            obj.rows.push(t);
            //alert('adding menu: ' + JSON.stringify(t));
            countitems++;
         }
      }
            
   }
   
   jsfcore_returnmenu(obj);
   
   // adjust the screen for adding tabs
   jsfada_screenheight = jsfcore_globalheight - jsfcore_header_height - jsfcore_footer_height - 10;
   if(!jsfcore_mobile && Boolean(jsfcore_loggedin)) {
      // browser and tablet, show tabs, draw border lines
      jsfcore_globalwidth_pgbldr = jsfcore_globalwidth - 22;
      jsfada_screenheight = jsfcore_globalheight - jsfcore_header_height - jsfcore_footer_height - 32 - 22;
      jQuery('#jsfcore_subheader').show();
      jQuery('#jsfcore_subheader').css('margin-left','5px');
      jQuery('#jsfcore_subheader').css('z-index','2');
      jQuery('#jsfcore_body').css('top','-1px');
      jQuery('#jsfcore_body').css('margin','0px 5px 5px 5px');
      jQuery('#jsfcore_body').css('padding','5px');
      jQuery('#jsfcore_body').css('border','1px solid #AAAAAA');
      jQuery('#jsfcore_body').css('border-bottom-left-radius','5px');
      jQuery('#jsfcore_body').css('border-bottom-right-radius','5px');
      jQuery('#jsfcore_body').css('z-index','1');
      jQuery('#jsfcore_body').css('height', jsfada_screenheight + 'px');
      jQuery('#jsfcore_body').css('overflow','hidden');
   }
}


var jsfada_fldpossuccess;
function jsfada_getFieldPos(wd_id,gname,successfn,addlquery) {
   if(Boolean(jsfada_tablesfields[jsfcore_flattenstr(wd_id,false,true)])) {
      if (Boolean(successfn) && typeof successfn === "function") successfn(wd_id);
   } else {
      jsfada_fldpossuccess = successfn;
      var callback = 'jsfada_getFieldPos_return';
      var query = '';
      query += '&wd_id=' + encodeURIComponent(wd_id);
      if(Boolean(gname)) query += '&groupname=' + encodeURIComponent(gname);
      if(Boolean(addlquery)) query += addlquery;
      jsfcore_QuickJSON('getfieldpositions',callback,query,true,true);   
   }
}

function jsfada_getFieldPos_return(jsondata) {
   if(Boolean(jsfcore_testing) || jsfada_debug) alert('field pos: ' + JSON.stringify(jsondata));
   //alert('field pos: ' + JSON.stringify(jsondata));
   //alert('webdata: ' + JSON.stringify(jsondata.webdata));
   jsfcore_ReturnJSON(jsondata);
   //jsfada_tablesfields[jsondata.wd_id] = jsondata.results;
   //jsfada_tablesfields[jsondata.wd_id] = jsondata;
   //alert('wd_id: ' + jsondata.webdata.wd_id + ' name: ' + jsondata.webdata.name);
   jsfada_tablesfields[jsondata.webdata.wd_id] = jsondata;
   jsfada_tablesfields[jsfcore_flattenstr(jsondata.webdata.name,false,true)] = jsondata;
   if(Boolean(jsfada_fldpossuccess) && typeof jsfada_fldpossuccess === "function") jsfada_fldpossuccess(jsondata.wd_id);
}

// Display the records for a given table
function jsfada_displaytable(tableid,filterstr,limit,page,ignoreforuser) {
   jsfada_currtableid = tableid;
   //jsfada_currrecordid = '';
   if(!Boolean(jsfada_currrecordid)) jsfada_currrecordid = '-123';
   if(Boolean(jsfada_currtableid)) {
      jsfada_getFieldPos(jsfada_currtableid);
      
      var listwidth = 380;
      var listheight = jsfada_screenheight - 10;
      var divid = 'jsfwdarea' + jsfada_currtableid;
      var css = 'margin-right:10px;';
      var remainderwidth = jsfcore_globalwidth - listwidth - 70;
      var detaildiv = '<div style=\"float:left;margin-left:15px;width:' + remainderwidth + 'px;height:' + listheight + 'px;\">';
      detaildiv += '<div id=\"jsfwdareadetails\" style=\"width:' + remainderwidth + 'px;height:' + listheight + 'px;overflow-x:hidden;overflow-y:auto;\"></div>';
      detaildiv += '</div>';
      if(Boolean(jsfcore_mobile)) {
         jsfada_currrecordid = '';
         listwidth = jsfcore_globalwidth - 20;
         remainderwidth = listwidth;
         divid = 'jsfwdarea';
         css = 'margin-left:10px;';
         detaildiv = '';
      }
      
      var str = '';
      str += '<div style=\"float:left;width:' + listwidth + 'px;height:' + listheight + 'px;overflow-x:hidden;overflow-y:auto;\">';
      str += '<div ';
      str += 'id=\"' + divid + '\" ';
      str += 'style=\"' + css + '\" ';
      str += '></div>';
      str += '</div>';
      str += detaildiv;
      str += '<div style=\"clear:both;\"></div>';
      
      jQuery('#jsfcore_body').html(str);
      
      /*
      //jsf_getwdtable_jsonp('',jsfada_currtableid,'',jsfcore_globaluser.userid,jsfcore_globaluser.token,filterstr,limit,page,50);
      jsfwd_filterstr = filterstr;
      //var callback = 'jsfwebdata_tabledisplay';
      var callback = 'jsfada_tabledisplay';
      //jsfwd_defaultformfnname = 'jsfada_recordclick_return';
      jsfwd_showcreaterecord = false;
      var params = '';
      params += '&enabledonly=1';
      //params += '&addrowdisplay=1';
      params += '&limit=' + limit;
      params += '&page=' + page;
      params += '&orderby=' + encodeURIComponent('created DESC');
      params += '&maxcol=20';
      if (Boolean(jsfwd_filterstr) && jsfwd_filterstr!='Search') params += '&filterstr=' + encodeURIComponent(jsfwd_filterstr);
      //alert('jsfada_displaytable::calling jsfcore_getwebdata_jsonp');
      jsftada_cacheid = jsfcore_getwebdata_jsonp(jsfada_currtableid,callback,params,false,ignoreforuser);
      */
      jsfada_displaytableresults(jsfada_currtableid,filterstr,limit,page,ignoreforuser);
   }
   
}

function jsfada_displaytableresults(tableid,filterstr,limit,page,ignoreforuser) {
   
   if(Boolean(ignoreforuser)) jsfada_ignoreforuser = true;
   else jsfada_ignoreforuser = false;
   
   if(!Boolean(limit)) limit=25;
   if(!Boolean(page)) page=1;
   jsfada_currtableid = tableid;
   if(!Boolean(jsfada_currrecordid)) jsfada_currrecordid = '-123';
   if(Boolean(jsfada_currtableid)) {
      jsfwd_filterstr = filterstr;
      //var callback = 'jsfwebdata_tabledisplay';
      var callback = 'jsfada_tabledisplay';
      //jsfwd_defaultformfnname = 'jsfada_recordclick_return';
      jsfwd_showcreaterecord = false;
      var params = '';
      params += '&enabledonly=1';
      //params += '&addrowdisplay=1';
      params += '&limit=' + limit;
      params += '&page=' + page;
      params += '&orderby=' + encodeURIComponent('created DESC');
      params += '&maxcol=20';
      if (Boolean(jsfwd_filterstr) && jsfwd_filterstr!='Search') params += '&filterstr=' + encodeURIComponent(jsfwd_filterstr);
      //alert('jsfada_displaytable::calling jsfcore_getwebdata_jsonp');
      jsftada_cacheid = jsfcore_getwebdata_jsonp(jsfada_currtableid,callback,params,false,jsfada_ignoreforuser);
   }
   
}


var jsfada_tabledisplay_counter = 0;
function jsfada_tabledisplay(jsondata){
   if(jsfada_tabledisplay_counter<10 && !Boolean(jsfada_tablesfields[jsfcore_flattenstr(jsondata.wd_id,false,true)])) {
         jsfada_tabledisplay_counter++;
         setTimeout(jsfada_tabledisplay,500,jsondata);
   } else {
      jsfada_tabledisplay_counter = 0;
      jsfcore_ReturnJSON(jsondata);
      //alert('tabledisplay: ' + JSON.stringify(jsondata));
      var str = '';
      var initfilter = false;
         
      var usingdiv = '#jsfwdarea';
      var tablediv = '#jsfwdarea';
      var detailsdiv = '#jsfwdareadetails';
      var multiplepossible = false;
      var newwindowfordetails = jsfwd_opennewwindow;
      if(Boolean(jsondata.wd_id) && jQuery(usingdiv + jsondata.wd_id).length>0) {
         usingdiv = usingdiv + jsondata.wd_id;
         tablediv = '#jsfwdtable' + jsondata.wd_id;
         multiplepossible = true;
         newwindowfordetails = true;
         if(jQuery(detailsdiv).length>0) newwindowfordetails = false;
      }
      
      if (jsondata.responsecode==1) {
         jsfada_mostrecentrows[jsondata.wd_id] = jsondata.rows;
         jsfada_mostrecentrows[jsfcore_flattenstr(jsondata.wdname,false,true)] = jsondata.rows;
         //alert(jsondata.query);
         var newpage = false;
         var totalrows = jsondata.rows.length;
         //var totalrows = jsondata.totalrows;
         
         jsfwd_pagenum = parseInt(jsondata.page);
         if(!Boolean(jsfwd_pagenum)) jsfwd_pagenum = 1;
         
         if (!Boolean(jsondata.wd_id)) jsondata.wd_id = '';
         if (!Boolean(jsondata.wdname)) jsondata.wdname = '';
         if (!Boolean(jsondata.userid)) jsondata.userid = '';
         if (!Boolean(jsondata.filterstr)) jsondata.filterstr = '';
         jsfwd_filterstr = jsondata.filterstr;
         if (!Boolean(jsondata.maxcol)) jsondata.maxcol = '';
         if (!Boolean(jsondata.callback)) jsondata.callback = '';
         if (!Boolean(jsondata.prefix)) jsondata.prefix = '';      
         if (!Boolean(jsfwd_orderby)) jsfwd_orderby = jsondata.orderby;      
         if (!Boolean(jsfwd_orderby) || jsfwd_orderby=='null') jsfwd_orderby = '';      
         if (Boolean(jsondata.limit)) jsfwd_limit = parseInt(jsondata.limit);      
   
         //alert('totalrows: ' + totalrows);
         if (totalrows>=jsfwd_limit) {
            totalrows = jsfwd_limit;
            newpage = true;
         }
   
         str = str + '<div id=\"jsfwdtableouter' + jsondata.wd_id + '\" class=\"jsfwdtableouter\">';
   
         // Call API to get records
         // jsfada_displaytable(tableid,filterstr,limit,page)
         var tempsrch = '';
         tempsrch += 'jsfada_displaytableresults(\'' + jsondata.wd_id + '\',';
         tempsrch += 'jQuery(\'#jsfwdfilterstrdiv' + jsondata.wd_id + '\').val(),';
         tempsrch += '\'' + jsfwd_limit + '\',';
         tempsrch += 'pg, ';
         tempsrch += '(Boolean(document.getElementById(\'jsfwdmeonly' + jsondata.wd_id + '\')) && !(document.getElementById(\'jsfwdmeonly' + jsondata.wd_id + '\').checked))';
         tempsrch += ');';
         
         if (Boolean(jsondata.inputdisplayrow) || jsondata.totalrows>9 || Boolean(jsondata.filterstr) || (Boolean(jsondata.foruserid) && Boolean(jsondata.userid) && jsondata.userid==jsondata.foruserid) || Boolean(jsfwd_xtraurl)) {
             str = str + '<div class=\"jsfwdtablerow jsfwdtablesearch\" style=\"margin-top:4px;margin-bottom:5px;\">';
             
             str = str + '<input type=\"text\" ';
             //str = str + 'style=\"float:left;margin-left:8px;margin-right:8px;width:' + (globalinnerwidth - 40 - 40).toString() + 'px;height:30px;font-size:18px;font-family:verdana;\" ';
             str = str + 'style=\"float:left;margin-left:8px;margin-right:8px;width:110px;font-size:10px;font-family:verdana;\" ';
             str = str + 'id=\"jsfwdfilterstrdiv' + jsondata.wd_id + '\" ';
             //str = str + 'onkeyup=\"if(event.keyCode==\'13\' || event.which==\'13\') ' + tempsrch + '\" ';
             str = str + 'value=\"' + jsondata.filterstr + '\">';
             
             str += '<div id=\"jsfwdmeonlydiv' + jsondata.wd_id + '\" style=\"float:left;margin-left:8px;margin-right:8px;font-size:10px;\">';
             str += '<input id=\"jsfwdmeonly' + jsondata.wd_id + '\" type=\"checkbox\" value=\"' + jsondata.userid + '\"';
             if(Boolean(jsondata.userid) && Boolean(jsondata.foruserid) && jsondata.userid==jsondata.foruserid) str += ' CHECKED';
             str += '>';
             str += 'My records</div>';
             
             str = str + '<div style=\"float:left;padding:3px;margin-left:2px;overflow:hidden;background-color:#EEEEEE;border:1px solid #222222;border-radius:2px;cursor:pointer;text-align:center;font-size:10px;font-family:arial;\" ';
             str = str + 'onclick=\"var pg=1;';
             str = str + tempsrch;
             str = str + '\">Go</div>';
             
             //jsfwd_filterstr = '';
             str = str + '</div>';
         }
         
         str = str + '<div class=\"jsfwdtablerow jsfwdtablepaging\">';
         
         var totalpages = Math.ceil(jsondata.totalrows / jsfwd_limit);
         if (totalpages>1 && (jsfwd_pagenum>1 || newpage)) {
            //alert('total rows: ' + jsondata.totalrows + ' limit: ' + jsfwd_limit + ' total pages: ' + totalpages);
            var tp = totalpages;
            if (tp>20) tp = 20;
            var start = parseInt(jsfwd_pagenum) - 10;
            if(start<1) start = 1;
            //alert('jsfwd_pagenum: ' + jsfwd_pagenum + ' starts: ' + start + ' tp: ' + tp);
            str = str + '<div class=\"jsfwdtablecurpg\">';
            str = str + jsondata.totalrows + ' results ';
            str = str + '<select id=\"jsfwdtablecurpgdd\" onChange=\"var pg=jQuery(\'#jsfwdtablecurpgdd\').val();';
            str += tempsrch;
            str = str + '\">';
            for(var i=0;i<tp;i++){
               var curpg = parseInt(i) + parseInt(start);
               if (totalpages >= curpg) {
                  str += '<option value=\"' + curpg + '\"';
                  if (curpg==jsfwd_pagenum) str += ' SELECTED';
                  str += '>Page ' + curpg + '</option>';
               }
            }
            str = str + '</select>';
            str = str + '</div>';
         }
         str = str + '</div>';
         var js = '';
         
         //var rowstable = jsfwebdata_tabledisplay_rows(usingdiv,jsondata,totalrows,newwindowfordetails,jsfwd_groupby,'price');
         //str += rowstable.str;
         var flds = jsfada_tablesfields[jsfcore_flattenstr(jsondata.wd_id,false,true)].results;
         var obj;
         for (var j=0;j<jsondata.rows.length;j++) {
            var row = jsondata.rows[j];
            var divid = 'jsfwdtableitem' + jsondata.wd_id + '_' + j;
            str += '<div id=\"' + divid + '\" ';
            str += 'data-wdrow=\"' + j + '\" ';
            str += 'data-wdrowid=\"' + row.wd_row_id + '\" ';
            str += 'class=\"jsfwdtablerow jsfwdtableitem\" ';
            str += 'onclick=\"jsfada_recordclick_return(\'' + jsondata.wd_id + '\',\'' + row.wd_row_id + '\');\" ';
            str += '>';
            
            str += '<div class=\"adarowdispCREATED rowfld_' + jsondata.shortname + '_created\" style=\"float:left;margin-right:5px;\">';
            str += row.created;
            str += '</div>';
            
            
            for (var i=0;i<flds.length;i++) {
               if(Boolean(parseInt(flds[i].header))) {
                  obj = jsfada_displayfield(jsondata.wd_id,row['wd_row_id'],flds[i],row[flds[i].field_id],'','rowfld_' + jsondata.shortname + '_' + flds[i].map,(Boolean(row.userid) && row.userid==jsfcore_globaluser.userid));
                  str += obj.rowdisp;
               }
            }
            str += '<div style=\"clear:both;\"></div>';
            str += '</div>';
         }
         
         
         str += '<div id=\"jsfwdtableitemnew' + jsondata.wd_id + '\" ';
         str += 'data-wdrow=\"new\" ';
         str += 'class=\"jsfwdtablerow jsfwdtableitem\" ';
         str += 'onclick=\"jsfada_recordclick_return(\'' + jsondata.wd_id + '\');\">';
         str = str + 'Create a new record\n';
         str = str + '</div>\n';
         
         str = str + '</div>\n';
         
         jQuery(usingdiv).html(str);
         
         if(Boolean(jsfada_currrecordid)) jsfada_recordclick_return(jsondata.wd_id,jsfada_currrecordid);
      } else {
         str = str + '<div style=\"padding:10px;color:red;font-size:16px;font-family:arial;\">';
         str = str + jsondata.error;
         str = str + '</div>';
         jQuery(usingdiv).html(str);
      }
   }
}

function jsfada_getfield(wd_id,field_id){
   var flds = jsfada_tablesfields[jsfcore_flattenstr(wd_id,false,true)].results;
   var fld = {};
   for (var i=0;i<flds.length;i++) {
      if(flds[i].field_id == field_id) {
         fld = flds[i];
         break;
      }
   }
   return fld;
}

function jsfada_recordclick_return(wd_id,wd_row_id) {
   
   jQuery('.jsfwdtableitem').css('background-color','#FFFFFF');
   if(Boolean(wd_row_id)) jQuery('.jsfwdtableitem[data-wdrowid=\"' + wd_row_id + '\"]').css('background-color','#d7eefc');
   //alert('jsfada_recordclick_return wd_id: ' + wd_id + ' row: ' + wd_row_id);
   jsfada_displayrecord(wd_id,jsfada_getrecentrow(wd_id,wd_row_id));
}

function jsfada_getrecentrow(wd_id,wd_row_id) {
   var row = {};
   if(Boolean(wd_row_id) && Boolean(jsfada_mostrecentrows) && Boolean(jsfada_mostrecentrows[jsfcore_flattenstr(wd_id,false,true)]) && jsfada_mostrecentrows[jsfcore_flattenstr(wd_id,false,true)].length>0){
      var rows = jsfada_mostrecentrows[jsfcore_flattenstr(wd_id,false,true)];
      for(var i=0;i<rows.length;i++) {
         if(parseInt(rows[i].wd_row_id) == parseInt(wd_row_id)) {
            row = rows[i];
            break;
         }
      }
   }
   return row;
}

// display a detail view of a record
// if row variable is undefined, creates a new record
// submit variables below are if you want to save to a specific record programmatically
var jsfada_submit_wdrowid;
var jsfada_submit_origemail;
var jsfada_detailsdivid;
function jsfada_displayrecord(wd_id,row,specificsection) {
   //alert('*chj jsfada_displayrecord wd_id: ' + wd_id + ' sct: ' + specificsection + ' row: ' + JSON.stringify(row));
   //alert('filter string: ' + jsfwd_filterstr);
   var obj;
   jsfada_currrecordid = '';
   if(Boolean(row) && Boolean(row.wd_row_id)) jsfada_currrecordid = row.wd_row_id;
   
   //alert('wd_id: ' + wd_id + ' FOUND row: ' + JSON.stringify(row));
   
   var divid = 'jsfwdareadetails';
   if(jQuery('#' + divid).length<1) divid = 'jsfwdarea' + wd_id;
   if(jQuery('#' + divid).length<1) divid = 'jsfwdarea';
   jsfada_detailsdivid = divid;
   
   var wd = jQuery('#' + divid).width() - 20;
   var ht = jsfada_screenheight - 20;
   if(Boolean(jsfcore_testing) || jsfada_debug) alert('height: ' + ht + ' width: ' + wd);
   
   var totalht = 0;
   var flds = jsfada_tablesfields[jsfcore_flattenstr(wd_id,false,true)].results;
   //alert('*chj fields: ' + JSON.stringify(flds));
   
   var str = '';
   str += '<div id=\"' + divid + '_top\"></div>';
   
   //alert('wd_id: ' + wd_id + ' converted: ' + jsfcore_flattenstr(wd_id,false,true) + ' sections: ' + JSON.stringify(jsfada_tablesfields[jsfcore_flattenstr(wd_id,false,true)].sections) + ' object: ' + JSON.stringify(jsfada_tablesfields[jsfcore_flattenstr(wd_id,false,true)]));
   
   obj = jsfada_recur_printsections(jsfada_tablesfields[jsfcore_flattenstr(wd_id,false,true)].sections);
   str += '<div id=\"' + divid + '_body\" class=\"jsfada_sct';
   if(Boolean(obj.classnames)) str += obj.classnames;
   str += '\">';
   //str += jsfada_recur_printsections(jsfada_tablesfields[jsfcore_flattenstr(wd_id)].sections);
   str += obj.str;
   str += '</div>';
   str += '<div id=\"' + divid + '_bottom\"></div>';
   jQuery('#' + divid).html(str);
   jQuery('#' + divid).show();
   
   //alert('*chj sections displayed');
   
   //str = jsfada_recur_printsections(jsfada_tablesfields[wd_id].sections);
   //jQuery('#' + divid + '_body').html(str);
   
   var disp = '';
   
   if((Boolean(jsfcore_globaluser.isadmin) && parseInt(jsfcore_globaluser.isadmin)==1) || (Boolean(row) && Boolean(row.userid) && row.userid==jsfcore_globaluser.userid)) {
      //Only allow edit/delete if this row exists and is owned by this user
      disp += '<div class=\"adafldedit\" style=\"position:relative;min-height:20px;margin-bottom:10px;\">';
      disp += '<div class=\"adafldbutton\" onclick=\"jsfada_editdata();\" style=\"float:left;margin-right:20px;\">';
      disp += 'Edit';
      disp += '</div>';
      disp += '<div class=\"adafldbutton\" onclick=\"jsf_removewdrowsimple_jsonp(\'' + wd_id + '\',\'' + row.wd_row_id + '\',\'' + row.origemail + '\',\'' + divid + '\');jsfada_currrecordid=\'\';\" style=\"float:left;margin-right:10px;\">';
      disp += 'Delete';
      disp += '</div>';
      disp += '<div class=\"jsfcore_link\" style=\"float:right;margin-right:10px;margin-top:10px;\"onclick=\"stats_showstats(\'' + row.wd_row_id + '\');\">';
      disp += 'View stats';
      disp += '</div>';
      disp += '<div style=\"clear:both;\"></div>';
      disp += '</div>';
      jQuery('#' + divid + '_top').html(disp);
   }
   
   var js = '';
   
   var savejs = '';
   savejs += 'function jsfada_submitwd(holdsubmit,callback){\n';
   savejs += 'if(!Boolean(callback)) callback = \'jsfada_returnsavedata\';\n';
   //savejs += 'alert(\'inside jsfada_submitwd\');\n';
   savejs += 'var url = \'\';\n';
   savejs += 'var temp = \'\';\n';
   savejs += 'var rqderror = \'\';\n';
   savejs += 'var rqderrorstr = \'\';\n';
   savejs += 'var c_url = jsfcore_domain + \'' + jsfada_servercontroller + '\';\n';
   savejs += 'c_url += \'&action=submitwebdata\';\n';
   savejs += 'c_url += \'&ignorenull=1\';\n';
   savejs += 'c_url += \'&callback=\' + callback;\n';
   savejs += 'c_url += \'&wd_id=' + wd_id + '\';\n';
   if(Boolean(row)) {
      if(Boolean(row.wd_row_id)) savejs += 'c_url += \'&wd_row_id=' + row.wd_row_id + '\';\n';
      if(Boolean(row.origemail)) savejs += 'c_url += \'&origemail=' + row.origemail + '\';\n';
   } else {
      row = {};
      savejs += 'if(Boolean(jsfada_submit_wdrowid)) c_url += \'&wd_row_id=\' + jsfada_submit_wdrowid;\n';
      savejs += 'if(Boolean(jsfada_submit_origemail)) c_url += \'&origemail=\' + jsfada_submit_origemail;\n';
   }
   savejs += 'c_url += \'&userid=\' + jsfcore_globaluser.userid;\n';
   savejs += 'c_url += \'&token=\' + jsfcore_globaluser.token;\n';
   savejs += 'url = c_url;\n';
   savejs += 'var c_urls = [];\n';

   // array for the text areas that can increase in height
   var incs = [];
   //alert('jsfada_displayrecord before field iteration');   
   for (var i=0;i<flds.length;i++) {
      //alert('field: ' + JSON.stringify(flds[i]));
      //alert('value: ' + row[flds[i].field_id]);
      obj = jsfada_displayfield(wd_id,row['wd_row_id'],flds[i],row[flds[i].field_id],wd);
      
      //alert('field obtained: ' + JSON.stringify(obj.disp));
      
      var diddy = '#' + divid + '_body';
      if(Boolean(flds[i].parent_s) && flds[i].parent_s!='-1' && flds[i].parent_s != -1) {
         // if a field has a parent and it's not the root, add it to its section
         diddy = '#adasect_' + flds[i].wd_id + '_' + flds[i].parent_s;
      }
      var ch = jQuery(diddy).children();
      //alert('children for ' + diddy + ': ' + JSON.stringify(ch));
      var added = false;
      if(Boolean(ch) && ch.length>0) {
         ch.each(function() {
            //alert('found child: ' + JSON.stringify(this));
            //alert('comparing this field (' + flds[i].sequence + ') to child sequence (' + jQuery(this).data('seq') + ').');
            if(parseInt(flds[i].sequence) < parseInt(jQuery(this).data('seq'))) {
               //alert('inserting before... (' + flds[i].sequence + ')');
               jQuery(this).before(obj.disp);
               added = true;
               return false;
            }
         });
      }
      
      if(!added) jQuery(diddy).append(obj.disp);
      
      //disp += obj.disp;
      savejs += obj.savejs;
      js += obj.js;
      totalht += obj.ht;
      if(Boolean(obj.incid)) incs.push(obj.incid);
   }
   savejs += '  if (rqderror) {\n';
   savejs += '    alert(\'Error: \' + rqderrorstr);\n';
   savejs += '  } else {\n';
   savejs += '    c_urls.push(url + \'&chj=\' + c_urls.length);\n';
   savejs += '    jsfada_urls = c_urls;\n';
   savejs += '    if(!Boolean(holdsubmit)) {\n';
   //savejs += '       alert(\'Saving all good... calling \' + callback);\n';
   savejs += '       var fn = window[callback];\n';
   savejs += '       if(typeof fn === \'function\') fn();\n';
   savejs += '       else jsfada_returnsavedata();\n';
   savejs += '    }';
   savejs += '  }\n';
   savejs += '}\n';
   
   disp = '';
   disp += '<div class=\"adafldsave\" style=\"position:relative;\">';
   disp += '<div class=\"adafldbutton\" id=\"adabtncancel\" onclick=\"jsfada_viewonlydata();\" style=\"float:left;margin-right:20px;\">';
   disp += 'Cancel';
   disp += '</div>';
   disp += '<div class=\"adafldbutton\" id=\"adabtnsave\" onclick=\"jsfada_savedata();\" style=\"float:left;margin-right:20px;\">';
   disp += 'Save';
   disp += '</div>';
   disp += '<div style=\"clear:both;\"></div>';
   disp += '</div>';
   disp += '\n<script>\n';
   disp += savejs;
   disp += js;
   disp += '\n</script>\n';
   totalht += 30;

   //alert('jsfada_displayrecord after field iteration');   
   jQuery('#' + divid + '_bottom').html(disp);
   //alert('jsfada_displayrecord after html added');
   
   // if there are any text areas and they can grow in height
   if(Boolean(jsfcore_testing) || jsfada_debug) alert('height: ' + ht + ' width: ' + wd + ' totalht: ' + totalht);
   if(incs.length>0) {
      var m = Math.ceil(totalht/ht);
      var newht = 55 + Math.floor( ((m*ht) - totalht) / incs.length);
      for (var i=0;i<incs.length;i++) {
         jQuery('#' + incs[i]).css('height',newht + 'px');
      }
   }
   
   
   if(Boolean(specificsection) && !isNaN(specificsection)) {
      jQuery('.jsfada_sct').hide();
      //alert('showing section' + specificsection);
      jQuery('.jsfada_sct' + specificsection).show();
      jQuery('.jsfada_cosct' + specificsection).show();
      //alert('hiding everything, then exposing section ' + specificsection);
   }
   
   
   
   if(Boolean(row) && Boolean(row.wd_row_id)) {
      //Start with read-only record
      jsfada_viewonlydata();
   } else {
      //Brand new record
      jQuery('#adabtncancel').hide();
      jsfada_editdata();
   }
   
   if(typeof jsfada_customforminit === 'function') {
      jsfada_customforminit(wd_id,row);
   }
   
   //alert('check field rels');
   jsfada_fieldchange(wd_id);
}

var jsfada_jdata_sectionclasses = {};
function jsfada_recur_printsections(sections, depth, cocnames){
   if(!Boolean(cocnames)) cocnames = '';
   var str = '';
   var classnames = '';
   if(!Boolean(depth)) depth = 1;
   if(Boolean(sections) && Boolean(sections.length)) {
      for(var i=0;i<sections.length;i++) {
         
         jsfada_jdata_sectionclasses[sections[i].section] = cocnames + ' jsfada_cosct' + sections[i].section;
         
         var obj = jsfada_recur_printsections(sections[i].children, (depth + 1), jsfada_jdata_sectionclasses[sections[i].section]);
         
         var tcnames = ' jsfada_sct' + sections[i].section + obj.classnames;
         classnames += tcnames;
         
         //jsfada_jdata_sectionclasses[sections[i].section] = tcnames;
         
         str += '<div data-seq=\"' + sections[i].sequence + '\" class=\"jsfada_depth' + depth + ' jsfada_sct' + tcnames + '\">';
         str += '<div id=\"adasect_' + sections[i].wd_id + '_' + sections[i].section + '\" data-seq=\"' + sections[i].sequence + '\">';
         //str += jsfada_recur_printsections(sections[i].children, (depth + 1));
         str += obj.str;
         str += '</div>';
         str += '</div>';
      }
   }
   var robj = {};
   robj.str = str;
   robj.classnames = classnames;
   return robj;
}


function jsfwd_postdelete(jsondata){
   //alert('filter string: ' + jsfwd_filterstr);
   //jsfada_displaytable(tableid,filterstr,limit,page)
   jsfada_displaytableresults(jsfada_currtableid,jsfwd_filterstr,jsfwd_limit,jsfwd_pagenum,jsfada_ignoreforuser);
}


// savejs must have:
//   url, temp
//   rqderror, rqderrorstr
//   c_urls: array of URLs that need to be sent
//   c_url: initial url with base parameters
function jsfada_displayfield(wd_id,wd_row_id,fld,val,wd,classname,auth,autofill) {
   if(Boolean(jsfcore_testing) || jsfada_debug) alert('field: ' + JSON.stringify(fld) + ' value: ' + val);
   //alert('displayfield: ' + JSON.stringify(fld) + ' value: ' + val);
   if(!Boolean(wd)) wd = 200;
   if(!Boolean(classname)) classname = '';
   
   if(!Boolean(val) || val=='%E%') val = '';
   
   var tempdivid = 'w' + fld.wd_id + 'a' + fld.field_id;
   
   var obj = {};
   obj.ht = 0;
   obj.js = '';
   obj.savejs = '';
   obj.rowdisp = '';
   obj.dispval = val;
   obj.disp = '';
   
   // Outer div for hide/display certain jsfv questions
   obj.disp += '<div ';
   obj.disp += 'class=\"adafld jsfada_sct';
   if(Boolean(fld.parent_s)) {
      obj.disp += ' jsfada_sct' + fld.parent_s;
      if(Boolean(jsfada_jdata_sectionclasses[fld.parent_s])) obj.disp += jsfada_jdata_sectionclasses[fld.parent_s];
   }
   obj.disp += '" ';
   obj.disp += 'data-seq=\"' + fld.sequence + '\" ';
   obj.disp += 'data-type=\"' + fld.field_type + '\" ';
   obj.disp += 'data-inputid=\"' + tempdivid + '\" ';
   if(Boolean(fld.map)) obj.disp += 'data-map=\"' + fld.map + '\" ';
   obj.disp += 'style=\"\">';

   // This div is for question relationships
   obj.disp += '<div ';
   obj.disp += 'class=\"adafld' + fld.field_type + '\" ';
   obj.disp += 'data-seq=\"' + fld.sequence + '\" ';
   obj.disp += 'data-type=\"' + fld.field_type + '\" ';
   obj.disp += 'data-inputid=\"' + tempdivid + '\" ';
   if(Boolean(fld.map)) obj.disp += 'data-map=\"' + fld.map + '\" ';
   obj.disp += 'id=\"adafld_' + fld.wd_id + '_' + fld.field_id + '\" ';
   obj.disp += 'style=\"\">';
   
   //Assume the field label is around 12 pixels high
   obj.ht += 12;
   
   obj.dispinput = '';
   obj.displbl = '';
   obj.displbl += '<div class=\"adafldlbl' + fld.field_type + ' adafldlbl\" id=\"adafldlbl_' + fld.wd_id + '_' + fld.field_id + '\" style=\"\">';
   if(Boolean(fld.label) && fld.label!='label') obj.displbl += fld.label;
   obj.displbl += '</div>';
   
   obj.disp += obj.displbl;
   obj.disp += '<div class=\"adafldvaldiv\" id=\"adafldvaldiv_' + fld.wd_id + '_' + fld.field_id + '\" style=\"\">';   
   //Assume the padding between label and value
   obj.ht += 8;
   
   //No height increase needed by default
   obj.incid = false;
   
   obj.inputdivid = tempdivid;
   obj.wd_id = fld.wd_id;
   obj.field_id = fld.field_id;
   
   var optionstoget = [];
   
   if(fld.field_type=='TEXTAREA') {
      obj.rowdisp += '<div class=\"adarowdisp' + fld.map + ' adarowdispTEXTAREA ' + classname + '\" style=\"float:left;margin-right:5px;\">';
      obj.rowdisp += val.substring(0,128);
      obj.rowdisp += '</div>';
      
      var taval = val;
      obj.disp += '<div class=\"adafldval\" id=\"adafldval_' + fld.wd_id + '_' + fld.field_id + '\" style=\"\">';
      obj.disp += val;
      obj.disp += '</div>';
      
      var str = '';
      str += '<div class=\"adafldedt\" id=\"adafldedt_' + fld.wd_id + '_' + fld.field_id + '\" style=\"\">';
      var addlcss = '';
      str += '<textarea ';
      if(Boolean(autofill)) {
         addlcss = 'font-style:normal;color:#000000;';
         if(!Boolean(val) || val==fld.label) {
            taval = fld.label;
            addlcss = 'font-style:italic;color:#999999;';
         }
         str += 'onblur=\"var txt=jQuery(\'#' + tempdivid + '\');if(!Boolean(txt.val()) || txt.val() == \'\'){txt.val(\'' + fld.label + '\');txt.css(\'font-style\',\'italic\').css(\'color\',\'#999999\');}\" ';
         str += 'onfocus=\"var txt=jQuery(\'#' + tempdivid + '\');if(Boolean(txt.val()) && txt.val() == \'' + fld.label + '\'){txt.val(\'\');txt.css(\'font-style\',\'normal\').css(\'color\',\'#000000\');}\" ';
      }
      str += 'id=\"' + tempdivid + '\" ';
      str += 'style=\"font-size:14px;width:' + wd + 'px;height:55px;' + addlcss + '\" ';
      str += '>' + taval + '</textarea>';
      str += '</div>';
      obj.dispinput += str;
      obj.disp += str;
      obj.incid = tempdivid;
      //Assume the field value is a certain height
      obj.ht += 65;
      
      var obj2 = jsfada_getparam(tempdivid,fld);
      obj.savejs += obj2.savejs;
      
      if(!Boolean(val)) obj.js += 'jQuery(\'#adafld_' + fld.wd_id + '_' + fld.field_id + '\').addClass(\'adafldhide\');\n';
      
   } else if(fld.field_type=='FOREIGNSRY' || fld.field_type=='FOREIGNSCT' || fld.field_type=='FOREIGNHYB') {
      if(Boolean(wd_row_id)) {
         var fldarrs = fld.question.split(';');
         
         var str = '';
         str += '<div class=\"adafldval adafldedt\" id=\"' + tempdivid + '\" style=\"font-size:14px;width:' + wd + 'px;height:55px;overflow-x:none;overflow-y:auto;\">';
         str += '</div>';
         obj.disp += str;
         obj.dispinput += str;
         
         var tjs = 'jsfada_loadexternalrows(\'' + tempdivid + '\',\'' + fldarrs[0] + '\',\'' + wd_id + '\',\'' + wd_row_id + '\',\'' + fld.field_id + '\');\n';
         obj.js += tjs;
         obj.dispinput += '\n<script>\n';
         obj.dispinput += tjs;
         obj.dispinput += '\n</script>\n';
         
         obj.incid = tempdivid;
         
         obj.ht += 65;
      } else {
         obj.js += 'jQuery(\'#adafld_' + fld.wd_id + '_' + fld.field_id + '\').hide();\n';
      }
   } else if(fld.field_type=='SNGLCHKBX') {
      if(!Boolean(val) || val.trim().toLowerCase()!='yes') val = 'No';
      obj.disp += '<div class=\"adafldval\" id=\"adafldval_' + fld.wd_id + '_' + fld.field_id + '\" style=\"\">';
      obj.disp += val;
      obj.disp += '</div>';
      
      var str = '';
      str += '<div class=\"adafldedt\" id=\"adafldedt_' + fld.wd_id + '_' + fld.field_id + '\" style=\"\">';
      str += '<input id=\"' + tempdivid + '\" type=\"hidden\" value=\"' + val + '\">';
      str += '<input ';
      str += 'type=\"checkbox\" ';
      str += 'value=\"Yes\" ';
      str += 'id=\"' + tempdivid + '_scb\" ';
      str += 'onclick=\"jQuery(\'#' + tempdivid + '\').val(\'No\');if(document.getElementById(\'' + tempdivid + '_scb\').checked) jQuery(\'#' + tempdivid + '\').val(\'Yes\');jsfada_fieldchange(\'' + fld.wd_id + '\',\'' + fld.field_id + '\');\" ';
      str += 'name=\"' + tempdivid + '_arr[]\"';
      if(Boolean(val) && val.trim().toLowerCase()=='yes') str += ' CHECKED';
      str += '>';
      
      var str2 = str;
      str += '</div>';
      str2 += fld.label;
      str2 += '</div>';
      obj.disp += str;
      obj.dispinput += str2;
      
      obj.ht += 35;
      
      var obj2 = jsfada_getparam(tempdivid,fld);
      obj.savejs += obj2.savejs;
      
      //Show single checkbox input for a table
      obj.rowdisp += '<div class=\"adarowdisp' + fld.map + ' adarowdispSNGLCHKBX ' + classname + '\" style=\"float:left;margin-right:5px;\">';
      if(Boolean(auth)) {
         obj.rowdisp += '<input id=\"row_' + wd_id + '_' + wd_row_id + '_' + fld.field_id + '\"';
         obj.rowdisp += ' type=\"checkbox\" value=\"Yes\"';
         if(Boolean(fld.map)) obj.rowdisp += ' data-map=\"' + fld.map + '\"';
         obj.rowdisp += ' onclick=\"' + jsfada_getstoppropagationjs() + 'jsfada_updatewdcheckbox(\'' + wd_id + '\',\'' + wd_row_id + '\',\'' + fld.field_id + '\');\"';
         if(Boolean(val) && val.trim().toLowerCase()=='yes') obj.rowdisp += ' CHECKED';
         obj.rowdisp += '>';
      } else {
         obj.rowdisp += val;
      }
      obj.rowdisp += '</div>';
      obj.rowdisp += '\n<script>\njsfada_checkarchivedcheckbox(\'' + fld.wd_id + '\',\'' + wd_row_id + '\',\'' + fld.field_id + '\');\n</script>\n';
   } else if(fld.field_type=='MBL_UPL') {
      var str = '';
      str += '<input id=\"' + tempdivid + '\" type=\"hidden\" value=\"' + val + '\">';
      str += '<div class=\"adafldval adafldedt\" id=\"adafldval_' + fld.wd_id + '_' + fld.field_id + '\" style=\"\">';
      
      //var t_obj = jsfada_formatupload(val);
      //obj.disp += t_obj.disp;
      //obj.ht += t_obj.ht;
      obj.ht += 80;

      str += '</div>';
      obj.disp += str;
      obj.dispinput += str;
      
      var tjs = 'jsfada_formatupload(\'' + val + '\',\'' + wd_id + '\',\'' + fld.field_id + '\');\n';
      
      obj.js += tjs;
      obj.dispinput += '\n<script>\n';
      obj.dispinput += tjs;
      obj.dispinput += '\n</script>\n';
      
      
      var obj2 = jsfada_getparam(tempdivid,fld);
      obj.savejs += obj2.savejs;
      
      obj.rowdisp += '<div class=\"adarowdisp' + fld.map + ' adarowdispUPLOAD ' + classname + '\" style=\"float:left;margin-right:5px;\">';
      obj.rowdisp += '<div style=\"position:relative;width:80px;height:40px;overflow:hidden;\">';
      if(Boolean(val)) {
         obj.rowdisp += '<img src=\"' + val + '\" style=\"position:absolute;left:-5px;top:-5px;max-width:100px;max-height:60px;\">';
      }
      obj.rowdisp += '</div>';
      obj.rowdisp += '</div>';
      
      if(Boolean(val) && (val.toLowerCase().endsWith('jpg') || val.toLowerCase().endsWith('jpeg') || val.toLowerCase().endsWith('png'))) {
         obj.dispval = '<img src=\"' + val + '\" style=\"';
         if(Boolean(wd)) obj.dispval += 'width:' + wd + 'px;height:auto;';
         else obj.dispval += 'width:100px;height:auto;';
         obj.dispval += '\">';
      }
   } else if(fld.field_type=='NEWCHKBX' || fld.field_type=='HRZCHKBX' || fld.field_type=='CHECKBOX' || fld.field_type=='FOREIGNTBL' || fld.field_type=='FOREIGNCB') {
      
      var str = '';
      str += '<div class=\"adafldedt' + fld.field_type + ' adafldedt\" id=\"adafldedt_' + fld.wd_id + '_' + fld.field_id + '\" style=\"\">';
      str += '<input id=\"' + tempdivid + '\" type=\"hidden\" value=\"' + val + '\">';
      var newval = '';
      var valarr = val.split(',');
      for(var j=0;j<fld.qopts.names.length;j++) {
         var foundval = false;
         for(var i=0;i<valarr.length;i++) {
            var x = valarr[i].trim().toLowerCase();
            var y = fld.qopts.names[j].trim().toLowerCase();
            var z = fld.qopts.values[j].trim().toLowerCase();
            if(x==y || x==z) {
               if(newval.length>1) newval += ', ';
               newval += fld.qopts.names[j];
               foundval = true;
               break;
            }
         }
         str += '<div class=\"adafldcheckbox\" style=\"position:relative;margin-bottom:10px;\">';
         
         str += '<div style=\"float:left;width:22px;height:32px;overflow:hidden;margin-right:15px;\">';
         str += '<div ';
         str += 'class=\"' + tempdivid + '_cb\" ';
         str += 'style=\"position:relative;cursor:pointer;width:17px;height:17px;overflow:hidden;border:1px solid #000000;\" ';
         str += 'onclick=\"jsfada_updatearrayparam(\'' + tempdivid + '\',\'' + j + '\');jsfada_fieldchange(\'' + fld.wd_id + '\',\'' + fld.field_id + '\');\" ';
         str += 'data-value=\"' + fld.qopts.values[j] + '\" ';
         str += 'data-index=\"' + j + '\" ';
         str += 'data-checked=\"';
         if(foundval) str += '1';
         else str += '0';
         str += '\" ';
         str += '>';
         str += '<div ';
         str += 'id=\"' + tempdivid + '_cb' + j + '\" ';
         str += 'style=\"margin-left:2px;margin-top:2px;width:13px;height:13px;overflow:hidden;background-color:#4d95e8;';
         if(!foundval) str += 'display:none;';
         str += '\" ';
         str += '>';
         str += '</div>';
         str += '</div>';
         str += '</div>';
         str += '<div class=\"adafldcheckboxtxt\" style=\"float:left;\">';
         str += fld.qopts.names[j];
         str += '</div>';
         str += '<div style=\"clear:both;\"></div>';
         
         str += '</div>';
         obj.ht += 25;
      }
      str += '</div>';
      
      obj.disp += '<div class=\"adafldval\" id=\"adafldval_' + fld.wd_id + '_' + fld.field_id + '\" style=\"\">';
      obj.disp += newval;
      obj.disp += '</div>';
      obj.disp += str;
      obj.dispinput += str;
      obj.ht += 14;
      
      obj.dispval = newval;
            
      //var obj2 = jsfada_getarrayparam(tempdivid,fld);
      var obj2 = jsfada_getparam(tempdivid,fld);
      obj.savejs += obj2.savejs;
      
      obj.rowdisp += '<div class=\"adarowdisp' + fld.map + ' adarowdispCHECKBOX ' + classname + '\" style=\"float:left;margin-right:5px;\">';
      obj.rowdisp += newval.substring(0,128);
      obj.rowdisp += '</div>';
   } else if(fld.field_type=='RADIO') {
      var str = '';
      str += '<div class=\"adafldedt' + fld.field_type + ' adafldedt\" id=\"adafldedt_' + fld.wd_id + '_' + fld.field_id + '\" style=\"\">';
      str += '<input id=\"' + tempdivid + '\" type=\"hidden\" value=\"' + val + '\">';
      var newval = val;
      for(var j=0;j<fld.qopts.names.length;j++) {
         
         var foundval = false;         
         if(val==fld.qopts.values[j] || val==fld.qopts.names[j]) foundval = true;
         
         
         str += '<div class=\"adafldradiobutton\" style=\"float:left;width:22px;height:22px;overflow:hidden;margin-right:10px;\">';
         str += '<div ';
         str += 'style=\"position:relative;cursor:pointer;width:18px;height:18px;overflow:hidden;border:1px solid #000000;border-radius:9px;\" ';
         str += 'onclick=\"jQuery(\'#' + tempdivid + '\').val(\'' + fld.qopts.values[j] + '\');jQuery(\'.' + tempdivid + '_rb\').hide();jQuery(\'#' + tempdivid + '_rb' + j + '\').show();jsfada_fieldchange(\'' + fld.wd_id + '\',\'' + fld.field_id + '\');\" ';
         str += 'data-value=\"' + fld.qopts.values[j] + '\" ';
         str += 'data-index=\"' + j + '\" ';
         str += 'data-checked=\"';
         if(foundval) str += '1';
         else str += '0';
         str += '\" ';
         str += '>';
         str += '<div ';
         str += 'id=\"' + tempdivid + '_rb' + j + '\" ';
         str += 'class=\"' + tempdivid + '_rb\" ';
         str += 'style=\"margin-left:2px;margin-top:2px;width:14px;height:14px;overflow:hidden;background-color:#4d95e8;border-radius:7px;';
         if(!foundval) str += 'display:none;';
         str += '\" ';
         str += '>';
         str += '</div>';
         str += '</div>';
         str += '</div>';
         
         str += '<div class=\"adafldradiotxt\" style=\"float:left;margin-top:6px;margin-right:35px;\">';
         str += fld.qopts.names[j];
         str += '</div>';
         
         if(j==1 || fld.qopts.names.length>2) {
            str += '<div style=\"clear:both;\"></div>';
            obj.ht += 25;
         }
      }
      str += '</div>';
      
      obj.disp += '<div class=\"adafldval\" id=\"adafldval_' + fld.wd_id + '_' + fld.field_id + '\" style=\"\">';
      obj.disp += newval;
      obj.disp += '</div>';
      obj.disp += str;
      obj.dispinput += str;
      obj.ht += 14;
            
      obj.dispval = newval;
            
      var obj2 = jsfada_getparam(tempdivid,fld);
      obj.savejs += obj2.savejs;
      
      obj.rowdisp += '<div class=\"adarowdisp' + fld.map + ' adarowdispRADIO ' + classname + '\" style=\"float:left;margin-right:5px;\">';
      obj.rowdisp += newval.substring(0,128);
      obj.rowdisp += '</div>';
   } else if(fld.field_type=='STATE' || fld.field_type=='DROPDOWN' || fld.field_type=='FOREIGN' || fld.field_type=='FOREIGNTDD') {
      var newval='';
      var str = '';
      str += '<div class=\"adafldedt\" id=\"adafldedt_' + fld.wd_id + '_' + fld.field_id + '\" style=\"\">';
      str += '<select class=\"adafldselect\" id=\"' + tempdivid + '\" onchange=\"jsfada_fieldchange(\'' + fld.wd_id + '\',\'' + fld.field_id + '\');\">';
      str += '<option value=\"\"></option>';
      for(var j=0;j<fld.qopts.names.length;j++) {
         var foundval = false;
         var x = val.trim().toLowerCase();
         var y = fld.qopts.names[j].trim().toLowerCase();
         var z = fld.qopts.values[j].trim().toLowerCase();
         if(x==y || x==z) {
            newval = fld.qopts.names[j];
            foundval = true;
         }
         str += '<option value=\"' + fld.qopts.values[j] + '\"';
         if(foundval) str += ' SELECTED';
         str += '>';
         str += fld.qopts.names[j];
         str += '</option>';
      }
      str += '</select>';
      str += '</div>';
      
      obj.disp += '<div class=\"adafldval\" id=\"adafldval_' + fld.wd_id + '_' + fld.field_id + '\" style=\"\">';
      obj.disp += newval;
      obj.disp += '</div>';
      obj.disp += str;
      obj.dispinput += str;
      obj.ht += 38;
            
      obj.dispval = newval;
            
      var obj2 = jsfada_getparam(tempdivid,fld);
      obj.savejs += obj2.savejs;
      
      obj.rowdisp += '<div class=\"adarowdisp' + fld.map + ' adarowdispDROPDOWN ' + classname + '\" style=\"float:left;margin-right:5px;\">';
      obj.rowdisp += newval.substring(0,128);
      obj.rowdisp += '</div>';
   } else if(fld.field_type=='INFO') {
      obj.dispval = fld.label;
      obj.disp += '';
      obj.dispinput += '';
      
      //Assume the field value is a certain height
      //obj.ht += 32;
      
      obj.rowdisp += '<div class=\"adarowdisp' + fld.map + ' adarowdispINFO ' + classname + '\" style=\"float:left;margin-right:5px;\">';
      //obj.rowdisp += fld.label.substring(0,128);
      obj.rowdisp += '</div>';
   } else if(fld.field_type=='SPACER') {
      //obj.dispval = fld.label;
      obj.dispval = '';
      obj.disp += '<div style=\"width:10px;height:20px;overflow:hidden;\"></div>';
      obj.dispinput += '';
      
      //Assume the field value is a certain height
      obj.ht += 32;
      
      obj.rowdisp += '<div class=\"adarowdisp' + fld.map + ' adarowdispSPACER ' + classname + '\" style=\"float:left;margin-right:5px;\">';
      //obj.rowdisp += fld.label.substring(0,128);
      obj.rowdisp += '</div>';
   } else {
      var dispval = val;
      var taval = val;
      
      if(fld.map.toLowerCase().includes('url') && val.toLowerCase().startsWith('http')) {
         dispval = '<a href=\"' + val + '\" target=\"_new\">' + val + '</a>';
      }
      
      obj.dispval = dispval;
            
      obj.disp += '<div class=\"adafldval\" id=\"adafldval_' + fld.wd_id + '_' + fld.field_id + '\" style=\"\">';
      obj.disp += dispval;
      obj.disp += '</div>';
      
      var str = '';
      str += '<div class=\"adafldedt\" id=\"adafldedt_' + fld.wd_id + '_' + fld.field_id + '\" style=\"\">';
      str += '<input ';
      var addlcss = '';
      if(Boolean(autofill)) {
         addlcss = 'font-style:normal;color:#000000;';
         if(!Boolean(val) || val==fld.label) {
            taval = fld.label;
            addlcss = 'font-style:italic;color:#999999;';
         }
         str += 'onblur=\"var txt=jQuery(\'#' + tempdivid + '\');if(!Boolean(txt.val()) || txt.val() == \'\'){txt.val(\'' + fld.label + '\');txt.css(\'font-style\',\'italic\').css(\'color\',\'#999999\');}\" ';
         str += 'onfocus=\"var txt=jQuery(\'#' + tempdivid + '\');if(Boolean(txt.val()) && txt.val() == \'' + fld.label + '\'){txt.val(\'\');txt.css(\'font-style\',\'normal\').css(\'color\',\'#000000\');}\" ';
      }
      str += 'type=\"text\" ';
      str += 'id=\"' + tempdivid + '\" ';
      str += 'onkeyup=\"\" '
      str += 'value=\"' + jsfcore_replaceAll('\"','&quot;',taval) + '\" ';
      str += 'style=\"font-size:14px;width:' + wd + 'px;' + addlcss + '\">';
      str += '</div>';
      
      obj.disp += str;
      obj.dispinput += str;
      
      //Assume the field value is a certain height
      obj.ht += 32;
      var obj2 = jsfada_getparam(tempdivid,fld);
      obj.savejs += obj2.savejs;
      
      
      if(fld.field_type=='DATE' && typeof showCalendarInput === 'function') {
         var tjs = '';
         tjs += 'var newtxt = showCalendarInput(\'' + tempdivid + '\',\'' + val + '\',\'\',1);\n';
         tjs += 'jQuery(\'#adafldedt_' + fld.wd_id + '_' + fld.field_id + '\').html(newtxt);\n';
         obj.js += tjs;
         obj.dispinput += '\n<script>\n';
         obj.dispinput += tjs;
         obj.dispinput += '\n</script>\n';
      }
      if(!Boolean(val)) obj.js += 'jQuery(\'#adafld_' + fld.wd_id + '_' + fld.field_id + '\').addClass(\'adafldhide\');\n';
      
      obj.rowdisp += '<div class=\"adarowdisp' + fld.map + ' adarowdisp' + fld.field_type + ' ' + classname + '\" style=\"float:left;margin-right:5px;\">';
      obj.rowdisp += val.substring(0,128);
      obj.rowdisp += '</div>';
   }
   obj.disp += '</div>';
   obj.disp += '</div>';
   obj.disp += '</div>';
   return obj;
}

function jsfada_fieldchange(wd_id,field_id) {
   //alert('jsfada_fieldchange(' + wd_id + ', ' + field_id + ')');
   
   //if(!Boolean(field_id)) jsfada_fielddefaults(wd_id);
   
   // Set all fields...
   var fldrels = jsfada_tablesfields[jsfcore_flattenstr(wd_id,false,true)].fieldrels;
   wd_id = jsfada_tablesfields[jsfcore_flattenstr(wd_id,false,true)].webdata.wd_id;
   
   if(Boolean(fldrels)) {
      //alert('jsfada_tablesfields: ' + JSON.stringify(jsfada_tablesfields));
      //alert('fieldrels: ' + JSON.stringify(fldrels));
      
      //var hideflds = fldrels.field2;
      var hideflds = fldrels.field1;
      var hidescts = fldrels.section;
      
      //alert('fieldrels: ' + JSON.stringify(fldrels));
      //alert('hideflds: ' + JSON.stringify(hideflds));
      //alert('hidesects: ' + JSON.stringify(hidescts));
      
      
      //for (var fld in hideflds) jQuery('#adafld_' + wd_id + '_' + fld.field_id).hide();
      for (var fld in hideflds) {
         //if(fld=='q165') alert('field: ' + fld);
         if(!Boolean(field_id) || field_id == fld) {
            var divid = '#w' + wd_id + 'a' + fld;
            //alert('divid: ' + divid);
            var val = jQuery(divid).val().toLowerCase();
            for(var i=0;i<hideflds[fld].length;i++) {
               //if(fld=='q156') alert('found q156, rel: ' + hideflds[fld][i].fid2);
               var field = hideflds[fld][i].fid2;
               jQuery('#adafld_' + wd_id + '_' + field).hide();
               
               var val2 = jsfcore_replaceAll('  ',' ',hideflds[fld][i].f1value.trim().toLowerCase());
               val2 = jsfcore_replaceAll(' ,',',',val2);
               val2 = jsfcore_replaceAll(', ',',',val2);
               if(val == val2 || val2.startsWith(val + ',') || val2.endsWith(',' + val) || val2.includes(',' + val + ',')) {
                  jQuery('#adafld_' + wd_id + '_' + field).show();
               }
            }
         }
      }
      
      for (var fld in hidescts) {
         if(!Boolean(field_id) || field_id == fld) {
            var divid = '#w' + wd_id + 'a' + fld;
            //alert('divid: ' + divid);
            var val = jQuery(divid).val().toLowerCase();
            for(var i=0;i<hidescts[fld].length;i++) {
               var sctn = hidescts[fld][i].fid2;
               //alert('found section field ' + fld + ' for sect: ' + sctn);
               jQuery('#adasect_' + wd_id + '_' + sctn).hide();
               
               var val2 = jsfcore_replaceAll('  ',' ',hidescts[fld][i].f1value.trim().toLowerCase());
               val2 = jsfcore_replaceAll(' ,',',',val2);
               val2 = jsfcore_replaceAll(', ',',',val2);
               
               //alert('looking for val: ' + val + ' have val: ' + val2);
               
               if(val == val2 || val2.startsWith(val + ',') || val2.endsWith(',' + val) || val2.includes(',' + val + ',')) {
                  jQuery('#adasect_' + wd_id + '_' + sctn).show();
               }
            }
         }
      }
   }
}

/*
function jsfada_fielddefaults(wd_id) {
   var fldrels = jsfada_tablesfields[wd_id].fieldrels;
   //var hideflds = fldrels.field2;
   var hideflds = fldrels.field1;
   var hidescts = fldrels.section;
   
   //for (var fld in hideflds) jQuery('#adafld_' + wd_id + '_' + fld.field_id).hide();
   for (var fld in hideflds) {
      for(var i=0;i<hideflds[fld].length;i++) {
         var field = hideflds[fld][i].fid2;
         jQuery('#adafld_' + wd_id + '_' + field).hide();
      }
   }
   
   for (var sct in hidescts) {
      for(var i=0;i<hidescts[sct].length;i++) {
         var sctn = hidescts[sct][i].fid2;
         jQuery('#adasect_' + wd_id + '_' + sctn).hide();
      }
   }
   
   alert('field rels: ' + JSON.stringify(fldrels));
}
*/


function jsfada_updatewdcheckbox(wd_id,wd_row_id,field_id){
   var value = jsfada_checkarchivedcheckbox(wd_id,wd_row_id,field_id);
   jsfada_updatewdfieldvalue(wd_id,wd_row_id,field_id,value);
}

function jsfada_checkarchivedcheckbox(wd_id,wd_row_id,field_id) {
   var value = 'NO';
   
   if(document.getElementById('row_' + wd_id + '_' + wd_row_id + '_' + field_id).checked) value='YES';
   
   var map = jQuery('#row_' + wd_id + '_' + wd_row_id + '_' + field_id).data('map');
   //alert('row_' + wd_id + '_' + wd_row_id + '_' + field_id + ' map: ' + map);
   if(Boolean(map) && (map=='archive' || map=='archived' || map=='removed')) {
      if(value.toUpperCase()=='YES') jQuery('.jsfwdtableitem[data-wdrowid=\"' + wd_row_id + '\"]').css('text-decoration','line-through');
      else jQuery('.jsfwdtableitem[data-wdrowid=\"' + wd_row_id + '\"]').css('text-decoration','');
   }
   return value;
}

function jsfada_updatewdfieldvalue(wd_id,wd_row_id,field_id,value,callback){
   var noloading = false;
   if(!Boolean(callback)) {
      callback = 'jsfcore_donothing';
      noloading = true;
   }
   var url = '';
   url += '&wd_id=' + wd_id;
   url += '&wd_row_id=' + wd_row_id;
   url += '&field=' + encodeURIComponent(field_id);
   url += '&value=' + encodeURIComponent(value);
   url += '&userid=' + encodeURIComponent(jsfcore_globaluser.userid);
   url += '&token=' + encodeURIComponent(jsfcore_globaluser.token);
   jsfcore_QuickJSON('submitwdfield',callback,url,false,noloading);
   
   var rows = jsfada_mostrecentrows[jsfcore_flattenstr(wd_id,false,true)];
   for(var i=0;i<rows.length;i++) {
      if(parseInt(rows[i].wd_row_id) == parseInt(wd_row_id)) {
         rows[i][field_id] = value;
         break;
      }
   }
   
}

function jsfada_getstoppropagationjs() {
   var str = 'if (!event) var event = window.event;event.cancelBubble = true;if (event.stopPropagation) event.stopPropagation();';
   return str;
}

function jsfada_formatupload(val,wd_id,field_id) {
   var tempdivid = 'w' + wd_id + 'a' + field_id;
   var disp = '';
   disp = '';
   if(val.toLowerCase().endsWith('.jpg') || val.toLowerCase().endsWith('.jpeg') || val.toLowerCase().endsWith('.png') || val.toLowerCase().endsWith('.gif')) {
      jQuery('#' + tempdivid).val(val);
      disp += '<div style=\"position:relative;width:250px;height:60px;\">';
      disp += '<a href=\"' + jsfcore_replaceAll('http://','https://',val) + '\" style=\"z=index:2;target=\"_new\">';
      disp += '<img src=\"' + jsfcore_replaceAll('http://','https://',val) + '\" style=\"max-width:250px;max-height:60px;\" border=\"0\">';
      disp += '</a>';
      disp += '<div onclick=\"' + jsfada_getstoppropagationjs() + 'if(confirm(\'Are you sure you want to delete this image?\')) jsfada_formatupload(\'%E%\',\'' + wd_id + '\',\'' + field_id + '\');\" style=\"position:absolute;left:5px;top:5px;z-index:3;width:16px;height:16px;overflow:hidden;background-color:#AA1212;border-radius:8px;color:#FFFFFF;font-weight:bold;text-align:center;margin-top:3px;font-size:12px;cursor:pointer;\">x</div>';
      disp += '</div>';
   } else if(Boolean(val) && val!='%E%') {
      jQuery('#' + tempdivid).val(val);
      var tarr = val.split('/');
      disp += '<div>';
      disp += '<a href=\"' + jsfcore_replaceAll('http://','https://',val) + '\" target=\"_new\">';
      disp += tarr[(tarr.length - 1)];
      disp += '</a>';
      disp += '</div>';
      disp += '<div onclick=\"' + jsfada_getstoppropagationjs() + 'if(confirm(\'Are you sure you want to delete this file?\')) jsfada_formatupload(\'%E%\',\'' + wd_id + '\',\'' + field_id + '\');\" style=\"font-size:10px;color:red;cursor:pointer;\">remove</div>';
   } else {
      jQuery('#' + tempdivid).val('%E%');
      disp += '<div ';
      disp += ' class=\"jsfcore_btn jsfadaedt\"';
      disp += ' style=\"margin:5px;\"';
      disp += ' onclick=\"window.open(\'' + jsfcore_domain + 'jsfcode/upload.php?userid=9&token=9&prefix=jsfwd&wd_id=' + wd_id + '&field_id=' + field_id + '\');\"';
      disp += '>';
      disp += 'Upload';
      disp += '</div>';
   }
   
   tempdivid = 'adafldval_' + wd_id + '_' + field_id;
   jQuery('#' + tempdivid).html(disp);
}


function jsfada_ReceiveMessage(e){
   var databack = e.data;
   var databack_a = databack.split(',');
   var p = databack_a[0];
   var f = databack_a[1];
   var w = databack_a[2];
   var fn = databack_a[3];
   jsfada_formatupload(fn,w,f);
}
window.addEventListener('message', jsfada_ReceiveMessage, false);



function jsfada_loadexternalrows(divid,wd_id,o_wd_id,o_wd_row_id,o_field_id){
   jQuery('#' + divid).html('<div style=\"margin:5px;font-size:8px;\">loading...</div>');
   var params = '';
   params += '&o_wd_row_id=' + encodeURIComponent(o_wd_row_id);
   params += '&o_wd_id=' + encodeURIComponent(o_wd_id);
   params += '&o_field_id=' + encodeURIComponent(o_field_id);
   params += '&divid=' + encodeURIComponent(divid);
   params += '&userid=' + encodeURIComponent(jsfcore_globaluser.userid);
   params += '&token=' + encodeURIComponent(jsfcore_globaluser.token);
   params += '&limit=10';
   params += '&maxcol=5';
   params += '&addrowdisplay=1';
   
   //alert('jsfada_loadexternalrows::calling jsfcore_getwebdata_jsonp');
   jsfcore_getwebdata_jsonp(wd_id,'jsfada_returnexternalrows',params,false,true,true);
}

function jsfada_returnexternalrows(jsondata){
   jsfcore_ReturnJSON(jsondata);
   var str = '';
   if(Boolean(jsondata) && Boolean(jsondata.rows) && jsondata.rows.length>=1) {
      for(var i=0;i<jsondata.rows.length;i++) {
         
         var delbtn2 = jsfwebdata_builddeletebutton(jsondata.wd_id,jsondata.rows[i].wd_row_id,jsondata.rows[i].origemail,jsondata.divid);
         
         str += '<div style=\"margin-top:4px;padding-top:4px;border-top:1px solid #EFEFEF;\">';
         //str += jsondata.rows[i].display;
         str += jsfcore_replaceAll('%%%DELETE%%%',delbtn2,jsondata.rows[i].display);
         str += '</div>';
      }
   } else {
      var tdivhide = 'adafld_' + jsondata.o_wd_id + '_' + jsondata.o_field_id;
      //alert('no rows... ' + tdivhide);
      jQuery('#' + tdivhide).hide();
   }
   
   jQuery('#' + jsondata.divid).html(str);
}

//function jsfada_showlightboxwebdata(wd_id,wd_row_id) {
//   var str = '';
//   
//   jsfcore_showlightbox(str);
//}

function jsfada_getparam(paramid,fld) {
   //alert('rqd: ' + fld.required + ' doing field: ' + JSON.stringify(fld));
   var maxurlsize = 2000;
   var obj = {};
   obj.savejs = '';
   
   obj.savejs += 'temp = jQuery(\'#' + paramid + '\').val();\n';
   if(Boolean(fld.required) && fld.required!='0') {
      obj.savejs += 'if(!Boolean(temp)) {\n';
      obj.savejs += '  rqderror = true;\n';
      obj.savejs += '  rqderrorstr += \'Please enter a value for: ' + fld.label + '\';\n';
      obj.savejs += '}\n';
   }
   obj.savejs += 'if(!rqderror) {\n';
   obj.savejs += '  if(!Boolean(temp)) temp=\'%E%\';\n';
   obj.savejs += '  if((url.length + temp.length) > ' + maxurlsize + ') {\n';
   obj.savejs += '    var totalc = 0;\n';
   obj.savejs += '    while(totalc < temp.length) {\n';
   obj.savejs += '      c_urls.push(url + \'&chj=\' + c_urls.length);\n';
   obj.savejs += '      url = c_url;\n';
   obj.savejs += '      var l = ' + maxurlsize + ' - url.length;\n';
   obj.savejs += '      var p = \'&' + paramid + '\';\n';
   obj.savejs += '      if(totalc>0) p += \'_append\';\n';
   obj.savejs += '      url = url + p + \'=\' + encodeURIComponent(temp.substr(totalc,l));\n';
   obj.savejs += '      totalc = totalc + l;\n';
   obj.savejs += '    }\n';
   obj.savejs += '  } else {\n';
   obj.savejs += '    url = url + \'&' + paramid + '=\' + encodeURIComponent(temp);\n';
   obj.savejs += '  }\n';
   obj.savejs += '}\n';
   
   return obj;
}




function jsfada_updatearrayparam(paramid,j) {
   var temp = '';
   
   jQuery('.' + paramid + '_cb').each(function(index) {
      var c = $(this).data('checked');
      var t = $(this).data('value');
      var i = $(this).data('index');
      
      if(i==j) {
         if(c=='1') c = '0';
         else c = '1';
      }
      
      if(c=='1') {
         if(temp.length > 0) temp += ',';
         temp += t;
         jQuery('#' + paramid + '_cb' + i).show();
         jQuery(this).data('checked','1');
      } else {
         jQuery(this).data('checked','0');
         jQuery('#' + paramid + '_cb' + i).hide();
      }
   });
   
   if(temp.length < 1) temp = '%E%';
   //alert('temp: ' + temp);
   jQuery('#' + paramid).val(temp);
}


/*
function jsfada_getarrayparam(paramid,fld) {
   var obj = {};
   obj.savejs = '';
   
   obj.savejs += 'temp = \'\';\n';
   obj.savejs += 'var chk_' + paramid + ' =  document.getElementsByName(\'' + paramid + '[]\');\n';
   obj.savejs += 'var chklen_' + paramid + ' = chk_' + paramid + '.length;\n';             
   obj.savejs += 'for(var k=0;k<chk_' + paramid + '.length;k++) {\n';
   obj.savejs += '  if(chk_' + paramid + '[k].checked) {\n';
   obj.savejs += '    if(temp.length > 1) temp += \',\';\n';
   obj.savejs += '    temp += chk_' + paramid + '[k].value;\n';
   obj.savejs += '  }\n';   
   obj.savejs += '}\n';
   obj.savejs += 'if(temp.length < 2) temp = \'%E%\';\n';
   obj.savejs += 'jQuery(\'#' + paramid + '\').val(temp);\n';
   
   var obj2 = jsfada_getparam(paramid,fld);
   obj.savejs += obj2.savejs;
   
   return obj;
}
*/


function jsfada_savedata() {
   // save data ...
   
   jsfada_submitwd();
   //jsfada_viewonlydata();
   //jsfada_displaytable(jsfada_currtableid);
}

function jsfada_returnsavedata(jsondata){
	//alert('in jsfada_returnsavedata');
   if(Boolean(jsfada_urls) && jsfada_urls.length>0) {
      url = jsfada_urls.shift();
      //alert('saving row: ' + url);
      jsfcore_CallJSONP(url);
   //} else if(Boolean(jsondata)) {
   //   jsfada_recordclick_return(jsondata);
   } else {
      jsfada_displaytableresults(jsondata.wd_id,jsfwd_filterstr,jsfwd_limit,jsfwd_pagenum,jsfada_ignoreforuser);
   }
}

// When a submit webdata occurs, this function is called
// at the end (in this case to display that record again)
function jsfwd_executefiltersearch_finished() {
   //alert('filter string: ' + jsfwd_filterstr);
   if(Boolean(jsfada_currtableid) && Boolean(jsfada_currrecordid)) {
      var jsondata = {};
      jsondata.wd_id = jsfada_currtableid;
      jsondata.wd_row_id = jsfada_currrecordid;
      jsfada_recordclick_return(jsondata);
   }
}

function jsfada_viewonlydata() {
   jQuery('.adafldhide').hide();
   jQuery('.adafldedt').hide();
   jQuery('.adafldsave').hide();
   jQuery('.adafldval').show();
   jQuery('.adafldedit').show();
}

function jsfada_editdata() {
   jQuery('.adafldval').hide();
   jQuery('.adafldedit').hide();
   jQuery('.adafldhide').show();
   jQuery('.adafldedt').show();
   jQuery('.adafldsave').show();
}
