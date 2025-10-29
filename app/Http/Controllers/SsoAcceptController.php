<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

class SsoAcceptController extends Controller
{
    /**
     * Endpoint penerima SSO dari Admin Center.
     * Menerima ?token=JWT HS256, payload berisi:
     *  - iss: 'admin-center'
     *  - aud: 'product-admin'
     *  - sub: email user (W A J I B)
     *  - iat/nbf/exp: standar JWT
     */
    public function login(Request $r)
    {
        $jwt = $r->query('token');
        if (!$jwt) {
            return redirect()->route('admin.login')->withErrors(['sso' => 'Token kosong']);
        }

        [$ok, $payload, $err] = $this->verifyHs256(
            $jwt,
            config('app.sso_jwt_secret', env('SSO_JWT_SECRET')) // ambil dari .env
        );

        if (!$ok || !is_array($payload)) {
            return redirect()->route('admin.login')->withErrors(['sso' => $err ?? 'Token tidak valid']);
        }

        // ---- Validasi klaim standar ----
        $expectedIss = env('SSO_JWT_ISS', 'admin-center');     // issuer dari Admin Center
        $expectedAud = env('SSO_JWT_AUD', 'product-admin');    // audience untuk app ini
        $now         = time();
        $skew        = 60; // toleransi 60 detik

        if (($payload['iss'] ?? null) !== $expectedIss) {
            return redirect()->route('admin.login')->withErrors(['sso' => 'Issuer salah']);
        }
        if (($payload['aud'] ?? null) !== $expectedAud) {
            return redirect()->route('admin.login')->withErrors(['sso' => 'Audience salah']);
        }
        if (isset($payload['nbf']) && $payload['nbf'] > $now + $skew) {
            return redirect()->route('admin.login')->withErrors(['sso' => 'Token belum berlaku']);
        }
        if (!isset($payload['exp']) || $payload['exp'] < ($now - $skew)) {
            return redirect()->route('admin.login')->withErrors(['sso' => 'Token kedaluwarsa']);
        }

        // ---- Ambil identitas (email) dari sub ----
        $email = strtolower(trim($payload['sub'] ?? ''));
        if ($email === '') {
            return redirect()->route('admin.login')->withErrors(['sso' => 'Sub (email) kosong']);
        }

        // ---- Cari user berdasarkan email ----
        $userModel = config('auth.providers.users.model'); // biasanya App\Models\User
        /** @var \Illuminate\Database\Eloquent\Model $user */
        $user = $userModel::whereRaw('LOWER(email) = ?', [$email])->first();

        if (!$user) {
            return redirect()->route('admin.login')->withErrors(['sso' => 'User belum terdaftar di sistem ini']);
        }

        // ---- Login-kan user ----
        Auth::login($user);
        $r->session()->regenerate();

        // Redirect ke intended atau ke dashboard admin
        // ganti route berikut bila dashboard-mu beda:
        return redirect()->intended(route('admin.home'));
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