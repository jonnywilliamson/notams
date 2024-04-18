<?php

namespace App\Jobs;

use App\Contracts\PullNotamFetcher;
use App\Contracts\PushNotamFetcher;
use App\DTO\NormalisedNotam;
use App\Enum\LLM;
use App\Enum\NotamStatus;
use App\Models\Notam;
use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class NewNotamFetcherJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public int $timeout = 600;

    public function __construct(
        protected string|iterable|null $icaoIdents = null,
        protected LLM $llm = LLM::GPT_3_5_TURBO,
        protected ?CarbonInterface $dateTime = null,
    ) {
    }

    public function handle(): void
    {
        $this->notamsFromSource()
            ->each(function (NormalisedNotam $normalisedNotam) {
                $notam = $this->insertIntoDatabase($normalisedNotam);

                if ($notam->status !== NotamStatus::TAGGED) {
                    NotamTagJob::dispatch($notam, $this->llm);
                }
            });
    }

    /**
     * If we receive a list of locations we will use a pull source,
     * otherwise we use a push source with a dateTime
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function notamsFromSource(): Enumerable
    {
        if ($this->icaoIdents) {
            return app(PullNotamFetcher::class)->get($this->locations());
        }

        return app(PushNotamFetcher::class)->get($this->dateTime);
    }

    protected function locations(): Collection
    {
        return is_iterable($this->icaoIdents) ? collect($this->icaoIdents) : str($this->icaoIdents)->explode(',');
    }

    protected function insertIntoDatabase(NormalisedNotam $normalisedNotam): Notam
    {
        //Due to memory limitations when trying to import
        //the entire fns database on the first call, it was
        //found it was more reliable to do this than chunked
        //upserts.
        return Notam::firstOrCreate(
            ['id' => $normalisedNotam->id],
            [
                'fullText' => $normalisedNotam->fullText,
                'source'   => $normalisedNotam->source,
                'status'   => NotamStatus::PROCESSING,
            ]);
    }
}
