<?php
include_once "../server/Classes.php";
error_reporting(E_ALL);


// Initial data to prime the database for Innovation Hub

$wd = new WebsiteData();
$dbLink = new MYSQLaccess;



$wdata = $wd->getWebData("Tools and Widgets");
$wd_id = $wdata['wd_id'];
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
$values[] = "20";
$values[] = "Yes";
$values[] = "chadjodon@hotmail.com";
$values[] = "Innovation HUB";
$values[] = "innovhub";
$values[] = "innovhub";
$values[] = "Tool to determine which IBM MVP program is best for sales.";
$values[] = getBaseURL()."innovhub/";
$wd->updateMultipleValues($wd_id,$names,$values,$wd_row_id);





print "<br><br>------------------------------------<br>Creating: innovhub Data Grab<br>";
$wd_id = $wd->newWebData("innovhub Data Grab","","3","chadjodon@hotmail.com","","1","1","-1","NEW","");
$wd->updateWebDataProperty($wd_id, "htags", "#innovhub ");
$sid = $wd->addSection($wd_id);
$qs = array();
$qs["name"] = $wd->addField($wd_id,$sid,NULL,"name","","TEXT","10","0","1","","0","0",FALSE,"","0","","name");
$qs["email"] = $wd->addField($wd_id,$sid,NULL,"email","","TEXT","20","0","1","","0","0",FALSE,"","0","","email");
$qs["phone"] = $wd->addField($wd_id,$sid,NULL,"phone","","TEXT","30","0","1","","0","0",FALSE,"","0","","phone");
$qs["comments"] = $wd->addField($wd_id,$sid,NULL,"comments","","TEXTAREA","40","0","1","","0","0",FALSE,"","0","","comments");
$qs["notes"] = $wd->addField($wd_id,$sid,NULL,"notes","","TEXTAREA","50","0","1","","0","0",FALSE,"","0","","notes");









print "<br><br>------------------------------------<br>Creating: innovhub engagement models<br>";
$wd_id = $wd->newWebData("innovhub engagement models","","3","chadjodon@hotmail.com","","1","2","-1","NEW","");
$wd->updateWebDataProperty($wd_id, "htags", "#innovhub ");
$sid = $wd->addSection($wd_id);
$qs = array();
$qs["sequence"] = $wd->addField($wd_id,$sid,NULL,"Sequence","","INT","10","0","1","","0","0",FALSE,"","0","","sequence");
$qs["enabled"] = $wd->addField($wd_id,$sid,NULL,"Enabled","Yes,No","DROPDOWN","20","0","1","Yes","0","0",FALSE,"","0","","enabled");
$qs["name"] = $wd->addField($wd_id,$sid,NULL,"Name","","TEXT","30","0","1","","0","0",FALSE,"","0","","name");


$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["sequence"]."='10', ".$qs["enabled"]."='Yes', ".$qs["name"]."='No Funding' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["sequence"]."='20', ".$qs["enabled"]."='Yes', ".$qs["name"]."='Partial Funding' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["sequence"]."='30', ".$qs["enabled"]."='Yes', ".$qs["name"]."='Fully Funded' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);


print "<br><br>------------------------------------<br>Creating: innovhub talent roles<br>";
$wd_id = $wd->newWebData("innovhub talent roles","","3","chadjodon@hotmail.com","","1","2","-1","NEW","");
$wd->updateWebDataProperty($wd_id, "htags", "#innovhub ");
$sid = $wd->addSection($wd_id);
$qs = array();
$qs["sequence"] = $wd->addField($wd_id,$sid,NULL,"Sequence","","INT","10","0","1","","0","0",FALSE,"","0","","sequence");
$qs["enabled"] = $wd->addField($wd_id,$sid,NULL,"Enabled","Yes,No","DROPDOWN","20","0","1","Yes","0","0",FALSE,"","0","","enabled");
$qs["name"] = $wd->addField($wd_id,$sid,NULL,"Name","","TEXT","30","0","1","","0","0",FALSE,"","0","","name");


$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["sequence"]."='10', ".$qs["enabled"]."='Yes', ".$qs["name"]."='Developers' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["sequence"]."='20', ".$qs["enabled"]."='Yes', ".$qs["name"]."='Engineers' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["sequence"]."='30', ".$qs["enabled"]."='Yes', ".$qs["name"]."='Designers' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["sequence"]."='40', ".$qs["enabled"]."='Yes', ".$qs["name"]."='Architect' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);


print "<br><br>------------------------------------<br>Creating: innovhub types of requests<br>";
$wd_id = $wd->newWebData("innovhub types of requests","","3","chadjodon@hotmail.com","","1","2","-1","NEW","");
$wd->updateWebDataProperty($wd_id, "htags", "#innovhub ");
$sid = $wd->addSection($wd_id);
$qs = array();
$qs["sequence"] = $wd->addField($wd_id,$sid,NULL,"Sequence","","INT","10","0","1","","0","0",FALSE,"","0","","sequence");
$qs["enabled"] = $wd->addField($wd_id,$sid,NULL,"Enabled","Yes,No","DROPDOWN","20","0","1","Yes","0","0",FALSE,"","0","","enabled");
$qs["name"] = $wd->addField($wd_id,$sid,NULL,"Name","","TEXT","30","0","1","","0","0",FALSE,"","0","","name");


$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["sequence"]."='10', ".$qs["enabled"]."='Yes', ".$qs["name"]."='Cloud Paks' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["sequence"]."='20', ".$qs["enabled"]."='Yes', ".$qs["name"]."='Cross-cloud' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["sequence"]."='30', ".$qs["enabled"]."='Yes', ".$qs["name"]."='Cross-brand' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);


print "<br><br>------------------------------------<br>Creating: innovhub programs<br>";
$wd_id = $wd->newWebData("innovhub programs","","3","chadjodon@hotmail.com","","1","2","-1","NEW","");
$wd->updateWebDataProperty($wd_id, "htags", "#innovhub ");
$sid = $wd->addSection($wd_id);
$qs = array();
$qs["sequence"] = $wd->addField($wd_id,$sid,NULL,"Sequence","","INT","1","0","1","","0","0",FALSE,"","0","","sequence");
$qs["enabled"] = $wd->addField($wd_id,$sid,NULL,"Enabled","Yes,No","DROPDOWN","2","0","1","Yes","0","0",FALSE,"","0","","enabled");
$qs["teamname"] = $wd->addField($wd_id,$sid,NULL,"Team Name","","TEXT","10","0","1","","0","0",FALSE,"","0","","teamname");
$qs["leaders"] = $wd->addField($wd_id,$sid,NULL,"Leaders","","TEXT","20","0","1","","0","0",FALSE,"","0","","leaders");
$qs["description"] = $wd->addField($wd_id,$sid,NULL,"Description","","TEXTAREA","30","0","1","","0","0",FALSE,"","0","","description");
$qs["notes"] = $wd->addField($wd_id,$sid,NULL,"Notes","","TEXTAREA","40","0","1","","0","0",FALSE,"","0","","notes");
$qs["model"] = $wd->addField($wd_id,$sid,NULL,"Engagement Model","innovhub engagement models,name","FOREIGNCB","50","0","1","","0","0",FALSE,"","1","","model");
$qs["talent"] = $wd->addField($wd_id,$sid,NULL,"Type of Talent","innovhub talent roles,name","FOREIGNCB","60","0","1","","0","0",FALSE,"","1","","talent");
$qs["request"] = $wd->addField($wd_id,$sid,NULL,"Type of Request","innovhub types of requests,name","FOREIGNCB","70","0","1","","0","0",FALSE,"","1","","request");
$qs["clients"] = $wd->addField($wd_id,$sid,NULL,"Available to specific clents","","TEXT","80","0","0","","0","0",FALSE,"","0","","clients");


$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["sequence"]."='10', ".$qs["enabled"]."='Yes', ".$qs["teamname"]."='IBM Garage for Cloud', ".$qs["leaders"]."='Katie Kean', ".$qs["description"]."='Engineering (Solution engineering) John McLean<BR>', ".$qs["notes"]."='Group of Fellows&#44; Distinguished Engineers&#44; cloud architects&#44; engineers..', ".$qs["model"]."='Partial Funding', ".$qs["talent"]."='Developers&#44;Engineers&#44;Designers&#44;Architect', ".$qs["request"]."='Cloud Paks&#44;Cross-cloud&#44;Cross-brand' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["sequence"]."='80', ".$qs["enabled"]."='Yes', ".$qs["teamname"]."='Cloud Surge', ".$qs["leaders"]."='Elly K', ".$qs["description"]."='Drive holistic engagements. For Industry clients&#44; 1:1 dedicated skill. Led by CTL/ICL', ".$qs["model"]."='Partial Funding', ".$qs["talent"]."='Engineers&#44;Architect', ".$qs["request"]."='Cross-brand' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["sequence"]."='300', ".$qs["enabled"]."='Yes', ".$qs["teamname"]."='Test client program', ".$qs["leaders"]."='Chad Jodon', ".$qs["description"]."='Wal-mart specific program', ".$qs["model"]."='1', ".$qs["talent"]."='1&#44;2', ".$qs["request"]."='1&#44;2&#44;3', ".$qs["clients"]."='Walmart' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);













print "<br><br>------------------------------------<br>Creating: innovhub Menu<br>";
$wd_id = $wd->newWebData("innovhub Menu","","3","chadjodon@hotmail.com","","1","","-1","NEW","");
$wd->updateWebDataProperty($wd_id, "htags", "#innovhub ");
$sid = $wd->addSection($wd_id);
$qs = array();
$qs["sequence"] = $wd->addField($wd_id,$sid,NULL,"Sequence","","INT","10","0","1","","0","0",FALSE,"","0","","sequence");
$qs["enabled"] = $wd->addField($wd_id,$sid,NULL,"Enabled","Yes,No","DROPDOWN","20","0","1","","0","0",FALSE,"","0","","enabled");
$qs["title"] = $wd->addField($wd_id,$sid,NULL,"Title","","TEXT","30","0","1","","0","0",FALSE,"","0","","title");
$qs["shortdescription"] = $wd->addField($wd_id,$sid,NULL,"Short Description","","TEXTAREA","32","0","1","","0","0",FALSE,"","0","","shortdescription");
$qs["divid"] = $wd->addField($wd_id,$sid,NULL,"Div ID","","TEXT","35","0","0","","0","0",FALSE,"","0","","divid");
$qs["onclick"] = $wd->addField($wd_id,$sid,NULL,"onclick","","TEXT","50","0","1","","0","0",FALSE,"","0","","onclick");
$qs["link"] = $wd->addField($wd_id,$sid,NULL,"URL","","TEXT","60","0","1","","0","0",FALSE,"","0","","link");
$qs["location"] = $wd->addField($wd_id,$sid,NULL,"Location","menu,bottom,both","DROPDOWN","70","0","1","","0","0",FALSE,"","0","","location");


$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["sequence"]."='10', ".$qs["enabled"]."='Yes', ".$qs["title"]."='What We Bring', ".$qs["link"]."='https://w3.ibm.com/w3publisher/innovation-hub-distribution/what-we-bring', ".$qs["location"]."='menu' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["sequence"]."='20', ".$qs["enabled"]."='Yes', ".$qs["title"]."='How It Works', ".$qs["link"]."='https://w3.ibm.com/w3publisher/innovation-hub-distribution/how-it-works', ".$qs["location"]."='menu' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["sequence"]."='30', ".$qs["enabled"]."='Yes', ".$qs["title"]."='Get Started', ".$qs["link"]."='https://w3.ibm.com/w3publisher/innovation-hub-distribution/get-started', ".$qs["location"]."='menu' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["sequence"]."='40', ".$qs["enabled"]."='Yes', ".$qs["title"]."='Our Accounts', ".$qs["link"]."='https://w3.ibm.com/w3publisher/innovation-hub-distribution/our-accounts', ".$qs["location"]."='menu' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["sequence"]."='50', ".$qs["enabled"]."='Yes', ".$qs["title"]."='Meet The Team', ".$qs["link"]."='https://w3.ibm.com/w3publisher/innovation-hub-distribution/meet-the-team', ".$qs["location"]."='menu' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["sequence"]."='60', ".$qs["enabled"]."='Yes', ".$qs["title"]."='Engage', ".$qs["divid"]."='engage', ".$qs["link"]."='https://ibminnovationhub.com/view/engage', ".$qs["location"]."='menu' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);





































