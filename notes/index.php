<?
include_once $_SERVER['DOCUMENT_ROOT'].'../config.php'; 
include_once $_SERVER['DOCUMENT_ROOT'].'../auth/check.php';
include_once $_SERVER['DOCUMENT_ROOT'].'../notes/tages.php';

class CNotes
{
	function __construct($userId, $mysqli)
	{
		$this->m_userId = $userId;
		$this->m_mysqli = $mysqli;
	}

	public function LastId()
	{
		$result = $this->m_mysqli->query("SELECT MAX(id) FROM `notes` WHERE `author_id`='".mysql_real_escape_string($this->m_userId)."'");
		$row = $result->fetch_row();
		return $row[0];
	}

	public function GetList($page = 0)
	{
		$sql = "SELECT `sortingDir` FROM `users` WHERE `id`='".mysql_real_escape_string($this->m_userId)."'";
		$result = $this->m_mysqli->query($sql);
		$direction = $result->fetch_row();

		$range[0] = $page * $this->m_numberNotesPage;
		$range[1] = ($page + 1) * $this->m_numberNotesPage;

		$sql = "SELECT * FROM `notes` 
			WHERE `author_id`='".mysql_real_escape_string($this->m_userId)."' 
			ORDER BY `date` ".$direction[0]."
			LIMIT ".$range[0].",".$range[1];
		$result = $this->m_mysqli->query($sql);
		
		while ($row = $result->fetch_assoc()) 
		{
        	$final[] = $row;
    	}

    	for ($i = 0; $i < count($final); $i++) 
    	{
			if(strlen($final[$i]["description"]) > 150)
			{
				$string = $final[$i]["description"];
				$string = substr($string, 0, 147);
				$string = rtrim($string, "!,.-");
				$string = substr($string, 0, strrpos($string, ' '));
				$string = $string."...";
				$final[$i]["description"] = $string;
			}
		}

		return $final;
	}


	public function SearchNotes($page = 0, $q)
	{
		$sql = "SELECT `sortingDir` FROM `users` WHERE `id`='".mysql_real_escape_string($this->m_userId)."'";
		$result = $this->m_mysqli->query($sql);
		$direction = $result->fetch_row();

		$range[0] = $page * $this->m_numberNotesPage;
		$range[1] = ($page + 1) * $this->m_numberNotesPage;

		$sql = "SELECT * FROM `notes` 
			WHERE `author_id`='".mysql_real_escape_string($this->m_userId)."' AND 
			`title` LIKE '%".mysql_real_escape_string($q)."%'
			ORDER BY `id` ".$direction[0]."
			LIMIT ".$range[0].",".$range[1];
		$result = $this->m_mysqli->query($sql);
		
		while ($row = $result->fetch_assoc()) {
        	$final[] = $row;
    	}

    	for ($i = 0; $i < count($final); $i++) 
    	{
			if(strlen($final[$i]["description"]) > 150)
			{
				$string = $final[$i]["description"];
				$string = substr($string, 0, 147);
				$string = rtrim($string, "!,.-");
				$string = substr($string, 0, strrpos($string, ' '));
				$string = $string."...";
				$final[$i]["description"] = $string;
			}
		}

		return $final;
	}


	public function GetNote($id)
	{
		$sql = "SELECT * FROM `notes` 
			WHERE `id`='".mysql_real_escape_string($id)."'";
		$result = $this->m_mysqli->query($sql);
		return $result->fetch_assoc();
	}

	public function GetCount()
	{
		$result = $this->m_mysqli->query("SELECT COUNT(*) FROM `notes` WHERE `author_id`='".mysql_real_escape_string($this->m_userId)."'");
		$row = $result->fetch_row();
		return $row[0];
	}

	public function AddNote($noteData)
	{
		$noteData["author_id"] = $_COOKIE['id'];

		$sql = "INSERT INTO notes SET title='".$noteData["title"]."', 
			description='".$noteData["description"]."', 
			video='".$noteData["video"]."',
			author_id='".$noteData["author_id"]."',
			date=CURRENT_TIMESTAMP;";
		$sql .= "SELECT MAX(id) FROM notes WHERE author_id = '".$noteData["author_id"]."';";
		
		if(!$this->m_mysqli->multi_query($sql))
		{
			echo "Не удалось выполнить мультизапрос: (" . $m_mysqli->errno . ") " . $m_mysqli->error;
		}
		$noteId = 0;
		
		do
		{
			if ($result = $this->m_mysqli->store_result()) 
			{
        	    $row = $result->fetch_row();
        	    $noteId = $row[0];
           		$result->free();
        	}
		}while($this->m_mysqli->next_result());
		
		return $noteId;
	}

	public function ChangeNote($id, $description, $title)
	{
		
		$sql = "UPDATE `notes`
			SET `description` = '".mysql_real_escape_string($description)."', `title` = '".mysql_real_escape_string($title)."', `date` = CURRENT_TIMESTAMP
			WHERE `id`='".mysql_real_escape_string($id)."'
		";
		$this->m_mysqli->query($sql);

		$sql = "SELECT date FROM `notes` WHERE `id`='".mysql_real_escape_string($id)."'";
		$result = $this->m_mysqli->query($sql);
		return $result->fetch_assoc();
	}	

	public function Remove($id)
	{
		mysql_query("
			DELETE FROM `notes` 
			WHERE `id`='".$id."'"
		) or die ("<br>Invalid query: " . mysql_error()); ;
	}

	private $m_userId = 0;
	private $m_numberNotesPage = 10;
	private $m_mysqli;
}

$notes = new CNotes($_COOKIE['id'], $mysqli);

if($_GET["action"] == "addNote")
{
	
	$noteData["title"] = "Без названия";
	$noteData["description"] = "";
	$noteData["video"] = "";
	$noteData["picture"] = "";

	$noteId = $notes->AddNote($noteData);
	$note = $notes->GetNote($noteId);

	echo json_encode(array('title' => $note["title"], 'description' => $note["description"], 'date' => $note["date"], 'id' => $note["id"]));
}
if($_GET["action"] == "getNote")
{
	$note = $notes->GetNote($_GET["id"]);
	echo json_encode(array('title' => $note["title"], 'description' => $note["description"], 'date' => $note["date"]));
}

if($_GET["action"] == "ChangeNote")
{
	$description = $_POST["description"];
	$title = $_POST["title"];
	$id = $_POST["id"];
	$result = $notes->ChangeNote($id, $description, $title);
	echo json_encode(array('date' => $result["date"]));
}

if($_GET["action"] == "searchNotes")
{
	$page = $_GET["page"];
	$q = $_POST["q"];
	$result = $notes->SearchNotes($page, $q);

	echo json_encode($result);
}

if($_GET["action"] == "getNotes")
{
	$page = $_GET["page"];
	$result = $notes->GetList($page);
	
	echo json_encode($result);
}

if($_GET["action"] == "remove")
{
	$notes->Remove($_GET["id"]);
}
if($_GET["action"] == "getCount")
{
	$result = $notes->GetCount();
	echo json_encode($result);
}
?>