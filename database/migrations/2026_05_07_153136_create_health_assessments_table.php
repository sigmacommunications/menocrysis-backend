<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('health_assessments', function (Blueprint $table) {
            $table->id();

            // Step 1 - Personal Info
            $table->integer('age');
            $table->string('gender')->nullable();
            $table->string('menstrual_noticed_recently')->nullable();
            $table->string('menstrual_status')->nullable();

            // Step 2 - Physical Symptoms
            $table->string('physical_changes')->nullable();
            $table->string('hormonal_discomfort')->nullable();
            

            // Step 3 - Emotional & Cognitive
            $table->string('mood_swings')->nullable();
            $table->string('anxiety_levels')->nullable();
            $table->string('brain_fog')->nullable();
            $table->string('memory_changes')->nullable();
            $table->string('motivation_levels')->nullable();

            // Step 4 - Lifestyle & Functioning
            $table->string('sleep_quality')->nullable();
            $table->string('stress_levels')->nullable();
            $table->string('exercise_frequency')->nullable();
            $table->string('daily_energy_levels')->nullable();
            $table->string('work_home_impact')->nullable();

            // Step 5 - Overall Assessment
            $table->string('feel_compared_to_year_ago')->nullable();
            $table->string('symptoms_feel_hormonal')->nullable();
            $table->string('symptoms_affected_quality_of_life')->nullable();

            // Meta fields
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('health_assessments');
    }
};
