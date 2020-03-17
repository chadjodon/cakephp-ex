var jsfsearch_domain = 'https://www.plasticsmarkets.org/';
var jsfsearch_servercontroller = 'jsfcode/jsoncontroller.php?format=jsonp';

//----------------------------------------------------------------------------------
// how we will make a jsonp call
//----------------------------------------------------------------------------------
function jsfsearch_CallJSONP(url,priority) {
   if (typeof jsf_CallJSONP == 'function') {
      //alert('calling jsf_calljsonp instead');
      jsf_CallJSONP(url,priority);
   } else {
      var script = document.createElement('script');
      script.setAttribute('src', url);
      document.getElementsByTagName('head')[0].appendChild(script);
   }
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


var jsfsearch_arrowindex={};
var jsfsearch_answers={};
var jsfsearch_onenter={};

// Try to avoid too many calls to backend
var jsfsearch_waitforpause = true;
var jsfsearch_timer = {};
var jsfsearch_interval = 400;
var jsfsearch_searching = {};

//----------------------------------------------------------------------------------
function jsfsearch_suggest(wd_id,phrase,divid,callback){
   if(!Boolean(jsfsearch_searching[divid])) {
      jsfsearch_arrowindex[divid] = 0;
      if (!Boolean(callback)) callback='jsfsearch_display';
   
      if (Boolean(phrase) && Boolean(wd_id)) {
         jsfsearch_searching[divid] = true;
         
         var url = jsfsearch_domain + jsfsearch_servercontroller + '&action=searchwdindex';
         url = url + '&callback=' + encodeURIComponent(callback);
         url = url + '&wd_id=' + encodeURIComponent(wd_id);
         url = url + '&phrase=' + encodeURIComponent(phrase);
         url = url + '&divid=' + encodeURIComponent(divid);
         //if (Boolean(xtra)) url = url + xtra;
      
         //alert('URL: ' + url);
      
         jsfsearch_CallJSONP(url);
      } else {
         //alert('no phrase or wd_id');
         jQuery('#' + divid + '_searchsuggest').hide();
      }
   }
}


function jsfsearch_testinput(divid,wd_id,def,onenter,nobutton,width,fsz) {
   
   if(!Boolean(onenter)) onenter = 'jsfsearch_submitsearchphrase';
   if(!Boolean(def)) def = 'Search';
   if(!Boolean(width)) width = 250;
   if(!Boolean(fsz)) fsz = 16;
   
   jsfsearch_onenter[divid] = onenter;
   
   var str = '';
   str += '<div style=\"position:relative;\">';
   str += '<div style=\"position:relative;float:left;\">';
   str += '<input id=\"' + divid + '_searchtext\" type=\"text\" ';
   str += 'autocomplete=\"off\" ';
   str += 'autocorrect=\"off\" ';
   if(Boolean(def)) {
      str += 'value=\"' + def + '\" ';
      str += 'onblur=\"if(this.value == \'\' || this.value == \'' + def + '\'){ this.value = \'' + def + '\';this.style.fontStyle=\'italic\';this.style.color=\'#BBBBBB\';}\" ';
      str += 'onfocus=\"if(this.value == \'' + def + '\'){ this.value = \'\';this.style.fontStyle=\'normal\';this.style.color=\'#111111\';}\" ';
      str += 'style=\"color:#BBBBBB;font-style:italic;width:' + width + 'px;font-size:' + fsz + 'px;\" ';
   } else {
      str += 'value=\"\" ';
      str += 'style=\"color:#000000;width:' + width + 'px;font-size:' + fsz + 'px;\" ';
   }
   str += '>';
   str += '<div id=\"' + divid + '_searchsuggest\" class=\"jsfsearchsuggest\" style=\"position:absolute;top:' + (Math.round(fsz * 1.8)) + 'px;left:6px;font-size:' + fsz + 'px;color:#444444;background-color:#F1F1F1;font-weight:normal;height:' + (fsz * 10) + 'px;opacity:0.85;padding:5px;display:none;z-index:90;\"></div>';
   str += '</div>';
   if(!Boolean(nobutton)) str += '<div onclick=\"' + jsfsearch_onenter[divid] + '(\'' + divid + '\');\" style=\"float:left;margin:0px 5px 0px 5px;padding:4px;text-align:center;width:55px;font-size:12px;background-color:#FFFFFF;color:#222222;border:1px solid #333333;border-radius:4px;cursor:pointer;\">Go</div>';
   str += '<div style=\"clear:both;\"></div>';
   str += '</div>';
   
   jQuery('#' + divid).html(str);
   jsfsearch_addlistener(wd_id,divid,def);
}

function jsfsearch_submitsearchphrase(divid){
   alert('test search submitted: ' + jQuery('#' + divid + '_searchtext').val());
}

function jsfsearch_addlistener(wd_id,divid,def) {
   jQuery(document).keyup(function(e) {
       if (e.keyCode == 27) {
          jQuery('.jsfsearchsuggest').hide();
       }
   });
   
   jQuery('#' + divid + '_searchtext').keyup(function(e) {
       //alert('key code: ' + e.keyCode);
       if (e.keyCode == 27) {
          jQuery('#' + divid + '_searchsuggest').hide();
          if(Boolean(def) && jQuery('#' + divid + '_searchtext').val()==def) jQuery('#' + divid + '_searchtext').val('');
       } else if (e.keyCode == 40) {
          jsfsearch_selectitem(divid,(jsfsearch_arrowindex[divid] + 1),true);
       } else if (e.keyCode == 38) {
          jsfsearch_selectitem(divid,(jsfsearch_arrowindex[divid] - 1),true);
       } else if (Boolean(jsfsearch_onenter[divid]) && e.keyCode == 13) {
         var jsfsearchFunct = window[jsfsearch_onenter[divid]];
         jsfsearchFunct(divid);          
       } else if(Boolean(jsfsearch_waitforpause)) {
          clearTimeout(jsfsearch_timer[divid]);
          jsfsearch_timer[divid] = setTimeout(jsfsearch_suggesttimeout, jsfsearch_interval, wd_id, divid);
          //alert('set timeout');
       //} else if (e.keyCode>=48 && e.keyCode<=90) {
       } else {
          jsfsearch_suggesttimeout(wd_id,divid);
       }
   });
}

function jsfsearch_suggesttimeout(wd_id,divid) {
   jsfsearch_suggest(wd_id,jQuery('#' + divid + '_searchtext').val(),divid);
}

function jsfsearch_display(jsondata) {
   //alert('suggestions: ' + JSON.stringify(jsondata));
   //jQuery('#' + jsondata.divid + '_searchsuggest').hide();
   jsfsearch_arrowindex[jsondata.divid] = 0;
   var str = '';
   var searchtxt;
   
   jsfsearch_answers[jsondata.divid] = jsondata.ans;
   if(Boolean(jsondata.ans) && jsondata.ans.length>0) {
      
      for(var i=0;i<jsondata.ans.length;i++) {
         str += '<div ';
         str += 'id=\"' + jsondata.divid + '_searchsuggest_' + (i + 1) + '\" ';
         str += 'class=\"jsfsearch_suggest\" ';
         str += 'data-txt=\"';
         if (Boolean(jsondata.start)) str += jsondata.start;
         str += ' ' + jsondata.ans[i] + '\" ';
         str += 'style=\"cursor:pointer;\" ';
         str += 'onclick=\"event.stopPropagation();jsfsearch_selectitem(\'' + jsondata.divid + '\',' + (i + 1) + ');\" ';
         str += '>';
         if (Boolean(jsondata.start)) str += jsondata.start;
         str += ' <span style=\"font-weight:bold;\">'+jsondata.ans[i]+'</b>';
         str += '</div>';
         if(i>9) break;
      }
      
      jQuery('#' + jsondata.divid + '_searchsuggest').html(str);
      jQuery('#' + jsondata.divid + '_searchsuggest').show();
   }
   jsfsearch_searching[jsondata.divid] = false;
}

function jsfsearch_selectitem(divid,i,usearrow){
   if(!Boolean(i) || i<1) {
      jsfsearch_arrowindex[divid] = 0;
      jQuery('#' + divid + '_searchsuggest').hide();
   } else if(!Boolean(jsfsearch_answers[divid]) || i > jsfsearch_answers[divid].length) {
      // do nothing - probaby down arrow when there's none left
   } else {
      jQuery('#' + divid + '_searchtext').val(jQuery('#' + divid + '_searchsuggest_' + i).data('txt'));
      if(!Boolean(usearrow)) {
         jQuery('#' + divid + '_searchsuggest').hide();
         jQuery('#' + divid + '_searchtext').focus();
         
         // if not using arrow, this is a result of clicking
         var jsfsearchFunct = window[jsfsearch_onenter[divid]];
         jsfsearchFunct(divid);          
      } else {
         jsfsearch_arrowindex[divid] = i;
         jQuery('.jsfsearch_suggest').css('background-color','');
         jQuery('.jsfsearch_suggest').css('color','');
         jQuery('#' + divid + '_searchsuggest_' + i).css('background-color','#555555');
         jQuery('#' + divid + '_searchsuggest_' + i).css('color','#E0E0E0');
      }
   }
}