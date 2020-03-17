var jsfpb_userid;
var jsfpb_token;
var jsfpb_admindivid = 'body';
var jsfpb_tablename = 'Roadmap Tool Values';
var jsfpb_backclick = '';



// A single page has several rows.
// a single row has several slots (width,sequence, enabled)
// a single slot has several layers (image, text, html, etc)
var jsfpb_page = {};
jsfpb_page.rows = [];
var jsfpb_changeswaiting=0;


// GET the jdata information - this will depend on the jdata name!!
var jsfpb_values = [];
var jsfpb_wd_id;

var jsfpb_temp_wdrowid;
function jsfpb_primedata(wd_row_id){
   //alert('primedata: ' + wd_row_id);
   jQuery('#jsfpb_loading').show();
   jsfpb_temp_wdrowid = wd_row_id;
   callback = 'jsfpb_returnvalues';
   var params ='';
   params += '&cmsenabled=1';
   params += '&maxcol=8';
   jsfpb_getwebdata_jsonp(jsfpb_tablename,callback,params);   
}

function jsfpb_checkifchanges(){
   var oktogo = true;
   if(jsfpb_changeswaiting==1) {
      if(!confirm('You have changes waiting.  Do you want to continue without saving?')) {
         oktogo=false;
      }
   }
   return oktogo;
}

function jsfpb_returnvalues(jsondata){
   jsfpb_values = jsondata.rows;   
   jsfpb_wd_id = jsondata.wd_id;
   
   var str = '';
   str = '';
   if(Boolean(jsfpb_backclick)) {
      str += '<span onclick=\"if(jsfpb_checkifchanges()) {' + jsfpb_backclick + '}\" style=\"font-size:10px;margin-right:20px;cursor:pointer;color:blue;\">';
      str += '&lt; back';
      str += '</span>';
   }
   str += 'Edit page: ';
   str += jsfpb_pagesselection('sel_pg_name','jsfpb_openpagetool();');
   str += ' &nbsp; &nbsp; ';
   str += '<span id=\"msg_pagelock\"></span>';
   str += ' &nbsp; &nbsp; ';
   str += '<span id=\"btn_pagelock\"></span>';
   str += ' &nbsp; &nbsp; ';
   str += '<input type=\"text\" id=\"new_pg_name\" style=\"font-size:10px;font-family:arial;width:130px;\">';
   str += '<span onclick=\"if (Boolean(jQuery(\'#new_pg_name\').val())) jsfpb_savepagechunks(jQuery(\'#new_pg_name\').val());\" style=\"margin-left:4px;padding:3px;border-radius:4px;font-size:8px;color#777777;border:1px solid #777777;background-color:#FFFFFF;cursor:pointer;\">Add New Page</span>';
   str += ' &nbsp; &nbsp; ';
   str += '<span onclick=\"jsfpb_schedulebackup();\" style=\"margin-left:4px;padding:3px;border-radius:4px;font-size:8px;color#777777;border:1px solid #777777;background-color:#FFFFFF;cursor:pointer;\">Backup Active Pages</span>';
   jQuery('#topadmin').html(str);
   
   //Drop down is set... now set the default page up
   if(Boolean(jsfpb_temp_wdrowid)){
      for(var i=0;i<jsfpb_values.length;i++){
         if(jsfpb_values[i].wd_row_id==jsfpb_temp_wdrowid) {
            //alert('found row: ' + jsfpb_values[i].wd_row_id);
            
            jQuery('#sel_pg_name').val(jsfpb_pagemappings[i.toString()]);
            //alert('i: ' + i);
            jsfpb_openpagetool(jsfpb_values[i].version);
            break;
         }
      }
      temp_remembjer_wdrowid = '';
   }
   jsfpb_changeswaiting = 0;
   jQuery('#jsfpb_loading').hide();
}




var jsf_ineditmode = false;
function jsfpb_checklock() {
   jsf_ineditmode = false;
   jQuery('#msg_pagelock').hide();
   jQuery('#btn_pagelock').hide();
   jQuery('#btn_pagesave').hide();
   var i = jQuery('#sel_pg_name').val();
   if(Boolean(jsfpb_values[i]) && Boolean(jsfpb_values[i].value)){
      jQuery('#jsfpb_loading').show();
      
      var name = jsfpb_values[i].name;

      var query = '';
      query += '&subaction=get';
      query += '&wd_id=' + jsfpb_wd_id;
      query += '&name=' + encodeURIComponent(name + '_jsflock');
      query += '&lookupuser=1';
      query += '&userid=' + encodeURIComponent(jsfpb_userid);
      query += '&token=' + encodeURIComponent(jsfpb_token);
      jsfpb_QuickJSON('jsfnvp','jsfpb_checklock_return',query);
   }
}

function jsfpb_checklock_return(jsondata) {
   if(jsondata.responsecode==0 || !Boolean(jsondata.value)) {
      document.getElementById('btn_pagelock').onclick = jsfpb_trylocking;
      jQuery('#btn_pagelock').css('cursor','pointer').css('padding','3px').css('font-size','8px').css('font-weight','bold').css('color','#111111').css('border','1px solid #777777').css('border-radius','4px').css('background-color','#FFE0E0');
      jQuery('#btn_pagelock').html('Lock This Page');
      jQuery('#btn_pagelock').show();
   } else if(parseInt(jsfpb_userid)==parseInt(jsondata.value)) {
      document.getElementById('btn_pagelock').onclick = jsfpb_tryunlocking;
      jQuery('#btn_pagelock').css('cursor','pointer').css('padding','3px').css('font-size','8px').css('font-weight','bold').css('color','#111111').css('border','1px solid #777777').css('border-radius','4px').css('background-color','#E0FFE0');
      jQuery('#btn_pagelock').html('Unlock This Page');
      jQuery('#btn_pagelock').show();
      jQuery('#btn_pagesave').show();
      jsf_ineditmode = true;
   } else {
      document.getElementById('btn_pagelock').onclick = jsfpb_forceunlocking;
      jQuery('#btn_pagelock').css('cursor','pointer').css('padding','3px').css('font-size','8px').css('font-weight','bold').css('color','#111111').css('border','1px solid #777777').css('border-radius','4px').css('background-color','#E0FFE0');
      jQuery('#btn_pagelock').html('Force Unlock');
      jQuery('#btn_pagelock').show();
      
      //document.getElementById('btn_pagelock').onclick = null;
      jQuery('#msg_pagelock').css('cursor','auto').css('padding','0px').css('font-size','12px').css('font-weight','bold').css('color','#AA2222').css('border','0').css('border-radius','0px').css('background-color','#ffffff');
      jQuery('#msg_pagelock').html('This page is locked by ' + jsondata.user.email);
      jQuery('#msg_pagelock').show();
   }
   jQuery('#jsfpb_loading').hide();
}

function jsfpb_trylocking() {
   jQuery('#btn_pagelock').hide();
   var i = jQuery('#sel_pg_name').val();
   if(Boolean(jsfpb_values[i]) && Boolean(jsfpb_values[i].value)){
      jQuery('#jsfpb_loading').show();
      
      var name = jsfpb_values[i].name;

      var query = '';
      query += '&subaction=checkandset';
      query += '&wd_id=' + jsfpb_wd_id;
      query += '&name=' + encodeURIComponent(name + '_jsflock');
      query += '&check=' + encodeURIComponent(jsfpb_userid);
      query += '&value=' + encodeURIComponent(jsfpb_userid);
      query += '&userid=' + encodeURIComponent(jsfpb_userid);
      query += '&token=' + encodeURIComponent(jsfpb_token);
      //query += '&lookupuser=1';
      jsfpb_QuickJSON('jsfnvp','jsfpb_trylocking_return',query);
   }
}

function jsfpb_forceunlocking() {
   if(confirm('Are you absolutely sure you want to unlock a page that was not originally locked by you?')){
      jQuery('#btn_pagelock').hide();
      jQuery('#msg_pagelock').hide();
      var i = jQuery('#sel_pg_name').val();
      if(Boolean(jsfpb_values[i]) && Boolean(jsfpb_values[i].value)){
         jQuery('#jsfpb_loading').show();
         
         var name = jsfpb_values[i].name;
   
         var query = '';
         query += '&subaction=set';
         query += '&wd_id=' + jsfpb_wd_id;
         query += '&name=' + encodeURIComponent(name + '_jsflock');
         query += '&value=' + encodeURIComponent(' ');
         query += '&userid=' + encodeURIComponent(jsfpb_userid);
         query += '&token=' + encodeURIComponent(jsfpb_token);
         jsfpb_QuickJSON('jsfnvp','jsfpb_trylocking_return',query);
      }
   }
}

function jsfpb_tryunlocking() {
   jQuery('#btn_pagelock').hide();
   var i = jQuery('#sel_pg_name').val();
   if(Boolean(jsfpb_values[i]) && Boolean(jsfpb_values[i].value)){
      jQuery('#jsfpb_loading').show();
      
      var name = jsfpb_values[i].name;

      var query = '';
      query += '&subaction=checkandset';
      query += '&wd_id=' + jsfpb_wd_id;
      query += '&name=' + encodeURIComponent(name + '_jsflock');
      query += '&check=' + encodeURIComponent(jsfpb_userid);
      query += '&value=' + encodeURIComponent(' ');
      query += '&userid=' + encodeURIComponent(jsfpb_userid);
      query += '&token=' + encodeURIComponent(jsfpb_token);
      jsfpb_QuickJSON('jsfnvp','jsfpb_trylocking_return',query);
   }
}

function jsfpb_trylocking_return(jsondata) {
   jsfpb_checklock();
}




function jsfpb_schedulebackup() {
   var flt_name = jsfpb_flattenstr(jsfpb_tablename);
   
   var url = jsfpb_domain + jsfpb_jsoncontroller;
   url += '&action=getwdandrows';
   url += '&wd_id=' + encodeURIComponent(jsfpb_tablename);
   url += '&cmsq_' + flt_name + '_enabled=yes';
   url += '&cmsq_' + flt_name + '_verstatus=ACTIVE';
   url += '&userid=' + encodeURIComponent(jsfpb_userid);
   url += '&token=' + encodeURIComponent(jsfpb_token);
   
   var query = '';
   query += '&subj=' + encodeURIComponent(jsfpb_getshortdate() + ' ' + jsfpb_tablename + ' backup');
   query += '&json=' + encodeURIComponent(url);
   query += '&userid=' + encodeURIComponent(jsfpb_userid);
   query += '&token=' + encodeURIComponent(jsfpb_token);
   
   jsfpb_QuickJSON('requestjsoncsv','jsfpb_schedulebackup_return',query);
}

function jsfpb_schedulebackup_return(jsondata) {
   alert('Successfully scheduled a backup.  This backup will complete within the next 24 hours.');
}

function jsfpb_getshortdate() {
   var dt = new Date();
   m = (dt.getMonth() + 1).toString().padStart(2, "0");
   d = dt.getDate().toString().padStart(2, "0");
   return dt.getFullYear() + m + d;
}



var jsfpb_pagemappings;
function jsfpb_pagesselection(divid,onchange) {
   jsfpb_pagemappings = {};
   var temparr = {};
   var masterid = {};
   var allnames = [];
   
   //alert('jsfpb_pagesselection values: ' + JSON.stringify(jsfpb_values));
   var str = '';
   str += '<select id=\"' + divid + '\"';
   if(Boolean(onchange)) str += ' onchange=\"' + onchange + '\"';
   str += '>';
   str += '<option value=\"\"> </option>';
   //jsfpb_values = jsondata.rows;
   var usednms = {};
   for  (var i=0;i<jsfpb_values.length;i++){
      if(jsfpb_values[i].name.substr(0,6) == 'Page: ' && jsfpb_values[i].name.indexOf('_jsf')=== -1) {
      	var nm = jsfpb_values[i].name.substr(6);
      	//alert('FOUND ' + jsfpb_values[i].name);
      	if(!Boolean(temparr[nm])) temparr[nm] = [];
      	temparr[nm].push(i.toString());
      	
      	if(!Boolean(usednms[nm])) {
      	   masterid[nm] = i.toString();
      	   allnames.push(nm);
      		str += '<option value=\"' + i + '\">' + nm + '</option>';
      		usednms[nm] = true;
      	}
      }
   }
   str += '</select>';
   
   for(var i=0;i<allnames.length;i++) {
      var nm = allnames[i];
      for(var j=0;j<temparr[nm].length;j++) {
         jsfpb_pagemappings[temparr[nm][j]] = masterid[nm];
      }
   }
   
   return str;
}


function jsfpb_versionselection() {
   var str = '';
   
   //alert('versions: ' + JSON.stringify(jsfpb_page_versions));
   
   str += '<div style=\"font-size:10px;\">';
   for(var i=0;i<jsfpb_page_versions.length;i++) {
      //alert('jsfpb_versionselection: ' + jsfpb_page.version);
      var obj = jsfpb_page_versions[i];
      var bg = '#f1f1f1';
      if(parseInt(jsfpb_page.version) == parseInt(obj.version)) bg = '#BBDDFF';
      
      var fclr = '#000000';
      if(obj.verstatus=='NEW') fclr = '#BB6767';
      else if(obj.verstatus=='INACTIVE') fclr = '#676767';
      
      str += '<div style=\"margin-bottom:2px;padding:3px;background-color:' + bg + ';cursor:pointer;margin-right:20px;\" onclick=\"jsfpb_openpagetool(\'' + obj.version + '\');\">';
      str += '<div style=\"float:left;width:25px;overflow:hidden;margin-right:8px;\">' + obj.version + '</div>';
      str += '<div style=\"float:left;width:60px;overflow:hidden;margin-right:8px;font-weight:bold;color:' + fclr + ';\">' + obj.verstatus + '</div>';
      str += '<div style=\"float:left;width:70px;overflow:hidden;margin-right:8px;\">' + obj.created.substr(0,10) + '</div>';
      str += '<div onclick=\"jsfpb_deletepage(\'' + obj.wd_row_id + '\');\" style=\"float:left;width:18px;overflow:hidden;margin-right:8px;color:red;cursor:pointer;\">x</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
   }
   str += '</div>';
   
   return str;
}



// Get version information
var jsfpb_page_versions;
var jsfpb_page_maxversion;
function jsfpb_loadpageversions(values,name){
   //alert('jsfpb_loadpageversions: bulding jsfpb_page_versions');
   jsfpb_page_versions = [];
   if(!Boolean(name)) name = jsfpb_page.name;
   
   if(name.substr(0,6)!='Page: ') name = 'Page: ' + name;   
   if(!Boolean(values)) values = jsfpb_displayvalues;
   else jsfpb_displayvalues = values;
   
   jsfpb_page_maxversion = 0;
   for(var j=0;j<values.length;j++){
      var nextname = values[j].name;
      if(name==nextname) {
         if(Boolean(values[j].version) && parseInt(values[j].version) > 0) {
            if(parseInt(values[j].version) > parseInt(jsfpb_page_maxversion)) jsfpb_page_maxversion = parseInt(values[j].version);
            var obj ={};
            obj.wd_row_id = values[j].wd_row_id;
            obj.created = values[j].created;
            obj.version = values[j].version;
            obj.verstatus = values[j].verstatus;
            if(Boolean(jsfpb_page) && Boolean(jsfpb_page.version) && jsfpb_page.version == values[j].version) {
               obj.current = true;
            }
            jsfpb_page_versions.push(obj);
         }
		}
   }
   
   //alert('before sort: ' + JSON.stringify(jsfpb_page_versions));
   jsfpb_page_versions.sort(function(a, b){return (parseInt(b.version)-parseInt(a.version))});
   //alert('after sort: ' + JSON.stringify(jsfpb_page_versions));
   
   return jsfpb_page_maxversion;
}



