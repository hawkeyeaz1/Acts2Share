<?php
require("common.php");
//TODO: Finish adapting this to the comment
//function i($v) { return isset($_POST[$v]); }
//function he($v) { return htmlentities($_POST[$v], ENT_QUOTES | ENT_DISALLOWED | ENT_HTML5, "UTF-8"); }

//$ip = inet_pton($_SERVER['REMOTE_ADDR']);

$style = "<style></style>";///////////////////
$javascript = "<script>alert(\"Got it!\")</script>";//////////////////////
$html = "<div>Post received.<br />You should receive an email shortly.</div>";//////////////////////
if(i('pid')) $pid = $_POST['pid']; //TODO: SANITIZE!!!!!!!!!!!!!!!!
if(i('firstname') && i('lastname') && i('personalemail') && i('churchemail') && i('scope') && i('summary') && i('needtext'))
{
 if(!(isset($_POST['prayer']) || isset($_POST['financial']) || isset($_POST['labor']) ||
      isset($_POST['time']) || isset($_POST['other']))) $_POST['other'] = 1; // Assume 'other' if none set
 $successaction = $javascript.he('firstname').he('lastname').he('personalemail').he('churchemail').he('summary').he('needtext');
 // TODO: Do not use the scope or cbtype values directly, do a switch case w/ default!!!
 echo "$pid;;$successstring:$successaction";
 storecomment();
}
else
{
?>
<style>
.inputtextbox
{
 float: left;
 width: 100%;
 min-height: 100px;
 max-height: 100%;
}
.right
{
 float: right;
}
</style>
<script type="text/javascript">
 $(init);
 function init()
 {
  //$('[name=firstname]').focus(); // Move focus to the first field
  $('.comment').submit(sf);
 }
 function sf()
 {
  var nf = $(this);
alert("Sending");
  if($('[name=firstname]').val()) { $.ajax({ url: nf.attr('action'), type: nf.attr('method'), data: nf.serialize(), success: submit }); }
  return false; // Prevent the default form submission occurring
 }
 function submit(response) // Handle the Ajax response
 {
  var str = "<?=$successstring?>";
  if(response.indexOf(str) == 0)
  {
   response = response.slice(str.length + 1);
   $("#needform").replaceWith("Post received.");
   ////////////////////////////////////////////
  }
  else { alert("Failed due to response of " + $.trim(response)); }
 }
</script>
<form action="comment.php" method="get">
 <input type="hidden" name="pid" value="<? $pid ?>" />
 <input type="text" placeholder="First Name [Public]" name="commentfirstname">
 <input type="text" placeholder="Last Name [Private]" name="commentlastname"><br />
 <input type="text" placeholder="you@email.com [Semipublic]" name="commentemail"
  pattern="^([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})$">
 <textarea class="inputtextbox" placeholder="Enter comment here." name="comment"></textarea>
 <div class="right">
  By clicking "submit" you are agreeing with the
<!-- TODO: tos.php -->
  <a href="tos.php">Terms of Service</a>
  <input type="submit"><input type="reset">
 </div>
</form>
<?
}
?>