@props(['lesson'])

<div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
    <div class="flex-1">
        <div class="flex items-center justify-between">
            <span class="font-medium text-gray-900 dark:text-white">
                {{ $lesson->type_cours }}
                <span class="text-xs text-gray-500">
                    ({{ $lesson->getTypeCoursNameAttribute() }})
                </span>
            </span>
            <span class="text-sm text-gray-500 dark:text-gray-400">
                {{ $lesson->start_time->format('H:i') }} - {{ $lesson->end_time->format('H:i') }}
            </span>
        </div>
        <div class="mt-1 text-sm text-gray-600 dark:text-gray-300">
            {{ optional($lesson->teacher)->getFullNameWithGradeAttribute() ?? 'Enseignant non assigné' }}
        </div>
        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            Salle: {{ $lesson->salle }}
        </div>
    </div>
</div>
