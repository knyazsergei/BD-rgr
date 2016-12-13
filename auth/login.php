<?
include $_SERVER['DOCUMENT_ROOT'].'../config.php'; 
include $_SERVER['DOCUMENT_ROOT'].'../auth/check.php';

if($authorized)
{
	header('Location: /index.php');
}

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
			return array('result' => false, 'text' => $this->m_error);
		}

		if(!($user['password'] === md5(md5($password))))
		{
			$this->m_error = "Invalid password";
			return array('result' => false, 'text' => $this->m_error);
		}
		
		$hash = md5($this->CodeGenerator(10)); 
		mysql_query("UPDATE users SET hash='".$hash."' WHERE id='".$user['id']."'") or die("MySQL Error: " . mysql_error()); 

		setcookie("id", $user['id'], time()+60*60*24*30, '/'); 
		setcookie("hash", $hash, time()+60*60*24*30, '/'); 
		
		return array('result' => true, 'text' => 'complete');
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
	$authorizer = new Cauthorizer;
	$result = $authorizer->Check($_POST["login"],$_POST["password"]);
	echo json_encode($result);
}?> 