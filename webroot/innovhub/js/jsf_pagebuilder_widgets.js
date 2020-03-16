var jsfpb_newsmedia = {};
var jsfpb_customtypes = [];

jsfpb_customtypes.push('Checklist Item');
jsfpb_customtypes.push('Sponsor Box');
jsfpb_customtypes.push('Tile Box');
jsfpb_customtypes.push('Resource Box');
jsfpb_customtypes.push('Formatted Content');
jsfpb_customtypes.push('Title Banner');
jsfpb_customtypes.push('Title Banner 2');
jsfpb_customtypes.push('TT Format');
jsfpb_customtypes.push('YouTube Video');
jsfpb_customtypes.push('Blue Callout');
jsfpb_customtypes.push('Embedded Video');
jsfpb_customtypes.push('jData Form');
jsfpb_customtypes.push('Image Gallery');
jsfpb_customtypes.push('Buttons');
jsfpb_customtypes.push('Download');
jsfpb_customtypes.push('Image & HTML');
jsfpb_customtypes.push('News & Media');
jsfpb_customtypes.push('JData List');
jsfpb_customtypes.push('Icon, Header, and Text');
//alert('custom types: ' + JSON.stringify(jsfpb_customtypes));

function jsfpb_customadmin(type,r,s,l) {
   var divid = r + '_' + s + '_' + l;
   var str = '';
   if(type=='Checklist Item') {
      str += '<div id=\"' + divid + '_ttldiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Checklist Title';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_ttl\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
      //str += '<div id=\"' + divid + '_addldiv\" style=\"position:relative;\">';
      //str += '<div style=\"float:left;width:180px;\">';
      //str += 'Additional Text';
      //str += '</div>';
      //str += '<div style=\"float:left;width:220px;\">';
      //str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_addl\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      //str += '</div>';
      //str += '<div style=\"clear:both;\"></div>';
      //str += '</div>';
   } else if(type=='Sponsor Box') {
      //str += '<div id=\"' + divid + '_ttldiv\" style=\"position:relative;\">';
      //str += '<div style=\"float:left;width:180px;\">';
      //str += 'Resource Title';
      //str += '</div>';
      //str += '<div style=\"float:left;width:220px;\">';
      //str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_ttl\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      //str += '</div>';
      //str += '<div style=\"clear:both;\"></div>';
      //str += '</div>';
      
      //str += '<div id=\"' + divid + '_urldiv\" style=\"position:relative;\">';
      //str += '<div style=\"float:left;width:180px;\">';
      //str += 'Resource Link';
      //str += '</div>';
      //str += '<div style=\"float:left;width:220px;\">';
      //str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_url\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      //str += '</div>';
      //str += '<div style=\"clear:both;\"></div>';
      //str += '</div>';
      
      //str += '<div ';
      //str += 'id=\"' + divid + '_imgbtndiv\" ';
      //str += 'style=\"width:80px;text-align:center;font-size:10px;color:#333333;cursor:pointer;background-color:#F1F1F1;padding:4px;border:1px solid #444444;border-radius:3px;\" ';
      //str += 'onclick=\"window.open(\'' + jsfpb_domain + 'jsfcode/uploadimage.php?userid=9&token=9&prefix=' + r + '&wd_id=' + s + '&field_id=' + l + '\');\"';
      //str += '>Select Image</div>';
      //str += '<div id=\"' + divid + '_imgdiv\" style=\"margin-top:5px;position:relative;\"></div>';
   } else if(type=='Tile Box') {
      
   } else if(type=='Resource Box') {
      str += '<div id=\"' + divid + '_ttldiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Resource Title';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_ttl\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
      //str += '<div id=\"' + divid + '_urldiv\" style=\"position:relative;\">';
      //str += '<div style=\"float:left;width:180px;\">';
      //str += 'Resource Link';
      //str += '</div>';
      //str += '<div style=\"float:left;width:220px;\">';
      //str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_url\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      //str += '</div>';
      //str += '<div style=\"clear:both;\"></div>';
      //str += '</div>';
      
      //str += '<div ';
      //str += 'id=\"' + divid + '_imgbtndiv\" ';
      //str += 'style=\"width:80px;text-align:center;font-size:10px;color:#333333;cursor:pointer;background-color:#F1F1F1;padding:4px;border:1px solid #444444;border-radius:3px;\" ';
      //str += 'onclick=\"window.open(\'' + jsfpb_domain + 'jsfcode/uploadimage.php?userid=9&token=9&prefix=' + r + '&wd_id=' + s + '&field_id=' + l + '\');\"';
      //str += '>Select Image</div>';
      //str += '<div id=\"' + divid + '_imgdiv\" style=\"margin-top:5px;position:relative;\"></div>';
   } else if(type=='Formatted Content') {
      str += '<div id=\"' + divid + '_hdrdiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Heading (optional)';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_hdr\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
      str += '<div id=\"' + divid + '_ttldiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Title';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_ttl\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
 
      str += '<div id=\"' + divid + '_brdrdiv\" style=\"position:relative;\">';
      str += '<div style=\"width:220px;margin-top:5px;margin-bottom:5px;\">';
      str += '<input type=\"checkbox\" value=\"1\" onchange=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" id=\"' + divid + '_brdr\">';
      str += ' Draw line bordering bottom';
      str += '</div>';
      str += '</div>';
            
      str += '<div id=\"' + divid + '_cirdiv\" style=\"position:relative;\">';
      str += '<div style=\"width:220px;margin-top:5px;margin-bottom:5px;\">';
      str += '<input type=\"checkbox\" value=\"1\" onchange=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" id=\"' + divid + '_cir\">';
      str += ' Draw circle around image';
      str += '</div>';
      str += '</div>';
            
      str += '<div id=\"' + divid + '_circlrdiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Color of Circle';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_circlr\" style=\"width:100px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
       
      str += '<div id=\"' + divid + '_plsdiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += '\"Plus\" Text (optional)';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_pls\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
      str += '<div id=\"' + divid + '_plsurldiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += '\"Plus\" URL (optional)';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_plsurl\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
      str += '<div id=\"' + divid + '_creddiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Reference (optional)';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_cred\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
   } else if(type=='Title Banner') {
      str += '<div id=\"' + divid + '_ttldiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Banner Title';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_ttl\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
      str += '<div id=\"' + divid + '_tntdiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Banner tint color';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_tnt\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
      str += '<div id=\"' + divid + '_lbl1div\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Bold Label';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_lbl1\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
      str += '<div id=\"' + divid + '_lbl2div\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Regular Label';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_lbl2\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
   } else if(type=='Title Banner 2') {
      str += '<div id=\"' + divid + '_ttldiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Banner Title';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_ttl\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
      str += '<div id=\"' + divid + '_subttldiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Banner Sub-Title';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_subttl\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
      str += '<div id=\"' + divid + '_grydiv\" style=\"position:relative;\">';
      str += '<div style=\"width:220px;margin-top:5px;margin-bottom:5px;\">';
      str += '<input type=\"checkbox\" value=\"1\" onchange=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" id=\"' + divid + '_gry\">';
      str += ' Greyscale';
      str += '</div>';
      str += '</div>';
      
      str += '<div id=\"' + divid + '_tntdiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Banner tint color';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_tnt\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
      str += '<div id=\"' + divid + '_lbl1div\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Bold Label';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_lbl1\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
      str += '<div id=\"' + divid + '_lbl2div\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Regular Label';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_lbl2\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
      str += '<div id=\"' + divid + '_pdfdiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'PDF download link';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_pdf\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
   } else if(type=='TT Format') {
      str += '<div id=\"' + divid + '_ttldiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Banner Title';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_ttl\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
      str += '<div id=\"' + divid + '_subttldiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Banner Sub-Title';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_subttl\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
      str += '<div id=\"' + divid + '_grydiv\" style=\"position:relative;\">';
      str += '<div style=\"width:220px;margin-top:5px;margin-bottom:5px;\">';
      str += '<input type=\"checkbox\" value=\"1\" onchange=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" id=\"' + divid + '_gry\">';
      str += ' Greyscale';
      str += '</div>';
      str += '</div>';
      
      str += '<div id=\"' + divid + '_tntdiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Banner tint color';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_tnt\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
      str += '<div id=\"' + divid + '_lbl1div\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Button Label';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_lbl1\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
      str += '<div id=\"' + divid + '_lbl2div\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Button URL';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_lbl2\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
      /*
      str += '<div id=\"' + divid + '_pdfdiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'PDF download link';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_pdf\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      */
      
   } else if(type=='YouTube Video') {
      str += '<div id=\"' + divid + '_ytiddiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'YouTube Video ID';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_ytid\" style=\"width:100px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
   } else if(type=='Blue Callout') {
      str += '<div id=\"' + divid + '_ttldiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Sub-text';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_ttl\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
   } else if(type=='Embedded Video') {
      str += '<div id=\"' + divid + '_ttldiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'MP4 file location';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_ttl\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
   } else if(type=='jData Form') {
      //nothing additional needed
      
   } else if(type=='Image Gallery') {
      //nothing additional needed
      
   } else if(type=='Buttons') {
      str += '<div id=\"' + divid + '_ttldiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Title';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_ttl\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
      str += '<div id=\"' + divid + '_subttldiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Sub-Title';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_subttl\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
      str += '<div id=\"' + divid + '_sidediv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Buttons Alignment';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<select id=\"' + divid + '_side\" onchange=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\">';
      str += '<option value=\"center\">Center</option>';
      str += '<option value=\"left\">Left</option>';
      str += '<option value=\"right\">Right</option>';
      str += '</select>';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
      for(var i=1;i<=5;i++) {
         str += '<div style=\"margin-top:8px;margin-bottom:3px;\">';

         str += '<div id=\"' + divid + '_btn' + i + 'div\" style=\"position:relative;\">';
         str += '<div style=\"float:left;width:180px;\">';
         str += 'Button ' + i + ' Label';
         str += '</div>';
         str += '<div style=\"float:left;width:220px;\">';
         str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_btn' + i + '\" style=\"width:200px;font-size:10px;font-family:arial;\">';
         str += '</div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
         
         str += '<div id=\"' + divid + '_url' + i + 'div\" style=\"position:relative;\">';
         str += '<div style=\"float:left;width:180px;\">';
         str += 'Button ' + i + ' URL';
         str += '</div>';
         str += '<div style=\"float:left;width:220px;\">';
         str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_url' + i + '\" style=\"width:200px;font-size:10px;font-family:arial;\">';
         str += '</div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
         
         str += '<div id=\"' + divid + '_bg' + i + 'div\" style=\"position:relative;\">';
         str += '<div style=\"float:left;width:180px;\">';
         str += 'Button ' + i + ' BG Color';
         str += '</div>';
         str += '<div style=\"float:left;width:220px;\">';
         str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_bg' + i + '\" style=\"width:200px;font-size:10px;font-family:arial;\">';
         str += '</div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
         
         str += '<div id=\"' + divid + '_fg' + i + 'div\" style=\"position:relative;\">';
         str += '<div style=\"float:left;width:180px;\">';
         str += 'Button ' + i + ' FG Color';
         str += '</div>';
         str += '<div style=\"float:left;width:220px;\">';
         str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_fg' + i + '\" style=\"width:200px;font-size:10px;font-family:arial;\">';
         str += '</div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
         
         
         str += '</div>';
      }
      
   } else if(type=='Download') {
      //nothing additional needed
   } else if(type=='Image & HTML') {
      //alert('here');
      str += '\n<script>\n';
      str += 'jQuery(\'#' + divid + '_mpadlbl\').html(\'Max width of image\');\n';
      str += '\n</script>\n';
   } else if(type=='News & Media') {
      str += '<div id=\"' + divid + '_wdiddiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'JData Name';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_wdid\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
   
      str += '<div id=\"' + divid + '_secdiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Title of Section';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_sec\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
   
      str += '<div id=\"' + divid + '_htagdiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Search Hashtag';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_htag\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
   
      str += '<div id=\"' + divid + '_typdiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Type of content';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_typ\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
      
      str += '<div id=\"' + divid + '_stydiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Layout Style';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<select id=\"' + divid + '_sty\" onchange=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\">';         
      str += '<option value=\"1\">Name only</option>';
      str += '<option value=\"2\">Name and description only</option>';
      str += '<option value=\"3\">Image on left, Name, Description, and By Line</option>';
      str += '<option value=\"4\">Video on page</option>';
      str += '</select>';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
   } else if(type=='JData List') {
      str += '<div id=\"' + divid + '_wdiddiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'JData Name';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_wdid\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
   
      str += '<div id=\"' + divid + '_secdiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Section Title';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_sec\" style=\"width:200px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
   
      str += '<div id=\"' + divid + '_colsdiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Number of Columns';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_cols\" style=\"width:100px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
   
      str += '<div id=\"' + divid + '_colwddiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Min Column width';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_colwd\" style=\"width:100px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
   
      str += '<div id=\"' + divid + '_colmaxdiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Max Column width';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_colmax\" style=\"width:100px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
   
      str += '<input type=\"hidden\" id=\"' + divid + '_param\" value=\"\">';
      str += '<div id=\"' + divid + '_paramdiv\" style=\"position:relative;margin-top:8px;margin-bottom:8px;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Search Parameters';
      str += '</div>';
      str += '<div id=\"' + divid + '_param_list\" style=\"float:left;width:280px;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
   
      str += '<div id=\"' + divid + '_stydiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Layout Style';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<select id=\"' + divid + '_sty\" onchange=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\">';         
      str += '<option value=\"1\">Name only</option>';
      str += '<option value=\"2\">Name and description only</option>';
      str += '<option value=\"3\">Image on left, Name, Description, and By Line</option>';
      str += '<option value=\"4\">Video on page</option>';
      str += '<option value=\"5\">Resources List</option>';
      str += '<option value=\"500\">Custom definition</option>';
      str += '</select>';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '<div id=\"' + divid + '_styinfo\"></div>';
      str += '</div>';
      
      str += '<div id=\"' + divid + '_tmpltdiv\" style=\"position:relative;display:none;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Custom Template';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<textarea onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" id=\"' + divid + '_tmplt\" style=\"width:200px;height:100px;font-size:10px;font-family:arial;\"></textarea>';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
   } else if(type=='Icon, Header, and Text') {
      str += '<div id=\"' + divid + '_ibgdiv\" style=\"position:relative;\">';
      str += '<div style=\"float:left;width:180px;\">';
      str += 'Icon Background Color';
      str += '</div>';
      str += '<div style=\"float:left;width:220px;\">';
      str += '<input onkeyup=\"jsfpb_changeLayer(' + r + ',' + s + ',' + l + ');\" type=\"text\" id=\"' + divid + '_ibg\" style=\"width:90px;font-size:10px;font-family:arial;\">';
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
   }
   
   return str;
}

