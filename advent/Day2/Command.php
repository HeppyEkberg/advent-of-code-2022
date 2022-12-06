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

    const SCENARIO = [
        'X' => self::LOOSE,
        'Y' => self::DRAW,
        'Z' => self::WIN,
    ];

    const LOOSE = 'LOOSE';
    const WIN = 'WIN';
    const DRAW = 'DRAW';

    public function handle()
    {
        $puzzle = File::get(dirname(__FILE__) . '/puzzle');

        $games = Str::of($puzzle)->explode(PHP_EOL)
            ->filter();

        $part1 = $this->part1($games);

        throw_if($part1 != 10718, new Exception('Part 1 is no longer correct'));

        $part2 = $this->part2($games);

        $this->info("Total score: {$part1}");
        $this->info("Total score: {$part2}");
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

        return $this->calculate($games);
    }

    public function part2(Collection $games) {
        $games = $games->map(function($game) {
            $choices = explode(' ', $game);
            $opponent = $this->transform($choices[0]);
            $scenario = self::SCENARIO[$choices[1]];

            return [
                'original' => $game,
                'scenario' => $scenario,
                'opponent' => $opponent,
            ];
        });


        $games = $games->map(function ($game) {
            $game['mine'] = match ($game['scenario']) {
                self::WIN => $this->win($game['opponent']),
                self::DRAW => $this->draw($game['opponent']),
                self::LOOSE => $this->loose($game['opponent']),
            };

            return $game;
        });

        return $this->calculate($games);
    }

    protected function calculate(Collection $games)
    {
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

    private function win($opponent)
    {
        return collect(self::WINS_AGAINST)->search($opponent);
    }

    private function draw($opponent)
    {
        return $opponent;
    }

    private function loose($opponent)
    {
        return collect(self::WINS_AGAINST)->get($opponent);
    }
}
