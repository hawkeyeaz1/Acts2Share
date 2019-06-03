<?php
session_start();
header("Content-type: text/css; charset: UTF-8");
$tabwidth = 23;
$_SESSION['top_gradient'] = "#efe";
$_SESSION['bottom_gradient'] = "#ccf";
$_SESSION['tab_top_gradient'] = "#fff";
$_SESSION['tab_bottom_gradient'] = "#efe";
$_SESSION['hover_tab_top_gradient'] = "#ffffbb";
$_SESSION['hover_tab_bottom_gradient'] = "#efe";
function generalstyle()
{
 return "html, body { display: inline; float: left; width: 100%; margin: 0px; border: 0px; padding: 0px; overflow: hidden; }

.arrow
 {
 border-radius: 10px;
 width: 20px;
 height: 20px;
 border: 1px solid #3A3A3A;
 text-align: center;
 position: absolute;
 margin: 0px auto;
 top: 50%;
 z-index: 100;
 font-weight: bold;
 box-shadow: 2px 2px 2px 1px #ccc;
 letter-spacing: 2px;
 line-height: 20px;
 background-color: #fff;
 outline: 2px outset #bbbb;
}
.left { display: inline; float: left; left: 2px; }
.right { display: inline; float: right; right: 2px; }
.innerbox { height: 100%; padding: 0px; margin: 0px; }
.innerbox
{
 width: 96%;
 height: 100%;
 display: inline;
 postition: absolute;
 overflow-y: auto;
 overflow-x: hidden;
 left: 2%;
 border: 0px;
 padding: 0px;
 margin: 0px auto;
}
.howitworks,.list,.aboutus { position: absolute; }
.howitworks,.aboutus { display: none; width: 0%; } /* Hide: Not primary view */
.postbox,.searchbox { display: none; width: 100%; height: auto; max-height: 50%; border: 1px solid black; position: relative; }
.postlist { position: relative; padding: 0px; }
.tooltip,#displaytooltip { display: none; }
.closepane { z-index: 4; }
.overlay { display: none; z-index: 3; }
#displaytooltip { position: absolute; background-color: yellow; border: 1px solid black; z-index: 2; }";
}

function panestyle()
{
 return ".pane
{
 width: 100%;
 height: 95%;
 margin: auto;
 overflow-x: hidden;
 overflow-y: auto;
 background: -webkit-gradient(linear, left top, left bottom, from(".$_SESSION['top_gradient']."), to(".$_SESSION['bottom_gradient']."));
 background: -webkit-linear-gradient(top, ".$_SESSION['top_gradient'].", ".$_SESSION['bottom_gradient'].");
 background: -moz-linear-gradient(top, ".$_SESSION['top_gradient'].", ".$_SESSION['bottom_gradient'].");
 background: -ms-linear-gradient(top, ".$_SESSION['top_gradient'].", ".$_SESSION['bottom_gradient'].");
 background: -o-linear-gradient(top, ".$_SESSION['top_gradient'].", ".$_SESSION['bottom_gradient'].");
}";
}

function liststyle()
{
 return "h6 { display: inline; }
.postlink { display: inline; }
.username { display: inline; }
.displayemail { display: inline; }
.postdate { display: inline; }";
}

function headingstyle()
{ //font-weight: bold;
 return ".heading
   {
    text-align: left;
    background-color: #FFFFFF;
    color: #3A3A3A;
    font-family: Calligraffitti;
    font-size: 16.0pt;
    line-height: 100%;
    min-height: 21px;
    max-height: 4%;
    text-shadow: 2.0px 2.0px 2.0px $bottom_gradient;
    margin: 8px;
    text-align: center;
    width: 95%;
   }";
}

function searchstyle()
{
/*
.searchpane
   {
    height: 125px;
    width: 300px;
    right: 21px;
    background-color: <?=$top_gradient ?>;
    position: absolute;
    z-index: 2;
   }
*/
 /*
 return ".input { display: inline; width: 77%; }
.searchinputcheckboxdiv { display: inline; float: right; top: 0px; width: 25%; position: absolute; }
.searchinputcheckboxplacementdiv { float: left; width: 100%; }
input[type=submit] { display: block; position: relative; }"; // change to search specific!!
*/
}

function poststyle()
{
 return ".singlepost { text-align: center; line-height: 70%; }";
 /*
 return ".inputtextbox
{
 float: left;
 width: 84%;
 min-height: 101px;
 max-height: 100%;
}
.postinputcheckboxdiv { float: right; width: 15%; }
.postinputcheckboxplacementdiv { float: left; width: 100%; }
.scope { float: right; top: 0px; }
.right { float: right; }";
 */
}

function commentstyle()
{
 /*
 return ".inputtextbox
{
 float: left;
 width: 100%;
 min-height: 100px;
 max-height: 100%;
}
.right { float: right; }";
 */
}

