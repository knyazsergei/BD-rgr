<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Valeo</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

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
        <div class="addNote">
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
                <div class="currentNoteTitle">
                    <?=$note['title']?>
                </div>
                <div class="currentTools">
                    <a href="#" class="trashNote">
                        <img src="/img/trash.png">
                    </a>
                </div> 
                <div class="currentNoteDate">
                    <?=$note['date']?>
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
    <script type="text/javascript">
        $(document).ready(function() {

            var currentNoteId = <?=$noteId?>;
            var currentTitle = "<?=$note['title']?>";
            var page = 0;

            $('.addNote').on('click', function(e)
            {
                $(this).animate({opacity: 0.2}, 500 );
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: 'notes/index.php?action=addNote',
                    success: function(jsondata){
                        $('.notes').prepend('<div class="note" id="' + this.id + '"><div class="noteTitle">' + jsondata.title + '</div><div class="shortDescription">' +jsondata.description + '</div></div>');
                        $('.currentNoteTitle').text(jsondata.title);
                        $('.currentNoteDate').text(jsondata.date);
                        $('.currentNoteDescritption').val(jsondata.description);
                        $(this).animate({opacity: 1}, 500 );
                        currentNoteId = jsondata.id;
                    }
                });
            });

            function loadNote(id)
            {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: 'notes/index.php?action=getNote&id=' + id ,
                    success: function(jsondata){
                        $('.currentNoteTitle').text(jsondata.title);
                        $('.currentNoteDate').text(jsondata.date);
                        $('.currentNoteDescritption').val(jsondata.description);
                        currentNoteId = id;
                        currentTitle = jsondata.title;
                    }
                });
            }

            $('.notes').on('click','div', function(e)
            {
               Save(currentNoteId);
                var id = $(this).attr("id");
                loadNote(id);
            });

            $('.trashNote').on('click', function(e)
            {
                var accuratelyRemove = confirm("Вы уверены, что хотите удалить данную заметку?");
                if(accuratelyRemove)
                {
                    $('.note[id=' + currentNoteId + ']').remove();
                    $.ajax({
                        url: 'notes/index.php?action=remove&id=' + currentNoteId,
                        success: function(){
                            var id = $('.notes').children().first().attr("id");
                            loadNote(id);
                        }
                    });
                }
            });
            

            function removeGetContentButton(button)
            {
                var parent = $(button).parent();
                var obj = $(button);
                obj.detach();
                obj.appendTo(parent);
            }

            $('#getContent').on('click', function(e)
            {
                page++;
                $.ajax({
                    type: 'POST',
                    url: "/notes/index.php?action=getNotes&page=" + page,
                    cache: false,
                        dataType: 'json',
                    success: function(jsondata){               
                        $.each(jsondata, function(index, note) {
                             $('.notes').append('<div class="note" id="' + note.id + '"><div class="noteTitle">' + note.title + '</div><div class="shortDescription">' + note.description + '</div></div>');
                        });

                        setTimeout(removeGetContentButton, 10, '#getContent');

                    }
                });
                return false;
            });

            var timeout;
            $('.currentNoteDescritption').bind('textchange', function () {
                var newText = $('.currentNoteDescritption').val();
                var size = <?=$descriptionSize?>;
                if(newText  .length > size)
                {
                    newText = newText.slice(0, size) + ' ...';
                }
                $('.note[id=' + currentNoteId + '] .shortDescription').text(newText);

                clearTimeout(timeout);
                var self = this;
                timeout = setTimeout(Save, 1000, currentNoteId);
            });

            $('.currentNoteTitle').on('click', function(e)
            {
                var title = prompt('Изменить название заметки', currentTitle);
                currentTitle = title;
                $(this).text(title); 
                $('.note[id=' + currentNoteId + '] .currentNoteTitle').text(title);
                Save(currentNoteId);
            });

            function Save(id) {
                clearTimeout(timeout);
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    data:{description:$('.currentNoteDescritption').val(), title:$('.currentNoteTitle').text()},
                    url: 'notes/index.php?action=ChangeNote&id=' + id ,
                    success: function(jsondata){
                        $('.currentNoteTitle').text(jsondata.title);
                        $('.currentNoteDate').text(jsondata.date);
                        $('.currentNoteDescritption').val(jsondata.description);
                    }
                });
            }
        });
    </script>

    <style>
        html, body 
        {
            width:100%;
            height:100%;
        }
        .head 
        {
            background: #507299;
            width:100%;
            height:42px;
        }
        .logo 
        {
            color: #fff;
            font-size: 14pt;
            font-weight: bolder;
            padding: 8px;
            text-align: center;
        }
        .main 
        {
            width:100%;
            height:calc(100% - 42px);
            background: #F7F7F7;
        }
        .leftColumn
        {
            width:calc(25% - 1px);
            height:100%;
            float:left;
            border-right: solid 1px #e4e6e9;
        }
        .rightColumn 
        {
            width: 75%;
            height:100%;
            float:left;
        }
        .addNote 
        {
            float:right;
            margin:24px;
            cursor: pointer;
        }

        .notes
        {
            width:100%;
            height:calc(100% - 86px);
            clear:both;
            overflow-y: scroll;
        }

        .note 
        {
            width:100%;
            height:120px;
            background: #FFF;
            border-bottom: 1px solid #f8f8f8;
        }

        .note:hover 
        {
            background: #fafbfc;
            cursor: pointer;
        }

        .noteTitle
        {
            font-size: 12pt;
            color: #676666;
            padding:10px;
            text-overflow: ellipsis;
        }
        .shortDescription    
        {
            padding:10px;
            font-size:10pt;
            overflow: hidden;
        }


        textarea.currentNoteDescritption 
        {
            width:100%;
            height:calc( 100% - 35px );
            border:none;
            box-sizing:border-box;
            padding:25px;
            outline:none;

        }

        .currentNoteHead 
        {
            padding: 5px;
            width: 100%;
            box-sizing: border-box;
            height: 30px;
        }

        .currentNoteTitle
        {
            float:left;
        }

        .currentTools 
        {
            float:left;
            margin: 0 10px;
        }

        .currentNoteDate 
        {
            float:right;
        }

        .leftColumn h1
        {
            float:left;
        }

        #getContent
        {
            text-align: center;
            padding: 10px;
            margin: 0 auto;
            display: block;
            font-size: 12pt;
            font-weight: bold;
        }
  </body>
</html>