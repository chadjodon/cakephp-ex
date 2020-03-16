<?php
include_once "Classes.php";
error_reporting(E_ALL);

$sql = file_get_contents("init_jsf.sql");

$sql = str_replace("\\","",$sql);
$sql = str_replace("\'","'",$sql);
$csvquery = NULL;
if ($sql!=NULL) {
   $dbLink = new MYSQLaccess;
   
   $queryArr = array();
   if (!is_array($sql)) {
      $queryArr = separateStringBy($sql,";");
   } else {
      $queryArr = $sql;
   }

   for ($i=0;$i<count($queryArr);$i++) {
      $query = trim(str_replace("\r"," ",str_replace("\n"," ",str_replace("\r\n"," ",$queryArr[$i]))));
      //print "<br>".$i.". ".$query."<br>";
      if ($query!=NULL) {
         if($csvquery==NULL) $csvquery = $query;
         $things = $dbLink->queryGetResults($query);
         print "<br>\n<div style=\"font-size:10px;font-size:10px;color:#888888;font-weight:bold;\">Query: ".$query."</div>";
         if (count($things)<1) {
            print "Your query executed, but there were no responses.<br>";
         } else {
            $keys = array_keys($things[0]);
            print "<table cellpadding=\"3\" cellspacing=\"1\" bgcolor=\"#222222\">\n";
            print "<tr><td bgcolor=\"#FFFFFF\">#</td>";
            for ($j=0; $j<count($keys); $j++) print "<td bgcolor=\"#FFFFFF\">".$keys[$j]."</td>";
            print "</tr>\n";
   
            for ($j=0; $j<count($things); $j++) {
               print "<tr><td bgcolor=\"#FFFFFF\">".$j."</td>";
               for ($k=0; $k<count($keys); $k++) print "<td bgcolor=\"#FFFFFF\">".convertBack($things[$j][$keys[$k]])."</td>";
               print "</tr>\n";
            }
            print "</table><br><br>\n";
         }
      }
   }
} else {
   print "<div style=\"padding:15px;font-size:10px;font-family:arial;color:#AAAAAA;\">No DB query entered.  Please enter a valid SQL query above.</div>";
}



$wd = new WebsiteData();
$wd_id = $wd->newWebData("API Security","",3,"chadjodon@hotmail.com","",1,0,NULL,"NEW",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL);
$wd->updateWebDataProperty($wd_id, "htags", "#system ");
$ns = $wd->addSection($wd_id);
$wd->addField($wd_id, $ns, NULL,"Enabled","No,Yes","DROPDOWN",10,0,1,"Yes",NULL,NULL, FALSE,"",0,NULL,"enabled");               
$wd->addField($wd_id, $ns, NULL,"Sequence","","INT",20,0,1,"Yes",NULL,NULL, FALSE,"",0,NULL,"sequence");               
$wd->addField($wd_id, $ns, NULL,"Notes","","TEXTAREA",30,0,1,"",NULL,NULL, FALSE,"",0,NULL,"notes");               
$wd->addField($wd_id, $ns, NULL,"Allow all domains?","No,Yes","DROPDOWN",40,0,1,"No",NULL,NULL, FALSE,"",0,NULL,"alldomains");               
$wd->addField($wd_id, $ns, NULL,"Domains","","TEXTAREA",50,0,1,"",NULL,NULL, FALSE,"",0,NULL,"domains");               
$wd->addField($wd_id, $ns, NULL,"Allow all actions?","No,Yes","DROPDOWN",60,0,1,"No",NULL,NULL, FALSE,"",0,NULL,"allactions");               
$wd->addField($wd_id, $ns, NULL,"Actions","","TEXTAREA",70,0,1,"",NULL,NULL, FALSE,"",0,NULL,"actions");               
$wd->addField($wd_id, $ns, NULL,"Allow access without a token?","No,Yes","DROPDOWN",80,0,1,"No",NULL,NULL, FALSE,"",0,NULL,"notokenrqd");               
$wd->addField($wd_id, $ns, NULL,"Domain Token","","TEXT",90,0,1,"",NULL,NULL, FALSE,"",0,NULL,"domaintoken");               
$wd->addField($wd_id, $ns, NULL,"Track this request?","No,Yes","DROPDOWN",100,0,1,"No",NULL,NULL, FALSE,"",0,NULL,"track");
$wd_row_id = $wd->addRow($wd_id);
$wd->updateFieldValue($wd_id,$wd_row_id,"sequence","10");
$wd->updateFieldValue($wd_id,$wd_row_id,"enabled","Yes");
$wd->updateFieldValue($wd_id,$wd_row_id,"notes","Default - catch all");
$wd->updateFieldValue($wd_id,$wd_row_id,"alldomains","Yes");
$wd->updateFieldValue($wd_id,$wd_row_id,"allactions","Yes");
$wd->updateFieldValue($wd_id,$wd_row_id,"notokenrqd","Yes");
$wd->updateFieldValue($wd_id,$wd_row_id,"track","No");


