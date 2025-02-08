<button {{ $attributes->merge([
    'type' => 'button',
    'class' => 'inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700 claire:bg-gray-100 claire:border-gray-400 claire:text-gray-800 claire:hover:bg-gray-200',
    'data-timestamp' => '2025-02-08 18:09:53',
    'data-user' => 'gasikaradigital'
]) }}>
    <div class="flex items-center space-x-2">
        <time class="text-xs text-gray-500 dark:text-gray-400" datetime="2025-02-08 18:09:53">
            {{ $slot }}
        </time>
        @if(isset($user))
            <span class="text-xs text-gray-500 dark:text-gray-400">
                {{ $user }}
            </span>
        @endif
    </div>
</button>