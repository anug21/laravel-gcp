<?php

namespace App\Providers;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Pennant\Feature;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(Client::class, function () {
            return ClientBuilder::create()
                ->setHosts(config('database.connections.elasticsearch.hosts'))
                ->build();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production') || $this->app->environment('staging')) {
            URL::forceScheme('https');
            $this->app['request']->server->set('HTTPS', 'on');

            $this->features();
        }
    }

    private function features(): void
    {
        // define global (artificial Pennant scope) features for the current user scope
        $features = DB::select("SELECT * FROM features WHERE scope='__global'");
        foreach ($features as $feature) {
            Feature::define($feature->name, $feature->value === 'true');
        }
    }
}
