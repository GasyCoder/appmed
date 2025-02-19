<div class="block lg:hidden">
    <div class="space-y-4">
        @foreach($weekDays as $dayNumber => $dayName)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 flex items-center justify-between">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ $dayName }}</h3>
                    @if($dayName === $calendarData['currentDay'])
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            Aujourd'hui
                        </span>
                    @endif
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @php
                            $dayLessons = collect($calendarData['calendar'])
                                ->map(function($slots) use($dayNumber) {
                                    return collect($slots)->first(function($slot) use($dayNumber) {
                                        return isset($slot['weekday']) && $slot['weekday'] == $dayNumber && $slot['type'] === 'lesson';
                                    });
                                })
                                ->filter();
                        @endphp

                        @if($dayLessons->isEmpty())
                            <div class="p-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                Aucun cours
                            </div>
                        @else
                        @foreach($dayLessons as $lesson)
                            <div class="p-4" style="background-color: {{ $lesson['color'] }}05;">
                                <div class="flex flex-col space-y-3">
                                    <!-- En-tête -->
                                    <div class="flex justify-between items-start">
                                        <div class="flex flex-col">
                                            <span class="text-sm" style="color: {{ $lesson['color'] }}">
                                                ({{ $lesson['type_cours_name'] }})
                                            </span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $lesson['start_time'] }} - {{ $lesson['end_time'] }}
                                            </span>
                                        </div>
                                        <span class="text-xs px-2 py-1 rounded-full"
                                            style="background-color: {{ $lesson['color'] }}20; color: {{ $lesson['color'] }}">
                                            {{ $lesson['salle'] }}
                                        </span>
                                    </div>

                                    <!-- UE et EC -->
                                    @if(isset($lesson['ue']))
                                        <div class="rounded-md p-2" style="background-color: {{ $lesson['color'] }}10">
                                            <div class="font-medium text-sm truncate" style="color: {{ $lesson['color'] }}">
                                                {{ $lesson['ue']->code }} - {{ $lesson['ue']->name }}
                                            </div>
                                            @if(isset($lesson['ec']))
                                                <div class="text-xs truncate mt-1" style="color: {{ $lesson['color'] }}90">
                                                    {{ $lesson['ec']->code }} - {{ $lesson['ec']->name }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    <!-- Informations complémentaires -->
                                    <div class="mt-auto pt-2 border-t border-gray-100 dark:border-gray-800">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="font-medium text-gray-700 dark:text-gray-300">
                                                {{ $lesson['niveau'] }}
                                            </span>
                                            <span class="text-gray-500 dark:text-gray-400">
                                                {{ $lesson['parcour'] }}
                                            </span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
