<!-- Vue Liste Documents -->
<div class="space-y-4">
    @forelse($documents as $document)
        <div class="group bg-white dark:bg-gray-800 rounded-xl overflow-hidden hover:shadow-lg transition-all duration-200">
            <div class="p-5">
                <div class="flex items-center gap-5">
                    <!-- Document Icon -->
                    <div class="flex-shrink-0">
                        <div class="p-3 bg-gradient-to-br from-blue-50 dark:from-blue-900/50 to-indigo-50 dark:to-indigo-900/50 
                                  rounded-xl border border-blue-100 dark:border-blue-800">
                            @php
                                $extension = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));
                            @endphp
                            @include('livewire.teacher.forms.file-icons')
                            <span class="block text-xs font-medium text-center mt-1 text-gray-500 dark:text-gray-400">
                                {{ strtoupper($document->extension) }}
                            </span>
                        </div>
                    </div>
 
                    <!-- Document Info -->
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $document->title }}</h3>
                        <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <span class="flex items-center gap-1.5">
                                <svg class="h-4 w-4 text-gray-400 dark:text-gray-500" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-width="2" fill="none" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                {{ $document->teacher->profil->grade }} {{ $document->uploader->name }}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <svg class="h-4 w-4 text-gray-400 dark:text-gray-500" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-width="2" fill="none" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $document->created_at->format('d/m/Y') }}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <svg class="h-4 w-4 text-gray-400 dark:text-gray-500" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-width="2" fill="none" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                {{ $document->formatted_size }}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <svg class="h-4 w-4 text-gray-400 dark:text-gray-500" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-width="2" fill="none" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                {{ $document->view_count }} vues
                            </span>
                        </div>
                    </div>
 
                    <!-- Actions -->
                    <div class="flex-shrink-0">
                        <button
                            onclick="openPdfViewer({
                                url: '{{ Storage::url($document->file_path) }}',
                                id: {{ $document->id }},
                                title: '{{ $document->title }}',
                                teacherName: '{{ $document->teacher->name ?? "Non assigné" }}'
                            })"
                            class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium
                                    bg-gradient-to-r from-blue-50 dark:from-blue-900/50 to-indigo-50 dark:to-indigo-900/50 
                                    text-blue-700 dark:text-blue-300
                                    hover:from-blue-100 dark:hover:from-blue-800 hover:to-indigo-100 dark:hover:to-indigo-800
                                    border border-blue-200 dark:border-blue-700 hover:border-blue-300 dark:hover:border-blue-600
                                    transition-all duration-200">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Consulter
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="flex flex-col items-center justify-center py-12 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700">
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
    @endforelse
 </div>