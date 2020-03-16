//--------------------------------------------------------------------------------------------
// JStoreFront Search/Map widget
// Version 1.0.1
// 190822
//
// Directions:
// 1. Add 2 javascript files to your html header (this file here and the jquery framework)
// It's likely you're already using jquery, in which case you do not want to include it a second time
// <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
// <script src="jsf_map.js"></script>
//
// 2. Add a div element to hold the map on the body of your html page (note its unique id attribute)
// <div id="my_unique_id"></div>
// 
// 3. Set Google map key, Domain name, then call this method at the bottom of your page (or after the loading of your page)
// <script>
// jsfmap_domain = 'https://www.plasticsmarkets.org/';
// jsfmap_googlekey = 'AIzaSyCb9hEAztlzWrk-A4aGU0DZheQJvnu5VHY';
// jsfmap_addJSONMap('my_unique_id',920,500,'companysearch','funcAfterMapLoads','&trackview=jsfmap',true,'Powered by plasticsmarkets.org');
// function funcAfterMapLoads(){
//    //do whatever you want (populate watermark, for instance)
// }
// </script>
//
//
// jsfmap_addJSONMap(divid,width,height,action,callback,query,searchvar,wm)
//    This method will populate a div tag with an interactive map pre-populated with PS/Foam curbside
//    and drop off locations.
//    divid - id to locate the div element to place the map
//    width - How many pixels wide you'd like to make the map (recommended 920)
//    height - How many pixels tall you'd like to make the map (recommended 500)
//    action - jsoncontroller action to retreive results
//    callback - what to do after map loads
//    query - query string containing parameters to retreive results
//    searchvar - set to true or 1 if you want to search the map for a location
//    wm - overlay watermark at the bottom center of the map if you care to add it
//
// Copyright 2018, 2019 (c) JStoreFront
//--------------------------------------------------------------------------------------------

var jsfmap_domain = 'https://www.jstorefront.com/';
var jsfmap_googlekey = '';
var jsfmap_map;
var jsfmap_disablecontrols;
var jsfmap_loadingmap=true;
var jsfmap_latlng;
var jsfmap_infowin;
var jsfmap_markers;
var jsfmap_globalpoints;
var jsfmap_openedpopup;

var jsfmap_searchtxt;
var jsfmap_callback;
var jsfmap_googleretry;

var jsfmap_divid;
var jsfmap_width;
var jsfmap_height;
var jsfmap_borderradius;
var jsfmap_action;
var jsfmap_query;
var jsfmap_searchvar;
var jsfmap_wm;

