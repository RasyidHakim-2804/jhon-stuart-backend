@component('mail::message')

<h1>Hi Admin</h1>
<p>You got an new contact message. Click the "Contact Page" button to forward you to contact page on admin panel. Thankyou. </p>
<br>
@include('vendor.mail.html.button', [
    'url' => config('app.frontend_url') . 'admins/contacts/',
    'slot' => 'Contact Page'
])
Thanks,
{{config('app.name')}}
    
@endcomponent