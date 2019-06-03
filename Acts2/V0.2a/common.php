<?php
// TODO: As soon as this file is included, it is local
//	thus connections to the db are per file, and there is no need to $_SESSION
require("dbconnect.php");
$successstring = "[:\f\v\t:]";
global $url;
global $orgname;

$url = 'http://purelydifferent.com/Acts2';
$orgname = "Need Share";

//global $connected;
//global $connection;

//$connected = false;
//$connection = 0;
function i($v) { return isset($_POST[$v]); }
function he($v) { return htmlentities($_POST[$v], ENT_QUOTES | ENT_DISALLOWED | ENT_HTML5, "UTF-8"); }

//http://php.net/manual/en/mysqli-stmt.bind-param.php

/*function dbconnect()
{
 if(!$connected)
 {
  $connection = @mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE) or die("Failed due to".mysqli_connect_errno());
  if(mysqli_connect_errno())
  {
   echo "Connection Failed: ".mysqli_connect_errno();
   exit();
  }
 $connected = true;
 }
}*/

define('GLOBAL_', 'g');
define('COUNTRY', 'n');
define('STATE_', 's');
define('CITY_', 'i');
define('CONGREGATION_', 'c');
define('DEFAULTSCOPE_', CONGREGATION_); // Alert! Check expandscope default!

function shrinkscope($scope)
{
 switch($scope)
 { // Valid values
  case "Global": return GLOBAL_; case "Country": return COUNTRY_; case "State": return STATE_;
  case "City": return CITY_; case "Congregation": default: /* Invalid value*/ return DEFAULTSCOPE_; // Set to valid value with narrowest scope
 }
}

function expandscope($scope)
{
 switch($scope)
 { // Valid values
  case GLOBAL_: return "Global"; case COUNTRY_: return "Country"; case STATE_: return "State";
  case CITY_: return "City"; case CONGREGATION_: default: return "Congregation";
 }
}

define('PRAYER_', 'p');
define('FINANCIAL_', 'f');
define('LABOR_', 'l');
define('TIME_', 't');
define('OTHER_', 'o');
define('DEFAULTTYPE', OTHER_);

function shrinktype($type)
{
 $string = "";
 foreach(explode(",", $type) as $value)
 {
  $value = trim($value);
  switch($type)
  {
   case "Prayer": $string .= PRAYER_.', '; break;
   case "Financial": $string .= FINANCIAL_.', '; break;
   case "Labor": $string .= LABOR_.', '; break;
   case "Time": $string .= TIME_.', ' ; break;
   case "Other": $tmp |= $string .= OTHER_.', '; break;
  }
 }
 return substr($string, 0, -2);
}

function expandtype($type)
{
 $string = "";
 if(strlen(trim($type)) > 1)
 {
  foreach(explode(',', $type) as $value)
  {
   switch(trim($value))
   {
    case PRAYER_: $string .= "Prayer, "; break;
    case FINANCIAL_: $string .= "Financial, "; break;
    case LABOR_: $string .= "Labor, "; break;
    case TIME_: $string .= "Time, " ; break;
    case OTHER_: $string .= "Other, "; break;
   }
  }
 }
 else
 {
  switch($type)
  {
   case PRAYER_: $string .= "Prayer, "; break;
   case FINANCIAL_: $string .= "Financial, "; break;
   case LABOR_: $string .= "Labor, "; break;
   case TIME_: $string .= "Time, " ; break;
   case OTHER_: $tmp |= $string .= "Other, "; break;
  }
 }
 return substr($string, 0, -2);
}

function obfuscatepemail($email)
{
 $at = strpos($email, '@');
 $name = substr($email, 0, $at);
 $domain = substr($email, $at + 1);
 if(strlen($name) < 7)
 {
  if(strlen($name) < 3) return $name[0]."...@".$domain[0]."...";
  else return $name[0]."...@".$domain;
 }
 else return $name[0].$name[1]."...".substr($name, strlen($name) - 1).'@'.$domain;
}
function obfuscatecemail($email) { $email = str_replace('@', '{', $email); $email[strrpos($email, '.')] = '}'; return $email; }
function alteremail($email) { return str_replace("@", "(a)", $email); }