function jsfpb_custompopulate(type,r,s,l) {
   var divid = r + '_' + s + '_' + l;
   var layer = jsfpb_page.rows[r].slots[s].layers[l];   
   
   if(type=='Checklist Item') {
      if (Boolean(layer.ttl)) jQuery('#' + divid + '_ttl').val(jsfpb_convertback(layer.ttl));
      //if (Boolean(layer.addl)) jQuery('#' + divid + '_addl').val(layer.addl);
      jQuery('#' + divid + '_contentdiv').show();
      
   } else if(type=='Sponsor Box') {
      //if (Boolean(layer.ttl)) jQuery('#' + divid + '_ttl').val(jsfpb_convertback(layer.ttl));
      //if (Boolean(layer.url)) jQuery('#' + divid + '_url').val(layer.url);   
      //if (Boolean(layer.img)) {
      //   var img = '<img src=\"' + layer.img + '\" style=\"max-width:120px;max-height:80px;width:auto;height:auto;\">';
      //   img += '<div onclick=\"if(confirm(\'Are you sure you want to permanently remove this image?\')) { jQuery(\'#' + divid + '_imgdiv\').html(\'\'); jsfpb_page.rows[' + r + '].slots[' + s + '].layers[' + l + '].img = \'\'; jsfpb_changeswaiting=1; }\" style=\"position:absolute;top:3px;left:3px;font-weight:bold;color:red;cursor:pointer;font-family:arial;font-size:10px;background-color:#EEEEEE;width:12px;height:12px;overflow:hidden;border-radius:4px;text-align:center;\">x</div>';
      //   img += '<div onclick=\"window.open(\'' + layer.img + '\');\" style=\"position:absolute;top:3px;left:18px;font-weight:bold;color:green;cursor:pointer;font-family:arial;font-size:10px;background-color:#EEEEEE;width:12px;height:12px;overflow:hidden;border-radius:4px;text-align:center;\">+</div>';
      //   jQuery('#' + divid + '_imgdiv').html(img);
      //}
      jQuery('#' + divid + '_contentdiv').show();
      jQuery('#' + divid + '_imgbtndiv').show();
      jQuery('#' + divid + '_imgdiv').show();
   } else if(type=='Tile Box') {
      jQuery('#' + divid + '_contentdiv').show();
      jQuery('#' + divid + '_imgbtndiv').show();
      jQuery('#' + divid + '_imgdiv').show();
   } else if(type=='Resource Box') {
      if (Boolean(layer.ttl)) jQuery('#' + divid + '_ttl').val(jsfpb_convertback(layer.ttl));
      //if (Boolean(layer.url)) jQuery('#' + divid + '_url').val(layer.url);   
      //if (Boolean(layer.img)) {
      //   var img = '<img src=\"' + layer.img + '\" style=\"max-width:120px;max-height:80px;width:auto;height:auto;\">';
      //   img += '<div onclick=\"if(confirm(\'Are you sure you want to permanently remove this image?\')) { jQuery(\'#' + divid + '_imgdiv\').html(\'\'); jsfpb_page.rows[' + r + '].slots[' + s + '].layers[' + l + '].img = \'\'; jsfpb_changeswaiting=1; }\" style=\"position:absolute;top:3px;left:3px;font-weight:bold;color:red;cursor:pointer;font-family:arial;font-size:10px;background-color:#EEEEEE;width:12px;height:12px;overflow:hidden;border-radius:4px;text-align:center;\">x</div>';
      //   img += '<div onclick=\"window.open(\'' + layer.img + '\');\" style=\"position:absolute;top:3px;left:18px;font-weight:bold;color:green;cursor:pointer;font-family:arial;font-size:10px;background-color:#EEEEEE;width:12px;height:12px;overflow:hidden;border-radius:4px;text-align:center;\">+</div>';
      //   jQuery('#' + divid + '_imgdiv').html(img);
      //}
      jQuery('#' + divid + '_contentdiv').show();
      jQuery('#' + divid + '_imgbtndiv').show();
      jQuery('#' + divid + '_imgdiv').show();
   } else if(type=='Formatted Content') {
      if (Boolean(layer.brdr)) document.getElementById(divid + '_brdr').checked = true;
      else document.getElementById(divid + '_brdr').checked = false;
      if (Boolean(layer.cir)) document.getElementById(divid + '_cir').checked = true;
      else document.getElementById(divid + '_cir').checked = false;
      if (Boolean(layer.circlr)) jQuery('#' + divid + '_circlr').val(jsfpb_convertback(layer.circlr));
      
      if (Boolean(layer.hdr)) jQuery('#' + divid + '_hdr').val(jsfpb_convertback(layer.hdr));
      if (Boolean(layer.ttl)) jQuery('#' + divid + '_ttl').val(jsfpb_convertback(layer.ttl));
      if (Boolean(layer.pls)) jQuery('#' + divid + '_pls').val(jsfpb_convertback(layer.pls));
      if (Boolean(layer.plsurl)) jQuery('#' + divid + '_plsurl').val(layer.plsurl);
      if (Boolean(layer.cred)) jQuery('#' + divid + '_cred').val(jsfpb_convertback(layer.cred));
      jQuery('#' + divid + '_imgbtndiv').show();
      jQuery('#' + divid + '_imgdiv').show();
      jQuery('#' + divid + '_contentdiv').show();
      
      //180214 exposing button field
      jQuery('#' + divid + '_mbtn1div').show();
      jQuery('#' + divid + '_murl1div').show();
      jQuery('#' + divid + '_mbg1div').show();
      jQuery('#' + divid + '_mfg1div').show();
      
      
      if(document.getElementById(divid + '_cir').checked) {
         jQuery('#' + divid + '_circlrdiv').fadeIn(300);
      } else {
         jQuery('#' + divid + '_circlrdiv').fadeOut(300);
      }
      
   } else if(type=='Title Banner') {
      if (Boolean(layer.ttl)) jQuery('#' + divid + '_ttl').val(jsfpb_convertback(layer.ttl));
      if (Boolean(layer.tnt)) jQuery('#' + divid + '_tnt').val(layer.tnt);
      if (Boolean(layer.lbl1)) jQuery('#' + divid + '_lbl1').val(jsfpb_convertback(layer.lbl1));
      if (Boolean(layer.lbl2)) jQuery('#' + divid + '_lbl2').val(jsfpb_convertback(layer.lbl2));
      jQuery('#' + divid + '_imgbtndiv').show();
      jQuery('#' + divid + '_imgdiv').show();      
   } else if(type=='Title Banner 2') {
      if (Boolean(layer.ttl)) jQuery('#' + divid + '_ttl').val(jsfpb_convertback(layer.ttl));
      if (Boolean(layer.subttl)) jQuery('#' + divid + '_subttl').val(jsfpb_convertback(layer.subttl));
      
      if (Boolean(layer.gry)) document.getElementById(divid + '_gry').checked = true;
      else document.getElementById(divid + '_gry').checked = false;
      
      if (Boolean(layer.tnt)) jQuery('#' + divid + '_tnt').val(layer.tnt);
      if (Boolean(layer.pdf)) jQuery('#' + divid + '_pdf').val(layer.pdf);
      if (Boolean(layer.lbl1)) jQuery('#' + divid + '_lbl1').val(jsfpb_convertback(layer.lbl1));
      if (Boolean(layer.lbl2)) jQuery('#' + divid + '_lbl2').val(jsfpb_convertback(layer.lbl2));
      jQuery('#' + divid + '_imgbtndiv').show();
      jQuery('#' + divid + '_imgdiv').show();      
      jQuery('#' + divid + '_contentdiv').show();
   } else if(type=='TT Format') {
      if (Boolean(layer.ttl)) jQuery('#' + divid + '_ttl').val(jsfpb_convertback(layer.ttl));
      if (Boolean(layer.subttl)) jQuery('#' + divid + '_subttl').val(jsfpb_convertback(layer.subttl));
      
      if (Boolean(layer.gry)) document.getElementById(divid + '_gry').checked = true;
      else document.getElementById(divid + '_gry').checked = false;
      
      if (Boolean(layer.tnt)) jQuery('#' + divid + '_tnt').val(layer.tnt);
      if (Boolean(layer.pdf)) jQuery('#' + divid + '_pdf').val(layer.pdf);
      if (Boolean(layer.lbl1)) jQuery('#' + divid + '_lbl1').val(jsfpb_convertback(layer.lbl1));
      if (Boolean(layer.lbl2)) jQuery('#' + divid + '_lbl2').val(jsfpb_convertback(layer.lbl2));
      jQuery('#' + divid + '_imgbtndiv').show();
      jQuery('#' + divid + '_imgdiv').show();      
      jQuery('#' + divid + '_contentdiv').show();
   } else if(type=='YouTube Video') {
      if (Boolean(layer.ytid)) jQuery('#' + divid + '_ytid').val(layer.ytid);
      jQuery('#' + divid + '_contentdiv').show();
   } else if(type=='Blue Callout') {
      if (Boolean(layer.ttl)) jQuery('#' + divid + '_ttl').val(jsfpb_convertback(layer.ttl));
      jQuery('#' + divid + '_contentdiv').show();
   } else if(type=='Embedded Video') {
      if (Boolean(layer.ttl)) jQuery('#' + divid + '_ttl').val(jsfpb_convertback(layer.ttl));
      jQuery('#' + divid + '_contentdiv').show();
      jQuery('#' + divid + '_imgbtndiv').show();
      jQuery('#' + divid + '_imgdiv').show();      
   } else if(type=='jData Form') {
      jQuery('#' + divid + '_contentdiv').show();
      jQuery('#' + divid + '_imgbtndiv').hide();
      jQuery('#' + divid + '_imgdiv').hide();      
   } else if(type=='Image Gallery') {
      jQuery('#' + divid + '_contentdiv').show();
      jQuery('#' + divid + '_imgbtndiv').hide();
      jQuery('#' + divid + '_imgdiv').hide();      
   } else if(type=='Buttons') {
      if (Boolean(layer.ttl)) jQuery('#' + divid + '_ttl').val(jsfpb_convertback(layer.ttl));
      if (Boolean(layer.subttl)) jQuery('#' + divid + '_subttl').val(jsfpb_convertback(layer.subttl));
      if (Boolean(layer.side)) jQuery('#' + divid + '_side').val(jsfpb_convertback(layer.side));
      for(var i=1;i<=5;i++) {
         if (Boolean(layer['btn' + i])) jQuery('#' + divid + '_btn' + i).val(jsfpb_convertback(layer['btn' + i]));
         if (Boolean(layer['url' + i])) jQuery('#' + divid + '_url' + i).val(jsfpb_convertback(layer['url' + i]));
         if (Boolean(layer['bg' + i])) jQuery('#' + divid + '_bg' + i).val(jsfpb_convertback(layer['bg' + i]));
         if (Boolean(layer['fg' + i])) jQuery('#' + divid + '_fg' + i).val(jsfpb_convertback(layer['fg' + i]));
      }
      jQuery('#' + divid + '_contentdiv').show();
      jQuery('#' + divid + '_imgbtndiv').hide();
      jQuery('#' + divid + '_imgdiv').hide();      
   } else if(type=='Download') {
      jQuery('#' + divid + '_imgbtndiv').show();
      jQuery('#' + divid + '_imgdiv').show();      
      jQuery('#' + divid + '_contentdiv').show();
   } else if(type=='Image & HTML') {
      jQuery('#' + divid + '_contentdiv').show();
      jQuery('#' + divid + '_imgbtndiv').show();
      jQuery('#' + divid + '_imgdiv').show();
      //jQuery('#' + divid + '_mttldiv').show();
      //jQuery('#' + divid + '_msubttldiv').show();
      jQuery('#' + divid + '_mpaddiv').show();
   } else if(type=='News & Media') {
      if(Boolean(layer.sec)) jQuery('#' + divid + '_sec').val(layer.sec);
      if(Boolean(layer.wdid)) jQuery('#' + divid + '_wdid').val(layer.wdid);
      if(Boolean(layer.typ)) jQuery('#' + divid + '_typ').val(layer.typ);
      if(Boolean(layer.sty)) jQuery('#' + divid + '_sty').val(layer.sty);
      if(Boolean(layer.htag)) jQuery('#' + divid + '_htag').val(layer.htag);
   } else if(type=='JData List') {
      if(Boolean(layer.wdid)) jQuery('#' + divid + '_wdid').val(jsfpb_convertback(layer.wdid));
      if(Boolean(layer.sec)) jQuery('#' + divid + '_sec').val(jsfpb_convertback(layer.sec));
      if(Boolean(layer.cols)) jQuery('#' + divid + '_cols').val(layer.cols);
      if(Boolean(layer.colwd)) jQuery('#' + divid + '_colwd').val(layer.colwd);
      if(Boolean(layer.colmax)) jQuery('#' + divid + '_colmax').val(layer.colmax);
      if(Boolean(layer.sty)) jQuery('#' + divid + '_sty').val(layer.sty);
      if(Boolean(layer.tmplt)) jQuery('#' + divid + '_tmplt').val(jsfpb_convertback(layer.tmplt));
      if(Boolean(layer.param)) {
         jQuery('#' + divid + '_param').val(layer.param);
      }
      jsfpb_showparams(divid + '_param');
      if(layer.sty=='500') jQuery('#' + divid + '_tmpltdiv').show();
   } else if(type=='Icon, Header, and Text') {
      jQuery('#' + divid + '_contentdiv').show();
      jQuery('#' + divid + '_imgbtndiv').show();
      jQuery('#' + divid + '_imgdiv').show();
      jQuery('#' + divid + '_mttldiv').show();
      //jQuery('#' + divid + '_msubttldiv').show();
      if(Boolean(layer.ibg)) jQuery('#' + divid + '_ibg').val(layer.ibg);
   }
}

