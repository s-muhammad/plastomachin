<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category', 20);
            $table->tinyInteger('priority')->default(2);
            $table->string('status', 20)->default('todo');
            $table->foreignId('keyword_id')->nullable()->constrained('seo_keywords')->nullOnDelete();
            $table->date('due_date')->nullable();
            $table->timestamp('done_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('category');
            $table->index('priority');
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_tasks');
    }
};