function validateTFString($bool) { return (isset($bool) ? ($bool == 'true' ? 'true' : 'false'): false); }
function validateInt($int) { if(isset($int)) return (!is_numeric($int) ? 0 : $int); else return false; }
function validateString($string) { return (isset($string) ? htmlentities($string, ENT_QUOTES | ENT_DISALLOWED | ENT_HTML5, "UTF-8") : false); }
function validateEmail($email)
{ // borrowed and made less readable from http://www.iamcal.com/publish/articles/php/parsing_email/
 if(isset($email))
 {
  $atom = '[^\\x00-\\x20\\x22\\x28\\x29\\x2c\\x2e\\x3a-\\x3c\\x3e\\x40\\x5b-\\x5d\\x7f-\\xff]+';
  $sub_domain = "($atom|\\x5b([^\\x0d\\x5b-\\x5d\\x80-\\xff]|\\x5c[\\x00-\\x7f])*\\x5d)";
  $word = "($atom|\\x22([^\\x0d\\x22\\x5c\\x80-\\xff]|\\x5c[\\x00-\\x7f])*\\x22)";
  return (preg_match("!^$word(\\x2e$word)*\\x40$sub_domain(\\x2e$sub_domain)*$!", $email) ? $email : "");
 }
 return false;
}

function sanidategeneric($array) // Validate, Sanitize
{
 /* *** Values are not yet set! Left for example purposes.
 if($paranoia) // These items, do not need to be checked as PHP/SQL has full control
 {
  =validateInt($array['postID']);    // SQL created; Range: 0 - 18446744073709551615; Safe
  =validateInt($array['commentID']); // SQL created; Range: 0 - 18446744073709551615; Safe
  if(isset($array['timestamp'])) // SQL created; Range: '1970-01-01 00:00:01' to '2038-01-09 03:14:07'; Safe
  {
   if(preg_match_all("/((?:2|1)\\d{3}(?:-|\\/)(?:(?:0[1-9])|(?:1[0-2]))(?:-|\\/)(?:(?:0[1-9])|(?:[1-2][0-9])|(?:3[0-1]))(?:T|\\s)(?:(?:[0-1][0-9])|(?:2[0-3])):(?:[0-5][0-9]):(?:[0-5][0-9]))/is", $array['timestamp'], $matches))
    =$array['timestamp'] = $matches[1][0]; // Technically we don't need to set it to the same value since it matches...
   else =$array['timestamp'] = "1970-01-01 00:00:01"; // Bad value! '0' it to default
  }
  =validateInt($array['randomSecurityID1']); // PHP created; Range: 0 - 4294967295; Safe
  =validateInt($array['randomSecurityID2']);  // PHP created; Range: 0 - 4294967295; Safe
  =validateInt($array['randomSecurityID3']);  // PHP created; Range: 0 - 4294967295; Safe
  // Network created; Range1: 0.0.0.0 - 255.255.255.255
  //                  Range2: 0000:0000:0000:0000:0000:0000:0000:0000 - FFFF:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF
  // Semisafe -- spoofable, but must be in valid range to arrive!
  if(isset($array['ip']))
  {
   $reipv4 = '((?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?))(?![\\d])';
   $s = '[0-9A-Fa-f]{1,4}'; // hexpair for 0000-FFFF or 0000-ffff
   $f = '((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})'; // IPv4 match
   $reipv6 = '/^\s*((($s:){7}($s|:))|(($s:){6}(:$s|$f|:))|(($s:){5}(((:$s){1,2})|:$f|:))|(($s:){4}(((:$s){1,3})|((:$s)?:$f)|:))|(($s:){3}(((:$s){1,4})|((:$s){0,2}:$f)|:))|(($s:){2}(((:$s){1,5})|((:$s){0,3}:$f)|:))|(($s:){1}(((:$s){1,6})|((:$s){0,4}:$f)|:))|(:(((:$s){1,7})|((:$s){0,5}:$f)|:)))(%.+)?\s*$/'; // http://forums.intermapper.com/viewtopic.php?t=452
   if(preg_match_all("/$reipv4/is", $array['ip'], $matches) || preg_match_all("/$reipv6/is", $array['ip'], $matches))
    =$array['ip'] = $matches[1][0];
   else =$array['ip'] = "InvalidIPAddress"
  }
 }
 // Html created; Range: 'false' or 'true'; Unsafe
 if(isset($array['visible'])) if($array['visible'] !== 'true') =$array['visible'] = 'false';
 if(isset($array['open'])) if($array['open'] !== 'true') =$array['open'] = 'false'; *** */
}

