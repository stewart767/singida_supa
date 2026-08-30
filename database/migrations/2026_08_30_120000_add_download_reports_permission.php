<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Permission;
use App\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        $permission = Permission::firstOrCreate(
            ['name' => 'download_reports'],
            [
                'display_name' => 'Download Reports',
                'group' => 'system'
            ]
        );

        // Assign to super_admin, registrar, finance_officer roles if they exist
        $roles = Role::whereIn('name', ['super_admin', 'registrar', 'finance_officer'])->get();
        foreach ($roles as $role) {
            $role->permissions()->syncWithoutDetaching([$permission->id]);
        }
    }

    public function down(): void
    {
        $permission = Permission::where('name', 'download_reports')->first();
        if ($permission) {
            $permission->roles()->detach();
            $permission->delete();
        }
    }
};
