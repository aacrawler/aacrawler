<?php

namespace App\Crawler;

use App\Crawler\Exceptions\CrawlerException;
use App\Crawler\Handlers\Contracts\HandlerInterface;
use DOMDocument;
use DOMXPath;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class Crawler
{
    private Queue $queue;
    private array $handlers;
    /**
     * Keeps track of all request times
     *
     * @var array
     */
    private array $requestTime;
    /**
     * host we are crawling
     *
     * @var string
     */
    private string $host;
    /**
     * Keeps trac of all crawled pages ad status codes
     *
     * @var array
     */
    private array $crawledPages = [];

    /**
     * @param Queue $queue Queue
     * @param HandlerInterface ...$handler List of handlers
     */
    public function __construct(Queue $queue, HandlerInterface ...$handler)
    {
        $this->queue = $queue;
        $this->handlers = $handler;
    }

    /**
     * Crawls first url, fill up the queue and process the queue
     *
     * @param string $url Starting point to crawl
     * @return void
     * @throws CrawlerException
     */
    public function crawl(string $url): void
    {
        $this->host = parse_url($url, PHP_URL_HOST);

        $this->queue->add($url);

        $this->populateQueue($url);

        $this->processQueue();
    }

    /**
     * Returns all the results from all the handlers and crawled pages
     *
     * @return Result
     */
    public function getResults(): Result
    {
        $result = [];
        $result['pages'] = count($this->crawledPages);
        $result['time'] = number_format(array_sum($this->requestTime) / count($this->requestTime), 2);

        foreach ($this->handlers as $handler) {
            $handlerResult = $handler->getResult();
            foreach ($handlerResult as $key => $value) {
                $result[$key] = $value;
            }
        }
        return new Result($result, $this->crawledPages);
    }

    /**
     * Populates the queue starting with first URL
     *
     * @param string $url First URL to get rest of the pages
     * @return void
     * @throws CrawlerException
     */
    private function populateQueue(string $url): void
    {
        $domDoc = $this->loadUrl($url);
        $xpath = new DOMXPath($domDoc);
        $aTags = $xpath->evaluate('/html/body//a');
        for ($i = 0; $i < $aTags->length; $i++) {
            if ($this->isQueueable($aTags->item($i)->getAttribute('href'))) {
                $parsedUrl = parse_url($aTags->item($i)->getAttribute('href')) ?: throw new CrawlerException(
                    "Can't parse URL " . $aTags->item($i)->getAttribute('href')
                );
                if (!$this->queue->isFull()) {
                    $this->queue->add('https://' . $this->host . ($parsedUrl['path'] ?? ''));
                } else {
                    return;
                }
            }
        }
        if (!$this->queue->isFull() && !$this->queue->last()) {
            $this->queue->next();
            $this->populateQueue($this->queue->current());
        }
    }

    /**
     * Checks if we should queue the URL
     *
     * @param string $href
     * @return bool
     */
    private function isQueueable(string $href): bool
    {
        $parsedUrl = parse_url($href);
        //check if link is internal.
        if (isset($parsedUrl['host']) && $parsedUrl['host'] === $this->host) {
            return true;
        }
        if (!isset($parsedUrl['host']) && !empty($parsedUrl['path'])) {
            return true;
        }
        return false;
    }

    /**
     * Loads the URL, adds url, response time and status code to the trackers
     *
     * @param string $url URL to fetch
     * @param bool $addResult Indicates if we need to keep stats for the fetched url
     * @return DOMDocument Fetched html loaded into DOMDocument
     * @throws CrawlerException
     */
    private function loadUrl(string $url, bool $addResult = false): DOMDocument
    {
        try {
            $response = Http::get($url);
        } catch (ConnectionException | RequestException $exception) {
            throw new CrawlerException("Can't load the url $url");
        }

        $this->requestTime[] = $response->transferStats->getTransferTime();
        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($response->body()) ?: throw new CrawlerException("Can't load html");
        if ($addResult) {
            $this->crawledPages[] = ['url' => $url, 'responseCode' => $response->status()];
        }
        return $doc;
    }

    /**
     * Processing the queeue
     *
     * @return void
     * @throws CrawlerException
     * @throws Exceptions\QueueException
     */
    private function processQueue(): void
    {
        $this->queue->rewind();
        foreach ($this->queue as $url) {
            $domDoc = $this->loadUrl($url, true);
            $this->passTrough($domDoc);
            $this->queue->remove($url);
        }
    }

    /**
     * Passing fetched html into each of the handlers
     *
     * @param DOMDocument $domDoc fetched html loaded into DOMDocument
     * @return void
     */
    private function passTrough(DOMDocument $domDoc): void
    {
        foreach ($this->handlers as $handler) {
            $handler->handle($domDoc, $this->host);
        }
    }
}
