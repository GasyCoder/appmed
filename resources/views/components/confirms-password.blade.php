@props(['title' => __('Confirm Password'), 'content' => __('For your security, please confirm your password to continue.'), 'button' => __('Confirm')])

@php
    $confirmableId = md5($attributes->wire('then'));
@endphp

<span
    {{ $attributes->wire('then') }}
    x-data
    x-ref="span"
    x-on:click="$wire.startConfirmingPassword('{{ $confirmableId }}')"
    x-on:password-confirmed.window="setTimeout(() => $event.detail.id === '{{ $confirmableId }}' && $refs.span.dispatchEvent(new CustomEvent('then', { bubbles: false })), 250);"
>
    {{ $slot }}
</span>

@once
<x-dialog-modal wire:model.live="confirmingPassword" class="dark:bg-gray-800 dark:text-gray-200 claire:bg-white claire:text-gray-900">
    <x-slot name="title">
        <h2 class="dark:text-gray-100 claire:text-gray-900">{{ $title }}</h2>
    </x-slot>

    <x-slot name="content">
        <p class="dark:text-gray-300 claire:text-gray-700">{{ $content }}</p>

        <div class="mt-4" x-data="{}" x-on:confirming-password.window="setTimeout(() => $refs.confirmable_password.focus(), 250)">
            <x-input type="password" class="mt-1 block w-3/4 dark:bg-gray-700 dark:text-gray-200 claire:bg-white claire:text-gray-900" placeholder="{{ __('Password') }}" autocomplete="current-password"
                        x-ref="confirmable_password"
                        wire:model="confirmablePassword"
                        wire:keydown.enter="confirmPassword" />

            <x-input-error for="confirmable_password" class="mt-2 dark:text-red-400 claire:text-red-600" />
        </div>
    </x-slot>

    <x-slot name="footer">
        <x-secondary-button wire:click="stopConfirmingPassword" wire:loading.attr="disabled" class="dark:border-gray-600 dark:hover:border-gray-400 claire:border-gray-300 claire:hover:border-gray-500">
            {{ __('Cancel') }}
        </x-secondary-button>

        <x-button class="ms-3 dark:bg-blue-600 dark:hover:bg-blue-700 claire:bg-blue-500 claire:hover:bg-blue-600" dusk="confirm-password-button" wire:click="confirmPassword" wire:loading.attr="disabled">
            {{ $button }}
        </x-button>
    </x-slot>
</x-dialog-modal>
@endonce
