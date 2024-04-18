<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class NormalisedNotam extends Data
{
    public function __construct(
        public string $id,
        public string $fullText,
        public string $source,
    ) {
    }
}
