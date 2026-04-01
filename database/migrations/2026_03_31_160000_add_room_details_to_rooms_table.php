<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('location')->nullable()->after('description');
            $table->unsignedSmallInteger('size_sqm')->nullable()->after('location');
            $table->json('amenities')->nullable()->after('size_sqm');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['location', 'size_sqm', 'amenities']);
        });
    }
};
