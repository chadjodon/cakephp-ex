<?php
//error_reporting(E_ALL);
unset($_SESSION['webdata']);


$toolid = getParameter("toolid");

$wd = new WebsiteData();
$webdata = $wd->getWebData("Tools and Widgets");
if($webdata==NULL || $webdata['wd_id']==NULL) {
   $wtxml = "<webdata><structure><name>Tools and Widgets</name><privatesrvy>3</privatesrvy><saveresults>1</saveresults><emailresults>2</emailresults><adminemail>chadjodon@hotmail.com</adminemail>";
   $wtxml .= "<wd_section><section>1034</section><parent_s>-1</parent_s><sequence>10</sequence><wd_field><sequence>1</sequence><label>sequence</label><field_type>INT</field_type><header>1</header></wd_field>";
   $wtxml .= "<wd_field><sequence>2</sequence><label>enabled</label><field_type>DROPDOWN</field_type><question>Yes,No</question><defaultval>Yes</defaultval><header>1</header></wd_field>";
   $wtxml .= "<wd_field><sequence>5</sequence><label>Your Email</label><field_type>TEXT</field_type><header>1</header><required>1</required><map>email</map></wd_field>";
   $wtxml .= "<wd_field><sequence>8</sequence><label>This widget is a case study (similar to VCCS)</label><field_type>SNGLCHKBX</field_type><header>1</header><required>0</required><srchfld>1</srchfld><filterfld>1</filterfld><map>casestudy</map></wd_field>";
   $wtxml .= "<wd_field><sequence>10</sequence><label>Name</label><field_type>TEXT</field_type><header>1</header><required>1</required><srchfld>1</srchfld><filterfld>1</filterfld><map>name</map></wd_field>";
   $wtxml .= "<wd_field><sequence>20</sequence><label>Hashtag</label><field_type>TEXT</field_type><header>1</header><required>1</required><srchfld>1</srchfld><filterfld>1</filterfld><map>htag</map></wd_field>";
   $wtxml .= "<wd_field><sequence>30</sequence><label>URL</label><field_type>TEXT</field_type><header>1</header><map>link</map></wd_field><wd_field><sequence>40</sequence><label>Abbreviation</label><field_type>TEXT</field_type><map>abbrev</map></wd_field>";
   $wtxml .= "<wd_field><sequence>50</sequence><label>Logo</label><field_type>MBL_UPL</field_type><header>1</header><map>img</map></wd_field><wd_field><sequence>60</sequence><label>Created</label><field_type>TEXT</field_type><map>created</map></wd_field>";
   $wtxml .= "<wd_field><sequence>70</sequence><label>Description</label><field_type>TEXTAREA</field_type><map>descr</map></wd_field><wd_field><sequence>80</sequence><label>Help tip</label><field_type>TEXTAREA</field_type><map>help</map></wd_field></wd_section></structure></webdata>";
   $wd->newWebDataFromXML($wtxml);
}

if(getParameter("newtool")==1) {
   $email = getParameter("email");
   $htag = convertHashtag(getParameter("htag"));
   
   $webdata = $wd->getWebData($htag." Pages");
   if($webdata==NULL && $email!=NULL && $htag!=NULL) {
      // Data Grab
      $wd_id = $wd->newWebData($htag." Data Grab","",3,$email,"",1,1);
      $wd->updateWebDataProperty($wd_id, "htags", "#".$htag." ");
      $sid = $wd->addSection($wd_id);
      $wd->addField($wd_id,$sid,NULL,"name",NULL,"TEXT",10,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,NULL);
      $wd->addField($wd_id,$sid,NULL,"email",NULL,"TEXT",20,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,NULL);
      $wd->addField($wd_id,$sid,NULL,"phone",NULL,"TEXT",30,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,NULL);
      $wd->addField($wd_id,$sid,NULL,"comments",NULL,"TEXTAREA",40,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,NULL);
      $wd->addField($wd_id,$sid,NULL,"notes",NULL,"TEXTAREA",50,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,NULL);
      
      // Site menu
      $wd_id = $wd->newWebData($htag." Menu","",3,$email,"",1,0);
      $wd->updateWebDataProperty($wd_id, "htags", "#".$htag." ");
      $sid = $wd->addSection($wd_id);
      $wd->addField($wd_id,$sid,NULL,"Sequence",NULL,"INT",10,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,NULL);
      $wd->addField($wd_id,$sid,NULL,"Enabled","Yes,No","DROPDOWN",20,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,NULL);
      $wd->addField($wd_id,$sid,NULL,"Require Login",NULL,"SNGLCHKBX",30,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,"reqlogin");
      $wd->addField($wd_id,$sid,NULL,"Title",NULL,"TEXT",40,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,NULL);
      $wd->addField($wd_id,$sid,NULL,"Subtitle",NULL,"TEXT",50,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,NULL);
      $wd->addField($wd_id,$sid,NULL,"Short Description",NULL,"TEXTAREA",60,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,"shortdescription");
      $wd->addField($wd_id,$sid,NULL,"Div ID",NULL,"TEXT",70,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,'divid');
      $wd->addField($wd_id,$sid,NULL,"Image",NULL,"MBL_UPL",80,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,NULL);
      $wd->addField($wd_id,$sid,NULL,"Parent",$htag." Menu,Title","FOREIGN",90,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,NULL);
      $wd->addField($wd_id,$sid,NULL,"onclick",NULL,"TEXT",100,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,NULL);
      $wd->addField($wd_id,$sid,NULL,"URL",NULL,"TEXT",110,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,"link");
      $wd->addField($wd_id,$sid,NULL,"Location","menu,bottom,both","DROPDOWN",120,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,NULL);
      
      // Page Builder
      $wd_id = $wd->newWebData($htag." Pages","",3,$email,"",1,0);
      $wd->updateWebDataProperty($wd_id, "htags", "#".$htag." ");
      $sid = $wd->addSection($wd_id);
      $wd->addField($wd_id,$sid,NULL,"Enabled","Yes,No","DROPDOWN",10,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,NULL);
      $wd->addField($wd_id,$sid,NULL,"Name",NULL,"TEXT",10,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,NULL);
      $wd->addField($wd_id,$sid,NULL,"Value",NULL,"TEXT",10,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,NULL);
      $wd->addField($wd_id,$sid,NULL,"Image",NULL,"MBL_UPL",10,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,NULL);
      $wd->addField($wd_id,$sid,NULL,"Version",NULL,"INT",100,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,"version");
      $wd->addField($wd_id,$sid,NULL,"Version Status","NEW,ACTIVE,INACTIVE","DROPDOWN",110,NULL,1,"NEW",0,0,FALSE,NULL,NULL,NULL,"verstatus");
      // addField($wd_id, $parent_s, $field_id, $label, $text, $field_type, $sequence, $privacy, $header=NULL, $defaultval=NULL, $required=NULL, $srchfld=NULL, $pub=FALSE, $notes=NULL, $filterfld=NULL, $style=NULL, $map=NULL)
      
      $casestudy = trim(strtolower(getParameter("casestudy")));
      $typeoftool = trim(strtolower(getParameter("typeoftool")));
      if($casestudy==NULL && 0==strcmp($typeoftool,"casestudy")) $casestudy="yes";
      if(0==strcmp($casestudy,"yes") || 0==strcmp($casestudy,"true") || 0==strcmp($casestudy,"1")) {
         $fn = getBaseURL()."casestudyfiles/casestudycategoriesjdata.xml";
         $wtxml = file_get_contents($fn);
         $wtxml = str_replace("%%%toolid%%%",$htag,$wtxml);
         //print "<div style=\"margin:20px;\">".$wtxml."</div>";
         $wd->newWebDataFromXML($wtxml);
         
         $fn = getBaseURL()."casestudyfiles/casestudiesjdata.xml";
         $wtxml = file_get_contents($fn);
         $wtxml = str_replace("%%%toolid%%%",$htag,$wtxml);
         $wd->newWebDataFromXML($wtxml);
         
         $fn = getBaseURL()."casestudyfiles/resourcecategoriesjdata.xml";
         $wtxml = file_get_contents($fn);
         $wtxml = str_replace("%%%toolid%%%",$htag,$wtxml);
         $wd->newWebDataFromXML($wtxml);
         
         $fn = getBaseURL()."casestudyfiles/resourcesjdata.xml";
         $wtxml = file_get_contents($fn);
         $wtxml = str_replace("%%%toolid%%%",$htag,$wtxml);
         $wd->newWebDataFromXML($wtxml);
         
         
         //print "<div style=\"margin:15px;\">";
         //print "Before the new Case Study tool is ready you still need to:<br>";
         //print "&bull; 1. Copy the contents of /casestudyfiles to /".$htag." on your webserver<br>";
         //print "&bull; 2. Set configuration variables in /".$htag."/pm_casestudy.js <br>";
         //print "&bull; 3. Enter categories, case studies, and resources in their respective jdata tables<br>";
         //print "</div>";
      }
      
   } else {
      print "<br>\nThere was a problem creating this tool template.  Sorry.<br><br><br>\n";
   }
}

