DROP TABLE IF EXISTS wd_0;
DROP TABLE IF EXISTS wd_1;
DROP TABLE IF EXISTS wd_2;
DROP TABLE IF EXISTS wd_3;
DROP TABLE IF EXISTS wd_4;
DROP TABLE IF EXISTS wd_5;
DROP TABLE IF EXISTS wd_6;
DROP TABLE IF EXISTS wd_7;
DROP TABLE IF EXISTS wd_8;
DROP TABLE IF EXISTS wd_9;
DROP TABLE IF EXISTS wd_10;
DROP TABLE IF EXISTS wd_11;
DROP TABLE IF EXISTS wd_12;
DROP TABLE IF EXISTS wd_13;
DROP TABLE IF EXISTS wd_14;
DROP TABLE IF EXISTS wd_15;
DROP TABLE IF EXISTS wd_16;
DROP TABLE IF EXISTS wd_17;
DROP TABLE IF EXISTS wd_18;
DROP TABLE IF EXISTS wd_19;
DROP TABLE IF EXISTS wd_20;
DROP TABLE IF EXISTS wd_21;
DROP TABLE IF EXISTS wd_22;
DROP TABLE IF EXISTS wd_23;
DROP TABLE IF EXISTS wd_24;
DROP TABLE IF EXISTS wd_25;
DROP TABLE IF EXISTS wd_26;
DROP TABLE IF EXISTS wd_27;
DROP TABLE IF EXISTS wd_28;
DROP TABLE IF EXISTS wd_29;

DROP TABLE IF EXISTS addr;
CREATE TABLE addr (
  addrid int(20) NOT NULL,
  userid bigint(20) DEFAULT NULL,
  email varchar(128) DEFAULT NULL,
  website varchar(255) DEFAULT NULL,
  phonenum varchar(32) DEFAULT NULL,
  phonenum2 varchar(32) DEFAULT NULL,
  phonenum3 varchar(32) DEFAULT NULL,
  phonenum4 varchar(32) DEFAULT NULL,
  addr1 varchar(64) DEFAULT NULL,
  addr2 varchar(64) DEFAULT NULL,
  city varchar(32) DEFAULT NULL,
  state char(2) DEFAULT NULL,
  zip varchar(11) DEFAULT NULL,
  country char(2) DEFAULT NULL,
  lat float(10,6) DEFAULT NULL,
  lng float(10,6) DEFAULT NULL
) ENGINE=MyISAM;

DROP TABLE IF EXISTS cmsadspc;
CREATE TABLE cmsadspc (
  adspaceid bigint(20) NOT NULL,
  adspcname varchar(128) DEFAULT NULL,
  adspctype varchar(128) DEFAULT NULL
) ENGINE=MyISAM;

DROP TABLE IF EXISTS cmsasdef;
CREATE TABLE cmsasdef (
  asdefid bigint(20) NOT NULL,
  adspaceid bigint(20) DEFAULT NULL,
  cmsid bigint(20) DEFAULT NULL,
  percent int(11) DEFAULT NULL,
  priority int(11) DEFAULT '1',
  status varchar(8) DEFAULT 'INACTIVE'
) ENGINE=MyISAM;

DROP TABLE IF EXISTS cmsfdes;
CREATE TABLE cmsfdes (
  divid bigint(20) NOT NULL,
  created datetime DEFAULT NULL,
  cmsid bigint(20) DEFAULT NULL,
  version int(11) NOT NULL DEFAULT '0',
  divtop int(11) NOT NULL DEFAULT '0',
  divleft int(11) NOT NULL DEFAULT '0',
  divwidth int(11) NOT NULL DEFAULT '0',
  divheight int(11) NOT NULL DEFAULT '0',
  bgcolor varchar(8) DEFAULT NULL,
  bgimage varchar(255) DEFAULT NULL,
  borderw tinyint(4) DEFAULT '1',
  borderc varchar(8) DEFAULT NULL,
  zindex int(11) DEFAULT NULL,
  contentref varchar(255) DEFAULT NULL,
  url varchar(255) DEFAULT NULL,
  label varchar(32) DEFAULT NULL,
  status tinyint(4) DEFAULT '1',
  origdivid bigint(20) DEFAULT NULL,
  style text,
  fixed tinyint(4) DEFAULT '0'
) ENGINE=MyISAM;

DROP TABLE IF EXISTS cmsfiles;
CREATE TABLE cmsfiles (
  cmsid bigint(20) NOT NULL,
  dir varchar(128) DEFAULT NULL,
  filename varchar(128) DEFAULT NULL,
  extension varchar(8) DEFAULT NULL,
  title varchar(128) DEFAULT NULL,
  filetype varchar(8) DEFAULT 'TEXT',
  contenttype int(11) DEFAULT NULL,
  privacy int(11) DEFAULT '0',
  xmp varchar(128) DEFAULT NULL,
  xmp_full varchar(128) DEFAULT NULL,
  cachetime int(11) DEFAULT '0',
  track tinyint(4) DEFAULT '0',
  htags text
) ENGINE=MyISAM;