function austyle()
{
 //return ".au { overflow-y: auto; height: 95%; }";
 /*
 return ".verseref { display: inline; padding: 10px; font-style: italic; }";
*/
}

function hiwstyle()
{
 return "
/*li.hiw
{
 margin: 0px;
 padding: 0px;
 border: 0px;
 float: left;
 text-indent: 0;
 width: 20%;
 text-align: left;
}
ul.hiw
{
 -webkit-margin-before: 0px;
 -webkit-margin-after: 0px;
 -webkit-margin-start: 0px;
 -webkit-margin-end: 0px;
 -webkit-padding-start: 0px;
}*/
#rightbar
{
 float: right;
 width: 46%;
 padding: 10px;
}
#leftbar
{
 float: left;
 width: 46%;
 padding: 10px;
}
#footer
{
 clear: both;
 text-align: center;
 font-weight: bold;
 font-size: 24.0px;
}
div.hiw { overflow-y: auto; height: 95%; }";
}

function tosstyle()
{
 /*
 return ".verse { /*display: inline;* / text-align: center; }
  .verseref { text-align: center; }
  .noteind { font-style: italic; display: inline; }
  .note { text-decoration: underline; display: inline; }";
*/
}

function tabstyle()
{
 return ".mainframe,.center,.tabstyle,.tabs { margin: 0px; border: 0px; padding: 0px; overflow: hidden; }
.mainframe,.center { width: 100%; }
.mainframe { height: 97%; position: absolute; }
ul { width: 90%; }
.tabstyle,.tabs { display: inline; }
.center { margin: 0 auto; align: center; width: 103% }
.tabs
{
 float: left;
 list-style: none;
 padding: 0px;
 min-height: 5%;
 max-height: 20px;
 width: 50%;
 text-align: center;
 background: -webkit-gradient(linear, left top, left bottom, from(".$_SESSION['tab_top_gradient']."), to(".$_SESSION['tab_bottom_gradient']."));
 background: -webkit-linear-gradient(top, ".$_SESSION['tab_top_gradient'].", ".$_SESSION['tab_bottom_gradient'].");
 background: -moz-linear-gradient(top, ".$_SESSION['tab_top_gradient'].", ".$_SESSION['tab_bottom_gradient'].");
 background: -ms-linear-gradient(top, ".$_SESSION['tab_top_gradient'].", ".$_SESSION['tab_bottom_gradient'].");
 background: -o-linear-gradient(top, ".$_SESSION['tab_top_gradient'].", ".$_SESSION['tab_bottom_gradient'].");
}
.tabs:hover
{
 border-top-color: ".$_SESSION['top_gradient'].";
 background: ".$_SESSION['top_gradient'].";
 background: -webkit-linear-gradient(top, ".$_SESSION['hover_tab_top_gradient'].", ".$_SESSION['hover_tab_bottom_gradient'].");
 background: -moz-linear-gradient(top, ".$_SESSION['hover_tab_top_gradient'].", ".$_SESSION['hover_tab_bottom_gradient'].");
 background: -ms-linear-gradient(top, ".$_SESSION['hover_tab_top_gradient'].", ".$_SESSION['hover_tab_bottom_gradient'].");
 background: -o-linear-gradient(top, ".$_SESSION['hover_tab_top_gradient'].", ".$_SESSION['hover_tab_bottom_gradient'].");
 border-top-left-radius: 10px;
 border-top-right-radius: 10px;
}";
 /*return ".followtab, input[type=hidden], form { border: 0px; margin: 0px; padding: 0px; display: inline; }
.followtab, form { background-color: orange; }
.followtab:hover, form:hover { background-color: green; }
.followtab { margin: auto; }";*/
}

function scopestyle()
{
 return "h6.scope
{
 display: inline;
 color: green;
}";
}

function typestyle()
{
  return "h5.type
{
 display: inline;
 color: pink;
}";
}

function datestyle()
{
 return ".postdate
{
 display: inline;
 color: red;
}";
}

function emailstyle()
{
 return ".displayemail
{
 display: inline;
 color: orange;
}";
}

function namestyle()
{
 return ".username
{
 display: inline;
 color: purple;
}";
}

function versestyle()
{
 return "";
}

echo generalstyle() . "\n" .
     headingstyle() . "\n" .
     scopestyle() . "\n" .
     typestyle() . "\n" .
     datestyle() . "\n" .
     emailstyle() . "\n" .
     namestyle() . "\n" .
     searchstyle() . "\n" .
     poststyle() . "\n" .
     commentstyle() . "\n" .
     austyle() . "\n" .
     hiwstyle() . "\n" .
     tosstyle() . "\n" .
     tabstyle() . "\n" .
     panestyle() . "\n" .
     liststyle();
?>