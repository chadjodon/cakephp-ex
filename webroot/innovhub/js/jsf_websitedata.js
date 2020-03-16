
//var defaultremotedomain = 'http://www.jstorefront.com/';
//var defaultremotedomain = 'https://www.jigbit.com/';
var defaultremotedomain = 'https://www.plasticsmarkets.org/';
var webdatarow;
var jsfwd_pagenum;
var jsfwd_limit;
var jsfwd_orderby;
var jsfwd_maxcol;
var jsfwd_searchflds='';
var jsfwd_xtraurl;
var jsfwd_prefix;
var jsfwd_total;
var jsfwd_answertotals;
var jsfwd_showfilterbutton=true;
var jsfwd_showsearchstring=true;
var jsfwd_opennewwindow=false;
var jsfwd_adminflag=false;
var jsfwd_showcreaterecord=true;

var jsfwd_mostrecentrows;

var jsfwd_rowid;
var jsfwd_wdid;
var jsfwd_filterstr;

var jsfwd_explicitwidth;
var jsfwd_explicitheight;

// To display a return code (or allow a user to enter a return code
var jsfwd_returninguser=false;
var jsfwd_savehistory=false;

var jsfwd_fulltable;
var jsfwd_fulltable_backup;

var jsfwd_visualuri="";

var jsfwd_userid;
var jsfwd_token;

var statsparams;
var jsfwd_title;

var jsfwd_returntoform=true;
var jsfwd_returnformoverride;

var jsfwd_defaultformfnname = 'jsfwebdata_display';

var jsfwd_testing=false;
var jsfwd_groupby = '';

var jsfwd_servercontroller = 'jsfcode/jsonpcontroller.php?jodon=1';

//----------------------------------------------------------------------------------
// how we will make a jsonp call
//----------------------------------------------------------------------------------
function jsfwebdata_CallJSONP(url,priority) {
   if(jsfwd_testing) alert(jsfwebdata_getDateTime() + ' Requesting json url: ' + url);
   //alert('***chj*** ' + jsfwebdata_getDateTime() + ' Requesting json url: ' + url);
   if (typeof jsf_CallJSONP == 'function') {
      //alert('***chj*** calling jsf_calljsonp instead');
      jsf_CallJSONP(url,priority);
   } else {
      var script = document.createElement('script');
      script.setAttribute('src', url);
      document.getElementsByTagName('head')[0].appendChild(script);
   }
}

function jsfwebdata_CallJSONP_inline(url) {
    var element = '<script language=\"javascript\" type=\"text/javascript\" src=\"' + url + '\"></script>';
    document.writeln(element);
}
 
//----------------------------------------------------------------------------------
// test that js file is in place
//----------------------------------------------------------------------------------
function jsfwebdata_AlertString(str){
   alert(str);
}

//----------------------------------------------------------------------------------
// callback for testing
//----------------------------------------------------------------------------------
function jsfwebdata_AlertJSONPRequest(jsondata){
   if (typeof jsf_endjsoning == 'function') jsf_endjsoning();
   alert(JSON.stringify(jsondata));
}

function jsfwebdata_DoNothing(jsondata){
   if (typeof jsf_endjsoning == 'function') jsf_endjsoning();
   //alert(JSON.stringify(jsondata));
}





//----------------------------------------------------------------------------------
// Search for WD Tables and display the ones that match
// #API
//----------------------------------------------------------------------------------
function jsf_searchforwd(userid,token,searchtxt,prvsrvy,externalid,foruserid,limit,callback){
   if(!Boolean(userid)) userid = jsfwd_userid;
   if(!Boolean(token)) token = jsfwd_token;
   if(!Boolean(limit)) limit = 10;
   if(!Boolean(callback)) callback = 'jsf_searchforwd_return';
   jsfwd_userid = userid;
   jsfwd_token = token;
   var url='';
   url += defaultremotedomain + jsfwd_servercontroller;
   url += '&action=getwebdatatables';
   url += '&callback=' + callback;
   url += '&userid=' + encodeURIComponent(userid);
   if(Boolean(foruserid)) url += '&foruserid=' + encodeURIComponent(foruserid);
   url += '&token=' + encodeURIComponent(token);
   url += '&limit=' + encodeURIComponent(limit);
   if(Boolean(searchtxt)) url += '&searchtxt=' + encodeURIComponent(searchtxt);
   if(Boolean(prvsrvy)) url += '&privatesrvy=' + encodeURIComponent(prvsrvy);
   if(Boolean(externalid)) url += '&externalid=' + encodeURIComponent(externalid);

   if (jsfwd_testing) alert('URL: ' + url);
   //alert('URL: ' + url);

   jsfwebdata_CallJSONP(url);
}

function jsf_searchforwd_return(jsondata) {
   // First Build the HTML
   var str = '';
   str += '<div id=\"jsfwd_tables\">';
   for(var i=0;i<jsondata.results.length;i++) {
      str += '<div id=\"jsfwdtable' + jsondata.results[i].wd_id + '\" class=\"jsfwddisptables\" style=\"display:none;\">';
      str += '<div class=\"jsfwddisptabletitle\">';
      if(Boolean(jsondata.isadmin)) str += '<span onclick=\"window.open(\'' + defaultremotedomain + 'jsfadmin/admincontroller.php?action=wd_listrows&wd_id=' + jsondata.results[i].wd_id + '\');\" style=\"cursor:pointer;\">';
      str += jsondata.results[i].name;
      if(Boolean(jsondata.isadmin)) str += '</span>';
      str += '</div>';
      str += '<div id=\"jsfwdarea' + jsondata.results[i].wd_id + '\">';
      str += '</div>';
      str += '</div>';
   }
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';
   jQuery('#jsfwdarea').html(str);
   
   // Now populate each div
   for(var i=0;i<jsondata.results.length;i++) {
      jsf_getwdtable_jsonp('',jsondata.results[i].wd_id,'',jsfwd_userid,jsfwd_token,'',10,1,6,'','','','','',0,1,jsondata.foruserid);
   }
}




//----------------------------------------------------------------------------------
// using the default callback, make sure there is a page div with the id "jsfwdarea"
// This prints a single record's form with data pre-populated
// #API
//----------------------------------------------------------------------------------
function jsf_getwebdata_jsonp(wdname,domain,callback,wd_id,prefix,userid,wd_row_id,testing,forcefull,msg,xtra){
   if (Boolean(testing)) jsfwd_testing=true;
   if (!Boolean(domain)) domain=defaultremotedomain;
   defaultremotedomain = domain;
   //if (!Boolean(callback)) callback='jsfwebdata_display';
   if (!Boolean(callback)) callback=jsfwd_defaultformfnname;
   if (!Boolean(prefix)) prefix='jsfwd';
   if (!Boolean(forcefull)) forcefull='0';
   if (!Boolean(wd_row_id) && Boolean(webdatarow)) wd_row_id=webdatarow;

   if (jsfwd_testing) alert(defaultremotedomain + ', ' + callback + ', ' + prefix);
   
   var firstdiv = '#' + prefix + 'area';
   var detailsdiv = '#jsfwdareadetails';
   var generaldiv = '#jsfwdarea';
   if(jQuery(detailsdiv).length>0) jQuery(detailsdiv).html('Loading...');
   else if(jQuery(firstdiv).length>0) jQuery(firstdiv).html('Loading...');
   else if(jQuery(generaldiv).length>0) jQuery(generaldiv).html('Loading...');
   
   

   var url = defaultremotedomain + jsfwd_servercontroller + '&action=getwebdata';
   url = url + '&callback=' + encodeURIComponent(callback);
   if (Boolean(wdname)) url = url + '&wdname=' + encodeURIComponent(wdname);
   if (Boolean(prefix)) url = url + '&prefix=' + encodeURIComponent(prefix);
   if (Boolean(userid)) url = url + '&userid=' + encodeURIComponent(userid);
   if (Boolean(wd_id)) url = url + '&wd_id=' + encodeURIComponent(wd_id);
   if (Boolean(wd_row_id)) url = url + '&wd_row_id=' + encodeURIComponent(wd_row_id);
   if (Boolean(forcefull)) url = url + '&forcefull=' + encodeURIComponent(forcefull);
   if (Boolean(msg)) url = url + '&wdmsg=' + encodeURIComponent(msg);
   if (Boolean(jsfwd_adminflag)) url = url + '&admin=1';
   if (Boolean(jsfwd_explicitwidth)) url = url + '&wdwidth=' + encodeURIComponent(jsfwd_explicitwidth);
   if (Boolean(jsfwd_explicitheight)) url = url + '&wdheight=' + encodeURIComponent(jsfwd_explicitheight);
   if (Boolean(xtra)) url = url + xtra;

   if (jsfwd_testing) alert('URL: ' + url);
   //alert('URL: ' + url);

   jsfwebdata_CallJSONP(url);
   //jsfwebdata_CallJSONP_inline(url);
}


function jsf_getwebdata2_jsonp(wdname,domain,callback,wd_id,prefix,userid,wd_row_id,testing,forcefull,msg,xtra){
   if (Boolean(testing)) jsfwd_testing=true;
   if (!Boolean(domain)) domain=defaultremotedomain;
   defaultremotedomain = domain;
   if (!Boolean(callback)) callback='jsfwebdata_display2';
   if (!Boolean(prefix)) prefix='jsfwd';
   if (!Boolean(forcefull)) forcefull='0';
   if (!Boolean(wd_row_id) && Boolean(webdatarow)) wd_row_id=webdatarow;

   if (jsfwd_testing) alert(defaultremotedomain + ', ' + callback + ', ' + prefix);
   
   var firstdiv = '#' + prefix + 'area';
   var detailsdiv = '#jsfwdareadetails';
   var generaldiv = '#jsfwdarea';
   if(jQuery(firstdiv).length>0) jQuery(firstdiv).html('Loading...');
   else if(jQuery(detailsdiv).length>0) jQuery(detailsdiv).html('Loading...');
   else if(jQuery(generaldiv).length>0) jQuery(generaldiv).html('Loading...');
   
   

   var url = defaultremotedomain + jsfwd_servercontroller + '&action=getwebdata';
   url = url + '&callback=' + encodeURIComponent(callback);
   if (Boolean(wdname)) url = url + '&wdname=' + encodeURIComponent(wdname);
   if (Boolean(prefix)) url = url + '&prefix=' + encodeURIComponent(prefix);
   if (Boolean(userid)) url = url + '&userid=' + encodeURIComponent(userid);
   if (Boolean(wd_id)) url = url + '&wd_id=' + encodeURIComponent(wd_id);
   if (Boolean(wd_row_id)) url = url + '&wd_row_id=' + encodeURIComponent(wd_row_id);
   if (Boolean(forcefull)) url = url + '&forcefull=' + encodeURIComponent(forcefull);
   if (Boolean(msg)) url = url + '&wdmsg=' + encodeURIComponent(msg);
   if (Boolean(xtra)) url = url + xtra;

   if (jsfwd_testing) alert('URL: ' + url);
   //alert('URL: ' + url);

   jsfwebdata_CallJSONP(url);
   //jsfwebdata_CallJSONP_inline(url);
}


// No question relationships will be honored with this view
function jsf_getwebdatasimple_jsonp(wdname,domain,callback,wd_id,prefix,userid,wd_row_id,testing,admin){
   if (Boolean(testing)) jsfwd_testing=false;
   if (!Boolean(domain)) domain=defaultremotedomain;
   defaultremotedomain = domain;
   if (!Boolean(callback)) callback='jsfwebdata_display';
   if (!Boolean(prefix)) prefix='jsfwd';
   if (!Boolean(wd_row_id) && Boolean(webdatarow)) wd_row_id=webdatarow;

   if (jsfwd_testing) alert(defaultremotedomain + ', ' + callback + ', ' + prefix);

   var url = defaultremotedomain + jsfwd_servercontroller + '&action=getwebdatasimple';
   url = url + '&callback=' + encodeURIComponent(callback);
   if (Boolean(admin)) url = url + '&admin=1';
   if (Boolean(wdname)) url = url + '&wdname=' + encodeURIComponent(wdname);
   if (Boolean(prefix)) url = url + '&prefix=' + encodeURIComponent(prefix);
   if (Boolean(userid)) url = url + '&userid=' + encodeURIComponent(userid);
   if (Boolean(wd_id)) url = url + '&wd_id=' + encodeURIComponent(wd_id);
   if (Boolean(wd_row_id)) url = url + '&wd_row_id=' + encodeURIComponent(wd_row_id);

   if (jsfwd_testing) alert('URL: ' + url);

   jsfwebdata_CallJSONP(url);
   //jsfwebdata_CallJSONP_inline(url);
}

function jsf_getwebdatapage_jsonp(wdname,domain,callback,wd_id,prefix,userid,wd_row_id,page,testing,noemail,explicitcss,origemail,email){
   if (typeof jsfwd_showloading == 'function') jsfwd_showloading();

   //alert('jsf_getwebdatapage_jsonp: name: ' + wdname + ' wd_id: ' + wd_id);
   if(Boolean(testing)) jsfwd_testing = true;
   if (!Boolean(prefix)) prefix='jsfwd';
   if (!Boolean(email)) email = window.localStorage.getItem(prefix + '_e');
   if (Boolean(jsfwd_testing)) alert('start');
   if (Boolean(jsfwd_fulltable)) {
      //alert('full table, calling url');
      jsfwebdata_display(jsfwd_fulltable);
   } else {
      //alert('not full table, calling url');
      jsfwd_pagenum = 1;
      jsfwd_visualuri='';
      jsfwd_rowid='';
      if (!Boolean(explicitcss)) explicitcss=false;
      //if (!Boolean(testing)) testing=false;
      if (!Boolean(domain)) domain=defaultremotedomain;
      defaultremotedomain = domain;
      if (!Boolean(callback)) callback='jsfwebdata_display';
      if (!Boolean(wd_row_id) && Boolean(webdatarow)) wd_row_id=webdatarow;
      //if (!Boolean(page)) page=1;
   
      if (Boolean(jsfwd_testing)) alert(defaultremotedomain + ', ' + callback + ', ' + prefix);
   
      var url = defaultremotedomain + jsfwd_servercontroller + '&action=getwebdatapage';
      url = url + '&callback=' + encodeURIComponent(callback);
      if (Boolean(wdname)) url = url + '&wdname=' + encodeURIComponent(wdname);
      if (Boolean(prefix)) url = url + '&prefix=' + encodeURIComponent(prefix);
      if (Boolean(userid)) url = url + '&userid=' + encodeURIComponent(userid);
      if (Boolean(wd_id)) url = url + '&wd_id=' + encodeURIComponent(wd_id);
      if (Boolean(page)) url = url + '&page=' + encodeURIComponent(page);
      if (Boolean(wd_row_id)) url = url + '&wd_row_id=' + encodeURIComponent(wd_row_id);
      if (Boolean(origemail)) url = url + '&origemail=' + encodeURIComponent(origemail);
      if (Boolean(email)) url = url + '&email=' + encodeURIComponent(email);
      //if (testing) url = url + '&testing=1';
      if (noemail) url = url + '&noemail=1';
      if (explicitcss) url = url + '&explicitcss=1';
   
      if (Boolean(jsfwd_testing)) alert('URL: ' + url);
      //alert('getwebdatapage URL: ' + url);
   
      jsfwebdata_CallJSONP(url);
      //jsfwebdata_CallJSONP_inline(url);
   }
}

