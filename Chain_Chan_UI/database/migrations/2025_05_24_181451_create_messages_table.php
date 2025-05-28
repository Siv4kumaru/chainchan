<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            $table->enum('sender', ['user', 'ai']);
            $table->text('text');
            $table->timestamp('client_timestamp')->nullable(); // Timestamp from JS
            $table->timestamps(); // Server-side timestamps
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};