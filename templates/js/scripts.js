 $(document).ready(function() {
    var page = 0;
    var currentNoteId = $('.id').text();
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
                if(currentNoteId != id)
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
                        }
                    });
                }
            }

            $('.notes').on('click','.note', function(e)
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
                var size = 90;
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
                var title = prompt('Изменить название заметки', $(this).text());
                $(this).text(title); 
                $('.note[id=' + currentNoteId + ']').children('.noteTitle').text(title);
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