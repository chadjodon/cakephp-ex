   var jsfwd_updateArr;
   var jsfwd_updatepending;

   function jsfwd_updateValue(key){
      var addkey = true;
      for (var i=0;i<jsfwd_updateArr.length;i++) {
         if (jsfwd_updateArr[i]==key) addkey = false;
      }
      if (addkey) jsfwd_updateArr.push(key);
      if (!jsfwd_updatepending) jsfwd_setPendingUpdates();
   }

   function jsfwd_updateWebsiteData(wd_id,domain,callback,userid,token){
      if (!Boolean(domain)) domain = defaultremotedomain;
      else defaultremotedomain = domain;
      var url = domain + 'jsfcode/jsonpcontroller.php?action=updatewebdata';
      if (!Boolean(callback)) callback = 'jsfwd_updateWebsiteDataResult';
      url = url + '&callback=' + encodeURIComponent(callback);
      if (Boolean(wd_id)) url = url + '&wd_id=' + encodeURIComponent(wd_id);
      url = url + '&userid=' + encodeURIComponent(userid);
      url = url + '&token=' + encodeURIComponent(token);
      url = url + '&wnew-userid=' + encodeURIComponent(userid);
      url = url + '&new-header=1';
      url = url + '&new-srchfld=1';

      for (var i=0;i<jsfwd_updateArr.length;i++) {
         //alert('yyyy: ' + jsfwd_updateArr[i] + ' val: ' + $('#' + jsfwd_updateArr[i]).val());
         var el = document.getElementById(jsfwd_updateArr[i]);
         if(el.type=='checkbox') {
            if (el.checked) url = url + '&' + encodeURIComponent(jsfwd_updateArr[i]) + '=' + encodeURIComponent($('#' + jsfwd_updateArr[i]).val());
            else url = url + '&' + encodeURIComponent(jsfwd_updateArr[i]) + '=0';
         } else {
            var val = $('#' + jsfwd_updateArr[i]).val();
            if (!Boolean(val)) val='&nbsp;';
            url = url + '&' + encodeURIComponent(jsfwd_updateArr[i]) + '=' + encodeURIComponent(val);
         }
      }

      //alert(url);
      jsfwebdata_CallJSONP(url);
   }

   function jsfwd_updateWebsiteDataResult(jsondata){
      if (typeof jsf_endjsoning == 'function') jsf_endjsoning();
      var failed=true;
      if (Boolean(jsondata.responsecode)) {
         if (jsondata.responsecode==1) {
            jsfwd_setNoPendingUpdates();
            failed = false;
         }
      }

      if (failed) {
         alert('Your form was not updated.  There was an internal error or a network connectivity problem.');
      } else {
         jsfwd_updateArr = [];
         alert('Your form was updated successfully.');
      }

      jsfwd_refreshScreen(jsondata.wd_id,'',jsondata.userid,jsondata.token);
   }

   function jsfwd_deleteQuestion(wd_id,field_id,callback,userid,token){
      if (confirm('Are you sure you want to permanently delete this question?')) {
         var url = domain + 'jsfcode/jsonpcontroller.php?action=removewdquestion';
         if (!Boolean(callback)) callback = 'jsfwd_deleteQuestionResult';
         url = url + '&callback=' + encodeURIComponent(callback);
         url = url + '&wd_id=' + encodeURIComponent(wd_id);
         url = url + '&field_id=' + encodeURIComponent(field_id);
         url = url + '&userid=' + encodeURIComponent(userid);
         url = url + '&token=' + encodeURIComponent(token);

         //alert("url: " + url);
         jsfwebdata_CallJSONP(url);
      }
   }

   function jsfwd_deleteQuestionResult(jsondata){
      if (typeof jsf_endjsoning == 'function') jsf_endjsoning();
      var failed=true;
      if (Boolean(jsondata.responsecode)) {
         if (jsondata.responsecode==1) {
            if (Boolean(jsondata.field_id)) {
               failed = false;
               var temparr = [];
               var quest = jsondata.field_id;
               if (Boolean(jsfwd_updateArr)) {
                  for (var i=0;i<jsfwd_updateArr.length;i++) {
                     var spl = jsfwd_updateArr[i].split("-");
                     if (spl[0]!=quest) temparr.push(jsfwd_updateArr[i]);
                  }
                  if (!Boolean(temparr) || temparr.length<1) {
                     jsfwd_updateArr = [];
                     jsfwd_setNoPendingUpdates();
                  } else {
                     jsfwd_updateArr = temparr;
                  }
               }
            }
         }
      } 

      if(failed) alert('We could not remove that question.  Please check and try again later.');
      jsfwd_refreshScreen(jsondata.wd_id,'',jsondata.userid,jsondata.token);
   }

   function jsfwd_setPendingUpdates(){
      //alert('jsfwd_setPendingUpdates()');
      jsfwd_updatepending = true;
      $('#s-1_outertable').css('background-color','#f41919');
      $('.sectionupdatebutton').show();
      $('.sectioncancelbutton').show();
      //$('.questionupdatebutton').show();            
   }

   function jsfwd_setNoPendingUpdates(){
      jsfwd_updateArr = [];
      jsfwd_updatepending=false;
      $('#s-1_outertable').css('background-color','#444444');
      $('.sectionupdatebutton').hide();
      //$('.questionupdatebutton').hide();            
      $('.sectioncancelbutton').hide();
   }

   function jsfwd_refreshScreen(wd_id,wdname,userid,token){

      var loadpage = false;
      if(jsfwd_updatepending) {
         if (confirm('Are you sure you want to cancel your changes?')) loadpage=true;
      } else {
         loadpage=true;
      }

      if (loadpage && Boolean(userid) && Boolean(token)) {
         var url = domain + 'jsfcode/jsonpcontroller.php?action=adminwebdata';
         url = url + '&callback=jsfwd_refreshCallback';
         if(Boolean(wd_id)) url = url + '&wd_id=' + encodeURIComponent(wd_id);
         if(Boolean(wdname)) url = url + '&wdname=' + encodeURIComponent(wdname);
         url = url + '&userid=' + encodeURIComponent(userid);
         url = url + '&token=' + encodeURIComponent(token);
         //alert("url: " + url);
         jsfwebdata_CallJSONP(url);
      }
   }

   function jsfwd_refreshCallback(jsondata){
      if (typeof jsf_endjsoning == 'function') jsf_endjsoning();
      if (jsondata.responsecode==1) {
         jsfwd_updateArr = [];
         jsfwd_updatepending = false;
         jQuery('#jsfwdarea').html(jsondata.html);
      }
   }

   function jsfwd_deleteSection(wd_id,section,callback,userid,token){
      if (confirm('Are you sure you want to permanently delete this section?')) {
         var url = domain + 'jsfcode/jsonpcontroller.php?action=removewdsection';
         if (!Boolean(callback)) callback = 'jsfwd_deleteSectionResult';
         url = url + '&callback=' + encodeURIComponent(callback);
         url = url + '&wd_id=' + encodeURIComponent(wd_id);
         url = url + '&section=' + encodeURIComponent(section);
         url = url + '&userid=' + encodeURIComponent(userid);
         url = url + '&token=' + encodeURIComponent(token);
         //alert("url: " + url);
         jsfwebdata_CallJSONP(url);
      }
   }

   function jsfwd_deleteSectionResult(jsondata){
      if (typeof jsf_endjsoning == 'function') jsf_endjsoning();
      var failed=true;
      if (Boolean(jsondata.responsecode)) {
         if (jsondata.responsecode==1) {
            if (Boolean(jsondata.section)) {
               failed = false;
               if (Boolean(jsfwd_updateArr)) {
                  var temparr = [];
                  var sect = 's' + jsondata.section;
                  for (var i=0;i<jsfwd_updateArr.length;i++) {
                     var spl = jsfwd_updateArr[i].split("-");
                     if (spl[0]!=sect) temparr.push(jsfwd_updateArr[i]);
                  }
                  if (!Boolean(temparr) || temparr.length<1) {
                     jsfwd_setNoPendingUpdates();
                  } else {
                     jsfwd_updateArr = temparr;
                  }
               }
            }
         }
      } 

      if(failed) alert('We could not remove that section.  Please check and try again later.');
      jsfwd_refreshScreen(jsondata.wd_id,'',jsondata.userid,jsondata.token);
   }

   
   
