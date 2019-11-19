<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'price' => $faker->randomFloat(2,0,1000)
    ];
});

$factory->defineAs(App\Product::class, 'reformatted',function (Faker $faker) {
    return [
        'data' => [
            'type' => 'products',
            'attributes' => [
                'name' => $faker->name,
                'price' => $faker->randomFloat(2,0, 1000)
            ]
        ]
    ];
});

$factory->defineAs(App\Product::class, 'withoutName', function (Faker $faker) {
    return [
        'data' => [
            'type' => 'products',
            'attributes' => [
                'name' => '',
                'price' => $faker->randomFloat($nbMaxDecimals = 2, $min = 0, $max = 500)
            ]
        ]
    ];
});
$factory->defineAs(App\Product::class, 'withoutPrice', function (Faker $faker) {
    return [
        'data' => [
            'type' => 'products',
            'attributes' => [
                'name' => $faker->name,
                'price' => null
            ]
        ]
    ];
});
$factory->defineAs(App\Product::class, 'withoutNumPrice', function (Faker $faker) {
    return [
        'data' => [
            'type' => 'products',
            'attributes' => [
                'name' => $faker->name,
                'price' => $faker->word
            ]
        ]
    ];
});
$factory->defineAs(App\Product::class, 'subzero',function (Faker $faker) {
    return [
        'data' => [
            'type' => 'products',
            'attributes' => [
                'name' => $faker->name,
                'price' => $faker->randomFloat($nbMaxDecimals = 2, $min =-500, $max = 0)
            ]
        ]
    ];
});