function jsfpb_openpagetool(ver){
   jsfpb_page ={};
   jsfpb_page.rows=[];
   
   var i = jQuery('#sel_pg_name').val();
   if(Boolean(jsfpb_values[i]) && Boolean(jsfpb_values[i].value)){
      
      jsfpb_page = jsfpb_loadpageobject(jsfpb_values,jsfpb_values[i].name,ver);
      jsfpb_loadpageversions(jsfpb_values,jsfpb_values[i].name);
      
      /*
      jsfpb_page = JSON.parse(jsfpb_values[i].value);
      jsfpb_page.name = jsfpb_values[i].name.substr(6);
      jsfpb_page.wd_row_id = jsfpb_values[i].wd_row_id;
      
      //check to see if this page is broken up into several DB entries
      if(Boolean(jsfpb_page.rowcount) && jsfpb_page.rowcount>3) {
         //alert('inside row count - see that there is more than 3');
         var finished = false;
         var wdcounter = 1;
         while(!finished && jsfpb_page.rowcount > jsfpb_page.rows.length){
            finished = true;
            for(var j=0;j<jsfpb_values.length;j++){
               var nextname = jsfpb_values[i].name + '_jsf' + wdcounter;
               //alert('looking at value: ' + jsfpb_values[j].name + ' comparing to: ' + nextname);
               if(jsfpb_values[j].name==nextname) {
                  //alert('found: ' + nextname + ' at index ' + j);
                  finished = false;
                  var temppage = JSON.parse(jsfpb_values[j].value);
                  //alert(JSON.stringify(temppage));
                  jsfpb_page.rows = jsfpb_page.rows.concat(temppage.rows);
                  wdcounter++;
                  break;
               }
            }
         }
      }
      */
   }
   jsfpb_displayPageInput();
}




   window.addEventListener('message', jsfpb_ReceiveMessage, false);
   function jsfpb_ReceiveMessage(e){
      var databack = e.data;
      //alert('***chj*** databack: ' + databack);
      var databack_a = databack.split(',');
      
      var a = databack_a[0];
      var b = databack_a[1];
      var c = databack_a[2];
      var fn = databack_a[3];
      
      var databack_b = a.split('_');
      var r = databack_b[0];
      var s = databack_b[1]
      var l = databack_b[2];
      jsfpb_changeswaiting=1;
      
      if(c=='seoimg') {
         var divid = 'page';
         var img = '<img src=\"' + fn + '\" onclick=\"\" style=\"max-width:120px;max-height:80px;width:auto;height:auto;\">';
         img += '<div onclick=\"if(confirm(\'Are you sure you want to permanently remove this image?\')) { jQuery(\'#' + divid + '_seoimgdiv\').html(\'\'); jsfpb_page.seoimg = \'\'; jsfpb_changeswaiting=1; }\" style=\"position:absolute;top:3px;left:3px;font-weight:bold;color:red;cursor:pointer;font-family:arial;font-size:10px;background-color:#EEEEEE;width:12px;height:12px;overflow:hidden;border-radius:4px;text-align:center;\">x</div>';
         img += '<div onclick=\"window.open(\'' + fn + '\');\" style=\"position:absolute;top:3px;left:18px;font-weight:bold;color:green;cursor:pointer;font-family:arial;font-size:10px;background-color:#EEEEEE;width:12px;height:12px;overflow:hidden;border-radius:4px;text-align:center;\">+</div>';
         jQuery('#' + divid + '_seoimgdiv').html(img);
         jsfpb_page.seoimg = fn;
      } else if(c=='bg') {
         //alert('chad here');
         var divid = 'row_' + r;
         var img = '<img src=\"' + fn + '\" onclick=\"\" style=\"max-width:120px;max-height:80px;width:auto;height:auto;\">';
         img += '<div onclick=\"if(confirm(\'Are you sure you want to permanently remove this image?\')) { jQuery(\'#' + divid + '_imgdiv\').html(\'\'); jQuery(\'#' + divid + '_tilediv\').hide(); jsfpb_page.rows[' + r + '].img = \'\'; jsfpb_changeswaiting=1; }\" style=\"position:absolute;top:3px;left:3px;font-weight:bold;color:red;cursor:pointer;font-family:arial;font-size:10px;background-color:#EEEEEE;width:12px;height:12px;overflow:hidden;border-radius:4px;text-align:center;\">x</div>';
         img += '<div onclick=\"window.open(\'' + fn + '\');\" style=\"position:absolute;top:3px;left:18px;font-weight:bold;color:green;cursor:pointer;font-family:arial;font-size:10px;background-color:#EEEEEE;width:12px;height:12px;overflow:hidden;border-radius:4px;text-align:center;\">+</div>';
         jQuery('#' + divid + '_imgdiv').html(img);
         jQuery('#' + divid + '_tilediv').show();
         jsfpb_page.rows[r].img = fn;
      } else if(c=='img') {
         var divid = r + '_' + s + '_' + l;
         var img = '<img src=\"' + fn + '\" onclick=\"\" style=\"max-width:120px;max-height:80px;width:auto;height:auto;\">';
         img += '<div onclick=\"if(confirm(\'Are you sure you want to permanently remove this image?\')) { jQuery(\'#' + divid + '_imgdiv\').html(\'\'); jsfpb_page.rows[' + r + '].slots[' + s + '].layers[' + l + '].img = \'\'; jsfpb_changeswaiting=1; }\" style=\"position:absolute;top:3px;left:3px;font-weight:bold;color:red;cursor:pointer;font-family:arial;font-size:10px;background-color:#EEEEEE;width:12px;height:12px;overflow:hidden;border-radius:4px;text-align:center;\">x</div>';
         img += '<div onclick=\"window.open(\'' + fn + '\');\" style=\"position:absolute;top:3px;left:18px;font-weight:bold;color:green;cursor:pointer;font-family:arial;font-size:10px;background-color:#EEEEEE;width:12px;height:12px;overflow:hidden;border-radius:4px;text-align:center;\">+</div>';
         jQuery('#' + divid + '_imgdiv').html(img);
         jsfpb_page.rows[r].slots[s].layers[l].img = fn;
      } else if(c=='visualimg') {
         // For visual pages - upload handled here
         var divid = 'jsfv_visualimgthumb';
         var img = jsfpb_displayadminimg(135,80,fn,divid,'jsfv_divs[\'' + b + '\'].img = \'\'; jQuery(\'#jsfv_div_img\').val(\'\'); jsfpb_changeswaiting=1;jsfv_changediv();');
         jQuery('#' + divid).html(img);
         jQuery('#jsfv_div_img').val(fn);
         jsfv_divs[b].img = fn;
         jsfv_changediv();
      } else if(c=='doc') {
         var divid = r + '_' + s + '_' + l;
         var str = '';
         str += '<div onclick=\"window.open(\'' + fn + '\');\" style=\"position:relative;top:3px;font-weight:bold;cursor:pointer;font-family:arial;font-size:10px;\">' + fn + '</div>';
         str += '<div ';
         str += 'onclick=\"if(confirm(\'Are you sure you want to permanently remove this document?\')) { jQuery(\'#' + divid + '_docdiv\').html(\'\'); jsfpb_page.rows[' + r + '].slots[' + s + '].layers[' + l + '].doc = \'\'; jsfpb_changeswaiting=1; }\" ';
         str += 'style=\"position:relative;top:3px;left:3px;cursor:pointer;font-family:arial;font-size:10px;\">delete</div>';
         jQuery('#' + divid + '_docdiv').html(str);
         jsfpb_page.rows[r].slots[s].layers[l].doc = fn;
      }

   }


   
function jsfpb_displayadminimg(wd,ht,fn,divid,removejs){
   if(!Boolean(wd)) wd = 120;
   if(!Boolean(ht)) ht = 80;
   var img = '';
   img += '<div style=\"position:relative;width:' + wd + 'px;height:' + ht + 'px;overflow:hidden;background-color:#f0f0f0;\">';
   img += '<img src=\"' + fn + '\" onclick=\"\" style=\"max-width:135px;max-height:60px;width:auto;height:auto;\">';
   img += '<div onclick=\"if(confirm(\'Are you sure you want to permanently remove this image?\')) { jQuery(\'#' + divid + '\').html(\'\'); ' + removejs + ' }\" style=\"position:absolute;top:3px;left:3px;font-weight:bold;color:red;cursor:pointer;font-family:arial;font-size:10px;background-color:#EEEEEE;width:12px;height:12px;overflow:hidden;border-radius:4px;text-align:center;\">x</div>';
   img += '<div onclick=\"window.open(\'' + fn + '\');\" style=\"position:absolute;top:3px;left:18px;font-weight:bold;color:green;cursor:pointer;font-family:arial;font-size:10px;background-color:#EEEEEE;width:12px;height:12px;overflow:hidden;border-radius:4px;text-align:center;\">+</div>';
   img += '</div>';
   return img;
}

   



var jsfpb_urls = [];
var jsfpb_uris = [];
var jsfpb_urls_i = 0;
function jsfpb_savepagechunks(newname,wd_row_id,newver){
   jsfpb_temp_wdrowid = '';
   //alert('1. jsfpb_savepagechunks: ' + newname);
   jQuery('#jsfpb_loading').show();
   
   var update = false;
   var newversion = 1;
   var currversion = 1;
   if(Boolean(jsfpb_page.version)) currversion = parseInt(jsfpb_page.version);
   
   var newstatus = 'NEW';
   
   if(Boolean(newver) && Boolean(wd_row_id)) {
      if(Boolean(jsfpb_page_maxversion)) newversion = jsfpb_page_maxversion + 1;
      newname = jsfpb_page.name;
   } else if (Boolean(wd_row_id)) {
      // Save existing page as same version
      update = true;
      //jsfpb_temp_wdrowid = wd_row_id;
      if(Boolean(jsfpb_page.verstatus)) newstatus = jsfpb_page.verstatus;
      newversion = currversion;
      
      newname = jsfpb_page.name;
   } else {
      // Make sure new name is valid
      var okwithname = false;
      while(!okwithname) {
         var found = false;
         for  (var i=0;i<jsfpb_values.length;i++){
            if(jsfpb_values[i].name == 'Page: ' + newname) {
               found = true;
               newname = newname + '_COPY';
               break;
            }
         }
         if(!found) okwithname = true;
      }      
   }
   //alert('new saving version: ' + newversion);
   var urls=[];
   var uris=[];
   
   // the new name may be different than the old name
   var temppage = jsfpb_page;
   var name = jsfpb_page.name;

   jsfpb_page.rowcount = jsfpb_page.rows.length;

   //alert('10. jsfpb_savepagechunks: ' + newname);
   
   
   //if(jsfpb_page.rowcount>3) {
   if(jsfpb_page.rowcount>0) {
      temppage = {};
      temppage.wd = jsfpb_page.wd;
      temppage.fsz = jsfpb_page.fsz;
      temppage.ffm = jsfpb_page.ffm;
      temppage.clr = jsfpb_page.clr;
      
      temppage.seotitle = jsfpb_page.seotitle;
      temppage.seodescr = jsfpb_page.seodescr;
      temppage.seoimg = jsfpb_page.seoimg;
      temppage.seohdr = jsfpb_page.seohdr;
      
      temppage.rowcount = jsfpb_page.rows.length;
      temppage.rows = [];
      //temppage.rows.push(jsfpb_page.rows[0]);
   }
   
   var query = '';
   query = jsfpb_domain + jsfpb_servercontroller;
   urls.push(query);
   query = '&action=' + encodeURIComponent('submitwebdata');
   query += '&wd_id=' + jsfpb_wd_id;
   query += '&enabled=Yes';
   query += '&name=' + encodeURIComponent('Page: ' + newname);
   query += '&value=' + encodeURIComponent(JSON.stringify(temppage));
   query += '&version=' + encodeURIComponent(newversion);
   query += '&verstatus=' + encodeURIComponent(newstatus);
   if (update) {
      query += '&wd_row_id=' + encodeURIComponent(wd_row_id);
      query += '&backup=1';
   }
   query += '&ignorenull=1';
   //urls.push(query);
   uris.push(query);
   
   // rowcounter is the row to start with, jsfcounter is the wd row name to start with
   //var rowcounter =1;
   var rowcounter =0;
   
   var jsfcounter = 1;
   var finished = false;
   while (rowcounter<jsfpb_page.rowcount) {
      temppage = {};
      temppage.rows = [];
      if(Boolean(jsfpb_page.rows[rowcounter])) {
         temppage.rows.push(jsfpb_page.rows[rowcounter]);
      }
      rowcounter++;
      
      wd_row_id = '';
      if(update) {
         for(var j=0;j<jsfpb_values.length;j++){
            if(jsfpb_values[j].name == 'Page: ' + name + '_jsf' + jsfcounter) {
               if(!Boolean(jsfpb_values[j].version) || parseInt(jsfpb_values[j].version)==currversion) {
                  wd_row_id = jsfpb_values[j].wd_row_id;
                  break;
               }
            }
         }
      }

      query = jsfpb_domain + jsfpb_servercontroller;
      urls.push(query);
      query = '&action=' + encodeURIComponent('submitwebdata');
      query += '&wd_id=' + jsfpb_wd_id;
      query += '&enabled=Yes';
      query += '&name=' + encodeURIComponent('Page: ' + newname + '_jsf' + jsfcounter);
      query += '&version=' + encodeURIComponent(newversion);
      query += '&verstatus=' + encodeURIComponent(newstatus);
      if (Boolean(wd_row_id)) query += '&wd_row_id=' + encodeURIComponent(wd_row_id);
      query += '&backup=1';
      //urls.push(query);   
      query += '&value=' + encodeURIComponent(JSON.stringify(temppage));      
      query += '&ignorenull=1';
      uris.push(query);   
      jsfcounter++;
   }

   jsfpb_urls = urls;
   jsfpb_uris = uris;
   jsfpb_urls_i = 0;
   jsfpb_savepagechunks_return();
}

function jsfpb_savepagechunks_return(jsondata) {
   //if(Boolean(jsondata)) alert(jsondata.wd_row_id);
   //alert('jsfpb_temp_wdrowid: ' + jsfpb_temp_wdrowid);
   if(Boolean(jsondata) && Boolean(jsondata.wd_row_id) && !Boolean(jsfpb_temp_wdrowid)) jsfpb_temp_wdrowid = jsondata.wd_row_id;
   
   if(jsfpb_urls_i<jsfpb_urls.length) {
      var url = jsfpb_urls[jsfpb_urls_i];
      var uri = jsfpb_uris[jsfpb_urls_i];
      jsfpb_urls_i++;
      
      /*
      jQuery.ajax({
        type: "POST",
        url: url,
        data: uri,
        success: jsfpb_savepagechunks_return
      });      
      */
      
      
      var query = url + '&callback=jsfpb_savepagechunks_return&' + uri;
      jsfpb_CallJSONP(query);
   } else {
      //alert('jsfpb_temp_wdrowid: ' + jsfpb_temp_wdrowid);
      jsfpb_showTool(jsfpb_temp_wdrowid);
      jQuery('#jsfpb_loading').hide();
   }
}
  






















