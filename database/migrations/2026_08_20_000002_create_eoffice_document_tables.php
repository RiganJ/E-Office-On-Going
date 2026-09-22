<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
 public function up(): void {
  Schema::create('document_types', function(Blueprint $t){$t->id();$t->string('name');$t->string('code')->unique();$t->timestamps();});
  Schema::create('documents', function(Blueprint $t){$t->id();$t->uuid('uuid')->unique();$t->foreignId('document_type_id')->constrained();$t->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();$t->foreignId('creator_id')->constrained('users');$t->string('number')->nullable()->unique();$t->string('subject');$t->text('content')->nullable();$t->string('status')->default('DRAFT')->index();$t->date('document_date')->nullable();$t->timestamp('deadline')->nullable();$t->string('verification_token', 64)->nullable()->unique();$t->string('file_hash',64)->nullable();$t->timestamps();$t->softDeletes();});
  Schema::create('document_files',function(Blueprint $t){$t->id();$t->foreignId('document_id')->constrained()->cascadeOnDelete();$t->string('original_filename');$t->string('stored_filename');$t->string('disk');$t->string('path');$t->string('mime_type');$t->unsignedBigInteger('size');$t->string('checksum',64);$t->foreignId('uploaded_by')->constrained('users');$t->timestamps();});
  Schema::create('incoming_letters',function(Blueprint $t){$t->id();$t->foreignId('document_id')->unique()->constrained()->cascadeOnDelete();$t->string('agenda_number')->unique();$t->string('sender');$t->string('sender_institution')->nullable();$t->string('letter_number')->nullable();$t->date('letter_date')->nullable();$t->date('received_date');$t->string('nature')->default('BIASA');$t->string('classification')->nullable();$t->timestamps();});
  Schema::create('dispositions',function(Blueprint $t){$t->id();$t->foreignId('document_id')->constrained()->cascadeOnDelete();$t->foreignId('from_user_id')->constrained('users');$t->foreignId('to_user_id')->nullable()->constrained('users')->nullOnDelete();$t->foreignId('to_unit_id')->nullable()->constrained('units')->nullOnDelete();$t->text('instruction');$t->text('notes')->nullable();$t->string('priority')->default('NORMAL');$t->timestamp('deadline')->nullable();$t->string('status')->default('UNREAD');$t->timestamp('read_at')->nullable();$t->timestamp('processed_at')->nullable();$t->timestamp('completed_at')->nullable();$t->timestamps();});
  Schema::create('activity_logs',function(Blueprint $t){$t->id();$t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();$t->morphs('subject');$t->string('action');$t->json('old_values')->nullable();$t->json('new_values')->nullable();$t->ipAddress('ip_address')->nullable();$t->text('user_agent')->nullable();$t->timestamps();});
 }
 public function down(): void { foreach(['activity_logs','dispositions','incoming_letters','document_files','documents','document_types'] as $x)Schema::dropIfExists($x); }
};
