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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            
            $table->string('type'); 
            $table->decimal('price', 10, 2)->default(0.00);
            $table->unsignedInteger('quantity')->default(0);
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');

            $table->index('event_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
