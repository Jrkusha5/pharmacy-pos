<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->string('barcode')->nullable()->unique()->after('sku');
            $table->decimal('selling_price', 14, 4)->nullable()->after('default_sell_price');
            $table->integer('min_stock_level')->default(0)->after('reorder_level');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['barcode', 'selling_price', 'min_stock_level']);
        });
    }
};

