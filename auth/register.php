<?
include '../config.php'; 

class Cregistrar
{
	public function Add($login, $email, $password)
	{
		if(!$this->CheckLogin($login))
		{
			echo "error: ".$this->m_error;
			return;
		}
		
		$password = md5(md5(trim($password))); 

		mysql_query("INSERT INTO users SET login='".$login."', email='".$email."', password='".$password."'") or die ("<br>Invalid query: " . mysql_error()); 
		echo "Registration successful";
	}

	private function CheckLogin($login)
	{
		if(!(strlen($login) > 3) && preg_match("/^[a-z]+([-_]?[a-z0-9]+){0,2}$/i",$login))
		{
			$this->m_error = "Invalid login";
			return false;
		}

		$query = mysql_query("SELECT COUNT(*) FROM users WHERE login='".mysql_real_escape_string($login)."'") or die ("<br>Invalid query: " . mysql_error()); 
		
		if(mysql_result($query, 0) > 0) 
		{
			$this->m_error = "User exists"; 
			return false;
		} 
		return true;

	}

	private $m_error = "Unknown error";
}

$registrar = new Cregistrar;
$registrar->Add($_GET["login"],$_GET["email"],$_GET["password"]);
?>