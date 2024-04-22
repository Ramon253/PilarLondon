<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title></title>
</head>
<body>
<form action="/api/post" method="post" enctype="multipart/form-data">
    @csrf
    <input type="text" name="name">
    <input type="text" name="subject">
    <input type="text" name="description">
    <input type="number" name="group_id">

    <input type="file" name="files[]">
    <input type="file" name="files[]">
    <input type="file" name="files[]">

    <button type="submit">
        Button
    </button>
</form>
</body>
</html>
