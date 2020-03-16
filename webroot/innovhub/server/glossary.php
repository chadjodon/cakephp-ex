<?php

/**
   CREATE TABLE glosprnt (
     glossid int(11) NOT NULL auto_increment,
     glosstitle varchar(64) default NULL,
     descr varchar(128) default NULL,
     PRIMARY KEY  (glossid)
   );

   CREATE TABLE glossary (
     glossaryid bigint not null default 0,
     term varchar(128) NOT NULL default '',
     definition text default NULL,
     PRIMARY KEY  (glossaryid, term)
   );
**/


class Glossary {
  var $glossaryid=0;
  var $glossaryterms;

  function Glossary($gid=0) {
      $this->setGlossaryId($gid);
  }

  function setGlossaryId($gid=0) {
      $this->glossaryid=$gid;
      $this->glossaryterms = $this->getGlossary();
  }

   function setGlossaryIdByName($name) {
      $this->glossaryid=0;
      $wd = new WebsiteData();
      $wdata = $wd->getWebData($name);
      if($wdata!=NULL && $wdata['privatesrvy']==11) {
         $this->setGlossaryId($wdata['wd_id']);
      } else {
         $sql = "SELECT * FROM glosprnt WHERE LOWER(glosstitle)='".strtolower($name)."';";
         $dbLink = new MYSQLAccess;
         $results = $dbLink->queryGetResults($sql);
         if ($results!=NULL && count($results>0)) {
            $this->setGlossaryId($results[0]['glossid']);
         }
      }
   }

   function getGlossaryId(){
      return $this->glossaryid;
   }

  function getGlossary() {
     $results = NULL;
     if ($this->glossaryid==null || $this->glossaryid==0){
        $this->glossaryterms = NULL;
     } else {        
        $wd = new WebsiteData();
        $wdata = $wd->getWebData($this->glossaryid);
        if($wdata!=NULL && $wdata['privatesrvy']==11) {
           $results1 = $wd->getRows($wdata['wd_id'],NULL,NULL,NULL,FALSE,NULL,FALSE,FALSE,TRUE,TRUE,TRUE);
           $results = $results1['results'];
        } else {        
           $dbLink = new MYSQLAccess;     
           $query = "SELECT * FROM glossary WHERE glossaryid='$this->glossaryid';";
           $results = $dbLink->queryGetResults($query);
        }
     }
     return $results;
  }
  
  function encodeTerm($term) {
     $term = strtolower($term);
     $term = str_replace(" ","",$term);
     $term = str_replace("a","1",$term);
     $term = str_replace("e","2",$term);
     $term = str_replace("i","3",$term);
     $term = str_replace("0","4",$term);
     $term = str_replace("u","5",$term);
     $term = str_replace("y","6",$term);
     $term = str_replace("t","7",$term);
     $term = str_replace("n","8",$term);
     $term = str_replace("s","9",$term);
     $term = str_replace("h","0",$term);
     $term = str_replace("r","a",$term);
     $term = str_replace("d","e",$term);
     $term = str_replace("l","i",$term);
     $term = str_replace("c","o",$term);
     $term = str_replace("m","u",$term);
     $term = str_replace("f","y",$term);
     $term = str_replace("-","dsh",$term);
     return $term;
  }

   function flagAllTerms($str,$clr="blue") {
      if ($this->glossaryid==null || $this->glossaryid==0) return $str;
      $ignoresects = array();
      $terms = $this->glossaryterms;
      $finishedTerms = null;      
      for ($i=0; $i<count($terms); $i++) {
         $link1  = "<span class=\"jsfglossarydisptip\"";
         //$link1 .= " onmouseover=\"jsfglsDisplayTip(this,0,0,'".$terms[$i]['definition']."');\"";
         $link1 .= " onmouseover=\"jsfglsDisplayTip(this,0,0,'".$this->encodeTerm($terms[$i]['term'])."');\"";
         $link1 .= " onclick=\"jsfglsviewglossary('".$this->encodeTerm($terms[$i]['term'])."');\"";
         $link1 .= " style=\"color:".$clr.";cursor:pointer;\">";
         $allwords = convertBack(trim($terms[$i]['term']).",".trim($terms[$i]['alternates']));
         $alts = separateStringBy($allwords,",",NULL,TRUE);
         for ($j=0; $j<count($alts); $j++) {
            $altWord = trim($alts[$j]);
            if ($finishedTerms[$altWord]!=1 && strpos($link1,$altWord)===FALSE) {
               $link = $link1.$altWord."</span>";
               $finishedTerms[$altWord] = 1;
               //$str = str_replace($altWord,$link,$str);
               $finished = FALSE;
               $offset = 0;
               while(!$finished && $offset<strlen($str)){
                  if(strpos($str,$altWord,$offset)!==FALSE) {
                     $start = strpos($str,$altWord,$offset);
                     $length = strlen($altWord);
                     $allow = TRUE;
                     for($k=0;$k<count($ignoresects);$k++) {
                        if($start>=$ignoresects[$k]['st'] && $start<=$ignoresects[$k]['en']) {
                           $allow = FALSE;
                           break;
                        }
                     }
                     if($allow && trim($terms[$i]['definition'])!=NULL) {
                        $tstr = substr($str,0,$start).$link.substr($str,($start+$length));
                        $str = $tstr;
                        $offset = $start + strlen($link);
                        for($k=0;$k<count($ignoresects);$k++) {
                           if($ignoresects[$k]['st']>$start){
                              $diff = strlen($link) - strlen($altWord);
                              $ignoresects[$k]['st'] += $diff;
                              $ignoresects[$k]['en'] += $diff;
                           }
                        }
                        $t_ig = array();
                        $t_ig['st'] = $start;
                        $t_ig['en'] = $offset;
                        $ignoresects[] = $t_ig;
                     } else {
                        $offset = $start + $length;
                        if($allow) {
                           $t_ig = array();
                           $t_ig['st'] = $start;
                           $t_ig['en'] = $offset;
                           $ignoresects[] = $t_ig;
                        }
                     }
                  } else {
                     $finished = TRUE;
                  }
               }
            }
         }
      }
      return $str;
   }
  
