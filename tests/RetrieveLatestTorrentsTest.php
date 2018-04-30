<?php

namespace YeTii\MultiScraper;

use PHPUnit\Framework\TestCase;

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
     * RetrieveLatestTorrentsTest constructor.
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
