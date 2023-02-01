<?php

namespace App\Providers;

use App\Crawler\Handlers\ImagesHandler;
use App\Crawler\Handlers\LinksHandler;
use App\Crawler\Handlers\TitleHandler;
use App\Crawler\Handlers\WordCountHandler;
use App\Crawler\Queue;
use App\Crawler\Crawler;
use App\Crawler\Result;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Crawler::class, function () {
            $request = $this->app->make(Request::class);
            $queue = new Queue($request->page_count);
            return new Crawler(
                $queue,
                new LinksHandler(),
                new TitleHandler(),
                new WordCountHandler(),
                new ImagesHandler()
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
