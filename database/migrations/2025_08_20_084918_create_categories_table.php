<?php

// 2025_01_01_000001_create_categories_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('categories', function (Blueprint $t) {
      $t->id();
      $t->string('name')->unique();
      $t->string('code')->nullable()->unique();
      $t->text('description')->nullable();
      $t->timestamps();
    });
  }
  public function down(): void { Schema::dropIfExists('categories'); }
};

