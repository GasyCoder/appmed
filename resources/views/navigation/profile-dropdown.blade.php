<!-- resources/views/navigation/profile-dropdown.blade.php -->
<div class="hidden sm:flex sm:items-center sm:ml-6">
    <x-dropdown align="right" width="48">
        <x-slot name="trigger">
            <button class="flex text-sm border-2 font-bold border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                <img class="h-10 w-10 rounded-full font-bold object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
            </button>
        </x-slot>

        <x-slot name="content">
            <div class="block px-4 py-2 text-xs text-gray-400">
                {{ __('Gestion du compte') }}
            </div>

            <x-dropdown-link href="{{ route('profile.show') }}">
                {{ __('Profile') }}
            </x-dropdown-link>

            <div class="border-t border-gray-200"></div>

            <form method="POST" action="{{ route('logout') }}" x-data>
                @csrf
                <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                    {{ __('Déconnexion') }}
                </x-dropdown-link>
            </form>
        </x-slot>
    </x-dropdown>
</div>
