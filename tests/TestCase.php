<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestCase extends WebTestCase
{
    protected $container;


    protected function setUp(): void
    {
        $this->container = $this->getContainer();
    }

    protected function get(string $class) {
        return $this->container->get($class);
    }
}