function jsfmap_addJSONMap(divid,width,height,action,callback,query,searchvar,wm){
   if(Boolean(divid)) jsfmap_divid=divid;
   if(Boolean(width)) jsfmap_width=width;
   if(Boolean(height)) jsfmap_height=height;
   if(Boolean(action)) jsfmap_action=action;
   if(Boolean(callback)) jsfmap_callback=callback;
   if(Boolean(query)) jsfmap_query=query;
   if(Boolean(searchvar)) jsfmap_searchvar=searchvar;
   if(Boolean(wm)) jsfmap_wm=wm;
   
   if(!Boolean(jsfmap_width)) jsfmap_width = jQuery('#' + jsfmap_divid).width();
   if(!Boolean(jsfmap_height)) jsfmap_height = 450;
   
   var ht_addl = 0;
   if(Boolean(jsfmap_searchvar)) ht_addl = 60;
   var ht_map = jsfmap_height - (10 + ht_addl);
   var mark = Math.floor((jsfmap_width - 140)/2);

   var str = '';
   str += '<div id=\"jsfmap_hidden\" style=\"display:none;\"></div>';
   str += '<div id=\"' + jsfmap_divid + '_inner\">';
   str += '  <div id=\"jsfmap_legend\"></div>';
   str += '  <div id=\"jsfmap_canvas_outer\">';
   str += '    <div id=\"jsfmap_canvas_outer_light\" style=\"display:none;z-index:999;background-color:#FFFFFF;opacity:0.8;\">';
   str += '      <div id=\"jsfmap_canvas_outer_light2\" style=\"position:relative;\">';
   str += '      </div>';
   str += '    </div>';
   str += '    <div id=\"jsfmap_canvas\"></div>';   
   str += '    <div id=\"jsfmap_watermark\" style=\"position:absolute;left:' + mark + 'px;bottom:5px;width:140px;height:40px;overflow:hidden;z-index:900;\"></div>';   
   str += '    <div id=\"jsfmap_erasecache\" onclick=\"jsfmap_removecache();\" style=\"position:absolute;left:' + (mark + 140) + 'px;bottom:5px;width:10px;height:40px;overflow:hidden;z-index:901;cursor:pointer;\"></div>';   
   str += '  </div>';      
   str += '  <div id=\"jsfmap_bottom\"></div>';
   str += '</div>';
   str += '<div id=\"jsfmap_content\"></div>';
   jQuery('#' + jsfmap_divid).html(str);
   
   jQuery('#' + jsfmap_divid).css('width',jsfmap_width + 'px');
   jQuery('#' + jsfmap_divid + '_inner').css('height',jsfmap_height + 'px').css('width',jsfmap_width + 'px');
   //jQuery('#jsfmap_legend').css('height',ht_addl + 'px').css('width',jsfmap_width + 'px').css('position','relative').css('background-color','#FFFFFF').css('overflow','hidden');
   jQuery('#jsfmap_legend').css('height','1px').css('width',jsfmap_width + 'px').css('position','relative').css('overflow','hidden');
   jQuery('#jsfmap_canvas_outer').css('height',ht_map + 'px').css('width',jsfmap_width + 'px').css('position','relative');
   jQuery('#jsfmap_canvas_outer_light').css('height',ht_map + 'px').css('width',jsfmap_width + 'px').css('position','absolute').css('left','0px').css('top','0px');
   jQuery('#jsfmap_canvas_outer_light2').css('height',ht_map + 'px').css('width',jsfmap_width + 'px').css('overflow-y','auto').css('overflow-x','auto');
   jQuery('#jsfmap_canvas').css('height',ht_map + 'px').css('width',jsfmap_width + 'px').css('position','relative');
   if(Boolean(jsfmap_borderradius)) jQuery('#jsfmap_canvas').css('border-radius',jsfmap_borderradius + 'px').css('overflow','hidden');
   if(Boolean(jsfmap_searchvar)) jQuery('#jsfmap_bottom').css('height',ht_addl + 'px').css('width',jsfmap_width + 'px').css('position','relative').css('background-color','#EEEEEE');   
   
   var googleurl = 'https://maps.googleapis.com/maps/api/js?key=';
   googleurl += jsfmap_googlekey;
   googleurl += '&callback=jsfmap_initMap';
   if (typeof google === 'undefined' || google === null) jsfmap_CallJSONP(googleurl);
   else jsfmap_initMap();
}

function jsfmap_initMap(){
   jsfmap_loadingmap = true;
   if(Boolean(jsfmap_action) || !Boolean(jsfmap_globalpoints)) {
      
      var uri = jsfmap_query;
      //if(Boolean(jsfmap_searchvar) && Boolean(jsfmap_searchtxt)) uri += '&' + jsfmap_searchvar + '=' + encodeURIComponent(jsfmap_searchtxt);
      jsfmap_QuickJSON(jsfmap_action,'jsfmap_return_locations',uri,true);
   } else {
      var jsondata = {};
      jsondata.responsecode = 1;
      jsondata.rows = jsfmap_globalpoints;
      jsfmap_return_locations(jsondata);
   }
   
   if(Boolean(jsfmap_wm)) jQuery('#jsfmap_watermark').html(jsfmap_wm);
   if(Boolean(jsfmap_searchvar)) jsfmap_showbottom();
}



