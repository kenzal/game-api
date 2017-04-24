<?php
namespace Tests\Traits;

use Faker\Factory;
use Faker\Generator;

/**
 * Data Faker Trait for use with unit tests
 */
trait Faker
{
    /**
     * Faker Generator
     *
     * @var Generator
     */
    protected $faker;

    /**
     * Initializes Faker Generator before each test
     *
     * @before
     */
    public function setUpFaker() : void
    {
        $this->faker = Factory::create();
    }

    /**
     * Returns the Faker Generator
     *
     * @return Generator Faker Generator
     */
    public function getFaker() : Generator
    {
        return $this->faker;
    }
}
