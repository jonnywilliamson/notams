<?php

namespace App\Actions\Fetchers;

use App\Contracts\PullNotamFetcher;
use App\DTO\NormalisedNotam;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class ICAONotamFetcher extends PullNotamFetcher
{
    /**
     * @return Collection<int, NormalisedNotam>
     */
    public function get(Collection $icaoLocations): Collection
    {
        $response = Http::withUserAgent(config('app.user-agent'))
            ->connectTimeout(60)
            ->timeout(60)
            ->get('https://api.anbdata.com/anb/states/notams/notams-realtime-list', [
                'api_key'   => config('NotamsSource.icao_api_key'),
                'locations' => $icaoLocations->flatten()->unique()->implode(','),
            ]);

        if ($response->failed()) {
            $this->reportError($response);

            return collect();
        }

        $this->log($response);

        return $this->normaliseNotams($response);
    }

    /**
     * @return Collection<int, NormalisedNotam>
     */
    protected function normaliseNotams(Response $response): Collection
    {
        return collect($response->object())
            ->map(fn ($sourceNotam) => new NormalisedNotam(
                id: $sourceNotam->key,
                fullText: $sourceNotam->all,
                source: json_encode($sourceNotam),
            ))
            ->sort()
            ->values();
    }
}
