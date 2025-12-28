<div class="space-y-4">
    {{-- MODAL --}}
    @if($showUserModal)
        <div class="fixed inset-0 z-50">
            <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" wire:click="resetForm"></div>

            <div class="relative mx-auto flex min-h-screen max-w-2xl items-center justify-center p-4">
                <div class="w-full overflow-hidden rounded-2xl bg-white shadow-xl ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-800">

                    {{-- Header --}}
                    <div class="flex items-start justify-between gap-4 border-b border-gray-200 px-6 py-5 dark:border-gray-800">
                        <div class="min-w-0">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $userId ? 'Modifier l’enseignant' : 'Nouvel enseignant' }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Nom, email et niveau(x) d’enseignement uniquement. Le profil sera complété plus tard.
                            </p>
                        </div>

                        <button type="button"
                                wire:click="resetForm"
                                class="inline-flex h-10 w-10 items-center justify-center rounded-xl
                                       border border-gray-200 bg-white text-gray-600 hover:bg-gray-50
                                       dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 transition"
                                aria-label="Fermer">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="createTeacher">
                        <div class="px-6 py-5 space-y-5">

                            {{-- Nom --}}
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Nom complet <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       wire:model.defer="name"
                                       class="h-11 w-full rounded-xl border border-gray-300 bg-white px-3 text-sm text-gray-900
                                              focus:border-indigo-500 focus:ring-indigo-500
                                              dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                       placeholder="Ex: Dr. Rakoto Jean">
                                @error('name')
                                    <p class="mt-1 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email"
                                       wire:model.defer="email"
                                       class="h-11 w-full rounded-xl border border-gray-300 bg-white px-3 text-sm text-gray-900
                                              focus:border-indigo-500 focus:ring-indigo-500
                                              dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                       placeholder="ex: prof@umg.mg">
                                @error('email')
                                    <p class="mt-1 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Niveaux --}}
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Niveau(x) d’enseignement <span class="text-red-500">*</span>
                                </label>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach($niveaux as $niveau)
                                        <label class="flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-3 py-2
                                                      hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:hover:bg-gray-800 cursor-pointer transition">
                                            <input type="checkbox"
                                                   wire:model.defer="selectedTeacherNiveaux"
                                                   value="{{ $niveau->id }}"
                                                   class="h-4 w-4 rounded text-indigo-600 focus:ring-indigo-500">
                                            <span class="text-sm text-gray-700 dark:text-gray-200">{{ $niveau->name }}</span>
                                        </label>
                                    @endforeach
                                </div>

                                @error('selectedTeacherNiveaux')
                                    <p class="mt-1 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Statut --}}
                            <div class="flex items-center justify-between rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-800/40">
                                <div>
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">Compte actif</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Désactivez pour bloquer l’accès.</div>
                                </div>

                                <button type="button"
                                        wire:click="$toggle('status')"
                                        class="relative inline-flex h-6 w-11 items-center rounded-full transition
                                        {{ $status ? 'bg-green-500 dark:bg-green-600' : 'bg-gray-200 dark:bg-gray-600' }}">
                                    <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition
                                        {{ $status ? 'translate-x-5' : 'translate-x-1' }}"></span>
                                </button>
                            </div>

                            <div class="rounded-xl border border-gray-200 bg-white p-4 text-sm text-gray-600 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                                À la création : un email est envoyé à l’enseignant pour définir son mot de passe.
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="border-t border-gray-200 bg-white px-6 py-4 dark:border-gray-800 dark:bg-gray-900">
                            <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                                <button type="button"
                                        wire:click="resetForm"
                                        class="inline-flex h-11 items-center justify-center rounded-xl
                                               border border-gray-200 bg-white px-4 text-sm font-medium text-gray-700 hover:bg-gray-50
                                               dark:border-gray-800 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800 transition">
                                    Annuler
                                </button>

                                <button type="submit"
                                        wire:loading.attr="disabled"
                                        wire:target="createTeacher"
                                        class="inline-flex h-11 items-center justify-center rounded-xl
                                               bg-indigo-600 px-4 text-sm font-semibold text-white hover:bg-indigo-700
                                               disabled:opacity-60 disabled:cursor-not-allowed transition">
                                    <span wire:loading.remove wire:target="createTeacher">
                                        {{ $userId ? 'Enregistrer' : 'Créer le compte' }}
                                    </span>
                                    <span wire:loading wire:target="createTeacher">Traitement...</span>
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    @endif
</div>
