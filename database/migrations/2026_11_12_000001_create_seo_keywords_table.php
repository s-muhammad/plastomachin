<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_keywords', function (Blueprint $table) {
            $table->id();
            $table->string('keyword', 191);
            $table->string('role', 20);
            $table->string('target_page')->nullable();
            $table->string('target_url')->nullable();
            $table->string('page_type', 20);
            $table->string('intent', 20);
            $table->tinyInteger('priority')->default(2);
            $table->unsignedInteger('volume')->nullable();
            $table->string('competition', 10)->nullable();
            $table->unsignedSmallInteger('position')->nullable();
            $table->string('status', 20)->default('planned');
            $table->string('content_title')->nullable();
            $table->text('secondary_keywords')->nullable();
            $table->text('internal_links')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('priority');
            $table->index('page_type');
            $table->index('target_url');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_keywords');
    }
};