// This will save any entry on this computer so a person can come back and return
// to a spot where they left off.
function jsf_getwebdatapagereturn_jsonp(wd_id,domain,callback,prefix,userid,wd_row_id,page,testing,noemail,explicitcss,origemail,email){
   if (Boolean(testing)) jsfwd_testing=true;
   if(jsfwd_testing) alert('start');
   
   if(!Boolean(wd_row_id)){
      if (typeof(window.localStorage)!='undefined') {
         wd_row_id = window.localStorage.getItem(wd_id + '_wri');
         origemail = window.localStorage.getItem(wd_id + '_oe');
         email = window.localStorage.getItem(wd_id + '_e');
      }
   } else if (Boolean(origemail)) {
      if (typeof(window.localStorage)!='undefined') {
         window.localStorage.setItem(wd_id + '_wri',wd_row_id);
         window.localStorage.setItem(wd_id + '_oe',origemail);
         window.localStorage.setItem(wd_id + '_e',email);
      }
   }
   
   if(Boolean(jsfwd_savehistory)) {
      var stateObj = {};
      stateObj.page= page;
      stateObj.userid= userid;
      stateObj.wd_id= wd_id;
      stateObj.wd_row_id= wd_row_id;
      stateObj.origemail= origemail;
      stateObj.email= email;
      var hurl = '';
      if(Boolean(wd_id) && Boolean(wd_row_id) && Boolean(origemail) && Boolean(email) && Boolean(page)) hurl = '/survey/' + wd_id + '/' + wd_row_id + '/' + origemail + '/' + page + '/' + email;
      else hurl = '/survey/' + wd_id;
      window.history.pushState(stateObj,'Page ' + page,hurl);
   }
   
   jsf_getwebdatapage_jsonp('',domain,callback,wd_id,prefix,userid,wd_row_id,page,testing,noemail,explicitcss,origemail,email);
}

function jsf_getwebdataspa_jsonp(wdname,domain,callback,prefix,userid,page){
      jsfwd_pagenum = 1;
      if (!Boolean(domain)) domain=defaultremotedomain;
      defaultremotedomain = domain;
      if (!Boolean(callback)) callback='jsfwebdata_spadisplay';
      if (!Boolean(prefix)) prefix='jsfwd';
      if (!Boolean(page)) page=1;
   
      var url = defaultremotedomain + jsfwd_servercontroller + '&action=getwebdataspa';
      url = url + '&callback=' + encodeURIComponent(callback);
      if (Boolean(wdname)) url = url + '&wdname=' + encodeURIComponent(wdname);
      if (Boolean(prefix)) url = url + '&prefix=' + encodeURIComponent(prefix);
      if (Boolean(userid)) url = url + '&userid=' + encodeURIComponent(userid);
      if (Boolean(page)) url = url + '&page=' + encodeURIComponent(page);
      jsfwebdata_CallJSONP(url);
}

//----------------------------------------------------------------------------------
// default callback to display a jsf webdata form entry
// make sure there is a page div with the id "jsfwdarea" to display the form
//----------------------------------------------------------------------------------


// return function for displaying form
function jsfwebdata_display(jsondata){
   //alert('jsfwebdata_dispay jsondata: ' + JSON.stringify(jsondata.privatesrvy));
   if (typeof jsf_endjsoning == 'function') jsf_endjsoning(jsondata);
   if (typeof jsfwd_hideloading == 'function') {
      jsfwd_hideloading();
   }

   var showsurvey = true;
   
   var str = '';
   //if(Boolean(jsondata.page)) str += jsondata.page + ", " + jsondata.privatesrvy + ", " + jsondata.pages;
   if (Boolean(jsondata.wdmsg)) str += jsondata.wdmsg;
   if(Boolean(jsondata.privatesrvy) && jsondata.privatesrvy==1) {
      if(Boolean(jsondata.page) && Boolean(jsondata.pages) && jsondata.page!=-1 && jsondata.pages>1) {
         str += '<div id=\"' + jsondata.prefix + 'wdpaging\" style=\"position:relative;margin:3px;font-size:10px;color:#999999;font-family:arial;\">';
         str += '<div style=\"float:left;\">Page: </div>';
         var addon = 0;
         if (Boolean(jsondata.hasuserid)) {
            addon = 1;
            str += '<div style=\"float:left;margin-left:2px;width:14px;height:14px;text-align:center;';
            if(jsondata.page == -2) str += 'border:1px solid #DDDDDD;border-radius:7px;\"';
            else str += 'border:1px solid #FFFFFF;border-radius:7px;cursor:pointer;\" onclick=\"jQuery(\'#' + jsondata.prefix + 'wdpaging\').html(\'Loading...\');jsfwdSubmitWDForm(-2);\"';
            str += '>1</div>'; 
         }
         for(var i=1;i<=jsondata.pages;i++) {
            str += '<div style=\"float:left;margin-left:2px;width:14px;height:14px;text-align:center;';
            if(i==jsondata.page) str += 'border:1px solid #DDDDDD;border-radius:7px;\"';
            else str += 'border:1px solid #FFFFFF;border-radius:7px;cursor:pointer;\" onclick=\"jQuery(\'#' + jsondata.prefix + 'wdpaging\').html(\'Loading...\');jsfwdSubmitWDForm(' + i + ');\"';
            str += '>' + (i + addon) + '</div>'; 
         }
         str += '<div onclick=\"jQuery(\'#' + jsondata.prefix + 'wdpaging\').html(\'Loading...\');jsfwdSubmitWDForm(-1);\" style=\"float:right;cursor:pointer;\">Save and Log Out</div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
      }
   } else if(jsfwd_returninguser){
      if(Boolean(jsondata.origemail)) {
         str += '<div style=\"margin:3px;text-align:right;font-size:10px;color:#999999;font-family:arial;\">';
         str += 'Save this return code if you would like to finish later: <b>' + jsondata.origemail + '</b>';
         str += '</div>';
      } else {
         str += '<div style=\"margin:3px 3px 2px 3px;text-align:right;font-size:12px;color:#777777;font-family:arial;\">';
         str += 'Enter your secure return code to reload your information ';
         str += '<input id=\"jsfwd_origemailinput\" type=\"text\" style=\"margin-left:3px;font-size:10px;width:110px;font-family:arial;\">';
         str += '<span ';
         str += 'id=\"jsfwd_origemailbutton\" ';
         str += 'onclick=\"jQuery(\'#jsfwd_origemailbutton\').html(\'Loading...\');jsfwd_enteruserorigemail(\'' + jsondata.prefix + '\',\'' + jsondata.wd_id + '\',jQuery(\'#jsfwd_origemailinput\').val());\" ';
         str += 'style=\"margin-left:3px;font-size:10px;width:110px;font-family:arial;padding:3px;border-radius:3px;border:1px solid #333333;background-color:#CCCCCC;cursor:pointer;\">Load</span>';
         str += '</div>';
         str += '<div style=\"margin:0px 3px 1px 3px;text-align:right;font-size:10px;color:#FF7777;font-family:arial;\">';
         str += 'Your unique return code will be generated on the next page.';
         str += '</div>';         
      }
   }
   
   str = str + jsondata.html + '\n';
   str = str + '<script type="text/javascript">\n';
   str = str + jsondata.js + '\n';
   if (Boolean(jsondata.populatejs)) str = str + jsondata.populatejs + '\n';
   if (Boolean(jsondata.relationshipjs1)) str = str + jsondata.relationshipjs1 + '\n';
   if (Boolean(jsondata.relationshipjs2)) str = str + jsondata.relationshipjs2 + '\n';
   str = str + jsondata.prefix + 'CheckWDRelationships();\n';
   str = str + '</script>\n';
   
   var detailsdiv = '#jsfwdareadetails';
   var generaldiv = '#jsfwdarea';
   if(jQuery(detailsdiv).length>0) jQuery(detailsdiv).html(str);
   else if(jQuery(generaldiv).length>0) jQuery(generaldiv).html(str);
   else alert('Internal error occurred.');
   
   window.scrollTo(0,0);
   if (Boolean(jsondata.privatesrvy) && jsondata.privatesrvy==7) {
      jsfwd_prefix = jsondata.prefix;
      jsfwd_total = jsondata.total;
      jsfwd_answertotals = jsondata.answertotals;
      jsfwebdata_visualresize(jsfwd_prefix,jsfwd_total,jsfwd_answertotals);
      jsfwd_fulltable = jsondata;
      jsfwd_fulltable_backup = jsondata;
      jsfwd_vis_clearall();
      jQuery('#' + jsfwd_prefix + '_wdvis_pg' + (jsfwd_pagenum-1)).show();

      //$(window).off('resize');
      //$(window).resize(function() {jsfwebdata_visualresize(jsfwd_prefix,jsfwd_total,jsfwd_answertotals);});
   }
   if (typeof jsfwd_enddisplaysurvey == 'function') jsfwd_enddisplaysurvey(jsondata);
   if (typeof(historyscr) !== 'undefined' && Boolean(historyscr) && Boolean(historycnt) && typeof show_edittable == 'function') {
      historyscr[historycnt] = 'edittable';
      historycnt++;   	 
   }

}

function jsfwebdata_display2(jsondata){
   //alert('jsfwebdata_dispay jsondata: ' + JSON.stringify(jsondata.privatesrvy));
   if (typeof jsf_endjsoning == 'function') jsf_endjsoning(jsondata);
   if (typeof jsfwd_hideloading == 'function') {
      jsfwd_hideloading();
   }

   var showsurvey = true;
   
   var str = '';
   //if(Boolean(jsondata.page)) str += jsondata.page + ", " + jsondata.privatesrvy + ", " + jsondata.pages;
   if (Boolean(jsondata.wdmsg)) str += jsondata.wdmsg;
   
   str = str + jsondata.html + '\n';
   str = str + '<script type="text/javascript">\n';
   str = str + jsondata.js + '\n';
   if (Boolean(jsondata.populatejs)) str = str + jsondata.populatejs + '\n';
   if (Boolean(jsondata.relationshipjs1)) str = str + jsondata.relationshipjs1 + '\n';
   if (Boolean(jsondata.relationshipjs2)) str = str + jsondata.relationshipjs2 + '\n';
   str = str + jsondata.prefix + 'CheckWDRelationships();\n';
   str = str + '</script>\n';
   
   var firstdiv = '#' + jsondata.prefix + 'area';
   var detailsdiv = '#jsfwdareadetails';
   var generaldiv = '#jsfwdarea';
   if(jQuery(firstdiv).length>0) jQuery(firstdiv).html(str);
   else if(jQuery(detailsdiv).length>0) jQuery(detailsdiv).html(str);
   else if(jQuery(generaldiv).length>0) jQuery(generaldiv).html(str);
   else alert('Internal error occurred.');
   
   window.scrollTo(0,0);
}

function jsfwd_enteruserorigemail(prefix,wd_id,origemail){
      var url = defaultremotedomain + jsfwd_servercontroller + '&action=retrievesecurewdrow';
      url = url + '&callback=' + encodeURIComponent('jsfwd_enteruserorigemail_return');
      if (Boolean(wd_id)) url = url + '&wd_id=' + encodeURIComponent(wd_id);
      if (Boolean(origemail)) url = url + '&origemail=' + encodeURIComponent(origemail);
      if (Boolean(prefix)) url = url + '&prefix=' + encodeURIComponent(prefix);
      //alert('url: ' + url);
      jsfwebdata_CallJSONP(url);
}

function jsfwd_enteruserorigemail_return(jsondata){
   if (typeof jsf_endjsoning == 'function') jsf_endjsoning();
   jsf_getwebdatapage_jsonp('','','',jsondata.wd_id,jsondata.prefix,'',jsondata.row.wd_row_id,'','','','',jsondata.row.origemail);   
}

function jsfwebdata_spadisplay(jsondata){
   //alert('jsfwebdata_dispay jsondata: ' + JSON.stringify(jsondata));
   if (typeof jsf_endjsoning == 'function') jsf_endjsoning();
   
   var str = '';
   if (Boolean(jsondata.wdmsg)) str = str + jsondata.wdmsg;
   str = str + jsondata.html + '\n';
   str = str + '<script type="text/javascript">\n';
   str = str + jsondata.js + '\n';
   if (Boolean(jsondata.populatejs)) str = str + jsondata.populatejs + '\n';
   if (Boolean(jsondata.relationshipjs1)) str = str + jsondata.relationshipjs1 + '\n';
   if (Boolean(jsondata.relationshipjs2)) str = str + jsondata.relationshipjs2 + '\n';
   str = str + jsondata.prefix + 'InitializeSPAForm();\n';
   str = str + '</script>\n';
   jQuery('#jsfwdarea').html(str);
   //jQuery('#jsfwdarea').scrollTop(0);
   window.scrollTo(0,0);
   if (Boolean(jsondata.privatesrvy) && jsondata.privatesrvy==7) {
      jsfwd_prefix = jsondata.prefix;
      jsfwd_total = jsondata.total;
      jsfwd_answertotals = jsondata.answertotals;
      jsfwebdata_visualresize(jsfwd_prefix,jsfwd_total,jsfwd_answertotals);
      jsfwd_fulltable = jsondata;
      jsfwd_fulltable_backup = jsondata;
      jsfwd_vis_clearall();
      jQuery('#' + jsfwd_prefix + '_wdvis_pg' + (jsfwd_pagenum-1)).show();

      //$(window).off('resize');
      //$(window).resize(function() {jsfwebdata_visualresize(jsfwd_prefix,jsfwd_total,jsfwd_answertotals);});
   }
   if (typeof jsfwd_enddisplaysurvey == 'function') jsfwd_enddisplaysurvey();
   if (typeof(historyscr) !== 'undefined' && Boolean(historyscr) && Boolean(historycnt) && typeof show_edittable == 'function') {
      historyscr[historycnt] = 'edittable';
      historycnt++;   	 
   }

}

function jsfwd_uploadfinished(val,wd_id,field_id){
	 jQuery('#jsfwdinputw' + wd_id + 'a' + field_id).val(val);
	 jQuery('#imgupl_w' + wd_id + 'q' + field_id).html('<img src=\"' + val + '\" style=\"width:80px;height:auto;\">');
}

