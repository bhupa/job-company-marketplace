<?php
if (!function_exists('getReasonsForCancellation')) {
    function getReasonsForCancellation()
    {
        return [
            [
                'id' => '1',
                'name' => 'cancellationReasons',
                'title' => [
                    'en' => trans(
                        'lang.account_cancellation.title_one',
                        [],
                        'en'
                    ),
                    'ja' => trans(
                        'lang.account_cancellation.title_one',
                        [],
                        'ja'
                    ),
                ],
                'options' => [
                    [
                        'id' => '1',
                        'title' => [
                            'en' => trans(
                                'lang.account_cancellation.dissatisfaction_with_service_quality',
                                [],
                                'en'
                            ),
                            'ja' => trans(
                                'lang.account_cancellation.dissatisfaction_with_service_quality',
                                [],
                                'ja'
                            ),
                        ],
                    ],
                    [
                        'id' => '2',
                        'title' => [
                            'en' => trans(
                                'lang.account_cancellation.high_fees',
                                [],
                                'en'
                            ),
                            'ja' => trans(
                                'lang.account_cancellation.high_fees',
                                [],
                                'ja'
                            ),
                        ],
                        'hasSubMenu' => true,
                        'subMenu' => [
                            [
                                'id' => '1',
                                'name' => 'highFees',
                                'title' => [
                                    'en' => trans(
                                        'lang.account_cancellation.title_two',
                                        [],
                                        'en'
                                    ),
                                    'ja' => trans(
                                        'lang.account_cancellation.title_two',
                                        [],
                                        'ja'
                                    ),
                                ],
                                'options' => [
                                    [
                                        'id' => '1',
                                        'title' => [
                                            'en' => trans(
                                                'lang.account_cancellation.considering',
                                                [],
                                                'en'
                                            ),
                                            'ja' => trans(
                                                'lang.account_cancellation.considering',
                                                [],
                                                'ja'
                                            ),
                                        ],
                                    ],
                                    [
                                        'id' => '2',
                                        'title' => [
                                            'en' => trans(
                                                'lang.account_cancellation.willing_to_consider_depending_on_the_price',
                                                [],
                                                'en'
                                            ),
                                            'ja' => trans(
                                                'lang.account_cancellation.willing_to_consider_depending_on_the_price',
                                                [],
                                                'ja'
                                            ),
                                        ],
                                    ],
                                    [
                                        'id' => '3',
                                        'title' => [
                                            'en' => trans(
                                                'lang.account_cancellation.not_considering',
                                                [],
                                                'en'
                                            ),
                                            'ja' => trans(
                                                'lang.account_cancellation.not_considering',
                                                [],
                                                'ja'
                                            ),
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        'id' => '3',
                        'title' => [
                            'en' => trans(
                                'lang.account_cancellation.poor_support',
                                [],
                                'en'
                            ),
                            'ja' => trans(
                                'lang.account_cancellation.poor_support',
                                [],
                                'ja'
                            ),
                        ],
                    ],
                    [
                        'id' => '4',
                        'title' => [
                            'en' => trans(
                                'lang.account_cancellation.poor_email_response',
                                [],
                                'en'
                            ),
                            'ja' => trans(
                                'lang.account_cancellation.poor_email_response',
                                [],
                                'ja'
                            ),
                        ],
                    ],
                    [
                        'id' => '5',
                        'title' => [
                            'en' => trans(
                                'lang.account_cancellation.lack_of_contact_from_the_other_party',
                                [],
                                'en'
                            ),
                            'ja' => trans(
                                'lang.account_cancellation.lack_of_contact_from_the_other_party',
                                [],
                                'ja'
                            ),
                        ],
                    ],
                    [
                        'id' => '6',
                        'title' => [
                            'en' => trans(
                                'lang.account_cancellation.poor_technical_support',
                                [],
                                'en'
                            ),
                            'ja' => trans(
                                'lang.account_cancellation.poor_technical_support',
                                [],
                                'ja'
                            ),
                        ],
                    ],
                    [
                        'id' => '7',
                        'title' => [
                            'en' => trans(
                                'lang.account_cancellation.complicated_payment_procedures',
                                [],
                                'en'
                            ),
                            'ja' => trans(
                                'lang.account_cancellation.complicated_payment_procedures',
                                [],
                                'ja'
                            ),
                        ],
                    ],
                    [
                        'id' => '8',
                        'title' => [
                            'en' => trans(
                                'lang.account_cancellation.low_frequency_of_use',
                                [],
                                'en'
                            ),
                            'ja' => trans(
                                'lang.account_cancellation.low_frequency_of_use',
                                [],
                                'ja'
                            ),
                        ],
                    ],
                    [
                        'id' => '9',
                        'title' => [
                            'en' => trans(
                                'lang.account_cancellation.experienced_troubles',
                                [],
                                'en'
                            ),
                            'ja' => trans(
                                'lang.account_cancellation.experienced_troubles',
                                [],
                                'ja'
                            ),
                        ],
                    ],
                    [
                        'id' => '10',
                        'title' => [
                            'en' => trans(
                                'lang.account_cancellation.other',
                                [],
                                'en'
                            ),
                            'ja' => trans(
                                'lang.account_cancellation.other',
                                [],
                                'ja'
                            ),
                        ],
                    ],
                ],
            ],
            [
                'id' => '2',
                'name' => 'futurePlans',
                'title' => [
                    'en' => trans(
                        'lang.account_cancellation.title_three',
                        [],
                        'en'
                    ),
                    'ja' => trans(
                        'lang.account_cancellation.title_three',
                        [],
                        'ja'
                    ),
                ],
                'options' => [
                    [
                        'id' => '1',
                        'title' => [
                            'en' => trans(
                                'lang.account_cancellation.use_other_service',
                                [],
                                'en'
                            ),
                            'ja' => trans(
                                'lang.account_cancellation.use_other_service',
                                [],
                                'ja'
                            ),
                        ],
                    ],
                    [
                        'id' => '2',
                        'title' => [
                            'en' => trans(
                                'lang.account_cancellation.continue_working_in_current_company',
                                [],
                                'en'
                            ),
                            'ja' => trans(
                                'lang.account_cancellation.continue_working_in_current_company',
                                [],
                                'ja'
                            ),
                        ],
                    ],
                ],
            ],
        ];
    }
}
?>
