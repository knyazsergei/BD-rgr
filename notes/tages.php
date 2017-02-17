<?
include_once $_SERVER['DOCUMENT_ROOT'].'../config.php'; 
include_once $_SERVER['DOCUMENT_ROOT'].'../auth/check.php';

class CTages 
{
	function __construct($userId, $mysqli)
	{
		$this->m_userId = $userId;
		$this->m_mysqli = $mysqli;
	}

	function Update($allTages, $postId)
	{

		$tagesArr = explode(',', $allTages);
		$tagesInBd = $this->GetTages($postId);
		$removed = array_diff($tagesInBd, $tagesArr);
		
		$added = array_diff($tagesArr, $tagesInBd);

		foreach ($added as $word) {
			$this->AddTage($word, $postId);
		}

		foreach ($removed as $word) {
			$this->RemoveTage($word, $postId);
		}

	}

	function RemoveTage($word, $postId)
	{
		$sql = "
			DELETE FROM `tages` 
			WHERE `word`='".$word."' AND `note_id` = '".$postId."' 
		";
		$result = $this->m_mysqli->query($sql);
	}

	function GetTages($postId)
	{
		$sql = "
			SELECT `word` FROM `tages` 
			WHERE `note_id`='".mysql_real_escape_string($postId)."'
		";
		$result = $this->m_mysqli->query($sql);
		$result = $result->fetch_all();
		$endResult = array();

		for($i = 0; $i < count($result);$i++)
		{
			$endResult[] = $result[$i][0];
		}

		return $endResult;
	}

	function GetTageId($word)
	{
		$id = $this->FindTage($word);
		if (empty($id)) 
		{
			$id = $this->AddTage($word);
		}
		return $id;
	}

	function GetTageById($id)
	{
		$sql = "
			SELECT `word` FROM `tages` 
			WHERE `id`='".mysql_real_escape_string($id)."'
		";
		$result = $this->m_mysqli->query($sql);
		$result = $result->fetch_assoc();
		return $result["word"];
	}

	private function AddTage($word, $postId)
	{
		$sql = "INSERT INTO tages SET word='".$word."', 
			note_id='".$postId."', 
			date=CURRENT_TIMESTAMP;
		";

		$sql .= "SELECT MAX(id) FROM tages;";
		
		if(!$this->m_mysqli->multi_query($sql))
		{
			echo "Не удалось выполнить мультизапрос: (" . $m_mysqli->errno . ") " . $m_mysqli->error;
		}

		$tageId = 0;
		
		do
		{
			if ($result = $this->m_mysqli->store_result()) 
			{
        	    $row = $result->fetch_row();
        	    $tageId = $row[0];
           		$result->free();
        	}
		} while($this->m_mysqli->next_result());
		
		return $tageId;
	}

	private function FindTage($word)
	{
		$sql = "
			SELECT id FROM `tages` 
			WHERE `word`='".mysql_real_escape_string($word)."'
		";
		$result = $this->m_mysqli->query($sql);
		$result = $result->fetch_assoc();
		return $result["id"];
	}

	private function IncrementTage($word)
	{
		$sql = "
			UPDATE `tages`
			SET `count` = `count`+ 1
			WHERE `word`='".mysql_real_escape_string($word)."'
		";
		$result = $this->m_mysqli->query($sql);
	}

	private $m_userId = 0;
	private $m_mysqli;
}

$tages = new CTages($_COOKIE['id' ], $mysqli);

if($_GET["action"] == "update")
{
	if(empty($_POST["tages"])||empty($_POST["postId"]))
	{

	}
	else
	{
		$allTages = $_POST["tages"];
		$postId = $_POST["postId"];
		$tages->Update($allTages, $postId);
	}
}

if($_GET["action"] == "getTages")
{
	if(!empty($_POST["postId"]))
	{
		$tagesArr = $tages->GetTages($_POST	["postId"]);
		echo json_encode($tagesArr);
	}
}