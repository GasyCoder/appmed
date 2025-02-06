<!-- resources/views/livewire/admin/dashboard.blade.php -->
<div>
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Users Card -->
        <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-indigo-50 rounded-xl">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <div class="text-3xl font-bold text-gray-900">{{ $stats['users_count'] }}</div>
                    <div class="text-sm text-gray-500">Utilisateurs</div>
                </div>
            </div>
            <div class="mt-4 flex space-x-4">
                <div class="text-sm">
                    <span class="text-indigo-600 font-medium">{{ $stats['teachers_count'] }}</span>
                    <span class="text-gray-500">enseignants</span>
                </div>
                <div class="text-sm">
                    <span class="text-indigo-600 font-medium">{{ $stats['students_count'] }}</span>
                    <span class="text-gray-500">étudiants</span>
                </div>
            </div>
        </div>

        <!-- Documents Card -->
        <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-orange-50 rounded-xl">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <div class="text-3xl font-bold text-gray-900">{{ $stats['documents_count'] }}</div>
                    <div class="text-sm text-gray-500">Documents</div>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-sm">
                    <span class="text-orange-600 font-medium">{{ $stats['pending_documents'] }}</span>
                    <span class="text-gray-500">documents en attente</span>
                </div>
            </div>
        </div>

        <!-- Categories Card -->
        <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-emerald-50 rounded-xl">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <div>
                    <div class="text-3xl font-bold text-gray-900">{{ $stats['parcours_count'] + $stats['niveau_count'] }}</div>
                    <div class="text-sm text-gray-500">Total Catégories</div>
                </div>
            </div>
            <div class="mt-4 flex space-x-4">
                <div class="text-sm">
                    <span class="text-emerald-600 font-medium">{{ $stats['parcours_count'] }}</span>
                    <span class="text-gray-500">parcours</span>
                </div>
                <div class="text-sm">
                    <span class="text-emerald-600 font-medium">{{ $stats['niveau_count'] }}</span>
                    <span class="text-gray-500">niveaux</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Documents Récents -->
    <div class="mt-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-medium text-gray-900">Documents Récents</h2>
            <a href="#" class="text-sm text-indigo-600 hover:text-indigo-900">Voir tout →</a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm">
            @forelse($recentDocuments as $document)
                <div class="p-4 hover:bg-gray-50 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <img class="h-8 w-8 rounded-full" src="{{ $document->uploader->profile_photo_url }}" alt="{{ $document->uploader->name }}">
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-900">{{ $document->title }}</h3>
                                <p class="text-xs text-gray-500">{{ $document->uploader->name }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $document->is_actif ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $document->is_actif ? 'Public' : 'En attente' }}
                            </span>
                            <span class="text-xs text-gray-500">{{ $document->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-12">
                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun document récent</h3>
                    <p class="mt-1 text-sm text-gray-500">Commencez par ajouter des documents.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
