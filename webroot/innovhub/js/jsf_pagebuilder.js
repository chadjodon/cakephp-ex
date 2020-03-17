var jsfpb_domain = 'https://www.plasticsmarkets.org/';

var jsfpb_contenttocall;
var jsfpb_resizedivs;
var jsfpb_verticalcenterdivs;
var jsfpb_pagestocall = [];
var jsfpb_vbstocall = [];
var jsfpb_displayvalues;
var jsfpb_wdname;
var jsfpb_callback;

//Something needs to set this field before a form can be displayed
var jsfpb_current_wd_row_id;


var jsfpb_headerheight = 0;
var jsfpb_footerheight = 0;

var jsfpb_defaultfont = 'Arial';

var jsfpb_devmode = false;

var jsfpb_jsoncontroller = 'jsfcode/jsoncontroller.php?jodon=1';
var jsfpb_servercontroller = 'jsfcode/jsoncontroller.php?format=jsonp';

var jsfpb_captcha;

// Given a list of WD table nvp data, this will construct the page object
// for display
function jsfpb_loadpageobject(values,name,ver){
   if(name.substr(0,6)!='Page: ') name = 'Page: ' + name;   
   if(!Boolean(values)) values = jsfpb_displayvalues;
   else jsfpb_displayvalues = values;
   
   var page ={};
   page.rows=[];
   
   // find most recent version
   if(!Boolean(ver) || parseInt(ver)<1){
   	var maxver = 0;
		for(var j=0;j<values.length;j++){
				
			var nextname = values[j].name;
			if(name==nextname) {
				
				// If most recent active version is requested
				var allowthisver = false;
				if(!Boolean(ver) || (parseInt(ver)<0 && Boolean(values[j].verstatus) && values[j].verstatus=='ACTIVE')){
					allowthisver = true;
				}
				
				if(allowthisver && Boolean(values[j].version) && parseInt(values[j].version) > maxver) {
					maxver = parseInt(values[j].version);
				}
			}
		}
		ver = maxver;
   }

   // build the object
   for(var j=0;j<values.length;j++){
      var nextname = values[j].name;
      if(name==nextname) {
      	if(!Boolean(ver) || !Boolean(values[j].version) || (parseInt(ver)==parseInt(values[j].version))) {
				page = JSON.parse(values[j].value);
				page.name = values[j].name.substr(6);
				page.wd_row_id = values[j].wd_row_id;
				if(Boolean(values[j].version) && Boolean(values[j].verstatus)) {
					page.version = values[j].version;
					page.verstatus = values[j].verstatus;
				}
				break;
			}
      }
   }
      
   //check to see if this page is broken up into several DB entries
   if(Boolean(page.rowcount) && page.rowcount>page.rows.length) {
      var finished = false;
      var wdcounter = 1;
      while(!finished && page.rowcount > page.rows.length){
         finished = true;
         for(var j=0;j<values.length;j++){
            var nextname = name + '_jsf' + wdcounter;
            if(values[j].name==nextname) {
            	if(!Boolean(ver) || !Boolean(values[j].version) || (parseInt(ver)==parseInt(values[j].version))) {
						finished = false;
						//alert('abouto to parse ' + j + ' counter: ' + wdcounter);
						if(Boolean(values[j].value)) {
							var temppage = JSON.parse(values[j].value);
							page.rows = page.rows.concat(temppage.rows);
						}
						wdcounter++;
						break;
					}
            }
         }
      }
   }
   
   return page;
}

//Without knowing all the nvp data, get a single page
// set version to "active"/"latest"/[ver #]
var jsfpb_lastver;
var jsfpb_lastdivid;
function jsfpb_getPage(wdname,pagename,pagewidth,divid,callback,debug,version){
   if(Boolean(wdname)) jsfpb_wdname = wdname;
   jsfpb_callback = callback;
   
   if(jsfpb_devmode && !Boolean(version)) verion = 'indevelopment';
   else if(!Boolean(version)) version = 'active';
   
   var query = '';
   query += '&pagename=' + encodeURIComponent(pagename);
   if(Boolean(version)) query += '&version=' + encodeURIComponent(version);
   query += '&wd_id=' + encodeURIComponent(jsfpb_wdname);
   query += '&width=' + encodeURIComponent(pagewidth);
   query += '&divid=' + encodeURIComponent(divid);
   query += '&subaction=singlepage';
   //if(Boolean(debug)) alert('url: ' + query + ' calling page builder: ' + pagename);
   //alert('url: ' + query + ' calling page builder: ' + pagename);
   
   jsfpb_lastver = version;
   jsfpb_lastdivid = divid;
   
   jsfpb_QuickJSON('pagebuilder','jsfpb_getPage_return',query,true);
}

function jsfpb_getPage_return(jsondata) {
   //alert('callback: ' + jsfpb_callback + ' just retreived page: ' + JSON.stringify(jsondata));
   jsfpb_ReturnJSON(jsondata);
   if(!Boolean(jsondata.divid)) jsondata.divid = 'jsfpb_body';
   if(!Boolean(jsondata.width)) jsondata.width = jQuery(window).width();
   
   if(Boolean(jsondata.responsecode) && Boolean(jsondata.page)) {
      jsfpb_getPageHTML(jsondata.page,jsondata.width,jsondata.divid);
   }
   if(Boolean(jsfpb_callback)) {
      var fn = window[jsfpb_callback];
      if(typeof fn === 'function') fn(jsondata);
      jsfpb_callback = '';
   }
}



function jsfpb_getPageHTML(jsfpb_page,pagewidth,divid,trackid,userinfo){
   //alert('getting page divid: ' + divid + ' page obj: ' + JSON.stringify(jsfpb_page));
   //jsfpb_pagestocall = [];
   //jsfpb_vbstocall = [];
   jsfpb_getInternalPageHTML(jsfpb_page,pagewidth,divid,trackid,userinfo);
   //alert('checking pages: ' + JSON.stringify(jsfpb_pagestocall));
   while(Boolean(jsfpb_pagestocall) && jsfpb_pagestocall.length>0) {
      var pageobj = jsfpb_pagestocall.shift();
      //alert('internal page: ' + JSON.stringify(pageobj));      
      /*
      var tempname = pageobj.page;
      if(tempname.substr(0,6)!='Page: ') tempname = 'Page: ' + tempname;
      var temppage = jsfpb_loadpageobject('',tempname);
      //alert('internal page: ' + JSON.stringify(pageobj) + '\n\n' + JSON.stringify(temppage));
      jsfpb_getInternalPageHTML(temppage,pageobj.width,pageobj.divid,trackid,userinfo);
      */
      
      jsfpb_getPage(jsfpb_wdname,pageobj.page,pageobj.width,pageobj.divid);
   }
   
   //alert('internal vbs: ' + JSON.stringify(jsfpb_vbstocall));
   while(Boolean(jsfpb_vbstocall) && jsfpb_vbstocall.length>0) {
      var vobj = jsfpb_vbstocall.shift();
      jsfpb_drawvisualcomponents(vobj.divid,vobj.name);
   }
}

function jsfpb_replaceAll(find, replace, str) {
   if(!Boolean(str)) str = '';
   find = find.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
   return str.replace(new RegExp(find, 'g'), replace);
}

function jsfpb_flattenstr(str,max,removespecial) {
   if(!Boolean(str)) str = '';
   var newstr = str.toLowerCase();
   if(Boolean(removespecial)) {
      newstr = jsfpb_replaceAll('\'','',newstr);
      newstr = jsfpb_replaceAll(',','',newstr);
      newstr = jsfpb_replaceAll('.','',newstr);
      newstr = jsfpb_replaceAll(':','',newstr);
      newstr = jsfpb_replaceAll(';','',newstr);
      //newstr = jsfpb_replaceAll('\\','',newstr);
      newstr = jsfpb_replaceAll('/','',newstr);
      //newstr = jsfpb_replaceAll(')','',newstr);
      //newstr = jsfpb_replaceAll('(','',newstr);
      newstr = jsfpb_replaceAll('&','',newstr);
      newstr = jsfpb_replaceAll('\"','',newstr);
      newstr = jsfpb_replaceAll('\n','',newstr);
   }
   newstr = jsfpb_replaceAll(' ','',newstr);
   if(Boolean(max) && !isNAN(max) && parseInt(max)>0) newstr = newstr.substr(0,parseInt(max));
   return newstr;
}


function jsfpb_drawarrow(fg,bg,dir,ht,wd,thk) {
   if(!Boolean(fg)) fg = '#777777';
   if(!Boolean(bg)) bg = '#FFFFFF';
   if(!Boolean(dir) || dir!='right') dir = 'left';
   if(!Boolean(ht)) ht = 28;
   if(!Boolean(wd)) wd = 12;
   if(!Boolean(thk)) thk = 4;
   
   var hht = Math.round(ht/2);
   
   var left = 0;
   var ddir = 'left';
   if(dir=='left') {
      left = thk;
      ddir = 'right';
   }
   
   var str = '';
   str += '<div style=\"position:relative;\">';
   str += '<div style=\"position:absolute;left:0px;top:0px;z-index:1;\">';
   str += '<div style=\"width: 0;height: 0;border-top-width:' + hht + 'px;border-top-style: solid;border-top-color: transparent;border-' + ddir + '-width:' + wd + 'px;border-' + ddir + '-style: solid;border-' + ddir + '-color:' + fg + ';\"></div>';
   str += '<div style=\"width: 0;height: 0;border-bottom-width:' + hht + 'px;border-bottom-style: solid;border-bottom-color: transparent;border-' + ddir + '-width:' + wd + 'px;border-' + ddir + '-style: solid;border-' + ddir + '-color:' + fg + ';\"></div>';
   str += '</div>';
   str += '<div style=\"position:absolute;left:' + left + 'px;top:' + thk + 'px;z-index:2;\">';
   str += '<div style=\"width: 0;height: 0;border-top-width:' + (hht - thk) + 'px;border-top-style: solid;border-top-color: transparent;border-' + ddir + '-width:' + (wd - thk) + 'px;border-' + ddir + '-style: solid;border-' + ddir + '-color:' + bg + ';\"></div>';
   str += '<div style=\"width: 0;height: 0;border-bottom-width:' + (hht - thk) + 'px;border-bottom-style: solid;border-bottom-color: transparent;border-' + ddir + '-width:' + (wd - thk) + 'px;border-' + ddir + '-style: solid;border-' + ddir + '-color:' + bg + ';\"></div>';
   str += '</div>';
   str += '</div>';
   return str;
}



function jsfpb_drawdownloadicon(fg,bg) {
   if(!Boolean(bg)) bg='#F2F2F2';
   if(!Boolean(fg)) fg='#334357';
   
   var str = '';
   str += '<div style=\"position:relative;width:26px;height:25px;overflow:hidden;background-color:' + bg + ';\">';

   str += '<div style=\"position:absolute;left:9px;top:0px;z-index:1;\">';
   str += '<div style=\"width:8px;height:8px;overflow:hidden;background-color:' + fg + ';border-top-left-radius:2px;border-top-right-radius:2px\">';
   str += '</div>';
   str += '</div>';

   str += '<div style=\"position:absolute;left:4px;top:8px;z-index:10;\">';
   str += '<div style=\"position:relative;\">';
   str += '<div style=\"float:left;width: 0;height: 0;border-bottom-width: 10px;border-bottom-style: solid;border-bottom-color: transparent;border-right-width: 9px;border-right-style: solid;border-right-color:' + fg + ';\"></div>';
   str += '<div style=\"float:left;width: 0;height: 0;border-bottom-width: 10px;border-bottom-style: solid;border-bottom-color: transparent;border-left-width: 9px;border-left-style: solid;border-left-color:' + fg + ';\"></div>';
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';
   str += '</div>';

   str += '<div style=\"position:absolute;left:2px;top:8px;z-index:2;\">';
   str += '<div style=\"position:relative;\">';
   str += '<div style=\"float:left;width: 0;height: 0;border-bottom-width: 12px;border-bottom-style: solid;border-bottom-color: transparent;border-right-width: 11px;border-right-style: solid;border-right-color:' + bg + ';\"></div>';
   str += '<div style=\"float:left;width: 0;height: 0;border-bottom-width: 12px;border-bottom-style: solid;border-bottom-color: transparent;border-left-width: 11px;border-left-style: solid;border-left-color:' + bg + ';\"></div>';
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';
   str += '</div>';

   str += '<div style=\"position:absolute;left:0px;top:16px;z-index:1;\">';
   str += '<div style=\"width:26px;height:9px;overflow:hidden;background-color:' + fg + ';border-radius:2px;\">';
   str += '</div>';
   str += '</div>';
   

   str += '</div>';
   
   return str;
}



function jsfpb_getInternalPageHTML(jsfpb_page,pagewidth,divid,trackid,userinfo){
   if(!Boolean(pagewidth)) pagewidth = jQuery(window).width();
   
   var str = '';
   str += '<div style=\"position:relative;';
   if(Boolean(jsfpb_page.ffm)) {
      str += 'font-family:' + jsfpb_page.ffm + ';';
      jsfpb_defaultfont = jsfpb_page.ffm;
      //alert('setting default font to: ' + jsfpb_defaultfont);
   }
   if(Boolean(jsfpb_page.fsz)) str += 'font-size:' + jsfpb_page.fsz + 'px;';
   if(Boolean(jsfpb_page.clr)) str += 'color:' + jsfpb_page.clr + ';';
   str += 'width:' + pagewidth + 'px;\">';
   jsfpb_contenttocall = [];
   jsfpb_resizedivs = [];
   jsfpb_verticalcenterdivs = [];
   for(var i=0;i<jsfpb_page.rows.length;i++) {
      if(!Boolean(jsfpb_page.rows[i].disable)) str += jsfpb_getRowHTML(jsfpb_page,i,pagewidth,divid,trackid,userinfo);
   }
   
   str += '</div>';
   
   //return str;
   jQuery('#' + divid).html(str);
   
   for(var i=0;i<jsfpb_contenttocall.length;i++){
      jsfpb_getcontent_jsonp(jsfpb_contenttocall[i].shortname,jsfpb_contenttocall[i].divid);
   }
   
   for(var i=0;i<jsfpb_resizedivs.length;i++){
      jQuery('#' + jsfpb_resizedivs[i].divid).hide();
      setTimeout( function(x){
         //var tw = jQuery('#' + jsfpb_resizedivs[i].dividfrom).outerWidth();
         var th = jQuery('#' + x.dividfrom).innerHeight();
         //jQuery('#' + jsfpb_resizedivs[i].divid).css('min-width',tw + 'px');
         jQuery('#' + x.divid).css('min-height',th + 'px');
         //alert('width: ' + tw + ' height: ' + th + ' from: ' + jsfpb_resizedivs[i].dividfrom + ' to: ' + jsfpb_resizedivs[i].divid);
         //alert('resize height: ' + th);
         jQuery('#' + x.divid).fadeIn(250);
      },600,jsfpb_resizedivs[i]);
   }
   
   
   for(var i=0;i<jsfpb_verticalcenterdivs.length;i++){
      //alert('centering ' + jsfpb_verticalcenterdivs[i].outerdiv + ', ' + jsfpb_verticalcenterdivs[i].innerdiv + ', top: ' + jsfpb_verticalcenterdivs[i].top);
      setTimeout( function(x){
         var ouht = jQuery('#' + x.outerdiv).innerHeight();
         var inht = jQuery('#' + x.innerdiv).innerHeight();
         //alert('outer height: ' + ouht + ' inner height: ' + inht);
         if(ouht < inht) ouht = inht;
         var diff = ouht - inht;
         var temptop = Math.round(diff * (parseInt(x.top)/100));
         jQuery('#' + x.innerdiv).css('top',temptop + 'px');
         //alert('centering ' + x.outerdiv + ', ' + x.innerdiv + ', top: ' + x.top + ', temptop: ' + temptop);
         //alert('outer height: ' + ouht + ' inner height: ' + inht + ' centering ' + x.outerdiv + ', ' + x.innerdiv + ', top: ' + x.top + ', temptop: ' + temptop);
      },800,jsfpb_verticalcenterdivs[i]);
   }
   
   
   
   jsfpb_contenttocall = [];
}

