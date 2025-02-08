<div {{ $attributes->merge(['class' => 'md:grid md:grid-cols-3 md:gap-6 dark:text-gray-300 claire:text-gray-700']) }}>
    <x-section-title>
        <x-slot name="title">
            <h2 class="dark:text-gray-100 claire:text-gray-900">{{ $title }}</h2>
        </x-slot>
        <x-slot name="description">
            <p class="dark:text-gray-300 claire:text-gray-700">{{ $description }}</p>
        </x-slot>
    </x-section-title>

    <div class="mt-5 md:mt-0 md:col-span-2">
        <div class="px-4 py-5 sm:p-6 dark:bg-gray-800 dark:text-gray-200 claire:bg-white claire:text-gray-900 shadow sm:rounded-lg">
            {{ $content }}
        </div>
    </div>
</div>
