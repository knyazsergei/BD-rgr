<?php
include_once $_SERVER['DOCUMENT_ROOT'].'../config.php'; 
include_once $_SERVER['DOCUMENT_ROOT'].'../auth/check.php';

$ds          = DIRECTORY_SEPARATOR;  //1
 
$storeFolder = 'uploads';   //2
 
if (!empty($_FILES)) {
    

    $tempFile = $_FILES['file']['tmp_name'];          //3             
      
    $targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;  //4
     
   
     

    $image = file_get_contents( $tempFile );
      // Экранируем специальные символы в содержимом файла
    $image = mysql_escape_string( $image );
      // Формируем запрос на добавление файла в базу данных
    $query ="INSERT INTO `images` set `image` = '".$image."';";
    $query .= "SELECT MAX(id) FROM `images`;";
      // После чего остается только выполнить данный запрос к базе данных

    if(!$mysqli->multi_query($query))
	{
		$handler = fopen("error.txt", "w+");
		fwrite($handler, "Не удалось выполнить мультизапрос: (" . $mysqli->errno . ") " . $mysqli->error);
		fclose($handler);

	}

	$imageId = 0;
	do
	{
		if ($result = $mysqli->store_result()) 
		{
    	    $row = $result->fetch_row();
    	    $imageId = $row[0];
       		$result->free();
    	}
	}while($mysqli->next_result());
	

	$targetFile =  $targetPath. $imageId.'.'.pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

    move_uploaded_file($tempFile,$targetFile); //6
}


/*
if ( isset( $_GET['id'] ) ) {
  // Здесь $id номер изображения
  $id = (int)$_GET['id'];
  if ( $id > 0 ) {
    $query = "SELECT `image` FROM `images` WHERE `id`=".$id;
    // Выполняем запрос и получаем файл


	$result = $mysqli->query($query);
	$res =  $result->fetch_assoc();
    if ( !empty($res ) ) {
      $image = $res["image"];
      // Отсылаем браузеру заголовок, сообщающий о том, что сейчас будет передаваться файл изображения
     header("Content-type: image/*"); 
      // И  передаем сам файл 
      echo $image['content']; 
    }
  }
}*/
?>   