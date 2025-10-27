<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Security-Policy" content="connect-src 'self' http://127.0.0.1:8765 http://localhost:8765;">

    <title>Anki-Vocab</title>

    {{-- Modern, clean font similar to your React look --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Animate.css v4 (prefixed classes) -->
    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
    integrity="sha512-V9AnG4tYgCqvXn1L3H9YHc7R9BghadgZ8q2o9g7l3oA1Yc9T7lcY1yqk3I9Sm5n7y3j6oM6R2H1KQJQ2M9Q8Ww=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body class="page-bg" style="font-family: Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial;">
    {{-- Decorative background blobs --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="blob top-1/4 left-1/4 w-96 h-96 bg-violet-200/20"></div>
      <div class="blob bottom-1/4 right-1/4 w-96 h-96 bg-indigo-200/20"></div>
    </div>

    {{-- Glassy nav header --}}
    <header class="relative z-50 nav-glass">
        <x-menu />
    </header>

    <main class="relative z-10">
      <div class="max-w-4xl mx-auto px-6 py-16">
        @yield('content')
      </div>
    </main>

    @stack('scripts')
  </body>
</html>
