<?php

function sendpostemail()
{// Text only, if we do HTML, WE NEED TO HTML ENCODE ENTITIES!
 $headers = "
From:".$_SESSION['personalemail']."\r\n".
"MIME-Version: 1.0\r\n".
"Content-type: text/plain; charset=utf-8\r\n".
"Reply-To: webmaster@".$_SESSION['site']."\r\n".
"Return-Path:<webmaster@".$_SESSION['site']."\r\n";
 $body = "
Scope: ".expandscope($_SESSION['scope'])."

Type: ".expandtype($_SESSION['type'])."

Summary: ".$_SESSION['summary']."

Need: ".
$_SESSION['need'];
 $footer = "
This was posted from ".inet_ntop($_SESSION['ip']).".

You can visit ".$_SESSION['orgname']." at ".$_SESSION['url'];
 if($_SESSION['personalemail'] !== "" && $_SESSION['churchemail'] !== "")
 { // TODO: Conditionaly send emails; if a bounce occurs, do not send the other to the individual
  $to = $_SESSION['personalemail'];
  $subject = $_SESSION['orgname'].": ".expandtype($_SESSION['type'])." for ".expandscope($_SESSION['scope']);
  $message ="
Hello ".$_SESSION['firstname']." ".$_SESSION['lastname'].",

This is to confirm your need has been posted and sent to ".$_SESSION['churchemail'].".

$body

To edit or to close your post, click on here: ".$_SESSION['url']."/".$_SESSION['dispatch']."?act=view&sub=need&pid=".$_SESSION['postID']."&rid=".$_SESSION['rid']."&uid=".$_SESSION['uid']."

Please note: This does not currently allow editing or cloning!

This email was sent at the request of someone providing ".$_SESSION['personalemail']." as their email address.
$footer";
// TODO: make the user post editable
  mail($to, $subject, $message, $headers);

  // To the church contact
  $to = $_SESSION['churchemail'];
  $subject = $_SESSION['orgname'].": ".$_SESSION['firstname']." ".$_SESSION['lastname'].", ".expandtype($_SESSION['type'])." for ".expandscope($_SESSION['scope']);
  $message = "
Hello ".$_SESSION['churchemail'].",


This is to let you know that ".$_SESSION['firstname']." ".$_SESSION['lastname']." has posted a need at ".$_SESSION['orgname']." and specified you as the contact church.
Please visit ".$_SESSION['url']."/".$_SESSION['dispatch']."?act=view&sub=need&pid=".$_SESSION['postID']."&rid=".$_SESSION['rid']."&cid=".$_SESSION['cid']." to view, approve, and to close the request when the need is met.

$body

$footer";
  mail($to, $subject, $message, $headers);
 }
 else return "Email addresses cannot match! Email not sent.";

}

function sendcommentemail()
{
 // TODO
}

?>