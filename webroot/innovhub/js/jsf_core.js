//////////////////////////////////////////////////
// jStoreFront Core widget
// 190905
//
// Copyright 2018, 2019 (c) jStoreFront
//
// Extenstion Points
//   jsfcore_custominit() - initialize app
//   jsfcore_customcontroller() - entry point into app
//   jsfcore_customstructure() - change header, or other elements
//   jsfcore_custommenu() - to create your own menu objects
//      expected fields: title,onclick,url,reqlogin,forheader,forfooter,forhtabs
//   jsfcore_customdrawmenu() - to offer a different layout of menu
//   jsfcore_customtogglemenu(closeonly) - add custom logic to animate menu
//   jsfcore_customcopyright
//
//////////////////////////////////////////////////

var jsfcore_ht = 'rsad';
var jsfcore_wd_menu;
var jsfcore_wd_pages;
var jsfcore_wd_pages_override;

var jsfcore_divid = 'pmcs';

var jsfcore_header_bgcolor = '#000000';
var jsfcore_header_color = '#FFFFFF';
var jsfcore_header_opacity = '0.95';
var jsfcore_header_height = 60;
var jsfcore_footer_height = 75;
var jsfcore_footer_bgcolor = '#717f86';
var jsfcore_bg_clr_bgcolor = '#000000';
var jsfcore_bg_clr_opacity = '0.85';
var jsfcore_bg_img_bgcolor = '#000000';
var jsfcore_menuiconcolor = '#BBBBBB';
var jsfcore_menu_bgcolor = '#000000';
var jsfcore_menu_color = '#FFFFFF';
var jsfcore_menu_opacity = '0.90';
var jsfcore_tabbedmenuitemid = '';

var jsfcore_forcehttps = true;
var jsfcore_basedir = '/';
var jsfcore_internallinks = true;

var jsfcore_usehash = false;
var jsfcore_currhash = '';


var jsfcore_logo;
var jsfcore_max_logowd;

var jsfcore_allowdevmode = false;
var jsfcore_useaccounts = false;
var jsfcore_newaccount = false;
var jsfcore_domain = 'https://www.plasticsmarkets.org/';
var jsfcore_referenceurl = jsfcore_domain;
//defaultremotedomain = jsfcore_domain;
var jsfcore_addhometomenu = true;

var jsfcore_font = '\'Roboto\', sans-serif';
var jsfcore_fontfamily = 'font-family: ' + jsfcore_font + ';';

var jsfcore_alwayssandwich = false;

var jsfcore_testing = false;

//For password reset
var jsfcore_fromemail = 'help@jstorefront.com';
var jsfcore_sitetitle = 'jStorefront';

var jsfcore_servercontroller = 'jsfcode/jsonpcontroller.php?jodon=1';


//////////////////////////////////////////////////
// Internal variables

var jsfcore_displayFunct;
var jsfcore_displayParam;
var jsfcore_historyscr=[];
var jsfcore_historyparam=[];
var jsfcore_historycnt=0;

var jsfcore_next_displayfunct;
var jsfcore_next_successmessage;
var jsfcore_next_errormessage;


var jsfcore_winwidth;
var jsfcore_globalwidth;
var jsfcore_globalwidth_pgbldr;
var jsfcore_globalmaxwidth=0;
var jsfcore_headermaxwidth=1100;
var jsfcore_menucollapsewidth=720;
var jsfcore_globalheight;
var jsfcore_mobile = false;


var jsfcore_resizing = false;
var jsfcore_delta = 1000;
function jsfcore_resizestart(){
   if (!jsfcore_resizing) {
      jsfcore_resizing = true;
      setTimeout(jsfcore_resizeend, jsfcore_delta);
   }
}

function jsfcore_resizeend() {
      var t_width = jQuery(window).width();
      if (t_width!=jsfcore_winwidth) {
         jsfcore_fixwidths();
         jsfcore_resizing = false;
      }
}


function jsfcore_QuickJSON(action,callback,query,checkcache,noloading){
   var runjson = true;
   var saveurl = '';
   if (Boolean(action) && Boolean(callback)) {
      var url = jsfcore_domain + jsfcore_servercontroller + '&action=' + encodeURIComponent(action);
      if (Boolean(query)) url = url + query;
      
      saveurl = url;
      url = url + '&callback=' + encodeURIComponent(callback);
      
      if(Boolean(jsfcore_testing)) alert('Calling json URL: ' + url);
      
      if(Boolean(checkcache)) {
         //alert('checking cache: ' + url);
         var str = window.localStorage.getItem('jsfcore_cache');
         if(Boolean(str)){
            //alert('found cache: ' + url);
            var jsf_cache = JSON.parse(str);
            if(jsf_cache.expiry<(Math.floor(Date.now() / 1000))) {
               //alert('expired cache: ' + url);
               jsf_cache = '';
               window.localStorage.removeItem('jsfcore_cache');
            } else if(Boolean(jsf_cache[saveurl])) {
               //alert('URL in cache: ' + url);
               var fn = window[callback];
               if(typeof fn === 'function') {
                  runjson = false;
                  //alert('using cache: ' + url);
                  fn(jsf_cache[saveurl]);
               }            
            }            
         }
      }

      if(runjson) {
         if(!Boolean(noloading)) jsfcore_showloading();
         if(Boolean(checkcache)) url += '&jsonsaveval=' + encodeURIComponent(saveurl);
         //alert('NOT using cache: ' + url);
         jsfcore_CallJSONP(url);
      }
   }
   return saveurl;
}

function jsfcore_removefromcache(cacheid){
   var str = window.localStorage.getItem('jsfcore_cache');
   if(Boolean(str)){
      var jsf_cache = JSON.parse(str);
      jsf_cache[cacheid] = '';
      window.localStorage.setItem('jsfcore_cache',JSON.stringify(jsf_cache));
   }
}

function jsf_endjsoning(jsondata) {
   jsfcore_ReturnJSON(jsondata);
}

function jsfcore_ReturnJSON(jsondata){
   jsfcore_hideloading();
   //alert(JSON.stringify(jsondata));
   if (Boolean(jsondata) && Boolean(jsondata.jsonsaveval)) {
      //alert('CHJ***** checking cache: jsf_endjsoning  url: ' + jsondata.jsonsaveval);
      var jsf_cache = {};
      jsf_cache.expiry = (Math.floor(Date.now() / 1000) + (60*60*24));
      jsf_cache.countindex = 1;
      var str = window.localStorage.getItem('jsfcore_cache');
      window.localStorage.removeItem('jsfcore_cache');
      if(Boolean(str)) {
         //alert('found jsf_cache, checking expiry...');
         temp = JSON.parse(str);
         if(Boolean(temp) && temp.expiry>(Math.floor(Date.now() / 1000)) && temp.countindex<150) {
            jsf_cache = temp;
         }
      }
      if(!Boolean(jsf_cache[jsondata.jsonsaveval])) jsf_cache.countindex++;
      jsf_cache[jsondata.jsonsaveval] = jsondata;
      window.localStorage.setItem('jsfcore_cache',JSON.stringify(jsf_cache));
   }
}

function jsfcore_CallJSONP(url) {
    var script = document.createElement('script');
    script.setAttribute('src', url);
    document.getElementsByTagName('head')[0].appendChild(script);
}
 
function jsfcore_AlertJSONPRequest(jsondata){
   jsfcore_ReturnJSON(jsondata);
   alert(JSON.stringify(jsondata));
}

function jsfcore_donothing(jsondata){
   jsfcore_ReturnJSON(jsondata);   
   //alert(JSON.stringify(jsondata));
}

function jsfcore_ismobile() {
   var retval = false;
   if(jsfcore_winwidth<600) retval = true;
   return retval;
}

function jsfcore_isbrowser() {
   var retval = false;
   if(jsfcore_winwidth>760) retval = true;
   return retval;
}

var jsfcore_col=[];
function jsfcore_updateclasses(jsondata){
   //alert('updating classes');
   var min_x = 65;
   
   var fontsz42 = 42;
   var fontsz36 = 36;
   var fontsz30 = 30;
   var fontsz28 = 28;
   var fontsz26 = 26;
   var fontsz20 = 20;
   var fontsz18 = 18;
   var fontsz16 = 16;
   var fontsz14 = 14;
   var fontsz12 = 12;
   var padsz20 = 20;
   var padsz15 = 15;
   var padsz10 = 10;
   if(jsfcore_globalwidth>1200) {
      fontsz42 += 2;
      fontsz36 += 2;
      fontsz30 += 2;
      fontsz28 += 2;
      fontsz26 += 2;
      fontsz20 += 2;
      fontsz18 += 2;
   } else if(jsfcore_globalwidth<480) {
      fontsz42 = 28;
      fontsz36 = 24;
      fontsz30 = 20;
      fontsz28 = 18;
      fontsz26 = 16;
      fontsz20 = 14;
      fontsz18 = 12;
      fontsz16 = 10;
      fontsz14 = 10;
      fontsz12 = 8;
      padsz20 = 8;
      padsz15 = 6;
      padsz10 = 4;
   } else if(jsfcore_globalwidth<760) {
      fontsz42 = 34;
      fontsz36 = 30;
      fontsz30 = 28;
      fontsz28 = 26;
      fontsz26 = 22;
      fontsz20 = 18;
      fontsz18 = 16;
      fontsz16 = 14;
      fontsz14 = 12;
      fontsz12 = 10;
      padsz20 = 12;
      padsz15 = 9;
      padsz10 = 6;
   }
   jQuery('.fontsz42').css('font-size',fontsz42 + 'px');
   jQuery('.fontsz36').css('font-size',fontsz36 + 'px');
   jQuery('.fontsz30').css('font-size',fontsz30 + 'px');
   jQuery('.fontsz28').css('font-size',fontsz28 + 'px');
   jQuery('.fontsz26').css('font-size',fontsz26 + 'px');
   jQuery('.fontsz20').css('font-size',fontsz20 + 'px');
   jQuery('.fontsz18').css('font-size',fontsz18 + 'px');
   jQuery('.fontsz16').css('font-size',fontsz16 + 'px');
   jQuery('.fontsz14').css('font-size',fontsz14 + 'px');
   jQuery('.fontsz12').css('font-size',fontsz12 + 'px');
   jQuery('.padsz20').css('padding',padsz20 + 'px');
   jQuery('.padsz15').css('padding',padsz15 + 'px');
   jQuery('.padsz10').css('padding',padsz10 + 'px');
   
   var x = Math.floor(jsfcore_globalwidth/12);
   
   if(jsfcore_ismobile()) {
      jQuery('.browseronly').hide();
      jQuery('.mobileonly').show();
      jQuery('.browserandtablet').hide();
      jQuery('.mobileandtablet').show();
   } else if(jsfcore_isbrowser()) {
      jQuery('.browseronly').show();
      jQuery('.mobileonly').hide();
      jQuery('.browserandtablet').show();
      jQuery('.mobileandtablet').hide();
   } else {
      jQuery('.browseronly').hide();
      jQuery('.mobileonly').hide();
      jQuery('.browserandtablet').show();
      jQuery('.mobileandtablet').show();
   }
   
   for (var i=1;i<=12;i++){
      var t = i * x;
      if(x<min_x) t = jsfcore_globalwidth;
      jQuery('.col' + i).css('width', t + 'px').css('float','left');
      jQuery('.img' + i).css('width', t + 'px').css('height','auto');
      jsfcore_col[i] = t;
      var t2 = Math.floor((t/16)*9);
      jQuery('.youtube' + i).css('width', t + 'px').css('height', t2 + 'px');
   }
   
   x = Math.floor(jsfcore_globalwidth/4);
   var y = Math.floor(jsfcore_globalwidth/2);
   if(x<100) {
      //both are globalwidth
      jQuery('.qcol1').css('width', jsfcore_globalwidth + 'px').css('float','left');
      jQuery('.qimg1').css('width', jsfcore_globalwidth + 'px').css('height','auto');
      jQuery('.qcol2').css('width', jsfcore_globalwidth + 'px').css('float','left');
      jQuery('.qimg2').css('width', jsfcore_globalwidth + 'px').css('height','auto');
   } else if(x<170) {
      //2 is ok by itself
      jQuery('.qcol1').css('width', y + 'px').css('float','left');
      jQuery('.qimg1').css('width', y + 'px').css('height','auto');
      jQuery('.qcol2').css('width', jsfcore_globalwidth + 'px').css('float','left');
      jQuery('.qimg2').css('width', jsfcore_globalwidth + 'px').css('height','auto');
   } else {
      jQuery('.qcol1').css('width', x + 'px').css('float','left');
      jQuery('.qimg1').css('width', x + 'px').css('height','auto');
      jQuery('.qcol2').css('width', x + 'px').css('float','left');
      jQuery('.qimg2').css('width', x + 'px').css('height','auto');
   }

   x = Math.floor(jsfcore_globalwidth/5);
   y = Math.floor(jsfcore_globalwidth/3);
   var z = Math.floor(jsfcore_globalwidth/2);
   if(x<80) {
      jQuery('.fifth').css('width', z + 'px').css('float','left');
      jQuery('.fifthimg').css('width', z + 'px').css('height','auto');
   } else if(x<120) {
      jQuery('.fifth').css('width', y + 'px').css('float','left');
      jQuery('.fifthimg').css('width', y + 'px').css('height','auto');
   } else {
      jQuery('.fifth').css('width', x + 'px').css('float','left');
      jQuery('.fifthimg').css('width', x + 'px').css('height','auto');
   }
}

