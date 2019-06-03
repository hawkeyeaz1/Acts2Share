<?php
//require("dbconnect.php");
require("css.php");
require("html.php");

$_SESSION['successstring'] = "[:\f\v\t:]";
$_SESSION['postbackstr'] = "postback.php?safe=";
$_SESSION['url'] = 'http://purelydifferent.com/Acts2';
$_SESSION['orgname'] = "Need Share";
$_SESSION['dispatch'] = "index2.php";

function main()
{
 ///session_start();
 ///$connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE) or die("Failed due to".mysqli_connect_errno());
 //if(!isset($_POST['action'])) $_POST['action'] = "view"; // if not set, we risk going infinite recursion... revisit design
 switch($_POST['action'])
 {
  case 'post': // Post a need
   
   break;
  case 'view': // View ...
   if(!isset($_POST['sub'])) $_POST['sub'] = "list"; // if not set, we risk going infinite recursion... revisit design
   switch($_POST['sub'])
   {
    case 'need': // ... user post of a need

     break;
    case 'howitworks': // ... how NeedShare works
     echo hiw();
     break;
    case 'aboutus': // ... about NeedShare

     break;
    case 'list': // ... listing of needs
    default:
     //header('Content-Type: text/plain; charset=UTF-8');
     echo "<div style=\"background-color: green; width: 23px; height: 15px;\">List</div>";
   }
   break;
  case 'search': // Search for posted needs

   break;
  default: // Default = General page
  // echo index();
 }
 ///mysqli_close($connection);
}

main();
/*
need to sanitize/filter input, output
connect to db
post some data at text/plain only!
send emails
*/
?>