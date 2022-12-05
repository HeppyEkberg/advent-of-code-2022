<?php

namespace Advent\Day2;

use Illuminate\Console\Command as Cmd;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Command extends Cmd
{
    protected $signature = 'day:2';
    protected $description = '';

    public function handle()
    {
        $puzzle = File::get(dirname(__FILE__) . '/puzzle');

        $elvesCalories = Str::of($puzzle)->explode(PHP_EOL . PHP_EOL)
            ->map(fn($elf) => Str::of($elf)->explode(PHP_EOL)->map(fn($calories) => (int) $calories)->sum())
            ->sortDesc();

        $this->info("Carried calories is: {$elvesCalories->first()}");

        $top3 = $elvesCalories->take(3)->sum();

        $this->info("Top 3 carries {$top3} calories together");
    }
}
