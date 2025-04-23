<?php

namespace App\Providers;

use Faker\Factory as FakerFactory;
use Faker\Generator;
use Illuminate\Support\ServiceProvider;

class FakerPhoneServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Generator::class, function () {
            $faker = FakerFactory::create();

            $faker->addProvider(new class($faker) extends \Faker\Provider\PhoneNumber
            {
                private function randFristDigitBetween($min, $max, $count)
                {
                    return rand($min, $max).$this->numerify(str_repeat('#', $count - 1));
                }

                public function validDutchMobileNumber()
                {
                    return '+316'.$this->randFristDigitBetween(1, 5, 8);
                }

                public function validBelgianMobileNumber()
                {
                    return '+324'.$this->randFristDigitBetween(5, 9, 8);
                }
            });

            return $faker;
        });
    }
}
