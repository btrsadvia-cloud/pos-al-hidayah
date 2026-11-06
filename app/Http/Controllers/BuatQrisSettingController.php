<?php

namespace App\Http\Controllers;

use App\Models\QrisTransaction;
use App\Services\BuatQrisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class BuatQrisSettingController extends Controller
{
    /**
     * Show BuatQris settings and transaction history.
     */
    public function index(BuatQrisService $service)
    {
        $transactions = QrisTransaction::with(['sale', 'creator'])
            ->latest()
            ->paginate(15);

        $webhookUrl = url('/api/buatqris/webhook');

        $currentConfig = [
            'base_url'       => config('buatqris.base_url', 'https://api.buatqris.site'),
            'account_id'     => config('buatqris.account_id', ''),
            'secret_token'   => config('buatqris.secret_token', ''),
            'signing_secret' => config('buatqris.signing_secret', ''),
            'fee_by'         => config('buatqris.fee_by', 'user'),
            'method'         => config('buatqris.method', ''),
            'test_mode'      => config('buatqris.test_mode', false),
            'is_configured'  => $service->isConfigured(),
        ];

        return view('settings.buatqris', compact('transactions', 'webhookUrl', 'currentConfig'));
    }

    /**
     * Update BuatQris settings into .env file.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'account_id'     => ['required', 'string', 'max:100'],
            'secret_token'   => ['required', 'string', 'max:150'],
            'signing_secret' => ['nullable', 'string', 'max:150'],
            'fee_by'         => ['required', 'in:user,buyer'],
            'method'         => ['nullable', 'string', 'in:qris_one,qris_two,qris_three,qris_four'],
            'test_mode'      => ['nullable'],
        ]);

        $envPath = base_path('.env');
        if (File::exists($envPath)) {
            $envContent = File::get($envPath);

            $replacements = [
                'BUATQRIS_ACCOUNT_ID'     => trim($validated['account_id']),
                'BUATQRIS_SECRET_TOKEN'   => trim($validated['secret_token']),
                'BUATQRIS_SIGNING_SECRET' => trim($validated['signing_secret'] ?? ''),
                'BUATQRIS_FEE_BY'         => $validated['fee_by'],
                'BUATQRIS_METHOD'         => $validated['method'] ?? '',
                'BUATQRIS_TEST_MODE'      => !empty($validated['test_mode']) ? 'true' : 'false',
            ];

            foreach ($replacements as $key => $value) {
                if (preg_match("/^{$key}=/m", $envContent)) {
                    $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
                } else {
                    $envContent .= "\n{$key}={$value}";
                }
            }

            File::put($envPath, $envContent);

            // Clear config cache so new env takes effect immediately
            \Artisan::call('config:clear');
        }

        return redirect()->route('settings.buatqris')
            ->with('success', 'Konfigurasi BuatQris berhasil disimpan dan diperbarui!');
    }

    /**
     * Recheck specific QRIS transaction status via API.
     */
    public function checkStatus(string $transactionId, BuatQrisService $service)
    {
        $res = $service->checkStatus($transactionId);

        if (!empty($res['rate_limited'])) {
            return back()->with('warning', $res['message']);
        }

        if ($res['success']) {
            return back()->with('success', "Status transaksi {$transactionId}: " . strtoupper($res['status']));
        }

        return back()->with('error', $res['message'] ?? 'Gagal memeriksa status transaksi.');
    }
}