function jsfpb_getRowHTML(jsfpb_page,r,pagewidth,divid,trackid,userinfo) {
   //alert('ROW: ' + JSON.stringify(jsfpb_page.rows[r]));
   if(jsfpb_page.rows[r].ht=='%%%HEIGHT%%%') {
      var tempheight = jQuery(window).height() - jsfpb_headerheight - jsfpb_footerheight;
      jsfpb_page.rows[r].vht = tempheight;
      //alert('row ' + r + ' height set height: ' + jsfpb_page.rows[r].ht);
   } else if(jsfpb_page.rows[r].ht=='%%%FULLHEIGHT%%%') {
      var tempheight = jQuery(window).height();
      jsfpb_page.rows[r].vht = tempheight;
      //alert('row ' + r + ' height set fullheight: ' + jsfpb_page.rows[r].ht);
   } else if(Boolean(jsfpb_page.rows[r].ht)) {
      //alert('row ' + r + ' height set explicitly: ' + jsfpb_page.rows[r].ht);
      jsfpb_page.rows[r].vht = jsfpb_page.rows[r].ht;
   }
   //alert('total height: ' + jQuery(window).height() + ' header: ' + jsfpb_headerheight + ' footer: ' + jsfpb_footerheight + ' height: ' + row.ht);
   
   var row = jsfpb_page.rows[r];
   var total = row.slots.length;
   var tempdivid = divid + '_r' + r;
   
   var dtype = 'browser';
   if(pagewidth<490) dtype = 'mobile';
   else if(pagewidth<770) dtype = 'tablet';
   
   var effectivewd = pagewidth;
   var effectivelf = 0;
   if(Boolean(jsfpb_page.wd) && jsfpb_page.wd>200 && jsfpb_page.wd<pagewidth) {
      effectivelf = Math.round((pagewidth - jsfpb_page.wd)/2);
      effectivewd = jsfpb_page.wd;
   }
      
   var divwidth = effectivewd;
   if(row.type.toLowerCase().substr(0,8)=='carousel') divwidth = total * effectivewd;
   
   var str = '';
   
   // outside div in case someone wants to hide/show with code
   //alert('label: ' + row.lbl + ' flattened: ' + jsfpb_flattenstr(row.lbl,false,true));
   str += '<div id=\"jsfpbc_' + jsfpb_flattenstr(row.lbl,false,true) + '\" style=\"position:relative;\">';
   
   // div is entire width of window. (this may or may not include background stuff)
   str += '<div id=\"' + tempdivid + '_outer\" style=\"position:relative;width:' + pagewidth + 'px;';
   if(Boolean(jsfpb_page.rows[r].pad) && jsfpb_page.rows[r].pad > 2) str += 'margin-top:' + jsfpb_page.rows[r].pad + 'px;';
   if(Boolean(jsfpb_page.rows[r].ext) && Boolean(jsfpb_page.rows[r].img) && Boolean(jsfpb_page.rows[r].tile)) str += 'background-image:URL(' + jsfpb_replaceAll('http:','https:',jsfpb_page.rows[r].img) + ');background-repeat:repeat;';
   else if(Boolean(jsfpb_page.rows[r].ext) && Boolean(jsfpb_page.rows[r].img) && Boolean(jsfpb_page.rows[r].ancimg)) str += 'background-image:URL(' + jsfpb_replaceAll('http:','https:',jsfpb_page.rows[r].img) + ');background-size:cover;background-position:bottom;';
   else if(Boolean(jsfpb_page.rows[r].ext) && Boolean(jsfpb_page.rows[r].img)) str += 'background-image:URL(' + jsfpb_replaceAll('http:','https:',jsfpb_page.rows[r].img) + ');background-size:cover;';
   if(Boolean(jsfpb_page.rows[r].ext) && Boolean(jsfpb_page.rows[r].bg)) str += 'background-color:' + jsfpb_page.rows[r].bg + ';';
   str += '\">';
   
   //  div is just the max width of the window
   str += '<div id=\"' + tempdivid + '_window\" style=\"';
   str += 'position:relative;';
   str += 'overflow:hidden;';
   if(!Boolean(jsfpb_page.rows[r].ext) && Boolean(jsfpb_page.rows[r].img) && Boolean(jsfpb_page.rows[r].tile)) str += 'background-image:URL(' + jsfpb_page.rows[r].img + ');background-repeat:repeat;';
   else if(!Boolean(jsfpb_page.rows[r].ext) && Boolean(jsfpb_page.rows[r].img) && Boolean(jsfpb_page.rows[r].ancimg)) str += 'background-image:URL(' + jsfpb_page.rows[r].img + ');background-size:cover;background-position:bottom;';
   else if(!Boolean(jsfpb_page.rows[r].ext) && Boolean(jsfpb_page.rows[r].img)) str += 'background-image:URL(' + jsfpb_page.rows[r].img + ');background-size:cover;';
   if(!Boolean(jsfpb_page.rows[r].ext) && Boolean(jsfpb_page.rows[r].bg)) str += 'background-color:' + jsfpb_page.rows[r].bg + ';';
   str += 'width:' + effectivewd + 'px;';
   str += 'left:' + effectivelf + 'px;';   
   str += '\">';

   // div to contain the contents of the widget(s)
   str += '<div id=\"' + tempdivid + '\" style=\"';
   str += 'z-index:1;';
   str += 'position:relative;';
   str += 'width:' + divwidth + 'px;';
   str += '\">';
   
   var i=0;
   while(i<total) {
      // only continue if this was meant for the target platform
      //if(r==4) alert('row ' + r + ', slot' + i + ' here: ' + jsfpb_page.rows[r].slots[i].type);
      //if(r==1 && Boolean(jsfpb_page.rows[r].slots[i].type)) alert('row ' + r + ', slot' + i + ' has a type');
      //else if(r==1 && !Boolean(jsfpb_page.rows[r].slots[i].type)) alert('row ' + r + ', slot' + i + ' does not have a type');
      if (!Boolean(jsfpb_page.rows[r].slots[i].type) || jsfpb_page.rows[r].slots[i].type.indexOf(dtype)!== -1) {
         //if(r==4) alert('made it r: ' + r + ' s: ' + i);
         var slotwd = jsfpb_page.rows[r].slots[i].wd;
         if(row.type.toLowerCase().substr(0,8)=='carousel') slotwd = 100;
         
         if(dtype=='browser') {
            // desktop width: relatively large browser, make it wide
            var wd = Math.floor((slotwd/100)*effectivewd);
            str += jsfpb_getSlotHTML(jsfpb_page,r,i,wd,tempdivid,trackid,userinfo);
         } else if(dtype=='tablet') {
            //if the next 2 are 20 or 25%, we can group them in tablet only
            if(i<(total - 1) && slotwd==jsfpb_page.rows[r].slots[(i+1)].wd && (slotwd=='25' || slotwd=='20' || slotwd=='15') && (!Boolean(jsfpb_page.rows[r].slots[(i+1)].type) || jsfpb_page.rows[r].slots[(i+1)].type.indexOf('tablet') !== -1)) {
               var wd = Math.floor(effectivewd/2);
               str += jsfpb_getSlotHTML(jsfpb_page,r,i,wd,tempdivid,trackid,userinfo);
               i = i+1;
               str += jsfpb_getSlotHTML(jsfpb_page,r,i,wd,tempdivid,trackid,userinfo);
            } else {
               str += jsfpb_getSlotHTML(jsfpb_page,r,i,effectivewd,tempdivid,trackid,userinfo);
            }         
         } else {
            // mobile: no matter how wide, always make them as wide as the screen
            str += jsfpb_getSlotHTML(jsfpb_page,r,i,effectivewd,tempdivid,trackid,userinfo);
         }
      }
      i = i + 1;
   }
   
   if(row.type=='carousel') {
      str += '<div style=\"position:absolute;bottom:0px;left:0px;z-index:200;\">';
      str += '<div style=\"position:relative;left:0px;height:35px;width:' + (total * effectivewd) + 'px;overflow:hidden;background-color:#333333;opacity:0.85;\">';
      for(var i=0; i<total; i++) {
         str += '<div style=\"position:absolute;bottom:0px;left:' + (i * effectivewd) + 'px;\">';
         str += '<div style=\"position:relative;height:35px;width:' + effectivewd + 'px;\">';
         str += '<div style=\"position:absolute;bottom:10px;left:10px;font-size:12px;font-family:arial;color:#FFFFFF;\">';
         if(Boolean(row.txt)) str += row.txt;
         str += '</div>';
         str += '<div style=\"position:absolute;bottom:10px;right:10px;font-size:10px;font-family:arial;\">';
         if(i>0) str += '<span onclick=\"jsfpb_movecarousel(\'' + tempdivid + '\',' + r + ',' + ((i-1) * effectivewd * -1) + ');\" style=\"margin-right:8px;color:#FFFFFF;cursor:pointer;\">&lt; prev</span>';
         else str += '<span style=\"margin-right:8px;color:#CCCCCC;\">&lt; prev</span>';
         str += '<span style=\"color:#CCCCCC;\">' + (i + 1) + ' of ' + total + '</span>';
         if(i<(total-1)) str += '<span onclick=\"jsfpb_movecarousel(\'' + tempdivid + '\',' + r + ',' + ((i+1) * effectivewd * -1) + ');\" style=\"margin-left:8px;color:#FFFFFF;cursor:pointer;\">next &gt;</span>';
         else str += '<span style=\"margin-left:8px;color:#CCCCCC;\">next &gt;</span>';
         str += '</div>';
         str += '</div>';
         str += '</div>';
      }
      str += '</div>';
      str += '</div>';
      
      // Ends main div set above
      str += '<div style=\"clear:both;\"></div>';
   } else if(row.type=='carousel2') {
      // Ends main div set above
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      //alert('here');
      
      str += '\n<scr';
      str += 'ipt>\n';
      str += 'var jsfpb_crpg' + r + '=1;\n';
      str += '</scr';
      str += 'ipt>\n';
      
      // First page turner.  Use id to hide.
      str += '<div id=\"\" style=\"z-index:2;position:absolute;bottom:0px;left:0px;width:' + effectivewd + 'px;height:40px;overflow:hidden;background-color:#ffffff;opacity:0.8;\">';
      str += '<div style=\"position:relative;left:0px;width:' + effectivewd + 'px;height:40px;\">';
         str += '<div id=\"jsfpb_crleftarrow' + r + '\" style=\"display:none;position:absolute;top:5px;left:5x;width:17px;height:30px;z-index:2;cursor:pointer;\" onclick=\"jsfpb_crpg' + r + '--;jsfpb_movecarousel(\'' + tempdivid + '\',' + r + ',(' + effectivewd + ' * -1 * (jsfpb_crpg' + r + ' - 1)));if(jsfpb_crpg' + r + '==1) jQuery(\'#jsfpb_crleftarrow' + r + '\').hide();jQuery(\'#jsfpb_crrightarrow' + r + '\').show();\">';
         str += jsfpb_drawarrow('#334357','#ffffff','left',30,17,7);
         str += '</div>';
         
         str += '<div id=\"jsfpb_crrightarrow' + r + '\" style=\"position:absolute;top:5px;right:5px;width:17px;height:30px;z-index:2;cursor:pointer;\" onclick=\"jsfpb_movecarousel(\'' + tempdivid + '\',' + r + ',(' + effectivewd + ' * -1 * jsfpb_crpg' + r + '));jsfpb_crpg' + r + '++;if(jsfpb_crpg' + r + '==' + total + ') jQuery(\'#jsfpb_crrightarrow' + r + '\').hide();jQuery(\'#jsfpb_crleftarrow' + r + '\').show();\">';
         str += jsfpb_drawarrow('#334357','#ffffff','right',30,17,7);
         str += '</div>';
         
         
         //decoys
         str += '<div style=\"position:absolute;top:5px;left:5x;width:17px;height:30px;z-index:1;\">';
         str += jsfpb_drawarrow('#BBBBBB','#ffffff','left',30,17,7);
         str += '</div>';
         
         str += '<div style=\"position:absolute;top:5px;right:5px;width:17px;height:30px;z-index:1;\">';
         str += jsfpb_drawarrow('#BBBBBB','#ffffff','right',30,17,7);
         str += '</div>';
         
      str += '</div>';
         
   } else {
      // Ends main div set above
      str += '<div style=\"clear:both;\"></div>';
   }
   
   str += '</div>';
   str += '</div>';
   str += '</div>';
   
   //End custom name (to hide/show)
   str += '</div>';
   
   return str;
}

function jsfpb_movecarousel(divid,r,position) {
   //jQuery('#carousel_rowbottom_' + r).css('left', position + 'px');
   //jQuery('#carousel_row_' + r).animate({left: position + 'px'},900);
   jQuery('#' + divid).animate({left: position + 'px'},900);
}

