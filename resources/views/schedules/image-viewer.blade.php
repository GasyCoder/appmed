<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $schedule->title }}</title>
    <style>
        body {
            margin: 0;
            background: #1a1a1a;
            display: flex;
            align-items: center;
            justify-center;
            min-height: 100vh;
        }
        img {
            max-width: 100%;
            max-height: 100vh;
            object-fit: contain;
        }
    </style>
</head>
<body>
    <img src="{{ $schedule->file_url }}" alt="{{ $schedule->title }}">
</body>
</html>