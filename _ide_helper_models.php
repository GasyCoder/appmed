<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $email
 * @property int $is_registered
 * @property string|null $verification_token
 * @property string|null $token_expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizedEmail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizedEmail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizedEmail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizedEmail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizedEmail whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizedEmail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizedEmail whereIsRegistered($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizedEmail whereTokenExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizedEmail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizedEmail whereVerificationToken($value)
 */
	class AuthorizedEmail extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string $file_path
 * @property string|null $original_filename
 * @property string|null $original_extension
 * @property string|null $converted_from
 * @property \Illuminate\Support\Carbon|null $converted_at
 * @property string|null $protected_path
 * @property string $file_type
 * @property int $file_size
 * @property bool $is_actif
 * @property int $download_count
 * @property int $view_count
 * @property int $niveau_id
 * @property int $semestre_id
 * @property int $parcour_id
 * @property int $uploaded_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read string $conversion_status
 * @property-read mixed $extension
 * @property-read mixed $formatted_size
 * @property-read \App\Models\Niveau $niveau
 * @property-read \App\Models\Parcour $parcour
 * @property-read \App\Models\Semestre $semestre
 * @property-read \App\Models\User $teacher
 * @property-read \App\Models\User $uploader
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DocumentView> $views
 * @property-read int|null $views_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document byOriginalFormat($format)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document converted()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereConvertedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereConvertedFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereDownloadCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereFileType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereIsActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereNiveauId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereOriginalExtension($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereOriginalFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereParcourId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereProtectedPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereSemestreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereUploadedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereViewCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document withoutTrashed()
 */
	class Document extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $document_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Document $document
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentView newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentView newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentView query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentView whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentView whereDocumentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentView whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentView whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentView whereUserId($value)
 */
	class DocumentView extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $niveau_id
 * @property int $parcour_id
 * @property int $teacher_id
 * @property int $semestre_id
 * @property int $programme_id
 * @property string $color
 * @property int $weekday
 * @property \Illuminate\Support\Carbon $start_time
 * @property \Illuminate\Support\Carbon $end_time
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property string $salle
 * @property string|null $type_cours
 * @property string|null $description
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read int $duration
 * @property-read string $type_cours_name
 * @property-read string $weekday_name
 * @property-read \App\Models\Niveau $niveau
 * @property-read \App\Models\Parcour $parcour
 * @property-read \App\Models\Programme $programme
 * @property-read \App\Models\Semestre $semestre
 * @property-read \App\Models\User $teacher
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson calendarByRole()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson forNiveau($niveauId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson forParcour($parcourId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson forTeacher($teacherId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson whereNiveauId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson whereParcourId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson whereProgrammeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson whereSalle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson whereSemestreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson whereTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson whereTypeCours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson whereWeekday($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lesson withoutTrashed()
 */
	class Lesson extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $sigle
 * @property string $name
 * @property bool $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Document> $documents
 * @property-read int|null $documents_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Semestre> $semestres
 * @property-read int|null $semestres_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $teachers
 * @property-read int|null $teachers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Niveau newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Niveau newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Niveau query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Niveau whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Niveau whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Niveau whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Niveau whereSigle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Niveau whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Niveau whereUpdatedAt($value)
 */
	class Niveau extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $sigle
 * @property string $name
 * @property bool $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Document> $documents
 * @property-read int|null $documents_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $teachers
 * @property-read int|null $teachers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parcour newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parcour newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parcour query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parcour whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parcour whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parcour whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parcour whereSigle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parcour whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parcour whereUpdatedAt($value)
 */
	class Parcour extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string|null $sexe
 * @property string|null $grade
 * @property string|null $telephone
 * @property string|null $adresse
 * @property string|null $ville
 * @property string|null $departement
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profil newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profil newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profil onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profil query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profil whereAdresse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profil whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profil whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profil whereDepartement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profil whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profil whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profil whereSexe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profil whereTelephone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profil whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profil whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profil whereVille($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profil withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profil withoutTrashed()
 */
	class Profil extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $type
 * @property string $code
 * @property string $name
 * @property int $order
 * @property int|null $parent_id
 * @property int $semestre_id
 * @property int $niveau_id
 * @property int $parcour_id
 * @property bool $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Programme> $elements
 * @property-read int|null $elements_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $enseignants
 * @property-read int|null $enseignants_count
 * @property-read int $annee
 * @property-read int $semestre_annee
 * @property-read \App\Models\Niveau $niveau
 * @property-read \App\Models\Parcour $parcour
 * @property-read Programme|null $parent
 * @property-read \App\Models\Semestre $semestre
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme byAnnee($annee)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme byNiveau($niveauId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme byParcours($parcourId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme bySemestre($semestreId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme ecs()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme ues()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme whereNiveauId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme whereParcourId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme whereSemestreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme withEnseignants()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme withoutEnseignants()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme withoutTrashed()
 */
	class Programme extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property int $niveau_id
 * @property bool $is_active
 * @property bool $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Document> $documents
 * @property-read int|null $documents_count
 * @property-read \App\Models\Niveau $niveau
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Semestre newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Semestre newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Semestre query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Semestre whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Semestre whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Semestre whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Semestre whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Semestre whereNiveauId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Semestre whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Semestre whereUpdatedAt($value)
 */
	class Semestre extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property bool $status
 * @property int|null $niveau_id
 * @property int|null $parcour_id
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property int|null $current_team_id
 * @property string|null $profile_photo_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Document> $documents
 * @property-read int|null $documents_count
 * @property-read array $charge_horaire
 * @property-read string $full_name_with_grade
 * @property-read int|null $programmes_count
 * @property-read array $teacher_stats
 * @property-read \App\Models\Niveau|null $niveau
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Niveau> $niveaux
 * @property-read int|null $niveaux_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Parcour|null $parcour
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Parcour> $parcours
 * @property-read int|null $parcours_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \App\Models\Profil|null $profil
 * @property-read string $profile_photo_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Programme> $programmes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Programme> $programmesResponsable
 * @property-read int|null $programmes_responsable_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Niveau> $teacherNiveaux
 * @property-read int|null $teacher_niveaux_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Parcour> $teacherParcours
 * @property-read int|null $teacher_parcours_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $teachers
 * @property-read int|null $teachers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User activeStudents()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User activeTeachers()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User byRole(string $role)
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User teachingProgramme(int $programmeId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCurrentTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNiveauId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereParcourId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfilePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 */
	class User extends \Eloquent {}
}

