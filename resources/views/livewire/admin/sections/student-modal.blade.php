@if($showUserModal)
<div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <div class="relative bg-white rounded-xl shadow-xl transform transition-all w-full max-w-2xl">
            <!-- En-tête du modal -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-t-xl px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-white">
                        {{ $userId ? 'Modifier l\'étudiant' : 'Nouvel étudiant' }}
                    </h3>
                    <button wire:click="resetForm" class="text-white hover:text-gray-200">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <form wire:submit="createStudent" class="p-6">
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
                                    @click="activeTab = 'academic'"
                                    :class="{'border-indigo-500 text-indigo-600': activeTab === 'academic',
                                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'academic'}"
                                    class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">
                                Niveau & Parcours
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

                                <div>
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
                                </div>
                            </div>
                        </div>

                        <!-- Profil détaillé -->
                        <div x-show="activeTab === 'profile'">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label for="sexe" class="block text-sm font-medium text-gray-700">Genre</label>
                                    <select wire:model="sexe" id="sexe"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Sélectionner un genre</option>
                                        <option value="homme">Homme</option>
                                        <option value="femme">Femme</option>
                                    </select>
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
                                    <label for="departement" class="block text-sm font-medium text-gray-700">Département</label>
                                    <input type="text"
                                           wire:model="departement"
                                           id="departement"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('departement')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label for="ville" class="block text-sm font-medium text-gray-700">Ville</label>
                                    <input type="text"
                                           wire:model="ville"
                                           id="ville"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('ville')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Niveau et Parcours -->
                        <div x-show="activeTab === 'academic'">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label for="niveau_id" class="block text-sm font-medium text-gray-700">Niveau</label>
                                    <select wire:model="niveau_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Sélectionner un niveau</option>
                                        @foreach($niveaux as $niveau)
                                            <option value="{{ $niveau->id }}">{{ $niveau->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('niveau_id')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label for="parcour_id" class="block text-sm font-medium text-gray-700">Parcours</label>
                                    <select wire:model="parcour_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Sélectionner un parcours</option>
                                        @foreach($parcours as $parcour)
                                            <option value="{{ $parcour->id }}">{{ $parcour->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('parcour_id')
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
                </div>

                <!-- Footer avec boutons -->
                <div class="mt-6 flex items-center justify-end space-x-3 border-t pt-4">
                    <button type="button"
                            wire:click="resetForm"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Annuler
                    </button>
                    <button type="submit"
                            wire:loading.attr="disabled"
                            wire:target="createStudent"
                            class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed min-w-[150px]">
                        <div wire:loading.flex wire:target="createStudent" class="items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Traitement...</span>
                        </div>
                        <span wire:loading.remove wire:target="createStudent">
                            {{ $userId ? 'Mettre à jour' : 'Créer' }}
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif