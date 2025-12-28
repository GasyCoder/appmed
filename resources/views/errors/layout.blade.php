<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Erreur' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

@php
    $code = $code ?? 500;

    // Auth safe (évite de casser la page erreur si auth/session pose problème)
    $isAuth = false;
    try { $isAuth = auth()->check(); } catch (\Throwable $e) { $isAuth = false; }

    // URL "Accueil" intelligente
    $homeUrl = '/';
    if ($isAuth && \Illuminate\Support\Facades\Route::has('dashboard')) {
        $homeUrl = route('dashboard');
    } elseif (!$isAuth && \Illuminate\Support\Facades\Route::has('login')) {
        $homeUrl = route('login');
    }

    // Évite le bug: previous() = current() (ou vide) => boucle infinie sur la même page erreur
    $currentUrl  = url()->current();
    $previousUrl = url()->previous();
    $backUrl     = ($previousUrl && $previousUrl !== $currentUrl) ? $previousUrl : $homeUrl;

    // Bouton recharger (utile surtout pour 419)
    $showReload = $showReload ?? ($code === 419);
@endphp

<body class="min-h-screen bg-white text-gray-900">
<main class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-lg">
        <div class="mb-6 text-center">
            <img src="{{ asset('assets/image/logo.png') }}" alt="Logo" class="mx-auto h-12 w-auto">
            <h1 class="mt-5 text-xl font-semibold tracking-tight">{{ $title ?? 'Erreur' }}</h1>

            @isset($subtitle)
                <p class="mt-2 text-sm text-gray-600">{{ $subtitle }}</p>
            @endisset
        </div>

        <div class="rounded-2xl bg-gray-50 p-4">
            <div class="flex items-start gap-3">
                <div class="mt-0.5 h-9 w-9 rounded-full bg-white flex items-center justify-center ring-1 ring-gray-200">
                    {!! $icon ?? '' !!}
                </div>

                <div class="flex-1">
                    @isset($message)
                        <div class="text-sm text-gray-700">{{ $message }}</div>
                    @endisset

                    @isset($hint)
                        <div class="mt-2 text-sm text-gray-600">{{ $hint }}</div>
                    @endisset
                </div>
            </div>

            <div class="mt-4 flex flex-col sm:flex-row flex-wrap gap-2">
                {{-- Accueil --}}
                <a href="{{ $homeUrl }}"
                   class="inline-flex justify-center items-center rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-gray-800 transition">
                    Accueil
                </a>

                {{-- Retour (avec fallback pour éviter boucle) --}}
                <a href="{{ $backUrl }}"
                   class="inline-flex justify-center items-center rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 ring-1 ring-gray-200 hover:bg-gray-100 transition">
                    Retour
                </a>

                {{-- Recharger (utile surtout pour 419) --}}
                @if($showReload)
                    <button type="button"
                            onclick="window.location.reload()"
                            class="inline-flex justify-center items-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition">
                        Recharger
                    </button>
                @endif

                {{-- Connexion / Déconnexion --}}
                @if($isAuth && \Illuminate\Support\Facades\Route::has('logout'))
                    <form method="POST" action="{{ route('logout') }}" class="w-full sm:w-auto">
                        @csrf
                        <button type="submit"
                                class="w-full inline-flex justify-center items-center rounded-xl px-4 py-2.5 text-sm font-semibold text-gray-700 hover:text-gray-900 ring-1 ring-gray-200 hover:bg-gray-100 transition">
                            Déconnexion
                        </button>
                    </form>
                @elseif(!$isAuth && \Illuminate\Support\Facades\Route::has('login'))
                    <a href="{{ route('login') }}"
                       class="inline-flex justify-center items-center rounded-xl px-4 py-2.5 text-sm font-semibold text-gray-700 hover:text-gray-900 transition">
                        Connexion
                    </a>
                @endif
            </div>
        </div>

        <p class="mt-6 text-center text-xs text-gray-500">
            Code : {{ $code ?? '—' }}
            @if(config('app.debug'))
                · {{ now()->format('d/m/Y H:i') }}
            @endif
        </p>
    </div>
</main>
</body>
</html>
