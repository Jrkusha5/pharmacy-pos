<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Purchases table: stores overall purchase transactions
        Schema::create('purchases', function (Blueprint $table) {
           $table->id();
            $table->uuid('cis_code')->unique();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->string('invoice_no')->unique();
            $table->date('purchased_at');
            $table->decimal('subtotal', 14, 4)->default(0);
            $table->decimal('total', 14, 4)->default(0);
            $table->decimal('paid_amount', 14, 4)->default(0);
            $table->decimal('due_amount', 14, 4)->default(0);
            $table->enum('payment_status', ['pending', 'partial', 'paid'])->default('pending');
            $table->enum('payment_method', ['cash', 'credit', 'bank_transfer', 'mobile_money'])->default('cash');
            $table->date('due_date')->nullable();
            $table->enum('status', ['draft', 'posted', 'void'])->default('posted');
            $table->text('note')->nullable();
            $table->timestamps();
        });

        // Purchase Items table: stores individual items in a purchase
        Schema::create('purchase_items', function (Blueprint $table) {
           $table->id();
            $table->foreignId('purchase_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->string('batch_no');
            $table->date('expires_at')->nullable();
            $table->integer('quantity');
            $table->decimal('unit_cost', 14, 4);
            $table->decimal('sell_price', 14, 4);
            $table->decimal('line_total', 14, 4);
            $table->timestamps(); // Important for joining with purchases table
        });
    }

    public function down(): void {
        Schema::dropIfExists('purchase_items');
        Schema::dropIfExists('purchases');
    }
};
