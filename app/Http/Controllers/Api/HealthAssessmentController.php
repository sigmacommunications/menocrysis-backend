<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\HealthAssessment;
use Illuminate\Support\Facades\Validator;
use Exception;

class HealthAssessmentController extends BaseController
{
    //
	
	public function store(Request $request) 
	{
        
        $input = $request->all();

		$validator = Validator::make($request->all(), [
            // Basic Information
            'age' => 'required|integer|min:1|max:120',
            'gender' => 'required|in:female,male,prefer_not_to_say',
            'menstrual_noticed_recently' => 'nullable|in:yes,no,not_applicable',
            'menstrual_status' => 'nullable|in:regular,irregular,stopped',
            
            // Physical Symptoms (boolean fields)
            'physical_changes' => 'nullable',
            'hormonal_discomfort' => 'nullable',
            
            // Emotional & Cognitive
            'mood_swings' => 'nullable',
            'anxiety_levels' => 'nullable',
            'brain_fog' => 'nullable',
            'memory_changes' => 'nullable',
            'motivation_levels' => 'nullable',
            
            // Lifestyle & Functioning
            'sleep_quality' => 'nullable',
            'stress_levels' => 'nullable',
            'exercise_frequency' => 'nullable',
            'daily_energy_levels' => 'nullable',
            'work_home_impact' => 'nullable',
            
            // Overall Assessment
            'feel_compared_to_year_ago' => 'nullable',
            'symptoms_feel_hormonal' => 'nullable',
            'symptoms_affected_quality_of_life' => 'nullable'
        ]);

		if ($validator->fails()) {
			// return ApiResponse::error('Failed to Add Category',$validator->errors()->first());
			return $this->sendError($validator->errors()->first());
		}
		
		try{
			$healthAssessment = HealthAssessment::create($input);
			
            //$analysis = $this->generateAnalysis($healthAssessment);
			
            $lorem =  'lorem ipsum dolor sit amet consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'; 

            return response()->json([
                'success' => true,
                'message' => 'Health assessment submitted successfully',
                'data' => $healthAssessment,
                'irragularcycle' => $lorem,
                'hotflashes' => $lorem,
                'moodchanges' => $lorem,
                'shortexplanation' => $lorem,
                //'analysis' => $analysis
            ], 201);
        }catch(Exception $e){
            return $this->sendError('Failed to Add Health Assessment',$e->getMessage());
        }

	}

    private function generateAnalysis($assessment)
    {
        // Detect stage based on age and symptoms
        $stage = $this->detectStage($assessment);
        
        // Get key symptoms detected
        $keySymptoms = $this->getKeySymptoms($assessment);
        
        // Generate explanation text
        $explanation = $this->generateExplanation($stage, $keySymptoms);
        
        // Generate recommendations
        $recommendations = $this->generateRecommendations($assessment, $stage);
        
        return [
            'identified_stage' => $stage,
            'key_symptoms_detected' => $keySymptoms,
            'explanation_text' => $explanation,
            'recommendations' => $recommendations,
            'next_steps' => $this->getNextSteps($stage),
            'severity_level' => $this->getSeverityLevel($assessment)
        ];
    }

