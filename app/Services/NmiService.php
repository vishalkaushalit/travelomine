<?php

namespace App\Services;

use App\Models\NmiTransaction;
use Illuminate\Support\Facades\Http;

class NmiService
{
    protected string $securityKey;

    protected string $apiUrl;

    protected ?int $merchantId = null;

    public function __construct()
    {
        // Load from config/nmi.php
        $this->securityKey = (string) config('nmi.security_key');
        $this->apiUrl = (string) config('nmi.api_url');
    }

    public function useMerchant(\App\Models\Merchant $merchant): static
    {
        $securityKey = $merchant->security_key ?? config('nmi.security_key');
        $apiUrl = $merchant->api_url ?? config('nmi.api_url');

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
     * Perform a sale transaction via Direct Post.
     * Sends: security_key, type=sale, amount, ccnumber, ccexp, cvv + billing fields.
     */
    public function sale(array $data): array
    {
        $cleanCardNumber = str_replace([' ', '-'], '', $data['ccnumber']);

        // Build NMI Direct Post payload
        $payload = [
            'security_key' => $this->securityKey,
            'type' => 'sale',
            'amount' => number_format($data['amount'], 2, '.', ''),
            'ccnumber' => $cleanCardNumber, // Use the cleaned number here!
            'ccexp' => $data['ccexp'],   // MMYY
            'cvv' => $data['cvv'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'address1' => $data['address1'] ?? null,
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'zip' => $data['zip'] ?? null,
            'country' => $data['country'] ?? null,
            'email' => $data['email'] ?? null,
            'orderid' => $data['order_id'] ?? null,
        ];
        // POST as application/x-www-form-urlencoded to transact.php
        $response = Http::asForm()->post($this->apiUrl, $payload);

        // NMI Direct Post responds as query-string like:
        // response=1&responsetext=SUCCESS&authcode=...&transactionid=...
        $result = [];
        parse_str($response->body(), $result);

        return $result;
    }

    /**
     * Map NMI response into DB and store transaction log.
     * Logs: names, last4, address, email, amount, timestamp, status, raw response.
     */
    public function logTransaction(array $requestData, array $response): NmiTransaction
    {
        // Map gateway response code to status string
        // According to NMI: 1=approved, 2=declined, 3=error.[web:5][web:80]
        $status = 'error';
        if (isset($response['response'])) {
            $status = $response['response'] == 1
                ? 'approved'
                : ($response['response'] == 2 ? 'declined' : 'error');
        }

        // Derive last 4 digits from original request (do NOT store full PAN)
        $cardLast4 = isset($requestData['ccnumber'])
            ? substr($requestData['ccnumber'], -4)
            : null;

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
            'amount' => $requestData['amount'],
            'currency' => 'USD',
            'status' => $status,
            'processed_at' => now(),
            'raw_response' => $response,
        ]);
    }

    /**
     * Log a transaction that came from a payment link
     */
    public function logTransactionFromLink(array $requestData, array $response, int $paymentLinkId): NmiTransaction
    {
        $status = 'error';
        if (isset($response['response'])) {
            $status = $response['response'] == 1
                ? 'approved'
                : ($response['response'] == 2 ? 'declined' : 'error');
        }

        $cardLast4 = isset($requestData['ccnumber'])
            ? substr(str_replace([' ', '-'], '', $requestData['ccnumber']), -4)
            : null;

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
            'amount' => $requestData['amount'],
            'currency' => 'USD',
            'status' => $status,
            'processed_at' => now(),
            'raw_response' => $response,
        ]);
    }
}
