var jsfimage_jsonurl;
var jsfimage_divid;
var jsfimage_domain = 'https://recyclemoreplastic.org/';
var jsfimage_width;

var jsfimage_orderedcategories;
var jsfimage_orderedcategories_use;

var jsfimage_userid;

var jsfimage_images = {};

//////////////////////////////////////////////////
//////////////////////////////////////////////////
// These two method can be ignored if there are no
// inexed categories

function jsfimage_initGallery(wd_id,divid,width){
   if(Boolean(width)) jsfimage_width = width;
   if(Boolean(divid)) jsfimage_divid = divid;
   
   jsfimage_jsonurl = jsfimage_domain + 'jsfcode/jsonpcontroller.php';
   jsfimage_jsonurl += '?action=getwdandrows';
   jsfimage_jsonurl += '&enabledonly=1';
   jsfimage_jsonurl += '&wd_id=' + encodeURIComponent(wd_id);
   jsfimage_jsonurl += '&divid=' + encodeURIComponent(divid);
   
   var url = jsfimage_domain + 'jsfcode/jsonpcontroller.php';
   url += '?action=getqopts';
   url += '&wd_id=' + encodeURIComponent(wd_id);
   url += '&field=' + encodeURIComponent('category');
   url += '&divid=' + encodeURIComponent(divid);
   jsfimage_QuickJSON(url,'jsfimage_initGallery_return',true);
}

function jsfimage_initGallery_return(jsondata) {
   jsfimage_ReturnJSON(jsondata);
   //alert('options: ' + JSON.stringify(jsondata.rows));
   jsfimage_orderedcategories = jsondata.rows;
   jsfimage_orderedcategories_use = [];
   jsfimage_getGalleryPage();
}
//////////////////////////////////////////////////
//////////////////////////////////////////////////


function jsfimage_getGalleryPage(url,divid,width){
   if(!Boolean(jsfimage_userid) && (typeof jsfcore_globaluser !== 'undefined') && Boolean(jsfcore_globaluser)) jsfimage_userid = jsfcore_globaluser.userid;
   if(Boolean(url)) jsfimage_jsonurl = url;
   if(Boolean(divid)) jsfimage_divid = divid;
   
   if(Boolean(jsfimage_divid) && Boolean(jsfimage_jsonurl)) {
      if(Boolean(width)) jsfimage_width = width;
      if(!Boolean(jsfimage_width)) jsfimage_width = jQuery('#' + jsfimage_divid).width();
      
      jsfimage_QuickJSON(jsfimage_jsonurl,'jsfimage_setgallerybody',true);
      
   } else {
      alert('Sorry for the inconvenience, but there was an internal error with the image gallery.');
   }
}


