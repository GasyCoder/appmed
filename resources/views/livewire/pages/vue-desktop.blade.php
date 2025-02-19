<div class="hidden lg:block bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-100 dark:border-gray-800 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            {{-- En-tête du tableau --}}
            <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 dark:text-gray-300 tracking-wider w-24 border-r border-gray-200 dark:border-gray-700">
                        Horaire
                    </th>
                    @foreach($weekDays as $dayName)
                        <th class="px-6 py-4 text-center font-medium tracking-wider relative group">
                            <span class="block text-sm text-gray-900 dark:text-white mb-1">{{ $dayName }}</span>
                            @if($dayName === $calendarData['currentDay'])
                                <span class="text-xs text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 px-3 py-1 rounded-full">
                                    Aujourd'hui
                                </span>
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>

            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                @foreach($calendarData['timeSlots'] as $slot)
                    <tr class="group/row hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-500 dark:text-gray-400 border-r border-gray-200 dark:border-gray-700 w-24">
                            {{ $slot['start'] }} - {{ $slot['end'] }}
                        </td>

                        @foreach($weekDays as $dayNumber => $dayName)
                            @php
                                $timeKey = $slot['start'] . ' - ' . $slot['end'];
                                $currentSlot = collect($calendarData['calendar'][$timeKey] ?? [])->first(function($s) use($dayNumber) {
                                    return isset($s['weekday']) && $s['weekday'] == $dayNumber;
                                });
                            @endphp

                            @if($currentSlot && $currentSlot['type'] === 'lesson')
                            <td class="p-2 relative" rowspan="{{ $currentSlot['rowspan'] }}">
                                <div class="h-full rounded-lg hover:shadow-lg transition-all duration-200 overflow-hidden group"
                                    style="background-color: {{ $currentSlot['color'] }}08; border-left: 4px solid {{ $currentSlot['color'] }}">

                                    <div class="p-3 h-full">
                                        <!-- En-tête du cours -->
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="flex items-center space-x-2">
                                                <span class="px-2 py-1 text-xs rounded-md font-medium"
                                                    style="background-color: {{ $currentSlot['color'] }}15; color: {{ $currentSlot['color'] }}">
                                                    {{ $currentSlot['type_cours_name'] }}
                                                </span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $currentSlot['start_time'] }} - {{ $currentSlot['end_time'] }}
                                                </span>
                                            </div>
                                            <span class="px-2.5 py-1 text-xs rounded-md font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                                                {{ $currentSlot['salle'] }}
                                            </span>
                                        </div>

                                        <!-- Informations du cours -->
                                        @if(isset($currentSlot['ue']))
                                            <div class="space-y-1.5 mb-3">
                                                <div class="font-medium text-gray-900 dark:text-white">
                                                    {{ $currentSlot['ue']->code }} - {{ $currentSlot['ue']->name }}
                                                </div>
                                                @if(isset($currentSlot['ec']))
                                                    <div class="text-sm text-gray-600 dark:text-gray-300">
                                                        {{ $currentSlot['ec']->code }} - {{ $currentSlot['ec']->name }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                        <!-- Niveau et Parcours -->
                                        <div class="mt-auto pt-2 border-t border-gray-100 dark:border-gray-800">
                                            <div class="flex items-center justify-between text-xs">
                                                <span class="font-medium text-gray-700 dark:text-gray-300">
                                                    {{ $currentSlot['niveau'] }}
                                                </span>
                                                <span class="text-gray-500 dark:text-gray-400">
                                                    {{ $currentSlot['parcour'] }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            @elseif(!$currentSlot || ($currentSlot['type'] === 'empty'))
                            <td class="p-2 border-r border-gray-100 dark:border-gray-800">
                                <div class="h-full min-h-[120px] rounded-lg border border-dashed border-gray-200 dark:border-gray-700">
                                </div>
                            </td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
