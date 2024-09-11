@component('mail::message')
<h1>FKRATEK</h1>
<p>People who have finished the course:</p>

@component('mail::panel')
{{ $message }}
@endcomponent

@endcomponent
