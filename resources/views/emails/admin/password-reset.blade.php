<?php // @formatter:off ?>
<x-mail::message>

# {{__('green::admin_base.emails.password_reset.subject', ['app' => config('app.name')])}}

@if($email){{__('green::admin_base.emails.password_reset.email')}}: {!! $email !!}@endif


@if($username){{__('green::admin_base.emails.password_reset.username')}}: {!! $username !!}@endif


{{__('green::admin_base.emails.password_reset.password')}}: {!! $password !!}


<x-mail::button :url="$login">
{{__('green::admin_base.emails.password_reset.login', ['app' => config('app.name')])}}
</x-mail::button>
</x-mail::message>