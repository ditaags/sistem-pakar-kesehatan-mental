<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class LogoutAdminDiHalamanPublik
{
    /**
     * Handle an incoming request.
     *
     * Logika: Begitu admin yang sedang login membuka halaman publik (Beranda, Konsultasi,
     * Hasil, Tentang Kami) melalui navigasi GET biasa, sesi admin otomatis di-logout.
     * Tidak memanggil session()->invalidate() agar data sesi publik (misal konsultasi) tidak hilang.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && $this->isStandardNavigationGet($request)) {
            Auth::logout();

            // Hapus cookie remember jika ada
            if ($recaller = Auth::getRecallerName()) {
                Cookie::queue(Cookie::forget($recaller));
            }

            // Hapus penanda aktivitas admin
            $request->session()->forget('admin_last_activity');

            // Regenerasi token CSRF tanpa membatalkan (invalidate) seluruh session data publik
            $request->session()->regenerateToken();
        }

        return $next($request);
    }

    /**
     * Memeriksa apakah request merupakan navigasi GET browser biasa
     * (bukan AJAX/Fetch, bukan prefetch, bukan permintaan aset statis).
     */
    protected function isStandardNavigationGet(Request $request): bool
    {
        // 1. Harus metode GET
        if (! $request->isMethod('GET')) {
            return false;
        }

        // 2. Bukan AJAX atau JSON request
        if ($request->ajax() || $request->pjax() || $request->expectsJson()) {
            return false;
        }

        // 3. Tidak berisi header X-Requested-With
        if ($request->headers->has('X-Requested-With')) {
            return false;
        }

        // 4. Bukan prefetch (Purpose: prefetch, Sec-Purpose: prefetch, X-Purpose: prefetch)
        $purpose = strtolower((string) $request->header('Purpose', ''));
        $secPurpose = strtolower((string) $request->header('Sec-Purpose', ''));
        $xPurpose = strtolower((string) $request->header('X-Purpose', ''));

        if (str_contains($purpose, 'prefetch') || str_contains($secPurpose, 'prefetch') || str_contains($xPurpose, 'prefetch')) {
            return false;
        }

        // 5. Bukan permintaan aset statis
        $path = strtolower($request->path());
        $assetExtensions = [
            '.css', '.js', '.png', '.jpg', '.jpeg', '.gif', '.svg', '.webp',
            '.ico', '.woff', '.woff2', '.ttf', '.eot', '.map', '.json'
        ];

        foreach ($assetExtensions as $ext) {
            if (str_ends_with($path, $ext)) {
                return false;
            }
        }

        if (str_starts_with($path, 'build/') || str_starts_with($path, 'assets/') || str_starts_with($path, 'storage/')) {
            return false;
        }

        return true;
    }
}
