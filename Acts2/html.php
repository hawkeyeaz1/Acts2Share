<?php

function index()
{
 $googlejquery = "https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js";
 $googlejqueryui = "https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js";
 return "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN' 'http://www.w3.org/TR/html4/loose.dtd'>
<html>
 <head>
  <title></title>
  <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
  <link rel='stylesheet' type='text/css' href='css.php'>
  <link href='http://fonts.googleapis.com/css?family=Calligraffitti' rel='stylesheet' type='text/css'>
  <script type='text/javascript' src='$googlejquery'></script>
  <script type='text/javascript' src='$googlejqueryui'></script>
 </head>
 <body style='height: 100%;'>
  <div class='heading'>
   Acts 2:44-45&nbsp;&nbsp;&nbsp;&nbsp;All the believers were together and had everything in common.
   They sold property and possessions to give to anyone who had need.<br>
  </div>
  <div class='left arrow'>&lt;</div>
  <div class='right arrow'>&gt;</div>
  <div id='displaytooltip'></div>
  
  <div class='overlay'><div class='closepane'>X</div><div class='pane'></div></div>
  <div class='innerbox howitworks'>".howitworks()."</div>
  <div class='innerbox list'>".viewlist()."</div>
  <div class='innerbox aboutus'>".aboutus()."</div>
  <script type='text/javascript'>
   var fheight = $('.postbox').show().css('height', 'auto').height();
   $('.postbox').css('height', '0%').css('height', '').hide(); // get height
   $('.left.arrow').hover(function() { $('#displaytooltip').html($('.innerbox').prevUntil(':visible').children('.tooltip').html()).show().css('top', $('.left.arrow').position().top + 25).css('left', $('.left.arrow').position().left + 5); }, function() { $('#displaytooltip').hide().html(''); });
   $('.left.arrow').click(function()
   {
    $('.right.arrow').show();
    var self = $('.innerbox:visible'), left = self.prev('.innerbox');
    if(left.length)
    {
     self.animate({ left: '100%' }, 500, function () { self.hide().css('left', '2%'); });
     left.show().animate({ width: '96%' }, 500);
    }
    if((left.prev('.innerbox').length) < 1) $(this).hide();
   });
   $('.right.arrow').hover(function() { $('#displaytooltip').html($('.innerbox').nextUntil(':visible').children('.tooltip').html()).show().css('top', $('.right.arrow').position().top + 25).css('left', $('.right.arrow').position().left - 5 - $('#displaytooltip').width() + $('.right.arrow').width()); }, function() { $('#displaytooltip').hide().html(''); });
   $('.right.arrow').click(function()
   {
    $('.left.arrow').show();
    var self = $('.innerbox:visible'), right = self.next('.innerbox');
    if(right.length)
    {
     self.animate({ left: '-100%' }, 500, function () { self.hide().css('left', '2%'); });
     right.show().animate({ width: '96%' }, 500);
    }
    if((right.next('.innerbox').length) < 1) $(this).hide();
   });
   $('#post').click(function ()
   {
    //if($('.searchbox').is(':visible')) $('.searchbox').animate({ height: '0%' }).hide();
    if($('.postbox').is(':visible')) $('.postbox').animate({ width: '0%', height: '0%' }, 500, function () { $(this).hide(); });
    else
    {
     //fheight = $('.postbox').show().css('height', 'auto').height(); // Broken // in case it has changed
     //$('.postbox').css('height', '').css('height', '0%');
     $('.postbox').show().animate({ width: '96%' }).css('height', fheight);
    }
    $('.postbox').css('max-height', '');

   });
   /*$('#search').click(function ()
   {
    if($('.postbox').is(':visible')) $('.postbox').animate({ height: '0%' }).hide();
    if($('.searchbox').is(':visible')) $('.searchbox').animate({ width: '0%' }, 500, function() { $(this).hide(); });
    else $('.searchbox').show().animate({ width: '99%' }).css('height', '');
   });*/
   $('#search').hide();
   $('#post').css('width', '105%');
   $('.closepane').click(function() { $('.overlay').hide(); });
  </script>
 </body>
</html>";
}