function sanidatesearch($array) // Validate, Sanitize
{
 // TODO: Much code will be shared with sanidatepost
}

function sanidatecomment($array) // Validate, Sanitize
{
 // TODO: Much code will be shared with sanidatepost
 $_SESSION['comment'] = validateString($array['comment']);      // Html created; Range: alphanumeric characters, text up to length 65535; Unsafe
}

function verifyTypeString($values)
{
 if(isset($values))
 {
  $p = $f = $l = $t = $o = 0;
  if(strlen(trim($values)) > 1)
  {
   foreach(explode(',', $values) as $option)
   {
    $option = trim($option);
    switch($option)
    {
     case PRAYER_: $p = 1; break;
     case FINANCIAL_: $f = 1; break;
     case LABOR_: $l = 1; break;
     case TIME_: $t = 1; break;
     case OTHER_: $o = 1; break;
     default:
    }
   }
  }
  else
  {
   switch($values)
   {
    case PRAYER_: $p = 1; break;
    case FINANCIAL_: $f = 1; break;
    case LABOR_: $l = 1; break;
    case TIME_: $t = 1; break;
    case OTHER_: $o = 1; break;
    default:
   }
  }
  if(!($p | $f | $l | $t | $o)) $o = 1;
  return substr((($p ? PRAYER_.', ' : '').($f ? FINANCIAL_.', ' : '').($l ? LABOR_.', ' : '').
                 ($t ? TIME_.', ' : '').($o ? OTHER_.', ' : '')), 0, -2);
 }
 else return false;
}

function sanidatepost($a) // Validate, Sanitize
{
 // Html created; Range: 'c', 'i', 's', 'n', 'g'; Unsafe
 if(isset($a['scope'])) { switch(shrinkscope($a['scope'])) { case CONGREGATION_: case CITY_: case STATE_: case COUNTRY_: case GLOBAL_: $_SESSION['scope'] = shrinkscope($a['scope']); break; default: $_SESSION['scope'] = DEFAULTSCOPE_; } }
 $_SESSION['needtype'] = verifyTypeString($a['needtype']); // PHP created from Html; Range: 'p', 'f', 'l', 't', 'o'; Safed (Unsafe made safe)
 $_SESSION['firstname'] = validateString($a['firstname']); // Html created; Range: alphanumeric characters, text up to length 15; Unsafe
 $_SESSION['lastname'] = validateString($a['lastname']); // Html created; Range: alphanumeric characters, text up to length 15; Unsafe
 $_SESSION['summary'] = validateString($a['summary']); // Html created; Range: alphanumeric characters, text up to length 255; Unsafe
 $_SESSION['needtext'] = validateString($a['needtext']); // Html created; Range: alphanumeric characters, text up to length 65535; Unsafe
 $_SESSION['personalemail'] = validateEmail($a['personalemail']); // Html created; Form: name@domain.ext, text up to length 30; Unsafe
 $_SESSION['churchemail'] = validateEmail($a['churchemail']); // Html created; Form: name@domain.ext, text up to length 30; Unsafe
}

