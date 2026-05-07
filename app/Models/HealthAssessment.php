<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthAssessment extends Model
{
    use HasFactory;
    protected $fillable = [
        'age',
        'gender',
        'menstrual_noticed_recently',
        'menstrual_status',
        'physical_changes',
        'hormonal_discomfort',
        'mood_swings',
        'anxiety_levels',
        'brain_fog',
        'memory_changes',
        'motivation_levels',
        'sleep_quality',
        'stress_levels',
        'exercise_frequency',
        'daily_energy_levels',
        'work_home_impact',
        'feel_compared_to_year_ago',
        'symptoms_feel_hormonal',
        'symptoms_affected_quality_of_life',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function score()
    {
        $score = 0;

        // Physical symptoms
        if ($this->fatigue) $score += 1;
        if ($this->weight_changes) $score += 1;
        if ($this->libido_changes) $score += 1;
        if ($this->hot_flashes) $score += 2;
        if ($this->night_sweats) $score += 2;
        if ($this->vaginal_dryness) $score += 2;

        // Emotional & cognitive
        if ($this->mood_swings === 'often') $score += 2;
        if ($this->anxiety_levels === 'moderate') $score += 1;
        if ($this->brain_fog === 'frequently') $score += 1;
        if ($this->memory_changes === 'moderate') $score += 1;
        if ($this->motivation_levels === 'no_motivation') $score += 1;

        // Lifestyle & functioning
        if ($this->sleep_quality === 'fair') $score += 1;
        if ($this->stress_levels === 'moderately_stressed') $score += 1;
        if ($this->exercise_frequency === 'never') $score += 1;
        if ($this->daily_energy_levels === 'often_low') $score += 1;
        if ($this->work_home_impact === 'moderately_affected') $score += 1;

        // Overall
        if ($this->feel_compared_to_year_ago === 'worse') $score += 2;
        if ($this->symptoms_feel_hormonal === 'definitely') $score += 2;
        if ($this->symptoms_affected_quality_of_life === 'significantly') $score += 2;

        return $score;
    }

    public function riskLevel()
    {
        $score = $this->score();

        if ($score <= 5) {
            return 'low';
        } elseif ($score <= 10) {
            return 'moderate';
        } elseif ($score <= 15) {
            return 'high';
        } else {
            return 'very_high';
        }
    }

    public function getRiskColorAttribute()
    {
        return [
            'low' => 'green',
            'moderate' => 'blue',
            'high' => 'orange',
            'very_high' => 'red',
        ][$this->riskLevel()];
    }

    public function getRiskDescriptionAttribute()
    {
        return [
            'low' => 'You seem to be managing well. Consider maintaining your healthy habits.',
            'moderate' => 'You’re experiencing some symptoms. Lifestyle changes could help.',
            'high' => 'Your symptoms are noticeable. It may be time to talk to a doctor.',
            'very_high' => 'Your symptoms are significantly impacting your life. Please consult a healthcare provider.',
        ][$this->riskLevel()];
    }

    public function getInsightsAttribute()
    {
        $score = $this->score();
        $insights = [];

        if ($score >= 5) {
            $insights[] = 'You’re experiencing multiple symptoms. Consider tracking them for a few weeks.';
        }

        if ($this->fatigue || $this->daily_energy_levels === 'often_low') {
            $insights[] = 'Fatigue is a common menopause symptom. Regular exercise and good sleep hygiene can help.';
        }

        if ($this->mood_swings || $this->anxiety_levels === 'moderate') {
            $insights[] = 'Mood changes can be challenging. Mindfulness and stress management techniques may help.';
        }

        if ($this->hot_flashes || $this->night_sweats) {
            $insights[] = 'These symptoms are strongly linked to hormonal changes. A doctor can discuss management options.';
        }

        if ($this->sleep_quality === 'fair' || $this->brain_fog === 'frequently') {
            $insights[] = 'Sleep and cognitive issues often go together. Try creating a relaxing bedtime routine.';
        }

        if (empty($insights)) {
            $insights[] = 'You seem to be handling the changes well. Keep up your healthy habits!';
        }

        return $insights;
    }

    public function getCategoryAttribute()
    {
        $score = $this->score();

        if ($score <= 5) {
            return 'mild';
        } elseif ($score <= 10) {
            return 'moderate';
        } elseif ($score <= 15) {
            return 'severe';
        } else {
            return 'very_severe';
        }
    }

    public function getMessageAttribute()
    {
        return [
            'mild' => 'Your symptoms are mild. Consider lifestyle adjustments and regular check-ups.',
            'moderate' => 'You’re experiencing moderate symptoms. Talk to your doctor about management options.',
            'severe' => 'Your symptoms are significant. A healthcare provider can help you create a treatment plan.',
            'very_severe' => 'You’re experiencing severe symptoms. Please consult a doctor as soon as possible.',
        ][$this->category];
    }

    public function getCategoryColorAttribute()
    {
        return [
            'mild' => 'green',
            'moderate' => 'blue',
            'severe' => 'orange',
            'very_severe' => 'red',
        ][$this->category];
    }

    public function getSymptomSummaryAttribute()
    {
        return [
            'fatigue' => (bool) $this->fatigue,
            'weight_changes' => (bool) $this->weight_changes,
            'libido_changes' => (bool) $this->libido_changes,
            'hot_flashes' => (bool) $this->hot_flashes,
            'night_sweats' => (bool) $this->night_sweats,
            'vaginal_dryness' => (bool) $this->vaginal_dryness,
            'mood_swings' => $this->mood_swings,
            'anxiety_levels' => $this->anxiety_levels,
            'brain_fog' => $this->brain_fog,
            'memory_changes' => $this->memory_changes,
            'motivation_levels' => $this->motivation_levels,
            'sleep_quality' => $this->sleep_quality,
            'stress_levels' => $this->stress_levels,
            'exercise_frequency' => $this->exercise_frequency,
            'daily_energy_levels' => $this->daily_energy_levels,
            'work_home_impact' => $this->work_home_impact,
            'feel_compared_to_year_ago' => $this->feel_compared_to_year_ago,
            'symptoms_feel_hormonal' => $this->symptoms_feel_hormonal,
            'symptoms_affected_quality_of_life' => $this->symptoms_affected_quality_of_life,
        ];
    }

    public function getImpactCategoriesAttribute()
    {
        $categories = [];

        if ($this->mood_swings === 'often' || $this->anxiety_levels === 'moderate' || $this->motivation_levels === 'no_motivation') {
            $categories[] = 'emotional';
        }

        if ($this->sleep_quality === 'fair' || $this->brain_fog === 'frequently') {
            $categories[] = 'cognitive';
        }

        if ($this->hot_flashes || $this->night_sweats || $this->vaginal_dryness) {
            $categories[] = 'physical';
        }

        if ($this->fatigue || $this->daily_energy_levels === 'often_low') {
            $categories[] = 'energy';
        }

        if ($this->work_home_impact === 'moderately_affected' || $this->stress_levels === 'moderately_stressed') {
            $categories[] = 'lifestyle';
        }

        return $categories;
    }

    public function getSeverityScoresAttribute()
    {
        return [
            'fatigue' => $this->fatigue ? 1 : 0,
            'weight_changes' => $this->weight_changes ? 1 : 0,
            'libido_changes' => $this->libido_changes ? 1 : 0,
            'hot_flashes' => $this->hot_flashes ? 2 : 0,
            'night_sweats' => $this->night_sweats ? 2 : 0,
            'vaginal_dryness' => $this->vaginal_dryness ? 2 : 0,
            'mood_swings' => $this->mood_swings === 'often' ? 2 : ($this->mood_swings === 'sometimes' ? 1 : 0),
            'anxiety_levels' => $this->anxiety_levels === 'moderate' ? 1 : 0,
            'brain_fog' => $this->brain_fog === 'frequently' ? 1 : 0,
            'memory_changes' => $this->memory_changes === 'moderate' ? 1 : 0,
            'motivation_levels' => $this->motivation_levels === 'no_motivation' ? 1 : 0,
            'sleep_quality' => $this->sleep_quality === 'fair' ? 1 : 0,
            'stress_levels' => $this->stress_levels === 'moderately_stressed' ? 1 : 0,
            'exercise_frequency' => $this->exercise_frequency === 'never' ? 1 : 0,
            'daily_energy_levels' => $this->daily_energy_levels === 'often_low' ? 1 : 0,
            'work_home_impact' => $this->work_home_impact === 'moderately_affected' ? 1 : 0,
            'feel_compared_to_year_ago' => $this->feel_compared_to_year_ago === 'worse' ? 2 : 0,
            'symptoms_feel_hormonal' => $this->symptoms_feel_hormonal === 'definitely' ? 2 : 0,
            'symptoms_affected_quality_of_life' => $this->symptoms_affected_quality_of_life === 'significantly' ? 2 : 0,
        ];
    }
}