DROP TABLE IF EXISTS cmsftemp;
CREATE TABLE cmsftemp (
  divid bigint(20) NOT NULL,
  created datetime DEFAULT NULL,
  tempcmsid bigint(20) DEFAULT NULL,
  cmsid bigint(20) NOT NULL,
  version int(11) NOT NULL DEFAULT '0',
  bgcolor varchar(8) DEFAULT NULL,
  url varchar(255) DEFAULT NULL,
  bgimage varchar(255) DEFAULT NULL,
  contentref varchar(255) DEFAULT NULL
) ENGINE=MyISAM;

DROP TABLE IF EXISTS cmsfver;
CREATE TABLE cmsfver (
  cmsid bigint(20) NOT NULL,
  siteid bigint(20) NOT NULL DEFAULT '-1',
  version int(11) NOT NULL DEFAULT '0',
  search text,
  metakw varchar(254) DEFAULT NULL,
  metadescr varchar(254) DEFAULT NULL,
  title varchar(128) DEFAULT NULL,
  created datetime DEFAULT NULL,
  lastupdate datetime DEFAULT NULL,
  status varchar(8) DEFAULT 'NEW',
  owner varchar(128) DEFAULT NULL,
  lastupdateby varchar(128) DEFAULT NULL,
  adminnotes text,
  theme int(11) DEFAULT '0'
) ENGINE=MyISAM;

DROP TABLE IF EXISTS cmsfwdg;
CREATE TABLE cmsfwdg (
  widgetid bigint(20) NOT NULL,
  objectname varchar(32) DEFAULT NULL,
  displayname varchar(128) DEFAULT NULL,
  filetype varchar(8) DEFAULT 'TEXT',
  descr text
) ENGINE=MyISAM;

DROP TABLE IF EXISTS cmsrules;
CREATE TABLE cmsrules (
  ruleid bigint(20) NOT NULL,
  themeid bigint(20) DEFAULT NULL,
  asdefid bigint(20) DEFAULT NULL,
  ruletype varchar(128) DEFAULT NULL,
  field1 varchar(128) DEFAULT NULL,
  field2 varchar(128) DEFAULT NULL,
  field3 varchar(128) DEFAULT NULL,
  field4 varchar(128) DEFAULT NULL,
  field5 varchar(128) DEFAULT NULL
) ENGINE=MyISAM;

DROP TABLE IF EXISTS cmstheme;
CREATE TABLE cmstheme (
  themeid bigint(20) NOT NULL,
  themename varchar(128) DEFAULT NULL,
  priority int(11) DEFAULT '1',
  startday int(11) DEFAULT '1',
  endday int(11) DEFAULT '383',
  status varchar(8) DEFAULT 'INACTIVE'
) ENGINE=MyISAM;

DROP TABLE IF EXISTS dbcache;
CREATE TABLE dbcache (
  dbcid bigint(20) NOT NULL,
  created datetime DEFAULT NULL,
  sqlstr text,
  sqlresults mediumtext,
  field1 bigint(20) NOT NULL DEFAULT '0',
  field2 varchar(255) DEFAULT NULL,
  field3 text
) ENGINE=MyISAM;

DROP TABLE IF EXISTS globals;
CREATE TABLE globals (
  name varchar(50) NOT NULL DEFAULT '',
  value varchar(254) DEFAULT NULL,
  themeid bigint(20) NOT NULL,
  siteid bigint(20) NOT NULL DEFAULT '-1'
) ENGINE=MyISAM;

INSERT INTO globals (name, value, themeid, siteid) VALUES
('bottomtemplate', 'bottom', 0, -1),
('toptemplate', 'top', 0, -1),
('errortemplate', 'error', 0, -1),
('tagbegindata', 'BEGINDATA', 0, -1),
('tagcms', 'CMS', 0, -1),
('tagcmslink', 'CMSLINK', 0, -1),
('tagdata', 'DATA', 0, -1),
('tagdatacol', 'DATACOL', 0, -1),
('tagtitlecol', 'DATATITLE', 0, -1),
('tagenddata', 'ENDDATA', 0, -1),
('tagphp', 'PHP', 0, -1),
('tagprop', 'PROP', 0, -1),
('tagparam', 'PARAM', 0, -1),
('tagstarter', '%%%', 0, -1),
('tagtitle', 'TITLE', 0, -1),
('tagurl', 'URL', 0, -1),
('WebsiteContact', 'help@jstorefront.com', 0, -1),
('tagrss', 'RSSTPL', 0, -1),
('tagrssfeed', 'RSSFEED', 0, -1),
('tagsearch', 'DATASEARCH', 0, -1),
('defaultTitle', 'Innovation HUB', 0, -1),
('jsfversion', '1.00.02.74', 0, -1),
('multisites', '1', 0, -1),
('RequireActivation', '0', 0, -1),
('AddLevel1ToNewUsers', '0', 0, -1),
('multisitesuseremails', '1', 0, -1),
('adminTop', 'server/admin/top_management.php', 0, -1),
('adminBottom', 'server/admin/bottom_management.php', 0, -1),
('adminWelcome', 'server/admin/dashboard.php', 0, -1),
('topsurvey', 'topsurvey', 0, -1),
('bottomsurvey', 'bottomsurvey', 0, -1);



DROP TABLE IF EXISTS glosprnt;
CREATE TABLE glosprnt (
  glossid int(11) NOT NULL,
  glosstitle varchar(64) DEFAULT NULL,
  descr text
) ENGINE=MyISAM;

