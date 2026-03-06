<?php

declare(strict_types=1);

namespace ADS\TakeHome\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    #[Test]
    public function example(): void
    {
        $this->assertSame(2, 1 + 1);
    }
}
