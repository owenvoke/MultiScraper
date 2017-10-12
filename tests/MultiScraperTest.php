<?php

use PHPUnit\Framework\TestCase;
use YeTii\MultiScraper\MultiScraper;

/**
 * Class MultiScraperTest
 */
class MultiScraperTest extends TestCase
{
    /**
     * @var MultiScraper
     */
    protected static $instance;

    /**
     * Test whether an instance of the MultiScraper can be initialised
     */
    public function testCanInitialiseInstance()
    {
        self::$instance = new MultiScraper();

        $this->assertInstanceOf(MultiScraper::class, self::$instance);
    }

    /**
     * Test whether the latest() method is working correctly
     */
    public function testCanGetLatest()
    {
        $results = self::$instance->latest();
        $this->assertArrayHasKey(0, $results);
        $this->assertInstanceOf(stdClass::class, $results[0]);
    }

    /**
     * Test whether the user() method is working correctly
     */
    public function testCanGetByUsername()
    {
        $results = self::$instance->user('ettv');
        $this->assertArrayHasKey(0, $results);
        $this->assertInstanceOf(stdClass::class, $results[0]);
    }

    /**
     * Test whether the search() method is working correctly
     */
    public function testCanGetBySearchQuery()
    {
        $results = self::$instance->user('ettv');
        $this->assertArrayHasKey(0, $results);
        $this->assertInstanceOf(stdClass::class, $results[0]);
    }
}