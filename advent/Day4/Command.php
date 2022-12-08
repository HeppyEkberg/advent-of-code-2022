<?php

namespace Advent\Day4;

use Closure;
use Illuminate\Console\Command as Cmd;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Command extends Cmd
{
    protected $signature = 'day:4';
    protected $description = '';

    public function handle()
    {
         // $this->tests();

        $puzzle = File::get(dirname(__FILE__) . '/puzzle');

        $inputs = Str::of($puzzle)
            ->explode(PHP_EOL)
            ->filter();

        $result = $this->exc($inputs);

        $contains = $result->where('contains', true)->count();
        $overlaps = $result->where('overlaps', true)->count();

        $this->info("Total contains: $contains");
        $this->info("Total overlaps: $overlaps");
    }

    public function exc(Collection $inputs): Collection
    {
        return $inputs->map(function (string $input) {
            $split = explode(',', $input);

            return [
                'input' => $input,
                'first' => $split[0],
                'second' => $split[1],
                'firstRange' => $this->getRange($split[0]),
                'secondRange' => $this->getRange($split[1]),
            ];
        })->map(function (array $couple) {
            $firstRange = $couple['firstRange'];
            $secondRange = $couple['secondRange'];

            return $couple + [
                    'contains' => $this->contains($firstRange, $secondRange),
                    'overlaps' => $this->overlaps($firstRange, $secondRange),
                ];
        });
    }

    public function tests()
    {
        $this->test('1-6', '2-3');
        $this->test('2-3', '1-6');
        $this->test('1-6', '2-8', false);
        $this->test('2-8', '1-6', false);

        die();
    }

    public function test($a, $b, $expect = true)
    {
        $result = $this->exc(collect(["$a,$b"]))->where('contains', true)->count() > 0;

        if($result != $expect) {
            throw new \Exception("$a,$b does not match expected: $expect");
        }

        if($expect) {
            $this->info("$a,$b = true");
        }
        else {
            $this->info("$a,$b = false");
        }
    }

    public function contains(Collection $firstRange, Collection $secondRange): bool
    {
        if($firstRange->intersect($secondRange)->count() == $firstRange->count()) {
            return true;
        }

        if($secondRange->intersect($firstRange)->count() == $secondRange->count()) {
            return true;
        }


        return false;
    }

    public function overlaps(Collection $firstRange, Collection $secondRange)
    {
        if($firstRange->intersect($secondRange)->count() > 0) {
            return true;
        }

        return false;
    }

    private function getRange(string $split)
    {
        $between = explode('-', $split);
        return collect(range($between[0], $between[1]));
    }

}
