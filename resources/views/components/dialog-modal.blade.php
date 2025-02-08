@props(['id' => null, 'maxWidth' => null])

<x-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
    <div class="px-6 py-4 dark:text-gray-100 claire:text-gray-900">
        <div class="text-lg font-medium dark:text-gray-200 claire:text-gray-800">
            {{ $title }}
        </div>

        <div class="mt-4 text-sm dark:text-gray-400 claire:text-gray-600">
            {{ $content }}
        </div>
    </div>

    <div class="flex flex-row justify-end px-6 py-4 dark:bg-gray-800 claire:bg-gray-100 dark:text-gray-200 claire:text-gray-800">
        {{ $footer }}
    </div>
</x-modal>
