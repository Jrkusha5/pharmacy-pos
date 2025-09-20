<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Sales table
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique();   // SALE-2025-00001
            $table->dateTime('sold_at');
            $table->enum('status', ['draft', 'completed', 'reversed', 'void'])->default('completed');
            $table->decimal('subtotal', 14, 4)->default(0);
            $table->decimal('total', 14, 4)->default(0);
            $table->text('note')->nullable();
            $table->timestamps();
        });

        // Sale Items table
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->foreignId('purchase_item_id')->nullable()->constrained('purchase_items')->onDelete('set null');
            $table->integer('quantity');
            $table->decimal('unit_price', 14, 4); // copied from purchase_items.sell_price
            $table->decimal('line_total', 14, 4);
            $table->date('expires_at')->nullable(); // snapshot from batch
            $table->json('meta')->nullable(); // allocations if FEFO splits across multiple batches
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
    }
};