function jsfpb_customchangelayer(type,r,s,l) {
   var divid = r + '_' + s + '_' + l;
   if(type=='Checklist Item') {
      jsfpb_page.rows[r].slots[s].layers[l].ttl = jsfpb_convertstring(jQuery('#' + divid + '_ttl').val());
      //jsfpb_page.rows[r].slots[s].layers[l].addl = jQuery('#' + divid + '_addl').val();
   } else if(type=='Sponsor Box') {
      //jsfpb_page.rows[r].slots[s].layers[l].ttl = jsfpb_convertstring(jQuery('#' + divid + '_ttl').val());
      //jsfpb_page.rows[r].slots[s].layers[l].url = jQuery('#' + divid + '_url').val();
   } else if(type=='Tile Box') {
   } else if(type=='Resource Box') {
      jsfpb_page.rows[r].slots[s].layers[l].ttl = jsfpb_convertstring(jQuery('#' + divid + '_ttl').val());
      //jsfpb_page.rows[r].slots[s].layers[l].url = jQuery('#' + divid + '_url').val();
   } else if(type=='Formatted Content') {
      if (Boolean(jQuery('#' + divid + '_brdr').val())) jsfpb_page.rows[r].slots[s].layers[l].brdr = document.getElementById(divid + '_brdr').checked;
      if (Boolean(jQuery('#' + divid + '_cir').val())) jsfpb_page.rows[r].slots[s].layers[l].cir = document.getElementById(divid + '_cir').checked;
      jsfpb_page.rows[r].slots[s].layers[l].circlr = jsfpb_convertstring(jQuery('#' + divid + '_circlr').val());
      jsfpb_page.rows[r].slots[s].layers[l].hdr = jsfpb_convertstring(jQuery('#' + divid + '_hdr').val());
      jsfpb_page.rows[r].slots[s].layers[l].ttl = jsfpb_convertstring(jQuery('#' + divid + '_ttl').val());
      jsfpb_page.rows[r].slots[s].layers[l].pls = jsfpb_convertstring(jQuery('#' + divid + '_pls').val());
      jsfpb_page.rows[r].slots[s].layers[l].plsurl = jQuery('#' + divid + '_plsurl').val();
      jsfpb_page.rows[r].slots[s].layers[l].cred = jsfpb_convertstring(jQuery('#' + divid + '_cred').val());
      
      if(Boolean(document.getElementById(divid + '_cir')) && document.getElementById(divid + '_cir').checked) {
         jQuery('#' + divid + '_circlrdiv').fadeIn(300);
      } else {
         jQuery('#' + divid + '_circlrdiv').fadeOut(300);
      }
            
   } else if(type=='Title Banner') {
      jsfpb_page.rows[r].slots[s].layers[l].ttl = jsfpb_convertstring(jQuery('#' + divid + '_ttl').val());
      jsfpb_page.rows[r].slots[s].layers[l].tnt = jQuery('#' + divid + '_tnt').val();
      jsfpb_page.rows[r].slots[s].layers[l].lbl1 = jsfpb_convertstring(jQuery('#' + divid + '_lbl1').val());
      jsfpb_page.rows[r].slots[s].layers[l].lbl2 = jsfpb_convertstring(jQuery('#' + divid + '_lbl2').val());
   } else if(type=='Title Banner 2') {
      jsfpb_page.rows[r].slots[s].layers[l].ttl = jsfpb_convertstring(jQuery('#' + divid + '_ttl').val());
      jsfpb_page.rows[r].slots[s].layers[l].subttl = jsfpb_convertstring(jQuery('#' + divid + '_subttl').val());
      jsfpb_page.rows[r].slots[s].layers[l].tnt = jQuery('#' + divid + '_tnt').val();
      if (Boolean(jQuery('#' + divid + '_gry').val())) jsfpb_page.rows[r].slots[s].layers[l].gry = document.getElementById(divid + '_gry').checked;
      jsfpb_page.rows[r].slots[s].layers[l].pdf = jQuery('#' + divid + '_pdf').val();
      jsfpb_page.rows[r].slots[s].layers[l].lbl1 = jsfpb_convertstring(jQuery('#' + divid + '_lbl1').val());
      jsfpb_page.rows[r].slots[s].layers[l].lbl2 = jsfpb_convertstring(jQuery('#' + divid + '_lbl2').val());
   } else if(type=='TT Format') {
      jsfpb_page.rows[r].slots[s].layers[l].ttl = jsfpb_convertstring(jQuery('#' + divid + '_ttl').val());
      jsfpb_page.rows[r].slots[s].layers[l].subttl = jsfpb_convertstring(jQuery('#' + divid + '_subttl').val());
      jsfpb_page.rows[r].slots[s].layers[l].tnt = jQuery('#' + divid + '_tnt').val();
      if (Boolean(jQuery('#' + divid + '_gry').val())) jsfpb_page.rows[r].slots[s].layers[l].gry = document.getElementById(divid + '_gry').checked;
      jsfpb_page.rows[r].slots[s].layers[l].pdf = jQuery('#' + divid + '_pdf').val();
      jsfpb_page.rows[r].slots[s].layers[l].lbl1 = jsfpb_convertstring(jQuery('#' + divid + '_lbl1').val());
      jsfpb_page.rows[r].slots[s].layers[l].lbl2 = jsfpb_convertstring(jQuery('#' + divid + '_lbl2').val());
   } else if(type=='YouTube Video') {
      jsfpb_page.rows[r].slots[s].layers[l].ytid = jQuery('#' + divid + '_ytid').val();
   } else if(type=='Blue Callout') {
      jsfpb_page.rows[r].slots[s].layers[l].ttl = jsfpb_convertstring(jQuery('#' + divid + '_ttl').val());
   } else if(type=='Embedded Video') {
      jsfpb_page.rows[r].slots[s].layers[l].ttl = jsfpb_convertstring(jQuery('#' + divid + '_ttl').val());
   } else if(type=='jData Form') {
   } else if(type=='Image Gallery') {
   } else if(type=='Buttons') {
      jsfpb_page.rows[r].slots[s].layers[l].ttl = jsfpb_convertstring(jQuery('#' + divid + '_ttl').val());
      jsfpb_page.rows[r].slots[s].layers[l].subttl = jsfpb_convertstring(jQuery('#' + divid + '_subttl').val());
      jsfpb_page.rows[r].slots[s].layers[l].side = jsfpb_convertstring(jQuery('#' + divid + '_side').val());
      for(var i=1;i<=5;i++) {
         jsfpb_page.rows[r].slots[s].layers[l]['btn' + i] = jsfpb_convertstring(jQuery('#' + divid + '_btn' + i).val());
         jsfpb_page.rows[r].slots[s].layers[l]['url' + i] = jsfpb_convertstring(jQuery('#' + divid + '_url' + i).val());
         jsfpb_page.rows[r].slots[s].layers[l]['bg' + i] = jsfpb_convertstring(jQuery('#' + divid + '_bg' + i).val());
         jsfpb_page.rows[r].slots[s].layers[l]['fg' + i] = jsfpb_convertstring(jQuery('#' + divid + '_fg' + i).val());
      }
   } else if(type=='Download') {
   } else if(type=='Image & HTML') {
   } else if(type=='News & Media') {
      jsfpb_page.rows[r].slots[s].layers[l].sec = jsfpb_convertstring(jQuery('#' + divid + '_sec').val());
      jsfpb_page.rows[r].slots[s].layers[l].wdid = jsfpb_convertstring(jQuery('#' + divid + '_wdid').val());
      jsfpb_page.rows[r].slots[s].layers[l].typ = jsfpb_convertstring(jQuery('#' + divid + '_typ').val());
      jsfpb_page.rows[r].slots[s].layers[l].sty = jsfpb_convertstring(jQuery('#' + divid + '_sty').val());
      jsfpb_page.rows[r].slots[s].layers[l].htag = jsfpb_convertstring(jQuery('#' + divid + '_htag').val());
   } else if(type=='JData List') {
      jsfpb_page.rows[r].slots[s].layers[l].wdid = jsfpb_convertstring(jQuery('#' + divid + '_wdid').val());
      jsfpb_page.rows[r].slots[s].layers[l].sec = jsfpb_convertstring(jQuery('#' + divid + '_sec').val());
      jsfpb_page.rows[r].slots[s].layers[l].cols = jsfpb_convertstring(jQuery('#' + divid + '_cols').val());
      jsfpb_page.rows[r].slots[s].layers[l].colwd = jsfpb_convertstring(jQuery('#' + divid + '_colwd').val());
      jsfpb_page.rows[r].slots[s].layers[l].colmax = jsfpb_convertstring(jQuery('#' + divid + '_colmax').val());
      jsfpb_page.rows[r].slots[s].layers[l].param = jsfpb_convertstring(jQuery('#' + divid + '_param').val());
      jsfpb_page.rows[r].slots[s].layers[l].sty = jsfpb_convertstring(jQuery('#' + divid + '_sty').val());
      jsfpb_page.rows[r].slots[s].layers[l].tmplt = jsfpb_convertstring(jQuery('#' + divid + '_tmplt').val());
      jsfpb_checkjdatastyle(divid);
   } else if(type=='Icon, Header, and Text') {
      jsfpb_page.rows[r].slots[s].layers[l].ibg = jsfpb_convertstring(jQuery('#' + divid + '_ibg').val());
   }
}

