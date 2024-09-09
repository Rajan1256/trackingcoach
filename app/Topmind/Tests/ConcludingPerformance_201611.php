<?php

namespace App\Topmind\Tests;

class ConcludingPerformance_201611 extends TestAbstract
{
    public $introductionTest;

    public $respiratoryRate;

    public $hrRestWithoutExercise;

    public $hrvRestWithoutExercise;

    public $hrRestWithExercise;

    public $hrvRestWithExercise;

    public $wattMax;

    public $hrAt;

    public $exerciseConclusions;

    public $recoveryConclusions;

    public $conclusion;

    public $weight;

    public $systolic;

    public $diastolic;

    public $rrAndHrRestWithoutExerciseExplanation;

    public $rrAndHrRestWithExerciseExplanation;

    public $respiratoryRateWithExercise;

    public $personalProgram;

    public $wattD2;

    public $hrAfterCycling;

    public $hrvAfterCycling;

    public $targetExplanation;

    /**
     * Return test name.
     *
     * @return string
     */
    public function getName()
    {
        return __('2nd TopMind™ test');
    }

    /**
     * Return blade path to this tests views.
     *
     * @return string
     */
    public function getViewPath()
    {
        return 'tests.performance.concluding.201611';
    }

    /**
     * Get validation rules as array.
     *
     * @return array
     */
    public function getValidationRules()
    {
        return [
            'date'             => 'required',
            'introductionTest' => 'required',
//            'respiratoryRate' => 'required|integer',
//            'hrRestWithoutExercise' => 'required|integer',
//            'hrRestWithExercise' => 'required|integer',
//            'hrvRestWithoutExercise' => 'required|integer',
//            'hrvRestWithExercise' => 'required|integer',
//            'rrAndHrRestWithoutExerciseImage' => 'required|image',
//            'rrAndHrRestWithoutExerciseExplanation' => 'required',
//            'rrAndHrRestWithExerciseImage' => 'required|image',
//            'rrAndHrRestWithExerciseExplanation' => 'required',
//            'restConclusions' => 'required',
//            'vo2Max' => 'required',
//            'wattMax' => 'required',
//            'hrAt' => 'required',
//            'recoveryCapacity' => 'required',
//            'exerciseConclusions' => 'required',
//            'recoveryConclusions' => 'required',
//            'weight' => 'required',
//            'systolic' => 'required|numeric',
//            'diastolic' => 'required|numeric',
//            'respiratoryRateWithExercise' => 'required|numeric'
        ];
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

    protected function getRelatedTests()
    {
        return ['introductionTest'];
    }
}
