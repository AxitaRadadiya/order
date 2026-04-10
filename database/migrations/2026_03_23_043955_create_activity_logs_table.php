<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            // Who did it (nullable for system/guest actions)
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_name')->nullable();        // snapshot of name at time of action

            // What they did
            $table->string('action');                       // login|logout|created|updated|deleted
            $table->string('description')->nullable();      // human-readable summary

            // What was affected
            $table->string('model_type')->nullable();       // e.g. App\Models\User
            $table->unsignedBigInteger('model_id')->nullable(); // affected record ID
            $table->string('model_label')->nullable();      // e.g. "User: John Doe"

            // Before / after for updates
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            // Context
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            // Indexes for fast filtering
            $table->index(['user_id', 'created_at']);
            $table->index(['model_type', 'model_id']);
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};