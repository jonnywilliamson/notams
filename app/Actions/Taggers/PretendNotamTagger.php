<?php

namespace App\Actions\Taggers;

use App\Contracts\NotamTagger;
use App\DTO\TagData;
use App\Enum\LLM;
use App\Models\Notam;
use App\OpenAI\Tags;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use JsonException;

class PretendNotamTagger extends NotamTagger
{
    protected Notam $notam;

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function tag(Notam $notam): void
    {
        $this->notam = $notam;

        $this->updateNotam($this->pretendTaggedData());

        $this->logData();
    }

    protected function logData(): void
    {
        //        Log::info(sprintf('Tag Success: %s', $this->notam->id));
    }

    public function setLLM(LLM $llm): static
    {
        $this->llm = $llm;

        return $this;
    }

    protected function pretendTaggedData(): TagData
    {
        $randomTag = Tags::all()->random();

        return new TagData(
            id: $this->notam->id,
            type: $randomTag[1],
            code: $randomTag[0],
            summary: Str::title(fake()->words(7, true)),
        );
    }
}
