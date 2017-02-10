<?php
include_once $_SERVER['DOCUMENT_ROOT'].'../config.php'; 
include_once $_SERVER['DOCUMENT_ROOT'].'../auth/check.php';

$target_dir = "uploads/";
$targetFileName = uniqid() . basename($_FILES["file"]["name"]);
$target_file = $target_dir . $targetFileName;

$isImage = false;

if(@is_array(getimagesize($mediapath)))
{
    $isImage = true;
} 
else 
{
    $isImage = false;
}

if (isImage && move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) 
{
    $sql = "INSERT INTO images SET image='".$targetFileName."', noteId='".$_GET["noteId"]."'"; 
    $mysqli->query($sql);
    $result = "The file ". $targetFileName. " has been uploaded.";
} else {
    $result = "Sorry, there was an error uploading your file.";
}

file_put_contents("asdasdsas.txt", $result);
?>`