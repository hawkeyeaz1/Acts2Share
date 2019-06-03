<?php
session_start();
define('GLOBAL_', 'g');		define('COUNTRY', 'n');		define('STATE_', 's');
define('CITY_', 'i');		define('CONGREGATION_', 'c');	define('DEFAULTSCOPE_', CONGREGATION_);

define('PRAYER_', 'p');		define('FINANCIAL_', 'f');	define('LABOR_', 'l');
define('TIME_', 't');		define('OTHER_', 'o');		define('DEFAULTTYPE', OTHER_);

define('ACTION_SUBMIT', 1);	define('ACTION_CLICK', 2);
define('FORM_POST', 1);		define('FORM_COMMENT', 2);	define('FORM_SEARCH', 3);
define('STATUS_OPEN', 1);	define('STATUS_CLOSED', 2);	define('STATUS_BOTH', 3);
define('PROP_REQUIRED', 1);	define('PROP_FOCUS', 2);

/*
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
*/

$_SESSION['site'] = 'purelydifferent.com';
$_SESSION['url'] = 'http://'.$_SESSION['site'].'/Acts2';
$_SESSION['method'] = "post";
$_SESSION['orgname'] = "Acts2gether";
$_SESSION['dispatch'] = basename(__FILE__);
$_SESSION['tabs'] = array(array('name' => 'Post', 'id' => 'post', 'act' => 'post', 'sub' => 'need'),
                          array('name' => 'View', 'id' => 'view', 'act' => 'view', 'sub' => 'list'),
                          array('name' => 'How It Works', 'id' => 'howitworks', 'act' => 'view', 'sub' => 'howitworks'),
                          array('name' => 'About Us', 'id' => 'aboutus', 'act' => 'view', 'sub' => 'aboutus')/*,
                          array('name' => 'Search', 'id' => 'search', 'act' => 'search', 'sub' => 'need')*/);
$len = count($_SESSION['tabs']);
$_SESSION['width'] = ($len > 0 ? (100 / $len) : 100) - 2;
$atom = '[^\\x00-\\x20\\x22\\x28\\x29\\x2c\\x2e\\x3a-\\x3c\\x3e\\x40\\x5b-\\x5d\\x7f-\\xff]+';
$sub_domain = "($atom|\\x5b([^\\x0d\\x5b-\\x5d\\x80-\\xff]|\\x5c[\\x00-\\x7f])*\\x5d)";
$word = "($atom|\\x22([^\\x0d\\x22\\x5c\\x80-\\xff]|\\x5c[\\x00-\\x7f])*\\x22)";
$_SESSION['emailfilter'] = "$word(\\x2e$word)*\\x40$sub_domain(\\x2e$sub_domain)*";

require("dbconnect.php");
require("email.php");
require("html.php");

/*
 TODO:
  other todos!
  calendar for search box
  error checking is not thorough enough
  summary length should be limited!
  write filter function for search, for location based limiting
  write feedback form
  finish post/search box
  fix view need from view list
  add visible todo list for site reveiwers
*/

function main()
{
 dbconnect();
 switch($_REQUEST['act'])
 {
  case 'view': // View ...
   switch($_REQUEST['sub'])
   {
    case 'need': // ... user post of a need
     echo viewneed();
     break;
    case 'termsofservice': // ... terms of service
     echo termsofservice();
     break;
    case 'list': // ... listing of needs
    default:
     echo viewlist();
   }
   break;
  case 'submit': // Submit ...
   switch($_REQUEST['sub'])
   {
    case 'need': // ... post of need
     echo submitpost();
     break;
    case 'comment': // ... comment on need posting
     echo submitcomment();
     break;
    case 'search': // ... search query
     echo "<script>alert('search');</script>";
     //echo submitsearch();
     break;
    case 'state': // ... state: open or closed, visible or invisible
     echo submitstate();
     break;
    case 'feedback': // ... feedback. Temporary?
     echo feedbackemail();
     break;
    default:
     echo "Invalid response received!";
   }
   break;
  default: // Default = Index page
   echo index();
 }
 dbclose();
}

function dbconnect()
{
 $_SESSION['connection'] = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE) or die("Failed due to" . mysqli_connect_errno());
}
function dbclose() { mysqli_close($_SESSION['connection']); }

function shrinkscope($scope)
{
 switch($scope)
 {
  case "Global": return GLOBAL_; case "Country": return COUNTRY_; case "State": return STATE_;
  case "City": return CITY_; case "Congregation": default: return DEFAULTSCOPE_; // Set valid value with narrowest scope
 }
}
function expandscope($scope)
{
 switch($scope)
 {
  case GLOBAL_: return "Global"; case COUNTRY_: return "Country"; case STATE_: return "State";
  case CITY_: return "City"; case CONGREGATION_: default: return "Congregation";
 }
}

