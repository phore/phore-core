<?php

namespace Phore\Core\Exception;

class YamlDecodeException extends \InvalidArgumentException
{
    private $errorLine;
    private $errorColumn;
    private $errorSourceLine;

    public function __construct(
        string $message,
        ?int $errorLine = null,
        ?int $errorColumn = null,
        ?string $errorSourceLine = null,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
        $this->errorLine = $errorLine;
        $this->errorColumn = $errorColumn;
        $this->errorSourceLine = $errorSourceLine;
    }

    public function getErrorLine(): ?int
    {
        return $this->errorLine;
    }

    public function getErrorColumn(): ?int
    {
        return $this->errorColumn;
    }

    public function getErrorSourceLine(): ?string
    {
        return $this->errorSourceLine;
    }
}
