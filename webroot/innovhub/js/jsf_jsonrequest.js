var jsf_json_xmlhttp;
var jsfCMSFunctToCall;
var jsf_json_using=false;

var jsf_json_xmlhttp2;
var jsfCMSFunctToCall2;
var jsf_json_using2=false;

var jsf_json_xmlhttp3;
var jsfCMSFunctToCall3;
var jsf_json_using3=false;

var jsf_json_xmlhttp4;
var jsfCMSFunctToCall4;
var jsf_json_using4=false;

var jsf_json_xmlhttp5;
var jsfCMSFunctToCall5;
var jsf_json_using5=false;

var jsf_json_xmlhttp6;
var jsfCMSFunctToCall6;
var jsf_json_using6=false;

var jsf_json_xmlhttp7;
var jsfCMSFunctToCall7;
var jsf_json_using7=false;

var jsf_json_xmlhttp8;
var jsfCMSFunctToCall8;
var jsf_json_using8=false;

var jsf_json_xmlhttp9;
var jsfCMSFunctToCall9;
var jsf_json_using9=false;


function jsf_json_sendRequest(url,functioncall) {
   //alert(url);
   if (!jsf_json_using) {
      jsf_json_using = true;
      jsfCMSFunctToCall = functioncall;
      jsf_json_xmlhttp=jsf_json_GetXmlHttpObject();
      if (jsf_json_xmlhttp==null) {
         alert ("Browser does not support HTTP Request");
         return;
      }
      jsf_json_xmlhttp.onreadystatechange=jsf_json_stateChanged;
      //alert('sending: ' + url);
      jsf_json_xmlhttp.open("GET",url,true);
      jsf_json_xmlhttp.send(null);
   } else if (!jsf_json_using2) {
      jsf_json_using2 = true;
      jsfCMSFunctToCall2 = functioncall;
      jsf_json_xmlhttp2=jsf_json_GetXmlHttpObject();
      if (jsf_json_xmlhttp2==null) {
         alert ("Browser does not support HTTP Request");
         return;
      }
      jsf_json_xmlhttp2.onreadystatechange=jsf_json_stateChanged2;
      jsf_json_xmlhttp2.open("GET",url,true);
      jsf_json_xmlhttp2.send(null);
   } else if (!jsf_json_using3) {
      jsf_json_using3 = true;
      jsfCMSFunctToCall3 = functioncall;
      jsf_json_xmlhttp3=jsf_json_GetXmlHttpObject();
      if (jsf_json_xmlhttp3==null) {
         alert ("Browser does not support HTTP Request");
         return;
      }
      jsf_json_xmlhttp3.onreadystatechange=jsf_json_stateChanged3;
      jsf_json_xmlhttp3.open("GET",url,true);
      jsf_json_xmlhttp3.send(null);
   } else if (!jsf_json_using4) {
      jsf_json_using4 = true;
      jsfCMSFunctToCall4 = functioncall;
      jsf_json_xmlhttp4=jsf_json_GetXmlHttpObject();
      if (jsf_json_xmlhttp4==null) {
         alert ("Browser does not support HTTP Request");
         return;
      }
      jsf_json_xmlhttp4.onreadystatechange=jsf_json_stateChanged4;
      jsf_json_xmlhttp4.open("GET",url,true);
      jsf_json_xmlhttp4.send(null);
   } else if (!jsf_json_using5) {
      jsf_json_using5 = true;
      jsfCMSFunctToCall5 = functioncall;
      jsf_json_xmlhttp5=jsf_json_GetXmlHttpObject();
      if (jsf_json_xmlhttp5==null) {
         alert ("Browser does not support HTTP Request");
         return;
      }
      jsf_json_xmlhttp5.onreadystatechange=jsf_json_stateChanged5;
      jsf_json_xmlhttp5.open("GET",url,true);
      jsf_json_xmlhttp5.send(null);
   } else if (!jsf_json_using6) {
      jsf_json_using6 = true;
      jsfCMSFunctToCall6 = functioncall;
      jsf_json_xmlhttp6=jsf_json_GetXmlHttpObject();
      if (jsf_json_xmlhttp6==null) {
         alert ("Browser does not support HTTP Request");
         return;
      }
      jsf_json_xmlhttp6.onreadystatechange=jsf_json_stateChanged6;
      jsf_json_xmlhttp6.open("GET",url,true);
      jsf_json_xmlhttp6.send(null);
   } else if (!jsf_json_using7) {
      jsf_json_using7 = true;
      jsfCMSFunctToCall7 = functioncall;
      jsf_json_xmlhttp7=jsf_json_GetXmlHttpObject();
      if (jsf_json_xmlhttp7==null) {
         alert ("Browser does not support HTTP Request");
         return;
      }
      jsf_json_xmlhttp7.onreadystatechange=jsf_json_stateChanged7;
      jsf_json_xmlhttp7.open("GET",url,true);
      jsf_json_xmlhttp7.send(null);
   } else if (!jsf_json_using8) {
      jsf_json_using8 = true;
      jsfCMSFunctToCall8 = functioncall;
      jsf_json_xmlhttp8=jsf_json_GetXmlHttpObject();
      if (jsf_json_xmlhttp8==null) {
         alert ("Browser does not support HTTP Request");
         return;
      }
      jsf_json_xmlhttp8.onreadystatechange=jsf_json_stateChanged8;
      jsf_json_xmlhttp8.open("GET",url,true);
      jsf_json_xmlhttp8.send(null);
   } else if (!jsf_json_using9) {
      jsf_json_using9 = true;
      jsfCMSFunctToCall9 = functioncall;
      jsf_json_xmlhttp9=jsf_json_GetXmlHttpObject();
      if (jsf_json_xmlhttp9==null) {
         alert ("Browser does not support HTTP Request");
         return;
      }
      jsf_json_xmlhttp9.onreadystatechange=jsf_json_stateChanged9;
      jsf_json_xmlhttp9.open("GET",url,true);
      jsf_json_xmlhttp9.send(null);
   }
}