function shrinktype($type)
{
 $string = "";
 foreach(explode(",", trim($type)) as $value)
 {
  switch($type)
  {
   case "Prayer": $string .= PRAYER_.','; break;
   case "Financial": $string .= FINANCIAL_.','; break;
   case "Labor": $string .= LABOR_.','; break;
   case "Time": $string .= TIME_.',' ; break;
   case "Other": $string .= OTHER_.','; break;
  }
 }
 return substr($string, 0, -2);
}
function expandtype($type)
{
 $string = "";
 if(strlen(trim($type)) < 2) return exty($type); // Single type
 foreach(explode(',', $type) as $value) $string .= exty(trim($value));
 return substr($string, 0, -2);
}
function exty($type)
{
 switch($type)
 {
  case PRAYER_: return "Prayer, "; case FINANCIAL_: return "Financial, ";  case LABOR_: return "Labor, ";
  case TIME_: return "Time, " ; case OTHER_: return "Other, "; default: return "";
 }
}

function obfuscatepemail($email)
{ // For ex: abcdefg@hijkl.com
 $at = strpos($email, '@');
 $name = substr($email, 0, $at);
 $domain = substr($email, $at + 1);
 if(strlen($name) < 7)
 {
  if(strlen($name) < 3) return $name[0]."...@".$domain[0]."..."; // If name < 3, a...@h...
  else return $name[0]."...@".$domain; // If 3 < name < 7, a...@hijkl.com
 }
 else return $name[0].$name[1]."...".substr($name, strlen($name) - 1).'@'.$domain; // ab...g@hijkl.com
}
function obfuscatecemail($email) { return str_replace('.', '}', str_replace('@', '{', $email)); } // For ex: abcdefg@hijkl.com -> abcdefg{hijkl}com

function postbox()
{
 return inputbox("firstname", "First Name [Public]", PROP_FOCUS).
        inputbox("lastname", "Last Name [Private]").
        inputemail("personalemail", "you@email.com [Semipublic]", PROP_REQUIRED).
        inputemail("churchemail", "contact@church.org [Semipublic]", PROP_REQUIRED).
        type().
        scope().
        inputbox("summary", "Summary").
        inputtext("need", "Enter need here").
        tos().
        submitreset();
}
function commentbox($pid, $rid, $cid)
{
 return hiddeninput($pid, $rid, $cid).
        inputbox("firstname", "First Name [Public]").
        inputbox("lastname", "Last Name [Private]").
        inputemail("personalemail", "you@email.com [Semipublic]", PROP_REQUIRED).
        inputtext("comment", "Enter comment here").
        tos().
        submitreset();
}
function searchbox()
{
 return inputbox("keyword", "Keyword(s)", PROP_FOCUS | PROP_REQUIRED).
        inputbox("firstname", "First Name").
        inputemail("churchemail", "contact@church.org").
        //calender().
        scope(1).
        searchrange().
        status().
        type(1).
        submitreset();
}

function filter()
{
 /*
  Contains: Keyword(s)
  Equals: First name
   Condition: Require additional parameters
   Note: No allowance for Last name for security/privacy to prevent brute force data leaks
 */
 $result = mysqli_prepare($_SESSION['connection'],
  "SELECT postID, timestamp, randomSecurityID1, firstName, personalEmail, scope, type, summary FROM Post WHERE open='true' AND visible='true' ORDER BY postID DESC");
 mysqli_stmt_execute($result);
 mysqli_stmt_bind_result($result, $postID, $timestamp, $randomSecurityID1, $firstName, $personalEmail, $scope, $type, $summary);
 while(mysqli_stmt_fetch($result))
 {
  $ret = "";
 }
 return $ret;
}

