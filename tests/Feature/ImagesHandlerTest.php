<?php

namespace Tests\Feature;

use App\Crawler\Handlers\ImagesHandler;
use DOMDocument;
use Tests\TestCase;

class ImagesHandlerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testImageHandler(): void
    {
        $images = new ImagesHandler();
        libxml_use_internal_errors(true);
        $domDoc = new DOMDocument();
        $domDoc->loadHTML('<html><body><img src="/test.jpg"/><img src="data:base64"/><img href="https://test.com/contact.png"/></body></html>');
        $images->handle($domDoc, 'test.com');

        $domDoc = new DOMDocument();
        $domDoc->loadHTML('<html><body><img src="/test.jpg&w=180"/><svg xmlns="http://www.w3.org/2000/svg"><path d="Hello Word"></path></svg><img href="https://exmple.com/contact.jpg"/></body></html>');
        $images->handle($domDoc, 'test.com');

        $domDoc = new DOMDocument();
        $domDoc->loadHTML('<html><body><img src="/about.jpg"/><img src="new.png"/><img src="https://exmple.com/contact.jpg"/></body></html>');
        $images->handle($domDoc, 'test.com');

        $this->assertEquals(['Number of images' => 7], $images->getResult());
    }
}
