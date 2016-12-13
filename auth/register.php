<?
include $_SERVER['DOCUMENT_ROOT'].'../config.php'; 
//include $_SERVER['DOCUMENT_ROOT'].'../auth/check.php';

if($authorized)
{
	echo json_encode(array('result' => true, 'text' => 'you are logged'));
	//header('Location: /index.php');
}

class Cregistrar
{
	public function Add($login, $email, $password)
	{
		if(!$this->CheckLogin($login))
		{
			return array('result' => false, 'text' => "error: ".$this->m_error);
		}
		
		$password = md5(md5(trim($password))); 

		mysql_query("INSERT INTO users SET login='".$login."', email='".$email."', password='".$password."'") or die ("<br>Invalid query: " . mysql_error()); 

		$user = mysql_fetch_assoc(
			mysql_query("SELECT id, password FROM `users` WHERE `login`='".mysql_real_escape_string($login)."'")
		); 

		$hash = md5($this->CodeGenerator(10)); 
		mysql_query("UPDATE users SET hash='".$hash."' WHERE id='".$user['id']."'") or die("MySQL Error: " . mysql_error()); 

		setcookie("id", $user['id'], time()+60*60*24*30, '/'); 
		setcookie("hash", $hash, time()+60*60*24*30, '/'); 
		
		return array('result' => true, 'text' => "Registration successful");
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


if(!empty($_POST["login"]))
{
	if($_POST["secondPassword"] === $_POST["password"])
	{
		$registrar = new Cregistrar;
		$result = $registrar->Add($_POST["login"],$_POST["email"],$_POST["password"]);
		echo json_encode($result);
	}
	else
	{
		echo json_encode(array('result' => false, 'text' => 'Passwords do not match'));
	}
}
?>