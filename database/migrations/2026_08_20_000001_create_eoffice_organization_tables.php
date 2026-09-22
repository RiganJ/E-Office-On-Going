<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('units', function (Blueprint $table) { $table->id(); $table->string('name'); $table->string('code')->unique(); $table->foreignId('parent_id')->nullable()->constrained('units')->nullOnDelete(); $table->boolean('is_active')->default(true); $table->timestamps(); });
        Schema::create('positions', function (Blueprint $table) { $table->id(); $table->string('name'); $table->string('code')->unique(); $table->timestamps(); });
        Schema::create('employees', function (Blueprint $table) { $table->id(); $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete(); $table->string('employee_number')->nullable()->unique(); $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete(); $table->timestamps(); });
        Schema::create('employee_positions', function (Blueprint $table) { $table->id(); $table->foreignId('employee_id')->constrained()->cascadeOnDelete(); $table->foreignId('position_id')->constrained()->cascadeOnDelete(); $table->foreignId('unit_id')->constrained()->cascadeOnDelete(); $table->boolean('is_primary')->default(false); $table->timestamps(); $table->unique(['employee_id','position_id','unit_id']); });
        Schema::create('roles', function (Blueprint $table) { $table->id(); $table->string('name')->unique(); $table->string('label'); $table->timestamps(); });
        Schema::create('permissions', function (Blueprint $table) { $table->id(); $table->string('name')->unique(); $table->string('label'); $table->timestamps(); });
        Schema::create('role_user', function (Blueprint $table) { $table->foreignId('role_id')->constrained()->cascadeOnDelete(); $table->foreignId('user_id')->constrained()->cascadeOnDelete(); $table->primary(['role_id','user_id']); });
        Schema::create('permission_role', function (Blueprint $table) { $table->foreignId('permission_id')->constrained()->cascadeOnDelete(); $table->foreignId('role_id')->constrained()->cascadeOnDelete(); $table->primary(['permission_id','role_id']); });
    }
    public function down(): void { Schema::dropIfExists('permission_role'); Schema::dropIfExists('role_user'); Schema::dropIfExists('permissions'); Schema::dropIfExists('roles'); Schema::dropIfExists('employee_positions'); Schema::dropIfExists('employees'); Schema::dropIfExists('positions'); Schema::dropIfExists('units'); }
};
