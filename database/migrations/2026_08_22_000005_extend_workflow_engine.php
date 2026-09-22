<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('workflow_approvals', function (Blueprint $table) {
            $table->foreignId('approver_position_id')->nullable()->after('approver_user_id')->constrained('positions')->nullOnDelete();
            $table->unsignedInteger('cycle')->default(1)->after('workflow_step_id');
            $table->index(['workflow_instance_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('workflow_approvals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approver_position_id');
            $table->dropIndex(['workflow_instance_id', 'status']);
            $table->dropColumn('cycle');
        });
    }
};
