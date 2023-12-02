<?php

namespace Phore\Core\Helper;

/**
 * Class PhoreEMail
 *
 * This class is designed to parse and handle email addresses in various formats.
 * It provides methods to extract and validate email addresses and names.
 *
 * Usage:
 * $emailAddress = new PhoreEMailAddress('John Doe <john.doe@example.com>');
 * echo 'Email: ' . $emailAddress->getEmail() . PHP_EOL;
 * echo 'Name: ' . $emailAddress->getName() . PHP_EOL;
 * echo 'Is valid: ' . ($emailAddress->isValid() ? 'Yes' : 'No') . PHP_EOL;
 */
class PhoreEMailAddress
{
    private string $rawInput;
    private ?string $email;
    private ?string $name;

    public function __construct(string $emailInput) {
        $this->rawInput = $emailInput;
        $this->extractEmailAndName();
    }

    private function extractEmailAndName(): void {
        $pattern = '/(?<name>.*?)<?(?<email>[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+)>?/';
        if (preg_match($pattern, $this->rawInput, $matches)) {
            $this->email = $matches['email'];
            $this->name = trim($matches['name']);
        } else {
            $this->email = null;
            $this->name = null;
        }
    }

    public function getEMailNormalized(): ?string {
        if ($this->email === null) {
            return null;
        }
        $email = strtolower($this->email);
        $email = str_replace(' ', '', $email);
        return $email;
    }
    
    public function getEmail(): ?string {
        return $this->email;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function validate(\Exception $exception = null): self {
        if ($this->email === null) {
            if ($exception !== null) {
                throw $exception;
            }
            throw new \InvalidArgumentException('Invalid email address: "$this->rawInput"');
        }
        return $this;
    } 
    
    public function isValid(): bool {
        return filter_var($this->email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
