<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Rôles
        $roles = [
            ['name' => 'admin', 'label' => 'Administrateur'],
            ['name' => 'teacher', 'label' => 'Enseignant'],
            ['name' => 'student', 'label' => 'Étudiant'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role['name']], ['label' => $role['label']]);
        }

        // Permissions
        $permissions = [
            ['name' => 'manage_users', 'label' => 'Gérer les utilisateurs'],
            ['name' => 'manage_categories', 'label' => 'Gérer les catégories'],
            ['name' => 'upload_documents', 'label' => 'Téléverser des documents'],
            ['name' => 'download_documents', 'label' => 'Télécharger des documents'],
            ['name' => 'view_documents', 'label' => 'Voir les documents'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission['name']], ['label' => $permission['label']]);
        }

        // Associer des permissions aux rôles
        Role::findByName('admin')->syncPermissions(Permission::all());
        Role::findByName('teacher')->syncPermissions([
            'upload_documents',
            'view_documents',
        ]);
        Role::findByName('student')->syncPermissions([
            'download_documents',
            'view_documents',
        ]);
    }
}
