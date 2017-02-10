<?php
include_once $_SERVER['DOCUMENT_ROOT'].'../config.php'; 
include_once $_SERVER['DOCUMENT_ROOT'].'../auth/check.php';

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
?>