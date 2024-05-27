@component('mail::message')

<h1>Hi {{$name}}</h1>
<p>This your otp for forget password</p>
<h1 style="text-align: center">{{$otp}}</h1>
<p>Note: Your OTP will expire in one minute</p>

<br>
Thanks,
{{config('app.name')}}
    
@endcomponent