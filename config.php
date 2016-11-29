<?
$dbhost = "localhost";
$dbuser = "admin";
$dbpassword = "123456";
$dbname = "rgr";

mysql_connect($dbhost, $dbuser, $dbpassword) or die ("MySQL Error: " . mysql_error());
mysql_query("set names utf8") or die ("<br>Invalid query: " . mysql_error());
mysql_select_db($dbname) or die ("<br>Invalid query: " . mysql_error());
?>