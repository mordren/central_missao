<?php

namespace App\Services;

use App\Models\PushToken;

class FcmService
{
    private string $projectId = '';
    private string $serviceAccountPath;

    public function __construct()
    {
        $this->serviceAccountPath = storage_path('app/firebase-service-account.json');

        // 1. Tenta ler do service account JSON
        if (file_exists($this->serviceAccountPath)) {
            $sa = json_decode(file_get_contents($this->serviceAccountPath), true);
            $this->projectId = $sa['project_id'] ?? 'central-4a98b';
        } else {
            // 2. Tenta config/env, com fallback hardcoded
            $fromConfig = config('services.firebase.project_id');
            $fromEnv    = getenv('FIREBASE_PROJECT_ID');
            $this->projectId = ($fromConfig ?: $fromEnv) ?: 'central-4a98b';
        }
    }

    /**
     * Envia notificação para todos os tokens registados.
     */
    public function sendToAll(string $title, string $body, array $data = []): int
    {
        $tokens = PushToken::pluck('token')->toArray();
        if (empty($tokens)) {
            return 0;
        }

        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            \Log::error('FCM: não foi possível obter access token');
            return 0;
        }

        \Log::info('FCM: access token obtido, enviando para ' . count($tokens) . ' token(s)');

        $sent = 0;
        // FCM v1 API não suporta multicast — envia um por um
        foreach ($tokens as $token) {
            $ok = $this->sendToToken($token, $title, $body, $data, $accessToken);
            if ($ok) $sent++;
        }

        return $sent;
    }

    /**
     * Envia notificação para um utilizador específico (todos os tokens dele).
     */
    public function sendToUser(int $userId, string $title, string $body, array $data = []): void
    {
        $tokens = PushToken::where('user_id', $userId)->pluck('token')->toArray();
        if (empty($tokens)) return;

        $accessToken = $this->getAccessToken();
        if (!$accessToken) return;

        foreach ($tokens as $token) {
            $this->sendToToken($token, $title, $body, $data, $accessToken);
        }
    }

    // ──────────────────────────────────────────────────
    // Internos
    // ──────────────────────────────────────────────────

    private function sendToToken(string $token, string $title, string $body, array $data, string $accessToken): bool
    {
        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $payload = json_encode([
            'message' => [
                'token'        => $token,
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                ],
                'webpush' => [
                    'notification' => [
                        'title' => $title,
                        'body'  => $body,
                        'icon'  => config('app.url') . '/images/logo.png',
                        'badge' => config('app.url') . '/images/logo.png',
                        'click_action' => config('app.url') . '/dashboard',
                    ],
                    'fcm_options' => [
                        'link' => config('app.url') . '/dashboard',
                    ],
                ],
                'data' => array_map('strval', $data),
            ],
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT => 10,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            \Log::warning("FCM: erro ao enviar para token ({$httpCode}): {$response}");

            // Token inválido/expirado — remover da base de dados
            if ($httpCode === 404 || $httpCode === 400) {
                PushToken::where('token', $token)->delete();
            }

            return false;
        }

        return true;
    }

    /**
     * Obtém um OAuth2 access token usando o service account JSON (JWT Bearer flow).
     * Não precisa de nenhum package externo — usa apenas openssl nativo do PHP.
     */
    private function getAccessToken(): ?string
    {
        if (!file_exists($this->serviceAccountPath)) {
            \Log::error('FCM: ficheiro service account não encontrado em ' . $this->serviceAccountPath);
            return null;
        }

        $sa = json_decode(file_get_contents($this->serviceAccountPath), true);
        if (!$sa) {
            \Log::error('FCM: service account JSON inválido');
            return null;
        }

        $now    = time();
        $header = base64url_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $claims = base64url_encode(json_encode([
            'iss'   => $sa['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud'   => 'https://oauth2.googleapis.com/token',
            'iat'   => $now,
            'exp'   => $now + 3600,
        ]));

        $signingInput = "{$header}.{$claims}";
        $privateKey   = openssl_pkey_get_private($sa['private_key']);
        if (!$privateKey) {
            \Log::error('FCM: chave privada inválida no service account');
            return null;
        }

        openssl_sign($signingInput, $signature, $privateKey, 'SHA256');
        $jwt = $signingInput . '.' . base64url_encode($signature);

        // Troca o JWT por um access token
        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
        ]);

        $result = json_decode(curl_exec($ch), true);
        $tokenHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($tokenHttpCode !== 200 || empty($result['access_token'])) {
            \Log::error('FCM: falha ao obter access token. HTTP ' . $tokenHttpCode . ' — ' . json_encode($result));
            return null;
        }

        return $result['access_token'] ?? null;
    }
}

// Helper: base64url sem padding (RFC 4648 §5)
if (!function_exists('base64url_encode')) {
    function base64url_encode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}