<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected ?string $apiUrl;
    protected ?string $apiKey;

    public function __construct()
    {
        $this->apiUrl = config('services.fastwa.url');
        $this->apiKey = config('services.fastwa.key');
    }

    /**
     * Kirim pesan teks via FastWA
     *
     * @param string $phone Nomor tujuan (format internasional, tanpa '+')
     * @param string $message
     * @return bool
     */
    public function sendMessage($phone, $message): bool
    {
        $phone = $this->formatPhoneNumber($phone);

        if (!$this->apiUrl || !$this->apiKey) {
            Log::error('Konfigurasi FastWA belum lengkap. Isi FASTWA_API_URL dan FASTWA_API_KEY di .env.');
            return false;
        }

        if (!$phone) {
            Log::warning('Nomor WA kosong atau tidak valid, pesan tidak dikirim.');
            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout(30)
                ->post($this->apiUrl, [
                    'api_key' => $this->apiKey,
                    'phone'   => $phone,
                    'message' => $message,
                ]);
        } catch (\Throwable $e) {
            Log::error("Gagal kirim WA ke {$phone}: {$e->getMessage()}");
            return false;
        }

        if ($response->successful()) {
            Log::info("WA terkirim ke {$phone}: {$response->body()}");
            return true;
        }

        Log::error("Gagal kirim WA ke {$phone} - HTTP {$response->status()}: {$response->body()}");
        return false;
    }

    /**
     * Format nomor HP ke format internasional (62xxx)
     */
    public function formatPhoneNumber($phone): ?string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (!$phone) {
            return null;
        }

        if (str_starts_with($phone, '620')) {
            $phone = '62' . substr($phone, 3);
        }

        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        if (substr($phone, 0, 1) === '8') {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}
