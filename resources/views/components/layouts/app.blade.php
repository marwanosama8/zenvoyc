<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-12">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('components.layouts.partials.head')

</head>
<body class="flex flex-col min-h-screen text-primary-900" x-data>
    <div id="app" class="flex flex-col flex-grow">
        <x-layouts.app.header class="flex-shrink-0"/>

        <div class="flex-grow">
            <div class="mx-auto">
                {{ $slot }}
            </div>
        </div>

        <x-layouts.app.footer class="flex-shrink-0" />

        @include('components.layouts.partials.tail')
    </div>
    <x-impersonate::banner/>
</body>
</html>
