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
        Schema::create('daily_stats', function (Blueprint $table) {
            $table->id();

            // Link to your User model (assuming you have a users table)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // The date/time the entry was made (e.g., "Mon, Oct 28")
            $table->timestamp('date_time')->useCurrent();

            // Weight & Body Fat: Using decimal(4,1) allows for numbers like 78.5
            $table->decimal('body_weight', 4, 1)->default(0);
            $table->decimal('body_fat', 3, 1)->default(0);

            // Sleep Duration: Store as integer to represent minutes (60 would be 1 hour)
            $table->integer('sleep_duration')->default(0);

            // Mood: Integer representing the icon selected (e.g., 1=Sad, 4=Very Happy)
            $table->tinyInteger('mood')->unsigned()->default(2);

            // Toggles for Workout and Cardio
            $table->boolean('workout')->default(false);
            $table->boolean('cardio')->default(false);

            // Notes field
            $table->text('notes')->nullable();

            // Laravel standard timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_stats');
    }
};
