{{-- livewire.admin.modal.calendar-grid --}}
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-20">
                        Horaire
                    </th>
                    @foreach($weekDays as $day)
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ $day }}
                        </th>
                    @endforeach
                    <th></th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($calendarData['timeSlots'] as $slot)

                    <tr>
                        {{-- Horaire --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 border-r border-gray-200 dark:border-gray-700">
                            {{ $slot['start'] }} - {{ $slot['end'] }}
                        </td>

                        {{-- Créneaux --}}
                        @foreach($weekDays as $dayNumber => $dayName)
                            @php
                                $timeKey = $slot['start'] . ' - ' . $slot['end'];
                                $currentSlot = collect($calendarData['calendar'][$timeKey] ?? [])->first(function($s) use($dayNumber) {
                                    return isset($s['weekday']) && $s['weekday'] == $dayNumber;
                                });
                            @endphp

                            @if($currentSlot && $currentSlot['type'] === 'lesson')
                            <td class="p-1 md:p-2" rowspan="{{ $currentSlot['rowspan'] }}">
                                <div class="relative rounded-lg shadow-sm border overflow-hidden transition-all"
                                    style="background-color: {{ $currentSlot['color'] ?? '#ffffff' }}10; border-color: {{ $currentSlot['color'] ?? '#e5e7eb' }}">
                                    <!-- Contenu principal -->
                                    <div class="p-2 md:p-3 space-y-2">
                                        <!-- En-tête avec les actions -->
                                        <div class="flex items-center justify-between">
                                            <!-- Horaire avec la couleur -->
                                            <div class="text-xs font-medium" style="color: {{ $currentSlot['color'] ?? '#6B7280' }}">
                                                {{ $currentSlot['start_time'] }} - {{ $currentSlot['end_time'] }}
                                            </div>

                                            <!-- Actions -->
                                            <div class="flex items-center space-x-1">
                                                <button wire:click="editLesson({{ $currentSlot['id'] }})"
                                                        class="p-1 text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                    </svg>
                                                </button>
                                                <button wire:click="confirmDelete({{ $currentSlot['id'] }})"
                                                        class="p-1 text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- UE et EC avec couleur de fond -->
                                        @if(isset($currentSlot['ue']))
                                            <div class="rounded-md p-2"
                                                style="background-color: {{ $currentSlot['color'] ?? '#f3f4f6' }}15">
                                                <div class="font-medium text-sm truncate"
                                                    style="color: {{ $currentSlot['color'] ?? '#111827' }}">
                                                    {{ $currentSlot['ue']->code }} - {{ $currentSlot['ue']->name }}
                                                </div>
                                                @if(isset($currentSlot['ec']))
                                                    <div class="text-xs truncate mt-0.5"
                                                        style="color: {{ $currentSlot['color'] ?? '#6B7280' }}">
                                                        {{ $currentSlot['ec']->code }} - {{ $currentSlot['ec']->name }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                        <!-- Informations complémentaires -->
                                        <div class="flex flex-col space-y-1.5">
                                            <!-- Enseignant -->
                                            <div class="flex items-center text-xs text-gray-600 dark:text-gray-400">
                                                <svg class="w-3.5 h-3.5 mr-1.5 flex-shrink-0"
                                                    style="color: {{ $currentSlot['color'] ?? '#6B7280' }}"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                                <span class="truncate">{{ $currentSlot['teacher'] }}</span>
                                            </div>

                                            <!-- Salle avec icône -->
                                            <div class="flex items-center text-xs text-gray-600 dark:text-gray-400">
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
                            @elseif(!$currentSlot || ($currentSlot['type'] === 'empty' && $currentSlot['available']))
                                <td class="p-2">
                                    <div wire:click="$set('showCreateModal', true)"
                                         class="h-full min-h-[100px] flex items-center justify-center border-2 border-dashed
                                                border-gray-200 dark:border-gray-700 rounded-lg hover:border-indigo-500
                                                dark:hover:border-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20
                                                transition-all cursor-pointer group">
                                        <span class="text-sm text-gray-400 dark:text-gray-500 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                                            Disponible
                                        </span>
                                    </div>
                                </td>
                            @else
                                <td class="p-2"></td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Légende --}}
    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
        <div class="flex items-center space-x-4 text-sm">
            <span class="flex items-center">
                <span class="w-3 h-3 rounded-full bg-blue-100 dark:bg-blue-900 mr-2"></span>
                <span class="text-gray-600 dark:text-gray-300">Cours Magistral (CM)</span>
            </span>
            <span class="flex items-center">
                <span class="w-3 h-3 rounded-full bg-green-100 dark:bg-green-900 mr-2"></span>
                <span class="text-gray-600 dark:text-gray-300">Visio Conférence (VC)</span>
            </span>
        </div>
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