function jsfpb_getSlotHTML(jsfpb_page,r,s,wd,divid,trackid,userinfo){
   //if(r==1) alert('page ' + r + ', ' + s + ', ' + wd);
   var tempdivid = divid + '_s' + s;   
   var str = '';
   
   str += '<div style=\"position:relative;float:left;z-index:' + (100 - r) + ';';
   str += 'width:' + wd + 'px;';
   //if(r==1 && s==2) str += 'background-color:green;';
   if(Boolean(jsfpb_page.rows[r].vht) && jsfpb_page.rows[r].vht > 10) {
      var tempht = jsfpb_page.rows[r].vht;
      
      if(Boolean(jsfpb_page.rows[r].htty) && jsfpb_page.rows[r].htty=='pct') {
         tempht = Math.round( (tempht/100) * jQuery(window).height());
      }
      if(tempht>10) str += 'height:' + tempht + 'px;';
   } else if(jsfpb_page.rows[r].htty!='NA') {
      str += 'min-height:10px;';
   }
   //***chj*** str += 'overflow:hidden;';
   str += '\">';
   var icount = 0;
   for(var i=0;i<jsfpb_page.rows[r].slots[s].layers.length;i++) {
      if(!Boolean(jsfpb_page.rows[r].slots[s].layers[i].hide)){
         var pos = 'absolute';
         if(icount==0) pos = 'relative';
         str += jsfpb_getLayerHTML(jsfpb_page,r,s,i,wd,pos,tempdivid,trackid,userinfo);
         icount++;
      }
   }
   
   str += '<div onclick=\"jQuery(\'#jsfpb_fullpage\').hide();jsfpb_displayRowInput(' + r + ');jsfpb_displaySlotInput(' + r + ',' + s + ');\" id=\"' + tempdivid + '_edit\" style=\"position:absolute;left:2px;top:2px;padding:2px;border:1px solid #AAAAAA;background-color:#FFFFFF;display:none;font-size:8px;font-family:arial;color:#000000;cursor:pointer;\">&lt; &gt;</div>';
   
   str += '</div>';
   //str += '<div style=\"clear:both;\"></div>';
   return str;
}



function jsfpb_getLayerHTML(jsfpb_page,r,s,l,w,position,divid,trackid,userinfo){
   jsfpb_page.rows[r].slots[s].layers[l].vcontent = jsfpb_page.rows[r].slots[s].layers[l].content;
   var type = jsfpb_page.rows[r].slots[s].layers[l].type;
   var tempdivid = divid + '_l' + l;
   
   var shortname;
   if(!Boolean(position)) position = 'absolute';
   
   var left = 0;
   if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].left)) left = Math.round((parseInt(jsfpb_page.rows[r].slots[s].layers[l].left) / 100 )* w);
   var width = w;
   if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].wd)) width = Math.round((parseFloat(jsfpb_page.rows[r].slots[s].layers[l].wd) / 100 )* w);
   if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].pad)) width = width - (2*jsfpb_page.rows[r].slots[s].layers[l].pad);
   
   var top = 0;
   
   var height;
   if(Boolean(jsfpb_page.rows[r].vht) && jsfpb_page.rows[r].vht > 10) {
      height = jsfpb_page.rows[r].vht;
      
      
      if(Boolean(jsfpb_page.rows[r].htty) && jsfpb_page.rows[r].htty=='pct') {
         height = Math.round( (height/100) * jQuery(window).height());
      }
      
      
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].top)) {
         top = Math.round((parseInt(jsfpb_page.rows[r].slots[s].layers[l].top) / 100 )* height);
         height = height - top; 
      }
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].pad)) height = height - (2*jsfpb_page.rows[r].slots[s].layers[l].pad);
   } else if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].top)) {
      top = Math.round((parseInt(jsfpb_page.rows[r].slots[s].layers[l].top) / 100 )* jQuery(window).height());
      //position = 'relative';
      
      // vertically align if formatted block only
      if(type=='Formatted Block') {
         var tempobj = {};
         
         // get row div id
         var sepdiv = divid.split('_');
         var tdiv = "";
         for(var i=0;i<(sepdiv.length - 1);i++) {
            if(i>0) tdiv += '_';
            tdiv += sepdiv[i];  
         }
         
         tempobj.outerdiv = tdiv;
         tempobj.innerdiv = tempdivid + '_outer';
         //tempobj.innerdiv = divid + '_l0_layercontent';         
         tempobj.top = jsfpb_page.rows[r].slots[s].layers[l].top;
         top = jsfpb_page.rows[r].slots[s].layers[l].top;
         jsfpb_verticalcenterdivs.push(tempobj);
      }
   }
   
   // After everything is done, make sure we're within the maximum size
   if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].max) && width>jsfpb_page.rows[r].slots[s].layers[l].max) {
      var diff = width - jsfpb_page.rows[r].slots[s].layers[l].max;
      width = jsfpb_page.rows[r].slots[s].layers[l].max;
      left = left + Math.round(diff/2);
   }
   
   var str = '';
   str += '<div ';
   str += 'style=\"position:' + position + ';';
   str += 'left:' + left + 'px;';
   str += 'top:' + top + 'px;';
   str += 'z-index:' + l + ';';
   str += '\" ';
   str += 'id=\"' + tempdivid + '_outer\" ';
   str += '>';
   
   str += '<div ';
   str += ' id=\"' + tempdivid + '\"';
   str += ' style=\"position:relative;';
   //str += 'width:' + width + 'px;overflow:hidden;';
   if(Boolean(height)) str += 'height:' + height + 'px;';
   if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].pad)) str += 'padding:' + jsfpb_page.rows[r].slots[s].layers[l].pad + 'px;';
   if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].clr)) str += 'color:' + jsfpb_page.rows[r].slots[s].layers[l].clr + ';';
   if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].fsz)) str += 'font-size:' + jsfpb_page.rows[r].slots[s].layers[l].fsz + 'px;';
   if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].ffm)) str += 'font-family:' + jsfpb_page.rows[r].slots[s].layers[l].ffm + ';';
   if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].aln)) str += 'text-align:center;';
   if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].bld)) str += 'font-weight:bold;';
   
   var trx = jsfpb_page.rows[r].slots[s].layers[l].trx;
   if(Boolean(trx) && trx!='100') {
      str += 'opacity:0.';
      if(trx.length<2) trx += '0';
      str += trx + ';';
   }
      
   str += '\">';
   str += '<div style=\"position:relative;';
   //str += 'width:' + width + 'px;overflow:hidden;';
   str += 'width:' + width + 'px;';
   if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].bg)) str += 'background-color:' + jsfpb_page.rows[r].slots[s].layers[l].bg + ';';
   if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].doc)) str += 'cursor:pointer;\" onclick=\"window.open(\'' + jsfpb_page.rows[r].slots[s].layers[l].doc + '\');';
   else if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].url)) str += 'cursor:pointer;\" onclick=\"window.open(\'' + jsfpb_page.rows[r].slots[s].layers[l].url + '\');';
   else if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].onclick)) str += 'cursor:pointer;\" onclick=\"' + jsfpb_convertback(jsfpb_page.rows[r].slots[s].layers[l].onclick) + ';';
   str += '\" ';
   str += ' id=\"' + tempdivid + '_layercontent\" ';
   str += '>';

   var tempheight = jQuery(window).height() - jsfpb_headerheight - jsfpb_footerheight;
   var tempfullheight = jQuery(window).height();
   if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].vcontent)) {
      jsfpb_page.rows[r].slots[s].layers[l].vcontent = jsfpb_replaceAll('%%%WIDTH%%%',width,jsfpb_page.rows[r].slots[s].layers[l].vcontent);
      jsfpb_page.rows[r].slots[s].layers[l].vcontent = jsfpb_replaceAll('%%%HEIGHT%%%',tempheight,jsfpb_page.rows[r].slots[s].layers[l].vcontent);
      jsfpb_page.rows[r].slots[s].layers[l].vcontent = jsfpb_replaceAll('%%%FULLHEIGHT%%%',tempfullheight,jsfpb_page.rows[r].slots[s].layers[l].vcontent);
   }
   
   if(type=='Text') {
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].vcontent)) {
         str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].vcontent);
      } else if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].bg) && l>0) {
         var tempobj = {};
         tempobj.divid = tempdivid + '_layercontent';
         tempobj.dividfrom = divid + '_l' + (l - 1) + '_layercontent';
         jsfpb_resizedivs.push(tempobj);         
      }
   } else if(type=='HTML') {
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].vcontent)) str += jsfpb_convertback(jsfpb_page.rows[r].slots[s].layers[l].vcontent);
   } else if(type=='Content') {
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].vcontent)) {
         var tempobj = {};
         tempobj.shortname = jsfpb_page.rows[r].slots[s].layers[l].vcontent;
         tempobj.divid = tempdivid;
         tempobj.width = width;
         jsfpb_contenttocall.push(tempobj);
      }
   } else if(type=='Page Import') {
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].vcontent)) {
         var tempobj = {};
         tempobj.page = jsfpb_page.rows[r].slots[s].layers[l].vcontent;
         tempobj.divid = tempdivid;
         tempobj.width = width;
         jsfpb_pagestocall.push(tempobj);
      }
   } else if(type=='Visual Builder') {
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].vcontent)) {
         //var v_height = width;
         //if(Boolean(height)) v_height = height;
         //else if(jQuery(window).height() < width) v_height = jQuery(window).height();
         
         str += '<div id=\"' + tempdivid + '_v\" ';
         str += 'style=\"position:relative;width:' + width + 'px;';
         if(Boolean(height)) str += 'height:' + height + 'px;';
         str += '\"></div>';
         
         var tempobj = {};
         tempobj.name = jsfpb_page.rows[r].slots[s].layers[l].vcontent;
         tempobj.divid = tempdivid + '_v';
         //alert('adding vb to queue: ' + JSON.stringify(tempobj));
         jsfpb_vbstocall.push(tempobj);
      }
   } else if(type=='Image') {
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].img)) {
         str += '<img src=\"' + jsfpb_replaceAll('http:','https:',jsfpb_page.rows[r].slots[s].layers[l].img) + '\" style=\"';
         if(jsfpb_page.rows[r].slots[s].layers[l].imgdsp == 'stretch') {
            var usingheight = tempheight;
            if(Boolean(height)) usingheight = height;
            if(width>(1.3*usingheight)) {
               str += 'min-height:' + usingheight + 'px;width:' + width + 'px;height:auto;';
            } else {
               str += 'min-width:' + width + 'px;height:' + usingheight + 'px;width:auto;';
            }
         } else if(Boolean(height)){
            str += 'max-width:' + width + 'px;max-height:' + height + 'px;width:auto;height:auto;';
         } else {
            str += 'max-width:' + width + 'px;width:auto;height:auto;';
         }
         str += '\">';
         if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].vcontent)) {
            str += '<div style=\"margin-top:6px;\">';
            str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].vcontent);
            str += '</div>';
         }
      }
   } else if(type=='Formatted Block') {
      var ftsz = 28;
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].fsz)) ftsz = jsfpb_page.rows[r].slots[s].layers[l].fsz;
      
      /*
      if(width<500) ftsz = ftsz - Math.round(ftsz * 1/2);
      else if(width<740) ftsz = ftsz - Math.round(ftsz * 2/7);
      */
      
      var title1used=false;
      var title2used=false;
      
      str += '<div id=\"' + tempdivid + '_ft\" style=\"position:relative;';
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].mpad)) {
         str += 'padding:' + jsfpb_page.rows[r].slots[s].layers[l].mpad + 'px;';
         width = width - (2 * parseInt(jsfpb_page.rows[r].slots[s].layers[l].mpad));
      }
      str += '\">';
      
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].mttl)) {
         str += '<div style=\"';
         str += 'position:relative;';
         str += 'font-size:' + ftsz + 'px;';
         str += 'font-weight:bold;';
         str += 'margin-bottom:' + Math.round(ftsz * 0.75) + 'px;';
         if(jsfpb_page.rows[r].slots[s].layers[l].mstyle=='standard') str += 'min-height:60px;';
         str += '\">';
         
         if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].dep) && jsfpb_page.rows[r].slots[s].layers[l].dep=='small shadow') {
            str += '<div style=\"position:absolute;left:1px;top:1px;width:' + width + 'px;z-index:-1;color:#000000;opacity:0.8;\">';
            str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].mttl);
            str += '</div>';
         } else if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].dep) && jsfpb_page.rows[r].slots[s].layers[l].dep=='large shadow') {
            str += '<div style=\"position:absolute;left:3px;top:3px;width:' + width + 'px;z-index:-1;color:#000000;opacity:0.8;\">';
            str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].mttl);
            str += '</div>';
         }

         str += '<div style=\"z-index:10;\">';
         str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].mttl);
         str += '</div>';
         
         str += '</div>';
         ftsz = Math.round(ftsz * 0.6);
         title1used = true;
      }

      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].msubttl)) {
         str += '<div style=\"position:relative;';
         str += 'font-size:' + ftsz + 'px;';
         str += 'margin-bottom:' + Math.round(ftsz * 0.9) + 'px;';
         if(jsfpb_page.rows[r].slots[s].layers[l].mstyle=='standard') str += 'min-height:50px;';
         str += '\">';
         str += '<div style=\"z-index:10;\">';
         str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].msubttl);
         str += '</div>';
         if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].dep) && jsfpb_page.rows[r].slots[s].layers[l].dep=='small shadow') {
            str += '<div style=\"position:absolute;left:1px;top:1px;width:' + width + 'px;z-index:-1;color:#000000;opacity:0.8;\">';
            str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].msubttl);
            str += '</div>';
         } else if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].dep) && jsfpb_page.rows[r].slots[s].layers[l].dep=='large shadow') {
            str += '<div style=\"position:absolute;left:3px;top:3px;width:' + width + 'px;z-index:-1;color:#000000;opacity:0.8;\">';
            str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].msubttl);
            str += '</div>';
         }
         str += '</div>';
         ftsz = Math.round(ftsz * 0.75);         
         title2used = true;
      }

      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].vcontent)) {
         str += '<div style=\"';
         str += 'font-size:' + ftsz + 'px;';
         str += 'font-weight:normal;';
         str += 'margin-bottom:' + Math.round(ftsz * 1.5) + 'px;';
         if(jsfpb_page.rows[r].slots[s].layers[l].mstyle=='standard') str += 'min-height:100px;';
         str += '\">';
         //str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].content);
         str += jsfpb_convertback(jsfpb_page.rows[r].slots[s].layers[l].vcontent);
         str += '</div>';
      }
      
      var btnwd = 230;
      if((width-40) < btnwd) btnwd = width - 40;

      var left = Math.round((width - (btnwd + 2))/2);
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].mside) && jsfpb_page.rows[r].slots[s].layers[l].mside.toLowerCase()=='left') left = 0;
      else if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].mside) && jsfpb_page.rows[r].slots[s].layers[l].mside.toLowerCase()=='right') left = (width - btnwd);
      
      str += '<div style=\"width:' + (btnwd + 2) + 'px;margin-left:' + left + 'px;\">';
      for(var i=1;i<=5;i++) {
         if(Boolean(jsfpb_page.rows[r].slots[s].layers[l]['mbtn' + i])) {
            var bg = '#9bada5';
            var bg2 = '#74867e';
            var fg = '#ffffff';
            var oc = '';
            if(Boolean(jsfpb_page.rows[r].slots[s].layers[l]['murl' + i])) {
               oc = jsfpb_convertback(jsfpb_page.rows[r].slots[s].layers[l]['murl' + i]);
               if(oc.substr(0,4)=='http') oc = 'window.open(\'' + oc + '\');';
            }
            if(Boolean(jsfpb_page.rows[r].slots[s].layers[l]['mbg' + i])) {
               bg = jsfpb_page.rows[r].slots[s].layers[l]['mbg' + i];
               if(Boolean(jsfpb_page.rows[r].slots[s].layers[l]['mhg' + i])) bg2 = jsfpb_page.rows[r].slots[s].layers[l]['mhg' + i];
               else bg2 = bg;
            }
            if(Boolean(jsfpb_page.rows[r].slots[s].layers[l]['mfg' + i])) fg = jsfpb_page.rows[r].slots[s].layers[l]['mfg' + i];
            
            var tempid = 'mttbtn' + i + '_' + r + '_' + s + '_' + l + '_' + jsfpb_replaceAll(':', '', jsfpb_replaceAll(' ', '', jsfpb_page.name));
            str += '<div style=\"margin-top:5px;margin-bottom:10px;padding:10px;text-align:center;';
            //str += 'border:1px solid ' + fg + ';';
            str += 'background-color:' + bg + ';';
            str += 'color:' + fg + ';';
            str += 'font-size:' + ftsz + 'px;';
            str += 'font-weight:normal;';
            str += 'border-radius:4px;cursor:pointer;\" ';
            str += 'class=\"jsfpb_button\" ';
            str += 'id=\"' + tempid + '\" ';
            str += 'onmouseover=\"jQuery(\'#' + tempid + '\').css(\'background-color\',\'' + bg2 + '\');\" ';
            str += 'onmouseout=\"jQuery(\'#' + tempid + '\').css(\'background-color\',\'' + bg + '\');\" ';
            str += 'onclick=\"' + oc + '\">';
            str += jsfpb_page.rows[r].slots[s].layers[l]['mbtn' + i];
            str += '</div>';
         }
      }
      str += '</div>';
      
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].img)) {
         str += '<img src=\"' + jsfpb_replaceAll('http:','https:',jsfpb_page.rows[r].slots[s].layers[l].img) + '\" style=\"z-index:1;width:' + width + 'px;height:auto;margin-bottom:' + Math.round(ftsz * 0.7) + 'px;';
         if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].gry)) str += 'filter:grayscale(100%);';
         str += '\">';
      }
      

      str += '</div>';
      
   } else if(type=='Article Block') {
      var fsz = jsfpb_page.rows[r].slots[s].layers[l].fsz;
      if(!Boolean(fsz)) fsz = 14;
      var fsz2 = fsz - 2;
      
      str += '<div id=\"' + tempdivid + '_ft\" style=\"position:relative;';
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].mpad)) {
         str += 'padding:' + jsfpb_page.rows[r].slots[s].layers[l].mpad + 'px;';
         width = width - (2 * parseInt(jsfpb_page.rows[r].slots[s].layers[l].mpad));
      }
      str += '\">';
      
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].img)) {
         str += '<div style=\"';
         str += 'position:relative;';
         str += 'width:' + width + 'px;';
         str += 'height:' + (Math.round(width * 0.4)) + 'px;';
         str += 'overflow:hidden;';
         str += 'margin-bottom:10px;';
         str += '\">';
         str += '<img src=\"' + jsfpb_replaceAll('http:','https:',jsfpb_page.rows[r].slots[s].layers[l].img) + '\" style=\"z-index:1;max-width:' + width + 'px;min-height:' + (Math.round(width * 0.4)) + 'px;width:auto;height:auto;margin-bottom:10px;';
         if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].gry)) str += 'filter:grayscale(100%);';
         str += '\">';
         str += '</div>';
      }

      
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].mttl)) {
         str += '<div style=\"';
         str += 'position:relative;';
         str += 'font-size:' + fsz + 'px;';
         str += 'font-weight:bold;';
         str += 'margin-bottom:1px;';
         str += '\">';
         str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].mttl);
         str += '</div>';
      }
      
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].msubttl)) {
         str += '<div style=\"';
         str += 'position:relative;';
         str += 'margin-bottom:10px;';
         str += 'font-size:' + fsz2 + 'px;';
         str += 'font-style:italic;';
         str += 'opacity:0.8;';
         str += '\">';
         str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].msubttl);
         str += '</div>';
      }
      

      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].vcontent)) {
         str += '<div style=\"';
         str += 'font-weight:normal;';
         str += 'font-size:' + fsz + 'px;';
         str += 'margin-bottom:15px;';
         str += 'min-height:50px;';
         str += '\">';
         str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].vcontent);
         str += '</div>';
      }
      
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].mbtn1)) {
         var oc = '';
         if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].murl1)) {
            oc = jsfpb_convertback(jsfpb_page.rows[r].slots[s].layers[l].murl1);
            if(oc.substr(0,4)=='http') oc = 'window.open(\'' + oc + '\');';
         }
         str += '<div style=\"margin-top:5px;margin-bottom:10px;\">';
         str += '<span style=\"';
         str += 'padding:6px 10px 6px 10px;';
         str += 'font-size:' + fsz2 + 'px;';
         str += 'text-align:center;';
         str += 'font-weight:normal;';
         str += 'border:1px solid ' + jsfpb_page.rows[r].slots[s].layers[l].clr + ';';
         str += 'border-radius:2px;cursor:pointer;\" ';
         str += 'class=\"jsfpb_button\" ';
         str += 'onclick=\"' + oc + '\">';
         str += jsfpb_page.rows[r].slots[s].layers[l].mbtn1;
         str += '</span>';
         str += '</div>';
      }
      str += '</div>';
      
   } else if(type=='Download Block') {
      var twd = 250;
      if(twd>width) twd = width;
      var tht = Math.round(0.8 * twd);
      var topht = Math.round(tht * 0.6);
      
      var bg = '#f2f2f2';
      var fg = '#334357';
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].mbg1)) bg = jsfpb_page.rows[r].slots[s].layers[l].mbg1;
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].mfg1)) fg = jsfpb_page.rows[r].slots[s].layers[l].mfg1;
      
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].mttl)) {
         str += '<div style=\"margin-top:3px;margin-bottom:12px;\">';
         str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].mttl);
         str += '</div>';
         
      }
      
      str += '<div style=\"width:' + twd + 'px;height:' + tht + 'px;overflow:hidden;border-radius:4px;\">';
      
      str += '<div style=\"width:' + twd + 'px;height:' + topht + 'px;overflow:hidden;\">';
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].img)) {
         str += '<img src=\"' + jsfpb_replaceAll('http:','https:',jsfpb_page.rows[r].slots[s].layers[l].img) + '\" style=\"width:' + twd + 'px;height:auto;\">';
      }
      str += '</div>'
      str += '<div style=\"width:' + twd + 'px;height:5px;overflow:hidden;background-color:' + fg + ';\">';
      str += '</div>'
      str += '<div style=\"width:' + twd + 'px;height:' + (tht - topht - 5) + 'px;overflow:hidden;background-color:' + bg + ';\">';
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].vcontent)) {
         str += '<div style=\"padding:8px;color:' + fg + ';text-align:center;font-size:10px;\">';
         str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].vcontent);
         str += '</div>'
      }
      str += '<div style=\"margin-left:' + (Math.round((twd - 26)/2)) + 'px;margin-top:2px;width:26px;\">';
      str += jsfpb_drawdownloadicon(fg,bg);
      str += '</div>'
      
      str += '</div>'
      str += '</div>'
      
      
   } else {
      // Try a custom widget
      if(typeof jsfpb_getCustomLayerHTML == "function") str += jsfpb_getCustomLayerHTML(type,r,s,l,width,height,jsfpb_page,trackid,userinfo);
   }
   
   str += '</div>';
   str += '</div>';
   str += '</div>';
   
   //jQuery('#emptyspace').html(str);
   //var h = jQuery('#' + divid + '_display').outerHeight();
   return str;
}






