<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('roles')->truncate();
        Schema::enableForeignKeyConstraints();

        $roles = [
            ['name' => 'Super Administrador', 'slug' => 'super-administrador'],
            ['name' => 'Administrador', 'slug' => 'administrador'],
            ['name' => 'Gerente', 'slug' => 'gerente'],
            ['name' => 'Supervisor', 'slug' => 'supervisor'],
            ['name' => 'Usuario', 'slug' => 'usuario'],
        ];

        collect($roles)->each(function ($role) {
            Role::create($role);
        });
    }
}
