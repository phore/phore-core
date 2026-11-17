<?php

namespace spec\Phore\Core\fixtures;

// Expose global functions as a PHPSpec subject without changing the library API.
class Functions
{
    public function value(mixed $value): mixed
    {
        return $value;
    }

    public function __call(string $name, array $arguments): mixed
    {
        return $name(...$arguments);
    }
}
