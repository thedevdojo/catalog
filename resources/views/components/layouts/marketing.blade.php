@props(['title' => null, 'description' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <x-partials.head :title="$title" :description="$description" />
</head>
<body class="grain min-h-screen font-sans antialiased">
    <x-partials.nav />

    <main>
        {{ $slot }}
    </main>

    <x-partials.footer />

    <livewire:cart.drawer />
    <x-toasts />
</body>
</html>
