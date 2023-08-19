<?php

namespace Tests\Traits;

use Faker\Generator;

trait Faker
{
    protected ?Generator $faker = null;

    protected function faker(): Generator
    {
        if(is_null($this->faker)) {
            $this->faker = app()->make(Generator::class);
        }

        return $this->faker;
    }
}