function jsfmap_return_locations(jsondata){
   //alert(JSON.stringify(jsondata));
   if(Boolean(jsondata) && Boolean(jsondata.responsecode)) {
      jsfmap_ReturnJSON(jsondata);
      jsfmap_googleretry = 0;
      if(Boolean(jsondata.results) && jsondata.results.length>0) jsfmap_globalpoints = jsondata.results;
      else if(Boolean(jsondata.rows) && jsondata.rows.length>0) jsfmap_globalpoints = jsondata.rows;
      else if(Boolean(jsondata.users) && jsondata.users.length>0) jsfmap_globalpoints = jsondata.users;
      jsfmap_buildMap();
   }
   
   
   if(Boolean(jsfmap_callback)) {
      var fn = window[jsfmap_callback];
      if(typeof fn === 'function') fn();
   }   
}

function jsfmap_buildMap(){
      jsfmap_loadingmap = true;
      var lat=39;
      var lng=-97;
      var zm = 4;
      
      jsfmap_infowin = [];
      jsfmap_markers = [];
      
      jsfmap_latlng = new google.maps.LatLng(lat,lng);
      var myOptions = {
         zoom: zm,
         center: jsfmap_latlng,
         mapTypeId: google.maps.MapTypeId.ROADMAP
      };
      
      if(Boolean(jsfmap_disablecontrols)) {
         myOptions.zoomControl = false;
         myOptions.scaleControl = false;
         myOptions.mapTypeControl = false;
         myOptions.rotateControl = false;
         myOptions.fullscreenControl = false;
         myOptions.rotateControl = false;
         myOptions.streetViewControl = false;
      }

      jsfmap_map = new google.maps.Map(document.getElementById('jsfmap_canvas'),myOptions);
      
      for(var i=0;i<jsfmap_globalpoints.length;i++) {
         if(Boolean(jsfmap_globalpoints[i].lat) && Boolean(jsfmap_globalpoints[i].lng)) {
            var latlng_i = new google.maps.LatLng(jsfmap_globalpoints[i].lat,jsfmap_globalpoints[i].lng);
            
            var icon;
            if(Boolean(jsfmap_globalpoints[i].icon)) icon = jsfmap_globalpoints[i].icon;
            var image;
            if(Boolean(icon)) {
               var iconsz = 30;
               if(Boolean(jsfmap_globalpoints[i].iconsz)) iconsz = parseInt(jsfmap_globalpoints[i].iconsz);
               image = {};
               image.url = icon;
               image.scaledSize = new google.maps.Size(iconsz,iconsz);
               //image.scaledSize = new google.maps.Size(30,30);
               image.origin = new google.maps.Point(0, 0);
               image.anchor = new google.maps.Point(15, 15);
            }
            
            var title;
            if(Boolean(jsfmap_globalpoints[i].title)) title = jsfmap_globalpoints[i].title;
            else if(Boolean(jsfmap_globalpoints[i].company)) title = jsfmap_globalpoints[i].company;
            
            var contentStr = '';
            
            if(Boolean(jsfmap_globalpoints[i].contentstr)) {
               contentStr += jsfmap_globalpoints[i].contentstr;
            } else {
               if(Boolean(jsfmap_globalpoints[i].company)) contentStr += '<div style=\"font-size:14px;font-weight:bold;font-family:verdana;color:#222299;padding-bottom:2px;border-bottom:1px solid #222299;margin-bottom:3px;\">' + jsfmap_globalpoints[i].company + '</div>';
               contentStr += '<div style=\"font-size:12px;font-family:verdana;color:#777777;\">';
               if(Boolean(jsfmap_globalpoints[i].addr1)) contentStr += jsfmap_globalpoints[i].addr1 + ' ';
               if(Boolean(jsfmap_globalpoints[i].city)) contentStr += jsfmap_globalpoints[i].city + ', ';
               if(Boolean(jsfmap_globalpoints[i].state)) contentStr += jsfmap_globalpoints[i].state + ' ';
               if(Boolean(jsfmap_globalpoints[i].zip)) contentStr += jsfmap_globalpoints[i].zip + ' ';
               if(Boolean(jsfmap_globalpoints[i].country)) contentStr += jsfmap_globalpoints[i].country;
               contentStr += '</div>';
               if(Boolean(jsfmap_globalpoints[i].phonenum)) contentStr += '<div style=\"font-size:12px;font-family:verdana;color:#333333;\">' + jsfmap_globalpoints[i].phonenum + '</div>';
               if(Boolean(jsfmap_globalpoints[i].instr)) {
                contentStr += '<div style=\"margin-top:3px;margin-bottom:4px;font-size:8px;font-family:verdana;color:#777777;font-style:italics;\">';
                contentStr += jsfmap_globalpoints[i].instr;
                contentStr += '</div>';
               }
               
               if(Boolean(jsfmap_globalpoints[i].website)) contentStr += '<a href=\"' + jsfmap_checkurl(jsfmap_globalpoints[i].website) + '\" style=\"font-size:10px;font-family:arial;color:red;\" target=\"_new\">View Website</a>';
               else if(Boolean(jsfmap_globalpoints[i].url)) contentStr += '<a href=\"' + jsfmap_checkurl(jsfmap_globalpoints[i].url) + '\" style=\"font-size:10px;font-family:arial;color:red;\" target=\"_new\">View Website</a>';
            }
            
            jsfmap_infowin[i] = new google.maps.InfoWindow({content: contentStr});      
            
            jsfmap_markers[i] = new google.maps.Marker({
               position: latlng_i,
               map: jsfmap_map,
               icon: image,
               title:title,
               zIndex: (100 + i)
            });
            
            google.maps.event.addListener(jsfmap_markers[i], 'click', (function(idx) {
               return function(){
                  //jsfmap_infowin[idx].open(jsfmap_map,jsfmap_markers[idx]);
                  if(Boolean(jsfmap_openedpopup)) jsfmap_openedpopup.close();
                  jsfmap_openedpopup = jsfmap_infowin[idx];
                  jsfmap_openedpopup.open(jsfmap_map,jsfmap_markers[idx]);
               };
            })(i));
         }
      }
      jsfmap_loadingmap = false;
}





