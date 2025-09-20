<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// 2025_01_01_000003_create_suppliers_table.php
return new class extends Migration {
  public function up(): void {
    Schema::create('suppliers', function (Blueprint $t) {
      $t->id();
      $t->string('name')->unique();
      $t->string('email')->nullable();
      $t->string('phone')->nullable();
      $t->string('address')->nullable();
      $t->boolean('active')->default(true);
      $t->timestamps();
    });
  }
  public function down(): void { Schema::dropIfExists('suppliers'); }
};