$wd_id = $wd->newWebData("Admin Menu","",3,"chadjodon@hotmail.com","",1,0);
$wd->updateWebDataProperty($wd_id, "htags", "#system #menu #admintool ");
$sid = $wd->addSection($wd_id);
$wd->addField($wd_id,$sid,NULL,"Sequence",NULL,"INT",10,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,NULL);
$wd->addField($wd_id,$sid,NULL,"Enabled","Yes,No","DROPDOWN",20,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,NULL);
$wd->addField($wd_id,$sid,NULL,"Require Login",NULL,"SNGLCHKBX",30,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,"reqlogin");
$wd->addField($wd_id,$sid,NULL,"Title",NULL,"TEXT",40,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,NULL);
$wd->addField($wd_id,$sid,NULL,"Subtitle",NULL,"TEXT",50,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,NULL);
$wd->addField($wd_id,$sid,NULL,"Short Description",NULL,"TEXTAREA",60,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,"shortdescription");
$wd->addField($wd_id,$sid,NULL,"Div ID",NULL,"TEXT",70,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,'divid');
$wd->addField($wd_id,$sid,NULL,"Image",NULL,"MBL_UPL",80,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,NULL);
$wd->addField($wd_id,$sid,NULL,"Parent","Admin Menu","FOREIGN",90,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,NULL);
$wd->addField($wd_id,$sid,NULL,"onclick",NULL,"TEXT",100,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,"link");
$wd->addField($wd_id,$sid,NULL,"Location","menu,bottom,both","DROPDOWN",120,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,NULL);

$p_wd_row_id = $wd->addRow($wd_id);
$names = array();
$values = array();
$names[] = "enabled";
$names[] = "reqlogin";
$names[] = "location";
$names[] = "sequence";
$names[] = "title";
$names[] = "link";
$values[] = "Yes";
$values[] = "YES";
$values[] = "both";
$values[] = "1";
$values[] = "Admin Menu";
$values[] = "admincontroller.php";
$wd->updateMultipleValues($wd_id,$names,$values,$p_wd_row_id);


$wd_row_id = $wd->addRow($wd_id);
$names = array();
$values = array();
$names[] = "parent";
$names[] = "enabled";
$names[] = "reqlogin";
$names[] = "location";
$names[] = "sequence";
$names[] = "title";
$names[] = "link";
$values[] = $p_wd_row_id;
$values[] = "Yes";
$values[] = "YES";
$values[] = "both";
$values[] = "10";
$values[] = "User List";
$values[] = "admincontroller.php?action=listuserscloning";
$wd->updateMultipleValues($wd_id,$names,$values,$wd_row_id);


$wd_row_id = $wd->addRow($wd_id);
$names = array();
$values = array();
$names[] = "parent";
$names[] = "enabled";
$names[] = "reqlogin";
$names[] = "location";
$names[] = "sequence";
$names[] = "title";
$names[] = "link";
$values[] = $p_wd_row_id;
$values[] = "Yes";
$values[] = "YES";
$values[] = "both";
$values[] = "20";
$values[] = "jData";
$values[] = "admincontroller.php?action=wd_listwebdata";
$wd->updateMultipleValues($wd_id,$names,$values,$wd_row_id);

$wd_row_id = $wd->addRow($wd_id);
$names = array();
$values = array();
$names[] = "parent";
$names[] = "enabled";
$names[] = "reqlogin";
$names[] = "location";
$names[] = "sequence";
$names[] = "title";
$names[] = "link";
$values[] = $p_wd_row_id;
$values[] = "Yes";
$values[] = "YES";
$values[] = "both";
$values[] = "30";
$values[] = "JSF Tools";
$values[] = "admincontroller.php?action=jsftools";
$wd->updateMultipleValues($wd_id,$names,$values,$wd_row_id);