function jsfcore_trackitem(action,str1,str2){
   var str3 = location.hostname;
   if(str3.substr(0,4)=='www.') str3 = str3.substr(4);
   
   var url = jsfcore_domain + jsfcore_servercontroller + '&action=trackitem';
   url = url + '&view=' + encodeURIComponent(jsfcore_ht);
   if (Boolean(rmpaction)) url = url + '&foraction=' + encodeURIComponent(action);
   if (Boolean(str1)) url = url + '&jsftrack1=' + encodeURIComponent(str1);
   if (Boolean(str2)) url = url + '&jsftrack2=' + encodeURIComponent(str2);
   if (Boolean(str3)) url = url + '&jsftrack3=' + encodeURIComponent(str3);
   if (Boolean(jsfcore_globaluser) && Boolean(jsfcore_globaluser.userid)) url = url + '&userid=' + jsfcore_globaluser.userid;
   url = url + '&callback=jsfcore_donothing';
   url = url + '&referer=' + encodeURIComponent(document.referrer);

   //alert('url: ' + url);
   jsfcore_CallJSONP(url);
}

function jsfcore_getwebdata_jsonp(wdname,callback,params,checkcache,ignoreforuser,noloading){
   var query = '';
   //if (Boolean(enabled)) query += '&cmsenabled=1';
   if (Boolean(jsfcore_loggedin)) { 
      query += '&userid=' + encodeURIComponent(jsfcore_globaluser.userid);
      if(!Boolean(ignoreforuser)) query += '&foruserid=' + encodeURIComponent(jsfcore_globaluser.userid);
      query += '&token=' + encodeURIComponent(jsfcore_globaluser.token);
   }
   if (Boolean(wdname)) query += '&wdname=' + encodeURIComponent(wdname);
   if (Boolean(params)) query += params;
   //alert('jsfcore_getwebdata_jsonp: ' + query);
   
   // Return the cached key (if cached) so caller can remove from cache
   return jsfcore_QuickJSON('getwdandrows',callback,query,checkcache,noloading);
}


function jsfcore_replaceAll(find, replace, str) {
   if(!Boolean(str)) str = '';
   find = find.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
   return str.replace(new RegExp(find, 'g'), replace);
}

function jsfcore_flattenstr(str,max,removespecial) {
   if(!Boolean(str)) str = '';
   var newstr = str.toLowerCase();
   if(Boolean(removespecial)) {
      newstr = jsfcore_replaceAll('#','',newstr);
      newstr = jsfcore_replaceAll('-','',newstr);
      newstr = jsfcore_replaceAll('\'','',newstr);
      newstr = jsfcore_replaceAll(',','',newstr);
      //newstr = jsfcore_replaceAll('.','',newstr);
      newstr = jsfcore_replaceAll(':','',newstr);
      newstr = jsfcore_replaceAll(';','',newstr);
      //newstr = jsfcore_replaceAll('\\','',newstr);
      newstr = jsfcore_replaceAll('/','',newstr);
      //newstr = jsfcore_replaceAll(')','',newstr);
      //newstr = jsfcore_replaceAll('(','',newstr);
      newstr = jsfcore_replaceAll('&','',newstr);
      newstr = jsfcore_replaceAll('\"','',newstr);
      newstr = jsfcore_replaceAll('\n','',newstr);
   }
   newstr = jsfcore_replaceAll(' ','',newstr);
   if(Boolean(max) && !isNAN(max) && parseInt(max)>0) newstr = newstr.substr(0,parseInt(max));
   return newstr;
}

function jsfcore_getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}   



var jsfcore_tri=0;
function jsfcore_drawtoggle(str,oc1,oc2) {
   jsfcore_tri++;
   var html = '';
   html += '<table cellpadding=\"0\" cellspacing=\"0\"><tr><td>';
   html += '<div onclick=\"jsfcore_toggleTri' + jsfcore_tri + '();\" style=\"width:18px;height:18px;border-radius:9px;background-color:#E0E0E0;\">';
   html += '<div id=\"jsfcore_tri_right' + jsfcore_tri + '\" style=\"position:relative;left:5px;top:4px;\">';
   html += '<div style=\"width: 0;height: 0;border-top-width: 5px;border-top-style: solid;border-top-color: transparent;border-left-width: 9px;border-left-style: solid;border-left-color: #777777;\"></div>';
   html += '<div style=\"width: 0;height: 0;border-bottom-width: 5px;border-bottom-style: solid;border-bottom-color: transparent;border-left-width: 9px;border-left-style: solid;border-left-color: #777777;\"></div>';
   html += '</div>';

   html += '<div id=\"jsfcore_tri_down' + jsfcore_tri + '\" style=\"position:relative;left:4px;top:5px;display:none;\">';
   html += '<div style=\"float:left;width: 0;height: 0;border-bottom-width: 9px;border-bottom-style: solid;border-bottom-color: transparent;border-right-width: 5px;border-right-style: solid;border-right-color: #777777;\"></div>';
   html += '<div style=\"float:left;width: 0;height: 0;border-bottom-width: 9px;border-bottom-style: solid;border-bottom-color: transparent;border-left-width: 5px;border-left-style: solid;border-left-color: #777777;\"></div>';
   html += '<div style=\"clear:both;\"></div>';
   html += '</div>';
   html += '</div>';
   
   html += '</td><td>';
   html += '<span style=\"padding-left:4px;\" onclick=\"jsfcore_toggleTri' + jsfcore_tri + '();\">' + str + '</span>';
   html += '</td></tr></table>';
   
   html += '<script>';
   html += 'var jsfcore_tri_' + jsfcore_tri + ' = 1;';
   html += 'function jsfcore_toggleTri' + jsfcore_tri + '(){';
   html += 'if(jsfcore_tri_' + jsfcore_tri + '==1) {';
   html += '   jsfcore_tri_' + jsfcore_tri + ' = 0;';
   html += '   document.getElementById(\'jsfcore_tri_right' + jsfcore_tri + '\').style.display = \'none\';';
   html += '   document.getElementById(\'jsfcore_tri_down' + jsfcore_tri + '\').style.display = \'\';';
   html += oc1;
   html += '} else {';
   html += '   jsfcore_tri_' + jsfcore_tri + ' = 1;';
   html += '   document.getElementById(\'jsfcore_tri_down' + jsfcore_tri + '\').style.display = \'none\';';
   html += '   document.getElementById(\'jsfcore_tri_right' + jsfcore_tri + '\').style.display = \'\';';
   html += oc2;
   html += '}';
   html += '}';
   html += '</script>';

   return html;
}


var jsfcore_loading_count = 0;
function jsfcore_showloading(){
   jsfcore_loading_count++;
   jQuery('#jsfcore_loading').show();
}

function jsfcore_hideloading(){
   //alert('loading count: ' + jsfcore_loading_count);
   
   if(jsfcore_loading_count>1) jsfcore_loading_count--;
   else jsfcore_loading_count=0;
   
   if(jsfcore_loading_count==0) jQuery('#jsfcore_loading').hide();
}



function jsfcore_goback(){
   if(Boolean(jsfcore_historycnt) && jsfcore_historycnt>0) jsfcore_historycnt--;
   else jsfcore_historycnt=0;
   jsfcore_displayFunct = jsfcore_historyscr[(jsfcore_historycnt-1)];
   jsfcore_displayParam = jsfcore_historyparam[(jsfcore_historycnt-1)];
   jsfcore_displayFunct();
}


function jsfcore_addhistory(func,param){
   jsfcore_togglemenu(true);
   jQuery('#jsfcore_lightbox').fadeOut(200);  
    window.scrollTo(0,0);

   if(!Boolean(jsfcore_historycnt) || jsfcore_historycnt<1) jsfcore_historycnt=0;
   
   jsfcore_displayFunct = func;
   jsfcore_displayParam = param;
   if (jsfcore_historycnt==0 || jsfcore_displayFunct != jsfcore_historyscr[(jsfcore_historycnt-1)] || jsfcore_displayParam != jsfcore_historyparam[(jsfcore_historycnt-1)]) {
      jsfcore_historyscr[jsfcore_historycnt] = jsfcore_displayFunct;
      jsfcore_historyparam[jsfcore_historycnt] = jsfcore_displayParam;
      jsfcore_historycnt++;
   }
   
   var str1 = jsfcore_globaluser.userid;
   //jsfcore_trackitem(jsfcore_displayFunct.name,str1);
}

function getLoadingHTML(){
   var str = '<div style=\"padding:30px;font-size:18px;' + jsfcore_fontfamily + 'color:#bbbbbb;\">Loading...</div>';
   return str;
}

function jsfcore_showPageView(pagename,skiphistory){
   if(!Boolean(pagename)) pagename = jsfcore_remember_pagename;
   jsfcore_remember_pagename = pagename;
   if(!Boolean(skiphistory)) jsfcore_addhistory(jsfcore_showPageView,pagename);
   if(!Boolean(skiphistory) && Boolean(jsfcore_usehash) && pagename!='homepage' && pagename!='home') {
      jsfcore_currhash = pagename;
      window.location.hash = jsfcore_currhash;
   }
   
   //alert('jsfcore_showPageView( ' + pagename + ')');
   
   if (typeof window[pagename] === 'function') {
      var myFunc = window[pagename];
      myFunc();
   } else {
      //alert('showpage: ' + pagename);
      jsfcore_showPage(pagename);
   }  
}





////////////////////////////////////////
////////////////////////////////////////
//                                    //
//           STARTING POINT           //
//                                    //
////////////////////////////////////////
////////////////////////////////////////

var jsfcore_initpaths;
function jsfcore_ready_init() {
   //alert('jsfcore_ready_init');
   
   var checkdev = window.localStorage.getItem('jsfpb_devmode');
   if(checkdev=='1') jsfpb_devmode = true;
   else jsfpb_devmode = false;
   
   jsfcore_wd_menu = jsfcore_ht + ' menu';
   jsfcore_wd_pages = jsfcore_ht + ' pages';
   if(Boolean(jsfcore_wd_pages_override)) jsfcore_wd_pages = jsfcore_wd_pages_override;
   
   jsfpb_domain = jsfcore_domain;
   defaultremotedomain = jsfcore_domain;
   
   
   if (Boolean(jsfcore_forcehttps) && !location.href.toLowerCase().startsWith('https:')) {
      location.href = jsfcore_replaceAll('http:','https:',location.href);
   } else {
      //alert('check account');
      jsfcore_checkaccount(jsfcore_controller);
   }
}

 function jsfpb_enterdevmode(){
   //window.localStorage.clear();
   jsfpb_devmode = true;
   jQuery('#jsfpb_exitdev').show();
   jQuery('#jsfpb_enterdev').hide();
   window.localStorage.setItem('jsfpb_devmode','1');
   jsfcore_fixwidths();
 }
 
 function jsfpb_exitdevmode(){
   window.localStorage.clear();
   jsfpb_devmode = false;
   jQuery('#jsfpb_exitdev').hide();
   jQuery('#jsfpb_enterdev').show();
   window.localStorage.removeItem('jsfpb_devmode');
   jsfcore_fixwidths();
 }


// One-time when page loads based on URL
function jsfcore_controller() {
   //alert('jsfcore_controller');
   
   jsfcore_displayFunct = jsfcore_showHomePage;
   jsfcore_displayParam = '';
   
   jsfcore_historyscr = [];
   jsfcore_historyparam = [];
   jsfcore_historycnt = 0;
   
   var initpathname = window.location.pathname;
   jsfcore_initpaths = initpathname.substr(1).split('/');
   
   var pos_1 = '';
   var pos_2 = '';
   if(Boolean(jsfcore_initpaths) && jsfcore_initpaths.length>0) {
      pos_1 = jsfcore_initpaths[(jsfcore_initpaths.length - 1)];
      if(jsfcore_initpaths.length>1) {
         pos_2 = jsfcore_initpaths[(jsfcore_initpaths.length - 2)];
      }
   }
   alert("position 1: " + pos_1 + ", position 2: " + pos_2);
   
   //alert('initpath: ' + initpathname + ' chunked: ' + JSON.stringify(jsfcore_initpaths));
   
      if(pos_1=='home') {
         //alert('case #1');
         jsfcore_remember_pagename = '';
         jsfcore_displayParam = '';
         jsfcore_displayFunct = '';
         for(var i=0;i<(jsfcore_initpaths.length - 1);i++) {
            jsfcore_basedir += jsfcore_initpaths[i] + '/';
         }
      } else if(pos_2=='view' && Boolean(pos_1)) {
         //alert('case #2');
         jsfcore_remember_pagename = pos_1;
         //alert('todo, view, ' + jsfcore_remember_pagename);
         jsfcore_displayParam = jsfcore_remember_pagename;
         jsfcore_displayFunct = jsfcore_showPageView;
         for(var i=0;i<(jsfcore_initpaths.length - 2);i++) {
            jsfcore_basedir += jsfcore_initpaths[i] + '/';
         }
      } else {
         if (typeof jsfcore_customcontroller == 'function') jsfcore_customcontroller();         
      }
   if (typeof jsfcore_custominit == 'function') jsfcore_custominit();         
   jsfcore_fixwidths();
   jQuery(window).resize(jsfcore_resizestart);
}



var jsfcore_remember_skiphistory;
var jsfcore_showHomePage_counter = 0;

function jsfcore_showHomePage_waitfordata() {
   jsfcore_showHomePage(jsfcore_remember_skiphistory);
}