function jsf_json_sendRequest2(url,functioncall) {
   jsf_json_sendRequest(url,functioncall)
}
function jsf_json_sendRequest3(url,functioncall) {
   jsf_json_sendRequest(url,functioncall)
}
function jsf_json_sendRequest4(url,functioncall) {
   jsf_json_sendRequest(url,functioncall)
}


function jsf_json_GetXmlHttpObject() {
   if (window.XMLHttpRequest) {
      // code for IE7+, Firefox, Chrome, Opera, Safari
      return new XMLHttpRequest();
   }
   if (window.ActiveXObject) {
      // code for IE6, IE5
      return new ActiveXObject("Microsoft.XMLHTTP");
   }
   return null;
}




function jsf_json_stateChanged() {
   //alert('readystate: ' + jsf_json_xmlhttp.readyState);
   if (jsf_json_xmlhttp.readyState==4) {
      var jsonStr=jsf_json_xmlhttp.responseText;
      //alert(jsonStr);
      var jsondata = eval("(" + jsonStr + ")");
      var output = jsfCMSFunctToCall(jsondata);
      jsf_json_using = false;
   }
}

function jsf_json_stateChanged2() {
   //alert('readystate: ' + jsf_json_xmlhttp2.readyState);
   if (jsf_json_xmlhttp2.readyState==4) {
      var jsonStr=jsf_json_xmlhttp2.responseText;
      //alert(jsonStr);
      var jsondata = eval("(" + jsonStr + ")");
      var output = jsfCMSFunctToCall2(jsondata);
      jsf_json_using2 = false;
   }
}

function jsf_json_stateChanged3() {
   //alert('readystate: ' + jsf_json_xmlhttp3.readyState);
   if (jsf_json_xmlhttp3.readyState==4) {
      var jsonStr=jsf_json_xmlhttp3.responseText;
      //alert(jsonStr);
      var jsondata = eval("(" + jsonStr + ")");
      var output = jsfCMSFunctToCall3(jsondata);
      jsf_json_using3 = false;
   }
}

function jsf_json_stateChanged4() {
   //alert('readystate: ' + jsf_json_xmlhttp4.readyState);
   if (jsf_json_xmlhttp4.readyState==4) {
      var jsonStr=jsf_json_xmlhttp4.responseText;
      //alert(jsonStr);
      var jsondata = eval("(" + jsonStr + ")");
      var output = jsfCMSFunctToCall4(jsondata);
      jsf_json_using4 = false;
   }
}

function jsf_json_stateChanged5() {
   //alert('readystate: ' + jsf_json_xmlhttp5.readyState);
   if (jsf_json_xmlhttp5.readyState==4) {
      var jsonStr=jsf_json_xmlhttp5.responseText;
      //alert(jsonStr);
      var jsondata = eval("(" + jsonStr + ")");
      var output = jsfCMSFunctToCall5(jsondata);
      jsf_json_using5 = false;
   }
}

function jsf_json_stateChanged6() {
   //alert('readystate: ' + jsf_json_xmlhttp6.readyState);
   if (jsf_json_xmlhttp6.readyState==4) {
      var jsonStr=jsf_json_xmlhttp6.responseText;
      //alert(jsonStr);
      var jsondata = eval("(" + jsonStr + ")");
      var output = jsfCMSFunctToCall6(jsondata);
      jsf_json_using6 = false;
   }
}

function jsf_json_stateChanged7() {
   //alert('readystate: ' + jsf_json_xmlhttp7.readyState);
   if (jsf_json_xmlhttp7.readyState==4) {
      var jsonStr=jsf_json_xmlhttp7.responseText;
      //alert(jsonStr);
      var jsondata = eval("(" + jsonStr + ")");
      var output = jsfCMSFunctToCall7(jsondata);
      jsf_json_using7 = false;
   }
}

function jsf_json_stateChanged8() {
   //alert('readystate: ' + jsf_json_xmlhttp8.readyState);
   if (jsf_json_xmlhttp8.readyState==4) {
      var jsonStr=jsf_json_xmlhttp8.responseText;
      //alert(jsonStr);
      var jsondata = eval("(" + jsonStr + ")");
      var output = jsfCMSFunctToCall8(jsondata);
      jsf_json_using8 = false;
   }
}

function jsf_json_stateChanged9() {
   //alert('readystate: ' + jsf_json_xmlhttp9.readyState);
   if (jsf_json_xmlhttp9.readyState==4) {
      var jsonStr=jsf_json_xmlhttp9.responseText;
      //alert(jsonStr);
      var jsondata = eval("(" + jsonStr + ")");
      var output = jsfCMSFunctToCall9(jsondata);
      jsf_json_using9 = false;
   }
}
