<?php
//error_reporting(E_ALL);
  $ua = new UserAcct();
  $wd = new WebsiteData();

   $jurl = getBaseURL()."jsfcode/jsoncontroller.php?action=getwdrows&userid=".isLoggedOn()."&token=".$_SESSION['s_user']['token']."&orderby=d.created%20DESC&limit=10";
   $durl = getBaseURL()."jsfcode/jsoncontroller.php?action=deletesinglewdrow&userid=".isLoggedOn()."&token=".$_SESSION['s_user']['token'];
   $aurl = getBaseURL()."jsfcode/jsoncontroller.php?action=quicksubmitwebdata&userid=".isLoggedOn()."&token=".$_SESSION['s_user']['token'];
   
   $str = "";
   //$webdata_arr = $wd->getWebDataByFuzzyName("dashboard data %");
   $webdata_arr = $wd->getWebData("#admindashboard",FALSE,FALSE,TRUE);
   if ($webdata_arr != NULL && count($webdata_arr)>0) {
     for ($i=0; $i<count($webdata_arr); $i++) {
        $qs = $wd->getHeaderFields($webdata_arr[$i]['wd_id']);
        $turl = $jurl."&wdname=".$webdata_arr[$i]['wd_id'];
        $turl .= "&cmsq_w".$webdata_arr[$i]['wd_id']."enabled=yes";
        
        $hdr_cnt = 0;
        for($j=0;$j<count($qs);$j++) {
           if(0!=strcmp(strtolower($qs[$j]['label']),"enabled")) {
              //$turl .= "&qids[]=".urlencode($qs[$j]['label']);
              $turl .= "&qids[]=".urlencode($qs[$j]['field_id']);
              $hdr_cnt++;
              if($hdr_cnt>6) break;
           }
        }
        
        $str .= "<div id=\"db".$i."list\" style=\"float:left;margin-top:10px;margin-right:10px;max-width:400px;height:200px;overflow:auto;padding:8px;border:1px solid #999999;border-radius:8px;\">";        
        $str .= "</div>\n";
        
        $str .= "<script>\n";
        $str .= "function return_del".$i."row(jsondata){\n";
        $str .= "jsf_json_sendRequest('".$turl."',show".$i."rows);\n";
        $str .= "}\n";
        $str .= "function delete".$i."row(id){\n";
        $str .= "if(confirm('Are you sure you want to delete this record?')){\n";
        $str .= "jsf_json_sendRequest('".$durl."&wdname=".$webdata_arr[$i]['wd_id']."&wd_row_id=' + id,return_del".$i."row);\n";        
        $str .= "}\n";
        $str .= "}\n";

        $str .= "function return_add".$i."row(jsondata){\n";
        $str .= "jsf_json_sendRequest('".$turl."',show".$i."rows);\n";
        $str .= "}\n";
        $str .= "function add".$i."row(";
        $pc = 0;
        for($j=0;$j<count($qs);$j++) {
           if(0!=strcmp(trim(strtolower($qs[$j]['label'])),"enabled")) {
              if($pc>0) $str .= ", ";
              //$str .= str_replace(" ","_",trim(strtolower($qs[$j]['label'])));
              $str .= $qs[$j]['field_id'];
              $pc++;
              if($pc>6) break;
           }
        }
        $str .= "){\n";
        $str .= "jsf_json_sendRequest('".$aurl;
        $pc = 0;
        for($j=0;$j<count($qs);$j++) {
           if(0!=strcmp(trim(strtolower($qs[$j]['label'])),"enabled")) {
              //$str .= "&".str_replace(" ","_",trim(strtolower($qs[$j]['label'])))."=' + ";
              //$str .= "encodeURIComponent(".str_replace(" ","_",trim(strtolower($qs[$j]['label']))).")";
              //$str .= " + '";
              $str .= "&".$qs[$j]['field_id']."=' + ";
              $str .= $qs[$j]['field_id'];
              $str .= " + '";
              $pc++;
              if($pc>6) break;
           }
        }
        $str .= "&enabled=YES&wdname=".$webdata_arr[$i]['wd_id']."',return_add".$i."row);\n";        
        $str .= "}\n";

        $str .= "function show".$i."rows(jsondata){\n";
        $str .= "var divid='db".$i."list';\n";
        $str .= "var str='';\n";
        $str .= "str += '<table cellpadding=\\\"1\\\" cellspacing=\\\"1\\\" style=\\\"font-size:10px;font-family:verdana;\\\">';\n";
        $str .= "str += '<tr style=\\\"font-size:10px;\\\">';\n";
        $str .= "str += '<td colspan=\\\"".($hdr_cnt + 1)."\\\">';\n";
        
        $str .= "str += '<a style=\\\"font-size:10px;font-family:verdana;\\\" href=\\\"';\n";
        $str .= "str += '".getBaseURL()."jsfadmin/admincontroller.php?action=wd_listrows&pageLimit=25&wd_id=".$webdata_arr[$i]['wd_id']."';\n";
        $str .= "str += '\\\" target=\\\"_new\\\">';\n";
        $str .= "str += '".$webdata_arr[$i]['info']."';\n";
        $str .= "str += '</a>';\n";

        $str .= "str += '</td>';\n";
        $str .= "str += '</tr>';\n";
        $str .= "str += '<tr>';\n";
        $pc = 0;
        for($j=0;$j<count($qs);$j++) {
           if(0!=strcmp(strtolower($qs[$j]['label']),"enabled")) {
              $str .= "str += '<td>".substr($qs[$j]['label'],0,22)."</td>';\n";
              $pc++;
              if($pc>6) break;
           }
        }
        $str .= "str += '<td></td>';\n";
        $str .= "str += '</tr>';\n";
        $str .= "if(Boolean(jsondata.rows)){\n";
        $str .= "for(var i=0;i<jsondata.rows.length;i++){\n";
        $str .= "var clr='#FFFFFF';\n";
        $str .= "if((i%2)==1) clr='#EEEEFF';\n";
        $str .= "str += '<tr bgcolor=\\\"' + clr + '\\\">';\n";
        $pc = 0;
        for($j=0;$j<count($qs);$j++) {
           if(0!=strcmp(trim(strtolower($qs[$j]['label'])),"enabled")) {
              $str .= "str += '<td>';\n";
              if(0==strcmp(trim(strtolower($qs[$j]['label'])),"url")) {
                 $str .= "str += '<a style=\\\"font-size:10px;font-family:verdana;\\\" href=\\\"';\n";
                 //$str .= "str += jsondata.rows[i].".str_replace(" ","",trim(strtolower($qs[$j]['label']))).";\n";
                 $str .= "str += jsondata.rows[i].".$qs[$j]['field_id'].";\n";
                 $str .= "str += '\\\" target=\\\"_new\\\">';\n";
                 //$str .= "var temp = jsondata.rows[i].".str_replace(" ","",trim(strtolower($qs[$j]['label']))).";\n";
                 $str .= "var temp = jsondata.rows[i].".$qs[$j]['field_id'].";\n";
                 $str .= "temp = temp.replace('%%%EMPTY%%%','');\n";
                 $str .= "temp = temp.replace('%E%','');\n";
                 $str .= "if(temp.length>30) temp = '...' + temp.substr((temp.length - 25));\n";
                 $str .= "str += temp;\n";
                 $str .= "str += '</a>';\n";
              } else {
                 //$str .= "str += jsondata.rows[i].".str_replace(" ","",trim(strtolower($qs[$j]['label']))).";\n";
                 //$str .= "str += jsondata.rows[i].".$qs[$j]['field_id'].";\n";
                 $str .= "var temp = jsondata.rows[i].".$qs[$j]['field_id'].";\n";
                 $str .= "temp = temp.replace('%%%EMPTY%%%, ','');\n";
                 $str .= "temp = temp.replace('%E%, ','');\n";
                 $str .= "temp = temp.replace('%%%EMPTY%%%','');\n";
                 $str .= "temp = temp.replace('%E%','');\n";
                 $str .= "if(temp.length>30) temp = '...' + temp.substr((temp.length - 25));\n";
                 $str .= "str += temp;\n";
              }
              $str .= "str += '</td>';\n";
              $pc++;
              if($pc>6) break;
           }
        }
        $str .= "str += '<td><div style=\\\"cursor:pointer;\\\" onClick=\\\"delete".$i."row(' + jsondata.rows[i].wd_row_id + ');\\\"><img src=\\\"/jsfimages/trash.gif\\\"></div></td>';\n";
        $str .= "str += '</tr>';\n";
        $str .= "}\n";
        $str .= "}\n";

        $str .= "str += '<tr>';\n";
        $pc = 0;
        for($j=0;$j<count($qs);$j++) {
           if(0!=strcmp(trim(strtolower($qs[$j]['label'])),"enabled")) {
              $wid = "140";
              if($pc>0) $wid = "40";
              $str .= "str += '<td>';\n";
              //$str .= "str += '<input type=\\\"text\\\" style=\\\"width:".$wid."px;font-size:8px;font-family:verdana;\\\" id=\\\"addrow".$i.str_replace(" ","",trim(strtolower($qs[$j]['label'])))."\\\">';\n";
              $str .= "str += '<input type=\\\"text\\\" style=\\\"width:".$wid."px;font-size:8px;font-family:verdana;\\\" id=\\\"addrow".$i.$qs[$j]['field_id']."\\\">';\n";
              $str .= "str += '</td>';\n";
              $pc++;
              if($pc>6) break;
           }
        }
        $str .= "str += '<td><div style=\\\"cursor:pointer;\\\" onClick=\\\"add".$i."row(";
        $pc = 0;
        for($j=0;$j<count($qs);$j++) {
           if(0!=strcmp(trim(strtolower($qs[$j]['label'])),"enabled")) {
              if($pc>0) $str .= ", ";
              //$str .= "jQuery(\\'#addrow".$i.str_replace(" ","_",trim(strtolower($qs[$j]['label'])))."\\').val()";
              $str .= "jQuery(\\'#addrow".$i.$qs[$j]['field_id']."\\').val()";
              $pc++;
              if($pc>6) break;
           }
        }
        $str .= ");\\\"><img src=\\\"/jsfimages/add.png\\\"></div></td>';\n";
        $str .= "str += '</tr>';\n";
        
        
        $str .= "str += '</table>';\n";
        $str .= "jQuery('#' + divid).html(str);\n";
        $str .= "}\n";
        $str .= "jsf_json_sendRequest('".$turl."',show".$i."rows);\n";
        $str .= "</script>\n";
     }
   }
   //$str .= "<div style=\"clear:both;width:1px;height:1px;overflow:hidden;\"></div>";
   print $str;

   
   
   
   $dbi = new MYSQLAccess();
   $str = "";
   $webdata = $wd->getWebDataByName("dashboard sql");
   if ($webdata!=NULL && $webdata['wd_id']>0) {
      $qs = $wd->getFieldLabels($webdata['wd_id']);
      $results = $dbi->queryGetResults("SELECT * FROM wd_".$webdata['wd_id']." WHERE dbmode<>'DELETED' AND LOWER(".$qs['enabled'].")='yes' ORDER BY ".$qs['sequence'].";");
      
      for ($i=0; $i<count($results); $i++) {        
         $str .= "<div id=\"db".$i."list\" style=\"float:left;margin-top:10px;margin-right:10px;max-width:400px;height:200px;overflow:auto;padding:8px;border:1px solid #999999;border-radius:8px;\">";
         if($results[$i][$qs['mainurl']] != NULL) {
            $str .= "<a style=\"font-size:10px;font-family:verdana;\" href=\"".convertBack($results[$i][$qs['mainurl']])."\" target=\"_new\">".$results[$i][$qs['name']]."</a>";
         } else {
            $str .= "<div style=\"font-size:10px;font-family:verdana;\">".$results[$i][$qs['name']]."</div>";
         }
         $str .= "<table cellpadding=\"2\" cellspacing=\"1\" style=\"font-size:10px;font-family:verdana;\">\n";
         $dbquery = convertBack($results[$i][$qs['sql']]);
         $dbresults = $dbi->queryGetResults($dbquery);
         if($dbresults==NULL || count($dbresults)<1){
            $str .= "<tr><td>No Rows to display at this time.</td></tr>";
         } else {
            $str .= "<tr>";
            foreach($dbresults[0] as $n => $v){
               $str .= "<td>".$n."</td>";
            }
            $str .= "</tr>";
            for ($j=0; $j<count($dbresults); $j++) {
               $clr = "#FFFFFF";
               if(($j%2)==1) $clr="#EEEEFF";
               $str .= "<tr bgcolor=\"".$clr."\">";
               foreach($dbresults[$j] as $n => $v){
                  $str .= "<td>";
                  if($results[$i][$qs['url']]==NULL) {
                     $str .= $v;
                  } else {
                     $str .= "<a style=\"font-size:10px;font-family:verdana;\" href=\"".convertBack($results[$i][$qs['url']]).$dbresults[$j][$results[$i][$qs['id']]]."\" target=\"_new\">".$v."</a>";
                  }
                  $str .= "</td>";
               }
               $str .= "</tr>";
            }
         }
        
         $str .= "</table>\n";
         $str .= "</div>\n";
      }
   }
   $str .= "<div style=\"clear:both;width:1px;height:1px;overflow:hidden;\"></div>";
   print $str;
   
   
