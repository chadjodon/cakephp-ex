//---------------------------------------
// Application
//---------------------------------------
var cal_name;
var cal_open=false;
var cal_style=0;

function showCalendarInput(name,defaultval,onchange,calstyle){
   if (Boolean(calstyle) && calstyle>0) cal_style = calstyle;
   //alert('***chj*** name: ' + name + ' defaultval: ' + defaultval + ' onchange: ' + onchange + '.');
   var htmlstr = '';
   htmlstr = htmlstr + '<div style=\"position:relative;\">';
   htmlstr = htmlstr + '<table cellpadding=\"1\" cellspacing=\"2\"><tr><td>';
   htmlstr = htmlstr + '<input type=\"text\" name=\"' + name + '\" id=\"' + name + '\" ';
   htmlstr = htmlstr + 'style=\"font-size:12px;font-family:verdana;color:#995555;\" ';
   htmlstr = htmlstr + 'value=\"';
   if(Boolean(defaultval)) htmlstr = htmlstr + defaultval;
   htmlstr = htmlstr + '\" ';
   htmlstr = htmlstr + 'onkeyup=\"';
   if(Boolean(onchange)) htmlstr = htmlstr + onchange;
   htmlstr = htmlstr + '\" ';
   htmlstr = htmlstr + '>';
   htmlstr = htmlstr + '</td><td>';
   htmlstr = htmlstr + '<div ';
   htmlstr = htmlstr + 'onclick=\"openCalendar(\'' + name + '\');';
   if(Boolean(onchange)) htmlstr = htmlstr + onchange;
   htmlstr = htmlstr + '\" ';
   htmlstr = htmlstr + 'style=\"cursor:pointer;height:16px;width:16px;padding:4px;font-size:12px;color:#000000;border:1px solid #808080;border-radius:4px;background-color:#DDDDDD;\">...</div>';
   htmlstr = htmlstr + '</td></tr></table>';
   htmlstr = htmlstr + '<div id=\"cal_' + name + '\" style=\"position:absolute;z-index:999;background-color:#AAAAAA;border:2px solid #707070;border-radius:4px;padding:5px;display:none;\">';
   htmlstr = htmlstr + '</div>';
   htmlstr = htmlstr + '</div>';
   return htmlstr;
}

function openCalendar(name){
   var opennew = true;
   
   if (cal_open) {
      if (cal_name==name) opennew=false;
      closeCalendar();
   }
   
   if (opennew) {
      cal_open = true;
      cal_name = name;
      var datestr = document.getElementById(cal_name).value;
      var y;
      var m;
      if(Boolean(datestr)){
         var timestr = datestr.split(" ");
         //var temp = datestr.split("/");
         //var temp2 = datestr.split("-");
         //var temp3 = datestr.split(".");
         var temp = timestr[0].split("/");
         var temp2 = timestr[0].split("-");
         var temp3 = timestr[0].split(".");
         if(Boolean(temp) && temp.length==3) {
            m = parseInt(temp[0])
            y = parseInt(temp[2])
         } else if(Boolean(temp2) && temp2.length==3) {
            m = parseInt(temp2[1])
            y = parseInt(temp2[0])
         } else if(Boolean(temp3) && temp3.length==3) {
            m = parseInt(temp3[0])
            y = parseInt(temp3[2])
         }
      }
      
      var htmlstr = setCal(y,m,'','','','','jsf_changeCalendar','jsf_chooseCalendarDay');
      document.getElementById('cal_' + cal_name).innerHTML = htmlstr;
      document.getElementById('cal_' + cal_name).style.display = '';
   }
}

function closeCalendar(){
   cal_open = false;
   document.getElementById('cal_' + cal_name).innerHTML = '';
   document.getElementById('cal_' + cal_name).style.display = 'none';
   cal_name = '';
}

function jsf_changeCalendar(m,y){
   var htmlstr = setCal(y,m,'','','','','jsf_changeCalendar','jsf_chooseCalendarDay');
   document.getElementById('cal_' + cal_name).innerHTML = htmlstr;
}