function viewlist()
{
 $result = mysqli_prepare($_SESSION['connection'],
  "SELECT postID, timestamp, randomSecurityID1, firstName, personalEmail, scope, type, summary FROM Post WHERE open='true' AND visible='true' ORDER BY postID DESC");
 //$count = mysqli_stmt_field_count($result);
 mysqli_stmt_execute($result);
 mysqli_stmt_bind_result($result, $postID, $timestamp, $randomSecurityID1, $firstName, $personalEmail, $scope, $type, $summary);
 $ret = "<div class='tooltip'>View List of Needs</div>
<div style='width: 100%; height: 20px;'>
<div class='tabs' id='post'>Post</div>
<div class='tabs' id='search' disable=true>Search</div>
</div>
<div class='postbox'>".form("need", "submit", "need", postbox())."</div>
<div class='searchbox'>".form("search", "search", "", searchbox())."</div>
<div class='postlist'>";
 while(mysqli_stmt_fetch($result))
 {
 $ret .= "
<div class='singlepost'>
 <h6 class='scope'>".expandscope($scope)."</h6>
 <h5 class='type'>".expandtype($type)."</h5>
 <a href='".$_SESSION['dispatch']."?act=view&amp;sub=need&amp;pid=$postID&amp;rid=$randomSecurityID1' class='postlink'>$summary</a>
 <div class='username'>$firstName</div>
 <div class='displayemail'>".obfuscatepemail($personalEmail)."</div>,
 <div class='postdate'>$timestamp</div>
</div><br>";
 }
 return $ret."</div>".htmlajaxhtml(".postlink", ".pane", ACTION_CLICK);
}

function viewneed()
{
 $ret = "";
 $pid = $_REQUEST['pid'];
 $result = mysqli_prepare($_SESSION['connection'], "SELECT timestamp, ".(isset($_REQUEST['cid']) ? "visible, open, " : "")."randomSecurityID1, ".
 (isset($_REQUEST['cid']) ? "randomSecurityID3, " : "")."firstName, personalEmail, churchEmail, scope, type, summary, need FROM Post WHERE postID='$pid' ORDER BY timestamp DESC");
 mysqli_stmt_execute($result);
 if(isset($_REQUEST['cid'])) mysqli_stmt_bind_result($result, $timestamp, $visible, $open, $randomSecurityID1, $randomSecurityID3, $firstName, $personalEmail, $churchEmail, $scope, $type, $summary, $need);
 else mysqli_stmt_bind_result($result, $timestamp, $randomSecurityID1, $firstName, $personalEmail, $churchEmail, $scope, $type, $summary, $need);
 mysqli_stmt_fetch($result);
 mysqli_stmt_close($result);
 if($_REQUEST['rid'] == $randomSecurityID1)
 {
  if(isset($_REQUEST['cid']) && $_REQUEST['cid'] == $randomSecurityID3)
   $ret .= form("#vo", "submit", "state", radiobox("visible", "Request is visible for viewing", $visible).
         radiobox("open", "Request is still open; need is not met", $open));
  $ret .= "
<div class=\"userposting\">
 <div class=\"singlepost\">
  <h6>".$timestamp."</h6>
  <div class=\"postlink\">".$summary."</div>
  <div class=\"username\">".expandscope($type)."</div>
  <div class=\"displayemail\">".obfuscatepemail($personalEmail)."</div>,
  <div class=\"postdate\">".$firstName."</div>
  <div class=\"displayemail\">".obfuscatecemail($churchEmail)."</div>
 </div><br>".$need."
</div>";
  $submitcomment = form("comment", "submit", "comment", commentbox($pid, $rid, $cid));
  $result = mysqli_prepare($_SESSION['connection'],
  "SELECT commentID, timestamp, firstName, personalEmail, comment FROM Comment WHERE postID='$pid' ORDER BY postID DESC");
  mysqli_stmt_execute($result);
  mysqli_stmt_bind_result($result, $cid, $timestamp, $firstName, $personalEmail, $commenttext);
  while(mysqli_stmt_fetch($result)) $comment .= "
<br>
<div class=\"singlecomment\">
 <div class=\"username\">$firstName</div>
 <div class=\"displayemail\">".obfuscatepemail($personalEmail)."</div>,
 <div class=\"postdate\">$timestamp</div><br>
  $commenttext
</div>";
 }
 return $ret.$submitcomment.$comment;
}