function jsfpb_getCustomLayerHTML(type,r,s,l,width,height,jsfpb_page,trackid,userinfo) {
   var str = '';
   if(type=='Checklist Item') {
      str += '<table cellpadding=\"0" cellspacing=\"5\">';
      str += '<tr valign=\"top\">';
      str += '<td><img src=\"' + jsfpb_domain + 'images/checkbox.png\"></td>';
      str += '<td style=\"color:#000000;font-weight:bold;\">' + jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].ttl) + '</td>';
      str += '</tr>';
      //str += '<tr>';
      //str += '<td></td>';
      //str += '<td>' + jsfpb_page.rows[r].slots[s].layers[l].content + '</td>';
      //str += '</tr>';
      str += '</table>';
      str += '<div style=\"margin-left:5px;margin-top:2px;font-weight:normal;color:#333333;\">' + jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].content) + '</div>';
      
   } else if(type=='Sponsor Box') {
      
      var imgw = width - 30;
      var imgh = Math.round(imgw/2);
      
      str += '<div style=\"position:relative;padding:15px 5px 5px 5px;border-bottom:1px solid #4e565a;\">';
      str += '<div style=\"position:relative;text-align:center;width:' + imgw + 'px;height:' + imgh + 'px;overflow:hidden;\">';
      str += '<img align=\"center\" src=\"' + jsfpb_replaceAll('http:','https:',jsfpb_page.rows[r].slots[s].layers[l].img) + '\" style=\"max-width:' + imgw + 'px;max-height:' + imgh + 'px;width:auto;height:auto;\">';
      str += '</div>';
      str += '<div style=\"font-size:12px;color:#444444;margin-top:12px;width:100%;height:70px;overflow:hidden;\">';
      str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].content);
      str += '</div>';
      str += '</div>';
      
   } else if(type=='Tile Box') {
      
      var imgw = width - 30;
      if(imgw>420) imgw = 420;
      var imgh = Math.round(imgw/2);
      var imgl = Math.round((width - imgw)/2);
      
      str += '<div style=\"position:relative;left:' + imgl + 'px;width:' + imgw + 'px;\">';
      str += '<img src=\"' + jsfpb_replaceAll('http:','https:',jsfpb_page.rows[r].slots[s].layers[l].img) + '\" style=\"width:' + imgw + 'px;height:auto;border-radius:10px;\">';
      str += '<div style=\"position:absolute;right:20px;bottom:15px;width:' + (imgw - 60) + 'px;text-align:right;\">';
      str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].content);
      str += '</div>';
      str += '</div>';
      
   } else if(type=='Resource Box') {
      str += '<div style=\"position:relative;width:199px;height:235px;overflow:hidden;margin-left:5px;margin-right:5px;margin-bottom:10px;\">';
      str += '<img src=\"' + jsfpb_domain + 'images/resourcebox.png\" style=\"width:199px;height:235px;\">';
      str += '<div style=\"position:absolute;top:18px;left:18px;width:172px;font-weight:bold;font-size:14px;\">';
      str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].ttl);
      str += '</div>';
      str += '<div style=\"position:absolute;bottom:4px;left:10px;width:187px;border-bottom-left-radius:4px;border-bottom-right-radius:4px;text-align:center;\">';
      str += '<img src=\"' + jsfpb_replaceAll('http:','https:',jsfpb_page.rows[r].slots[s].layers[l].img) + '\" style=\"width:187px;height:auto;\">';
      str += '</div>';
      str += '</div>';
      
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].content)) {
         str += '<div style=\"font-size:12px;color:#444444;margin-top:6px;\">';
         str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].content);
         str += '</div>';
      }     
      
   } else if(type=='Formatted Content') {
      var ftsz = 18;
      var imgwd = 185;
      var pdwd = 40;
      if(width<320) {
         imgwd = 135;
         pdwd = 20;
      } else if(width<600) {
         imgwd = 150;
         pdwd = 30;
      } else if(width>850) {
         imgwd = 220;
      }
      
      var tempwd = 0;
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].fsz)) ftsz = jsfpb_page.rows[r].slots[s].layers[l].fsz;
      
      top_bot_padding = 0;
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].img) && Boolean(jsfpb_page.rows[r].slots[s].layers[l].hdr) && Boolean(jsfpb_page.rows[r].slots[s].layers[l].pls)) top_bot_padding = 15;
      
      str += '<div ';
      str += 'style=\"position:relative;width:' + width + 'px;padding-top:' + top_bot_padding + 'px;padding-bottom:' + top_bot_padding + 'px;margin-top:4px;margin-bottom:4px;';
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].brdr)) str += 'border-bottom:1px solid #4e565a;';
      str += '\">';
      
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].img)) {
         if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].cir) && Boolean(jsfpb_page.rows[r].slots[s].layers[l].circlr)) {
            if((imgwd%2)==1) imgwd = imgwd + 1;
            var brdr = Math.round(imgwd * 0.086);
            var pluswd = Math.round(imgwd * 0.25);
            
            str += '<div style=\"float:left;width:' + imgwd + 'px;margin-right:' + pdwd + 'px;\">';
            
            //Main block: square, clickable
            str += '<div onclick=\"pmrm_showfactoid(\'' + jsfpb_page.rows[r].slots[s].layers[l].img + '\',\'' + jsfpb_page.rows[r].slots[s].layers[l].ttl + '\',\'' + jsfpb_page.rows[r].slots[s].layers[l].content + '\');\" style=\"position:relative;width:' + imgwd + 'px;height:' + imgwd + 'px;cursor:pointer;overflow:hidden;\">';
            
            //Main circle inside square
            str += '<div style=\"z-index:1;position:absolute;left:0px;top:0px;width:' + imgwd + 'px;height:' + imgwd + 'px;overflow:hidden;background-color:' + jsfpb_page.rows[r].slots[s].layers[l].circlr + ';border-radius:' + (Math.round(imgwd/2)) + 'px;\">';
            str += '</div>';
            
            //Image: also rounded off - constrained by height (hopefully it's a square, or larger width than height)
            str += '<div style=\"z-index:10;position:absolute;left:' + brdr + 'px;top:' + brdr + 'px;width:' + (imgwd - (2*brdr)) + 'px;height:' + (imgwd - (2*brdr)) + 'px;overflow:hidden;background-color:#FFFFFF;border-radius:' + (Math.round((imgwd - (2*brdr))/2)) + 'px;\">';
            str += '<img src=\"' + jsfpb_replaceAll('http:','https:',jsfpb_page.rows[r].slots[s].layers[l].img) + '\" style=\"height:' + (imgwd - brdr) + 'px;width:auto;\">';
            str += '</div>';
            
            var x = Math.round(pluswd/4);
            
            //Bottom-right circle to contain the plus
            str += '<div style=\"z-index:2;position:absolute;right:0px;bottom:0px;width:' + (4*x) + 'px;height:' + (4*x) + 'px;background-color:' + jsfpb_page.rows[r].slots[s].layers[l].circlr + ';border-radius:' + (2*x) + 'px;\">';
            str += '</div>';
            
            //Plus sign in bottom-right
            str += '<div style=\"z-index:3;position:absolute;right:' + x + 'px;bottom:' + x + 'px;width:' + (2*x) + 'px;height:' + (2*x) + 'px;background-color:#FFFFFF;border-radius:' + x + 'px;\">';
            str += '</div>';
            str += '<div style=\"z-index:4;position:absolute;right:' + (x+5) + 'px;bottom:' + (2*x - 1) + 'px;width:' + (2*x - 10) + 'px;height:2px;background-color:' + jsfpb_page.rows[r].slots[s].layers[l].circlr + ';overflow:hidden;\">';
            str += '</div>';
            str += '<div style=\"z-index:4;position:absolute;bottom:' + (x+5) + 'px;right:' + (2*x - 1) + 'px;height:' + (2*x - 10) + 'px;width:2px;background-color:' + jsfpb_page.rows[r].slots[s].layers[l].circlr + ';overflow:hidden;\">';
            str += '</div>';
            
            str += '</div>';
            
            str += '</div>';            
         } else {         
            str += '<div ';
            str += 'style=\"float:left;width:' + imgwd + 'px;margin-right:' + pdwd + 'px;margin-bottom:10px;';
            if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].murl1)) str += 'cursor:pointer;\" onclick=\"location.href=' + jsfpb_convertback(jsfpb_page.rows[r].slots[s].layers[l].murl1);
            str += '\">';
            str += '<img src=\"' + jsfpb_replaceAll('http:','https:',jsfpb_page.rows[r].slots[s].layers[l].img) + '\" style=\"width:' + imgwd + 'px;height:auto;\">';
            str += '</div>';
         }
         tempwd = imgwd + pdwd;
      }
      str += '<div style=\"float:left;width:' + (width - tempwd) + 'px;margin-bottom:10px;\">';
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].hdr)) {
         str += '<div style=\"font-size:' + (ftsz-4) + 'px;color:#000000;font-weight:bold;margin-bottom:11px;\">';
         str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].hdr);
         str += '</div>';
      }
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].ttl)) {
         str += '<div style=\"font-size:' + ftsz + 'px;color:#222222;margin-bottom:6px;\">';
         str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].ttl);
         str += '</div>';
      }
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].content)) {
         str += '<div style=\"font-size:' + (ftsz-2) + 'px;color:#505050;margin-bottom:13px;font-weight:normal;\">';
         str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].content);
         str += '</div>';
      }
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].mbtn1)) {
         str += '<div onclick=\"' + jsfpb_convertback(jsfpb_page.rows[r].slots[s].layers[l].murl1) + '\" style=\"float:right;font-size:' + (ftsz-2) + 'px;color:' + jsfpb_page.rows[r].slots[s].layers[l].mfg1 + ';background-color:' + jsfpb_page.rows[r].slots[s].layers[l].mbg1 + ';padding:8px 12px 8px 12px;font-weight:normal;cursor:pointer;text-align:center;min-width:140px;\">';
         str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].mbtn1);
         str += '</div>';
         str += '<div style=\"clear:both;\"></div>';
      }
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].pls)) {
         str += '<div style=\"position:relative;font-size:10px;margin-bottom:5px;height:42px;width:' + (width - tempwd - 10) + 'px;\">';
         str += '<div ';
         str += 'style=\"position:absolute;left:0px;top:0px;width:12px;height:12px;border-radius:6px;background-color:#000000;';
         if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].plsurl)) str += 'cursor:pointer;\" onclick=\"' + jsfpb_page.rows[r].slots[s].layers[l].plsurl + ';';
         str += '\">';
         str += '<div style=\"position:absolute;left:2px;top:5px;width:8px;height:2px;background-color:#FFFFFF;\"></div>';
         str += '<div style=\"position:absolute;left:5px;top:2px;width:2px;height:8px;background-color:#FFFFFF;\"></div>';
         str += '</div>';
         str += '<div style=\"position:absolute;left:18px;top:5px;width:75px;height:24px;padding-right:8px;font-size:' + (ftsz-6) + 'px;font-weight:bold;color:#a5d059;border-right: 1px solid #555555;\">';
         str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].pls);
         str += '</div>';
         str += '<div style=\"position:absolute;left:115px;top:10px;font-size:' + (ftsz-6) + 'px;font-weight:bold;color:#000000;\">';
         str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].cred);
         str += '</div>';
         str += '</div>';
      }
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '</div>';
      
   } else if(type=='Title Banner') {
      var ftsz = 26;
      var toppx = 50;
      var clr = '#FFFFFF';
      if(width<300) {
         ftsz = 18;
      } else if(width<500) {
         ftsz = 22;
      }
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].fsz)) ftsz = jsfpb_page.rows[r].slots[s].layers[l].fsz;
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].clr)) clr = jsfpb_page.rows[r].slots[s].layers[l].clr;
      
      if(Boolean(height)) toppx = Math.round((height - ftsz - 12) /2);
      
      str += '<div style=\"position:relative;width:' + width + 'px;';
      if(Boolean(height)) str += 'height:' + (height-10) + 'px;';
      str += 'overflow:hidden;border-bottom:10px solid #000000;\">';
      str += '<img src=\"' + jsfpb_replaceAll('http:','https:',jsfpb_page.rows[r].slots[s].layers[l].img) + '\" style=\"z-index:1;width:' + width + 'px;height:auto;';
      if(Boolean(height) && Boolean(jsfpb_page.rows[r].slots[s].layers[l].tnt)) str += 'filter:grayscale(100%);';
      str += '\">';
      
      if(Boolean(height) && Boolean(jsfpb_page.rows[r].slots[s].layers[l].tnt)) str += '<div style=\"position:absolute;z-index:3;left:0px;top:0px;width:' + width + 'px;height:' + (height - 10) + 'px;background-color:' + jsfpb_page.rows[r].slots[s].layers[l].tnt + ';opacity:0.8;\"></div>';
      //str += '<div style=\"position:absolute;left:10px;top:' + toppx + 'px;width:' + (width - 20) + 'px;text-align:center;font-size:' + ftsz + 'px;color:' + clr + ';font-weight:bold;text-shadow: 2px 2px 2px #000000;\">';
      str += '<div style=\"position:absolute;z-index:3;left:10px;top:' + toppx + 'px;width:' + (width - 20) + 'px;text-align:center;font-size:' + ftsz + 'px;color:' + clr + ';font-weight:bold;\">';
      str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].ttl);
      str += '</div>';
      
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].lbl1)) {
         str += '<div style=\"position:absolute;z-index:4;left:40px;bottom:0px;width:200px;text-align:center;font-size:20px;background-color:#000000;color:#FFFFFF;padding:8px;border-top-left-radius:8px;border-top-right-radius:8px;\">';
         str += '<span style=\"font-weight:bold;\">' + jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].lbl1) + '</span> ' + jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].lbl2);
         str += '</div>';
      }
      
      str += '</div>';
      
   } else if(type=='Title Banner 2') {
      var ftsz = 26;
      //var toppx = 50;
      var toppx = 0;
      var clr = '#FFFFFF';
      var clr2 = '#DDDDDD';
      if(width<300) {
         ftsz = 18;
      } else if(width<500) {
         ftsz = 22;
      }
      
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].tnt)) {
         var tempc = jsfpb_page.rows[r].slots[s].layers[l].tnt.substr(1,1);
         if(tempc=='F' || tempc=='E' || tempc=='D' || tempc=='C' ||
            tempc=='B' || tempc=='A' || tempc=='9') {
            clr = '#000000';
            clr2 = '#252525';
         }
      }
      
      //alert('tb2 layer: ' + JSON.stringify(jsfpb_page.rows[r].slots[s].layers[l]));
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].fsz)) ftsz = jsfpb_page.rows[r].slots[s].layers[l].fsz;
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].clr)) clr = jsfpb_page.rows[r].slots[s].layers[l].clr;
      
      if(Boolean(height)) toppx = Math.round((height - ftsz - 12) /2) - ftsz;
      
      str += '<div style=\"position:relative;width:' + width + 'px;';
      if(Boolean(height)) str += 'height:' + height + 'px;';
      str += 'overflow:hidden;\">';
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].img)) {
         str += '<img src=\"' + jsfpb_replaceAll('http:','https:',jsfpb_page.rows[r].slots[s].layers[l].img) + '\" style=\"position:absolute;top:0px;left:0px;z-index:1;width:' + width + 'px;height:auto;';
         if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].gry)) str += 'filter:grayscale(100%);';
         str += '\">';
      }
      
      if(Boolean(height) && Boolean(jsfpb_page.rows[r].slots[s].layers[l].tnt)) str += '<div style=\"position:absolute;z-index:3;left:0px;top:0px;width:' + width + 'px;height:' + (height - 10) + 'px;background-color:' + jsfpb_page.rows[r].slots[s].layers[l].tnt + ';opacity:0.8;\"></div>';
      
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].ttl)) {
         var temp_pos = 'position:absolute;';
         var temp_top = 'top:' + toppx + 'px;';
         if(l<1) {
            temp_pos = 'position:relative;';
            temp_top = 'margin-' + temp_top;
         }
         
         str += '<div style=\"' + temp_pos + temp_top + 'z-index:3;left:10px;width:' + (width - 20) + 'px;text-align:center;font-size:' + ftsz + 'px;color:' + clr + ';font-weight:bold;\">';
         str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].ttl);
         if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].subttl)) {
            var temppad = Math.round(width * 0.08) + 10;
            str += '<div style=\"width:' + (width - 20 - (2*temppad)) + 'px;margin-left:' + temppad + 'px;margin-top:10px;';
            
            if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].aln)) str += 'text-align:center;';
            else str += 'text-align:right;';
            
            str += 'font-size:' + (ftsz - 4) + 'px;color:' + clr2 + ';font-weight:normal;\">';
            str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].subttl);
            str += '</div>';
         }
         str += '</div>';
      }
      
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].lbl1) || Boolean(jsfpb_page.rows[r].slots[s].layers[l].lbl2) || Boolean(jsfpb_page.rows[r].slots[s].layers[l].pdf)) {
         str += '<div style=\"position:absolute;left:0px;bottom:0px;z-index:4;width:' + width + 'px;height:50px;background-color:#111111;opacity:0.8;\"></div>';
         str += '<div style=\"position:absolute;left:15px;bottom:12px;z-index:5;font-size:18px;color:#FFFFFF;text-transform:uppercase;\">';
         if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].lbl1)) str += '<span style=\"font-weight:bold;margin-left:3px;margin-right:3px;\">' + jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].lbl1) + '</span>';
         if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].lbl2)) str += '<span style=\"margin-left:3px;\">' + jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].lbl2) + '</span>';
         str += '</div>';
         
         if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].pdf)) {
            str += '<div onclick=\"window.open(\'' + jsfpb_page.rows[r].slots[s].layers[l].pdf + '\');jsfpb_trackitem(\'DownloadPDF\',\'' + jsfpb_page.rows[r].slots[s].layers[l].pdf + '\',\'\',\'' + trackid + '\');\" style=\"position:absolute;z-index:5;right:15px;bottom:14px;cursor:pointer;font-weight:normal;font-size:14px;color:#96cb3d;\">';
            str += 'DOWNLOAD PDF &gt;';
            str += '</div>';
         }
      }
      
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].content)) str += jsfpb_convertback(jsfpb_page.rows[r].slots[s].layers[l].content);      
      
      str += '</div>';
      
   } else if(type=='TT Format') {
      var ftsz = 26;
      var toppx = 0;
      var clr = '#FFFFFF';
      var clr2 = '#DDDDDD';
      if(width<300) {
         ftsz = 18;
      } else if(width<500) {
         ftsz = 22;
      }
      
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].tnt)) {
         var tempc = jsfpb_page.rows[r].slots[s].layers[l].tnt.substr(1,1);
         if(tempc=='F' || tempc=='E' || tempc=='D' || tempc=='C' ||
            tempc=='B' || tempc=='A' || tempc=='9') {
            clr = '#000000';
            clr2 = '#252525';
         }
      }
      
      //alert('tb2 layer: ' + JSON.stringify(jsfpb_page.rows[r].slots[s].layers[l]));
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].fsz)) ftsz = jsfpb_page.rows[r].slots[s].layers[l].fsz;
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].clr)) {
         clr = jsfpb_page.rows[r].slots[s].layers[l].clr;
         clr2 = clr;
      }
      
      //if(Boolean(height)) toppx = Math.round((height - ftsz - 12) /2) - ftsz;
      
      str += '<div style=\"position:relative;width:' + width + 'px;';
      if(Boolean(height)) str += 'height:' + height + 'px;';
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].pad)) {
         str += 'padding:' + jsfpb_page.rows[r].slots[s].layers[l].pad + 'px;';
         width = width - 2*jsfpb_page.rows[r].slots[s].layers[l].pad;
      }
      str += 'overflow:hidden;\" ';
      str += 'class=\"ttformat\" ';
      str += '>';
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].img)) {
         str += '<img src=\"' + jsfpb_replaceAll('http:','https:',jsfpb_page.rows[r].slots[s].layers[l].img) + '\" style=\"z-index:1;width:' + width + 'px;height:auto;';
         if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].gry)) str += 'filter:grayscale(100%);';
         str += '\">';
      }
      
      if(Boolean(height) && Boolean(jsfpb_page.rows[r].slots[s].layers[l].tnt)) str += '<div style=\"position:absolute;z-index:3;left:0px;top:0px;width:' + width + 'px;height:' + (height - 10) + 'px;background-color:' + jsfpb_page.rows[r].slots[s].layers[l].tnt + ';opacity:0.8;\"></div>';
      
      
      
      
      var temp_pos = 'position:absolute;';
      var temp_top = 'top:' + toppx + 'px;';
      if(l<1) {
         temp_pos = 'position:relative;';
         temp_top = 'margin-' + temp_top;
      }
      
      str += '<div style=\"' + temp_pos + temp_top + 'z-index:3;width:' + width + 'px;\">';
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].ttl)) {
         str += '<div style=\"width:' + width + 'px;text-align:center;font-size:' + ftsz + 'px;color:' + clr + ';font-weight:bold;min-height:50px;margin-bottom:12px;\">';
         str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].ttl);
         str += '</div>';
      }
      
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].subttl)) {
         str += '<div style=\"width:' + width + 'px;margin-bottom:15px;min-height:30px;';
         
         if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].aln)) str += 'text-align:center;';
         else str += 'text-align:left;';
         
         str += 'font-size:' + (ftsz - 2) + 'px;color:' + clr2 + ';font-weight:normal;\">';
         str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].subttl);
         str += '</div>';
      }
      str += '</div>';
      
      
      /*
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].lbl1) || Boolean(jsfpb_page.rows[r].slots[s].layers[l].lbl2) || Boolean(jsfpb_page.rows[r].slots[s].layers[l].pdf)) {
         str += '<div style=\"position:absolute;left:0px;bottom:0px;z-index:4;width:' + width + 'px;height:50px;background-color:#111111;opacity:0.8;\"></div>';
         str += '<div style=\"position:absolute;left:15px;bottom:12px;z-index:5;font-size:18px;color:#FFFFFF;text-transform:uppercase;\">';
         if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].lbl1)) str += '<span style=\"font-weight:bold;margin-left:3px;margin-right:3px;\">' + jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].lbl1) + '</span>';
         if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].lbl2)) str += '<span style=\"margin-left:3px;\">' + jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].lbl2) + '</span>';
         str += '</div>';
         
         if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].pdf)) {
            str += '<div onclick=\"window.open(\'' + jsfpb_page.rows[r].slots[s].layers[l].pdf + '\');jsfpb_trackitem(\'DownloadPDF\',\'' + jsfpb_page.rows[r].slots[s].layers[l].pdf + '\',\'\',\'' + trackid + '\');\" style=\"position:absolute;z-index:5;right:15px;bottom:14px;cursor:pointer;font-weight:normal;font-size:14px;color:#96cb3d;\">';
            str += 'DOWNLOAD PDF &gt;';
            str += '</div>';
         }
      }
      */
      
      
      
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].content)) {
         str += '<div style=\"margin-bottom:17px;min-height:90px;line-height:130%;font-size:16px;\">';
         str += jsfpb_convertback(jsfpb_page.rows[r].slots[s].layers[l].content);
         str += '</div>';
      }
      
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].lbl1)) {
         var ocstr = jsfpb_convertback(jsfpb_page.rows[r].slots[s].layers[l].lbl2);
         if(ocstr.substr(0,4)=='http') ocstr = 'window.open(\'' + ocstr + '\');';
         
         var btnwd = 232;
         if(width<(btnwd + 10)) btnwd = width - 10;
         btnleft = Math.round((width-btnwd)/2);
         
         str += '<div ';
         str += 'style=\"margin-top:25px;margin-bottom:30px;';
         str += 'margin-left:' + btnleft + 'px;';
         str += 'text-align:center;';
         str += 'font-size:14px;';
         str += 'padding-top:10px;';
         str += 'padding-bottom:10px;';
         str += 'border-radius:4px;';
         str += 'cursor:pointer;';
         str += 'color:#FFFFFF;';
         str += 'background-color:#7d9031;';
         str += 'width:' + btnwd + 'px;';
         str += '\" ';
         str += 'class=\"ttbtn_primary\" ';
         str += 'id=\"ttbtn_' + r + '_' + s + '_' + l + '\" ';
         str += 'onmouseover=\"jQuery(\'#ttbtn_' + r + '_' + s + '_' + l + '\').css(\'background-color\',\'#647520\');\" ';
         str += 'onmouseout=\"jQuery(\'#ttbtn_' + r + '_' + s + '_' + l + '\').css(\'background-color\',\'#7d9031\');\" ';
         str += 'onclick=\"' + ocstr + '\" ';
         str += '>';
         str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].lbl1);
         str += '</div>';
      }

      str += '</div>';
      
   } else if(type=='YouTube Video') {
      //aD0J197g7jQ
      str += '<iframe width=\"' + width + '\" height=\"' + (Math.floor(width * 9/16)) + '\" src=\"https://www.youtube.com/embed/' + jsfpb_page.rows[r].slots[s].layers[l].ytid + '\" frameborder=\"0\" allowfullscreen></iframe>';
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].content)) {
         str += '<div style=\"margin-top:6px;\">';
         str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].content);
         str += '</div>';
      }
      
      
   } else if(type=='Blue Callout') {
      var fsz = 20;
      var clr = '#36b0c5';
      var bg = '#d1eef4';
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].fsz)) fsz = jsfpb_page.rows[r].slots[s].layers[l].fsz;
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].clr)) clr = jsfpb_page.rows[r].slots[s].layers[l].clr;
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].bg)) bg = jsfpb_page.rows[r].slots[s].layers[l].bg;
      
      str += '<div style=\"position:relative;padding:40px;font-weight:bold;';
      str += 'text-transform: uppercase;text-align:center;';
      str += 'font-size:' + fsz + 'px;';
      str += 'background-color:' + bg + ';';
      str += 'color:' + clr + ';';
      str += '\">';
      str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].content);
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].ttl)) {
         str += '<div style=\"font-size:' + (fsz - 2) + 'px;text-align:center;color:#404040;margin-top:18px;text-transform:none;font-weight:normal;\">';
         str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].ttl);
         str += '</div>';
      }
      str += '</div>';
      
   } else if(type=='Embedded Video') {
      v_ht = Math.floor(width * 9/16);
      str += '<div style=\"position:relative;width:' + width + 'px;height:' + v_ht + 'px;\">';
      str += '<video width=\"' + width + '\" height=\"' + v_ht + '\" controls>';
      str += '<source src=\"' + jsfpb_page.rows[r].slots[s].layers[l].ttl + '\" type=\"video/mp4\">';
      str += '</video>';      
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].img)) {
         var divid = 'embedvid' + r + '_' + s + '_' + l;
         str += '<img id=\"' + divid + '\" onclick=\"jQuery(\'#' + divid + '\').fadeOut(300);\" src=\"' + jsfpb_page.rows[r].slots[s].layers[l].img + '\" style=\"position:absolute;left:0px;top:0px;z-index:10;width:' + width + 'px;height:' + v_ht + 'px;';
         if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].gry)) str += 'filter:grayscale(100%);';
         str += '\">';
      }
      str += '</div>';
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].content)) {
         str += '<div style=\"margin-top:6px;\">';
         str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].content);
         str += '</div>';
      }
   } else if(type=='jData Form') {
      str += '<div id=\"jsfwdarea\"></div>';
      
      
      
      //str += '\n<script>\n';
      //str += 'jsf_getwebdatapage_jsonp(\'' + jsfpb_page.rows[r].slots[s].layers[l].content + '\',jsfpb_domain);\n';
      //str += '\n</script>\n';
      
      
      str += '\n<script>\n';
      if (typeof jsfada_displayrecord === "function") {
         //str += 'jsfada_displayrecord(\'' + jsfpb_page.rows[r].slots[s].layers[l].content + '\');\n';
         str += 'jsfada_getFieldPos(\'' + jsfpb_page.rows[r].slots[s].layers[l].content + '\',\'\',jsfada_displayrecord);\n';
      } else {
         str += 'jsf_getwebdatapage_jsonp(\'' + jsfpb_page.rows[r].slots[s].layers[l].content + '\',jsfpb_domain);\n';
      }
      str += '\n</script>\n';
      
      
      
      
   } else if(type=='Image Gallery') {
      if (typeof jsfimage_getGalleryPage !== "function") {      
         var script = document.createElement('script');
         script.src = jsfpb_domain + 'jsfcode/jsf_imagegallery.js';
         document.getElementsByTagName('head')[0].appendChild(script);      
      }
      
      str += '<div id=\"jsfimagegallery_' + r + '_' + s + '_' + l + '\" style=\"margin-bottom:200px;\"></div>';
      str += '\n<script>\n';
      str += 'var checkgallerycnt_' + r + '_' + s + '_' + l + ' = 0;\n';
      str += 'function checkgallery_' + r + '_' + s + '_' + l + '() {\n';
      str += '   if(checkgallerycnt_' + r + '_' + s + '_' + l + '<4){\n';
      str += '      if (typeof jsfimage_initGallery !== \'function\') {\n';
      str += '         checkgallerycnt_' + r + '_' + s + '_' + l + '++;\n';
      str += '         setTimeout(checkgallery_' + r + '_' + s + '_' + l + ',500);\n';
      str += '      } else {\n';
      str += '         checkgallerycnt_' + r + '_' + s + '_' + l + ' = 0;\n';
      str += '         jsfimage_initGallery(\'' + jsfpb_page.rows[r].slots[s].layers[l].content + '\',\'jsfimagegallery_' + r + '_' + s + '_' + l + '\',' + width + ');\n';
      //str += '         jsfimage_getGalleryPage(\'' + jsfpb_page.rows[r].slots[s].layers[l].content + '\',\'jsfimagegallery_' + r + '_' + s + '_' + l + '\',' + width + ');\n';
      str += '      }\n';
      str += '   }\n';
      str += '}\n';
      str += 'checkgallery_' + r + '_' + s + '_' + l + '();\n';
      str += '\n</script>\n';
   } else if(type=='Buttons') {
      var ftsz = 24;
      var btnwd = 230;
      if((width-10) < btnwd) btnwd = width - 10;
            
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].ttl)) {
         str += '<div style=\"font-size:' + ftsz + 'px;font-weight:bold;margin-bottom:5px;\">';
         str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].ttl);
         str += '</div>';
      }
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].subttl)) {
         str += '<div style=\"font-size:' + (ftsz - 4) + 'px;font-weight:normal;margin-top:10px;\">';
         str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].subttl);
         str += '</div>';
      }
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].content)) {
         str += '<div style=\"font-size:' + (ftsz - 8) + 'px;font-weight:normal;margin-top:10px;\">';
         str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].content);
         str += '</div>';
      }
      
      // for now, all buttons are centered
      var left = Math.round((width - (btnwd + 2))/2);
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].side) && jsfpb_page.rows[r].slots[s].layers[l].side.toLowerCase()=='left') left = 0;
      else if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].side) && jsfpb_page.rows[r].slots[s].layers[l].side.toLowerCase()=='right') left = (width - btnwd);
      
      str += '<div style=\"width:' + (btnwd + 2) + 'px;margin-left:' + left + 'px;\">';
      for(var i=1;i<=5;i++) {
         if(Boolean(jsfpb_page.rows[r].slots[s].layers[l]['btn' + i])) {
            var bg = '#9bada5';
            var bg2 = '#74867e';
            var fg = '#ffffff';
            var oc = '';
            if(Boolean(jsfpb_page.rows[r].slots[s].layers[l]['url' + i])) {
               oc = jsfpb_convertback(jsfpb_page.rows[r].slots[s].layers[l]['url' + i]);
               if(oc.substr(0,4)=='http') oc = 'window.open(\'' + oc + '\');';
            }
            if(Boolean(jsfpb_page.rows[r].slots[s].layers[l]['bg' + i])) bg = jsfpb_page.rows[r].slots[s].layers[l]['bg' + i];
            if(Boolean(jsfpb_page.rows[r].slots[s].layers[l]['bg' + i])) bg2 = jsfpb_page.rows[r].slots[s].layers[l]['bg' + i];
            if(Boolean(jsfpb_page.rows[r].slots[s].layers[l]['fg' + i])) fg = jsfpb_page.rows[r].slots[s].layers[l]['fg' + i];
            var tempid = 'ttbtn' + i + '_' + r + '_' + s + '_' + l + '_' + jsfpb_replaceAll(':', '', jsfpb_replaceAll(' ', '', jsfpb_page.name));
            str += '<div style=\"margin-top:15px;padding:10px;text-align:center;';
            //str += 'border:1px solid ' + fg + ';';
            str += 'background-color:' + bg + ';';
            str += 'color:' + fg + ';';
            str += 'border-radius:4px;cursor:pointer;\" ';
            str += 'class=\"ttbtn_secondary\" ';
            str += 'id=\"' + tempid + '\" ';
            str += 'onmouseover=\"jQuery(\'#' + tempid + '\').css(\'background-color\',\'' + bg2 + '\');\" ';
            str += 'onmouseout=\"jQuery(\'#' + tempid + '\').css(\'background-color\',\'' + bg + '\');\" ';
            str += 'onclick=\"' + oc + '\">';
            str += jsfpb_page.rows[r].slots[s].layers[l]['btn' + i];
            str += '</div>';
         }
      }
      str += '</div>';
   } else if(type=='Download') {
      var twd = 250;
      if(twd>width) twd = width;
      var tht = Math.round(0.8 * twd);
      var topht = Math.round(tht * 0.6);
      
      str += '<div style=\"width:' + twd + 'px;height:' + tht + 'px;overflow:hidden;border-radius:4px;\">';
      
      str += '<div style=\"width:' + twd + 'px;height:' + topht + 'px;overflow:hidden;\">';
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].img)) {
         str += '<img src=\"' + jsfpb_replaceAll('http:','https:',jsfpb_page.rows[r].slots[s].layers[l].img) + '\" style=\"width:' + twd + 'px;height:auto;\">';
      }
      str += '</div>'
      str += '<div style=\"width:' + twd + 'px;height:5px;overflow:hidden;background-color:#334357;\">';
      str += '</div>'
      str += '<div style=\"width:' + twd + 'px;height:' + (tht - topht - 5) + 'px;overflow:hidden;background-color:#f7f7f7;\">';
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].content)) {
         str += '<div style=\"padding:8px;color:#334357;text-align:center;font-size:10px;\">';
         str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].content);
         str += '</div>'
      }
      str += '<img src=\"' + jsfpb_domain + 'images/download.png\" style=\"margin-left:' + (Math.round((twd - 24)/2)) + 'px;margin-top:6px;width:24px;height:auto;\">';
      str += '</div>'

      
      str += '</div>'
   } else if(type=='Image & HTML') {
      var iwd = Math.round(width * 0.4);
      if(iwd>jsfpb_page.rows[r].slots[s].layers[l].mpad) iwd = jsfpb_page.rows[r].slots[s].layers[l].mpad;
      
      var twd = width - iwd - 12;
      
      str += '<div style=\"width:' + iwd + 'px;float:left;\">';
      if(Boolean(jsfpb_page.rows[r].slots[s].layers[l].img)) {
         str += '<img src=\"' + jsfpb_replaceAll('http:','https:',jsfpb_page.rows[r].slots[s].layers[l].img) + '\" style=\"width:' + iwd + 'px;height:auto;\">';
      }
      str += '</div>'
      
      str += '<div style=\"float:left;width:' + twd + 'px;margin-left:10px;\">';
      str += jsfpb_convertback(jsfpb_page.rows[r].slots[s].layers[l].content);
      str += '</div>'
      str += '<div style=\"clear:both;\"></div>'
   } else if(type=='News & Media') {
      var divwd = Math.floor(width/2);
      var divgap = 0;
      if(divwd < 240) divwd = width;
      if(divwd > 400) {
         divwd = 400;
         if((2*divwd) > width) divgap = Math.floor((width - divwd)/2);
         else divgap = Math.floor((width - (2*divwd))/2);
      }
      var divid = r + '_' + s + '_' + l + '_' + divwd;
      jsfpb_newsmedia[divid] = jsfpb_page.rows[r].slots[s].layers[l];
      
      str += '<div id=\"' + divid + 'newsmedia\" style=\"position:relative;margin-left:' + divgap + 'px;margin-right:' + divgap + 'px;\">';
      str += '</div>';
      str += '\n<script>\n';
      str += 'var query=\'\';\n';
      str += 'query += \'&wd_id=' + encodeURIComponent(jsfpb_newsmedia[divid].wdid) + '\';\n';
      str += 'query += \'&divid=' + encodeURIComponent(divid) + '\';\n';
      str += 'query += \'&cmsz_' + jsfpb_replaceAll(' ','',jsfpb_newsmedia[divid].wdid).toLowerCase() + '_hashtags=' + encodeURIComponent(jsfpb_newsmedia[divid].htag) + '\';\n';
      str += 'query += \'&cmsz_' + jsfpb_replaceAll(' ','',jsfpb_newsmedia[divid].wdid).toLowerCase() + '_ctype=' + encodeURIComponent(jsfpb_newsmedia[divid].typ) + '\';\n';
      str += 'query += \'&cmsenabled=1\';\n';
      str += 'jsfpb_QuickJSON(\'getwdandrows\',\'jsfpb_newsmediareturn\',query,true);\n';
      str += '\n</script>\n';
   } else if(type=='JData List') {
      var cols = parseInt(jsfpb_page.rows[r].slots[s].layers[l].cols);
      var colwd = parseInt(jsfpb_page.rows[r].slots[s].layers[l].colwd);
      var colmax = parseInt(jsfpb_page.rows[r].slots[s].layers[l].colmax);
      var finished = false;
      var divwd = width;
      while(!finished) {
         divwd = Math.floor(width/cols);
         if(cols==1 || divwd>=colwd) {
            finished=true;
         } else {
            cols = cols - 1;
         }
      }
      var divgap = 0;
      if(divwd > colmax) {
         divwd = colmax;
      }
      jsfpb_page.rows[r].slots[s].layers[l].divwd = divwd;
      jsfpb_page.rows[r].slots[s].layers[l].divcols = cols;
      divgap = Math.floor((width - (cols*divwd))/2);
      var divid = r + '_' + s + '_' + l;
      jsfpb_newsmedia[divid] = jsfpb_page.rows[r].slots[s].layers[l];
      
      str += '<div id=\"' + divid + 'jdatalist\" style=\"position:relative;margin-left:' + divgap + 'px;margin-right:' + divgap + 'px;\">';
      str += '</div>';
      str += '\n<script>\n';
      str += 'var query=\'\';\n';
      str += 'query += \'&wd_id=' + encodeURIComponent(jsfpb_newsmedia[divid].wdid) + '\';\n';
      str += 'query += \'&divid=' + encodeURIComponent(divid) + '\';\n';
      str += 'query += \'&cmsenabled=1\';\n';
      str += 'query += \'' + jsfpb_buildquery(jsfpb_newsmedia[divid].wdid,jsfpb_newsmedia[divid].param) + '\';\n';
      str += 'jsfpb_QuickJSON(\'getwdandrows\',\'jsfpb_jdatalistreturn\',query,true);\n';
      str += '\n</script>\n';
   } else if(type=='Icon, Header, and Text') {
      str += '<div style=\"position:relative;margin:10px;\">';
      str += '<div style=\"float:left;width:40px;height:40px;overflow:hidden;\">';
      str += '<div style=\"background-color:' + jsfpb_page.rows[r].slots[s].layers[l].ibg + ';border-radius:20px;width:40px;height:40px;overflow:hidden;\">';
      str += '<img src=\"' + jsfpb_replaceAll('http:','https:',jsfpb_page.rows[r].slots[s].layers[l].img) + '\" style=\"position:relative;left:8px;top:8px;width:24px;height:24px;\">';
      str += '</div>';
      str += '</div>';
      str += '<div style=\"float:left;margin-left:15px;margin-top:10px;width:' + (width - 40 - 30 - 20) + 'px;font-size:16px;font-weight:bold;color:#333333;\">';
      str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].mttl);
      str += '</div>';
      str += '<div style=\"clear:both;\"></div>';
      str += '<div style=\"margin-top:5px;margin-left:55px;width:' + (width - 20 - 60) + 'px;font-size:14px;color:#999999;font-weight:normal;\">';
      str += jsfpb_convertdisplay(jsfpb_page.rows[r].slots[s].layers[l].content);
      str += '</div>';
      str += '</div>';
   }
   
   return str;
}