//***chj***
function jsfpb_deletepage(wd_row_id){
   if(confirm('Are you sure you want to delete this page permanently?')){
      jQuery('#jsfpb_loading').show();
      
      var name = '';
      var value = '';
      for(var i=0;i<jsfpb_values.length;i++){
         if(jsfpb_values[i].wd_row_id== wd_row_id) {
            name = jsfpb_values[i].name;
            value = jsfpb_values[i].value;
            version = jsfpb_values[i].version;
            break;
         }
      }      
      
      var urls=[];
      var query = jsfpb_domain + jsfpb_servercontroller;
      query += '&action=' + encodeURIComponent('submitwebdata');
      query += '&callback=' + encodeURIComponent('jsfpb_savepagechunks_return'); 
      query += '&wd_id=' + jsfpb_wd_id;
      query += '&enabled=No';
      query += '&wd_row_id=' + encodeURIComponent(wd_row_id);
      query += '&name=' + encodeURIComponent(name);
      query += '&value=' + encodeURIComponent(value);      
      query += '&ignorenull=1';
      urls.push(query);
      
      var jsfcounter = 1;
      var finished = false;
      while (!finished) {
         finished = true;
         for(var j=0;j<jsfpb_values.length;j++){
            if(!Boolean(version) || !Boolean(jsfpb_values[j].version) || (parseInt(version) == parseInt(jsfpb_values[j].version))) {
               if(jsfpb_values[j].name== name + '_jsf' + jsfcounter) {
                  var query = jsfpb_domain + jsfpb_servercontroller;
                  query += '&action=' + encodeURIComponent('submitwebdata');
                  query += '&callback=' + encodeURIComponent('jsfpb_savepagechunks_return'); 
                  query += '&wd_id=' + jsfpb_wd_id;
                  query += '&enabled=No';
                  query += '&wd_row_id=' + encodeURIComponent(jsfpb_values[j].wd_row_id);
                  query += '&name=' + encodeURIComponent(jsfpb_values[j].name);
                  query += '&value=' + encodeURIComponent(jsfpb_values[j].value);      
                  query += '&ignorenull=1';
                  urls.push(query);
                  jsfcounter++;
                  finished = false;
                  break;
               }
            }
         }
      }
      jsfpb_urls = urls;
      jsfpb_urls_i = 0;
      jsfpb_savepagechunks_return();
   }
}
   

function jsfpb_savepage(name,wd_row_id){
   jQuery('#jsfpb_loading').show();
   
   // NOTE: I've hardcoded the wd_id and question ids into this
   var query = '';
   query += '&wd_id=' + jsfpb_wd_id;
   query += '&enabled=Yes';
   query += '&name=' + encodeURIComponent('Page: ' + name);
   if (Boolean(jsfpb_page)) query += '&value=' + encodeURIComponent(JSON.stringify(jsfpb_page));
   if (Boolean(wd_row_id)) query += '&wd_row_id=' + encodeURIComponent(wd_row_id);
   query += '&userid=' + encodeURIComponent(jsfpb_userid);
   query += '&token=' + encodeURIComponent(jsfpb_token);
   query += '&ignorenull=1';
   
   jsfpb_QuickJSON('submitwebdata','jsfpb_returnsavepage',query);
}

function jsfpb_returnsavepage(jsondata){
   //alert('after save: ' + JSON.stringify(jsondata));
   jsfpb_showTool(jsondata.wd_row_id);
   jQuery('#jsfpb_loading').hide();
}

// Build the admin to the tooling
var jsfpb_savedheight;
var jsfpb_savedwidth;
function jsfpb_showTool(wd_row_id,width,height){
   //alert('jsfpb_showTool: ' + wd_row_id);
   if(!Boolean(width)) width = jsfpb_savedwidth;
   if(!Boolean(height)) height = jsfpb_savedheight;
   if(!Boolean(width)) width = jQuery(window).width();
   if(!Boolean(height)) height = jQuery(window).height();
   
   jsfpb_savedwidth = width;
   jsfpb_savedheight = height;
   
   //alert('pb width: ' + jsfpb_savedwidth + ' height: ' + jsfpb_savedheight);

   var str='';
   str += '<div id=\"jsfpb_outercontainer\" style=\"position:relative;width:' + width + 'px;height:' + height + 'px;overflow:hidden;\">';

   str += '<div id=\"jsfpb_loading\" style=\"position:absolute;z-index:100;width:' + width + 'px;height:' + height + 'px;background-color:#BBBBBB;opacity:0.8;display:none;\">';
   str += '<div style=\"position:relative;\">';
   str += '<div style=\"position:absolute;left:' + (Math.round((width-240)/2)) + 'px;top:' + (Math.round((height-70)/2)) + 'px;width:218px;height:48px;padding:10px;border:1px solid #111111;border-radius:8px;overflow:hidden;font-size:32px;font-weight:bold;font-family:arial;text-align:center;\">';
   str += 'Loading...';
   str += '</div>';
   str += '</div>';
   str += '</div>';
   
   str += '<div id=\"jsfpb_fullpage\" style=\"position:absolute;z-index:99;width:' + width + 'px;height:' + height + 'px;background-color:#FFFFFF;display:none;\">';
   str += '<div style=\"position:relative;width:' + width + 'px;height:' + height + 'px;\">';
   str += '<div id=\"jsfpb_fullpage_inner\" style=\"position:relative;z-index:1;width:' + width + 'px;height:' + height + 'px;overflow:auto;\"></div>';
   str += '<div onclick=\"jQuery(\'#jsfpb_fullpage\').hide();\" style=\"position:absolute;top:5px;right:5px;z-index:10;font-size:28px;cursor:pointer;font-family:arial;color:RED;font-weight:bold;\">x</div>';
   str += '</div>';
   str += '</div>';
   
   height = height - 30;
   
   //alert('width: ' + width + ', height: ' + height);
   var h_wd = Math.floor(width/2) - 2;
   var h_ht = Math.floor(height/2) - 2;

   str += '<div style=\"position:absolute;z-index:1;width:' + width + 'px;height:30px;background-color:#F1F1F1;\">';
   str += '<div style=\"position:relative;left:5px;top:5px;width:' + (width - 10) + 'px;height:20px;overflow:hidden;\" id=\"topadmin\"></div>';
   str += '</div>';
   str += '<div style=\"position:absolute;z-index:1;left:0px;top:30px;width:' + h_wd + 'px;height:' + h_ht + 'px;border:1px solid #EDEDED;font-size:12px;color:#2e2e2e;font-family:verdana;\">';
   str += '<div style=\"position:relative;padding:10px;width:' + (h_wd - 20 - 2) + 'px;height:' + (h_ht - 20 - 2) + 'px;overflow:auto;\" id=\"rowsadmin\">';
   //str += '<div style=\"padding:8px;font-size:20px;font-weight:bold;color:#AA3333;\">Please Read</div>';
   //str += '<div style=\"padding:8px;font-size:20px;font-weight:bold;color:#121212;\">New functionality: Please lock a page before making any changes!</div>';
   //str += '<div style=\"padding:8px;font-size:16px;font-weight:normal;color:#444444;\">You can no longer save changes to a page without locking it first.  But make sure you unlock it when you finish or noone else can edit it until you do.</div>';
   str += '</div>';
   str += '</div>';
   str += '<div style=\"position:absolute;z-index:1;left:' + (h_wd + 2) + 'px;top:30px;width:' + h_wd + 'px;height:' + h_ht + 'px;border:1px solid #EDEDED;font-size:12px;color:#2e2e2e;font-family:verdana;\">';
   str += '<div style=\"position:relative;padding:10px;width:' + (h_wd - 20 - 2) + 'px;height:' + (h_ht - 20 - 2) + 'px;overflow:auto;\" id=\"slotsadmin\"></div>';
   str += '</div>';
   str += '<div style=\"position:absolute;z-index:1;left:0px;top:' + (h_ht + 2 + 30) + 'px;width:' + h_wd + 'px;height:' + h_ht + 'px;border:1px solid #EDEDED;font-size:12px;color:#2e2e2e;font-family:verdana;\">';
   str += '<div style=\"position:relative;padding:10px;width:' + (h_wd - 20 - 2) + 'px;height:' + (h_ht - 20 - 2) + 'px;overflow:auto;\" id=\"layersadmin\"></div>';
   str += '</div>';
   str += '<div style=\"position:absolute;z-index:1;left:' + (h_wd + 2) + 'px;top:' + (h_ht + 2 + 30) + 'px;width:' + h_wd + 'px;height:' + h_ht + 'px;border:1px solid #EDEDED;font-size:12px;color:#2e2e2e;font-family:verdana;\">';
   str += '<div style=\"position:relative;padding:10px;width:' + (h_wd - 20 - 2) + 'px;height:' + (h_ht - 20 - 2) + 'px;overflow:auto;\" id=\"preview\"></div>';
   str += '</div>';

   str += '</div>';
   
   jQuery('#' + jsfpb_admindivid).html(str);
   
   jsfpb_primedata(wd_row_id);
   //jsfpb_displayPageInput();
}

//-----------------------------------------------
// PAGE ADMIN *********************************************
function jsfpb_displayPageInput(){
   var str = '';
   str += jsfpb_displayPageThumbnail();
   
   jQuery('#rowsadmin').html(str);
   jsfpb_checklock();
   
   jQuery('#slotsadmin').html('');
   jQuery('#layersadmin').html('');
   jQuery('#preview').html('');
   
   jQuery('#pageversionblock').hide();
   if(Boolean(jsfpb_page.version) && Boolean(jsfpb_page.verstatus)) {
      jQuery('#pageversionblock').show();
      var ver = jsfpb_page.name + ' version ' + jsfpb_page.version + ' (' + jsfpb_page.verstatus + ')';
      jQuery('#viewpageversion').html(ver);
      
      jQuery('#btn_veractivate').hide();
      jQuery('#btn_verdeactivate').hide();
      if(jsfpb_page.verstatus=='ACTIVE') jQuery('#btn_verdeactivate').show();
      else jQuery('#btn_veractivate').show();
      jQuery('#btn_newversion').show();
      
      //create view of all versions: pageversiontable
      jQuery('#pageversiontable').html(jsfpb_versionselection());
   }
   
   jQuery('#page_name').val(jsfpb_page.name);
   jQuery('#page_wd').val(jsfpb_page.wd);
   jQuery('#page_fsz').val(jsfpb_page.fsz);
   jQuery('#page_ffm').val(jsfpb_page.ffm);
   jQuery('#page_clr').val(jsfpb_page.clr);
   
   if(Boolean(jsfpb_page.clr)) jQuery('#page_clrpre').css('background-color',jsfpb_page.clr);
   
   if(Boolean(jsfpb_page.seotitle)) jQuery('#page_seotitle').val(jsfpb_page.seotitle);
   if(Boolean(jsfpb_page.seodescr)) jQuery('#page_seodescr').val(jsfpb_page.seodescr);
   if(Boolean(jsfpb_page.seoimg)) {
      var divid = 'page';
      var img = '<img src=\"' + jsfpb_page.seoimg + '\" onclick=\"\" style=\"max-width:120px;max-height:80px;width:auto;height:auto;\">';
      img += '<div onclick=\"if(confirm(\'Are you sure you want to permanently remove this image?\')) { jQuery(\'#' + divid + '_seoimgdiv\').html(\'\'); jsfpb_page.seoimg = \'\'; jsfpb_changeswaiting=1; }\" style=\"position:absolute;top:3px;left:3px;font-weight:bold;color:red;cursor:pointer;font-family:arial;font-size:10px;background-color:#EEEEEE;width:12px;height:12px;overflow:hidden;border-radius:4px;text-align:center;\">x</div>';
      img += '<div onclick=\"window.open(\'' + jsfpb_page.seoimg + '\');\" style=\"position:absolute;top:3px;left:18px;font-weight:bold;color:green;cursor:pointer;font-family:arial;font-size:10px;background-color:#EEEEEE;width:12px;height:12px;overflow:hidden;border-radius:4px;text-align:center;\">+</div>';
      jQuery('#' + divid + '_seoimgdiv').html(img);
   }
   if(Boolean(jsfpb_page.seohdr)) jQuery('#page_seohdr').val(jsfpb_page.seohdr);
   
   jsfpb_displayRowInput(0);
   jsfpb_displaySlotInput(0,0);   
}