print "<br><br>------------------------------------<br>Creating: innovhub Pages<br>";
$wd_id = $wd->newWebData("innovhub Pages","","3","chadjodon@hotmail.com","","1","","-1","NEW","");
$wd->updateWebDataProperty($wd_id, "htags", "#innovhub ");
$sid = $wd->addSection($wd_id);
$qs = array();
$qs["enabled"] = $wd->addField($wd_id,$sid,NULL,"Enabled","Yes,No","DROPDOWN","10","0","1","","0","0",FALSE,"","0","","enabled");
$qs["name"] = $wd->addField($wd_id,$sid,NULL,"Name","","TEXT","10","0","1","","0","0",FALSE,"","0","","name");
$qs["value"] = $wd->addField($wd_id,$sid,NULL,"Value","","TEXT","10","0","1","","0","0",FALSE,"","0","","value");
$qs["image"] = $wd->addField($wd_id,$sid,NULL,"Image","","MBL_UPL","10","0","1","","0","0",FALSE,"","0","","image");
$qs["version"] = $wd->addField($wd_id,$sid,NULL,"Version","","INT","100","0","1","","0","0",FALSE,"","0","","version");
$qs["verstatus"] = $wd->addField($wd_id,$sid,NULL,"Version Status","NEW,ACTIVE,INACTIVE","DROPDOWN","110","0","1","NEW","0","0",FALSE,"","0","","verstatus");


