<?php

namespace App\Actions\Fetchers;

use App\Contracts\PushNotamFetcher;
use App\DTO\NormalisedNotam;
use App\Models\FnsNotam;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Facades\Storage;
use Throwable;

class FNSNotamFetcher extends PushNotamFetcher
{
    public function get(?CarbonInterface $time = null): Enumerable
    {
        try {

            return FnsNotam::where('storedtimestamp', '>=', $this->calculateStartTime($time))
                ->orderBy('storedtimestamp')
                ->cursor()
                ->map(fn (FnsNotam $fnsNotam) => $this->normaliseNotam($fnsNotam));

        } catch (Throwable $exception) {
            report($exception);

            return collect();
        }
    }

    /**
     * If the user provides a time then we will just accept that as the start time to look
     * for new notams from the fns database.
     *
     * Otherwise, we will attempt to get the timestamp from the last notam that was taken from the
     * fns database and saved to a text file. We will take 200 milliseconds off it just to cover
     * any other notams that might have been written to the database very close to the last one we
     * took.
     *
     * If there are any issues with parsing the timestamp etc, we just reset and grab all the notams
     * again. (i.e. starting from the epoch)
     */
    protected function calculateStartTime(?CarbonInterface $time): CarbonInterface
    {
        try {
            if ($time) {
                return $time;
            }

            return CarbonImmutable::createFromTimestampMs(
                Storage::disk('local')->get('FNS_Timestamp.txt')
            )
                ->subMilliseconds(200);

        } catch (Throwable $exception) {

            return CarbonImmutable::parse(0);
        }
    }

    protected function normaliseNotam($rawNotam): NormalisedNotam
    {
        //TODO
        //Struggling to refactor how to save the latest FNS notam storedtimestamp due to
        //using cursor on the eloquent query.
        Storage::disk('local')->put('FNS_Timestamp.txt', $rawNotam->storedtimestamp->getTimestampMs());

        $xmlData = $rawNotam->aixmnotammessage;

        return new NormalisedNotam(
            id: $xmlData->notamId(),
            fullText: $xmlData->fullText(),
            source: $rawNotam->toJson(),
        );
    }
}