function jsfwebdata_visualresize(prefix,total,answertotals){
   var pf = prefix + '_wdvis';

   var wd_winwidth = $(window).width();
   var wd_winheight = $(window).height();

   var wd_curwidth = wd_winwidth;
   var wd_curheight = wd_winheight;

   var wd_padding = 32;
   var wd_title = 28;
   var wd_text = 20;
   var wd_radius = 16;
   var wd_maxacross = 6;
   if (wd_curwidth<760 || wd_curheight<420) {
      wd_padding = 24;
      wd_title = 22;
      wd_text = 18;
      wd_radius = 12;
   }
   if (wd_curwidth<500 || wd_curheight<250) {
      wd_padding = 16;
      wd_title = 16;
      wd_text = 14;
      wd_radius = 8;
   }
   if (Math.round(1.99*wd_curwidth) < wd_curheight) {
      wd_maxacross = 2;
   } else if (Math.round(1.5*wd_curwidth) < wd_curheight) {
      wd_maxacross = 3;
   } else if (wd_curwidth < wd_curheight) {
      wd_maxacross = 4;
   }
   //alert('win width: ' + wd_winwidth + ', height: ' + wd_winheight + ', padding: ' + wd_padding + ', title: ' + wd_title + ', text: ' + wd_text); 
   jQuery('#jsfwdarea').css('position','relative').css('width',wd_curwidth + 'px').css('height',wd_curheight + 'px').css('overflow-x','hidden').css('overflow-y','auto');
   //jQuery('#jsfwdarea').css('background-image','URL(/jsfimages/wood_grey.jpg)');
   //jQuery('#jsfwdarea').css('background-color','#CDCDCD');

   wd_curwidth = wd_curwidth - (wd_padding * 2);
   wd_curheight = wd_curheight - (wd_padding * 2);

   jQuery('.' + pf).css('position','relative').css('width',wd_curwidth + 'px').css('height',wd_curheight + 'px');
   jQuery('.' + pf).css('overflow','hidden').css('padding',wd_padding + 'px');

   jQuery('.' + pf + '_top').css('position','relative').css('width',wd_curwidth + 'px').css('height','1px').css('overflow','hidden');

   wd_curheight = wd_curheight - 1;

   var qht = ((wd_title + 2)*2);
   jQuery('.' + pf + '_q').css('position','relative').css('width',wd_curwidth + 'px').css('height',qht + 'px');
   jQuery('.' + pf + '_q').css('font-size',wd_title + 'px').css('font-family','verdana').css('font-weight','bold');
   jQuery('.' + pf + '_q').css('margin-bottom',wd_padding + 'px');

   jQuery('.' + pf + '_addl').css('margin-top','50px').css('font-size','14px').css('font-family','verdana').css('color','#333333');
   jQuery('.' + pf + '_refresh').css('margin-top','100px').css('font-size','12px').css('font-family','verdana').css('color','#3333DD').css('cursor','pointer');

   wd_curheight = wd_curheight - qht - wd_padding;

   jQuery('.' + pf + '_a').css('position','relative').css('width',wd_curwidth + 'px').css('height',wd_curheight + 'px');
   jQuery('.' + pf + '_finished').css('clear','both');

   var sm_padding;
   var sm_across;
   var sm_vert;
   var sm_a1;
   var sm_v1;
   var sm_v1_padding;
   var sm_v1_internal;

   var sm_yno_a1;
   var sm_yno_v1;
   var sm_yno_a1_padding;
   var sm_yno_a1_internal;
   var sm_yno_v2;
   var sm_yno_a2;
   var sm_yno_a2_padding;
   var sm_yno_a2_internal;
   
   var sm_num_padding;
   var sm_num_across;
   var sm_num_vert;
   var sm_num_a1;
   var sm_num_v1;
   var sm_num_a1_padding;
   var sm_num_a1_internal;
   
   var totalanswerwidth;
   
   jQuery('.' + prefix + 'answersdiv').css('margin-left','auto').css('margin-right','auto');

   for (var i=0; i<total; i++) {      
      sm_padding = Math.round(wd_padding/2);
      sm_vert = Math.ceil(answertotals[i] / wd_maxacross);
      sm_across = Math.ceil(answertotals[i]/sm_vert);
      sm_a1 = Math.floor(wd_curwidth / sm_across) - sm_padding - 4;
      sm_v1 = Math.floor(wd_curheight / sm_vert) - sm_padding - 4;

      if (sm_a1 > (2 * sm_v1)) sm_a1 = 2 * sm_v1;
      else if (sm_a1 < sm_v1) sm_v1 = sm_a1;

      sm_v1_padding = Math.floor((sm_v1 - (wd_text+2))/2);
      sm_v1_internal = sm_v1 - sm_v1_padding;
   
      jQuery('.' + pf + '_xxx' + i + '_outer').css('position','relative').css('float','left').css('width',sm_a1 + 'px').css('height',sm_v1 + 'px');
      jQuery('.' + pf + '_xxx' + i + '_outer').css('margin-right',sm_padding + 'px').css('margin-bottom',sm_padding + 'px');
      jQuery('.' + pf + '_xxx' + i + '_choice').css('position','absolute').css('left','0px').css('top','0px').css('width',sm_a1 + 'px').css('height',(sm_v1 - 4) + 'px');
      jQuery('.' + pf + '_xxx' + i + '_choice').css('border','2px solid #444444').css('cursor','pointer').css('border-radius',wd_radius + 'px');
      jQuery('.' + pf + '_xxx' + i + '_name').css('position','absolute').css('left','0px').css('top','0px').css('width',sm_a1 + 'px').css('height',sm_v1_internal + 'px');
      jQuery('.' + pf + '_xxx' + i + '_name').css('padding-top',sm_v1_padding + 'px').css('text-align','center');
      jQuery('.' + pf + '_xxx' + i + '_name').css('font-size',wd_text + 'px').css('font-family','verdana').css('font-weight','bold').css('color','#FFFFFF');


      sm_yno_a1 = Math.floor(wd_curwidth / 2) - wd_padding - 4;
      sm_yno_v1 = 2 * (Math.floor(wd_curheight / 3) - wd_padding - 4);
      if (sm_yno_a1 > sm_yno_v1) sm_yno_a1 = sm_yno_v1;
      sm_yno_a1_padding = Math.floor((sm_yno_a1 - wd_text)/2);
      sm_yno_a1_internal = sm_yno_a1 - sm_yno_a1_padding;

      sm_yno_v2 = sm_yno_a1 - 10;
      sm_yno_a2 = (sm_yno_a1 * 2) + wd_padding;
      if (sm_yno_v2 > 40) sm_yno_v2 = 40;
      sm_yno_a2_padding = Math.floor((sm_yno_v2 - wd_text - 4)/2);
      sm_yno_a2_internal = sm_yno_v2 - sm_yno_a2_padding;

      jQuery('#' + pf + '_yno' + i + '_outer0').css('position','relative').css('float','left');
      jQuery('#' + pf + '_yno' + i + '_outer0').css('margin-right',wd_padding + 'px').css('margin-bottom',wd_padding + 'px');
      jQuery('#' + pf + '_yno' + i + '_outer0').css('height',sm_yno_a1 + 'px').css('width',sm_yno_a1 + 'px');
      jQuery('#' + pf + '_yno' + i + '_choice0').css('height',sm_yno_a1 + 'px').css('width',sm_yno_a1 + 'px').css('border-radius',sm_yno_a1 + 'px');
      jQuery('#' + pf + '_yno' + i + '_choice0').css('position','absolute').css('left','0px').css('top','0px').css('width',sm_yno_a1 + 'px').css('height',sm_yno_a1 + 'px');
      jQuery('#' + pf + '_yno' + i + '_choice0').css('border','2px solid #444444');
      jQuery('.' + pf + '_yno' + i + '_choice').css('cursor','pointer');
      jQuery('#' + pf + '_yno' + i + '_name0').css('position','absolute').css('left','0px').css('top','0px').css('width',sm_yno_a1 + 'px').css('height',sm_yno_a1_internal + 'px');
      jQuery('#' + pf + '_yno' + i + '_name0').css('padding-top',sm_yno_a1_padding + 'px').css('text-align','center');
      jQuery('#' + pf + '_yno' + i + '_name0').css('font-size',wd_text + 'px').css('font-family','verdana').css('font-weight','bold').css('color','#FFFFFF');
      jQuery('#' + pf + '_yno' + i + '_outer1').css('position','relative').css('float','left');
      jQuery('#' + pf + '_yno' + i + '_outer1').css('margin-right',wd_padding + 'px').css('margin-bottom',wd_padding + 'px');
      jQuery('#' + pf + '_yno' + i + '_outer1').css('height',sm_yno_a1 + 'px').css('width',sm_yno_a1 + 'px');
      jQuery('#' + pf + '_yno' + i + '_choice1').css('height',sm_yno_a1 + 'px').css('width',sm_yno_a1 + 'px').css('border-radius',sm_yno_a1 + 'px');
      jQuery('#' + pf + '_yno' + i + '_choice1').css('position','absolute').css('left','0px').css('top','0px').css('width',sm_yno_a1 + 'px').css('height',sm_yno_a1 + 'px');
      jQuery('#' + pf + '_yno' + i + '_choice1').css('border','2px solid #444444');
      jQuery('#' + pf + '_yno' + i + '_name1').css('position','absolute').css('left','0px').css('top','0px').css('width',sm_yno_a1 + 'px').css('height',sm_yno_a1_internal + 'px');
      jQuery('#' + pf + '_yno' + i + '_name1').css('padding-top',sm_yno_a1_padding + 'px').css('text-align','center');
      jQuery('#' + pf + '_yno' + i + '_name1').css('font-size',wd_text + 'px').css('font-family','verdana').css('font-weight','bold').css('color','#FFFFFF');
      jQuery('#' + pf + '_yno' + i + '_outer2').css('position','relative').css('clear','both');
      jQuery('#' + pf + '_yno' + i + '_outer2').css('margin-right',wd_padding + 'px').css('margin-bottom',wd_padding + 'px').css('margin-top',wd_padding + 'px');
      jQuery('#' + pf + '_yno' + i + '_outer2').css('height',sm_yno_v2 + 'px').css('width',sm_yno_a2 + 'px');
      jQuery('#' + pf + '_yno' + i + '_choice2').css('height',sm_yno_v2 + 'px').css('width',sm_yno_a2 + 'px').css('border-radius',wd_radius + 'px');
      jQuery('#' + pf + '_yno' + i + '_choice2').css('position','absolute').css('left','0px').css('top','0px').css('width',sm_yno_a2 + 'px').css('height',sm_yno_v2 + 'px');
      jQuery('#' + pf + '_yno' + i + '_choice2').css('border','1px solid #606060');
      jQuery('#' + pf + '_yno' + i + '_name2').css('position','absolute').css('left','0px').css('top','0px').css('width',sm_yno_a2 + 'px').css('height',sm_yno_v2 + 'px');
      jQuery('#' + pf + '_yno' + i + '_name2').css('padding-top',sm_yno_a2_padding + 'px').css('text-align','center');
      jQuery('#' + pf + '_yno' + i + '_name2').css('font-size',(wd_text - 4) + 'px').css('font-family','verdana').css('font-weight','normal').css('color','#FFFFFF');


      sm_num_padding = Math.round(wd_padding/3);
      sm_num_across = answertotals[i];
      sm_num_vert = 1;
      sm_num_a1 = Math.floor(wd_curwidth / sm_num_across) - sm_num_padding - 2;
      sm_num_v1 = Math.floor(wd_curheight / sm_num_vert) - sm_num_padding - 2;
      if (sm_num_a1 > sm_num_v1) sm_num_a1 = sm_num_v1;
      if (sm_num_a1 > 90) sm_num_a1 = 90;
      
      var num_txtsize = wd_text - 4;
      if (sm_num_a1>=80) num_txtsize = wd_text + 4;
      else if (sm_num_a1>=60) num_txtsize = wd_text + 2;
      else if (sm_num_a1>=50) num_txtsize = wd_text;
      else if (sm_num_a1>=35) num_txtsize = wd_text - 2;

      sm_num_a1_padding = Math.floor((sm_num_a1 - num_txtsize)/2);
      sm_num_a1_internal = sm_num_a1 - sm_num_a1_padding;

      jQuery('.' + pf + '_num' + i + '_outer').css('position','relative').css('float','left').css('width',sm_num_a1 + 'px').css('height',sm_num_a1 + 'px');
      jQuery('.' + pf + '_num' + i + '_outer').css('margin-right',sm_num_padding + 'px').css('margin-bottom',sm_num_padding + 'px');
      jQuery('.' + pf + '_num' + i + '_choice').css('position','absolute').css('left','0px').css('top','0px').css('width',sm_num_a1 + 'px').css('height',sm_num_a1 + 'px');
      jQuery('.' + pf + '_num' + i + '_choice').css('border','1px solid #444444').css('border-radius',sm_num_a1 + 'px');
      jQuery('.' + pf + '_num' + i + '_choice').css('background-color','#cebd2d').css('cursor','pointer');
      jQuery('.' + pf + '_num' + i + '_name').css('position','absolute').css('left','0px').css('top','0px').css('width',sm_num_a1 + 'px').css('height',sm_num_a1_internal + 'px');
      jQuery('.' + pf + '_num' + i + '_name').css('padding-top',sm_num_a1_padding + 'px').css('text-align','center');
      jQuery('.' + pf + '_num' + i + '_name').css('font-size',num_txtsize + 'px').css('font-family','verdana').css('font-weight','bold').css('color','#FFFFFF');

      jQuery('.' + pf + '_pic' + i + '_outer').css('position','relative').css('float','left').css('width',sm_a1 + 'px').css('height',sm_v1 + 'px');
      jQuery('.' + pf + '_pic' + i + '_outer').css('margin-right',sm_padding + 'px').css('margin-bottom',sm_padding + 'px');
      jQuery('.' + pf + '_pic' + i + '_img').css('position','absolute').css('left','0px').css('top','0px').css('max-width',sm_a1 + 'px').css('max-height',(sm_v1 - 4) + 'px');
      jQuery('.' + pf + '_pic' + i + '_img').css('width','auto').css('height','auto').css('cursor','pointer').css('border-radius',Math.round(wd_radius/2) + 'px');

      sm_num_padding = Math.round(wd_padding/3);
      sm_num_vert = Math.ceil(answertotals[i]/(wd_maxacross + 2));
      sm_num_across = Math.ceil(answertotals[i]/sm_num_vert);
      sm_num_a1 = Math.floor(wd_curwidth / sm_num_across) - sm_num_padding - 2;
      sm_num_v1 = Math.floor(wd_curheight / sm_num_vert) - sm_num_padding - 2;
      if (sm_num_a1 > 200) sm_num_a1 = 200;
      if (sm_num_a1 < sm_num_v1) sm_num_v1 = sm_num_a1;
      sm_num_a1_padding = Math.floor((sm_num_a1 - (wd_text-4))/2);
      sm_num_a1_internal = sm_num_a1 - sm_num_a1_padding;
      jQuery('.' + pf + '_sml' + i + '_outer').css('position','relative').css('float','left').css('width',sm_num_a1 + 'px').css('height',sm_num_v1 + 'px');
      jQuery('.' + pf + '_sml' + i + '_outer').css('margin-right',sm_num_padding + 'px').css('margin-bottom',sm_num_padding + 'px');
      jQuery('.' + pf + '_sml' + i + '_choice').css('position','absolute').css('left','0px').css('top','0px').css('width',sm_num_a1 + 'px').css('height',sm_num_v1 + 'px');
      jQuery('.' + pf + '_sml' + i + '_choice').css('border','1px solid #444444').css('cursor','pointer').css('border-radius',wd_radius + 'px');
      jQuery('.' + pf + '_sml' + i + '_name').css('position','absolute').css('left','0px').css('top','0px').css('width',sm_num_a1 + 'px').css('height',sm_num_a1_internal + 'px');
      jQuery('.' + pf + '_sml' + i + '_name').css('padding-top',sm_num_a1_padding + 'px').css('text-align','center');
      jQuery('.' + pf + '_sml' + i + '_name').css('font-size',wd_text + 'px').css('font-family','verdana').css('font-weight','bold').css('color','#FFFFFF');
   }






}





