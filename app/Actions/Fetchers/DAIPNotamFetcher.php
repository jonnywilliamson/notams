<?php

namespace App\Actions\Fetchers;

use App\Contracts\PullNotamFetcher;
use App\DTO\NormalisedNotam;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class DAIPNotamFetcher extends PullNotamFetcher
{
    /**
     * @return Collection<int, NormalisedNotam>
     */
    public function get(Collection $icaoLocations): Collection
    {
        $response = Http::asJson()
            ->withoutVerifying()
            ->withUserAgent(config('app.user-agent'))
            ->connectTimeout(60)
            ->timeout(60)
            ->post('https://www.daip.jcs.mil/daip/mobile/query', [
                'type' => 'LOCATION',
                'locs' => $icaoLocations->flatten()->unique()->implode(','),
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
        return collect($response->json('group'))
            ->pluck('notams.0') //Data is buried in another array
            ->mapWithKeys(fn (array $notamData) => [$notamData['code'] => $notamData['list']])
            ->map(fn ($notams, $icaoLocation) => $this->createStandardNotam($notams, $icaoLocation))
            ->collapse()
            ->sort()
            ->values();
    }

    protected function createStandardNotam($notams, $icaoLocation): Collection
    {
        return collect($notams)
            ->map(fn (array $notamData) => new NormalisedNotam(
                id: "{$notamData['id']}-$icaoLocation",
                fullText: trim($notamData['rawtext']),
                source: json_encode($notamData)
            ));
    }
}
