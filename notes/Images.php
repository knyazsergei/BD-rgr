<?php
include_once $_SERVER['DOCUMENT_ROOT'].'../config.php'; 
include_once $_SERVER['DOCUMENT_ROOT'].'../auth/check.php';


if($_GET["action"] == "getImage")
{

$noteId = $_GET['noteId'];
    $sql = "
	   SELECT * FROM `images` 
	   WHERE `noteId`='".$noteId."'
    ";

    if ($result = $mysqli->query($sql)) 
    {
        while ($row = $result->fetch_assoc()) 
        {
    	   $obj['name'] = $row["image"];
    	   $obj['size'] = filesize("uploads/".$row["image"]);
    	   $res[] = $obj;
        }

        header('Content-Type: application/json');
        echo json_encode($res);
    }
}

if($_GET["action"] == "removeImage")
{
    $userId = $_COOKIE['id'];
    $name = $_POST["name"];

    function GetNote($id, $mysqli)
    {
        $sql = "SELECT * FROM `notes` WHERE `id`='".mysql_real_escape_string($id)."'";
        $result = $mysqli->query($sql);
        return $result->fetch_assoc();
    }

    function GetImage($name, $mysqli)
    {
        $sql = "SELECT * FROM `images` 
            WHERE `image`='".mysql_real_escape_string($name)."'";
        $result = $mysqli->query($sql);
        return $result->fetch_assoc();
    }

    function Remove($name, $mysqli)
    {
        $sql = "
            DELETE FROM `images` 
            WHERE `image`='".$name."'";
        $result = $mysqli->query($sql);
        unlink("uploads/".$name);//remove from disk
    }

    function isOwn($mysqli,$userId, $name)
    {
        $image = GetImage($name, $mysqli);
        $note = GetNote($image["noteId"], $mysqli);
        

        if($note["author_id"] == $userId)
        {
            Remove($name, $mysqli);
        }
    }
    isOwn($mysqli, $userId, $name);
}
?>