<?php

namespace App\Crawler\Handlers;

use DOMDocument;
use DOMXPath;
use App\Crawler\Handlers\Contracts\HandlerInterface;

class ImagesHandler implements HandlerInterface
{
    private const LABEL = 'Number of images';

    private array $images = [];

    public function handle(DOMDocument $domDoc, string $host): void
    {
        $xpath = new DOMXPath($domDoc);
        $tags = $xpath->evaluate('/html/body//img|/html/body//path');
        for ($i = 0; $i < $tags->length; $i++) {
            $src = $tags->item($i)->getAttribute('src') ? $tags->item($i)->getAttribute(
                'src'
            ) : $this->getUniqueSignature($tags->item($i)->getAttribute('d'));
            $src = $this->getUniqueSignature($src);
            $this->images[$src] = $src;
        }
    }

    public function getResult(): array
    {
        return [self::LABEL => count($this->images)];
    }

    /**
     * Generate unique signature for different types of images
     *
     * @param string $source
     * @return string
     */
    private function getUniqueSignature(string $source): string
    {
        if (str_starts_with($source, 'data')) {
            //embedded image
            return md5($source);
        }
        if (str_starts_with($source, 'http') || str_starts_with($source, '/')) {
            //regular image
            return preg_replace('/&.*/', '', $source);
        }
        //svg
        return md5($source);
    }
}
