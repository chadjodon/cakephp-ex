<?php

class Context {

   function getSiteSQL($fieldName = "siteid"){
      $query = "( ".$fieldName." IN (";
       $sitearr = $this->getSiteContext(); 
       for ($i=0; $i<count($sitearr); $i++) {
         $query .= $sitearr[$i]['siteid'].",";
       }
      $query .= "0) OR ".$fieldName." IS NULL) ";

      //print "\n<!-- getSiteSQL query: ".$query." -->\n";
      
      return $query;
   }

   function setSiteInCookie($siteid){
      if (!headers_sent()) {
         setcookie("siteid",$siteid, time()+60*60*24*180,'/',getCookieDomain());
      }
   }

   function checkSiteCookie(){
      if (isset($_COOKIE["siteid"])) {
         $siteid = $_COOKIE["siteid"];
         if ($siteid!=null && $siteid>0) $this->setSiteContext($siteid);
      }
   }

   function setSiteContext($siteid=-1){
      if ($siteid != NULL) {
         if ($siteid==-1) {
            $sitearr = null;
            $sitearr[0] = $this->getSiteInfo(-1);
            $_SESSION['sitearr'] = $sitearr;         
            $this->setSiteInCookie($siteid);
         } else if (is_numeric($siteid)) {
            $sitearr = $this->getSiteContext();
            if ($sitearr[0]['siteid']!=$siteid) {
               $this->clearSiteContext();
               $sitearr = $this->getSiteRel($siteid);
               $_SESSION['sitearr'] = $sitearr;
               $this->setSiteInCookie($siteid);
            }
         } else {
            $sitearr = $this->getSiteContext();
            if (0!=strcmp($sitearr[0]['shortname'],$siteid)) {
               $this->clearSiteContext();
               $temp = $this->getSiteByShortname($siteid);
               $sitearr = $this->getSiteRel($temp['siteid']);
               $_SESSION['sitearr'] = $sitearr;
               $this->setSiteInCookie($temp['siteid']);
            }
         }
      }
   }

   function transformSiteid($siteid=-1){
      if ($siteid == NULL || is_numeric($siteid)) {
         return $siteid;
      } else {
         $temp = $this->getSiteByShortname($siteid);
         return $temp['siteid'];
      }
   }

   function clearSiteContext(){
         unset($_SESSION['sitearr']);
         $ver = new Version();
         $ver->clearSessionCache();
   }

   function isSiteSet(){
      return (isset($_SESSION['sitearr']) && $_SESSION['sitearr'][0]['siteid']!=-1);
   }

   function getSiteContext(){
      if (!isset($_SESSION['sitearr'])) $this->setSiteContext(-1);

      //print "\n<!-- getSiteContext() sitearr:\n";
      //print_r($_SESSION['sitearr']);
      //print "\n-->\n";

      return $_SESSION['sitearr'];
   }

   function addSite ($priority,$name,$shortname,$shortdescr,$descr,$site_url,$site_type,$parent=-1,$image1=NULL,$image2=NULL,$image3=NULL,$image4=NULL,$image5=NULL,$alternates=NULL,$metadescr=NULL,$keywords=NULL){
      $sql = "insert into microsites (priority,name,shortname,shortdescr,descr,site_url,site_type,image1,image2,image3,image4,image5,alternates,metadescr,keywords) VALUES ('".$priority."','".$name."','".$shortname."','".$shortdescr."','".$descr."','".$site_url."','".$site_type."','".$image1."','".$image2."','".$image3."','".$image4."','".$image5."','".convertString($alternates)."','".convertString($metadescr)."','".convertString($keywords)."');";
      $dbLink = new MYSQLAccess;
      $newsiteid = $dbLink->insertGetValue($sql);

      $sql = "insert into micrositerel (siteid,parent,reltype) VALUES ('".$newsiteid."','".$parent."','');";
      $dbLink->insert($sql);

      return $newsiteid;
   }

   function updateSite ($siteid,$priority,$name,$shortname,$shortdescr,$descr,$site_url,$site_type,$image1=NULL,$image2=NULL,$image3=NULL,$image4=NULL,$image5=NULL,$alternates=NULL,$metadescr=NULL,$keywords=NULL){
      $sql = "UPDATE microsites set priority='".$priority."', name='".$name."', shortname='".$shortname."', shortdescr='".$shortdescr."', descr='".$descr."', site_url='".$site_url."', site_type='".$site_type."', alternates='".convertString($alternates)."', metadescr='".convertString($metadescr)."', keywords='".convertString($keywords)."'";
      if ($image1!=NULL) $sql .= ", image1='".$image1."'";
      if ($image2!=NULL) $sql .= ", image2='".$image2."'";
      if ($image3!=NULL) $sql .= ", image3='".$image3."'";
      if ($image4!=NULL) $sql .= ", image4='".$image4."'";
      if ($image5!=NULL) $sql .= ", image5='".$image5."'";
      $sql .= " WHERE siteid='".$siteid."';";
      $dbLink = new MYSQLAccess;
      $dbLink->update($sql);
   }

