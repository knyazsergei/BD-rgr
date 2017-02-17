<?
include_once $_SERVER['DOCUMENT_ROOT'].'../config.php'; 
include_once $_SERVER['DOCUMENT_ROOT'].'../auth/check.php';
include_once $_SERVER['DOCUMENT_ROOT'].'../notes/tages.php';

class CAdminPanel
{
	function __construct($userId, $mysqli)
	{
		$this->m_userId = $userId;
		$this->m_mysqli = $mysqli;
	}



	public function GetNotes()
	{
		$notes = mysql_query("SELECT `users`.`login`, `notes`.`title`, `notes`.`id`, `notes`.`description` FROM `users` INNER JOIN `notes` on `users`.`id` = `notes`.`author_id`"
		) or die ("<br>Invalid query: " . mysql_error()); ;

		return $notes;
	}

	public function GetCountNotes()
	{
		$result = $this->m_mysqli->query("SELECT COUNT(id) FROM `notes` WHERE `author_id`='".mysql_real_escape_string($this->m_userId)."'");
		$row = $result->fetch_row();
		return $row[0];
	}

	public function SearchNotes($q1, $q2)
	{
		$notes = mysql_query("SELECT * FROM `notes` 
			WHERE `title` LIKE '%".mysql_real_escape_string($q1)."%' OR `description` LIKE '%".mysql_real_escape_string($q2)."%'"
		) or die ("<br>Invalid query: " . mysql_error()); ;

		return $notes;
	}
	

	private $m_userId = 0;
	private $m_numberNotesPage = 10;
	private $m_mysqli;
}

if($_COOKIE['id' ] == 16)
{
	$AdminPanel = new CAdminPanel($_COOKIE['id' ], $mysqli);
	
	$note = $AdminPanel->GetNotes($page);
	$result = array();
	for($i = 0; $i < mysql_num_rows($note);$i++)
	{
		$result[] = mysql_fetch_array($note, MYSQL_ASSOC );
	}

	?>
	<table border="1">
   <caption>Stats</caption>
   <tr>
   	<th>id</th>
    <th>login</th>
    <th>title</th>
    <th>description</th>
   </tr>
   <?
	foreach ($result as $key => $value) {
		echo "<tr><td>".$value["id"]."</td><td>".$value["login"]."</td><td>".$value["title"]."</td><td>".$value["description"]."</td></tr>";
	}
	?>
	</table>
	Всего записей: <?echo $AdminPanel->GetCountNotes();?>
	<br />
	<form method="POST">
		<input name="q1" type="search" placeholder="Enter title">
		<input name="q2" type="search" placeholder="Enter description">
		<input type="submit" value="Search">
	</form>
	<?
	if(!empty($_POST["q1"])||!empty($_POST["q2"]))
	{
		$q1 = $_POST["q1"];
		$q2 = $_POST["q2"];
		$searchResult = $AdminPanel->SearchNotes($q1, $q2);
		$sresult = array();
		for($i = 0; $i < mysql_num_rows($searchResult);$i++)
		{
			$sresult[] = mysql_fetch_array($searchResult, MYSQL_ASSOC );
		}
	
		echo "Запрос: ".$q1." ".$q2."<br />";
		?>
			<table border="1">
   			<caption>Stats</caption>
   			<tr>
   			 <th>title</th>
   			 <th>description</th>
   			</tr>
   		<?
		foreach ($sresult as $key => $value) 
		{
			echo "<tr><td>".$value["title"]."</td><td>".$value["description"]."</td></tr>";
		}
		?>
	</table>
	<?
	}
}
?>