$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='No', ".$qs["name"]."='Page: home', ".$qs["value"]."='{&#34;rowcount&#34;:1&#44;&#34;rows&#34;:[]}', ".$qs["version"]."='1', ".$qs["verstatus"]."='NEW' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='YES', ".$qs["name"]."='Page: home_jsflock', ".$qs["value"]."='1', ".$qs["version"]."='1', ".$qs["verstatus"]."='ACTIVE' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='No', ".$qs["name"]."='Page: home_jsf1', ".$qs["value"]."='{&#34;rows&#34;:[{&#34;type&#34;:&#34;sequential&#34;&#44;&#34;pad&#34;:0&#44;&#34;slots&#34;:[{&#34;wd&#34;:100&#44;&#34;layers&#34;:[{&#34;type&#34;:&#34;Visual Builder&#34;&#44;&#34;wd&#34;:&#34;100&#34;&#44;&#34;left&#34;:&#34;0&#34;&#44;&#34;top&#34;:&#34;0&#34;&#44;&#34;trx&#34;:&#34;&#34;&#44;&#34;fsz&#34;:null&#44;&#34;pad&#34;:null&#44;&#34;max&#34;:null&#44;&#34;ffm&#34;:&#34;&#34;&#44;&#34;aln&#34;:false&#44;&#34;bld&#34;:false&#44;&#34;dep&#34;:&#34;no shadow&#34;&#44;&#34;hide&#34;:false&#44;&#34;clr&#34;:&#34;&#34;&#44;&#34;bg&#34;:&#34;&#34;&#44;&#34;content&#34;:&#34;homeheader&#34;&#44;&#34;imgdsp&#34;:&#34;full&#34;&#44;&#34;url&#34;:&#34;&#34;&#44;&#34;onclick&#34;:&#34;&#34;&#44;&#34;mttl&#34;:&#34;&#34;&#44;&#34;msubttl&#34;:&#34;&#34;&#44;&#34;mside&#34;:&#34;center&#34;&#44;&#34;mstyle&#34;:&#34;none&#34;&#44;&#34;mpad&#34;:&#34;&#34;&#44;&#34;mbtn1&#34;:&#34;&#34;&#44;&#34;murl1&#34;:&#34;&#34;&#44;&#34;mbg1&#34;:&#34;&#34;&#44;&#34;mfg1&#34;:&#34;&#34;&#44;&#34;mhg1&#34;:&#34;&#34;&#44;&#34;mbtn2&#34;:&#34;&#34;&#44;&#34;murl2&#34;:&#34;&#34;&#44;&#34;mbg2&#34;:&#34;&#34;&#44;&#34;mfg2&#34;:&#34;&#34;&#44;&#34;mhg2&#34;:&#34;&#34;&#44;&#34;mbtn3&#34;:&#34;&#34;&#44;&#34;murl3&#34;:&#34;&#34;&#44;&#34;mbg3&#34;:&#34;&#34;&#44;&#34;mfg3&#34;:&#34;&#34;&#44;&#34;mhg3&#34;:&#34;&#34;&#44;&#34;mbtn4&#34;:&#34;&#34;&#44;&#34;murl4&#34;:&#34;&#34;&#44;&#34;mbg4&#34;:&#34;&#34;&#44;&#34;mfg4&#34;:&#34;&#34;&#44;&#34;mhg4&#34;:&#34;&#34;&#44;&#34;mbtn5&#34;:&#34;&#34;&#44;&#34;murl5&#34;:&#34;&#34;&#44;&#34;mbg5&#34;:&#34;&#34;&#44;&#34;mfg5&#34;:&#34;&#34;&#44;&#34;mhg5&#34;:&#34;&#34;&#44;&#34;vcontent&#34;:&#34;homeheader&#34;}]}]&#44;&#34;htty&#34;:&#34;px&#34;&#44;&#34;bg&#34;:&#34;&#34;&#44;&#34;ext&#34;:false&#44;&#34;tile&#34;:false&#44;&#34;ancimg&#34;:true&#44;&#34;txt&#34;:&#34;&#34;&#44;&#34;lbl&#34;:&#34;Row 1&#34;&#44;&#34;ht&#34;:220&#44;&#34;img&#34;:&#34;http://www.jstorefront.com/jsfcode/srvyfiles/wd_bg/20200302225902_9_0_bg_0_1.jpg&#34;&#44;&#34;vht&#34;:220}]}', ".$qs["version"]."='1', ".$qs["verstatus"]."='NEW' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='yes', ".$qs["name"]."='Visual: homeheader', ".$qs["value"]."='{&#34;divname&#34;:&#34;title&#34;&#44;&#34;lf&#34;:2147&#44;&#34;tp&#34;:1867&#44;&#34;wd&#34;:6000&#44;&#34;ht&#34;:2933&#44;&#34;rad&#34;:0&#44;&#34;zindex&#34;:&#34;1&#34;&#44;&#34;fsz&#34;:280&#44;&#34;fclr&#34;:&#34;#000000&#34;&#44;&#34;ffam&#34;:&#34;&#34;&#44;&#34;classname&#34;:&#34;&#34;&#44;&#34;fbld&#34;:&#34;0&#34;&#44;&#34;rqd&#34;:&#34;0&#34;&#44;&#34;tabi&#34;:&#34;1&#34;&#44;&#34;fund&#34;:&#34;0&#34;&#44;&#34;fitl&#34;:&#34;0&#34;&#44;&#34;faln&#34;:&#34;left&#34;&#44;&#34;bgclr&#34;:&#34;&#34;&#44;&#34;opacity&#34;:&#34;&#34;&#44;&#34;hide&#34;:&#34;0&#34;&#44;&#34;onclick&#34;:&#34;&#34;&#44;&#34;type&#34;:&#34;&#34;&#44;&#34;wd_id&#34;:&#34;&#34;&#44;&#34;section&#34;:&#34;&#34;&#44;&#34;field_id&#34;:&#34;&#34;&#44;&#34;wdtype&#34;:&#34;display&#34;&#44;&#34;txt&#34;:&#34;&#34;&#44;&#34;img&#34;:&#34;&#34;&#44;&#34;fin&#34;:&#34;&#34;&#44;&#34;fout&#34;:&#34;&#34;&#44;&#34;move&#34;:&#34;&#34;&#44;&#34;parent&#34;:&#34;&#34;}' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='yes', ".$qs["name"]."='Visual: homeheader_20200302backup', ".$qs["value"]."='{&#34;divname&#34;:&#34;Layer 1&#34;&#44;&#34;lf&#34;:2966&#44;&#34;tp&#34;:1944&#44;&#34;wd&#34;:10401&#44;&#34;ht&#34;:4068&#44;&#34;rad&#34;:0&#44;&#34;zindex&#34;:&#34;1&#34;&#44;&#34;fsz&#34;:281&#44;&#34;fclr&#34;:&#34;#000000&#34;&#44;&#34;ffam&#34;:&#34;&#34;&#44;&#34;classname&#34;:&#34;&#34;&#44;&#34;fbld&#34;:&#34;0&#34;&#44;&#34;rqd&#34;:&#34;0&#34;&#44;&#34;tabi&#34;:&#34;1&#34;&#44;&#34;fund&#34;:&#34;0&#34;&#44;&#34;fitl&#34;:&#34;0&#34;&#44;&#34;faln&#34;:&#34;left&#34;&#44;&#34;bgclr&#34;:&#34;&#34;&#44;&#34;opacity&#34;:&#34;&#34;&#44;&#34;hide&#34;:&#34;0&#34;&#44;&#34;onclick&#34;:&#34;&#34;&#44;&#34;type&#34;:&#34;&#34;&#44;&#34;wd_id&#34;:&#34;&#34;&#44;&#34;section&#34;:&#34;&#34;&#44;&#34;field_id&#34;:&#34;&#34;&#44;&#34;wdtype&#34;:&#34;display&#34;&#44;&#34;txt&#34;:&#34;&#34;&#44;&#34;img&#34;:&#34;&#34;&#44;&#34;fin&#34;:&#34;&#34;&#44;&#34;fout&#34;:&#34;&#34;&#44;&#34;move&#34;:&#34;&#34;&#44;&#34;parent&#34;:&#34;&#34;}' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='yes', ".$qs["name"]."='Visual: homeheader', ".$qs["value"]."='{&#34;type&#34;:&#34;header&#34;&#44;&#34;infoonly&#34;:&#34;Innovation HUB Engagement&#34;&#44;&#34;oright&#34;:&#34;750&#34;}' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='yes', ".$qs["name"]."='Visual: homeheader', ".$qs["value"]."='{&#34;divname&#34;:&#34;titletext&#34;&#44;&#34;lf&#34;:0&#44;&#34;tp&#34;:1147&#44;&#34;wd&#34;:6000&#44;&#34;ht&#34;:507&#44;&#34;rad&#34;:0&#44;&#34;zindex&#34;:&#34;1&#34;&#44;&#34;fsz&#34;:347&#44;&#34;fclr&#34;:&#34;#FFFFFF&#34;&#44;&#34;ffam&#34;:&#34;&#34;&#44;&#34;classname&#34;:&#34;&#34;&#44;&#34;fbld&#34;:&#34;1&#34;&#44;&#34;rqd&#34;:&#34;0&#34;&#44;&#34;tabi&#34;:&#34;1&#34;&#44;&#34;fund&#34;:&#34;0&#34;&#44;&#34;fitl&#34;:&#34;0&#34;&#44;&#34;faln&#34;:&#34;center&#34;&#44;&#34;bgclr&#34;:&#34;&#34;&#44;&#34;opacity&#34;:&#34;&#34;&#44;&#34;hide&#34;:&#34;0&#34;&#44;&#34;onclick&#34;:&#34;&#34;&#44;&#34;type&#34;:&#34;&#34;&#44;&#34;wd_id&#34;:&#34;&#34;&#44;&#34;section&#34;:&#34;&#34;&#44;&#34;field_id&#34;:&#34;&#34;&#44;&#34;wdtype&#34;:&#34;display&#34;&#44;&#34;txt&#34;:&#34;Innovation HUB Engagement&#34;&#44;&#34;img&#34;:&#34;&#34;&#44;&#34;fin&#34;:&#34;&#34;&#44;&#34;fout&#34;:&#34;&#34;&#44;&#34;move&#34;:&#34;&#34;&#44;&#34;parent&#34;:&#34;4&#34;}' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='yes', ".$qs["name"]."='Visual: homeheader', ".$qs["value"]."='{&#34;divname&#34;:&#34;dummy&#34;&#44;&#34;lf&#34;:1160&#44;&#34;tp&#34;:1053&#44;&#34;wd&#34;:7933&#44;&#34;ht&#34;:4600&#44;&#34;rad&#34;:0&#44;&#34;zindex&#34;:&#34;1&#34;&#44;&#34;fsz&#34;:187&#44;&#34;fclr&#34;:&#34;#000000&#34;&#44;&#34;ffam&#34;:&#34;&#34;&#44;&#34;classname&#34;:&#34;&#34;&#44;&#34;fbld&#34;:&#34;0&#34;&#44;&#34;rqd&#34;:&#34;0&#34;&#44;&#34;tabi&#34;:&#34;1&#34;&#44;&#34;fund&#34;:&#34;0&#34;&#44;&#34;fitl&#34;:&#34;0&#34;&#44;&#34;faln&#34;:&#34;left&#34;&#44;&#34;bgclr&#34;:&#34;#DDDDDD&#34;&#44;&#34;opacity&#34;:&#34;&#34;&#44;&#34;hide&#34;:&#34;1&#34;&#44;&#34;onclick&#34;:&#34;&#34;&#44;&#34;type&#34;:&#34;&#34;&#44;&#34;wd_id&#34;:&#34;&#34;&#44;&#34;section&#34;:&#34;&#34;&#44;&#34;field_id&#34;:&#34;&#34;&#44;&#34;wdtype&#34;:&#34;display&#34;&#44;&#34;txt&#34;:&#34;&#34;&#44;&#34;img&#34;:&#34;&#34;&#44;&#34;fin&#34;:&#34;&#34;&#44;&#34;fout&#34;:&#34;&#34;&#44;&#34;move&#34;:&#34;&#34;&#44;&#34;parent&#34;:&#34;&#34;}' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='Yes', ".$qs["name"]."='Page: homepage', ".$qs["value"]."='{&#34;rowcount&#34;:1&#44;&#34;rows&#34;:[]}', ".$qs["version"]."='1', ".$qs["verstatus"]."='ACTIVE' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='Yes', ".$qs["name"]."='Page: homepage_jsf1', ".$qs["value"]."='{&#34;rows&#34;:[{&#34;type&#34;:&#34;sequential&#34;&#44;&#34;pad&#34;:0&#44;&#34;slots&#34;:[{&#34;wd&#34;:100&#44;&#34;layers&#34;:[{&#34;type&#34;:&#34;HTML&#34;&#44;&#34;wd&#34;:&#34;100&#34;&#44;&#34;left&#34;:&#34;0&#34;&#44;&#34;top&#34;:&#34;0&#34;&#44;&#34;trx&#34;:&#34;&#34;&#44;&#34;fsz&#34;:null&#44;&#34;pad&#34;:null&#44;&#34;max&#34;:null&#44;&#34;ffm&#34;:&#34;&#34;&#44;&#34;aln&#34;:false&#44;&#34;bld&#34;:false&#44;&#34;dep&#34;:&#34;no shadow&#34;&#44;&#34;hide&#34;:false&#44;&#34;clr&#34;:&#34;&#34;&#44;&#34;bg&#34;:&#34;&#34;&#44;&#34;content&#34;:&#34;&lt;script&gt;#jsflf#location.href = #jsfsquote#https://w3.ibm.com/w3publisher/innovation-hub-distribution#jsfsquote#;#jsflf#&lt;/script&gt;&#34;&#44;&#34;imgdsp&#34;:&#34;full&#34;&#44;&#34;url&#34;:&#34;&#34;&#44;&#34;onclick&#34;:&#34;&#34;&#44;&#34;mttl&#34;:&#34;&#34;&#44;&#34;msubttl&#34;:&#34;&#34;&#44;&#34;mside&#34;:&#34;center&#34;&#44;&#34;mstyle&#34;:&#34;none&#34;&#44;&#34;mpad&#34;:&#34;&#34;&#44;&#34;mbtn1&#34;:&#34;&#34;&#44;&#34;murl1&#34;:&#34;&#34;&#44;&#34;mbg1&#34;:&#34;&#34;&#44;&#34;mfg1&#34;:&#34;&#34;&#44;&#34;mhg1&#34;:&#34;&#34;&#44;&#34;mbtn2&#34;:&#34;&#34;&#44;&#34;murl2&#34;:&#34;&#34;&#44;&#34;mbg2&#34;:&#34;&#34;&#44;&#34;mfg2&#34;:&#34;&#34;&#44;&#34;mhg2&#34;:&#34;&#34;&#44;&#34;mbtn3&#34;:&#34;&#34;&#44;&#34;murl3&#34;:&#34;&#34;&#44;&#34;mbg3&#34;:&#34;&#34;&#44;&#34;mfg3&#34;:&#34;&#34;&#44;&#34;mhg3&#34;:&#34;&#34;&#44;&#34;mbtn4&#34;:&#34;&#34;&#44;&#34;murl4&#34;:&#34;&#34;&#44;&#34;mbg4&#34;:&#34;&#34;&#44;&#34;mfg4&#34;:&#34;&#34;&#44;&#34;mhg4&#34;:&#34;&#34;&#44;&#34;mbtn5&#34;:&#34;&#34;&#44;&#34;murl5&#34;:&#34;&#34;&#44;&#34;mbg5&#34;:&#34;&#34;&#44;&#34;mfg5&#34;:&#34;&#34;&#44;&#34;mhg5&#34;:&#34;&#34;&#44;&#34;vcontent&#34;:&#34;homeheader&#34;}]}]&#44;&#34;htty&#34;:&#34;px&#34;&#44;&#34;bg&#34;:&#34;&#34;&#44;&#34;ext&#34;:false&#44;&#34;tile&#34;:false&#44;&#34;ancimg&#34;:true&#44;&#34;txt&#34;:&#34;&#34;&#44;&#34;lbl&#34;:&#34;Row 1&#34;&#44;&#34;ht&#34;:0&#44;&#34;img&#34;:&#34;&#34;&#44;&#34;vht&#34;:220}]}', ".$qs["version"]."='1', ".$qs["verstatus"]."='ACTIVE' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='Yes', ".$qs["name"]."='Page: engage', ".$qs["value"]."='{&#34;rowcount&#34;:4&#44;&#34;rows&#34;:[]}', ".$qs["version"]."='1', ".$qs["verstatus"]."='ACTIVE' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='Yes', ".$qs["name"]."='Page: engage_jsf1', ".$qs["value"]."='{&#34;rows&#34;:[{&#34;type&#34;:&#34;sequential&#34;&#44;&#34;pad&#34;:0&#44;&#34;slots&#34;:[{&#34;wd&#34;:100&#44;&#34;layers&#34;:[{&#34;type&#34;:&#34;Visual Builder&#34;&#44;&#34;wd&#34;:&#34;100&#34;&#44;&#34;left&#34;:&#34;0&#34;&#44;&#34;top&#34;:&#34;0&#34;&#44;&#34;trx&#34;:&#34;&#34;&#44;&#34;fsz&#34;:null&#44;&#34;pad&#34;:null&#44;&#34;max&#34;:null&#44;&#34;ffm&#34;:&#34;&#34;&#44;&#34;aln&#34;:false&#44;&#34;bld&#34;:false&#44;&#34;dep&#34;:&#34;no shadow&#34;&#44;&#34;hide&#34;:false&#44;&#34;clr&#34;:&#34;&#34;&#44;&#34;bg&#34;:&#34;&#34;&#44;&#34;content&#34;:&#34;homeheader&#34;&#44;&#34;imgdsp&#34;:&#34;full&#34;&#44;&#34;url&#34;:&#34;&#34;&#44;&#34;onclick&#34;:&#34;&#34;&#44;&#34;mttl&#34;:&#34;&#34;&#44;&#34;msubttl&#34;:&#34;&#34;&#44;&#34;mside&#34;:&#34;center&#34;&#44;&#34;mstyle&#34;:&#34;none&#34;&#44;&#34;mpad&#34;:&#34;&#34;&#44;&#34;mbtn1&#34;:&#34;&#34;&#44;&#34;murl1&#34;:&#34;&#34;&#44;&#34;mbg1&#34;:&#34;&#34;&#44;&#34;mfg1&#34;:&#34;&#34;&#44;&#34;mhg1&#34;:&#34;&#34;&#44;&#34;mbtn2&#34;:&#34;&#34;&#44;&#34;murl2&#34;:&#34;&#34;&#44;&#34;mbg2&#34;:&#34;&#34;&#44;&#34;mfg2&#34;:&#34;&#34;&#44;&#34;mhg2&#34;:&#34;&#34;&#44;&#34;mbtn3&#34;:&#34;&#34;&#44;&#34;murl3&#34;:&#34;&#34;&#44;&#34;mbg3&#34;:&#34;&#34;&#44;&#34;mfg3&#34;:&#34;&#34;&#44;&#34;mhg3&#34;:&#34;&#34;&#44;&#34;mbtn4&#34;:&#34;&#34;&#44;&#34;murl4&#34;:&#34;&#34;&#44;&#34;mbg4&#34;:&#34;&#34;&#44;&#34;mfg4&#34;:&#34;&#34;&#44;&#34;mhg4&#34;:&#34;&#34;&#44;&#34;mbtn5&#34;:&#34;&#34;&#44;&#34;murl5&#34;:&#34;&#34;&#44;&#34;mbg5&#34;:&#34;&#34;&#44;&#34;mfg5&#34;:&#34;&#34;&#44;&#34;mhg5&#34;:&#34;&#34;&#44;&#34;vcontent&#34;:&#34;homeheader&#34;}]}]&#44;&#34;htty&#34;:&#34;px&#34;&#44;&#34;bg&#34;:&#34;&#34;&#44;&#34;ext&#34;:false&#44;&#34;tile&#34;:false&#44;&#34;ancimg&#34;:true&#44;&#34;txt&#34;:&#34;&#34;&#44;&#34;lbl&#34;:&#34;herobanner&#34;&#44;&#34;ht&#34;:220&#44;&#34;img&#34;:&#34;http://www.jstorefront.com/jsfcode/srvyfiles/wd_bg/20200302225902_9_0_bg_0_1.jpg&#34;&#44;&#34;vht&#34;:220}]}', ".$qs["version"]."='1', ".$qs["verstatus"]."='ACTIVE' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='YES', ".$qs["name"]."='Page: homepage_jsflock', ".$qs["value"]."='1', ".$qs["version"]."='1', ".$qs["verstatus"]."='ACTIVE' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='YES', ".$qs["name"]."='Page: engage_jsflock', ".$qs["value"]."='1', ".$qs["version"]."='1', ".$qs["verstatus"]."='ACTIVE' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='Yes', ".$qs["name"]."='Page: engage_jsf2', ".$qs["value"]."='{&#34;rows&#34;:[{&#34;type&#34;:&#34;sequential&#34;&#44;&#34;pad&#34;:0&#44;&#34;slots&#34;:[{&#34;wd&#34;:100&#44;&#34;layers&#34;:[{&#34;type&#34;:&#34;Visual Builder&#34;&#44;&#34;wd&#34;:&#34;100&#34;&#44;&#34;left&#34;:&#34;0&#34;&#44;&#34;top&#34;:&#34;0&#34;&#44;&#34;trx&#34;:&#34;&#34;&#44;&#34;fsz&#34;:null&#44;&#34;pad&#34;:null&#44;&#34;max&#34;:null&#44;&#34;ffm&#34;:&#34;&#34;&#44;&#34;aln&#34;:false&#44;&#34;bld&#34;:false&#44;&#34;dep&#34;:&#34;no shadow&#34;&#44;&#34;hide&#34;:false&#44;&#34;clr&#34;:&#34;&#34;&#44;&#34;bg&#34;:&#34;&#34;&#44;&#34;content&#34;:&#34;clientquestion&#34;&#44;&#34;imgdsp&#34;:&#34;full&#34;&#44;&#34;url&#34;:&#34;&#34;&#44;&#34;onclick&#34;:&#34;&#34;&#44;&#34;mttl&#34;:&#34;&#34;&#44;&#34;msubttl&#34;:&#34;&#34;&#44;&#34;mside&#34;:&#34;center&#34;&#44;&#34;mstyle&#34;:&#34;none&#34;&#44;&#34;mpad&#34;:&#34;&#34;&#44;&#34;mbtn1&#34;:&#34;&#34;&#44;&#34;murl1&#34;:&#34;&#34;&#44;&#34;mbg1&#34;:&#34;&#34;&#44;&#34;mfg1&#34;:&#34;&#34;&#44;&#34;mhg1&#34;:&#34;&#34;&#44;&#34;mbtn2&#34;:&#34;&#34;&#44;&#34;murl2&#34;:&#34;&#34;&#44;&#34;mbg2&#34;:&#34;&#34;&#44;&#34;mfg2&#34;:&#34;&#34;&#44;&#34;mhg2&#34;:&#34;&#34;&#44;&#34;mbtn3&#34;:&#34;&#34;&#44;&#34;murl3&#34;:&#34;&#34;&#44;&#34;mbg3&#34;:&#34;&#34;&#44;&#34;mfg3&#34;:&#34;&#34;&#44;&#34;mhg3&#34;:&#34;&#34;&#44;&#34;mbtn4&#34;:&#34;&#34;&#44;&#34;murl4&#34;:&#34;&#34;&#44;&#34;mbg4&#34;:&#34;&#34;&#44;&#34;mfg4&#34;:&#34;&#34;&#44;&#34;mhg4&#34;:&#34;&#34;&#44;&#34;mbtn5&#34;:&#34;&#34;&#44;&#34;murl5&#34;:&#34;&#34;&#44;&#34;mbg5&#34;:&#34;&#34;&#44;&#34;mfg5&#34;:&#34;&#34;&#44;&#34;mhg5&#34;:&#34;&#34;}]}]&#44;&#34;htty&#34;:&#34;px&#34;&#44;&#34;bg&#34;:&#34;&#34;&#44;&#34;ext&#34;:false&#44;&#34;tile&#34;:false&#44;&#34;ancimg&#34;:false&#44;&#34;txt&#34;:&#34;&#34;&#44;&#34;lbl&#34;:&#34;question1&#34;&#44;&#34;ht&#34;:0}]}', ".$qs["version"]."='1', ".$qs["verstatus"]."='ACTIVE' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='Yes', ".$qs["name"]."='Page: engage_jsf3', ".$qs["value"]."='{&#34;rows&#34;:[{&#34;type&#34;:&#34;sequential&#34;&#44;&#34;pad&#34;:0&#44;&#34;slots&#34;:[{&#34;wd&#34;:&#34;25&#34;&#44;&#34;layers&#34;:[{&#34;type&#34;:&#34;HTML&#34;&#44;&#34;wd&#34;:&#34;100&#34;&#44;&#34;left&#34;:&#34;0&#34;&#44;&#34;top&#34;:&#34;0&#34;&#44;&#34;trx&#34;:&#34;&#34;&#44;&#34;fsz&#34;:null&#44;&#34;pad&#34;:15&#44;&#34;max&#34;:250&#44;&#34;ffm&#34;:&#34;&#34;&#44;&#34;aln&#34;:false&#44;&#34;bld&#34;:false&#44;&#34;dep&#34;:&#34;no shadow&#34;&#44;&#34;hide&#34;:false&#44;&#34;clr&#34;:&#34;&#34;&#44;&#34;bg&#34;:&#34;&#34;&#44;&#34;content&#34;:&#34;&lt;div id=#jsfquote#ih_filters#jsfquote#&gt;#jsflf#&lt;/div&gt;&#34;&#44;&#34;imgdsp&#34;:&#34;full&#34;&#44;&#34;url&#34;:&#34;&#34;&#44;&#34;onclick&#34;:&#34;&#34;&#44;&#34;mttl&#34;:&#34;&#34;&#44;&#34;msubttl&#34;:&#34;&#34;&#44;&#34;mside&#34;:&#34;center&#34;&#44;&#34;mstyle&#34;:&#34;none&#34;&#44;&#34;mpad&#34;:&#34;&#34;&#44;&#34;mbtn1&#34;:&#34;&#34;&#44;&#34;murl1&#34;:&#34;&#34;&#44;&#34;mbg1&#34;:&#34;&#34;&#44;&#34;mfg1&#34;:&#34;&#34;&#44;&#34;mhg1&#34;:&#34;&#34;&#44;&#34;mbtn2&#34;:&#34;&#34;&#44;&#34;murl2&#34;:&#34;&#34;&#44;&#34;mbg2&#34;:&#34;&#34;&#44;&#34;mfg2&#34;:&#34;&#34;&#44;&#34;mhg2&#34;:&#34;&#34;&#44;&#34;mbtn3&#34;:&#34;&#34;&#44;&#34;murl3&#34;:&#34;&#34;&#44;&#34;mbg3&#34;:&#34;&#34;&#44;&#34;mfg3&#34;:&#34;&#34;&#44;&#34;mhg3&#34;:&#34;&#34;&#44;&#34;mbtn4&#34;:&#34;&#34;&#44;&#34;murl4&#34;:&#34;&#34;&#44;&#34;mbg4&#34;:&#34;&#34;&#44;&#34;mfg4&#34;:&#34;&#34;&#44;&#34;mhg4&#34;:&#34;&#34;&#44;&#34;mbtn5&#34;:&#34;&#34;&#44;&#34;murl5&#34;:&#34;&#34;&#44;&#34;mbg5&#34;:&#34;&#34;&#44;&#34;mfg5&#34;:&#34;&#34;&#44;&#34;mhg5&#34;:&#34;&#34;}]&#44;&#34;type&#34;:&#34;browsertabletmobile&#34;}&#44;{&#34;wd&#34;:&#34;75&#34;&#44;&#34;layers&#34;:[{&#34;type&#34;:&#34;HTML&#34;&#44;&#34;wd&#34;:&#34;100&#34;&#44;&#34;left&#34;:&#34;0&#34;&#44;&#34;top&#34;:&#34;0&#34;&#44;&#34;trx&#34;:&#34;&#34;&#44;&#34;fsz&#34;:null&#44;&#34;pad&#34;:15&#44;&#34;max&#34;:null&#44;&#34;ffm&#34;:&#34;&#34;&#44;&#34;aln&#34;:false&#44;&#34;bld&#34;:false&#44;&#34;dep&#34;:&#34;no shadow&#34;&#44;&#34;hide&#34;:false&#44;&#34;clr&#34;:&#34;&#34;&#44;&#34;bg&#34;:&#34;&#34;&#44;&#34;content&#34;:&#34;&lt;div id=#jsfquote#ih_paging#jsfquote#&gt;#jsflf#&lt;/div&gt;#jsflf##jsflf#&lt;div id=#jsfquote#ih_breadcrumb#jsfquote#&gt;#jsflf#&lt;/div&gt;#jsflf##jsflf#&lt;div id=#jsfquote#ih_results#jsfquote#&gt;#jsflf#&lt;/div&gt;&#34;&#44;&#34;imgdsp&#34;:&#34;full&#34;&#44;&#34;url&#34;:&#34;&#34;&#44;&#34;onclick&#34;:&#34;&#34;&#44;&#34;mttl&#34;:&#34;&#34;&#44;&#34;msubttl&#34;:&#34;&#34;&#44;&#34;mside&#34;:&#34;center&#34;&#44;&#34;mstyle&#34;:&#34;none&#34;&#44;&#34;mpad&#34;:&#34;&#34;&#44;&#34;mbtn1&#34;:&#34;&#34;&#44;&#34;murl1&#34;:&#34;&#34;&#44;&#34;mbg1&#34;:&#34;&#34;&#44;&#34;mfg1&#34;:&#34;&#34;&#44;&#34;mhg1&#34;:&#34;&#34;&#44;&#34;mbtn2&#34;:&#34;&#34;&#44;&#34;murl2&#34;:&#34;&#34;&#44;&#34;mbg2&#34;:&#34;&#34;&#44;&#34;mfg2&#34;:&#34;&#34;&#44;&#34;mhg2&#34;:&#34;&#34;&#44;&#34;mbtn3&#34;:&#34;&#34;&#44;&#34;murl3&#34;:&#34;&#34;&#44;&#34;mbg3&#34;:&#34;&#34;&#44;&#34;mfg3&#34;:&#34;&#34;&#44;&#34;mhg3&#34;:&#34;&#34;&#44;&#34;mbtn4&#34;:&#34;&#34;&#44;&#34;murl4&#34;:&#34;&#34;&#44;&#34;mbg4&#34;:&#34;&#34;&#44;&#34;mfg4&#34;:&#34;&#34;&#44;&#34;mhg4&#34;:&#34;&#34;&#44;&#34;mbtn5&#34;:&#34;&#34;&#44;&#34;murl5&#34;:&#34;&#34;&#44;&#34;mbg5&#34;:&#34;&#34;&#44;&#34;mfg5&#34;:&#34;&#34;&#44;&#34;mhg5&#34;:&#34;&#34;}]&#44;&#34;type&#34;:&#34;browsertabletmobile&#34;}]&#44;&#34;htty&#34;:&#34;px&#34;&#44;&#34;bg&#34;:&#34;&#34;&#44;&#34;ext&#34;:false&#44;&#34;tile&#34;:false&#44;&#34;ancimg&#34;:false&#44;&#34;txt&#34;:&#34;&#34;&#44;&#34;lbl&#34;:&#34;body&#34;&#44;&#34;ht&#34;:0}]}', ".$qs["version"]."='1', ".$qs["verstatus"]."='ACTIVE' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='Yes', ".$qs["name"]."='Page: engage_jsf4', ".$qs["value"]."='{&#34;rows&#34;:[{&#34;type&#34;:&#34;sequential&#34;&#44;&#34;pad&#34;:0&#44;&#34;slots&#34;:[{&#34;wd&#34;:100&#44;&#34;layers&#34;:[{&#34;type&#34;:&#34;HTML&#34;&#44;&#34;wd&#34;:&#34;100&#34;&#44;&#34;left&#34;:&#34;0&#34;&#44;&#34;top&#34;:&#34;0&#34;&#44;&#34;trx&#34;:&#34;&#34;&#44;&#34;fsz&#34;:null&#44;&#34;pad&#34;:null&#44;&#34;max&#34;:null&#44;&#34;ffm&#34;:&#34;&#34;&#44;&#34;aln&#34;:false&#44;&#34;bld&#34;:false&#44;&#34;dep&#34;:&#34;no shadow&#34;&#44;&#34;hide&#34;:false&#44;&#34;clr&#34;:&#34;&#34;&#44;&#34;bg&#34;:&#34;&#34;&#44;&#34;content&#34;:&#34;&lt;script&gt;#jsflf#var unerlinemenulink_cnt = 0;#jsflf#function underlinemenulink() {#jsflf# &nbsp;if(jQuery(#jsfsquote##toplink_engage#jsfsquote#).length&gt;0 || unerlinemenulink_cnt&gt;10){#jsflf# &nbsp; &nbsp;jQuery(#jsfsquote##toplink_engage#jsfsquote#).css(#jsfsquote#border-bottom#jsfsquote#&#44;#jsfsquote#2px solid #232323#jsfsquote#);#jsflf# &nbsp;} else {#jsflf# &nbsp; &nbsp;setTimeout(underlinemenulink&#44;600);#jsflf# &nbsp; &nbsp;unerlinemenulink_cnt++;#jsflf# &nbsp;}#jsflf#}#jsflf##jsflf#underlinemenulink();#jsflf#ih_getfilters();#jsflf#ih_showresults();#jsflf#&lt;/script&gt;&#34;&#44;&#34;imgdsp&#34;:&#34;full&#34;&#44;&#34;url&#34;:&#34;&#34;&#44;&#34;onclick&#34;:&#34;&#34;&#44;&#34;mttl&#34;:&#34;&#34;&#44;&#34;msubttl&#34;:&#34;&#34;&#44;&#34;mside&#34;:&#34;center&#34;&#44;&#34;mstyle&#34;:&#34;none&#34;&#44;&#34;mpad&#34;:&#34;&#34;&#44;&#34;mbtn1&#34;:&#34;&#34;&#44;&#34;murl1&#34;:&#34;&#34;&#44;&#34;mbg1&#34;:&#34;&#34;&#44;&#34;mfg1&#34;:&#34;&#34;&#44;&#34;mhg1&#34;:&#34;&#34;&#44;&#34;mbtn2&#34;:&#34;&#34;&#44;&#34;murl2&#34;:&#34;&#34;&#44;&#34;mbg2&#34;:&#34;&#34;&#44;&#34;mfg2&#34;:&#34;&#34;&#44;&#34;mhg2&#34;:&#34;&#34;&#44;&#34;mbtn3&#34;:&#34;&#34;&#44;&#34;murl3&#34;:&#34;&#34;&#44;&#34;mbg3&#34;:&#34;&#34;&#44;&#34;mfg3&#34;:&#34;&#34;&#44;&#34;mhg3&#34;:&#34;&#34;&#44;&#34;mbtn4&#34;:&#34;&#34;&#44;&#34;murl4&#34;:&#34;&#34;&#44;&#34;mbg4&#34;:&#34;&#34;&#44;&#34;mfg4&#34;:&#34;&#34;&#44;&#34;mhg4&#34;:&#34;&#34;&#44;&#34;mbtn5&#34;:&#34;&#34;&#44;&#34;murl5&#34;:&#34;&#34;&#44;&#34;mbg5&#34;:&#34;&#34;&#44;&#34;mfg5&#34;:&#34;&#34;&#44;&#34;mhg5&#34;:&#34;&#34;}]}]&#44;&#34;htty&#34;:&#34;px&#34;&#44;&#34;bg&#34;:&#34;&#34;&#44;&#34;ext&#34;:false&#44;&#34;tile&#34;:false&#44;&#34;ancimg&#34;:false&#44;&#34;txt&#34;:&#34;&#34;&#44;&#34;lbl&#34;:&#34;js code&#34;&#44;&#34;ht&#34;:0}]}', ".$qs["version"]."='1', ".$qs["verstatus"]."='ACTIVE' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='Yes', ".$qs["name"]."='Page: adminhome', ".$qs["value"]."='{&#34;rowcount&#34;:1&#44;&#34;rows&#34;:[]}', ".$qs["version"]."='1', ".$qs["verstatus"]."='ACTIVE' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='YES', ".$qs["name"]."='Page: adminhome_jsflock', ".$qs["value"]."='1', ".$qs["version"]."='1', ".$qs["verstatus"]."='ACTIVE' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='Yes', ".$qs["name"]."='Page: adminhome_jsf1', ".$qs["value"]."='{&#34;rows&#34;:[{&#34;type&#34;:&#34;sequential&#34;&#44;&#34;pad&#34;:0&#44;&#34;slots&#34;:[{&#34;wd&#34;:100&#44;&#34;layers&#34;:[{&#34;type&#34;:&#34;Visual Builder&#34;&#44;&#34;wd&#34;:&#34;100&#34;&#44;&#34;left&#34;:&#34;0&#34;&#44;&#34;top&#34;:&#34;0&#34;&#44;&#34;trx&#34;:&#34;&#34;&#44;&#34;fsz&#34;:null&#44;&#34;pad&#34;:20&#44;&#34;max&#34;:null&#44;&#34;ffm&#34;:&#34;&#34;&#44;&#34;aln&#34;:false&#44;&#34;bld&#34;:false&#44;&#34;dep&#34;:&#34;no shadow&#34;&#44;&#34;hide&#34;:false&#44;&#34;clr&#34;:&#34;&#34;&#44;&#34;bg&#34;:&#34;&#34;&#44;&#34;content&#34;:&#34;adminhome&#34;&#44;&#34;imgdsp&#34;:&#34;full&#34;&#44;&#34;url&#34;:&#34;&#34;&#44;&#34;onclick&#34;:&#34;&#34;&#44;&#34;mttl&#34;:&#34;&#34;&#44;&#34;msubttl&#34;:&#34;&#34;&#44;&#34;mside&#34;:&#34;center&#34;&#44;&#34;mstyle&#34;:&#34;none&#34;&#44;&#34;mpad&#34;:&#34;&#34;&#44;&#34;mbtn1&#34;:&#34;&#34;&#44;&#34;murl1&#34;:&#34;&#34;&#44;&#34;mbg1&#34;:&#34;&#34;&#44;&#34;mfg1&#34;:&#34;&#34;&#44;&#34;mhg1&#34;:&#34;&#34;&#44;&#34;mbtn2&#34;:&#34;&#34;&#44;&#34;murl2&#34;:&#34;&#34;&#44;&#34;mbg2&#34;:&#34;&#34;&#44;&#34;mfg2&#34;:&#34;&#34;&#44;&#34;mhg2&#34;:&#34;&#34;&#44;&#34;mbtn3&#34;:&#34;&#34;&#44;&#34;murl3&#34;:&#34;&#34;&#44;&#34;mbg3&#34;:&#34;&#34;&#44;&#34;mfg3&#34;:&#34;&#34;&#44;&#34;mhg3&#34;:&#34;&#34;&#44;&#34;mbtn4&#34;:&#34;&#34;&#44;&#34;murl4&#34;:&#34;&#34;&#44;&#34;mbg4&#34;:&#34;&#34;&#44;&#34;mfg4&#34;:&#34;&#34;&#44;&#34;mhg4&#34;:&#34;&#34;&#44;&#34;mbtn5&#34;:&#34;&#34;&#44;&#34;murl5&#34;:&#34;&#34;&#44;&#34;mbg5&#34;:&#34;&#34;&#44;&#34;mfg5&#34;:&#34;&#34;&#44;&#34;mhg5&#34;:&#34;&#34;}]}]&#44;&#34;htty&#34;:&#34;px&#34;}]}', ".$qs["version"]."='1', ".$qs["verstatus"]."='ACTIVE' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='yes', ".$qs["name"]."='Visual: adminhome', ".$qs["value"]."='{&#34;divname&#34;:&#34;container&#34;&#44;&#34;lf&#34;:2029&#44;&#34;tp&#34;:1749&#44;&#34;wd&#34;:9640&#44;&#34;ht&#34;:4927&#44;&#34;rad&#34;:0&#44;&#34;zindex&#34;:&#34;1&#34;&#44;&#34;fsz&#34;:187&#44;&#34;fclr&#34;:&#34;#000000&#34;&#44;&#34;ffam&#34;:&#34;&#34;&#44;&#34;classname&#34;:&#34;&#34;&#44;&#34;fbld&#34;:&#34;0&#34;&#44;&#34;rqd&#34;:&#34;0&#34;&#44;&#34;tabi&#34;:&#34;1&#34;&#44;&#34;fund&#34;:&#34;0&#34;&#44;&#34;fitl&#34;:&#34;0&#34;&#44;&#34;faln&#34;:&#34;left&#34;&#44;&#34;bgclr&#34;:&#34;&#34;&#44;&#34;opacity&#34;:&#34;&#34;&#44;&#34;hide&#34;:&#34;0&#34;&#44;&#34;onclick&#34;:&#34;&#34;&#44;&#34;type&#34;:&#34;&#34;&#44;&#34;wd_id&#34;:&#34;&#34;&#44;&#34;section&#34;:&#34;&#34;&#44;&#34;field_id&#34;:&#34;&#34;&#44;&#34;wdtype&#34;:&#34;display&#34;&#44;&#34;txt&#34;:&#34;&#34;&#44;&#34;img&#34;:&#34;&#34;&#44;&#34;fin&#34;:&#34;&#34;&#44;&#34;fout&#34;:&#34;&#34;&#44;&#34;move&#34;:&#34;&#34;&#44;&#34;parent&#34;:&#34;&#34;}' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='yes', ".$qs["name"]."='Visual: adminhome_20200304backup', ".$qs["value"]."='{&#34;divname&#34;:&#34;container&#34;&#44;&#34;lf&#34;:2029&#44;&#34;tp&#34;:1749&#44;&#34;wd&#34;:6248&#44;&#34;ht&#34;:6008&#44;&#34;rad&#34;:0&#44;&#34;zindex&#34;:&#34;1&#34;&#44;&#34;fsz&#34;:187&#44;&#34;fclr&#34;:&#34;#000000&#34;&#44;&#34;ffam&#34;:&#34;&#34;&#44;&#34;classname&#34;:&#34;&#34;&#44;&#34;fbld&#34;:&#34;0&#34;&#44;&#34;rqd&#34;:&#34;0&#34;&#44;&#34;tabi&#34;:&#34;1&#34;&#44;&#34;fund&#34;:&#34;0&#34;&#44;&#34;fitl&#34;:&#34;0&#34;&#44;&#34;faln&#34;:&#34;left&#34;&#44;&#34;bgclr&#34;:&#34;&#34;&#44;&#34;opacity&#34;:&#34;&#34;&#44;&#34;hide&#34;:&#34;0&#34;&#44;&#34;onclick&#34;:&#34;&#34;&#44;&#34;type&#34;:&#34;&#34;&#44;&#34;wd_id&#34;:&#34;&#34;&#44;&#34;section&#34;:&#34;&#34;&#44;&#34;field_id&#34;:&#34;&#34;&#44;&#34;wdtype&#34;:&#34;display&#34;&#44;&#34;txt&#34;:&#34;&#34;&#44;&#34;img&#34;:&#34;&#34;&#44;&#34;fin&#34;:&#34;&#34;&#44;&#34;fout&#34;:&#34;&#34;&#44;&#34;move&#34;:&#34;&#34;&#44;&#34;parent&#34;:&#34;&#34;}' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='yes', ".$qs["name"]."='Visual: adminhome', ".$qs["value"]."='{&#34;divname&#34;:&#34;title&#34;&#44;&#34;lf&#34;:67&#44;&#34;tp&#34;:120&#44;&#34;wd&#34;:5968&#44;&#34;ht&#34;:374&#44;&#34;rad&#34;:0&#44;&#34;zindex&#34;:&#34;1&#34;&#44;&#34;fsz&#34;:240&#44;&#34;fclr&#34;:&#34;#000000&#34;&#44;&#34;ffam&#34;:&#34;&#34;&#44;&#34;classname&#34;:&#34;&#34;&#44;&#34;fbld&#34;:&#34;1&#34;&#44;&#34;rqd&#34;:&#34;0&#34;&#44;&#34;tabi&#34;:&#34;1&#34;&#44;&#34;fund&#34;:&#34;0&#34;&#44;&#34;fitl&#34;:&#34;0&#34;&#44;&#34;faln&#34;:&#34;left&#34;&#44;&#34;bgclr&#34;:&#34;&#34;&#44;&#34;opacity&#34;:&#34;&#34;&#44;&#34;hide&#34;:&#34;0&#34;&#44;&#34;onclick&#34;:&#34;&#34;&#44;&#34;type&#34;:&#34;&#34;&#44;&#34;wd_id&#34;:&#34;&#34;&#44;&#34;section&#34;:&#34;&#34;&#44;&#34;field_id&#34;:&#34;&#34;&#44;&#34;wdtype&#34;:&#34;display&#34;&#44;&#34;txt&#34;:&#34;Innovation Hub Administration&#34;&#44;&#34;img&#34;:&#34;&#34;&#44;&#34;fin&#34;:&#34;&#34;&#44;&#34;fout&#34;:&#34;&#34;&#44;&#34;move&#34;:&#34;&#34;&#44;&#34;parent&#34;:&#34;20&#34;}' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='yes', ".$qs["name"]."='Visual: adminhome_20200304backup', ".$qs["value"]."='{&#34;divname&#34;:&#34;container&#34;&#44;&#34;lf&#34;:2029&#44;&#34;tp&#34;:1749&#44;&#34;wd&#34;:6248&#44;&#34;ht&#34;:6008&#44;&#34;rad&#34;:0&#44;&#34;zindex&#34;:&#34;1&#34;&#44;&#34;fsz&#34;:187&#44;&#34;fclr&#34;:&#34;#000000&#34;&#44;&#34;ffam&#34;:&#34;&#34;&#44;&#34;classname&#34;:&#34;&#34;&#44;&#34;fbld&#34;:&#34;0&#34;&#44;&#34;rqd&#34;:&#34;0&#34;&#44;&#34;tabi&#34;:&#34;1&#34;&#44;&#34;fund&#34;:&#34;0&#34;&#44;&#34;fitl&#34;:&#34;0&#34;&#44;&#34;faln&#34;:&#34;left&#34;&#44;&#34;bgclr&#34;:&#34;&#34;&#44;&#34;opacity&#34;:&#34;&#34;&#44;&#34;hide&#34;:&#34;0&#34;&#44;&#34;onclick&#34;:&#34;&#34;&#44;&#34;type&#34;:&#34;&#34;&#44;&#34;wd_id&#34;:&#34;&#34;&#44;&#34;section&#34;:&#34;&#34;&#44;&#34;field_id&#34;:&#34;&#34;&#44;&#34;wdtype&#34;:&#34;display&#34;&#44;&#34;txt&#34;:&#34;&#34;&#44;&#34;img&#34;:&#34;&#34;&#44;&#34;fin&#34;:&#34;&#34;&#44;&#34;fout&#34;:&#34;&#34;&#44;&#34;move&#34;:&#34;&#34;&#44;&#34;parent&#34;:&#34;&#34;}' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='yes', ".$qs["name"]."='Visual: adminhome_20200304backup', ".$qs["value"]."='{&#34;divname&#34;:&#34;Layer 2&#34;&#44;&#34;lf&#34;:2937&#44;&#34;tp&#34;:881&#44;&#34;wd&#34;:3284&#44;&#34;ht&#34;:721&#44;&#34;rad&#34;:0&#44;&#34;zindex&#34;:&#34;1&#34;&#44;&#34;fsz&#34;:187&#44;&#34;fclr&#34;:&#34;#000000&#34;&#44;&#34;ffam&#34;:&#34;&#34;&#44;&#34;classname&#34;:&#34;&#34;&#44;&#34;fbld&#34;:&#34;0&#34;&#44;&#34;rqd&#34;:&#34;0&#34;&#44;&#34;tabi&#34;:&#34;1&#34;&#44;&#34;fund&#34;:&#34;0&#34;&#44;&#34;fitl&#34;:&#34;0&#34;&#44;&#34;faln&#34;:&#34;left&#34;&#44;&#34;bgclr&#34;:&#34;&#34;&#44;&#34;opacity&#34;:&#34;&#34;&#44;&#34;hide&#34;:&#34;0&#34;&#44;&#34;onclick&#34;:&#34;&#34;&#44;&#34;type&#34;:&#34;&#34;&#44;&#34;wd_id&#34;:&#34;&#34;&#44;&#34;section&#34;:&#34;&#34;&#44;&#34;field_id&#34;:&#34;&#34;&#44;&#34;wdtype&#34;:&#34;display&#34;&#44;&#34;txt&#34;:&#34;&#34;&#44;&#34;img&#34;:&#34;&#34;&#44;&#34;fin&#34;:&#34;&#34;&#44;&#34;fout&#34;:&#34;&#34;&#44;&#34;move&#34;:&#34;&#34;&#44;&#34;parent&#34;:&#34;&#34;}' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='yes', ".$qs["name"]."='Visual: adminhome', ".$qs["value"]."='{&#34;divname&#34;:&#34;Layer 3&#34;&#44;&#34;lf&#34;:53&#44;&#34;tp&#34;:454&#44;&#34;wd&#34;:9693&#44;&#34;ht&#34;:1041&#44;&#34;rad&#34;:0&#44;&#34;zindex&#34;:&#34;1&#34;&#44;&#34;fsz&#34;:187&#44;&#34;fclr&#34;:&#34;#000000&#34;&#44;&#34;ffam&#34;:&#34;&#34;&#44;&#34;classname&#34;:&#34;&#34;&#44;&#34;fbld&#34;:&#34;0&#34;&#44;&#34;rqd&#34;:&#34;0&#34;&#44;&#34;tabi&#34;:&#34;1&#34;&#44;&#34;fund&#34;:&#34;0&#34;&#44;&#34;fitl&#34;:&#34;0&#34;&#44;&#34;faln&#34;:&#34;left&#34;&#44;&#34;bgclr&#34;:&#34;&#34;&#44;&#34;opacity&#34;:&#34;&#34;&#44;&#34;hide&#34;:&#34;0&#34;&#44;&#34;onclick&#34;:&#34;&#34;&#44;&#34;type&#34;:&#34;&#34;&#44;&#34;wd_id&#34;:&#34;&#34;&#44;&#34;section&#34;:&#34;&#34;&#44;&#34;field_id&#34;:&#34;&#34;&#44;&#34;wdtype&#34;:&#34;display&#34;&#44;&#34;txt&#34;:&#34;Use this tool to create new records and facets for the Innovation HUB engagement page located at:#jsflf#http://ibminnovationhub.com/view/engage#jsflf##jsflf#Brief description of the tabs above:#jsflf#&#34;&#44;&#34;img&#34;:&#34;&#34;&#44;&#34;fin&#34;:&#34;&#34;&#44;&#34;fout&#34;:&#34;&#34;&#44;&#34;move&#34;:&#34;&#34;&#44;&#34;parent&#34;:&#34;20&#34;}' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='yes', ".$qs["name"]."='Visual: adminhome', ".$qs["value"]."='{&#34;divname&#34;:&#34;Layer 4&#34;&#44;&#34;lf&#34;:174&#44;&#34;tp&#34;:1509&#44;&#34;wd&#34;:4166&#44;&#34;ht&#34;:294&#44;&#34;rad&#34;:&#34;0&#34;&#44;&#34;zindex&#34;:&#34;1&#34;&#44;&#34;fsz&#34;:187&#44;&#34;fclr&#34;:&#34;#000000&#34;&#44;&#34;ffam&#34;:&#34;&#34;&#44;&#34;classname&#34;:&#34;&#34;&#44;&#34;fbld&#34;:&#34;0&#34;&#44;&#34;rqd&#34;:&#34;0&#34;&#44;&#34;tabi&#34;:&#34;1&#34;&#44;&#34;fund&#34;:&#34;0&#34;&#44;&#34;fitl&#34;:&#34;0&#34;&#44;&#34;faln&#34;:&#34;left&#34;&#44;&#34;bgclr&#34;:&#34;&#34;&#44;&#34;opacity&#34;:&#34;&#34;&#44;&#34;hide&#34;:&#34;0&#34;&#44;&#34;onclick&#34;:&#34;&#34;&#44;&#34;type&#34;:&#34;&#34;&#44;&#34;wd_id&#34;:&#34;&#34;&#44;&#34;section&#34;:&#34;&#34;&#44;&#34;field_id&#34;:&#34;&#34;&#44;&#34;wdtype&#34;:&#34;display&#34;&#44;&#34;txt&#34;:&#34;#jsfbullet# PROGRAMS TAB&#34;&#44;&#34;img&#34;:&#34;&#34;&#44;&#34;fin&#34;:&#34;&#34;&#44;&#34;fout&#34;:&#34;&#34;&#44;&#34;move&#34;:&#34;&#34;&#44;&#34;parent&#34;:&#34;20&#34;}' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='yes', ".$qs["name"]."='Visual: adminhome', ".$qs["value"]."='{&#34;divname&#34;:&#34;Layer 3&#34;&#44;&#34;lf&#34;:280&#44;&#34;tp&#34;:1776&#44;&#34;wd&#34;:8678&#44;&#34;ht&#34;:494&#44;&#34;rad&#34;:0&#44;&#34;zindex&#34;:&#34;1&#34;&#44;&#34;fsz&#34;:187&#44;&#34;fclr&#34;:&#34;#000000&#34;&#44;&#34;ffam&#34;:&#34;&#34;&#44;&#34;classname&#34;:&#34;&#34;&#44;&#34;fbld&#34;:&#34;0&#34;&#44;&#34;rqd&#34;:&#34;0&#34;&#44;&#34;tabi&#34;:&#34;1&#34;&#44;&#34;fund&#34;:&#34;0&#34;&#44;&#34;fitl&#34;:&#34;0&#34;&#44;&#34;faln&#34;:&#34;left&#34;&#44;&#34;bgclr&#34;:&#34;&#34;&#44;&#34;opacity&#34;:&#34;&#34;&#44;&#34;hide&#34;:&#34;0&#34;&#44;&#34;onclick&#34;:&#34;&#34;&#44;&#34;type&#34;:&#34;&#34;&#44;&#34;wd_id&#34;:&#34;&#34;&#44;&#34;section&#34;:&#34;&#34;&#44;&#34;field_id&#34;:&#34;&#34;&#44;&#34;wdtype&#34;:&#34;display&#34;&#44;&#34;txt&#34;:&#34;Create new IBM program details which can be displayed and filtered on the website&#34;&#44;&#34;img&#34;:&#34;&#34;&#44;&#34;fin&#34;:&#34;&#34;&#44;&#34;fout&#34;:&#34;&#34;&#44;&#34;move&#34;:&#34;&#34;&#44;&#34;parent&#34;:&#34;20&#34;}' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='yes', ".$qs["name"]."='Visual: adminhome', ".$qs["value"]."='{&#34;divname&#34;:&#34;Layer 4&#34;&#44;&#34;lf&#34;:174&#44;&#34;tp&#34;:2283&#44;&#34;wd&#34;:4166&#44;&#34;ht&#34;:294&#44;&#34;rad&#34;:0&#44;&#34;zindex&#34;:&#34;1&#34;&#44;&#34;fsz&#34;:187&#44;&#34;fclr&#34;:&#34;#000000&#34;&#44;&#34;ffam&#34;:&#34;&#34;&#44;&#34;classname&#34;:&#34;&#34;&#44;&#34;fbld&#34;:&#34;0&#34;&#44;&#34;rqd&#34;:&#34;0&#34;&#44;&#34;tabi&#34;:&#34;1&#34;&#44;&#34;fund&#34;:&#34;0&#34;&#44;&#34;fitl&#34;:&#34;0&#34;&#44;&#34;faln&#34;:&#34;left&#34;&#44;&#34;bgclr&#34;:&#34;&#34;&#44;&#34;opacity&#34;:&#34;&#34;&#44;&#34;hide&#34;:&#34;0&#34;&#44;&#34;onclick&#34;:&#34;&#34;&#44;&#34;type&#34;:&#34;&#34;&#44;&#34;wd_id&#34;:&#34;&#34;&#44;&#34;section&#34;:&#34;&#34;&#44;&#34;field_id&#34;:&#34;&#34;&#44;&#34;wdtype&#34;:&#34;display&#34;&#44;&#34;txt&#34;:&#34;#jsfbullet# REQUEST TYPE&#34;&#44;&#34;img&#34;:&#34;&#34;&#44;&#34;fin&#34;:&#34;&#34;&#44;&#34;fout&#34;:&#34;&#34;&#44;&#34;move&#34;:&#34;&#34;&#44;&#34;parent&#34;:&#34;20&#34;}' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='yes', ".$qs["name"]."='Visual: adminhome', ".$qs["value"]."='{&#34;divname&#34;:&#34;Layer 3&#34;&#44;&#34;lf&#34;:280&#44;&#34;tp&#34;:2577&#44;&#34;wd&#34;:9439&#44;&#34;ht&#34;:494&#44;&#34;rad&#34;:0&#44;&#34;zindex&#34;:&#34;1&#34;&#44;&#34;fsz&#34;:187&#44;&#34;fclr&#34;:&#34;#000000&#34;&#44;&#34;ffam&#34;:&#34;&#34;&#44;&#34;classname&#34;:&#34;&#34;&#44;&#34;fbld&#34;:&#34;0&#34;&#44;&#34;rqd&#34;:&#34;0&#34;&#44;&#34;tabi&#34;:&#34;1&#34;&#44;&#34;fund&#34;:&#34;0&#34;&#44;&#34;fitl&#34;:&#34;0&#34;&#44;&#34;faln&#34;:&#34;left&#34;&#44;&#34;bgclr&#34;:&#34;&#34;&#44;&#34;opacity&#34;:&#34;&#34;&#44;&#34;hide&#34;:&#34;0&#34;&#44;&#34;onclick&#34;:&#34;&#34;&#44;&#34;type&#34;:&#34;&#34;&#44;&#34;wd_id&#34;:&#34;&#34;&#44;&#34;section&#34;:&#34;&#34;&#44;&#34;field_id&#34;:&#34;&#34;&#44;&#34;wdtype&#34;:&#34;display&#34;&#44;&#34;txt&#34;:&#34;Add/Remove/Update options to the Types of Request to the program filtering (cross-cloud&#44; cross-brand&#44; etc) &#34;&#44;&#34;img&#34;:&#34;&#34;&#44;&#34;fin&#34;:&#34;&#34;&#44;&#34;fout&#34;:&#34;&#34;&#44;&#34;move&#34;:&#34;&#34;&#44;&#34;parent&#34;:&#34;20&#34;}' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='yes', ".$qs["name"]."='Visual: adminhome', ".$qs["value"]."='{&#34;divname&#34;:&#34;Layer 4&#34;&#44;&#34;lf&#34;:200&#44;&#34;tp&#34;:3071&#44;&#34;wd&#34;:4166&#44;&#34;ht&#34;:294&#44;&#34;rad&#34;:0&#44;&#34;zindex&#34;:&#34;1&#34;&#44;&#34;fsz&#34;:187&#44;&#34;fclr&#34;:&#34;#000000&#34;&#44;&#34;ffam&#34;:&#34;&#34;&#44;&#34;classname&#34;:&#34;&#34;&#44;&#34;fbld&#34;:&#34;0&#34;&#44;&#34;rqd&#34;:&#34;0&#34;&#44;&#34;tabi&#34;:&#34;1&#34;&#44;&#34;fund&#34;:&#34;0&#34;&#44;&#34;fitl&#34;:&#34;0&#34;&#44;&#34;faln&#34;:&#34;left&#34;&#44;&#34;bgclr&#34;:&#34;&#34;&#44;&#34;opacity&#34;:&#34;&#34;&#44;&#34;hide&#34;:&#34;0&#34;&#44;&#34;onclick&#34;:&#34;&#34;&#44;&#34;type&#34;:&#34;&#34;&#44;&#34;wd_id&#34;:&#34;&#34;&#44;&#34;section&#34;:&#34;&#34;&#44;&#34;field_id&#34;:&#34;&#34;&#44;&#34;wdtype&#34;:&#34;display&#34;&#44;&#34;txt&#34;:&#34;#jsfbullet# MODELS&#34;&#44;&#34;img&#34;:&#34;&#34;&#44;&#34;fin&#34;:&#34;&#34;&#44;&#34;fout&#34;:&#34;&#34;&#44;&#34;move&#34;:&#34;&#34;&#44;&#34;parent&#34;:&#34;20&#34;}' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='yes', ".$qs["name"]."='Visual: adminhome', ".$qs["value"]."='{&#34;divname&#34;:&#34;Layer 3&#34;&#44;&#34;lf&#34;:267&#44;&#34;tp&#34;:3338&#44;&#34;wd&#34;:9439&#44;&#34;ht&#34;:494&#44;&#34;rad&#34;:0&#44;&#34;zindex&#34;:&#34;1&#34;&#44;&#34;fsz&#34;:187&#44;&#34;fclr&#34;:&#34;#000000&#34;&#44;&#34;ffam&#34;:&#34;&#34;&#44;&#34;classname&#34;:&#34;&#34;&#44;&#34;fbld&#34;:&#34;0&#34;&#44;&#34;rqd&#34;:&#34;0&#34;&#44;&#34;tabi&#34;:&#34;1&#34;&#44;&#34;fund&#34;:&#34;0&#34;&#44;&#34;fitl&#34;:&#34;0&#34;&#44;&#34;faln&#34;:&#34;left&#34;&#44;&#34;bgclr&#34;:&#34;&#34;&#44;&#34;opacity&#34;:&#34;&#34;&#44;&#34;hide&#34;:&#34;0&#34;&#44;&#34;onclick&#34;:&#34;&#34;&#44;&#34;type&#34;:&#34;&#34;&#44;&#34;wd_id&#34;:&#34;&#34;&#44;&#34;section&#34;:&#34;&#34;&#44;&#34;field_id&#34;:&#34;&#34;&#44;&#34;wdtype&#34;:&#34;display&#34;&#44;&#34;txt&#34;:&#34;Add/Remove/Update options for the engagement models to assign to the different programs (funding)&#34;&#44;&#34;img&#34;:&#34;&#34;&#44;&#34;fin&#34;:&#34;&#34;&#44;&#34;fout&#34;:&#34;&#34;&#44;&#34;move&#34;:&#34;&#34;&#44;&#34;parent&#34;:&#34;20&#34;}' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='yes', ".$qs["name"]."='Visual: adminhome', ".$qs["value"]."='{&#34;divname&#34;:&#34;Layer 4&#34;&#44;&#34;lf&#34;:160&#44;&#34;tp&#34;:3858&#44;&#34;wd&#34;:4166&#44;&#34;ht&#34;:294&#44;&#34;rad&#34;:0&#44;&#34;zindex&#34;:&#34;1&#34;&#44;&#34;fsz&#34;:187&#44;&#34;fclr&#34;:&#34;#000000&#34;&#44;&#34;ffam&#34;:&#34;&#34;&#44;&#34;classname&#34;:&#34;&#34;&#44;&#34;fbld&#34;:&#34;0&#34;&#44;&#34;rqd&#34;:&#34;0&#34;&#44;&#34;tabi&#34;:&#34;1&#34;&#44;&#34;fund&#34;:&#34;0&#34;&#44;&#34;fitl&#34;:&#34;0&#34;&#44;&#34;faln&#34;:&#34;left&#34;&#44;&#34;bgclr&#34;:&#34;&#34;&#44;&#34;opacity&#34;:&#34;&#34;&#44;&#34;hide&#34;:&#34;0&#34;&#44;&#34;onclick&#34;:&#34;&#34;&#44;&#34;type&#34;:&#34;&#34;&#44;&#34;wd_id&#34;:&#34;&#34;&#44;&#34;section&#34;:&#34;&#34;&#44;&#34;field_id&#34;:&#34;&#34;&#44;&#34;wdtype&#34;:&#34;display&#34;&#44;&#34;txt&#34;:&#34;#jsfbullet# ROLES&#34;&#44;&#34;img&#34;:&#34;&#34;&#44;&#34;fin&#34;:&#34;&#34;&#44;&#34;fout&#34;:&#34;&#34;&#44;&#34;move&#34;:&#34;&#34;&#44;&#34;parent&#34;:&#34;20&#34;}' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='yes', ".$qs["name"]."='Visual: adminhome', ".$qs["value"]."='{&#34;divname&#34;:&#34;Layer 3&#34;&#44;&#34;lf&#34;:254&#44;&#34;tp&#34;:4139&#44;&#34;wd&#34;:9439&#44;&#34;ht&#34;:494&#44;&#34;rad&#34;:0&#44;&#34;zindex&#34;:&#34;1&#34;&#44;&#34;fsz&#34;:187&#44;&#34;fclr&#34;:&#34;#000000&#34;&#44;&#34;ffam&#34;:&#34;&#34;&#44;&#34;classname&#34;:&#34;&#34;&#44;&#34;fbld&#34;:&#34;0&#34;&#44;&#34;rqd&#34;:&#34;0&#34;&#44;&#34;tabi&#34;:&#34;1&#34;&#44;&#34;fund&#34;:&#34;0&#34;&#44;&#34;fitl&#34;:&#34;0&#34;&#44;&#34;faln&#34;:&#34;left&#34;&#44;&#34;bgclr&#34;:&#34;&#34;&#44;&#34;opacity&#34;:&#34;&#34;&#44;&#34;hide&#34;:&#34;0&#34;&#44;&#34;onclick&#34;:&#34;&#34;&#44;&#34;type&#34;:&#34;&#34;&#44;&#34;wd_id&#34;:&#34;&#34;&#44;&#34;section&#34;:&#34;&#34;&#44;&#34;field_id&#34;:&#34;&#34;&#44;&#34;wdtype&#34;:&#34;display&#34;&#44;&#34;txt&#34;:&#34;Add/Remove/Update options for the roles that can be associated to different programs (Designer&#44; Dev&#44; etc)&#34;&#44;&#34;img&#34;:&#34;&#34;&#44;&#34;fin&#34;:&#34;&#34;&#44;&#34;fout&#34;:&#34;&#34;&#44;&#34;move&#34;:&#34;&#34;&#44;&#34;parent&#34;:&#34;20&#34;}' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='yes', ".$qs["name"]."='Visual: adminhome', ".$qs["value"]."='{&#34;type&#34;:&#34;header&#34;&#44;&#34;infoonly&#34;:&#34;&#34;&#44;&#34;oright&#34;:&#34;750&#34;}' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='yes', ".$qs["name"]."='Visual: clientquestion', ".$qs["value"]."='{&#34;divname&#34;:&#34;container&#34;&#44;&#34;lf&#34;:1360&#44;&#34;tp&#34;:1800&#44;&#34;wd&#34;:7413&#44;&#34;ht&#34;:3440&#44;&#34;rad&#34;:0&#44;&#34;zindex&#34;:&#34;1&#34;&#44;&#34;fsz&#34;:187&#44;&#34;fclr&#34;:&#34;#000000&#34;&#44;&#34;ffam&#34;:&#34;&#34;&#44;&#34;classname&#34;:&#34;&#34;&#44;&#34;fbld&#34;:&#34;0&#34;&#44;&#34;rqd&#34;:&#34;0&#34;&#44;&#34;tabi&#34;:&#34;1&#34;&#44;&#34;fund&#34;:&#34;0&#34;&#44;&#34;fitl&#34;:&#34;0&#34;&#44;&#34;faln&#34;:&#34;left&#34;&#44;&#34;bgclr&#34;:&#34;&#34;&#44;&#34;opacity&#34;:&#34;&#34;&#44;&#34;hide&#34;:&#34;0&#34;&#44;&#34;onclick&#34;:&#34;&#34;&#44;&#34;type&#34;:&#34;&#34;&#44;&#34;wd_id&#34;:&#34;&#34;&#44;&#34;section&#34;:&#34;&#34;&#44;&#34;field_id&#34;:&#34;&#34;&#44;&#34;wdtype&#34;:&#34;display&#34;&#44;&#34;txt&#34;:&#34;&#34;&#44;&#34;img&#34;:&#34;&#34;&#44;&#34;fin&#34;:&#34;&#34;&#44;&#34;fout&#34;:&#34;&#34;&#44;&#34;move&#34;:&#34;&#34;&#44;&#34;parent&#34;:&#34;&#34;}' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='yes', ".$qs["name"]."='Visual: clientquestion_20200304backup', ".$qs["value"]."='{&#34;divname&#34;:&#34;container&#34;&#44;&#34;lf&#34;:1362&#44;&#34;tp&#34;:1802&#44;&#34;wd&#34;:7864&#44;&#34;ht&#34;:4312&#44;&#34;rad&#34;:0&#44;&#34;zindex&#34;:&#34;1&#34;&#44;&#34;fsz&#34;:187&#44;&#34;fclr&#34;:&#34;#000000&#34;&#44;&#34;ffam&#34;:&#34;&#34;&#44;&#34;classname&#34;:&#34;&#34;&#44;&#34;fbld&#34;:&#34;0&#34;&#44;&#34;rqd&#34;:&#34;0&#34;&#44;&#34;tabi&#34;:&#34;1&#34;&#44;&#34;fund&#34;:&#34;0&#34;&#44;&#34;fitl&#34;:&#34;0&#34;&#44;&#34;faln&#34;:&#34;left&#34;&#44;&#34;bgclr&#34;:&#34;&#34;&#44;&#34;opacity&#34;:&#34;&#34;&#44;&#34;hide&#34;:&#34;0&#34;&#44;&#34;onclick&#34;:&#34;&#34;&#44;&#34;type&#34;:&#34;&#34;&#44;&#34;wd_id&#34;:&#34;&#34;&#44;&#34;section&#34;:&#34;&#34;&#44;&#34;field_id&#34;:&#34;&#34;&#44;&#34;wdtype&#34;:&#34;display&#34;&#44;&#34;txt&#34;:&#34;&#34;&#44;&#34;img&#34;:&#34;&#34;&#44;&#34;fin&#34;:&#34;&#34;&#44;&#34;fout&#34;:&#34;&#34;&#44;&#34;move&#34;:&#34;&#34;&#44;&#34;parent&#34;:&#34;&#34;}' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='yes', ".$qs["name"]."='Visual: clientquestion', ".$qs["value"]."='{&#34;type&#34;:&#34;header&#34;&#44;&#34;infoonly&#34;:&#34;&#34;&#44;&#34;oright&#34;:&#34;750&#34;}' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='yes', ".$qs["name"]."='Visual: clientquestion_20200304backup', ".$qs["value"]."='{&#34;divname&#34;:&#34;container&#34;&#44;&#34;lf&#34;:1362&#44;&#34;tp&#34;:1802&#44;&#34;wd&#34;:9319&#44;&#34;ht&#34;:5434&#44;&#34;rad&#34;:0&#44;&#34;zindex&#34;:&#34;1&#34;&#44;&#34;fsz&#34;:187&#44;&#34;fclr&#34;:&#34;#000000&#34;&#44;&#34;ffam&#34;:&#34;&#34;&#44;&#34;classname&#34;:&#34;&#34;&#44;&#34;fbld&#34;:&#34;0&#34;&#44;&#34;rqd&#34;:&#34;0&#34;&#44;&#34;tabi&#34;:&#34;1&#34;&#44;&#34;fund&#34;:&#34;0&#34;&#44;&#34;fitl&#34;:&#34;0&#34;&#44;&#34;faln&#34;:&#34;left&#34;&#44;&#34;bgclr&#34;:&#34;&#34;&#44;&#34;opacity&#34;:&#34;&#34;&#44;&#34;hide&#34;:&#34;0&#34;&#44;&#34;onclick&#34;:&#34;&#34;&#44;&#34;type&#34;:&#34;&#34;&#44;&#34;wd_id&#34;:&#34;&#34;&#44;&#34;section&#34;:&#34;&#34;&#44;&#34;field_id&#34;:&#34;&#34;&#44;&#34;wdtype&#34;:&#34;display&#34;&#44;&#34;txt&#34;:&#34;&#34;&#44;&#34;img&#34;:&#34;&#34;&#44;&#34;fin&#34;:&#34;&#34;&#44;&#34;fout&#34;:&#34;&#34;&#44;&#34;move&#34;:&#34;&#34;&#44;&#34;parent&#34;:&#34;&#34;}' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='yes', ".$qs["name"]."='Visual: clientquestion_20200304backup', ".$qs["value"]."='{&#34;type&#34;:&#34;header&#34;&#44;&#34;infoonly&#34;:&#34;&#34;&#44;&#34;oright&#34;:&#34;750&#34;}' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='yes', ".$qs["name"]."='Visual: clientquestion', ".$qs["value"]."='{&#34;divname&#34;:&#34;label&#34;&#44;&#34;lf&#34;:547&#44;&#34;tp&#34;:1160&#44;&#34;wd&#34;:1573&#44;&#34;ht&#34;:413&#44;&#34;rad&#34;:0&#44;&#34;zindex&#34;:&#34;1&#34;&#44;&#34;fsz&#34;:213&#44;&#34;fclr&#34;:&#34;#000000&#34;&#44;&#34;ffam&#34;:&#34;&#34;&#44;&#34;classname&#34;:&#34;&#34;&#44;&#34;fbld&#34;:&#34;0&#34;&#44;&#34;rqd&#34;:&#34;0&#34;&#44;&#34;tabi&#34;:&#34;1&#34;&#44;&#34;fund&#34;:&#34;0&#34;&#44;&#34;fitl&#34;:&#34;0&#34;&#44;&#34;faln&#34;:&#34;left&#34;&#44;&#34;bgclr&#34;:&#34;&#34;&#44;&#34;opacity&#34;:&#34;&#34;&#44;&#34;hide&#34;:&#34;0&#34;&#44;&#34;onclick&#34;:&#34;&#34;&#44;&#34;type&#34;:&#34;&#34;&#44;&#34;wd_id&#34;:&#34;&#34;&#44;&#34;section&#34;:&#34;&#34;&#44;&#34;field_id&#34;:&#34;&#34;&#44;&#34;wdtype&#34;:&#34;display&#34;&#44;&#34;txt&#34;:&#34;Your client:&#34;&#44;&#34;img&#34;:&#34;&#34;&#44;&#34;fin&#34;:&#34;&#34;&#44;&#34;fout&#34;:&#34;&#34;&#44;&#34;move&#34;:&#34;&#34;&#44;&#34;parent&#34;:&#34;36&#34;}' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='yes', ".$qs["name"]."='Visual: clientquestion', ".$qs["value"]."='{&#34;divname&#34;:&#34;inpt_clientname&#34;&#44;&#34;lf&#34;:1867&#44;&#34;tp&#34;:1107&#44;&#34;wd&#34;:3667&#44;&#34;ht&#34;:427&#44;&#34;rad&#34;:0&#44;&#34;zindex&#34;:&#34;1&#34;&#44;&#34;fsz&#34;:187&#44;&#34;fclr&#34;:&#34;#000000&#34;&#44;&#34;ffam&#34;:&#34;&#34;&#44;&#34;classname&#34;:&#34;&#34;&#44;&#34;fbld&#34;:&#34;0&#34;&#44;&#34;rqd&#34;:&#34;0&#34;&#44;&#34;tabi&#34;:&#34;1&#34;&#44;&#34;fund&#34;:&#34;0&#34;&#44;&#34;fitl&#34;:&#34;0&#34;&#44;&#34;faln&#34;:&#34;left&#34;&#44;&#34;bgclr&#34;:&#34;&#34;&#44;&#34;opacity&#34;:&#34;&#34;&#44;&#34;hide&#34;:&#34;0&#34;&#44;&#34;onclick&#34;:&#34;&#34;&#44;&#34;type&#34;:&#34;textbox&#34;&#44;&#34;wd_id&#34;:&#34;innovhub programs&#34;&#44;&#34;section&#34;:&#34;&#34;&#44;&#34;field_id&#34;:&#34;&#34;&#44;&#34;wdtype&#34;:&#34;display&#34;&#44;&#34;txt&#34;:&#34;&#34;&#44;&#34;img&#34;:&#34;&#34;&#44;&#34;fin&#34;:&#34;&#34;&#44;&#34;fout&#34;:&#34;&#34;&#44;&#34;move&#34;:&#34;&#34;&#44;&#34;parent&#34;:&#34;36&#34;}' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='yes', ".$qs["name"]."='Visual: clientquestion', ".$qs["value"]."='{&#34;divname&#34;:&#34;Layer 5&#34;&#44;&#34;lf&#34;:533&#44;&#34;tp&#34;:600&#44;&#34;wd&#34;:6613&#44;&#34;ht&#34;:293&#44;&#34;rad&#34;:0&#44;&#34;zindex&#34;:&#34;1&#34;&#44;&#34;fsz&#34;:160&#44;&#34;fclr&#34;:&#34;#000000&#34;&#44;&#34;ffam&#34;:&#34;&#34;&#44;&#34;classname&#34;:&#34;&#34;&#44;&#34;fbld&#34;:&#34;0&#34;&#44;&#34;rqd&#34;:&#34;0&#34;&#44;&#34;tabi&#34;:&#34;1&#34;&#44;&#34;fund&#34;:&#34;0&#34;&#44;&#34;fitl&#34;:&#34;0&#34;&#44;&#34;faln&#34;:&#34;left&#34;&#44;&#34;bgclr&#34;:&#34;&#34;&#44;&#34;opacity&#34;:&#34;&#34;&#44;&#34;hide&#34;:&#34;0&#34;&#44;&#34;onclick&#34;:&#34;&#34;&#44;&#34;type&#34;:&#34;&#34;&#44;&#34;wd_id&#34;:&#34;&#34;&#44;&#34;section&#34;:&#34;&#34;&#44;&#34;field_id&#34;:&#34;&#34;&#44;&#34;wdtype&#34;:&#34;display&#34;&#44;&#34;txt&#34;:&#34;Please tell us about your client to continue so we can include all possible programs&#34;&#44;&#34;img&#34;:&#34;&#34;&#44;&#34;fin&#34;:&#34;&#34;&#44;&#34;fout&#34;:&#34;&#34;&#44;&#34;move&#34;:&#34;&#34;&#44;&#34;parent&#34;:&#34;36&#34;}' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='yes', ".$qs["name"]."='Visual: clientquestion', ".$qs["value"]."='{&#34;divname&#34;:&#34;nextbutton&#34;&#44;&#34;lf&#34;:5213&#44;&#34;tp&#34;:1987&#44;&#34;wd&#34;:1307&#44;&#34;ht&#34;:427&#44;&#34;rad&#34;:107&#44;&#34;zindex&#34;:&#34;1&#34;&#44;&#34;fsz&#34;:187&#44;&#34;pad&#34;:93&#44;&#34;fclr&#34;:&#34;#FFFFFF&#34;&#44;&#34;ffam&#34;:&#34;&#34;&#44;&#34;classname&#34;:&#34;&#34;&#44;&#34;fbld&#34;:&#34;0&#34;&#44;&#34;rqd&#34;:&#34;0&#34;&#44;&#34;tabi&#34;:&#34;1&#34;&#44;&#34;fund&#34;:&#34;0&#34;&#44;&#34;fitl&#34;:&#34;0&#34;&#44;&#34;faln&#34;:&#34;center&#34;&#44;&#34;bgclr&#34;:&#34;#99BBDD&#34;&#44;&#34;opacity&#34;:&#34;&#34;&#44;&#34;hide&#34;:&#34;0&#34;&#44;&#34;onclick&#34;:&#34;enterclientname();&#34;&#44;&#34;type&#34;:&#34;&#34;&#44;&#34;wd_id&#34;:&#34;&#34;&#44;&#34;section&#34;:&#34;&#34;&#44;&#34;field_id&#34;:&#34;&#34;&#44;&#34;wdtype&#34;:&#34;display&#34;&#44;&#34;txt&#34;:&#34;Next&#34;&#44;&#34;img&#34;:&#34;&#34;&#44;&#34;fin&#34;:&#34;&#34;&#44;&#34;fout&#34;:&#34;&#34;&#44;&#34;move&#34;:&#34;&#34;&#44;&#34;parent&#34;:&#34;36&#34;}' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='yes', ".$qs["name"]."='Visual: clientquestion', ".$qs["value"]."='{&#34;divname&#34;:&#34;Layer 7&#34;&#44;&#34;lf&#34;:2107&#44;&#34;tp&#34;:5320&#44;&#34;wd&#34;:8933&#44;&#34;ht&#34;:3240&#44;&#34;rad&#34;:0&#44;&#34;zindex&#34;:&#34;1&#34;&#44;&#34;fsz&#34;:187&#44;&#34;fclr&#34;:&#34;#000000&#34;&#44;&#34;ffam&#34;:&#34;&#34;&#44;&#34;classname&#34;:&#34;&#34;&#44;&#34;fbld&#34;:&#34;0&#34;&#44;&#34;rqd&#34;:&#34;0&#34;&#44;&#34;tabi&#34;:&#34;1&#34;&#44;&#34;fund&#34;:&#34;0&#34;&#44;&#34;fitl&#34;:&#34;0&#34;&#44;&#34;faln&#34;:&#34;left&#34;&#44;&#34;bgclr&#34;:&#34;&#34;&#44;&#34;opacity&#34;:&#34;&#34;&#44;&#34;hide&#34;:&#34;0&#34;&#44;&#34;onclick&#34;:&#34;&#34;&#44;&#34;type&#34;:&#34;code&#34;&#44;&#34;wd_id&#34;:&#34;&#34;&#44;&#34;section&#34;:&#34;&#34;&#44;&#34;field_id&#34;:&#34;&#34;&#44;&#34;wdtype&#34;:&#34;display&#34;&#44;&#34;txt&#34;:&#34;jQuery(#jsfsquote##jsfpbc_body#jsfsquote#).hide();#jsflf##jsflf#function enterclientname(){#jsflf# &nbsp;ih_clientname = jQuery(#jsfsquote##inpt_clientname#jsfsquote#).val();#jsflf# &nbsp;if(!Boolean(ih_clientname)) ih_clientname = jQuery(#jsfsquote##inpt_clientname_searchtext#jsfsquote#).val();#jsflf# &nbsp;//alert(#jsfsquote#client: #jsfsquote# + ih_clientname);#jsflf# &nbsp;ih_showresults();#jsflf# &nbsp;jQuery(#jsfsquote##jsfpbc_question1#jsfsquote#).hide();#jsflf# &nbsp;jQuery(#jsfsquote##jsfpbc_body#jsfsquote#).show(); &nbsp;#jsflf#}&#34;&#44;&#34;img&#34;:&#34;&#34;&#44;&#34;fin&#34;:&#34;&#34;&#44;&#34;fout&#34;:&#34;&#34;&#44;&#34;move&#34;:&#34;&#34;&#44;&#34;parent&#34;:&#34;&#34;}' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='yes', ".$qs["name"]."='Visual: homeheader_20200304backup', ".$qs["value"]."='{&#34;divname&#34;:&#34;title&#34;&#44;&#34;lf&#34;:2147&#44;&#34;tp&#34;:1867&#44;&#34;wd&#34;:6000&#44;&#34;ht&#34;:2933&#44;&#34;rad&#34;:0&#44;&#34;zindex&#34;:&#34;1&#34;&#44;&#34;fsz&#34;:280&#44;&#34;fclr&#34;:&#34;#000000&#34;&#44;&#34;ffam&#34;:&#34;&#34;&#44;&#34;classname&#34;:&#34;&#34;&#44;&#34;fbld&#34;:&#34;0&#34;&#44;&#34;rqd&#34;:&#34;0&#34;&#44;&#34;tabi&#34;:&#34;1&#34;&#44;&#34;fund&#34;:&#34;0&#34;&#44;&#34;fitl&#34;:&#34;0&#34;&#44;&#34;faln&#34;:&#34;left&#34;&#44;&#34;bgclr&#34;:&#34;&#34;&#44;&#34;opacity&#34;:&#34;&#34;&#44;&#34;hide&#34;:&#34;0&#34;&#44;&#34;onclick&#34;:&#34;&#34;&#44;&#34;type&#34;:&#34;&#34;&#44;&#34;wd_id&#34;:&#34;&#34;&#44;&#34;section&#34;:&#34;&#34;&#44;&#34;field_id&#34;:&#34;&#34;&#44;&#34;wdtype&#34;:&#34;display&#34;&#44;&#34;txt&#34;:&#34;&#34;&#44;&#34;img&#34;:&#34;&#34;&#44;&#34;fin&#34;:&#34;&#34;&#44;&#34;fout&#34;:&#34;&#34;&#44;&#34;move&#34;:&#34;&#34;&#44;&#34;parent&#34;:&#34;&#34;}' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='yes', ".$qs["name"]."='Visual: homeheader_20200304backup', ".$qs["value"]."='{&#34;type&#34;:&#34;header&#34;&#44;&#34;infoonly&#34;:&#34;Innovation HUB Engagement&#34;&#44;&#34;oright&#34;:&#34;750&#34;}' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='yes', ".$qs["name"]."='Visual: homeheader_20200304backup', ".$qs["value"]."='{&#34;divname&#34;:&#34;titletext&#34;&#44;&#34;lf&#34;:0&#44;&#34;tp&#34;:1147&#44;&#34;wd&#34;:6000&#44;&#34;ht&#34;:507&#44;&#34;rad&#34;:0&#44;&#34;zindex&#34;:&#34;1&#34;&#44;&#34;fsz&#34;:347&#44;&#34;fclr&#34;:&#34;#FFFFFF&#34;&#44;&#34;ffam&#34;:&#34;&#34;&#44;&#34;classname&#34;:&#34;&#34;&#44;&#34;fbld&#34;:&#34;1&#34;&#44;&#34;rqd&#34;:&#34;0&#34;&#44;&#34;tabi&#34;:&#34;1&#34;&#44;&#34;fund&#34;:&#34;0&#34;&#44;&#34;fitl&#34;:&#34;0&#34;&#44;&#34;faln&#34;:&#34;center&#34;&#44;&#34;bgclr&#34;:&#34;&#34;&#44;&#34;opacity&#34;:&#34;&#34;&#44;&#34;hide&#34;:&#34;0&#34;&#44;&#34;onclick&#34;:&#34;&#34;&#44;&#34;type&#34;:&#34;&#34;&#44;&#34;wd_id&#34;:&#34;&#34;&#44;&#34;section&#34;:&#34;&#34;&#44;&#34;field_id&#34;:&#34;&#34;&#44;&#34;wdtype&#34;:&#34;display&#34;&#44;&#34;txt&#34;:&#34;Innovation HUB Engagement&#34;&#44;&#34;img&#34;:&#34;&#34;&#44;&#34;fin&#34;:&#34;&#34;&#44;&#34;fout&#34;:&#34;&#34;&#44;&#34;move&#34;:&#34;&#34;&#44;&#34;parent&#34;:&#34;4&#34;}' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);
$wd_row_id = $wd->addRow($wd_id);
$query = "UPDATE wd_".$wd_id." SET ".$qs["enabled"]."='yes', ".$qs["name"]."='Visual: homeheader_20200304backup', ".$qs["value"]."='{&#34;divname&#34;:&#34;dummy&#34;&#44;&#34;lf&#34;:1160&#44;&#34;tp&#34;:1053&#44;&#34;wd&#34;:7933&#44;&#34;ht&#34;:4600&#44;&#34;rad&#34;:0&#44;&#34;zindex&#34;:&#34;1&#34;&#44;&#34;fsz&#34;:187&#44;&#34;fclr&#34;:&#34;#000000&#34;&#44;&#34;ffam&#34;:&#34;&#34;&#44;&#34;classname&#34;:&#34;&#34;&#44;&#34;fbld&#34;:&#34;0&#34;&#44;&#34;rqd&#34;:&#34;0&#34;&#44;&#34;tabi&#34;:&#34;1&#34;&#44;&#34;fund&#34;:&#34;0&#34;&#44;&#34;fitl&#34;:&#34;0&#34;&#44;&#34;faln&#34;:&#34;left&#34;&#44;&#34;bgclr&#34;:&#34;#DDDDDD&#34;&#44;&#34;opacity&#34;:&#34;&#34;&#44;&#34;hide&#34;:&#34;1&#34;&#44;&#34;onclick&#34;:&#34;&#34;&#44;&#34;type&#34;:&#34;&#34;&#44;&#34;wd_id&#34;:&#34;&#34;&#44;&#34;section&#34;:&#34;&#34;&#44;&#34;field_id&#34;:&#34;&#34;&#44;&#34;wdtype&#34;:&#34;display&#34;&#44;&#34;txt&#34;:&#34;&#34;&#44;&#34;img&#34;:&#34;&#34;&#44;&#34;fin&#34;:&#34;&#34;&#44;&#34;fout&#34;:&#34;&#34;&#44;&#34;move&#34;:&#34;&#34;&#44;&#34;parent&#34;:&#34;&#34;}' WHERE wd_row_id=".$wd_row_id;
$dbLink->update($query);














?>