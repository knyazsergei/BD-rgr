<?
include $_SERVER['DOCUMENT_ROOT'].'../config.php'; 

if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) 
{
	$userdata = mysql_fetch_assoc(mysql_query("SELECT * FROM users WHERE id = '".intval($_COOKIE['id'])."'"));

	if(($userdata['hash'] !== $_COOKIE['hash']) || ($userdata['id'] !== $_COOKIE['id'])) 
	{
		$authorized = false;
		setcookie('id', '', time() - 60*24*30*12, '/'); 
		setcookie('hash', '', time() - 60*24*30*12, '/');
		if(!$authPage)
		{
			header('Location: /auth/');
			exit();
		}
	}
	else
	{
		$authorized = true;
	}
}
else
{
	$authorized = false;
	if(!$authPage)
	{
		header('Location: /auth/');
		exit();
	}
}
?>