DROP TABLE IF EXISTS glossary;
CREATE TABLE glossary (
  glossaryid bigint(20) NOT NULL DEFAULT '0',
  term varchar(128) NOT NULL DEFAULT '',
  definition text,
  alternates text
) ENGINE=MyISAM;

DROP TABLE IF EXISTS micrositerel;
CREATE TABLE micrositerel (
  siteid bigint(20) NOT NULL DEFAULT '-1',
  parent bigint(20) NOT NULL DEFAULT '-1',
  reltype varchar(128) DEFAULT NULL
) ENGINE=MyISAM;

DROP TABLE IF EXISTS microsites;
CREATE TABLE microsites (
  siteid bigint(20) NOT NULL,
  priority int(11) DEFAULT '1',
  name varchar(128) DEFAULT NULL,
  metadescr varchar(255) DEFAULT NULL,
  keywords varchar(255) DEFAULT NULL,
  shortname varchar(128) DEFAULT NULL,
  alternates varchar(255) DEFAULT NULL,
  shortdescr text,
  descr text,
  site_url varchar(255) DEFAULT NULL,
  site_type int(11) NOT NULL DEFAULT '1',
  image1 varchar(255) DEFAULT NULL,
  image2 varchar(255) DEFAULT NULL,
  image3 varchar(255) DEFAULT NULL,
  image4 varchar(255) DEFAULT NULL,
  image5 varchar(255) DEFAULT NULL
) ENGINE=MyISAM;

DROP TABLE IF EXISTS referral;
CREATE TABLE referral (
  refid int(20) NOT NULL,
  refuserid bigint(20) DEFAULT NULL,
  newuserid bigint(20) DEFAULT NULL,
  created datetime DEFAULT NULL,
  adminnotes text
) ENGINE=MyISAM;

DROP TABLE IF EXISTS schedemail;
CREATE TABLE schedemail (
  semailid bigint(20) NOT NULL,
  schedid bigint(20) DEFAULT NULL,
  cmsid bigint(20) DEFAULT NULL,
  userid bigint(20) DEFAULT NULL,
  status varchar(32) DEFAULT NULL,
  timesent datetime DEFAULT NULL,
  timeadded datetime DEFAULT NULL,
  content text,
  subject varchar(128) DEFAULT NULL,
  priority bigint(20) DEFAULT '10',
  field1 bigint(20) DEFAULT NULL,
  field2 bigint(20) DEFAULT NULL,
  field3 varchar(128) DEFAULT NULL,
  field4 varchar(255) DEFAULT NULL,
  field5 text,
  field6 varchar(128) DEFAULT NULL,
  starton datetime DEFAULT NULL,
  field7 datetime DEFAULT NULL,
  classname varchar(255) DEFAULT NULL,
  phpobj text,
  phpfile varchar(255) DEFAULT NULL,
  resched varchar(16) DEFAULT NULL
) ENGINE=MyISAM;

DROP TABLE IF EXISTS schedjobs;
CREATE TABLE schedjobs (
  schedid bigint(20) NOT NULL,
  created datetime DEFAULT NULL,
  starttime datetime DEFAULT NULL,
  started datetime DEFAULT NULL,
  ended datetime DEFAULT NULL,
  byuserid bigint(20) DEFAULT NULL,
  typestr varchar(32) DEFAULT NULL,
  status varchar(16) DEFAULT NULL,
  tinterval1 bigint(20) DEFAULT NULL,
  tinterval2 bigint(20) DEFAULT NULL,
  ninterval1 bigint(20) DEFAULT NULL,
  ninterval2 bigint(20) DEFAULT NULL,
  field1 bigint(20) DEFAULT NULL,
  field2 bigint(20) DEFAULT NULL,
  field3 varchar(128) DEFAULT NULL,
  field4 varchar(128) DEFAULT NULL,
  field5 varchar(128) DEFAULT NULL,
  field6 varchar(128) DEFAULT NULL,
  field7 text
) ENGINE=MyISAM;

DROP TABLE IF EXISTS tracker;
CREATE TABLE tracker (
  trkid bigint(20) NOT NULL,
  created datetime DEFAULT NULL,
  userid bigint(20) DEFAULT NULL,
  view varchar(128) DEFAULT NULL,
  action varchar(128) DEFAULT NULL,
  jsftrack1 varchar(255) DEFAULT NULL,
  jsftrack2 varchar(255) DEFAULT NULL,
  jsftrack3 varchar(255) DEFAULT NULL,
  referer varchar(255) DEFAULT NULL,
  agent varchar(255) DEFAULT NULL,
  sessionid varchar(128) DEFAULT NULL,
  ipaddr varchar(64) DEFAULT NULL,
  country varchar(5) DEFAULT NULL,
  region varchar(5) DEFAULT NULL,
  city varchar(128) DEFAULT NULL,
  lat float(10,6) DEFAULT NULL,
  lng float(10,6) DEFAULT NULL,
  postal varchar(10) DEFAULT NULL
) ENGINE=MyISAM;

