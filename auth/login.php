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

		$hash = md5($this->CodeGenerator(10)); 
		mysql_query("UPDATE users SET hash='".$hash."' WHERE id='".$user['id']."'") or die("MySQL Error: " . mysql_error()); 

		setcookie("id", $user['id'], time()+60*60*24*30, '/'); 
		setcookie("hash", $hash, time()+60*60*24*30, '/'); 

		echo "You are logged";
	}

	private function CodeGenerator($length=6) 
	{ 
    	$chars = implode("",range('a', 'z'));
    	$code = ""; 
    	$clen = strlen($chars) - 1;   
    	while (strlen($code) < $length) 
    	{ 
    	    $code .= $chars[mt_rand(0,$clen)];   
    	} 
    	return $code; 
	}

	private $m_error = "Unknown error";
}

$authorizer = new Cauthorizer;
$authorizer->Check($_GET["login"], $_GET["password"]);
?> 