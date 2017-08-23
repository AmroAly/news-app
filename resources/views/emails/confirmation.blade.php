<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Confirmation Email</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.1/css/materialize.min.css">

    </head>
    <style>
        nav {
            margin-bottom: 30px;
        }
    </style>
    <body>
        <div class="container">
            <nav>
                <div class="nav-wrapper">
                    <a href="#" class="brand-logo center">News App</a>
                </div>
            </nav>
            <h6>Thank you for Sign Up!</h6>

            <p>To Confirm your email address you need to click this <a href='{{ url("register/confirm/{$user->verfication_token}") }}'>link</a></p>
        </div>

    </body>
</html>