function jsfcore_showHomePage(skipHistory){
   // ***chj*** future: check if user is logged in and has campaign!!!
   //alert('jsfcore_showHomePage');
   jsfcore_remember_skiphistory = skipHistory;
   //if(!Boolean(jsfcore_informationboxes) || jsfcore_informationboxes.length<1) {
   if(jsfcore_showHomePage_counter<4 && (!Boolean(jsfcore_menuitems) || jsfcore_menuitems.length<1)) {
      //alert('waiting for data');
      setTimeout(jsfcore_showHomePage_waitfordata, 600);
      jsfcore_showHomePage_counter++;
   } else {
      jsfcore_showHomePage_counter = 0;
      //alert('data is in');
      if(!Boolean(skipHistory)) jsfcore_addhistory(jsfcore_showHomePage,skipHistory);
      jsfcore_togglemenu(true);
      window.scrollTo(0,0);
      
      //Default to the first campaign when the next line is uncommented
      //jsfcore_showcampaign();
      //jsfcore_setbgcolor('#FFFFFF');
      //jsfcore_showPage('homepage');
      jsfcore_showPageView('homepage',1);
   }   
}





//--------------------------
// auto text boxes
function jsfcore_autotext_leave(divid,dfault) {
   var txt = jQuery('#' + divid);
   if(!Boolean(txt.val()) || txt.val() == ''){
      txt.val(dfault);
      txt.css('font-style','italic').css('color','#999999');
   }  
}

function jsfcore_autotext_enter(divid,dfault) {
   var txt = jQuery('#' + divid);
   if(Boolean(txt.val()) && txt.val() == dfault){
      txt.val('');
      txt.css('font-style','normal').css('color','#000000');
   }   
}

function jsfcore_getautotext(divid,dfault,css,val,classstr,rqd){
   if(!Boolean(val)) val = dfault;
   if(!Boolean(css) && !Boolean(classstr)) css = 'width:200px;' + jsfcore_fontfamily + 'font-size:16px;background-color:#F0F0F0;border:1px solid #888888;border-radius:2px;margin:3px;';
   if(!Boolean(css)) css = '';
   
   var dcss = 'font-style:normal;color:#000000;';
   if(val==dfault) dcss = 'font-style:italic;color:#999999;';
   
   var type = 'text';
   if(dfault.toLowerCase()=='email' || dfault.toLowerCase()=='confirm email') type='email';
   else if(dfault.toLowerCase()=='phone' || dfault.toLowerCase()=='phone number') type='tel';
   
   var str = '';
   str += '<div class=\"jsfcore_txtinput\">';
   str += '<input type=\"' + type + '\" value=\"' + val + '\" ';
   str += 'id=\"' + divid + '\" ';
   str += 'data-ignoretxt=\"' + dfault + '\" ';
   if(Boolean(rqd)) str += 'data-required=\"yes\" ';
   if(Boolean(classstr)) str += 'class=\"' + classstr + '\" ';
   str += 'onblur=\"jsfcore_autotext_leave(\'' + divid + '\',\'' + dfault + '\');\" ';
   str += 'onfocus=\"jsfcore_autotext_enter(\'' + divid + '\',\'' + dfault + '\');\" ';
   str += 'style=\"' + css + dcss + '\">';
   if(Boolean(rqd)) str += '<span style=\"margin-left:5px;color:red;font-size:16px;font-weight:bold;\">*</span>';
   str += '</div>';
   return str;
}

function jsfcore_getautotextarea(divid,dfault,css,val,classstr){
   if(!Boolean(val)) val = dfault;
   if(!Boolean(css) && !Boolean(classstr)) css = 'width:200px;height:70px;' + jsfcore_fontfamily + 'font-size:16px;background-color:#F0F0F0;border:1px solid #888888;border-radius:2px;margin:3px;';
   if(!Boolean(css)) css = '';
   
   var dcss = 'font-style:normal;color:#000000;';
   if(val==dfault) dcss = 'font-style:italic;color:#999999;';
   
   var str = '';
   str += '<div class=\"jsfcore_txtinput\">';
   str += '<textarea ';
   str += 'id=\"' + divid + '\" ';
   str += 'data-ignoretxt=\"' + dfault + '\" ';
   if(Boolean(classstr)) str += 'class=\"' + classstr + '\" ';
   str += 'onblur=\"jsfcore_autotext_leave(\'' + divid + '\',\'' + dfault + '\');\" ';
   str += 'onfocus=\"jsfcore_autotext_enter(\'' + divid + '\',\'' + dfault + '\');\" ';
   str += 'style=\"' + css + dcss + '\">' + val + '</textarea>';
   str += '</div>';
   return str;
}

function jsfcore_pwfield(divid,val,css,classstr){
   if(!Boolean(css) && !Boolean(classstr)) css = 'width:200px;' + jsfcore_fontfamily + 'font-size:16px;background-color:#F0F0F0;border:1px solid #888888;border-radius:2px;margin:3px;';
   if(!Boolean(css)) css = '';
   
   var str = '';
   str += '<div class=\"jsfcore_txtinput\">';
   str += '<input ';
   str += ' id=\"' + divid + '\" ';
   if(Boolean(classstr)) str += 'class=\"' + classstr + '\" ';
   str += ' type=\"password\" ';
   str += ' style=\"' + css + 'color:#000000;font-style:normal;display:none;\"';
   str += ' value=\"\"';
   str += ' >';
   str += '<input ';
   str += ' id=\"' + divid + '_clear\" ';
   if(Boolean(classstr)) str += 'class=\"' + classstr + '\" ';
   str += ' type=\"text\" ';
   str += ' style=\"' + css + 'color:#999999;font-style:italic;\"';
   str += ' value=\"' + val + '\"';
   str += ' >';
   str += '</div>';

   str += '<script language=\"javascript\" type=\"text/javascript\">\n';
   str += 'jQuery(\'#' + divid + '_clear\').focus(function() { \n';
   str += '   jQuery(\'#' + divid + '_clear\').hide(); \n';
   str += '   jQuery(\'#' + divid + '\').show(); \n';
   str += '   jQuery(\'#' + divid + '\').focus(); \n';
   str += '}); \n';
   str += 'jQuery(\'#' + divid + '\').blur(function() { \n';
   str += '   if(jQuery(\'#' + divid + '\').val() == \'\') { \n';
   str += '      jQuery(\'#' + divid + '_clear\').show(); \n';
   str += '      jQuery(\'#' + divid + '\').hide(); \n';
   str += '   }\n';
   str += '});\n';
   str += '</script>\n';
   
   return str;
}


function jsfcore_checkifpassed(dt) {
   if(!Boolean(dt)) return false;
   
   var y = parseInt(dt.substr(0,4));
   var m = parseInt(dt.substr(5,2)) - 1;
   var d = parseInt(dt.substr(8,2));
   var hr = parseInt(dt.substr(11,2));
   var mn = parseInt(dt.substr(14,2));
   
   var newdate = new Date();
   var timecamp_obj = new Date(y,m,d,hr,mn,0,0);
   var timecamp = Math.floor(timecamp_obj.getTime() / 1000);
   var timecurr = Math.floor(Date.now() / 1000) + ((newdate.getTimezoneOffset() - 240) * 60);
   
   return (timecurr>timecamp);
}

function jsfcore_formatdate(dt,skiptime) {
   var y = parseInt(dt.substr(0,4));
   var m = parseInt(dt.substr(5,2));
   var d = parseInt(dt.substr(8,2));
   var hr = parseInt(dt.substr(11,2));
   var mn = dt.substr(14,2);
   
   if(!Boolean(hr)) hr = '23';
   if(!Boolean(mn)) mn = '59';
   
   var ampm = 'am';
   if(hr>11) ampm = 'pm';
   
   if(hr==0) hr = 12;
   else if (hr>12) hr = hr - 12;
   
   var month = 'Jan';
   if(m==2) month = 'Feb';
   else if(m==3) month = 'Mar';
   else if(m==4) month = 'Apr';
   else if(m==5) month = 'May';
   else if(m==6) month = 'Jun';
   else if(m==7) month = 'Jul';
   else if(m==8) month = 'Aug';
   else if(m==9) month = 'Sep';
   else if(m==10) month = 'Oct';
   else if(m==11) month = 'Nov';
   else if(m==12) month = 'Dec';
   
   var temp = month + ' ' + d + ', ' + y;
   if(!Boolean(skiptime)) temp += ' ' + hr + ':' + mn + ampm + ' (ET)';
   
   return temp;
}




function jsfcore_loadinghtml(){
  var str = '';
  str += '<div style=\"border:1px solid #DDDDDD;border-radius:8px;margin:10px;padding:10px;font-size:32px;' + jsfcore_fontfamily + 'font-weight:bold;color:#777777;width:200px;\">';
  str += 'LOADING';
  str += '<span id=\"loading1dot\">.</span>';
  str += '<span id=\"loading2dot\">.</span>';
  str += '<span id=\"loading3dot\">.</span>';
  str += '</div>';
  str += '\n<script>\n';
  str += 'function loadingdots() {\n';
  str += '   jQuery(\'#loading2dot\').fadeOut(400,function(){\n';
  str += '      jQuery(\'#loading1dot\').fadeOut(400,function(){\n';
  str += '         jQuery(\'#loading2dot\').fadeIn(400);\n';
  str += '         jQuery(\'#loading3dot\').fadeOut(400,function(){\n';
  str += '            jQuery(\'#loading1dot\').fadeIn(400);\n';
  str += '            loadingdots();\n';
  str += '            jQuery(\'#loading3dot\').fadeIn(400);\n';
  str += '         });\n';
  str += '      });\n';
  str += '   });\n';
  str += '}\n';
  str += '//loadingdots();\n';
  str += '</script>';
  return str;
}

var jsfcore_remember_pagename;
function jsfcore_showPage(pagename,success) {
   if(!Boolean(success)) success = 'jsfcore_updateclasses';
   if(!Boolean(pagename)) pagename = jsfcore_remember_pagename;
   jsfcore_remember_pagename = pagename;
   //alert('jsfcore_showPage: ' + pagename);
   jsfpb_getPage(jsfcore_wd_pages,pagename,jsfcore_globalwidth_pgbldr,'jsfcore_body',success);
}

//--------------------------------------------
// User login

function jsfcore_showloginpage() {
   var str =jsfcore_showlogin('');
   jQuery('#jsfcore_body').html(str);
   
   //layout the page better
   var loginwidth = jsfcore_globalwidth - 30;
   if(loginwidth>400) loginwidth = 400;
   var loginleft = Math.round((jsfcore_globalwidth - loginwidth)/2);
   
   jQuery('#jsfcore_acct').css('width',loginwidth + 'px');
   jQuery('#jsfcore_acct').css('margin-left',loginleft + 'px');
   
   jQuery('#inputloginemail').css('margin','5px 0px 0px 0px');
   jQuery('#inputloginemail').css('border-radius','0px');
   jQuery('#inputloginemail').css('border','1px solid #676767');
   jQuery('#inputloginemail').css('border-top-left-radius','10px');
   jQuery('#inputloginemail').css('border-top-right-radius','10px');
   jQuery('#inputloginemail').css('width',(loginwidth - 20) + 'px');
   
   jQuery('#inputpassword').css('margin','0px 0px 10px 0px');
   jQuery('#inputpassword').css('border-radius','0px');
   jQuery('#inputpassword').css('border-top','0');
   jQuery('#inputpassword').css('border-left','1px solid #676767');
   jQuery('#inputpassword').css('border-right','1px solid #676767');
   jQuery('#inputpassword').css('border-bottom','1px solid #676767');
   jQuery('#inputpassword').css('border-bottom-left-radius','10px');
   jQuery('#inputpassword').css('border-bottom-right-radius','10px');
   jQuery('#inputpassword').css('width',(loginwidth - 20) + 'px');

   jQuery('#inputpassword_clear').css('margin','0px 0px 10px 0px');
   jQuery('#inputpassword_clear').css('border-radius','0px');
   jQuery('#inputpassword_clear').css('border-top','0');
   jQuery('#inputpassword_clear').css('border-left','1px solid #676767');
   jQuery('#inputpassword_clear').css('border-right','1px solid #676767');
   jQuery('#inputpassword_clear').css('border-bottom','1px solid #676767');
   jQuery('#inputpassword_clear').css('border-bottom-left-radius','10px');
   jQuery('#inputpassword_clear').css('border-bottom-right-radius','10px');
   jQuery('#inputpassword_clear').css('width',(loginwidth - 20) + 'px');
   
   jQuery('#inputresetemail').css('margin','5px 0px 0px 0px');
   jQuery('#inputresetemail').css('border-radius','0px');
   jQuery('#inputresetemail').css('border','1px solid #676767');
   jQuery('#inputresetemail').css('border-radius','10px');
   jQuery('#inputresetemail').css('width',(loginwidth - 20) + 'px');
   
   jQuery('#inputnewemail').css('margin','5px 0px 0px 0px');
   jQuery('#inputnewemail').css('border-radius','0px');
   jQuery('#inputnewemail').css('border','1px solid #676767');
   jQuery('#inputnewemail').css('border-top-left-radius','10px');
   jQuery('#inputnewemail').css('border-top-right-radius','10px');
   jQuery('#inputnewemail').css('width',(loginwidth - 20) + 'px');
   
   jQuery('#inputconfirmemail').css('border-top','0');
   jQuery('#inputconfirmemail').css('border-radius','0px');
   jQuery('#inputconfirmemail').css('border-bottom','0');
   jQuery('#inputconfirmemail').css('border-left','1px solid #676767');
   jQuery('#inputconfirmemail').css('border-right','1px solid #676767');
   jQuery('#inputconfirmemail').css('width',(loginwidth - 20) + 'px');
   
   jQuery('#inputnewpassword').css('margin','0px 0px 10px 0px');
   jQuery('#inputnewpassword').css('border-radius','0px');
   jQuery('#inputnewpassword').css('border','1px solid #676767');
   jQuery('#inputnewpassword').css('border-bottom-left-radius','10px');
   jQuery('#inputnewpassword').css('border-bottom-right-radius','10px');
   jQuery('#inputnewpassword').css('width',(loginwidth - 20) + 'px');

   jQuery('#inputnewpassword_clear').css('margin','0px 0px 10px 0px');
   jQuery('#inputnewpassword_clear').css('border-radius','0px');
   jQuery('#inputnewpassword_clear').css('border','1px solid #676767');
   jQuery('#inputnewpassword_clear').css('border-bottom-left-radius','10px');
   jQuery('#inputnewpassword_clear').css('border-bottom-right-radius','10px');
   jQuery('#inputnewpassword_clear').css('width',(loginwidth - 20) + 'px');
}