    /**
     * Detect which stage the user is in
     */
    private function detectStage($assessment)
    {
        $age = $assessment->age;
        $symptoms = $this->getSymptomSeverityScore($assessment);
        
        // Premenopause (Usually 30-45)
        if ($age >= 30 && $age <= 45 && $symptoms['total'] > 20) {
            if ($assessment->menstrual_status == 'irregular' || 
                $assessment->hot_flashes || 
                $assessment->mood_swings == 'often') {
                return [
                    'name' => 'Perimenopause',
                    'description' => 'Perimenopause is the transitional period before menopause, where hormonal fluctuations begin to cause various physical and emotional changes.',
                    'stage_icon' => '🌙',
                    'color' => '#FF6B6B'
                ];
            }
        }
        
        // Early Menopause (40-50)
        if ($age >= 40 && $age <= 50 && $symptoms['total'] > 40) {
            return [
                'name' => 'Early Menopause Transition',
                'description' => 'You are in the early stages of menopause transition with noticeable hormonal changes affecting your daily life.',
                'stage_icon' => '🌸',
                'color' => '#FF8C42'
            ];
        }
        
        // Full Menopause (45-55)
        if ($age >= 45 && $age <= 55 && $symptoms['total'] > 60) {
            if ($assessment->menstrual_status == 'stopped' ||
                ($assessment->hot_flashes && $assessment->night_sweats)) {
                return [
                    'name' => 'Menopause',
                    'description' => 'You are experiencing classic menopause symptoms as your body completes the natural transition.',
                    'stage_icon' => '🍂',
                    'color' => '#9B59B6'
                ];
            }
        }
        
        // Post Menopause (55+)
        if ($age > 55) {
            return [
                'name' => 'Post-Menopause',
                'description' => 'You are in the post-menopausal stage. Focus on long-term health maintenance and symptom management.',
                'stage_icon' => '✨',
                'color' => '#3498DB'
            ];
        }
        
        // Default for younger women
        if ($age < 30) {
            return [
                'name' => 'Early Hormonal Changes',
                'description' => 'Hormonal fluctuations are common at this age. Tracking your symptoms can help identify patterns.',
                'stage_icon' => '💫',
                'color' => '#2ECC71'
            ];
        }
        
        // Default
        return [
            'name' => 'Hormonal Health Assessment',
            'description' => 'Based on your symptoms, hormonal evaluation may provide more insights into your health.',
            'stage_icon' => '🔄',
            'color' => '#95A5A6'
        ];
    }

    /**
     * Get key symptoms detected
     */
    private function getKeySymptoms($assessment)
    {
        $symptoms = [];
        
        // Menstrual symptoms
        if ($assessment->menstrual_status == 'irregular') {
            $symptoms[] = [
                'name' => 'Irregular Cycles',
                'description' => 'Changes in menstrual cycle length, flow, or frequency. This is often one of the first signs of hormonal transition.',
                'icon' => '📅',
                'severity' => $assessment->menstrual_status == 'irregular' ? 'moderate' : 'high'
            ];
        }
        
        if ($assessment->menstrual_status == 'stopped') {
            $symptoms[] = [
                'name' => 'Missed Periods',
                'description' => 'Absence of menstruation for extended periods indicates significant hormonal changes.',
                'icon' => '⏸️',
                'severity' => 'high'
            ];
        }
        
        // Physical symptoms
        if ($assessment->hot_flashes) {
            $symptoms[] = [
                'name' => 'Hot Flashes',
                'description' => 'Sudden feelings of warmth, often in the upper body, which can be accompanied by sweating and redness.',
                'icon' => '🔥',
                'severity' => $assessment->hot_flashes ? 'moderate' : 'mild'
            ];
        }
        
        if ($assessment->night_sweats) {
            $symptoms[] = [
                'name' => 'Night Sweats',
                'description' => 'Episodes of excessive sweating during sleep that can disrupt rest and affect sleep quality.',
                'icon' => '💦',
                'severity' => 'moderate'
            ];
        }
        
        if ($assessment->fatigue) {
            $symptoms[] = [
                'name' => 'Persistent Fatigue',
                'description' => 'Ongoing tiredness or lack of energy that affects daily activities and productivity.',
                'icon' => '😴',
                'severity' => $assessment->daily_energy_levels == 'often_low' ? 'high' : 'moderate'
            ];
        }
        
        if ($assessment->weight_changes) {
            $symptoms[] = [
                'name' => 'Weight Changes',
                'description' => 'Unexplained weight gain or redistribution, particularly around the abdomen area.',
                'icon' => '⚖️',
                'severity' => 'moderate'
            ];
        }
        
        if ($assessment->libido_changes) {
            $symptoms[] = [
                'name' => 'Libido Changes',
                'description' => 'Changes in sexual desire or response, often related to hormonal fluctuations.',
                'icon' => '❤️',
                'severity' => 'moderate'
            ];
        }
        
        // Emotional & Cognitive symptoms
        if ($assessment->mood_swings == 'often') {
            $symptoms[] = [
                'name' => 'Mood Changes',
                'description' => 'Frequent emotional fluctuations, irritability, or unexplained shifts in mood.',
                'icon' => '🎭',
                'severity' => 'high'
            ];
        } elseif ($assessment->mood_swings == 'sometimes') {
            $symptoms[] = [
                'name' => 'Mood Changes',
                'description' => 'Occasional emotional fluctuations that may be linked to hormonal cycles.',
                'icon' => '🎭',
                'severity' => 'mild'
            ];
        }
        
        if ($assessment->anxiety_levels == 'moderate') {
            $symptoms[] = [
                'name' => 'Increased Anxiety',
                'description' => 'Persistent worry, nervousness, or feeling on edge that affects daily functioning.',
                'icon' => '😰',
                'severity' => 'moderate'
            ];
        }
        
        if ($assessment->brain_fog == 'frequently') {
            $symptoms[] = [
                'name' => 'Brain Fog',
                'description' => 'Difficulty concentrating, memory lapses, or feeling mentally unclear.',
                'icon' => '🌫️',
                'severity' => 'high'
            ];
        }
        
        if ($assessment->sleep_quality == 'fair') {
            $symptoms[] = [
                'name' => 'Sleep Disturbances',
                'description' => 'Difficulty falling asleep, staying asleep, or waking up feeling unrested.',
                'icon' => '😴',
                'severity' => 'moderate'
            ];
        }
        
        // Return top 3-4 symptoms
        return array_slice($symptoms, 0, 4);
    }

