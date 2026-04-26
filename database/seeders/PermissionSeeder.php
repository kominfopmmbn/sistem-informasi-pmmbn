<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $guard = 'web';

        $names = [
            'articles.view',
            'articles.create',
            'articles.update',
            'articles.delete',
            'articles.other',
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'roles.view',
            'roles.create',
            'roles.update',
            'roles.delete',
        ];

        foreach ($names as $name) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => $guard],
            );
        }

        $administrator = Role::query()
            ->where('name', 'Administrator')
            ->where('guard_name', $guard)
            ->first();

        if ($administrator !== null) {
            $administrator->givePermissionTo($names);
        }
    }
}
