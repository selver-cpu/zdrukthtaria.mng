<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $apiUrl;
    protected $apiKey;
    protected $sender;

    public function __construct()
    {
        // Konfiguro për SMS gateway të Kosovës/Shqipërisë
        // Mund të përdorësh: Twilio, Nexmo, ose një provider lokal
        $this->apiUrl = env('SMS_API_URL', 'https://api.sms-provider.com/send');
        $this->apiKey = env('SMS_API_KEY', '');
        $this->sender = env('SMS_SENDER', 'ColiDecor');
    }

    /**
     * Dërgo SMS
     * 
     * @param string $phoneNumber Numri i telefonit (format: +383xxxxxxxx ose +355xxxxxxxx)
     * @param string $message Mesazhi
     * @return bool
     */
    public function send(string $phoneNumber, string $message): bool
    {
        try {
            // Pastro numrin e telefonit
            $phoneNumber = $this->formatPhoneNumber($phoneNumber);
            
            // Nëse nuk ka API key, log dhe kthe false
            if (empty($this->apiKey)) {
                Log::warning('SMS API key not configured. SMS not sent to: ' . $phoneNumber);
                return false;
            }

            // Dërgo SMS përmes API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl, [
                'to' => $phoneNumber,
                'from' => $this->sender,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info('SMS sent successfully to: ' . $phoneNumber);
                return true;
            } else {
                Log::error('SMS sending failed: ' . $response->body());
                return false;
            }

        } catch (\Exception $e) {
            Log::error('SMS sending error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Formato numrin e telefonit
     * 
     * @param string $phoneNumber
     * @return string
     */
    protected function formatPhoneNumber(string $phoneNumber): string
    {
        // Hiq hapësirat dhe karakteret speciale
        $phoneNumber = preg_replace('/[^0-9+]/', '', $phoneNumber);
        
        // Nëse fillon me 0, zëvendëso me +383 (Kosovë)
        if (substr($phoneNumber, 0, 1) === '0') {
            $phoneNumber = '+383' . substr($phoneNumber, 1);
        }
        
        // Nëse nuk ka +, shto +383
        if (substr($phoneNumber, 0, 1) !== '+') {
            $phoneNumber = '+383' . $phoneNumber;
        }
        
        return $phoneNumber;
    }

    /**
     * Valido numrin e telefonit
     * 
     * @param string $phoneNumber
     * @return bool
     */
    public function isValidPhoneNumber(string $phoneNumber): bool
    {
        $phoneNumber = $this->formatPhoneNumber($phoneNumber);
        
        // Kontrollo nëse është numër i Kosovës (+383) ose Shqipërisë (+355)
        return preg_match('/^\+383[0-9]{8}$/', $phoneNumber) || 
               preg_match('/^\+355[0-9]{9}$/', $phoneNumber);
    }
}
