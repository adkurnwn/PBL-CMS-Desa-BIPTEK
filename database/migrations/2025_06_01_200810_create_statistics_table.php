<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('statistics', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'content_view' or 'feature_visit'
            $table->string('category'); // 'news', 'gallery', 'event', 'homepage', 'profile', etc.
            $table->unsignedBigInteger('content_id')->nullable(); // For content views
            $table->string('feature_name')->nullable(); // For feature visits
            $table->string('ip_hash'); // Hashed IP for privacy
            $table->string('user_agent_hash')->nullable(); // Hashed user agent
            $table->string('session_id')->nullable(); // Browser session ID
            $table->date('date'); // For easy date-based queries
            $table->time('time'); // For hourly analytics if needed
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['type', 'category', 'date']);
            $table->index(['content_id', 'date']);
            $table->index(['feature_name', 'date']);
            $table->index(['ip_hash', 'date']);
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statistics');
    }
};
