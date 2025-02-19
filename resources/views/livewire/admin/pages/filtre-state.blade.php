{{-- livewire.admin.pages.filtre --}}
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
    {{-- En-tête avec niveau et semestre --}}
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 dark:from-indigo-800 dark:to-indigo-900 p-6 text-white">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="space-y-4 md:space-y-2">
                <h2 class="text-2xl font-bold">
                    @if($selectedNiveau)
                        {{ optional($niveaux->find($selectedNiveau))->name }}
                        @if($semestres->count() > 0)
                            <span class="text-sm ml-2 bg-white/20 px-2 py-1 rounded-full">
                                {{ $semestres->pluck('name')->join(', ') }}
                            </span>
                        @endif
                    @else
                        Emploi du temps
                    @endif
                </h2>
                <div class="flex gap-4">
                    <select wire:model.live="selectedNiveau"
                            class="bg-white/10 border-0 text-gray-400 dark:text-gray-700 rounded-lg focus:ring-2 focus:ring-white">
                        <option value="">Sélectionner un niveau</option>
                        @foreach($niveaux as $niveau)
                            <option value="{{ $niveau->id }}">{{ $niveau->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <button wire:click="$set('showCreateModal', true)"
                    class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 bg-white text-indigo-600 rounded-lg
                           hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2
                           focus:ring-offset-indigo-600 transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouveau cours
            </button>
        </div>
    </div>

    {{-- Filtres secondaires --}}
    <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 dark:bg-gray-900">
        <div>
            <select wire:model.live="selectedParcour"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white
                           focus:ring-indigo-500 dark:focus:ring-indigo-400"
                    @if(!$selectedNiveau) disabled @endif>
                <option value="">Tous les parcours</option>
                @foreach($parcours as $parcour)
                    <option value="{{ $parcour->id }}">{{ $parcour->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <select wire:model.live="selectedTeacher"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white
                           focus:ring-indigo-500 dark:focus:ring-indigo-400">
                <option value="">Tous les enseignants</option>
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}">{{ $teacher->getFullNameWithGradeAttribute() }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
{{-- Statistiques Section --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    {{-- Total Stats Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-3 sm:p-6 transform hover:scale-102 transition-transform duration-300">
        <div class="flex items-center space-x-3 sm:space-x-4">
            <div class="rounded-full p-2 sm:p-3 bg-indigo-100 dark:bg-indigo-900">
                <svg class="w-4 h-4 sm:w-6 sm:h-6 text-indigo-600 dark:text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <h3 class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-300">Total des cours</h3>
                <p class="text-lg sm:text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                    {{ $calendarData['summary']['total_lessons'] }}
                </p>
            </div>
        </div>
    </div>

    {{-- Hours Stats Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-3 sm:p-6 transform hover:scale-102 transition-transform duration-300">
        <div class="flex items-center space-x-3 sm:space-x-4">
            <div class="rounded-full p-2 sm:p-3 bg-emerald-100 dark:bg-emerald-900">
                <svg class="w-4 h-4 sm:w-6 sm:h-6 text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-300">Volume horaire</h3>
                <p class="text-lg sm:text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                    {{ $calendarData['summary']['total_hours'] }}h
                </p>
            </div>
        </div>
    </div>

    {{-- Distribution Stats Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-3 sm:p-6">
        <h3 class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-300 mb-3 sm:mb-4">Répartition</h3>
        <div class="space-y-2 sm:space-y-3">
            @foreach($calendarData['summary']['lessons_by_type'] as $type => $count)
                <div class="flex items-center justify-between">
                    <span class="text-xs sm:text-sm text-gray-600 dark:text-gray-300">{{ $type }}</span>
                    <span class="px-2 sm:px-3 py-1 text-xs sm:text-sm font-medium rounded-full
                        {{ $type === 'CM' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' :
                           ($type === 'TD' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                            'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200') }}">
                        {{ $count }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</div>