function jsf_chooseCalendarDay(d,m,y){
   var str = '';
   if (Boolean(cal_style) && cal_style==1) {
      str = str + y + '-';
      if(m<10) str = str + '0';
      str = str + m.toString() + '-';
      if(d<10) str = str + '0';
      str = str + d.toString();
   } else {
      if(m<10) str = str + '0';
      str = str + m.toString() + '/';
      if(d<10) str = str + '0';
      str = str + d.toString() + '/';
      str = str + y;
   }
   //alert('ID: ' + cal_name + ' date: ' + str);
   document.getElementById(cal_name).value = str;
   //jQuery('#' + cal_name).val(str);
   closeCalendar();
}




//---------------------------------------
// API: setCal - returns an HTML string of a calendar given a date, appointments
//    and functions
//---------------------------------------
function setCal(year,month,date,wd,ht,appts,calfunc,dayfunc) {
   var now = new Date();
   if (!Boolean(year)) year = now.getFullYear();

   if (!Boolean(month)) month = now.getMonth() + 1;
   if (month==(now.getMonth() + 1) && year==now.getFullYear() && !Boolean(date)) date = now.getDate();
   else if (!Boolean(date)) date=367;

   var firstDayInstance = new Date(year, (month - 1), 1);
   var firstDay = firstDayInstance.getDay();
   
   var days = getDays(month, year);
   return drawCal(firstDay + 1, days, date, month, year, wd, ht, appts, calfunc, dayfunc);
}


//---------------------------------------
// Required methods for API
//---------------------------------------
function drawCal(firstDay, lastDate, date, month, year, wd, ht, appts, calfunc, dayfunc) {
   //alert(firstDay + ', ' + lastDate + ', ' + date + ', ' + monthName + ', ' + year);
   if (!Boolean(wd)) wd = 30;
   if (!Boolean(ht)) ht = 30;
   if (!Boolean(calfunc)) calfunc = 'changeCalendar';
   if (!Boolean(dayfunc)) dayfunc = 'chooseCalendarDay';

   var monthName = getMonthAbbrev(month);
   
   var text = '';
   text += '<div style=\"position:relative;\">';
   text += '<div style=\"position:absolute;right:1px;top:1px;color:red;font-size:10px;font-weight:bold;cursor:pointer;\" onclick=\"closeCalendar();\">X</div>';
   text += '<table cellspacing=\"0\" cellpadding=\"0\">';
   text += '<tr><td colspan=\"7\">';

   var prevyear = parseInt(year);
   var prevmonth = parseInt(month) - 1;
   if (prevmonth<1) {
      prevmonth = 12;
      prevyear = parseInt(prevyear) - 1;
   }
   var nextyear = parseInt(year);
   var nextmonth = parseInt(month) + 1;
   if (nextmonth>12) {
      nextmonth = 1;
      nextyear = parseInt(nextyear) + 1;
   }
   //alert('py: ' + prevyear + ' pm: ' + prevmonth + ' ny: ' + nextyear + ' nm: ' + nextmonth);
   text += '<span onclick=\"' + calfunc + '(' + prevmonth + ',' + prevyear + ');\" style=\"text-align:center;font-size:12px;cursor:pointer;color:blue;\">prev</span> &nbsp; &nbsp;';
   text += '<span style=\"text-align:center;font-size:14px;font-weight:bold;\">' + monthName + ' ';
   //text += year;
   text += '<select name=\"jsfyear\" id=\"jsfyear\" style=\"font-size:10px;font-family:verdana;\" onChange=\"' + calfunc + '(' + month + ',document.getElementById(\'jsfyear\').value);\">';
   var st = 2040;
   var en = 1940;
   
   if(year>(st-5)) st = year + 5;
   else if(year<(en+5)) en = year - 5;
   
   for(var i=st;i>en;i--) {
      var sel = '';
      if(i==year) sel=' SELECTED';
      text += '<option value=\"' + i + '\"' + sel + '>' + i + '</option>';
   }
   text += '</select>';
   text += '</span>';
   text += ' &nbsp; &nbsp;<span onclick=\"' + calfunc + '(' + nextmonth + ',' + nextyear + ');\" style=\"text-align:center;font-size:12px;cursor:pointer;color:blue;\">next</span>';
   text += '</td></tr>';

   text += '<tr><td colspan=\"7\">';
   text += '<div style=\"width:3px;height:3px;overflow:hidden;\"></div>';
   text += '</td></tr>';

   text += '<tr align=\"center\" valign=\"middle\">';
   for (var i=0; i<7; i++) {
      text += '<td align=\"center\" style=\"font-size:12px;font-family:arial;color:#777777;\">' + getDayName(i) + '</td>'; 
   }
   text += '</tr>';

   var digit = 1;
   var curCell = 1;
   var totalrows = Math.ceil((lastDate + firstDay - 1)/7);
   for (var row=1; row<=totalrows; row++) {
      text += '<tr align=\"right\" valign=\"top\">';
      for (var col=1; col<=7; col++) {
         var style = 'width:' + wd.toString() + 'px;height:' + ht.toString() + 'px;text-align:right;font-family:arial;border-top:1px solid #222222;border-left:1px solid #222222;cursor:pointer;';
         if (col==7 || digit==lastDate) style = style + 'border-right:1px solid #222222;';
         if ((digit+7)>lastDate) style = style + 'border-bottom:1px solid #222222;';
         if (digit > lastDate) break;
         if (curCell < firstDay) {
            text += '<td></td>';
            curCell++;
         } else {
            var onclickfunc = dayfunc + '(' + digit + ',' + month + ',' + year + ');';
            if (digit == date) {
               text += '<td onclick=\"' + onclickfunc + '\" style=\"' + style + 'font-size:12px;font-weight:bold;color:red;\">';
            } else {
               text += '<td onclick=\"' + onclickfunc + '\" style=\"' + style + 'font-size:10px;color:#555555;\">';
            }
            text += digit;

            var dateindx = year;
            if (month<10) dateindx = dateindx + '-0' + month;
            else dateindx = dateindx + '-' + month;
            if (digit<10) dateindx = dateindx + '-0' + digit;
            else dateindx = dateindx + '-' + digit;
            if (Boolean(appts) && Boolean(appts[dateindx])) {
               for (var i=0; i<appts[dateindx].length; i++) {
                  text += '<div style=\"margin:1px;float:left;width:6px;height:6px;overflow:hidden;border-radius:3px;background-color:' + appts[dateindx][i]['color'] + ';\"></div>';
               }
               text += '<div style=\"clear:both;width:1px;height:1px;overflow:hidden;\"></div>';
            }

            text += '</td>';
            digit++;
         }
      }
      text += '</tr>';
   }

   text += '</table>';
   text += '</div>';
   return text;
}

