<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MerchantController extends Controller
{
    public function index()
    {
        $merchants = Merchant::latest()->paginate(15);

        return view('admin.merchants.index', compact('merchants'));
    }

    public function create()
    {
        $merchant = new Merchant();

        return view('admin.merchants.create', compact('merchant'));
    }

    public function store(Request $request)
    {
        $data = $this->validateMerchant($request);

        Merchant::create($data);

        return redirect()
            ->route('admin.merchants.index')
            ->with('success', 'Merchant created successfully.');
    }

    public function edit(Merchant $merchant)
    {
        return view('admin.merchants.edit', compact('merchant'));
    }

    public function update(Request $request, Merchant $merchant)
    {
        $data = $this->validateMerchant($request, $merchant->id);

        $merchant->update($data);

        return redirect()
            ->route('admin.merchants.index')
            ->with('success', 'Merchant updated successfully.');
    }

    public function destroy(Merchant $merchant)
    {
        $merchant->delete();

        return redirect()
            ->route('admin.merchants.index')
            ->with('success', 'Merchant deleted successfully.');
    }

    protected function validateMerchant(Request $request, ?int $merchantId = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'merchant_code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('merchants', 'merchant_code')->ignore($merchantId),
            ],
            'security_key' => ['nullable', 'string', 'max:255'],
            'tokenization_key' => ['nullable', 'string', 'max:255'],
            'api_url' => ['nullable', 'url', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'support_mail' => ['nullable', 'email', 'max:255'],
            'wallet_balance' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],

            'smtp_host' => ['nullable', 'string', 'max:255'],
            'smtp_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'smtp_username' => ['nullable', 'string', 'max:255'],
            'smtp_password' => ['nullable', 'string', 'max:1000'],
            'smtp_encryption' => ['nullable', Rule::in(['tls', 'ssl', 'starttls'])],
            'from_email' => ['nullable', 'email', 'max:255'],
            'from_name' => ['nullable', 'string', 'max:255'],
            'reply_to_email' => ['nullable', 'email', 'max:255'],
            'reply_to_name' => ['nullable', 'string', 'max:255'],
            'is_smtp_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['is_smtp_active'] = $request->boolean('is_smtp_active');
        $data['wallet_balance'] = $data['wallet_balance'] ?? 0;

        return $data;
    }
}