<?php

namespace Tests\Feature;

use App\Crawler\Result;
use Tests\TestCase;

class ResultTest extends TestCase
{
    public function testResult(): void
    {
        $result = new Result(['test stats'], ['test pages']);

        $this->assertEquals(['test stats'], $result->getStats());
        $this->assertEquals(['test pages'], $result->getPageStats());
    }
}
