<?php

namespace App\Providers;

use App\Actions\Fetchers\FNSNotamFetcher;
use App\Actions\Fetchers\ICAONotamFetcher;
use App\Actions\Taggers\PretendNotamTagger;
use App\Contracts\NotamTagger;
use App\Contracts\PullNotamFetcher;
use App\Contracts\PushNotamFetcher;
use App\Models\User;
use Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public array $bindings = [
        PushNotamFetcher::class => FNSNotamFetcher::class,
        PullNotamFetcher::class => ICAONotamFetcher::class, //not in use anymore
        NotamTagger::class      => PretendNotamTagger::class,
    ];

    public array $singletons = [
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('viewPulse', function (User $user) {
            return in_array($user->email, [
                config('horizon.admin_email'),
            ]);
        });
    }
}
