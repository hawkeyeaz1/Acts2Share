<?php
/* Postback is designed to protect the *end user* from html scripts posted as 'post content'. It forces the data to be viewed as plain text (rather than html). It is intended to be only *one* means of protection, along with excaping the html. */
//header('Content-Type: text/plain; charset=UTF-8');
require("css.php");
if(isset($_REQUEST['safe'])) echo urldecode($_REQUEST['safe']);
?>