DROP TABLE IF EXISTS trackerarch;
CREATE TABLE trackerarch (
  archid bigint(20) NOT NULL,
  trkid bigint(20) DEFAULT NULL,
  created datetime DEFAULT NULL,
  userid bigint(20) DEFAULT NULL,
  view varchar(128) DEFAULT NULL,
  action varchar(128) DEFAULT NULL,
  jsftrack1 varchar(255) DEFAULT NULL,
  jsftrack2 varchar(255) DEFAULT NULL,
  jsftrack3 varchar(255) DEFAULT NULL,
  referer varchar(255) DEFAULT NULL,
  agent varchar(255) DEFAULT NULL,
  sessionid varchar(128) DEFAULT NULL,
  ipaddr varchar(64) DEFAULT NULL,
  country varchar(5) DEFAULT NULL,
  region varchar(5) DEFAULT NULL,
  city varchar(128) DEFAULT NULL,
  lat float(10,6) DEFAULT NULL,
  lng float(10,6) DEFAULT NULL,
  scan1 tinyint(4) DEFAULT NULL,
  scan2 tinyint(4) DEFAULT NULL,
  scan3 tinyint(4) DEFAULT NULL,
  postal varchar(10) DEFAULT NULL
) ENGINE=InnoDB;

DROP TABLE IF EXISTS trackerref;
CREATE TABLE trackerref (
  trid bigint(20) NOT NULL,
  tref varchar(255) DEFAULT NULL,
  reftype varchar(32) DEFAULT NULL,
  counter bigint(20) DEFAULT '0',
  refmonth datetime DEFAULT NULL
) ENGINE=MyISAM;

DROP TABLE IF EXISTS trackerstats;
CREATE TABLE trackerstats (
  trkhour datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  trkstat varchar(8) NOT NULL DEFAULT '',
  created datetime DEFAULT NULL,
  visits int(11) DEFAULT '0',
  clkthru int(11) DEFAULT '0',
  newuser int(11) DEFAULT '0',
  google int(11) DEFAULT '0',
  facebook int(11) DEFAULT '0',
  yahoo int(11) DEFAULT '0',
  bing int(11) DEFAULT '0',
  pinterest int(11) DEFAULT '0',
  ask int(11) DEFAULT '0',
  windows int(11) DEFAULT '0',
  macintosh int(11) DEFAULT '0',
  linux int(11) DEFAULT '0',
  bberry int(11) DEFAULT '0',
  ios int(11) DEFAULT '0',
  android int(11) DEFAULT '0',
  wince int(11) DEFAULT '0',
  stAB int(11) DEFAULT '0',
  stAK int(11) DEFAULT '0',
  stAL int(11) DEFAULT '0',
  stAR int(11) DEFAULT '0',
  stAZ int(11) DEFAULT '0',
  stBC int(11) DEFAULT '0',
  stCA int(11) DEFAULT '0',
  stCO int(11) DEFAULT '0',
  stCT int(11) DEFAULT '0',
  stDC int(11) DEFAULT '0',
  stDE int(11) DEFAULT '0',
  stFL int(11) DEFAULT '0',
  stGA int(11) DEFAULT '0',
  stHI int(11) DEFAULT '0',
  stIA int(11) DEFAULT '0',
  stID int(11) DEFAULT '0',
  stIL int(11) DEFAULT '0',
  stIN int(11) DEFAULT '0',
  stKS int(11) DEFAULT '0',
  stKY int(11) DEFAULT '0',
  stLA int(11) DEFAULT '0',
  stMA int(11) DEFAULT '0',
  stMB int(11) DEFAULT '0',
  stMD int(11) DEFAULT '0',
  stME int(11) DEFAULT '0',
  stMI int(11) DEFAULT '0',
  stMO int(11) DEFAULT '0',
  stMN int(11) DEFAULT '0',
  stMS int(11) DEFAULT '0',
  stMT int(11) DEFAULT '0',
  stNB int(11) DEFAULT '0',
  stNC int(11) DEFAULT '0',
  stND int(11) DEFAULT '0',
  stNE int(11) DEFAULT '0',
  stNH int(11) DEFAULT '0',
  stNJ int(11) DEFAULT '0',
  stNL int(11) DEFAULT '0',
  stNM int(11) DEFAULT '0',
  stNS int(11) DEFAULT '0',
  stNT int(11) DEFAULT '0',
  stNU int(11) DEFAULT '0',
  stNV int(11) DEFAULT '0',
  stNY int(11) DEFAULT '0',
  stOH int(11) DEFAULT '0',
  stOK int(11) DEFAULT '0',
  stON int(11) DEFAULT '0',
  stOR int(11) DEFAULT '0',
  stPA int(11) DEFAULT '0',
  stPE int(11) DEFAULT '0',
  stPR int(11) DEFAULT '0',
  stQC int(11) DEFAULT '0',
  stRI int(11) DEFAULT '0',
  stSC int(11) DEFAULT '0',
  stSD int(11) DEFAULT '0',
  stSK int(11) DEFAULT '0',
  stTN int(11) DEFAULT '0',
  stTX int(11) DEFAULT '0',
  stUT int(11) DEFAULT '0',
  stVI int(11) DEFAULT '0',
  stVT int(11) DEFAULT '0',
  stVA int(11) DEFAULT '0',
  stWA int(11) DEFAULT '0',
  stWI int(11) DEFAULT '0',
  stWV int(11) DEFAULT '0',
  stWY int(11) DEFAULT '0',
  stYT int(11) DEFAULT '0',
  field1 int(11) DEFAULT '0',
  field2 int(11) DEFAULT '0',
  field3 int(11) DEFAULT '0',
  field4 int(11) DEFAULT '0',
  field5 int(11) DEFAULT '0',
  field6 int(11) DEFAULT '0',
  field7 varchar(255) DEFAULT NULL,
  field8 text
) ENGINE=MyISAM;

