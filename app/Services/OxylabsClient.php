<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OxylabsClient
{
    protected string $endpoint;

    protected ?string $apiKey;
    protected ?string $username;
    protected ?string $password;
    protected bool $verify;
    protected int|float $timeout;
    protected int|float $connectTimeout;

    public function __construct()
    {
        $this->endpoint = config('services.oxylabs.url') ?? env('OXYLABS_URL');
        $this->apiKey = config('services.oxylabs.key') ?? env('OXYLABS_API_KEY');
        $this->username = config('services.oxylabs.username') ?? env('OXYLABS_USERNAME');
        $this->password = config('services.oxylabs.password') ?? env('OXYLABS_PASSWORD');
        $this->verify = filter_var(config('services.oxylabs.verify', env('OXYLABS_VERIFY', true)), FILTER_VALIDATE_BOOLEAN);
        $this->timeout = (int) (config('services.oxylabs.timeout', env('OXYLABS_TIMEOUT', 60)));
        $this->connectTimeout = (int) (config('services.oxylabs.connect_timeout', env('OXYLABS_CONNECT_TIMEOUT', 10)));
    }

    /**
     * Perform a query to Oxylabs Realtime API.
     * Returns array with response info.
     */
    public function query(array $payload): array
    {
        // Configure HTTP client with optional SSL verification and timeouts.
        $options = [
            'verify' => $this->verify,
            'timeout' => $this->timeout,
            'connect_timeout' => $this->connectTimeout,
        ];

        $client = Http::withOptions($options)->acceptJson();

        if ($this->apiKey) {
            $client = $client->withToken($this->apiKey);
        } elseif ($this->username && $this->password) {
            $client = $client->withBasicAuth($this->username, $this->password);
        }

        $resp = $client->post($this->endpoint, $payload);

        if (! $resp->successful()) {
            return [
                'status' => 'error',
                'code'   => $resp->status(),
                'body'   => $resp->body(),
            ];
        }

        // Try decode JSON; fallback to raw body
        $body = null;
        try {
            $body = $resp->json();
        } catch (\Exception $e) {
            $body = $resp->body();
        }

        return [
            'status' => 'ok',
            'code'   => $resp->status(),
            'body'   => $body,
        ];
    }
}
