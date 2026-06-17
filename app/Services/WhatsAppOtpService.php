<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class WhatsAppOtpService
{
   
    public function sendOtp(string $mobileNo, string $otp): array
    {
        $apiKey = config('services.whatsapp.api_key');
        $phoneNumberId = config('services.whatsapp.phone_number_id');
        $templateName = config('services.whatsapp.template_name', 'otp');
        $templateLanguageCode = config('services.whatsapp.template_language_code', 'en');
        $defaultCountryCode = config('services.whatsapp.default_country_code', '91');

        if (empty($apiKey) || empty($phoneNumberId)) {
            throw new \RuntimeException('WhatsApp API is not configured. Please set services.whatsapp.api_key and services.whatsapp.phone_number_id.');
        }

        // Strip everything except digits (spaces, +, dashes, etc).
        $mobileNo = preg_replace('/\D/', '', $mobileNo);

        if (strlen($mobileNo) === 10) {
            $mobileNo = $defaultCountryCode . $mobileNo;
        }

        $apiUrl = "https://graph.facebook.com/v19.0/{$phoneNumberId}/messages";

        $data = [
            'messaging_product' => 'whatsapp',
            'to' => $mobileNo,
            'recipient_type' => 'individual',
            'type' => 'template',
            'template' => [
                'language' => [
                    'policy' => 'deterministic',
                    'code' => $templateLanguageCode,
                ],
                'name' => $templateName,
                'components' => [
                    [
                        'type' => 'body',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => $otp,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $headers = [
            "Authorization: Bearer {$apiKey}",
            'Content-Type: application/json',
        ];

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

        $response = curl_exec($ch);
        $curlErrNo = curl_errno($ch);
        $curlErr = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($curlErrNo) {
            Log::error('WhatsApp OTP send: curl-level failure', [
                'mobile' => $mobileNo,
                'curl_errno' => $curlErrNo,
                'curl_error' => $curlErr,
            ]);

            throw new \RuntimeException("WhatsApp curl error ({$curlErrNo}): {$curlErr}");
        }

        $decoded = json_decode((string) $response, true);

        if ($httpCode < 200 || $httpCode >= 300 || isset($decoded['error'])) {
            $errorMessage = $decoded['error']['message'] ?? "Unexpected HTTP {$httpCode} response";
            $errorCode = $decoded['error']['code'] ?? null;

            Log::error('WhatsApp OTP send: API rejected the request', [
                'mobile' => $mobileNo,
                'http_code' => $httpCode,
                'error_code' => $errorCode,
                'raw_response' => $response,
            ]);

            throw new \RuntimeException("WhatsApp send failed: {$errorMessage}");
        }

        return $decoded ?? [];
    }
}