DROP TABLE IF EXISTS useraccess;
CREATE TABLE useraccess (
  userid bigint(20) DEFAULT NULL,
  sys varchar(64) DEFAULT NULL,
  id varchar(64) DEFAULT NULL
) ENGINE=MyISAM;

DROP TABLE IF EXISTS useracct;
CREATE TABLE useracct (
  userid bigint(20) NOT NULL,
  siteid bigint(20) DEFAULT NULL,
  alive tinyint(4) NOT NULL DEFAULT '1',
  activated tinyint(4) NOT NULL DEFAULT '0',
  usertype varchar(32) NOT NULL DEFAULT 'user',
  refsrc varchar(128) DEFAULT NULL,
  email varchar(128) DEFAULT NULL,
  username varchar(128) DEFAULT NULL,
  email2 varchar(128) DEFAULT NULL,
  fname varchar(64) DEFAULT NULL,
  mname varchar(64) DEFAULT NULL,
  lname varchar(64) DEFAULT NULL,
  company varchar(255) DEFAULT NULL,
  title varchar(255) DEFAULT NULL,
  parentid bigint(20) DEFAULT NULL,
  parentid2 bigint(20) DEFAULT NULL,
  addrid int(11) DEFAULT NULL,
  password varchar(64) DEFAULT NULL,
  password2 varchar(64) DEFAULT NULL,
  ownersite varchar(255) DEFAULT NULL,
  nletter varchar(128) DEFAULT NULL,
  other varchar(255) DEFAULT NULL,
  notes text,
  ulevel int(11) DEFAULT NULL,
  orgid int(11) DEFAULT NULL,
  age varchar(4) DEFAULT NULL,
  gender char(2) DEFAULT NULL,
  edu char(2) DEFAULT NULL,
  marital char(2) DEFAULT NULL,
  created date DEFAULT NULL,
  lastupdated date DEFAULT NULL,
  login date DEFAULT NULL,
  lastlogin date DEFAULT NULL,
  field1 varchar(128) DEFAULT NULL,
  field2 varchar(128) DEFAULT NULL,
  field3 varchar(128) DEFAULT NULL,
  field4 bigint(20) DEFAULT NULL,
  field5 bigint(20) DEFAULT NULL,
  field6 bigint(20) DEFAULT NULL,
  website varchar(255) DEFAULT NULL,
  phonenum varchar(32) DEFAULT NULL,
  phonenum2 varchar(32) DEFAULT NULL,
  phonenum3 varchar(32) DEFAULT NULL,
  phonenum4 varchar(32) DEFAULT NULL,
  addr1 varchar(64) DEFAULT NULL,
  addr2 varchar(64) DEFAULT NULL,
  city varchar(32) DEFAULT NULL,
  state char(2) DEFAULT NULL,
  zip varchar(11) DEFAULT NULL,
  country char(2) DEFAULT NULL,
  lat float(10,6) DEFAULT NULL,
  lng float(10,6) DEFAULT NULL,
  emailflag tinyint(4) DEFAULT NULL,
  token varchar(128) DEFAULT NULL,
  dbmode varchar(8) DEFAULT 'NEW',
  activatedstr varchar(32) DEFAULT NULL,
  lastupdatedby text,
  lastverified date DEFAULT NULL
) ENGINE=MyISAM;

