<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            $this->rebuildUsersTableForSqlite(['user', 'admin', 'super_admin']);

            return;
        }

        DB::statement("ALTER TABLE users MODIFY role ENUM('user', 'admin', 'super_admin') NOT NULL DEFAULT 'user'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("UPDATE users SET role = 'admin' WHERE role = 'super_admin'");

        if (DB::getDriverName() === 'sqlite') {
            $this->rebuildUsersTableForSqlite(['user', 'admin']);

            return;
        }

        DB::statement("ALTER TABLE users MODIFY role ENUM('user', 'admin') NOT NULL DEFAULT 'user'");
    }

    private function rebuildUsersTableForSqlite(array $roles): void
    {
        $quotedRoles = collect($roles)
            ->map(fn (string $role) => "'{$role}'")
            ->implode(', ');

        DB::statement('PRAGMA foreign_keys=OFF');
        DB::statement("
            CREATE TABLE users_temp (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                name VARCHAR NOT NULL,
                email VARCHAR NOT NULL UNIQUE,
                email_verified_at DATETIME NULL,
                password VARCHAR NOT NULL,
                two_factor_secret TEXT NULL,
                two_factor_recovery_codes TEXT NULL,
                two_factor_confirmed_at DATETIME NULL,
                remember_token VARCHAR(100) NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                role VARCHAR NOT NULL DEFAULT 'user' CHECK (role IN ({$quotedRoles}))
            )
        ");

        DB::statement('
            INSERT INTO users_temp (
                id, name, email, email_verified_at, password,
                two_factor_secret, two_factor_recovery_codes, two_factor_confirmed_at,
                remember_token, created_at, updated_at, role
            )
            SELECT
                id, name, email, email_verified_at, password,
                two_factor_secret, two_factor_recovery_codes, two_factor_confirmed_at,
                remember_token, created_at, updated_at, role
            FROM users
        ');

        DB::statement('DROP TABLE users');
        DB::statement('ALTER TABLE users_temp RENAME TO users');
        DB::statement('CREATE INDEX users_role_index ON users (role)');
        DB::statement('PRAGMA foreign_keys=ON');
    }
};

