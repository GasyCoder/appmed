{{-- Right top bar content --}}
@php
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Carbon;

    // ==========================================================
    // SAFE GUARDS
    // ==========================================================
    $isAuth = auth()->check();
    $user   = $isAuth ? auth()->user() : null;

    // Anti "hasRole() on null"
    $canShowNotifs = false;
    if ($isAuth && $user) {
        $canShowNotifs = method_exists($user, 'hasRole')
            ? $user->hasRole(['student', 'teacher', 'admin'])
            : true;
    }

    // ==========================================================
    // ANNONCES NON LUES (count + last items)
    // ==========================================================
    $annCount = 0;
    $annItems = collect();

    $hasTables =
        Schema::hasTable('announcements') &&
        Schema::hasTable('announcement_views');

    if ($canShowNotifs && $hasTables) {

        $roles = method_exists($user, 'getRoleNames')
            ? $user->getRoleNames()->values()->all()
            : [];

        $base = DB::table('announcements')
            ->where('is_active', 1)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->where(function ($q) use ($roles) {
                $q->whereNull('audience_roles');
                foreach ($roles as $r) {
                    $q->orWhereRaw("JSON_CONTAINS(audience_roles, JSON_QUOTE(?))", [$r]);
                }
            })
            ->whereNotExists(function ($q) use ($user) {
                $q->select(DB::raw(1))
                    ->from('announcement_views')
                    ->whereColumn('announcement_views.announcement_id', 'announcements.id')
                    ->where('announcement_views.user_id', $user->id);
            });

        $annCount = (int) (clone $base)->count();

        $annItems = (clone $base)
            ->select('id', 'title', 'created_at')
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();
    }

    $annIndexUrl = Route::has('announcements.index') ? route('announcements.index') : '#';

    $annLink = function ($id) use ($annIndexUrl) {
        return Route::has('announcements.show')
            ? route('announcements.show', $id)
            : $annIndexUrl . '?open=' . $id;
    };
@endphp

<div class="flex items-center gap-2">

    {{-- Dark mode --}}
    <button type="button"
            x-data
            @click="$store.theme.toggle()"
            class="inline-flex items-center justify-center h-10 w-10 rounded-full
                border border-gray-200 dark:border-gray-800
                bg-white dark:bg-gray-950
                text-gray-700 dark:text-gray-200
                hover:bg-gray-50 dark:hover:bg-gray-900
                shadow-sm
                focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/70"
            aria-label="Basculer le thème"
            title="Basculer le thème">

        <svg x-cloak x-show="$store.theme.darkMode" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <!-- sun -->
            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
        </svg>

        <svg x-cloak x-show="!$store.theme.darkMode" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <!-- moon -->
            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
        </svg>
    </button>



    {{-- ✅ ANNONCES (badge + dropdown) --}}
    @if($canShowNotifs)
        <div x-data="{ open: false }" class="relative">
            <button
                type="button"
                @click="open = !open"
                @keydown.escape.window="open = false"
                class="relative inline-flex items-center justify-center h-10 w-10 rounded-full
                        border border-gray-200 dark:border-gray-800
                        bg-white dark:bg-gray-950
                        text-gray-700 dark:text-gray-200
                        hover:bg-gray-50 dark:hover:bg-gray-900
                        shadow-sm
                        focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/70"
                aria-label="Annonces"
                title="Annonces"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>

                @if($annCount > 0)
                    <span class="absolute -top-1 -right-1 inline-flex items-center justify-center
                                 min-w-[1.25rem] h-5 px-1.5 rounded-full
                                 text-[11px] font-bold text-white bg-amber-500
                                 ring-2 ring-white dark:ring-gray-950">
                        {{ $annCount > 99 ? '99+' : $annCount }}
                    </span>
                @else
                    <span class="absolute top-2 right-2 block h-2 w-2 rounded-full bg-emerald-500"></span>
                @endif
            </button>

            <div
                x-cloak
                x-show="open"
                @click.away="open = false"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                class="absolute right-0 mt-2 w-96 max-w-[calc(100vw-2rem)] overflow-hidden rounded-xl
                       border border-gray-200 dark:border-gray-800
                       bg-white dark:bg-gray-950
                       shadow-lg"
            >
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-800">
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Annonces</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        @if($annCount > 0)
                            {{ $annCount }} annonce(s) non lue(s)
                        @else
                            Vous êtes à jour.
                        @endif
                    </p>
                </div>

                <div class="max-h-80 overflow-y-auto">
                    @if($annItems->isEmpty())
                        <div class="p-4">
                            <div class="rounded-lg border border-dashed border-gray-200 dark:border-gray-800 p-4 text-center">
                                <p class="text-sm text-gray-700 dark:text-gray-200">Aucune nouvelle annonce</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Rien à signaler.</p>
                            </div>
                        </div>
                    @else
                        <div class="divide-y divide-gray-200 dark:divide-gray-800">
                            @foreach($annItems as $a)
                                <a href="{{ $annLink($a->id) }}"
                                   class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-900/40 transition">
                                    <div class="flex items-start gap-3">
                                        <span class="mt-1 inline-block h-2.5 w-2.5 rounded-full bg-amber-500 shrink-0"></span>
                                        <div class="min-w-0">
                                            <div class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
                                                {{ $a->title ?? 'Annonce' }}
                                            </div>
                                            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                {{ Carbon::parse($a->created_at)->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-800">
                    <a href="{{ $annIndexUrl }}"
                       class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                        Voir toutes les annonces
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- ✅ Profile dropdown (SAFE auth) --}}
    @if($isAuth)
        <div x-data="{ open: false }" class="relative">
            <button
                type="button"
                @click="open = !open"
                @keydown.escape.window="open = false"
                class="inline-flex items-center gap-3 rounded-full pl-1 pr-2 py-1
                        hover:bg-gray-100 dark:hover:bg-gray-900
                        focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/70"
                aria-label="Menu du compte"
                title="Compte"
            >
                <div class="relative">
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos() && $user?->profile_photo_url)
                        <img class="w-9 h-9 rounded-full object-cover ring-2 ring-white dark:ring-gray-900"
                             src="{{ $user->profile_photo_url }}"
                             alt="{{ $user->name }}" />
                    @else
                        <div class="w-9 h-9 rounded-full bg-gray-100 dark:bg-gray-900
                                    ring-2 ring-white dark:ring-gray-900
                                    flex items-center justify-center">
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                {{ mb_substr($user?->name ?? 'U', 0, 1) }}
                            </span>
                        </div>
                    @endif

                    <span class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-gray-950"></span>
                </div>

                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div
                x-cloak
                x-show="open"
                @click.away="open = false"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                class="absolute right-0 mt-2 w-56 overflow-hidden rounded-xl
                        border border-gray-200 dark:border-gray-800
                        bg-white dark:bg-gray-950
                        shadow-lg"
            >
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-800">
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
                        {{ $user->name }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                        {{ $user->email }}
                    </p>
                </div>

                <div class="py-1">
                    <a href="{{ route('profile.show') }}"
                       class="flex items-center gap-2 px-4 py-2 text-sm
                              text-gray-700 dark:text-gray-200
                              hover:bg-gray-100 dark:hover:bg-gray-900">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Profil
                    </a>
                </div>

                <div class="border-t border-gray-200 dark:border-gray-800"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center gap-2 px-4 py-2 text-sm
                                   text-gray-700 dark:text-gray-200
                                   hover:bg-gray-100 dark:hover:bg-gray-900">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Déconnexion
                    </button>
                </form>
            </div>
        </div>
    @endif

</div>
