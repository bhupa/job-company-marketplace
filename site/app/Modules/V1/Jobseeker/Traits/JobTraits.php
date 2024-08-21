<?php

namespace App\Modules\V1\Jobseeker\Traits;

trait JobTraits
    {
    protected static $experiences = [
        ['label' => 'Less than 1 year', 'value' => '1'],
        ['label' => 'Less than 2 years', 'value' => '2'],
        ['label' => 'Less than 3 years', 'value' => '3'],
        ['label' => '3 years or more', 'value' => '4'],
    ];
    protected static $gender = [
        ['label' => 'Male', 'value' => '1'],
        ['label' => 'Female', 'value' => '2'],
        ['label' => 'Other', 'value' => '3'],
    ];
    protected static $occupation = [
        ['label' => 'Building cleaning', 'value' => '1'],
        [
            'label' => 'Industrial machinery manufacturing In dus trial machinery manufacturing in the house',
            'value' => '2',
        ],
        ['label' => 'Electrical and electronic information', 'value' => '3'],
        ['label' => 'Construction industry', 'value' => '4'],
        ['label' => 'Civil Engineering division', 'value' => '5'],
        ['label' => 'Building division', 'value' => '6'],
        ['label' => 'Lifeline/equipment classification', 'value' => '7'],
        ['label' => 'Food and beverage manufacturing', 'value' => '8'],
        ['label' => 'Nursing', 'value' => '9'],
    ];
    protected static $levels = [
        ['label' => 'N1', 'value' => '1'],
        ['label' => 'N2', 'value' => '2'],
        ['label' => 'N3', 'value' => '3'],
        ['label' => 'N4', 'value' => '4'],
        ['label' => 'N5', 'value' => '5'],
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getExperienceValue($value)
    {
        $matchingExperience = collect(self::$experiences)->firstWhere(
            'value',
            $value
        );

        return $matchingExperience['label'];
    }

    public function getGenderValue($value)
    {
        $matchingGender = collect(self::$gender)->firstWhere('value', $value);

        return $matchingGender['label'];
    }

    public function getOccupationValue($value)
    {
        $matchingOccupation = collect(self::$occupation)->firstWhere(
            'value',
            $value
        );

        return $matchingOccupation['label'];
    }

    public function getLevelValue($value)
    {
        $matchingLevel = collect(self::$levels)->firstWhere('value', $value);

        return $matchingLevel['label'];
    }

    public function getDaysName($values)
    {
        $daysNames = [
            1 => 'Sunday',
            2 => 'Monday',
            3 => 'Tuesday',
            4 => 'Wednesday',
            5 => 'Thursday',
            6 => 'Friday',
            7 => 'Saturday',
        ];

        return array_map(function ($value) use ($daysNames) {
            return $daysNames[$value] ?? '';
        }, $values);
    }
}
