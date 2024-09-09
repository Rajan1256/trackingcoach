<?php

use App\Topmind\Tests\ConcludingPerformance_201611;
use App\Topmind\Tests\IntroductionPerformance_201611;

return [
    'tests'     => [
        IntroductionPerformance_201611::class,
        ConcludingPerformance_201611::class,
    ],
    'color'     => '#ffd500',
    'logo'      => '/img/trackingcoach.svg',
    'languages' => [
        'en' => 'English',
        'nl' => 'Dutch',
        'es' => 'Spanish',
    ],
];
