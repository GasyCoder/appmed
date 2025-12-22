<!-- Vue Liste Documents (UI améliorée) -->
<div class="space-y-4">
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

            $grade = $document->teacher?->profil?->grade;
            $teacherName = $document->uploader?->name;
            $views = (int)($document->view_count ?? 0);

            // Taille : privilégier formatted_size si tu l'as, sinon calcul simple
            $sizeLabel = $document->formatted_size
                ?? ($document->file_size ? number_format($document->file_size / 1024 / 1024, 1) . ' MB' : '-');
        @endphp

        <article
            class="group bg-white dark:bg-gray-800 rounded-2xl border border-gray-200/70 dark:border-gray-700/70 shadow-sm
                   hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 overflow-hidden
                   hover:ring-2 hover:ring-blue-500/20 dark:hover:ring-blue-400/20">

            <div class="p-4 sm:p-5">
                <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-5">

                    <!-- Icon + extension -->
                    <div class="flex-shrink-0">
                        <div class="p-3 rounded-2xl bg-gray-50 dark:bg-gray-700/60 border border-gray-100 dark:border-gray-700 w-16 text-center">
                            @include('livewire.teacher.forms.file-icons')

                            <span class="mt-2 inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[11px] font-semibold ring-1 {{ $badgeClass }}">
                                {{ strtoupper($extension) }}
                            </span>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-3">
                            <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white leading-6 line-clamp-2">
                                {{ $document->title }}
                            </h3>

                            <!-- Indicateur format (desktop) -->
                            <div class="hidden sm:flex items-center">
                                @if($isPdf)
                                    <span class="text-xs font-semibold text-green-600 dark:text-green-400">
                                        Lecture directe
                                    </span>
                                @elseif($isPowerPoint)
                                    <span class="text-xs font-semibold text-orange-600 dark:text-orange-400">
                                        Téléchargement conseillé
                                    </span>
                                @else
                                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">
                                        Document
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Meta -->
                        <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-2 text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                            <!-- Teacher -->
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="h-4 w-4 text-gray-400 dark:text-gray-500" viewBox="0 0 24 24" fill="none">
                                    <path stroke="currentColor" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    <path stroke="currentColor" stroke-width="2" d="M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span class="truncate">
                                    {{ trim(($grade ?? '') . ' ' . ($teacherName ?? '')) ?: 'Enseignant non défini' }}
                                </span>
                            </span>

                            <!-- Date -->
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="h-4 w-4 text-gray-400 dark:text-gray-500" viewBox="0 0 24 24" fill="none">
                                    <path stroke="currentColor" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ $document->created_at->format('d/m/Y') }}
                            </span>

                            <!-- Size -->
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="h-4 w-4 text-gray-400 dark:text-gray-500" viewBox="0 0 24 24" fill="none">
                                    <path stroke="currentColor" stroke-width="2"
                                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                {{ $sizeLabel }}
                            </span>

                            <!-- Views -->
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="h-4 w-4 text-gray-400 dark:text-gray-500" viewBox="0 0 24 24" fill="none">
                                    <path stroke="currentColor" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke="currentColor" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                {{ $views }} vues
                            </span>
                        </div>

                        <!-- Mobile format hint -->
                        <div class="mt-3 sm:hidden">
                            @if($isPdf)
                                <span class="text-xs font-semibold text-green-600 dark:text-green-400">✓ Lecture directe</span>
                            @elseif($isPowerPoint)
                                <span class="text-xs font-semibold text-orange-600 dark:text-orange-400">Téléchargement recommandé</span>
                            @else
                                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">Document</span>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex-shrink-0 w-full sm:w-auto">
                        <div class="flex gap-2 sm:flex-col sm:items-stretch">
                            <!-- Consulter (compte la vue) -->
                            <a href="{{ route('document.serve', $document) }}"
                               target="_blank" rel="noopener"
                               class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                                      text-white bg-blue-600 hover:bg-blue-700
                                      focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800
                                      transition">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Consulter
                            </a>

                            @php $filename = basename($document->file_path); @endphp
                            <!-- Télécharger -->
                            <a href="{{ route('pdf.download', ['filename' => urlencode($filename)]) }}" download
                               class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                                      text-gray-700 bg-gray-100 hover:bg-gray-200
                                      dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600
                                      focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800
                                      transition"
                               title="Télécharger">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Télécharger
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </article>

    @empty
        <div class="flex flex-col items-center justify-center py-10 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700">
            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-full mb-4">
                <svg class="h-8 w-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">Aucun document trouvé</h3>
            <p class="mt-2 text-xs sm:text-sm text-gray-500 dark:text-gray-400 text-center max-w-sm px-4">
                Modifiez vos critères de recherche pour trouver des documents.
            </p>
        </div>
    @endforelse
</div>

<style>
.line-clamp-2{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
</style>
