<?php

namespace App\Crawler;

use App\Crawler\Exceptions\QueueException;
use Iterator;

class Queue implements Iterator
{
    private int $position = 0;
    private array $urls = [];
    private int $length = 6;

    /**
     * @param int $length Size of the queue
     */
    public function __construct(int $length)
    {
        $this->length = $length;
    }

    public function current(): mixed
    {
        return $this->urls[$this->position];
    }

    public function next(): void
    {
        $this->position++;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return isset($this->urls[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    /**
     * Adds element into the queue
     *
     * @param string $url Element to add
     * @return bool
     */
    public function add(string $url): bool
    {
        $url = trim($url);
        if (count($this->urls) >= $this->length) {
            return false;
        }
        if (in_array($url, $this->urls, true)) {
            return false;
        }

        $this->urls[] = $url;
        return true;
    }

    /**
     * Removes element from the queue
     *
     * @param string $url Element to remove
     * @return bool
     * @throws QueueException
     */
    public function remove(string $url): bool
    {
        $index = array_search($url, $this->urls, true);

        if (!in_array($url, $this->urls, true)) {
            throw new QueueException("Url $url is not found in the queue");
        }
        unset($this->urls[$index]);
        return true;
    }

    /**
     * Checks if queue is full
     *
     * @return bool
     */
    public function isFull(): bool
    {
        return count($this->urls) >= $this->length;
    }

    /**
     * Checks if we reached last element of the queue
     *
     * @return bool
     */
    public function last(): bool
    {
        return $this->position === count($this->urls) - 1;
    }
}
