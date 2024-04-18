<?php

use App\Jobs\NewNotamFetcherJob;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new NewNotamFetcherJob())
    ->everyFiveMinutes();