/////////////////////////////////////////////////
//    CUSTOM UTILITIES
/////////////////////////////////////////////////


function jsfpb_jdatalistreturn(jsondata) {
   //alert('news/media return: ' + JSON.stringify(jsondata));
   //alert('tool info: ' + JSON.stringify(jsfpb_newsmedia));
   var str = '';
   var sty = jsfpb_newsmedia[jsondata.divid].sty;
   var width = jsfpb_newsmedia[jsondata.divid].divwd;
   var cols = jsfpb_newsmedia[jsondata.divid].divcols;
   var tmplt = '';
   
   if(!Boolean(sty)) sty = '1';
   if(sty=='1') {
      //alert('here now');
      //Name Only
      tmplt += '<div ';
      tmplt += 'style=\"position:relative;margin:5px 5px 5px 5px;width:' + (width - 10) + 'px;height:34px;overflow:hidden;cursor:pointer;border-bottom:1px solid #DDDDDD;\" ';
      tmplt += 'onclick=\"window.open(\'%%%linkurl%%%\');\" ';
      tmplt += '>%%%linktext%%%</div>';
   } else if(sty=='2') {
      //Name,Description Only
      tmplt += '<div style=\"position:relative;margin:7px 10px 2px 10px;width:' + (width - 22) + 'px;overflow:hidden;\">';
      tmplt += '<div ';
      tmplt += 'style=\"position:relative;margin-bottom:2px;cursor:pointer;\" ';
      tmplt += 'onclick=\"window.open(\'%%%linkurl%%%\');\" ';
      tmplt += '>%%%linktext%%%</div>';
      tmplt += '<div style=\"margin-top:2px;color:#888888;font-weight:normal;font-size:12px;\">';
      tmplt += '%%%description%%%';
      tmplt += '</div>';
      tmplt += '</div>';
   } else if(sty=='3') {
      //Image, Name, Description, By Line
      tmplt += '<div style=\"position:relative;border-bottom:1px solid #EDEDED;margin-right:10px;margin-bottom:10px;\">';
      tmplt += '<div style=\"float:left;width:90px;height:90px;overflow:hidden;\">';
      tmplt += '<img src=\"%%%dispimg%%%\" style=\"max-width:90px;max-height:90px;\">';
      tmplt += '</div>';
      tmplt += '<div style=\"float:left;\">';
      tmplt += '<div ';
      tmplt += 'style=\"position:relative;margin:10px 5px 5px 5px;width:' + (width - 20 - 100) + 'px;height:36px;overflow:hidden;cursor:pointer;\" ';
      tmplt += 'onclick=\"window.open(\'%%%linkurl%%%\');\" ';
      tmplt += '>%%%linktext%%%</div>';
      tmplt += '<div style=\"margin:0px 5px 5px 5px;color:#333333;font-weight:normal;width:' + (width - 20 - 100) + 'px;height:32px;overflow:hidden;font-size:14px;\">';
      tmplt += '%%%description%%%';
      tmplt += '</div>';
      tmplt += '<div style=\"margin:0px 5px 10px 5px;color:#AAAAAA;font-weight:normal;width:' + (width - 20 - 100) + 'px;height:14px;font-size:12px;overflow:hidden;\">';
      tmplt += '%%%line3%%%';
      tmplt += '</div>';
      tmplt += '</div>';
      tmplt += '<div style=\"clear:both;\"></div>';
      tmplt += '</div>';
   } else if(sty=='4') {
      //Video inline
      tmplt += '<iframe width=\"' + (width - 10) + '\" height=\"' + (Math.floor((width - 10) * 9/16)) + '\" src=\"https://www.youtube.com/embed/%%%ytid%%%\" frameborder=\"0\" allowfullscreen></iframe>';
      tmplt += '<div ';
      tmplt += 'style=\"position:relative;margin:5px 5px 3px 5px;width:' + (width - 10) + 'px;height:34px;overflow:hidden;\" ';
      tmplt += '>%%%linktext%%%</div>';
      tmplt += '<div style=\"margin:0px 5px 10px 5px;padding-bottom:5px;color:#444444;font-weight:normal;width:' + (width - 10) + 'px;height:45px;overflow:hidden;border-bottom:1px solid #DDDDDD;font-size:12px;\">';
      tmplt += '%%%description%%%';
      tmplt += '</div>';
   } else if(sty=='5') {
      //Image, Name, Description typically in a list (usually single column)
      tmplt += '<div style=\"position:relative;border-bottom:1px solid #999999;padding-bottom:10px;margin-bottom:10px;margin-top:10px;\">';
      tmplt += '<div style=\"float:left;width:180px;margin-right:15px;\">';
      tmplt += '<img src=\"%%%dispimg%%%\" style=\"max-width:180px;max-height:120px;\">';
      tmplt += '</div>';
      tmplt += '<div style=\"float:left;width:' + (width - 200) + 'px;\">';
      tmplt += '<div style=\"cursor:pointer;\" onclick=\"window.open(\'%%%linkurl%%%\');\">%%%linktext%%%</div>';
      tmplt += '<div style=\"margin-top:5px;\">%%%links%%%</div>';
      tmplt += '</div>';
      tmplt += '<div style=\"clear:both;\"></div>';
      tmplt += '</div>';
   } else if (sty=='500') {
      tmplt = jsfpb_convertback(jsfpb_newsmedia[jsondata.divid].tmplt);
   }
   
   str += '<div id=\"' + jsondata.divid + '_jdatalist_data\" style=\"position:relative;\">';
   if(jsondata.rows.length>0 && Boolean(jsfpb_newsmedia[jsondata.divid].sec)) str += '<div id=\"' + jsondata.divid + '_jdatalist_title\" style=\"margin-top:5px;padding-top:5px;border-top:1px solid #AAAAAA;margin-bottom:2px;font-weight:bold;color:#333333;font-size:18px;\">' + jsfpb_newsmedia[jsondata.divid].sec + '</div>';
   for(var i=0;i<jsondata.rows.length;i++) {
      if(i>0 && (i%cols)==0) str += '<div style=\"clear:both;\"></div>';
      str += '<div style=\"float:left;width:' + width + 'px;\">';
      
      var template = tmplt;
      
      var linkurl = jsondata.rows[i].linkurl;
      if(Boolean(jsondata.rows[i].fileupload)) linkurl = jsondata.rows[i].fileupload;
      else if(Boolean(jsondata.rows[i].url)) linkurl = jsondata.rows[i].url;
      
      if(Boolean(linkurl)) {
         linkurl = jsfpb_replaceAll('http:','https:',linkurl);
         
         var patharr = linkurl.split('/');
         var ytidarr = patharr[(patharr.length - 1)].split('=');
         var ytid = ytidarr[(ytidarr.length - 1)];
         
         template = jsfpb_replaceAll('%%%linkurl%%%',linkurl,template);
         template = jsfpb_replaceAll('%%%ytid%%%',ytid,template);
      }

      
      // Three possible links/images under the text
      var third = Math.floor((width - 200 - 45)/3);
      var linkdiv = '';
      
      var xtralink;
      var xtratxt;
      var xtraimg;
      
      for(var j=1;j<=3;j++) {
         xtralink = jsondata.rows[i]['xtralink' + j];
         xtratxt = jsondata.rows[i]['xtratxt' + j];
         xtraimg = jsondata.rows[i]['xtraimg' + j];
         if(Boolean(xtralink)) {
            xtralink = jsfpb_replaceAll('http:','https:',xtralink);
            linkdiv += '<div style=\"float:left;margin-right:15px;cursor:pointer;width:' + third + 'px;\" onclick=\"window.open(\'' + xtralink + '\');\">';
            if(Boolean(xtraimg)) {
               xtraimg = jsfpb_replaceAll('http:','https:',xtraimg);
               linkdiv += '<img src=\"' + xtraimg + '\" style=\"width:' + third + 'px;height:auto;\">';
            } else {
               linkdiv += '<div style=\"color:#FFFFFF;background-color:#f89b21;width:' + third + 'px;height:50px;overflow:hidden;\">';
               linkdiv += '<div style=\"padding:10px;\">';
               linkdiv += '<table cellpadding=\"0\" cellspacing=\"0\" style=\"font-size:12px;height:30px;\">';
               linkdiv += '<tr>';
               linkdiv += '<td>';
               linkdiv += '<div style=\"margin-right:10px;\">';
               linkdiv += xtratxt;
               linkdiv += '</div>';
               linkdiv += '</td>';
               linkdiv += '<td>&gt;</td>';
               linkdiv += '</tr>';
               linkdiv += '</table>';
               linkdiv += '</div>';
               linkdiv += '</div>';
            }
            linkdiv += '</div>';
         }
      }
      template = jsfpb_replaceAll('%%%links%%%',linkdiv,template);

      // All other data
      for (var key in jsondata.rows[i]) {
         template = jsfpb_replaceAll('%%%' + key + '%%%',jsondata.rows[i][key],template);
      }
      
      str += template;
      str += '</div>';
   }
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';
   jQuery('#' + jsondata.divid + 'jdatalist').html(str);
}