?>


<div id="pmtools"></div>

<script src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['jsFolder']; ?>jsf_websitedata.js"></script>
<script src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['jsFolder']; ?>jsf_pagebuilder.js"></script>
<script src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['jsFolder']; ?>jsf_pagebuilder_admin.js"></script>
<script src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['jsFolder']; ?>jsf_pagebuilder_widgets.js"></script>
<script>
   jsfpb_codedir = '<?php echo $GLOBALS['codeFolder']; ?>';
   jsfpb_servercontroller = jsfpb_codedir + 'jsoncontroller.php?format=jsonp';
   jsfwd_servercontroller = jsfpb_servercontroller;
   jsfpb_jsoncontroller = jsfpb_codedir + 'jsoncontroller.php?format=json';

   jsfpb_userid = '<?php echo isLoggedOn(); ?>';
   jsfpb_token = '<?php echo $_SESSION['s_user']['token']; ?>';
   var jsftools_inittoolid = '<?php echo $toolid; ?>';
   
   
   
   var url = '<?php echo getBaseURL().$GLOBALS['codeFolder']; ?>jsoncontroller.php?action=getwdandrows';
   url += '&userid=<?php echo isLoggedOn(); ?>&token=<?php echo $_SESSION['s_user']['token']; ?>';
   url += '&wdname=' + encodeURIComponent('Tools and Widgets');
   url += '&cmsenabled=1';
   url += '&orderby=name';
   url += '&maxcol=10';
   //alert('url: ' + url);
   jsf_json_sendRequest(url,setuppage);

   var tools=[];
   function setuppage(jsondata) {
      if(Boolean(jsondata) && Boolean(jsondata.rows)) {
         //alert(JSON.stringify(jsondata.rows));
         tools = jsondata.rows;
      }
      listTools();
   }
   
   // Return all additional URLs when a user clicks on a tool
   function getURLs(wd_row_id) {
	   var url = '<?php echo getBaseURL().$GLOBALS['codeFolder']; ?>jsoncontroller.php?action=getwdandrows';
	   url += '&userid=<?php echo isLoggedOn(); ?>&token=<?php echo $_SESSION['s_user']['token']; ?>';
	   url += '&wdname=' + encodeURIComponent('Tools and Widgets URLs');
	   url += '&o_wd_id=' + encodeURIComponent('Tools and Widgets');
	   url += '&o_field_id=' + encodeURIComponent('addlurls');
	   url += '&o_wd_row_id=' + wd_row_id;
	   url += '&divid=addlurls_' + wd_row_id;
	   url += '&cmsenabled=1';
	   url += '&maxcol=10';
	   //alert('url: ' + url);
	   jsf_json_sendRequest(url,getURLs_return);
   }
   
   function getURLs_return(jsondata) {
   	if(Boolean(jsondata) && Boolean(jsondata.divid) && Boolean(jsondata.rows) && jsondata.rows.length > 0) {
			var str = '';
			
			str += '<div style=\"margin-left:10px;padding:3px;border-radius:2px;background-color:#F0F0F0;margin-top:10px;margin-bottom:15px;font-size:12px;\">';
			str += 'Additional URLs';
			str += '<table cellpadding=\"2\" cellspacing=\"3\" style=\"\">';
			for(var i=0;i<jsondata.rows.length;i++) {
				str += '<tr valign=\"top\">';
				str += '<td><a href=\"' + jsondata.rows[i].url + '\" target=\"_new\"  style=\"font-size:12px;\">' + jsondata.rows[i].url + '</a></td>';
				str += '<td><div style=\"font-weight:100;\">' + jsondata.rows[i].type + '</div></td>';
				str += '<td>';
				str += '<span style=\"font-weight:bold;color:#111111;\">' + jsondata.rows[i].info + ': </span>';
				str += '<span style=\"font-weight:normal;color:#353535;\">' + jsondata.rows[i].descr + '</span>';
				str += '</td>';
				str += '</tr>';
			}
			str += '</table>';
			str += '</div>';
			
			jQuery('#' + jsondata.divid).html(str);
		}
   }
   
   function listTools(){
      var str = '';
      var str_links = '';
      var init_i = '';
      
      str_links  += '<div id=\"mainmenu\" style=\"padding:20px;\">';
      str_links  += '   <div style=\"font-size:18px;font-family:verdana;margin-bottom:10px;\">Edit tools</div>';
      for(var i =0; i<tools.length; i++) {
         str += '<div id=\"' + tools[i].abbrev + '\" style=\"display:none;\">';
         str += '<div id=\"' + tools[i].abbrev + '_adminpages\" style=\"display:none;\"></div>';
         str += '<div id=\"' + tools[i].abbrev + '_general\" style=\"padding:20px;\">';
         
         str += '<div onclick=\"jQuery(\'#' + tools[i].abbrev + '\').hide();jQuery(\'#mainmenu\').show();jQuery(\'#createnew\').show();\" style=\"cursor:pointer;font-size:10px;font-family:verdana;color:blue;margin-bottom:5px;\">';
         str += '&lt; Back';
         str += '</div>';
         
         str += '<div style=\"float:left;margin-right:12px;\">';
         str += '<img src=\"' + tools[i].img + '\" style=\"max-height:120px;max-width:180px;height:auto;width:auto;border-radius:5px;\">';
         str += '</div>';
         
         str += '<div style=\"float:left;\">';
         str += '<div style=\"font-size:20px;font-family:verdana;color:#232323;\">';
         str += tools[i].name;
         str += '<span onclick=\"window.open(\'<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['codeFolder']; ?>ViewWDataJSON.php?admin=1&wd_id=' + encodeURIComponent('Tools and Widgets') + '&wd_row_id=' + tools[i].wd_row_id + '\');\" style=\"margin-left:20px;color:BLUE;font-size:8px;cursor:pointer;\">[edit]</span>';
         str += '</div>';
         if(Boolean(tools[i].created)) {
            str += '<div style=\"font-size:12px;font-family:verdana;color:#444444;\">';
            str += tools[i].created;
            str += '</div>';
         }
         str += '<div onclick=\"window.open(\'' + tools[i].link + '\');\" style=\"cursor:pointer;font-size:12px;font-family:verdana;color:blue;\">';
         str += tools[i].link;
         str += '</div>';
         str += '<div id=\"addlurls_' + tools[i].wd_row_id + '\"></div>';
         str += '<div style=\"font-size:10px;font-family:verdana;color:#707070;\">';
         str += '#' + tools[i].htag;
         str += '</div>';
         str += '<div style=\"margin-top:1px;\" id=\"' + tools[i].abbrev + '_pages\"></div>';
         str += '</div>';
         
         str += '<div style=\"clear:both;\"></div>';
         
         if(Boolean(tools[i].descr)) str += '<div style=\"margin-top:15px;margin-bottom:4px;font-size:14px;color:#444444;font-family:verdana;\">' + tools[i].descr + '</div>';
         if(Boolean(tools[i].help)) str += '<div style=\"margin-top:2px;margin-bottom:4px;font-size:12px;color:#777777;font-family:verdana;\">' + tools[i].help + '</div>';
         
         str += '<div style=\"float:left;border:1px solid #EFEFEF;border-radius:3px;margin:13px 13px 2px 2px;padding:5px;width:280px;height:180px;overflow-y:auto;overflow-x:hidden;font-size:12px;color:#686b6f;\" id=\"' + tools[i].abbrev + '_csv\"></div>';
         str += '<div style=\"float:left;border:1px solid #EFEFEF;border-radius:3px;margin:13px 13px 2px 2px;padding:5px;width:280px;height:180px;overflow-y:auto;overflow-x:hidden;font-size:12px;color:#686b6f;\" id=\"' + tools[i].abbrev + '_report\"></div>';
         str += '<div style=\"float:left;border:1px solid #EFEFEF;border-radius:3px;margin:13px 13px 2px 2px;padding:5px;width:280px;height:180px;overflow-y:auto;overflow-x:hidden;font-size:12px;color:#686b6f;\" id=\"' + tools[i].abbrev + '_jdata\">Loading...</div>';
         str += '<div style=\"float:left;border:1px solid #EFEFEF;border-radius:3px;margin:13px 13px 2px 2px;padding:5px;width:280px;height:180px;overflow-y:auto;overflow-x:hidden;font-size:12px;color:#686b6f;\" id=\"' + tools[i].abbrev + '_cms\"></div>';         
         str += '<div style=\"float:left;border:1px solid #EFEFEF;border-radius:3px;margin:13px 13px 2px 2px;padding:5px;width:280px;height:180px;overflow-y:auto;overflow-x:hidden;font-size:12px;color:#686b6f;\" id=\"' + tools[i].abbrev + '_dyn\"></div>';         
         str += '<div style=\"clear:both;\"></div>';

         str += '<div style=\"margin:13px 13px 2px 2px;padding:5px;font-size:12px;color:#686b6f;\" id=\"' + tools[i].abbrev + '_users\"></div>';         
         
         str += '</div>';
         
         str += '<div id=\"counters_sect_' + tools[i].abbrev + '\" style=\"position:relative;\">';
         str += '</div>';
         
         str += '</div>';
         
         var str_links_oc = 'searchhtags(' + i + ');jQuery(\'#mainmenu\').hide();jQuery(\'#createnew\').hide();jQuery(\'#' + tools[i].abbrev + '\').show();';
         
         
         
         
         //str_links += '<div style=\"margin-bottom:8px;\">';
         str_links += '<div style=\"position:relative;margin:10px;padding:5px;border:1px solid #C2C2DE;border-radius:4px;width:250px;height:100px;overflow:hidden;float:left;\">';
         str_links += '<div onclick=\"' + str_links_oc + '\" style=\"font-size:14px;font-family:verdana;margin-bottom:1px;color:blue;font-weight:bold;cursor:pointer;\">' + tools[i].name + '</div>';
         str_links += '<div onclick=\"window.open(\'' + tools[i].link + '\');\" style=\"font-size:10px;font-family:verdana;margin-bottom:5px;color:#7777EE;font-weight:normal;cursor:pointer;\">' + tools[i].link + '</div>';
         if(Boolean(tools[i].img)) str_links += '<img onclick=\"' + str_links_oc + '\" src=\"' + tools[i].img + '\" style=\"position:absolute;left:5px;bottom:5px;max-width:200px;max-height:40px;height:auto;width:auto;border-radius:2px;\">';
         str_links += '<div onclick=\"window.open(\'<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['codeFolder']; ?>ViewWDataJSON.php?admin=1&wd_id=' + encodeURIComponent('Tools and Widgets') + '&wd_row_id=' + tools[i].wd_row_id + '\');\" style=\"position:absolute;bottom:5px;right:5px;color:BLUE;font-size:8px;cursor:pointer;\">[edit]</div>';         
         str_links += '</div>';
         
         
         
         if(Boolean(jsftools_inittoolid) && (jsftools_inittoolid==tools[i].abbrev || jsftools_inittoolid==tools[i].htag)) {
            init_i=str_links_oc;
         }
      }
      str_links  += '<div style=\"clear:both;\"></div>';
      str_links  += '</div>';
      
      str += str_links;
      
      str += '<div id=\"createnew\" style=\"margin-top:40px;margin-bottom:20px;\">';
      str += '<div id=\"createnew_expand\" onclick=\"jQuery(\'#jsfwdarea\').show();jQuery(\'#createnew_expand\').hide();jQuery(\'#createnew_collapse\').show();\" style=\"color:blue;cursor:pointer;\">+ Create a new tool template</div>';
      str += '<div id=\"createnew_collapse\" onclick=\"jQuery(\'#jsfwdarea\').hide();jQuery(\'#createnew_expand\').show();jQuery(\'#createnew_collapse\').hide();\" style=\"color:blue;cursor:pointer;display:none;\">- Create a new tool template</div>';
      str += '<div id=\"jsfwdarea\" style=\"display:none;\"></div>';
      str += '</div>';
      if(Boolean(init_i)) {
         str += '\n<scr';
         str += 'ipt>\n' + init_i + '\n</scr';
         str += 'ipt>\n';
      }
      
      jQuery('#pmtools').html(str);
      jsf_getwebdatapage_jsonp('Tools and Widgets','<?php echo getBaseURL(); ?>');
   }

   function showtoolpages(toolid,tablename) {
      var tools_wd = jQuery('#container').width() - 10;
      var tools_ht = jQuery('#container').height() - 10;
      jsfpb_admindivid = toolid + '_adminpages';
      jsfpb_domain = '<?php echo getBaseURL(); ?>';
      jsfpb_tablename = tablename;
      jsfpb_backclick = 'jQuery(\'#' + jsfpb_admindivid + '\').hide();jQuery(\'#' + toolid + '_general\').show();';
      jsfpb_showTool('',tools_wd,tools_ht);
      jQuery('#' + toolid + '_general').hide();
      jQuery('#' + jsfpb_admindivid).show();
      //alert('here');
   }
   
   
   var pmtools_toolid;
   var pmtools_htag;
   var pmtools_userjsonurl;
   function searchhtags(i,searchtxt,orderby){
      var toolid = tools[i].abbrev;
      var wd_row_id = tools[i].wd_row_id;
      var htags = tools[i].htag;
      
      getURLs(wd_row_id);
      
      pmtools_toolid = toolid;
      pmtools_htag = htags;
      if(htags.startsWith('#')) pmtools_htag = htags.substring(1);
      
      var url = '<?php echo getBaseURL().$GLOBALS['codeFolder']; ?>jsoncontroller.php?action=jsfhashtag&tb=webdata&col=htags&prk=wd_id&htaction=search';
      url += '&userid=<?php echo isLoggedOn(); ?>&token=<?php echo $_SESSION['s_user']['token']; ?>';
      url += '&searchcols=' + encodeURIComponent('wd_id,name,htags,info');
      if(Boolean(htags)) url += '&searchht=' + encodeURIComponent(htags);
      if(Boolean(searchtxt)) url += '&searchtxt=' + encodeURIComponent(searchtxt);
      if(Boolean(orderby)) url += '&orderby=' + encodeURIComponent(orderby);
      //alert('url: ' + url);
      jQuery('#' + pmtools_toolid + '_jdata').hide();
      jsf_json_sendRequest(url,setuphtags);
      
      // while getting jdata, also get content
      searchhtagsver(toolid,htags,searchtxt,orderby);
      
      searchcsvrequests(toolid,wd_row_id);
      searchreportrequests(toolid,wd_row_id);
      getdynamicreports();
      
      if(Boolean(tools[i].showusersearch)) {
        pmtools_userjsonurl = tools[i].searchuserform;
        var url = '<?php echo getBaseURL().$GLOBALS['codeFolder']; ?>jsoncontroller.php?action=getwdandrows';
        url += '&userid=<?php echo isLoggedOn(); ?>&token=<?php echo $_SESSION['s_user']['token']; ?>';
        url += '&wd_id=' + encodeURIComponent('Tools and Widgets User Search');
        url += '&o_wd_id=' + encodeURIComponent('Tools and Widgets');
        url += '&o_field_id=usersearchfields';
        url += '&o_wd_row_id=' + wd_row_id;
        url += '&cmsenabled=1';
        url += '&maxcol=10';
        //alert('url: ' + url);
        jsf_json_sendRequest(url,setupusersearchform);
      }
   }
  
  var jsftools_usersearchinput;
  var jsftools_dynajson;
  var jsftools_dynajson_rev;
  function setupusersearchform(jsondata) {
         var jsoncalls = [];
         var str = '';
         str += '<div style=\"margin-top:10px;padding-top:10px;border-top:1px solid #F0F0F0;color:#333333;font-size:12px;font-family:verdana;\">';
         str += '<div>';
         str += '<div style=\"margin-bottom:5px;font-size:16px;color:#000000;\">Search Users of this tool</div>';
         
         var js = '';
         js += 'function searchusers_' + pmtools_toolid + '(sendemail){\n';
         js += '  var val;\n';
         js += '  var e;\n';
         js += '  var msg;\n';
         js += '  var url = \'<?php echo getBaseURL(); ?>' + pmtools_userjsonurl + '\';\n';
         js += '  url += \'&userid=<?php echo isLoggedOn(); ?>\';\n';
         js += '  url += \'&token=<?php echo $_SESSION['s_user']['token']; ?>\';\n';
         js += '  url += \'&divid=' + pmtools_toolid + '_userformresults\';\n';
    
         //build dependency lists
         jsftools_usersearchinput = {};
         jsftools_dynajson = {};
         jsftools_dynajson_rev = {};
         for(var j=0;j<jsondata.rows.length;j++) {
           var tempobjdivid = pmtools_toolid + '_' + j;
           jsftools_usersearchinput[tempobjdivid] = jsondata.rows[j];
           if(Boolean(jsondata.rows[j].dependencies)) {
             var deps = jsondata.rows[j].dependencies.split(';');
             for(var k=0;k<deps.length;k++){
               var dinfo = deps[k].split(',');
               if(!Boolean(jsftools_dynajson[tempobjdivid])) jsftools_dynajson[tempobjdivid] = [];
               var tempobj = {};
               tempobj.relto = dinfo[0];
               tempobj.param = dinfo[1];
               
               for(var m=0;m<jsondata.rows.length;m++) {
                 if(dinfo[0]==jsondata.rows[m].param) {
                   var tempobjdivid2 = pmtools_toolid + '_' + m;
                   tempobj.divid = tempobjdivid2;
                   tempobj.json = jsondata.rows[m].json;
                   if(!Boolean(jsftools_dynajson_rev[tempobjdivid2])) jsftools_dynajson_rev[tempobjdivid2] = [];
                   
                   var tempobj2 = {};
                   tempobj2.divid = tempobjdivid;
                   tempobj2.param = dinfo[1];
                   jsftools_dynajson_rev[tempobjdivid2].push(tempobj2);
                   break;
                 }
               }
               
               jsftools_dynajson[tempobjdivid].push(tempobj);               
             }
           }
         }

         
         //create form input elements on page
         for(var j=0;j<jsondata.rows.length;j++) {
            if(Boolean(jsondata.rows[j].name)) {
               str += '<div style=\"margin-top:8px;\">';
               str += '<div style=\"float:left;width:150px;\">' + jsondata.rows[j].name + '</div>';
               str += '<div id=\"userformdiv_' + pmtools_toolid + '_' + j + '\" style=\"float:left;\">';
               if(jsondata.rows[j].type=='text') {
                  str += '<input type=\"text\" id=\"userform_' + pmtools_toolid + '_' + j + '\" style=\"\">';
               } else if(jsondata.rows[j].type=='date') {
                  str += '<input type=\"text\" id=\"userform_' + pmtools_toolid + '_' + j + '\" style=\"\">';
               } else if(jsondata.rows[j].type=='bool') {
                  str += '<select id=\"userform_' + pmtools_toolid + '_' + j + '\" style=\"\">';
                  str += '<option value=\"\"></option>';
                  str += '<option value=\"YES\">Yes</option>';
                  str += '<option value=\"NO\">No</option>';
                  str += '</select>';
               } else if(jsondata.rows[j].type=='json') {
                  //This is NOT a jsonp... just jsoncontroller.php
                  var tempurl = jsondata.rows[j].json;
                  tempurl += '&divid=' + pmtools_toolid + '_' + j;
                  jsoncalls.push(tempurl);
               }
               str += '</div>';
               str += '<div style=\"clear:both;\"></div>';
               str += '</div>';
               js += 'val = jQuery(\'#userform_' + pmtools_toolid + '_' + j + '\').val();\n';
               if(Boolean(jsondata.rows[j].required) && jsondata.rows[j].required.toLowerCase()=='yes') {
                  js += 'if(!Boolean(val)) {\n';
                  js += '  e=true;\n';
                  js += '  msg = \'Please enter a value for ' + jsondata.rows[j].name + '\';\n';
                  js += '}\n';
               }
               js += 'if(Boolean(val)) url += \'&' + jsondata.rows[j].param + '=\' + encodeURIComponent(val);\n';
            }
         }
         js += 'if(Boolean(sendemail)) {\n';
         js += '  var cmsid = jQuery(\'#userform_' + pmtools_toolid + '_cmsid\').val();\n';
         js += '  if(Boolean(cmsid)) {\n';
         js += '    url += \'&sendemail=1\';\n';
         js += '    url += \'&cmsid=\' + cmsid;\n';
         js += '  } else {\n';
         js += '    alert(\'Please select content and set the from email address before sending email.\');\n';
         js += '  }\n';
         js += '}\n';
         //js += 'alert(\'url: \' + url);\n';
         js += 'if(Boolean(e)) {\n';
         js += '  alert(msg);\n';
         js += '} else {\n';
         js += '  jQuery(\'#' + pmtools_toolid + '_userformresults\').html(\'Loading...\');\n';
         //js += '  alert(\'url: \' + url);\n';
         js += '  jsf_json_sendRequest(url,jsftools_searchusers);\n';
         js += '}\n';
         js += '}\n';
         
         str += '<div style=\"margin-top:8px;\">';
         str += '<div style=\"float:left;width:150px;\">Email</div>';
         str += '<div id=\"userformdiv_' + pmtools_toolid + '_cmsid\" style=\"float:left;\">';
         str += '<select id=\"userform_' + pmtools_toolid + '_cmsid\" style=\"\">';
         str += '<option value=\"\"></option>';
         <?php
            $ss = new Version();
            $shortcuts = $ss->getAllShortcuts(6);
            for ($i=0; $i<count($shortcuts); $i++) {
         ?>
            str += '<option value=\"<?php echo $shortcuts[$i]['cmsid']; ?>\">';
            str += '<?php echo $shortcuts[$i]['title']." (".$shortcuts[$i]['filename'].")"; ?>';
            str += '</option>';
         <?php
            }
         ?>
         str += '</select>';
         str += '</div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
         
         str += '<div style=\"margin-top:4px;margin-bottom:2px;\">';
         str += '<div onclick=\"searchusers_' + pmtools_toolid + '();\" style=\"float:left;cursor:pointer;border:1px solid #777777;background-color:#F1F1F1;border-radius:4px;padding:4px;width:100px;text-align:center;margin:8px;\">Search</div>';
         str += '<div onclick=\"searchusers_' + pmtools_toolid + '(1);\" style=\"float:left;cursor:pointer;border:1px solid #777777;background-color:#F1F1F1;border-radius:4px;padding:4px;width:100px;text-align:center;margin:8px;\">Send Email</div>';
         str += '<div style=\"clear:both;\"></div>';
         str += '</div>';
         str += '</div>';
         str += '<div id=\"' + pmtools_toolid + '_userformresults\"></div>';
         str += '</div>';
         str += '\n<script>\n' + js + '\n<';
         str += '/s';
         str += 'cript>\n';
         //alert('str: ' + str);
         
         jQuery('#' + pmtools_toolid + '_users').html(str);
         jQuery('#' + pmtools_toolid + '_users').show();
         
         while(jsoncalls.length>0) {
            var query = jsoncalls.shift();
            //alert('calling: ' + query);
            jsf_json_sendRequest('<?php echo getBaseURL(); ?>' + query,jsftools_userformdropdown);         
         }

    
  }
   
   function jsftools_searchusers(jsondata){
      var str = '';
      if(Boolean(jsondata) && Boolean(jsondata.users) && jsondata.users.length>0) {
         var uids = '';
         for(var i=0;i<jsondata.users.length;i++) {
            uids += jsondata.users[i].userid + ',';
         }
         var url = '';
         url += '<?php echo getBaseURL().$GLOBALS['adminFolder']; ?>admincontroller.php';
         url += '?action=listuserscloning';
         url += '&s_filter=' + encodeURIComponent(uids);
         
         if(Boolean(jsondata.emailsscheduled)){
            str += '*Emails were sent successfully.<br>';
         }
         str += '<a href=\"' + url + '\" target=\"_new\">';
         str += 'View ' + jsondata.users.length + ' user results.';
         str += '</a>';
      } else {
         str += 'No results were found from your search.';
      }
      jQuery('#' + jsondata.divid).html(str);
   }
   
   function jsftools_userformdropdown(jsondata){
      var str = '';
      str += '<select id=\"userform_' + jsondata.divid + '\" ';
      if(Boolean(jsftools_dynajson[jsondata.divid])) {
         str += 'onchange=\"';
         
         for (var n=0;n<jsftools_dynajson[jsondata.divid].length;n++) {
           var tempurl = jsftools_dynajson[jsondata.divid][n].json;
           tempurl += '&divid=' + jsftools_dynajson[jsondata.divid][n].divid;
           for(var o=0;o<jsftools_dynajson_rev[jsftools_dynajson[jsondata.divid][n].divid].length;o++) {
             tempurl += '&' + jsftools_dynajson_rev[jsftools_dynajson[jsondata.divid][n].divid][o].param + '=';
             //tempurl += '=' + encodeURIComponent(jQuery('#userform_' + jsftools_dynajson_rev[jsftools_dynajson[jsondata.divid][n].divid][o].divid).val());
             //alert('div: #userform_' + jsftools_dynajson_rev[jsftools_dynajson[jsondata.divid][n].divid][o].divid);
             tempurl += '\' + encodeURIComponent(jQuery(\'#userform_' + jsondata.divid + '\').val())';
           }
           str += 'jsf_json_sendRequest(\'<?php echo getBaseURL(); ?>' + tempurl;
           str += ',jsftools_userformdropdown);';
         }
         
         str += '\" ';
      }
      str += '>';
      str += '<option value=\"\"></option>';
      for(var i=0;i<jsondata.rows.length;i++) {
         var val = jsondata.rows[i].wd_row_id;
         str += '<option value=\"' + val + '\">';
         if(jsondata.rows[i].name.length>40) str += jsondata.rows[i].name.substring(0,40);
         else str += jsondata.rows[i].name;
         str += '</option>';
      }
      str += '</select>';
      jQuery('#userformdiv_' + jsondata.divid).html(str);
   }
   
   function setuphtags(jsondata){
      //alert('jsondata: ' + JSON.stringify(jsondata));
      var str = '';
      var pagesstr = '';
      if(Boolean(jsondata.results) && jsondata.results.length>0){
         str += '<div style=\"font-weight:bold;cursor:pointer;\" onclick=\"window.open(\'<?php echo getBaseURL().$GLOBALS['adminFolder']; ?>admincontroller.php?action=wd_listwebdata&htags=display\');\">JData</div>';
         //alert('found jsondata: ' + JSON.stringify(jsondata));
         //alert('divid: ' + pmtools_divid + ' length: ' + jsondata.results.length);
         for(var i=0;i<jsondata.results.length;i++){
            // Check if this is a pages possibility
            var lastSix = jsondata.results[i].name.substr(jsondata.results[i].name.length - 6);
            var lastNine = jsondata.results[i].name.substr(jsondata.results[i].name.length - 9);
            //alert('last six: ' + lastSix);
            if(lastNine==' QA Pages') {
               pagesstr += '<div onclick=\"showtoolpages(\'' + pmtools_toolid + '\',\'' + jsondata.results[i].name + '\');\" style=\"cursor:pointer;font-size:12px;font-family:verdana;color:#772222;margin-top:1px;\">';
               pagesstr += 'QA Page Builder (test pages)';
               pagesstr += '</div>';
            } else if(lastSix==' Pages') {
               pagesstr += '<div onclick=\"showtoolpages(\'' + pmtools_toolid + '\',\'' + jsondata.results[i].name + '\');\" style=\"cursor:pointer;font-size:12px;font-family:verdana;color:#772222;margin-top:1px;\">';
               pagesstr += 'Page Builder (new & existing pages)';
               pagesstr += '</div>';
            } else {
               str += '<div onclick=\"window.open(\'<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=wd_listrows&wd_id=' + jsondata.results[i].wd_id + '&pageLimit=200\');\" style=\"cursor:pointer;font-size:12px;font-family:verdana;color:#772222;margin-top:4px;height:15px;overflow:hidden;\">';
               str += jsondata.results[i].name + ' (' + jsondata.results[i].info + ')';
               str += '</div>';
               if(lastSix=='Values') {
                  pagesstr += '<div onclick=\"showtoolpages(\'' + pmtools_toolid + '\',\'' + jsondata.results[i].name + '\');\" style=\"cursor:pointer;font-size:12px;font-family:verdana;color:#772222;margin-top:1px;\">';
                  pagesstr += 'Page Builder (new & existing pages)';
                  pagesstr += '</div>';
               }
            }
         }
         //alert('str: ' + str);
         jQuery('#' + pmtools_toolid + '_jdata').show();
      }
      
      jQuery('#' + pmtools_toolid + '_jdata').html(str);
      jQuery('#' + pmtools_toolid + '_pages').html(pagesstr);
   }
   
   
   function getdynamicreports() {
      jQuery('#' + pmtools_toolid + '_dyn').html('');
      jQuery('#' + pmtools_toolid + '_dyn').hide();
      var url = '<?php echo getBaseURL().$GLOBALS['codeFolder']; ?>jsoncontroller.php?action=getwdandrows';
      url += '&userid=<?php echo isLoggedOn(); ?>&token=<?php echo $_SESSION['s_user']['token']; ?>';
      url += '&wd_id=' + encodeURIComponent('Tools and Widgets Dynamic Reports');
      url += '&cmsenabled=1';
      url += '&cmsz_toolsandwidgetsdynamicreports_hashtags=' + encodeURIComponent(pmtools_htag);
      //alert('url: ' + url);
      jsf_json_sendRequest(url,returndynamicreports);
   }
   
   var jsftools_ezreports;
   function returndynamicreports(jsondata) {
      //alert('dynamic reports: ' + JSON.stringify(jsondata));
      jQuery('#counters_sect_' + pmtools_toolid).html('');
      var str = '';
      jsftools_ezreports = [];
      if(Boolean(jsondata.rows) && jsondata.rows.length>0){
         str += '<div style=\"font-weight:bold;cursor:pointer;\" onclick=\"window.open(\'<?php echo getBaseURL().$GLOBALS['adminFolder']; ?>admincontroller.php?action=scheduledcsvs&type=CUSTOM\');\">Reports</div>';
         for(var i=0;i<jsondata.rows.length;i++){
            if(Boolean(jsondata.rows[i].countonly) && jsondata.rows[i].countonly.toLowerCase()=='yes') {
               jsftools_ezreports.push(jsondata.rows[i].wd_row_id);
               var html = '';
               html += '<div style=\"padding:10px;margin:5px;width:200px;float:left;border:2px solid #555555;border-radius:4px;\">';
               html += '<div style=\"margin-bottom:3px;font-size:14px;font-weight:bold;text-align:center;color:#434343;\">';
               html += jsondata.rows[i].name;
               html += '</div>';
               html += '<div style=\"margin-bottom:5px;font-size:10px;font-weight:bold;text-align:center;color:#787878;\">';
               html += jsondata.rows[i].description;
               html += '</div>';
               html += '<div id=\"counterreport_' + pmtools_toolid + '_' + jsondata.rows[i].wd_row_id + '\" ';
               html += 'style=\"font-size:28px;font-weight:bold;margin-bottom:2px;color:#111111;text-align:center;\">';
               html += '</div>';
               html += '<div onclick=\"window.open(\'<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['codeFolder']; ?>ViewWDataJSON.php?admin=1&wd_id=' + jsondata.wd_id + '&wd_row_id=' + jsondata.rows[i].wd_row_id + '\');event.stopPropagation();\" style=\"text-align:right;font-size:8px;color:blue;cursor:pointer;\">';
               html += 'edit';
               html += '</div>'; 
               html += '</div>';
               jQuery('#counters_sect_' + pmtools_toolid).append(html);
            } else {
               var id = pmtools_vtoolid + 'dynrep_' + jsondata.rows[i].wd_row_id;
               str += '<div id=\"' + id + '\" onclick=\"location.href=\'admincontroller.php?action=jsfreports&reportid=' + jsondata.rows[i].wd_row_id + '&toolid=' + pmtools_toolid + '&htag=' + pmtools_htag + '\';\" style=\"margin-top:3px;margin-bottom:5px;cursor:pointer;\">';
               str += '<div style=\"font-size:14px;font-weight:bold;color:#222222;\">';
               str += jsondata.rows[i].name;
               str += '<span onclick=\"window.open(\'<?php echo getBaseURL().$GLOBALS['codeFolder']; ?>ViewWDataJSON.php?admin=1&wd_id=' + jsondata.wd_id + '&wd_row_id=' + jsondata.rows[i].wd_row_id + '\');event.stopPropagation();\" style=\"margin-left:15px;font-size:8px;color:blue;cursor:pointer;\">';
               str += 'edit';
               str += '</span>';
               str += '</div>';
               str += '<div style=\"font-size:12px;font-weight:normal;color:#787878;\">';
               str += jsondata.rows[i].description;
               str += '</div>';
               str += '</div>';
            }
         }
         str += '<div onclick=\"window.open(\'<?php echo getBaseURL().$GLOBALS['codeFolder']; ?>ViewWDataJSON.php?admin=1&wd_id=' + jsondata.wd_id + '\');\" style=\"margin-top:5px;margin-bottom:5px;cursor:pointer;\">';
         str += '<div style=\"font-size:12px;font-weight:normal;color:#4444;\">';
         str += 'Add a new report';
         str += '</div>';
         str += '</div>';
         str += '<div onclick=\"window.open(\'/<?php echo getBaseURL().$GLOBALS['adminFolder']; ?>admincontroller.php?action=wd_listrows&pageLimit=25&wd_id=' + jsondata.wd_id + '\');\" style=\"margin-top:5px;margin-bottom:5px;cursor:pointer;\">';
         str += '<div style=\"font-size:12px;font-weight:normal;color:#4444;\">';
         str += 'List All Reports (all tools)';
         str += '</div>';
         str += '</div>';
         jQuery('#' + pmtools_toolid + '_dyn').show();
      }
      jQuery('#' + pmtools_toolid + '_dyn').html(str);
      
      processezreports();
   }
   
   function processezreports() {
      if(Boolean(jsftools_ezreports) && jsftools_ezreports.length>0){
         var reportid = jsftools_ezreports.shift();
         var url = '<?php echo getBaseURL().$GLOBALS['codeFolder']; ?>jsoncontroller.php?action=getdynamicreportdata';
         url += '&userid=<?php echo isLoggedOn(); ?>&token=<?php echo $_SESSION['s_user']['token']; ?>';
         url += '&reportid=' + reportid;
         url += '&divid=counterreport_' + pmtools_toolid + '_' + reportid;
         jsf_json_sendRequest(url,processezreports_return);
      } else {
         jQuery('#counters_sect_' + pmtools_toolid).append('<div style=\"clear:both;\"></div>');
      }
   }
   
   function processezreports_return(jsondata) {
      //alert(JSON.stringify(jsondata));
      var results = [];
      if(Boolean(jsondata) && Boolean(jsondata.results) && jsondata.results.length>0) {
         results = jsondata.results;
      } else if(Boolean(jsondata) && Boolean(jsondata.rows) && jsondata.rows.length>0) {
         results = jsondata.rows;
      } else if(Boolean(jsondata) && Boolean(jsondata.users) && jsondata.users.length>0) {
         results = jsondata.users;
      } else if(Boolean(jsondata) && Boolean(jsondata.records) && jsondata.records.length>0) {
         results = jsondata.records;
      }
      
      jQuery('#' + jsondata.divid).html(results[0]['count(*)']);
      
      processezreports();
   }
   
   function searchcsvrequests(toolid,wd_row_id) {
      pmtools_vtoolid = toolid;
      var url = '<?php echo getBaseURL().$GLOBALS['codeFolder']; ?>jsoncontroller.php?action=getwdandrows';
      url += '&userid=<?php echo isLoggedOn(); ?>&token=<?php echo $_SESSION['s_user']['token']; ?>';
      url += '&wd_id=' + encodeURIComponent('Tools and Widgets CSV Requests');
      url += '&o_wd_id=' + encodeURIComponent('Tools and Widgets');
      url += '&o_field_id=csvrequests';
      url += '&o_wd_row_id=' + wd_row_id;
      url += '&cmsenabled=1';
      url += '&maxcol=10';
      //alert('url: ' + url);
      jsf_json_sendRequest(url,setupcsvrequests);
   }
   
   function searchreportrequests(toolid,wd_row_id) {
      jQuery('#' + pmtools_vtoolid + '_report').hide();
      pmtools_vtoolid = toolid;
      var url = '<?php echo getBaseURL().$GLOBALS['codeFolder']; ?>jsoncontroller.php?action=getwdandrows';
      url += '&userid=<?php echo isLoggedOn(); ?>&token=<?php echo $_SESSION['s_user']['token']; ?>';
      url += '&wd_id=' + encodeURIComponent('Tools and Widgets Reports');
      url += '&o_wd_id=' + encodeURIComponent('Tools and Widgets');
      url += '&o_field_id=reports';
      url += '&o_wd_row_id=' + wd_row_id;
      url += '&cmsenabled=1';
      url += '&maxcol=10';
      //alert('url: ' + url);
      jsf_json_sendRequest(url,setupreportrequests);
   }
   
   function setupcsvrequests(jsondata){
      var str = '';
      if(Boolean(jsondata.rows) && jsondata.rows.length>0){
         str += '<div style=\"font-weight:bold;cursor:pointer;\" onclick=\"window.open(\'/<?php echo getBaseURL().$GLOBALS['adminFolder']; ?>admincontroller.php?action=scheduledcsvs&type=CUSTOM\');\">CSV downloads</div>';
         for(var i=0;i<jsondata.rows.length;i++){
            var id = pmtools_vtoolid + 'csvreq_' + jsondata.rows[i].wd_row_id;
            str += '<div id=\"' + id + '\">';
            str += '<div ';
            str += 'style=\"cursor:pointer;\" ';
            str += 'onclick=\"jsft_requestcsv(\'' + jsondata.rows[i].name + '\',';
            str += '\'' + jsondata.rows[i].jsonurl + '\',\'';
            if(Boolean(jsondata.rows[i].results_var)) str += jsondata.rows[i].results_var;
            str += '\',\'';
            if(Boolean(jsondata.rows[i].limit_var)) str += jsondata.rows[i].limit_var;
            str += '\',\'';
            if(Boolean(jsondata.rows[i].pgnum_var)) str += jsondata.rows[i].pgnum_var;
            str += '\',\'' + id + '\');\" ';
            str += '>';
            str += '&bull; Request ' + jsondata.rows[i].name + ' CSV';
            str += '</div>';
            str += '</div>';
         }
         jQuery('#' + pmtools_vtoolid + '_csv').show();
      } else {
         jQuery('#' + pmtools_vtoolid + '_csv').hide();
      }
      jQuery('#' + pmtools_vtoolid + '_csv').html(str);
   }
   
   function pmtools_replaceAll(find, replace, str) {
     return str.replace(new RegExp(find, 'g'), replace);
   }
   
   
   function setupreportrequests(jsondata){
      var str = '';
      if(Boolean(jsondata.rows) && jsondata.rows.length>0){
         str += '<div style=\"font-weight:bold;\">Reports</div>';
         for(var i=0;i<jsondata.rows.length;i++){
            str += '<div ';
            str += 'onclick=\"location.href=\'<?php echo getBaseURL().$GLOBALS['adminFolder']; ?>admincontroller.php?action=jsfstats&sql=' + pmtools_replaceAll('\'','\\\'',encodeURIComponent(jsondata.rows[i].sql)) + '&name=' + encodeURIComponent(jsondata.rows[i].name) + '&datefield=' + encodeURIComponent(jsondata.rows[i].datefield) + '&backurl=' + encodeURIComponent('<?php echo getBaseURL().$GLOBALS['adminFolder']; ?>admincontroller.php?action=jsftools') + '\';\" ';
            str += 'style=\"cursor:pointer;color:blue;margin:5px;\">';
            str += '&bull; ' + jsondata.rows[i].name;
            str += '</div>';
         }
         jQuery('#' + pmtools_vtoolid + '_report').html(str);
         jQuery('#' + pmtools_vtoolid + '_report').show();
      }
   }
   
   function searchhtagsver(toolid,htags,searchtxt,orderby){
      jQuery('#' + pmtools_toolid + '_cms').hide();
      pmtools_vtoolid = toolid;
      
      pmtools_htag = htags;
      if(htags.startsWith('#')) pmtools_htag = htags.substring(1);
      
      var url = '<?php echo getBaseURL().$GLOBALS['codeFolder']; ?>jsoncontroller.php?action=jsfhashtag&tb=cmsfiles&col=htags&prk=cmsid&htaction=search';
      url += '&userid=<?php echo isLoggedOn(); ?>&token=<?php echo $_SESSION['s_user']['token']; ?>';
      url += '&searchcols=' + encodeURIComponent('cmsid,filename,htags,title');
      if(Boolean(htags)) url += '&searchht=' + encodeURIComponent(htags);
      if(Boolean(searchtxt)) url += '&searchtxt=' + encodeURIComponent(searchtxt);
      if(Boolean(orderby)) url += '&orderby=' + encodeURIComponent(orderby);
      //alert('url: ' + url);
      jsf_json_sendRequest(url,setuphtagsver);
   }
   
   function setuphtagsver(jsondata){
      //alert('jsondata: ' + JSON.stringify(jsondata));
      var str = '';
      if(Boolean(jsondata.results) && jsondata.results.length>0){
         str += '<div style=\"font-weight:bold;cursor:pointer;\" onclick=\"window.open(\'<?php echo getBaseURL().$GLOBALS['adminFolder']; ?>admincontroller.php?action=managefiles\');\">Content</div>';
         for(var i=0;i<jsondata.results.length;i++){
            str += '<div onclick=\"location.href=\'<?php echo getBaseURL().$GLOBALS['adminFolder']; ?>admincontroller.php?action=managefiles&cmsid=' + jsondata.results[i].cmsid + '&htagfilter=,' + pmtools_htag + '\';\" style=\"cursor:pointer;font-size:12px;font-family:verdana;color:#772222;margin-top:4px;height:15px;overflow:hidden;\">';
            str += jsondata.results[i].filename + ' (' + jsondata.results[i].title + ')';
            str += '</div>';
         }
         jQuery('#' + pmtools_toolid + '_cms').show();
      }
      jQuery('#' + pmtools_toolid + '_cms').html(str);
   }

   
   function jsfwdpagedcallback_end(jsondata){
      //var url = 'pmtools.php?i=1';
      var url = 'admincontroller.php?action=jsftools';
      url += '&newtool=1';
      url += '&email=' + encodeURIComponent(jsondata.row.email);
      url += '&htag=' + encodeURIComponent(jsondata.row.htag);
      url += '&casestudy=' + encodeURIComponent(jsondata.row.casestudy);
      //alert('successful.  Refreshing the pmtools page: ' + url);
      location.href = url;
   }
   
   
   
   
   function jsft_requestcsv(subj,jsft_csv_url,results_var,limit_var,pgnum_var,divid){
      if(!Boolean(divid)) divid = 'body';
      if(Boolean(jsft_csv_url)){
         jQuery('#' + divid).html('loading...');
         if(!Boolean(subj) || subj=='CSV Subject') subj = 'Back end company search csv';
         if(!Boolean(results_var)) results_var = 'rows';
         
         var uri = '<?php echo getBaseURL().$GLOBALS['codeFolder']; ?>jsoncontroller.php?action=requestjsoncsv';
         if(Boolean(limit_var)) uri += '&limit=' + limit_var;
         if(Boolean(pgnum_var)) uri += '&page=' + pgnum_var;
         uri += '&results=' + results_var;
         uri += '&subj=' + encodeURIComponent(subj);
         uri += '&json=' + encodeURIComponent(jsft_csv_url);
         uri += '&divid=' + encodeURIComponent(divid);
         uri += '&userid=<?php echo isLoggedOn(); ?>&token=<?php echo $_SESSION['s_user']['token']; ?>';
         jsf_json_sendRequest(uri,jsft_requestcsv_return);
      } else {
         alert('sorry, could not request a csv at this time');
      }
   }
   
   function jsft_requestcsv_return(jsondata) {
      var str = '';
      str += '<div style=\"font-size:10px;color:#88BB88;\">';
      str += '<div>CSV Success!</div>';
      str += '<div>JSON URL: ' + jsondata.json + '</div>';
      str += '<div>Subject: ' + jsondata.subj + '</div>';
      str += '</div>';
      
      jQuery('#' + jsondata.divid).html(str);
   }
   
</script>
