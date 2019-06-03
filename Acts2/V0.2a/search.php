<?php
require("common.php");
// TODO: Do live search. Remove submit. http://www.w3schools.com/php/php_ajax_livesearch.asp
// TODO: add date range

$style = "<style></style>";///////////////////
$javascript = "<script></script>";//////////////////////
$html = "<div>Post received.<br />You should receive an email shortly.</div>";//////////////////////

if(i('searchkeyword') || i('searchfirstname') || i('searchchurchemail') || i('searchopen') || i('searchscope') ||
   i('searchcbfinancial') || i('searchcblabor') || i('searchcbprayer') || i('searchcbtime') || i('searchcbother'))
{
 // TODO: Here we filter $(".content")
  $successaction = he('searchkeyword').he('searchfirstname').he('searchchurchemail').he('searchopen');
 echo "$successstring:$successaction";
}
else
{
?>
<style>
.input { display: inline; width: 77%; }
.searchinputcheckboxdiv { display: inline; float: right; top: 0px; width: 25%; position: absolute; }
.searchinputcheckboxplacementdiv { float: left; width: 100%; }
input[type=submit] { display: block; position: relative; }
</style>
<script type="text/javascript">
 $( init );
 function init()
 {
  $('[name=searchkeyword]').focus(); // Move focus to the first field
  $('#searchform').submit(sf);
 }
 function sf()
 {
  var form = $(this);
  if($('[name=searchkeyword]').val()) { $.ajax({ url: form.attr('action') + "?ajax=true", type: form.attr('method'), data: form.serialize(), success: submit }); }
  return false; // Prevent the default form submission occurring
 }
 function submit(response) // Handle the Ajax response
 {
  var str = "<?=$successstring?>";
  if(response.indexOf(str) == 0)
  {
   response = response.slice(str.length + 1);
   $(".content").append(response);
  }
  else { alert("Failed due to response of " + $.trim(response)); }
 }
</script>
<form id="searchform" action="<?=basename(__FILE__)?>" method="post" style="display: inline;">
 <input type="text" name="searchkeyword" placeholder="Keyword(s) [Required]">
 <input type="text" name="searchfirstname" placeholder="First Name [Optional]"><br />
 <input type="text" name="searchchurchemail" placeholder="contact@church.org [Optional]"
  pattern="^([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})$"><br />
 <select name="searchscope">
  <option value="All">All</option>
  <option value="Congregation">Congregation</option>
  <option value="City">City</option>
  <option value="State">State</option>
  <option value="Country">Country</option>
  <option value="Global">Global</option>
 </select>
 <select name="searchscopeunder">
  <option value="And Under">And Under</option>
  <option value="Scope Only">Scope Only</option>
 </select>
 <div class="searchinputcheckboxdiv">
  <div class="searchinputcheckboxplacementdiv">
   <input type="checkbox" name="searchcbfinancial"/>Financial<br />
   <input type="checkbox" name="searchcblabor"/>Labor<br />
   <input type="checkbox" name="searchcbprayer"/>Prayer<br />
   <input type="checkbox" name="searchcbtime"/>Time<br />
   <input type="checkbox" name="searchcbother"/>Other<br />
  </div>
 </div>
 <select name="searchopen">
  <option value="Open">Open</option>
  <option value="Closed">Closed</option>
  <option value="Both">Both</option>
 </select>
 <input type="submit" value="Search" />
</form>
<?php
}
?>