https://www.plasticsmarkets.org/jsfadmin/admincontroller.php?action=sqlquery

$wd_row_id = $wd->addRow($wd_id);
$names = array();
$values = array();
$names[] = "parent";
$names[] = "enabled";
$names[] = "reqlogin";
$names[] = "location";
$names[] = "sequence";
$names[] = "title";
$names[] = "link";
$values[] = $p_wd_row_id;
$values[] = "Yes";
$values[] = "YES";
$values[] = "both";
$values[] = "40";
$values[] = "SQL Query";
$values[] = "admincontroller.php?action=sqlquery";
$wd->updateMultipleValues($wd_id,$names,$values,$wd_row_id);



$wd_id = $wd->newWebData("Tools and Widgets Menu","",3,"chadjodon@hotmail.com","",1,0);
$wd->updateWebDataProperty($wd_id, "htags", "#system #menu #toolswidgets ");
$sid = $wd->addSection($wd_id);
$wd->addField($wd_id,$sid,NULL,"Sequence",NULL,"INT",10,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,NULL);
$wd->addField($wd_id,$sid,NULL,"Enabled","Yes,No","DROPDOWN",20,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,NULL);
$wd->addField($wd_id,$sid,NULL,"Require Login",NULL,"SNGLCHKBX",30,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,"reqlogin");
$wd->addField($wd_id,$sid,NULL,"Title",NULL,"TEXT",40,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,NULL);
$wd->addField($wd_id,$sid,NULL,"Subtitle",NULL,"TEXT",50,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,NULL);
$wd->addField($wd_id,$sid,NULL,"Short Description",NULL,"TEXTAREA",60,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,"shortdescription");
$wd->addField($wd_id,$sid,NULL,"Div ID",NULL,"TEXT",70,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,'divid');
$wd->addField($wd_id,$sid,NULL,"Image",NULL,"MBL_UPL",80,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,NULL);
$wd->addField($wd_id,$sid,NULL,"Parent","Admin Menu","FOREIGN",90,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,NULL);
$wd->addField($wd_id,$sid,NULL,"onclick",NULL,"TEXT",100,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,"link");
$wd->addField($wd_id,$sid,NULL,"Location","menu,bottom,both","DROPDOWN",120,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,NULL);





$wd_id = $wd->newWebData("Tools and Widgets","",3,"chadjodon@hotmail.com","",1,0);
$wd->updateWebDataProperty($wd_id, "htags", "#system #menu #toolswidgets ");
$sid = $wd->addSection($wd_id);
$wd->addField($wd_id,$sid,NULL,"Sequence",NULL,"INT",10,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,NULL);
$wd->addField($wd_id,$sid,NULL,"Enabled","Yes,No","DROPDOWN",20,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,NULL);
$wd->addField($wd_id,$sid,NULL,"Your Email",NULL,"TEXT",30,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,"email");
$wd->addField($wd_id,$sid,NULL,"Name",NULL,"TEXT",40,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,"title");
$wd->addField($wd_id,$sid,NULL,"Hashtag",NULL,"TEXT",50,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,"htag");
$wd->addField($wd_id,$sid,NULL,"URL",NULL,"TEXT",60,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,"link");
$wd->addField($wd_id,$sid,NULL,"Logo",NULL,"MBL_UPL",70,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,"img");
$wd->addField($wd_id,$sid,NULL,"Description",NULL,"TEXTAREA",80,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,"descr");
$wd->addField($wd_id,$sid,NULL,"Additional URLs","Tools and Widgets Menu","FOREIGNSRY",90,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,"addlurls");
$wd->addField($wd_id,$sid,NULL,"Abbreviation",NULL,"TEXT",100,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,"abbrev");
$wd->addField($wd_id,$sid,NULL,"Help tips",NULL,"TEXTAREA",110,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,"help");

