<?php
require("common.php");
?>
<style>
 .content
{
 text-align: center;
}
.singlepost
{
 text-align: center;
 margin: 0px;
 padding: 0px;
 border: 0px;
}
.username { display: inline; text-align: center; font-style: italic; font-size: 10px; }
.postdate { display: inline; text-align: center; color: #FF0000; font-size: 15px; }
.commentcount { display: inline; text-align: center;  font-size: 12px; }
.displayemail { display: inline; text-align: center; font-size: 10px; color: blue; }
h6 { display: inline; text-align: center; color: #333333; font-size: 10px; }
h5 { display: inline; color: green; font-size: 10px; font-weight: normal; }
h4 { display: inline; color: red; font-size: 10px; font-weight: normal; }
</style>
<?
/*
<div class="singlepost"><h5>Open</h5> <h6>Time</h6> <a href="userpost.php" class="postlink">I need help counting to 3</a> <div class="username">Namles</div> <div class="displayemail">nam...@math.edu</div>, <div class="postdate">24:62 Feburary 31, 3011</div> : <div class="commentcount">8 Trillion Comments</div></div><br />
<div class="singlepost"><h5>Open</h5> <h6>Labor, Time</h6> <a href="userpost.php" class="postlink">I need help tying my shoes</a> <div class="username">Baerfoot</div> <div class="displayemail">bae...@shoestore.com</div>, <div class="postdate">25:64 Feburary 30, 3011</div> : <div class="commentcount">3 Million Comments</div></div><br />
<div class="singlepost"><h5>Open</h5> <h6>Labor</h6> <a href="userpost.php" class="postlink">I locked my house keys outside</a> <div class="username">Keilaust</div> <div class="displayemail">kei...@locksmith.com</div>, <div class="postdate">29:70 Feburary 30, 3011</div> : <div class="commentcount">3.14159265 Million Comments</div></div><br />
*/
$connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE) or die("Failed due to" . mysqli_connect_errno());
//if($stmt = mysqli_prepare($connection, "SELECT * FROM Post WHERE visible='1' and open='1' ORDER BY timestamp DESC"))
$result = mysqli_prepare($connection,
 "SELECT postID, timestamp, randomSecurityID1, firstName, personalEmail, scope, type, summary FROM Post WHERE open='true' AND visible='true' ORDER BY postID DESC");
$count = mysqli_stmt_field_count($result);
mysqli_stmt_execute($result);
mysqli_stmt_bind_result($result, $postID, $timestamp, $randomSecurityID1, $firstName, $personalEmail, $scope, $type, $summary);
while(mysqli_stmt_fetch($result))
{
 echo "<div class=\"singlepost\"><h6>".expandtype($type)."</h6> <a href=\"userpost.php?pid=".$postID.'&rid='.$randomSecurityID1."\" class=\"postlink\">".$summary."</a> <div class=\"username\">".$firstName."</div> <div class=\"displayemail\">".obfuscatepemail($personalEmail)."</div>, <div class=\"postdate\">".$timestamp."</div></div><br />";
}
mysqli_close($connection);
?>