   function getjscript() {
      $str = "";
      if ($this->glossaryid!=null && $this->glossaryid>0) {
         $str .= "<div id=\"jsfglsTipBox\" style=\"display:none;opacity:0.9;position:absolute;z-index:999;width:300px;font-size:12px;font-weight:normal;font-family:verdana;border:1px solid #000099;border-radius:5px;padding:10px;color:#000099;background-color:#EEEEFF;\"></div>\n";
         $str .= "<script type = \"text/javascript\">\n";
         $str .= "var jsfglsTipBoxID = 'jsfglsTipBox';\n";
         $str .= "var jsfglstip_box_id;\n";
         $str .= "function jsfglsfindPosX(obj) {\n";
         $str .= "   var curleft = 0;\n";
         $str .= "   if(obj.offsetParent)\n";
         $str .= "   while(1) {";
         $str .= "      curleft += obj.offsetLeft;\n";
         $str .= "      if(!obj.offsetParent) break;\n";
         $str .= "      obj = obj.offsetParent;\n}\n";
         $str .= "   else if(obj.x) curleft += obj.x;\n";
         $str .= "   return curleft;\n";
         $str .= "}\n";
         $str .= "function jsfglsfindPosY(obj) {\n";
         $str .= "   var curtop = 0;\n";
         $str .= "   if(obj.offsetParent)\n";
         $str .= "   while(1){\n";
         $str .= "      curtop += obj.offsetTop;\n";
         $str .= "      if(!obj.offsetParent) break;\n";
         $str .= "      obj = obj.offsetParent;\n";
         $str .= "   }\n";
         $str .= "   else if(obj.y) curtop += obj.y;\n";
         $str .= "   return curtop;\n";
         $str .= "}\n";
         $str .= "function jsfglsDisplayTip(me,offX,offY,term) {\n";
         //$str .= "function jsfglsDisplayTip(me,offX,offY,content) {\n";
         $str .= "   var tipO = me;\n";
         $str .= "   jsfglstip_box_id = document.getElementById(jsfglsTipBoxID);\n";
         $str .= "   if(Boolean(jsfglstip_box_id)){\n";
         $str .= "      var x = jsfglsfindPosX(me) + 20;\n";
         $str .= "      var y = jsfglsfindPosY(me) + 20;\n";
         $str .= "      jsfglstip_box_id.style.left = String(parseInt(x + offX) + 'px');\n";
         $str .= "      jsfglstip_box_id.style.top = String(parseInt(y + offY) + 'px');\n";
         //$str .= "      jsfglstip_box_id.innerHTML = content;\n";
         $str .= "      jsfglstip_box_id.innerHTML = jsfglsgetdef(term);\n";
         $str .= "      jsfglstip_box_id.style.display = \"block\";\n";
         $str .= "      tipO.onmouseout = jsfglsHideTip;\n";
         $str .= "   }\n";
         $str .= "}\n";
         $str .= "function jsfglsHideTip() { jsfglstip_box_id.style.display = 'none'; }\n";
         $str .= "function jsfglsviewglossary(term) {\n";
         //$str .= " var url='".getBaseURL()."jsfcode/viewglossary.php?glossaryid=".$this->glossaryid."#' + term.replace(/\\s+/g,'').toLowerCase();\n";
         $str .= " var url='".getBaseURL()."jsfcode/viewglossary.php?glossaryid=".$this->glossaryid."#' + term;\n";
         $str .= " var viewglossary = window.open(url,'glossarywindow','scrollbars=yes,menubar=no,height=400,width=600,resizable=yes,toolbar=no,location=no,status=no');\n";
         $str .= "}\n";
         $str .= "function jsfglsgetdef(term) {\n";
         $terms = $this->glossaryterms;
         $str .= "  var str='';\n";
         $str .= "  if(term=='') str = '';\n";
         for ($i=0; $i<count($terms); $i++) {
            if(trim($terms[$i]['definition'])!=NULL) {
               $str .= "  else if(term=='".$this->encodeTerm($terms[$i]['term'])."') str = '".$terms[$i]['definition']."';\n";
            }
         }
         $str .= "  return str;\n";
         $str .= "}\n";
         $str .= "</script>\n";
      }
      return $str;
      //return "";
   }

