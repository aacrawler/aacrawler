<?php

namespace App\Crawler\Handlers;

use DOMDocument;
use DOMXPath;
use App\Crawler\Handlers\Contracts\HandlerInterface;

class LinksHandler implements HandlerInterface
{
    private const LABEL_INTERNAL = 'Internal links';
    private const LABEL_EXTERNAL = 'External links';

    private array $internalLinks = [];
    private array $externalLinks = [];

    public function handle(DOMDocument $domDoc, string $host): void
    {
        $xpath = new DOMXPath($domDoc);
        $aTags = $xpath->evaluate('/html/body//a');
        for ($i = 0; $i < $aTags->length; $i++) {
            $href = $aTags->item($i)->getAttribute('href');
            if (empty($href) || $href[0] === '#') {
                continue;
            }
            $this->sort($href, $host);
        }
    }

    public function getResult(): array
    {
        return [
            self::LABEL_INTERNAL => count($this->internalLinks),
            self::LABEL_EXTERNAL => count($this->externalLinks)
        ];
    }

    /**
     * Sorts links between internal and external
     *
     * @param string $href
     * @param string $host
     * @return void
     */
    private function sort(string $href, string $host): void
    {
        $parsedUrl = parse_url($href);
        if ($this->isInternal($parsedUrl, $host)) {
            $this->internalLinks[$parsedUrl['path']] = $parsedUrl['path'];
        } else {
            $this->externalLinks[$href] = $href;
        }
    }

    /**
     * Detects if link is internal
     *
     * @param array $parsedUrl
     * @param string $host
     * @return bool
     */
    private function isInternal(array $parsedUrl, string $host): bool
    {
        if (isset($parsedUrl['host']) && $parsedUrl['host'] === $host) {
            return true;
        }
        if (!isset($parsedUrl['host']) && !empty($parsedUrl['path'])) {
            return true;
        }
        return false;
    }
}
