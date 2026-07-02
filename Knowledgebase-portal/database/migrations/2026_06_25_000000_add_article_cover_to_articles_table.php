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
        if (Schema::hasColumn('articles', 'article_cover')) {
            return;
        }

        Schema::table('articles', function (Blueprint $table) {
            $table->string('article_cover')->default('#24a1c7')->nullable()->after('visibility');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('articles', 'article_cover')) {
            return;
        }

        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('article_cover');
        });
    }
};
