<x-filament-panels::page>
    <div class="absolute inset-0 flex justify-center items-center">
        <div class="w-full bg-white px-6 py-12 shadow-sm ring-1 ring-gray-950/5 rounded-xl dark:bg-gray-900 dark:ring-white/10 sm:px-12 sm:max-w-lg grid auto-cols-fr gap-y-6">
            <header class="flex flex-col items-center">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                    {{ __('green::admin-auth.pages.change-password.heading') }}
                </h1>
            </header>
            <x-filament-panels::form wire:submit="changePassword">
                {{ $this->form }}
                <x-filament-panels::form.actions
                    :actions="$this->getFormActions()"
                    :full-width="$this->hasFullWidthFormActions()"
                />
            </x-filament-panels::form>
        </div>
    </div>
</x-filament-panels::page>
