<?php

namespace Alhames\FilterBundle\Tests;

use Alhames\FilterBundle\FilterManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FilterManagerTest extends WebTestCase
{
    public function testClass(): void
    {
        $manager = $this->getManager();
        $this->assertInstanceOf(FilterManager::class, $manager);
    }

    public function getManager(): FilterManager
    {
        return self::getContainer()->get(FilterManager::class);
    }
}
