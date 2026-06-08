// 2026_05_27_075401_create_daily_stats_table.php
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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('log_date');
            $table->decimal('body_weight', 5, 1)->default(0);
            $table->decimal('body_fat', 3, 1)->default(0);
            $table->integer('sleep_duration')->default(0);
            $table->tinyInteger('mood')->unsigned()->default(2);
            $table->boolean('workout')->default(false);
            $table->boolean('cardio')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'log_date']);
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