function jsfcore_showloginbox() {
   var str =jsfcore_showlogin('');
   //alert('str: ' + str);
   //jQuery('#jsfcore_lightbox').html(str);
   //jQuery('#jsfcore_lightbox').fadeIn(400);
   
   jsfcore_showlightbox(str);
}

var jsfcore_globaluser;
var jsfcore_loggedin = false;
var jsfcore_waitingonlogin = false;
var jsfcore_afteraccountfn;
function jsfcore_checkaccount(fn){
   //alert('jsfcore_checkaccount');
   //check if user is logged in already...
   if (!jsfcore_waitingonlogin && !jsfcore_loggedin) {
      //alert('checking if user/token is available');
      jsfcore_waitingonlogin = true;
      jsfcore_loggedin = false;
      jsfcore_globaluser = {};
      
      var userid = window.localStorage.getItem('userid');
      var token = window.localStorage.getItem('token');
      if (Boolean(userid) && Boolean(token)) {
         jsfcore_afteraccountfn = fn;
         var uri = '';
         uri += '&userid=' + encodeURIComponent(userid);
         uri += '&token=' + encodeURIComponent(token);
         jsfcore_QuickJSON('acctinfo','jsfcore_returnacctinfo',uri,false);
      } else {
         jsfcore_waitingonlogin = false;
         if(Boolean(fn)) fn();
      }
   } else {
      if(Boolean(fn)) fn();
   }
}

function jsfcore_logout(skipmessage){
   jsfcore_currentteam = '';
   jsfcore_loggedin = false;
   jsfcore_waitingonlogin = false;
   jsfcore_globaluser = {};
   //remove user info from db
   if (typeof(window.localStorage)!='undefined') {
      window.localStorage.removeItem('userid');
      window.localStorage.removeItem('email');
      window.localStorage.removeItem('token');
      window.localStorage.removeItem('fname');
      window.localStorage.removeItem('lname');
      window.localStorage.removeItem('phonenum');
      window.localStorage.removeItem('field1');
      window.localStorage.removeItem('field2');
      window.localStorage.removeItem('isadmin');
   }
   
   
   jsfcore_displayParam = '';
   jsfcore_displayFunct = jsfcore_showHomePage;            
   jsfcore_fixwidths();
   if(!Boolean(skipmessage)) jsfcore_populateMessage('You have logged out successfully.',10000);
}

function jsfcore_returnacctinfo(jsondata){
   jsfcore_ReturnJSON(jsondata);
   //alert('response from acctinfo: ' + JSON.stringify(jsondata));
   if (jsondata.responsecode==1 || jsondata.responsecode=='1') {
      //alert('jsfcore_returnacctinfo: user object - ' + JSON.stringify(jsondata.user));
      jsfcore_globaluser = jsondata.user;
      jsfcore_globaluser.fullname = jsfcore_globaluser.fname + ' ' + jsfcore_globaluser.lname;
      
      if(!Boolean(jsfcore_globaluser.isadmin) || parseInt(jsfcore_globaluser.isadmin)!=1) jsfcore_globaluser.isadmin=0;
      else jsfcore_globaluser.isadmin = 1;
      
      jsfcore_loggedin = true;
      window.localStorage.setItem('email',jsondata.user.email);
      window.localStorage.setItem('userid',jsondata.user.userid);
      window.localStorage.setItem('token',jsondata.user.token);
      window.localStorage.setItem('fname',jsondata.user.fname);
      window.localStorage.setItem('lname',jsondata.user.lname);
      window.localStorage.setItem('phonenum',jsondata.user.phonenum);
      window.localStorage.setItem('field1',jsondata.user.field1);
      window.localStorage.setItem('field2',jsondata.user.field2);
      window.localStorage.setItem('isadmin',jsondata.user.isadmin);
   } else {
      jsfcore_loggedin = false;
      jsfcore_globaluser = {};
      if (typeof(window.localStorage)!='undefined') {
         window.localStorage.removeItem('userid');
         window.localStorage.removeItem('email');
         window.localStorage.removeItem('token');
         window.localStorage.removeItem('fname');
         window.localStorage.removeItem('lname');
         window.localStorage.removeItem('phonenum');
         window.localStorage.removeItem('field1');
         window.localStorage.removeItem('field2');
         window.localStorage.removeItem('isadmin');
      }
   }
   jsfcore_waitingonlogin = false;
   if(Boolean(jsfcore_afteraccountfn)) jsfcore_afteraccountfn();
}




// May hide this from display
function jsfcore_createaccount(ip){
   jsfcore_addhistory(jsfcore_createaccount,ip);
   
   var str = '';
   if(jsfcore_loggedin) {
      jsfcore_showHomePage(true);     
   } else {
      str += '<div style=\"min-height:' + (jsfcore_globalheight-jsfcore_header_height-80) + 'px;\">';
      str += '<div style=\"padding:80px 20px 20px 20px;\">';
      
      var ewd = jsfcore_globalwidth - 80;
      if(ewd > 400) ewd = 400;
      str += '<div style=\"width:' + ewd + 'px;background-color:#FFFFFF;padding:20px;border-top-right-radius:25px;border-bottom-left-radius:25px;\">';
      str += jsfcore_shownewacct();
      str += '</div>';

      str += '</div>';
      str += '</div>';
      jQuery('#jsfcore_body').html(str);
   }
}




function jsfcore_showlogin(useremail){
   var htmlstr = '';
   
   htmlstr += '<div id=\"jsfcore_acct\">';
   htmlstr += '<div id=\"jsfcore_login\">';
   htmlstr += jsfcore_getautotext('inputloginemail','Email','',useremail,'jsfcore_biginput');
   htmlstr += jsfcore_pwfield('inputpassword','Password','','jsfcore_biginput');
   htmlstr += '<div style=\"margin-top:10px;\">';
   htmlstr += '<div ';
   htmlstr += ' id=\"button_login\"';
   htmlstr += ' class=\"jsfcore_btn\"';
   htmlstr += ' style=\"float:right;margin-left:25px;\"';
   htmlstr += ' onclick=\"jsfcore_executelogin(jQuery(\'#inputloginemail\').val(),jQuery(\'#inputpassword\').val());\"';
   htmlstr += '>';
   htmlstr += 'Log Me In';
   htmlstr += '</div>';

   /*
   htmlstr += '<div style=\"margin-top:15px;margin-bottom:15px;font-weight:bold;color:#000000;font-size:16px;\"> - OR - </div>';
   
   htmlstr += '<div style=\"margin:20px 0px 25px 0px;\">';
   htmlstr += jsfcore_fbgetbutton();
   htmlstr += '</div>';
   */

   htmlstr += '<div ';
   htmlstr += ' class=\"jsfcore_link\"';
   htmlstr += ' style=\"float:right;margin-left:25px;margin-top:10px;\"';
   htmlstr += ' onclick=\"useremail=jQuery(\'#inputloginemail\').val();if(Boolean(useremail)) jQuery(\'#inputresetemail\').val(useremail);jQuery(\'#jsfcore_login\').hide();jQuery(\'#jsfcore_newuser\').hide();jQuery(\'#jsfcore_pwreset\').show();\"';
   htmlstr += '>';
   htmlstr += 'Lost Password?';
   htmlstr += '</div>';
   
   if(Boolean(jsfcore_newaccount)) {
      htmlstr += '<div ';
      htmlstr += ' class=\"jsfcore_link\"';
      htmlstr += ' style=\"float:right;margin-left:25px;margin-top:10px;\"';
      htmlstr += ' onclick=\"jQuery(\'#jsfcore_login\').hide();jQuery(\'#jsfcore_pwreset\').hide();jQuery(\'#jsfcore_newuser\').show();\"';
      htmlstr += '>';
      htmlstr += 'Create Account';
      htmlstr += '</div>';
   }
   htmlstr += '<div style=\"clear:both;\"></div>';
   htmlstr += '</div>';
   htmlstr += '</div>';
   
   
   // Password Reset block
   htmlstr += '<div id=\"jsfcore_pwreset\" style=\"display:none;\">';
   htmlstr += jsfcore_getautotext('inputresetemail','Email','','','jsfcore_biginput');
   
   htmlstr += '<div style=\"margin-top:10px;\">';
   htmlstr += '<div ';
   htmlstr += ' id=\"button_resetpassword\"';
   htmlstr += ' class=\"jsfcore_btn\"';
   htmlstr += ' style=\"float:right;margin-left:25px;\"';
   htmlstr += ' onclick=\"jsfcore_executeresetpassword(jQuery(\'#inputresetemail\').val());\"';
   htmlstr += '>';
   htmlstr += 'Reset Password';
   htmlstr += '</div>';
   
   htmlstr += '<div ';
   htmlstr += ' class=\"jsfcore_link\"';
   htmlstr += ' style=\"float:right;margin-left:25px;margin-top:10px;\"';
   htmlstr += ' onclick=\"useremail=jQuery(\'#inputresetemail\').val();if(Boolean(useremail)) jQuery(\'#inputloginemail\').val(useremail);jQuery(\'#jsfcore_login\').show();jQuery(\'#jsfcore_pwreset\').hide();\"';
   htmlstr += '>';
   htmlstr += 'Return to log in';
   htmlstr += '</div>';
   htmlstr += '<div style=\"clear:both;\"></div>';
   htmlstr += '</div>';
   htmlstr += '</div>';

   
   // New account block
   htmlstr += '<div id=\"jsfcore_newuser\" style=\"margin-bottom:30px;display:none;\">';
   htmlstr += jsfcore_getautotext('inputnewemail','Email','','','jsfcore_biginput');
   htmlstr += jsfcore_getautotext('inputconfirmemail','Confirm Email','','','jsfcore_biginput');
   htmlstr += jsfcore_pwfield('inputnewpassword','Password','','jsfcore_biginput');
   //htmlstr += '<div onclick=\"location.href=\'' + jsfcore_privacypolicy_url + '\';\" style=\"color:blue;font-size:8px;cursor:pointer;margin-top:4px;\">View our privacy policy</div>';
   htmlstr += '<div style=\"margin-top:10px;\">';
   htmlstr += '<div ';
   htmlstr += ' id=\"button_login\"';
   htmlstr += ' class=\"jsfcore_btn\"';
   htmlstr += ' style=\"float:right;margin-left:25px;\"';
   htmlstr += ' onclick=\"jsfcore_executenewacct(jQuery(\'#inputnewemail\').val().toLowerCase(),jQuery(\'#inputconfirmemail\').val().toLowerCase(),jQuery(\'#inputnewpassword\').val());\"';
   htmlstr += '>';
   htmlstr += 'Create Account';
   htmlstr += '</div>';
   htmlstr += '<div ';
   htmlstr += ' class=\"jsfcore_link\"';
   htmlstr += ' style=\"float:right;margin-left:25px;margin-top:10px;\"';
   htmlstr += ' onclick=\"jQuery(\'#jsfcore_login\').show();jQuery(\'#jsfcore_pwreset\').hide();jQuery(\'#jsfcore_newuser\').hide();\"';
   htmlstr += '>';
   htmlstr += 'Return to log in';
   htmlstr += '</div>';
   htmlstr += '<div style=\"clear:both;\"></div>';
   htmlstr += '</div>';
   htmlstr += '</div>';
   //htmlstr += '<div style=\"margin-top:15px;margin-bottom:15px;font-weight:bold;color:#000000;font-size:16px;\"> - OR - </div>';
   //htmlstr += jsfcore_fbgetbutton();
   
   
   
   
   htmlstr += '</div>';

   
   
   return htmlstr;
}   
   