function jsfpb_displayPageThumbnail(){
   var str = '';
   str += '';
   
   str += '<div style=\"position:relative;\">';
   
   str += '<div style=\"float:left;width:250px;\">';
   
   str += '<div style=\"margin-top:3px;margin-bottom:10px;\">';
   str += '<span id=\"btn_pagesave\" onclick=\"jsfpb_savepagechunks(jQuery(\'#page_name\').val(),jsfpb_page.wd_row_id);\" style=\"padding:4px;border-radius:5px;font-size:10px;color#CCCCCC;border:1px solid #CCCCCC;cursor:pointer;background-color:#FFFFFF;\">Save</span>';
   str += '<span onclick=\"jsfpb_previewHTML();\" style=\"margin-left:15px;padding:4px;border-radius:5px;font-size:10px;color#CCCCCC;border:1px solid #CCCCCC;cursor:pointer;background-color:#FFFFFF;\">Preview</span>';
   str += '</div>';
   

   //show versions **chj
   str += '<div id=\"pageversionblock\" style=\"display:none;\">';
   str += '<div id=\"viewpageversion\" style=\"margin-top:3px;margin-bottom:10px;\">';
   str += '</div>';
   
   //str += jsfpb_togglehtml('Version Information','jsfpb_version_info');   
   //str += '<div id=\"jsfpb_version_info\" style=\"position:relative;display:none;\">';
   str += '<div id=\"jsfpb_version_info\" style=\"position:relative;margin-bottom:3px;\">';
   
   str += '<div id=\"pageversiontable\" style=\"margin-top:3px;margin-bottom:10px;\">';
   str += '</div>';
   
   str += '<div id=\"pageversionactions\" style=\"margin-top:3px;margin-bottom:10px;\">';
   str += '<span id=\"btn_veractivate\" onclick=\"jsfpb_page.verstatus=\'ACTIVE\';jsfpb_savepagechunks(\'\',jsfpb_page.wd_row_id,false);\" style=\"padding:4px;border-radius:5px;font-size:10px;color#CCCCCC;border:1px solid #CCCCCC;cursor:pointer;background-color:#FFFFFF;margin-right:10px;margin-bottom:5px;\">Activate</span>';
   str += '<span id=\"btn_verdeactivate\" onclick=\"jsfpb_page.verstatus=\'INACTIVE\';jsfpb_savepagechunks(\'\',jsfpb_page.wd_row_id,false);\" style=\"padding:4px;border-radius:5px;font-size:10px;color#CCCCCC;border:1px solid #CCCCCC;cursor:pointer;background-color:#FFFFFF;margin-right:10px;margin-bottom:5px;\">Deactivate</span>';
   str += '<span id=\"btn_newversion\" onclick=\"jsfpb_savepagechunks(\'\',jsfpb_page.wd_row_id,true);\" style=\"padding:4px;border-radius:5px;font-size:10px;color#CCCCCC;border:1px solid #CCCCCC;cursor:pointer;background-color:#FFFFFF;margin-right:10px;margin-bottom:5px;\">New Version</span>';
   str += '</div>';
   
   str += '</div>';
   str += '</div>';
   // End showing version information
   
   str += jsfpb_togglehtml('Page Information','jsfpb_page_info');   
   str += '<div id=\"jsfpb_page_info\" style=\"position:relative;display:none;\">';
   
   str += '<div id=\"page_namediv\" style=\"position:relative;\">';
   str += '<div style=\"float:left;width:120px;\">';
   str += 'Page Name';
   str += '</div>';
   str += '<div style=\"float:left;width:110px;\">';
   str += '<input type=\"text\" onkeyup=\"jsfpb_changeswaiting=1;\" id=\"page_name\" style=\"width:100px;font-size:10px;font-family:arial;\">';
   str += '</div>';
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';
   
   str += '<div id=\"page_wddiv\" style=\"position:relative;\">';
   str += '<div style=\"float:left;width:120px;\">';
   str += 'Max Width';
   str += '</div>';
   str += '<div style=\"float:left;width:130px;\">';
   str += '<input onkeyup=\"jsfpb_page.wd=parseInt(jQuery(\'#page_wd\').val());jsfpb_changeswaiting=1;\" type=\"text\" id=\"page_wd\" style=\"width:60px;font-size:10px;font-family:arial;\">';
   str += '</div>';
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';
   
   str += '<div id=\"page_fszdiv\" style=\"position:relative;\">';
   str += '<div style=\"float:left;width:120px;\">';
   str += 'Default Font Sz';
   str += '</div>';
   str += '<div style=\"float:left;width:130px;\">';
   str += '<input onkeyup=\"jsfpb_page.fsz=parseInt(jQuery(\'#page_fsz\').val());jsfpb_changeswaiting=1;\" type=\"text\" id=\"page_fsz\" style=\"width:60px;font-size:10px;font-family:arial;\">';
   str += '</div>';
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';
   
   str += '<div id=\"page_ffmdiv\" style=\"position:relative;\">';
   str += '<div style=\"float:left;width:120px;\">';
   str += 'Default Font';
   str += '</div>';
   str += '<div style=\"float:left;width:130px;\">';
   str += '<input onkeyup=\"jsfpb_page.ffm=jQuery(\'#page_ffm\').val();jsfpb_changeswaiting=1;\" type=\"text\" id=\"page_ffm\" style=\"width:60px;font-size:10px;font-family:arial;\">';
   str += '</div>';
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';
   
   str += '<div id=\"page_clrdiv\" style=\"position:relative;\">';
   str += '<div style=\"float:left;width:120px;\">';
   str += 'Default Font Color';
   str += '</div>';
   str += '<div style=\"float:left;width:70px;\">';
   str += '<input onkeyup=\"jsfpb_page.clr=jQuery(\'#page_clr\').val();jsfpb_changeswaiting=1;\" type=\"text\" id=\"page_clr\" style=\"width:60px;font-size:10px;font-family:arial;\">';
   str += '</div>';
   str += '<div id=\"page_clrpre\" style=\"float:left;width:18px;height:18px;overflow:hidden;\"></div>';   
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';
   
   
   
   str += jsfpb_togglehtml('Page SEO','jsfpb_page_seo');   
   str += '<div id=\"jsfpb_page_seo\" style=\"position:relative;display:none;margin:5px 0px 15px 5px;\">';
   
   str += '<div id=\"page_seotitlediv\" style=\"position:relative;\">';
   str += '<div style=\"float:left;width:110px;\">';
   str += 'Page Title';
   str += '</div>';
   str += '<div style=\"float:left;width:120px;\">';
   str += '<input type=\"text\" onkeyup=\"jsfpb_page.seotitle=jQuery(\'#page_seotitle\').val();jsfpb_changeswaiting=1;\" id=\"page_seotitle\" style=\"width:110px;font-size:10px;font-family:arial;\">';
   str += '</div>';
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';
   
   str += '<div id=\"page_seodescrdiv\" style=\"position:relative;\">';
   str += '<div style=\"float:left;width:110px;\">';
   str += 'Description';
   str += '</div>';
   str += '<div style=\"float:left;width:120px;\">';
   str += '<textarea onkeyup=\"jsfpb_page.seodescr=jQuery(\'#page_seodescr\').val();jsfpb_changeswaiting=1;\" id=\"page_seodescr\" style=\"width:110px;height:40px;font-size:10px;font-family:arial;\"></textarea>';
   str += '</div>';
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';
   
   str += '<div ';
   str += 'id=\"page_seoimgbtndiv\" ';
   str += 'style=\"width:80px;text-align:center;font-size:10px;color:#333333;cursor:pointer;background-color:#F1F1F1;padding:4px;border:1px solid #444444;border-radius:3px;\" ';
   str += 'onclick=\"window.open(\'' + jsfpb_domain + jsfpb_codedir + 'uploadimage.php?userid=9&token=9&prefix=0_0_0&wd_id=seoimg&field_id=0\');\"';
   str += '>Page Image</div>';
   str += '<div id=\"page_seoimgdiv\" style=\"margin-top:5px;position:relative;\"></div>';
   
   str += '<div id=\"page_seohdrdiv\" style=\"position:relative;\">';
   str += '<div style=\"float:left;width:110px;\">';
   str += 'Page Meta Header';
   str += '</div>';
   str += '<div style=\"float:left;width:120px;\">';
   str += '<input type=\"text\" onkeyup=\"jsfpb_page.seohdr=jQuery(\'#page_seohdr\').val();jsfpb_changeswaiting=1;\" id=\"page_seohdr\" style=\"width:110px;font-size:10px;font-family:arial;\">';
   str += '</div>';
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';
   
   str += '</div>';
   
   
   
   str += '<div id=\"page_mergediv\" style=\"position:relative;\">';
   str += '<div style=\"float:left;width:120px;\">';
   str += 'Merge Rows From';
   str += '</div>';
   str += '<div style=\"float:left;\">';
   str += jsfpb_pagesselection('page_merge','jsfpb_mergerows();');
   str += '</div>';
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';

   
   
   str += '<div style=\"margin:10px;padding:10px;\">';
   str += '<span onclick=\"jsfpb_deletepage(jsfpb_page.wd_row_id);\" style=\"cursor:pointer;padding:5px;border-radius:4px;background-color:#DD8888;border: 1px solid #555555;font-family:arial;font-size:12px;color:#303030;\">';
   str += 'Delete Page';
   str += '</span>';
   str += '</div>';
   
   str += '</div>';
   
   str += '</div>';
   
   str += '<div style=\"float:left;width:250px;\">';
   
   str += '<div style=\"position:relative;margin-bottom:8px;margin-top:4px;width:240px;\">';
   for (var i=0;i<jsfpb_page.rows.length;i++){
     str += '<div id=\"bgdiv_' + i + '\" style=\"position:relative;width:230px;height:45px;overflow:hidden;\">';
     str += '<div onclick=\"jsfpb_displayRowInput(' + i + ');\" style=\"float:left;width:60px;height:45px;overflow:hidden;border-right:1px solid #BBBBBB;\">';
     str += '<div>';
     var templbl = 'Row ' + (i+1);
     if(Boolean(jsfpb_page.rows[i].lbl)) templbl = jsfpb_page.rows[i].lbl;
     str += '<span style=\"font-size:8px;font-family:arial;margin-left:5px;margin-top:1px;cursor:pointer;\">' + templbl + '</span>';
     str += '<span onclick=\"if(confirm(\'Are you sure you want to permanently remove this row and all of its content?\')) jsfpb_removeRow(' + i + ');\" style=\"cursor:pointer;margin-left:4px;margin-right:4px;font-size:8px;color:RED;\">x</span>';
     str += '</div>';
     str += '<div>';
     str += '<span onclick=\"if(confirm(\'Are you sure you want to copy this row and all of its content?\')) jsfpb_copyRow(' + i + ');\" style=\"cursor:pointer;margin-left:4px;margin-right:4px;font-size:8px;color:blue;\">copy</span>';
     str += '</div>';
     str += '<div style=\"border-bottom:1px solid #F4F4F4;\">';
     if(i>0) str += '<span onclick=\"jsfpb_moveRow(' + i + ',-1);\" style=\"font-size:8px;font-family:arial;margin-left:5px;margin-top:1px;cursor:pointer;\">Up</span>';
     if(i<(jsfpb_page.rows.length - 1)) str += '<span onclick=\"jsfpb_moveRow(' + i + ',1);\" style=\"font-size:8px;font-family:arial;margin-left:5px;margin-top:1px;cursor:pointer;\">Down</span>';
     str += '</div>';
     str += '</div>';
     for(var j=0;j<jsfpb_page.rows[i].slots.length;j++) {
        var wdpct = jsfpb_page.rows[i].slots[j].wd;
        if(jsfpb_page.rows[i].type.toLowerCase().substr(0,8)=='carousel') wdpct = 100;
        var css = 'height:44px;border-bottom:1px solid #BBBBBB;';
        if(i==0) css = 'height:43px;border-bottom:1px solid #BBBBBB;border-top:1px solid #BBBBBB;';
        str += '<div onclick=\"jsfpb_displayRowInput(' + i + ');jsfpb_displaySlotInput(' + i + ',' + j + ');\" style=\"float:left;width:' + Math.floor(1.6 * parseFloat(wdpct) - 1.0) + 'px;overflow:hidden;border-right:1px solid #BBBBBB;' + css + '\">';
        str += '<span style=\"font-size:10px;font-family:arial;margin-left:5px;margin-top:3px;\">' + wdpct + '%</span>';
        str += '</div>';
        if(jsfpb_page.rows[i].type.toLowerCase().substr(0,8)=='carousel') break;
     }
     str += '<div style=\"clear:both;\"></div>';
     str += '</div>';
   }
   str += '</div>';
   
   str += '<div style=\"position:relative;\">';
   str += '<div onclick=\"jsfpb_addNewRow(parseInt(jQuery(\'#jsfpb_newrowcols\').val()));\" style=\"float:left;margin-right:10px;width:70px;overflow:hidden;text-align:center;font-size:10px;color:#444444;border:1px solid #CCCCCC;padding:5px;border-radius:4px;background-color:#EEEEFF;cursor:pointer;\">Add Row</div>';
   str += '<div style=\"float:left;\">';
   str += '<select id=\"jsfpb_newrowcols\" style=\"margin-top:3px;\">';
   str += '<option value=\"1\">Single</option>';
   str += '<option value=\"2\">2 Columns</option>';
   str += '<option value=\"3\">3 Columns</option>';
   str += '<option value=\"4\">4 Columns</option>';
   str += '</select>';
   str += '</div>';
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';
   
   str += '</div>';
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';
   
   return str;
}

function jsfpb_mergerows() {
   if(confirm('Are you sure you want to merge rows from the selected page with your current rows?')) {
      var i = jQuery('#page_merge').val();
      var temppage = jsfpb_loadpageobject('',jsfpb_values[i].name);
      jsfpb_page.rows = jsfpb_page.rows.concat(temppage.rows);
   }
   
   jQuery('#page_merge').val('');
   jsfpb_displayPageInput();   
}

function jsfpb_addNewRow(cols) {
   jsfpb_changeswaiting=1;
   
   if(!Boolean(cols)) cols = 1;
   
   var temp = {};
   temp.type = 'sequential';
   temp.pad = 0;
   temp.slots = [];
   
   for(var i=0; i<cols; i++) {
      var temp2 = {};
      temp2.wd = Math.floor(100/cols);
      temp2.layers = [];
      
      var temp3 = {};
      temp3.type = 'Text';
      temp2.layers.push(temp3);
   
      temp.slots.push(temp2);
   }
   jsfpb_page.rows.push(temp);
   jsfpb_displayPageInput();
}

function jsfpb_removeRow(i){
   jsfpb_changeswaiting=1;
   jsfpb_page.rows.splice(i,1);
   jsfpb_displayPageInput();
}

function jsfpb_moveRow(i,direction) {
   jsfpb_changeswaiting=1;
   var temp = jsfpb_page.rows[i + direction];
   jsfpb_page.rows[i + direction] = jsfpb_page.rows[i];
   jsfpb_page.rows[i] = temp;
   
   jsfpb_displayPageInput();
}

function jsfpb_copyRow(i) {
   var copy = JSON.parse(JSON.stringify(jsfpb_page.rows[i]));
   copy.lbl = '';
   jsfpb_page.rows.push(copy);
   jsfpb_displayPageInput();
}




