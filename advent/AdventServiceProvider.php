<?php

namespace Advent;

use Illuminate\Support\ServiceProvider;

class AdventServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            Day1\Command::class,
            Day2\Command::class,
            Day3\Command::class,
            Day4\Command::class,
            Day5\Command::class,
            Day6\Command::class,
        ]);
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
