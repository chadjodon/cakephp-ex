<?php
$template = new Template();
$version = new Version();

$shortname = trim(getParameter("shortname"));
$versionNum = trim(getParameter("version"));

$contents = "";
if ($versionNum==NULL) {
   $contents = $version->getAsciiFileContents($shortname);
} else {
   $contents = $version->getVersionByShortname($shortname,$versionNum);
}

print $template->doSubstitutions($contents['contents']);
?>
