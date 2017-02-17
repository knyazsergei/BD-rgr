 $(document).ready(function()
{
    var timeout;
    var page = 0;
    var currentNoteId = 0;
    var currentNoteTitle = "";
    var currentNoteDescritption = "";
    var myDropZone;

    $(document).ready(InitApp());

    function InitApp()
    {
        //load note
        page--;
    
        GetNotes(function(){
            if($(".notes > div").children().length != 0)
            {
                currentNoteId = $(".notes div:first-child").parent().attr("id"); 
                var id = currentNoteId;
                currentNoteId = currentNoteId - 1;
                loadNote(id);
            }
        });

        //tages
        $('.addTage').tagEditor(
        { 
            initialTags: $('.addTage').val(),
            delimiter: ', ',
            placeholder: 'Добавить метки ...',
            forceLowercase: true,
            removeDuplicates: true,
            clickDelete: true,
            animateDelete: 0
        });   

        //init images 
        //image upload
        myDropZone = $("#images-box").dropzone({
            addRemoveLinks: true, 
            init: function() { 
                if(currentNoteId != 0)
                {
                    LoadImages(currentNoteId, this);
                }
            } 
        });
    }

    function loadNote(id)
    {
        if(currentNoteId != id)
        {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: 'notes/index.php?action=getNote&id=' + id ,
                success: function(jsondata)
                {
                    currentNoteTitle = jsondata.title;
                    currentNoteDescritption = jsondata.description;
                    $('.currentNoteTitle').text(jsondata.title);
                    $('.currentNoteDate').text(jsondata.date);
                    $('.currentNoteDescritption').val(jsondata.description);


                    //update tages
                    var tags = $('.addTage').tagEditor('getTags')[0].tags;
                    for (i = 0; i < tags.length; i++) { $('.addTage').tagEditor('removeTag', tags[i]); }

                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        data: {postId:id},
                        url: 'notes/tages.php?action=getTages',
                        success: function(tags)
                        {
                            for (i = 0; i < tags.length; i++) 
                            {
                                $('.addTage').tagEditor('addTag', tags[i]); 
                            }
                            currentNoteId = id;
                            LoadImages(currentNoteId, myDropZone);
                            console.log("note loaded: " + id);
                            return true;
                        }
                    });
                }
            });
        }
        return false;
    }

    $('.notes').on('click','.note', function(e)
    {
        Save(currentNoteId);
        var id = $(this).attr("id");
        console.log("choose: " + id);
        $('#id').text(id);
        loadNote(id);
    });

    $('.saveNote').on('click', function(e)
    {
        Save(currentNoteId);
    });

    $('.trashNote').on('click', function(e)
    {
        var accuratelyRemove = confirm("Вы уверены, что хотите удалить данную заметку?");
        if(accuratelyRemove)
        {
            loadCount();
            $('.note[id=' + currentNoteId + ']').remove();
            $.ajax({
                url: 'notes/index.php?action=remove&id=' + currentNoteId,
                success: function(){
                    var id = $('.notes').children().first().attr("id");
                    loadNote(id);
                    if($(".notes > div").children().length == 0)
                    {
                        $(".rightColumn").hide();
                    }
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

    function GetNotes(callback)
    {
        loadCount();
        page++;

        $.ajax({
            type: 'POST',
            url: "/notes/index.php?action=getNotes&page=" + page,
            dataType: 'json',
            success: function(jsondata)
            {
                console.log("Notes loaded");
                $.each(jsondata, function(index, note) 
                {
                    var description = note.description;
                    if(description.length>200) {
                        var description = description.substr(0,197)+'...';
                    }
                     $('.notes').append('<div class="note" id="' + note.id + '"><div class="noteTitle">' + note.title + '</div><div class="shortDescription">' + description + '</div></div>');
                });
                setTimeout(removeGetContentButton, 10, '#getContent');
                callback();
            }
        });
    }


    $('#getContent').on('click', function(e)
    {
       GetNotes();
    });

    $('.currentNoteTitle').on('click', function(e)
    {
        var title = prompt('Изменить название заметки', $(this).text());
        if(title != null)
        {
            $(this).text(title); 
            $('.note[id=' + currentNoteId + ']').children('.noteTitle').text(title);
            Save(currentNoteId);
        }
    });


    function Save(id) {
        if(currentNoteTitle != $('.currentNoteTitle').text() || currentNoteDescritption != $('.currentNoteDate').text())
        {
            console.log("Preparing to save: " + id);
            //save note
            var description = $('.currentNoteDescritption').val();
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data:{description:description, title:$('.currentNoteTitle').text(), id:id},
                url: 'notes/index.php?action=ChangeNote',
                success: function(jsondata)
                {
                    console.log("note saved: " + id + " " + jsondata);
                    SaveTages(id)
                }
            });
        }
    }


    function SaveTages(id)
    {
        //save tages
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: 'notes/tages.php?action=update',
            data:{tages:$('.addTage').val(), postId:id},
            success: function(jsondata)
            {
                return true;
            }
        });  
        return false;
    }
    
    function LoadImages(id, thisDropzone)
    {
        $.getJSON('/notes/getItemImages.php?noteId=' + id, function(data) 
        { // get the json response

                $.each(data, function(key,value)
                { //loop through it
                    var mockFile = { name: value.name, size: value.size }; // here we get the file name and size as response 
                    thisDropzone.options.addedfile.call(thisDropzone, mockFile);
                    thisDropzone.options.thumbnail.call(thisDropzone, mockFile, "notes/uploads/"+value.name);//uploadsfolder is the folder where you have all those uploaded files
                });
            });
    }

    function SetDirectSortingOrder()
    {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: 'profile/index.php?action=setDSortingOrder'
        });  
    }

    $('.addNote').on('click', function(e)
    {
        $(this).animate({opacity: 0.2}, 500 );
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: 'notes/index.php?action=addNote',
            success: function(jsondata)
            {
                $('.notes').prepend('<div class="note" id="' + this.id + '"><div class="noteTitle">' + jsondata.title + '</div><div class="shortDescription">' +jsondata.description + '</div></div>');
                $('.currentNoteTitle').text(jsondata.title);
                $('.currentNoteDate').text(jsondata.date);
                $('.currentNoteDescritption').val(jsondata.description);
                $(this).animate({opacity: 1}, 500 );
                currentNoteId = jsondata.id;
                $(".rightColumn").show();
            }
        });
        loadCount();

    });

    $('#reverse').click(function()
    {
        $(".notes").empty();
        page = -1;
        SetDirectSortingOrder();
        GetNotes();
        return false;
    });

/*
    $(".searchTerm").keyup(function() {
        $('#contenInput').text($(".searchTerm").val()); 
    });
*/
    $('.searchButton').click(function()
    {
        $(".notes").empty();
        page = -1;
        Search($(".searchTerm").val());
        return false;
    });

  function loadCount()
    {
        $.ajax({
                type: 'POST',
                dataType: 'json',
                url: 'notes/index.php?action=getCount',
                success: function(count)
                {
                    $("#count span").html(count);
                }
            });
    }

    function Search(quary)
    {
        console.log(quary);
        loadCount();
        page++;
        $.ajax({
            type: 'POST',
            url: "/notes/index.php?action=searchNotes&page=" + page,
            cache: false,
            dataType: 'json',
            data:{q:quary},
            success: function(jsondata)
            {        
                $.each(jsondata, function(index, note) 
                {
                     $('.notes').append('<div class="note" id="' + note.id + '"><div class="noteTitle">' + note.title + '</div><div class="shortDescription">' + note.description + '</div></div>');
                });
                setTimeout(removeGetContentButton, 10, '#getContent');
                return true;
            }
        });

        return false;
    }
    
});