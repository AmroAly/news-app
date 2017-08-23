<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>{{ $post->title }}</title>
    </head>
    <style>
        body, html {
            background: #ccc;
            padding: 0;
            margin: 0;
            font-family: sans-serif;
        }
        .news {
            margin: 100px;
            width: 100%;
            text-align: center;
        }
        h1 {
            background: #fff;
        }
        img {
            max-width: 500px;
        }
    </style>
    <body>
        <div class="container">
            <div class="news">
            <img src="{{ base_path('public/images/'. $post->image)}}" alt="">
            <p>Reporter: {{ $post->user->name }} at {{ $post->created_at }}</p>
            <h1>{{ $post->title }}</h1>
            <p>{{ $post->body }}</p>
        </div>
        </div>
    </body>
</html>