// ROWS -----------------
function jsfpb_displayRowInput(r){
   if(r<jsfpb_page.rows.length) {
      //alert('row: ' + JSON.stringify(jsfpb_page.rows[r]));
      var divid = 'row_' + r;
      
      // highlight the correct row
      for (var i=0;i<jsfpb_page.rows.length;i++){
        jQuery('#bgdiv_' + i).css('background-color','white');
        if(Boolean(jsfpb_page.rows[i].disable)) jQuery('#bgdiv_' + i).css('background-color','#FFF0F0');
      }
      jQuery('#bgdiv_' + r).css('background-color','#EDEDED');
      if(Boolean(jsfpb_page.rows[r].disable)) jQuery('#bgdiv_' + r).css('background-color','#EDD1D1');

      // display a row with slots in detail
      var str = '';
      
      str += '<div id=\"' + divid + '_lbldiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Label';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input type=\"text\" onkeyup=\"jsfpb_changeRow(' + r + ');\" id=\"' + divid + '_lbl\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
   
      
      str += jsfpb_togglehtml('Row Information','jsfpb_row_info');   
      str += '<div id=\"jsfpb_row_info\" style=\"position:relative;display:none;\">';

      str += '<div id=\"' + divid + '_extdiv\" style=\"position:relative;\">';
      str += '<div style=\"width:220px;margin-top:5px;margin-bottom:5px;\">';
      str += '<input type=\"checkbox\" value=\"1\" onchange=\"jsfpb_changeRow(' + r + ');\" id=\"' + divid + '_ext\">';
      str += ' Extend row background to full width';
      str += '</div>';
      str += '</div>';
      
      
      str += '<div id=\"' + divid + '_paddiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Pixels Buffer';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input type=\"text\" onkeyup=\"jsfpb_changeRow(' + r + ');\" id=\"' + divid + '_pad\" style=\"width:90px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
   
      str += '<div id=\"' + divid + '_typediv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Display';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<select onchange=\"jsfpb_changeRow(' + r + ');\" id=\"' + divid + '_type\"><option value=\"sequential\">Sequentially</option><option value=\"carousel\">Carousel</option><option value=\"carousel2\">Carousel 2</option></select>';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
   
      str += '<div id=\"' + divid + '_txtdiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Navigation Title';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input type=\"text\" onkeyup=\"jsfpb_changeRow(' + r + ');\" id=\"' + divid + '_txt\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
   
      str += '<div id=\"' + divid + '_htdiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Height';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<select onchange=\"jsfpb_changeRow(' + r + ');\" id=\"' + divid + '_htty\">';
      str += '<option value=\"NA\">None</option>';
      str += '<option value=\"px\">Pixels</option>';
      str += '<option value=\"pct\">Percent</option>';
      str += '</select> ';      
      str += '<input type=\"text\" onkeyup=\"jsfpb_changeRow(' + r + ');\" id=\"' + divid + '_ht\" style=\"display:none;width:50px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
      str += '<div id=\"' + divid + '_bgdiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Row Background Color';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input type=\"text\" onkeyup=\"jsfpb_changeRow(' + r + ');\" id=\"' + divid + '_bg\" style=\"width:90px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div id=\"' + divid + '_bgpre\" style=\"float:left;width:18px;height:18px;overflow:hidden;\"></div>';   
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
      // ***chj***
      //new background
      str += '<div ';
      str += 'id=\"' + divid + '_imgbtndiv\" ';
      str += 'style=\"width:140px;text-align:center;font-size:10px;color:#333333;cursor:pointer;background-color:#F1F1F1;padding:4px;border:1px solid #444444;border-radius:3px;\" ';
      str += 'onclick=\"window.open(\'' + jsfpb_domain + jsfpb_codedir + 'uploadimage.php?userid=9&token=9&prefix=' + r + '&wd_id=bg&field_id=0\');\"';
      str += '>Select Row Background</div>';
      str += '<div id=\"' + divid + '_imgdiv\" style=\"margin-top:5px;position:relative;\"></div>';

      str += '<div id=\"' + divid + '_tilediv\" style=\"position:relative;display:none;\">';
      str += '<div style=\"width:220px;margin-top:5px;margin-bottom:5px;\">';
      str += '<input type=\"checkbox\" value=\"1\" onchange=\"jsfpb_changeRow(' + r + ');\" id=\"' + divid + '_tile\">';
      str += ' Tile background';
      str += '</div>';
      str += '<div style=\"width:220px;margin-top:5px;margin-bottom:5px;\">';
      str += '<input type=\"checkbox\" value=\"1\" onchange=\"jsfpb_changeRow(' + r + ');\" id=\"' + divid + '_ancimg\">';
      str += ' Anchor from bottom';
      str += '</div>';
      str += '</div>';
      
      
      
      str += '</div>';

      
      
      
      
      
      
      var disablebg = '#FFFFFF';
      //if(Boolean(jsfpb_page.rows[r].disable)) disablebg = '#FFDEDE';
      var titleclr = '#000000';
      if(Boolean(jsfpb_page.rows[r].disable)) titleclr = '#BB4444';
      
      str += '<div style=\"margin-top:6px;font-size:14px;font-weight:bold;font-family:arial;color:' + titleclr + ';\">';
      str += 'Row ' + (r+1);
      if(Boolean(jsfpb_page.rows[r].disable)) str += ' (Disabled)'
      str += '</div>';
      str += '<div style=\"position:relative;width:410px;height:100px;overflow:hidden;border-left:1px solid #BBBBBB;border-bottom:1px solid #BBBBBB;border-top:1px solid #BBBBBB;margin-bottom:8px;background-color:' + disablebg + ';\">';
      for(var i=0;i<jsfpb_page.rows[r].slots.length;i++) {
         var wdpct = jsfpb_page.rows[r].slots[i].wd;
         var disp = '<br>' + jsfpb_page.rows[r].slots[i].wd + '%';
         if(jsfpb_page.rows[r].type.toLowerCase().substr(0,8)=='carousel') {
            wdpct = 20;
            disp = '<br>carousel #' + (i + 1);
         }
         str += '<div id=\"bgdiv_' + r + '_' + i + '\" onclick=\"jsfpb_displaySlotInput(' + r + ',' + i + ');\" style=\"float:left;width:' + Math.floor(4.0 * parseFloat(wdpct) - 1.0) + 'px;height:100px;overflow:hidden;border-right:1px solid #BBBBBB;\">';
         str += '<div style=\"font-size:10px;font-family:arial;margin-left:5px;margin-top:3px;\">';
         str += 'Slot ' + (i+1) + '<span onclick=\"if(confirm(\'Are you sure you want to permanently remove this slot and all of its content?\')) jsfpb_removeSlot(' + r + ',' + i + ');\" style=\"cursor:pointer;margin-left:4px;margin-right:4px;font-size:8px;color:RED;\">x</span>';
         str += disp;
         str += '<div>';
         if(i>0) str += '<span onclick=\"jsfpb_moveSlot(' + r + ',' + i + ',-1);\" style=\"font-size:8px;color:#4444AA;cursor:pointer;margin-right:10px;\">Left</span>';
         if(i<(jsfpb_page.rows[r].slots.length - 1)) str += '<span onclick=\"jsfpb_moveSlot(' + r + ',' + i + ',1);\" style=\"font-size:8px;color:#4444AA;cursor:pointer;margin-right:10px;\">Right</span>';
         str += '</div>';
         str += '</div>';
         str += '</div>';
      }
      str += '<div style=\"clear:both;\"></div>';
      
      var enable_xtra = 'display:none;';
      var disable_xtra = '';
      if(Boolean(jsfpb_page.rows[r].disable)) {
         enable_xtra = '';
         disable_xtra = 'display:none;';         
      }
      str += '</div>';
      
      str += '<div id=\"rowenable' + r + '\" onclick=\"jsfpb_enablerow(' + r + ');\" style=\"cursor:pointer;margin:5px;' + enable_xtra + '\">Enable Row</div>';
      str += '<div id=\"rowdisable' + r + '\" onclick=\"jsfpb_disablerow(' + r + ');\" style=\"cursor:pointer;margin:5px;' + disable_xtra + '\">Disable Row</div>';
      
      if(jsfpb_getRemainingPCT(r)<95.1 || (jsfpb_page.rows[r].type.toLowerCase().substr(0,8)=='carousel' && jsfpb_page.rows[r].slots.length<5)) str += '<div onclick=\"jsfpb_addNewSlot(' + r + ');\" style=\"width:80px;overflow:hidden;text-align:center;font-size:10px;color:#444444;border:1px solid #CCCCCC;padding:5px;border-radius:4px;background-color:#EEEEFF;cursor:pointer;\">Add Slot</div>';
      jQuery('#slotsadmin').html(str);
      
      var templbl = 'Row ' + (r+1);
      if(Boolean(jsfpb_page.rows[r].lbl)) templbl = jsfpb_page.rows[r].lbl;
      jQuery('#' + divid + '_lbl').val(templbl);
      
      jQuery('#' + divid + '_pad').val(jsfpb_page.rows[r].pad);
      jQuery('#' + divid + '_type').val(jsfpb_page.rows[r].type);
      jQuery('#' + divid + '_txt').val(jsfpb_page.rows[r].txt);
      if (Boolean(jsfpb_page.rows[r].ext)) document.getElementById(divid + '_ext').checked = true;
      else document.getElementById(divid + '_ext').checked = false;
      
      
      if (Boolean(jsfpb_page.rows[r].tile)) document.getElementById(divid + '_tile').checked = true;
      else document.getElementById(divid + '_tile').checked = false;
      
      if (Boolean(jsfpb_page.rows[r].ancimg)) document.getElementById(divid + '_ancimg').checked = true;
      else document.getElementById(divid + '_ancimg').checked = false;
      
      if (Boolean(jsfpb_page.rows[r].bg)) {
         //alert('chaddddd here');
         jQuery('#' + divid + '_bg').val(jsfpb_page.rows[r].bg);
         //jQuery('#' + divid + '_tilediv').show();
         jQuery('#' + divid + '_bgpre').css('background-color',jsfpb_page.rows[r].bg);
      }
      
      if(jsfpb_page.rows[r].type=='sequential') jQuery('#' + divid + '_txtdiv').hide();
      
      if(!Boolean(jsfpb_page.rows[r].htty)) jsfpb_page.rows[r].htty='px';
      jQuery('#' + divid + '_htty').val(jsfpb_page.rows[r].htty);
      if(jsfpb_page.rows[r].htty!='NA') jQuery('#' + divid + '_ht').show();
      if(Boolean(jsfpb_page.rows[r].ht)) jQuery('#' + divid + '_ht').val(jsfpb_page.rows[r].ht);
      else jQuery('#' + divid + '_ht').val('');
      
      if (Boolean(jsfpb_page.rows[r].img)) {
         var img = '<img src=\"' + jsfpb_page.rows[r].img + '\" style=\"max-width:120px;max-height:80px;width:auto;height:auto;\">';
         img += '<div onclick=\"if(confirm(\'Are you sure you want to permanently remove this image?\')) { jQuery(\'#' + divid + '_imgdiv\').html(\'\'); jQuery(\'#' + divid + '_tilediv\').hide(); jsfpb_page.rows[' + r + '].img = \'\'; jsfpb_changeswaiting=1; }\" style=\"position:absolute;top:3px;left:3px;font-weight:bold;color:red;cursor:pointer;font-family:arial;font-size:10px;background-color:#EEEEEE;width:12px;height:12px;overflow:hidden;border-radius:4px;text-align:center;\">x</div>';
         img += '<div onclick=\"window.open(\'' + jsfpb_page.rows[r].img + '\');\" style=\"position:absolute;top:3px;left:18px;font-weight:bold;color:green;cursor:pointer;font-family:arial;font-size:10px;background-color:#EEEEEE;width:12px;height:12px;overflow:hidden;border-radius:4px;text-align:center;\">+</div>';
         jQuery('#' + divid + '_imgdiv').html(img);
         jQuery('#' + divid + '_tilediv').show();
      }
      
      
      jQuery('#layersadmin').html('');
      jQuery('#preview').html('');
   }
   
}

function jsfpb_enablerow(r) {
   jsfpb_page.rows[r].disable = 0;
   jsfpb_changeswaiting=1;
   //jQuery('#rowenable' + r).hide();
   //jQuery('#rowdisable' + r).show();
   jsfpb_displayPageInput();
   jsfpb_displayRowInput(r);
}

function jsfpb_disablerow(r) {
   jsfpb_page.rows[r].disable = 1;
   jsfpb_changeswaiting=1;
   //jQuery('#rowdisable' + r).hide();
   //jQuery('#rowenable' + r).show();
   jsfpb_displayPageInput();
   jsfpb_displayRowInput(r);
}

function jsfpb_changeRow(r) {
   var divid = 'row_' + r;
   var temp = jsfpb_page.rows[r].type;
   jsfpb_page.rows[r].type = jQuery('#' + divid + '_type').val();
   if(jsfpb_page.rows[r].type=='sequential') jQuery('#' + divid + '_txtdiv').hide();
   else jQuery('#' + divid + '_txtdiv').show();
   
   jsfpb_page.rows[r].bg = jQuery('#' + divid + '_bg').val();
   if (Boolean(jsfpb_page.rows[r].bg)) {
      jQuery('#' + divid + '_bgpre').css('background-color',jsfpb_page.rows[r].bg);
   }
   
   jsfpb_page.rows[r].ext = document.getElementById(divid + '_ext').checked;   
   jsfpb_page.rows[r].tile = document.getElementById(divid + '_tile').checked;   
   jsfpb_page.rows[r].ancimg = document.getElementById(divid + '_ancimg').checked;   
   
   jsfpb_page.rows[r].txt = jQuery('#' + divid + '_txt').val();
   jsfpb_page.rows[r].lbl = jQuery('#' + divid + '_lbl').val();
   
   jsfpb_page.rows[r].htty = jQuery('#' + divid + '_htty').val();
   if(Boolean(jsfpb_page.rows[r].htty) && jsfpb_page.rows[r].htty=='NA') jQuery('#' + divid + '_ht').hide();
   else jQuery('#' + divid + '_ht').show();
   
   if(Boolean(jQuery('#' + divid + '_ht').val()) && jQuery('#' + divid + '_ht').val().toLowerCase()=='%%%height%%%') {
      jsfpb_page.rows[r].ht = '%%%HEIGHT%%%';
   } else if(Boolean(jQuery('#' + divid + '_ht').val())) {
      jsfpb_page.rows[r].ht = parseInt(jQuery('#' + divid + '_ht').val());
   } else {
      jsfpb_page.rows[r].ht = 0;
   }
   
   if(Boolean(jQuery('#' + divid + '_pad').val())) jsfpb_page.rows[r].pad = parseInt(jQuery('#' + divid + '_pad').val());
   else jsfpb_page.rows[r].pad = 0;
         
   jsfpb_changeswaiting=1;
   //jsfpb_displayPageInput();
   //jsfpb_displayRowInput(r);
}

function jsfpb_moveSlot(r,s,direction) {
   jsfpb_changeswaiting=1;
   var temp = jsfpb_page.rows[r].slots[s + direction];
   jsfpb_page.rows[r].slots[s + direction] = jsfpb_page.rows[r].slots[s];
   jsfpb_page.rows[r].slots[s] = temp;
   
   jsfpb_displayPageInput();
   jsfpb_displayRowInput(r);
}


function jsfpb_getRemainingPCT(r) {
   var total = 0.0;
   for(var i=0;i<jsfpb_page.rows[r].slots.length;i++) {
      total += parseFloat(jsfpb_page.rows[r].slots[i].wd);
   }
   return total;
}

function jsfpb_removeSlot(r,s) {
   jsfpb_changeswaiting=1;
   jsfpb_page.rows[r].slots.splice(s,1);
   jsfpb_displayRowInput(r);
}

function jsfpb_addNewSlot(r) {
   jsfpb_changeswaiting=1;
   var temp2 = {};
   var x = 100.0 - jsfpb_getRemainingPCT(r);
   if(x<34.01 && x>33.0) x=33.3;
   //if(x<5) x = 20;
   
   if(jsfpb_page.rows[r].type.toLowerCase().substr(0,8)=='carousel') x = 20.0;
   
   temp2.wd = Math.floor(x);
   temp2.layers = [];
   var temp3 = {};
   temp3.type = 'Text';
   temp2.layers.push(temp3);
   jsfpb_page.rows[r].slots.push(temp2);
   jsfpb_displayPageInput();
   jsfpb_displayRowInput(r);
}





// SLOTS ----------------
function jsfpb_displaySlotInput(r,s){
   var divid = r + '_' + s;
   
   if(Boolean(jsfpb_page) && Boolean(jsfpb_page.rows) && Boolean(jsfpb_page.rows[r])) { 
      for (var i=0;i<jsfpb_page.rows[r].slots.length;i++) {
         jQuery('#bgdiv_' + r + '_' + i).css('background-color','white');
      }
      jQuery('#bgdiv_' + r + '_' + s).css('background-color','#EDEDED');
      
      
   
      var str = '';
      var addlcss = '';
      if(jsfpb_page.rows[r].type.toLowerCase().substr(0,8)=='carousel') addlcss = 'display:none;';
      str += '<div style=\"margin-bottom:5px;font-size:14px;font-weight:bold;font-family:arial;\">Row ' + (r+1) + ' Slot ' + (s+1) + '</div>';
      
      str += '<div id=\"' + divid + '_wddiv\" style=\"position:relative;' + addlcss + '\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Slot Width';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<select onchange=\"jsfpb_changeSlot(' + r + ',' + s + ');\" id=\"' + divid + '_wd\">';
      str += '<option value=\"100\">100%</option>';
      str += '<option value=\"91.666\">11 slots (of 12)</option>';
      str += '<option value=\"83.333\">10 slots (of 12)</option>';
      str += '<option value=\"80\">80%</option>';
      str += '<option value=\"75\">75% (9 of 12)</option>';
      str += '<option value=\"70\">70%</option>';
      str += '<option value=\"67\">67%</option>';
      str += '<option value=\"66.666\">8 slots (of 12)</option>';
      str += '<option value=\"65\">65%</option>';
      str += '<option value=\"60\">60%</option>';
      str += '<option value=\"58.333\">7 slots (of 12)</option>';
      str += '<option value=\"55\">55%</option>';
      str += '<option value=\"50\">50% (6 of 12)</option>';
      str += '<option value=\"45\">45%</option>';
      str += '<option value=\"41.666\">5 slots (of 12)</option>';
      str += '<option value=\"40\">40%</option>';
      str += '<option value=\"35\">35%</option>';
      str += '<option value=\"33.333\">4 slots (of 12)</option>';
      str += '<option value=\"33\">33% (4 of 12)</option>';
      str += '<option value=\"30\">30%</option>';
      str += '<option value=\"25\">25% (3 of 12)</option>';
      str += '<option value=\"23\">23%</option>';
      str += '<option value=\"22.5\">22.5%</option>';
      str += '<option value=\"20\">20%</option>';
      str += '<option value=\"16.666\">2 slots (of 12)</option>';
      str += '<option value=\"15\">15%</option>';
      str += '<option value=\"12\">12%</option>';
      str += '<option value=\"10\">10%</option>';
      str += '<option value=\"8.333\">1 slot (of 12)</option>';
      str += '<option value=\"5\">5%</option>';
      str += '</select>';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
   
      str += '<div id=\"' + divid + '_typediv\" style=\"position:relative;' + addlcss + '\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Slot Visibility';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<select onchange=\"jsfpb_changeSlot(' + r + ',' + s + ');\" id=\"' + divid + '_type\"><option value=\"browsertabletmobile\">All</option><option value=\"browser\">Browser Only</option><option value=\"mobile\">Mobile Only</option><option value=\"browsertablet\">Browser and Tablet</option><option value=\"tabletmobile\">Tablet and Mobile</option></select>';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
   
      str += '<div id=\"' + divid + '_layersdiv\" style=\"position:relative;margin-top:6px;margin-bottom:8px;width:250px;overflow:hidden;\">';
      for (var i=0;i<jsfpb_page.rows[r].slots[s].layers.length;i++) {
         str += '<div id=\"bgdiv_' + r + '_' + s + '_' + i + '\" onclick=\"jsfpb_displayLayerInput(' + r + ',' + s + ',' + i + ');\" style=\"position:relative;padding:3px;border:1px solid #CCCCCC;\">';
         str += '<span onclick=\"if(confirm(\'Are you sure you would like to permanently delete this layer?\')) jsfpb_removeLayer(' + r + ',' + s + ',' + i + ');\" style=\"font-size:8px;color:red;margin-left:2px;margin-right:3px;cursor:pointer;\">x</span>';
         str += '<span style=\"font-size:8px;font-family:arial;\">';
         str += 'Layer ' + (i+1);
         if(jsfpb_page.rows[r].slots[s].layers[i].type=='Text' && Boolean(jsfpb_page.rows[r].slots[s].layers[i].content)) str += ': ' + jsfpb_page.rows[r].slots[s].layers[i].content.substr(0,10);
         //else if(jsfpb_page.rows[r].slots[s].layers[i].type=='HTML' && Boolean(jsfpb_page.rows[r].slots[s].layers[i].content)) str += ' HTML: ' + jsfpb_page.rows[r].slots[s].layers[i].content.substr(0,10);
         else if(jsfpb_page.rows[r].slots[s].layers[i].type=='HTML' && Boolean(jsfpb_page.rows[r].slots[s].layers[i].content)) str += ' HTML';
         else str += ': ' + jsfpb_page.rows[r].slots[s].layers[i].type;
         str += '</span>';
         
         if(i>0) str += '<span onclick=\"jsfpb_moveLayer(' + r + ',' + s + ',' + i + ',-1);\" style=\"margin-left:15px;font-size:8px;font-family:arial;margin-top:3px;color:blue;cursor:pointer;\">Up</span>';
         if(i<(jsfpb_page.rows[r].slots[s].layers.length - 1)) str += '<span onclick=\"jsfpb_moveLayer(' + r + ',' + s + ',' + i + ',1);\" style=\"margin-left:15px;font-size:8px;font-family:arial;margin-top:3px;color:blue;cursor:pointer;\">Down</span>';
         
         if(Boolean(jsfpb_page.rows[r].slots[s].layers[i].hide)) {
            str += '<span style=\"margin-left:10px;font-size:8px;color:BLUE;font-style:italic;\">*Hidden</span>';
         }
         
         str += '</div>';
      }
      str += '</div>';
      str += '<div onclick=\"jsfpb_addNewLayer(' + r + ',' + s + ');\" style=\"width:80px;overflow:hidden;text-align:center;font-size:10px;color:#444444;border:1px solid #CCCCCC;padding:5px;border-radius:4px;background-color:#EEEEFF;cursor:pointer;\">Add Layer</div>';
      
      
      jQuery('#layersadmin').html(str);
      jQuery('#preview').html('');
      jsfpb_populateSlot(r,s);
      jsfpb_displayLayerInput(r,s,0);
   }
}