DROP TABLE IF EXISTS useracct_pub;
CREATE TABLE useracct_pub (
  userid bigint(20) NOT NULL,
  dbmode varchar(8) DEFAULT 'NEW',
  siteid bigint(20) DEFAULT NULL,
  alive tinyint(4) NOT NULL DEFAULT '1',
  emailflag tinyint(4) DEFAULT NULL,
  activated tinyint(4) NOT NULL DEFAULT '0',
  usertype varchar(32) NOT NULL DEFAULT 'user',
  refsrc varchar(128) DEFAULT NULL,
  email varchar(128) DEFAULT NULL,
  username varchar(128) DEFAULT NULL,
  email2 varchar(128) DEFAULT NULL,
  fname varchar(64) DEFAULT NULL,
  mname varchar(64) DEFAULT NULL,
  lname varchar(64) DEFAULT NULL,
  company varchar(255) DEFAULT NULL,
  title varchar(255) DEFAULT NULL,
  parentid bigint(20) DEFAULT NULL,
  parentid2 bigint(20) DEFAULT NULL,
  addrid int(11) DEFAULT NULL,
  password varchar(64) DEFAULT NULL,
  password2 varchar(64) DEFAULT NULL,
  ownersite varchar(255) DEFAULT NULL,
  nletter varchar(128) DEFAULT NULL,
  other varchar(255) DEFAULT NULL,
  notes text,
  ulevel int(11) DEFAULT NULL,
  orgid int(11) DEFAULT NULL,
  age varchar(4) DEFAULT NULL,
  gender char(2) DEFAULT NULL,
  edu char(2) DEFAULT NULL,
  marital char(2) DEFAULT NULL,
  created date DEFAULT NULL,
  lastupdated date DEFAULT NULL,
  login date DEFAULT NULL,
  lastlogin date DEFAULT NULL,
  token varchar(128) DEFAULT NULL,
  field1 varchar(128) DEFAULT NULL,
  field2 varchar(128) DEFAULT NULL,
  field3 varchar(128) DEFAULT NULL,
  field4 bigint(20) DEFAULT NULL,
  field5 bigint(20) DEFAULT NULL,
  field6 bigint(20) DEFAULT NULL,
  website varchar(255) DEFAULT NULL,
  phonenum varchar(32) DEFAULT NULL,
  phonenum2 varchar(32) DEFAULT NULL,
  phonenum3 varchar(32) DEFAULT NULL,
  phonenum4 varchar(32) DEFAULT NULL,
  addr1 varchar(64) DEFAULT NULL,
  addr2 varchar(64) DEFAULT NULL,
  city varchar(32) DEFAULT NULL,
  state char(2) DEFAULT NULL,
  zip varchar(11) DEFAULT NULL,
  country char(2) DEFAULT NULL,
  lat float(10,6) DEFAULT NULL,
  lng float(10,6) DEFAULT NULL,
  activatedstr varchar(32) DEFAULT NULL,
  lastupdatedby text,
  lastverified date DEFAULT NULL
) ENGINE=MyISAM;

DROP TABLE IF EXISTS usermsg;
CREATE TABLE usermsg (
  messageid bigint(20) NOT NULL,
  userid bigint(20) DEFAULT NULL,
  sender varchar(255) DEFAULT NULL,
  subject varchar(255) DEFAULT NULL,
  content text,
  url varchar(255) DEFAULT NULL,
  msgtype varchar(32) DEFAULT NULL,
  complete tinyint(4) NOT NULL DEFAULT '0',
  created datetime DEFAULT NULL,
  opened datetime DEFAULT NULL,
  externalid varchar(128) DEFAULT NULL,
  field1 varchar(128) DEFAULT NULL,
  field2 varchar(128) DEFAULT NULL,
  field3 varchar(128) DEFAULT NULL,
  field4 varchar(128) DEFAULT NULL,
  field5 varchar(128) DEFAULT NULL
) ENGINE=MyISAM;

DROP TABLE IF EXISTS userpost;
CREATE TABLE userpost (
  postid bigint(20) NOT NULL,
  status varchar(16) DEFAULT NULL,
  posttype varchar(16) DEFAULT NULL,
  refid varchar(32) DEFAULT NULL,
  userid bigint(20) NOT NULL DEFAULT '-1',
  quickie tinyint(4) NOT NULL DEFAULT '0',
  title varchar(255) DEFAULT NULL,
  content text,
  visibility tinyint(4) DEFAULT NULL,
  category varchar(255) DEFAULT NULL,
  created datetime DEFAULT NULL,
  updated datetime DEFAULT NULL,
  field1 bigint(20) NOT NULL DEFAULT '0',
  field2 bigint(20) NOT NULL DEFAULT '0',
  field3 varchar(255) DEFAULT NULL,
  field4 text,
  docdate datetime DEFAULT NULL
) ENGINE=MyISAM;

DROP TABLE IF EXISTS userrel;
CREATE TABLE userrel (
  userrel_id bigint(20) NOT NULL,
  userid bigint(20) NOT NULL DEFAULT '0',
  reluserid bigint(20) NOT NULL DEFAULT '0',
  field1 varchar(128) DEFAULT NULL,
  field2 varchar(128) DEFAULT NULL,
  rel_type varchar(10) DEFAULT NULL
) ENGINE=MyISAM;

DROP TABLE IF EXISTS userrel_pub;
CREATE TABLE userrel_pub (
  userrel_id bigint(20) NOT NULL,
  userid bigint(20) NOT NULL DEFAULT '0',
  reluserid bigint(20) NOT NULL DEFAULT '0',
  field1 varchar(128) DEFAULT NULL,
  field2 varchar(128) DEFAULT NULL,
  rel_type varchar(10) DEFAULT NULL
) ENGINE=MyISAM;

DROP TABLE IF EXISTS userseggroup;
CREATE TABLE userseggroup (
  seggroupid bigint(20) NOT NULL,
  parentid bigint(20) NOT NULL DEFAULT '-1',
  name varchar(128) DEFAULT NULL
) ENGINE=MyISAM;

