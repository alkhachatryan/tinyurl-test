<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index('is_deleted');
            $table->index('is_top');
            $table->index(['price', 'is_top']);
            $table->index(['name', 'is_top']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_deleted']);
            $table->dropIndex(['is_top']);
            $table->dropIndex(['price', 'is_top']);
            $table->dropIndex(['name', 'is_top']);
        });
    }
};