?>  
  
  
  
  
  
  
  
<?php

  ///////////////////////////////////////////////////////////////////////////////////////////////

  $url = getBaseURL()."jsfadmin/admincontroller.php?action=dashboard&subaction=deletecomment&postid=";
  $up = new UserPost();

   $allUsers = $ua->getAdminUsers();
   $userOpts = NULL;
   for ($i=0; $i<count($allUsers); $i++) {
      $userOpts[$allUsers[$i]['email']." - ".$allUsers[$i]['fname']." ".$allUsers[$i]['lname']] = $allUsers[$i]['userid'];
   }
   $userList = getOptionList("refid", $userOpts, NULL, FALSE);
?>

<hr>

<div style="position:relative;height:600px;width:900px;margin-top:20px;">

   <div style="position:absolute;left:10px;top:10px;width:800px;height:30px;overflow:hidden;">
   Welcome to <?php echo $vars['defaultTitle']; ?> admin center.
   user: <?= $vars['email'] ?>
   </div>

   <div style="position:absolute;left:10px;top:50px;width:425px;height:20px;overflow:hidden;font-size:16px;color:#999999;font-family:verdana;">
      Your To-Do List
   </div>
   <div style="position:absolute;left:10px;top:70px;width:425px;height:150px;overflow:auto;border:1px solid #777777;border-radius:8px;padding:4px;">
   <div style="width:395px;">   
         <div style="position:relative;width:395px;">
         <?php
            $str = $up->displayPostsFor("adminui",isLoggedOn(),"ADMINUI",NULL,"ACTIVE",200,TRUE,"todo",$url);
            print $str;
         ?>
         </div>
   </div>
   </div>

   <div style="position:absolute;left:450px;top:50px;width:425px;height:20px;overflow:hidden;font-size:16px;color:#999999;font-family:verdana;">
      Your List of Notes
   </div>
   <div style="position:absolute;left:450px;top:70px;width:425px;height:150px;overflow:auto;border:1px solid #777777;border-radius:8px;padding:4px;">
   <div style="width:395px;">   
         <div style="position:relative;width:395px;">
         <?php
            $str = $up->displayPostsFor("adminui",isLoggedOn(),"ADMINUI",NULL,"ACTIVE",200,TRUE,"notes",$url);
            print $str;
         ?>
         </div>
   </div>
   </div>


   <div style="position:absolute;left:10px;top:230px;width:425px;height:20px;overflow:hidden;font-size:16px;color:#999999;font-family:verdana;">
      Other admins' personal messages
   </div>
   <div style="position:absolute;left:10px;top:250px;width:425px;height:150px;overflow:auto;border:1px solid #777777;border-radius:8px;padding:4px;">
   <div style="width:395px;">   
         <div style="position:relative;width:395px;">
         <?php
            $str = $up->displayPostsFor("adminui",NULL,"ADMINUI",isLoggedOn(),"ACTIVE",200,TRUE,"others",$url);
            print $str;
         ?>
         </div>
   </div>
   </div>

   <div style="position:absolute;left:450px;top:230px;width:425px;height:20px;overflow:hidden;font-size:16px;color:#999999;font-family:verdana;">
      Admin global messages
   </div>
   <div style="position:absolute;left:450px;top:250px;width:425px;height:150px;overflow:auto;border:1px solid #777777;border-radius:8px;padding:4px;">
   <div style="width:395px;">   
         <div style="position:relative;width:395px;">
         <?php
            $str = $up->displayPostsFor("adminui",NULL,"ADMINGLOBAL",NULL,"ACTIVE",200,TRUE,NULL,$url);
            print $str;
         ?>
         </div>
   </div>
   </div>

   
   <div id="adminpostselectionouter" style="position:absolute;left:10px;top:425px;width:600px;height:20px;">
      <select id="adminpostselection" name="adminpostselection" onchange="selectPostType();">
         <option value="todopost">Add a todo to my list</option>
         <option value="notepost">Add to my notes list</option>
         <option value="wallpost">Send a comment to another administrator</option>
         <option value="globalpost">Send a comment to all administrators</option>
      </select>
   </div>

