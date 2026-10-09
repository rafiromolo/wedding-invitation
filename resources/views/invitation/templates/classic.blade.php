<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="referrer" content="no-referrer">
    <title>{{ $invitation->title }}</title>
</head>
<body>
    <main>
        @if ($guest)
            <p>Kepada Yth.</p>
            <h2>{{ $guest->name }}</h2>
        @else
            <p>Anda diundang ke acara berikut.</p>
        @endif

        <h1>{{ $invitation->title }}</h1>
        <p>{{ $invitation->event_date->format('d-m-Y H:i') }}</p>
        <p>{{ $invitation->event_data['venue'] ?? '' }}</p>
    </main>
</body>
</html>