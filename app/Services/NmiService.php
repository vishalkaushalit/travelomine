<?php

namespace App\Services;

use App\Models\Merchant;
use App\Models\NmiTransaction;
use Illuminate\Support\Facades\Http;

class NmiService
{
    protected string $securityKey;
    protected string $apiUrl;
    protected ?int $merchantId = null;

    public function __construct()
    {
        $this->securityKey = (string) config('nmi.security_key');
        $this->apiUrl = (string) config('nmi.api_url');
    }

    public function useMerchant(Merchant $merchant): static
    {
        $securityKey = $merchant->security_key ?: config('nmi.security_key');
        $apiUrl = $merchant->api_url ?: config('nmi.api_url');

        if (empty($securityKey)) {
            throw new \Exception('No security key found for selected merchant and no fallback NMI key is configured.');
        }

        if (empty($apiUrl)) {
            throw new \Exception('No API URL found for selected merchant and no fallback NMI URL is configured.');
        }

        $this->securityKey = (string) $securityKey;
        $this->apiUrl = (string) $apiUrl;
        $this->merchantId = $merchant->id;

        return $this;
    }

    /**
     * Old raw-card sale flow.
     * Keep only if you still need sandbox/backward compatibility.
     */
    public function sale(array $data): array
    {
        $cleanCardNumber = str_replace([' ', '-'], '', $data['ccnumber']);

        $payload = [
            'security_key' => $this->securityKey,
            'type' => 'sale',
            'amount' => number_format((float) $data['amount'], 2, '.', ''),
            'ccnumber' => $cleanCardNumber,
            'ccexp' => $data['ccexp'],
            'cvv' => $data['cvv'],
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'address1' => $data['address1'] ?? null,
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'zip' => $data['zip'] ?? null,
            'country' => $data['country'] ?? null,
            'email' => $data['email'] ?? null,
            'orderid' => $data['order_id'] ?? null,
        ];

        return $this->sendRequest($payload);
    }

    /**
     * New token-based sale flow for Collect.js/live usage.
     */
    public function saleWithToken(array $data): array
    {
        $payload = [
            'security_key' => $this->securityKey,
            'type' => 'sale',
            'amount' => number_format((float) $data['amount'], 2, '.', ''),
            'payment_token' => $data['payment_token'],
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'address1' => $data['address1'] ?? null,
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'zip' => $data['zip'] ?? null,
            'country' => $data['country'] ?? null,
            'email' => $data['email'] ?? null,
            'orderid' => $data['order_id'] ?? null,
        ];

        return $this->sendRequest($payload);
    }

    protected function sendRequest(array $payload): array
    {
        $response = Http::asForm()->post($this->apiUrl, $payload);

        $result = [];
        parse_str($response->body(), $result);

        return $result;
    }

    public function logTransaction(array $requestData, array $response): NmiTransaction
    {
        $status = $this->mapStatus($response);

        $cardLast4 = isset($requestData['ccnumber'])
            ? substr(str_replace([' ', '-'], '', $requestData['ccnumber']), -4)
            : ($response['last4'] ?? null);

        return NmiTransaction::create([
            'order_id' => $requestData['order_id'] ?? null,
            'transaction_id' => $response['transactionid'] ?? null,
            'type' => $response['type'] ?? 'sale',
            'customer_first_name' => $requestData['first_name'] ?? null,
            'customer_last_name' => $requestData['last_name'] ?? null,
            'email' => $requestData['email'] ?? null,
            'card_last4' => $cardLast4,
            'card_brand' => $response['cardbrand'] ?? null,
            'address1' => $requestData['address1'] ?? null,
            'city' => $requestData['city'] ?? null,
            'state' => $requestData['state'] ?? null,
            'zip' => $requestData['zip'] ?? null,
            'country' => $requestData['country'] ?? null,
            'amount' => $requestData['amount'] ?? 0,
            'currency' => 'USD',
            'status' => $status,
            'processed_at' => now(),
            'raw_response' => $response,
        ]);
    }

    public function logTransactionFromLink(array $requestData, array $response, int $paymentLinkId): NmiTransaction
    {
        $status = $this->mapStatus($response);

        $cardLast4 = isset($requestData['ccnumber'])
            ? substr(str_replace([' ', '-'], '', $requestData['ccnumber']), -4)
            : ($response['last4'] ?? null);

        return NmiTransaction::create([
            'payment_link_id' => $paymentLinkId,
            'order_id' => $requestData['order_id'] ?? null,
            'transaction_id' => $response['transactionid'] ?? null,
            'type' => $response['type'] ?? 'sale',
            'customer_first_name' => $requestData['first_name'] ?? null,
            'customer_last_name' => $requestData['last_name'] ?? null,
            'email' => $requestData['email'] ?? null,
            'card_last4' => $cardLast4,
            'card_brand' => $response['cardbrand'] ?? null,
            'address1' => $requestData['address1'] ?? null,
            'city' => $requestData['city'] ?? null,
            'state' => $requestData['state'] ?? null,
            'zip' => $requestData['zip'] ?? null,
            'country' => $requestData['country'] ?? null,
            'amount' => $requestData['amount'] ?? 0,
            'currency' => 'USD',
            'status' => $status,
            'processed_at' => now(),
            'raw_response' => $response,
        ]);
    }

    protected function mapStatus(array $response): string
    {
        if (! isset($response['response'])) {
            return 'error';
        }

        return match ((string) $response['response']) {
            '1' => 'approved',
            '2' => 'declined',
            default => 'error',
        };
    }
}