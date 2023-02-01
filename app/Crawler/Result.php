<?php

namespace App\Crawler;

class Result
{
    private array $aggregatedStats = [];
    private array $pageStats = [];

    /**
     * @param array $aggregatedStats Aggregated stats from all the handlers
     * @param array $pageStats List of all crawled urls with their status codes
     */
    public function __construct(array $aggregatedStats, array $pageStats)
    {
        $this->aggregatedStats = $aggregatedStats;
        $this->pageStats = $pageStats;
    }

    /**
     * Provides aggregated stats from all the handlers
     *
     * @return array
     */
    public function getStats(): array
    {
        return $this->aggregatedStats;
    }

    /**
     * Provides list of all crawled urls with their status codes
     *
     * @return array
     */
    public function getPageStats(): array
    {
        return $this->pageStats;
    }
}
