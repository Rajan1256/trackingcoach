<?php

namespace App\Topmind\Tests;

class IntroductionPerformance_201611 extends TestAbstract
{
    public $respiratoryRate;

    public $respiratoryRateWithExercise;

    public $hrRestWithoutExercise;

    public $hrvRestWithoutExercise;

    public $hrRestWithExercise;

    public $hrvRestWithExercise;

    public $rrAndHrRestWithoutExerciseExplanation;

    public $rrAndHrRestWithExerciseExplanation;

    public $personalTargets;

    public $hrMax;

    public $wattMax;

    public $hrAt;

    public $wattAt;

    public $tableType;

    public $personalProgram;

    public $conclusion;

    public $length;

    public $weight;

    public $systolic;

    public $diastolic;

    public $wattD2;

    public $hrAfterCycling;

    public $hrvAfterCycling;

    /**
     * Return blade path to this tests views.
     *
     * @return string
     */
    public function getViewPath()
    {
        return 'tests.performance.introduction.201611';
    }

    /**
     * Get validation rules as array.
     *
     * @return array
     */
    public function getValidationRules()
    {
        return [
            'date' => 'required',
//            'respiratoryRate' => 'required|integer',
//            'respiratoryRateWithExercise' => 'required|integer',
//            'hrRestWithoutExercise' => 'required|integer',
//            'hrRestWithExercise' => 'required|integer',
//            'hrvRestWithoutExercise' => 'required|integer',
//            'hrvRestWithExercise' => 'required|integer',
//            'rrAndHrRestWithoutExerciseExplanation' => 'required',
//            'rrAndHrRestWithExerciseExplanation' => 'required',
//            'personalTargets' => 'required',
//            'vo2Max' => 'required',
//            'hrMax' => 'required',
//            'wattMax' => 'required',
//            'hrAt' => 'required',
//            'wattAt' => 'required',
//            'personalProgram' => 'required',
//            'conclusion' => 'required',
//            'rrAndHrRestWithoutExerciseImage' => 'required|image',
//            'rrAndHrRestWithExerciseImage' => 'required|image',
//            'length' => 'required|numeric|min:140|max:230',
//            'weight' => 'required',
//            'systolic' => 'required|numeric',
//            'diastolic' => 'required|numeric',
        ];
    }

    /**
     * Return test name.
     *
     * @return string
     */
    public function getName()
    {
        return __('1st TopMind™ test');
    }

    /**
     * Get upload collection names.
     *
     * @return array
     */
    public function getUploadCollections()
    {
        return [
            'rrAndHrRestWithoutExerciseImage',
            'rrAndHrRestWithExerciseImage',
        ];
    }
}
