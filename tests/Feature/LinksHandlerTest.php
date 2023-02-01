<?php

namespace Tests\Feature;

use App\Crawler\Handlers\LinksHandler;
use DOMDocument;
use Tests\TestCase;

class LinksHandlerTest extends TestCase
{
    public function testLinks(): void
    {
        $links = new LinksHandler();
        $domDoc = new DOMDocument();
        $domDoc->loadHTML('<html><body><a href="/test">This is test</a><a href="">Hello Word</a><a href="https://test.com/contact"></a></body></html>');
        $links->handle($domDoc, 'test.com');

        $domDoc = new DOMDocument();
        $domDoc->loadHTML('<html><body><a href="/test">This is test</a><a href="">Hello Word</a><a href="https://exmple.com/contact"></a></body></html>');
        $links->handle($domDoc, 'test.com');

        $domDoc = new DOMDocument();
        $domDoc->loadHTML('<html><body><a href="/about">This is test</a><a href="#up">Hello Word</a><a href="https://exmple.com/contact"></a></body></html>');
        $links->handle($domDoc, 'test.com');

        $this->assertEquals(['Internal links' => 3, 'External links' => 1], $links->getResult());
    }
}
