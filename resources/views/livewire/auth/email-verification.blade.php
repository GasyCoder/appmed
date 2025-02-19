<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        @if(session('error'))
            <div class="mb-4 font-medium text-sm text-red-600">
                {{ session('error') }}
            </div>
        @endif

        <x-validation-errors class="mb-4" />

        <div>
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Vérification de l\'email') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                {{ __('Veuillez entrer votre adresse email universitaire pour commencer l\'inscription.') }}
            </p>

            <form method="POST" action="{{ route('email.verify') }}">
                @csrf

                <div class="mt-4">
                    <x-label for="email" value="{{ __('Email') }}" />
                    <x-input
                        id="email"
                        name="email"
                        class="block mt-1 w-full"
                        type="email"
                        value="{{ old('email') }}"
                        placeholder="exemple@facmed.mg"
                        required
                        autofocus
                    />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                        {{ __('Déjà inscrit?') }}
                    </a>
                    <x-button class="ms-4">
                        {{ __('Continuer') }}
                    </x-button>
                </div>
            </form>
        </div>
    </x-authentication-card>
</x-guest-layout>