function jsfcore_shownewacct(useremail){
   var htmlstr = '';
   
   if(Boolean(jsfcore_currentteam)) {
      htmlstr += '<div style=\"color:#555555;font-size:12px;margin-bottom:2px;\">';
      htmlstr += 'You\'re joining';
      htmlstr += '</div>';
      htmlstr += '<div style=\"color:#67bcae;font-weight:bold;font-size:16px;margin-bottom:0px;\">' + jsfcore_getTeamType(true) + ' ';
      htmlstr += jsfcore_getteamname(jsfcore_currentteam);
      htmlstr += '</div>';
      htmlstr += '<div style=\"margin-bottom:10px;color:blue;cursor:pointer;font-size:10px;\" onclick=\"location.href=\'/view/jsfcore_showteamoptions\';\">Switch ' + jsfcore_getTeamType(true) + ' &gt;</div>';
   } else {
      htmlstr += '<div style=\"color:#67bcae;font-weight:bold;font-size:16px;margin-bottom:10px;\">';
      htmlstr += 'Email and password are all you need to join!';
      //htmlstr += ' <span style=\"margin-left:15px;color:blue;cursor:pointer;font-size:10px;\" onclick=\"jsfcore_showteamoptions();\">Pick a team</span>';
      htmlstr += '</div>';
   }
   htmlstr += '<div id=\"jsfcore_newuser\" style=\"margin-bottom:30px;\">';
   htmlstr += jsfcore_getautotext('inputnewemail','Email');
   htmlstr += jsfcore_getautotext('inputconfirmemail','Confirm Email');
   htmlstr += jsfcore_pwfield('inputnewpassword','Password');
   //htmlstr += '<div onclick=\"location.href=\'' + jsfcore_privacypolicy_url + '\';\" style=\"color:blue;font-size:8px;cursor:pointer;margin-top:4px;\">View our privacy policy</div>';
   htmlstr += '<div ';
   htmlstr += ' id=\"button_login\"';
   htmlstr += ' class=\"jsfcore_btn\"';
   htmlstr += ' style=\"width:200px;margin-top:4px;\"';
   htmlstr += ' onclick=\"jsfcore_executenewacct(jQuery(\'#inputnewemail\').val().toLowerCase(),jQuery(\'#inputconfirmemail\').val().toLowerCase(),jQuery(\'#inputnewpassword\').val());\"';
   htmlstr += '>';
   htmlstr += 'Create Your Account';
   htmlstr += '</div>';
   htmlstr += '</div>';
   htmlstr += '<div style=\"margin-top:15px;margin-bottom:15px;font-weight:bold;color:#000000;font-size:16px;\"> - OR - </div>';
   htmlstr += jsfcore_fbgetbutton();
   htmlstr += '<div style=\"margin-top:40px;margin-bottom:1px;cursor:pointer;color:blue;font-size:12px;\" onclick=\"jsfcore_showloginbox();\">Already involved?  Sign in &gt;</div>';   
   
   return htmlstr;
}










function jsfcore_showfield(id,vallbl,val) {
   
   var css = 'background:transparent;width:195px;border:0px;font-size:18px;font-family:arial;';
   var str = '<div style=\"width:200px;margin-top:8px;margin-bottom:5px;border:2px solid #b0c7d9;border-radius:4px;\">';
   str += jsfcore_getautotext(id,vallbl,css,val);
   str += '</div>';
   return str;
}

function jsfcore_showpwfield(id,val) {
   var css = 'background:transparent;width:195px;border:0px;font-size:18px;font-family:arial;';
   var str = '<div style=\"width:200px;margin-top:8px;margin-bottom:5px;border:2px solid #b0c7d9;border-radius:4px;\">';
   str += jsfcore_pwfield(id,val,css);
   str += '</div>';
   return str;
}
   

function jsfcore_showuseracct() {
   jsfcore_togglemenu(true);
   if(Boolean(jsfcore_globaluser) && Boolean(jsfcore_globaluser.userid)) {
      var txt = '';
      var vallbl = 'First Name';
      var val = vallbl;
      var id = 'accountfname';
      if (Boolean(jsfcore_globaluser.fname)) val = jsfcore_globaluser.fname;
      txt += jsfcore_showfield(id,vallbl,val);
      
      vallbl = 'Last Name';
      val = vallbl;
      id = 'accountlname';
      if (Boolean(jsfcore_globaluser.lname)) val = jsfcore_globaluser.lname;
      txt += jsfcore_showfield(id,vallbl,val);
      
      vallbl = 'Phone Number';
      val = vallbl;
      id = 'accountphonenum';
      if (Boolean(jsfcore_globaluser.phonenum)) val = jsfcore_globaluser.phonenum;
      txt += jsfcore_showfield(id,vallbl,val);
      
      if(jsfcore_globaluser.email.includes('dummy') && jsfcore_globaluser.email.includes('facebook')) {
         txt += '<div style=\"margin-top:8px;margin-bottom:10px;\">Use facebook to log into your SpreadItForward account.</div>';
      } else {
         txt += '<div style=\"margin-top:8px;margin-bottom:10px;\"><input id=\"jsfcore_chngpw\" type=\"checkbox\" onclick=\"jQuery(\'#password_login_hidden\').val(\'\');jQuery(\'#cpassword_login_hidden\').val(\'\');if(document.getElementById(\'jsfcore_chngpw\').checked) jQuery(\'#jsfcore_chngpw_div\').show(); else jQuery(\'#jsfcore_chngpw_div\').hide();\"> Change my password</div>';
      }
      
      txt += '<div id=\"jsfcore_chngpw_div\" style=\"display:none;\">';
      txt += '<div id=\"jsfcore_pwlength\" style=\"margin:5px 0px 5px 0px;font-size:12px;font-family:arial;color:#CC3333;font-weight:bold;display:none;\">Your password must be at least 6 characters.</div>';
      txt += '<div id=\"jsfcore_pwnomatch\" style=\"margin:5px 0px 5px 0px;font-size:12px;font-family:arial;color:#CC3333;font-weight:bold;display:none;\">Please check your passwords and make sure they match.</div>';
      txt += jsfcore_showpwfield('password_login','New Password');
      txt += jsfcore_showpwfield('cpassword_login','Confirm Password');
      txt += '</div>';
   
      txt += '<div ';
      txt += ' id=\"jsfcore_button_acctupdate\"';
      txt += ' style=\"margin-top:10px;width:130px;background-color:#DFDFDF;border-radius:5px;border:1px solid #000000;text-align:center;padding:8px;color:#000000;font-family:arial;font-size:16px;cursor:pointer;\"';
      txt += ' onclick=\"jsfcore_execute_acctupdate();\"';
      txt += '>';
      txt += 'Save Updates';
      txt += '</div>';
      txt += '<div ';
      txt += ' id=\"jsfcore_loading_acctupdate\"';
      txt += ' style=\"display:none;padding:8px;color:#444444;font-family:arial;font-size:16px;cursor:pointer;\"';
      txt += '>';
      txt += 'Loading...';
      txt += '</div>';
      
      jsfcore_showlightbox(txt);
   }
}

function jsfcore_execute_acctupdate(callback){
   var pw = jQuery('#password_login_hidden').val();
   var cpw = jQuery('#cpassword_login_hidden').val();

   jQuery('#jsfcore_pwlength').hide();
   jQuery('#jsfcore_pwnomatch').hide();
   if(Boolean(pw) && pw.length<6) {
      jQuery('#jsfcore_pwlength').show();
   } else if(Boolean(pw) && pw!=cpw) {
      jQuery('#jsfcore_pwnomatch').show();
   } else {
      var fname = jQuery('#accountfname').val();
      var lname = jQuery('#accountlname').val();
      var phonenum  = jQuery('#accountphonenum').val();
      if (!Boolean(callback)) callback = 'jsfcore_return_acctupdate';
      jQuery('#jsfcore_button_acctupdate').hide();
      jQuery('#jsfcore_loading_acctupdate').show();
      var userid = jsfcore_globaluser.userid;
      var token = jsfcore_globaluser.token;
      var url = '';
      url += '&userid=' + encodeURIComponent(userid) + '&token=' + encodeURIComponent(token);
      url += '&fname=' + encodeURIComponent(fname);
      url += '&lname=' + encodeURIComponent(lname);
      url += '&phonenum=' + encodeURIComponent(phonenum);
      if(Boolean(pw)) url += '&changepassword=1&password=' + encodeURIComponent(pw) + '&cpassword=' + encodeURIComponent(cpw);
      jsfcore_QuickJSON('updateuser',callback,url);
   }
}

function jsfcore_return_acctupdate(jsondata){
   jsfcore_ReturnJSON(jsondata)
   //alert(JSON.stringify(jsondata));
   var msg = 'Your account was updated successfully!';
   var color = '#AAEEAA';
   var showtime = 8000;
   if (jsondata.responsecode==1) {
      jsfcore_globaluser = jsondata.user;
      if(!Boolean(jsfcore_globaluser.isadmin) || parseInt(jsfcore_globaluser.isadmin)!=1) jsfcore_globaluser.isadmin=0;
      else jsfcore_globaluser.isadmin = 1;
      window.localStorage.setItem('email',jsondata.user.email);
      window.localStorage.setItem('userid',jsondata.user.userid);
      window.localStorage.setItem('token',jsondata.user.token);
      window.localStorage.setItem('fname',jsondata.user.fname);
      window.localStorage.setItem('lname',jsondata.user.lname);
      window.localStorage.setItem('phonenum',jsondata.user.phonenum);
      window.localStorage.setItem('field1',jsondata.user.field1);
      window.localStorage.setItem('field2',jsondata.user.field2);
      window.localStorage.setItem('isadmin',jsondata.user.isadmin);
      if(Boolean(jsondata.responsetxt)) {
         msg = jsondata.responsetxt;
         showtime = 10000;
      }
      jQuery('#jsfcore_lightbox').fadeOut(400,'swing',function(){jQuery('#jsfcore_lightbox').html('');});
      jsfcore_populateMessage(msg);
   } else {
      jsfcore_logout(1);
      alert('Your data was not updated successfully for security reasons.  Please log back in to try again.');
   }
}






function jsfcore_executenewacct(email,cemail,password){
   if(!Boolean(email) || !Boolean(cemail)) {
      jsfcore_populateMessage('Please enter your email address twice.');
      window.scrollTo(0,0);
   } else if(email!=cemail) {
      jsfcore_populateMessage('Please check that your email addresses match.');
      window.scrollTo(0,0);
   } else if(!Boolean(password)) {
      jsfcore_populateMessage('Please set a password before continuing.');
      window.scrollTo(0,0);
   } else {
      var url = '&email=' + encodeURIComponent(email);
      url += '&password=' + encodeURIComponent(password);
      url += '&cpassword=' + encodeURIComponent(password);
      url += '&usertype=sif';
      url += '&refsrc=sif';
      url += '&notes=sif';
      //alert('url: ' + url);
      jsfcore_QuickJSON('adduser','jsfcore_returnnewacct',url,false);
   }
}

function jsfcore_returnnewacct(jsondata){
   jsfcore_ReturnJSON(jsondata);
   //alert(JSON.stringify(jsondata));
   if (jsondata.responsecode==1) {
      //alert('jsfcore_returnnewacct new user: ' + JSON.stringify(jsondata.user));
      jsfcore_globaluser = jsondata.user;
      if(!Boolean(jsfcore_globaluser.isadmin) || parseInt(jsfcore_globaluser.isadmin)!=1) jsfcore_globaluser.isadmin=0;
      else jsfcore_globaluser.isadmin = 1;
      jsfcore_loggedin = true;
      jsfcore_waitingonlogin = false;
      window.localStorage.setItem('email',jsondata.user.email);
      window.localStorage.setItem('userid',jsondata.user.userid);
      window.localStorage.setItem('token',jsondata.user.token);
      window.localStorage.setItem('fname',jsondata.user.fname);
      window.localStorage.setItem('lname',jsondata.user.lname);
      window.localStorage.setItem('phonenum',jsondata.user.phonenum);
      window.localStorage.setItem('field1',jsondata.user.field1);
      window.localStorage.setItem('field2',jsondata.user.field2);
      window.localStorage.setItem('isadmin',jsondata.user.isadmin);
      
      jsfcore_displayParam = '';
      jsfcore_displayFunct = '';            
      jsfcore_fixwidths();
      jsfcore_populateMessage('You\'re ready to go!  Thanks for joining!',10000);
   } else {
      jsfcore_loggedin = false;
      jsfcore_waitingonlogin = false;
      jsfcore_globaluser = {};
      if (typeof(window.localStorage)!='undefined') {
         window.localStorage.removeItem('userid');
         window.localStorage.removeItem('email');
         window.localStorage.removeItem('token');
         window.localStorage.removeItem('fname');
         window.localStorage.removeItem('lname');
         window.localStorage.removeItem('phonenum');
         window.localStorage.removeItem('field1');
         window.localStorage.removeItem('field2');
         window.localStorage.removeItem('isadmin');
      }
      
      if(Boolean(jsondata.responsetext)) {
         // indicates that this was a new user attempt
         jsfcore_populateMessage(jsondata.responsetext,9000);
      }
   }

   //jsfcore_showHomePage(true);

}


var jsfcore_prevpw;
function jsfcore_executelogin(email,password,callback){
   if(!Boolean(callback)) callback = 'jsfcore_returnlogin';
   jsfcore_prevpw = password;
   //var url = '&includerels=1&email=' + encodeURIComponent(email);
   var url = '&email=' + encodeURIComponent(email);
   url += '&password=' + encodeURIComponent(password);
   //alert('url: ' + url);
   jsfcore_QuickJSON('login',callback,url,false);   
}

