<?php

namespace Advent;

use Advent\Day1\DayOneCommand;
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
            DayOneCommand::class,
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
