<?php
require("common.php");
// echo "<script>alert(".'"'.htmlentities($_POST['firstname'], ENT_QUOTES | ENT_DISALLOWED | ENT_HTML5, "UTF-8").'"'.");</script>";
//TODO for security
// verify data type, range, value possibilities
// filter strings!
// -search for [;], [<*>] or other javascript, html, SQL or PHP tokens the string may have
// use parameterization! http://www.codinghorror.com/blog/2005/04/give-me-parameterized-sql-or-give-me-death.html
//                   http://bobby-tables.com/php.html

$style = "<style></style>";///////////////////
$javascript = "<script></script>";//////////////////////
$html = "<div>Post received.<br />You should receive an email shortly.</div>";//////////////////////
if(i('firstname') && i('lastname') && i('personalemail') && i('churchemail') && i('scope') && i('summary') && i('needtext'))
{
 $_POST['needtype'] = ((isset($_POST['prayer']) ? PRAYER_.',' : '').
                       (isset($_POST['financial']) ? FINANCIAL_.',' : '').
                       (isset($_POST['labor']) ? LABOR_.',' : '').
                       (isset($_POST['time']) ? TIME_.',' : '').
                       (isset($_POST['other']) ? OTHER_.',' : ''));
 $_POST['needtype'] = substr($_POST['needtype'], 0, -1); // NOTE: Requiring sanidatepost to enforce defaults!
 sanidatepost($_POST);
 /*$firstname = he('firstname');
 $lastname = he('lastname');
 $personalemail = he('personalemail');
 $churchemail = he('churchemail');
 $scope = he('scope');
 $summary = he('summary');
 $needtext = he('needtext');
 $scope = shrinkscope($scope);
 $_SESSION['firstname'] = $firstname;
 $_SESSION['lastname'] = $lastname;
 $_SESSION['personalemail'] = $personalemail;
 $_SESSION['churchemail'] = $churchemail;
 $_SESSION['scope'] = $scope;
 $_SESSION['needtype'] = $needtype;
 $_SESSION['summary'] = $summary;
 $_SESSION['needtext'] = $needtext;*/
 sendpostemail(storepost());

 //$successaction = "$firstname, $lastname, $personalemail, $churchemail, $summary, $needtext";
 echo "$successstring:$successaction";
}
else
{
?>

<style>
.inputtextbox
{
 float: left;
 width: 84%;
 min-height: 101px;
 max-height: 100%;
}
.postinputcheckboxdiv
{
 float: right;
 width: 15%;
}
.postinputcheckboxplacementdiv
{
 float: left;
 width: 100%;
}
.scope
{
 float: right;
 top: 0px;
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
  $('#needform').submit(sf);
  $('[name=firstname]').focus();
 }
 function sf()
 {
  var nf = $(this);
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
   $(".myoverlay").slideToggle("slow");
  }
  else { alert("Failed due to response of " + $.trim(response)); }
 }
</script>
<form id="needform" action="<?=basename(__FILE__)?>" method="post">
 <input type="text" name="firstname" placeholder="First Name [Public]" required="required"/>
 <input type="text" name="lastname" placeholder="Last Name [Private]" required="required"/><br />
 <input type="email" name="personalemail" placeholder="you@email.com [Semipublic]" required="required"
  pattern="^([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})$"/>
 <input type="email" name="churchemail" placeholder="contact@church.org [Public]" required="required"
  pattern="^([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})$"/><br />
 <select class="scope" name="scope">
  <option value="Congregation">Congregation</option>
  <option value="City">City</option>
  <option value="State">State</option>
  <option value="Country">Country</option>
  <option value="Global">Global</option>
 </select>
 <input type="text" name="summary" placeholder="Summary" maxlength="40" required="required" style="width: 400px;"/>
 <textarea class="inputtextbox" name="needtext" placeholder="Enter need here." required="required"></textarea>
 <div class="postinputcheckboxdiv">
  <div class="postinputcheckboxplacementdiv" name="needtype" required="required">
   <input type="checkbox" name="prayer"/><label for="prayer">Prayer</label><br />
   <input type="checkbox" name="financial"/><label for="financial">Financial</label><br />
   <input type="checkbox" name="labor"/><label for="labor">Labor</label><br />
   <input type="checkbox" name="time"/><label for="time">Time</label><br />
   <input type="checkbox" name="other"/><label for="other">Other</label><br />
  </div>
 </div>
 <div class="right">
  By clicking "submit" you are agreeing with the
  <a href="info.php?disp=tos">Terms of Service</a>
  <input type="submit"><input type="reset">
 </div>
</form>
<?php
}
?>