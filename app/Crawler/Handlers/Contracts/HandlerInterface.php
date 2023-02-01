<?php

namespace App\Crawler\Handlers\Contracts;

use DOMDocument;

interface HandlerInterface
{
    /**
     * Method to process html
     *
     * @param DOMDocument $domDoc Fetched html loaded into DOMDocument
     * @param string $host hostname of the crawled website
     * @return void
     */
    public function handle(DOMDocument $domDoc, string $host): void;

    /**
     * Returns aggregated result
     *
     * @return array
     */
    public function getResult(): array;
}
