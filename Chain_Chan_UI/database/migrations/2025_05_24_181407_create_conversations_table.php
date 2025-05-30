<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id(); // Server-side auto-incrementing ID
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('client_conversation_id')->unique(); // The ID generated by JS
            $table->string('title')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};