function jsfwebdata_trackitem(domain,callback,view,tr1,tr2,tr3,testing){
   if (!Boolean(callback)) callback='jsfwebdata_DoNothing';
   if (Boolean(testing)) jsfwd_testing=true;
   if (!Boolean(domain)) domain=defaultremotedomain;

   if (jsfwd_testing) alert(defaultremotedomain + ', ' + callback);

   var rnd = Math.floor(Math.random()*10000);
   var validate = jsfwebdata_encrypt(rnd.toString(),'9352');
   
   var url = defaultremotedomain + jsfwd_servercontroller + '&callback=' + callback;
   url = url + '&action=jsontrackitem';
   url = url + '&rnd=' + rnd;
   url = url + '&sessionid=9352';
   url = url + '&validate=' + encodeURIComponent(validate);
   if (Boolean(tr1)) url = url + '&jsftrack1=' + encodeURIComponent(tr1);
   if (Boolean(tr2)) url = url + '&jsftrack2=' + encodeURIComponent(tr2);
   if (Boolean(tr3)) url = url + '&jsftrack3=' + encodeURIComponent(tr3);
   if (Boolean(view)) url = url + '&view=' + view;

   if (jsfwd_testing) alert('URL: ' + url);
   
   jsfwebdata_CallJSONP(url);
}

function jsfwebdata_encrypt(key,text){
   var resultStr = '';
   var currPos = 0;
   for (var i=0; i<text.length; i++) {
      currPos = i % key.length;
      currKey = key.charCodeAt(currPos);
      currChr = text.charCodeAt(i);
      var resultChr = currKey ^ currChr;
      var resultChrStr = '';
      if (resultChr<10) resultChrStr = '0' + resultChr;
      else resultChrStr = resultChr.toString();
      resultStr = resultStr + resultChrStr + ',';
   }
   return resultStr;
}






//----------------------------------------------------------------------------------
// default callback to submit a jsf webdata
// this assumes a prefix of "jsfwd" and the default callback name for submitting
// make sure there is a page div with the id "jsfwdarea" to display the form
//----------------------------------------------------------------------------------
var jsfwebdata_urls = [];
function jsfwdcallback(jsondata){
   if (typeof jsf_endjsoning == 'function') jsf_endjsoning();
   //alert('jsfwdcallback jsondata: ' + JSON.stringify(jsondata));
   //alert('***chj*** In jsfwdcallback.');
   if (jsondata.responsecode==1) {
      if (typeof(window.localStorage)!='undefined') {
         window.localStorage.setItem(jsondata.wd_id + '_wri',jsondata.wd_row_id);
         window.localStorage.setItem(jsondata.wd_id + '_oe',jsondata.origemail);
         window.localStorage.setItem(jsondata.wd_id + '_e',jsondata.email);
      }
   }
      
   if (jsondata.responsecode==1 && jsfwebdata_urls.length>0) {
      var url = jsfwebdata_urls.shift();
      //alert('***chj*** In jsfwdcallback.  New length:' + jsfwebdata_urls.length + '  Calling: ' + url);
      jsfwebdata_CallJSONP(url);
   } else {
      //alert('***chj*** In jsfwdcallback.  No more urls to call!  Yay');
      var str = '';
      if (jsondata.responsecode==1) {
         str = str + '<div style=\"padding:10px;color:green;font-size:16px;font-family:arial;\">';
         str = str + 'Your data was successfully submitted. Thank you!';
         str = str + '</div>';
         if (typeof jsfwdcallback_end == 'function') jsfwdcallback_end(jsondata);
      } else {
         str = str + '<div style=\"padding:10px;color:red;font-size:16px;font-family:arial;\">';
         str = str + jsondata.error;
         str = str + '</div>';
      }
      
      if(jsfwd_returntoform) { 
         jsf_getwebdata_jsonp(jsondata.wdname,'','',jsondata.wd_id,'',jsondata.userid,jsondata.wd_row_id,'',1,str);      
      } else if(Boolean(jsfwd_returnformoverride)) {
         var myFunc = window[jsfwd_returnformoverride];
         myFunc();
      } else {
         jQuery('#jsfwdarea').html(str);
      }
   }
}

function jsfwdorgcallback(jsondata){
   //alert('***chj*** In jsfwdcallback.  No more urls to call!  Yay');
   var str = '';
   if (jsondata.responsecode==1) {
      str = str + '<div style=\"padding:10px;color:green;font-size:16px;font-family:arial;\">';
      str = str + 'Your data was successfully submitted. Thank you!';
      str = str + '</div>';
      if (typeof jsfwdcallback_end == 'function') jsfwdcallback_end(jsondata);
   } else {
      str = str + '<div style=\"padding:10px;color:red;font-size:16px;font-family:arial;\">';
      str = str + jsondata.error;
      str = str + '</div>';
   }
   
   jsf_getwebdata2_jsonp(jsondata.wdname,'','',jsondata.wd_id,jsondata.prefix,jsondata.userid,jsondata.wd_row_id,'',1,str);      
}

function jsfwdpagedcallback(jsondata){
   if (typeof jsf_endjsoning == 'function') jsf_endjsoning(jsondata);
   if (Boolean(jsondata.newrecord) && jsondata.newrecord==1 && typeof jsfwdnewrecord == 'function') jsfwdnewrecord(jsondata);
   //alert(JSON.stringify(jsondata));
   
   if (jsondata.responsecode==1 && jsfwebdata_urls.length>0) {
      var url = jsfwebdata_urls.shift();
      jsfwebdata_CallJSONP(url);
   } else {  
      var str = '';
      if (jsondata.responsecode==1) {
         if (typeof(window.localStorage)!='undefined') {
            window.localStorage.setItem(jsondata.wd_id + '_wri',jsondata.wd_row_id);
            window.localStorage.setItem(jsondata.wd_id + '_oe',jsondata.origemail);
            window.localStorage.setItem(jsondata.wd_id + '_e',jsondata.email);
         }
         
         if (jsondata.nextpg==-1) {
            str = str + '<div id=\"jsfwd_finalmessage\" style=\"padding:10px;color:green;font-size:16px;font-family:arial;\">';
            str = str + 'Your data was successfully submitted. Thank you!';
            str = str + '</div>';
            jQuery('#jsfwdarea').html(str);
            jsfwebdata_SetCookie('jsf_wd_w' + jsondata.wd_id,jsondata.wd_row_id);
            
            jsfpoll_jsfwdpagedcallback_end(jsondata);
            if (typeof jsfwdpagedcallback_end == 'function') jsfwdpagedcallback_end(jsondata);
         } else {
            var testing = false;
            var noemail = false;
            var explicitcss = false;
            if (Boolean(jsondata.testing) && jsondata.testing==1) testing=true;
            if (Boolean(jsondata.noemail) && jsondata.noemail==1) noemail=true;
            if (Boolean(jsondata.explicitcss) && jsondata.explicitcss==1) explicitcss=true;
            
            //jsf_getwebdatapage_jsonp('','','',jsondata.wd_id,'','',jsondata.wd_row_id,jsondata.nextpg,testing,noemail,explicitcss,jsondata.origemail);
            jsf_getwebdatapagereturn_jsonp(jsondata.wd_id,'','','',jsondata.userid,jsondata.wd_row_id,jsondata.nextpg,testing,noemail,explicitcss,jsondata.origemail,jsondata.email);         
         }
      } else {
         str = str + '<div style=\"padding:10px;color:red;font-size:16px;font-family:arial;\">';
         str = str + jsondata.error;
         str = str + '</div>';
         jQuery('#jsfwdarea_error').html(str);
         jQuery('#jsfwdwdsubmitload').hide();
         jQuery('#jsfwdwdsubmit').show();
      }
   }
}


function jsfwdvisualcallback(jsondata){
   if (typeof jsf_endjsoning == 'function') jsf_endjsoning();
   //alert(JSON.stringify(jsondata));
   var str = '';
   if (jsondata.responsecode==1) {
      if (jsondata.nextpg==-1) {
         str = str + '<div id=\"jsfwd_finalmessage\" style=\"padding:10px;color:green;font-size:16px;font-family:arial;\">';
         str = str + 'Your data was successfully submitted.  Thank you!';
         str = str + '</div>';
         jQuery('#jsfwdarea').html(str);
         jsfwebdata_SetCookie('jsf_wd_w' + jsondata.wd_id,jsondata.wd_row_id);
         if (typeof jsfwdvisualcallback_end == 'function') jsfwdvisualcallback_end();
      } else {
         var testing = false;
         var noemail = false;
         var explicitcss = false;
         if (Boolean(jsondata.testing) && jsondata.testing==1) testing=true;
         if (Boolean(jsondata.noemail) && jsondata.noemail==1) noemail=true;
         if (Boolean(jsondata.explicitcss) && jsondata.explicitcss==1) explicitcss=true;
         if (Boolean(jsondata.wd_row_id)) jsfwd_rowid = jsondata.wd_row_id;
         if (Boolean(jsondata.wd_id)) jsfwd_wdid = jsondata.wd_id;
         jQuery('#' + jsondata.prefix + '_wdvis_pg' + (jsondata.nextpg-1)).show();
      }
   } else {
      str = str + '<div style=\"padding:10px;color:red;font-size:16px;font-family:arial;\">';
      str = str + jsondata.error;
      str = str + '</div>';
      jQuery('#jsfwdarea').html(str);
   }
}


//----------------------------------------------------------------------------------
// Print the contents of a jdata into a table
// This prints a table of multiple rows
// #API
//----------------------------------------------------------------------------------
function jsf_getwdtable_jsonp(wdname,wd_id,domain,userid,token,filterstr,limit,page,maxcol,callback,prefix,xtraurl,orderby,searchflds,testing,skipfiltering,foruserid,groupby){
   if (Boolean(testing)) jsfwd_testing=true;
   if (Boolean(userid)) jsfwd_userid=userid;
   if (Boolean(token)) jsfwd_token=token;
   if (Boolean(groupby)) jsfwd_groupby=groupby;
   if (!Boolean(domain)) domain=defaultremotedomain;
   defaultremotedomain = domain;
   if (!Boolean(callback)) callback='jsfwebdata_tabledisplay';
   if (!Boolean(prefix)) prefix='jsfwd';
   if (!Boolean(limit) || isNaN(limit)) limit=25;
   if (!Boolean(page)) page=1;
   if (!Boolean(maxcol)) maxcol=2;
   var a_limit = 'LIMIT ' + ((parseInt(page) - 1)* parseInt(limit)).toString() + ',' + limit;
   jsfwd_pagenum = page;
   jsfwd_limit = limit;
   jsfwd_orderby = orderby;
   jsfwd_maxcol = maxcol;
   if(Boolean(xtraurl)) jsfwd_xtraurl = xtraurl;
   if(Boolean(filterstr)) jsfwd_filterstr = filterstr;
   if(Boolean(searchflds)) jsfwd_searchflds = searchflds;

   if (jsfwd_testing) alert(defaultremotedomain + ', ' + callback + ', ' + prefix);

   var url = defaultremotedomain + jsfwd_servercontroller;
   url = url + '&action=getwdandrows';
   url = url + '&callback=' + encodeURIComponent(callback);
   url = url + '&enabledonly=1';
   if(!Boolean(skipfiltering)) {
      url = url + '&addfiltering=1';
      if(!Boolean(jsfwd_showfilterbutton)) url = url + '&autosearch=1';
   }
   url = url + '&addrowdisplay=1';
   if(Boolean(foruserid)) url = url + '&foruserid=' + encodeURIComponent(foruserid);
   url = url + '&userid=' + encodeURIComponent(userid);
   url = url + '&token=' + encodeURIComponent(token);
   if (Boolean(wdname)) url = url + '&wdname=' + encodeURIComponent(wdname);
   if (Boolean(wd_id)) url = url + '&wd_id=' + encodeURIComponent(wd_id);
   if (Boolean(orderby) && orderby!='null') url = url + '&orderby=' + encodeURIComponent(orderby);
   if (Boolean(maxcol)) url = url + '&maxcol=' + encodeURIComponent(maxcol);
   if (Boolean(jsfwd_searchflds)) url = url + '&searchflds=' + encodeURIComponent(jsfwd_searchflds);
   if (Boolean(prefix)) url = url + '&prefix=' + encodeURIComponent(prefix);
   if (Boolean(jsfwd_filterstr) && jsfwd_filterstr!='Search') url = url + '&filterstr=' + encodeURIComponent(jsfwd_filterstr);
   if (Boolean(a_limit)) url = url + '&limit=' + encodeURIComponent(a_limit);
   if (Boolean(jsfwd_xtraurl)) url = url + jsfwd_xtraurl;
   //alert('URL: ' + url);
   if (jsfwd_testing) alert('URL: ' + url);
   //alert('URL: ' + url);

   jsfwebdata_CallJSONP(url);
   //jsfwebdata_CallJSONP_inline(url);
}


