<?
$authPage = true;

if(!empty($_GET["action"]))
{
	include $_SERVER['DOCUMENT_ROOT'].'../auth/register.php';
	include $_SERVER['DOCUMENT_ROOT'].'../templates/auth/register.php';
}
else
{
	include $_SERVER['DOCUMENT_ROOT'].'../auth/login.php';
	include $_SERVER['DOCUMENT_ROOT'].'../templates/auth/index.php';
}
?>