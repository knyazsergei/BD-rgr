<?
include_once $_SERVER['DOCUMENT_ROOT'].'../config.php'; 
include_once $_SERVER['DOCUMENT_ROOT'].'../auth/check.php';

class CNotes
{
	function __construct($userId, $mysqli)
	{
		$this->m_userId = $userId;
		$this->m_mysqli = $mysqli;
	}
	public function ChangeSortingOrder()
	{
		$sql = "SELECT `sortingDir` FROM `users` WHERE `id`='".mysql_real_escape_string($this->m_userId)."'";
		$result = $this->m_mysqli->query($sql);
		$row = $result->fetch_row();
		
		$dir = "ASC";

		if($row[0] == $dir)
		{
			$dir = "DESC";
		}
		
		
		$sql = "UPDATE `users`
			SET `sortingDir` = '".$dir."'
			WHERE `id`='".mysql_real_escape_string($this->m_userId)."'
		";
		$result = $this->m_mysqli->query($sql);
	}	

	private $m_userId = 0;
	private $m_mysqli;
}

$profile = new CNotes($_COOKIE['id'], $mysqli);

if($_GET["action"] == "setDSortingOrder")
{
	$profile->ChangeSortingOrder();
}
?>