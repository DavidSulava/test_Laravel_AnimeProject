<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>contact mail</title>
</head>
<body>
    <div>
        <h4>{{ $post['msg'] }}</h4>
        <br><br>
        <h3>{{ 'From: '.$post['mail'] }}</h3>
    </div>
</body>
</html>