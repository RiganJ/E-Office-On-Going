<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void {
  Schema::table('document_metadata',function(Blueprint $table){
   if(!Schema::hasColumn('document_metadata','document_id'))$table->foreignId('document_id')->nullable()->unique()->constrained()->cascadeOnDelete();
   if(!Schema::hasColumn('document_metadata','data'))$table->json('data')->nullable();
   if(!Schema::hasColumn('document_metadata','created_at'))$table->timestamps();
  });
 }
 public function down():void {}
};