function aboutus()
{
 return "
<!-- TODO: Insert Description -->
<div class='tooltip'>About Us</div>
<h1>We are:</h1>
<ul>
 <li class='au'>Non profit<b>*</b></li>
 <li class='au'>Non denominational</li>
</ul>
<h1>We aim to:</h1>
<ul>
 <li class='au'>Provide a service for Christian (Acts 2:44-45) to share and meet their needs--this is <i>one</i> way God meets our needs</li>
  <li class='au'>Operate at no charge to either the person in need or the person helping to meet the need</li>
  <li class='au'>Show no advertising</li>
  <li class='au'>Provide a safe, inviting environment</li>
  <li class='au'>Glorify God</li>
</ul>
<h1>We believe:</h1>
<ul>
 <li class='au'>God is perfect, holy, omniscient, omnipotent and sovereign</li><div class='verseref'></div>
 <li class='au'>God spoke everything into existance, but He formed man with His hands</li><div class='verseref'>Genesis 1</div>
 <li class='au'>God made us male and female, both in His image</li><div class='verseref'>Genesis 1</div>
 <li class='au'>God will not act against His nature</li><div class='verseref'>Matthew 12:25</div>
 <li class='au'>God is three in one</li><div class='verseref'>Mark 12:29, 1 John 5:7-8</div>
 <ul>
  <li class='au'>God is Father, Son and Holy Spirit, just like we are Mind, Body amd Spirit</li><div class='verseref'>1 John 5:7-8, Genesis 1:26</div>
 </ul>
 <li class='au'>God is the same yesterday, today and forever; God does not change</li><div class='verseref'>Malachi 3:6</div>
 <li class='au'>God is just</li><div class='verseref'>Job 34, Psalm 7:11, Hebrews 6:10</div>
 <li class='au'>God is love</li><div class='verseref'>1 John 4:7-8, 16</div>
 <li class='au'>God cannot sin</li><div class='verseref'>2 Corinthians 5:21, 1 John 3:8-9</div>
 <li class='au'>God still speaks today</li><div class='verseref'>Hebrews 4:12</div>
 <li class='au'>God still does miracles</li><div class='verseref'>1 Corinithains 12:7-11</div>
 <li class='au'>God answers prayer</li><div class='verseref'>Matthew 7:7, Luke 11:9, James 1:5</div>
 <ul>
  <li class='au'>Sometimes the answer is 'yes', 'wait', 'I have something better', 'not now' or 'no'</li><div class='verseref'>James 1:17</div>
 </ul>
 <br>
 <li class='au'>We are all sinful by nature, and all have sinned and falled short of God's standard of perfection</li><div class='verseref'>Romans 3:23</div>
 <li class='au'>We can never be good enough on our own</li><div class='verseref'></div>
 <li class='au'>We need God's forgiveness to be in His presence</li><div class='verseref'></div>
 <ul>
  <li class='au'>We get this by repenting of our sins, asking for forgineness, and accepting His forgiveness</li><div class='verseref'></div>
   <ul>
<li class='au'>We do not have to say a sinner's prayer, but we must confess with our mouths, and live as followers of Christ</li><div class='verseref'></div>
   </ul>
  <li class='au'>We are covered by the Blood of Jesus which clenses our sins and makes us holy</li><div class='verseref'></div>
  <li class='au'>Jesus dwells in our hearts</li><div class='verseref'></div>
 </ul>
 <li class='au'>Without God\'s forgiveness, we are separated from Him</li><div class='verseref'></div>
 <ul>
  <li class='au'>On death or rapture, this is Hell, which is an everlasting lake of fire and isolation</li><div class='verseref'></div>
  <li class='au'>We can repent up to death or rapture, no matter how sinful we are</li><div class='verseref'></div>
  <li class='au'>The <u>only</u> unforgivable sin is blasphemy agains the Holy Spirit; equating the Holy Spirit with demons</li><div class='verseref'></div>
 </ul>
 <li class='au'>We will be known by our fruit</li><div class='verseref'></div>
 <li class='au'>Being a Christian (Christ Follower) does not make us perfect</li><div class='verseref'></div>
 <ul>
  <li class='au'>It just means we are forgiven, are striving to be like Christ</li><div class='verseref'></div>
 </ul>
 <br>
 <li class='au'>Jesus (the) Christ was and is God's one and only Son</li><div class='verseref'></div>
 <li class='au'>Jesus was subject to all temptations mankind is subject to</li><div class='verseref'></div>
 <ul>
  <li class='au'>But Jesus lived a perfect, sinless life</li><div class='verseref'></div>
 </ul>
 <li class='au'>Jesus was crucified, died and buried, and on the third day, He rose again from the dead</li><div class='verseref'></div>
 <li class='au'>Jesus will judge the living and the dead</li><div class='verseref'></div>
 <li class='au'>Jesus will come for His bride</li><div class='verseref'></div>
 <br>
 <li class='au'>God's Word, the Bible is authoritative truth</li><div class='verseref'>2 Timothy 3:16</div>
 <li class='au'>The Bible does not contradict itself</li><div class='verseref'></div>
 <li class='au'v>You cannot accept only part of the Bible; either it is all true or none is true</li><div class='verseref'>2 Timothy 3:16</div>
 <li class='au'>The Bible as we know it is not all of God's spoken or written words, but it is all relevant to everyone</li><div class='verseref'>2 Timothy 3:16</div>
 <br>
 <li class='au'>No one <i>denomination</i> is 'right' or 'wrong'</li><div class='verseref'></div>
 <li class='au'>Not everyone who goes to church will be in Heaven, but not everyone who is in Heaven will have gone to church</li><div class='verseref'></div>
</ul>
<h1>How we operate (financial/support):</h1><br>
We accept donations from
<ul>
 <li class='au'>Individual donors<b>*</b></li>
 <li class='au'>Churches<b>*</b></li>
 <li class='au'>Businesses<b>*</b></li>
</ul>
<br><b>*Pending</b>
<!-- TODO: Add Contact Us -->";
}

