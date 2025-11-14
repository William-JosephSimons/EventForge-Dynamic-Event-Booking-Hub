<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            // Foreign key to the attendee
            $table->foreignId('user_id')->constrained();
            // Foreign key to the event
            $table->foreignId('event_id')->constrained();
            $table->timestamps();

            // Add a unique constraint for the combination of user and 
            // event to prevent a user from booking the same event twice
            $table->unique(['user_id', 'event_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