    /**
     * Generate explanation text
     */
    private function generateExplanation($stage, $keySymptoms)
    {
        $stageName = $stage['name'];
        $symptomList = implode(', ', array_column(array_slice($keySymptoms, 0, 3), 'name'));
        
        if ($stageName == 'Perimenopause') {
            return "Based on your age and symptoms, you appear to be in Perimenopause. During this phase, your body begins the natural transition toward menopause. The hormonal fluctuations you're experiencing—particularly {$symptomList}—are typical for this stage. These changes occur as your ovaries gradually produce less estrogen. While these symptoms can be challenging, understanding them is the first step toward effective management. Most women experience perimenopause for 4-8 years before reaching full menopause.";
        }
        
        if ($stageName == 'Menopause') {
            return "Your symptoms indicate you've entered Menopause. You're experiencing classic signs including {$symptomList}. At this stage, your body has significantly reduced estrogen production. The symptoms you're facing are a normal part of this biological transition. While menopause brings changes, many women find relief through various management strategies. Remember that menopause is not an illness but a natural life stage that affects each woman differently.";
        }
        
        if ($stageName == 'Early Hormonal Changes') {
            return "Your assessment shows early signs of hormonal changes. Even at your age, hormonal fluctuations can cause symptoms like {$symptomList}. These changes might be related to your natural cycle, stress, lifestyle factors, or early hormonal transitions. Tracking your symptoms over time will help identify patterns and triggers. Many women experience similar changes and find relief through lifestyle adjustments and natural remedies.";
        }
        
        return "Based on your assessment, you're experiencing hormonal changes common during {$stageName}. Key symptoms include {$symptomList}. These changes occur as your body's hormone levels fluctuate naturally. While these symptoms can be uncomfortable, they're typically manageable with proper care and attention. Your results provide a personalized roadmap for understanding and addressing your unique hormonal health journey.";
    }

