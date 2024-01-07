<x-filament-panels::page.simple>
    @if (filament()->hasRegistration())
        <x-slot name="subheading">
            {{ __('filament-panels::pages/auth/login.actions.register.before') }}

            {{ $this->registerAction }}
        </x-slot>
    @endif

    {{ \Filament\Support\Facades\FilamentView::renderHook('panels::auth.login.form.before') }}

    <x-filament-panels::form wire:submit="login">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />

        @php($idProviderActions = $this->getIdProviderActions())
        @if($idProviderActions)
            <div class="my-2 flex items-center">
                <div class="flex-1 border-t border-neutral-300 dark:border-neutral-700"></div>
                <p class="px-4 text-sm text-center dark:text-white">
                    {{ __('green::admin-auth.pages.login.or') }}
                </p>
                <div class="flex-1 border-t border-neutral-300 dark:border-neutral-700"></div>
            </div>
            @foreach ($idProviderActions as $action)
                {{ $action }}
            @endforeach
        @endif

    </x-filament-panels::form>

    {{ \Filament\Support\Facades\FilamentView::renderHook('panels::auth.login.form.after') }}
</x-filament-panels::page.simple>