// Show rows from a table with delete button
function jsfwebdata_tabledisplay(jsondata){
   //alert(JSON.stringify(jsondata));
   if (typeof jsf_endjsoning == 'function') jsf_endjsoning(jsondata);
   var str = '';
   var initfilter = false;
   
   if(!Boolean(jsfwd_pagenum)) jsfwd_pagenum = 1;
   
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
      jsfwd_mostrecentrows = jsondata.rows;
   	//alert(jsondata.query);
      var newpage = false;
      var totalrows = jsondata.rows.length;
      //var totalrows = jsondata.totalrows;
      
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
      if (!Boolean(jsfwd_limit)) jsfwd_limit = jsondata.limit;      

      //alert('totalrows: ' + totalrows);
      if (totalrows>=jsfwd_limit) {
         totalrows = jsfwd_limit;
         newpage = true;
      }

      str = str + '<div id=\"jsfwdtableouter' + jsondata.wd_id + '\" class=\"jsfwdtableouter\">';

      if (Boolean(jsondata.inputdisplayrow) || jsondata.totalrows>9 || Boolean(jsondata.filterstr) || (Boolean(jsondata.foruserid) && Boolean(jsondata.userid) && jsondata.userid==jsondata.foruserid) || Boolean(jsfwd_xtraurl)) {
      	 str = str + '<div class=\"jsfwdtablerow\" style=\"margin-top:4px;margin-bottom:5px;\">';
      	 
      	 if(Boolean(jsondata.filtercount) && jsondata.filtercount>0) {
             var temp = '';
             //jsf_getwdtable_jsonp(wdname,wd_id,domain,userid,token,filterstr,limit,page,maxcol,callback,prefix,xtraurl,orderby,searchflds,testing,skipfiltering,foruserid,groupby){
             temp = temp + 'jsf_getwdtable_jsonp(';
             temp = temp + '\'' + jsondata.wdname + '\',';
             temp = temp + '\'' + jsondata.wd_id + '\',';
             temp = temp + '\'\',';
             temp = temp + '\'' + jsondata.userid + '\',';
             temp = temp + '\'' + jsondata.token + '\',';
             //temp = temp + 'jQuery(\'#jsfwdfilterstrdiv\').val(),';
             temp = temp + 'jsfwd_filterstr,';
             temp = temp + '\'' + jsfwd_limit + '\',';
             temp = temp + '\'\',';
             temp = temp + '\'' + jsondata.maxcol + '\',';
             temp = temp + '\'\',';
             temp = temp + '\'\',';
             temp = temp + 'jsfwd_xtraurl,';
             temp = temp + '\'' + jsfwd_orderby + '\',';
             if(Boolean(jsfwd_searchflds)) temp += '\'' + jsfwd_searchflds + '\',';
             else temp += '\'\',';
             temp = temp + '\'\',';
             temp = temp + '\'\',';
             if(Boolean(jsondata.foruserid)) temp += '\'' + jsondata.foruserid + '\',';
             else temp += '\'\',';
             temp = temp + '\'\');';
      	    
      	    str = str + '<script>\n';
      	    str = str + jsondata.filterinit;
      	    str = str + jsondata.filterget;
      	    str += '\nfunction jsfwd_executefiltersearch() {\n';
      	    str += 'jsfwd_getsearchuri();\n';
      	    str += 'if(typeof jsfwd_executefiltersearch_loading === \'function\') jsfwd_executefiltersearch_loading();\n';
      	    str += 'else jQuery(\'#jsfwdtableouter' + jsondata.wd_id + '\').html(\'loading...\');\n';
      	    if(Boolean(jsondata.adduser)) str += 'jsfwd_xtraurl += \'&adduser=1\';\n';
      	    str += temp + '\n';
      	    str += '}\n';
      	    str = str + '</script>\n';
      	    str = str + '<div style=\"font-size:12px;font-family:arial;\">\n';
      	    
      	    var defaultcssinput = 'color:#000000;font-style:normal;';
      	    if(!Boolean(jsondata.filterstr) || jsondata.filterstr=='Search') {
      	       jsondata.filterstr = 'Search';
      	       defaultcssinput = 'color:#999999;font-style:italic;';
      	    }
      	    var hidesearchdiv = '';
      	    if(!Boolean(jsfwd_showsearchstring)) hidesearchdiv = 'display:none;';
      	    
             //str = str + '<div style=\"float:left;margin:4px 4px 8px 2px;' + hidesearchdiv + '\">Search</div>';
             str = str + '<input type=\"text\" ';
             str = str + 'style=\"float:left;margin-left:2px;margin-right:8px;width:100px;font-size:12px;font-family:verdana;' + hidesearchdiv + defaultcssinput + '\" ';
             str = str + 'id=\"jsfwdfilterstrdiv' + jsondata.wd_id + '\" ';
             str = str + 'onblur=\"var txt = jQuery(\'#jsfwdfilterstrdiv' + jsondata.wd_id + '\');if(!Boolean(txt.val()) || txt.val() == \'\'){ txt.val(\'Search\');txt.css(\'font-style\',\'italic\').css(\'color\',\'#999999\');}\" ';
             str = str + 'onfocus=\"var txt = jQuery(\'#jsfwdfilterstrdiv' + jsondata.wd_id + '\');if(Boolean(txt.val()) && txt.val() == \'Search\'){ txt.val(\'\');txt.css(\'font-style\',\'normal\').css(\'color\',\'#000000\');}\" ';
             str = str + 'onkeyup=\"if(event.keyCode==\'13\' || event.which==\'13\') ' + temp + '\" ';
             str = str + 'value=\"' + jsondata.filterstr + '\">';
      	    
      	    str = str + jsondata.filterhtml;
      	    if(!Boolean(parseInt(jsondata.autosearch))) {
                str = str + '<div ';
                str += 'id=\"jsfwdfilter_gobtn\" ';
                str = str + 'style=\"float:left;margin-left:15px;margin-bottom:15px;font-size:10px;font-family:arial;padding:4px;border:1px solid #000000;background-color:#DEDEDE;cursor:pointer;border-radius:4px;text-align:center;width:24px;\" ';
                str = str + 'onclick=\"jsfwd_executefiltersearch();\" ';
                str = str + '>Go</div>';
      	    }
      	    
      	    if(Boolean(jsfwd_xtraurl) || Boolean(jsfwd_filterstr)) {
      	       //alert('url: ' + jsfwd_xtraurl + ' filter str: ' + jsfwd_filterstr);
                str = str + '<div ';
                str += 'id=\"jsfwdfilter_clearbtn\" ';
                str = str + 'style=\"float:left;margin-left:15px;margin-bottom:15px;font-size:10px;font-family:arial;padding:4px;border:1px solid #000000;background-color:#DEDEDE;cursor:pointer;border-radius:4px;text-align:center;width:24px;\" ';
                //str = str + 'onclick=\"jQuery(\'#jsfwdfilterstrdiv\').val(\'\');jsfwd_filterstr=\'\';jsfwd_xtraurl=\'\';' + temp + '\" ';
                str = str + 'onclick=\"jsfwd_filterstr=\'\';jsfwd_xtraurl=\'\';' + temp + '\" ';
                str = str + '>Clear</div>';
      	    }
      	    
             str = str + '<div ';
             str += 'id=\"jsfwdfilter_extrabtn\" ';
             str = str + 'style=\"float:left;margin-left:15px;margin-bottom:15px;font-size:10px;font-family:arial;\" ';
             str = str + '></div>';
                
      	    str = str + '<div style=\"clear:both;width:1px;height:10px;\"></div>';
      	    str = str + '</div>\n';
      	          	    
      	    initfilter = true;
      	 } else {
             var temp = '';
             temp += 'var xfui=\'\';';
             temp += 'if(document.getElementById(\'jsfwdmeonly' + jsondata.wd_id + '\').checked) xfui=\'' + jsondata.userid + '\';';
             temp = temp + 'jsf_getwdtable_jsonp(';
             temp = temp + '\'' + jsondata.wdname + '\',';
             temp = temp + '\'' + jsondata.wd_id + '\',';
             temp = temp + '\'\',';
             temp = temp + '\'' + jsondata.userid + '\',';
             temp = temp + '\'' + jsondata.token + '\',';
             temp = temp + 'jQuery(\'#jsfwdfilterstrdiv' + jsondata.wd_id + '\').val(),';
             temp = temp + '\'' + jsfwd_limit + '\',';
             temp = temp + '\'\',';
             temp = temp + '\'' + jsondata.maxcol + '\',';
             temp = temp + '\'\',';
             temp = temp + '\'\',';
             if(Boolean(jsfwd_xtraurl)) temp = temp + '\'' + jsfwd_xtraurl + '\',';
             else temp = temp + '\'\',';
             temp = temp + '\'' + jsfwd_orderby + '\',';
             if(Boolean(jsfwd_searchflds)) temp += '\'' + jsfwd_searchflds + '\'';
             else temp += '\'\'';
             temp += ',\'\'';
             temp += ',\'\'';
             temp += ',xfui';
             temp = temp + ');';
             
             //jsf_getwdtable_jsonp(wdname,wd_id,domain,userid,token,filterstr,limit,page,maxcol,callback,prefix,xtraurl,orderby,searchflds,testing,skipfiltering,foruserid,groupby)
   
             str = str + '<input type=\"text\" ';
             //str = str + 'style=\"float:left;margin-left:8px;margin-right:8px;width:' + (globalinnerwidth - 40 - 40).toString() + 'px;height:30px;font-size:18px;font-family:verdana;\" ';
             str = str + 'style=\"float:left;margin-left:8px;margin-right:8px;width:110px;font-size:10px;font-family:verdana;\" ';
             str = str + 'id=\"jsfwdfilterstrdiv' + jsondata.wd_id + '\" ';
             //str = str + 'onkeyup=\"if(event.keyCode==\'13\' || event.which==\'13\') ' + temp + '\" ';
             str = str + 'value=\"' + jsondata.filterstr + '\">';
             
             str += '<div id=\"jsfwdmeonlydiv' + jsondata.wd_id + '\" style=\"float:left;margin-left:8px;margin-right:8px;font-size:10px;\">';
             str += '<input id=\"jsfwdmeonly' + jsondata.wd_id + '\" type=\"checkbox\" value=\"' + jsondata.userid + '\"';
             if(Boolean(jsondata.userid) && Boolean(jsondata.foruserid) && jsondata.userid==jsondata.foruserid) str += ' CHECKED';
             str += '>';
             str += 'My records</div>';
             
             str = str + '<div style=\"float:left;padding:3px;margin-left:2px;overflow:hidden;background-color:#EEEEEE;border:1px solid #222222;border-radius:2px;cursor:pointer;text-align:center;font-size:10px;font-family:arial;\" ';
             str = str + 'onclick=\"';
             str = str + temp;
             str = str + '\">Go</div>';
             
             //jsfwd_filterstr = '';
      	 }
      	 str = str + '</div>';
      }
      
      str = str + '<div class=\"jsfwdtablerow jsfwdtablepaging\">';
      if (Boolean(jsfwd_pagenum) && jsfwd_pagenum>1) {
         str = str + '<div class=\"jsfwdtableprevpg\" onclick=\"';
         //function jsf_getwdtable_jsonp(wdname,wd_id,domain,userid,token,filterstr,limit,page,maxcol,callback,prefix,testing){
         str = str + 'jsf_getwdtable_jsonp(';
         str = str + '\'' + jsondata.wdname + '\',';
         str = str + '\'' + jsondata.wd_id + '\',';
         str = str + '\'\',';
         str = str + '\'' + jsondata.userid + '\',';
         str = str + '\'' + jsondata.token + '\',';
         str = str + '\'' + jsondata.filterstr + '\',';
         str = str + '\'' + jsfwd_limit + '\',';
         str = str + (jsfwd_pagenum-1).toString() + ',';
         str = str + '\'' + jsondata.maxcol + '\',';
         str = str + '\'\',';
         str = str + '\'\',';
         if(Boolean(jsfwd_xtraurl)) str = str + '\'' + jsfwd_xtraurl + '\',';
         else str = str + '\'\',';
         str = str + '\'' + jsfwd_orderby + '\',';
         if(Boolean(jsfwd_searchflds)) str += '\'' + jsfwd_searchflds + '\',';
         else str += '\'\',';
         str = str + '\'\',';
         str = str + '\'\',';
         if(Boolean(jsondata.foruserid)) str += '\'' + jsondata.foruserid + '\',';
         else str += '\'\',';
         str = str + '\'\');\"></div>';
      }
      
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
         str = str + 'jsf_getwdtable_jsonp(';
         str = str + '\'' + jsondata.wdname + '\',';
         str = str + '\'' + jsondata.wd_id + '\',';
         str = str + '\'\',';
         str = str + '\'' + jsondata.userid + '\',';
         str = str + '\'' + jsondata.token + '\',';
         str = str + '\'' + jsondata.filterstr + '\',';
         str = str + '\'' + jsfwd_limit + '\',';
         str = str + 'pg,';
         str = str + '\'' + jsondata.maxcol + '\',';
         str = str + '\'\',';
         str = str + '\'\',';
         if(Boolean(jsfwd_xtraurl)) str = str + '\'' + jsfwd_xtraurl + '\',';
         else str = str + '\'\',';
         str = str + '\'' + jsfwd_orderby + '\',';
         str = str + '\'' + jsfwd_searchflds + '\',';
         str = str + '\'\',';
         str = str + '\'\',';
         if(Boolean(jsondata.foruserid)) str += '\'' + jsondata.foruserid + '\',';
         else str += '\'\',';
         str = str + '\'\');';
         str = str + '\">';
         for(var i=0;i<tp;i++){
            var curpg = parseInt(i) + parseInt(start);
            if (totalpages >= curpg) {
               str = str + '<option value=\"' + curpg + '\"';
               if (curpg==jsfwd_pagenum) str = str + ' SELECTED';
               str = str + '>Page ' + curpg + '</option>';
            }
         }
         str = str + '</select>';
         str = str + '</div>';
      }
      if (newpage) {
         var curpg = parseInt(jsfwd_pagenum) + 1;
         str = str + '<div class=\"jsfwdtablenextpg\" onclick=\"';
         str = str + 'jsf_getwdtable_jsonp(';
         str = str + '\'' + jsondata.wdname + '\',';
         str = str + '\'' + jsondata.wd_id + '\',';
         str = str + '\'\',';
         str = str + '\'' + jsondata.userid + '\',';
         str = str + '\'' + jsondata.token + '\',';
         str = str + '\'' + jsondata.filterstr + '\',';
         str = str + '\'' + jsfwd_limit + '\',';
         str = str + curpg.toString() + ',';
         str = str + '\'' + jsondata.maxcol + '\',';
         str = str + '\'\',';
         str = str + '\'\',';
         if(Boolean(jsfwd_xtraurl)) str = str + '\'' + jsfwd_xtraurl + '\',';
         else str = str + '\'\',';
         str = str + '\'' + jsfwd_orderby + '\',';
         str = str + '\'' + jsfwd_searchflds + '\',';
         str = str + '\'\',';
         str = str + '\'\',';
         if(Boolean(jsondata.foruserid)) str += '\'' + jsondata.foruserid + '\',';
         else str += '\'\',';
         str = str + '\'\');\"></div>';
      }
      str = str + '</div>';
      var js = '';
      
      var rowstable = jsfwebdata_tabledisplay_rows(usingdiv,jsondata,totalrows,newwindowfordetails,jsfwd_groupby,'price');
      str += rowstable.str;
      js += rowstable.js;
      /*
      for (var i=0; i<totalrows; i++ ){
         str += '<div id=\"jsfwdtableitem' + jsondata.wd_id + '_' + i + '\" ';
         str += 'data-wdrow=\"' + i + '\" ';
         str += 'class=\"jsfwdtablerow jsfwdtableitem\" ';
         if(newwindowfordetails) str += 'onclick=\"window.open(\'' + defaultremotedomain + 'jsfcode/ViewWDataJSON.php?wd_id=' + jsondata.wd_id + '&wd_row_id=' + jsondata.rows[i].wd_row_id + '&origemail=' + jsondata.rows[i].origemail + '\');\" ';
         else str += 'onclick=\"jsf_getwebdata_jsonp(\'' + jsondata.wdname + '\',\'\',\'\',\'' + jsondata.wd_id + '\',\'\',\'' + jsondata.userid + '\',\'' + jsondata.rows[i].wd_row_id + '\',\'\',1);\" ';
         str += '>';
         str = str + '<div id=\"jsfwdtableitemdel' + jsondata.wd_id + '_' + i + '\" class=\"jsfwdtablerowdelbtn\"';
         str = str + ' onClick=\"';
         str = str + 'if (!event) var event = window.event;event.cancelBubble = true;if (event.stopPropagation) event.stopPropagation();';
         str = str + 'jQuery(\'' + usingdiv + '\').html(\'Loading...\');jsf_removewdrow_jsonp(';
         str = str + jsondata.wd_id + ',' + jsondata.rows[i].wd_row_id;
         //if (!Boolean(jsondata.testing)) jsondata.testing = false;
         //else if (jsondata.testing==1) jsondata.testing=true;
         str = str + ',\'' + defaultremotedomain + '\'';
         str = str + ',\'' + jsondata.userid + '\'';
         str = str + ',\'' + jsondata.filterstr + '\'';
         str = str + ',\'' + jsfwd_limit + '\'';
         str = str + ',\'' + jsfwd_pagenum + '\'';
         str = str + ',\'' + jsondata.maxcol + '\'';
         str = str + ',\'' + jsondata.callback + '\'';
         str = str + ',\'' + jsondata.prefix + '\'';
         //str = str + ',\'' + jsondata.testing + '\'';
         str = str + ');\"';
         str = str + '>Delete</div>';
         str = str + jsondata.rows[i].display;
         str = str + '</div>';
         js = js + 'jQuery(\'#jsfwdtableitemdel' + jsondata.wd_id + '_' + i + '\').hide();\n';
      }
      */
      
      if(jsfwd_showcreaterecord) {
         str += '<div id=\"jsfwdtableitemnew' + jsondata.wd_id + '\" ';
         str += 'data-wdrow=\"new\" ';
         str += 'class=\"jsfwdtablerow jsfwdtableitem\" ';
         if(multiplepossible || jsfwd_opennewwindow) str += 'onclick=\"window.open(\'' + defaultremotedomain + 'jsfcode/ViewWDataJSON.php?wd_id=' + jsondata.wd_id + '\',\'detailtargetwindow\',\'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=500,height=500\');\">';
         else str += 'onclick=\"jsf_getwebdata_jsonp(\'' + jsondata.wdname + '\',\'\',\'\',\'' + jsondata.wd_id + '\',\'\',\'' + jsondata.userid + '\',\'\',\'\',1);\">';
         str = str + 'Create a new record\n';
         str = str + '</div>\n';
      }
      
      str = str + '</div>\n';
      str = str + '<script>\n';
      str = str + 'function clearAllDelButtons' + jsondata.wd_id + '(){\n' + js + '}\n';
      for (var i=0; i<totalrows; i++ ){
         str += 'jQuery(\'#jsfwdtableitem' + jsondata.wd_id + '_' + i + '\').on(\'swipeleft\',function(){alert(\'slide left\');clearAllDelButtons' + jsondata.wd_id + '();var x = jQuery(this).attr(\'data-wdrow\');jQuery(\'#jsfwdtableitemdel' + jsondata.wd_id + '_\' + x).slideToggle(\'400\');});\n';
         str += 'jQuery(\'#jsfwdtableitem' + jsondata.wd_id + '_' + i + '\').on(\'swiperight\',function(){alert(\'slide right\');clearAllDelButtons' + jsondata.wd_id + '();});\n';
         //str += 'jQuery(\'#jsfwdtableitem' + jsondata.wd_id + '_' + i + '\').on(\'swipeleft\',function(){alert(\'slide left\');});\n';
         //str += 'jQuery(\'#jsfwdtableitem' + jsondata.wd_id + '_' + i + '\').on(\'swiperight\',function(){alert(\'slide right\');});\n';
      }
      str += '</script>\n';
      //alert('div: ' + usingdiv);
      jQuery(usingdiv).html(str);
      if(initfilter) jsfwd_initsearchuri();
      //for (var i=0; i<totalrows; i++ ){
      //   jQuery('#jsfwdtableitem' + jsondata.wd_id + '_' + i).on('swipeleft',function(){clearAllDelButtons();var x = jQuery(this).attr('data-wdrow');jQuery('#jsfwdtableitemdel' + x).slideToggle('400');});
      //   jQuery('#jsfwdtableitem' + jsondata.wd_id + '_' + i).on('swiperight',function(){clearAllDelButtons();});
      //}
      
      //Display or not to display
      if(multiplepossible && (totalrows>0 || (Boolean(jsondata.htags) && jsondata.htags.includes('#display')))) jQuery(tablediv).show();
      
   } else {
      str = str + '<div style=\"padding:10px;color:red;font-size:16px;font-family:arial;\">';
      str = str + jsondata.error;
      str = str + '</div>';
      jQuery(usingdiv).html(str);
   }
   
   if(typeof jsfwd_executefiltersearch_finished === 'function') jsfwd_executefiltersearch_finished();   
}

