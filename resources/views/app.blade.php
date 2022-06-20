<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
    <title inertia>{{ config('app.name', 'Laravel') }}</title>
    <link href="{{ mix('/css/app.css') }}" rel="stylesheet"/>
    <script src="{{ mix('/js/app.js') }}" defer></script>
{{--    <script>window.Laravel = {csrfToken: '{{ csrf_token() }}'}</script>--}}
    <meta name="csrf-token" id="csrf-token" content="{{ csrf_token() }}">
    @routes
</head>
<body>
@inertia
</body>
</html>
