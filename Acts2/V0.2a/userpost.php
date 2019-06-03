<?php
require("common.php");
?>
<script type="text/javascript" src="http://cdn.jquerytools.org/1.2.6/full/jquery.tools.min.js"></script>
<style>
 .content { text-align: center; }
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
 .userposting
{
 width: 85%;
 height: 65%;
 background-color: #eee;
 background: -webkit-gradient(linear, left top, left bottom, from(#eee), to(#ccc));
 background: -webkit-linear-gradient(top, #eee, #ccc);
 background: -moz-linear-gradient(top, #eee, #ccc);
 background: -ms-linear-gradient(top, #eee, #ccc);
 background: -o-linear-gradient(top, #eee, #ccc);
 overflow: hidden;
 padding: 0px;
 margin: auto;
 border: 0px;
 text-align: center;
}
.comments, .comment
{
 height: 25%;
 width: 75%;
 background-color: <?=$top_gradient ?>;
 background: -webkit-gradient(linear, left top, left bottom, from(<?=$top_gradient ?>), to(<?=$bottom_gradient ?>));
 background: -webkit-linear-gradient(top, <?=$top_gradient ?>, <?=$bottom_gradient ?>);
 background: -moz-linear-gradient(top, <?=$top_gradient ?>, <?=$bottom_gradient ?>);
 background: -ms-linear-gradient(top, <?=$top_gradient ?>, <?=$bottom_gradient ?>);
 background: -o-linear-gradient(top, <?=$top_gradient ?>, <?=$bottom_gradient ?>);
 overflow: hidden;
 padding: 0px;
 margin: auto;
 border: 0px;
 text-align: center;
}
.postlink
{
 display: inline;
}
</style>
<?
/*<div class="userposting">
 <div class="singlepost"><h6>Time</h6> <div class="postlink">I need help counting to 3</div> <div class="username">Namles</div> <div class="displayemail">nam...@math.edu</div>, <div class="postdate">24:62 Feburary 31, 3011</div></div><br />
 I am unable to count to 3. I am embarrassed that I cannot do it even though I am only 24 <i>months</i> old! I need assistance learning as I need to be able to do this to grow up.
</div>
<div class="comments">
 <div class="singlecomment">
  <div class="username">Matt</div> <div class="displayemail">mat...@toddler.org</div>, <div class="postdate">24:63 Feburary 31, 3011</div><br />
  I would love to help you with that.  I recall struggling to learn it last month when I was your age, and it is frustrating to say the least.
 </div>
 <hr align="center" style="width: 70%; height: 0px;" color="#666" />
 <div class="singlecomment">
  <div class="username">Mick</div> <div class="displayemail">mick...@infant.net</div>, <div class="postdate">24:64 Februrary 31, 3011</div><br />
  I am a teacher and am able to offer any assistance. I have been teaching others how to count to 5 for 8 months now.
  </div>
</div>*/

function invalidlink($level)
{
 echo "The link you entered is an invalid link! ".$level;
}

if(isset($_POST['pid']) && isset($_POST['rid']) && isset($_POST['cid']))
{ // Accept visible/open state changes.
 $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE) or die("Failed due to" . mysqli_connect_errno());
 $result = mysqli_prepare($connection, "UPDATE Post SET visible=?, open=? WHERE postID=? AND randomSecurityID1=? AND randomSecurityID3=?");
 $visible = validateTFString($_POST['visible']);
 $open = validateTFString($_POST['open']);
 // If pid, rid and cid do not match, it will fail
 mysqli_stmt_bind_param($result, "ssiii", $visible, $open, $_POST['pid'], $_POST['rid'], $_POST['cid']) or die("Binding failed.");
 if(mysqli_stmt_execute($result) === true) echo "Success!";
 else echo "Failed!";
 mysqli_stmt_close($result);
 mysqli_close($connection);
}
else if(isset($_GET['pid']) && isset($_GET['rid']))
{
 $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE) or die("Failed due to" . mysqli_connect_errno());
// TODO: get the comments.
 $pid = $_GET['pid'];
 if(isset($_GET['cid'])) // Give visible/open options?
 {
  $result = mysqli_prepare($connection, "SELECT visible, open, randomSecurityID1, randomSecurityID3 FROM Post WHERE postID=?");
  mysqli_stmt_bind_param($result, "i", $pid);
  mysqli_stmt_execute($result);
  mysqli_stmt_bind_result($result, $visible, $open, $randomSecurityID1, $randomSecurityID3);
  mysqli_stmt_close($result);
 }
 $result = mysqli_prepare($connection, "SELECT timestamp, ".(isset($_GET['cid']) ? "visible, open, " : "")."randomSecurityID1, ".
 (isset($_GET['cid']) ? "randomSecurityID3, " : "")."firstName, personalEmail, churchEmail, scope, type, summary, need FROM Post WHERE postID=? ORDER BY timestamp DESC");
 mysqli_stmt_bind_param($result, "i", $pid);
 mysqli_stmt_execute($result);
 if(isset($_GET['cid'])) mysqli_stmt_bind_result($result, $timestamp, $visible, $open, $randomSecurityID1, $randomSecurityID3, $firstName, $personalEmail, $churchEmail, $scope, $type, $summary, $need);
 else mysqli_stmt_bind_result($result, $timestamp, $randomSecurityID1, $firstName, $personalEmail, $churchEmail, $scope, $type, $summary, $need);
 mysqli_stmt_fetch($result);
 mysqli_stmt_close($result);
 if($_GET['rid'] == $randomSecurityID1)
 {
  if(isset($_GET['cid']) && $_GET['cid'] == $randomSecurityID3) echo "
  <form id=\"vo\" action=".basename(__FILE__)." method=\"post\"><input type=\"checkbox\" name=\"visible\" value=\"true\"".($visible ?  "checked=\"true\"" : "")." /> Request is visible for viewing<input type=\"checkbox\" name=\"open\" value=\"true\"".($open ?  "checked=\"true\"" : "")." /> Request is still open; need is not met<input type=\"hidden\" name=\"pid\" value=".$pid." /><input type=\"hidden\" name=\"rid\" value=".$randomSecurityID1." /><input type=\"hidden\" name=\"cid\" value=".$randomSecurityID3." /><input type=\"submit\" value=\"Submit\" /></form>
  <script type=\"text/javascript\">$(\"#vo\").submit(sf); function sf() { $.ajax({ url: $(this).attr('action'), type: $(this).attr('method'), data: $(this).serialize(), success: submit }); return false; } function submit(response) { var str = ".$successstring."; if(response.indexOf(str) == 0) { response = response.slice(str.length + 1); $(\"#vo\").replaceWith(response); } else { alert(\"Failed due to response of \" + $.trim(response)); } }</script>";
  echo "<div class=\"userposting\">\n<div class=\"singlepost\"><h6>".$timestamp."</h6> <div class=\"postlink\">".$summary."</div> <div class=\"username\">".expandscope($type)."</div> <div class=\"displayemail\">".obfuscatepemail($personalEmail)."</div>, <div class=\"postdate\">".$firstName."</div> <div class=\"displayemail\">".obfuscatecemail($churchEmail)."</div></div><br />".$need."\n</div>";
  $result = mysqli_prepare($connection,
  "SELECT commentID, timestamp, firstName, personalEmail, comment FROM Comment WHERE postID='$pid' AND visible='true' ORDER BY postID DESC");
  mysqli_stmt_execute($result);
  mysqli_stmt_bind_result($result, $cid, $timestamp, $firstName, $personalEmail, $commentext);
  while(mysqli_stmt_fetch($result)) echo "<div class=\"singlecomment\">\n<div class=\"username\">$firstName</div> <div class=\"displayemail\">".obfuscatepemail($personalEmail)."</div>, <div class=\"postdate\">$timestamp</div><br />\n  $commenttext\n</div>";

  echo "<div class=\"comment\"><script type=\"text/javascript\">$('.comment').load(\"comment.php?pid=$pid\")</script></div>";
  /*commentID postID timestamp visible ip randomSecurityID1 randomSecurityID2 randomSecurityID3 firstName lastName personalEmail comment*/

 }
 else invalidlink(2);
 mysqli_close($connection);
}
else invalidlink(1);
?>