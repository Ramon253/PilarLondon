<html lang="es">
<head>
    <title>Código de Activación en Pilar London</title>
</head>
<body>
<div style="padding: 1rem; background: #464450; color: whitesmoke; border-radius: 0.5rem; text-align: center">
    <h1 style="color: white">De {{$mail['name']}} </h1>
    <h2 style="color: white">Email {{$mail['email']}} </h2>
    @if(isset($mail['phone']))
        <h2 style="color: white">Phone {{$mail['phone']}} </h2>
    @endif
    @if(isset($mail['subject']))
        <h2 style="color: white">Subject {{$mail['subject']}} </h2>
    @endif
    <p style="color: lightgrey">{{$mail['message']}}</p>
</div>
</body>
</html>