function jsfwebdata_tabledisplay_rows(usingdiv,jsondata,totalrows,newwindowfordetails,groupby,aggregatekey) {
      var str = '';
      var js = '';
      if(!Boolean(totalrows)) totalrows = jsondata.rows.length;
      
      if(Boolean(groupby)) {
         //alert('jsondata: ' + JSON.stringify(jsondata.fieldsbyname));
         var dispgroups = {};
         for (var i=0; i<totalrows; i++ ){
            if(!Boolean(dispgroups[jsondata.rows[i][groupby]])) dispgroups[jsondata.rows[i][groupby]] = [];
            dispgroups[jsondata.rows[i][groupby]].push(i);
         }
         for (var key in dispgroups) {
            var grpname = key;
            if(Boolean(jsondata.fieldsbyname[groupby]) && Boolean(jsondata.fieldsubs[jsondata.fieldsbyname[groupby]][key])) grpname = jsondata.fieldsubs[jsondata.fieldsbyname[groupby]][key];
            str += '\n<script>\n';
            str += 'function jsfwdtablesectionclick' + jsondata.wd_id + '_' + key + '() {\n';
            str += '   if(jQuery(\'#jsfwdtablesectind' + jsondata.wd_id + '_' + key + '\').html()==\'+\') {\n';
            str += '      jQuery(\'#jsfwdtablesectind' + jsondata.wd_id + '_' + key + '\').html(\'-\');\n';
            str += '      jQuery(\'#jsfwdtablesection' + jsondata.wd_id + '_' + key + '\').show();\n';
            str += '   } else {\n';
            str += '      jQuery(\'#jsfwdtablesectind' + jsondata.wd_id + '_' + key + '\').html(\'+\');\n';
            str += '      jQuery(\'#jsfwdtablesection' + jsondata.wd_id + '_' + key + '\').hide();\n';
            str += '   }\n';
            str += '}\n';
            str += '</script>\n';
            str += '<div style=\"margin:8px 1px 4px 1px;\">';
            str += '<div style=\"font-weight:bold;cursor:pointer;\" onclick=\"jsfwdtablesectionclick' + jsondata.wd_id + '_' + key + '();\">';
            str += '<span id=\"jsfwdtablesectind' + jsondata.wd_id + '_' + key + '\">+</span> ';
            str += grpname;
            if(dispgroups[key].length>0 && Boolean(aggregatekey) && Boolean(jsondata.rows[0][aggregatekey])){
            //if(dispgroups[key].length>1 && Boolean(aggregatekey) && Boolean(jsondata.rows[0][aggregatekey])){
               var total = 0.0;
               for (var i=0; i<dispgroups[key].length; i++ ){
                  total = total + parseFloat(jsondata.rows[jsondata,dispgroups[key][i]][aggregatekey]);
               }
               var avg = (total / dispgroups[key].length).toFixed(4);
               str += ' <span style=\"margin-left:15px;font-weight:normal;color:#777777;\">Average';
               if(aggregatekey.toLowerCase()=='price') str += ' price: $';
               else str += ': ';
               str += avg + '</span>';
            }
            str += '</div>';
            str += '<div id=\"jsfwdtablesection' + jsondata.wd_id + '_' + key + '\" style=\"margin-left:25px;display:none;\">';
                        
            for (var i=0; i<dispgroups[key].length; i++ ){
               str += jsfwebdata_tabledisplay_singlerow(usingdiv,jsondata,dispgroups[key][i],newwindowfordetails);
               js += 'jQuery(\'#jsfwdtableitemdel' + jsondata.wd_id + '_' + dispgroups[key][i] + '\').hide();\n';
            }
            if(Boolean(jsondata.inputdisplayrow)) {
               var tempdisp = jsfwebdata_replaceAll('jsfchjprefixsub','w' + jsondata.wd_id + '_gb' + key,jsondata.inputdisplayrow);
               str += tempdisp;
               str += '\n<script>\njQuery(\'#w' + jsondata.wd_id + '_gb' + key + '_a' + jsondata.fieldsbyname[groupby] + '\').val(\'' + key + '\');\n</script>\n';
            }
            str += '</div>';
            str += '</div>';
         }
      } else {      
         for (var i=0; i<totalrows; i++ ){
            str += jsfwebdata_tabledisplay_singlerow(usingdiv,jsondata,i,newwindowfordetails);
            js += 'jQuery(\'#jsfwdtableitemdel' + jsondata.wd_id + '_' + i + '\').hide();\n';
         }
      }
      var respobj = [];
      respobj.str = str;
      respobj.js = js;
      return respobj;
}

function jsfwebdata_tabledisplay_singlerow(usingdiv,jsondata,i,newwindowfordetails) {
   var str = '';
   var divid = 'jsfwdtableitem' + jsondata.wd_id + '_' + i;
   str += '<div id=\"' + divid + '\" ';
   str += 'data-wdrow=\"' + i + '\" ';
   str += 'data-wdrowid=\"' + jsondata.rows[i].wd_row_id + '\" ';
   str += 'class=\"jsfwdtablerow jsfwdtableitem\" ';
   if(Boolean(newwindowfordetails)) str += 'onclick=\"window.open(\'' + defaultremotedomain + 'jsfcode/ViewWDataJSON.php?wd_id=' + jsondata.wd_id + '&wd_row_id=' + jsondata.rows[i].wd_row_id + '&origemail=' + jsondata.rows[i].origemail + '\',\'detailtargetwindow\',\'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=500,height=500\');\" ';
   else str += 'onclick=\"jsf_getwebdata_jsonp(\'' + jsondata.wdname + '\',\'\',\'\',\'' + jsondata.wd_id + '\',\'\',\'' + jsondata.userid + '\',\'' + jsondata.rows[i].wd_row_id + '\',\'\',1);\" ';
   str += '>';
   str = str + '<div id=\"jsfwdtableitemdel' + jsondata.wd_id + '_' + i + '\" class=\"jsfwdtablerowdelbtn\"';
   str = str + ' onClick=\"';
   str = str + 'if (!event) var event = window.event;event.cancelBubble = true;if (event.stopPropagation) event.stopPropagation();';
   str = str + 'jQuery(\'' + usingdiv + '\').html(\'Loading...\');jsf_removewdrow_jsonp(';
   str = str + jsondata.wd_id + ',' + jsondata.rows[i].wd_row_id;
   //if (!Boolean(jsondata.testing)) jsondata.testing = false;
   //else if (jsondata.testing==1) jsondata.testing=true;
   str = str + ',\'' + defaultremotedomain + '\'';
   str = str + ',\'' + jsondata.userid + '\'';
   str = str + ',\'' + jsondata.filterstr + '\'';
   str = str + ',\'' + jsfwd_limit + '\'';
   str = str + ',\'' + jsfwd_pagenum + '\'';
   str = str + ',\'' + jsondata.maxcol + '\'';
   str = str + ',\'' + jsondata.callback + '\'';
   str = str + ',\'' + jsondata.prefix + '\'';
   //str = str + ',\'' + jsondata.testing + '\'';
   str = str + ');\"';
   str = str + '>Delete</div>';
   
   //New button style
   var delbtn2 = jsfwebdata_builddeletebutton(jsondata.wd_id,jsondata.rows[i].wd_row_id,jsondata.rows[i].origemail,divid);
   //delbtn2 += '<div';
   //delbtn2 += ' id=\"jsfwddel_' + jsondata.wd_id + '_' + jsondata.rows[i].wd_row_id + '\"';
   //delbtn2 += ' class=\"jsfwdtablerowdelbtn2\"';
   //delbtn2 += ' data-wdid=\"' + jsondata.wd_id + '\"';
   //delbtn2 += ' data-wdrowid=\"' + jsondata.rows[i].wd_row_id + '\"';
   //delbtn2 += ' onclick=\"if (!event) var event = window.event;event.cancelBubble = true;if (event.stopPropagation) event.stopPropagation();jsf_removewdrowsimple_jsonp(\'' + jsondata.wd_id + '\',\'' + jsondata.rows[i].wd_row_id + '\',\'' + divid + '\');\"';
   //delbtn2 += '>';
   //delbtn2 += 'Delete';
   //delbtn2 += '</div>';
   
   str = str + jsfwebdata_replaceAll('%%%DELETE%%%',delbtn2,jsondata.rows[i].display);
   str = str + '</div>';
   return str;
}

function jsfwebdata_builddeletebutton(wd_id,wd_row_id,origemail,divid,oc) {
   //New button style
   var delbtn2 = '';
   delbtn2 += '<div';
   delbtn2 += ' id=\"jsfwddel_' + wd_id + '_' + wd_row_id + '\"';
   delbtn2 += ' class=\"jsfwdtablerowdelbtn2\"';
   delbtn2 += ' data-wdid=\"' + wd_id + '\"';
   delbtn2 += ' data-wdrowid=\"' + wd_row_id + '\"';
   delbtn2 += ' onclick=\"';
   delbtn2 += 'if (!event) var event = window.event;';
   delbtn2 += 'event.cancelBubble = true;';
   delbtn2 += 'if (event.stopPropagation) event.stopPropagation();';
   delbtn2 += 'jsf_removewdrowsimple_jsonp(\'' + wd_id + '\',\'' + wd_row_id + '\',\'' + origemail + '\',\'' + divid + '\');';
   if(Boolean(oc)) delbtn2 += oc;
   delbtn2 += '\">';
   delbtn2 += 'Delete';
   delbtn2 += '</div>';
   return delbtn2;
}


// Builds a button to remove the wd_link entry (keeps the record in tact)
function jsfwebdata_buildremovebutton(wd_id,wd_row_id,linkid,origemail,divid,oc) {
   //New button style
   var delbtn2 = '';
   delbtn2 += '<div';
   delbtn2 += ' id=\"jsfwdrem_' + wd_id + '_' + wd_row_id + '\"';
   delbtn2 += ' class=\"jsfwdtablerowrembtn2\"';
   delbtn2 += ' data-wdid=\"' + wd_id + '\"';
   delbtn2 += ' data-wdrowid=\"' + wd_row_id + '\"';
   delbtn2 += ' onclick=\"';
   delbtn2 += 'if (!event) var event = window.event;';
   delbtn2 += 'event.cancelBubble = true;';
   delbtn2 += 'if (event.stopPropagation) event.stopPropagation();';
   delbtn2 += 'jsf_removeforeignlink_jsonp(\'' + wd_id + '\',\'' + wd_row_id + '\',\'' + linkid + '\',\'' + origemail + '\',\'' + divid + '\');';
   //delbtn2 += 'jsf_removewdrowsimple_jsonp(\'' + wd_id + '\',\'' + wd_row_id + '\',\'' + origemail + '\',\'' + divid + '\');';
   if(Boolean(oc)) delbtn2 += oc;
   delbtn2 += '\">';
   delbtn2 += 'Remove';
   delbtn2 += '</div>';
   return delbtn2;
}


