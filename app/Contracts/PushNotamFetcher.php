<?php

namespace App\Contracts;

use App\DTO\NormalisedNotam;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Facades\Log;

abstract class PushNotamFetcher
{
    /**
     * @return Collection<int, NormalisedNotam>
     */
    abstract public function get(?CarbonInterface $time = null): Enumerable;

    abstract protected function normaliseNotam($rawNotam): NormalisedNotam;

    protected function reportError(): void
    {
        //TODO - Where/who do we want to notify these errors to?
        Log::error(
            'Error retrieving Notams from server.',
        );
    }
}