function jsfpb_newsmediareturn(jsondata) {
   //alert('news/media return: ' + JSON.stringify(jsondata));
   //alert('tool info: ' + JSON.stringify(jsfpb_newsmedia));
   var str = '';
   var sty = jsfpb_newsmedia[jsondata.divid].sty;
   var comp = jsondata.divid.split('_');
   var width = parseInt(comp[3]);
   
   str += '<div style=\"position:relative;padding-bottom:5px;\">';
   str += '<div style=\"position:relative;font-weight:bold;font-size:16px;margin-bottom:15px;color:#f89b21;\">' + jsfpb_newsmedia[jsondata.divid].sec + '</div>';
   for(var i=0;i<jsondata.rows.length;i++) {
      str += '<div style=\"float:left;width:' + width + 'px;\">';
      
      if(!Boolean(sty)) sty = '1';
      if(sty=='1') {
         //alert('here now');
         //Name Only
         str += '<div ';
         str += 'style=\"position:relative;margin:5px 5px 5px 5px;width:' + (width - 10) + 'px;height:34px;overflow:hidden;cursor:pointer;border-bottom:1px solid #DDDDDD;\" ';
         str += 'onclick=\"window.open(\'';
         if(Boolean(jsondata.rows[i].fileupload)) str += jsondata.rows[i].fileupload;
         else str += jsondata.rows[i].linkurl;
         str += '\');\" ';
         str += '>';
         str += jsondata.rows[i].linktext;
         str += '</div>';
      } else if(sty=='2') {
         //Name,Description Only
         str += '<div ';
         str += 'style=\"position:relative;margin:8px 5px 3px 5px;width:' + (width - 10) + 'px;height:34px;overflow:hidden;cursor:pointer;\" ';
         str += 'onclick=\"window.open(\'';
         if(Boolean(jsondata.rows[i].fileupload)) str += jsondata.rows[i].fileupload;
         else str += jsondata.rows[i].linkurl;
         str += '\');\" ';
         str += '>';
         str += jsondata.rows[i].linktext;
         str += '</div>';
         str += '<div style=\"margin:0px 5px 10px 5px;padding-bottom:5px;color:#444444;font-weight:normal;width:' + (width - 10) + 'px;height:45px;overflow:hidden;border-bottom:1px solid #DDDDDD;font-size:12px;\">';
         str += jsondata.rows[i].description;
         str += '</div>';
      } else if(sty=='3') {
         //Image, Name, Description, By Line
         str += '<div style=\"position:relative;border-bottom:1px solid #EDEDED;margin-right:10px;margin-bottom:10px;\">';
         str += '<div style=\"float:left;width:90px;height:90px;overflow:hidden;\">';
         str += '<img src=\"' + jsondata.rows[i].dispimg + '\" style=\"max-width:90px;max-height:90px;\">';
         str += '</div>';
         str += '<div style=\"float:left;\">';
         str += '<div ';
         str += 'style=\"position:relative;margin:10px 5px 5px 5px;width:' + (width - 20 - 100) + 'px;height:36px;overflow:hidden;cursor:pointer;\" ';
         str += 'onclick=\"window.open(\'';
         if(Boolean(jsondata.rows[i].fileupload)) str += jsondata.rows[i].fileupload;
         else str += jsondata.rows[i].linkurl;
         str += '\');\" ';
         str += '>';
         str += jsondata.rows[i].linktext;
         str += '</div>';
         str += '<div style=\"margin:0px 5px 5px 5px;color:#333333;font-weight:normal;width:' + (width - 20 - 100) + 'px;height:32px;overflow:hidden;font-size:14px;\">';
         str += jsondata.rows[i].description;
         str += '</div>';
         str += '<div style=\"margin:0px 5px 10px 5px;color:#AAAAAA;font-weight:normal;width:' + (width - 20 - 100) + 'px;height:14px;font-size:12px;overflow:hidden;\">';
         str += jsondata.rows[i].line3;
         str += '</div>';
         str += '</div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
      } else if(sty=='4') {
         //Video inline
         var patharr = jsondata.rows[i].linkurl.split('/');
         var iframeurl = '';
         if(jsondata.rows[i].linkurl.toLowerCase().includes('vimeo')) {
            var vimeoid = patharr[(patharr.length - 1)];
            iframeurl = 'https://player.vimeo.com/video/' + vimeoid;
         } else {
            var ytidarr = patharr[(patharr.length - 1)].split('=');
            var ytid = ytidarr[(ytidarr.length - 1)];
            iframeurl = 'https://www.youtube.com/embed/' + ytid;
         }
         
         var vwd = width - 20;
         if(vwd>300) vwd = 300;
         var vpd = Math.floor((width - vwd)/2);
         
         str += '<div ';
         str += 'style=\"position:relative;margin:14px ' + vpd + 'px 8px ' + vpd + 'px;width:' + vwd + 'px;height:250px;overflow:hidden;\" ';
         str += '>';
         str += '<iframe width=\"' + vwd + '\" height=\"' + (Math.floor(vwd * 9/16)) + '\" src=\"' + iframeurl + '\" frameborder=\"0\" allowfullscreen></iframe>';
         str += '<div ';
         str += 'style=\"margin-top:5px;position:relative;max-height:34px;overflow:hidden;color:#68696A;\" ';
         str += '>';
         str += jsondata.rows[i].linktext;
         str += '</div>';
         str += '<div style=\"margin-top:5px;padding-bottom:8px;color:#68bcae;font-weight:normal;max-height:45px;overflow:hidden;border-bottom:1px solid #DDDDDD;font-size:12px;\">';
         str += jsondata.rows[i].description;
         str += '</div>';
         str += '</div>';
      }
      
      str += '</div>';
   }
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';
   jQuery('#' + jsondata.divid + 'newsmedia').html(str);
}