function jsf_removewdrowsimple_jsonp(wd_id,wd_row_id,origemail,divid){
   if(Boolean(jsfwd_userid) && Boolean(jsfwd_token)) {
      if(confirm('Delete this item?')) {
         if(Boolean(divid) && jQuery('#' + divid).length>0) jQuery('#' + divid).html('<div style=\"padding:5px;text-align:center;font-weight:bold;\">Deleting...</div>');
         var callback = 'jsf_removewdrowsimple_return';
         var url='';
         url += defaultremotedomain + jsfwd_servercontroller;
         url += '&action=deletesinglewdrow';
         url += '&callback=' + callback;
         url += '&wd_id=' + encodeURIComponent(wd_id);
         url += '&wd_row_id=' + encodeURIComponent(wd_row_id);
         url += '&origemail=' + encodeURIComponent(origemail);
         url += '&userid=' + encodeURIComponent(jsfwd_userid);
         url += '&token=' + encodeURIComponent(jsfwd_token);
         url += '&divid=' + encodeURIComponent(divid);
      
         if (jsfwd_testing) alert('URL: ' + url);
         //alert('URL: ' + url);
      
         jsfwebdata_CallJSONP(url);
      }
   }
}

function jsf_removewdrowsimple_return(jsondata){
   //alert(JSON.stringify(jsondata));
   if (typeof jsf_endjsoning == 'function') jsf_endjsoning(jsondata);
   if(Boolean(jsondata.divid) && jQuery('#' + jsondata.divid).length>0) jQuery('#' + jsondata.divid).hide();
   if (typeof jsfwd_postdelete == 'function') jsfwd_postdelete(jsondata);
}

function jsf_removeforeignlink_jsonp(wd_id,wd_row_id,linkid,origemail,divid){
   if(Boolean(jsfwd_userid) && Boolean(jsfwd_token)) {
      if(confirm('Delete reference to this item?')) {
         if(Boolean(divid) && jQuery('#' + divid).length>0) jQuery('#' + divid).html('<div style=\"padding:5px;text-align:center;font-weight:bold;\">Removing...</div>');
         // Same callback as delete... very similary except keep the row elsewhere in the DB
         var callback = 'jsf_removewdrowsimple_return';
         
         var url='';
         url += defaultremotedomain + jsfwd_servercontroller;
         url += '&action=removeforeignlink';
         url += '&callback=' + callback;
         url += '&linkid=' + encodeURIComponent(linkid);
         url += '&wd_id=' + encodeURIComponent(wd_id);
         url += '&wd_row_id=' + encodeURIComponent(wd_row_id);
         url += '&origemail=' + encodeURIComponent(origemail);
         url += '&userid=' + encodeURIComponent(jsfwd_userid);
         url += '&token=' + encodeURIComponent(jsfwd_token);
         url += '&divid=' + encodeURIComponent(divid);
      
         if (jsfwd_testing) alert('URL: ' + url);
         //alert('URL: ' + url);
      
         jsfwebdata_CallJSONP(url);
      }
   }
}

// #API
function jsf_removewdrow_jsonp(wd_id,wd_row_id,domain,userid,filterstr,limit,page,maxcol,callback,prefix,testing){
   //alert('jsf_removewdrow_jsonp(' + wd_id + ',' + wd_row_id + ')');
   if (Boolean(testing)) jsfwd_testing=true;
   //testing=true;
   if (!Boolean(domain)) domain=defaultremotedomain;
   defaultremotedomain = domain;
   if (!Boolean(callback)) callback='jsfwebdata_tabledisplay';
   if (!Boolean(prefix)) prefix='jsfwd';
   if (!Boolean(limit)) limit=25;
   if (!Boolean(page)) page=1;
   var a_limit = 'LIMIT ' + ((page-1)*limit).toString() + ',' + (limit+1).toString();
   jsfwd_pagenum = page;
   jsfwd_limit = limit;

   if (jsfwd_testing) alert(defaultremotedomain + ', ' + callback + ', ' + prefix);

   var url = defaultremotedomain + jsfwd_servercontroller + '&action=removewdrow';
   url = url + '&callback=' + encodeURIComponent(callback);
   url = url + '&enabledonly=1';
   url = url + '&addfiltering=1';
   url = url + '&addrowdisplay=1';
   if (Boolean(wd_id)) url = url + '&wd_id=' + encodeURIComponent(wd_id);
   if (Boolean(wd_row_id)) url = url + '&wd_row_id=' + encodeURIComponent(wd_row_id);
   if (Boolean(prefix)) url = url + '&prefix=' + encodeURIComponent(prefix);
   if (Boolean(userid)) url = url + '&userid=' + encodeURIComponent(userid);
   if (Boolean(filterstr)) url = url + '&filterstr=' + encodeURIComponent(filterstr);
   if (Boolean(a_limit)) url = url + '&limit=' + encodeURIComponent(a_limit);

   if (jsfwd_testing) alert('URL: ' + url);
   //alert('URL: ' + url);
   jsfwd_rowid = '';
   
   jsfwebdata_CallJSONP(url);
   //jsfwebdata_CallJSONP_inline(url);
}




//----------------------------------------------------------------------------- 
// Show table stats (answers/data)
//----------------------------------------------------------------------------- 
function jsf_getwdsavedstats_jsonp(domain,userid,orgid,wd_id,callback){
   if (!Boolean(domain)) domain=defaultremotedomain;
   defaultremotedomain = domain;
   if (!Boolean(callback)) callback='jsfwebdata_tablesavedstat';

   var url = defaultremotedomain + jsfwd_servercontroller + '&action=getwdsavedstats';
   url = url + '&callback=' + encodeURIComponent(callback);
   url = url + '&userid=' + encodeURIComponent(userid);
   if (Boolean(orgid)) url = url + '&orgid=' + encodeURIComponent(orgid);
   if (Boolean(wd_id)) url = url + '&wd_id=' + encodeURIComponent(wd_id);
   
   //alert('URL: ' + url);

   jsfwebdata_CallJSONP(url);
   //jsfwebdata_CallJSONP_inline(url);
}

function jsfwebdata_tablesavedstat(jsondata){
   //alert(JSON.stringify(jsondata));
   if (typeof jsf_endjsoning == 'function') jsf_endjsoning(jsondata);
   var slist = jsondata.statlist;
   t = [];
   t['name'] = '<b>New Report</b>';
   t['onclick'] = 'jsf_getwdsearch_jsonp(\'\',' + jsondata.wd_id + ');';
   slist.push(t);
   jQuery('#jsfwdarea').html(getListHTML(slist));
}





function jsf_getwdsearch_jsonp(domain,wd_id,callback){
   if (!Boolean(domain)) domain=defaultremotedomain;
   defaultremotedomain = domain;
   if (!Boolean(callback)) callback='jsfwebdata_searchform';

   var url = defaultremotedomain + jsfwd_servercontroller + '&action=getwdsearch';
   url = url + '&callback=' + encodeURIComponent(callback);
   url = url + '&wd_id=' + encodeURIComponent(wd_id);
   
   //alert('URL: ' + url);

   jsfwebdata_CallJSONP(url);
   //jsfwebdata_CallJSONP_inline(url);
}

function jsfwebdata_searchform(jsondata){
   //alert(JSON.stringify(jsondata));
   if (typeof jsf_endjsoning == 'function') jsf_endjsoning(jsondata);
   var searchhtml = '<div style=\"padding:8px;\" style=\"clear:both;font-size:14px;font-family:verdana;\">';
   searchhtml = searchhtml + '<div style=\"margin-top:6px;margin-bottom:1px;\">Choose which variables to query by:</div>';
   searchhtml = searchhtml + jsondata.html;
   searchhtml = searchhtml + '<div style=\"clear:both;width:1px;height:1px;overflow:hidden;\"></div>';
   
   if(Boolean(jsondata.fields)) {
 	    searchhtml = searchhtml + '<div id=\"reportfields\" style=\"margin-top:8px;margin-bottom:8px;padding-top:5px;border-top:1px solid #AAAAAA;>';
 	    searchhtml = searchhtml + '<div style=\"margin-top:6px;margin-bottom:6px;\">Choose which graphs to display in this report</div>';
		 for (var i=0;i<jsondata.fields.length;i++){
		 	  searchhtml = searchhtml + '<div style=\"margin-top:1px;margin-bottom:1px;\"><input type=\"checkbox\" name=\"reportfield[]\" value=\"' + jsondata.fields[i].field_id + '\" CHECKED> ' + jsondata.fields[i].label + '</div>';
		 }
		 searchhtml = searchhtml + '</div>';
   }
   
   searchhtml = searchhtml + '<div style=\"margin-top:4px;margin-bottom:6px;\">';
   searchhtml = searchhtml + 'Frequency: ';
   searchhtml = searchhtml + '<select name=\"jsfwd_frequency\">';
   searchhtml = searchhtml + '<option value=\"None\">None</option>';
   searchhtml = searchhtml + '<option value=\"Weekly\">Weekly</option>';
   searchhtml = searchhtml + '<option value=\"Monthly\">Monthly</option>';
   searchhtml = searchhtml + '<option value=\"Quarterly\">Quarterly</option>';
   searchhtml = searchhtml + '</select>';
   searchhtml = searchhtml + '</div>';
   searchhtml = searchhtml + '<div style=\"margin-top:4px;margin-bottom:6px;\">';
   searchhtml = searchhtml + 'Report Name: <input type=\"text\" name=\"jsfwd_searchname\" id=\"jsfwd_searchname\" style=\"font-size:12px;font-family:verdana;width:150px;\">';
   searchhtml = searchhtml + '</div>';
   searchhtml = searchhtml + '<div style=\"padding:5px;border:1px solid #797979;border-radius:4px;background-color:#DCDCDC;font-family:verdana;font-size:12px;cursor:pointer;text-align:center;width:150px;\" ';
   searchhtml = searchhtml + 'onclick=\"saveWDReport();\"';
   searchhtml = searchhtml + '>';
   searchhtml = searchhtml + 'Save</div>';
   searchhtml = searchhtml + '</div>';
   searchhtml = searchhtml + '\n<script>\n';
   searchhtml = searchhtml + 'function saveWDReport() {\n';
   searchhtml = searchhtml + '  var temp=jsfwd_retrunsearchfields();\n';
   searchhtml = searchhtml + '  var str = \'\';\n';
   searchhtml = searchhtml + '  for(var i=0;i<temp.length;i++){\n';
   searchhtml = searchhtml + '    var t = jQuery(\'#\' + temp[i]).val();\n';
   searchhtml = searchhtml + '    if(Boolean(t)){\n';
   searchhtml = searchhtml + '      str = str + \'&\' + temp[i] + \'=\' + t;\n';
   searchhtml = searchhtml + '    }\n';
   searchhtml = searchhtml + '  }\n';
   searchhtml = searchhtml + '  var searchname = jQuery(\'#jsfwd_searchname\').val();\n';
   //searchhtml = searchhtml + '  alert(\'Name: \' + searchname);\n';
   searchhtml = searchhtml + '  var frequency = jQuery(\'#jsfwd_frequency\').val();\n';
   searchhtml = searchhtml + '  var wd_id = \'' + jsondata.wd_id + '\';\n';
   //searchhtml = searchhtml + '  var field_id = \'\';\n';
   searchhtml = searchhtml + '  var field_id = jQuery(\'#reportfields input:checkbox:checked\').map(function() {return this.value;}).get().join(\',\');\n';
   //searchhtml = searchhtml + '  alert(\'Field_id: \' + field_id);\n';
   searchhtml = searchhtml + '  var enabled = \'Yes\';\n';
   searchhtml = searchhtml + '  var sequence = \'100\';\n';
   searchhtml = searchhtml + '  jsf_savewdstat_jsonp(wd_id,field_id,str,searchname,globaluser.userid,enabled,sequence,frequency);\n';
   searchhtml = searchhtml + '}\n';
   searchhtml = searchhtml + '</script>\n';
   jQuery('#jsfwdarea').html(searchhtml);
}


function jsf_savewdstat_jsonp(wd_id,field_id,params,reportname,userid,enabled,sequence,frequency,domain,callback){
   if (!Boolean(domain)) domain=defaultremotedomain;
   defaultremotedomain = domain;
	if (!Boolean(callback)) callback='jsfwebdata_returnsavedstat';

   var url = defaultremotedomain + jsfwd_servercontroller + '&action=newwdsavedstat';
   url = url + '&callback=' + encodeURIComponent(callback);
   url = url + '&wd_id=' + encodeURIComponent(wd_id);
   if(Boolean(field_id)) url = url + '&field_id=' + encodeURIComponent(field_id);
   if(Boolean(params)) url = url + '&params=' + encodeURIComponent(params);
   url = url + '&reportname=' + encodeURIComponent(reportname);
   url = url + '&userid=' + encodeURIComponent(userid);
   if(Boolean(enabled)) url = url + '&enabled=' + encodeURIComponent(enabled);
   if(Boolean(sequence)) url = url + '&sequence=' + encodeURIComponent(sequence);
   if(Boolean(frequency)) url = url + '&frequency=' + encodeURIComponent(frequency);
	 
   //alert('URL: ' + url);

   jsfwebdata_CallJSONP(url);
   //jsfwebdata_CallJSONP_inline(url);
}

function jsfwebdata_returnsavedstat(jsondata){
	if (typeof jsf_endjsoning == 'function') jsf_endjsoning(jsondata);
   jsf_getwdsavedstats_jsonp('',jsondata.userid,'',jsondata.wd_id);
}


function jsf_getwdstats_jsonp(wdname,wd_id,width,domain,userid,token,filterstr,callback,prefix,urlparams,title,testing){
   if (Boolean(testing)) jsfwd_testing=true;
   if (!Boolean(domain)) domain=defaultremotedomain;
   defaultremotedomain = domain;
   if (!Boolean(callback)) callback='jsfwebdata_tablestat';
   if (!Boolean(prefix)) prefix='jsfwd';

   if (jsfwd_testing) alert(defaultremotedomain + ', ' + callback + ', ' + prefix);

   var url = defaultremotedomain + jsfwd_servercontroller + '&action=getwdstats';
   url = url + '&callback=' + encodeURIComponent(callback);
   url = url + '&enabledonly=1';
   if(Boolean(width)) url = url + '&width=' + encodeURIComponent(width);
   if(Boolean(userid)) url = url + '&userid=' + encodeURIComponent(userid);
   if(Boolean(token)) url = url + '&token=' + encodeURIComponent(token);
   if(Boolean(title)) url = url + '&title=' + encodeURIComponent(title);
   if(Boolean(wdname)) url = url + '&wdname=' + encodeURIComponent(wdname);
   if(Boolean(wd_id)) url = url + '&wd_id=' + encodeURIComponent(wd_id);
   //if (Boolean(field_id)) url = url + '&field_id=' + encodeURIComponent(field_id);
   if(Boolean(prefix)) url = url + '&prefix=' + encodeURIComponent(prefix);
   if(Boolean(filterstr)) url = url + '&filterstr=' + encodeURIComponent(filterstr);
   
   if(Boolean(urlparams)) url = url + urlparams;
   //if ((names instanceof Array) && (values instanceof Array)) {
   //   for (var i=0; i<names.length; i++) {
   //      if (Boolean(values[i]) && Boolean(names[i])) url = url + '&' + names[i] + '=' + encodeURIComponent(values[i]);
   //   }
   //}
   
   //alert('URL: ' + url);
   if (jsfwd_testing) alert('URL: ' + url);

   jsfwebdata_CallJSONP(url);
   //jsfwebdata_CallJSONP_inline(url);
}

