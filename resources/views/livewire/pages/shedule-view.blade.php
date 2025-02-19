        {{-- Statistiques compactes --}}
        <div x-data="{ isOpen: false }">
            <!-- Bouton pour afficher/masquer -->
            <button
                @click="isOpen = !isOpen"
                class="flex items-center gap-2 mb-4 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">
                <svg
                    class="w-4 h-4 transition-transform"
                    :class="{ 'rotate-180': isOpen }"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
                <span class="font-medium">Statistiques de la semaine</span>
            </button>

            <!-- Contenu statistiques -->
            <div
                x-show="isOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform -translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-2"
                class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

                {{-- Total des cours --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-2">
                        <div class="rounded-lg p-2 bg-purple-50 dark:bg-purple-900/50">
                            <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">Total cours</p>
                            <p class="text-sm font-semibold text-purple-600 dark:text-purple-400">
                                {{ $calendarData['summary']['total_lessons'] }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Volume horaire --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-2">
                        <div class="rounded-lg p-2 bg-indigo-50 dark:bg-indigo-900/50">
                            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">Volume</p>
                            <p class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">
                                {{ $calendarData['summary']['total_hours'] }}h
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Répartition par type --}}
                @foreach($calendarData['summary']['lessons_by_type'] as $type => $count)
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-2">
                            <div class="rounded-lg p-2
                                {{ $type === 'CM' ? 'bg-blue-50 dark:bg-blue-900/50' :
                                ($type === 'TD' ? 'bg-green-50 dark:bg-green-900/50' :
                                    'bg-amber-50 dark:bg-amber-900/50') }}">
                                <span class="text-xs font-medium
                                    {{ $type === 'CM' ? 'text-blue-600 dark:text-blue-400' :
                                    ($type === 'TD' ? 'text-green-600 dark:text-green-400' :
                                        'text-amber-600 dark:text-amber-400') }}">
                                    {{ $type }}
                                </span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $typesCours[$type] ?? $type }}</p>
                                <p class="text-sm font-semibold
                                    {{ $type === 'CM' ? 'text-blue-600 dark:text-blue-400' :
                                    ($type === 'VC' ? 'text-green-600 dark:text-green-400' :
                                        'text-amber-600 dark:text-amber-400') }}">
                                    {{ $count }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Vue mobile du calendrier --}}
        @include('livewire.pages.vue-mobile')

        {{-- Vue desktop du calendrier --}}
        @include('livewire.pages.vue-desktop')

        {{-- Légende et informations complémentaires --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4">
            <div class="flex flex-col space-y-4">
                {{-- Légende des types de cours --}}
                <div class="flex flex-wrap gap-4 items-center justify-center sm:justify-start">
                    <span class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-blue-100 dark:bg-blue-900 mr-2"></span>
                        <span class="text-sm text-gray-600 dark:text-gray-300">CM (Cours Magistral)</span>
                    </span>
                    <span class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-green-100 dark:bg-green-900 mr-2"></span>
                        <span class="text-sm text-gray-600 dark:text-gray-300">VC (Visio Conférence)</span>
                    </span>
                </div>

                {{-- Informations de session --}}
                <div class="flex flex-wrap gap-4 items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>
                            Dernière mise à jour : {{ Carbon\Carbon::parse($currentDateTime)->locale('fr')->isoFormat('DD MMMM YYYY, HH:mm:ss') }}
                        </span>
                    </div>
                    <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span>Connecté en tant que : {{ auth()->user()->name }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bouton de rafraîchissement flottant --}}
        <div class="fixed bottom-4 right-4 flex flex-col space-y-2">
            <button wire:click="$refresh"
                    class="p-3 bg-purple-600 hover:bg-purple-700 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 group">
                <svg class="w-6 h-6 transform group-hover:rotate-180 transition-transform duration-500"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </button>
            <div class="absolute bottom-full right-0 mb-2 w-48 text-sm text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 p-2 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                Dernière mise à jour: {{ Carbon\Carbon::parse($currentDateTime)->diffForHumans() }}
            </div>
        </div>
