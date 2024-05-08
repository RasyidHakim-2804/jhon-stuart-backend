<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>OTP foget Password</title>
</head>
<body>
    <h1>Hi {{$name}}</h1>
    <p>This your otp for forget password</p>
    <h3>{{$otp}}</h3>
    <p>This otp will be expired untill: {{$expired_at}}</p>
    <p>want to resend it? <a href="{{$resend}}">resend</a> </p>
</body>
</html>