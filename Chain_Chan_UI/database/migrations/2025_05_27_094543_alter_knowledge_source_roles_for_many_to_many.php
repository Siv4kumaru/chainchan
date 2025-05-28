<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('knowledge_source_roles', function (Blueprint $table) {
            $table->id();
            $table->string('source_identifier');
            $table->unsignedBigInteger('role_id'); // Still good practice to keep it unsigned if roles.id is unsigned
            // $table->timestamps(); // Optional

            // ---- REMOVE FOREIGN KEY CONSTRAINT ----
            // $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');

            // Keep indexes for performance
            $table->index('source_identifier');
            $table->index('role_id'); // Index role_id for faster lookups/joins from app side
            $table->unique(['source_identifier', 'role_id']); // Composite unique key for many-to-many
        });
    }

    public function down()
    {
        Schema::dropIfExists('knowledge_source_roles');
    }
};