    /**
     * Generate recommendations
     */
    private function generateRecommendations($assessment, $stage)
    {
        $recommendations = [];
        
        // Lifestyle recommendations based on symptoms
        if ($assessment->sleep_quality == 'fair') {
            $recommendations[] = [
                'category' => 'Sleep Hygiene',
                'title' => 'Improve Your Sleep Quality',
                'description' => 'Establish a consistent sleep schedule, create a cool, dark sleeping environment, and avoid screens 1 hour before bed. Consider relaxation techniques like deep breathing or meditation before sleep.',
                'priority' => 'high',
                'action_items' => [
                    'Go to bed and wake up at the same time daily',
                    'Keep bedroom temperature between 60-67°F (15-19°C)',
                    'Use blackout curtains and white noise machine',
                    'Avoid caffeine after 2 PM'
                ]
            ];
        }
        
        if ($assessment->stress_levels == 'moderately_stressed') {
            $recommendations[] = [
                'category' => 'Stress Management',
                'title' => 'Reduce Stress Levels',
                'description' => 'Chronic stress can worsen hormonal symptoms. Implement daily stress-reduction practices to help balance your hormones naturally.',
                'priority' => 'high',
                'action_items' => [
                    'Practice 10-15 minutes of mindfulness meditation daily',
                    'Take short breaks throughout your workday',
                    'Try yoga or gentle stretching exercises',
                    'Consider journaling to process emotions'
                ]
            ];
        }
        
        if ($assessment->hot_flashes || $assessment->night_sweats) {
            $recommendations[] = [
                'category' => 'Symptom Management',
                'title' => 'Manage Hot Flashes & Night Sweats',
                'description' => 'Hot flashes and night sweats are common hormonal symptoms that can be managed with lifestyle adjustments and natural remedies.',
                'priority' => 'high',
                'action_items' => [
                    'Dress in layers to easily adjust temperature',
                    'Keep a portable fan at your desk or bedside',
                    'Avoid triggers like spicy foods, caffeine, and alcohol',
                    'Practice paced breathing techniques when a hot flash starts'
                ]
            ];
        }
        
        if ($assessment->mood_swings == 'often' || $assessment->anxiety_levels == 'moderate') {
            $recommendations[] = [
                'category' => 'Emotional Wellness',
                'title' => 'Support Emotional Balance',
                'description' => 'Hormonal fluctuations can significantly impact mood and emotional wellbeing. These strategies can help stabilize your mood.',
                'priority' => 'high',
                'action_items' => [
                    'Track your mood alongside your cycle/hormonal symptoms',
                    'Connect with support groups or friends experiencing similar changes',
                    'Consider cognitive behavioral therapy (CBT) techniques',
                    'Regular exercise (especially walking or swimming) to boost mood'
                ]
            ];
        }
        
        if ($assessment->exercise_frequency == 'never') {
            $recommendations[] = [
                'category' => 'Physical Activity',
                'title' => 'Start Moving Regularly',
                'description' => 'Regular physical activity helps regulate hormones, improve mood, reduce stress, and manage weight.',
                'priority' => 'medium',
                'action_items' => [
                    'Start with 10-15 minute walks daily',
                    'Try low-impact exercises like swimming or cycling',
                    'Include strength training 2-3 times per week',
                    'Find an activity you enjoy to stay motivated'
                ]
            ];
        }
        
        if ($assessment->daily_energy_levels == 'often_low') {
            $recommendations[] = [
                'category' => 'Energy Management',
                'title' => 'Boost Your Energy Naturally',
                'description' => 'Low energy is often linked to hormonal changes, sleep quality, and nutrition. These strategies can help restore your vitality.',
                'priority' => 'medium',
                'action_items' => [
                    'Eat balanced meals with protein, healthy fats, and complex carbs',
                    'Stay hydrated throughout the day',
                    'Take short movement breaks every hour',
                    'Consider B-complex vitamins or iron supplements (consult doctor first)'
                ]
            ];
        }
        
        // Medical recommendations based on severity
        if ($this->getSeverityLevel($assessment) == 'severe') {
            $recommendations[] = [
                'category' => 'Medical Consultation',
                'title' => 'Consult a Healthcare Provider',
                'description' => 'Your symptoms indicate significant hormonal changes. A healthcare provider can offer personalized treatment options including hormone therapy, medications, or alternative treatments.',
                'priority' => 'urgent',
                'action_items' => [
                    'Schedule an appointment with a gynecologist or endocrinologist',
                    'Bring your symptom journal to the appointment',
                    'Discuss hormone testing options',
                    'Ask about bioidentical hormone replacement therapy (BHRT) if appropriate'
                ]
            ];
        }
        
        // Diet recommendations
        $recommendations[] = [
            'category' => 'Nutrition',
            'title' => 'Hormone-Balancing Nutrition',
            'description' => 'The right foods can help support hormonal health and reduce symptom severity.',
            'priority' => 'medium',
            'action_items' => [
                'Increase intake of phytoestrogen-rich foods (soy, flaxseeds, legumes)',
                'Eat cruciferous vegetables (broccoli, cauliflower, kale) for liver support',
                'Include omega-3 fatty acids from fish, walnuts, or chia seeds',
                'Limit sugar, processed foods, and refined carbohydrates'
            ]
        ];
        
        return $recommendations;
    }