function howitworks()
{
 return "
<div class='tooltip'>How it Works</div>
<div class='hiw container'>
 <div id='leftbar'>
  <h1>Posting Need</h1>
  <ul>
   <li class='hiw'>You post your need.</li>
   <li class='hiw'>An email is sent to you and to the church you designated. This email allows you to:</li>
   <ul>
    <li class='hiw'>Link directly to your post</li>
    <li class='hiw'>Edit</li>
    <li class='hiw'>Close when the need is met</li>
   </ul>
   <li class='hiw'>Your church may email you directly to ask/check additional information</li>
   <li class='hiw'>If your church cannot meet the need readily, your church will approve your post and spread the need at your church</li>
   <li class='hiw'>Others will see and, as they are able, will respond.<br>Your church will receive copies of the emails</li>
   <li class='hiw'>Once your need is met, you will use your email to close the request</li>
  </ul>
  <br>
  <h2>The need is routed through your church to:</h2>
  <ul>
   <li class='hiw'>Provide you protection</li>
   <li class='hiw'>Expose your need for prayer</li>
   <li class='hiw'>Possible faster fulfillment</li>
   <li class='hiw'>Allow the giver the opportunity for possible tax credit</li>
  </ul>
 </div>
 <div id='rightbar'>
  <h1>Giving</h1>
  <ul>
   <li class='hiw'>You view the needs</li>
   <li class='hiw'>If you see a need you can help meet, you can post offering your help</li>
   <ul>
    <li class='hiw'>If your help is accepted, the church will be in contact with you to arrange things</li>
    <li class='hiw'>If your help is declined (due to need being met, for example), you have no obligation to that need</li>
   </ul>
   <li class='hiw'>You will provide the needed help</li>
  </ul>
 </div>
 <div id='footer'>God will be glorified!</div><br>
 <div id='verse'>Acts 4:32 Now the multitude of those who believed were of one heart and one soul; neither did anyone say that any of the things he possessed was his own, but they had all things in common.<br>
 34 Nor was there anyone among them who lacked; for all who were possessors of lands or houses sold them, and brought the proceeds of the things that were sold,<br>
 35 and laid them at the apostles feet; and they distributed to each as anyone had need.
 36 And Joses,[a] who was also named Barnabas by the apostles (which is translated Son of Encouragement), a Levite of the country of Cyprus, 37 having land, sold it, and brought the money and laid it at the apostles feet.
 </div><br>
 <div class='verse'>Romans 12:13 ... distributing to the needs of the saints, given to hospitality.</div><br>
 <div class='verse'>1 John 3:17 But whoever has this worlds goods, and sees his brother in need, and shuts up his heart from him, how does the love of God abide in him?</div><br>
 <div class='verse'>1 Timothy 6:18 Let them do good, that they be rich in good works, ready to give, willing to share...</div><br>
 <div class='verse'>Hebrews 13:16 But do not forget to do good and to share, for with such sacrifices God is well pleased.</div><br>
 <div class='verse'>Romans 12:10 Be kindly affectionate to one another with brotherly love, ...<br>
 13 distributing to the needs of the saints, given to hospitality.</div>
</div>";
}