function jsfpb_addNewLayer(r,s){
   jsfpb_changeswaiting=1;
   var temp3 = {};
   temp3.type = 'Text';
   jsfpb_page.rows[r].slots[s].layers.push(temp3);
   jsfpb_displaySlotInput(r,s);
}

function jsfpb_removeLayer(r,s,l) {
   jsfpb_changeswaiting=1;
   jsfpb_page.rows[r].slots[s].layers.splice(l,1);
   jsfpb_displaySlotInput(r,s);
}

function jsfpb_populateSlot(r,s) {
   var divid = r + '_' + s;
   var slot = jsfpb_page.rows[r].slots[s];
   if (Boolean(slot.wd)) jQuery('#' + divid + '_wd').val(slot.wd);
   if (Boolean(slot.type)) jQuery('#' + divid + '_type').val(slot.type);
}

function jsfpb_changeSlot(r,s) {
   jsfpb_changeswaiting=1;
   var divid = r + '_' + s;
   var val = jQuery('#' + divid + '_wd').val();
   jsfpb_page.rows[r].slots[s].wd = val;
   
   val = jQuery('#' + divid + '_type').val();
   jsfpb_page.rows[r].slots[s].type = val;
   
   jsfpb_displayPageInput();
   jsfpb_displayRowInput(r);
   jsfpb_displaySlotInput(r,s);
   

}

function jsfpb_moveLayer(r,s,i,direction) {
   jsfpb_changeswaiting=1;
   var temp = jsfpb_page.rows[r].slots[s].layers[i + direction];
   jsfpb_page.rows[r].slots[s].layers[i + direction] = jsfpb_page.rows[r].slots[s].layers[i];
   jsfpb_page.rows[r].slots[s].layers[i] = temp;
   
   jsfpb_displayPageInput();
   jsfpb_displayRowInput(r);
   jsfpb_displaySlotInput(r,s);
}





// ****chj*** New widgets here!!!
// LAYERS ---------------

function jsfpb_displayLayerInput(r,s,l){
   var divid = r + '_' + s + '_' + l;
   
   for(var i=0;i<jsfpb_page.rows[r].slots[s].layers.length;i++){
      jQuery('#bgdiv_' + r + '_' + s + '_' + i).css('background-color','#FFFFFF');
   }
   jQuery('#bgdiv_' + r + '_' + s + '_' + l).css('background-color','#EDEDED');
   
   var str = '';

   str += '<div id=\"' + divid + '_typediv\" style=\"position:relative;\">';
   str += '<div style=\"float:left;width:180px;\">';
   str += 'Type';
   str += '</div>';
   str += '<div style=\"float:left;width:220px;\">';
   str += '<select onchange=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ',true);\" id=\"' + divid + '_type\">';
   str += '<option value=\"Text\">Text</option>';
   str += '<option value=\"HTML\">HTML</option>';
   str += '<option value=\"Image\">Image</option>';
   str += '<option value=\"Content\">Content</option>';
   str += '<option value=\"Page Import\">Page Import</option>';
   str += '<option value=\"Formatted Block\">Formatted Block</option>';
   str += '<option value=\"Article Block\">Article Block</option>';
   str += '<option value=\"Download Block\">Download Block</option>';
   str += '<option value=\"Visual Builder\">Visual Builder</option>';
   
   //alert('custom types: ' + JSON.stringify(jsfpb_customtypes));

   if(Boolean(jsfpb_customtypes) && jsfpb_customtypes.length>0) {
      //alert('custom types: ' + jsfpb_customtypes.length);
      for(var i=0;i<jsfpb_customtypes.length;i++) {
         str += '<option value=\"' + jsfpb_customtypes[i] + '\">' + jsfpb_customtypes[i] + '</option>';         
      }
   }
   str += '</select>';
   str += '</div>';
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';

   
   
   
   str += jsfpb_togglehtml('Positioning','position');   
   str += '<div id=\"position\" style=\"display:none;\">';
   str += '<div id=\"' + divid + '_wddiv\" style=\"position:relative;\">';
   str += '<div style=\"float:left;width:180px;\">';
   str += 'Width';
   str += '</div>';
   str += '<div style=\"float:left;width:220px;\">';
   str += '<select onchange=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" id=\"' + divid + '_wd\">';
   str += '<option value=\"100\">100%</option>';
   str += '<option value=\"80\">80%</option>';
   str += '<option value=\"75\">75%</option>';
   str += '<option value=\"60\">60%</option>';
   str += '<option value=\"50\">50%</option>';
   str += '<option value=\"40\">40%</option>';
   str += '<option value=\"25\">25%</option>';
   str += '<option value=\"20\">20%</option>';
   str += '</select>';
   str += '</div>';
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';

   str += '<div id=\"' + divid + '_leftdiv\" style=\"position:relative;\">';
   str += '<div style=\"float:left;width:180px;\">';
   str += 'Left';
   str += '</div>';
   str += '<div style=\"float:left;width:220px;\">';
   str += '<select onchange=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" id=\"' + divid + '_left\">';
   str += '<option value=\"0\">0%</option>';
   str += '<option value=\"10\">10%</option>';
   str += '<option value=\"12\">12%</option>';
   str += '<option value=\"20\">20%</option>';
   str += '<option value=\"25\">25%</option>';
   str += '<option value=\"40\">40%</option>';
   str += '<option value=\"50\">50%</option>';
   str += '<option value=\"60\">60%</option>';
   str += '<option value=\"75\">75%</option>';
   str += '<option value=\"80\">80%</option>';
   str += '</select>';
   str += '</div>';
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';

   str += '<div id=\"' + divid + '_topdiv\" style=\"position:relative;\">';
   str += '<div style=\"float:left;width:180px;\">';
   str += 'Top';
   str += '</div>';
   str += '<div style=\"float:left;width:220px;\">';
   str += '<select onchange=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" id=\"' + divid + '_top\">';
   str += '<option value=\"0\">0%</option>';
   str += '<option value=\"10\">10%</option>';
   str += '<option value=\"12\">12%</option>';
   str += '<option value=\"20\">20%</option>';
   str += '<option value=\"25\">25%</option>';
   str += '<option value=\"40\">40%</option>';
   str += '<option value=\"50\">50%</option>';
   str += '<option value=\"60\">60%</option>';
   str += '<option value=\"75\">75%</option>';
   str += '<option value=\"80\">80%</option>';
   str += '<option value=\"85\">85%</option>';
   str += '<option value=\"90\">90%</option>';
   str += '</select>';
   str += '</div>';
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';
   
   str += '<div id=\"' + divid + '_paddiv\" style=\"position:relative;\">';
   str += '<div style=\"float:left;width:180px;\">';
   str += 'Padding';
   str += '</div>';
   str += '<div style=\"float:left;width:220px;\">';
   str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_pad\" style=\"width:80px;font-size:10px;font-family:arial;\">';
   str += '</div>';
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';
   
   str += '<div id=\"' + divid + '_maxdiv\" style=\"position:relative;\">';
   str += '<div style=\"float:left;width:180px;\">';
   str += 'Max Width';
   str += '</div>';
   str += '<div style=\"float:left;width:220px;\">';
   str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_max\" style=\"width:80px;font-size:10px;font-family:arial;\">';
   str += '</div>';
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';
   
   
   str += '</div>';

   
   
   str += jsfpb_togglehtml('Font','font');   
   str += '<div id=\"font\" style=\"display:none;\">';
   
   str += '<div id=\"' + divid + '_fszdiv\" style=\"position:relative;\">';
   str += '<div style=\"float:left;width:180px;\">';
   str += 'Font Size';
   str += '</div>';
   str += '<div style=\"float:left;width:220px;\">';
   str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_fsz\" style=\"width:80px;font-size:10px;font-family:arial;\">';
   str += '</div>';
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';
   
   str += '<div id=\"' + divid + '_ffmdiv\" style=\"position:relative;\">';
   str += '<div style=\"float:left;width:180px;\">';
   str += 'Font Family';
   str += '</div>';
   str += '<div style=\"float:left;width:220px;\">';
   str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_ffm\" style=\"width:200px;font-size:10px;font-family:arial;\">';
   str += '</div>';
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';
   
   str += '<div id=\"' + divid + '_alndiv\" style=\"position:relative;\">';
   str += '<div style=\"width:220px;margin-top:5px;margin-bottom:5px;\">';
   str += '<input type=\"checkbox\" value=\"1\" onchange=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" id=\"' + divid + '_aln\">';
   str += ' Center-align';
   str += '</div>';
   str += '</div>';
   
   str += '<div id=\"' + divid + '_blddiv\" style=\"position:relative;\">';
   str += '<div style=\"width:220px;margin-top:5px;margin-bottom:5px;\">';
   str += '<input type=\"checkbox\" value=\"1\" onchange=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" id=\"' + divid + '_bld\">';
   str += ' Bold';
   str += '</div>';
   str += '</div>';
   
   str += '<div id=\"' + divid + '_depdiv\" style=\"position:relative;\">';
   str += '<div style=\"float:left;width:180px;\">';
   str += 'Shadow';
   str += '</div>';
   str += '<div style=\"float:left;width:220px;\">';
   str += '<select onchange=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" id=\"' + divid + '_dep\"><option value=\"no shadow\">No Shadow</option><option value=\"small shadow\">Small Shadow</option><option value=\"large shadow\">Large Shadow</option></select>';
   str += '</div>';
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';
   
   str += '<div id=\"' + divid + '_clrdiv\" style=\"position:relative;\">';
   str += '<div style=\"float:left;width:180px;\">';
   str += 'Font Color';
   str += '</div>';
   str += '<div style=\"float:left;width:70px;\">';
   str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_clr\" style=\"width:60px;font-size:10px;font-family:arial;\">';
   str += '</div>';
   str += '<div id=\"' + divid + '_clrpre\" style=\"float:left;width:18px;height:18px;overflow:hidden;\"></div>';   
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';
   
   str += '</div>';

   
   
   
   str += jsfpb_togglehtml('Other','other');   
   str += '<div id=\"other\" style=\"display:none;\">';

   str += '<div id=\"' + divid + '_urldiv\" style=\"position:relative;\">';
   str += '<div style=\"float:left;width:180px;\">';
   str += 'URL Link';
   str += '</div>';
   str += '<div style=\"float:left;width:220px;\">';
   str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_url\" style=\"width:80px;font-size:10px;font-family:arial;\">';
   str += '</div>';
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';

   
   str += '<div id=\"' + divid + '_onclickdiv\" style=\"position:relative;\">';
   str += '<div style=\"float:left;width:180px;\">';
   str += 'OnClick Link';
   str += '</div>';
   str += '<div style=\"float:left;width:220px;\">';
   str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_onclick\" style=\"width:80px;font-size:10px;font-family:arial;\">';
   str += '</div>';
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';

   str += '<div ';
   str += 'id=\"' + divid + '_docbtndiv\" ';
   str += 'style=\"width:80px;text-align:center;font-size:10px;color:#333333;cursor:pointer;background-color:#F1F1F1;padding:4px;border:1px solid #444444;border-radius:3px;\" ';
   str += 'onclick=\"window.open(\'' + jsfpb_domain + jsfpb_codedir + 'uploadimage.php?userid=9&token=9&prefix=' + r + '_' + s + '_' + l + '&wd_id=doc&field_id=0\');\"';
   str += '>Upload Doc</div>';
   str += '<div id=\"' + divid + '_docdiv\" style=\"margin-top:5px;margin-bottom:15px;position:relative;\"></div>';
   

   str += '<div id=\"' + divid + '_bgdiv\" style=\"position:relative;\">';
   str += '<div style=\"float:left;width:180px;\">';
   str += 'Background Color';
   str += '</div>';
   str += '<div style=\"float:left;width:70px;\">';
   str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_bg\" style=\"width:60px;font-size:10px;font-family:arial;\">';
   str += '</div>';
   str += '<div id=\"' + divid + '_bgpre\" style=\"float:left;width:18px;height:18px;overflow:hidden;\"></div>';   
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';
   
   str += '<div id=\"' + divid + '_trxdiv\" style=\"position:relative;\">';
   str += '<div style=\"float:left;width:180px;\">';
   str += 'Transparency<br><span style=\"font-size:10px;\">100 = no transparency, 80 = mostly visible, 0 = invisible</span>';
   str += '</div>';
   str += '<div style=\"float:left;width:220px;\">';
   str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_trx\" style=\"width:80px;font-size:10px;font-family:arial;\">';
   str += '</div>';
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';
   
   
   str += '<div id=\"' + divid + '_hidediv\" style=\"position:relative;\">';
   str += '<div style=\"width:220px;margin-top:5px;margin-bottom:5px;\">';
   str += '<input type=\"checkbox\" value=\"1\" onchange=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" id=\"' + divid + '_hide\">';
   str += ' Hide';
   str += '</div>';
   str += '</div>';
   
   str += '</div>';
   

   str += '<div ';
   str += 'id=\"' + divid + '_imgbtndiv\" ';
   str += 'style=\"width:80px;text-align:center;font-size:10px;color:#333333;cursor:pointer;background-color:#F1F1F1;padding:4px;border:1px solid #444444;border-radius:3px;\" ';
   //str += 'onclick=\"window.open(\'' + jsfpb_domain + jsfpb_codedir + 'uploadimage.php?userid=9&token=9&prefix=' + r + '&wd_id=' + s + '&field_id=' + l + '\');\"';
   str += 'onclick=\"window.open(\'' + jsfpb_domain + jsfpb_codedir + 'uploadimage.php?userid=9&token=9&prefix=' + r + '_' + s + '_' + l + '&wd_id=img&field_id=0\');\"';
   str += '>Select Image</div>';
   str += '<div id=\"' + divid + '_imgdiv\" style=\"margin-top:5px;position:relative;\"></div>';
   str += '<div id=\"' + divid + '_imgdspdiv\" style=\"position:relative;\">';

   str += '<div style=\"float:left;width:180px;\">';
   str += 'Image Display';
   str += '</div>';
   str += '<div style=\"float:left;width:220px;\">';
   str += '<select onchange=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" id=\"' + divid + '_imgdsp\">';
   str += '<option value=\"full\">Full Image</option>';
   str += '<option value=\"stretch\">Stretch to fill</option>';
   str += '</select>';
   str += '</div>';
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';

   
   str += '<div id=\"' + divid + '_contentdiv\" style=\"position:relative;\">';
   str += '<div id=\"' + divid + '_contentlbl\" style=\"float:left;width:180px;\">';
   str += 'Content';
   str += '</div>';
   str += '<div id=\"' + divid + '_contentinput\" style=\"float:left;width:220px;\">';
   //str += '<textarea onkeyup=\"jsfpb_page.rows[' + r + '].slots[' + s + '].layers[' + l + '].content = jsfpb_replaceAll(\'\\\"\',\'#jsfquote#\',jQuery(\'#' + divid + '_content\').val());\" id=\"' + divid + '_content\" style=\"width:200px;height:190px;font-size:10px;font-family:arial;\"></textarea>';
   str += '<textarea onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" id=\"' + divid + '_content\" style=\"width:200px;height:190px;font-size:10px;font-family:arial;\"></textarea>';
   str += '</div>';
   str += '<div style=\"clear:both;\"></div>';
   str += '<div id=\"' + divid + '_contentextra\"></div>';
   str += '</div>';
   


   
   
   
   
   // 171024 - new fields globally
   str += '<div id=\"' + divid + '_mttldiv\" style=\"position:relative;display:none;\">';
   str += '<div style=\"float:left;width:180px;\">';
   str += 'Title';
   str += '</div>';
   str += '<div style=\"float:left;width:220px;\">';
   str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_mttl\" style=\"width:200px;font-size:10px;font-family:arial;\">';
   str += '</div>';
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';
   
   str += '<div id=\"' + divid + '_msubttldiv\" style=\"position:relative;display:none;\">';
   str += '<div style=\"float:left;width:180px;\">';
   str += 'Sub-Title';
   str += '</div>';
   str += '<div style=\"float:left;width:220px;\">';
   str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_msubttl\" style=\"width:200px;font-size:10px;font-family:arial;\">';
   str += '</div>';
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';
   
   str += '<div id=\"' + divid + '_mpaddiv\" style=\"position:relative;display:none;\">';
   str += '<div id=\"' + divid + '_mpadlbl\" style=\"float:left;width:180px;\">';
   str += 'Internal Padding';
   str += '</div>';
   str += '<div style=\"float:left;width:220px;\">';
   str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_mpad\" style=\"width:90px;font-size:10px;font-family:arial;\">';
   str += '</div>';
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';
   
   str += '<div id=\"' + divid + '_mstylediv\" style=\"position:relative;display:none;\">';
   str += '<div style=\"float:left;width:180px;\">';
   str += 'Formatting Style';
   str += '</div>';
   str += '<div style=\"float:left;width:220px;\">';
   str += '<select id=\"' + divid + '_mstyle\" onchange=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\">';
   str += '<option value=\"none\">None</option>';
   str += '<option value=\"standard\">Standard-sized Titles</option>';
   str += '<option value=\"right\">Other</option>';
   str += '</select>';
   str += '</div>';
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';
   
   str += '<div id=\"' + divid + '_msidediv\" style=\"position:relative;display:none;\">';
   str += '<div style=\"float:left;width:180px;\">';
   str += 'Buttons Alignment';
   str += '</div>';
   str += '<div style=\"float:left;width:220px;\">';
   str += '<select id=\"' + divid + '_mside\" onchange=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\">';
   str += '<option value=\"center\">Center</option>';
   str += '<option value=\"left\">Left</option>';
   str += '<option value=\"right\">Right</option>';
   str += '</select>';
   str += '</div>';
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';
   
   for(var i=1;i<=5;i++) {
      str += '<div style=\"margin-top:8px;margin-bottom:3px;\">';

      str += '<div id=\"' + divid + '_mbtn' + i + 'div\" style=\"position:relative;display:none;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Button ' + i + ' Label';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_mbtn' + i + '\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
      str += '<div id=\"' + divid + '_murl' + i + 'div\" style=\"position:relative;display:none;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Button ' + i + ' URL';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_murl' + i + '\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
      str += '<div id=\"' + divid + '_mfg' + i + 'div\" style=\"position:relative;display:none;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'FG Color' + i;
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_mfg' + i + '\" style=\"width:60px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div id=\"' + divid + '_mfgpre\" style=\"float:left;width:18px;height:18px;overflow:hidden;\"></div>';   
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
      str += '<div id=\"' + divid + '_mbg' + i + 'div\" style=\"position:relative;display:none;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'BG Color' + i;
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_mbg' + i + '\" style=\"width:60px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div id=\"' + divid + '_mbgpre\" style=\"float:left;width:18px;height:18px;overflow:hidden;\"></div>';   
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
      str += '<div id=\"' + divid + '_mhg' + i + 'div\" style=\"position:relative;display:none;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Hover BG Color ' + i;
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_mhg' + i + '\" style=\"width:60px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div id=\"' + divid + '_mbgpre\" style=\"float:left;width:18px;height:18px;overflow:hidden;\"></div>';   
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
      
      str += '</div>';
   }

   
   
   
   
   
   
   
   
   if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].type) && jsfpb_page.rows[r].slots[s].layers[l].type!='Image' && jsfpb_page.rows[r].slots[s].layers[l].type!='Text' && jsfpb_page.rows[r].slots[s].layers[l].type!='HTML') str += jsfpb_customadmin(jsfpb_page.rows[r].slots[s].layers[l].type,r,s,l);
   
   jQuery('#preview').html(str);
   jsfpb_populateLayer(r,s,l);
}