$wd_row_id = $wd->addRow($wd_id);
$names = array();
$values = array();
$names[] = "sequence";
$names[] = "enabled";
$names[] = "email";
$names[] = "name";
$names[] = "hashtag";
$names[] = "abbrev";
$names[] = "descr";
$names[] = "url";
$values[] = "10";
$values[] = "Yes";
$values[] = "chadjodon@hotmail.com";
$values[] = "Administration UI";
$values[] = "admintool";
$values[] = "admintool";
$values[] = "This UI you are currently using";
$values[] = getBaseURL().$GLOBALS['adminFolder']."admincontroller.php";
$wd->updateMultipleValues($wd_id,$names,$values,$wd_row_id);



$wd_id = $wd->newWebData("Tools and Widgets Dynamic Reports Data","","3","chadjodon@hotmail.com","","1","2","-1","NEW","");
$sid = $wd->addSection($wd_id);
$wd->addField($wd_id,$sid,NULL,"Enabled","Yes,No","DROPDOWN","10","0","1","Yes","0","0",FALSE,"","0","","enabled");
$wd->addField($wd_id,$sid,NULL,"Sequence","","INT","20","0","1","","0","0",FALSE,"","0","","sequence");
$wd->addField($wd_id,$sid,NULL,"Bottom display","","INT","30","0","1","","0","0",FALSE,"","0","","ydisp");
$wd->addField($wd_id,$sid,NULL,"Value","","TEXT","40","0","1","","0","0",FALSE,"","0","","val");
$wd->addField($wd_id,$sid,NULL,"Value Display","","TEXT","50","0","1","","0","0",FALSE,"","0","","xdisp");


$wd_id = $wd->newWebData("Tools and Widgets Dynamic Report Search","","4","chadjodon@hotmail.com","","1","2","-1","NEW","");
$sid = $wd->addSection($wd_id);
$wd->addField($wd_id,$sid,NULL,"Sequence","","INT","1","0","1","","0","0",FALSE,"","0","","sequence");
$wd->addField($wd_id,$sid,NULL,"Display Name","","TEXT","10","0","1","","1","1",FALSE,"","1","","name");
$wd->addField($wd_id,$sid,NULL,"Parameter Name","","TEXT","10","0","1","","1","1",FALSE,"","1","","param");
$wd->addField($wd_id,$sid,NULL,"Prefix","","TEXT","20","0","1","","0","1",FALSE,"","1","","prefix");
$wd->addField($wd_id,$sid,NULL,"Data Type","text,int,date,json,bool,opts,json opts,info","DROPDOWN","30","0","1","","1","1",FALSE,"","1","","type");
$wd->addField($wd_id,$sid,NULL,"Required","","SNGLCHKBX","31","0","1","","0","0",FALSE,"","0","","required");
$wd->addField($wd_id,$sid,NULL,"Values","","TEXT","32","0","1","","0","0",FALSE,"","0","","values");
$wd->addField($wd_id,$sid,NULL,"JSON URL","","TEXTAREA","40","0","1","","0","1",FALSE,"","1","","json");
$wd->addField($wd_id,$sid,NULL,"Dependencies","","TEXTAREA","50","0","1","","0","1",FALSE,"","1",""," ");


$wd_id = $wd->newWebData("Tools and Widgets Dynamic Reports Saved","","4","chadjodon@hotmail.com","","1","2","-1","NEW","");
$sid = $wd->addSection($wd_id);
$wd->addField($wd_id,$sid,NULL,"Subject","","TEXT","1","0","1","","0","1",FALSE,"","1","","subject");
$wd->addField($wd_id,$sid,NULL,"Parameters"," ","TEXTAREA","10","0","1","","0","1",FALSE,"","1","","parameters");
$wd->addField($wd_id,$sid,NULL,"Unique Name","","TEXT","20","0","1","","0","0",FALSE,"","0","","dataid");