function termsofservice()
{
 return "
All posts, comments and interactions should follow the purpose of the site (to allow people to post needs, and to help meet needs) and also comply with the following verses:<br><br>
<div class='verse'>Finally, brethren, whatever things are true, whatever things are noble, whatever things are just, whatever things are pure, whatever things are lovely, whatever things are of good report, if there is any virtue and if there is anything praiseworthy-meditate on these things.</div><div class='verseref'>Philippians 4:8</div><br><br>
<div class='verse'>Let each of us please his neighbor for his good, leading to edification.</div><div class='verseref'>Romans 15:2</div><br><br>
<div class='verse'>How is it then, brethren? Whenever you come together, each of you has a psalm, has a teaching, has a tongue, has a revelation, has an interpretation. Let all things be done for edification.</div><div class='verseref'>1 Corinithans 14:26</div><br><br>
<div class='verse'>Let no corrupt word proceed out of your mouth, but what is good for necessary edification, that it may impart grace to the hearers.</div><div class='verseref'>Ephisians 4:29</div><br><br>
<div class='verse'>Though I speak with the tongues of men and of angels, but have not love, I have become sounding brass or a clanging cymbal. And though I have the gift of prophecy, and understand all mysteries and all knowledge, and though I have all faith, so that I could remove mountains, but have not love, I am nothing. And though I bestow all my goods to feed the poor, and though I give my body to be burned, but have not love, it profits me nothing. Love suffers long and is kind; love does not envy; love does not parade itself, is not puffed up; does not behave rudely, does not seek its own, is not provoked, thinks no evil; does not rejoice in iniquity, but rejoices in the truth; bears all things, believes all things, hopes all things, endures all things.</div><div class='verseref'>1 Corinthians 13:1-8</div><br><br>
<div class='verse'></div><div class='verseref'></div><br><br>
Except as otherwise required by just applicable law;<br>
<h1>We do:</h1><br><br>Send first and last name, email address and the summary, text, scope, type and ip address of the poster of need to the church email address you give.<br>
&nbsp;&nbsp;&nbsp;<div class='noteind'>Note:</div> <div class='note'>".$_SESSION['orgname']." cannot control what the chuch you provide church does with the information provided to them.</div><br>
Store the information given for archival and purposes to allow people to search for previously posted needs.<br><br>
<h1>Except what is mentioned above, we do NOT:</h1><br><br>
Sell, lease, rent, give or otherwise divuldge any information to any 3rd party.<br>
Publish your last name, IP address or unobscured or improperly obscured email address to the public.";
}

function htmlajaxhtml($obj, $dest, $submitorclick)
{ // TODO: Working here
 return "
<script type='text/javascript'>
 $('$obj').".($submitorclick == ACTION_SUBMIT ? "submit" : "click")."(function(event)
 {
  var urls = $(this).attr('".($submitorclick == ACTION_SUBMIT ? "action" : "href")."').split('?'), url = urls[0], param = urls[1];
  $.post(url, param".($submitorclick == ACTION_SUBMIT ? " + '&' + $('$obj').serialize()" : "").",
   function (content) { $('$dest').html(content); });
  $('.overlay').show();
  event.preventDefault();
  return false;
 });
</script>";
}

function inputbox($name, $text, $properties = 0)
{ return "<input type='text' name='$name' placeholder='".$text."' required='required'>".
   (($properties & PROP_FOCUS) ? "<script type='text/javascript'>$('[name=$name]').focus();</script>" : ""); }
