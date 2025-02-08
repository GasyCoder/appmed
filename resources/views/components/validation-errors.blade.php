@if ($errors->any())
    <div {{ $attributes->merge(['class' => 'p-4 mb-4 rounded-lg bg-red-50 border border-red-200']) }}>
        <div class="flex items-center space-x-2 mb-2">
            <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="font-medium text-red-700">{{ __('Whoops! Something went wrong.') }}</p>
        </div>

        <ul class="mt-3 list-inside text-sm text-red-600 space-y-1">
            @foreach ($errors->all() as $error)
                <li class="flex items-center space-x-2">
                    <svg class="h-3 w-3 text-red-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <circle cx="10" cy="10" r="3"/>
                    </svg>
                    <span>{{ $error }}</span>
                </li>
            @endforeach
        </ul>
    </div>
@endif