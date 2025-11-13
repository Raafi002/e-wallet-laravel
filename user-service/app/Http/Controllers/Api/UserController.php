<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // GET /api/users
    public function index()
    {
        return response()->json(User::all(), 200);
    }

    // POST /api/users
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'pin'   => 'required|string|min:4|max:6',
        ]);

        $user = User::create($data);

        return response()->json($user, 201);
    }

    // GET /api/users/{id}
    public function show($id)
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        return response()->json($user, 200);
    }

    // GET /api/users/{id}/balance
    public function balance($id)
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        return response()->json([
            'user_id' => $user->id,
            'balance' => $user->balance,
        ], 200);
    }

    // PUT /api/internal/users/{id}/balance
    // body: { "type": "credit"|"debit", "amount": 10000 }
    public function updateBalance(Request $request, $id)
    {
        $data = $request->validate([
            'type'   => 'required|in:credit,debit',
            'amount' => 'required|numeric|min:0',
        ]);

        $user = User::find($id);

        if (! $user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        if ($data['type'] === 'credit') {
            $user->balance += $data['amount'];
        } else {
            if ($user->balance < $data['amount']) {
                return response()->json([
                    'message' => 'Insufficient balance',
                ], 400);
            }

            $user->balance -= $data['amount'];
        }

        $user->save();

        return response()->json([
            'user_id' => $user->id,
            'balance' => $user->balance,
        ], 200);
    }
}
