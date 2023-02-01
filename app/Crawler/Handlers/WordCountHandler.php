<?php

namespace App\Crawler\Handlers;

use App\Crawler\Handlers\Contracts\HandlerInterface;
use DOMDocument;
use DOMXPath;

class WordCountHandler implements HandlerInterface
{
    private const LABEL = 'Avg word count';

    private array $wordCounts = [];

    public function handle(DOMDocument $domDoc, string $host): void
    {
        $count = 0;
        $xpath = new DOMXPath($domDoc);
        $scripts = $xpath->evaluate('/html/body/script');
        foreach ($scripts as $scriptNode) {
            $scriptNode->parentNode->removeChild($scriptNode);
        }
        $textnodes = $xpath->evaluate('/html/body/span|/html/body/div|/html/body/li|/html/body/a');
        foreach ($textnodes as $textnode) {
            $count += str_word_count(trim($textnode->textContent));
        }
        $this->wordCounts[] = $count;
    }

    public function getResult(): array
    {
        return [self::LABEL => array_sum($this->wordCounts) / count($this->wordCounts)];
    }
}
