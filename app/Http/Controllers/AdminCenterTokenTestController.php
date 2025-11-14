<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User; // model user lokal sistem tsb

class AdminCenterTokenTestController extends Controller
{
    public function handle(Request $r)
    {
        $token = $r->input('token');
        if (!$token) {
            return response()->json(['ok' => false, 'reason' => 'missing_token'], 400);
        }

        try {
            [$header, $payload] = $this->decodeAndVerify($token, env('SSO_JWT_SECRET'));
        } catch (\Throwable $e) {
            return response()->json([
                'ok'     => false,
                'reason' => 'invalid_token',
                'message'=> $e->getMessage(),
            ], 400);
        }

        // cek user berdasarkan sub/email di payload
        $sub   = $payload['sub'] ?? null;
        $email = $payload['email'] ?? $sub;

        $user = $email ? User::where('email', $email)->first()
                       : User::where('username', $sub)->first();

        return response()->json([
            'ok'         => true,
            'user_found' => (bool) $user,
            'message'    => $user ? 'Token valid & user dikenali.'
                                  : 'Token valid tetapi user belum ada di sistem ini.',
        ]);
    }

    private function decodeAndVerify(string $jwt, string $secret): array
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            throw new \RuntimeException('Token format invalid');
        }

        [$h64, $p64, $s64] = $parts;

        $jsonHeader  = base64_decode(strtr($h64, '-_', '+/'));
        $jsonPayload = base64_decode(strtr($p64, '-_', '+/'));
        $sigProvided = base64_decode(strtr($s64, '-_', '+/'));

        $header  = json_decode($jsonHeader, true);
        $payload = json_decode($jsonPayload, true);

        $alg = $header['alg'] ?? null;
        if ($alg !== 'HS256') {
            throw new \RuntimeException('Algoritma tidak didukung');
        }

        $signing = $h64 . '.' . $p64;
        $sigCalc = hash_hmac('sha256', $signing, $secret, true);

        if (!hash_equals($sigCalc, $sigProvided)) {
            throw new \RuntimeException('Signature tidak valid');
        }

        $now = time();
        if (($payload['nbf'] ?? $now) > $now) {
            throw new \RuntimeException('Token belum berlaku');
        }
        if (($payload['exp'] ?? $now) < $now) {
            throw new \RuntimeException('Token kedaluwarsa');
        }

        return [$header, $payload];
    }
}