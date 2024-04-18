<?php

namespace App\DTO;

use Illuminate\Support\Str;
use Spatie\LaravelData\Data;

class FnsXmlNotam extends Data
{
    public function __construct(
        public ?string $beginPosition = null,
        public ?string $endPosition = null,
        public ?string $interpretation = null,
        public ?string $scenario = null,
        public ?string $series = null,
        public ?string $number = null,
        public ?string $year = null,
        public ?string $type = null,
        public ?string $issued = null,
        public ?string $affectedFIR = null,
        public ?string $selectionCode = null,
        public ?string $traffic = null,
        public ?string $purpose = null,
        public ?string $scope = null,
        public ?string $minimumFL = null,
        public ?string $maximumFL = null,
        public ?string $coordinates = null,
        public ?string $radius = null,
        public ?string $location = null,
        public ?string $effectiveStart = null,
        public ?string $effectiveEnd = null,
        public ?string $text = null,
        public ?string $translationType = null,
        public ?string $simpleText = null,
        public ?string $formattedText = null,
        public ?string $classification = null,
        public ?string $accountId = null,
        public ?string $airportname = null,
        public ?string $originID = null,
        public ?string $lastUpdated = null,
        public ?string $icaoLocation = null,
    ) {
    }

    public function notamId(): string
    {
        $notamNumber = Str::padLeft($this->number, 4, '0');
        $notamYear = Str::substr($this->year, 2, 2);

        return "{$this->series}{$notamNumber}/{$notamYear}-".Str::random(6);
    }

    public function fullText(): string
    {
        $fullText = $this->formattedText ?? $this->text;

        return str($fullText)->stripTags()->trim()->value();
    }
}
