<?php // @formatter:off ?>
<x-mail::message>

# {{__('green::admin-auth.emails.password-reset.subject', ['app' => config('app.name')])}}

@if($email){{__('green::admin-auth.emails.password-reset.email')}}: {!! $email !!}@endif


@if($username){{__('green::admin-auth.emails.password-reset.username')}}: {!! $username !!}@endif


{{__('green::admin-auth.emails.password-reset.password')}}: {!! $password !!}


<x-mail::button :url="$login">
{{__('green::admin-auth.emails.password-reset.login', ['app' => config('app.name')])}}
</x-mail::button>
</x-mail::message>
