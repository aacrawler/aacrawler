<?php

namespace Tests\Feature;

use App\Crawler\Handlers\TitleHandler;
use DOMDocument;
use Tests\TestCase;

class TitleHandlerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testTitleHandler(): void
    {
        $wordCount = new TitleHandler();
        $domDoc = new DOMDocument();
        $domDoc->loadHTML('<html><title>This is test</title><div>Hello Word</div></html>');
        $wordCount->handle($domDoc, '');

        $domDoc = new DOMDocument();
        $domDoc->loadHTML('<html><title>new title.</title><div>Hello Word</div></html>');
        $wordCount->handle($domDoc, '');

        $this->assertEquals(['Avg title length' => 11], $wordCount->getResult());
    }
}
