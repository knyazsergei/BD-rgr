<?
include '../config.php'; 

function CheckLogin($login)
{
	return (strlen($login) > 3) && preg_match("/^[a-z]+([-_]?[a-z0-9]+){0,2}$/i",$login);
}

if(CheckLogin($_GET["login"]))
{
	echo "all ok";
}
else
{
	echo "login is bad";
}
?>