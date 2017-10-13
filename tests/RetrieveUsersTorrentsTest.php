<?php

use PHPUnit\Framework\TestCase;
use YeTii\MultiScraper\MultiScraper;

/**
 * Class RetrieveUsersTorrentsTest
 */
class RetrieveUsersTorrentsTest extends TestCase
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
     * Test whether the user() method is working correctly
     */
    public function testCanRetrieveUsersTorrents()
    {
        $results = $this->instance->user('ettv');
        $this->assertArrayHasKey(0, $results);
        $this->assertInstanceOf(stdClass::class, $results[0]);
    }
}