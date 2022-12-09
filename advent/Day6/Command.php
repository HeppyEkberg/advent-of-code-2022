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

        [$part1, $part1iterations] = Str::of($puzzle)
            ->trim()
            ->split(1)
            ->reduceSpread($this->firstUniqueCharacters(4), collect(), 0);

        [$part2, $part2iterations] = Str::of($puzzle)
            ->trim()
            ->split(1)
            ->reduceSpread($this->firstUniqueCharacters(14), collect(), 0);

        $this->info("Part 1 marker detected after: {$part1iterations} (".$part1->implode('').")");
        $this->info("Part 2 marker detected after: {$part2iterations} (".$part2->implode('').")");
    }

    public function firstUniqueCharacters(int $characters) {
        return function (Collection $previous, $iterations, $character) use ($characters) {
            if($previous->unique()->count() == $characters) {
                return [$previous, $iterations];
            }

            if($previous->count() > ($characters - 1)) {
                $previous->shift();
            }

            $previous->add($character);
            $iterations = $iterations + 1;

            return [$previous, $iterations];
        };
    }
}


