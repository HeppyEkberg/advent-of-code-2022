<?php

namespace Advent\Day6;

use Illuminate\Console\Command as Cmd;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Str;

class Command extends Cmd
{
    protected $signature = 'day:6';
    protected $description = '';

    public Collection $stacks;
    public function handle()
    {
        $puzzle = File::get(dirname(__FILE__) . '/puzzle');

        [$previous, $iterations] = Str::of($puzzle)
            ->trim()
            ->split(1)
            ->reduceSpread(function (Collection $previous, $iterations, $character) {
                if($previous->unique()->count() == 4) {
                    return [$previous, $iterations];
                }

                if($previous->count() > 3) {
                    $previous->shift();
                }

                $previous->add($character);
                $iterations = $iterations + 1;

                return [$previous, $iterations];
            }, collect(), 0);

        dd($previous, $iterations, strlen($puzzle));
    }

}


