<!-- resources/views/livewire/admin/dashboard.blade.php -->
<div x-data="{ loading: true }" x-init="setTimeout(() => loading = false, 600)">
    
    {{-- Skeleton Loading --}}
    <div x-show="loading" x-cloak class="space-y-8">
        {{-- Stats Cards Skeleton --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-skeleton.stat-card />
            <x-skeleton.stat-card />
            <x-skeleton.stat-card />
        </div>

        {{-- Recents Skeleton --}}
        <div class="space-y-4">
            <x-skeleton.line width="w-48" height="h-6" />
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                    <x-skeleton.table-row :columns="3" :hasActions="false" />
                </div>
                <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                    <x-skeleton.table-row :columns="3" :hasActions="false" />
                </div>
                <div class="p-4">
                    <x-skeleton.table-row :columns="3" :hasActions="false" />
                </div>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div x-show="!loading" x-cloak x-transition>
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Users Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-indigo-50 dark:bg-indigo-900 rounded-xl">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['users_count'] }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Utilisateurs</div>
                    </div>
                </div>
                <div class="mt-4 flex space-x-4">
                    <div class="text-sm">
                        <span class="text-indigo-600 dark:text-indigo-400 font-medium">{{ $stats['teachers_count'] }}</span>
                        <span class="text-gray-500 dark:text-gray-400">enseignants</span>
                    </div>
                    <div class="text-sm">
                        <span class="text-indigo-600 dark:text-indigo-400 font-medium">{{ $stats['students_count'] }}</span>
                        <span class="text-gray-500 dark:text-gray-400">étudiants</span>
                    </div>
                </div>
            </div>

            <!-- Documents Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-orange-50 dark:bg-orange-900 rounded-xl">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['documents_count'] }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Documents</div>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="text-sm">
                        <span class="text-orange-600 dark:text-orange-400 font-medium">{{ $stats['pending_documents'] }}</span>
                        <span class="text-gray-500 dark:text-gray-400">documents en attente</span>
                    </div>
                </div>
            </div>

            <!-- Categories Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-900 rounded-xl">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['parcours_count'] + $stats['niveau_count'] }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Total Catégories</div>
                    </div>
                </div>
                <div class="mt-4 flex space-x-4">
                    <div class="text-sm">
                        <span class="text-emerald-600 dark:text-emerald-400 font-medium">{{ $stats['parcours_count'] }}</span>
                        <span class="text-gray-500 dark:text-gray-400">parcours</span>
                    </div>
                    <div class="text-sm">
                        <span class="text-emerald-600 dark:text-emerald-400 font-medium">{{ $stats['niveau_count'] }}</span>
                        <span class="text-gray-500 dark:text-gray-400">niveaux</span>
                    </div>
                </div>
            </div>
        </div>


        <!-- Documents Récents -->
        <div class="mt-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Documents Récents</h2>
                <a href="#" class="text-sm text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">Voir tout →</a>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm">
                @forelse($recentDocuments as $document)
                    <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700' : '' }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <img class="h-8 w-8 rounded-full" src="{{ $document->uploader->profile_photo_url }}" alt="{{ $document->uploader->name }}">
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $document->title }}</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $document->uploader->name }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $document->is_actif ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300' : 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300' }}">
                                    {{ $document->is_actif ? 'Public' : 'En attente' }}
                                </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $document->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-12">
                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Aucun document récent</h3>
                        <p class="mt-1 text-sm dark:text-gray-400">Commencez par ajouter des documents.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
