<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Weather Forecast Junior</title>
    @viteReactRefresh
    @vite('resources/js/app.jsx')
    @vite('resources/css/app.css')
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body class="font-sans bg-gradient-to-br from-gray-50 via-white to-gray-100 text-gray-800 antialiased">
  {{$slot}}
</body>

</html>