   function deleteSite($siteid){
      if ($this->isSiteLeaf($siteid)){
         $dbLink = new MYSQLAccess;
         $sql = "DELETE FROM micrositerel WHERE siteid='".$siteid."';";
         $dbLink->delete($sql);
         $sql = "DELETE FROM microsites WHERE siteid='".$siteid."';";
         $dbLink->delete($sql);
         return TRUE;
      } else {
         return FALSE;
      }
   }

   function isSiteLeaf($siteid){
      $children = $this->getChildren($siteid);
      if (count($children)>0 || $siteid==-1) return FALSE;
      else return TRUE;
   }

   function getChildren($siteid){
      $sql = "select * from micrositerel r, microsites s where r.siteid=s.siteid AND r.parent='".$siteid."' ORDER BY s.priority ASC;";
      $dbLink = new MYSQLAccess;
      return $dbLink->queryGetResults($sql);
   }

   function getOptionListFor($siteid,$recursive=FALSE,$paramname=NULL){
      if ($paramname==NULL) $paramname="s_siteid";
      if ($recursive) {
         $opts['All Locations'] = "-1";
         $opts = $this->getSiteOptions($siteid,0,$opts);
         //$extra = "onChange=\"window.location.href='admincontroller.php?action=welcome&s_siteid='+this.form.s_siteid.options[this.form.s_siteid.selectedIndex].value;\"";
         $extra = "onChange=\"window.location.href='".getBaseURL()."jsfcode/controller.php?s_siteid='+this.form.s_siteid.options[this.form.s_siteid.selectedIndex].value;\"";
         $sitearr = $this->getSiteContext();
         return getOptionList($paramname, $opts, $sitearr[0]['siteid'], FALSE, $extra);
      } else {
         $children = $this->getChildren($siteid);
         $opts = NULL;
         $opts['Choose Location'] = "-1";
         for ($i=0; $i<count($children); $i++) {
            $opts[$children[$i]['name']]=$children[$i]['siteid'];
         }
         return getOptionList($paramname, $opts, NULL, FALSE, $extra);
      }
   }

   function getSEOOptionListFor($siteid,$url){
      $opts['All Locations'] = "root";
      $opts = $this->getShortnameOptions($siteid,0,$opts);
      $replaceStr = "'+this.form.s_siteid.options[this.form.s_siteid.selectedIndex].value+'";
      $url = str_replace("%%%SHORTNAME%%%",$replaceStr,$url);
      $extra = "onChange=\"window.location.href='".$url."';\"";
      $sitearr = $this->getSiteContext();
      return getOptionList("s_siteid", $opts, $sitearr[0]['shortname'], FALSE, $extra);
   }

   function getVerticalImagesFor($siteid,$imagename="image1",$url=NULL){
      $children = $this->getChildren($siteid);
      $template = new Template();
      $returnStr = "<table cellpadding=\"0\" cellspacing=\"0\">\n";
      for ($i=0; $i<count($children); $i++) {
         if ($url==NULL) {
            $returnStr .= "<tr><td><a href=\"".$template->doBasicSubstitutions($children[$i]['site_url'])."\"><img src=\"".$GLOBALS['srvyURL'].$children[$i][$imagename]."\" border=\"0\"></a></td></tr>\n";
         } else {
            $returnStr .= "<tr><td><a href=\"".$url."&s_siteid=".$children[$i]['siteid']."\"><img src=\"".$GLOBALS['srvyURL'].$children[$i][$imagename]."\" border=\"0\"></a></td></tr>\n";
         }
         $returnStr .= "<tr><TD><IMG SRC=\"".getBaseURL().$GLOBALS['imagesDir']."pixel.gif\" WIDTH=\"1\" HEIGHT=\"15\"></TD></tr>\n";
      }
      $returnStr .= "</table>\n";
      return $returnStr;
   }

   function getSiteRel($siteid=-1,$siteArr=NULL,$siteArrElement=0){
      if ($siteid==-1) {
         $siteArr[$siteArrElement] = $this->getSiteInfo(-1);
         return $siteArr;
      } else if ($siteid==NULL) {
         return NULL;
      } else {
         $siteArr[$siteArrElement] = $this->getSiteInfo($siteid);
         return $this->getSiteRel($siteArr[$siteArrElement]['parent'],$siteArr,$siteArrElement+1);
      }
   }

   function getSiteByShortname($shortname){
      if (0==strcmp($shortname,"root")) {
         return $this->getSiteInfo(-1);
      } else {
         $dbLink = new MYSQLAccess;
         $query = "SELECT * FROM microsites WHERE LOWER(shortname)='".strtolower($shortname)."';";
         $results = $dbLink->queryGetResults($query);
         if ($results==NULL || count($results)<1) {
            $query = "SELECT * FROM microsites WHERE (";
            $query .= "LOWER(alternates)='".strtolower($shortname)."' ";
            $query .= "OR LOWER(alternates) LIKE '".convertString(strtolower($shortname).",")."%' ";
            $query .= "OR LOWER(alternates) LIKE '%".convertString(",".strtolower($shortname).",")."%' ";
            $query .= "OR LOWER(alternates) LIKE '%".convertString(",".strtolower($shortname))."'";
            $query .= ");";
            $results = $dbLink->queryGetResults($query);
         }
         return $results[0];
      }
   }

