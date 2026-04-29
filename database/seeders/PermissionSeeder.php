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
            'org_regions.view',
            'org_regions.create',
            'org_regions.update',
            'org_regions.delete',
            'colleges.view',
            'colleges.create',
            'colleges.update',
            'colleges.delete',
            'provinces.view',
            'provinces.create',
            'provinces.update',
            'provinces.delete',
            'cities.view',
            'cities.create',
            'cities.update',
            'cities.delete',
            'districts.view',
            'districts.create',
            'districts.update',
            'districts.delete',
            'villages.view',
            'villages.create',
            'villages.update',
            'villages.delete',
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