function jsfmap_showbottom(){
   var searchtxt;
   var txtwd;
   var fsz;
   var btn;
   var pad;
   if(jsfmap_width<480) {
      searchtxt = 70;
      btn = 35;
      fsz = 8;
      pad = 4;
   } else if (jsfmap_width<600) {
      searchtxt = 90;
      btn = 40;
      fsz = 10;
      pad = 6;
   } else if (jsfmap_width<720) {
      searchtxt = 130;
      btn = 55;
      fsz = 12;
      pad = 8;
   } else {
      searchtxt = 170;
      btn = 70;
      fsz = 14;
      pad = 10;
   }
   txtwd = jsfmap_width - searchtxt - btn - pad - 20;
   
   var txt = 'Enter a location';
   if(Boolean(jsfmap_searchtxt)) txt = jsfmap_searchtxt;
   
   var str = '';
   str += '<div id=\"jsfmap_disclaimer\" style=\"float:left;width:' + txtwd + 'px;margin-right:' + 10 + 'px;margin-top:5px;\">'; 
   str += '</div>';
   
   str += '<div id=\"jsfmap_searchform\" style=\"float:left;width:' + (jsfmap_width - txtwd - 15) + 'px;margin-top:10px;\">'; 
   str += '<div id=\"jsfmap_searchform_text\" style=\"float:left;width:' + (searchtxt + pad) + 'px;margin-right:5px;\">'; 
   str += '<input ';
   str += 'type=\"text\" ';
   str += 'id=\"mapsearchtext\" ';
   str += 'style=\"font-size:' + fsz + 'px;width:' + searchtxt + 'px;border-radius:3px;\" ';
   str += 'onblur=\"jsfmap_searchblur();\" ';
   str += 'onfocus=\"jsfmap_searchfocus();\" ';
   str += 'onkeyup=\"jsfmap_search_enter_check(event);\" ';
   str += 'value=\"' + txt + '\" ';
   str += '>';
   str += '</div>';
   str += '<div ';
   str += 'style=\"float:left;width:' + (btn - 10) + 'px;padding:4px;border:1px solid #333333;border-radius:4px;text-align:center;background-color:#DDDDDD;font-size:' + fsz + 'px;font-family:arial;cursor:pointer;\" ';
   str += 'onclick=\"jsfmap_search_enter();\" '; 
   str += 'id=\"jsfmap_locate_button\" '; 
   str += '>'; 
   str += 'Locate</div>';
   str += '<div ';
   str += 'style=\"display:none;float:left;width:' + (btn - 10) + 'px;padding:3px;border:0px;text-align:center;font-size:' + fsz + 'px;font-family:arial;font-style:italic;\" ';
   str += 'id=\"jsfmap_locate_wait\" '; 
   str += '>'; 
   str += 'Searching...</div>';
   str += '</div>';
   
   jQuery('#jsfmap_bottom').html(str);
   jsfmap_searchblur();
}

