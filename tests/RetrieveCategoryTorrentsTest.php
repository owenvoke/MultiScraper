<?php

namespace YeTii\MultiScraper;

use PHPUnit\Framework\TestCase;

/**
 * Class RetrieveCategoryTorrentsTest
 */
class RetrieveCategoryTorrentsTest extends TestCase
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
     * Test whether the category() method is working correctly
     */
    public function testCanRetrieveCategoryTorrents()
    {
        $results = $this->instance->category(Category::MOVIES);
        $this->assertArrayHasKey(0, $results);
        $this->assertInstanceOf(stdClass::class, $results[0]);
    }
}
