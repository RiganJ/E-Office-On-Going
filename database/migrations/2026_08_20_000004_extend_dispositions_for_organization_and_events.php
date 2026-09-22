<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('dispositions', function (Blueprint $table) {
            $table->foreignId('from_position_id')->nullable()->after('from_user_id')->constrained('positions')->nullOnDelete();
            $table->foreignId('from_unit_id')->nullable()->after('from_position_id')->constrained('units')->nullOnDelete();
            $table->foreignId('to_position_id')->nullable()->after('to_user_id')->constrained('positions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('dispositions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('from_position_id');
            $table->dropConstrainedForeignId('from_unit_id');
            $table->dropConstrainedForeignId('to_position_id');
        });
    }
};
