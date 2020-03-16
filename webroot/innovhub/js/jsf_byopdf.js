//--------------------------------------
// widget to be used on a site to build
// your own pdf file
//
//
// <div id="pmcs"></div>
// <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
// <script src="https://www.plasticsmarkets.org/jsfcode/jsf_byopdf.js"></script>
// <script>
// byopdf_pdfname = 'ChadTest1';
// byopdf_pdfuserid = 1;
// byopdf_ready_init();
// </script>
//--------------------------------------
var byopdf_domain = 'https://www.plasticsmarkets.org/';

var byopdf_displayFunct;
var byopdf_historyscr=[];
var byopdf_historycnt=0;

var byopdf_dynvalues={};
var byopdf_divid = 'pmcs';
var byopdf_addl_left = 0;
var byopdf_addl_top = 0;

var byopdf_next_displayfunct;
var byopdf_next_successmessage;
var byopdf_next_errormessage;


var byopdf_winwidth;
var byopdf_globalwidth;
var byopdf_globalheight;
var byopdf_globalmaxwidth = 999999999;

var byopdf_view;

var byopdf_headerheight = 10;
var byopdf_footerheight = 10;

var byopdf_ptspi = 72;
var byopdf_pxpi = 300;

var byopdf_font = 'verdana';
var byopdf_fontfamily = 'font-family:' + byopdf_font + ';';

var temp;
var tempid;