   function getSiteInfo($siteid=-1){
      if ($siteid==-1) {
         $site['name'] = "Root Shared Site";
         $site['shortname'] = "root";
         $site['descr'] = "Default site (root) inherited by all microsites.";
         $site['shortdescr'] = "Default site (root) inherited by all microsites.";
         $site['priority'] = "1";
         $site['siteid'] = "-1";
         return $site;
      } else {
         $sql = "Select * from microsites s, micrositerel r where r.siteid=s.siteid AND s.siteid='".$siteid."';";
         $dbLink = new MYSQLAccess;
         $sites = $dbLink->queryGetResults($sql);
         if ($sites==NULL || $sites[0]==NULL || $sites[0]['siteid']==NULL) {
            return $this->getSiteInfo(-1);
         }
         return $sites[0];
      }
   }

   function getSiteOptions($siteid=-1, $depth=0, $opts=NULL, $leavesOnly=FALSE, $buffer="--"){
      if ($siteid==-1 && !$leavesOnly) $opts['Root Shared Site'] = -1;
      $results = $this->getChildren($siteid);
      $prefix = "";
      for ($j=0; $j<=$depth; $j++) $prefix .= $buffer;
      for ($i=0; $i<count($results); $i++) {
         if ($leavesOnly) {
            $temp = $this->getChildren($results[$i]['siteid']);
            if ($temp==NULL || count($temp)<1) {
               $opts[$results[$i]['name']] = $results[$i]['siteid'];
            } else {
               $opts = $this->getSiteOptions($results[$i]['siteid'], $depth+1, $opts, TRUE, $buffer);
            }
         } else {
            $opts[$prefix.$results[$i]['name']] = $results[$i]['siteid'];
            $opts = $this->getSiteOptions($results[$i]['siteid'], $depth+1, $opts, FALSE, $buffer);
         }
      }
      return $opts;
   }

   function getAdminSiteOptions($userid, $siteid=-1, $depth=0, $opts=NULL){
      $opts = NULL;
      $ua = new UserAcct();
      $sites = $ua->getUsersAccessPointsFor($userid,"ADMINSITEID");
      if ($sites==NULL || count($sites)<1) {
         $opts = $this->getSiteOptions($siteid,$depth,$opts);
      } else {
         for ($i=0; $i<count($sites); $i++) {
            $siteid = $this->transformSiteid($sites[$i]['id']);
            $siteObj = $this->getSiteInfo($siteid);
            $opts[$siteObj['name']] = $siteObj['siteid'];
         }
      }
      return $opts;
   }

   function getShortnameOptions($siteid=-1, $depth=0, $opts=NULL){
      if ($siteid==-1) $opts['Root Shared Site'] = "root";
      $results = $this->getChildren($siteid);
      $prefix = "";
      for ($j=0; $j<=$depth; $j++) $prefix .= "--";
      for ($i=0; $i<count($results); $i++) {
         $opts[$prefix.$results[$i]['name']] = $results[$i]['shortname'];
         $opts = $this->getShortnameOptions($results[$i]['siteid'], $depth+1, $opts);
      }
      return $opts;
   }

    function displayHierarchy($siteid=-1,$depth=0, $rowId=2){
       $siteInfo = $this->getSiteInfo($siteid);

       if ($rowId == 2) $rowId=1; else $rowId=2;

       $name = $siteInfo['name'];
       $image="<img src=\"folder.gif\">";
       
       $addLink = " <a href=\"admincontroller.php?action=siteadd&parent=".$siteid;
       $addLink .= "\">[Add]</a> ";

       $removeLink = "admincontroller.php?action=siteremove&siteid=".$siteid;
       $removeLink = " <a href=\"".$removeLink."\" onClick=\"return confirm('Are you sure you want to delete this microsite?')\">[Remove]</a>";
       
       $editLink = " <a href=\"admincontroller.php?action=siteadd&siteid=".$siteid;
       $editLink .= "\">[Edit]</a> ";
       $links = $addLink.$removeLink.$editLink;
       
       print "<tr class=\"list_row".$rowId."\" id=\"item".$siteInfo['siteid']."\"><td>";
       for ($j=0; $j<$depth; $j++) print ".........";
       print $image."<B>".$name."</B>&nbsp;&nbsp;";
       print "</td>";
       print "<td> &nbsp; ".$siteInfo['shortdescr']." &nbsp;&nbsp; </td>";
       print "<td> &nbsp; ".$siteInfo['priority']." &nbsp;&nbsp; </td>";

       $template = new Template();
       print "<td><a href=\"".$template->doBasicSubstitutions($siteInfo['site_url'])."\" target=\"_new\">".$siteInfo['site_url']."</a></td>";
       print "<td align=\"right\">";
       print "<font size=-2>".$links."</font>";
       print "</td></tr>\n";
      
       $results = $this->getChildren($siteid);
       for ($i=0; $i<count($results); $i++) $rowId = $this->displayHierarchy($results[$i]['siteid'],$depth+1,$rowId);
       return $rowId;
    }
   
}

?>