function submitpost()
{
 $visible = 'true'; // Is visible initially?
 $open = 'true'; // Is post open?
 $ip = $_SESSION['ip'] = inet_pton($_SERVER['REMOTE_ADDR']);
 $randomSecurityID1 = $_SESSION['rid'] = mt_rand();
 $randomSecurityID2 = $_SESSION['uid'] = mt_rand();
 $randomSecurityID3 = $_SESSION['cid'] = mt_rand();
 $firstName = $_SESSION['firstname'] = validateString($_REQUEST['firstname']);
 $lastName = $_SESSION['lastname'] = validateString($_REQUEST['lastname']);
 $personalEmail = $_SESSION['personalemail'] = validateEmail($_REQUEST['personalemail']);
 $churchEmail = $_SESSION['churchemail'] = validateEmail($_REQUEST['churchemail']);
 $scope = $_SESSION['scope'] = validateScope($_REQUEST['scope']);
 $_REQUEST['needtype'] = ((isset($_REQUEST['prayer']) ? PRAYER_.',' : '').
                       (isset($_REQUEST['financial']) ? FINANCIAL_.',' : '').
                       (isset($_REQUEST['labor']) ? LABOR_.',' : '').
                       (isset($_REQUEST['time']) ? TIME_.',' : '').
                       (isset($_REQUEST['other']) ? OTHER_.',' : ''));
 $type = $_SESSION['type'] = verifyTypeString($_REQUEST['needtype']);
 $summary = $_SESSION['summary'] = validateString($_REQUEST['summary']);
 $need = $_SESSION['need'] = validateString($_REQUEST['need']);
 if($stmt = mysqli_prepare($_SESSION['connection'],
 "INSERT INTO Post (visible, open, ip, randomSecurityID1, randomSecurityID2, randomSecurityID3, firstName, lastName,
  personalEmail, churchEmail, scope, type, summary, need) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"))
 {
  mysqli_stmt_bind_param($stmt, "sssiiissssssss", $visible, $open, $ip, $randomSecurityID1, $randomSecurityID2,
  $randomSecurityID3, $firstName, $lastName, $personalEmail, $churchEmail, $scope, $type, $summary, $need) or die("Failed to insert post");
  mysqli_stmt_execute($stmt) or die("Failed to insert post");
  $_SESSION['postID'] = mysqli_stmt_insert_id($stmt);
  mysqli_stmt_close($stmt);
  sendpostemail();
  return submissionacknowledge('post');
 }
 else die("Failed to prepare");
}

function submitcomment()
{
 $pid = validateInt($_REQUEST['pid']);
 $visible = 'true'; // Is it visible?
 $ip = inet_pton($_SERVER['REMOTE_ADDR']);
 $randomSecurityID1 = mt_rand(); // TODO:
 $randomSecurityID2 = mt_rand(); // TODO:
 $randomSecurityID3 = mt_rand(); // TODO:
 $firstName = validateString($_REQUEST['firstname']);
 $lastName = validateString($_REQUEST['lastname']);
 $personalEmail = validateEmail($_REQUEST['personalemail']);
 $comment = validateString($_REQUEST['comment']);
 if($stmt = mysqli_prepare($_SESSION['connection'],
 "INSERT INTO Comment (postID, visible, ip, randomSecurityID1, randomSecurityID2, randomSecurityID3, firstName, lastName,
  personalEmail, comment) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"))
 {
  mysqli_stmt_bind_param($stmt, "issiiissss", $pid, $visible, $ip, $randomSecurityID1, $randomSecurityID2,
  $randomSecurityID3, $firstName, $lastName, $personalEmail, $comment) or die("Failed to insert post");
  mysqli_stmt_execute($stmt) or die("Failed to insert post");
  mysqli_stmt_close($stmt);
  // email
  return submissionacknowledge('comment');
 }
 else die("Failed to prepare");
}

function submitsearch()
{
 if($keyword = validateString($_REQUEST['keyword']))
  $searchstring = "(Post.summary LIKE '%$keyword%' OR Post.need LIKE '%$keyword%') AND ";
 if($firstName = validateString($_REQUEST['firstname'])) $searchstring .= "firstName='$firstName' AND ";
 if($churchEmail = validateEmail($_REQUEST['churchemail'])) $searchstring = "churchEmail='$churchEmail' AND ";
 if($scope = validateScope($_REQUEST['scope']) & isset($_REQUEST['searchscoperange']))
  switch($_REQUEST['searchscoperange'])
  {
   case 'And Under': $searchstring = ""/*TODO*/; break;
   case 'Scope Only': default: $searchstring = "scope='$scope' AND ";
  }

 if(isset($_REQUEST['status'])) switch($_REQUEST['status'])
  {
   case 'Open':
   case 'Closed': $searchstring = "open='".($_REQUEST['status'] == 'Open' ? 'tru' : 'fals')."e' AND "; break;
   default:
  }
 if($type = validateTypeString($_REQUEST['type'])) $searchstring = ""/*TODO*/;
 // TODO: trim off excess ' AND '
 if($stmt = mysqli_prepare($_SESSION['connection'],
 "SELECT postID, timestamp, randomSecurityID1, firstName, personalEmail, scope, type, summary FROM Post WHERE $searchstring ORDER BY postID DESC"))
 {
  mysqli_stmt_bind_result($result, $postID, $timestamp, $randomSecurityID1, $firstName, $personalEmail, $scope, $type, $summary) or die("Failed to bind");
  mysqli_stmt_execute($stmt) or die("Failed to execute");
  mysqli_stmt_close($stmt);
  return submissionacknowledge('search');
 }
 else die("Failed to prepare");
}

function validateTFString($bool) { return (isset($bool) ? ($bool == 'true' ? 'true' : 'false'): false); }
function validateInt($int) { if(isset($int)) return (!is_numeric($int) ? 0 : $int); else return false; }
function validateString($string)
{ return (isset($string) ? htmlentities($string, ENT_QUOTES | ENT_DISALLOWED | ENT_HTML5, "UTF-8") : false); }
function validateEmail($email) // borrowed and made less readable from http://www.iamcal.com/publish/articles/php/parsing_email/
{ return (isset($email) ? (preg_match("!^".$_SESSION['emailfilter']."$!", $email) ? $email : "") : false); }
function validateScope($scope)
{
 switch(shrinkscope($scope)) { case CONGREGATION_: case CITY_: case STATE_: case COUNTRY_: case GLOBAL_: return shrinkscope($scope); break; default: return DEFAULTSCOPE_; }
}

function verifyTypeString($values)
{
 if(!isset($values)) return false;
 $p = $f = $l = $t = $o = 0;
 if(strlen(trim($values)) > 1) foreach(explode(',', $values) as $option)
  {
   $option = trim($option);
   switch($option)
   {
    case PRAYER_: $p = 1; break; case FINANCIAL_: $f = 1; break; case LABOR_: $l = 1; break;
    case TIME_: $t = 1; break; case OTHER_: $o = 1; break; default:
   }
  }
 else switch($values)
  {
   case PRAYER_: $p = 1; break; case FINANCIAL_: $f = 1; break; case LABOR_: $l = 1; break;
   case TIME_: $t = 1; break; case OTHER_: $o = 1; break; default:
  }
 if(!($p | $f | $l | $t | $o)) $o = 1;
 return substr((($p ? PRAYER_.',' : '').($f ? FINANCIAL_.',' : '').($l ? LABOR_.',' : '').
                ($t ? TIME_.',' : '').($o ? OTHER_.',' : '')), 0, -1);
}

main();

/* NOTE: Below may have syntax errors due to manual updates after table creation
$action = "CREATE TABLE Post
(
 postID bigint UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 timestamp TIMESTAMP DEFAULT NOW(),
 visible enum('false', 'true') NOT NULL DEFAULT 'false',
 open enum('false', 'true') NOT NULL DEFAULT 'false',
 ip varchar(16) BINARY NOT NULL,
 randomSecurityID1 int NOT NULL,
 randomSecurityID2 int NOT NULL,
 randomSecurityID3 int NOT NULL,
 firstName varchar(15) NOT NULL,
 lastName varchar(15) NOT NULL,
 personalEmail varchar(30) NOT NULL,
 churchEmail varchar(30) NOT NULL,
 scope enum('c', 'i', 's', 'n', 'g') NOT NULL DEFAULT 'c',
 type set ('p', 'f', 'l', 't', 'o') NOT NULL DEFAULT 'o',
 summary tinytext NOT NULL,
 need text NOT NULL
);";

/ * Explanation
 postID is obvious
 timestamp time of post or edit
 visible allows hiding for initial moderation, or due to a TOS violation, etc
 open is whether need has been met
 ip allows IPv4 or IPv6
 randomSecurityID1 is used to prevent iterating through the postID; requires direct links to access
 randomSecurityID2 is used for the email link, to allow editing, rather than username/password
 randomSecurityID3 is used for giving the church moderation access
 firstName is obvious
 lastName is obvious, is not displayed
 personalEmail is displayed in obscured form
 churchEmail public, but is partially obscured to prevent spamming
 scope should this need be shared with the congregation, city, state/province, country, or the world.
 type of need, prayer, financial, timel labor, other
 summary short description displayed on main page
 need is the full text
* /

$action = "CREATE TABLE Comment
(
 commentID bigint NOT NULL AUTO_INCREMENT PRIMARY KEY,
 postID bigint NOT NULL,
 timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 visible enum('false', 'true') NOT NULL DEFAULT 'false',
 ip varchar(16) BINARY NOT NULL,
 randomSecurityID1 int NOT NULL,
 randomSecurityID2 int NOT NULL,
 randomSecurityID3 int NOT NULL,
 firstName varchar(15) NOT NULL,
 lastName varchar(15) NOT NULL,
 personalEmail varchar(30) NOT NULL,
 comment text NOT NULL
)";

/ * Explanation
 commentID is obvious
 postID is the post for which the comment is in response to
 timestamp is obvious
 visible is the same as in the post table
 ip is the same as in the post table
 randomSecurityID1 is the same as in the post table
 randomSecurityID2 is the same as in the post table
 randomSecurityID3 is the same as in the post table
 firstName is the same as in the post table
 lastName is the same as in the post table
 personalEmail is the same as in the post table
 comment is the full comment
*/

?>