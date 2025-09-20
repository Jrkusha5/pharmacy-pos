<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// 2025_01_01_000002_create_units_table.php
return new class extends Migration {
  public function up(): void {
    Schema::create('units', function (Blueprint $t) {
      $t->id();
      $t->string('name');        // e.g. Tablet, Bottle, Box
      $t->string('abbreviation'); // e.g. tab, btl, box
      $t->timestamps();
      $t->unique(['name','abbreviation']);
    });
  }
  public function down(): void { Schema::dropIfExists('units'); }
};
