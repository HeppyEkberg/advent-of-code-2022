<?php

namespace Advent\Day2;

use Exception;
use Illuminate\Console\Command as Cmd;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Command extends Cmd
{
    protected $signature = 'day:2';
    protected $description = '';

    const MAP = [
        'X' => self::ROCK,
        'Y' => self::PAPER,
        'Z' => self::SCISSOR,
        'A' => self::ROCK,
        'B' => self::PAPER,
        'C' => self::SCISSOR,
    ];

    const POINTS = [
        self::ROCK => 1,
        self::PAPER => 2,
        self::SCISSOR => 3,
    ];

    const PAPER = 'paper';
    const ROCK = 'rock';
    const SCISSOR = 'scissor';

    const WINS_AGAINST = [
        self::ROCK => self::SCISSOR,
        self::PAPER => self::ROCK,
        self::SCISSOR => self::PAPER,
    ];

    public function handle()
    {
        $puzzle = File::get(dirname(__FILE__) . '/puzzle');

        $games = Str::of($puzzle)->explode(PHP_EOL)
            ->filter();

        $part1 = $this->part1($games);

        throw_if($part1 != 10718, new Exception('Part 1 is no longer correct'));

        $this->info("Total score: {$part1}");
    }

    public function part1(Collection $games)
    {

        $games = $games->map(function($game) {
            $choices = explode(' ', $game);
            $opponent = $this->transform($choices[0]);
            $mine = $this->transform($choices[1]);

            return [
                'original' => $game,
                'mine' => $mine,
                'opponent' => $opponent,
            ];
        });

        $wins = $games->filter(function(array $game) {
            return $this->wins($game['mine'], $game['opponent']);
        });

        $draws = $games->filter(function(array $game) {
            return $game['mine'] === $game['opponent'];
        });

        $choices = $games->sum(fn($game) => self::POINTS[$game['mine']]);

        return $choices + ($wins->count() * 6) + ($draws->count() * 3);
    }

    protected function wins($a, $b): bool
    {
        return $b == self::WINS_AGAINST[$a];
    }

    public function transform($choice): string
    {
        return self::MAP[$choice];
    }
}
