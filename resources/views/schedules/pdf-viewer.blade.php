<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $schedule->title }}</title>
    <style>
        body { margin: 0; overflow: hidden; }
        iframe { border: none; width: 100vw; height: 100vh; }
    </style>
</head>
<body>
    <iframe src="{{ route('schedule.serve', $schedule->id) }}#toolbar=0&navpanes=0"></iframe>
</body>
</html>