<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $activity->title }} - ONÇAS DO OESTE</title>
    
    {{-- Open Graph Meta Tags for WhatsApp preview --}}
    <meta property="og:title" content="{{ $activity->title }} - ONÇAS DO OESTE">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit($activity->description ?? 'Confira esta Missão!', 200) }}">
    @if ($activity->banner)
        <meta property="og:image" content="{{ url($activity->banner) }}">
        <meta property="og:image:type" content="image/jpeg">
        <meta name="twitter:card" content="summary_large_image">
    @else
        <meta name="twitter:card" content="summary">
    @endif
    <meta property="og:url" content="{{ route('activities.share', $activity) }}">
    <meta property="og:type" content="website">
    <meta name="twitter:title" content="{{ $activity->title }}">
    <meta name="twitter:description" content="{{ \Illuminate\Support\Str::limit($activity->description ?? 'Confira esta Missão!', 200) }}">
    @if ($activity->banner)
        <meta name="twitter:image" content="{{ url($activity->banner) }}">
    @endif
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #0A0A0A 0%, #1A1A1A 100%);
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            background: #141414;
            border: 1px solid #2A2A2A;
            border-radius: 16px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }
        h1 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 16px;
            word-break: break-word;
        }
        .badge {
            display: inline-block;
            background: rgba(255, 214, 0, 0.1);
            color: #FFD600;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
        }
        @if ($activity->banner)
        .banner {
            width: 100%;
            max-height: 300px;
            object-fit: cover;
            border-radius: 12px;
            margin: 20px 0;
            border: 1px solid #2A2A2A;
        }
        @endif
        p {
            color: #888;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .pre-wrap {
            white-space: pre-wrap;
            word-break: break-word;
            color: #888;
        }
        .info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin: 24px 0;
            text-align: left;
        }
        .info-item {
            background: #1A1A1A;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #2A2A2A;
        }
        .info-label {
            font-size: 12px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .info-value {
            font-weight: 600;
            font-size: 14px;
            color: #fff;
        }
        .points {
            color: #FFD600;
        }
        .cta {
            margin-top: 28px;
        }
        a {
            display: inline-block;
            background: #FFD600;
            color: #0A0A0A;
            padding: 12px 32px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: background 0.2s;
        }
        a:hover {
            background: #FFC107;
        }
        .note {
            font-size: 12px;
            color: #666;
            margin-top: 16px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="badge">{{ $activity->typeLabel() }}</div>
        <h1>{{ $activity->title }}</h1>
        
        @if ($activity->banner)
            <img src="{{ url($activity->banner) }}" alt="Banner" class="banner">
        @endif
        
        @if ($activity->description)
            <p class="pre-wrap">{{ $activity->description }}</p>
        @endif
        
        <div class="info">
            @if ($activity->date_time)
                <div class="info-item">
                    <div class="info-label">Data</div>
                    <div class="info-value">{{ $activity->date_time->format('d/m/Y') }}</div>
                </div>
            @endif
            
            <div class="info-item">
                <div class="info-label">Pontos</div>
                <div class="info-value points">+{{ $activity->points }}</div>
            </div>
            
            @if ($activity->location)
                <div class="info-item">
                    <div class="info-label">Local</div>
                    <div class="info-value">{{ $activity->location }}</div>
                </div>
            @endif
            
            {{-- Prazo escondido na interface; usado internamente para eventos presenciais (date_time +24h) --}}
        </div>
        
        <div class="cta">
            <a href="{{ route('login') }}">Entrar para participar</a>
        </div>
        
        <p class="note">Faça login para ver todos os detalhes e participar desta Missão</p>
    </div>
    
    <script>
        // Redirect to activity page (WhatsApp/bots will only see meta tags, users get redirected)
        window.location.href = "{{ route('activities.show', $activity) }}";
    </script>
</body>
</html>