function drawWeek(year,month,date,wd,appts){
   var now = new Date();
   if (!Boolean(year)) year = now.getFullYear();
   if (!Boolean(month)) month = now.getMonth() + 1;
   if (!Boolean(date)) date = now.getDate();

   if (!Boolean(wd)) wd = 65;
   var daywidth = wd;

   var fontsize = 14;
   if (wd<45) {
      fontsize = 12;
      daywidth = wd * 7;
   } else if (wd<110) {
      fontsize = 8;
   }

   var prevyear = year;
   var prevmonth = month - 1;
   if (prevmonth<1) {
      prevmonth = 12;
      prevyear = prevyear - 1;
   }
   var nextyear = year;
   var nextmonth = month + 1;
   if (nextmonth>12) {
      nextmonth = 1;
      nextyear = nextyear + 1;
   }

   var sunMonth = month;
   var sunYear = year;
   var firstDayInstance = new Date(year, (month - 1), date);
   var sunDay = date - firstDayInstance.getDay();
   if (sunDay < 1) {
      sunDay = getDays(prevmonth, prevyear) + sunDay;
      sunMonth = prevmonth;
      sunYear = prevyear;
   }

   var sunMax = getDays(sunMonth, sunYear);

   var htmlstr = '';
   var style = 'position:relative;width:' + daywidth.toString() + 'px;';
   //style = style + 'height:' + ((fontsize * 14) + (fontsize * 3)).toString() + 'px;';
   //style = style + 'overflow:hidden;';
   style = style + 'min-height:100px;';
   style = style + 'padding:2px;font-family:arial;color:#444444;font-size:' + fontsize + 'px;';
   htmlstr = htmlstr + '<table cellpadding=\"0\" cellspacing=\"0\"><tr valign=\"top\">';
   for (var i=0; i<7; i++) {
      var curry = sunYear;
      var currm = sunMonth;
      var currd = sunDay + i;
      if (currd>sunMax) {
         currd = currd - sunMax;
         currm++;
         if (currm>12) {
            currm = 1;
            curry++;
         }
      }
      if (wd>=45 || (year==curry && month==currm && date==currd)) {
         htmlstr = htmlstr + '<td style=\"border:1px solid #AAAAAA;\">';
         htmlstr = htmlstr + '<div style=\'' + style + '\'>';
         htmlstr = htmlstr + '<div style=\'padding:3px;border-radius:3px;background-color:#999999;color:#FFFFFF;\'>';
         htmlstr = htmlstr + getDayName(i) + ' ' + getMonthAbbrev(currm) + ' ' + currd.toString() + ', ' + curry.toString();
         htmlstr = htmlstr + '</div>';
         
         var dateindx = curry;
         if (currm<10) dateindx = dateindx + '-0' + currm;
         else dateindx = dateindx + '-' + currm;
         if (currd<10) dateindx = dateindx + '-0' + currd;
         else dateindx = dateindx + '-' + currd;
         if (Boolean(appts) && Boolean(appts[dateindx])) {
            for (var j=0; j<appts[dateindx].length; j++) {
               htmlstr = htmlstr + '<div onclick=\"' + appts[dateindx][j]['onclick'] + '\" style=\"margin-top:10px;margin-bottom:2px;margin-right:2px;margin-left:2px;padding-top:1px;padding-bottom:1px;border-top:1px solid #343434;background-color:#DEDEDE;font-size:' + fontsize.toString() + 'px;font-family:arial;color:' + appts[dateindx][j]['color'] + ';\">';
               htmlstr = htmlstr  + '<span style=\"font-weight:bold;\">' + appts[dateindx][j]['start'];
               htmlstr = htmlstr  + ' ' + appts[dateindx][j]['customer'] + '</span>';
               htmlstr = htmlstr  + ' - ' + appts[dateindx][j]['name'];
               if (Boolean(appts[dateindx][j]['info'])) htmlstr = htmlstr  + ': ' + appts[dateindx][j]['info'];
               htmlstr = htmlstr + '</div>';
            }
         }
         htmlstr = htmlstr + '</div>';
         htmlstr = htmlstr + '</td>';
      }
   }
   htmlstr = htmlstr + '</tr></table>';

   return htmlstr;
}