function jsfpb_populateLayer(r,s,l) {
   var divid = r + '_' + s + '_' + l;

   if(!Boolean(jsfpb_page.rows[r].slots[s].layers[l])) jsfpb_page.rows[r].slots[s].layers[l] = {};
   var layer = jsfpb_page.rows[r].slots[s].layers[l];
   
   if (Boolean(layer.type)) jQuery('#' + divid + '_type').val(layer.type);
   
   
   if (Boolean(layer.wd)) jQuery('#' + divid + '_wd').val(layer.wd);
   if (Boolean(layer.left)) jQuery('#' + divid + '_left').val(layer.left);
   if (Boolean(layer.top)) jQuery('#' + divid + '_top').val(layer.top);
   if (Boolean(layer.fsz)) jQuery('#' + divid + '_fsz').val(layer.fsz);
   if (Boolean(layer.pad)) jQuery('#' + divid + '_pad').val(layer.pad);
   if (Boolean(layer.max)) jQuery('#' + divid + '_max').val(layer.max);
   if (Boolean(layer.ffm)) jQuery('#' + divid + '_ffm').val(layer.ffm);
   if (Boolean(layer.dep)) jQuery('#' + divid + '_dep').val(layer.dep);
   
   if (Boolean(layer.aln)) document.getElementById(divid + '_aln').checked = true;
   else document.getElementById(divid + '_aln').checked = false;
   
   if (Boolean(layer.bld)) document.getElementById(divid + '_bld').checked = true;
   else document.getElementById(divid + '_bld').checked = false;
   
   if (Boolean(layer.hide)) document.getElementById(divid + '_hide').checked = true;
   else document.getElementById(divid + '_hide').checked = false;
   
   if (Boolean(layer.clr)) {
      jQuery('#' + divid + '_clr').val(layer.clr);
      jQuery('#' + divid + '_clrpre').css('background-color',layer.clr);
   }
   if (Boolean(layer.bg)) {
      jQuery('#' + divid + '_bg').val(layer.bg);
      jQuery('#' + divid + '_bgpre').css('background-color',layer.bg);
   }
   if (Boolean(layer.trx)) jQuery('#' + divid + '_trx').val(layer.trx);
   if (Boolean(layer.content)) jQuery('#' + divid + '_content').val(jsfpb_convertback(layer.content));
   if (Boolean(layer.imgdsp)) jQuery('#' + divid + '_imgdsp').val(layer.imgdsp);
   if (Boolean(layer.url)) jQuery('#' + divid + '_url').val(layer.url);
   if (Boolean(layer.onclick)) jQuery('#' + divid + '_onclick').val(jsfpb_convertback(layer.onclick));
   
   if (Boolean(layer.doc)) {
      var doc = '';
      doc += '<div onclick=\"window.open(\'' + layer.doc + '\');\" style=\"position:relative;top:3px;font-weight:bold;cursor:pointer;font-family:arial;font-size:10px;\">' + layer.doc + '</div>';
      doc += '<div ';
      doc += 'onclick=\"if(confirm(\'Are you sure you want to permanently remove this document?\')) { jQuery(\'#' + divid + '_docdiv\').html(\'\'); jsfpb_page.rows[' + r + '].slots[' + s + '].layers[' + l + '].doc = \'\'; jsfpb_changeswaiting=1; }\" ';
      doc += 'style=\"position:relative;top:3px;left:3px;cursor:pointer;font-family:arial;font-size:10px;\">delete</div>';
      jQuery('#' + divid + '_docdiv').html(doc);
   }
   
   if (Boolean(layer.img)) {
      var img = '<img src=\"' + layer.img + '\" style=\"max-width:120px;max-height:80px;width:auto;height:auto;\">';
      img += '<div onclick=\"if(confirm(\'Are you sure you want to permanently remove this image?\')) { jQuery(\'#' + divid + '_imgdiv\').html(\'\'); jsfpb_page.rows[' + r + '].slots[' + s + '].layers[' + l + '].img = \'\'; jsfpb_changeswaiting=1; }\" style=\"position:absolute;top:3px;left:3px;font-weight:bold;color:red;cursor:pointer;font-family:arial;font-size:10px;background-color:#EEEEEE;width:12px;height:12px;overflow:hidden;border-radius:4px;text-align:center;\">x</div>';
      img += '<div onclick=\"window.open(\'' + layer.img + '\');\" style=\"position:absolute;top:3px;left:18px;font-weight:bold;color:green;cursor:pointer;font-family:arial;font-size:10px;background-color:#EEEEEE;width:12px;height:12px;overflow:hidden;border-radius:4px;text-align:center;\">+</div>';
      jQuery('#' + divid + '_imgdiv').html(img);
      //jsfpb_page.rows[r].slots[s].layers[l].img = fn;
   }
   
   
   // 171024 - new fields
   if (Boolean(layer.mttl)) jQuery('#' + divid + '_mttl').val(jsfpb_convertback(layer.mttl));
   if (Boolean(layer.msubttl)) jQuery('#' + divid + '_msubttl').val(jsfpb_convertback(layer.msubttl));
   if (Boolean(layer.mstyle)) jQuery('#' + divid + '_mstyle').val(jsfpb_convertback(layer.mstyle));
   if (Boolean(layer.mpad)) jQuery('#' + divid + '_mpad').val(jsfpb_convertback(layer.mpad));
   if (Boolean(layer.mside)) jQuery('#' + divid + '_mside').val(jsfpb_convertback(layer.mside));
   for(var i=1;i<=5;i++) {
      if (Boolean(layer['mbtn' + i])) jQuery('#' + divid + '_mbtn' + i).val(jsfpb_convertback(layer['mbtn' + i]));
      if (Boolean(layer['murl' + i])) jQuery('#' + divid + '_murl' + i).val(jsfpb_convertback(layer['murl' + i]));
      if (Boolean(layer['mbg' + i])) {
         jQuery('#' + divid + '_mbg' + i).val(jsfpb_convertback(layer['mbg' + i]));
         jQuery('#' + divid + '_mbgpre').css('background-color',layer['mbg' + i]);
      }
      if (Boolean(layer['mfg' + i])) {
         jQuery('#' + divid + '_mfg' + i).val(jsfpb_convertback(layer['mfg' + i]));
         jQuery('#' + divid + '_mfgpre').css('background-color',layer['mfg' + i]);
      }
      if (Boolean(layer['mhg' + i])) {
         jQuery('#' + divid + '_mhg' + i).val(jsfpb_convertback(layer['mhg' + i]));
         jQuery('#' + divid + '_mhgpre').css('background-color',layer['mhg' + i]);
      }
   }

      
   
   
   jQuery('#' + divid + '_wddiv').show();
   jQuery('#' + divid + '_leftdiv').show();
   jQuery('#' + divid + '_maxdiv').show();
   jQuery('#' + divid + '_topdiv').show();
   jQuery('#' + divid + '_typediv').show();
   jQuery('#' + divid + '_trxdiv').show();
   jQuery('#' + divid + '_urldiv').show();
   jQuery('#' + divid + '_onclickdiv').show();
   var type = jQuery('#' + divid + '_type').val();
   if(type=='Text') {
      jQuery('#' + divid + '_fszdiv').show();
      jQuery('#' + divid + '_paddiv').show();
      jQuery('#' + divid + '_ffmdiv').show();
      jQuery('#' + divid + '_depdiv').hide();
      jQuery('#' + divid + '_clrdiv').show();
      jQuery('#' + divid + '_bgdiv').show();
      jQuery('#' + divid + '_contentdiv').show();
      jQuery('#' + divid + '_imgbtndiv').hide();
      jQuery('#' + divid + '_imgdiv').hide();
      jQuery('#' + divid + '_imgdspdiv').hide();
   } else if(type=='HTML') {
      jQuery('#' + divid + '_fszdiv').show();
      jQuery('#' + divid + '_paddiv').show();
      jQuery('#' + divid + '_ffmdiv').show();
      jQuery('#' + divid + '_depdiv').hide();
      jQuery('#' + divid + '_clrdiv').show();
      jQuery('#' + divid + '_bgdiv').show();
      jQuery('#' + divid + '_contentdiv').show();
      jQuery('#' + divid + '_imgbtndiv').hide();
      jQuery('#' + divid + '_imgdiv').hide();
      jQuery('#' + divid + '_imgdspdiv').hide();
   } else if(type=='Image') {
      jQuery('#' + divid + '_fszdiv').show();
      jQuery('#' + divid + '_paddiv').show();
      jQuery('#' + divid + '_ffmdiv').show();
      jQuery('#' + divid + '_depdiv').hide();
      jQuery('#' + divid + '_clrdiv').show();
      jQuery('#' + divid + '_bgdiv').show();
      jQuery('#' + divid + '_contentdiv').show();
      jQuery('#' + divid + '_imgbtndiv').show();
      jQuery('#' + divid + '_imgdiv').show();
      jQuery('#' + divid + '_imgdspdiv').show();
   } else if(type=='Content') {
      jQuery('#' + divid + '_fszdiv').hide();
      jQuery('#' + divid + '_paddiv').show();
      jQuery('#' + divid + '_ffmdiv').hide();
      jQuery('#' + divid + '_depdiv').hide();
      jQuery('#' + divid + '_clrdiv').hide();
      jQuery('#' + divid + '_bgdiv').hide();
      jQuery('#' + divid + '_contentdiv').show();
      jQuery('#' + divid + '_imgbtndiv').hide();
      jQuery('#' + divid + '_imgdiv').hide();
      jQuery('#' + divid + '_imgdspdiv').hide();
   } else if(type=='Page Import') {
      jQuery('#' + divid + '_fszdiv').show();
      jQuery('#' + divid + '_paddiv').show();
      jQuery('#' + divid + '_ffmdiv').show();
      jQuery('#' + divid + '_depdiv').hide();
      jQuery('#' + divid + '_clrdiv').show();
      jQuery('#' + divid + '_bgdiv').show();
      jQuery('#' + divid + '_contentdiv').show();
      jQuery('#' + divid + '_imgbtndiv').hide();
      jQuery('#' + divid + '_imgdiv').hide();
      jQuery('#' + divid + '_imgdspdiv').hide();
      jsfpb_switchcontenttype(divid,r,s,l,'text');
      jsfpb_addpageimportlink(divid,layer.content);
      jQuery('#' + divid + '_contentlbl').html('Page name');
   } else if(type=='Formatted Block') {
      jQuery('#' + divid + '_fszdiv').show();
      jQuery('#' + divid + '_paddiv').show();
      jQuery('#' + divid + '_ffmdiv').show();
      jQuery('#' + divid + '_depdiv').show();
      jQuery('#' + divid + '_clrdiv').show();
      jQuery('#' + divid + '_bgdiv').show();
      jQuery('#' + divid + '_contentdiv').show();
      jQuery('#' + divid + '_imgbtndiv').show();
      jQuery('#' + divid + '_imgdiv').show();
      jQuery('#' + divid + '_imgdspdiv').show();
      
      jQuery('#' + divid + '_mttldiv').show();
      jQuery('#' + divid + '_msubttldiv').show();
      jQuery('#' + divid + '_msidediv').show();
      jQuery('#' + divid + '_mstylediv').show();
      jQuery('#' + divid + '_mpaddiv').show();
      for(var i=1;i<=5;i++) {
         jQuery('#' + divid + '_mbtn' + i + 'div').show();
         jQuery('#' + divid + '_murl' + i + 'div').show();
         jQuery('#' + divid + '_mbg' + i + 'div').show();
         jQuery('#' + divid + '_mfg' + i + 'div').show();
         jQuery('#' + divid + '_mhg' + i + 'div').show();
      }
   } else if(type=='Article Block') {
      jQuery('#' + divid + '_fszdiv').show();
      jQuery('#' + divid + '_paddiv').show();
      jQuery('#' + divid + '_ffmdiv').show();
      jQuery('#' + divid + '_depdiv').hide();
      jQuery('#' + divid + '_clrdiv').show();
      jQuery('#' + divid + '_bgdiv').show();
      jQuery('#' + divid + '_contentdiv').show();
      jQuery('#' + divid + '_imgbtndiv').show();
      jQuery('#' + divid + '_imgdiv').show();
      jQuery('#' + divid + '_imgdspdiv').show();
      
      jQuery('#' + divid + '_mttldiv').show();
      jQuery('#' + divid + '_msubttldiv').show();
      jQuery('#' + divid + '_msidediv').hide();
      jQuery('#' + divid + '_mstylediv').hide();
      jQuery('#' + divid + '_mpaddiv').show();
      jQuery('#' + divid + '_mbtn1div').show();
      jQuery('#' + divid + '_murl1div').show();
      
   } else if(type=='Download Block') {
      jQuery('#' + divid + '_fszdiv').show();
      jQuery('#' + divid + '_paddiv').show();
      jQuery('#' + divid + '_ffmdiv').show();
      jQuery('#' + divid + '_depdiv').hide();
      jQuery('#' + divid + '_clrdiv').show();
      jQuery('#' + divid + '_bgdiv').show();
      jQuery('#' + divid + '_contentdiv').show();
      jQuery('#' + divid + '_imgbtndiv').show();
      jQuery('#' + divid + '_imgdiv').show();
      jQuery('#' + divid + '_imgdspdiv').show();
      jQuery('#' + divid + '_mttldiv').show();
      jQuery('#' + divid + '_mbg1div').show();
      jQuery('#' + divid + '_mfg1div').show();
   } else if(type=='Visual Builder') {
      jQuery('#' + divid + '_fszdiv').hide();
      jQuery('#' + divid + '_paddiv').show();
      jQuery('#' + divid + '_ffmdiv').hide();
      jQuery('#' + divid + '_depdiv').hide();
      jQuery('#' + divid + '_clrdiv').hide();
      jQuery('#' + divid + '_bgdiv').hide();
      jQuery('#' + divid + '_contentdiv').show();
      jQuery('#' + divid + '_imgbtndiv').hide();
      jQuery('#' + divid + '_imgdiv').hide();
      jQuery('#' + divid + '_imgdspdiv').hide();
      jsfpb_switchcontenttype(divid,r,s,l,'text');
      //alert('calling jsfpb_addvisualbuilderlink(' + divid + ')');
      jsfpb_addvisualbuilderlink(divid);
      jQuery('#' + divid + '_contentlbl').html('Reference Name');
   } else {
      jQuery('#' + divid + '_fszdiv').show();
      jQuery('#' + divid + '_paddiv').show();
      jQuery('#' + divid + '_ffmdiv').show();
      jQuery('#' + divid + '_depdiv').hide();
      jQuery('#' + divid + '_clrdiv').show();
      jQuery('#' + divid + '_bgdiv').show();
      jQuery('#' + divid + '_contentdiv').hide();
      jQuery('#' + divid + '_imgbtndiv').hide();
      jQuery('#' + divid + '_imgdiv').hide();
      jQuery('#' + divid + '_imgdspdiv').hide();
      jsfpb_custompopulate(type,r,s,l);      
   }
   
}

