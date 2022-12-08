<?php

namespace Advent\Day5;

use Illuminate\Console\Command as Cmd;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Str;

class Command extends Cmd
{
    protected $signature = 'day:5';
    protected $description = '';

    public Collection $stacks;
    public function handle()
    {
         // $this->tests();
        $this->stacks = collect();

        $puzzle = File::get(dirname(__FILE__) . '/puzzle');

        $stacks = $this->stacks($puzzle);
        $stacks2 = $this->stacks($puzzle);
        $moves = $this->moves($puzzle);

        $moves->each(fn(array $move) => $this->move($move, $stacks));
        $moves->each(fn(array $move) => $this->moveReverse($move, $stacks2));


        $string = $stacks->map(fn(Collection $stack) => $stack->last());
        $string2 = $stacks2->map(fn(Collection $stack) => $stack->last());

        $part1 = Str::of($string->implode(''))->replace('[', '')->replace(']', '')->toString();
        $part2 = Str::of($string2->implode(''))->replace('[', '')->replace(']', '')->toString();
        $this->info("Part 1: $part1");
        $this->info("Part 2: $part2");
    }

    public function stacks($puzzle): Collection
    {
        $stacksInput = explode(PHP_EOL . PHP_EOL, $puzzle);
        $stacks = Str::of($stacksInput[0])->explode(PHP_EOL);

        $numberStacks = (int) Str::of($stacks->pop())->split(1)->last();
        $this->stacks = collect(array_fill(1, $numberStacks, null))->map(fn($current) => collect());

        $stacks->map(function($stack) {
            return Str::of($stack)->split(4)->map(fn($chunk) => trim($chunk));
        })->each(function (Collection $stack) use ($stacks) {
            $stack->each(function ($crate, $key) {
                $this->addCrate($key + 1, $crate);
            });
        });

        $stacks = $this->stacks;
        $this->stacks = collect();

        return $stacks;
    }

    public function addCrate($column, $crate) {
        if(empty($crate)) {
            return;
        }

        $this->stacks->get($column)->prepend($crate);
    }

    public function moves(string $puzzle)
    {
        $stacksInput = explode(PHP_EOL . PHP_EOL, $puzzle);
        return Str::of($stacksInput[1])
            ->explode(PHP_EOL)
            ->filter()
            ->map(fn($move) => [
                'move' => $move,
                'amount' => (int) trim(Str::between($move, 'move', 'from')),
                'from' => (int) trim(Str::between($move, 'from', 'to')),
                'to' => (int) trim(Str::after($move, 'to')),
            ]);
    }

    private function move(array $move, Collection $stacks)
    {
        /** @var Collection $from */
        $from = $stacks->get($move['from']);
        /** @var Collection $to */
        $to = $stacks->get($move['to']);

        //dump($move);
        //dump([$from->toArray(), $to->toArray()]);

        $shifted = $from->pop($move['amount']);
        $shifted = $shifted instanceof Collection
            ? $shifted
            : collect([$shifted]);

        $to = $to->merge($shifted);

        $stacks->put($move['from'], $from);
        $stacks->put($move['to'], $to);

        //dump([$from->toArray(), $to->toArray()]);
        //dump('---------------------------------------');
    }

    private function moveReverse(array $move, Collection $stacks)
    {
        /** @var Collection $from */
        $from = $stacks->get($move['from']);
        /** @var Collection $to */
        $to = $stacks->get($move['to']);

        //dump($move);
        //dump([$from->toArray(), $to->toArray()]);

        $shifted = $from->pop($move['amount']);
        $shifted = $shifted instanceof Collection
            ? $shifted
            : collect([$shifted]);

        $to = $to->merge($shifted->reverse());

        $stacks->put($move['from'], $from);
        $stacks->put($move['to'], $to);

        //dump([$from->toArray(), $to->toArray()]);
        //dump('---------------------------------------');
    }
}