$wd_id = $wd->newWebData("Tools and Widgets Dynamic Reports","","4","chadjodon@hotmail.com","","1","2","-1","NEW","");
$sid = $wd->addSection($wd_id);
$wd->addField($wd_id,$sid,NULL,"Enabled","Yes,No","DROPDOWN","10","0","1","Yes","0","0",FALSE,"","0","","enabled");
$wd->addField($wd_id,$sid,NULL,"Sequence","","INT","20","0","0","","0","0",FALSE,"","0","","sequence");
$wd->addField($wd_id,$sid,NULL,"Authentication Tokens","","TEXTAREA","22","0","1","","0","0",FALSE,"","0","","accesstokens");
$wd->addField($wd_id,$sid,NULL,"Unique Name","","TEXT","25","0","1","","0","1",FALSE,"","1","","reportid");
$wd->addField($wd_id,$sid,NULL,"Name","","TEXT","30","0","1","","0","1",FALSE,"","1","","name");
$wd->addField($wd_id,$sid,NULL,"Description","","TEXTAREA","40","0","1","","0","1",FALSE,"","1","","description");
$wd->addField($wd_id,$sid,NULL,"Hashtags","","TEXTAREA","50","0","1","","0","1",FALSE,"","1","","hashtags");
$wd->addField($wd_id,$sid,NULL,"Type of query","JSON,Database,Website Data,Simple Data","DROPDOWN","60","0","1","","0","0",FALSE,"","0","","type");
$wd->addField($wd_id,$sid,NULL,"Simple Data Chart","Tools and Widgets Dynamic Reports Data","FOREIGNSRY","65","0","0","","0","0",FALSE,"","0","","simpledata");
$wd->addField($wd_id,$sid,NULL,"JSON URL","","TEXTAREA","70","0","0","","0","0",FALSE,"","0","","json");
$wd->addField($wd_id,$sid,NULL,"SQL Query","","TEXTAREA","80","0","0","","0","0",FALSE,"","0","","sql");
$wd->addField($wd_id,$sid,NULL,"Name of WebData Table","","TEXT","100","0","1","","0","0",FALSE,"","0","","wdparam");
$wd->addField($wd_id,$sid,NULL,"Group By","","TEXT","110","0","1","","0","0",FALSE,"","0","","groupparam");
$wd->addField($wd_id,$sid,NULL,"Average Field","","TEXT","120","0","1","","0","0",FALSE,"","0","","avgfld");
$wd->addField($wd_id,$sid,NULL,"Order by","","TEXT","130","0","1","","0","0",FALSE,"","0","","orderparam");
$wd->addField($wd_id,$sid,NULL,"Additional Where Clause","","TEXT","140","0","1","","0","0",FALSE,"","0","","addlwhere");
$wd->addField($wd_id,$sid,NULL,"Additional Select Clause","","TEXT","141","0","0","","0","0",FALSE,"","0","","addlselect");
$wd->addField($wd_id,$sid,NULL,"Notes 1","","TEXTAREA","150","0","0","","0","0",FALSE,"","0","","notes");
$wd->addField($wd_id,$sid,NULL,"This is for users (email)","","SNGLCHKBX","160","0","0","","0","0",FALSE,"","0","","forusers");
$wd->addField($wd_id,$sid,NULL,"Search Fields","Tools and Widgets Dynamic Report Search","FOREIGNSRY","170","0","0","","0","0",FALSE,"","0","","parameters");
$wd->addField($wd_id,$sid,NULL,"Show Graph","","SNGLCHKBX","180","0","0","","0","0",FALSE,"","0","","showgraph");
$wd->addField($wd_id,$sid,NULL,"Display count only","","SNGLCHKBX","190","0","0","","0","0",FALSE,"","0","","countonly");
$wd->addField($wd_id,$sid,NULL,"Dispay parameter","","TEXT","199","0","0","","0","0",FALSE,"","0","","graphdata");
$wd->addField($wd_id,$sid,NULL,"x data parameters","","TEXT","200","0","0","","0","0",FALSE,"","0","","graphx");
$wd->addField($wd_id,$sid,NULL,"y data parameters","","TEXT","210","0","0","","0","0",FALSE,"","0","","graphy");
$wd->addField($wd_id,$sid,NULL,"Notes 2","","TEXTAREA","220","0","1","","0","0",FALSE,"","0","","notes2");
$wd->addField($wd_id,$sid,NULL,"Saved Searches","Tools and Widgets Dynamic Reports Saved","FOREIGNSRY","230","0","0","","0","0",FALSE,"","0","","savedsearch");



$wd_id = $wd->newWebData("User Properties","",3,"chadjodon@hotmail.com","",1,0);
$sid = $wd->addSection($wd_id);
$wd->addField($wd_id,$sid,NULL,"Image",NULL,"MBL_UPL",80,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,"pic");
$wd->addField($wd_id,$sid,NULL,"Background Notes",NULL,"TEXTAREA",100,NULL,1,NULL,0,0,FALSE,NULL,NULL,NULL,"info");

?>
