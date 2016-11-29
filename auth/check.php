<?
include '../config.php'; 

if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) 
{
	$userdata = mysql_fetch_assoc(mysql_query("SELECT * FROM users WHERE id = '".intval($_COOKIE['id'])."'"));

	if(($userdata['hash'] !== $_COOKIE['hash']) || ($userdata['id'] !== $_COOKIE['id'])) 
	{
		setcookie('id', '', time() - 60*24*30*12, '/'); 
		setcookie('hash', '', time() - 60*24*30*12, '/');
		header('Location: login.php');
		exit();
	}
}
echo "You are logged";
?>