function jsfpb_changeLayer(r,s,l,refresh) {
   jsfpb_changeswaiting=1;
   var divid = r + '_' + s + '_' + l;
   
   jQuery('#' + divid + '_contentextra').html('');
   
   //alert('wd value: ' + jQuery('#' + divid + '_wd').val());
   jsfpb_page.rows[r].slots[s].layers[l].wd = jQuery('#' + divid + '_wd').val();
   //alert('new wd value: ' + jsfpb_page.rows[r].slots[s].layers[l].wd);
   jsfpb_page.rows[r].slots[s].layers[l].left = jQuery('#' + divid + '_left').val();
   jsfpb_page.rows[r].slots[s].layers[l].top = jQuery('#' + divid + '_top').val();
   jsfpb_page.rows[r].slots[s].layers[l].type = jQuery('#' + divid + '_type').val();
   jsfpb_page.rows[r].slots[s].layers[l].trx = jQuery('#' + divid + '_trx').val();
   jsfpb_page.rows[r].slots[s].layers[l].fsz = parseInt(jQuery('#' + divid + '_fsz').val());
   jsfpb_page.rows[r].slots[s].layers[l].pad = parseInt(jQuery('#' + divid + '_pad').val());
   jsfpb_page.rows[r].slots[s].layers[l].max = parseInt(jQuery('#' + divid + '_max').val());
   jsfpb_page.rows[r].slots[s].layers[l].ffm = jsfpb_convertstring(jQuery('#' + divid + '_ffm').val());
   jsfpb_page.rows[r].slots[s].layers[l].aln = document.getElementById(divid + '_aln').checked;
   jsfpb_page.rows[r].slots[s].layers[l].bld = document.getElementById(divid + '_bld').checked;
   jsfpb_page.rows[r].slots[s].layers[l].dep = jsfpb_convertstring(jQuery('#' + divid + '_dep').val());
   jsfpb_page.rows[r].slots[s].layers[l].hide = document.getElementById(divid + '_hide').checked;
   jsfpb_page.rows[r].slots[s].layers[l].clr = jsfpb_convertstring(jQuery('#' + divid + '_clr').val());
   jQuery('#' + divid + '_clrpre').css('background-color',jsfpb_page.rows[r].slots[s].layers[l].clr);
   jsfpb_page.rows[r].slots[s].layers[l].bg = jsfpb_convertstring(jQuery('#' + divid + '_bg').val());
   jQuery('#' + divid + '_bgpre').css('background-color',jsfpb_page.rows[r].slots[s].layers[l].bg);
   jsfpb_page.rows[r].slots[s].layers[l].content = jsfpb_convertstring(jQuery('#' + divid + '_content').val());
   jsfpb_page.rows[r].slots[s].layers[l].imgdsp = jQuery('#' + divid + '_imgdsp').val();
   jsfpb_page.rows[r].slots[s].layers[l].url = jsfpb_convertstring(jQuery('#' + divid + '_url').val());
   jsfpb_page.rows[r].slots[s].layers[l].onclick = jsfpb_convertstring(jQuery('#' + divid + '_onclick').val());

   jsfpb_page.rows[r].slots[s].layers[l].mttl = jsfpb_convertstring(jQuery('#' + divid + '_mttl').val());
   jsfpb_page.rows[r].slots[s].layers[l].msubttl = jsfpb_convertstring(jQuery('#' + divid + '_msubttl').val());
   jsfpb_page.rows[r].slots[s].layers[l].mside = jsfpb_convertstring(jQuery('#' + divid + '_mside').val());
   jsfpb_page.rows[r].slots[s].layers[l].mstyle = jsfpb_convertstring(jQuery('#' + divid + '_mstyle').val());
   jsfpb_page.rows[r].slots[s].layers[l].mpad = jsfpb_convertstring(jQuery('#' + divid + '_mpad').val());
   for(var i=1;i<=5;i++) {
      jsfpb_page.rows[r].slots[s].layers[l]['mbtn' + i] = jsfpb_convertstring(jQuery('#' + divid + '_mbtn' + i).val());
      jsfpb_page.rows[r].slots[s].layers[l]['murl' + i] = jsfpb_convertstring(jQuery('#' + divid + '_murl' + i).val());
      jsfpb_page.rows[r].slots[s].layers[l]['mbg' + i] = jsfpb_convertstring(jQuery('#' + divid + '_mbg' + i).val());
      jQuery('#' + divid + '_mbgpre').css('background-color',jsfpb_page.rows[r].slots[s].layers[l]['mbg' + i]);
      jsfpb_page.rows[r].slots[s].layers[l]['mfg' + i] = jsfpb_convertstring(jQuery('#' + divid + '_mfg' + i).val());
      jQuery('#' + divid + '_mfgpre').css('background-color',jsfpb_page.rows[r].slots[s].layers[l]['mfg' + i]);
      jsfpb_page.rows[r].slots[s].layers[l]['mhg' + i] = jsfpb_convertstring(jQuery('#' + divid + '_mhg' + i).val());
      jQuery('#' + divid + '_mhgpre').css('background-color',jsfpb_page.rows[r].slots[s].layers[l]['mhg' + i]);
   }
   
   
   if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].type) && jsfpb_page.rows[r].slots[s].layers[l].type!='Image' && jsfpb_page.rows[r].slots[s].layers[l].type!='Text' && jsfpb_page.rows[r].slots[s].layers[l].type!='HTML') {
      jsfpb_customchangelayer(jsfpb_page.rows[r].slots[s].layers[l].type,r,s,l);
      if(jsfpb_page.rows[r].slots[s].layers[l].type=='Page Import') {
         jsfpb_addpageimportlink(divid,jsfpb_page.rows[r].slots[s].layers[l].content);
      }
   }

   if(Boolean(refresh)) {
      jsfpb_switchcontenttype(divid,r,s,l,'textarea');
      jsfpb_displaySlotInput(r,s);
      jsfpb_displayLayerInput(r,s,l);
   }
}

function jsfpb_addpageimportlink(divid,pgname) {
   if(Boolean(divid) && Boolean(pgname)) {
      var ival;
      var found = false;
      for  (var i=0;i<jsfpb_values.length;i++){
         var temp = jsfpb_values[i].name.substr(6);
         if(temp == pgname) {
            ival = i;
            found = true;
            break;
         }
      }

      if(Boolean(found)) {
         var str = '';
         str += '<div style=\"margin-left:180px;margin-bottom:10px;color:blue;font-size:10px;cursor:pointer;\" ';
         str += 'onclick=\"';
         str += 'if(Boolean(jsfpb_changeswaiting)) { alert(\'Please save changes to this page first.\');';
         str += '} else { ';
         str += 'jQuery(\'#sel_pg_name\').val(\'' + ival + '\');';
         str += 'jsfpb_openpagetool(); ';
         str += '}';
         str += '\">Edit This Page &gt;</div>';
         jQuery('#' + divid + '_contentextra').html(str);
      }
   }
}

function jsfpb_addvisualbuilderlink(divid) {
   if(Boolean(divid)) {
      var str = '';
      str += '<div style=\"margin-left:180px;margin-bottom:10px;color:blue;font-size:10px;cursor:pointer;\" ';
      str += 'onclick=\"';
      str += 'var vname=jQuery(\'#' + divid + '_content\').val();';
      str += 'if(Boolean(vname)) ';
      str += 'window.open(\'' + jsfpb_domain + jsfpb_codedir + 'jsf_visualbuilder.php?userid=' + jsfpb_userid + '&token=' + jsfpb_token + '&wd_id=\' + encodeURIComponent(\'' + jsfpb_tablename + '\') + \'&name=\' + encodeURIComponent(vname));';
      str += ' else ';
      str += 'alert(\'Please enter a name before launching the builder.\');';
      str += '\">Launch Visual Builder &gt;</div>';
      jQuery('#' + divid + '_contentextra').html(str);
   }
}

function jsfpb_previewHTML(){
   //jQuery('#jsfpb_fullpage_inner').html(jsfpb_getPageHTML(jsfpb_page));
   jsfpb_getPageHTML(jsfpb_page,'','jsfpb_fullpage_inner');
   jQuery('#jsfpb_fullpage').show();
   
   for(var i=0;i<jsfpb_page.rows.length;i++) {
      for(var j=0;j<jsfpb_page.rows[i].slots.length;j++) {
         jQuery('#jsfpb_fullpage_inner_r' + i + '_s' + j + '_edit').show();
      }
   }
   
}

function jsfpb_togglehtml(title,divid){
   var str = '';
   str += '<div style=\"position:relative;margin-top:5px;margin-bottom:5px;\">';
   str += '<div onclick=\"jQuery(\'#' + divid + '_plus\').hide();jQuery(\'#' + divid + '_minus\').show();jQuery(\'#' + divid + '\').show();\" id=\"' + divid + '_plus\" style=\"position:relative;float:left;width:14px;height:14px;border-radius:7px;margin-right:4px;background-color:#555555;cursor:pointer;\">';
   str += '<div style=\"position:absolute;left:6px;top:3px;width:2px;height:8px;overflow:hidden;background-color:#FFFFFF;\"></div>';
   str += '<div style=\"position:absolute;left:3px;top:6px;width:8px;height:2px;overflow:hidden;background-color:#FFFFFF;\"></div>';
   str += '</div>';
   str += '<div onclick=\"jQuery(\'#' + divid + '_minus\').hide();jQuery(\'#' + divid + '_plus\').show();jQuery(\'#' + divid + '\').hide();\" id=\"' + divid + '_minus\" style=\"position:relative;float:left;display:none;width:14px;height:14px;border-radius:7px;margin-right:4px;background-color:#555555;cursor:pointer;\">';
   str += '<div style=\"position:absolute;left:3px;top:6px;width:8px;height:2px;overflow:hidden;background-color:#FFFFFF;\"></div>';
   str += '</div>';
   str += '<div style=\"position:relative;float:left;font-size:14px;color:#444444;font-family:arial;padding-top:2px;\">';
   str += title;
   str += '</div>';
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';
   return str;
}

function jsfpb_switchcontenttype(divid,r,s,l,type) {
   if(!Boolean(type) || type!='textarea') type='text';
   var val = jQuery('#' + divid + '_content').val();
   var str = '';
   if(type=='textarea') {
      str += '<textarea onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" id=\"' + divid + '_content\" style=\"width:200px;height:190px;font-size:10px;font-family:arial;\"></textarea>';
   } else {
      str += '<input type=\"text\" onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" id=\"' + divid + '_content\" style=\"width:200px;font-size:10px;font-family:arial;\">';
   }
   jQuery('#' + divid + '_contentinput').html(str);
   if(Boolean(val)) jQuery('#' + divid + '_content').val(val);
}