<?php

namespace Database\Seeders\Support;

/**
 * Deterministic SVG artwork generator for demo products.
 *
 * Every product gets an 800x1000 "staged photograph": a warm studio
 * backdrop, a wooden or linen table surface, soft contact shadows, and
 * the product standing upright — typographic book covers, record
 * sleeves with the vinyl peeking out, upright board game boxes.
 * Palette + layout variant derive from the product slug, so reseeding
 * always reproduces identical artwork.
 */
class ProductArt
{
    private const WIDTH = 800;

    private const HEIGHT = 1000;

    /** Y coordinate where the table surface meets the backdrop. */
    private const TABLE_Y = 768;

    /**
     * Muted scene palettes: backdrop pair, table pair, object tones.
     *
     * @var array<int, array{bgTop: string, bgBottom: string, tableTop: string, tableBottom: string, cover: string, coverInk: string, tone: string, toneSoft: string}>
     */
    private const PALETTES = [
        ['bgTop' => '#f3efe7', 'bgBottom' => '#e7e0d2', 'tableTop' => '#c9aa80', 'tableBottom' => '#a9875c', 'cover' => '#f1ece0', 'coverInk' => '#2c2825', 'tone' => '#2c2825', 'toneSoft' => '#857d6f'],
        ['bgTop' => '#efece7', 'bgBottom' => '#e0dbd2', 'tableTop' => '#cdb18a', 'tableBottom' => '#ad8d64', 'cover' => '#2e2a26', 'coverInk' => '#ece5d6', 'tone' => '#a98e6a', 'toneSoft' => '#c9b694'],
        ['bgTop' => '#f1ede4', 'bgBottom' => '#e5ddcc', 'tableTop' => '#e3ddcd', 'tableBottom' => '#cdc5b0', 'cover' => '#9aa794', 'coverInk' => '#26302a', 'tone' => '#536052', 'toneSoft' => '#b9c1b0'],
        ['bgTop' => '#eeebe7', 'bgBottom' => '#ddd8cf', 'tableTop' => '#c5a67e', 'tableBottom' => '#a3815a', 'cover' => '#a3b1ba', 'coverInk' => '#26313a', 'tone' => '#4d6273', 'toneSoft' => '#c0cad0'],
        ['bgTop' => '#f3eee4', 'bgBottom' => '#e6dcc8', 'tableTop' => '#cfb38c', 'tableBottom' => '#af8f64', 'cover' => '#f4f0e6', 'coverInk' => '#33302b', 'tone' => '#b07a52', 'toneSoft' => '#d3ac8c'],
        ['bgTop' => '#f0ece4', 'bgBottom' => '#e2dbcd', 'tableTop' => '#e6e0d2', 'tableBottom' => '#d0c9b6', 'cover' => '#42403a', 'coverInk' => '#e9e3d4', 'tone' => '#8a8568', 'toneSoft' => '#b3ae93'],
        ['bgTop' => '#f2eee8', 'bgBottom' => '#e4ded4', 'tableTop' => '#c8a87e', 'tableBottom' => '#a78759', 'cover' => '#ddcfb4', 'coverInk' => '#3a342b', 'tone' => '#7c6a4c', 'toneSoft' => '#c0ad8b'],
        ['bgTop' => '#efeae3', 'bgBottom' => '#e1d8ca', 'tableTop' => '#d2b990', 'tableBottom' => '#b29466', 'cover' => '#b98a68', 'coverInk' => '#332620', 'tone' => '#84573c', 'toneSoft' => '#d6b598'],
    ];

    /**
     * @return array{bgTop: string, bgBottom: string, tableTop: string, tableBottom: string, cover: string, coverInk: string, tone: string, toneSoft: string}
     */
    public static function palette(string $slug): array
    {
        return self::PALETTES[crc32($slug) % count(self::PALETTES)];
    }

    public static function accent(string $slug): string
    {
        return self::palette($slug)['tone'];
    }

    public static function generate(string $category, string $slug, string $title, string $creator): string
    {
        $p = self::palette($slug);
        $variant = crc32(strrev($slug)) % 3;
        $seed = crc32($slug.$category);

        $art = match ($category) {
            'books' => self::book($title, $creator, $p, $variant, $seed),
            'vinyl' => self::vinyl($title, $creator, $p, $variant, $seed),
            default => self::game($title, $creator, $p, $variant, $seed),
        };

        return self::scene($art, $p, $seed);
    }

