<?php

namespace Database\Seeders;

use App\Models\Inspector;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class InspectorSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $role = Role::findOrCreate('Inspectores', 'web');

            User::whereHas('roles', fn ($query) => $query->where('name', 'Inspectores'))
                ->each(function (User $user): void {
                    if (Inspector::where('user_id', $user->id)->exists()) {
                        return;
                    }
                    $inspector = Inspector::whereNull('user_id')->where('email', $user->email)->first();
                    if ($inspector !== null) {
                        $inspector->update(['user_id' => $user->id]);
                    } else {
                        Inspector::create([
                            'user_id' => $user->id, 'name' => $user->name,
                            'email' => $user->email, 'phone' => $user->phone,
                        ]);
                    }
                });

            $initialPassword = Hash::make('InsureTech2026!');
            $missing = max(0, 10 - Inspector::count());
            if ($missing > 0) {
                Inspector::factory()->count($missing)->state([
                    'user_id' => User::factory()->state(['password' => $initialPassword]),
                ])->create();
            }

            Inspector::orderBy('id')->each(function (Inspector $inspector) use ($role, $initialPassword): void {
                $user = $inspector->user;
                if ($user === null) {
                    $email = $inspector->email ?: 'inspector-'.$inspector->id.'@example.test';
                    $user = User::where('email', $email)->first();
                    if ($user !== null && Inspector::where('user_id', $user->id)->exists()) {
                        $email = 'inspector-'.$inspector->id.'@example.test';
                        $user = User::where('email', $email)->first();
                    }
                    if ($user === null) {
                        $user = new User([
                            'name' => $inspector->name, 'email' => $email, 'password' => $initialPassword,
                        ]);
                        $user->phone = $inspector->phone;
                        $user->save();
                    }
                    $inspector->update(['user_id' => $user->id]);
                }
                $user->assignRole($role);
            });
        });
    }
}
