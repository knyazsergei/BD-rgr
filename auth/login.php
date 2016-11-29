<?
include '../config.php'; 

class Cauthorizer
{
	public function Check($login, $password)
	{
		$user = mysql_fetch_assoc(
			mysql_query("SELECT id, password FROM `users` WHERE `login`='".mysql_real_escape_string($login)."'")
		); 

		if(empty($user))
		{
			$this->m_error = "Unknown user";
			echo $this->m_error;
			return false;
		}

		if(!($user['password'] === md5(md5($password))))
		{
			$this->m_error = "Invalid password";
			echo $this->m_error;
			return false;
		} 

		echo "You are logged";
	}

	private $m_error = "Unknown error";
}

$authorizer = new Cauthorizer;
$authorizer->Check($_GET["login"], $_GET["password"]);
?> 