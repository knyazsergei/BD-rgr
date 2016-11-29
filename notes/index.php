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

	public function GetList($page = 0)
	{
		$range[0] = $page * $this->m_numberNotesPage;
		$range[1] = ($page + 1) * $this->m_numberNotesPage;
		$notes = mysql_query("
			SELECT * FROM `notes` 
			WHERE `author_id`='".mysql_real_escape_string($this->m_userId)."' 
			ORDER BY `date` ASC
			LIMIT ".$range[0].",".$range[1]
		) or die ("<br>Invalid query: " . mysql_error()); ;

		return $notes;
	}

	public function GetNote($id)
	{
		$query = "SELECT * FROM `notes` 
			WHERE `id`='".mysql_real_escape_string($id)."'";
		$result = $this->m_mysqli->query($query);
		return $result->fetch_assoc();
	}


	public function AddNote($noteData)
	{
		$noteData["author_id"] = $_COOKIE['id'];

		$sql = "INSERT INTO notes SET title='".$noteData["title"]."', 
			description='".$noteData["description"]."', 
			video='".$noteData["video"]."',
			picture='".$noteData["picture"]."',
			author_id='".$noteData["author_id"]."',
			date=CURRENT_TIMESTAMP;";
		$sql .= "SELECT MAX(id) FROM notes;";
		
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

	public function ChangeNoteDescription($id, $description)
	{
		
		$query = "UPDATE `notes`
			SET `description` = '".$description."'
			WHERE `id`='".mysql_real_escape_string($id)."'
		";
		$result = $this->m_mysqli->query($query);
		
		
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

$notes = new CNotes($_COOKIE['id' ], $mysqli);

if($_GET["action"] == "addNote")
{
	
	$noteData["title"] = "Без названия";
	$noteData["description"] = "";
	$noteData["video"] = "";
	$noteData["picture"] = "";

	$noteId = $notes->AddNote($noteData);
	$note = $notes->GetNote($noteId);

	echo json_encode(array('title' => $note["title"], 'description' => $note["description"], 'date' => $note["date"], 'id' => $noteId));
}
if($_GET["action"] == "getNote")
{
	$note = $notes->GetNote($_GET["id"]);
	echo json_encode(array('title' => $note["title"], 'description' => $note["description"], 'date' => $note["date"]));
}

if($_GET["action"] == "ChangeNoteDescription")
{
	$description = $_POST["description"];
	$note = $notes->ChangeNoteDescription($_GET["id"], $description);
}

if($_GET["action"] == "getNotes")
{
	$page = $_GET["page"];
	$note = $notes->GetList($page);

	$result = array();
	for($i = 0; $i < mysql_num_rows($note);$i++)
	{
		$result[] = mysql_fetch_array($note, MYSQL_ASSOC );
	}
	
	echo json_encode($result);
}
if($_GET["action"] == "remove")
{
	$notes->Remove($_GET["id"]);
}
?>