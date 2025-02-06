<!-- Header Section -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
    <!-- Top Section with Gradient -->
    <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-blue-50/50 to-indigo-50/50">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <!-- Left side - Logo & Info -->
            <div class="flex items-center gap-5">
                <div class="p-3 bg-white rounded-xl shadow-md">
                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                        Documents
                    </h2>
                    <div class="flex items-center gap-3 mt-2">
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-gradient-to-r from-blue-500/10 to-blue-500/20 text-blue-700 border border-blue-200">
                            {{ auth()->user()->niveau->name }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-gradient-to-r from-indigo-500/10 to-indigo-500/20 text-indigo-700 border border-indigo-200">
                            {{ auth()->user()->parcour->name }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right side - Date -->
            <div class="flex items-center gap-3 text-sm px-4 py-2 bg-white rounded-xl shadow-sm border border-gray-100">
                <svg class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <time class="font-medium text-gray-700" datetime="{{ now()->format('Y-m-d\TH:i:s') }}">
                    {{ now()->format('d/m/Y H:i') }}
                </time>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="p-6">
        <div class="p-4 flex flex-wrap gap-4 items-center">
            <!-- Recherche -->
            <div class="relative flex-1 min-w-[300px]">
                <input type="text" wire:model.live="search" placeholder="Rechercher un document..." class="w-full pl-10 pr-4 py-2.5 border-0 rounded-lg bg-gray-50 text-sm focus:ring-2 focus:ring-blue-500">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <!-- Filtres -->
            <select wire:model.live="teacherFilter" class="py-2.5 pl-10 pr-4 bg-gray-50 border-0 rounded-lg text-sm appearance-none min-w-[200px]">
                <option value="">Tous les enseignants</option>
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="semesterFilter" class="py-2.5 pl-10 pr-4 bg-gray-50 border-0 rounded-lg text-sm appearance-none min-w-[180px]">
                <option value="">Tous les semestres</option>
                @foreach($semestres as $semestre)
                    <option value="{{ $semestre->id }}">{{ $semestre->name }}</option>
                @endforeach
            </select>

            <!-- Vue toggle -->
            <div class="bg-gray-100 rounded-lg p-1 flex gap-1">
                <button wire:click="toggleView('grid')" class="p-2 rounded {{ $viewType === 'grid' ? 'bg-white shadow text-blue-600' : 'text-gray-500' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </button>
                <button wire:click="toggleView('list')" class="p-2 rounded {{ $viewType === 'list' ? 'bg-white shadow text-blue-600' : 'text-gray-500' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

    </div>
 </div>
