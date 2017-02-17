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
        <div class="head-items">
        </div>
        <div class="logo head-items">
            Valeo
        </div>
        <div class="profile head-items">
        <ul class="menu">
            <li><a href=#><?=$userName?><img src="<?=$avatar?>" /></a>
                <ul class="submenu">
                    <li><a href=#>Помощь</a></li>
                    <li><a href=#>Настройки</a></li>
                    <li><a href=/auth/logout.php>Выход</a></li>
                </ul>
            </li>
        </ul>
        </div>
    </div>
    <div class="main">
        <div class="leftColumn">
        <h1>Notes</h1>
        <div class="addNote" title="Добавить заметку">
            <img src="/img/add.png" />
        </div>
        <form method="POST" class="search">
            <input name="q" type="search" placeholder="Serach line..." class="searchTerm" />
            <input type="submit" value="S" class="searchButton" />
        </form>
        <div class="notes">
        <?if($notes->GetCount() > 5):?>
            <a href="#" id="getContent">Загрузить заметки</a>
        <?endif?>
        </div>
        <footer><div id="count">Count:<span>0</span></div><a href="#" id="reverse"><img src="/img/reverse.svg"></a></footer>
        </div>  
        <div class="rightColumn">
            <?$note = $notes->GetNote($notes->LastId());?>
            <div class="currentNoteHead">
                <div style="padding: 5px;">
                    <div class="currentNoteTitle"><?=$note['title']?></div>
                    <div class="currentTools">
                        <a href="#" class="trashNote">
                            <img src="/img/trash.png" title="Удалить заметку">
                        </a>
                        <a href="#" class="saveNote">
                            <img src="/img/save.svg" title="Сохранить заметку">
                        </a>
                    </div> 
                    <div class="currentNoteDate"><?=$note['date']?></div>
                    <div id="id" style="display:none"><?=$note['id']?></div>
                </div>
                <div class="tages">
                    <input type="text" placeholder="Можно добавить метку прямо здесь" class="addTage" text="<?
                        $arrTages = $tages->GetTages($note['id']);
                        foreach ($arrTages as $word) {
                            echo $word.',';
                        }
                    ?>">
                </div>
            </div>
            <textarea class="currentNoteDescritption" wrap="soft" placeholder="Начни писать прямо здесь! :)"><?/*=$note['description']*/?></textarea>
            <div class="images">
                    <form action="/notes/uploadImage.php?noteId=<?=$note['id']?>" class="dropzone" id="images-box">
                        <div class="fallback">
                            <input name="file" type="file" multiple />
                        </div>
                    </form>
            </div>
        </div>
    </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="/js/jquery.textchange.min.js"></script>

    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.caret.min.js"></script>
    <script src="js/jquery.tag-editor.min.js"></script>
    <script src="/js/dropzone.js"></script>
    <script src="js/scripts.js"></script>
    
  </body>
</html>