function jsfpb_convertstring(str){
   var temp = '';
   if(Boolean(str)) {
      // Remove any non-ascii character
      temp = str.replace(/[^\x00-\x7F]/g, "");
      
      // Convert special characters for javascript
      temp = jsfpb_replaceAll('\"','#jsfquote#',temp);
      temp = jsfpb_replaceAll('&#34;','#jsfquote#',temp);
      temp = jsfpb_replaceAll('\'','#jsfsquote#',temp);
      temp = jsfpb_replaceAll('&#39;','#jsfsquote#',temp);
      temp = jsfpb_replaceAll('\r','#jsfcr#',temp);
      temp = jsfpb_replaceAll('\n','#jsflf#',temp);
      temp = jsfpb_replaceAll('&bull;','#jsfbullet#',temp);
   }
   return temp;
}

function jsfpb_convertback(str){
   var temp = jsfpb_replaceAll('#jsfquote#','\"',str);
   temp = jsfpb_replaceAll('#jsfsquote#','\'',temp);
   temp = jsfpb_replaceAll('#jsflf#','\n',temp);
   temp = jsfpb_replaceAll('#jsfcr#','\r',temp);
   temp = jsfpb_replaceAll('#jsfbullet#','&bull;',temp);
   return temp;
}

function jsfpb_convertbackinput(str){
   var temp = jsfpb_replaceAll('#jsfquote#','\"',str);
   temp = jsfpb_replaceAll('#jsfsquote#','\'',temp);
   temp = jsfpb_replaceAll('#jsflf#','\n',temp);
   temp = jsfpb_replaceAll('#jsfcr#','\r',temp);
   //temp = jsfpb_replaceAll('#jsfbullet#','&bull;',temp);
   return temp;
}

function jsfpb_convertdisplay(str){
   var temp = '';
   if(Boolean(str)) {   
      temp = jsfpb_replaceAll('#jsfquote#','\"',str);
      temp = jsfpb_replaceAll('#jsfsquote#','\'',temp);
      temp = jsfpb_replaceAll('#jsflf#','<br>',temp);
      temp = jsfpb_replaceAll('#jsfcr#','',temp);
      temp = jsfpb_replaceAll('#jsfbullet#','&bull;',temp);
      
      //Automatic link and email replacement...
       var replacePattern1, replacePattern2, replacePattern3;
   
       //URLs starting with http://, https://, or ftp://
       replacePattern1 = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
       temp = temp.replace(replacePattern1, '<a href="$1" target="_blank">$1</a>');
   
       //URLs starting with "www." (without // before it, or it'd re-link the ones done above).
       replacePattern2 = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
       temp = temp.replace(replacePattern2, '$1<a href="https://$2" target="_blank">$2</a>');
   
       //Change email addresses to mailto:: links.
       replacePattern3 = /(([a-zA-Z0-9\-\_\.])+@[a-zA-Z\_]+?(\.[a-zA-Z]{2,6})+)/gim;
       temp = temp.replace(replacePattern3, '<a href="mailto:$1">$1</a>');
   }
   
   return temp;
}





//------------ IN PROGRESS... ----------------------//




