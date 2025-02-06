<!-- resources/views/navigation/mobile-profile.blade.php -->
<div class="pt-4 pb-1 border-t border-gray-200">
    <div class="flex items-center px-4">
        <div class="flex-shrink-0">
            <img class="h-10 w-10 rounded-full" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
        </div>
        <div class="ml-3">
            <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
            <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
        </div>
    </div>

    <div class="mt-3 space-y-1">
        <x-responsive-nav-link href="{{ route('profile.show') }}">
            {{ __('Profile') }}
        </x-responsive-nav-link>

        <form method="POST" action="{{ route('logout') }}" x-data>
            @csrf
            <x-responsive-nav-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                {{ __('Déconnexion') }}
            </x-responsive-nav-link>
        </form>
    </div>
</div>
