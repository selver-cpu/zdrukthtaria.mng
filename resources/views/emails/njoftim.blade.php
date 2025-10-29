<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Njoftim</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .message {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 12px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .projekt-info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔔 Njoftim i Ri</h1>
        <p>{{ config('app.name') }}</p>
    </div>
    
    <div class="content">
        <div class="message">
            <p style="font-size: 16px; margin: 0;">{{ $mesazhi }}</p>
        </div>
        
        @if($projekt)
            <div class="projekt-info">
                <strong>📋 Projekti:</strong> {{ $projekt->emri_projektit }}<br>
                <strong>👤 Klienti:</strong> {{ $projekt->klient->emri_klientit ?? 'N/A' }}<br>
                <strong>📊 Statusi:</strong> {{ $projekt->statusi->emri_statusit ?? 'N/A' }}
            </div>
        @endif
        
        <p style="margin-top: 20px; color: #666; font-size: 14px;">
            <strong>Data:</strong> {{ $data->format('d.m.Y H:i') }}
        </p>
        
        <center>
            <a href="{{ config('app.url') }}/njoftimet" class="button">
                Shiko Njoftimet
            </a>
        </center>
    </div>
    
    <div class="footer">
        <p>Ky email u dërgua automatikisht nga {{ config('app.name') }}</p>
        <p>Mos u përgjigj këtij email-i.</p>
    </div>
</body>
</html>
