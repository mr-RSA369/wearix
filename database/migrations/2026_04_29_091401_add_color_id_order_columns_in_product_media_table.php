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
        Schema::table('product_media', function (Blueprint $table) {
            if (!Schema::hasColumn('product_media', 'color_id')) {
                $table->foreignId('color_id')->nullable()->after('path')->constrained('product_colors')->nullOnDelete();
            }

            if (!Schema::hasColumn('product_media', 'order')) {
                $table->integer('order')->default(0)->after('color_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_media', function (Blueprint $table) {
            if (Schema::hasColumn('product_media', 'color_id')) {
                $table->dropForeign(['color_id']);
                $table->dropColumn('color_id');
            }

            if (Schema::hasColumn('product_media', 'order')) {
                $table->dropColumn('order');
            }
        });
    }
};
