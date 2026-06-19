@props(['title' => null, 'description' => null])

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ $title ? $title.' — Catalog' : 'Catalog — Books, vinyl & board games, thoughtfully curated' }}</title>
<meta name="description" content="{{ $description ?? 'A small shop with strong opinions. Books worth rereading, records worth flipping, and games worth the table space. Free U.S. shipping over $75.' }}">

<meta property="og:title" content="{{ $title ? $title.' — Catalog' : 'Catalog — Books, vinyl & board games' }}">
<meta property="og:description" content="{{ $description ?? 'A small shop with strong opinions. Books, vinyl records, and board games — thoughtfully curated.' }}">
<meta property="og:type" content="website">

<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='8' fill='%231d1a14'/%3E%3Ccircle cx='16' cy='16' r='7.5' fill='none' stroke='%23f6f4ee' stroke-width='2'/%3E%3Ccircle cx='16' cy='16' r='2' fill='%23bc4b26'/%3E%3C/svg%3E">

<link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
<link href="https://fonts.bunny.net/css?family=fraunces:300,400,500,600,300i,400i|hanken-grotesk:400,500,600,700" rel="stylesheet">

@vite(['resources/css/app.css', 'resources/js/app.js'])
