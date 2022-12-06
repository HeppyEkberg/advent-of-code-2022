<?php

namespace Advent\Day3;

use Illuminate\Console\Command as Cmd;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Command extends Cmd
{
    protected $signature = 'day:3';
    protected $description = '';

    public function handle()
    {
        $puzzle = File::get(dirname(__FILE__) . '/puzzle');

        $rucksacks = collect(explode(PHP_EOL, $puzzle))
            ->filter()
            ->map(fn($items) => collect(str_split($items))->chunk(strlen($items) / 2))
            ->map(function ($rucksack) {
                $type = $rucksack[0]->intersect($rucksack[1])->unique()->first();

                return [
                    'compartments' => $rucksack->toArray(),
                    'type' => $type,
                    'priority' => $this->priority($type),
                ];
            });

        $total = $rucksacks->sum('priority');
        $this->info("Total priority is: {$total}");
    }

    private function priority(?string $type): ?int
    {
        if(ctype_upper($type)) {
            return ord($type) - 38;
        }

        return ord($type) - 96;
    }
}
