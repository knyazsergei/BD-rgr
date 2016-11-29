<?
include '../config.php'; 

function CheckLogin($login)
{
	return (strlen($login) > 3) && preg_match("/^[a-z]+([-_]?[a-z0-9]+){0,2}$/i",$login);
}

$login = $_GET["login"]

if(CheckLogin())
{
	echo "all ok";
}
else
{
	echo "login is bad";
}

$query = mysql_query("SELECT COUNT(*) FROM users WHERE login='".mysql_real_escape_string($login)."'")or die ("<br>Invalid query: " . mysql_error()); 
if(mysql_result($query, 0) > 0) 
{
	echo "User exists"; 
} 
?>