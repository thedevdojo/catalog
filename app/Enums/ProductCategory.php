<?php

namespace App\Enums;

enum ProductCategory: string
{
    case Books = 'books';
    case Vinyl = 'vinyl';
    case BoardGames = 'board-games';

    public function label(): string
    {
        return match ($this) {
            self::Books => 'Books',
            self::Vinyl => 'Vinyl',
            self::BoardGames => 'Board Games',
        };
    }

    public function singularLabel(): string
    {
        return match ($this) {
            self::Books => 'Book',
            self::Vinyl => 'Vinyl LP',
            self::BoardGames => 'Board Game',
        };
    }

    public function creatorLabel(): string
    {
        return match ($this) {
            self::Books => 'Author',
            self::Vinyl => 'Artist',
            self::BoardGames => 'Designer',
        };
    }

    public function tagline(): string
    {
        return match ($this) {
            self::Books => 'Stories worth keeping on the shelf',
            self::Vinyl => 'Records that reward a second listen',
            self::BoardGames => 'Nights around the table, well spent',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Books => 'First editions, modern classics, and the kind of paperbacks you press into a friend\'s hands. Every title is read before it makes the shelf.',
            self::Vinyl => 'Pressings we actually spin in the shop — new audiophile reissues, overlooked gems, and records that sound better at 33⅓.',
            self::BoardGames => 'Games chosen for the people around the table, not the rulebook. Tested on real game nights, rated by how often they come back out.',
        };
    }
}
