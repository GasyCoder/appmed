<x-form-section submit="updateProfileInformation">
    <x-slot name="title">
        <span class="text-gray-900 dark:text-gray-100">{{ __('Profile Information') }}</span>
    </x-slot>

    <x-slot name="description">
        <span class="text-gray-600 dark:text-gray-400">{{ __('Update your account\'s profile information and email address.') }}</span>
    </x-slot>

    <x-slot name="form">
        <!-- Profile Photo -->
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div x-data="{photoName: null, photoPreview: null}" class="col-span-6 sm:col-span-4">
                <!-- Profile Photo File Input -->
                <input type="file" id="photo" class="hidden"
                       wire:model.live="photo"
                       x-ref="photo"
                       x-on:change="
                           photoName = $refs.photo.files[0].name;
                           const reader = new FileReader();
                           reader.onload = (e) => {
                               photoPreview = e.target.result;
                           };
                           reader.readAsDataURL($refs.photo.files[0]);
                       " />

                <x-label for="photo" value="{{ __('Photo') }}" class="text-gray-700 dark:text-gray-300" />

                <!-- Current Profile Photo -->
                <div class="mt-2" x-show="! photoPreview">
                    <img src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->name }}" class="rounded-full size-20 object-cover ring-2 ring-gray-200 dark:ring-gray-700">
                </div>

                <!-- New Profile Photo Preview -->
                <div class="mt-2" x-show="photoPreview" style="display: none;">
                    <span class="block rounded-full size-20 bg-cover bg-no-repeat bg-center ring-2 ring-gray-200 dark:ring-gray-700"
                          x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                    </span>
                </div>

                <x-secondary-button
                    class="mt-2 me-2 dark:border-gray-600 dark:hover:border-gray-400 claire:border-gray-300 claire:hover:border-gray-500"
                    type="button"
                    x-on:click.prevent="$refs.photo.click()"
                    title="{{ __('Select A New Photo') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </x-secondary-button>
            
                @if ($this->user->profile_photo_path)
                    <x-secondary-button
                        type="button"
                        class="mt-2 dark:border-red-600 dark:hover:border-red-400 claire:border-red-300 claire:hover:border-red-500"
                        wire:click="deleteProfilePhoto"
                        title="{{ __('Remove Photo') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </x-secondary-button>
                @endif
                
                <x-input-error for="photo" class="mt-2" />
            </div>
        @endif

        <!-- Name -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="name" value="{{ __('Name') }}" class="text-gray-700 dark:text-gray-300" />
            <x-input id="name" type="text" class="mt-1 block w-full dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300" wire:model="state.name" required autocomplete="name" />
            <x-input-error for="name" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="email" value="{{ __('Email') }}" class="text-gray-700 dark:text-gray-300" />
            <x-input id="email" type="email" class="mt-1 block w-full dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300" wire:model="state.email" required autocomplete="username" />
            <x-input-error for="email" class="mt-2" />

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
                <p class="text-sm mt-2 text-gray-600 dark:text-gray-400">
                    {{ __('Your email address is unverified.') }}

                    <button type="button" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" wire:click.prevent="sendEmailVerification">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </p>

                @if ($this->verificationLinkSent)
                    <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </p>
                @endif
            @endif
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            <span class="text-green-600 dark:text-green-400">{{ __('Saved.') }}</span>
        </x-action-message>

        <x-button wire:loading.attr="disabled" wire:target="photo" class="dark:bg-indigo-600 dark:hover:bg-indigo-700 dark:focus:bg-indigo-700 dark:active:bg-indigo-800">
            {{ __('Save') }}
        </x-button>
    </x-slot>
</x-form-section>