<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap');
    </style>
    {{-- <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet" /> --}}
    @routes
    @vite('resources/js/app.js')
    @inertiaHead
  </head>
  <body class="font-sans">
    @inertia
  </body>
</html>
