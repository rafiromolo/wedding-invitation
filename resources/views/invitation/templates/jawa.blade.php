@php
    $d = $invitation->event_data ?? [];
    $safeUrl = fn ($u) => is_string($u) && str_starts_with($u, 'https://') ? $u : null;
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="referrer" content="no-referrer">
    <title>{{ $invitation->title }}</title>
    <style>
        :root { --sogan:#4a2f1b; --emas:#b8893a; --krem:#f6efe0; --tinta:#2b1d12; }
        * { box-sizing:border-box; }
        body { margin: 0; background: var(--krem); color: var(--tinta);
            font-family: Georgia, 'Times New Roman', serif; line-height: 1.6; }
        section { max-width: 480px; margin: 0 auto; padding: 56px 24px; text-align: center; }
        .cover { min-height: 100svh; display: flex; flex-direction: column; justify-content: center;
                 background: var(--sogan); color: var(--krem); max-width: none; }
        .bingkai { border:1px solid var(--emas); outline:1px solid var(--emas); outline-offset:6px;
                   padding:32px 16px; max-width:420px; margin:0 auto; }
        .kecil { font-size:.85rem; letter-spacing:.2em; text-transform:uppercase; color:var(--emas); }
        h1,h2,h3 { font-weight:normal; margin:.4em 0; }
        h1 { font-size:2.2rem; } h2 { font-size:1.6rem; }
        .nama-tamu { font-size:1.4rem; border-top:1px solid var(--emas); border-bottom:1px solid var(--emas);
                     padding:12px 0; margin:24px 0; }
        .pemisah { color:var(--emas); letter-spacing:.5em; margin:24px 0; }
        .acara { border:1px solid var(--emas); padding:20px; margin:16px 0; background:#fffaf0; }
        a.tombol { display:inline-block; margin-top:12px; padding:10px 20px;
                   border:1px solid var(--sogan); color:var(--sogan); text-decoration:none; }
    </style>
</head>
<body>
    <section class="cover">
        <div class="bingkai">
            <p class="kecil">{{ $d['greeting'] ?? 'Halo cok' }}</p>
            <h1>{{ $d['bride']['name'] ?? '' }} &amp; {{ $d['groom']['name'] ?? '' }}</h1>
            <p>{{ $invitation->event_date->translatedFormat('d F Y') }}</p>
            @if ($guest)
                <p class="kecil">Kepada Yth.</p>
                <div class="nama-tamu">{{ $guest->name }}</div>
            @endif
        </div>
    </section>

    <section>
        <p class="kecil">Mempelai</p>
        <h2>{{ $d['bride']['full_name'] ?? '' }}</h2>
        <p>{{ $d['bride']['parents'] ?? '' }}</p>
        <p class="pemisah">◆ ◆ ◆</p>
        <h2>{{ $d['groom']['full_name'] ?? '' }}</h2>
        <p>{{ $d['groom']['parents'] ?? '' }}</p>
    </section>

    <section>
        <p class="kecil">Tata Acara</p>
        @foreach (($d['events'] ?? []) as $event)
            <div class="acara">
                <h3>{{ $event['name'] ?? '' }}</h3>
                @if (!empty($event['starts_at']))
                    <p>{{ \Illuminate\Support\Carbon::parse($event['starts_at'])->translatedFormat('l, d F Y · H:i') }} WIB</p>
                @endif
                <p><strong>{{ $event['venue'] ?? '' }}</strong><br>{{ $event['address'] ?? '' }}</p>
                @if ($url = $safeUrl($event['maps_url'] ?? null))
                    <a class="tombol" href="{{ $url }}" target="_blank" rel="noopener noreferrer">Buka Peta</a>
                @endif
            </div>
        @endforeach
    </section>
</body>
</html>