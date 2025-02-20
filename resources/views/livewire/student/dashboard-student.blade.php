<div>
    <div class="p-4 space-y-6">

        {{-- En-tête avec informations de l'étudiant et date/heure --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 dark:from-indigo-800 dark:to-indigo-900 p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="space-y-2">
                        <h2 class="text-2xl font-bold text-white">Bienvenue, {{ auth()->user()->name }}</h2>
                        <div class="flex items-center space-x-2 text-indigo-100">
                            <span>{{ $student->niveau->sigle ?? 'Niveau non défini' }}</span>
                            @if($student->parcour)
                                <span>•</span>
                                <span>{{ $student->parcour->sigle }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="mt-4 md:mt-0 text-right">
                        <div class="text-white text-sm">
                            <div class="flex items-center justify-end space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>{{ $currentDateTime->format('d/m/Y H:i') }}</span>
                            </div>
                            @if($lastLogin)
                                <div class="text-indigo-200 text-xs mt-1">
                                    Dernière activité: {{ $lastLogin->diffForHumans() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sessions et activités récentes --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    Sessions récentes
                </h3>
            </div>
            <div class="p-4">
                @if($userSessions->isEmpty())
                    <p class="text-gray-500 dark:text-gray-400">Aucune session récente</p>
                @else
                    <div class="space-y-4">
                        @foreach($userSessions as $session)
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        Session depuis {{ $session->ip_address }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $session->last_activity->diffForHumans() }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ Str::limit($session->user_agent, 100) }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- Cours d'aujourd'hui --}}
            <div class="col-span-2 space-y-6">
                {{-- Cours d'aujourd'hui --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                Cours d'aujourd'hui ({{ $currentDayName }})
                            </h3>
                        </div>
                    </div>
                    <div class="p-4">
                        @if($todayLessons->isEmpty())
                            <p class="text-gray-500 dark:text-gray-400 text-center">
                                Aucun cours prévu aujourd'hui
                            </p>
                        @else
                            <div class="space-y-4">
                                @foreach($todayLessons as $lesson)
                                    <x-lesson-card :lesson="$lesson" />
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Prochains cours --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            Prochains cours
                        </h3>
                    </div>
                    <div class="p-4">
                        @if($upcomingLessons->isEmpty())
                            <p class="text-gray-500 dark:text-gray-400 text-center">
                                Aucun cours programmé
                            </p>
                        @else
                            <div class="space-y-4">
                                @foreach($upcomingLessons as $lesson)
                                    <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <span class="font-medium text-gray-900 dark:text-white">
                                                    {{ $lesson->type_cours }}
                                                    <span class="text-xs text-gray-500">
                                                        ({{ $lesson->getTypeCoursNameAttribute() }})
                                                    </span>
                                                </span>
                                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $lesson->day_name }} • {{ $lesson->start_time->format('H:i') }} - {{ $lesson->end_time->format('H:i') }}
                                                </span>
                                            </div>
                                            <div class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                                {{ optional($lesson->teacher)->getFullNameWithGradeAttribute() ?? 'Enseignant non assigné' }}
                                            </div>
                                            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                Salle: {{ $lesson->salle }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Documents récents --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Documents récents</h3>
                </div>
                <div class="p-4">
                    @if($recentDocuments->isEmpty())
                        <p class="text-gray-500 dark:text-gray-400">Aucun document récent</p>
                    @else
                        <div class="space-y-4">
                            @foreach($recentDocuments as $document)
                                <div class="flex items-center space-x-3">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ $document->title }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            Par {{ $document->uploader->getFullNameWithGradeAttribute() }}
                                        </p>
                                    </div>
                                    <a href="{{ route('student.document')}}"
                                            class="flex-shrink-0 text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 4.5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7zm0 11a4 4 0 110-8 4 4 0 010 8z"/>
                                            </svg>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Enseignants --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Mes enseignants</h3>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($teachers as $teacher)
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <img class="h-10 w-10 rounded-full"
                                     src="{{ $teacher->profile_photo_url }}"
                                     alt="{{ $teacher->name }}">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $teacher->getFullNameWithGradeAttribute() }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $teacher->teacherNiveaux->pluck('name')->join(', ') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
