<div class="mx-auto w-full max-w-[88rem] px-4 sm:px-6 lg:px-8 py-6 pb-24 lg:pb-6 space-y-6" x-data="{ viewMode: 'grid' }">

@php
    $authUser = auth()->user();
    $isStudent = $authUser && method_exists($authUser, 'hasRole') && $authUser->hasRole('student');
@endphp

    {{-- Bouton retour (uniquement pour les étudiants) --}}
    @if($isStudent)
        <div class="mb-4">
            <a href="{{ route('studentEspace') }}"
               class="inline-flex items-center gap-3 px-4 py-3 rounded-2xl
                      text-gray-700 dark:text-gray-300
                      hover:bg-gray-50 dark:hover:bg-gray-900/50
                      transition">
                <div class="h-10 w-10 rounded-xl bg-gray-100 dark:bg-gray-900
                            flex items-center justify-center
                            text-gray-700 dark:text-gray-200">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </div>
                <div>
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">
                        Retour à l'accueil
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        Menu étudiant
                    </div>
                </div>
            </a>
        </div>
    @endif

    {{-- Header --}}
    <div class="mb-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <h1 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                    Emplois du temps
                </h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Consultez vos emplois du temps et plannings
                </p>
            </div>

            {{-- Quick stats (UI only) --}}
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-2 rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-200">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    Total : {{ $schedules->count() }}
                </span>
            </div>
        </div>
    </div>

    {{-- Top controls --}}
    <div class="mb-6">
        <div class="rounded-2xl bg-white/70 dark:bg-gray-900/40 backdrop-blur border border-gray-200/60 dark:border-gray-800/70 shadow-sm">
            <div class="p-3 sm:p-4">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">

                    {{-- Left: Filter pills --}}
                    <div class="flex items-center gap-2 overflow-x-auto pb-1 
                                [-ms-overflow-style:none] [scrollbar-width:none] 
                                [&::-webkit-scrollbar]:hidden">
                        
                        {{-- Tous --}}
                        <button wire:click="setViewedFilter('all')"
                                class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-medium transition shrink-0
                                    focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/70
                                    {{ $viewedFilter === 'all' 
                                        ? 'bg-blue-600 text-white shadow-sm ring-2 ring-blue-600/20' 
                                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="hidden min-[375px]:inline">Tous</span>
                            <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[10px] sm:text-xs font-semibold
                                        {{ $viewedFilter === 'all' ? 'bg-white/20' : 'bg-gray-200 dark:bg-gray-700' }}">
                                {{ ($viewedCount ?? 0) + ($unviewedCount ?? 0) }}
                            </span>
                        </button>

                        {{-- Non vus --}}
                        <button wire:click="setViewedFilter('unviewed')"
                                class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-medium transition shrink-0
                                    focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500/70
                                    {{ $viewedFilter === 'unviewed' 
                                        ? 'bg-red-600 text-white shadow-sm ring-2 ring-red-600/20' 
                                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                            <span class="hidden min-[375px]:inline whitespace-nowrap">Non vus</span>
                            @if(($unviewedCount ?? 0) > 0)
                                <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[10px] sm:text-xs font-semibold
                                            {{ $viewedFilter === 'unviewed' ? 'bg-white/20' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300' }}">
                                    {{ $unviewedCount }}
                                </span>
                            @endif
                        </button>

                        {{-- Vus --}}
                        <button wire:click="setViewedFilter('viewed')"
                                class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-medium transition shrink-0
                                    focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/70
                                    {{ $viewedFilter === 'viewed' 
                                        ? 'bg-emerald-600 text-white shadow-sm ring-2 ring-emerald-600/20' 
                                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <span class="hidden min-[375px]:inline">Vus</span>
                            @if(($viewedCount ?? 0) > 0)
                                <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[10px] sm:text-xs font-semibold
                                            {{ $viewedFilter === 'viewed' ? 'bg-white/20' : 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300' }}">
                                    {{ $viewedCount }}
                                </span>
                            @endif
                        </button>
                    </div>

                    {{-- Right: View switch (segmented control) --}}
                    <div class="flex items-center justify-end">
                        <div class="inline-flex items-center rounded-full bg-gray-100 p-1 dark:bg-gray-800">
                            <button @click="viewMode = 'grid'"
                                    class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-sm font-medium transition
                                           focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/70"
                                    :class="viewMode === 'grid' ? 'bg-white text-gray-900 shadow-sm dark:bg-gray-900 dark:text-white' : 'text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white'">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                </svg>
                                Grille
                            </button>

                            <button @click="viewMode = 'list'"
                                    class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-sm font-medium transition
                                           focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/70"
                                    :class="viewMode === 'list' ? 'bg-white text-gray-900 shadow-sm dark:bg-gray-900 dark:text-white' : 'text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white'">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                                Liste
                            </button>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Flash messages (UI polish) --}}
            @if (session()->has('error') || session()->has('success'))
                <div class="px-3 pb-3 sm:px-4 sm:pb-4">
                    @if (session()->has('error'))
                        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-900/50 dark:bg-red-900/20 dark:text-red-300">
                            <div class="flex items-start gap-2">
                                <svg class="mt-0.5 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 9v2m0 4h.01M10.29 3.86l-8.3 14.38A2 2 0 003.72 21h16.56a2 2 0 001.73-2.76l-8.3-14.38a2 2 0 00-3.42 0z"/>
                                </svg>
                                <p>{{ session('error') }}</p>
                            </div>
                        </div>
                    @endif

                    @if (session()->has('success'))
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-900/20 dark:text-emerald-200">
                            <div class="flex items-start gap-2">
                                <svg class="mt-0.5 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p>{{ session('success') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Vue Grille --}}
    <div x-show="viewMode === 'grid'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($schedules as $schedule)
            <div class="group rounded-2xl bg-white dark:bg-gray-900 border border-gray-200/70 dark:border-gray-800/70 shadow-sm hover:shadow-md transition overflow-hidden">
                {{-- Preview --}}
                <div class="relative h-36 bg-gray-100 dark:bg-gray-800 overflow-hidden">
                    @php
                        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
                        $extension = pathinfo($schedule->file_path, PATHINFO_EXTENSION);
                        $isImage = in_array(strtolower($extension), $imageExtensions);
                    @endphp
                    <img src="{{ asset('assets/image/shedule.jpg') }}"
                            alt="{{ $schedule->title }}"
                            class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.03]">
                    {{-- Type badge --}}
                    <div class="absolute top-2 right-2">
                        <span class="inline-flex items-center rounded-full bg-white/85 dark:bg-gray-950/60 backdrop-blur px-2.5 py-1 text-[11px] font-semibold text-gray-800 dark:text-gray-100 border border-gray-200/70 dark:border-gray-800/70">
                            {{ ucfirst(str_replace('_', ' ', $schedule->type)) }}
                        </span>
                    </div>
                </div>

                {{-- Content --}}
                <div class="p-4">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-2 min-h-[2.5rem]">
                        {{ $schedule->title }}
                    </h3>

                    {{-- Meta chips --}}
                    <div class="mt-3 flex flex-wrap gap-1.5">
                        <span class="inline-flex items-center rounded-full bg-indigo-50 px-2 py-1 text-[11px] font-medium text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">
                            {{ $schedule->academic_year }}
                        </span>

                        @if($schedule->niveau)
                            <span class="inline-flex items-center rounded-full bg-sky-50 px-2 py-1 text-[11px] font-medium text-sky-700 dark:bg-sky-900/30 dark:text-sky-300">
                                {{ $schedule->niveau->name }}
                            </span>
                        @endif

                        @if($schedule->parcour)
                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-1 text-[11px] font-medium text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                                {{ $schedule->parcour->name }}
                            </span>
                        @endif
                    </div>

                    {{-- Dates --}}
                    @if($schedule->start_date || $schedule->end_date)
                        <div class="mt-3 flex items-center gap-2 rounded-xl bg-gray-50 px-3 py-2 text-xs text-gray-700 dark:bg-gray-800 dark:text-gray-200">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>

                            @if($schedule->start_date && $schedule->end_date)
                                <span class="font-medium">
                                    {{ \Carbon\Carbon::parse($schedule->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($schedule->end_date)->format('d/m/Y') }}
                                </span>
                            @elseif($schedule->start_date)
                                <span>Dès {{ \Carbon\Carbon::parse($schedule->start_date)->format('d/m/Y') }}</span>
                            @elseif($schedule->end_date)
                                <span>Jusqu'au {{ \Carbon\Carbon::parse($schedule->end_date)->format('d/m/Y') }}</span>
                            @endif
                        </div>
                    @endif

                    {{-- Stats --}}
                    <div class="mt-3 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            {{ $schedule->view_count ?? 0 }}
                        </span>

                        @php
                            $filePath = storage_path('app/public/' . $schedule->file_path);
                            $fileSize = file_exists($filePath) ? filesize($filePath) : 0;

                            if ($fileSize >= 1048576) {
                                $formatted = number_format($fileSize / 1048576, 1) . ' MB';
                            } elseif ($fileSize >= 1024) {
                                $formatted = number_format($fileSize / 1024, 0) . ' KB';
                            } else {
                                $formatted = $fileSize . ' B';
                            }
                        @endphp
                        <span>{{ $formatted }}</span>
                    </div>

                    {{-- Actions --}}
                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('schedule.view', $schedule->id) }}"
                           target="_blank"
                           class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-700
                                  focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/70 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-950 transition">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Voir PDF
                        </a>

                        <button wire:click="downloadSchedule({{ $schedule->id }})"
                                class="inline-flex items-center justify-center rounded-xl border border-gray-200 px-3 py-2 text-gray-700 hover:bg-gray-50
                                       dark:border-gray-800 dark:text-gray-200 dark:hover:bg-gray-800
                                       focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/70 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-950 transition"
                                title="Télécharger">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                        </button>
                    </div>

                </div>
            </div>
        @empty
            <div class="col-span-full rounded-2xl border border-gray-200/70 dark:border-gray-800/70 bg-white dark:bg-gray-900 p-10 text-center shadow-sm">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800">
                    <svg class="h-6 w-6 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="mt-3 text-sm font-semibold text-gray-900 dark:text-white">
                    Aucun emploi du temps disponible
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Les emplois du temps seront bientôt disponibles.
                </p>
            </div>
        @endforelse
    </div>

    {{-- Vue Liste --}}
    <div x-show="viewMode === 'list'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-3">
        @forelse($schedules as $schedule)
            <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-200/70 dark:border-gray-800/70 shadow-sm hover:shadow-md transition overflow-hidden">
                <div class="p-4 sm:p-5 flex flex-col sm:flex-row gap-4">
                    {{-- Thumb --}}
                    <div class="flex-shrink-0">
                        <div class="w-full sm:w-28 h-24 rounded-2xl bg-gray-100 dark:bg-gray-800 overflow-hidden border border-gray-200/70 dark:border-gray-800/70">
                            @php
                                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
                                $extension = pathinfo($schedule->file_path, PATHINFO_EXTENSION);
                                $isImage = in_array(strtolower($extension), $imageExtensions);
                            @endphp

                            @if($isImage)
                                <img src="{{ Storage::url($schedule->file_path) }}"
                                     alt="{{ $schedule->title }}"
                                     class="h-full w-full object-cover">
                            @else
                                <div class="h-full w-full flex items-center justify-center">
                                    <svg class="h-8 w-8 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0">
                                <h3 class="text-sm sm:text-base font-semibold text-gray-900 dark:text-white">
                                    {{ $schedule->title }}
                                </h3>

                                <div class="mt-2 flex flex-wrap gap-1.5">
                                    <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-1 text-[11px] font-medium text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">
                                        {{ ucfirst(str_replace('_', ' ', $schedule->type)) }}
                                    </span>
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-[11px] font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-200">
                                        {{ $schedule->academic_year }}
                                    </span>
                                    @if($schedule->niveau)
                                        <span class="inline-flex items-center rounded-full bg-sky-50 px-2.5 py-1 text-[11px] font-medium text-sky-700 dark:bg-sky-900/30 dark:text-sky-300">
                                            {{ $schedule->niveau->name }}
                                        </span>
                                    @endif
                                    @if($schedule->parcour)
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-medium text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                                            {{ $schedule->parcour->name }}
                                        </span>
                                    @endif
                                </div>

                                @if($schedule->start_date || $schedule->end_date)
                                    <div class="mt-2 inline-flex items-center gap-2 text-xs text-gray-600 dark:text-gray-300">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>

                                        @if($schedule->start_date && $schedule->end_date)
                                            <span class="font-medium">
                                                {{ \Carbon\Carbon::parse($schedule->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($schedule->end_date)->format('d/m/Y') }}
                                            </span>
                                        @elseif($schedule->start_date)
                                            <span>Dès {{ \Carbon\Carbon::parse($schedule->start_date)->format('d/m/Y') }}</span>
                                        @elseif($schedule->end_date)
                                            <span>Jusqu'au {{ \Carbon\Carbon::parse($schedule->end_date)->format('d/m/Y') }}</span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-2 sm:ml-4">
                                <a href="{{ route('schedule.view', $schedule->id) }}"
                                   target="_blank"
                                   class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-700
                                          focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/70 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-950 transition">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Voir
                                </a>

                                <button wire:click="downloadSchedule({{ $schedule->id }})"
                                        class="inline-flex items-center justify-center rounded-xl border border-gray-200 px-3 py-2 text-gray-700 hover:bg-gray-50
                                               dark:border-gray-800 dark:text-gray-200 dark:hover:bg-gray-800
                                               focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/70 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-950 transition"
                                        title="Télécharger">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Small footer stats --}}
                        <div class="mt-3 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                {{ $schedule->view_count ?? 0 }}
                            </span>

                            @php
                                $filePath = storage_path('app/public/' . $schedule->file_path);
                                $fileSize = file_exists($filePath) ? filesize($filePath) : 0;

                                if ($fileSize >= 1048576) {
                                    $formatted = number_format($fileSize / 1048576, 1) . ' MB';
                                } elseif ($fileSize >= 1024) {
                                    $formatted = number_format($fileSize / 1024, 0) . ' KB';
                                } else {
                                    $formatted = $fileSize . ' B';
                                }
                            @endphp
                            <span>{{ $formatted }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-gray-200/70 dark:border-gray-800/70 bg-white dark:bg-gray-900 p-10 text-center shadow-sm">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800">
                    <svg class="h-6 w-6 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="mt-3 text-sm font-semibold text-gray-900 dark:text-white">
                    Aucun emploi du temps disponible
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Les emplois du temps seront bientôt disponibles.
                </p>
            </div>
        @endforelse
    </div>

    {{-- Loading indicator --}}
    <div wire:loading
         class="fixed bottom-4 right-4 z-50 rounded-2xl bg-gray-900 text-white px-4 py-2 shadow-lg">
        <div class="flex items-center gap-2">
            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-sm font-medium">Chargement…</span>
        </div>
    </div>

</div>
