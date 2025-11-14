<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TransactionController extends Controller
{
    protected string $userServiceBaseUrl;

    public function __construct()
    {
        $this->userServiceBaseUrl = env('USER_SERVICE_BASE_URL', 'http://localhost:8001');
    }

    // GET /api/transactions
    public function index()
    {
        return response()->json(Transaction::all(), 200);
    }

    // POST /api/transactions/topup
    public function topup(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|integer',
            'amount'  => 'required|numeric|min:1',
        ]);

        // Panggil User Service untuk tambah saldo (credit)
        $response = Http::put(
            $this->userServiceBaseUrl . '/api/internal/users/' . $data['user_id'] . '/balance',
            [
                'type'   => 'credit',
                'amount' => $data['amount'],
            ]
        );

        if ($response->failed()) {
            return response()->json([
                'message' => 'Failed to update balance on user-service',
                'detail'  => $response->json(),
            ], 400);
        }

        // Simpan transaksi
        $transaction = Transaction::create([
            'user_id'     => $data['user_id'],
            'type'        => 'topup',
            'amount'      => $data['amount'],
            'description' => 'Topup balance',
        ]);

        return response()->json([
            'transaction' => $transaction,
            'user'        => $response->json(),
        ], 201);
    }

    // POST /api/transactions/pay
    public function pay(Request $request)
    {
        $data = $request->validate([
            'user_id'  => 'required|integer',
            'amount'   => 'required|numeric|min:1',
            'merchant' => 'required|string',
            'note'     => 'nullable|string',
        ]);

        // Cek saldo dulu
        $balanceResponse = Http::get(
            $this->userServiceBaseUrl . '/api/users/' . $data['user_id'] . '/balance'
        );

        if ($balanceResponse->failed()) {
            return response()->json([
                'message' => 'User not found on user-service',
            ], 404);
        }

        $balanceData = $balanceResponse->json();

        if ($balanceData['balance'] < $data['amount']) {
            return response()->json([
                'message' => 'Insufficient balance',
            ], 400);
        }

        // Kurangi saldo (debit)
        $updateResponse = Http::put(
            $this->userServiceBaseUrl . '/api/internal/users/' . $data['user_id'] . '/balance',
            [
                'type'   => 'debit',
                'amount' => $data['amount'],
            ]
        );

        if ($updateResponse->failed()) {
            return response()->json([
                'message' => 'Failed to deduct balance on user-service',
                'detail'  => $updateResponse->json(),
            ], 400);
        }

        // Simpan transaksi
        $transaction = Transaction::create([
            'user_id'     => $data['user_id'],
            'type'        => 'payment',
            'amount'      => $data['amount'],
            'description' => 'Payment to ' . $data['merchant']
                . (!empty($data['note']) ? ' - ' . $data['note'] : ''),
        ]);

        return response()->json([
            'transaction' => $transaction,
            'user'        => $updateResponse->json(),
        ], 201);
    }
}