function jsfimage_catexists(id,returnname,returndescr) {
   //alert('jsfimage_catexists id: ' + id + ' list: ' + JSON.stringify(jsfimage_orderedcategories));
   var foundmatch = false;
   var descr = '';
   var name = '';
   for(var j=0;j<jsfimage_orderedcategories.length;j++){
      //alert('id shortened: ' + jsfimage_flattenstr(id) + ' and name: ' + jsfimage_flattenstr(jsfimage_orderedcategories[j].name));
      if (jsfimage_flattenstr(id)==jsfimage_flattenstr(jsfimage_orderedcategories[j].value)) {
         foundmatch = true;
         descr = jsfimage_orderedcategories[j].descr;
         name = jsfimage_orderedcategories[j].name;
      } else if (jsfimage_flattenstr(id)==jsfimage_flattenstr(jsfimage_orderedcategories[j].name)) {
         foundmatch = true;
         descr = jsfimage_orderedcategories[j].descr;
         name = jsfimage_orderedcategories[j].name;
      }
      if(Boolean(foundmatch)) break;
   }
   
   var retobj = foundmatch;
   if(Boolean(returnname)) retobj = name;
   else if(Boolean(returndescr)) retobj = descr;
   
   return retobj;
}


   function jsfimage_clicktab(i){
      if(!Boolean(i)) i=1;
      var iteration = 0;
      for(var j=0;j<jsfimage_orderedcategories_use.length;j++){
      //for (var key in jsfimage_images) {
         var key = jsfimage_flattenstr(jsfimage_orderedcategories_use[j].name);
         if(Boolean(jsfimage_images[key])){
            iteration++;
            jQuery('#jsfimage_tab' + iteration).css('background-color','#F7F7F7');
            jQuery('#jsfimage_tab' + iteration).css('color','#96989b');
            jQuery('#jsfimage_body' + iteration).hide();
         }
      }      
      jQuery('#jsfimage_tab' + i).css('background-color','#9bada5');
      jQuery('#jsfimage_tab' + i).css('color','#ffffff');
      jQuery('#jsfimage_body' + i).show();
   }


   function jsfimage_setgallerybody(jsondata) {
      jsfimage_ReturnJSON(jsondata);
      //alert(JSON.stringify(jsondata));
      
      var numofcategories = jsfimage_categorizedata(jsondata);
      
      var across = 8;
      var totalpad = 50;
      var minwd = 110;
      while(across>2 && Math.floor((jsfimage_width-totalpad)/across)<minwd) {
         across = across - 1;
         totalpad = totalpad - 4;
      }
      
      var imgpd = Math.floor(totalpad/5);
      var imgwd = Math.floor((jsfimage_width-totalpad)/across) - 2*imgpd;
      
      //alert('total width: ' + jsfimage_width + ' total pad: ' + totalpad + ' images across: ' + across + ' image pad: ' + imgpd);
      
      var str = '';
      
      // Main body of tabs + category image gallery
      str += '<div style=\"padding:' + (Math.round(totalpad/2)) + 'px;\">';
      
      
      // Show Tabs...
      str += '<div style=\"position:relative;width:' + (jsfimage_width - totalpad) + 'px;margin-top:10px;margin-bottom:10px;\">';
      var tabwd = Math.floor((jsfimage_width - totalpad)/numofcategories) - (numofcategories + 1);
      
      var iteration = 0;
      for(var j=0;j<jsfimage_orderedcategories_use.length;j++){
      //for (var key in jsfimage_images) {
         var key = jsfimage_flattenstr(jsfimage_orderedcategories_use[j].name);
         if(Boolean(jsfimage_images[key])){
            iteration++;
            var group = jsfimage_images[key];
            str += '<div style=\"float:left;width:' + tabwd + 'px;overflow:hidden;cursor:pointer;\">';
            
            // By default, css
            var css = 'border-bottom:1px solid #9bada5;border-left:1px solid #9bada5;border-top:1px solid #9bada5;color:#96989b;text-align:center;padding-top:20px;padding-bottom:20px;background-color:#F7F7F7;';
            if(iteration==1) css += 'border-top-left-radius:5px;border-bottom-left-radius:5px;';
            if(iteration==numofcategories) css += 'border-top-right-radius:5px;border-bottom-right-radius:5px;border-right:1px solid #9bada5;';
            str += '<div onclick=\"jsfimage_clicktab(' + iteration + ');\" id=\"jsfimage_tab' + iteration + '\" style=\"' + css + '\">';
            str += group.display;
            str += '</div>';
            str += '</div>';
         }
      }
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';


      // Prepare all category galleries
      iteration = 0;
      for(var j=0;j<jsfimage_orderedcategories_use.length;j++){
      //for (var key in jsfimage_images) {
         var key = jsfimage_flattenstr(jsfimage_orderedcategories_use[j].name);
         if(Boolean(jsfimage_images[key])){
            iteration++;
            var group = jsfimage_images[key];
            str += '<div id=\"jsfimage_body' + iteration + '\" style=\"position:relative;display:none;\">';      
            if(Boolean(group.descr)) str += '<div style=\"margin-bottom:10px;font-size:18px;font-weight:bold;\">' + group.descr + '</div>';
            str += jsfimage_layoutGalleryPics(group.images,imgwd,imgpd,across,'b' + iteration);
            str += '<div style=\"clear:both;\"></div>';
            str += '</div>';
         }
      }
               
      str += '</div>';
      
      jQuery('#' + jsfimage_divid).html(str);
      jsfimage_clicktab();
   }



   function jsfimage_layoutGalleryPics(picarr,wd,pd,across,type){
      var usedimg = {};
      var usedimgcount = 0;
      var uIncs = new Array();

      var str = '';
      if (Boolean(picarr)) {
         for (var i=0; i<picarr.length; i++) {
            if(!Boolean(usedimg['img' + picarr[i].wd_row_id])) {
               usedimg['img' + picarr[i].wd_row_id] = true;
               str += '<script>';
               str += 'var jsfimage_gall' + type + picarr[i].wd_row_id + '=false;';
               str += 'function jsfimage_gallswap' + type + picarr[i].wd_row_id + '(){';
               //str += 'alert(\'hello\');';
               str += 'jQuery(\'.jsfimage_galleryshade\').hide();';
               str += 'jQuery(\'.jsfimage_galleryhover\').hide();';
               str += 'if(jsfimage_gall' + type + picarr[i].wd_row_id + '){';
               str += 'jQuery(\'#shade' + type + picarr[i].wd_row_id + '\').hide();';
               str += 'jQuery(\'#hover' + type + picarr[i].wd_row_id + '\').hide();';
               str += 'jsfimage_gall' + type + picarr[i].wd_row_id + '=false;';
               str += '} else {';
               str += 'jQuery(\'#shade' + type + picarr[i].wd_row_id + '\').show();';
               str += 'jQuery(\'#hover' + type + picarr[i].wd_row_id + '\').show();';
               str += 'jsfimage_gall' + type + picarr[i].wd_row_id + '=true;';
               str += '}';
               str += '}';
               str += '</script>';
               
               // Start box
               str += '<div ';
               str += 'style=\"float:left;margin:' + pd + 'px;font-size:12px;min-height:178px;\" ';
               str += 'id=\"main' + type + picarr[i].wd_row_id + '\" ';
               str += 'onclick=\"jsfimage_gallswap' + type + picarr[i].wd_row_id + '();\" ';
               str += '>';
               
               // image
               str += '<div style=\"position:relative;width:' + wd + 'px;height:' + wd + 'px;overflow:hidden;border-radius:3px;\">';
               
               // shade
               str += '<div class=\"jsfimage_galleryshade\" id=\"shade' + type + picarr[i].wd_row_id + '\" style=\"position:absolute;z-index:2;display:none;left:0px;top:0px;width:' + wd + 'px;height:' + wd + 'px;border-radius:3px;background-color:#222222;opacity:0.2;\">';
               str += '</div>';
               
               var img = '';
               if (Boolean(picarr[i].thumbnail)) {
                  img = picarr[i].thumbnail;
               } else if (Boolean(picarr[i].jpeg)) {
                  img = picarr[i].jpeg;
               } else if (Boolean(picarr[i].jpegimage)) {
                  img = picarr[i].jpegimage;
               } else if (Boolean(picarr[i].image)) {
                  img = picarr[i].image;
               } else if (Boolean(picarr[i].png)) {
                  img = picarr[i].png;
               }
               if(!img.startsWith('http')) img = jsfimage_domain + 'jsfcode/srvyfiles/' + img;
               img = jsfimage_replaceAll('http:','https:',img);
               
               str += '<img src=\"' + img + '\" ';
               //str += '<img src=\"' + replaceAll('https:','http:',img) + '\" ';
               //str += 'onmouseover=\"jQuery(\'#hover' + type + picarr[i].wd_row_id + '\').show();\" ';
               //str += 'onmouseout=\"jQuery(\'#hover' + type + picarr[i].wd_row_id + '\').hide();\" ';
               str += 'style=\"z-index:1;width:' + wd + 'px;height:auto;\">';
               
               str += '</div>';
               
               // hover-over image
               str += '<div style=\"position:relative;display:none;\" id=\"hover' + type + picarr[i].wd_row_id + '\" class=\"jsfimage_galleryhover\">';
               var alignhover = 'left';
               var left = 0;
               var hoverwidth = 370;
               if((usedimgcount % across) >= Math.round(across/2)) {
                  alignhover='right';
                  left = wd - hoverwidth;
               }
               usedimgcount++;
               str += '<div style=\"position:absolute;left:0px;top:0px;\">' + jsfimage_hoverimglarge(img,alignhover,left,picarr[i],'jsfimage_gallswap' + type + picarr[i].wd_row_id,hoverwidth) + '</div>';
               str += '</div>';
               
               // print out the image name
               var name = '';
               if(Boolean(picarr[i].name)) name = picarr[i].name;
               else if(Boolean(picarr[i].caption)) name = picarr[i].caption;
               else if(Boolean(picarr[i].title)) name = picarr[i].title;
               else if(Boolean(picarr[i].imagename)) name = picarr[i].imagename;
               else if(Boolean(picarr[i].filename)) name = picarr[i].filename;
               str += '<div style=\"width:' + wd + 'px;text-align:center;margin-top:3px;">';
               str += name;
               str += '</div>';
               
               // close box
               str += '</div>';
            }
         }
      }
      return str;
   }
   

   function jsfimage_hoverimglarge(img,alignhover,left,imgobj,closefunc,wd) {
      var str = '';
      var bclr = '#FFFFFF';
      var ht = wd - 50;
      //alert(JSON.stringify(imgobj));
   
      str += '<div style=\"position:relative;top:-10px;left:' + (left - 5) + 'px;width:' + (wd + 5) + 'px;height:' + ht + 'px;\">';
      
      str += '<div style=\"position:absolute;left:0px;top:0px;\">';
      
      
      var bclr2 = '#000000';   
      str += '<div style=\"position:relative;z-index:1;top:-2px;width:' + wd + 'px;height:' + ht + 'px;opacity:0.1;\">';
      str += '<div style=\"position:absolute;' + alignhover + ':30px;top:0px;width:50px;height:20px;overflow:hidden;\">';
      str += '<div style=\"float:left;width: 0;height: 0;border-top-width: 20px;border-top-style: solid;border-top-color: transparent;border-right-width: 25px;border-right-style: solid;border-right-color:' + bclr2 + ';\"></div>';
      str += '<div style=\"float:left;width: 0;height: 0;border-top-width: 20px;border-top-style: solid;border-top-color: transparent;border-left-width: 25px;border-left-style: solid;border-left-color:' + bclr2 + ';\"></div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      str += '<div style=\"position:absolute;top:20px;left:5px;z-index:20;width:' + (wd - 15) + 'px;height:' + (ht - 30) + 'px;overflow:hidden;background-color:' + bclr2 + ';border-radius:4px;\">';
      str += '</div>';   
      str += '</div>';
   
   
      
      str += '</div>';
      str += '<div style=\"position:absolute;left:0px;top:0px;\">';
      
      
      
      str += '<div style=\"position:relative;z-index:2;top:0px;left:5px;width:' + wd + 'px;height:' + ht + 'px;\">';
      
      str += '<div style=\"position:absolute;' + alignhover + ':30px;top:0px;width:50px;height:20px;overflow:hidden;\">';
      str += '<div style=\"float:left;width: 0;height: 0;border-top-width: 20px;border-top-style: solid;border-top-color: transparent;border-right-width: 25px;border-right-style: solid;border-right-color:' + bclr + ';\"></div>';
      str += '<div style=\"float:left;width: 0;height: 0;border-top-width: 20px;border-top-style: solid;border-top-color: transparent;border-left-width: 25px;border-left-style: solid;border-left-color:' + bclr + ';\"></div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
      str += '<div style=\"position:absolute;top:20px;left:5px;z-index:20;width:' + (wd - 15) + 'px;height:' + (ht - 30) + 'px;overflow:hidden;background-color:' + bclr + ';border-radius:4px;\">';
      str += '<div style=\"position:relative;width:' + (wd - 15) + 'px;height:' + (ht - 30) + 'px;\">';
      //str += '<div style=\"position:absolute;right:10px;top:10px;width:' + (wd-75) + 'px;height:' + (ht-40) + 'px;overflow:hidden;\">';
      str += '<div style=\"position:absolute;right:10px;top:10px;height:' + (ht-40) + 'px;overflow:hidden;\">';
      str += '<img src=\"' + img + '\" style=\"height:' + (ht-50) + 'px;width:auto;\">';
      //str += '<img src=\"' + replaceAll('https:','http:',img) + '\" style=\"height:' + (ht-50) + 'px;width:auto;\">';
      str += '</div>';
      str += '</div>';
      str += '</div>';
      
      str += '<div style=\"position:absolute;top:25px;left:' + (wd - 35) + 'px;z-index:21;cursor:pointer;\" onclick=\"event.stopPropagation();' + closefunc + '();\">';
      str += '<div style=\"position:relative;width:18px;height:18px;overflow:hidden;border-radius:9px;background-color:#EE5555;\">';   
      str += '<div style=\"position:absolute;top:2px;left:0px;width:18px;text-align:center;font-size:10px;color:#FFFFFF;font-weight:bold;\">';
      str += 'x';
      str += '</div>';
      str += '</div>';
      str += '</div>';
      
      str += '<div style=\"position:absolute;top:25px;left:' + 10 + 'px;z-index:21;\">';
      str += '<div style=\"margin-top:3px;margin-bottom:10px;width:60px;height:25px;overflow:hidden;text-align:center;font-weight:bold;font-size:10px;\">';
      str += 'Download';
      str += '</div>';
      
      var pdfimg = '';
      if(Boolean(imgobj.pdf)) pdfimg = imgobj.pdf;
      else if(Boolean(imgobj.pdfupload)) pdfimg = imgobj.pdfupload;
      else if(Boolean(imgobj.largepdf)) pdfimg = imgobj.largepdf;
      
      if(Boolean(pdfimg)) {
         if(!pdfimg.startsWith('http')) pdfimg = jsfimage_domain + 'jsfcode/srvyfiles/' + pdfimg;
         str += '<div ';
         str += 'style=\"margin-top:3px;margin-bottom:2px;width:60px;height:25px;overflow:hidden;text-align:center;font-size:10px;border-radius:5px;border:1px solid #555555;background-color:#FFFFFF;padding-top:5px;cursor:pointer;\" ';
         str += 'onclick=\"window.open(\'' + pdfimg + '\');rmp_trackitem(\'TermTool_DownloadPic\',\'' + imgobj.name + '\',\'' + imgobj.wd_row_id + '\',\'' + pdfimg + '\',\'' + jsfimage_userid + '\');\" ';
         str += '>';
         str += 'pdf';
         str += '</div>';
      }

      var jpgimg = '';
      if(Boolean(imgobj.jpeg)) jpgimg = imgobj.jpeg;
      else if(Boolean(imgobj.jpg)) jpgimg = imgobj.jpg;
      else if(Boolean(imgobj.jpgimage)) jpgimg = imgobj.jpgimage;
      else if(Boolean(imgobj.jpgupload)) jpgimg = imgobj.jpgupload;
      else if(Boolean(imgobj.largejpg)) jpgimg = imgobj.largejpg;
      
      if(Boolean(jpgimg)) {
         if(!jpgimg.startsWith('http')) jpgimg = jsfimage_domain + 'jsfcode/srvyfiles/' + jpgimg;
         str += '<div ';
         str += 'style=\"margin-top:3px;margin-bottom:2px;width:60px;height:25px;overflow:hidden;text-align:center;font-size:10px;border-radius:5px;border:1px solid #555555;background-color:#FFFFFF;padding-top:5px;cursor:pointer;\" ';
         str += 'onclick=\"window.open(\'' + jpgimg + '\');rmp_trackitem(\'TermTool_DownloadPic\',\'' + imgobj.name + '\',\'' + imgobj.wd_row_id + '\',\'' + jpgimg + '\',\'' + jsfimage_userid + '\');\" ';
         str += '>';
         str += 'jpg';
         str += '</div>';
      }

      
      var pngimg = '';
      if(Boolean(imgobj.png)) pngimg = imgobj.png;
      else if(Boolean(imgobj.pngimg)) pngimg = imgobj.pngimg;
      else if(Boolean(imgobj.pngimage)) pngimg = imgobj.pngimage;
      else if(Boolean(imgobj.pngupload)) pngimg = imgobj.pngupload;
      else if(Boolean(imgobj.largepng)) pngimg = imgobj.largepng;
      
      if(Boolean(pngimg)) {
         if(!pngimg.startsWith('http')) pngimg = jsfimage_domain + 'jsfcode/srvyfiles/' + pngimg;
         str += '<div ';
         str += 'style=\"margin-top:3px;margin-bottom:2px;width:60px;height:25px;overflow:hidden;text-align:center;font-size:10px;border-radius:5px;border:1px solid #555555;background-color:#FFFFFF;padding-top:5px;cursor:pointer;\" ';
         str += 'onclick=\"window.open(\'' + pngimg + '\');rmp_trackitem(\'TermTool_DownloadPic\',\'' + imgobj.name + '\',\'' + imgobj.wd_row_id + '\',\'' + pngimg + '\',\'' + jsfimage_userid + '\');\" ';
         str += '>';
         str += 'png';
         str += '</div>';
      }

      
      var epsimg = '';
      if(Boolean(imgobj.eps)) epsimg = imgobj.eps;
      else if(Boolean(imgobj.epsimage)) epsimg = imgobj.epsimage;
      else if(Boolean(imgobj.epsupload)) epsimg = imgobj.epsupload;
      else if(Boolean(imgobj.largeeps)) epsimg = imgobj.largeeps;
      
      if(Boolean(epsimg)) {
         if(!epsimg.startsWith('http')) epsimg = jsfimage_domain + 'jsfcode/srvyfiles/' + epsimg;
         str += '<div ';
         str += 'style=\"margin-top:3px;margin-bottom:2px;width:60px;height:25px;overflow:hidden;text-align:center;font-size:10px;border-radius:5px;border:1px solid #555555;background-color:#FFFFFF;padding-top:5px;cursor:pointer;\" ';
         str += 'onclick=\"window.open(\'' + epsimg + '\');rmp_trackitem(\'TermTool_DownloadPic\',\'' + imgobj.name + '\',\'' + imgobj.wd_row_id + '\',\'' + epsimg + '\',\'' + jsfimage_userid + '\');\" ';
         str += '>';
         str += 'eps';
         str += '</div>';
      }
      
      var tiffimg = '';
      if(Boolean(imgobj.tiff)) tiffimg = imgobj.tiff;
      else if(Boolean(imgobj.tiffimage)) tiffimg = imgobj.tiffimage;
      else if(Boolean(imgobj.tiffupload)) tiffimg = imgobj.tiffupload;
      else if(Boolean(imgobj.largetiff)) tiffimg = imgobj.largetiff;
      
      if(Boolean(tiffimg)) {
         if(!tiffimg.startsWith('http')) tiffimg = jsfimage_domain + 'jsfcode/srvyfiles/' + tiffimg;
         str += '<div ';
         str += 'style=\"margin-top:3px;margin-bottom:2px;width:60px;height:25px;overflow:hidden;text-align:center;font-size:10px;border-radius:5px;border:1px solid #555555;background-color:#FFFFFF;padding-top:5px;cursor:pointer;\" ';
         str += 'onclick=\"window.open(\'' + tiffimg + '\');rmp_trackitem(\'TermTool_DownloadPic\',\'' + imgobj.name + '\',\'' + imgobj.wd_row_id + '\',\'' + tiffimg + '\',\'' + jsfimage_userid + '\');\" ';
         str += '>';
         str += 'tiff';
         str += '</div>';
      }
      
      str += '</div>';
      
      
      //shadow
      for(var i=5;i<=10;i++) {
         str += '<div style=\"position:absolute;top:' + (20 + i) + 'px;left:' + (i - 5) + 'px;z-index:' + i + ';width:' + (wd - 10) + 'px;height:' + (ht - 30) + 'px;overflow:hidden;background-color:#2e2e2e;opacity:0.1;border-radius:5px;\">';
         str += '</div>';
      }
      str += '</div>';
      
      
      
      
      str += '</div>';
      str += '</div>';
      
      
      
      return str;
   }
   
   

   function jsfimage_categorizedata(jsondata){
      var numofcategories = 0;
      var rows;
      jsfimage_images = {};
      
      jsfimage_orderedcategories_use = [];
      
      if(Boolean(jsondata.rows) && jsondata.rows.length>0) rows = jsondata.rows;
      else if(Boolean(jsondata.results) && jsondata.results.length>0) rows = jsondata.results;
      else if(Boolean(jsondata.records) && jsondata.records.length>0) rows = jsondata.records;
      else if(Boolean(jsondata.users) && jsondata.users.length>0) rows = jsondata.users;
      
      for(var i=0;i<rows.length;i++) {
         //An image can be part of several categories
         //alert('category: ' + rows[i].category);
         var temparr = jsfimage_convertback(rows[i].category).split(',');
         for(var j=0;j<temparr.length;j++) {
            var disp = temparr[j].trim();
            if(Boolean(disp)) {
               //alert('disp: ' + disp);
               var descr;
               if(jsfimage_catexists(temparr[j])) {
                  disp = jsfimage_catexists(temparr[j],true);
                  descr = jsfimage_catexists(temparr[j],false,true);
                  //alert('category: ' + disp + ' descr: ' + descr);
               }
               
               var temp = jsfimage_flattenstr(disp);
               if(Boolean(temp)) {
                  if(!Boolean(jsfimage_images[temp])) {
                     jsfimage_images[temp] = {};
                     jsfimage_images[temp].counter = 0;
                     jsfimage_images[temp].display = disp;
                     jsfimage_images[temp].descr = descr;
                     jsfimage_images[temp].images = [];
                     numofcategories++;
                     
                     var tobj = {};
                     tobj.name = disp;
                     tobj.descr = descr;
                     tobj.value = disp;
                     jsfimage_orderedcategories_use.push(tobj);
                  }
                  jsfimage_images[temp].counter++;
                  jsfimage_images[temp].images.push(rows[i]);
               }
            }
         }
      }
      
      return numofcategories;
   }


