<?php

namespace App\Exceptions;

use RuntimeException;

class InsufficientDriverBalanceException extends RuntimeException
{
    public function __construct(
        public readonly int $requiredCents,
        public readonly int $availableCents,
    ) {
        parent::__construct(
            'The driver does not have enough wallet balance.'
        );
    }
}