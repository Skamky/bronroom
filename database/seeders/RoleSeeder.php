<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'id' => Role::ID_USER,
                'code' => 'user',
                'label' => 'Пользователь',
            ],
            [
               'id' => Role::ID_ADMIN,
                'code' => 'admin',
                'label' => 'Администратор',
            ],
        ];
    //    Role::query()->truncate();
        Role::query()->upsert($roles,            ['code']);

//        foreach ($roles as $role) {
//            Role::query()->updateOrCreate(['code' => $role['code']], $role);
//        }
    }
}
