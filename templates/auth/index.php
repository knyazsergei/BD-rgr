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
    <link href="/css/style.css" rel="stylesheet">
    <link href="/css/auth/style.css" rel="stylesheet">
    
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
    <div class="main container">
        <div class="card card-container">
        <h2 class='login_title text-center'>Авторизация</h2>
        <hr>
            <form class="form-signin" id="loginForm" method="post" action="/auth/login.php">
                <span id="reauth-email" class="reauth-email"></span>
                <p class="input_title">Логин</p>
                <input type="text" id="inputEmail" name="login" class="login_box" placeholder="" required autofocus>
                <p class="input_title">Пароль</p>
                <input type="password" name="password" class="login_box inputPassword" placeholder="******" required>
                <div id="remember" class="checkbox">
                    <label>
                        <a href="/auth/index.php?action=register">Регистрация</a>
                    </label>
                    <label>
                        <a href="#">Забыли пароль?</a>
                    </label>
                </div>
                <button class="btn btn-lg btn-primary" type="submit">Войти</button>
            </form><!-- /form -->
        </div><!-- /card-container -->
    </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="/js/jquery.textchange.min.js"></script>

    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/auth/scripts.js"></script>
  </body>
</html>