<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            $allowedRoles = [
                'admin' => 'Admin - Menerima dan menginput data operasional.',
                'owner' => 'Owner - Monitoring dan melihat laporan seluruh domain.',
                'manager' => 'Manager - Pemeriksaan, persetujuan, dan pengendalian eksekusi.',
            ];

            $targetRoles = [];
            foreach ($allowedRoles as $name => $description) {
                $targetRoles[$name] = Role::firstOrCreate(
                    ['name' => $name],
                    ['description' => $description],
                );
            }

            $legacyRoles = Role::whereNotIn('name', array_keys($allowedRoles))->get();
            foreach ($legacyRoles as $legacyRole) {
                $normalizedName = strtolower(str_replace([' ', '-'], '_', trim($legacyRole->name)));
                $targetName = match ($normalizedName) {
                    'super_admin', 'superadmin' => 'owner',
                    'management' => 'manager',
                    default => 'admin',
                };

                User::where('role_id', $legacyRole->id)
                    ->update(['role_id' => $targetRoles[$targetName]->id]);

                $legacyRole->permissions()->detach();
                $legacyRole->delete();
            }
        });
    }

    public function down(): void
    {
        // Legacy roles cannot be restored safely because their original
        // user assignments are intentionally normalized in the up migration.
    }
};
