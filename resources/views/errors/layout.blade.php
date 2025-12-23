<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Erreur' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white text-gray-900">
<main class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-lg">
        <div class="mb-6 text-center">
            <img src="{{ asset('assets/image/logo_med.png') }}" alt="Logo" class="mx-auto h-12 w-auto">
            <h1 class="mt-5 text-xl font-semibold tracking-tight">{{ $title ?? 'Erreur' }}</h1>
            @isset($subtitle)
                <p class="mt-2 text-sm text-gray-600">{{ $subtitle }}</p>
            @endisset
        </div>

        <div class="rounded-2xl bg-gray-50 p-4">
            <div class="flex items-start gap-3">
                <div class="mt-0.5 h-9 w-9 rounded-full bg-white flex items-center justify-center">
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

            <div class="mt-4 flex flex-col sm:flex-row gap-2">
                <button type="button"
                        onclick="window.location.reload()"
                        class="inline-flex justify-center items-center rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-gray-800 transition">
                    Recharger
                </button>

                <a href="{{ url()->previous() }}"
                   class="inline-flex justify-center items-center rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 ring-1 ring-gray-200 hover:bg-gray-100 transition">
                    Retour
                </a>

                @if (Route::has('login'))
                    <a href="{{ route('login') }}"
                       class="inline-flex justify-center items-center rounded-xl px-4 py-2.5 text-sm font-semibold text-gray-700 hover:text-gray-900 transition">
                        Connexion
                    </a>
                @endif
            </div>
        </div>

        <p class="mt-6 text-center text-xs text-gray-500">
            Code : {{ $code ?? '—' }} @if(app()->hasDebugModeEnabled()) · {{ now()->format('d/m/Y H:i') }} @endif
        </p>
    </div>
</main>
</body>
</html>
