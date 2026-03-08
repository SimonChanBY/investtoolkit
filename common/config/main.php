<?php

return [
    'name' => 'Invest Toolkit',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'dateFormat' => 'dd.MM.yyyy',
            'datetimeFormat' => 'php:d-m-Y H:i',
            'timeFormat' => 'php:H:i',
            'decimalSeparator' => ',',
            'thousandSeparator' => ' ',
            'currencyCode' => 'EUR',
            'nullDisplay' => '<span class="not-set">(not set)</span>',
            'booleanFormat' => ['<span class="text-danger">No</span>', '<span class="text-success">Yes</span>'],
        ],
    ],
];