function getDays(month, year) {
   var monthindex = month - 1;
   var ar = new Array(12);
   ar[0] = 31;
   ar[1] = (year % 4 == 0) ? 29 : 28;
   ar[2] = 31;
   ar[3] = 30;
   ar[4] = 31;
   ar[5] = 30;
   ar[6] = 31;
   ar[7] = 31;
   ar[8] = 30;
   ar[9] = 31;
   ar[10] = 30;
   ar[11] = 31;
   
   return ar[monthindex];
}

function getMonthName(month) {
   var monthindex = month - 1;
   // create array to hold name of each month
   var ar = new Array(12);
   ar[0] = "January";
   ar[1] = "February";
   ar[2] = "March";
   ar[3] = "April";
   ar[4] = "May";
   ar[5] = "June";
   ar[6] = "July";
   ar[7] = "August";
   ar[8] = "September";
   ar[9] = "October";
   ar[10] = "November";
   ar[11] = "December";
   
   return ar[monthindex];
}

function getMonthAbbrev(month) {
   var monthindex = month - 1;
   // create array to hold name of each month
   var ar = new Array(12);
   ar[0] = "Jan";
   ar[1] = "Feb";
   ar[2] = "Mar";
   ar[3] = "Apr";
   ar[4] = "May";
   ar[5] = "Jun";
   ar[6] = "Jul";
   ar[7] = "Aug";
   ar[8] = "Sep";
   ar[9] = "Oct";
   ar[10] = "Nov";
   ar[11] = "Dec";
   
   return ar[monthindex];
}

function getDayName(indx){
   var weekDay = new Array(7);
   weekDay[0] = 'Sun';
   weekDay[1] = 'Mon';
   weekDay[2] = 'Tues';
   weekDay[3] = 'Wed';
   weekDay[4] = 'Thu';
   weekDay[5] = 'Fri';
   weekDay[6] = 'Sat';
   return weekDay[indx];
}