    // ------------------------------------------------------------------
    // Scene framework
    // ------------------------------------------------------------------

    private static function scene(string $object, array $p, int $seed): string
    {
        $w = self::WIDTH;
        $h = self::HEIGHT;
        $tableY = self::TABLE_Y;

        // Some scenes get a quiet ceramic vase with a stem for staging.
        $prop = $seed % 3 === 0 ? self::vaseProp($seed) : '';

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {$w} {$h}" width="{$w}" height="{$h}">
<defs>
<linearGradient id="bg" x1="0" y1="0" x2="0" y2="1">
<stop offset="0" stop-color="{$p['bgTop']}"/>
<stop offset="1" stop-color="{$p['bgBottom']}"/>
</linearGradient>
<linearGradient id="table" x1="0" y1="0" x2="0" y2="1">
<stop offset="0" stop-color="{$p['tableTop']}"/>
<stop offset="1" stop-color="{$p['tableBottom']}"/>
</linearGradient>
<radialGradient id="glow" cx="0.5" cy="0.42" r="0.55">
<stop offset="0" stop-color="#ffffff" stop-opacity="0.5"/>
<stop offset="1" stop-color="#ffffff" stop-opacity="0"/>
</radialGradient>
<radialGradient id="vignette" cx="0.5" cy="0.46" r="0.85">
<stop offset="0.62" stop-color="#2b251d" stop-opacity="0"/>
<stop offset="1" stop-color="#2b251d" stop-opacity="0.11"/>
</radialGradient>
<filter id="soft" x="-60%" y="-60%" width="220%" height="220%"><feGaussianBlur in="SourceGraphic" stdDeviation="18"/></filter>
<filter id="softer" x="-60%" y="-60%" width="220%" height="220%"><feGaussianBlur in="SourceGraphic" stdDeviation="34"/></filter>
</defs>
<rect width="{$w}" height="{$h}" fill="url(#bg)"/>
<rect width="{$w}" height="{$h}" fill="url(#glow)"/>
<rect x="0" y="{$tableY}" width="{$w}" height="{$h}" fill="url(#table)"/>
<rect x="0" y="{$tableY}" width="{$w}" height="3" fill="#ffffff" opacity="0.28"/>
{$prop}
{$object}
<rect width="{$w}" height="{$h}" fill="url(#vignette)"/>
</svg>
SVG;
    }

    private static function shadow(float $cx, float $cy, float $rx, float $opacity = 0.32): string
    {
        return '<ellipse cx="'.$cx.'" cy="'.$cy.'" rx="'.$rx.'" ry="'.($rx * 0.085).'" fill="#2b231a" opacity="'.$opacity.'" filter="url(#soft)"/>'
            .'<ellipse cx="'.$cx.'" cy="'.$cy.'" rx="'.($rx * 0.62).'" ry="'.($rx * 0.05).'" fill="#241d15" opacity="'.($opacity * 0.8).'" filter="url(#softer)"/>';
    }

    private static function vaseProp(int $seed): string
    {
        $right = $seed % 2 === 0;
        $x = $right ? 672 : 122;
        $baseY = self::TABLE_Y + 14;
        $clay = '#cfc5b2';
        $stem = '#7e8068';

        return '<g opacity="0.9">'
            .self::shadow($x, $baseY + 4, 52, 0.18)
            .'<path d="M'.($x - 30).' '.($baseY - 96).' q-10 50 8 82 q8 14 22 14 q14 0 22 -14 q18 -32 8 -82 q-6 -22 -30 -22 q-24 0 -30 22 Z" fill="'.$clay.'"/>'
            .'<path d="M'.$x.' '.($baseY - 112).' q-6 -70 26 -118" fill="none" stroke="'.$stem.'" stroke-width="3.5" stroke-linecap="round"/>'
            .'<path d="M'.$x.' '.($baseY - 112).' q2 -58 -18 -98" fill="none" stroke="'.$stem.'" stroke-width="3" stroke-linecap="round"/>'
            .'<ellipse cx="'.($x + 26).'" cy="'.($baseY - 230).'" rx="7" ry="16" fill="'.$stem.'" transform="rotate(24 '.($x + 26).' '.($baseY - 230).')"/>'
            .'<ellipse cx="'.($x + 14).'" cy="'.($baseY - 188).'" rx="6" ry="14" fill="'.$stem.'" transform="rotate(-18 '.($x + 14).' '.($baseY - 188).')" opacity="0.85"/>'
            .'<ellipse cx="'.($x - 18).'" cy="'.($baseY - 206).'" rx="6" ry="14" fill="'.$stem.'" transform="rotate(-30 '.($x - 18).' '.($baseY - 206).')" opacity="0.9"/>'
            .'</g>';
    }

