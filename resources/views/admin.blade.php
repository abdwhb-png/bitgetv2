<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    @include('inc.favicon')

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        .p-badge {
            font-size: 10px !important;
            min-width: 1rem !important;
            height: 1rem !important;
        }

        pre {
            background-color: #f4f4f4;
            padding: 10px;
            border: 1px solid #ccc;
            overflow: auto;
        }

        .system-dark pre {
            background-color: #444444;
            padding: 10px;
            border: 1px solid #ccc;
            overflow: auto;
        }

        .json-key {
            color: brown;
        }

        .json-value {
            color: navy;
        }

        .json-string {
            color: olive;
        }
    </style>

    <!-- Scripts -->
    @routes
    @php
        $dir = Str::startsWith($page['component'], 'Auth/') ? '' : 'Admin/';
    @endphp
    @vite(['resources/js/admin.js', "resources/js/Pages/{$dir}{$page['component']}.vue"])
    @inertiaHead
</head>

<body class="font-sans antialiased" id="admin">
    @inertia
</body>

</html>
