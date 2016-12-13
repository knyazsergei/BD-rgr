$(function(){
    $('#register').submit(function()
    {
        $.ajax(
        {
            type: 'POST',
            dataType: 'json',
            url: '/auth/register.php',
            data: $(this).serialize(),
            success: function(jsondata)
            {
                if(jsondata.result)
                {
                    window.location = "/";
                }
                else
                {
                    alert(jsondata.text);
                }
            }
        });
        return false;
    });

    $('#loginForm').submit(function()
    {
       $.ajax(
        {
            type: 'POST',
            dataType: 'json',
            url: '/auth/login.php',
            data: $(this).serialize(),
            success: function(jsondata)
            {
                if(jsondata.result)
                {
                    window.location = "/";
                }
                else
                {
                    alert(jsondata.text);
                }
            }
        });
        return false;
    });
});
