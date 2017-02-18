 $(document).ready(function()
{
    var timeout;
    var page = 0;
    var currentNoteId = 0;
    var currentNoteTitle = "";
    var currentNoteDescritption = "";
    var currentNoteTages = "";
    var myDropZone;
    var imageCleaner;

    var searchUsed = false;

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
                LoadImages(currentNoteId, this);
                
                //init images
                $("div.images").dropzone({ 
                    url: "/notes/uploadImage.php?noteId=" + id, 
                    maxFilesize: 5,
                    maxFile: 10,
                    addRemoveLinks: true,
                    autoDiscover: false,
                    dictResponseError: 'Server not Configured',
                    acceptedFiles: ".png,.jpg,.gif,.bmp,.jpeg",
                    previewsContainer: ".previewImage",
                    dictDefaultMessage: "drop here your image",
                    init:function()
                    {
                        myDropZone = this;
                        myDropZone.options.dictRemoveFile = "x";

                        var imageCleaner2 = function () {
                            myDropZone.removeAllFiles();
                        };
                        imageCleaner = imageCleaner2;
                    },
                    removedfile: function(file) 
                    {
                        var name = file.name;
                         console.log("remove name");
                        $.ajax({
                            type: 'POST',
                            url: 'notes/Images.php?action=removeImage',
                            dataType: 'json',
                            data:{name:name}
                        });
                        var _ref;
                        return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;        

                    }
                });
                /*
                myDropZone = $("#imageZone").dropzone({
                    autoDiscover: false,
                    init: function(){
                        $.getJSON(url, function(data) 
                        {
                            $.each(data, function(index, val) 
                            {
                                var mockFile = { name: val.name, size: val.size };
                                dz.options.addedfile.call(dz, mockFile);
                                dz.options.thumbnail.call(dz, mockFile, "/notes/uploads/" + val.name);
                            });
                        });
                        //LoadImages(currentNoteId, this);
                    }
                });
                */
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

        document.onkeydown = function(e) 
        {
            if ((e.ctrlKey && e.keyCode == 'S'.charCodeAt(0)) && $(".currentNoteDescritption").is( ":focus" )) 
            {
                Save(currentNoteId);
                return false;
            }
        }

        $( ".currentNoteDescritption" ).blur(function() {
            Save(currentNoteId);
        });

        window.onbeforeunload = function (event) 
        {
            Save(currentNoteId);
        }

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
                            currentNoteTages = tags;
                            currentNoteId = id;
                            LoadImages(currentNoteId, myDropZone);
                            console.log("note loaded: " + id);
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
        return false;
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
        /*
        modal({
                type: 'prompt',
                title: 'Изменение названия заметки',
                text: 'Введите новое название:',
                callback: function(title) {
                    if(title != null)
                    {
                        $(this).text(title); 
                        $('.note[id=' + currentNoteId + ']').children('.noteTitle').text(title);
                        Save(currentNoteId);
                    }
                }
            });
        */
        var title = prompt('Изменить название заметки', $(this).text());
        if(title != null)
                    {
                        $(this).text(title); 
                        $('.note[id=' + currentNoteId + ']').children('.noteTitle').text(title);
                        Save(currentNoteId);
                    }
       
    });


    function Save(id) {
        var title = $('.currentNoteTitle').text();
        var description = $('.currentNoteDescritption').val();

        if(currentNoteTitle != title || currentNoteDescritption != description)
        {
            console.log("Preparing to save: " + id);
            //save note
                $.ajax({
                type: 'POST',
                dataType: 'json',
                data:{description:description, title:$('.currentNoteTitle').text(), id:id},
                url: 'notes/index.php?action=ChangeNote',
                success: function(jsondata)
                {
                    console.log(jsondata.date);
                    currentNoteTitle = title;
                    currentNoteDescritption = description;
                    if(currentNoteId == id)
                    {
                        $(".currentNoteDate").text(jsondata.date);
                    }
                    console.log("note saved: " + id + " " + jsondata.date);
                }
            });
        }
        SaveTages(id);
    }


    function SaveTages(id)
    {
        //save tages
        var tages = $('.addTage').val();
        if(currentNoteTages != tages)
        {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: 'notes/tages.php?action=update',
                data:{tages: tages, postId:id},
                success: function(jsondata)
                {
                    console.log("tages saved");
                },
                complete:function(jsondata)
                {
                    console.log(jsondata);            
                }
            });  
        }
    }
    
    function LoadImages(id, thisDropzone)
    {
        var url = '/notes/Images.php?action=getImage&noteId=' + id;
        console.log(url);

        $(".dz-preview").remove();

        $.getJSON(url, function(data) 
        {
            $.each(data, function(index, val) 
            {
                var mockFile = { name: val.name, size: val.size, status: 'success'  };
                var filePath = "/notes/uploads/" + val.name;
                thisDropzone.emit("addedfile", mockFile);
                thisDropzone.emit("thumbnail", mockFile, filePath); 
                thisDropzone.files.push( mockFile ); 
            });
        });
    }

    function SetDirectSortingOrder()
    {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: 'profile/index.php?action=setDSortingOrder',
            complete:function()
            {
                if(searchUsed)
                {
                    Search($(".searchTerm").val());
                }
                else
                {
                    GetNotes();
                }
            }
        });  
    }

    function AddNote()
    {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: 'notes/index.php?action=addNote',
            success: function(jsondata)
            {
                console.log("added: " + jsondata.id);
                $('.notes').prepend('<div class="note" id="' + jsondata.id + '"><div class="noteTitle">' + jsondata.title + '</div><div class="shortDescription">' +jsondata.description + '</div></div>');
                loadNote(jsondata.id);
            }
        });
        loadCount();
    }

    $('.addNote').on('click', function(e)
    {
        AddNote();
    });



    $('#reverse').click(function()
    {
        $(".notes").empty();
        page = -1;
        SetDirectSortingOrder();
        return false;
    });

/*
    $(".searchTerm").keyup(function() {
        $('#contenInput').text($(".searchTerm").val()); 
    });
*/
    $('.searchButton').click(function()
    {
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
        $(".notes").empty();
        page = -1;
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
                searchUsed = true;
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


    $('.previewImage').on('click','.dz-image', function(e)
    {
        url = $(this).children("img").attr("src");

        modal({
            type: 'primary',
            title: 'Image',
            text: '<img src="' + url + '" class="modalImage"/ >',
        });
    });

        $('a#helpWindow').click(function() {
            modal({
                type: 'primary',
                title: 'Help',
                text: 'To download a photo, transfer photos from your computer to the bottom panel of the site.',
            });
        });

        $('a#settingsWindow').click(function() {
            modal({
                type: 'alert',
                title: 'Error',
                text: 'Section of the website is under construction',
            });
        });


});