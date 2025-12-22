{{-- vue pour desktop (version optimisée avec full color) --}}
<div class="hidden lg:block bg-white dark:bg-gray-900 rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full">
            {{-- En-tête du tableau --}}
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400 tracking-wider w-20 border-r border-gray-200 dark:border-gray-700">
                        Horaire
                    </th>
                    @foreach($weekDays as $dayNumber => $dayName)
                        <th class="px-3 py-2.5 text-center tracking-wider border-l">
                            <span class="block text-xs font-medium text-gray-900 dark:text-white">{{ $dayName }}</span>
                            @if($dayName === $calendarData['currentDay'])
                                <div class="w-2 h-2 rounded-full bg-blue-500 mx-auto"></div>
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>

            <tbody>
                @foreach($calendarData['timeSlots'] as $slot)
                    <tr class="@if(strtotime($slot['start']) == strtotime('12:00')) border-t border-gray-300 dark:border-gray-600 @endif">
                        <td class="px-4 py-1 whitespace-nowrap text-xs font-medium text-gray-500 dark:text-gray-400 border-r border-gray-200 dark:border-gray-700 w-20">
                            {{ $slot['start'] }}
                        </td>

                        @foreach($weekDays as $dayNumber => $dayName)
                            @php
                                $timeKey = $slot['start'] . ' - ' . $slot['end'];
                                $currentSlot = collect($calendarData['calendar'][$timeKey] ?? [])->first(function($s) use($dayNumber) {
                                    return isset($s['weekday']) && $s['weekday'] == $dayNumber;
                                });

                                // Vérifier si ce créneau est occupé par un cours qui s'étend sur plusieurs plages
                                $isOccupiedByMultiSlotCourse = false;
                                foreach($calendarData['calendar'] as $otherKey => $otherSlots) {
                                    if ($otherKey === $timeKey) continue;
                                    foreach($otherSlots as $otherSlot) {
                                        if (isset($otherSlot['weekday']) && $otherSlot['weekday'] == $dayNumber
                                            && $otherSlot['type'] === 'lesson' && isset($otherSlot['rowspan'])
                                            && $otherSlot['rowspan'] > 1) {
                                            $otherStartTime = strtotime($otherSlot['start_time']);
                                            $otherEndTime = strtotime($otherSlot['end_time']);
                                            $currentStartTime = strtotime($slot['start']);

                                            if ($currentStartTime > $otherStartTime && $currentStartTime < $otherEndTime) {
                                                $isOccupiedByMultiSlotCourse = true;
                                                break;
                                            }
                                        }
                                    }
                                    if ($isOccupiedByMultiSlotCourse) break;
                                }
                            @endphp

                            @if($currentSlot && $currentSlot['type'] === 'lesson')
                            <td class="p-1 align-top" @if(isset($currentSlot['rowspan']) && $currentSlot['rowspan'] > 1) rowspan="{{ $currentSlot['rowspan'] }}" @endif>
                                <div class="rounded h-full p-2 text-white"
                                    style="background-color: {{ $currentSlot['color'] }};">
                                    <div class="h-full flex flex-col min-h-[60px]">
                                        <!-- En-tête du cours -->
                                        <div class="flex items-start justify-between">
                                            <div class="flex items-center space-x-2 mb-2">
                                                <span class="text-xs text-gray-50 dark:text-gray-50">
                                                    {{ $currentSlot['start_time'] }} - {{ $currentSlot['end_time'] }}
                                                </span>
                                            </div>
                                        </div>
                                        <!-- UE et EC -->
                                        @if(isset($currentSlot['ue']))
                                            <div>
                                                <div class="text-xs font-bold leading-tight">
                                                    {{ $currentSlot['ue']->code }} - {{ $currentSlot['ue']->name }}
                                                </div>
                                                @if(isset($currentSlot['ec']))
                                                    <div class="text-[10px] opacity-90 leading-tight mt-0.5">
                                                        {{ $currentSlot['ec']->code }} - {{ $currentSlot['ec']->name }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                        <!-- Infos prof/salle -->
                                        <div class="mt-2 flex justify-between text-[10px] items-end">
                                            <div class="truncate opacity-90" title="{{ $currentSlot['teacher'] }}">
                                                {{ $currentSlot['teacher'] }}
                                            </div>
                                            <div class="font-bold flex-shrink-0 ml-1">
                                                {{ $currentSlot['salle'] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            @elseif($isOccupiedByMultiSlotCourse)
                                <!-- Cette cellule est déjà couverte par un cours multi-horaire -->
                            @else
                                <td class="px-1 py-0.5 min-h-[60px]">
                                </td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