function jsfmap_searchblur() {
   jQuery('#mapsearchtext').css('font-style','normal').css('color','#222222');
   if(jQuery('#mapsearchtext').val() == '' || jQuery('#mapsearchtext').val() == 'Enter a location') {
      jQuery('#mapsearchtext').val('Enter a location');
      jQuery('#mapsearchtext').css('font-style','italic').css('color','#B0B0B0');
   }
   //alert('onblur');
}

function jsfmap_searchfocus() {
   jQuery('#mapsearchtext').css('font-style','normal').css('color','#222222');
   if(jQuery('#mapsearchtext').val() == 'Enter a location') {
      jQuery('#mapsearchtext').val('');
   }
}


function jsfmap_search_enter_check(event){
   var charCode = (typeof event.which === "number") ? event.which : event.keyCode;
   if (charCode==13) jsfmap_search_enter();
}

function jsfmap_search_enter(){
   jQuery('#jsfmap_locate_button').hide();
   jQuery('#jsfmap_locate_wait').show();
   var srchtxt=jQuery('#mapsearchtext').val();
   if(srchtxt=='Enter a location') srchtxt='';
   if(srchtxt=='999999' || srchtxt=='refresh') {
      jsfmap_removecache();
      srchtxt = '';
   }
   jsfmap_searchtxt = srchtxt;
   jsfmap_searchlocation();
}




//-------------------------------------------
// Move the map and zoom into the location

function jsfmap_searchlocation() {
   if(Boolean(jsfmap_searchtxt)) {
      var uri = '&zip=' + encodeURIComponent(jsfmap_searchtxt);
      jsfmap_QuickJSON('geocode','jsfmap_return_searchlocation',uri,true);
   }
}

function jsfmap_return_searchlocation(jsondata){
   jsfmap_ReturnJSON(jsondata);
   
   //alert('json: ' + JSON.stringify(jsondata));
   if(jsondata.responsecode==1) {
      var zoom = 10;
      if(!Boolean(jsondata.accuracy) || (jsondata.accuracy!='city' && jsondata.accuracy!='zip')) zoom=7;
      //alert('zoom: ' + zoom);
      jsfmap_moveToLocation(jsondata.latitude,jsondata.longitude,zoom);
   } else {
      alert('Sorry, that location could not be found.  Please try searching again.');
   }
   jQuery('#jsfmap_locate_wait').hide();   
   jQuery('#jsfmap_locate_button').show();
}