function jsfpb_QuickJSON(action,callback,query,checkcache){
   var runjson = true;
   if (Boolean(action) && Boolean(callback)) {
      var url = jsfpb_domain + jsfpb_servercontroller + '&action=' + encodeURIComponent(action);
      if (Boolean(query)) url = url + query;
      
      var saveurl = url;
      url = url + '&callback=' + encodeURIComponent(callback);
      
      //alert('jsfpagebuilder JSON request URL: ' + url);
      
      if(Boolean(checkcache)) {
         //alert('checking cache: ' + url);
         var str = window.localStorage.getItem('jsfpb_cache');
         if(Boolean(str)){
            //alert('found cache: ' + url);
            var jsf_cache = JSON.parse(str);
            if(jsf_cache.expiry<(Math.floor(Date.now() / 1000))) {
               //alert('expired cache: ' + url);
               jsf_cache = '';
               window.localStorage.removeItem('jsfpb_cache');
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
         jsfpb_CallJSONP(url);
      }
   }   
   
}

function jsfpb_ReturnJSON(jsondata){
   //alert('jsfpb return json: ' + JSON.stringify(jsondata));
   if (Boolean(jsondata) && Boolean(jsondata.jsonsaveval)) {
      //alert('CHJ***** checking cache: jsf_endjsoning  url: ' + jsondata.jsonsaveval);
      var jsf_cache = {};
      jsf_cache.expiry = (Math.floor(Date.now() / 1000) + (60*60*24));
      jsf_cache.countindex = 1;
      var str = window.localStorage.getItem('jsfpb_cache');
      window.localStorage.removeItem('jsfpb_cache');
      if(Boolean(str)) {
         //alert('found jsf_cache, checking expiry...');
         temp = JSON.parse(str);
         if(Boolean(temp) && temp.expiry>(Math.floor(Date.now() / 1000)) && temp.countindex<150) {
            jsf_cache = temp;
         }
      }
      if(!Boolean(jsf_cache[jsondata.jsonsaveval])) jsf_cache.countindex++;
      jsf_cache[jsondata.jsonsaveval] = jsondata;
      //alert('about to set cache: ' + JSON.stringify(jsf_cache));
      window.localStorage.setItem('jsfpb_cache',JSON.stringify(jsf_cache));
   }
}


function jsfpb_donothing(jsondata){
   jsfpb_ReturnJSON(jsondata);
   //alert(JSON.stringify(jsondata));
}

function jsfpb_trackitem(foraction,str1,str2,trackid){
   var view = 'JSFPageBuilder';
   if(Boolean(trackid)) view = trackid;
   
   if (typeof pmrm_showHomePage !== 'undefined' && typeof pmrm_showHomePage === 'function') view='WRAPRoadmap';
   else if (typeof pmcs_showHomePage !== 'undefined' && typeof pmcs_showHomePage === 'function') view='VCCS';
   
   var str3 = location.hostname;
   if(str3.substr(0,4)=='www.') str3 = str3.substr(4);
   
   var url = '';
   url = url + '&view=' + encodeURIComponent(view);
   if (Boolean(foraction)) url = url + '&foraction=' + encodeURIComponent(foraction);
   if (Boolean(str1)) url = url + '&jsftrack1=' + encodeURIComponent(str1);
   if (Boolean(str2)) url = url + '&jsftrack2=' + encodeURIComponent(str2);
   if (Boolean(str3)) url = url + '&jsftrack3=' + encodeURIComponent(str3);
   //if (Boolean(globaluser)) url = url + '&userid=' + globaluser.userid;
   url = url + '&callback=jsfpb_donothing';
   url = url + '&referer=' + encodeURIComponent(document.referrer);

   //alert('url: ' + url);
   jsfpb_QuickJSON('trackitem','jsfpb_donothing',url);
}




function jsfpb_CallJSONP(url) {
    //var script = document.createElement('script');
    //script.setAttribute('src', url);
    //script.setAttribute('type', 'application/json');
    //document.getElementsByTagName('head')[0].appendChild(script);
    
    var script = '<script src=\"' + url + '\"></script>';
    jQuery('head').append(script);
}




function jsfpb_getcontent_jsonp(shortname,divid){
   if(!Boolean(divid)) divid = 'jsfcontent';
   if (Boolean(shortname)) {
      var jsondata;
      var str = window.localStorage.getItem(divid + '_' + shortname);
      if(Boolean(str)){
         //alert('found cache: ' + url);
         jsondata = JSON.parse(str);
         if(jsondata.expiry<(Math.floor(Date.now() / 1000))) {
            jsondata = '';
            window.localStorage.removeItem(divid + '_' + shortname);
         }
      }
         
      if (Boolean(jsondata)) {
         jsfpb_returncontent(jsondata,true);
      } else {
         var query = '&shortname=' + encodeURIComponent(shortname);
         query += '&divid=' + encodeURIComponent(divid);
         jsfpb_QuickJSON('getcmscontent','jsfpb_returncontent',query);
      }
   }
}

function jsfpb_returncontent(jsondata,olddata){
   if(Boolean(jsondata.vcontent)){
      if(!Boolean(jsondata.divid)) jsondata.divid='jsfcontent';
      jQuery('#' + jsondata.divid).html(jsondata.vcontent);
      if(!Boolean(olddata)) {
         jsondata.expiry = (Math.floor(Date.now() / 1000) + (60*60*24));
         window.localStorage.setItem(jsondata.divid + '_' + jsondata.shortname,JSON.stringify(jsondata));
      }
   }
}



// function for creating search parameters of a jdata table
function jsfpb_shorterwdname(wdname) {
   //alert('wdname: ' + wdname);
   wdname = jsfpb_replaceAll(' ', '', wdname);
   return wdname.toLowerCase();
}

function jsfpb_getwebdata_jsonp(wdname,callback,params,checkcache){
   var query = '';
   if (Boolean(wdname)) query += '&wdname=' + encodeURIComponent(wdname);
   if (Boolean(params)) query += params;
   jsfpb_QuickJSON('getwdandrows',callback,query,checkcache)   
}

function jsfpb_drawvisualcomponents(divid,name){
   //alert('jsfpb_drawvisualcomponents(' + divid + ',' + name + ')');
   if(!Boolean(jsfpb_wdname) && Boolean(jsfpb_tablename)) jsfpb_wdname = jsfpb_tablename;
   var callback = 'jsfpb_drawvisualcomponents_return';
   var params ='';
   params += '&cmsenabled=1';
   params += '&maxcol=8';
   params += '&cmsq_' + jsfpb_shorterwdname(jsfpb_wdname) + '_name=' + encodeURIComponent('Visual: ' + name);
   params += '&divid=' + encodeURIComponent(divid);
   //jsfpb_getwebdata_jsonp(jsfpb_wdname,callback,params,true);
   jsfpb_drawvisualcomponents_sync(jsfpb_wdname,callback,params);
}

var jsfpb_drawvisualcomponents_busy = false;
//var jsfpb_drawvisualcomponents_re;
function jsfpb_drawvisualcomponents_sync(wdname,callback,params) {
   if(!jsfpb_drawvisualcomponents_busy) {
      jsfpb_drawvisualcomponents_busy = true;
      //jsfpb_drawvisualcomponents_re = params;
      //alert('entering zone: ' + jsfpb_drawvisualcomponents_re);
      jsfpb_getwebdata_jsonp(jsfpb_wdname,callback,params,true);
   } else {
      setTimeout(jsfpb_drawvisualcomponents_sync,500,wdname,callback,params);
   }
}

var jsfpb_visualformelements;
var jsfpb_visualformlists;
var jsfpb_jdatacustomlists;
var jsfpb_jdatacustomlists_rows;
var jsfpb_visualforms;
var jsfpb_visualjdata;
var jsfpb_visualjdatagetrows;
//var jsfpb_visualjdatarows = {};
var jsfpb_additionalvisualcomponents = [];
function jsfpb_drawvisualcomponents_return(jsondata) {
   //if(jsfpb_devmode) alert('jsfpb_drawvisualcomponents_return divide: ' + jsondata.divid);
   //if(jsfpb_devmode) alert('jsfpb_drawvisualcomponents_return beginning # of additional callbacks: ' + jsfpb_additionalvisualcomponents.length);
   //alert('user: ' + JSON.stringify(jsfcore_globaluser));
   //if(jsfpb_devmode) alert('displaying ' + jsondata.rows.length + ' records: ' + JSON.stringify(jsondata.rows));
   jsfpb_ReturnJSON(jsondata);
   var str = '';
   
   // create a list of jdata fields to display
   jsfpb_visualformelements = [];
   jsfpb_visualformlists = [];
   jsfpb_jdatacustomlists = [];
   jsfpb_visualforms = [];
   jsfpb_visualjdata = {};
   jsfpb_visualjdatagetrows = {};
   
   if(Boolean(jsondata) && Boolean(jsondata.rows) && jsondata.rows.length>0) {
      // Calculate the left and top of total area
      var minleft = 10000000;
      var maxright = 0;
      var mintop = 10000000;
      var maxbot = 0;
      var oright;
      var fixdim = false;
      var variableht = false;
      // child top + parent top
      var parenttops = {};
      var rmnrows = jsondata.rows;
      var dimcounter = 0;
      while(dimcounter<50 && Boolean(rmnrows) && rmnrows.length>0) {
         dimcounter++;
         var newrmnrows = [];
         for(var i=0;i<rmnrows.length;i++) {
            var lyr = JSON.parse(rmnrows[i].value);
            if(lyr.type=='header') {
               //alert('layer header found: ' + JSON.stringify(lyr));
               if(Boolean(lyr.oright) && !isNaN(lyr.oright) && parseInt(lyr.oright)>200) {
                  oright = parseInt(lyr.oright);
               }
               if(Boolean(lyr.fixdim) && lyr.fixdim=='1') {
                  fixdim = true;
               }
            } else if(lyr.type!='code' && (!Boolean(lyr.hide) || lyr.hide!='1')) {
               if(Boolean(lyr.parent) && !Boolean(parenttops[lyr.parent])) {
                  newrmnrows.push(rmnrows[i]);
               } else {
                  var tht = 0;
                  if(lyr.ht != 'auto' && !isNaN(lyr.ht)) tht = parseInt(lyr.ht);
                  else variableht = true;
                  
                  var lf = parseInt(lyr.lf);
                  var tp = parseInt(lyr.tp);
                  if(lyr.parent) {
                     lf += parseInt(parenttops[lyr.parent].lf);
                     tp += parseInt(parenttops[lyr.parent].tp);
                  }
                  var obj = {};
                  obj.lf = lf;
                  obj.tp = tp;
                  parenttops[rmnrows[i].wd_row_id] = obj;
                  
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
      
      // determine total width and height of area needed and ratios
      var vwd = maxright - minleft;
      var vht = maxbot - mintop;
      var v_ratio = vwd / vht;
      
      var divwd = jQuery('#' + jsondata.divid).width();
      var divht = jQuery('#' + jsondata.divid).height();
      
      
      var usingtp = 0;
      var eff_ratio;
      //If originator specified strict sizing
      if(Boolean(oright)) {
         eff_ratio = oright / 10000.0;
         
         //Make sure content fits in the slot
         if(divwd < (vwd * eff_ratio)) eff_ratio = (divwd / vwd);
      } else {
         eff_ratio = (divwd / vwd);
         if(Boolean(divht) && !variableht){
            if(fixdim) {
               if((eff_ratio * vht)>divht) eff_ratio = (divht/vht);
            } else {
               //**chj**
               var adjht = eff_ratio * vht;
               if(adjht>divht) {
                  if(((adjht - divht)/adjht)>0.2) {
                     eff_ratio = (divht/(0.8 * vht));
                     adjht = eff_ratio * vht;
                  }
                  usingtp = (-1) * Math.round((adjht - divht)/2);
               } else {
                  eff_ratio = (divht/vht);
                  var adjwd = eff_ratio * vwd;
                  if(adjwd>divwd) {
                     if(((adjwd - divwd)/adjwd)>0.2) {
                        eff_ratio = (divwd / (vwd * 0.8));
                        adjwd = eff_ratio * vwd;
                     }
                  }
               }
            }
         }
      }
      
      // This will effectively center the layer within the div
      var usinglf = Math.floor((divwd - (eff_ratio * vwd))/2);
      
      // make sure there's at least enough room for estimate
      jQuery('#' + jsondata.divid).css('min-height',Math.ceil(vht * eff_ratio) + 'px');
      

      // Add all elements to the page
      var rmnrows = jsondata.rows;
      var rmnrows_new;
      var rmndivid;
      var rmncounter = 0;
      while(Boolean(rmnrows) && rmnrows.length>0 && rmncounter<10) {
         rmncounter++;
         rmnrows_new = [];
         for(var i=0;i<rmnrows.length;i++) {
            var lyr = JSON.parse(rmnrows[i].value);
            //alert('layer name ' + lyr.divname + ' type: ' + lyr.type);
            //if(jsfpb_devmode) alert('counter: ' + dimcounter + ' looking at layer: ' + JSON.stringify(lyr));
            str = '';
            
            // Decide if this element belongs in the main or sub element
            var adj_top = mintop;
            var adj_left = minleft;
            var adj_usingtp = usingtp;
            var adj_usinglf = usinglf;
            rmndivid = jsondata.divid;            
            if(Boolean(lyr.parent)) {
               rmndivid = 'jsfv_' + lyr.parent + '_opstyle';
               adj_top = 0;
               adj_left = 0;
               adj_usingtp = 0;
               adj_usinglf = 0;
            }
            
            if(jQuery('#' + rmndivid).length < 1) {
               // Parent element may not yet be created, try again next time
               //if(jsfpb_devmode) alert('this layer divid\'s parent could not be found');
               rmnrows_new.push(rmnrows[i]);
            } else if (!Boolean(rmnrows[i].skip)) {
               //if(jsfpb_devmode) alert('this layer made it through');
               // This element is legit - create the html for it, then add it to the DOM
               if(Boolean(jsfpb_defaultfont) && (!Boolean(lyr.ffam) || lyr.ffam.toLowerCase()=='arial')) lyr.ffam = jsfpb_defaultfont;
               if(lyr.type=='code') {
                  //alert('pb code block found');
                  // This is a code block
                  if(Boolean(lyr.txt)) {
                     str += '\n<scr';
                     str += 'ipt>\n';
                     str += jsfpb_convertback(lyr.txt);
                     str += '\n</scr';
                     str += 'ipt>\n';
                  }
               } else if(!Boolean(lyr.hide) || lyr.hide!='1') {
                  var unq_id = rmnrows[i].wd_row_id;
                  if(Boolean(rmnrows[i].globalid)) {
                     unq_id = rmnrows[i].globalid;
                     //if(jsfpb_devmode) alert('global id set: ' + unq_id);
                  }
                  
                  //if(jsfpb_devmode) alert('divid: ' + rmndivid + ' this layer made it through: ' + JSON.stringify(lyr));
                  var top = adj_usingtp + Math.round(eff_ratio * (parseInt(lyr.tp) - adj_top));
                  var left = adj_usinglf + Math.round(eff_ratio * (parseInt(lyr.lf) - adj_left));
                  var animation = '';
                  
                  if(Boolean(lyr.fout)) {
                     // last thing that will happen is a fade out
                     animation = 'setTimeout(function(){jQuery(\'#jsfv_' + unq_id + '_outer\').fadeOut(500);}, ' + (parseFloat(lyr.fout) * 1000) + ');';
                  }
                  
                  var move = lyr.move;
                  if(Boolean(move)) {
                     move += lyr.lf + ',' + lyr.tp + ',';
                     var mvarr = move.split(';');
                     if(Boolean(mvarr) && mvarr.length>0) {
                        var obj = jsfpb_iterateanimation(mvarr,rmnrows[i].wd_row_id,0,adj_usinglf,adj_usingtp,eff_ratio,adj_left,adj_top,lyr.divname,animation);
                        animation = obj.js;
                        top = obj.tp;
                        left = obj.lf;
                     }
                  }
                  
                  var disp = '';
                  if(Boolean(lyr.fin)) {
                     disp = 'display:none;';
                     animation = 'setTimeout(function(){jQuery(\'#jsfv_' + unq_id + '_outer\').fadeIn(500,function(){' + animation + '});}, ' + (parseFloat(lyr.fin) * 1000) + ');';
                  }
                  
                  var width = Math.round(eff_ratio * parseFloat(lyr.wd));
                  var height = 'auto';
                  if(lyr.ht != 'auto') height = Math.round(eff_ratio * parseInt(lyr.ht));
                  if(isNaN(top) || parseInt(top)<0) top = 0;
                  str += '<div id=\"jsfv_' + unq_id + '_outer\" ';
                  str += 'style=\"' + disp;
                  if(height != 'auto') {
                     str += 'height:' + height + 'px;';
                     str += 'overflow:hidden;';
                     str += 'position:absolute;';
                     str += 'left:' + left + 'px;';
                     str += 'top:' + top + 'px;';
                  } else {
                     str += 'position:relative;';
                     str += 'padding-left:' + left + 'px;';
                     str += 'padding-top:' + top + 'px;';
                  }
                  
                  str += 'width:' + width + 'px;';
                  if(Boolean(lyr.zindex)) str += 'z-index:' + lyr.zindex + ';';
                  if(Boolean(lyr.rad)) str += 'border-radius:' + Math.round(eff_ratio * parseFloat(lyr.rad)) + 'px;';
                  if(Boolean(lyr.onclick)) str += 'cursor:pointer;';
                  str += '\"';
                  if(Boolean(lyr.onclick)) {
                     var oc = jsfpb_replaceAll('http:','https:',lyr.onclick);
                     if(oc.startsWith('http')) oc = 'window.open(\'' + oc + '\');';
                     else if(oc.includes('/') && !oc.endsWith(';') && !oc.includes('(') && !oc.includes(')')) oc = 'location.href=\'' + oc + '\';';
                     else if(!oc.endsWith(';') && !oc.includes('(') && !oc.includes(')')) oc = 'jsfpb_getPage(jsfpb_wdname,\'' + oc + '\',\'\',jsfpb_lastdivid,\'\',\'\',jsfpb_lastver);jQuery(\'body\').scrollTop(0);';
                     str += ' onclick=\"' + oc + '\"';
                  }
                  str += '>';

                  // Put all font style here so children divs can inherit
                  str += '<div id=\"jsfv_' + unq_id + '_opstyle\" style=\"position:relative;';
                  str += 'width:' + width + 'px;';
                  if(height != 'auto') str += 'height:' + height + 'px;overflow:hidden;';
                  if(Boolean(lyr.fsz)) str += 'font-size:' + Math.round(eff_ratio * parseInt(lyr.fsz)) + 'px;';
                  if(Boolean(lyr.ffam)) str += 'font-family:' + lyr.ffam + ';';
                  if(Boolean(lyr.fclr)) str += 'color:' + lyr.fclr + ';';
                  if(Boolean(lyr.fbld) && lyr.fbld=='1') str += 'font-weight:bold;';
                  else if(Boolean(lyr.fbld) && lyr.fbld=='2') str += 'font-weight:100;';
                  if(Boolean(lyr.fund) && lyr.fund=='1') str += 'text-decoration:underline;';
                  if(Boolean(lyr.fitl) && lyr.fitl=='1') str += 'font-style:italic;';
                  if(Boolean(lyr.faln)) str += 'text-align:' + lyr.faln + ';';
                  str += '\">';
                  
                  str += '<div id=\"jsfv_' + unq_id + '_opopac\" style=\"';
                  str += 'position:absolute;';
                  str += 'z-index:1;';
                  str += 'left:0px;top:0px;';
                  str += 'width:' + width + 'px;';
                  if(height != 'auto') str += 'height:' + height + 'px;overflow:hidden;';
                  if(Boolean(lyr.opacity)) str += 'opacity:' + lyr.opacity + ';';
                  if(Boolean(lyr.bgclr)) str += 'background-color:' + lyr.bgclr + ';';
                  str += '\">';
                  if(Boolean(lyr.img)) {
                     str += '<img src=\"' + jsfpb_replaceAll('http:','https:',lyr.img) + '\"';
                     str += ' style=\"';
                     str += 'position:absolute;left:0px;top:0px;';
                     str += 'max-width:' + width + 'px;';
                     if(height != 'auto') str += 'max-height:' + height + 'px;';
                     str += '\">';
                  }
                  str += '</div>';
                  
                  if(Boolean(lyr.txt) && Boolean(lyr.type) && lyr.type=='youtube') {
                     str += '<div style=\"position:absolute;left:0px;top:0px;z-index:2;\">';
                     str += '<iframe width=\"' + width + '\" height=\"' + (Math.floor(width * 9/16)) + '\" src=\"https://www.youtube.com/embed/' + lyr.txt + '\" frameborder=\"0\" allowfullscreen></iframe>';
                     str += '</div>';
                     
                  } else if(Boolean(lyr.txt) || (Boolean(lyr.type) && lyr.type=='textbox') || (Boolean(lyr.type) && lyr.type=='textarea') || (Boolean(lyr.type) && lyr.type=='captcha') || (Boolean(lyr.type) && lyr.type=='user' && Boolean(lyr.field_id)) || (Boolean(lyr.type) && lyr.type=='wdata' && Boolean(lyr.wd_id) && Boolean(lyr.field_id)) || (Boolean(lyr.type) && lyr.type=='searchbox' && Boolean(lyr.wd_id)) || (Boolean(lyr.type) && lyr.type=='jdataform' && Boolean(lyr.wd_id)) || (Boolean(lyr.type) && lyr.type=='jdatalist' && Boolean(lyr.wd_id)) || (Boolean(lyr.type) && lyr.type=='jdatacustomlist' && Boolean(lyr.wd_id))) {
                     //alert('here is ' + lyr.type + ', ' + lyr.divname);
                     str += '<div ';
                     str += 'id=\"jsfv_' + unq_id + '_txtdisp\" ';
                     if(Boolean(lyr.classname)) str += 'class=\"' + lyr.classname + '\" ';
                     str += 'style=\"position:relative;z-index:2;';
                     //str += 'left:0px;top:0px;';
                     str += 'width:' + width + 'px;';
                     if(height != 'auto') str += 'height:' + height + 'px;overflow:hidden;';
                     str += '\">';
                     
                     if(Boolean(lyr.type) && lyr.type=='textbox') {
                        var inptcss = '';
                        inptcss += 'width:' + (width - 20) + 'px;';
                        if(height != 'auto') inptcss += 'height:' + (height - 8) + 'px;';
                        if(Boolean(lyr.fsz)) inptcss += 'font-size:' + Math.round(eff_ratio * parseInt(lyr.fsz)) + 'px;';
                        //alert(lyr.divname + ' tab index: ' + lyr.tabi);
                        str += jsfpb_getautotext(jsfpb_flattenstr(lyr.divname,false,true),lyr.txt,inptcss,'','',(parseInt(lyr.rqd)==1),lyr.tabi);
                     } else if(Boolean(lyr.type) && lyr.type=='dropdown') {
                        var inptcss = '';
                        inptcss += 'width:' + (width - 20) + 'px;';
                        if(height != 'auto') inptcss += 'height:' + (height - 8) + 'px;';
                        if(Boolean(lyr.fsz)) inptcss += 'font-size:' + Math.round(eff_ratio * parseInt(lyr.fsz)) + 'px;';
                        str += jsfpb_getdropdown(jsfpb_flattenstr(lyr.divname,false,true),lyr.txt,inptcss,'','',(parseInt(lyr.rqd)==1),lyr.tabi);
                     } else if(Boolean(lyr.type) && lyr.type=='statedropdown') {
                        var inptcss = '';
                        inptcss += 'width:' + (width - 20) + 'px;';
                        if(height != 'auto') inptcss += 'height:' + (height - 8) + 'px;';
                        if(Boolean(lyr.fsz)) inptcss += 'font-size:' + Math.round(eff_ratio * parseInt(lyr.fsz)) + 'px;';
                        str += jsfpb_getstatedropdown(jsfpb_flattenstr(lyr.divname,false,true),inptcss,'','',(parseInt(lyr.rqd)==1),lyr.tabi);
                     } else if(Boolean(lyr.type) && lyr.type=='textarea') {
                        var inptcss = '';
                        inptcss += 'width:' + (width - 20) + 'px;';
                        if(height != 'auto') inptcss += 'height:' + (height - 8) + 'px;';
                        if(Boolean(lyr.fsz)) inptcss += 'font-size:' + Math.round(eff_ratio * parseInt(lyr.fsz)) + 'px;';
                        str += jsfpb_getautotextarea(jsfpb_flattenstr(lyr.divname,false,true),lyr.txt,inptcss,'','',lyr.tabi);
                     } else if(Boolean(lyr.type) && lyr.type=='captcha') {
                        var chars = '2346789abcdefghjkmnprtuvwxyz';
                        var tempcaptcha;
                        var tempstr = '';
                        var tempcnt = 0;
                        while (tempcnt < 6) {
                           tempcaptcha = Math.floor(Math.random() * chars.length);
                           tempstr = tempstr + chars.substring(tempcaptcha,(tempcaptcha+1));
                           tempcnt = tempcnt + 1;
                        }
                        jsfpb_captcha = tempstr;
                        str += '<img src=\"' + jsfpb_domain + 'secimage/' + tempstr + '.jpg\">';
                     } else if(Boolean(lyr.type) && lyr.type=='searchbox') {
                        str += '<div id=\"' + jsfpb_flattenstr(lyr.divname,false,true) + '\"></div>';
                        str += '\n<scr';
                        str += 'ipt>\n';
                        str += 'jsfsearch_testinput(\'' + jsfpb_flattenstr(lyr.divname,false,true) + '\',\'' + lyr.wd_id + '\',\'' + lyr.txt + '\',\'\',true,' + (width - 20) + ',' + Math.round(eff_ratio * parseInt(lyr.fsz)) + ');\n';
                        str += '\n</scr';
                        str += 'ipt>\n';
                     } else if(Boolean(lyr.type) && lyr.type=='password') {
                        var inptcss = '';
                        inptcss += 'width:' + (width - 20) + 'px;';
                        if(height != 'auto') inptcss += 'height:' + (height - 8) + 'px;';
                        if(Boolean(lyr.fsz)) inptcss += 'font-size:' + Math.round(eff_ratio * parseInt(lyr.fsz)) + 'px;';
                        str += jsfpb_pwfield(jsfpb_flattenstr(lyr.divname,false,true),lyr.txt,inptcss,'',(parseInt(lyr.rqd)==1),lyr.tabi);
                     } else if(Boolean(lyr.type) && lyr.type=='user') {
                        //alert('field: ' + lyr.field_id + ' user: ' + JSON.stringify(jsfcore_globaluser));
                        str += '<div style=\"';
                        if(Boolean(lyr.pad)) str += 'padding:' + Math.round(eff_ratio * parseInt(lyr.pad)) + 'px;';
                        if(lyr.field_id=='url' || lyr.field_id=='website') str += 'cursor:pointer;\" onclick=\"window.open(\'' + jsfcore_globaluser[lyr.field_id] + '\');';
                        str += '\">';
                        str += jsfcore_globaluser[lyr.field_id];
                        str += '</div>';                        
                     } else if(Boolean(lyr.type) && lyr.type=='wdata') {
                        var adafld = {};
                        adafld.wd_id = lyr.wd_id;
                        adafld.field_id = lyr.field_id;
                        adafld.wdtype = lyr.wdtype;
                        adafld.divid = 'jsfv_' + unq_id + '_txtdisp';
                        jsfpb_visualformelements.push(adafld);
                        if(!Boolean(jsfpb_visualjdata[lyr.wd_id])) jsfpb_visualjdata[lyr.wd_id] = true;
                        if(!Boolean(jsfpb_visualjdatagetrows[lyr.wd_id]) && lyr.wdtype!='new') jsfpb_visualjdatagetrows[lyr.wd_id] = true;
                        //if(jsfpb_devmode) alert('wdata field: ' + adafld.field_id + ' wd: ' + adafld.wd_id + ' type: ' + adafld.wdtype);
                        //var gname = '';
                        //jsfada_getFieldPos(lyr.wd_id,gname,jsfpb_);
                        
                        //var flds = jsfada_tablesfields[jsondata.wd_id].results;
                        //var obj = jsfada_displayfield(lyr.wd_id,row['wd_row_id'],flds[i],row[flds[i].field_id],'','rowfld_' + jsondata.shortname + '_' + flds[i].map,(Boolean(row.userid) && row.userid==jsfcore_globaluser.userid));
     
                     } else if(Boolean(lyr.type) && lyr.type=='jdataform') {
                        //alert('building ' + lyr.type + ': ' + lyr.divname + ' wd: ' + lyr.wd_id + ' section: ' + lyr.section);
                        var adafld = {};
                        adafld.wd_id = lyr.wd_id;
                        adafld.wdtype = lyr.wdtype;
                        adafld.section = lyr.section;
                        adafld.divid = 'jsfv_' + unq_id + '_txtdisp';
                        jsfpb_visualforms.push(adafld);
                        if(!Boolean(jsfpb_visualjdata[lyr.wd_id])) jsfpb_visualjdata[lyr.wd_id] = true;
                        if(!Boolean(jsfpb_visualjdatagetrows[lyr.wd_id]) && lyr.wdtype!='new') jsfpb_visualjdatagetrows[lyr.wd_id] = true;
                        
                        //jsfada_displayrecord(wd_id,row)
                        //alert('1 jsfpb_visualforms: ' + JSON.stringify(jsfpb_visualforms));
                        
                     } else if(Boolean(lyr.type) && lyr.type=='jdatacustomlist') {
                        // Create a new object to pass to jsfpb_drawvisualcomponents_return
                        
                        // find children, grandchildren and mark them to be skipped by this path
                        var ch = jsfpb_getvisualtree(rmnrows[i].wd_row_id,rmnrows);
                        var newrows = [];
                        //alert(ch.length + ' children: ' + JSON.stringify(ch));
                        for(var k=0;k<ch.length;k++) {
                           for(m=0;m<rmnrows.length;m++) {
                              if(ch[k].wd_row_id == rmnrows[m].wd_row_id) {
                                 rmnrows[m].skip = true;
                                 var newrow = {};
                                 newrow.skip = false;
                                 newrow.name = ch[k].name;
                                 newrow.value = ch[k].value;
                                 newrow.created = ch[k].created;
                                 newrow.wd_row_id = ch[k].wd_row_id;
                                 //newrow.globalid = ch[k].wd_row_id + '_' + jsfpb_jdatacustomlists.length;
                                 newrow.version = ch[k].version;
                                 newrow.verstatus = ch[k].verstatus;
                                 if(parseInt(ch[k].depth) == 1) {
                                    //alert('found a child');
                                    var tlyr = JSON.parse(ch[k].value);
                                    tlyr.parent = '';
                                    newrow.value = JSON.stringify(tlyr);
                                 }
                                 newrows.push(newrow);
                                 break;
                              }
                           }
                        }
                        
                        // Take children/grandchildren and add them to individual rows
                        var obj = {};
                        obj.rows = newrows;
                        //if(jsfpb_devmode) alert('adding layers: ' + JSON.stringify(ch));
                        obj.divid = rmndivid;
                        obj.wd_id = lyr.wd_id;
                        obj.wdtype = lyr.wdtype;
                        obj.section = lyr.section;
                        obj.url = lyr.txt;
                        jsfpb_jdatacustomlists.push(obj);
                        //alert('jsfpb_jdatacustomlists count: ' + obj.rows.length + ' divid: ' + obj.divid + ' wd_id: ' + obj.wd_id + ' wdtype: ' + obj.wdtype + ' url: ' + obj.url);
                        
                        
                     } else if(Boolean(lyr.type) && lyr.type=='jdatalist') {
                        var adafld = {};
                        adafld.wd_id = lyr.wd_id;
                        adafld.divid = 'jsfv_' + unq_id + '_txtdisp';
                        jsfpb_visualformlists.push(adafld);
                        if(!Boolean(jsfpb_visualjdata[lyr.wd_id])) jsfpb_visualjdata[lyr.wd_id] = true;
                        if(!Boolean(jsfpb_visualjdatagetrows[lyr.wd_id])) jsfpb_visualjdatagetrows[lyr.wd_id] = true;
                        
                        //var filterstr = '';
                        //var limit = '';
                        //var page = '';
                        //var ignoreforuser = true;
                        //jsfada_displaytable(lyr.wd_id,filterstr,limit,page,ignoreforuser);                        
                     } else if(Boolean(lyr.txt)) {
                        if(Boolean(lyr.pad)) str += '<div style=\"padding:' + Math.round(eff_ratio * parseInt(lyr.pad)) + 'px;\">';
                        str += jsfpb_convertdisplay(lyr.txt);
                        if(Boolean(lyr.pad)) str += '</div>';
                     }
                     
                     str += '</div>';
                  }
                  str += '</div>';
                  
                  str += '</div>';
                  if(Boolean(animation)) {
                     str += '\n<scr';
                     str += 'ipt>\n' + animation + '\n</sc';
                     str += 'ript>\n';
                  }
               }
               // Add the above HTML to the document
               //if(jsfpb_devmode) alert('adding html to: ' + rmndivid);
               jQuery('#' + rmndivid).append(str);
            }
         }
         rmnrows = rmnrows_new;
      }
   }
   
   //if(jsfpb_devmode) alert('jsfpb_drawvisualcomponents_return ending # of additional callbacks: ' + jsfpb_additionalvisualcomponents.length);

   //alert('2 jsfpb_visualforms: ' + JSON.stringify(jsfpb_visualforms));
   jsfpb_iteratejdata();
}

function jsfpb_getvisualtree(parentid,allelements,depth) {
   //alert('parentid: ' + parentid + ' elements: ' + JSON.stringify(allelements));
   var finalarray = [];
   if(!Boolean(depth) || isNaN(depth)) depth = 1;
   for(var i=0;i<allelements.length;i++) {
      var lyr = JSON.parse(allelements[i].value);
      var arr = [];
      if(Boolean(lyr.parent) && lyr.parent == parentid) {
         //alert('found one: ' + JSON.stringify(lyr));
         arr = jsfpb_getvisualtree(allelements[i].wd_row_id,allelements,(depth + 1));
         if(!Boolean(arr)) arr = [];
         allelements[i].depth = depth;
         arr.unshift(allelements[i]);
         finalarray = finalarray.concat(arr);
      }
   }
   //alert('final array: ' + JSON.stringify(finalarray));
   return finalarray;
}

var jsfpb_jdata_currobj;
function jsfpb_iteratejdata(jsondata) {
   //if(jsfpb_devmode) alert('jsfpb_iteratejdata beginning # of additional callbacks: ' + jsfpb_additionalvisualcomponents.length);
   
   var somethinghappened = false;
   
   //alert('3 jsfpb_visualforms: ' + JSON.stringify(jsfpb_visualforms));
   //alert('iterating jdata.  jdata: ' + JSON.stringify(jsondata));
   // This could be returning from getwdandrows
   if(Boolean(jsondata) && Boolean(jsondata.rows) && jsondata.responsecode==1) {
      jsfada_mostrecentrows[jsondata.wd_id] = jsondata.rows;
      jsfada_mostrecentrows[jsfpb_flattenstr(jsondata.wdname,false,true)] = jsondata.rows;
   }
   
   // First get data and config for the tables needed
   // recursively cycle through
   var holdforwd = false;
   for (var key in jsfpb_visualjdata) {
      //alert('checking jdata: ' + key);
      if(Boolean(jsfpb_visualjdata[key])) {
         //alert('checking jdata: ' + key);
         // API to get data/config, and never call again
         
         if(!Boolean(jsfada_tablesfields) || !Boolean(jsfada_tablesfields[jsfpb_flattenstr(key,false,true)])) {
            //alert('getting field pos, then iterating...');
            //if(!Boolean(jsfada_tablesfields)) alert('no jsfada_tablesfields available.');
            //else if(!Boolean(jsfada_tablesfields[jsfcore_flattenstr(key)])) alert('no entry [' + jsfcore_flattenstr(key) + '] in jsfada_tablesfields.  obj: ' + JSON.stringify(jsfada_tablesfields));
            holdforwd = true;
            var gname = '';
            somethinghappened = true;
            //alert('calling jsfada_getFieldPos(' + key + ')');
            jsfada_getFieldPos(key,gname,jsfpb_iteratejdata);
         } else if(!Boolean(jsfada_mostrecentrows) || !Boolean(jsfada_mostrecentrows[jsfpb_flattenstr(key,false,true)])) {
            //alert('getting jdata rows, then iterating... wd_id: ' + key);
            jsfpb_visualjdata[key] = false;
            
            if(Boolean(jsfpb_visualjdatagetrows[key])) {
               //alert('actually getting jdata rows, then iterating... wd_id: ' + key);
               jsfpb_visualjdatagetrows[key] = false;
               holdforwd = true;
               //alert('found wdata: ' + key);
               
               somethinghappened = true;
               
               var params = '&limit=100';
               params += '&maxcol=20';
               jsfcore_getwebdata_jsonp(key,'jsfpb_iteratejdata',params,true,true);
            }
         }
         break;
      }
   }
   
   //alert('3 jsfpb_visualforms: ' + JSON.stringify(jsfpb_visualforms));
   // Once data/config is done, now display necessary stuff
   if(!holdforwd) {
      //alert('made it...!');
      if(Boolean(jsfpb_visualforms) && jsfpb_visualforms.length>0) {
         jsfpb_jdata_currobj = jsfpb_visualforms.shift();
         //alert('current obj: ' + JSON.stringify(jsfpb_jdata_currobj));
         var str = '<div id=\"jsfwdareadetails\"></div>';
         jQuery('#' + jsfpb_jdata_currobj.divid).html(str);
         
         var row;
         if(Boolean(jsfpb_current_wd_row_id)) row = jsfada_getrecentrow(jsfpb_jdata_currobj.wd_id,jsfpb_current_wd_row_id);
         jsfada_displayrecord(jsfpb_jdata_currobj.wd_id,row,jsfpb_jdata_currobj.section);
         somethinghappened = true;
         jsfpb_iteratejdata();
      } else if(Boolean(jsfpb_visualformlists) && jsfpb_visualformlists.length>0) {
         jsfpb_jdata_currobj = jsfpb_visualformlists.shift();
         var str = '<div id=\"jsfwdarea\"></div>';
         jQuery('#' + jsfpb_jdata_currobj.divid).html(str);
         
         var filterstr = '';
         var limit = '';
         var page = '';
         var ignoreforuser = true;
         jsfada_displaytableresults(jsfpb_jdata_currobj.wd_id,filterstr,limit,page,ignoreforuser);
         somethinghappened = true;
         jsfpb_iteratejdata();
      } else if(Boolean(jsfpb_visualformelements) && jsfpb_visualformelements.length>0) {
         jsfpb_jdata_currobj = jsfpb_visualformelements.shift();

         // See if we have a row to use for this
         var row = {};
         if(Boolean(jsfpb_current_wd_row_id)) row = jsfada_getrecentrow(jsfpb_jdata_currobj.wd_id,jsfpb_current_wd_row_id);
         
         var flds = jsfada_tablesfields[jsfpb_flattenstr(jsfpb_jdata_currobj.wd_id,false,true)].results;
         for (var j=0;j<flds.length;j++) {
            var fld = flds[j];
            if(fld.field_id == jsfpb_jdata_currobj.field_id) {
               //function jsfada_displayfield(wd_id,wd_row_id,fld,val,wd,classname,auth,autofill) {
               var obj = jsfada_displayfield(jsfpb_jdata_currobj.wd_id,jsfpb_current_wd_row_id,fld,row[fld.field_id],jQuery('#' + jsfpb_jdata_currobj.divid).width(),'',false,true);
               
               //based on the type of field display, show data vs input, etc
               if(jsfpb_jdata_currobj.wdtype == 'edit' && Boolean(jsfpb_current_wd_row_id)) jQuery('#' + jsfpb_jdata_currobj.divid).append(obj.dispinput);
               else if(jsfpb_jdata_currobj.wdtype == 'display') jQuery('#' + jsfpb_jdata_currobj.divid).append(obj.dispval);
               else jQuery('#' + jsfpb_jdata_currobj.divid).append(obj.dispinput);
               
               break;
            }
         }
         somethinghappened = true;
         jsfpb_iteratejdata();
      } else if(Boolean(jsfpb_jdatacustomlists) && jsfpb_jdatacustomlists.length>0) {
         jsfpb_jdata_currobj = jsfpb_jdatacustomlists[0];
         
         if(Boolean(jsfpb_jdatacustomlists_rows) && Boolean(jsfpb_jdatacustomlists_rows[jsfpb_jdata_currobj.wd_id])) {
            jsfpb_jdata_currobj = jsfpb_jdatacustomlists.shift();
            var params = jsfcore_explodequery(jsfpb_jdata_currobj.url);
            
            if(Boolean(jsfpb_jdatacustomlists_rows[jsfpb_jdata_currobj.wd_id]) && Boolean(jsfpb_jdatacustomlists_rows[jsfpb_jdata_currobj.wd_id].rows)) {
               var rows = jsfpb_jdatacustomlists_rows[jsfpb_jdata_currobj.wd_id].rows;
               
               var outer = '<div id=\"' + jsfpb_jdata_currobj.divid + '_outer\"></div>';
               jQuery('#' + jsfpb_jdata_currobj.divid).append(outer);
               
               for(var i=0;i<rows.length;i++) {
                  //alert('adding ' + i + ' to html');
                  var str = '';
                  str += '<div ';
                  str += 'class=\"' + jsfpb_jdata_currobj.divid + '_div\" ';
                  str += 'id=\"' + jsfpb_jdata_currobj.divid + '_' + rows[i].wd_row_id + '\" ';
                  str += 'style=\"position:relative;\" ';
                  str += '>';
                  str += '</div>';
                  jQuery('#' + jsfpb_jdata_currobj.divid + '_outer').append(str);
                  
                  jsondata = {};
                  // copy the layers by value, not reference
                  //jsondata.rows = jsfpb_jdata_currobj.rows;
                  jsondata.rows = JSON.parse(JSON.stringify(jsfpb_jdata_currobj.rows));
                  jsondata.divid = jsfpb_jdata_currobj.divid + '_' + rows[i].wd_row_id;
                  jsondata.wd_row_id = rows[i].wd_row_id;
                  for(var j=0;j<jsondata.rows.length;j++) {
                     jsondata.rows[j].globalid = jsondata.rows[j].wd_row_id + '_' + jsondata.wd_row_id;
                  }
                  //jsondata.globalid = rows[i].wd_row_id + '_' + i;
                  //if(jsfpb_devmode) alert('pushing visual callback: ' + JSON.stringify(jsondata));
                  jsfpb_additionalvisualcomponents.push(jsondata);
               }

               // determine scrolling and alignment
               var twd = jQuery('#' + jsfpb_jdata_currobj.divid).width();
               var tht = jQuery('#' + jsfpb_jdata_currobj.divid).height();
               if(!Boolean(tht) || parseInt(tht)<20) tht = 20;
               if(params['jsfhoriz']=='1') {
                  jQuery('.' + jsfpb_jdata_currobj.divid + '_div').css('float','left');
                  jQuery('.' + jsfpb_jdata_currobj.divid + '_div').css('width',twd + 'px');
                  jQuery('.' + jsfpb_jdata_currobj.divid + '_div').css('height',tht + 'px');
                  jQuery('#' + jsfpb_jdata_currobj.divid).css('overflow-x','auto');
                  var str = '<div style=\"clear:both;\"></div>';
                  jQuery('#' + jsfpb_jdata_currobj.divid + '_outer').append(str);
                  jQuery('#' + jsfpb_jdata_currobj.divid + '_outer').css('width',(twd * rows.length) + 'px');
               } else {
                  jQuery('.' + jsfpb_jdata_currobj.divid + '_div').css('position','relative');
                  jQuery('#' + jsfpb_jdata_currobj.divid).css('overflow-y','auto');
               }
               
            }
            jsfpb_iteratejdata();
         } else {
            // get the rows
            jsfpb_getrowsforlist();
         }
         somethinghappened = true;
      }
   }
   
   //if(jsfpb_devmode) alert('main # of additional callbacks: ' + jsfpb_additionalvisualcomponents.length);
   
   if(!somethinghappened) {
      jsfpb_drawvisualcomponents_busy = false;
      
      if(Boolean(jsfpb_additionalvisualcomponents) && jsfpb_additionalvisualcomponents.length>0) {
         //if(jsfpb_devmode) alert('# of additional callbacks: ' + jsfpb_additionalvisualcomponents.length);
         // as the page was built, elements were found to create within the page
         var obj = jsfpb_additionalvisualcomponents.shift();
         jsfpb_current_wd_row_id = obj.wd_row_id;
         //alert('starting the process over again.  ' + jsfpb_additionalvisualcomponents.length + ' items: ' + JSON.stringify(obj));
         jsfpb_drawvisualcomponents_return(obj);
      } else {
         //alert('leaving zone: ' + jsfpb_drawvisualcomponents_re);
         jQuery('.adafldlbl').css('font-size','12px');
         jQuery('.adafldlbl').css('color','#393939');
         jQuery('.adafld').css('margin-bottom','10px');
         jQuery('.adafldcheckboxtxt').css('font-size','12px');
         jQuery('.adafldcheckboxtxt').css('padding-top','2px');
         jQuery('.adafldedtNEWCHKBX').css('margin-top','15px');
         jQuery('.adafldedtRADIO').css('margin-top','10px');
         jQuery('.adafldRADIO').css('margin-top','25px');
         jQuery('.adafldRADIO').css('margin-bottom','30px');
         jQuery('.adafldNEWCHKBX').css('margin-bottom','25px');
         jQuery('.adafldNEWCHKBX').css('border-bottom','1px solid #DEDEDE');      
         jQuery('.adafldlblNEWCHKBX').css('color','#696969');
         jQuery('.adafldselect').css('width','280px');
         jQuery('.adafldselect').css('font-size','16px');
         jQuery('.adafldradiobutton').css('margin-bottom','5px');
         jQuery('.adafldradiobutton').css('margin-top','5px');
         jQuery('#adabtnsave').hide();
      }
   }

   //if(jsfpb_devmode) alert('jsfpb_iteratejdata ending # of additional callbacks: ' + jsfpb_additionalvisualcomponents.length);
}



function jsfpb_getrowsforlist(){
   var params = '';
   if(Boolean(jsfpb_jdata_currobj.url)) params = jsfpb_jdata_currobj.url;
   
   var checkcache = jsfcore_explodequery(params);
   
   params += '&wd_id=' + encodeURIComponent(jsfpb_jdata_currobj.wd_id);
   //alert('params: ' + params);
   // determine if we should check for cache with a parameter in the URL
   jsfpb_QuickJSON('getwdandrows','jsfpb_getrowsforlist_return',params,(!Boolean(checkcache) || !Boolean(checkcache['jsfnocache']) || checkcache['jsfnocache']!='1'));
}

function jsfpb_getrowsforlist_return(jsondata){
   jsfpb_ReturnJSON(jsondata);
   
   //alert('return from list: ' + JSON.stringify(jsondata));
   
   if(!Boolean(jsfada_mostrecentrows)) jsfada_mostrecentrows = {};
   jsfada_mostrecentrows[jsondata.wd_id] = jsondata.rows;
   jsfada_mostrecentrows[jsfpb_flattenstr(jsondata.wdname,false,true)] = jsondata.rows;
   
   if(!Boolean(jsfpb_jdatacustomlists_rows)) jsfpb_jdatacustomlists_rows = {};
   jsfpb_jdatacustomlists_rows[jsfpb_jdata_currobj.wd_id] = jsondata;
   
   jsfpb_iteratejdata();
}

function jsfpb_iterateanimation(mvarr,id,i,usinglf,usingtp,eff_ratio,minleft,mintop,divname,animation){
   if(!Boolean(i)) i=0;
   var obj = {};
   obj.js = '';
   if(i<mvarr.length) {
      // Recursively get all movements before this one
      var temp = jsfpb_iterateanimation(mvarr,id,(i+1),usinglf,usingtp,eff_ratio,minleft,mintop,divname,animation);
      
      var currmv = mvarr[i].split(',');
      
      // If this is the first position, just remember coords, do not move
      if(i==0) {
         obj.lf = usinglf + Math.round(eff_ratio * (parseInt(currmv[0]) - minleft));
         obj.tp = usingtp + Math.round(eff_ratio * (parseInt(currmv[1]) - mintop));
         if(Boolean(currmv[2]) && currmv[2]!='0') obj.js = 'setTimeout(function(){' + temp.js + '}, ' + (parseFloat(currmv[2]) * 1000) + ');';
         else obj.js = temp.js;
      } else {
         if(Boolean(currmv[0]) && Boolean(currmv[1])) {
            var left = usinglf + Math.round(eff_ratio * (parseInt(currmv[0]) - minleft));
            var top = usingtp + Math.round(eff_ratio * (parseInt(currmv[1]) - mintop));
            
            obj.js = 'jQuery(\'#jsfv_' + id + '_outer\').animate({\'top\':\'' + top + 'px\', \'left\':\'' + left + 'px\'}, 500,function(){';
            if(Boolean(currmv[2]) && currmv[2]!='0') {
               obj.js += 'setTimeout(function(){' + temp.js + '}, ' + (parseFloat(currmv[2]) * 1000);
               
               // if we should do something at the very end, add it here
               if(i==(mvarr.length - 1) && Boolean(animation)) {
                  obj.js += ',function(){' + animation + '}';
               }
               obj.js += ');';
            } else {
               obj.js += temp.js;
               
               // if we should do something at the very end, add it here
               if(i==(mvarr.length - 1) && Boolean(animation)) {
                  obj.js += animation;
               }
            }
            obj.js += '});';
         }
      }
   }
   
   return obj;
}



// Auto populated input text fields
// Originally copied from jsfcore
function jsfpb_autotext_leave(divid,dfault) {
   var txt = jQuery('#' + divid);
   if(Boolean(dfault)) {
      if(!Boolean(txt.val()) || txt.val() == ''){
         txt.val(dfault);
         txt.css('font-style','italic').css('color','#999999');
      }
   } else {
      txt.css('font-style','normal').css('color','#000000');
   }
}

function jsfpb_autotext_enter(divid,dfault) {
   
   var txt = jQuery('#' + divid);
   if(Boolean(dfault)) {
      if(Boolean(txt.val()) && txt.val() == dfault){
         txt.val('');
         txt.css('font-style','normal').css('color','#000000');
      }
   } else {
      txt.css('font-style','normal').css('color','#000000');
   }
}

var jsfpb_statenames = {
    "AL": "Alabama", "AK": "Alaska", "AZ": "Arizona", "AR": "Arkansas", "CA": "California", "CO": "Colorado", "CT": "Connecticut", "DE": "Delaware", "FL": "Florida", "GA": "Georgia", "HI": "Hawaii", "ID": "Idaho", "IL": "Illinois", "IN": "Indiana", "IA": "Iowa", "KS": "Kansas", "KY": "Kentucky", "LA": "Louisiana", "ME": "Maine", "MD": "Maryland", "MA": "Massachusetts", "MI": "Michigan", "MN": "Minnesota", "MS": "Mississippi", "MO": "Missouri", "MT": "Montana", "NE": "Nebraska", "NV": "Nevada", "NH": "New Hampshire", "NJ": "New Jersey", "NM": "New Mexico", "NY": "New York", "NC": "North Carolina", "ND": "North Dakota", "OH": "Ohio", "OK": "Oklahoma", "OR": "Oregon", "PA": "Pennsylvania", "RI": "Rhode Island", "SC": "South Carolina", "SD": "South Dakota", "TN": "Tennessee", "TX": "Texas", "UT": "Utah", "VT": "Vermont", "VA": "Virginia", "WA": "Washington", "WV": "West Virginia", "WI": "Wisconsin", "WY": "Wyoming", "AS": "American Samoa", "DC": "Washington, D.C", "FM": "Federated States of Micronesia", "GU": "Guam", "MH": "Marshall Islands", "MP": "Northern Mariana Islands", "PW": "Palau", "PR": "Puerto Rico", "VI": "Virgin Islands"
}

function jsfpb_getstatedropdown(divid,css,val,classstr,rqd,tabi){
   var values = '';
   for(var ndx in jsfpb_statenames) {
      if(values.length > 1) values += ',';
      values += ndx;
   }
   return jsfpb_getdropdown(divid,values,css,val,classstr,rqd,tabi);
}


function jsfpb_getdropdown(divid,values,css,val,classstr,rqd,tabi){
   if(!Boolean(val)) val = '';
   if(!Boolean(css) && !Boolean(classstr)) css = 'width:200px;font-size:16px;margin:3px;';
   if(!Boolean(css)) css = '';
   
   var valarr = values.split(',');
   var str = '';
   
   str += '<div>';
   str += '<select id=\"' + divid + '\" ';
   if(Boolean(tabi)) str += 'tabindex=\"' + tabi + '\" ';
   str += 'data-ignoretxt=\"' + valarr[0] + '\" ';
   if(Boolean(rqd)) str += 'data-required=\"yes\" ';
   else str += 'data-required=\"no\" ';
   str += 'style=\"' + css + '\">';
   str += '<option value=\"\">' + valarr[0] + '</option>';
   for(var i=1;i<valarr.length;i++) {
      var sel = '';
      if(Boolean(val) && val==valarr[i]) sel = ' SELECTED';
      str += '<option value=\"' + valarr[i] + '\"' + sel + '>' + valarr[i] + '</option>';
   }
   str += '</select>';
   if(Boolean(rqd)) str += '<span style=\"margin-left:5px;color:red;font-size:16px;font-weight:bold;\">*</span>';
   str += '</div>';
   
   return str;
}

function jsfpb_getautotext(divid,dfault,css,val,classstr,rqd,tabi){
   if(!Boolean(val)) val = dfault;
   if(!Boolean(css) && !Boolean(classstr)) css = 'width:200px;' + jsfpb_fontfamily + 'font-size:16px;background-color:#F0F0F0;border:1px solid #888888;border-radius:2px;margin:3px;';
   if(!Boolean(css)) css = '';
   
   var dcss = 'font-style:normal;color:#000000;';
   if(val==dfault) dcss = 'font-style:italic;color:#999999;';
   
   var type = 'text';
   if(dfault.toLowerCase()=='email' || dfault.toLowerCase()=='confirm email') type='email';
   else if(dfault.toLowerCase()=='phone' || dfault.toLowerCase()=='phone number') type='tel';
   
   var str = '';
   str += '<div class=\"jsfpb_txtinput\">';
   str += '<input type=\"' + type + '\" value=\"' + val + '\" ';
   str += 'id=\"' + divid + '\" ';
   if(Boolean(tabi)) str += 'tabindex=\"' + tabi + '\" ';
   str += 'data-ignoretxt=\"' + dfault + '\" ';
   if(Boolean(rqd)) str += 'data-required=\"yes\" ';
   else str += 'data-required=\"no\" ';
   if(Boolean(classstr)) str += 'class=\"' + classstr + '\" ';
   str += 'onblur=\"jsfpb_autotext_leave(\'' + divid + '\',\'' + dfault + '\');\" ';
   str += 'onfocus=\"jsfpb_autotext_enter(\'' + divid + '\',\'' + dfault + '\');\" ';
   str += 'style=\"' + css + dcss + '\">';
   if(Boolean(rqd)) str += '<span style=\"margin-left:5px;color:red;font-size:16px;font-weight:bold;\">*</span>';
   str += '</div>';
   return str;
}

function jsfpb_getinput(nm,disp) {
   var obj = {};
   var temp = jQuery('#' + nm).val();
   var dft = jQuery('#' + nm).data('ignoretxt');
   if(Boolean(temp) && Boolean(dft) && temp==dft) temp = '';
   
   obj.val = '';
   if(Boolean(temp)) obj.val = temp;
   obj.error = false;
   obj.msg = '';
   obj.name = nm;
   
   // if this is a required field, make sure it's not empty nor the default text
   if(jQuery('#' + nm).data('required')=='yes' && !Boolean(temp)) {
      obj.error = true;
      obj.msg = 'Please enter a value for \"';
      if(Boolean(disp)) obj.msg += disp;
      else if(Boolean(jQuery('#' + nm).data('ignoretxt'))) obj.msg += jQuery('#' + nm).data('ignoretxt');
      else obj.msg += nm;
      obj.msg += '\".';
   }
   
   return obj;
}


function jsfpb_getautotextarea(divid,dfault,css,val,classstr,tabi){
   if(!Boolean(val)) val = dfault;
   if(!Boolean(css) && !Boolean(classstr)) css = 'width:200px;height:70px;' + jsfpb_fontfamily + 'font-size:16px;background-color:#F0F0F0;border:1px solid #888888;border-radius:2px;margin:3px;';
   if(!Boolean(css)) css = '';
   
   var dcss = 'font-style:normal;color:#000000;';
   if(Boolean(dfault) && val==dfault) dcss = 'font-style:italic;color:#999999;';
   
   var str = '';
   str += '<div class=\"jsfpb_txtinput\">';
   str += '<textarea ';
   str += 'id=\"' + divid + '\" ';
   if(Boolean(tabi)) str += 'tabindex=\"' + tabi + '\" ';
   if(Boolean(dfault)) str += 'data-ignoretxt=\"' + dfault + '\" ';
   if(Boolean(classstr)) str += 'class=\"' + classstr + '\" ';
   if(Boolean(dfault)) str += 'onblur=\"jsfpb_autotext_leave(\'' + divid + '\',\'' + dfault + '\');\" ';
   if(Boolean(dfault)) str += 'onfocus=\"jsfpb_autotext_enter(\'' + divid + '\',\'' + dfault + '\');\" ';
   str += 'style=\"' + css + dcss + '\">' + val + '</textarea>';
   str += '</div>';
   return str;
}

function jsfpb_pwfield(divid,val,css,classstr,rqd,tabi){
   if(!Boolean(css) && !Boolean(classstr)) css = 'width:200px;font-size:16px;background-color:#F0F0F0;border:1px solid #888888;border-radius:2px;margin:3px;';
   if(!Boolean(css)) css = '';
   
   var str = '';
   str += '<div class=\"jsfcore_txtinput\">';
   str += '<input ';
   str += ' id=\"' + divid + '\" ';
   if(Boolean(tabi)) str += 'tabindex=\"' + tabi + '\" ';
   str += 'data-ignoretxt=\"' + val + '\" ';
   if(Boolean(rqd)) str += 'data-required=\"yes\" ';
   else str += 'data-required=\"no\" ';
   if(Boolean(classstr)) str += 'class=\"' + classstr + '\" ';
   str += ' type=\"password\" ';
   str += ' style=\"' + css + 'color:#000000;font-style:normal;display:none;\"';
   str += ' value=\"\"';
   str += ' >';
   str += '<input ';
   str += 'data-ignoretxt=\"' + val + '\" ';
   if(Boolean(rqd)) str += 'data-required=\"yes\" ';
   else str += 'data-required=\"no\" ';
   str += ' id=\"' + divid + '_clear\" ';
   if(Boolean(tabi)) str += 'tabindex=\"' + tabi + '\" ';
   if(Boolean(classstr)) str += 'class=\"' + classstr + '\" ';
   str += ' type=\"text\" ';
   str += ' style=\"' + css + 'color:#999999;font-style:italic;\"';
   str += ' value=\"' + val + '\"';
   str += ' >';
   if(Boolean(rqd)) str += '<span style=\"margin-left:5px;color:red;font-size:16px;font-weight:bold;\">*</span>';
   str += '</div>';

   str += '<scr';
   str += 'ipt language=\"javascript\" type=\"text/javascript\">\n';
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
   str += '</scr';
   str += 'ipt>\n';
   
   return str;
}