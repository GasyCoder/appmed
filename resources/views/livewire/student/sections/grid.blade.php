<!-- Grille de Documents -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($documents as $document)
        <div class="group relative bg-white dark:bg-gray-800 rounded-xl shadow-xl overflow-hidden hover:shadow-lg transition-all duration-300">
            <!-- Type de document et date -->
            <div class="absolute top-4 right-4 flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-white dark:bg-gray-700 shadow-sm border border-gray-100 dark:border-gray-600 text-gray-600 dark:text-gray-300">
                    {{ strtoupper($document->extension) }}
                </span>
            </div>

            <!-- Contenu principal -->
            <div class="p-6">
                <!-- En-tête avec icône et info enseignant -->
                <div class="flex items-start gap-4 mb-4">
                    <div class="p-3 bg-gradient-to-br from-blue-50 dark:from-blue-900/50 to-indigo-50 dark:to-indigo-900/50 rounded-xl border border-blue-100 dark:border-blue-800">
                        @php
                            $extension = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));
                        @endphp
                        @include('livewire.teacher.forms.file-icons')
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white line-clamp-2 mb-1">
                            {{ $document->title }}
                        </h3>
                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                            <span class="font-medium">{{ $document->teacher->profil->grade }}</span>
                            <span class="text-gray-400 dark:text-gray-500">•</span>
                            <span>{{ $document->uploader->name }}</span>
                        </div>
                    </div>
                </div>

                <!-- Métadonnées -->
                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                        <svg class="h-4 w-4 mr-1.5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ $document->created_at->format('d/m/Y') }}
                    </div>
                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                        <svg class="h-4 w-4 mr-1.5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        {{ number_format($document->file_size / 1024 / 1024, 2) }} MB
                    </div>
                </div>

                <!-- Actions -->
                <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button onclick="openPdfViewer({
                        url: '{{ Storage::url($document->file_path) }}',
                        id: {{ $document->id }},
                        title: '{{ $document->title }}',
                        teacherName: '{{ $document->teacher->name ?? "Non assigné" }}'
                    })"
                    class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-medium
                           bg-gradient-to-r from-blue-50 dark:from-blue-900/50 to-indigo-50 dark:to-indigo-900/50
                           text-blue-700 dark:text-blue-300
                           hover:from-blue-100 dark:hover:from-blue-800 hover:to-indigo-100 dark:hover:to-indigo-800
                           border border-blue-200 dark:border-blue-700 hover:border-blue-300 dark:hover:border-blue-600
                           transition-all duration-200">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Consulter le document
                    </button>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full">
            <div class="flex flex-col items-center justify-center px-6 py-12 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700">
                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-full mb-4">
                    <svg class="h-8 w-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Aucun document trouvé</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 text-center max-w-sm">
                    Modifiez vos critères de recherche pour trouver des documents.
                </p>
            </div>
        </div>
    @endforelse
 </div>