function jsfmap_moveToLocation(lat,lng,zoom) {
   if(!Boolean(zoom)) zoom=8;
    var center = new google.maps.LatLng(lat,lng);
    jsfmap_map.panTo(center);
    jsfmap_map.setZoom(zoom);
}

// Adjust the zoom of the map is loaded, otherwise check again
// shortly until we're either successful or we tried 10 times
var jsfmap_zoomcounter;
function jsfmap_setZoom(zoom) {
   if(!Boolean(jsfmap_zoomcounter)) jsfmap_zoomcounter=0;
   if(!Boolean(jsfmap_map) || Boolean(jsfmap_loadingmap)){
      jsfmap_zoomcounter++;
      if(jsfmap_zoomcounter<10) {
         setTimeout(jsfmap_setZoom,500,zoom);
      } else {
         jsfmap_zoomcounter=0;
      }
   } else {
      jsfmap_zoomcounter=0;
      jsfmap_map.setZoom(zoom);
   }
   
}










//-------------------------------------------
// JSON utility functions

function jsfmap_QuickJSON(action,callback,query,checkcache) {
   var runjson = true;
   
   if(!Boolean(action)) alert('internal error (code 9322)');
   if(!Boolean(callback)) alert('internal error (code 9323)');
   
   if (Boolean(action) && Boolean(callback)) {
      var url = jsfmap_domain + 'jsfcode/jsonpcontroller.php?action=' + encodeURIComponent(action);
      if (Boolean(query)) url += query;
      
      var saveurl = url;
      url = url + '&callback=' + encodeURIComponent(callback);
      
      //alert('URL: ' + url);
      
      if(Boolean(checkcache)) {
         //alert('checking cache: ' + url);
         var str = window.localStorage.getItem('jsfmap_cache');
         if(Boolean(str)){
            //alert('found cache: ' + url);
            var jsf_cache = JSON.parse(str);
            if(jsf_cache.expiry<(Math.floor(Date.now() / 1000))) {
               //alert('expired cache: ' + url);
               jsf_cache = '';
               window.localStorage.removeItem('jsfmap_cache');
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
         if(Boolean(checkcache)) url = url + '&jsonsaveval=' + encodeURIComponent(saveurl);
         //alert('NOT using cache: ' + url);
         jsfmap_CallJSONP(url);
      }
   }   
   
}

function jsfmap_removecache(){
   window.localStorage.clear();
}


function jsfmap_ReturnJSON(jsondata){
   //alert(JSON.stringify(jsondata));
   if (Boolean(jsondata) && Boolean(jsondata.jsonsaveval)) {
      //alert('CHJ***** checking cache: jsf_endjsoning  url: ' + jsondata.jsonsaveval);
      var jsf_cache = {};
      jsf_cache.expiry = (Math.floor(Date.now() / 1000) + (60*60*24));
      jsf_cache.countindex = 1;
      var str = window.localStorage.getItem('jsfmap_cache');
      window.localStorage.removeItem('jsfmap_cache');
      if(Boolean(str)) {
         //alert('found jsf_cache, checking expiry...');
         temp = JSON.parse(str);
         if(Boolean(temp) && temp.expiry>(Math.floor(Date.now() / 1000)) && temp.countindex<150) {
            jsf_cache = temp;
         }
      }
      if(!Boolean(jsf_cache[jsondata.jsonsaveval])) jsf_cache.countindex++;
      jsf_cache[jsondata.jsonsaveval] = jsondata;
      window.localStorage.setItem('jsfmap_cache',JSON.stringify(jsf_cache));
   }
}


function jsfmap_CallJSONP(url) {
    var script = document.createElement('script');
    script.setAttribute('src', url);
    document.getElementsByTagName('head')[0].appendChild(script);
}

function jsfmap_checkurl(url){
   if(Boolean(url) && url.substr(0,4).toLowerCase()!='http') url = 'http://' + url;
   return url;
}