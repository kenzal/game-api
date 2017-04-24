<?php
namespace Tests\Traits;

use Faker\Factory;

trait Faker
{
    protected $faker;

    /**
     * @before
     */
    public function setUpFaker()
    {
        $this->faker = Factory::create();
    }

    public function getFaker()
    {
        return $this->faker;
    }
}
