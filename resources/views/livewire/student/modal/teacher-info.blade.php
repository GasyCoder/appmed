<!-- Modal du profil détaillé -->
@if($showTeacherModal && $selectedTeacher)
<div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity z-50">
   <div class="fixed inset-0 z-10 overflow-y-auto">
       <div class="flex min-h-full items-center justify-center p-4">
           <div class="relative transform overflow-hidden rounded-xl bg-white dark:bg-gray-800 shadow-xl transition-all w-full max-w-md">
               <!-- En-tête du modal avec bannière -->
               <div class="relative h-32 bg-gradient-to-r from-indigo-500 to-purple-600 dark:from-indigo-600 dark:to-purple-700">
                   <button wire:click="closeTeacherModal"
                           class="absolute right-3 top-3 text-white hover:text-gray-200 focus:outline-none">
                       <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                       </svg>
                   </button>
                   <div class="absolute -bottom-10 left-1/2 transform -translate-x-1/2">
                       <img class="h-20 w-20 rounded-full border-4 border-white dark:border-gray-800 object-cover shadow-lg"
                            src="{{ $selectedTeacher->profile_photo_url }}"
                            alt="{{ $selectedTeacher->name }}">
                   </div>
               </div>

               <!-- Contenu du modal -->
               <div class="px-4 pt-14 pb-4">
                   <div class="text-center mb-4">
                       <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                           {{ $selectedTeacher->profil->grade. '.' .$selectedTeacher->name }}
                       </h3>
                       <a href="mailto:{{ $selectedTeacher->email }}"
                          class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                           {{ $selectedTeacher->email }}
                       </a>
                   </div>

                   @if($selectedTeacher->profil)
                   <div class="space-y-3 mb-4">
                       @if($selectedTeacher->profil->departement)
                       <div class="flex items-center text-sm">
                           <svg class="h-4 w-4 text-gray-400 dark:text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                           </svg>
                           <span class="text-gray-600 dark:text-gray-300">{{ $selectedTeacher->profil->departement }}</span>
                       </div>
                       @endif

                       @if($selectedTeacher->profil->telephone)
                       <div class="flex items-center text-sm">
                           <svg class="h-4 w-4 text-gray-400 dark:text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                           </svg>
                           <span class="text-gray-600 dark:text-gray-300">{{ $selectedTeacher->profil->telephone }}</span>
                       </div>
                       @endif
                   </div>
                   @endif

                   <!-- Statistiques -->
                   <div class="grid grid-cols-3 gap-4 py-3 border-t border-b border-gray-100 dark:border-gray-700">
                       <div class="text-center">
                           <span class="block text-lg font-semibold text-indigo-600 dark:text-indigo-400">
                               {{ $selectedTeacher->teacherNiveaux->count() }}
                           </span>
                           <span class="text-xs text-gray-500 dark:text-gray-400">Niveaux</span>
                       </div>
                       <div class="text-center">
                           <span class="block text-lg font-semibold text-indigo-600 dark:text-indigo-400">
                               {{ $selectedTeacher->documents_count ?? 0 }}
                           </span>
                           <span class="text-xs text-gray-500 dark:text-gray-400">Documents</span>
                       </div>
                       <div class="text-center">
                           <span class="block text-lg font-semibold text-indigo-600 dark:text-indigo-400">
                               {{ $selectedTeacher->teacherParcours->count() }}
                           </span>
                           <span class="text-xs text-gray-500 dark:text-gray-400">Parcours</span>
                       </div>
                   </div>

                   <!-- Bouton Contacter -->
                   <div class="mt-4 space-y-3">
                       <a href="mailto:{{ $selectedTeacher->email }}"
                          class="inline-flex items-center justify-center w-full px-4 py-2 
                                 bg-indigo-600 dark:bg-indigo-500 text-white font-semibold rounded-lg 
                                 shadow-md hover:bg-indigo-700 dark:hover:bg-indigo-600 
                                 hover:shadow-lg focus:outline-none focus:ring-2 
                                 focus:ring-indigo-500 dark:focus:ring-indigo-400 
                                 focus:ring-offset-2 dark:focus:ring-offset-gray-800 
                                 transition duration-300 ease-in-out">
                           <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                   d="M16 12H8m8 0H8m8-6H8m8 12H8m-4 0a2 2 0 01-2-2V6a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H4z"/>
                           </svg>
                           Contacter
                       </a>
                   </div>
               </div>
           </div>
       </div>
   </div>
</div>
@endif