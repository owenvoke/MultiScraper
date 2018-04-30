<?php

namespace YeTii\MultiScraper;

use PHPUnit\Framework\TestCase;

/**
 * Class RetrieveSearchedTorrentsTest
 */
class RetrieveSearchedTorrentsTest extends TestCase
{
    /**
     * @var MultiScraper
     */
    protected $instance;

    /**
     * RetrieveUsersTorrentsTest constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->instance = new MultiScraper();
    }

    /**
     * Test whether the search() method is working correctly
     */
    public function testCanRetrieveSearchedTorrents()
    {
        $results = $this->instance->search('ettv');
        $this->assertArrayHasKey(0, $results);
        $this->assertInstanceOf(stdClass::class, $results[0]);
    }
}