    /**
     * Get next steps based on stage
     */
    private function getNextSteps($stage)
    {
        $stageName = $stage['name'];
        
        $nextSteps = [
            'Track Your Symptoms Daily' => 'Use a journal or app to record your symptoms, cycle changes, and triggers.',
            'Schedule Regular Check-ups' => 'Annual wellness visits help monitor your hormonal health.',
            'Join Support Communities' => 'Connect with others experiencing similar hormonal changes.',
        ];
        
        if ($stageName == 'Perimenopause') {
            $nextSteps['Consider Hormone Testing'] = 'Discuss hormone level testing with your healthcare provider.';
            $nextSteps['Learn About Treatment Options'] = 'Research lifestyle changes, natural remedies, and medical treatments.';
        }
        
        if ($stageName == 'Menopause') {
            $nextSteps['Evaluate Bone Health'] = 'Schedule a bone density screening.';
            $nextSteps['Review Heart Health'] = 'Discuss cardiovascular risk factors with your doctor.';
        }
        
        return $nextSteps;
    }

    /**
     * Get severity level
     */
    private function getSeverityLevel($assessment)
    {
        $score = $this->getSymptomSeverityScore($assessment)['total'];
        
        if ($score < 30) return 'mild';
        if ($score < 60) return 'moderate';
        if ($score < 80) return 'severe';
        return 'very_severe';
    }

    /**
     * Calculate symptom severity score
     */
    private function getSymptomSeverityScore($assessment)
    {
        $score = 0;
        $maxScore = 100;
        
        // Score physical symptoms (each = 5 points)
        $physicalSymptoms = 0;
        if ($assessment->fatigue) $physicalSymptoms += 5;
        if ($assessment->weight_changes) $physicalSymptoms += 5;
        if ($assessment->libido_changes) $physicalSymptoms += 5;
        if ($assessment->hot_flashes) $physicalSymptoms += 8;
        if ($assessment->night_sweats) $physicalSymptoms += 8;
        if ($assessment->vaginal_dryness) $physicalSymptoms += 5;
        
        // Score emotional symptoms
        $emotionalScore = 0;
        $emotionalScore += $assessment->mood_swings == 'often' ? 15 : ($assessment->mood_swings == 'sometimes' ? 8 : 0);
        $emotionalScore += $assessment->anxiety_levels == 'moderate' ? 15 : ($assessment->anxiety_levels == 'mild' ? 8 : 0);
        $emotionalScore += $assessment->brain_fog == 'frequently' ? 10 : ($assessment->brain_fog == 'occasionally' ? 5 : 0);
        $emotionalScore += $assessment->memory_changes == 'moderate' ? 10 : ($assessment->memory_changes == 'slight' ? 5 : 0);
        $emotionalScore += $assessment->motivation_levels == 'no_motivation' ? 10 : ($assessment->motivation_levels == 'low_motivation' ? 5 : 0);
        
        // Score lifestyle impact
        $lifestyleScore = 0;
        $lifestyleScore += $assessment->sleep_quality == 'fair' ? 10 : ($assessment->sleep_quality == 'good' ? 5 : 0);
        $lifestyleScore += $assessment->stress_levels == 'moderately_stressed' ? 12 : ($assessment->stress_levels == 'mildly_stressed' ? 6 : 0);
        $lifestyleScore += $assessment->daily_energy_levels == 'often_low' ? 10 : ($assessment->daily_energy_levels == 'often_good' ? 5 : 0);
        $lifestyleScore += $assessment->work_home_impact == 'moderately_affected' ? 10 : ($assessment->work_home_impact == 'slightly_affected' ? 5 : 0);
        
        $totalScore = $physicalSymptoms + $emotionalScore + $lifestyleScore;
        
        return [
            'total' => $totalScore,
            'physical' => $physicalSymptoms,
            'emotional' => $emotionalScore,
            'lifestyle' => $lifestyleScore
        ];
    }
}
