<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <meta name="x-apple-disable-message-reformatting">
    <title>Njoftim për Projektin: {{ $projekti->emri_projektit }}</title>
    <style>
        /* Reset styles */
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Arial, Helvetica, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
        }
        
        /* Container styles */
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        /* Header styles */
        .header {
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        
        /* Content styles */
        .content {
            padding: 25px;
        }
        
        /* Info section */
        .info-section {
            background-color: #f5f7fa;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .info-row {
            margin-bottom: 12px;
            display: flex;
            flex-wrap: wrap;
        }
        
        .info-label {
            font-weight: bold;
            min-width: 140px;
            color: #2c3e50;
        }
        
        .info-value {
            flex: 1;
        }
        
        /* Message styles */
        .message {
            background-color: #f8f9fa;
            border-left: 4px solid #2c3e50;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 6px 6px 0;
        }
        
        /* Documents section */
        .documents {
            margin-top: 25px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        
        .document-list {
            list-style-type: none;
            padding-left: 0;
        }
        
        .document-item {
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        /* Button styles */
        .btn {
            display: inline-block;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-weight: 500;
            margin-top: 15px;
        }
        
        /* Footer styles */
        .footer {
            margin-top: 30px;
            padding: 20px;
            text-align: center;
            font-size: 13px;
            color: #777;
            background-color: #f5f7fa;
            border-top: 1px solid #eee;
        }
        
        /* Responsive styles */
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                border-radius: 0;
            }
            
            .content, .header, .footer {
                padding: 15px;
            }
            
            .info-row {
                flex-direction: column;
            }
            
            .info-label {
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Njoftim për Projektin: {{ $projekti->emri_projektit }}</h1>
        </div>
        
        <div class="content">
            <h2>Përshëndetje {{ $user->emri }} {{ $user->mbiemri }},</h2>
            
            <div class="message">
                <p>{{ $mesazhi }}</p>
            </div>
            
            <div class="info-section">
                <h3>Të dhënat e projektit</h3>
                
                <div class="info-row">
                    <span class="info-label">Emri i Projektit:</span>
                    <span class="info-value">{{ $projekti->emri_projektit }}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Klienti:</span>
                    <span class="info-value">{{ $projekti->klient->person_kontakti ?? 'N/A' }}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Statusi:</span>
                    <span class="info-value">{{ $projekti->statusi_projektit->emri_statusit ?? 'N/A' }}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Data e Fillimit:</span>
                    <span class="info-value">{{ $projekti->data_fillimit_parashikuar ? date('d.m.Y', strtotime($projekti->data_fillimit_parashikuar)) : 'N/A' }}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Data e Përfundimit:</span>
                    <span class="info-value">{{ $projekti->data_perfundimit_parashikuar ? date('d.m.Y', strtotime($projekti->data_perfundimit_parashikuar)) : 'N/A' }}</span>
                </div>
                
                @if($projekti->pershkrimi)
                <div class="info-row">
                    <span class="info-label">Përshkrimi:</span>
                    <span class="info-value">{{ $projekti->pershkrimi }}</span>
                </div>
                @endif
            </div>
            
            @if(count($dokumentet) > 0)
            <div class="documents">
                <h3>Dokumentet e Bashkangjitura</h3>
                <p>Këto dokumente janë bashkangjitur në këtë email:</p>
                
                <ul class="document-list">
                    @foreach($dokumentet as $dokument)
                    <li class="document-item">
                        <span class="info-label">{{ $dokument->emri_skedarit }}</span>
                        <span>({{ round($dokument->madhesia_skedarit / 1024, 2) }} KB)</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <a href="{{ route('projektet.show', $projekti->projekt_id) }}" class="btn">Shiko Projektin</a>
        </div>
        
        <div class="footer">
            <p>Ky është një email automatik, ju lutemi mos i përgjigjeni këtij emaili.</p>
            <p>&copy; {{ date('Y') }} Carpentry Design App. Të gjitha të drejtat e rezervuara.</p>
        </div>
    </div>
</body>
</html>