function jsfwebdata_tablestat(jsondata){
   //alert(JSON.stringify(jsondata));
   if (typeof jsf_endjsoning == 'function') jsf_endjsoning(jsondata);
   jQuery('#jsfwdarea').html(jsondata.stathtml);
}


function jsfwebdata_tableurl(param,divid,cnt,url){
   if(!Boolean(cnt)) cnt = 0;
   cnt = parseInt(cnt);
   
   var t_divid;
   var t_param;
   
   for(var i=0;i<cnt;i++) {
      t_divid = divid + '_' + i;
      t_param = param + '_' + i;
      url = jsfwebdata_buildurl(t_param,t_divid,url);
   }
   return url;
}

function jsfwebdata_buildurl(param,divid,url){
   var temp = jQuery('#' + divid).val();
   if(!Boolean(temp)) temp='%E%';
   if(!Boolean(url)) url = '';
   url += '&' + param + '=' + encodeURIComponent(jsfwebdata_convertstring(temp));
   return url;
}

function jsfwebdata_convertstring(str){
   var temp = '';
   if(Boolean(str)) {
      // Remove any non-ascii character
      temp = str.replace(/[^\x00-\x7F]/g, "");
      temp = jsfwebdata_replaceAll('\n','<br>',temp);
      temp = jsfwebdata_replaceAll('\r','',temp);
   }
   return temp;
}

function jsfwebdata_convertback(str){
   var temp = '';
   if(Boolean(str)) {
      temp = jsfwebdata_replaceAll('<br>','\n',temp);
   }
   return temp;
}

function jsfwebdata_replaceAll(find, replace, str) {
  if(Boolean(str)) str = str.replace(new RegExp(find, 'g'), replace);
  return str;
}



//----------------------------------------------------
// Embed a poll on your page using this method:
//
// defaultremotedomain = 'https://www.plasticsmarkets.org/';
// jsfpoll_getpublicpoll('#chadpoll');
//
// Note: your jdata table must have 
//   1. hashtags: #active and #poll
//   2. The hashtag you send into the function
//   3. And must be type: website data, public survey, or mobile survey
// This allows polls to dynamically change on the backend without changing the webiste's HTML
// You can achieve this by removing #active on one poll, and adding to another
// If more than 1 poll has these requirements one of them will be chosen randomly
//----------------------------------------------------
var jsfpoll_currentwdid;
function jsfpoll_getpublicpoll(htag) {
   jsfpoll_currentwdid = '';
   if(Boolean(htag)) {
      //alert('searching for htag: ' + htag);
      jsf_searchforwd('','','#active #poll ' + htag,'','','',25,'jsfpoll_searchforwd_return');
   }
}

function jsfpoll_searchforwd_return(jsondata) {
   if (Boolean(jsondata) && Boolean(jsondata.results) && jsondata.results.length>0) {
      
      // randomly select one of the qualifying polls
      var rn = Math.floor(Math.random() * jsondata.results.length);
      
      jsfpoll_currentwdid = jsondata.results[rn].wd_id;
      //decide if we show this, or show stat results
      var wd_row_id = window.localStorage.getItem('jsfpoll_' + jsfpoll_currentwdid);
      if(Boolean(wd_row_id)) {
         jsf_getwdstats_jsonp('',jsfpoll_currentwdid,jQuery('#jsfwdarea').width());
      } else {
         //jsf_getwebdata_jsonp(wdname,domain,callback,wd_id,prefix,userid,wd_row_id,testing,forcefull,msg,xtra)
         jsf_getwebdatapage_jsonp(jsfpoll_currentwdid);
      }
   } else {
      jQuery('#jsfwdarea').html('');
   }
}
 
function jsfpoll_jsfwdpagedcallback_end(jsondata){
   if(jsondata.wd_id == jsfpoll_currentwdid) {
      window.localStorage.setItem('jsfpoll_' + jsondata.wd_id,jsondata.wd_row_id);
      jsf_getwdstats_jsonp('',jsondata.wd_id,jQuery('#jsfwdarea').width());
      jsfpoll_currentwdid = '';
   }
}



//----------------------------------------------------------------------------------
// user account access
//----------------------------------------------------------------------------------
function jsf_logout_jsonp(){
   jsfwd_userid = '';
   jsfwd_token = '';
   jsfwebdata_DeleteCookie('jsfwd_userid');
   jsfwebdata_DeleteCookie('jsfwd_token');
}

function jsf_login_jsonp(email,password,callback,domain,testing){
   if (Boolean(testing)) jsfwd_testing=true;
   if (!Boolean(domain)) domain=defaultremotedomain;
   defaultremotedomain = domain;
   if (!Boolean(callback)) callback='jsfwebdata_logincallback';

   if (jsfwd_testing) alert(defaultremotedomain + ', ' + callback + ', ' + prefix);

   var url = defaultremotedomain + jsfwd_servercontroller + '&action=login';
   url = url + '&callback=' + encodeURIComponent(callback);
   url = url + '&email=' + encodeURIComponent(email);
   url = url + '&password=' + encodeURIComponent(password);

   if (jsfwd_testing) alert('URL: ' + url);

   jsfwebdata_CallJSONP(url);
   //jsfwebdata_CallJSONP_inline(url);
}


function jsfwebdata_logincallback(jsondata){
   if (typeof jsf_endjsoning == 'function') jsf_endjsoning(jsondata);
   if (jsondata.responsecode==1) {
      jsfwd_userid = jsondata.user.userid;
      jsfwd_token = jsondata.user.token;
      jsfwebdata_SetCookie('jsfwd_userid',jsfwd_userid);
      jsfwebdata_SetCookie('jsfwd_token',jsfwd_token);
      if (typeof jsfwd_loginsuccess == 'function') jsfwd_loginsuccess(jsondata);
   } else {
      if (typeof jsfwd_loginfail == 'function') jsfwd_loginfail();
   }
}


//----------------------------------------------------------------------------------
// Useful Cookie manipulation functions...
//----------------------------------------------------------------------------------
function jsfwebdata_getCookieDomain(){
   var url = document.URL;

   var beginindex=url.indexOf("://");
   if (beginindex<0) beginindex=0;
   else beginindex += 3;

   var str=url.substring(beginindex);
   var endindex=str.indexOf(":");
   if (endindex<0) endindex=str.indexOf("/");
   if (endindex<0) endindex=str.length;
   str = str.substring(0,endindex);

   var temp = (str.split(".").length) - 2;
   for (var i=0; i<temp; i++) {
      beginindex=str.indexOf(".")+1;
      str=str.substring(beginindex);
   }
   str = "." + str;

   return str;
}

function jsfwebdata_DeleteCookie(name){
   document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT; path=/; domain=' + jsfwebdata_getCookieDomain();
}

function jsfwebdata_SetCookie(name,value,shorttime){
   var today = new Date();
   var expires = today.getTime() + (365 * 1000 * 60 * 60 * 24);
   if(shorttime==1) expires = today.getTime() + (1000 * 60 * 5);
   else if(shorttime==2) expires = today.getTime() + (1000 * 60 * 60);
   var expires_date = new Date(expires);
   
   var cookieStr = name + '=' + escape( value ) + '; ';
   cookieStr = cookieStr + 'expires=' + expires_date.toGMTString() + '; path=/; ';
   cookieStr = cookieStr + 'domain=' + jsfwebdata_getCookieDomain();

   document.cookie = cookieStr;
}

function jsfwebdata_GetCookie(name) {
	var a_all_cookies = document.cookie.split( ';' );
	var a_temp_cookie = '';
	var cookie_name = '';
	var cookie_value = '';
	var b_cookie_found = false;
   for (var i = 0; i < a_all_cookies.length; i++ ){
		a_temp_cookie = a_all_cookies[i].split('=');
		cookie_name = a_temp_cookie[0].replace(/^\s+|\s+$/g, '');
		if ( cookie_name == name ) {
			b_cookie_found = true;
			if ( a_temp_cookie.length > 1 ) {
				cookie_value = unescape( a_temp_cookie[1].replace(/^\s+|\s+$/g, '') );
			}
			break;
		}
		a_temp_cookie = null;
		cookie_name = '';
	}
   return cookie_value;
}

function jsfwebdata_getDateTime() {
    var now     = new Date(); 
    var year    = now.getFullYear();
    var month   = now.getMonth()+1; 
    var day     = now.getDate();
    var hour    = now.getHours();
    var minute  = now.getMinutes();
    var second  = now.getSeconds();
    
    if(month.toString().length == 1) var month = '0'+month;
    if(day.toString().length == 1) var day = '0'+day;   
    if(hour.toString().length == 1) var hour = '0'+hour;
    if(minute.toString().length == 1) var minute = '0'+minute;
    if(second.toString().length == 1) var second = '0'+second;
    
    var datetimestr = day + '/' + month + '/' + year + ' ' + hour + ':' + minute + ':' + second;   
    return datetimestr;
}



//For a color wheel...
var jsfwd_clr_a;
var jsfwd_clr_b;
var jsfwd_clr_c;

function jsfwd_createSpectrum(divid,oc){
   jsfwd_clr_a = 15;
   jsfwd_clr_b = 0;
   jsfwd_clr_c = 0;
   var str = '';
   str += '<div id=\"pltclr_' + divid + '\" style=\"width:190px;height:15px;overflow:hidden;border:1px solid #EDEDED;border-radius:3px;margin-top:5px;margin-bottom:5px;\"></div>';
   str += jsfwd_colorstep('b','inc',divid,oc);
   str += jsfwd_colorstep('a','dec',divid,oc);
   str += jsfwd_colorstep('c','inc',divid,oc);
   str += jsfwd_colorstep('b','dec',divid,oc);
   str += jsfwd_colorstep('a','inc',divid,oc);
   str += jsfwd_colorstep('c','dec',divid,oc);
   str += '<div style=\"clear:both;\"></div>';
   str += '<div id=\"plt2_' + divid + '\" style=\"margin-top:5px;\"></div>';
   jQuery('#plt_' + divid).html(str);
   jQuery('#pltclr_' + divid).css('background-color',jQuery('#' + divid).val());
}

function jsfwd_colorstep(col,dir,divid,oc){
   var str = '';
   for(var i=0;i<15;i++){
      var rd = jsfwd_clr_a.toString(16);
      var gr = jsfwd_clr_b.toString(16);
      var bl = jsfwd_clr_c.toString(16);
      var clr = rd + rd + gr + gr + bl + bl;

      str += '<div style=\"float:left;width:2px;height:20px;overflow:hidden;background-color:#' + clr + ';\" ';
      str += 'onmouseover=\"jQuery(\'#pltclr_' + divid + '\').css(\'background-color\',\'#' + clr + '\');\" ';
      str += 'onmouseout=\"jQuery(\'#pltclr_' + divid + '\').css(\'background-color\',jQuery(\'#' + divid + '\').val());\" ';
      str += 'onclick=\"';
      str += 'jQuery(\'#' + divid + '\').val(\'#' + clr + '\');';
      str += 'jsfwd_shade(' + jsfwd_clr_a + ',' + jsfwd_clr_b + ',' + jsfwd_clr_c + ',\'' + divid + '\');';
      if(Boolean(oc)) str += oc;
      str += '\"></div>';
      
      if(col=='a' && dir=='inc') {
         // 0000ff - ff00ff
         jsfwd_clr_a++;
      } else if(col=='a' && dir=='dec') {
         // ffff00 - 00ff00
         jsfwd_clr_a--;
      } else if(col=='b' && dir=='inc') {
         // ff0000 - ffff00
         jsfwd_clr_b++;
      } else if(col=='b' && dir=='dec') {
         // 00ffff - 0000ff
         jsfwd_clr_b--;
      } else if(col=='c' && dir=='inc') {
         // 00ff00 - 00ffff
         jsfwd_clr_c++;
      } else if(col=='c' && dir=='dec') {
         // ff00ff - ff0000
         jsfwd_clr_c--;
      }      
   }
   return str;
}

function jsfwd_shade(orig_a,orig_b,orig_c,divid){
   var str = '';
   for(var i=0;i<=25;i++){
         var a = Math.round(orig_a * ((4*i)/100));
         var b = Math.round(orig_b * ((4*i)/100));
         var c = Math.round(orig_c * ((4*i)/100));
         
         var rd = a.toString(16);
         var gr = b.toString(16);
         var bl = c.toString(16);
         
         var clr = rd + rd + gr + gr + bl + bl;
         str += '<div style=\"float:left;width:4px;height:20px;overflow:hidden;background-color:#' + clr + ';\" ';
         str += 'onmouseover=\"jQuery(\'#pltclr_' + divid + '\').css(\'background-color\',\'#' + clr + '\');\" ';
         str += 'onmouseout=\"jQuery(\'#pltclr_' + divid + '\').css(\'background-color\',jQuery(\'#' + divid + '\').val());\" ';
         str += 'onclick=\"';
         str += 'jQuery(\'#' + divid + '\').val(\'#' + clr + '\');';
         str += '\"></div>';
   }
   for(var i=0;i<=25;i++){
         var a = orig_a + Math.round((15-orig_a) * ((4*i)/100));
         var b = orig_b + Math.round((15-orig_b) * ((4*i)/100));
         var c = orig_c + Math.round((15-orig_c) * ((4*i)/100));
         
         var rd = a.toString(16);
         var gr = b.toString(16);
         var bl = c.toString(16);
         
         var clr = rd + rd + gr + gr + bl + bl;
         str += '<div style=\"float:left;width:4px;height:20px;overflow:hidden;background-color:#' + clr + ';\" ';
         str += 'onmouseover=\"jQuery(\'#pltclr_' + divid + '\').css(\'background-color\',\'#' + clr + '\');\" ';
         str += 'onmouseout=\"jQuery(\'#pltclr_' + divid + '\').css(\'background-color\',jQuery(\'#' + divid + '\').val());\" ';
         str += 'onclick=\"jQuery(\'#' + divid + '\').val(\'#' + clr + '\');\"></div>';
   }
   str += '<div style=\"clear:both;\"></div>';   
   jQuery('#plt2_' + divid).html(str);
}