function byopdf_QuickJSON(action,callback,query,checkcache){
   var runjson = true;
   if (Boolean(action) && Boolean(callback)) {
      var url = byopdf_domain + 'jsfcode/jsonpcontroller.php?action=' + encodeURIComponent(action);
      if (Boolean(query)) url = url + query;
      var saveurl = url;
      url = url + '&callback=' + encodeURIComponent(callback);
      //alert('URL: ' + url);
      if(Boolean(checkcache)) {
         //alert('checking cache: ' + url);
         var str = window.localStorage.getItem('byopdf_cache');
         if(Boolean(str)){
            //alert('found cache: ' + url);
            var jsf_cache = JSON.parse(str);
            if(jsf_cache.expiry<(Math.floor(Date.now() / 1000))) {
               //alert('expired cache: ' + url);
               jsf_cache = '';
               window.localStorage.removeItem('byopdf_cache');
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
         byopdf_showloading();
         if(Boolean(checkcache)) url = url + '&jsonsaveval=' + encodeURIComponent(saveurl);
         //alert('NOT using cache: ' + url);
         byopdf_CallJSONP(url);
      }
   }   
}

function byopdf_ReturnJSON(jsondata){
   byopdf_hideloading();
   //alert(JSON.stringify(jsondata));
   if (Boolean(jsondata) && Boolean(jsondata.jsonsaveval)) {
      //alert('CHJ***** checking cache: jsf_endjsoning  url: ' + jsondata.jsonsaveval);
      var jsf_cache = {};
      jsf_cache.expiry = (Math.floor(Date.now() / 1000) + (60*60*24));
      jsf_cache.countindex = 1;
      var str = window.localStorage.getItem('byopdf_cache');
      window.localStorage.removeItem('byopdf_cache');
      if(Boolean(str)) {
         //alert('found jsf_cache, checking expiry...');
         temp = JSON.parse(str);
         if(Boolean(temp) && temp.expiry>(Math.floor(Date.now() / 1000)) && temp.countindex<150) {
            jsf_cache = temp;
         }
      }
      if(!Boolean(jsf_cache[jsondata.jsonsaveval])) jsf_cache.countindex++;
      jsf_cache[jsondata.jsonsaveval] = jsondata;
      window.localStorage.setItem('byopdf_cache',JSON.stringify(jsf_cache));
   }
}

function byopdf_CallJSONP(url) {
   //alert('byopdf_CallJSONP: ' + url);
    var script = document.createElement('script');
    script.setAttribute('src', url);
    document.getElementsByTagName('head')[0].appendChild(script);
}
 
function byopdf_AlertJSONPRequest(jsondata){
   byopdf_ReturnJSON(jsondata);
   alert(JSON.stringify(jsondata));
}

function byopdf_donothing(jsondata){
   //byopdf_ReturnJSON(jsondata);   
   //alert(JSON.stringify(jsondata));
}

function byopdf_trackitem(rmpaction,str1,str2){
   var view = 'PlasticMarkets.org';
   var str3 = location.hostname;
   if(str3.substr(0,4)=='www.') str3 = str3.substr(4);
   
   var url = byopdf_domain + 'jsfcode/jsonpcontroller.php?action=trackitem';
   url = url + '&view=' + encodeURIComponent(view);
   if (Boolean(rmpaction)) url = url + '&foraction=' + encodeURIComponent(rmpaction);
   if (Boolean(str1)) url = url + '&jsftrack1=' + encodeURIComponent(str1);
   if (Boolean(str2)) url = url + '&jsftrack2=' + encodeURIComponent(str2);
   if (Boolean(str3)) url = url + '&jsftrack3=' + encodeURIComponent(str3);
   if (Boolean(byopdf_globaluser) && Boolean(byopdf_globaluser.userid)) url = url + '&userid=' + byopdf_globaluser.userid;
   url = url + '&callback=byopdf_donothing';
   url = url + '&referer=' + encodeURIComponent(document.referrer);

   //alert('url: ' + url);
   byopdf_CallJSONP(url);
}

function byopdf_getwebdata_jsonp(wdname,callback,params,checkcache){
   var query = '';
   if (Boolean(wdname)) query += '&wdname=' + encodeURIComponent(wdname);
   if (Boolean(params)) query += params;
   byopdf_QuickJSON('getwdandrows',callback,query,checkcache);   
}

function byopdf_explodequery(url) {
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


function byopdf_replaceAll(find, replace, str) {
  return str.replace(new RegExp(find, 'g'), replace);
}

function byopdf_convertstring(str){
   var temp = '';
   if(Boolean(str)) {
      // Remove any non-ascii character
      temp = str.replace(/[^\x00-\x7F]/g, "");
      
      // Convert special characters for javascript
      temp = byopdf_replaceAll('\"','#jsfquote#',temp);
      temp = byopdf_replaceAll('\'','#jsfsquote#',temp);
      temp = byopdf_replaceAll('\n','#jsflf#',temp);
      temp = byopdf_replaceAll('\r','#jsfcr#',temp);
      temp = byopdf_replaceAll('&bull;','#jsfbullet#',temp);
   }
   return temp;
}

function byopdf_convertback(str){
   var temp = '';
   if(Boolean(str)) {
      temp = byopdf_replaceAll('#jsfquote#','\"',str);
      temp = byopdf_replaceAll('#jsfsquote#','\'',temp);
      temp = byopdf_replaceAll('#jsflf#','\n',temp);
      temp = byopdf_replaceAll('#jsfcr#','\r',temp);
      temp = byopdf_replaceAll('#jsfbullet#','&bull;',temp);
      temp = byopdf_replaceAll('&#44;',',',temp);
   }
   return temp;
}

function byopdf_convertdisplay(str){
   var temp = '';
   if(Boolean(str)) {   
      temp = byopdf_replaceAll('#jsfquote#','\"',str);
      temp = byopdf_replaceAll('#jsfsquote#','\'',temp);
      temp = byopdf_replaceAll('#jsflf#','<br>',temp);
      temp = byopdf_replaceAll('#jsfcr#','',temp);
      temp = byopdf_replaceAll('#jsfbullet#','&bull;',temp);
      
      //Automatic link and email replacement...
       var replacePattern1, replacePattern2, replacePattern3;
   
       //URLs starting with http://, https://, or ftp://
       replacePattern1 = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
       temp = temp.replace(replacePattern1, '<a href="$1" target="_blank">$1</a>');
   
       //URLs starting with "www." (without // before it, or it'd re-link the ones done above).
       replacePattern2 = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
       temp = temp.replace(replacePattern2, '$1<a href="http://$2" target="_blank">$2</a>');
   
       //Change email addresses to mailto:: links.
       replacePattern3 = /(([a-zA-Z0-9\-\_\.])+@[a-zA-Z\_]+?(\.[a-zA-Z]{2,6})+)/gim;
       temp = temp.replace(replacePattern3, '<a href="mailto:$1">$1</a>');
   }
   
   return temp;
}


function byopdf_getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}   



var byopdf_initpaths;
var byopdf_inittries=0;
function byopdf_ready_init() {
   byopdf_inittries++;
   
   if (!location.href.toLowerCase().startsWith('https:')) {
      location.href = byopdf_replaceAll('http:','https:',location.href);
   } else {
         byopdf_historyscr = [];
         byopdf_historycnt = 0;
         
         /*
         var initpathname = window.location.pathname;
         byopdf_initpaths = initpathname.substr(1).split('/');
         if(byopdf_initpaths[0]=='view') {
            byopdf_remember_pagename = byopdf_initpaths[1];
            byopdf_displayFunct = byopdf_showPage;
         } else if(byopdf_initpaths[0]=='search') {
            byopdf_lastsearchphrase = byopdf_initpaths[1];
            byopdf_displayFunct = byopdf_showsearchresults;
         } else if(byopdf_initpaths[0]=='profile') {
            byopdf_profileid = byopdf_initpaths[1];
            byopdf_displayFunct = byopdf_showprofile;            
         } else if(byopdf_initpaths[0]=='pcrproduct') {
            byopdf_remember_pagename = 'buyrecycled';
            byopdf_displayFunct = byopdf_showPage;
         } else if(byopdf_initpaths[0]=='pcrbizproduct') {
            byopdf_remember_pagename = 'buyrecycled-business';
            byopdf_displayFunct = byopdf_showPage;
         } else if(Boolean(byopdf_initpaths[0]) && !Boolean(byopdf_initpaths[1])) {
            byopdf_remember_pagename = byopdf_initpaths[0];
            byopdf_displayFunct = byopdf_showPage;
         }
         */
         byopdf_fixwidths();
   }
}



var byopdf_tri=0;
function byopdf_drawtoggle(str,oc1,oc2) {
   byopdf_tri++;
   var html = '';
   html += '<table cellpadding=\"0\" cellspacing=\"0\"><tr><td>';
   html += '<div onclick=\"byopdf_toggleTri' + byopdf_tri + '();\" style=\"width:18px;height:18px;border-radius:9px;background-color:#E0E0E0;\">';
   html += '<div id=\"byopdf_tri_right' + byopdf_tri + '\" style=\"position:relative;left:5px;top:4px;\">';
   html += '<div style=\"width: 0;height: 0;border-top-width: 5px;border-top-style: solid;border-top-color: transparent;border-left-width: 9px;border-left-style: solid;border-left-color: #777777;\"></div>';
   html += '<div style=\"width: 0;height: 0;border-bottom-width: 5px;border-bottom-style: solid;border-bottom-color: transparent;border-left-width: 9px;border-left-style: solid;border-left-color: #777777;\"></div>';
   html += '</div>';

   html += '<div id=\"byopdf_tri_down' + byopdf_tri + '\" style=\"position:relative;left:4px;top:5px;display:none;\">';
   html += '<div style=\"float:left;width: 0;height: 0;border-bottom-width: 9px;border-bottom-style: solid;border-bottom-color: transparent;border-right-width: 5px;border-right-style: solid;border-right-color: #777777;\"></div>';
   html += '<div style=\"float:left;width: 0;height: 0;border-bottom-width: 9px;border-bottom-style: solid;border-bottom-color: transparent;border-left-width: 5px;border-left-style: solid;border-left-color: #777777;\"></div>';
   html += '<div style=\"clear:both;\"></div>';
   html += '</div>';
   html += '</div>';
   
   html += '</td><td>';
   html += '<span style=\"padding-left:4px;\">' + str + '</span>';
   html += '</td></tr></table>';
   
   html += '<script>';
   html += 'var byopdf_tri_' + byopdf_tri + ' = 1;';
   html += 'function byopdf_toggleTri' + byopdf_tri + '(){';
   html += 'if(byopdf_tri_' + byopdf_tri + '==1) {';
   html += '   byopdf_tri_' + byopdf_tri + ' = 0;';
   html += '   document.getElementById(\'byopdf_tri_right' + byopdf_tri + '\').style.display = \'none\';';
   html += '   document.getElementById(\'byopdf_tri_down' + byopdf_tri + '\').style.display = \'\';';
   html += oc1;
   html += '} else {';
   html += '   byopdf_tri_' + byopdf_tri + ' = 1;';
   html += '   document.getElementById(\'byopdf_tri_down' + byopdf_tri + '\').style.display = \'none\';';
   html += '   document.getElementById(\'byopdf_tri_right' + byopdf_tri + '\').style.display = \'\';';
   html += oc2;
   html += '}';
   html += '}';
   html += '</script>';

   return html;
}



// END basic functions
//--------------------------------------------------------------------------
var byopdf_constwd = 0;
var byopdf_constht = 0;

function byopdf_fixwidths(){
   //if(!Boolean(byopdf_values)) byopdf_primedata();   
   
   byopdf_winwidth = jQuery(window).width();
   if(Boolean(byopdf_constwd)) byopdf_winwidth = byopdf_constwd;
   
   byopdf_globalwidth = byopdf_winwidth;
   if(Boolean(byopdf_globalmaxwidth) && byopdf_globalmaxwidth>400 && byopdf_globalwidth>byopdf_globalmaxwidth) byopdf_globalwidth = byopdf_globalmaxwidth;
   
   byopdf_globalheight = jQuery(window).height();
   if(Boolean(byopdf_constht)) byopdf_globalheight = byopdf_constht;
   
   //alert('temp msg height: ' + byopdf_globalheight);
      
   byopdf_buildPageStructure();   
   byopdf_display();
}   
   
function byopdf_buildPageStructure(){
   //alert('byopdf_buildPageStructure started');
   var t_gap = Math.floor((byopdf_winwidth - byopdf_globalwidth)/2);
   
   var str = '';
   str = str + '<div id=\"byopdf_full\">';
   str = str + '<div id=\"byopdf_page\">';
   str = str + '<div id=\"byopdf_header\"></div>';
   str = str + '<div id=\"byopdf_message\" style=\"position:fixed;display:none;z-index:100;background-color:#DDDDDD;opacity:0.99;color:#881111;\"></div>';
   str = str + '<div id=\"byopdf_menu\" style=\"display:none;position:fixed;z-index:101;background-color:#000000;opacity:0.90;color:#FFFFFF;\"></div>';
   str = str + '<div id=\"byopdf_innermenu\" style=\"display:none;position:fixed;z-index:102;color:#FFFFFF;\"></div>';
   str = str + '<div id=\"byopdf_lightbox\" style=\"display:none;position:fixed;left:0px;top:0px;z-index:102;\"></div>';
   str = str + '<div id=\"byopdf_loading\" style=\"display:none;position:fixed;z-index:200;background-color:#999999;opacity:0.9;color:#000000;font-style:italic;font-size:20px;font-weight:bold;' + byopdf_fontfamily + 'text-align:center;padding-top:100px;\">Loading...</div>';
   str = str + '<div id=\"byopdf_body\">' + byopdf_loadinghtml() + '</div>';
   str = str + '<div id=\"byopdf_footer\"></div>';
   str = str + '</div>';
   str = str + '</div>';
   jQuery('#' + byopdf_divid).html(str);
   
   jQuery('body').css('background-color','#ffffff');   
   jQuery('#' + byopdf_divid).css('width', byopdf_winwidth + 'px').css('min-height',byopdf_globalheight + 'px');
   jQuery('#byopdf_full').css('width', byopdf_winwidth + 'px').css('min-height',byopdf_globalheight + 'px').css('position','relative').css('top','0px').css('left','0px').css('z-index','1').css('font-family',byopdf_font);
   jQuery('#byopdf_lightbox').css('width', byopdf_winwidth + 'px').css('height',byopdf_globalheight + 'px');
   jQuery('#byopdf_message').css('width', byopdf_globalwidth + 'px').css('height','30px').css('top','2px').css('left',t_gap + 'px').css('overflow','hidden');
   jQuery('#byopdf_loading').css('width',byopdf_winwidth + 'px').css('height',byopdf_globalheight + 'px').css('overflow','hidden');
   jQuery('#byopdf_header').css('position', 'fixed').css('z-index','100').css('width',byopdf_winwidth + 'px').css('height',byopdf_headerheight + 'px').css('overflow','hidden').css('top','0px').css('left','0px').css('background-color','#FFFFFF').css('color','#000000');
   
   var menuwd = byopdf_globalwidth;
   if(menuwd>500) menuwd = 500;
   var menuht = byopdf_globalheight - byopdf_headerheight;
   jQuery('#byopdf_menu').css('width', menuwd + 'px').css('height', menuht + 'px').css('top',byopdf_headerheight + 'px').css('left',(t_gap + (byopdf_winwidth - menuwd)) + 'px');
   jQuery('#byopdf_innermenu').css('width', menuwd + 'px').css('height', menuht + 'px').css('top',byopdf_headerheight + 'px').css('left',(t_gap + (byopdf_winwidth - menuwd)) + 'px');
   jQuery('#byopdf_page').css('width', byopdf_winwidth + 'px').css('position','relative').css('top','0px').css('left','0px').css('z-index','2');
   jQuery('#byopdf_body').css('width', byopdf_winwidth + 'px').css('position','relative').css('padding-top',byopdf_headerheight + 'px').css('left','0px').css('z-index','3').css('min-height',(byopdf_globalheight - byopdf_headerheight - byopdf_footerheight) + 'px').css('margin-bottom',byopdf_footerheight + 'px');
   jQuery('#byopdf_footer').css('position', 'fixed').css('z-index','100').css('width', byopdf_winwidth + 'px').css('height',byopdf_footerheight + 'px').css('overflow','hidden').css('left','0px').css('bottom','0px').css('z-index','3').css('background-color','#ffffff');
   
   var hdrstr = '';
   var ftrstr = '';
   /*
   hdrstr += '<div style=\"position:relative;\">';
   hdrstr += '<img src=\"/logo.png\" style=\"position:absolute;top:10px;left:' + (t_gap + 10) + 'px;max-height:' + (byopdf_headerheight - 20) + 'px;max-width:' + (byopdf_winwidth - byopdf_headerheight - 20) + 'px;height:auto;width:auto;cursor:pointer;\" onclick=\"location.href=\'/view/home\';\">';
   var miconpos = 20;
   hdrstr += '<div id=\"byopdf_horizmenu\" style=\"position:absolute;right:' + t_gap + 'px;top:' + miconpos + 'px;\">';
   hdrstr += '</div>';
   hdrstr += '</div>';
   */
   
   ftrstr += '<div style=\"position:relative;width:100%;height:' + byopdf_footerheight + 'px;overflow:hidden;\" onclick=\"window.localStorage.clear();alert(\'cached cleared\');\">';
   ftrstr += '</div>';
   
   jQuery('#byopdf_header').html(hdrstr);
   jQuery('#byopdf_footer').html(ftrstr);
   
}

function byopdf_getcopyright() {
   var d = new Date();
   var str = '';
   str += '&copy; ' + d.getFullYear();
   str += ' All rights reserved ';
   return str;
}

function byopdf_togglemenu(closeonly) {
   if(jQuery('#byopdf_menu').is(':visible')) {
      jQuery('#byopdf_menu').fadeOut(100);
      jQuery('#byopdf_innermenu').fadeOut(100);
   } else if(!Boolean(closeonly)) {
      jQuery('#byopdf_menu').fadeIn(400);
      jQuery('#byopdf_innermenu').fadeIn(400);
   }
}


function byopdf_populateMessage(str,s_time,bgcolor,fgcolor){
   var content = '<div style=\"position:relative;width:100%;height:30px;text-align:left;\">';
   content = content + '<div style=\"position:absolute;left:5px;top:7px;height:16px;cursor:pointer;\" onclick=\"byopdf_emptyMessage();\">';
   content = content + '<img src=\"/close.png\" style=\"height:16px;width:auto;\">';
   content = content + '</div>';
   content = content + '<div style=\"position:absolute;left:30px;top:5px;font-size:14px;' + byopdf_fontfamily + '\">';
   content = content + str;
   content = content + '</div>';
   content = content + '</div>';
   
   if(Boolean(bgcolor)) jQuery('#byopdf_message').css('background-color',bgcolor);
   if(Boolean(fgcolor)) jQuery('#byopdf_message').css('color',fgcolor);
   jQuery('#byopdf_message').html(content);
   jQuery('#byopdf_message').fadeIn(500);
   if(Boolean(s_time)) setTimeout(byopdf_emptyMessage,s_time);
}
function byopdf_emptyMessage(){
   jQuery('#byopdf_message').fadeOut(500);
}


function byopdf_showlightboximage(image) {
   var str = '';
   
   var lb_wd = Math.round(byopdf_globalwidth * 0.75);
   if(lb_wd<300) lb_wd = 300;
   else if(lb_wd>800) lb_wd = 800;
   var lb_left = Math.round((byopdf_winwidth - lb_wd)/2);
   
   var lb_ht = Math.round(byopdf_globalheight * 0.75);
   if(lb_ht<300) lb_ht = 300;
   else if(lb_ht>800) lb_ht = 800;
   var lb_top = Math.round((byopdf_globalheight - lb_ht)/2);
   
   str += '<div style=\"width:' + byopdf_winwidth + 'px;height:' + byopdf_globalheight + 'px;position:relative;\">';
   
   // darken the BG
   str += '   <div style=\"position:absolute;top:0px;left:0px;background-color:#222222;opacity:0.8;width:' + byopdf_winwidth + 'px;height:' + byopdf_globalheight + 'px;\"></div>';
   
   
   // position the box
   str += '<div style=\"position:absolute;top:' + lb_top + 'px;left:' + lb_left + 'px;\">';
   
   str += '<div style=\"z-index:2;position:absolute;left:0px;top:0px;width:' + lb_wd + 'px;height:' + lb_ht + 'px;border-radius:8px;overflow:hidden;background-color:#FFFFFF;\"></div>';
   str += '<div style=\"z-index:5;position:relative;width:' + lb_wd + 'px;height:' + lb_ht + 'px;border-radius:8px;overflow:hidden;\">';

   // draw "close" icon
   str += '<div style=\"position:absolute;right:7px;top:7px;z-index:10;\">';
   str += '   <div style=\"position:relative;width:18px;height:18px;background-color:#000000;border-radius:9px;overflow:hidden;cursor:pointer;\" onclick=\"jQuery(\'#byopdf_lightbox\').fadeOut(200);\">';
   str += '      <div style=\"position:absolute;left:0px;top:0px;width:18px;font-size:14px;' + byopdf_fontfamily + 'font-weight:bold;text-align:center;color:#FFFFFF;\">x</div>';
   str += '   </div>';
   str += '</div>';
   
   // display the content of this box
   str += '<div style=\"position:relative;left:10px;top:10px;z-index:1;width:' + (lb_wd - 20) + 'px;height:' + (lb_ht - 20) + 'px;overflow:hidden;\">';
   str += '<img src=\"' + byopdf_replaceAll('http:','https:',image) + '\" style=\"max-height:' + (lb_ht - 20) + 'px;max-width:' + (lb_wd - 20) + 'px;width:auto;height:auto;\">';
   str += '</div>';

   str += '</div>';
   
   str += '</div>';   
   str += '</div>';
   
   jQuery('#byopdf_lightbox').html(str);
   jQuery('#byopdf_lightbox').fadeIn(400);
}


function byopdf_showlightbox(txt,bgimg) {
   var str = '';
   
   var lb_wd = Math.round(byopdf_globalwidth/2);
   if(lb_wd<300) lb_wd = 300;
   else if(lb_wd>600) lb_wd = 600;
   var lb_left = Math.round((byopdf_winwidth - lb_wd)/2);
   
   var lb_ht = Math.round(byopdf_globalheight * 0.75);
   if(lb_ht<300) lb_ht = 300;
   else if(lb_ht>600) lb_ht = 600;
   var lb_top = Math.round((byopdf_globalheight - lb_ht)/2);
   
   str += '<div style=\"width:' + byopdf_winwidth + 'px;height:' + byopdf_globalheight + 'px;position:relative;\">';
   
   // darken the BG
   str += '   <div style=\"position:absolute;top:0px;left:0px;background-color:#222222;opacity:0.8;width:' + byopdf_winwidth + 'px;height:' + byopdf_globalheight + 'px;\"></div>';
   
   
   // position the box
   str += '<div style=\"position:absolute;top:' + lb_top + 'px;left:' + lb_left + 'px;\">';
   
   // draw the box
   if(Boolean(bgimg)) {
      str += '<div style=\"z-index:1;position:absolute;left:0px;top:0px;width:' + lb_wd + 'px;height:' + lb_ht + 'px;border-radius:8px;overflow:hidden;background-image:URL(' + byopdf_replaceAll('http:','https:',bgimg) + ');background-size:cover;background-position:center;\"></div>';
      str += '<div style=\"z-index:2;position:absolute;left:0px;top:0px;width:' + lb_wd + 'px;height:' + lb_ht + 'px;border-radius:8px;overflow:hidden;background-color:#FFFFFF;opacity:0.9;\"></div>';
   } else {
      str += '<div style=\"z-index:2;position:absolute;left:0px;top:0px;width:' + lb_wd + 'px;height:' + lb_ht + 'px;border-radius:8px;overflow:hidden;background-color:#FFFFFF;\"></div>';
   }
   str += '<div style=\"z-index:5;position:relative;width:' + lb_wd + 'px;height:' + lb_ht + 'px;border-radius:8px;overflow:hidden;\">';

   // draw "close" icon
   str += '<div style=\"position:absolute;right:7px;top:7px;z-index:10;\">';
   str += '   <div style=\"position:relative;width:18px;height:18px;background-color:#000000;border-radius:9px;overflow:hidden;cursor:pointer;\" onclick=\"jQuery(\'#byopdf_lightbox\').fadeOut(200);\">';
   str += '      <div style=\"position:absolute;left:0px;top:0px;width:18px;font-size:14px;' + byopdf_fontfamily + 'font-weight:bold;text-align:center;color:#FFFFFF;\">x</div>';
   str += '   </div>';
   str += '</div>';
   
   // display the content of this box
   str += '<div style=\"position:relative;left:10px;top:10px;z-index:1;width:' + (lb_wd - 20) + 'px;height:' + (lb_ht - 20) + 'px;overflow:hidden;font-size:14px;\">';
   str += txt;
   str += '</div>';

   str += '</div>';
   
   str += '</div>';   
   str += '</div>';
   
   jQuery('#byopdf_lightbox').html(str);
   jQuery('#byopdf_lightbox').fadeIn(400);
}


function byopdf_checktime(tm1,tm2,delta) {
   var timeok = false;
   if(!Boolean(delta) || !Boolean(tm1) || !Boolean(tm2)) timeok=true;
   else if((parseInt(tm2) - parseInt(tm1))>(parseInt(delta)*1000)) timeok=true;
   //alert(tm1 + ' ' + tm2 + ' ' + delta + ' ' + timeok);
   return timeok;
}

function byopdf_showlightboxupload(activity,fn,notes,wd_row_id) {
   var yt = byopdf_getactivityobj(activity);   
   var str = '';
   
   var lb_wd = Math.round(byopdf_globalwidth/2);
   if(lb_wd<360) lb_wd = 360;
   else if(lb_wd>800) lb_wd = 800;
   var lb_left = Math.round((byopdf_winwidth - lb_wd)/2);
   
   var lb_ht = Math.floor((lb_wd) * 9/16);
   var lb_top = Math.round((byopdf_globalheight - lb_ht)/2);
   
   str += '<div style=\"width:' + byopdf_winwidth + 'px;height:' + byopdf_globalheight + 'px;position:relative;\">';
   
   // darken the BG
   str += '   <div style=\"position:absolute;top:0px;left:0px;background-color:#222222;opacity:0.8;width:' + byopdf_winwidth + 'px;height:' + byopdf_globalheight + 'px;\"></div>';
   
   
   // position the box
   str += '<div style=\"position:absolute;top:' + lb_top + 'px;left:' + lb_left + 'px;\">';
   
   // draw the box
   str += '<div style=\"position:relative;background-color:#FFFFFF;border-radius:8px;width:' + lb_wd + 'px;height:' + lb_ht + 'px;overflow:hidden;\">';

   // draw "close" icon
   str += '<div style=\"position:absolute;right:5px;top:5px;z-index:10;\">';
   str += '   <div style=\"position:relative;width:26px;height:26px;background-color:#111111;border-radius:13px;overflow:hidden;cursor:pointer;\" ';
   str += ' onclick=\"jQuery(\'#byopdf_lightbox\').fadeOut(300,\'swing\',function(){jQuery(\'#byopdf_lightbox\').html(\'\');});';
   //if(Boolean(givepts)) str += ' if(byopdf_checktime(\'' + timecheck + '\',Date.now(),\'' + yt.seconds + '\')) byopdf_submituseractivity(' +  activity + ');';
   str += '\">';
   str += '      <div style=\"position:absolute;left:0px;top:0px;width:26px;font-size:18px;' + byopdf_fontfamily + 'font-weight:bold;text-align:center;color:#ffffff;\">x</div>';
   str += '   </div>';
   str += '</div>';
   
   // display the content of this box
   str += '<div style=\"position:relative;left:30px;top:30px;z-index:1;width:' + (lb_wd - 60) + 'px;height:' + (lb_ht - 60) + 'px;overflow:hidden;font-size:14px;\">';
   var imgwd = Math.round((lb_wd - 60)/2);
   if(imgwd>250) imgwd = (lb_wd - 60 - 250);
   else if(imgwd<160) imgwd = (lb_wd - 60 - 160);
   str += '<div style=\"position:absolute;left:0px;top:0px;width:' + imgwd + 'px;height:' + (lb_ht - 60) + 'px;\">';
   str += '<div style=\"position:relative;width:' + imgwd + 'px;height:' + (lb_ht - 60) + 'px;overflow:hidden;\">';
   str += '<div id=\"byopdf_imagetorotate\" style=\"z-index:1;\">';
   str += '<img src=\"' + byopdf_replaceAll('http:','https:',fn) + '?i=' + byopdf_getRandomInt(1,1000) + '\" style=\"max-width:' + imgwd + 'px;max-height:' + (lb_ht - 60) + 'px;width:auto;height:auto;\">';
   str += '</div>';
   str += '<div style=\"z-index:2;position:absolute;left:5px;top:5px;\">';
   str += '<img src=\"' + byopdf_domain + 'jsfimages/clockwise.jpg\" style=\"width:auto;height:25px;cursor:pointer;opacity:0.6;\" onclick=\"byopdf_rotateimage(\'' + fn + '\',270,' + imgwd + ',' + (lb_ht - 60) + ');\">';
   str += '</div>';
   str += '<div style=\"z-index:2;position:absolute;right:5px;top:5px;\">';
   str += '<img src=\"' + byopdf_domain + 'jsfimages/counterclockwise.jpg\" style=\"width:auto;height:25px;cursor:pointer;opacity:0.6;\" onclick=\"byopdf_rotateimage(\'' + fn + '\',90,' + imgwd + ',' + (lb_ht - 60) + ');\">';   
   str += '</div>';
   str += '<div id="byopdf_imagetorotate_txt" style=\"display:none;z-index:2;position:absolute;bottom:0px;left:0px;padding:10px;background-color:#FFFFFF;opacity:0.8;font-size:12px;color:#AA3333\">';
   str += 'Your image was modified successfully but may not be rotated in the public feed immediately.';   
   str += '</div>';
   str += '</div>';
   str += '</div>';
   
   var tawd = lb_wd - 60 - imgwd - 20;
   var taht = (lb_ht - 60 - 90);
   if(taht>150) taht = 150;
   str += '<div style=\"position:absolute;left:' + (imgwd + 10) + 'px;top:0px;width:' + tawd + 'px;\">';
   str += '<div style=\"font-size:12px;\">Comments</div>';
   str += '<textarea id=\"byopdf_activityupload\" style=\"width:' + (tawd-10) + 'px;height:' + taht + 'px;\">';
   if(Boolean(notes)) str += byopdf_convertback(notes);
   str += '</textarea>';
   var oc = 'byopdf_submituseractivity(' + activity + ',\'' + fn + '\',byopdf_convertstring(jQuery(\'#byopdf_activityupload\').val()),true);';
   if(Boolean(wd_row_id)) {
      oc = 'byopdf_updateuseractivity(\'byopdf_returnuseractivityupdate\',' + wd_row_id + ',\'\',byopdf_convertstring(jQuery(\'#byopdf_activityupload\').val()));';
   }
   str += '<div onclick=\"jQuery(\'#byopdf_lightbox\').fadeOut(300,\'swing\',function(){jQuery(\'#byopdf_lightbox\').html(\'\');});' + oc + '\" ';
   str += 'style=\"float:left;cursor:pointer;text-align:center;font-size:12px;margin:8px;padding:3px;border-radius:4px;border:1px solid #222222;width:50px;\">Confirm</div>';
   str += '<div onclick=\"jQuery(\'#byopdf_lightbox\').fadeOut(300,\'swing\',function(){jQuery(\'#byopdf_lightbox\').html(\'\');});\" ';
   str += 'style=\"float:left;cursor:pointer;text-align:center;font-size:12px;margin:8px;padding:3px;border-radius:4px;border:1px solid #222222;width:50px;\">Cancel</div>';

   str += '</div>';
   str += '</div>';

   str += '</div>';
   
   str += '</div>';   
   str += '</div>';
   
   jQuery('#byopdf_lightbox').html(str);
   jQuery('#byopdf_lightbox').fadeIn(400);
}

function byopdf_rotateimage(fn,degrees,wd,ht){
   jQuery('#byopdf_imagetorotate').html('');
   var url = '';
   url += '&degrees=' + degrees;
   url += '&fn=' + encodeURIComponent(fn);
   url += '&passthru1=' + encodeURIComponent(wd);
   url += '&passthru2=' + encodeURIComponent(ht);
   byopdf_QuickJSON('rotateimage','byopdf_returnrotateimage',url);
}

function byopdf_returnrotateimage(jsondata){
   byopdf_ReturnJSON(jsondata);
   //alert(JSON.stringify(jsondata));
   
   var str = '<img src=\"' + byopdf_replaceAll('http:','https:',jsondata.fn) + '?i=' + byopdf_getRandomInt(1,1000) + '\" style=\"max-width:' + jsondata.passthru1 + 'px;max-height:' + jsondata.passthru2 + 'px;width:auto;height:auto;\">';
   jQuery('#byopdf_imagetorotate').html(str);
   jQuery('#byopdf_imagetorotate_txt').fadeIn(400);
}


var byopdf_loading_count = 0;
function byopdf_showloading(){
   byopdf_loading_count++;
   jQuery('#byopdf_loading').show();
}

function byopdf_hideloading(){
   if(byopdf_loading_count>1) byopdf_loading_count--;
   else byopdf_loading_count=0;
   
   if(byopdf_loading_count==0) jQuery('#byopdf_loading').hide();
}



function byopdf_goback(){
   if(Boolean(byopdf_historycnt) && byopdf_historycnt>0) byopdf_historycnt--;
   else byopdf_historycnt=0;
   byopdf_displayFunct = byopdf_historyscr[(byopdf_historycnt-1)];
   byopdf_displayFunct();
}

function byopdf_gobackto(i){
   byopdf_historycnt=i+1;
   byopdf_displayFunct = byopdf_historyscr[i];
   byopdf_displayFunct();
}

function byopdf_gotoname(nm){
   jQuery('#byopdf_lightbox').fadeOut(200);  
   byopdf_displayFunct = window[nm];
   byopdf_displayFunct();
}













//var byopdf_curr_hash = '';
//var byopdf_hashing = false;
function byopdf_addhistory(func){
    byopdf_togglemenu(true);
   jQuery('#byopdf_lightbox').fadeOut(200);  
    window.scrollTo(0,0);

   if(!Boolean(byopdf_historycnt) || byopdf_historycnt<1) byopdf_historycnt=0;
   
   byopdf_displayFunct = func;
   if (byopdf_historycnt==0 || byopdf_displayFunct != byopdf_historyscr[(byopdf_historycnt-1)]) {
      //byopdf_hashing = true;
      byopdf_historyscr[byopdf_historycnt] = byopdf_displayFunct;
      //byopdf_curr_hash = '#' + func.name;
      //window.location.hash = byopdf_curr_hash;
      byopdf_historycnt++;
      //byopdf_hashing = false;
   }
}


function byopdf_display(t_funct){
   //alert('byopdf_display started');
   if(Boolean(t_funct)) byopdf_displayFunct = t_funct;
   else if(Boolean(byopdf_initpaths) && Boolean(byopdf_initpaths[2]) && !isNaN(byopdf_initpaths[2])) byopdf_displayFunct = byopdf_showpost;
   else if(!Boolean(byopdf_displayFunct)) byopdf_displayFunct = byopdf_showHomePage;
   byopdf_displayFunct();
}




function getLoadingHTML(){
   var str = '<div style=\"padding:30px;font-size:18px;' + byopdf_fontfamily + 'color:#bbbbbb;\">Loading...</div>';
   return str;
}





var byopdf_remember_pagename;
function byopdf_showPage(pagename) {
   if(!Boolean(pagename)) pagename = byopdf_remember_pagename;
   byopdf_remember_pagename = pagename;
   
   var fn_name = 'byopdf_show_' + byopdf_remember_pagename;
   //alert('function: ' + fn_name);
   if (typeof window[fn_name] === 'function') {
      var myFunc = window[fn_name];
      myFunc();
   } else {
      //jsfpb_getPage('MoreRecycling Pages',pagename,byopdf_winwidth,'byopdf_body');
   }
}





//--------------------------------------------------------
// These next two variables must be set in order to load an
// existing pdf with defaults
var byopdf_pdfname;
var byopdf_pdfdisplayname;
var byopdf_pdfsubtitle;
var byopdf_pdfuploaddisp;
var byopdf_pdfuploadhelp;
var byopdf_pdfuserid;
var byopdf_externalid;
var byopdf_wd_row_id;

var byopdf_pdftemplate;
var byopdf_pdfpositions;
var byopdf_pdfdata={};

var byopdf_imageurls = [];

var byopdf_prop;
var byopdf_changeswaiting;

function byopdf_showHomePage(){
//function byopdf_showHomePage(skipHistory){
   //if(!Boolean(skipHistory)) byopdf_addhistory(byopdf_showHomePage);
   //window.scrollTo(0,0);
   
   var t_gap = Math.floor((byopdf_winwidth - byopdf_globalwidth)/2);

   var leftside = Math.floor(byopdf_globalwidth/2);
   if(leftside>420) leftside = 420;
   else if(leftside<200) leftside = 200;
   
   //var rightside = byopdf_globalwidth - leftside - 10;
   var rightside = byopdf_globalwidth - leftside - 8;
   
   var str = '';
   str += '<div style=\"position:relative;width:' + byopdf_globalwidth + 'px;height:' + (byopdf_globalheight - byopdf_headerheight - byopdf_footerheight) + 'px;overflow:hidden;left:' + t_gap + 'px;\">';
   str += '<div id=\"byopdf_left\" style=\"float:left;width:' + leftside + 'px;height:' + (byopdf_globalheight - byopdf_headerheight - byopdf_footerheight) + 'px;overflow:hidden;\"></div>';
   str += '<div id=\"byopdf_right_outer\" style=\"float:left;width:' + rightside + 'px;height:' + (byopdf_globalheight - byopdf_headerheight - byopdf_footerheight) + 'px;overflow:hidden;\">';
   str += '<div id=\"byopdf_right\" style=\"position:relative;width:' + (rightside - 8) + 'px;height:' + (byopdf_globalheight - byopdf_headerheight - byopdf_footerheight - 8) + 'px;overflow:hidden;margin:2px;padding:2px;\"></div>';
   str += '</div>';
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';

   jQuery('#byopdf_body').html(str);
   
   if(!Boolean(byopdf_pdfname)) {
      byopdf_pdfname = window.localStorage.getItem('byopdf_pdfname');
   }
   byopdf_loadpdf();
}

function byopdf_loadpdf(pdfname){
   if(Boolean(pdfname)) byopdf_pdfname = pdfname;
   window.localStorage.setItem('byopdf_pdfname',byopdf_pdfname);
   
   var callback = 'byopdf_loadpdf_return';
   var wdname = 'BYO PDF Templates';
   var query = '';
   query += '&wdname=' + encodeURIComponent(wdname);
   query += '&cmsq_byopdftemplates_name=' + encodeURIComponent(byopdf_pdfname);
   byopdf_QuickJSON('getwdandrows',callback,query,true);   
   
}

function byopdf_loadpdf_return(jsondata){
   //alert('byopdf_loadpdf_return: ' + JSON.stringify(jsondata));
   byopdf_ReturnJSON(jsondata);
   
   if(Boolean(jsondata) && Boolean(jsondata.rows)) {
      byopdf_pdftemplate = jsondata.rows[0];
      byopdf_pdftemplate.wd_id = jsondata.wd_id;
      
      var wdname = 'BYO PDF Fields';
      var callback = 'byopdf_loadpdffields_return';
      var query = '';
      query += '&wdname=' + encodeURIComponent(wdname);
      if(Boolean(byopdf_pdfuserid)) query += '&foruserid=' + byopdf_pdfuserid;
      else if(Boolean(byopdf_externalid)) query += '&externalid=' + byopdf_externalid;
      byopdf_QuickJSON('getwdandrows',callback,query,false);      
   }   
}

function byopdf_loadpdffields_return(jsondata){
   //alert('byopdf_loadpdffields_return: ' + JSON.stringify(jsondata));
   byopdf_ReturnJSON(jsondata);
   byopdf_pdfdata = {};
   if(Boolean(jsondata) && Boolean(jsondata.rows) && jsondata.rows.length>0) {
      byopdf_pdfdata = jsondata.rows[0];
   }
   byopdf_pdfdata.wd_id = jsondata.wd_id;
   var callback = 'byopdf_loadpdfpos_return';
   var query = '';
   query += '&wd_id=' + jsondata.wd_id;
   query += '&groupname=' + encodeURIComponent(byopdf_pdftemplate.fieldpos);
   byopdf_QuickJSON('getfieldpositions',callback,query,true);   
}

function byopdf_loadpdfpos_return(jsondata){
   //alert('byopdf_loadpdfpos_return: ' + JSON.stringify(jsondata));
   byopdf_ReturnJSON(jsondata);
   byopdf_pdfpositions = jsondata.results;   

   // Finally, SHOW everything we just retreived from the database :)
   byopdf_changeswaiting = false;
   byopdf_displaypdfprogress();
   byopdf_displaypdfinput();
}

var byopdf_buttonbg1 = '#647520';
var byopdf_buttonbg2 = '#7d9031';
var byopdf_titlecolor = '#7e9137';
var byopdf_templatenamecolor = '#99ada5';

function byopdf_displaypdfprogress(){
   // RIGHT hand side of screen - actual pdf
   var ht = jQuery('#byopdf_right').height() - 18 - 28;
   var wd = jQuery('#byopdf_right').width() - 18;
   
   var orig_ht;
   var orig_wd;
   if(byopdf_pdftemplate.unit.toLowerCase()=='inches') {
      orig_ht = byopdf_conv_in_px(parseFloat(byopdf_pdftemplate.height));
      orig_wd = byopdf_conv_in_px(parseFloat(byopdf_pdftemplate.width));
   } else if(byopdf_pdftemplate.unit.toLowerCase()=='points') {
      orig_ht = byopdf_conv_pt_px(parseFloat(byopdf_pdftemplate.height));
      orig_wd = byopdf_conv_pt_px(parseFloat(byopdf_pdftemplate.width));
   } else {
      orig_ht = parseInt(byopdf_pdftemplate.height);
      orig_wd = parseInt(byopdf_pdftemplate.width);
   }
   
   // determine the scale/proportion of the display
   var prop = wd / orig_wd;
   if((orig_ht * prop) > ht) prop = ht / orig_ht;
   // save the proportion globally
   byopdf_prop = prop;
   
   var pic_wd = Math.floor(prop * orig_wd) - 2;
   var pic_ht = Math.floor(prop * orig_ht) - 2;
   
   var js = '';
   var str = '';
   var groups = {};
   str += '<div style=\"position:relative;left:5px;top:5px;width:' + wd + 'px;overflow-x:hidden;\">';
   str += '<div style=\"position:relative;width:' + wd + 'px;height:' + ht + 'px;overflow-y:auto;overflow-x:hidden;\">';
  
   var img = byopdf_pdftemplate.thumbnail;
   if(!Boolean(img)) img = byopdf_pdftemplate.hiresimg;
   str += '<img src=\"' + byopdf_replaceAll('http:','https:',img) + '\" style=\"width:' + pic_wd + 'px;height:' + pic_ht + 'px;border:1px solid #787878;border-radius:2px;\">';
   for(var i=0;i<byopdf_pdfpositions.length;i++) {
      if(!Boolean(byopdf_pdfdata)) byopdf_pdfdata = {};
      if(!Boolean(byopdf_pdfdata[byopdf_pdfpositions[i].field_id])) byopdf_pdfdata[byopdf_pdfpositions[i].field_id] = '';
      
      var temp_txt = '';
      if(Boolean(byopdf_pdfdata[byopdf_pdfpositions[i].field_id])) temp_txt = byopdf_pdfdata[byopdf_pdfpositions[i].field_id];
      if(!Boolean(temp_txt) && Boolean(byopdf_pdfpositions[i].defval)) byopdf_pdfdata[byopdf_pdfpositions[i].field_id] = byopdf_pdfpositions[i].defval;
      
      var tparams = byopdf_explodequery(byopdf_pdfpositions[i].params);
      var tlf;
      var ttp;
      var twd;
      var tht;
      var tfsz;
      var tgap=0;
      if(byopdf_pdfpositions[i].unit=='in') {
         tlf = byopdf_conv_in_px(prop * parseFloat(byopdf_pdfpositions[i].leftpos));
         ttp = byopdf_conv_in_px(prop * parseFloat(byopdf_pdfpositions[i].toppos));
         twd = byopdf_conv_in_px(prop * parseFloat(byopdf_pdfpositions[i].width));
         tht = byopdf_conv_in_px(prop * parseFloat(byopdf_pdfpositions[i].height));
         if(Boolean(tparams.fontsize)) tfsz = byopdf_conv_in_px(prop * parseFloat(tparams.fontsize));
         if(Boolean(tparams.gap)) tgap = byopdf_conv_in_px(prop * parseFloat(tparams.gap));
      } else if(byopdf_pdfpositions[i].unit=='pt') {
         tlf = byopdf_conv_pt_px(prop * parseFloat(byopdf_pdfpositions[i].leftpos));
         ttp = byopdf_conv_pt_px(prop * parseFloat(byopdf_pdfpositions[i].toppos));
         twd = byopdf_conv_pt_px(prop * parseFloat(byopdf_pdfpositions[i].width));
         tht = byopdf_conv_pt_px(prop * parseFloat(byopdf_pdfpositions[i].height));
         if(Boolean(tparams.fontsize)) tfsz = byopdf_conv_pt_px(prop * parseFloat(tparams.fontsize));
         if(Boolean(tparams.gap)) tgap = byopdf_conv_pt_px(prop * parseFloat(tparams.gap));
      } else {
         tlf = Math.round(prop * parseInt(byopdf_pdfpositions[i].leftpos));
         ttp = Math.round(prop * parseInt(byopdf_pdfpositions[i].toppos));
         twd = Math.round(prop * parseInt(byopdf_pdfpositions[i].width));
         tht = Math.round(prop * parseInt(byopdf_pdfpositions[i].height));
         if(Boolean(tparams.fontsize)) tfsz = Math.round(prop * parseInt(tparams.fontsize));
         if(Boolean(tparams.gap)) tgap = Math.round(prop * parseInt(tparams.gap));
      }
      
      //alert('field: ' + byopdf_pdfpositions[i].field_id + ' type: ' + byopdf_pdfpositions[i].disptype);
      
      if(byopdf_pdfpositions[i].disptype=='TEXT' || byopdf_pdfpositions[i].disptype=='DROPDOWN'){
         // display data on template
         str += '<div id=\"thumb_' + byopdf_pdfpositions[i].field_id + '\" style=\"position:absolute;left:' + tlf + 'px;top:' + ttp + 'px;width:' + twd + 'px;height:' + tht + 'px;overflow:hidden;z-index:' + (i + 5) + ';';
         if(Boolean(tfsz)) str += 'font-size:' + tfsz + 'px;';
         if(Boolean(tparams.color)) str += 'color:' + tparams.color + ';';
         if(Boolean(tparams.backgroundcolor)) str += 'background-color:' + tparams.backgroundcolor + ';';
         if(Boolean(tparams.textalign)) str += 'text-align:' + tparams.textalign + ';';
         if(Boolean(tparams.fontweight)) str += 'font-weight:' + tparams.fontweight + ';';
         str += '\">';
         var contentstr = byopdf_pdfdata[byopdf_pdfpositions[i].field_id];
         if(!Boolean(contentstr) && (!Boolean(tparams.noedit) || tparams.noedit=='false')) contentstr = byopdf_pdfpositions[i].label;
         str += byopdf_convertdisplay(contentstr);
         str += '</div>';
         
         // button to start editing
         if((!Boolean(tparams.noedit) || tparams.noedit=='false') && byopdf_pdfpositions[i].disptype=='TEXT') {
            str += '<div onclick=\"byopdf_editfield(\'' + byopdf_pdfpositions[i].field_id + '\');\" id=\"edit_' + byopdf_pdfpositions[i].field_id + '\" style=\"position:absolute;left:' + (tlf + twd - 14) + 'px;top:' + (ttp + 2) + 'px;z-index:' + (i + 6) + ';cursor:pointer;opacity:0.8;\">';
            str += '<img src=\"' + byopdf_domain + 'images/pencil_edit.png\" style=\"width:12px;height:12px;\"></div>';
            
            // button to finish editing
            str += '<div onclick=\"byopdf_donefield(\'' + byopdf_pdfpositions[i].field_id + '\');\" id=\"done_' + byopdf_pdfpositions[i].field_id + '\" style=\"position:absolute;left:' + (tlf + twd - 8) + 'px;top:' + (ttp + 2) + 'px;font-size:10px;z-index:' + (i + 11) + ';cursor:pointer;color:#EEEEEE;background-color:#333333;padding:1px;border-radius:3px;opacity:0.8;display:none;\">';
            str += 'X</div>';
            
            // Editable field display
            var taht = tht;
            if(taht<32) taht=32;
            str += '<div id=\"thumbinput_' + byopdf_pdfpositions[i].field_id + '\" ';
            str += 'style=\"position:absolute;left:' + tlf + 'px;top:' + (ttp + 2) + 'px;width:' + (twd - 3) + 'px;height:' + (taht - 3) + 'px;overflow:hidden;z-index:' + (i + 10) + ';display:none;\" ';
            str += '>';
            str += '<textarea onkeyup=\"byopdf_updatetextfield(\'' + byopdf_pdfpositions[i].field_id + '\');\" id=\"input_' + byopdf_pdfpositions[i].field_id + '\" style=\"font-size:10px;width:' + (twd - 5) + 'px;height:' + (taht - 5) + 'px;\">';
            var data = '';
            if(Boolean(byopdf_pdfdata)) data = byopdf_pdfdata[byopdf_pdfpositions[i].field_id];
            if(Boolean(data) && data!='%%%EMPTY%%%' && data!='%E%') str += byopdf_convertback(data);
            str += '</textarea>';
            str += '</div>';
            
            js += 'temp = jQuery(\'#input_' + byopdf_pdfpositions[i].field_id + '\').val();\n';
            js += 'if(!Boolean(temp)) temp = \'%E%\';\n';
            js += 'url += \'&' + byopdf_pdfpositions[i].field_id + '=\' + encodeURIComponent(byopdf_convertstring(temp));\n';
            
         } else if ((!Boolean(tparams.noedit) || tparams.noedit=='false') && byopdf_pdfpositions[i].disptype=='DROPDOWN') {
            //alert('here');
            str += '<div onclick=\"byopdf_editfield(\'' + byopdf_pdfpositions[i].field_id + '\');\" id=\"edit_' + byopdf_pdfpositions[i].field_id + '\" style=\"position:absolute;left:' + (tlf + twd - 14) + 'px;top:' + (ttp + 2) + 'px;z-index:' + (i + 6) + ';cursor:pointer;opacity:0.8;\">';
            str += '<img src=\"' + byopdf_domain + 'images/pencil_edit.png\" style=\"width:12px;height:12px;\"></div>';
            
            // button to finish editing
            str += '<div onclick=\"byopdf_donefield(\'' + byopdf_pdfpositions[i].field_id + '\');\" id=\"done_' + byopdf_pdfpositions[i].field_id + '\" style=\"position:absolute;left:' + (tlf + twd - 8) + 'px;top:' + (ttp + 2) + 'px;font-size:10px;z-index:' + (i + 11) + ';cursor:pointer;color:#EEEEEE;background-color:#333333;padding:1px;border-radius:3px;opacity:0.8;display:none;\">';
            str += 'X</div>';
            
            var ans = byopdf_pdfdata[byopdf_pdfpositions[i].field_id].toLowerCase();
            
            // Editable field display
            var taht = tht;
            if(taht<48) taht=48;

            str += '<div id=\"thumbinput_' + byopdf_pdfpositions[i].field_id + '\" ';
            str += 'style=\"position:absolute;left:' + tlf + 'px;top:' + ttp + 'px;z-index:' + (i + 10) + ';padding:3px;background-color:#FFFFFF;min-width:' + (twd - 6) + 'px;min-height:' + (taht) + 'px;display:none;\" ';
            str += '>';
            
            str += '<select onchange=\"byopdf_updatetextfield(\'' + byopdf_pdfpositions[i].field_id + '\');byopdf_donefield(\'' + byopdf_pdfpositions[i].field_id + '\');\" id=\"input_' + byopdf_pdfpositions[i].field_id + '\" style=\"margin-top:22px;font-size:10px;\">';
            str += '<option value=\"\"></option>';
            for(var j=0;j<10;j++) {
               if(Boolean(tparams['option' + j])) {
                  str += '<option value=\"' + tparams['option' + j] + '\"';
                  if(ans==tparams['option' + j].toLowerCase()) str += ' SELECTED';
                  str += '>' + tparams['option' + j] + '</option>';
               }
            }
            str += '</select>';
            str += '</div>'; 
            str += '</div>';
            
            js += 'temp = jQuery(\'#input_' + byopdf_pdfpositions[i].field_id + '\').val();\n';
            js += 'if(!Boolean(temp)) temp = \'%E%\';\n';
            js += 'url += \'&' + byopdf_pdfpositions[i].field_id + '=\' + encodeURIComponent(byopdf_convertstring(temp));\n';
         }
         

      } else if(byopdf_pdfpositions[i].disptype=='TEXTBLOCK'){
         // display data on template
         var grpnm = tparams.group;
         if(!Boolean(groups[grpnm])) {
            groups[grpnm] = {};
            groups[grpnm].zindex = i + 5;
            groups[grpnm].left = tlf;
            groups[grpnm].top = ttp;
            groups[grpnm].width = twd;
            groups[grpnm].height = tht;
            groups[grpnm].fsz = tfsz;
            groups[grpnm].color = tparams.color;
            groups[grpnm].bgcolor = tparams.backgroundcolor;
            groups[grpnm].align = tparams.textalign;
            groups[grpnm].fwt = tparams.fontweight;
            groups[grpnm].html = '';
         }
         groups[grpnm].html += '<div id=\"thumb_' + byopdf_pdfpositions[i].field_id + '\" style=\"';
         if(Boolean(tgap)) groups[grpnm].html += 'margin-top:' + tgap + 'px;';
         if(Boolean(tfsz)) groups[grpnm].html += 'font-size:' + tfsz + 'px;';
         if(Boolean(tparams.color)) groups[grpnm].html += 'color:' + tparams.color + ';';
         if(Boolean(tparams.backgroundcolor)) groups[grpnm].html += 'background-color:' + tparams.backgroundcolor + ';';
         if(Boolean(tparams.textalign)) groups[grpnm].html += 'text-align:' + tparams.textalign + ';';
         if(Boolean(tparams.fontweight)) groups[grpnm].html += 'font-weight:' + tparams.fontweight + ';';
         groups[grpnm].html += '\">';
         var contentstr = byopdf_pdfdata[byopdf_pdfpositions[i].field_id];
         if(Boolean(contentstr)) groups[grpnm].html += byopdf_convertdisplay(contentstr);
         groups[grpnm].html += '</div>';

         // Input fields are on the left hand side, but still include javascript for it
         js += 'temp = jQuery(\'#input_' + byopdf_pdfpositions[i].field_id + '\').val();\n';
         js += 'if(!Boolean(temp)) temp = \'%E%\';\n';
         js += 'url += \'&' + byopdf_pdfpositions[i].field_id + '=\' + encodeURIComponent(byopdf_convertstring(temp));\n';
      } else if(byopdf_pdfpositions[i].disptype=='DROPDOWNEXT'){
         // Input fields are on the left hand side, but still include javascript for it
         str += '<div id=\"thumb_' + byopdf_pdfpositions[i].field_id + '\" style=\"position:absolute;left:' + tlf + 'px;top:' + ttp + 'px;width:' + twd + 'px;height:' + tht + 'px;overflow:hidden;z-index:' + (i + 5) + ';';
         if(Boolean(tfsz)) str += 'font-size:' + tfsz + 'px;';
         if(Boolean(tparams.color)) str += 'color:' + tparams.color + ';';
         if(Boolean(tparams.backgroundcolor)) str += 'background-color:' + tparams.backgroundcolor + ';';
         if(Boolean(tparams.textalign)) str += 'text-align:' + tparams.textalign + ';';
         if(Boolean(tparams.fontweight)) str += 'font-weight:' + tparams.fontweight + ';';
         str += '\">';
         var contentstr = byopdf_pdfdata[byopdf_pdfpositions[i].field_id];
         if(!Boolean(contentstr) && (!Boolean(tparams.noedit) || tparams.noedit=='false')) contentstr = byopdf_pdfpositions[i].label;
         str += byopdf_convertdisplay(contentstr);
         str += '</div>';
         js += 'temp = jQuery(\'#input_' + byopdf_pdfpositions[i].field_id + '\').val();\n';
         js += 'if(!Boolean(temp)) temp = \'%E%\';\n';
         js += 'url += \'&' + byopdf_pdfpositions[i].field_id + '=\' + encodeURIComponent(byopdf_convertstring(temp));\n';
      } else if (byopdf_pdfpositions[i].disptype=='CHECKBOX') {
         var input_wd = twd - 3;
         var input_ht = taht - 3;
         if(input_ht<300) input_ht = 300;
         if(input_wd<200) input_wd = 200;
         
         byopdf_customoptions[byopdf_pdfpositions[i].field_id] = [];
         
         // display data on template
         str += '<div id=\"thumb_' + byopdf_pdfpositions[i].field_id + '\" style=\"position:absolute;left:' + tlf + 'px;top:' + ttp + 'px;width:' + twd + 'px;height:' + tht + 'px;overflow:hidden;z-index:' + (i + 5) + ';';
         if(Boolean(tfsz)) str += 'font-size:' + tfsz + 'px;';
         if(Boolean(tparams.color)) str += 'color:' + tparams.color + ';';
         if(Boolean(tparams.backgroundcolor)) str += 'background-color:' + tparams.backgroundcolor + ';';
         if(Boolean(tparams.textalign)) str += 'text-align:' + tparams.textalign + ';';
         if(Boolean(tparams.fontweight)) str += 'font-weight:' + tparams.fontweight + ';';
         str += '\">';
         str += '</div>';         
         
         str += '<div onclick=\"byopdf_editfield(\'' + byopdf_pdfpositions[i].field_id + '\');\" id=\"edit_' + byopdf_pdfpositions[i].field_id + '\" style=\"position:absolute;left:' + (tlf + twd - 14) + 'px;top:' + (ttp + 2) + 'px;z-index:' + (i + 6) + ';cursor:pointer;opacity:0.8;\">';
         str += '<img src=\"' + byopdf_domain + 'images/pencil_edit.png\" style=\"width:12px;height:12px;\"></div>';
         
         // button to finish editing
         str += '<div onclick=\"byopdf_donefield(\'' + byopdf_pdfpositions[i].field_id + '\');\" id=\"done_' + byopdf_pdfpositions[i].field_id + '\" style=\"position:absolute;left:' + (tlf + twd - 8) + 'px;top:' + (ttp + 2) + 'px;font-size:10px;z-index:' + (i + 11) + ';cursor:pointer;color:#EEEEEE;background-color:#333333;padding:1px;border-radius:3px;opacity:0.8;display:none;\">';
         str += 'X</div>';
         
         var taht = tht;
         if(taht<32) taht=32;
         str += '<div id=\"thumbinput_' + byopdf_pdfpositions[i].field_id + '\" ';
         //str += 'style=\"position:absolute;left:' + tlf + 'px;top:' + (ttp + 2) + 'px;width:' + (twd - 3) + 'px;height:' + (taht - 3) + 'px;overflow-x:hidden;overflow-y:auto;z-index:' + (i + 10) + ';background-color:#FFFFFF;display:none;\" ';
         str += 'style=\"position:absolute;left:' + tlf + 'px;top:' + (ttp + 2) + 'px;width:' + input_wd + 'px;height:' + input_ht + 'px;overflow-x:hidden;overflow-y:auto;z-index:' + (i + 10) + ';background-color:#FFFFFF;display:none;\" ';
         str += '>';
         //alert('ans: ' + byopdf_pdfdata[byopdf_pdfpositions[i].field_id]);
         var ans = byopdf_convertback(byopdf_pdfdata[byopdf_pdfpositions[i].field_id]).split(',');
         //alert('ans: ' + JSON.stringify(ans));
         for(var j=0;j<20;j++) {
            if(Boolean(tparams['option' + j])) {
               str += '<div style=\"margin-top:1px;\">';
               str += '<input type=\"checkbox\" id=\"cb_' + byopdf_pdfpositions[i].field_id + '_' + j + '\" onclick=\"byopdf_changelist(' + i + ',1);\" value=\"' + tparams['option' + j] + '\" style=\"font-size:12px;margin:0px 5px 0px 0px;padding:0px;\"';
               for(var k=0;k<ans.length;k++) {
                  //alert('checking: ' + ans[k].trim().toLowerCase());
                  if(ans[k].trim().toLowerCase()==tparams['option' + j].trim().toLowerCase()) {
                     str += ' CHECKED';
                     break;
                  }
               }
               str += '> ' + tparams['option' + j] + '</div>';
            }
         }
         
         // custom fields
         for(var k=0;k<ans.length;k++) {
            var tfound = false;
            for(var j=0;j<20;j++) {
               if(Boolean(tparams['option' + j])) {
                  if(ans[k].trim().toLowerCase()==tparams['option' + j].trim().toLowerCase()) {
                     tfound = true;
                     break;
                  }
               }
            }
            if(!tfound) byopdf_customoptions[byopdf_pdfpositions[i].field_id].push(ans[k]);
         }
         str += '<div id=\"customlist_' + byopdf_pdfpositions[i].field_id + '\"></div>';
         str += '<div>';
         str += '<input type=\"text\" id=\"custom_' + byopdf_pdfpositions[i].field_id + '\" style=\"font-size:10px;\"> ';
         str += '<span onclick=\"byopdf_addcustomoption(' + i + ',jQuery(\'#custom_' + byopdf_pdfpositions[i].field_id + '\').val());jQuery(\'#custom_' + byopdf_pdfpositions[i].field_id + '\').val(\'\');\" style=\"font-size:10px;color:red;cursor:pointer;\">add item</span>';
         str += '</div>';
         
         str += '</div>';
         str += '\n<script>\n';
         str += 'byopdf_changelist(' + i + ');\n';
         str += '</script>\n';
         
         
         js += 'temp = byopdf_pdfdata[\'' + byopdf_pdfpositions[i].field_id + '\'];\n';
         js += 'if(!Boolean(temp)) temp = \'%E%\';\n';
         js += 'url += \'&' + byopdf_pdfpositions[i].field_id + '=\' + encodeURIComponent(temp);\n';
         
         
      } else if (byopdf_pdfpositions[i].disptype=='CHECKBOXLEFT') {
         var input_wd = twd - 3;
         var input_ht = taht - 3;
         if(input_ht<300) input_ht = 300;
         if(input_wd<200) input_wd = 200;
         
         // display data on template
         str += '<div id=\"thumb_' + byopdf_pdfpositions[i].field_id + '\" style=\"position:absolute;left:' + tlf + 'px;top:' + ttp + 'px;width:' + twd + 'px;height:' + tht + 'px;overflow:hidden;z-index:' + (i + 5) + ';';
         if(Boolean(tfsz)) str += 'font-size:' + tfsz + 'px;';
         if(Boolean(tparams.color)) str += 'color:' + tparams.color + ';';
         if(Boolean(tparams.backgroundcolor)) str += 'background-color:' + tparams.backgroundcolor + ';';
         if(Boolean(tparams.textalign)) str += 'text-align:' + tparams.textalign + ';';
         if(Boolean(tparams.fontweight)) str += 'font-weight:' + tparams.fontweight + ';';
         str += '\">';
         str += '</div>';         
         
         
         
         js += 'temp = byopdf_pdfdata[\'' + byopdf_pdfpositions[i].field_id + '\'];\n';
         js += 'if(!Boolean(temp)) temp = \'%E%\';\n';
         js += 'url += \'&' + byopdf_pdfpositions[i].field_id + '=\' + encodeURIComponent(temp);\n';
         
         
      } else if(byopdf_pdfpositions[i].disptype=='GRID'){
         // display data on template
         if(!Boolean(tparams.noedit) || tparams.noedit=='false') {
            str += '<div ondrop=\"byopdf_drop(event);\" ondragover=\"byopdf_allowdrop(event);\" class=\"byocontainer\" ';
            str += 'id=\"thumb_' + byopdf_pdfpositions[i].field_id + '\" ';
            str += 'style=\"position:absolute;left:' + tlf + 'px;top:' + ttp + 'px;width:' + twd + 'px;height:' + tht + 'px;overflow:hidden;z-index:' + (i + 7) + ';\" ';
            if(!Boolean(tparams.cols)) tparams.cols = 1;
            str += 'data-cols=\"' + tparams.cols + '\" ';
            var cellwd = Math.floor(parseInt(twd) / parseInt(tparams.cols));
            str += 'data-cellwd=\"' + cellwd + '\" ';
            if(!Boolean(tparams.rows)) tparams.rows = 1;
            str += 'data-rows=\"' + tparams.rows + '\" ';
            var cellht = Math.floor(parseInt(tht) / parseInt(tparams.rows));
            str += 'data-cellht=\"' + cellht + '\" ';
            if(!Boolean(tparams.addpadding)) tparams.addpadding = '0';
            str += 'data-addpadding=\"' + tparams.addpadding + '\" ';
            if(!Boolean(tparams.showcaption)) tparams.showcaption = '0';
            str += 'data-showcaption=\"' + tparams.showcaption + '\" ';
            if(Boolean(tparams.color)) str += 'data-fontcolor=\"' + tparams.color + '\" ';
            str += '>';
            //str += byopdf_pdfdata[byopdf_pdfpositions[i].field_id];
            str += '</div>';
            str += '<div id=\"thumbbg_' + byopdf_pdfpositions[i].field_id + '\" style=\"position:absolute;left:' + tlf + 'px;top:' + ttp + 'px;width:' + twd + 'px;height:' + tht + 'px;overflow:hidden;z-index:' + (i + 5) + ';color:#222222;font-size:10px;';
            if(Boolean(tparams.backgroundcolor)) str += 'background-color:' + tparams.backgroundcolor + ';';
            else str += 'background-color:#A4AAAF;opacity:0.4;';
            str += '\"><span style=\"margin:4px;\">' + byopdf_pdfpositions[i].label + ' Drop Area</span></div>';
            if(!Boolean(byopdf_dropimgs)) byopdf_dropimgs = {};
            if(!Boolean(byopdf_dropimgs['thumb_' + byopdf_pdfpositions[i].field_id])) byopdf_dropimgs['thumb_' + byopdf_pdfpositions[i].field_id] = [];
            for(var j=0;j<parseInt(tparams.cols);j++){
               for(var k=0;k<parseInt(tparams.rows);k++) {
                  byopdf_dropimgs['thumb_' + byopdf_pdfpositions[i].field_id][(j + (k*tparams.rows))] = false;
                  str += '<div style=\"position:absolute;left:' + (tlf + (j * cellwd)) + 'px;top:' + (ttp + (k * cellht)) + 'px;z-index:' + (6 + i) + ';width:' + (cellwd - 2) + 'px;height:' + (cellht - 2) + 'px;overflow:hidden;border:1px dashed #3F3F3F;border-radius:3px;\"></div>';
               }
            }
         }
         
         var ans = byopdf_pdfdata[byopdf_pdfpositions[i].field_id];
         ans_imgs = ans.split(';');
         for(var j=0;j<ans_imgs.length;j++) {
            if(Boolean(ans_imgs[j])) {
               var temp = {};
               ans_par = ans_imgs[j].split(',');
               var ndx = ans_par[0];            
               temp.image = ans_par[1];
               temp.size = ans_par[2];
               temp.caption = ans_par[3];
               byopdf_dropimgs['thumb_' + byopdf_pdfpositions[i].field_id][ndx] = temp;
            }
         }
         str += '\n<script>\n';
         str += 'byopdf_fillgrid(\'thumb_' + byopdf_pdfpositions[i].field_id + '\');\n';
         str += '\n</script>\n';
         
         js += 'if(Boolean(byopdf_dropimgs[\'thumb_' + byopdf_pdfpositions[i].field_id + '\'])){\n';
         js += '  temp = \'\';\n';
         js += '  for(var j=0;j<byopdf_dropimgs[\'thumb_' + byopdf_pdfpositions[i].field_id + '\'].length;j++) {\n';
         js += '  if(Boolean(byopdf_dropimgs[\'thumb_' + byopdf_pdfpositions[i].field_id + '\'][j])) {\n';
         js += '    temp += j + \',\' + byopdf_dropimgs[\'thumb_' + byopdf_pdfpositions[i].field_id + '\'][j].image + \',\' + byopdf_dropimgs[\'thumb_' + byopdf_pdfpositions[i].field_id + '\'][j].size + \',\' + byopdf_dropimgs[\'thumb_' + byopdf_pdfpositions[i].field_id + '\'][j].caption + \';\';\n';
         js += '  }\n';
         js += '  }\n';
         js += '  if(!Boolean(temp)) temp=\'0,%E%\';\n';
         js += '  url += \'&' + byopdf_pdfpositions[i].field_id + '=\' + encodeURIComponent(temp);\n';
         js += '} else {\n';
         js += '  url += \'&' + byopdf_pdfpositions[i].field_id + '=\' + encodeURIComponent(\'%E%\');\n';
         js += '}\n';
         
      }      
   }
   
   // Go back through and put groups in a paragraph form
   for (var key in groups) {
      if (groups.hasOwnProperty(key)) {
         str += '<div id=\"thumbgroup_' + key + '\" ';
         str += 'style=\"position:absolute;left:' + groups[key].left + 'px;top:' + groups[key].top + 'px;width:' + groups[key].width + 'px;height:' + groups[key].height + 'px;overflow:hidden;z-index:' + groups[key].zindex + ';';
         if(Boolean(groups[key].fsz)) str += 'font-size:' + groups[key].fsz + 'px;';
         if(Boolean(groups[key].color)) str += 'color:' + groups[key].color + ';';
         if(Boolean(groups[key].bgcolor)) str += 'background-color:' + groups[key].bgcolor + ';';
         if(Boolean(groups[key].align)) str += 'text-align:' + groups[key].align + ';';
         if(Boolean(groups[key].fwt)) str += 'font-weight:' + groups[key].fwt + ';';
         str += '\">';
         str += groups[key].html;
         str += '</div>';
      }
   }
   
   str += '</div>';
   
   str += '<div style=\"margin-left:10px;margin-top:5px;\">';
   str += '<div onclick=\"byopdf_submitchanges(false);\" ';
   str += 'style=\"float:left;margin:2px 0px 0px 5px;width:110px;font-size:12px;padding:6px;text-align:center;color:#FFFFFF;background-color:' + byopdf_buttonbg1 + ';border-radius:3px;cursor:pointer;\" ';
   str += 'onmouseover=\"jQuery(\'#byopdf_savebutton2\').css(\'background-color\',byopdf_buttonbg2);\" ';
   str += 'onmouseout=\"jQuery(\'#byopdf_savebutton2\').css(\'background-color\',byopdf_buttonbg1);\" ';
   str += 'id=\"byopdf_savebutton2\" ';
   str += 'class=\"byopdf_savebutton\" ';
   str += '>Save</div>';
   str += '<div onclick=\"byopdf_submitchanges(true);\" ';
   str += 'style=\"float:left;margin:2px 0px 0px 5px;width:110px;font-size:12px;padding:6px;text-align:center;color:#FFFFFF;background-color:' + byopdf_buttonbg1 + ';border-radius:3px;cursor:pointer;\" ';
   str += 'onmouseover=\"jQuery(\'#byopdf_getpdf2\').css(\'background-color\',byopdf_buttonbg2);\" ';
   str += 'onmouseout=\"jQuery(\'#byopdf_getpdf2\').css(\'background-color\',byopdf_buttonbg1);\" ';
   str += 'id=\"byopdf_getpdf2\" ';
   str += 'class=\"byopdf_getpdf\" ';
   str += '>Save & Review</div>';
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';
   
   
   str += '</div>';
   str += '\n<script>\n';
   str += 'function byopdf_submitchanges(showpdf) {\n';
   str += '   byopdf_showpdfaftersave = false;\n';
   str += '   if(Boolean(showpdf)) byopdf_showpdfaftersave = true;\n';
   str += '   var url = \'\';\n';
   str += '   url += \'&wd_id=\' + byopdf_pdfdata.wd_id;\n';
   str += '   url += \'&ignorenull=1\';\n';
   str += '   if(Boolean(byopdf_pdfdata.wd_row_id)) url += \'&wd_row_id=\' + byopdf_pdfdata.wd_row_id;\n';
   str += '   if(Boolean(byopdf_pdfuserid)) url += \'&userid=\' + byopdf_pdfuserid;\n';
   str += '   if(Boolean(byopdf_externalid)) url += \'&externalid=\' + byopdf_externalid;\n';
   str += js;
   //str += '   alert(\'URL: \' + url);\n';
   str += '   byopdf_QuickJSON(\'submitwebdata\',\'byopdf_submitchanges_return\',url,false);\n';
   str += '}\n';
   str += '\n</script>\n';
   jQuery('#byopdf_right').html(str);
   
}

var byopdf_showpdfaftersave = false;
var byopdf_showpdffunction;
var byopdf_showpdfurl;

function byopdf_submitchanges_return(jsondata) {
   byopdf_ReturnJSON(jsondata);
   if(jsondata.responsecode==1) {
      byopdf_pdfdata.wd_row_id = jsondata.wd_row_id;
      byopdf_changeswaiting = false;
      if(byopdf_showpdfaftersave) byopdf_getpdf();
      else alert('Your changes were saved');
   } else {
      alert(jsondata.error);
   }
}

function byopdf_getpdf(){
   if(Boolean(byopdf_pdfdata) && Boolean(byopdf_pdftemplate)) {
      var url = byopdf_domain + 'byopdf/';
      url += byopdf_pdfdata.wd_id + '/';
      url += byopdf_pdftemplate.wd_id + '/';
      url += byopdf_pdfdata.wd_row_id + '/';
      url += byopdf_pdftemplate.wd_row_id;
      url += '.pdf';
      byopdf_showpdfurl = url;
      if(Boolean(byopdf_showpdffunction)) byopdf_showpdffunction(byopdf_showpdfurl);
      else window.open(byopdf_showpdfurl);
   } else {
      alert('Please save your PDF before attempting to create the PDF.');
   }
}


var byopdf_customoptions = {};
function byopdf_removecustomoption(indx,i) {
   var field_id = byopdf_pdfpositions[indx].field_id;
   if(Boolean(byopdf_customoptions[field_id])) {
      if(i<byopdf_customoptions[field_id].length) {
         //alert('indx: ' + indx + ' field_id: ' + field_id + ' i: ' + i);
         byopdf_customoptions[field_id].splice(i,1);
      }
   }
   byopdf_changelist(indx,1);
}

function byopdf_addcustomoption(indx,str) {
   var field_id = byopdf_pdfpositions[indx].field_id;
   if(!Boolean(byopdf_customoptions[field_id])) {
      byopdf_customoptions[field_id] = [];
   }
   byopdf_customoptions[field_id].push(str);
   byopdf_changelist(indx,1);
}


function byopdf_changelist(indx,setchange) {
   var field_id = byopdf_pdfpositions[indx].field_id;
   var tparams = byopdf_explodequery(byopdf_pdfpositions[indx].params);
   var str = '';
   var html = '';
   var totalitems = 0;
   for(var j=0;j<20;j++) {
      var id = 'cb_' + field_id + '_' + j;
      if(jQuery('#' + id).length == 1 && document.getElementById(id).checked) {
         if(Boolean(str) && str.length > 0) str += ',';
         str += byopdf_convertstring(jQuery('#' + id).val().trim());
         if(!Boolean(tparams.inline)) html += '<div style=\"margin-top:1px;\">';
         else html += '&nbsp; ';
         if(totalitems>0 || !Boolean(tparams.inline)) html += '&bull; &nbsp;';
         html += jQuery('#' + id).val();
         if(!Boolean(tparams.inline)) html += '</div>';
         totalitems++;
      }
   }
   if(totalitems>0 && Boolean(tparams.prefix)) html = tparams.prefix + html;
   
   var showlist = '';
   if(Boolean(byopdf_customoptions[field_id])) {
      for(var j=0;j<byopdf_customoptions[field_id].length;j++) {
         if(Boolean(str) && str.length > 0) str += ',';
         var temp_str = byopdf_convertstring(byopdf_customoptions[field_id][j].trim());
         str += temp_str;
         if(!Boolean(tparams.inline)) html += '<div style=\"margin-top:1px;\">';
         else html += '&nbsp; ';
         html += '&bull; &nbsp;';
         html += temp_str;
         if(!Boolean(tparams.inline)) html += '</div>';
         showlist += '<div style=\"font-size:10px;color:#222222;\">' + temp_str + ' ';
         showlist += '<span onclick=\"byopdf_removecustomoption(' + indx + ',' + j + ');\" style=\"margin-left:10px;font-size:10px;cursor:pointer;color:red;\">remove</span>';
         showlist += '</div>';
      }
   }
   byopdf_pdfdata[byopdf_pdfpositions[indx].field_id] = str;
   if(!Boolean(html)) html = byopdf_pdfpositions[indx].label;
   jQuery('#thumb_' + byopdf_pdfpositions[indx].field_id).html(html);
   jQuery('#customlist_' + byopdf_pdfpositions[indx].field_id).html(showlist);
   if(Boolean(setchange)) byopdf_changeinput();
}

function byopdf_displaypdfinput() {
   // LEFT hand side of screen - all the inputs
   var ht = jQuery('#byopdf_left').height() - 10;
   var wd = jQuery('#byopdf_left').width() - 10;
   
   var str = '';
   str += '<div style=\"position:relative;left:5px;top:5px;width:' + wd + 'px;height:' + ht + 'px;overflow-y:auto;overflow-x:hidden;\">';
   str += '<div style=\"position:relative;font-size:18px;font-weight:bold;color:' + byopdf_titlecolor + ';margin-bottom:8px;\">';
   str += 'Customize';
   str += '</div>';
   str += '<div style=\"position:relative;font-size:14px;font-weight:bold;color:' + byopdf_titlecolor + ';margin-bottom:4px;\">';
   str += byopdf_pdfdisplayname;
   str += '</div>';
   str += '<div style=\"position:relative;\">';
   str += '<div style=\"float:left;width:' + (wd - 130) + 'px;overflow:hidden;margin-bottom:5px;\">';
   //str += '<div style=\"font-size:14px;font-weight:bold;color:' + byopdf_titlecolor + ';margin-bottom:5px;\">' + byopdf_pdfdisplayname + '</div>';
   str += '<div style=\"font-size:12px;font-weight:normal;color:#555555;\">' + byopdf_pdfsubtitle + '</div>';
   str += '</div>';
   str += '<div onclick=\"byopdf_submitchanges(false);\" ';
   str += 'style=\"float:left;margin:4px 0px 10px 10px;width:90px;font-size:12px;padding:6px;text-align:center;color:#FFFFFF;background-color:' + byopdf_buttonbg1 + ';border-radius:3px;cursor:pointer;\" ';
   str += 'onmouseover=\"jQuery(\'#byopdf_savebutton\').css(\'background-color\',byopdf_buttonbg2);\" ';
   str += 'onmouseout=\"jQuery(\'#byopdf_savebutton\').css(\'background-color\',byopdf_buttonbg1);\" ';
   str += 'id=\"byopdf_savebutton\" ';
   str += 'class=\"byopdf_savebutton\" ';
   str += '>Save</div>';
   //str += '<div onclick=\"byopdf_submitchanges(true);\" ';
   //str += 'style=\"float:left;margin:4px 0px 10px 10px;width:80px;font-size:10px;padding:4px;text-align:center;color:#FFFFFF;background-color:' + byopdf_buttonbg1 + ';border-radius:3px;cursor:pointer;\" ';
   //str += 'onmouseover=\"jQuery(\'#byopdf_getpdf\').css(\'background-color\',byopdf_buttonbg1);\" ';
   //str += 'onmouseout=\"jQuery(\'#byopdf_getpdf\').css(\'background-color\',byopdf_buttonbg2);\" ';
   //str += 'id=\"byopdf_getpdf\" ';
   //str += 'class=\"byopdf_getpdf\" ';
   //str += '>Save & Finish</div>';
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';
   byopdf_imageurls = [];
   byopdf_groupstarted = '';
   for(var i=0;i<byopdf_pdfpositions.length;i++) {
      str += byopdf_getinputdisplay(byopdf_pdfpositions[i],wd,i);
   }
   
   //allow users to upload files
   var lbl = '';
   lbl += '<div ';
   //lbl += 'style=\"min-height:15px;font-size:10px;color:blue;cursor:pointer;margin:4px;\" ';
   lbl += 'style=\"position:relative;margin:8px 0px 10px 8px;width:100px;font-size:12px;padding:6px;text-align:center;color:#FFFFFF;background-color:' + byopdf_buttonbg1 + ';border-radius:3px;cursor:pointer;\" ';
   lbl += 'onclick=\"window.open(byopdf_domain + \'jsfcode/uploadimage.php?userid=9&token=9&prefix=upload&wd_id=' + byopdf_pdfuserid + '&field_id=customfield&imageonly=1\');\" ';
   lbl += '>';
   lbl += 'Upload';
   lbl += '</div>';
   var field = {};
   field.label = byopdf_pdfuploaddisp;
   if(!Boolean(field.label)) field.label = 'Uploaded Images';
   field.shortdescr = byopdf_pdfuploadhelp;
   field.shortdescr += lbl;
   field.field_id = 'upload';
   field.disptype = 'GRID';
   field.json = byopdf_domain + 'jsfcode/jsonpcontroller.php?action=getwdandrows&foruserid=' + byopdf_pdfuserid + '&enabledonly=1&wd_id=' + encodeURIComponent('BYO PDF Uploads');
   str += byopdf_getinputdisplay(field,wd);
   
   str += '</div>';
   jQuery('#byopdf_left').html(str);
   
   //if(Boolean(byopdf_pdfdata)) jQuery('.byopdf_getpdf').show();
   
   while(byopdf_imageurls.length>0) {
      var query = byopdf_imageurls.shift();
      //alert('calling: ' + query);
      byopdf_CallJSONP(query);
   }

}

var byopdf_groupstarted;
function byopdf_getinputdisplay(field,wd,indx) {
   var str = '';
   var tparams = byopdf_explodequery(field.params);
  
   //if(Boolean(tparams.group)) alert('group found: ' + tparams.group);
   //else alert('no group found: ' + field.field_id);
   
   var label = field.subname;
   if(!Boolean(label)) label = field.label;
   
   if(field.disptype=='GRID' && Boolean(field.json)) {
      str += '<div style=\"position:relative;padding-bottom:15px;padding-top:15px;border-bottom:1px solid #f4f4f4;border-top:1px solid #888888;\">';
      str += '<div style=\"float:left;width:' + wd + 'px;margin-bottom:10px;\">';
      str += '<div style=\"font-size:14px;font-weight:bold;color:' + byopdf_titlecolor + ';\">' + label + '</div>';
      if(Boolean(field.shortdescr)) str += '<div style=\"font-size:12px;font-weight:normal;color:#777777;\">' + field.shortdescr + '</div>';
      str += '<div id=\"grid_' + field.field_id + '_moremsg\" style=\"display:none;font-style:italic;font-size:10px;font-weight:bold;color:#344358;\">Please scroll through the images below</div>';
      str += '</div>';
      
      var fjson = field.json;
      
      // If the json URL is dynamic, it will be placed in an associative array
      // dynamically by the caller
      if(Boolean(fjson) && fjson.substr(0,4)!='http' && Boolean(byopdf_dynvalues[fjson])) fjson = byopdf_dynvalues[fjson];
      
      //alert('images url: ' + fjson);
      
      var url = fjson + '&callback=byopdf_showimageoptions&divid=grid_' + field.field_id;
      byopdf_imageurls.push(url);
      str += '<div id=\"grid_' + field.field_id + '\" ';
      str += 'style=\"float:left;width:' + wd + 'px;max-height:210px;overflow-x:hidden;overflow-y:auto;\" ';
      str += '>';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
   } else if(field.disptype=='TEXTBLOCK' && (!Boolean(tparams.noedit) || tparams.noedit=='false')) {
      var twd = Math.floor(wd/2);
      if(twd<100) twd = wd;
      else if(twd>140) twd = 140;
      
      var temp_txt = byopdf_pdfdata[field.field_id];
      if(!Boolean(temp_txt) && Boolean(field.defval)) temp_txt = field.defval;
      if(!Boolean(temp_txt)) temp_txt = '';
      
      // only if this is the first of the group
      if(tparams.group!=byopdf_groupstarted) {
         var toplabel = tparams.group.substring(0,1).toUpperCase() + tparams.group.substring(1);
         str += '<div style=\"position:relative;padding-top:15px;border-top:1px solid #888888;width:100%height:2px;overflow:hidden;\"></div>';
         str += '<div style=\"margin-bottom:8px;\">';
         str += '<div style=\"font-size:14px;font-weight:bold;color:' + byopdf_titlecolor + ';\">' + toplabel + '</div>';
         if(Boolean(field.shortdescr)) str += '<div style=\"font-size:12px;font-weight:normal;color:#777777;\">' + field.shortdescr + '</div>';
         str += '</div>';
      }
      byopdf_groupstarted = tparams.group;

      str += '<div style=\"position:relative;margin-bottom:2px;padding-bottom:1px;margin-top:2px;\">';
      str += '<div style=\"float:left;width:' + twd + 'px;margin-left:5px;margin-right:10px;\">';
      str += '<div style=\"font-size:12px;color:#555555;\">' + label + '</div>';
      str += '</div>';
      str += '<div style=\"float:left;width:' + (wd - twd - 10 - 10) + 'px;\">';
      str += '<input type=\"text\" style=\"width:' + (wd - twd - 10 - 15) + 'px;\" id=\"input_' + field.field_id + '\" value=\"' + temp_txt + '\" onkeyup=\"byopdf_updatetextfield(\'' + field.field_id + '\');\">';
      //str += '<input type=\"text\" style=\"width:' + (twd - 15) + 'px;\" id=\"input_' + field.field_id + '\" value=\"' + temp_txt + '\" onkeyup=\"jQuery(\'#thumb_' + field.field_id + '\').html(jQuery(\'#input_' + field.field_id + '\').val());\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';

   } else if(field.disptype=='INFORMATION') {
      //Outer body
      str += '<div style=\"position:relative;padding-bottom:15px;padding-top:15px;border-bottom:1px solid #f4f4f4;border-top:1px solid #888888;\">';
      
      //Name and title
      str += '<div style=\"margin-bottom:10px;\">';
      str += '<div style=\"font-size:14px;font-weight:bold;color:' + byopdf_titlecolor + ';\">' + label + '</div>';
      if(Boolean(field.shortdescr)) str += '<div style=\"font-size:12px;font-weight:normal;color:#777777;\">' + field.shortdescr + '</div>';
      str += '</div>';

      str += '</div>';      

   } else if(field.disptype=='CHECKBOXLEFT' && (!Boolean(tparams.noedit) || tparams.noedit=='false')) {
      byopdf_customoptions[field.field_id] = [];
      
      //Outer body
      str += '<div style=\"position:relative;padding-bottom:15px;padding-top:15px;border-bottom:1px solid #f4f4f4;border-top:1px solid #888888;\">';
      
      //Name and title
      str += '<div style=\"float:left;width:' + wd + 'px;margin-bottom:10px;\">';
      str += '<div style=\"font-size:14px;font-weight:bold;color:' + byopdf_titlecolor + ';\">' + label + '</div>';
      if(Boolean(field.shortdescr)) str += '<div style=\"font-size:12px;font-weight:normal;color:#777777;\">' + field.shortdescr + '</div>';
      str += '</div>';
      
      var temp_txt = byopdf_pdfdata[field.field_id];
      if(!Boolean(temp_txt) && Boolean(field.defval)) temp_txt = field.defval;
      if(!Boolean(temp_txt)) temp_txt = '';
      
      //Options as specified as parameters
      var ans = byopdf_convertback(temp_txt).split(',');
      for(var j=0;j<20;j++) {
         if(Boolean(tparams['option' + j])) {
            str += '<div style=\"margin-top:1px;font-size:12px;font-weight:normal;\">';
            str += '<input type=\"checkbox\" id=\"cb_' + field.field_id + '_' + j + '\" onclick=\"byopdf_changelist(' + indx + ',1);\" value=\"' + tparams['option' + j] + '\" style=\"font-size:12px;margin:0px 5px 0px 0px;padding:0px;\"';
            for(var k=0;k<ans.length;k++) {
               //alert('checking: ' + ans[k].trim().toLowerCase());
               if(ans[k].trim().toLowerCase()==tparams['option' + j].trim().toLowerCase()) {
                  str += ' CHECKED';
                  break;
               }
            }
            str += '> ' + tparams['option' + j] + '</div>';
         }
      }
         
      // custom fields initialization
      for(var k=0;k<ans.length;k++) {
         if(Boolean(ans[k]) && Boolean(ans[k].trim())) {
            var tfound = false;
            for(var j=0;j<20;j++) {
               if(Boolean(tparams['option' + j])) {
                  if(ans[k].trim().toLowerCase()==tparams['option' + j].trim().toLowerCase()) {
                     tfound = true;
                     break;
                  }
               }
            }
            if(!tfound) byopdf_customoptions[field.field_id].push(ans[k]);
         }
      }
      
      // empty slot to list custom options (later)
      str += '<div id=\"customlist_' + field.field_id + '\" style=\"font-size:12px;font-weight:normal;\"></div>';
      
      // opportunity to create a new option here
      str += '<div>';
      str += '<input type=\"text\" id=\"custom_' + field.field_id + '\" style=\"font-size:10px;\"> ';
      str += '<span onclick=\"byopdf_addcustomoption(' + indx + ',jQuery(\'#custom_' + field.field_id + '\').val());jQuery(\'#custom_' + field.field_id + '\').val(\'\');\" style=\"font-size:10px;color:red;cursor:pointer;\">add item</span>';
      str += '</div>';
      
      str += '</div>';
      str += '\n<script>\n';
      str += 'byopdf_changelist(' + indx + ');\n';
      str += '</script>\n';
      
      
   } else if(field.disptype=='DROPDOWNEXT' && (!Boolean(tparams.noedit) || tparams.noedit=='false')) {
      var twd = Math.floor(wd/2);
      if(twd<100) twd = wd;
      else if(twd>120) twd = 120;
      
      var temp_txt = byopdf_pdfdata[field.field_id];
      if(!Boolean(temp_txt) && Boolean(field.defval)) temp_txt = field.defval;
      if(!Boolean(temp_txt)) temp_txt = '';
      
      str += '<div style=\"position:relative;padding-bottom:15px;padding-top:15px;border-bottom:1px solid #f4f4f4;border-top:1px solid #888888;\">';
      str += '<div style=\"margin-bottom:10px;\">';
      str += '<div style=\"font-size:14px;font-weight:bold;color:' + byopdf_titlecolor + ';\">' + label + '</div>';
      if(Boolean(field.shortdescr)) str += '<div style=\"font-size:12px;font-weight:normal;color:#777777;\">' + field.shortdescr + '</div>';
      str += '</div>';
      str += '<div style=\"\">';
      
      var optstr = '';
      var inlist = false;
      for(var j=0;j<10;j++) {
         if(Boolean(tparams['option' + j])) {
            optstr += '<option value=\"' + tparams['option' + j] + '\"';
            if(temp_txt.toLowerCase()==tparams['option' + j].toLowerCase()) {
               inlist = true;
               optstr += ' SELECTED';
            }
            optstr += '>' + tparams['option' + j] + '</option>';
         }
      }
      
      str += '<input type=\"hidden\" id=\"input_' + field.field_id + '\" value=\"' + temp_txt + '\">';
      str += '<div style=\"position:relative;\">';
      str += '<div style=\"float:left;margin-right:10px;margin-bottom:10px;\">';
      if(tparams.nofreeform!='1') {
        str += '<input type=\"radio\" ';
        str += 'onclick=\"jQuery(\'#input_' + field.field_id + '\').val(jQuery(\'#dd_' + field.field_id + '\').val());byopdf_updatetextfield(\'' + field.field_id + '\');\" ';
        str += 'name=\"radio_' + field.field_id + '\" ';
        str += 'id=\"radio_' + field.field_id + '_1\" ';
        str += 'value=\"1\" ';
        if(inlist) str += ' CHECKED';
        str += '>';
      }
      str += '</div>';
      str += '<div style=\"float:left;margin-right:2px;margin-bottom:16px;\">';
      str += '<span style=\"font-size:12px;font-weight:normal;\">Choose one</span><br>';
      str += '<select onchange=\"jQuery(\'#radio_' + field.field_id + '_1\').prop(\'checked\', true);jQuery(\'#input_' + field.field_id + '\').val(jQuery(\'#dd_' + field.field_id + '\').val());byopdf_updatetextfield(\'' + field.field_id + '\');\" id=\"dd_' + field.field_id + '\" style=\"font-size:10px;\">';
      str += '<option value=\"\"></option>';
      str += optstr;
      str += '</select>';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
     
     
      if(tparams.nofreeform!='1') {
        str += '<div style=\"position:relative;\">';
        str += '<div style=\"float:left;margin-right:10px;margin-bottom:10px;\">';
        str += '<input type=\"radio\" ';
        str += 'onclick=\"jQuery(\'#input_' + field.field_id + '\').val(jQuery(\'#txt_' + field.field_id + '\').val());byopdf_updatetextfield(\'' + field.field_id + '\');\" ';
        str += 'name=\"radio_' + field.field_id + '\" ';
        str += 'id=\"radio_' + field.field_id + '_2\" ';
        str += 'value=\"2\" ';
        if(!inlist) str += ' CHECKED';
        str += '>';
        str += '</div>';
        str += '<div style=\"float:left;margin-right:2px;margin-bottom:16px;\">';
        str += '<span style=\"font-size:12px;font-weight:normal;\">Write Your Own</span><br>';
        str += '<input type=\"text\" ';
        str += 'onkeyup=\"jQuery(\'#radio_' + field.field_id + '_2\').prop(\'checked\', true);jQuery(\'#input_' + field.field_id + '\').val(jQuery(\'#txt_' + field.field_id + '\').val());byopdf_updatetextfield(\'' + field.field_id + '\');\" ';
        str += 'id=\"txt_' + field.field_id + '\" ';
        str += 'style=\"font-size:10px;\" ';
        if(!inlist) str += 'value=\"' + temp_txt + '\"';
        str += '>';
        str += '</div>';
        str += '<div style=\"clear:both;\"></div>';
        str += '</div>';
      }     
            
      str += '</div>';
      str += '</div>';
   }
   return str;
}

function byopdf_showimageoptions(jsondata) {
   var optwd = 55;
   var optht = 90;
  
   var opttotalwd = jQuery('#' + jsondata.divid).width();
   var opttotalht = 210;
   
   //alert('byopdf_showimageoptions: ' + JSON.stringify(jsondata));
   byopdf_ReturnJSON(jsondata);
   
   var results=[];
   if(Boolean(jsondata.results) && jsondata.results.length>0) results=jsondata.results;
   else if(Boolean(jsondata.rows) && jsondata.rows.length>0) results=jsondata.rows;
   //alert('lengths: ' + results.length + ' rows: ' + JSON.stringify(results));
      
   var optacross = Math.floor(opttotalwd / (optwd + 4));
   var optvert = Math.ceil(results.length / optacross);
   if(optvert > Math.floor(opttotalht / optht)) jQuery('#' + jsondata.divid + '_moremsg').show();

   var str = '';
   for(var i=0;i<results.length;i++) {
      var useimg = results[i].smallpng;
      if(!Boolean(useimg) && useimg!='undefined') useimg = results[i].largepng;
      if(!Boolean(useimg) && useimg!='undefined') useimg = results[i].image;
      if(!Boolean(useimg) && useimg!='undefined') useimg = results[i].jpgimage;
      if(!Boolean(useimg) && useimg!='undefined') useimg = results[i].pdfthumbnail;
      if(!Boolean(useimg) && useimg!='undefined') useimg = byopdf_domain + 'jsfimages/noimage.png';
      
      if(!Boolean(results[i].name)) results[i].name='';
      if(!Boolean(results[i].caption)) results[i].caption=results[i].name;
      if(!Boolean(results[i].size)) results[i].size='';
      str += '<div ';
      str += 'id=\"byo' + i + '_' + jsondata.divid + '\" ';
      str += 'draggable=\"true\" ';
      str += 'ondragstart=\"byopdf_drag(event);\" ';
      str += 'class=\"byoimage\" ';
      str += 'style=\"float:left;width:' + optwd + 'px;height:' + optht + 'px;margin-right:4px;overflow:hidden;\" ';
      str += 'data-image=\"' + byopdf_replaceAll('http:','https:',useimg) + '\" ';
      str += 'data-name=\"' + results[i].name + '\" ';
      str += 'data-caption=\"' + results[i].caption + '\" ';
      str += 'data-size=\"' + results[i].size + '\" ';
      str += 'data-remove=\"-1\" ';
      str += '>';
      str += '<div style=\"height:' + (optwd - 4) + 'px;width:' + (optwd - 4) + 'px;overflow:hidden;margin-left:2px;margin-top:2px;\">';
      str += '<img id=\"byo' + i + '_' + jsondata.divid + '_img\" src=\"' + byopdf_replaceAll('http:','https:',useimg) + '\" style=\"max-height:' + (optwd - 4) + 'px;max-width:' + (optwd - 4) + 'px;width:auto;height:auto;display:block;margin:auto;\">';
      str += '</div>';
      str += '<div style=\"font-size:8px;color:#2e2e2e;text-align:center;\">';
      if(Boolean(results[i].caption) && results[i].caption!='upload') str += results[i].caption;
      str += '</div>';
      str += '</div>';
   }
   
   jQuery('#' + jsondata.divid).html(str);
}





var byopdf_dropimgs = {};

function byopdf_allowdrop(ev) {
    ev.preventDefault();
}

function byopdf_drag(ev) {
   var id = ev.currentTarget.id;
   ev.dataTransfer.setData('text', id);
}

function byopdf_drop(ev) {
    ev.preventDefault();
    
    var data = ev.dataTransfer.getData('text');
    var id = ev.currentTarget.id;
    //alert('data: ' + data + ' id: ' + id);
    
    var leftpos = parseInt(jQuery('#' + id).css('left').slice(0,-2));
    var toppos = parseInt(jQuery('#' + id).css('top').slice(0,-2));
    //alert('pagex: ' + ev.pageX + ' LeftWidth: ' + jQuery('#byopdf_left').width() + ' AddlLeft: ' + byopdf_addl_left);
    
    // Get the position to start from
    var viewportOffset = document.getElementById('byopdf_right').getBoundingClientRect();
    byopdf_addl_top = Math.round(viewportOffset.top + window.scrollY);
    
    
    //alert('pagey: ' + ev.pageY + ' headerht: ' + byopdf_headerheight + ' addlht: ' + byopdf_addl_top);
    var xpos = ev.pageX - jQuery('#byopdf_left').width() - 5 - leftpos - byopdf_addl_left;
    var ypos = ev.pageY - byopdf_headerheight - 5 - toppos - byopdf_addl_top;
    //alert('id: ' + id + ' left: ' + leftpos + ' top: ' + toppos + ' x: ' + xpos + ' y: ' + ypos);
    
    var rows = parseInt(jQuery('#' + id).data('rows'));
    var cols = parseInt(jQuery('#' + id).data('cols'));
    
    var cellwd = parseInt(jQuery('#' + id).data('cellwd'));
    var cellht = parseInt(jQuery('#' + id).data('cellht'));
    var xindx = Math.floor(xpos / cellwd);
    var yindx = Math.floor(ypos / cellht);
    var sindx = xindx + (yindx * (cols));
    //alert('cellwd: ' + cellwd + ' cellht: ' + cellht + ' xindex: ' + xindx + ' yindex: ' + yindx + ' index: ' + sindx);
    
    if(!Boolean(byopdf_dropimgs)) {
       byopdf_dropimgs = {};
       byopdf_dropimgs[id] = [];
    } else if(!Boolean(byopdf_dropimgs[id])) {
       byopdf_dropimgs[id] = [];
    }
    
    var temp = {};
    temp.image = jQuery('#' + data).data('image');
    temp.name = jQuery('#' + data).data('name');
    temp.caption = jQuery('#' + data).data('caption');
    temp.size = jQuery('#' + data).data('size');    
    
    var removeindex = parseInt(jQuery('#' + data).data('remove'));
    var removeid = jQuery('#' + data).data('id');
    if(removeindex>=0 && Boolean(removeid)) {
       byopdf_removeimage(removeid,removeindex,(removeid==id));
    }

    if(Boolean(byopdf_dropimgs[id][sindx])) {
       for(var i=0;i<byopdf_dropimgs[id].length;i++) {
          if(!Boolean(byopdf_dropimgs[id][i])) {
             byopdf_dropimgs[id][i] = byopdf_dropimgs[id][sindx];
             break;
          }
       }
    }
    
    byopdf_dropimgs[id][sindx] = temp;
    
    byopdf_fillgrid(id);
    byopdf_changeinput();
   //alert(img.src);      
    
   //ev.target.appendChild(document.getElementById(data));
   //var newdiv = document.createElement('div');
   //newdiv.setAttribute('id', data + '_rimg');
   //newdiv.style.cssText = 'width:px;height:px;float:left;';
}

function byopdf_fillgrid(id) {
   var str = '';
   var rows = parseInt(jQuery('#' + id).data('rows'));
   var cols = parseInt(jQuery('#' + id).data('cols'));
   var xtraht = 0;
   var xtrapd = 0;
   var addpadding = false;
   if(jQuery('#' + id).data('addpadding') && jQuery('#' + id).data('addpadding')=='1') addpadding=true;
   var showcaption = false;
   if(jQuery('#' + id).data('showcaption') && jQuery('#' + id).data('showcaption')=='1') showcaption=true;
   var fontcolor = '#000000';
   if(jQuery('#' + id).data('fontcolor')) fontcolor=jQuery('#' + id).data('fontcolor');
   
   var cellwd = parseInt(jQuery('#' + id).data('cellwd'));
   var cellht = parseInt(jQuery('#' + id).data('cellht'));
   if(addpadding) xtrapd = Math.floor(cellht * 0.03);
   if(showcaption) xtraht = Math.ceil(cellht * 0.19);
   var xtrafontsize = Math.floor((xtraht / 2) - 3);
   if(xtrafontsize<4) xtrafontsize = 4;
         
   str += '<div style=\"position:relative;\">';
   var usingcell = [];
   
   for(var i=0;i<(rows * cols); i++) {
      if(Boolean(byopdf_dropimgs[id][i]) && Boolean(byopdf_dropimgs[id][i].image) && byopdf_dropimgs[id][i].image!='%E%') {
         usingcell[i] = true;
         cellwd = parseInt(jQuery('#' + id).data('cellwd'));
         cellht = parseInt(jQuery('#' + id).data('cellht'));
         //alert('height: ' + cellht + ' extra ht: ' + xtraht + ' font: ' + xtrafontsize);
         var x = (i % cols);
         var y = Math.floor(i/cols);
         var sz = byopdf_dropimgs[id][i].size.toLowerCase();
         //alert('size: ' + sz);
         if(sz=='large') {
            var try_x = cols - x;
            var try_y = rows - y;
            var g = false;
            if(try_y>try_x) try_y = try_x + 1;
            else if(try_x>try_y) try_x = try_y + 1;
            while(try_x>0 && try_y>0 && !Boolean(g)) {
               g = true;
               for(var l_c=0;l_c<try_x;l_c++){
                  for(var l_r=0;l_r<try_y;l_r++){
                     var tempi = (i + l_c + (l_r * cols));
                     if(tempi!=i && (Boolean(byopdf_dropimgs[id][tempi]) || Boolean(usingcell[tempi]))) {
                        g = false;
                        break;
                     }
                  }
                  if(!Boolean(g)) break;
               }
               if(!Boolean(g)) {
                  if(try_y>try_x) try_y = try_x;
                  else try_x = try_x - 1;
               }
            }
            
            cellwd = try_x * cellwd;
            cellht = try_y * cellht;
            
            for(var j=0;j<try_x;j++) {
               for(var k=0;k<try_y;k++) {
                  var tindex = i + ((j-1) + ((k-1) * cols));
                  usingcell[tindex] = true;
               }
            }
         } else if(sz=='large square') {
            if(x<(cols-1) && y<(rows-1)) {
               if(!Boolean(byopdf_dropimgs[id][(i + 1)]) && !Boolean(usingcell[(i + 1)])) {
                  if(!Boolean(byopdf_dropimgs[id][(i + cols)]) && !Boolean(usingcell[(i + cols)])) {
                     if(!Boolean(byopdf_dropimgs[id][(i + cols + 1)]) && !Boolean(usingcell[(i + cols + 1)])) {
                        usingcell[(i + 1)] = true;
                        usingcell[(i + cols)] = true;
                        usingcell[(i + cols + 1)] = true;
                        cellwd = 2 * cellwd;
                        cellht = 2 * cellht;
                     }
                  }
               }
            }
         } else if(sz=='extra wide') {
            if(x<(cols-1)) {
               if(!Boolean(byopdf_dropimgs[id][i + 1]) && !Boolean(usingcell[i + 1])) {
                  cellwd = 2 * cellwd;
                  usingcell[i + 1] = true;
               }
            }
         } else if(sz=='extra tall') {
            if(y<(rows-1)) {
               if(!Boolean(byopdf_dropimgs[id][(i + cols)]) && !Boolean(usingcell[(i + cols)])) {
                  cellht = 2 * cellht;
                  usingcell[i + cols] = true;
               }
            }
         }
         
         str += '<div ';
         str += 'style=\"position:absolute;left:' + (x * parseInt(jQuery('#' + id).data('cellwd'))) + 'px;top:' + (y * parseInt(jQuery('#' + id).data('cellht'))) + 'px;width:' + cellwd + 'px;height:' + cellht + 'px;overflow:hidden;\" ';
         str += 'data-image=\"' + byopdf_dropimgs[id][i].image + '\" ';
         str += 'data-name=\"' + byopdf_dropimgs[id][i].name + '\" ';
         str += 'data-caption=\"' + byopdf_dropimgs[id][i].caption + '\" ';
         str += 'data-size=\"' + sz + '\" ';
         str += 'data-remove=\"' + i + '\" ';
         str += 'data-id=\"' + id + '\" ';
         str += 'draggable=\"true\" ';
         str += 'ondragstart=\"byopdf_drag(event);\" ';
         str += 'id=\"' + id + '_' + i + '\" ';
         str += 'class=\"byoimage\" ';
         str += '>';
         str += '<div style=\"position:relative;margin-top:' + xtrapd + 'px;margin-left:' + xtrapd + 'px;width:' + (cellwd - (2*xtrapd)) + 'px;height:' + (cellht - (2*xtrapd)) + 'px;\">';
         str += '<img src=\"' + byopdf_dropimgs[id][i].image + '\" style=\"max-width:' + (cellwd - (2*xtrapd)) + 'px;max-height:' + (cellht - (2*xtrapd) - xtraht) + 'px;width:auto;height:auto;z-index:1;display:block;margin:auto;\">';
         str += '<div onclick=\"byopdf_removeimage(\'' + id + '\',' + i + ');\" style=\"position:absolute;right:1px;top:1px;color:red;font-size:10px;cursor:pointer;z-index:2;\">X</div>';
         if(showcaption) str += '<div style=\"position:absolute;left:' + (2 + xtrapd) + 'px;top:' + (cellht - (2*xtrapd) - xtraht + 2) + 'px;width:' + (cellwd - (2*xtrapd) - 4) + 'px;color:' + fontcolor + ';font-size:' + xtrafontsize + 'px;z-index:2;text-align:center;\">' + byopdf_dropimgs[id][i].caption + '</div>';
         str += '</div>';
         str += '</div>';
      }
   }
   str += '</div>';
   jQuery('#' + id).html(str);
}



//-----------------------------------------------------------------------------------------------------
// START - Templates display
// Show Template selection div
// byopdf_getTemplatesPage(toolanme,divid);
// toolname - 'Terms and Tools' or 'WRAP'...
// divid - id of element to display selections
   var byopdf_pdfsubtitles;
   var byopdf_pdfdisplaynames;
   var byopdf_pdfuploaddisps;
   var byopdf_pdfuploadhelps;
   var byopdf_pdftoolname;
   var byopdf_pdfcategories;
   var byopdf_templatedivid;

// FIRST - get the template categories to display
function byopdf_getTemplatesPage(toolname,divid){
  //if(!Boolean(toolname)) toolname='WRAP';
  if(!Boolean(toolname)) toolname='Terms and Tools';
  byopdf_pdftoolname = toolname;
  byopdf_templatedivid = divid;

  var params = '';
  params += '&cmsenabled=1';
  params += '&maxcol=10';
  //params += '&divid=' + encodeURIComponent(divid);
  byopdf_getwebdata_jsonp('BYO PDF Template Categories','byopdf_gettemplateoptions',params,true);
}
  
// SECOND - get the templates themselves
function byopdf_gettemplateoptions(jsondata) {
  //alert('get template categories: ' + JSON.stringify(jsondata));
  byopdf_pdfcategories = jsondata.rows;
  
  var params = '';
  params += '&cmsenabled=1';
  params += '&maxcol=10';
  //params += '&divid=' + encodeURIComponent(jsondata.divid);
  params += '&cmsq_byopdftemplates_tool=' + encodeURIComponent(byopdf_pdftoolname);
  byopdf_getwebdata_jsonp('BYO PDF Templates','byopdf_displaytemplateoptions',params,true);
}
   
// THIRD - display templates catgorized
   function byopdf_displaytemplateoptions(jsondata) {
      byopdf_pdfsubtitles = {};
      byopdf_pdfdisplaynames = {};
      byopdf_pdfuploaddisps = {};
      byopdf_pdfuploadhelps = {};
      var catstr = {};
      var js = 'function byopdf_cleartemplatesel(){\n';
      var initjsstr = '';
     
      var tempcellpad = 7;
      var tempwidth = 195;
      var tempheight = 235;
      var temptopheight = 65;

      for (var i=0;i<jsondata.rows.length;i++){
         // initialize a category group for display
         if(!Boolean(jsondata.rows[i].category)) jsondata.rows[i].category = 'none';
         if(!Boolean(catstr[jsondata.rows[i].category])) catstr[jsondata.rows[i].category] = '\n';
        
         var img = jsondata.rows[i].exampleimage;
         if(!Boolean(img)) img = jsondata.rows[i].thumbnail;
         if(!Boolean(img)) img = jsondata.rows[i].hiresimg;
         var disp = jsondata.rows[i].display;
         if(!Boolean(disp)) disp = jsondata.rows[i].name;
         var title = jsondata.rows[i].longdescr;
         if(!Boolean(title)) title = jsondata.rows[i].subtitle;
         if(!Boolean(title)) title = '';
         
         var subtitle = jsondata.rows[i].subtitle;
         if(!Boolean(subtitle)) subtitle = '';

         byopdf_pdfsubtitles[jsondata.rows[i].name] = title;
         byopdf_pdfdisplaynames[jsondata.rows[i].name] = disp;
         byopdf_pdfuploaddisps[jsondata.rows[i].name] = '';
         byopdf_pdfuploadhelps[jsondata.rows[i].name] = '';
         if(Boolean(jsondata.rows[i].upload) && jsondata.rows[i].upload.toLowerCase()=='yes') {
            byopdf_pdfuploaddisps[jsondata.rows[i].name] = jsondata.rows[i].uploaddisp;
            byopdf_pdfuploadhelps[jsondata.rows[i].name] = jsondata.rows[i].uploadhelp;
         }
         
         // tempjs is the code that will execute if this option is selected
         var tempjs = '';
         tempjs += 'byopdf_pdfname=\'' + jsondata.rows[i].name + '\';';
         tempjs += 'byopdf_pdfdisplayname=byopdf_pdfdisplaynames[byopdf_pdfname];';
         tempjs += 'byopdf_pdfsubtitle=byopdf_pdfsubtitles[byopdf_pdfname];';
         tempjs += 'byopdf_pdfuploaddisp=byopdf_pdfuploaddisps[byopdf_pdfname];';
         tempjs += 'byopdf_pdfuploadhelp=byopdf_pdfuploadhelps[byopdf_pdfname];';
         tempjs += 'byopdf_cleartemplatesel();';
         tempjs += 'jQuery(\'#byopdf_tpl' + i + '\').css(\'border\',\'1px solid RED\');';
         tempjs += 'jQuery(\'#byopdf_tplradio_' + i + '\').prop(\'checked\', true);';
        
         if(i==0) initjsstr = tempjs;
         
         catstr[jsondata.rows[i].category] += '<div ';
         catstr[jsondata.rows[i].category] += 'style=\"float:left;width:' + tempwidth + 'px;height:' + tempheight + 'px;padding:' + tempcellpad + 'px;margin-right:' + tempcellpad + 'px;margin-bottom:' + tempcellpad + 'px;overflow:hidden;border:1px solid #FFFFFF;\" ';
         catstr[jsondata.rows[i].category] += 'id=\"byopdf_tpl' + i + '\" ';
         catstr[jsondata.rows[i].category] += 'onclick=\"' + tempjs + '\" ';
         catstr[jsondata.rows[i].category] += '>';
         
         // Radio button + name/description
         catstr[jsondata.rows[i].category] += '<div style=\"position:relative;height:' + temptopheight + 'px;overflow:hidden;\">';
         catstr[jsondata.rows[i].category] += '<div style=\"float:left;width:24px;overflow:hidden;\">';
         catstr[jsondata.rows[i].category] += '<input type=\"radio\" name=\"byopdf_tpl\" id=\"byopdf_tplradio_' + i + '\" onclick=\"' + tempjs + '\">';
         catstr[jsondata.rows[i].category] += '</div>';
         catstr[jsondata.rows[i].category] += '<div style=\"float:left;margin-left:4px;width:' + (tempwidth - 30 - (2 * tempcellpad))+ 'px;overflow:hidden;\">';
         catstr[jsondata.rows[i].category] += '<div style=\"font-size:14px;font-weight:bold;color:#99ada5;\">' + disp + '</div>';
         catstr[jsondata.rows[i].category] += '<div style=\"font-size:12px;font-weight:normal;color:#595959;\">' + subtitle + '</div>';
         catstr[jsondata.rows[i].category] += '</div>';
         catstr[jsondata.rows[i].category] += '<div style=\"clear:both;\"></div>';
         catstr[jsondata.rows[i].category] += '</div>';
         
         catstr[jsondata.rows[i].category] += '<div style=\"margin-top:' + tempcellpad + 'px;width:' + (tempwidth - (2 * tempcellpad)) + 'px;height:' + (tempheight - temptopheight - (3 * tempcellpad))+ 'px;overflow:hidden;\">';
         catstr[jsondata.rows[i].category] += '<img src=\"' + replaceAll('http:','https:',img) + '\" ';
         catstr[jsondata.rows[i].category] += 'style=\"display:block;margin:auto;max-width:' + (tempwidth - (2 * tempcellpad)) + 'px;max-height:' + (tempheight - temptopheight - (3 * tempcellpad))+ 'px;width:auto;height:auto;cursor:pointer;\" ';
         catstr[jsondata.rows[i].category] += '>';
         catstr[jsondata.rows[i].category] += '</div>';
         
         catstr[jsondata.rows[i].category] += '</div>';
         
         js += 'jQuery(\'#byopdf_tpl' + i + '\').css(\'border\',\'1px solid #FFFFFF\');\n';
      }
      js += '}\n';
     
      var str = '';
      for(var j=0;j<byopdf_pdfcategories.length;j++){
        if(Boolean(catstr[byopdf_pdfcategories[j].wd_row_id])) {
          str += '<div style=\"border-top:1px solid #AAAAAA;padding:' + tempcellpad + 'px;\">';
          str += '<div style=\"margin-bottom:4px;font-size:18px;color:#222222;font-weight:bold;\">';
          str += byopdf_pdfcategories[j].name;
          str += '</div>';
          str += '<div style=\"margin-bottom:8px;font-size:14px;color:#999999;\">';
          str += byopdf_pdfcategories[j].description;
          str += '</div>';
          str += catstr[byopdf_pdfcategories[j].wd_row_id];
          str += '<div style=\"clear:both;\"></div>';
          str += '</div>';
        }
      }
      if(Boolean(catstr.none)) {
        str += '<div style=\"border-top:1px solid #AAAAAA;padding:' + tempcellpad + 'px;\">';
        str += '<div style=\"margin-bottom:8px;font-size:18px;color:#222222;font-weight:bold;\">';
        str += 'All Other Templates';
        str += '</div>';
        str += catstr.none;
        str += '<div style=\"clear:both;\"></div>';
        str += '</div>';
      }
     
      str += '\n<script>\n';
      str += js + '\n';
      str += initjsstr + '\n';
      str += '\n</script>\n';
      jQuery('#' + byopdf_templatedivid).html(str);
   }

// END - templates display
//-----------------------------------------------------------------------------------------------------











function byopdf_removeimage(id,indx,skipdisplay) {   
   byopdf_changeinput();
   var temp = byopdf_dropimgs[id][indx];
   if(Boolean(temp)) {
      var rows = parseInt(jQuery('#' + id).data('rows'));
      var cols = parseInt(jQuery('#' + id).data('cols'));
      
      var x = (indx % cols);
      var y = Math.floor(indx/cols);
      
      /*
      var sz = temp.size.toLowerCase();
      if(!Boolean(sz) || sz=='normal' || temp.fit=='0') {
         byopdf_dropimgs[id][indx]=false;
      } else if(sz=='extra wide') {
         byopdf_dropimgs[id][indx]=false;
         byopdf_dropimgs[id][(indx + 1)]=false;
      } else if(sz=='extra tall') {
         byopdf_dropimgs[id][indx]=false;
         byopdf_dropimgs[id][(indx + cols)]=false;
      } else if(sz=='large') {
         byopdf_dropimgs[id][indx]=false;
         byopdf_dropimgs[id][(indx + 1)]=false;
         byopdf_dropimgs[id][(indx + cols)]=false;
         byopdf_dropimgs[id][(indx + cols + 1)]=false;
      }
      */
      byopdf_dropimgs[id][indx]=false;
      
      
      if(!Boolean(skipdisplay)) byopdf_fillgrid(id);
   }
}






function uploadReceiveMessage(e){
   var databack = e.data;
   var databack_a = databack.split(',');
   var p = databack_a[0];
   var f = databack_a[1];
   var w = databack_a[2];
   var fn = databack_a[3];
   byopdf_submitnewupload(fn);
}
window.addEventListener('message', uploadReceiveMessage, false);


function byopdf_submitnewupload(fn){
   if(Boolean(fn) && Boolean(byopdf_pdfuserid)) {
      var query = '';
      query += '&wd_id=' + encodeURIComponent('BYO PDF Uploads');
      query += '&image=' + encodeURIComponent(fn);
      query += '&userid=' + byopdf_pdfuserid;
      query += '&enabled=Yes';
      query += '&name=' + encodeURIComponent('upload');
      query += '&caption=' + encodeURIComponent('upload');
      byopdf_QuickJSON('submitwebdata','byopdf_submitnewupload_return',query,false);
   }
}

function byopdf_submitnewupload_return(jsondata){
   byopdf_ReturnJSON(jsondata);
   byopdf_displaypdfinput();
}






function byopdf_editfield(field_id) {
   jQuery('#edit_' + field_id).hide();
   jQuery('#done_' + field_id).show();
   jQuery('#thumbinput_' + field_id).show();
}

function byopdf_donefield(field_id) {
   jQuery('#edit_' + field_id).show();
   jQuery('#done_' + field_id).hide();
   jQuery('#thumbinput_' + field_id).hide();
}

function byopdf_updatetextfield(field_id) {
   byopdf_changeinput();
   var val = jQuery('#input_' + field_id).val();
   var tstr = byopdf_convertdisplay(byopdf_convertstring(val));
   jQuery('#thumb_' + field_id).html(tstr);
}

function byopdf_changeinput(){
   byopdf_changeswaiting=true;
   //jQuery('.byopdf_savebutton').show();   
   //jQuery('.byopdf_getpdf').hide();
}







function byopdf_conv_in_px(inches) {
   var px = Math.round(inches * byopdf_pxpi);
   return px;
}

function byopdf_conv_pt_px(points) {
   return byopdf_conv_in_px(points/byopdf_ptspi);
}









//----------------------------------
// Loading div
function byopdf_loadinghtml(){
  var str = '';
  str += '<div style=\"border:1px solid #DDDDDD;border-radius:8px;margin:10px;padding:10px;font-size:32px;' + byopdf_fontfamily + 'font-weight:bold;color:#777777;width:200px;\">';
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
  str += 'loadingdots();\n';
  str += '</script>';
  return str;
}




//--------------------------
// auto text boxes
function byopdf_autotext_leave(divid,dfault) {
   var txt = jQuery('#' + divid);
   if(!Boolean(txt.val()) || txt.val() == ''){
      txt.val(dfault);
      txt.css('font-style','italic').css('color','#999999');
   }  
}

function byopdf_autotext_enter(divid,dfault) {
   var txt = jQuery('#' + divid);
   if(Boolean(txt.val()) && txt.val() == dfault){
      txt.val('');
      txt.css('font-style','normal').css('color','#000000');
   }   
}

function byopdf_getautotext(divid,dfault,css,val){
   if(!Boolean(val)) val = dfault;
   if(!Boolean(css)) css = 'width:200px;' + byopdf_fontfamily + 'font-size:16px;background-color:#F0F0F0;border:1px solid #888888;border-radius:2px;margin:3px;';
   
   var dcss = 'font-style:normal;color:#000000;';
   if(val==dfault) dcss = 'font-style:italic;color:#999999;';
   
   var str = '';
   str += '<div class=\"byopdf_txtinput\">';
   str += '<input type=\"text\" value=\"' + val + '\" ';
   str += 'id=\"' + divid + '\" ';
   str += 'onblur=\"byopdf_autotext_leave(\'' + divid + '\',\'' + dfault + '\');\" ';
   str += 'onfocus=\"byopdf_autotext_enter(\'' + divid + '\',\'' + dfault + '\');\" ';
   str += 'style=\"' + css + dcss + '\">';
   str += '</div>';
   return str;
}


//----------------------------------
// format Database formatted date

function byopdf_formatdate(dt,skiptime) {
   var y = parseInt(dt.substr(0,4));
   var m = parseInt(dt.substr(5,2));
   var d = parseInt(dt.substr(8,2));
   var hr = parseInt(dt.substr(11,2));
   var mn = dt.substr(14,2);
   
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
