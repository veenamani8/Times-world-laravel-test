<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Request Logger</title>
    @vite('resources/js/app.js')
</head>
<body>
<div id="app">
    <list-vue></list-vue>
</div>
</body>
</html>
