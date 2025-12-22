{{-- Vue mobile --}}
<div class="block lg:hidden">
    <div class="space-y-4">
        @foreach($weekDays as $dayNumber => $dayName)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 flex items-center justify-between border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ $dayName }}</h3>
                    @if($dayName === $calendarData['currentDay'])
                        <span class="text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 px-3 py-1 rounded-full">
                            Aujourd'hui
                        </span>
                    @endif
                </div>

                <div class="p-2 space-y-2">
                    @php
                        $dayLessons = collect($calendarData['calendar'])
                            ->map(function($slots) use($dayNumber) {
                                return collect($slots)->first(function($slot) use($dayNumber) {
                                    return isset($slot['weekday']) && $slot['weekday'] == $dayNumber && $slot['type'] === 'lesson';
                                });
                            })
                            ->filter()
                            ->sortBy('start_time');
                    @endphp

                    @if($dayLessons->isEmpty())
                        <div class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                            Aucun cours
                        </div>
                    @else
                        @foreach($dayLessons as $lesson)
                            <div class="rounded-lg overflow-hidden">
                                <div class="p-3 text-white" style="background-color: {{ $lesson['color'] }};">
                                    <!-- Horaire -->
                                    <div class="text-xs mb-2 font-medium opacity-90">
                                        {{ $lesson['start_time'] }} - {{ $lesson['end_time'] }}
                                    </div>
                                    
                                    <!-- UE et EC -->
                                    @if(isset($lesson['ue']))
                                        <div class="mb-2">
                                            <div class="text-sm font-bold leading-tight">
                                                {{ $lesson['ue']->code }} - {{ $lesson['ue']->name }}
                                            </div>
                                            @if(isset($lesson['ec']))
                                                <div class="text-xs opacity-90 leading-tight mt-1">
                                                    {{ $lesson['ec']->code }} - {{ $lesson['ec']->name }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    <!-- Informations enseignant/salle -->
                                    <div class="flex justify-between text-xs items-end mt-2">
                                        <div class="truncate opacity-90" title="{{ $lesson['teacher'] }}">
                                            {{ $lesson['teacher'] }}
                                        </div>
                                        <div class="font-bold flex-shrink-0 ml-2">
                                            {{ $lesson['salle'] }}
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