// For JData List
function jsfpb_checkjdatastyle(divid) {
   var str = '';
   var sty = jQuery('#' + divid + '_sty').val();
   if(sty=='500') {
      jQuery('#' + divid + '_tmpltdiv').show();
   } else if(sty=='1') {
      
   } else {
      jQuery('#' + divid + '_tmpltdiv').hide();
   }
   jQuery('#' + divid + '_styinfo').html(str);
}

function jsfpb_showparams(divid){
   var htmlstr = '';
   var str = jQuery('#' + divid).val();
   var params = jsfpb_separateparams(str);
   var bgclr = '';
   
   var locarr = divid.split('_');
   var oc = 'jsfpb_changeLayer(' + locarr[0] + ',' + locarr[1] + ',' + locarr[2] + ');';
   
   for(var i=0;i<params.length;i++) {
      if(bgclr=='#F0F0F0') bgclr = '#E0E4EE';
      else bgclr = '#F0F0F0';
      htmlstr += '<div style=\"padding:3px;font-size:10px;background-color:' + bgclr + ';\">';
      htmlstr += '<div style=\"width:90px;height:22px;overflow:hidden;float:left;\">';
      htmlstr += params[i].name;
      htmlstr += '</div>';
      htmlstr += '<div style=\"margin-left:5px;width:140px;height:22px;overflow:hidden;float:left;\">';
      htmlstr += params[i].value;
      htmlstr += '</div>';
      htmlstr += '<div onclick=\"jsfpb_removeparameter(\'' + divid + '\',' + i + ');' + oc + '\" style=\"margin-left:5px;float:left;cursor:pointer;color:red;\">';
      htmlstr += 'X</div>';
      htmlstr += '<div style=\"clear:both;\"></div>';
      htmlstr += '</div>';
   }
   htmlstr += '<div style=\"margin-top:3px;padding:3px;font-size:10px;background-color:#ffffff;\">';
   htmlstr += '<div style=\"width:90px;overflow:hidden;float:left;\">';
   htmlstr += '<input type=\"text\" id=\"' + divid + '_name\" style=\"font-size:10px;width:82px;\">';
   htmlstr += '</div>';
   htmlstr += '<div style=\"margin-left:5px;width:140px;overflow:hidden;float:left;\">';
   htmlstr += '<input type=\"text\" id=\"' + divid + '_value\" style=\"font-size:10px;width:132px;\">';
   htmlstr += '</div>';
   htmlstr += '<div onclick=\"jsfpb_addparameter(\'' + divid + '\');' + oc + '\" style=\"margin-left:5px;float:left;cursor:pointer;color:red;\">';
   htmlstr += 'add</div>';
   htmlstr += '<div style=\"clear:both;\"></div>';
   htmlstr += '</div>';
   jQuery('#' + divid + '_list').html(htmlstr);
}

