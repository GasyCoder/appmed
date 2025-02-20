{{-- livewire.admin.modal.calendar-grid --}}
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
    <!-- En-tête avec navigation -->
    <div class="py-3 px-4 flex items-center justify-between border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-medium text-gray-800 dark:text-white">Emploi du temps</h2>
        <div class="flex items-center gap-2">
            <button class="p-1 rounded text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <span class="text-sm text-gray-600 dark:text-gray-300">Semaine du 20 Feb au 26 Feb 2025</span>
            <button class="p-1 rounded text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Grille du calendrier -->
    <div class="overflow-x-auto bg-gray-50 dark:bg-gray-900">
        <table class="w-full border-collapse">
            <thead>
                <tr>
                    <th class="bg-gray-50 dark:bg-gray-800 border-b border-r border-gray-200 dark:border-gray-700 sticky left-0 z-10 w-20 p-3">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">HORAIRE</span>
                    </th>
                    @foreach($weekDays as $day)
                        <th class="py-3 text-center border-b border-gray-200 dark:border-gray-700">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $day }}</span>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($calendarData['timeSlots'] as $slot)
                    <tr class="@if(strtotime($slot['start']) == strtotime('12:00')) border-t-2 border-gray-300 dark:border-gray-600 @endif">
                        <!-- Horaire (colonne fixe) -->
                        <td class="py-3 px-3 text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 border-r border-b border-gray-200 dark:border-gray-700 sticky left-0 z-10 min-w-[80px]">
                            {{ $slot['start'] }}
                        </td>

                        <!-- Créneaux pour chaque jour -->
                        @foreach($weekDays as $dayNumber => $dayName)
                            @php
                                $timeKey = $slot['start'] . ' - ' . $slot['end'];
                                $currentSlot = collect($calendarData['calendar'][$timeKey] ?? [])->first(function($s) use($dayNumber) {
                                    return isset($s['weekday']) && $s['weekday'] == $dayNumber;
                                });

                                // Si ce créneau contient un cours
                                $hasCourse = $currentSlot && $currentSlot['type'] === 'lesson';

                                // Si ce créneau est déjà couvert par un cours s'étalant sur plusieurs plages
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
                                            $currentEndTime = strtotime($slot['end']);

                                            if ($currentStartTime >= $otherStartTime && $currentStartTime < $otherEndTime) {
                                                $isOccupiedByMultiSlotCourse = true;
                                                break;
                                            }
                                        }
                                    }
                                    if ($isOccupiedByMultiSlotCourse) break;
                                }
                            @endphp

                            @if($hasCourse)
                                <td class="p-1 border-b border-r border-gray-100 dark:border-gray-800 @if($dayNumber == date('N')) bg-blue-50/30 dark:bg-blue-900/10 @endif"
                                    @if(isset($currentSlot['rowspan']) && $currentSlot['rowspan'] > 1) rowspan="{{ $currentSlot['rowspan'] }}" @endif>
                                    <div class="relative h-full rounded overflow-hidden border-l-4 bg-white dark:bg-gray-800 hover:shadow-md transition-shadow cursor-pointer group min-h-[90px]"
                                        style="border-left-color: {{ $currentSlot['color'] ?? '#e5e7eb' }}; background-color: {{ $currentSlot['color'] ?? '#ffffff' }}05;">
                                        <div class="p-2.5 h-full flex flex-col">
                                            <!-- En-tête avec horaire et actions -->
                                            <div class="flex items-center justify-between mb-1.5">
                                                <span class="text-xs font-medium" style="color: {{ $currentSlot['color'] ?? '#6B7280' }}">
                                                    {{ $currentSlot['start_time'] }} - {{ $currentSlot['end_time'] }}
                                                </span>

                                                <!-- Actions (visibles au survol) -->
                                                <div class="hidden group-hover:flex space-x-1">
                                                    <button wire:click="editLesson({{ $currentSlot['id'] }})"
                                                            class="p-1 text-gray-400 hover:text-blue-500 dark:hover:text-blue-400">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                        </svg>
                                                    </button>
                                                    <button wire:click="confirmDelete({{ $currentSlot['id'] }})"
                                                            class="p-1 text-gray-400 hover:text-red-500 dark:hover:text-red-400">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Contenu du cours -->
                                            <div class="space-y-1">
                                                @if(isset($currentSlot['ue']))
                                                    <div class="text-xs font-semibold line-clamp-2" style="color: {{ $currentSlot['color'] ?? '#111827' }}">
                                                        {{ $currentSlot['ue']->code }} - {{ $currentSlot['ue']->name }}
                                                    </div>
                                                    @if(isset($currentSlot['ec']))
                                                        <div class="text-xs line-clamp-1 mt-0.5" style="color: {{ $currentSlot['color'] ?? '#6B7280' }}">
                                                            {{ $currentSlot['ec']->code }} - {{ $currentSlot['ec']->name }}
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>

                                            <!-- Informations enseignant/salle -->
                                            <div class="mt-auto pt-1.5 text-xs text-gray-500 dark:text-gray-400">
                                                <div class="flex items-center" title="{{ $currentSlot['teacher'] }}">
                                                    <svg class="w-3.5 h-3.5 mr-1.5 flex-shrink-0"
                                                        style="color: {{ $currentSlot['color'] ?? '#6B7280' }}"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                    </svg>
                                                    <span class="truncate">{{ $currentSlot['teacher'] }}</span>
                                                </div>
                                                <div class="flex items-center mt-1">
                                                    <svg class="w-3.5 h-3.5 mr-1.5 flex-shrink-0"
                                                        style="color: {{ $currentSlot['color'] ?? '#6B7280' }}"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                    </svg>
                                                    <span class="font-medium">{{ $currentSlot['salle'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            @elseif($isOccupiedByMultiSlotCourse)
                                <!-- Cette cellule est déjà couverte par un cours multi-horaire -->
                            @else
                                <td class="border-b border-r border-gray-100 dark:border-gray-800 @if($dayNumber == date('N')) bg-blue-50/30 dark:bg-blue-900/10 @endif h-[60px]">
                                    <div class="h-full flex items-center justify-center">
                                        <div wire:click="$set('showCreateModal', true)"
                                            class="h-12 w-[90%] flex items-center justify-center border border-dashed
                                                border-gray-200 dark:border-gray-700 hover:border-indigo-500
                                                dark:hover:border-indigo-400 hover:bg-indigo-50/30 dark:hover:bg-indigo-900/20
                                                transition-all cursor-pointer rounded-md group">
                                            <span class="text-xs text-gray-400 dark:text-gray-500 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                                                Disponible
                                            </span>
                                        </div>
                                    </div>
                                </td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Légende -->
    <div class="p-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-750 flex flex-wrap gap-3">
        <span class="flex items-center">
            <span class="w-3 h-3 rounded-sm bg-blue-500 mr-1.5"></span>
            <span class="text-xs text-gray-600 dark:text-gray-300">Cours Magistral</span>
        </span>
        <span class="flex items-center">
            <span class="w-3 h-3 rounded-sm bg-green-500 mr-1.5"></span>
            <span class="text-xs text-gray-600 dark:text-gray-300">Travaux Dirigés</span>
        </span>
        <span class="flex items-center">
            <span class="w-3 h-3 rounded-sm bg-purple-500 mr-1.5"></span>
            <span class="text-xs text-gray-600 dark:text-gray-300">Travaux Pratiques</span>
        </span>
        <span class="flex items-center">
            <span class="w-3 h-3 rounded-sm bg-orange-500 mr-1.5"></span>
            <span class="text-xs text-gray-600 dark:text-gray-300">Visio Conférence</span>
        </span>
    </div>
</div>

{{-- Modal de confirmation de suppression --}}
@if($showDeleteModal)
<div class="fixed inset-0 overflow-y-auto z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-4 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity"></div>

        <div class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                            Confirmer la suppression
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Êtes-vous sûr de vouloir supprimer ce cours ? Cette action ne peut pas être annulée.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" wire:click="deleteLesson"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Supprimer
                </button>
                <button type="button" wire:click="$set('showDeleteModal', false)"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Annuler
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- CSS nécessaire pour la grille  -->
<style>
.grid-cols-time-slots {
    grid-template-columns: 80px repeat(6, 1fr);
}

/* Limiter le nombre de lignes */
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

@media (max-width: 768px) {
    .grid-cols-time-slots {
        grid-template-columns: 60px repeat(6, minmax(120px, 1fr));
    }
}
</style>
