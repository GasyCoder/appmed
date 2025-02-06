<div class="py-6 bg-gray-50">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- En-tête avec recherche -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Mes Enseignants</h1>
            <p class="mt-1 text-sm text-gray-600">Consultez les informations de vos enseignants</p>
        </div>

        <!-- Barre de recherche -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
            <div class="max-w-md">
                <label for="search" class="sr-only">Rechercher un enseignant</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input wire:model.live="search"
                           type="search"
                           id="search"
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white
                                  placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1
                                  focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                           placeholder="Rechercher par nom, grade ou département...">
                </div>
            </div>
        </div>

        <!-- Grille des enseignants -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($teachers as $teacher)
                <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden">
                    <!-- En-tête de la carte -->
                    <div class="relative h-32 bg-gradient-to-r from-indigo-500 to-purple-600">
                        <div class="absolute -bottom-10 left-6">
                            <img class="h-20 w-20 rounded-xl border-4 border-white object-cover shadow-sm"
                                 src="{{ $teacher->profile_photo_url }}"
                                 alt="{{ $teacher->name }}">
                        </div>
                    </div>

                    <!-- Contenu de la carte -->
                    <div class="p-6 pt-12">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">
                                @if($teacher->profil->grade ?? '')
                                    {{ $teacher->profil->grade. '. ' .$teacher->name }}
                                @else
                                    {{ $teacher->name }}
                                @endif
                            </h3>
                            <div class="mt-1 flex items-center text-sm text-gray-500">
                                <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                {{ $teacher->email }}
                            </div>
                        </div>

                        @if($teacher->profil)
                        <div class="space-y-2 mb-4">
                            {{-- @if($teacher->profil->grade)
                            <div class="flex items-center text-sm">
                                <svg class="h-4 w-4 text-gray-400 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                                </svg>
                                <span class="text-gray-600">{{ $teacher->profil->grade }}</span>
                            </div>
                            @endif --}}

                            @if($teacher->profil->departement)
                            <div class="flex items-center text-sm">
                                <svg class="h-4 w-4 text-gray-400 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span class="text-gray-600">{{ $teacher->profil->departement }}</span>
                            </div>
                            @endif
                        </div>
                        @endif

                        <!-- Statistiques -->
                        <div class="grid grid-cols-3 gap-4 py-4 border-t border-b border-gray-100">
                            <div class="text-center">
                                <span class="block text-lg font-semibold text-indigo-600">
                                    {{ $teacher->teacherNiveaux->count() }}
                                </span>
                                <span class="text-xs text-gray-500">Niveaux</span>
                            </div>
                            <div class="text-center">
                                <span class="block text-lg font-semibold text-indigo-600">
                                    {{ $teacher->teacherParcours->count() }}
                                </span>
                                <span class="text-xs text-gray-500">Parcours</span>
                            </div>
                            <div class="text-center">
                                <span class="block text-lg font-semibold text-indigo-600">
                                    {{ $teacher->documents_count }}
                                </span>
                                <span class="text-xs text-gray-500">Documents</span>
                            </div>
                        </div>

                        <!-- Bouton Voir profil -->
                        <div class="mt-4">
                            <button wire:click="showTeacherProfile({{ $teacher->id }})"
                                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent
                                           rounded-lg text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700
                                           focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500
                                           transition-colors duration-200">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Voir le profil
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="text-center bg-white rounded-lg shadow-sm p-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun enseignant trouvé</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Aucun enseignant n'est disponible pour votre niveau actuellement.
                        </p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Modal du profil détaillé -->
    @include('livewire.student.modal.teacher-info')
</div>