function storepost()
{
 //dbconnect();
 //createPostTable();
 //createCommentTable();
 $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE) or die("Failed due to".mysqli_connect_errno());
 $visible = $_SESSION['visible'] = 'true'; // Not visible initially
 $open = $_SESSION['open'] = 'true'; // New posts are open
 $ip = $_SESSION['ip'] = inet_pton($_SERVER['REMOTE_ADDR']);
 $randomSecurityID1 = $_SESSION['randomSecurityID1'] = mt_rand();
 $randomSecurityID2 = $_SESSION['randomSecurityID2'] = mt_rand();
 $randomSecurityID3 = $_SESSION['randomSecurityID3'] = mt_rand();
 $firstName = $_SESSION['firstname'];
 $lastName = $_SESSION['lastname'];
 $personalEmail = $_SESSION['personalemail'];
 $churchEmail = $_SESSION['churchemail'];
 $scope = $_SESSION['scope'];
 $type = $_SESSION['needtype'];
 $summary = $_SESSION['summary'];
 $need = $_SESSION['needtext'];
 if($stmt = mysqli_prepare($connection,
 "INSERT INTO Post (visible, open, ip, randomSecurityID1, randomSecurityID2, randomSecurityID3, firstName, lastName,
  personalEmail, churchEmail, scope, type, summary, need) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"))
 {
  mysqli_stmt_bind_param($stmt, "sssiiissssssss", $visible, $open, $ip, $randomSecurityID1, $randomSecurityID2,
  $randomSecurityID3, $firstName, $lastName, $personalEmail, $churchEmail, $scope, $type, $summary, $need) or die("Failed to insert post; b");
  mysqli_stmt_execute($stmt) or die("Failed to insert post; e");
  $_SESSION['postID'] = mysqli_stmt_insert_id($stmt);
  mysqli_stmt_close($stmt);
 }
 mysqli_close($connection);
 return array($randomSecurityID1, $randomSecurityID2, $randomSecurityID3);
}

function checkcrlf($field) { if(eregi("\r",$field) || eregi("\n",$field)) die("Invalid Input!"); }

