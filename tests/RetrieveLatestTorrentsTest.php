<?php

use PHPUnit\Framework\TestCase;
use YeTii\MultiScraper\MultiScraper;

/**
 * Class RetrieveLatestTorrentsTest
 */
class RetrieveLatestTorrentsTest extends TestCase
{
    /**
     * @var MultiScraper
     */
    protected $instance;

    /**
     * Test whether an instance of the MultiScraper can be initialised
     */
    public function __construct()
    {
        parent::__construct();
        $this->instance = new MultiScraper();
    }

    /**
     * Test whether the latest() method is working correctly
     */
    public function testCanRetrieveLatestTorrents()
    {
        $results = $this->instance->latest();
        $this->assertArrayHasKey(0, $results);
        $this->assertInstanceOf(stdClass::class, $results[0]);
    }
}