function jsfcore_returnlogin(jsondata){
   jsfcore_ReturnJSON(jsondata);
   //alert(JSON.stringify(jsondata));
   var msg = 'Your login credentials were not correct.  Please try again.';
   var color = '#EEAAAA';
   var showtime = 5000;
   if (jsondata.responsecode==1) {
      //alert('jsfcore_returnlogin: user object - ' + JSON.stringify(jsondata.user));
      window.localStorage.clear();
      jsfcore_globaluser = jsondata.user;
      if(!Boolean(jsfcore_globaluser.isadmin) || parseInt(jsfcore_globaluser.isadmin)!=1) jsfcore_globaluser.isadmin=0;
      else jsfcore_globaluser.isadmin = 1;
      jsfcore_loggedin = true;
      msg = 'You have logged in successfully.';
      color = '#AAEEAA';
      showtime = 3300;

      window.localStorage.setItem('email',jsondata.user.email);
      window.localStorage.setItem('userid',jsondata.user.userid);
      window.localStorage.setItem('token',jsondata.user.token);
      window.localStorage.setItem('fname',jsondata.user.fname);
      window.localStorage.setItem('lname',jsondata.user.lname);
      window.localStorage.setItem('phonenum',jsondata.user.phonenum);
      window.localStorage.setItem('field1',jsondata.user.field1);
      window.localStorage.setItem('field2',jsondata.user.field2);
      window.localStorage.setItem('isadmin',jsondata.user.isadmin);
   }

   jsfcore_fixwidths();
   jsfcore_populateMessage(msg,showtime,color);
}

function jsfcore_executeresetpassword(email,callback){
   if(!Boolean(callback)) callback = 'jsfcore_returnresetpassword';
   var url = '&email=' + encodeURIComponent(email);
   url += '&fromemail=' + encodeURIComponent(jsfcore_fromemail);
   url += '&title=' + encodeURIComponent(jsfcore_sitetitle);
   url += '&url=' + encodeURIComponent(jsfcore_referenceurl);
   //alert('url: ' + jsfcore_domain + 'jsfcode/jsoncontroller.php?action=forgottenpw' + url);
   jsfcore_QuickJSON('forgottenpw',callback,url);   
}

function jsfcore_returnresetpassword(jsondata){
   jsfcore_ReturnJSON(jsondata);
   var msg = 'Your password has been reset and sent to you through email.';
   var color = '#AAEEAA';
   jsfcore_fixwidths();
   jsfcore_populateMessage(msg,5000,color);
}




