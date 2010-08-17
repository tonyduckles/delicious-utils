<?php
/*
  Name: delicious-xml2rss.php
  Description: Given a Delicious "posts/all"-style XML text, output an
    RSS-style XML feed.
  Author: Tony Duckles <tony@nynim.org>

  Usage:
  1) Create the "userinfo.inc.php" file, with your Delicious username and
     full-name, e.g.:
       <?php
         define('DELICIOUS_USERNAME', 'username');
       ?>
  2) Run this script and redirect the output as needed:
       $ php delicious-xml2rss.php < delicious-posts.xml > delicious-rss.xml
*/

  // Load the (private) "auth_info.inc.php" file
  require('userinfo.inc.php');
  if (!defined('DELICIOUS_USERNAME'))
    define('DELICIOUS_USERNAME', 'username');

  // Print the initial '<?xml ... >' line using PHP code, so that the trailing 
  // "?"+">" doesn't confuse PHP and/or editor syntax-highlighting.
  print '<?xml version="1.0" encoding="UTF-8"?>'."\n";
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:wfw="http://wellformedweb.org/CommentAPI/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:cc="http://web.resource.org/cc/">
  <channel>
   <title>Delicious/<? print DELICIOUS_USERNAME; ?></title>
    <link>http://delicious.com/<? print DELICIOUS_USERNAME; ?></link>
    <description>bookmarks posted by <? print DELICIOUS_USERNAME; ?></description>
    <atom:link rel="self" type="application/rss+xml" href="http://feeds.delicious.com/v2/rss/<? print DELICIOUS_USERNAME; ?>?count=15"/>
    <cc:license rdf:resource="http://creativecommons.org/licenses/by/2.5/"/>
<?php
  $lines = file('php://stdin');
  foreach ($lines as $line_num => $line) {
    if ($line_num > 1) {
      $pattern = '/<post href="(.*)" hash="(.*)" description="(.*)" tag="(.*)" time="(.*)" extended="(.*)" meta="(.*)"/';
      if (preg_match($pattern, $line, $matches) > 0) {
        echo '    <item>'."\n";
        echo '      <title>'.$matches[3].'</title>'."\n";
        echo '      <pubDate>'.date('r', strtotime(str_replace('T', ' ',$matches[5]))).'</pubDate>'."\n";
        echo '      <guid isPermaLink="false">http://delicious.com/url/'.$matches[2].'#'.DELICIOUS_USERNAME.'</guid>'."\n";
        echo '      <link>'.$matches[1].'</link>'."\n";
        echo '      <dc:creator><![CDATA['.DELICIOUS_USERNAME.']]></dc:creator>'."\n";
        echo '      <comments>http://delicious.com/url/'.$matches[2].'</comments>'."\n";
        echo '      <wfw:commentRss>http://feeds.delicious.com/v2/rss/url/'.$matches[2].'</wfw:commentRss>'."\n";
        echo '      <source url="http://feeds.delicious.com/v2/rss/'.DELICIOUS_USERNAME.'">'.DELICIOUS_USERNAME.'\'s bookmarks</source>'."\n";
        $tags = explode(' ', $matches[4]);
        foreach ($tags as $tag) {
          echo '      <category domain="http://delicious.com/'.DELICIOUS_USERNAME.'/">'.$tag.'</category>'."\n";
        }
        echo '    </item>'."\n";
      }
    }
  }
?>
  </channel>
</rss>
