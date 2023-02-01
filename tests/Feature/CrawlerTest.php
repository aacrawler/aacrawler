<?php

namespace Tests\Feature;

use App\Crawler\Crawler;
use App\Crawler\Queue;
use App\Crawler\Result;
use Illuminate\Support\Facades\Http;

use Tests\TestCase;

class CrawlerTest extends TestCase
{
    public function testCrawl(): void
    {
        $queue = new Queue(3);
        Http::fake([
            'https://www.example.com' => Http::response('<html><body><a href="/test">This is test</a><a href="">Hello Word</a><a href="https://test.com/contact"></a></body></html>', 200),
        ]);

        Http::fake([
            'https://www.example.com/test' => Http::response('<html><body><a href="/deeper">This is test</a><a href="">Hello Word</a><a href="https://test.com/contact"></a></body></html>', 200),
        ]);
        Http::fake([
            'https://www.example.com/deeper' => Http::response('<html></html>', 404),
        ]);
        $crawler = $this->getMockBuilder(Crawler::class)
            ->onlyMethods(['loadUrl', 'populateQueue'])
            ->setConstructorArgs([$queue])
            ->getMock();

        $crawler->crawl('https://www.example.com');
        $result = $crawler->getResults();

        $this->assertInstanceOf(Result::class, $result);

        $this->assertEquals([
            ['url' => 'https://www.example.com', 'responseCode' => 200],
            ['url' => 'https://www.example.com/test', 'responseCode' => 200],
            ['url' => 'https://www.example.com/deeper', 'responseCode' => 404],
            ], $result->getPageStats());
        $this->assertEquals(['pages' => 3, 'time' => '0.00'], $result->getStats());
    }
}
