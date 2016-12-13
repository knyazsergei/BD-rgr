<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Valeo</title>

    <!-- Bootstrap -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/jquery.tag-editor.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
    
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="head">
        <div class="logo">
            Valeo
        </div>
    </div>
    <div class="main">
        <div class="leftColumn">
        <h1>Notes</h1>
        <div class="addNote" title="Добавить заметку">
            <img src="/img/add.png">
        </div>
        <div class="notes">
            <?
                $result = $notes->GetList();
                $used = false;
                $noteId = 0;
                $descriptionSize = 90;
                while ($note = mysql_fetch_assoc($result))
                {
                    if(!$used)
                    {
                        $noteId = $note['id'];
                        $used = true;
                    }
                    $description = $note['description'];
                    if(strlen($description) >= $descriptionSize)
                    {
                        $description = substr($description, 0, $descriptionSize);
                        $description = rtrim($description, "!,.-");
                        $description = substr($description, 0, strrpos($description, ' '));
                        $description = $description."… ";
                    }
                    echo '<div class="note" id="'.$note['id'].'">
                <div class="noteTitle">'.$note['title'].'</div>
                <div class="shortDescription">'.$description.'</div>
            </div>';
                }
            ?>
            <a href="#" id="getContent">Загрузить заметки</a>
        </div>
        </div>  
        <div class="rightColumn"><?$note = $notes->GetNote($noteId);?>
            <div class="currentNoteHead">
                <div style="padding: 5px;">
                    <div class="currentNoteTitle"><?=$note['title']?></div>
                    <div class="currentTools">
                        <a href="#" class="trashNote">
                            <img src="/img/trash.png" title="Удалить заметку">
                        </a>
                    </div> 
                    <div class="currentNoteDate"><?=$note['date']?></div>
                    <div class="id" style="display:none"><?=$note['id']?></div>
                </div>
                <div class="tages">
                    <input type="text" placeholder="Можно добавить метку прямо здесь" class="addTage">
                </div>
            </div>
            <textarea class="currentNoteDescritption" wrap="off" placeholder="Начни писать прямо здесь! :)"><?=$note['description']?></textarea>
        </div>
    </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="/js/jquery.textchange.min.js"></script>

    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.caret.min.js"></script>
    <script src="js/jquery.tag-editor.min.js"></script>
    <script src="js/scripts.js"></script>
  </body>
</html>