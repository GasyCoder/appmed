<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="p-4 space-y-6 max-w-7xl mx-auto">

    {{-- En-tête compact --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-purple-500 to-indigo-600 dark:from-purple-700 dark:to-indigo-800 p-4">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                {{-- Informations principales --}}
                <div class="w-full sm:w-auto">
                    <div class="flex items-center gap-3 mb-2">
                        <h2 class="text-lg font-semibold text-white">Mon emploi du temps</h2>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <span class="w-1.5 h-1.5 mr-1 bg-green-500 rounded-full"></span>
                            En ligne
                        </span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 text-sm">
                        <span class="text-purple-100">
                            {{ auth()->user()->getFullNameWithGradeAttribute() }}
                        </span>
                        <span class="hidden sm:block text-purple-200/50">|</span>
                        <div class="flex items-center text-xs text-purple-200/90">
                            <span>{{ Carbon\Carbon::now()->locale('fr')->isoFormat('D MMM YYYY') }}</span>
                            <span class="mx-2 px-1.5 py-0.5 bg-white/10 rounded">
                                {{ Carbon\Carbon::now()->format('H:i') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Dernière connexion --}}
                <div class="text-right">
                    <span class="text-xs text-purple-200/70">
                        <select wire:model.live="selectedSemestre"
                                class="w-full sm:w-auto bg-white/10 border-0 text-white rounded-lg
                                    focus:ring-2 focus:ring-white/50 text-sm py-1.5 px-3
                                    hover:bg-white/20 transition-colors">
                            @foreach($semestres as $semestre)
                                <option value="{{ $semestre->id }}" class="text-gray-900">
                                    {{ $semestre->name }}
                                </option>
                            @endforeach
                        </select>
                    </span>
                </div>
            </div>
        </div>
    </div>

        @include('livewire.pages.shedule-view')
    </div>
</div>
@include('livewire.pages.scripts-styles')
