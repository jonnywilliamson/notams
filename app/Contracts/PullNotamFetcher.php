<?php

namespace App\Contracts;

use App\DTO\NormalisedNotam;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

abstract class PullNotamFetcher
{
    /**
     * @return Collection<int, NormalisedNotam>
     */
    abstract public function get(Collection $icaoLocations): Collection;

    abstract protected function normaliseNotams(Response $response): Collection;

    protected function reportError(Response $response): void
    {
        //TODO - Where/who do we want to notify these errors to?
        Log::error(
            'Error retrieving Notams from server.',
            [$response->status(), $response->reason(), $response->body()]
        );
    }

    protected function log(Response $response): void
    {
        Storage::disk('local')->put('responses/'.now()->format('Y-m-d_H_i_s').'.json', $response->body());
    }
}