DROP TABLE IF EXISTS usersegment;
CREATE TABLE usersegment (
  segmentid bigint(20) NOT NULL,
  name varchar(128) DEFAULT NULL,
  descr text,
  seggroupid bigint(20) NOT NULL DEFAULT '-1',
  dropdown tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=MyISAM;

DROP TABLE IF EXISTS usersegnvp;
CREATE TABLE usersegnvp (
  segmentid bigint(20) NOT NULL,
  name varchar(128) NOT NULL,
  value varchar(128) NOT NULL DEFAULT ''
) ENGINE=MyISAM;

DROP TABLE IF EXISTS wd_fields;
CREATE TABLE wd_fields (
  field_id varchar(10) NOT NULL DEFAULT '',
  parent_s int(11) DEFAULT '-1',
  sequence int(11) UNSIGNED NOT NULL DEFAULT '0',
  wd_id int(20) UNSIGNED NOT NULL DEFAULT '0',
  label text,
  field_type varchar(10) DEFAULT NULL,
  question text,
  defaultval varchar(255) DEFAULT NULL,
  privacy int(10) NOT NULL DEFAULT '0',
  header tinyint(4) NOT NULL DEFAULT '0',
  required tinyint(4) NOT NULL DEFAULT '0',
  srchfld tinyint(4) DEFAULT '0',
  notes text,
  filterfld tinyint(4) DEFAULT '0',
  stylecss varchar(255) DEFAULT NULL,
  map varchar(64) DEFAULT NULL,
  disa tinyint(4) DEFAULT '0',
  hide tinyint(4) DEFAULT '0'
) ENGINE=MyISAM;

DROP TABLE IF EXISTS wd_fldpos;
CREATE TABLE wd_fldpos (
  posid int(20) UNSIGNED NOT NULL,
  groupname varchar(32) DEFAULT NULL,
  field_id varchar(10) DEFAULT NULL,
  wd_id int(20) UNSIGNED NOT NULL DEFAULT '0',
  leftpos int(11) DEFAULT NULL,
  toppos int(11) DEFAULT NULL,
  rightpos int(11) DEFAULT NULL,
  bottompos int(11) DEFAULT NULL,
  width int(11) DEFAULT NULL,
  height int(11) DEFAULT NULL,
  notes text,
  disa tinyint(4) DEFAULT '0',
  defval text,
  adminresp varchar(128) DEFAULT NULL,
  statusind varchar(16) DEFAULT NULL,
  instructions text,
  subname varchar(128) DEFAULT NULL,
  shortdescr text,
  longdescr text,
  unit varchar(10) DEFAULT NULL,
  disptype varchar(64) DEFAULT NULL,
  params text
) ENGINE=MyISAM;

DROP TABLE IF EXISTS wd_link;
CREATE TABLE wd_link (
  linkid int(20) UNSIGNED NOT NULL,
  wd_id1 int(20) UNSIGNED NOT NULL DEFAULT '0',
  wd_row_id1 int(20) UNSIGNED NOT NULL DEFAULT '0',
  field_id varchar(10) NOT NULL DEFAULT '',
  wd_id2 int(20) UNSIGNED NOT NULL DEFAULT '0',
  wd_row_id2 int(20) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM;

DROP TABLE IF EXISTS wd_rel;
CREATE TABLE wd_rel (
  rel_id int(20) NOT NULL,
  wd_id int(20) UNSIGNED NOT NULL DEFAULT '0',
  rel_type varchar(10) DEFAULT NULL,
  fid1 varchar(10) DEFAULT NULL,
  fid2 varchar(10) DEFAULT NULL,
  f1value varchar(255) DEFAULT NULL
) ENGINE=MyISAM;

DROP TABLE IF EXISTS wd_sections;
CREATE TABLE wd_sections (
  section int(20) NOT NULL,
  parent_s int(11) DEFAULT '-1',
  sec_type varchar(10) DEFAULT NULL,
  wd_id int(20) UNSIGNED NOT NULL DEFAULT '0',
  sequence int(11) UNSIGNED NOT NULL DEFAULT '0',
  label text,
  dyna int(11) DEFAULT '0',
  question varchar(255) DEFAULT NULL,
  param1 int(11) DEFAULT NULL,
  param2 int(11) DEFAULT NULL,
  param3 int(11) DEFAULT NULL,
  param4 int(11) DEFAULT NULL,
  param5 varchar(64) DEFAULT NULL,
  param6 varchar(64) DEFAULT NULL
) ENGINE=MyISAM;

DROP TABLE IF EXISTS webdata;
CREATE TABLE webdata (
  wd_id int(20) UNSIGNED NOT NULL,
  siteid bigint(20) NOT NULL DEFAULT '-1',
  name varchar(255) DEFAULT NULL,
  shortname varchar(64) DEFAULT NULL,
  version varchar(10) DEFAULT NULL,
  privatesrvy int(11) DEFAULT '2',
  saveresults int(11) DEFAULT '0',
  emailresults int(11) DEFAULT '0',
  adminemail varchar(255) DEFAULT NULL,
  filename varchar(128) DEFAULT NULL,
  info text,
  lastmod date DEFAULT NULL,
  status varchar(16) DEFAULT 'NEW',
  createdon datetime DEFAULT NULL,
  glossaryid int(11) NOT NULL DEFAULT '0',
  starttime datetime DEFAULT NULL,
  endtime datetime DEFAULT NULL,
  externalid bigint(20) DEFAULT NULL,
  field1 varchar(128) DEFAULT NULL,
  field2 varchar(128) DEFAULT NULL,
  field3 varchar(128) DEFAULT NULL,
  field4 text,
  password varchar(64) DEFAULT NULL,
  captcha tinyint(4) DEFAULT '0',
  userrel varchar(10) DEFAULT NULL,
  esign tinyint(4) DEFAULT '0',
  htags text,
  usertype varchar(32) DEFAULT NULL,
  rowdisplay text,
  sequence int(11) DEFAULT NULL
) ENGINE=MyISAM;


ALTER TABLE addr
  ADD PRIMARY KEY (addrid);

ALTER TABLE cmsadspc
  ADD PRIMARY KEY (adspaceid);

ALTER TABLE cmsasdef
  ADD PRIMARY KEY (asdefid);

ALTER TABLE cmsfdes
  ADD PRIMARY KEY (divid);

ALTER TABLE cmsfiles
  ADD PRIMARY KEY (cmsid);

ALTER TABLE cmsftemp
  ADD PRIMARY KEY (divid,cmsid,version);

ALTER TABLE cmsfver
  ADD PRIMARY KEY (cmsid,version);

ALTER TABLE cmsfwdg
  ADD PRIMARY KEY (widgetid);

ALTER TABLE cmsrules
  ADD PRIMARY KEY (ruleid);

ALTER TABLE cmstheme
  ADD PRIMARY KEY (themeid);

ALTER TABLE dbcache
  ADD PRIMARY KEY (dbcid);

ALTER TABLE globals
  ADD PRIMARY KEY (themeid,name,siteid);

ALTER TABLE glosprnt
  ADD PRIMARY KEY (glossid);

ALTER TABLE glossary
  ADD PRIMARY KEY (glossaryid,term);

ALTER TABLE micrositerel
  ADD PRIMARY KEY (siteid,parent);

ALTER TABLE microsites
  ADD PRIMARY KEY (siteid);

ALTER TABLE referral
  ADD PRIMARY KEY (refid);

ALTER TABLE schedemail
  ADD PRIMARY KEY (semailid);

ALTER TABLE schedjobs
  ADD PRIMARY KEY (schedid);

ALTER TABLE tracker
  ADD PRIMARY KEY (trkid);

ALTER TABLE trackerarch
  ADD PRIMARY KEY (archid);

ALTER TABLE trackerref
  ADD PRIMARY KEY (trid);

ALTER TABLE trackerstats
  ADD PRIMARY KEY (trkhour,trkstat);

ALTER TABLE useracct
  ADD PRIMARY KEY (userid);

ALTER TABLE useracct_pub
  ADD PRIMARY KEY (userid);

ALTER TABLE usermsg
  ADD PRIMARY KEY (messageid);

ALTER TABLE userpost
  ADD PRIMARY KEY (postid);

ALTER TABLE userrel
  ADD PRIMARY KEY (userrel_id);

ALTER TABLE userrel_pub
  ADD PRIMARY KEY (userrel_id);

ALTER TABLE userseggroup
  ADD PRIMARY KEY (seggroupid);

ALTER TABLE usersegment
  ADD PRIMARY KEY (segmentid);

ALTER TABLE usersegnvp
  ADD PRIMARY KEY (segmentid,name,value);

ALTER TABLE wd_fields
  ADD PRIMARY KEY (wd_id,field_id);

ALTER TABLE wd_fldpos
  ADD PRIMARY KEY (posid);

ALTER TABLE wd_link
  ADD PRIMARY KEY (linkid);

ALTER TABLE wd_rel
  ADD PRIMARY KEY (rel_id);

ALTER TABLE wd_sections
  ADD PRIMARY KEY (section,wd_id);

ALTER TABLE webdata
  ADD PRIMARY KEY (wd_id);


ALTER TABLE addr
  MODIFY addrid int(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE cmsadspc
  MODIFY adspaceid bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE cmsasdef
  MODIFY asdefid bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE cmsfdes
  MODIFY divid bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE cmsfiles
  MODIFY cmsid bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE cmsfwdg
  MODIFY widgetid bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE cmsrules
  MODIFY ruleid bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE cmstheme
  MODIFY themeid bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE dbcache
  MODIFY dbcid bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE glosprnt
  MODIFY glossid int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE microsites
  MODIFY siteid bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE referral
  MODIFY refid int(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE schedemail
  MODIFY semailid bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE schedjobs
  MODIFY schedid bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE tracker
  MODIFY trkid bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE trackerarch
  MODIFY archid bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE trackerref
  MODIFY trid bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE useracct
  MODIFY userid bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE usermsg
  MODIFY messageid bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE userpost
  MODIFY postid bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE userrel
  MODIFY userrel_id bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE userseggroup
  MODIFY seggroupid bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE usersegment
  MODIFY segmentid bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE wd_fldpos
  MODIFY posid int(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE wd_link
  MODIFY linkid int(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE wd_rel
  MODIFY rel_id int(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE wd_sections
  MODIFY section int(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE webdata
  MODIFY wd_id int(20) UNSIGNED NOT NULL AUTO_INCREMENT;


INSERT INTO useraccess VALUES (1,'ADMIN','4095');
INSERT INTO useracct (userid, email, fname, lname, addrid, password, nletter, other, ulevel, orgid, age, gender, edu, marital, created, login, lastlogin) VALUES(1, 'chadjodon@hotmail.com', 'Chad', 'Jodon', 1, 'b497dd1a701a33026f7211533620780d', 'NO', NULL, 4095, NULL, '', '', '', '', '2008-01-16', '2009-03-16', '2009-03-16');

ALTER TABLE useracct AUTO_INCREMENT=100;