    // ------------------------------------------------------------------
    // Text helpers
    // ------------------------------------------------------------------

    /**
     * @return list<string>
     */
    private static function lines(string $text, int $maxChars): array
    {
        $words = preg_split('/\s+/', trim($text)) ?: [];
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            $candidate = $current === '' ? $word : $current.' '.$word;

            if (mb_strlen($candidate) > $maxChars && $current !== '') {
                $lines[] = $current;
                $current = $word;
            } else {
                $current = $candidate;
            }
        }

        if ($current !== '') {
            $lines[] = $current;
        }

        return $lines;
    }

    private static function serif(array $lines, float $x, float $startY, float $lineHeight, float $size, string $fill, string $anchor = 'middle', string $weight = '400', float $tracking = 0.5): string
    {
        $out = '';

        foreach ($lines as $i => $line) {
            $y = $startY + $i * $lineHeight;
            $escaped = htmlspecialchars($line, ENT_XML1);
            $out .= "<text x=\"{$x}\" y=\"{$y}\" text-anchor=\"{$anchor}\" font-family=\"Georgia, 'Times New Roman', serif\" font-size=\"{$size}\" font-weight=\"{$weight}\" letter-spacing=\"{$tracking}\" fill=\"{$fill}\">{$escaped}</text>";
        }

        return $out;
    }

    private static function caps(string $text, float $x, float $y, float $size, string $fill, string $anchor = 'middle', float $tracking = 4, string $weight = '600'): string
    {
        $escaped = htmlspecialchars(mb_strtoupper($text), ENT_XML1);

        return "<text x=\"{$x}\" y=\"{$y}\" text-anchor=\"{$anchor}\" font-family=\"'Helvetica Neue', Helvetica, Arial, sans-serif\" font-size=\"{$size}\" font-weight=\"{$weight}\" letter-spacing=\"{$tracking}\" fill=\"{$fill}\">{$escaped}</text>";
    }

    private static function serifCaps(string $text, float $x, float $y, float $size, string $fill, string $anchor = 'middle', float $tracking = 6): string
    {
        $escaped = htmlspecialchars(mb_strtoupper($text), ENT_XML1);

        return "<text x=\"{$x}\" y=\"{$y}\" text-anchor=\"{$anchor}\" font-family=\"Georgia, 'Times New Roman', serif\" font-size=\"{$size}\" letter-spacing=\"{$tracking}\" fill=\"{$fill}\">{$escaped}</text>";
    }

    // ------------------------------------------------------------------
    // Books
    // ------------------------------------------------------------------

    private static function book(string $title, string $creator, array $p, int $variant, int $seed): string
    {
        if ($variant === 2) {
            return self::bookStack($title, $creator, $p, $seed);
        }

        // Standing hardcover: 430 x 620, base sunk a touch into the table.
        $w = 430;
        $h = 620;
        $x = (self::WIDTH - $w) / 2;
        $baseY = self::TABLE_Y + 16;
        $y = $baseY - $h;
        $cx = self::WIDTH / 2;

        $dark = $variant === 1;
        $cover = $dark ? '#2e2a26' : $p['cover'];
        $ink = $dark ? '#e9e2d2' : $p['coverInk'];
        $accent = $dark ? $p['toneSoft'] : $p['tone'];

        // Adjust for palettes whose cover is already dark.
        if (! $dark && self::isDark($cover)) {
            $ink = '#ece5d6';
            $accent = $p['toneSoft'];
        }

        $titleLines = self::lines($title, 14);
        $titleY = $y + 178;

        $art = self::shadow($cx, $baseY, $w * 0.62)
            // page block peeking right
            .'<rect x="'.($x + $w - 6).'" y="'.($y + 10).'" width="14" height="'.($h - 14).'" rx="2" fill="#f6f1e3"/>'
            .'<rect x="'.($x + $w + 2).'" y="'.($y + 16).'" width="4" height="'.($h - 26).'" fill="#ddd5c0"/>'
            // cover
            .'<rect x="'.$x.'" y="'.$y.'" width="'.$w.'" height="'.$h.'" rx="5" fill="'.$cover.'"/>'
            // gentle top light on the cover
            .'<rect x="'.$x.'" y="'.$y.'" width="'.$w.'" height="'.$h.'" rx="5" fill="url(#glow)" opacity="0.35"/>'
            // spine crease
            .'<rect x="'.($x + 22).'" y="'.$y.'" width="3" height="'.$h.'" fill="#000000" opacity="0.12"/>'
            .'<rect x="'.($x + 26).'" y="'.$y.'" width="2" height="'.$h.'" fill="#ffffff" opacity="0.18"/>'
            // typography
            .'<circle cx="'.($cx + 10).'" cy="'.($y + 108).'" r="3.5" fill="'.$accent.'"/>'
            .'<line x1="'.($cx - 30).'" y1="'.($y + 108).'" x2="'.($cx - 8).'" y2="'.($y + 108).'" stroke="'.$accent.'" stroke-width="1.5"/>'
            .'<line x1="'.($cx + 28).'" y1="'.($y + 108).'" x2="'.($cx + 50).'" y2="'.($y + 108).'" stroke="'.$accent.'" stroke-width="1.5"/>'
            .self::serif(array_map('mb_strtoupper', $titleLines), $cx + 10, $titleY, 56, 38, $ink, 'middle', '400', 4)
            .'<line x1="'.($cx - 24).'" y1="'.($titleY + count($titleLines) * 56 - 18).'" x2="'.($cx + 44).'" y2="'.($titleY + count($titleLines) * 56 - 18).'" stroke="'.$accent.'" stroke-width="1.5"/>'
            .self::caps($creator, $cx + 10, $baseY - 96, 15, $ink, 'middle', 3.5, '500')
            .self::caps('Catalog Press', $cx + 10, $baseY - 52, 10, $accent, 'middle', 3, '600');

        return $art;
    }

    private static function bookStack(string $title, string $creator, array $p, int $seed): string
    {
        $cx = self::WIDTH / 2;
        $baseY = self::TABLE_Y + 12;

        // Bottom to top: muted tone, dark, cream (title on top for contrast).
        $stack = [
            ['w' => 560, 'h' => 98, 'offset' => 0, 'color' => $p['toneSoft'], 'ink' => '#33302b'],
            ['w' => 524, 'h' => 88, 'offset' => 16, 'color' => '#332f2a', 'ink' => '#e9e2d2'],
            ['w' => 544, 'h' => 94, 'offset' => -10, 'color' => '#f2ecdd', 'ink' => '#33302b'],
        ];

        $art = self::shadow($cx, $baseY, 340);
        $yCursor = $baseY;
        $centers = [];

        foreach ($stack as $book) {
            $yCursor -= $book['h'];
            $bx = $cx - $book['w'] / 2 + $book['offset'];
            $centers[] = ['x' => $cx + $book['offset'], 'y' => $yCursor + $book['h'] / 2 + 7];

            $art .= '<rect x="'.$bx.'" y="'.$yCursor.'" width="'.$book['w'].'" height="'.$book['h'].'" rx="10" fill="'.$book['color'].'"/>'
                .'<rect x="'.$bx.'" y="'.$yCursor.'" width="'.$book['w'].'" height="'.$book['h'].'" rx="10" fill="none" stroke="#000000" stroke-opacity="0.14" stroke-width="1.5"/>'
                // page block at the right end of each lying book
                .'<rect x="'.($bx + $book['w'] - 26).'" y="'.($yCursor + 7).'" width="19" height="'.($book['h'] - 14).'" rx="3" fill="#f6f1e3"/>'
                .'<path d="M'.($bx + $book['w'] - 21).' '.($yCursor + 12).' v'.($book['h'] - 24).' M'.($bx + $book['w'] - 16).' '.($yCursor + 12).' v'.($book['h'] - 24).' M'.($bx + $book['w'] - 11).' '.($yCursor + 12).' v'.($book['h'] - 24).'" stroke="#d8cfb8" stroke-width="1.5"/>'
                // soft shading under the book above
                .'<rect x="'.$bx.'" y="'.($yCursor + $book['h'] - 5).'" width="'.$book['w'].'" height="5" fill="#000000" opacity="0.1"/>';
        }

        $shortTitle = mb_strlen($title) > 26 ? mb_substr($title, 0, 24).'…' : $title;

        $art .= self::serifCaps($shortTitle, $centers[2]['x'] - 12, $centers[2]['y'], 26, $stack[2]['ink'], 'middle', 3)
            .self::caps($creator, $centers[1]['x'] - 12, $centers[1]['y'] - 1, 14, $stack[1]['ink'], 'middle', 3.5, '500')
            .self::caps('Catalog Press · First Edition', $centers[0]['x'] - 12, $centers[0]['y'] - 1, 12, $stack[0]['ink'], 'middle', 3, '500');

        return $art;
    }

    // ------------------------------------------------------------------
    // Vinyl
    // ------------------------------------------------------------------

    private static function vinyl(string $title, string $creator, array $p, int $variant, int $seed): string
    {
        // Sleeve standing on a small wooden stand, record leaning out right.
        $s = 520;
        $x = 130;
        $baseY = self::TABLE_Y + 10;
        $y = $baseY - $s;
        $scx = $x + $s / 2;

        // Record peeking from behind the sleeve's right edge.
        $discR = 244;
        $discCx = $x + $s + 60;
        $discCy = $baseY - $discR + 6;

        $rings = '';
        foreach ([214, 188, 162, 136, 110] as $r) {
            $rings .= '<circle cx="'.$discCx.'" cy="'.$discCy.'" r="'.$r.'" fill="none" stroke="#ffffff" stroke-opacity="0.05" stroke-width="9"/>';
        }

        $disc = self::shadow($discCx, $baseY + 2, $discR * 0.7, 0.26)
            .'<circle cx="'.$discCx.'" cy="'.$discCy.'" r="'.$discR.'" fill="#1c1916"/>'
            .'<circle cx="'.$discCx.'" cy="'.$discCy.'" r="'.$discR.'" fill="none" stroke="#ffffff" stroke-opacity="0.08" stroke-width="2"/>'
            .$rings
            .'<circle cx="'.$discCx.'" cy="'.$discCy.'" r="80" fill="'.$p['tone'].'"/>'
            .'<circle cx="'.$discCx.'" cy="'.$discCy.'" r="80" fill="url(#glow)" opacity="0.3"/>'
            .'<circle cx="'.$discCx.'" cy="'.$discCy.'" r="6" fill="'.$p['bgTop'].'"/>';

        $sleeveArt = match ($variant) {
            // Quiet field: cream inner panel, tonal disc, thin frame.
            0 => (function () use ($x, $y, $s, $scx, $p) {
                return '<rect x="'.$x.'" y="'.$y.'" width="'.$s.'" height="'.$s.'" fill="'.$p['cover'].'"/>'
                    .'<rect x="'.($x + 34).'" y="'.($y + 34).'" width="'.($s - 68).'" height="'.($s - 68).'" fill="#f3eee2"/>'
                    .'<rect x="'.($x + 34).'" y="'.($y + 34).'" width="'.($s - 68).'" height="'.($s - 68).'" fill="none" stroke="#000000" stroke-opacity="0.08" stroke-width="1.5"/>'
                    .'<circle cx="'.$scx.'" cy="'.($y + $s / 2 - 22).'" r="92" fill="'.$p['tone'].'"/>'
                    .'<circle cx="'.$scx.'" cy="'.($y + $s / 2 - 22).'" r="92" fill="url(#glow)" opacity="0.4"/>'
                    .'<circle cx="'.$scx.'" cy="'.($y + $s / 2 - 22).'" r="110" fill="none" stroke="'.$p['tone'].'" stroke-opacity="0.35" stroke-width="1.5"/>';
            })(),
            // Dark sleeve, editorial type.
            1 => '<rect x="'.$x.'" y="'.$y.'" width="'.$s.'" height="'.$s.'" fill="#272320"/>'
                .'<rect x="'.($x + 40).'" y="'.($y + $s - 168).'" width="120" height="2" fill="'.$p['toneSoft'].'"/>',
            // Horizon split with low sun.
            default => '<rect x="'.$x.'" y="'.$y.'" width="'.$s.'" height="'.$s.'" fill="'.$p['cover'].'"/>'
                .'<rect x="'.$x.'" y="'.($y + (int) ($s * 0.58)).'" width="'.$s.'" height="'.(int) ($s * 0.42).'" fill="'.$p['toneSoft'].'" opacity="0.65"/>'
                .'<circle cx="'.$scx.'" cy="'.($y + (int) ($s * 0.58)).'" r="86" fill="'.$p['tone'].'"/>'
                .'<rect x="'.$x.'" y="'.($y + (int) ($s * 0.58)).'" width="'.$s.'" height="2" fill="#ffffff" opacity="0.35"/>',
        };

        $sleeveInk = $variant === 1 ? '#e9e2d2' : (self::isDark($p['cover']) ? '#ece5d6' : $p['coverInk']);
        $sleeveAccent = $variant === 1 ? $p['toneSoft'] : (self::isDark($p['cover']) ? $p['toneSoft'] : $p['tone']);
        $titleLines = self::lines($title, 16);

        $type = $variant === 1
            ? self::serif($titleLines, $x + 40, $y + 96, 54, 44, $sleeveInk, 'start', '400', 0.5)
                .self::caps($creator, $x + 40, $y + $s - 130, 15, $sleeveAccent, 'start', 4, '500')
            : self::caps($creator, $scx, $y + 64, 15, $sleeveAccent, 'middle', 5, '600')
                .self::serif($titleLines, $scx, $y + $s - 64 - (count($titleLines) - 1) * 40, 40, 33, $sleeveInk, 'middle', '400', 1);

        // Wooden plate stand.
        $stand = '<path d="M'.($scx - 110).' '.($baseY + 2).' l34 -44 l12 8 l-26 36 Z" fill="#9a7b52"/>'
            .'<path d="M'.($scx + 110).' '.($baseY + 2).' l-34 -44 l-12 8 l26 36 Z" fill="#8d6f49"/>';

        $sleeve = self::shadow($scx, $baseY + 4, $s * 0.56)
            .$stand
            .'<g>'.$sleeveArt
            .'<rect x="'.$x.'" y="'.$y.'" width="'.$s.'" height="'.$s.'" fill="url(#glow)" opacity="0.25"/>'
            .'<rect x="'.$x.'" y="'.$y.'" width="'.$s.'" height="'.$s.'" fill="none" stroke="#000000" stroke-opacity="0.14" stroke-width="1.5"/>'
            .'<rect x="'.($x + $s - 16).'" y="'.$y.'" width="16" height="'.$s.'" fill="#000000" opacity="0.08"/>'
            .$type
            .'</g>';

        return $disc.$sleeve;
    }

    // ------------------------------------------------------------------
    // Board games
    // ------------------------------------------------------------------

    private static function game(string $title, string $creator, array $p, int $variant, int $seed): string
    {
        // Upright box, portrait, standing on the table.
        $w = 520;
        $h = 600;
        $x = (self::WIDTH - $w) / 2;
        $baseY = self::TABLE_Y + 12;
        $y = $baseY - $h;
        $cx = self::WIDTH / 2;

        $boxDark = $variant === 1;
        $box = $boxDark ? '#2e2b27' : ($variant === 2 ? $p['cover'] : '#ece6d6');
        $ink = $boxDark || self::isDark($box) ? '#e9e2d2' : '#33302b';
        $accent = $boxDark || self::isDark($box) ? $p['toneSoft'] : $p['tone'];

        // Central emblem — soft watercolor wash + clean geometry.
        $emblemCy = $y + 252;
        $emblem = '<ellipse cx="'.($cx - 30).'" cy="'.($emblemCy - 12).'" rx="120" ry="96" fill="'.$p['toneSoft'].'" opacity="0.55" filter="url(#soft)"/>'
            .'<ellipse cx="'.($cx + 44).'" cy="'.($emblemCy + 18).'" rx="104" ry="86" fill="'.$p['tone'].'" opacity="0.3" filter="url(#soft)"/>';

        $emblem .= match ($variant) {
            // Die outline with pips.
            0 => '<rect x="'.($cx - 74).'" y="'.($emblemCy - 74).'" width="148" height="148" rx="26" fill="none" stroke="'.$accent.'" stroke-width="3.5" transform="rotate(7 '.$cx.' '.$emblemCy.')"/>'
                .'<g transform="rotate(7 '.$cx.' '.$emblemCy.')" fill="'.$accent.'">'
                .'<circle cx="'.($cx - 34).'" cy="'.($emblemCy - 34).'" r="9"/>'
                .'<circle cx="'.($cx + 34).'" cy="'.($emblemCy - 34).'" r="9"/>'
                .'<circle cx="'.$cx.'" cy="'.$emblemCy.'" r="9"/>'
                .'<circle cx="'.($cx - 34).'" cy="'.($emblemCy + 34).'" r="9"/>'
                .'<circle cx="'.($cx + 34).'" cy="'.($emblemCy + 34).'" r="9"/>'
                .'</g>',
            // Sun and peaks line art.
            1 => '<circle cx="'.($cx + 38).'" cy="'.($emblemCy - 36).'" r="42" fill="'.$accent.'" opacity="0.9"/>'
                .'<path d="M'.($cx - 130).' '.($emblemCy + 78).' l74 -108 l46 62 l34 -44 l60 90 Z" fill="none" stroke="'.$ink.'" stroke-width="3.5" stroke-linejoin="round"/>',
            // Meeple silhouette.
            default => '<path d="M'.$cx.' '.($emblemCy - 78).' c-26 0 -38 22 -34 42 c3 14 12 22 12 22 c-26 8 -50 26 -56 56 c-2 12 6 18 16 18 l124 0 c10 0 18 -6 16 -18 c-6 -30 -30 -48 -56 -56 c0 0 9 -8 12 -22 c4 -20 -8 -42 -34 -42 Z" fill="'.$accent.'"/>',
        };

        $titleLines = self::lines($title, 15);
        $titleStartY = $y + 442;

        $art = self::shadow($cx, $baseY, $w * 0.64)
            // box side depth (right edge)
            .'<rect x="'.($x + $w - 4).'" y="'.($y + 6).'" width="16" height="'.($h - 8).'" rx="4" fill="#000000" opacity="0.18"/>'
            // box front
            .'<rect x="'.$x.'" y="'.$y.'" width="'.$w.'" height="'.$h.'" rx="8" fill="'.$box.'"/>'
            .'<rect x="'.$x.'" y="'.$y.'" width="'.$w.'" height="'.$h.'" rx="8" fill="url(#glow)" opacity="0.3"/>'
            // lid seam near the top
            .'<rect x="'.$x.'" y="'.($y + 64).'" width="'.$w.'" height="2.5" fill="#000000" opacity="0.12"/>'
            .self::caps('A Catalog Tabletop Original', $cx, $y + 42, 11, $accent, 'middle', 3.5)
            .$emblem
            .self::serif(array_map('mb_strtoupper', $titleLines), $cx, $titleStartY, 56, 40, $ink, 'middle', '400', 7)
            .'<line x1="'.($cx - 30).'" y1="'.($titleStartY + count($titleLines) * 56 - 22).'" x2="'.($cx + 30).'" y2="'.($titleStartY + count($titleLines) * 56 - 22).'" stroke="'.$accent.'" stroke-width="2"/>'
            .self::caps($creator, $cx, $baseY - 44, 13, $ink, 'middle', 3, '500');

        return $art;
    }

    private static function isDark(string $hex): bool
    {
        $hex = ltrim($hex, '#');
        [$r, $g, $b] = [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];

        return ($r * 299 + $g * 587 + $b * 114) / 1000 < 120;
    }
}
