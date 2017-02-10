<?
$authPage = true;
include $_SERVER['DOCUMENT_ROOT'].'../auth/check.php';

if(!empty($_GET["action"]))
{
	include $_SERVER['DOCUMENT_ROOT'].'../templates/auth/register.php';
}
else
{
	include $_SERVER['DOCUMENT_ROOT'].'../templates/auth/index.php';
}
?>