function radiobox($name, $text, $checked)
{ return '<input type="checkbox" name="'.$name.'" value="true"'.($checked ?  ' checked="true"' : '').'>'.$text; }
function inputemail($type, $text, $properties = 0)
{ return "<input type='text' name='$type' placeholder='$text' pattern='".$_SESSION['emailfilter']."'".(($properties & PROP_REQUIRED) ? "required='required'" : "")."><br>"; }
function inputtext($name, $placeholder)
{ return "<textarea class='inputtextbox' name='$name' placeholder='$placeholder' required='required'></textarea>"; }
function inputhidden($name, $id) { return "<input type='hidden' name='$name' value='$id'>"; }
function hiddeninput($pid, $rid, $cid)
{  return inputhidden("pid", $pid).inputhidden("rid", $rid).inputhidden("cid", $cid); }
function submitreset() { return "<input type='submit'><input type='reset'>"; }
function type($any = 0) // $all is set to include a search type 'any'
{
 return "
<div class='inputcheckboxdiv'>
 <div class='inputcheckboxplacementdiv' name='needtype' required='required'>".
  ($any ? "<input type='checkbox' name='any'><label for='any'>Any</label><br>" : "")."
  <input type='checkbox' name='prayer'><label for='prayer'>Prayer</label><br>
  <input type='checkbox' name='financial'><label for='financial'>Financial</label><br>
  <input type='checkbox' name='labor'><label for='labor'>Labor</label><br>
  <input type='checkbox' name='time'><label for='time'>Time</label><br>
  <input type='checkbox' name='other'><label for='other'>Other</label><br>
 </div>
</div>";
}
function status()
{
 return "
<select name='status'>
 <option value='Open'>Open</option>
 <option value='Closed'>Closed</option>
 <option value='Both'>Both</option>
</select>";
}
function scope($all = 0) // $all is set to include a search scope 'all'
{
 return "
<select name='scope'>".
 ($all ? "<option value='All'>All</option>" : "")."
 <option value='Congregation'>Congregation</option>
 <option value='City'>City</option>
 <option value='State'>State</option>
 <option value='Country'>Country</option>
 <option value='Global'>Global</option>
</select>";
}
function searchrange()
{
 return "
<select name='searchscoperange'>
 <option value='And Under'>And Under</option>
 <option value='Scope Only'>Scope Only</option>
</select>";
}
function tos()
{
 return "
<div class='right'>
 By clicking 'submit' you are agreeing with the
 <a href='".$_SESSION['dispatch']."?act=view&amp;sub=termsofservice'>Terms of Service</a>
</div>";
}

/*function select($array, $id, $class)
{
 $list = "";
 $str = "
<select id='$id' class='$class'>\n";
 foreach($item in $array) $str = " <option value='".$item['value']."'>".$item['list']."</option>\n";
 return $str."</select>";
}

 $_SESSION['month'] = array(array('name'=> "January", 'days' => 31), array('name'=> "February", 'days' => 0),
                      array('name'=> "March", 'days' => 31), array('name'=> "April", 'days' => 30),
                      array('name'=> "May", 'days' => 31), array('name'=> "June", 'days' => 30),
                      array('name'=> "July", 'days' => 31), array('name'=> "August", 'days' => 31),
                      array('name'=> "September", 'days' => 30), array('name'=> "October", 'days' => 31),
                      array('name'=> "November", 'days' => 30), array('name'=> "December", 'days' => 31));

function calendaryear($start, $end) { return select(range($end, $start), 'year', 'calendar year'); }
function calendarmonth()
{
 foreach($month in $_SESSION['month'])
 {
  
 }
}
function calendarday($year, $month) { ; }

function calendar()
{

}*/

function form($id, $act, $sub, $content)
{
 return "
<div id='".$id."div'>
 <form id='$id"."form' action='".$_SESSION['dispatch']."?act=$act&amp;sub=$sub' method='".$_SESSION['method']."'>
 $content
 </form>
</div>".
htmlajaxhtml("#$id"."form", "#".$id."div", ACTION_SUBMIT);
}

function submissionacknowledge($item)
{ return "<script type='text/javascript'>alert('We have received your $item.');</script>"; }
?>