function sendpostemail($postid)
{
 global $url;
 global $orgname;
 /*$connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE) or die("Failed! due to".mysqli_connect_errno());
 $stmt = mysqli_prepare($connection,
 "SELECT postID, ip, randomSecurityID1, randomSecurityID2, randomSecurityID3, firstName, lastName,
  personalEmail, churchEmail, scope, type, summary, need FROM Post WHERE ") or die("Failed, due to".mysqli_connect_errno());
 {
  mysqli_stmt_bind_param($stmt, "isiiissssssss", $postID, $ip, $randomSecurityID1, $randomSecurityID2,
  $randomSecurityID3, $firstName, $lastName, $personalEmail, $churchEmail, $scope, $type, $summary, $need);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);
 }
 mysqli_close($connection);*/

 if($_SESSION['personalemail'] !== "" && $_SESSION['churchemail'] !== "")
 { // TODO: Conditionaly send emails; if a bounce occurs, do not send the other
  // To the individual
  $to = $_SESSION['personalemail'];
  $subject = "Need Share: ".expandtype($_SESSION['needtype'])." for ".expandscope($_SESSION['scope']);
  $message = "Hello ".html_entity_decode($_SESSION['firstname'])." ".html_entity_decode($_SESSION['lastname']).",\n\n
This is to confirm your need has been posted and sent to ".html_entity_decode($_SESSION['churchemail']).
".\n\nScope: ".expandscope($_SESSION['scope']).
"\n\nType: ".expandtype($_SESSION['needtype']).
"\n\nSummary: ".html_entity_decode($_SESSION['summary'])."\n\nNeed:\n".html_entity_decode($_SESSION['needtext'])."\n
To edit or to close your post, click on here: $url/userpost.php?pid=".$_SESSION['postID']."&rid=".$_SESSION['randomSecurityID1']."&uid=".$_SESSION['randomSecurityID2']."\n
Please note: This does not yet work!\n\n
Your post will show up once it is approved by your church.\n\n
This email was sent at the request of someone providing ".html_entity_decode($_SESSION['personalemail'])." as their email address.\n
This was posted from ".inet_ntop($_SESSION['ip'])."
You can visit ".$orgname." at ".$url;
  // TODO: make the user post editable
  $headers = "From:".$_SESSION['personalemail']."\r\nMIME-Version: 1.0\r\nContent-type: text/plain; charset=utf-8\r\n";
  mail($to, $subject, $message, $headers);

  // To the church contact
  $to = $_SESSION['churchemail'];
  $subject = "Need Share: ".html_entity_decode($_SESSION['firstname'])." ".html_entity_decode($_SESSION['lastname']).", ".expandtype($_SESSION['needtype'])." for ".expandscope($_SESSION['scope']);
  $message = "Hello ".html_entity_decode($_SESSION['churchemail']).",\n\n
This is to let you know that ".html_entity_decode($_SESSION['firstname'])." ".html_entity_decode($_SESSION['lastname'])." has posted a need at ".$orgname." and specified you as the contact church.\n
Please visit $url/userpost.php?pid=".$_SESSION['postID']."&rid=".$_SESSION['randomSecurityID1']."&cid=".$_SESSION['randomSecurityID3']." to view, approve, and to close the request when the need is met.\n\n
Scope: ".expandscope($_SESSION['scope'])."\n\nType: ".expandtype($_SESSION['needtype'])."\n\nSummary: ".html_entity_decode($_SESSION['summary'])."\n\nNeed:\n".html_entity_decode($_SESSION['needtext'])."\n\n
This was posted from ".inet_ntop($_SESSION['ip']).".\n\nYou can visit ".$orgname." at ".$url;
  $headers = "From:".html_entity_decode($_SESSION['personalemail'])."\r\nMIME-Version: 1.0\r\nContent-type: text/plain; charset=utf-8\r\n";
  mail($to, $subject, $message, $headers);
 }
 else return "Bad email address! Email not sent.";
}

function storecomment() // TODO: SANTIZE!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
{ // TODO: working on this
 $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE) or die("Failed due to".mysqli_connect_errno());
 $pid = $_POST['pid'];
 $visible = 1; // Not visible initially
 $ip = inet_pton($_SERVER['REMOTE_ADDR']);
 $randomSecurityID1 = 0; // TODO:
 $randomSecurityID2 = 0; // TODO:
 $randomSecurityID3 = 0; // TODO:
 $firstName = $_POST['firstname'];
 $lastName = $_POST['lastname'];
 $personalEmail = $_POST['personalemail'];
 $comment = $_POST['comment'];
 if($stmt = mysqli_prepare($connection,
 "INSERT INTO Comment (postID, visible, ip, randomSecurityID1, randomSecurityID2, randomSecurityID3, firstName, lastName,
  personalEmail, comment) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"))
 {
  mysqli_stmt_bind_param($stmt, "issiiisssss", $pid, $visible, $ip, $randomSecurityID1, $randomSecurityID2,
  $randomSecurityID3, $firstName, $lastName, $personalEmail, $churchEmail, $comment) or die("Failed to insert post; b");
  mysqli_stmt_execute($stmt) or die("Failed to insert post; e");
  $_SESSION['postID'] = mysqli_stmt_insert_id($stmt);
  mysqli_stmt_close($stmt);
 }
 mysqli_close($connection);
 return array($randomSecurityID1, $randomSecurityID2, $randomSecurityID3);
}

/*
http://www.w3schools.com/sql/default.asp
http://www.w3schools.com/php/php_mysql_insert.asp
http://en.wikibooks.org/wiki/PHP_Programming/SQL_Injection#Use_Parameterized_Statements
http://php.net/manual/en/security.database.sql-injection.php
http://mattbango.com/notebook/web-development/prepared-statements-in-php-and-mysqli/
*/
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
* /
*/
?>