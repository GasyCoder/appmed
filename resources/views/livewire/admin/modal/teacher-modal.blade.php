@if($showUserModal)
<div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <div class="relative bg-white rounded-xl shadow-xl transform transition-all w-full max-w-2xl">
            <!-- En-tête du modal -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-t-xl px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-white">
                        {{ $userId ? 'Modifier l\'enseignant' : 'Nouvel enseignant' }}
                    </h3>
                    <button wire:click="resetForm" class="text-white hover:text-gray-200">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <form wire:submit="createTeacher" class="p-6">
                <!-- Sections avec onglets -->
                <div x-data="{ activeTab: 'info' }">
                    <!-- Navigation des onglets -->
                    <div class="border-b border-gray-200 mb-6">
                        <nav class="-mb-px flex space-x-8">
                            <button type="button"
                                    @click="activeTab = 'info'"
                                    :class="{'border-indigo-500 text-indigo-600': activeTab === 'info',
                                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'info'}"
                                    class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">
                                Informations de base
                            </button>
                            <button type="button"
                                    @click="activeTab = 'profile'"
                                    :class="{'border-indigo-500 text-indigo-600': activeTab === 'profile',
                                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'profile'}"
                                    class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">
                                Profil détaillé
                            </button>
                            <button type="button"
                                    @click="activeTab = 'niveaux'"
                                    :class="{'border-indigo-500 text-indigo-600': activeTab === 'niveaux',
                                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'niveaux'}"
                                    class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">
                                Niveaux & Parcours
                            </button>
                        </nav>
                    </div>

                    <!-- Contenu des onglets -->
                    <div class="space-y-6">
                        <!-- Informations de base -->
                        <div x-show="activeTab === 'info'">
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Nom complet</label>
                                    <input type="text"
                                           wire:model="name"
                                           id="name"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('name')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email"
                                           wire:model="email"
                                           id="email"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('email')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700">
                                        {{ $userId ? 'Nouveau mot de passe (laisser vide si inchangé)' : 'Mot de passe' }}
                                    </label>
                                    <input type="password"
                                           wire:model="password"
                                           id="password"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('password')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div> --}}

                            </div>
                        </div>

                        <!-- Profil détaillé -->
                        <div x-show="activeTab === 'profile'">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Grade</label>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                        @foreach(\App\Models\Profil::getGrades() as $grade)
                                            <label class="inline-flex items-center p-2 border rounded-md cursor-pointer hover:bg-gray-50 transition-colors duration-200">
                                                <input type="radio"
                                                       wire:model="grade"
                                                       name="grade"
                                                       value="{{ $grade }}"
                                                       class="form-radio h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                                <span class="ml-2 text-sm font-medium text-gray-700">{{ $grade }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('grade')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Genre</label>
                                    <div class="grid grid-cols-2 gap-4">
                                        <label class="inline-flex items-center p-2 border rounded-md cursor-pointer hover:bg-gray-50 transition-colors duration-200">
                                            <input type="radio"
                                                   wire:model="sexe"
                                                   name="sexe"
                                                   value="homme"
                                                   class="form-radio h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                            <span class="ml-2 text-sm font-medium text-gray-700">Homme</span>
                                        </label>
                                        <label class="inline-flex items-center p-2 border rounded-md cursor-pointer hover:bg-gray-50 transition-colors duration-200">
                                            <input type="radio"
                                                   wire:model="sexe"
                                                   name="sexe"
                                                   value="femme"
                                                   class="form-radio h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                            <span class="ml-2 text-sm font-medium text-gray-700">Femme</span>
                                        </label>
                                    </div>
                                    @error('sexe')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label for="telephone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                                    <input type="tel"
                                           wire:model="telephone"
                                           id="telephone"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('telephone')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label for="departement" class="block text-sm font-medium text-gray-700">
                                        Spécialité
                                    </label>
                                    <input type="text"
                                           wire:model="departement"
                                           id="departement"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('departement')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Niveaux et Parcours -->
                        <div x-show="activeTab === 'niveaux'">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Niveaux d'enseignement</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach($niveaux as $niveau)
                                            <label class="inline-flex items-center p-2 border rounded-md cursor-pointer hover:bg-gray-50">
                                                <input type="checkbox"
                                                       wire:model="selectedTeacherNiveaux"
                                                       value="{{ $niveau->id }}"
                                                       class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                                <span class="ml-2 text-sm text-gray-700">{{ $niveau->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('selectedTeacherNiveaux')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Parcours d'enseignement</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach($parcours as $parcour)
                                            <label class="inline-flex items-center p-2 border rounded-md cursor-pointer hover:bg-gray-50">
                                                <input type="checkbox"
                                                       wire:model="selectedTeacherParcours"
                                                       value="{{ $parcour->id }}"
                                                       class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                                <span class="ml-2 text-sm text-gray-700">{{ $parcour->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('selectedTeacherParcours')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status de l'utilisateur -->
                            <div class="mt-4">
                                <label class="flex items-center">
                                    <input type="checkbox"
                                           wire:model="status"
                                           class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700">Compte actif</span>
                                </label>
                            </div>
                        </div>
                    </div>
                                    <!-- Footer avec boutons -->
                @include('livewire.admin.sections.footer-buttons')
                </div>
            </form>
        </div>
    </div>
</div>
@endif
