@component('mail::message')

<h1>Hi {{$name}}</h1>
<p>Your account registration almost done. Please click verify button to verify your email. </p>
<br>
@include('vendor.mail.html.button', [
    'url'  => $url,
    'slot' => 'Verify'
])
Thanks,
{{config('app.name')}}
    
@endcomponent