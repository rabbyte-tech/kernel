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
        Schema::create('mcp_entries', function (Blueprint $table) {
            $table->id();
            $table->ulid('public_id')->unique();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->string('type');
            $table->string('name');
            $table->string('class');
            $table->string('permission');
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();

            $table->unique(['package_id', 'type', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mcp_entries');
    }
};
