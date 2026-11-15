<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_click_events', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20);
            $table->string('page_url', 2048);
            $table->string('link_url', 2048)->nullable();
            $table->string('referrer', 2048)->nullable();
            $table->string('utm_source', 255)->nullable();
            $table->string('utm_medium', 255)->nullable();
            $table->string('utm_campaign', 255)->nullable();
            $table->timestamps();

            $table->index(['type', 'created_at']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_click_events');
    }
};
