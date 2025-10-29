<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SsoAcceptController extends Controller
{
    /**
     * Endpoint penerima SSO dari Admin Center.
     * Query: ?token=JWT (HS256)
     * Payload minimal:
     *  - iss: 'admin-center' (atau SSO_JWT_ISS)
     *  - aud: 'product-admin' (atau SSO_JWT_AUD)
     *  - sub: email user (WAJIB)
     *  - iat/nbf/exp: standar JWT
     */
    public function login(Request $r)
    {
        $fail = function (string $msg) {
            return redirect()->route('admin.login')->withErrors(['sso' => $msg]);
        };

        // 1) Ambil token
        $jwt = $r->query('token');
        if (!$jwt) return $fail('Token kosong');

        // 2) Verifikasi HS256
        [$ok, $payload, $err] = $this->verifyHs256(
            $jwt,
            config('app.sso_jwt_secret', env('SSO_JWT_SECRET'))
        );
        if (!$ok || !is_array($payload)) return $fail($err ?? 'Token tidak valid');

        // 3) Validasi klaim
        $expectedIss = env('SSO_JWT_ISS', 'admin-center');
        $expectedAud = env('SSO_JWT_AUD', 'product-admin');
        $now  = time();
        $skew = 60; // toleransi clock skew

        if (($payload['iss'] ?? null) !== $expectedIss)                return $fail('Issuer salah');
        if (($payload['aud'] ?? null) !== $expectedAud)                return $fail('Audience salah');
        if (isset($payload['nbf']) && $payload['nbf'] > $now + $skew)  return $fail('Token belum berlaku');
        if (!isset($payload['exp']) || $payload['exp'] < ($now - $skew)) return $fail('Token kedaluwarsa');

        // 4) Identitas (email) dari sub
        $email = strtolower(trim($payload['sub'] ?? ''));
        if ($email === '') return $fail('Sub (email) kosong');

        // 5) Cari user by email (case-insensitive)
        $userModel = config('auth.providers.users.model'); // ex: App\Models\User
        /** @var \Illuminate\Database\Eloquent\Model $user */
        $user = $userModel::whereRaw('LOWER(email) = ?', [$email])->first();
        if (!$user) return $fail('User belum terdaftar di sistem ini');

        // (Opsional) Batasi hanya admin (sesuai middleware user-access:admin & LoginController)
        if (property_exists($user, 'type') && (int)($user->type) !== 1) {
            return $fail('Akun ini tidak memiliki akses admin.');
        }

        // 6) Login & redirect langsung ke halaman Banner admin
        Auth::login($user);
        $r->session()->regenerate();

        // Gunakan URL langsung untuk menghindari masalah routing
        return redirect('/admin/banner');
    }

    /**
     * Verifikasi HS256 JWT sederhana (tanpa lib eksternal).
     * @return array{0: bool, 1: array<mixed>|null, 2: string|null}
     */
    private function verifyHs256(string $jwt, ?string $secret): array
    {
        if (!$secret) return [false, null, 'Secret kosong'];

        $parts = explode('.', $jwt);
        if (count($parts) !== 3) return [false, null, 'Format token salah'];

        [$h, $p, $s] = $parts;

        $json = function (string $seg) {
            $dec = $this->base64UrlDecode($seg);
            return json_decode($dec, true);
        };

        $header  = $json($h);
        $payload = $json($p);
        if (!is_array($header) || ($header['alg'] ?? '') !== 'HS256') {
            return [false, null, 'Alg harus HS256'];
        }

        $sig  = $this->base64UrlDecode($s);
        $calc = hash_hmac('sha256', $h . '.' . $p, $secret, true);

        if (!hash_equals($calc, $sig)) {
            return [false, null, 'Signature mismatch'];
        }

        return [true, $payload, null];
    }

    private function base64UrlDecode(string $input): string
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $input .= str_repeat('=', 4 - $remainder);
        }
        $input = strtr($input, '-_', '+/');
        return base64_decode($input) ?: '';
    }
}