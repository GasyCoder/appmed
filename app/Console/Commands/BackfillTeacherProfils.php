<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillTeacherProfils extends Command
{
    protected $signature = 'teachers:backfill-profils {--dry-run : Ne modifie rien, affiche seulement}';
    protected $description = 'Crée un profil pour chaque enseignant qui n’en a pas';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');

        $teachers = User::query()
            ->role('teacher')
            ->with('profil')
            ->get();

        $missing = $teachers->filter(fn ($u) => $u->profil === null);

        $this->info("Teachers total: {$teachers->count()}");
        $this->info("Missing profils: {$missing->count()}");

        if ($missing->isEmpty()) {
            $this->info("Rien à faire.");
            return self::SUCCESS;
        }

        if ($dry) {
            $this->warn("Dry-run actif. Aucun insert.");
            $missing->take(50)->each(fn ($u) => $this->line("- user_id={$u->id} {$u->email}"));
            if ($missing->count() > 50) $this->line("... (liste tronquée)");
            return self::SUCCESS;
        }

        DB::transaction(function () use ($missing) {
            foreach ($missing as $user) {
                $user->profil()->create([
                    'telephone'   => null,
                    'sexe'        => null,
                    'grade'       => null,
                    'adresse'     => null,
                    'ville'       => null,
                    'departement' => null,
                ]);
            }
        });

        $this->info("Profils créés: {$missing->count()}");
        return self::SUCCESS;
    }
}