///////////////////////////////////////////////
function jsfimage_QuickJSON(url,callback,checkcache){
   var runjson = true;
   var saveurl = '';
   if (Boolean(callback)) {
      saveurl = url;
      url = url + '&callback=' + encodeURIComponent(callback);
      
      //alert('URL: ' + url);
      
      if(Boolean(checkcache)) {
         //alert('checking cache: ' + url);
         var str = window.localStorage.getItem('jsfimage_cache');
         if(Boolean(str)){
            //alert('found cache: ' + url);
            var jsf_cache = JSON.parse(str);
            if(jsf_cache.expiry<(Math.floor(Date.now() / 1000))) {
               //alert('expired cache: ' + url);
               jsf_cache = '';
               window.localStorage.removeItem('jsfimage_cache');
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
         if(Boolean(checkcache)) url += '&jsonsaveval=' + encodeURIComponent(saveurl);
         //alert('NOT using cache: ' + url);
         jsfimage_CallJSONP(url);
      }
   }
   return saveurl;
}

function jsfimage_ReturnJSON(jsondata){
   //jsfimage_hideloading();
   //alert(JSON.stringify(jsondata));
   if (Boolean(jsondata) && Boolean(jsondata.jsonsaveval)) {
      //alert('CHJ***** checking cache: jsf_endjsoning  url: ' + jsondata.jsonsaveval);
      var jsf_cache = {};
      jsf_cache.expiry = (Math.floor(Date.now() / 1000) + (60*60*24));
      jsf_cache.countindex = 0;
      var str = window.localStorage.getItem('jsfimage_cache');
      window.localStorage.removeItem('jsfimage_cache');
      if(Boolean(str)) {
         //alert('found jsf_cache, checking expiry...');
         temp = JSON.parse(str);
         if(Boolean(temp) && temp.expiry>(Math.floor(Date.now() / 1000)) && temp.countindex<150) {
            jsf_cache = temp;
         }
      }
      jsf_cache.countindex++;
      jsf_cache[jsondata.jsonsaveval] = jsondata;
      window.localStorage.setItem('jsfimage_cache',JSON.stringify(jsf_cache));
   }
}

function jsfimage_CallJSONP(url) {
    var script = document.createElement('script');
    script.setAttribute('src', url);
    document.getElementsByTagName('head')[0].appendChild(script);
}
 


function jsfimage_replaceAll(find, replacewith, str) {
   //alert(typeof str);
   if(!Boolean(str) || typeof str !== 'string') return '';
   else return str.replace(new RegExp(find, 'g'), replacewith);
}

function jsfimage_flattenstr(str) {
   var newstr = '';
   if(Boolean(str)) {
      newstr = str.toLowerCase();
      newstr = jsfimage_replaceAll('_','',newstr);
      newstr = jsfimage_replaceAll('-','',newstr);
      newstr = jsfimage_replaceAll('\'','',newstr);
      newstr = jsfimage_replaceAll('\"','',newstr);
      newstr = jsfimage_replaceAll('&nbsp;','',newstr);
      newstr = jsfimage_replaceAll(' ','',newstr);
   }
   return newstr;
}

function jsfimage_convertback(str) {
   str = jsfimage_replaceAll('%E%','',str);
   str = jsfimage_replaceAll('&#44;',',',str);
   str = jsfimage_replaceAll('&nbsp;',' ',str);
   return str;
}