function jsfpb_separateparams(str) {
   var ret = [];
   if(Boolean(str)) {
      var arr1 = str.split('&');
      for(var i=0;i<arr1.length;i++){
         if(Boolean(arr1[i])) {
            var arr2 = arr1[i].split('=');
            var temp = {};
            temp.name = arr2[0];
            temp.value = decodeURIComponent(arr2[1]);
            ret.push(temp);
         }
      }
   }
   return ret;
}

function jsfpb_addparameter(divid,str,name,value) {
   if(!Boolean(str) && Boolean(divid)) str = jQuery('#' + divid).val();
   if(!Boolean(name) && Boolean(divid)) name = jQuery('#' + divid + '_name').val();
   if(!Boolean(value) && Boolean(divid)) value = jQuery('#' + divid + '_value').val();
   if(Boolean(name)) {
      str += '&' + name + '=' + encodeURIComponent(value);
      if(Boolean(divid)) {
         jQuery('#' + divid).val(str);
         jQuery('#' + divid + '_name').val('');
         jQuery('#' + divid + '_value').val('');
         jsfpb_showparams(divid);
      }
   }
   return str;
}

      
function jsfpb_buildquery(wdid,str) {
   var params = jsfpb_separateparams(str);
   var query = '';
   for(var i=0;i<params.length;i++) {
      query += '&cmsz_' + jsfpb_replaceAll(' ','',wdid).toLowerCase() + '_' + params[i].name + '=' + encodeURIComponent(params[i].value);
   }
   return query;
}

      
function jsfpb_removeparameter(divid,ndx) {
   var str = jQuery('#' + divid).val();
   var newstr = '';
   
   var params = jsfpb_separateparams(str);
   for(var i=0;i<params.length;i++) {
      if(i != ndx) {
         newstr += jsfpb_addparameter('',newstr,params[i].name,params[i].value);
      }
   }
   jQuery('#' + divid).val(newstr);
   jsfpb_showparams(divid);
}
      