function jsf_encrypt(key,text){
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




////////////////////////////////////////////////////////////
// MENU
function jsfcore_getmenu(){
   
   if (typeof jsfcore_custommenu == 'function') {
      jsfcore_custommenu();
   } else {      
      var callback = 'jsfcore_returnmenu';
      var params ='&cmsenabled=1';
      //alert('jsfcore_getmenu');
      jsfcore_getwebdata_jsonp(jsfcore_wd_menu,callback,params,true);
   }
}

var jsfcore_menuitems;
function jsfcore_returnmenu(jsondata){
   jsfcore_ReturnJSON(jsondata);   
   //alert('menu response: ' + JSON.stringify(jsondata));
   jsfcore_menuitems = [];
   var temp;

   if(Boolean(jsondata) && Boolean(jsondata.rows) && jsondata.rows.length>0) {
      jsfcore_menuitems = jsondata.rows;
   }
   
   if(Boolean(jsfcore_addhometomenu)) {
      temp = {};
      temp.title = 'Home';
      temp.divid = 'home';
      temp.forheader = 'YES';
      temp.forfooter = 'YES';
      //temp.fortabs = 'YES';
      temp.fortabs = 'NO';
      temp.onclick = 'jsfcore_showHomePage();';
      jsfcore_menuitems.unshift(temp);
   }
      
   if(Boolean(jsfcore_globaluser) && Boolean(jsfcore_globaluser.userid)) {
      
      temp = {};
      temp.title = 'My Account';
      temp.divid = 'myaccount';
      temp.forheader = 'YES';
      temp.forfooter = 'YES';
      temp.fortabs = 'NO';
      temp.onclick = 'jsfcore_showuseracct();';
      jsfcore_menuitems.push(temp);
      
      temp = {};
      temp.title = 'Log out';
      temp.divid = 'logout';
      temp.forheader = 'YES';
      temp.forfooter = 'YES';
      temp.fortabs = 'NO';
      temp.onclick = 'jsfcore_logout();';
      jsfcore_menuitems.push(temp);
   } else {
      if(Boolean(jsfcore_useaccounts)) {
         temp = {};
         temp.title = 'Log in';
         temp.divid = 'login';
         temp.forheader = 'YES';
         temp.forfooter = 'YES';
         temp.fortabs = 'NO';
         temp.onclick = 'jsfcore_showloginbox();';
         jsfcore_menuitems.push(temp);
      }
   }
   
   // Convert flags to new style 190427
   if(Boolean(jsfcore_menuitems) && Boolean(jsfcore_menuitems.length) && jsfcore_menuitems.length<50) {
      for (var i=0; i<jsfcore_menuitems.length; i++){
         if(!Boolean(jsfcore_menuitems[i].forheader) && !Boolean(jsfcore_menuitems[i].forfooter) && !Boolean(jsfcore_menuitems[i].fortabs)) {
            if(!Boolean(jsfcore_menuitems[i].location)) {
               jsfcore_menuitems[i].forheader = 'YES';
               jsfcore_menuitems[i].fortabs = 'YES';
               jsfcore_menuitems[i].forfooter = 'YES';               
            } else if(jsfcore_menuitems[i].location=='menu') {
               jsfcore_menuitems[i].forheader = 'YES';
               jsfcore_menuitems[i].fortabs = 'YES';
               jsfcore_menuitems[i].forfooter = 'NO';               
            } else if(jsfcore_menuitems[i].location=='both') {
               jsfcore_menuitems[i].forheader = 'YES';
               jsfcore_menuitems[i].fortabs = 'YES';
               jsfcore_menuitems[i].forfooter = 'YES';               
            } else if(jsfcore_menuitems[i].location=='bottom') {
               jsfcore_menuitems[i].forheader = 'NO';
               jsfcore_menuitems[i].fortabs = 'NO';
               jsfcore_menuitems[i].forfooter = 'YES';               
            }
         }
      }
   }
   
   jsfcore_createmenu();
}

function jsfcore_createmenu(){
   //alert('menu items: ' + JSON.stringify(jsfcore_menuitems));
   if (typeof jsfcore_customdrawmenu == 'function') {
      jsfcore_customdrawmenu();
   } else {      
      var str = '';
      var topstr = '';
      var tabstr = '';
      var botstr = '';
      
      tabstr += '<div style=\"\">';
      str += '<div style=\"padding-bottom:40px;\">';
      jsfcore_tabbedmenuitemid = '';
      var maxtabitems = Math.floor((jsfcore_globalwidth - 22 - 88)/88);
      var counttabitems = 0;
      if(Boolean(jsfcore_menuitems) && Boolean(jsfcore_menuitems.length) && jsfcore_menuitems.length<50) {
         for (var i=0; i<jsfcore_menuitems.length; i++){
            if(!Boolean(jsfcore_menuitems[i].reqlogin) || jsfcore_menuitems[i].reqlogin.toUpperCase()!='YES' || Boolean(jsfcore_loggedin)) {
               //alert('specific menu item: ' + JSON.stringify(jsfcore_menuitems[i]));
               var mobileadded = false;
               if(Boolean(jsfcore_menuitems[i].forheader) && jsfcore_menuitems[i].forheader.toUpperCase()=='YES') {
                  if(!Boolean(mobileadded)) {
                     //alert('added ' + jsfcore_menuitems[i].title + ' to mobile');
                     mobileadded = true;
                     str += jsfcore_createmenuitem(jsfcore_menuitems[i].title,jsfcore_menuitems[i].onclick,jsfcore_menuitems[i].url,false,false,false,jsfcore_menuitems[i].divid);
                  }
                  topstr += jsfcore_createmenuitem(jsfcore_menuitems[i].title,jsfcore_menuitems[i].onclick,jsfcore_menuitems[i].url,true,false,false,jsfcore_menuitems[i].divid);
               }            
               if(Boolean(jsfcore_menuitems[i].forfooter) && jsfcore_menuitems[i].forfooter.toUpperCase()=='YES') {
                  botstr += jsfcore_createmenuitem(jsfcore_menuitems[i].title,jsfcore_menuitems[i].onclick,jsfcore_menuitems[i].url,false,true,false,jsfcore_menuitems[i].divid);
               }
               if(Boolean(jsfcore_menuitems[i].fortabs) && jsfcore_menuitems[i].fortabs.toUpperCase()=='YES') {
                  if(!Boolean(mobileadded)) {
                     //alert('added ' + jsfcore_menuitems[i].title + ' to mobile');
                     mobileadded = true;
                     str += jsfcore_createmenuitem(jsfcore_menuitems[i].title,jsfcore_menuitems[i].onclick,jsfcore_menuitems[i].url,false,false,false,jsfcore_menuitems[i].divid);
                  }
                  if(counttabitems<maxtabitems) {
                     counttabitems++;
                     tabstr += jsfcore_createmenuitem(jsfcore_menuitems[i].title,jsfcore_menuitems[i].onclick,jsfcore_menuitems[i].url,false,false,true,jsfcore_menuitems[i].divid);
                  }
               }
            }
         }
      }
      
      tabstr += '<div style=\"clear:both;\"></div>';
      tabstr += '</div>';
      
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
      jQuery('#jsfcore_menu').html(str);
      jQuery('#jsfcore_innermenu').html(str);
      
      if(jQuery('#jsfcore_subheader').length>0) {
         jQuery('#jsfcore_subheader').html(tabstr);
         //Check for auto-selecting tab on page refresh
         if(Boolean(jsfcore_tabbedmenuitemid)) {
            jsfcore_choosetab(jsfcore_tabbedmenuitemid);
            jsfcore_tabbedmenuitemid = '';
         }
      }
      if(jQuery('#jsfcore_topmenulinks').length>0) {
         //alert('MENU FOUND: ' + topstr);
         jQuery('#jsfcore_topmenulinks').html(topstr);
      }
      if(jQuery('#jsfcore_menubottomlinks').length>0) {
         jQuery('#jsfcore_menubottomlinks').html(botstr);
      }
   }
}

function jsfcore_convurl(url) {
   if(Boolean(jsfcore_forcehttps)) {
      url = jsfcore_replaceAll('http:','https:',url);
   }
   return url;
}

function jsfcore_createmenuitem(name,oc,url,toplinks,botlinks,tabbed,divid) {
   if((!Boolean(oc) && Boolean(url)) || (!jsfcore_internallinks && Boolean(url))){
      //if(!url.startsWith('http')) url = 'https://' + url;
      if(!url.startsWith('/') && !url.startsWith('http')) url = jsfcore_basedir + url;
      oc = 'location.href=\'' + jsfcore_convurl(url) + '\';';
   }
   var str = '';
   var id = jsfcore_flattenstr(name);
   
   if(Boolean(toplinks) || Boolean(botlinks)) {
      // horizontal list of links
      str += '<span ';
      
      if(Boolean(toplinks) && Boolean(divid)) str += 'id=\"toplink_' + divid + '\" ';
      else if(Boolean(divid)) str += 'id=\"botlink_' + divid + '\" ';
      
      if(Boolean(toplinks)) str += 'class=\"topmenulink\" ';
      else if(Boolean(botlinks)) str += 'class=\"bottommenulink\" ';
      str += 'onclick=\"jsfcore_choosetab(\'jsfcoretab_' + id + '\');' + oc + '\" style=\"cursor:pointer;\">';
      str += name;
      str += '</span>';
   } else if(!Boolean(tabbed)) {
      //mobile menu list
      str += '<div onclick=\"jsfcore_togglemenu(true);jsfcore_choosetab(\'jsfcoretab_' + id + '\');' + oc + '\" style=\"cursor:pointer;padding:9px 30px 9px 15px;text-align:right;font-size:16px;font-weight:bold;border-top:1px solid #444444;\">';
      //str += name + ' mobile';
      str += name;
      str += '</div>';
   } else {
      //tabs: predefined background colors
      str += '<div ';
      str += 'style=\"float:left;cursor:pointer;width:80px;height:24px;overflow:hidden;padding-left:6px;padding-top:6px;font-size:12px;font-family:arial;border:1px solid #A0A0A0;background-color:#EDEDED;border-top-left-radius:8px;\" ';
      str += 'onclick=\"jsfcore_choosetab(\'jsfcoretab_' + id + '\');' + oc + '\" ';
      str += 'class=\"jsfcore_tabbedmenuitem\" ';
      str += 'id=\"jsfcoretab_' + id + '\" ';
      str += '>' + name + '</div>';
      
      //See if this menu item should be pre-selected
      // This is only on a page refresh that we would need this
      var temppaths = window.location.href.substr(9).split('/');
      if(Boolean(temppaths) && temppaths.length>2) {
         var compstr = '';
         for(var j=1;j<temppaths.length;j++) {
            if(j>1) compstr += '/';
            compstr += temppaths[j];
         }
         //alert('compare: ' + compstr + ' with ' + oc);
         if(compstr.length>6 && oc.length>19 && oc.indexOf(compstr)!== -1) {
            jsfcore_tabbedmenuitemid='jsfcoretab_' + id;
         }
      }
      
   }
   return str;
}


function jsfcore_choosetab(id) {
   if(Boolean(id) && jQuery('#' + id).length>0) {
      jQuery('.jsfcore_tabbedmenuitem').css('background-color','#EDEDED').css('border-bottom','1px solid #A0A0A0');
      jQuery('#' + id).css('background-color','#FFFFFF').css('border-bottom','1px solid #FFFFFF');
   }
}
// MENU
////////////////////////////////////////////////////////////


function jsfcore_fixwidths(){
   //alert('function ' + jsfcore_displayFunct.toString() + ' param: ' + jsfcore_displayParam);
   jsfcore_winwidth = jQuery(window).width();
   jsfcore_globalwidth = jsfcore_winwidth;
   if(Boolean(jsfcore_globalmaxwidth) && jsfcore_globalmaxwidth>400 && jsfcore_globalwidth>jsfcore_globalmaxwidth) jsfcore_globalwidth = jsfcore_globalmaxwidth;
   jsfcore_globalwidth_pgbldr = jsfcore_globalwidth;
   jsfcore_globalheight = jQuery(window).height();
      
   jsfcore_buildPageStructure();
   //jsfcore_setbgcolor('#DDDDDD');
   if(!Boolean(jsfcore_displayFunct)) jsfcore_displayFunct = jsfcore_showHomePage;
   jsfcore_displayFunct(jsfcore_displayParam);
   jsfcore_createmenu();
   jsfcore_updateclasses();   
}   
   
function drawMenuIcon(fatter,clr){
   if(!Boolean(clr)) clr = jsfcore_menuiconcolor;
   var str = '';
   if(Boolean(fatter)) {
      str += '<div style=\"position:relative;width:24px;height:24px;overflow:hidden;\">';
      str += '<div style=\"position:absolute;top:0px;left:0px;width:24px;height:4px;background-color:' + clr + ';opacity:0.6;overflow:hidden;border-radius:1px;\"></div>';
      str += '<div style=\"position:absolute;top:10px;left:0px;width:24px;height:4px;background-color:' + clr + ';opacity:0.6;overflow:hidden;border-radius:1px;\"></div>';
      str += '<div style=\"position:absolute;top:20px;left:0px;width:24px;height:4px;background-color:' + clr + ';opacity:0.6;overflow:hidden;border-radius:1px;\"></div>';
      str += '</div>';
   } else {
      str += '<div style=\"position:relative;width:20px;height:20px;overflow:hidden;\">';
      str += '<div style=\"position:absolute;top:0px;left:0px;width:20px;height:2px;background-color:' + clr + ';overflow:hidden;\"></div>';
      str += '<div style=\"position:absolute;top:8px;left:0px;width:20px;height:2px;background-color:' + clr + ';overflow:hidden;\"></div>';
      str += '<div style=\"position:absolute;top:16px;left:0px;width:20px;height:2px;background-color:' + clr + ';overflow:hidden;\"></div>';
      str += '</div>';
   }
   return str;
}

function jsfcore_buildPageStructure(){
   //alert('jsfcore_buildPageStructure started');
   var t_gap = Math.floor((jsfcore_winwidth - jsfcore_globalwidth)/2);
   
   var str = '';

   // only if dev mode is allowed (even so, this is barely visible)
   if(jsfcore_allowdevmode) {
      str += '<div id=\"jsfpb_enterdev\" style=\"position:absolute;top:0px;left:0px;z-index:10;width:10px;height:10px;overflow:hidden;background-color:#F1F1F1;\" onclick=\"jsfpb_enterdevmode();\"></div>';
      str += '<div id=\"jsfpb_exitdev\" style=\"display:none;position:absolute;top:0px;left:0px;z-index:10;background-color:#F1F1F1;font-size:10px;padding:2px;cursor:pointer;\" onclick=\"jsfpb_exitdevmode();\">Dev Mode</div>';
   }
   
   
   str = str + '<div id=\"jsfcore_full\">';
   str = str + '<div id=\"jsfcore_page\">';
   str = str + '<div id=\"jsfcore_header\" style=\"position:fixed;z-index:100;\"></div>';
   str = str + '<div id=\"jsfcore_bg_clr\" style=\"position:fixed;z-index:2;\"></div>';
   str = str + '<div id=\"jsfcore_bg_img\" style=\"position:fixed;z-index:1;\"></div>';
   str = str + '<div id=\"jsfcore_message\" style=\"position:fixed;display:none;z-index:100;opacity:0.92;color:#881111;\"></div>';
   
   str = str + '<div id=\"jsfcore_outermenu\" style=\"display:none;position:fixed;z-index:101;\">';
   str = str + '<div style=\"position:relative;\">';
   str = str + '<div id=\"jsfcore_menu\" style=\"position:absolute;top:0px;left:0px;z-index:101;background-color:' + jsfcore_menu_bgcolor + ';opacity:' + jsfcore_menu_opacity + ';color:' + jsfcore_menu_color + ';\"></div>';
   str = str + '<div id=\"jsfcore_innermenu\" style=\"position:absolute;top:0px;left:0px;z-index:102;color:#FFFFFF;\"></div>';
   str = str + '</div>';
   str = str + '</div>';
   
   str = str + '<div id=\"jsfcore_lightbox\" style=\"display:none;position:fixed;left:0px;top:0px;z-index:102;\"></div>';
   str = str + '<div id=\"jsfcore_loading\" style=\"display:none;position:fixed;z-index:200;background-color:#999999;opacity:0.9;color:#000000;font-style:italic;font-size:20px;font-weight:bold;' + jsfcore_fontfamily + 'text-align:center;padding-top:100px;\">Loading...</div>';
   str = str + '<div id=\"jsfcore_outerbody\">';
   str = str + '  <div id=\"jsfcore_subheader\" style=\"display:none;\"></div>';
   //str = str + '  <div id=\"jsfcore_body\">' + jsfcore_loadinghtml() + '</div>';
   str = str + '  <div id=\"jsfcore_body\"><span style=\"margin:10px;\">Loading...</span></div>';
   str = str + '</div>';
   str = str + '<div id=\"jsfcore_footer\"></div>';
   str = str + '</div>';
   str = str + '</div>';
   jQuery('#' + jsfcore_divid).html(str);
   
   if(jsfcore_allowdevmode && jsfpb_devmode) {
      jQuery('#jsfpb_exitdev').show();
      jQuery('#jsfpb_enterdev').hide();
   }
   
   
   jQuery('#' + jsfcore_divid).css('width', jsfcore_winwidth + 'px').css('min-height',jsfcore_globalheight + 'px');
   //jQuery('#jsfcore_full').css('width', jsfcore_winwidth + 'px').css('min-height',jsfcore_globalheight + 'px').css('position','relative').css('top','0px').css('left','0px').css('z-index','1').css('background-color','#f9c941').css('background-image','URL(' + jsfcore_domain + 'roadmap/jsfcore_bg.jpg)');
   jQuery('#jsfcore_full').css('width', jsfcore_winwidth + 'px').css('min-height',jsfcore_globalheight + 'px').css('position','relative').css('top','0px').css('left','0px').css('z-index','1').css('background-color','#ffffff').css('font-family',jsfcore_font);
   jQuery('#jsfcore_lightbox').css('width', jsfcore_winwidth + 'px').css('height',jsfcore_globalheight + 'px');
   
   jQuery('#jsfcore_header').css('width', jsfcore_globalwidth + 'px').css('height',jsfcore_header_height + 'px').css('left',t_gap + 'px').css('overflow','hidden');
   jQuery('#jsfcore_header').css('background-color',jsfcore_header_bgcolor);
   jQuery('#jsfcore_header').css('color',jsfcore_header_color);
   jQuery('#jsfcore_header').css('opacity',jsfcore_header_opacity);
   
   jQuery('#jsfcore_bg_clr').css('width', jsfcore_globalwidth + 'px').css('top',jsfcore_header_height + 'px').css('height',(jsfcore_globalheight - jsfcore_header_height) + 'px').css('left',t_gap + 'px').css('overflow','hidden');
   jQuery('#jsfcore_bg_clr').css('background-color',jsfcore_bg_clr_bgcolor);
   jQuery('#jsfcore_bg_clr').css('opacity',jsfcore_bg_clr_opacity);
      
   jQuery('#jsfcore_bg_img').css('width', jsfcore_globalwidth + 'px').css('top',jsfcore_header_height + 'px').css('height',(jsfcore_globalheight - jsfcore_header_height) + 'px').css('left',t_gap + 'px').css('overflow','hidden');
   jQuery('#jsfcore_bg_img').css('background-color',jsfcore_bg_img_bgcolor);
   
   jQuery('#jsfcore_message').css('width', jsfcore_globalwidth + 'px').css('height','40px').css('top',jsfcore_header_height + 'px').css('left',t_gap + 'px').css('overflow','hidden');
   
   // Most styling for loading/menu happens inline
   jQuery('#jsfcore_loading').css('width',jsfcore_winwidth + 'px').css('height',jsfcore_globalheight + 'px').css('overflow','hidden');
   
   //jQuery('#jsfcore_menu').css('width', jsfcore_globalwidth + 'px').css('top','60px').css('left',t_gap + 'px');
   //jQuery('#jsfcore_innermenu').css('width', jsfcore_globalwidth + 'px').css('top','60px').css('left',t_gap + 'px');
   
   // Menu height and width
   //var menuwd = jsfcore_globalwidth;
   var menuwd = jsfcore_globalwidth_pgbldr;
   //if(menuwd>500) menuwd = 500;
   if(menuwd>jsfcore_headermaxwidth) menuwd = jsfcore_headermaxwidth;
   var menuht = jsfcore_globalheight - jsfcore_header_height;
   
   //jQuery('#jsfcore_menu').css('width', menuwd + 'px').css('height', menuht + 'px').css('top',jsfcore_header_height + 'px').css('left',(t_gap + (jsfcore_winwidth - menuwd)) + 'px');
   //jQuery('#jsfcore_innermenu').css('width', menuwd + 'px').css('height', menuht + 'px').css('top',jsfcore_header_height + 'px').css('left',(t_gap + (jsfcore_winwidth - menuwd)) + 'px');
   
   //alert('global width: ' + globalwidth + ' window width: ' + winwidth);
   jQuery('#jsfcore_page').css('width', jsfcore_globalwidth + 'px').css('position','relative').css('top','0px').css('left',t_gap + 'px').css('z-index','2').css('background-color','#ffffff');
   //alert('page is done');
   
   //jQuery('#jsfcore_body').css('width', globalwidth + 'px').css('position','relative').css('margin-top','70px').css('left','0px').css('z-index','3').css('background-color','#ffffff').css('overflow','hidden');
   //jQuery('#jsfcore_body').css('width', jsfcore_globalwidth + 'px').css('position','relative').css('padding-top','70px').css('left','0px').css('z-index','3');
   //jQuery('#jsfcore_body').css('width', jsfcore_globalwidth + 'px').css('min-height', (jsfcore_globalheight-60) + 'px').css('position','relative').css('padding-top','0px').css('left','0px').css('z-index','3');
   jQuery('#jsfcore_outerbody').css('width', jsfcore_globalwidth + 'px').css('min-height', (jsfcore_globalheight - jsfcore_header_height - jsfcore_footer_height) + 'px').css('position','relative').css('padding-top',jsfcore_header_height + 'px').css('left','0px').css('z-index','3');
   if(jQuery('#jsfcore_subheader').length>0) jQuery('#jsfcore_subheader').css('position','relative');
   jQuery('#jsfcore_body').css('position','relative');
   
   if(Boolean(jsfcore_footer_height)) {   
      jQuery('#jsfcore_footer').css('width', jsfcore_globalwidth + 'px').css('position','relative').css('left','0px').css('z-index','3');
   }
   
   jsfpb_headerheight = jsfcore_header_height;
   jsfpb_footerheight = jsfcore_footer_height;
   
   var temp_topbuffer = 10;
   var max_logowd = 180;
   if(jsfcore_globalwidth>jsfcore_headermaxwidth) {
      temp_topbuffer = Math.round((jsfcore_globalwidth - jsfcore_headermaxwidth)/2);
      max_logowd = 300;
   } else if(jsfcore_globalwidth>800) {
      temp_topbuffer = 40;
      max_logowd = 240;
   }
   if(Boolean(jsfcore_max_logowd) && !isNaN(jsfcore_max_logowd)) max_logowd = jsfcore_max_logowd;
   
   
   
   if(!Boolean(jsfcore_logo)) jsfcore_logo = jsfcore_basedir + 'logo.png';
   
   str = '';
   if(jsfcore_internallinks) str += '<div id=\"jsfcore_logoouter\" style=\"position:absolute;left:' + temp_topbuffer + 'px;top:10px;overflow:hidden;cursor:pointer;\" onclick=\"jsfcore_showHomePage();\">';
   else str += '<div id=\"jsfcore_logoouter\" style=\"position:absolute;left:' + temp_topbuffer + 'px;top:10px;overflow:hidden;cursor:pointer;\" onclick=\"location.href=\'' + jsfcore_basedir + 'home\';\">';
   str += '<img id=\"jsfcore_logo\" src=\"' + jsfcore_convurl(jsfcore_logo) + '\" style=\"max-width:' + max_logowd + 'px;max-height:' + (jsfcore_header_height - 20) + 'px;height:auto;width:auto;\">';
   str += '</div>';
   
   // Draw a menu icon if screen size is less than 720px;
   if(jsfcore_globalwidth<jsfcore_menucollapsewidth || Boolean(jsfcore_alwayssandwich)) {
      jsfcore_mobile = true;
      str += '<div style=\"position:absolute;font-size:10px;right:60px;top:50px;color:#555555;\" onclick=\"window.localStorage.clear();jsfcore_populateMessage(\'Cache was erased.\',3200);\">.</div>';
      str += '<div style=\"position:absolute;right:' + temp_topbuffer + 'px;top:20px;width:30px;height:30px;overflow:hidden;cursor:pointer;\" onClick=\"jsfcore_togglemenu();\">';
      str += drawMenuIcon(false);
      str += '</div>';
      
      //jQuery('#jsfcore_menu').css('width', menuwd + 'px').css('height', menuht + 'px').css('top',jsfcore_header_height + 'px').css('right',temp_topbuffer + 'px');
      //jQuery('#jsfcore_innermenu').css('width', menuwd + 'px').css('height', menuht + 'px').css('top',jsfcore_header_height + 'px').css('right',temp_topbuffer + 'px');
      jQuery('#jsfcore_outermenu').css('width', menuwd + 'px').css('top',jsfcore_header_height + 'px').css('right',temp_topbuffer + 'px');
      jQuery('#jsfcore_menu').css('width', menuwd + 'px');
      jQuery('#jsfcore_innermenu').css('width', menuwd + 'px');
      
   } else {
      jsfcore_mobile = false;
      str += '<div style=\"position:absolute;font-size:10px;right:60px;top:50px;color:#555555;\" onclick=\"window.localStorage.clear();jsfcore_populateMessage(\'Cache was erased.\',3200);\">.</div>';
      str += '<div id=\"jsfcore_topmenulinks\" style=\"position:absolute;font-size:10px;right:' + temp_topbuffer + 'px;top:20px;color:#555555;\"></div>';
   }
   
   jQuery('#jsfcore_header').html(str);
   
   
   if(Boolean(jsfcore_footer_height)) {   
      str = '';
      str += '<div style=\"position:relative;min-height:' + jsfcore_footer_height + 'px;background-color:' + jsfcore_footer_bgcolor + ';\">';
      str += '<div id=\"jsfcore_menubottomlinks\" style=\"float:left;margin-left:' + temp_topbuffer + 'px;margin-top:20px;\">';
      str += '</div>';
      str += '<div style=\"float:right;margin-top:20px;margin-right:' + temp_topbuffer + 'px;font-size:10px;color:#FFFFFF;' + jsfcore_fontfamily + 'text-align:right;\">';
      str += jsfcore_getcopyright();
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      jQuery('#jsfcore_footer').html(str);
      //alert('just set footer in build structure');
   }
   
   if (typeof jsfcore_customstructure == 'function') jsfcore_customstructure();
         
   // WAIT till structure is built before getting menu items
   //alert('menu started');
   jsfcore_getmenu();
   //alert('menu ended');
   
   //alert('structure done');
}

function jsfcore_getcopyright() {
    var str = '';
   if (typeof jsfcore_customcopyright == 'function') {
      str = jsfcore_customcopyright();
   } else {
      var currentTime = new Date();
     //str += '<a href=\"' + jsfcore_privacypolicy_url + '\" target=\"_new\">Privacy Policy</a>';
     //str += ' &nbsp; &nbsp; ';
     str += 'Copyright &copy; ' + currentTime.getFullYear();
   }
  return str;
}

function jsfcore_togglemenu(closeonly) {
   
   if (typeof jsfcore_customtogglemenu == 'function') {
      str = jsfcore_customtogglemenu(closeonly);
   } else {
      if(jQuery('#jsfcore_menu').is(':visible')) {
         jQuery('#jsfcore_outermenu').fadeOut(100);
      } else if(!Boolean(closeonly)) {
         jQuery('#jsfcore_outermenu').fadeIn(400);
      }
   }
}


function jsfcore_populateMessage(str,s_time,bgcolor,fgcolor){
   var content = '<div style=\"position:relative;width:100%;height:40px;text-align:left;\">';
   content = content + '<div style=\"position:absolute;left:8px;top:8px;height:20px;cursor:pointer;\" onclick=\"jsfcore_emptyMessage();\">';
   //content = content + '<img src=\"/close.png\" style=\"height:20px;width:auto;\">';
   content += '<div style=\"width:20px;height:20px;overflow:hidden;border-radius:10px;border:0px;background-color:RED;color:#FFFFFF;font-weight:bold;text-align:center;font-size:14px;margin-top:4px;\">';
   content += 'x</div>';
   content = content + '</div>';
   content = content + '<div style=\"position:absolute;left:40px;top:10px;font-size:18px;' + jsfcore_fontfamily + '\">';
   content = content + str;
   content = content + '</div>';
   content = content + '</div>';
   
   if(!Boolean(bgcolor)) bgcolor = '#CCCCCC';
   jQuery('#jsfcore_message').css('background-color',bgcolor);
   if(!Boolean(fgcolor)) fgcolor = '#000000';
   jQuery('#jsfcore_message').css('color',fgcolor);
   jQuery('#jsfcore_message').html(content);
   jQuery('#jsfcore_message').fadeIn(500);
   if(Boolean(s_time)) setTimeout(jsfcore_emptyMessage,s_time);
}
function jsfcore_emptyMessage(){
   jQuery('#jsfcore_message').fadeOut(500);
}


function jsfcore_showlightboximage(image) {
   var str = '';
   
   var lb_wd = Math.round(jsfcore_globalwidth * 0.75);
   if(lb_wd<300) lb_wd = 300;
   else if(lb_wd>800) lb_wd = 800;
   var lb_left = Math.round((jsfcore_winwidth - lb_wd)/2);
   
   var lb_ht = Math.round(jsfcore_globalheight * 0.75);
   if(lb_ht<300) lb_ht = 300;
   else if(lb_ht>800) lb_ht = 800;
   var lb_top = Math.round((jsfcore_globalheight - lb_ht)/2);
   
   str += '<div style=\"width:' + jsfcore_winwidth + 'px;height:' + jsfcore_globalheight + 'px;position:relative;\">';
   
   // darken the BG
   str += '   <div style=\"position:absolute;top:0px;left:0px;background-color:#222222;opacity:0.8;width:' + jsfcore_winwidth + 'px;height:' + jsfcore_globalheight + 'px;\"></div>';
   
   
   // position the box
   str += '<div style=\"position:absolute;top:' + lb_top + 'px;left:' + lb_left + 'px;\">';
   
   str += '<div style=\"z-index:2;position:absolute;left:0px;top:0px;width:' + lb_wd + 'px;height:' + lb_ht + 'px;border-radius:8px;overflow:hidden;background-color:#FFFFFF;\"></div>';
   str += '<div style=\"z-index:5;position:relative;width:' + lb_wd + 'px;height:' + lb_ht + 'px;border-radius:8px;overflow:hidden;\">';

   // draw "close" icon
   str += '<div style=\"position:absolute;right:7px;top:7px;z-index:10;\">';
   str += '   <div style=\"position:relative;width:18px;height:18px;background-color:#000000;border-radius:9px;overflow:hidden;cursor:pointer;\" onclick=\"jQuery(\'#jsfcore_lightbox\').fadeOut(200);\">';
   str += '      <div style=\"position:absolute;left:0px;top:0px;width:18px;font-size:14px;' + jsfcore_fontfamily + 'font-weight:bold;text-align:center;color:#FFFFFF;\">x</div>';
   str += '   </div>';
   str += '</div>';
   
   // display the content of this box
   str += '<div style=\"position:relative;left:10px;top:10px;z-index:1;width:' + (lb_wd - 20) + 'px;height:' + (lb_ht - 20) + 'px;overflow:hidden;\">';
   str += '<img src=\"' + jsfcore_convurl(image) + '\" style=\"max-height:' + (lb_ht - 20) + 'px;max-width:' + (lb_wd - 20) + 'px;width:auto;height:auto;\">';
   str += '</div>';

   str += '</div>';
   
   str += '</div>';   
   str += '</div>';
   
   jQuery('#jsfcore_lightbox').html(str);
   jQuery('#jsfcore_lightbox').fadeIn(400);
}


function jsfcore_showlightbox(txt,bgimg) {
   var str = '';
   
   var lb_wd = Math.round(jsfcore_globalwidth * 0.7);
   var lb_ht = Math.round(jsfcore_globalheight * 0.75);
   
   if(lb_wd<325) {
      lb_wd = jsfcore_globalwidth;
      lb_ht = jsfcore_globalheight;
   } else if(lb_wd<400) {
      lb_wd = jsfcore_globalwidth - 20;
   } else if(lb_wd>600) {
      lb_wd = 600;
   }
   var lb_left = Math.round((jsfcore_winwidth - lb_wd)/2);
   
   if(lb_ht<300) lb_ht = 300;
   else if(lb_ht>800) lb_ht = 800;
   var lb_top = Math.round((jsfcore_globalheight - lb_ht)/2);
   
   str += '<div style=\"width:' + jsfcore_winwidth + 'px;height:' + jsfcore_globalheight + 'px;position:relative;\">';
   
   // darken the BG
   str += '   <div style=\"position:absolute;top:0px;left:0px;background-color:#222222;opacity:0.8;width:' + jsfcore_winwidth + 'px;height:' + jsfcore_globalheight + 'px;\"></div>';
   
   
   // position the box
   str += '<div style=\"position:absolute;top:' + lb_top + 'px;left:' + lb_left + 'px;\">';
   
   // draw the box
   if(Boolean(bgimg)) {
      str += '<div style=\"z-index:1;position:absolute;left:0px;top:0px;width:' + lb_wd + 'px;height:' + lb_ht + 'px;border-radius:8px;overflow:hidden;background-image:URL(' + jsfcore_convurl(bgimg) + ');background-size:cover;background-position:center;\"></div>';
      str += '<div style=\"z-index:2;position:absolute;left:0px;top:0px;width:' + lb_wd + 'px;height:' + lb_ht + 'px;border-radius:8px;overflow:hidden;background-color:#FFFFFF;opacity:0.9;\"></div>';
   } else {
      str += '<div style=\"z-index:2;position:absolute;left:0px;top:0px;width:' + lb_wd + 'px;height:' + lb_ht + 'px;border-radius:8px;overflow:hidden;background-color:#FFFFFF;\"></div>';
   }
   str += '<div style=\"z-index:5;position:relative;width:' + lb_wd + 'px;height:' + lb_ht + 'px;border-radius:8px;overflow:hidden;\">';

   // draw "close" icon
   str += '<div style=\"position:absolute;right:7px;top:7px;z-index:10;\">';
   str += '   <div style=\"position:relative;width:18px;height:18px;background-color:#000000;border-radius:9px;overflow:hidden;cursor:pointer;\" onclick=\"jQuery(\'#jsfcore_lightbox\').fadeOut(200);\">';
   str += '      <div style=\"position:absolute;left:0px;top:0px;width:18px;font-size:14px;' + jsfcore_fontfamily + 'font-weight:bold;text-align:center;color:#FFFFFF;\">x</div>';
   str += '   </div>';
   str += '</div>';
   
   // display the content of this box
   str += '<div style=\"position:relative;left:10px;top:10px;z-index:1;width:' + (lb_wd - 20) + 'px;height:' + (lb_ht - 20) + 'px;overflow:hidden;font-size:14px;\">';
   str += txt;
   str += '</div>';

   str += '</div>';
   
   str += '</div>';   
   str += '</div>';
   
   jQuery('#jsfcore_lightbox').html(str);
   jQuery('#jsfcore_lightbox').fadeIn(400);
}


function jsfcore_checktime(tm1,tm2,delta) {
   var timeok = false;
   if(!Boolean(delta) || !Boolean(tm1) || !Boolean(tm2)) timeok=true;
   else if((parseInt(tm2) - parseInt(tm1))>(parseInt(delta)*1000)) timeok=true;
   //alert(tm1 + ' ' + tm2 + ' ' + delta + ' ' + timeok);
   return timeok;
}

function jsfcore_getstoppropagationjs() {
   var str = 'if (!event) var event = window.event;event.cancelBubble = true;if (event.stopPropagation) event.stopPropagation();';
   return str;
}


function jsfcore_rotateimage(fn,degrees,wd,ht){
   jQuery('#jsfcore_imagetorotate').html('');
   var url = '';
   url += '&degrees=' + degrees;
   url += '&fn=' + encodeURIComponent(fn);
   url += '&passthru1=' + encodeURIComponent(wd);
   url += '&passthru2=' + encodeURIComponent(ht);
   jsfcore_QuickJSON('rotateimage','jsfcore_returnrotateimage',url);
}

function jsfcore_returnrotateimage(jsondata){
   jsfcore_ReturnJSON(jsondata);
   //alert(JSON.stringify(jsondata));
   
   var str = '<img src=\"' + jsfcore_convurl(jsondata.fn) + '?i=' + jsfcore_getRandomInt(1,1000) + '\" style=\"max-width:' + jsondata.passthru1 + 'px;max-height:' + jsondata.passthru2 + 'px;width:auto;height:auto;\">';
   jQuery('#jsfcore_imagetorotate').html(str);
   jQuery('#jsfcore_imagetorotate_txt').fadeIn(400);
}

function jsfcore_explodequery(url) {
   var params = {};
   if(Boolean(url)) {
      var urlarr = url.split('?');
      var uri;
      if(Boolean(urlarr[1])) uri = urlarr[1];
      else uri = urlarr[0];
      
      var vals = uri.split('&');
      for(var i=0; i<vals.length; i++){
         if(Boolean(vals[i])) {
            var xarr = vals[i].split('=');
            if(Boolean(xarr[0]) && Boolean(xarr[1])) params[xarr[0]] = decodeURIComponent(xarr[1]);
         }
      }
   }
   return params;
}