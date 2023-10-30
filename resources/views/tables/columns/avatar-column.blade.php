@php
    $avatar = $getAvatar();
@endphp
<div class="fi-ta-text gap-y-1 px-3 py-4">
    <div class="flex gap-1.5">
        @if($avatar)
            <div class="w-9">
                <x-filament::avatar
                        :src="$avatar"
                        :attributes="
                    \Filament\Support\prepare_inherited_attributes($attributes)
                        ->class(['fi-user-avatar rounded-full w-9'])
                "/>
            </div>
        @endif
        <div class="inline-flex items-center text-sm text-gray-950 dark:text-white">
            {{ $getState() }}
        </div>
    </div>
</div>
