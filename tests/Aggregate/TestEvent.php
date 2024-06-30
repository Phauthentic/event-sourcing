<?php

declare(strict_types=1);

namespace Phauthentic\EventSourcing\Test\Aggregate;

/**
 *
 */
class TestEvent
{
    public function __construct(private readonly string $text = '')
    {
    }

    public function getText()
    {
        return $this->text;
    }
}