  function removeTerm ($term) {
     $dbLink = new MYSQLAccess;     
     $query = "DELETE FROM glossary WHERE term='".$term."' AND glossaryid=".$this->glossaryid.";";
      $dbLink->delete($query);
      return true;
  }

  function insertTerm ($term,$definition="", $alternates="") {
     $dbLink = new MYSQLAccess;     
     $query = "INSERT INTO glossary set term='".$term."', definition='".convertString($definition)."', glossaryid=".$this->glossaryid.", alternates='".$alternates."';";
     $dbLink->insert($query);
     return true;
  }

  function printAllTerms() {
      $str = "<FORM><INPUT type=\"button\" value=\"Close Window\" onClick=\"window.close()\"></FORM><br>";
      $str .= "<center><h2>GLOSSARY</h2></center>\n";
      if ($this->glossaryid==null || $this->glossaryid==0) return $str;
      $str .= "<table align=\"center\" width=\"90%\" cellpadding=\"5\" cellspacing=\"2\" bgcolor=\"#DDDDDD\">\n";
      $terms = $this->glossaryterms;      
      for ($i=0; $i<count($terms); $i++) {
         $str .= "<tr valign=\"top\"><td><a name=\"".$this->encodeTerm($terms[$i]['term'])."\"/>".$terms[$i]['term']."</td><td bgcolor=\"#FFFFFF\">".$terms[$i]['definition']."</td></tr>\n";
      }
      $str .= "</table>\n";
      $str .= "<br><FORM><INPUT type=\"button\" value=\"Close Window\" onClick=\"window.close()\"></FORM>";
      $str .= "<br><img src=\"".getBaseURL()."jsfimages\pixel.gif\" width=\"1\" height=\"400\">";
      return $str;
  }




   function getGlossaryTitle(){
     if ($this->glossaryid==null || $this->glossaryid==0) return NULL;
     $dbLink = new MYSQLAccess;     
     $query = "SELECT * FROM glosprnt WHERE glossid='".$this->glossaryid."';";
     $results = $dbLink->queryGetResults($query);
     return $results[0]['glosstitle'];
   }



   function getGlossaryOptions($default=null){
      $opt = array();
      
      $wd = new WebsiteData();
      $results = $wd->getWebTables(NULL,11);
      for ($i=0; $i<count($results); $i++) {
         $opt[$results[$i]['name']." (jdata)"]=$results[$i]['wd_id'];
      }
      
      $results = $this->getGlossaries();
      for ($i=0; $i<count($results); $i++) {
         if(!isset($opt[$results[$i]['glosstitle']])) $opt[$results[$i]['glosstitle']]=$results[$i]['glossid'];
      }
      return getOptionList("glossaryid", $opt, $this->getGlossaryId(), TRUE);
   }

   function getGlossaries() {
      $sql = "SELECT * FROM glosprnt ORDER BY glosstitle;";
      $dbLink = new MYSQLAccess;
      $results = $dbLink->queryGetResults($sql);
      return $results;
   }

   function newGlossary($glosstitle, $descr){
      $sql = "INSERT INTO glosprnt (glosstitle,descr) VALUES ('".$glosstitle."','".$descr."');";
      $dbLink = new MYSQLAccess;
      $this->setGlossaryId($dbLink->insertGetValue($sql));
   }
   
   function editGlossary($glosstitle, $descr){
      $sql = "UPDATE glosprnt set glosstitle='".$glosstitle."',descr='".$descr."' WHERE glossid=".$this->getGlossaryId().";";
      $dbLink = new MYSQLAccess;
      $dbLink->update($sql);
      return true;
   }
   
   function removeGlossary() {
      $terms = $this->getGlossary();
      if (count($terms)>0) {
         return false;
      } else {
         $dbLink = new MYSQLAccess;
      	$sql = "DELETE FROM glosprnt WHERE glossid=".$this->getGlossaryId().";";
      	$dbLink->delete($sql);
         $this->setGlossaryId(0);
         return true;
      }
   }

}

?>
