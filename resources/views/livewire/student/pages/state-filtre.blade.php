{{-- En-tête compact --}}
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-indigo-500 to-indigo-700 dark:from-indigo-700 dark:to-indigo-900 p-4">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
            {{-- Info Section --}}
            <div class="w-full sm:w-auto">
                <div class="flex items-center gap-2 text-white">
                    <h2 class="text-lg font-semibold">Mon emploi du temps</h2>
                    <span class="text-white/40">|</span>
                    <span class="text-sm text-white/90">{{ $calendarData['currentNiveau'] }}</span>
                    @if($calendarData['currentParcour'])
                        <span class="text-white/40">|</span>
                        <span class="text-sm text-white/90">{{ $calendarData['currentParcour'] }}</span>
                    @endif
                </div>
                <p class="text-xs text-white/70 mt-1">
                    {{ Carbon\Carbon::parse($currentDateTime)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                </p>
            </div>

            {{-- Sélecteur de semestre --}}
            <div class="w-full sm:w-auto">
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
            </div>
        </div>
    </div>
</div>
