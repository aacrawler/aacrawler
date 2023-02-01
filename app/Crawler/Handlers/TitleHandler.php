<?php

namespace App\Crawler\Handlers;

use App\Crawler\Handlers\Contracts\HandlerInterface;
use DOMDocument;
use DOMXPath;

class TitleHandler implements HandlerInterface
{
    private const LABEL = 'Avg title length';

    private array $titles = [];

    public function handle(DOMDocument $domDoc, string $host): void
    {
        $xpath = new DOMXPath($domDoc);
        $titles = $xpath->evaluate('/html//title');
        
        if (count($titles) === 0) {
            $this->titles[] = 0;
        } else {
            $this->titles[] = strlen(trim(str_replace(['"', "'"], '', $titles[0]->nodeValue)));
        }
    }

    public function getResult(): array
    {
        return [self::LABEL => array_sum($this->titles) / count($this->titles)];
    }
}
