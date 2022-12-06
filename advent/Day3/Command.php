<?php

namespace Advent\Day3;

use Illuminate\Console\Command as Cmd;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

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
        $totalBadges = $this->part2($rucksacks);
        $this->info("Total priority is: {$total}");
        $this->info("Total group badges is: {$totalBadges}");
    }

    public function part2(Collection $rucksacks): ?int
    {
        return $rucksacks->chunk(3)
            ->map(function ($group) {
                $items = $group->map(fn($rucksack) => array_merge(...$rucksack['compartments']))->values();
                $badge = collect($items->get(0))->intersect($items->get(1))->intersect($items->get(2))->unique()->first();

                return [
                    'group' => $group,
                    'badge' => $badge,
                    'priority' => $this->priority($badge),
                ];
            })
            ->sum('priority');
    }

    private function priority(?string $type): ?int
    {
        if(ctype_upper($type)) {
            return ord($type) - 38;
        }

        return ord($type) - 96;
    }
}
