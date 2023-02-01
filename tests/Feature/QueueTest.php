<?php

namespace Tests\Feature;

use App\Crawler\Exceptions\QueueException;
use App\Crawler\Queue;

use Tests\TestCase;

class QueueTest extends TestCase
{
    public function testQueueIsFull(): void
    {
        $queue = new Queue(1);
        $queue->add('https://test.com');

        $this->assertTrue($queue->isFull());
    }

    public function testQueueAddDuplicate(): void
    {
        $queue = new Queue(1);
        $queue->add('https://test.com');

        $this->assertFalse($queue->add('https://test.com'));
    }

    public function testQueueRemove(): void
    {
        $queue = new Queue(1);
        $queue->add('https://test.com');
        $queue->add('https://test.ca');
        $this->assertTrue($queue->remove('https://test.com'));

        $this->expectException(QueueException::class);
        $queue->remove('https://test.ca');
    }
}