<script type="text/javascript">
function selectPostType() {
   var e = document.getElementById("adminpostselection");
   var val = e.options[e.selectedIndex].value;
   document.getElementById("todopost").style.display='none';
   document.getElementById("notepost").style.display='none';
   document.getElementById("wallpost").style.display='none';
   document.getElementById("globalpost").style.display='none';
   if (Boolean(val)) document.getElementById(val).style.display='';
}
</script>

   <div id="todopost" style="position:absolute;left:10px;top:450px;width:700px;height:110px;background-color:#c88b93;border:1px solid #555555;border-radius:8px;padding:4px;">
      <div style="position:relative;width:600px;height:80px;">
      <form action="admincontroller.php" method="post">
      <input type="hidden" name="action" value="dashboard">
      <input type="hidden" name="subaction" value="addcomment">
      <input type="hidden" name="posttype" value="ADMINUI">
      <input type="hidden" name="category" value="todo">
      <input type="hidden" name="userid" value="<?php echo isLoggedOn(); ?>">
      <input type="hidden" name="status" value="ACTIVE">
      <div style="position:absolute;left:10px;top:10px;width:120px;height:18px;font-size:14px;font-family:verdana;">Add a to-do:</div>
      <div style="position:absolute;left:130px;top:10px;width:430px;height:20px;font-size:14px;font-family:verdana;">
         <input type="text" name="content" value="" style="width:400px;height:18px;font-size:12px;font-family:verdana;">
      </div>
      <div style="position:absolute;left:10px;top:50px;width:200px;height:25px;">
         <input type="image" name="submit" value="submit" src="submit.png">
      </div>
      </form>
      </div>
   </div>


   <div id="notepost" style="position:absolute;left:10px;top:450px;width:700px;height:110px;display:none;background-color:#c8c78b;border:1px solid #555555;border-radius:8px;padding:4px;">
      <div style="position:relative;width:600px;height:80px;">
      <form action="admincontroller.php" method="post">
      <input type="hidden" name="action" value="dashboard">
      <input type="hidden" name="subaction" value="addcomment">
      <input type="hidden" name="posttype" value="ADMINUI">
      <input type="hidden" name="category" value="notes">
      <input type="hidden" name="userid" value="<?php echo isLoggedOn(); ?>">
      <input type="hidden" name="status" value="ACTIVE">
      <div style="position:absolute;left:10px;top:10px;width:120px;height:18px;font-size:14px;font-family:verdana;">Add a note:</div>
      <div style="position:absolute;left:130px;top:10px;width:430px;height:20px;font-size:14px;font-family:verdana;">
         <input type="text" name="content" value="" style="width:400px;height:18px;font-size:12px;font-family:verdana;">
      </div>
      <div style="position:absolute;left:10px;top:50px;width:200px;height:25px;">
         <input type="image" name="submit" value="submit" src="submit.png">
      </div>
      </form>
      </div>
   </div>

   <div id="wallpost" style="position:absolute;left:10px;top:450px;width:700px;height:140px;display:none;background-color:#CCCCCC;border:1px solid #555555;border-radius:8px;padding:4px;">
      <div style="position:relative;width:600px;height:110px;">
      <form action="admincontroller.php" method="post">
      <input type="hidden" name="action" value="dashboard">
      <input type="hidden" name="subaction" value="addcomment">
      <input type="hidden" name="posttype" value="ADMINUI">
      <input type="hidden" name="category" value="others">
      <input type="hidden" name="userid" value="<?php echo isLoggedOn(); ?>">
      <input type="hidden" name="status" value="ACTIVE">
      <div style="position:absolute;left:10px;top:10px;width:120px;height:18px;font-size:14px;font-family:verdana;">Send to user:</div>
      <div style="position:absolute;left:130px;top:10px;width:430px;height:20px;font-size:14px;font-family:verdana;">
         <?php echo $userList; ?>
      </div>
      <div style="position:absolute;left:10px;top:50px;width:120px;height:18px;font-size:14px;font-family:verdana;">Message:</div>
      <div style="position:absolute;left:130px;top:50px;width:430px;height:20px;font-size:14px;font-family:verdana;">
         <input type="text" name="content" value="" style="width:400px;height:18px;font-size:12px;font-family:verdana;">
      </div>
      <div style="position:absolute;left:10px;top:75px;width:200px;height:25px;">
         <input type="image" name="submit" value="submit" src="submit.png">
      </div>
      </form>
      </div>
   </div>

   <div id="globalpost" style="position:absolute;left:10px;top:450px;width:700px;height:110px;display:none;background-color:#8b8fc8;border:1px solid #555555;border-radius:8px;padding:4px;">
      <div style="position:relative;width:600px;height:80px;">
      <form action="admincontroller.php" method="post">
      <input type="hidden" name="action" value="dashboard">
      <input type="hidden" name="subaction" value="addcomment">
      <input type="hidden" name="posttype" value="ADMINGLOBAL">
      <input type="hidden" name="userid" value="<?php echo isLoggedOn(); ?>">
      <input type="hidden" name="status" value="ACTIVE">
      <div style="position:absolute;left:10px;top:10px;width:135px;height:18px;font-size:14px;font-family:verdana;">Post to everyone:</div>
      <div style="position:absolute;left:140px;top:10px;width:430px;height:20px;font-size:14px;font-family:verdana;">
         <input type="text" name="content" value="" style="width:400px;height:18px;font-size:12px;font-family:verdana;">
      </div>
      <div style="position:absolute;left:10px;top:50px;width:200px;height:25px;">
         <input type="image" name="submit" value="submit" src="submit.png">
      </div>
      </form>
      </div>
   </div>
</div>
