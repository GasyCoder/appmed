<!-- Grille de Documents (UI améliorée) -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($documents as $document)
        @php
            $extension = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));
            $isPdf = $extension === 'pdf';
            $isPowerPoint = in_array($extension, ['ppt', 'pptx']);
            $badgeClass = $isPdf
                ? 'bg-red-50 text-red-700 ring-red-200 dark:bg-red-900/20 dark:text-red-300 dark:ring-red-800/40'
                : ($isPowerPoint
                    ? 'bg-orange-50 text-orange-700 ring-orange-200 dark:bg-orange-900/20 dark:text-orange-300 dark:ring-orange-800/40'
                    : 'bg-blue-50 text-blue-700 ring-blue-200 dark:bg-blue-900/20 dark:text-blue-300 dark:ring-blue-800/40');
        @endphp

        <article
            class="group bg-white dark:bg-gray-800 rounded-2xl border border-gray-200/70 dark:border-gray-700/70 shadow-sm
                   hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 overflow-hidden
                   hover:ring-2 hover:ring-blue-500/20 dark:hover:ring-blue-400/20">

            <!-- Header -->
            <div class="p-5 border-b border-gray-100 dark:border-gray-700/70">
                <div class="flex items-start gap-4">
                    <!-- Icon -->
                    <div class="flex-shrink-0 p-3 rounded-xl bg-gray-50 dark:bg-gray-700/60 border border-gray-100 dark:border-gray-700">
                        @include('livewire.teacher.forms.file-icons')
                    </div>

                    <div class="flex-1 min-w-0">
                        <!-- Top row: badge + date (small) -->
                        <div class="flex items-center justify-between gap-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold ring-1 {{ $badgeClass }}">
                                {{ strtoupper($extension) }}
                            </span>

                            <span class="text-[11px] text-gray-500 dark:text-gray-400">
                                {{ $document->created_at->format('d/m/Y') }}
                            </span>
                        </div>

                        <!-- Title -->
                        <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white leading-5 line-clamp-2">
                            {{ $document->title }}
                        </h3>

                        <!-- Teacher -->
                        <div class="mt-2 text-xs text-gray-600 dark:text-gray-400 flex flex-wrap items-center gap-2">
                            @php
                                $grade = $document->teacher?->profil?->grade;
                                $teacherName = $document->uploader?->name;
                            @endphp

                            @if($grade)
                                <span class="font-medium text-gray-700 dark:text-gray-300">{{ $grade }}</span>
                            @endif

                            @if($teacherName)
                                <span class="text-gray-400 dark:text-gray-500">•</span>
                                <span class="truncate">{{ $teacherName }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="p-5">
                <!-- Meta row -->
                <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                    <div class="flex items-center gap-4">
                        <!-- Size -->
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            @if($document->file_size)
                                {{ number_format($document->file_size / 1024 / 1024, 1) }} MB
                            @else
                                -
                            @endif
                        </span>

                        <!-- Views -->
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            {{ (int)($document->view_count ?? 0) }} vues
                        </span>
                    </div>

                    <!-- Hint (format) -->
                    <span class="hidden sm:inline-flex items-center gap-1.5">
                        @if($isPdf)
                            <span class="text-green-600 dark:text-green-400 font-medium">Lecture directe</span>
                        @elseif($isPowerPoint)
                            <span class="text-orange-600 dark:text-orange-400 font-medium">Téléchargement conseillé</span>
                        @else
                            <span class="text-gray-500 dark:text-gray-400 font-medium">Document</span>
                        @endif
                    </span>
                </div>

                <!-- Actions -->
                <div class="mt-4 flex gap-2">
                    <!-- Consulter (compte la vue via document.serve) -->
                    <a href="{{ route('document.serve', $document) }}"
                       target="_blank" rel="noopener"
                       class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl text-sm font-semibold
                              text-white bg-blue-600 hover:bg-blue-700
                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800
                              transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Consulter
                    </a>

                    @php $filename = basename($document->file_path); @endphp
                    <a href="{{ route('pdf.download', ['filename' => urlencode($filename)]) }}" download
                       class="inline-flex items-center justify-center px-3 py-2.5 rounded-xl text-sm font-semibold
                              text-gray-700 bg-gray-100 hover:bg-gray-200
                              dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600
                              focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800
                              transition"
                       title="Télécharger">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </a>
                </div>

                <!-- Mobile hint -->
                <div class="mt-3 text-center sm:hidden">
                    @if($isPdf)
                        <span class="text-xs text-green-600 dark:text-green-400 font-medium">✓ Lecture directe</span>
                    @elseif($isPowerPoint)
                        <span class="text-xs text-orange-600 dark:text-orange-400 font-medium">Téléchargement recommandé</span>
                    @else
                        <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Document</span>
                    @endif
                </div>
            </div>
        </article>

    @empty
        <div class="col-span-full">
            <div class="text-center py-16 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900
                        rounded-2xl border-2 border-dashed border-gray-300 dark:border-gray-600">
                <div class="mb-6">
                    <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                              d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Aucun document disponible</h3>
                <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                    Modifiez vos critères de recherche ou revenez plus tard pour découvrir de nouveaux documents.
                </p>
            </div>
        </div>
    @endforelse
</div>

<style>
.line-clamp-2{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
</style>
