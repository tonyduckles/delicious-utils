<?php
/*
  Name: delicious-dump-alltags.php
  Description: Makes a "tags/all" Delicious API call and writes the output to
    stdout. If there's any problems, error-messages are written to stderr.
  Author: Tony Duckles <tony@nynim.org>

  Uses the fantastic "php-delicious" library created by E.J. Eliot:
    http://www.phpdelicious.com/

  Usage:
  1) Create the "auth_info.inc.php" file, with your Delicious username and
     password, e.g.:
       <?php
         // Private username+password info
         define('AUTH_DELICIOUS_USERNAME', 'username');
         define('AUTH_DELICIOUS_PASSWORD', 'password');
       ?>
  2) Run this script and redirect the output as needed:
       $ php delicious-dump-alltags.php > delicious-tags.xml
*/

  // Load php-delicious library
  require('php-delicious/php-delicious.inc.php');
  // Load the (private) "auth_info.inc.php" file
  require('auth_info.inc.php');

  $sCmd = PHP_DELICIOUS_BASE_URL.'tags/get';
  $oPhpDelicious = new PhpDelicious(AUTH_DELICIOUS_USERNAME, AUTH_DELICIOUS_PASSWORD);
  if ($sXml = $oPhpDelicious->HttpRequest($sCmd)) {
   if (strlen($sXml) > 0) {
     //// Strip last two lines off the file, because these contain a timestamp
     //$sXml = substr($sXml, 0, strrpos($sXml,"\n"));
     //$sXml = substr($sXml, 0, strrpos($sXml,"\n"));

     // If $sXml contains no line-breaks, pretty-ify the XML
     if (strpos($sXml, "\n") !== true) {
       $sXml = str_replace("?><tags ","?>\n<tags ", $sXml);
       $sXml = str_replace("><tag ",">\n  <tag ", $sXml);
       $sXml = str_replace("></tags>",">\n</tags>", $sXml);
     }

     fwrite(STDOUT, $sXml);
   }
  } else {
   fwrite(STDERR, "Error making HttpRequest(\"sCmd\"): LastErrorNo = ".$oPhpDelicious->LastErrorNo()."\n");
  }

?>
