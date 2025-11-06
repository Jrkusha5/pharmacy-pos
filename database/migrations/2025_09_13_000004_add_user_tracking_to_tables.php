<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add created_by to purchases
        Schema::table('purchases', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->after('id')->constrained('users')->onDelete('set null');
        });

        // Add created_by to sales
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->after('customer_id')->constrained('users')->onDelete('set null');
        });

        // Add created_by to stock_movements
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->after('id')->constrained('users')->onDelete('set null');
        });

        // Add created_by to customers
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->after('id')->constrained('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });
    }
};

