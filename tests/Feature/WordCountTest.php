<?php

namespace Tests\Feature;

use App\Crawler\Handlers\WordCountHandler;
use DOMDocument;
use Tests\TestCase;

class WordCountTest extends TestCase
{
    public function testWordCount(): void
    {
        $wordCount = new WordCountHandler();
        $domDoc = new DOMDocument();
        $domDoc->loadHTML('<html><body><span>This is test</span><div>Hello Word</div></body></html>');
        $wordCount->handle($domDoc, '');

        $domDoc = new DOMDocument();
        $domDoc->loadHTML('<html><body><span>Second <a>page</a></span><div>test</div></body></html>');
        $wordCount->handle($domDoc, '');

        $this->assertEquals(['Avg word count' => 4], $wordCount->getResult());
    }
}
